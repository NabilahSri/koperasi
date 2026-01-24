<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\UserAnggotaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PengambilanSimpananController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('pages.login');
// });

Route::get('/', [LoginController::class, 'index']);


Route::middleware(['statuslogin'])->group(function () {
    //Jenis
    Route::get('/jenis', [JenisController::class, 'index'])->name('jenis');
    Route::post('/jenis/create', [JenisController::class, 'create']);
    Route::post('/jenis/edit/{id}', [JenisController::class, 'edit']);
    Route::get('/jenis/delete/{id}', [JenisController::class, 'delete']);

    //Kategori
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
    Route::post('/kategori/create', [KategoriController::class, 'create']);
    Route::post('/kategori/edit/{id}', [KategoriController::class, 'edit']);
    Route::get('/kategori/delete/{id}', [KategoriController::class, 'delete']);

    //Simpanan
    Route::get('/simpanan', [SimpananController::class, 'index'])->name('simpanan');
    Route::post('/simpanan/create', [SimpananController::class, 'create']);
    Route::post('/simpanan/edit/{id}', [SimpananController::class, 'edit']);
    Route::get('/simpanan/delete/{id}', [SimpananController::class, 'delete']);
    Route::get('/simpanan/getJumlah/{id_user}/{id_kat}', [SimpananController::class, 'getJumlah']);

    //Pengajuan
    Route::get('/tagihan/pengajuan', [PengajuanController::class, 'index']);
    Route::post('/tagihan/pengajuan/create', [PengajuanController::class, 'create']);
    Route::post('/tagihan/pengajuan/edit/{id}', [PengajuanController::class, 'edit']);
    Route::get('/tagihan/pengajuan/delete/{id}', [PengajuanController::class, 'delete']);

    //UserAdmin
    Route::get('/users/admin', [UserAdminController::class, 'index']);
    Route::post('/users/admin/create', [UserAdminController::class, 'create']);
    Route::post('/users/admin/edit/{id}', [UserAdminController::class, 'edit']);
    Route::get('/users/admin/delete/{id}', [UserAdminController::class, 'delete']);

    //UserAnggota
    Route::get('/users/anggota', [UserAnggotaController::class, 'index']);
    Route::post('/users/anggota/create', [UserAnggotaController::class, 'create']);
    Route::post('/users/anggota/edit/{id}', [UserAnggotaController::class, 'edit']);
    Route::get('/users/anggota/delete/{id}', [UserAnggotaController::class, 'delete']);

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    //Profil
    Route::get('/profile', [LoginController::class, 'show']);

    //Tagihan
    Route::get('/tagihan/bayar', [TagihanController::class, 'index']);
    Route::post('/tagihan/bayar/create/{id}', [TagihanController::class, 'create']);

    //Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index']);
    Route::post('/pengaturan/edit/{id}', [PengaturanController::class, 'edit']);

    //Laporan
    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::post('/laporan/filterdata', [LaporanController::class, 'filterData']);
    Route::get('/laporan/export', [LaporanController::class, 'export']);
    Route::get('/laporan/filterdata/export', [LaporanController::class, 'exportFilteredData']);

    //Activity Logs
    Route::get('/activity/logs', [ActivityLogController::class, 'index'])->name('activity.logs');
    Route::get('/activity/logs/export', [ActivityLogController::class, 'export'])->name('activity.logs.export');

    // Pengambilan Tabungan
    Route::get('/pengambilan', [PengambilanSimpananController::class, 'index'])->name('pengambilan.index');
    Route::get('/pengambilan/create', [PengambilanSimpananController::class, 'create'])->name('pengambilan.create');
    Route::post('/pengambilan/store', [PengambilanSimpananController::class, 'store'])->name('pengambilan.store');
    Route::get('/pengambilan/getSaldo/{userId}/{kategoriId}', [PengambilanSimpananController::class, 'getSaldo']);

    //Riwayat
    Route::get('/histori/simpanan', [HistoryController::class, 'simpanan']);
    Route::get('/histori/tagihan', [HistoryController::class, 'tagihan']);
});

Route::post('/login', [LoginController::class, 'auth']);
Route::get('/logout', [LoginController::class, 'logout']);
