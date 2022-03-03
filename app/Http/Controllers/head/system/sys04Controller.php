<?php

namespace App\Http\Controllers\head\system;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sys04Controller extends Controller
{
    protected $types = [
        "shop" => "상점",
        "stock" => "재고",
        "delivery" => "배송",
        "point" => "적립금",
        "api" => "API",
        "sms" => "SMS",
        "stock_reduction" => "부가기능",
        "list_count" => "게시물",
        "etc" => "기타"
    ];

    public function index()
    {

        $values = [
            'types' => $this->types
        ];
        return view(Config::get('shop.head.view') . '/system/sys04', $values);
    }

    public function create()
    {
        $values = [
            'type' => '',
            'name' => '',
        ];
        return view(Config::get('shop.head.view') . '/system/sys04_show', $values);
    }

    public function show($type,$name)
    {
        $values = [
            'type' => $type,
            'name' => $name,
        ];
        return view(Config::get('shop.head.view') . '/system/sys04_show', $values);
    }

    public function get($type,$name)
    {
        $sql =
            /** @lang text */
            "
            select * from conf 
			where type = :type and name = :name
            ";
        $conf = DB::select($sql, array("type" => $type,"name" => $name));
        // print_r ($conf);

        return response()->json([
            "code" => 200,
            "total" => count($conf),
            "conf" => $conf
        ]);
    }

    public function search(Request $req)
    {

        $type        = $req->input('type', '');
        $name        = $req->input('name', '');
        $idx         = $req->input('idx', '');

        $where = "";

        if ($type != ""){
            if($type == "etc"){
                $keys = [];
                foreach($this->types as $key => $val){
                    if($key != "etc"){
                        array_push($keys,sprintf("'%s'",$key));
                    }
                }
                $key_str = join(",",$keys);
                $where .= " and type not in ( $key_str ) ";
            } else {
                $where .= " and type like '%" . Lib::quote($type) . "%' ";
            }
        }
        if ($name  != "")        $where .= " and name  like '%" . Lib::quote($name ) . "%' ";
        if ($idx  != "")        $where .= " and idx  like '%" . Lib::quote($idx ) . "%' ";
        // console.log ('wherer::::'.$where);
        $sql =
            /** @lang text */
            "
            select * from conf
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

    public function store(Request $request)
    {
        $type = $request->input('type');
        $name = $request->input('name');
        $idx = $request->input('idx','');
        $value = $request->input('value','');
        $mvalue = $request->input('mvalue','');
        $content = $request->input('content','');
        $desc = $request->input('desc','');

        $conf = [
            'type' => $type,
            'name' => $name,
            'idx' => "$idx",
            'value' => $value,
            'mvalue' => $mvalue,
            'content' => "$content",
            'desc'=> "$desc",
            'rt' => DB::raw('now()'),
            'ut' => DB::raw('now()'),
        ];
        try {
                DB::transaction(function () use (&$result, $conf) {
                    DB::table('conf')->insert($conf);
                });
                $code = 200;
                $msg = "";
        } catch (Exception $e) {
                $code = 503;
                $msg = $e->getMessage();
        }
        return response()->json(['code' => $code, 'msg' => $msg]);
    }

    public function update($type,$name,Request $request)
    {

        $idx = $request->input('idx');
        $value = $request->input('value');
        $mvalue = $request->input('mvalue');
        $content = $request->input('content');
        $desc = $request->input('desc');

        $conf = [
            'idx' => $idx,
            'value' => $value,
            'mvalue' => $mvalue,
            'content' => $content,
            'desc'=>$desc,
            'ut' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $type,$name, $conf) {
                DB::table('conf')
                    ->where('type', '=', $type)
                    ->where('name', '=', $name)
                    ->update($conf);
            });
            $code = 200;
            $msg = '';
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg]);
    }

    public function delete($type,$name, Request $req)
    {
        try {
            DB::transaction(function () use (&$result, $type,$name) {
                DB::table('conf')
                    ->where('type', '=', $type)
                    ->where('name', '=', $name)
                    ->delete();
            });
            $code = 200;
            $msg = '';
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code]);
    }
}
