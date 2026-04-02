<?php
namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class MonthlyAttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1);
        $daysInMonth = $date->daysInMonth;
        $employees = Employee::orderBy('name')->get();
        
        $data = collect();

        $counter = 1;
        foreach ($employees as $emp) {
            $row = [$counter++, $emp->id, $emp->name];
            
            $attendance = Attendance::where('employee_id', $emp->id)
                ->whereYear('attendance_date', $this->year)
                ->whereMonth('attendance_date', $this->month)
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->attendance_date)->format('M j');
                });

            // Fetch Holidays for context
            $holidays = \App\Models\Holiday::whereYear('date', $this->year)
                ->whereMonth('date', $this->month)
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->date)->format('M j');
                });

            $p = 0; $a = 0; $h = 0; $l = 0; $po = 0; $ot = 0;
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $checkDate = Carbon::createFromDate($this->year, $this->month, $d);
                $dayStr = $checkDate->format('M j');
                $record = $attendance->get($dayStr);
                $holiday = $holidays->get($dayStr);
                
                if ($record) {
                    $statusLower = strtolower($record->status);
                    $row[] = strtoupper($record->status);
                    
                    if ($statusLower === 'present' || $statusLower === 'late') $p++;
                    elseif ($statusLower === 'absent') $a++;
                    elseif ($statusLower === 'half_day') $h++;
                    elseif ($statusLower === 'leave') $l++;

                    if ($record->total_hours > 8) {
                        $ot += ($record->total_hours - 8);
                    }
                } elseif ($holiday) {
                    $row[] = 'HOLIDAY';
                    $po++; // Paid Off
                } elseif ($checkDate->isSunday()) {
                    $row[] = 'SUNDAY';
                    $po++; // Paid Off
                } else {
                    $row[] = '';
                }
            }

            $paidDays = $p + ($h * 0.5) + $l + $po;
            $row = array_merge($row, [$p, $po, $a, $h, $l, $paidDays, round($ot, 2)]);
            $data->push($row);
        }

        return $data;
    }

    public function headings(): array
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1);
        $daysInMonth = $date->daysInMonth;

        $headings = ['SR. NO', 'EMP ID', 'EMPLOYEE NAME'];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $checkDate = Carbon::createFromDate($this->year, $this->month, $i);
            $headings[] = $checkDate->format('M d');
        }
        return array_merge($headings, ['ACTUAL PRESENT', 'PAID OFFS', 'TOTAL ABSENT', 'TOTAL HALF DAY', 'TOTAL LEAVE', 'TOTAL PAID DAYS', 'OVERTIME (HRS)']);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]], // Bold headings
        ];
    }
}
