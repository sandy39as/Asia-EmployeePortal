<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            /*
             * izin
             * cuti
             * sakit
             */
            $table->string('jenis', 30);

            /*
             * full_day
             * hourly
             */
            $table->string('durasi_type', 20)
                ->default('full_day');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            /*
             * Hanya dipakai kalau durasi_type = hourly
             */
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            $table->text('alasan');

            /*
             * File surat dokter / bukti / dokumen pendukung
             */
            $table->string('lampiran_path')->nullable();
            $table->string('lampiran_original_name')->nullable();

            /*
             * pending
             * approved
             * rejected
             * cancelled
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Approval Portal
             */
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();

            $table->text('rejection_reason')->nullable();

            /*
             * Nanti digunakan ketika terhubung ke FaceLog lokal.
             */
            $table->timestamp('local_synced_at')->nullable();
            $table->string('local_sync_status', 30)
                ->nullable();

            $table->timestamps();

            $table->index([
                'employee_id',
                'status',
            ]);

            $table->index([
                'tanggal_mulai',
                'tanggal_selesai',
            ]);

            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
