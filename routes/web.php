<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UnitperusahaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Universal
Route::get('/presensi/showfile/{file}', [PresensiController::class, 'showfile']);

Route::get('/presensi/showfilelembur/{file}', [PresensiController::class, 'showfilelembur']);

// showfilewfh dipindahkan ke dalam group auth:karyawan di bawah



// Route::get('/createrolepermission', function (){
//     try {
//         Role::create(['name' => 'admin 2']);
//         // Permission::create(['name' => 'view-karyawan']);
//         // Permission::create(['name' => 'view-unitperusahaan']);
//         echo "Sukses";

//     } catch (\Exception $e) {
//         echo "Error";
//     }
// });


// Route::get('/give-user-role', function () {
//     try {
//         $user = User::findOrFail(1);
//         $user->assignRole('administrator');

//         echo "Sukses";
//     } catch (Exception $e) {
//         echo "Error";
//     }
// });

// Route::get('/give-role-permission', function () {
//     try {
//         $role = Role::findorfail(1);
//         $role->givePermissionTo('view-unitperusahaan');
//         echo "Sukses";
//     } catch (\Exception $e) {
//         //throw $th;
//         echo "Error";
//     }
// });


//admin

Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

Route::group(['middleware' => ['auth:user', 'role:administrator,user']], function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    //user admin
    Route::get('/panel/users', [UserController::class, 'index']);
    Route::post('/users/store', [UserController::class, 'store']);
    Route::post('/users/edit', [UserController::class, 'edit']);
    Route::post('/users/{id}/resetpassword', [UserController::class, 'resetpassword']);
    Route::post('/users/{id_user}/update', [UserController::class, 'update']);
    Route::post('/users/{id_user}/delete', [UserController::class, 'delete']);

    //unit perusahaan
    Route::get('/unitperusahaan', [UnitperusahaanController::class, 'index']);
    // Route::get('/unitperusahaan', [UnitperusahaanController::class,'index'])->middleware('permission:view-unitperusahaan,user');
    Route::post('/unitperusahaan/store', [UnitperusahaanController::class, 'store']);
    Route::post('/unitperusahaan/edit', [UnitperusahaanController::class, 'edit']);
    Route::post('/unitperusahaan/{unit}/update', [UnitperusahaanController::class, 'update']);
    Route::post('/unitperusahaan/{unit}/delete', [UnitperusahaanController::class, 'delete']);

    //karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    // Route::get('/karyawan', [KaryawanController::class, 'index'])->middleware('permission:view-karyawan,user');
    Route::get('/karyawan/get-atasan', [KaryawanController::class, 'getAtasan']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update']);
    Route::post('/karyawan/{nik}/resetpassword', [KaryawanController::class, 'resetpassword']);
    Route::post('/karyawan/{nik}/delete', [KaryawanController::class, 'delete']);

    //monitoring presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::post('/tampilkanpetamasuk', [PresensiController::class, 'tampilkanpetamasuk']);
    Route::post('/tampilkanpetapulang', [PresensiController::class, 'tampilkanpetapulang']);

    //presensi dan rekapitulasi
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::post('/getkaryawanbyunit', [PresensiController::class, 'getkaryawanbyunit']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);

    //data izin karyawan
    Route::get('/presensi/dataizin', [PresensiController::class, 'dataizin']);
    // Route::post('/presensi/dataizin/{id}/delete', [PresensiController::class, 'deleteizin']); -> ceritanya 1 function untuk dua duanya
    Route::post('/presensi/dataizin/{id}/delete', [PresensiController::class, 'deleteizinadmin']);

    //data lembur karyawan
    Route::get('/presensi/datalembur', [PresensiController::class, 'datalembur']);
    Route::post('/presensi/datalembur/{id}/delete', [PresensiController::class, 'deletelemburadmin']);

    //data WFH karyawan
    Route::get('/presensi/datawfh', [PresensiController::class, 'datawfh']);
    Route::post('/presensi/datawfh/{id}/delete', [PresensiController::class, 'deletewfhadmin']);
    Route::post('/presensi/datawfh/{id}/approve', [PresensiController::class, 'approveWfhAdmin']);
    Route::post('/presensi/datawfh/{id}/reject', [PresensiController::class, 'rejectWfhAdmin']);
    Route::post('/presensi/datawfh/{id}/approve-laporan-admin', [PresensiController::class, 'approveLaporanAdmin']);
    Route::post('/presensi/datawfh/{id}/reject-laporan-admin', [PresensiController::class, 'rejectLaporanAdmin']);

    // Admin realtime data (hanya admin)
    Route::get('/api/realtime/admin', function () {
        $pendingWfh = DB::table('wfh')->where('status', 'pending_admin')->count();
        $pendingLaporan = DB::table('wfh')->where('laporan_status', 'pending_admin')->count();
        $totalPending = $pendingWfh + $pendingLaporan;
        return response()->json([
            'pending_wfh' => $pendingWfh,
            'pending_laporan' => $pendingLaporan,
            'total_pending' => $totalPending,
        ]);
    });

    // Admin realtime: cek update data wfh
    Route::get('/api/realtime/admin/wfh-check', function () {
        $lastId = request('last_id', 0);
        $newCount = DB::table('wfh')->where('id', '>', $lastId)->count();
        $lastCheck = request('last_check', now()->subSeconds(10));
        $updatedCount = DB::table('wfh')->where('dikirim_tanggal', '>', $lastCheck)->count();
        return response()->json([
            'new_data' => $newCount > 0,
            'updated_data' => $updatedCount > 0,
            'latest_id' => DB::table('wfh')->max('id'),
        ]);
    });

    // Admin realtime: fetch data wfh terbaru (JSON) untuk AJAX table update
    Route::get('/api/realtime/admin/wfh-data', function () {
        $nama_karyawan = request('nama_karyawan');
        $unit = request('unit');
        $tanggal = request('tanggal');
        $status = request('status');
        $page = request('page', 1);

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

        $html = view('presensi.datawfh-rows', compact('datawfh'))->render();
        $pagination = $datawfh->setPath('/presensi/datawfh')->appends(request()->query())->links('vendor.pagination.bootstrap-5')->render();

        return response()->json([
            'html' => $html,
            'pagination' => $pagination,
            'total' => $datawfh->total(),
        ]);
    });

    // Admin settings & permissions
    Route::get('/admin/settings/permissions', [UserPermissionController::class, 'adminSettings']);
    Route::get('/api/admin/permissions', [UserPermissionController::class, 'adminGetPermissions']);
    Route::post('/api/admin/permissions/toggle', [UserPermissionController::class, 'adminTogglePermission']);
});


