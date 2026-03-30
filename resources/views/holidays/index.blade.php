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

                            <form method="POST" action="{{ route('holidays.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <input type="text" name="title" class="form-control" placeholder="Enter Holiday Name"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>

                                <button class="btn btn-primary btn-sm">SAVE</button>
                            </form>

                        </div>

                        <!-- RIGHT TABLE -->
                        <div class="col-lg-8 col-md-7 table-section">

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold mb-0" style="color: #334155;">Holiday List</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group d-none d-md-flex" style="width: 200px;">
                                        <span class="input-group-text bg-light border-0"><i class="feather-search text-muted small"></i></span>
                                        <input type="text" id="search" class="form-control bg-light border-0 shadow-none form-control-sm ps-0" placeholder="Search...">
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

                                                    <form id="delete-holiday-{{ $h->id }}" action="{{ route('holidays.destroy', $h->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="javascript:void(0);" 
                                                            onclick="if(confirm('Delete this holiday?')) document.getElementById('delete-holiday-{{ $h->id }}').submit();"
                                                            class="avatar-text avatar-md bg-soft-danger text-danger" title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </a>
                                                    </form>
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

    <!-- UPPERCASE SCRIPT -->
    <script>
        document.querySelectorAll('.uppercase').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>

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
    </style>

@endsection