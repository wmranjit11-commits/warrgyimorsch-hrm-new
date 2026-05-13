<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all(['id', 'name', 'role', 'employee_id']);
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Role: '{$user->role}', EmployeeID: " . ($user->employee_id ?? 'NULL') . "\n";
}
