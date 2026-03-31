<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveAllotment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        return redirect()->route('leave.allotment');
    }

    public function allotment(Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('m'));
        $year = Carbon::now()->format('Y');
        
        $month = $selectedMonth;

        $employees = Employee::orderBy('name', 'asc')->get();
        $allotments = LeaveAllotment::where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $history = LeaveAllotment::with('employee')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leave.allotment', compact('employees', 'allotments', 'selectedMonth', 'history'));
    }

    public function storeAllotment(Request $request)
    {
        $month = $request->input('month');
        $year = Carbon::now()->format('Y');

        $allotments = $request->input('allotments', []);
        $employeeIds = array_keys($allotments);

        // Remove allotments for employees who were removed from the list in UI
        LeaveAllotment::where('month', $month)
            ->where('year', $year)
            ->whereNotIn('employee_id', $employeeIds)
            ->delete();

        foreach ($allotments as $employeeId => $count) {
            LeaveAllotment::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'leave_count' => $count ?? 0,
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Leaves allotted successfully']);
    }

    public function balanceList()
    {
        $balances = $this->calculateBalances();
        return view('leave.balance', compact('balances'));
    }

    public function apiBalanceList()
    {
        $balances = $this->calculateBalances();
        return response()->json($balances);
    }

    private function calculateBalances()
    {
        $employees = Employee::all();
        $balances = [];

        foreach ($employees as $employee) {
            $totalAllotted = LeaveAllotment::where('employee_id', $employee->id)->sum('leave_count');
            
            $fullDays = Attendance::where('employee_id', $employee->id)
                ->where('status', 'leave')
                ->count();
            
            $halfDays = Attendance::where('employee_id', $employee->id)
                ->where('status', 'half_day')
                ->count();
                
            $totalTaken = $fullDays + ($halfDays * 0.5);

            $balances[] = (object)[
                'id' => $employee->id,
                'name' => $employee->name,
                'total_allotted' => $totalAllotted,
                'total_taken' => $totalTaken,
                'balance' => $totalAllotted - $totalTaken
            ];
        }
        return $balances;
    }
}
