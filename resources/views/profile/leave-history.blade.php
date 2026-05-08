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
        flex-wrap: wrap;
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
    .status-on_hold { background: #fff7ed; color: #ea580c; }

    .spotlight-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }

    /* Category Tiles */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-top: 5px;
    }
    .category-grid input[type="radio"] {
        display: none;
    }
    .category-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px 10px;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 85px;
    }
    .category-tile i {
        width: 20px;
        height: 20px;
        margin-bottom: 8px;
        color: #64748b;
        transition: all 0.3s ease;
    }
    .category-tile span {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        text-align: center;
    }
    .category-grid input[type="radio"]:checked + .category-tile {
        border-color: #3858f9;
        background: rgba(56, 88, 249, 0.04);
    }
    .category-grid input[type="radio"]:checked + .category-tile i {
        color: #3858f9;
    }
    .category-grid input[type="radio"]:checked + .category-tile span {
        color: #3858f9;
    }
    .category-tile:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    /* Mobile Enhancements */
    @media (max-width: 767.98px) {
        .category-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .portfolio-header-v2 {
            padding: 30px 0;
            text-align: center;
        }
        .header-actions {
            flex-direction: column !important;
            width: 100% !important;
            gap: 15px !important;
            margin-top: 20px;
        }
        .toggle-switch {
            width: 100% !important;
            justify-content: center;
        }
        .toggle-opt {
            flex: 1;
        }
        .apply-btn {
            width: 100% !important;
            justify-content: center;
        }
        .spotlight-card {
            padding: 30px 20px;
            text-align: center;
        }
        .spotlight-card .d-flex {
            flex-direction: column !important;
        }
        .spotlight-card .text-end {
            text-align: center !important;
            margin-top: 20px;
        }
        .spotlight-card .text-muted {
            margin: 15px auto 0 !important;
        }
        .minimal-row {
            flex-direction: column !important;
            padding: 20px;
            text-align: center;
        }
        .date-column {
            border-right: none;
            border-bottom: 2px solid #f1f5f9;
            padding-right: 0;
            margin-right: 0;
            padding-bottom: 15px;
            margin-bottom: 15px;
            width: 100%;
        }
        .status-area {
            margin-top: 20px;
            width: 100%;
            text-align: center;
        }
        .meta-tags {
            justify-content: center;
        }
    }
</style>

<script src="https://unpkg.com/feather-icons"></script>

