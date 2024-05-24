<?php

namespace App\Http\Controllers\NumberSequence;

use App\Http\Controllers\Controller;
use App\Models\NumberSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NumberSequenceController extends Controller
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

        NumberSequence::create([
            "code" => $request->code,
            "description" => $request->description,
        ]);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "required|string",
            "description" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        $numberSequence->update([
            "code" => $request->code,
            "description" => $request->description,
        ]);
    }

    function delete ($id) {
        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        $numberSequence->delete();
    }

    function detail ($id) {
        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        return response()->json([
            "number_sequence" => $numberSequence
        ], 200);
    }

    function getAll () {
        $numberSequences = NumberSequence::all();
        return response()->json([
            "number_sequences" => $numberSequences
        ], 200);
    }
}
