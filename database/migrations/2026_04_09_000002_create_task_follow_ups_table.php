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
        Schema::create('task_follow_ups', function (Blueprint $row) {
            $row->id();
            $row->foreignId('daily_task_id')->constrained('daily_tasks')->onDelete('cascade');
            $row->text('work_description');
            $row->string('reference_name')->nullable();
            $row->string('time_taken')->nullable();
            $row->string('photo')->nullable();
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_follow_ups');
    }
};
