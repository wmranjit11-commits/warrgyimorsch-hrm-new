@extends('layouts.app')

@section('content')
    <div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <!-- Standardized Header -->
        <div class="px-4 pt-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-light-brand text-primary fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;">
                            <i class="bi bi-arrow-left fs-5"></i>
                        </a>
                        <div>
                            <h5 class="fw-bold mb-0" style="color: #334155;">Payroll Module</h5>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="#"
                                            class="text-decoration-none text-muted small">Home</a></li>
                                    <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;"
                                        aria-current="page">Calculation</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4">
            <!-- Calculation Filter Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white;">
                <div class="card-header bg-white border-0 px-4 py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">Calculation Settings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Employee Name</label>
                            <select id="employeeSelect" class="form-select border-0 bg-light px-3 shadow-none fw-bold"
                                style="border-radius: 8px; height: 38px; font-size: 13px;">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;">Month</label>
                            <input type="month" id="monthSelect"
                                class="form-control border-0 bg-light px-3 shadow-none fw-bold"
                                value="{{ date('Y-m') }}" max="{{ date('Y-m') }}" style="border-radius: 8px; height: 38px; font-size: 13px;">
                        </div>
                        <div class="col-md-2">
                            <button
                                class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                onclick="calculatePayroll()"
                                style="background: #3858f9; border: none; height: 38px; border-radius: 8px; font-size: 13px;">
                                <i class="bi bi-calculator"></i> CALCULATE
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calculation Result -->
            <div id="calculationResult" style="display: none;">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm mb-5"
                            style="border-radius: 16px; background: white; overflow: hidden; border: 1px solid #e2e8f0 !important;">
                            <div class="card-header bg-white border-bottom px-4 py-3 text-center">
                                <h6 class="fw-bold mb-0 text-dark text-uppercase letter-spacing-1"
                                    style="letter-spacing: 1px;">Payroll Summary</h6>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                <div class="row justify-content-center">
                                    <div class="col-md-11">
                                        <div class="bg-light p-4 rounded-4 border mb-4">
                                            <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                                                <span class="text-muted fw-bold">Full Monthly Gross</span>
                                                <span class="fw-bold text-dark fs-5" id="resMonthlyBasic"></span>
                                            </div>

                                            <!-- Earnings Section -->
                                            <div class="mb-4">
                                                <label class="small fw-bold text-primary text-uppercase mb-2"
                                                    style="font-size: 10px; letter-spacing: 0.5px;">Earnings
                                                    (Pro-rated)</label>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted small">Basic Salary</span>
                                                    <span class="fw-bold text-dark small" id="resPBasic">Rs. 0.00</span>
                                                </div>
                                                <div id="hraRow" class="d-flex justify-content-between mb-1"
                                                    style="display: none !important;">
                                                    <span class="text-muted small">HRA</span>
                                                    <span class="fw-bold text-dark small" id="resPHRA">Rs. 0.00</span>
                                                </div>
                                                <div id="convRow" class="d-flex justify-content-between mb-1"
                                                    style="display: none !important;">
                                                    <span class="text-muted small">Conveyance</span>
                                                    <span class="fw-bold text-dark small" id="resPConv">Rs. 0.00</span>
                                                </div>
                                                <div id="medRow" class="d-flex justify-content-between mb-1"
                                                    style="display: none !important;">
                                                    <span class="text-muted small">Medical</span>
                                                    <span class="fw-bold text-dark small" id="resPMed">Rs. 0.00</span>
                                                </div>
                                                <div id="otherRow" class="d-flex justify-content-between mb-1"
                                                    style="display: none !important;">
                                                    <span class="text-muted small">Other Allowance</span>
                                                    <span class="fw-bold text-dark small" id="resPOther">Rs. 0.00</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                                    <span class="fw-bold text-dark small">Gross Salary</span>
                                                    <span class="fw-bold text-dark small" id="resGross">Rs. 0.00</span>
                                                </div>
                                            </div>

                                            <!-- Deductions Section -->
                                            <div class="mb-4">
                                                <label class="small fw-bold text-danger text-uppercase mb-2"
                                                    style="font-size: 10px; letter-spacing: 0.5px;">Deductions</label>
                                                <div id="pfRow" class="d-flex justify-content-between mb-1"
                                                    style="display: none !important;">
                                                    <span class="text-muted small">PF (12% of Basic)</span>
                                                    <span class="fw-bold text-danger small" id="resPF">- Rs. 0.00</span>
                                                </div>
                                                <div id="lossRow" class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted small">Salary Loss (LWP)</span>
                                                    <span class="fw-bold text-danger small" id="resLoss">- Rs. 0.00</span>
                                                </div>
                                                <div id="esiRow" class="d-flex justify-content-between mb-1"
                                                    style="display: none !important;">
                                                    <span class="text-muted small">ESI (0.75% of Gross)</span>
                                                    <span class="fw-bold text-danger small" id="resESI">- Rs. 0.00</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                                    <span class="text-muted small">Total Deductions</span>
                                                    <span class="fw-bold text-danger small" id="resTotalDeductions">- Rs.
                                                        0.00</span>
                                                </div>
                                            </div>

                                            <!-- Summary Boxes Removed to reduce clutter as per user request -->

                                            <div class="pt-3 border-top mt-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 fw-bold text-dark">Net Payable</h4>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Payable Days:</span>
                                                            <input type="number" id="resPayableDays" class="form-control form-control-sm border-0 fw-bold text-primary text-center px-1" 
                                                                step="0.5" min="0" max="31" oninput="updateCalculations()"
                                                                style="width: 65px; height: 30px; background: #ebf0ff; border-radius: 6px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                                        </div>
                                                    </div>
                                                    <h3 class="mb-0 fw-bold text-primary" id="resNetPayable"
                                                        style="font-size: 2.2rem; letter-spacing: -1px;"></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-2 px-md-3">
                                    <button class="btn btn-primary py-3 fw-bold shadow-sm" id="submitPayrollBtn"
                                        style="background: #3858f9; border: none; border-radius: 12px; font-size: 1.1rem;"
                                        onclick="savePayroll()">
                                        <i class="bi bi-check2-circle me-2 fs-5"></i> CONFIRM & SAVE PAYROLL
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="noCalculation" class="text-center py-5">
                <div class="py-5" style="border: 2px dashed #e2e8f0; border-radius: 16px; background: #f8fafc;">
                    <i class="bi bi-calculator text-primary" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-dark mt-3 fw-bold fs-5 mb-1">Payroll Ready to Generate</p>
                    <p class="text-muted small">Select an employee and month above to start the calculation.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
        <div id="successToast" class="toast align-items-center border-0 shadow-lg" role="alert"
            style="border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); min-width: 320px;">
            <div class="d-flex">
                <div class="toast-body text-white fw-bold d-flex align-items-center gap-2 py-3 px-4">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <span id="toastMessage">Payroll saved successfully! Redirecting...</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto shadow-none"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        let currentPayrollData = null;
        let baseSalaryData = null; // Stores full month values for pro-rata recalculation

        function calculatePayroll() {
            const monthInput = document.getElementById('monthSelect').value;
            const employeeId = document.getElementById('employeeSelect').value;

            if (!monthInput || !employeeId) {
                alert('Please select both month and employee');
                return;
            }

            const noCalc = document.getElementById('noCalculation');
            noCalc.innerHTML = '<div class="py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 fw-bold text-primary">Calculating Attendance & Pro-rata...</p></div>';

            fetch('{{ route("payroll.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ month: monthInput, employee_id: employeeId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentPayrollData = data.payroll;
                        const p = data.payroll;
                        const d = data.details || {};
                        
                        // Extract actual days in selected month for accurate pro-rata calculation
                        const selDate = new Date(monthInput + '-01');
                        const daysInMonth = new Date(selDate.getFullYear(), selDate.getMonth() + 1, 0).getDate();
                        
                        // Use elapsed days so far for ongoing months
                        const rangeDays = d.total_days_in_range || daysInMonth;

                        baseSalaryData = {
                            monthly_salary: parseFloat(p.monthly_salary),
                            hra: parseFloat(p.full_hra || 0),
                            conv: parseFloat(p.full_conveyance || 0),
                            med: parseFloat(p.full_medical || 0),
                            other: parseFloat(p.full_other || 0),
                            days_divisor: daysInMonth,
                            range_days: rangeDays 
                        };

                        // Initialize Manual Input with calculated value
                        const payableDaysInput = document.getElementById('resPayableDays');
                        payableDaysInput.value = p.payable_days || 0;
                        
                        // Update UI Header to show we are calculating for a partial period
                        const label = document.getElementById('resMonthlyBasic').previousElementSibling;
                        if (rangeDays < daysInMonth) {
                            label.innerText = "Full Gross (Target Period)";
                        } else {
                            label.innerText = "Full Monthly Gross";
                        }
                        
                        // First render
                        renderPayrollUI();

                        noCalc.style.display = 'none';
                        document.getElementById('calculationResult').style.display = 'block';

                        const btn = document.getElementById('submitPayrollBtn');
                        btn.innerHTML = '<i class="bi bi-check2-circle me-2 fs-5"></i> CONFIRM & SAVE PAYROLL';
                        btn.disabled = false;
                    } else {
                        alert(data.message || 'Error occurred');
                        noCalc.innerHTML = '<div class="py-5"><i class="bi bi-exclamation-circle text-danger fs-1"></i><p class="mt-3 fw-bold text-danger">' + (data.message) + '</p></div>';
                    }
                })
                .catch(err => {
                    console.error('Payroll Error:', err);
                    alert('An error occurred during calculation. Please check the console for details.');
                    noCalc.innerHTML = '<div class="py-5"><i class="bi bi-exclamation-circle text-danger fs-1"></i><p class="mt-3 fw-bold text-danger">Calculation Failed. Please retry.</p></div>';
                });
        }

        // Live Recalculation Core Logic
        function updateCalculations() {
            if (!baseSalaryData) return;

            const days = parseFloat(document.getElementById('resPayableDays').value) || 0;
            const div = baseSalaryData.days_divisor;

            // Pro-rate Base Earnings
            const proRatedBasic = (baseSalaryData.monthly_salary / div) * days;
            
            // Sync current data object
            currentPayrollData.payable_days = days;
            currentPayrollData.basic_salary = proRatedBasic.toFixed(2);
            
            // Recalculate Deductions (Standard rules)
            // PF = 12% of pro-rated basic (capped at 1800 often, but following current code)
            const pf = proRatedBasic * 0.12;
            currentPayrollData.pf_deduction = pf.toFixed(2);
            
            // ESI = 0.75% of pro-rated gross (approx for now, will calculate properly in render)
            // Actually, keep it simple: total deductions = PF + whatever else was there
            
            renderPayrollUI();
        }

        function renderPayrollUI() {
            const p = currentPayrollData;
            const b = baseSalaryData;
            const days = parseFloat(p.payable_days) || 0;
            const div = b.days_divisor;

            // Potential Earnings for the period (e.g. 2 days of April = ~1333)
            const potentialPeriodGross = (b.monthly_salary / div) * b.range_days;
            document.getElementById('resMonthlyBasic').textContent = 'Rs. ' + f(potentialPeriodGross);
            document.getElementById('resPBasic').textContent = 'Rs. ' + f(p.basic_salary);

            // Pro-rate other earnings on the fly
            const proHRA = (b.hra / div) * days;
            const proConv = (b.conv / div) * days;
            const proMed = (b.med / div) * days;
            const proOther = (b.other / div) * days;

            updateRow('hraRow', 'resPHRA', proHRA);
            updateRow('convRow', 'resPConv', proConv);
            updateRow('medRow', 'resPMed', proMed);
            updateRow('otherRow', 'resPOther', proOther);

            const actualGross = parseFloat(p.basic_salary) + proHRA + proConv + proMed + proOther;
            document.getElementById('resGross').textContent = 'Rs. ' + f(actualGross);
            p.gross_salary = actualGross.toFixed(2);

            // Re-sync Deductions
            let pf = 0;
            if (p.has_pf) pf = parseFloat(p.basic_salary) * 0.12;
            
            let esi = 0;
            if (p.has_esi) esi = actualGross * 0.0075;

            // Salary Loss is specifically the difference between what they COULD have earned so far vs what they DID
            const salaryLoss = potentialPeriodGross - actualGross;
            updateRow('lossRow', 'resLoss', salaryLoss, true);
            
            updateRow('pfRow', 'resPF', pf, true);
            updateRow('esiRow', 'resESI', esi, true);
            
            const totalDeductions = pf + esi + salaryLoss;
            document.getElementById('resTotalDeductions').textContent = '- Rs. ' + f(totalDeductions);
            
            p.pf_deduction = pf.toFixed(2);
            p.esi_deduction = esi.toFixed(2);
            p.salary_loss = salaryLoss.toFixed(2);
            p.deductions = totalDeductions.toFixed(2);

            // Final Net is Potential minus Deductions (which now makes intuitive sense)
            const net = potentialPeriodGross - totalDeductions;
            document.getElementById('resNetPayable').textContent = 'Rs. ' + f(net);
            p.net_salary = net.toFixed(2);
        }

        function updateRow(rowId, spanId, value, isDeduction = false) {
            const row = document.getElementById(rowId);
            const span = document.getElementById(spanId);
            const val = parseFloat(value) || 0;
            if (val > 0) {
                row.style.setProperty('display', 'flex', 'important');
                span.textContent = (isDeduction ? '- ' : '') + 'Rs. ' + f(val);
            } else {
                row.style.setProperty('display', 'none', 'important');
            }
        }


        function savePayroll() {
            if (!currentPayrollData) return;

            const btn = document.getElementById('submitPayrollBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
            btn.disabled = true;

            fetch('{{ route("payroll.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(currentPayrollData)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // 1. Show success toast
                        const toastEl = document.getElementById('successToast');
                        const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
                        toast.show();

                        // 2. Update button to show success
                        btn.innerHTML = '<i class="bi bi-check-all me-2 fs-5"></i> SAVED SUCCESSFULLY';
                        btn.className = 'btn btn-success py-3 fw-bold shadow-sm';
                        btn.style.background = '#10b981';
                        btn.style.border = 'none';
                        btn.style.borderRadius = '12px';

                        // 3. Redirect to payroll list
                        setTimeout(() => {
                            window.location.href = '{{ route("payroll.index") }}';
                        }, 1500);
                    } else {
                        alert(data.message);
                        btn.innerHTML = '<i class="bi bi-check2-circle me-2 fs-5"></i> SUBMIT PAYROLL';
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    alert('Something went wrong. Please try again.');
                    btn.innerHTML = '<i class="bi bi-check2-circle me-2 fs-5"></i> SUBMIT PAYROLL';
                    btn.disabled = false;
                });
        }

        function f(n) { return parseFloat(n).toLocaleString('en-IN', { minimumFractionDigits: 2 }); }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">";
            color: #94a3b8;
        }

        .display-6 {
            font-size: 2.25rem;
        }
    </style>
@endsection