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
    protected $startDate;
    protected $endDate;
    protected $dates = [];
    protected $attendanceMap;
    protected $employeeId;


    public function __construct($startDate, $endDate ,$employeeId)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        $this->employeeId = $employeeId;
        $period = Carbon::parse($this->startDate);

        while ($period->lte($this->endDate)) {
            $this->dates[] = $period->copy();
            $period->addDay();
        }

        $attQuery = Attendance::whereBetween('attendance_date', [
            $this->startDate->toDateString(),
            $this->endDate->toDateString()
        ]);

        if (!empty($this->employeeId)) {
            $attQuery->where('employee_id', $this->employeeId);
        }

        $attendances = $attQuery->get();

        $this->attendanceMap = [];

        foreach ($attendances as $att) {
            $dateKey = Carbon::parse($att->attendance_date)->format('Y-m-d');

            $this->attendanceMap[$att->employee_id][$dateKey] = strtoupper(substr($att->status, 0, 1));
        }
    }

  public function collection()
    {
        $query = Employee::orderBy('name', 'asc');

        if (!empty($this->employeeId)) {
            $query->where('id', $this->employeeId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        $headers = ['SR. NO', 'EMPLOYEE NAME', 'DESIGNATION'];

        foreach ($this->dates as $date) {
            $headers[] = $date->format('d M');
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

        foreach ($this->dates as $date) {
            $dateKey = $date->format('Y-m-d');

            $status = $this->attendanceMap[$emp->id][$dateKey] ?? '-';

            $row[] = $status;

            if ($status === 'P') {
                $present++;
            } elseif ($status === 'A') {
                $absent++;
            } elseif ($status !== '-') {
                $others++;
            }
        }

        $row[] = $present;
        $row[] = $absent;
        $row[] = $others;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3858F9'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('D2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

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