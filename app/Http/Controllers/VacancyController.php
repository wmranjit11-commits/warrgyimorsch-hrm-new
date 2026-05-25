<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\JobVacancy;
use App\Models\JobRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VacancyController extends Controller
{
    public function show() {
        $departments = Department::select('id','name')->get();
        $designations = Designation::select('id', 'name')->get();
        $employees = Employee::whereIn('role', [ 'super_admin', 'manager', 'hr-executive', 'team_leader'])
                ->select('id','name')
                ->get();

        $applications = JobVacancy::with(['department', 'interviewer'])
            ->latest()
            ->get();

        $pendingCount = JobVacancy::where('status', 'pending')->count();
        $awaitedCount = JobVacancy::where('status', 'awaited')->count();
        $rejectedCount = JobVacancy::where('status', 'rejected')->count();
        $selectedCount = JobVacancy::where('status', 'selected')->count();

        return view('vacancy.index', compact('departments', 'designations', 'employees', 'applications', 'pendingCount', 'awaitedCount', 'rejectedCount', 'selectedCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:2048'
        ]);

        $data = $request->all();

        if($request->hasFile('resume')){
            $data['resume'] =
            $request->file('resume')
            ->store('resumes','public');
        }

        JobVacancy::create($data);

        return back()
            ->with('success',
            'Candidate saved successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $app = JobVacancy::findOrFail($id);
        $app->status = $request->status;
        $app->save();

        return back()->with('success', 'Status updated successfully');
    }


    public function showRequirements() {
        $roles = DB::table('designations')->get();

        $requirements = DB::table('job_requirements')
            ->leftJoin(
                'designations',
                'job_requirements.role_id',
                '=',
                'designations.id'
            )
            ->select(
                'job_requirements.*',
                'designations.name as role_name'
            )
            ->latest()
            ->get();

        return view('vacancy.job_requirement', compact('roles', 'requirements'));
    }

    public function storeRequirement(Request $request)
    {
        $request->validate([
            'role_id'=>'required',
            'priority'=>'required',
            'date'=>'required',
            'candidate_type'=>'required',
            'skills'=>'required'
        ]);
        JobRequirement::create([
            'role_id'=>$request->role_id,
            'priority'=>$request->priority,
            'date'=>$request->date,
            'candidate_type'=>$request->candidate_type,
            'minimum_experience'=>
                $request->candidate_type=="Experience"
                ? $request->minimum_experience
                : null,
            'skills' => array_map('trim', explode(',', $request->skills))
        ]);
        return back()->with('success', 'Saved Successfully');
    }
}
