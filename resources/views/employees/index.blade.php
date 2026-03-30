@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <!-- Main Content Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: white;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                <div>
                    <h5 class="fw-bold mb-0" style="color: #334155;">Employee Management</h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted small">Home</a></li>
                            <li class="breadcrumb-item active small fw-bold" style="color: #3858f9;" aria-current="page">List</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Right Aligned Search & Actions -->
                    <div class="input-group d-none d-md-flex" style="width: 250px;">
                        <span class="input-group-text bg-light border-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control bg-light border-0 shadow-none" placeholder="Search..." onkeyup="applyFilters()">
                    </div>
                    
                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-primary text-primary" data-bs-toggle="collapse" data-bs-target="#filterSection" title="Filter Records">
                        <i class="feather-filter"></i>
                    </a>

                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-info text-info" onclick="location.reload()" title="Refresh">
                        <i class="feather-refresh-cw"></i>
                    </a>

                    <a href="{{ route('employees.create') }}" class="avatar-text avatar-md bg-primary text-white" title="Add Employee">
                        <i class="feather-plus"></i>
                    </a>

                    @if(isset($employees) && $employees->count() > 0)
                    <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger" id="deleteSelectedBtn" onclick="deleteSelectedEmployees()" title="Delete Selected">
                        <i class="feather-trash-2"></i>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Collapsible Filter Section -->
            <div class="collapse" id="filterSection">
                <div class="card-body border-bottom bg-light bg-opacity-10 p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Employee Name / ID</label>
                            <input type="text" id="filterEmployeeName" class="form-control border-0 shadow-sm" placeholder="Search..." onkeyup="applyFilters()" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Employee Type</label>
                            <select id="filterEmployeeType" class="form-select border-0 shadow-sm" onchange="applyFilters()" style="border-radius: 8px;">
                                <option value="">All Types</option>
                                <option value="permanent">Employee</option>
                                <option value="contract">Worker</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Department</label>
                            <select id="filterDepartment" class="form-select border-0 shadow-sm" onchange="applyFilters()" style="border-radius: 8px;">
                                <option value="">All Departments</option>
                                <option value="administration">Administration</option>
                                <option value="business_development">Business Development</option>
                                <option value="hr">HR Department</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100 fw-bold shadow-sm" onclick="applyFilters()" style="background: #3858f9; border: none; height: 38px; border-radius: 8px;">
                                <i class="feather-check-circle me-1"></i> APPLY FILTERS
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- SHOW ENTRIES -->
                <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2">
                    <span class="text-muted small fw-bold text-uppercase">Show</span>
                    <select class="form-select d-inline-block py-1 px-2 border-0 bg-light" style="width: 80px; border-radius: 8px;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-muted small fw-bold text-uppercase">entries</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover" id="employeeTable">

                        <thead class="bg-light">
                            <tr style="height: 60px;">
                                <th style="width:70px; padding: 15px; text-align: center;"><input type="checkbox"
                                        id="selectAll"></th>
                                <th style="width:120px; padding: 15px; font-size: 14px; text-align: center;">SR. NO. <i
                                        class="ms-1"></i></th>
                                <th style="width:200px; padding: 15px; font-size: 14px; text-align: center;">NAME <i
                                        class=" ms-1"></i></th>
                                <th style="width:180px; padding: 15px; font-size: 14px; text-align: center;">ROLE <i
                                        class=" ms-1"></i></th>
                                <th style="width:200px; padding: 15px; font-size: 14px; text-align: center;">DEPARTMENT <i
                                        class="ms-1"></i></th>
                                <th style="width:140px; padding: 15px; font-size: 14px; text-align: center;">PHOTO</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($employees as $key => $emp)
                                <tr class="fade-row" id="emp-row-{{ $emp->id }}" style="height: 60px; vertical-align: middle;"
                                    data-employee-id="{{ $emp->id }}">

                                    <td style="padding: 12px; text-align: center;"><input type="checkbox" class="emp-checkbox"
                                            data-id="{{ $emp->id }}"></td>

                                    <td class="fw-bold" style="padding: 12px; font-size: 15px; text-align: center;">
                                        {{ $key + 1 }}</td>

                                    <td style="padding: 12px; text-align: center;" data-employee-id="{{ $emp->id }}">
                                        <div class="dropdown">
                                            <a href="javascript:void(0)"
                                                class="fw-bold d-flex align-items-center justify-content-center" role="button"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                style="color:#3b82f6; font-size: 15px;">
                                                <span style="font-size:15px;">{{ $emp->name }}</span>
                                                <span class="ms-2 text-muted"
                                                    style="font-size:12px; color:#3b82f6;">(EC{{ str_pad($emp->id, 4, '0', STR_PAD_LEFT) }})</span>
                                            </a>
                                            <ul class="dropdown-menu shadow-sm border-0 p-2"
                                                style="min-width:180px;left:0 !important;top:100% !important;max-height:none !important;overflow-y:visible !important;box-shadow:0 2px 8px rgba(0,0,0,0.07);border-radius:8px;">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                                        href="javascript:void(0)" onclick="viewEmployee({{ $emp->id }})"
                                                        style="color:#6366f1;font-weight:500;">
                                                        <i class="feather-eye" style="color:#6366f1;font-size:16px;"></i>
                                                        View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                                        href="{{ route('employees.edit', $emp->id) }}"
                                                        style="color:#22c55e;font-weight:500;">
                                                        <i class="feather-edit-3"
                                                            style="color:#22c55e;font-size:16px;"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                                        href="javascript:void(0)" onclick="deleteEmployee({{ $emp->id }})"
                                                        style="color:#ef4444;font-weight:500;">
                                                        <i class="feather-trash-2" style="color:#ef4444;font-size:16px;"></i>
                                                        Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td style="padding: 12px; font-size: 15px; text-align: center;">
                                        {{ ucfirst(str_replace('_', ' ', $emp->role)) }}</td>

                                    <td style="padding: 12px; font-size: 15px; text-align: center;">
                                        {{ ucfirst(str_replace('_', ' ', $emp->department)) }}</td>

                                    <td class="text-center" style="padding: 12px; text-align: center;">
                                        @if($emp->photo)
                                            <img src="/storage/{{ $emp->photo }}"
                                                style="width:50px;height:50px;border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;">
                                        @else
                                            <div
                                                style="width:50px;height:50px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;border:1px solid #e5e7eb;font-size:10px;color:#9ca3af;">
                                                N/A
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No employees found. <a
                                            href="{{ route('employees.create') }}">Add one</a></td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <!-- PAGINATION -->
                @if($employees->count())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }}
                            entries
                        </div>
                        <div>
                            {{ $employees->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>

    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- RIGHT SIDE MODAL FOR EMPLOYEE DETAILS -->
    <div class="offcanvas offcanvas-end custom-side-modal" tabindex="-1" id="employeeModal" aria-labelledby="employeeModalLabel">
        <div class="offcanvas-header p-3 p-md-4"
            style="background: #0f172a; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <div class="d-flex align-items-center gap-3">
                <div style="background: rgba(255,255,255,0.1); width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-person-circle text-white fs-4"></i>
                </div>
                <div>
                    <h5 class="offcanvas-title text-white fw-bold mb-0" id="employeeModalLabel">Employee Profile</h5>
                    <div style="font-size: 11px; color: rgba(255,255,255,0.85); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;">
                        System ID: <span id="employeeCodeDisplay" style="color: #818cf8; font-weight: 800;">-</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn btn-sm" id="editEmployeeBtn" onclick="editEmployee()" 
                    style="background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); font-weight: 600; padding: 6px 15px; border-radius: 8px;">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </button>
                <button type="button" class="btn btn-sm" id="deleteEmployeeBtn" onclick="deleteEmployee()"
                    style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 600; padding: 6px 15px; border-radius: 8px;">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="offcanvas" aria-label="Close" style="opacity: 0.8;"></button>
            </div>
        </div>
        <div class="offcanvas-body p-0" id="employeeDetails" style="background: #ffffff;">
            <div class="d-flex align-items-center justify-content-center h-100" style="min-height: 400px;">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Retrieving Records...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ANIMATION -->
    <style>
        .fade-row {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-out {
            opacity: 0 !important;
            transform: translateX(20px);
            transition: all 0.4s ease-out;
        }

        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 0.25rem rgba(239, 68, 68, 0.1) !important;
        }

        /* RESPONSIVE STYLES */
        .custom-side-modal {
            width: 600px !important;
            border-left: none;
            box-shadow: -10px 0 30px rgba(0,0,0,0.15);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (max-width: 991px) {
            .custom-side-modal {
                width: 100% !important;
            }
            .page-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 15px;
            }
            .page-header-right {
                width: 100%;
                margin-left: 0 !important;
            }
            .page-header-right-items {
                display: flex;
                width: 100%;
                gap: 10px;
            }
            .page-header-right-items .btn {
                flex: 1;
                font-size: 13px;
                padding: 10px;
            }
            .details-grid {
                grid-template-columns: 1fr !important;
            }
            .nav-tabs-custom {
                margin: 0 10px 20px 10px !important;
                flex-wrap: wrap;
            }
            .nav-tab {
                flex: 1 1 30%;
                font-size: 11px !important;
                padding: 10px 5px !important;
            }
        }

        @media (max-width: 576px) {
            .nav-tab {
                flex: 1 1 45%;
            }
            .salary-total {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
        }
            .table th, .table td {
                font-size: 13px !important;
                padding: 10px 8px !important;
                white-space: nowrap;
            }
            .offcanvas-header {
                padding: 15px !important;
            }
            #editEmployeeBtn, #deleteEmployeeBtn {
                padding: 5px 12px !important;
                font-size: 11px !important;
            }
        }

        table thead th {
            border-bottom: 2px solid #f0f0f0;
            color: #555;
            font-weight: 600;
            font-size: 13px;
        }

            {
            background-color: #f9f9f9;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8fafc !important;
        }

        table tbody tr:nth-child(odd) {
            background-color: #fff !important;
        }


        .employee-photo {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #333;
            font-size: 14px;
        }

        /* PREMIUM EMPLOYEE DETAILS STYLES */
        .employee-details-container {
            background: #ffffff;
            position: relative;
            min-height: 100%;
        }

        .employee-premium-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            padding: 30px 25px;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            margin-bottom: 25px;
        }

        .header-photo-wrapper img,
        .header-photo-wrapper .employee-photo-premium {
            width: 100px;
            height: 100px;
            border-radius: 24px;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        .nav-tabs-custom {
            display: flex;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 14px;
            margin: 0 20px 25px 20px;
            gap: 4px;
        }

        .nav-tab {
            flex: 1;
            padding: 10px 5px;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .nav-tab.active {
            color: #6366f1;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.12);
        }

        .tab-pane {
            display: none;
            padding: 0 20px 30px 20px;
            animation: paneSlideIn 0.4s ease-out;
        }

        @keyframes paneSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-pane.active {
            display: block;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .detail-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        }

        .detail-card:hover {
            border-color: #6366f1;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.08);
            transform: translateY(-2px);
        }

        .detail-card.full-width {
            grid-column: 1 / -1;
        }

        .detail-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #6366f1;
            background: rgba(99, 102, 241, 0.1);
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .detail-card:hover .detail-icon {
            background: #6366f1;
            color: #ffffff;
            transform: scale(1.1);
        }

        .detail-content {
            flex: 1;
            min-width: 0;
        }

        .status-enrolled { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .status-not-enrolled { background: rgba(241, 245, 249, 1); color: #475569; }

        .detail-label {
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
            display: block;
        }

        .salary-breakdown {
            background: #f8fafc;
            border-radius: 18px;
            padding: 20px;
            border: 1px solid #eef2f6;
        }

        .salary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .salary-item:last-child { border-bottom: none; }

        .salary-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .salary-amount {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        .salary-total {
            margin-top: 20px;
            padding: 18px 22px;
            background: #f1f5f9;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .salary-total .salary-label {
            color: #475569;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .salary-total .salary-amount.total {
            color: #1e293b !important;
            font-size: 24px !important;
            font-weight: 900 !important;
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }


    </style>

    <!-- SEARCH & VIEW MODAL -->
    <script>
        // Search functionality (top search bar)
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll("#employeeTable tbody tr");

            rows.forEach(row => {
                if (row.id === 'noResultsRow') return;

                const displayStatus = row.innerText.toLowerCase().includes(searchValue) ? '' : 'none';
                row.style.display = displayStatus;
            });

            // Show no results message if needed
            const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none' && r.id !== 'noResultsRow').length;
            if (visibleRows === 0) {
                const tbody = document.querySelector("#employeeTable tbody");
                if (!document.getElementById('noResultsRow')) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = '<td colspan="6" class="text-center text-muted">No employees found</td>';
                    tbody.appendChild(noResultsRow);
                }
            } else {
                const noResultsRow = document.getElementById('noResultsRow');
                if (noResultsRow) {
                    noResultsRow.remove();
                }
            }
        });

        // Select All Checkbox
        document.getElementById('selectAll').addEventListener('change', function () {
            document.querySelectorAll('.emp-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // View Employee Details in Right Side Panel
        function viewEmployee(empId) {
            // Store current employee ID for edit/delete buttons
            window.currentEmployeeId = empId;

            fetch(`/api/employees/${empId}`)
                .then(res => res.json())
                .then(emp => {
                    // Update employee code in header
                    document.getElementById('employeeCodeDisplay').textContent = `EC${String(emp.id).padStart(4, '0')}`;

                    let photoHtml = emp.photo
                        ? `<img src="/storage/${emp.photo}" class="employee-photo-premium">`
                        : `<div class="employee-photo-premium bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                                   </div>`;

                    const html = `
                                <div class="employee-details-container">
                                    <div class="employee-premium-header">
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="header-photo-wrapper">
                                                ${emp.photo ? 
                                                    `<img src="/storage/${emp.photo}" alt="${emp.name}">` : 
                                                    `<div class="employee-photo-premium d-flex align-items-center justify-content-center bg-white-opacity" style="background: rgba(255,255,255,0.1); width:100px; height:100px; border-radius: 20px;">
                                                        <i class="bi bi-person text-white fs-1"></i>
                                                    </div>`
                                                }
                                            </div>
                                            <div>
                                                <h3 class="mb-1 fw-bold text-white">${emp.name}</h3>
                                                <div class="d-flex align-items-center gap-2 opacity-75">
                                                    <i class="bi bi-briefcase small"></i>
                                                    <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">${emp.designation || 'EMPLOYEE'}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <span class="badge bg-primary px-3 rounded-pill" style="font-size: 10px;">${emp.department || 'N/A'}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="nav-tabs-custom">
                                        <button class="nav-tab active" id="tabPersonal" onclick="showTab('personal')"><i class="bi bi-person"></i>PERSONAL</button>
                                        <button class="nav-tab" id="tabBank" onclick="showTab('bank')"><i class="bi bi-bank"></i>BANK</button>
                                        <button class="nav-tab" id="tabSalary" onclick="showTab('salary')"><i class="bi bi-cash-coin"></i>SALARY</button>
                                    </div>

                                    <div class="tab-content" id="modalTabContent">
                                        <!-- PERSONAL TAB -->
                                        <div id="employeeTabPersonal" class="tab-pane active">
                                            <div class="details-grid">
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-person-fill"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Username</label>
                                                        <p class="detail-value">${emp.username || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-phone"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Mobile</label>
                                                        <p class="detail-value">${emp.mobile_number || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card full-width">
                                                    <div class="detail-icon"><i class="bi bi-envelope"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Email Address</label>
                                                        <p class="detail-value">${emp.email || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-calendar-event"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Date of Birth</label>
                                                        <p class="detail-value">${emp.date_of_birth || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-gender-ambiguous"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Gender</label>
                                                        <p class="detail-value">${emp.gender ? emp.gender.charAt(0).toUpperCase() + emp.gender.slice(1) : 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-card-heading"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Aadhaar Number</label>
                                                        <p class="detail-value">${emp.aadhaar_number || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-credit-card-2-back"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">PAN Number</label>
                                                        <p class="detail-value">${emp.pan_number || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card full-width">
                                                    <div class="detail-icon"><i class="bi bi-geo-alt"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Residential Address</label>
                                                        <p class="detail-value">${emp.address || 'N/A'}</p>
                                                    </div>
                                                </div>

                                                <!-- WORK & JOB DETAILS (Merged into Personal) -->
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-shield-lock"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">System Role</label>
                                                        <p class="detail-value">${emp.role ? emp.role.replace(/_/g, ' ').toUpperCase() : 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-calendar-check"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Joining Date</label>
                                                        <p class="detail-value">${emp.date_of_joining || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-clock"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Shift Hours</label>
                                                        <p class="detail-value">${emp.time_in || 'N/A'} - ${emp.time_out || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-calendar-x"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Leave Balance</label>
                                                        <p class="detail-value text-primary">${emp.leave || '0'} Days</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card full-width">
                                                    <div class="detail-icon"><i class="bi bi-info-circle"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Employee Type</label>
                                                        <p class="detail-value">${emp.employee_type ? emp.employee_type.toUpperCase() : 'N/A'}</p>
                                                    </div>
                                                </div>

                                                <!-- STATUTORY DETAILS (Merged into Personal) -->
                                                <div class="detail-card full-width" style="border-left: 4px solid #6366f1;">
                                                    <div class="detail-content">
                                                        <label class="detail-label text-primary">Statutory Enrollment</label>
                                                        <div class="d-flex flex-column gap-3 mt-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="small fw-bold text-dark">PF Number</span>
                                                                <span class="status-badge ${emp.pf ? 'status-enrolled' : 'status-not-enrolled'}">${emp.pf ? emp.pf_number : 'Not Enrolled'}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="small fw-bold text-dark">ESI Number</span>
                                                                <span class="status-badge ${emp.esi ? 'status-enrolled' : 'status-not-enrolled'}">${emp.esi ? emp.esi_number : 'Not Enrolled'}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="small fw-bold text-dark">Insurance</span>
                                                                <span class="status-badge ${emp.insurance ? 'status-enrolled' : 'status-not-enrolled'}">${emp.insurance ? emp.insurance_provider : 'Not Enrolled'}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BANK TAB -->
                                        <div id="employeeTabBank" class="tab-pane">
                                            <div class="details-grid">
                                                <div class="detail-card full-width">
                                                    <div class="detail-icon"><i class="bi bi-bank"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Bank Name</label>
                                                        <p class="detail-value">${emp.bank_name || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-hash"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">Account Number</label>
                                                        <p class="detail-value">${emp.account_number || 'N/A'}</p>
                                                    </div>
                                                </div>
                                                <div class="detail-card">
                                                    <div class="detail-icon"><i class="bi bi-upc-scan"></i></div>
                                                    <div class="detail-content">
                                                        <label class="detail-label">IFSC Code</label>
                                                        <p class="detail-value text-primary">${emp.ifsc_code || 'N/A'}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SALARY TAB -->
                                        <div id="employeeTabSalary" class="tab-pane">
                                            <div class="salary-container">
                                                <div class="salary-breakdown full-width">
                                                    <div class="salary-item">
                                                        <div class="salary-label"><i class="bi bi-cash"></i>Basic Salary</div>
                                                        <div class="salary-amount">₹ ${parseFloat(emp.basic_salary || 0).toLocaleString('en-IN')}</div>
                                                    </div>
                                                    <div class="salary-item">
                                                        <div class="salary-label"><i class="bi bi-house"></i>HRA</div>
                                                        <div class="salary-amount">₹ ${parseFloat(emp.hra || 0).toLocaleString('en-IN')}</div>
                                                    </div>
                                                    <div class="salary-item">
                                                        <div class="salary-label"><i class="bi bi-truck"></i>Conveyance</div>
                                                        <div class="salary-amount">₹ ${parseFloat(emp.conveyance_allowance || 0).toLocaleString('en-IN')}</div>
                                                    </div>
                                                    <div class="salary-item">
                                                        <div class="salary-label"><i class="bi bi-activity"></i>Medical</div>
                                                        <div class="salary-amount">₹ ${parseFloat(emp.medical_allowance || 0).toLocaleString('en-IN')}</div>
                                                    </div>
                                                    <div class="salary-item">
                                                        <div class="salary-label"><i class="bi bi-gift"></i>Other Allowance</div>
                                                        <div class="salary-amount">₹ ${parseFloat(emp.other_allowance || 0).toLocaleString('en-IN')}</div>
                                                    </div>
                                                    <div class="salary-total">
                                                        <div class="salary-label"><strong>Total Salary</strong></div>
                                                        <div class="salary-amount total">₹ ${(parseFloat(emp.basic_salary || 0) + parseFloat(emp.hra || 0) + parseFloat(emp.conveyance_allowance || 0) + parseFloat(emp.medical_allowance || 0) + parseFloat(emp.other_allowance || 0)).toLocaleString('en-IN')}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                    document.getElementById('employeeDetails').innerHTML = html;
                    new bootstrap.Offcanvas(document.getElementById('employeeModal')).show();
                })
                .catch(err => {
                    console.error('Error loading employee:', err);
                    document.getElementById('employeeDetails').innerHTML = '<div class="p-4 text-center text-danger"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2">Error loading employee details</p></div>';
                });
        }

        // Edit Employee
        function editEmployee(empId = null) {
            const id = empId || window.currentEmployeeId;
            if (!id) {
                alert('No employee selected');
                return;
            }
            window.location.href = `/employees/${id}/edit`;
        }

        // Delete Employee
        function deleteEmployee(empId = null) {
            const id = empId || window.currentEmployeeId;
            if (!id) {
                alert('No employee selected');
                return;
            }

            if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/employees/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(res => {
                        if (res.status === 200 || res.status === 204 || res.ok) {
                            // Close modal if open
                            const modal = bootstrap.Offcanvas.getInstance(document.getElementById('employeeModal'));
                            if (modal) modal.hide();
                            
                            // Remove row from table
                            const row = document.getElementById(`emp-row-${id}`);
                            if (row) {
                                row.classList.add('fade-out'); // Add a fade-out effect
                                setTimeout(() => {
                                    row.remove();
                                    // Check if table is empty
                                    const visibleRows = document.querySelectorAll("#employeeTable tbody tr:not(#noResultsRow)").length;
                                    if (visibleRows === 0) {
                                        const tbody = document.querySelector("#employeeTable tbody");
                                        const noResultsRow = document.createElement('tr');
                                        noResultsRow.id = 'noResultsRow';
                                        noResultsRow.innerHTML = '<td colspan="6" class="text-center py-4 text-muted">No employees found. <a href="{{ route('employees.create') }}">Add one</a></td>';
                                        tbody.appendChild(noResultsRow);
                                    }
                                }, 400);
                            }
                        } else {
                            alert('Error deleting employee');
                        }
                    })
                    .catch(err => {
                        console.error('Delete Error:', err);
                        alert('Error deleting employee');
                    });
            }
        }

        // Tab switching function
        function showTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            // Remove active class from all buttons
            document.querySelectorAll('.nav-tab').forEach(el => el.classList.remove('active'));

            // Show selected tab and add active class
            const targetPane = document.getElementById('employeeTab' + tab.charAt(0).toUpperCase() + tab.slice(1));
            const targetButton = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
            
            if (targetPane) targetPane.classList.add('active');
            if (targetButton) targetButton.classList.add('active');
        }

        // Toggle Filter Section
        function toggleFilter() {
            const filterSection = document.getElementById('filterSection');
            if (filterSection.style.display === 'none') {
                filterSection.style.display = 'block';
            } else {
                filterSection.style.display = 'none';
            }
        }

        // Reset Filter
        function resetFilter() {
            document.getElementById('filterSection').style.display = 'none';
        }

        // Apply Filters
        function applyFilters() {
            const employeeName = document.getElementById('filterEmployeeName').value.toLowerCase();
            const employeeType = document.getElementById('filterEmployeeType').value;
            const department = document.getElementById('filterDepartment').value;
            const role = document.getElementById('filterRole').value;

            const rows = document.querySelectorAll("#employeeTable tbody tr");
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.querySelector('td:nth-child(6)')) { // Skip empty rows
                    const name = row.querySelector('td:nth-child(3)').innerText.toLowerCase();
                    const rowRole = row.querySelector('td:nth-child(4)').innerText.toLowerCase();
                    const rowDepartment = row.querySelector('td:nth-child(5)').innerText.toLowerCase();

                    // Check if row matches all filters
                    const nameMatch = name.includes(employeeName);
                    const roleMatch = role === '' || rowRole.includes(role.replace(/_/g, ' '));
                    const departmentMatch = department === '' || rowDepartment.includes(department.replace(/_/g, ' '));

                    if (nameMatch && roleMatch && departmentMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Show "no results" message if no rows match
            if (visibleCount === 0) {
                const tbody = document.querySelector("#employeeTable tbody");
                if (!document.getElementById('noResultsRow')) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = '<td colspan="6" class="text-center py-4 text-muted">No employees match the filters</td>';
                    tbody.appendChild(noResultsRow);
                }
            } else {
                const noResultsRow = document.getElementById('noResultsRow');
                if (noResultsRow) {
                    noResultsRow.remove();
                }
            }
        }

        // Clear Filters
        function clearFilters() {
            document.getElementById('filterEmployeeName').value = '';
            document.getElementById('filterEmployeeType').value = '';
            document.getElementById('filterDepartment').value = '';
            document.getElementById('filterRole').value = '';

            // Show all rows
            document.querySelectorAll("#employeeTable tbody tr").forEach(row => {
                row.style.display = '';
            });

            // Remove no results message
            const noResultsRow = document.getElementById('noResultsRow');
            if (noResultsRow) {
                noResultsRow.remove();
            }

            document.getElementById('filterSection').style.display = 'none';
        }

        // Delete Selected Employees (Bulk Delete)
        function deleteSelectedEmployees() {
            const selectedCheckboxes = document.querySelectorAll('.emp-checkbox:checked');
            const employeeIds = Array.from(selectedCheckboxes).map(cb => cb.getAttribute('data-id'));

            if (employeeIds.length === 0) {
                alert('Please select at least one employee');
                return;
            }

            if (confirm(`Delete ${employeeIds.length} employee(s)? This action cannot be undone.`)) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Delete each employee
                Promise.all(employeeIds.map(id => 
                    fetch(`/employees/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    }).then(res => {
                        if (res.ok) {
                            const row = document.getElementById(`emp-row-${id}`);
                            if (row) row.remove();
                        }
                        return res;
                    })
                ))
                .then(() => {
                    // Check if table is empty
                    const visibleRows = document.querySelectorAll("#employeeTable tbody tr:not(#noResultsRow)").length;
                    if (visibleRows === 0) {
                        const tbody = document.querySelector("#employeeTable tbody");
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.id = 'noResultsRow';
                        noResultsRow.innerHTML = '<td colspan="6" class="text-center py-4 text-muted">No employees found. <a href="{{ route('employees.create') }}">Add one</a></td>';
                        tbody.appendChild(noResultsRow);
                    }
                    
                    // Uncheck select all
                    document.getElementById('selectAll').checked = false;
                })
                    .catch(err => {
                        console.error('Delete Error:', err);
                        alert('Error deleting employees');
                    });
            }
        }
    </script>

@endsection