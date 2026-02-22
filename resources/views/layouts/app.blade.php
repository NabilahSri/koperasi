<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="description" content="Kleon Admin Template">
    <meta name="author" content="">
    <link href="{{ $logoPath ?? '/assets/img/logo-icon.svg' }}" rel="shortcut icon" type="image/png">
    <link href="../assets/img/apple-touch-icon.html" rel="apple-touch-icon">
    <link href="../assets/img/apple-touch-icon-72x72.html" rel="apple-touch-icon" sizes="72x72">
    <link href="../assets/img/apple-touch-icon-114x114.html" rel="apple-touch-icon" sizes="114x114">
    <link href="../assets/img/apple-touch-icon-144x144.html" rel="apple-touch-icon" sizes="144x144">
    <title>{{ $nama ?? '' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="../assets/css/main.css" id="stylesheet">
    <link rel="stylesheet" href="../assets/font-icons/bootstrap-icons.css" id="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('css')
</head>

<body class="bg-light">
    <div id="preloader">
        <div class="preloader-inner">
            <div class="spinner"></div>
            <div class="logo"><img src="{{ $logoPath ?? '/assets/img/logo-icon.svg' }}" alt="img"></div>
        </div>
    </div>
    @include('components.navbar')
    @include('components.sidebar')
    @yield('content')
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../plugins/jquery_ui/jquery-ui.1.12.1.min.js"></script>
    <script src="../plugins/apexchart/apexcharts.min.js"></script>
    <script src="../plugins/apexchart/apexchart-inits/apexcharts-analytics-2.js"></script>
    <script src="../plugins/peity/jquery.peity.min.js"></script>
    <script src="../plugins/peity/piety-init.js"></script>
    <script src="../plugins/select2/js/select2.min.js"></script>
    <script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables/js/datatables.init.js"></script>
    <script src="../plugins/flatpickr/flatpickr.min.js"></script>
    <script src="../plugins/dropzone/dropzone.min.js"></script>
    <script src="../plugins/dropzone/dropzone_custom.js"></script>
    <script src="../plugins/tinymce/tinymce.min.js"></script>
    <script src="../plugins/prism/prism.js"></script>
    <script src="../plugins/jquery-repeater/jquery.repeater.js"></script>
    <script src="../plugins/sweetalert/sweetalert2.min.js"></script>
    <script src="../plugins/sweetalert/sweetalert2-init.js"></script>
    <script src="../plugins/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="../plugins/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="../assets/js/snippets.js"></script>
    <script src="../assets/js/theme.js"></script>
    @yield('script')
</body>

</html>
