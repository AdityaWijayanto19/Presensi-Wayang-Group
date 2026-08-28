<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UnitperusahaanController extends Controller
{

    // =====================================================
    // DATA UNIT PERUSAHAAN
    // =====================================================

    // TAMPILKAN DATA UNIT
    public function index()
    {
        $unitperusahaan = DB::table('unitperusahaan')
            ->orderBy('unit')
            ->get();

        return view(
            'unitperusahaan.index',
            compact('unitperusahaan')
        );
    }



    // =====================================================
    // TAMBAH UNIT PERUSAHAAN
    // =====================================================

    // SIMPAN DATA UNIT
    public function store(Request $request)
    {
        $unit = $request->unit;
        $perusahaan = $request->perusahaan;

        $u = [
            'unit' => $unit,
            'perusahaan' => $perusahaan,
            'jam_masuk' => $request->jam_masuk
        ];

        $simpan = DB::table('unitperusahaan')
            ->insert($u);

        if ($simpan) {

            return Redirect::back()->with([
                'success' => 'Data Berhasil Disimpan!'
            ]);

        } else {

            return Redirect::back()->with([
                'error' => 'Data Gagal Disimpan!'
            ]);

        }
    }



    // =====================================================
    // EDIT UNIT PERUSAHAAN
    // =====================================================

    // FORM EDIT UNIT
    public function edit(Request $request)
    {
        $unit = $request->unit;

        $unitperusahaan = DB::table('unitperusahaan')
            ->where('unit', $unit)
            ->first();

        return view(
            'unitperusahaan.edit',
            compact('unitperusahaan')
        );
    }

    // UPDATE DATA UNIT
    public function update(string $unit, Request $request)
    {
        try {

            $u = [
                'unit' => $request->unit,
                'perusahaan' => $request->perusahaan,
                'jam_masuk' => $request->jam_masuk
            ];

            DB::table('unitperusahaan')
                ->where('unit', $unit)
                ->update($u);

            return Redirect::back()->with([
                'success' => 'Data Berhasil Diperbarui!'
            ]);

        } catch (\Exception $e) {

            return Redirect::back()->with([
                'error' => 'Data Gagal Diperbarui!'
            ]);

        }
    }



    // =====================================================
    // HAPUS UNIT PERUSAHAAN
    // =====================================================

    public function delete(string $unit)
    {
        $hapus = DB::table('unitperusahaan')
            ->where('unit', $unit)
            ->delete();

        if ($hapus) {

            return Redirect::back()->with([
                'success' => 'Data Berhasil Dihapus!'
            ]);

        } else {

            return Redirect::back()->with([
                'error' => 'Data Gagal Dihapus!'
            ]);

        }
    }

}