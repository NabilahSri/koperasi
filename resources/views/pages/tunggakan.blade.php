@extends('component.template')

@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-danger text-white">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-2">Total Anggota Menunggak</h6>
                                        <h3 class="mb-0 fw-bold">{{ $total_nunggak }}</h3>
                                    </div>
                                    <div class="fs-1">
                                        <i class="bi bi-person-x-fill"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-warning text-dark">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-2">Nunggak Iuran Wajib</h6>
                                        <h3 class="mb-0 fw-bold">{{ $nunggak_iuran }}</h3>
                                    </div>
                                    <div class="fs-1">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-info text-white">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-2">Nunggak Tagihan</h6>
                                        <h3 class="mb-0 fw-bold">{{ $nunggak_tagihan }}</h3>
                                    </div>
                                    <div class="fs-1">
                                        <i class="bi bi-cash-coin"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Six -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 p-5 pb-0">
                        <div class="row">
                            <div class="col-md-6 col-12 text-muted">
                                <h4 class="mb-0">Daftar Anggota Bermasalah</h4>
                            </div>
                        </div>

                        <div class="card-body pt-2">
                            <div class="table-responsive">
                                <table id="table-6" class="display text-center table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Anggota</th>
                                            <th>Kontak</th>
                                            <th>Status Tunggakan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($anggota as $key => $item)
                                            <tr>
                                                <td class="align-middle">{{ $loop->iteration }}</td>
                                                <td class="align-middle text-start">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar-sm">
                                                            @if ($item->foto)
                                                                <img src="{{ asset('storage/' . $item->foto) }}"
                                                                    alt="Avatar" class="rounded-circle" width="40"
                                                                    height="40" style="object-fit: cover;">
                                                            @else
                                                                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white"
                                                                    style="width: 40px; height: 40px;">
                                                                    {{ substr($item->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-bold">{{ $item->name }}</h6>
                                                            <small class="text-muted">{{ $item->no_user }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-start">
                                                    <div><i class="bi bi-telephone me-2"></i>{{ $item->nohp }}</div>
                                                    <div class="small text-muted"><i
                                                            class="bi bi-envelope me-2"></i>{{ $item->email }}</div>
                                                </td>
                                                <td class="align-middle text-start">
                                                    <div class="d-flex flex-column gap-1">
                                                        @if (isset($item->tunggakan_iuran) && $item->tunggakan_iuran > 0)
                                                            <span class="badge bg-warning text-dark text-start p-2">
                                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Iuran
                                                                Wajib: {{ $item->tunggakan_iuran }} Bulan
                                                            </span>
                                                        @endif

                                                        @if (isset($item->tunggakan_tagihan) && $item->tunggakan_tagihan > 0)
                                                            <span class="badge bg-danger text-start p-2">
                                                                <i class="bi bi-x-circle-fill me-1"></i> Tagihan:
                                                                {{ $item->tunggakan_tagihan }} Bulan
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $phone = preg_replace('/^0/', '62', $item->nohp);
                                                        $message = "Halo {$item->name}, kami dari Koperasi. Kami ingin menginformasikan mengenai tunggakan Anda: ";
                                                        if ($item->tunggakan_iuran > 0) {
                                                            $message .= "Iuran Wajib ({$item->tunggakan_iuran} bulan) ";
                                                        }
                                                        if ($item->tunggakan_tagihan > 0) {
                                                            $message .= "Tagihan ({$item->tunggakan_tagihan} bulan)";
                                                        }
                                                        $message .= '. Mohon segera diselesaikan. Terima kasih.';
                                                    @endphp
                                                    <a href="https://wa.me/{{ $phone }}?text={{ urlencode($message) }}"
                                                        target="_blank"
                                                        class="btn btn-success btn-sm d-flex align-items-center gap-2 justify-content-center">
                                                        <i class="bi bi-whatsapp"></i> Hubungi
                                                    </a>
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
    </main>
@endsection
