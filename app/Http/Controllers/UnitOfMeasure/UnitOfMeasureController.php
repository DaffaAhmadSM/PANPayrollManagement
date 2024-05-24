<?php

namespace App\Http\Controllers\UnitOfMeasure;

use Illuminate\Http\Request;
use App\Models\NumberSequence;
use App\Http\Controllers\Controller;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Facades\Validator;

class UnitOfMeasureController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "code" => "required|string",
            "description" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        UnitOfMeasure::create([
            "code" => $request->code,
            "description" => $request->description,
        ]);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string",
            "description" => "string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $unitOfMeasure = UnitOfMeasure::find($id);
        if (!$unitOfMeasure) {
            return response()->json([
                "message" => "Unit of measure not found"
            ], 404);
        }

        $unitOfMeasure->update($request->all());

        return response()->json([
            "message" => "Unit of measure updated"
        ]);
    }

    function delete ($id) {
        $unitOfMeasure = UnitOfMeasure::find($id);
        if (!$unitOfMeasure) {
            return response()->json([
                "message" => "Unit of measure not found"
            ], 404);
        }

        $unitOfMeasure->delete();

        return response()->json([
            "message" => "Unit of measure deleted"
        ]);
    }

    function detail ($id) {
        $unitOfMeasure = UnitOfMeasure::find($id);
        if (!$unitOfMeasure) {
            return response()->json([
                "message" => "Unit of measure not found"
            ], 404);
        }

        return response()->json([
            "message" => "Unit of measure detail",
            "data" => $unitOfMeasure
        ]);
    }

    function list () {
        $unitOfMeasures = UnitOfMeasure::all();

        return response()->json([
            "message" => "Unit of measure list",
            "data" => $unitOfMeasures
        ]);
    }
}
