<?php

namespace App\Http\Controllers\WorkingHour;

use App\Models\WorkingHour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WorkingHourController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "code" => "required|string",
            "description" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        WorkingHour::create([
            "code" => $request->code,
            "description" => $request->description,
        ]);

        return response()->json([
            "message" => "Working hour created"
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string",
            "description" => "string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $workingHour = WorkingHour::find($id);
        if (!$workingHour) {
            return response()->json([
                "message" => "Working hour not found"
            ], 404);
        }

        $workingHour->update($request->all());

        return response()->json([
            "message" => "Working hour updated"
        ], 200);
    }

    function detail($id){
        $workingHour = WorkingHour::with('workingHourDetail')->find($id);
        if (!$workingHour) {
            return response()->json([
                "message" => "Working hour not found"
            ], 404);
        }

        return response()->json([
            "message" => "Working hour detail",
            "data" => $workingHour
        ], 200);
    }

    function delete ($id) {
        $workingHour = WorkingHour::find($id);
        if (!$workingHour) {
            return response()->json([
                "message" => "Working hour not found"
            ], 404);
        }

        $workingHour->delete();

        return response()->json([
            "message" => "Working hour deleted"
        ], 200);
    }

    function list(){
        $workingHour = WorkingHour::cursorPaginate(10, ['id', 'code', 'description']);

        return response()->json([
            "message" => "Working hour list",
            "header" => ["code", "description"],
            "data" => $workingHour
        ], 200);
    }

    function getAll() {
        $workingHour = WorkingHour::get(['id', 'code', 'description']);

        return response()->json([
            "message" => "Working hour list",
            "header" => ["code", "description"],
            "data" => $workingHour
        ], 200);
    }

    function search(Request $request) {
        $validate = Validator::make($request->all(), [
            "search" => "required|string"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $workingHour = WorkingHour::where("code", "like", "%$request->search%")
            ->cursorPaginate(10, ['id', 'code', 'description']);

        return response()->json([
            "message" => "Working hour list",
            "header" => ["code", "description"],
            "data" => $workingHour
        ], 200);
    }
}
