<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('nik')->primary();
            $table->string('nama_lengkap');
            $table->string('jabatan');
            $table->string('unit');
            $table->string('no_hp');
            $table->string('foto')->default('nophoto.png');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('unit')
                ->references('unit')
                ->on('unitperusahaan')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
