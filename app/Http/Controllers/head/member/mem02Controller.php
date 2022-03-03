<?php

namespace App\Http\Controllers\head\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;

class mem02Controller extends Controller
{
    public function index($type='') {
        //taxpayer_yn
        $sql = /** @lang text */
            " 
            select group_no as id, group_nm as val from user_group order by group_no 
         ";
        $groups = DB::select($sql);

        $values = [
            "groups" => $groups
        ];


        return view( Config::get('shop.head.view') . '/member/mem02', $values);
    }

    public function show($type = '', $user_id="", Request $req) {
        if ($type == 'add')
            $values = $this->__getShowAddData();
        else if ($type == 'edit')
            $values = $this->__getShowEditData($user_id);

        // 회원그룹 콤보
        $sql = " select group_no as id, group_nm as val from user_group order by group_no ";
        $values['groups'] = DB::select($sql);


        $values['type'] = $type;
        $values['admin_id'] = Auth('head')->user()->id;

        // dd($values);
        return view( Config::get('shop.head.view') . '/member/mem02_show', $values);
    }

    public function search(Request $req) {

        $cond = $this->get_condition($req);

        $where = $cond[0];
        $order_by = $cond[1];
        $join = $cond[2];

        $page = $req->input("page", 1);
        $page_size	= $req->input("limit", 100);

        if ($page < 1 or $page == "") $page = 1;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $sql = /** @lang text */
                "
                select
                count(*) as total
                from member_inactive a
                where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $total = $row->total;

            // 페이지 얻기
            $page_cnt=(int)(($total - 1)/$page_size) + 1;
            $startno = ($page - 1) * $page_size;
            //$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page, "page_total"=>count);
        } else {
            $startno = ($page - 1) * $page_size;
            //$arr_header = null;
        }

        $sql = /** @lang text */
            "
			select
				'' as chk, a.user_id, a.name as user_nm, a.regdate, a.lastdate,
				a.visit_cnt, a.auth_type, f.code_val as auth_type_nm,
				a.auth_yn, a.mobile_chk, a.yn, a.site
			from member_inactive a
				left outer join code f on f.code_kind_cd = 'G_AUTH_TYPE' and a.auth_type = f.code_id
				left outer join member_stat e on a.user_id = e.user_id
				$join
			where 1=1 $where
			$order_by
			limit $startno, $page_size

		";
        $arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page);


        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $result
        ]);
    }

    public function active(Request $req){

        $user_ids	= Request("user_ids");

        try {
            DB::beginTransaction();

            for($i = 0; $i<count($user_ids); $i++) {
                $user_id = $user_ids[$i];

                $sql = /** @lang text */
                    "
                    update member, member_inactive
                    set
                        member.name = member_inactive.name,
                        member.name_eng = member_inactive.name_eng,
                        member.jumin = member_inactive.jumin,
                        member.jumin1 = member_inactive.jumin1,
                        member.jumin2 = member_inactive.jumin2,
                        member.email = member_inactive.email,
                        member.email_chk = member_inactive.email_chk,
                        member.zip = member_inactive.zip,
                        member.addr = member_inactive.addr,
                        member.addr2 = member_inactive.addr2,
                        member.phone = member_inactive.phone,
                        member.mobile = member_inactive.mobile,
                        member.job = member_inactive.job,
                        member.married_yn = member_inactive.married_yn,
                        member.married_date = member_inactive.married_date,
                        member.rmobile = member_inactive.rmobile,
                        member.mobile_chk = member_inactive.mobile_chk,
                        member.yyyy_chk = member_inactive.yyyy_chk,
                        member.yyyy = member_inactive.yyyy,
                        member.mm = member_inactive.mm,
                        member.dd = member_inactive.dd,
                        member.opt = member_inactive.opt,
                        member.out_yn = member_inactive.out_yn,
                        member.enjumin = member_inactive.enjumin,
                        member.anniv_date = member_inactive.anniv_date,
                        member.anniv_type = member_inactive.anniv_type,
                        member.interest = member_inactive.interest,
                        member.memo = member_inactive.memo,
                        member.pwd_reset_yn = member_inactive.pwd_reset_yn,
                        member.recommend_id = member_inactive.recommend_id,
                        member.site = member_inactive.site,
                        member.lastdate = now()
                    where 
                        member.user_id = :member_user_id
                        and member_inactive.user_id = :member_active_user_id
                ";
                DB::update($sql, [
                    'member_user_id' => $user_id,
                    'member_active_user_id' => $user_id
                ]);
                DB::table('member_inactive')->where('user_id', $user_id)->delete();
            }

            DB::commit();
            $code = 200;
            $msg = "";

        } catch(Exception $e){
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }
        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);

    }

    private function get_condition(Request $req) {

        $user_id	= Request("user_ids");
        $name		= Request("name");
        $user_group	= Request("user_group");

        $sdate		= Request("sdate");
        $edate		= Request("edate");
        $last_sdate	= Request("last_sdate");
        $last_edate	= Request("last_edate");

        $where = "";

        if($user_id !=""){
            $ids = explode(",",$user_id);
            if(count($ids) > 1){
                if(count($ids) > 300) array_splice($ids,300);
                for($i=0;$i<count($ids);$i++){
                    $ids[$i] = sprintf("'%s'",$ids[$i]);
                }
                $in_ids = join(",",$ids);
                $where .= " and a.user_id in ( $in_ids ) ";
            } else {
                $where .= " and a.user_id = '$user_id' ";
            }
        }

        if($name != "")				$where .= " and a.name = '$name' ";

        if($sdate != "")			$where .= " and a.regdate >= '$sdate' ";
        if($edate != "")			$where .= " and a.regdate < DATE_ADD('$edate', INTERVAL 1 DAY) ";
        if($last_sdate != "")		$where .= " and a.lastdate >= '$last_sdate' ";
        if($last_edate != "")		$where .= " and a.lastdate < DATE_ADD('$last_edate', INTERVAL 1 DAY) ";


        $join = "";
        if($user_group != ""){
            $join = " inner join user_group_member b on a.user_id = b.user_id ";
            $join .= " inner join user_group c on b.group_no = c.group_no and c.group_no in ($user_group) ";
        }

        $page = Request("page",1);
        if ($page < 1 or $page == "") $page = 1;

        $page_size	= Request("limit", 100);
        $ord_field	= Request("ord_field","a.user_id");
        $ord		= Request("ord","asc");

        $str_order_by = sprintf(" order by %s %s ",$ord_field,$ord);
        return [$where, $str_order_by, $join];
    }


}
