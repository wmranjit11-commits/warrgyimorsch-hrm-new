@extends('layouts.app')

@section('content')
<!-- Feather Icons CDN for redundancy -->
<script src="https://unpkg.com/feather-icons"></script>

<div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Top Header -->
    <div class="px-4 py-3 bg-white border-bottom shadow-sm mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="fw-bold mb-0" style="color: #334155;">Leave Management</h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted small">Home</a></li>
                            <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;" aria-current="page">Leave Allotment</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="d-flex align-items-center pe-2">
                <button class="btn btn-light-brand text-primary fw-bold small d-flex align-items-center px-4" 
                    style="border-radius: 10px; height: 42px; border: 1.5px solid #e2e8f0; background: #fff;"
                    data-bs-toggle="modal" data-bs-target="#leaveBalanceModal" onclick="fetchLeaveBalances()">
                    <i data-feather="list" style="width: 16px; height: 16px;" class="me-2 text-primary"></i> VIEW BALANCE LIST
                </button>
            </div>
        </div>
    </div>

    <div class="px-4">
        <div class="row g-4">
            <!-- Allotment Entry Panel -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 100%;">
                    <div class="card-header bg-white border-bottom p-4">
                        <h6 class="fw-bold mb-4 text-dark hstack gap-2">
                            <i data-feather="plus-circle" class="text-primary" style="width: 18px; height: 18px;"></i> Monthly Allotment
                        </h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted mb-1 text-uppercase">Select Allotment Month</label>
                                <select id="monthSelect" class="form-select border-0 bg-light fw-bold" style="border-radius: 10px; height: 48px; border: 1.5px solid #f1f5f9; padding-left: 15px;" onchange="updateView()">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }} {{ date('Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="employeeTable">
                            <thead class="bg-light sticky-top">
                                <tr class="small text-muted fw-bold text-uppercase">
                                    <th class="ps-4 border-0 py-3">Employee</th>
                                    <th class="text-center border-0 py-3" style="width: 120px;">Leave Count</th>
                                    <th class="text-center border-0 py-3" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $emp)
                                    <tr class="border-bottom">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-text avatar-xs bg-soft-primary text-primary small fw-bold">
                                                    {{ substr($emp->name, 0, 1) }}
                                                </div>
                                                <span class="fw-bold text-dark">{{ $emp->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <input type="number" step="0.5" class="form-control form-control-sm text-center fw-bold allotment-input border-0 bg-light shadow-none" 
                                                    data-employee-id="{{ $emp->id }}" 
                                                    value="{{ $allotments[$emp->id]->leave_count ?? 1.5 }}" 
                                                    style="border-radius: 8px; width: 80px; height: 38px;" readonly>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-icon btn-soft-danger d-inline-flex align-items-center justify-content-center mx-auto" onclick="removeRow(this)" style="border-radius: 8px; width: 34px; height: 34px; border: none;">
                                                <i data-feather="minus" style="width: 16px; height: 16px;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white border-top p-4 text-end sticky-bottom">
                        <button class="btn btn-primary px-5 py-2 fw-bold shadow-sm hstack gap-2 justify-content-center w-100" onclick="saveAllotments()" style="background: #3858f9; border: none; border-radius: 10px; height: 52px; font-size: 15px;">
                            <i data-feather="save" style="width: 18px; height: 18px;"></i> SAVE ALLOTMENTS
                        </button>
                    </div>
                </div>
            </div>

            <!-- History Panel -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 100%;">
                    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">Recent Allotment History</h6>
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-light border-0" style="border-radius: 10px 0 0 10px; border-right: 0;"><i data-feather="search" style="width: 14px; height: 14px;" class="text-muted"></i></span>
                            <input type="text" class="form-control form-control-sm border-0 bg-light px-2" placeholder="Search history..." id="historySearch" style="border-radius: 0 10px 10px 0; height: 40px; box-shadow: none;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="historyTable">
                                <thead class="bg-light">
                                    <tr class="small text-muted fw-bold text-uppercase">
                                        <th class="ps-4 py-3">Employee</th>
                                        <th class="text-center py-3">Allotted</th>
                                        <th class="text-center py-3">Month</th>
                                        <th class="text-center pe-4 py-3" style="width: 180px;">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $h)
                                        <tr class="border-bottom">
                                            <td class="ps-4 py-3">
                                                <span class="text-dark fw-semibold">{{ $h->employee->name }}</span>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="badge bg-soft-primary text-primary px-3 rounded-pill fw-bold" style="font-size: 11px;">{{ number_format($h->leave_count, 1) }}</span>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="badge bg-gray-100 text-dark border-0 px-2 py-1">{{ date('F', mktime(0, 0, 0, $h->month, 1)) }}</span>
                                            </td>
                                            <td class="text-end pe-4 small text-muted py-3">{{ $h->created_at->format('d M, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($history->hasPages())
                        <div class="card-footer bg-white border-top p-3 small">
                            {{ $history->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- LEAVE BALANCE POPUP MODAL -->
<div class="modal fade" id="leaveBalanceModal" tabindex="-1" aria-labelledby="leaveBalanceModalLabel" aria-hidden="true" style="z-index: 9999 !important;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border border-2 shadow-lg" style="border-radius: 12px; overflow: hidden; background: #ffffff !important; border-color: #cbd5e1 !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;">
            <div class="modal-header bg-white border-bottom p-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" id="leaveBalanceModalLabel" style="font-size: 20px;">Leave Balance Inventory</h5>
                    <p class="text-muted small mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Live status for all employees</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close" style="background-color: #f1f5f9; border-radius: 50%; padding: 10px;"></button>
            </div>
            <div class="modal-body p-4 bg-white" style="background-color: #ffffff !important;">
                <div class="input-group mb-4" style="border-radius: 10px; overflow: hidden; border: 2px solid #e2e8f0;">
                    <span class="input-group-text bg-white border-0 ps-3"><i data-feather="search" style="width: 16px; height: 16px;" class="text-primary"></i></span>
                    <input type="text" id="balanceSearch" class="form-control border-0 bg-white px-3 fw-bold text-dark" placeholder="Search by employee name..." style="height: 54px; box-shadow: none; font-size: 15px;">
                </div>

                <div class="table-responsive rounded-3 border" style="border-color: #e2e8f0 !important; background: #ffffff;">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8fafc;">
                            <tr class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1px;">
                                <th class="ps-4 py-3 border-0">Employee</th>
                                <th class="text-center py-3 border-0">Total Allotted</th>
                                <th class="text-center py-3 border-0">Used</th>
                                <th class="text-center pe-4 py-3 border-0">Available</th>
                            </tr>
                        </thead>
                        <tbody id="balanceTableBody" class="bg-white">
                            <!-- Populated via AJAX -->
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;"></div>
                                    <div class="mt-3 text-dark fw-bold">Fetching latest records...</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3">
                <button type="button" class="btn btn-dark fw-bold px-5 py-2 rounded-pill shadow-sm" data-bs-dismiss="modal" style="background: #0f172a; border: none;">DISMISS LIST</button>
            </div>
        </div>
    </div>
</div>
@endpush

<script>
    /* FORCE REMOVE ALL BLUR EFFECTS */
    const styleFix = document.createElement('style');
    styleFix.innerHTML = `
        .modal-open .nxl-container, 
        .modal-open .nxl-header, 
        .modal-open .nxl-navigation {
            filter: none !important;
            -webkit-filter: none !important;
        }
        .modal-backdrop {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
        }
        .modal-content {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
    `;
    document.head.appendChild(styleFix);

    function fetchLeaveBalances() {
        const tbody = document.getElementById('balanceTableBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm" role="status"></div><div class="mt-2 text-muted small">Loading records...</div></td></tr>';

        fetch('/api/leave/balance')
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                data.forEach(item => {
                    const balanceClass = item.balance < 0 ? 'text-danger' : 'text-primary';
                    const row = document.createElement('tr');
                    row.className = 'balance-row';
                    row.innerHTML = `
                        <td class="ps-3 py-3">
                            <div class="fw-bold text-dark small">${item.name}</div>
                        </td>
                        <td class="text-center small fw-bold">${item.total_allotted}</td>
                        <td class="text-center small fw-bold text-muted">${item.total_taken}</td>
                        <td class="text-center pe-3">
                            <span class="badge rounded-pill px-2 py-1 ${item.balance < 0 ? 'bg-soft-danger text-danger' : 'bg-soft-primary text-primary'} fw-bold" style="font-size: 10px;">
                                ${item.balance}
                            </span>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
    }

    // Search logic for Balance Offcanvas
    document.addEventListener('DOMContentLoaded', function() {
        const bSearch = document.getElementById('balanceSearch');
        if(bSearch) {
            bSearch.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('.balance-row');
                rows.forEach(row => {
                    const text = row.querySelector('div').innerText.toLowerCase();
                    row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
                });
            });
        }
    });

    function updateView() {
        const month = document.getElementById('monthSelect').value;
        window.location.href = "{{ route('leave.allotment') }}?month=" + month;
    }

    function removeRow(btn) {
        if(confirm('Remove this employee from this month\'s allotment?')) {
            btn.closest('tr').remove();
        }
    }

    function saveAllotments() {
        const month = document.getElementById('monthSelect').value;
        const inputs = document.querySelectorAll('.allotment-input');
        const allotments = {};
        
        inputs.forEach(input => {
            allotments[input.getAttribute('data-employee-id')] = input.value;
        });

        fetch('{{ route("leave.storeAllotment") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ month, allotments })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Allotments saved successfully');
                window.location.reload();
            } else {
                alert('Error saving allotments');
            }
        });
    }

    const hSearch = document.getElementById('historySearch');
    if(hSearch) {
        hSearch.addEventListener('keyup', function() {
            const filter = hSearch.value.toLowerCase();
            const rows = document.getElementById('historyTable').getElementsByTagName('tr');
            for(let i = 1; i < rows.length; i++) {
                const text = rows[i].getElementsByTagName('td')[0].innerText.toLowerCase();
                rows[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
            }
        });
    }

    // Initialize Feather Icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>

<style>
    .bg-soft-primary { background-color: rgba(56, 88, 249, 0.08); }
    .bg-soft-danger { background-color: rgba(239, 68, 68, 0.08); }
    .avatar-xs { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
    .btn-light-brand { background: #f8fafc; border: 1px solid #e2e8f0; }
    .btn-light-brand:hover { background: #f1f5f9; }
    .btn-soft-danger { color: #ef4444 !important; background: rgba(239, 68, 68, 0.08) !important; }
    .btn-soft-danger:hover { background: #ef4444 !important; color: #fff !important; }
    
    /* REMOVE ALL BLUR EFFECTS */
    .modal-backdrop.show { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; background-color: rgba(0, 0, 0, 0.5) !important; }
    .modal-content { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
</style>
@endsection
