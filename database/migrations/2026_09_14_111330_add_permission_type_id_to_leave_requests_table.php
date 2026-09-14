<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_type_id')
                ->nullable()
                ->after('leave_category');

            $table->foreign('permission_type_id')
                ->references('id')
                ->on('permission_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign([
                'permission_type_id',
            ]);

            $table->dropColumn(
                'permission_type_id'
            );
        });
    }
};
