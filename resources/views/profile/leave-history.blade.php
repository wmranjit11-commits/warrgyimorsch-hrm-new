@extends('layouts.app')

@section('content')
<style>
    .clean-portfolio {
        background: #fdfdfe;
        min-height: 100vh;
        padding-bottom: 60px;
    }
    .portfolio-header-v2 {
        background: #fff;
        padding: 40px 0;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 40px;
    }
    .toggle-switch {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        width: fit-content;
    }
    .toggle-opt {
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: #94a3b8;
    }
    .toggle-opt.active {
        background: #fff;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .archive-list {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .minimal-row {
        background: #fff;
        border-radius: 20px;
        padding: 25px 30px;
        border: 1px solid #f1f5f9;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s;
    }
    .minimal-row:hover {
        border-color: #3858f9;
        background: #fafbff;
    }

    .date-column {
        min-width: 80px;
        text-align: center;
        border-right: 2px solid #f1f5f9;
        padding-right: 25px;
        margin-right: 25px;
    }
    .day-num {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        display: block;
        line-height: 1;
    }
    .month-text {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
    }

    .info-column {
        flex-grow: 1;
    }
    .type-title {
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .meta-tags {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .tag-sm {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .status-area {
        text-align: right;
        min-width: 120px;
    }
    .status-dot-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 6px 16px;
        border-radius: 100px;
        display: inline-block;
    }

    .status-approved { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fef9c3; color: #a16207; }
    .status-rejected { background: #fff1f2; color: #e11d48; }

    .spotlight-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
</style>

<script src="https://unpkg.com/feather-icons"></script>

<div class="clean-portfolio">
    <div class="portfolio-header-v2">
        <div class="container" style="max-width: 1000px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">My Logs</h2>
                    <p class="text-muted small mb-0 fw-medium">History of your leave applications</p>
                </div>
                <div class="d-flex justify-content-between" style="width: 25rem;">
                    <button class="btn btn-primary fw-bold small d-flex align-items-center px-4 shadow-sm"
                        data-bs-toggle="offcanvas" data-bs-target="#applyLeaveModal"
                        style="border-radius: 10px; height: 42px; background: #3858f9; border: none;">
                        <i data-feather="plus" style="width: 16px; height: 16px;" class="me-2"></i> Apply For Leave
                    </button>
                    <div class="toggle-switch">
                        <button class="toggle-opt active" onclick="switchTab('recent', this)">Recent</button>
                        <button class="toggle-opt" onclick="switchTab('archive', this)">Archive</button>
                    </div>
                </div>
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
                            style="height: 48px; border-radius: 10px;" required>
                            <option value="">Select Employee</option>
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Today's Date</label>
                        <input type="text" class="form-control border-0 bg-light shadow-sm" value="{{ date('d-m-Y') }}"
                            style="height: 48px; border-radius: 10px;" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Leave Category <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-3 bg-white p-2 rounded-3 shadow-sm border" style="height: 48px;">
                            <div class="form-check d-flex align-items-center mb-0 ps-4">
                                <input class="form-check-input" type="radio" name="leave_category" value="Full Day"
                                    id="catFull" checked onchange="toggleCategoryFields()">
                                <label class="form-check-label small fw-bold ms-1" for="catFull">Full Day</label>
                            </div>
                            <div class="form-check d-flex align-items-center mb-0 ps-2">
                                <input class="form-check-input" type="radio" name="leave_category" value="Half Day"
                                    id="catHalf" onchange="toggleCategoryFields()">
                                <label class="form-check-label small fw-bold ms-1" for="catHalf">Half Day</label>
                            </div>
                            <div class="form-check d-flex align-items-center mb-0 ps-2">
                                <input class="form-check-input" type="radio" name="leave_category" value="Gatepass"
                                    id="catGate" onchange="toggleCategoryFields()">
                                <label class="form-check-label small fw-bold ms-1" for="catGate">Early Leave</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="halfDayOptionWrapper" style="display: none;">
                        <label class="form-label small fw-bold text-muted text-uppercase">Which Half? <span
                                class="text-danger">*</span></label>
                        <select name="half_day_type" class="form-select border-0 bg-white shadow-sm"
                            style="height: 48px; border-radius: 10px;">
                            <option value="First Half">First Half</option>
                            <option value="Second Half">Second Half</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Leave Type <span
                                class="text-danger">*</span></label>
                        <select name="leave_type" id="leaveType" class="form-select border-0 bg-white shadow-sm"
                            style="height: 48px; border-radius: 10px;" required>
                            <option value="">Select Type...</option>
                            <option value="Paid Leave">Paid Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Gatepass Leave">Early Leave</option>
                            <option value="Casual Leave">Casual Leave</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Start Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="startDate" class="form-control border-0 bg-white shadow-sm"
                            style="height: 48px; border-radius: 10px;" required onchange="calculateDays()"
                            min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-4" id="endDateWrapper">
                        <label class="form-label small fw-bold text-muted text-uppercase">End Date</label>
                        <input type="date" name="end_date" id="endDate" class="form-control border-0 bg-white shadow-sm"
                            style="height: 48px; border-radius: 10px;" onchange="calculateDays()" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-4" id="startTimeWrapper" style="display: none;">
                        <label class="form-label small fw-bold text-muted text-uppercase">Start Time <span
                                class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="startTime" class="form-control border-0 bg-white shadow-sm"
                            style="height: 48px; border-radius: 10px;" onchange="calculateDays()">
                    </div>

                    <div class="col-md-4" id="endTimeWrapper" style="display: none;">
                        <label class="form-label small fw-bold text-muted text-uppercase">End Time (Auto)</label>
                        <input type="time" name="end_time" id="endTime"
                            class="form-control border-0 bg-light shadow-sm text-muted fw-bold"
                            style="height: 48px; border-radius: 10px;" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Total Days/Duration</label>
                        <input type="text" name="total_days" id="totalDays"
                            class="form-control border-0 bg-light shadow-sm fw-bold text-primary" value="1"
                            style="height: 48px; border-radius: 10px;" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Reason <span
                                class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control border-0 bg-white shadow-sm"
                            placeholder="Enter short reason" style="height: 48px; border-radius: 10px;" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase">Message (Optional)</label>
                        <textarea name="message" class="form-control border-0 bg-white shadow-sm"
                            placeholder="Enter detailed message" style="height: 100px; border-radius: 10px;"></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm"
                            style="height: 50px; border-radius: 12px; background: #16a34a; border: none;">Apply Now</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="archive-list">
        <!-- RECENT SPOTLIGHT -->
        <div id="tab-recent" class="content-pane">
            @php $latest = $leaves->first(); @endphp
            @if($latest)
                <div class="spotlight-card">
                    <span class="text-primary small fw-800 text-uppercase letter-spacing-1 mb-3 d-block">Latest Recording</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-800 text-dark mb-1" style="font-size: 28px;">{{ $latest->leave_type }}</h3>
                            <div class="meta-tags mb-4">
                                <span class="tag-sm">{{ $latest->leave_category }}</span>
                                <span class="text-muted small fw-bold">|</span>
                                <span class="text-muted small fw-bold">{{ $latest->total_days }} Total Days</span>
                            </div>
                            
                            <div class="d-flex align-items-center gap-3 text-dark fw-bold">
                                <i class="feather-calendar text-primary"></i>
                                <span>{{ $latest->start_date->format('d M, Y') }} — {{ $latest->end_date ? $latest->end_date->format('d M, Y') : 'Same Day' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="status-dot-label {{ 'status-'.strtolower($latest->status) }}">
                                {{ strtoupper($latest->status) }}
                            </div>
                            <p class="text-muted small mt-3 mb-0" style="max-width: 200px;">"{{ $latest->reason }}"</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- FULL LIST -->
        <div id="tab-archive" class="content-pane d-none">
            @foreach($leaves as $item)
                <div class="minimal-row">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="date-column">
                            <span class="day-num">{{ $item->start_date->format('d') }}</span>
                            <span class="month-text">{{ $item->start_date->format('M') }}</span>
                        </div>
                        <div class="info-column">
                            <h5 class="type-title">{{ $item->leave_type }}</h5>
                            <div class="meta-tags">
                                <span class="tag-sm">{{ $item->leave_category }}</span>
                                <span class="text-muted small fw-bold">{{ $item->total_days }}D</span>
                                <span class="text-muted small fw-medium">at {{ $item->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="status-area">
                        <div class="status-dot-label {{ 'status-'.strtolower($item->status) }}">
                            {{ strtoupper($item->status) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function switchTab(tabId, el) {
        document.querySelectorAll('.toggle-opt').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.content-pane').forEach(p => p.classList.add('d-none'));
        document.getElementById('tab-' + tabId).classList.remove('d-none');
    }

    function toggleCategoryFields() {
            const category = document.querySelector('input[name="leave_category"]:checked').value;
            const endDateWrapper = document.getElementById('endDateWrapper');
            const startTimeWrapper = document.getElementById('startTimeWrapper');
            const endTimeWrapper = document.getElementById('endTimeWrapper');
            const halfDayOptionWrapper = document.getElementById('halfDayOptionWrapper');
            const leaveType = document.getElementById('leaveType');

            halfDayOptionWrapper.style.display = 'none';
            endTimeWrapper.style.display = 'none';

            if (category === 'Gatepass') {
                endDateWrapper.style.display = 'none';
                startTimeWrapper.style.display = 'block';
                endTimeWrapper.style.display = 'block';
                leaveType.value = 'Gatepass Leave';
                document.getElementById('endDate').required = false;
                document.getElementById('startTime').required = true;
            } else if (category === 'Half Day') {
                endDateWrapper.style.display = 'none';
                startTimeWrapper.style.display = 'none';
                halfDayOptionWrapper.style.display = 'block';
                document.getElementById('endDate').required = false;
                document.getElementById('startTime').required = false;
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

            if (category === 'Gatepass') {
                const startTimeInput = document.getElementById('startTime');
                const endTimeInput = document.getElementById('endTime');
                if (startTimeInput.value) {
                    const [hours, minutes] = startTimeInput.value.split(':');
                    let dateObj = new Date();
                    dateObj.setHours(parseInt(hours) + 1, parseInt(minutes));
                    endTimeInput.value = dateObj.toTimeString().substring(0, 5);
                }
                totalDaysInput.value = '1 Hour (Gatepass)';
                return;
            }

            if (start && end) {
                const sDate = new Date(start);
                const eDate = new Date(end);
                const diffTime = eDate - sDate;
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                // Same day logic or 8th-to-9th logic: both counts as 1 day 
                // because end date is exclusive (return date).
                if (diffDays === 0) {
                    diffDays = 1;
                }

                if (category === 'Half Day') {
                    diffDays = 0.5;
                }

                totalDaysInput.value = diffDays < 0 ? 0 : diffDays + ' Days';
            } else if (start) {
                totalDaysInput.value = category === 'Half Day' ? '0.5 Days' : '1 Days';
            } else {
                totalDaysInput.value = category === 'Half Day' ? '0.5 Days' : '0 Days';
            }
    }

    document.addEventListener('DOMContentLoaded', function () {
        feather.replace();
        const applyForm = document.getElementById('applyLeaveForm');

        if (applyForm) {

            applyForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = {};

                formData.forEach((value, key) => data[key] = value);

                let totalDaysRaw = data['total_days'] || '0';
                data['total_days'] = parseFloat(totalDaysRaw.replace(/[^0-9.]/g, '')) || 0;

                if (data['leave_category'] === 'Half Day' && data['half_day_type']) {
                    data['leave_category'] = `Half Day (${data['half_day_type']})`;
                }

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
                        showToast('Leave applied successfully!', 'success');
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                })
                .catch(err => {
                    console.error(err);
                });
            });

        }
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

                    const catRaw = data.leave_category.toLowerCase();
                    let catDisp = data.leave_category.toUpperCase();
                    let catClass = 'bg-soft-primary text-primary';

                    if (catRaw.includes('half')) {
                        catDisp = catDisp.replace('HALF', 'HALF DAY').replace('HALF DAY DAY', 'HALF DAY');
                        catClass = 'bg-soft-warning text-warning';
                    } else if (catRaw === 'full' || catRaw.trim() === 'full') {
                        catDisp = 'FULL DAY';
                    } else if (catRaw.includes('gatepass')) {
                        catClass = 'bg-soft-info text-info';
                    }

                    const catBadge = document.getElementById('viewCategoryBadge');
                    catBadge.textContent = catDisp;
                    catBadge.className = `badge ${catClass} border border-opacity-10`;

                    const statusBadge = document.getElementById('viewStatusBadge');
                    const status = data.status.toLowerCase();
                    statusBadge.textContent = status.toUpperCase();

                    // Reset classes
                    statusBadge.className = 'badge rounded-pill fw-bold text-uppercase';
                    const statusClass = {
                        'pending': 'bg-soft-dark text-dark',
                        'approved': 'bg-soft-primary text-primary',
                        'on_hold': 'bg-soft-warning text-warning',
                        'rejected': 'bg-soft-danger text-danger',
                        'unauthorised': 'bg-soft-danger text-danger'
                    }[status] || 'bg-light';
                    statusBadge.classList.add(...statusClass.split(' '));

                    const cat = data.leave_category.toLowerCase();
                    const isGatepass = cat.includes('gatepass');
                    const isHalfDay = cat.includes('half');

                    // Duration & Days
                    if (isGatepass) {
                        document.getElementById('viewTotalDays').textContent = '1 Hour (Gatepass Slot)';
                    } else if (isHalfDay) {
                        document.getElementById('viewTotalDays').textContent = '0.5 Days (Half Day)';
                    } else {
                        document.getElementById('viewTotalDays').textContent = `${data.total_days} Total Days`;
                    }

                    // Timeframe section
                    if (isGatepass) {
                        // Gatepass: Show date + time slot
                        document.getElementById('viewStartDateText').textContent = new Date(data.start_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                        document.getElementById('viewStartTimeText').textContent = data.start_time ? data.start_time.substring(0, 5) : 'N/A';
                        document.getElementById('viewEndDateText').textContent = 'Same Day';
                        document.getElementById('viewEndTimeText').textContent = data.end_time ? data.end_time.substring(0, 5) : 'N/A';
                    } else if (isHalfDay) {
                        // Half Day: Show date + which half
                        document.getElementById('viewStartDateText').textContent = new Date(data.start_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });

                        // Extract "First Half" or "Second Half" more robustly
                        let whichHalf = 'Half Day';
                        if (cat.includes('first')) whichHalf = 'First Half';
                        else if (cat.includes('second')) whichHalf = 'Second Half';

                        document.getElementById('viewStartTimeText').textContent = whichHalf;

                        // End part for Half Day
                        document.getElementById('viewEndDateText').textContent = 'Same Day';
                        document.getElementById('viewEndTimeText').textContent = '0.5 Day';
                    } else {
                        // Full Day: Show date range
                        document.getElementById('viewStartDateText').textContent = new Date(data.start_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
                        document.getElementById('viewStartTimeText').textContent = 'Full Day';
                        document.getElementById('viewEndDateText').textContent = data.end_date ? new Date(data.end_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
                        document.getElementById('viewEndTimeText').textContent = 'Return Date';
                    }

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
                            'rejected': 'bg-soft-danger text-danger',
                            'unauthorised': 'bg-soft-danger text-danger'
                        }[item.status] || 'bg-light';

                        const catRaw = item.leave_category.toLowerCase();
                        let catClass = 'bg-soft-primary text-primary';
                        let catDisp = item.leave_category.toUpperCase();
                        
                        if (catRaw.includes('half')) {
                            catDisp = catDisp.replace('HALF', 'HALF DAY').replace('HALF DAY DAY', 'HALF DAY');
                            catClass = 'bg-soft-warning text-warning';
                        } else if (catRaw === 'full' || catRaw === 'full day') {
                            catDisp = 'FULL DAY';
                        } else if (catRaw.includes('gatepass')) {
                            catClass = 'bg-soft-info text-info';
                        }

                        tbody.innerHTML += `
                                                    <tr class="${isFuture ? 'bg-soft-primary bg-opacity-10' : ''}">
                                                        <td class="ps-3 py-3">
                                                            <div class="fw-bold text-dark small">${dateFormatted}</div>
                                                            ${isFuture ? '<div class="text-primary fw-bold" style="font-size: 8px;">UPCOMING</div>' : ''}
                                                        </td>
                                                        <td>
                                                            <div class="fw-semibold text-dark small">${item.leave_type}</div>
                                                            <span class="badge ${catClass} p-1 px-2 mt-1" style="font-size: 8px; font-weight: 800;">${catDisp}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge ${statusClass} px-2 rounded-pill fw-bold" style="font-size: 10px;">${item.status.toUpperCase()}</span>
                                                        </td>
                                                        <td class="pe-3 text-end fw-bold text-primary small">${item.leave_category.toLowerCase().includes('gatepass') ? '1 Hr' : item.total_days}</td>
                                                    </tr>
                                                `;
                    });
                });
    }

    function showToast(message, type) {
            const toast = document.getElementById('customToast');
            const toastMsg = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');
            toastMsg.textContent = message;

            toast.className = 'custom-toast';
            if (type === 'success') {
                toast.classList.add('toast-success');
                toastIcon.innerHTML = '✓';
            } else {
                toast.classList.add('toast-error');
                toastIcon.innerHTML = '✗';
            }

            toast.classList.add('toast-show');
            setTimeout(() => {
                toast.classList.remove('toast-show');
            }, 2000);
    }

</script>

    <!-- Toast Notification -->
    <div id="customToast" class="custom-toast">
        <span id="toastIcon" class="toast-icon"></span>
        <span id="toastMessage"></span>
    </div>

<style>
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
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            transform: translateX(120%);
            transition: transform 0.4s ease;
            font-family: 'Inter', sans-serif;
        }

        .custom-toast.toast-show {
            transform: translateX(0);
        }

        .custom-toast.toast-success {
            background: linear-gradient(135deg, #16a34a, #22c55e);
        }

        .custom-toast.toast-error {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
        }
</style>
@endsection
