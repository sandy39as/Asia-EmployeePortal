<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kabag_employee', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kabag_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'kabag_user_id',
                'employee_id',
            ]);

            // Satu karyawan hanya punya satu Kabag aktif.
            $table->unique('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kabag_employee');
    }
};
