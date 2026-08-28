<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UnitperusahaanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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

Route::get('/presensi/showfilewfh/{file}', [PresensiController::class, 'showfilewfh']);



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

Route::middleware(['guest:user'])->group(function (){
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
    Route::get('/unitperusahaan', [UnitperusahaanController::class,'index']);
    // Route::get('/unitperusahaan', [UnitperusahaanController::class,'index'])->middleware('permission:view-unitperusahaan,user');
    Route::post('/unitperusahaan/store', [UnitperusahaanController::class,'store']);
    Route::post('/unitperusahaan/edit', [UnitperusahaanController::class,'edit']);
    Route::post('/unitperusahaan/{unit}/update', [UnitperusahaanController::class, 'update']);
    Route::post('/unitperusahaan/{unit}/delete', [UnitperusahaanController::class, 'delete']);

    //karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    // Route::get('/karyawan', [KaryawanController::class, 'index'])->middleware('permission:view-karyawan,user');
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
});


//karyawan

Route::middleware(['guest:karyawan'])->group(function (){
    Route::get('/', function () {
    return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
    });

Route::middleware(['auth:karyawan'])->group(function (){
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    //route presensi
    Route::get('/presensi/create' ,[PresensiController::class, 'create']);
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
    
    //route WFH
    Route::get('/presensi/wfh', [PresensiController::class, 'wfh']);
    Route::get('/presensi/buatwfh', [PresensiController::class, 'buatwfh']);
    Route::post('/presensi/storewfh', [PresensiController::class, 'storewfh']);
    Route::delete('/presensi/deletewfh/{id}', [PresensiController::class, 'deletewfh']);
    
});