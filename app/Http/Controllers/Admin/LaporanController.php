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
        $data['user'] = User::all();
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

        $data['user'] = User::whereIn('id', $userIds)->get();

        $data['simpanan'] = [];
        $data['tagihan'] = [];
        $data['pengambilan'] = [];
        $data['pengambilan_total'] = [];
        $data['pengambilan_manasuka_total'] = [];
        $data['pengambilan_lebaran_total'] = [];
        $manasukaIds = Kategori::whereIn('nama', ['Manasuka', 'Simpanan Manasuka'])->pluck('id')->toArray();
        $lebaranIds = Kategori::whereIn('nama', ['Lebaran', 'Simpanan Lebaran'])->pluck('id')->toArray();

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

        return view('admin.pages.laporan', $data);
    }

    public function export()
    {
        $users = User::all();
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
        $headings = ['No', 'No Anggota', 'Nama', 'Alamat'];
        foreach ($kategoriList as $k) {
            $headings[] = $k->nama;
        }
        $headings[] = 'Jumlah Simpanan';
        $headings[] = 'Jumlah Tagihan';
        $headings[] = 'Jumlah Pengambilan Manasuka';
        $headings[] = 'Jumlah Pengambilan Lebaran';

        $colTotals = [];
        foreach ($kategoriList as $k) {
            $colTotals[$k->id] = 0;
        }
        $grandTotalSimpanan = 0;
        $grandTotalTagihan = 0;
        $grandTotalPengambilanManasuka = 0;
        $grandTotalPengambilanLebaran = 0;
        $manasukaIds = [];
        $lebaranIds = [];
        foreach ($kategoriList as $k) {
            if (in_array($k->nama, ['Manasuka', 'Simpanan Manasuka'])) $manasukaIds[] = $k->id;
            if (in_array($k->nama, ['Lebaran', 'Simpanan Lebaran'])) $lebaranIds[] = $k->id;
        }

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->name,
                $user->alamat,
            ];

            $userTotalSimpanan = 0;
            $userTotalTagihan = 0;
            $userTotalPengambilanManasuka = 0;
            $userTotalPengambilanLebaran = 0;

            foreach ($kategoriList as $k) {
                $amount = $lookup[$user->id][$k->id] ?? 0;
                $rowData[] = $amount ?: '-';

                $colTotals[$k->id] += $amount;

                if ($k->jenis && stripos($k->jenis->nama, 'Simpanan') !== false) {
                    $userTotalSimpanan += $amount;
                } else {
                    $userTotalTagihan += $amount;
                }
            }
            foreach ($pengambilanData as $p) {
                if ($p->id_user == $user->id) {
                    if (in_array($p->id_kategori, $manasukaIds)) $userTotalPengambilanManasuka += $p->jumlah;
                    if (in_array($p->id_kategori, $lebaranIds)) $userTotalPengambilanLebaran += $p->jumlah;
                }
            }

            $rowData[] = $userTotalSimpanan ?: '-';
            $rowData[] = $userTotalTagihan ?: '-';
            $rowData[] = $userTotalPengambilanManasuka ?: '-';
            $rowData[] = $userTotalPengambilanLebaran ?: '-';

            $grandTotalSimpanan += $userTotalSimpanan;
            $grandTotalTagihan += $userTotalTagihan;
            $grandTotalPengambilanManasuka += $userTotalPengambilanManasuka;
            $grandTotalPengambilanLebaran += $userTotalPengambilanLebaran;

            $data[] = $rowData;
        }

        $totalRow = ['', '', '', ''];
        foreach ($kategoriList as $k) {
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalSimpanan ?: '-';
        $totalRow[] = $grandTotalTagihan ?: '-';
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

        $users = User::whereIn('id', $userIdsWithTransactions)->get();

        $lookup = [];
        foreach ($simpananData as $s) {
            $lookup[$s->id_user][$s->id_kategori] = $s->jumlah;
        }
        foreach ($tagihanData as $t) {
            $lookup[$t->id_user][$t->id_kategori] = $t->jumlah;
        }

        $data = [];
        $headings = ['No', 'No Anggota', 'Nama', 'Alamat'];
        foreach ($kategoriList as $k) {
            $headings[] = $k->nama;
        }
        $headings[] = 'Jumlah Simpanan';
        $headings[] = 'Jumlah Tagihan';
        $headings[] = 'Jumlah Pengambilan Manasuka';
        $headings[] = 'Jumlah Pengambilan Lebaran';

        $colTotals = [];
        foreach ($kategoriList as $k) {
            $colTotals[$k->id] = 0;
        }
        $grandTotalSimpanan = 0;
        $grandTotalTagihan = 0;
        $grandTotalPengambilanManasuka = 0;
        $grandTotalPengambilanLebaran = 0;
        $manasukaIds = [];
        $lebaranIds = [];
        foreach ($kategoriList as $k) {
            if (in_array($k->nama, ['Manasuka', 'Simpanan Manasuka'])) $manasukaIds[] = $k->id;
            if (in_array($k->nama, ['Lebaran', 'Simpanan Lebaran'])) $lebaranIds[] = $k->id;
        }

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->name,
                $user->alamat,
            ];

            $userTotalSimpanan = 0;
            $userTotalTagihan = 0;
            $userTotalPengambilanManasuka = 0;
            $userTotalPengambilanLebaran = 0;

            foreach ($kategoriList as $k) {
                $amount = $lookup[$user->id][$k->id] ?? 0;
                $rowData[] = $amount ?: '-';

                $colTotals[$k->id] += $amount;

                if ($k->jenis && stripos($k->jenis->nama, 'Simpanan') !== false) {
                    $userTotalSimpanan += $amount;
                } else {
                    $userTotalTagihan += $amount;
                }
            }
            foreach ($pengambilanData as $p) {
                if ($p->id_user == $user->id) {
                    if (in_array($p->id_kategori, $manasukaIds)) $userTotalPengambilanManasuka += $p->jumlah;
                    if (in_array($p->id_kategori, $lebaranIds)) $userTotalPengambilanLebaran += $p->jumlah;
                }
            }

            $rowData[] = $userTotalSimpanan ?: '-';
            $rowData[] = $userTotalTagihan ?: '-';
            $rowData[] = $userTotalPengambilanManasuka ?: '-';
            $rowData[] = $userTotalPengambilanLebaran ?: '-';

            $grandTotalSimpanan += $userTotalSimpanan;
            $grandTotalTagihan += $userTotalTagihan;
            $grandTotalPengambilanManasuka += $userTotalPengambilanManasuka;
            $grandTotalPengambilanLebaran += $userTotalPengambilanLebaran;

            $data[] = $rowData;
        }

        $totalRow = ['', '', '', ''];
        foreach ($kategoriList as $k) {
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalSimpanan ?: '-';
        $totalRow[] = $grandTotalTagihan ?: '-';
        $totalRow[] = $grandTotalPengambilanManasuka ?: '-';
        $totalRow[] = $grandTotalPengambilanLebaran ?: '-';

        $data[] = $totalRow;

        $filename = 'Laporan Koprasi Bulan ' . $formattedMonth2 . '.xlsx';

        return Excel::download(new FilteredDataExport($data, $formattedMonth, $headings), $filename);
    }
}
