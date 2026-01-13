<?php

namespace App\Http\Controllers;

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
        $simpanan = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))->groupBy('id_user', 'id_kategori')
            ->get();
        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))->groupBy('id_user', 'id_kategori')->get();
        foreach ($simpanan as $key => $value) {
            # code...
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
        }

        foreach ($tagihan as $key => $value) {
            # code...
            $data['tagihan'][$value->id_user][$value->id_kategori] = $value->jumlah;
        }

        return view('pages.laporan', $data);
        // dd($data['simpanan']);
    }

    public function filterData(Request $request)
    {
        $data['kategori'] = Kategori::all();

        $selectedMonth = $request->input('filter_month');
        Session::put('filtered_month', $selectedMonth);
        $formattedMonth = Carbon::parse($selectedMonth)->format('m');

        // Query untuk mendapatkan data sesuai dengan bulan yang dipilih
        $simpanan = TransaksiS::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        $tagihan = TransaksiT::select('id_user', 'id_kategori', DB::raw('SUM(jumlah) AS jumlah'))
            ->whereMonth('tanggal', '=', $formattedMonth)
            ->groupBy('id_user', 'id_kategori')
            ->get();

        // Get unique user IDs from both simpanan and tagihan results
        $userIds = array_merge($simpanan->pluck('id_user')->toArray(), $tagihan->pluck('id_user')->toArray());
        $userIds = array_unique($userIds);

        // Retrieve only the users whose IDs are in the filtered results
        $data['user'] = User::whereIn('id', $userIds)->get();

        $data['simpanan'] = [];
        $data['tagihan'] = [];

        foreach ($simpanan as $value) {
            $data['simpanan'][$value->id_user][$value->id_kategori] = $value->jumlah;
        }

        foreach ($tagihan as $value) {
            $data['tagihan'][$value->id_user][$value->id_kategori] = $value->jumlah;
        }

        return view('pages.laporan', $data);
    }

    //EXPORT ALL DATA
    public function export()
    {
        $users = User::all();
        $data = [];

        foreach ($users as $key => $user) {
            $rowData = [
                'No' => $key + 1,
                'No Anggota' => $user->no_user,
                'Nama' => $user->name,
                'Alamat' => $user->alamat,
                'Iuran Pokok' => $user->iuran_pokok,
                'Iuran Wajib' => $this->getTransactionAmount($user->id, 'Iuran Wajib', TransaksiS::class) ?: '-',
                'Jumlah Simpanan ' . $user->name => $this->getTotalTransactionAmount($user->id, TransaksiS::class) ?: '-',
                'Pinjaman' => $this->getTransactionAmount($user->id, 'pinjaman', TransaksiT::class) ?: '-',
                'Bagi Hasil' => $this->getTransactionAmount($user->id, 'bagihasil', TransaksiT::class) ?: '-',
                'Jumlah Tagihan ' . $user->name => $this->getTotalTransactionAmount($user->id, TransaksiT::class) ?: '-',
            ];

            $data[] = $rowData;
        }

        $totalKategoriSimpananPokok = User::sum('iuran_pokok');
        $totalKategoriSimpananWajib = $this->getTotalByKategori('Iuran Wajib', TransaksiS::class) ?: '-';
        $totalKategoriPinjaman = $this->getTotalByKategori('Pinjaman', TransaksiT::class) ?: '-';
        $totalKategoriBagihasil = $this->getTotalByKategori('Bagihasil', TransaksiT::class);
        $totalSimpananSemuaAnggota = $this->getTotalTransactionAmountSemuaAnggota(TransaksiS::class) ?: '-';
        $totalTagihanSemuaAnggota = $this->getTotalTransactionAmountSemuaAnggota(TransaksiT::class) ?: '-';

        $dataRowSemuaAnggota = [
            'No' => '',
            'No Anggota' => '',
            'Nama' => '',
            'Alamat' => '',
            'Iuran Pokok' => $totalKategoriSimpananPokok,
            'Iuran Wajib' => $totalKategoriSimpananWajib,
            'Jumlah Simpanan' => $totalSimpananSemuaAnggota,
            'Pinjaman' => $totalKategoriPinjaman,
            'Bagi Hasil' => $totalKategoriBagihasil,
            'Jumlah Tagihan' => $totalTagihanSemuaAnggota,
        ];

        $data[] = $dataRowSemuaAnggota;

        return Excel::download(new LaporanExport($data), 'Laporan Master Koperasi.xlsx');
    }

    protected function getTransactionAmount($userId, $kategori, $model)
    {
        $kategoriId = Kategori::where('nama', $kategori)->value('id');

        return $model::where('id_user', $userId)
            ->where('id_kategori', $kategoriId)
            ->sum('jumlah');
    }

    protected function getTotalTransactionAmount($userId, $model)
    {
        if ($model == TransaksiS::class) {
            $jumlahwajib = $model::where('id_user', $userId)
                ->sum('jumlah');
            $jumlahpokok = User::where('id', $userId)
                ->sum('iuran_pokok');
            $totalsimpanan = $jumlahwajib += $jumlahpokok;
        } else {
            $totalsimpanan = $model::where('id_user', $userId)
                ->sum('jumlah');
        }
        return $totalsimpanan;
    }

    protected function getTotalTransactionAmountSemuaAnggota($model)
    {
        $total = 0;

        foreach (User::all() as $user) {
            $total += $this->getTotalTransactionAmount($user->id, $model);
        }

        return $total;
    }

    protected function getTotalByKategori($kategori, $model)
    {
        $total2 = 0;
        foreach (User::all() as $user) {

            $total2 += $this->getTransactionAmount($user->id,  $kategori, $model);
        }

        return $total2;
    }

    //EXPORT FILTER DATA
    public function exportFilteredData(Request $request)
    {
        $selectedMonth = Session::get('filtered_month');
        $formattedMonth = Carbon::parse($selectedMonth)->format('m');
        $formattedMonth2 = Carbon::parse($selectedMonth)->locale('id')->isoFormat('MMMM YYYY');

        // Ambil semua user yang memiliki transaksi pada bulan tertentu
        $userIdsWithTransactions = [];

        // Ambil id pengguna yang melakukan transaksi pada bulan tertentu dari TransaksiS
        $usersWithTransactionsS = TransaksiS::whereMonth('tanggal', $formattedMonth)
            ->pluck('id_user')
            ->toArray();
        $userIdsWithTransactions = array_merge($userIdsWithTransactions, $usersWithTransactionsS);

        // Ambil id pengguna yang melakukan transaksi pada bulan tertentu dari TransaksiT
        $usersWithTransactionsT = TransaksiT::whereMonth('tanggal', $formattedMonth)
            ->pluck('id_user')
            ->toArray();
        $userIdsWithTransactions = array_merge($userIdsWithTransactions, $usersWithTransactionsT);

        // Hilangkan duplikat pengguna yang memiliki transaksi pada bulan tertentu
        $userIdsWithTransactions = array_unique($userIdsWithTransactions);

        // Ambil data pengguna yang memiliki transaksi pada bulan tertentu
        $users = User::whereIn('id', $userIdsWithTransactions)->get();

        $data = [];

        foreach ($users as $key => $user) {
            $rowData = [
                'No' => $key + 1,
                'No Anggota' => $user->no_user,
                'Nama' => $user->name,
                'Alamat' => $user->alamat,
                'Iuran Pokok' => $user->iuran_pokok ?: '-',
                'Iuran Wajib' => $this->getTransactionAmountFiltered($user->id, 'Iuran Wajib', TransaksiS::class, $formattedMonth) ?: '-',
                'Jumlah Simpanan ' . $user->name => $this->getTotalTransactionAmountFiltered($user->id, TransaksiS::class, $formattedMonth) ?: '-',
                'Pinjaman' => $this->getTransactionAmountFiltered($user->id, 'pinjaman', TransaksiT::class, $formattedMonth) ?: '-',
                'Bagi Hasil' => $this->getTransactionAmountFiltered($user->id, 'bagihasil', TransaksiT::class, $formattedMonth) ?: '-',
                'Jumlah Tagihan ' . $user->name => $this->getTotalTransactionAmountFiltered($user->id, TransaksiT::class, $formattedMonth) ?: '-',
            ];

            $data[] = $rowData;
        }

        $totalKategoriSimpananPokok = User::whereIn('id', $userIdsWithTransactions)->sum('iuran_pokok');
        $totalKategoriSimpananWajib = $this->TotalByKategoriFiltered('Iuran Wajib', TransaksiS::class, $formattedMonth) ?: '-';
        $totalKategoriPinjaman = $this->TotalByKategoriFiltered('Pinjaman', TransaksiT::class, $formattedMonth) ?: '-';
        $totalKategoriBagihasil = $this->TotalByKategoriFiltered('Bagihasil', TransaksiT::class, $formattedMonth) ?: '-';
        $totalSimpananSemuaAnggota = $this->AmountSemuaAnggota(TransaksiS::class, $formattedMonth, $userIdsWithTransactions) ?: '-';
        $totalTagihanSemuaAnggota = $this->AmountSemuaAnggota(TransaksiT::class, $formattedMonth, $userIdsWithTransactions) ?: '-';

        $dataRowSemuaAnggota = [
            'No' => '',
            'No Anggota' => '',
            'Nama' => '',
            'Alamat' => '',
            'Iuran Pokok' => $totalKategoriSimpananPokok,
            'Iuran Wajib' => $totalKategoriSimpananWajib,
            'Jumlah Simpanan' => $totalSimpananSemuaAnggota,
            'Pinjaman' => $totalKategoriPinjaman,
            'Bagi Hasil' => $totalKategoriBagihasil,
            'Jumlah Tagihan' => $totalTagihanSemuaAnggota,
        ];

        $data[] = $dataRowSemuaAnggota;

        $filename = 'Laporan Koprasi Bulan ' . $formattedMonth2 . '.xlsx';

        return Excel::download(new FilteredDataExport($data, $formattedMonth), $filename);
    }


    protected function getTransactionAmountFiltered($userId, $kategori, $model, $formattedMonth)
    {
        $kategoriId = Kategori::where('nama', $kategori)->value('id');

        return $model::where('id_user', $userId)
            ->where('id_kategori', $kategoriId)
            ->whereMonth('tanggal', $formattedMonth)
            ->sum('jumlah');
    }

    protected function getTotalTransactionAmountFiltered($userId, $model, $formattedMonth)
    {
        if ($model == TransaksiS::class) {
            $jumlahwajib = $model::where('id_user', $userId)
                ->whereMonth('tanggal', $formattedMonth)
                ->sum('jumlah');
            $jumlahpokok = User::where('id', $userId)
                ->sum('iuran_pokok');
            $totalsimpanan = $jumlahwajib += $jumlahpokok;
        } else {
            $totalsimpanan = $model::where('id_user', $userId)
                ->whereMonth('tanggal', $formattedMonth)
                ->sum('jumlah');
        }
        return $totalsimpanan;
    }

    protected function AmountSemuaAnggota($model, $formattedMonth, $userIdsWithTransactions)
    {
        $total = 0;
        $users = User::whereIn('id', $userIdsWithTransactions)->get();

        foreach ($users as $user) {
            $total += $this->getTotalTransactionAmountFiltered($user->id, $model, $formattedMonth);
        }

        return $total;
    }

    protected function TotalByKategoriFiltered($kategori, $model, $formattedMonth)
    {
        $total2 = 0;
        foreach (User::all() as $user) {

            $total2 += $this->getTransactionAmountFiltered($user->id,  $kategori, $model, $formattedMonth);
        }

        return $total2;
    }
}
