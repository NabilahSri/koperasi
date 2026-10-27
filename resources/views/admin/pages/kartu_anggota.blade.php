@extends('layouts.app')

@section('css')
    <style>
        /* Styling Kartu Anggota */
        .kartu-container {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            padding: 24px;
            border-radius: 8px;
            color: #000;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }

        .kartu-header {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            border: 2px solid #000;
            margin-bottom: 8px;
            background: #fff;
        }

        .kartu-catatan-box {
            width: 32%;
            border-right: 2px solid #000;
            padding: 8px;
            display: flex;
            flex-direction: column;
        }

        .kartu-catatan-box h6 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: none;
        }

        .kartu-catatan-textarea {
            width: 100%;
            flex-grow: 1;
            min-height: 85px;
            border: 1px dashed #bbb;
            padding: 6px;
            font-size: 12px;
            resize: vertical;
            background: #fafafa;
        }

        .kartu-kop-center {
            width: 38%;
            text-align: center;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-right: 2px solid #000;
        }

        .kartu-kop-center .logo-img {
            max-height: 48px;
            margin-bottom: 4px;
        }

        .kartu-title-main {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #000;
            margin: 0;
            line-height: 1.2;
        }

        .kartu-title-sub {
            font-size: 14px;
            font-weight: 800;
            color: #d60000;
            /* Merah seperti Excel */
            margin: 2px 0;
            text-transform: uppercase;
        }

        .kartu-title-durusul {
            font-size: 13px;
            font-weight: 800;
            color: #4b0082;
            /* Ungu indigo seperti Excel */
            margin: 1px 0;
            text-transform: uppercase;
        }

        .kartu-title-nik {
            font-size: 10px;
            font-weight: 600;
            color: #111;
            margin: 1px 0;
        }

        .kartu-info-right {
            width: 30%;
            padding: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kartu-info-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        .kartu-info-table td {
            padding: 4px 6px;
            border: 1px solid #000;
        }

        .kartu-info-table .info-label {
            width: 40%;
            font-weight: bold;
            background: #fdfdfd;
        }

        .kartu-info-table .info-val {
            width: 60%;
            font-weight: 600;
        }

        /* Header Lanjutan untuk Lembar 2 ke atas */
        .kartu-header-continuation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid #000;
            padding: 8px 14px;
            margin-bottom: 8px;
            background: #fff;
        }

        /* Grid Tabel Kartu Anggota */
        .table-kartu-wrap {
            overflow-x: auto;
        }

        .table-kartu {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            color: #000;
            border: 2px solid #000;
        }

        .table-kartu th,
        .table-kartu td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .th-yellow-main {
            background-color: #FFFF00 !important;
            color: #000 !important;
            text-align: center;
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .th-yellow-sub {
            background-color: #FFFF00 !important;
            color: #000 !important;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            line-height: 1.2;
        }

        .border-divider-col {
            border-left: 2.5px solid #000 !important;
        }

        .table-kartu tbody tr td {
            height: 25px;
            background: #fff;
        }

        .table-kartu tbody tr:nth-child(even) td {
            background: #fbfbfb;
        }

        .table-kartu tbody tr:hover td {
            background: #f0f7ff;
        }

        .row-pindahan td {
            background-color: #fffde7 !important;
            font-weight: bold;
        }

        .table-kartu tfoot th {
            background-color: #e9ecef !important;
            font-weight: bold;
            font-size: 11px;
            border: 1.5px solid #000;
        }

        /* Lembar Navigation Pills */
        .lembar-nav-tabs .nav-link {
            border: 1px solid #dee2e6;
            color: #495057;
            background: #f8f9fa;
        }

        .lembar-nav-tabs .nav-link.active {
            background-color: #6956CE;
            color: #fff;
            border-color: #6956CE;
        }

        /* Print Setup */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .kleon-vertical-nav,
            .header,
            .no-print,
            .btn,
            .modal,
            #preloader {
                display: none !important;
            }

            .main-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }

            .inner-contents {
                padding: 0 !important;
            }

            /* Print All Sheets vs Print Only Active Sheet */
            body.print-only-active .tab-pane:not(.active) {
                display: none !important;
            }

            body.print-all-sheets .tab-pane {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            body.print-all-sheets .tab-pane:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }

            .kartu-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .th-yellow-main,
            .th-yellow-sub {
                background-color: #FFFF00 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .row-pindahan td {
                background-color: #fffde7 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .kartu-title-sub {
                color: #d60000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .kartu-title-durusul {
                color: #4b0082 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: landscape;
                margin: 8mm;
            }
        }
    </style>
