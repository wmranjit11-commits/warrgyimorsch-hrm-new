<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveApplication::with('employee');

        // Search Filters
        if ($request->filled('category')) {
            $query->where('leave_category', $request->category);
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

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);
        $employees = Employee::all();

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

        $data = $request->only(['employee_id', 'leave_type', 'leave_category', 'start_date', 'end_date', 'reason', 'message', 'total_days', 'start_time']);
        $data['status'] = 'pending';

        if ($request->leave_category === 'gatepass') {
            $data['end_date'] = $request->start_date;
            $data['total_days'] = 0; // Or 0.125 or whatever, user said 1 hour count
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

        if ($request->filled('category')) {
            $query->where('leave_category', $request->category);
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

        $filename = "leave_applications_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function() use ($leaves) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Sr.No.', 'Employee Name', 'Status', 'Leave Type', 'Category', 'Start Date', 'End Date', 'Start Time', 'End Time', 'Total Days', 'Reason', 'Message']);

            foreach ($leaves as $key => $leave) {
                fputcsv($file, [
                    $key + 1,
                    $leave->employee->name,
                    ucfirst($leave->status),
                    $leave->leave_type,
                    strtoupper($leave->leave_category),
                    $leave->start_date->format('d-m-Y'),
                    $leave->end_date ? $leave->end_date->format('d-m-Y') : '-',
                    $leave->start_time ? Carbon::parse($leave->start_time)->format('h:i A') : '-',
                    $leave->end_time ? Carbon::parse($leave->end_time)->format('h:i A') : '-',
                    $leave->total_days,
                    $leave->reason,
                    $leave->message,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateAction(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|exists:leave_applications,id',
            'status' => 'required|in:pending,approved,rejected,on_hold',
        ]);

        $leave = LeaveApplication::findOrFail($request->leave_id);
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
