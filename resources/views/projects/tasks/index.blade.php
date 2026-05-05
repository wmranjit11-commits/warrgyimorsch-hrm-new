@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10" style="color: #3858f9; font-weight: 700;">Task History</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Daily Tasks</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-primary text-white shadow-sm"
                            style="border-radius: 10px;" data-bs-toggle="offcanvas" data-bs-target="#taskOffcanvas"
                            onclick="resetTaskForm()" title="Create Task">
                            <i class="feather-plus"></i>
                        </a>
                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary shadow-sm"
                            style="border-radius: 10px;" data-bs-toggle="collapse" data-bs-target="#taskFilterSection"
                            title="Filter Tasks">
                            <i class="feather-filter"></i>
                        </a>
                        <a href="javascript:void(0);"
                            class="avatar-text avatar-md bg-soft-secondary text-secondary shadow-sm"
                            style="border-radius: 10px;" onclick="location.reload()" title="Refresh">
                            <i class="feather-refresh-cw"></i>
                        </a>
                        <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger shadow-sm"
                            style="border-radius: 10px;" onclick="bulkDelete()" title="Delete Bulk">
                            <i class="feather-trash-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ main-content ] start -->
    <div class="main-content pt-4">
        <div class="row">
            <div class="col-12">
                <!-- FILTER CARD (Collapsible) -->
                <div id="taskFilterSection" class="collapse">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white;">
                        <div class="card-body p-4">
                            <form action="{{ route('daily-tasks.index') }}" method="GET">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Search By
                                            Project</label>
                                        <select name="project_id" class="form-select border-0 bg-light shadow-none fw-bold"
                                            style="height: 48px; border-radius: 10px; font-size: 14px;">
                                            <option value="">Select Project...</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Search By
                                            Employee Name</label>
                                        <select name="employee_id" class="form-select border-0 bg-light shadow-none fw-bold"
                                            style="height: 48px; border-radius: 10px; font-size: 14px;">
                                            <option value="">Select Employee Name</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Search By
                                            Status</label>
                                        <select name="status" class="form-select border-0 bg-light shadow-none fw-bold"
                                            style="height: 48px; border-radius: 10px; font-size: 14px;">
                                            <option value="">Select Status</option>
                                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="In Process" {{ request('status') == 'In Process' ? 'selected' : '' }}>In Process</option>
                                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On
                                                Hold</option>
                                            <option value="Review" {{ request('status') == 'Review' ? 'selected' : '' }}>
                                                Review</option>
                                            <option value="Rework" {{ request('status') == 'Rework' ? 'selected' : '' }}>
                                                Rework</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">From
                                            Date</label>
                                        <input type="date" name="from_date"
                                            class="form-control border-0 bg-light shadow-none fw-bold"
                                            value="{{ request('from_date') }}" onclick="this.showPicker()"
                                            style="height: 48px; border-radius: 10px; font-size: 14px;">
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Upto
                                            Date</label>
                                        <input type="date" name="upto_date"
                                            class="form-control border-0 bg-light shadow-none fw-bold"
                                            value="{{ request('upto_date') }}" onclick="this.showPicker()"
                                            style="height: 48px; border-radius: 10px; font-size: 14px;">
                                    </div>
                                    <div class="col-md-4 mt-3 d-flex align-items-end gap-2">
                                        <button type="submit"
                                            class="btn btn-primary fw-bold shadow-sm d-flex align-items-center justify-content-center flex-grow-1"
                                            style="background: #3858f9; border: none; height: 48px; border-radius: 10px;">
                                            <i class="feather-search me-1"></i> SEARCH
                                        </button>
                                        <a href="{{ route('daily-tasks.index') }}"
                                            class="btn btn-soft-danger fw-bold d-flex align-items-center justify-content-center"
                                            style="border-radius: 10px; height: 48px; width: 100px; font-size: 14px;">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- DATA TABLE CARD -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                        style="border-radius: 12px 12px 0 0;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Show</span>
                            <select id="entriesLimit" class="form-select select-small border-0 bg-light shadow-none fw-bold"
                                onchange="paginateTable()"
                                style="width: 110px; height: 44px; border-radius: 10px; font-size: 14px; color: #1e293b; padding: 0 12px; line-height: 44px;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">entries</span>
                        </div>
                        <div class="input-group bg-light" style="width: 250px; border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-transparent border-0"><i
                                    class="feather-search text-muted"></i></span>
                            <input type="text" id="taskSearch"
                                class="form-control border-0 bg-transparent shadow-none fw-bold" onkeyup="filterTasks()"
                                placeholder="Search..." style="height: 44px; font-size: 14px;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tasksTable">
                                <thead style="background: #3858f9; color: white;">
                                    <tr style="height: 60px; vertical-align: middle;">
                                        <th class="ps-4" style="width: 60px;"><input type="checkbox" id="selectAllTasks"
                                                class="form-check-input shadow-none"></th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Project.</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Task Title</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Start Date</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Time Tracking</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Priority</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Status</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Assign To</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Assign By</th>
                                        <th class="pe-4 text-center"
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white; white-space: nowrap; width: 220px;">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody style="border-top: 1px solid #f1f5f9;">
                                    @forelse($tasks as $index => $task)
                                        <tr class="task-row" style="height: 70px; border-bottom: 1px solid #f1f5f9;">
                                            <td class="ps-4"><input type="checkbox"
                                                    class="form-check-input task-checkbox shadow-none" value="{{ $task->id }}">
                                            </td>
                                            <td class="fw-bold" style="font-size: 14px; color: #1e293b;">
                                                <div class="d-flex align-items-center">
                                                    @if($task->project)
                                                        <div class="avatar-image border-0 position-relative me-2"
                                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                            <!-- SVG Ring for Task List -->
                                                            <svg width="32" height="32" viewBox="0 0 100 100"
                                                                style="position: absolute; transform: rotate(-90deg);">
                                                                <circle cx="50" cy="50" r="44" fill="none" stroke="#f1f5f9"
                                                                    stroke-width="12"></circle>
                                                                <circle cx="50" cy="50" r="44" fill="none" stroke="#1d4ed8"
                                                                    stroke-width="12" stroke-dasharray="276.46"
                                                                    stroke-dashoffset="{{ 276.46 * (1 - ($task->project->progress ?? 0) / 100) }}"
                                                                    stroke-linecap="round"
                                                                    style="transition: stroke-dashoffset 0.5s ease-in-out;">
                                                                </circle>
                                                            </svg>
                                                            <div class="avatar-text bg-white text-primary rounded-circle shadow-sm"
                                                                style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; z-index: 1; border: 1px solid rgba(0,0,0,0.05); font-size: 10px;">
                                                                <i class="feather-briefcase"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $task->project->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td
                                                style="font-size: 14px; color: #475569; max-width: 200px; white-space: normal; word-break: break-word;">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <div class="fw-bold text-dark">{{ $task->task_title }}</div>
                                                    @php
                                                        $s = $task->status;
                                                        $statusSlug = strtolower(str_replace(' ', '-', $s));
                                                        $statusClass = 'status-' . $statusSlug;
                                                    @endphp
                                                    <span class="badge {{ $statusClass }} px-2 py-1"
                                                        style="font-size: 9px; border-radius: 6px; letter-spacing: 0.3px; text-transform: uppercase;">
                                                        {{ $s }}
                                                    </span>
                                                </div>
                                                @if($task->followUps->where('photo', '!=', null)->count() > 0)
                                                    @php
                                                        $latestPhoto = $task->followUps->where('photo', '!=', null)->sortByDesc('created_at')->first()->photo;
                                                    @endphp
                                                    <div class="mt-2 d-flex justify-content-center">
                                                        <a href="javascript:void(0);"
                                                            onclick="viewAttachmentPopup('{{ asset('storage/' . $latestPhoto) }}')"
                                                            class="premium-attachment-link">
                                                            View Attachment
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="font-size: 14px; color: #475569;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="feather-calendar text-primary" style="font-size: 12px;"></i>
                                                    <span class="fw-bold">{{ $task->start_date->format('d M Y') }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if($task->status != 'Completed' && $task->end_date)
                                                        <div class="task-timer fw-bold text-primary mb-1" style="font-size: 13px;"
                                                            data-end="{{ $task->end_date->toIso8601String() }}">
                                                            Calculating...
                                                        </div>
                                                    @else
                                                        <div class="fw-bold text-success mb-1" style="font-size: 13px;">
                                                            <i class="feather-check-circle me-1"></i> Completed
                                                        </div>
                                                    @endif
                                                    <span class="text-muted d-flex align-items-center gap-1"
                                                        style="font-size: 11px; font-weight: 600;">
                                                        <i class="feather-clock" style="font-size: 10px;"></i>
                                                        Spent: <span class="text-dark">{{ $task->total_time }} hrs</span>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $p = strtolower($task->priority);
                                                    $priorityClass = 'priority-' . ($p == 'hard' ? 'hard' : ($p == 'medium' ? 'medium' : ($p == 'low' ? 'low' : 'normal')));
                                                @endphp
                                                <div class="dropdown">
                                                    <span class="priority-badge {{ $priorityClass }} dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        data-bs-boundary="viewport">
                                                        {{ $task->priority }} <i class="feather-chevron-down fs-10 ms-1"></i>
                                                    </span>
                                                    <ul class="dropdown-menu shadow-lg border-0" style="border-radius: 12px;">
                                                        <li><a class="dropdown-item fw-bold priority-hard py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskPriority({{ $task->id }}, 'Hard')">Hard</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold priority-medium py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskPriority({{ $task->id }}, 'Medium')">Medium</a>
                                                        </li>
                                                        <!-- <li><a class="dropdown-item fw-bold priority-normal py-2 mb-1 rounded mx-2" href="javascript:void(0);" onclick="updateTaskPriority({{ $task->id }}, 'Normal')">Normal</a></li> -->
                                                        <li><a class="dropdown-item fw-bold priority-low py-2 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskPriority({{ $task->id }}, 'Low')">Low</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $s = $task->status;
                                                    $statusSlug = strtolower(str_replace(' ', '-', $s));
                                                    $statusClass = 'status-' . $statusSlug;
                                                @endphp
                                                <div class="dropdown premium-status-dropdown">
                                                    <button class="btn-status {{ $statusClass }} dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        data-bs-boundary="viewport">
                                                        {{ $s }} <i class="feather-chevron-down fs-10 ms-1"></i>
                                                    </button>
                                                    <ul class="dropdown-menu shadow-lg border-0"
                                                        style="border-radius: 12px; z-index: 99999 !important;">
                                                        <li><a class="dropdown-item fw-bold status-pending py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskStatus({{ $task->id }}, 'Pending')">Pending</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold status-in-process py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskStatus({{ $task->id }}, 'In Process')">In
                                                                Process</a></li>
                                                        <li><a class="dropdown-item fw-bold status-completed py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskStatus({{ $task->id }}, 'Completed')">Completed</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold status-on-hold py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskStatus({{ $task->id }}, 'On Hold')">On
                                                                Hold</a></li>
                                                        <li><a class="dropdown-item fw-bold status-review py-2 mb-1 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskStatus({{ $task->id }}, 'Review')">Review</a>
                                                        </li>
                                                        <li><a class="dropdown-item fw-bold status-rework py-2 rounded mx-2"
                                                                href="javascript:void(0);"
                                                                onclick="updateTaskStatus({{ $task->id }}, 'Rework')">Rework</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td style="font-size: 14px; color: #475569;">
                                                {{ $task->employee ? $task->employee->name : '-' }}
                                            </td>
                                            <td style="font-size: 14px; color: #475569;">
                                                {{ $task->creator ? $task->creator->name : '-' }}
                                            </td>
                                            <td class="pe-4 text-center" style="white-space: nowrap; width: 220px;">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-secondary text-secondary rounded"
                                                        title="View Details & History" onclick="showTaskDesc({{ $task->id }})">
                                                        <i class="feather-file-text"></i>
                                                    </a>
                                                    <template id="task_desc_{{ $task->id }}">
                                                        <div class="p-2">
                                                            <div class="mb-4">
                                                                <h6
                                                                    class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                                                    <i class="feather-info"></i> Original Task Description
                                                                </h6>
                                                                <div class="p-3 bg-white rounded border"
                                                                    style="font-size: 14px; min-height: 100px;">
                                                                    {!! $task->description ?? '<span class="text-muted">No description provided.</span>' !!}
                                                                </div>
                                                            </div>

                                                            <hr class="my-4">

                                                            <div class="mb-3">
                                                                <h6
                                                                    class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                                                    <i class="feather-clock"></i> Work Progress History
                                                                </h6>
                                                                @if($task->followUps->count() > 0)
                                                                    <div class="timeline-container px-2">
                                                                        @foreach($task->followUps->sortByDesc('created_at') as $fu)
                                                                            <div class="mb-4 ps-4 position-relative"
                                                                                style="border-left: 2px dashed #cbd5e1;">
                                                                                <div class="position-absolute"
                                                                                    style="left: -9px; top: 0; width: 16px; height: 16px; background: #3858f9; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #3858f920;">
                                                                                </div>
                                                                                <div class="card border-0 shadow-sm"
                                                                                    style="border-radius: 12px; background: #f8fafc;">
                                                                                    <div class="card-body p-3">
                                                                                        <div
                                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                                            <span
                                                                                                class="fw-bold text-dark small">{{ $fu->reference_name ?? 'Employee' }}</span>
                                                                                            <span
                                                                                                class="badge bg-soft-primary text-primary small">{{ $fu->created_at->format('d M, Y') }}</span>
                                                                                        </div>
                                                                                        <div class="text-muted small mb-2">
                                                                                            {!! $fu->work_description !!}
                                                                                        </div>
                                                                                        <div
                                                                                            class="d-flex align-items-center gap-2 mt-2">
                                                                                            <span
                                                                                                class="badge bg-soft-dark text-dark fw-bold"
                                                                                                style="font-size: 10px;">
                                                                                                <i class="feather-clock me-1"></i>
                                                                                                {{ $fu->time_taken }} hrs
                                                                                            </span>
                                                                                            @if($fu->photo)
                                                                                                <a href="javascript:void(0);"
                                                                                                    onclick="viewAttachmentPopup('{{ asset('storage/' . $fu->photo) }}')"
                                                                                                    class="badge bg-soft-info text-info text-decoration-none">
                                                                                                    <i class="feather-image"></i> View Image
                                                                                                </a>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-soft-secondary py-3 text-center"
                                                                        style="border-radius: 12px;">
                                                                        <i class="feather-info me-2"></i> No history available for
                                                                        this task.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-primary text-primary rounded"
                                                        title="Edit" onclick="editTask({{ json_encode($task) }})">
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <form action="{{ route('daily-tasks.destroy', $task->id) }}" method="POST"
                                                        class="delete-form d-inline" onsubmit="deleteRecord(event, this)">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="avatar-text avatar-md bg-soft-danger text-danger rounded border-0"
                                                            title="Delete Task">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                    @php
                                                        $canAddProgress = (strtoupper(auth()->user()->role) === 'ADMIN' || strtoupper(auth()->user()->role) === 'SUPER ADMIN' || auth()->user()->employee_id == $task->employee_id);
                                                    @endphp

                                                    @if($canAddProgress)
                                                        <a href="javascript:void(0);"
                                                            class="avatar-text avatar-md bg-soft-info text-info rounded"
                                                            title="Add Work Progress" data-bs-toggle="modal"
                                                            data-bs-target="#followUpModal"
                                                            onclick="openFollowUpModal({{ $task->id }}, '{{ addslashes($task->project->name ?? 'N/A') }}', 'add', '{{ addslashes($task->task_title) }}', {{ $task->employee_id }}, '{{ addslashes($task->employee->name ?? '') }}')">
                                                            <i class="feather-plus-circle"></i>
                                                        </a>
                                                    @endif

                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-dark text-dark rounded"
                                                        title="View Work History" data-bs-toggle="modal"
                                                        data-bs-target="#followUpModal"
                                                        onclick="openFollowUpModal({{ $task->id }}, '{{ addslashes($task->project->name ?? 'N/A') }}', 'history', '{{ addslashes($task->task_title) }}')">
                                                        <i class="feather-clock"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">No tasks found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- ARROW PAGINATION -->
                        <div class="px-4 py-4 border-top d-flex justify-content-between align-items-center bg-white"
                            style="border-radius: 0 0 12px 12px;">
                            <div class="small text-muted fw-bold" id="entriesInfo" style="font-size: 14px;">Showing 1 to
                                {{ $tasks->count() }} of {{ $tasks->count() }} entries
                            </div>
                            <nav>
                                <ul class="pagination pagination-md mb-0 gap-1" id="paginationList">
                                    <li class="page-item disabled mx-1"><a
                                            class="page-link border rounded d-flex align-items-center justify-content-center text-muted shadow-none"
                                            href="#" style="width: 40px; height: 40px;"><i
                                                class="feather-chevron-left"></i></a></li>
                                    <li class="page-item active mx-1"><a
                                            class="page-link border rounded d-flex align-items-center justify-content-center text-white shadow-sm"
                                            href="#"
                                            style="background: #3858f9; border-color: #3858f9; width: 40px; height: 40px; font-weight: 700;">1</a>
                                    </li>
                                    <li class="page-item disabled mx-1"><a
                                            class="page-link border rounded d-flex align-items-center justify-content-center text-muted shadow-none"
                                            href="#" style="width: 40px; height: 40px;"><i
                                                class="feather-chevron-right"></i></a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- SIDE PANEL: CREATE/EDIT TASK -->
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="taskOffcanvas" aria-labelledby="taskOffcanvasLabel"
        style="width: 50% !important;">
        <div class="offcanvas-header text-white p-4" style="background: #3858f9;">
            <h5 class="offcanvas-title fw-bold" id="taskOffcanvasLabel" style="color: #ffffff !important;">Create Task</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4" style="background: #f8fafc;">
            <form id="taskForm">
                @csrf
                <div id="methodField"></div>
                <input type="hidden" name="id" id="taskId">

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Project <span
                                class="text-danger">*</span></label>
                        <select name="project_id" id="taskProjectId"
                            class="form-select premium-select" data-placeholder="Select Project..." required>
                            <option value="">Select Project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Task Title <span
                                class="text-danger">*</span></label>
                        <input type="text" name="task_title" id="taskTitle"
                            class="form-control premium-input" placeholder="Enter Task Title..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Start Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="taskStartDate"
                            class="form-control premium-input" onclick="this.showPicker()" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">End Date</label>
                        <input type="date" name="end_date" id="taskEndDate"
                            class="form-control premium-input" onclick="this.showPicker()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Priority <span
                                class="text-danger">*</span></label>
                        <select name="priority" id="taskPriority" class="form-select premium-select" data-placeholder="Select Priority..." required>
                            <option value="">Select priority...</option>
                            <option value="Hard">Hard</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Status</label>
                        <select name="status" id="taskStatus" class="form-select premium-select" data-placeholder="Select Status..." required>
                            <option value="Pending">Pending</option>
                            <option value="In Process">In Process</option>
                            <option value="Completed">Completed</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Review">Review</option>
                            <option value="Rework">Rework</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Assign To <span
                                class="text-danger">*</span></label>
                        <select name="employee_id" id="taskEmployeeId"
                            class="form-select premium-select" data-placeholder="Select Employee..." required>
                            @if(count($employees) > 1)
                                <option value="">Employee name</option>
                            @endif
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ count($employees) == 1 ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Task Description</label>
                        <textarea name="description" id="taskDesc"
                            class="form-control premium-input" rows="3"
                            placeholder="Enter detailed task description..."></textarea>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="button" id="submitTaskBtn" class="btn btn-primary w-100 fw-bold shadow-sm"
                        style="background: #3858f9; border: none; height: 56px; border-radius: 12px; font-size: 16px; letter-spacing: 0.5px;">SUBMIT
                        TASK</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TASK FOLLOW-UP MODAL (HISTORY DESCRIPTION) -->
    <div class="modal border-0" id="followUpModal" tabindex="-1" aria-hidden="true"
        style="backdrop-filter: none !important;">
        <div class="modal-dialog modal-dialog-centered"
            style="max-width: 55%; min-width: 800px; transform: none !important; transition: none !important;">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 16px; overflow: hidden; filter: none !important; -webkit-filter: none !important; transform: none !important;">
                <div class="modal-header text-white p-3" style="background: #3858f9; border: none !important;">
                    <h5 class="modal-title fw-bold" id="followUpModalLabel" style="color: #ffffff !important;">Work History
                    </h5>
                    <span id="followUpTaskTitle" class="badge bg-white text-primary ms-2 fw-bold"
                        style="font-size: 11px; padding: 5px 10px; border-radius: 6px; letter-spacing: 0.5px; text-transform: uppercase;"></span>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" style="background-color: #f8fafc !important; transform: none !important;">
                    <div class="row g-4">
                        <!-- ADD FOLLOW UP FORM (LEFT) -->
                        <div class="col-lg-5 d-none" id="followUpFormColumn">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-body p-4">
                                    <form id="followUpForm" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="daily_task_id" id="followUpTaskId">
                                        <input type="hidden" name="time_taken" id="totalFollowUpHours" value="0">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Performed
                                                By</label>
                                            <div class="p-3 rounded-3 bg-soft-primary border border-primary border-opacity-10 d-flex align-items-center">
                                                <div class="avatar-text avatar-sm bg-primary text-white rounded-circle me-3" id="followUpEmpInitial">M</div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-14" id="followUpEmpNameDisplay">...</div>
                                                    <div class="text-muted small" style="font-size: 11px;">Assigned Task Owner</div>
                                                </div>
                                                <input type="hidden" name="reference_name" id="followUpEmployee">
                                            </div>
                                        </div>

                                        <!-- QUICK TASK ADDER -->
                                        <div class="row g-2 mb-3 p-2 rounded"
                                            style="background: #f1f5f9; border: 1px dashed #cbd5e1;">
                                            <div class="col-6">
                                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Sub-Task</label>
                                                <input type="text" id="quickTaskTitle"
                                                    class="form-control premium-input shadow-none" placeholder="Task Title..."
                                                    style="height: 35px !important; border-radius: 8px !important; font-size: 12px !important;">
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label fw-bold fs-10 text-muted text-uppercase mb-1">Hrs</label>
                                                <input type="text" id="quickTaskHours"
                                                    class="form-control premium-input shadow-none" placeholder="Hrs"
                                                    style="height: 35px !important; border-radius: 8px !important; font-size: 12px !important;">
                                            </div>
                                            <div class="col-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-primary w-100 p-0 fw-bold"
                                                    onclick="addQuickTaskToDesc()"
                                                    style="height: 35px; border-radius: 8px; font-size: 10px; background: #3858f9;">
                                                    ADD
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Work
                                                Description <span class="text-danger">*</span></label>
                                            <textarea name="work_description" id="workDesc"
                                                class="form-control premium-input" rows="3"
                                                placeholder="Enter detailed work progress description..." required></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">Upload
                                                Attachment (Image/PDF/Doc)</label>
                                            <input type="file" name="photo" id="photoInput"
                                                class="form-control premium-input"
                                                onchange="previewImage(this)"
                                                accept=".jpeg,.jpg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                                            <!-- REAL-TIME PREVIEW AREA -->
                                            <div id="previewContainer" class="mt-3 d-none"
                                                style="position: relative; width: 100%; height: auto; min-height: 50px; border-radius: 12px; overflow: hidden; border: 2px dashed #e2e8f0; padding: 10px; background: #f8fafc; text-align: center;">
                                                <img id="photoPreview" src="#" alt="Preview"
                                                    style="width: 100%; max-height: 180px; object-fit: contain !important; border-radius: 8px; display: none;">
                                                <div id="documentPreview" class="fw-bold text-primary"
                                                    style="display: none; padding: 20px;">
                                                    <i class="feather-file-text me-2" style="font-size: 24px;"></i> Document
                                                    Selected
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger rounded-circle"
                                                    onclick="removePreview()"
                                                    style="position: absolute; top: 10px; right: 10px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                                                    <i class="feather-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="submit" id="submitReplyBtn"
                                            class="btn btn-primary w-100 fw-bold shadow-sm"
                                            style="background: #3858f9; border: none; height: 52px; border-radius: 12px; font-size: 15px;">SUBMIT
                                            REPLY</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- FOLLOW UP HISTORY TABLE (RIGHT) -->
                        <div class="col-lg-7" id="followUpHistoryColumn">
                            <div class="card border-0 shadow-sm overflow-hidden"
                                style="border-radius: 12px; background: #ffffff; min-height: 500px;">
                                <div class="card-header bg-white border-bottom py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small fw-bold text-uppercase"
                                                style="font-size: 10px; letter-spacing: 0.5px;">Show</span>
                                            <select id="modalEntriesLimit"
                                                class="form-select select-small border-0 bg-light shadow-none fw-bold"
                                                onchange="changeModalEntries()"
                                                style="width: 95px; height: 36px; font-size: 13px; border-radius: 8px; padding: 0 10px; cursor: pointer;">
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                            </select>
                                            <span class="text-muted small fw-bold text-uppercase"
                                                style="font-size: 10px; letter-spacing: 0.5px;">entries</span>
                                        </div>
                                        <div class="input-group bg-light"
                                            style="width: 180px; border-radius: 8px; overflow: hidden;">
                                            <span class="input-group-text bg-transparent border-0 py-0"><i
                                                    class="feather-search text-muted"></i></span>
                                            <input type="text" id="modalSearch"
                                                class="form-control border-0 bg-transparent shadow-none fw-bold"
                                                placeholder="Search..." onkeyup="filterModalHistory()"
                                                style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="modalTableContainer"
                                        style="max-height: 450px; overflow-y: auto; overflow-x: hidden; width: 100%;">
                                        <table class="table table-hover align-middle mb-0"
                                            style="width: 100%; table-layout: fixed;">
                                            <thead
                                                style="background: #3858f9; color: white; position: sticky; top: 0; z-index: 1;">
                                                <tr style="height: 52px; vertical-align: middle;">
                                                    <th class="ps-4"
                                                        style="font-size: 11px; text-transform: uppercase; color: #ffffff !important; width: 50px;">
                                                        #</th>
                                                    <th
                                                        style="font-size: 11px; text-transform: uppercase; color: #ffffff !important;">
                                                        Employee Name</th>
                                                    <th
                                                        style="font-size: 11px; text-transform: uppercase; color: #ffffff !important; width: 120px;">
                                                        Time Spent</th>
                                                    <th
                                                        style="font-size: 11px; text-transform: uppercase; color: #ffffff !important; width: 110px;">
                                                        Date</th>
                                                    <th class="pe-3 text-center"
                                                        style="font-size: 11px; text-transform: uppercase; color: #ffffff !important; width: 100px;">
                                                        Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="followUpHistoryBody">
                                                <!-- Loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- NUMBERED PAGINATION FOR MODAL -->
                                    <div id="modalPaginationContainer"
                                        class="px-3 py-3 border-top d-flex justify-content-between align-items-center bg-white">
                                        <div class="small text-muted fw-bold" id="modalEntriesInfo"
                                            style="font-size: 12px;">Showing 0 to 0 of 0 entries</div>
                                        <div class="d-flex gap-1 align-items-center" id="modalPaginationButtons">
                                            <!-- Numbered buttons injection -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CUSTOM ATTACHMENT VIEWER MODAL -->
        <div id="customAttachmentModal"
            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background:rgba(0,0,0,0.7); align-items:center; justify-content:center; backdrop-filter: blur(4px);">
            <div
                style="position:relative; width:85%; max-width:1000px; background:#ffffff; border-radius:16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column;">
                <div
                    style="padding: 15px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <h5 class="fw-bold text-primary m-0" style="font-size: 18px;"><i
                            class="feather-paperclip me-2"></i>Attached File</h5>
                    <button onclick="document.getElementById('customAttachmentModal').style.display='none'"
                        style="background:none; border:none; font-size:28px; line-height: 1; cursor:pointer; color: #64748b;">&times;</button>
                </div>
                <div id="customAttachmentContent" style="padding: 20px; text-align:center; overflow:hidden;"></div>
            </div>
        </div>
@endsection

    @push('scripts')
        <!-- Summernote CDN -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

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

            .bg-soft-warning {
                background: rgba(245, 158, 11, 0.08) !important;
                color: #f59e0b;
            }

            .bg-soft-danger {
                background: rgba(239, 68, 68, 0.08) !important;
                color: #ef4444;
            }

            .bg-soft-secondary {
                background: rgba(100, 116, 139, 0.08) !important;
                color: #64748b;
            }

            .form-control:focus,
            .form-select:focus {
                border: 1.5px solid #3858f9 !important;
                box-shadow: 0 0 0 0.2rem rgba(56, 88, 249, 0.1) !important;
                background-color: #ffffff !important;
            }

            .form-select {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%233858f9' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
                background-size: 12px 12px !important;
                background-position: right 1rem center !important;
                cursor: pointer;
            }

            .form-control,
            .form-select {
                transition: all 0.2s ease-in-out !important;
                border: 1px solid #e2e8f0 !important;
            }

            .form-control:hover,
            .form-select:hover {
                border-color: #cbd5e1 !important;
                background-color: #f1f5f9 !important;
            }

            .modal {
                transform: none !important;
                filter: none !important;
                position: fixed !important;
            }

            .modal-backdrop {
                background-color: rgba(15, 23, 42, 0.75) !important;
                transition: none !important;
            }

            .modal-content {
                filter: none !important;
                border: none !important;
                transform: none !important;
            }

            .page-link {
                color: #64748b;
                font-weight: 700;
                transition: all 0.2s;
                border: 1px solid #e2e8f0 !important;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 38px;
                height: 38px;
                border-radius: 8px !important;
            }

            /* Prevent shaking/shifting */
            .timer-display,
            .task-timer {
                font-variant-numeric: tabular-nums;
                min-width: 140px;
                display: inline-block;
                white-space: nowrap;
            }

            .task-row {
                transition: background-color 0.2s ease;
            }

            .task-row:hover {
                background-color: #f8fafc !important;
            }

            .table> :not(caption)>*>* {
                background-color: transparent !important;
                box-shadow: none !important;
            }

            .active>.page-link {
                background-color: #3858f9 !important;
                border-color: #3858f9 !important;
                color: #ffffff !important;
            }

            .page-link:hover:not(.text-white) {
                border-color: #3858f9 !important;
                color: #3858f9;
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
                margin-bottom: 1.1rem !important;
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

            .custom-html-content img {
                max-width: 100% !important;
                height: auto !important;
                border-radius: 8px;
                margin: 10px 0;
                display: block;
            }

            .custom-html-content ol {
                padding-left: 25px !important;
                list-style-type: decimal !important;
                margin-bottom: 15px;
            }

            .custom-html-content ul {
                padding-left: 25px !important;
                list-style-type: disc !important;
                margin-bottom: 15px;
            }

            .custom-html-content p {
                margin-bottom: 10px;
            }

            .custom-html-content {
                text-align: left;
                font-size: 15px;
                line-height: 1.6;
                color: #1e293b;
                word-wrap: break-word;
                word-break: break-word;
                overflow-wrap: break-word;
                padding: 15px 20px 15px 25px !important;
                background: #fff !important;
                overflow-x: hidden !important;
                width: 100%;
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
                padding: 25px !important;
                background: white !important;
            }

            /* Smooth Collapse Animation for Filter */
            .collapse {
                transition: height 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .collapsing {
                transition: height 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
                height: 0;
                overflow: hidden;
            }
        </style>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <style>
            /* Premium Select2 Theme */
            .select2-container--default .select2-selection--single {
                background-color: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 10px !important;
                height: 48px !important;
                display: flex !important;
                align-items: center !important;
                padding: 0 12px !important;
                transition: all 0.2s ease !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #1e293b !important;
                font-weight: 700 !important;
                font-size: 14px !important;
                padding-left: 0 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 48px !important;
                right: 12px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow b {
                border-color: #3858f9 transparent transparent transparent !important;
                border-width: 6px 4px 0 4px !important;
            }

            .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
                border-color: transparent transparent #3858f9 transparent !important;
                border-width: 0 4px 6px 4px !important;
            }

            .select2-dropdown {
                border: none !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
                border-radius: 12px !important;
                padding: 8px !important;
                background: #ffffff !important;
                z-index: 1070 !important;
            }

            .select2-results__option {
                padding: 10px 15px !important;
                border-radius: 8px !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                color: #475569 !important;
                margin-bottom: 2px !important;
            }

            .select2-results__option--highlighted[aria-selected] {
                background-color: #3858f9 !important;
                color: #ffffff !important;
            }

            .select2-results__option[aria-selected=true] {
                background-color: #f1f5f9 !important;
                color: #3858f9 !important;
            }

            .select2-search--dropdown {
                padding: 10px !important;
            }

            .select2-search--dropdown .select2-search__field {
                border: 1px solid #e2e8f0 !important;
                border-radius: 8px !important;
                padding: 8px 12px !important;
                background: #f8fafc !important;
                font-weight: 600 !important;
            }

            /* Remove global z-index to prevent backdrop overlap */
            .select2-container {
                z-index: auto !important;
            }
        </style>

        <script>
            let globalFollowUps = [];
            let modalCurrentPage = 1;
            let modalPageSize = 5;
            let myFollowUpModal = null;
            let currentModalEmployeeFilter = 'All';

            document.addEventListener('DOMContentLoaded', function () {
                myFollowUpModal = new bootstrap.Modal(document.getElementById('followUpModal'));

                // Initialize Select2 with Premium Styling
                if (window.jQuery && $.fn.select2) {
                    $('.form-select:not(.select-small)').select2({
                        width: '100%',
                        placeholder: function () { return $(this).attr('placeholder') || 'Select Option'; },
                        dropdownParent: $('body')
                    });

                    $('.select-small').select2({
                        width: 'element',
                        minimumResultsForSearch: Infinity,
                        dropdownParent: $('body')
                    });

                    // Re-init for specific containers if needed
                    $('#taskOffcanvas, #followUpModal').on('shown.bs.modal shown.bs.offcanvas', function () {
                        $(this).find('.form-select').select2({
                            width: '100%',
                            dropdownParent: $(this)
                        });
                    });
                }

                // Robust Backdrop Cleanup
                document.getElementById('followUpModal').addEventListener('hidden.bs.modal', function () {
                    const backdrops = document.getElementsByClassName('modal-backdrop');
                    while (backdrops.length > 0) {
                        backdrops[0].parentNode.removeChild(backdrops[0]);
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });

                // Fix dropdown clipping globally for this page
                var dropdownEls = document.querySelectorAll('.dropdown-toggle');
                dropdownEls.forEach(function (el) {
                    new bootstrap.Dropdown(el, {
                        boundary: 'viewport',
                        popperConfig: { strategy: 'fixed' }
                    });
                });
            });

            function previewImage(input) {
                const preview = document.getElementById('photoPreview');
                const docPreview = document.getElementById('documentPreview');
                const container = document.getElementById('previewContainer');

                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const isImage = file.type.startsWith('image/');

                    container.classList.remove('d-none');

                    if (isImage) {
                        preview.style.display = 'block';
                        docPreview.style.display = 'none';
                        const reader = new FileReader();
                        reader.onload = function (e) { preview.src = e.target.result; }
                        reader.readAsDataURL(file);
                    } else {
                        preview.style.display = 'none';
                        docPreview.style.display = 'block';
                        docPreview.innerHTML = `<div class="d-flex flex-column align-items-center justify-content-center p-3">
                                                <i class="feather-file-text mb-2" style="font-size: 32px; color: #3858f9;"></i>
                                                <span class="text-dark small">${file.name}</span>
                                            </div>`;
                    }
                }
            }

            function removePreview() {
                document.getElementById('photoInput').value = '';
                document.getElementById('previewContainer').classList.add('d-none');
            }

            function bulkDelete() {
                const checked = Array.from(document.querySelectorAll('.task-checkbox:checked')).map(cb => cb.value);
                if (checked.length === 0) { Toast.fire({ icon: 'warning', title: 'Please select tasks to delete.' }); return; }
                Swal.fire({
                    title: 'Are you sure?', text: `You are about to delete ${checked.length} tasks!`, icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route("daily-tasks.bulk-delete") }}', {
                            method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ ids: checked })
                        }).then(res => res.json()).then(data => { if (data.success) { Toast.fire({ icon: 'success', title: data.success }).then(() => location.reload()); } });
                    }
                });
            }

            function editTask(task) {
                document.getElementById('taskForm').reset();
                document.getElementById('taskOffcanvasLabel').innerText = 'Edit Task';
                document.getElementById('submitTaskBtn').innerText = 'UPDATE TASK';

                // Set basic fields
                document.getElementById('taskId').value = task.id || '';
                document.getElementById('taskTitle').value = task.task_title || '';
                document.getElementById('taskStartDate').value = task.start_date ? task.start_date.substring(0, 10) : '';
                document.getElementById('taskEndDate').value = task.end_date ? task.end_date.substring(0, 10) : '';
                document.getElementById('taskPriority').value = task.priority || '';
                document.getElementById('taskStatus').value = task.status || 'In Process';

                // Set Select fields (Project & Employee)
                if (window.jQuery && $.fn.select2) {
                    $('#taskProjectId').val(task.project_id).trigger('change');
                    $('#taskEmployeeId').val(task.employee_id).trigger('change');
                    $('#taskPriority').val(task.priority).trigger('change');
                    $('#taskStatus').val(task.status).trigger('change');
                } else {
                    const projSelect = document.getElementById('taskProjectId');
                    if (projSelect) projSelect.value = task.project_id || '';

                    const empSelect = document.getElementById('taskEmployeeId');
                    if (empSelect) empSelect.value = task.employee_id || '';
                }

                // Summernote description
                try {
                    const desc = task.description || '';
                    if ($('#taskDesc').length && typeof $.fn.summernote === 'function') {
                        $('#taskDesc').summernote('code', desc);
                    } else {
                        document.getElementById('taskDesc').value = desc;
                    }
                } catch (e) {
                    console.error('Summernote load error', e);
                    if (document.getElementById('taskDesc')) document.getElementById('taskDesc').value = task.description || '';
                }

                // Form action and method
                document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                const formObj = document.getElementById('taskForm');
                if (formObj) formObj.action = `/daily-tasks/${task.id}`;

                // Show Offcanvas
                const offElement = document.getElementById('taskOffcanvas');
                if (offElement) {
                    const bOff = bootstrap.Offcanvas.getInstance(offElement) || new bootstrap.Offcanvas(offElement);
                    bOff.show();
                }
            }

            function resetTaskForm() {
                document.getElementById('taskForm').reset();
                document.getElementById('taskForm').action = `{{ route('daily-tasks.store') }}`;
                document.getElementById('taskOffcanvasLabel').innerText = 'Create Task';
                document.getElementById('submitTaskBtn').innerText = 'SUBMIT TASK';
                document.getElementById('taskId').value = '';
                document.getElementById('methodField').innerHTML = '';

                document.getElementById('taskForm').querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.getElementById('taskForm').querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                try {
                    if ($('#taskDesc').length && typeof $.fn.summernote === 'function') {
                        $('#taskDesc').summernote('code', '');
                    } else {
                        document.getElementById('taskDesc').value = '';
                    }
                    if (window.jQuery && $.fn.select2) {
                        $('.form-select').val('').trigger('change');
                    }
                } catch (e) { }
            }

            function openFollowUpModal(taskId, taskProjectName, mode = 'history', taskTitle = '', assignedEmpId = null, assignedEmpName = '') {
                document.getElementById('followUpTaskId').value = taskId;

                // Display both Project and Task Title in header
                let headerTitle = taskProjectName;
                if (taskTitle) {
                    headerTitle += ` | Task: ${taskTitle}`;
                }
                document.getElementById('followUpTaskTitle').innerText = headerTitle;
                document.getElementById('followUpForm').reset();
                document.getElementById('totalFollowUpHours').value = 0;
                try { $('#workDesc').summernote('code', ''); } catch (e) { }
                removePreview();
                modalCurrentPage = 1;
                loadFollowUpHistory(taskId);

                const formCol = document.getElementById('followUpFormColumn');
                const historyCol = document.getElementById('followUpHistoryColumn');
                const modalDialog = document.querySelector('#followUpModal .modal-dialog');

                if (mode === 'add') {
                    formCol.classList.remove('d-none');
                    historyCol.classList.remove('col-lg-12');
                    historyCol.classList.add('col-lg-7');
                    modalDialog.classList.add('modal-xl');
                    document.getElementById('followUpModalLabel').innerText = 'Add Work Progress';

                    // Display assigned employee name and lock it
                    if (assignedEmpName) {
                        const empDisplay = document.getElementById('followUpEmpNameDisplay');
                        const empInitial = document.getElementById('followUpEmpInitial');
                        const empHidden = document.getElementById('followUpEmployee');
                        
                        if (empDisplay) empDisplay.innerText = assignedEmpName;
                        if (empInitial) empInitial.innerText = assignedEmpName.charAt(0).toUpperCase();
                        if (empHidden) empHidden.value = assignedEmpName;
                    }
                } else {
                    formCol.classList.add('d-none');
                    historyCol.classList.remove('col-lg-7');
                    historyCol.classList.add('col-lg-12');
                    modalDialog.classList.remove('modal-xl');
                    document.getElementById('followUpModalLabel').innerText = 'Work History';
                }
            }

            function loadFollowUpHistory(taskId) {
                fetch(`/daily-tasks/${taskId}/follow-ups`)
                    .then(res => res.json())
                    .then(data => {
                        globalFollowUps = data;
                        renderModalTable();
                    });
            }

            function addQuickTaskToDesc() {
                const titleInput = document.getElementById('quickTaskTitle');
                const hoursInput = document.getElementById('quickTaskHours');
                const title = titleInput.value;
                const hours = hoursInput.value;

                if (!title) { Toast.fire({ icon: 'warning', title: 'Enter sub-task name' }); return; }

                // 1. Calculate and Update Hidden Total Hours field
                const hiddenHoursField = document.getElementById('totalFollowUpHours');
                let currentTotal = parseFloat(hiddenHoursField.value) || 0;
                let addedHours = parseFloat(hours) || 0;
                hiddenHoursField.value = currentTotal + addedHours;

                // 2. Format HTML - Prepend to the TOP using Real Bullets and Numbered List for details
                const timeStr = hours ? ` — <b style="color: #3858f9;">${hours} ${isNaN(hours) ? '' : 'Hours'}</b>` : '';
                const html = `<div class="mb-4" style="border-left: 4px solid #3858f9; padding-left: 20px;">
                                                    <p class="mb-2" style="font-size: 16px; color: #1e293b;"><strong>• ${title.toUpperCase()}</strong>${timeStr}</p>
                                                    <ol class="text-muted" style="font-size: 14px; line-height: 1.7;">
                                                        <li>&nbsp;</li>
                                                    </ol>
                                                  </div><hr style="border-top: 2px solid #f1f5f9; margin: 20px 0;">`;

                if ($('#workDesc').length && typeof $.fn.summernote === 'function') {
                    const currentContent = $('#workDesc').summernote('code');
                    // Prepend new task to the TOP
                    $('#workDesc').summernote('code', html + currentContent);
                } else {
                    const el = document.getElementById('workDesc');
                    el.value = html + el.value;
                }

                // Clear inputs
                titleInput.value = '';
                hoursInput.value = '';

                Toast.fire({ icon: 'success', title: `Task added. Total: ${hiddenHoursField.value} hrs` });
            }

            function renderModalTable() {
                const body = document.getElementById('followUpHistoryBody');
                const searchTerm = document.getElementById('modalSearch').value.toLowerCase();

                let filtered = globalFollowUps.filter(fu => {
                    const matchesSearch = fu.work_description.toLowerCase().includes(searchTerm) || (fu.reference_name && fu.reference_name.toLowerCase().includes(searchTerm));
                    return matchesSearch;
                });

                const totalItems = filtered.length;
                const totalPages = Math.ceil(totalItems / modalPageSize) || 1;
                if (modalCurrentPage > totalPages) modalCurrentPage = totalPages;
                const startIdx = (modalCurrentPage - 1) * modalPageSize;
                const paginated = filtered.slice(startIdx, startIdx + modalPageSize);

                body.innerHTML = '';
                paginated.forEach((fu, index) => {
                    let timeDisplay = fu.time_taken || '-';
                    if (fu.time_taken && !isNaN(fu.time_taken)) {
                        timeDisplay = fu.time_taken + ' Hours';
                    }

                    let nameHtml = `<span class="fw-bold text-dark" style="font-size: 14px;"><i class="feather-user me-1 text-primary"></i>${fu.reference_name || 'Anonymous'}</span>`;
                    let viewBtn = `<a href="javascript:void(0);" onclick="toggleFollowUpRow(${fu.id}, this)" class="avatar-text avatar-md bg-soft-info text-info rounded-circle shadow-none me-2" title="View Details" style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-decoration:none;"><i class="feather-eye" style="font-size:14px;"></i></a>`;
                    let delBtn = `<a href="javascript:void(0);" onclick="deleteFollowUp(${fu.id})" class="avatar-text avatar-md bg-soft-danger text-danger rounded-circle shadow-none" title="Delete" style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-decoration:none;"><i class="feather-trash-2" style="font-size:14px;"></i></a>`;

                    body.innerHTML += `
                                                            <tr style="height: 70px; border-bottom: 1px solid #f1f5f9; background: white;">
                                                                <td class="ps-4 fw-bold text-dark" style="font-size: 14px;">${startIdx + index + 1}</td>
                                                                <td style="font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                    ${nameHtml}
                                                                </td>
                                                                <td style="font-size: 14px; font-weight: 700; color: #475569; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                    ${timeDisplay}
                                                                </td>
                                                                <td style="font-size: 13px; white-space: nowrap;">
                                                                    <div class="fw-bold text-dark">${new Date(fu.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                                                                </td>
                                                                <td class="pe-3 text-center">
                                                                    <div class="d-flex align-items-center justify-content-center">
                                                                        ${viewBtn}
                                                                        ${delBtn}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr id="desc_row_${fu.id}" class="d-none" style="background: #f8fafc;">
                                                                <td colspan="5" class="p-0">
                                                                    <div id="desc_content_${fu.id}" style="display: none; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                                                        <div style="padding: 20px 25px;">
                                                                            <div class="custom-html-content shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff !important; padding: 25px !important; width: 100%; overflow-x: hidden; word-wrap: break-word;">
                                                                                ${fu.work_description}
                                                                            </div>
                                                                            ${fu.photo ? `
                                                                            <div class="mt-3">
                                                                                <a href="javascript:void(0);" onclick="viewAttachmentPopup('/storage/${fu.photo}')" class="btn btn-sm btn-soft-primary fw-bold" style="border-radius: 8px;">
                                                                                    <i class="feather-image me-1"></i> View Attached File
                                                                                </a>
                                                                            </div>` : ''}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        `;
                });
                if (totalItems === 0) body.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted fw-bold">No history found.</td></tr>';

                document.getElementById('modalEntriesInfo').innerText = `Showing ${totalItems === 0 ? 0 : startIdx + 1} to ${Math.min(startIdx + modalPageSize, totalItems)} of ${totalItems} entries`;

                const pgnBtn = document.getElementById('modalPaginationButtons');
                let pgnHtml = `<a class="page-link cursor-pointer ${modalCurrentPage === 1 ? 'text-muted disabled' : ''}" onclick="changeModalPage(${modalCurrentPage - 1})"><i class="feather-chevron-left"></i></a>`;
                for (let i = 1; i <= totalPages; i++) { pgnHtml += `<li class="page-item ${i === modalCurrentPage ? 'active' : ''}"><a class="page-link cursor-pointer" onclick="changeModalPage(${i})">${i}</a></li>`; }
                pgnHtml += `<a class="page-link cursor-pointer ${modalCurrentPage === totalPages || totalItems === 0 ? 'text-muted disabled' : ''}" onclick="changeModalPage(${modalCurrentPage + 1})"><i class="feather-chevron-right"></i></a>`;
                pgnBtn.innerHTML = pgnHtml;
            }

            function changeModalPage(page) { modalCurrentPage = page; renderModalTable(); }
            function changeModalEntries() { modalPageSize = parseInt(document.getElementById('modalEntriesLimit').value); modalCurrentPage = 1; renderModalTable(); }
            function filterModalHistory() { modalCurrentPage = 1; renderModalTable(); }

            function filterTasks() {
                const filter = document.getElementById('taskSearch').value.toLowerCase();
                const rows = document.querySelectorAll('.task-row');
                let visibleCount = 0;
                rows.forEach(row => { const matches = row.innerText.toLowerCase().includes(filter); row.style.display = matches ? '' : 'none'; if (matches) visibleCount++; });
                const limit = parseInt(document.getElementById('entriesLimit').value);
                let counted = 0; let actVis = 0;
                rows.forEach(row => { if (row.style.display !== 'none') { if (counted >= limit) row.style.display = 'none'; else actVis++; counted++; } });
                document.getElementById('entriesInfo').innerText = `Showing 1 to ${actVis} of ${visibleCount} entries (filtered)`;
            }

            document.getElementById('followUpForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('submitReplyBtn');
                const origText = btn.innerText;
                btn.innerText = 'SUBMITTING...'; btn.disabled = true;

                // Clear previous errors
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                fetch('{{ route("daily-tasks.follow-up.store") }}', { method: 'POST', body: new FormData(this), headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(res => res.json()).then(data => {
                        btn.innerText = origText; btn.disabled = false;
                        if (data.success) {
                            this.reset();
                            $('#workDesc').summernote('code', '');
                            removePreview();
                            Toast.fire({ icon: 'success', title: data.success });
                            if (myFollowUpModal) myFollowUpModal.hide();
                        } else if (data.errors) {
                            for (const [key, value] of Object.entries(data.errors)) {
                                const input = this.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'invalid-feedback d-block fw-bold small text-danger mb-1';
                                    errorDiv.innerText = value[0];
                                    input.parentNode.insertBefore(errorDiv, input);
                                }
                            }
                        } else if (data.message) {
                            Toast.fire({ icon: 'error', title: data.message });
                        }
                    }).catch(err => { btn.innerText = origText; btn.disabled = false; Toast.fire({ icon: 'error', title: 'Upload failed. Max size 10MB.' }); });
            });

            document.getElementById('submitTaskBtn').addEventListener('click', function () {
                const form = document.getElementById('taskForm');

                // Clear previous errors
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                const url = document.getElementById('methodField').innerHTML !== '' ? `/daily-tasks/${document.getElementById('taskId').value}` : '/daily-tasks';
                fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(Object.fromEntries(new FormData(form).entries())) })
                    .then(res => res.json()).then(result => {
                        if (result.success) {
                            Toast.fire({ icon: 'success', title: result.success }).then(() => location.reload());
                        } else if (result.errors) {
                            for (const [key, value] of Object.entries(result.errors)) {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'invalid-feedback d-block fw-bold small text-danger mb-1';
                                    errorDiv.innerText = value[0];
                                    input.parentNode.insertBefore(errorDiv, input);
                                }
                            }
                        } else if (result.message) {
                            Toast.fire({ icon: 'error', title: result.message });
                        }
                    });
            });

            // Task Timer Logic
            function updateTaskTimers() {
                const now = new Date();
                const timers = document.querySelectorAll('.task-timer');

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
                        let diff = now - end;

                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        diff -= days * (1000 * 60 * 60 * 24);
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        diff -= hours * (1000 * 60 * 60);
                        const mins = Math.floor(diff / (1000 * 60));
                        diff -= mins * (1000 * 60);
                        const secs = Math.floor(diff / 1000);

                        timer.innerHTML = `
                                                                    <span class="text-danger">${days}d</span> 
                                                                    <span class="text-danger small">${hours}h ${mins}m ${secs}s</span>
                                                                    <span class="text-danger fw-bold ms-1" style="font-size:9px;">OVERDUE</span>
                                                                `;
                    }
                });
            }

            setInterval(updateTaskTimers, 1000);
            document.addEventListener('DOMContentLoaded', () => {
                filterTasks();
                updateTaskTimers();
            });

            function showTaskDesc(id) {
                const html = document.getElementById('task_desc_' + id).innerHTML;
                Swal.fire({
                    title: 'Task Description',
                    html: `<div class="custom-html-content" style="max-height: 60vh; overflow-y: auto; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">${html}</div>`,
                    showConfirmButton: true,
                    confirmButtonColor: '#3858f9'
                });
            }

            function toggleFollowUpRow(id, btn) {
                const row = document.getElementById('desc_row_' + id);
                const content = document.getElementById('desc_content_' + id);
                const icon = btn.querySelector('i');

                if (row.classList.contains('d-none')) {
                    // Show Smoothly
                    row.classList.remove('d-none');
                    $(content).slideDown(400, "swing");
                    icon.classList.remove('feather-eye');
                    icon.classList.add('feather-eye-off');
                    btn.classList.remove('bg-soft-info', 'text-info');
                    btn.classList.add('bg-info', 'text-white');
                } else {
                    // Hide Smoothly
                    $(content).slideUp(350, "swing", function () {
                        row.classList.add('d-none');
                    });
                    icon.classList.remove('feather-eye-off');
                    icon.classList.add('feather-eye');
                    btn.classList.remove('bg-info', 'text-white');
                    btn.classList.add('bg-soft-info', 'text-info');
                }
            }

            $(document).ready(function () {
                $('#workDesc, #taskDesc').summernote({
                    placeholder: 'Enter Description...',
                    tabsize: 2,
                    height: 100,
                    maximumImageFileSize: 1024 * 1024 * 5, // 5MB limit
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
                                if (files[i].size > 1024 * 1024 * 5) {
                                    Toast.fire({ icon: 'error', title: 'Image too large (Max 5MB)' });
                                    continue;
                                }
                                // Summernote handles base64 by default if we don't handle it here, 
                                // but we can manually invoke it to be safe
                                let reader = new FileReader();
                                reader.onload = (e) => {
                                    $(this).summernote('insertImage', e.target.result);
                                };
                                reader.readAsDataURL(files[i]);
                            }
                        },
                        onPaste: function (e) {
                            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
                            if (bufferText) {
                                // Optionally clean up pasted HTML
                            }
                        },
                        onChange: function (contents, $editable) {
                            $(this).val(contents);
                        }
                    }
                });
            });

            function deleteFollowUp(id) {
                    Swal.fire({
                        title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/daily-tasks/follow-up/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                            }).then(res => res.json()).then(data => {
                                if (data.success) {
                                    Toast.fire({ icon: 'success', title: data.success });
                                    loadFollowUpHistory(document.getElementById('followUpTaskId').value);
                                }
                            });
                        }
                    });
                }

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

                function updateTaskStatus(id, status) {
                    fetch(`/daily-tasks/${id}/status`, {
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

                function updateTaskPriority(id, priority) {
                    fetch(`/daily-tasks/${id}/priority`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ priority: priority })
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

                function showTaskDesc(id) {
                    const html = document.getElementById('task_desc_' + id).innerHTML;
                    Swal.fire({
                        title: 'Task Details & Progress',
                        html: `<div class="custom-html-content" style="max-height: 75vh; overflow-y: auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; text-align: left;">${html}</div>`,
                        width: '800px',
                        showConfirmButton: true,
                        confirmButtonColor: '#3858f9'
                    });
                }

                function viewAttachmentPopup(url) {
                    const isImage = url.match(/\.(jpeg|jpg|gif|png|webp)$/i) != null;
                    let htmlContent = '';

                    if (isImage) {
                        htmlContent = `<img src="${url}" style="width: 100%; max-height: 70vh; object-fit: contain; border-radius: 8px;">`;
                    } else {
                        htmlContent = `<iframe src="${url}" style="width: 100%; height: 70vh; border: none; border-radius: 8px;"></iframe>`;
                    }

                    Swal.fire({
                        title: 'Attachment Preview',
                        html: htmlContent,
                        width: '900px',
                        showCloseButton: true,
                        showConfirmButton: false
                    });
                }
            </script>

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
                    });
                </script>
            @endif
    @endpush