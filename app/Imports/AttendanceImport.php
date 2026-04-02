<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    public $importedMonth = null;

    /**
     * Process the entire collection of rows at once.
     * This allows us to group multiple punches (IN/OUT rows) for the same employee correctly.
     */
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) return;

        // 1. Group rows by employee_id and date
        $grouped = $rows->groupBy(function ($row) {
            // Priority list for Employee ID columns
            $possibleIdColumns = ['employee_id', 'emp_id', 'id', 'emp_code', 'code', 'member_id', 'userid'];
            $employeeId = null;

            foreach ($possibleIdColumns as $col) {
                if (isset($row[$col]) && !empty($row[$col])) {
                    $val = $row[$col];
                    // If we find a purely numeric ID, that's our best bet
                    if (is_numeric($val) || (is_string($val) && preg_match('/^[0-9]+$/', preg_replace('/[^0-9]/', '', $val)))) {
                        $employeeId = $val;
                        break;
                    }
                }
            }
            
            // Fallback to first available if no numeric found (though numeric is preferred)
            if (!$employeeId) {
                $employeeId = $row['employee_id'] ?? $row['emp_id'] ?? $row['id'] ?? $row['emp_code'] ?? $row['code'] ?? null;
            }
            
            // Fuzzy detection for Date
            $dateValue = $row['date'] ?? $row['attendance_date'] ?? $row['day'] ?? $row['dated'] ?? $row['datetime'] ?? null;

            if (!$employeeId || !$dateValue) return 'invalid';

            try {
                $date = $this->parseDate($dateValue);
                // Set the first valid month for redirection
                if (!$this->importedMonth) {
                    $this->importedMonth = Carbon::parse($date)->format('Y-m');
                }
                return $employeeId . '_' . $date;
            } catch (\Exception $e) {
                return 'invalid';
            }
        });

        foreach ($grouped as $key => $groupRows) {
            if ($key === 'invalid') continue;

            [$employeeId, $attendanceDate] = explode('_', $key);

            // Strip any non-numeric prefixes (like 'EC0001' -> '1') if it's not a numeric ID
            if (!is_numeric($employeeId)) {
                $employeeId = preg_replace('/[^0-9]/', '', $employeeId);
            }

            // 1.5 CRITICAL CHECK: Does the employee actually exist?
            // If we don't check this, MySQL throws a Foreign Key Integrity Constraint error (500).
            $employeeExists = \App\Models\Employee::where('id', $employeeId)->exists();
            if (!$employeeExists) {
                // If the employee doesn't exist, we skip this grouping entirely to avoid crashing.
                continue;
            }

            // 2. Identify all punch times in this group
            $punches = [];
            foreach ($groupRows as $row) {
                // Look for punch time in various possible columns (fuzzy)
                $timeValue = $row['time'] ?? $row['punch_time'] ?? $row['check_in'] ?? $row['check_out'] ?? $row['punch'] ?? $row['clock'] ?? null;
                
                if ($timeValue) {
                    try {
                        $punches[] = $this->parseTime($timeValue);
                    } catch (\Exception $e) {
                        // Skip unparseable times
                    }
                }
            }

            if (empty($punches)) continue;

            // 3. Status determination: Earliest is IN, Latest is OUT
            sort($punches);
            $checkIn = $punches[0];
            $checkOut = (count($punches) > 1) ? end($punches) : null;

            // 4. Calculate total hours
            $totalHours = 0;
            if ($checkIn && $checkOut) {
                try {
                    $in = Carbon::parse($checkIn);
                    $out = Carbon::parse($checkOut);
                    
                    if ($out->lt($in)) {
                        $out->addDay();
                    }
                    
                    $totalMinutes = $in->diffInMinutes($out);
                    $totalHours = round($totalMinutes / 60, 2);
                } catch (\Exception $e) {}
            }

            // 5. Update or Create Database Record
            Attendance::updateOrCreate(
                [
                    'employee_id'     => (int)$employeeId,
                    'attendance_date' => $attendanceDate,
                ],
                [
                    'check_in'    => $checkIn,
                    'check_out'   => $checkOut,
                    'total_hours' => $totalHours,
                    'status'      => $this->determineStatus($totalHours, $groupRows->first()['status'] ?? 'present'),
                ]
            );
        }
    }

    /**
     * Logic to determine status based on hours or priority
     */
    private function determineStatus($hours, $originalStatus)
    {
        $status = strtolower($originalStatus);
        if ($hours > 0 && $hours < 4) return 'half_day';
        return $status ?: 'present';
    }

    private function parseDate($value)
    {
        if (is_numeric($value)) {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
        }
        return Carbon::parse($value)->format('Y-m-d');
    }

    private function parseTime($value)
    {
        if (is_numeric($value)) {
            // Usually returns a full datetime string from Excel time serial
            return Carbon::instance(Date::excelToDateTimeObject($value))->format('H:i:s');
        }
        return Carbon::parse($value)->format('H:i:s');
    }
}