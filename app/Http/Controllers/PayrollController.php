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
            $isSunday = $current->isSunday();
            
            $datesData[] = (object) [
                'attendance_date' => $dateStr,
                'is_holiday' => $isHoliday,
                'is_sunday' => $isSunday,
                'holiday_title' => $isHoliday ? $holidays[$dateStr]->title : ($isSunday ? 'Weekly Off' : null),
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
        $datesData = collect($datesData)->filter(function ($item) {
            // Only show dates with records, holidays, Sundays, or Today
            $isToday = $item->attendance_date === date('Y-m-d');
            return $item->count > 0 || $item->is_holiday || $item->is_sunday || $isToday;
        })->sortByDesc('attendance_date');

        return view('payroll.attendance', [
            'attendance_dates' => $datesData,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'current_month' => date('Y-m', strtotime($startDate))
        ]);
    }

    /**
     * Detailed Monthly Attendance View (Employee-wise punches)
     */
    public function attendanceDetailed(Request $request)
    {
        $month = $request->month ?: date('Y-m');
        $search = $request->search;

        $startDate = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $query = Attendance::with('employee')
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('employee_id', 'asc')
            ->paginate(50);

        return view('payroll.attendance-detailed', compact('attendances', 'month', 'startDate', 'endDate'));
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
        $holidays = Holiday::all()->pluck('title', 'date');
        return view('payroll.add-attendance', compact('employees', 'holidays'));
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

                    // Auto-Leave Logic: If no check-in, set status to 'leave' UNLESS there is a check-out (for fixing later)
                    if (empty($checkIn) && !empty($checkOut)) {
                        $status = 'present'; // Allow to be edited later
                    } elseif (empty($checkIn)) {
                        $status = 'leave';
                        $checkOut = null; 
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
            $monthStr = $request->month; // Format: YYYY-MM
            $employeeId = $request->employee_id;

            // 1. Future Month Restriction
            $selectedDate = Carbon::createFromFormat('Y-m', $monthStr)->startOfMonth();
            $currentMonthStart = Carbon::now()->startOfMonth();

            if ($selectedDate->gt($currentMonthStart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: You cannot calculate payroll for future months.',
                ]);
            }

            // Get employee
            $employee = Employee::findOrFail($employeeId);
            $joiningDate = $employee->date_of_joining ? Carbon::parse($employee->date_of_joining) : null;

            $startOfMonth = $selectedDate->copy()->startOfMonth();
            $endOfMonth = $selectedDate->copy()->endOfMonth();
            $totalDaysInMonth = $selectedDate->daysInMonth;

            // Real-time Range Handling: If current month, only calculate up to Today/Yesterday
            $calcStart = $startOfMonth->copy();
            if ($joiningDate && $joiningDate->gt($startOfMonth) && $joiningDate->lte($endOfMonth)) {
                $calcStart = $joiningDate->copy();
            }

            $calcEnd = $endOfMonth->copy();
            $today = Carbon::today();
            $rangeEndForStats = $endOfMonth->copy();
            if ($selectedDate->isCurrentMonth() && $today->lt($endOfMonth)) {
                $rangeEndForStats = $today->copy(); // For stats like absent/paid-offs, only look up to today
            }

            // 2. Fetch Holidays, Attendance and Approved Leaves for the range
            $holidayDates = \App\Models\Holiday::whereBetween('date', [$calcStart->toDateString(), $rangeEndForStats->toDateString()])
                ->pluck('date')->toArray();

            $attendanceData = Attendance::where('employee_id', $employeeId)
                ->whereBetween('attendance_date', [$calcStart->toDateString(), $rangeEndForStats->toDateString()])
                ->get()
                ->keyBy(function ($item) {
                    return $item->attendance_date instanceof \Carbon\Carbon
                        ? $item->attendance_date->toDateString()
                        : (string) $item->attendance_date;
                });

            $approvedLeaves = \App\Models\LeaveApplication::where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->where(function ($query) use ($calcStart, $rangeEndForStats) {
                    $query->whereBetween('start_date', [$calcStart->toDateString(), $rangeEndForStats->toDateString()])
                        ->orWhereBetween('end_date', [$calcStart->toDateString(), $rangeEndForStats->toDateString()]);
                })
                ->get();

            // 3. Daily Loop for status determination
            $workedDays = 0;
            $paidOffDays = 0;
            $absentDaysTotal = 0; // Cumulative for adj
            $leavesCount = 0; // Purely for display
            $debugLog = [];

            $current = $calcStart->copy();
            while ($current->lte($rangeEndForStats)) {
                $dStr = $current->toDateString();
                $isSunday = $current->isSunday();
                $isHoliday = in_array($dStr, $holidayDates);

                $att = $attendanceData->get($dStr);
                $hasLeaveApp = $approvedLeaves->contains(function ($leave) use ($current) {
                    return $current->between($leave->start_date, $leave->end_date ?? $leave->start_date);
                });

                if ($isSunday || $isHoliday) {
                    $paidOffDays++;
                    $debugLog[] = "$dStr: Paid Off (" . ($isSunday ? "Sunday" : "Holiday") . ")";
                } elseif ($att) {
                    $status = trim(strtolower($att->status));
                    if (in_array($status, ['present', 'late'])) {
                        $workedDays += 1;
                        $debugLog[] = "$dStr: Worked ($status)";
                    } elseif ($status === 'half_day') {
                        $workedDays += 0.5;
                        $absentDaysTotal += 0.5;
                        $debugLog[] = "$dStr: Half-Day";
                    } else { // 'absent' or 'leave' in attendance table
                        $absentDaysTotal += 1;
                        $leavesCount += ($status === 'leave' ? 1 : 0);
                        $debugLog[] = "$dStr: Marked $status";
                    }
                } elseif ($hasLeaveApp) {
                    $absentDaysTotal += 1;
                    $leavesCount += 1;
                    $debugLog[] = "$dStr: Approved Leave Application";
                } else {
                    $absentDaysTotal += 1;
                    $debugLog[] = "$dStr: Absent (No Record)";
                }
                $current->addDay();
            }

            // 4. Simplified Rule: No automatic adjustment. User handles it manually.
            $payableDays = $workedDays + $paidOffDays;

            // Limit payable days to total days in the month
            if ($payableDays > $totalDaysInMonth) {
                $payableDays = $totalDaysInMonth;
            }

            // 5. Calculate Components (Pro-rated - Denominator is always the FULL month days)
            $fullBasic = (float) ($employee->basic_salary ?: 0);
            $fullHRA = (float) ($employee->hra ?: 0);
            $fullConv = (float) ($employee->conveyance_allowance ?: 0);
            $fullMed = (float) ($employee->medical_allowance ?: 0);
            $fullOther = (float) ($employee->other_allowance ?: 0);

            $prorateFactor = $payableDays / $totalDaysInMonth;

            $pBasic = round($fullBasic * $prorateFactor, 2);
            $pHRA = round($fullHRA * $prorateFactor, 2);
            $pConv = round($fullConv * $prorateFactor, 2);
            $pMed = round($fullMed * $prorateFactor, 2);
            $pOther = round($fullOther * $prorateFactor, 2);

            $grossSalary = round($pBasic + $pHRA + $pConv + $pMed + $pOther, 2);
            
            // Period Potential: Total possible earnings for the elapsed days so far
            $totalDaysInRange = $rangeEndForStats->diffInDays($calcStart) + 1;
            $periodPotential = round(($fullBasic + $fullHRA + $fullConv + $fullMed + $fullOther) * ($totalDaysInRange / $totalDaysInMonth), 2);
            $salaryLoss = round($periodPotential - $grossSalary, 2); 

            // 6. Deductions
            $pfDeduction = 0;
            $esiDeduction = 0;

            if ($employee->pf_number) {
                $pfDeduction = round($pBasic * 0.12, 2);
            }
            if ($employee->esi_number) {
                $esiDeduction = round($grossSalary * 0.0075, 2);
            }

            $totalDeductions = round($pfDeduction + $esiDeduction + $salaryLoss, 2);
            $netSalary = round($periodPotential - $totalDeductions, 2); 

            $payrollData = [
                'employee_id' => $employeeId,
                'month' => $monthStr,
                'payable_days' => round($payableDays, 1),
                'unpaid_days' => round($totalDaysInRange - $payableDays, 1),
                'salary_loss' => $salaryLoss,
                'basic_salary' => $pBasic,
                'hra' => $pHRA,
                'conveyance_allowance' => $pConv,
                'medical_allowance' => $pMed,
                'other_allowance' => $pOther,
                'gross_salary' => $grossSalary,
                'deductions' => $totalDeductions,
                'pf_deduction' => $pfDeduction,
                'esi_deduction' => $esiDeduction,
                'other_deduction' => 0,
                'net_salary' => $netSalary,
                'status' => 'pending',
                'monthly_salary' => ($fullBasic + $fullHRA + $fullConv + $fullMed + $fullOther),
                'full_hra' => $fullHRA,
                'full_conveyance' => $fullConv,
                'full_medical' => $fullMed,
                'full_other' => $fullOther,
                'has_pf' => !empty($employee->pf_number),
                'has_esi' => !empty($employee->esi_number),
            ];

            return response()->json([
                'success' => true,
                'payroll' => $payrollData,
                'details' => [
                    'worked_days' => round($workedDays, 1),
                    'absent_days' => round($absentDaysTotal, 1),
                    'leaves_taken' => round($leavesCount, 1),
                    'paid_offs' => $paidOffDays,
                    'allowed_leaves' => 0,
                    'total_payable' => round($payableDays, 1),
                    'days_in_month' => $totalDaysInMonth,
                    'total_days_in_range' => $totalDaysInRange,
                    'calc_up_to' => $rangeEndForStats->toDateString(),
                    'debug_log' => $debugLog
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage() . ' On line ' . $e->getLine(),
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
            foreach ($cols as $c) {
                if (isset($data[$c]))
                    $data[$c] = (float) str_replace(',', '', $data[$c]);
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

            // 2. Import Process (Using instance to capture the month)
            $import = new AttendanceImport();
            Excel::import($import, $request->file('import_file'));

            // Redirect to attendance page with the month from the file
            $month = $import->importedMonth ?: date('Y-m');

            return redirect()->route('payroll.attendance', ['month' => $month])
                ->with('success', 'Success: Attendance data imported successfully!');

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
     * Update an individual attendance record
     */
    public function updateAttendanceRecord(Request $request, $id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;

            $totalHours = 0;
            $status = $attendance->status;

            if ($checkIn && $checkOut) {
                $in = Carbon::parse($checkIn);
                $out = Carbon::parse($checkOut);

                // Handle night shifts
                if ($out->lessThan($in)) {
                    $out->addDay();
                }

                $totalHours = $out->diffInMinutes($in) / 60;
                
                // If it's a punch update, we assume they are present/late unless it's a leave
                if ($status === 'absent' || $status === 'leave') {
                    $status = 'present';
                }
            } elseif (empty($checkIn) && !empty($checkOut)) {
                $status = 'present'; // Allow fixing later
                $totalHours = 0;
            } elseif (empty($checkIn)) {
                $status = 'leave';
                $checkOut = null;
            }

            $attendance->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => $status,
                'total_hours' => round($totalHours, 2),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully!',
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
