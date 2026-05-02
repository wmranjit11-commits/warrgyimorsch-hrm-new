@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Projects</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Projects</li>
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
                        <button type="button" id="btn-bulk-delete" class="btn btn-danger">
                            <i class="feather-trash-2 me-2"></i>
                            <span>Delete Selected</span>
                        </button>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" data-bs-toggle="collapse"
                            data-bs-target="#filterSection">
                            <i class="feather-filter"></i>
                        </a>
                    </div>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Create Project</span>
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
            <div class="row">
                @php
                    $notStartedCount = $projects->filter(fn($p) => strtolower($p->status) == 'not started')->count();
                    $inProgressCount = $projects->filter(fn($p) => strtolower($p->status) == 'in progress')->count();
                    $onHoldCount = $projects->filter(fn($p) => strtolower($p->status) == 'on hold')->count();
                    $declinedCount = $projects->filter(fn($p) => in_array(strtolower($p->status), ['declined', 'cancelled']))->count();
                    $finishedCount = $projects->filter(fn($p) => in_array(strtolower($p->status), ['finished', 'completed']))->count();
                @endphp
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-warning">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Not Started</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $notStartedCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-warning text-warning">
                                    <i class="feather-pause-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-primary">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">In Progress</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $inProgressCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-primary text-primary">
                                    <i class="feather-play-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-4">
                    <div class="card stretch stretch-full border-start border-4 border-info">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">On Hold</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $onHoldCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-info text-info">
                                    <i class="feather-minus-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-6">
                    <div class="card stretch stretch-full border-start border-4 border-danger">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Declined</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $declinedCount }}</span>
                                </div>
                                <div class="avatar-text avatar-md bg-soft-danger text-danger">
                                    <i class="feather-x-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl col-md-6">
                    <div class="card stretch stretch-full border-start border-4 border-success">
                        <div class="card-body p-3">
                            <div class="hstack justify-content-between">
                                <div>
                                    <span class="fs-10 fw-bold text-uppercase d-block mb-1">Finished</span>
                                    <span class="fs-20 fw-bolder d-block">{{ $finishedCount }}</span>
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

    <div class="collapse" id="filterSection">
        <div class="card stretch stretch-full border-bottom bg-light bg-opacity-10 p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Project Name</label>
                    <input type="text" id="filterProjectName" class="form-control border-0 shadow-sm"
                        placeholder="Search..." onkeyup="applyFilters()" style="border-radius: 8px; height: 44px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Status</label>
                    <select id="filterStatus" class="form-select border-0 shadow-sm" onchange="applyFilters()"
                        style="border-radius: 8px; height: 44px;">
                        <option value="">All Status</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Not Started">Not Started</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Declined">Declined/Cancelled</option>
                        <option value="Finished">Finished</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Department</label>
                    <select id="filterDepartment" class="form-select border-0 shadow-sm" onchange="applyFilters()"
                        style="border-radius: 8px; height: 44px;">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button class="btn btn-primary flex-grow-1 fw-bold shadow-sm" onclick="applyFilters()"
                        style="height: 44px; border-radius: 8px;">APPLY</button>
                    <a href="{{ route('projects.index') }}" class="btn btn-light shadow-sm"
                        style="height: 44px; border-radius: 8px; width: 60px; display: flex; align-items: center; justify-content: center;"><i
                            class="feather-refresh-cw"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="projectList">
                                <thead>
                                    <tr>
                                        <th class="wd-30">
                                            <div class="btn-group mb-1">
                                                <div class="custom-control custom-checkbox ms-1">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="checkAllProject">
                                                    <label class="custom-control-label" for="checkAllProject"></label>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Project Name</th>
                                        <th>Technology</th>
                                        <th>Department</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Lead</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                        <tr class="single-item">
                                            <td>
                                                <div class="item-checkbox ms-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input checkbox"
                                                            id="checkBox_{{ $project->id }}">
                                                        <label class="custom-control-label"
                                                            for="checkBox_{{ $project->id }}"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="project-name-td" style="max-width: 300px;">
                                                <div class="hstack gap-4">
                                                    <div class="avatar-image border-0">
                                                        <div class="avatar-text bg-soft-primary text-primary rounded">
                                                            <i class="feather-briefcase"></i>
                                                        </div>
                                                    </div>
                                                    <div class="project-info-wrapper">
                                                        <a href="{{ route('projects.show', $project->id) }}"
                                                            class="fw-bold text-dark fs-14 mb-1 d-block">{{ $project->name }}</a>
                                                        <div class="fs-12 text-muted text-truncate-1-line mb-1"
                                                            style="max-width: 250px;">
                                                            {!! strip_tags($project->description) !!}
                                                        </div>
                                                        <div class="project-list-actions mt-1" style="font-size: 10px;">
                                                            <a href="{{ route('projects.show', $project->id) }}"
                                                                class="text-primary fw-medium me-1">VIEW</a>
                                                            <span class="text-muted opacity-50 me-1">|</span>
                                                            <a href="{{ route('projects.edit', $project->id) }}"
                                                                class="text-info fw-medium me-1">EDIT</a>
                                                            <span class="text-muted opacity-50 me-1">|</span>
                                                            <form action="{{ route('projects.destroy', $project->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <a href="javascript:void(0);"
                                                                    onclick="if(confirm('Are you sure?')) this.closest('form').submit();"
                                                                    class="text-danger fw-medium">REMOVE</a>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-soft-secondary text-secondary">{{ $project->technology }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $project->id) }}" class="hstack gap-3">
                                                    <div>
                                                        <span
                                                            class="text-truncate-1-line fw-semibold text-dark">{{ $project->department }}</span>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>{{ $project->start_date ? $project->start_date->format('Y-m-d') : '-' }}</td>
                                            <td>{{ $project->end_date ? $project->end_date->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center py-2" style="min-width: 240px;">
                                                    <div class="avatar-text avatar-xs bg-soft-primary text-primary rounded-circle me-2"
                                                        title="Project Lead"><i class="feather-user-check"
                                                            style="font-size: 11px;"></i></div>
                                                    <select
                                                        class="form-select select2-lead-mini border bg-white fs-13 fw-bold text-dark shadow-none"
                                                        data-select2-selector="lead" data-project-id="{{ $project->id }}"
                                                        style="width: 220px; padding: 8px 12px; border-radius: 8px; cursor: pointer;">
                                                        <option value="">Select Lead...</option>
                                                        @foreach($employees as $emp)
                                                            @php
                                                                $leaders = is_array($project->leaders) ? $project->leaders : [];
                                                                $isSelected = in_array($emp->id, $leaders);
                                                             @endphp
                                                            <option value="{{ $emp->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                                {{ $emp->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-select select2-status shadow-none"
                                                    data-select2-selector="status" data-project-id="{{ $project->id }}"
                                                    style="min-width: 140px; padding: 8px 12px; height: 38px;">
                                                    <option value="In Progress" data-bg="bg-primary" {{ $project->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="Not Started" data-bg="bg-warning" {{ $project->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                    <option value="On Hold" data-bg="bg-success" {{ $project->status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                                    <option value="Declined" data-bg="bg-danger" {{ $project->status == 'Declined' ? 'selected' : '' }}>Declined</option>
                                                    <option value="Finished" data-bg="bg-teal" {{ $project->status == 'Finished' ? 'selected' : '' }}>Finished</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('projects.show', $project->id) }}"
                                                        class="avatar-text avatar-md bg-soft-primary text-primary">
                                                        <i class="feather feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.edit', $project->id) }}"
                                                        class="avatar-text avatar-md bg-soft-info text-info">
                                                        <i class="feather feather-edit-3"></i>
                                                    </a>
                                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="avatar-text avatar-md bg-soft-danger text-danger border-0"
                                                            onclick="return confirm('Are you sure?')">
                                                            <i class="feather feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

    <style>
        /* 1:1 Select2 Parity */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 4px !important;
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .project-list-actions a:hover {
            text-decoration: underline !important;
        }

        .badge {
            font-size: 10px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-body.p-3 {
            padding: 1.25rem 1.5rem !important;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Filter Functionality
            window.applyFilters = function () {
                var name = $('#filterProjectName').val().toLowerCase();
                var status = $('#filterStatus').val();
                var department = $('#filterDepartment').val();

                $('#projectList tbody tr.single-item').each(function () {
                    var rowName = $(this).find('.project-name-td a').text().toLowerCase();
                    var rowStatus = $(this).find('[data-select2-selector="status"]').val();
                    var rowDept = $(this).find('td:nth-child(4) span').text().trim(); // Department is 4th column

                    var matchName = name === "" || rowName.includes(name);
                    var matchStatus = status === "" || rowStatus === status;
                    var matchDept = department === "" || rowDept === department;

                    if (matchName && matchStatus && matchDept) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            };
            // Check All Functionality
            $('#checkAllProject').on('change', function () {
                $('.checkbox').prop('checked', $(this).prop('checked'));
                toggleBulkAction();
            });

            $('.checkbox').on('change', function () {
                toggleBulkAction();
            });

            function toggleBulkAction() {
                if ($('.checkbox:checked').length > 0) {
                    $('#bulk-action-wrapper').fadeIn();
                } else {
                    $('#bulk-action-wrapper').fadeOut();
                }
            }

            // Bulk Delete
            $('#btn-bulk-delete').on('click', function () {
                if (confirm('Are you sure you want to delete selected projects?')) {
                    var ids = [];
                    $('.checkbox:checked').each(function () {
                        ids.push($(this).attr('id').split('_')[1]);
                    });

                    $.ajax({
                        url: '{{ route("projects.bulk-delete") }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
            });

            // Status Select2 Template
            function formatStatus(state) {
                if (!state.id) return state.text;
                var bg = $(state.element).data('bg');
                return $('<span class="badge ' + bg + ' rounded-pill px-3" style="padding-top: 6px; padding-bottom: 6px; display: inline-block;">' + state.text + '</span>');
            }

            $('[data-select2-selector="status"]').select2({
                width: '140px',
                templateResult: formatStatus,
                templateSelection: formatStatus,
                minimumResultsForSearch: Infinity
            }).on('change', function () {
                var id = $(this).data('project-id');
                var status = $(this).val();
                $.ajax({
                    url: '/projects/' + id + '/update-field',
                    type: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { status: status },
                    success: function (response) {
                        if (typeof Toast !== 'undefined') {
                            Toast.fire({ icon: 'success', title: 'Status Updated Successfully' });
                        }
                    },
                    error: function (xhr) {
                        alert('Error updating status. Please try again.');
                        console.log(xhr.responseText);
                    }
                });
            });

            // User formatting for Select2
            function formatUser(state) {
                if (!state.id) return state.text;
                return $('<div class="d-flex align-items-center gap-2"><div class="avatar-text avatar-xs bg-soft-primary text-primary rounded-circle" style="width:20px; height:20px; font-size:9px;">' + state.text.charAt(0) + '</div><span class="fs-12 fw-bold text-dark">' + state.text + '</span></div>');
            }

            $('[data-select2-selector="lead"]').select2({
                width: '100%',
                templateResult: formatUser,
                templateSelection: formatUser,
                dropdownCssClass: 'fs-12 shadow-sm',
                dropdownAutoWidth: true
            }).on('change', function () {
                var id = $(this).data('project-id');
                var leaders = [$(this).val()];
                var $this = $(this);

                $.ajax({
                    url: '/projects/' + id + '/update-field',
                    type: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { leaders: leaders },
                    success: function (response) {
                        if (typeof Toast !== 'undefined') {
                            Toast.fire({ icon: 'success', title: 'Lead Updated Successfully' });
                        }
                    },
                    error: function (xhr) {
                        alert('Error updating lead. Please try again.');
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush