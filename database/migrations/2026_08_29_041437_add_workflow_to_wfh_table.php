<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wfh', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('nik');
            $table->string('live_location')->nullable()->after('tgl_wfh');
            $table->text('deskripsi_pekerjaan')->nullable()->after('live_location');
            $table->char('atasan_nik', 16)->charset('utf8mb4')->collation('utf8mb4_general_ci')->nullable()->after('deskripsi_pekerjaan')->index();
            $table->enum('status', ['pending_atasan','pending_admin','approved','rejected'])->default('pending_atasan')->after('atasan_nik')->index();
            $table->enum('atasan_status', ['pending','approved','rejected'])->default('pending')->after('status');
            $table->enum('admin_status', ['pending','approved','rejected'])->default('pending')->after('atasan_status');
            $table->text('rejected_reason')->nullable()->after('admin_status');
            $table->string('pdf_form_path')->nullable()->after('rejected_reason');
            $table->text('laporan_deskripsi')->nullable()->after('pdf_form_path');
            $table->string('laporan_file')->nullable()->after('laporan_deskripsi');
            $table->timestamp('approved_at')->nullable()->after('laporan_file');
            $table->foreign('atasan_nik')->references('nik')->on('karyawan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wfh', function (Blueprint $table) {
            $table->dropForeign(['atasan_nik']);
            $table->dropColumn(['jabatan','live_location','deskripsi_pekerjaan','atasan_nik','status','atasan_status','admin_status','rejected_reason','pdf_form_path','laporan_deskripsi','laporan_file','approved_at']);
        });
    }
};
