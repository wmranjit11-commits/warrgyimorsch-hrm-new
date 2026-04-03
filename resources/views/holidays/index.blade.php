@extends('layouts.app')

@section('content')

    <div class="main-content">
        <div class="container-fluid">

            <div class="card shadow-sm custom-card">
                <div class="card-body">

                    <div class="row g-4">

                        <!-- LEFT FORM -->
                        <div class="col-lg-4 col-md-5 form-section">

                            <h6 class="fw-bold mb-4" style="color: #334155;"><i class="feather-calendar me-2"></i>New Holiday</h6>

                            <form id="holidayForm">
                                @csrf

                                <div class="mb-3">
                                    <input type="text" name="title" id="holidayTitle" class="form-control border-0 bg-light px-3 fw-bold shadow-sm" placeholder="Enter Holiday Name"
                                        style="height: 38px; font-size: 13px; border-radius: 8px;" required>
                                </div>

                                <div class="mb-3">
                                    <input type="date" name="date" id="holidayDate" class="form-control border-0 bg-light px-3 fw-bold shadow-sm" value="{{ date('Y-m-d') }}"
                                        style="height: 38px; font-size: 13px; border-radius: 8px; padding-top: 0; padding-bottom: 0; line-height: 1.5; cursor: pointer;"
                                        onclick="this.showPicker()" required>
                                </div>

                                <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm" style="height: 38px; font-size: 13px; border-radius: 8px; background: #3858f9; border: none;">SAVE HOLIDAY</button>
                            </form>

                        </div>

                        <!-- RIGHT TABLE -->
                        <div class="col-lg-8 col-md-7 table-section">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold mb-0" style="color: #334155;">Holiday List</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group d-none d-md-flex shadow-sm" style="width: 220px; border-radius: 8px; overflow: hidden; height: 38px;">
                                        <span class="input-group-text bg-light border-0"><i class="feather-search text-muted small"></i></span>
                                        <input type="text" id="search" class="form-control bg-light border-0 shadow-none ps-0 fw-bold" placeholder="Search..." style="height: 38px; font-size: 13px;">
                                    </div>
                                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-info text-info" onclick="location.reload()" title="Refresh">
                                        <i class="feather-refresh-cw"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0" id="holidayTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SR. NO.</th>
                                            <th>TITLE</th>
                                            <th>DATE</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($holidays as $key => $h)
                                            <tr>
                                                <td>{{ $holidays->firstItem() + $key }}</td>
                                                <td>{{ strtoupper($h->title) }}</td>
                                                <td>{{ $h->date }}</td>
                                                <td class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('holidays.edit', $h->id) }}"
                                                        class="avatar-text avatar-md bg-soft-success text-success" title="Edit">
                                                        <i class="feather-edit-3"></i>
                                                    </a>

                                                    <a href="javascript:void(0);" 
                                                        onclick="deleteHoliday({{ $h->id }})"
                                                        class="avatar-text avatar-md bg-soft-danger text-danger" title="Delete">
                                                        <i class="feather-trash-2"></i>
                                                    </a>
                                                </td>
                                                <style>
                                                    .avatar-md {
                                                        width: 32px !important;
                                                        height: 32px !important;
                                                        display: flex !important;
                                                        align-items: center !important;
                                                        justify-content: center !important;
                                                        border-radius: 8px !important;
                                                        font-size: 14px !important;
                                                        text-decoration: none !important;
                                                    }
                                                </style>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">No holidays found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    Showing {{ $holidays->firstItem() }} to {{ $holidays->lastItem() }}
                                </div>

                                <div>
                                    {{ $holidays->links('pagination::bootstrap-5') }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- SEARCH -->
    <script>
        document.getElementById('search').addEventListener('keyup', function () {
            let search = this.value;

            fetch(`{{ route('holidays.index') }}?search=` + search)
                .then(res => res.text())
                .then(data => {
                    let parser = new DOMParser();
                    let htmlDoc = parser.parseFromString(data, 'text/html');

                    let newTable = htmlDoc.querySelector('#holidayTable tbody').innerHTML;
                    document.querySelector('#holidayTable tbody').innerHTML = newTable;
                });
        });
    </script>

    <script>
        document.getElementById('show').addEventListener('change', function () {

            let show = this.value;
            let search = document.getElementById('search').value;

            window.location.href = `{{ route('holidays.index') }}?show=${show}&search=${search}`;
        });
    </script>

    <script>
        document.querySelectorAll('.uppercase').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });

        // AJAX Add Holiday
        document.getElementById('holidayForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const title = document.getElementById('holidayTitle').value;
            const date = document.getElementById('holidayDate').value;

            fetch('{{ route("holidays.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ title, date })
            })
            .then(res => {
                if (res.ok || res.redirected) {
                    showToast('Holiday added successfully!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('Error adding holiday', 'error');
                }
            })
            .catch(() => showToast('Something went wrong!', 'error'));
        });

        // AJAX Delete Holiday
        function deleteHoliday(id) {
            fetch('/holidays/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => {
                if (res.ok || res.redirected) {
                    showToast('Holiday deleted successfully!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('Error deleting holiday', 'error');
                }
            })
            .catch(() => showToast('Something went wrong!', 'error'));
        }

        // Toast function
        function showToast(message, type) {
            const toast = document.getElementById('customToast');
            const toastMsg = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');
            toastMsg.textContent = message;
            toast.className = 'custom-toast';
            if (type === 'success') {
                toast.classList.add('toast-success');
                toastIcon.innerHTML = '\u2713';
            } else {
                toast.classList.add('toast-error');
                toastIcon.innerHTML = '\u2717';
            }
            toast.classList.add('toast-show');
            setTimeout(() => { toast.classList.remove('toast-show'); }, 2000);
        }

        // Show session flash as toast
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session("success") }}', 'success');
            });
        @endif
    </script>

    <!-- Toast Notification -->
    <div id="customToast" class="custom-toast">
        <span id="toastIcon" class="toast-icon"></span>
        <span id="toastMessage"></span>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        .main-content {
            padding: 25px;
        }

        .custom-card {
            border-radius: 12px;
        }

        .form-section {
            border-right: 1px solid #eee;
            padding-right: 20px;
        }

        .table-section {
            padding-left: 20px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .uppercase {
            text-transform: uppercase;
        }

        @media(max-width:768px) {
            .form-section {
                border-right: none;
                border-bottom: 1px solid #eee;
            }

            .table-section {
                padding-left: 0;
            }
        }

        .custom-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 14px 24px;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            transform: translateX(120%);
            transition: transform 0.4s ease;
            font-family: 'Inter', sans-serif;
        }
        .custom-toast.toast-show { transform: translateX(0); }
        .custom-toast.toast-success { background: linear-gradient(135deg, #16a34a, #22c55e); }
        .custom-toast.toast-error { background: linear-gradient(135deg, #dc2626, #ef4444); }
        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
        }
    </style>

@endsection