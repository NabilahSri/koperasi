<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\FilteredDataExport;
use App\Exports\LaporanExport;
use App\Models\Kategori;
use App\Models\TransaksiS;
use App\Models\TransaksiT;
use App\Models\User;
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
        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))->groupBy('id_user', 'id_kategori')->get();
        foreach ($simpanan as $key => $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
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

        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->whereYear('tanggal', '=', $formattedYear)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $userIds = array_merge($simpanan->pluck('id_user')->toArray(), $tagihan->pluck('id_user')->toArray());
        $userIds = array_unique($userIds);

        $data['user'] = User::whereIn('id', $userIds)->get();

        $data['simpanan'] = [];
        $data['tagihan'] = [];

        foreach ($simpanan as $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
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

        $colTotals = [];
        foreach ($kategoriList as $k) {
            $colTotals[$k->id] = 0;
        }
        $grandTotalSimpanan = 0;
        $grandTotalTagihan = 0;

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->no_user,
                $user->name,
                $user->alamat,
            ];

            $userTotalSimpanan = 0;
            $userTotalTagihan = 0;

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

            $rowData[] = $userTotalSimpanan ?: '-';
            $rowData[] = $userTotalTagihan ?: '-';

            $grandTotalSimpanan += $userTotalSimpanan;
            $grandTotalTagihan += $userTotalTagihan;

            $data[] = $rowData;
        }

        $totalRow = ['', '', '', ''];
        foreach ($kategoriList as $k) {
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalSimpanan ?: '-';
        $totalRow[] = $grandTotalTagihan ?: '-';

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

        $userIdsWithTransactions = array_merge(
            $simpananData->pluck('id_user')->toArray(),
            $tagihanData->pluck('id_user')->toArray()
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

        $colTotals = [];
        foreach ($kategoriList as $k) {
            $colTotals[$k->id] = 0;
        }
        $grandTotalSimpanan = 0;
        $grandTotalTagihan = 0;

        foreach ($users as $key => $user) {
            $rowData = [
                $key + 1,
                $user->no_user,
                $user->name,
                $user->alamat,
            ];

            $userTotalSimpanan = 0;
            $userTotalTagihan = 0;

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

            $rowData[] = $userTotalSimpanan ?: '-';
            $rowData[] = $userTotalTagihan ?: '-';

            $grandTotalSimpanan += $userTotalSimpanan;
            $grandTotalTagihan += $userTotalTagihan;

            $data[] = $rowData;
        }

        $totalRow = ['', '', '', ''];
        foreach ($kategoriList as $k) {
            $totalRow[] = $colTotals[$k->id] ?: '-';
        }
        $totalRow[] = $grandTotalSimpanan ?: '-';
        $totalRow[] = $grandTotalTagihan ?: '-';

        $data[] = $totalRow;

        $filename = 'Laporan Koprasi Bulan ' . $formattedMonth2 . '.xlsx';

        return Excel::download(new FilteredDataExport($data, $formattedMonth, $headings), $filename);
    }
}
