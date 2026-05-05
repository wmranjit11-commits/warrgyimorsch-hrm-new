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
            z-index: 9999999 !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
        }

        .table-responsive {
            overflow: visible !important;
            /* Allow dropdowns to pop out */
        }

        /* Global Premium Input Styling */
        .form-control, .form-select, .premium-input, .premium-select {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            height: 48px !important;
            padding: 10px 16px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            transition: all 0.2s ease !important;
            box-shadow: none !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff !important;
            border-color: #3858f9 !important;
            box-shadow: 0 0 0 4px rgba(56, 88, 249, 0.1) !important;
        }
        textarea.form-control { height: auto !important; }

        /* Global Select2 Premium Overrides */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            min-height: 48px !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
        }
        .select2-container--default .select2-selection--multiple {
            padding: 4px 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            font-weight: 500 !important;
            padding-left: 4px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            background-color: #fff !important;
            border-color: #3858f9 !important;
        }
        .select2-container .select2-selection--multiple .select2-selection__choice {
            background-color: #3858f9 !important;
            border: none !important;
            color: #fff !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            margin-top: 2px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }
        .select2-container .select2-selection--multiple .select2-selection__choice__remove {
            color: rgba(255, 255, 255, 0.8) !important;
            margin-right: 8px !important;
            border: none !important;
        }
        .select2-search__field::placeholder { color: #94a3b8 !important; }

        /* Premium Status & Priority UI */
        .premium-status-dropdown .btn-status,
        .priority-badge,
        .lead-select-btn {
            position: relative;
        }

        .premium-status-dropdown .btn-status.dropdown-toggle::after,
        .priority-badge.dropdown-toggle::after,
        .lead-select-btn.dropdown-toggle::after {
            display: none !important;
            /* Hide default Bootstrap caret */
        }

        .premium-status-dropdown .btn-status {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
            cursor: pointer;
            letter-spacing: 0.3px;
        }

        /* Status Colors */
        .status-pending {
            background: rgba(100, 116, 139, 0.1) !important;
            color: #64748b !important;
        }

        .status-in-process {
            background: rgba(56, 88, 249, 0.1) !important;
            color: #3858f9 !important;
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.1) !important;
            color: #22c55e !important;
        }

        .status-on-hold {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .status-review {
            background: rgba(6, 182, 212, 0.1) !important;
            color: #06b6d4 !important;
        }

        .status-rework {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        /* Priority UI */
        .priority-badge {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
            cursor: pointer;
            border: none;
        }

        .priority-badge.dropdown-toggle::after {
            display: none;
            /* We will use custom arrow if needed or none as per image */
        }

        .priority-hard {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }

        .priority-medium {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
        }

        .priority-low {
            background: rgba(34, 197, 94, 0.1) !important;
            color: #22c55e !important;
        }

        .priority-normal {
            background: rgba(56, 88, 249, 0.1) !important;
            color: #3858f9 !important;
        }

        /* Lead Select UI */
        .lead-select-btn {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #334155;
            cursor: pointer;
            width: 120px;
            /* Reduced fixed width */
            justify-content: space-between;
        }

        .lead-select-btn span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            max-width: 85px;
            /* Reduced max-width */
            text-align: left;
        }

        .lead-select-btn:hover {
            border-color: #3858f9;
            background: #f8fafc;
        }

        .premium-attachment-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            background: rgba(56, 88, 249, 0.08);
            color: #3858f9;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 6px;
            transition: all 0.3s;
            text-decoration: none !important;
            border: 1px solid rgba(56, 88, 249, 0.15);
        }

        .premium-attachment-link:hover {
            background: #3858f9;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(56, 88, 249, 0.25);
            transform: translateY(-1px);
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

        // Global Delete Confirmation
        function deleteData(event) {
            event.preventDefault();
            const form = event.currentTarget;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3858f9',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary px-4',
                    cancelButton: 'btn btn-light-brand px-4 me-3'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
    @yield('modals')
    @stack('modals')
    @stack('scripts')
</body>

</html>
