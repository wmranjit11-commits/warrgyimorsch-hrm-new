@extends('layouts.app')

@section('content')
<style>
    .clean-portfolio {
        background: #fdfdfe;
        min-height: 100vh;
        padding-bottom: 60px;
    }
    .portfolio-header-v2 {
        background: #fff;
        padding: 40px 0;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 40px;
    }
    .toggle-switch {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        width: fit-content;
    }
    .toggle-opt {
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: #94a3b8;
    }
    .toggle-opt.active {
        background: #fff;
        color: #1e293b;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .archive-list {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .minimal-row {
        background: #fff;
        border-radius: 20px;
        padding: 25px 30px;
        border: 1px solid #f1f5f9;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s;
    }
    .minimal-row:hover {
        border-color: #3858f9;
        background: #fafbff;
    }

    .date-column {
        min-width: 80px;
        text-align: center;
        border-right: 2px solid #f1f5f9;
        padding-right: 25px;
        margin-right: 25px;
    }
    .day-num {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        display: block;
        line-height: 1;
    }
    .month-text {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
    }

    .info-column {
        flex-grow: 1;
    }
    .type-title {
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .meta-tags {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .tag-sm {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .status-area {
        text-align: right;
        min-width: 120px;
    }
    .status-dot-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 6px 16px;
        border-radius: 100px;
        display: inline-block;
    }

    .status-approved { background: #ecfdf5; color: #059669; }
    .status-pending { background: #fef9c3; color: #a16207; }
    .status-rejected { background: #fff1f2; color: #e11d48; }

    .spotlight-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
</style>

<div class="clean-portfolio">
    <div class="portfolio-header-v2">
        <div class="container" style="max-width: 1000px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">My Logs</h2>
                    <p class="text-muted small mb-0 fw-medium">History of your leave applications</p>
                </div>
                
                <div class="toggle-switch">
                    <button class="toggle-opt active" onclick="switchTab('recent', this)">Recent</button>
                    <button class="toggle-opt" onclick="switchTab('archive', this)">Archive</button>
                </div>
            </div>
        </div>
    </div>

    <div class="archive-list">
        <!-- RECENT SPOTLIGHT -->
        <div id="tab-recent" class="content-pane">
            @php $latest = $leaves->first(); @endphp
            @if($latest)
                <div class="spotlight-card">
                    <span class="text-primary small fw-800 text-uppercase letter-spacing-1 mb-3 d-block">Latest Recording</span>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-800 text-dark mb-1" style="font-size: 28px;">{{ $latest->leave_type }}</h3>
                            <div class="meta-tags mb-4">
                                <span class="tag-sm">{{ $latest->leave_category }}</span>
                                <span class="text-muted small fw-bold">|</span>
                                <span class="text-muted small fw-bold">{{ $latest->total_days }} Total Days</span>
                            </div>
                            
                            <div class="d-flex align-items-center gap-3 text-dark fw-bold">
                                <i class="feather-calendar text-primary"></i>
                                <span>{{ $latest->start_date->format('d M, Y') }} — {{ $latest->end_date ? $latest->end_date->format('d M, Y') : 'Same Day' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="status-dot-label {{ 'status-'.strtolower($latest->status) }}">
                                {{ strtoupper($latest->status) }}
                            </div>
                            <p class="text-muted small mt-3 mb-0" style="max-width: 200px;">"{{ $latest->reason }}"</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- FULL LIST -->
        <div id="tab-archive" class="content-pane d-none">
            @foreach($leaves as $item)
                <div class="minimal-row">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="date-column">
                            <span class="day-num">{{ $item->start_date->format('d') }}</span>
                            <span class="month-text">{{ $item->start_date->format('M') }}</span>
                        </div>
                        <div class="info-column">
                            <h5 class="type-title">{{ $item->leave_type }}</h5>
                            <div class="meta-tags">
                                <span class="tag-sm">{{ $item->leave_category }}</span>
                                <span class="text-muted small fw-bold">{{ $item->total_days }}D</span>
                                <span class="text-muted small fw-medium">at {{ $item->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="status-area">
                        <div class="status-dot-label {{ 'status-'.strtolower($item->status) }}">
                            {{ strtoupper($item->status) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function switchTab(tabId, el) {
        document.querySelectorAll('.toggle-opt').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.content-pane').forEach(p => p.classList.add('d-none'));
        document.getElementById('tab-' + tabId).classList.remove('d-none');
    }
</script>
@endsection
