<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr class="border-b">
                <th scope="row">Employee</th>
                <th>Designation</th>
                <th class="text-center">Total Leaves</th>
                <th class="text-end">Last Taken</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaveHistory as $emp)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-md bg-soft-info text-info">
                                {{ substr($emp->name, 0, 1) }}
                            </div>
                            <span class="fw-bold">{{ $emp->name }}</span>
                        </div>
                    </td>
                    <td><span class="fs-12 text-muted uppercase small">{{ $emp->designation }}</span></td>
                    <td class="text-center">
                        <span class="badge bg-soft-danger text-danger fs-12 fw-black">{{ $emp->leaves_count }}</span>
                    </td>
                    <td class="text-end">
                        <span class="fs-11 fw-medium">{{ $emp->leaves()->latest()->first() ? $emp->leaves()->latest()->first()->start_date->format('M d, Y') : 'N/A' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex justify-content-between align-items-center p-3">
    <div class="fs-11 text-muted">Page {{ $leaveHistory->currentPage() }} of {{ $leaveHistory->lastPage() }}</div>
    @if($leaveHistory->hasPages())
        <div class="pagination-ajax" data-target="#leave-history-container">
            {{ $leaveHistory->appends(request()->except('leave_history_page'))->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
