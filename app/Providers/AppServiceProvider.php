<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $lembaga = DB::table('lembaga')->where('id', 1)->first();
            $nama = $lembaga ? $lembaga->nama : '';
            $logoPath = $lembaga && !empty($lembaga->logo) ? asset('storage/' . $lembaga->logo) : asset('assets/img/logo-icon.svg');
            $view->with([
                'lembaga' => $lembaga,
                'nama' => $nama,
                'logoPath' => $logoPath,
            ]);
        });
    }
}
