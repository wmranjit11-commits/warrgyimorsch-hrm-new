<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Departments
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('departments', 'short_name')) {
                Schema::table('departments', function (Blueprint $table) {
                    $table->string('short_name')->nullable()->after('name');
                });
            }
        }

        // 2. Designations
        if (!Schema::hasTable('designations')) {
            Schema::create('designations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('designations', 'short_name')) {
                Schema::table('designations', function (Blueprint $table) {
                    $table->string('short_name')->nullable()->after('name');
                });
            }
        }

        // 3. Roles Master
        if (!Schema::hasTable('roles_master')) {
            Schema::create('roles_master', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Not dropping by default to be safe with data, but here for reference
        // Schema::dropIfExists('departments');
        // Schema::dropIfExists('designations');
        // Schema::dropIfExists('roles_master');
    }
};
