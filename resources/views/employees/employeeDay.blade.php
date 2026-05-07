@extends('layouts.app')

@section('content')

<div class="employee-day-wrapper">

    <!-- Header -->
    <div class="top-header">

        <div>
            <h1 class="page-title">
               Celebrations
            </h1>

            <p class="page-subtitle">
                Upcoming birthdays & work anniversaries
            </p>
        </div>

        <!-- Tabs -->
        <div class="custom-tabs">

            <button class="tab-btn active"
                onclick="switchTab('birthday', this)">
                Birthday
            </button>

            <button class="tab-btn"
                onclick="switchTab('anniversary', this)">
                Anniversary
            </button>

        </div>

    </div>

    <!-- Birthday Tab -->
    <div id="tab-birthday" class="tab-content">

        @foreach ($employees as $employee)

            @php

                $birthday = \Carbon\Carbon::parse($employee->date_of_birth)
                    ->year(now()->year);

                if ($birthday->isPast()) {
                    $birthday->addYear();
                }

                $today = now()->startOfDay();

                $totalDaysLeft = $today->diffInDays($birthday->startOfDay());

            @endphp

            @if ($totalDaysLeft <= 9)
                <div class="log-card">

                    <div class="log-date">

                        <h2>
                            {{ $birthday->format('d') }}
                        </h2>

                        <span>
                            {{ strtoupper($birthday->format('M')) }}
                        </span>

                    </div>

                    <div class="log-info">

                        <h4>
                            {{ $employee->name }}
                        </h4>

                        <div class="log-meta">

                            <span class="meta-badge">
                                BIRTHDAY
                            </span>

                            <span class="meta-days">
                                {{ $totalDaysLeft }} days left
                            </span>


                        </div>

                    </div>

                    <div>
                        <span class="status-pill">
                            UPCOMING
                        </span>
                    </div>

                </div>
            @endif

        @endforeach

    </div>



    <!-- Anniversary Tab -->
    <div id="tab-anniversary" class="tab-content d-none">

        @foreach ($employees as $employee)

            @php

                $joiningDate = \Carbon\Carbon::parse($employee->date_of_joining);

                $anniversary = $joiningDate->copy()
                    ->year(now()->year);

                if ($anniversary->isPast()) {
                    $anniversary->addYear();
                }

                $totalDaysLeft = $today->diffInDays($anniversary->startOfDay());

                $yearsCompleted = $joiningDate->diffInYears($anniversary);

            @endphp

            @if ($totalDaysLeft <= 3)
                <div class="log-card">

                    <div class="log-date">

                        <h2>
                            {{ $anniversary->format('d') }}
                        </h2>

                        <span>
                            {{ strtoupper($anniversary->format('M')) }}
                        </span>

                    </div>

                    <div class="log-info">

                        <h4>
                            {{ $employee->name }}
                        </h4>

                        <div class="log-meta">

                            <span class="meta-badge anniversary">
                                {{ $yearsCompleted }} YEARS
                            </span>

                            <span class="meta-days">
                                {{ $totalDaysLeft }} days left
                            </span>

                        </div>

                    </div>

                    <div>
                        <span class="status-pill">
                            UPCOMING
                        </span>
                    </div>

                </div>
            @endif


        @endforeach

    </div>

</div>

<style>

    /* Wrapper */
    .employee-day-wrapper{
        padding: 32px 36px;
        background: #f8fafc;
        min-height: 100vh;
    }

    /* Header */
    .top-header{
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-title{
        font-size: 24px;
        font-weight: 700;
        color: #172b4d;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .page-subtitle{
        font-size: 14px;
        color: #7b8794;
        margin-bottom: 0;
        font-weight: 500;
    }

    /* Tabs */
    .custom-tabs{
        background: #f1f5f9;
        padding: 4px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .tab-btn{
        border: none;
        background: transparent;
        padding: 10px 26px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #94a3b8;
        transition: 0.3s;
    }

    .tab-btn.active{
        background: #fff;
        color: #172b4d;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .tab-content{
        width: 75%;
        margin: auto;
    }

    /* Cards */
    .log-card{
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 24px;
        padding: 28px 32px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    /* Date */
    .log-date{
        min-width: 78px;
        border-right: 1px solid #edf0f5;
        padding-right: 24px;
        text-align: center;
    }

    .log-date h2{
        font-size: 28px;
        line-height: 1;
        font-weight: 700;
        color: #172b4d;
        margin-bottom: 2px;
    }

    .log-date span{
        font-size: 13px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }

    /* Info */
    .log-info{
        flex: 1;
    }

    .log-info h4{
        font-size: 16px;
        font-weight: 700;
        color: #172b4d;
        margin-bottom: 8px;
    }

    /* Meta */
    .log-meta{
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .meta-badge{
        background: #f1f5f9;
        color: #8b9bb4;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .meta-badge.anniversary{
        background: #ecfdf3;
        color: #16a34a;
    }

    .meta-days{
        color: #64748b;
        font-weight: 700;
        font-size: 13px;
    }

    .meta-time{
        color: #64748b;
        font-size: 13px;
    }

    /* Status */
    .status-pill{
        background: #f1f5f9;
        color: #64748b;
        padding: 9px 18px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .3px;
    }

/* Mobile */
@media(max-width:768px){

    .employee-day-wrapper{
        padding: 20px;
    }

    .log-card{
        flex-direction: column;
        align-items: flex-start;
        padding: 22px;
    }

    .log-date{
        border-right: none;
        border-bottom: 1px solid #edf0f5;
        padding-right: 0;
        padding-bottom: 14px;
        width: 100%;
        text-align: left;
    }

}

</style>

<script>

    function switchTab(tabId, el){

        document.querySelectorAll('.tab-btn')
            .forEach(btn => btn.classList.remove('active'));

        el.classList.add('active');

        document.querySelectorAll('.tab-content')
            .forEach(tab => tab.classList.add('d-none'));

        document.getElementById('tab-' + tabId)
            .classList.remove('d-none');
    }

</script>

@endsection