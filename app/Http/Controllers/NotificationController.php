<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $role = strtoupper(auth()->user()->role);
        $notifications = [];

        if ($role == 'ADMIN' || $role == 'SUPER ADMIN') {
            $notifications = LeaveApplication::with('employee')
                ->whereIn('status', ['pending', 'Pending'])
                ->latest()
                ->paginate(20);
        } else {
            $notifications = LeaveApplication::where('employee_id', auth()->user()->employee_id)
                ->whereIn('status', ['approved', 'rejected', 'Approved', 'Rejected'])
                ->where('updated_at', '>=', now()->subDays(30)) // Show more history on the full page
                ->latest()
                ->paginate(20);
        }

        return view('notifications.index', compact('notifications', 'role'));
    }
}
