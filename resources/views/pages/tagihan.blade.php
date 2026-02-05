@extends('component.template')

@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Bayar Tagihan</h4>
                    </div>

                    <div class="card-body pt-2">
                        <div class="d-flex justify-content-end align-items-end">
                            {{-- <span class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                                    data-bs-target="#primaryModal">Tambah</span> --}}
                        </div>
                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Jumlah Pinjaman</th>
                                        <th>Sisa Pinjaman</th>
                                        <th>Jumlah Bagi Hasil</th>
                                        <th>Total Bagi Hasil Terbayar</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuan as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>Rp {{ number_format($item->nominal_pinjaman, 0, ',', '.') }}</td>
                                            @php
                                                $total_pembayaran = 0;
                                                $total_bagihasil = 0;
                                            @endphp
                                            @foreach ($tagihan as $value)
                                                @if ($value->id_pengajuan == $item->id)
                                                    @if ($value->id_kategori == 3)
                                                        @php
                                                            $total_pembayaran += $value->jumlah;
                                                        @endphp
                                                    @endif

                                                    @if ($value->id_kategori == 4)
                                                        @php
                                                            $total_bagihasil += $value->jumlah;
                                                        @endphp
                                                    @endif
                                                @endif
                                            @endforeach
                                            <td>Rp
                                                {{ number_format($item->nominal_pinjaman - $total_pembayaran, 0, ',', '.') }}
                                            </td>
                                            <td>Rp {{ number_format($item->nominal_bagihasil, 0, ',', '.') }}
                                            </td>
                                            <td>Rp
                                                {{ number_format($total_bagihasil, 0, ',', '.') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-success text-white px-3 shadow-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#bayarModal{{ $item->id }}">Bayar</a>
                                            </td>
                                        </tr>
                                        {{-- Bayar --}}
                                        <div class="modal fade" id="bayarModal{{ $item->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title text-white">Bayar Tagihan </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form method="post" action="/tagihan/bayar/create/{{ $item->id }}">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <div class="row g-4">
                                                                <!-- Section 1: Informasi Pembayar -->
                                                                <div class="col-12">
                                                                    <h6 class="text-primary fw-bold mb-3"><i
                                                                            class="bi bi-person-badge me-2"></i>Informasi
                                                                        Pembayar</h6>
                                                                    <div class="p-3 bg-light rounded">
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Nama
                                                                                    Anggota</label>
                                                                                <input type="text"
                                                                                    class="form-control bg-white"
                                                                                    name="nama"
                                                                                    value="{{ $item->user->name }}"
                                                                                    readonly>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Tanggal
                                                                                    Pembayaran</label>
                                                                                <input type="date" class="form-control"
                                                                                    name="tanggal"
                                                                                    value="{{ date('Y-m-d') }}" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Section 2: Rincian Pembayaran -->
                                                                <div class="col-12">
                                                                    <h6 class="text-primary fw-bold mb-3"><i
                                                                            class="bi bi-cash-stack me-2"></i>Rincian
                                                                        Pembayaran</h6>
                                                                    <div
                                                                        class="p-3 bg-primary-subtle border-primary-subtle rounded">
                                                                        <div class="row g-3"
                                                                            style="max-height: 400px; overflow-y: auto;">
                                                                            @foreach ($kategori as $data)
                                                                                @php
                                                                                    $isBagiHasil =
                                                                                        $data->id == 4 ||
                                                                                        stripos(
                                                                                            $data->nama,
                                                                                            'Bagi Hasil',
                                                                                        ) !== false;
                                                                                    $inputValue = '';
                                                                                    $readonlyAttribute = '';
                                                                                    $bgStyle = '';

                                                                                    if ($isBagiHasil) {
                                                                                        // $sisaBagiHasil = max(0, $item->nominal_bagihasil - $total_bagihasil);
                                                                                        $inputValue =
                                                                                            'Rp. ' .
                                                                                            number_format(
                                                                                                $item->nominal_bagihasil,
                                                                                                0,
                                                                                                ',',
                                                                                                '.',
                                                                                            );
                                                                                        $readonlyAttribute = 'readonly';
                                                                                        $bgStyle =
                                                                                            'background-color: #e9ecef;';
                                                                                    }
                                                                                @endphp
                                                                                <div class="col-12">
                                                                                    <div
                                                                                        class="card border shadow-none hover-shadow-sm transition-all">
                                                                                        <div
                                                                                            class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                                                                                            <label
                                                                                                class="form-label mb-0 fw-medium text-dark flex-grow-1">
                                                                                                {{ $data->nama }}
                                                                                            </label>
                                                                                            <div class="input-group"
                                                                                                style="width: 200px;">
                                                                                                <input type="hidden"
                                                                                                    name="transaksi[{{ $data->id }}][id_kategori]"
                                                                                                    value="{{ $data->id }}">
                                                                                                <input type="text"
                                                                                                    class="form-control fw-bold text-end currency-input"
                                                                                                    name="transaksi[{{ $data->id }}][jumlah]"
                                                                                                    placeholder="Rp 0"
                                                                                                    value="{{ $inputValue }}"
                                                                                                    {{ $readonlyAttribute }}
                                                                                                    style="{{ $bgStyle }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                            <div class="col-12">
                                                                                <label
                                                                                    class="form-label small text-primary fw-bold">Keterangan</label>
                                                                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
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
                                                                    class="btn btn-primary px-4 fw-bold shadow-sm">Simpan
                                                                    Transaksi</button>
                                                            </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Format Rupiah
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

            // Apply to all currency inputs
            document.querySelectorAll('.currency-input').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value, 'Rp. ');
                });
            });

            // Clean up before submit
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const currencyInputs = this.querySelectorAll('.currency-input');
                    currencyInputs.forEach(input => {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    });
                });
            });
        });
    </script>
@endsection
