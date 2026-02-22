@extends('layouts.app')
@if (auth()->user()->role == 'admin')
    @section('content')
        <main class="main-wrapper">
            <div class="container-fluid">
                <div class="inner-contents">
                    <div class="page-header d-flex align-items-center justify-content-between mr-bottom-30">
                        <div class="left-part">
                            <h2 class="text-dark">Dashboard</h2>
                        </div>
                        <div id="realtime-clock">
                            <h3 id="clock" class="text-end"></h3>
                            <p id="date"></p>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card-graph">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-primary text-white"><i
                                                class="bi bi-people"></i></span>
                                        <h6 class="mb-0">Total Anggota</h6>
                                    </div>
                                    <div class="card-content d-flex align-items-center justify-content-between gap-5">
                                        <h3 class="fs-38 mb-0 mt-5">{{ $total_anggota }}</h3>
                                        <div id="chart-1"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-primary text-white">
                                    <div class="card-footer-info d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0"><span class="fw-bold"></span>total anggota yang terdaftar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card-graph">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-info text-white"><i class="bi bi-people"></i></span>
                                        <h6 class="mb-0">Total Admin</h6>
                                    </div>
                                    <div class="card-content d-flex align-items-center justify-content-between gap-5">
                                        <h3 class="fs-38 mb-0 mt-5">{{ $total_admin }}</h3>
                                        <div id="chart-2"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-info text-white">
                                    <div class="card-footer-info d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-down-circle-fill"></i></span>
                                        <p class="mb-0"><span class="fw-bold"></span>total admin yang terdaftar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card-graph">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-secondary text-white"><i
                                                class="bi bi-box-seam"></i></span>
                                        <h6 class="mb-0 mt-2">Total Tagihan</h6>
                                    </div>
                                    <div class="card-content d-flex align-items-center justify-content-between gap-5">
                                        <h3 class="fs-38 mb-0 mt-5">{{ number_format($total_semua_tagihan) }}</h3>
                                        <div id="chart-3"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-secondary text-white">
                                    <div class="card-footer-info d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0"><span class="fw-bold"></span> Total Tagihan anggota dan admin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card-graph">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-danger text-white"><i
                                                class="bi bi-box-seam"></i></span>
                                        <h6 class="mb-0 mt-2">Total Simpanan</h6>
                                    </div>
                                    <div class="card-content d-flex align-items-center justify-content-between gap-5">
                                        <h3 class="fs-38 mb-0 mt-5">{{ number_format($total_semua_simpanan) }}</h3>
                                        <div id="chart-3"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-danger text-white">
                                    <div class="card-footer-info d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0"><span class="fw-bold"></span> Total simpanan anggota dan admin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    @endsection
@endif
