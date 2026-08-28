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
            'perusahaan'
        );

        $query->join(
            'unitperusahaan',
            'karyawan.unit',
            '=',
            'unitperusahaan.unit'
        );

        $query->orderBy('nama_lengkap');

        if (!empty($request->nama_karyawan)) {
            $query->where(
                'nama_lengkap',
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
            'jabatan' => 'required',
            'unit' => 'required',
            'no_hp' => 'required',
        ]);

        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $unit = $request->unit;
        $jabatan = $request->jabatan;
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
                'jabatan' => $jabatan,
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
            'password' => 'nullable|min:5'
        ]);

        $nama_lengkap = $request->nama_lengkap;
        $unit = $request->unit;
        $jabatan = $request->jabatan;
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
                'jabatan' => $jabatan,
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