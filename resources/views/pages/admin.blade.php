@extends('component.template')

@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <div class="row">
                            <div class="col-md-6 col-12 text-muted">
                                <h4 class="mb-0">Data Admin</h4>
                            </div>
                        </div>

                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-end align-items-end">
                                <span class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                                    data-bs-target="#primaryModal">Tambah</span>
                            </div>
                            <div class="table-responsive">
                                <table id="table-6" class="display text-center">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Anggota</th>
                                            <th>Email</th>
                                            <th>Iuran Wajib</th>
                                            <th>Iuran Pokok</th>
                                            <th>No Hp</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admin as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <div class="employee d-flex gap-2 flex-wrap">
                                                        <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                            <img src="{{ asset('storage/' . $item->foto) }}" alt="img"
                                                                width="50">
                                                        </div>
                                                        <div class="description mt-2">
                                                            <h6>{{ $item->name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item->email }}</td>
                                                <td>{{ 'Rp ' . number_format($item->iuran_wajib !== null ? $item->iuran_wajib : 0, 0, ',', '.') }}
                                                </td>
                                                <td>{{ 'Rp ' . number_format($item->iuran_pokok !== null ? $item->iuran_pokok : 0, 0, ',', '.') }}
                                                </td>
                                                <td>{{ $item->nohp }}</td>
                                                <td class="text-center">
                                                    <a href="#"
                                                        class="btn btn-icon btn-sm btn-danger"data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $item->id }}"><i
                                                            class="bi bi-trash fs-18"></i></a>
                                                    <a href="#" class="btn btn-icon btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $item->id }}"><i
                                                            class="bi bi-pencil-square fs-18"></i></a>
                                                </td>
                                            </tr>
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
                                                            <p class="text-muted small mb-4">Data "{{ $item->name }}"
                                                                akan dihapus permanen.</p>
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-light btn-sm px-3 fw-medium"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <a href="/users/admin/delete/{{ $item->id }}"
                                                                    class="btn btn-danger btn-sm px-3 fw-bold">Hapus</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal Edit-->
                                            <div class="modal modal-lg fade" id="editModal{{ $item->id }}"
                                                tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title text-white">Ubah Data Admin</h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="/users/admin/edit/{{ $item->id }}"
                                                            enctype="multipart/form-data" method="POST">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">
                                                                    <!-- Section 1: Informasi Akun -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-shield-lock me-2"></i>Informasi
                                                                            Akun</h6>
                                                                        <div class="p-3 bg-light">
                                                                            <div class="row g-3">
                                                                                <div class="col-md-4">
                                                                                    <label
                                                                                        class="form-label small text-muted fw-bold">No
                                                                                        User</label>
                                                                                    <input type="number"
                                                                                        class="form-control" name="no_user"
                                                                                        value="{{ $item->no_user }}"
                                                                                        required>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label
                                                                                        class="form-label small text-muted fw-bold">Email</label>
                                                                                    <input type="email"
                                                                                        class="form-control" name="email"
                                                                                        value="{{ $item->email }}"
                                                                                        required>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <label
                                                                                        class="form-label small text-muted fw-bold">Password</label>
                                                                                    <input type="password"
                                                                                        class="form-control" name="password"
                                                                                        placeholder="(Biarkan kosong jika tidak diubah)">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Section 2: Data Pribadi -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-person-vcard me-2"></i>Data
                                                                            Pribadi</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Nama
                                                                                    Lengkap</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="name"
                                                                                    value="{{ $item->name }}" required>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">No
                                                                                    Handphone</label>
                                                                                <input type="number" class="form-control"
                                                                                    name="nohp"
                                                                                    value="{{ $item->nohp }}" required>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Alamat</label>
                                                                                <textarea rows="3" name="alamat" class="form-control" required>{{ $item->alamat }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Section 3: Keuangan -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-cash-coin me-2"></i>Informasi
                                                                            Keuangan</h6>
                                                                        <div
                                                                            class="p-3 bg-primary-subtle border-primary-subtle">
                                                                            <div class="row g-3">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label small text-primary fw-bold">Iuran
                                                                                        Wajib</label>
                                                                                    <div class="input-group">
                                                                                        <input type="text"
                                                                                            class="form-control fw-bold currency-input"
                                                                                            name="iuran_wajib"
                                                                                            value="{{ $item->iuran_wajib }}"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label small text-primary fw-bold">Iuran
                                                                                        Pokok</label>
                                                                                    <div class="input-group">
                                                                                        <input type="text"
                                                                                            class="form-control fw-bold currency-input"
                                                                                            name="iuran_pokok"
                                                                                            value="{{ $item->iuran_pokok }}"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Section 4: Dokumen & Foto -->
                                                                    <div class="col-12">
                                                                        <h6 class="text-primary fw-bold mb-3"><i
                                                                                class="bi bi-file-earmark-image me-2"></i>Dokumen
                                                                            & Foto</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Upload
                                                                                    KTP <small class="text-muted">(JPG/PNG,
                                                                                        Max 2MB)</small></label>
                                                                                <div class="input-group mb-2">
                                                                                    <input type="file"
                                                                                        class="form-control"
                                                                                        name="ktp"
                                                                                        accept=".jpg,.jpeg,.png"
                                                                                        onchange="previewImage(this, 'preview-ktp-edit-{{ $item->id }}')">
                                                                                </div>
                                                                                <img id="preview-ktp-edit-{{ $item->id }}"
                                                                                    src="{{ $item->ktp ? asset('storage/' . $item->ktp) : '' }}"
                                                                                    class="img-thumbnail"
                                                                                    style="max-height: 150px; display: {{ $item->ktp ? 'block' : 'none' }};">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label small text-muted fw-bold">Foto
                                                                                    Profil <small
                                                                                        class="text-muted">(JPG/PNG, Max
                                                                                        2MB)</small></label>
                                                                                <div class="input-group mb-2">
                                                                                    <input type="file"
                                                                                        class="form-control"
                                                                                        name="foto"
                                                                                        accept=".jpg,.jpeg,.png"
                                                                                        onchange="previewImage(this, 'preview-foto-edit-{{ $item->id }}')">
                                                                                </div>
                                                                                <img id="preview-foto-edit-{{ $item->id }}"
                                                                                    src="{{ $item->foto ? asset('storage/' . $item->foto) : '' }}"
                                                                                    class="img-thumbnail"
                                                                                    style="max-height: 150px; display: {{ $item->foto ? 'block' : 'none' }};">
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Modal Tambah Admin -->
                        <div class="modal modal-lg fade" id="primaryModal" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title text-white">Tambah Data Admin</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="/users/admin/create" enctype="multipart/form-data" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <div class="row g-4">
                                                <!-- Section 1: Informasi Akun -->
                                                <div class="col-12">
                                                    <h6 class="text-primary fw-bold mb-3"><i
                                                            class="bi bi-shield-lock me-2"></i>Informasi Akun</h6>
                                                    <div class="p-3 bg-light">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label small text-muted fw-bold">No
                                                                    User</label>
                                                                <input type="number" class="form-control" name="no_user"
                                                                    placeholder="12345" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label
                                                                    class="form-label small text-muted fw-bold">Email</label>
                                                                <input type="email" class="form-control" name="email"
                                                                    placeholder="email@example.com" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label
                                                                    class="form-label small text-muted fw-bold">Password</label>
                                                                <input type="password" class="form-control"
                                                                    name="password" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 2: Data Pribadi -->
                                                <div class="col-12">
                                                    <h6 class="text-primary fw-bold mb-3"><i
                                                            class="bi bi-person-vcard me-2"></i>Data Pribadi</h6>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small text-muted fw-bold">Nama
                                                                Lengkap</label>
                                                            <input type="text" class="form-control" name="name"
                                                                placeholder="Nama Lengkap" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small text-muted fw-bold">No
                                                                Handphone</label>
                                                            <input type="number" class="form-control" name="nohp"
                                                                placeholder="08123..." required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label
                                                                class="form-label small text-muted fw-bold">Alamat</label>
                                                            <textarea rows="3" name="alamat" class="form-control" placeholder="Alamat Lengkap" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 3: Keuangan -->
                                                <div class="col-12">
                                                    <h6 class="text-primary fw-bold mb-3"><i
                                                            class="bi bi-cash-coin me-2"></i>Informasi Keuangan</h6>
                                                    <div class="p-3 bg-primary-subtle border-primary-subtle">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small text-primary fw-bold">Iuran
                                                                    Wajib</label>
                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control fw-bold currency-input"
                                                                        name="iuran_wajib" placeholder="Rp 0" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small text-primary fw-bold">Iuran
                                                                    Pokok</label>
                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control fw-bold currency-input"
                                                                        name="iuran_pokok" placeholder="Rp 0" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 4: Dokumen & Foto -->
                                                <div class="col-12">
                                                    <h6 class="text-primary fw-bold mb-3"><i
                                                            class="bi bi-file-earmark-image me-2"></i>Dokumen & Foto</h6>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small text-muted fw-bold">Upload KTP
                                                                <small>(JPG/PNG, Max
                                                                    2MB)</small></label>
                                                            <input type="file" class="form-control mb-2"
                                                                name="ktp" required accept=".jpg,.jpeg,.png"
                                                                onchange="previewImage(this, 'preview-ktp-add')">
                                                            <img id="preview-ktp-add" class="img-thumbnail"
                                                                style="max-height: 150px; display: none;">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small text-muted fw-bold">Foto Profil
                                                                <small>(JPG/PNG, Max
                                                                    2MB)</small></label>
                                                            <input type="file" class="form-control mb-2"
                                                                name="foto" required accept=".jpg,.jpeg,.png"
                                                                onchange="previewImage(this, 'preview-foto-add')">
                                                            <img id="preview-foto-add" class="img-thumbnail"
                                                                style="max-height: 150px; display: none;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light px-4 py-3">
                                            <button type="button" class="btn btn-outline-secondary px-4 fw-medium"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Simpan
                                                Data</button>
                                        </div>
                                    </form>
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
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file) {
                // Validasi Tipe File
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    alert('Hanya file JPG dan PNG yang diperbolehkan!');
                    input.value = ''; // Reset input
                    preview.style.display = 'none';
                    return;
                }

                // Validasi Ukuran File
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    input.value = ''; // Reset input
                    preview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currencyInputs = document.querySelectorAll('.currency-input');

            currencyInputs.forEach(input => {
                // Format awal jika ada value
                if (input.value) {
                    input.value = formatRupiah(input.value, 'Rp ');
                }

                input.addEventListener('keyup', function(e) {
                    input.value = formatRupiah(this.value, 'Rp ');
                });
            });

            // Bersihkan format sebelum submit form
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
