@extends('layouts.app')

@section('content')
    <!-- [ page-header ] start -->
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
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <div id="bulk-action-wrapper" style="display: none;">
                        <a href="javascript:void(0);" id="btn-bulk-delete" class="btn btn-icon btn-soft-danger"
                            style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center;
                            justify-content: center;">
                            <i class="feather-trash-2 fs-18"></i>
                        </a>
                    </div>
                    <!-- <div class="filter-toggle-wrapper">
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand" id="toggleFilter"
                            style="cursor: pointer;">
                            <i class="feather-filter"></i>
                        </a>
                    </div>
                    <a href="javascript:void(0)" class="btn btn-primary" data-bs-toggle="offcanvas"
                    data-bs-target="#projectOffcanvas">
                        <i class="feather-plus me-2"></i>
                        <span>Add</span>
                    </a> -->
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <form action="{{url('/employee-review/store')}}" method="POST">
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
                                <option>January</option>
                                <option>February</option>
                                <option>March</option>
                                <option>April</option>
                                <option>May</option>
                                <option>June</option>
                                <option>July</option>
                                <option>August</option>
                                <option>September</option>
                                <option>October</option>
                                <option>November</option>
                                <option>December</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Select Period</label>
                            <select name="period" class="form-control">
                                <option value="First Half">First Half</option>
                                <option value="Second Half">Second Half</option>
                            </select>
                        </div>

                    </div>
                </div>
                <table class="table table-bordered mt-4">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>criteria_name</th>
                            <th>criteria_point</th>
                            <th>self_review</th>
                            <th>author_review</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                Attendance
                                <input type="hidden" name="criteria_name[]" value="Attendance">
                            </td>
                            <td>
                                5
                                <input type="hidden" name="criteria_point[]" value="5">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="5" name="self_review[]">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Behaviour
                                <input type="hidden" name="criteria_name[]" value="Behaviour">
                            </td>
                            <td>
                                7.5
                                <input type="hidden" name="criteria_point[]" value="7.5">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="7.5" name="self_review[]">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="7.5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Results
                                <input type="hidden" name="criteria_name[]" value="Results">
                            </td>
                            <td>
                                12.5
                                <input type="hidden" name="criteria_point[]" value="12.5">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="12.5" name="self_review[]">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="12.5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Extra Efforts
                                <input type="hidden" name="criteria_name[]" value="Extra Efforts">
                            </td>
                            <td>
                                5
                                <input type="hidden" name="criteria_point[]" value="5">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="5" name="self_review[]">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Honesty
                                <input type="hidden" name="criteria_name[]" value="Honesty">
                            </td>
                            <td>
                                5
                                <input type="hidden" name="criteria_point[]" value="5">
                            </td>
                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="5" name="self_review[]">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Punctuality
                                <input type="hidden" name="criteria_name[]" value="Punctuality">
                            </td>

                            <td>
                                5
                                <input type="hidden" name="criteria_point[]" value="5">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="5" name="self_review[]">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Reporting
                                <input type="hidden" name="criteria_name[]" value="Reporting">
                            </td>

                            <td>
                                7.5
                                <input type="hidden" name="criteria_point[]" value="7.5">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="7.5" name="self_review[]">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="7.5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Customer Relationship
                                <input type="hidden" name="criteria_name[]" value="Customer Relationship">
                            </td>

                            <td>
                                2.5
                                <input type="hidden" name="criteria_point[]" value="2.5">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control self" data-point="2.5" name="self_review[]">
                            </td>

                            <td>
                                <input type="number" step=".5" class="form-control author" data-point="2.5" name="author_review[]">
                            </td>
                        </tr>
                        <tr>
                            <td><b>Total</b></td>
                            <td>
                                <input readonly value="50" class="form-control">
                            </td>
                            <td>
                                <input readonly id="selfTotal" name="self_total" class="form-control">
                            </td>
                            <td>
                                <input readonly id="authorTotal" name="author_total" class="form-control">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-dark w-100">Save</button>
            </div>
        </form>

        <table class="table">
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
                        <td>{{$loop->iteration}}</td>
                        <td>{{$review->month}} {{$review->period}}</td>
                        <td>{{$review->employee->name ?? 'N/A'}}</td>
                        <td>{{$review->self_total}}</td>
                        <td>{{$review->author_total}}</td>
                        <td>
                            <button class="btn btn-dark viewBtn" data-id="{{$review->id}}">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="reviewModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h3>Review Details</h3>
                </div>

                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Criteria</th>
                                <th>Point</th>
                                <th>Self</th>
                                <th>Author</th>
                            </tr>
                        </thead>
                        <tbody id="reviewData"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(function(){

            function updateTotals(){
                var selfTotal = 0;
                var authorTotal = 0;

                $("input[name='self_review[]']").each(function(){
                    selfTotal += Number($(this).val()) || 0;
                });

                $("input[name='author_review[]']").each(function(){
                    authorTotal += Number($(this).val()) || 0;
                });

                $("#selfTotal").val(selfTotal);
                $("#authorTotal").val(authorTotal);
            }

            // Update totals instantly on every keystroke
            $("input[name='self_review[]'], input[name='author_review[]']")
            .on("input", function(){
                updateTotals();
            });

            // Validate on blur (when user leaves the field)
            $("input[name='self_review[]'], input[name='author_review[]']")
            .on("blur", function(){
                let max = Number($(this).data("point"));
                let val = Number($(this).val());

                if(val > max && val !== ''){
                    alert("Cannot exceed " + max + " points for this criteria");
                    $(this).val("");
                    updateTotals();
                }
            });

            // Initialize totals on page load
            updateTotals();

            // View button functionality
            $(document).on('click', '.viewBtn', function(){
                let reviewId = $(this).data('id');
                let modal = $('#reviewModal');
                
                $.ajax({
                    url: '{{ url("/employee-review/details") }}/' + reviewId,
                    type: 'GET',
                    success: function(data){
                        let html = '';
                        data.forEach(function(item, index){
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.criteria_name}</td>
                                    <td>${item.criteria_point}</td>
                                    <td>${item.self_review}</td>
                                    <td>${item.author_review || '-'}</td>
                                </tr>
                            `;
                        });
                        $('#reviewData').html(html);
                        modal.modal('show');
                    }
                });
            });

        });

</script>
@endsection
