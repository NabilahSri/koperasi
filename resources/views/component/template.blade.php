<!DOCTYPE html>
<html lang="zxx">

    <!-- Mirrored from wpthemebooster.com/demo/themeforest/html/kleon/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Jan 2024 12:33:45 GMT -->

    <head>
        <!-- Meta Tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="description" content="Kleon Admin Template">
        <meta name="author" content="">

        <!-- Favicon and touch Icons -->
        @php
            $lembaga = DB::table('lembaga')->where('id', 1)->first();
            $logoPath = $lembaga ? asset('storage/' . $lembaga->logo) : '/assets/img/logo-icon.svg';
        @endphp
        <link href="{{ $logoPath }}" rel="shortcut icon" type="image/png">
        <link href="../assets/img/apple-touch-icon.html" rel="apple-touch-icon">
        <link href="../assets/img/apple-touch-icon-72x72.html" rel="apple-touch-icon" sizes="72x72">
        <link href="../assets/img/apple-touch-icon-114x114.html" rel="apple-touch-icon" sizes="114x114">
        <link href="../assets/img/apple-touch-icon-144x144.html" rel="apple-touch-icon" sizes="144x144">

        <!-- Page Title -->
        @php
            $lembaga = DB::table('lembaga')->where('id', 1)->first();
            $nama = $lembaga ? $lembaga->nama : '';
        @endphp
        <title>{{ $nama }}</title>

        <!-- Styles Include -->
        <link rel="stylesheet" href="../assets/css/main.css" id="stylesheet">

        <link rel="stylesheet" href="../assets/font-icons/bootstrap-icons.css" id="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
            integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>

    @yield('css')


    <body class="bg-light">

        <!-- Preloader -->
        <div id="preloader">
            <div class="preloader-inner">
                <div class="spinner"></div>
                @php
                    $lembaga = DB::table('lembaga')->where('id', 1)->first();
                    $logoPath = $lembaga ? asset('storage/' . $lembaga->logo) : '/assets/img/logo-icon.svg';
                @endphp
                <div class="logo"><img src="{{ $logoPath }}" alt="img"></div>
            </div>
        </div>

        {{-- Navbar --}}
        @include('component.navbar')

        {{-- Sidebar --}}
        @include('component.sidebar')

        <!-- Theme Customizer Panel -->
        {{-- <button
            class="aside_open btn btn-primary position-fixed z-index-9 rounded-circle p-0 m-0 d-flex align-items-center justify-content-center"
            type="button" data-bs-toggle="offcanvas" data-bs-target="#themeSwitcher"><i
                class="bi bi-gear-fill fs-20"></i></button>
        <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="themeSwitcher">
            <div class="offcanvas-header bg-light-200">
                <h5 class="offcanvas-title">Theme Customizer</h5>
                <button type="button"
                    class="aside_close btn btn-danger z-index-9 rounded-circle p-0 m-0 d-flex align-items-center justify-content-center"
                    data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="offcanvas-body bg-white p-0">
                <div class="bg-light-500 p-4 flex-grow-1">
                    <h6 class="mb-3 lh-16">Theme Color Mode</h6>
                    <div>
                        <div class="form-switch p-0">
                            <label class="form-label mb-0" for="colorSwitch4">Light</label>
                            <input type="checkbox" class="form-check-input border-0 m-0 mx-3" id="colorSwitch4">
                            <label class="form-label mb-0" for="colorSwitch4">Dark</label>
                        </div>
                    </div>
                </div>


                <div class="bg-light-200 p-4 flex-grow-1">
                    <h6 class="mb-3 lh-16">Navigation Layout</h6>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check form-check-inline with-layout-image m-0">
                                <input type="radio" class="form-check-input" id="verticalNav" name="checkLayout"
                                    value="vertical" checked>
                                <label class="form-label mb-0" for="verticalNav">
                                    <span class="d-inline-block mb-2">
                                        <img class="light-version img-fluid rounded-1"
                                            src="assets/img/customizer/vertical-light.png" alt="img">
                                        <img class="dark-version img-fluid rounded-1"
                                            src="assets/img/customizer/vertical-dark.png" alt="img">
                                    </span>
                                    <span class="label-text">Vertical</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-check-inline with-layout-image m-0">
                                <input type="radio" class="form-check-input" id="horizontalNav" name="checkLayout"
                                    value="horizontal">
                                <label class="form-label mb-0" for="horizontalNav">
                                    <span class="d-inline-block mb-2">
                                        <img class="light-version img-fluid rounded-1"
                                            src="assets/img/customizer/horizontal-light.png" alt="img">
                                        <img class="dark-version img-fluid rounded-1"
                                            src="assets/img/customizer/horizontal-dark.png" alt="img">
                                    </span>
                                    <span class="label-text">Horizontal</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-check-inline with-layout-image m-0">
                                <input type="radio" class="form-check-input" id="comboNav" name="checkLayout"
                                    value="combo">
                                <label class="form-label mb-0" for="comboNav">
                                    <span class="d-inline-block mb-2">
                                        <img class="light-version img-fluid rounded-1"
                                            src="assets/img/customizer/combo-light.png" alt="img">
                                        <img class="dark-version img-fluid rounded-1"
                                            src="assets/img/customizer/combo-dark.png" alt="img">
                                    </span>
                                    <span class="label-text">Combo</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="bg-light-500 p-4 flex-grow-1">
                    <h6 class="mb-3 lh-16">Vertical Navigation Styles</h6>
                    <div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="fullwidthNav" name="checkVerticalNav"
                                value="fullwidth" checked>
                            <label class="form-label mb-0" for="fullwidthNav">Fullwidth</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="collapseNav" name="checkVerticalNav"
                                value="collapse">
                            <label class="form-label mb-0" for="collapseNav">Collapse</label>
                        </div>
                    </div>
                </div>


                <div class="bg-light-200 p-4 flex-grow-1">
                    <h6 class="mb-3 lh-16">Header Position</h6>
                    <div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="scrollableHeader"
                                name="headerPosition" value="scrollable" checked>
                            <label class="form-label mb-0" for="scrollableHeader">Scrollable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="fixedHeader" name="headerPosition"
                                value="fixed">
                            <label class="form-label mb-0" for="fixedHeader">Fixed</label>
                        </div>
                    </div>
                </div>

                <div class="bg-light-500 p-4 flex-grow-1">
                    <h6 class="mb-3 lh-16">Topbar Style</h6>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check form-check-inline with-layout-image m-0">
                                <label class="form-label mb-0">
                                    <a href="index.html" target="_blank" rel="noopener noreferrer"
                                        class="fs-14 fw-semibold text-dark">
                                        <span class="d-inline-block mb-2">
                                            <img class="light-version img-fluid rounded-1"
                                                src="assets/img/customizer/vertical-light.png" alt="img">
                                            <img class="dark-version img-fluid rounded-1"
                                                src="assets/img/customizer/vertical-dark.png" alt="img">
                                        </span>
                                        <span class="label-text">Style One</span>
                                    </a>
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-check-inline with-layout-image m-0">
                                <label class="form-label mb-0">
                                    <a href="index-horizontal.html" target="_blank" rel="noopener noreferrer"
                                        class="fs-14 fw-semibold text-dark">
                                        <span class="d-inline-block mb-2">
                                            <img class="light-version img-fluid rounded-1"
                                                src="assets/img/customizer/horizontal-light.png" alt="img">
                                            <img class="dark-version img-fluid rounded-1"
                                                src="assets/img/customizer/horizontal-dark.png" alt="img">
                                        </span>
                                        <span class="label-text">Style Two</span>
                                    </a>
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-check-inline with-layout-image m-0">
                                <label class="form-label mb-0">
                                    <a href="index-combo.html" target="_blank" rel="noopener noreferrer"
                                        class="fs-14 fw-semibold text-dark">
                                        <span class="d-inline-block mb-2">
                                            <img class="light-version img-fluid rounded-1"
                                                src="assets/img/customizer/combo-light.png" alt="img">
                                            <img class="dark-version img-fluid rounded-1"
                                                src="assets/img/customizer/combo-dark.png" alt="img">
                                        </span>
                                        <span class="label-text">Style Three</span>
                                    </a>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Main Wrapper-->
        @yield('content')



        <!-- Core JS -->
        <script src="../assets/js/jquery-3.6.0.min.js"></script>
        <script src="../assets/js/bootstrap.bundle.min.js"></script>

        <!-- jQuery UI Kit -->
        <script src="../plugins/jquery_ui/jquery-ui.1.12.1.min.js"></script>

        <!-- ApexChart -->
        <script src="../plugins/apexchart/apexcharts.min.js"></script>
        <script src="../plugins/apexchart/apexchart-inits/apexcharts-analytics-2.js"></script>

        <!-- Peity  -->
        <script src="../plugins/peity/jquery.peity.min.js"></script>
        <script src="../plugins/peity/piety-init.js"></script>

        <!-- Select 2 -->
        <script src="../plugins/select2/js/select2.min.js"></script>

        <!-- Datatables -->
        <script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../plugins/datatables/js/datatables.init.js"></script>

        <!-- Date Picker -->
        <script src="../plugins/flatpickr/flatpickr.min.js"></script>

        <!-- Dropzone -->
        <script src="../plugins/dropzone/dropzone.min.js"></script>
        <script src="../plugins/dropzone/dropzone_custom.js"></script>

        <!-- TinyMCE -->
        <script src="../plugins/tinymce/tinymce.min.js"></script>
        <script src="../plugins/prism/prism.js"></script>
        <script src="../plugins/jquery-repeater/jquery.repeater.js"></script>

        <!-- Sweet Alert -->
        <script src="../plugins/sweetalert/sweetalert2.min.js"></script>
        <script src="../plugins/sweetalert/sweetalert2-init.js"></script>
        <script src="../plugins/nicescroll/jquery.nicescroll.min.js"></script>
        <script src="../plugins/nicescroll/jquery.nicescroll.min.js"></script>

        <!-- Snippets JS -->
        <script src="../assets/js/snippets.js"></script>

        <!-- Theme Custom JS -->
        <script src="../assets/js/theme.js"></script>

        @yield('script')

    </body>


    <!-- Mirrored from wpthemebooster.com/demo/themeforest/html/kleon/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Jan 2024 12:33:48 GMT -->

</html>
<link rel="stylesheet" href="../assets/css/main.css" id="stylesheet">
<link rel="stylesheet" href="../assets/css/main." id="stylesheet">
