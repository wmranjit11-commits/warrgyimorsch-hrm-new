<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::paginate(10);
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:20',
                'department' => 'required|string',
                'designation' => 'required|string',
                'role' => 'required|string',
                'basic_salary' => 'required|numeric|min:0',
                'leave' => 'nullable|numeric|min:0',
            ]);

            // Create employee with all fields
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'role' => $request->role,
                'department' => $request->department,
                'designation' => $request->designation,
                'date_of_joining' => $request->date_of_joining,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender ?? 'male',
                'employee_type' => $request->employee_type,
                'username' => $request->username,
                'password' => $request->password,
                'aadhaar_number' => $request->aadhaar_number,
                'pan_number' => $request->pan_number,
                'address' => $request->address,
                'time_in' => $request->time_in ?? '09:00',
                'time_out' => $request->time_out ?? '19:00',
                'leave' => $request->leave ?? 0,
                'pf' => $request->has('pf'),
                'pf_number' => $request->pf_number,
                'esi' => $request->has('esi'),
                'esi_number' => $request->esi_number,
                'insurance' => $request->has('insurance'),
                'insurance_provider' => $request->insurance_provider,
                'insurance_policy_number' => $request->insurance_policy_number,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'basic_salary' => $request->basic_salary ?? 0,
                'hra' => $request->hra ?? 0,
                'conveyance_allowance' => $request->conveyance_allowance ?? 0,
                'medical_allowance' => $request->medical_allowance ?? 0,
                'other_allowance' => $request->other_allowance ?? 0,
            ]);

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/employees'), $filename);
                $employee->update(['photo' => 'uploads/employees/' . $filename]);
            }

            return redirect()->route('employees.index')
                ->with('success', 'Employee added successfully! ✓');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    /**
     * Get employee as JSON for modal display
     */
    public function getJson($id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'department' => 'required|string',
            'designation' => 'required|string',
            'role' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'leave' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'role' => $request->role,
            'department' => $request->department,
            'designation' => $request->designation,
            'date_of_joining' => $request->date_of_joining,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender ?? 'male',
            'employee_type' => $request->employee_type,
            'username' => $request->username,
            'aadhaar_number' => $request->aadhaar_number,
            'pan_number' => $request->pan_number,
            'address' => $request->address,
            'time_in' => $request->time_in ?? '09:00',
            'time_out' => $request->time_out ?? '19:00',
            'leave' => $request->leave ?? 0,
            'pf' => $request->has('pf'),
            'pf_number' => $request->pf_number,
            'esi' => $request->has('esi'),
            'esi_number' => $request->esi_number,
            'insurance' => $request->has('insurance'),
            'insurance_provider' => $request->insurance_provider,
            'insurance_policy_number' => $request->insurance_policy_number,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'basic_salary' => $request->basic_salary ?? 0,
            'hra' => $request->hra ?? 0,
            'conveyance_allowance' => $request->conveyance_allowance ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'other_allowance' => $request->other_allowance ?? 0,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/employees'), $filename);
            $updateData['photo'] = 'uploads/employees/' . $filename;
        }

        $employee->update($updateData);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully! ✓');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Employee deleted successfully!']);
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully! ✓');
    }
}
