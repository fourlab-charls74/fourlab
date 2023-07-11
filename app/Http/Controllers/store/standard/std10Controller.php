<?php

namespace App\Http\Controllers\store\standard;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class std10Controller extends Controller
{
    public function index() {
        $values = [
            'section_types' => SLib::getCodes('G_SECTION_TYPE'),
        ];
        return view( Config::get('shop.store.view') . '/standard/std10',$values);
    }

    public function create(){

        $values = [
            'code' => '',
        ];
        return view( Config::get('shop.store.view') . '/standard/std10_show',$values);
    }

    public function show($code) {

        $sql = /** @lang text */
            "
            select 
                size_kind_cd
                , size_kind_nm
                , use_yn
            from size_kind 
			where size_kind_cd = :code
         ";
        $data_size_kind = DB::selectOne($sql,array("code" => $code));

        $values = [
            'code' => $code,
            'data_size_kind' => $data_size_kind,
        ];

        return view( Config::get('shop.store.view') . '/standard/std10_show',$values);
    }

    public function search(Request $req) {

        $size_kind_cd	= $req->input('size_kind_cd', '');
        $size_kind_nm	= $req->input('size_kind_nm', '');
        $use_yn		= $req->input('use_yn', '');

        $where = "";

        if ($size_kind_cd != "")	$where .= " and size_kind_cd like '%" . Lib::quote($size_kind_cd) . "%' ";
        if ($size_kind_nm != "")	$where .= " and size_kind_nm like '%" . Lib::quote($size_kind_nm) . "%' ";
        if ($use_yn != "")  		$where .= " and use_yn = '" . Lib::quote($use_yn) . "' ";

        $sql = /** @lang text */
            "
            select 
            	size_kind_cd, size_kind_nm, use_yn, admin_nm, rt, ut, seq 
            from size_kind 
			where 1=1 $where
			order by seq
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

    //저장
    public function save(Request $request) {

        $size_kind_cd = $request->input('size_kind_cd');
        $size_kind_nm = $request->input('size_kind_nm');
        $use_yn = $request->input('use_yn','Y');
        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $data_size_kind = [
            'size_kind_cd' => $size_kind_cd,
            'size_kind_nm' => $size_kind_nm,
            'use_yn' => $use_yn,
            'seq' => 0,
            'admin_nm' => $id,
            'admin_nm' => $name,
            'rt' => DB::raw('now()'),
            'ut' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $data_size_kind) {
                DB::table('size_kind')->insert($data_size_kind);
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

        $size_kind_cd = $request->input('size_kind_cd');
        $size_kind_nm = $request->input('size_kind_nm');
        $use_yn = $request->input('use_yn','Y');
        //$admin_nm = $request->input('admin_nm');

        $id = Auth::guard('head')->user()->id;
        $name = Auth::guard('head')->user()->name;

        $data_code_kind = [
            'size_kind_cd' => $size_kind_cd,
            'size_kind_nm' => $size_kind_nm,
            'use_yn' => $use_yn,
            'seq' => 0,
            'admin_nm' => $id,
            'admin_nm' => $name,
            'rt' => DB::raw('now()'),
            'ut' => DB::raw('now()'),
        ];

        try {
            DB::transaction(function () use (&$result, $code,$data_code_kind) {
                DB::table('size_kind')
                    ->where('size_kind_cd','=',$code)
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
                DB::table('size_kind')->where('size_kind_cd', $code)->delete();
                DB::table('size')->where('size_kind_cd', $code)->delete();
            });
            $code = 200;
        } catch(Exception $e){
            $code = 500;
        }
        return response()->json(['code' => $code]);
    }

    public function size_search($code) {

        $sql = /** @lang text */
            "
            select * from size where size_kind_cd = :code      
            order by size_seq asc      
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

    public function size_add($code,Request $request) {

        $data_codes = json_decode($request->input("data"));

        try {
            DB::transaction(function () use (&$result, $code,$data_codes) {

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                for($i=0;$i<count($data_codes);$i++){

                    $data = (array)$data_codes[$i];

                    $data_size = [
                        "size_kind_cd" => $code,
                        "size_cd" => $data['size_cd'],
                        "size_nm" => isset($data['size_nm'])? $data['size_nm']:'',
                        "use_yn" => "Y",
                        "size_seq" => 0,
                        "admin_id" => $id,
                        "admin_nm" => $name,
                        'rt' => DB::raw('now()'),
                        'ut' => DB::raw('now()')
                    ];

                    $cnt = DB::table('size')
                        ->where('size_kind_cd','=',$code)
                        ->where('size_cd','=',$data_size["size_cd"])
                        ->count();

                    if($cnt === 0){
                        DB::table('size')->insert($data_size);
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

    public function size_mod($code,Request $request) {

        $data_codes = json_decode($request->input("data"));


        try {
            DB::transaction(function () use (&$result, $code,$data_codes) {

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                for($i=0;$i<count($data_codes);$i++){

                    $data = (array)$data_codes[$i];

                    $data_code = [
                        "size_kind_cd" => $code,
                        "size_cd" => $data['size_cd'],
                        "size_nm" => isset($data['size_nm'])? $data['size_nm']:'',
                        "use_yn" => "Y",
                        "size_seq" => 0,
                        "admin_id" => $id,
                        "admin_nm" => $name,
                        'ut' => DB::raw('now()')
                    ];

                    DB::table('size')
                    ->where('size_kind_cd','=',$code)
                    ->where('size_cd', '=', $data['size_cd'])
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

    public function size_del($code,Request $request) {

        $code_ids = $request->input('code_ids');

        try {
            DB::transaction(function () use (&$result, $code,$code_ids) {
                for($i=0;$i<count($code_ids);$i++){
                    DB::table('size')
                        ->where('size_kind_cd','=',$code)
                        ->where('size_cd','=',$code_ids[$i])
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

    public function size_seq($code,Request $request) {

        $code_ids = $request->input('code_ids');

        try {
            DB::transaction(function () use (&$result, $code,$code_ids) {
                for($i=0;$i<count($code_ids);$i++){
                    DB::table('size')
                        ->where('size_kind_cd','=',$code)
                        ->where('size_cd','=',$code_ids[$i])
                        ->update(['size_seq' => $i+1]);
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

    //사용여부 변경
    public function change_yn($code,Request $request) {

        $data = $request->input('rows');

        try {
			DB::beginTransaction();

            foreach ($data as $d) {
                $size_cd = $d['size_cd'];
                $use_yn = $d['use_yn'];
                $change_yn = "";

                if($use_yn == 'Y') {
                    $change_yn = 'N';
                } else {
                    $change_yn = 'Y';
                }

                DB::table('size')
                    ->where('size_kind_cd','=', $code)
                    ->where('size_cd', '=', $size_cd)
                    ->update([
                        'use_yn' => $change_yn,
                        'ut' => now()
                    ]);
            }
	
			$msg = "정상적으로 저장되었습니다.";
            $code = 200;
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
        return response()->json(['code' => $code,"msg" => $msg]);
    }

	//사이즈 리스트 순서변경
	public function change_seq(Request $request) {

		$size_kind_cds = $request->input('size_kind_cds');

		try {
			DB::beginTransaction();
			for($i=0;$i<count($size_kind_cds);$i++) {
				DB::table('size_kind')
					->where('size_kind_cd','=',$size_kind_cds[$i])
					->update(['seq' => $i+1]);
			}

			$msg = "사이즈 리스트 순서가 변경되었습니다.";
			$code = 200;
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json(['code' => $code,"msg" => $msg]);
	}
	
}
