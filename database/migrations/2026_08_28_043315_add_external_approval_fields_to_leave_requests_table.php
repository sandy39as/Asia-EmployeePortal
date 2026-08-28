<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'leave_requests',
            function (Blueprint $table) {

                $table->string(
                    'external_approved_by_name'
                )
                    ->nullable()
                    ->after('approved_at');

                $table->string(
                    'external_rejected_by_name'
                )
                    ->nullable()
                    ->after('rejected_at');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'leave_requests',
            function (Blueprint $table) {

                $table->dropColumn([
                    'external_approved_by_name',
                    'external_rejected_by_name',
                ]);
            }
        );
    }
};
