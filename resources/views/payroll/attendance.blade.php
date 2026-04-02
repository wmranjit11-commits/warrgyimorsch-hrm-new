@extends('layouts.app')

@section('content')
    <style>
        .bg-soft-success {
            background: rgba(34, 197, 94, 0.12) !important;
            color: #16a34a !important;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.12) !important;
            color: #dc2626 !important;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .bg-soft-warning {
            background: rgba(245, 158, 11, 0.12) !important;
            color: #d97706 !important;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .bg-soft-info {
            background: rgba(6, 182, 212, 0.12) !important;
            color: #0891b2 !important;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .bg-soft-primary {
            background: rgba(56, 88, 249, 0.12) !important;
            color: #3858f9 !important;
            border: 1px solid rgba(56, 88, 249, 0.2);
        }

        .bg-soft-dark {
            background: rgba(30, 41, 59, 0.08) !important;
            color: #1e293b !important;
            border: 1px solid rgba(30, 41, 59, 0.15);
        }

        .badge-att {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .badge-att:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
    <div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <!-- Main Content Card -->
        <div class="px-4 pt-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #334155;">Attendance Management</h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#"
                                        class="text-decoration-none text-muted small">Home</a></li>
                                <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;"
                                    aria-current="page">Attendance List</li>
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

                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-success text-success"
                            onclick="exportMonthlySheet()" title="Download Attendance Sheet (Excel)">
                            <i class="feather-download"></i>
                        </a>

                        <a href="{{ route('payroll.attendance.add') }}" class="avatar-text avatar-md bg-primary text-white"
                            title="Add Attendance">
                            <i class="feather-plus"></i>
                        </a>
                    </div>
                </div>

                <!-- Prominent Filter Section -->
                <div id="filterSection" class="border-bottom bg-light bg-opacity-10 p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">Select Month</label>
                            <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <input type="month" id="monthFilter" class="form-control border-0 bg-white py-2 fw-bold"
                                    value="{{ request('month', date('Y-m')) }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">From Date</label>
                            <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <input type="date" id="startDate" class="form-control border-0 bg-white py-2 fw-bold"
                                    value="{{ $start_date }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-2">To Date</label>
                            <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <input type="date" id="endDate" class="form-control border-0 bg-white py-2 fw-bold"
                                    value="{{ $end_date }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button
                                class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                onclick="applyFilters()"
                                style="background: #3858f9; border: none; height: 42px; border-radius: 8px;">
                                <i class="feather-search"></i> FILTER
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('payroll.attendance') }}"
                                class="btn btn-outline-secondary w-100 fw-bold d-flex align-items-center justify-content-center gap-2"
                                style="height: 42px; border-radius: 8px; border-style: dashed;">
                                <i class="feather-refresh-cw"></i> RESET
                            </a>
                        </div>
                    </div>
                </div>

                @if ($message = Session::get('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center py-3" role="alert"
                        style="border-radius: 12px; background: #ecfdf5; color: #065f46;">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div class="fw-bold">{{ $message }}</div>
                        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: #ffffff; border-bottom: 1px solid #f1f5f9;">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase"
                                    style="letter-spacing: 0.5px; width: 150px;">DATE</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                    ATTENDANCE HISTORY</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end"
                                    style="width: 150px; letter-spacing: 0.5px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance_dates as $att)
                                <tr class="border-bottom hover-row">
                                    <td class="ps-4 py-3 text-dark fw-bold">
                                        {{ \Carbon\Carbon::parse($att->attendance_date)->format('d-m-Y') }}
                                        <div class="small text-muted fw-normal">
                                            {{ \Carbon\Carbon::parse($att->attendance_date)->format('l') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($att->is_holiday)
                                                <div class="badge-att bg-soft-primary" title="Official Holiday">
                                                    <i class="bi bi-star-fill me-2"></i>
                                                    <span class="fw-bold small">HOLIDAY: {{ $att->holiday_title }}</span>
                                                </div>
                                            @endif

                                            @if($att->count > 0)
                                                <div class="badge-att bg-soft-successclickable" title="Click to view Present employees"
                                                                 onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'present')">
                                                                <i class="bi bi-person-check-fill me-2"></i>
                                                                <span class="small fw-bold">{{ $att->present }} Present</span>
                                                            </div>
                                                            <div class="badge-att bg-soft-warning clickable" title="Click to view Half Day records"
                                                                 onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'half_day')">
                                                                <i class="bi bi-clock-fill me-2"></i>
                                                                <span class="small fw-bold">{{ $att->half_day }} Half Day</span>
                                                            </div>
                                                            <div class="badge-att bg-soft-dark clickable" title="Click to view Overtime performance"
                                                                 onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'overtime')">
                                                                <i class="bi bi-alarm-fill me-2"></i>
                                                                <span class="small fw-bold">{{ $att->overtime }} Overtime</span>
                                                            </div>
                                                            <div class="badge-att bg-soft-info clickable" title="Click to view Leave records"
                                                                 onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'leave')">
                                                                <i class="bi bi-calendar-event-fill me-2"></i>
                                                                <span class="small fw-bold">{{ $att->leave }} Leave</span>
                                                            </div>
                                                            <div class="badge-att bg-soft-danger clickable" title="Click to view Absent employees"
                                                                 onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'absent')">
                                                                <i class="bi bi-person-x-fill me-2"></i>
                                                                <span class="small fw-bold">{{ $att->absent }} Absent</span>
                                                            </div>
                                            @elseif(!$att->is_holiday)
                                                        <span class="text-muted small italic">No attendance marked for this date</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-primary text-primary"
                                                        onclick="openAttendanceDetails('{{ $att->attendance_date }}')" title="View Details">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    @if($att->count > 0)
                                                        <a href="javascript:void(0);"
                                                            class="avatar-text avatar-md bg-soft-danger text-danger"
                                                            onclick="deleteAttendanceByDate('{{ $att->attendance_date }}')" title="Delete All">
                                                            <i class="feather-trash-2"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                            @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5">
                                            <div class="py-5">
                                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem; opacity: 0.2;"></i>
                                                <p class="text-muted mt-3 fw-bold">No Attendance Records Found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('modals')
    <div class="offcanvas offcanvas-end" tabindex="-1" id="attendanceDetailOffcanvas" style="width: 900px;">
        <div class="offcanvas-header border-bottom px-4 py-3 bg-white shadow-sm">
            <div class="d-flex flex-column">
                <h5 class="offcanvas-title fw-bold" style="color: #334155;">Record for <span id="offcanvasDate"
                        class="text-primary"></span></h5>
                <div id="statusIndicator" class="small fw-bold text-muted mt-1">Showing All Records</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-link text-decoration-none fw-bold small p-0 me-3" id="showAllBtn"
                    style="display:none;" onclick="resetModalFilter()">Show All</button>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
            </div>
        </div>
        <div class="offcanvas-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 small fw-bold text-muted text-uppercase" style="width: 80px;">SR. NO.</th>
                            <th class="py-3 small fw-bold text-muted text-uppercase">EMPLOYEE</th>
                            <th class="py-3 small fw-bold text-muted text-uppercase text-center">CHECK IN</th>
                            <th class="py-3 small fw-bold text-muted text-uppercase text-center">CHECK OUT</th>
                            <th class="py-3 small fw-bold text-muted text-uppercase text-center">STATUS</th>
                            <th class="pe-4 py-3 small fw-bold text-muted text-uppercase text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="offcanvasTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        function toggleFilter() {
            const filter = document.getElementById('filterSection');
            filter.style.display = filter.style.display === 'none' ? 'block' : 'none';
        }

        function applyFilters() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const month = document.getElementById('monthFilter').value;

            let url = new URL(window.location.href);
            if (month) {
                url.searchParams.set('month', month);
                // Clear manual dates if month is selected
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');
            } else {
                if (start) url.searchParams.set('start_date', start);
                if (end) url.searchParams.set('end_date', end);
                url.searchParams.delete('month');
            }
            window.location.href = url.toString();
        }

        let lastFetchedData = null;
        let lastDate = null;

        function resetModalFilter() {
            if (lastFetchedData) renderTable(lastFetchedData, null);
            document.getElementById('showAllBtn').style.display = 'none';
            document.getElementById('statusIndicator').innerText = 'Showing All Records';
        }

        function openAttendanceDetails(date, filterStatus = null) {
            lastDate = date;
            fetch(`/payroll/attendance/details?date=${date}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        lastFetchedData = data.data;
                        document.getElementById('offcanvasDate').innerText = date;
                        renderTable(data.data, filterStatus);

                        const offcanvasEl = document.getElementById('attendanceDetailOffcanvas');
                        const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                        offcanvas.show();
                    }
                });
        }

        function renderTable(rows, filterStatus) {
            const body = document.getElementById('offcanvasTableBody');
            body.innerHTML = '';

            let count = 0;
            rows.forEach((item, index) => {
                let match = !filterStatus;
                let itemStatus = item.status.toLowerCase();

                if (filterStatus === 'present' && (itemStatus === 'present' || itemStatus === 'late')) match = true;
                if (filterStatus === 'absent' && itemStatus === 'absent') match = true;
                if (filterStatus === 'half_day' && itemStatus === 'half_day') match = true;
                if (filterStatus === 'leave' && itemStatus === 'leave') match = true;
                if (filterStatus === 'overtime' && parseFloat(item.total_hours) > 9) match = true;

                if (match) {
                    count++;
                    body.innerHTML += `
                        <tr class="border-bottom">
                            <td class="ps-4 py-3 text-muted fw-bold">${count}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${item.employee.photo_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(item.employee.name)}" 
                                         class="rounded-circle me-2" style="width:30px; height:30px; object-fit:cover;">
                                    <div class="fw-bold text-dark">${item.employee.name}</div>
                                </div>
                            </td>
                            <td class="text-center">${item.check_in ? formatTime(item.check_in) : '--'}</td>
                            <td class="text-center">${item.check_out ? formatTime(item.check_out) : '--'}</td>
                            <td class="text-center">
                                <span class="status-badge ${getStatusBadge(item.status)}">${item.status}</span>
                            </td>
                            <td class="pe-4 text-center">
                                <button class="btn btn-sm text-danger shadow-none" onclick="deleteSingleAttendance(${item.id}, '${lastDate}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }
            });

            if (filterStatus) {
                document.getElementById('showAllBtn').style.display = 'block';
                document.getElementById('statusIndicator').innerHTML = `Showing: <span class="text-primary text-uppercase">${filterStatus}</span> (${count} found)`;
            } else {
                document.getElementById('showAllBtn').style.display = 'none';
                document.getElementById('statusIndicator').innerText = 'Showing All Records';
            }
        }

        function formatTime(time) {
            if (!time) return '--';
            let [h, m] = time.split(':');
            let ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${m} ${ampm}`;
        }

        function getStatusBadge(status) {
            switch (status.toLowerCase()) {
                case 'present': return 'status-badge-success';
                case 'absent': return 'status-badge-danger';
                case 'half_day': return 'status-badge-warning';
                default: return 'status-badge-info';
            }
        }

        function deleteAttendanceByDate(date) {
            if (confirm('Are you sure you want to delete all records for ' + date + '?')) {
                fetch(`/payroll/attendance/date/${date}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => res.json()).then(data => {
                    if (data.success) window.location.reload();
                });
            }
        }

        function deleteSingleAttendance(id, date) {
            if (confirm('Delete this record?')) {
                fetch(`/payroll/attendance/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => res.json()).then(data => {
                    if (data.success) openAttendanceDetails(date);
                });
            }
        }

        function exportAttendance() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            window.location.href = '{{ route("payroll.attendance.export") }}?start_date=' + start + '&end_date=' + end;
        }

        function exportMonthlySheet() {
            const start = document.getElementById('startDate').value;
            if(!start) {
                alert('Please select a From Date to determine the month.');
                return;
            }
            const date = new Date(start);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            window.location.href = `{{ route('payroll.attendance.export') }}?type=monthly_sheet&year=${year}&month=${month}`;
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

        .ref-badge {
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.5px;
            min-width: 90px;
            display: inline-block;
            text-align: center;
            text-transform: uppercase;
        }

        .clickable {
            cursor: pointer;
            transition: transform 0.1s;
        }

        .clickable:hover {
            transform: scale(1.05);
        }

        .badge-green {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }

        .badge-blue {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        .badge-yellow {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }

        .badge-red {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .status-badge-success {
            background: #ecfdf5;
            color: #059669;
        }

        .status-badge-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-badge-warning {
            background: #fffbeb;
            color: #d97706;
        }

        .status-badge-info {
            background: #eff6ff;
            color: #2563eb;
        }

        .action-btn-outline {
            background: transparent !important;
            border: 0 !important;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            color: #64748b;
            box-shadow: none !important;
        }

        .action-btn-outline:hover {
            background: #f1f5f9 !important;
            color: #3858f9;
            border: 0 !important;
        }
    </style>
@endpush