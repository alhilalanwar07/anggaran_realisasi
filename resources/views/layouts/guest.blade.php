<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ url('/') }}/assets/img/logo/favicon.ico" type="image/x-icon" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/kaiadmin.min.css" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .container {
            display: flex;
            width: 100vw;
            height: 100vh;
            padding: 0 !important;
        }

        .left-panel {
            flex: 2;
            background-color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            padding: 20px;
        }

        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
            background-color: #fff;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <img src="{{ asset('assets/img/logo/web-app-manifest-512x512.png') }}" alt="Logo" class="w-24 mb-5">
                <h1 class="text-2xl font-bold mb-2 text-center">Sistem Informasi Keuangan Pemerintahan Daerah</h1>
                <p class="text-center">Kabupaten Kolaka Timur</p>
            </div>
        </div>
        <div class="right-panel">
            {{ $slot }}
            <footer class="mt-8 text-sm text-gray-500">
                &copy; {{ date('Y') }}, SIUPD - Kabupaten Kolaka Timur.
            </footer>
        </div>
    </div>
    <!-- Core JS Files -->
    <script src="{{ url('/') }}/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="{{ url('/') }}/assets/js/core/popper.min.js"></script>
    <script src="{{ url('/') }}/assets/js/core/bootstrap.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ url('/') }}/assets/js/kaiadmin.min.js"></script>
</body>
</html>
