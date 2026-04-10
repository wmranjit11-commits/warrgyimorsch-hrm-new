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
                    <li class="breadcrumb-item">Dashboard</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <div id="reportrange" class="reportrange-picker d-flex align-items-center">
                            <span class="reportrange-picker-field"></span>
                        </div>
                        <div class="dropdown filter-dropdown">
                            <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10"
                                data-bs-auto-close="outside">
                                <i class="feather-filter me-2"></i>
                                <span>Filter</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="Role" checked="checked" />
                                        <label class="custom-control-label c-pointer" for="Role">Role</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="Team" checked="checked" />
                                        <label class="custom-control-label c-pointer" for="Team">Team</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="Email" checked="checked" />
                                        <label class="custom-control-label c-pointer" for="Email">Email</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="Member" checked="checked" />
                                        <label class="custom-control-label c-pointer" for="Member">Member</label>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="Recommendation"
                                            checked="checked" />
                                        <label class="custom-control-label c-pointer"
                                            for="Recommendation">Recommendation</label>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-plus me-3"></i>
                                    <span>Create New</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-filter me-3"></i>
                                    <span>Manage Filter</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->
        <!-- [ Main Content ] start -->
        <div class="main-content pt-4">
            <div class="row">
                <!-- [Invoices Awaiting Payment] start -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-gray-200">
                                        <i class="feather-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">₹{{ number_format($totalPaidAmount, 0) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Paid in {{ $selectedMonthLabel }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                        data-bs-offset="0, 10">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <div class="dropdown-header text-uppercase fs-10 fw-800 text-muted">Select History</div>
                                        @for ($i = 0; $i < 6; $i++)
                                            @php $m = \Carbon\Carbon::now()->startOfMonth()->subMonths($i); @endphp
                                            <a href="{{ route('dashboard', ['month' => $m->format('Y-m')]) }}"
                                                class="dropdown-item {{ $selectedMonth == $m->format('Y-m') ? 'active' : '' }}">
                                                <i class="feather-calendar me-2"></i>
                                                <span>{{ $m->format('M Y') }} Overview</span>
                                            </a>
                                        @endfor
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            onclick="showMonthlySummary('{{ $selectedMonth }}')">
                                            <i class="feather-file-text me-2"></i>
                                            <span>Full Breakdown Details</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);"
                                        class="fs-12 fw-medium text-muted text-truncate-1-line">{{ $totalEmpPaid }} Employees
                                        Paid </a>
                                    <div class="w-100 text-end">
                                        <span class="fs-12 text-dark">₹{{ number_format($totalNetSalary, 0) }}</span>
                                        <span
                                            class="fs-11 text-muted">({{ $totalNetSalary > 0 ? round(($totalPaidAmount / $totalNetSalary) * 100) : 0 }}%)</span>
                                    </div>
                                </div>
                                <div class="progress mt-2 ht-3">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ $totalNetSalary > 0 ? ($totalPaidAmount / $totalNetSalary) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [Invoices Awaiting Payment] end -->
                <!-- [Pending Amount] start -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-gray-200">
                                        <i class="feather-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">₹{{ number_format($totalPendingAmount, 0) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Pending in {{ $selectedMonthLabel }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                        data-bs-offset="0, 10">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <div class="dropdown-header text-uppercase fs-10 fw-800 text-muted">Select History</div>
                                        @for ($i = 0; $i < 6; $i++)
                                            @php $m = \Carbon\Carbon::now()->startOfMonth()->subMonths($i); @endphp
                                            <a href="{{ route('dashboard', ['month' => $m->format('Y-m')]) }}"
                                                class="dropdown-item {{ $selectedMonth == $m->format('Y-m') ? 'active' : '' }}">
                                                <i class="feather-calendar me-2"></i>
                                                <span>{{ $m->format('M Y') }} Overview</span>
                                            </a>
                                        @endfor
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            onclick="showMonthlySummary('{{ $selectedMonth }}')">
                                            <i class="feather-file-text me-2"></i>
                                            <span>Full Breakdown Details</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);"
                                        class="fs-12 fw-medium text-muted text-truncate-1-line">Awaiting Payment </a>
                                    <div class="w-100 text-end">
                                        <span class="fs-12 text-dark">{{ $totalEmpPending }} Employees</span>
                                        <span
                                            class="fs-11 text-muted">({{ $totalNetSalary > 0 ? round(($totalPendingAmount / $totalNetSalary) * 100) : 0 }}%)</span>
                                    </div>
                                </div>
                                <div class="progress mt-2 ht-3">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $totalNetSalary > 0 ? ($totalPendingAmount / $totalNetSalary) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [Pending Amount] end -->
                <!-- [Rejected Amount] start -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-gray-200">
                                        <i class="feather-x-circle text-danger"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">₹{{ number_format($totalRejectedAmount, 0) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Rejected in {{ $selectedMonthLabel }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                        data-bs-offset="0, 10">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <div class="dropdown-header text-uppercase fs-10 fw-800 text-muted">Select History</div>
                                        @for ($i = 0; $i < 6; $i++)
                                            @php $m = \Carbon\Carbon::now()->startOfMonth()->subMonths($i); @endphp
                                            <a href="{{ route('dashboard', ['month' => $m->format('Y-m')]) }}"
                                                class="dropdown-item {{ $selectedMonth == $m->format('Y-m') ? 'active' : '' }}">
                                                <i class="feather-calendar me-2"></i>
                                                <span>{{ $m->format('M Y') }} Overview</span>
                                            </a>
                                        @endfor
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            onclick="showMonthlySummary('{{ $selectedMonth }}')">
                                            <i class="feather-file-text me-2"></i>
                                            <span>Full Breakdown Details</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);"
                                        class="fs-12 fw-medium text-muted text-truncate-1-line">Payment Failed </a>
                                    <div class="w-100 text-end">
                                        <span class="fs-12 text-dark">Rejected</span>
                                        <span
                                            class="fs-11 text-muted">({{ $totalNetSalary > 0 ? round(($totalRejectedAmount / $totalNetSalary) * 100) : 0 }}%)</span>
                                    </div>
                                </div>
                                <div class="progress mt-2 ht-3">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: {{ $totalNetSalary > 0 ? ($totalRejectedAmount / $totalNetSalary) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [Rejected Amount] end -->
                <!-- [Total Employees] start -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-gray-200">
                                        <i class="feather-users text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ $totalEmployees }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">TOTAL STAFF</h3>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                        data-bs-offset="0, 10">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <div class="dropdown-header text-uppercase fs-10 fw-800 text-muted">Quick Access</div>
                                        <a href="{{ route('dashboard') }}" class="dropdown-item">
                                            <i class="feather-refresh-cw me-2"></i>
                                            <span>Show All-Time History</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        @for ($i = 0; $i < 6; $i++)
                                            @php $m = \Carbon\Carbon::now()->startOfMonth()->subMonths($i); @endphp
                                            <a href="{{ route('dashboard', ['month' => $m->format('Y-m')]) }}"
                                                class="dropdown-item {{ $selectedMonth == $m->format('Y-m') ? 'active' : '' }}">
                                                <i class="feather-calendar me-2"></i>
                                                <span>{{ $m->format('M Y') }} Overview</span>
                                            </a>
                                        @endfor
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            onclick="showFullYearBreakdown('{{ date('Y') }}')">
                                            <i class="feather-file-text me-2"></i>
                                            <span>Full Yearly Breakdown</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);" class="fs-12 fw-medium text-muted text-truncate-1-line">
                                        Attendance Rate </a>
                                    <div class="w-100 text-end">
                                        <span class="fs-12 text-dark">{{ $attendanceRate }}%</span>
                                        <span class="fs-11 text-muted">(Today)</span>
                                    </div>
                                </div>
                                <div class="progress mt-2 ht-3">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ $attendanceRate }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [Total Employees] end -->
                <!-- [Payment Records] start -->
                <div class="col-xxl-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Monthly Payroll Trends</h5>
                            <div class="card-header-action">
                                <div class="card-header-btn">
                                    <div data-bs-toggle="tooltip" title="Delete">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                            data-bs-toggle="remove"> </a>
                                    </div>
                                    <div data-bs-toggle="tooltip" title="Refresh">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                            data-bs-toggle="refresh"> </a>
                                    </div>
                                    <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                            data-bs-toggle="expand"> </a>
                                    </div>
                                </div>
                                <!-- <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                        data-bs-offset="25, 25">
                                        <div data-bs-toggle="tooltip" title="Options">
                                            <i class="feather-more-vertical"></i>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="feather-at-sign"></i>New</a>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="feather-calendar"></i>Event</a>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="feather-bell"></i>Snoozed</a>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="feather-trash-2"></i>Deleted</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="feather-settings"></i>Settings</a>
                                        <a href="javascript:void(0);" class="dropdown-item"><i
                                                class="feather-life-buoy"></i>Tips & Tricks</a>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div id="payment-records-chart"></div>
                        </div>
                        <div class="card-footer">
                            <div class="row g-4">
                                <div class="col-lg-3">
                                    <div class="p-3 border border-dashed rounded">
                                        <div class="fs-12 text-muted mb-1">Pending</div>
                                            <h6 class="fw-bold text-dark">₹{{ number_format($totalPendingAmount, 0) }}</h6>
                                            <div class="progress mt-2 ht-3">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalNetSalary > 0 ? ($totalPendingAmount / $totalNetSalary) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="p-3 border border-dashed rounded">
                                            <div class="fs-12 text-muted mb-1">Paid</div>
                                            <h6 class="fw-bold text-dark">₹{{ number_format($totalPaidAmount, 0) }}</h6>
                                            <div class="progress mt-2 ht-3">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalNetSalary > 0 ? ($totalPaidAmount / $totalNetSalary) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="p-3 border border-dashed rounded">
                                            <div class="fs-12 text-muted mb-1">Rejected</div>
                                            <h6 class="fw-bold text-dark">₹{{ number_format($totalRejectedAmount, 0) }}</h6>
                                            <div class="progress mt-2 ht-3">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $totalNetSalary > 0 ? ($totalRejectedAmount / $totalNetSalary) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="p-3 border border-dashed rounded">
                                            <div class="fs-12 text-muted mb-1">Total Salary</div>
                                            <h6 class="fw-bold text-dark">₹{{ number_format($totalNetSalary, 0) }}</h6>
                                            <div class="progress mt-2 ht-3">
                                                <div class="progress-bar bg-dark" role="progressbar" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Payment Records] end -->
                    <!--! BEGIN: [Upcoming Schedule] !-->
                    <div class="col-xxl-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Upcoming Holidays</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Delete text-primary">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                                data-bs-toggle="remove"> </a>
                                        </div>
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                                data-bs-toggle="refresh"> </a>
                                        </div>
                                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                                data-bs-toggle="expand"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @forelse($upcomingHolidays as $index => $holiday)
                                    @php $hDate = \Carbon\Carbon::parse($holiday->date); @endphp
                                    <div class="p-3 border border-dashed rounded-3 mb-3 holiday-slide-item {{ $index >= 4 ? 'd-none' : '' }}" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <div
                                                    class="wd-50 ht-50 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date">
                                                    <span class="fs-18 fw-bold mb-1 d-block">{{ $hDate->format('d') }}</span>
                                                    <span class="fs-10 fw-semibold text-uppercase d-block">{{ $hDate->format('M') }}</span>
                                                </div>
                                                <div class="text-dark">
                                                    <a href="javascript:void(0);" class="fw-bold mb-2 text-truncate-1-line">{{ $holiday->title }}</a>
                                                    <span class="fs-11 fw-normal text-muted text-truncate-1-line">Holiday ({{ $hDate->format('Y') }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">No upcoming holidays.</div>
                                @endforelse

                                @if(count($upcomingHolidays) > 4)
                                    <div class="d-flex align-items-center justify-content-center gap-4 mt-2">
                                        <a href="javascript:void(0);" id="prev-holiday" class="avatar-text avatar-md bg-soft-primary text-primary opacity-50 border-0 disabled shadow-sm">
                                            <i class="feather-chevron-left fs-20"></i>
                                        </a>
                                        <a href="javascript:void(0);" id="next-holiday" class="avatar-text avatar-md bg-soft-primary text-primary border-0 shadow-sm">
                                            <i class="feather-chevron-right fs-20"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('holidays.index') }}"
                                class="card-footer fs-11 fw-bold text-uppercase text-center py-4">View Full Holiday Calendar</a>
                        </div>
                    </div>
                    <!--! END: [Upcoming Schedule] !-->
                    <!-- [Latest Leads] start -->
                    <div class="col-xxl-8">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Recent Payroll History</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Delete">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                                data-bs-toggle="remove"> </a>
                                        </div>
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                                data-bs-toggle="refresh"> </a>
                                        </div>
                                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                                data-bs-toggle="expand"> </a>
                                        </div>
                                    </div>
                                    <!-- <div class="dropdown">
                                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                            data-bs-offset="25, 25">
                                            <div data-bs-toggle="tooltip" title="Options">
                                                <i class="feather-more-vertical"></i>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-at-sign"></i>New</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-calendar"></i>Event</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-bell"></i>Snoozed</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-trash-2"></i>Deleted</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-settings"></i>Settings</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-life-buoy"></i>Tips & Tricks</a>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th scope="row">Employee</th>
                                                <th>Month</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentPayrolls as $rp)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="avatar-text avatar-md bg-soft-primary text-primary">
                                                                {{ substr($rp->employee->name, 0, 1) }}
                                                            </div>
                                                            <a href="javascript:void(0);">
                                                                <span class="d-block text-truncate-1-line">{{ $rp->employee->name }}</span>
                                                                <span class="fs-12 d-block fw-normal text-muted small text-uppercase">{{ $rp->employee->designation }}</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-gray-200 text-dark">{{ $rp->month }}</span>
                                                    </td>
                                                    <td>₹{{ number_format($rp->net_salary, 0) }}</td>
                                                    <td>
                                                        <span class="badge {{ $rp->status == 'paid' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} text-uppercase">
                                                            {{ $rp->status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                                            <a href="{{ route('payroll.export', ['id' => $rp->id, 'format' => 'pdf']) }}" class="avatar-text avatar-md bg-soft-info text-info" title="Download PDF">
                                                                <i class="feather-download"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">No recent payrolls found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-column align-items-center gap-3">
                                <div class="fs-12 text-muted">
                                    @if($recentPayrolls->total() > 0)
                                        Showing {{ $recentPayrolls->firstItem() }} to {{ $recentPayrolls->lastItem() }} of {{ $recentPayrolls->total() }} entries
                                    @endif
                                </div>
                                @if($recentPayrolls->hasPages())
                                    <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
                                        <li>
                                            <a href="{{ $recentPayrolls->previousPageUrl() }}" class="{{ $recentPayrolls->onFirstPage() ? 'opacity-50 pointer-events-none' : '' }}">
                                                <i class="feather-chevron-left"></i>
                                            </a>
                                        </li>
                                        @foreach ($recentPayrolls->getUrlRange(max(1, $recentPayrolls->currentPage() - 1), min($recentPayrolls->lastPage(), $recentPayrolls->currentPage() + 1)) as $page => $url)
                                            <li>
                                                <a href="{{ $url }}" class="{{ ($page == $recentPayrolls->currentPage()) ? 'active' : '' }}">{{ $page }}</a>
                                            </li>
                                        @endforeach
                                        <li>
                                            <a href="{{ $recentPayrolls->nextPageUrl() }}" class="{{ !$recentPayrolls->hasMorePages() ? 'opacity-50 pointer-events-none' : '' }}">
                                                <i class="feather-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- [Latest Leads] end -->

                      <!--! BEGIN: [Attendance Analytics] !-->
                    <div class="col-xxl-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header border-bottom-0 pb-0">
                                <h5 class="card-title">Attendance Analytics</h5>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('payroll.attendance') }}" class="dropdown-item">
                                            <i class="feather-external-link me-2"></i>
                                            <span>Full Attendance List</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-3 text-center">
                                <div class="py-4 position-relative">
                                    <div class="fs-1 fw-black text-primary mb-1 mt-2">{{ $attendanceRate }}%</div>
                                    <div class="text-muted fw-bold small text-uppercase">Average Attendance Rate</div>
                                </div>

                                <div class="p-3 bg-soft-primary rounded-3 text-start mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-bold text-dark">Staff Presence Today</span>
                                        <span class="small fw-black text-primary">{{ $todayPresent }}/{{ $totalEmployees }}</span>
                                    </div>
                                    <div class="progress ht-5 bg-white mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $totalEmployees > 0 ? ($todayPresent / $totalEmployees) * 100 : 0 }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-dark">Leave Today</span>
                                        <span class="small fw-black text-danger">{{ $todayLeave ?? 0 }} Staff</span>
                                    </div>
                                </div>

                                <div class="row g-2 text-start">
                                    <div class="col-6 border-end">
                                        <div class="fs-5 fw-bold text-dark">{{ $totalEmployees }}</div>
                                        <div class="fs-11 text-muted text-uppercase fw-bold">Total Staff</div>
                                    </div>
                                    <div class="col-6 ps-3">
                                        <div class="fs-5 fw-bold text-success">{{ $todayPresent }}</div>
                                        <div class="fs-11 text-muted text-uppercase fw-bold">Checked-in</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-top p-3 bg-light bg-opacity-10 text-center">
                                <a href="{{ route('payroll.attendance.add') }}" class="fs-12 fw-bold text-primary text-uppercase">
                                    <i class="feather-plus-circle me-1"></i> Add Daily Records
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--! END: [Attendance Analytics] !-->
                </div><!-- row end -->
                <div class="row pt-4">

                    <!--! BEGIN: [Team Progress] !-->
                    <!-- <div class="col-xxl-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Team Progress</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Delete">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                                data-bs-toggle="remove"> </a>
                                        </div>
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                                data-bs-toggle="refresh"> </a>
                                        </div>
                                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                                data-bs-toggle="expand"> </a>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                                            data-bs-offset="25, 25">
                                            <div data-bs-toggle="tooltip" title="Options">
                                                <i class="feather-more-vertical"></i>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-at-sign"></i>New</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-calendar"></i>Event</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-bell"></i>Snoozed</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-trash-2"></i>Deleted</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-settings"></i>Settings</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="feather-life-buoy"></i>Tips & Tricks</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action">
                                <div class="hstack justify-content-between border border-dashed rounded-3 p-3 mb-3">
                                    <div class="hstack gap-3">
                                        <div class="avatar-image">
                                            <img src="assets/images/avatar/1.png" alt="" class="img-fluid" />
                                        </div>
                                        <div>
                                            <a href="javascript:void(0);">Alexandra Della</a>
                                            <div class="fs-11 text-muted">Frontend Developer</div>
                                        </div>
                                    </div>
                                    <div class="team-progress-1"></div>
                                </div>
                                <div class="hstack justify-content-between border border-dashed rounded-3 p-3 mb-3">
                                    <div class="hstack gap-3">
                                        <div class="avatar-image">
                                            <img src="assets/images/avatar/2.png" alt="" class="img-fluid" />
                                        </div>
                                        <div>
                                            <a href="javascript:void(0);">Archie Cantones</a>
                                            <div class="fs-11 text-muted">UI/UX Designer</div>
                                        </div>
                                    </div>
                                    <div class="team-progress-2"></div>
                                </div>
                                <div class="hstack justify-content-between border border-dashed rounded-3 p-3 mb-3">
                                    <div class="hstack gap-3">
                                        <div class="avatar-image">
                                            <img src="assets/images/avatar/3.png" alt="" class="img-fluid" />
                                        </div>
                                        <div>
                                            <a href="javascript:void(0);">Malanie Hanvey</a>
                                            <div class="fs-11 text-muted">Backend Developer</div>
                                        </div>
                                    </div>
                                    <div class="team-progress-3"></div>
                                </div>
                                <div class="hstack justify-content-between border border-dashed rounded-3 p-3 mb-2">
                                    <div class="hstack gap-3">
                                        <div class="avatar-image">
                                            <img src="assets/images/avatar/4.png" alt="" class="img-fluid" />
                                        </div>
                                        <div>
                                            <a href="javascript:void(0);">Kenneth Hune</a>
                                            <div class="fs-11 text-muted">Digital Marketer</div>
                                        </div>
                                    </div>
                                    <div class="team-progress-4"></div>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="card-footer fs-11 fw-bold text-uppercase text-center">Update 30 Min
                                Ago</a>
                        </div>
                    </div> -->
                    <!--! END: [Team Progress] !-->
                </div><!-- second row end -->
                <!-- Monthly Summary Modal [ENHANCED] -->
            <div class="modal fade" id="summaryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="modal-header bg-soft-primary border-0 p-4">
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-primary">Financial Breakdown History</h5>
                                <p class="text-muted small mb-0 mt-1">Detailed payroll analytics for the last 6 months</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-uppercase fs-10 fw-800 text-muted">
                                        <tr>
                                            <th class="ps-4">Month</th>
                                            <th>Basic / Earnings</th>
                                            <th>Deductions</th>
                                            <th>Net Payable</th>
                                            <th class="pe-4 text-end">Trends</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalHistoryTable">
                                        <!-- Populated via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 bg-light bg-opacity-50">
                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="feather-info me-1"></i> Data shown is aggregated for all employees.
                                </div>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close Summary</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </div>

            <!-- Holiday Modal Deleted -->

            <!-- [ Main Content ] end -->
@endsection

@push('scripts')
    <script shadow>
    $(document).ready(function() {
        // Initializing the chart with dynamic data from controller
        var options = {
            chart: {
                height: 380,
                width: "100%",
                type: "bar",
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            stroke: {
                width: [0, 0, 0],
                show: false
            },
            plotOptions: {
                bar: {
                    columnWidth: "45%",
                    borderRadius: 4,
                    dataLabels: { position: 'top' }
                }
            },
            colors: ["#e2e8f0", "#10b981", "#f59e0b"], 
            series: [
                {
                    name: "Total Payroll (Expected)",
                    type: "bar",
                    data: {!! json_encode($chartTotal) !!}
                },
                {
                    name: "Completed (Paid)",
                    type: "bar",
                    data: {!! json_encode($chartPaid) !!}
                },
                {
                    name: "Pending (Unpaid)",
                    type: "bar",
                    data: {!! json_encode($chartPending) !!}
                }
            ],
            fill: {
                opacity: [1, 1, 1],
                type: ['solid', 'solid', 'solid']
            },
            markers: { size: 0 },
            xaxis: {
                categories: {!! json_encode($chartMonths) !!},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        fontSize: "10px",
                        colors: "#64748b",
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(e) {
                        return "₹" + e.toLocaleString()
                    },
                    style: { color: "#64748b", fontWeight: 600 }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            dataLabels: { enabled: false },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(e) {
                        return "₹" + e.toLocaleString()
                    }
                },
                theme: 'dark'
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: "12px",
                fontFamily: "Inter",
                fontWeight: 600,
                markers: { radius: 12 }
            }
        };

        // Re-render chart to ensure dynamic data is applied over theme defaults
        setTimeout(function() {
            const chartContainer = document.querySelector("#payment-records-chart");
            var chart;
            if (chartContainer) {
                chartContainer.innerHTML = '';
                chart = new ApexCharts(chartContainer, options);
                chart.render();
            }
            // Modal Drill-down Logic
            var summaryModal = new bootstrap.Modal(document.getElementById('summaryModal'));

            function showMonthlySummary(month) {
                fetch(`/dashboard/summary?month=${month}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            let html = '';
                            data.history.forEach(item => {
                                html += `
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">${item.month}</div>
                                            <div class="fs-10 text-muted text-uppercase fw-bold">Financial Record</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">₹${item.earnings.toLocaleString()}</div>
                                            <div class="fs-10 text-success text-uppercase fw-bold">Total Earnings</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-danger">-₹${item.deductions.toLocaleString()}</div>
                                            <div class="fs-10 text-muted text-uppercase fw-bold">Total Deducted</div>
                                        </td>
                                        <td>
                                            <div class="fw-black text-primary">₹${item.net.toLocaleString()}</div>
                                            <div class="fs-10 text-muted text-uppercase fw-bold">Net Distributed</div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <span class="badge bg-soft-primary text-primary fs-10 text-uppercase fw-bold">Analyzed</span>
                                        </td>
                                    </tr>
                                `;
                            });
                            document.getElementById('modalHistoryTable').innerHTML = html;
                            summaryModal.show();
                        }
                    });
            }
            // Chart Update Logic
            function updateChartRange(range) {
                fetch(`/dashboard/chart?range=${range}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            chart.updateSeries(data.series);
                            chart.updateOptions({
                                xaxis: { categories: data.labels }
                            });
                        }
                    });
            }

            // Holiday Slider Logic
            let currentHolidayPage = 0;
            const holidaysPerPage = 4;
            const totalHolidays = {{ count($upcomingHolidays) }};
            const holidayItems = document.querySelectorAll('.holiday-slide-item');
            const prevHolidayBtn = document.getElementById('prev-holiday');
            const nextHolidayBtn = document.getElementById('next-holiday');

            function updateHolidayView() {
                holidayItems.forEach((item, index) => {
                    const start = currentHolidayPage * holidaysPerPage;
                    const end = start + holidaysPerPage;
                    if (index >= start && index < end) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });

                // Update Button States
                if (currentHolidayPage === 0) {
                    prevHolidayBtn.classList.add('disabled');
                    prevHolidayBtn.style.opacity = '0.5';
                } else {
                    prevHolidayBtn.classList.remove('disabled');
                    prevHolidayBtn.style.opacity = '1';
                }

                if ((currentHolidayPage + 1) * holidaysPerPage >= totalHolidays) {
                    nextHolidayBtn.classList.add('disabled');
                    nextHolidayBtn.style.opacity = '0.5';
                } else {
                    nextHolidayBtn.classList.remove('disabled');
                    nextHolidayBtn.style.opacity = '1';
                }
            }

            if (nextHolidayBtn) {
                nextHolidayBtn.addEventListener('click', function() {
                    if ((currentHolidayPage + 1) * holidaysPerPage < totalHolidays) {
                        currentHolidayPage++;
                        updateHolidayView();
                    }
                });
            }

            if (prevHolidayBtn) {
                prevHolidayBtn.addEventListener('click', function() {
                    if (currentHolidayPage > 0) {
                        currentHolidayPage--;
                        updateHolidayView();
                    }
                });
            }
        }, 500);
    });
    </script>
@endpush