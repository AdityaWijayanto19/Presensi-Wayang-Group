<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Enums\WfhStatus;

class MarkUnpaid extends Command
{
    protected $signature = 'wfh:mark-unpaid';
    protected $description = 'Mark approved WFH as unpaid if laporan was not submitted before midnight';

    public function handle()
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $affected = DB::table('wfh')
            ->where('status', WfhStatus::Approved->value)
            ->where('tgl_wfh', '<', date('Y-m-d'))
            ->where(function ($q) {
                $q->whereNull('laporan_deskripsi')
                  ->orWhere('laporan_deskripsi', '');
            })
            ->update(['status' => WfhStatus::Unpaid->value]);

        $this->info("Marked {$affected} WFH records as unpaid.");

        cache()->forget('pending_wfh_count');
        cache()->forget('pending_wfh_admin_count');

        return 0;
    }
}
