<?php

namespace App\Http\Controllers\shop\standard;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class std51Controller extends Controller
{
    public function index() {
        $values = [
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
        ];
        return view( Config::get('shop.shop.view') . '/standard/std51',$values);
    }

    public function create(){

        $values = [
            'code' => '',
        ];
        return view( Config::get('shop.shop.view') . '/standard/std51_show',$values);
    }

    public function show($code) {

        $sql = /** @lang text */
            "
            select * from code_kind 
			where code_kind_cd = :code
         ";
        $data_code_kind = DB::selectOne($sql,array("code" => $code));

        $values = [
            'code' => $code,
            'data_code_kind' => $data_code_kind,
        ];

        return view( Config::get('shop.shop.view') . '/standard/std51_show',$values);
    }

    public function search(Request $req) {

        $code_kind_cd	= $req->input('code_kind_cd', '');
        $code_kind_nm	= $req->input('code_kind_nm', '');
        $use_yn		= $req->input('use_yn', '');

        $where = "";

        if ($code_kind_cd != "")	$where .= " and code_kind_cd like '%" . Lib::quote($code_kind_cd) . "%' ";
        if ($code_kind_nm != "")	$where .= " and code_kind_nm like '%" . Lib::quote($code_kind_nm) . "%' ";
        if ($use_yn != "")  		$where .= " and use_yn = '" . Lib::quote($use_yn) . "' ";

        $sql = /** @lang text */
            "
            select * from code_kind 
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

    public function store(Request $request) {

        $code_kind_cd = $request->input('code_kind_cd');
        $code_kind_nm = $request->input('code_kind_nm');
        $code_kind_nm_eng = $request->input('code_kind_nm_eng');
        $use_yn = $request->input('use_yn','Y');
        //$admin_nm = $request->input('admin_nm');

        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $data_code_kind = [
            'code_kind_cd' => $code_kind_cd,
            'code_kind_nm' => $code_kind_nm,
            'code_kind_nm_eng' => $code_kind_nm_eng,
            'use_yn' => $use_yn,
            'seq' => 0,
            'admin_nm' => $id,
            'admin_nm' => $name,
            'rt' => DB::raw('now()'),
            'ut' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $data_code_kind) {
                DB::table('code_kind')->insert($data_code_kind);
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code,'msg' => $msg]);
    }

    public function update($code, Request $request){

        $code_kind_cd = $request->input('code_kind_cd');
        $code_kind_nm = $request->input('code_kind_nm');
        $code_kind_nm_eng = $request->input('code_kind_nm_eng');
        $use_yn = $request->input('use_yn','Y');
        //$admin_nm = $request->input('admin_nm');

        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $data_code_kind = [
            'code_kind_cd' => $code_kind_cd,
            'code_kind_nm' => $code_kind_nm,
            'code_kind_nm_eng' => $code_kind_nm_eng,
            'use_yn' => $use_yn,
            'seq' => 0,
            'admin_nm' => $id,
            'admin_nm' => $name,
            'rt' => DB::raw('now()'),
            'ut' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $code,$data_code_kind) {
                DB::table('code_kind')
                    ->where('code_kind_cd','=',$code)
                    ->update($data_code_kind);
            });
            $code = 200;
        } catch (Exception $e) {
            $code = 500;
        }

        return response()->json(['code' => $code]);
    }

    public function delete($code, Request $req) {
        try {
            DB::transaction(function () use (&$result,$code) {
                DB::table('code_kind')->where('code_kind_cd', $code)->delete();
                DB::table('code')->where('code_kind_cd', $code)->delete();
            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function data_search($code) {

        $sql = /** @lang text */
            "
            select * from code where code_kind_cd = :code      
            order by code_seq asc      
        ";

        $rows = DB::select($sql,array("code" => $code));

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function data_add($code,Request $request) {

        $data_codes = json_decode($request->input("data"));

        try {
            DB::transaction(function () use (&$result, $code,$data_codes) {

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                for($i=0;$i<count($data_codes);$i++){

                    $data = (array)$data_codes[$i];

                    $data_code = [
                        "code_kind_cd" => $code,
                        "code_id" => $data['code_id'],
                        "code_val" => isset($data['code_val'])? $data['code_val']:'',
                        "code_val2" => isset($data['code_val2'])? $data['code_val2']:'',
                        "code_val3" => isset($data['code_val3'])? $data['code_val3']:'',
                        "code_val_eng" => isset($data['code_val_eng'])? $data['code_val_eng']:'',
                        "use_yn" => "Y",
                        "code_seq" => 0,
                        "admin_id" => $id,
                        "admin_nm" => $name,
                        'rt' => DB::raw('now()'),
                        'ut' => DB::raw('now()')
                    ];

                    $cnt = DB::table('code')
                        ->where('code_kind_cd','=',$code)
                        ->where('code_id','=',$data_code["code_id"])
                        ->count();

                    if($cnt === 0){
                        DB::table('code')->insert($data_code);
                    }
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function data_mod($code,Request $request) {

        $data_codes = json_decode($request->input("data"));


        try {
            DB::transaction(function () use (&$result, $code,$data_codes) {

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                for($i=0;$i<count($data_codes);$i++){

                    $data = (array)$data_codes[$i];

                    $data_code = [
                        "code_kind_cd" => $code,
                        "code_id" => $data['code_id'],
                        "code_val" => isset($data['code_val'])? $data['code_val']:'',
                        "code_val2" => isset($data['code_val2'])? $data['code_val2']:'',
                        "code_val3" => isset($data['code_val3'])? $data['code_val3']:'',
                        "code_val_eng" => isset($data['code_val_eng'])? $data['code_val_eng']:'',
                        "use_yn" => "Y",
                        "code_seq" => 0,
                        "admin_id" => $id,
                        "admin_nm" => $name,
                        'ut' => DB::raw('now()')
                    ];

                    DB::table('code')
                    ->where('code_kind_cd','=',$code)
                    ->where('code_id', '=', $data['code_id'])
                    ->update($data_code);

                   
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function data_del($code,Request $request) {

        $code_ids = $request->input('code_ids');

        try {
            DB::transaction(function () use (&$result, $code,$code_ids) {
                for($i=0;$i<count($code_ids);$i++){
                    DB::table('code')
                        ->where('code_kind_cd','=',$code)
                        ->where('code_id','=',$code_ids[$i])
                        ->delete();
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }

    public function data_seq($code,Request $request) {

        $code_ids = $request->input('code_ids');

        try {
            DB::transaction(function () use (&$result, $code,$code_ids) {
                for($i=0;$i<count($code_ids);$i++){
                    DB::table('code')
                        ->where('code_kind_cd','=',$code)
                        ->where('code_id','=',$code_ids[$i])
                        ->update(['code_seq' => $i+1]);
                }
            });
            $code = 200;
            $msg = "";
        } catch (Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json(['code' => $code,"msg" => $msg]);
    }
}
