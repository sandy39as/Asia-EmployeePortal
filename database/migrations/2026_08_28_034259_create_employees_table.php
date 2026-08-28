<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            /*
             * ID asli dari tabel karyawans FaceLog.
             * Ini yang menjadi penghubung utama kedua sistem.
             */
            $table->unsignedBigInteger('source_karyawan_id')->unique();

            /*
             * ID yang digunakan karyawan untuk login.
             * Contoh:
             * A0001
             * A0403
             * A0719
             */
            $table->string('employee_code', 20)->unique();

            $table->string('nama');

            /*
             * Hanya informasi/reference.
             * TIDAK digunakan sebagai identity karena PIN bisa sama.
             */
            $table->string('pin_fingerspot')->nullable();

            /*
             * Reference device FaceLog jika suatu saat dibutuhkan.
             */
            $table->unsignedBigInteger('source_device_id')->nullable();

            $table->string('jabatan')->nullable();

            $table->date('tanggal_masuk')->nullable();

            $table->string('status_kerja')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();

            $table->index('nama');
            $table->index('pin_fingerspot');
            $table->index('source_device_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
