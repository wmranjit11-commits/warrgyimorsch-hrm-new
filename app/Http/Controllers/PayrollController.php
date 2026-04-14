<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
// use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    /**
     * Display Attendance List
     */
   public function attendance(Request $request)
    {
        $query = Attendance::query();

        $query->join('employees', 'attendances.employee_id', '=', 'employees.id');

         // ✅ Apply only if user selects filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('attendance_date', '>=', $request->start_date)
                ->whereDate('attendance_date', '<=', $request->end_date);
        }
        // ✅ GROUPING (required for your blade)
        $attendance = $query->selectRaw("
                attendances.attendance_date,
                COUNT(CASE WHEN attendances.status = 'present' THEN 1 END) as present_count,
                COUNT(CASE WHEN attendances.status = 'half_day' THEN 1 END) as half_day_count,
                COUNT(CASE WHEN attendances.status = 'wfh' THEN 1 END) as wfh_count,
                COUNT(CASE WHEN attendances.status IN ('absent','leave') THEN 1 END) as leave_count,
                COUNT(CASE WHEN attendances.total_hours >= 9 THEN 1 END) as overtime_count,
                SUM(CASE 
                    WHEN TIME(attendances.check_out) <= SUBTIME(TIME(employees.time_out), '00:30:00')
                    AND attendances.check_out IS NOT NULL
                    THEN 1
                    ELSE 0
                END) as early_count
            ")
            ->groupBy('attendances.attendance_date')
            ->orderBy('attendances.attendance_date', 'desc')
            ->paginate(31);

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
    // public function storeAttendance(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         if (!$request->attendance_date) {
    //             throw new \Exception('Attendance date is required.');
    //         }

    //         foreach ($request->employees as $employeeData) {
    //                 $checkIn = !empty($employeeData['check_in']) ? $employeeData['check_in'] : null;
    //                 $checkOut = !empty($employeeData['check_out']) ? $employeeData['check_out'] : null;

    //                 if ($checkIn || $checkOut) {
    //                 $status = $employeeData['status'] ?? 'present';


    //               $totalHours = 0;

    //                 if ($checkIn && $checkOut) {
    //                     try {
    //                         $in = Carbon::createFromFormat('H:i', $checkIn);
    //                         $out = Carbon::createFromFormat('H:i', $checkOut);

    //                         // 🔥 Get difference (can be negative)
    //                         $diffMinutes = $in->diffInMinutes($out, false);

    //                         // ✅ Handle night shift (e.g. 10 PM → 6 AM)
    //                         if ($diffMinutes < 0) {
    //                             $diffMinutes += 24 * 60;
    //                         }

    //                        $totalHours = round($diffMinutes / 60, 2);

    //                     } catch (\Exception $e) {
    //                         $totalHours = 0;
    //                     }
    //                 }

    //                 // Auto-Leave Logic: If no check-in, set status to 'leave'
    //                 if (empty($checkIn)) {
    //                     $status = 'leave';
    //                     $checkOut = null; // Ensure no check-out if no check-in
    //                 }

    //                 Attendance::updateOrCreate(
    //                     [
    //                         'employee_id' => $employeeData['employee_id'],
    //                         'attendance_date' => $request->attendance_date
    //                     ],
    //                     [
    //                         'check_in' => $checkIn,
    //                         'check_out' => $checkOut,
    //                         'status' => $status,
    //                         'total_hours' => round($totalHours, 2),
    //                     ]
    //                 );
    //             }
    //         }

    //         return redirect()->route('payroll.attendance', ['start_date' => $request->attendance_date, 'end_date' => $request->attendance_date])
    //             ->with('success', 'Attendance records have been updated successfully! ✓');
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Error: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    public function storeAttendance(Request $request)
    {
        foreach ($request->employees as $emp) {

            // skip if everything empty and absent
            if (
                empty($emp['check_in']) &&
                empty($emp['check_out']) &&
                ($emp['status'] ?? 'present') == 'present'
            ) {
                continue;
            }

            $checkIn = !empty($emp['check_in']) ? $emp['check_in'] : null;
            $checkOut = !empty($emp['check_out']) ? $emp['check_out'] : null;

            $totalHours = 0;

            if ($checkIn && $checkOut) {
                try {
                    $in = \Carbon\Carbon::createFromFormat('H:i', $checkIn);
                    $out = \Carbon\Carbon::createFromFormat('H:i', $checkOut);

                    $diffMinutes = $in->diffInMinutes($out, false);

                    // night shift
                    if ($diffMinutes < 0) {
                        $diffMinutes += 24 * 60;
                    }

                    $totalHours = round($diffMinutes / 60, 2);

                } catch (\Exception $e) {
                    $totalHours = 0;
                }
            }

            Attendance::create([
                'employee_id' => $emp['employee_id'],
                'attendance_date' => $request->attendance_date,
                'check_in' => !empty($emp['check_in']) ? $emp['check_in'] : null,
                'check_out' => !empty($emp['check_out']) ? $emp['check_out'] : null,
                'total_hours' => $totalHours,
                'status' => $emp['status'] ?? 'present',
            ]);
        }

        return redirect()->route('payroll.attendance')->with('success', 'Attendance saved successfully');
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
            // echo $totalDays;exit;
            // 📊 Get Attendance
            $records = Attendance::where('employee_id', $employeeId)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $monthNum)
                ->get();

            $leaves = LeaveApplication::where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->where(function ($q) use ($year, $monthNum) {
                    $q->whereMonth('start_date', $monthNum)
                    ->orWhereMonth('end_date', $monthNum);
                })
                ->get();

                $leaveDays = 0;
                foreach ($leaves as $leave) {

                    // If already stored → best case
                    if (!empty($leave->total_days)) {
                        $leaveDays += $leave->total_days;
                    } else {

                        // fallback (date diff)
                        $days = $leave->start_date->diffInDays($leave->end_date) + 1;
                        $leaveDays += $days;
                    }
                }

           $attendanceDays = 0;

            foreach ($records as $r) {

                switch ($r->status) {

                    case 'present':
                    case 'work_from_home':
                    case 'late':
                        $attendanceDays += 1;
                        break;

                    case 'half_day':
                        $attendanceDays += 0.5;
                        break;

                    case 'absent':
                    default:
                        break;
                }
            }

            // Leaves
            $leaveDays = 0;

            foreach ($leaves as $leave) {
                $leaveDays += $leave->total_days ?? 
                    ($leave->start_date->diffInDays($leave->end_date) + 1);
            }

            // Final
            $payableDays = min($attendanceDays + $leaveDays, $totalDays);

            // Safety
            $payableDays = min($payableDays, $totalDays);
            $overrideSalary = $request->override_salary ?? null;
            $unpaidDays = $totalDays - $payableDays;

            // 💰 STEP 2: Monthly Salary (Annual → Monthly)
            $monthlySalary = (
                ($employee->basic_salary ?? 0) +
                ($employee->hra ?? 0) +
                ($employee->conveyance_allowance ?? 0) +
                ($employee->medical_allowance ?? 0) +
                ($employee->other_allowance ?? 0)
            );

            // Per day salary
            $perDaySalary = $monthlySalary / $totalDays;

            // Gross Salary
            $grossSalary = $perDaySalary * $payableDays;

            // Override (HR control)
            if ($overrideSalary) {
                $grossSalary = $overrideSalary;
            }

            // 📉 STEP 3: Deductions
            $pf = 0;
            $esi = 0;

            $pf = $request->pf ?? $pf;
            $esi = $request->esi ?? $esi;
            $otherDeduction = $request->other_deduction ?? 0;

            $totalDeductions = $pf + $esi + $otherDeduction;

            // // PF (12% of basic portion only)
            // if ($employee->pf) {
            //     $basicMonthly = ($employee->basic_salary ?? 0) / 12;
            //     $pf = ($basicMonthly / $totalDays * $payableDays) * 0.12;
            // }

            // // ESI (only if salary <= 21000)
            // if ($employee->esi && $grossSalary <= 21000) {
            //     $esi = $grossSalary * 0.0075;
            // }

            // $totalDeductions = $pf + $esi;

            // 💵 STEP 4: Net Salary
            $netSalary = max(0, $grossSalary - $totalDeductions);

            // 📉 Salary Loss
            $fullMonthSalary = $monthlySalary;
            $salaryLoss = $fullMonthSalary - $grossSalary;

            // 📦 Final Data
           $payrollData = [
                'employee_id' => $employeeId,
                'month' => $month,

                // Attendance
                'payable_days' => $payableDays,
                'unpaid_days' => round($unpaidDays, 2),

                //  ORIGINAL MONTHLY SALARY (IMPORTANT)
                'basic_salary' => $employee->basic_salary,
                'hra' => $employee->hra,
                'conveyance_allowance' => $employee->conveyance_allowance,
                'medical_allowance' => $employee->medical_allowance,
                'other_allowance' => $employee->other_allowance,

                //  CALCULATED (for reference only)
                'calculated_basic' => round(($employee->basic_salary / $totalDays) * $payableDays, 2),
                'calculated_hra' => round(($employee->hra / $totalDays) * $payableDays, 2),

                // Gross
                'gross_salary' => round($grossSalary, 2),

                // Deductions
                'pf_deduction' => round($pf, 2),
                'esi_deduction' => round($esi, 2),
                'other_deduction' => $otherDeduction,
                'deductions' => round($totalDeductions, 2),

                // Final
                'salary_loss' => round($salaryLoss, 2),
                'net_salary' => round($netSalary, 2),

                'status' => 'pending',
            ];

            // dd($payrollData);

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
     * Export attendance records (Professional Monthly Excel Grid)
     */
    public function exportAttendance(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        
        $monthName = Carbon::create($year, $month, 1)->format('F_Y');
        $filename = 'attendance_sheet_' . $monthName . '.xlsx';

        return Excel::download(new \App\Exports\AttendanceExport($year, $month), $filename);
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

    public function editByDate($attendance_date)
    {
        $attendances = Attendance::with('employee')
            ->whereDate('attendance_date', $attendance_date)
            ->get();

        if ($attendances->isEmpty()) {
            return redirect()->back()->with('error', 'No attendance records found for this date.');
        }

        $employees = $attendances->map(function ($attendance) {
            $employee = $attendance->employee;

            $employee->old_check_in = $attendance->check_in 
                ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') 
                : '';

            $employee->old_check_out = $attendance->check_out 
                ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') 
                : '';

            $employee->old_status = $attendance->status;

            // edit flags
            $employee->can_edit_check_in = empty($attendance->check_in);
            $employee->can_edit_check_out = empty($attendance->check_out);

            // absent if both empty
            $employee->is_absent = empty($attendance->check_in) && empty($attendance->check_out);

            // duration
            if ($attendance->check_in && $attendance->check_out) {
                $in = strtotime($attendance->check_in);
                $out = strtotime($attendance->check_out);

                if ($out < $in) {
                    $out += 86400;
                }

                $diff = $out - $in;
                $hours = floor($diff / 3600);
                $minutes = floor(($diff % 3600) / 60);

                $employee->old_duration = $hours . 'h ' . $minutes . 'm';
            } else {
                $employee->old_duration = '--';
            }

            return $employee;
        });

        return view('payroll.add-attendance', [
            'employees' => $employees,
            'edit_date' => $attendance_date,
            'is_edit' => true
        ]);
    }

    // public function updateByDate(Request $request, $attendance_date)
    // {
    //     foreach ($request->employees as $emp) {

    //         $attendance = Attendance::whereDate('attendance_date', $attendance_date)
    //             ->where('employee_id', $emp['employee_id'])
    //             ->first();

    //         if (!$attendance) {
    //             continue;
    //         }

    //         // update values directly
    //         $attendance->check_in = $emp['check_in'];
    //         $attendance->check_out = $emp['check_out'];
    //         $attendance->status = $emp['status'];

    //         $attendance->save();
    //     }

    //     return redirect()->back()->with('success', 'Attendance updated successfully');
    // }
    public function updateByDate(Request $request, $attendance_date)
    {
        foreach ($request->employees as $emp) {

            $attendance = Attendance::whereDate('attendance_date', $attendance_date)
                ->where('employee_id', $emp['employee_id'])
                ->first();

            if (!$attendance) {
                continue;
            }

            $checkIn = !empty($emp['check_in']) ? $emp['check_in'] : null;
            $checkOut = !empty($emp['check_out']) ? $emp['check_out'] : null;

            $totalHours = 0;

            // calculate total hours if both times exist
            if ($checkIn && $checkOut) {
                try {
                    $in = \Carbon\Carbon::createFromFormat('H:i', $checkIn);
                    $out = \Carbon\Carbon::createFromFormat('H:i', $checkOut);

                    $diffMinutes = $in->diffInMinutes($out, false);

                    // handle night shift
                    if ($diffMinutes < 0) {
                        $diffMinutes += 24 * 60;
                    }

                    $totalHours = round($diffMinutes / 60, 2);

                } catch (\Exception $e) {
                    $totalHours = 0;
                }
            }

            $attendance->check_in = $checkIn;
            $attendance->check_out = $checkOut;
            $attendance->status = $emp['status'];
            $attendance->total_hours = $totalHours;

            $attendance->save();
        }

        return redirect()->route('payroll.attendance')
            ->with('success', 'Attendance updated successfully');
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
