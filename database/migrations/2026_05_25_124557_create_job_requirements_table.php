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
        Schema::create('job_requirements', function (Blueprint $table) {
            $table->id();

            $table->enum('priority',[
                'Urgent',
                'High',
                'Medium',
                'Low'
            ]);

            $table->date('date');

            $table->enum('candidate_type',[
                'Fresher',
                'Experience'
            ]);

            $table->integer('minimum_experience')->nullable();

            $table->json('skills');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requirements');
    }
};
