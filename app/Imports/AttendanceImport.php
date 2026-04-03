<?php

namespace App\Imports;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceImport implements ToModel, WithHeadingRow
{
//     public function model(array $row)
// {
//     try {
//         $employeeId  = $row['employee_id'] ?? null;
//         $dateTimeRaw = $row['attendence_date'] ?? null;

//         if (empty($employeeId) || empty($dateTimeRaw)) {
//             return null;
//         }

//         if (is_numeric($dateTimeRaw)) {
//             $punchDateTime = Carbon::instance(Date::excelToDateTimeObject($dateTimeRaw));
//         } elseif ($dateTimeRaw instanceof \DateTimeInterface) {
//             $punchDateTime = Carbon::instance($dateTimeRaw);
//         } else {
//             $punchDateTime = Carbon::parse(trim((string) $dateTimeRaw));
//         }

//         $attendanceDate = $punchDateTime->format('Y-m-d');
//         $punchTime      = $punchDateTime->format('H:i:s');

//         // Sirf open attendance uthao
//         $openAttendance = Attendance::where('employee_id', $employeeId)
//             ->whereNull('check_out')
//             ->orderByDesc('attendance_date')
//             ->orderByDesc('check_in')
//             ->first();

//         // Agar open attendance mila to usko close karne ki koshish karo
//         if ($openAttendance && $openAttendance->check_in) {
//             $checkInDateTime = Carbon::parse(
//                 $openAttendance->attendance_date . ' ' . $openAttendance->check_in
//             );

//             $maxAllowed = $checkInDateTime->copy()->addHours(12);

//             if (
//                 $punchDateTime->greaterThan($checkInDateTime) &&
//                 $punchDateTime->lessThanOrEqualTo($maxAllowed)
//             ) {
//                 $totalMinutes = $checkInDateTime->diffInMinutes($punchDateTime);
//                 $totalHours   = round($totalMinutes / 60, 2);

//                 $openAttendance->update([
//                     'check_out'   => $punchTime,
//                     'total_hours' => $totalHours,
//                 ]);

//                 return null;
//             }
//         }

//         // Duplicate IN avoid karo (optional but useful)
//         $duplicateIn = Attendance::where('employee_id', $employeeId)
//             ->whereDate('attendance_date', $attendanceDate)
//             ->where('check_in', $punchTime)
//             ->exists();

//         if ($duplicateIn) {
//             return null;
//         }

//         // Naya check-in
//         return new Attendance([
//             'employee_id'     => $employeeId,
//             'attendance_date' => $attendanceDate,
//             'check_in'        => $punchTime,
//             'check_out'       => null,
//             'total_hours'     => 0,
//             'status'          => 'present',
//         ]);

//     } catch (\Throwable $e) {
//         Log::error('Attendance row import error: ' . $e->getMessage(), [
//             'row' => $row,
//         ]);

//         return null;
//     }
// }

public function model(array $row)
{
    try {
        $employeeId  = $row['employee_id'] ?? null;
        $dateTimeRaw = $row['attendence_date'] ?? null;

        if (empty($employeeId) || empty($dateTimeRaw)) {
            return null;
        }

        // ✅ CHECK: user exist karta hai ya nahi
        $userExists = \App\Models\User::where('id', $employeeId)->exists();

        if (!$userExists) {
            // optional log
            \Log::warning('Invalid Employee ID skipped', [
                'employee_id' => $employeeId
            ]);
            return null;
        }

        // ✅ Date parsing
        if (is_numeric($dateTimeRaw)) {
            $punchDateTime = \Carbon\Carbon::instance(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateTimeRaw)
            );
        } elseif ($dateTimeRaw instanceof \DateTimeInterface) {
            $punchDateTime = \Carbon\Carbon::instance($dateTimeRaw);
        } else {
            $punchDateTime = \Carbon\Carbon::parse(trim((string) $dateTimeRaw));
        }

        $attendanceDate = $punchDateTime->format('Y-m-d');
        $punchTime      = $punchDateTime->format('H:i:s');

        // ✅ SAME DAY open attendance
        $openAttendance = \App\Models\Attendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->whereNull('check_out')
            ->orderBy('check_in')
            ->first();

        // ✅ Checkout logic
        if ($openAttendance) {

            $checkInDateTime = \Carbon\Carbon::parse(
                $attendanceDate . ' ' . $openAttendance->check_in
            );

            if ($punchDateTime->greaterThan($checkInDateTime)) {

                $totalMinutes = $checkInDateTime->diffInMinutes($punchDateTime);

                $openAttendance->update([
                    'check_out'   => $punchTime,
                    'total_hours' => round($totalMinutes / 60, 2),
                ]);

                return null;
            }
        }

        // ✅ Duplicate check
        $duplicate = \App\Models\Attendance::where('employee_id', $employeeId)
            ->whereDate('attendance_date', $attendanceDate)
            ->where('check_in', $punchTime)
            ->exists();

        if ($duplicate) {
            return null;
        }

        // ✅ Insert new IN
        return new \App\Models\Attendance([
            'employee_id'     => $employeeId,
            'attendance_date' => $attendanceDate,
            'check_in'        => $punchTime,
            'check_out'       => null,
            'total_hours'     => 0,
            'status'          => 'present',
        ]);

    } catch (\Throwable $e) {

        \Log::error('Attendance Import Error', [
            'error' => $e->getMessage(),
            'row'   => $row,
        ]);

        return null;
    }
}
}