<?php

namespace App\Http\Controllers\leave;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LeaveCategory;
use Illuminate\Support\Facades\Validator;

class LeaveCategoryController extends Controller
{

    public function list(Request $request)
    {
        $page = $request->page ?? 70;
        $leaveCategories = LeaveCategory::with('employee:id,no')->cursorPaginate($page, ['id', 'employee_id', 'description', 'deduct', 'paid', 'note']);

        return response()->json([
            'message' => 'Leave categories fetched successfully',
            'data' => $leaveCategories,
            'header' => ['Employee ID', 'Description', 'Deduct', 'Paid', 'Note']
        ]);
    }

    public function getAll()
    {
        $leaveCategories = LeaveCategory::get(['id', 'employee_id', 'description']);

        return response()->json([
            'message' => 'Leave categories fetched successfully',
            'data' => $leaveCategories
        ]);
    }

    public function detail($id)
    {
        $leaveCategory = LeaveCategory::with('employee:id,no')->find($id);

        if (!$leaveCategory) {
            return response()->json([
                'message' => 'Leave category not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Leave category fetched successfully',
            'data' => $leaveCategory
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'description' => 'required|string|max:255',
            'deduct' => 'required|in:yes,no',
            'paid' => 'required|in:yes,no',
            'note' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        LeaveCategory::create($request->all());

        return response()->json([
            'message' => 'Leave category created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'emlpoyee_id' => 'exists:employees,id',
            'description' => 'string|max:255',
            'deduct' => 'in:yes,no',
            'paid' => 'in:yes,no',
            'note' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $leaveCategory = LeaveCategory::find($id);

        if (!$leaveCategory) {
            return response()->json([
                'message' => 'Leave category not found'
            ], 404);
        }

        $leaveCategory->update($request->all());

        return response()->json([
            'message' => 'Leave category updated successfully'
        ]);
    }

    public function delete($id)
    {
        $leaveCategory = LeaveCategory::find($id);

        if (!$leaveCategory) {
            return response()->json([
                'message' => 'Leave category not found'
            ], 404);
        }

        $leaveCategory->delete();

        return response()->json([
            'message' => 'Leave category deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $leaveCategories = LeaveCategory::with('employee:id,no')->whereHas('employee', function ($query) use ($request) {
            $query->where('no', 'like', '%' . $request->search . '%');
        })->orWhere('description', 'like', '%' . $request->search . '%')->cursorPaginate(70, ['id', 'employee_id', 'description', 'deduct', 'paid', 'note']);

        return response()->json([
            'message' => 'Leave categories fetched successfully',
            'data' => $leaveCategories,
            'headers' => ['Employee ID', 'Description', 'Deduct', 'Paid', 'Note']
        ]);
    }
}
