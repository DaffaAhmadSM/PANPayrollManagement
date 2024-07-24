<?php

namespace App\Http\Controllers\WorkingHour;

use Illuminate\Http\Request;
use App\Models\WorkingHoursDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WorkingHoursDetailController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "working_hour_id" => "required|string",
            "day" => "required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday",
            "hour" => "required|decimal",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        WorkingHoursDetail::create([
            "working_hour_id" => $request->working_hour_id,
            "day" => $request->day,
            "hour" => $request->hour,
        ]);

        return response()->json([
            "message" => "Working hour detail created"
        ], 201);
    }

    function delete ($id) {
        $workingHourDetail = WorkingHoursDetail::find($id);
        if (!$workingHourDetail) {
            return response()->json([
                "message" => "Working hour detail not found"
            ], 404);
        }

        $workingHourDetail->delete();

        return response()->json([
            "message" => "Working hour detail deleted"
        ], 200);
    }


    function detail ($id) {
        $workingHourDetail = WorkingHoursDetail::find($id);
        if (!$workingHourDetail) {
            return response()->json([
                "message" => "Working hour detail not found"
            ], 404);
        }

        return response()->json([
            "data" => $workingHourDetail
        ], 200);
    }

    public function list (Request $request) {
        $page = $request->page ?? 70;
        $workingHourDetail = WorkingHoursDetail::with('workingHour')->cursorPaginate($page, ['id', 'working_hour_id', 'day', 'hour']);
        return response()->json([
            "message" => "Working hour detail list",
            "data" => $workingHourDetail,
            "header" => [
                "Working Hour",
                "Day",
                "Hour"
            ]
        ], 200);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()->first()
            ], 400);
        }

        $workingHourDetail = WorkingHoursDetail::whereHas('workingHour', function($query) use ($request){
            $query->where('name', 'like', "%$request->search%");
        })
        ->orWhere('day', 'like', "%$request->search%")
        ->orWhere('hour', 'like', "%$request->search%")
        ->with('workingHour')->cursorPaginate(70, ['id', 'working_hour_id', 'day', 'hour']);

        return response()->json([
            "message" => "Working hour detail list",
            "data" => $workingHourDetail,
            "header" => [
                "Working Hour",
                "Day",
                "Hour"
            ]
        ], 200);
    }

    function getList ($working_hour){
        $workingHourDetail = WorkingHoursDetail::where('working_hour_id', $working_hour)->get();
        if (!$workingHourDetail) {
            return response()->json([
                "message" => "Working hour detail not found"
            ], 404);
        }

        return response()->json([
            "data" => $workingHourDetail
        ], 200);
    }
}
