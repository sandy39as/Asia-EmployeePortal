<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table
                ->boolean('must_change_username')
                ->default(false)
                ->after('must_change_password');
        });

        DB::table('users')
            ->where('role', 'karyawan')
            ->where('must_change_password', true)
            ->update([
                'must_change_username' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_username');
        });
    }
};
