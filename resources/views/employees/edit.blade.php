@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Edit Employee</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <a href="{{ route('employees.index') }}" class="btn btn-md btn-light-brand">
                    <i class="feather-arrow-left me-2"></i> Back to List
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <style>
        @media (max-width: 576px) {
            .nav-tabs .nav-link {
                text-align: left;
                padding-left: 20px !important;
                border-radius: 0 !important;
                border-bottom: 1px solid #eee !important;
            }

            .nav-tabs .nav-link.active {
                background: #fff !important;
                border-left: 4px solid #6366f1 !important;
            }

            .card.stretch {
                border-radius: 0 !important;
                margin: -15px !important;
            }

            .tab-content {
                padding: 15px !important;
            }

            .page-header {
                padding: 15px !important;
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .page-header-right {
                margin-top: 10px !important;
                margin-left: 0 !important;
                width: 100% !important;
            }

            .btn-lg {
                width: 100% !important;
                margin-bottom: 10px !important;
                margin-left: 0 !important;
            }
        }
    </style>

    <!-- [ Main Content ] start -->
    <div class="main-content">

        <!-- ERROR MESSAGES -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <strong><i class="feather-alert-circle me-2"></i> Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- SUCCESS MESSAGE -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="feather-check-circle me-2"></i> <strong>{{ session('success') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card stretch stretch-full">
            <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-justified mb-0 flex-column flex-sm-row custom-tabs" id="employeeTab" role="tablist"
                    style="background: #f1f5f9; border-radius: 12px 12px 0 0; padding: 8px 8px 0 8px; border: none;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold active w-100" id="personal-tab" data-bs-toggle="tab"
                            data-bs-target="#personal" type="button" role="tab" aria-controls="personal"
                            aria-selected="true"
                            style="font-size: 15px; padding: 12px 0; border: none; border-radius: 10px 10px 0 0; transition: all 0.3s ease;">
                            <i class="bi bi-person-circle me-2"></i>Personal Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold w-100" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank"
                            type="button" role="tab" aria-controls="bank" aria-selected="false"
                            style="font-size: 15px; padding: 12px 0; border: none; border-radius: 10px 10px 0 0; transition: all 0.3s ease;">
                            <i class="bi bi-bank2 me-2"></i>Bank Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold w-100" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary"
                            type="button" role="tab" aria-controls="salary" aria-selected="false"
                            style="font-size: 15px; padding: 12px 0; border: none; border-radius: 10px 10px 0 0; transition: all 0.3s ease;">
                            <i class="bi bi-cash-coin me-2"></i>Salary Details</button>
                    </li>
                </ul>

                <style>
                    .custom-tabs .nav-link {
                        color: #64748b;
                        background: transparent !important;
                    }
                    .custom-tabs .nav-link.active {
                        color: #3858f9 !important;
                        background: #fff !important;
                        position: relative;
                    }
                    /* Smooth Animation for Tabs */
                    .tab-pane {
                        animation: fadeIn 0.4s ease-out;
                    }
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(5px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    .custom-tabs .nav-link:not(.active):hover {
                        background: rgba(255,255,255,0.4) !important;
                        color: #334155;
                    }
                </style>

                <div class="tab-content p-4" id="employeeTabContent" style="background: #fff; border-radius: 0 0 10px 10px;">

                    <!-- Personal Details Tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="fw-bold">Employee Code</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" name="employee_code" class="form-control"
                                        placeholder="Enter employee code"
                                        value="{{ old('employee_code', $employee->employee_code) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Name <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Enter employee name"
                                        value="{{ old('name', $employee->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Email</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-mail"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Enter email"
                                        value="{{ old('email', $employee->email) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="fw-bold">Role</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <select name="role" class="form-control" required>
                                        <option value="">Select Role</option>
                                        <option value="super_admin" {{ old('role', $employee->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="business_operation_head" {{ old('role', $employee->role) == 'business_operation_head' ? 'selected' : '' }}>Business
                                            Operation Head</option>
                                        <option value="hr_executive" {{ old('role', $employee->role) == 'hr_executive' ? 'selected' : '' }}>HR Executive</option>
                                        <option value="team_leader" {{ old('role', $employee->role) == 'team_leader' ? 'selected' : '' }}>Team Leader</option>
                                        <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                                        <option value="hr_marketing" {{ old('role', $employee->role) == 'hr_marketing' ? 'selected' : '' }}>HR Marketing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-phone"></i></span>
                                    <input type="text" name="mobile_number" class="form-control"
                                        placeholder="Enter mobile number"
                                        value="{{ old('mobile_number', $employee->mobile_number) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Department</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-users"></i></span>
                                    <select name="department" class="form-control" required>
                                        <option value="">Select department...</option>
                                        <option value="administration" {{ old('department', $employee->department) == 'administration' ? 'selected' : '' }}>Administration
                                            (Admin)</option>
                                        <option value="business_development" {{ old('department', $employee->department) == 'business_development' ? 'selected' : '' }}>Business
                                            Development (BD)</option>
                                        <option value="hr" {{ old('department', $employee->department) == 'hr' ? 'selected' : '' }}>HR Department (HR)</option>
                                        <option value="web_development" {{ old('department', $employee->department) == 'web_development' ? 'selected' : '' }}>Web Development
                                            (WD)</option>
                                        <option value="digital_marketing" {{ old('department', $employee->department) == 'digital_marketing' ? 'selected' : '' }}>Digital
                                            Marketing (DM)</option>
                                        <option value="web_graphics" {{ old('department', $employee->department) == 'web_graphics' ? 'selected' : '' }}>Web & Graphics
                                            Design (WGD)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Designation <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-users"></i></span>
                                    <select name="designation" class="form-control" required>
                                        <option value="">Select designation...</option>

                                        <!-- Management -->
                                        <option value="Chief Executive Officer (CEO)" {{ old('designation', $employee->designation) == 'Chief Executive Officer (CEO)' ? 'selected' : '' }}>
                                            Chief Executive Officer (CEO)</option>
                                        <option value="Chief Finance Officer (CFO)" {{ old('designation', $employee->designation) == 'Chief Finance Officer (CFO)' ? 'selected' : '' }}>
                                            Chief Finance Officer (CFO)</option>
                                        <option value="Chief Technology Officer (CTO)" {{ old('designation', $employee->designation) == 'Chief Technology Officer (CTO)' ? 'selected' : '' }}>
                                            Chief Technology Officer (CTO)</option>
                                        <option value="Project Manager" {{ old('designation', $employee->designation) == 'Project Manager' ? 'selected' : '' }}>Project Manager
                                        </option>
                                        <option value="Team Lead / Tech Lead" {{ old('designation', $employee->designation) == 'Team Lead / Tech Lead' ? 'selected' : '' }}>Team Lead
                                            / Tech Lead</option>

                                        <!-- Development -->
                                        <option value="Software Engineer / Developer" {{ old('designation', $employee->designation) == 'Software Engineer / Developer' ? 'selected' : '' }}>
                                            Software Engineer / Developer</option>
                                        <option value="Frontend Developer (React / Next.js)" {{ old('designation', $employee->designation) == 'Frontend Developer (React / Next.js)' ? 'selected' : '' }}>Frontend Developer (React / Next.js)</option>
                                        <option value="Backend Developer (Laravel / Node.js)" {{ old('designation', $employee->designation) == 'Backend Developer (Laravel / Node.js)' ? 'selected' : '' }}>Backend Developer (Laravel / Node.js)</option>
                                        <option value="Full Stack Developer" {{ old('designation', $employee->designation) == 'Full Stack Developer' ? 'selected' : '' }}>Full Stack
                                            Developer</option>
                                        <option value="Mobile App Developer (Flutter / Android / iOS)" {{ old('designation', $employee->designation) == 'Mobile App Developer (Flutter / Android / iOS)' ? 'selected' : '' }}>Mobile App Developer (Flutter / Android / iOS)</option>
                                        <option value="Web Developer Intern" {{ old('designation', $employee->designation) == 'Web Developer Intern' ? 'selected' : '' }}>Web
                                            Developer Intern</option>

                                        <!-- Specialized -->
                                        <option value="DevOps Engineer" {{ old('designation', $employee->designation) == 'DevOps Engineer' ? 'selected' : '' }}>DevOps Engineer
                                        </option>
                                        <option value="Cloud Engineer (AWS / Azure / GCP)" {{ old('designation', $employee->designation) == 'Cloud Engineer (AWS / Azure / GCP)' ? 'selected' : '' }}>Cloud Engineer (AWS / Azure / GCP)</option>
                                        <option value="Data Science Engineer" {{ old('designation', $employee->designation) == 'Data Science Engineer' ? 'selected' : '' }}>Data
                                            Science Engineer</option>
                                        <option value="AI / Machine Learning Engineer" {{ old('designation', $employee->designation) == 'AI / Machine Learning Engineer' ? 'selected' : '' }}>
                                            AI / Machine Learning Engineer</option>

                                        <!-- Testing -->
                                        <option value="QA Engineer / Tester" {{ old('designation', $employee->designation) == 'QA Engineer / Tester' ? 'selected' : '' }}>QA Engineer
                                            / Tester</option>
                                        <option value="Automation Test Engineer" {{ old('designation', $employee->designation) == 'Automation Test Engineer' ? 'selected' : '' }}>
                                            Automation Test Engineer</option>

                                        <!-- Design -->
                                        <option value="UI/UX Designer" {{ old('designation', $employee->designation) == 'UI/UX Designer' ? 'selected' : '' }}>UI/UX Designer</option>
                                        <option value="Graphic Designer" {{ old('designation', $employee->designation) == 'Graphic Designer' ? 'selected' : '' }}>Graphic
                                            Designer</option>
                                        <option value="Social Media Executive" {{ old('designation', $employee->designation) == 'Social Media Executive' ? 'selected' : '' }}>Social
                                            Media Executive</option>

                                        <!-- Support -->
                                        <option value="System Administrator" {{ old('designation', $employee->designation) == 'System Administrator' ? 'selected' : '' }}>System
                                            Administrator</option>
                                        <option value="IT Support Engineer" {{ old('designation', $employee->designation) == 'IT Support Engineer' ? 'selected' : '' }}>IT Support
                                            Engineer</option>

                                        <!-- Business -->
                                        <option value="Business Development Manager (BDM)" {{ old('designation', $employee->designation) == 'Business Development Manager (BDM)' ? 'selected' : '' }}>Business Development Manager (BDM)</option>
                                        <option value="Sales Executive" {{ old('designation', $employee->designation) == 'Sales Executive' ? 'selected' : '' }}>Sales Executive
                                        </option>
                                        <option value="Digital Marketing Executive" {{ old('designation', $employee->designation) == 'Digital Marketing Executive' ? 'selected' : '' }}>
                                            Digital Marketing Executive</option>
                                        <option value="SEO Executive" {{ old('designation', $employee->designation) == 'SEO Executive' ? 'selected' : '' }}>SEO Executive</option>
                                        <option value="SEO Intern" {{ old('designation', $employee->designation) == 'SEO Intern' ? 'selected' : '' }}>SEO Intern</option>

                                        <!-- HR -->
                                        <option value="HR Manager" {{ old('designation', $employee->designation) == 'HR Manager' ? 'selected' : '' }}>HR Manager</option>
                                        <option value="HR Executive" {{ old('designation', $employee->designation) == 'HR Executive' ? 'selected' : '' }}>HR Executive</option>
                                        <option value="HR Intern" {{ old('designation', $employee->designation) == 'HR Intern' ? 'selected' : '' }}>HR Intern</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Date of Joining</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                    <input type="date" name="date_of_joining" class="form-control"
                                        value="{{ old('date_of_joining', $employee->date_of_joining) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Date of Birth</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Gender</label><br>
                                <div class="d-flex align-items-center mb-3">
                                    <input class="form-check-input me-2" type="radio" name="gender" value="male" {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }}>
                                    <span class="me-2 text-success"><i class=""></i></span>Male
                                    <input class="form-check-input ms-4 me-2" type="radio" name="gender" value="female" {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }}>
                                    <span class="me-2 text-danger"><i class=""></i></span>Female
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="fw-bold">Password (Leave blank to keep current)</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Aadhaar Number</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                    <input type="text" name="aadhaar_number" class="form-control"
                                        placeholder="Enter Aadhaar Number"
                                        value="{{ old('aadhaar_number', $employee->aadhaar_number) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">PAN Number</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                    <input type="text" name="pan_number" class="form-control" placeholder="E.G. ABCDE2548K"
                                        value="{{ old('pan_number', $employee->pan_number) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Address</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-map-pin"></i></span>
                                    <textarea name="address" class="form-control"
                                        placeholder="Enter Address">{{ old('address', $employee->address) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Time In</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-clock"></i></span>
                                    <input type="time" name="time_in" class="form-control"
                                        value="{{ old('time_in', $employee->time_in) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Time Out</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-clock"></i></span>
                                    <input type="time" name="time_out" class="form-control"
                                        value="{{ old('time_out', $employee->time_out) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Leave</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                    <input type="text" name="leave" class="form-control"
                                        value="{{ old('leave', $employee->leave) }}" placeholder="Enter Leave Allotment">
                                </div>
                            </div>

                            <!-- PF, ESI, Insurance Toggles and Fields -->
                            <div class="col-md-12 mt-4">
                                <div class="row g-3">
                                    <!-- PF Section -->
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="pf" id="pfToggle" {{ old('pf', $employee->pf) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="pfToggle">Eligible For PF</label>
                                        </div>
                                        <div id="pfField"
                                            style="display: {{ old('pf', $employee->pf) ? 'block' : 'none' }};">
                                            <label class="fw-bold small">PF No.</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <input type="text" name="pf_number" class="form-control"
                                                    placeholder="PF No."
                                                    value="{{ old('pf_number', $employee->pf_number) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ESI Section -->
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="esi" id="esiToggle" {{ old('esi', $employee->esi) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="esiToggle">Eligible For ESI</label>
                                        </div>
                                        <div id="esiField"
                                            style="display: {{ old('esi', $employee->esi) ? 'block' : 'none' }};">
                                            <label class="fw-bold small">ESI No.</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shield"></i></span>
                                                <input type="text" name="esi_number" class="form-control"
                                                    placeholder="ESI No."
                                                    value="{{ old('esi_number', $employee->esi_number) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Insurance Section -->
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="insurance"
                                                id="insuranceToggle" {{ old('insurance', $employee->insurance) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="insuranceToggle">Insurance</label>
                                        </div>
                                        <div id="insuranceFields"
                                            style="display: {{ old('insurance', $employee->insurance) ? 'block' : 'none' }};">
                                            <div class="mb-2">
                                                <label class="fw-bold small">Provider</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-shield"></i></span>
                                                    <input type="text" name="insurance_provider" class="form-control"
                                                        placeholder="Insurance Company"
                                                        value="{{ old('insurance_provider', $employee->insurance_provider) }}">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="fw-bold small">Policy Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                                    <input type="text" name="insurance_policy_number" class="form-control"
                                                        placeholder="Policy Number"
                                                        value="{{ old('insurance_policy_number', $employee->insurance_policy_number) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PHOTO UPLOAD -->
                                    <!-- LEFT SIDE -->
                                    <div class="col-md-4 d-flex align-items-center mt-3 mt:md-3">

                                        <!-- Hidden Input -->
                                        <input type="file" id="photoInput" name="photo" accept="image/*" hidden>

                                        <!-- Image Box -->
                                        <div onclick="document.getElementById('photoInput').click()"
                                            class="bg-light border d-flex align-items-center justify-content-center"
                                            style="width:120px; height:120px; cursor:pointer; overflow:hidden; border-radius: 15px; border: 2px dashed #cbd5e1 !important;">

                                            <img id="previewImg"
                                                src="{{ $employee->photo ? asset('storage/' . $employee->photo) : '' }}"
                                                style="width:100%; height:100%; object-fit:cover; display: {{ $employee->photo ? 'block' : 'none' }};">

                                            <span id="placeholderText"
                                                style="color:#64748b; font-weight: 600; font-size: 12px; display: {{ $employee->photo ? 'none' : 'block' }}; text-transform: uppercase;">SELECT
                                                PHOTO</span>
                                        </div>

                                        <!-- TEXT SIDE -->
                                        <div class="ms-3">
                                            <label class="fw-bold d-block">Upload Photo</label>
                                            <small class="text-muted d-block"># Max upload size 2mb</small>
                                            <small class="text-muted d-block"># Allowed: png, jpg, jpeg, webp</small>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- JS for Toggle Logic -->
                            <script>
                                document.getElementById('pfToggle').addEventListener('change', function () {
                                    document.getElementById('pfField').style.display = this.checked ? 'block' : 'none';
                                });
                                document.getElementById('esiToggle').addEventListener('change', function () {
                                    document.getElementById('esiField').style.display = this.checked ? 'block' : 'none';
                                });
                                document.getElementById('insuranceToggle').addEventListener('change', function () {
                                    document.getElementById('insuranceFields').style.display = this.checked ? 'block' : 'none';
                                });

                                document.getElementById('photoInput').addEventListener('change', function (event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = function (e) {
                                            document.getElementById('previewImg').src = e.target.result;
                                            document.getElementById('previewImg').style.display = 'block';
                                            document.getElementById('placeholderText').style.display = 'none';
                                        }
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-primary" onclick="nextTab('bank')">
                                Next <i class="feather-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Bank Details Tab -->
                    <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="fw-bold">Bank Name <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-home"></i></span>
                                    <input type="text" name="bank_name" class="form-control" placeholder="Bank Name"
                                        value="{{ old('bank_name', $employee->bank_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Account Number <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                    <input type="text" name="account_number" class="form-control"
                                        placeholder="Account Number"
                                        value="{{ old('account_number', $employee->account_number) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">IFSC Code <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-key"></i></span>
                                    <input type="text" name="ifsc_code" class="form-control" placeholder="IFSC Code"
                                        value="{{ old('ifsc_code', $employee->ifsc_code) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light" onclick="prevTab('personal')">
                                <i class="feather-arrow-left"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextTab('salary')">
                                Next <i class="feather-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Salary Details Tab -->
                    <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="fw-bold">Basic Salary <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                    <input type="number" name="basic_salary" id="basic_salary"
                                        class="form-control salary-input" placeholder="Basic Salary"
                                        value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">HRA</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-home"></i></span>
                                    <input type="number" name="hra" id="hra" class="form-control salary-input"
                                        value="{{ old('hra', $employee->hra) }}" placeholder="HRA">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Conveyance Allowance</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-truck"></i></span>
                                    <input type="number" name="conveyance_allowance" id="conveyance_allowance"
                                        class="form-control salary-input"
                                        value="{{ old('conveyance_allowance', $employee->conveyance_allowance) }}"
                                        placeholder="Conveyance Allowance">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Medical Allowance</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-activity"></i></span>
                                    <input type="number" name="medical_allowance" id="medical_allowance"
                                        class="form-control salary-input"
                                        value="{{ old('medical_allowance', $employee->medical_allowance) }}"
                                        placeholder="Medical Allowance">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Other Allowance</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-gift"></i></span>
                                    <input type="number" name="other_allowance" id="other_allowance"
                                        class="form-control salary-input"
                                        value="{{ old('other_allowance', $employee->other_allowance) }}"
                                        placeholder="Other Allowance">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Total Salary</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-credit-card"></i></span>
                                    <input type="number" id="total_salary" class="form-control bg-light" value="0.00"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="feather-check me-2"></i> UPDATE EMPLOYEE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
        // Tab Navigation with Validation
        function nextTab(targetTabId) {
            const currentTab = document.querySelector('.tab-pane.show.active');
            const requiredFields = currentTab.querySelectorAll('[required]');
            let missingFields = [];

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    let labelText = "a required field";
                    const container = field.closest('.col-md-4, .col-md-6, .col-md-12');
                    if (container) {
                        const label = container.querySelector('label');
                        if (label) labelText = label.innerText.replace('*', '').trim();
                    }
                    missingFields.push(labelText);
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (missingFields.length > 0) {
                alert("Please fill these required fields:\n- " + missingFields.join("\n- "));
                return;
            }

            // Standard Way to switch tabs
            const triggerEl = document.querySelector('#' + targetTabId + '-tab');
            if (triggerEl) {
                const tabInstance = bootstrap.Tab.getOrCreateInstance(triggerEl);
                tabInstance.show();
                window.scrollTo(0, 0); // Scroll to top of tab
            }
        }

        function prevTab(tabId) {
            const triggerEl = document.querySelector('#' + tabId + '-tab');
            if (triggerEl) {
                const tabInstance = bootstrap.Tab.getOrCreateInstance(triggerEl);
                tabInstance.show();
                window.scrollTo(0, 0);
            }
        }

        // Form Submit Validation
        document.querySelector('form').addEventListener('submit', function (e) {
            const requiredFields = this.querySelectorAll('[required]');
            let missingFields = [];

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    let labelText = "a required field";
                    const container = field.closest('.col-md-4, .col-md-6, .col-md-12');
                    if (container) {
                        const label = container.querySelector('label');
                        if (label) labelText = label.innerText.replace('*', '').trim();
                    }
                    missingFields.push(labelText);
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (missingFields.length > 0) {
                e.preventDefault();
                alert("Cannot submit. Please fill these required fields:\n- " + missingFields.join("\n- "));

                const firstInvalid = this.querySelector('.is-invalid');
                if (firstInvalid) {
                    const tabPane = firstInvalid.closest('.tab-pane');
                    const tabId = tabPane.id;
                    const tabLink = new bootstrap.Tab(document.querySelector('#' + tabId + '-tab'));
                    tabLink.show();
                }
            }
        });

        // Salary Calculation
        document.querySelectorAll('.salary-input').forEach(input => {
            input.addEventListener('input', calculateTotalSalary);
        });

        function calculateTotalSalary() {
            const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
            const hra = parseFloat(document.getElementById('hra').value) || 0;
            const conveyance = parseFloat(document.getElementById('conveyance_allowance').value) || 0;
            const medical = parseFloat(document.getElementById('medical_allowance').value) || 0;
            const other = parseFloat(document.getElementById('other_allowance').value) || 0;

            const total = basic + hra + conveyance + medical + other;
            document.getElementById('total_salary').value = total.toFixed(2);
        }

        // Initialize total on load
        window.addEventListener('load', calculateTotalSalary);
    </script>
@endsection