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
            // For past months, show average based on actual days in that month
            $daysInMonth = $selectedDate->daysInMonth;
            $monthPresent = Attendance::whereMonth('attendance_date', $selectedDate->month)
                ->whereYear('attendance_date', $selectedDate->year)
                ->whereIn('status', ['present', 'half_day', 'late', 'leave', 'absent'])
                ->count();

            $todayPresent = $daysInMonth > 0 ? round($monthPresent / $daysInMonth) : 0;
            $todayLeave = 0; // Average leave is complex, default to 0 for past summary
            $attendanceRate = ($totalEmployees > 0 && $daysInMonth > 0) ? round(($monthPresent / ($totalEmployees * $daysInMonth)) * 100, 1) : 0;
        }

        // Payroll Metrics (Selected Month)
        $totalPaidAmount = Payroll::where('month', $selectedMonth)->where('status', 'paid')->sum('net_salary');
        $totalPendingAmount = Payroll::where('month', $selectedMonth)->where('status', 'pending')->sum('net_salary');
        $totalRejectedAmount = Payroll::where('month', $selectedMonth)->where('status', 'rejected')->sum('net_salary');
        $totalNetSalary = Payroll::where('month', $selectedMonth)->sum('net_salary');

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
        // dd($recentPayrolls);
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
