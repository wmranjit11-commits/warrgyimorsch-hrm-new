<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->time('start_time')->after('end_date')->nullable();
            $table->time('end_time')->after('start_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
