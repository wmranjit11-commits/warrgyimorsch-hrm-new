@extends('layouts.app')

@section('content')

    <!-- [ page-header ] start -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10" style="color: #3858f9; font-weight: 700;">Project Management</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Daily Task Master</li>
            </ul>
        </div>
        <div class="page-header-right">
            <div class="d-flex gap-2">
                <a href="javascript:void(0);" class="avatar-text avatar-md bg-primary text-white shadow-sm"
                    style="border-radius: 10px;" data-bs-toggle="offcanvas" data-bs-target="#projectOffcanvas"
                    onclick="resetForm()" title="Add Project">
                    <i class="feather-plus"></i>
                </a>
                <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary shadow-sm"
                    style="border-radius: 10px;" data-bs-toggle="collapse" data-bs-target="#filterSection"
                    title="Filter Projects">
                    <i class="feather-filter"></i>
                </a>
                <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-secondary text-secondary shadow-sm"
                    style="border-radius: 10px;" onclick="location.reload()" title="Refresh">
                    <i class="feather-refresh-cw"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ main-content ] start -->
    <div class="main-content pt-2" style="margin-bottom: 100px;">
        <div class="row">
            <!-- PROJECT LIST (FULL WIDTH) -->
            <div class="col-12">
                <!-- Filter Section (Collapsible Wrapper) -->
                <div id="filterSection" class="collapse">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white;">
                        <div class="card-body p-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Search
                                        Name</label>
                                    <input type="text" id="projectSearchName"
                                        class="form-control border-0 bg-light shadow-none fw-bold"
                                        placeholder="Project name..." onkeyup="filterProjects()"
                                        style="height: 44px; border-radius: 10px;">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Status</label>
                                    <select id="projectSearchStatus"
                                        class="form-select border-0 bg-light shadow-none fw-bold"
                                        onchange="filterProjects()" style="height: 44px; border-radius: 10px;">
                                        <option value="">All Status</option>
                                        <option value="In Process">In Process</option>
                                        <option value="Completed">Completed</option>
                                        <option value="On Hold">On Hold</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase mb-2">Department</label>
                                    <select id="projectSearchDept" class="form-select border-0 bg-light shadow-none fw-bold"
                                        onchange="filterProjects()" style="height: 44px; border-radius: 10px;">
                                        <option value="">All Departments</option>
                                        <option value="Web Development">Web Development</option>
                                        <option value="Mobile Development">Mobile Development</option>
                                        <option value="Design">Design</option>
                                        <option value="Quality Assurance">Quality Assurance</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button class="btn btn-primary flex-grow-1 fw-bold shadow-none"
                                        onclick="filterProjects()"
                                        style="height: 44px; border-radius: 10px; background: #3858f9; border: none;">
                                        APPLY
                                    </button>
                                    <a href="{{ route('projects.index') }}"
                                        class="btn btn-soft-danger fw-bold d-flex align-items-center justify-content-center"
                                        style="border-radius: 10px; height: 44px; width: 80px; font-size: 13px; text-decoration: none;">
                                        RESET
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; background: white;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                        style="border-radius: 12px 12px 0 0;">

                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 me-3" style="color: #334155; font-size: 16px;">Project Overview</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Show</span>
                                <select id="entriesLimit" class="form-select d-inline-block border-0 bg-light fw-bold"
                                    onchange="paginateTable()"
                                    style="width: 100px; border-radius: 10px; height: 40px; font-size: 14px; color: #1e293b; padding: 0 10px;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text bg-light border-0"><i
                                    class="feather-search text-muted"></i></span>
                            <input type="text" id="projectSearch" class="form-control bg-light border-0 shadow-none fw-bold"
                                placeholder="Search project name..." onkeyup="filterProjects()"
                                style="height: 44px; font-size: 14px; border-radius: 0 10px 10px 0;">
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="projectsTable">
                                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <tr style="height: 60px; vertical-align: middle;">
                                        <th class="ps-4"
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            SR NO</th>
                                        <th
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Project & Progress</th>
                                        <th
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Start Date</th>
                                        <th
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Time Tracking / End Date</th>
                                        <th
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Status</th>
                                        <th
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Department</th>
                                        <th
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Technology</th>
                                        <th class="pe-4 text-center"
                                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody style="border-top: 1px solid #f1f5f9;">
                                    @forelse($projects as $index => $project)
                                        <tr class="project-row" style="height: 80px;"
                                            data-name="{{ strtolower($project->name) }}" data-status="{{ $project->status }}"
                                            data-dept="{{ $project->department }}">
                                            <td class="ps-4 fw-bold" style="font-size: 13px;">{{ $index + 1 }}</td>
                                            <td class="project-column-progress">
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="fw-bold mb-1"
                                                        style="color: #1e293b; font-size: 14px;">{{ $project->name }}</span>
                                                    <div class="progress shadow-none"
                                                        style="height: 6px; width: 140px; background: #f1f5f9; border-radius: 10px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $project->progress }}%; background: {{ $project->progress >= 100 ? '#22c55e' : ($project->progress > 80 ? '#f59e0b' : '#3858f9') }}; border-radius: 10px;"
                                                            aria-valuenow="{{ $project->progress }}" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center"
                                                        style="width: 140px;">
                                                        <span class="text-muted"
                                                            style="font-size: 10px; font-weight: 700;">{{ $project->progress }}%
                                                            DONE</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-text avatar-sm bg-soft-primary rounded-circle"
                                                        style="width: 32px; height: 32px;">
                                                        <i class="feather-calendar" style="font-size: 12px;"></i>
                                                    </div>
                                                    <span class="fw-bold"
                                                        style="font-size: 13px; color: #475569;">{{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if($project->status != 'Completed' && $project->end_date)
                                                        <div class="timer-display fw-bold text-primary mb-1"
                                                            style="font-size: 13px;"
                                                            data-start="{{ $project->start_date ? $project->start_date->toIso8601String() : '' }}"
                                                            data-end="{{ $project->end_date->toIso8601String() }}">
                                                            Calculating...
                                                        </div>
                                                    @else
                                                        <div class="fw-bold text-success mb-1" style="font-size: 13px;">
                                                            <i class="feather-check-circle me-1"></i> Completed
                                                        </div>
                                                    @endif
                                                    <span class="text-muted" style="font-size: 11px; font-weight: 600;">
                                                        Deadline:
                                                        {{ $project->end_date ? $project->end_date->format('d M Y') : 'No Deadline' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $projStatusClass = 'bg-soft-primary text-primary';
                                                    if ($project->status == 'Completed')
                                                        $projStatusClass = 'bg-soft-success text-success';
                                                    elseif ($project->status == 'On Hold')
                                                        $projStatusClass = 'bg-soft-warning text-warning';
                                                    elseif ($project->status == 'Review')
                                                        $projStatusClass = 'bg-soft-info text-info';
                                                    elseif ($project->status == 'Pending')
                                                        $projStatusClass = 'bg-soft-secondary text-secondary';
                                                    elseif ($project->status == 'Rework')
                                                        $projStatusClass = 'bg-soft-danger text-danger';
                                                @endphp
                                                <div class="dropdown">
                                                    <span class="badge {{ $projStatusClass }} dropdown-toggle cursor-pointer"
                                                        data-bs-toggle="dropdown" data-bs-boundary="viewport"
                                                        aria-expanded="false"
                                                        style="padding: 6px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.3px; cursor: pointer; min-width: 100px; display: inline-block; text-align: center;">
                                                        {{ $project->status }}
                                                    </span>
                                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm"
                                                        style="border-radius: 12px; font-size: 12px; z-index: 999 !important;">
                                                        <li><a class="dropdown-item fw-bold" href="javascript:void(0);"
                                                                onclick="updateProjectStatus({{ $project->id }}, 'Pending')">Pending</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold text-primary"
                                                                href="javascript:void(0);"
                                                                onclick="updateProjectStatus({{ $project->id }}, 'In Process')">In
                                                                Process</a></li>
                                                        <li><a class="dropdown-item fw-bold text-success"
                                                                href="javascript:void(0);"
                                                                onclick="updateProjectStatus({{ $project->id }}, 'Completed')">Completed</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold text-warning"
                                                                href="javascript:void(0);"
                                                                onclick="updateProjectStatus({{ $project->id }}, 'On Hold')">On
                                                                Hold</a></li>
                                                        <li><a class="dropdown-item fw-bold text-info"
                                                                href="javascript:void(0);"
                                                                onclick="updateProjectStatus({{ $project->id }}, 'Review')">Review</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold text-danger"
                                                                href="javascript:void(0);"
                                                                onclick="updateProjectStatus({{ $project->id }}, 'Rework')">Rework</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark"
                                                    style="font-size: 12px;">{{ $project->department }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted"
                                                    style="font-size: 11px;">{{ $project->technology }}</span>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-primary text-primary rounded"
                                                        title="View Team" onclick="showProjectTeam({{ $project->id }})">
                                                        <i class="feather-users"></i>
                                                    </a>
                                                    <template id="proj_team_{{ $project->id }}">
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless align-middle mb-0">
                                                                <thead style="background: #f8fafc; border-bottom: 2px solid #f1f5f9;">
                                                                    <tr>
                                                                        <th class="ps-3 py-3" style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">EMPLOYEE</th>
                                                                        <th class="py-3 text-center" style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; width: 120px;">STATUS</th>
                                                                        <th class="py-3" style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">ASSIGNED TASK</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($project->tasks->sortByDesc('id') as $task)
                                                                        @php
                                                                            $employee = $task->employee;
                                                                            $taskStatusClass = 'bg-soft-primary text-primary';
                                                                            if ($task->status == 'Completed') $taskStatusClass = 'bg-soft-success text-success';
                                                                            elseif ($task->status == 'Pending') $taskStatusClass = 'bg-soft-secondary text-secondary';
                                                                            elseif ($task->status == 'On Hold') $taskStatusClass = 'bg-soft-warning text-warning';
                                                                        @endphp
                                                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                                                            <td class="ps-3 py-3" style="width: 200px;">
                                                                                <div class="d-flex align-items-center gap-2">
                                                                                    <div class="avatar-text avatar-sm bg-soft-primary text-primary rounded-circle fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                                                                        {{ strtoupper(substr($employee->name ?? '?', 0, 1)) }}
                                                                                    </div>
                                                                                    <div class="d-flex flex-column">
                                                                                        <span class="fw-bold text-dark" style="font-size: 13px;">{{ $employee->name ?? 'Unknown' }}</span>
                                                                                        <span class="text-muted" style="font-size: 10px;">{{ $employee->designation ?? 'Member' }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="py-3 text-center">
                                                                                <span class="badge {{ $taskStatusClass }}" 
                                                                                    style="padding: 6px 10px; border-radius: 8px; font-size: 9px; font-weight: 800; text-transform: uppercase; min-width: 90px;">
                                                                                    {{ $task->status }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="py-3">
                                                                                <span class="fw-bold text-muted" style="font-size: 13px; line-height: 1.4; display: block; word-break: break-word;">
                                                                                    {{ $task->task_title }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="3" class="text-center py-5">
                                                                                <div class="text-muted fw-bold" style="font-size: 14px;">No tasks assigned yet.</div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </template>

                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-secondary text-secondary rounded"
                                                        title="View Description" onclick="showProjectDesc({{ $project->id }})">
                                                        <i class="feather-file-text"></i>
                                                    </a>
                                                    <template
                                                        id="proj_desc_{{ $project->id }}">{!! $project->description ?? '<span class="text-muted">No description provided.</span>' !!}</template>

                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-info text-info rounded"
                                                        onclick="editProject({{ json_encode($project) }})" title="Edit Project">
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                                        class="delete-form d-inline" onsubmit="deleteRecord(event, this)">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="avatar-text avatar-md bg-soft-danger text-danger rounded border-0"
                                                            title="Delete Project">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">No projects found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- STANDARD PAGINATION ARROWS -->
                        <div class="px-4 py-4 border-top d-flex justify-content-between align-items-center bg-white"
                            style="border-radius: 0 0 12px 12px;">
                            <div class="small text-muted fw-bold" id="entriesInfo" style="font-size: 14px;">Showing 1 to
                                {{ $projects->count() }} of {{ $projects->count() }} entries
                            </div>
                            <nav>
                                <ul class="pagination pagination-md mb-0 gap-1">
                                    <li class="page-item disabled mx-1">
                                        <a class="page-link border-0 rounded-circle d-flex align-items-center justify-content-center text-muted"
                                            href="javascript:void(0);"
                                            style="width: 36px; height: 36px; background: #f8fafc;"><i
                                                class="feather-chevron-left"></i></a>
                                    </li>
                                    <li class="page-item active mx-1">
                                        <a class="page-link border-0 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                            href="javascript:void(0);"
                                            style="background: #3858f9; width: 36px; height: 36px;">1</a>
                                    </li>
                                    <li class="page-item disabled mx-1">
                                        <a class="page-link border-0 rounded-circle d-flex align-items-center justify-content-center text-muted"
                                            href="javascript:void(0);"
                                            style="width: 36px; height: 36px; background: #f8fafc;"><i
                                                class="feather-chevron-right"></i></a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROJECT ADD/EDIT OFFCANVAS -->
    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="projectOffcanvas"
        aria-labelledby="projectOffcanvasLabel" style="width: 450px;">
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-primary" id="projectOffcanvasLabel">Project Information</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <form id="projectForm" action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Project Name <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name" id="projectName" value="{{ old('name') }}"
                        class="form-control border-0 bg-light shadow-none" placeholder="e.g. ERP Development"
                        style="border-radius: 10px; height: 48px;" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Start Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}"
                            class="form-control border-0 bg-light shadow-none" onclick="this.showPicker()"
                            style="border-radius: 10px; height: 48px;" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">End Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="endDate" value="{{ old('end_date') }}"
                            class="form-control border-0 bg-light shadow-none" onclick="this.showPicker()"
                            style="border-radius: 10px; height: 48px;" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Status <span
                            class="text-danger">*</span></label>
                    <select name="status" id="projectStatus" class="form-select border-0 bg-light shadow-none"
                        style="border-radius: 10px; height: 48px;">
                        <option value="Pending">Pending</option>
                        <option value="In Process">In Process</option>
                        <option value="Completed">Completed</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Review">Review</option>
                        <option value="Rework">Rework</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Department <span
                                class="text-danger">*</span></label>
                        <select name="department" id="projectDept" class="form-select border-0 bg-light shadow-none"
                            style="border-radius: 10px; height: 48px;" required>
                            <option value="">Select department...</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Mobile Development">Mobile Development</option>
                            <option value="Design">Design</option>
                            <option value="Quality Assurance">Quality Assurance</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Technology Stack <span
                                class="text-danger">*</span></label>
                        <input type="text" name="technology" id="projectTech" list="techList"
                            value="{{ old('technology') }}" class="form-control border-0 bg-light shadow-none"
                            placeholder="PHP, React..." style="border-radius: 10px; height: 48px;" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Work Description</label>
                    <textarea name="description" id="projectDesc" class="form-control border-0 bg-light shadow-none"
                        rows="3" placeholder="Project details..." style="border-radius: 10px;"></textarea>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-bold shadow-sm mt-2"
                    style="background: #3858f9; border: none; height: 52px; border-radius: 10px; letter-spacing: 0.5px;">
                    SAVE PROJECT
                </button>
            </form>
        </div>
    </div>

    <style>
        .bg-soft-primary {
            background: rgba(56, 88, 249, 0.08) !important;
            color: #3858f9;
        }

        .bg-soft-success {
            background: rgba(34, 197, 94, 0.08) !important;
            color: #22c55e;
        }

        .bg-soft-info {
            background: rgba(13, 202, 240, 0.08) !important;
            color: #0dcaf0;
        }

        .bg-soft-danger {
            background: rgba(239, 68, 68, 0.08) !important;
            color: #ef4444;
        }

        .bg-soft-warning {
            background: rgba(245, 158, 11, 0.08) !important;
            color: #f59e0b;
        }

        .bg-soft-secondary {
            background: rgba(100, 116, 139, 0.08) !important;
            color: #64748b;
        }

        .form-control:focus,
        .form-select:focus {
            border: 1.5px solid #3858f9 !important;
            box-shadow: 0 0 0 0.2rem rgba(56, 88, 249, 0.1) !important;
        }

        .table thead th {
            border: none !important;
        }

        .page-link {
            color: #64748b;
            font-weight: 700;
            transition: all 0.2s;
            border-color: #e2e8f0;
            border-radius: 8px !important;
        }

        .active>.page-link {
            background-color: #3858f9 !important;
            border-color: #3858f9 !important;
            color: #ffffff !important;
        }

        .custom-html-content ul {
            list-style-type: disc !important;
            padding-left: 30px !important;
            margin-bottom: 1rem !important;
            list-style-position: outside !important;
            display: block !important;
        }

        .custom-html-content ol {
            list-style-type: decimal !important;
            padding-left: 30px !important;
            margin-bottom: 1rem !important;
            list-style-position: outside !important;
            display: block !important;
        }

        .custom-html-content li {
            display: list-item !important;
            margin-bottom: 0.6rem !important;
            list-style-type: inherit !important;
        }

        .custom-html-content p {
            margin-bottom: 1rem !important;
            line-height: 1.6 !important;
        }

        .custom-html-content {
            text-align: left;
            font-size: 15px;
            line-height: 1.6;
            color: #1e293b;
            padding: 25px 30px 25px 40px !important;
            background: #fff !important;
            border-radius: 12px;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .custom-html-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 8px;
            margin: 10px 0;
            display: block;
        }

        /* Summernote point indentation fix */
        .note-editable ul {
            list-style-type: disc !important;
            padding-left: 30px !important;
            list-style-position: outside !important;
            display: block !important;
        }

        .note-editable ol {
            list-style-type: decimal !important;
            padding-left: 30px !important;
            list-style-position: outside !important;
            display: block !important;
        }

        .note-editable li {
            display: list-item !important;
            list-style-type: inherit !important;
        }

        .note-editable {
            min-height: 200px;
            padding: 20px !important;
            background: white !important;
        }

        /* Smooth Collapse Animation for Filter */
        .collapsing {
            transition: height 0.35s ease-in-out !important;
        }

        /* Prevent shaking/shifting */
        .timer-display,
        .task-timer {
            font-variant-numeric: tabular-nums;
            min-width: 140px;
            display: inline-block;
            white-space: nowrap;
        }

        .project-row {
            transition: background-color 0.2s ease;
        }

        .project-row:hover {
            background-color: #f8fafc !important;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
            background-color: transparent !important;
            border-bottom-width: 1px;
            box-shadow: none !important;
        }
    </style>

    @push('scripts')
        <!-- Summernote CDN -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

        <script>
            function paginateTable() {
                const limitSelect = document.getElementById('entriesLimit');
                const limit = parseInt(limitSelect.value);
                const allRows = document.querySelectorAll('.project-row');
                let visibleCount = 0;

                allRows.forEach((row, index) => {
                    if (row.style.display !== 'none') {
                        row.style.display = (visibleCount < limit) ? '' : 'none';
                        visibleCount++;
                    }
                });

                document.getElementById('entriesInfo').innerText = `Showing 1 to ${Math.min(limit, visibleCount)} of ${allRows.length} entries`;
            }

            document.addEventListener('DOMContentLoaded', paginateTable);

            function editProject(project) {
                // Open Offcanvas
                const offcanvasElement = document.getElementById('projectOffcanvas');
                const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                offcanvas.show();

                document.getElementById('projectName').value = project.name;
                document.getElementById('startDate').value = project.start_date ? project.start_date.substring(0, 10) : '';
                document.getElementById('endDate').value = project.end_date ? project.end_date.substring(0, 10) : '';
                document.getElementById('projectStatus').value = project.status;
                document.getElementById('projectDept').value = project.department;
                try {
                    if ($('#projectDesc').length && typeof $.fn.summernote === 'function') {
                        $('#projectDesc').summernote('code', project.description || '');
                    } else {
                        document.getElementById('projectDesc').value = project.description || '';
                    }
                } catch (e) { console.error('Summernote load error', e); }
                document.getElementById('projectTech').value = project.technology;

                const form = document.getElementById('projectForm');
                form.action = `/projects/${project.id}`;
                document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

                document.getElementById('submitBtn').innerText = 'UPDATE PROJECT';
                document.getElementById('projectOffcanvasLabel').innerText = 'Edit Project Information';
            }

            function resetForm() {
                document.getElementById('projectForm').reset();
                try {
                    if ($('#projectDesc').length && typeof $.fn.summernote === 'function') {
                        $('#projectDesc').summernote('code', '');
                    } else {
                        document.getElementById('projectDesc').value = '';
                    }
                } catch (e) { }
                document.getElementById('projectForm').action = "{{ route('projects.store') }}";
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('submitBtn').innerText = 'SAVE PROJECT';
                document.getElementById('projectOffcanvasLabel').innerText = 'Project Information';
            }

            function resetAllFilters() {
                document.getElementById('projectSearchName').value = '';
                document.getElementById('projectSearch').value = '';
                document.getElementById('projectSearchStatus').value = '';
                document.getElementById('projectSearchDept').value = '';
                filterProjects();
            }

            function filterProjects() {
                const nameSearch = document.getElementById('projectSearchName').value.toLowerCase();
                const headerSearch = document.getElementById('projectSearch').value.toLowerCase();
                const statusSearch = document.getElementById('projectSearchStatus').value;
                const deptSearch = document.getElementById('projectSearchDept').value;

                const rows = document.querySelectorAll('.project-row');

                rows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const status = row.getAttribute('data-status');
                    const dept = row.getAttribute('data-dept');

                    let isMatch = true;

                    if (nameSearch && !name.includes(nameSearch)) isMatch = false;
                    if (headerSearch && !name.includes(headerSearch)) isMatch = false;
                    if (statusSearch && status !== statusSearch) isMatch = false;
                    if (deptSearch && dept !== deptSearch) isMatch = false;

                    row.style.display = isMatch ? '' : 'none';
                });

                paginateTable();
            }

            // Live Timer Logic
            function updateTimers() {
                const now = new Date();
                const timers = document.querySelectorAll('.timer-display');

                timers.forEach(timer => {
                    const end = new Date(timer.getAttribute('data-end'));

                    if (now < end) {
                        let diff = end - now;
                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        diff -= days * (1000 * 60 * 60 * 24);
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        diff -= hours * (1000 * 60 * 60);
                        const mins = Math.floor(diff / (1000 * 60));
                        diff -= mins * (1000 * 60);
                        const secs = Math.floor(diff / 1000);

                        timer.innerHTML = `
                                                                            <span class="text-primary">${days}d</span> 
                                                                            <span class="text-secondary">${hours}h ${mins}m ${secs}s</span>
                                                                            <span class="text-muted small ms-1" style="font-size:9px;">LEFT</span>
                                                                        `;
                    } else {
                        timer.innerHTML = `<span class="text-danger fw-bold"><i class="feather-alert-circle me-1"></i> DEADLINE PASSED</span>`;
                    }
                });
            }

            setInterval(updateTimers, 1000);
            document.addEventListener('DOMContentLoaded', updateTimers);

            function showProjectDesc(id) {
                const html = document.getElementById('proj_desc_' + id).innerHTML;
                Swal.fire({
                    title: 'Project Description',
                    html: `<div class="custom-html-content" style="max-height: 60vh; overflow-y: auto; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">${html}</div>`,
                    showConfirmButton: true,
                    confirmButtonColor: '#3858f9'
                });
            }

            function showProjectTeam(id) {
                const html = document.getElementById('proj_team_' + id).innerHTML;
                Swal.fire({
                    title: '<span style="color: #3858f9; font-weight: 700;">Project Team & Tasks</span>',
                    html: `<div class="mt-3" style="text-align: left;">${html}</div>`,
                    width: '700px',
                    showConfirmButton: true,
                    confirmButtonText: 'CLOSE',
                    confirmButtonColor: '#3858f9'
                });
            }

            $(document).ready(function () {
                $('#projectDesc').summernote({
                    placeholder: 'Enter Project Description',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function (files) {
                            for (let i = 0; i < files.length; i++) {
                                if (files[i].size > 1024 * 1024 * 5) { // 5MB limit
                                    Toast.fire({ icon: 'error', title: 'Image too large (Max 5MB)' });
                                    continue;
                                }
                                let reader = new FileReader();
                                reader.onload = (e) => {
                                    $(this).summernote('insertImage', e.target.result);
                                };
                                reader.readAsDataURL(files[i]);
                            }
                        },
                        onChange: function (contents, $editable) {
                            $('#projectDesc').val(contents);
                        }
                    }
                });

                // CRITICAL: Sync Summernote before form submission
                $('#projectForm').on('submit', function () {
                    if ($('#projectDesc').summernote('isEmpty')) {
                        $('#projectDesc').val('');
                    } else {
                        $('#projectDesc').val($('#projectDesc').summernote('code'));
                    }
                });
            });

            function deleteRecord(e, form) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?', text: "You won't be able to revert this action!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            function updateProjectStatus(id, status) {
                fetch(`/projects/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Toast.fire({ icon: 'success', title: data.success }).then(() => location.reload());
                        } else {
                            Toast.fire({ icon: 'error', title: 'Update failed' });
                        }
                    });
            }

            document.addEventListener('DOMContentLoaded', function () {
                var dropdownEls = document.querySelectorAll('.dropdown-toggle');
                dropdownEls.forEach(function (el) {
                    new bootstrap.Dropdown(el, {
                        boundary: 'viewport',
                        popperConfig: { strategy: 'fixed' }
                    });
                });
            });
        </script>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
                });
            </script>
        @endif
    @endpush
@endsection