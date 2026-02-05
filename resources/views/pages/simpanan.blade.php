@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <h4 class="mb-0">Data Simpanan</h4>
                    </div>

                    <div class="card-body pt-2">
                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted">Nama Anggota</label>
                                <select id="filter-name" class="form-select form-select-sm">
                                    <option value="">Semua</option>
                                    @foreach ($user as $u)
                                        <option value="{{ $u->name }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small text-muted">Dari Tanggal</label>
                                <input type="date" id="filter-start" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small text-muted">Sampai Tanggal</label>
                                <input type="date" id="filter-end" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end">
                                <button type="button" id="filter-reset" class="btn btn-light btn-sm">Reset</button>
                                <span class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#tambahModal">Tambah</span>
                            </div>
                        </div>
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
                                                <a href="#" class="btn btn-icon btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $item->id }}"><i
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
                                            {{-- modalEdit --}}
                                            <div class="modal modal-lg fade" id="editModal{{ $item->id }}"
                                                tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title text-white">Edit Data Simpanan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="/simpanan/edit/{{ $item->id }}"
                                                            class="formEditSimpanan">
                                                            @csrf
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">
                                                                    <div class="col-md-5 border-end">
                                                                        <h6
                                                                            class="text-primary fw-bold mb-3 d-flex align-items-center">
                                                                            <i class="bi bi-person-vcard fs-5 me-2"></i>
                                                                            Data Anggota
                                                                        </h6>
                                                                        <div class="mb-3">
                                                                            <label
                                                                                class="form-label text-muted small fw-semibold">Pilih
                                                                                Anggota</label>
                                                                            <select name="id_user" class="form-select"
                                                                                required>
                                                                                <option value="{{ $item->id_user }}"
                                                                                    selected>{{ $item->user->name }}
                                                                                </option>
                                                                                @foreach ($user as $data)
                                                                                    <option value="{{ $data->id }}">
                                                                                        {{ $data->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label
                                                                                class="form-label text-muted small fw-semibold">Nama
                                                                                Penyetor</label>
                                                                            <input type="text"
                                                                                class="form-control bg-light"
                                                                                name="nama_penyetor" required
                                                                                placeholder="Nama penyetor"
                                                                                value="{{ $item->nama_penyetor }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label
                                                                                class="form-label text-muted small fw-semibold">Tanggal
                                                                                Transaksi</label>
                                                                            <input type="date"
                                                                                class="form-control bg-light"
                                                                                name="tanggal" required
                                                                                value="{{ $item->tanggal }}">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label
                                                                                class="form-label text-muted small fw-semibold">Catatan</label>
                                                                            <textarea class="form-control bg-light" name="keterangan" rows="3" placeholder="Tulis keterangan...">{{ $item->keterangan }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <h6
                                                                            class="text-primary fw-bold mb-3 d-flex align-items-center">
                                                                            <i class="bi bi-cash-stack fs-5 me-2"></i>
                                                                            Rincian Simpanan
                                                                        </h6>
                                                                        <div class="bg-primary-subtle p-3 rounded mb-3">
                                                                            <small class="text-primary fw-semibold"><i
                                                                                    class="bi bi-info-circle-fill me-1"></i>
                                                                                Instruksi:</small>
                                                                            <p class="mb-0 small text-dark">Perbarui
                                                                                nominal sesuai kategori simpanan yang ingin
                                                                                diubah.</p>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label
                                                                                class="form-label text-muted small fw-semibold">Kategori</label>
                                                                            <input type="text"
                                                                                class="form-control bg-light"
                                                                                value="{{ $item->kategori->nama }}"
                                                                                readonly>
                                                                            <input type="hidden" name="id_kategori"
                                                                                value="{{ $item->id_kategori }}">
                                                                        </div>
                                                                        <div
                                                                            class="card border shadow-none hover-shadow-sm transition-all">
                                                                            <div
                                                                                class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                                                                                <label
                                                                                    class="form-label mb-0 fw-medium text-dark flex-grow-1">
                                                                                    Jumlah Bayar
                                                                                </label>
                                                                                <div class="input-group"
                                                                                    style="width: 200px;">
                                                                                    <input type="text"
                                                                                        class="form-control fw-bold text-end rupiah-input"
                                                                                        name="jumlah" placeholder="Rp 0"
                                                                                        value="{{ $item->jumlah }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light px-4 py-3">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary px-4 fw-medium"
                                                                    data-bs-dismiss="modal">
                                                                    Batal
                                                                </button>
                                                                <button type="submit"
                                                                    class="btn btn-primary px-4 fw-bold shadow-sm">
                                                                    Simpan Perubahan
                                                                </button>
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
                                        <!-- Left Side: User Information -->
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

                                        <!-- Right Side: Transaction Details -->
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
                                                                        class="form-control fw-bold text-end rupiah-input amount-input"
                                                                        name="transaksi[{{ $item->id }}][jumlah]"
                                                                        placeholder="Rp 0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Total Amount Section -->
                                            <div class="mt-3 p-3 bg-light border-primary">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-primary fw-bold">Total Pembayaran</h6>
                                                    <h4 class="mb-0 fw-bold text-primary" id="totalPayment">Rp 0</h4>
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
                                        Simpan Transaksi
                                    </button>
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
        /* Fungsi Format Rupiah */
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

        function calculateTotal() {
            let total = 0;
            $('.amount-input').each(function() {
                let val = $(this).val().replace(/[^0-9]/g, '');
                if (val) {
                    total += parseInt(val);
                }
            });
            $('#totalPayment').text(total > 0 ? formatRupiah(total.toString(), 'Rp ') : 'Rp 0');
        }

        function autoFillAmounts() {
            const userId = document.getElementById('id_user_add').value;
            if (!userId) return;

            // Menggunakan endpoint yang sama untuk mendapatkan data user
            $.get("/simpanan/getJumlah/" + userId + "/0", function(data, status) {
                if (data) {
                    // Auto-fill logic removed as iuran columns are deleted from users table

                    // Recalculate total
                    calculateTotal();
                }
            });
        }

        $(document).ready(function() {
            // Calculate total on input change
            $(document).on('keyup input', '.amount-input', function() {
                calculateTotal();
            });

            $('.user-select').select2({
                dropdownParent: $('#tambahModal')
            });

            if ($.fn.select2) {
                $('#filter-name').select2({
                    placeholder: 'Semua',
                    allowClear: true,
                    width: 'resolve'
                });
            }

            var dt6 = $('#table-6').DataTable();

            $('#filter-name').on('change', function() {
                dt6.column(1).search(this.value).draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'table-6') return true;
                var min = $('#filter-start').val();
                var max = $('#filter-end').val();
                if (!min && !max) return true;
                var api = new $.fn.dataTable.Api(settings);
                var node = api.row(dataIndex).node();
                var dateStr = $(node).find('td').eq(6).attr('data-date');
                if (!dateStr) return true;
                var date = new Date(dateStr);
                var minDate = min ? new Date(min) : null;
                var maxDate = max ? new Date(max) : null;
                if ((minDate === null || date >= minDate) && (maxDate === null || date <= maxDate)) {
                    return true;
                }
                return false;
            });

            $('#filter-start, #filter-end').on('change', function() {
                dt6.draw();
            });

            $('#filter-reset').on('click', function() {
                $('#filter-name').val('').trigger('change');
                $('#filter-start').val('');
                $('#filter-end').val('');
                dt6.draw();
            });

            // Event listener untuk input rupiah
            $(document).on('keyup', '.rupiah-input', function(e) {
                $(this).val(formatRupiah($(this).val(), 'Rp '));
            });

            // Validasi Frontend & Unmask Rupiah saat submit
            $('#formCreateSimpanan').on('submit', function(e) {
                let hasInput = false;
                let inputs = $(this).find('.rupiah-input');

                // Cek apakah ada input yang valid (> 0)
                inputs.each(function() {
                    let rawVal = $(this).val().replace(/[^0-9]/g, '');
                    if (rawVal && parseInt(rawVal) > 0) {
                        hasInput = true;
                        return false; // break loop
                    }
                });

                if (!hasInput) {
                    e.preventDefault();
                    alert('Harap isi minimal satu nominal kategori simpanan!');
                    return false;
                }

                // Jika validasi sukses, bersihkan format rupiah menjadi angka murni sebelum submit
                inputs.each(function() {
                    let rawVal = $(this).val().replace(/[^0-9]/g, '');
                    $(this).val(rawVal);
                });
            });

            // Unmask rupiah saat submit di form edit
            $('.formEditSimpanan').on('submit', function() {
                $(this).find('.rupiah-input').each(function() {
                    let rawVal = $(this).val().replace(/[^0-9]/g, '');
                    $(this).val(rawVal);
                });
            });

            $(document).on('shown.bs.modal', function(e) {
                var modal = $(e.target);
                var id = modal.attr('id') || '';
                if (id.indexOf('editModal') === 0) {
                    modal.find('.rupiah-input').each(function() {
                        var raw = $(this).val().replace(/[^0-9]/g, '');
                        if (raw) {
                            $(this).val(formatRupiah(raw, 'Rp '));
                        }
                    });
                }
            });
        });
    </script>
@endsection
