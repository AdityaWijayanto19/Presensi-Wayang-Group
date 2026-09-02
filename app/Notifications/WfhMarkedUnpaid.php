<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Services\WfhService;

class WfhMarkedUnpaid extends Notification
{
    use Queueable;

    public function __construct(public $wfh) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'wfh_unpaid',
            'wfh_id' => $this->wfh->id,
            'tgl_wfh' => $this->wfh->tgl_wfh,
            'message' => 'WFH tanggal ' . $this->wfh->tgl_wfh . ' ditandai sebagai Unpaid karena belum upload laporan',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    public function sendWebPush(object $notifiable): void
    {
        WfhService::sendWebPush(
            $notifiable->nik,
            'WFH Unpaid',
            'WFH tanggal ' . $this->wfh->tgl_wfh . ' ditandai sebagai Unpaid karena belum upload laporan',
            '/presensi/wfh',
            'wfh-unpaid-' . $this->wfh->id
        );
    }
}
