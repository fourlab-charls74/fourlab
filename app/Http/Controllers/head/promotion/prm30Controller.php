<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class prm30Controller extends Controller
{
    public function index()
    {
        $sql =
            /** @lang text */
            "
                select 
                    ifnull(pv,0) as pv,ifnull(tags,0) as tags,ifnull(mpv,0) as mpv 
                from  search_func
           ";
        $sch = DB::selectOne($sql);
        if (!isset($sch)) {
            $sch = (object)array(
                'pv' => 0,
                'tags' => 0,
                'mpv' => 0,
            );
        }

        $values = [
            'sch' => $sch
        ];
        //dd($values);
        return view(Config::get('shop.head.view') . '/promotion/prm30', $values);
    }

    public function search(Request $req)
    {
        $cfg_img_size_list        = SLib::getCodesValue("G_IMG_SIZE", "list");
        $cfg_img_size_real        = SLib::getCodesValue("G_IMG_SIZE", "real");

        $kwd              = Request("kwd");
        $pv_1m_fr         = Request("pv_1m_fr");
        $pv_1m_to         = Request("pv_1m_to");
        $disp_yn          = Request("disp_yn");
        $ex_pop_yn        = Request("ex_pop_yn");
        $sch_rel_cnt      = Request("sch_rel_cnt", 2);
        $sch_cnt_fr       = Request("sch_cnt_fr");
        $sch_cnt_to       = Request("sch_cnt_to");

        $ord_field        = Request("ord_field");
        $ord              = Request("ord");
        $limit            = $req->input("limit", 100);

        $page = Request("page", 1);
        if ($page < 1 or $page == "") $page = 1;

        $where = "";

        if ($kwd != "")    $where .= " and s.kwd like '$kwd%'";
        if ($pv_1m_fr != "")    $where .= " and s.pv_1m >= $pv_1m_fr ";
        if ($pv_1m_to != "") $where .= " and s.pv_1m <= $pv_1m_to ";

        if ($disp_yn != "")    $where .= " and s.disp_yn = '$disp_yn' ";
        if ($ex_pop_yn != "")    $where .= " and s.ex_pop_yn = '$ex_pop_yn' ";

        if ($sch_cnt_fr != "") $where .= " and s.sch_cnt_1m >= '$sch_cnt_fr' ";
        if ($sch_cnt_to != "") $where .= " and s.sch_cnt_1m <= '$sch_cnt_to' ";

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            // 갯수 얻기
            $sql =
                /** @lang text */
            " 
            select count(*) as total
            from  search s
            where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $total = $row->total;
            if ($total > 0) {
                $page_cnt = (int)(($total - 1) / $page_size) + 1;
            }
        }

        $sql = "
            select
                '' as blank,
                s.kwd,s.rank,s.rank_inc,
                s.pv_1d,s.pv_1w,s.pv_1m, pv,
                s.sch_cnt_1d,s.sch_cnt_1w,s.sch_cnt_1m,
                s.tags,s.mpv,s.point,
                s.disp_yn,
                s.ex_pop_yn,
                
                ( select group_concat(rkwd order by pv desc) from search_rel where kwd = s.kwd and pv >= $sch_rel_cnt ) as kwd_rel,
                s.rt, s.ut
            from  search s
            where 1=1 $where
            order by $ord_field $ord
            limit $startno,$page_size
        ";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function edit_mpv()
    {
        $datas = Request('datas', []);
        // $user = [
        //     'id' => Auth('head')->user()->id,
        //     'name' => Auth('head')->user()->name
        // ];

        if (count($datas) === 0) {
            return response()->json(null, 400);
        }

        foreach ($datas as $data) {
            $mpv = Lib::uncm($data['mpv']);
            $kwd = $data['kwd'];
            $sql = "update search set mpv = '$mpv' where kwd = '$kwd'";

            DB::update($sql);
        }

        return response()->json(null, 204);
    }

    public function edit_disp()
    {
        $kwds = Request('datas', []);
        $yn   = Request("yn", "N");

        if (!in_array($yn, array("Y", "N"))) {
            return response()->json(null, 400);
        }

        foreach ($kwds as $kwd) {
            $sql = "update search set disp_yn = '$yn' where kwd = '$kwd'";

            DB::update($sql);
        }

        return response()->json(null, 204);
    }

    public function edit_ex_pop()
    {
        $kwds = Request('datas', []);
        $yn   = Request("yn", "N");

        if (!in_array($yn, array("Y", "N"))) {
            return response()->json(null, 400);
        }

        foreach ($kwds as $kwd) {
            $sql = "update search set ex_pop_yn = '$yn' where kwd = '$kwd'";

            DB::update($sql);
        }

        return response()->json(null, 204);
    }

    public function edit_synonym()
    {
        $kwd    = Request("kwd");
        $synonym    = Request("synonym");

        $sql = "
            select count(*) as cnt from search where kwd = '$kwd'
        ";

        $cnt = DB::selectOne($sql)->cnt;

        if ($cnt == 0) {
            $sql = "insert into search ( kwd, synonym, rt, ut ) values ( '$kwd', '$synonym' , now(), now() )";
            DB::insert($sql);
        } else {

            $sql = "
                update search set
                    synonym = '$synonym',
                    rt = now()
                where kwd = '$kwd'
            ";
            DB::update($sql);
        }

        return response()->json(null, 204);
    }

    public function edit_point(Request $request)
    {
        $id = Auth('head')->user()->id;
        $pv = Request("pv", 1);
        $tags = Request("tags", 1);
        $mpv = Request("mpv", 1);

        // Save Point Function
        try {
            DB::beginTransaction();

            DB::table('search_func')->truncate();

            $sql = "insert into search_func ( pv, tags, mpv,admin_id, rt, ut ) values ( '$pv','$tags','$mpv','$id', now(),now() )";
            DB::insert($sql);

            $sql = "select * from  search_func";
            $row = DB::selectOne($sql);

            $pv = $row->pv;
            $tags = $row->tags;
            $mpv = $row->mpv;

            $sql = "
                update search 
                    set point = $pv * ifnull(pv_1m,0) + $tags * ifnull(tags,0) + $mpv * ifnull(mpv,0)
            ";
                
            DB::update($sql);
            DB::commit();
            return response()->json(null, 204);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function edit_rank()
    {
        try {
            DB::beginTransaction();

            $sql = "
                create temporary table _tmp_search (
                    idx int auto_increment primary key,
                    kwd varchar(100),
                    rank int,
                    index ( kwd )
                )
            ";
            DB::statement($sql);

            $sql = "
                insert into _tmp_search ( kwd, rank ) select kwd,rank from search order by point desc
            ";

            DB::insert($sql);

            $sql = "
                update search s inner join _tmp_search a on s.kwd = a.kwd set
                    s.rank = a.idx,
                    rank_inc_day = if(date_format(now(),'%Y%m%d') > ifnull(s.rank_inc_day,''),date_format(now(),'%Y%m%d'),rank_inc_day),
                    rank_inc = if(date_format(now(),'%Y%m%d') >  ifnull(s.rank_inc_day,''),if( s.rank > 0,s.rank - a.idx,0),s.rank_inc),
                    ut = now()
                    where date_format(now(),'%Y%m%d') > ifnull(s.rank_inc_day,'')
            ";
            DB::update($sql);

            // dd($aa);

            DB::commit();
            // return response()->json(null, 204);
            return response()->json(["msg" => "성공"], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['msg' => $e->getMessage()], 500);
        }
    }
}
