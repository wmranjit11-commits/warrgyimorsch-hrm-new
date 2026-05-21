@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Job Vacancy</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Job Vacancy</li>
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
                    <div id="bulk-action-wrapper" style="display: none;">
                        <a href="javascript:void(0);" id="btn-bulk-delete" class="btn btn-icon btn-soft-danger"
                            style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; 
                            justify-content: center;">
                            <i class="feather-trash-2 fs-18"></i>
                        </a>
                    </div>
                    <div class="filter-toggle-wrapper">
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" id="toggleFilter"
                            style="cursor: pointer;">
                            <i class="feather-filter"></i>
                        </a>
                    </div>
                    <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="offcanvas" 
                    data-bs-target="#projectOffcanvas">
                        <i class="feather-plus me-2"></i>
                        <span>Add</span>
                    </a>
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="page-header-collapse">
        <div class="accordion-body pb-2">
            <div class="row g-3">
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-secondary">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Pending</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $pendingCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-secondary text-secondary">
                                    <i class="feather-pause-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-warning">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Awaited</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $awaitedCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-warning text-warning">
                                    <i class="feather-minus-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-danger">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Rejected</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $rejectedCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-danger text-danger">
                                    <i class="feather-refresh-ccw"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-success">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Selected</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $selectedCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-success text-success">
                                    <i class="feather-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- filter section -->
     <div class="filter-wrapper" id="filterSection" style="display: none;">
        <div class="card stretch stretch-full border-bottom bg-light bg-opacity-10 p-4 mb-4">
            <div class="row g-3">

                <!-- NAME SEARCH -->
                <div class="col-md-4">
                    <label class="form-label">Applicant Name</label>
                    <input type="text"
                        id="filterName"
                        class="form-control"
                        placeholder="Search by name..."
                        onkeyup="applyFilters()">
                </div>

                <!-- STATUS -->
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filterStatus" class="form-select" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="awaited">Awaited</option>
                        <option value="rejected">Rejected</option>
                        <option value="selected">Selected</option>
                    </select>
                </div>

                <!-- DEPARTMENT -->
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select id="filterDepartment" class="form-select" onchange="applyFilters()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- BUTTONS -->
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <!-- <button type="button"
                        class="btn btn-primary flex-grow-1 fw-bold"
                        onclick="applyFilters()"
                        style="height: 48px; border-radius: 12px;">
                        APPLY
                    </button> -->

                    <button class="btn btn-light"
                        onclick="resetFilters()"
                        style="height: 48px; width: 60px; border-radius: 12px;">
                        <i class="feather-refresh-cw"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Main containt  -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Qualification</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Interview Schedule</th>
                                        <th>Interviewer</th>
                                        <th>Status</th>
                                        <th>Resume</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($applications as $app)
                                    <tr class="job-row">
                                        <td class="job-name">{{ $app->name }}</td>
                                        <td>{{ $app->qualification }}</td>
                                        <td class="job-department">{{ $app->department->name ?? 'N/A' }}</td>
                                        <td>{{ $app->designation }}</td>
                                        <td>
                                            {{ $app->interview_date }} <br>
                                            <small>{{ \Carbon\Carbon::parse($app->interview_time)->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $app->interviewer->name ?? 'N/A' }}</td>

                                        {{-- STATUS (editable dropdown) --}}
                                        <td class="job-status">
                                            <form method="POST" action="{{ url('/job-applications/update-status/'.$app->id) }}">
                                                @csrf

                                                <select name="status" class="form-select form-select-sm job-status-select"
                                                    onchange="this.form.submit()">
                                                    <option value="Pending" {{ $app->status == 'Pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="Selected" {{ $app->status == 'Selected' ? 'selected' : '' }}>
                                                        Selected
                                                    </option>
                                                    <option value="Awaited" {{ $app->status == 'Awaited' ? 'selected' : '' }}>
                                                        Awaited
                                                    </option>
                                                    <option value="Rejected" {{ $app->status == 'Rejected' ? 'selected' : '' }}>
                                                        Rejected
                                                    </option>
                                                </select>
                                            </form>
                                        </td>

                                        {{-- RESUME VIEW ICON --}}
                                        <td>
                                            <a href="{{ asset('storage/' . $app->resume) }}" target="_blank"
                                                class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle" title="View Details">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add section  -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="projectOffcanvas" style="width:600px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2">
                <i class="feather-user-plus text-primary"></i>
                Candidate Information
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <form action="{{ url('/job-vacancy/store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-primary">
                            Basic Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name </label>
                                <input type="text" class="form-control" name="name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="phone">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Qualification</label>
                                <input type="text" class="form-control" placeholder="B.Tech, MCA..." name="qualification">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>

                                <select class="form-select" name="department_id" required>
                                    <option value="">Select Department</option>

                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control" placeholder="Enter designation">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Experience</label>
                                <input type="text" class="form-control" placeholder="1 years" name="experience">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interview -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-primary">Interview Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interview Date</label>
                                <input type="date" class="form-control" name="interview_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interview Time</label>
                                <input type="time" class="form-control" name="interview_time">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select"name="status">
                                    <option>Pending</option>
                                    <option>Selected</option>
                                    <option>Awaited</option>
                                    <option>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Interviewer</label>

                                <select class="form-select" name="interviewer_id">
                                    <option value="">Select Interviewer</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-primary">Profile Upload</h6>
                        <div class="mb-3">
                            <label class="form-label">Upload Resume/Profile</label>
                            <input type="file" class="form-control" name="resume">
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary w-100">
                    <i class="feather-save me-2"></i>
                    Save Candidate
                </button>
            </form>
        </div>
    </div>

    <script>
// Toggle filter
document.getElementById("toggleFilter").addEventListener("click", function () {
    let filter = document.getElementById("filterSection");

    filter.style.display = (filter.style.display === "none" || filter.style.display === "")
        ? "block"
        : "none";
});


// Apply filters
function applyFilters() {
    let name = document.getElementById("filterName").value.toLowerCase();
    let status = document.getElementById("filterStatus").value.toLowerCase();
    let department = document.getElementById("filterDepartment").value.toLowerCase();

    let rows = document.querySelectorAll(".job-row");

    rows.forEach(row => {

        let rowName = row.querySelector(".job-name")?.innerText.toLowerCase() || "";
        let rowStatus = row.querySelector(".job-status-select")?.value.toLowerCase() || "";
        let rowDept = row.querySelector(".job-department")?.innerText.toLowerCase() || "";

        let matchName = rowName.includes(name);
        let matchStatus = status === "" || rowStatus.includes(status);
        let matchDept = department === "" || rowDept.includes(department);

        row.style.display = (matchName && matchStatus && matchDept) ? "" : "none";
    });
}


// Reset filters
function resetFilters() {
    document.getElementById("filterName").value = "";
    document.getElementById("filterStatus").value = "";
    document.getElementById("filterDepartment").value = "";
    applyFilters();
}
</script>
@endsection