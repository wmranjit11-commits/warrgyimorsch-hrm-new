<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use App\Models\Project;
use App\Models\Employee;
use App\Models\TaskFollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyTask::with(['project', 'employee', 'creator', 'followUps']);

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
        $projects = Project::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();

        return view('projects.tasks.index', compact('tasks', 'projects', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'description' => 'nullable|string',
        ]);

        $validated['assigned_by'] = Auth::id();

        DailyTask::create($validated);

        return response()->json(['success' => 'Task created successfully!']);
    }

    public function update(Request $request, DailyTask $dailyTask)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|string',
            'status' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'description' => 'nullable|string',
        ]);

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp|max:10240',
        ]);

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

        $dailyTask->update(['status' => $validated['status']]);

        return response()->json(['success' => 'Task status updated successfully!']);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('editor_images', $filename, 'public');
            return response()->json(['url' => asset('storage/' . $path)]);
        }
        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
