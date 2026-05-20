<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_status_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('task_status_histories', 'task_id')) {
                $table->foreignId('task_id')->nullable()->after('id')->constrained('daily_tasks')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('task_status_histories', 'old_status')) {
                $table->string('old_status')->nullable()->after('task_id');
            }

            if (!Schema::hasColumn('task_status_histories', 'new_status')) {
                $table->string('new_status')->nullable()->after('old_status');
            }

            if (!Schema::hasColumn('task_status_histories', 'comment')) {
                $table->text('comment')->nullable()->after('new_status');
            }

            if (!Schema::hasColumn('task_status_histories', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('comment')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('task_status_histories', function (Blueprint $table) {
            if (Schema::hasColumn('task_status_histories', 'updated_by')) {
                $table->dropConstrainedForeignId('updated_by');
            }

            if (Schema::hasColumn('task_status_histories', 'task_id')) {
                $table->dropConstrainedForeignId('task_id');
            }

            if (Schema::hasColumn('task_status_histories', 'comment')) {
                $table->dropColumn('comment');
            }

            if (Schema::hasColumn('task_status_histories', 'new_status')) {
                $table->dropColumn('new_status');
            }

            if (Schema::hasColumn('task_status_histories', 'old_status')) {
                $table->dropColumn('old_status');
            }
        });
    }
};
