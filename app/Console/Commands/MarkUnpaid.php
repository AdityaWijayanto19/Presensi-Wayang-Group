<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Enums\WfhStatus;
use App\Notifications\WfhMarkedUnpaid;

class MarkUnpaid extends Command
{
    protected $signature = 'wfh:mark-unpaid';
    protected $description = 'Mark approved WFH as unpaid if laporan was not submitted before midnight';

    public function handle()
    {
        // 1. Query WFH yang akan jadi unpaid (sebelum update)
        $wfhList = DB::table('wfh')
            ->where('status', WfhStatus::Approved->value)
            ->where('tgl_wfh', '<', date('Y-m-d'))
            ->where(function ($q) {
                $q->whereNull('laporan_deskripsi')
                  ->orWhere('laporan_deskripsi', '');
            })
            ->get();

        if ($wfhList->isEmpty()) {
            $this->info('No WFH records to mark as unpaid.');
            return 0;
        }

        // 2. Update status ke unpaid
        $affected = DB::table('wfh')
            ->where('status', WfhStatus::Approved->value)
            ->where('tgl_wfh', '<', date('Y-m-d'))
            ->where(function ($q) {
                $q->whereNull('laporan_deskripsi')
                  ->orWhere('laporan_deskripsi', '');
            })
            ->update(['status' => WfhStatus::Unpaid->value]);

        $this->info("Marked {$affected} WFH records as unpaid.");

        // 3. Kirim notifikasi + web push ke setiap karyawan
        foreach ($wfhList as $wfh) {
            $karyawan = \App\Models\Karyawan::where('nik', $wfh->nik)->first();
            if ($karyawan) {
                $karyawan->notify(new WfhMarkedUnpaid($wfh));
                // Web push via WfhService
                \App\Services\WfhService::sendWebPush(
                    $wfh->nik,
                    'WFH Unpaid',
                    'WFH tanggal ' . $wfh->tgl_wfh . ' ditandai sebagai Unpaid karena belum upload laporan',
                    '/presensi/wfh'
                );
            }
        }

        $this->info('Notifications sent to ' . $wfhList->count() . ' karyawan.');

        cache()->forget('pending_wfh_count');
        cache()->forget('pending_wfh_admin_count');

        return 0;
    }
}
