<?php

namespace App\Components;

use Illuminate\Support\Facades\DB;
use Exception;
class SLib
{
    public static function getCodes($code_kind,$code_ids = array()){
        $query = DB::table("code")
            ->where("code_kind_cd", "=", $code_kind)
            ->where("use_yn", "=", "Y");
            //->where("code_id", "<>", "K");
        foreach($code_ids as $code_id => $sign){
            $query = $query->where("code_id",$sign,$code_id);
        }
        return $query->orderBy("code_seq")->get();
    }

    public static function getCodesValue($code_kind, $code_id,$value = 'code_val'){
        return DB::table("code")
            ->where("code_kind_cd","=",$code_kind)
            ->where("use_yn","=","Y")
            //->where("code_id","<>","K")
            ->where("code_id", $code_id)
            ->value($value);
    }

    public static function getOrdStates(){
        return SLib::getCodes('G_ORD_STATE');
    }

    public static function getItems(){
        return DB::table("opt")
            ->select("opt_kind_cd as cd", "opt_kind_nm as val")
            ->where("opt_id","=","K")
            ->where("use_yn","=","Y")
            ->orderBy("opt_kind_nm")->get();
    }

    public static function getClmStates(){
        return SLib::getCodes('G_CLM_STATE');
    }

    public static function getMDs(){
        return DB::table('mgr_user')->where("md_yn",'=','Y')->orderBy("name")->get();
    }

    public static function getSalePlaces($site_yn = '')
    {
        //21-05-03 ceduce 본사 온라인 사이트 판매처 정보 값 검색 $site_yn 변수 등록

        return DB::table('company')
            ->where("com_type",'=','4')
            ->where("use_yn","=","Y")
            ->where(function($query) use ($site_yn){
                if($site_yn != ""){
                    $query->where("site_yn","=",$site_yn);
                }
            })
            ->orderBy("com_nm")->get();
    }

    public static function getValidStoreGrades()
    {
        $sql = "
            select
                grade_cd as code_id, name as code_val, seq, sdate, edate
            from store_grade sg
            where sg.sdate <= date_format(now(), '%Y-%m') and sg.edate >= date_format(now(), '%Y-%m')
            order by sg.seq asc
        ";
        return DB::select($sql);
    }

    public static function getBanks()
    {
        return DB::table('code')
            ->where("code_kind_cd","=",'BANK')
            ->where("use_yn","=","Y")
            ->where("code_id","<>","K")
            ->orderBy("code_seq")
            ->select(DB::raw("concat(code_val,'_',ifnull(code_val2, '')) as name"), DB::raw("concat(code_val,' [',ifnull(code_val2, ''),']') as value"))->get();
    }

    public static function getDlvSeries($id = "")
    {
        $query = DB::table('order_dlv_series');
        if($id !== ""){
            $query->where("com_id",$id);
        } else {
            $query->where(DB::raw("ifnull(com_id,'')"),$id);
        }
        return $query->orderBy("dlv_series_no","desc")
            ->limit(30)
            ->select(DB::raw("dlv_series_no as name"), DB::raw("dlv_series_nm as value"))->get();
    }

    public static function getStoreTypes()
    {
        $sql = "
			select *
			from code
			where
				code_kind_cd = 'store_type' and use_yn = 'Y' order by code_seq
		";
		$store_types = DB::select($sql);
        return $store_types;
    }

    public static function getUsedSaleKinds($sale_kind_ids = "")
    {
        $where = "";
        if (is_array($sale_kind_ids)) {
            if (count($sale_kind_ids) == 1 && $sale_kind_ids[0] != "") {
                $where .= " and s.sale_kind = '" . Lib::quote($sale_kind_ids[0]) . "' ";
            } else if (count($sale_kind_ids) > 1) {
                $where .= " and s.sale_kind in (" . join(",", $sale_kind_ids) . ") ";
            }
        } else if ($sale_kind_ids != "") {
			$where .= " and s.sale_kind = '" . Lib::quote($sale_kind_ids) . "' ";
		}

        $sql = "
            select
                s.sale_kind as code_id, c.code_val as code_val, s.idx as sale_type_cd,
                s.sale_type_nm, s.sale_apply, s.amt_kind, s.sale_amt, s.sale_per, s.use_yn,
                (
                    select count(ss.idx)
                    from sale_type_store ss
                    where ss.sale_type_cd = s.idx
                        and ss.use_yn = 'Y'
                ) as store_cnt
            from sale_type s
                inner join code c on c.code_kind_cd = 'SALE_KIND' and c.code_id = s.sale_kind
                where 1=1 $where
            order by sale_kind
        ";
        $sale_kinds = DB::select($sql);
        return $sale_kinds;
    }

