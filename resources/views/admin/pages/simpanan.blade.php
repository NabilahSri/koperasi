@extends('layouts.app')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Data Simpanan</h4>
                    </div>

                    <div class="card-body pt-2">
                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="get" action="/simpanan" class="row g-3 align-items-end mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted">Nama Anggota</label>
                                <select name="filter_user_id" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    @foreach ($user as $u)
                                        <option value="{{ $u->id }}"
                                            {{ isset($filter_user_id) && $filter_user_id == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small text-muted">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ $start_date ?? '' }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small text-muted">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ $end_date ?? '' }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end">
                                <a href="/simpanan" class="btn btn-light btn-sm">Reset</a>
                                <button type="submit" class="btn btn-warning btn-sm text-white">Filter</button>
                                <span class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#tambahModal">Tambah</span>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Nama Petugas</th>
                                        <th>Kategori</th>
                                        <th>Nama Penyetor</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($simpanan as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->petugas->name }}</td>
                                            <td>{{ $item->kategori->nama }}</td>
                                            <td>{{ $item->nama_penyetor }}</td>
                                            <td>Rp
                                                {{ number_format($item->jumlah !== null ? $item->jumlah : 0, 0, ',', '.') }}
                                            </td>
                                            <td data-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-icon btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $item->id }}"><i
                                                        class="bi bi-trash fs-18"></i></a>
                                                <a href="#" class="btn btn-icon btn-sm btn-warning btn-edit-simpanan"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="{{ $item->id }}" data-id-user="{{ $item->id_user }}"
                                                    data-nama-penyetor="{{ $item->nama_penyetor }}"
                                                    data-tanggal="{{ $item->tanggal }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    data-kategori-nama="{{ $item->kategori->nama }}"
                                                    data-id-kategori="{{ $item->id_kategori }}"
                                                    data-jumlah="{{ $item->jumlah }}"><i
                                                        class="bi bi-pencil-square fs-18"></i></a>
                                            </td>
                                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-body text-center p-4">
                                                            <div class="mb-3 text-danger">
                                                                <i class="bi bi-exclamation-circle display-4"></i>
                                                            </div>
                                                            <h5 class="fw-bold mb-2">Hapus Data?</h5>
                                                            <p class="text-muted small mb-4">Data simpanan
                                                                "{{ $item->user->name }}"
                                                                akan dihapus permanen.</p>
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-light btn-sm px-3 fw-medium"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <a href="/simpanan/delete/{{ $item->id }}"
                                                                    class="btn btn-danger btn-sm px-3 fw-bold">Hapus</a>
                                                            </div>
                                                        </div>
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

                <div class="modal modal-lg fade" id="editModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white">Edit Data Simpanan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="" class="formEditSimpanan" id="formEditSimpanan">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-5 border-end">
                                            <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                                <i class="bi bi-person-vcard fs-5 me-2"></i>
                                                Data Anggota
                                            </h6>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Pilih Anggota</label>
                                                <select name="id_user" class="form-select" required>
                                                    @foreach ($user as $data)
                                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Nama
                                                    Penyetor</label>
                                                <input type="text" class="form-control bg-light" name="nama_penyetor"
                                                    required placeholder="Nama penyetor">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Tanggal
                                                    Transaksi</label>
                                                <input type="date" class="form-control bg-light" name="tanggal"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Catatan</label>
                                                <textarea class="form-control bg-light" name="keterangan" rows="3" placeholder="Tulis keterangan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                                <i class="bi bi-cash-stack fs-5 me-2"></i>
                                                Rincian Simpanan
                                            </h6>
                                            <div class="bg-primary-subtle p-3 rounded mb-3">
                                                <small class="text-primary fw-semibold">
                                                    <i class="bi bi-info-circle-fill me-1"></i>
                                                    Instruksi:
                                                </small>
                                                <p class="mb-0 small text-dark">Perbarui nominal sesuai kategori simpanan
                                                    yang ingin diubah.</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Kategori</label>
                                                <input type="text" class="form-control bg-light" id="kategori_nama"
                                                    readonly>
                                                <input type="hidden" name="id_kategori">
                                            </div>
                                            <div class="card border shadow-none hover-shadow-sm transition-all">
                                                <div
                                                    class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                                                    <label class="form-label mb-0 fw-medium text-dark flex-grow-1">
                                                        Jumlah Bayar
                                                    </label>
                                                    <div class="input-group" style="width: 200px;">
                                                        <input type="text"
                                                            class="form-control fw-bold text-end rupiah-input"
                                                            name="jumlah" placeholder="Rp 0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light px-4 py-3">
                                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium"
                                        data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal modal-lg fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white">Tambah Data Simpanan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="/simpanan/create" id="formCreateSimpanan">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-5 border-end">
                                            <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                                <i class="bi bi-person-vcard fs-5 me-2"></i> Data Anggota
                                            </h6>

                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Pilih
                                                    Anggota</label>
                                                <select name="id_user" id="id_user_add" class="form-control user-select"
                                                    required onchange="autoFillAmounts()">
                                                    <option value="" selected disabled>Cari nama anggota...</option>
                                                    @foreach ($user as $data)
                                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Nama
                                                    Penyetor</label>
                                                <input type="text" class="form-control bg-light" name="nama_penyetor"
                                                    id="validationDefault01" required placeholder="Nama penyetor">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Tanggal
                                                    Transaksi</label>
                                                <input type="date" class="form-control bg-light" name="tanggal"
                                                    id="validationDefault01" required value="{{ date('Y-m-d') }}">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Catatan</label>
                                                <textarea class="form-control bg-light" name="keterangan" rows="3" placeholder="Tulis keterangan..."></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                                <i class="bi bi-cash-stack fs-5 me-2"></i> Rincian Simpanan
                                            </h6>

                                            <div class="bg-primary-subtle p-3 rounded mb-3">
                                                <small class="text-primary fw-semibold"><i
                                                        class="bi bi-info-circle-fill me-1"></i> Instruksi:</small>
                                                <p class="mb-0 small text-dark">Isi nominal pada kategori simpanan yang
                                                    ingin dibayarkan saja.</p>
                                            </div>

                                            <div class="row g-3" style="max-height: 400px; overflow-y: auto;">
                                                @foreach ($kategori as $item)
                                                    <div class="col-12">
                                                        <div
                                                            class="card border shadow-none hover-shadow-sm transition-all">
                                                            <div
                                                                class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                                                                <label
                                                                    class="form-label mb-0 fw-medium text-dark flex-grow-1">
                                                                    {{ $item->nama }}
                                                                </label>
                                                                <div class="input-group" style="width: 200px;">
                                                                    <input type="hidden"
                                                                        name="transaksi[{{ $item->id }}][id_kategori]"
                                                                        value="{{ $item->id }}">
                                                                    <input type="text"
                                                                        class="form-control fw-bold text-end rupiah-input"
                                                                        name="transaksi[{{ $item->id }}][jumlah]"
                                                                        placeholder="Rp 0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light px-4 py-3">
                                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Simpan
                                        Transaksi</button>
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
            document.querySelectorAll('.currency-input, .rupiah-input').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value, 'Rp. ');
                });
            });
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const currencyInputs = this.querySelectorAll('.currency-input, .rupiah-input');
                    currencyInputs.forEach(input => {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    });
                });
            });


            // Autofill data pada modal edit dari atribut data-*
            const editButtons = document.querySelectorAll('.btn-edit-simpanan');
            const editForm = document.getElementById('formEditSimpanan');
            editButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (!editForm) return;
                    const id = btn.getAttribute('data-id') || '';
                    const idUser = btn.getAttribute('data-id-user') || '';
                    const namaPenyetor = btn.getAttribute('data-nama-penyetor') || '';
                    const tanggal = (btn.getAttribute('data-tanggal') || '').substring(0, 10);
                    const keterangan = btn.getAttribute('data-keterangan') || '';
                    const kategoriNama = btn.getAttribute('data-kategori-nama') || '';
                    const idKategori = btn.getAttribute('data-id-kategori') || '';
                    const jumlah = btn.getAttribute('data-jumlah') || '';

                    editForm.setAttribute('action', '/simpanan/edit/' + id);
                    const selectUser = editForm.querySelector('[name="id_user"]');
                    if (selectUser) selectUser.value = idUser;
                    const namaInput = editForm.querySelector('[name="nama_penyetor"]');
                    if (namaInput) namaInput.value = namaPenyetor;
                    const tanggalInput = editForm.querySelector('[name="tanggal"]');
                    if (tanggalInput) tanggalInput.value = tanggal;
                    const ketInput = editForm.querySelector('[name="keterangan"]');
                    if (ketInput) ketInput.value = keterangan;
                    const katNama = document.getElementById('kategori_nama');
                    if (katNama) katNama.value = kategoriNama;
                    const idKatInput = editForm.querySelector('[name="id_kategori"]');
                    if (idKatInput) idKatInput.value = idKategori;
                    const jumlahInput = editForm.querySelector('[name="jumlah"]');
                    if (jumlahInput) jumlahInput.value = formatRupiah(String(jumlah), 'Rp. ');
                });
            });
        });
    </script>
@endsection
