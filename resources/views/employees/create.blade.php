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



        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
            @csrf
            <!-- Tab Navigation with custom style -->
            <ul class="nav nav-tabs nav-justified mb-0 flex-column flex-sm-row" id="employeeTab" role="tablist"
                style="background: #f8f9fa; border-radius: 10px 10px 0 0; border-bottom: 2px solid #dee2e6;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold active w-100" id="personal-tab" data-bs-toggle="tab"
                        data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true"
                        style="font-size: 16px; padding: 15px 0; border: none; background: #f8f9fa; border-radius: 10px 10px 0 0;">
                        <i class="bi bi-person-circle me-2"></i>Personal Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold w-100" id="bank-tab" type="button" data-bs-toggle="tab"
                        data-bs-target="#bank" role="tab" aria-controls="bank" aria-selected="false"
                        style="font-size: 16px; padding: 15px 0; border: none; background: #f8f9fa; border-radius: 10px 10px 0 0;">
                        <i class="bi bi-bank2 me-2"></i>Bank Details</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold w-100" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary"
                        type="button" role="tab" aria-controls="salary" aria-selected="false"
                        style="font-size: 16px; padding: 15px 0; border: none; background: #f8f9fa; border-radius: 10px 10px 0 0;">
                        <i class="bi bi-cash-coin me-2"></i>Salary Details</button>
                </li>
            </ul>
            <div class="tab-content p-4" id="employeeTabContent"
                style="background: #fff; border-radius: 0 0 10px 10px; border: 1px solid #e3e6ef; border-top: none;">
                <!-- Personal Details Tab -->
                <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="fw-bold">Employee Code</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code') }}"
                                    placeholder="Enter employee code">
                                @error('employee_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Name <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter employee name"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Email</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-mail"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold">Role</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="hr_executive" {{ old('role') == 'hr_executive' ? 'selected' : '' }}>HR Executive</option>
                                    <option value="hr_intern" {{ old('role') == 'hr_intern' ? 'selected' : '' }}>HR Intern</option>
                                    <option value="team_leader" {{ old('role') == 'team_leader' ? 'selected' : '' }}>Team Leader</option>
                                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-phone"></i></span>
                                <input type="text" name="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror" value="{{ old('mobile_number') }}"
                                    placeholder="Enter mobile number" required>
                                @error('mobile_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Department</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-users"></i></span>
                                <select name="department" class="form-control @error('department') is-invalid @enderror" required>
                                    <option value="">Select department...</option>
                                    <option value="administration" {{ old('department') == 'administration' ? 'selected' : '' }}>Administration (Admin)</option>
                                    <option value="business_development" {{ old('department') == 'business_development' ? 'selected' : '' }}>Business Development (BD)</option>
                                    <option value="hr" {{ old('department') == 'hr' ? 'selected' : '' }}>HR Department (HR)</option>
                                    <option value="web_development" {{ old('department') == 'web_development' ? 'selected' : '' }}>Web Development (WD)</option>
                                    <option value="digital_marketing" {{ old('department') == 'digital_marketing' ? 'selected' : '' }}>Digital Marketing (DM)</option>
                                    <option value="web_graphics" {{ old('department') == 'web_graphics' ? 'selected' : '' }}>Web & Graphics Design (WGD)</option>
                                    <!-- aur add karo jo chahiye -->
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Designation <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-users"></i></span>
                                <select name="designation" class="form-control @error('designation') is-invalid @enderror" required>
                                    <option value="">Select designation...</option>
                                    @php
                                        $designations = [
                                            'Chief Executive Officer (CEO)', 'Chief Finance Officer (CFO)', 'Chief Technology Officer (CTO)',
                                            'Project Manager', 'Team Lead / Tech Lead', 'Software Engineer / Developer',
                                            'Frontend Developer (React / Next.js)', 'Backend Developer (Laravel / Node.js)',
                                            'Full Stack Developer', 'Mobile App Developer (Flutter / Android / iOS)',
                                            'Web Developer Intern', 'DevOps Engineer', 'Cloud Engineer (AWS / Azure / GCP)',
                                            'Data Science Engineer', 'AI / Machine Learning Engineer', 'QA Engineer / Tester',
                                            'Automation Test Engineer', 'UI/UX Designer', 'Graphic Designer',
                                            'Social Media Executive', 'System Administrator', 'IT Support Engineer',
                                            'Business Development Manager (BDM)', 'Sales Executive', 'Digital Marketing Executive',
                                            'SEO Executive', 'SEO Intern', 'HR Manager', 'HR Executive', 'HR Intern'
                                        ];
                                    @endphp
                                    @foreach($designations as $dsg)
                                        <option value="{{ $dsg }}" {{ old('designation') == $dsg ? 'selected' : '' }}>{{ $dsg }}</option>
                                    @endforeach
                                </select>
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Date of Joining</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                <input type="date" name="date_of_joining" class="form-control @error('date_of_joining') is-invalid @enderror" value="{{ old('date_of_joining', \Carbon\Carbon::today()->toDateString()) }}">
                                @error('date_of_joining')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Date of Birth</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Aadhaar Number</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                <input type="text" name="aadhaar_number" class="form-control @error('aadhaar_number') is-invalid @enderror" value="{{ old('aadhaar_number') }}"
                                    placeholder="Enter Aadhaar Number">
                                @error('aadhaar_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">PAN Number</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                <input type="text" name="pan_number" class="form-control @error('pan_number') is-invalid @enderror" value="{{ old('pan_number') }}" placeholder="E.G. ABCDE2548K">
                                @error('pan_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Address</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-map-pin"></i></span>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Time In</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-clock"></i></span>
                                <input type="time" name="time_in" class="form-control @error('time_in') is-invalid @enderror" value="{{ old('time_in', '09:30') }}">
                                @error('time_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Time Out</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-clock"></i></span>
                                <input type="time" name="time_out" class="form-control @error('time_out') is-invalid @enderror" value="{{ old('time_out', '18:00') }}">
                                @error('time_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Leave</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                <input type="text" name="leave" class="form-control @error('leave') is-invalid @enderror" value="{{ old('leave') }}"
                                    placeholder="Enter Leave Allotment">
                                @error('leave')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                    <div id="pfField" style="display: none;">
                                        <label class="fw-bold small">PF No.</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                                            <input type="text" name="pf_number" class="form-control @error('pf_number') is-invalid @enderror" value="{{ old('pf_number') }}" placeholder="PF No.">
                                            @error('pf_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ESI Section -->
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="esi" id="esiToggle" {{ old('esi') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="esiToggle">Eligible For ESI</label>
                                    </div>
                                    <div id="esiField" style="display: none;">
                                        <label class="fw-bold small">ESI No.</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-shield"></i></span>
                                            <input type="text" name="esi_number" class="form-control @error('esi_number') is-invalid @enderror" value="{{ old('esi_number') }}" placeholder="ESI No.">
                                            @error('esi_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                    <div id="insuranceFields" style="display: none;">
                                        <div class="mb-2">
                                            <label class="fw-bold small">Provider</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-shield"></i></span>
                                                <input type="text" name="insurance_provider" class="form-control @error('insurance_provider') is-invalid @enderror"
                                                    value="{{ old('insurance_provider') }}" placeholder="Insurance Company">
                                                @error('insurance_provider')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div>
                                            <label class="fw-bold small">Policy Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                                <input type="text" name="insurance_policy_number" class="form-control @error('insurance_policy_number') is-invalid @enderror"
                                                    value="{{ old('insurance_policy_number') }}" placeholder="Policy Number">
                                                @error('insurance_policy_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" placeholder="Bank Name" required>
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Account Number <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-hash"></i></span>
                                <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" placeholder="Account Number"
                                    required>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">IFSC Code <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-key"></i></span>
                                <input type="text" name="ifsc_code" class="form-control @error('ifsc_code') is-invalid @enderror" value="{{ old('ifsc_code') }}" placeholder="IFSC Code" required>
                                @error('ifsc_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <input type="number" name="basic_salary" id="basic_salary" class="form-control salary-input @error('basic_salary') is-invalid @enderror"
                                    value="{{ old('basic_salary') }}" placeholder="Basic Salary" required>
                                @error('basic_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">HRA</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-home"></i></span>
                                <input type="number" name="hra" id="hra" class="form-control salary-input @error('hra') is-invalid @enderror"
                                    value="{{ old('hra') }}" placeholder="HRA">
                                @error('hra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Conveyance Allowance</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-truck"></i></span>
                                <input type="number" name="conveyance_allowance" id="conveyance_allowance"
                                    class="form-control salary-input @error('conveyance_allowance') is-invalid @enderror" 
                                    value="{{ old('conveyance_allowance') }}" placeholder="Conveyance Allowance">
                                @error('conveyance_allowance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Medical Allowance</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-activity"></i></span>
                                <input type="number" name="medical_allowance" id="medical_allowance"
                                    class="form-control salary-input @error('medical_allowance') is-invalid @enderror" 
                                    value="{{ old('medical_allowance') }}" placeholder="Medical Allowance">
                                @error('medical_allowance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold">Other Allowance</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="feather-gift"></i></span>
                                <input type="number" name="other_allowance" id="other_allowance"
                                    class="form-control salary-input @error('other_allowance') is-invalid @enderror" 
                                    value="{{ old('other_allowance') }}" placeholder="Other Allowance">
                                @error('other_allowance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

        // Initialize total on load if values exist and switch to error tab
        window.addEventListener('load', function() {
            calculateTotalSalary();
            
            // Auto-switch to tab with validation errors
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                const tabPane = firstError.closest('.tab-pane');
                if (tabPane) {
                    const tabId = tabPane.id;
                    const tabTrigger = document.querySelector('#' + tabId + '-tab');
                    if (tabTrigger) {
                        bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                    }
                }
            }

            // Set initial visibility for toggles based on old values
            const pfToggle = document.getElementById('pfToggle');
            if (pfToggle) document.getElementById('pfField').style.display = pfToggle.checked ? 'block' : 'none';
            
            const esiToggle = document.getElementById('esiToggle');
            if (esiToggle) document.getElementById('esiField').style.display = esiToggle.checked ? 'block' : 'none';
            
            const insuranceToggle = document.getElementById('insuranceToggle');
            if (insuranceToggle) document.getElementById('insuranceFields').style.display = insuranceToggle.checked ? 'block' : 'none';
        });
    </script>

@endsection