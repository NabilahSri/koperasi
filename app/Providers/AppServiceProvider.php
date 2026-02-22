<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $lembaga = DB::table('lembaga')->where('id', 1)->first();
            $nama = $lembaga ? $lembaga->nama : '';
            $logoPath = $lembaga && $lembaga->logo ? asset('storage/' . $lembaga->logo) : '/assets/img/logo-icon.svg';
            $view->with([
                'lembaga' => $lembaga,
                'nama' => $nama,
                'logoPath' => $logoPath,
            ]);
        });
    }
}
