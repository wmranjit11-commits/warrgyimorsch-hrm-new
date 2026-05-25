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
        Schema::table('task_follow_ups', function (Blueprint $table) {
            if (!Schema::hasColumn('task_follow_ups', 'project_id')) {
                $table->foreignId('project_id')
                    ->nullable()
                    ->after('daily_task_id')
                    ->constrained('projects')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_follow_ups', function (Blueprint $table) {
            if (Schema::hasColumn('task_follow_ups', 'project_id')) {
                $table->dropConstrainedForeignId('project_id');
            }
        });
    }
};
