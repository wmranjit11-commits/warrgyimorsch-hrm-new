<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Holiday;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{

    private function getAttendanceAnalytics($from, $to, $employeeId = null)
    {
        $fromDate = Carbon::parse($from)->toDateString();
        $toDate = Carbon::parse($to)->toDateString();

        $query = Attendance::whereBetween('attendance_date', [$fromDate, $toDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->selectRaw("
            SUM(IF(status IN ('present', 'half_day', 'late') OR check_in IS NOT NULL, 1, 0)) as present_count,
            SUM(IF(status = 'wfh', 1, 0)) as wfh_count,
            SUM(IF(status = 'half_day', 1, 0)) as halfDay_count,
            SUM(IF(status = 'leave', 1, 0)) as leave_count,
            SUM(IF(status = 'late' OR (check_in IS NOT NULL AND TIME(check_in) > '09:30:00'), 1, 0)) as late_count,
            SUM(IF(status = 'early_leave', 1, 0)) as early_count,
            SUM(IF(status = 'absent', 1, 0)) as absent_count
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

        $analyticsEmployeeId = $isAdmin ? null : $employeeId;
        $todayAnalytics = $this->getAttendanceAnalytics($today, $today, $analyticsEmployeeId);
        $todayDashboardAnalytics = $this->getAttendanceAnalytics($today, $today);

        // Attendance Metrics
        $isCurrentMonth = ($selectedMonth == Carbon::now()->format('Y-m'));

        if ($isAdmin) {
            if ($isCurrentMonth) {
                $todayPresent = (int) ($todayAnalytics->present_count ?? 0);
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
            $todayPresent = (int) ($todayAnalytics->present_count ?? 0);
            $todayLeave = Attendance::where('employee_id', $employeeId)->where('attendance_date', $today)->whereIn('status', ['absent', 'leave'])->count();
            $attendanceRate = ($totalEmployees > 0) ? number_format(($todayPresent / $totalEmployees) * 100, 2) : 0;

            $myPayroll = Payroll::where('employee_id', $employeeId)->where('month', $selectedMonth)->first();
            $totalPaidAmount = ($myPayroll && $myPayroll->status == 'paid') ? $myPayroll->net_salary : 0;
            $totalPendingAmount = ($myPayroll && $myPayroll->status == 'pending') ? $myPayroll->net_salary : 0;
            $totalRejectedAmount = ($myPayroll && $myPayroll->status == 'rejected') ? $myPayroll->net_salary : 0;
            $totalNetSalary = $myPayroll ? $myPayroll->net_salary : 0;

            $totalEmpPaid = ($myPayroll && $myPayroll->status == 'paid') ? 1 : 0;
            $totalEmpPending = ($myPayroll && $myPayroll->status == 'pending') ? 1 : 0;
        }

        // Attendance analytics card for all roles.
        $present = (int) ($todayDashboardAnalytics->present_count ?? 0);
        $wfh = (int) ($todayDashboardAnalytics->wfh_count ?? 0);
        $late = (int) ($todayDashboardAnalytics->late_count ?? 0);
        $half_day = (int) ($todayDashboardAnalytics->halfDay_count ?? 0);
        $leave = (int) ($todayDashboardAnalytics->leave_count ?? 0);
        $early = (int) ($todayDashboardAnalytics->early_count ?? 0);
        $absent = (int) ($todayDashboardAnalytics->absent_count ?? 0);
        $attendanceRate = $totalEmployees > 0 ? round(($present / $totalEmployees) * 100, 2) : 0;

        // NEW DATE FILTER ANALYTICS (ADD HERE ONLY)

        if ($request->has('from') || $request->has('filter')) {
            if ($request->filter == 'today') {
                $from = Carbon::today()->toDateString();
                $to   = Carbon::today()->toDateString();
            } elseif ($request->filter == 'yesterday') {
                $from = Carbon::yesterday()->toDateString();
                $to   = Carbon::yesterday()->toDateString();
            } elseif ($request->filter == 'week') {
                $from = Carbon::now()->subDays(6)->toDateString();
                $to   = Carbon::today()->toDateString();
            } elseif ($request->filter == 'month') {
                $from = Carbon::now()->startOfMonth()->toDateString();
                $to   = Carbon::today()->toDateString();
            } else {
                $from = $request->from ?? Carbon::today()->toDateString();
                $to   = $request->to ?? Carbon::today()->toDateString();
            }

            $analytics = $this->getAttendanceAnalytics($from, $to);

            $rangePresent = (int) ($analytics->present_count ?? 0);
            $rangeWFH     = (int) ($analytics->wfh_count ?? 0);
            $rangeLeave   = (int) ($analytics->leave_count ?? 0);
            $rangeLate    = (int) ($analytics->late_count ?? 0);
            $rangeEarly   = (int) ($analytics->early_count ?? 0);
            $rangeAbsent  = (int) ($analytics->absent_count ?? 0);
            $rangeHalfday = (int) ($analytics->halfDay_count ?? 0);

            $rangeCheckedIn = $rangePresent;
            $days = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
            $denominator = $totalEmployees * $days;

            $rangeAttendanceRate = ($denominator > 0)
                ? number_format(($rangeCheckedIn / $denominator) * 100, 2)
                : 0;
        } else {
            $rangePresent = 0;
            $rangeWFH     = 0;
            $rangeLeave   = 0;
            $rangeLate    = 0;
            $rangeEarly   = 0;
            $rangeAbsent  = 0;
            $rangeHalfday = 0;

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

        // Selected month for leave report (default = current month)
        $leaveReport = $this->getLeaveReport($request);
        $employees = Employee::all();

        // Employee Leave on Today
        $todayLeaveEmployees = Attendance::with('employee') 
            ->whereDate('attendance_date', $today) 
            ->whereIn('status', ['leave'])
            // ->when(!$isAdmin, fn($q) => $q->where('employee_id', $employeeId)) 
            ->get();

        // Late arrival on today
        $todayLateEmployees = Attendance::with('employee')
        ->whereDate('attendance_date', $today)
        ->whereTime('check_in', '>', '09:30:00')
        // ->when(!$isAdmin, fn($q) => $q->where('employee_id', $employeeId))
        ->get();

        $officeTime = Carbon::createFromTime(9, 30, 0);

        $todayLateEmployees = $todayLateEmployees->map(function ($item) use ($officeTime) {
            $checkIn = Carbon::parse($item->check_in);

            $lateMinutes = $officeTime->diffInMinutes($checkIn);

            $hours = floor($lateMinutes / 60);
            $minutes = $lateMinutes % 60;

            if ($hours > 0) {
                $item->late_duration = $hours . ' hr ' . $minutes . ' min';
            } else {
                $item->late_duration = $minutes . ' min';
            }

            return $item;
        });

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
            'rangeAbsent',
            'rangeHalfday',
            'present',
            'wfh',
            'leave',
            'late',
            'early',
            'absent',
            'half_day',
            'leaveReport',
            'employees',
            'todayLeaveEmployees',
            'todayLateEmployees',
        ));
    }

    private function getLeaveReport(Request $request)
    {
        // $query = Attendance::join('employees', 'attendances.employee_id', '=', 'employees.id')
        //     ->join('leave_applications', function ($join) {
        //         $join->on('attendances.employee_id', '=', 'leave_applications.employee_id')
        //             ->whereColumn('attendances.attendance_date', '>=', 'leave_applications.start_date')
        //             ->whereColumn('attendances.attendance_date', '<=', 'leave_applications.end_date');
        //     })
        //     ->where('leave_applications.status', 'approved');

        $query = LeaveApplication::join('employees', 'leave_applications.employee_id', '=', 'employees.id')
        ->where('leave_applications.status', 'approved');

        if ($request->employee_id) {
            $query->where('employees.id', $request->employee_id);
        }

        $from = null;
        $to   = Carbon::today();

        if ($request->leave_filter) {
            switch ($request->leave_filter) {
                case 'week':
                    $from = Carbon::now()->subWeek();
                    break;
                case 'last_month':
                    $from = Carbon::now()->subMonth()->startOfMonth();
                    $to   = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case '3month':
                    $from = Carbon::now()->subMonths(3);
                    break;
                case '6month':
                    $from = Carbon::now()->subMonths(6);
                    break;
                case 'year':
                    $from = Carbon::now()->subYear();
                    break;
            }
        }

        // Custom range
        if ($request->leave_from && $request->leave_to) {
            $from = Carbon::parse($request->leave_from);
            $to   = Carbon::parse($request->leave_to);
        }

        // Default = last month
        if (!$request->leave_filter && !$request->leave_from) {
            $from = Carbon::now()->startOfMonth();
            $to   = Carbon::now();
        }

        if ($from && $to) {
            // $query->whereBetween('attendances.attendance_date', [$from, $to]);
            $query->where(function($q) use ($from, $to) {
                $q->whereBetween('leave_applications.start_date', [$from, $to])
                ->orWhereBetween('leave_applications.end_date', [$from, $to]);
            });
        }

        // dd($request->leave_from, $request->leave_to);

        return $query->selectRaw("
                employees.id,
                employees.name,
                employees.designation,
                COUNT(*) as leave_count
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
