@extends('layouts.app')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Riwayat Transaksi Tagihan</h4>
                            @if (in_array(auth()->user()->role, ['admin', 'operator']))
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
                                        @if (auth()->user()->role == 'admin')
                                            <th>Aksi</th>
                                        @endif
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
                                            @if (auth()->user()->role == 'admin')
                                                <td>
                                                    <a href="#" class="btn btn-icon btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editTagihanModal{{ $item->id }}"><i
                                                            class="bi bi-pencil-square fs-18"></i></a>
                                                </td>
                                            @endif
                                        </tr>
                                        @if (auth()->user()->role == 'admin')
                                            <div class="modal modal-lg fade" id="editTagihanModal{{ $item->id }}"
                                                tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title text-white">Ubah Transaksi Tagihan</h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="/histori/tagihan/edit/{{ $item->id }}"
                                                            method="POST" class="tagihan-edit-form">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-person-badge me-2"></i>Informasi
                                                                            Anggota</h6>
                                                                        <div class="p-3 bg-light rounded">
                                                                            <div class="row g-3">
                                                                                <div class="col-md-12">
                                                                                    <label
                                                                                        class="form-label small text-muted fw-bold">Nama
                                                                                        Anggota</label>
                                                                                    <input type="text"
                                                                                        class="form-control bg-white"
                                                                                        value="{{ $item->users->name }}"
                                                                                        readonly>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-receipt me-2"></i>Detail Tagihan
                                                                        </h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Kategori</label>
                                                                                <input type="text"
                                                                                    class="form-control bg-white"
                                                                                    value="{{ $item->kategori->nama }}"
                                                                                    readonly>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Jumlah</label>
                                                                                <input type="text"
                                                                                    class="form-control fw-bold currency-input"
                                                                                    name="jumlah"
                                                                                    value="Rp. {{ number_format($item->jumlah, 0, ',', '.') }}"
                                                                                    required>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Tanggal</label>
                                                                                <input type="text" class="form-control bg-white"
                                                                                    value="{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}"
                                                                                    readonly>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Keterangan</label>
                                                                                <input type="text" class="form-control bg-white"
                                                                                    value="{{ $item->keterangan }}"
                                                                                    readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light px-4 py-3">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary px-4 fw-medium"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary px-4 fw-bold shadow-sm">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
            var table = $('#table-6').DataTable();
            $('#filter-nama').on('change', function() {
                var selectedName = $(this).val();
                if (selectedName) {
                    var regex = '^' + $.fn.dataTable.util.escapeRegex(selectedName) + '$';
                    table.column(1).search(regex, true, false).draw();
                } else {
                    table.column(1).search('').draw();
                }
            });

            const formatRupiah = (angka, prefix) => {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
            };

            $('.currency-input').on('keyup', function() {
                this.value = formatRupiah(this.value, 'Rp. ');
            });

            $('form.tagihan-edit-form').on('submit', function() {
                $(this)
                    .find('.currency-input')
                    .each(function() {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
            });
        });
    </script>
@endsection
