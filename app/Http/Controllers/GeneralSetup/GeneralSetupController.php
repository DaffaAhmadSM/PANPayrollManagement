<?php

namespace App\Http\Controllers\GeneralSetup;

use App\Models\GeneralSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\NumberSequence;
use Illuminate\Support\Facades\Validator;

class GeneralSetupController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "number_sequence_id" => "required|string|exists:number_sequences,id",
            "customer" => "required|string",
            "customer_invoice" => "required|string",
            "customer_contract" => "required|string",
            "customer_timesheet" => "string|nullable",
            "employee" => "string|nullable",
            "leave_request" => "string|nullable",
            "leave_adjustment" => "string|nullable",
            "timesheet" => "string|nullable",
            "invent_journal_id" => "string|nullable",
            "invent_trans_id" => "string|nullable",
            "vacancy_no" => "string|nullable",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        try {
            GeneralSetup::create($request->all());
            $number_sequence = NumberSequence::find($request->number_sequence_id);
            $number_sequence->increment('current_number', 1);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }

        return response()->json([
            "message" => "General setup created"
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "customer_contract" => "string",
            "customer_timesheet" => "string",
            "customer_invoice" => "string",
            "employee" => "string|nullable",
            "leave_request" => "string|nullable",
            "leave_adjustment" => "string|nullable",
            "timesheet" => "string|nullable",
            "invent_journal_id" => "string|nullable",
            "invent_trans_id" => "string|nullable",
            "vacancy_no" => "string|nullable",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $generalSetup = GeneralSetup::find($id);
        if (!$generalSetup) {
            return response()->json([
                "message" => "General setup not found"
            ], 404);
        }

        $generalSetup->update($request->except(["number_sequence_id"]));

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
        $generalSetup = GeneralSetup::cursorPaginate(10, ['id', 'customer', 'customer_contract','customer_invoice']);

        return response()->json([
            "message" => "General setup list",
            "header" => ["customer", "Customer Contract", "Customer Invoice"],
            "data" => $generalSetup
        ], 200);
    }

    function search (Request $request) {
        $validate = Validator::make($request->all(), [
            "search" => "required|string"
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()->first()
            ], 400);
        }

        $generalSetup = GeneralSetup::where('customer', 'like', "%$request->search%")
            ->orWhere('customer_contract', 'like', "%$request->search%")
            ->orWhere('customer_invoice', 'like', "%$request->search%")
            ->cursorPaginate(10, ['id', 'customer', 'customer_contract','customer_invoice']);

        return response()->json([
            "message" => "General setup search result",
            "header" => ["customer", "Customer Contract", "Customer Invoice"],
            "data" => $generalSetup
        ], 200);
    }
}
