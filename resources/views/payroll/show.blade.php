<div class="p-4 bg-white" style="font-family: 'Inter', sans-serif;">
    <!-- Header Summary -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <div class="text-uppercase text-muted small fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Employee Name</div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-circle text-primary"></i> {{ $payroll->employee->name }}
            </h5>
        </div>
        <div class="text-end">
            <div class="text-uppercase text-muted small fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Payroll Month</div>
            <span class="badge px-3 py-2 fs-6 fw-bold shadow-sm" style="border-radius: 8px; background: #3858f9;">
                <i class="bi bi-calendar3 me-1"></i> {{ $payroll->month }}
            </span>
        </div>
    </div>

    <!-- Value Cards Grid -->
    <div class="row g-3 mb-4">
        <!-- Payable Days -->
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 border h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Payable Days</div>
                    <i class="bi bi-calendar-check text-muted fs-6"></i>
                </div>
                <div class="fs-4 fw-bold mb-0 text-dark">{{ $payroll->payable_days }}</div>
                <div class="text-muted small">Out of {{ \Carbon\Carbon::parse($payroll->month)->daysInMonth }} days</div>
            </div>
        </div>

        <!-- Salary Loss -->
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 border h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Salary Loss</div>
                    <i class="bi bi-dash-circle text-danger fs-6"></i>
                </div>
                <div class="fs-4 fw-bold mb-0 text-danger">Rs. {{ number_format($payroll->deductions, 2) }}</div>
                <div class="text-muted small">Due to unpaid leaves</div>
            </div>
        </div>

        <!-- Total Net Salary -->
        <div class="col-md-4">
            <div class="p-3 rounded-3 text-white shadow-sm h-100" style="background: linear-gradient(135deg, #3858f9 0%, #1e3a8a 100%);">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="text-white opacity-75 small fw-bold text-uppercase" style="font-size: 10px;">Net Payable</div>
                    <i class="bi bi-wallet2 fs-6"></i>
                </div>
                <div class="fs-4 fw-bold mb-0">Rs. {{ number_format($payroll->net_salary, 2) }}</div>
                <div class="text-white opacity-75 small mt-1">Final Take Home</div>
            </div>
        </div>
    </div>

    <!-- Additional Breakdown -->
    <div class="p-4 rounded-4 border bg-white shadow-sm">
        <h6 class="text-uppercase fw-bold text-dark mb-4 d-flex align-items-center gap-2" style="font-size: 12px; letter-spacing: 0.5px;">
            <i class="bi bi-bar-chart-steps text-primary"></i> Earnings Summary
        </h6>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="text-muted mb-1 small">Basic Salary (Monthly)</div>
                <div class="fw-bold fs-5 text-dark">Rs. {{ number_format($payroll->employee->basic_salary, 2) }}</div>
            </div>
            <div class="col-md-6 border-start">
                <div class="text-muted mb-1 small">Basic Salary (Pro-rated)</div>
                <div class="fw-bold fs-5 text-primary">Rs. {{ number_format($payroll->basic_salary, 2) }}</div>
            </div>
        </div>
        <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
            <i class="bi bi-info-circle me-1"></i> As per company policy, payroll is calculated strictly on Basic Salary. No other allowances or statutory deductions are applicable.
        </div>
    </div>
</div>

<style>
    .text-primary { color: #3858f9 !important; }
</style>
>
</style>