<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->foreignId('kabag_user_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('kabag_status', 20)
                ->default('pending')
                ->after('status');

            $table->foreignId('kabag_approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('kabag_approved_at')
                ->nullable();

            $table->foreignId('kabag_rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('kabag_rejected_at')
                ->nullable();

            $table->text('kabag_rejection_reason')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->dropForeign(['kabag_user_id']);
            $table->dropForeign(['kabag_approved_by']);
            $table->dropForeign(['kabag_rejected_by']);

            $table->dropColumn([
                'kabag_user_id',
                'kabag_status',
                'kabag_approved_by',
                'kabag_approved_at',
                'kabag_rejected_by',
                'kabag_rejected_at',
                'kabag_rejection_reason',
            ]);
        });
    }
};
