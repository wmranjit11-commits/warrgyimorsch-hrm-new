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
            background: rgba(255, 193, 7, 0.15) !important;
            color: #ffc107 !important;
            border: 1px solid rgba(255, 193, 7, 0.3);
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
    <div class="container-fluid px-0" style="min-height: 100vh; font-family: 'Inter', sans-serif;">
        <!-- Main Content Card -->
        <div class="px-4 pt-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    <div>
                        <h5 class="fw-bold mb-0">Attendance Management</h5>
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
                                placeholder="Search..." onkeydown="if(event.key==='Enter') applyFilters()">
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
                <div id="filterSection" class="collapse border-bottom bg-body-tertiary">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Select Month</label>
                                <input type="month" id="monthFilter" class="form-control border-0 bg-body-secondary px-3 fw-bold shadow-none"
                                    value="{{ request('month', date('Y-m')) }}" style="font-size: 13px; height: 38px; padding-top: 0; padding-bottom: 0; line-height: 1.5; border-radius: 8px; cursor: pointer;"
                                    onclick="this.showPicker()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">From Date</label>
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; height: 38px;">
                                    <input type="date" id="startDate" class="form-control border-0 bg-body-secondary px-3 fw-bold shadow-none"
                                        value="{{ $start_date }}" style="font-size: 13px; height: 38px; cursor: pointer;"
                                        onclick="this.showPicker()">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">To Date</label>
                                <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; height: 38px;">
                                    <input type="date" id="endDate" class="form-control border-0 bg-body-secondary px-3 fw-bold shadow-none"
                                        value="{{ $end_date }}" style="font-size: 13px; height: 38px; cursor: pointer;"
                                        onclick="this.showPicker()">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button
                                    class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                    onclick="applyFilters()"
                                    style="background: #3858f9; border: none; height: 38px; border-radius: 8px; font-size: 13px;">
                                    <i class="feather-search"></i> FILTER
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('payroll.attendance') }}"
                                    class="btn btn-outline-secondary w-100 fw-bold d-flex align-items-center justify-content-center gap-2"
                                    style="height: 38px; border-radius: 8px; border-style: dashed; font-size: 13px;">
                                    <i class="feather-refresh-cw"></i> RESET
                                </a>
                            </div>
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
                                            {{ \Carbon\Carbon::parse($att->attendance_date)->format('l') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($att->is_holiday)
                                                <div class="badge-att bg-soft-primary" title="Official Holiday">
                                                    <i class="bi bi-star-fill me-2"></i>
                                                    <span class="fw-bold small">HOLIDAY: {{ $att->holiday_title }}</span>
                                                </div>
                                            @elseif(isset($att->is_sunday) && $att->is_sunday)
                                                <div class="badge-att bg-soft-primary" title="Weekly Off">
                                                    <i class="bi bi-sun-fill me-2"></i>
                                                    <span class="fw-bold small">SUNDAY</span>
                                                </div>
                                            @endif

                                            @if($att->count > 0)
                                                <div class="badge-att bg-soft-success clickable"
                                                    title="Click to view Present employees"
                                                    onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'present')">
                                                    <i class="bi bi-person-check-fill me-2"></i>
                                                    <span class="small fw-bold">{{ $att->present }} Present</span>
                                                </div>
                                                <div class="badge-att bg-soft-warning clickable"
                                                    title="Click to view Half Day records"
                                                    onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'half_day')">
                                                    <i class="bi bi-clock-fill me-2"></i>
                                                    <span class="small fw-bold">{{ $att->half_day }} Half Day</span>
                                                </div>
                                                <div class="badge-att bg-soft-dark clickable"
                                                    title="Click to view Overtime performance"
                                                    onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'overtime')">
                                                    <i class="bi bi-alarm-fill me-2"></i>
                                                    <span class="small fw-bold">{{ $att->overtime }} Overtime</span>
                                                </div>
                                                <div class="badge-att bg-soft-info clickable" title="Click to view Leave records"
                                                    onclick="openAttendanceDetails('{{ $att->attendance_date }}', 'leave')">
                                                    <i class="bi bi-calendar-event-fill me-2"></i>
                                                    <span class="small fw-bold">{{ $att->leave }} Leave</span>
                                                </div>
                                                <div class="badge-att bg-soft-danger clickable"
                                                    title="Click to view Absent employees"
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
                                                onclick="openAttendanceDetails('{{ $att->attendance_date }}')"
                                                title="View Details">
                                                <i class="feather-eye"></i>
                                            </a>
                                            @if($att->count > 0)
                                                <a href="javascript:void(0);"
                                                    class="avatar-text avatar-md bg-soft-danger text-danger"
                                                    onclick="deleteAttendanceByDate('{{ $att->attendance_date }}')"
                                                    title="Delete All">
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
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Check-In</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Check-Out</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Duration</th>
                            <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Status</th>
                            <th class="pe-4 py-3 small fw-bold text-muted text-uppercase text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="offcanvasTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-bold" style="color: #334155;">Edit Punch Times</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" id="edit_att_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold mb-1">Check-In Time</label>
                            <input type="time" id="edit_check_in" class="form-control shadow-none border-0 py-2"
                                style="background: #f8fafc; border-radius: 10px; color: #1e293b; font-weight: 600; cursor: pointer;"
                                onclick="this.showPicker()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold mb-1">Check-Out Time</label>
                            <input type="time" id="edit_check_out" class="form-control shadow-none border-0 py-2"
                                style="background: #f8fafc; border-radius: 10px; color: #1e293b; font-weight: 600; cursor: pointer;"
                                onclick="this.showPicker()">
                        </div>
                    </div>
                    <div class="mt-4 p-3 rounded-4" style="background: #f0f4ff; border: 1px dashed #3858f9;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            <div class="small text-primary fw-bold">Note: Total hours will be automatically recalculated.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" class="btn btn-light px-4 py-2 border-0 fw-bold"
                        style="border-radius: 10px; background: #f1f5f9; color: #64748b;"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white px-4 py-2 border-0 fw-bold"
                        style="border-radius: 10px; background: #3858f9;" onclick="saveAttendanceEdit()">Save
                        Improvements</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>


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
                        const dateObj = new Date(date);
                        const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                        document.getElementById('offcanvasDate').innerText = `${date} (${dayName})`;
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
                                    <span class="badge rounded-pill bg-light text-dark fw-bold" style="font-size: 11px;">
                                        ${calculateHours(item.check_in, item.check_out, item.total_hours)}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge ${getStatusBadge(item.status)}">${item.status}</span>
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="javascript:void(0);" 
                                           class="avatar-text avatar-sm bg-soft-primary text-primary" 
                                           onclick="openEditModal(${item.id}, '${item.check_in || ''}', '${item.check_out || ''}')"
                                           title="Edit Record">
                                            <i class="feather-edit-3" style="font-size: 12px;"></i>
                                        </a>
                                        <a href="javascript:void(0);" 
                                           class="avatar-text avatar-sm bg-soft-danger text-danger" 
                                           onclick="deleteSingleAttendance(${item.id}, '${lastDate}')"
                                           title="Delete Record">
                                            <i class="feather-trash-2" style="font-size: 12px;"></i>
                                        </a>
                                    </div>
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

        function calculateHours(checkIn, checkOut, savedHours) {
            if (!checkIn || !checkOut) return '--';
            try {
                const [inH, inM] = checkIn.split(':').map(Number);
                const [outH, outM] = checkOut.split(':').map(Number);

                let inDate = new Date(2000, 0, 1, inH, inM);
                let outDate = new Date(2000, 0, 1, outH, outM);

                if (outDate < inDate) outDate.setDate(outDate.getDate() + 1);

                const diffMs = outDate - inDate;
                return (diffMs / (1000 * 60 * 60)).toFixed(2) + ' hrs';
            } catch (e) {
                return savedHours ? savedHours + ' hrs' : '--';
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
            if (confirm('Are you sure you want to delete this specific attendance record?')) {
                fetch(`/payroll/attendance/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            openAttendanceDetails(date); // Refresh list
                        }
                    });
            }
        }

        // --- Individual Edit Functions ---
        function openEditModal(id, checkIn, checkOut) {
            document.getElementById('edit_att_id').value = id;
            document.getElementById('edit_check_in').value = checkIn || '';
            document.getElementById('edit_check_out').value = checkOut || '';

            const editModal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
            editModal.show();
        }

        function saveAttendanceEdit() {
            const id = document.getElementById('edit_att_id').value;
            const checkIn = document.getElementById('edit_check_in').value;
            const checkOut = document.getElementById('edit_check_out').value;

            fetch(`/payroll/attendance/${id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    check_in: checkIn,
                    check_out: checkOut
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('editAttendanceModal')).hide();
                        if (lastDate) openAttendanceDetails(lastDate); // Refresh the offcanvas list

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success: Attendance updated successfully!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: err.message
                    });
                });
        }

        function exportAttendance() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            window.location.href = '{{ route("payroll.attendance.export") }}?start_date=' + start + '&end_date=' + end;
        }

        function exportMonthlySheet() {
            const start = document.getElementById('startDate').value;
            if (!start) {
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
            background: rgba(34, 197, 94, 0.12) !important;
            color: #16a34a !important;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .status-badge-danger {
            background: rgba(239, 68, 68, 0.12) !important;
            color: #dc2626 !important;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .status-badge-warning {
            background: rgba(245, 158, 11, 0.12) !important;
            color: #d97706 !important;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .status-badge-info {
            background: rgba(56, 88, 249, 0.12) !important;
            color: #3858f9 !important;
            border: 1px solid rgba(56, 88, 249, 0.2);
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