<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
                'employee_code' => 'required|string|max:50|unique:employees,employee_code',
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'mobile_number' => 'required|string|max:20',
                'department' => 'required|string',
                'designation' => 'required|string',
                'role' => 'required|string',
                'bank_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',
                'ifsc_code' => 'required|string|max:20',
                'basic_salary' => 'required|numeric|min:0',
            ]);

            return DB::transaction(function () use ($request) {
                $data = $request->all();

                // Password hashing
                $rawPassword = $request->filled('password') ? $request->password : '12345678';
                $data['password'] = Hash::make($rawPassword);

                // Defaults
                $data['gender'] = $request->gender ?? 'male';
                $data['time_in'] = $request->time_in ?? '09:00';
                $data['time_out'] = $request->time_out ?? '19:00';
                $data['leave'] = $request->leave ?? 0;

                // Toggles
                $data['pf'] = $request->has('pf');
                $data['esi'] = $request->has('esi');
                $data['insurance'] = $request->has('insurance');

                // Salary Defaults
                $data['hra'] = $request->hra ?? 0;
                $data['conveyance_allowance'] = $request->conveyance_allowance ?? 0;
                $data['medical_allowance'] = $request->medical_allowance ?? 0;
                $data['other_allowance'] = $request->other_allowance ?? 0;

                // Handle Photo upload
                if ($request->hasFile('photo')) {
                    $data['photo'] = $request->file('photo')->store('employees', 'public');
                }

                // Create employee
                $employee = Employee::create($data);

                // Create User for login if email is present
                if ($request->filled('email')) {
                    User::create([
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'password' => Hash::make($rawPassword),
                        'role' => $employee->role,
                        'employee_id' => $employee->id,
                    ]);
                }

                return redirect()->route('employees.index')
                    ->with('success', 'Employee added and User account created successfully! ✓');
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
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
     * Get employee attendance for modal tab
     */
    public function getAttendance(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $query = \App\Models\Attendance::where('employee_id', $id);

        if ($request->filled('month')) {
            // month is in YYYY-MM format
            $parts = explode('-', $request->month);
            $year = $parts[0];
            $month = $parts[1];

            $query->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month);
        } else {
            // Default to current month if no filter
            $query->whereYear('attendance_date', date('Y'))
                ->whereMonth('attendance_date', date('m'));
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->get();

        return response()->json($attendance);
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
        try {
            $employee = Employee::findOrFail($id);
            // Find existing user by employee_id or email
            $user = User::where('employee_id', $employee->id)
                ->orWhere('email', $employee->email)
                ->first();
            $userId = $user ? $user->id : 'NULL';

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:20',
                'department' => 'required|string',
                'designation' => 'required|string',
                'role' => 'required|string',
                'email' => 'nullable|email|unique:users,email,' . $userId,
            ]);

            return DB::transaction(function () use ($request, $employee, $user) {
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
                    'employee_code' => $request->employee_code,
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
                    $updateData['password'] = Hash::make($request->password);
                }

                $employee->update($updateData);

                // Handle photo upload
                if ($request->hasFile('photo')) {
                    $request->validate([
                        'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                    ]);

                    $file = $request->file('photo');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('employees', $filename, 'public');

                    if ($employee->photo && \Storage::disk('public')->exists($employee->photo)) {
                        \Storage::disk('public')->delete($employee->photo);
                    }

                    $employee->update(['photo' => $path]);
                }

                // Sync with User table
                if ($request->filled('email')) {
                    $user = User::where('employee_id', $employee->id)->orWhere('email', $employee->email)->first();

                    $userData = [
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'role' => $employee->role,
                        'employee_id' => $employee->id,
                    ];

                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->password);
                    }

                    if ($user) {
                        $user->update($userData);
                    } else {
                        User::create($userData + ['password' => Hash::make($request->password ?? '12345678')]);
                    }
                }

                return redirect()->route('employees.index')
                    ->with('success', 'Employee and User account updated successfully! ✓');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully! ✓');
    }

    public function export()
    {
        $employees = Employee::orderBy('name', 'asc')->get();
        $filename = "employees_" . date('Y-m-d_H-i-s') . ".xlsx";
        return Excel::download(new EmployeesExport($employees), $filename);
    }
}
