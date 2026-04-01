<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Exports\MonthlyAttendanceExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{

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
     * Get attendance records with filtering (Consolidated)
     */
    public function attendance(Request $request)
    {
        $startDate = $request->start_date ?: date('Y-m-01');
        $endDate = $request->end_date ?: date('Y-m-t');

        // Handle full month selection
        if ($request->filled('month')) {
            $monthDate = \Carbon\Carbon::parse($request->month . '-01');
            $startDate = $monthDate->copy()->startOfMonth()->toDateString();
            $endDate = $monthDate->copy()->endOfMonth()->toDateString();
        }

        // Fetch attendance grouped by date with counts
        $attendanceData = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->selectRaw("attendance_date, 
                        COUNT(CASE WHEN LOWER(status) IN ('present', 'late') THEN 1 END) as present_count,
                        COUNT(CASE WHEN LOWER(status) = 'absent' THEN 1 END) as absent_count,
                        COUNT(CASE WHEN LOWER(status) = 'half_day' THEN 1 END) as half_day_count,
                        COUNT(CASE WHEN LOWER(status) = 'leave' THEN 1 END) as leave_count,
                        COUNT(CASE WHEN total_hours > 9 THEN 1 END) as overtime_count,
                        COUNT(*) as total_count")
            ->groupBy('attendance_date')
            ->get()
            ->mapWithKeys(function ($item) {
                // Formatting the date key to ensure perfect string matching in the date loop
                return [date('Y-m-d', strtotime($item->attendance_date)) => $item];
            });

        // Fetch Holidays for the range
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy('date');

        // Generate dates from the start of the month/range up to 'today' (or endDate if past)
        $datesData = [];
        $current = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        // Don't show future dates in the history
        $today = \Carbon\Carbon::today();
        if ($end->gt($today)) {
            $end = $today;
        }

        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $isHoliday = isset($holidays[$dateStr]);
            $datesData[] = (object)[
                'attendance_date' => $dateStr,
                'is_holiday' => $isHoliday,
                'holiday_title' => $isHoliday ? $holidays[$dateStr]->title : null,
                'count' => $attendanceData[$dateStr]->total_count ?? 0,
                'present' => $attendanceData[$dateStr]->present_count ?? 0,
                'absent' => $attendanceData[$dateStr]->absent_count ?? 0,
                'half_day' => $attendanceData[$dateStr]->half_day_count ?? 0,
                'leave' => $attendanceData[$dateStr]->leave_count ?? 0,
                'overtime' => $attendanceData[$dateStr]->overtime_count ?? 0
            ];
            $current->addDay();
        }

        // Sort descending to show latest records first
        $datesData = collect($datesData)->filter(function($item) {
            // Only show dates with records, holidays, or Today/Yesterday (if today is the month being viewed)
            $isToday = $item->attendance_date === date('Y-m-d');
            return $item->count > 0 || $item->is_holiday || $isToday;
        })->sortByDesc('attendance_date');
        
        return view('payroll.attendance', [
            'attendance_dates' => $datesData,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'current_month' => date('Y-m', strtotime($startDate))
        ]);
    }

    public function getAttendance(Request $request)
    {
        return $this->attendance($request);
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
        try {
            if (!$request->attendance_date) {
                throw new \Exception('Attendance date is required.');
            }

            foreach ($request->employees as $employeeData) {
                if ($employeeData['employee_id']) {
                    $status = $employeeData['status'] ?? 'present';

                    $checkIn = !empty($employeeData['check_in']) ? $employeeData['check_in'] : null;
                    $checkOut = !empty($employeeData['check_out']) ? $employeeData['check_out'] : null;

                    $totalHours = 0;
                    if ($checkIn && $checkOut) {
                        try {
                            $in = Carbon::createFromFormat('H:i', $checkIn);
                            $out = Carbon::createFromFormat('H:i', $checkOut);

                            // Handle night shifts (check-out after midnight)
                            if ($out->lessThan($in)) {
                                $out->addDay();
                            }

                            $totalHours = $out->diffInMinutes($in) / 60;
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
                            'total_hours' => max(0, round($totalHours, 2)),
                        ]
                    );
                }
            }

            $targetMonth = date('Y-m', strtotime($request->attendance_date));
            return redirect()->route('payroll.attendance', ['month' => $targetMonth])
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
        $employees = Employee::orderBy('name')->get();
        return view('payroll.calculation', compact('employees'));
    }

    /**
     * Calculate and display payroll
     */
    public function calculatePayroll(Request $request)
    {
        try {
            $month = $request->month; // Format: YYYY-MM
            $employeeId = $request->employee_id;

            // 1. Future Month Restriction (Allow current and past months)
            $selectedDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $currentMonthStart = Carbon::now()->startOfMonth();

            if ($selectedDate->gt($currentMonthStart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: You cannot calculate payroll for future months.',
                ]);
            }

            // Get employee
            $employee = Employee::findOrFail($employeeId);

            $year = $selectedDate->format('Y');
            $monthNum = $selectedDate->format('m');
            $startOfMonth = $selectedDate->copy()->startOfMonth()->toDateString();
            $endOfMonth = $selectedDate->copy()->endOfMonth()->toDateString();

            // 2. Fetch Holidays for this month (Index Friendly)
            $holidaysCount = \App\Models\Holiday::whereBetween('date', [$startOfMonth, $endOfMonth])->count();

            // 3. Get attendance stats for this month in ONE query (Lightning Fast)
            $stats = Attendance::where('employee_id', $employeeId)
                ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
                ->selectRaw("
                    COUNT(*) as total_attendance,
                    SUM(CASE 
                        WHEN LOWER(status) = 'half_day' THEN 4 
                        WHEN LOWER(status) IN ('present', 'late') THEN IFNULL(CASE WHEN total_hours > 8 THEN 8 ELSE total_hours END, 8)
                        ELSE 0 
                    END) as total_hours_worked
                ")
                ->first();

            $hasAttendance = $stats->total_attendance > 0;
            $totalHoursWorked = (float)$stats->total_hours_worked ?: 0;

            $totalDays = $selectedDate->daysInMonth;
            $workedDays = $totalHoursWorked / 8;

            if (!$hasAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: This employee has no attendance marked for this month. Calculation skipped.',
                ]);
            }

            // 5. Calculate Payable Days (Worked + 1.5 Allowed + Holidays)
            $allowedLeaves = 1.5;
            $payableDays = $workedDays + $allowedLeaves + $holidaysCount;

            // Cap at total days in month
            if ($payableDays > $totalDays) {
                $payableDays = $totalDays;
            }

            // 5. Calculate Salary Components (Pro-rated Basic Salary Only)
            $rawBasic = $employee->basic_salary ?? 0;
            $monthlyBasic = (float) str_replace(',', '', $rawBasic);

            // Pro-rate Basic Salary based on Payable Days
            // If totalDays is 0 (shouldn't happen), default to 30 to avoid division by zero
            $daysInMonth = $totalDays > 0 ? $totalDays : 30;
            $pBasic = max(0, ($monthlyBasic / $daysInMonth) * $payableDays);

            // All other components are explicitly 0 as per "Basic Only" policy
            $pHRA = 0;
            $pConveyance = 0;
            $pMedical = 0;
            $pOther = 0;

            $grossSalary = $pBasic;
            $salaryLoss = max(0, $monthlyBasic - $pBasic);
            $unpaidDays = max(0, $totalDays - $payableDays);

            // No PF/ESI/Deductions as requested ("pe pf vagera kuchnhi ketgea")
            $totalDeductions = 0;
            $netSalary = max(0, $grossSalary);

            $payrollData = [
                'employee_id' => $employeeId,
                'month' => $month,
                'payable_days' => round($payableDays, 1),
                'unpaid_days' => round($unpaidDays, 1),
                'salary_loss' => round($salaryLoss, 2),
                'basic_salary' => round($pBasic, 2),
                'hra' => round($pHRA, 2),
                'conveyance_allowance' => round($pConveyance, 2),
                'medical_allowance' => round($pMedical, 2),
                'other_allowance' => round($pOther, 2),
                'gross_salary' => round($grossSalary, 2),
                'deductions' => 0,
                'pf_deduction' => 0,
                'esi_deduction' => 0,
                'other_deduction' => 0,
                'net_salary' => round($netSalary, 2),
                'status' => 'pending',
            ];

            return response()->json([
                'success' => true,
                'payroll' => $payrollData,
                'details' => [
                    'worked_days' => round($workedDays, 1),
                    'allowed_leaves' => $allowedLeaves,
                    'holidays' => $holidaysCount,
                    'total_payable' => round($payableDays, 1),
                    'days_in_month' => $daysInMonth,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get monthly attendance summary for an employee (AJAX)
     */
    public function getEmployeeAttendance(Request $request)
    {
        try {
            $employeeId = $request->employee_id;
            $month = $request->month; // YYYY-MM

            // 1. Validate Month (Restrict Future Calculation)
            $inputDate = Carbon::parse($month . '-01')->startOfMonth();
            $currentMonthStart = Carbon::now()->startOfMonth();

            if ($inputDate->gt($currentMonthStart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot view attendance for future months.'
                ], 400);
            }

            $totalDays = $inputDate->daysInMonth;
            $year = $inputDate->year;
            $monthNum = $inputDate->month;

            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $monthNum)
                ->orderBy('attendance_date', 'asc')
                ->get();

            // Fetch Holidays for context
            $holidays = \App\Models\Holiday::whereYear('date', $year)
                ->whereMonth('date', $monthNum)
                ->get()
                ->pluck('date')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'holidays' => $holidays,
                'month_name' => $inputDate->format('F Y')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store calculated payroll
     */
    public function storePayroll(Request $request)
    {
        try {
            $data = $request->all();
            
            // Clean numeric fields
            $cols = ['payable_days', 'unpaid_days', 'salary_loss', 'gross_salary', 'basic_salary', 'net_salary'];
            foreach($cols as $c) {
                if(isset($data[$c])) $data[$c] = (float) str_replace(',', '', $data[$c]);
            }

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
        // 1. Validation Logic
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return back()->with('warning', 'Warning: Please upload a valid Excel or CSV file!');
        }

        try {
            if ($request->file('import_file')->getSize() == 0) {
                return back()->with('warning', 'Warning: The uploaded Excel file is empty.');
            }

            // 2. Import Process
            Excel::import(new AttendanceImport, $request->file('import_file'));

            return back()->with('success', 'Success: Attendance data imported successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: Something went wrong. ' . $e->getMessage());
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
        if ($request->type === 'monthly_sheet') {
            return $this->exportMonthlyAttendanceSheet($request);
        }

        $query = Attendance::with('employee');
        // ... existing vertical export logic ...
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

        $filename = 'attendance_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['EMPLOYEE', 'DATE', 'CHECK IN', 'CHECK OUT', 'STATUS', 'DURATION']);

            foreach ($attendances as $att) {
                fputcsv($file, [
                    $att->employee->name,
                    $att->attendance_date,
                    $att->check_in,
                    $att->check_out,
                    $att->status,
                    $att->total_hours . ' hrs'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Monthly Attendance Sheet (Matrix Format)
     */
    private function exportMonthlyAttendanceSheet(Request $request)
    {
        $year = $request->year ?: date('Y');
        $month = $request->month ?: date('m');
        $date = Carbon::createFromDate($year, $month, 1);
        
        $filename = 'Monthly_Attendance_' . $date->format('F_Y') . '.xlsx';
        
        return Excel::download(new MonthlyAttendanceExport($year, $month), $filename);
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
     * Download individual PDF payslip (Optimized for size)
     */
    public function downloadPdf($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.payslip_pdf', compact('payroll'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'Helvetica'
            ]);

        $filename = 'payslip_' . str_replace(' ', '_', $payroll->employee->name) . '_' . $payroll->month . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Bulk download PDF payslips for a month/filter (Optimized)
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
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.bulk_payslip_pdf', compact('payrolls'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultFont' => 'Helvetica'
            ]);

        $filename = 'bulk_payslips_' . ($request->month ?? date('Y-m')) . '.pdf';
        return $pdf->download($filename);
    }
}
