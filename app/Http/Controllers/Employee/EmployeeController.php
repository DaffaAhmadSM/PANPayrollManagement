<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{

    public function list(Request $request)
    {
        $page = $request->perpage ?? 1;
        $employees = Employee::cursorPaginate($page, ['id', 'no', 'name', 'type', 'gender', 'search_name']);

        return response()->json([
            'message' => 'Success',
            'data' => $employees,
            'header' => ['No', 'Name', 'Type', 'Gender', 'Search Name']
        ], 200);
    }

    public function getAll(){
        $employees = Employee::take(70)->get(['id', 'no',]);
        return response()->json([
            'message' => 'Success',
            'data' => $employees
        ], 200);
    }

    public function detail($id)
    {
        $employee = Employee::with('education', 'classificationOfTaxPayer')->find($id);

        if(!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $employee
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'required|exists:number_sequences,id',
            'name' => 'string|max:255',
            'type' => 'in:employee,freelance',
            'search_name' => 'string|max:255',
            'gender' => 'in:male,female',
            'birth_date' => 'date',
            'birth_place' => 'string|max:255',
            'blood_type' => 'in:A,B,AB,O',
            'religion' => 'in:Muslim,Protestant,Catholic,Hindu,Buddhist,Confucian',
            'ethnic_group' => 'string|max:255',
            'phone' => 'string|max:255',
            'email' => 'email|max:255',
            'marital_status' => 'in:single,married,divorced,widowed,none',
            'number_of_dependents' => 'integer|min:0',
            'status' => 'in:active,inactive',
            'last_education' => 'required|exists:education_levels,id',
            'clothes_size' => 'string|max:255',
            'shoes_size' => 'string|max:255',
            'entitle_leaved_per_month' => 'numeric|between:0,99999999.99',
            'img_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'identity_number' => 'string|max:255',
            'family_card_number' => 'string|max:255',
            'passport_number' => 'string|max:255',
            'passport_expired_date' => 'date',
            'tax_number' => 'string|max:255',
            'tax_start_date' => 'date',
            'classification_of_tax_payer_id' => 'required|exists:classification_of_tax_payers,id',
            'tax_paid_by_company' => 'in:yes,no',
            'tax_calculation_method' => 'in:gross,net,gross_up,none',
            'emergency_contact_name' => 'string|max:255',
            'emergency_contact_phone' => 'string|max:255',
            'emergency_contact_address' => 'string|max:255',
            'emergency_contact_relationship' => 'string|max:255',
            'bank_account' => 'string|max:255',
            'bank_branch' => 'string|max:255',
            'bank_no' => 'string|max:255',
            'bank_holder' => 'string|max:255',
            'bpjs_tk' => 'string|max:255',
            'bpjs_medical' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()->first()
            ],400);
        }

        if($request->hasFile('img_picture')) {
            $random = Str::random(5);
            $imageName = time(). $random .'.'.$request->img->extension();
            Storage::disk('public')->putFileAs('images/employee/', $request->img, $imageName);
            $request->merge(['img_picture' => 'images/employee/' . $imageName]);
        }

        $numberSequence = NumberSequence::find($request->number_sequence_id);

        $request->merge(['no' => $numberSequence->code . $numberSequence->current_number]);

        try {
            DB::beginTransaction();
            $numberSequence->increment('current_number');
            Employee::create($request->all());

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create employee'
            ], 500);
        }


        return response()->json([
            'message' => 'Employee created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'exists:number_sequences,id',
            'name' => 'string|max:255',
            'type' => 'in:employee,freelance',
            'search_name' => 'string|max:255',
            'gender' => 'in:male,female',
            'birth_date' => 'date',
            'birth_place' => 'string|max:255',
            'blood_type' => 'in:A,B,AB,O',
            'religion' => 'in:Muslim,Protestant,Catholic,Hindu,Buddhist,Confucian',
            'ethnic_group' => 'string|max:255',
            'phone' => 'string|max:255',
            'email' => 'email|max:255',
            'marital_status' => 'in:single,married,divorced,widowed,none',
            'number_of_dependents' => 'integer|min:0',
            'status' => 'in:active,inactive',
            'last_education' => 'required|exists:education_levels,id',
            'clothes_size' => 'string|max:255',
            'shoes_size' => 'string|max:255',
            'entitle_leaved_per_month' => 'numeric|between:0,99999999.99',
            'img_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'identity_number' => 'string|max:255',
            'family_card_number' => 'string|max:255',
            'passport_number' => 'string|max:255',
            'passport_expired_date' => 'date',
            'tax_number' => 'string|max:255',
            'tax_start_date' => 'date',
            'classification_of_tax_payer_id' => 'required|exists:classification_of_tax_payers,id',
            'tax_paid_by_company' => 'in:yes,no',
            'tax_calculation_method' => 'in:gross,net,gross_up,none',
            'emergency_contact_name' => 'string|max:255',
            'emergency_contact_phone' => 'string|max:255',
            'emergency_contact_address' => 'string|max:255',
            'emergency_contact_relationship' => 'string|max:255',
            'bank_account' => 'string|max:255',
            'bank_branch' => 'string|max:255',
            'bank_no' => 'string|max:255',
            'bank_holder' => 'string|max:255',
            'bpjs_tk' => 'string|max:255',
            'bpjs_medical' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()->first()
            ],400);
        }


        $employee = Employee::find($id);

        if(!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }

        if($request->hasFile('img_picture')) {
            Storage::disk('public')->delete($employee->img_picture);
            $random = Str::random(5);
            $imageName = time(). $random .'.'.$request->img->extension();
            Storage::disk('public')->putFileAs('images/employee/', $request->img, $imageName);
            $request->merge(['img_picture' => 'images/employee/' .  $imageName]);
        }

        if($request->has('number_sequence_id') && $request->number_sequence_id != $employee->number_sequence_id) {
            $numberSequence = NumberSequence::find($request->number_sequence_id);
            $request->merge(['no' => $numberSequence->code . $numberSequence->current_number]);
            $numberSequence->increment('current_number');
        }

        $employee->update($request->all());

        return response()->json([
            'message' => 'Employee updated successfully'
        ], 200);

    }

    public function delete($id)
    {
        $employee = Employee::find($id);

        if(!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }

        Storage::disk('public')->delete($employee->img_picture);
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ], 200);
    }


    public function search(Request $request){
        $data = Employee::where('name', 'like', '%'.$request->name.'%')->orWhere('no', 'like', '%'.$request->name.'%')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }
}
