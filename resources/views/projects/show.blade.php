@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Projects</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item">Project Details</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary">
                        <i class="feather-edit-3 me-2"></i>
                        <span>Edit Project</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <div class="bg-white py-3 border-bottom rounded-0 p-md-0 mb-0">
        <div class="d-flex align-items-center justify-content-between">
            <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs nav-tabs-custom-style" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overviewTab">Overview</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#activityTab">Activity</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#discussionsTab">Discussions</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-pane fade active show" id="overviewTab">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card stretch stretch-full overflow-hidden shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="d-md-flex align-items-center justify-content-between">
                                    <div class="hstack gap-4">
                                        <div class="avatar-image border-0 position-relative">
                                            <!-- Premium SVG Circular Progress -->
                                            <div class="progress-ring-wrapper"
                                                style="position: relative; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                                @php $progressVal = $project->progress; @endphp
                                                <svg width="70" height="70" viewBox="0 0 100 100"
                                                    style="position: absolute; transform: rotate(-90deg);">
                                                    <!-- Background Track -->
                                                    <circle cx="50" cy="50" r="42" fill="none" stroke="#f1f5f9"
                                                        stroke-width="10"></circle>
                                                    <!-- Progress Bar -->
                                                    <circle cx="50" cy="50" r="42" fill="none" stroke="#1d4ed8"
                                                        stroke-width="10" stroke-dasharray="263.89"
                                                        stroke-dashoffset="{{ 263.89 * (1 - $progressVal / 100) }}"
                                                        stroke-linecap="round"
                                                        style="transition: stroke-dashoffset 0.8s ease-in-out;"></circle>
                                                </svg>
                                                <div class="avatar-text avatar-xl bg-white text-primary rounded-circle shadow-sm"
                                                    style="width: 54px; height: 54px; display: flex; align-items: center; justify-content: center; z-index: 1; border: 1px solid rgba(0,0,0,0.05);">
                                                    <i class="feather-briefcase fs-30"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="fw-bold text-dark mb-1">{{ $project->name }}</h3>
                                            <div class="hstack gap-3 fs-12 text-muted">
                                                <span
                                                    class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">{{ $project->status }}</span>
                                                <span class="vr"></span>
                                                <span><i class="feather-calendar me-1"></i>
                                                    {{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</span>
                                                <span class="vr"></span>
                                                <span><i class="feather-tag me-1"></i> {{ $project->technology }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 mt-md-0 d-flex gap-2">
                                        <a href="{{ route('projects.edit', $project->id) }}"
                                            class="btn btn-light-brand border shadow-sm px-4">
                                            <i class="feather-edit-3 me-2"></i><span>Edit</span>
                                        </a>
                                        <button class="btn btn-primary px-4 shadow-sm">
                                            <i class="feather-share-2 me-2"></i><span>Share</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $startDate = $project->start_date;
                        $endDate = $project->end_date;
                        $today = now();
                        $progress = 0;

                        if ($startDate && $endDate) {
                            $totalDuration = $startDate->diffInDays($endDate);
                            $elapsedDuration = $startDate->diffInDays($today);

                            if ($today < $startDate) {
                                $progress = 0;
                            } elseif ($today > $endDate) {
                                $progress = 100;
                            } else {
                                $progress = $totalDuration > 0 ? round(($elapsedDuration / $totalDuration) * 100) : 0;
                            }
                        } elseif ($startDate && !$endDate) {
                            $progress = 50; // Default if no end date
                        }

                        if ($project->status == 'Finished' || $project->status == 'Completed')
                            $progress = 100;
                    @endphp

                    <div class="col-xl-12">
                        <div class="card stretch stretch-full border-0 shadow-sm mb-4">
                            <div class="card-header border-bottom-0 pt-4 px-4">
                                <h5 class="card-title fw-bold">Overview & Description</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row g-4 mb-5">
                                    <div class="col-sm-4">
                                        <div class="p-3 border rounded-3 bg-light bg-opacity-10">
                                            <span
                                                class="fs-11 text-muted text-uppercase fw-bold d-block mb-1">Department</span>
                                            <span class="fw-bold text-dark">{{ $project->department }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="p-3 border rounded-3 bg-light bg-opacity-10">
                                            <span class="fs-11 text-muted text-uppercase fw-bold d-block mb-1">Start
                                                Date</span>
                                            <span
                                                class="fw-bold text-dark">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="p-3 border rounded-3 bg-light bg-opacity-10">
                                            <span class="fs-11 text-muted text-uppercase fw-bold d-block mb-1">Due
                                                Date</span>
                                            <span
                                                class="fw-bold text-dark">{{ $project->end_date ? $project->end_date->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark mb-3">Project Description</h6>
                                <div class="text-muted lh-lg fs-14">
                                    {!! $project->description !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row g-4">
                            <!-- Progress Graph Card -->
                            <div class="col-xl-4">
                                <div class="card stretch stretch-full border-0 shadow-sm">
                                    <div class="card-body p-4 text-center">
                                        <h6 class="fw-bold text-dark mb-4">Timeline Progress</h6>
                                        <div id="project-progress-chart"></div>
                                        <div
                                            class="hstack justify-content-center gap-3 text-muted fs-11 mt-3 pt-3 border-top">
                                            <span>Status: <strong
                                                    class="text-primary">{{ $project->status }}</strong></span>
                                            <span class="vr"></span>
                                            <span>Progress: <strong class="text-dark">{{ $progress }}%</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Details Card -->
                            <div class="col-xl-8">
                                <div class="card stretch stretch-full border-0 shadow-sm">
                                    <div class="card-header border-bottom-0 pt-4 px-4">
                                        <h6 class="card-title fw-bold">Project Team</h6>
                                    </div>
                                    <div class="card-body p-4 pt-2">
                                        <div class="row">
                                            <div class="col-md-6 border-end">
                                                <span class="fs-10 text-muted text-uppercase fw-bold d-block mb-3">Project
                                                    Leads</span>
                                                @php $leaders = is_array($project->leaders) ? $project->leaders : []; @endphp
                                                @forelse($employees->whereIn('id', $leaders) as $emp)
                                                    <div class="hstack gap-3 mb-3">
                                                        <div
                                                            class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle">
                                                            {{ substr($emp->name, 0, 1) }}</div>
                                                        <div>
                                                            <div class="fs-13 fw-bold text-dark">{{ $emp->name }}</div>
                                                            <div class="fs-11 text-muted">Lead Designer</div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <span class="text-muted fs-12">No leads assigned.</span>
                                                @endforelse
                                            </div>
                                            <div class="col-md-6">
                                                <span class="fs-10 text-muted text-uppercase fw-bold d-block mb-3">Team
                                                    Members</span>
                                                @php $members = is_array($project->members) ? $project->members : []; @endphp
                                                <div class="row g-3">
                                                    @forelse($employees->whereIn('id', $members) as $emp)
                                                        <div class="col-6">
                                                            <div class="hstack gap-2">
                                                                <div
                                                                    class="avatar-text avatar-sm bg-soft-info text-info rounded-circle">
                                                                    {{ substr($emp->name, 0, 1) }}</div>
                                                                <div class="fs-12 fw-medium text-dark">{{ $emp->name }}</div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-12 text-muted fs-12">No members assigned.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div class="tab-pane fade" id="activityTab">
                <style>
                    .activity-description ul {
                        list-style-type: disc !important;
                        padding-left: 25px !important;
                        margin-bottom: 10px !important;
                        display: block !important;
                    }

                    .activity-description ol {
                        list-style-type: decimal !important;
                        padding-left: 25px !important;
                        margin-bottom: 10px !important;
                        display: block !important;
                    }

                    .activity-description li {
                        display: list-item !important;
                        margin-bottom: 5px !important;
                        list-style-type: inherit !important;
                    }

                    .activity-description p {
                        margin-bottom: 10px !important;
                    }
                </style>
                <div class="activity-feed">
                    @forelse($activities as $activity)
                        <div class="card mb-3 border shadow-none" style="border-radius: 12px; background: #fff;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-text avatar-sm bg-soft-primary text-primary rounded-circle fw-bold"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            {{ substr($activity->dailyTask->employee->name ?? 'U', 0, 1) }}
                                        </div>
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">
                                            {{ $activity->dailyTask->employee->name ?? 'Unknown' }}</h6>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-end">
                                        <span class="fw-bold text-primary"
                                            style="font-size: 13px;">{{ $activity->created_at->format('h:i A') }}</span>
                                        <span class="text-muted small"
                                            style="font-size: 11px;">{{ $activity->created_at->format('d M, Y') }}</span>
                                    </div>
                                </div>

                                <div class="ps-1">
                                    <div class="text-muted fs-13 mb-2 activity-description" style="line-height: 1.6;">
                                        @php
                                            // First decode in case it's escaped, then strip tags but keep lists
                                            $desc = html_entity_decode($activity->work_description);
                                            $desc = strip_tags($desc, '<ul><li><ol><p><br><b><strong>');

                                            // Improved regex: only match if it's not already inside a tag or looks like a tag attribute
                                            // We'll also use a simpler replacement to avoid conflicts
                                            $desc = preg_replace('/(?<![="])(\d+)\s*(Hours|Hour|hrs|hr)(?![^<]*>)/i', '<span class="badge bg-soft-warning text-warning fw-bold px-2 py-1 ms-1"><i class="feather-clock me-1" style="font-size: 10px;"></i>$1 $2</span>', $desc);
                                        @endphp
                                        {!! $desc !!}
                                    </div>
                                    @if($activity->photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $activity->photo) }}" class="img-fluid rounded border"
                                                style="max-height: 110px; cursor: pointer; transition: opacity 0.2s;"
                                                onclick="window.open(this.src)" onmouseover="this.style.opacity='0.9'"
                                                onmouseout="this.style.opacity='1'">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="avatar-text avatar-xl bg-soft-secondary text-secondary mx-auto mb-3 rounded-circle">
                                <i class="feather-activity"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Activity Yet</h5>
                            <p class="text-muted small">There is no activity on this project</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Discussions Tab -->
            <div class="tab-pane fade" id="discussionsTab">
                <div class="card stretch stretch-full border-0 shadow-sm">
                    <div class="card-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="card-title fw-bold mb-0">Project Discussions & Meetings</h6>
                        <button class="btn btn-primary btn-sm"><i class="feather-plus me-1"></i> New Meeting</button>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center py-5">
                            <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-4 rounded-circle">
                                <i class="feather-message-square fs-30"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Discussions Yet</h5>
                            <p class="text-muted fs-13">Start a new discussion or log a meeting related to this project.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var options = {
                series: [{{ $progress }}],
                chart: {
                    height: 250,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        hollow: { size: '70%', },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                offsetY: 10,
                                fontSize: '22px',
                                fontWeight: 'bold',
                                formatter: function (val) { return val + "%"; }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#3454d1'],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100]
                    }
                },
                stroke: { lineCap: 'round' },
                labels: ['Progress'],
            };

            var chart = new ApexCharts(document.querySelector("#project-progress-chart"), options);
            chart.render();

            // --- Persist Active Tab on Refresh (Using localStorage) ---
            var storageKey = 'activeProjectTab_' + '{{ $project->id }}';
            var activeTab = localStorage.getItem(storageKey);

            if (activeTab) {
                var tabTrigger = $('.nav-tabs button[data-bs-target="' + activeTab + '"]');
                if (tabTrigger.length) {
                    tabTrigger.tab('show');
                }
            }

            // Update localStorage on tab click
            $('.nav-tabs button').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr('data-bs-target');
                localStorage.setItem(storageKey, target);
            });
        });
    </script>
@endpush