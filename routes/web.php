<?php

use App\Http\Controllers\LoginController;
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
    require __DIR__ . '/web/admin.php';
    require __DIR__ . '/web/anggota.php';
    Route::get('/profile', [LoginController::class, 'show']);
});

Route::post('/login', [LoginController::class, 'auth']);
Route::get('/logout', [LoginController::class, 'logout']);
