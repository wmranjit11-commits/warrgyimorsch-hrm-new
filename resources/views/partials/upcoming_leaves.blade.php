<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr class="border-b">
                <th scope="row">Employee</th>
                <th>Leave Date</th>
                <th>Days</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($upcomingLeaves as $leave)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-md bg-soft-primary text-primary">
                                {{ substr($leave->employee->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="d-block text-truncate-1-line fw-bold">{{ $leave->employee->name }}</span>
                                <span class="fs-12 d-block text-muted small">{{ $leave->leave_type }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="d-block fw-bold">{{ $leave->start_date->format('d M, Y') }}</span>
                        <span class="fs-11 text-muted">to {{ $leave->end_date->format('d M, Y') }}</span>
                    </td>
                    <td><span class="badge bg-gray-200 text-dark">{{ $leave->total_days }} Days</span></td>
                    <td>
                        <span class="badge {{ $leave->status == 'approved' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} text-uppercase">
                            {{ $leave->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('leave.history') }}" class="avatar-text avatar-sm bg-soft-primary text-primary" title="View Details">
                                <i class="feather-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No upcoming leaves found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex justify-content-between align-items-center p-3">
    <div class="fs-11 text-muted">Page {{ $upcomingLeaves->currentPage() }} of {{ $upcomingLeaves->lastPage() }}</div>
    @if($upcomingLeaves->hasPages())
        <div class="pagination-ajax" data-target="#upcoming-leaves-container">
            {{ $upcomingLeaves->appends(request()->except('upcoming_leaves_page'))->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
