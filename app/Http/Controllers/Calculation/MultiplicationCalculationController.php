<?php

namespace App\Http\Controllers\Calculation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MultiplicationCalculation;
use Illuminate\Support\Facades\Validator;

class MultiplicationCalculationController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "code" => "required|string",
            "description" => "required|string",
            "multiplier" => "required|decimal"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        MultiplicationCalculation::create([
            "code" => $request->code,
            "description" => $request->description,
            "multiplier" => $request->multiplier
        ]);

        return response()->json([
            "message" => "Multiplication calculation created"
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string",
            "description" => "string",
            "multiplier" => "decimal"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $multiplicationCalculation = MultiplicationCalculation::find($id);
        if (!$multiplicationCalculation) {
            return response()->json([
                "message" => "Multiplication calculation not found"
            ], 404);
        }

        $multiplicationCalculation->update($request->all());

        return response()->json([
            "message" => "Multiplication calculation updated"
        ], 200);
    }

    function delete ($id) {
        $multiplicationCalculation = MultiplicationCalculation::find($id);
        if (!$multiplicationCalculation) {
            return response()->json([
                "message" => "Multiplication calculation not found"
            ], 404);
        }

        $multiplicationCalculation->delete();

        return response()->json([
            "message" => "Multiplication calculation deleted"
        ], 200);
    }

    function detail ($id) {
        $multiplicationCalculation = MultiplicationCalculation::find($id);
        if (!$multiplicationCalculation) {
            return response()->json([
                "message" => "Multiplication calculation not found"
            ], 404);
        }

        return response()->json([
            "data" => $multiplicationCalculation
        ], 200);
    }

    function getList () {
        $multiplicationCalculation = MultiplicationCalculation::all();

        return response()->json([
            "data" => $multiplicationCalculation
        ], 200);
    }
}
