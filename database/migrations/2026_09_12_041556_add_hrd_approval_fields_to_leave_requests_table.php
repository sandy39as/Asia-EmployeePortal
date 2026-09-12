<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->string('hrd_status', 20)
                ->default('waiting')
                ->after('kabag_status');

            $table->foreignId('hrd_approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('hrd_approved_at')
                ->nullable();

            $table->foreignId('hrd_rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('hrd_rejected_at')
                ->nullable();

            $table->text('hrd_rejection_reason')
                ->nullable();

            $table->string('hrd_action_source', 20)
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->dropForeign(['hrd_approved_by']);
            $table->dropForeign(['hrd_rejected_by']);

            $table->dropColumn([
                'hrd_status',
                'hrd_approved_by',
                'hrd_approved_at',
                'hrd_rejected_by',
                'hrd_rejected_at',
                'hrd_rejection_reason',
                'hrd_action_source',
            ]);
        });
    }
};
