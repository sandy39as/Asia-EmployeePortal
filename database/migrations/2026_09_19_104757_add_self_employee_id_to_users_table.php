<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('self_employee_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('employees')
                ->nullOnDelete();

            $table->index(
                ['role', 'self_employee_id'],
                'users_role_self_employee_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_self_employee_idx');
            $table->dropConstrainedForeignId('self_employee_id');
        });
    }
};
