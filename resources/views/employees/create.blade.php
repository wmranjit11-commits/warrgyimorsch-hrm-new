@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Add Employee</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                <li class="breadcrumb-item">Add New</li>
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



        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-justified mb-0 flex-column flex-sm-row custom-tabs" id="employeeTab" role="tablist"
                style="background: #f1f5f9; border-radius: 12px 12px 0 0; padding: 8px 8px 0 8px; border: none;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold active w-100" id="personal-tab" data-bs-toggle="tab"
                        data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true"
                        style="font-size: 15px; padding: 12px 0; border: none; border-radius: 10px 10px 0 0; transition: all 0.3s ease;">
                        <i class="bi bi-person-circle me-2"></i>Personal Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold w-100" id="bank-tab" type="button" data-bs-toggle="tab"
                        data-bs-target="#bank" role="tab" aria-controls="bank" aria-selected="false"
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
                                    placeholder="Enter employee code" value="{{ old('employee_code') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Name <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Enter employee name"
                                    value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Email</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-mail"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ old('email') }}" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold">Role</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="hr_executive" {{ old('role') == 'hr_executive' ? 'selected' : '' }}>HR Executive</option>
                                    <option value="hr_intern" {{ old('role') == 'hr_intern' ? 'selected' : '' }}>HR Intern</option>
                                    <option value="team_leader" {{ old('role') == 'team_leader' ? 'selected' : '' }}>Team Leader</option>
                                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-phone"></i></span>
                                <input type="text" name="mobile_number" class="form-control"
                                    placeholder="Enter mobile number" value="{{ old('mobile_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Department</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-users"></i></span>
                                <select name="department" class="form-control" required>
                                    <option value="">Select department...</option>
                                    <option value="administration" {{ old('department') == 'administration' ? 'selected' : '' }}>Administration (Admin)</option>
                                    <option value="business_development" {{ old('department') == 'business_development' ? 'selected' : '' }}>Business Development (BD)</option>
                                    <option value="hr" {{ old('department') == 'hr' ? 'selected' : '' }}>HR Department (HR)</option>
                                    <option value="web_development" {{ old('department') == 'web_development' ? 'selected' : '' }}>Web Development (WD)</option>
                                    <option value="digital_marketing" {{ old('department') == 'digital_marketing' ? 'selected' : '' }}>Digital Marketing (DM)</option>
                                    <option value="web_graphics" {{ old('department') == 'web_graphics' ? 'selected' : '' }}>Web & Graphics Design (WGD)</option>
                                    <!-- aur add karo jo chahiye -->
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
                                    <option value="Chief Executive Officer (CEO)">Chief Executive Officer (CEO)</option>
                                    <option value="Chief Finance Officer (CFO)">Chief Finance Officer (CFO)</option>
                                    <option value="Chief Technology Officer (CTO)">Chief Technology Officer (CTO)</option>
                                    <option value="Project Manager">Project Manager</option>
                                    <option value="Team Lead / Tech Lead">Team Lead / Tech Lead</option>

                                    <!-- Development -->
                                    <option value="Software Engineer / Developer">Software Engineer / Developer</option>
                                    <option value="Frontend Developer (React / Next.js)">Frontend Developer (React /
                                        Next.js)</option>
                                    <option value="Backend Developer (Laravel / Node.js)">Backend Developer (Laravel /
                                        Node.js)</option>
                                    <option value="Full Stack Developer">Full Stack Developer</option>
                                    <option value="Mobile App Developer (Flutter / Android / iOS)">Mobile App Developer
                                        (Flutter / Android / iOS)</option>
                                    <option value="Web Developer Intern">Web Developer Intern</option>

                                    <!-- Specialized -->
                                    <option value="DevOps Engineer">DevOps Engineer</option>
                                    <option value="Cloud Engineer (AWS / Azure / GCP)">Cloud Engineer (AWS / Azure / GCP)
                                    </option>
                                    <option value="Data Science Engineer">Data Science Engineer</option>
                                    <option value="AI / Machine Learning Engineer">AI / Machine Learning Engineer</option>

                                    <!-- Testing -->
                                    <option value="QA Engineer / Tester">QA Engineer / Tester</option>
                                    <option value="Automation Test Engineer">Automation Test Engineer</option>

                                    <!-- Design -->
                                    <option value="UI/UX Designer">UI/UX Designer</option>
                                    <option value="Graphic Designer">Graphic Designer</option>
                                    <option value="Social Media Executive">Social Media Executive</option>

                                    <!-- Support -->
                                    <option value="System Administrator">System Administrator</option>
                                    <option value="IT Support Engineer">IT Support Engineer</option>

                                    <!-- Business -->
                                    <option value="Business Development Manager (BDM)">Business Development Manager (BDM)
                                    </option>
                                    <option value="Sales Executive">Sales Executive</option>
                                    <option value="Digital Marketing Executive">Digital Marketing Executive</option>
                                    <option value="SEO Executive">SEO Executive</option>
                                    <option value="SEO Intern">SEO Intern</option>

                                    <!-- HR -->
                                    <option value="HR Manager">HR Manager</option>
                                    <option value="HR Executive">HR Executive</option>
                                    <option value="HR Intern">HR Intern</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Date of Joining</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Date of Birth</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Gender</label><br>
                            <div class="d-flex align-items-center mb-3">
                                <input class="form-check-input me-2" type="radio" name="gender" value="male" {{ old('gender', 'male') == 'male' ? 'checked' : '' }}>
                                <span class="me-2 text-success"><i class=""></i></span>Male
                                <input class="form-check-input ms-4 me-2" type="radio" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                                <span class="me-2 text-danger"><i class=""></i></span>Female
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold">Password</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-lock"></i></span>
                                <input type="password" name="password" id="createEmpPassword" class="form-control" placeholder="Password" autocomplete="new-password" value="">
                                <span class="input-group-text" onclick="togglePassword('createEmpPassword', this)" style="cursor:pointer; background:#fff; border-left:none; transition: color 0.2s;" onmouseover="this.style.color='#3858f9'" onmouseout="this.style.color='#94a3b8'">
                                    <i class="feather-eye-off"></i>
                                </span>
                            </div>
                        </div>
                        <script>
                            function togglePassword(inputId, el) {
                                const input = document.getElementById(inputId);
                                const icon = el.querySelector('i');
                                if (input.type === 'password') { input.type = 'text'; icon.className = 'feather-eye'; }
                                else { input.type = 'password'; icon.className = 'feather-eye-off'; }
                            }
                        </script>
                        <div class="col-md-4">
                            <label class="fw-bold">Aadhaar Number</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                <input type="text" name="aadhaar_number" class="form-control"
                                    placeholder="Enter Aadhaar Number" value="{{ old('aadhaar_number') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">PAN Number</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                <input type="text" name="pan_number" class="form-control" placeholder="E.G. ABCDE2548K" value="{{ old('pan_number') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Address</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-map-pin"></i></span>
                                <textarea name="address" class="form-control" placeholder="Enter Address">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Time In</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-clock"></i></span>
                                <input type="time" name="time_in" class="form-control" value="{{ old('time_in', '09:30') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Time Out</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-clock"></i></span>
                                <input type="time" name="time_out" class="form-control" value="{{ old('time_out', '18:00') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Leave</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                <input type="text" name="leave" class="form-control" value="{{ old('leave') }}"
                                    placeholder="Enter Leave Allotment">
                            </div>
                        </div>
                        <!-- <div class="row align-items-center"> -->


                        <!-- PF, ESI, Insurance Toggles and Fields -->
                        <div class="col-md-12 mt-4">
                            <div class="row g-3">
                                <!-- PF Section -->
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="pf" id="pfToggle" {{ old('pf') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="pfToggle">Eligible For PF</label>
                                    </div>
                                        <div id="pfField" style="display: {{ old('pf') ? 'block' : 'none' }};">
                                            <label class="fw-bold small">PF No.</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                                <input type="text" name="pf_number" class="form-control" placeholder="PF No." value="{{ old('pf_number') }}">
                                            </div>
                                        </div>
                                </div>

                                <!-- ESI Section -->
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="esi" id="esiToggle" {{ old('esi') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="esiToggle">Eligible For ESI</label>
                                    </div>
                                        <div id="esiField" style="display: {{ old('esi') ? 'block' : 'none' }};">
                                            <label class="fw-bold small">ESI No.</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shield"></i></span>
                                                <input type="text" name="esi_number" class="form-control" placeholder="ESI No." value="{{ old('esi_number') }}">
                                            </div>
                                        </div>
                                </div>

                                <!-- Insurance Section -->
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="insurance"
                                            id="insuranceToggle" {{ old('insurance') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="insuranceToggle">Insurance</label>
                                    </div>
                                    <div id="insuranceFields" style="display: {{ old('insurance') ? 'block' : 'none' }};">
                                        <div class="mb-2">
                                            <label class="fw-bold small">Provider</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shield"></i></span>
                                                <input type="text" name="insurance_provider" class="form-control"
                                                    placeholder="Insurance Company" value="{{ old('insurance_provider') }}">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="fw-bold small">Policy Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                                <input type="text" name="insurance_policy_number" class="form-control"
                                                    placeholder="Policy Number" value="{{ old('insurance_policy_number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- LEFT SIDE -->
                                <div class="col-md-4 d-flex align-items-center mt-3 mt:md-3">

                                    <!-- Hidden Input -->
                                    <input type="file" id="photoInput" name="photo" accept="image/*" hidden>

                                    <!-- Image Box -->
                                    <div onclick="document.getElementById('photoInput').click()"
                                        class="bg-light border d-flex align-items-center justify-content-center"
                                        style="width:120px; height:120px; cursor:pointer; overflow:hidden; border-radius: 15px; border: 2px dashed #cbd5e1 !important;">

                                        <img id="previewImg" src=""
                                            style="width:100%; height:100%; object-fit:cover; display:none;">

                                        <span id="placeholderText"
                                            style="color:#64748b; font-weight: 600; font-size: 12px; text-transform: uppercase;">SELECT
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
                                <input type="text" name="bank_name" class="form-control" placeholder="Bank Name" value="{{ old('bank_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Account Number <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-hash"></i></span>
                                <input type="text" name="account_number" class="form-control" placeholder="Account Number"
                                    value="{{ old('account_number') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">IFSC Code <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-key"></i></span>
                                <input type="text" name="ifsc_code" class="form-control" placeholder="IFSC Code" value="{{ old('ifsc_code') }}" required>
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
                                <input type="number" name="basic_salary" id="basic_salary" class="form-control salary-input"
                                    placeholder="Basic Salary" value="{{ old('basic_salary') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">HRA</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-home"></i></span>
                                <input type="number" name="hra" id="hra" class="form-control salary-input"
                                    placeholder="HRA" value="{{ old('hra') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Conveyance Allowance</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-truck"></i></span>
                                <input type="number" name="conveyance_allowance" id="conveyance_allowance"
                                    class="form-control salary-input" placeholder="Conveyance Allowance" value="{{ old('conveyance_allowance') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Medical Allowance</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-activity"></i></span>
                                <input type="number" name="medical_allowance" id="medical_allowance"
                                    class="form-control salary-input" placeholder="Medical Allowance" value="{{ old('medical_allowance') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Other Allowance</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-gift"></i></span>
                                <input type="number" name="other_allowance" id="other_allowance"
                                    class="form-control salary-input" placeholder="Other Allowance" value="{{ old('other_allowance') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Total Salary</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-credit-card"></i></span>
                                <input type="number" id="total_salary" class="form-control bg-light" value="0.00" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-light" onclick="prevTab('bank')">
                            <i class="feather-arrow-left"></i> Previous
                        </button>

                        <button type="submit" class="btn btn-success">
                            <i class="feather-check"></i> Submit Employee
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- [ Main Content ] end -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@@1.10.5/font/bootstrap-icons.css">
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

            const triggerEl = document.querySelector('#' + targetTabId + '-tab');
            if (triggerEl) {
                const tabInstance = bootstrap.Tab.getOrCreateInstance(triggerEl);
                tabInstance.show();
                window.scrollTo(0, 0);
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
                    const label = field.closest('.col-md-4, .col-md-12').querySelector('label').innerText.replace('*', '').trim();
                    missingFields.push(label);
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (missingFields.length > 0) {
                e.preventDefault();
                const message = "Cannot submit. Please fill these required fields:\n- " + missingFields.join("\n- ");
                alert(message);

                // Switch to the first tab with an error
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

        // Initialize total on load if values exist
        window.addEventListener('load', calculateTotalSalary);
    </script>

@endsection