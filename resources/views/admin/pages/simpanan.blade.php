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
                            <div class="col-12 col-md-3">
                                <label class="form-label small text-muted">Nama Anggota</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm" id="filterUserSearch"
                                        placeholder="Cari nama anggota...">
                                    <input type="hidden" name="filter_user_id" id="filter_user_id_hidden"
                                        value="{{ $filter_user_id ?? '' }}">
                                    <div id="filterUserDropdown" class="dropdown-menu w-100 mt-1"
                                        style="max-height: 220px; overflow-y: auto; display: none;">
                                        <button type="button" class="dropdown-item" data-id=""
                                            data-name="Semua">Semua</button>
                                        @foreach ($user as $u)
                                            <button type="button" class="dropdown-item" data-id="{{ $u->id }}"
                                                data-name="{{ $u->name }}">{{ $u->name }}</button>
                                        @endforeach
                                    </div>
                                </div>
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
                            <div class="col-12 col-md-3 d-flex gap-2 justify-content-center">
                                <a href="/simpanan" class="btn btn-success btn-sm text-white">Reset</a>
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
                                                    data-user-name="{{ $item->user->name }}"
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
                                                <label class="form-label text-muted small fw-semibold">Pilih
                                                    Anggota</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" id="editUserSearch"
                                                        placeholder="Cari nama anggota..." autocomplete="off">
                                                    <input type="hidden" name="id_user" id="edit_user_id_hidden"
                                                        required>
                                                    <div id="editUserDropdown" class="dropdown-menu w-100 mt-1"
                                                        style="max-height: 220px; overflow-y: auto; display: none;">
                                                        @foreach ($user as $data)
                                                            <button type="button" class="dropdown-item"
                                                                data-id="{{ $data->id }}"
                                                                data-name="{{ $data->name }}">{{ $data->name }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
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
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" id="addUserSearch"
                                                        placeholder="Cari nama anggota..." autocomplete="off">
                                                    <input type="hidden" name="id_user" id="add_user_id_hidden"
                                                        required>
                                                    <div id="addUserDropdown" class="dropdown-menu w-100 mt-1"
                                                        style="max-height: 220px; overflow-y: auto; display: none;">
                                                        @foreach ($user as $data)
                                                            <button type="button" class="dropdown-item"
                                                                data-id="{{ $data->id }}"
                                                                data-name="{{ $data->name }}">{{ $data->name }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
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
                                            <div class="mt-3 border-0 bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold text-dark">Total Nominal Dibayar</span>
                                                    <span id="total-nominal-add" class="fw-bold text-primary">Rp 0</span>
                                                </div>
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
            const fInput = document.getElementById('filterUserSearch');
            const fHidden = document.getElementById('filter_user_id_hidden');
            const fDrop = document.getElementById('filterUserDropdown');
            if (fInput && fHidden && fDrop) {
                const items = Array.from(fDrop.querySelectorAll('.dropdown-item'));

                function showDrop() {
                    fDrop.style.display = 'block';
                }

                function hideDrop() {
                    fDrop.style.display = 'none';
                }

                function setSelection(id, name) {
                    fHidden.value = id;
                    fInput.value = name || '';
                }

                function filterList(q) {
                    const s = (q || '').toLowerCase();
                    items.forEach(it => {
                        const n = String(it.getAttribute('data-name') || '').toLowerCase();
                        it.style.display = s ? (n.includes(s) ? '' : 'none') : '';
                    });
                }
                if (fHidden.value) {
                    const found = items.find(it => String(it.getAttribute('data-id')) === String(fHidden.value));
                    if (found) fInput.value = found.getAttribute('data-name') || '';
                } else {
                    fInput.value = '';
                }
                fInput.addEventListener('focus', function() {
                    showDrop();
                    filterList(this.value);
                });
                fInput.addEventListener('input', function() {
                    showDrop();
                    filterList(this.value);
                });
                items.forEach(it => {
                    it.addEventListener('click', function() {
                        setSelection(this.getAttribute('data-id') || '', this.getAttribute(
                            'data-name') || '');
                        hideDrop();
                    });
                });
                document.addEventListener('click', function(e) {
                    if (!fDrop.contains(e.target) && e.target !== fInput) hideDrop();
                });
            }
            const aInput = document.getElementById('addUserSearch');
            const aHidden = document.getElementById('add_user_id_hidden');
            const aDrop = document.getElementById('addUserDropdown');
            if (aInput && aHidden && aDrop) {
                const items = Array.from(aDrop.querySelectorAll('.dropdown-item'));

                function showDrop() {
                    aDrop.style.display = 'block';
                }

                function hideDrop() {
                    aDrop.style.display = 'none';
                }

                function setSelection(id, name) {
                    aHidden.value = id;
                    aInput.value = name || '';
                    if (typeof window.autoFillAmounts === 'function') window.autoFillAmounts();
                }

                function filterList(q) {
                    const s = (q || '').toLowerCase();
                    items.forEach(it => {
                        const n = String(it.getAttribute('data-name') || '').toLowerCase();
                        it.style.display = s ? (n.includes(s) ? '' : 'none') : '';
                    });
                }
                aInput.addEventListener('focus', function() {
                    showDrop();
                    filterList(this.value);
                });
                aInput.addEventListener('input', function() {
                    showDrop();
                    filterList(this.value);
                });
                items.forEach(it => {
                    it.addEventListener('click', function() {
                        setSelection(this.getAttribute('data-id') || '', this.getAttribute(
                            'data-name') || '');
                        hideDrop();
                    });
                });
                document.addEventListener('click', function(e) {
                    if (!aDrop.contains(e.target) && e.target !== aInput) hideDrop();
                });
            }
            const eInput = document.getElementById('editUserSearch');
            const eHidden = document.getElementById('edit_user_id_hidden');
            const eDrop = document.getElementById('editUserDropdown');
            if (eInput && eHidden && eDrop) {
                const items = Array.from(eDrop.querySelectorAll('.dropdown-item'));

                function showDrop() {
                    eDrop.style.display = 'block';
                }

                function hideDrop() {
                    eDrop.style.display = 'none';
                }

                function setSelection(id, name) {
                    eHidden.value = id;
                    eInput.value = name || '';
                }

                function filterList(q) {
                    const s = (q || '').toLowerCase();
                    items.forEach(it => {
                        const n = String(it.getAttribute('data-name') || '').toLowerCase();
                        it.style.display = s ? (n.includes(s) ? '' : 'none') : '';
                    });
                }
                eInput.addEventListener('focus', function() {
                    showDrop();
                    filterList(this.value);
                });
                eInput.addEventListener('input', function() {
                    showDrop();
                    filterList(this.value);
                });
                items.forEach(it => {
                    it.addEventListener('click', function() {
                        setSelection(this.getAttribute('data-id') || '', this.getAttribute(
                            'data-name') || '');
                        hideDrop();
                    });
                });
                document.addEventListener('click', function(e) {
                    if (!eDrop.contains(e.target) && e.target !== eInput) hideDrop();
                });
            }
            document.querySelectorAll('.currency-input, .rupiah-input').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value, 'Rp. ');
                    if (this.closest('#formCreateSimpanan')) {
                        updateTotalNominalAdd();
                    }
                });
                input.addEventListener('change', function() {
                    if (this.closest('#formCreateSimpanan')) {
                        updateTotalNominalAdd();
                    }
                });
                input.addEventListener('input', function() {
                    if (this.closest('#formCreateSimpanan')) {
                        updateTotalNominalAdd();
                    }
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

            function parseRupiahToNumber(str) {
                if (!str) return 0;
                return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
            }

            function updateTotalNominalAdd() {
                const form = document.getElementById('formCreateSimpanan');
                if (!form) return;
                const inputs = form.querySelectorAll('input.rupiah-input[name^="transaksi"]');
                let total = 0;
                inputs.forEach(inp => {
                    total += parseRupiahToNumber(inp.value);
                });
                const el = document.getElementById('total-nominal-add');
                if (el) el.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            }

            document.addEventListener('shown.bs.modal', function(e) {
                const modal = e.target;
                if (modal && modal.id === 'tambahModal') {
                    updateTotalNominalAdd();
                }
            });

            window.autoFillAmounts = window.autoFillAmounts || function() {
                updateTotalNominalAdd();
            };

            // Autofill data pada modal edit dari atribut data-*
            const editButtons = document.querySelectorAll('.btn-edit-simpanan');
            const editForm = document.getElementById('formEditSimpanan');
            editButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (!editForm) return;
                    const id = btn.getAttribute('data-id') || '';
                    const idUser = btn.getAttribute('data-id-user') || '';
                    const userName = btn.getAttribute('data-user-name') || '';
                    const namaPenyetor = btn.getAttribute('data-nama-penyetor') || '';
                    const tanggal = (btn.getAttribute('data-tanggal') || '').substring(0, 10);
                    const keterangan = btn.getAttribute('data-keterangan') || '';
                    const kategoriNama = btn.getAttribute('data-kategori-nama') || '';
                    const idKategori = btn.getAttribute('data-id-kategori') || '';
                    const jumlah = btn.getAttribute('data-jumlah') || '';

                    editForm.setAttribute('action', '/simpanan/edit/' + id);
                    const selectUser = editForm.querySelector('[name="id_user"]');
                    if (selectUser) selectUser.value = idUser;
                    const eHidden2 = document.getElementById('edit_user_id_hidden');
                    const eInput2 = document.getElementById('editUserSearch');
                    if (eHidden2) eHidden2.value = idUser;
                    if (eInput2) eInput2.value = userName;
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
