@extends('layouts.app')
@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Dashboard</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Attendance History</li>
            </ul>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->

    <div class="container-fluid mt-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-0">
                    <i class="feather-calendar me-2"></i>Attendance History
                </h4>
                <small class="text-muted">
                    Record for {{ $employee->name }}
                </small>
            </div>

            <!-- Month Filter -->
            <form method="GET">
                <input type="month" name="month"
                    value="{{ $selectedMonth }}"
                    class="form-control shadow-sm"
                    onchange="this.form.submit()">
            </form>
        </div>

        <!-- Summary Cards -->
        <!-- <div class="row mb-4">
            @php
                $present = collect($history)->where('status', 'Present')->count();
                $absent = collect($history)->where('status', 'Absent')->count();
                $leave = collect($history)->filter(fn($h) => str_contains($h['status'], 'Leave'))->count();
            @endphp

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6>Present Days</h6>
                        <h3>{{ $present }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body">
                        <h6>Absent Days</h6>
                        <h3>{{ $absent }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <h6>Leave Days</h6>
                        <h3>{{ $leave }}</h3>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table align-middle table-hover mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted fw-bold text-uppercase" style="font-size: 12px;">
                                <th class="ps-4">#</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($history as $index => $item)

                                @php
                                    $icon = 'feather-check-circle text-success';

                                    if (str_contains($item['status'], 'Absent')) {
                                        $icon = 'feather-x-circle text-danger';
                                    } elseif (str_contains($item['status'], 'Leave')) {
                                        $icon = 'feather-info text-info';
                                    } elseif (str_contains($item['status'], 'Half')) {
                                        $icon = 'feather-clock text-warning';
                                    } elseif (str_contains($item['status'], 'Sunday')) {
                                        $icon = 'feather-calendar text-muted';
                                    }
                                @endphp

                                <tr style="height: 60px;">
                                    <td class="ps-4 fw-bold text-muted">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="fw-semibold text-dark">
                                        {{ $item['date'] }}
                                    </td>

                                    <td>{{ $item['punch_in'] }}</td>
                                    <td>{{ $item['punch_out'] }}</td>

                                    <td class="fw-bold text-primary">
                                        {{ $item['total_hours'] }}
                                    </td>

                                    <td class="text-center">
                                        <span class="badge px-3 py-2 rounded-pill d-inline-flex align-items-center text-black">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="feather-info fs-1 text-muted mb-3 d-block"></i>
                                        <h5>No Records Found</h5>
                                        <p class="text-muted">
                                            No attendance records for selected month.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>
    <!-- [ Main Content ] end -->
@endsection