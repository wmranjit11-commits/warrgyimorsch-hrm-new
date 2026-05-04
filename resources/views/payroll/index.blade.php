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
                        <!-- Share Salary Report -->
                        <a href="javascript:void(0)" id="shareReportBtn" class="btn btn-icon btn-light-brand text-white bg-primary" 
                            title="Share Salary Report">
                            <label>Share salary report</label>
                        </a>
                        
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

                        <!-- <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-info text-info"
                            onclick="exportPayroll('pdf')" title="Download All (PDF)">
                            <i class="feather-download"></i>
                        </a> -->
                        <div class="relative inline-block" id="exportWrapper">
                            <!-- Button -->
                            <a href="javascript:void(0);"
                            id="exportBtn"
                            class="avatar-text avatar-md bg-soft-primary text-primary d-flex align-items-center justify-content-center">
                                <i class="feather-download"></i>
                            </a>

                            <!-- Dropdown -->
                            <div id="exportMenu"
                                class="d-none position-absolute end-0 mt-2 bg-white border rounded shadow"
                                style="width: 140px; z-index: 9999;">

                                <button onclick="exportPayroll('pdf')"
                                    class="dropdown-item text-start">
                                    📄 PDF
                                </button>

                                <button onclick="exportPayroll('excel')"
                                    class="dropdown-item text-start">
                                    📊 Excel
                                </button>

                            </div>
                        </div>

                        <button type="button" class="avatar-text avatar-md bg-primary text-white border-0 shadow-sm"
                            data-bs-toggle="offcanvas" data-bs-target="#payrollCalculationOffcanvas"
                            title="New Calculation">
                            <i class="feather-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- salary report -->
                <div id="salaryFormSection" class="card mt-4 p-4" style="display: none;">
                    <h4>Salary Slip</h4>

                    <form action="{{ route('payroll.sendDateRange') }}" method="POST">
                        @csrf
                        <!-- Employee Dropdown -->
                        <div class="mb-3">
                            <label class="form-label">Select Employee</label>
                            <select class="form-control" name="employee_id">
                                <option value="">Select</option>
                                @foreach (\App\Models\Employee::all() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="mb-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control">
                        </div>

                        <!-- Date To -->
                        <div class="mb-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Generate</button>
                    </form>
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
                                            <a href="javascript:void(0);"
                                                class="avatar-text avatar-md bg-soft-secondary text-secondary comment-btn"
                                                data-id="{{ $payroll->id }}"
                                                data-remark="{{ $payroll->remarks ?? '' }}"
                                                data-role="{{ auth()->user()->role }}"
                                                title="Comment">
                                                <i class="feather-message-square"></i>
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
                    <div class="modal fade" id="commentModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content" style="border-radius: 12px;">
                                
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Comment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <textarea id="remarksField" class="form-control" rows="4" placeholder="Write comment..."></textarea>
                                    <input type="hidden" id="userRole">
                                    <input type="hidden" id="payrollId">
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button id="saveBtn" class="btn btn-primary" onclick="saveComment()">Save</button>
                                </div>

                            </div>
                        </div>
                    </div>
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

        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const remark = this.dataset.remark; // already JSON-safe string
                const role = this.dataset.role;

                openCommentModal(id, remark, role);
            });
        });

        function openCommentModal(id, remarks, role) {
            role = (role || '').toLowerCase();
            let modalEl = document.getElementById('commentModal');
            let modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Set hidden fields
            document.getElementById('payrollId').value = id;
            document.getElementById('userRole').value = role;

            let field = document.getElementById('remarksField');
            let title = modalEl.querySelector('.modal-title');
            let saveBtn = document.getElementById('saveBtn');

            field.value = remarks || '';
            
            if (role === 'employee') {
                // ✅ Employee: can edit
                field.removeAttribute('readonly');
                field.style.display = 'block';

                title.innerText = 'Add Comment';
            } else {
                // ✅ Admin/HR: view only
                field.setAttribute('readonly', true);
                field.style.display = 'block'; // keep visible
                title.innerText = 'Remarks';
                saveBtn.style.display = 'none';
            }
        }

        function saveComment() {
            let role = document.getElementById('userRole').value;
            if (role.toLowerCase() !== 'employee') return;

            let id = document.getElementById('payrollId').value;
            let remarks = document.getElementById('remarksField').value;

            let formData = new FormData();
            formData.append('remarks', remarks);

            fetch(`/payroll/${id}/remarks`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('commentModal')).hide();
                    location.reload();
                } else {
                    alert("Error: " + (data.error || "Unknown error"));
                }
            })
            .catch(err => console.error("Fetch Error:", err));
        }

        document.addEventListener('DOMContentLoaded', function () {

            const btn = document.getElementById('exportBtn');
            const menu = document.getElementById('exportMenu');

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('d-none');
            });

            menu.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            document.addEventListener('click', function () {
                menu.classList.add('d-none');
            });

        });

        document.getElementById("shareReportBtn").addEventListener("click", function () {
            let section = document.getElementById("salaryFormSection");

            if (section.style.display === "none") {
                section.style.display = "block";
            } else {
                section.style.display = "none";
            }
        });

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
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this payroll record!",
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
                    fetch(`/payroll/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            if (typeof Toast !== 'undefined') {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Payroll record deleted'
                                });
                            }
                            setTimeout(() => location.reload(), 1000);
                        }
                    });
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