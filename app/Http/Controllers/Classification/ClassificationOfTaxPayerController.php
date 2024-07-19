<?php

namespace App\Http\Controllers\Classification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ClassificationOfTaxPayer;
use Illuminate\Support\Facades\Validator;

class ClassificationOfTaxPayerController extends Controller
{

    function getAll(){
        $classificationOfTaxPayers = ClassificationOfTaxPayer::get(['id', 'code']);
        return response()->json([
            "message" => "Success get all classification of tax payer",
            "data" => $classificationOfTaxPayers
        ], 200);
    }
    
    function create(Request $request) {
        $validate = Validator::make($request->all(), [
            "code" => "required|string",
            "description" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        ClassificationOfTaxPayer::create([
            "code" => $request->code,
            "description" => $request->description,
        ]);

        return response()->json([
            "message" => "Classification of tax payer created"
        ], 201);
    }

    function update(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string",
            "description" => "string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $classificationOfTaxPayer = ClassificationOfTaxPayer::find($id);
        if (!$classificationOfTaxPayer) {
            return response()->json([
                "message" => "Classification of tax payer not found"
            ], 404);
        }

        $classificationOfTaxPayer->update($request->all(['code', 'description']));

        return response()->json([
            "message" => "Classification of tax payer updated"
        ], 200);
    }

    function delete($id) {
        $classificationOfTaxPayer = ClassificationOfTaxPayer::find($id);
        if (!$classificationOfTaxPayer) {
            return response()->json([
                "message" => "Classification of tax payer not found"
            ], 404);
        }

        $classificationOfTaxPayer->delete();

        return response()->json([
            "message" => "Classification of tax payer deleted"
        ], 200);
    }

    function getList() {
        $validate = Validator::make(request()->all(), [
            "perpage" => "integer",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $page = request()->perpage ? request()->perpage : 20;

        $classificationOfTaxPayers = ClassificationOfTaxPayer::cursorPaginate($page, ['id', 'code', 'description']);

        return response()->json([
            'message' => 'Success get classification of tax payer list',
            'header' => ['code', 'description'],
            "data" => $classificationOfTaxPayers
        ], 200);
    }

    function detail($id) {
        $classificationOfTaxPayer = ClassificationOfTaxPayer::find($id);
        if (!$classificationOfTaxPayer) {
            return response()->json([
                "message" => "Classification of tax payer not found"
            ], 404);
        }

        return response()->json([
            "message" => "Success get classification of tax payer detail",
            "data" => $classificationOfTaxPayer
        ], 200);
    }

    function search (Request $request){
        $validate = Validator::make($request->all(), [
            "search" => "string|required"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $classificationOfTaxPayers = ClassificationOfTaxPayer::where("code", "like", "%$request->search%")
            ->cursorPaginate(10, ['id', 'code', 'description']);

        return response()->json([
            "message" => "Success get classification of tax payer list",
            "header" => ["code", "description"],
            "data" => $classificationOfTaxPayers
        ], 200);
    }
}