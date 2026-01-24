@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                {{-- Statistic Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3">
                                        <i class="bi bi-cash-stack fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-0">Total Pengambilan</h6>
                                </div>
                                <h3 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalPengambilan, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-check fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-0">Pengambilan Hari Ini</h6>
                                </div>
                                <h3 class="fw-bold mb-0 text-primary">Rp
                                    {{ number_format($pengambilanHariIni, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-month fs-4"></i>
                                    </div>
                                    <h6 class="text-muted mb-0">Pengambilan Bulan Ini</h6>
                                </div>
                                <h3 class="fw-bold mb-0 text-warning">Rp
                                    {{ number_format($pengambilanBulanIni, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent border-0 p-5 pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Riwayat Pengambilan Tabungan</h4>
                    </div>

                    <div class="card-body pt-2">
                        <div class="row g-3 align-items-end mb-4">
                            <div class="col-12 col-md-12 d-flex gap-2 justify-content-md-end">
                                <span class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                                    Ambil Tabungan
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="table-6" class="display text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Kategori</th>
                                        <th>Petugas</th>
                                        <th>Jumlah Ambil</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($withdrawals as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td><span
                                                    class="badge bg-light text-dark border">{{ $item->kategori->nama }}</span>
                                            </td>
                                            <td>{{ $item->petugas->name }}</td>
                                            <td class="text-danger fw-bold">Rp
                                                {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                            <td data-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td>{{ $item->keterangan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Tambah Pengambilan --}}
                <div class="modal modal-lg fade" id="tambahModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title text-white">
                                    <i class="bi bi-cash-coin me-2"></i>Ambil Tabungan
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('pengambilan.store') }}" id="formPengambilan">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                        <div>
                                            Pastikan saldo anggota mencukupi sebelum melakukan pengambilan.
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6 border-end">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pilih Anggota</label>
                                                <select name="id_user" id="id_user_ambil" class="form-select user-select"
                                                    required>
                                                    <option value="" selected disabled>Cari nama anggota...</option>
                                                    @foreach ($users as $data)
                                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Pilih Kategori</label>
                                                <select name="id_kategori" id="id_kategori_ambil" class="form-select"
                                                    required disabled>
                                                    <option value="" selected disabled>Pilih Kategori...</option>
                                                    @foreach (App\Models\Kategori::whereIn('nama', ['Manasuka', 'Lebaran', 'Simpanan Manasuka', 'Simpanan Lebaran'])->get() as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Saldo Tersedia</label>
                                                <div class="input-group">
                                                    <input type="text" id="saldo_saat_ini"
                                                        class="form-control bg-light text-dark fw-bold" readonly
                                                        value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tanggal Pengambilan</label>
                                                <input type="date" class="form-control" name="tanggal"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nominal Pengambilan</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control fw-bold rupiah-input"
                                                        name="jumlah" id="jumlah_ambil" placeholder="0" required>
                                                </div>
                                                <small class="text-danger mt-1 fw-bold" id="error-msg"
                                                    style="display:none;">
                                                    <i class="bi bi-x-circle me-1"></i>Saldo tidak mencukupi!
                                                </small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Keterangan</label>
                                                <textarea class="form-control" name="keterangan" rows="2" placeholder="Contoh: Keperluan mendesak..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger px-4">
                                        <i class="bi bi-save me-1"></i> Proses Pengambilan
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
        $(document).ready(function() {
            // Modal Logic
            $('.user-select').select2({
                dropdownParent: $('#tambahModal'),
                width: '100%'
            });

            $('#id_user_ambil').change(function() {
                if ($(this).val()) {
                    $('#id_kategori_ambil').prop('disabled', false);
                    checkSaldo();
                } else {
                    $('#id_kategori_ambil').prop('disabled', true);
                }
            });

            $('#id_kategori_ambil').change(function() {
                checkSaldo();
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

            var currentSaldo = 0;

            function checkSaldo() {
                var userId = $('#id_user_ambil').val();
                var catId = $('#id_kategori_ambil').val();

                if (userId && catId) {
                    $.get('/pengambilan/getSaldo/' + userId + '/' + catId, function(data) {
                        currentSaldo = parseInt(data.saldo) || 0;
                        $('#saldo_saat_ini').val(formatRupiah(currentSaldo.toString(), ''));

                        validateAmount();
                    });
                }
            }

            function validateAmount() {
                var val = parseInt($('#jumlah_ambil').val().replace(/[^0-9]/g, '')) || 0;
                if (val > currentSaldo && currentSaldo >= 0) { // Simple check
                    $('#error-msg').show();
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $('#error-msg').hide();
                    $('button[type="submit"]').prop('disabled', false);
                }
            }

            $('.rupiah-input').on('keyup', function() {
                $(this).val(formatRupiah($(this).val(), ''));
                validateAmount();
            });

            $('#formPengambilan').on('submit', function() {
                $('.rupiah-input').each(function() {
                    var raw = $(this).val().replace(/[^0-9]/g, '');
                    $(this).val(raw);
                });
            });

            // Reset modal on close
            $('#tambahModal').on('hidden.bs.modal', function() {
                $(this).find('form').trigger('reset');
                $('#id_user_ambil').val('').trigger('change');
                $('#id_kategori_ambil').prop('disabled', true);
                $('#saldo_saat_ini').val('0');
                $('#error-msg').hide();
                $('button[type="submit"]').prop('disabled', false);
            });
        });
    </script>
@endsection
