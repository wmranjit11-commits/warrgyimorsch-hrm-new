<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">
</head>

<body>

    {{-- Sidebar / Navigation --}}
    @include('layouts.nav')

    {{-- Header --}}
    @include('layouts.header')

    {{-- Main Content --}}
    <main class="nxl-container">
        <div class="nxl-content">
            @yield('content')
        </div>

        {{-- Footer --}}
        @include('layouts.footer')
    </main>

    <!-- JS -->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>

    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>

    <script>
        function toggleMenu(element) {
            const parent = element.parentElement;
            const submenu = parent.querySelector(".nxl-submenu");

            if (submenu) {
                submenu.style.display =
                    submenu.style.display === "block" ? "none" : "block";
            }
        }
    </script>
    @yield('modals')
    @stack('modals')
    @stack('scripts')
</body>

</html>