<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    // =====================================================
    // DASHBOARD ADMIN
    // =====================================================

    public function dashboardadmin()
    {
        // TANGGAL HARI INI
        $hariini = date('Y-m-d');
        $bulanini = date('m');
        $tahunini = date('Y');

        // REKAP PRESENSI HARI INI
        $rekappresensi = DB::table('presensi')
            ->selectRaw('
                COUNT(nik) as jmlhadir,
                COUNT(IF(terlambat > 0, 1, NULL)) as jmltelat
            ')
            ->where('tgl_presensi', $hariini)
            ->first();

        // REKAP IZIN / SAKIT HARI INI
        $rekapizin = DB::table('izin')
            ->selectRaw('COUNT(*) as jmlizin')
            ->where('tgl_izin', $hariini)
            ->whereIn('jenis_izin', ['i', 's'])
            ->first();

        // REKAP LEMBUR HARI INI
        $rekaplembur = DB::table('lembur')
            ->selectRaw('COUNT(*) as jmllembur')
            ->where('tgl_lembur', $hariini)
            ->first();

        // REKAP WFH HARI INI
        $rekapwfh = DB::table('wfh')
            ->selectRaw('COUNT(*) as jmlwfh')
            ->where('tgl_wfh', $hariini)
            ->first();

        // TOTAL KARYAWAN
        $jmlkaryawan = DB::table('karyawan')->count();

        // TAMPILKAN DASHBOARD
        return view(
            'dashboard.dashboardadmin',
            compact(
                'rekappresensi',
                'rekapizin',
                'rekaplembur',
                'rekapwfh',
                'jmlkaryawan'
            )
        );
    }



    // =====================================================
    // DASHBOARD / HOME KARYAWAN
    // =====================================================

    public function index()
    {
        // TANGGAL
        $hariini = date('Y-m-d');
        $bulanini = date('m') * 1;
        $tahunini = date('Y');

        // DATA KARYAWAN LOGIN
        $nik = Auth::guard('karyawan')->user()->nik;

        // PRESENSI HARI INI
        $presensihariini = DB::table('presensi')
            ->where('nik', $nik)
            ->where('tgl_presensi', $hariini)
            ->first();

        // HISTORI PRESENSI BULAN INI
        $historibulanini = DB::table('presensi')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        // REKAP KEHADIRAN BULAN INI
        $rekappresensi = DB::table('presensi')
            ->selectRaw('
                COUNT(nik) as jmlhadir,
                COUNT(IF(terlambat > 0, 1, NULL)) as jmltelat
            ')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi) = "' . $tahunini . '"')
            ->first();

        // NAMA BULAN
        $namabulan = [
            "",
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        // REKAP IZIN & SAKIT BULAN INI
        $rekapizin = DB::table('izin')
            ->selectRaw('
                SUM(IF(jenis_izin = "i" OR jenis_izin = "s", 1, 0)) as jmlizin,
                SUM(IF(jenis_izin = "l", 1, 0)) as jmllembur
            ')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_izin) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_izin) = "' . $tahunini . '"')
            ->first();

        // REKAP LEMBUR BULAN INI
        $rekaplembur = DB::table('lembur')
            ->selectRaw('COUNT(id) as jmllembur')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_lembur) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_lembur) = "' . $tahunini . '"')
            ->first();
            
        // =====================================================
        // REKAP WFH BULAN INI
        // =====================================================
        
        $rekapwfh = DB::table('wfh')
            ->selectRaw('COUNT(id) as jmlwfh')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_wfh) = "' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_wfh) = "' . $tahunini . '"')
            ->first();

        // TAMPILKAN DASHBOARD
        return view(
            'dashboard.dashboard',
            compact(
                'presensihariini',
                'historibulanini',
                'namabulan',
                'bulanini',
                'tahunini',
                'rekappresensi',
                'rekapizin',
                'rekaplembur',
                'rekapwfh'
)
        );
    }
}