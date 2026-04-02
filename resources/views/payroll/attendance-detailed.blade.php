@extends('layouts.app')

@section('content')
    <div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="px-4 pt-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #334155;">Detailed Monthly Attendance</h5>
                        <p class="text-muted small mb-0">Full punch records for {{ date('F Y', strtotime($month)) }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('payroll.attendance', ['month' => $month]) }}"
                            class="btn btn-outline-primary btn-sm fw-bold px-3" style="border-radius: 8px;">
                            <i class="feather-list me-1"></i> SUMMARY VIEW
                        </a>
                        <a href="{{ route('payroll.attendance.add') }}" class="btn btn-primary btn-sm fw-bold px-3"
                            style="border-radius: 8px; background: #3858f9; border: none;">
                            <i class="feather-plus me-1"></i> ADD ATTENDANCE
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body bg-light bg-opacity-10 border-bottom p-4">
                    <form action="{{ route('payroll.attendance.detailed') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Select Month</label>
                            <input type="month" name="month" class="form-control border-0 shadow-sm fw-bold"
                                value="{{ $month }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Search Employee</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-0"><i
                                        class="feather-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-0 fw-bold"
                                    placeholder="Name or ID..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block text-white">.</label>
                            <button type="submit" class="btn btn-dark w-100 fw-bold"
                                style="border-radius: 8px;">SEARCH</button>
                        </div>
                        <div class="col-md-2 text-end ms-auto">
                            <label class="form-label d-block text-white">.</label>
                            <a href="{{ route('payroll.attendance.detailed', ['month' => $month]) }}"
                                class="btn btn-light-brand w-100 fw-bold" style="border-radius: 8px;">RESET</a>
                        </div>
                    </form>
                </div>

                @if ($message = Session::get('success'))
                    <div class="mx-4 mt-4">
                        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center py-3"
                            style="border-radius: 12px; background: #ecfdf5; color: #065f46;">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div class="fw-bold">{{ $message }}</div>
                            <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="width: 120px;">DATE
                                </th>
                                <th class="py-3 text-muted small fw-bold text-uppercase" style="width: 80px;">ID</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">EMPLOYEE NAME</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">CHECK IN</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">CHECK OUT</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">HOURS</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">STATUS</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                                <tr class="border-bottom hover-row">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">{{ date('d-m-Y', strtotime($att->attendance_date)) }}
                                        </div>
                                        <div class="small text-muted">{{ date('D', strtotime($att->attendance_date)) }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border fw-bold">{{ $att->employee_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle fw-bold me-2"
                                                style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ strtoupper(substr($att->employee->name, 0, 1)) }}
                                            </div>
                                            <div class="fw-bold text-dark">{{ $att->employee->name }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-primary">
                                        {{ $att->check_in ? date('h:i A', strtotime($att->check_in)) : '--' }}</td>
                                    <td class="text-center fw-bold text-primary">
                                        {{ $att->check_out ? date('h:i A', strtotime($att->check_out)) : '--' }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill bg-soft-dark text-dark px-3">{{ $att->total_hours ?: '0.00' }}
                                            hrs</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge status-badge {{ $att->status == 'present' ? 'bg-soft-success' : ($att->status == 'absent' ? 'bg-soft-danger' : 'bg-soft-warning') }}">
                                            {{ strtoupper($att->status) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-icon btn-sm action-btn-outline" title="Edit (Coming Soon)">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                            <form action="{{ route('payroll.attendance.destroy', $att->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm text-danger action-btn-outline"
                                                    title="Delete">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="py-5">
                                            <i class="feather-slash fs-1 opacity-25 d-block mb-3"></i>
                                            No attendance records found for this period.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-0 p-4">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-row:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 6px 12px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .bg-soft-success {
            background: #ecfdf5;
            color: #059669;
        }

        .bg-soft-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .bg-soft-warning {
            background: #fffbeb;
            color: #d97706;
        }

        .bg-soft-dark {
            background: #f1f5f9;
            color: #334155;
        }

        .bg-soft-primary {
            background: #eef2ff;
            color: #3858f9;
        }

        .action-btn-outline {
            border: none;
            background: transparent;
            color: #64748b;
            transition: all 0.2s;
        }

        .action-btn-outline:hover {
            background: #f1f5f9;
            color: #3858f9;
        }
    </style>
@endsection