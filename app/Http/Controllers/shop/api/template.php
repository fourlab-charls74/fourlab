<?php

namespace App\Http\Controllers\shop\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class template extends Controller
{
    public function index(Request $req){
        $values = [  ];

        return view( Config::get('shop.shop.view') . "/common/template", $values);
    }

    public function search() {
        
		$sql = "
            select qna_no, subject, ans_msg from qna_ans_type
            where kind = 'SMS' and use_yn = 'Y'
            order by type, subject desc
        ";
        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [
                'total' => count($rows)
            ],
            "body" => $rows
        ]);
    }

    public function detail($no){
        $sql = "
			select ans_msg as ans from qna_ans_type where qna_no = '$no'
        ";
        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [
                'total' => count($rows)
            ],
            "body" => $rows
        ]);
    }
}