    /* 매장특성정보 */
    public static function getStoreProp($no)
    {
        $sql = "
            select
                s.manage_type, s.account_yn, s.exp_manage_yn, s.priority, s.competitor_yn, s.pos_yn, s.ostore_stock_yn, s.sale_dist_yn, s.rt_yn, s.open_month_stock_yn, s.point_in_yn, s.sale_place_match_yn
            from store s
            where s.store_cd = :store_cd
        ";
        return DB::selectOne($sql, ['store_cd'=>$no]);
    }

    /* 메뉴정보 */
    public static function getLnbs($type)
    {
        if($type == 'store') {
            $query = DB::table('store_controller as lnb');
            $main_table = 'store_controller';
        } else if ($type == 'shop') {
            $query = DB::table('shop_controller as lnb');
            $main_table = 'shop_controller';
        } else if ($type == 'head') {
            $query = DB::table('handle_controller as lnb');
            $main_table = 'handle_controller';
        }
        $query = $query->selectRaw("
            lnb.*,
            (select entry from $main_table where menu_no = lnb.entry and lev > 1) as main_no
        ");
        $query = $query->where('menu_no', '>', 1);
        $query = $query->where('is_del', 0);
        $query = $query->where('state', '>=', 0);
        $query = $query->orderby('entry', 'asc');
        $query = $query->orderbyRaw('seq is null asc');
        $query = $query->orderby('seq', 'asc');
        return $query->get();
    }

    /* 특정권한 메뉴정보 */
    public static function getSpecialGroupLnbs($type, $id)
    {
        $menu_table = null;
        $main_table = null;

        if($type == 'store') {
            $menu_table = 'store_controller as lnb';
            $main_table = 'store_controller';
        } else if ($type == 'shop') {
            $menu_table = 'shop_controller as lnb';
            $main_table = 'shop_controller';
        } else if ($type == 'head') {
            $menu_table = 'handle_controller as lnb';
            $main_table = 'handle_controller';
        }

        $sql = "
            select 
                lnb.*,
                (select entry from $main_table where menu_no = lnb.entry and lev > 1) as main_no 
            from $menu_table
            where 
                lnb.menu_no  > 1
                and lnb.is_del = 0
                and lnb.state >= 0
                and lnb.menu_no in (
                    select menu_no from mgr_group_menu_role mgmr where group_no = '$id'
                )
            order by entry asc, seq asc
        ";

        return DB::select($sql);
    }

    public static function getLogisticsGroupYn($id, $group_code) {

        $logistics_sql = "
            select 
                group_no
            from 
                mgr_group_menu_exception
            where
                group_no in (
                    select group_no from mgr_user_group mug where id = '$id'
                )   
        ";

        $group_code_sql = "
            select 
                group_no
            from 
                mgr_button_into_menu_exception
            where
                group_code = '$group_code'
        ";
        
        $group_code = DB::selectOne($group_code_sql);
        $results = DB::select($logistics_sql);
        
        if(count($results) > 0) {
            foreach($results as $result) {
                if($result->group_no === $group_code->group_no) {
                    return 'Y';
                }
            }
        }

        return 'N';
    }

    //판매채널 가져오는 부분
    public static function getStoreChannel()
    {
        $sql = "
			select
				store_channel
				, store_channel_cd
				, use_yn
			from store_channel
			where dep = 1 and use_yn = 'Y'
			order by seq
		";
        return DB::select($sql);
    }

    //매장구분 가져오는 부분
    public static function getStoreKind()
    {
        $sql = "
            select
                store_kind
                , store_kind_cd
                , use_yn
            from store_channel
            where dep = 2 and use_yn = 'Y'
        ";
        return DB::select($sql);
    }

}
