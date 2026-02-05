@if (auth()->user()->role == 'admin')
    <!-- Admin -->
    <div class="kleon-vertical-nav">
        <!-- Logo  -->
        <div class="logo d-flex align-items-center">
            @php
                $lembaga = DB::table('lembaga')->where('id', 1)->first();
                $logoPath = $lembaga ? asset('storage/' . $lembaga->logo) : '/assets/img/logo-icon.svg';
            @endphp

            <img src="{{ $logoPath }}" alt="logo" class="justify-content-center ms-5 mt-2"
                style="width: 40%; height: 40%;">
            <button type="button" class="kleon-vertical-nav-toggle justify-content-end"><i
                    class="bi bi-list"></i></button>
        </div>

        <div class="kleon-navmenu">
            {{-- <h6 class="hidden-header text-gray text-uppercase ls-1 ms-4 mb-4">Main Menu</h6> --}}
            <ul class="main-menu">

                <!-- Dashboard -->
                <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}"><a
                        href="{{ url('dashboard') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-speedometer fs-18"></i></span> <span class="nav-text">Dashboard</span></a>
                </li>

                <li class="menu-section-title text-gray ff-heading fs-16 fw-bold text-uppercase mt-4 mb-2">
                    <span>PAGES</span>
                </li>

                <!-- Users -->
                <li class="menu-item menu-item-has-children {{ request()->is('users*') ? 'active' : '' }}"><a
                        href="javascript:void(0);"> <span class="nav-icon flex-shrink-0"><i
                                class="bi bi-people fs-18"></i></span> <span class="nav-text">Users</span></a>
                    <ul class="sub-menu">
                        <li class="menu-item {{ request()->is('users/admin') ? 'active' : '' }}"><a
                                href="{{ url('users/admin') }}"> Admin </a></li>
                        <li class="menu-item {{ request()->is('users/anggota') ? 'active' : '' }}"><a
                                href="{{ url('users/anggota') }}"> Anggota </a></li>
                    </ul>
                    <span class='submenu-opener'><i class='bi bi-chevron-right'></i></span>
                </li>
                <!-- Tunggakan -->
                <li class="menu-item {{ request()->is('tunggakan') ? 'active' : '' }}"><a
                        href="{{ url('tunggakan') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-exclamation-circle fs-18"></i></span> <span class="nav-text">Info Tunggakan</span></a></li>
                <!-- Jenis -->
                <li class="menu-item {{ request()->is('jenis') ? 'active' : '' }}"><a href="{{ url('jenis') }}"><span
                            class="nav-icon flex-shrink-0"><i class="bi bi-card-text fs-18"></i></span> <span
                            class="nav-text">Jenis</span></a></li>
                <!-- Kategori -->
                <li class="menu-item {{ request()->is('kategori') ? 'active' : '' }}"><a
                        href="{{ url('kategori') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-archive fs-18"></i></span> <span class="nav-text">Kategori</span></a></li>
                <!-- Simpanan -->
                <li class="menu-item {{ request()->is('simpanan') ? 'active' : '' }}"><a
                        href="{{ url('simpanan') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-wallet2 fs-18"></i></span> <span class="nav-text">Simpanan</span></a></li>
                <!-- Ambil Tabungan -->
                <li class="menu-item {{ request()->is('pengambilan*') ? 'active' : '' }}"><a
                        href="{{ url('pengambilan') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-cash-stack fs-18"></i></span> <span class="nav-text">Ambil
                            Tabungan</span></a></li>
                <!-- Tagihan -->
                <li class="menu-item menu-item-has-children {{ request()->is('tagihan*') ? 'active' : '' }}"><a
                        href="javascript:void(0);"> <span class="nav-icon flex-shrink-0"><i
                                class="bi bi-cash fs-18"></i></span> <span class="nav-text">Tagihan</span></a>
                    <ul class="sub-menu">
                        <li class="menu-item {{ request()->is('tagihan/pengajuan') ? 'active' : '' }}"><a
                                href="{{ url('tagihan/pengajuan') }}"> Pengajuan </a></li>
                        <li class="menu-item {{ request()->is('tagihan/bayar') ? 'active' : '' }}"><a
                                href="{{ url('tagihan/bayar') }}"> Bayar Tagihan </a></li>
                        <li class="menu-item {{ request()->is('histori/tagihan') ? 'active' : '' }}"><a
                                href="{{ url('histori/tagihan') }}"> Riwayat </a></li>
                    </ul>
                    <span class='submenu-opener'><i class='bi bi-chevron-right'></i></span>
                </li>
                <!-- Laporan -->
                <li class="menu-item {{ request()->is('laporan') ? 'active' : '' }}"><a
                        href="{{ url('laporan') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-receipt fs-18"></i></span> <span class="nav-text">Laporan</span></a></li>
                <!-- Activity Log -->
                <li class="menu-item {{ request()->is('activity/logs') ? 'active' : '' }}"><a
                        href="{{ url('activity/logs') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-journal-text fs-18"></i></span> <span class="nav-text">Activity
                            Log</span></a></li>
                <!-- Pengaturan -->
                <li class="menu-item {{ request()->is('pengaturan') ? 'active' : '' }}"><a
                        href="{{ url('pengaturan') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-gear fs-18"></i></span> <span class="nav-text">Pengaturan</span></a></li>




            </ul>
        </div>

        <div class="card border-0 rounded-0 mt-6">
            <div class="card-body p-0">
                @php
                    $lembaga = DB::table('lembaga')->where('id', 1)->first();
                    $nama = $lembaga ? $lembaga->nama : '';
                @endphp
                <h6 class="text-gray lh-20 mb-0">{{ $nama }}</h6>
                <h6 class="text-gray fs-14 fw-medium">Made with </i> by <a href="#"></a></h6>
            </div>
        </div>
    </div>
