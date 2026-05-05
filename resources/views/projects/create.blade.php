@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Projects</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Create Project</li>
            </ul>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-top-0">
                    <div class="card-body p-0">
                        <div id="project-create-wizard" style="visibility: hidden; height: 0; overflow: hidden;">

                            <!-- Step 1: Type -->
                            <h3>TYPE</h3>
                            <section>
                                <div class="mb-5">
                                        <h2 class="fs-16 fw-bold">Project type</h2>
                                        <p class="text-muted">Select project type first.</p>
                                    </div>
                                    <fieldset>
                                        <label class="w-100" for="project_personal">
                                            <input class="card-input-element" type="radio" name="type" id="project_personal"
                                                value="Personal" required>
                                            <span
                                                class="card card-body d-flex flex-row justify-content-between align-items-center">
                                                <span class="hstack gap-3">
                                                    <span class="avatar-text"><i class="feather-user"></i></span>
                                                    <span>
                                                        <span class="d-block fs-13 fw-bold text-dark">Personal Project</span>
                                                        <span class="d-block text-muted mb-0">Individual project management</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>
                                        <label class="w-100" for="project_team">
                                            <input class="card-input-element" type="radio" name="type" id="project_team"
                                                value="Team">
                                            <span
                                                class="card card-body d-flex flex-row justify-content-between align-items-center">
                                                <span class="hstack gap-3">
                                                    <span class="avatar-text"><i class="feather-users"></i></span>
                                                    <span>
                                                        <span class="d-block fs-13 fw-bold text-dark">Team Project</span>
                                                        <span class="d-block text-muted mb-0">Collaborative project for multiple members</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>
                                    </fieldset>
                                    <hr class="mb-5">
                                    <div class="mb-5">
                                        <h2 class="fs-16 fw-bold">Project manage</h2>
                                        <p class="text-muted">Who can manage projects</p>
                                    </div>
                                    <fieldset>
                                        <label class="w-100" for="project_everyone">
                                            <input class="card-input-element" type="radio" name="manage" id="project_everyone"
                                                value="Everyone" required>
                                            <span
                                                class="card card-body d-flex flex-row justify-content-between align-items-center">
                                                <span class="hstack gap-3">
                                                    <span class="avatar-text"><i class="feather-globe"></i></span>
                                                    <span>
                                                        <span class="d-block fs-13 fw-bold text-dark">Everyone</span>
                                                        <span class="d-block text-muted mb-0">Visible to all authenticated users.</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>
                                        <label class="w-100" for="project_admin">
                                            <input class="card-input-element" type="radio" name="manage" id="project_admin"
                                                value="Admin">
                                            <span
                                                class="card card-body d-flex flex-row justify-content-between align-items-center">
                                                <span class="hstack gap-3">
                                                    <span class="avatar-text"><i class="feather-shield"></i></span>
                                                    <span>
                                                        <span class="d-block fs-13 fw-bold text-dark">Only Admin's</span>
                                                        <span class="d-block text-muted mb-0">Only admins can manage everything.</span>
                                                    </span>
                                                </span>
                                            </span>
                                        </label>
                                    </fieldset>
                                </section>

                                <!-- Step 2: Details -->
                                <h3>DETAILS</h3>
                                <section>
                                    <div class="mb-5">
                                        <h2 class="fs-16 fw-bold">Project details</h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Project Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control premium-input shadow-none" id="projectName"
                                                placeholder="e.g. Website development" required>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Technology</label>
                                            <input type="text" class="form-control premium-input shadow-none" id="projectTechnology"
                                                placeholder="e.g. PHP, Laravel, React">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold fs-12 text-muted text-uppercase">Project Description</label>
                                        <textarea id="summernote-main" class="form-control" style="min-height: 200px; border-radius: 8px;"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control premium-input shadow-none" id="projectStartDate" required>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">End Date</label>
                                            <input type="date" class="form-control premium-input shadow-none" id="projectEndDate" onclick="this.showPicker()">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Department <span class="text-danger">*</span></label>
                                            <select class="form-select premium-select" id="projectDepartment" data-placeholder="Select Department" required>
                                                <option value=""></option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Status</label>
                                            <select class="form-select premium-select" id="projectStatus" data-placeholder="Select Status">
                                                <option value=""></option>
                                                <option value="Pending">Pending</option>
                                                <option value="In Process">In Process</option>
                                                <option value="Completed">Completed</option>
                                                <option value="On Hold">On Hold</option>
                                                <option value="Review">Review</option>
                                                <option value="Rework">Rework</option>
                                            </select>
                                        </div>
                                    </div>
                                </section>

                                <!-- Step 3: Assigned -->
                                <h3>ASSIGNED</h3>
                                <section>
                                    <div class="mb-5">
                                        <h2 class="fs-16 fw-bold">Project Assignment</h2>
                                        <p class="text-muted">Select project leads and team members.</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Project Leads <span class="text-danger">*</span></label>
                                            <select class="form-select premium-select" id="projectLeaders" multiple="multiple" data-placeholder="Select Leads..." required>
                                                @foreach($employees as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold fs-12 text-muted text-uppercase">Team Members</label>
                                            <select class="form-select premium-select" id="projectMembers" multiple="multiple" data-placeholder="Select Members...">
                                                @foreach($employees as $emp)
                                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </section>

                                <!-- Step 4: Completed -->
                                <h3>COMPLETED</h3>
                                <section class="py-5">
                                    <div class="text-center">
                                        <h2 class="fs-20 fw-bold">Success!</h2>
                                        <p class="mb-5 text-muted">Project details are ready. Click Finish to create.</p>
                                        <form id="finalCreateForm" action="{{ route('projects.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" id="hiddenType">
                                            <input type="hidden" name="manage" id="hiddenManage">
                                            <input type="hidden" name="name" id="hiddenName">
                                            <input type="hidden" name="technology" id="hiddenTechnology">
                                            <input type="hidden" name="description" id="hiddenDesc">
                                            <input type="hidden" name="start_date" id="hiddenStartDate">
                                            <input type="hidden" name="end_date" id="hiddenEndDate">
                                            <input type="hidden" name="department" id="hiddenDepartment">
                                            <input type="hidden" name="status" id="hiddenStatus">
                                            <div id="hiddenMembersContainer"></div>
                                            <div id="hiddenLeadersContainer"></div>
                                            <div class="d-flex justify-content-center">
                                                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-lg" style="border-radius: 12px;">CREATE PROJECT NOW</button>
                                            </div>
                                        </form>
                                    </div>
                                </section>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #project-create-wizard h3 {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }

            .wizard>.content>section {
                display: none !important;
            }

            .wizard>.content>section.current {
                display: block !important;
            }

            .wizard>.steps {
                background: #fff !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            .wizard>.steps ul {
                display: flex !important;
                padding: 0 !important;
                margin: 0 !important;
                list-style: none !important;
                width: 100% !important;
            }

            .wizard>.steps li {
                flex: 1 !important;
                text-align: center !important;
                border-right: 1px solid #e2e8f0 !important;
            }

            .wizard>.steps a {
                display: block !important;
                padding: 25px 15px !important;
                color: #64748b !important;
                font-weight: 700 !important;
                text-decoration: none !important;
                border-bottom: 4px solid transparent !important;
                font-size: 11px !important;
                text-transform: uppercase !important;
            }

            .wizard>.steps .current a {
                color: #3454d1 !important;
                border-bottom-color: #3454d1 !important;
            }

            .wizard>.content {
                padding: 50px !important;
                background: #fff !important;
            }

            .wizard>.actions {
                padding: 20px 50px !important;
                background: #fff !important;
                border-top: 1px solid #e2e8f0 !important;
            }

            .wizard>.actions ul {
                display: flex !important;
                justify-content: flex-end !important;
                gap: 10px !important;
                list-style: none !important;
                padding: 0 !important;
            }

            .wizard>.actions a {
                display: block !important;
                padding: 10px 25px !important;
                background: #3454d1 !important;
                color: #fff !important;
                border-radius: 4px !important;
                text-decoration: none !important;
                font-weight: 600 !important;
            }

            .card-input-element {
                display: none;
            }

            .card-input-element:checked+.card {
                border-color: #3454d1 !important;
                background-color: rgba(52, 84, 209, 0.05) !important;
                border-width: 2px !important;
            }

            /* Select2 Premium Styling */
            .select2-container--default .select2-selection--single, 
            .select2-container--default .select2-selection--multiple {
                min-height: 48px !important;
                border-radius: 12px !important;
                border: 1px solid #ebf0f5 !important;
                display: flex !important;
                align-items: center !important;
                background-color: #fcfdfe !important;
                transition: all 0.2s ease;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered,
            .select2-container--default .select2-selection--multiple .select2-selection__rendered {
                color: #1a202c !important;
                font-weight: 600 !important;
                padding-left: 15px !important;
                font-size: 13px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px !important;
                right: 10px !important;
            }

            .select2-dropdown {
                border: 0 !important;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
                border-radius: 15px !important;
                overflow: hidden !important;
                margin-top: 8px !important;
                padding: 8px 0 !important;
                z-index: 99999 !important;
            }

            .select2-search--dropdown {
                padding: 12px 15px !important;
            }

            .select2-search--dropdown .select2-search__field {
                border-radius: 10px !important;
                border: 1px solid #ebf0f5 !important;
                padding: 10px 15px !important;
                background-color: #f8fafc !important;
                font-size: 13px !important;
            }

            .select2-results__option {
                padding: 10px 15px !important;
                margin: 2px 10px !important;
                border-radius: 10px !important;
                font-weight: 600 !important;
                font-size: 13px !important;
                color: #4a5568 !important;
                transition: all 0.2s ease;
            }

            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: rgba(56, 88, 249, 0.08) !important;
                color: #3858f9 !important;
            }

            .select2-container--default .select2-results__option[aria-selected="true"] {
                background-color: rgba(56, 88, 249, 0.1) !important;
                color: #3858f9 !important;
            }

            /* Multiple selection chip styling */
            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #3454d1 !important;
                border: none !important;
                color: #fff !important;
                border-radius: 6px !important;
                padding: 4px 10px !important;
                font-size: 11px !important;
                margin-top: 7px !important;
            }

            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: #fff !important;
                margin-right: 5px !important;
            }


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
        $(document).ready(function () {
            $("#project-create-wizard").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "fade",
                autoFocus: true,
                labels: { finish: "FINISH", next: "NEXT", previous: "PREVIOUS" },
                onInit: function (event, currentIndex) {
                    $("#project-create-wizard").css({ 'visibility': 'visible', 'height': 'auto', 'overflow': 'visible' });

                    // Initialize Summernote FIRST so it doesn't get blocked
                    $('#summernote-main').summernote({
                        height: 250,
                        placeholder: 'Enter detailed project description...',
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ]
                    });

                    // Initialize Select2
                    if ($.fn.select2) {
                        $('.premium-select').each(function() {
                            $(this).select2({
                                width: '100%',
                                placeholder: $(this).data('placeholder'),
                                allowClear: true,
                                minimumResultsForSearch: 0
                            });
                        });
                    }
                },
                onStepChanging: function (event, currentIndex, newIndex) {
                    // Always allow going back
                    if (currentIndex > newIndex) return true;

                    // Clear previous errors
                    $('.form-control, .form-select, .note-editor').removeClass('is-invalid');
                    $('.invalid-feedback').remove();

                    // Validation for Step 1: Type & Manage
                    if (currentIndex === 0) {
                        var type = $('input[name="type"]:checked').val();
                        var manage = $('input[name="manage"]:checked').val();
                        var hasError = false;

                        if (!type) {
                            $('[for="project_personal"]').parent().after('<div class="invalid-feedback d-block fw-bold mt-2" style="font-size: 11px; margin-left: 5px;">Please select project type.</div>');
                            hasError = true;
                        }
                        if (!manage) {
                            $('[for="project_everyone"]').parent().after('<div class="invalid-feedback d-block fw-bold mt-2" style="font-size: 11px; margin-left: 5px;">Please select management access.</div>');
                            hasError = true;
                        }

                        if (hasError) return false;
                    }

                    // Validation for Step 2: Details
                    if (currentIndex === 1) {
                        var name = $('#projectName').val();
                        var dept = $('#projectDepartment').val();
                        var desc = $('#summernote-main').summernote('code');
                        var startDate = $('#projectStartDate').val();
                        var hasError = false;

                        if (!name) {
                            $('#projectName').addClass('is-invalid').after('<div class="invalid-feedback fw-bold" style="font-size: 11px;">Project name is required.</div>');
                            hasError = true;
                        }
                        if (!startDate) {
                            $('#projectStartDate').addClass('is-invalid').after('<div class="invalid-feedback fw-bold" style="font-size: 11px;">Start date is required.</div>');
                            hasError = true;
                        }
                        if (!dept) {
                            $('#projectDepartment').addClass('is-invalid');
                            $('#projectDepartment').parent().append('<div class="invalid-feedback d-block fw-bold" style="font-size: 11px;">Department is required.</div>');
                            hasError = true;
                        }

                        if (hasError) return false;
                    }

                    // Validation for Step 3: Assigned
                    if (currentIndex === 2) {
                        var leaders = $('#projectLeaders').val();
                        if (!leaders || leaders.length === 0) {
                            $('#projectLeaders').parent().append('<div class="invalid-feedback d-block fw-bold" style="font-size: 11px;">Please select at least one Project Lead.</div>');
                            return false;
                        }
                    }

                    return true;
                },
                onFinished: function (event, currentIndex) {
                    syncAndSubmit();
                }
            });

            function syncAndSubmit() {
                // Ensure everything is synced
                $('#hiddenType').val($('input[name="type"]:checked').val());
                $('#hiddenManage').val($('input[name="manage"]:checked').val());
                $('#hiddenName').val($('#projectName').val());
                $('#hiddenTechnology').val($('#projectTechnology').val());
                $('#hiddenDesc').val($('#summernote-main').summernote('code'));
                $('#hiddenStartDate').val($('#projectStartDate').val());
                $('#hiddenEndDate').val($('#projectEndDate').val());
                $('#hiddenDepartment').val($('#projectDepartment').val());
                $('#hiddenStatus').val($('#projectStatus').val());

                // Sync Members
                var members = $('#projectMembers').val();
                var membersHtml = '';
                if (members && members.length > 0) {
                    members.forEach(function (id) {
                        membersHtml += '<input type="hidden" name="members[]" value="' + id + '">';
                    });
                }
                $('#hiddenMembersContainer').html(membersHtml);

                // Sync Leaders
                var leaders = $('#projectLeaders').val();
                var leadersHtml = '';
                if (leaders && leaders.length > 0) {
                    leaders.forEach(function (id) {
                        leadersHtml += '<input type="hidden" name="leaders[]" value="' + id + '">';
                    });
                }
                $('#hiddenLeadersContainer').html(leadersHtml);

                $('#finalCreateForm').submit();
            }

            // Also allow the manual finish button to work
            $('#finalCreateForm').on('submit', function(e) {
                // If it's not already synced, sync it
                if($('#hiddenName').val() === "") {
                    syncAndSubmit();
                }
            });
        });
    </script>
@endpush