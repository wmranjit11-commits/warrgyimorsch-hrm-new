<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone');

            $table->unsignedBigInteger('department_id');

            $table->string('qualification')->nullable();
            $table->string('experience')->nullable();

            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();

            $table->unsignedBigInteger('interviewer_id')->nullable();

            $table->string('status')->default('Pending');

            $table->string('resume')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');

            $table->foreign('interviewer_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};