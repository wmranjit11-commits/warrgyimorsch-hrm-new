@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Projects</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Edit Project</li>
        </ul>
    </div>
</div>
<!-- [ page-header ] end -->

<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-top-0">
                <div class="card-body p-0">
                    <form id="project-edit-form" action="{{ route('projects.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div id="project-edit-wizard" style="visibility: hidden; height: 0; overflow: hidden;">
                            
                            <!-- Step 1: Type -->
                            <h3>TYPE</h3>
                            <section>
                                <div class="mb-5">
                                    <h2 class="fs-16 fw-bold">Project type</h2>
                                    <p class="text-muted">Select project type first.</p>
                                </div>
                                <fieldset>
                                    <label class="w-100" for="project_personal">
                                        <input class="card-input-element" type="radio" name="type" id="project_personal" value="Personal" {{ $project->type == 'Personal' ? 'checked' : '' }} required>
                                        <span class="card card-body d-flex flex-row justify-content-between align-items-center">
                                            <span class="hstack gap-3">
                                                <span class="avatar-text text-primary" style="background: rgba(52, 84, 209, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><i class="feather-user"></i></span>
                                                <span>
                                                    <span class="d-block fs-13 fw-bold text-dark">Personal Project</span>
                                                    <span class="d-block text-muted mb-0">Individual project management</span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="w-100" for="project_team">
                                        <input class="card-input-element" type="radio" name="type" id="project_team" value="Team" {{ $project->type == 'Team' ? 'checked' : '' }}>
                                        <span class="card card-body d-flex flex-row justify-content-between align-items-center">
                                            <span class="hstack gap-3">
                                                <span class="avatar-text text-primary" style="background: rgba(52, 84, 209, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><i class="feather-users"></i></span>
                                                <span>
                                                    <span class="d-block fs-13 fw-bold text-dark">Team Project</span>
                                                    <span class="d-block text-muted mb-0">Collaborative project for multiple members</span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </fieldset>
                                <hr class="my-5">
                                <div class="mb-5">
                                    <h2 class="fs-16 fw-bold">Project manage</h2>
                                    <p class="text-muted">Who can manage projects</p>
                                </div>
                                <fieldset>
                                    <label class="w-100" for="project_everyone">
                                        <input class="card-input-element" type="radio" name="manage" id="project_everyone" value="Everyone" {{ $project->manage == 'Everyone' || !$project->manage ? 'checked' : '' }} required>
                                        <span class="card card-body d-flex flex-row justify-content-between align-items-center">
                                            <span class="hstack gap-3">
                                                <span class="avatar-text text-primary" style="background: rgba(52, 84, 209, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><i class="feather-globe"></i></span>
                                                <span>
                                                    <span class="d-block fs-13 fw-bold text-dark">Everyone</span>
                                                    <span class="d-block text-muted mb-0">Visible to all authenticated users.</span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <label class="w-100" for="project_admin">
                                        <input class="card-input-element" type="radio" name="manage" id="project_admin" value="Admin" {{ $project->manage == 'Admin' ? 'checked' : '' }}>
                                        <span class="card card-body d-flex flex-row justify-content-between align-items-center">
                                            <span class="hstack gap-3">
                                                <span class="avatar-text text-primary" style="background: rgba(52, 84, 209, 0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><i class="feather-shield"></i></span>
                                                <span>
                                                    <span class="d-block fs-13 fw-bold text-dark">Only Admin's</span>
                                                    <span class="d-block text-muted mb-0">Only admins can manage.</span>
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </fieldset>

                            </section>

                            <!-- Step 2: Details -->
                            <h3>DETAILS</h3>
                            <section>
                                <div class="mb-5"><h2 class="fs-16 fw-bold">Project details</h2></div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="{{ $project->name }}" style="border-radius: 8px; height: 46px;" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Technology <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="technology" value="{{ $project->technology }}" style="border-radius: 8px; height: 46px;" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="projectDescriptionEditor" style="display:none;">{!! $project->description !!}</textarea>
                                    <textarea id="summernote-main" class="form-control" style="min-height: 250px;">{!! $project->description !!}</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ $project->end_date ? $project->end_date->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Department</label>
                                        <select class="form-select" name="department">
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->name }}" {{ $project->department == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Not Started" {{ $project->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                            <option value="In Progress" {{ $project->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="On Hold" {{ $project->status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                            <option value="Declined" {{ $project->status == 'Declined' ? 'selected' : '' }}>Declined</option>
                                            <option value="Finished" {{ $project->status == 'Finished' ? 'selected' : '' }}>Finished</option>
                                        </select>
                                    </div>
                                </div>
                            </section>

                            <!-- Step 3: Members -->
                            <h3>MEMBERS</h3>
                            <section>
                                <div class="mb-5">
                                    <h2 class="fs-16 fw-bold">Project Assignment</h2>
                                    <p class="text-muted">Select project leads and team members.</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Project Leads</label>
                                        <select class="form-select select2-multiple" name="leaders[]" multiple="multiple" style="width: 100%;">
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}" {{ is_array($project->leaders) && in_array($emp->id, $project->leaders) ? 'selected' : '' }}>{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Team Members</label>
                                        <select class="form-select select2-multiple" name="members[]" multiple="multiple" style="width: 100%;">
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}" {{ is_array($project->members) && in_array($emp->id, $project->members) ? 'selected' : '' }}>{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </section>

                            <!-- Step 4: Completed -->
                            <h3>COMPLETED</h3>
                            <section class="py-5">
                                <div class="text-center">
                                    <h2 class="fs-20 fw-bold">Ready to Update?</h2>
                                    <p class="mb-5 text-muted">All changes are verified. Click Update to finalize.</p>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-lg" style="border-radius: 12px;">UPDATE PROJECT NOW</button>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Wizard Header Styling */
    #project-edit-wizard h3 { display: none !important; }
    .wizard > .content > section { display: none !important; }
    .wizard > .content > section.current { display: block !important; }
    .wizard > .steps { background: #fff !important; border-bottom: 1px solid #e2e8f0 !important; }
    .wizard > .steps ul { display: flex !important; padding: 0 !important; margin: 0 !important; list-style: none !important; width: 100% !important; }
    .wizard > .steps li { flex: 1 !important; text-align: center !important; border-right: 1px solid #e2e8f0 !important; }
    .wizard > .steps a { display: block !important; padding: 25px 15px !important; color: #64748b !important; font-weight: 700 !important; text-decoration: none !important; border-bottom: 4px solid transparent !important; font-size: 11px !important; text-transform: uppercase !important; }
    .wizard > .steps .current a { color: #3454d1 !important; border-bottom-color: #3454d1 !important; }
    .wizard > .content { padding: 50px !important; background: #fff !important; min-height: 450px; }
    .wizard > .actions { padding: 20px 50px !important; background: #fff !important; border-top: 1px solid #e2e8f0 !important; }
    .wizard > .actions ul { display: flex !important; justify-content: flex-end !important; gap: 10px !important; list-style: none !important; }
    .wizard > .actions a { display: block !important; padding: 10px 25px !important; background: #3454d1 !important; color: #fff !important; border-radius: 4px !important; text-decoration: none !important; }
    /* Card Input Radio styling */
    .card-input-element { display: none; }
    .card-input-element:checked + .card { border-color: #3454d1 !important; background-color: rgba(52, 84, 209, 0.05) !important; border-width: 2px !important; }
    /* Select2 */
    .select2-container--default .select2-selection--multiple { border: 1px solid #e2e8f0 !important; border-radius: 8px !important; padding: 8px !important; min-height: 48px !important; }
</style>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        var form = $("#project-edit-form");

        $("#project-edit-wizard").steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "fade",
            autoFocus: true,
            labels: { finish: "UPDATE", next: "NEXT", previous: "PREVIOUS" },
            onInit: function (event, currentIndex) {
                $("#project-edit-wizard").css({ 'visibility': 'visible', 'height': 'auto', 'overflow': 'visible' });
                
                // Initialize Summernote FIRST
                $('#summernote-main').summernote({ height: 250 });

                // Initialize Select2
                if ($.fn.select2) {
                    $('.select2-multiple').select2({
                        width: '100%',
                        placeholder: "Select...",
                        closeOnSelect: false,
                        allowClear: true,
                        dropdownParent: $('#project-edit-wizard')
                    });
                }
            },
            onStepChanging: function (event, currentIndex, newIndex) {
                $('#projectDescriptionEditor').val($('#summernote-main').summernote('code'));
                return true;
            },
            onFinished: function (event, currentIndex) {
                $('#projectDescriptionEditor').val($('#summernote-main').summernote('code'));
                form.submit();
            }
        });
    });
</script>
@endpush
