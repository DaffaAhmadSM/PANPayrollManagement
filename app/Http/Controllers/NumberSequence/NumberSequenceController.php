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
            "code" => "required|string|unique:number_sequences,code",
            "description" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        NumberSequence::create([
            "code" => $request->code,
            "description" => $request->description,
        ], 201);

        return response()->json([
            "message" => "Number sequence created"
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

        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        $numberSequence->update($request->all('code', 'description'));

        return response()->json([
            "message" => "Number sequence updated"
        ], 200);
    }

    function delete ($id) {
        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        $numberSequence->delete();

        return response()->json([
            "message" => "Number sequence deleted"
        ], 200);
    }

    function detail ($id) {
        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        return response()->json([
            "message" => "Number sequence detail",
            "data" => $numberSequence
        ], 200);
    }

    function getAll () {
        $numberSequences = NumberSequence::cursorPaginate(10, ['id', 'code', 'description']);
        return response()->json([
            "message" => "Number sequence list",
            "header" => ["code", "description"],
            "data" => $numberSequences
        ], 200);
    }

    function search (Request $request) {
        $validate = Validator::make($request->all(), [
            "search" => "required|string"
        ]);

        
        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->first()
            ], 400);
        }

        $numberSequences = NumberSequence::where('code', 'like', "%$request->search%")
            ->orWhere('description', 'like', "%$request->search%")
            ->cursorPaginate(10, ['id', 'code', 'description']);

        return response()->json([
            "message" => "Number sequence search result",
            "header" => ["code", "description"],
            "data" => $numberSequences
        ], 200);
    }
}
