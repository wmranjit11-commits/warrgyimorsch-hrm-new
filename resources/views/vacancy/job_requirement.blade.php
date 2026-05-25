@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Job Requirement</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Job Requirement</li>
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
                        <a href="javascript:void(0);" id="btn-bulk-delete" class="btn btn-icon btn-soft-danger"
                            style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; 
                            justify-content: center;">
                            <i class="feather-trash-2 fs-18"></i>
                        </a>
                    </div>
                    <!-- <div class="filter-toggle-wrapper">
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" id="toggleFilter"
                            style="cursor: pointer;">
                            <i class="feather-filter"></i>
                        </a>
                    </div>
                    <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="offcanvas" 
                    data-bs-target="#projectOffcanvas">
                        <i class="feather-plus me-2"></i>
                        <span>Add</span>
                    </a> -->
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="container mt-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-white">Job Requirement</h4>
                </div>

                <div class="card-body">
                    <form action="{{route('requirement.store')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Role</label>
                                <select name="role_id" class="form-select" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="">Select</option>
                                    <option>Urgent</option>
                                    <option>High</option>
                                    <option>Medium</option>
                                    <option>Low</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Fresher / Experience</label>
                                <select name="candidate_type" id="candidateType" class="form-select">
                                    <option value="">Select</option>
                                    <option>Fresher</option>
                                    <option>Experience</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 d-none" id="experienceDiv">
                                <label>Minimum Experience</label>
                                <input type="number" name="minimum_experience" class="form-control" placeholder="Years">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Skills</label>
                                <input type="text" name="skills" class="form-control" placeholder="HTML,CSS,Javascript,React">
                                <small>Use comma separated skills</small>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card mt-4 shadow">
            <div class="card-header">
                <h5>Requirement List</h5>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role</th>
                            <th>Priority</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Experience</th>
                            <th>Skills</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($requirements as $item)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->role_name}}</td>
                                <td><span class="badge bg-danger">{{$item->priority}}</span></td>
                                <td>{{$item->date}}</td>
                                <td>{{$item->candidate_type}}</td>
                                <td>{{$item->minimum_experience ?? '-'}}</td>
                                <td>
                                    @php
                                        $skills = is_array($item->skills)
                                            ? $item->skills
                                            : json_decode($item->skills, true);

                                        if (!is_array($skills)) {
                                            $skills = explode(',', $item->skills);
                                        }
                                    @endphp

                                    @foreach($skills as $skill)
                                        <span class="badge bg-info">{{ trim($skill) }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const candidateType = document.getElementById('candidateType');
            const experienceDiv = document.getElementById('experienceDiv');
            
            // Handle change event
            candidateType.addEventListener('change', function () {
                if (this.value === 'Experience') {
                    experienceDiv.classList.remove('d-none');
                } else {
                    experienceDiv.classList.add('d-none');
                }
            });
        });
    </script>
@endpush
