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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('pf_number')->nullable()->after('pf');
            $table->string('esi_number')->nullable()->after('esi');
            $table->string('insurance_provider')->nullable()->after('insurance');
            $table->string('insurance_policy_number')->nullable()->after('insurance_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['pf_number', 'esi_number', 'insurance_provider', 'insurance_policy_number']);
        });
    }
};
