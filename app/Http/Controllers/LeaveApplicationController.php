<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\Attendance;
use App\Exports\LeaveApplicationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        // echo auth()->user()->role;exit;
        $query = LeaveApplication::with('employee');

        // Search Filters
        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('leave_category', 'LIKE', '%' . $request->category . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('start_date', '<=', $request->to_date);
        }

        // $role = strtoupper(auth()->user()->role);
        $roleSlug = auth()->user()->role; // e.g. "manager"

        $roleId = DB::table('roles_master')
            ->where('slug', $roleSlug)
            ->value('id');

        $isAdmin = in_array($roleId, [1, 2, 3, 4]);
        if (!$isAdmin) {
            $query->where('employee_id', auth()->user()->employee_id);
            $employees = Employee::where('id', auth()->user()->employee_id)->get();
        } else {
            $employees = Employee::all();
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('leave.history', compact('leaves', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required',
            'leave_category' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'reason' => 'required',
        ]);

        $data = $request->only(['employee_id', 'leave_type', 'leave_category', 'start_date', 'end_date', 'reason', 'message', 'total_days', 'start_time', 'end_time']);
        $data['status'] = 'pending';

        if (str_contains(strtolower($request->leave_category), 'gatepass')) {
            $data['end_date'] = $request->start_date;
            $data['total_days'] = 0;
            if ($request->filled('start_time')) {
                $startTime = Carbon::createFromFormat('H:i', $request->start_time);
                $data['end_time'] = $startTime->copy()->addHour()->format('H:i');
            }
        } else {
            $data['end_date'] = $request->end_date ?? $request->start_date;
        }

        LeaveApplication::create($data);

        return response()->json(['success' => true, 'message' => 'Leave application submitted successfully']);
    }

    public function export(Request $request)
    {
        $query = LeaveApplication::with('employee');

        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('leave_category', 'LIKE', '%' . $request->category . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('start_date', '<=', $request->to_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->get();
        $filename = "leave_applications_" . date('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new LeaveApplicationsExport($leaves), $filename);
    }

    public function updateAction(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|exists:leave_applications,id',
            'status' => 'required|in:pending,approved,rejected,on_hold,unauthorised,unpaid',
        ]);

        $leave = LeaveApplication::findOrFail($request->leave_id);
        $oldStatus = $leave->status;
        $newStatus = $request->status;

        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = $leave->end_date ? Carbon::parse($leave->end_date) : $startDate->copy();

            if ($startDate->equalTo($endDate)) {
                $endDate->addDay();
            }

            for ($date = $startDate->copy(); $date->lt($endDate); $date->addDay()) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $leave->employee_id,
                        'attendance_date' => $date->format('Y-m-d')
                    ],
                    [
                        'status' => str_contains(strtolower($leave->leave_category), 'half') ? 'half_day' : 'leave',
                        'total_hours' => str_contains(strtolower($leave->leave_category), 'half') ? 4 : 0,
                        'check_in' => null,
                        'check_out' => null
                    ]
                );
            }
        } elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = $leave->end_date ? Carbon::parse($leave->end_date) : $startDate->copy();

            if ($startDate->equalTo($endDate)) {
                $endDate->addDay();
            }

            Attendance::where('employee_id', $leave->employee_id)
                ->where('attendance_date', '>=', $startDate->format('Y-m-d'))
                ->where('attendance_date', '<', $endDate->format('Y-m-d'))
                ->whereIn('status', ['leave', 'half_day'])
                ->delete();
        }

        $leave->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->delete();
        return response()->json(['success' => true, 'message' => 'Leave application deleted']);
    }

    public function getDetails($id)
    {
        $leave = LeaveApplication::with('employee')->findOrFail($id);
        return response()->json($leave);
    }

    public function getEmployeeLeaves($employeeId)
    {
        $leaves = LeaveApplication::where('employee_id', $employeeId)
            ->orderBy('start_date', 'desc')
            ->get();
        return response()->json($leaves);
    }
}
