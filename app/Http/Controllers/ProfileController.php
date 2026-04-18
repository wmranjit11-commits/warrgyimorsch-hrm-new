<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\LeaveAllotment;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employee = Employee::find($user->employee_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        if ($employee) {
            $updateData = [
                'name' => $request->name,
                'mobile_number' => $request->mobile_number
            ];

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('employees', $filename, 'public');

                if ($employee->photo && \Storage::disk('public')->exists($employee->photo)) {
                    \Storage::disk('public')->delete($employee->photo);
                }
                $updateData['photo'] = $path;
            }

            $employee->update($updateData);
        }

        return Redirect::route('profile.show')->with('success', 'Profile updated successfully! ✓');
    }

    public function show(Request $request): View
    {
        $user = $request->user();
        $employee = Employee::find($user->employee_id);

        return view('profile.show', [
            'user' => $user,
            'employee' => $employee
        ]);
    }

    public function leaveBalance(Request $request): View
    {
        $user = $request->user();
        $employee = Employee::find($user->employee_id);
        
        $balances = [];
        if ($employee) {
            // Allotment: Treating the leave_count as a monthly quota
            $total_allotted = LeaveAllotment::where('employee_id', $employee->id)->sum('leave_count');

            // Usage: Approved leaves that START in this month
            $total_used = LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereYear('start_date', date('Y'))
                ->whereMonth('start_date', date('m'))
                ->sum('total_days');

            $balances[] = [
                'type' => date('F') . ' Leave Cycle (' . date('Y') . ')',
                'allotted' => $total_allotted,
                'used' => $total_used,
                'available' => $total_allotted - $total_used,
                'reference' => 'Monthly Quota'
            ];
        }

        return view('profile.leave-balance', [
            'user' => $user,
            'employee' => $employee,
            'balances' => $balances,
            'total_allotted' => $balances[0]['allotted'] ?? 0,
            'total_used' => $balances[0]['used'] ?? 0,
            'balance' => $balances[0]['available'] ?? 0
        ]);
    }

    public function leaveHistory(Request $request): View
    {
        $user = $request->user();
        $employee = Employee::find($user->employee_id);

        $leaves = collect([]);
        if ($employee) {
            $leaves = LeaveApplication::where('employee_id', $employee->id)->orderBy('created_at', 'desc')->get();
        }

        return view('profile.leave-history', [
            'user' => $user,
            'employee' => $employee,
            'leaves' => $leaves
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => $validated['password'],
        ]);

        $employee = Employee::find($user->employee_id);
        if ($employee) {
            $employee->update(['password' => $validated['password']]);
        }

        return Redirect::route('profile.show')->with('success', 'Password updated successfully! ✓');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
