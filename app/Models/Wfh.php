<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wfh extends Model
{
    protected $table = 'wfh';
    protected $fillable = [
        'nik',
        'tgl_wfh',
        'file_form',
        'file_laporan',
        'dikirim_tanggal',
    ];

    protected $casts = [
        'tgl_wfh' => 'date',
        'dikirim_tanggal' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
