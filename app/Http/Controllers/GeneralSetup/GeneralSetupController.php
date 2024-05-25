<?php

namespace App\Http\Controllers\GeneralSetup;

use App\Models\GeneralSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GeneralSetupController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "number_sequence_id" => "required|string|exists:number_sequences,id",
            "customer" => "required|string",
            "customer_invoice" => "required|string",
            "customer_contract" => "required|string",
            "customer_timesheet" => "string",
            "employee" => "string",
            "leave_request" => "string",
            "leave_adjustment" => "string",
            "timesheet" => "string",
            "invent_journal_id" => "string",
            "invent_trans_id" => "string",
            "vacancy_no" => "string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }


        GeneralSetup::create($request->all());

        return response()->json([
            "message" => "General setup created"
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "customer_contract" => "string",
            "customer_timesheet" => "string",
            "customer_invoice" => "string",
            "employee" => "string",
            "leave_request" => "string",
            "leave_adjustment" => "string",
            "timesheet" => "string",
            "invent_journal_id" => "string",
            "invent_trans_id" => "string",
            "vacancy_no" => "string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $generalSetup = GeneralSetup::find($id);
        if (!$generalSetup) {
            return response()->json([
                "message" => "General setup not found"
            ], 404);
        }

        $generalSetup->update($request->except(["number_sequence_id", "customer"]));

        return response()->json([
            "message" => "General setup updated"
        ], 200);

    }

    function delete ($id) {
        $generalSetup = GeneralSetup::find($id);
        if (!$generalSetup) {
            return response()->json([
                "message" => "General setup not found"
            ], 404);
        }

        $generalSetup->delete();

        return response()->json([
            "message" => "General setup deleted"
        ], 200);
    }

    function detail($id) {
        $generalSetup = GeneralSetup::with('numberSequence')->find($id);
        if (!$generalSetup) {
            return response()->json([
                "message" => "General setup not found"
            ], 404);
        }

        return response()->json([
            "data" => $generalSetup
        ], 200);
    }

    function getAll () {
        $generalSetup = GeneralSetup::all(['id', 'customer', 'customer_contract','customer_invoice']);

        return response()->json([
            "data" => $generalSetup
        ], 200);
    }
}
