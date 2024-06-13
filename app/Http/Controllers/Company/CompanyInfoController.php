<?php

namespace App\Http\Controllers\Company;

use App\Models\CompanyInfo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompanyInfoController extends Controller
{
    function create(Request $request) {
        $validate = Validator::make($request->all(), [
            'code' => 'required|string',
            'name' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'post_code' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'fax' => 'required|string',
            'email' => 'required|string',
            'homepage' => 'required|string',
            'img' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank_name' => 'required|string',
            'bank_account_no' => 'required|string',
            'bank_address' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->first()
            ], 400);
        }
        
        $random = Str::random(5);
        // prosses image name and store it
        $imageName = time(). $random .'.'.$request->img->extension();
        Storage::disk('public')->putFileAs('images', $request->img, $imageName);
        $request->merge(['img_logo' => $imageName]);

        CompanyInfo::create($request->except('img'));

        return response()->json([
            'message' => 'Company info created successfully'
        ], 201);
    }

    function delete($id) {
        $companyInfo = CompanyInfo::find($id);
        if (!$companyInfo) {
            return response()->json([
                'message' => 'Company info not found'
            ], 404);
        }

        // delete image if exist
        if ($companyInfo->img_logo) {
            Storage::disk('public')->delete('images/'.$companyInfo->img_logo);
        }

        $companyInfo->delete();

        return response()->json([
            'message' => 'Company info deleted successfully'
        ], 200);
    }

    function update(Request $request, $id) {
        
        $validate = Validator::make($request->all(), [
            'code' => 'string',
            'name' => 'string',
            'country' => 'string',
            'city' => 'string',
            'post_code' => 'string',
            'address' => 'string',
            'phone' => 'string',
            'fax' => 'string',
            'email' => 'string',
            'homepage' => 'string',
            'img' => 'file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank_name' => 'string',
            'bank_account_no' => 'string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->first()
            ], 400);
        }


        $companyInfo = CompanyInfo::find($id);
        
        if (!$companyInfo) {
            return response()->json([
                'message' => 'Company info not found'
            ], 404);
        }

        if($request->hasFile('img')) {
            // delete old image
            Storage::disk('public')->delete('images/'.$companyInfo->img_logo);

            $random = Str::random(5);
            // prosses image name and store it
            $imageName = time(). $random .'.'.$request->img->extension();
            Storage::disk('public')->putFileAs('images', $request->img, $imageName);
            $request->merge(['img_logo' => $imageName]);
        }

        $companyInfo->update($request->except('img'));

        return response()->json([
            'message' => 'Company info updated successfully'
        ], 200);
    }

    function list() {
        $companyInfo = CompanyInfo::cursorPaginate(10, ['id', 'code', 'name']);
        return response()->json([
            'message' => 'Company info list',
            'header' => ['code', 'name'],
            'data' => $companyInfo
        ], 200);
    }

    function detail($id){
        $companyInfo = CompanyInfo::find($id)->setHidden(['created_at', 'updated_at']);
        if (!$companyInfo) {
            return response()->json([
                'message' => 'Company info not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Company info detail',
            'data' => $companyInfo
        ], 200);
    }

    function search(Request $request) {
        $validate = Validator::make($request()->all(), [
            'search' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->first()
            ], 400);
        }

        $companyInfo = CompanyInfo::where('code', 'like', '%'.$request->search.'%')
            ->orWhere('name', 'like', '%'.$request->search.'%')
            ->cursorPaginate(20, ['id', 'code', 'name']);

        return response()->json([
            'message' => 'Company info search result',
            'header' => ['code', 'name'],
            'data' => $companyInfo
        ], 200);
    }
}
