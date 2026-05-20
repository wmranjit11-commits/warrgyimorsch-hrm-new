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
        Schema::create('daily_tasks', function (Blueprint $row) {
            $row->id();
            $row->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $row->string('task_title');
            $row->date('start_date');
            $row->date('end_date');
            $row->string('priority'); // Hard, Medium, Low
            $row->string('status')->default('Pending');
            $row->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $row->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $row->text('description')->nullable();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_tasks');
    }
};
