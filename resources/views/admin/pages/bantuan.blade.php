@extends('layouts.app')

@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 1;">
                            <div class="card-header bg-transparent border-0 p-4 pb-0">
                                <h5 class="mb-0 text-primary fw-bold">
                                    <i class="bi bi-plus-circle me-2"></i>Catat Bantuan
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" action="/bantuan/create" id="formCreateBantuan">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Pilih Anggota</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" id="addUserSearch"
                                                placeholder="Cari nama anggota..." autocomplete="off">
                                            <input type="hidden" name="id_user" id="add_user_id_hidden" required>
                                            <div id="addUserDropdown" class="dropdown-menu w-100 mt-1"
                                                style="max-height: 220px; overflow-y: auto; display: none;">
                                                @foreach ($anggota as $u)
                                                    <button type="button" class="dropdown-item"
                                                        data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                                                        data-display="{{ $u->name }} - {{ $u->alamat ?? '-' }}">
                                                        {{ $u->name }} - {{ $u->alamat ?? '-' }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('id_user')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Nama Bantuan</label>
                                        <input type="text" class="form-control" name="nama_bantuan" list="bantuanList"
                                            placeholder="Contoh: Bantuan Pendidikan" required>
                                        <datalist id="bantuanList">
                                            @foreach ($bantuanNames as $name)
                                                <option value="{{ $name }}"></option>
                                            @endforeach
                                        </datalist>
                                        @error('nama_bantuan')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Jumlah</label>
                                        <input type="text" class="form-control rupiah-input" name="jumlah"
                                            placeholder="Rp. 0" required>
                                        @error('jumlah')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Tanggal</label>
                                        <input type="date" class="form-control" name="tanggal"
                                            value="{{ date('Y-m-d') }}" required>
                                        @error('tanggal')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Keterangan</label>
                                        <input type="text" class="form-control" name="keterangan" placeholder="Opsional">
                                        @error('keterangan')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary fw-bold">
                                            <i class="bi bi-save me-1"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 p-4 pb-0">
                                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <h4 class="mb-0 fw-bold">Rekap Bantuan</h4>
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="small text-muted mb-0">Filter Bantuan</label>
                                        <select id="filterRekapBantuan" class="form-select form-select-sm"
                                            style="min-width: 220px;">
                                            <option value="">Semua</option>
                                            @foreach ($bantuanNames as $name)
                                                <option value="{{ $name }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table id="table-6" class="display text-center table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="8%">No</th>
                                                <th>Nama Bantuan</th>
                                                <th>Anggota</th>
                                                <th width="18%">Total</th>
                                                <th width="12%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rekap as $key => $row)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $row->nama_bantuan }}</td>
                                                    <td>{{ $row->nama_anggota }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($row->total_jumlah, 0, ',', '.') }}</td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary btn-detail-bantuan"
                                                            data-bs-toggle="modal" data-bs-target="#detailBantuanModal"
                                                            data-user-id="{{ $row->id_user }}"
                                                            data-user-name="{{ $row->nama_anggota }}">
                                                            Detail
                                                        </button>
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
            </div>
        </div>
    </main>

    <div class="modal fade" id="detailBantuanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title text-white mb-0">Riwayat Bantuan</h5>
                        <div class="small">Anggota: <span id="detailBantuanUserName">-</span></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted mb-0">Filter Bantuan</label>
                            <select id="filterDetailBantuan" class="form-select form-select-sm"
                                style="min-width: 240px;">
                                <option value="">Semua</option>
                                @foreach ($bantuanNames as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Total</div>
                            <div class="fw-bold" id="detailBantuanTotal">Rp 0</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table-riwayat-user" class="display text-center table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="8%">No</th>
                                    <th>Nama Bantuan</th>
                                    <th width="18%">Jumlah</th>
                                    <th width="14%">Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function formatRupiah(angka, prefix) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
        }

        function setupUserDropdown(inputId, hiddenId, dropdownId) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            const drop = document.getElementById(dropdownId);
            if (!input || !hidden || !drop) return;

            const items = Array.from(drop.querySelectorAll('.dropdown-item'));

            function showDrop() {
                drop.style.display = 'block';
            }

            function hideDrop() {
                drop.style.display = 'none';
            }

            function filterList(q) {
                const query = (q || '').toLowerCase();
                let shown = 0;
                items.forEach(it => {
                    const text = (it.getAttribute('data-display') || it.textContent || '').toLowerCase();
                    const ok = text.includes(query);
                    it.style.display = ok ? '' : 'none';
                    if (ok) shown++;
                });
                if (shown === 0) hideDrop();
            }

            function clearSelection() {
                hidden.value = '';
            }

            function setSelection(id, display) {
                hidden.value = id;
                input.value = display || '';
            }

            input.addEventListener('focus', function() {
                showDrop();
                filterList(this.value);
            });
            input.addEventListener('click', function() {
                showDrop();
                filterList(this.value);
            });
            input.addEventListener('input', function() {
                clearSelection();
                showDrop();
                filterList(this.value);
            });
            items.forEach(it => {
                it.addEventListener('click', function() {
                    setSelection(this.getAttribute('data-id') || '', this.getAttribute('data-display') ||
                        this
                        .getAttribute('data-name') || '');
                    hideDrop();
                });
            });
            document.addEventListener('click', function(e) {
                if (!drop.contains(e.target) && e.target !== input) hideDrop();
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupUserDropdown('addUserSearch', 'add_user_id_hidden', 'addUserDropdown');

            document.querySelectorAll('.rupiah-input').forEach(input => {
                input.addEventListener('keyup', function() {
                    this.value = formatRupiah(this.value, 'Rp. ');
                });
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const currencyInputs = this.querySelectorAll('.rupiah-input');
                    currencyInputs.forEach(input => {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    });
                });
            });

            const filterRekap = document.getElementById('filterRekapBantuan');
            const filterDetail = document.getElementById('filterDetailBantuan');
            const bantuanRiwayat = @json($riwayatForJs ?? []);
            let currentDetailUserId = null;

            function escapeHtml(value) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                };
                return String(value ?? '').replace(/[&<>"']/g, m => map[m]);
            }

            function formatRupiahIDR(number) {
                const n = parseInt(String(number ?? '0').replace(/[^0-9]/g, ''), 10) || 0;
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
            }

            function renderDetailRows() {
                const tbody = document.querySelector('#table-riwayat-user tbody');
                if (!tbody) return;

                const selectedBantuan = (filterDetail && filterDetail.value) ? filterDetail.value : '';
                const rows = bantuanRiwayat.filter(r => {
                    if (!currentDetailUserId) return false;
                    const sameUser = String(r.id_user) === String(currentDetailUserId);
                    const sameBantuan = !selectedBantuan || String(r.nama_bantuan) === String(
                        selectedBantuan);
                    return sameUser && sameBantuan;
                });

                let total = 0;
                tbody.innerHTML = rows.map((r, idx) => {
                    const jumlah = parseInt(r.jumlah, 10) || 0;
                    total += jumlah;
                    return `
                        <tr>
                            <td>${idx + 1}</td>
                            <td>${escapeHtml(r.nama_bantuan)}</td>
                            <td class="text-end">${formatRupiahIDR(jumlah)}</td>
                            <td>${escapeHtml(r.tanggal)}</td>
                            <td>${escapeHtml(r.keterangan || '-')}</td>
                            <td>${escapeHtml(r.petugas_name || '-')}</td>
                        </tr>
                    `;
                }).join('');

                const totalEl = document.getElementById('detailBantuanTotal');
                if (totalEl) totalEl.textContent = formatRupiahIDR(total);

                if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
                    const selector = '#table-riwayat-user';
                    if (window.jQuery.fn.dataTable.isDataTable(selector)) {
                        window.jQuery(selector).DataTable().destroy();
                    }
                    window.jQuery(selector).DataTable();
                }
            }

            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
                const rekapTable = window.jQuery('#table-rekap-bantuan').DataTable();

                if (filterRekap) {
                    filterRekap.addEventListener('change', function() {
                        rekapTable.column(1).search(this.value || '').draw();
                    });
                }
            }

            const detailModal = document.getElementById('detailBantuanModal');
            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', function(e) {
                    const trigger = e.relatedTarget;
                    if (!trigger) return;
                    currentDetailUserId = trigger.getAttribute('data-user-id') || null;
                    const userName = trigger.getAttribute('data-user-name') || '-';
                    const nameEl = document.getElementById('detailBantuanUserName');
                    if (nameEl) nameEl.textContent = userName;
                    if (filterDetail) filterDetail.value = '';
                    renderDetailRows();
                });
            }

            if (filterDetail) {
                filterDetail.addEventListener('change', function() {
                    renderDetailRows();
                });
            }
        });
    </script>
@endsection
