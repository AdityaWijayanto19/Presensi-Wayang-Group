<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wfh', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->date('tgl_wfh');
            $table->string('file_form');
            $table->string('file_laporan');
            $table->timestamp('dikirim_tanggal');
            $table->timestamps();

            $table->foreign('nik')
                ->references('nik')
                ->on('karyawan')
                ->onDelete('cascade');

            $table->index(['tgl_wfh', 'nik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wfh');
    }
};
