@extends('layouts.app')

@section('content')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Employee Review</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Employee Review</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ url('/employee-review/store') }}" method="POST">
        @csrf
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h4>Employee Review</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Leave Allotment Month</label>
                            <select name="month" class="form-control" required>
                                <option value="">Select Month</option>
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                    <option value="{{ $month }}" {{ old('month') === $month ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Select Period</label>
                            <select name="period" class="form-control">
                                <option value="First Half" {{ old('period', 'First Half') === 'First Half' ? 'selected' : '' }}>First Half</option>
                                <option value="Second Half" {{ old('period') === 'Second Half' ? 'selected' : '' }}>Second Half</option>
                            </select>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered mt-4">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Criteria Name</th>
                            <th>Max Point</th>
                            <th>Self Review</th>
                            <th>Author Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Attendance <input type="hidden" name="criteria_name[]" value="Attendance"></td>
                            <td>5 <input type="hidden" name="criteria_point[]" value="5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="5" name="self_review[]" min="0" max="5" value="{{ old('self_review.0') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="5" name="author_review[]" min="0" max="5" value="{{ old('author_review.0') }}"></td>
                        </tr>
                        <tr>
                            <td>Behaviour <input type="hidden" name="criteria_name[]" value="Behaviour"></td>
                            <td>7.5 <input type="hidden" name="criteria_point[]" value="7.5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="7.5" name="self_review[]" min="0" max="7.5" value="{{ old('self_review.1') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="7.5" name="author_review[]" min="0" max="7.5" value="{{ old('author_review.1') }}"></td>
                        </tr>
                        <tr>
                            <td>Results <input type="hidden" name="criteria_name[]" value="Results"></td>
                            <td>12.5 <input type="hidden" name="criteria_point[]" value="12.5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="12.5" name="self_review[]" min="0" max="12.5" value="{{ old('self_review.2') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="12.5" name="author_review[]" min="0" max="12.5" value="{{ old('author_review.2') }}"></td>
                        </tr>
                        <tr>
                            <td>Extra Efforts <input type="hidden" name="criteria_name[]" value="Extra Efforts"></td>
                            <td>5 <input type="hidden" name="criteria_point[]" value="5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="5" name="self_review[]" min="0" max="5" value="{{ old('self_review.3') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="5" name="author_review[]" min="0" max="5" value="{{ old('author_review.3') }}"></td>
                        </tr>
                        <tr>
                            <td>Honesty <input type="hidden" name="criteria_name[]" value="Honesty"></td>
                            <td>5 <input type="hidden" name="criteria_point[]" value="5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="5" name="self_review[]" min="0" max="5" value="{{ old('self_review.4') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="5" name="author_review[]" min="0" max="5" value="{{ old('author_review.4') }}"></td>
                        </tr>
                        <tr>
                            <td>Punctuality <input type="hidden" name="criteria_name[]" value="Punctuality"></td>
                            <td>5 <input type="hidden" name="criteria_point[]" value="5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="5" name="self_review[]" min="0" max="5" value="{{ old('self_review.5') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="5" name="author_review[]" min="0" max="5" value="{{ old('author_review.5') }}"></td>
                        </tr>
                        <tr>
                            <td>Reporting <input type="hidden" name="criteria_name[]" value="Reporting"></td>
                            <td>7.5 <input type="hidden" name="criteria_point[]" value="7.5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="7.5" name="self_review[]" min="0" max="7.5" value="{{ old('self_review.6') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="7.5" name="author_review[]" min="0" max="7.5" value="{{ old('author_review.6') }}"></td>
                        </tr>
                        <tr>
                            <td>Customer Relationship <input type="hidden" name="criteria_name[]" value="Customer Relationship"></td>
                            <td>2.5 <input type="hidden" name="criteria_point[]" value="2.5"></td>
                            <td><input type="number" step=".5" class="form-control self-input" data-point="2.5" name="self_review[]" min="0" max="2.5" value="{{ old('self_review.7') }}"></td>
                            <td><input type="number" step=".5" class="form-control author-input" data-point="2.5" name="author_review[]" min="0" max="2.5" value="{{ old('author_review.7') }}"></td>
                        </tr>
                        <tr class="table-info">
                            <td><b>Total</b></td>
                            <td><input readonly value="50" class="form-control"></td>
                            <td><input readonly id="selfTotal" name="self_total" class="form-control" value="{{ old('self_total', 0) }}"></td>
                            <td><input readonly id="authorTotal" name="author_total" class="form-control" value="{{ old('author_total', 0) }}"></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-dark w-100">Save</button>
            </div>
        </form>

        <h4 class="mt-5">Submitted Reviews</h4>
        <table class="table table-striped mt-2">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Month</th>
                    <th>Employee</th>
                    <th>Self Review</th>
                    <th>Author Review</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $review->month }} ({{ $review->period }})</td>
                        <td>{{ $review->employee->name ?? 'N/A' }}</td>
                        <td>{{ $review->self_total }}</td>
                        <td>{{ $review->author_total }}</td>
                        <td>
                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $review->id }}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-2">
            {{ $reviews->links() }}
        </div>
    </div>

    @foreach($reviews as $review)
        <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Review Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Criteria</th>
                                    <th>Max Point</th>
                                    <th>Self Score</th>
                                    <th>Author Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($review->details as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->criteria_name }}</td>
                                        <td><span class="badge bg-secondary">{{ $detail->criteria_point }}</span></td>
                                        <td>{{ $detail->self_review }}</td>
                                        <td>{{ $detail->author_review }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No metadata logged for this entry.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            function updateTotals() {
                let selfTotal = 0;
                let authorTotal = 0;

                document.querySelectorAll(".self-input").forEach(function(input) {
                    let val = parseFloat(input.value);
                    if (!isNaN(val)) selfTotal += val;
                });

                document.querySelectorAll(".author-input").forEach(function(input) {
                    let val = parseFloat(input.value);
                    if (!isNaN(val)) authorTotal += val;
                });

                document.getElementById("selfTotal").value = selfTotal % 1 === 0 ? selfTotal : selfTotal.toFixed(1);
                document.getElementById("authorTotal").value = authorTotal % 1 === 0 ? authorTotal : authorTotal.toFixed(1);
            }

            document.addEventListener("input", function(e) {
                if (e.target.classList.contains("self-input") || e.target.classList.contains("author-input")) {
                    let max = parseFloat(e.target.getAttribute("data-point")) || 0;
                    let val = parseFloat(e.target.value) || 0;

                    if (val > max && e.target.value !== "") {
                        alert("Cannot exceed " + max + " points for this criteria");
                        e.target.value = "";
                    }
                    updateTotals();
                }
            });

            // Run validation check on blur fallback
            document.addEventListener("focusout", function(e) {
                if (e.target.classList.contains("self-input") || e.target.classList.contains("author-input")) {
                    let max = parseFloat(e.target.getAttribute("data-point")) || 0;
                    let val = parseFloat(e.target.value) || 0;

                    if (val > max && e.target.value !== "") {
                        e.target.value = "";
                        updateTotals();
                    }
                }
            });

            updateTotals();
        });
    </script>
@endsection