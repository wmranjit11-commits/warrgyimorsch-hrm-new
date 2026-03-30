@extends('layouts.app')

@section('content')
<div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Top Header matching Saral ERP -->
    <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white border-bottom shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <h5 class="fw-bold mb-0 me-3" style="color: #334155;">Payroll Module</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted small">Home</a></li>
                    <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;" aria-current="page">Add Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="px-4">
        <form action="{{ route('payroll.attendance.store') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white; overflow: hidden;">
                <div class="card-header bg-white border-0 px-4 py-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Add Attendance</h5>
                    <div class="d-flex align-items-center gap-3">
                        <label class="small fw-bold text-muted mb-0">Attendance Date:</label>
                        <div class="input-group" style="width: 220px;">
                            <span class="input-group-text bg-light border-0" style="border-radius: 8px 0 0 8px;"><i class="bi bi-calendar3 text-muted"></i></span>
                            <input type="date" name="attendance_date" class="form-control border-0 bg-light fw-bold small p-2 shadow-none" 
                                   value="{{ date('Y-m-d') }}" required style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead style="background: #ffffff; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="width: 25%; letter-spacing: 0.5px;">EMPLOYEE NAME</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="width: 20%; letter-spacing: 0.5px;">CHECK IN</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="width: 20%; letter-spacing: 0.5px;">CHECK OUT</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="width: 15%; letter-spacing: 0.5px;">TOTAL TIME</th>
                                <th class="pe-4 py-3 text-muted small fw-bold text-uppercase" style="width: 20%; letter-spacing: 0.5px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $index => $emp)
                            <tr class="border-bottom hover-row">
                                <td class="ps-4 py-4">
                                    <div class="fw-bold text-dark fs-6">{{ $emp->name }}</div>
                                    <input type="hidden" name="employees[{{ $index }}][employee_id]" value="{{ $emp->id }}">
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="form-check form-switch p-0 m-0">
                                            <input class="form-check-input check-toggle shadow-none" type="checkbox" 
                                                   id="toggle_in_{{ $index }}" 
                                                   onchange="toggleInput('in', {{ $index }}, this)"
                                                   style="float: none;">
                                        </div>
                                        <div class="input-wrapper">
                                            <input type="time" name="employees[{{ $index }}][check_in]" 
                                                   id="input_in_{{ $index }}"
                                                   class="form-control time-input shadow-none" 
                                                   onchange="calculateDuration({{ $index }})">
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="form-check form-switch p-0 m-0">
                                            <input class="form-check-input check-toggle shadow-none" type="checkbox" 
                                                   id="toggle_out_{{ $index }}" 
                                                   onchange="toggleInput('out', {{ $index }}, this)"
                                                   style="float: none;" disabled>
                                        </div>
                                        <div class="input-wrapper">
                                            <input type="time" name="employees[{{ $index }}][check_out]" 
                                                   id="input_out_{{ $index }}"
                                                   class="form-control time-input shadow-none" 
                                                   onchange="calculateDuration({{ $index }})">
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-muted small" id="duration_{{ $index }}">--</span>
                                </td>
                                <td class="pe-4">
                                    <select name="employees[{{ $index }}][status]" class="form-select border-0 bg-light fw-bold small py-2 shadow-none" style="border-radius: 8px; cursor: pointer;">
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="half_day">Half Day</option>
                                        <option value="leave">Leave</option>
                                        <option value="late">Late</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white border-0 p-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="background: #3858f9; border: none; border-radius: 8px;">
                        SAVE ATTENDANCE
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleInput(type, index, toggle) {
        const input = document.getElementById(`input_${type}_${index}`);
        const outToggle = document.getElementById(`toggle_out_${index}`);
        
        if (toggle.checked) {
            // Automatically capture current time
            const now = new Date();
            input.value = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            
            // Visual Active State
            input.classList.add('active');
            
            // Lock inputs (Immutable)
            input.readOnly = true;
            input.style.pointerEvents = 'none';
            input.tabIndex = -1;
            
            // Lock toggle (Immutable)
            toggle.style.pointerEvents = 'none';
            toggle.tabIndex = -1;

            if (type === 'in' && outToggle) {
                outToggle.disabled = false;
            }
        }
        calculateDuration(index);
    }

    function calculateDuration(index) {
        const inTime = document.getElementById(`input_in_${index}`).value;
        const outTime = document.getElementById(`input_out_${index}`).value;
        const display = document.getElementById(`duration_${index}`);

        if (inTime && outTime) {
            const start = new Date(`2000-01-01T${inTime}:00`);
            let end = new Date(`2000-01-01T${outTime}:00`);
            
            if (end < start) end = new Date(`2000-01-02T${outTime}:00`);
            
            const diff = (end - start) / 1000 / 60 / 60; // hours
            display.innerText = diff.toFixed(1) + ' hrs';
            display.classList.remove('text-muted');
            display.classList.add('text-primary');
        } else {
            display.innerText = '--';
            display.classList.add('text-muted');
            display.classList.remove('text-primary');
        }
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    
    .breadcrumb-item + .breadcrumb-item::before { content: ">"; color: #94a3b8; }
    
    .time-input {
        background: #f1f5f9 !important;
        border: none !important;
        border-radius: 8px !important;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        color: #64748b;
        padding: 8px;
        width: 140px;
        transition: all 0.2s;
    }
    
    .time-input.active {
        background: #ffffff !important;
        color: #1e293b;
        box-shadow: 0 0 0 1px #e2e8f0, 0 2px 4px rgba(0,0,0,0.05) !important;
    }
    
    .check-toggle:checked {
        background-color: #3858f9 !important;
        border-color: #3858f9 !important;
        box-shadow: 0 0 8px rgba(56, 88, 249, 0.4) !important;
    }
    
    .hover-row:hover { background-color: #fbfcfe; }
    
    /* Modern Switch Styling with Visible Thumb */
    .form-switch .form-check-input {
        width: 46px !important;
        height: 24px !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3.5' fill='rgba%280, 0, 0, 0.2%29'/%3e%3c/svg%3e") !important;
        background-position: left 2px center !important;
        background-size: 18px !important;
        cursor: pointer;
        transition: background-position 0.15s ease-in-out, background-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        background-repeat: no-repeat;
        border: 1px solid #cbd5e1 !important;
    }
    .form-switch .form-check-input:checked,
    .form-switch .form-check-input[checked] {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3.5' fill='%23ffffff'/%3e%3c/svg%3e") !important;
        background-color: #3858f9 !important;
        border-color: #3858f9 !important;
        background-position: right 2px center !important;
        box-shadow: 0 0 10px rgba(56, 88, 249, 0.3) !important;
    }
    .form-switch .form-check-input:focus {
        border-color: #3858f9 !important;
        box-shadow: 0 0 0 0.25rem rgba(56, 88, 249, 0.1) !important;
    }
    .form-switch .form-check-input:disabled, 
    .form-switch .form-check-input[style*="pointer-events: none"] {
        opacity: 0.8 !important;
        cursor: not-allowed;
    }
</style>
@endsection

