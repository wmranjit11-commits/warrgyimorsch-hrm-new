<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use App\Models\Project;
use App\Models\Employee;
use App\Models\TaskFollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DailyTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyTask::with(['project', 'employee', 'creator', 'followUps']);

        // Data Restriction Logic
        $roleSlug = auth()->user()->role; // e.g. "manager"

        $roleId = DB::table('roles_master')
            ->where('slug', $roleSlug)
            ->value('id');

        $isAdmin = in_array($roleId, [1, 2, 3, 4]);
        // $role = strtoupper(auth()->user()->role);
        if (!$isAdmin) {
            $query->where('employee_id', auth()->user()->employee_id);
            $employees = Employee::where('id', auth()->user()->employee_id)->get();
            $projects = Project::orderBy('name')->get();
        } else {
            $projects = Project::orderBy('name')->get();
            $employees = Employee::orderBy('name')->get();
        }

        // Filtering
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->from_date) {
            $query->where('start_date', '>=', $request->from_date);
        }
        if ($request->upto_date) {
            $query->where('end_date', '<=', $request->upto_date);
        }

        $tasks = $query->latest()->get();

        return view('projects.tasks.index', compact('tasks', 'projects', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'description' => 'nullable|string',
        ]);

        $validated['assigned_by'] = Auth::id();

        // Force employee_id for non-admins
        // $role = strtoupper(auth()->user()->role ?? 'USER');
        $roleSlug = auth()->user()->role; // e.g. "manager"

        $roleId = DB::table('roles_master')
            ->where('slug', $roleSlug)
            ->value('id');

        $isAdmin = in_array($roleId, [1, 2, 3, 4]);
        if (!$isAdmin) {
            $validated['employee_id'] = auth()->user()->employee_id;
        }

        DailyTask::create($validated);

        return response()->json(['success' => 'Task created successfully!']);
    }

    public function update(Request $request, DailyTask $dailyTask)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'description' => 'nullable|string',
        ]);

        // Force employee_id for non-admins
        // $role = strtoupper(auth()->user()->role ?? 'USER');
        $roleSlug = auth()->user()->role; // e.g. "manager"

        $roleId = DB::table('roles_master')
            ->where('slug', $roleSlug)
            ->value('id');

        $isAdmin = in_array($roleId, [1, 2, 3, 4]);
        if (!$isAdmin) {
            $validated['employee_id'] = auth()->user()->employee_id;
        }

        $dailyTask->update($validated);

        return response()->json(['success' => 'Task updated successfully!']);
    }

    public function destroy(DailyTask $dailyTask)
    {
        $dailyTask->delete();
        return back()->with('success', 'Task deleted successfully!');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if ($ids && is_array($ids)) {
            DailyTask::whereIn('id', $ids)->delete();
            return response()->json(['success' => 'Tasks deleted successfully!']);
        }
        return response()->json(['error' => 'No tasks selected.'], 400);
    }

    public function storeFollowUp(Request $request)
    {
        $validated = $request->validate([
            'daily_task_id' => 'required|exists:daily_tasks,id',
            'work_description' => 'required|string',
            'reference_name' => 'nullable|string',
            'time_taken' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,pdf,doc,docx,xls,xlsx,csv,txt,zip,rar|max:10240',
        ]);

        // $role = strtoupper(auth()->user()->role ?? 'USER');
        $roleSlug = auth()->user()->role; // e.g. "manager"

        $roleId = DB::table('roles_master')
            ->where('slug', $roleSlug)
            ->value('id');

        $isAdmin = in_array($roleId, [1, 2, 3, 4]);
        // if ($role !== 'ADMIN' && $role !== 'SUPER ADMIN') {
        if (!$isAdmin) {
            $validated['reference_name'] = auth()->user()->name;
        }

        // FORCE: Always record progress under the assigned employee's name
        $validated['reference_name'] = $task->employee->name ?? 'Unknown';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('task_followups', 'public');
            $validated['photo'] = $path;
        }

        TaskFollowUp::create($validated);

        return response()->json(['success' => 'Reply submitted successfully!']);
    }

    public function getFollowUps($taskId)
    {
        $followUps = TaskFollowUp::where('daily_task_id', $taskId)->latest()->get();
        return response()->json($followUps);
    }

    public function destroyFollowUp($id)
    {
        $followUp = TaskFollowUp::findOrFail($id);
        if ($followUp->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($followUp->photo);
        }
        $followUp->delete();
        return response()->json(['success' => 'Task history description deleted successfully!']);
    }
    public function updateStatus(Request $request, DailyTask $dailyTask)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,In Process,Completed,On Hold,Review,Rework',
        ]);

        $role = strtoupper(auth()->user()->role ?? 'USER');
        $project = $dailyTask->project;
        $isLead = false;
        if ($project && is_array($project->leaders)) {
            $isLead = in_array(auth()->user()->employee_id, $project->leaders);
        }

        if ($role !== 'ADMIN' && $role !== 'SUPER ADMIN' && !$isLead) {
            return response()->json(['error' => 'Only Admin or Project Lead can change status.'], 403);
        }

        $dailyTask->update(['status' => $validated['status']]);

        return response()->json(['success' => 'Task status updated successfully!']);
    }

    public function updatePriority(Request $request, DailyTask $dailyTask)
    {
        $validated = $request->validate([
            'priority' => 'required|string|in:Hard,Medium,Low,Normal',
        ]);

        $dailyTask->update(['priority' => $validated['priority']]);

        return response()->json(['success' => 'Task priority updated successfully!']);
    }
}
