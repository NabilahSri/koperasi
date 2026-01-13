@extends('component.template')

@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="row g-4">
                    <!-- Kolom Kiri: Form Tambah/Edit -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 1;">
                            <div class="card-header bg-transparent border-0 p-4 pb-0">
                                <h5 class="mb-0 text-primary fw-bold" id="formTitle"><i
                                        class="bi bi-plus-circle me-2"></i>Tambah Jenis</h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" action="/jenis/create" id="jenisForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="namaJenis" class="form-label fw-bold small text-muted">Nama
                                            Jenis</label>
                                        <input type="text" class="form-control" name="nama" id="namaJenis" required
                                            placeholder="Contoh: Simpanan Wajib">
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold" id="btnSubmit">
                                            <i class="bi bi-save me-1"></i> Simpan
                                        </button>
                                        <button type="button" class="btn btn-outline-danger fw-medium" id="btnBatal"
                                            style="display: none;" onclick="resetForm()">
                                            <i class="bi bi-x-circle me-1"></i> Batal Edit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Tabel Data -->
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4 pb-0">
                                <h4 class="mb-0 fw-bold">Data Jenis</h4>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table id="table-6" class="display text-center table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="10%">No</th>
                                                <th>Jenis</th>
                                                <th width="20%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jenis as $key => $item)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $item->nama }}</td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button type="button"
                                                                class="btn btn-icon btn-sm btn-warning text-white"
                                                                onclick="editData('{{ $item->id }}', '{{ $item->nama }}')"
                                                                data-bs-toggle="tooltip" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-icon btn-sm btn-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal{{ $item->id }}"
                                                                title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Modal Delete -->
                                                <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-body text-center p-4">
                                                                <div class="mb-3 text-danger">
                                                                    <i class="bi bi-exclamation-circle display-4"></i>
                                                                </div>
                                                                <h5 class="fw-bold mb-2">Hapus Data?</h5>
                                                                <p class="text-muted small mb-4">Data "{{ $item->nama }}"
                                                                    akan dihapus permanen.</p>
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    <button type="button"
                                                                        class="btn btn-light btn-sm px-3 fw-medium"
                                                                        data-bs-dismiss="modal">Batal</button>
                                                                    <a href="/jenis/delete/{{ $item->id }}"
                                                                        class="btn btn-danger btn-sm px-3 fw-bold">Hapus</a>
                                                                </div>
                                                            </div>
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
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script>
        function editData(id, nama) {
            // Ubah Judul dan Icon
            const titleEl = document.getElementById('formTitle');
            titleEl.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Jenis';
            titleEl.classList.remove('text-primary');
            titleEl.classList.add('text-warning');

            // Isi Value
            document.getElementById('namaJenis').value = nama;

            // Ubah Action Form
            document.getElementById('jenisForm').action = '/jenis/edit/' + id;

            // Ubah Tombol Simpan
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update';
            btnSubmit.classList.remove('btn-primary');
            btnSubmit.classList.add('btn-warning', 'text-white');

            // Tampilkan Tombol Batal
            document.getElementById('btnBatal').style.display = 'block';

            // Scroll ke form (untuk mobile)
            document.getElementById('jenisForm').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Fokus ke input
            document.getElementById('namaJenis').focus();
        }

        function resetForm() {
            // Reset Judul
            const titleEl = document.getElementById('formTitle');
            titleEl.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Tambah Jenis';
            titleEl.classList.remove('text-warning');
            titleEl.classList.add('text-primary');

            // Reset Value
            document.getElementById('namaJenis').value = '';

            // Reset Action Form
            document.getElementById('jenisForm').action = '/jenis/create';

            // Reset Tombol Simpan
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.innerHTML = '<i class="bi bi-save me-1"></i> Simpan';
            btnSubmit.classList.remove('btn-warning', 'text-white');
            btnSubmit.classList.add('btn-primary');

            // Sembunyikan Tombol Batal
            document.getElementById('btnBatal').style.display = 'none';
        }
    </script>
@endsection
