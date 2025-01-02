<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>
        @yield('title', config('app.name'))
    </title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ url('/') }}/assets/img/logo/favicon.ico" type="image/x-icon" />

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <script src="{{ url('/') }}/assets/js/plugin/webfont/webfont.min.js" data-navigate-track></script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/fonts.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/kaiadmin.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireStyles
</head>
<body>
    <div class="wrapper">
        <livewire:layout.admin-navigation />

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="white">
                        <a href="#" class="logo">
                            <img src="{{ url('/') }}/assets/img/logo/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <livewire:layout.admin-header />
                <!-- End Navbar -->
            </div>

            <div class="container">
                {{ $slot }}
            </div>

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright">
                        2024, made with <i class="fa fa-heart heart text-danger"></i> by
                        <a href="#">KOMINFO</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{ url('/') }}/assets/js/core/jquery-3.7.1.min.js" data-navigate-track></script>
    <script src="{{ url('/') }}/assets/js/core/popper.min.js" data-navigate-track></script>
    <script src="{{ url('/') }}/assets/js/core/bootstrap.min.js" data-navigate-track></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ url('/') }}/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js" data-navigate-track></script>

    <!-- Chart JS -->
    <script src="{{ url('/') }}/assets/js/plugin/chart.js/chart.min.js" data-navigate-track></script>

    <!-- jQuery Sparkline -->
    <script src="{{ url('/') }}/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js" data-navigate-track></script>

    <!-- Chart Circle -->
    <script src="{{ url('/') }}/assets/js/plugin/chart-circle/circles.min.js" data-navigate-track></script>

    <!-- Datatables -->
    <script src="{{ url('/') }}/assets/js/plugin/datatables/datatables.min.js" data-navigate-track></script>

    <!-- Bootstrap Notify -->
    <script src="{{ url('/') }}/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js" data-navigate-track></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ url('/') }}/assets/js/plugin/jsvectormap/jsvectormap.min.js" data-navigate-track></script>
    <script src="{{ url('/') }}/assets/js/plugin/jsvectormap/world.js" data-navigate-track></script>

    <!-- Sweet Alert -->
    <script src="{{ url('/') }}/assets/js/plugin/sweetalert/sweetalert.min.js" data-navigate-track></script>

    <!-- Kaiadmin JS -->
    <script src="{{ url('/') }}/assets/js/kaiadmin.min.js" data-navigate-track></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" data-navigate-track></script>




    @stack('script')
    @stack('scripts')
    @livewireScripts
    @livewireChartsScripts
</body>
</html>
