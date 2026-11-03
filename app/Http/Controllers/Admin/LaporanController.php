<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\FilteredDataExport;
use App\Exports\LaporanExport;
use App\Models\Kategori;
use App\Models\TransaksiS;
use App\Models\TransaksiT;
use App\Models\User;
use App\Models\PengambilanSimpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        Session::forget('filtered_month');
        $data['selectedMonth'] = null;
        $data['kategori'] = Kategori::with('jenis')->orderBy('id_jenis')->orderBy('id')->get();
        $data['user'] = User::where('role', 'anggota')->active()->orderBy('id', 'asc')->get();
        $activeUserIds = $data['user']->pluck('id')->all();
        $data['simpanan'] = [];
        $data['tagihan'] = [];

        $simpanan = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))->groupBy('id_user', 'id_kategori')
            ->whereIn('id_user', $activeUserIds)
            ->get();
        $pengambilan = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user', 'id_kategori')->get();
        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user', 'id_kategori')->get();
        $data['pengambilan'] = [];
        $data['pengambilan_total'] = [];
        $data['pengambilan_manasuka_total'] = [];
        $data['pengambilan_lebaran_total'] = [];
        $data['simpanan_manasuka_total'] = [];
        $data['simpanan_lebaran_total'] = [];
        $data['sisa_manasuka_total'] = [];
        $data['sisa_lebaran_total'] = [];
        $manasukaIds = Kategori::whereIn('nama', ['Manasuka', 'Simpanan Manasuka'])->pluck('id')->toArray();
        $lebaranIds = Kategori::whereIn('nama', ['Lebaran', 'Simpanan Lebaran'])->pluck('id')->toArray();
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();
        $bagihasilKategori = Kategori::whereIn('nama', ['Bagihasil', 'Bagi Hasil', 'Bagi hasil'])->first();
        $data['pinjaman_total'] = [];
        $data['pinjaman_dibayar'] = [];
        $data['pinjaman_sisa'] = [];
        $data['propisi_total'] = [];
        $data['bagihasil_total'] = [];
        $data['bagihasil_dibayar'] = [];
        foreach ($simpanan as $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
            if (in_array($value->id_kategori, $manasukaIds)) {
                $data['simpanan_manasuka_total'][$value->id_user] = ($data['simpanan_manasuka_total'][$value->id_user] ?? 0) + $value->jumlah;
            }
            if (in_array($value->id_kategori, $lebaranIds)) {
                $data['simpanan_lebaran_total'][$value->id_user] = ($data['simpanan_lebaran_total'][$value->id_user] ?? 0) + $value->jumlah;
            }
        }
        foreach ($pengambilan as $p) {
            $data['pengambilan'][$p->id_user][$p->id_kategori] = $p->jumlah;
            $data['pengambilan_total'][$p->id_user] = ($data['pengambilan_total'][$p->id_user] ?? 0) + $p->jumlah;
            if (in_array($p->id_kategori, $manasukaIds)) {
                $data['pengambilan_manasuka_total'][$p->id_user] = ($data['pengambilan_manasuka_total'][$p->id_user] ?? 0) + $p->jumlah;
            }
            if (in_array($p->id_kategori, $lebaranIds)) {
                $data['pengambilan_lebaran_total'][$p->id_user] = ($data['pengambilan_lebaran_total'][$p->id_user] ?? 0) + $p->jumlah;
            }
        }

        foreach ($tagihan as $key => $value) {
            $data['tagihan'][$value->id_user][$value->id_kategori] = $value->jumlah;
        }

        foreach ($data['user'] as $u) {
            $simpM = $data['simpanan_manasuka_total'][$u->id] ?? 0;
            $ambilM = $data['pengambilan_manasuka_total'][$u->id] ?? 0;
            $data['sisa_manasuka_total'][$u->id] = max($simpM - $ambilM, 0);

            $simpL = $data['simpanan_lebaran_total'][$u->id] ?? 0;
            $ambilL = $data['pengambilan_lebaran_total'][$u->id] ?? 0;
            $data['sisa_lebaran_total'][$u->id] = max($simpL - $ambilL, 0);
        }

        // Hitung pinjaman total, dibayar, sisa (semua waktu)
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get();
        foreach ($pinjamanTotal as $pt) {
            $data['pinjaman_total'][$pt->id_user] = $pt->total;
        }
        $propisiTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(COALESCE(propisi, 0)) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get();
        foreach ($propisiTotal as $pr) {
            $data['propisi_total'][$pr->id_user] = $pr->total;
        }
        $dibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user');
        if ($pinjamanKategori) $dibayarQuery->where('id_kategori', $pinjamanKategori->id);
        $dibayarList = $dibayarQuery->get();
        foreach ($dibayarList as $dp) {
            $data['pinjaman_dibayar'][$dp->id_user] = $dp->total;
        }
        foreach ($data['user'] as $u) {
            $t = $data['pinjaman_total'][$u->id] ?? 0;
            $d = $data['pinjaman_dibayar'][$u->id] ?? 0;
            $data['pinjaman_sisa'][$u->id] = max($t - $d, 0);
        }

        // Bagi hasil total (all time) dan dibayar (all time)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get();
        foreach ($bagihasilTotal as $bh) {
            $data['bagihasil_total'][$bh->id_user] = $bh->total;
        }
        $bagihasilDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user');
        if ($bagihasilKategori) {
            $bagihasilDibayarQuery->where('id_kategori', $bagihasilKategori->id);
        }
        $bagihasilDibayarList = $bagihasilDibayarQuery->get();
        foreach ($bagihasilDibayarList as $bd) {
            $data['bagihasil_dibayar'][$bd->id_user] = $bd->total;
        }

        return view('admin.pages.laporan', $data);
    }

    public function filterData(Request $request)
    {
        $selectedMonth = $request->input('filter_month', Session::get('filtered_month'));
        if (empty($selectedMonth)) {
            return redirect('/laporan');
        }
        Session::put('filtered_month', $selectedMonth);
        $data['selectedMonth'] = $selectedMonth;

        $data['kategori'] = Kategori::with('jenis')->orderBy('id_jenis')->orderBy('id')->get();
        $activeUserIds = User::where('role', 'anggota')->active()->pluck('id')->all();

        $formattedMonth = Carbon::parse($selectedMonth)->format('m');
        $formattedYear = Carbon::parse($selectedMonth)->format('Y');

        $simpanan = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengambilan = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengajuanMonthUserIds = \App\Models\Pengajuan::whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal_pengajuan', '=', $formattedMonth)
            ->whereYear('tanggal_pengajuan', '=', $formattedYear)
            ->pluck('id_user')
            ->toArray();

        $userIds = array_merge(
            $simpanan->pluck('id_user')->toArray(),
            $tagihan->pluck('id_user')->toArray(),
            $pengambilan->pluck('id_user')->toArray(),
            $pengajuanMonthUserIds
        );
        $userIds = array_unique($userIds);

        $data['user'] = User::where('role', 'anggota')
            ->active()
            ->whereIn('id', $userIds)
            ->orderBy('id', 'asc')
            ->get();

        $data['simpanan'] = [];
        $data['tagihan'] = [];
        $data['pengambilan'] = [];
        $data['pengambilan_total'] = [];
        $data['pengambilan_manasuka_total'] = [];
        $data['pengambilan_lebaran_total'] = [];
        $data['simpanan_manasuka_total'] = [];
        $data['simpanan_lebaran_total'] = [];
        $data['sisa_manasuka_total'] = [];
        $data['sisa_lebaran_total'] = [];
        $manasukaIds = Kategori::whereIn('nama', ['Manasuka', 'Simpanan Manasuka'])->pluck('id')->toArray();
        $lebaranIds = Kategori::whereIn('nama', ['Lebaran', 'Simpanan Lebaran'])->pluck('id')->toArray();
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();
        $bagihasilKategori = Kategori::whereIn('nama', ['Bagihasil', 'Bagi Hasil', 'Bagi hasil'])->first();
        $data['pinjaman_total'] = [];
        $data['pinjaman_dibayar'] = [];
        $data['pinjaman_sisa'] = [];
        $data['propisi_total'] = [];
        $data['bagihasil_total'] = [];
        $data['bagihasil_dibayar'] = [];

        foreach ($simpanan as $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
            if (in_array($value->id_kategori, $manasukaIds)) {
                $data['simpanan_manasuka_total'][$value->id_user] = ($data['simpanan_manasuka_total'][$value->id_user] ?? 0) + $value->jumlah;
            }
            if (in_array($value->id_kategori, $lebaranIds)) {
                $data['simpanan_lebaran_total'][$value->id_user] = ($data['simpanan_lebaran_total'][$value->id_user] ?? 0) + $value->jumlah;
            }
        }
        foreach ($pengambilan as $p) {
            $data['pengambilan'][$p->id_user][$p->id_kategori] = $p->jumlah;
            $data['pengambilan_total'][$p->id_user] = ($data['pengambilan_total'][$p->id_user] ?? 0) + $p->jumlah;
            if (in_array($p->id_kategori, $manasukaIds)) {
                $data['pengambilan_manasuka_total'][$p->id_user] = ($data['pengambilan_manasuka_total'][$p->id_user] ?? 0) + $p->jumlah;
            }
            if (in_array($p->id_kategori, $lebaranIds)) {
                $data['pengambilan_lebaran_total'][$p->id_user] = ($data['pengambilan_lebaran_total'][$p->id_user] ?? 0) + $p->jumlah;
            }
        }

        foreach ($tagihan as $value) {
            $data['tagihan'][$value->id_user][$value->id_kategori] = $value->jumlah;
        }

        foreach ($data['user'] as $u) {
            $simpM = $data['simpanan_manasuka_total'][$u->id] ?? 0;
            $ambilM = $data['pengambilan_manasuka_total'][$u->id] ?? 0;
            $data['sisa_manasuka_total'][$u->id] = max($simpM - $ambilM, 0);

            $simpL = $data['simpanan_lebaran_total'][$u->id] ?? 0;
            $ambilL = $data['pengambilan_lebaran_total'][$u->id] ?? 0;
            $data['sisa_lebaran_total'][$u->id] = max($simpL - $ambilL, 0);
        }

        // Pinjaman total (semua waktu), dibayar (sesuai filter bulan), sisa
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get();
        foreach ($pinjamanTotal as $pt) {
            $data['pinjaman_total'][$pt->id_user] = $pt->total;
        }
        $propisiTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(COALESCE(propisi, 0)) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get();
        foreach ($propisiTotal as $pr) {
            $data['propisi_total'][$pr->id_user] = $pr->total;
        }
        $dibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user');
        if ($pinjamanKategori) $dibayarQuery->where('id_kategori', $pinjamanKategori->id);
        $dibayarList = $dibayarQuery->get();
        foreach ($dibayarList as $dp) {
            $data['pinjaman_dibayar'][$dp->id_user] = $dp->total;
        }
        foreach ($data['user'] as $u) {
            $t = $data['pinjaman_total'][$u->id] ?? 0;
            $d = $data['pinjaman_dibayar'][$u->id] ?? 0;
            $data['pinjaman_sisa'][$u->id] = max($t - $d, 0);
        }

        // Bagi hasil total (all time), dibayar (filtered by month)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get();
        foreach ($bagihasilTotal as $bh) {
            $data['bagihasil_total'][$bh->id_user] = $bh->total;
        }
        $bagihasilDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user');
        if ($bagihasilKategori) $bagihasilDibayarQuery->where('id_kategori', $bagihasilKategori->id);
        $bagihasilDibayarList = $bagihasilDibayarQuery->get();
        foreach ($bagihasilDibayarList as $bd) {
            $data['bagihasil_dibayar'][$bd->id_user] = $bd->total;
        }

        return view('admin.pages.laporan', $data);
    }

    public function export()
    {
        $users = User::where('role', 'anggota')->active()->orderBy('id', 'asc')->get();
        $activeUserIds = $users->pluck('id')->all();
        $kategoriList = Kategori::with('jenis')->orderBy('id_jenis')->orderBy('id')->get();

        $simpananData = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihanData = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengambilanData = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $lookup = [];
        foreach ($simpananData as $s) {
            $lookup[$s->id_user][$s->id_kategori] = $s->jumlah;
        }
        foreach ($tagihanData as $t) {
            $lookup[$t->id_user][$t->id_kategori] = $t->jumlah;
        }

        $data = [];
        $headings = ['No', 'Nama', 'Alamat'];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                continue;
            }
            $headings[] = $k->nama;
        }
        $headings[] = 'Nominal Pinjaman';
        $headings[] = 'Propisi';
        $headings[] = 'Pinjaman Terbayar';
        $headings[] = 'Sisa Pinjaman';
        $headings[] = 'Bagi Hasil';

        $colTotals = [];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                continue;
            }
            $colTotals[$k->id] = 0;
        }
        $grandTotalPinjamanTotal = 0;
        $grandTotalPropisi = 0;
        $grandTotalPinjamanDibayar = 0;
        $grandTotalPinjamanSisa = 0;
        $grandTotalBagihasilTotal = 0;
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();

        // Pinjaman totals (all time) and dibayar (all time) for export
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get()->keyBy('id_user');
        $propisiTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(COALESCE(propisi, 0)) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get()->keyBy('id_user');
        $pinjamanDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'));
        if ($pinjamanKategori) {
            $pinjamanDibayarQuery->where('id_kategori', $pinjamanKategori->id);
        }
        $pinjamanDibayar = $pinjamanDibayarQuery->whereIn('id_user', $activeUserIds)->groupBy('id_user')->get()->keyBy('id_user');

        // Bagihasil totals (all time)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get()->keyBy('id_user');

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->name,
                $user->alamat,
            ];

            foreach ($kategoriList as $k) {
                if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                    continue;
                }
                $amount = $lookup[$user->id][$k->id] ?? 0;
                $rowData[] = $amount ?: '-';

                $colTotals[$k->id] += $amount;
            }

            $userPinjamanTotal = $pinjamanTotal->get($user->id)->total ?? 0;
            $userPropisiTotal = $propisiTotal->get($user->id)->total ?? 0;
            $userPinjamanDibayar = $pinjamanDibayar->get($user->id)->total ?? 0;
            $userPinjamanSisa = max($userPinjamanTotal - $userPinjamanDibayar, 0);
            $userBagihasilTotal = $bagihasilTotal->get($user->id)->total ?? 0;

            $rowData[] = $userPinjamanTotal ?: 0;
            $rowData[] = $userPropisiTotal ?: '-';
            $rowData[] = $userPinjamanDibayar ?: 0;
            $rowData[] = $userPinjamanSisa ?: 0;
            $rowData[] = $userBagihasilTotal ?: '-';

            $grandTotalPinjamanTotal += $userPinjamanTotal;
            $grandTotalPropisi += $userPropisiTotal;
            $grandTotalPinjamanDibayar += $userPinjamanDibayar;
            $grandTotalPinjamanSisa += $userPinjamanSisa;
            $grandTotalBagihasilTotal += $userBagihasilTotal;

            $data[] = $rowData;
        }

        $totalRow = ['', '', ''];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                continue;
            }
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalPinjamanTotal ?: '-';
        $totalRow[] = $grandTotalPropisi ?: '-';
        $totalRow[] = $grandTotalPinjamanDibayar ?: '-';
        $totalRow[] = $grandTotalPinjamanSisa ?: '-';
        $totalRow[] = $grandTotalBagihasilTotal ?: '-';

        $data[] = $totalRow;

        return Excel::download(new LaporanExport($data, $headings), 'Laporan Master Koperasi.xlsx');
    }

    public function exportFilteredData(Request $request)
    {
        $selectedMonth = $request->input('filter_month', Session::get('filtered_month'));
        if (empty($selectedMonth)) {
            return redirect('/laporan/export');
        }
        $formattedMonth = Carbon::parse($selectedMonth)->format('m');
        $formattedYear = Carbon::parse($selectedMonth)->format('Y');
        $formattedMonth2 = Carbon::parse($selectedMonth)->locale('id')->isoFormat('MMMM YYYY');

        $kategoriList = Kategori::with('jenis')->orderBy('id_jenis')->orderBy('id')->get();
        $activeUserIds = User::where('role', 'anggota')->active()->pluck('id')->all();

        $userIdsWithTransactions = [];

        $simpananData = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihanData = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengambilanData = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengajuanMonthUserIds = \App\Models\Pengajuan::whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal_pengajuan', $formattedMonth)
            ->whereYear('tanggal_pengajuan', $formattedYear)
            ->pluck('id_user')
            ->toArray();

        $userIdsWithTransactions = array_merge(
            $simpananData->pluck('id_user')->toArray(),
            $tagihanData->pluck('id_user')->toArray(),
            $pengambilanData->pluck('id_user')->toArray(),
            $pengajuanMonthUserIds
        );
        $userIdsWithTransactions = array_unique($userIdsWithTransactions);

        $users = User::where('role', 'anggota')
            ->active()
            ->whereIn('id', $userIdsWithTransactions)
            ->orderBy('id', 'asc')
            ->get();

        $lookup = [];
        foreach ($simpananData as $s) {
            $lookup[$s->id_user][$s->id_kategori] = $s->jumlah;
        }
        foreach ($tagihanData as $t) {
            $lookup[$t->id_user][$t->id_kategori] = $t->jumlah;
        }

        $data = [];
        $headings = ['No', 'Nama', 'Alamat'];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                continue;
            }
            $headings[] = $k->nama;
        }
        $headings[] = 'Nominal Pinjaman';
        $headings[] = 'Propisi';
        $headings[] = 'Pinjaman Terbayar';
        $headings[] = 'Sisa Pinjaman';
        $headings[] = 'Bagi Hasil';

        $colTotals = [];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                continue;
            }
            $colTotals[$k->id] = 0;
        }
        $grandTotalPinjamanTotal = 0;
        $grandTotalPropisi = 0;
        $grandTotalPinjamanDibayar = 0;
        $grandTotalPinjamanSisa = 0;
        $grandTotalBagihasilTotal = 0;
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();

        // Pinjaman total (all time), dibayar (filtered by month), sisa
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get()->keyBy('id_user');
        $propisiTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(COALESCE(propisi, 0)) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get()->keyBy('id_user');
        $pinjamanDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear);
        if ($pinjamanKategori) {
            $pinjamanDibayarQuery->where('id_kategori', $pinjamanKategori->id);
        }
        $pinjamanDibayar = $pinjamanDibayarQuery->groupBy('id_user')->get()->keyBy('id_user');

        // Bagihasil total (all time)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->whereIn('id_user', $activeUserIds)
            ->groupBy('id_user')->get()->keyBy('id_user');

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->name,
                $user->alamat,
            ];

            foreach ($kategoriList as $k) {
                if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                    continue;
                }
                $amount = $lookup[$user->id][$k->id] ?? 0;
                $rowData[] = $amount ?: '-';

                $colTotals[$k->id] += $amount;
            }

            $userPinjamanTotal = $pinjamanTotal->get($user->id)->total ?? 0;
            $userPropisiTotal = $propisiTotal->get($user->id)->total ?? 0;
            $userPinjamanDibayar = $pinjamanDibayar->get($user->id)->total ?? 0;
            $userPinjamanSisa = max($userPinjamanTotal - $userPinjamanDibayar, 0);
            $userBagihasilTotal = $bagihasilTotal->get($user->id)->total ?? 0;

            $rowData[] = $userPinjamanTotal ?: 0;
            $rowData[] = $userPropisiTotal ?: '-';
            $rowData[] = $userPinjamanDibayar ?: 0;
            $rowData[] = $userPinjamanSisa ?: 0;
            $rowData[] = $userBagihasilTotal ?: '-';

            $grandTotalPinjamanTotal += $userPinjamanTotal;
            $grandTotalPropisi += $userPropisiTotal;
            $grandTotalPinjamanDibayar += $userPinjamanDibayar;
            $grandTotalPinjamanSisa += $userPinjamanSisa;
            $grandTotalBagihasilTotal += $userBagihasilTotal;

            $data[] = $rowData;
        }

        $totalRow = ['', '', ''];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && in_array(strtolower($k->nama), ['pinjaman', 'bagihasil', 'bagi hasil'])) {
                continue;
            }
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalPinjamanTotal ?: '-';
        $totalRow[] = $grandTotalPropisi ?: '-';
        $totalRow[] = $grandTotalPinjamanDibayar ?: '-';
        $totalRow[] = $grandTotalPinjamanSisa ?: '-';
        $totalRow[] = $grandTotalBagihasilTotal ?: '-';

        $data[] = $totalRow;

        $filename = 'Laporan Koprasi Bulan ' . $formattedMonth2 . '.xlsx';

        return Excel::download(new FilteredDataExport($data, $formattedMonth, $headings), $filename);
    }
}
