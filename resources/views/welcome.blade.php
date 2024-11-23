<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPD - Sistem Informasi Pemerintahan Daerah</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ url('/') }}/assets/img/logo/favicon.ico" type="image/x-icon" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/fonts.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/kaiadmin.min.css" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset("assets/img/bg-1.jpg") }}');
            background-size: cover;
            background-position: center;
            min-height: 500px;
            color: white;
        }
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/img/logo/web-app-manifest-512x512.png') }}" height="40" alt="Logo">
                SIUPD
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero d-flex align-items-center">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Sistem Informasi Pemerintahan Daerah</h1>
            <p class="lead mb-4">Kabupaten Kolaka Timur</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Fitur SIPD</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                        <h4>Perencanaan</h4>
                        <p>Perencanaan pembangunan dan penganggaran daerah yang terintegrasi</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-money-bill-wave fa-3x mb-3 text-primary"></i>
                        <h4>Penganggaran</h4>
                        <p>Pengelolaan anggaran daerah yang efisien dan transparan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-tasks fa-3x mb-3 text-primary"></i>
                        <h4>Penatausahaan</h4>
                        <p>Penatausahaan keuangan daerah yang akuntabel</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">Copyright {{ date('Y') }}, SIUPD - Kabupaten Kolaka Timur.</p>
        </div>
    </footer>

    <!--   Core JS Files   -->
    <script src="{{ url('/') }}/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="{{ url('/') }}/assets/js/core/popper.min.js"></script>
    <script src="{{ url('/') }}/assets/js/core/bootstrap.min.js"></script>
    <script src="{{ url('/') }}/assets/js/plugin/webfont/webfont.min.js"></script>
    <script src="{{ url('/') }}/assets/js/kaiadmin.min.js"></script>
</body>
</html>
