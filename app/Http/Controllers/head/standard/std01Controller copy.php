<?php

namespace App\Http\Controllers\head\standard;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class std01Controller extends Controller
{
    public function index()
    {
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        $values = [
            "admin_id" => $id,
            "admin_nm" => $name
        ];
        return view(Config::get('shop.head.view') . '/standard/std01', $values);
    }
    public function create()
    {
        $values = [
            'opt_kind_cd' => '',
            'opt_kind_nm' => '',
        ];
        return view(Config::get('shop.head.view') . '/standard/std01_show', $values);
    }
    public function show($opt_kind_cd,$opt_kind_nm)
    {
        $values = [
            'type' => $opt_kind_cd,
            'name' => $opt_kind_nm,
        ];
        return view(Config::get('shop.head.view') . '/standard/std01_show', $values);
    }

    public function get($opt_kind_cd,$opt_kind_nm)
    {
        $sql =
            /** @lang text */
            "
            select * from opt 
			where opt_kind_cd = :opt_kind_cd and opt_kind_nm = :opt_kind_nm
            ";
        $conf = DB::select($sql, array("opt_kind_cd" => $opt_kind_cd,"opt_kind_nm" => $opt_kind_nm));
        // print_r ($conf);

        return response()->json([
            "code" => 200,
            "total" => count($opt),
            "conf" => $opt
        ]);
    }