@else
    <!-- Anggota -->
    <div class="kleon-vertical-nav">
        <!-- Logo  -->
        <div class="logo d-flex align-items-center">
            @php
                $lembaga = DB::table('lembaga')->where('id', 1)->first();
                $logoPath = $lembaga ? asset('storage/' . $lembaga->logo) : '/assets/img/logo-icon.svg';
            @endphp

            <img src="{{ $logoPath }}" alt="logo" class="justify-content-center ms-5 mt-2"
                style="width: 40%; height: 40%;">
            <button type="button" class="kleon-vertical-nav-toggle justify-content-end"><i
                    class="bi bi-list"></i></button>
        </div>

        <div class="kleon-navmenu">
            {{-- <h6 class="hidden-header text-gray text-uppercase ls-1 ms-4 mb-4">Main Menu</h6> --}}
            <ul class="main-menu">

                <!-- Dashboard -->
                <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}"><a
                        href="{{ url('dashboard') }}"><span class="nav-icon flex-shrink-0"><i
                                class="bi bi-speedometer fs-18"></i></span> <span class="nav-text">Dashboard</span></a>
                </li>

                <li class="menu-section-title text-gray ff-heading fs-16 fw-bold text-uppercase mt-4 mb-2">
                    <span>PAGES</span>
                </li>

                <!-- Riwayat -->
                <li class="menu-item menu-item-has-children {{ request()->is('histori*') ? 'active' : '' }}"><a
                        href="javascript:void(0);"> <span class="nav-icon flex-shrink-0"><i
                                class="bi bi-receipt fs-18"></i></span> <span class="nav-text">History</span></a>
                    <ul class="sub-menu">
                        <li class="menu-item {{ request()->is('histori/simpanan') ? 'active' : '' }}"><a
                                href="{{ url('histori/simpanan') }}"> Simpanan </a></li>
                        <li class="menu-item {{ request()->is('histori/tagihan') ? 'active' : '' }}"><a
                                href="{{ url('histori/tagihan') }}"> Tagihan </a></li>
                        <li class="menu-item {{ request()->is('histori/pengambilan') ? 'active' : '' }}"><a
                                href="{{ url('histori/pengambilan') }}"> Pengambilan Tabungan </a></li>
                    </ul>
                    <span class='submenu-opener'><i class='bi bi-chevron-right'></i></span>
                </li>
            </ul>
        </div>

        <div class="card border-0 rounded-0 mt-6">
            <div class="card-body p-0">
                @php
                    $lembaga = DB::table('lembaga')->where('id', 1)->first();
                    $nama = $lembaga ? $lembaga->nama : '';
                @endphp
                <h6 class="text-gray lh-20 mb-0">{{ $nama }}</h6>
                <h6 class="text-gray fs-14 fw-medium">Made with </i> by <a href="#"></a></h6>
            </div>
        </div>
    </div>
@endif
