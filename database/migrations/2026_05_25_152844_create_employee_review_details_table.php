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
        Schema::create('employee_review_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id');
            $table->string('criteria_name');
            $table->decimal('criteria_point',8,2);
            $table->decimal('self_review',8,2)->default(0);
            $table->decimal('author_review',8,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_review_details');
    }
};
