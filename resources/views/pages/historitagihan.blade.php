@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Riwayat Transaksi Tagihan</h4>
                            @if (auth()->user()->role == 'admin')
                                <div class="col-md-3">
                                    <select id="filter-nama" class="form-select">
                                        <option value="">Semua Anggota</option>
                                        @foreach ($user as $u)
                                            @if ($u->role == 'anggota')
                                                <option value="{{ $u->name }}">{{ $u->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tagihan as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->users->name }}</td>
                                            <td>{{ $item->kategori->nama }}</td>
                                            <td>Rp. {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}
                                            </td>
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

@section('script')
    <script>
        $(document).ready(function() {
            // Ambil instance DataTable yang sudah ada
            var table = $('#table-6').DataTable();

            // Event listener untuk dropdown filter
            $('#filter-nama').on('change', function() {
                var selectedName = $(this).val();

                // Lakukan pencarian pada kolom ke-2 (index 1: Nama Anggota)
                if (selectedName) {
                    // Menggunakan pencarian smart: false dan regex: true untuk pencarian eksak
                    // Tapi untuk nama orang, pencarian string biasa seringkali cukup,
                    // namun untuk menghindari "Budi" match "Budi Santoso", kita pakai regex
                    var regex = '^' + $.fn.dataTable.util.escapeRegex(selectedName) + '$';
                    table.column(1).search(regex, true, false).draw();
                } else {
                    // Reset pencarian
                    table.column(1).search('').draw();
                }
            });
        });
    </script>
@endsection
