<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('year');

            $table->unsignedInteger('entitlement')
                ->default(12);

            $table->unsignedInteger('used')
                ->default(0);

            $table->unsignedInteger('remaining')
                ->default(12);

            $table->timestamps();

            $table->unique([
                'employee_id',
                'year',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
