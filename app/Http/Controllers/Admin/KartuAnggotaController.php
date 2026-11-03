<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\Lembaga;
use App\Models\Pengajuan;
use App\Models\PengambilanSimpanan;
use App\Models\TransaksiS;
use App\Models\TransaksiT;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KartuAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', 'anggota')
            ->active()
            ->orderBy('no_user')
            ->get();

        $selectedUserId = $request->get('user_id');

        if (!$selectedUserId && $users->isNotEmpty()) {
            $selectedUserId = $users->first()->id;
        }

        $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;
        $lembaga = Lembaga::first();

        $tagihanRows = [];
        $simpananRows = [];

        $totalPinjaman = 0;
        $totalAngsuran = 0;
        $totalBagiHasil = 0;
        $totalPropisi = 0;
        $sisaUtangAkhir = 0;

        $totalSimpPokok = 0;
        $totalSimpWajib = 0;
        $totalSimpManasuka = 0;
        $totalSimpLebaran = 0;
        $totalSimpNiaga = 0;
        $totalSimpananKotor = 0;
        $totalPengambilan = 0;
        $saldoSimpananAkhir = 0;

        $pengajuanAktif = collect();
        $pengajuanList = collect();
        $kategoriSimpanan = Kategori::where('id_jenis', 1)->get();
        $kategoriTagihan = Kategori::where('id_jenis', 2)->get();

        if ($selectedUser) {
            $pengajuanAktif = Pengajuan::where('id_user', $selectedUser->id)
                ->where('keterangan', 'belum lunas')
                ->get();

            // 1. Ambil & Susun Data Tagihan
            $pengajuanList = Pengajuan::where('id_user', $selectedUser->id)
                ->orderBy('tanggal_pengajuan', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $transaksiTagihanList = TransaksiT::with(['kategori', 'pengajuan'])
                ->where('id_user', $selectedUser->id)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $tagihanEvents = collect();

            foreach ($pengajuanList as $pj) {
                $tagihanEvents->push([
                    'type' => 'pinjaman',
                    'date' => $pj->tanggal_pengajuan,
                    'created_at' => $pj->created_at,
                    'id' => $pj->id,
                    'id_pengajuan' => $pj->id,
                    'group_ids' => (string) $pj->id,
                    'nominal_pinjaman' => (int) $pj->nominal_pinjaman,
                    'propisi' => (int) ($pj->propisi ?? 0),
                    'nominal_bagihasil_master' => (int) ($pj->nominal_bagihasil ?? 0),
                    'cicilan' => 0,
                    'bagi_hasil' => 0,
                    'keterangan' => $pj->keterangan,
                    'paraf' => 'Admin',
                ]);
                $totalPinjaman += (int) $pj->nominal_pinjaman;
                $totalPropisi += (int) ($pj->propisi ?? 0);
            }

            // Group tagihan by tanggal & id_pengajuan agar cicilan + bagi hasil di tanggal yang sama tampil 1 baris
            $groupedTagihan = $transaksiTagihanList->where('jumlah', '>', 0)->groupBy(function ($item) {
                return $item->tanggal . '_' . $item->id_pengajuan;
            });

            $katPinjaman = Kategori::where('nama', 'Pinjaman')->first();
            $idKatPinjaman = $katPinjaman ? $katPinjaman->id : 3;

            foreach ($groupedTagihan as $group) {
                $first = $group->first();
                $cicilan = 0;
                $bagiHasil = 0;

                foreach ($group as $t) {
                    $namaKat = strtolower($t->kategori->nama ?? '');
                    if (str_contains($namaKat, 'pinjam') || $t->id_kategori == $idKatPinjaman) {
                        $cicilan += (int) $t->jumlah;
                    } else {
                        $bagiHasil += (int) $t->jumlah;
                    }
                }

                $tagihanEvents->push([
                    'type' => 'angsuran',
                    'date' => $first->tanggal,
                    'created_at' => $first->created_at,
                    'id' => $first->id,
                    'id_pengajuan' => $first->id_pengajuan,
                    'group_ids' => $group->pluck('id')->implode(','),
                    'nominal_pinjaman' => 0,
                    'propisi' => 0,
                    'nominal_bagihasil_master' => 0,
                    'cicilan' => $cicilan,
                    'bagi_hasil' => $bagiHasil,
                    'keterangan' => $first->keterangan ?? 'Bayar Angsuran',
                    'paraf' => 'Admin',
                ]);

                $totalAngsuran += $cicilan;
                $totalBagiHasil += $bagiHasil;
            }

            // Urutkan cronologis event tagihan
            $sortedTagihan = $tagihanEvents->sortBy(function ($item) {
                return $item['date'] . '_' . ($item['type'] === 'pinjaman' ? '0' : '1') . '_' . $item['created_at'];
            });

            $currentUtang = 0;
            foreach ($sortedTagihan as $item) {
                if ($item['type'] === 'pinjaman') {
                    $currentUtang += $item['nominal_pinjaman'];
                } else {
                    $currentUtang = max(0, $currentUtang - $item['cicilan']);
                }

                $tagihanRows[] = [
                    'type' => $item['type'],
                    'id' => $item['id'],
                    'id_pengajuan' => $item['id_pengajuan'],
                    'group_ids' => $item['group_ids'],
                    'tanggal' => $item['date'],
                    'besar_pinjaman' => $item['nominal_pinjaman'] > 0 ? $item['nominal_pinjaman'] : null,
                    'nominal_bagihasil_master' => $item['nominal_bagihasil_master'] ?? 0,
                    'cicilan' => $item['cicilan'] > 0 ? $item['cicilan'] : null,
                    'sisa_utang' => $currentUtang,
                    'bagi_hasil' => $item['bagi_hasil'] > 0 ? $item['bagi_hasil'] : null,
                    'propisi' => $item['propisi'] > 0 ? $item['propisi'] : null,
                    'paraf' => $item['paraf'],
                    'keterangan' => $item['keterangan'],
                ];
            }
            $sisaUtangAkhir = $currentUtang;

            // 2. Ambil & Susun Data Kekayaan Anggota (Simpanan & Pengambilan)
            $transaksiSimpananList = TransaksiS::with(['kategori', 'petugas'])
                ->where('id_user', $selectedUser->id)
                ->where('jumlah', '>', 0)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $pengambilanList = PengambilanSimpanan::with(['kategori', 'petugas'])
                ->where('id_user', $selectedUser->id)
                ->where('jumlah', '>', 0)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $simpananEvents = collect();

            foreach ($transaksiSimpananList as $ts) {
                $katNama = strtolower($ts->kategori->nama ?? '');
                $nominal = (int) $ts->jumlah;
                $colKey = 'manasuka';

                if (str_contains($katNama, 'pokok')) {
                    $colKey = 'pokok';
                    $totalSimpPokok += $nominal;
                } elseif (str_contains($katNama, 'wajib')) {
                    $colKey = 'wajib';
                    $totalSimpWajib += $nominal;
                } elseif (str_contains($katNama, 'manasuka')) {
                    $colKey = 'manasuka';
                    $totalSimpManasuka += $nominal;
                } elseif (str_contains($katNama, 'lebaran')) {
                    $colKey = 'lebaran';
                    $totalSimpLebaran += $nominal;
                } elseif (str_contains($katNama, 'niaga')) {
                    $colKey = 'niaga';
                    $totalSimpNiaga += $nominal;
                } else {
                    $colKey = 'manasuka';
                    $totalSimpManasuka += $nominal;
                }

                $totalSimpananKotor += $nominal;

                $simpananEvents->push([
                    'type' => 'simpanan',
                    'col' => $colKey,
                    'date' => $ts->tanggal,
                    'created_at' => $ts->created_at,
                    'id' => $ts->id,
                    'id_kategori' => $ts->id_kategori,
                    'kategori_nama' => $ts->kategori->nama ?? ucfirst($colKey),
                    'nominal_raw' => $nominal,
                    'nama_pihak' => $ts->nama_penyetor ?: ($selectedUser->name ?? ''),
                    'paraf' => $ts->petugas->name ?? 'Admin',
                    'keterangan' => $ts->keterangan ?? 'Setoran',
                ]);
            }

            foreach ($pengambilanList as $ps) {
                $jumlahAmbil = (int) $ps->jumlah;
                $totalPengambilan += $jumlahAmbil;

                $simpananEvents->push([
                    'type' => 'pengambilan',
                    'col' => 'pengambilan',
                    'date' => $ps->tanggal,
                    'created_at' => $ps->created_at,
                    'id' => $ps->id,
                    'id_kategori' => $ps->id_kategori,
                    'kategori_nama' => $ps->kategori->nama ?? 'Penarikan Simpanan',
                    'nominal_raw' => $jumlahAmbil,
                    'nama_pihak' => $ps->nama_pengambil ?: ($selectedUser->name ?? ''),
                    'paraf' => $ps->petugas->name ?? 'Admin',
                    'keterangan' => $ps->keterangan ?? 'Penarikan Simpanan',
                ]);
            }

            // Gabungkan transaksi Kekayaan Anggota yang memiliki tanggal yang sama menjadi 1 baris
            $groupedSimpananByDate = $simpananEvents
                ->sortBy(function ($item) {
                    return $item['date'] . '_' . ($item['type'] === 'simpanan' ? '0' : '1') . '_' . $item['id'];
                })
                ->groupBy('date');

            $currentSaldoSimpanan = 0;
            $colKeys = ['pokok', 'wajib', 'manasuka', 'lebaran', 'niaga', 'pengambilan'];

            foreach ($groupedSimpananByDate as $date => $eventsOnDate) {
                $colTotals = [
                    'pokok' => 0,
                    'wajib' => 0,
                    'manasuka' => 0,
                    'lebaran' => 0,
                    'niaga' => 0,
                    'pengambilan' => 0,
                ];
                $cells = [];

                foreach ($colKeys as $col) {
                    $colEvents = $eventsOnDate->where('col', $col);
                    if ($colEvents->isNotEmpty()) {
                        $sumCol = (int) $colEvents->sum('nominal_raw');
                        $colTotals[$col] = $sumCol;
                        $firstCol = $colEvents->first();

                        $cells[$col] = [
                            'type' => $firstCol['type'],
                            'id' => $firstCol['id'],
                            'group_ids' => $colEvents->pluck('id')->implode(','),
                            'id_kategori' => $firstCol['id_kategori'],
                            'kategori_nama' => $firstCol['kategori_nama'],
                            'nominal' => $sumCol,
                            'tanggal' => $date,
                            'nama_pihak' => $firstCol['nama_pihak'],
                            'keterangan' => $firstCol['keterangan'],
                        ];
                    }
                }

                $totalMasukHariIni = $colTotals['pokok'] + $colTotals['wajib'] + $colTotals['manasuka'] + $colTotals['lebaran'] + $colTotals['niaga'];
                $currentSaldoSimpanan = max(0, $currentSaldoSimpanan + $totalMasukHariIni - $colTotals['pengambilan']);

                $firstEvent = $eventsOnDate->first();
                $singleCell = count($cells) === 1 ? reset($cells) : null;

                $simpananRows[] = [
                    'tanggal' => $date,
                    'pokok' => $colTotals['pokok'] > 0 ? $colTotals['pokok'] : null,
                    'wajib' => $colTotals['wajib'] > 0 ? $colTotals['wajib'] : null,
                    'manasuka' => $colTotals['manasuka'] > 0 ? $colTotals['manasuka'] : null,
                    'lebaran' => $colTotals['lebaran'] > 0 ? $colTotals['lebaran'] : null,
                    'niaga' => $colTotals['niaga'] > 0 ? $colTotals['niaga'] : null,
                    'jumlah_simpanan' => $currentSaldoSimpanan,
                    'pengambilan' => $colTotals['pengambilan'] > 0 ? $colTotals['pengambilan'] : null,
                    'paraf' => $firstEvent['paraf'] ?? 'Admin',
                    'keterangan' => $firstEvent['keterangan'] ?? '',
                    'cells' => $cells,
                    'single_cell' => $singleCell,
                ];
            }
            $saldoSimpananAkhir = $currentSaldoSimpanan;
        }

        // Pembagian per Lembar (Masing-masing tepat 25 baris per lembar)
        $lembarList = [];
        $totalTransactions = max(count($tagihanRows), count($simpananRows));
        $totalLembar = $totalTransactions <= 25 ? 1 : 1 + (int) ceil(($totalTransactions - 25) / 24);

        $tagihanIndex = 0;
        $simpananIndex = 0;
        $lastSisaUtang = 0;
        $lastSaldoSimpanan = 0;

        for ($p = 1; $p <= $totalLembar; $p++) {
            $isFirstLembar = ($p === 1);
            $capacity = $isFirstLembar ? 25 : 24;

            $pindahanRow = null;
            if (!$isFirstLembar) {
                $pindahanRow = [
                    'tagihan' => [
                        'tanggal' => '-',
                        'besar_pinjaman' => null,
                        'cicilan' => null,
                        'sisa_utang' => $lastSisaUtang,
                        'bagi_hasil' => null,
                        'propisi' => null,
                        'paraf' => '-',
                        'keterangan' => 'Pindahan dari Lembar ' . ($p - 1),
                    ],
                    'simpanan' => [
                        'tanggal' => '-',
                        'pokok' => null,
                        'wajib' => null,
                        'manasuka' => null,
                        'lebaran' => null,
                        'niaga' => null,
                        'jumlah_simpanan' => $lastSaldoSimpanan,
                        'pengambilan' => null,
                        'paraf' => '-',
                        'keterangan' => 'Pindahan dari Lembar ' . ($p - 1),
                    ],
                ];
            }

            $lembarTagihan = array_slice($tagihanRows, $tagihanIndex, $capacity);
            $lembarSimpanan = array_slice($simpananRows, $simpananIndex, $capacity);

            // Catat sisa utang & saldo simpanan berjalan di akhir lembar ini
            foreach ($lembarTagihan as $lt) {
                $lastSisaUtang = $lt['sisa_utang'];
            }
            foreach ($lembarSimpanan as $ls) {
                $lastSaldoSimpanan = $ls['jumlah_simpanan'];
            }

            $transactionRows = [];
            for ($i = 0; $i < $capacity; $i++) {
                $transactionRows[] = [
                    'tagihan' => $lembarTagihan[$i] ?? null,
                    'simpanan' => $lembarSimpanan[$i] ?? null,
                ];
            }

            $tagihanIndex += $capacity;
            $simpananIndex += $capacity;

            $lembarList[] = [
                'nomor_lembar' => $p,
                'is_first' => $isFirstLembar,
                'pindahan' => $pindahanRow,
                'rows' => $transactionRows,
                'sisa_utang_lembar' => $lastSisaUtang,
                'saldo_simpanan_lembar' => $lastSaldoSimpanan,
            ];
        }

        return view('admin.pages.kartu_anggota', compact(
            'users',
            'selectedUser',
            'lembaga',
            'tagihanRows',
            'simpananRows',
            'lembarList',
            'totalLembar',
            'totalPinjaman',
            'totalAngsuran',
            'totalBagiHasil',
            'totalPropisi',
            'sisaUtangAkhir',
            'totalSimpPokok',
            'totalSimpWajib',
            'totalSimpManasuka',
            'totalSimpLebaran',
            'totalSimpNiaga',
            'totalSimpananKotor',
            'totalPengambilan',
            'saldoSimpananAkhir',
            'pengajuanAktif',
            'pengajuanList',
            'kategoriSimpanan',
            'kategoriTagihan'
        ));
    }

    public function storeSimpanan(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id,is_active,1',
            'id_kategori' => 'required|exists:kategori,id',
            'jumlah' => 'required',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'nama_penyetor' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->id_user);
        $nominal = (int) preg_replace('/[^0-9]/', '', (string) $request->jumlah);

        if ($nominal <= 0) {
            return redirect()->back()->with('error', 'Nominal simpanan harus lebih dari 0.');
        }

        TransaksiS::create([
            'id_user' => $user->id,
            'id_kategori' => $request->id_kategori,
            'id_petugas' => Auth::id() ?? 1,
            'nama_penyetor' => $request->nama_penyetor ?: $user->name,
            'jumlah' => $nominal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?: 'Setoran Simpanan',
        ]);

        return redirect()->route('kartu.anggota', ['user_id' => $user->id])
            ->with('success', 'Setoran simpanan berhasil dicatat.');
    }

    public function storePengambilan(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id,is_active,1',
            'id_kategori' => 'required|exists:kategori,id',
            'jumlah' => 'required',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'nama_pengambil' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->id_user);
        $nominal = (int) preg_replace('/[^0-9]/', '', (string) $request->jumlah);

        if ($nominal <= 0) {
            return redirect()->back()->with('error', 'Nominal penarikan harus lebih dari 0.');
        }

        // Cek Saldo Kategori
        $totalMasuk = TransaksiS::where('id_user', $user->id)
            ->where('id_kategori', $request->id_kategori)
            ->sum('jumlah');

        $totalKeluar = PengambilanSimpanan::where('id_user', $user->id)
            ->where('id_kategori', $request->id_kategori)
            ->sum('jumlah');

        $saldoTersedia = $totalMasuk - $totalKeluar;

        if ($nominal > $saldoTersedia) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi. Saldo kategori ini saat ini: Rp ' . number_format($saldoTersedia, 0, ',', '.'));
        }

        PengambilanSimpanan::create([
            'id_user' => $user->id,
            'id_kategori' => $request->id_kategori,
            'id_petugas' => Auth::id() ?? 1,
            'nama_pengambil' => $request->nama_pengambil ?: $user->name,
            'jumlah' => $nominal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?: 'Penarikan Simpanan',
        ]);

        return redirect()->route('kartu.anggota', ['user_id' => $user->id])
            ->with('success', 'Penarikan simpanan berhasil dicatat.');
    }

    public function storePinjaman(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id,is_active,1',
            'nominal_pinjaman' => 'required',
            'propisi' => 'nullable',
            'nominal_bagihasil' => 'nullable',
            'tanggal_pengajuan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->id_user);
        $nominalPinjaman = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_pinjaman);
        $propisi = (int) preg_replace('/[^0-9]/', '', (string) $request->propisi);
        $nominalBagihasil = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_bagihasil);

        if ($nominalPinjaman <= 0) {
            return redirect()->back()->with('error', 'Nominal pinjaman harus lebih dari 0.');
        }

        if ($request->propisi === null || $request->propisi === '') {
            $propisi = (int) round($nominalPinjaman * 0.01);
        }

        Pengajuan::create([
            'id_user' => $user->id,
            'nominal_pinjaman' => $nominalPinjaman,
            'propisi' => $propisi,
            'nominal_bagihasil' => $nominalBagihasil,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'keterangan' => 'belum lunas',
        ]);

        return redirect()->route('kartu.anggota', ['user_id' => $user->id])
            ->with('success', 'Pinjaman baru berhasil ditambahkan.');
    }

    public function storeBayarAngsuran(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id,is_active,1',
            'id_pengajuan' => 'required|exists:pengajuan,id',
            'nominal_angsuran' => 'required',
            'nominal_bagihasil' => 'nullable',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->id_user);
        $pengajuan = Pengajuan::where('id', $request->id_pengajuan)
            ->where('id_user', $user->id)
            ->firstOrFail();

        $cicilan = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_angsuran);
        $bagiHasil = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_bagihasil);

        if ($cicilan <= 0 && $bagiHasil <= 0) {
            return redirect()->back()->with('error', 'Masukkan nominal angsuran atau bagi hasil.');
        }

        $katPinjaman = Kategori::where('nama', 'Pinjaman')->first();
        $idKatPinjaman = $katPinjaman ? $katPinjaman->id : 3;

        $katBagiHasil = Kategori::where('nama', 'Bagi hasil')->orWhere('nama', 'Bagihasil')->first();
        $idKatBagiHasil = $katBagiHasil ? $katBagiHasil->id : 4;

        if ($cicilan > 0) {
            TransaksiT::create([
                'id_user' => $user->id,
                'id_pengajuan' => $pengajuan->id,
                'id_kategori' => $idKatPinjaman,
                'jumlah' => $cicilan,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan ?: 'Cicilan Pokok',
            ]);
        }

        if ($bagiHasil > 0) {
            TransaksiT::create([
                'id_user' => $user->id,
                'id_pengajuan' => $pengajuan->id,
                'id_kategori' => $idKatBagiHasil,
                'jumlah' => $bagiHasil,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan ?: 'Bagi Hasil',
            ]);
        }

        // Cek pelunasan
        $totalBayarPinjaman = TransaksiT::where('id_pengajuan', $pengajuan->id)
            ->where('id_kategori', $idKatPinjaman)
            ->sum('jumlah');

        if ($totalBayarPinjaman >= $pengajuan->nominal_pinjaman) {
            $pengajuan->update(['keterangan' => 'sudah lunas']);
        }

        return redirect()->route('kartu.anggota', ['user_id' => $user->id])
            ->with('success', 'Pembayaran angsuran berhasil dicatat.');
    }

    public function updateCatatan(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'catatan' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->id_user);
        $user->catatan = $request->catatan;
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan.']);
        }

        return redirect()->route('kartu.anggota', ['user_id' => $user->id])
            ->with('success', 'Catatan berhasil diperbarui.');
    }

    public function getSaldoKategori($userId, $kategoriId)
    {
        $totalMasuk = TransaksiS::where('id_user', $userId)
            ->where('id_kategori', $kategoriId)
            ->sum('jumlah');

        $totalKeluar = PengambilanSimpanan::where('id_user', $userId)
            ->where('id_kategori', $kategoriId)
            ->sum('jumlah');

        $saldo = max(0, $totalMasuk - $totalKeluar);

        return response()->json([
            'saldo' => $saldo,
            'saldo_rp' => 'Rp ' . number_format($saldo, 0, ',', '.'),
        ]);
    }

    public function updateSimpanan(Request $request, $id)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'jumlah' => 'required',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'nama_penyetor' => 'nullable|string',
        ]);

        $simpanan = TransaksiS::findOrFail($id);
        $userId = $simpanan->id_user;
        $oldKategoriId = $simpanan->id_kategori;
        $newKategoriId = (int) $request->id_kategori;
        $nominalBaru = (int) preg_replace('/[^0-9]/', '', (string) $request->jumlah);

        $groupIds = $request->filled('group_ids')
            ? array_filter(array_map('intval', explode(',', $request->group_ids)))
            : [$simpanan->id];
        if (empty($groupIds)) {
            $groupIds = [$simpanan->id];
        }

        if ($nominalBaru <= 0) {
            return redirect()->back()->with('error', 'Nominal simpanan harus lebih dari 0.');
        }

        // Jika kategori diubah, pastikan saldo kategori lama tidak menjadi minus
        if ($oldKategoriId != $newKategoriId) {
            $masukLamaSelainIni = TransaksiS::where('id_user', $userId)
                ->where('id_kategori', $oldKategoriId)
                ->whereNotIn('id', $groupIds)
                ->sum('jumlah');
            $keluarLama = PengambilanSimpanan::where('id_user', $userId)
                ->where('id_kategori', $oldKategoriId)
                ->sum('jumlah');

            if ($masukLamaSelainIni < $keluarLama) {
                return redirect()->back()->with('error', 'Tidak dapat memindahkan kategori setoran ini karena saldo pada kategori asal sudah pernah ditarik dan akan menjadi minus.');
            }
        } else {
            // Jika kategori sama namun nominal dikurangi, pastikan total masuk masih >= total keluar
            $masukSelainIni = TransaksiS::where('id_user', $userId)
                ->where('id_kategori', $oldKategoriId)
                ->whereNotIn('id', $groupIds)
                ->sum('jumlah');
            $keluar = PengambilanSimpanan::where('id_user', $userId)
                ->where('id_kategori', $oldKategoriId)
                ->sum('jumlah');

            if (($masukSelainIni + $nominalBaru) < $keluar) {
                return redirect()->back()->with('error', 'Nominal simpanan tidak boleh lebih kecil dari total penarikan yang sudah dilakukan pada kategori ini.');
            }
        }

        $simpanan->update([
            'id_kategori' => $newKategoriId,
            'nama_penyetor' => $request->nama_penyetor ?: $simpanan->nama_penyetor,
            'jumlah' => $nominalBaru,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?: 'Setoran Simpanan',
        ]);

        // Jika sebelumnya ada duplikat di tanggal & kategori yang sama dalam sel ini, hapus sisanya agar total sesuai
        $extraIds = array_diff($groupIds, [$simpanan->id]);
        if (!empty($extraIds)) {
            TransaksiS::where('id_user', $userId)->whereIn('id', $extraIds)->delete();
        }

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data setoran simpanan berhasil diperbarui.');
    }

    public function deleteSimpanan(Request $request, $id)
    {
        $simpanan = TransaksiS::findOrFail($id);
        $userId = $simpanan->id_user;

        $groupIds = $request->filled('group_ids')
            ? array_filter(array_map('intval', explode(',', $request->group_ids)))
            : [$simpanan->id];
        if (empty($groupIds)) {
            $groupIds = [$simpanan->id];
        }

        $masukSelainIni = TransaksiS::where('id_user', $userId)
            ->where('id_kategori', $simpanan->id_kategori)
            ->whereNotIn('id', $groupIds)
            ->sum('jumlah');
        $keluar = PengambilanSimpanan::where('id_user', $userId)
            ->where('id_kategori', $simpanan->id_kategori)
            ->sum('jumlah');

        if ($masukSelainIni < $keluar) {
            return redirect()->back()->with('error', 'Setoran ini tidak dapat dihapus karena sebagian/seluruh saldonya sudah pernah ditarik (saldo kategori akan menjadi minus).');
        }

        $records = TransaksiS::where('id_user', $userId)->whereIn('id', $groupIds)->get();
        foreach ($records as $rec) {
            $rec->delete();
        }

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data setoran simpanan berhasil dihapus.');
    }

    public function updatePengambilan(Request $request, $id)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'jumlah' => 'required',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'nama_pengambil' => 'nullable|string',
        ]);

        $pengambilan = PengambilanSimpanan::findOrFail($id);
        $userId = $pengambilan->id_user;
        $newKategoriId = (int) $request->id_kategori;
        $nominalBaru = (int) preg_replace('/[^0-9]/', '', (string) $request->jumlah);

        $groupIds = $request->filled('group_ids')
            ? array_filter(array_map('intval', explode(',', $request->group_ids)))
            : [$pengambilan->id];
        if (empty($groupIds)) {
            $groupIds = [$pengambilan->id];
        }

        if ($nominalBaru <= 0) {
            return redirect()->back()->with('error', 'Nominal penarikan harus lebih dari 0.');
        }

        $totalMasuk = TransaksiS::where('id_user', $userId)
            ->where('id_kategori', $newKategoriId)
            ->sum('jumlah');

        $totalKeluarSelainIni = PengambilanSimpanan::where('id_user', $userId)
            ->where('id_kategori', $newKategoriId)
            ->whereNotIn('id', $groupIds)
            ->sum('jumlah');

        $saldoTersedia = $totalMasuk - $totalKeluarSelainIni;

        if ($nominalBaru > $saldoTersedia) {
            return redirect()->back()->with('error', 'Saldo tidak mencukupi untuk perubahan ini. Saldo tersedia di kategori tersebut: Rp ' . number_format(max(0, $saldoTersedia), 0, ',', '.'));
        }

        $pengambilan->update([
            'id_kategori' => $newKategoriId,
            'nama_pengambil' => $request->nama_pengambil ?: $pengambilan->nama_pengambil,
            'jumlah' => $nominalBaru,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan ?: 'Penarikan Simpanan',
        ]);

        $extraIds = array_diff($groupIds, [$pengambilan->id]);
        if (!empty($extraIds)) {
            PengambilanSimpanan::where('id_user', $userId)->whereIn('id', $extraIds)->delete();
        }

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data penarikan simpanan berhasil diperbarui.');
    }

    public function deletePengambilan(Request $request, $id)
    {
        $pengambilan = PengambilanSimpanan::findOrFail($id);
        $userId = $pengambilan->id_user;

        $groupIds = $request->filled('group_ids')
            ? array_filter(array_map('intval', explode(',', $request->group_ids)))
            : [$pengambilan->id];
        if (empty($groupIds)) {
            $groupIds = [$pengambilan->id];
        }

        $records = PengambilanSimpanan::where('id_user', $userId)->whereIn('id', $groupIds)->get();
        foreach ($records as $rec) {
            $rec->delete();
        }

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data penarikan simpanan berhasil dihapus.');
    }

    public function updatePinjaman(Request $request, $id)
    {
        $request->validate([
            'nominal_pinjaman' => 'required',
            'propisi' => 'nullable',
            'nominal_bagihasil' => 'nullable',
            'tanggal_pengajuan' => 'required|date',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        $userId = $pengajuan->id_user;

        $nominalPinjaman = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_pinjaman);
        $propisi = (int) preg_replace('/[^0-9]/', '', (string) $request->propisi);
        $nominalBagihasil = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_bagihasil);

        if ($nominalPinjaman <= 0) {
            return redirect()->back()->with('error', 'Nominal pinjaman harus lebih dari 0.');
        }

        if ($request->propisi === null || $request->propisi === '') {
            $propisi = (int) round($nominalPinjaman * 0.01);
        }

        $pengajuan->update([
            'nominal_pinjaman' => $nominalPinjaman,
            'propisi' => $propisi,
            'nominal_bagihasil' => $nominalBagihasil,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
        ]);

        $this->syncStatusPengajuan($pengajuan->id);

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data pinjaman berhasil diperbarui.');
    }

    public function deletePinjaman(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $userId = $pengajuan->id_user;

        // Hapus seluruh transaksi angsuran yang terkait dengan pinjaman ini
        $angsuranTerkait = TransaksiT::where('id_pengajuan', $pengajuan->id)->get();
        foreach ($angsuranTerkait as $angsuran) {
            $angsuran->delete();
        }

        $pengajuan->delete();

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data pinjaman beserta riwayat angsurannya berhasil dihapus.');
    }

    public function updateBayarAngsuran(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'group_ids' => 'required|string',
            'id_pengajuan' => 'required|exists:pengajuan,id',
            'nominal_angsuran' => 'nullable',
            'nominal_bagihasil' => 'nullable',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $userId = (int) $request->id_user;
        $groupIds = array_filter(array_map('intval', explode(',', $request->group_ids)));

        $existingRecords = TransaksiT::where('id_user', $userId)
            ->whereIn('id', $groupIds)
            ->get();

        if ($existingRecords->isEmpty()) {
            return redirect()->back()->with('error', 'Data angsuran tidak ditemukan.');
        }

        $pengajuanBaru = Pengajuan::where('id', $request->id_pengajuan)
            ->where('id_user', $userId)
            ->firstOrFail();

        $cicilan = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_angsuran);
        $bagiHasil = (int) preg_replace('/[^0-9]/', '', (string) $request->nominal_bagihasil);

        if ($cicilan <= 0 && $bagiHasil <= 0) {
            return redirect()->back()->with('error', 'Masukkan minimal nominal angsuran pokok atau bagi hasil.');
        }

        $katPinjaman = Kategori::where('nama', 'Pinjaman')->first();
        $idKatPinjaman = $katPinjaman ? $katPinjaman->id : 3;

        $katBagiHasil = Kategori::where('nama', 'Bagi hasil')->orWhere('nama', 'Bagihasil')->first();
        $idKatBagiHasil = $katBagiHasil ? $katBagiHasil->id : 4;

        $oldPengajuanIds = $existingRecords->pluck('id_pengajuan')->unique()->toArray();
        $refCreatedAt = $existingRecords->first()->created_at;

        $recordCicilan = $existingRecords->first(function ($item) use ($idKatPinjaman) {
            $namaKat = strtolower($item->kategori->nama ?? '');
            return str_contains($namaKat, 'pinjam') || $item->id_kategori == $idKatPinjaman;
        });

        $recordBagiHasil = $existingRecords->first(function ($item) use ($idKatPinjaman, $recordCicilan) {
            if ($recordCicilan && $item->id === $recordCicilan->id) {
                return false;
            }
            $namaKat = strtolower($item->kategori->nama ?? '');
            return !str_contains($namaKat, 'pinjam') && $item->id_kategori != $idKatPinjaman;
        });

        // 1. Update / Buat / Hapus Cicilan Pokok
        if ($cicilan > 0) {
            if ($recordCicilan) {
                $recordCicilan->update([
                    'id_pengajuan' => $pengajuanBaru->id,
                    'id_kategori' => $idKatPinjaman,
                    'jumlah' => $cicilan,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Cicilan Pokok',
                ]);
            } else {
                $newCicilan = new TransaksiT([
                    'id_user' => $userId,
                    'id_pengajuan' => $pengajuanBaru->id,
                    'id_kategori' => $idKatPinjaman,
                    'jumlah' => $cicilan,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Cicilan Pokok',
                ]);
                $newCicilan->created_at = $refCreatedAt;
                $newCicilan->save();
            }
        } elseif ($recordCicilan) {
            $recordCicilan->delete();
        }

        // 2. Update / Buat / Hapus Bagi Hasil
        if ($bagiHasil > 0) {
            if ($recordBagiHasil) {
                $recordBagiHasil->update([
                    'id_pengajuan' => $pengajuanBaru->id,
                    'id_kategori' => $idKatBagiHasil,
                    'jumlah' => $bagiHasil,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Bagi Hasil',
                ]);
            } else {
                $newBagiHasil = new TransaksiT([
                    'id_user' => $userId,
                    'id_pengajuan' => $pengajuanBaru->id,
                    'id_kategori' => $idKatBagiHasil,
                    'jumlah' => $bagiHasil,
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan ?: 'Bagi Hasil',
                ]);
                $newBagiHasil->created_at = $refCreatedAt;
                $newBagiHasil->save();
            }
        } elseif ($recordBagiHasil) {
            $recordBagiHasil->delete();
        }

        $keptIds = array_filter([
            $recordCicilan ? $recordCicilan->id : null,
            $recordBagiHasil ? $recordBagiHasil->id : null,
        ]);

        foreach ($existingRecords as $rec) {
            if (!in_array($rec->id, $keptIds)) {
                $rec->delete();
            }
        }

        // Sinkronisasi status lunas pada pengajuan lama & baru
        $affectedPengajuanIds = array_unique(array_merge($oldPengajuanIds, [$pengajuanBaru->id]));
        foreach ($affectedPengajuanIds as $pid) {
            $this->syncStatusPengajuan($pid);
        }

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data pembayaran angsuran berhasil diperbarui.');
    }

    public function deleteBayarAngsuran(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'group_ids' => 'required|string',
        ]);

        $userId = (int) $request->id_user;
        $groupIds = array_filter(array_map('intval', explode(',', $request->group_ids)));

        $records = TransaksiT::where('id_user', $userId)
            ->whereIn('id', $groupIds)
            ->get();

        $affectedPengajuanIds = $records->pluck('id_pengajuan')->unique()->toArray();

        foreach ($records as $rec) {
            $rec->delete();
        }

        foreach ($affectedPengajuanIds as $pid) {
            $this->syncStatusPengajuan($pid);
        }

        return redirect()->route('kartu.anggota', ['user_id' => $userId])
            ->with('success', 'Data pembayaran angsuran berhasil dihapus.');
    }

    private function syncStatusPengajuan($idPengajuan)
    {
        $pengajuan = Pengajuan::find($idPengajuan);
        if (!$pengajuan) {
            return;
        }

        $katPinjaman = Kategori::where('nama', 'Pinjaman')->first();
        $idKatPinjaman = $katPinjaman ? $katPinjaman->id : 3;

        $totalBayarPinjaman = TransaksiT::where('id_pengajuan', $pengajuan->id)
            ->where('id_kategori', $idKatPinjaman)
            ->sum('jumlah');

        $status = $totalBayarPinjaman >= (int) $pengajuan->nominal_pinjaman ? 'sudah lunas' : 'belum lunas';
        if ($pengajuan->keterangan !== $status) {
            $pengajuan->update(['keterangan' => $status]);
        }
    }
}
