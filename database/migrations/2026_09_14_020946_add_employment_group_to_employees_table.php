<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employment_group', 30)
                ->nullable()
                ->after('status_kerja')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex([
                'employment_group'
            ]);

            $table->dropColumn(
                'employment_group'
            );
        });
    }
};
