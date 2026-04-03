@extends('layouts.app')

@section('content')
    <div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <!-- Main Content Card -->
        <div class="px-4 pt-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-body-tertiary border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light-brand text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;">
                            <i class="bi bi-arrow-left fs-5"></i>
                        </a>
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

                        <a href="{{ route('payroll.calculation') }}" class="avatar-text avatar-md bg-primary text-white"
                            title="New Calculation">
                            <i class="feather-plus"></i>
                        </a>
                    </div>
                </div>

                <!-- Collapsible Filter Section -->
                <div class="collapse" id="filterSection">
                    <div class="card-body border-bottom bg-light bg-opacity-10 p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Employee</label>
                                <select id="employeeFilter"
                                    class="form-select border-0 bg-white px-3 fw-bold shadow-sm"
                                    style="border-radius: 8px; height: 38px; font-size: 13px; padding-top: 0; padding-bottom: 0; line-height: 1.5;">
                                    <option value="">All Employees</option>
                                    @foreach(\App\Models\Employee::all() as $emp)
                                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Month</label>
                                <input type="month" id="monthFilter"
                                    class="form-control border-0 bg-white px-3 fw-bold shadow-sm"
                                    value="{{ request('month') }}" style="border-radius: 8px; height: 38px; font-size: 13px; padding-top: 0; padding-bottom: 0; line-height: 1.5;"
                                    onclick="this.showPicker()">
                            </div>
                            <div class="col-md-4 d-flex gap-2">
                                <button
                                    class="btn btn-primary flex-grow-1 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                    onclick="applyFilters()"
                                    style="background: #3858f9; border: none; height: 38px; border-radius: 8px; font-size: 13px;">
                                    <i class="feather-search"></i> APPLY
                                </button>
                                <button class="btn btn-light border px-3 shadow-none" onclick="resetFilters()"
                                    style="height: 38px; border-radius: 8px;">
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
                                    <td class="text-end fw-bold text-dark">Rs.{{ number_format($payroll->gross_salary, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-danger">Rs.{{ number_format($payroll->deductions, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-primary" style="font-size: 15px;">
                                        Rs.{{ number_format($payroll->net_salary, 2) }}</td>
                                    <td class="text-center">
                                        <select onchange="updateStatus({{ $payroll->id }}, this.value)"
                                            class="form-select form-select-sm border-0 fw-bold shadow-none {{ $payroll->status == 'paid' ? 'text-success' : 'text-warning' }}"
                                            style="background: #f8fafc; border-radius: 6px; cursor: pointer;">
                                            <option value="pending" {{ $payroll->status == 'pending' ? 'selected' : '' }}>PENDING
                                            </option>
                                            <option value="paid" {{ $payroll->status == 'paid' ? 'selected' : '' }}>PAID</option>
                                            <option value="rejected" {{ $payroll->status == 'rejected' ? 'selected' : '' }}>
                                                REJECTED</option>
                                        </select>
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
    <!-- Statement Modal -->
    <div class="modal fade" id="payrollDetailModal" tabindex="-1" aria-labelledby="payrollModalLabel" aria-hidden="true"
        data-bs-backdrop="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header border-0 px-4 py-3" style="background: #3858f9; color: #ffffff;">
                    <h5 class="modal-title fw-bold" id="payrollModalLabel"><i class="bi bi-file-earmark-text-fill me-2"></i>
                        Payroll Statement</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="payrollModalBody"
                    style="min-height: 400px; max-height: 80vh; overflow-y: auto;">
                    <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
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
            // Instant delete as requested
            fetch(`/payroll/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    // Show a quick notification if possible, then reload
                    location.reload();
                }
            });
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