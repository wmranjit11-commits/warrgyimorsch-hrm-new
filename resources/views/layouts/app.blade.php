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

    <style>
        .btn-soft-danger {
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.15);
        }
        .btn-soft-danger:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        /* Hide scrollbar for the navigation sidebar */
        .nxl-navigation .navbar-content {
            scrollbar-width: none !important; /* Firefox */
            -ms-overflow-style: none !important;  /* IE and Edge */
        }
        
        .nxl-navigation .navbar-content::-webkit-scrollbar {
            display: none !important; /* Chrome, Safari and Opera */
        }

        /* Force hide Perfect Scrollbar rails in the sidebar */
        .nxl-navigation .ps__rail-y,
        .nxl-navigation .ps__rail-x {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
    </style>
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
    @if(Route::currentRouteName() != 'dashboard')
        <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
    @endif
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toast UI configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end', // Aap isko 'center', 'top', ya 'bottom-end' bhi kar sakte hain
            showConfirmButton: false,
            timer: 3000, // 3 seconds tak dikhega
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Success Alert
        @if(Session::has('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ Session::get('success') }}"
            });
        @endif

        // Error Alert
        @if(Session::has('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ Session::get('error') }}"
            });
        @endif

        // Warning Alert
        @if(Session::has('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ Session::get('warning') }}"
            });
        @endif

        // Info Alert
        @if(Session::has('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ Session::get('info') }}"
            });
        @endif
    </script>
    @yield('modals')
    @stack('modals')
    @stack('scripts')
</body>

</html>