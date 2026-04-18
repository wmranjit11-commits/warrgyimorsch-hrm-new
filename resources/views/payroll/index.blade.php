@extends('layouts.app')

@section('content')
    <div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <!-- Main Content Card -->
        <div class="px-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #334155;">Payroll History</h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#"
                                        class="text-decoration-none text-muted small">Home</a></li>
                                <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;"
                                    aria-current="page">History</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Right Aligned Search & Actions -->
                        <div class="input-group d-none d-md-flex" style="width: 250px;">
                            <span class="input-group-text bg-light border-0"><i
                                    class="feather-search text-muted"></i></span>
                            <input type="text" id="tableSearch" class="form-control bg-light border-0 shadow-none"
                                placeholder="Search..." onkeyup="applyFilters()">
                        </div>

                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary"
                            data-bs-toggle="collapse" data-bs-target="#filterSection" title="Filter Records">
                            <i class="feather-filter"></i>
                        </a>

                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-info text-info"
                            onclick="exportPayroll('pdf')" title="Download All (PDF)">
                            <i class="feather-download"></i>
                        </a>

                        <button type="button" class="avatar-text avatar-md bg-primary text-white border-0 shadow-sm"
                            data-bs-toggle="offcanvas" data-bs-target="#payrollCalculationOffcanvas"
                            title="New Calculation">
                            <i class="feather-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Collapsible Filter Section -->
                <div class="collapse" id="filterSection">
                    <div class="card-body border-bottom bg-light bg-opacity-10 p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-2">Employee</label>
                                <select id="employeeFilter"
                                    class="form-select border-0 bg-white py-2 px-3 shadow-sm fw-bold"
                                    style="border-radius: 8px; height: 40px;">
                                    <option value="">All Employees</option>
                                    @foreach(\App\Models\Employee::all() as $emp)
                                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-2">Month</label>
                                <input type="month" id="monthFilter"
                                    class="form-control border-0 bg-white py-2 px-3 shadow-sm fw-bold"
                                    value="{{ request('month') }}" style="border-radius: 8px; height: 40px;">
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button
                                    class="btn btn-primary flex-grow-1 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                    onclick="applyFilters()"
                                    style="background: #3858f9; border: none; height: 40px; border-radius: 8px;">
                                    <i class="feather-search"></i> APPLY
                                </button>
                                <button class="btn btn-light border px-3 shadow-none" onclick="resetFilters()"
                                    style="height: 40px; border-radius: 8px;">
                                    <i class="feather-refresh-cw" style="font-size: 14px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: #ffffff; border-bottom: 1px solid #f1f5f9;">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="width: 80px;">SR. NO.
                                </th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">EMPLOYEE NAME</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">MONTH</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center">PAYABLE DAYS</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">GROSS SALARY</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">DEDUCTIONS</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">NET SALARY</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="width: 140px;">
                                    STATUS</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-center"
                                    style="width: 150px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $index => $payroll)
                                <tr class="hover-row border-bottom">
                                    <td class="ps-4 py-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $payroll->employee->name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted small fw-bold">{{ $payroll->month }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ $payroll->payable_days }}</td>
                                    <td class="text-end fw-bold text-dark">₹{{ number_format($payroll->gross_salary, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">₹{{ number_format($payroll->deductions, 2) }}</td>
                                    <td class="text-end fw-bold text-primary" style="font-size: 15px;">
                                        ₹{{ number_format($payroll->net_salary, 2) }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = 'bg-soft-warning text-warning';
                                            if ($payroll->status == 'paid') $statusClass = 'bg-soft-success text-success';
                                            elseif ($payroll->status == 'rejected') $statusClass = 'bg-soft-danger text-danger';
                                        @endphp
                                        <div class="dropdown">
                                            <span class="badge {{ $statusClass }} dropdown-toggle cursor-pointer"
                                                data-bs-toggle="dropdown" 
                                                data-bs-boundary="viewport"
                                                aria-expanded="false"
                                                style="padding: 7px 14px; border-radius: 9px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; min-width: 105px; display: inline-block; text-align: center;">
                                                {{ $payroll->status }}
                                            </span>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" 
                                                style="border-radius: 16px; min-width: 160px; z-index: 9999999 !important; border: 1px solid rgba(0,0,0,0.05) !important; position: absolute !important;">
                                                <li><a class="dropdown-item fw-bold text-warning rounded-3 py-2 px-3 mb-1" href="javascript:void(0);" onclick="updateStatus({{ $payroll->id }}, 'pending')" style="font-size: 13px;">Pending</a></li>
                                                <li><a class="dropdown-item fw-bold text-success rounded-3 py-2 px-3 mb-1" href="javascript:void(0);" onclick="updateStatus({{ $payroll->id }}, 'paid')" style="font-size: 13px;">Paid</a></li>
                                                <li><a class="dropdown-item fw-bold text-danger rounded-3 py-2 px-3" href="javascript:void(0);" onclick="updateStatus({{ $payroll->id }}, 'rejected')" style="font-size: 13px;">Rejected</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="javascript:void(0);"
                                                class="avatar-text avatar-md bg-soft-primary text-primary"
                                                onclick="viewPayroll({{ $payroll->id }})" title="View">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-info text-info"
                                                onclick="downloadSlip({{ $payroll->id }}, 'pdf')" title="Download PDF">
                                                <i class="feather-download"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                                class="avatar-text avatar-md bg-soft-danger text-danger"
                                                onclick="deletePayroll({{ $payroll->id }})" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="bi bi-calculator text-muted" style="font-size: 3rem; opacity: 0.2;"></i>
                                            <p class="text-muted mt-3 fw-bold">No payroll records found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payrolls->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $payrolls->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Payroll Calculation Offcanvas -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="payrollCalculationOffcanvas" style="width: 650px !important; background: #f8fafc;">
        <div class="offcanvas-header bg-white border-bottom px-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-3">
                    <i class="feather-calculator"></i>
                </div>
                <div>
                    <h5 class="offcanvas-title fw-bold text-dark">Enterprise Payroll Engine</h5>
                    <p class="text-muted small mb-0">Generate and validate monthly employee payslips</p>
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('payroll._calculation_form')
        </div>
    </div>

    <!-- Statement Modal -->
    <div class="modal fade" id="payrollDetailModal" tabindex="-1" aria-labelledby="payrollModalLabel" aria-hidden="true"
        data-bs-backdrop="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: #fff; overflow: hidden;">
                <div class="modal-header border-0 px-4 py-3" style="background: #3858f9; color: #ffffff;">
                    <h5 class="modal-title fw-bold" id="payrollModalLabel"><i class="bi bi-file-earmark-text-fill me-2"></i>
                        Payroll Statement</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-white" id="payrollModalBody"
                    style="min-height: 400px; max-height: 80vh; overflow-y: auto;">
                    <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-secondary fw-bold px-4 shadow-none" data-bs-dismiss="modal"
                        style="border-radius: 8px;">CLOSE</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        function applyFilters() {
            const month = document.getElementById('monthFilter').value;
            const empId = document.getElementById('employeeFilter').value;
            window.location.href = `{{ route("payroll.index") }}?month=${month}&employee_id=${empId}`;
        }

        function resetFilters() {
            window.location.href = '{{ route("payroll.index") }}';
        }

        function viewPayroll(id) {
            const modalEl = document.getElementById('payrollDetailModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            document.getElementById('payrollModalBody').innerHTML = `
                    <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    </div>`;

            modal.show();

            fetch(`/payroll/${id}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('payrollModalBody').innerHTML = html;
                })
                .catch(err => {
                    document.getElementById('payrollModalBody').innerHTML = '<div class="p-5 text-center text-danger">Error loading payroll data.</div>';
                });
        }

        function downloadSlip(id, format = 'csv') {
            window.location.href = `{{ route('payroll.export') }}?id=${id}&format=${format}`;
        }

        function deletePayroll(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                fetch(`/payroll/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => res.json()).then(data => {
                    if (data.success) location.reload();
                });
            }
        }

        function updateStatus(id, newStatus) {
            fetch(`/payroll/${id}/status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    // Instantly update color
                    location.reload();
                }
            });
        }

        function exportPayroll(format = 'csv') {
            const month = document.getElementById('monthFilter').value;
            const empId = document.getElementById('employeeFilter').value;
            window.location.href = `{{ route('payroll.export') }}?month=${month}&employee_id=${empId}&format=${format}`;
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
            color: #94a3b8;
        }

        .hover-row:hover {
            background-color: #fbfcfe;
        }

        .action-btn-outline {
            background: transparent !important;
            border: none !important;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            color: #64748b;
        }

        .table-responsive {
            overflow: visible !important;
        }

        .dropdown-menu {
            z-index: 99999999 !important;
        }

        .action-btn-outline:hover {
            background: #f1f5f9 !important;
            color: #3858f9;
        }

        .form-select-sm {
            font-size: 11px;
            letter-spacing: 0.3px;
            padding-top: 5px;
            padding-bottom: 5px;
        }
    </style>
@endpush