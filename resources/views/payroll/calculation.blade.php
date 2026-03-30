@extends('layouts.app')

@section('content')
<div class="container-fluid px-0" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white border-bottom shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <h5 class="fw-bold mb-0 me-3" style="color: #334155;">Payroll Module</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted small">Home</a></li>
                    <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;" aria-current="page">Payroll Calculation</li>
                </ol>
            </nav>
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
                        <label class="form-label small fw-bold text-muted mb-2">Employee Name</label>
                        <select id="employeeSelect" class="form-select border-0 bg-light py-2 px-3 shadow-none fw-bold" style="border-radius: 8px; height: 45px;">
                            <option value="">Select Employee</option>
                            @foreach (\App\Models\Employee::all() as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted mb-2">Month</label>
                        <input type="month" id="monthSelect" class="form-control border-0 bg-light py-2 px-3 shadow-none fw-bold" 
                               value="{{ date('Y-m') }}" style="border-radius: 8px; height: 45px;">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" onclick="calculatePayroll()" style="background: #3858f9; border: none; height: 45px; border-radius: 8px;">
                            <i class="bi bi-calculator"></i> CALCULATE
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculation Result -->
        <div id="calculationResult" style="display: none;">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white; overflow: hidden;">
                        <div class="card-header bg-white border-bottom px-4 py-3">
                            <h6 class="fw-bold mb-0 text-dark">Salary Components</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="small fw-bold text-primary text-uppercase mb-3" style="letter-spacing: 0.5px;">Earnings</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0">
                                            <tbody class="text-dark fw-bold">
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">Basic Salary</td>
                                                    <td class="text-end py-2" id="tableBasicSalary">₹ 0.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">HRA</td>
                                                    <td class="text-end py-2" id="tableHRA">₹ 0.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">Conveyance</td>
                                                    <td class="text-end py-2" id="tableConveyance">₹ 0.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">Medical</td>
                                                    <td class="text-end py-2" id="tableMedical">₹ 0.00</td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td class="ps-0 py-3">Gross Salary</td>
                                                    <td class="text-end py-3 text-dark fs-5" id="tableGrossSalary">₹ 0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="small fw-bold text-danger text-uppercase mb-3" style="letter-spacing: 0.5px;">Deductions</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0">
                                            <tbody class="text-dark fw-bold">
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">PF (12%)</td>
                                                    <td class="text-end py-2" id="tablePFDeduction">₹ 0.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">ESI (1.75%)</td>
                                                    <td class="text-end py-2" id="tableESIDeduction">₹ 0.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0 py-2 text-muted fw-normal">Other</td>
                                                    <td class="text-end py-2" id="tableOtherDeduction">₹ 0.00</td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td class="ps-0 py-3 text-danger">Total Deductions</td>
                                                    <td class="text-end py-3 text-danger fs-5" id="tableTotalDeductions">₹ 0.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: white;">
                        <div class="card-header bg-white border-bottom px-4 py-3">
                            <h6 class="fw-bold mb-0 text-dark">Net Salary</h6>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="p-4 rounded-4 mb-4" style="background: linear-gradient(135deg, #3858f9 0%, #1e3a8a 100%);">
                                <div class="text-white opacity-75 small mb-1">Take Home Pay</div>
                                <div class="display-6 fw-bold text-white" id="tableNetSalary">₹ 0.00</div>
                            </div>
                            
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <div class="text-muted small mb-1">Payable Days</div>
                                        <div class="fw-bold fs-5" id="resultPayableDays">0</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <div class="text-muted small mb-1">Month</div>
                                        <div class="fw-bold fs-6" id="resultMonth">--</div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="background: #3858f9; border: none; border-radius: 12px;" onclick="savePayroll()">
                                <i class="bi bi-check2-circle me-2 fs-5"></i> SUBMIT PAYROLL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="noCalculation" class="text-center py-5">
            <div class="py-5">
                <i class="bi bi-calculator text-muted" style="font-size: 4rem; opacity: 0.1;"></i>
                <p class="text-muted mt-3 fw-bold fs-5">Calculate Salary Report</p>
                <p class="text-muted small">Select an employee and period to generate the payroll statement.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPayrollData = null;

    function calculatePayroll() {
        const month = document.getElementById('monthSelect').value;
        const employeeId = document.getElementById('employeeSelect').value;

        if (!month || !employeeId) {
            alert('Please select both month and employee');
            return;
        }

        const noCalc = document.getElementById('noCalculation');
        noCalc.innerHTML = '<div class="py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 fw-bold text-primary">Calculating...</p></div>';

        fetch('{{ route("payroll.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ month, employee_id: employeeId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentPayrollData = data.payroll;
                displayPayrollData(data.payroll);
                noCalc.style.display = 'none';
                document.getElementById('calculationResult').style.display = 'block';
            } else {
                alert(data.message || 'Error occurred');
                noCalc.innerHTML = '<div class="py-5"><i class="bi bi-exclamation-circle text-danger fs-1"></i><p class="mt-3 fw-bold text-danger">'+(data.message)+'</p></div>';
            }
        });
    }

    function displayPayrollData(p) {
        document.getElementById('resultMonth').textContent = p.month;
        document.getElementById('resultPayableDays').textContent = p.payable_days;
        document.getElementById('tableBasicSalary').textContent = '₹ ' + f(p.basic_salary);
        document.getElementById('tableHRA').textContent = '₹ ' + f(p.hra);
        document.getElementById('tableConveyance').textContent = '₹ ' + f(p.conveyance_allowance);
        document.getElementById('tableMedical').textContent = '₹ ' + f(p.medical_allowance);
        document.getElementById('tableGrossSalary').textContent = '₹ ' + f(p.gross_salary);
        document.getElementById('tablePFDeduction').textContent = '₹ ' + f(p.pf_deduction);
        document.getElementById('tableESIDeduction').textContent = '₹ ' + f(p.esi_deduction);
        document.getElementById('tableOtherDeduction').textContent = '₹ 0.00';
        document.getElementById('tableTotalDeductions').textContent = '₹ ' + f(p.deductions);
        document.getElementById('tableNetSalary').textContent = '₹ ' + f(p.net_salary);
    }

    function savePayroll() {
        if (!currentPayrollData) return;
        fetch('{{ route("payroll.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(currentPayrollData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = "{{ route('payroll.index') }}?success=Payroll+Submitted";
            } else {
                alert(data.message);
            }
        });
    }

    function f(n) { return parseFloat(n).toLocaleString('en-IN', { minimumFractionDigits: 2 }); }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    .breadcrumb-item + .breadcrumb-item::before { content: ">"; color: #94a3b8; }
    .display-6 { font-size: 2.25rem; }
</style>
@endsection