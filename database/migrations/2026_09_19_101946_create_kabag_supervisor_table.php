<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kabag_supervisor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kabag_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('supervisor_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['kabag_user_id', 'supervisor_user_id'],
                'kabag_supervisor_unique'
            );

            $table->index(
                'kabag_user_id',
                'kabag_supervisor_kabag_idx'
            );

            $table->index(
                'supervisor_user_id',
                'kabag_supervisor_supervisor_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kabag_supervisor');
    }
};
