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
                                                    class="form-control">
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-sm btn-icon btn-warning text-white"><i
                                                        class="bi bi-funnel fs-18"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                @if (request()->is('laporan/filterdata*'))
                                    <form action="/laporan/filterdata/export" method="get">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-2 text-white">Export
                                            Excel</button>
                                    </form>
                                @else
                                    <form action="/laporan/export" method="get">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-2 text-white">Export
                                            Excel</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div>
                            <table id="table-1" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        @foreach ($kategori as $item)
                                            @if (!($item->jenis->nama == 'Tagihan'))
                                                <th>{{ $item->nama }}</th>
                                            @endif
                                        @endforeach
                                        <th>Nominal Pinjaman</th>
                                        <th>Propisi</th>
                                        <th>Pinjaman Terbayar</th>
                                        <th>Sisa Pinjaman</th>
                                        <th>Nominal Bagi Hasil</th>
                                        <th>Jumlah Nominal Bagi Hasil</th>
                                        <th>Pengambilan Manasuka</th>
                                        <th>Pengambilan Lebaran</th>
                                        <th>Sisa Tabungan Manasuka</th>
                                        <th>Sisa Tabungan Lebaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            @foreach ($kategori as $data)
                                                @if ($data->jenis->nama == 'Tagihan')
                                                    @php continue; @endphp
                                                @endif
                                                @if ($data->jenis->nama == 'Simpanan')
                                                    @if (isset($simpanan[$item->id][$data->id]))
                                                        <td>Rp. {{ number_format($simpanan[$item->id][$data->id]) }}</td>
                                                    @else
                                                        <td>0</td>
                                                    @endif
                                                @elseif($data->jenis->nama == 'Tagihan')
                                                    @if (isset($tagihan[$item->id][$data->id]))
                                                        <td>Rp. {{ number_format($tagihan[$item->id][$data->id]) }}</td>
                                                    @else
                                                        <td>0</td>
                                                    @endif
                                                @else
                                                    <td>0</td>
                                                @endif
                                            @endforeach
                                            <td>Rp. {{ number_format($pinjaman_total[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($propisi_total[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($pinjaman_dibayar[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($pinjaman_sisa[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($bagihasil_total[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($bagihasil_dibayar[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($pengambilan_manasuka_total[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($pengambilan_lebaran_total[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($sisa_manasuka_total[$item->id] ?? 0) }}</td>
                                            <td>Rp. {{ number_format($sisa_lebaran_total[$item->id] ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (request()->is('laporan/filterdata*'))
                                <a href="/laporan">Reset Filter</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
