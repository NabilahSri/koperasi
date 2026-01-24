@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Riwayat Pengambilan Tabungan</h4>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Petugas</th>
                                        <th>Jumlah Ambil</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengambilan as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->kategori->nama }}</span></td>
                                            <td>{{ $item->petugas->name }}</td>
                                            <td class="text-danger fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                            <td data-search="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}" data-order="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td>{{ $item->keterangan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
