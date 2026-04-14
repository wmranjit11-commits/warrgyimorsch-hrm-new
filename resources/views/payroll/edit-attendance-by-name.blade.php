@extends('layouts.app')

@section('content')

<div class="page-header d-flex align-items-center">
    <div class="page-header-left">
        <div class="page-header-title">
            <h5 class="m-b-10">Payroll Module</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item">Edit Attendance By Employee</li>
        </ul>
    </div>
</div>

<div style="padding: 30px;">

    <!-- Debug Box -->
    <div style="background: #f0f8ff; padding: 10px; margin-bottom: 15px; border-radius: 4px; border-left: 4px solid #3858f9;">
        <small style="color: #0066cc;">
            <strong>Debug:</strong> Total Records Found:
            <strong>{{ count($attendance) }}</strong>
        </small>
    </div>

    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

        <form action="{{ route('payroll.attendance.employee.updateByName', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Employee Name -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">
                    Employee Name:
                </label>

                <input type="text"
                       value="{{ $employee->name }}"
                       readonly
                       style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; width: 300px; font-size: 14px; background: #f5f5f5;">

                <small style="color: #d9534f; margin-left: 10px;">
                    Employee name cannot be changed.
                </small>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 15px; text-align: left; border-right: 1px solid #ddd; font-weight: bold;">
                                Attendance Date
                            </th>
                            <th style="padding: 15px; text-align: center; border-right: 1px solid #ddd; font-weight: bold; width: 140px;">
                                Check In
                            </th>
                            <th style="padding: 15px; text-align: center; border-right: 1px solid #ddd; font-weight: bold; width: 140px;">
                                Check Out
                            </th>
                            <th style="padding: 15px; text-align: center; border-right: 1px solid #ddd; font-weight: bold; width: 110px;">
                                Total Time
                            </th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; width: 130px;">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($attendance as $index => $record)
                        <tr style="border-bottom: 1px solid #ddd; background: {{ $loop->even ? '#fafafa' : 'white' }};">

                            <!-- Date -->
                            <td style="padding: 15px; border-right: 1px solid #ddd; font-weight: 500;">
                                {{ \Carbon\Carbon::parse($record->attendance_date)->format('d-m-Y') }}
                                <input type="hidden" name="attendance_ids[]" value="{{ $record->id }}">
                            </td>

                            <!-- Check In -->
                            <td style="padding: 15px; border-right: 1px solid #ddd; text-align: center;">
                                <div style="margin-bottom: 8px;">
                                    <input type="time"
                                        id="check_in_{{ $index }}"
                                        name="check_in[{{ $record->id }}]"
                                        value="{{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('H:i') : '' }}"
                                        onchange="calculateDuration({{ $index }})">
                                </div>

                                <label style="font-size: 12px; cursor: pointer;">
                                    <input type="checkbox"
                                        onchange="setNowTime('check_in_{{ $index }}', {{ $index }})">
                                    <span>Set Now</span>
                                </label>
                            </td>

                            <!-- Check Out -->
                            <td style="padding: 15px; border-right: 1px solid #ddd; text-align: center;">
                                <div style="margin-bottom: 8px;">
                                    <input type="time"
                                        id="check_out_{{ $index }}"
                                        name="check_out[{{ $record->id }}]"
                                        value="{{ $record->check_in ? \Carbon\Carbon::parse($record->check_out)->format('H:i') : '' }}"
                                        onchange="calculateDuration({{ $index }})">
                                </div>

                                <label style="font-size: 12px; cursor: pointer;">
                                    <input type="checkbox"
                                        onchange="setNowTime('check_out_{{ $index }}', {{ $index }})">
                                    <span>Set Now</span>
                                </label>
                            </td>

                            <!-- Duration -->
                            <td style="padding: 15px; border-right: 1px solid #ddd; text-align: center;">
                                <span id="duration_{{ $index }}" style="font-weight: bold; color: #3858f9;">
                                    --
                                </span>
                            </td>

                            <!-- Status -->
                            <td style="padding: 15px;">
                                <select name="status[{{ $record->id }}]"
                                        id="status_{{ $index }}"
                                        style="padding: 8px; border: 1px solid #ddd; border-radius: 3px; width: 120px; cursor: pointer;">

                                    <option value="present" {{ $record->status == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ $record->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="half_day" {{ $record->status == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                    <option value="wfh" {{ $record->status == 'wfh' ? 'selected' : '' }}>WFH</option>
                                    <option value="leave" {{ $record->status == 'leave' ? 'selected' : '' }}>Leave</option>
                                    <option value="late" {{ $record->status == 'late' ? 'selected' : '' }}>Late</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                <a href="{{ route('payroll.attendace.employee') }}"
                   class="btn btn-outline-secondary w-100 w-sm-auto">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary fw-bold w-100 w-sm-auto px-4">
                    UPDATE ATTENDANCE
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.onload = function () {
        document.querySelectorAll('[id^="check_in_"]').forEach((el, index) => {
            calculateDuration(index);
        });
    };

    function setNowTime(fieldId, index) {
        const field = document.getElementById(fieldId);
        const now = new Date();

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        field.value = hours + ':' + minutes;

        // Enable checkout if check-in is set
        if (fieldId.includes('check_in')) {
            const checkOutField = document.getElementById('check_out_' + index);

            if (checkOutField) {
                checkOutField.disabled = false;
                checkOutField.style.background = 'white';
            }
        }

        calculateDuration(index);
    }

    function calculateDuration(index) {
        const checkInField = document.getElementById('check_in_' + index);
        const checkOutField = document.getElementById('check_out_' + index);
        const durationDisplay = document.getElementById('duration_' + index);

        const checkIn = checkInField.value;
        const checkOut = checkOutField.value;

        if (checkIn && checkOut) {
            const [inHours, inMinutes] = checkIn.split(':').map(Number);
            const [outHours, outMinutes] = checkOut.split(':').map(Number);

            let inTotal = inHours * 60 + inMinutes;
            let outTotal = outHours * 60 + outMinutes;

            // Night shift support
            if (outTotal < inTotal) {
                outTotal += 24 * 60;
            }

            const diffMinutes = outTotal - inTotal;
            const hours = Math.floor(diffMinutes / 60);
            const minutes = diffMinutes % 60;

            durationDisplay.textContent = hours + 'h ' + minutes + 'm';
            durationDisplay.style.color = '#3858f9';
        } else {
            durationDisplay.textContent = '--';
            durationDisplay.style.color = '#999';
        }
    }
</script>

@endsection