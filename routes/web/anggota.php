<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Anggota\HistoryController;

Route::get('/histori/simpanan', [HistoryController::class, 'simpanan']);
Route::get('/histori/tagihan', [HistoryController::class, 'tagihan']);
Route::get('/histori/pengambilan', [HistoryController::class, 'pengambilan']);