@endsection

@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Alert Notifikasi -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show no-print mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show no-print mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Toolbar Atas: Pemilihan Anggota & Tombol Aksi Cepat -->
                <div class="card border-0 shadow-sm mb-4 no-print">
                    <div class="card-body p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-4 col-md-5">
                                <label class="form-label fw-bold text-dark mb-1">
                                    <i class="bi bi-person-badge me-1"></i> Pilih Anggota Koperasi:
                                </label>
                                <select id="userSelector" class="form-select select2 w-100">
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}"
                                            {{ $selectedUser && $selectedUser->id == $u->id ? 'selected' : '' }}>
                                            [{{ $u->no_user ?? '---' }}] {{ $u->name }} -
                                            {{ $u->alamat ?? 'Alamat (-)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-8 col-md-7 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2 mt-2 mt-md-0">
                                    <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalSimpanan" data-toggle="modal"
                                        data-target="#modalSimpanan">
                                        <i class="bi bi-wallet2 me-1"></i> + Simpanan
                                    </button>
                                    <button type="button"
                                        class="btn btn-warning text-dark btn-sm px-3 shadow-sm fw-semibold"
                                        data-bs-toggle="modal" data-bs-target="#modalPengambilan" data-toggle="modal"
                                        data-target="#modalPengambilan">
                                        <i class="bi bi-cash-stack me-1"></i> - Ambil Simpanan
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm px-3 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalPinjaman" data-toggle="modal"
                                        data-target="#modalPinjaman">
                                        <i class="bi bi-file-earmark-plus me-1"></i> + Pinjaman Baru
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm px-3 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalAngsuran" data-toggle="modal"
                                        data-target="#modalAngsuran">
                                        <i class="bi bi-check2-circle me-1"></i> Bayar Angsuran
                                    </button>
                                    {{-- <div class="btn-group">
                                        <button type="button" class="btn btn-dark btn-sm px-3 shadow-sm" onclick="printLembarAktif()">
                                            <i class="bi bi-printer me-1"></i> Cetak Lembar Ini
                                        </button>
                                        @if (isset($totalLembar) && $totalLembar > 1)
                                            <button type="button" class="btn btn-secondary btn-sm px-2 shadow-sm" onclick="printSemuaLembar()" title="Cetak Semua Lembar">
                                                <i class="bi bi-printer-fill me-1"></i> Semua ({{ $totalLembar }})
                                            </button>
                                        @endif
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Statistik Kartu (Screen Only) -->
                @if ($selectedUser)
                    <div class="row g-3 mb-4 no-print">
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm border-start border-4 border-primary">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold text-uppercase">Total Simpanan Bersih</div>
                                    <h4 class="text-primary fw-bold mb-0">Rp
                                        {{ number_format($saldoSimpananAkhir, 0, ',', '.') }}</h4>
                                    <div class="small text-muted mt-1">Total Setor: Rp
                                        {{ number_format($totalSimpananKotor, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm border-start border-4 border-warning">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold text-uppercase">Total Pengambilan</div>
                                    <h4 class="text-warning fw-bold mb-0">Rp
                                        {{ number_format($totalPengambilan, 0, ',', '.') }}</h4>
                                    <div class="small text-muted mt-1">Penarikan tabungan anggota</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm border-start border-4 border-danger">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold text-uppercase">Sisa Utang Pinjaman</div>
                                    <h4 class="text-danger fw-bold mb-0">Rp
                                        {{ number_format($sisaUtangAkhir, 0, ',', '.') }}</h4>
                                    <div class="small text-muted mt-1">Dari Pinjaman: Rp
                                        {{ number_format($totalPinjaman, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm border-start border-4 border-success">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold text-uppercase">Total Cicilan Dibayar</div>
                                    <h4 class="text-success fw-bold mb-0">Rp
                                        {{ number_format($totalAngsuran, 0, ',', '.') }}</h4>
                                    <div class="small text-muted mt-1">Bagi Hasil: Rp
                                        {{ number_format($totalBagiHasil, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($selectedUser)
                    <!-- Navigasi Lembar / Halaman (25 baris per lembar) -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark small"><i class="bi bi-layers me-1"></i> Lembar Kartu:</span>
                            <ul class="nav nav-pills lembar-nav-tabs" id="lembarTabs" role="tablist">
                                @foreach ($lembarList as $lembar)
                                    <li class="nav-item me-1" role="presentation">
                                        <button
                                            class="nav-link {{ $loop->first ? 'active' : '' }} btn-sm px-3 py-1 fw-bold"
                                            id="tab-lembar-{{ $lembar['nomor_lembar'] }}" data-bs-toggle="pill"
                                            data-bs-target="#content-lembar-{{ $lembar['nomor_lembar'] }}" type="button"
                                            role="tab">
                                            Lembar {{ $lembar['nomor_lembar'] }}
                                            @if ($lembar['is_first'])
                                                <span class="badge bg-light text-dark ms-1">Hal 1</span>
                                            @else
                                                <span class="badge bg-secondary ms-1">Hal
                                                    {{ $lembar['nomor_lembar'] }}</span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Masing-masing lembar memuat tepat <strong>25
                                baris</strong>
                        </div>
                    </div>

                    <!-- KONTEN LEMBAR KARTU ANGGOTA -->
                    <div class="tab-content" id="lembarTabContent">
                        @foreach ($lembarList as $lembar)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="content-lembar-{{ $lembar['nomor_lembar'] }}" role="tabpanel">

                                <div class="kartu-container mb-4">
                                    @if ($lembar['is_first'])
                                        <!-- Header Lengkap (Khusus Lembar 1) -->
                                        <div class="kartu-header">
                                            <!-- Kotak Catatan Kiri -->
                                            <div class="kartu-catatan-box">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6>Catatan:</h6>
                                                    <button type="button" id="btnSimpanCatatan"
                                                        class="btn btn-outline-secondary btn-xs py-0 px-2 no-print"
                                                        style="font-size: 11px;">
                                                        <i class="bi bi-save me-1"></i>Simpan
                                                    </button>
                                                </div>
                                                <textarea id="catatanTextarea" class="kartu-catatan-textarea" placeholder="Tulis catatan anggota di sini...">{{ $selectedUser->catatan }}</textarea>
                                                <small id="catatanFeedback" class="text-success mt-1 no-print"
                                                    style="display: none; font-size: 10px;">
                                                    <i class="bi bi-check"></i> Tersimpan
                                                </small>
                                            </div>

                                            <!-- Kop Tengah (Koperasi Identity) -->
                                            <div class="kartu-kop-center">
                                                <img src="{{ $logoPath ?? asset('assets/img/logo-icon.svg') }}"
                                                    alt="Logo" class="logo-img">
                                                <h2 class="kartu-title-main">KARTU ANGGOTA</h2>
                                                <h3 class="kartu-title-sub">KOPONTREN " AL FALAH "</h3>
                                                <h4 class="kartu-title-durusul">DURUSUL ATHFAL</h4>
                                                <p class="kartu-title-nik mb-0">NIK : 320.627.108.0002</p>
                                                <p class="kartu-title-nik mb-0">Badan Hukum 571/BH/KWK-10/X/'97</p>
                                            </div>

                                            <!-- Info Anggota Kanan -->
                                            <div class="kartu-info-right">
                                                <table class="kartu-info-table">
                                                    <tr>
                                                        <td class="info-label">NO ANGGOTA:</td>
                                                        <td class="info-val">{{ $selectedUser->no_user ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="info-label">NAMA:</td>
                                                        <td class="info-val">{{ $selectedUser->name ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="info-label">ALAMAT:</td>
                                                        <td class="info-val">{{ $selectedUser->alamat ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Header Lanjutan (Lembar 2, 3, dst) -->
                                        <div class="kartu-header-continuation">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $logoPath ?? asset('assets/img/logo-icon.svg') }}"
                                                    alt="Logo" style="max-height: 40px; margin-right: 12px;">
                                                <div>
                                                    <h5 class="mb-0 fw-bold">KARTU ANGGOTA &mdash; KOPONTREN " AL FALAH "
                                                    </h5>
                                                    <div class="small fw-bold text-danger">LEMBAR
                                                        KE-{{ $lembar['nomor_lembar'] }} (LANJUTAN) &bull; DURUSUL ATHFAL
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <table class="kartu-info-table">
                                                    <tr>
                                                        <td class="info-label" style="padding: 2px 6px;">NO ANGGOTA:</td>
                                                        <td class="info-val" style="padding: 2px 6px;">
                                                            {{ $selectedUser->no_user ?? '-' }}</td>
                                                        <td class="info-label" style="padding: 2px 6px;">NAMA:</td>
                                                        <td class="info-val" style="padding: 2px 6px;">
                                                            {{ $selectedUser->name ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Tabel Transaksi: Kiri Tagihan, Kanan Kekayaan Anggota -->
                                    <div class="table-kartu-wrap">
                                        <table class="table-kartu">
                                            <thead>
                                                <tr>
                                                    <!-- Header Kiri -->
                                                    <th colspan="7" class="th-yellow-main">JENIS TAGIHAN</th>
                                                    <!-- Header Kanan -->
                                                    <th colspan="9" class="th-yellow-main border-divider-col">KEKAYAAN
                                                        ANGGOTA</th>
                                                </tr>
                                                <tr>
                                                    <!-- Sub-header Tagihan -->
                                                    <th class="th-yellow-sub" style="width: 7%;">TGL/<br>BULAN/<br>TAHUN
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 7%;">BESAR<br>PINJAMAN<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 7%;">
                                                        CICILAN/<br>ANGSURAN<br>Rp.</th>
                                                    <th class="th-yellow-sub" style="width: 7%;">SISA<br>UTANG<br>Rp.</th>
                                                    <th class="th-yellow-sub" style="width: 6%;">BAGI<br>HASIL<br>Rp.</th>
                                                    <th class="th-yellow-sub" style="width: 5%;">PROPISI<br>Rp.</th>
                                                    <th class="th-yellow-sub" style="width: 6%;">TANDA<br>TANGAN/<br>PARAF
                                                    </th>

                                                    <!-- Sub-header Kekayaan -->
                                                    <th class="th-yellow-sub border-divider-col" style="width: 7%;">TG
                                                        L/<br>BLN/<br>THN</th>
                                                    <th class="th-yellow-sub" style="width: 6%;">SIMP.<br>POKOK<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 6%;">SIMP.<br>WAJIB<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 6%;">SIMP.<br>MANASUKA<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 6%;">SIMP.<br>LEBARAN<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 6%;">SIMP.<br>NIAGA<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 7%;">JUMLAH<br>SIMPANAN<br>Rp.
                                                    </th>
                                                    <th class="th-yellow-sub" style="width: 6%;">PENGAM-<br>BILAN
                                                        SIM-<br>PANAN Rp.</th>
                                                    <th class="th-yellow-sub" style="width: 5%;">TTD</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Jika Lembar >= 2, Baris 1 adalah Baris Saldo Pindahan -->
                                                @if ($lembar['pindahan'])
                                                    <tr class="row-pindahan">
                                                        <td class="text-center">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end fw-bold text-danger">
                                                            {{ $lembar['pindahan']['tagihan']['sisa_utang'] > 0 ? number_format($lembar['pindahan']['tagihan']['sisa_utang'], 0, ',', '.') : '0' }}
                                                        </td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-center small"><span
                                                                class="badge bg-warning text-dark py-0"
                                                                style="font-size: 9px;">PINDAHAN HAL
                                                                {{ $lembar['nomor_lembar'] - 1 }}</span></td>

                                                        <td class="text-center border-divider-col">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-end fw-bold text-primary">
                                                            {{ $lembar['pindahan']['simpanan']['jumlah_simpanan'] > 0 ? number_format($lembar['pindahan']['simpanan']['jumlah_simpanan'], 0, ',', '.') : '0' }}
                                                        </td>
                                                        <td class="text-end">-</td>
                                                        <td class="text-center small"><span
                                                                class="badge bg-warning text-dark py-0"
                                                                style="font-size: 9px;">PINDAHAN HAL
                                                                {{ $lembar['nomor_lembar'] - 1 }}</span></td>
                                                    </tr>
                                                @endif

                                                <!-- 24 baris transaksi (atau 25 di Lembar 1) -->
                                                @foreach ($lembar['rows'] as $row)
                                                    @php
                                                        $t = $row['tagihan'];
                                                        $s = $row['simpanan'];
                                                    @endphp
                                                    <tr>
                                                        <!-- Kolom Tagihan -->
                                                        <td class="text-center">
                                                            {{ $t ? date('d/m/Y', strtotime($t['tanggal'])) : '' }}</td>
                                                        <td class="text-end">
                                                            {{ $t && $t['besar_pinjaman'] ? number_format($t['besar_pinjaman'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $t && $t['cicilan'] ? number_format($t['cicilan'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end fw-semibold">
                                                            {{ $t ? number_format($t['sisa_utang'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $t && $t['bagi_hasil'] ? number_format($t['bagi_hasil'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $t && $t['propisi'] ? number_format($t['propisi'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-center small">{{ $t ? $t['paraf'] ?? '' : '' }}
                                                        </td>

                                                        <!-- Kolom Kekayaan Anggota -->
                                                        <td class="text-center border-divider-col">
                                                            {{ $s ? date('d/m/Y', strtotime($s['tanggal'])) : '' }}</td>
                                                        <td class="text-end">
                                                            {{ $s && $s['pokok'] ? number_format($s['pokok'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $s && $s['wajib'] ? number_format($s['wajib'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $s && $s['manasuka'] ? number_format($s['manasuka'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $s && $s['lebaran'] ? number_format($s['lebaran'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $s && $s['niaga'] ? number_format($s['niaga'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end fw-semibold">
                                                            {{ $s ? number_format($s['jumlah_simpanan'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-end text-danger">
                                                            {{ $s && $s['pengambilan'] ? number_format($s['pengambilan'], 0, ',', '.') : '' }}
                                                        </td>
                                                        <td class="text-center small">{{ $s ? $s['paraf'] ?? '' : '' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="text-center">{{ $loop->last ? 'TOTAL' : 'SALDO LEMBAR' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalPinjaman > 0 ? number_format($totalPinjaman, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalAngsuran > 0 ? number_format($totalAngsuran, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end text-danger fw-bold">
                                                        {{ number_format($lembar['sisa_utang_lembar'], 0, ',', '.') }}</th>
                                                    <th class="text-end">
                                                        {{ $totalBagiHasil > 0 ? number_format($totalBagiHasil, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalPropisi > 0 ? number_format($totalPropisi, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th></th>

                                                    <th class="text-center border-divider-col">
                                                        {{ $loop->last ? 'SALDO AKHIR' : 'SALDO LEMBAR' }}</th>
                                                    <th class="text-end">
                                                        {{ $totalSimpPokok > 0 ? number_format($totalSimpPokok, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalSimpWajib > 0 ? number_format($totalSimpWajib, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalSimpManasuka > 0 ? number_format($totalSimpManasuka, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalSimpLebaran > 0 ? number_format($totalSimpLebaran, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end">
                                                        {{ $totalSimpNiaga > 0 ? number_format($totalSimpNiaga, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th class="text-end text-primary fw-bold">
                                                        {{ number_format($lembar['saldo_simpanan_lembar'], 0, ',', '.') }}
                                                    </th>
                                                    <th class="text-end text-danger">
                                                        {{ $totalPengambilan > 0 ? number_format($totalPengambilan, 0, ',', '.') : '-' }}
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-person-x fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Belum ada anggota yang dipilih</h5>
                        <p class="text-muted small">Silakan pilih salah satu anggota melalui dropdown di atas.</p>
                    </div>
                @endif

            </div>
        </div>
    </main>

    @if ($selectedUser)
        <!-- MODAL 1: Tambah Simpanan -->
        <div class="modal fade" id="modalSimpanan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('kartu.simpanan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_user" value="{{ $selectedUser->id }}">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i> Tambah Simpanan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anggota</label>
                                <input type="text" class="form-control bg-light"
                                    value="[{{ $selectedUser->no_user }}] {{ $selectedUser->name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori Simpanan <span
                                        class="text-danger">*</span></label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoriSimpanan as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jumlah (Rp) <span class="text-danger">*</span></label>
                                <input type="text" name="jumlah" class="form-control rupiah-input" placeholder="0"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Transaksi <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Penyetor</label>
                                <input type="text" name="nama_penyetor" class="form-control"
                                    value="{{ $selectedUser->name }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Setoran Simpanan">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan
                                Setoran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL 2: Ambil Simpanan -->
        <div class="modal fade" id="modalPengambilan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('kartu.pengambilan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_user" value="{{ $selectedUser->id }}">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i> Penarikan Simpanan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anggota</label>
                                <input type="text" class="form-control bg-light"
                                    value="[{{ $selectedUser->no_user }}] {{ $selectedUser->name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Kategori Tabungan <span
                                        class="text-danger">*</span></label>
                                <select name="id_kategori" id="kategoriPengambilanSelect" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoriSimpanan as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-2 text-muted small" id="saldoInfoText">
                                    Pilih kategori untuk melihat saldo tersedia.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nominal Penarikan (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="jumlah" class="form-control rupiah-input" placeholder="0"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Penarikan <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Pengambil</label>
                                <input type="text" name="nama_pengambil" class="form-control"
                                    value="{{ $selectedUser->name }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Penarikan Simpanan">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning text-dark fw-bold"><i
                                    class="bi bi-cash me-1"></i> Proses Penarikan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL 3: Pinjaman Baru -->
        <div class="modal fade" id="modalPinjaman" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('kartu.pinjaman.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_user" value="{{ $selectedUser->id }}">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="bi bi-file-earmark-plus me-2"></i> Tambah Pinjaman Baru
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anggota</label>
                                <input type="text" class="form-control bg-light"
                                    value="[{{ $selectedUser->no_user }}] {{ $selectedUser->name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Besar Pinjaman (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nominal_pinjaman" id="nominalPinjamanInput"
                                    class="form-control rupiah-input" placeholder="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Propisi 1% (Rp)</label>
                                <input type="text" name="propisi" id="propisiInput" class="form-control rupiah-input"
                                    placeholder="0">
                                <small class="text-muted" style="font-size: 11px;">Otomatis dihitung 1% dari pinjaman,
                                    dapat diubah manual bila perlu.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nominal Bagi Hasil (Rp)</label>
                                <input type="text" name="nominal_bagihasil" class="form-control rupiah-input"
                                    placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Pencairan <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pengajuan" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Keperluan Pinjaman / Modal Usaha">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-save me-1"></i> Simpan
                                Pinjaman</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL 4: Bayar Angsuran -->
        <div class="modal fade" id="modalAngsuran" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('kartu.angsuran.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_user" value="{{ $selectedUser->id }}">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="bi bi-check2-circle me-2"></i> Bayar Angsuran / Cicilan
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Anggota</label>
                                <input type="text" class="form-control bg-light"
                                    value="[{{ $selectedUser->no_user }}] {{ $selectedUser->name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Pinjaman Aktif <span
                                        class="text-danger">*</span></label>
                                @if ($pengajuanAktif->isNotEmpty())
                                    <select name="id_pengajuan" class="form-select" required>
                                        @foreach ($pengajuanAktif as $pj)
                                            <option value="{{ $pj->id }}">
                                                Pinjaman Rp {{ number_format($pj->nominal_pinjaman, 0, ',', '.') }}
                                                (Tgl: {{ date('d/m/Y', strtotime($pj->tanggal_pengajuan)) }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-warning py-2 small mb-0">
                                        Anggota ini saat ini tidak memiliki pinjaman aktif yang belum lunas.
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cicilan Pokok Pinjaman (Rp) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nominal_angsuran" class="form-control rupiah-input"
                                    placeholder="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bagi Hasil (Rp)</label>
                                <input type="text" name="nominal_bagihasil" class="form-control rupiah-input"
                                    placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Pembayaran <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Angsuran Pinjaman">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"
                                {{ $pengajuanAktif->isEmpty() ? 'disabled' : '' }}>
                                <i class="bi bi-save me-1"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('script')
    <script>
        function printLembarAktif() {
            $('body').removeClass('print-all-sheets').addClass('print-only-active');
            window.print();
        }

        function printSemuaLembar() {
            $('body').removeClass('print-only-active').addClass('print-all-sheets');
            window.print();
        }

        $(document).ready(function() {
            // Inisialisasi dropdown select2 untuk pemilihan anggota
            if ($.fn.select2) {
                $('#userSelector').select2({
                    placeholder: 'Cari Anggota (Nama / No User)...',
                    allowClear: false,
                    width: '100%'
                });
            }

            // Navigasi saat anggota diganti
            $('#userSelector').on('change', function() {
                var userId = $(this).val();
                if (userId) {
                    window.location.href = "{{ url('kartu-anggota') }}?user_id=" + userId;
                }
            });

            // Format angka ribuan pada input rupiah
            $('.rupiah-input').on('keyup', function() {
                var val = $(this).val().replace(/[^0-9]/g, '');
                if (val) {
                    $(this).val(new Intl.NumberFormat('id-ID').format(val));
                } else {
                    $(this).val('');
                }
            });

            // Hitung propisi 1% otomatis saat nominal pinjaman diketik
            $('#nominalPinjamanInput').on('keyup', function() {
                var val = parseInt($(this).val().replace(/[^0-9]/g, '')) || 0;
                if (val > 0) {
                    var propisi = Math.round(val * 0.01);
                    $('#propisiInput').val(new Intl.NumberFormat('id-ID').format(propisi));
                } else {
                    $('#propisiInput').val('0');
                }
            });

            // Cek saldo kategori simpanan saat memilih di modal penarikan
            $('#kategoriPengambilanSelect').on('change', function() {
                var katId = $(this).val();
                var userId = "{{ $selectedUser->id ?? '' }}";

                if (katId && userId) {
                    $('#saldoInfoText').html(
                        '<span class="spinner-border spinner-border-sm text-primary"></span> Mengecek saldo...'
                    );
                    $.ajax({
                        url: "{{ url('kartu-anggota/saldo') }}/" + userId + "/" + katId,
                        type: "GET",
                        dataType: "json",
                        success: function(res) {
                            $('#saldoInfoText').html(
                                'Saldo tersedia: <strong class="text-success">' + res
                                .saldo_rp + '</strong>');
                        },
                        error: function() {
                            $('#saldoInfoText').html(
                                '<span class="text-danger">Gagal memuat saldo kategori.</span>'
                            );
                        }
                    });
                } else {
                    $('#saldoInfoText').text('Pilih kategori untuk melihat saldo tersedia.');
                }
            });

            // Simpan Catatan via AJAX
            $('#btnSimpanCatatan').on('click', function() {
                var catatan = $('#catatanTextarea').val();
                var userId = "{{ $selectedUser->id ?? '' }}";
                var btn = $(this);

                if (!userId) return;

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: "{{ route('kartu.catatan.update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_user: userId,
                        catatan: catatan
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i>Simpan');
                        $('#catatanFeedback').fadeIn().delay(2000).fadeOut();
                    },
                    error: function() {
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i>Simpan');
                        alert('Gagal menyimpan catatan.');
                    }
                });
            });
        });
    </script>
@endsection
