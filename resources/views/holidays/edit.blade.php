@extends('layouts.app')

@section('content')

<div class="main-content">
<div class="container-fluid">

    <div class="card shadow-sm custom-card">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-3"
            style="border-radius: 12px 12px 0 0;">
            <a href="{{ route('holidays.index') }}" class="btn btn-sm btn-light-brand text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <h6 class="fw-bold mb-0" style="color: #334155;">Edit Holiday</h6>
        </div>

        <div class="card-body p-4">

            <form method="POST" action="{{ route('holidays.update', $holiday->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Holiday Name</label>
                        <input type="text" name="title"
                            class="form-control border-0 bg-light px-3 fw-bold shadow-sm uppercase"
                            value="{{ strtoupper($holiday->title) }}" required style="height: 38px; font-size: 13px; border-radius: 8px;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Date</label>
                        <input type="date" name="date"
                            class="form-control border-0 bg-light px-3 fw-bold shadow-sm"
                            style="cursor: pointer; height: 38px; font-size: 13px; border-radius: 8px;" onclick="this.showPicker()"
                            value="{{ $holiday->date }}" required>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 8px; background: #3858f9; border: none; height: 38px;">
                        UPDATE HOLIDAY
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
</div>

<!-- UPPERCASE SCRIPT -->
<script>
document.querySelectorAll('.uppercase').forEach(input => {
    input.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });
});
</script>

@endsection