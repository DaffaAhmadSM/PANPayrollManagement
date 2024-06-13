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
            "code" => "required|string|unique:unit_of_measures,code",
            "description" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        UnitOfMeasure::create([
            "code" => $request->code,
            "description" => $request->description,
        ]);

        return response()->json([
            "message" => "Unit of measure created"
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

        $unitOfMeasure = UnitOfMeasure::find($id);
        if (!$unitOfMeasure) {
            return response()->json([
                "message" => "Unit of measure not found"
            ], 404);
        }

        $unitOfMeasure->update($request->all());

        return response()->json([
            "message" => "Unit of measure updated"
        ], 200);
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
        ], 200);
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
        ], 200);
    }

    function list () {
        $unitOfMeasures = UnitOfMeasure::cursorPaginate(10,['id', 'code', 'description']);

        return response()->json([
            "message" => "Unit of measure list",
            "header" => ["code", "description"],
            "data" => $unitOfMeasures
        ], 200);
    }

    function search (Request $request) {
        $validate = Validator::make($request->all(), [
            "search" => "required|string"
        ]);

        $unitOfMeasures = UnitOfMeasure::where("code", "like", "%$request->search%")
            ->orWhere("description", "like", "%$request->q%")
            ->cursorPaginate(10, ['id', 'code', 'description']);

        return response()->json([
            "message" => "Unit of measure search result",
            "header" => ["code", "description"],
            "data" => $unitOfMeasures
        ], 200);
    }
}
