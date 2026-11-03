@extends('layouts.app')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Data Laporan</h4>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="form-group d-flex align-items-center">
                                    <form action="/laporan/filterdata" method="post">
                                        @csrf
                                        <div class="row align-items-center">
                                            <label for="filter_month" class="col-auto mb-0 mr-2">Filter By Bulan</label>
                                            <div class="col-auto">
                                                <input type="month" name="filter_month" id="filter_month"
                                                    class="form-control" value="{{ $selectedMonth ?? '' }}">
                                            </div>
                                            <div class="col-auto d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-icon btn-warning text-white" title="Filter">
                                                    <i class="bi bi-funnel fs-18"></i>
                                                </button>
                                                @if (!empty($selectedMonth) || request()->is('laporan/filterdata*'))
                                                    <a href="/laporan" class="btn btn-sm btn-secondary text-white d-flex align-items-center">
                                                        Reset Filter
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                @if (!empty($selectedMonth) || request()->is('laporan/filterdata*'))
                                    <form action="/laporan/filterdata/export" method="get">
                                        <input type="hidden" name="filter_month" value="{{ $selectedMonth ?? '' }}">
                                        <button type="submit" class="btn btn-sm btn-success mb-2 text-white">Export
                                            Excel</button>
                                    </form>
                                @else
                                    <form action="/laporan/export" method="get">
                                        <button type="submit" class="btn btn-sm btn-success mb-2 text-white">Export
                                            Excel</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div>
                            @php
                                $colTotals = [];
                                foreach ($kategori as $kat) {
                                    if ($kat->jenis && $kat->jenis->nama == 'Tagihan' && $kat->nama == 'Pinjaman') {
                                        continue;
                                    }
                                    $colTotals[$kat->id] = 0;
                                }
                                $grandPinjamanTotal = 0;
                                $grandPropisiTotal = 0;
                                $grandPinjamanDibayar = 0;
                                $grandPinjamanSisa = 0;
                                $grandBagihasilTotal = 0;
                            @endphp
                            <table id="table-1" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        @foreach ($kategori as $item)
                                            @if ($item->jenis && $item->jenis->nama == 'Tagihan' && $item->nama == 'Pinjaman')
                                                @php continue; @endphp
                                            @endif
                                            <th>{{ $item->nama }}</th>
                                        @endforeach
                                        <th>Nominal Pinjaman</th>
                                        <th>Propisi</th>
                                        <th>Pinjaman Terbayar</th>
                                        <th>Sisa Pinjaman</th>
                                        <th>Nominal Bagi Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user as $key => $item)
                                        @php
                                            $uPinjamanTotal = $pinjaman_total[$item->id] ?? 0;
                                            $uPropisiTotal = $propisi_total[$item->id] ?? 0;
                                            $uPinjamanDibayar = $pinjaman_dibayar[$item->id] ?? 0;
                                            $uPinjamanSisa = $pinjaman_sisa[$item->id] ?? 0;
                                            $uBagihasilTotal = $bagihasil_total[$item->id] ?? 0;

                                            $grandPinjamanTotal += $uPinjamanTotal;
                                            $grandPropisiTotal += $uPropisiTotal;
                                            $grandPinjamanDibayar += $uPinjamanDibayar;
                                            $grandPinjamanSisa += $uPinjamanSisa;
                                            $grandBagihasilTotal += $uBagihasilTotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            @foreach ($kategori as $data)
                                                @if ($data->jenis && $data->jenis->nama == 'Tagihan' && $data->nama == 'Pinjaman')
                                                    @php continue; @endphp
                                                @endif
                                                @if ($data->jenis && $data->jenis->nama == 'Simpanan')
                                                    @php
                                                        $val = $simpanan[$item->id][$data->id] ?? 0;
                                                        $colTotals[$data->id] += $val;
                                                    @endphp
                                                    <td>{{ $val > 0 ? 'Rp ' . number_format($val, 0, ',', '.') : '-' }}</td>
                                                @elseif ($data->jenis && $data->jenis->nama == 'Tagihan')
                                                    @php
                                                        $val = $tagihan[$item->id][$data->id] ?? 0;
                                                        $colTotals[$data->id] += $val;
                                                    @endphp
                                                    <td>{{ $val > 0 ? 'Rp ' . number_format($val, 0, ',', '.') : '-' }}</td>
                                                @else
                                                    <td>-</td>
                                                @endif
                                            @endforeach
                                            <td>{{ $uPinjamanTotal > 0 ? 'Rp ' . number_format($uPinjamanTotal, 0, ',', '.') : 'Rp 0' }}</td>
                                            <td>{{ $uPropisiTotal > 0 ? 'Rp ' . number_format($uPropisiTotal, 0, ',', '.') : '-' }}</td>
                                            <td>{{ $uPinjamanDibayar > 0 ? 'Rp ' . number_format($uPinjamanDibayar, 0, ',', '.') : 'Rp 0' }}</td>
                                            <td>{{ $uPinjamanSisa > 0 ? 'Rp ' . number_format($uPinjamanSisa, 0, ',', '.') : 'Rp 0' }}</td>
                                            <td>{{ $uBagihasilTotal > 0 ? 'Rp ' . number_format($uBagihasilTotal, 0, ',', '.') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <th colspan="3" class="text-end">Total</th>
                                        @foreach ($kategori as $item)
                                            @if ($item->jenis && $item->jenis->nama == 'Tagihan' && $item->nama == 'Pinjaman')
                                                @php continue; @endphp
                                            @endif
                                            <th>{{ ($colTotals[$item->id] ?? 0) > 0 ? 'Rp ' . number_format($colTotals[$item->id], 0, ',', '.') : '-' }}</th>
                                        @endforeach
                                        <th>{{ $grandPinjamanTotal > 0 ? 'Rp ' . number_format($grandPinjamanTotal, 0, ',', '.') : '-' }}</th>
                                        <th>{{ $grandPropisiTotal > 0 ? 'Rp ' . number_format($grandPropisiTotal, 0, ',', '.') : '-' }}</th>
                                        <th>{{ $grandPinjamanDibayar > 0 ? 'Rp ' . number_format($grandPinjamanDibayar, 0, ',', '.') : '-' }}</th>
                                        <th>{{ $grandPinjamanSisa > 0 ? 'Rp ' . number_format($grandPinjamanSisa, 0, ',', '.') : '-' }}</th>
                                        <th>{{ $grandBagihasilTotal > 0 ? 'Rp ' . number_format($grandBagihasilTotal, 0, ',', '.') : '-' }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
