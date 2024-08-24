<?php

namespace App\Http\Controllers;

use App\Imports\McdImport;
use Illuminate\Http\Request;

class ImportTimeSheetController extends Controller
{
    public function importToTemp(Request $request) {
        $data = (new McdImport)->toArray($request->file('file'));

        return $data;
    }
}
