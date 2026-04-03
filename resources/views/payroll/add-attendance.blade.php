@extends('layouts.app')

@section('content')
<div class="page-header d-flex align-items-center">
    <div class="page-header-left">
        <div class="page-header-title">
            <h5 class="m-b-10">Payroll Module</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item">Add Attendance</li>
        </ul>
    </div>

    <div class="dropdown d-inline-block ms-auto float-end">
    <a href="#" class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" aria-expanded="false" title="Import Attendance">
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
</div>
<div style="padding: 30px;"> 
    {{-- <h2 style="margin-bottom: 20px; color: #333;">Add Attendance</h2> --}}

    {{-- <div style="background: #f8f9fa; padding: 20px; margin-bottom: 20px; border-radius: 8px; border: 1px dashed #3858f9;">
    <form action="{{ route('payroll.attendance.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display: flex; align-items: center; gap: 15px;">
            <label style="font-weight: bold; color: #333;">Import Attendance (Excel):</label>
            <input type="file" name="import_file" accept=".xlsx, .xls, .csv" required>
            <button type="submit" style="background: #28a745; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                UPLOAD & CALCULATE
            </button>
        </div>
        <small style="color: #666; display: block; margin-top: 5px;">
            Columns needed: <strong>employee_id, date, check_in, check_out, status</strong>
        </small>
    </form>
</div> --}}
    
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <form action="{{ route('payroll.attendance.store') }}" method="POST">
                    @csrf
                    <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Daily Attendance Marking</h5>
                            <p class="text-muted small mb-0">Record check-in and check-out times for employees</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div id="dateOffBadge" class="badge bg-soft-primary text-primary px-3 py-2 fw-bold" style="display:none; border-radius:8px;">
                                <i class="bi bi-calendar-x-fill me-1"></i> <span id="dateOffText">OFF-DAY</span>
                            </div>
                            <label class="fw-bold small text-muted text-uppercase mb-0">Date:</label>
                            <input type="date" name="attendance_date" id="attendance_date_input" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required 
                                   class="form-control border-0 bg-light shadow-none fw-bold" 
                                   style="border-radius: 12px; width: 180px; height: 45px; padding-left: 15px; cursor: pointer;"
                                   onclick="this.showPicker()"
                                   onchange="checkIfOffDay()">
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if(count($employees) == 0)
                            <div class="p-5 text-center">
                                <div class="mb-3 text-warning">
                                    <i class="bi bi-exclamation-circle fs-1"></i>
                                </div>
                                <h5 class="fw-bold">No Employees Found</h5>
                                <p class="text-muted">Please add employees first before marking attendance.</p>
                                <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="bi bi-person-plus me-2"></i> Add Employee
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                                    <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 border-0 text-muted small text-uppercase fw-bold" style="width: 250px;">Employee</th>
                                                <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">In Time (Check-In)</th>
                                                <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Out Time (Check-Out)</th>
                                                <th class="py-3 border-0 text-muted small text-uppercase fw-bold text-center">Duration</th>
                                                <th class="pe-4 py-3 border-0 text-muted small text-uppercase fw-bold text-center">Status</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employees as $index => $emp)
                                        <input type="hidden" name="employees[{{ $index }}][employee_id]" value="{{ $emp->id }}">
                                        <tr class="border-bottom hover-row" style="height: 100px;">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle fw-bold" style="width: 42px; height: 42px; font-size: 14px;">
                                                            {{ strtoupper(substr($emp->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <div class="fw-bold text-dark" style="font-size: 14px;">{{ $emp->name }}</div>
                                                        <div class="text-muted small">EC{{ str_pad($emp->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <input type="time" id="check_in_input_{{ $index }}" name="employees[{{ $index }}][check_in]" 
                                                           class="form-control form-control-sm text-center fw-bold mb-2 p-1 shadow-none border-0 bg-light" 
                                                           style="height: 32px; width: 100px; font-size: 13px; border-radius: 8px; cursor: pointer;"
                                                           onchange="manualTimeChange({{ $index }})"
                                                           onclick="this.showPicker()">
                                                    
                                                    <div class="form-check form-switch p-0">
                                                        <input class="form-check-input ms-0" type="checkbox" role="switch" 
                                                               id="check_in_toggle_{{ $index }}" 
                                                               style="width: 45px; height: 22px; cursor: pointer;"
                                                               onchange="toggleCheckIn({{ $index }})">
                                                    </div>
                                                    <label class="small text-muted fw-bold mt-1" style="font-size: 9px; letter-spacing: 0.5px;">CHECK IN</label>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <div class="d-flex flex-column align-items-center">
                                                    <input type="time" id="check_out_input_{{ $index }}" name="employees[{{ $index }}][check_out]" 
                                                           class="form-control form-control-sm text-center fw-bold mb-2 p-1 shadow-none border-0 bg-light" 
                                                           style="height: 32px; width: 100px; font-size: 13px; border-radius: 8px; cursor: pointer;"
                                                           onchange="manualTimeChange({{ $index }})"
                                                           onclick="this.showPicker()">

                                                    <div class="form-check form-switch p-0">
                                                        <input class="form-check-input ms-0" type="checkbox" role="switch" 
                                                               id="check_out_toggle_{{ $index }}" 
                                                               style="width: 45px; height: 22px; cursor: pointer;"
                                                               onchange="toggleCheckOut({{ $index }})">
                                                    </div>
                                                    <label class="small text-muted fw-bold mt-1" style="font-size: 9px; letter-spacing: 0.5px;">CHECK OUT</label>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center align-middle">
                                                <span id="duration_{{ $index }}" class="badge rounded-pill bg-light text-dark fw-bold px-3 py-2" style="font-size: 12px; min-width: 80px;">--</span>
                                            </td>
                                            <td class="pe-4 py-3 text-center align-middle">
                                                <select name="employees[{{ $index }}][status]" id="status_{{ $index }}"
                                                        class="form-select border-0 bg-light fw-bold mx-auto" 
                                                        style="border-radius: 8px; font-size: 12px; width: 130px; height: 40px; box-shadow: none;">
                                                    <option value="absent" selected>Absent</option>
                                                    <option value="present">Present</option>
                                                    <option value="half_day">Half Day</option>
                                                    <option value="leave">Leave</option>
                                                </select>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>

                    @if(count($employees) > 0)
                        <div class="card-footer bg-white border-0 p-4 text-center">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-lg" 
                                    style="background: #3858f9; border: none; border-radius: 12px; transition: all 0.3s; transform: translateY(0);">
                                <i class="bi bi-check2-circle me-2"></i> Post Attendance Data
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background: #eef2ff !important; }
    .hover-row:hover { background-color: #f8faff; }
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 3px rgba(56, 88, 249, 0.1); background: #fff !important; border: 1px solid #3858f9 !important; }
    .form-check-input:checked { background-color: #3858f9; border-color: #3858f9; }
    
    /* Better time input styling for AM/PM */
    input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(34%) sepia(87%) saturate(2658%) hue-rotate(224deg) brightness(98%) contrast(98%);
        cursor: pointer;
    }
    .time-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 10px;
        font-weight: 700;
        color: #3858f9;
        font-size: 12px;
        min-height: 28px;
        min-width: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
</style>

<script>
    function toggleCheckIn(index) {
        const toggle = document.getElementById('check_in_toggle_' + index);
        const input = document.getElementById('check_in_input_' + index);
        
        if (toggle.checked) {
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            input.value = time;
            input.classList.remove('bg-light');
            input.classList.add('bg-white', 'border');
        } else {
            input.value = '';
            input.classList.remove('bg-white', 'border');
            input.classList.add('bg-light');
        }
        syncStatus(index);
        calculateDuration(index);
    }

    function toggleCheckOut(index) {
        const toggle = document.getElementById('check_out_toggle_' + index);
        const input = document.getElementById('check_out_input_' + index);
        
        if (toggle.checked) {
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            input.value = time;
            input.classList.remove('bg-light');
            input.classList.add('bg-white', 'border');
        } else {
            input.value = '';
            input.classList.remove('bg-white', 'border');
            input.classList.add('bg-light');
        }
        syncStatus(index);
        calculateDuration(index);
    }

    function manualTimeChange(index) {
        const inI = document.getElementById('check_in_input_' + index);
        const outI = document.getElementById('check_out_input_' + index);
        const inT = document.getElementById('check_in_toggle_' + index);
        const outT = document.getElementById('check_out_toggle_' + index);

        if (inI.value) {
            inT.checked = true;
            inI.classList.remove('bg-light');
            inI.classList.add('bg-white', 'border');
        }
        if (outI.value) {
            outT.checked = true;
            outI.classList.remove('bg-light');
            outI.classList.add('bg-white', 'border');
        }

        syncStatus(index);
        calculateDuration(index);
    }

    function syncStatus(index) {
        const inT = document.getElementById('check_in_toggle_' + index);
        const outT = document.getElementById('check_out_toggle_' + index);
        const status = document.getElementById('status_' + index);
        
        if (inT.checked || outT.checked) {
            if (status.value === 'absent') status.value = 'present';
        } else {
            status.value = 'absent';
        }
    }

    function calculateDuration(index) {
        const checkIn = document.getElementById('check_in_input_' + index).value;
        const checkOut = document.getElementById('check_out_input_' + index).value;
        const durationDisplay = document.getElementById('duration_' + index);
        
        if (checkIn && checkOut) {
            const [inH, inM] = checkIn.split(':').map(Number);
            const [outH, outM] = checkOut.split(':').map(Number);
            let diff = (outH * 60 + outM) - (inH * 60 + inM);
            if (diff < 0) diff += 24 * 60;
            
            const hours = Math.floor(diff / 60);
            const minutes = diff % 60;
            
            durationDisplay.textContent = hours + 'h ' + minutes + 'm';
            durationDisplay.className = 'badge rounded-pill bg-soft-primary text-primary fw-bold px-3 py-2';
        } else {
            durationDisplay.textContent = '--';
            durationDisplay.className = 'badge rounded-pill bg-light text-dark fw-bold px-3 py-2';
        }
    }
    // Holidays passed from backend
    const holidays = @json($holidays ?? []);

    function checkIfOffDay() {
        const input = document.getElementById('attendance_date_input');
        const badge = document.getElementById('dateOffBadge');
        const text = document.getElementById('dateOffText');
        
        if (!input.value) return;
        
        const date = new Date(input.value);
        const dateStr = input.value; // YYYY-MM-DD
        const isSunday = date.getDay() === 0;
        const holidayTitle = holidays[dateStr];
        
        if (isSunday || holidayTitle) {
            badge.style.display = 'inline-flex';
            badge.classList.remove('bg-soft-primary', 'text-primary', 'bg-soft-danger', 'text-danger');
            
            if (holidayTitle) {
                text.innerText = 'HOLIDAY: ' + holidayTitle;
                badge.classList.add('bg-soft-danger', 'text-danger');
            } else {
                text.innerText = 'WEEKLY OFF (SUNDAY)';
                badge.classList.add('bg-soft-primary', 'text-primary');
            }
        } else {
            badge.style.display = 'none';
        }
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        checkIfOffDay();
    });
</script>
@endsection

