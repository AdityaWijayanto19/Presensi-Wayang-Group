<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{

    // =====================================================
    // DATA KARYAWAN
    // =====================================================

    // TAMPILKAN DATA KARYAWAN
    public function index(Request $request)
    {
        $query = Karyawan::query();

        $query->select(
            'karyawan.*',
            'perusahaan',
            'atasan.nama_lengkap as atasan_nama',
            'atasan.jabatan as atasan_jabatan'
        );

        $query->join(
            'unitperusahaan',
            'karyawan.unit',
            '=',
            'unitperusahaan.unit'
        );

        $query->leftJoin('karyawan as atasan', 'karyawan.atasan_nik', '=', 'atasan.nik');

        $query->orderBy('karyawan.nama_lengkap');

        if (!empty($request->nama_karyawan)) {
            $query->where('karyawan.nama_lengkap',
                'like',
                '%' . $request->nama_karyawan . '%'
            );
        }

        if (!empty($request->unit)) {
            $query->where(
                'karyawan.unit',
                $request->unit
            );
        }

        if (!empty($request->jabatan_filter)) {
            $query->where('karyawan.jabatan', $request->jabatan_filter);
        }

        $karyawan = $query
            ->paginate(5)
            ->withQueryString();

        $unitperusahaan = DB::table('unitperusahaan')->get();

        return view(
            'karyawan.index',
            compact(
                'karyawan',
                'unitperusahaan'
            )
        );
    }



    // =====================================================
    // TAMBAH KARYAWAN
    // =====================================================

    // SIMPAN DATA KARYAWAN
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:karyawan,nik',
            'nama_lengkap' => 'required',
            'jabatan' => 'required|in:Intern,Staff,SPV,Manager,GM,Direktur', // UI Jabatan dropdown -> DB jabatan (hierarchy)
            'posisi' => 'required', // UI Posisi text -> DB posisi (job title)
            'role_approved' => 'nullable|in:Staff,Manager,GM,Direktur',
            'atasan_nik' => 'nullable|exists:karyawan,nik',
            'unit' => 'required',
            'no_hp' => 'required',
        ]);

        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $unit = $request->unit;
        $jabatan = $request->jabatan;  // hierarchy level
        $posisi = $request->posisi;    // job title
        $roleApproved = $request->role_approved ?: null;
        $atasan_nik = $request->atasan_nik ?: null;
        // Direktur tidak punya atasan
        if ($roleApproved === 'Direktur') {
            $atasan_nik = null;
        }
        // Cegah self-reference
        if ($atasan_nik === $nik) {
            $atasan_nik = null;
        }
        $no_hp = $request->no_hp;
        $password = Hash::make('12345');

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = 'nophoto.png';
        }

        try {

            $data = [
                'nik' => $nik,
                'nama_lengkap' => $nama_lengkap,
                'unit' => $unit,
                'jabatan' => $jabatan,   // hierarchy level
                'posisi' => $posisi,     // job title
                'role_approved' => $roleApproved,
                'atasan_nik' => $atasan_nik,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ];

            $simpan = DB::table('karyawan')->insert($data);

            if ($simpan) {

                if ($request->hasFile('foto')) {

                    $request->file('foto')->move(
                        public_path('storage/uploads/karyawan'),
                        $foto
                    );
                }

                return Redirect::back()->with([
                    'success' => 'Data karyawan berhasil disimpan!'
                ]);
            }

        } catch (\Exception $e) {

            return Redirect::back()->with([
                'error' => 'Data karyawan gagal disimpan!'
            ]);
        }
    }



    // =====================================================
    // EDIT KARYAWAN
    // =====================================================

    // FORM EDIT KARYAWAN
    public function edit(Request $request)
    {
        $nik = $request->nik;
        $page = $request->page;

        $unitperusahaan = DB::table('unitperusahaan')->get();

        $karyawan = DB::table('karyawan')
            ->where('nik', $nik)
            ->first();

        return view(
            'karyawan.edit',
            compact(
                'unitperusahaan',
                'karyawan',
                'page'
            )
        );
    }

    // UPDATE DATA KARYAWAN
    public function update(string $nik, Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|min:5',
            'jabatan' => 'required|in:Intern,Staff,SPV,Manager,GM,Direktur', // UI Jabatan -> DB jabatan (hierarchy)
            'posisi' => 'required', // UI Posisi -> DB posisi (job title)
            'role_approved' => 'nullable|in:Staff,Manager,GM,Direktur',
            'atasan_nik' => 'nullable|exists:karyawan,nik',
        ]);

        $nama_lengkap = $request->nama_lengkap;
        $unit = $request->unit;
        $jabatan = $request->jabatan;  // hierarchy level
        $posisi = $request->posisi;    // job title
        $roleApproved = $request->role_approved ?: null;
        $atasan_nik = $request->atasan_nik ?: null;
        if ($roleApproved === 'Direktur') {
            $atasan_nik = null;
        }
        // cegah atasan diri sendiri
        if ($atasan_nik === $nik) {
            $atasan_nik = null;
        }
        $no_hp = $request->no_hp;
        $foto_lama = $request->foto_lama;

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $foto_lama;
        }

        try {

            $data = [
                'nama_lengkap' => $nama_lengkap,
                'unit' => $unit,
                'jabatan' => $jabatan,   // hierarchy level
                'posisi' => $posisi,     // job title
                'role_approved' => $roleApproved,
                'atasan_nik' => $atasan_nik,
                'no_hp' => $no_hp,
                'foto' => $foto,
            ];

            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            DB::table('karyawan')
                ->where('nik', $nik)
                ->update($data);

            if ($request->hasFile('foto')) {

                if ($foto_lama != 'nophoto.png') {
                    @unlink(
                        public_path(
                            'storage/uploads/karyawan/' . $foto_lama
                        )
                    );
                }

                $request->file('foto')->move(
                    public_path('storage/uploads/karyawan'),
                    $foto
                );
            }

            return Redirect::to('/karyawan?page=' . $request->page)
                ->with([
                    'success' => 'Data karyawan berhasil diperbarui!'
                ]);

        } catch (\Exception $e) {

            return Redirect::back()->with([
                'error' => 'Data karyawan gagal diperbarui!'
            ]);
        }
    }



    // =====================================================
    // RESET PASSWORD
    // =====================================================

    public function resetpassword(string $nik)
    {
        try {

            DB::table('karyawan')
                ->where('nik', $nik)
                ->update([
                    'password' => Hash::make('12345')
                ]);

            return Redirect::back()->with([
                'success' => 'Password berhasil direset menjadi 12345'
            ]);

        } catch (\Exception $e) {

            return Redirect::back()->with([
                'error' => 'Password gagal direset'
            ]);
        }
    }



    // =====================================================
    // HAPUS KARYAWAN
    // =====================================================

    public function getAtasan(Request $request)
    {
        $roleApproved = $request->role_approved;
        if (!$roleApproved) {
            return response()->json([]);
        }
        // Role Approved -> ambil atasan level DI ATASNYA
        // Staff -> Manager, Manager -> GM, GM -> Direktur, Direktur -> null
        $atasanMap = [
            'Staff' => 'Manager',
            'Manager' => 'GM',
            'GM' => 'Direktur',
            'Direktur' => null,
        ];
        $targetPosisi = $atasanMap[$roleApproved] ?? null;
        if (!$targetPosisi) {
            return response()->json([]);
        }
        // exclude self if editing
        $excludeNik = $request->exclude_nik;
        $query = DB::table('karyawan')->where('role_approved', $targetPosisi)->select('nik','nama_lengkap','jabatan','posisi')->orderBy('karyawan.nama_lengkap');
        if ($excludeNik) {
            $query->where('nik', '!=', $excludeNik);
        }
        return response()->json($query->get());
    }

    public function delete(string $nik)
    {
        $karyawan = DB::table('karyawan')
            ->where('nik', $nik)
            ->first();

        if (!$karyawan) {
            return Redirect::back()->with([
                'error' => 'Data karyawan tidak ditemukan!'
            ]);
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Hapus Foto Profil
            |--------------------------------------------------------------------------
            */

            if ($karyawan->foto != 'nophoto.png') {
                Storage::delete('public/uploads/karyawan/' . $karyawan->foto);
            }

            /*
            |--------------------------------------------------------------------------
            | Hapus Foto Presensi
            |--------------------------------------------------------------------------
            */

            $presensi = DB::table('presensi')
                ->where('nik', $nik)
                ->get();

            foreach ($presensi as $p) {

                if (!empty($p->foto_in)) {
                    Storage::delete('public/uploads/absensi/' . $p->foto_in);
                }

                if (!empty($p->foto_out)) {
                    Storage::delete('public/uploads/absensi/' . $p->foto_out);
                }
            }

            DB::table('presensi')
                ->where('nik', $nik)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Hapus Dokumen Izin
            |--------------------------------------------------------------------------
            */

            $izin = DB::table('izin')
                ->where('nik', $nik)
                ->get();

            foreach ($izin as $i) {

                if (!empty($i->file)) {
                    Storage::delete('public/uploads/izin/' . $i->file);
                }
            }

            DB::table('izin')
                ->where('nik', $nik)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Hapus Dokumen Lembur
            |--------------------------------------------------------------------------
            */

            $lembur = DB::table('lembur')
                ->where('nik', $nik)
                ->get();

            foreach ($lembur as $l) {

                if (!empty($l->file_form)) {
                    Storage::delete('public/uploads/lembur/' . $l->file_form);
                }

                if (!empty($l->file_laporan)) {
                    Storage::delete('public/uploads/lembur/' . $l->file_laporan);
                }
            }

            DB::table('lembur')
                ->where('nik', $nik)
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Hapus Dokumen WFH milik karyawan ini saja
            |--------------------------------------------------------------------------
            */

            $wfhOwn = DB::table('wfh')
                ->where('nik', $nik)
                ->get();

            foreach ($wfhOwn as $w) {
                if (!empty($w->pdf_form_path)) {
                    Storage::disk('public')->delete($w->pdf_form_path);
                }
                if (!empty($w->laporan_file)) {
                    Storage::disk('public')->delete($w->laporan_file);
                }
            }

            // hapus wfh milik karyawan
            DB::table('wfh')->where('nik', $nik)->delete();
            // null-kan atasan yang dipegang karyawan ini
            DB::table('karyawan')->where('atasan_nik', $nik)->update(['atasan_nik' => null]);
            // null-kan atasan_nik di wfh yang menunggu approval dari karyawan ini + update laporan status
            DB::table('wfh')->where('atasan_nik', $nik)->update([
                'atasan_nik' => null,
                'status' => 'pending_admin',
                'atasan_status' => 'pending',
            ]);
            // update laporan yang menunggu approval atasan ini
            DB::table('wfh')->where('laporan_atasan_nik', $nik)->where('laporan_status', 'pending_atasan')->update([
                'laporan_atasan_nik' => null,
                'laporan_status' => 'pending_admin',
                'laporan_atasan_status' => 'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Hapus Data Karyawan
            |--------------------------------------------------------------------------
            */

            DB::table('karyawan')
                ->where('nik', $nik)
                ->delete();

            DB::commit();

            return Redirect::back()->with([
                'success' => 'Data karyawan dan riwayatnya berhasil dihapus!'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return Redirect::back()->with([
                'error' => 'Data karyawan gagal dihapus!'
            ]);
        }
    }

}