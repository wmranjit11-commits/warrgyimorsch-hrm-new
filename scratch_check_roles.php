<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$roles = DB::table('roles_master')->get();
foreach($roles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}, Slug: {$role->slug}\n";
}
