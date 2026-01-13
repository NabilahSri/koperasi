@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
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
                                                <input type="month" name="filter_month" id="filter_month" class="form-control">
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
                                @if(request()->is('laporan/filterdata*'))
                                    <form action="/laporan/filterdata/export" method="get">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-2 text-white">Export Excel</button>
                                    </form>
                                @else
                                    <form action="/laporan/export" method="get">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success mb-2 text-white">Export Excel</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-1" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Anggota</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        @foreach ($kategori as $item)
                                            <th>{{ $item->nama }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->no_user }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            @foreach ($kategori as $data)
                                                {{-- @if ($data->id == 1)
                                                    <td>Rp. {{ number_format($item->iuran_pokok) }}</td> 
                                                @endif --}}
                                                {{-- @if ($data->id == 2) --}}
                                                @if($data->jenis->nama == 'Simpanan')
                                                    @if (isset($simpanan[$item->id][$data->id]))
                                                        <td>Rp. {{ number_format($simpanan[$item->id][$data->id]) }}</td>
                                                    @else (isset($tagihan[$item->id][$data->id]))
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
                                                
                                                {{-- @endif --}}
                                                {{-- @if ($data->id == 4)
                                                    @if (isset($tagihan[$item->id][$data->id]))
                                                        <td>Rp. {{ number_format($tagihan[$item->id][$data->id]) }}</td>
                                                    @else
                                                        <td>0</td>
                                                    @endif
                                                @endif --}}
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(request()->is('laporan/filterdata*'))
                                <a href="/laporan">Reset Filter</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
