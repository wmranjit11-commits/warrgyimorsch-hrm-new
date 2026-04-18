@extends('layouts.app')

@section('content')
<style>
    .inventory-container {
        background: #f8fafc;
        min-height: 100vh;
        padding: 30px;
    }
    .inventory-card {
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .inventory-header {
        padding: 40px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
    }
    .premium-search {
        background: #f1f5f9;
        border: 2px solid transparent;
        border-radius: 18px;
        padding: 18px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s;
    }
    .premium-search:focus-within {
        background: #fff;
        border-color: #3858f9;
        box-shadow: 0 10px 30px rgba(56, 88, 249, 0.08);
    }
    .premium-search input {
        border: none;
        outline: none;
        background: transparent;
        width: 100%;
        font-weight: 700;
        color: #1e293b;
        font-size: 16px;
    }
    .premium-table thead th {
        background: #f8fafc;
        border: none;
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 20px 40px;
    }
    .premium-table tbody td {
        padding: 30px 40px;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .type-pill {
        background: #f1f5f9;
        padding: 10px 20px;
        border-radius: 14px;
        font-weight: 700;
        color: #1e293b;
        display: inline-block;
    }
    .value-box {
        text-align: center;
    }
    .value-box .label {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        display: block;
        margin-bottom: 5px;
    }
    .value-box .number {
        font-size: 18px;
        font-weight: 700;
        color: #334155;
    }
    .available-badge {
        background: rgba(34, 197, 94, 0.08);
        color: #16a34a;
        font-weight: 800;
        padding: 12px 24px;
        border-radius: 16px;
        font-size: 20px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.1);
    }
    .export-btn {
        background: #3858f9 !important;
        color: #fff !important;
        border-radius: 18px !important;
        padding: 16px 35px !important;
        font-weight: 700 !important;
        transition: all 0.3s !important;
        border: none !important;
        box-shadow: 0 10px 25px rgba(56, 88, 249, 0.3) !important;
    }
    .export-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(56, 88, 249, 0.45) !important;
    }
</style>

<div class="inventory-container">
    <div class="d-flex justify-content-between align-items-center mb-5 mt-2">
        <div>
            <h2 class="fw-bolder text-dark mb-1" style="font-size: 32px;">Leave Balance</h2>
            <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: 1.5px;">Detailed inventory of your leave accounts</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('profile.show') }}" class="btn btn-light rounded-circle shadow-sm" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <i class="feather-user"></i>
            </a>
        </div>
    </div>
    
    <div class="inventory-card">
        <div class="inventory-header">
            <div class="premium-search">
                <i class="feather-search text-primary" style="font-size: 20px;"></i>
                <input type="text" id="inventorySearch" placeholder="Search leave categories (e.g. Sick, Casual)..." onkeyup="filterInventory()">
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table premium-table mb-0" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>LEAVE CATEGORY</th>
                            <th class="text-center">ALLOCATED CAPACITY</th>
                            <th class="text-center">UTILIZED</th>
                            <th class="text-center">REMAINING BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balances as $b)
                        <tr class="align-middle">
                            <td>
                                <div class="type-pill">
                                    <i class="feather-tag me-2 opacity-50"></i>
                                    {{ strtoupper($b['type']) }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="value-box">
                                    <span class="label text-uppercase">Total Days</span>
                                    <span class="number">{{ number_format($b['allotted'], 1) }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="value-box">
                                    <span class="label text-uppercase text-danger">Days Used</span>
                                    <span class="number text-danger">{{ number_format($b['used'], 1) }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="available-badge">
                                    {{ number_format($b['available'], 1) }}
                                    <span class="fs-11 fw-bold opacity-50 d-block" style="margin-top: -5px;">DAYS</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(in_array(strtolower(auth()->user()->role), ['admin', 'super admin']))
        <div class="p-5 bg-light bg-opacity-30 d-flex justify-content-center border-top">
            <a href="{{ route('leave.balance.export') }}" class="btn export-btn d-flex align-items-center gap-3">
                <i class="feather-file-text fs-5"></i>
                GENERATE INVENTORY REPORT
            </a>
        </div>
        @endif
    </div>
</div>

<script>
    function filterInventory() {
        const input = document.getElementById('inventorySearch');
        const filter = input.value.toLowerCase();
        const rows = document.querySelector('#inventoryTable tbody').getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            const text = rows[i].getElementsByTagName('td')[0].innerText.toLowerCase();
            rows[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
        }
    }
</script>
@endsection

