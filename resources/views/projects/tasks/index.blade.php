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
                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary"
                        onclick="location.reload()" title="Refresh">
                        <i class="feather-refresh-cw"></i>
                    </a>
                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger"
                        onclick="bulkDelete()" title="Delete Bulk">
                        <i class="feather-trash-2"></i>
                    </a>
                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-primary text-white"
                        data-bs-toggle="offcanvas" data-bs-target="#taskOffcanvas" onclick="resetTaskForm()"
                        title="Create Task">
                        <i class="feather-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ main-content ] start -->
    <div class="main-content pt-4">
        <div class="row">
            <div class="col-12">
                <!-- FILTER CARD -->
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
                                        <option value="In Process" {{ request('status') == 'In Process' ? 'selected' : '' }}>
                                            In Process</option>
                                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">From Date</label>
                                    <input type="date" name="from_date"
                                        class="form-control border-0 bg-light shadow-none fw-bold"
                                        value="{{ request('from_date') }}" onclick="this.showPicker()"
                                        style="height: 48px; border-radius: 10px; font-size: 14px;">
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-2">Upto Date</label>
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

                <!-- DATA TABLE CARD -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                        style="border-radius: 12px 12px 0 0;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Show</span>
                            <select id="entriesLimit" class="form-select border-0 bg-light shadow-none fw-bold"
                                onchange="paginateTable()"
                                style="width: 90px; height: 44px; border-radius: 10px; font-size: 14px; color: #1e293b; padding: 0 12px; line-height: 44px;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">entries</span>
                        </div>
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-light border-0"><i
                                    class="feather-search text-muted"></i></span>
                            <input type="text" id="taskSearch" class="form-control border-0 bg-light shadow-none fw-bold"
                                onkeyup="filterTasks()" placeholder="Search..."
                                style="height: 44px; font-size: 14px; border-radius: 0 10px 10px 0;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="tasksTable">
                                <thead style="background: #3858f9; color: white;">
                                    <tr style="height: 60px; vertical-align: middle;">
                                        <th class="ps-4" style="width: 80px; font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Sr. No.</th>
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
                                            End Date</th>
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
                                            style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: white;">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody style="border-top: 1px solid #f1f5f9;">
                                    @forelse($tasks as $index => $task)
                                        <tr class="task-row" style="height: 70px; border-bottom: 1px solid #f1f5f9;">
                                            <td class="ps-4 fw-bold" style="font-size: 14px; color: #1e293b;">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="fw-bold" style="font-size: 14px; color: #1e293b;">
                                                {{ $task->project->name ?? ($task->project_id ? 'Proj ID: '.$task->project_id : '-') }}</td>
                                            <td style="font-size: 14px; color: #475569;">{{ $task->task_title }}</td>
                                            <td style="font-size: 14px; color: #475569;">
                                                {{ $task->start_date->format('Y-m-d') }}</td>
                                            <td style="font-size: 14px; color: #475569;">{{ $task->end_date->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                @php
                                                    $priorityClass = 'bg-soft-info text-info';
                                                    if (strtolower($task->priority) == 'hard') $priorityClass = 'bg-soft-danger text-danger';
                                                    elseif (strtolower($task->priority) == 'medium') $priorityClass = 'bg-soft-warning text-warning';
                                                    elseif (strtolower($task->priority) == 'low') $priorityClass = 'bg-soft-success text-success';
                                                @endphp
                                                <span class="badge {{ $priorityClass }}"
                                                    style="padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                    {{ $task->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $taskStatusClass = 'bg-soft-primary text-primary';
                                                    if ($task->status == 'Completed') $taskStatusClass = 'bg-soft-success text-success';
                                                    elseif ($task->status == 'On Hold') $taskStatusClass = 'bg-soft-warning text-warning';
                                                @endphp
                                                <span class="badge {{ $taskStatusClass }}"
                                                    style="padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td style="font-size: 14px; color: #475569;">
                                                {{ $task->employee ? $task->employee->name : '-' }}</td>
                                            <td style="font-size: 14px; color: #475569;">
                                                {{ $task->creator ? $task->creator->name : '-' }}</td>
                                            <td class="pe-4 text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-secondary text-secondary rounded"
                                                        title="View Description" onclick="showTaskDesc({{ $task->id }})">
                                                        <i class="feather-file-text"></i>
                                                    </a>
                                                    <template id="task_desc_{{ $task->id }}">{!! $task->description ?? '<span class="text-muted">No description provided.</span>' !!}</template>
                                                    
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
                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-info text-info rounded"
                                                        title="Task History"
                                                        onclick="openFollowUpModal({{ $task->id }}, '{{ $task->task_title }}')">
                                                        <i class="feather-mail"></i>
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
                                {{ $tasks->count() }} of {{ $tasks->count() }} entries</div>
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
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Project <span
                                class="text-danger">*</span></label>
                        <select name="project_id" id="taskProjectId"
                            class="form-select border-0 bg-light shadow-none fw-bold"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                            <option value="">Select Project...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Task Title <span
                                class="text-danger">*</span></label>
                        <input type="text" name="task_title" id="taskTitle"
                            class="form-control border-0 bg-light shadow-none fw-bold" placeholder="Enter Task Title"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Start Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="taskStartDate"
                            class="form-control border-0 bg-light shadow-none fw-bold" onclick="this.showPicker()"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">End Date <span
                                class="text-danger">*</span></label>
                        <input type="date" name="end_date" id="taskEndDate"
                            class="form-control border-0 bg-light shadow-none fw-bold" onclick="this.showPicker()"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Priority <span
                                class="text-danger">*</span></label>
                        <select name="priority" id="taskPriority" class="form-select border-0 bg-light shadow-none fw-bold"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                            <option value="">Select priority...</option>
                            <option value="Hard">Hard</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Status</label>
                        <select name="status" id="taskStatus" class="form-select border-0 bg-light shadow-none fw-bold"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                            <option value="In Process">Status</option>
                            <option value="In Process">In Process</option>
                            <option value="Completed">Completed</option>
                            <option value="On Hold">On Hold</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Assign To <span
                                class="text-danger">*</span></label>
                        <select name="employee_id" id="taskEmployeeId"
                            class="form-select border-0 bg-light shadow-none fw-bold"
                            style="height: 48px; border-radius: 10px; font-size: 14px;" required>
                            <option value="">Employee name</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Task Description</label>
                        <textarea name="description" id="taskDesc" class="form-control border-0 bg-light shadow-none fw-bold" rows="3" placeholder="Enter task description" style="border-radius: 10px; font-size: 14px;"></textarea>
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
        <div class="modal-dialog modal-xl modal-dialog-centered"
            style="transform: none !important; transition: none !important;">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 16px; overflow: hidden; filter: none !important; -webkit-filter: none !important; transform: none !important;">
                <div class="modal-header text-white p-4" style="background: #3858f9; border: none !important;">
                    <h5 class="modal-title fw-bold" style="color: #ffffff !important;">Task History Description: <span
                            id="followUpTaskTitle" style="color: #ffffff !important; opacity: 0.9;"></span></h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8fafc !important; transform: none !important;">
                    <div class="row g-4">
                        <!-- ADD FOLLOW UP FORM (LEFT) -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-body p-4">
                                    <form id="followUpForm" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="daily_task_id" id="followUpTaskId">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Work
                                                Description <span class="text-danger">*</span></label>
                                            <textarea name="work_description" id="workDesc"
                                                class="form-control border-0 bg-light shadow-none fw-bold" rows="5"
                                                placeholder="Write Your Reply" required
                                                style="border-radius: 10px; font-size: 14px;"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label
                                                class="form-label fw-bold small text-muted text-uppercase mb-2">Reference</label>
                                            <input type="text" name="reference_name"
                                                class="form-control border-0 bg-light shadow-none fw-bold"
                                                placeholder="Reference Name"
                                                style="height: 48px; border-radius: 10px; font-size: 14px;">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Time
                                                Taken (e.g. 5 Hours or 2 Days)</label>
                                            <input type="text" name="time_taken"
                                                class="form-control border-0 bg-light shadow-none fw-bold"
                                                placeholder="Enter timeframe"
                                                style="height: 48px; border-radius: 10px; font-size: 14px;">
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Upload
                                                Photo</label>
                                            <input type="file" name="photo" id="photoInput"
                                                class="form-control border-0 bg-light shadow-none fw-bold"
                                                onchange="previewImage(this)"
                                                style="border-radius: 10px; padding: 10px; font-size: 14px;">
                                            <!-- REAL-TIME PREVIEW AREA -->
                                            <div id="previewContainer" class="mt-3 d-none"
                                                style="position: relative; width: 100%; height: 180px; border-radius: 12px; overflow: hidden; border: 2px dashed #e2e8f0; padding: 10px; background: #f8fafc;">
                                                <img id="photoPreview" src="#" alt="Preview"
                                                    style="width: 100%; height: 100%; object-fit: contain !important; border-radius: 8px;">
                                                <button type="button" class="btn btn-sm btn-danger rounded-circle"
                                                    onclick="removePreview()"
                                                    style="position: absolute; top: 15px; right: 15px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; z-index: 10;">
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
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm overflow-hidden"
                                style="border-radius: 12px; background: #ffffff;">
                                <div
                                    class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small fw-bold text-uppercase"
                                            style="font-size: 11px;">Show</span>
                                        <select id="modalEntriesLimit"
                                            class="form-select border-0 bg-light shadow-none fw-bold"
                                            onchange="changeModalEntries()"
                                            style="width: 90px; height: 44px; font-size: 14px; border-radius: 10px; padding: 0 12px; line-height: 44px;">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                        </select>
                                        <span class="text-muted small fw-bold text-uppercase"
                                            style="font-size: 11px;">entries</span>
                                    </div>
                                    <div class="input-group" style="width: 200px;">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="feather-search text-muted"></i></span>
                                        <input type="text" id="modalSearch"
                                            class="form-control border-0 bg-light shadow-none fw-bold"
                                            placeholder="Search..." onkeyup="filterModalHistory()"
                                            style="height: 44px; font-size: 14px; border-radius: 0 10px 10px 0;">
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="modalTableContainer" class="table-responsive"
                                        style="max-height: 420px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead
                                                style="background: #3858f9; color: white; position: sticky; top: 0; z-index: 1;">
                                                <tr style="height: 52px; vertical-align: middle;">
                                                    <th class="ps-3"
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
                                                        Sr.No.</th>
                                                    <th
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
                                                        Follow Up</th>
                                                    <th
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
                                                        Reference</th>
                                                    <th
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
                                                        Time Taken</th>
                                                    <th
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
                                                        Document</th>
                                                    <th
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
                                                        Followup_time</th>
                                                    <th class="pe-3 text-center"
                                                        style="font-size: 12px; text-transform: uppercase; color: #ffffff !important;">
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

        .active>.page-link {
            background-color: #3858f9 !important;
            border-color: #3858f9 !important;
            color: #ffffff !important;
        }

        .page-link:hover:not(.text-white) {
            border-color: #3858f9 !important;
            color: #3858f9;
        }

        .custom-html-content ul { list-style-type: disc !important; padding-left: 35px !important; margin-bottom: 1.2rem !important; list-style-position: outside !important; display: block !important; }
        .custom-html-content ol { list-style-type: decimal !important; padding-left: 35px !important; margin-bottom: 1.2rem !important; list-style-position: outside !important; display: block !important; }
        .custom-html-content li { display: list-item !important; margin-bottom: 0.8rem !important; list-style-type: inherit !important; line-height: 1.6 !important; }
        .custom-html-content p { margin-bottom: 1rem !important; line-height: 1.6 !important; }
        .custom-html-content { text-align: left !important; font-size: 15px; line-height: 1.6; color: #1e293b; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word; padding: 30px 30px 30px 45px !important; background: #ffffff !important; border-radius: 12px; }
        
        /* Summernote point indentation fix */
        .note-editable ul { list-style-type: disc !important; padding-left: 35px !important; list-style-position: outside !important; display: block !important; }
        .note-editable ol { list-style-type: decimal !important; padding-left: 35px !important; list-style-position: outside !important; display: block !important; }
        .note-editable li { display: list-item !important; margin-bottom: 0.5rem !important; list-style-type: inherit !important; }
        .note-editable { min-height: 200px; padding: 25px !important; background: white !important; }
    </style>

    <script>
        let globalFollowUps = [];
        let modalCurrentPage = 1;
        let modalPageSize = 5;

        function previewImage(input) {
            const preview = document.getElementById('photoPreview');
            const container = document.getElementById('previewContainer');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) { preview.src = e.target.result; container.classList.remove('d-none'); }
                reader.readAsDataURL(input.files[0]);
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
            const pId = task.project_id || '';
            const eId = task.employee_id || '';
            
            // Try both native and jQuery to be absolutely sure
            const projSelect = document.getElementById('taskProjectId');
            if (projSelect) {
                projSelect.value = pId;
                if (projSelect.value !== pId.toString()) {
                    // Fallback search if value doesn't match directly
                    for(let i=0; i<projSelect.options.length; i++) {
                        if(projSelect.options[i].value == pId) {
                            projSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
            
            const empSelect = document.getElementById('taskEmployeeId');
            if (empSelect) {
                empSelect.value = eId;
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
                if(document.getElementById('taskDesc')) document.getElementById('taskDesc').value = task.description || '';
            }

            // Form action and method
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            const formObj = document.getElementById('taskForm');
            if(formObj) formObj.action = `/daily-tasks/${task.id}`;
            
            // Show Offcanvas
            const offElement = document.getElementById('taskOffcanvas');
            if(offElement) {
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
            } catch (e) {}
        }

        function openFollowUpModal(taskId, taskTitle) {
            document.getElementById('followUpTaskId').value = taskId;
            document.getElementById('followUpTaskTitle').innerText = taskTitle;
            removePreview();
            modalCurrentPage = 1;
            loadFollowUpHistory(taskId);
            new bootstrap.Modal(document.getElementById('followUpModal')).show();
        }

        function loadFollowUpHistory(taskId) {
            fetch(`/daily-tasks/${taskId}/follow-ups`).then(res => res.json()).then(data => { globalFollowUps = data; renderModalTable(); });
        }

        function renderModalTable() {
            const body = document.getElementById('followUpHistoryBody');
            const searchTerm = document.getElementById('modalSearch').value.toLowerCase();
            let filtered = globalFollowUps.filter(fu => fu.work_description.toLowerCase().includes(searchTerm) || (fu.reference_name && fu.reference_name.toLowerCase().includes(searchTerm)));
            const totalItems = filtered.length;
            const totalPages = Math.ceil(totalItems / modalPageSize) || 1;
            if (modalCurrentPage > totalPages) modalCurrentPage = totalPages;
            const startIdx = (modalCurrentPage - 1) * modalPageSize;
            const paginated = filtered.slice(startIdx, startIdx + modalPageSize);

            body.innerHTML = '';
            paginated.forEach((fu, index) => {
                // TIME TAKEN UNIT LOGIC
                let timeDisplay = fu.time_taken || '-';
                if (fu.time_taken && !isNaN(fu.time_taken)) {
                    timeDisplay = fu.time_taken + ' Hours';
                }

                let descBtn = `<a href="javascript:void(0);" onclick="showFollowUpDesc(${fu.id})" class="badge bg-soft-info text-info border-0" style="padding: 6px 12px; border-radius: 8px; text-decoration: none;">View</a>`;

                body.innerHTML += `
                    <tr style="height: 56px; border-bottom: 1px solid #f1f5f9;">
                        <td class="ps-3 fw-bold" style="font-size: 13px;">${startIdx + index + 1}</td>
                        <td style="font-size: 13px;">
                            ${descBtn}
                        </td>
                        <td style="font-size: 13px; color: #475569;">${fu.reference_name || '-'}</td>
                        <td style="font-size: 13px; color: #475569; font-weight: 700;">${timeDisplay}</td>
                        <td style="font-size: 13px;">
                            ${fu.photo ? `<a href="/storage/${fu.photo}" target="_blank"><img src="/storage/${fu.photo}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;"></a>` : '-'}
                        </td>
                        <td style="font-size: 12px; white-space: nowrap;">
                            <div class="fw-bold text-dark">${new Date(fu.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                            <div class="text-muted mt-1" style="font-size: 11px;">${new Date(fu.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}</div>
                        </td>
                        <td class="pe-3 text-center"><a href="javascript:void(0);" onclick="deleteFollowUp(${fu.id})" class="avatar-text avatar-sm bg-soft-danger text-danger rounded"><i class="feather-trash-2"></i></a></td>
                    </tr>
                `;
            });
            if (totalItems === 0) body.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted fw-bold">No history found.</td></tr>';

            document.getElementById('modalEntriesInfo').innerText = `Showing ${totalItems === 0 ? 0 : startIdx + 1} to ${Math.min(startIdx + modalPageSize, totalItems)} of ${totalItems} entries`;

            const pgnBtn = document.getElementById('modalPaginationButtons');
            let pgnHtml = `<a class="page-link ${modalCurrentPage === 1 ? 'text-muted disabled' : ''}" onclick="changeModalPage(${modalCurrentPage - 1})"><i class="feather-chevron-left"></i></a>`;
            for (let i = 1; i <= totalPages; i++) { pgnHtml += `<li class="page-item ${i === modalCurrentPage ? 'active' : ''}"><a class="page-link" onclick="changeModalPage(${i})">${i}</a></li>`; }
            pgnHtml += `<a class="page-link ${modalCurrentPage === totalPages || totalItems === 0 ? 'text-muted disabled' : ''}" onclick="changeModalPage(${modalCurrentPage + 1})"><i class="feather-chevron-right"></i></a>`;
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
                        removePreview(); Toast.fire({ icon: 'success', title: data.success }); loadFollowUpHistory(document.getElementById('followUpTaskId').value); 
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

        document.addEventListener('DOMContentLoaded', () => { filterTasks(); });

        function showTaskDesc(id) {
            const html = document.getElementById('task_desc_' + id).innerHTML;
            Swal.fire({
                title: 'Task Description',
                html: `<div class="custom-html-content" style="max-height: 60vh; overflow-y: auto; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">${html}</div>`,
                showConfirmButton: true,
                confirmButtonColor: '#3858f9'
            });
        }

        function showFollowUpDesc(id) {
            const fu = globalFollowUps.find(f => f.id === id);
            if (fu) {
                Swal.fire({
                    title: 'Work Description',
                    html: `<div class="custom-html-content" style="max-height: 400px; overflow-y: auto; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">${fu.work_description}</div>`,
                    showConfirmButton: true,
                    confirmButtonColor: '#3858f9'
                });
            }
        }

        $(document).ready(function() {
            $('#workDesc, #taskDesc').summernote({
                placeholder: 'Enter Description...',
                tabsize: 2,
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents, $editable) {
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
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        });
    </script>
    @endif
@endpush