<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NumberSequence;
use Faker\Core\Number;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perpage' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ]);
        }

        $page = $request->perpage ?? 10;
        $customer = Customer::cursorPaginate($page, ['id','no', 'name', 'email']);
        $customer->values()->all();
        return response()->json([
            'message' => 'Success',
            'data' => $customer,
            'header' => ["no", "name", "email"],
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // no from number sequence
            'name' => 'required|string',
            'email' => 'required|email',
            'fax' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'working_hour_id' => 'required|integer|exists:working_hours,id',    
            'number_sequence_id' => 'required|integer|exists:number_sequences,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ]);
        }

        $customer = Customer::create($request->all());
        NumberSequence::find($request->number_sequence_id)->increment('current_number');
        return response()->json([
            'message' => 'Customer created successfully',
        ]);
    }


    public function detail(string $id)
    {
        $customer = Customer::with('workingHour:id,code', 'numberSequence:id,code')->find($id); 
        if (!$customer) {
            return response()->json([       
                'message' => 'Customer not found',
            ]);
        }
        return response()->json([
            'message' => 'Customer found',
            'data' => $customer,
        ]);
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ]);
        }

        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ]);
        }

        $customer->update($request->all());
        return response()->json([
            'message' => 'Customer updated successfully',
        ]);
    }


    public function delete(string $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ]);
        }
        $customer->delete();
        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }


}
