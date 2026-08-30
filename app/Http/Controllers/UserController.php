<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{

    // =====================================================
    // DATA USER / ADMIN
    // =====================================================

    // TAMPILKAN DATA USER
    public function index()
    {
        $unitperusahaan = DB::table('unitperusahaan')
            ->orderBy('unit')
            ->get();

        $role = DB::table('roles')
            ->orderBy('id')
            ->get();

        $users = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.unit',
                'unitperusahaan.perusahaan',
                'roles.name as role'
            )
            ->join('unitperusahaan', 'users.unit', '=', 'unitperusahaan.unit')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->get();

        return view(
            'users.index',
            compact('users', 'unitperusahaan', 'role')
        );
    }



    // =====================================================
    // TAMBAH USER / ADMIN
    // =====================================================

    // SIMPAN DATA USER
    public function store(Request $request)
    {
        $request->validate([
            'nama_user' => 'required',
            'email' => 'required|email|unique:users,email',
            'unit' => 'required|exists:unitperusahaan,unit',
            'role' => 'required',
            'password' => 'required|min:6',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $request->nama_user,
                'email' => $request->email,
                'unit' => $request->unit,
                'password' => $request->password, // 'hashed' cast handles hashing
            ]);

            $user->assignRole($request->role);

            DB::commit();

            return Redirect::back()->with([
                'success' => 'Data User / Admin Berhasil Disimpan'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return Redirect::back()->with([
                'warning' => 'Data User / Admin Gagal Disimpan'
            ]);

        }
    }



    // =====================================================
    // EDIT USER / ADMIN
    // =====================================================

    // FORM EDIT USER
    public function edit(Request $request)
    {
        $id_user = $request->id_user;

        $unitperusahaan = DB::table('unitperusahaan')
            ->orderBy('unit')
            ->get();

        $role = DB::table('roles')
            ->orderBy('id')
            ->get();

        $user = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('id', $id_user)
            ->first();

        return view(
            'users.edituser',
            compact('unitperusahaan', 'role', 'user')
        );
    }

    // UPDATE DATA USER
    public function update(Request $request, int $id_user)
    {
        $request->validate([
            'password' => 'nullable|min:6'
        ], [
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $nama_user = $request->nama_user;
        $email = $request->email;
        $unit = $request->unit;
        $role = $request->role;

        $data = [
            'name' => $nama_user,
            'email' => $email,
            'unit' => $unit
        ];

        // Kalau password diisi
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        DB::beginTransaction();

        try {

            DB::table('users')
                ->where('id', $id_user)
                ->update($data);

            DB::table('model_has_roles')
                ->where('model_id', $id_user)
                ->update([
                    'role_id' => $role
                ]);

            DB::commit();

            return Redirect::back()->with([
                'success' => 'Data User / Admin Berhasil Diperbarui'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return Redirect::back()->with([
                'warning' => 'Data User / Admin Gagal Diperbarui'
            ]);

        }
    }



    // =====================================================
    // RESET PASSWORD USER
    // =====================================================

    public function resetpassword(int $id)
    {
        DB::table('users')
            ->where('id', $id)
            ->update([
                'password' => Hash::make('12345678')
            ]);

        return Redirect::back()->with([
            'success' => 'Password berhasil direset'
        ]);
    }



    // =====================================================
    // HAPUS USER / ADMIN
    // =====================================================

    public function delete(int $id_user)
    {
        try {

            DB::table('model_has_roles')
                ->where('model_id', $id_user)
                ->delete();

            DB::table('users')
                ->where('id', $id_user)
                ->delete();

            return Redirect::back()->with([
                'success' => 'Data User / Admin Berhasil Dihapus'
            ]);

        } catch (\Exception $e) {

            return Redirect::back()->with([
                'warning' => 'Data User / Admin Gagal Dihapus'
            ]);

        }
    }

}