<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{


    private function getAttendanceAnalytics($from, $to, $employeeId = null)
    {
        $query = Attendance::join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->whereDate('attendance_date', '>=', $from)
            ->whereDate('attendance_date', '<=', $to);

        if ($employeeId) {
            $query->where('attendances.employee_id', $employeeId);
        }

        return $query->selectRaw("
            COUNT(CASE WHEN attendances.status = 'present' THEN 1 END) as present,
            COUNT(CASE WHEN attendances.status = 'half_day' THEN 1 END) as half_day,
            COUNT(CASE WHEN attendances.status = 'wfh' THEN 1 END) as wfh,
            COUNT(CASE WHEN attendances.status IN ('absent','leave') THEN 1 END) as leave_count,
            COUNT(CASE WHEN attendances.status = 'late' THEN 1 END) as late,

            SUM(
                CASE 
                    WHEN attendances.check_out IS NOT NULL AND (
                        (
                            attendances.status = 'present'
                            AND TIME(attendances.check_out) <= SUBTIME(TIME(employees.time_out), '00:30:00')
                        )
                        OR
                        (
                            attendances.status = 'half_day'
                            AND TIME(attendances.check_out) <= SUBTIME(
                                ADDTIME(TIME(employees.time_in), 
                                SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(employees.time_out, employees.time_in)) / 2)),
                                '00:30:00'
                            )
                        )
                    )
                    THEN 1 ELSE 0
                END
            ) as early_out
        ")->first();
    }

    public function index(Request $request)
    {
        $role = strtoupper(auth()->user()->role ?? 'USER');
         $isAdmin = in_array($role, ['MANAGER', 'SUPER_ADMIN', 'HR_EXECUTIVE', 'HR_INTERN']);
        $employeeId = auth()->user()->employee_id;

        $today = Carbon::today()->toDateString();

        // Month Filtering
        $selectedMonth = $request->get('month');
        $hasSelectedMonth = !empty($selectedMonth);

        if (!$hasSelectedMonth) {
            $selectedMonth = Carbon::now()->format('Y-m');
        }

        $selectedDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $selectedMonthLabel = $selectedDate->format('F Y');

        // Employee Metrics
        // $totalEmployees = $isAdmin ? Employee::count() : 1;
        $totalEmployees = Employee::count();

        // Attendance Metrics
        $isCurrentMonth = ($selectedMonth == Carbon::now()->format('Y-m'));

        if ($isAdmin) {
            if ($isCurrentMonth) {
                $todayPresent = Attendance::where('attendance_date', $today)->whereIn('status', ['present', 'half_day', 'late'])->count();
                $todayLeave = Attendance::where('attendance_date', $today)->whereIn('status', ['absent', 'leave'])->count();
                $attendanceRate = $totalEmployees > 0 ? round(($todayPresent / $totalEmployees) * 100, 1) : 0;
            } else {
                $daysInMonth = $selectedDate->daysInMonth;
                $monthPresent = Attendance::whereMonth('attendance_date', $selectedDate->month)
                    ->whereYear('attendance_date', $selectedDate->year)
                    ->whereIn('status', ['present', 'half_day', 'late', 'leave', 'absent'])
                    ->count();

                $todayPresent = $daysInMonth > 0 ? round($monthPresent / $daysInMonth) : 0;
                $todayLeave = 0;
                $attendanceRate = ($totalEmployees > 0 && $daysInMonth > 0) ? round(($monthPresent / ($totalEmployees * $daysInMonth)) * 100, 1) : 0;
            }

            // Payroll Metrics (Selected Month)
            $totalPaidAmount = Payroll::where('month', $selectedMonth)->where('status', 'paid')->sum('net_salary');
            $totalPendingAmount = Payroll::where('month', $selectedMonth)->where('status', 'pending')->sum('net_salary');
            $totalRejectedAmount = Payroll::where('month', $selectedMonth)->where('status', 'rejected')->sum('net_salary');
            $totalNetSalary = Payroll::where('month', $selectedMonth)->sum('net_salary');

            $totalEmpPaid = Payroll::where('month', $selectedMonth)->where('status', 'paid')->count();
            $totalEmpPending = Payroll::where('month', $selectedMonth)->where('status', 'pending')->count();
        } else {
            // Employee specific metrics
            $todayPresent = Attendance::where('employee_id', $employeeId)->where('attendance_date', $today)->whereIn('status', ['present', 'half_day', 'late'])->count();
            $todayLeave = Attendance::where('employee_id', $employeeId)->where('attendance_date', $today)->whereIn('status', ['absent', 'leave'])->count();
            $attendanceRate = $todayPresent > 0 ? 100 : 0;

            $myPayroll = Payroll::where('employee_id', $employeeId)->where('month', $selectedMonth)->first();
            $totalPaidAmount = ($myPayroll && $myPayroll->status == 'paid') ? $myPayroll->net_salary : 0;
            $totalPendingAmount = ($myPayroll && $myPayroll->status == 'pending') ? $myPayroll->net_salary : 0;
            $totalRejectedAmount = ($myPayroll && $myPayroll->status == 'rejected') ? $myPayroll->net_salary : 0;
            $totalNetSalary = $myPayroll ? $myPayroll->net_salary : 0;

            $totalEmpPaid = ($myPayroll && $myPayroll->status == 'paid') ? 1 : 0;
            $totalEmpPending = ($myPayroll && $myPayroll->status == 'pending') ? 1 : 0;
        }

        // OLD dashboard values (for Blade when NOT filtered)
        $present = $todayPresent;

        $wfh = Attendance::whereDate('attendance_date', $today)
                ->when(!$isAdmin, fn($q) => $q->where('employee_id', $employeeId))
                ->where('status', 'wfh')
                ->count();

        $late = Attendance::whereDate('attendance_date', $today)
                ->when(!$isAdmin, fn($q) => $q->where('employee_id', $employeeId))
                ->where('status', 'late')
                ->count();

        $leave = $todayLeave;

        // simple early count (basic version)
        $early = Attendance::whereDate('attendance_date', $today)
                ->when(!$isAdmin, fn($q) => $q->where('employee_id', $employeeId))
                ->whereNotNull('check_out')
                ->count();


        // NEW DATE FILTER ANALYTICS (ADD HERE ONLY)

        if ($request->has('from') || $request->has('filter')) {

            if ($request->filter == 'today') {
                $from = Carbon::today();
                $to   = Carbon::today();
            } elseif ($request->filter == 'yesterday') {
                $from = Carbon::yesterday();
                $to   = Carbon::yesterday();
            } elseif ($request->filter == 'week') {
                $from = Carbon::now()->subDays(7);
                $to   = Carbon::today();
            } elseif ($request->filter == 'month') {
                $from = Carbon::now()->startOfMonth();
                $to   = Carbon::today();
            } else {
                $from = $request->from ?? Carbon::today();
                $to   = $request->to ?? Carbon::today();
            }

            $analytics = $this->getAttendanceAnalytics(
                $from,
                $to,
                $isAdmin ? null : $employeeId
            );

            $rangePresent = $analytics->present ?? 0;
            $rangeWFH     = $analytics->wfh ?? 0;
            $rangeLeave   = $analytics->leave_count ?? 0;
            $rangeLate    = $analytics->late ?? 0;
            $rangeEarly   = $analytics->early_out ?? 0;
            $rangeHalfDay = $analytics->half_day ?? 0;

            $rangeCheckedIn = $rangePresent + $rangeWFH + $rangeHalfDay + $rangeLate;

            // $days = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
            // $rangeAttendanceRate = ($totalEmployees > 0 && $days > 0)
            //     ? round(($rangeCheckedIn / ($totalEmployees * $days)) * 100, 1)
            //     : 0;

            $rangeAttendanceRate = $totalEmployees > 0
            ? round(($rangeCheckedIn / $totalEmployees) * 100, 1)
            : 0;


        } else {
            $rangePresent = 0;
            $rangeWFH     = 0;
            $rangeLeave   = 0;
            $rangeLate    = 0;
            $rangeEarly   = 0;
            $rangeAttendanceRate = 0;
        }

        // Chart Data: 6 Months
        $chartMonths = [];
        $chartTotal = [];
        $chartPaid = [];
        $chartPending = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = (clone $selectedDate)->subMonths($i);
            $mLabel = $m->format('M/y');
            $mValue = $m->format('Y-m');

            $chartMonths[] = $mLabel;
            
            $pQuery = Payroll::where('month', $mValue);
            if (!$isAdmin) $pQuery->where('employee_id', $employeeId);

            $chartTotal[] = (clone $pQuery)->sum('net_salary');
            $chartPaid[] = (clone $pQuery)->where('status', 'paid')->sum('net_salary');
            $chartPending[] = (clone $pQuery)->where('status', 'pending')->sum('net_salary');
        }

        // Recent Activity
        $pRecent = Payroll::with('employee')->where('month', $selectedMonth);
        if (!$isAdmin) $pRecent->where('employee_id', $employeeId);
        $recentPayrolls = $pRecent->latest()->paginate(10);

        // Upcoming Holidays
        $upcomingHolidays = Holiday::where('date', '>=', $today)->orderBy('date')->limit(20)->get();

        // Selected month for leave report (default = last month)
        $leaveReport = $this->getLeaveReport($request);
        $employees = Employee::all();

        // Employee Leave on Today
        $todayLeaveEmployees = Attendance::with('employee')
            ->whereDate('attendance_date', $today)
            ->whereIn('status', ['leave']) // only leave (not absent)
            ->when(!$isAdmin, fn($q) => $q->where('employee_id', $employeeId))
            ->get();

        return view('dashboard', compact(
            'totalEmployees',
            'todayPresent',
            'todayLeave',
            'attendanceRate',
            'totalPaidAmount',
            'totalPendingAmount',
            'totalRejectedAmount',
            'totalNetSalary',
            'totalEmpPaid',
            'totalEmpPending',
            'chartMonths',
            'chartTotal',
            'chartPaid',
            'chartPending',
            'selectedMonth',
            'selectedMonthLabel',
            'recentPayrolls',
            'upcomingHolidays',
            'rangePresent',
            'rangeWFH',
            'rangeLeave',
            'rangeLate',
            'rangeEarly',
            'rangeAttendanceRate',
            'present',
            'wfh',
            'leave',
            'late',
            'early',
            'leaveReport',
            'employees',
            'todayLeaveEmployees',
        ));
    }

    // Latest Leave Report
    private function getLeaveReport(Request $request)
    {
        $query = \App\Models\Attendance::join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->join('leave_applications', function ($join) {
                $join->on('attendances.employee_id', '=', 'leave_applications.employee_id')
                    ->whereColumn('attendances.attendance_date', '>=', 'leave_applications.start_date')
                    ->whereColumn('attendances.attendance_date', '<=', 'leave_applications.end_date');
            })
            ->where('leave_applications.status', 'approved');

        // Employee filter
        if ($request->employee_id) {
            $query->where('employees.id', $request->employee_id);
        }

        // Date range
        $from = null;
        $to   = \Carbon\Carbon::today();

        if ($request->filter) {
            switch ($request->filter) {
                case 'week':   
                    $from = Carbon::now()->subWeek();
                    $to   = Carbon::today();
                    break;
                case 'month':  
                    $from = Carbon::now()->subMonth()->startOfMonth();
                    $to   = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case '3month': 
                    $from = Carbon::now()->subMonths(3);
                    $to   = Carbon::today();
                    break;
                case '6month': 
                    $from = Carbon::now()->subMonths(6);
                    $to   = Carbon::today();
                    break;
                case 'year': 
                    $from = Carbon::now()->subYear();
                    $to   = Carbon::today();
                    break;
            }
        }

        // Custom range overrides
        if ($request->from && $request->to) {
            $from = \Carbon\Carbon::parse($request->from);
            $to   = \Carbon\Carbon::parse($request->to);
        }

        // Default = last month
        if (!$request->filter && !$request->from) {
            $from = Carbon::now()->startOfMonth();
            $to = Carbon::now()->endOfMonth();
        }

        if ($from && $to) {
            $query->whereBetween('attendances.attendance_date', [$from, $to])
                ->whereDate('attendances.attendance_date', '<=', Carbon::today());
        }

        return $query->selectRaw("
                employees.id,
                employees.name,
                employees.designation,
                COUNT(CASE WHEN attendances.status IN ('leave') THEN 1 END) as leave_count
            ")
            ->groupBy('employees.id', 'employees.name', 'employees.designation')
            ->havingRaw("leave_count > 0")
            ->orderByDesc('leave_count')
            ->get();
    }


    /**
     * Get Full Year Breakdown (Requested by User)
     */
    public function getFullYearBreakdown(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $m = Carbon::createFromDate($year, $i, 1)->format('Y-m');
            $mLabel = Carbon::createFromDate($year, $i, 1)->format('F');

            $data[] = [
                'month' => $mLabel,
                'total_gross' => Payroll::where('month', $m)->sum('gross_salary'),
                'total_net' => Payroll::where('month', $m)->sum('net_salary'),
                'staff_count' => Payroll::where('month', $m)->count(),
                'status' => Payroll::where('month', $m)->where('status', 'pending')->exists() ? 'Pending' : 'Completed'
            ];
        }

        return response()->json([
            'success' => true,
            'year' => $year,
            'breakdown' => $data
        ]);
    }

    public function getMonthlySummary(Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedCarbon = Carbon::parse($selectedMonth . '-01');

        $history = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = (clone $selectedCarbon)->subMonths($i);
            $mValue = $m->format('Y-m');
            $mLabel = $m->format('M Y');

            $basic = Payroll::where('month', $mValue)->sum('basic_salary');
            $hra = Payroll::where('month', $mValue)->sum('hra');
            $medical = Payroll::where('month', $mValue)->sum('medical_allowance');
            $conveyance = Payroll::where('month', $mValue)->sum('conveyance_allowance');
            $other_allw = Payroll::where('month', $mValue)->sum('other_allowance');

            $pf = Payroll::where('month', $mValue)->sum('pf_deduction');
            $esi = Payroll::where('month', $mValue)->sum('esi_deduction');
            $other_ded = Payroll::where('month', $mValue)->sum('other_deduction');

            $history[] = [
                'month' => $mLabel,
                'earnings' => $basic + $hra + $medical + $conveyance + $other_allw,
                'deductions' => $pf + $esi + $other_ded,
                'net' => Payroll::where('month', $mValue)->sum('net_salary'),
                'details' => [
                    'basic' => $basic,
                    'hra' => $hra,
                    'medical' => $medical,
                    'conveyance' => $conveyance,
                    'other_allw' => $other_allw,
                    'pf' => $pf,
                    'esi' => $esi,
                    'other_ded' => $other_ded
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'selectedMonth' => $selectedMonth,
            'history' => $history,
            // Keep current for backward compatibility if needed in old scripts
            'current' => end($history)
        ]);
    }

    public function getChartData(Request $request)
    {
        $range = (int) $request->get('range', 6);
        $chartMonths = [];
        $chartPaid = [];
        $chartPending = [];
        $chartRejected = [];

        for ($i = ($range - 1); $i >= 0; $i--) {
            $m = Carbon::now()->startOfMonth()->subMonths($i);
            $mLabel = $m->format('M/y');
            $mValue = $m->format('Y-m');

            $chartMonths[] = $mLabel;
            $chartTotal[] = Payroll::where('month', $mValue)->sum('gross_salary');
            $chartPaid[] = Payroll::where('month', $mValue)->where('status', 'paid')->sum('net_salary');
            $chartPending[] = Payroll::where('month', $mValue)->where('status', 'pending')->sum('net_salary');
            $chartRejected[] = Payroll::where('month', $mValue)->where('status', 'rejected')->sum('net_salary');
        }

        return response()->json([
            'success' => true,
            'labels' => $chartMonths,
            'series' => [
                [
                    'name' => 'Total Payroll',
                    'type' => 'area',
                    'data' => $chartTotal
                ],
                [
                    'name' => 'Completed',
                    'type' => 'bar',
                    'data' => $chartPaid
                ],
                [
                    'name' => 'Pending',
                    'type' => 'bar',
                    'data' => $chartPending
                ]
            ]
        ]);
    }
}
