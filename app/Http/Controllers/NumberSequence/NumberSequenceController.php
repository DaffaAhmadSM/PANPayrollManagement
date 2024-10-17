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
            "prefix" => "string|nullable",
            "starting_number" => "integer|nullable",
            "ending_number" => "integer|nullable",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        if ($request->starting_number > $request->ending_number) {
            return response()->json([
                "message" => "Starting number must be less than ending number"
            ], 400);
        }

        if ($request->starting_number) {
            $request->merge([
                "current_number" => $request->starting_number
            ]);
        }

        $data = NumberSequence::create($request->all(), 201);

        return response()->json([
            "message" => "Number sequence created",
            "data" => $data
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string|nullable",
            "description" => "string|nullable",
            "prefix" => "string|nullable",
            "starting_number" => "integer|nullable",
            "ending_number" => "integer|nullable",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        if($request->starting_number && $request->ending_number) {
            if ($request->starting_number > $request->ending_number) {
                return response()->json([
                    "message" => "Starting number must be less than ending number"
                ], 400);
            }
        }

        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        $numberSequence->update($request->all());

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

    function list () {
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

    function getAll () {
        $numberSequences = NumberSequence::get(['id', 'code']);
        return response()->json([
            "message" => "Number sequence list",
            "data" => $numberSequences
        ], 200);
    }

    function generateNumber ($id) {
        $numberSequence = NumberSequence::find($id);
        if (!$numberSequence) {
            return response()->json([
                "message" => "Number sequence not found"
            ], 404);
        }

        if ($numberSequence->starting_number > $numberSequence->current_number) {
            $numberSequence->current_number = $numberSequence->starting_number;
        }

        $digits = strlen((string) $numberSequence->ending_number);
        $prefix = $numberSequence->prefix ? $numberSequence->prefix : '';
        $generated_number = $prefix . str_pad($numberSequence->current_number, $digits, '0', STR_PAD_LEFT);

        return response()->json([
            "message" => "Number generated",
            "number" => $generated_number
        ], 200);
    }
}
