<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class std10Controller extends Controller
{
    public function index()
    {
        $values = [
            'user_yn' => SLib::getCodes('G_USER_TYPE'),
            'types' => SLib::getCodes('G_AD_TYPE'),
            'states' => SLib::getCodes('IS_SHOW')
        ];

        return view(Config::get('shop.head.view') . '/standard/std10', $values);
    }

    public function show($code = '')
    {

        $query = "
            select * 
            from ad_dc
        ";

        $ad_sale = DB::select($query);
        
        if (empty($code)) {
            $type   = "";
            $name   = "";
            $state  = "1";
            $dc_no  = "";
        } else {
            $sql = "
                select * 
                from ad 
                where ad = '$code' 
            ";

            $row = DB::selectOne($sql);

            if (isset($row->type)) {
                $type   = $row->type;
                $name   = $row->name;
                $state  = $row->state;
                $dc_no  = $row->dc_no;
            }
        }

        $values = [
            'code' => $code,
            'type' => $type,
            'name' => $name,
            'state' => $state,
            'dc_no' => $dc_no,
            'types' => SLib::getCodes('G_AD_TYPE'),
            'states' => SLib::getCodes('IS_SHOW'),
            'ad_sale' => $ad_sale
        ];

        return view(Config::get('shop.head.view') . '/standard/std10_show', $values);
    }

    public function search(Request $req)
    {
        $type        = $req->input('type', '');
        $user_yn        = $req->input('user_yn', '');
        $name        = $req->input('name', '');
        $state        = $req->input('state', '');

        $where = "";

        if ($type != "")        $where .= " and type = '$type'";
        if ($user_yn != "")        $where .= " and user_yn = '$user_yn'";
        if ($name != "")        $where .= " and name like '%$name%' ";
        if ($state != "")        $where .= " and state = '$state' ";

        $sql = "
            select  
                user_yn, 
                type, 
                ad, 
                name,  
                if(state = 1, '사용', '미사용') as state,
                rt, 
                ut
            from ad
            where 1=1 $where
        ";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function store($code = '', Request $req)
    {
        if ($code === '') {
            $cnt = DB::table('ad')->whereRaw("ad = UPPER('$req->code')")->get()->count();

            if ($cnt > 0) {
                return response()->json([
                    'msg' => '중복된 코드가 있습니다.'
                ], 500);
            }
        }

        $wheres = ['ad' => DB::raw("UPPER('$req->code')")];
        //updateOrInsert
        $values = [
            'type' => $req->input('type', ''),
            'name' => $req->input('name', ''),
            'state' => $req->input('state', ''),
            'dc_no' => $req->input('ad_sale', '')
        ];
        $values[$code === '' ? 'rt' : 'ut'] = now();

        DB::table('ad')->updateOrInsert($wheres, $values);

        return response()->json(null, 204);
    }

    public function delete($code, Request $req)
    {
        DB::table('ad')->where('ad', $code)->delete();

        return response()->json(null, 204);
    }
}