    public function search(Request $request)
    {
        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;

        $opt_kind    = $request->input("opt_kind");
        $use_yn    = $request->input("use_yn", "");
        $memo        = $request->input("memo");

        $where = "";
        if ($opt_kind != "") {
            $where .= "and (a.opt_kind_cd like '%$opt_kind%' or a.opt_kind_nm like '%$opt_kind%')";
        }
        if ($use_yn != "") {
            $where .= "and a.use_yn = '$use_yn'";
        }
        if ($memo != "") {
            $where .= "and a.memo like '%$memo%'";
        }

        $page_size = 10;
        if ($page == 1) {
            $query = "
                select count(*) as total
                from opt a
                left outer join (
                    select opt_kind_cd, count(*) as goods_cnt
                    from goods 
                    group by opt_kind_cd
                ) b on a.opt_kind_cd = b.opt_kind_cd
                where 1=1 
                    $where
            ";
            //echo $query;
            //$row = DB::select($query,['com_id' => $com_id]);
            $row = DB::select($query);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }


        $query = "
            select a.opt_kind_cd, a.opt_kind_nm, ifnull(b.goods_cnt, '0') as goods_cnt, a.use_yn, a.regi_date, a.upd_date
            from opt a
                left outer join (
                    select opt_kind_cd, count(*) as goods_cnt
                    from goods 
                    group by opt_kind_cd
                ) b on a.opt_kind_cd = b.opt_kind_cd
            where a.opt_id = 'K'
                $where
            order by no desc
        ";
        $result = DB::select($query);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function GetOpt(Request $request)
    {
        $opt_kind_cd = $request->input("opt_kind_cd");


        $query1 = "
			select opt_kind_cd, opt_kind_nm
			from opt
			where opt_id = 'K'
                and use_yn = 'Y'
            order by no asc
        ";
        $kind_cd_items = DB::select($query1);

        $query2 = "
			select
				opt_kind_cd, opt_kind_nm, memo, use_yn, admin_id, admin_nm, regi_date, upd_date
			from opt
			where opt_kind_cd = '$opt_kind_cd'
				and opt_id = 'K'
		";
        $result = DB::select($query2);

        return response()->json([
            "code" => 200,
            "body" => $result,
            "kind_cd_items" => $kind_cd_items
        ]);
    }

    /*
		Function: CheckOpt
		품목 코드 중복확인
	*/
    function CheckOpt(Request $request)
    {

        $opt_kind_cd = $request->input("opt_kind_cd");
        $opt_type = 0;

        $query = "
			select
				count(*) as cnt
			from opt
			where opt_kind_cd = '$opt_kind_cd'
				and opt_id = 'K'
		";
        $row = DB::select($query);
        $cnt = $row[0]->cnt;
        if ($cnt > 0) {
            $opt_type = 0;
        } else {
            $opt_type = 1;
        }

        return response()->json([
            "code" => 200,
            "responseText" => $opt_type
        ]);
    }

    public function Command(Request $request)
    {
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        $cmd = $request->input("cmd");

        $opt_kind_cd        = $request->input("opt_kind_cd");
        $opt_kind_nm         = $request->input("opt_kind_nm");
        $memo                = $request->input("memo");
        $use_yn                = $request->input("use_yn");
        $chg_opt_kind_cd     = $request->input("chg_opt_kind_cd");

        //$result = false;

        /*
        echo $cmd;
        echo "<br>";
        */
        $opt_in_result = 500;
        $goods_result = 500;
        if ($cmd == "addcmd") {
            $query = "
				select count(*) as cnt
				from opt 
				where opt_kind_cd = '$opt_kind_cd'
					and opt_id = 'K'
			";
            $row = DB::select($query);
            $cnt = $row[0]->cnt;
            //echo $cnt;
            if ($cnt == 0) {        // 등록된 코드가 없을때만 insert
                $insert_opt = "insert into opt(
                    opt_kind_cd, opt_kind_nm, opt_id, use_yn, memo, opt_seq, admin_id, admin_nm, regi_date, upd_date
                )values(
                    '$opt_kind_cd', '$opt_kind_nm', 'K', '$use_yn', '$memo', 0, '$id', '$name', now(), now()
                )";

                try {
                    DB::insert($insert_opt);
                    $opt_in_result = 200;
                } catch (Exception $e) {
                    $opt_in_result = 500;
                };
                $opt_cnt = 1;
            }
        } else if ($cmd == "editcmd") {
            $update_items = [
                "opt_kind_nm" => $opt_kind_nm,
                "use_yn" => $use_yn,
                "memo" => $memo,
                "admin_id" => $id,
                "admin_nm" => $name,
                "upd_date" => now()
            ];
            try {
                DB::table('opt')
                    ->where('opt_kind_cd', '=', $opt_kind_cd)
                    ->where('opt_id', '=', 'K')
                    ->update($update_items);
                //$code = 200;
                $opt_in_result = 200;
            } catch (Exception $e) {
                //$code = 500;
                $opt_in_result = 500;
            }

            //$goods_class_up_result = 200;

            //echo $query;
            //$result = $conn->Execute($sql);
        } else if ($cmd == "delcmd") {
            //echo "삭제!!!";
            //삭제할 해당 품목의 상품 폼목을 none으로 변경
            $update_goods = [
                "opt_kind_cd" => "none"
            ];

            try {
                DB::table('goods')
                    ->where('opt_kind_cd', '=', $opt_kind_cd)
                    ->update($update_goods);
                //$code = 200;
                $goods_result = 200;
            } catch (Exception $e) {
                //$code = 500;
                $goods_result = 500;
            }

            //품목 삭제 
            $query1 = "
				delete
				from opt 
				where opt_kind_cd = '$opt_kind_cd'
					and opt_id = 'K'
            ";
            try {
                DB::table('opt')
                    ->where('opt_kind_cd', '=', $opt_kind_cd)
                    ->where('opt_id', '=', "K")
                    ->delete();
                $opt_in_result = 200;
            } catch (Exception $e) {
                $opt_in_result = 500;
            }
        } else if ($cmd == "chg_opt_kind") {    // 품목 변경
            //상품 품목을 변경할 품목으로 변경
            /*
            $sql = "
				update goods set 
					opt_kind_cd = '$chg_opt_kind_cd'
				where opt_kind_cd = '$OPT_KIND_CD'
            ";
            */
            $update_goods = [
                "opt_kind_cd" => $chg_opt_kind_cd
            ];
            try {
                DB::table('goods')
                    ->where('opt_kind_cd', '=', $opt_kind_cd)
                    ->update($update_goods);
                //$code = 200;
                $goods_result = 200;
            } catch (Exception $e) {
                //$code = 500;
                $goods_result = 500;
            }

            //변경된 품목은 삭제 처리
            /*
			$sql = "
				delete
				from opt 
				where opt_kind_cd = '$OPT_KIND_CD'
            ";			
            */
            try {
                DB::table('opt')
                    ->where('opt_kind_cd', '=', $opt_kind_cd)
                    ->where('opt_id', '=', "K")
                    ->delete();
                $opt_in_result = 200;
            } catch (Exception $e) {
                $opt_in_result = 500;
            }
        }

        return response()->json([
            "opt_in_result" => $opt_in_result,
            "goods_result" => $goods_result
        ]);
    }
}
