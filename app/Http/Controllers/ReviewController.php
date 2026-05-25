<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeReviewDetail;
use App\Models\EmployeeReview;
use App\Models\Employee;

class ReviewController extends Controller
{
    public function index() {
        $user = auth()->user();
        $userRole = strtolower($user->role);
        
        // Get base query
        $query = EmployeeReview::query();
        
        // Filter based on user role
        if (in_array($userRole, ['admin', 'super admin'])) {
            // Admin can see all reviews
            $query = EmployeeReview::latest();
        } elseif ($userRole === 'team leader') {
            // Team leader sees reviews of employees in their department
            $userDepartment = $user->employee->department ?? null;
            
            if ($userDepartment) {
                $employeeIds = Employee::where('department', $userDepartment)
                    ->pluck('id');
                
                $query = EmployeeReview::whereIn('employee_id', $employeeIds)->latest();
            } else {
                $query = EmployeeReview::where('employee_id', $user->id)->latest();
            }
        } else {
            // Regular employee sees only their own reviews
            $query = EmployeeReview::where('employee_id', $user->id)->latest();
        }
        
        $reviews = $query->paginate(10);
        return view('review.review', compact('reviews'));
    }

    public function store(Request $request){
        $exists= EmployeeReview::where('employee_id',auth()->id())
            ->where('month',$request->month)
            ->where('period',$request->period)
            ->exists();

        if($exists){
            return back()->withErrors('Already submitted');
        }

        $selfTotal = array_sum(array_map('floatval', $request->self_review));

        $authorTotal = array_sum(array_map('floatval', $request->author_review));

        $review=EmployeeReview::create([
            'employee_id'=>auth()->id(),
            'month'=>$request->month,
            'period'=>$request->period,
            'self_total'=>$selfTotal,
            'author_total'=>$authorTotal
        ]);

        foreach($request->criteria_name as $key=>$row){
            EmployeeReviewDetail::create([
                'review_id'=>$review->id,
                'criteria_name'=>$request->criteria_name[$key],
                'criteria_point'=>$request->criteria_point[$key],
                'self_review'=>$request->self_review[$key],
                'author_review'=>$request->author_review[$key]
            ]);
        }

        return back()->with('success','Review Saved');
    }

    public function details($id){
        return EmployeeReviewDetail::where('review_id', $id)->get();
    }
}
