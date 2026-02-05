<?php

namespace App\Http\Controllers;

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
        $anggota = User::orderBy('no_user', 'desc')->get();

        $pengaturan = Lembaga::first();
        $tenggatIuran = $pengaturan ? ($pengaturan->tenggat_iuran_wajib ?? 1) : 1;
        $tenggatTagihan = $pengaturan ? ($pengaturan->tenggat_bayar_tagihan ?? 1) : 1;

        $today = Carbon::now();
        $currentDay = $today->day;
        $currentMonth = $today->copy()->startOfMonth();

        // Cari ID Kategori secara dinamis
        $kategoriIuran = Kategori::where('nama', 'like', '%Iuran Wajib%')->first();
        $idIuranWajib = $kategoriIuran ? $kategoriIuran->id : 2;

        foreach ($anggota as $user) {
            // Hitung Tunggakan Iuran Wajib
            $lastIuran = TransaksiS::where('id_user', $user->id)
                ->where('id_kategori', $idIuranWajib)
                ->orderBy('tanggal', 'desc')
                ->first();

            if ($lastIuran) {
                $lastPaidDate = Carbon::parse($lastIuran->tanggal)->startOfMonth();
            } else {
                // Jika belum pernah bayar, hitung dari bulan daftar (anggap bulan daftar belum dibayar)
                $lastPaidDate = Carbon::parse($user->created_at)->startOfMonth()->subMonth();
            }

            $diffMonths = $lastPaidDate->diffInMonths($currentMonth);

            // Jika hari ini belum lewat tenggat, kurangi 1 bulan (karena bulan ini belum dianggap nunggak)
            if ($currentDay <= $tenggatIuran && $diffMonths > 0) {
                $diffMonths--;
            }

            $user->tunggakan_iuran = $diffMonths;

            // Hitung Tunggakan Tagihan (Pinjaman)
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
                    // Jika belum pernah bayar, hitung dari bulan pengajuan (angsuran mulai bulan depan)
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

        // Filter hanya yang menunggak
        $anggota = $anggota->filter(function ($user) {
            return $user->tunggakan_iuran > 0 || $user->tunggakan_tagihan > 0;
        });

        $data['total_nunggak'] = $anggota->count();
        $data['nunggak_iuran'] = $anggota->where('tunggakan_iuran', '>', 0)->count();
        $data['nunggak_tagihan'] = $anggota->where('tunggakan_tagihan', '>', 0)->count();

        $data['anggota'] = $anggota;
        return view('pages.tunggakan', $data);
    }
}
