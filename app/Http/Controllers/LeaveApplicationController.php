<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\Attendance;
use App\Exports\LeaveApplicationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveApplicationMail;
use App\Mail\LeaveStatusUpdatedMail;

class LeaveApplicationController extends Controller
{
    private function getAttendanceStatusFromLeave(string $leaveCategory, ?string $leaveType = null): string
    {
        $normalizedCategory = strtolower($leaveCategory);
        $normalizedType = strtolower($leaveType ?? '');

        if (str_contains($normalizedType, 'half')) {
            return 'half_day';
        }

        if (str_contains($normalizedCategory, 'wfh')) {
            return 'wfh';
        }

        if (str_contains($normalizedCategory, 'gatepass')) {
            return 'early_leave';
        }

        return 'leave';
    }

    private function getAttendanceHoursFromLeave(string $leaveCategory, ?string $leaveType = null): int
    {
        $normalizedCategory = strtolower($leaveCategory);
        $normalizedType = strtolower($leaveType ?? '');

        if (str_contains($normalizedType, 'half')) {
            return 4;
        }

        if (str_contains($normalizedCategory, 'wfh')) {
            return 8;
        }

        return 0;
    }

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

        $roleName = auth()->user()->role ?? '';
        $roleSlug = \Illuminate\Support\Str::slug($roleName);

        $roleId = DB::table('roles_master')
            ->where('slug', $roleSlug)
            ->orWhere('name', $roleName)
            ->value('id');

        $isAdmin = in_array($roleId, [1, 2, 3, 4]);

        // Secondary safety check: If DB lookup fails or doesn't match ID list, 
        // check if role name contains admin or hr keywords
        if (!$isAdmin && $roleName) {
            $lowerRole = strtolower($roleName);
            if (str_contains($lowerRole, 'admin') || str_contains($lowerRole, 'hr')) {
                $isAdmin = true;
            }
        }

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
            'leave_type' => 'nullable|string',
            'leave_category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable',
            'reason' => 'required',
        ]);

        $data = $request->only(['employee_id', 'leave_type', 'leave_category', 'start_date', 'end_date', 'reason', 'message', 'total_days', 'start_time', 'end_time']);
        $data['status'] = 'pending';

        // Ensure employee_id is set (fallback for non-admin users)
        if (empty($data['employee_id'])) {
            $data['employee_id'] = auth()->user()->employee_id;
        }

        if ($request->leave_category === 'Gatepass Leave') {
            $data['leave_type'] = 'Early Leave';
            $data['total_days'] = 0.125; // 1 hour
        } 

        if ($request->leave_category === 'Gatepass Leave') {
            $data['end_date'] = $request->start_date;
            if ($request->filled('start_time')) {
                try {
                    $startTime = \Carbon\Carbon::parse($request->start_time);
                    $data['end_time'] = $startTime->copy()->addHour()->format('H:i');
                } catch (\Exception $e) {
                    // Fallback or ignore if time format is invalid
                }
            }
        } else {
            $data['end_date'] = $request->end_date ?? $request->start_date;
        }

        // LeaveApplication::create($data);
        $leave = LeaveApplication::create($data);
        $employee = Employee::findOrFail($data['employee_id']);

        Mail::to('mdkaif14104@gmail.com')
            ->send((new LeaveApplicationMail($leave, $employee))->replyTo($employee->email));

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
                        'status' => $this->getAttendanceStatusFromLeave(
                            $leave->leave_category,
                            $leave->leave_type
                        ),
                        'total_hours' => $this->getAttendanceHoursFromLeave(
                            $leave->leave_category,
                            $leave->leave_type
                        ),
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
                ->whereIn('status', ['leave', 'half_day', 'wfh', 'early_leave'])
                ->delete();
        }

        $leave->update(['status' => $request->status]);

        $employee = Employee::find($leave->employee_id);

        if ($employee && $employee->email) {
            Mail::to($employee->email)
                ->send(new LeaveStatusUpdatedMail($leave, $employee));
        }

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
