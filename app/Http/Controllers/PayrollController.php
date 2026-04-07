<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    /**
     * Display Attendance List
     */
    public function attendance(Request $request)
    {
        $query = Attendance::query();

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
        }

        // Filter by Employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Default filter (current month)
        if (!$request->filled('start_date') && !$request->filled('end_date') && !$request->filled('employee_id')) {
            $query->whereYear('attendance_date', now()->year)
                ->whereMonth('attendance_date', now()->month);
        }

        // Aggregation
        $attendance = $query->selectRaw("
                attendance_date,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_day_count,
                SUM(CASE WHEN status IN ('absent','leave') THEN 1 ELSE 0 END) as leave_count,
                SUM(CASE WHEN total_hours >= 9 THEN 1 ELSE 0 END) as overtime_count
            ")
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc')
            ->paginate(15);
            // echo "<pre>";print_r($attendance->toArray());exit;
        return view('payroll.attendance', compact('attendance'));
    }

    /**
     * Get attendance details for a specific date (AJAX)
     */
    public function getAttendanceDetails(Request $request)
    {
        $date = $request->date;
        $details = Attendance::with('employee')
            ->where('attendance_date', $date)
            ->get();

        return response()->json([
            'success' => true,
            'date' => Carbon::parse($date)->format('d M Y'),
            'data' => $details
        ]);
    }

    /**
     * Get attendance records with filtering
     */
    public function getAttendance(Request $request)
    {
        $query = Attendance::with('employee');

        // Filter by Date Range (Start Date to End Date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('year') && $request->filled('month')) {
            $query->whereYear('attendance_date', $request->year)
                ->whereMonth('attendance_date', $request->month);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->paginate(10);

        return view('payroll.attendance', compact('attendance'));
    }

    /**
     * Add Attendance - Show form
     */
    public function addAttendance()
    {
        $employees = Employee::all();
        return view('payroll.add-attendance', compact('employees'));
    }

    /**
     * Store Attendance
     */
    public function storeAttendance(Request $request)
    {
        // dd($request->all());
        try {
            if (!$request->attendance_date) {
                throw new \Exception('Attendance date is required.');
            }

            foreach ($request->employees as $employeeData) {
                    $checkIn = !empty($employeeData['check_in']) ? $employeeData['check_in'] : null;
                    $checkOut = !empty($employeeData['check_out']) ? $employeeData['check_out'] : null;

                    if ($checkIn || $checkOut) {
                    $status = $employeeData['status'] ?? 'present';


                  $totalHours = 0;

                    if ($checkIn && $checkOut) {
                        try {
                            $in = Carbon::createFromFormat('H:i', $checkIn);
                            $out = Carbon::createFromFormat('H:i', $checkOut);

                            // 🔥 Get difference (can be negative)
                            $diffMinutes = $in->diffInMinutes($out, false);

                            // ✅ Handle night shift (e.g. 10 PM → 6 AM)
                            if ($diffMinutes < 0) {
                                $diffMinutes += 24 * 60;
                            }

                           $totalHours = round($diffMinutes / 60, 2);

                        } catch (\Exception $e) {
                            $totalHours = 0;
                        }
                    }

                    // Auto-Leave Logic: If no check-in, set status to 'leave'
                    if (empty($checkIn)) {
                        $status = 'leave';
                        $checkOut = null; // Ensure no check-out if no check-in
                    }

                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $employeeData['employee_id'],
                            'attendance_date' => $request->attendance_date
                        ],
                        [
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'status' => $status,
                            'total_hours' => round($totalHours, 2),
                        ]
                    );
                }
            }

            return redirect()->route('payroll.attendance', ['start_date' => $request->attendance_date, 'end_date' => $request->attendance_date])
                ->with('success', 'Attendance records have been updated successfully! ✓');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display Payroll Calculation page
     */
    public function calculation()
    {
        return view('payroll.calculation');
    }

    /**
     * Calculate and display payroll
     */
   public function calculatePayroll(Request $request)
    {
        try {
            $month = $request->month; // YYYY-MM
            $employeeId = $request->employee_id;

            $employee = Employee::findOrFail($employeeId);

            // 📅 Month Setup
            $date = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $year = $date->year;
            $monthNum = $date->month;
            $totalDays = $date->daysInMonth;

            // 📊 Get Attendance
            $records = Attendance::where('employee_id', $employeeId)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $monthNum)
                ->get();

            // ✅ STEP 1: Total Working Hours
            $totalHours = 0;

            foreach ($records as $r) {
                if ($r->status == 'absent') continue;

                // Paid Leave = full day (8 hrs)
                if ($r->status == 'leave') {
                    $totalHours += 8;
                } else {
                    $totalHours += $r->total_hours ?? 0;
                }
            }

            // Convert to days
            $payableDays = round($totalHours / 8, 2);
            $payableDays = min($payableDays, $totalDays); // safety

            $unpaidDays = $totalDays - $payableDays;

            // 💰 STEP 2: Monthly Salary (Annual → Monthly)
            $monthlySalary = (
                ($employee->basic_salary ?? 0) +
                ($employee->hra ?? 0) +
                ($employee->conveyance_allowance ?? 0) +
                ($employee->medical_allowance ?? 0) +
                ($employee->other_allowance ?? 0)
            ) / 12;

            // Per day salary
            $perDaySalary = $monthlySalary / $totalDays;

            // Gross Salary
            $grossSalary = $perDaySalary * $payableDays;

            // 📉 STEP 3: Deductions
            $pf = 0;
            $esi = 0;

            // PF (12% of basic portion only)
            if ($employee->pf) {
                $basicMonthly = ($employee->basic_salary ?? 0) / 12;
                $pf = ($basicMonthly / $totalDays * $payableDays) * 0.12;
            }

            // ESI (only if salary <= 21000)
            if ($employee->esi && $grossSalary <= 21000) {
                $esi = $grossSalary * 0.0075;
            }

            $totalDeductions = $pf + $esi;

            // 💵 STEP 4: Net Salary
            $netSalary = max(0, $grossSalary - $totalDeductions);

            // 📉 Salary Loss
            $fullMonthSalary = $monthlySalary;
            $salaryLoss = $fullMonthSalary - $grossSalary;

            // 📦 Final Data
            $payrollData = [
                'employee_id' => $employeeId,
                'month' => $month,
                'payable_days' => $payableDays,
                'unpaid_days' => round($unpaidDays, 2),
                'salary_loss' => round($salaryLoss, 2),

                'basic_salary' => round(($employee->basic_salary / 12 / $totalDays) * $payableDays, 2),
                'hra' => round(($employee->hra / 12 / $totalDays) * $payableDays, 2),
                'conveyance_allowance' => round(($employee->conveyance_allowance / 12 / $totalDays) * $payableDays, 2),
                'medical_allowance' => round(($employee->medical_allowance / 12 / $totalDays) * $payableDays, 2),
                'other_allowance' => round(($employee->other_allowance / 12 / $totalDays) * $payableDays, 2),

                'gross_salary' => round($grossSalary, 2),

                'pf_deduction' => round($pf, 2),
                'esi_deduction' => round($esi, 2),
                'other_deduction' => 0,
                'deductions' => round($totalDeductions, 2),

                'net_salary' => round($netSalary, 2),

                'status' => 'pending',
            ];

            return response()->json([
                'success' => true,
                'payroll' => $payrollData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store calculated payroll
     */
    public function storePayroll(Request $request)
    {
        try {
            $data = $request->all();
            $payroll = Payroll::updateOrCreate(
                ['employee_id' => $data['employee_id'], 'month' => $data['month']],
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Payroll saved successfully!',
                'payroll_id' => $payroll->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

public function import(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $file = $request->file('import_file');

        if (!$file || $file->getSize() == 0) {
            return back()->with('error', 'Uploaded file is empty.');
        }

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\AttendanceImport, $file);

        return back()->with('success', 'Attendance imported successfully!');

    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('Attendance Import Controller Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return back()->with('error', $e->getMessage());
    }
}

    /**
     * Display payroll list
     */
    public function index(Request $request)
    {
        $query = Payroll::with('employee');

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Get payroll records with filtering
     */
    public function getPayroll(Request $request)
    {
        $query = Payroll::with('employee');

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('month', 'desc')->paginate(10);

        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show payroll record
     */
    public function show($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        return view('payroll.show', compact('payroll'));
    }

    /**
     * Export payroll as PDF or CSV
     */
    public function export(Request $request)
    {
        $query = Payroll::with('employee');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        $payrolls = $query->get();

        if ($payrolls->count() == 1) {
            $payroll = $payrolls->first();
            $filename = 'payslip_' . $payroll->employee->name . '_' . $payroll->month . '.csv';
        } else {
            $filename = 'payroll_report_' . date('Y-m-d') . '.csv';
        }

        // NEW: Default to PDF format for a professional workflow
        $format = $request->query('format', 'pdf');

        if ($format === 'pdf') {
            if ($payrolls->count() == 1) {
                return $this->downloadPdf($payrolls->first()->id);
            } else {
                return $this->bulkDownloadPdf($request);
            }
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['Company Name', 'PAYROLL SLIP/REPORT']);
            fputcsv($file, ['Generated On', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['']);

            // CSV Headers
            fputcsv($file, [
                'SR.NO',
                'EMPLOYEE NAME',
                'MONTH',
                'PAYABLE DAYS',
                'BASIC SALARY',
                'HRA',
                'CONVEYANCE',
                'MEDICAL',
                'OTHER ALLOWANCE',
                'PF DEDUCTION',
                'ESI DEDUCTION',
                'OTHER DEDUCTION',
                'GROSS SALARY',
                'TOTAL DEDUCTIONS',
                'NET SALARY',
                'STATUS'
            ]);

            // CSV Data
            $counter = 1;
            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $counter++,
                    $payroll->employee->name,
                    $payroll->month,
                    $payroll->payable_days,
                    $payroll->basic_salary,
                    $payroll->hra,
                    $payroll->conveyance_allowance,
                    $payroll->medical_allowance,
                    $payroll->other_allowance,
                    $payroll->pf_deduction,
                    $payroll->esi_deduction,
                    $payroll->other_deduction,
                    $payroll->gross_salary,
                    $payroll->deductions,
                    $payroll->net_salary,
                    $payroll->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export attendance records (CSV)
     */
    public function exportAttendance(Request $request)
    {
        $query = Attendance::with('employee');

        // Filter by Date Range (Start Date to End Date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('year') && $request->filled('month')) {
            $query->whereYear('attendance_date', $request->year)
                ->whereMonth('attendance_date', $request->month);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        if ($request->filled('employee_id') && $attendances->count() > 0) {
            $empName = $attendances->first()->employee->name;
            $filename = 'attendance_' . str_replace(' ', '_', $empName) . '_' . date('Y-m-d') . '.csv';
        } else {
            $filename = 'attendance_report_' . date('Y-m-d_His') . '.csv';
        }
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            // Invoice-like header
            fputcsv($file, ['Company Name', 'Payroll Attendance Report']);
            fputcsv($file, ['Generated On', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['']);
            fputcsv($file, ['Date', 'Employee Name', 'Designation', 'Check In', 'Check Out', 'Status', 'Hours']);

            foreach ($attendances as $att) {
                fputcsv($file, [
                    $att->attendance_date->format('Y-m-d'),
                    $att->employee->name,
                    $att->employee->designation ?? 'N/A',
                    $att->check_in,
                    $att->check_out,
                    $att->status,
                    $att->total_hours,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Update payroll status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            $payroll->update([
                'status' => $request->status,
                'payment_date' => $request->status === 'paid' ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payroll status updated!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function edit($id){
        $attendance = Attendance::with('employee')->findOrFail($id);
        // Employee object mein purani attendance daal dete hain taaki blade page par array loop chal sake
        $employee = $attendance->employee;
        $employee->old_check_in = $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : null;
        $employee->old_check_out = $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : null;
        $employee->old_status = $attendance->status;
        $employee->old_duration = $attendance->total_hours ? intval($attendance->total_hours) . 'h ' . round(($attendance->total_hours - intval($attendance->total_hours)) * 60) . 'm' : '--';

        return view('payroll.add-attendance', [
            'employees' => [$employee], // Array mein sirf ek employee jayega
            'edit_date' => \Carbon\Carbon::parse($attendance->attendance_date)->format('Y-m-d'),
            'is_edit' => true // Is flag se hum page ko batayenge ki ye edit mode hai
        ]);
    }

    public function destroy($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            $payroll->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payroll deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified attendance record from storage.
     */
    public function destroyAttendance($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove all attendance records for a specific date.
     */
    public function destroyAttendanceByDate($date)
    {
        try {
            Attendance::where('attendance_date', $date)->delete();

            return response()->json([
                'success' => true,
                'message' => 'All attendance records for ' . $date . ' deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Bulk remove selected attendance records by date.
     */
    public function bulkDestroyAttendance(Request $request)
    {
        try {
            $dates = $request->dates;
            if (!empty($dates)) {
                Attendance::whereIn('attendance_date', $dates)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Selected attendance records deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Download individual PDF payslip
     */
    public function downloadPdf($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.payslip_pdf', compact('payroll'));
        
        $filename = 'payslip_' . str_replace(' ', '_', $payroll->employee->name) . '_' . $payroll->month . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Bulk download PDF payslips for a month/filter
     */
    public function bulkDownloadPdf(Request $request)
    {
        $query = Payroll::with('employee');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->get();

        if ($payrolls->count() == 0) {
            return back()->with('warning', 'No payroll records found for the selected filter.');
        }

        // Generate a single PDF with all slips separated by page breaks
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.bulk_payslip_pdf', compact('payrolls'));
        
        $filename = 'bulk_payslips_' . ($request->month ?? date('Y-m')) . '.pdf';
        return $pdf->download($filename);
    }
}
