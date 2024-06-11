<?php

namespace App\Http\Controllers\AppSetting;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AppSettingController extends Controller
{
    function getAll() {
        $transformedAppSettings = [];
        $appSettings = AppSetting::all(['name', 'value']);
        foreach ($appSettings as $appSetting) {
            $transformedAppSettings[$appSetting->name] = $appSetting->value;
        }
        return response()->json(
             $transformedAppSettings
        , 200);
    }

    function create(Request $request) {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|string',
            'description' => 'nullable|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        $appSetting = AppSetting::create(
            $request->only(['name', 'value', 'description'])
        );

        return response()->json([
            'message' => 'App setting created successfully'
        ], 201);
    }

    function update(Request $request, $id) {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|string',
            'description' => 'nullable|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()
            ], 400);
        }

        $appSetting = AppSetting::find($id);
        if (!$appSetting) {
            return response()->json([
                'message' => 'App setting not found'
            ], 404);
        }

        $appSetting->update(
            $request->only(['name', 'value', 'description'])
        );

        return response()->json([
            'message' => 'App setting updated successfully'
        ], 200);
    }

    function delete($id) {
        $appSetting = AppSetting::find($id);
        if (!$appSetting) {
            return response()->json([
                'message' => 'App setting not found'
            ], 404);
        }

        $appSetting->delete();

        return response()->json([
            'message' => 'App setting deleted successfully'
        ], 200);
    }
}
