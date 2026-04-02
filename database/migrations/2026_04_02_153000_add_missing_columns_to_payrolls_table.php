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
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'unpaid_days')) {
                $table->decimal('unpaid_days', 8, 2)->after('payable_days')->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'salary_loss')) {
                $table->decimal('salary_loss', 12, 2)->after('unpaid_days')->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'monthly_salary')) {
                $table->decimal('monthly_salary', 12, 2)->after('net_salary')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['unpaid_days', 'salary_loss', 'monthly_salary']);
        });
    }
};
