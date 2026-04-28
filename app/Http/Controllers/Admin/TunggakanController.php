<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TransaksiS;
use App\Models\TransaksiT;
use App\Models\Kategori;
use App\Models\Lembaga;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TunggakanController extends Controller
{
    public function index()
    {
        $anggota = User::where('role', 'anggota')->active()->orderBy('no_user', 'desc')->get();

        $pengaturan = Lembaga::first();
        $tenggatIuran = $pengaturan ? ($pengaturan->tenggat_iuran_wajib ?? 1) : 1;
        $tenggatTagihan = $pengaturan ? ($pengaturan->tenggat_bayar_tagihan ?? 1) : 1;

        $today = Carbon::now();
        $currentDay = $today->day;
        $currentMonth = $today->copy()->startOfMonth();

        $kategoriIuran = Kategori::where('nama', 'like', '%Iuran Wajib%')->first();
        $idIuranWajib = $kategoriIuran ? $kategoriIuran->id : 2;

        foreach ($anggota as $user) {
            $lastIuran = TransaksiS::where('id_user', $user->id)
                ->where('id_kategori', $idIuranWajib)
                ->orderBy('tanggal', 'desc')
                ->first();

            if ($lastIuran) {
                $lastPaidDate = Carbon::parse($lastIuran->tanggal)->startOfMonth();
            } else {
                $lastPaidDate = Carbon::parse($user->created_at)->startOfMonth()->subMonth();
            }

            $diffMonths = $lastPaidDate->diffInMonths($currentMonth);

            if ($currentDay <= $tenggatIuran && $diffMonths > 0) {
                $diffMonths--;
            }

            $user->tunggakan_iuran = $diffMonths;

            $activeLoan = Pengajuan::where('id_user', $user->id)
                ->where('keterangan', 'belum lunas')
                ->first();

            if ($activeLoan) {
                $lastBayarTagihan = TransaksiT::where('id_pengajuan', $activeLoan->id)
                    ->orderBy('tanggal', 'desc')
                    ->first();

                if ($lastBayarTagihan) {
                    $lastPaidTagihanDate = Carbon::parse($lastBayarTagihan->tanggal)->startOfMonth();
                } else {
                    $lastPaidTagihanDate = Carbon::parse($activeLoan->tanggal_pengajuan)->startOfMonth();
                }

                $diffMonthsTagihan = $lastPaidTagihanDate->diffInMonths($currentMonth);

                if ($currentDay <= $tenggatTagihan && $diffMonthsTagihan > 0) {
                    $diffMonthsTagihan--;
                }

                $user->tunggakan_tagihan = $diffMonthsTagihan;
            } else {
                $user->tunggakan_tagihan = 0;
            }
        }

        $anggota = $anggota->filter(function ($user) {
            return $user->tunggakan_iuran > 0 || $user->tunggakan_tagihan > 0;
        });

        $data['total_nunggak'] = $anggota->count();
        $data['nunggak_iuran'] = $anggota->where('tunggakan_iuran', '>', 0)->count();
        $data['nunggak_tagihan'] = $anggota->where('tunggakan_tagihan', '>', 0)->count();

        $data['anggota'] = $anggota;
        return view('admin.pages.tunggakan', $data);
    }
}
