<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_id')
                ->nullable()
                ->after('id')
                ->unique()
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('username')
                ->nullable()
                ->after('name')
                ->unique();

            $table->string('role', 30)
                ->default('karyawan')
                ->after('password');

            /*
             * TRUE = akun masih menggunakan password awal.
             * Setelah login pertama wajib mengganti password.
             */
            $table->boolean('must_change_password')
                ->default(true)
                ->after('role');

            $table->boolean('is_active')
                ->default(true)
                ->after('must_change_password');
        });

        /*
         * Email untuk karyawan tidak diwajibkan.
         */
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);

            $table->dropColumn([
                'employee_id',
                'username',
                'role',
                'must_change_password',
                'is_active',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
