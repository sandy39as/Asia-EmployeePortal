<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->unsignedBigInteger(
                'source_kategori_karyawan_id'
            )
                ->nullable()
                ->after('employment_group');

            $table->string(
                'source_kategori_karyawan_name',
                150
            )
                ->nullable()
                ->after('source_kategori_karyawan_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropColumn([
                'source_kategori_karyawan_id',
                'source_kategori_karyawan_name',
            ]);
        });
    }
};
