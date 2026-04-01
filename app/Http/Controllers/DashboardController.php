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
    public function index(Request $request)
    {
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
        $totalEmployees = Employee::count();

        // Attendance Metrics (If current month selected, use today. If past, use month avg)
        $isCurrentMonth = ($selectedMonth == Carbon::now()->format('Y-m'));

        if ($isCurrentMonth) {
            $todayPresent = Attendance::where('attendance_date', $today)->whereIn('status', ['present', 'half_day', 'late'])->count();
            $todayLeave = Attendance::where('attendance_date', $today)->whereIn('status', ['absent', 'leave'])->count();
            $attendanceRate = $totalEmployees > 0 ? round(($todayPresent / $totalEmployees) * 100, 1) : 0;
        } else {
            // For past months, show average based on actual days that had attendance marked
            $uniqueDaysCount = Attendance::whereMonth('attendance_date', $selectedDate->month)
                ->whereYear('attendance_date', $selectedDate->year)
                ->distinct('attendance_date')
                ->count('attendance_date');

            $monthPresent = Attendance::whereMonth('attendance_date', $selectedDate->month)
                ->whereYear('attendance_date', $selectedDate->year)
                ->whereIn('status', ['present', 'half_day', 'late'])
                ->count();

            $todayPresent = $uniqueDaysCount > 0 ? round($monthPresent / $uniqueDaysCount) : 0;
            $todayLeave = 0; 
            $attendanceRate = ($totalEmployees > 0 && $uniqueDaysCount > 0) ? round(($monthPresent / ($totalEmployees * $uniqueDaysCount)) * 100, 1) : 0;
        }

        // Payroll Metrics (Selected Month)
        $totalPaidAmount = max(0, Payroll::where('month', $selectedMonth)->where('status', 'paid')->sum('net_salary'));
        $totalPendingAmount = max(0, Payroll::where('month', $selectedMonth)->where('status', 'pending')->sum('net_salary'));
        $totalRejectedAmount = max(0, Payroll::where('month', $selectedMonth)->where('status', 'rejected')->sum('net_salary'));
        $totalNetSalary = max(0, Payroll::where('month', $selectedMonth)->sum('net_salary'));

        $totalEmpPaid = Payroll::where('month', $selectedMonth)->where('status', 'paid')->count();
        $totalEmpPending = Payroll::where('month', $selectedMonth)->where('status', 'pending')->count();

        // Chart Data: 6 Months leading to selected month
        $chartMonths = [];
        $chartTotal = [];
        $chartPaid = [];
        $chartPending = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = (clone $selectedDate)->subMonths($i);
            $mLabel = $m->format('M/y');
            $mValue = $m->format('Y-m');

            $chartMonths[] = $mLabel;
            $chartTotal[] = Payroll::where('month', $mValue)->sum('net_salary');
            $chartPaid[] = Payroll::where('month', $mValue)->where('status', 'paid')->sum('net_salary');
            $chartPending[] = Payroll::where('month', $mValue)->where('status', 'pending')->sum('net_salary');
        }

        // Recent Activity (Filtered by Month)
        $recentPayrolls = Payroll::with('employee')
            ->where('month', $selectedMonth)
            ->latest()
            ->paginate(10);

        // Upcoming Holidays
        $upcomingHolidays = Holiday::where('date', '>=', $today)->orderBy('date')->limit(20)->get();

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
            'upcomingHolidays'
        ));
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
            $net = Payroll::where('month', $mValue)->sum('net_salary');

            $history[] = [
                'month' => $mLabel,
                'earnings' => $basic,
                'deductions' => 0,
                'net' => $net,
                'details' => [
                    'basic' => $basic,
                    'hra' => 0,
                    'medical' => 0,
                    'conveyance' => 0,
                    'other_allw' => 0,
                    'pf' => 0,
                    'esi' => 0,
                    'other_ded' => 0
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
