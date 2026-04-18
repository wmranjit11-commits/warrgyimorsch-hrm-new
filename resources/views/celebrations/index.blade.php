@extends('layouts.app')

@section('title', 'Staff Celebrations')

@section('content')
<div class="main-content" style="background: #fafbff; min-height: 100vh;">
    <div class="container-fluid p-4 mt-3">
        <!-- Dashboard Header -->
        <div class="row mb-5 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                        <i class="feather-gift fs-4"></i>
                    </div>
                    <div>
                        <h2 class="fw-black text-dark mb-0 fs-28 ls-n1">Celebration Hub</h2>
                        <p class="text-muted mb-0 fw-medium">Celebrating team success and personal milestones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="d-inline-flex bg-white p-2 rounded-pill shadow-sm border px-4">
                    <span class="text-muted fw-bold fs-11 text-uppercase ls-1">Next 30 Days: <span class="text-primary">{{ $celebrations->count() }} Events</span></span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($celebrations as $item)
                @php 
                    $emp = $item['employee'];
                    $isToday = $item['date']->isToday();
                    $isB = $item['type'] == 'Birthday';
                    $themeColor = $isB ? '#6366f1' : '#10b981';
                    $bgSoft = $isB ? 'rgba(99, 102, 241, 0.05)' : 'rgba(16, 185, 129, 0.05)';
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="celebration-card position-relative {{ $isToday ? 'is-today' : '' }}">
                        <!-- Decorative floating elements for Today -->
                        @if($isToday)
                            <div class="confetti-decor">🎉</div>
                            <div class="confetti-decor-right">🥳</div>
                        @endif

                        <div class="card border-0 shadow-soft h-100 overflow-hidden" style="border-radius: 24px; transition: all 0.3s ease;">
                            <div class="card-body p-4 text-center">
                                <!-- Type Badge -->
                                <div class="mb-4">
                                    <span class="badge rounded-pill fw-black px-3 py-2 fs-10 text-uppercase ls-1" 
                                          style="background: {{ $bgSoft }}; color: {{ $themeColor }}; border: 1px solid {{ $isB ? 'rgba(99,102,241,0.1)' : 'rgba(16,185,129,0.1)' }};">
                                        <i class="{{ $item['icon'] }} me-1"></i> {{ $item['type'] }}
                                    </span>
                                </div>

                                <!-- Avatar with custom logic -->
                                <div class="avatar-perspective mb-4">
                                    <div class="avatar-ring" style="background: conic-gradient({{ $themeColor }}, transparent);"></div>
                                    <div class="main-avatar mx-auto position-relative">
                                        @if($emp->photo)
                                            <img src="{{ asset('storage/' . $emp->photo) }}" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm" alt="">
                                        @else
                                            <div class="avatar-initial rounded-circle w-100 h-100 d-flex align-items-center justify-content-center fw-black text-white shadow-sm" 
                                                 style="background: linear-gradient(135deg, {{ $themeColor }}, {{ $isB ? '#818cf8' : '#34d399' }}); font-size: 32px;">
                                                {{ substr($emp->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <!-- Special Floating Icon -->
                                        <div class="special-icon-float shadow-sm bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; position: absolute; bottom: 5px; right: 5px;">
                                            <span class="fs-14">{{ $isB ? '🎂' : '🏆' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employee Info -->
                                <h5 class="fw-black text-dark mb-1 fs-18 ls-n1">{{ $emp->name }}</h5>
                                <p class="text-muted fs-12 fw-bold mb-4 text-uppercase ls-1 opacity-75">{{ $emp->designation ?? 'Team Member' }}</p>

                                <!-- Date & Detail -->
                                <div class="bg-light-soft rounded-4 p-3 mb-4" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-start">
                                            <div class="fs-10 text-muted fw-bold text-uppercase ls-1">EVENT DATE</div>
                                            <div class="fw-black text-dark fs-14">{{ $item['original_date']->format('d F') }}</div>
                                        </div>
                                        <div class="text-end">
                                            @if($isB)
                                                <div class="fs-10 text-muted fw-bold text-uppercase ls-1">TURNING</div>
                                                <div class="fw-black text-primary fs-14">{{ $item['date']->year - $item['original_date']->year }}</div>
                                            @else
                                                <div class="fs-10 text-muted fw-bold text-uppercase ls-1">SERVICE</div>
                                                <div class="fw-black text-success fs-14">{{ $item['years'] }}y</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- CTA / Countdown -->
                                <div class="mt-2">
                                    @if($isToday)
                                        <div class="celebrate-btn shadow-sm rounded-pill py-2 px-4 d-inline-block text-white fw-black fs-12" 
                                             style="background: linear-gradient(to right, {{ $themeColor }}, {{ $isB ? '#818cf8' : '#34d399' }}); cursor: default;">
                                            CELEBRATE TODAY! 🎊
                                        </div>
                                    @else
                                        <div class="countdown-text fs-11 fw-bold text-muted text-uppercase ls-1">
                                            In <span class="text-dark fw-black">{{ $today->diffInDays($item['date']) }} Days</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-10">
                    <div class="bg-white p-5 rounded-5 shadow-sm d-inline-block border">
                        <i class="feather-calendar fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="fw-black text-dark mb-2">Peaceful Times</h4>
                        <p class="text-muted mb-0">No celebrations scheduled for the next 30 days.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap');
    
    .main-content { font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .fw-black { font-weight: 800; }
    .ls-n1 { letter-spacing: -1px; }
    .ls-1 { letter-spacing: 1px; }
    .fs-28 { font-size: 28px; }

    .shadow-soft { box-shadow: 0 10px 30px rgba(0,0,0,0.02); }

    .celebration-card { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }
    .celebration-card:hover { transform: translateY(-10px); }
    .celebration-card:hover .card { box-shadow: 0 40px 80px rgba(0,0,0,0.08) !important; border: 1px solid rgba(0,0,0,0.05); }

    .avatar-perspective { perspective: 1000px; display: inline-block; }
    .main-avatar { width: 100px; height: 100px; z-index: 2; position: relative; }
    
    .avatar-ring {
        position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px;
        border-radius: 50%; opacity: 0.1; z-index: 1;
        animation: rotateRing 10s linear infinite;
    }
    @keyframes rotateRing { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .is-today .card { border: 2px solid #6366f1 !important; background: linear-gradient(135deg, #fff 0%, #f9faff 100%); }
    .confetti-decor { position: absolute; top: -10px; left: -10px; font-size: 30px; z-index: 10; animation: float 3s ease-in-out infinite; }
    .confetti-decor-right { position: absolute; top: -10px; right: -10px; font-size: 30px; z-index: 10; animation: float 3s ease-in-out infinite 1.5s; }

    @keyframes float { 0% { transform: translateY(0); } 50% { transform: translateY(-10px) rotate(5deg); } 100% { transform: translateY(0); } }

    .bg-light-soft { background: #f8fafc; }
    .py-10 { padding-top: 5rem; padding-bottom: 5rem; }
</style>
@endsection
