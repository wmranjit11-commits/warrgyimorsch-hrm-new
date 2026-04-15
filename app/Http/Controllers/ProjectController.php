<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['tasks.employee'])->withCount('tasks')->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|string',
            'department' => 'required|string',
            'description' => 'nullable|string',
            'technology' => 'required|string',
        ]);

        Project::create($validated);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|string',
            'department' => 'required|string',
            'description' => 'nullable|string',
            'technology' => 'required|string',
        ]);

        $project->update($validated);

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,In Process,Completed,On Hold,Review,Rework',
        ]);

        $project->update(['status' => $validated['status']]);

        return response()->json(['success' => 'Project status updated successfully.']);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully.');
    }
}
