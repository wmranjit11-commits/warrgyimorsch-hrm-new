<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Ye line zarur add karein
use Carbon\Carbon;

class AttendanceImport implements ToModel, WithHeadingRow
{public function model(array $row)
{
    try {

        // Column mapping (header nahi hai)
        $employeeId = $row[0] ?? null;
        $dateTimeRaw = $row[1] ?? null;

        if (!$employeeId || !$dateTimeRaw) {
            return null; // skip invalid row
        }

        // DateTime parse
        $dateTime = is_numeric($dateTimeRaw) 
            ? Carbon::instance(Date::excelToDateTimeObject($dateTimeRaw))
            : Carbon::parse($dateTimeRaw);

        $formattedDate = $dateTime->format('Y-m-d');
        $time = $dateTime->format('H:i:s');

        // Check existing
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('attendance_date', $formattedDate)
            ->first();

        if (!$attendance) {

            // First entry = check_in
            return new Attendance([
                'employee_id'     => $employeeId,
                'attendance_date' => $formattedDate,
                'check_in'        => $time,
                'check_out'       => null,
                'total_hours'     => 0,
                'status'          => 'present',
            ]);

        } else {

            // Second entry = check_out
            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = Carbon::parse($time);

            if ($checkOut->lt($checkIn)) {
                $checkOut->addDay();
            }

            $totalMinutes = $checkIn->diffInMinutes($checkOut);
            $totalHours = round($totalMinutes / 60, 2);

            $attendance->update([
                'check_out' => $checkOut->format('H:i:s'),
                'total_hours' => $totalHours,
            ]);
        }

        return null;

    } catch (\Exception $e) {
        \Log::error('Import Error: ' . $e->getMessage());
        return null;
    }
}

}