@extends('component.template')
@if (auth()->user()->role == 'admin')
    @section('content')
        <main class="main-wrapper">
            <div class="container-fluid">
                <div class="inner-contents">
                    <div class="page-header d-flex align-items-center justify-content-between mr-bottom-30">
                        <div class="left-part">
                            <h2 class="text-dark">Dashboard</h2>
                            {{-- <p class="text-gray mb-0">Lorem ipsum dolor sit amet </p> --}}
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

                        {{-- <div class="col-xxl-6 col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0 p-5 pb-0">
                                    <h4 class="mb-0">Simpanan Masuk Hari Ini</h4>
                                </div>

                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table id="table-6" class="display text-center">
                                            <thead>
                                                <tr>
                                                    <th>Simpanan</th>
                                                    <th>Jumlah</th>
                                                    <th>Tanggal</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($simpanan_masuk as $smasuks)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex gap-2 flex-wrap">
                                                                <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                                    <img src="assets/img/clients/1.jpg" alt="img">
                                                                </div>
                                                                <div>
                                                                    <a href="invoice.html" class="fs-14 fw-semibold">#INV-0001234</a>
                                                                    <h6 class="fw-semibold mb-0">{{ $smasuks->user->name }}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <i class="bi bi-bookmark-check-fill text-gray"></i>
                                                                <div>
                                                                    <p class="fs-14 fw-semibold text-dark mb-0">Amount</p>
                                                                    <h5 class="fs-20 mb-0">$ 650,036.34</h5>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><p class="fs-14 text-gray mb-0">2m ago</p></td>
                                                        <td>
                                                            <div class="dropdown text-end">
                                                                <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </a>
                                                                <div class="dropdown-menu p-0">
                                                                    <a class="dropdown-item" href="invoice.html"><i class="bi bi-eye"></i></a>
                                                                    <a class="dropdown-item" href="assets/img/kleon-invoice.pdf" download><i class="bi bi-download"></i></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0 p-5 pb-0">
                                    <h4 class="mb-0"></h4>
                                </div>

                                <div class="card-body pt-2">
                                    <div class="table-responsive">
                                        <table id="table-5" class="display text-center">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                                <img src="assets/img/clients/1.jpg" alt="img">
                                                            </div>
                                                            <div>
                                                                <a href="invoice.html" class="fs-14 fw-semibold">#INV-0001234</a>
                                                                <h6 class="fw-semibold mb-0">Jean Graphic Inc.</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <i class="bi bi-bookmark-check-fill text-gray"></i>
                                                            <div>
                                                                <p class="fs-14 fw-semibold text-dark mb-0">Amount</p>
                                                                <h5 class="fs-20 mb-0">$ 650,036.34</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><p class="fs-14 text-gray mb-0">2m ago</p></td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu p-0">
                                                                <a class="dropdown-item" href="invoice.html"><i class="bi bi-eye"></i></a>
                                                                <a class="dropdown-item" href="assets/img/kleon-invoice.pdf" download><i class="bi bi-download"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                                <img src="assets/img/clients/2.jpg" alt="img">
                                                            </div>
                                                            <div>
                                                                <a href="invoice.html" class="fs-14 fw-semibold">#INV-0001235</a>
                                                                <h6 class="fw-semibold mb-0">Higspeed Studios</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <i class="bi bi-bookmark-check-fill text-gray"></i>
                                                            <div>
                                                                <p class="fs-14 fw-semibold text-dark mb-0">Amount</p>
                                                                <h5 class="fs-20 mb-0">$ 178.34</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><p class="fs-14 text-gray mb-0">12m ago</p></td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu p-0">
                                                                <a class="dropdown-item" href="invoice.html"><i class="bi bi-eye"></i></a>
                                                                <a class="dropdown-item" href="assets/img/kleon-invoice.pdf" download><i class="bi bi-download"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                                <img src="assets/img/clients/3.jpg" alt="img">
                                                            </div>
                                                            <div>
                                                                <a href="invoice.html" class="fs-14 fw-semibold">#INV-0001236</a>
                                                                <h6 class="fw-semibold mb-0">Fullspeedo Crew</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <i class="bi bi-bookmark-check-fill text-gray"></i>
                                                            <div>
                                                                <p class="fs-14 fw-semibold text-dark mb-0">Amount</p>
                                                                <h5 class="fs-20 mb-0">$ 326.34</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><p class="fs-14 text-gray mb-0">6h ago</p></td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu p-0">
                                                                <a class="dropdown-item" href="invoice.html"><i class="bi bi-eye"></i></a>
                                                                <a class="dropdown-item" href="assets/img/kleon-invoice.pdf" download><i class="bi bi-download"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                                <img src="assets/img/clients/4.jpg" alt="img">
                                                            </div>
                                                            <div>
                                                                <a href="invoice.html" class="fs-14 fw-semibold">#INV-0001237</a>
                                                                <h6 class="fw-semibold mb-0">Wedeloper Team</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <i class="bi bi-bookmark-check-fill text-gray"></i>
                                                            <div>
                                                                <p class="fs-14 fw-semibold text-dark mb-0">Amount</p>
                                                                <h5 class="fs-20 mb-0">$ 50,036.34</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><p class="fs-14 text-gray mb-0">18h ago</p></td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu p-0">
                                                                <a class="dropdown-item" href="invoice.html"><i class="bi bi-eye"></i></a>
                                                                <a class="dropdown-item" href="assets/img/kleon-invoice.pdf" download><i class="bi bi-download"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <div class="profilepicture flex-shrink-0 d-none d-xl-block">
                                                                <img src="assets/img/clients/5.jpg" alt="img">
                                                            </div>
                                                            <div>
                                                                <a href="invoice.html" class="fs-14 fw-semibold">#INV-0001238</a>
                                                                <h6 class="fw-semibold mb-0">Humbly Humble</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <i class="bi bi-bookmark-check-fill text-gray"></i>
                                                            <div>
                                                                <p class="fs-14 fw-semibold text-dark mb-0">Amount</p>
                                                                <h5 class="fs-20 mb-0">$ 23,936.34</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><p class="fs-14 text-gray mb-0">Yesterday, 07:03 AM</p></td>
                                                    <td>
                                                        <div class="dropdown text-end">
                                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu p-0">
                                                                <a class="dropdown-item" href="invoice.html"><i class="bi bi-eye"></i></a>
                                                                <a class="dropdown-item" href="assets/img/kleon-invoice.pdf" download><i class="bi bi-download"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    {{-- <div class="row">
                        <div class="col-xxl-8">
                            <div class="card border-0 card-graph p-5">
                                <div class="card-header card-graph--header  bg-transparent border-0 p-0 mb-5 d-flex align-items-center justify-content-between gap-3">

                                    <div>
                                        <h4 class="mb-0">Weekly Target</h4>
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="ms-auto d-flex align-items-center gap-3">
                                        <div class="dropdown">
                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu p-0">
                                                <a class="dropdown-item" href="#">View</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item text-danger" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center gap-4 mb-5 justify-content-between flex-wrap">
                                        <div>
                                            <div class="card-content mb-1">
                                                <h3 class="fs-38 mb-0">32,741</h3>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="icon text-success"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                                <p class="mb-0 text-gray"><span class="fw-bold text-success">+15%</span> than last month</p>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <div>
                                                <p class="mb-0 d-flex align-items-center"><span class="indicator bg-primary"></span> Completed</p>
                                                <h4>85,741 <span class="text-primary ff-base fs-14 fw-normal">50%</span></h4>
                                            </div>
                                            <div>
                                                <p class="mb-0 d-flex align-items-center"><span class="indicator bg-info"></span> Ongoing</p>
                                                <h4>23,733 <span class="text-info ff-base fs-14 fw-normal">25%</span></h4>
                                            </div>
                                            <div>
                                                <p class="mb-0 d-flex align-items-center"><span class="indicator bg-secondary"></span> Completed</p>
                                                <h4>15,468 <span class="text-secondary ff-base fs-14 fw-normal">5%</span></h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress-stacked">
                                        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
                                          <div class="progress-bar"><span class="ff-heading fs-18 fw-bold">50%</span></div>
                                        </div>
                                        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 25%">
                                          <div class="progress-bar bg-info"><span class="ff-heading fs-18 fw-bold">25%</span></div>
                                        </div>
                                        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 15%">
                                          <div class="progress-bar bg-secondary"><span class="ff-heading fs-18 fw-bold">15%</span></div>
                                        </div>
                                      </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-4">
                            <div class="card border-0 p-5">
                                <div class="card-header card-graph--header  bg-transparent border-0 p-0 mb-4 d-flex align-items-center justify-content-between gap-3">
                                    <h4 class="mb-0">Progress</h4>
                                    <div class="ms-auto d-flex align-items-center gap-3">
                                        <div class="dropdown">
                                            <a href="#" data-bs-toggle="dropdown" class="fs-24 text-gray">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu p-0">
                                                <a class="dropdown-item" href="#">View</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item text-danger" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div id="chart-19"></div>
                                    <div class="d-flex align-items-center justify-content-between gap-5">
                                        <div>
                                            <p class="mb-0"><span class="indicator bg-primary"></span> Completed</p>
                                            <h4>85,741 <span class="text-primary ff-base fs-14 fw-normal">65%</span></h4>
                                        </div>
                                        <div>
                                            <p class="mb-0"><span class="indicator bg-secondary"></span> Ongoing</p>
                                            <h4>23,733 <span class="text-secondary ff-base fs-14 fw-normal">35%</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </main>
    @endsection
@else
    @section('content')
        <main class="main-wrapper">
            <div class="container-fluid">
                <div class="inner-contents">
                    <div class="page-header d-flex align-items-center justify-content-between mr-bottom-30">
                        <div class="left-part">
                            <h2 class="text-dark">Dashboard</h2>
                            {{-- <p class="text-gray mb-0">Lorem ipsum dolor sit amet </p> --}}
                        </div>
                        <div id="realtime-clock">
                            <h3 id="clock" class="text-end"></h3>
                            <p id="date"></p>
                        </div>

                    </div>

                    {{-- <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-body py-0 d-flex align-items-center gap-2 gap-lg-5 flex-wrap">
                                <div>
                                    <div id="chart-34">
                                    </div>
                                </div>
                                <div>
                                    <h2 class="fs-38">{{ number_format($sisa_pinjaman) }}</h2>
                                    <h5 class="fw-semibold text-gray mb-0">Sisa Pinjaman</h5>
                                    <p class="text-gray mb-0">yang belum lunas </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-body py-0 d-flex align-items-center gap-2 gap-lg-5 flex-wrap">
                                <div>
                                    <div id="chart-35"></div>
                                </div>
                                <div>
                                    <h2 class="fs-38">{{ number_format($sisa_bagihasil) }}</h2>
                                    <h5 class="fw-semibold text-gray mb-0">Sisa Bagi Hasil</h5>
                                    <p class="text-gray mb-0">yang belum lunas </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                    <div class="row">
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card--stat bg-primary text-white">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-secondary text-white"><i
                                                class="bi bi-bookmarks-fill"></i></span>
                                        <h5 class="mb-0 text-white fw-semibold">Jumlah Pinjaman</h5>
                                    </div>
                                    <div class="card-content mt-4 mb-4">
                                        <h5 class="fs-38 mb-0 text-white">
                                            {{ number_format($pinjaman->nominal_pinjaman ?? 0) }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0"><span
                                                class="fw-bold">{{ number_format($sisa_pinjaman ?? 0) }}</span> belum
                                            dibayar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card--stat bg-info text-white">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-primary text-white"><i
                                                class="bi bi-bookmarks-fill"></i></span>
                                        <h5 class="mb-0 text-white fw-semibold">Jumlah Bagi Hasil</h5>
                                    </div>
                                    <div class="card-content mt-4 mb-4">
                                        <h3 class="fs-38 mb-0 text-white">
                                            {{ number_format($pinjaman->nominal_bagihasil ?? 0) }}</h3>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0"><span
                                                class="fw-bold">{{ number_format($sisa_bagihasil ?? 0) }}</span> belum
                                            dibayar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card--stat bg-primary text-white">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-secondary text-white"><i
                                                class="bi bi-bookmarks-fill"></i></span>
                                        <h5 class="mb-0 text-white fw-semibold">Iuran Pokok</h5>
                                    </div>
                                    <div class="card-content d-flex align-items-center justify-content-between gap-5">
                                        <h3 class="fs-38 mb-0 text-white">{{ number_format($total_iuran_pokok) }}</h3>
                                        <div id="chart-3"></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0"><span class="fw-bold">0</span> belum dibayar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="card border-0 card--stat bg-info text-white">
                                <div class="card-body">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <span class="d-inline-block bg-primary text-white"><i
                                                class="bi bi-bookmarks-fill"></i></span>
                                        <h5 class="mb-0 text-white fw-semibold">Iuran Wajib</h5>
                                    </div>
                                    <div class="card-content mt-4 mb-4">
                                        <h3 class="fs-38 mb-0 text-white">{{ number_format($total_iuran_wajib) }}</h3>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon"><i class="bi bi-arrow-up-circle-fill"></i></span>
                                        <p class="mb-0">Total Simpanan Wajib</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </main>
    @endsection
@endif
@section('script')
    <script src="plugins/apexchart/apexcharts.min.js"></script>
    <script src="plugins/apexchart/apexchart-inits/apexcharts-general.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script>
        // Chart untuk sisa pinjaman
        var ctxSisaPinjaman = document.getElementById('chartSisaPinjaman').getContext('2d');
        var chartSisaPinjaman = new Chart(ctxSisaPinjaman, {
            type: 'doughnut', // Mengubah tipe chart menjadi doughnut (pie chart dengan lubang di tengah)
            data: {
                labels: ['Sisa Pinjaman', 'Jumlah yang Sudah Dibayar'],
                datasets: [{
                    label: 'Sisa Pinjaman',
                    data: [{{ $sisa_pinjaman }}, {{ $jumlah_bayar_pinjaman }}], // Memasukkan data sisa pinjaman dan jumlah yang sudah dibayar
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                labels: false,
                cutout: '70%', // Menentukan persentase bagian dalam yang dipotong (membuat lubang)
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed && context.parsed.percentage) {
                                    label += context.parsed.percentage.toFixed(2) + '%';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Chart untuk sisa bagi hasil
        var ctxSisaBagiHasil = document.getElementById('chartSisaBagiHasil').getContext('2d');
        var chartSisaBagiHasil = new Chart(ctxSisaBagiHasil, {
            type: 'pie', // Mengubah tipe chart menjadi pie
            data: {
                labels: ['Sisa Bagi Hasil', 'Jumlah yang Sudah Dibayar'],
                datasets: [{
                    label: 'Sisa Bagi Hasil',
                    data: [{{ $sisa_bagihasil }}, {{ $jumlah_bayar_bagihasil }}], // Memasukkan data sisa bagi hasil dan jumlah yang sudah dibayar
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script> --}}
    <script>
        function updateClock() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            document.getElementById('clock').textContent = timeString;

            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('id-ID', options);
            document.getElementById('date').textContent = dateString;
        }

        // Memperbarui waktu awal saat halaman dimuat
        updateClock();

        // Memperbarui waktu setiap detik
        setInterval(updateClock, 1000);
    </script>
@endsection
