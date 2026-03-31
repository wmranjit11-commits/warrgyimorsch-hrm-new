<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('leave_type'); // Paid, Sick, Religious, Gatepass
            $table->string('leave_category'); // Full, Half, Gatepass
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('reason')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, on_hold
            $table->float('total_days')->default(1.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
