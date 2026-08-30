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
        Schema::table('karyawan', function (Blueprint $table) {
            $table->enum('posisi', ['Staff', 'Manager', 'GM', 'Direktur'])->nullable()->after('jabatan')->index();
            $table->string('atasan_nik')->nullable()->after('posisi')->index();
            $table->foreign('atasan_nik')->references('nik')->on('karyawan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign(['atasan_nik']);
            $table->dropColumn(['posisi', 'atasan_nik']);
        });
    }
};