<div class="clean-portfolio">
    <div class="portfolio-header-v2">
        <div class="container" style="max-width: 1000px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-3 mb-md-0">
                    <h2 class="fw-bold text-dark mb-1">My Logs</h2>
                    <p class="text-muted small mb-0 fw-medium">History of your leave applications</p>
                </div>
                <div class="d-flex align-items-center gap-3 header-actions" style="width: auto;">
                    <button class="btn btn-primary fw-bold small d-flex align-items-center px-4 shadow-sm apply-btn"
                        data-bs-toggle="offcanvas" data-bs-target="#applyLeaveModal"
                        style="border-radius: 10px; height: 42px; background: #3858f9; border: none;">
                        <i data-feather="plus" style="width: 16px; height: 16px;" class="me-2"></i> APPLY FOR LEAVE
                    </button>
                    <div class="toggle-switch shadow-sm">
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
                        <input type="text"
                            class="form-control border-0 bg-light shadow-sm"
                            value="{{ auth()->user()->name }}"
                            readonly
                            style="height: 48px; border-radius: 10px;">
                        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Today's Date</label>
                        <input type="text" class="form-control border-0 bg-light shadow-sm" value="{{ date('d-m-Y') }}"
                            style="height: 48px; border-radius: 10px;" readonly>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Leave Category <span class="text-danger">*</span></label>
                        <div class="category-grid">
                            <input type="radio" name="leave_category" value="Full Day" id="catFull" checked onchange="toggleCategoryFields()">
                            <label for="catFull" class="category-tile">
                                <i data-feather="sun"></i>
                                <span>Full Day</span>
                            </label>

                            <input type="radio" name="leave_category" value="Half Day" id="catHalf" onchange="toggleCategoryFields()">
                            <label for="catHalf" class="category-tile">
                                <i data-feather="clock"></i>
                                <span>Half Day</span>
                            </label>

                            <input type="radio" name="leave_category" value="Gatepass" id="catGate" onchange="toggleCategoryFields()">
                            <label for="catGate" class="category-tile">
                                <i data-feather="log-out"></i>
                                <span>Early Leave</span>
                            </label>

                            <input type="radio" name="leave_category" value="WFH" id="catWFH" onchange="toggleCategoryFields()">
                            <label for="catWFH" class="category-tile">
                                <i data-feather="home"></i>
                                <span>WFH</span>
                            </label>
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

                    <div class="col-md-6" id="leaveTypeWrapper">
                        <label class="form-label small fw-bold text-muted text-uppercase">Leave Type <span
                                class="text-danger">*</span></label>
                        <select name="leave_type" id="leaveType" class="form-select border-0 bg-white shadow-sm"
                            style="height: 48px; border-radius: 10px;" required>
                            <option value="">Select Type...</option>
                            <option value="Paid Leave">Paid Leave</option>
                            <option value="Sick Leave">Sick Leave</option>
                            <option value="Gatepass Leave">Early Leave</option>
                            <option value="Casual Leave">Casual Leave</option>
                            <option value="WFH">WFH</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Start Date <span
                                class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="date" name="start_date" id="startDate" class="form-control border-0 bg-white shadow-sm"
                                style="height: 48px; border-radius: 10px; padding-right: 40px;" required onchange="calculateDays()"
                                min="{{ date('Y-m-d') }}">
                            <i data-feather="calendar" class="position-absolute text-primary" style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; width: 18px;"></i>
                        </div>
                    </div>

                    <div class="col-md-4" id="endDateWrapper">
                        <label class="form-label small fw-bold text-muted text-uppercase">End Date</label>
                        <div class="position-relative">
                            <input type="date" name="end_date" id="endDate" class="form-control border-0 bg-white shadow-sm"
                                style="height: 48px; border-radius: 10px; padding-right: 40px;" onchange="calculateDays()" min="{{ date('Y-m-d') }}">
                            <i data-feather="calendar" class="position-absolute text-primary" style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; width: 18px;"></i>
                        </div>
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
                            style="height: 50px; border-radius: 12px; background: #16a34a; border: none;">APPLY NOW</button>
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
                <div class="spotlight-card shadow-sm border-0">
                    <span class="text-primary small fw-bold text-uppercase mb-3 d-block" style="letter-spacing: 1px;">LATEST RECORDING</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold text-dark mb-2" style="font-size: 24px;">{{ $latest->leave_type }}</h3>
                            <div class="meta-tags mb-4">
                                <span class="tag-sm">{{ $latest->leave_category }}</span>
                                <span class="text-muted small fw-bold">|</span>
                                <span class="text-muted small fw-bold">{{ $latest->total_days }} Total Days</span>
                            </div>
                            
                            <div class="d-flex align-items-center gap-3 text-dark fw-bold bg-light p-3 rounded-4 d-inline-flex">
                                <i class="feather-calendar text-primary" style="width: 20px;"></i>
                                <span style="font-size: 15px;">{{ $latest->start_date->format('d M, Y') }} — {{ $latest->end_date ? $latest->end_date->format('d M, Y') : 'Same Day' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="status-dot-label shadow-sm {{ 'status-'.strtolower($latest->status) }}">
                                {{ strtoupper(str_replace('_', ' ', $latest->status)) }}
                            </div>
                            @if($latest->reason)
                                <p class="text-muted small mt-3 mb-0" style="max-width: 250px; font-style: italic;">"{{ $latest->reason }}"</p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="bg-light p-4 rounded-circle d-inline-block mb-3">
                        <i data-feather="file-text" style="width: 40px; height: 40px;" class="text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-muted">No recent logs found.</h5>
                </div>
            @endif
        </div>

        <!-- FULL LIST -->
        <div id="tab-archive" class="content-pane d-none">
            @forelse($leaves as $item)
                <div class="minimal-row shadow-sm border-0 mb-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="date-column">
                            <span class="day-num">{{ $item->start_date->format('d') }}</span>
                            <span class="month-text">{{ $item->start_date->format('M') }}</span>
                        </div>
                        <div class="info-column">
                            <h5 class="type-title mb-1">{{ $item->leave_type }}</h5>
                            <div class="meta-tags">
                                <span class="tag-sm bg-soft-primary text-primary">{{ $item->leave_category }}</span>
                                <span class="text-muted small fw-bold">{{ $item->total_days }}D</span>
                                <span class="text-muted small fw-medium">at {{ $item->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="status-area">
                        <div class="status-dot-label shadow-sm {{ 'status-'.strtolower($item->status) }}">
                            {{ strtoupper(str_replace('_', ' ', $item->status)) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i data-feather="archive" style="width: 40px; height: 40px;" class="text-muted mb-3"></i>
                    <h5 class="fw-bold text-muted">Archive is empty.</h5>
                </div>
            @endforelse
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

            if (diffDays === 0) diffDays = 1;
            if (category === 'Half Day') diffDays = 0.5;

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
                        setTimeout(() => window.location.reload(), 1500);
                    }
                })
                .catch(err => console.error(err));
            });
        }
    });

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
        setTimeout(() => toast.classList.remove('toast-show'), 2000);
    }
</script>

<div id="customToast" class="custom-toast">
    <span id="toastIcon" class="toast-icon"></span>
    <span id="toastMessage"></span>
</div>

<style>
    .custom-toast {
        position: fixed; top: 20px; right: 20px; padding: 14px 24px; border-radius: 12px;
        color: #fff; font-weight: 600; font-size: 14px; z-index: 99999; display: flex;
        align-items: center; gap: 10px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        transform: translateX(120%); transition: transform 0.4s ease;
    }
    .custom-toast.toast-show { transform: translateX(0); }
    .custom-toast.toast-success { background: linear-gradient(135deg, #16a34a, #22c55e); }
    .custom-toast.toast-error { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .toast-icon { width: 24px; height: 24px; border-radius: 50%; background: rgba(255, 255, 255, 0.25); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        background: transparent; bottom: 0; color: transparent; cursor: pointer;
        height: auto; left: 0; position: absolute; right: 0; top: 0; width: auto;
    }
</style>
@endsection
