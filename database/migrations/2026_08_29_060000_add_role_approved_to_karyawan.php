<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('karyawan', 'role_approved')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->enum('role_approved', ['Staff', 'Manager', 'GM', 'Direktur'])->nullable()->after('posisi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('karyawan', 'role_approved')) {
            Schema::table('karyawan', function ($table) {
                $table->dropColumn('role_approved');
            });
        }
    }
};