//karyawan

Route::middleware(['guest:karyawan'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    //route presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);

    //route histori
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

    //route izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::delete('/presensi/deleteizin/{id}', [PresensiController::class, 'deleteizin']);

    //route lembur
    Route::get('/presensi/lembur', [PresensiController::class, 'lembur']);
    Route::get('/presensi/buatlembur', [PresensiController::class, 'buatlembur']);
    Route::post('/presensi/storelembur', [PresensiController::class, 'storelembur']);
    Route::delete('/presensi/deletelembur/{id}', [PresensiController::class, 'deletelembur']);

    //show file wfh (auth required)
    Route::get('/presensi/showfilewfh/{file}', [PresensiController::class, 'showfilewfh']);

    //route WFH
    Route::get('/presensi/wfh', [PresensiController::class, 'wfh']);
    Route::get('/presensi/buatwfh', [PresensiController::class, 'buatwfh']);
    Route::post('/presensi/storewfh', [PresensiController::class, 'storewfh']);
    Route::delete('/presensi/deletewfh/{id}', [PresensiController::class, 'deletewfh']);
    Route::post('/presensi/wfh/{id}/approve-atasan', [PresensiController::class, 'approveWfhAtasan']);
    Route::post('/presensi/wfh/{id}/reject-atasan', [PresensiController::class, 'rejectWfhAtasan']);
    Route::get('/presensi/wfh/{id}/laporan', [PresensiController::class, 'buatLaporanWfh']);
    Route::post('/presensi/wfh/{id}/laporan', [PresensiController::class, 'storeLaporanWfh']);
    Route::post('/presensi/wfh/{id}/approve-laporan-atasan', [PresensiController::class, 'approveLaporanAtasan']);
    Route::post('/presensi/wfh/{id}/reject-laporan-atasan', [PresensiController::class, 'rejectLaporanAtasan']);

    Route::get('/notifications', function () {
        $user = Auth::guard('karyawan')->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $notifications = $user->notifications()->latest()->get();

        // Group by tanggal
        $grouped = $notifications->groupBy(function ($n) {
            return $n->created_at->format('Y-m-d');
        })->map(function ($items, $date) {
            return [
                'date' => $date,
                'label' => \Carbon\Carbon::parse($date)->isToday() ? 'Hari Ini'
                    : (\Carbon\Carbon::parse($date)->isYesterday() ? 'Kemarin'
                    : \Carbon\Carbon::parse($date)->translatedFormat('d M Y')),
                'items' => $items,
            ];
        })->values();

        return view('presensi.notifikasi', compact('grouped'));
    });
    Route::post('/notifications/{id}/read', function ($id) {
        $user = Auth::guard('karyawan')->user();
        $n = $user->notifications()->where('id', $id)->first();
        if ($n) $n->markAsRead();
        return response()->json(['ok' => true]);
    });
    Route::post('/notifications/read-all', function () {
        $user = Auth::guard('karyawan')->user();
        if (!$user) return response()->json(['ok' => false], 401);
        $user->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    });

    // Push notification subscription
    Route::post('/api/push/subscribe', function (Request $request) {
        $request->validate([
            'endpoint' => 'required|url',
            'public_key' => 'required|string',
            'auth_token' => 'required|string',
        ]);
        $nik = Auth::guard('karyawan')->user()->nik;
        DB::table('push_subscriptions')->updateOrInsert(
            ['nik' => $nik, 'endpoint' => $request->endpoint],
            [
                'public_key' => $request->public_key,
                'auth_token' => $request->auth_token,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return response()->json(['ok' => true]);
    });
    Route::post('/api/push/unsubscribe', function () {
        $nik = Auth::guard('karyawan')->user()->nik;
        DB::table('push_subscriptions')->where('nik', $nik)->delete();
        return response()->json(['ok' => true]);
    });

    // Settings & permissions
    Route::get('/settings/permissions', [UserPermissionController::class, 'karyawanSettings']);
    Route::get('/api/user/permissions', [UserPermissionController::class, 'getPermissions']);
    Route::post('/api/user/permissions/toggle', [UserPermissionController::class, 'togglePermission']);

    // =====================================================
    // REALTIME POLLING ENDPOINTS
    // =====================================================

    // Dashboard karyawan realtime data
    Route::get('/api/realtime/dashboard', function () {
        $nik = Auth::guard('karyawan')->user()->nik;
        $hariini = date('Y-m-d');

        // Presensi hari ini
        $presensi = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();

        // WFH saya yang perlu tindakan
        $wfhSaya = DB::table('wfh')
            ->where('nik', $nik)
            ->where(function ($q) {
                $q->whereIn('status', ['pending_atasan', 'pending_admin', 'rejected'])
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'approved')
                            ->where(function ($q3) {
                                $q3->whereNull('laporan_deskripsi')->orWhere('laporan_deskripsi', '');
                            });
                    });
            })
            ->orderBy('tgl_wfh', 'desc')
            ->limit(5)
            ->get();

        // Pending persetujuan atasan
        $karyawan = \App\Models\Karyawan::where('nik', $nik)->first();
        $pendingAtasan = collect();
        $pendingLaporanAtasan = collect();
        if (!empty($karyawan->role_approved)) {
            $pendingAtasan = DB::table('wfh')
                ->join('karyawan', 'wfh.nik', '=', 'karyawan.nik')
                ->leftJoin('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit')
                ->where('wfh.atasan_nik', $nik)
                ->where('wfh.status', 'pending_atasan')
                ->select('wfh.*', 'karyawan.nama_lengkap', 'karyawan.jabatan', 'karyawan.posisi', 'unitperusahaan.perusahaan', 'karyawan.unit')
                ->orderBy('wfh.tgl_wfh', 'desc')
                ->get();
            $pendingLaporanAtasan = DB::table('wfh')
                ->join('karyawan', 'wfh.nik', '=', 'karyawan.nik')
                ->leftJoin('unitperusahaan', 'karyawan.unit', '=', 'unitperusahaan.unit')
                ->where('wfh.laporan_atasan_nik', $nik)
                ->where('wfh.laporan_status', 'pending_atasan')
                ->select('wfh.*', 'karyawan.nama_lengkap', 'karyawan.jabatan', 'karyawan.posisi', 'unitperusahaan.perusahaan', 'karyawan.unit')
                ->orderBy('wfh.tgl_wfh', 'desc')
                ->get();
        }

        // Notifikasi baru
        $notifications = $karyawan->notifications()->latest()->take(10)->get()->map(fn($n) => [
            'id' => $n->id,
            'data' => $n->data,
            'read_at' => $n->read_at,
            'created_at' => $n->created_at->diffForHumans()
        ]);
        $unreadCount = $karyawan->notifications()->whereNull('read_at')->count();

        return response()->json([
            'presensi' => $presensi,
            'wfhSaya' => $wfhSaya,
            'pendingAtasan' => $pendingAtasan,
            'pendingLaporanAtasan' => $pendingLaporanAtasan,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    });
});
