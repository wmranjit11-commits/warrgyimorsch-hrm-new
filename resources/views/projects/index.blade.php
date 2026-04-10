@extends('layouts.app')

@section('content')

    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10" style="color: #3858f9; font-weight: 700;">Project Management</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Daily Task Master</li>
            </ul>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ main-content ] start -->
    <div class="main-content pt-4" style="margin-bottom: 100px;">
        <div class="row g-4">
            <!-- PROJECT FORM (LEFT - 4 Cols) -->
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
                    <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                        <h6 class="fw-bold mb-0 text-uppercase"
                            style="color: #64748b; font-size: 11px; letter-spacing: 0.5px;" id="formTitle">Project
                            Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <form id="projectForm" action="{{ route('projects.store') }}" method="POST">
                            @csrf
                            <div id="methodField"></div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Project Name <span class="text-danger">*</span></label>
                                @error('name')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <input type="text" name="name" id="projectName" value="{{ old('name') }}"
                                    class="form-control border-0 bg-light shadow-none @error('name') is-invalid @enderror" placeholder="Enter project Name"
                                    style="border-radius: 10px; height: 48px; font-size: 14px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Start Date <span
                                        class="text-danger">*</span></label>
                                @error('start_date')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}"
                                    class="form-control border-0 bg-light shadow-none @error('start_date') is-invalid @enderror" onclick="this.showPicker()"
                                    style="border-radius: 10px; height: 48px; font-size: 14px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">End Date <span
                                        class="text-danger">*</span></label>
                                @error('end_date')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <input type="date" name="end_date" id="endDate" value="{{ old('end_date') }}"
                                    class="form-control border-0 bg-light shadow-none @error('end_date') is-invalid @enderror" onclick="this.showPicker()"
                                    style="border-radius: 10px; height: 48px; font-size: 14px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Status <span class="text-danger">*</span></label>
                                @error('status')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <select name="status" id="projectStatus" class="form-select border-0 bg-light shadow-none @error('status') is-invalid @enderror"
                                    style="border-radius: 10px; height: 48px; font-size: 14px;">
                                    <option value="In Process" {{ old('status') == 'In Process' ? 'selected' : '' }}>In Process</option>
                                    <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="On Hold" {{ old('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Department <span
                                        class="text-danger">*</span></label>
                                @error('department')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <select name="department" id="projectDept" class="form-select border-0 bg-light shadow-none @error('department') is-invalid @enderror"
                                    style="border-radius: 10px; height: 48px; font-size: 14px;">
                                    <option value="">Select department...</option>
                                    <option value="Web Development" {{ old('department') == 'Web Development' ? 'selected' : '' }}>Web Development</option>
                                    <option value="Mobile Development" {{ old('department') == 'Mobile Development' ? 'selected' : '' }}>Mobile Development</option>
                                    <option value="Design" {{ old('department') == 'Design' ? 'selected' : '' }}>Design</option>
                                    <option value="Quality Assurance" {{ old('department') == 'Quality Assurance' ? 'selected' : '' }}>Quality Assurance</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Work Description</label>
                                @error('description')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <textarea name="description" id="projectDesc"
                                    class="form-control border-0 bg-light shadow-none @error('description') is-invalid @enderror" rows="3" placeholder="Enter Message"
                                    style="border-radius: 10px; font-size: 14px;">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Technology
                                    Name <span class="text-danger">*</span></label>
                                @error('technology')<div class="text-danger small fw-bold mb-1">{{ $message }}</div>@enderror
                                <input type="text" name="technology" id="projectTech" list="techList" value="{{ old('technology') }}"
                                    class="form-control border-0 bg-light shadow-none @error('technology') is-invalid @enderror"
                                    placeholder="Enter or select technology"
                                    style="border-radius: 10px; height: 48px; font-size: 14px;">
                                <datalist id="techList">
                                    <option value="Laravel">
                                    <option value="PHP">
                                    <option value="React.js">
                                    <option value="Vue.js">
                                    <option value="Angular">
                                    <option value="Node.js">
                                    <option value="Python">
                                    <option value="Flutter">
                                    <option value="Swift">
                                    <option value="Android / Kotlin">
                                    <option value="WordPress">
                                    <option value="Shopify">
                                </datalist>
                            </div>

                            <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-bold shadow-sm"
                                style="background: #3858f9; border: none; height: 52px; border-radius: 10px; font-size: 14px; letter-spacing: 0.5px;">
                                SAVE PROJECT
                            </button>

                            <button type="button" id="cancelBtn"
                                class="btn btn-soft-danger w-100 fw-bold mt-2 d-none align-items-center justify-content-center"
                                style="height: 48px; border-radius: 10px; font-size: 13px;" onclick="resetForm()">
                                CANCEL EDIT
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- PROJECT LIST (RIGHT - 8 Cols) -->
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; background: white;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                        style="border-radius: 12px 12px 0 0;">

                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 me-3" style="color: #334155; font-size: 16px;">Project List</h5>
                            <div class="d-flex align-items-center gap-2 d-none d-md-flex">
                                <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Show</span>
                                <select id="entriesLimit" class="form-select d-inline-block border-0 bg-light fw-bold"
                                    onchange="paginateTable()"
                                    style="width: 90px; border-radius: 10px; height: 44px; font-size: 14px; color: #1e293b; padding: 0 12px; line-height: 44px;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span class="text-muted small fw-bold text-uppercase"
                                    style="font-size: 11px;">entries</span>
                            </div>
                        </div>

                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-light border-0"><i
                                    class="feather-search text-muted"></i></span>
                            <input type="text" id="projectSearch" class="form-control bg-light border-0 shadow-none fw-bold"
                                placeholder="Search..." onkeyup="filterProjects()"
                                style="height: 44px; font-size: 14px; border-radius: 0 10px 10px 0;">
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0" id="projectsTable">
                                <thead style="background: #3858f9; color: white;">
                                    <tr style="height: 60px; vertical-align: middle;">
                                        <th class="ps-4"
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">
                                            Sr.No.</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">
                                            Project Name</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase; white-space: nowrap;">
                                            Start Date</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase; white-space: nowrap;">
                                            End Date</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">
                                            Status</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">
                                            Department</th>
                                        <th
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">
                                            Technology</th>
                                        <th class="pe-4 text-center"
                                            style="font-size: 12px; font-weight: 700; color: white; text-transform: uppercase;">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody style="border-top: 1px solid #f1f5f9;">
                                    @forelse($projects as $index => $project)
                                        <tr class="project-row" style="height: 70px;">
                                            <td class="ps-4 fw-bold" style="font-size: 14px;">{{ $index + 1 }}</td>
                                            <td class="fw-bold" style="color: #3858f9; font-size: 14px;">{{ $project->name }}
                                            </td>
                                            <td style="font-size: 14px; white-space: nowrap;">
                                                {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</td>
                                            <td style="font-size: 14px; white-space: nowrap;">
                                                {{ $project->end_date ? $project->end_date->format('d M Y') : '-' }}</td>
                                            <td>
                                                @php
                                                    $projStatusClass = 'bg-soft-primary text-primary';
                                                    if ($project->status == 'Completed') $projStatusClass = 'bg-soft-success text-success';
                                                    elseif ($project->status == 'On Hold') $projStatusClass = 'bg-soft-warning text-warning';
                                                @endphp
                                                <span class="badge {{ $projStatusClass }}"
                                                    style="padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                                    {{ $project->status }}
                                                </span>
                                            </td>
                                            <td style="font-size: 14px;">{{ $project->department }}</td>
                                            <td style="font-size: 14px;">{{ $project->technology }}</td>
                                            <td class="pe-4 text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-soft-secondary text-secondary rounded"
                                                        title="View Description" onclick="showProjectDesc({{ $project->id }})">
                                                        <i class="feather-file-text"></i>
                                                    </a>
                                                    <template id="proj_desc_{{ $project->id }}">{!! $project->description ?? '<span class="text-muted">No description provided.</span>' !!}</template>
                                                    
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
                                {{ $projects->count() }} of {{ $projects->count() }} entries</div>
                            <nav>
                                <ul class="pagination pagination-md mb-0 gap-1">
                                    <li class="page-item disabled mx-1">
                                        <a class="page-link border rounded d-flex align-items-center justify-content-center text-muted"
                                            href="javascript:void(0);" style="width: 40px; height: 40px;"><i
                                                class="feather-chevron-left"></i></a>
                                    </li>
                                    <li class="page-item active mx-1">
                                        <a class="page-link border rounded d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                            href="javascript:void(0);"
                                            style="background: #3858f9; border-color: #3858f9; width: 40px; height: 40px; font-weight: 700;">1</a>
                                    </li>
                                    <li class="page-item disabled mx-1">
                                        <a class="page-link border rounded d-flex align-items-center justify-content-center text-muted"
                                            href="javascript:void(0);" style="width: 40px; height: 40px;"><i
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

        .custom-html-content ul { list-style-type: disc !important; padding-left: 30px !important; margin-bottom: 1rem !important; list-style-position: outside !important; display: block !important; }
        .custom-html-content ol { list-style-type: decimal !important; padding-left: 30px !important; margin-bottom: 1rem !important; list-style-position: outside !important; display: block !important; }
        .custom-html-content li { display: list-item !important; margin-bottom: 0.6rem !important; list-style-type: inherit !important; }
        .custom-html-content p { margin-bottom: 1rem !important; line-height: 1.6 !important; }
        .custom-html-content { text-align: left; font-size: 15px; line-height: 1.6; color: #1e293b; padding: 25px 30px 25px 40px !important; background: #f8fafc !important; border-radius: 12px; }
        
        /* Summernote point indentation fix */
        .note-editable ul { list-style-type: disc !important; padding-left: 30px !important; list-style-position: outside !important; display: block !important; }
        .note-editable ol { list-style-type: decimal !important; padding-left: 30px !important; list-style-position: outside !important; display: block !important; }
        .note-editable li { display: list-item !important; list-style-type: inherit !important; }
        .note-editable { min-height: 200px; padding: 20px !important; background: white !important; }
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
            document.getElementById('projectForm').scrollIntoView({ behavior: 'smooth', block: 'center' });

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
            document.getElementById('cancelBtn').classList.remove('d-none');
            document.getElementById('cancelBtn').classList.add('d-flex');
        }

        function resetForm() {
            document.getElementById('projectForm').reset();
            try {
                if ($('#projectDesc').length && typeof $.fn.summernote === 'function') {
                    $('#projectDesc').summernote('code', '');
                } else {
                    document.getElementById('projectDesc').value = '';
                }
            } catch (e) {}
            document.getElementById('projectForm').action = "{{ route('projects.store') }}";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('submitBtn').innerText = 'SAVE PROJECT';
            document.getElementById('cancelBtn').classList.add('d-none');
            document.getElementById('cancelBtn').classList.remove('d-flex');
        }

        function filterProjects() {
            const input = document.getElementById('projectSearch');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('.project-row');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });

            paginateTable();
        }

        function showProjectDesc(id) {
            const html = document.getElementById('proj_desc_' + id).innerHTML;
            Swal.fire({
                title: 'Project Description',
                html: `<div class="custom-html-content" style="max-height: 60vh; overflow-y: auto; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">${html}</div>`,
                showConfirmButton: true,
                confirmButtonColor: '#3858f9'
            });
        }

        $(document).ready(function() {
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
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents, $editable) {
                        $('#projectDesc').val(contents);
                    }
                }
            });

            // CRITICAL: Sync Summernote before form submission
            $('#projectForm').on('submit', function() {
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
    </script>
    
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        });
    </script>
    @endif
@endpush
@endsection