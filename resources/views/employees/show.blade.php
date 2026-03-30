@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-0">Employee Details</h5>
                <small class="text-muted">{{ $employee->name }}
                    (EC{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }})</small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="row">
                    <!-- LEFT: PHOTO -->
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}"
                                    style="width:200px;height:200px;border-radius:8px;object-fit:cover;">
                            @else
                                <div
                                    style="width:200px;height:200px;background:#ddd;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                                    <span class="text-muted">200x200</span>
                                </div>
                            @endif
                        </div>

                        <!-- FLAGS -->
                        <div class="mt-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="pf" {{ $employee->pf ? 'checked' : '' }}
                                    disabled>
                                <label class="form-check-label" for="pf">Eligible For PF</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="esi" {{ $employee->esi ? 'checked' : '' }} disabled>
                                <label class="form-check-label" for="esi">Eligible For ESI</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="insurance" {{ $employee->insurance ? 'checked' : '' }} disabled>
                                <label class="form-check-label" for="insurance">Insurance</label>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: DETAILS TABS -->
                    <div class="col-md-8">

                        <ul class="nav nav-tabs mb-3" id="employeeTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="personal-tab" data-bs-toggle="tab"
                                    data-bs-target="#personal" type="button" role="tab" aria-controls="personal"
                                    aria-selected="true">Personal</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank"
                                    type="button" role="tab" aria-controls="bank" aria-selected="false">Bank</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary"
                                    type="button" role="tab" aria-controls="salary" aria-selected="false">Salary</button>
                            </li>
                        </ul>

                        <!-- PERSONAL TAB -->
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Name</label>
                                        <p class="text-dark">{{ $employee->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Email</label>
                                        <p class="text-dark">{{ $employee->email ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Mobile Number</label>
                                        <p class="text-dark">{{ $employee->mobile_number }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Gender</label>
                                        <p class="text-dark">{{ ucfirst($employee->gender ?? 'N/A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Role</label>
                                        <p class="text-dark">{{ ucwords(str_replace('_', ' ', $employee->role)) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Department</label>
                                        <p class="text-dark">{{ ucwords(str_replace('_', ' ', $employee->department)) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Designation</label>
                                        <p class="text-dark">{{ $employee->designation }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Date of Joining</label>
                                        <p class="text-dark">{{ $employee->date_of_joining ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Date of Birth</label>
                                        <p class="text-dark">{{ $employee->date_of_birth ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- BANK TAB -->
                            <div class="tab-pane fade" id="bank" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Bank Name</label>
                                        <p class="text-dark">{{ $employee->bank_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Account Number</label>
                                        <p class="text-dark">{{ $employee->account_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">IFSC Code</label>
                                        <p class="text-dark">{{ $employee->ifsc_code ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- SALARY TAB -->
                            <div class="tab-pane fade" id="salary" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Basic Salary</label>
                                        <p class="text-dark">₹ {{ number_format($employee->basic_salary ?? 0, 2) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">HRA</label>
                                        <p class="text-dark">₹ {{ number_format($employee->hra ?? 0, 2) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-muted">Conveyance Allowance</label>
                                        <p class="text-dark">₹ {{ number_format($employee->conveyance_allowance ?? 0, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection