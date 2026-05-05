@extends('layouts.app')

@section('content')

<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10" style="color: #3858f9; font-weight: 700;">Holiday Management</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Holiday List</li>
        </ul>
    </div>
</div>
<!-- [ page-header ] end -->

<!-- [ main-content ] start -->
<div class="main-content pt-4" style="margin-bottom: 100px;">
    <div class="row g-4">
        <!-- HOLIDAY FORM (LEFT - 4 Cols) -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                    <h6 class="fw-bold mb-0 text-uppercase" style="color: #64748b; font-size: 11px; letter-spacing: 0.5px;">New Holiday</h6>
                </div>
                <div class="card-body p-4">
                    <form id="holidayForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Holiday Title</label>
                            <input type="text" name="title" id="holidayTitle" class="form-control border-0 bg-light shadow-none" 
                                placeholder="Enter holiday title" required style="border-radius: 10px; height: 48px; font-size: 14px;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Holiday Date</label>
                            <input type="date" name="date" id="holidayDate" class="form-control border-0 bg-light shadow-none" 
                                value="{{ date('Y-m-d') }}" onclick="this.showPicker()" required 
                                style="border-radius: 10px; height: 48px; font-size: 14px;">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" 
                            style="background: #3858f9; border: none; height: 52px; border-radius: 10px; font-size: 14px; letter-spacing: 0.5px;">
                            SAVE HOLIDAY
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- HOLIDAY LIST (RIGHT - 8 Cols) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold mb-0 me-3" style="color: #334155; font-size: 16px;">Holiday List</h5>
                    </div>

                    <div class="input-group" style="width: 250px;">
                        <span class="input-group-text bg-light border-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" id="holidaySearch" class="form-control bg-light border-0 shadow-none fw-bold"
                            placeholder="Search..." onkeyup="filterHolidays()" style="height: 44px; font-size: 14px; border-radius: 0 10px 10px 0;">
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0" id="holidayTable">
                            <thead style="background: #3858f9; color: white;">
                                <tr style="height: 60px; vertical-align: middle;">
                                    <th class="ps-4" style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">Sr.No.</th>
                                    <th style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">Holiday Title</th>
                                    <th style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">Date</th>
                                    <th class="pe-4 text-center" style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">Action</th>
                                </tr>
                            </thead>
                            <tbody style="border-top: 1px solid #f1f5f9;">
                                @forelse($holidays as $index => $h)
                                <tr class="holiday-row" style="height: 70px;">
                                    <td class="ps-4 fw-bold" style="font-size: 14px;">{{ $index + 1 }}</td>
                                    <td class="fw-bold" style="color: #3858f9; font-size: 14px;">{{ strtoupper($h->title) }}</td>
                                    <td style="font-size: 14px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="feather-calendar text-muted small"></i>
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($h->date)->format('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('holidays.edit', $h->id) }}" class="avatar-text avatar-md bg-soft-info text-info rounded" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <button type="button" onclick="confirmDelete({{ $h->id }})" class="avatar-text avatar-md bg-soft-danger text-danger rounded border-0" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No holidays recorded.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-4 py-4 border-top bg-white d-flex justify-content-center" style="border-radius: 0 0 12px 12px;">
                        {{ $holidays->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-info { background: rgba(13, 202, 240, 0.08) !important; color: #0dcaf0; }
    .bg-soft-danger { background: rgba(239, 68, 68, 0.08) !important; color: #ef4444; }
    .form-control:focus, .form-select:focus { 
        border: 1.5px solid #3858f9 !important; 
        box-shadow: 0 0 0 0.2rem rgba(56, 88, 249, 0.1) !important; 
    }
    .table thead th { border: none !important; }
    .avatar-md { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
</style>

<script>
    function filterHolidays() {
        const input = document.getElementById('holidaySearch');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('.holiday-row');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

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
        .then(res => res.json())
        .then(data => {
            if (data.success || data.id) { // Accepting either success flag or created object
                Toast.fire({
                    icon: 'success',
                    title: 'Holiday added successfully!'
                });
                setTimeout(() => window.location.reload(), 1500);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: data.message || 'Error adding holiday'
                });
            }
        })
        .catch(() => Toast.fire({ icon: 'error', title: 'Something went wrong!' }));
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this holiday!",
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
                deleteHoliday(id);
            }
        });
    }

    function deleteHoliday(id) {
        fetch('/holidays/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => {
            if (res.ok || res.status === 200) {
                Toast.fire({
                    icon: 'success',
                    title: 'Holiday deleted successfully!'
                });
                setTimeout(() => window.location.reload(), 1500);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Error deleting holiday'
                });
            }
        })
        .catch(() => Toast.fire({ icon: 'error', title: 'Something went wrong!' }));
    }
</script>

@endsection