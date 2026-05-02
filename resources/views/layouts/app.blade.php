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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bs-body-font-family: 'Inter', sans-serif !important;
        }

        body,
        html,
        input,
        button,
        select,
        textarea {
            font-family: 'Inter', sans-serif !important;
        }

        .btn-soft-danger {
            background: rgba(239, 68, 68, 0.08) !important;
            color: #ef4444 !important;
            border: 1.5px solid rgba(239, 68, 68, 0.2) !important;
        }

        .btn-soft-danger:hover {
            background: #ef4444 !important;
            color: #fff !important;
            border-color: #ef4444 !important;
        }

        .btn-soft-primary {
            background: rgba(56, 88, 249, 0.08) !important;
            color: #3858f9 !important;
            border: 1.5px solid rgba(56, 88, 249, 0.2) !important;
        }

        .btn-soft-primary:hover {
            background: #3858f9 !important;
            color: #fff !important;
            border-color: #3858f9 !important;
        }

        .btn-soft-success {
            background: rgba(34, 197, 94, 0.08) !important;
            color: #22c55e !important;
            border: 1.5px solid rgba(34, 197, 94, 0.2) !important;
        }

        .btn-soft-success:hover {
            background: #22c55e !important;
            color: #fff !important;
            border-color: #22c55e !important;
        }

        .btn-soft-secondary {
            background: rgba(100, 116, 139, 0.08) !important;
            color: #64748b !important;
            border: 1.5px solid rgba(100, 116, 139, 0.2) !important;
        }

        .btn-soft-secondary:hover {
            background: #64748b !important;
            color: #fff !important;
            border-color: #64748b !important;
        }

        .dropdown-menu {
            z-index: 999999 !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15) !important;
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