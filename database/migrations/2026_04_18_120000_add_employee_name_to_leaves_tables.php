<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->string('employee_name')->nullable()->after('employee_id');
        });

        Schema::table('leave_allotments', function (Blueprint $table) {
            $table->string('employee_name')->nullable()->after('employee_id');
        });

        // Optional: Update existing records
        $employees = \App\Models\Employee::all()->keyBy('id');
        
        \App\Models\LeaveApplication::all()->each(function($leave) use ($employees) {
            if (isset($employees[$leave->employee_id])) {
                $leave->update(['employee_name' => $employees[$leave->employee_id]->name]);
            }
        });

        \App\Models\LeaveAllotment::all()->each(function($allotment) use ($employees) {
            if (isset($employees[$allotment->employee_id])) {
                $allotment->update(['employee_name' => $employees[$allotment->employee_id]->name]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropColumn('employee_name');
        });

        Schema::table('leave_allotments', function (Blueprint $table) {
            $table->dropColumn('employee_name');
        });
    }
};
