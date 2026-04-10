<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $year;
    protected $month;
    protected $daysInMonth;
    protected $attendanceMap;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        // Fetch records and build map
        $attendances = Attendance::whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();

        $this->attendanceMap = [];
        foreach ($attendances as $att) {
            $this->attendanceMap[$att->employee_id][Carbon::parse($att->attendance_date)->format('j')] = strtoupper(substr($att->status, 0, 1));
        }
    }

    public function collection()
    {
        return Employee::orderBy('name', 'asc')->get();
    }

    public function headings(): array
    {
        $monthName = Carbon::create($this->year, $this->month, 1)->format('M');
        $headers = ['SR. NO', 'EMPLOYEE NAME', 'DESIGNATION'];

        for ($i = 1; $i <= $this->daysInMonth; $i++) {
            $headers[] = $monthName . ' ' . $i;
        }

        $headers[] = 'PRESENT';
        $headers[] = 'ABSENT';
        $headers[] = 'LEAVE/HD';

        return $headers;
    }

    public function map($emp): array
    {
        static $counter = 0;
        $counter++;

        $row = [
            $counter,
            $emp->name,
            $emp->designation ?? 'N/A',
        ];

        $present = 0;
        $absent = 0;
        $others = 0;

        for ($i = 1; $i <= $this->daysInMonth; $i++) {
            $status = $this->attendanceMap[$emp->id][$i] ?? '-';
            $row[] = $status;

            if ($status === 'P') $present++;
            elseif ($status === 'A') $absent++;
            elseif ($status !== '-') $others++;
        }

        $row[] = $present;
        $row[] = $absent;
        $row[] = $others;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        // Styling the header
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3858F9']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Center all attendance statuses
        $sheet->getStyle('D2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add borders to everything
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        return [];
    }
}
