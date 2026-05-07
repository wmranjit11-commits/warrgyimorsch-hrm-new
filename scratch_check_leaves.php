<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LeaveApplication;

$nullLeaves = LeaveApplication::whereNull('employee_id')->get();
echo "Total Null Employee ID: " . $nullLeaves->count() . "\n";
foreach($nullLeaves as $leave) {
    echo "ID: {$leave->id}, Category: {$leave->leave_category}, Created: {$leave->created_at}\n";
}

$wfhLeaves = LeaveApplication::where('leave_category', 'LIKE', '%WFH%')->get();
echo "\nWFH Leaves:\n";
foreach($wfhLeaves as $leave) {
    echo "ID: {$leave->id}, EmpID: " . ($leave->employee_id ?? 'NULL') . ", Category: {$leave->leave_category}\n";
}
