<?php

namespace App\Http\Controllers;

use App\Enums\Jabatan;
use App\Enums\WfhStatus;
use App\Notifications\WfhApproved;
use App\Notifications\WfhApprovedByAtasan;
use App\Notifications\WfhMarkedUnpaid;
use App\Notifications\WfhRejected;
use App\Notifications\WfhSubmitted;
use App\Services\WfhService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PresensiController extends Controller
{

    // =====================================================
    // PRESENSI KARYAWAN
    // =====================================================

    public function create()
    {
        $hariini = date('Y-m-d');
        $nik = Auth::guard('karyawan')->user()->nik;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
        return view('presensi.create', compact('cek'));
    }

    // PROSES PRESENSI MASUK / PULANG
    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        if ($jam < '07:00:00') {
            echo "error|Presensi baru dibuka pukul 07:00!|in";
            return;
        }
        $karyawan = DB::table('karyawan')
            ->where('nik', $nik)
            ->first();

        //ambil data jam unit perusahaan
        $unit = $karyawan->unit;

        $unitkerja = DB::table('unitperusahaan')
            ->where('unit', $unit)
            ->first();

        $jam_masuk = $unitkerja->jam_masuk;


        // ================= PERHITUNGAN KETERLAMBATAN =================
        $terlambat = 0;

        // Arthama tidak memiliki aturan keterlambatan
        if ($unit != 'Arthama') {

            $jamMasuk = strtotime($jam_masuk);
            $jamAbsen = strtotime($jam);

            if ($jamAbsen > $jamMasuk) {

                // Selisih menit dari jam masuk
                $selisihMenit = floor(($jamAbsen - $jamMasuk) / 60);

                if ($selisihMenit <= 60) {

                    // 1 - 60 menit dihitung sesuai menit sebenarnya
                    $terlambat = $selisihMenit;

                } else {

                    // >60 menit dibulatkan per kelipatan 60
                    $terlambat = floor($selisihMenit / 60) * 60;

                }

            }

        }

        // //perhitungan lebih 5 menit akan terlambat
        // $jam_batas_telat = date(
        //     'H:i:s',
        //     strtotime($jam_masuk . ' +5 minutes')
        // );


        // $terlambat = 0;

        // if ($jam > $jam_batas_telat) {

        //     $jamMasukHour = (int) date('H', strtotime($jam_masuk));
        //     $jamAbsenHour = (int) date('H', strtotime($jam));

        //     $terlambat = $jamAbsenHour - $jamMasukHour;

        //         if ($terlambat < 1) {
        //             $terlambat = 1;
        //         }
        // }

        $lokasi = $request->lokasi;
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";

        // CEK DATA PRESENSI HARI INI
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $tgl_presensi)
            ->where('nik', $nik)
            ->first();

        // PENENTU IN / OUT
        if($cek){
            $status = "out";
        } else {
            $status = "in";
        }

        // VALIDASI MINIMAL 8 JAM KERJA
        if ($cek && $cek->jam_out == null) {

            $jamMasuk = strtotime($cek->jam_in);
            $jamSekarang = strtotime($jam);

            $selisihJamKerja = ($jamSekarang - $jamMasuk) / 3600;

            if ($selisihJamKerja < 8) {

                $sisaJam = ceil(8 - $selisihJamKerja);

                echo "error|Belum bisa presensi pulang! Minimal bekerja 8 jam.|out";
                return;
            }
        }

        // CEK LAPORAN WFH - Karyawan WFH harus upload laporan sebelum absen pulang
        if ($cek && $cek->jam_out == null) {
            $wfhToday = DB::table('wfh')
                ->where('nik', $nik)
                ->where('tgl_wfh', $tgl_presensi)
                ->where('status', 'approved')
                ->first();
            if ($wfhToday && empty($wfhToday->laporan_deskripsi)) {
                echo 'error|Anda harus mengupload laporan WFH terlebih dahulu sebelum presensi pulang. Silakan upload laporan.|out';
                return;
            }
        }

        // CEK APAKAH SUDAH PRESENSI PULANG
        if($cek && $cek->jam_out != null){
            echo "error|Anda sudah melakukan presensi pulang!|done";
            return;
        }

        // UBAH NAMA FILE (FIX BUG KETIMPA)
        $formatName = $nik . "-" . $tgl_presensi . "-" . $status;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        // ================== PROSES ==================

        if($cek){
            // PRESENSI PULANG
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'lokasi_out' => $lokasi
            ];

            $update = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('nik',$nik)
                ->update($data_pulang);

            // if($update){
            //     echo "success|Presensi berhasil, selamat istirahat!|out";
            //     Storage::put($file, $image_base64);
            // }else{
            //     echo "error|Presensi gagal, coba lagi!|out";
            // }

            if($update){

                $hasil = file_put_contents(
                    public_path('storage/uploads/absensi/' . $fileName),
                    $image_base64
                );

                if($hasil){
                    echo "success|Presensi berhasil, selamat istirahat!|out";
                }else{
                    echo "error|Gagal menyimpan foto!|out";
                }
            }


        } else {
            // PRESENSI MASUK
            $data = [
                'nik' => $nik,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'lokasi_in' => $lokasi,
                'terlambat' => $terlambat,
            ];

            $simpan = DB::table('presensi')->insert($data);

            // if($simpan){
            //     echo "success|Presensi berhasil, selamat bekerja!|in";
            //     Storage::put($file, $image_base64);
            // }else{
            //     echo "error|Presensi gagal, coba lagi!|in";
            // }

            if($simpan){

                $hasil = file_put_contents(
                    public_path('storage/uploads/absensi/' . $fileName),
                    $image_base64
                );

                if($hasil){
                    echo "success|Presensi berhasil, selamat bekerja!|in";
                }else{
                    echo "error|Gagal menyimpan foto!|in";
                }
            }

        }
    }



    // =====================================================
    // HISTORI PRESENSI
    // =====================================================

    public function histori()
    {
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        return view ('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;

        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi) ="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi) ="' . $tahun . '"')
            ->where('nik', $nik)
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }



    // =====================================================
    // IZIN KARYAWAN
    // =====================================================

    public function izin()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dataizin = DB::table('izin')->where('nik', $nik)->orderBy('tgl_izin', 'desc')->get();

        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    // TAMPILKAN FILE IZIN
    public function showfile(string $file)
    {
        $path = storage_path('app/public/uploads/izin/' . $file);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path);
    }

    // SIMPAN DATA IZIN
    public function storeizin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        // VALIDASI
        $request->validate([
            'tgl_izin' => 'required|date',
            'jenis_izin' => 'required',
            'file' => 'required|mimes:pdf,doc,docx|max:4096'
        ]);

        // CEK APAKAH SUDAH ADA PENGAJUAN DI TANGGAL TERSEBUT
        $cek = DB::table('izin')
            ->where('nik', $nik)
            ->where('tgl_izin', $request->tgl_izin)
            ->count();

        if ($cek > 0) {

            return redirect()->back()->with(
                'error',
                'Anda sudah mengirim data izin pada tanggal tersebut!'
            );

        }


        // CEK ADA FILE
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            // BUAT NAMA FILE
            $nama_asli = $file->getClientOriginalName();

            // BUAT NAMA FILE UNIK
            $nama_file = date('YmdHis') . "-" . $nama_asli;

            // SIMPAN FILE
            $file->storeAs('public/uploads/izin', $nama_file);

            // SIMPAN KE DATABASE
            DB::table('izin')->insert([
                'nik' => $nik,
                'tgl_izin' => $request->tgl_izin,
                'jenis_izin' => $request->jenis_izin,
                'file' => $nama_file,
                'dikirim_tanggal' => now()
            ]);

            return redirect('/presensi/izin')->with('success', 'Data izin berhasil dikirim!');
        } else {
            return redirect()->back()->with('error', 'Data gagal dikirim!');
        }
    }

    // HAPUS DATA IZIN
    public function deleteizin(int $id)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        $izin = DB::table('izin')
            ->where('id', $id)
            ->where('nik', $nik)
            ->first();

        if (!$izin) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }

        Storage::delete('public/uploads/izin/' . $izin->file);

        DB::table('izin')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Data izin berhasil dihapus!');
    }

    // =====================================================
    // LEMBUR KARYAWAN
    // =====================================================

    public function lembur()
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        $datalembur = DB::table('lembur')
            ->where('nik', $nik)
            ->orderBy('tgl_lembur', 'desc')
            ->get();

        return view('presensi.lembur', compact('datalembur'));
    }

    public function buatlembur()
    {
        return view('presensi.buatlembur');
    }

    // TAMPILKAN FILE LEMBUR
    public function showfilelembur(string $file)
    {
        $path = storage_path('app/public/uploads/lembur/' . $file);

        if (!file_exists($path)) {
        abort(404);
        }

        return response()->file($path);
    }

    // SIMPAN DATA LEMBUR
    public function storelembur(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        // VALIDASI
        $request->validate([
            'tgl_lembur'   => 'required|date',
            'durasi'       => 'required|in:1 Jam,1.5 Jam,2 Jam,2.5 Jam,3 Jam,3.5 Jam,4 Jam,4.5 Jam,5 Jam,Prorate',
            'file_form'    => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
            'file_laporan' => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096'
        ]);

        // TIDAK BOLEH MEMILIH TANGGAL MASA DEPAN
        if ($request->tgl_lembur > date('Y-m-d')) {

            return redirect()->back()->with(
                'error',
                'Tanggal lembur tidak boleh melebihi hari ini!'
            );

        }

        // CEK APAKAH SUDAH ADA LEMBUR DI TANGGAL TERSEBUT
        $cek = DB::table('lembur')
            ->where('nik', $nik)
            ->where('tgl_lembur', $request->tgl_lembur)
            ->count();

        if ($cek > 0) {

            return redirect()->back()->with(
                'error',
                'Anda sudah mengirim data lembur pada tanggal tersebut!'
            );

        }

        // CEK KEDUA FILE
        if ($request->hasFile('file_form') && $request->hasFile('file_laporan')) {

            $form = $request->file('file_form');
            $laporan = $request->file('file_laporan');

            // NAMA FILE
            $timestamp = date('YmdHis');

            $nama_form = $timestamp . "-form-" . $form->getClientOriginalName();
            $nama_laporan = $timestamp . "-laporan-" . $laporan->getClientOriginalName();

            // SIMPAN FILE
            $form->storeAs('public/uploads/lembur', $nama_form);
            $laporan->storeAs('public/uploads/lembur', $nama_laporan);

            // SIMPAN DATABASE
            DB::table('lembur')->insert([
                'nik'              => $nik,
                'tgl_lembur'       => $request->tgl_lembur,
                'durasi'           => $request->durasi,
                'file_form'        => $nama_form,
                'file_laporan'     => $nama_laporan,
                'dikirim_tanggal'  => now()
            ]);

            return redirect('/presensi/lembur')
                ->with('success', 'Data lembur berhasil dikirim!');

        } else {

            return redirect()->back()
                ->with('error', 'Data gagal dikirim!');

        }
    }

    // HAPUS DATA LEMBUR
    public function deletelembur(int $id)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        $lembur = DB::table('lembur')
            ->where('id', $id)
            ->where('nik', $nik)
            ->first();

        if (!$lembur) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }

        // HAPUS FILE FORM
        Storage::delete('public/uploads/lembur/' . $lembur->file_form);

        // HAPUS FILE LAPORAN
        Storage::delete('public/uploads/lembur/' . $lembur->file_laporan);

        // HAPUS DATA DATABASE
        DB::table('lembur')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Data lembur berhasil dihapus!');
    }
    // =====================================================
    // WFH KARYAWAN - Refactored Workflow (Best Practice)
    // =====================================================

    public function wfh()
    {
        $karyawan = Auth::guard("karyawan")->user();
        $nik = $karyawan->nik;

        // History WFH — hanya status terminal (sudah tidak ada tahapan lagi)
        $datawfh = DB::table("wfh")
            ->leftJoin("karyawan as atasan", "wfh.atasan_nik", "=", "atasan.nik")
            ->where("wfh.nik", $nik)
            ->where(function ($q) {
                // Approved + laporan sudah approved atau rejected (terminal)
                $q->where(function ($q2) {
                    $q2->where("wfh.status", WfhStatus::Approved->value)
                        ->whereIn("wfh.laporan_status", ["approved", "rejected"]);
                });
                // Atau pengajuan ditolak
                $q->orWhere("wfh.status", WfhStatus::Rejected->value);
                // Atau unpaid
                $q->orWhere("wfh.status", WfhStatus::Unpaid->value);
            })
            ->select("wfh.*", "atasan.nama_lengkap as atasan_nama", "atasan.jabatan as atasan_jabatan")
            ->orderBy("wfh.tgl_wfh", "desc")
            ->get();

        return view("presensi.wfh", compact("datawfh"));
    }

    public function buatwfh()
    {
        $karyawan = Auth::guard("karyawan")->user()->load("unitperusahaan");
        $nik = $karyawan->nik;
        // Ambil presensi hari ini untuk lokasi absen masuk
        $presensiToday = DB::table('presensi')
            ->where('nik', $nik)
            ->where('tgl_presensi', date('Y-m-d'))
            ->first();

        // Cek jam kerja untuk disable tanggal hari ini
        $unitkerja = DB::table('unitperusahaan')->where('unit', $karyawan->unit)->first();
        $jamMasuk = $unitkerja?->jam_masuk ?? '08:00:00';
        $sekarang = date('H:i:s');
        $disableToday = ($sekarang >= $jamMasuk);

        return view("presensi.buatwfh", compact("karyawan", "presensiToday", "disableToday"));
    }

    // =====================================================
    // TAMPILKAN FILE FORM WFH
    // =====================================================

    public function showfilewfh(string $file)
    {
        // Sanitize: only allow safe filename characters
        $file = basename($file);
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $file)) {
            abort(404);
        }

        $candidates = [
            'wfh/' . $file,
            'uploads/wfh/' . $file,
        ];
        foreach ($candidates as $rel) {
            if (Storage::disk('public')->exists($rel)) {
                return Storage::disk('public')->response($rel);
            }
        }
        // Fallback DB lookup
        $wfh = DB::table('wfh')
            ->where('pdf_form_path', 'like', '%/' . $file)
            ->orWhere('laporan_file', 'like', '%/' . $file)
            ->first();
        if ($wfh) {
            $try = [
                $wfh->pdf_form_path ?? '',
                $wfh->laporan_file ?? '',
            ];
            foreach ($try as $rel) {
                if ($rel && Storage::disk('public')->exists($rel)) {
                    return Storage::disk('public')->response($rel);
                }
                $abs = storage_path('app/public/' . $rel);
                if ($rel && file_exists($abs)) return response()->file($abs);
            }
        }
        abort(404);
    }


    // =====================================================
    // SIMPAN DATA WFH - New Workflow
    // =====================================================

    public function storewfh(Request $request)
    {
        $karyawan = Auth::guard("karyawan")->user();
        $nik = $karyawan->nik;

        $request->validate([
            "tgl_wfh" => "required|date|after_or_equal:today",
            "keterangan" => "required|string|min:5|max:1000",
            "deskripsi_pekerjaan" => "required|string|min:10|max:2000",
        ]);

        // Cek duplikat tanggal
        $cek = DB::table("wfh")->where("nik", $nik)->where("tgl_wfh", $request->tgl_wfh)->exists();
        if ($cek) {
            return redirect()->back()->with("error", "Anda sudah mengajukan WFH pada tanggal tersebut!")->withInput();
        }

        // Validasi H-1 minimal & jam kerja (optional, not blocking)
        // Ambil atasan dari DB (admin set)
        $karyawanFresh = \App\Models\Karyawan::with("unitperusahaan")->where("nik", $nik)->first();
        $atasanNik = WfhService::determineAtasanNik($karyawanFresh);
        $jabatan = $karyawanFresh->jabatan instanceof Jabatan ? $karyawanFresh->jabatan->value : $karyawanFresh->jabatan;
        $posisi = $karyawanFresh->posisi; // job title

        $initial = WfhService::initialStatus($karyawanFresh);
        $status = $initial["status"];
        $atasanStatus = $initial["atasan_status"];
        $adminStatus = $initial["admin_status"];

        // Data untuk PDF
        $unit = $karyawanFresh->unit;
        $perusahaan = $karyawanFresh->unitperusahaan->perusahaan ?? "-";
        $jabatan = $karyawanFresh->jabatan;
        $atasan = $atasanNik ? \App\Models\Karyawan::where("nik", $atasanNik)->first() : null;

        $pdfData = [
            "headerSuratPath" => "assets/img/header-surat.png",
            "nama_lengkap" => $karyawanFresh->nama_lengkap,
            "jabatan" => $jabatan instanceof Jabatan ? $jabatan->value : $jabatan,
            "posisi" => $posisi ?? "-",
            "perusahaan" => $perusahaan,
            "tgl_wfh" => $request->tgl_wfh,
            "deskripsi_pekerjaan" => $request->deskripsi_pekerjaan,
            "nama_atasan" => $atasan?->nama_lengkap ?? "-",
            "jabatan_atasan" => $atasan?->jabatan instanceof Jabatan ? $atasan->jabatan->value : ($atasan?->jabatan ?? "-"),
            "nama_approver" => "-",
            "jabatan_approver" => "-",
        ];

        // Stempel path
        $stempelPath = WfhService::getStempelPath();

        DB::beginTransaction();
        try {
            $pdfPath = WfhService::generatePdf($pdfData, $stempelPath);

            $id = DB::table("wfh")->insertGetId([
                "nik" => $nik,
                "jabatan" => $jabatan,
                "posisi" => $posisi,
                "tgl_wfh" => $request->tgl_wfh,
                "deskripsi_pekerjaan" => $request->deskripsi_pekerjaan,
                "keterangan" => $request->keterangan,
                "atasan_nik" => $atasanNik,
                "status" => $status,
                "atasan_status" => $atasanStatus,
                "admin_status" => $adminStatus,
                "pdf_form_path" => $pdfPath,
                "dikirim_tanggal" => now(),
            ]);

            $wfh = DB::table("wfh")->where("id", $id)->first();

            // Notifikasi ke atasan atau admin
            if ($atasanNik) {
                $atasanUser = \App\Models\Karyawan::where("nik", $atasanNik)->first();
                if ($atasanUser) {
                    $atasanUser->notify(new WfhSubmitted((object)$wfh, $karyawanFresh));
                    WfhService::sendWebPush($atasanNik, "Pengajuan WFH Baru", $karyawanFresh->nama_lengkap . " mengajukan WFH " . $request->tgl_wfh, '/presensi/datawfh', 'wfh-submitted-' . $wfh->id);
                }
            } else {
                // Direktur langsung ke admin - notify all admin users
                $admins = \App\Models\User::role("administrator")->get();
                foreach ($admins as $admin) {
                    $admin->notify(new WfhSubmitted((object)$wfh, $karyawanFresh));
                }
            }

            DB::commit();
            cache()->forget("pending_wfh_count");
            cache()->forget("pending_wfh_admin_count");
            return redirect("/presensi/wfh")->with("success", "Pengajuan WFH berhasil! Menunggu persetujuan.");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("storewfh failed: " . $e->getMessage());
            return redirect()->back()->with("error", "Gagal mengajukan WFH. Silakan coba lagi.")->withInput();
        }
    }

    // =====================================================
    // HAPUS DATA WFH
    // =====================================================

    public function deletewfh(int $id)
    {
        $nik = Auth::guard("karyawan")->user()->nik;

        $wfh = DB::table("wfh")
            ->where("id", $id)
            ->where("nik", $nik)
            ->first();

        if (!$wfh) {
            return redirect()->back()->with("error", "Data tidak ditemukan!");
        }

        // Hanya bisa hapus jika masih pending
        if (!in_array($wfh->status, [WfhStatus::PendingAtasan->value, WfhStatus::PendingAdmin->value])) {
            return redirect()->back()->with("error", "WFH yang sudah disetujui/ditolak tidak bisa dihapus!");
        }

        WfhService::deleteWfhFiles($wfh);

        DB::table("wfh")->where("id", $id)->delete();

        return redirect()->back()->with("success", "Data WFH berhasil dihapus!");
    }

    // =====================================================
    // APPROVAL ATASAN
    // =====================================================
    public function approveWfhAtasan(Request $request, int $id)
    {
        $karyawan = Auth::guard("karyawan")->user();
        $wfh = DB::table("wfh")->where("id", $id)->first();
        if (!$wfh) return redirect()->back()->with("error", "Data tidak ditemukan");
        if ($wfh->atasan_nik !== $karyawan->nik) return redirect()->back()->with("error", "Anda bukan atasan untuk pengajuan ini");
        if ($wfh->status !== WfhStatus::PendingAtasan->value) return redirect()->back()->with("error", "Status tidak valid");

        DB::table("wfh")->where("id", $id)->update([
            "atasan_status" => "approved",
            "status" => WfhStatus::PendingAdmin->value,
            ]);

        // Notifikasi ke pengaju
        $pengaju = \App\Models\Karyawan::where("nik", $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new WfhApprovedByAtasan((object)$wfh, $karyawan));
            WfhService::sendWebPush($wfh->nik, "WFH Disetujui", "WFH " . $wfh->tgl_wfh . " disetujui, menunggu persetujuan selanjutnya", null, 'wfh-approved-atasan-' . $wfh->id);
        }
        // Notifikasi ke admin
        $admins = \App\Models\User::role("administrator")->get();
        foreach ($admins as $admin) $admin->notify(new WfhApprovedByAtasan((object)$wfh, $karyawan));
        cache()->forget("pending_wfh_count"); cache()->forget("pending_wfh_admin_count");

        return redirect()->back()->with("success", "WFH disetujui, diteruskan ke Admin");
    }

    public function rejectWfhAtasan(Request $request, int $id)
    {
        $request->validate(["rejected_reason" => "required|string|min:5|max:500"]);
        $karyawan = Auth::guard("karyawan")->user();
        $wfh = DB::table("wfh")->where("id", $id)->first();
        if (!$wfh || $wfh->atasan_nik !== $karyawan->nik) return redirect()->back()->with("error", "Akses ditolak");
        if ($wfh->status !== WfhStatus::PendingAtasan->value) return redirect()->back()->with("error", "Status tidak valid untuk penolakan");

        DB::table("wfh")->where("id", $id)->update([
            "atasan_status" => "rejected",
            "status" => WfhStatus::Rejected->value,
            "rejected_reason" => $request->rejected_reason,
            ]);
        $pengaju = \App\Models\Karyawan::where("nik", $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new WfhRejected((object)$wfh, $request->rejected_reason));
            WfhService::sendWebPush($wfh->nik, "WFH Ditolak", "WFH " . $wfh->tgl_wfh . " ditolak: " . $request->rejected_reason, null, 'wfh-rejected-atasan-' . $wfh->id);
        }
        cache()->forget("pending_wfh_count");
        return redirect()->back()->with("success", "WFH ditolak");
    }

    // =====================================================
    // APPROVAL ADMIN
    // =====================================================
    public function approveWfhAdmin(int $id)
    {
        $user = Auth::guard('user')->user();
        $wfh = DB::table("wfh")->where("id", $id)->first();
        if (!$wfh) return redirect()->back()->with("error", "Data tidak ditemukan");
        if ($wfh->status !== WfhStatus::PendingAdmin->value) return redirect()->back()->with("error", "Status tidak valid untuk persetujuan");
        if ($wfh->admin_status !== 'pending') return redirect()->back()->with("error", "WFH ini sudah diproses");

        DB::table("wfh")->where("id", $id)->update([
            "admin_status" => "approved",
            "status" => WfhStatus::Approved->value,
            "approved_at" => now(),
            ]);
        $pengaju = \App\Models\Karyawan::where("nik", $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new WfhApproved((object)$wfh));
            WfhService::sendWebPush($wfh->nik, "WFH Disetujui ", "WFH " . $wfh->tgl_wfh . " disetujui! Silakan input Laporan.", '/presensi/wfh/' . $id . '/laporan', 'wfh-approved-admin-' . $wfh->id);
        }
        cache()->forget("pending_wfh_count"); cache()->forget("pending_wfh_admin_count");
        return redirect()->back()->with("success", "WFH disetujui. Karyawan bisa input laporan.");
    }

    public function rejectWfhAdmin(Request $request, int $id)
    {
        $request->validate(["rejected_reason" => "required|string|min:5|max:500"]);
        $user = Auth::guard('user')->user();
        $wfh = DB::table("wfh")->where("id", $id)->first();
        if (!$wfh) return redirect()->back()->with("error", "Data tidak ditemukan");
        if ($wfh->status !== WfhStatus::PendingAdmin->value) return redirect()->back()->with("error", "Status tidak valid untuk penolakan");
        if ($wfh->admin_status !== 'pending') return redirect()->back()->with("error", "WFH ini sudah diproses");

        DB::table("wfh")->where("id", $id)->update([
            "admin_status" => "rejected",
            "status" => WfhStatus::Rejected->value,
            "rejected_reason" => $request->rejected_reason,
            ]);
        $pengaju = \App\Models\Karyawan::where("nik", $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new WfhRejected((object)$wfh, $request->rejected_reason));
            WfhService::sendWebPush($wfh->nik, "WFH Ditolak", "WFH " . $wfh->tgl_wfh . " ditolak Admin", null, 'wfh-rejected-admin-' . $wfh->id);
        }
        cache()->forget("pending_wfh_count"); cache()->forget("pending_wfh_admin_count");
        return redirect()->back()->with("success", "WFH ditolak");
    }

    // =====================================================
    // REVERSE GEOCODING (Nominatim / OpenStreetMap)
    // =====================================================

    private function reverseGeocode(string $coordinates): string
    {
        if (empty($coordinates) || $coordinates === '-') {
            return '-';
        }

        $parts = array_map('floatval', explode(',', $coordinates));
        if (count($parts) !== 2) {
            return $coordinates;
        }

        $lat = $parts[0];
        $lng = $parts[1];

        try {
            $url = sprintf(
                'https://nominatim.openstreetmap.org/reverse?lat=%s&lon=%s&format=json&addressdetails=1&accept-language=id',
                urlencode($lat),
                urlencode($lng)
            );

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_HTTPHEADER     => ['User-Agent: PresensiDigital/1.0'],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                return $coordinates;
            }

            $data = json_decode($response, true);
            if (!isset($data['display_name'])) {
                return $coordinates;
            }

            return $data['display_name'];
        } catch (\Exception $e) {
            return $coordinates;
        }
    }

    // =====================================================
    // LAPORAN WFH (setelah approved) - Full workflow
    // =====================================================
    public function buatLaporanWfh(int $id)
    {
        $nik = Auth::guard("karyawan")->user()->nik;
        $wfh = DB::table("wfh")->where("id", $id)->where("nik", $nik)->where("status", WfhStatus::Approved->value)->first();
        if (!$wfh) abort(400, "Laporan hanya bisa diisi setelah WFH disetujui");

        // STRICT: Laporan hanya bisa diupload di TANGGAL WFH itu sendiri
        $tglWfh = $wfh->tgl_wfh instanceof \Carbon\Carbon ? $wfh->tgl_wfh->format('Y-m-d') : date('Y-m-d', strtotime($wfh->tgl_wfh));
        $hariIni = date('Y-m-d');
        if ($tglWfh !== $hariIni) {
            return redirect()->back()->with('error', 'Laporan hanya bisa diupload pada tanggal WFH (' . date('d M Y', strtotime($tglWfh)) . '). Hari ini: ' . date('d M Y') . '.');
        }

        // Cek apakah sudah absen masuk hari ini
        $presensiToday = DB::table('presensi')
            ->where('nik', $nik)
            ->where('tgl_presensi', $hariIni)
            ->first();
        if (!$presensiToday || !$presensiToday->jam_in) {
            return redirect()->back()->with('error', 'Anda belum melakukan absen masuk hari ini. Silakan absen masuk terlebih dahulu.');
        }

        // Cek apakah sudah 7 jam sejak jam_in
        $jamMasuk = \Carbon\Carbon::parse($presensiToday->jam_in);
        $selisihJam = $jamMasuk->diffInHours(now());
        if ($selisihJam < 7) {
            $sisa = ceil(7 - $selisihJam);
            return redirect()->back()->with('error', 'Laporan hanya bisa diisi setelah 7 jam absen masuk. Sisa waktu: ' . $sisa . ' jam.');
        }

        $liveLocation = $this->reverseGeocode($presensiToday->lokasi_in ?? '');

        return view("presensi.buat-laporan-wfh", compact("wfh", "liveLocation"));
    }

    public function storeLaporanWfh(Request $request, int $id)
    {
        $request->validate([
            "laporan_deskripsi" => "required|string|min:10|max:3000",
            "laporan_images" => "required|array|min:2|max:5",
            "laporan_images.*" => "required|image|mimes:jpg,jpeg,png|max:4096",
        ]);
        $nik = Auth::guard("karyawan")->user()->nik;
        $wfh = DB::table("wfh")->where("id", $id)->where("nik", $nik)->where("status", WfhStatus::Approved->value)->first();
        if (!$wfh) return redirect()->back()->with("error", "Akses ditolak");

        // Determine initial laporan status
        $karyawan = \App\Models\Karyawan::where('nik', $nik)->first();
        if (!$karyawan) return redirect()->back()->with("error", "Data karyawan tidak ditemukan");

        $presensiToday = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', date('Y-m-d'))->first();

        DB::beginTransaction();
        try {
            // Upload images
            $imagePaths = [];
            if ($request->hasFile('laporan_images')) {
                foreach ($request->file('laporan_images') as $idx => $file) {
                    $nama = Str::uuid() . '-laporan-' . ($idx + 1) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('wfh/laporan', $nama, 'public');
                    $imagePaths[] = $path;
                }
            }

            $initialLaporan = WfhService::initialLaporanStatus($karyawan);

            // Atasan for laporan
            $laporanAtasanNik = $karyawan->atasan_nik;
            if ($karyawan->role_approved === 'Direktur' || empty($karyawan->role_approved) || empty($laporanAtasanNik)) {
                $laporanAtasanNik = null;
            }

            $atasan = $laporanAtasanNik
                ? \App\Models\Karyawan::where('nik', $laporanAtasanNik)->first()
                : null;

            // Generate PDF Laporan
            $unit = $karyawan->unit;
            $jabatan = $karyawan->jabatan;
            $perusahaan = DB::table('unitperusahaan')->where('unit', $unit)->value('perusahaan') ?? '-';
            $weekdayMap = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
            $hariTanggal = $weekdayMap[now()->format('l')] . ', ' . now()->format('d F Y');

            $liveLocation = $this->reverseGeocode($presensiToday->lokasi_in ?? '-');

            $pdfData = [
                'headerSuratPath' => 'assets/img/header-surat.png',
                'nik' => $nik,
                'nama_lengkap' => $karyawan->nama_lengkap,
                'jabatan' => $jabatan,
                'posisi' => $karyawan->posisi,
                'unit' => $unit,
                'perusahaan' => $perusahaan,
                'tgl_wfh' => $wfh->tgl_wfh,
                'live_location' => $liveLocation,
                'keterangan' => $wfh->keterangan ?? '-',
                'laporan_deskripsi' => $request->laporan_deskripsi,
                'laporan_images' => $imagePaths,
                'hariTanggal' => $hariTanggal,
                'nama_atasan' => $atasan?->nama_lengkap ?? '-',
                'jabatan_atasan' => $atasan?->jabatan instanceof Jabatan
                    ? $atasan->jabatan->value
                    : ($atasan?->jabatan ?? '-'),
            ];

            $stempelPath = WfhService::getStempelPath();

            $pdf = Pdf::loadView('presensi.laporan-pdf', array_merge($pdfData, ['stempelPath' => $stempelPath]));
            $pdf->setPaper('A4', 'portrait');
            $pdfFilename = Str::uuid() . '-laporan-' . Str::slug($karyawan->nama_lengkap) . '.pdf';
            $pdfPath = 'wfh/laporan/' . $pdfFilename;
            Storage::disk('public')->put($pdfPath, $pdf->output());

            DB::table('wfh')->where('id', $id)->update([
                'laporan_deskripsi' => $request->laporan_deskripsi,
                'laporan_images' => json_encode($imagePaths),
                'laporan_file' => $pdfPath,
                'laporan_atasan_nik' => $laporanAtasanNik,
                'laporan_status' => $initialLaporan['laporan_status'],
                'laporan_atasan_status' => $initialLaporan['laporan_atasan_status'],
                'laporan_admin_status' => $initialLaporan['laporan_admin_status'],
            ]);

            DB::commit();

            // Notify atasan or admin (after commit)
            $wfhUpdated = DB::table('wfh')->where('id', $id)->first();
            if ($laporanAtasanNik) {
                $atasanUser = \App\Models\Karyawan::where('nik', $laporanAtasanNik)->first();
                if ($atasanUser) {
                    $atasanUser->notify(new \App\Notifications\WfhSubmitted((object)$wfhUpdated, $karyawan));
                    WfhService::sendWebPush($laporanAtasanNik, 'Laporan WFH Diajukan', $karyawan->nama_lengkap . ' mengajukan laporan WFH ' . $wfh->tgl_wfh, null, 'laporan-submitted-' . $id);
                }
            } else {
                $admins = \App\Models\User::role('administrator')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\WfhSubmitted((object)$wfhUpdated, $karyawan));
                }
            }

            return redirect('/presensi/wfh')->with('success', 'Laporan WFH berhasil dikirim! Menunggu persetujuan atasan dan administrator.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("storeLaporanWfh failed: " . $e->getMessage());
            return redirect()->back()->with("error", "Gagal mengirim laporan WFH. Silakan coba lagi.")->withInput();
        }
    }

    // =====================================================
    // APPROVAL LAPORAN ATASAN
    // =====================================================
    public function approveLaporanAtasan(Request $request, int $id)
    {
        $karyawan = Auth::guard('karyawan')->user();
        $wfh = DB::table('wfh')->where('id', $id)->first();
        if (!$wfh) return redirect()->back()->with('error', 'Data tidak ditemukan');
        if ($wfh->laporan_atasan_nik !== $karyawan->nik) return redirect()->back()->with('error', 'Anda bukan atasan untuk laporan ini');
        if ($wfh->laporan_status !== 'pending_atasan') return redirect()->back()->with('error', 'Status laporan tidak valid');

        DB::table('wfh')->where('id', $id)->update([
            'laporan_atasan_status' => 'approved',
            'laporan_status' => 'pending_admin',
        ]);

        // Notify pengaju
        $pengaju = \App\Models\Karyawan::where('nik', $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new \App\Notifications\WfhApprovedByAtasan((object)$wfh, $karyawan));
            WfhService::sendWebPush($wfh->nik, 'Laporan Disetujui Atasan', 'Laporan WFH ' . $wfh->tgl_wfh . ' disetujui atasan, menunggu persetujuan admin', null, 'laporan-approved-atasan-' . $wfh->id);
        }
        // Notify admin
        $admins = \App\Models\User::role('administrator')->get();
        foreach ($admins as $admin) $admin->notify(new \App\Notifications\WfhSubmitted((object)$wfh, $pengaju));
        cache()->forget('pending_laporan_admin_count');

        return redirect()->back()->with('success', 'Laporan disetujui atasan, diteruskan ke Admin');
    }

    public function rejectLaporanAtasan(Request $request, int $id)
    {
        $request->validate(['rejected_reason' => 'required|string|min:5|max:500']);
        $karyawan = Auth::guard('karyawan')->user();
        $wfh = DB::table('wfh')->where('id', $id)->first();
        if (!$wfh || $wfh->laporan_atasan_nik !== $karyawan->nik) return redirect()->back()->with('error', 'Akses ditolak');
        if ($wfh->laporan_status !== 'pending_atasan') return redirect()->back()->with('error', 'Status laporan tidak valid untuk penolakan');

        DB::table('wfh')->where('id', $id)->update([
            'laporan_atasan_status' => 'rejected',
            'laporan_status' => 'rejected',
            'laporan_rejected_reason' => $request->rejected_reason,
        ]);
        $pengaju = \App\Models\Karyawan::where('nik', $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new \App\Notifications\WfhRejected((object)$wfh, $request->rejected_reason));
            WfhService::sendWebPush($wfh->nik, 'Laporan Ditolak Atasan', 'Laporan WFH ' . $wfh->tgl_wfh . ' ditolak atasan: ' . $request->rejected_reason, null, 'laporan-rejected-atasan-' . $wfh->id);
        }
        cache()->forget('pending_laporan_admin_count');
        return redirect()->back()->with('success', 'Laporan ditolak atasan');
    }

    // =====================================================
    // APPROVAL LAPORAN ADMIN
    // =====================================================
    public function approveLaporanAdmin(int $id)
    {
        $user = Auth::guard('user')->user();
        $wfh = DB::table('wfh')->where('id', $id)->first();
        if (!$wfh) return redirect()->back()->with('error', 'Data tidak ditemukan');
        if ($wfh->laporan_status !== 'pending_admin') return redirect()->back()->with('error', 'Status laporan tidak valid untuk persetujuan');
        if ($wfh->laporan_admin_status !== 'pending') return redirect()->back()->with('error', 'Laporan ini sudah diproses');

        DB::table('wfh')->where('id', $id)->update([
            'laporan_admin_status' => 'approved',
            'laporan_status' => 'approved',
            'laporan_approved_at' => now(),
        ]);
        $pengaju = \App\Models\Karyawan::where('nik', $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new \App\Notifications\WfhApproved((object)$wfh));
            WfhService::sendWebPush($wfh->nik, "Laporan Disetujui Admin", "Laporan WFH " . $wfh->tgl_wfh . " telah disetujui.", null, 'laporan-approved-admin-' . $wfh->id);
        }
        cache()->forget('pending_laporan_admin_count');
        return redirect()->back()->with('success', 'Laporan disetujui Admin');
    }

    public function rejectLaporanAdmin(Request $request, int $id)
    {
        $request->validate(['rejected_reason' => 'required|string|min:5|max:500']);
        $user = Auth::guard('user')->user();
        $wfh = DB::table('wfh')->where('id', $id)->first();
        if (!$wfh) return redirect()->back()->with('error', 'Data tidak ditemukan');
        if ($wfh->laporan_status !== 'pending_admin') return redirect()->back()->with('error', 'Status laporan tidak valid untuk penolakan');
        if ($wfh->laporan_admin_status !== 'pending') return redirect()->back()->with('error', 'Laporan ini sudah diproses');

        DB::table('wfh')->where('id', $id)->update([
            'laporan_admin_status' => 'rejected',
            'laporan_status' => 'rejected',
            'laporan_rejected_reason' => $request->rejected_reason,
        ]);
        $pengaju = \App\Models\Karyawan::where('nik', $wfh->nik)->first();
        if ($pengaju) {
            $pengaju->notify(new \App\Notifications\WfhRejected((object)$wfh, $request->rejected_reason));
            WfhService::sendWebPush($wfh->nik, "Laporan Ditolak Admin", "Laporan WFH " . $wfh->tgl_wfh . " ditolak Admin", null, 'laporan-rejected-admin-' . $wfh->id);
        }
        cache()->forget('pending_laporan_admin_count');
        return redirect()->back()->with('success', 'Laporan ditolak Admin');
    }


    // =====================================================
    // ADMIN - MONITORING PRESENSI
    // =====================================================


    public function monitoring()
    {
        $unitperusahaan = DB::table('unitperusahaan')
        ->get();

        return view('presensi.monitoring', compact('unitperusahaan'));
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $nama_karyawan = $request->nama_karyawan;
        $unit = $request->unit;

        $query = DB::table('presensi')
            ->select(
                'presensi.*',
                'nama_lengkap',
                'unitperusahaan.unit as unitkerja',
                'lembur.durasi'
            )
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->join('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit')
            ->leftJoin('lembur', function ($join) {
                $join->on('presensi.nik', '=', 'lembur.nik')
                     ->on('presensi.tgl_presensi', '=', 'lembur.tgl_lembur');
            })
            ->where('tgl_presensi', $tanggal);

        if(!empty($nama_karyawan)){
            $query->where('nama_lengkap', 'like', '%' . $nama_karyawan . '%');
        }

        if(!empty($unit)){
            $query->where('unitperusahaan.unit', $unit);
        }

        $presensi = $query->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpetamasuk(Request $request)
    {
        $id = $request->id;

        $presensi = DB::table('presensi')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->where('presensi.id', $id)
            ->first();

        return view('presensi.showmapin', compact('presensi'));
    }

    public function tampilkanpetapulang(Request $request)
    {
        $id = $request->id;

        $presensi = DB::table('presensi')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->where('presensi.id', $id)
            ->first();

        return view('presensi.showmapout', compact('presensi'));
    }



    // =====================================================
    // ADMIN - LAPORAN PRESENSI
    // =====================================================

    public function laporan()
    {
        $namabulan = [
            "", "Januari", "Februari", "Maret", "April",
            "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        $unit = DB::table('unitperusahaan')
            ->orderBy('perusahaan')
            ->get();

        $karyawan = DB::table('karyawan')
            ->orderBy('nama_lengkap')
            ->get();

        return view(
            'presensi.laporanpresensi',
            compact(
                'namabulan',
                'karyawan',
                'unit'
            )
        );
    }

    public function getkaryawanbyunit(Request $request)
    {
        $unit = $request->unit;

        $karyawan = DB::table('karyawan')
            ->where('unit', $unit)
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json($karyawan);
    }

    // =====================================================
    // CETAK LAPORAN PRESENSI
    // =====================================================

    public function cetaklaporan(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $unit = $request->unit;

        // =====================================================
        // VALIDASI FILTER
        // =====================================================

        if (empty($bulan) || empty($tahun) || empty($unit) || empty($nik)) {

            return Redirect::back()->with([
                'warning' => 'Harap lengkapi seluruh filter terlebih dahulu'
            ]);

        }

        // =====================================================
        // NAMA BULAN
        // =====================================================

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

        // =====================================================
        // DATA KARYAWAN
        // =====================================================

        $karyawan = DB::table('karyawan')
            ->join('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit')
            ->select(
                'karyawan.*',
                'unitperusahaan.perusahaan'
            )
            ->where('karyawan.nik', $nik)
            ->first();

        // =====================================================
        // CEK DATA KARYAWAN
        // =====================================================

        if (!$karyawan) {

            return Redirect::back()->with([
                'warning' => 'Data karyawan tidak ditemukan'
            ]);

        }

        // =====================================================
        // DATA PRESENSI
        // =====================================================

        $presensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun)
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        // =====================================================
        // HITUNG TOTAL JAM KERJA
        // =====================================================

        $totalMenitKerja = 0;

        foreach ($presensi as $p) {

            if ($p->jam_out != null) {

                $awal = strtotime($p->jam_in);
                $akhir = strtotime($p->jam_out);

                $selisih = ($akhir - $awal) / 60;

                $totalMenitKerja += $selisih;
            }
        }

        $totalJamKerja = floor($totalMenitKerja / 60);
        $sisaMenitKerja = $totalMenitKerja % 60;

        // =====================================================
        // DATA LEMBUR
        // =====================================================

        $lembur = DB::table('lembur')
            ->where('nik', $nik)
            ->whereMonth('tgl_lembur', $bulan)
            ->whereYear('tgl_lembur', $tahun)
            ->get()
            ->keyBy('tgl_lembur');

        // =====================================================
        // HITUNG TOTAL LEMBUR & PRORATE
        // =====================================================

        $totalLembur = 0;
        $totalProrate = 0;

        foreach ($lembur as $item) {

            if ($item->durasi == 'Prorate') {

                $totalProrate++;

            } else {

                $angka = (float) $item->durasi;

                $totalLembur += $angka;

            }

        }

        // =====================================================
        // DATA WFH
        // =====================================================

        $wfh = DB::table('wfh')
            ->where('nik', $nik)
            ->where('status', 'approved')
            ->whereMonth('tgl_wfh', $bulan)
            ->whereYear('tgl_wfh', $tahun)
            ->get()
            ->keyBy('tgl_wfh');

        // =====================================================
        // HITUNG TOTAL WFH (hanya yang approved)
        // =====================================================

        $totalWfh = $wfh->count();

        // =====================================================
        // CEK DATA PRESENSI
        // =====================================================

        if ($presensi->isEmpty()) {

            return Redirect::back()->with([
                'warning' => 'Data presensi tidak ditemukan'
            ]);

        }

        // =====================================================
        // GENERATE PDF
        // =====================================================

        $pdf = Pdf::loadView(
            'presensi.cetaklaporan',
            compact(
                'bulan',
                'tahun',
                'namabulan',
                'karyawan',
                'presensi',
                'lembur',
                'wfh',
                'totalLembur',
                'totalProrate',
                'totalWfh',
                'sisaMenitKerja',
                'totalJamKerja'
            )
        );

        // =====================================================
        // DOWNLOAD PDF
        // =====================================================

        return $pdf->download(
            'Laporan_Presensi_' . $karyawan->nama_lengkap . '.pdf'
        );
    }



    // =====================================================
    // ADMIN - DATA IZIN
    // =====================================================

    public function dataizin(Request $request)
    {
        $nama_karyawan = $request->nama_karyawan;
        $unit = $request->unit;
        $tanggal = $request->tanggal;
        $jenis_izin = $request->jenis_izin;

        $query = DB::table('izin')
            ->join('karyawan', 'izin.nik', '=', 'karyawan.nik')
            ->join('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit');

        if (!empty($nama_karyawan)) {
            $query->where(
                'karyawan.nama_lengkap',
                'like',
                '%' . $nama_karyawan . '%'
            );
        }

        if (!empty($unit)) {
            $query->where(
                'unitperusahaan.unit',
                $unit
            );
        }

        if (!empty($jenis_izin)) {
            $query->where(
                'izin.jenis_izin',
                $jenis_izin
            );
        }

        if (!empty($tanggal)) {
            $query->where(
                'izin.tgl_izin',
                $tanggal
            );
        }

        $dataizin = $query
        ->orderBy('tgl_izin', 'desc')
        ->paginate(5);

        $unitperusahaan = DB::table('unitperusahaan')->get();

        return view(
            'presensi.dataizin',
            compact('dataizin', 'unitperusahaan')
        );
    }

    public function deleteizinadmin(int $id)
    {
        $izin = DB::table('izin')
            ->where('id', $id)
            ->first();

        if (!$izin) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }

        Storage::delete('public/uploads/izin/' . $izin->file);

        DB::table('izin')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Data izin berhasil dihapus!');
    }



    // =====================================================
    // ADMIN - DATA LEMBUR
    // =====================================================

    public function datalembur(Request $request)
    {
        $nama_karyawan = $request->nama_karyawan;
        $unit = $request->unit;
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $query = DB::table('lembur')
            ->join('karyawan', 'lembur.nik', '=', 'karyawan.nik')
            ->join('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit');

        if (!empty($nama_karyawan)) {
            $query->where(
                'karyawan.nama_lengkap',
                'like',
                '%' . $nama_karyawan . '%'
            );
        }

        if (!empty($unit)) {
            $query->where(
                'unitperusahaan.unit',
                $unit
            );
        }

        if (!empty($tanggal)) {
            $query->where(
                'lembur.tgl_lembur',
                $tanggal
            );
        }

        $datalembur = $query
            ->orderBy('tgl_lembur', 'desc')
            ->paginate(5);

        $unitperusahaan = DB::table('unitperusahaan')->get();

        return view(
            'presensi.datalembur',
            compact('datalembur', 'unitperusahaan')
        );
    }

    // =====================================================
    // ADMIN - HAPUS DATA LEMBUR
    // =====================================================

    public function deletelemburadmin(int $id)
    {
        $lembur = DB::table('lembur')
            ->where('id', $id)
            ->first();

        if ($lembur) {

            Storage::delete('public/uploads/lembur/' . $lembur->file_form);

            Storage::delete('public/uploads/lembur/' . $lembur->file_laporan);

            DB::table('lembur')
                ->where('id', $id)
                ->delete();
        }

        return redirect()->back()->with('success', 'Data lembur berhasil dihapus!');
    }


    // =====================================================
    // ADMIN - DATA WFH
    // =====================================================

    public function datawfh(Request $request)
    {
        $nama_karyawan = $request->nama_karyawan;
        $unit = $request->unit;
        $tanggal = $request->tanggal;
        $status = $request->status;

        $query = DB::table('wfh')
            ->join('karyawan', 'wfh.nik', '=', 'karyawan.nik')
            ->join('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit')
            ->leftJoin('karyawan as atasan', 'wfh.atasan_nik', '=', 'atasan.nik')
            ->select('wfh.*', 'karyawan.nama_lengkap', 'karyawan.jabatan', 'karyawan.posisi', 'karyawan.unit', 'unitperusahaan.perusahaan', 'atasan.nama_lengkap as atasan_nama', 'atasan.jabatan as atasan_jabatan');

        if (!empty($nama_karyawan)) {
            $query->where('karyawan.nama_lengkap', 'like', '%' . $nama_karyawan . '%');
        }
        if (!empty($unit)) {
            $query->where('unitperusahaan.unit', $unit);
        }
        if (!empty($tanggal)) {
            $query->where('wfh.tgl_wfh', $tanggal);
        }
        if (!empty($status)) {
            $query->where('wfh.status', $status);
        }

        $datawfh = $query->orderBy('wfh.tgl_wfh', 'desc')->paginate(5)->withQueryString();
        $unitperusahaan = DB::table('unitperusahaan')->get();

        $pendingWfhAdmin = DB::table('wfh')->where('status', 'pending_admin')->count();
        $pendingLaporanAdmin = DB::table('wfh')->where('laporan_status', 'pending_admin')->count();

        return view('presensi.datawfh', compact('datawfh', 'unitperusahaan', 'pendingWfhAdmin', 'pendingLaporanAdmin'));
    }

    // =====================================================
    // ADMIN - HAPUS DATA WFH
    // =====================================================

    public function deletewfhadmin(int $id)
    {
        $wfh = DB::table('wfh')->where('id', $id)->first();
        if (!$wfh) return redirect()->back()->with('error', 'Data tidak ditemukan');

        WfhService::deleteWfhFiles($wfh);
        DB::table('wfh')->where('id', $id)->delete();
        cache()->forget('pending_wfh_count');
        cache()->forget('pending_wfh_admin_count');
        cache()->forget('pending_laporan_admin_count');
        return redirect()->back()->with('success', 'Data WFH berhasil dihapus!');
    }
}
