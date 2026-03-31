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
                        <h5 class="fw-bold mb-0" style="color: #334155;">Leave History</h5>
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
                <div class="d-flex align-items-center pe-2 gap-2">
                    <a href="{{ route('leave.export', request()->all()) }}" class="btn btn-light-brand text-muted fw-bold small d-flex align-items-center px-4 shadow-sm"
                        style="border-radius: 10px; height: 42px; border: 1.5px solid #e2e8f0; background: #fff; text-decoration: none;">
                        <i data-feather="download" style="width: 16px; height: 16px;" class="me-2"></i> Export
                    </a>
                    <button class="btn btn-primary fw-bold small d-flex align-items-center px-4 shadow-sm"
                        data-bs-toggle="offcanvas" data-bs-target="#applyLeaveModal"
                        style="border-radius: 10px; height: 42px; background: #3858f9; border: none;">
                        <i data-feather="plus" style="width: 16px; height: 16px;" class="me-2"></i> Apply For Leave
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="px-4 mb-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                <form action="{{ route('leave.history') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase"
                            style="letter-spacing: 0.5px;">Search By Category</label>
                        <select name="category" class="form-select border-0 bg-light"
                            style="border-radius: 10px; height: 45px;">
                            <option value="">Select Category</option>
                            <option value="full" {{ request('category') == 'full' ? 'selected' : '' }}>Full Leave</option>
                            <option value="half" {{ request('category') == 'half' ? 'selected' : '' }}>Half Leave</option>
                            <option value="gatepass" {{ request('category') == 'gatepass' ? 'selected' : '' }}>Gatepass Leave
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase"
                            style="letter-spacing: 0.5px;">Search By Status</label>
                        <select name="status" class="form-select border-0 bg-light"
                            style="border-radius: 10px; height: 45px;">
                            <option value="">Select Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase"
                            style="letter-spacing: 0.5px;">From Date</label>
                        <input type="date" name="from_date" class="form-control border-0 bg-light"
                            value="{{ request('from_date') }}" style="border-radius: 10px; height: 45px;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase"
                            style="letter-spacing: 0.5px;">Upto Date</label>
                        <input type="date" name="to_date" class="form-control border-0 bg-light"
                            value="{{ request('to_date') }}" style="border-radius: 10px; height: 45px;">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary fw-bold flex-grow-1"
                            style="border-radius: 10px; height: 45px; background: #3858f9; border: none;">Search</button>
                        <a href="{{ route('leave.history') }}"
                            class="btn btn-soft-danger fw-bold d-flex align-items-center justify-content-center"
                            style="border-radius: 10px; height: 45px; width: 100px;">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="px-4 pb-5">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                    <th class="ps-4">Sr.No.</th>
                                    <th>Employee</th>
                                    <th class="text-center">Status</th>
                                    <th>Leave Type</th>
                                    <th>Category</th>
                                    <th>Duration</th>
                                    <th>Leave Reason</th>
                                    <th class="text-center pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $key => $leave)
                                    <tr class="border-bottom">
                                        <td class="ps-4 fw-semibold text-muted">{{ $leaves->firstItem() + $key }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 cursor-pointer"
                                                onclick="openViewModal({{ $leave->id }})">
                                                <div class="avatar-text avatar-xs bg-soft-primary text-primary small fw-bold">
                                                    {{ substr($leave->employee->name, 0, 1) }}
                                                </div>
                                                <span class="fw-bold text-dark">{{ $leave->employee->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-soft-dark text-dark',
                                                    'approved' => 'bg-soft-primary text-primary',
                                                    'on_hold' => 'bg-soft-warning text-warning',
                                                    'rejected' => 'bg-soft-danger text-danger'
                                                ][$leave->status] ?? 'bg-soft-secondary text-secondary';
                                            @endphp
                                            <span class="badge {{ $statusClass }} px-3 rounded-pill fw-bold text-capitalize"
                                                style="font-size: 11px; min-width: 80px;">
                                                {{ str_replace('_', ' ', $leave->status) }}
                                            </span>
                                        </td>
                                        <td><span class="fw-semibold text-dark">{{ $leave->leave_type }}</span></td>
                                        <td>
                                            <span class="badge bg-light text-dark fw-bold border"
                                                style="font-size: 10px;">{{ strtoupper($leave->leave_category) }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            <div class="fw-bold text-dark">{{ $leave->start_date->format('d-M-Y') }}</div>
                                            @if($leave->leave_category === 'gatepass' && $leave->start_time)
                                                <span class="badge bg-soft-info text-info p-1 px-2 mt-1" style="font-size: 9px;">
                                                    <i data-feather="clock" style="width: 10px; height: 10px;"></i>
                                                    {{ Carbon\Carbon::parse($leave->start_time)->format('h:i A') }} - {{ $leave->end_time ? Carbon\Carbon::parse($leave->end_time)->format('h:i A') : 'N/A' }}
                                                </span>
                                            @elseif($leave->end_date && $leave->end_date->gt($leave->start_date))
                                                <span style="font-size: 10px;">Upto {{ $leave->end_date->format('d-M-Y') }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-dark fw-semibold">{{ $leave->reason }}</td>
                                        <td class="text-center pe-4">
                                            <div class="hstack gap-2 justify-content-center">
                                                <button class="btn btn-icon btn-soft-info"
                                                    onclick="openViewModal({{ $leave->id }})" title="View Details">
                                                    <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <button class="btn btn-icon btn-soft-primary"
                                                    onclick="openActionModal({{ $leave->id }})" title="Take Action">
                                                    <i data-feather="edit-3" style="width: 14px; height: 14px;"></i>
                                                </button>
                                                <button class="btn btn-icon btn-soft-danger"
                                                    onclick="deleteApplication({{ $leave->id }})" title="Delete">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">No leave applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($leaves->hasPages())
                    <div class="card-footer bg-white border-top p-3 small d-flex justify-content-end">
                        {{ $leaves->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- APPLY LEAVE MODAL -->
    <div class="offcanvas offcanvas-end custom-side-modal" tabindex="-1" id="applyLeaveModal" style="width: 650px;">
        <div class="offcanvas-header bg-white border-bottom p-4">
            <h5 class="offcanvas-title fw-bold" id="applyLeaveModalLabel">Apply For Leave</h5>
            <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 bg-light bg-opacity-10">
            <form id="applyLeaveForm">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Employee <span
                                class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select border-0 bg-white shadow-sm"
                            style="height: 50px; border-radius: 10px;" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Today's Date</label>
                        <input type="text" class="form-control border-0 bg-light shadow-sm" value="{{ date('d-m-Y') }}"
                            style="height: 50px; border-radius: 10px;" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Leave Category <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-3 bg-white p-2 rounded-3 shadow-sm border" style="height: 50px;">
                            <div class="form-check d-flex align-items-center mb-0 ps-4">
                                <input class="form-check-input" type="radio" name="leave_category" value="full" id="catFull" checked onchange="toggleCategoryFields()">
                                <label class="form-check-label small fw-bold ms-1" for="catFull">Full</label>
                            </div>
                            <div class="form-check d-flex align-items-center mb-0 ps-2">
                                <input class="form-check-input" type="radio" name="leave_category" value="half" id="catHalf" onchange="toggleCategoryFields()">
                                <label class="form-check-label small fw-bold ms-1" for="catHalf">Half</label>
                            </div>
                            <div class="form-check d-flex align-items-center mb-0 ps-2">
                                <input class="form-check-input" type="radio" name="leave_category" value="gatepass" id="catGate" onchange="toggleCategoryFields()">
                                <label class="form-check-label small fw-bold ms-1" for="catGate">Gatepass</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Leave Type <span
                                class="text-danger">*</span></label>
                        <select name="leave_type" id="leaveType" class="form-select border-0 bg-white shadow-sm"
                            style="height: 50px; border-radius: 10px;" required>
                            <option value="">Select Type...</option>
                            <option value="Paid Leave">Paid Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Religious Leave">Religious Leave</option>
                            <option value="Gatepass Leave">Gatepass Leave</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Start Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="startDate" class="form-control border-0 bg-white shadow-sm"
                            style="height: 50px; border-radius: 10px;" required onchange="calculateDays()">
                    </div>
                    
                    <div class="col-md-4" id="endDateWrapper">
                        <label class="form-label small fw-bold text-muted text-uppercase">End Date</label>
                        <input type="date" name="end_date" id="endDate" class="form-control border-0 bg-white shadow-sm"
                            style="height: 50px; border-radius: 10px;" onchange="calculateDays()">
                    </div>

                    <div class="col-md-4" id="startTimeWrapper" style="display: none;">
                        <label class="form-label small fw-bold text-muted text-uppercase">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="startTime" class="form-control border-0 bg-white shadow-sm"
                            style="height: 50px; border-radius: 10px;" onchange="calculateDays()">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Total Days/Duration</label>
                        <input type="text" name="total_days" id="totalDays" class="form-control border-0 bg-light shadow-sm fw-bold text-primary"
                            value="1" style="height: 50px; border-radius: 10px;" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Reason <span
                                class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control border-0 bg-white shadow-sm"
                            placeholder="Enter short reason" style="height: 50px; border-radius: 10px;" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Message (Optional)</label>
                        <textarea name="message" class="form-control border-0 bg-white shadow-sm"
                            placeholder="Enter detailed message" style="height: 100px; border-radius: 10px;"></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm"
                            style="height: 55px; border-radius: 12px; background: #16a34a; border: none;">Apply Now</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- LEAVE ACTION MODAL -->
    <div class="offcanvas offcanvas-end custom-side-modal" tabindex="-1" id="leaveActionModal" style="width: 600px;">
        <div class="offcanvas-header bg-white border-bottom p-4">
            <h5 class="offcanvas-title fw-bold" id="leaveActionModalLabel">Leave Action Page</h5>
            <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4 bg-light bg-opacity-10">
            <div id="actionModalContent">
                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-soft-primary p-2 rounded-3 me-3">
                        <i data-feather="file-text" class="text-primary"></i>
                    </div>
                    <div>
                        <span class="small fw-bold text-muted text-uppercase d-block">Application ID</span>
                        <span id="displayAppCode" class="fw-bold text-dark fs-5">-</span>
                    </div>
                </div>

                <form id="actionForm">
                    @csrf
                    <input type="hidden" name="leave_id" id="actionLeaveId">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Set Status</label>
                        <select name="status" class="form-select border-0 bg-white shadow-sm"
                            style="height: 55px; border-radius: 12px; font-weight: 600;">
                            <option value="pending">⏳ Pending</option>
                            <option value="approved">✅ Approved</option>
                            <option value="on_hold">⏸️ On Hold</option>
                            <option value="rejected">❌ Rejected</option>
                        </select>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm"
                            style="height: 55px; border-radius: 12px; background: #16a34a; border: none;">Update Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RESTORED TABBED VIEW PORTFOLIO MODAL -->
    <div class="offcanvas offcanvas-end custom-side-modal" tabindex="-1" id="viewLeaveModal" style="width: 800px;">
        <div class="offcanvas-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center w-100">
                <div class="avatar-text bg-soft-primary text-primary fw-bold me-3 shadow-sm" style="width: 50px; height: 50px; font-size: 20px;">
                    <span id="viewAvatarLetter">E</span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-0 text-dark" id="viewEmployeeName">Employee Name</h5>
                    <span class="text-muted small fw-bold text-uppercase d-block mt-1" style="letter-spacing: 0.5px;">Employee Portfolio</span>
                </div>
                <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        </div>
        <div class="offcanvas-body p-0 bg-light bg-opacity-25">
            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom px-4 bg-white border-bottom shadow-sm" id="viewTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-uppercase py-3" id="details-tab" data-bs-toggle="tab" data-bs-target="#detailsContent" type="button" role="tab" style="font-size: 11px; letter-spacing: 0.5px;">Current Application</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-uppercase py-3 text-primary" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyContent" type="button" role="tab" style="font-size: 11px; letter-spacing: 0.5px;">Full History & Future</button>
                </li>
            </ul>

            <div class="tab-content" id="viewTabsContent">
                <!-- Details Tab -->
                <div class="tab-pane fade show active p-4" id="detailsContent" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-white p-3 rounded-4 shadow-sm border h-100">
                                <label class="small fw-bold text-muted text-uppercase d-block mb-1">Leave Info</label>
                                <div class="fw-bold text-dark" id="viewLeaveType">-</div>
                                <span id="viewCategoryBadge" class="badge bg-soft-primary text-primary border border-primary border-opacity-10 mt-1">CAT</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-3 rounded-4 shadow-sm border h-100">
                                <label class="small fw-bold text-muted text-uppercase d-block mb-1">Duration & Days</label>
                                <div id="viewTotalDays" class="fw-bold text-primary fs-5">-</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-3 rounded-4 shadow-sm border">
                                <label class="small fw-bold text-muted text-uppercase d-block mb-1">Timeframe</label>
                                <div class="row align-items-center">
                                    <div class="col-5 border-end">
                                        <div class="small text-muted">From Date</div>
                                        <div id="viewStartDateText" class="fw-bold text-dark">-</div>
                                        <div id="viewStartTimeText" class="small text-primary fw-semibold">N/A</div>
                                    </div>
                                    <div class="col-2 text-center text-muted">
                                        <i data-feather="arrow-right" style="width: 16px;"></i>
                                    </div>
                                    <div class="col-5">
                                        <div class="small text-muted">To / End</div>
                                        <div id="viewEndDateText" class="fw-bold text-dark">-</div>
                                        <div id="viewEndTimeText" class="small text-primary fw-semibold">N/A</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-3 rounded-4 shadow-sm border">
                                <label class="small fw-bold text-muted text-uppercase d-block mb-1">Reason</label>
                                <div id="viewReason" class="fw-bold text-dark fs-6">-</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-3 rounded-4 shadow-sm border" style="min-height: 150px;">
                                <label class="small fw-bold text-muted text-uppercase d-block mb-1">Employee Message</label>
                                <div id="viewMessage" class="small text-dark mt-2" style="white-space: pre-line; line-height: 1.6;">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade p-4" id="historyContent" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Attendance Record</h6>
                        <span id="historyCountBadge" class="badge bg-soft-primary text-primary rounded-pill px-3 fw-bold border border-primary border-opacity-10">0 Total</span>
                    </div>
                    <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">
                                        <th class="ps-3 py-3">Date Range</th>
                                        <th>Details</th>
                                        <th class="text-center">Status</th>
                                        <th class="pe-3 text-end">Days</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCategoryFields() {
            const category = document.querySelector('input[name="leave_category"]:checked').value;
            const endDateWrapper = document.getElementById('endDateWrapper');
            const startTimeWrapper = document.getElementById('startTimeWrapper');
            const leaveType = document.getElementById('leaveType');

            if (category === 'gatepass') {
                endDateWrapper.style.display = 'none';
                startTimeWrapper.style.display = 'block';
                leaveType.value = 'Gatepass Leave';
                document.getElementById('endDate').required = false;
                document.getElementById('startTime').required = true;
            } else {
                endDateWrapper.style.display = 'block';
                startTimeWrapper.style.display = 'none';
                document.getElementById('endDate').required = false;
                document.getElementById('startTime').required = false;
            }
            calculateDays();
        }

        function calculateDays() {
            const category = document.querySelector('input[name="leave_category"]:checked').value;
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const totalDaysInput = document.getElementById('totalDays');

            if (category === 'gatepass') {
                totalDaysInput.value = '1 Hour (Gatepass)';
                return;
            }

            if (start && end) {
                const sDate = new Date(start);
                const eDate = new Date(end);
                const diffTime = eDate - sDate;
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                if (category === 'half') {
                    diffDays = 0.5;
                }
                
                totalDaysInput.value = diffDays < 0 ? 0 : diffDays + ' Days';
            } else {
                totalDaysInput.value = category === 'half' ? '0.5 Days' : '1 Day';
            }
        }

        document.getElementById('applyLeaveForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch('{{ route("leave.apply") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Submitted!');
                        window.location.reload();
                    }
                });
        });

        function openActionModal(id) {
            fetch(`/api/leave/details/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('actionLeaveId').value = data.id;
                    document.getElementById('displayAppCode').textContent = `LA-${String(data.id).padStart(4, '0')}`;
                    document.querySelector('#actionForm select[name="status"]').value = data.status;
                    const modal = new bootstrap.Offcanvas(document.getElementById('leaveActionModal'));
                    modal.show();
                });
        }

        function openViewModal(id) {
            fetch(`/api/leave/details/${id}`)
                .then(res => res.json())
                .then(data => {
                    // Reset to details tab
                    const initialTab = new bootstrap.Tab(document.getElementById('details-tab'));
                    initialTab.show();

                    document.getElementById('viewEmployeeName').textContent = data.employee.name;
                    document.getElementById('viewAvatarLetter').textContent = data.employee.name.charAt(0);
                    document.getElementById('viewLeaveType').textContent = data.leave_type;
                    document.getElementById('viewCategoryBadge').textContent = data.leave_category.toUpperCase();
                    
                    document.getElementById('viewStartDateText').textContent = new Date(data.start_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                    document.getElementById('viewStartTimeText').textContent = data.start_time ? data.start_time.substring(0, 5) : 'N/A';
                    
                    document.getElementById('viewEndDateText').textContent = data.end_date ? new Date(data.end_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
                    document.getElementById('viewEndTimeText').textContent = data.end_time ? data.end_time.substring(0, 5) : 'N/A';
                    
                    document.getElementById('viewTotalDays').textContent = data.leave_category === 'gatepass' ? '1 Hour (Gatepass Slot)' : `${data.total_days} Total Days`;
                    document.getElementById('viewReason').textContent = data.reason;
                    document.getElementById('viewMessage').textContent = data.message || 'No extra message.';

                    fetchHistory(data.employee_id);

                    const modal = new bootstrap.Offcanvas(document.getElementById('viewLeaveModal'));
                    modal.show();
                });
        }

        function fetchHistory(empId) {
            const tbody = document.getElementById('historyTableBody');
            const badge = document.getElementById('historyCountBadge');
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted small">Loading records...</td></tr>';

            fetch(`/api/leave/employee/${empId}`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    badge.textContent = `${data.length} Total`;
                    
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted small">No history available for this employee.</td></tr>';
                        return;
                    }

                    const today = new Date();
                    data.forEach(item => {
                        const sDate = new Date(item.start_date);
                        const isFuture = sDate > today;
                        const dateFormatted = sDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                        
                        const statusClass = {
                            'pending': 'bg-soft-dark text-dark',
                            'approved': 'bg-soft-primary text-primary',
                            'on_hold': 'bg-soft-warning text-warning',
                            'rejected': 'bg-soft-danger text-danger'
                        }[item.status] || 'bg-light';

                        tbody.innerHTML += `
                            <tr class="${isFuture ? 'bg-soft-primary bg-opacity-10' : ''}">
                                <td class="ps-3 py-3">
                                    <div class="fw-bold text-dark small">${dateFormatted}</div>
                                    ${isFuture ? '<div class="text-primary fw-bold" style="font-size: 8px;">UPCOMING</div>' : ''}
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">${item.leave_type}</div>
                                    <div class="text-muted" style="font-size: 9px;">${item.leave_category.toUpperCase()}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge ${statusClass} px-2 rounded-pill fw-bold" style="font-size: 10px;">${item.status.toUpperCase()}</span>
                                </td>
                                <td class="pe-3 text-end fw-bold text-primary small">${item.leave_category === 'gatepass' ? '1 Hr' : item.total_days}</td>
                            </tr>
                        `;
                    });
                });
        }

        document.getElementById('actionForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch('{{ route("leave.updateAction") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
        });

        function deleteApplication(id) {
            if (confirm('Sure?')) {
                fetch(`/leave/application/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        window.location.reload();
                    });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            toggleCategoryFields();
        });
    </script>

    <style>
        .bg-soft-primary { background-color: rgba(56, 88, 249, 0.08); }
        .bg-soft-danger { background-color: rgba(239, 68, 68, 0.08); }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.08); }
        .bg-soft-dark { background-color: rgba(51, 65, 85, 0.08); }
        .bg-soft-info { background-color: rgba(6, 182, 212, 0.08); }
        .avatar-text { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .avatar-xs { width: 32px; height: 32px; font-size: 11px; }
        .btn-icon { width: 35px; height: 35px; border-radius: 10px; border: none; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .btn-icon:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-soft-primary { color: #3858f9; background: rgba(56, 88, 249, 0.1); }
        .btn-soft-primary:hover { background: #3858f9; color: #fff; }
        .custom-side-modal { border-left: 2px solid #3858f9; box-shadow: -20px 0 50px rgba(0, 0, 0, 0.15); }
        .nav-tabs-custom .nav-link { border: none; color: #64748b; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .nav-tabs-custom .nav-link.active { color: #3858f9; border-bottom-color: #3858f9; background: transparent; }
        .cursor-pointer { cursor: pointer; }
    </style>
@endsection