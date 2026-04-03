@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center gap-3">
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-light-brand text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Employee</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item">Edit</li>
                </ul>
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
                <!-- Tab Navigation with custom style -->
                <ul class="nav nav-tabs nav-justified mb-0 flex-column flex-sm-row" id="employeeTab" role="tablist"
                    style="background: #f8f9fa; border-radius: 10px 10px 0 0; border-bottom: 2px solid #dee2e6;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold active w-100" id="personal-tab" data-bs-toggle="tab"
                            data-bs-target="#personal" type="button" role="tab" aria-controls="personal"
                            aria-selected="true"
                            style="font-size: 16px; padding: 15px 0; border: none; background: #f8f9fa; border-radius: 10px 10px 0 0;">
                            <i class="bi bi-person-circle me-2"></i>Personal Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold w-100" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank"
                            type="button" role="tab" aria-controls="bank" aria-selected="false"
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
                                <label class="fw-bold">Employee Type</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <select name="employee_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="permanent" {{ old('employee_type', $employee->employee_type) == 'permanent' ? 'selected' : '' }}>Employee</option>
                                        <option value="contract" {{ old('employee_type', $employee->employee_type) == 'contract' ? 'selected' : '' }}>Worker</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Role</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <select name="role" class="form-control" required>
                                        <option value="">Select Role</option>
                                        <option value="super_admin" {{ old('role', $employee->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="admin" {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="manager" {{ old('role', $employee->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="team_leader" {{ old('role', $employee->role) == 'team_leader' ? 'selected' : '' }}>Team Leader</option>
                                        <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                                        <option value="hr_exec" {{ old('role', $employee->role) == 'hr_exec' ? 'selected' : '' }}>HR Exec</option>
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
                                        <option value="software_development" {{ old('department', $employee->department) == 'software_development' ? 'selected' : '' }}>Software
                                            Development</option>
                                        <option value="mobile_app_development" {{ old('department', $employee->department) == 'mobile_app_development' ? 'selected' : '' }}>Mobile App
                                            Development</option>
                                        <option value="ui_ux_design" {{ old('department', $employee->department) == 'ui_ux_design' ? 'selected' : '' }}>UI/UX Design
                                        </option>
                                        <option value="quality_assurance" {{ old('department', $employee->department) == 'quality_assurance' ? 'selected' : '' }}>Quality
                                            Assurance (QA)</option>
                                        <option value="devops_infrastructure" {{ old('department', $employee->department) == 'devops_infrastructure' ? 'selected' : '' }}>DevOps &
                                            Infrastructure</option>
                                        <option value="digital_marketing" {{ old('department', $employee->department) == 'digital_marketing' ? 'selected' : '' }}>Digital
                                            Marketing</option>
                                        <option value="sales_business_development" {{ old('department', $employee->department) == 'sales_business_development' ? 'selected' : '' }}>Sales
                                            & Business Development</option>
                                        <option value="hr" {{ old('department', $employee->department) == 'hr' ? 'selected' : '' }}>Human Resources (HR)</option>
                                        <option value="finance_accounts" {{ old('department', $employee->department) == 'finance_accounts' ? 'selected' : '' }}>Finance &
                                            Accounts</option>
                                        <option value="management_administration" {{ old('department', $employee->department) == 'management_administration' ? 'selected' : '' }}>
                                            Management / Administration</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Designation <span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-users"></i></span>
                                    <select name="designation" class="form-control" required>
                                        <option value="">Select designation...</option>
                                        <option value="project_manager" {{ old('designation', $employee->designation) == 'project_manager' ? 'selected' : '' }}>Project Manager
                                        </option>
                                        <option value="team_lead" {{ old('designation', $employee->designation) == 'team_lead' ? 'selected' : '' }}>Team Lead</option>
                                        <option value="senior_software_engineer" {{ old('designation', $employee->designation) == 'senior_software_engineer' ? 'selected' : '' }}>Senior
                                            Software Engineer</option>
                                        <option value="software_engineer" {{ old('designation', $employee->designation) == 'software_engineer' ? 'selected' : '' }}>Software
                                            Engineer</option>
                                        <option value="junior_software_engineer" {{ old('designation', $employee->designation) == 'junior_software_engineer' ? 'selected' : '' }}>Junior
                                            Software Engineer</option>
                                        <option value="frontend_developer" {{ old('designation', $employee->designation) == 'frontend_developer' ? 'selected' : '' }}>Frontend
                                            Developer</option>
                                        <option value="backend_developer" {{ old('designation', $employee->designation) == 'backend_developer' ? 'selected' : '' }}>Backend
                                            Developer</option>
                                        <option value="full_stack_developer" {{ old('designation', $employee->designation) == 'full_stack_developer' ? 'selected' : '' }}>Full Stack
                                            Developer</option>
                                        <option value="mobile_app_developer" {{ old('designation', $employee->designation) == 'mobile_app_developer' ? 'selected' : '' }}>Mobile App
                                            Developer (iOS/Android)</option>
                                        <option value="ui_ux_designer" {{ old('designation', $employee->designation) == 'ui_ux_designer' ? 'selected' : '' }}>UI/UX Designer
                                        </option>
                                        <option value="qa_engineer" {{ old('designation', $employee->designation) == 'qa_engineer' ? 'selected' : '' }}>QA Engineer / Tester
                                        </option>
                                        <option value="devops_engineer" {{ old('designation', $employee->designation) == 'devops_engineer' ? 'selected' : '' }}>DevOps Engineer
                                        </option>
                                        <option value="system_administrator" {{ old('designation', $employee->designation) == 'system_administrator' ? 'selected' : '' }}>System
                                            Administrator</option>
                                        <option value="seo_specialist" {{ old('designation', $employee->designation) == 'seo_specialist' ? 'selected' : '' }}>SEO Specialist
                                        </option>
                                        <option value="content_writer" {{ old('designation', $employee->designation) == 'content_writer' ? 'selected' : '' }}>Content Writer
                                        </option>
                                        <option value="hr_manager" {{ old('designation', $employee->designation) == 'hr_manager' ? 'selected' : '' }}>HR Manager /
                                            Executive</option>
                                        <option value="bde_sales_executive" {{ old('designation', $employee->designation) == 'bde_sales_executive' ? 'selected' : '' }}>BDE / Sales
                                            Executive</option>
                                        <option value="accountant" {{ old('designation', $employee->designation) == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="office_assistant" {{ old('designation', $employee->designation) == 'office_assistant' ? 'selected' : '' }}>Office
                                            Assistant</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Date of Joining</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                    <input type="date" name="date_of_joining" class="form-control"
                                        style="cursor: pointer;" onclick="this.showPicker()"
                                        value="{{ old('date_of_joining', $employee->date_of_joining) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Date of Birth</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        style="cursor: pointer;" onclick="this.showPicker()"
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
                                <label class="fw-bold">Username</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-key"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="Username"
                                        value="{{ old('username', $employee->username) }}">
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
                                        style="cursor: pointer;" onclick="this.showPicker()"
                                        value="{{ old('time_in', $employee->time_in) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Time Out</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-clock"></i></span>
                                    <input type="time" name="time_out" class="form-control"
                                        style="cursor: pointer;" onclick="this.showPicker()"
                                        value="{{ old('time_out', $employee->time_out) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Leave</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                    <input type="number" name="leave" class="form-control"
                                        value="{{ old('leave', $employee->leave) }}" step="0.5">
                                </div>
                            </div>
                            <!-- Statutory Toggles -->
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="pf" id="pfToggle" style="width: 40px; height: 20px;" {{ $employee->pf ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-2" for="pfToggle">PF</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="esi" id="esiToggle" style="width: 40px; height: 20px;" {{ $employee->esi ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-2" for="esiToggle">ESI</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="insurance" id="insToggle" style="width: 40px; height: 20px;" {{ $employee->insurance ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-2" for="insToggle">Insurance</label>
                                </div>
                            </div>

                            <!-- Dynamic Input Fields -->
                            <div class="col-md-4" id="pf_div" style="display: {{ $employee->pf ? 'block' : 'none' }};">
                                <label class="fw-bold">PF Number</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                    <input type="text" name="pf_number" class="form-control" placeholder="Enter PF Number" value="{{ old('pf_number', $employee->pf_number) }}">
                                </div>
                            </div>

                            <div class="col-md-4" id="esi_div" style="display: {{ $employee->esi ? 'block' : 'none' }};">
                                <label class="fw-bold">ESI Number</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                    <input type="text" name="esi_number" class="form-control" placeholder="Enter ESI Number" value="{{ old('esi_number', $employee->esi_number) }}">
                                </div>
                            </div>

                            <div class="col-md-4" id="ins_prov_div" style="display: {{ $employee->insurance ? 'block' : 'none' }};">
                                <label class="fw-bold">Insurance Provider</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-shield"></i></span>
                                    <input type="text" name="insurance_provider" class="form-control" placeholder="Provider Name" value="{{ old('insurance_provider', $employee->insurance_provider) }}">
                                </div>
                            </div>

                            <div class="col-md-4" id="ins_pol_div" style="display: {{ $employee->insurance ? 'block' : 'none' }};">
                                <label class="fw-bold">Policy Number</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-file-text"></i></span>
                                    <input type="text" name="insurance_policy_number" class="form-control" placeholder="Policy Number" value="{{ old('insurance_policy_number', $employee->insurance_policy_number) }}">
                                </div>
                            </div>

                            <script>
                                document.getElementById('pfToggle').addEventListener('change', function() {
                                    document.getElementById('pf_div').style.display = this.checked ? 'block' : 'none';
                                });
                                document.getElementById('esiToggle').addEventListener('change', function() {
                                    document.getElementById('esi_div').style.display = this.checked ? 'block' : 'none';
                                });
                                document.getElementById('insToggle').addEventListener('change', function() {
                                    const isChecked = this.checked;
                                    document.getElementById('ins_prov_div').style.display = isChecked ? 'block' : 'none';
                                    document.getElementById('ins_pol_div').style.display = isChecked ? 'block' : 'none';
                                });
                            </script>
                            <!-- PHOTO UPLOAD SECTION -->
                            <div class="col-md-12 mt-4">
                                <div class="row g-3">
                                    <div class="col-md-4 d-flex align-items-center">
                                        <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
                                        <div onclick="document.getElementById('photoInput').click()"
                                            class="bg-light border d-flex align-items-center justify-content-center"
                                            style="width:120px; height:120px; cursor:pointer; overflow:hidden;">
                                            <img id="previewImg"
                                                src="{{ $employee->photo ? '/storage/' . $employee->photo : '' }}"
                                                style="width:100%; height:100%; object-fit:cover; display: {{ $employee->photo ? 'block' : 'none' }};">
                                            <span id="placeholderText"
                                                style="color:#888; display: {{ $employee->photo ? 'none' : 'block' }};">200x200</span>
                                        </div>
                                        <div class="ms-3">
                                            <label class="fw-bold d-block">Upload Photo</label>
                                            <small class="text-muted d-block"># Max upload size 2mb</small>
                                            <small class="text-muted d-block"># Allowed: png, jpg, jpeg</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- JS for Photo Preview -->
                            <script>
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
                                <label class="fw-bold">Bank Name</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-home"></i></span>
                                    <input type="text" name="bank_name" class="form-control" placeholder="Bank Name"
                                        value="{{ old('bank_name', $employee->bank_name) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Account Number</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                    <input type="text" name="account_number" class="form-control"
                                        placeholder="Account Number"
                                        value="{{ old('account_number', $employee->account_number) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">IFSC Code</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="feather-key"></i></span>
                                    <input type="text" name="ifsc_code" class="form-control" placeholder="IFSC Code"
                                        value="{{ old('ifsc_code', $employee->ifsc_code) }}">
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