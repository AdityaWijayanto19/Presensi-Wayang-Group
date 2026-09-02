<?php

namespace App\Services;

use App\Enums\Jabatan;
use App\Enums\WfhStatus;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Minishlink\WebPush\WebPush;

class WfhService
{
    public static function initialStatus(Karyawan $karyawan): array
    {
        if ($karyawan->role_approved === 'Direktur' || empty($karyawan->role_approved) || empty($karyawan->atasan_nik)) {
            return [
                'status' => WfhStatus::PendingAdmin->value,
                'atasan_status' => 'pending',
                'admin_status' => 'pending',
            ];
        }
        return [
            'status' => WfhStatus::PendingAtasan->value,
            'atasan_status' => 'pending',
            'admin_status' => 'pending',
        ];
    }

    public static function initialLaporanStatus(Karyawan $karyawan): array
    {
        if ($karyawan->role_approved === 'Direktur' || empty($karyawan->role_approved) || empty($karyawan->atasan_nik)) {
            return [
                'laporan_status' => 'pending_admin',
                'laporan_atasan_status' => 'pending',
                'laporan_admin_status' => 'pending',
            ];
        }
        return [
            'laporan_status' => 'pending_atasan',
            'laporan_atasan_status' => 'pending',
            'laporan_admin_status' => 'pending',
        ];
    }

    public static function determineAtasanNik(Karyawan $karyawan): ?string
    {
        if ($karyawan->role_approved === 'Direktur' || empty($karyawan->role_approved) || empty($karyawan->atasan_nik)) {
            return null;
        }
        return $karyawan->atasan_nik;
    }

    public static function canApproveAtasan(object $wfh, Karyawan $karyawan): bool
    {
        return $wfh->atasan_nik === $karyawan->nik
            && $wfh->status === WfhStatus::PendingAtasan->value
            && $wfh->atasan_status === 'pending';
    }

    public static function canApproveAdmin(object $wfh): bool
    {
        return $wfh->status === WfhStatus::PendingAdmin->value
            && $wfh->admin_status === 'pending';
    }

    public static function canLapor($presensi): bool
    {
        if (!$presensi || !$presensi->jam_in) return false;
        $jamMasuk = \Carbon\Carbon::parse($presensi->jam_in);
        $selisihJam = $jamMasuk->diffInHours(now());
        return $selisihJam >= 7;
    }

    public static function getStempelPath(): ?string
    {
        if (file_exists(public_path('storage/uploads/stempel/stempel.png'))) {
            return 'storage/uploads/stempel/stempel.png';
        }
        if (file_exists(storage_path('app/template/stempel.png'))) {
            if (!is_dir(public_path('storage/uploads/stempel'))) {
                @mkdir(public_path('storage/uploads/stempel'), 0755, true);
            }
            @copy(storage_path('app/template/stempel.png'), public_path('storage/uploads/stempel/stempel.png'));
            return 'storage/uploads/stempel/stempel.png';
        }
        return null;
    }

    public static function deleteWfhFiles(object $wfh): void
    {
        if (!empty($wfh->pdf_form_path)) {
            Storage::disk('public')->delete($wfh->pdf_form_path);
        }
        if (!empty($wfh->laporan_file)) {
            Storage::disk('public')->delete($wfh->laporan_file);
        }
    }

    public static function generatePdf(array $data, ?string $stempelPath = null): string
    {
        $pdf = Pdf::loadView('presensi.pengajuan-wfh-pdf', array_merge($data, ['stempelPath' => $stempelPath]));
        $pdf->setPaper('A4', 'portrait');
        $dir = 'wfh';
        $filename = Str::uuid() . '-' . Str::slug($data['nama_lengkap']) . '-wfh.pdf';
        $path = $dir . '/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        return $path;
    }

    public static function generateLaporanPdf(array $data, ?string $stempelPath = null): string
    {
        $pdf = Pdf::loadView('presensi.laporan-pdf', array_merge($data, ['stempelPath' => $stempelPath]));
        $pdf->setPaper('A4', 'portrait');
        $filename = Str::uuid() . '-laporan-' . Str::slug($data['nama_lengkap']) . '.pdf';
        $path = 'wfh/laporan/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        return $path;
    }

    public static function sendWebPush(string $nik, string $title, string $body, ?string $url = null, ?string $tag = null): void
    {
        if (!config('webpush.enabled')) return;

        try {
            $subscriptions = DB::table('push_subscriptions')->where('nik', $nik)->get();
            if ($subscriptions->isEmpty()) return;

            $auth = [
                'VAPID' => [
                    'subject' => config('webpush.vapid.subject'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ];

            $webPush = new WebPush($auth);

            foreach ($subscriptions as $sub) {
                $payload = json_encode([
                    'title' => $title,
                    'body' => $body,
                    'url' => $url ?? '/dashboard',
                    'tag' => $tag,
                ]);

                $subscription = new \Minishlink\WebPush\Subscription(
                    $sub->endpoint,
                    $sub->public_key,
                    $sub->auth_token
                );

                $webPush->sendOneNotification(
                    $subscription,
                    $payload,
                    ['TTL' => 3600]
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Web push failed: ' . $e->getMessage());
        }
    }
}
