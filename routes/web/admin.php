<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JenisController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\SimpananController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\TunggakanController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PengambilanSimpananController;
use App\Http\Controllers\Admin\BantuanController;

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/jenis', [JenisController::class, 'index'])->name('jenis');
Route::post('/jenis/create', [JenisController::class, 'create']);
Route::post('/jenis/edit/{id}', [JenisController::class, 'edit']);
Route::get('/jenis/delete/{id}', [JenisController::class, 'delete']);

Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
Route::post('/kategori/create', [KategoriController::class, 'create']);
Route::post('/kategori/edit/{id}', [KategoriController::class, 'edit']);
Route::get('/kategori/delete/{id}', [KategoriController::class, 'delete']);

Route::get('/simpanan', [SimpananController::class, 'index'])->name('simpanan');
Route::post('/simpanan/create', [SimpananController::class, 'create']);
Route::post('/simpanan/edit/{id}', [SimpananController::class, 'edit']);
Route::get('/simpanan/delete/{id}', [SimpananController::class, 'delete']);
Route::get('/simpanan/getJumlah/{id_user}/{id_kat}', [SimpananController::class, 'getJumlah']);

Route::get('/tagihan/pengajuan', [PengajuanController::class, 'index']);
Route::post('/tagihan/pengajuan/create', [PengajuanController::class, 'create']);
Route::post('/tagihan/pengajuan/edit/{id}', [PengajuanController::class, 'edit']);
Route::get('/tagihan/pengajuan/delete/{id}', [PengajuanController::class, 'delete']);

Route::get('/users', [UserAdminController::class, 'index']);
Route::post('/users/create', [UserAdminController::class, 'create']);
Route::post('/users/edit/{id}', [UserAdminController::class, 'edit']);
Route::get('/users/delete/{id}', [UserAdminController::class, 'delete']);

Route::get('/tunggakan', [TunggakanController::class, 'index']);

Route::get('/tagihan/bayar', [TagihanController::class, 'index']);
Route::post('/tagihan/bayar/create/{id}', [TagihanController::class, 'create']);

Route::get('/pengaturan', [PengaturanController::class, 'index']);
Route::post('/pengaturan/edit/{id}', [PengaturanController::class, 'edit']);

Route::get('/laporan', [LaporanController::class, 'index']);
Route::post('/laporan/filterdata', [LaporanController::class, 'filterData']);
Route::get('/laporan/export', [LaporanController::class, 'export']);
Route::get('/laporan/filterdata/export', [LaporanController::class, 'exportFilteredData']);

Route::get('/activity/logs', [ActivityLogController::class, 'index'])->name('activity.logs');
Route::get('/activity/logs/export', [ActivityLogController::class, 'export'])->name('activity.logs.export');

Route::get('/pengambilan', [PengambilanSimpananController::class, 'index'])->name('pengambilan.index');
Route::get('/pengambilan/create', [PengambilanSimpananController::class, 'create'])->name('pengambilan.create');
Route::post('/pengambilan/store', [PengambilanSimpananController::class, 'store'])->name('pengambilan.store');
Route::get('/pengambilan/getSaldo/{userId}/{kategoriId}', [PengambilanSimpananController::class, 'getSaldo']);

Route::get('/bantuan', [BantuanController::class, 'index']);
Route::post('/bantuan/create', [BantuanController::class, 'create']);
