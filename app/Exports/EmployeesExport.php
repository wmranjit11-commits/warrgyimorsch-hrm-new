<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithColumnWidths
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function headings(): array
    {
        return [
            'SR. NO',
            'EMPLOYEE CODE',
            'NAME',
            'EMAIL',
            'MOBILE NUMBER',
            'GENDER',
            'DATE OF BIRTH',
            'DATE OF JOINING',
            'EMPLOYEE TYPE',
            'ROLE',
            'DEPARTMENT',
            'DESIGNATION',
            'USERNAME',
            'AADHAAR NUMBER',
            'PAN NUMBER',
            'ADDRESS',
            'TIME IN',
            'TIME OUT',
            'PF',
            'PF NUMBER',
            'ESI',
            'ESI NUMBER',
            'INSURANCE',
            'INSURANCE PROVIDER',
            'INSURANCE POLICY NO.',
            'BANK NAME',
            'ACCOUNT NUMBER',
            'IFSC CODE',
            'BASIC SALARY',
            'HRA',
            'CONVEYANCE ALLOWANCE',
            'MEDICAL ALLOWANCE',
            'OTHER ALLOWANCE',
            'TOTAL SALARY (CTC)',
        ];
    }

    public function map($emp): array
    {
        static $counter = 0;
        $counter++;

        $totalSalary = ($emp->basic_salary ?? 0) + ($emp->hra ?? 0) + ($emp->conveyance_allowance ?? 0) + ($emp->medical_allowance ?? 0) + ($emp->other_allowance ?? 0);

        return [
            $counter,
            'EC' . str_pad($emp->id, 4, '0', STR_PAD_LEFT),
            $emp->name,
            $emp->email ?? '-',
            $emp->mobile_number ?? '-',
            $emp->gender ? ucfirst($emp->gender) : '-',
            $emp->date_of_birth ?? '-',
            $emp->date_of_joining ?? '-',
            $emp->employee_type ? ucfirst(str_replace('_', ' ', $emp->employee_type)) : '-',
            $emp->role ? ucfirst(str_replace('_', ' ', $emp->role)) : '-',
            $emp->department ? ucfirst(str_replace('_', ' ', $emp->department)) : '-',
            $emp->designation ?? '-',
            $emp->username ?? '-',
            $emp->aadhaar_number ?? '-',
            $emp->pan_number ?? '-',
            $emp->address ?? '-',
            $emp->time_in ?? '-',
            $emp->time_out ?? '-',
            $emp->pf ? 'Yes' : 'No',
            $emp->pf_number ?? '-',
            $emp->esi ? 'Yes' : 'No',
            $emp->esi_number ?? '-',
            $emp->insurance ? 'Yes' : 'No',
            $emp->insurance_provider ?? '-',
            $emp->insurance_policy_number ?? '-',
            $emp->bank_name ?? '-',
            $emp->account_number ?? '-',
            $emp->ifsc_code ?? '-',
            $emp->basic_salary ?? 0,
            $emp->hra ?? 0,
            $emp->conveyance_allowance ?? 0,
            $emp->medical_allowance ?? 0,
            $emp->other_allowance ?? 0,
            $totalSalary,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,    // SR. NO
            'B' => 15,   // EMPLOYEE CODE
            'C' => 25,   // NAME
            'D' => 28,   // EMAIL
            'E' => 16,   // MOBILE
            'F' => 10,   // GENDER
            'G' => 14,   // DOB
            'H' => 14,   // DOJ
            'I' => 16,   // TYPE
            'J' => 18,   // ROLE
            'K' => 22,   // DEPARTMENT
            'L' => 22,   // DESIGNATION
            'M' => 16,   // USERNAME
            'N' => 18,   // AADHAAR
            'O' => 14,   // PAN
            'P' => 35,   // ADDRESS
            'Q' => 10,   // TIME IN
            'R' => 10,   // TIME OUT
            'S' => 6,    // PF
            'T' => 16,   // PF NUMBER
            'U' => 6,    // ESI
            'V' => 16,   // ESI NUMBER
            'W' => 10,   // INSURANCE
            'X' => 22,   // INSURANCE PROVIDER
            'Y' => 22,   // INSURANCE POLICY
            'Z' => 22,   // BANK NAME
            'AA' => 20,  // ACCOUNT NUMBER
            'AB' => 14,  // IFSC
            'AC' => 14,  // BASIC
            'AD' => 10,  // HRA
            'AE' => 22,  // CONVEYANCE
            'AF' => 20,  // MEDICAL
            'AG' => 18,  // OTHER
            'AH' => 16,  // TOTAL
        ];
    }
}
