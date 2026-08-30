<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update karyawan.posisi enum to include Intern and SPV
        $posisiExists = DB::select("SHOW COLUMNS FROM karyawan LIKE 'posisi'");
        if (!empty($posisiExists)) {
            DB::statement("ALTER TABLE karyawan MODIFY COLUMN posisi ENUM('Intern','Staff','SPV','Manager','GM','Direktur') NULL");
        }

        // 1b. Update wfh.status enum to include unpaid
        DB::statement("ALTER TABLE wfh MODIFY COLUMN status ENUM('pending_atasan','pending_admin','approved','rejected','unpaid') DEFAULT 'pending_atasan' NOT NULL");

        // 2. Add keterangan (alasan WFH) to wfh table
        if (!Schema::hasColumn('wfh', 'keterangan')) {
            Schema::table('wfh', function (Blueprint $table) {
                $table->text('keterangan')->nullable()->after('deskripsi_pekerjaan');
            });
        }

        // 3. Add laporan workflow columns to wfh table (only missing ones)
        $columnsToAdd = [
            'laporan_atasan_nik' => function ($table) { $table->char('laporan_atasan_nik', 16)->nullable()->after('file_laporan'); },
            'laporan_status' => function ($table) { $table->enum('laporan_status', ['pending_atasan', 'pending_admin', 'approved', 'rejected'])->nullable()->after('laporan_atasan_nik'); },
            'laporan_atasan_status' => function ($table) { $table->enum('laporan_atasan_status', ['pending', 'approved', 'rejected'])->nullable()->after('laporan_status'); },
            'laporan_admin_status' => function ($table) { $table->enum('laporan_admin_status', ['pending', 'approved', 'rejected'])->nullable()->after('laporan_atasan_status'); },
            'laporan_rejected_reason' => function ($table) { $table->text('laporan_rejected_reason')->nullable()->after('laporan_admin_status'); },
            'laporan_approved_at' => function ($table) { $table->timestamp('laporan_approved_at')->nullable()->after('laporan_rejected_reason'); },
            'laporan_images' => function ($table) { $table->text('laporan_images')->nullable()->after('laporan_file'); },
        ];

        Schema::table('wfh', function (Blueprint $table) use ($columnsToAdd) {
            foreach ($columnsToAdd as $col => $adder) {
                if (!Schema::hasColumn('wfh', $col)) {
                    $adder($table);
                }
            }
        });

        // Fix laporan_atasan_nik: must be CHAR(16) with utf8mb4_general_ci to match karyawan.nik
        $colInfo = DB::select("SELECT COLLATION_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wfh' AND COLUMN_NAME = 'laporan_atasan_nik'");
        $currentCollation = $colInfo[0]->COLLATION_NAME ?? '';
        if (strtolower($currentCollation) !== 'utf8mb4_general_ci') {
            DB::statement("ALTER TABLE wfh MODIFY COLUMN laporan_atasan_nik CHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
        }

        // 4. Add foreign key if not exists
        $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wfh' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'wfh_laporan_atasan_nik_foreign'");
        if (empty($fks)) {
            DB::statement("ALTER TABLE wfh ADD CONSTRAINT wfh_laporan_atasan_nik_foreign FOREIGN KEY (laporan_atasan_nik) REFERENCES karyawan(nik) ON DELETE SET NULL");
        }
    }

    public function down(): void
    {
        // Drop FK if exists
        $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wfh' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'wfh_laporan_atasan_nik_foreign'");
        if (!empty($fks)) {
            DB::statement("ALTER TABLE wfh DROP FOREIGN KEY wfh_laporan_atasan_nik_foreign");
        }

        DB::statement("ALTER TABLE karyawan MODIFY COLUMN posisi ENUM('Staff','Manager','GM','Direktur') NULL");
        DB::statement("ALTER TABLE wfh MODIFY COLUMN status ENUM('pending_atasan','pending_admin','approved','rejected') DEFAULT 'pending_atasan' NOT NULL");

        Schema::table('wfh', function (Blueprint $table) {
            $table->dropColumn([
                'keterangan',
                'laporan_atasan_nik',
                'laporan_status',
                'laporan_atasan_status',
                'laporan_admin_status',
                'laporan_rejected_reason',
                'laporan_approved_at',
                'laporan_images',
            ]);
        });
    }
};
