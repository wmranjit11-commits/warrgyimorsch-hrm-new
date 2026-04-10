<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class AttendanceImport implements ToCollection
{
  public function collection(Collection $rows)
{
    $data = [];

    foreach ($rows as $index => $row) {

        if ($index == 0) continue;

        $employeeCode = trim($row[0] ?? '');
        $dateTimeRaw  = $row[1] ?? null;

        if (!$employeeCode || !$dateTimeRaw) continue;

       $employee = Employee::where('employee_code', $employeeCode)->first();

       if (!$employee) {
            \Log::warning('Employee not found', [
                'employee_code' => $employeeCode
            ]);
            continue;
        }

        try {
            if (is_numeric($dateTimeRaw)) {
                $dateTime = Carbon::instance(
                    Date::excelToDateTimeObject($dateTimeRaw)
                );
            } else {
                $dateTime = Carbon::parse($dateTimeRaw);
            }
        } catch (\Exception $e) {
            \Log::error('Date parse failed', ['value' => $dateTimeRaw]);
            continue;
        }

        // ✅ IMPORTANT: Store punches
        $key = $employee->id . '_' . $dateTime->format('Y-m-d');
        $data[$key]['employee_id'] = $employee->id;
        $data[$key]['punches'][] = $dateTime;
    }

    // ✅ Process grouped punches
   foreach ($data as $key => $entry) {

        $employeeId = $entry['employee_id'];
        $punches = $entry['punches'];

        $count = count($punches);
        if ($count == 1) {
            // ❗ Only one punch
            $first = $punches[0];
            $last  = null;
            $hours = 0;
            $status = 'absent'; // ya 'incomplete'
        } else {
            // ✅ Normal case
            $first = reset($punches);
            $last  = end($punches);

            $hours = $first->diffInMinutes($last) / 60;

            if ($hours >= 8) {
                $status = 'present';
            } elseif ($hours >= 4) {
                $status = 'half_day';
            } else {
                $status = 'absent';
            }
        }

        Attendance::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'attendance_date' => $first->format('Y-m-d'),
            ],
            [
                'check_in'    => $first->format('H:i:s'),
                'check_out'   =>$last ? $last->format('H:i:s') : null,
                'total_hours' => round($hours, 2),
                'status'      => $status,
            ]
        );
    }
}
}