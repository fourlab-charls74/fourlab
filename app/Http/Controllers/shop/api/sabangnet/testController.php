<?php

namespace App\Http\Controllers\head\api\sabangnet;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;

class testController extends Controller
{
    public function index(Request $request)
    {
        $tax = $request->input('amt');

        return response()->json([
            "code" => 200,
            "tax" => 0.1*$tax
        ]);
    }



}
