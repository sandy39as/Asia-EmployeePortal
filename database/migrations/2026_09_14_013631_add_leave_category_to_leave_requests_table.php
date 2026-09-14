<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->string(
                'leave_category',
                30
            )
                ->nullable()
                ->after('jenis');

            $table->foreignId(
                'special_leave_type_id'
            )
                ->nullable()
                ->after('leave_category')
                ->constrained(
                    'special_leave_types'
                )
                ->nullOnDelete();

            $table->unsignedInteger(
                'leave_days'
            )
                ->nullable()
                ->after('special_leave_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {

            $table->dropForeign([
                'special_leave_type_id'
            ]);

            $table->dropColumn([
                'leave_category',
                'special_leave_type_id',
                'leave_days',
            ]);
        });
    }
};
