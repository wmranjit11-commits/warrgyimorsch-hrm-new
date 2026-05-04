<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\DailyTask;
use App\Models\TaskFollowUp;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['tasks.employee'])->latest()->get();
        $employees = \App\Models\Employee::all();
        $departments = \App\Models\Department::all();
        return view('projects.index', compact('projects', 'employees', 'departments'));
    }

    public function create()
    {
        $employees = \App\Models\Employee::all();
        $departments = \App\Models\Department::all();
        return view('projects.create', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'department' => 'required|string',
            'description' => 'required',
            'type' => 'required',
            'manage' => 'required',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'department' => $request->department,
            'description' => $request->description,
            'technology' => $request->technology,
            'leaders' => $request->leaders,
            'members' => $request->members,
            'type' => $request->type,
            'manage' => $request->manage,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show($id)
    {
        $project = Project::with(['tasks.employee', 'tasks.followUps'])->findOrFail($id);
        $employees = \App\Models\Employee::all();
        $departments = \App\Models\Department::all();

        // Fetch activities (TaskFollowUps) for this project
        $activities = TaskFollowUp::whereHas('dailyTask', function ($q) use ($id) {
            $q->where('project_id', $id);
        })->with('dailyTask.employee')->latest()->get();

        return view('projects.show', compact('project', 'employees', 'departments', 'activities'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $employees = \App\Models\Employee::all();
        $departments = \App\Models\Department::all();

        return view('projects.edit', compact('project', 'employees', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'department' => 'required|string',
            'description' => 'required',
            'type' => 'required',
        ]);

        $project = Project::findOrFail($id);

        $project->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'department' => $request->department,
            'description' => $request->description,
            'technology' => $request->technology,
            'leaders' => $request->leaders,
            'members' => $request->members,
            'type' => $request->type,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function updateField(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $fields = ['status', 'members', 'leaders'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $project->$field = $request->$field;
            }
        }

        $project->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            Project::whereIn('id', $ids)->delete();
        }
        return response()->json(['success' => true]);
    }

    public function tasksSummary($id)
    {
        $project = Project::with(['tasks.employee', 'tasks.followUps' => function($q) {
            $q->latest();
        }])->findOrFail($id);

        $tasks = $project->tasks->map(function($task) {
            $latestFU = $task->followUps->first();
            return [
                'id' => $task->id,
                'task_title' => $task->task_title,
                'status' => $task->status,
                'employee' => $task->employee,
                'follow_ups' => $task->followUps,
                'total_time_calc' => $task->total_time,
                'latest_activity_date' => $latestFU ? $latestFU->created_at->format('d M, h:i A') : 'No updates'
            ];
        });

        return response()->json([
            'project_name' => $project->name,
            'tasks' => $tasks
        ]);
    }
}
