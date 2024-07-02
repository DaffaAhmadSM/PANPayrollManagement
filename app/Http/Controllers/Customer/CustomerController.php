<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NumberSequence;
use Faker\Core\Number;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $page = $request->perpage ?? 10;
        $customer = Customer::orderBy('id', 'desc')->cursorPaginate($page, ['id','no', 'name', 'email']);
        return response()->json([
            'message' => 'Success',
            'data' => $customer,
            'header' => ["no", "name", "email"],
        ],200);
    }

    public function getAll(){
        $customer = Customer::get(['id','name']);
        return response()->json([
            'message' => 'Success',
            'data' => $customer,
        ],200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // no from number sequence
            'no' => 'required|string|unique:customers,no',
            'name' => 'required|string',
            'email' => 'required|email',
            'fax' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'working_hour_id' => 'required|integer|exists:working_hours,id',    
            'number_sequence_id' => 'required|integer|exists:number_sequences,id',
        ],
        [
            'no.unique' => 'The no has already been taken, please refresh to get the latest no.',
        ]
    );

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $customer = Customer::create($request->all());
        DB::beginTransaction();
        try {
            NumberSequence::find($request->number_sequence_id)->increment('current_number');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create customer',
            ], 500);
        }
        
        return response()->json([
            'message' => 'Customer created successfully',
        ], 200);
    }


    public function detail(string $id)
    {
        $customer = Customer::with('workingHour:id,code', 'numberSequence:id,code')->find($id); 
        if (!$customer) {
            return response()->json([       
                'message' => 'Customer not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Customer found',
            'data' => $customer,
        ],200);
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'no' => 'string',
            'name' => 'string',
            'email' => 'email',
            'fax' => 'string',
            'phone' => 'string',
            'address' => 'string',
            'working_hour_id' => 'integer|exists:working_hours,id',    
            'number_sequence_id' => 'integer|exists:number_sequences,id',
        ],
        [
            'no.unique' => 'The no has already been taken, please refresh to get the latest no.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ],400);
        }


        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ],404);
        }

        if($request->number_sequence_id && $request->number_sequence_id != $customer->number_sequence_id){
            DB::beginTransaction();
            try {
                NumberSequence::find($request->number_sequence_id)->increment('current_number');
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to update customer',
                ], 500);
            }
        }

        $customer->update($request->all());
        return response()->json([
            'message' => 'Customer updated successfully',
        ],200);
    }


    public function delete(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ],404);
        }
        $customer->delete();
        return response()->json([
            'message' => 'Customer deleted successfully',
        ],200);
    }


}
