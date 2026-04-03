<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LeaveApplicationsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $leaves;

    public function __construct($leaves)
    {
        $this->leaves = $leaves;
    }

    public function collection()
    {
        return $this->leaves;
    }

    public function headings(): array
    {
        return [
            'SR. NO',
            'EMPLOYEE NAME',
            'STATUS',
            'LEAVE TYPE',
            'CATEGORY',
            'START DATE',
            'END DATE',
            'START TIME',
            'END TIME',
            'TOTAL DAYS',
            'REASON',
            'MESSAGE'
        ];
    }

    public function map($leave): array
    {
        static $counter = 0;
        $counter++;

        $isGatepass = strtolower($leave->leave_category) === 'gatepass';
        $category = strtoupper($leave->leave_category);
        if ($category === 'HALF') {
            $category = 'HALF DAY';
        } elseif ($category === 'FULL') {
            $category = 'FULL DAY';
        }

        return [
            $counter,
            $leave->employee->name ?? 'N/A',
            strtoupper($leave->status),
            ucwords($leave->leave_type),
            $category,
            $leave->start_date ? $leave->start_date->format('d-M-Y') : '-',
            $leave->end_date ? $leave->end_date->format('d-M-Y') : '-',
            ($isGatepass && $leave->start_time) ? Carbon::parse($leave->start_time)->format('h:i A') : '',
            ($isGatepass && $leave->end_time) ? Carbon::parse($leave->end_time)->format('h:i A') : '',
            $leave->total_days,
            $leave->reason,
            $leave->message
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]], // Bold headings
        ];
    }
}
