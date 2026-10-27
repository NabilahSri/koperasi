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
                    'nominal_pinjaman' => (int) $pj->nominal_pinjaman,
                    'propisi' => (int) ($pj->propisi ?? 0),
                    'cicilan' => 0,
                    'bagi_hasil' => 0,
                    'keterangan' => $pj->keterangan,
                    'paraf' => 'Admin',
                ]);
                $totalPinjaman += (int) $pj->nominal_pinjaman;
                $totalPropisi += (int) ($pj->propisi ?? 0);
            }

            // Group tagihan by tanggal & id_pengajuan agar cicilan + bagi hasil tampil 1 baris jika di tanggal sama
            $groupedTagihan = $transaksiTagihanList->groupBy(function ($item) {
                return $item->tanggal . '_' . $item->id_pengajuan . '_' . $item->created_at->format('YmdHis');
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
                    'nominal_pinjaman' => 0,
                    'propisi' => 0,
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
                    'tanggal' => $item['date'],
                    'besar_pinjaman' => $item['nominal_pinjaman'] > 0 ? $item['nominal_pinjaman'] : null,
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
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $pengambilanList = PengambilanSimpanan::with(['kategori', 'petugas'])
                ->where('id_user', $selectedUser->id)
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $simpananEvents = collect();

            foreach ($transaksiSimpananList as $ts) {
                $katNama = strtolower($ts->kategori->nama ?? '');
                $pokok = 0;
                $wajib = 0;
                $manasuka = 0;
                $lebaran = 0;
                $niaga = 0;

                if (str_contains($katNama, 'pokok')) {
                    $pokok = (int) $ts->jumlah;
                    $totalSimpPokok += $pokok;
                } elseif (str_contains($katNama, 'wajib')) {
                    $wajib = (int) $ts->jumlah;
                    $totalSimpWajib += $wajib;
                } elseif (str_contains($katNama, 'manasuka')) {
                    $manasuka = (int) $ts->jumlah;
                    $totalSimpManasuka += $manasuka;
                } elseif (str_contains($katNama, 'lebaran')) {
                    $lebaran = (int) $ts->jumlah;
                    $totalSimpLebaran += $lebaran;
                } elseif (str_contains($katNama, 'niaga')) {
                    $niaga = (int) $ts->jumlah;
                    $totalSimpNiaga += $niaga;
                } else {
                    $manasuka = (int) $ts->jumlah;
                    $totalSimpManasuka += $manasuka;
                }

                $totalSimpananKotor += (int) $ts->jumlah;

                $simpananEvents->push([
                    'type' => 'simpanan',
                    'date' => $ts->tanggal,
                    'created_at' => $ts->created_at,
                    'id' => $ts->id,
                    'pokok' => $pokok,
                    'wajib' => $wajib,
                    'manasuka' => $manasuka,
                    'lebaran' => $lebaran,
                    'niaga' => $niaga,
                    'pengambilan' => 0,
                    'paraf' => $ts->petugas->name ?? 'Admin',
                    'keterangan' => $ts->keterangan ?? 'Setoran',
                ]);
            }

            foreach ($pengambilanList as $ps) {
                $jumlahAmbil = (int) $ps->jumlah;
                $totalPengambilan += $jumlahAmbil;

                $simpananEvents->push([
                    'type' => 'pengambilan',
                    'date' => $ps->tanggal,
                    'created_at' => $ps->created_at,
                    'id' => $ps->id,
                    'pokok' => 0,
                    'wajib' => 0,
                    'manasuka' => 0,
                    'lebaran' => 0,
                    'niaga' => 0,
                    'pengambilan' => $jumlahAmbil,
                    'paraf' => $ps->petugas->name ?? 'Admin',
                    'keterangan' => $ps->keterangan ?? 'Penarikan Simpanan',
                ]);
            }

            $sortedSimpanan = $simpananEvents->sortBy(function ($item) {
                return $item['date'] . '_' . ($item['type'] === 'simpanan' ? '0' : '1') . '_' . $item['created_at'];
            });

            $currentSaldoSimpanan = 0;
            foreach ($sortedSimpanan as $item) {
                if ($item['type'] === 'simpanan') {
                    $currentSaldoSimpanan += ($item['pokok'] + $item['wajib'] + $item['manasuka'] + $item['lebaran'] + $item['niaga']);
                } else {
                    $currentSaldoSimpanan = max(0, $currentSaldoSimpanan - $item['pengambilan']);
                }

                $simpananRows[] = [
                    'tanggal' => $item['date'],
                    'pokok' => $item['pokok'] > 0 ? $item['pokok'] : null,
                    'wajib' => $item['wajib'] > 0 ? $item['wajib'] : null,
                    'manasuka' => $item['manasuka'] > 0 ? $item['manasuka'] : null,
                    'lebaran' => $item['lebaran'] > 0 ? $item['lebaran'] : null,
                    'niaga' => $item['niaga'] > 0 ? $item['niaga'] : null,
                    'jumlah_simpanan' => $currentSaldoSimpanan,
                    'pengambilan' => $item['pengambilan'] > 0 ? $item['pengambilan'] : null,
                    'paraf' => $item['paraf'],
                    'keterangan' => $item['keterangan'],
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
}
