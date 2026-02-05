@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Data Pengajuan Pinjaman</h4>
                    </div>

                    <div class="card-body pt-2">
                        <div class="d-flex justify-content-end align-items-end">
                            <span class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                                data-bs-target="#tambahModal">Tambah</span>
                        </div>
                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Nominal Pinjaman</th>
                                        <th>Nominal Bagihasil</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuan as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ 'Rp ' . number_format($item->nominal_pinjaman, 0, ',', '.') }}</td>
                                            <td>{{ 'Rp ' . number_format($item->nominal_bagihasil, 0, ',', '.') }}</td>
                                            <td>{{ $item->tanggal_pengajuan }}</td>
                                            <td class="text-center">
                                                @if ($item->keterangan === 'sudah lunas')
                                                    <button
                                                        class="btn btn-success text-white btn-sm">{{ $item->keterangan }}</button>
                                                @else
                                                    <button class="btn text-white btn-sm"
                                                        style="background-color: darkgrey">{{ $item->keterangan }}</button>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="#" class="btn btn-icon btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $item->id }}"><i
                                                        class="bi bi-trash fs-18"></i></a>
                                                <a href="#" class="btn btn-icon btn-sm btn-warning"
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}"><i
                                                        class="bi bi-pencil-square fs-18"></i></a>
                                            </td>
                                            {{-- modalHapus --}}
                                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-body text-center p-4">
                                                            <div class="mb-3 text-danger">
                                                                <i class="bi bi-exclamation-circle display-4"></i>
                                                            </div>
                                                            <h5 class="fw-bold mb-2">Hapus Data?</h5>
                                                            <p class="text-muted small mb-4">Data pengajuan
                                                                "{{ $item->user->name }}"
                                                                akan dihapus permanen.</p>
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-light btn-sm px-3 fw-medium"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <a href="/tagihan/pengajuan/delete/{{ $item->id }}"
                                                                    class="btn btn-danger btn-sm px-3 fw-bold">Hapus</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- modalEdit --}}
                                            <div class="modal modal-lg fade" id="editModal{{ $item->id }}"
                                                tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title text-white">Edit Data Pengajuan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST"
                                                            action="/tagihan/pengajuan/edit/{{ $item->id }}">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">
                                                                    <!-- Section 1: Informasi Pengaju -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-person-badge me-2"></i>Informasi
                                                                            Pengaju</h6>
                                                                        <div class="p-3 bg-light">
                                                                            <div class="row g-3">
                                                                                <div class="col-12">
                                                                                    <label
                                                                                        class="form-label small text-muted fw-bold">Nama
                                                                                        User/Anggota</label>
                                                                                    <select name="id_user"
                                                                                        class="form-control" required>
                                                                                        <option value="{{ $item->id_user }}"
                                                                                            selected>
                                                                                            {{ $item->user->name }}
                                                                                        </option>
                                                                                        @foreach ($user as $data)
                                                                                            <option
                                                                                                value="{{ $data->id }}">
                                                                                                {{ $data->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Section 2: Detail Pinjaman -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-cash-stack me-2"></i>Detail
                                                                            Pinjaman</h6>
                                                                        <div
                                                                            class="p-3 bg-primary-subtle border-primary-subtle">
                                                                            <div class="row g-3">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label small text-primary fw-bold">Nominal
                                                                                        Pinjaman</label>
                                                                                    <div class="input-group">
                                                                                        <input type="text"
                                                                                            class="form-control fw-bold currency-input"
                                                                                            name="nominal_pinjaman"
                                                                                            value="{{ $item->nominal_pinjaman }}"
                                                                                            placeholder="Masukan nominal pinjaman">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label small text-primary fw-bold">Nominal
                                                                                        Bagihasil</label>
                                                                                    <div class="input-group">
                                                                                        <input type="text"
                                                                                            class="form-control fw-bold currency-input"
                                                                                            name="nominal_bagihasil"
                                                                                            value="{{ $item->nominal_bagihasil }}"
                                                                                            required
                                                                                            placeholder="Masukan nominal bagihasil">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Section 3: Waktu & Status -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-calendar-check me-2"></i>Waktu
                                                                            Pengajuan
                                                                        </h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Tanggal
                                                                                    Pengajuan</label>
                                                                                <input type="date" class="form-control"
                                                                                    name="tanggal_pengajuan" required
                                                                                    value="{{ $item->tanggal_pengajuan }}">
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
                                                                    Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- modalTambah --}}
                <div class="modal modal-lg fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white">Tambah Data Pengajuan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="/tagihan/pengajuan/create">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        <!-- Section 1: Informasi Pengaju -->
                                        <div class="col-12">
                                            <h6 class="text-primary fw-bold mb-3"><i
                                                    class="bi bi-person-badge me-2"></i>Informasi Pengaju</h6>
                                            <div class="p-3 bg-light">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label small text-muted fw-bold">Nama
                                                            User/Anggota</label>
                                                        <select name="id_user" id="id_user" class="form-control"
                                                            required>
                                                            <option value="" selected disabled>Pilih nama
                                                                user/anggota</option>
                                                            @foreach ($user as $data)
                                                                <option value="{{ $data->id }}">{{ $data->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section 2: Detail Pinjaman -->
                                        <div class="col-12">
                                            <h6 class="text-primary fw-bold mb-3"><i
                                                    class="bi bi-cash-stack me-2"></i>Detail Pinjaman</h6>
                                            <div class="p-3 bg-primary-subtle border-primary-subtle">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-primary fw-bold">Nominal
                                                            Pinjaman</label>
                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control fw-bold currency-input"
                                                                name="nominal_pinjaman"
                                                                placeholder="Masukan nominal pinjaman">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-primary fw-bold">Nominal
                                                            Bagihasil</label>
                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control fw-bold currency-input"
                                                                name="nominal_bagihasil" required
                                                                placeholder="Masukan nominal bagihasil">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section 3: Waktu & Status -->
                                        <div class="col-12">
                                            <h6 class="text-primary fw-bold mb-3"><i
                                                    class="bi bi-calendar-check me-2"></i>Waktu Pengajuan</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small text-muted fw-bold">Tanggal
                                                        Pengajuan</label>
                                                    <input type="date" class="form-control" name="tanggal_pengajuan"
                                                        required value="{{ date('Y-m-d') }}">
                                                </div>
                                                <!-- Keterangan is optional/commented in original, but added here for structure if needed, or omitted to match original functionality -->
                                                {{-- <div class="col-md-6">
                                                    <label class="form-label small text-muted fw-bold">Keterangan</label>
                                                    <textarea class="form-control" name="keterangan" rows="1"></textarea>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light px-4 py-3">
                                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Simpan</button>
                                </div>
                            </form>
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
            const currencyInputs = document.querySelectorAll('.currency-input');

            currencyInputs.forEach(input => {
                // Format initial value if exists
                if (input.value) {
                    input.value = formatRupiah(input.value, 'Rp ');
                }

                input.addEventListener('keyup', function(e) {
                    input.value = formatRupiah(this.value, 'Rp ');
                });
            });

            // Clean up currency symbols before submit
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    currencyInputs.forEach(input => {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    });
                });
            });
        });

        function formatRupiah(angka, prefix) {
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
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }
    </script>
@endsection
