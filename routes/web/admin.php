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
use App\Http\Controllers\Admin\KartuAnggotaController;

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/kartu-anggota', [KartuAnggotaController::class, 'index'])->name('kartu.anggota');
Route::post('/kartu-anggota/simpanan', [KartuAnggotaController::class, 'storeSimpanan'])->name('kartu.simpanan.store');
Route::post('/kartu-anggota/simpanan/update/{id}', [KartuAnggotaController::class, 'updateSimpanan'])->name('kartu.simpanan.update');
Route::post('/kartu-anggota/simpanan/delete/{id}', [KartuAnggotaController::class, 'deleteSimpanan'])->name('kartu.simpanan.delete');

Route::post('/kartu-anggota/pengambilan', [KartuAnggotaController::class, 'storePengambilan'])->name('kartu.pengambilan.store');
Route::post('/kartu-anggota/pengambilan/update/{id}', [KartuAnggotaController::class, 'updatePengambilan'])->name('kartu.pengambilan.update');
Route::post('/kartu-anggota/pengambilan/delete/{id}', [KartuAnggotaController::class, 'deletePengambilan'])->name('kartu.pengambilan.delete');

Route::post('/kartu-anggota/pinjaman', [KartuAnggotaController::class, 'storePinjaman'])->name('kartu.pinjaman.store');
Route::post('/kartu-anggota/pinjaman/update/{id}', [KartuAnggotaController::class, 'updatePinjaman'])->name('kartu.pinjaman.update');
Route::post('/kartu-anggota/pinjaman/delete/{id}', [KartuAnggotaController::class, 'deletePinjaman'])->name('kartu.pinjaman.delete');

Route::post('/kartu-anggota/bayar-angsuran', [KartuAnggotaController::class, 'storeBayarAngsuran'])->name('kartu.angsuran.store');
Route::post('/kartu-anggota/bayar-angsuran/update', [KartuAnggotaController::class, 'updateBayarAngsuran'])->name('kartu.angsuran.update');
Route::post('/kartu-anggota/bayar-angsuran/delete', [KartuAnggotaController::class, 'deleteBayarAngsuran'])->name('kartu.angsuran.delete');

Route::post('/kartu-anggota/catatan', [KartuAnggotaController::class, 'updateCatatan'])->name('kartu.catatan.update');
Route::get('/kartu-anggota/saldo/{userId}/{kategoriId}', [KartuAnggotaController::class, 'getSaldoKategori'])->name('kartu.saldo');

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
