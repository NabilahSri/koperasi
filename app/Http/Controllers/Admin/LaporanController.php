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
        $data['kategori'] = Kategori::orderBy('id_jenis')->get();
        $data['user'] = User::where('role', 'anggota')->get();
        $data['simpanan'] = [];
        $data['tagihan'] = [];

        $simpanan = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))->groupBy('id_user', 'id_kategori')
            ->get();
        $pengambilan = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->groupBy('id_user', 'id_kategori')->get();
        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))->groupBy('id_user', 'id_kategori')->get();
        $data['pengambilan'] = [];
        $data['pengambilan_total'] = [];
        $data['pengambilan_manasuka_total'] = [];
        $data['pengambilan_lebaran_total'] = [];
        $manasukaIds = Kategori::whereIn('nama', ['Manasuka', 'Simpanan Manasuka'])->pluck('id')->toArray();
        $lebaranIds = Kategori::whereIn('nama', ['Lebaran', 'Simpanan Lebaran'])->pluck('id')->toArray();
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();
        $bagihasilKategori = Kategori::whereIn('nama', ['Bagihasil', 'Bagi Hasil', 'Bagi hasil'])->first();
        $data['pinjaman_total'] = [];
        $data['pinjaman_dibayar'] = [];
        $data['pinjaman_sisa'] = [];
        $data['bagihasil_total'] = [];
        $data['bagihasil_dibayar'] = [];
        foreach ($simpanan as $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
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

        // Hitung pinjaman total, dibayar, sisa (semua waktu)
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->groupBy('id_user')->get();
        foreach ($pinjamanTotal as $pt) {
            $data['pinjaman_total'][$pt->id_user] = $pt->total;
        }
        $dibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))->groupBy('id_user');
        if ($pinjamanKategori) $dibayarQuery->where('id_kategori', $pinjamanKategori->id);
        $dibayarList = $dibayarQuery->get();
        foreach ($dibayarList as $dp) {
            $data['pinjaman_dibayar'][$dp->id_user] = $dp->total;
        }
        foreach (User::all() as $u) {
            $t = $data['pinjaman_total'][$u->id] ?? 0;
            $d = $data['pinjaman_dibayar'][$u->id] ?? 0;
            $data['pinjaman_sisa'][$u->id] = max($t - $d, 0);
        }

        // Bagi hasil total (all time) dan dibayar (all time)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->groupBy('id_user')->get();
        foreach ($bagihasilTotal as $bh) {
            $data['bagihasil_total'][$bh->id_user] = $bh->total;
        }
        $bagihasilDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))->groupBy('id_user');
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
        $data['kategori'] = Kategori::orderBy('id_jenis')->get();

        $selectedMonth = $request->input('filter_month');
        Session::put('filtered_month', $selectedMonth);
        $formattedMonth = Carbon::parse($selectedMonth)->format('m');
        $formattedYear = Carbon::parse($selectedMonth)->format('Y');

        $simpanan = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengambilan = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $userIds = array_merge(
            $simpanan->pluck('id_user')->toArray(),
            $tagihan->pluck('id_user')->toArray(),
            $pengambilan->pluck('id_user')->toArray()
        );
        $userIds = array_unique($userIds);

        $data['user'] = User::where('role', 'anggota')->whereIn('id', $userIds)->get();

        $data['simpanan'] = [];
        $data['tagihan'] = [];
        $data['pengambilan'] = [];
        $data['pengambilan_total'] = [];
        $data['pengambilan_manasuka_total'] = [];
        $data['pengambilan_lebaran_total'] = [];
        $manasukaIds = Kategori::whereIn('nama', ['Manasuka', 'Simpanan Manasuka'])->pluck('id')->toArray();
        $lebaranIds = Kategori::whereIn('nama', ['Lebaran', 'Simpanan Lebaran'])->pluck('id')->toArray();
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();
        $bagihasilKategori = Kategori::whereIn('nama', ['Bagihasil', 'Bagi Hasil', 'Bagi hasil'])->first();
        $data['pinjaman_total'] = [];
        $data['pinjaman_dibayar'] = [];
        $data['pinjaman_sisa'] = [];
        $data['bagihasil_total'] = [];
        $data['bagihasil_dibayar'] = [];

        foreach ($simpanan as $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
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

        // Pinjaman total (semua waktu), dibayar (sesuai filter bulan), sisa
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->groupBy('id_user')->get();
        foreach ($pinjamanTotal as $pt) {
            $data['pinjaman_total'][$pt->id_user] = $pt->total;
        }
        $dibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
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
            ->groupBy('id_user')->get();
        foreach ($bagihasilTotal as $bh) {
            $data['bagihasil_total'][$bh->id_user] = $bh->total;
        }
        $bagihasilDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
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
        $users = User::where('role', 'anggota')->get();
        $kategoriList = Kategori::with('jenis')->orderBy('id_jenis')->get();

        $simpananData = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihanData = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengambilanData = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
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
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                continue;
            }
            $headings[] = $k->nama;
        }
        $headings[] = 'Nominal Pinjaman';
        $headings[] = 'Pinjaman Terbayar';
        $headings[] = 'Sisa Pinjaman';
        $headings[] = 'Nominal Bagi Hasil';
        $headings[] = 'Jumlah Nominal Bagi Hasil';
        $headings[] = 'Pengambilan Manasuka';
        $headings[] = 'Pengambilan Lebaran';

        $colTotals = [];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                continue;
            }
            $colTotals[$k->id] = 0;
        }
        $grandTotalPinjamanTotal = 0;
        $grandTotalPinjamanDibayar = 0;
        $grandTotalPinjamanSisa = 0;
        $grandTotalPengambilanManasuka = 0;
        $grandTotalPengambilanLebaran = 0;
        $manasukaIds = [];
        $lebaranIds = [];
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();
        $bagihasilKategori = Kategori::whereIn('nama', ['Bagihasil', 'Bagi Hasil', 'Bagi hasil'])->first();
        foreach ($kategoriList as $k) {
            if (in_array($k->nama, ['Manasuka', 'Simpanan Manasuka'])) $manasukaIds[] = $k->id;
            if (in_array($k->nama, ['Lebaran', 'Simpanan Lebaran'])) $lebaranIds[] = $k->id;
        }

        // Pinjaman totals (all time) and dibayar (all time) for export
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->groupBy('id_user')->get()->keyBy('id_user');
        $pinjamanDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'));
        if ($pinjamanKategori) {
            $pinjamanDibayarQuery->where('id_kategori', $pinjamanKategori->id);
        }
        $pinjamanDibayar = $pinjamanDibayarQuery->groupBy('id_user')->get()->keyBy('id_user');

        // Bagihasil totals (all time) and dibayar (all time)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->groupBy('id_user')->get()->keyBy('id_user');
        $bagihasilDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'));
        if ($bagihasilKategori) {
            $bagihasilDibayarQuery->where('id_kategori', $bagihasilKategori->id);
        }
        $bagihasilDibayar = $bagihasilDibayarQuery->groupBy('id_user')->get()->keyBy('id_user');

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->name,
                $user->alamat,
            ];

            $userTotalPengambilanManasuka = 0;
            $userTotalPengambilanLebaran = 0;

            foreach ($kategoriList as $k) {
                if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                    continue;
                }
                $amount = $lookup[$user->id][$k->id] ?? 0;
                $rowData[] = $amount ?: '-';

                $colTotals[$k->id] += $amount;
            }
            foreach ($pengambilanData as $p) {
                if ($p->id_user == $user->id) {
                    if (in_array($p->id_kategori, $manasukaIds)) $userTotalPengambilanManasuka += $p->jumlah;
                    if (in_array($p->id_kategori, $lebaranIds)) $userTotalPengambilanLebaran += $p->jumlah;
                }
            }

            $userPinjamanTotal = $pinjamanTotal->get($user->id)->total ?? 0;
            $userPinjamanDibayar = $pinjamanDibayar->get($user->id)->total ?? 0;
            $userPinjamanSisa = max($userPinjamanTotal - $userPinjamanDibayar, 0);

            $rowData[] = $userPinjamanTotal ?: '-';
            $rowData[] = $userPinjamanDibayar ?: '-';
            $rowData[] = $userPinjamanSisa ?: '-';
            $rowData[] = ($bagihasilTotal->get($user->id)->total ?? 0) ?: '-';
            $rowData[] = ($bagihasilDibayar->get($user->id)->total ?? 0) ?: '-';
            $rowData[] = $userTotalPengambilanManasuka ?: '-';
            $rowData[] = $userTotalPengambilanLebaran ?: '-';

            $grandTotalPinjamanTotal += $userPinjamanTotal;
            $grandTotalPinjamanDibayar += $userPinjamanDibayar;
            $grandTotalPinjamanSisa += $userPinjamanSisa;
            $grandTotalBagihasilTotal = ($grandTotalBagihasilTotal ?? 0) + ($bagihasilTotal->get($user->id)->total ?? 0);
            $grandTotalBagihasilDibayar = ($grandTotalBagihasilDibayar ?? 0) + ($bagihasilDibayar->get($user->id)->total ?? 0);
            $grandTotalPengambilanManasuka += $userTotalPengambilanManasuka;
            $grandTotalPengambilanLebaran += $userTotalPengambilanLebaran;

            $data[] = $rowData;
        }

        $totalRow = ['', '', '', ''];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                continue;
            }
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalPinjamanTotal ?: '-';
        $totalRow[] = $grandTotalPinjamanDibayar ?: '-';
        $totalRow[] = $grandTotalPinjamanSisa ?: '-';
        $totalRow[] = ($grandTotalBagihasilTotal ?? 0) ?: '-';
        $totalRow[] = ($grandTotalBagihasilDibayar ?? 0) ?: '-';
        $totalRow[] = $grandTotalPengambilanManasuka ?: '-';
        $totalRow[] = $grandTotalPengambilanLebaran ?: '-';

        $data[] = $totalRow;

        return Excel::download(new LaporanExport($data, $headings), 'Laporan Master Koperasi.xlsx');
    }

    public function exportFilteredData(Request $request)
    {
        $selectedMonth = Session::get('filtered_month');
        $formattedMonth = Carbon::parse($selectedMonth)->format('m');
        $formattedYear = Carbon::parse($selectedMonth)->format('Y');
        $formattedMonth2 = Carbon::parse($selectedMonth)->locale('id')->isoFormat('MMMM YYYY');

        $kategoriList = Kategori::with('jenis')->orderBy('id_jenis')->get();

        $userIdsWithTransactions = [];

        $simpananData = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihanData = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $pengambilanData = PengambilanSimpanan::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $userIdsWithTransactions = array_merge(
            $simpananData->pluck('id_user')->toArray(),
            $tagihanData->pluck('id_user')->toArray(),
            $pengambilanData->pluck('id_user')->toArray()
        );
        $userIdsWithTransactions = array_unique($userIdsWithTransactions);

        $users = User::where('role', 'anggota')->whereIn('id', $userIdsWithTransactions)->get();

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
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                continue;
            }
            $headings[] = $k->nama;
        }
        $headings[] = 'Nominal Pinjaman';
        $headings[] = 'Pinjaman Terbayar';
        $headings[] = 'Sisa Pinjaman';
        $headings[] = 'Nominal Bagi Hasil';
        $headings[] = 'Jumlah Nominal Bagi Hasil';
        $headings[] = 'Pengambilan Manasuka';
        $headings[] = 'Pengambilan Lebaran';

        $colTotals = [];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                continue;
            }
            $colTotals[$k->id] = 0;
        }
        $grandTotalPinjamanTotal = 0;
        $grandTotalPinjamanDibayar = 0;
        $grandTotalPinjamanSisa = 0;
        $grandTotalPengambilanManasuka = 0;
        $grandTotalPengambilanLebaran = 0;
        $grandTotalBagihasilTotal = 0;
        $grandTotalBagihasilDibayar = 0;
        $manasukaIds = [];
        $lebaranIds = [];
        $pinjamanKategori = Kategori::where('nama', 'Pinjaman')->first();
        $bagihasilKategori = Kategori::whereIn('nama', ['Bagihasil', 'Bagi Hasil', 'Bagi hasil'])->first();
        foreach ($kategoriList as $k) {
            if (in_array($k->nama, ['Manasuka', 'Simpanan Manasuka'])) $manasukaIds[] = $k->id;
            if (in_array($k->nama, ['Lebaran', 'Simpanan Lebaran'])) $lebaranIds[] = $k->id;
        }

        // Pinjaman total (all time), dibayar (filtered by month), sisa
        $pinjamanTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_pinjaman) AS total'))
            ->groupBy('id_user')->get()->keyBy('id_user');
        $pinjamanDibayarQuery = TransaksiT::select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear);
        if ($pinjamanKategori) {
            $pinjamanDibayarQuery->where('id_kategori', $pinjamanKategori->id);
        }
        $pinjamanDibayar = $pinjamanDibayarQuery->groupBy('id_user')->get()->keyBy('id_user');

        // Bagihasil total (all time) dan dibayar (sesuai filter bulan)
        $bagihasilTotal = \App\Models\Pengajuan::select('id_user', DB::raw('SUM(nominal_bagihasil) AS total'))
            ->groupBy('id_user')->get()->keyBy('id_user');
        $bagihasilDibayarQuery = TransaksiT::where('id_kategori', $bagihasilKategori->id)
            ->select('id_user', DB::raw('SUM(jumlah) AS total'))
            ->whereMonth('tanggal', $formattedMonth)
            ->whereYear('tanggal', $formattedYear);
        if ($bagihasilKategori) {
            $bagihasilDibayarQuery->where('id_kategori', $bagihasilKategori->id);
        }
        $bagihasilDibayar = $bagihasilDibayarQuery->groupBy('id_user')->get()->keyBy('id_user');

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->name,
                $user->alamat,
            ];

            $userTotalPengambilanManasuka = 0;
            $userTotalPengambilanLebaran = 0;

            foreach ($kategoriList as $k) {
                if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                    continue;
                }
                $amount = $lookup[$user->id][$k->id] ?? 0;
                $rowData[] = $amount ?: '-';

                $colTotals[$k->id] += $amount;
            }
            foreach ($pengambilanData as $p) {
                if ($p->id_user == $user->id) {
                    if (in_array($p->id_kategori, $manasukaIds)) $userTotalPengambilanManasuka += $p->jumlah;
                    if (in_array($p->id_kategori, $lebaranIds)) $userTotalPengambilanLebaran += $p->jumlah;
                }
            }

            $userPinjamanTotal = $pinjamanTotal->get($user->id)->total ?? 0;
            $userPinjamanDibayar = $pinjamanDibayar->get($user->id)->total ?? 0;
            $userPinjamanSisa = max($userPinjamanTotal - $userPinjamanDibayar, 0);

            $rowData[] = $userPinjamanTotal ?: '-';
            $rowData[] = $userPinjamanDibayar ?: '-';
            $rowData[] = $userPinjamanSisa ?: '-';
            $rowData[] = ($bagihasilTotal->get($user->id)->total ?? 0) ?: '-';
            $rowData[] = ($bagihasilDibayar->get($user->id)->total ?? 0) ?: '-';
            $rowData[] = $userTotalPengambilanManasuka ?: '-';
            $rowData[] = $userTotalPengambilanLebaran ?: '-';

            $grandTotalPinjamanTotal += $userPinjamanTotal;
            $grandTotalPinjamanDibayar += $userPinjamanDibayar;
            $grandTotalPinjamanSisa += $userPinjamanSisa;
            $grandTotalBagihasilTotal += ($bagihasilTotal->get($user->id)->total ?? 0);
            $grandTotalBagihasilDibayar += ($bagihasilDibayar->get($user->id)->total ?? 0);
            $grandTotalPengambilanManasuka += $userTotalPengambilanManasuka;
            $grandTotalPengambilanLebaran += $userTotalPengambilanLebaran;

            $data[] = $rowData;
        }

        $totalRow = ['', '', '', ''];
        foreach ($kategoriList as $k) {
            if ($k->jenis && $k->jenis->nama === 'Tagihan' && $k->nama === 'Pinjaman') {
                continue;
            }
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalPinjamanTotal ?: '-';
        $totalRow[] = $grandTotalPinjamanDibayar ?: '-';
        $totalRow[] = $grandTotalPinjamanSisa ?: '-';
        $totalRow[] = $grandTotalBagihasilTotal ?: '-';
        $totalRow[] = $grandTotalBagihasilDibayar ?: '-';
        $totalRow[] = $grandTotalPengambilanManasuka ?: '-';
        $totalRow[] = $grandTotalPengambilanLebaran ?: '-';

        $data[] = $totalRow;

        $filename = 'Laporan Koprasi Bulan ' . $formattedMonth2 . '.xlsx';

        return Excel::download(new FilteredDataExport($data, $formattedMonth, $headings), $filename);
    }
}
