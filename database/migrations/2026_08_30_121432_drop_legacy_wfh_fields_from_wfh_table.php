<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wfh', function (Blueprint $table) {
            $table->dropColumn(['file_form', 'file_laporan']);
        });
    }

    public function down(): void
    {
        Schema::table('wfh', function (Blueprint $table) {
            $table->string('file_form')->nullable()->after('pdf_form_path');
            $table->string('file_laporan')->nullable()->after('file_form');
        });
    }
};
