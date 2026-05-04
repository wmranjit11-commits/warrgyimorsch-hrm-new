@extends('layouts.app')

@section('content')
<div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Main Content Card -->
    <div class="px-4 pt-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                <div>
                    <h5 class="fw-bold mb-0" style="color: #334155;">Attendance Management</h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted small">Home</a></li>
                            <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;" aria-current="page">Attendance List</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('payroll.attendace.employee') }}" class="btn btn-icon btn-light-brand"><label>Employee wise attendance</label></a>
                     <div class="dropdown d-inline-block ms-auto float-end">
                        <a href="#" class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" aria-expanded="false" title="Import Attendance"> <label>Import Attendance &nbsp; </label>
                            <i class="fas fa-upload"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end p-4 shadow-sm border-0" style="width: 320px;">
                            <form action="{{ route('payroll.attendance.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark mb-2">Import Attendance (Excel)</label>
                                    <input type="file" class="form-control" name="import_file" accept=".xlsx, .xls, .csv" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100 fw-bold">
                                    <i class="fas fa-file-import me-2"></i> UPLOAD & CALCULATE
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Right Aligned Search & Actions -->
                    <div class="input-group d-none d-md-flex" style="width: 250px;">
                        <span class="input-group-text bg-light border-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" id="tableSearch" class="form-control bg-light border-0 shadow-none" placeholder="Search..." onkeyup="applyFilters()">
                    </div>
                    
                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary" data-bs-toggle="collapse" data-bs-target="#filterSection" title="Filter Records">
                        <i class="feather-filter"></i>
                    </a>

                    <a href="javascript:void(0);" 
                        class="avatar-text avatar-md bg-soft-info text-info" 
                        onclick="exportAttendance()" 
                        title="Export Data">
                            <i class="feather-download"></i>
                        </a>
                    <a href="{{ route('payroll.attendance.add') }}" class="avatar-text avatar-md bg-primary text-white" title="Add Attendance">
                        <i class="feather-plus"></i>
                    </a>
                </div>
            </div>

            <!-- Collapsible Filter Section -->
            <div class="collapse" id="filterSection">
                <div class="card-body border-bottom bg-light bg-opacity-10 p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-2">Quick Range</label>
                            <select id="quickRange" class="form-select border-0 bg-white py-2 px-3 shadow-sm fw-bold" 
                                    onchange="updateDateRange(this.value)" style="border-radius: 8px; height: 40px;">
                                <option value="">All Time</option>
                                <option value="today" {{ request('range') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ request('range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="week" {{ request('range') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="3months" {{ request('range') == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                                <option value="6months" {{ request('range') == '6months' ? 'selected' : '' }}>Last 6 Months</option>
                                <option value="1year" {{ request('range') == '1year' ? 'selected' : '' }}>Last 1 Year</option>
                                <option value="custom" {{ (request('start_date') && request('range') == 'custom') ? 'selected' : '' }}>Custom Date</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-2">Start Date</label>
                            <input type="date" id="startDate" class="form-control border-0 bg-white py-2 px-3 shadow-sm fw-bold" 
                                   value="{{ request('start_date') }}" style="border-radius: 8px; height: 40px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-2">End Date</label>
                            <input type="date" id="endDate" class="form-control border-0 bg-white py-2 px-3 shadow-sm fw-bold" 
                                   value="{{ request('end_date') }}" style="border-radius: 8px; height: 40px;">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" onclick="applyFilters()" style="background: #3858f9; border: none; height: 40px; border-radius: 8px;">
                                <i class="feather-search"></i> APPLY
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center py-3" role="alert" style="border-radius: 12px; background: #ecfdf5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div class="fw-bold">{{ $message }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
            </div>
        @endif

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background: #ffffff; border-bottom: 1px solid #f1f5f9;">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px; width: 150px;">DATE</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">ATTENDANCE HISTORY</th>
                            <th class="pe-4 py-3 text-muted small fw-bold text-uppercase text-end" style="width: 150px; letter-spacing: 0.5px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendance as $att)
                            <tr class="border-bottom hover-row">
                                <td class="ps-4 py-4 text-dark fw-bold">
                                    {{ \Carbon\Carbon::parse($att->attendance_date)->format('d-m-Y') }}
                                </td>
                                <td>
                                    @php $date = \Carbon\Carbon::parse($att->attendance_date)->format('Y-m-d'); @endphp
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="ref-badge badge-green clickable" title="View Present" onclick="openAttendanceDetails('{{ $date }}', 'present')">
                                            Present: <span class="fw-bold ms-1">{{ $att->present_count }}</span>
                                        </div>
                                        <div class="ref-badge badge-blue clickable" title="View Overtime" onclick="openAttendanceDetails('{{ $date }}', 'overtime')">
                                            Overtime: <span class="fw-bold ms-1">{{ $att->overtime_count }}</span>
                                        </div>
                                        <div class="ref-badge badge-yellow clickable" title="View Half Day" onclick="openAttendanceDetails('{{ $date }}', 'half_day')">
                                            Half Day: <span class="fw-bold ms-1">{{ $att->half_day_count }}</span>
                                        </div>
                                        <div class="ref-badge badge-red clickable" title="View Leave" onclick="openAttendanceDetails('{{ $date }}', 'leave')">
                                            Leave: <span class="fw-bold ms-1">{{ $att->leave_count }}</span>
                                        </div>
                                        <div class="ref-badge badge-purple clickable" title="View Present" onclick="openAttendanceDetails('{{ $date }}', 'wfh')">
                                            WFH: <span class="fw-bold ms-1">{{ $att->wfh_count }}</span>
                                        </div>
                                        <div class="ref-badge badge-purple clickable" title="View Present" onclick="openAttendanceDetails('{{ $date }}', 'early_out')">
                                            Early Out: <span class="fw-bold ms-1">{{ $att->early_count }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('payroll.attendance.editByDate', \Carbon\Carbon::parse($att->attendance_date)->format('Y-m-d')) }} " class="avatar-text avatar-md bg-soft-primary text-primary">
                                           <i class="feather-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary" onclick="openAttendanceDetails('{{ $date }}')" title="View">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger" onclick="deleteAttendanceByDate('{{ $date }}')" title="Delete">
                                            <i class="feather-trash-2"></i>
                                        </a>
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
            @if($attendance->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $attendance->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="offcanvas offcanvas-end" tabindex="-1" id="attendanceDetailOffcanvas" style="width: 900px;">
    <div class="offcanvas-header border-bottom px-4 py-3 bg-white shadow-sm">
        <div class="d-flex flex-column">
            <h5 class="offcanvas-title fw-bold" style="color: #334155;">Record for <span id="offcanvasDate" class="text-primary"></span></h5>
            <div id="statusIndicator" class="small fw-bold text-muted mt-1">Showing All Records</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-link text-decoration-none fw-bold small p-0 me-3" id="showAllBtn" style="display:none;" onclick="resetModalFilter()">Show All</button>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
        </div>
    </div>
    <div class="offcanvas-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 small fw-bold text-muted text-uppercase" style="width: 80px;">SR. NO.</th>
                        <th class="py-3 small fw-bold text-muted text-uppercase">EMPLOYEE NAME</th>
                        <th class="py-3 small fw-bold text-muted text-uppercase text-center">CHECK IN</th>
                        <th class="py-3 small fw-bold text-muted text-uppercase text-center">CHECK OUT</th>
                        <th class="py-3 small fw-bold text-muted text-uppercase text-center">Working Hrs</th>
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
    function updateDateRange(range) {
        const startInput = document.getElementById('startDate');
        const endInput = document.getElementById('endDate');
        const today = new Date();
        let start = new Date();
        let end = new Date();

        switch (range) {
            case 'today':
                // already set to today
                break;
            case 'yesterday':
                start.setDate(today.getDate() - 1);
                end.setDate(today.getDate() - 1);
                break;
            case 'week':
                start.setDate(today.getDate() - today.getDay());
                break;
            case 'month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case '3months':
                start.setMonth(today.getMonth() - 2);
                start.setDate(1);
                break;
            case '6months':
                start.setMonth(today.getMonth() - 5);
                start.setDate(1);
                break;
            case '1year':
                start.setFullYear(today.getFullYear() - 1);
                start.setMonth(today.getMonth() + 1);
                start.setDate(1);
                break;
            case 'custom':
                return; // don't change inputs
        }

        startInput.value = start.toISOString().split('T')[0];
        endInput.value = end.toISOString().split('T')[0];
    }

    // If user manually changes date, switch range to custom
    document.addEventListener('DOMContentLoaded', function() {
        const startInput = document.getElementById('startDate');
        const endInput = document.getElementById('endDate');
        const quickRange = document.getElementById('quickRange');

        [startInput, endInput].forEach(el => {
            el.addEventListener('change', () => {
                quickRange.value = 'custom';
            });
        });

        // Initialize today if no range/date is set
        if (!quickRange.value || (quickRange.value === 'today' && !startInput.value)) {
            updateDateRange('today');
        }
    });

    function applyFilters() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const range = document.getElementById('quickRange').value;
        
        let url = new URL(window.location.href);
        if(start) url.searchParams.set('start_date', start);
        if(end) url.searchParams.set('end_date', end);
        if(range) url.searchParams.set('range', range);
        window.location.href = url.toString();
    }

    let lastFetchedData = null;
    let lastDate = null;

    function resetModalFilter() {
        if(lastFetchedData) renderTable(lastFetchedData, null);
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
            if (filterStatus === 'present' && item.status === 'present') match = true;
            if (filterStatus === 'half_day' && item.status === 'half_day') match = true;
            if (filterStatus === 'leave' && (item.status === 'leave' || item.status === 'absent')) match = true;
            if (filterStatus === 'late' && item.status === 'late') match = true;
            if (filterStatus === 'overtime' && item.total_hours > 9.30) match = true;
            if (filterStatus === 'wfh' && item.status === 'wfh') match = true;
            if (filterStatus === 'early_out' && item.check_out && item.employee?.time_out) {
                const shiftEnd = new Date(`1970-01-01T${item.employee.time_out}`);
                shiftEnd.setMinutes(shiftEnd.getMinutes() - 30);

                const checkOut = new Date(`1970-01-01T${item.check_out}`);

                if (checkOut <= shiftEnd) {
                    match = true;
                }
            }

            if (match) {
                count++;
                body.innerHTML += `
                    <tr class="border-bottom">
                        <td class="ps-4 py-3 text-muted fw-bold">${index + 1}</td>
                        <td class="fw-bold text-dark">${item.employee.name}</td>
                        <td class="text-center">${item.check_in ? formatTime(item.check_in) : '--'}</td>
                        <td class="text-center">${item.check_out ? formatTime(item.check_out) : '--'}</td>
                       <td class="text-center">${formatHours(item.total_hours)}</td>
                        <td class="text-center">
                            <span class="status-badge ${getStatusBadge(item.status)}">${item.status}</span>
                        </td>
                        <td class="pe-4 text-center d-flex">
                            <button class="btn btn-sm text-danger shadow-none" onclick="deleteSingleAttendance(${item.id}, '${lastDate}')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <button class="btn btn-sm text-danger shadow-none" onclick="editSingleAttendance(${item.id}, '${lastDate}')">
                                <i class="feather-edit"></i>
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

    function formatHours(decimalHours) {
        if (!decimalHours) return '--';

        let hours = Math.floor(decimalHours);
        let minutes = Math.round((decimalHours - hours) * 60);

        // handle edge case (60 minutes)
        if (minutes === 60) {
            hours += 1;
            minutes = 0;
        }

        return `${hours}h ${minutes.toString().padStart(2, '0')}m`;
    }


    function formatTime(time) {
        if(!time) return '--';
        let [h, m] = time.split(':');
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return `${h}:${m} ${ampm}`;
    }

    function getStatusBadge(status) {
        switch(status.toLowerCase()) {
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
                if(data.success) window.location.reload();
            });
        }
    }

    function deleteSingleAttendance(id, date) {
        if (confirm('Delete this record?')) {
            fetch(`/payroll/attendance/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => res.json()).then(data => {
                if(data.success) openAttendanceDetails(date);
            });
        }
    }

    function editSingleAttendance(id) {
        window.location.href = `/payroll/attendance/${id}/edit`;
    }

    function exportAttendance() {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;

        if (!start || !end) {
            alert('Please select both dates');
            return;
        }

        window.location.href = "{{ route('payroll.attendance.export') }}" 
            + "?start_date=" + start 
            + "&end_date=" + end;
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    
    .breadcrumb-item + .breadcrumb-item::before { content: ">"; color: #94a3b8; }
    
    .hover-row:hover { background-color: #fbfcfe; }

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
    .clickable { cursor: pointer; transition: transform 0.1s; }
    .clickable:hover { transform: scale(1.05); }
    .badge-green { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .badge-blue { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .badge-yellow { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
    .badge-red { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .status-badge-success { background: #ecfdf5; color: #059669; }
    .status-badge-danger { background: #fef2f2; color: #dc2626; }
    .status-badge-warning { background: #fffbeb; color: #d97706; }
    .status-badge-info { background: #eff6ff; color: #2563eb; }

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

    .badge-purple {
        background: #f3e8ff;
        color: #7c3aed;
        border: 1px solid #ddd6fe;
    }
</style>
@endpush