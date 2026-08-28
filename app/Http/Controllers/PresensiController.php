<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;

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
    // WFH KARYAWAN
    // =====================================================
    
    public function wfh()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
    
        $datawfh = DB::table('wfh')
            ->where('nik', $nik)
            ->orderBy('tgl_wfh', 'desc')
            ->get();
    
        return view('presensi.wfh', compact('datawfh'));
    }
    
    public function buatwfh()
    {
        return view('presensi.buatwfh');
    }
    
    // =====================================================
    // TAMPILKAN FILE FORM WFH
    // =====================================================
    
    public function showfilewfh(string $file)
    {
        $path = storage_path('app/public/uploads/wfh/' . $file);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->file($path);
    }

    
    // =====================================================
    // SIMPAN DATA WFH
    // =====================================================
    
    public function storewfh(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
    
        // VALIDASI
        $request->validate([
            'tgl_wfh'       => 'required|date',
            'file_form'     => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
            'file_laporan'  => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096'
        ]);
    
        // CEK APAKAH SUDAH ADA WFH DI TANGGAL TERSEBUT
        $cek = DB::table('wfh')
            ->where('nik', $nik)
            ->where('tgl_wfh', $request->tgl_wfh)
            ->count();
    
        if ($cek > 0) {
    
            return redirect()->back()->with(
                'error',
                'Anda sudah mengirim data WFH pada tanggal tersebut!'
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
            $form->storeAs('public/uploads/wfh', $nama_form);
            $laporan->storeAs('public/uploads/wfh', $nama_laporan);
    
            // SIMPAN DATABASE
            DB::table('wfh')->insert([
                'nik'              => $nik,
                'tgl_wfh'          => $request->tgl_wfh,
                'file_form'        => $nama_form,
                'file_laporan'     => $nama_laporan,
                'dikirim_tanggal'  => now()
            ]);
    
            return redirect('/presensi/wfh')
                ->with('success', 'Data WFH berhasil dikirim!');
    
        } else {
    
            return redirect()->back()
                ->with('error', 'Data gagal dikirim!');
    
        }
    }
    
    // =====================================================
    // HAPUS DATA WFH
    // =====================================================
    
    public function deletewfh(int $id)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
    
        $wfh = DB::table('wfh')
            ->where('id', $id)
            ->where('nik', $nik)
            ->first();
    
        if (!$wfh) {
    
            return redirect()->back()->with(
                'error',
                'Data tidak ditemukan!'
            );
    
        }
    
        // HAPUS FILE FORM
        Storage::delete('public/uploads/wfh/' . $wfh->file_form);
    
        // HAPUS FILE LAPORAN
        Storage::delete('public/uploads/wfh/' . $wfh->file_laporan);
    
        // HAPUS DATA DATABASE
        DB::table('wfh')
            ->where('id', $id)
            ->delete();
    
        return redirect()->back()->with(
            'success',
            'Data WFH berhasil dihapus!'
        );
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
            ->whereMonth('tgl_wfh', $bulan)
            ->whereYear('tgl_wfh', $tahun)
            ->get()
            ->keyBy('tgl_wfh');
    
        // =====================================================
        // HITUNG TOTAL WFH
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
        $tanggal = $request->tanggal ?? date('Y-m-d');
    
        $query = DB::table('wfh')
            ->join('karyawan', 'wfh.nik', '=', 'karyawan.nik')
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
                'wfh.tgl_wfh',
                $tanggal
            );
        }
    
        $datawfh = $query
            ->orderBy('tgl_wfh', 'desc')
            ->paginate(5);
    
        $unitperusahaan = DB::table('unitperusahaan')->get();
    
        return view(
            'presensi.datawfh',
            compact('datawfh', 'unitperusahaan')
        );
    }
    
    // =====================================================
    // ADMIN - HAPUS DATA WFH
    // =====================================================
    
    public function deletewfhadmin(int $id)
    {
        $wfh = DB::table('wfh')
            ->where('id', $id)
            ->first();
    
        if ($wfh) {
    
            // HAPUS FILE FORM
            Storage::delete('public/uploads/wfh/' . $wfh->file_form);
    
            // HAPUS FILE LAPORAN
            Storage::delete('public/uploads/wfh/' . $wfh->file_laporan);
    
            // HAPUS DATABASE
            DB::table('wfh')
                ->where('id', $id)
                ->delete();
        }
    
        return redirect()->back()->with(
            'success',
            'Data WFH berhasil dihapus!'
        );
    }
}