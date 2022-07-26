<?php

namespace App\Components;

use Illuminate\Support\Facades\DB;
use Exception;

class SLib
{
    public static function getCodes($code_kind,$code_ids = array()){
        $query = DB::table("code")
            ->where("code_kind_cd", "=", $code_kind)
            ->where("use_yn", "=", "Y")
            ->where("code_id", "<>", "K");
        foreach($code_ids as $code_id => $sign){
            $query = $query->where("code_id",$sign,$code_id);
        }
        return $query->orderBy("code_seq")->get();
    }

    public static function getCodesValue($code_kind, $code_id,$value = 'code_val'){
        return DB::table("code")
            ->where("code_kind_cd","=",$code_kind)
            ->where("use_yn","=","Y")
            ->where("code_id","<>","K")
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

    public static function getUsedSaleKinds($sale_kind_id = '')
    {
        $where = "";
        if ($sale_kind_id != "") $where .= " and s.sale_kind = '" . Lib::quote($sale_kind_id) . "' ";
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

}