<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Ye line zarur add karein
use Carbon\Carbon;

class AttendanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            // 1. Excel Serial Date ko PHP Date mein convert karna
            $formattedDate = is_numeric($row['date']) 
                ? Carbon::instance(Date::excelToDateTimeObject($row['date']))->format('Y-m-d')
                : Carbon::parse($row['date'])->format('Y-m-d');

            // 2. Check-in aur Check-out ko handle karna
            // Agar Excel mein time cell format 'Time' hai, to excelToDateTimeObject use karein
            $checkIn = is_numeric($row['check_in'])
                ? Carbon::instance(Date::excelToDateTimeObject($row['check_in']))
                : Carbon::parse($row['check_in']);

            $checkOut = is_numeric($row['check_out'])
                ? Carbon::instance(Date::excelToDateTimeObject($row['check_out']))
                : Carbon::parse($row['check_out']);

            // 3. Calculation logic
            if ($checkOut->lt($checkIn)) {
                $checkOut->addDay();
            }

            $totalMinutes = $checkIn->diffInMinutes($checkOut);
            $totalHours = round($totalMinutes / 60, 2);

            return new Attendance([
                'employee_id'     => $row['employee_id'],
                'attendance_date' => $formattedDate,
                'check_in'        => $checkIn->format('H:i:s'),
                'check_out'       => $checkOut->format('H:i:s'),
                'total_hours'     => $totalHours,
                'status'          => $row['status'] ?? 'present',
            ]);
        } catch (\Exception $e) {
            // Agar koi error aaye to skip ya log karein
            return null;
        }
    }
}