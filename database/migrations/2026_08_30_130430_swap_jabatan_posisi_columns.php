<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================
        // KARYAWAN TABLE
        // Before: jabatan = job title (VARCHAR), posisi = hierarchy (ENUM)
        // After:  jabatan = hierarchy (ENUM), posisi = job title (VARCHAR)
        //
        // Handles two scenarios:
        //   A) posisi already has hierarchy data → just swap
        //   B) posisi is empty (production import) → extract hierarchy from job title first
        // =====================================================

        // Detect if posisi has hierarchy data or is empty
        $hasPosisiData = DB::table('karyawan')
            ->whereNotNull('posisi')
            ->where('posisi', '!=', '')
            ->count();

        if ($hasPosisiData === 0) {
            // Scenario B: posisi is empty — extract hierarchy from job title keywords
            DB::unprepared("
                UPDATE karyawan SET posisi = CASE
                    WHEN UPPER(jabatan) LIKE '%DIREKTUR%' THEN 'Direktur'
                    WHEN UPPER(jabatan) LIKE '%GM %' OR UPPER(jabatan) = 'GM' THEN 'GM'
                    WHEN UPPER(jabatan) LIKE '%MANAGER%' THEN 'Manager'
                    WHEN UPPER(jabatan) LIKE '%SPV%' OR UPPER(jabatan) LIKE '%SUPERVISOR%' THEN 'SPV'
                    WHEN UPPER(jabatan) LIKE '%INTERN%' OR UPPER(jabatan) LIKE '%PRAKERIN%' THEN 'Intern'
                    ELSE 'Staff'
                END
            ");
        }

        // Step 1: Backup job title ke kolom sementara
        $hasTmpCol = Schema::hasColumn('karyawan', '_tmp_job_title');
        if (!$hasTmpCol) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->string('_tmp_job_title')->nullable()->after('nama_lengkap');
            });
        }
        DB::table('karyawan')
            ->whereNotNull('jabatan')
            ->update(['_tmp_job_title' => DB::raw('jabatan')]);

        // Step 2: Copy hierarchy dari posisi → jabatan
        DB::table('karyawan')
            ->whereNotNull('posisi')
            ->update(['jabatan' => DB::raw('posisi')]);

        // Step 3: Ubah tipe kolom (ENUM untuk hierarchy, VARCHAR untuk job title)
        DB::statement("ALTER TABLE karyawan MODIFY COLUMN jabatan ENUM('Intern','Staff','SPV','Manager','GM','Direktur') NULL");
        DB::statement("ALTER TABLE karyawan MODIFY COLUMN posisi VARCHAR(255) NULL");

        // Step 4: Restore job title dari backup → posisi
        DB::table('karyawan')
            ->whereNotNull('_tmp_job_title')
            ->update(['posisi' => DB::raw('_tmp_job_title')]);

        // Step 5: Drop kolom sementara
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('_tmp_job_title');
        });

        // =====================================================
        // WFH TABLE
        // Before: jabatan = job title (VARCHAR)
        // After:  jabatan = hierarchy (ENUM), posisi = job title (VARCHAR)
        // =====================================================

        // Step 6: Tambah kolom posisi untuk job title
        if (!Schema::hasColumn('wfh', 'posisi')) {
            Schema::table('wfh', function (Blueprint $table) {
                $table->string('posisi')->nullable()->after('jabatan');
            });
        }

        // Step 7: Backup job title dari jabatan → posisi
        DB::table('wfh')
            ->whereNotNull('jabatan')
            ->update(['posisi' => DB::raw('jabatan')]);

        // Step 8: Update jabatan ke hierarchy level dari karyawan
        DB::statement('
            UPDATE wfh
            INNER JOIN karyawan ON wfh.nik = karyawan.nik
            SET wfh.jabatan = karyawan.jabatan
        ');

        // Step 9: Ubah tipe jabatan ke ENUM
        DB::statement("ALTER TABLE wfh MODIFY COLUMN jabatan ENUM('Intern','Staff','SPV','Manager','GM','Direktur') NULL");
    }

    public function down(): void
    {
        // Reverse WFH
        DB::table('wfh')->whereNotNull('posisi')->update(['jabatan' => DB::raw('posisi')]);
        DB::statement("ALTER TABLE wfh MODIFY COLUMN jabatan VARCHAR(255) NULL");
        Schema::table('wfh', function (Blueprint $table) {
            $table->dropColumn('posisi');
        });

        // Reverse Karyawan
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('_tmp_job_title')->nullable()->after('nama_lengkap');
        });
        DB::table('karyawan')
            ->whereNotNull('posisi')
            ->update(['_tmp_job_title' => DB::raw('posisi')]);

        DB::table('karyawan')
            ->whereNotNull('jabatan')
            ->update(['posisi' => DB::raw('jabatan')]);

        DB::table('karyawan')
            ->whereNotNull('_tmp_job_title')
            ->update(['jabatan' => DB::raw('_tmp_job_title')]);

        DB::statement("ALTER TABLE karyawan MODIFY COLUMN jabatan VARCHAR(255) NULL");
        DB::statement("ALTER TABLE karyawan MODIFY COLUMN posisi ENUM('Intern','Staff','SPV','Manager','GM','Direktur') NULL");

        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('_tmp_job_title');
        });
    }
};
