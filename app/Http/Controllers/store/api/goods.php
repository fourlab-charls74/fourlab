<?php

namespace App\Http\Controllers\store\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use App\Exports\ExcelViewExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Conf;
use Carbon\Carbon;
use PDO;

class goods extends Controller
{
	public function show(Request $request)
	{
        $sdate		= now()->sub(1, 'week')->format('Y-m-d');
        $store_cd   = $request->input('store_cd', '');
        $storage_cd   = $request->input('storage_cd', '');
        $include_not_match = $request->input('include_not_match', '');

        $store = null;
        $storage = null;

        // $event_cds	= [];
        // $sell_types	= []; //판매유형
        // $code_kinds	= [];

        $conf = new Conf();
        $domain		= $conf->getConfigValue("shop", "domain");

        if ($store_cd != '') {
            if ($store_cd == 'ALL') $store = (object)['store_cd' => 'ALL', 'store_nm' => '전체 매장'];
            else $store = DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', $store_cd)->first();
        }
        if ($storage_cd != '') {
            if ($storage_cd == 'ALL') $storage = (object)['storage_cd' => 'ALL', 'storage_nm' => '전체 창고'];
            $storage = DB::table('storage')->select('storage_cd', 'storage_nm')->where('storage_cd', $storage_cd)->first();
        }

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
            'domain'		=> $domain,
            'style_no'		=> "",
            'store'         => $store,
            'storage'       => $storage,
            'include_not_match' => $include_not_match,
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
            'items'			=> SLib::getItems(),
			// 'event_cds'		=> $event_cds,
			// 'code_kinds'	    => $code_kinds,
            // 'com_types'     => SLib::getCodes('G_COM_TYPE'),
		];

		return view(Config::get('shop.store.view') . '/common/goods_search', $values);
	}

	public function file_search()
	{
		$values = [];
		return view(Config::get('shop.store.view') . '/common/goods_file_search', $values);
	}

	public function search(Request $request)
	{
        $store_cd = $request->input('store_cd', '');
        $storage_cd = $request->input('storage_cd', '');
        $include_not_match = $request->input('include_not_match', ''); // 비매칭상품 포함검색 여부
        $ext_zero_qty = $request->input("ext_zero_qty", '');

		$page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $prd_cd = $request->input("prd_cd", "");
        $style_no = $request->input("style_no");
        $style_nos = $request->input("style_no", '');
        $goods_no = $request->input("goods_no");
        $goods_nos = $request->input('goods_nos', '');       // 상품번호 textarea
        $item = $request->input("item");
        $brand_nm = $request->input("brand_nm");
        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $goods_nm_eng = $request->input("goods_nm_eng");
        $prd_cd_range_text = $request->input("prd_cd_range", '');
        
        $com_id = $request->input("com_cd");

        if ($com_id != null) {
            $sql = "
                select
                    com_type
                from company
                where com_id = '$com_id'
            ";
            $com_type = DB::selectOne($sql);
        }
       

        // $goods_stat = $request->input("goods_stat");
        // $head_desc = $request->input("head_desc");
        // $ad_desc = $request->input("ad_desc");

        $ord = $request->input('ord', 'desc');
        $ord_field = $request->input('ord_field', 'pc.rt');
        $orderby = sprintf("order by %s %s, pc.prd_cd", $ord_field, $ord);
        
        $where = "";
        if($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$where .= ")";
		}
        // if ($style_no != "") $where .= " and g.style_no like '" . Lib::quote($style_no) . "%' ";
        if ($item != "") $where .= " and g.opt_kind_cd = '" . Lib::quote($item) . "' ";
        if ($brand_cd != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        } else if ($brand_cd == "" && $brand_nm != "") {
            $where .= " and g.brand = '" . Lib::quote($brand_cd) . "' ";
        }
        if ($goods_nm != "") $where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
        if ($goods_nm_eng != "") $where .= " and g.goods_nm_eng like '%" . Lib::quote($goods_nm_eng) . "%' ";

        // if ($head_desc != "") $where .= " and g.head_desc like '%" . Lib::quote($head_desc) . "%' ";
        // if ($ad_desc != "") $where .= " and g.ad_desc like '%" . Lib::quote($ad_desc) . "%' ";

        // if( is_array($goods_stat)) {
        //     if (count($goods_stat) == 1 && $goods_stat[0] != "") {
        //         $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat[0]) . "' ";
        //     } else if (count($goods_stat) > 1) {
        //         $where .= " and g.sale_stat_cl in (" . join(",", $goods_stat) . ") ";
        //     }
        // } else if($goods_stat != ""){
        //     $where .= " and g.sale_stat_cl = '" . Lib::quote($goods_stat) . "' ";
        // }

        if ($goods_nos != "") $goods_no = $goods_nos;
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);
        
        if ($style_nos != "") $style_no = $style_nos;
        $style_no = preg_replace("/\s/",",",$style_no);
        $style_no = preg_replace("/\t/",",",$style_no);
        $style_no = preg_replace("/\n/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);

        // 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        if( $style_no != "" ){
            $style_nos = explode(",",$style_no);
            if(count($style_nos) > 1){
                if(count($style_nos) > 500) array_splice($style_nos,500);
                $in_style_nos = join(",",$style_nos);
                $where .= " and g.style_no in ( $in_style_nos ) ";
            } else {
                if ($style_no != "") $where .= " and g.style_no = '" . Lib::quote($style_no) . "' ";
            }
        }

        $having = "";
        if ($ext_zero_qty == "true") {
            if ($store_cd != '' || $storage_cd != '') {
                $having .= " and (sum(ifnull(pss.wqty, 0)) != 0) ";
            }
        }

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sqls = '';
        if ($store_cd != '') $sqls = $this->_store_sql($store_cd, $where, $orderby, $limit, $having, ['com_id' => $com_id]);
        else if ($storage_cd != '') {
            if ($include_not_match === 'Y') $sqls = $this->_storage_include_not_match_sql($storage_cd, $where, $orderby, $limit, $having, ['com_id' => $com_id]);
            else $sqls = $this->_storage_sql($storage_cd, $where, $orderby, $limit, $having, ['com_id' => $com_id]);
        }
        else if ($include_not_match === 'Y') $sqls = $this->_normal_include_not_match_sql($where, $limit, ['com_id' => $com_id], $ord_field, $ord);
        else if ($com_type->com_type??'' == 6) $sqls = $this->_wonboo_sql($where, $orderby, $limit, ['com_id' => $com_id]);
        else $sqls = $this->_normal_sql($where, $orderby, $limit, ['com_id' => $com_id]);

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $row = DB::select($sqls->total_sql);
            $total = $row[0]->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sqls->sql);
        $stmt->execute();
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row["img"] != "") {
				$row["img"] = sprintf("%s%s", config("shop.image_svr"), $row["img"]);
			}
			$result[] = $row;
        }

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

    // 기본 상품검색 sql
    private function _normal_sql($where = '', $orderby = '', $limit = '', $values = [])
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $inner_where = "";
        if (isset($values['com_id'])) $inner_where .= " and g.com_id = '" . Lib::quote($values['com_id'] ?? '') . "'";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select pc.prd_cd
                from product_code pc
                    inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                where 1=1 $inner_where $where
                group by pc.prd_cd
            ) a
        ";

        $sql = "
            select
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.goods_no
                , g.style_no
                , g.opt_kind_cd
                , opt.opt_kind_nm
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , g.goods_nm
                , g.goods_nm_eng
                , pc.brand as brand_cd
                , b.brand_nm as brand
                , pc.color, c.code_val as color_nm
                , ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
                , pc.goods_opt
                , (ps.qty - ps.wqty) as s_qty
                , ps.wqty as sg_qty
                , ps.qty as total_qty
                , p.tag_price as goods_sh
                , p.price
                , p.wonga
                -- , ps.wonga
                , (100 / (p.price / (p.price - p.wonga))) as margin_rate
                , (p.price - p.wonga) as margin_amt
                , g.org_nm
                , g.com_id
                , com.com_nm
                , g.make
                , pc.rt as reg_dm
            from product_code pc
                inner join product p on p.prd_cd = pc.prd_cd
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                left outer join company com on com.com_id = g.com_id
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
            where 1=1 $inner_where $where
            group by pc.prd_cd
            $orderby
            $limit
        ";
        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 기본 상품검색 sql (비매칭상품 포함검색)
    private function _normal_include_not_match_sql($where = '', $limit = '', $values = [], $ord_field, $ord)
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $delimiter = ".";
        $ordfield = substr($ord_field, strpos($ord_field, $delimiter) + 1);
        if($ordfield == 'rt') {
            $ordfield = 'reg_dm';
        }
        $orderby2 = sprintf("order by a.%s %s, a.prd_cd asc", $ordfield, $ord);
        
        $inner_where = "";
        if (isset($values['com_id'])) $inner_where .= " and a.com_id = '" . Lib::quote($values['com_id'] ?? '') . "'";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select p.prd_cd
                    , if(pc.goods_no = 0, p.com_id, g.com_id) as com_id
                from product p
                    inner join product_code pc on pc.prd_cd = p.prd_cd
                    inner join product_stock ps on ps.prd_cd = p.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                where p.type = 'N' $where
                group by pc.prd_cd
            ) a
            where 1=1 $inner_where
        ";

        $sql = "
            select
                a.prd_cd, a.style_no, a.prd_cd_p, a.goods_no, a.brand_cd, a.color, a.size, a.goods_opt, a.opt, a.reg_dm, a.s_qty, 
                a.sg_qty, a.total_qty, a.goods_nm, a.goods_nm_eng, a.goods_sh, a.price, a.wonga, a.margin_rate, a.margin_amt, a.com_id,
                a.org_nm, a.make, a.opt_kind_cd, a.img, a.brand, a.color_nm, a.color_cd
                , com.com_nm
                , if (a.goods_no = 0, a.opt, a.opt_kind_cd) as opt_kind_cd
                , if (a.goods_no = 0, c.code_val, opt.opt_kind_nm) as opt_kind_nm
            from (
                select p.prd_cd, p.style_no
                    , pc.prd_cd_p, pc.goods_no, pc.brand as brand_cd, pc.color, pc.goods_opt, pc.opt, pc.rt as reg_dm
                    , pc.size
                    , (ps.qty - ps.wqty) as s_qty, ps.wqty as sg_qty, ps.qty as total_qty
                    , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                    , if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng
                    , p.tag_price as goods_sh
                    , p.price
                    , p.wonga as wonga
                    , (100 / (p.price / (p.price - p.wonga))) as margin_rate
                    , p.price - p.wonga as margin_amt
                    , if(pc.goods_no = 0, p.com_id, g.com_id) as com_id
                    , g.org_nm, g.make, g.opt_kind_cd
                    , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                        select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                        from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                    )) as img
                    , b.brand_nm as brand
                    , c.code_val as color_nm
                	, c.code_val2 as color_cd
                from product p
                    inner join product_code pc on pc.prd_cd = p.prd_cd
                    inner join product_stock ps on ps.prd_cd = p.prd_cd
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                where p.type = 'N' $where
                group by pc.prd_cd
            ) a
                left outer join company com on com.com_id = a.com_id
                left outer join opt opt on opt.opt_kind_cd = a.opt_kind_cd and opt.opt_id = 'K'
                left outer join code c on c.code_kind_cd = 'PRD_CD_OPT' and c.code_id = a.opt
            where 1=1 $inner_where
            $orderby2
            $limit
        ";

        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 매장별 상품검색 sql
    private function _store_sql($store_cd, $where = '', $orderby = '', $limit = '', $having = '', $values = [])
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $store_where = "";
        if ($store_cd != '' && $store_cd != 'ALL') $store_where .= " and pss.store_cd = '$store_cd' ";

        $inner_where = "";
        if (isset($values['com_id'])) $inner_where .= " and g.com_id = '" . Lib::quote($values['com_id'] ?? '') . "'";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select pc.prd_cd
                from product_code pc
                    inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                    left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd $store_where
                where 1=1 $inner_where $where
                group by pc.prd_cd
                having 1=1 $having
            ) a
        ";

        $sql = "
            select
                pc.prd_cd
                , pc.prd_cd_p as prd_cd_p
                , pc.goods_no
                , g.style_no
                , g.opt_kind_cd
                , opt.opt_kind_nm
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , g.goods_nm
                , g.goods_nm_eng
                , pc.brand as brand_cd
                , b.brand_nm as brand
                , pc.color, c.code_val as color_nm
                , c.code_val2 as color_cd
                , pc.size
                , pc.goods_opt
                , sum(ifnull(pss.qty, 0)) as store_qty
                , sum(ifnull(pss.wqty, 0)) as store_wqty
                , (ps.qty - ps.wqty) as s_qty
                , ps.wqty as sg_qty
                , ps.qty as total_qty
                , p.tag_price as goods_sh
                , p.price
                , p.wonga
                -- , ps.wonga
                , (100 / (p.price / (p.price - p.wonga))) as margin_rate
                , (p.price - p.wonga) as margin_amt
                , g.org_nm
                , g.com_id
                , com.com_nm
                , g.make
                , pc.rt as reg_dm
            from product_code pc
                inner join product p on p.prd_cd = pc.prd_cd
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                left outer join product_stock_store pss on pss.prd_cd = pc.prd_cd $store_where
                left outer join company com on com.com_id = g.com_id
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
            where 1=1 $inner_where $where
            group by pc.prd_cd
            having 1=1 $having
            $orderby
            $limit
        ";
        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 창고별 상품검색 sql
    private function _storage_sql($storage_cd, $where = '', $orderby = '', $limit = '', $having = '', $values = [])
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $storage_where = "";
        if ($storage_cd != '' && $storage_cd != 'ALL') $storage_where .= " and pss.storage_cd = '$storage_cd' ";

        $inner_where = "";
        if (isset($values['com_id'])) $inner_where .= " and g.com_id = '" . Lib::quote($values['com_id'] ?? '') . "'";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select pc.prd_cd
                from product_code pc
                    inner join product_stock ps on ps.prd_cd = pc.prd_cd
                    inner join goods g on g.goods_no = pc.goods_no
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                    left outer join product_stock_storage pss on pss.prd_cd = pc.prd_cd $storage_where
                where 1=1 $where $inner_where
                group by pc.prd_cd
                having 1=1 $having
            ) a
        ";

        $sql = "
            select
                pc.prd_cd
                , concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.goods_no
                , g.style_no
                , g.opt_kind_cd
                , opt.opt_kind_nm
                , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                    select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                    from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                )) as img
                , g.goods_nm
                , g.goods_nm_eng
                , pc.brand as brand_cd
                , b.brand_nm as brand
                , pc.color, c.code_val as color_nm
                , ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
                , pc.goods_opt
                , sum(ifnull(pss.qty, 0)) as storage_qty
                , sum(ifnull(pss.wqty, 0)) as storage_wqty
                , (ps.qty - ps.wqty) as s_qty
                , ps.wqty as sg_qty
                , ps.qty as total_qty
                , p.tag_price as goods_sh
                , p.price
                , p.wonga
                -- , ps.wonga
                , (100 / (p.price / (p.price - p.wonga))) as margin_rate
                , (p.price - p.wonga) as margin_amt
                , g.org_nm
                , g.com_id
                , com.com_nm
                , g.make
                , pc.rt as reg_dm
            from product_code pc
                inner join product p on p.prd_cd = pc.prd_cd
                inner join product_stock ps on ps.prd_cd = pc.prd_cd
                inner join goods g on g.goods_no = pc.goods_no
                inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                left outer join product_stock_storage pss on pss.prd_cd = pc.prd_cd $storage_where
                left outer join company com on com.com_id = g.com_id
                left outer join brand b on b.br_cd = pc.brand
                left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
            where 1=1 $inner_where $where
            group by pc.prd_cd
            having 1=1 $having
            $orderby
            $limit
        ";
        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 창고별 상품검색 sql (비매칭상품 포함검색)
    private function _storage_include_not_match_sql($storage_cd, $where = '', $orderby = '', $limit = '', $having = '', $values = [])
    {
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

        $storage_where = "";
        if ($storage_cd != '' && $storage_cd != 'ALL') $storage_where .= " and pss.storage_cd = '$storage_cd' ";

        $inner_where = "";
        if (isset($values['com_id'])) $inner_where .= " and g.com_id = '" . Lib::quote($values['com_id'] ?? '') . "'";

        $total_sql = "
            select count(prd_cd) as total
            from (
                select p.prd_cd
                    , if(pc.goods_no = 0, p.com_id, g.com_id) as com_id
                from product p
                    inner join product_code pc on pc.prd_cd = p.prd_cd
                    inner join product_stock ps on ps.prd_cd = p.prd_cd
                    left outer join product_stock_storage pss on pss.prd_cd = p.prd_cd $storage_where
                    left outer join goods g on g.goods_no = pc.goods_no
                where p.type = 'N' $where
                group by pc.prd_cd
                having 1=1 $having
            ) a
            where 1=1 $inner_where
        ";

        $sql = "
            select a.*, com.com_nm
                , if (a.goods_no = 0, a.opt, a.opt_kind_cd) as opt_kind_cd
                , if (a.goods_no = 0, c.code_val, opt.opt_kind_nm) as opt_kind_nm
            from (
                select p.prd_cd, p.style_no
                    , pc.prd_cd_p, pc.goods_no, pc.brand as brand_cd, pc.color, pc.goods_opt, pc.opt, pc.rt as reg_dm
                    , ifnull((
						select s.size_cd from size s
						where s.size_kind_cd = pc.size_kind
						   and s.size_cd = pc.size
						   and use_yn = 'Y'
					),'') as size
                    , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                    , if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng
                    , p.tag_price as goods_sh
                    , p.price
                    , p.wonga
                    , (100 / (p.price / (p.price - p.wonga))) as margin_rate
                    , p.price - p.wonga as margin_amt
                    , if(pc.goods_no = 0, p.com_id, g.com_id) as com_id
                    , g.org_nm, g.make, g.opt_kind_cd
                    , if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                        select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                        from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                    )) as img
                    , b.brand_nm as brand
                    , c.code_val as color_nm
                    , sum(ifnull(pss.qty, 0)) as storage_qty
					, sum(ifnull(pss.wqty, 0)) as storage_wqty
					, (ps.qty - ps.wqty) as s_qty
					, ps.wqty as sg_qty
					, ps.qty as total_qty
                from product p
                    inner join product_code pc on pc.prd_cd = p.prd_cd
                    inner join product_stock ps on ps.prd_cd = p.prd_cd
                    left outer join product_stock_storage pss on pss.prd_cd = p.prd_cd $storage_where
                    left outer join goods g on g.goods_no = pc.goods_no
                    left outer join brand b on b.br_cd = pc.brand
                    left outer join code c on c.code_kind_cd = 'PRD_CD_COLOR' and pc.color = c.code_id
                where p.type = 'N' $where
                group by pc.prd_cd
                having 1=1 $having
                $orderby
            ) a
                left outer join company com on com.com_id = a.com_id
                left outer join opt opt on opt.opt_kind_cd = a.opt_kind_cd and opt.opt_id = 'K'
                left outer join code c on c.code_kind_cd = 'PRD_CD_OPT' and c.code_id = a.opt
            where 1=1 $inner_where
            $limit
        ";

        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    // 원부자재 상품검색
    private function _wonboo_sql($where = '', $orderby = '', $limit = '', $values = [])
    {
        // dd('원부자재상품검색');
        $cfg_img_size_real = "a_500";
        $cfg_img_size_list = "s_50";

       
        $inner_where = "";
        if (isset($values['com_id'])) $inner_where .= " and p.com_id = '" . Lib::quote($values['com_id'] ?? '') . "'";

        $total_sql = "
            select count(p.prd_cd) as total
            from product p
                inner join product_code pc on p.prd_cd = pc.prd_cd
                left outer join company com on com.com_id = p.com_id
                left outer join product_stock_storage pss on p.prd_cd = pss.prd_cd
				left outer join product_stock_store pss2 on p.prd_cd = pss2.prd_cd
                left outer join product_image i on p.prd_cd = i.prd_cd
                left outer join code c on c.code_id = pc.opt and c.code_kind_cd = 'PRD_MATERIAL_OPT'
                left outer join code d on d.code_id = pc.brand and d.code_kind_cd = 'PRD_MATERIAL_TYPE'
            where 1=1 and p.use_yn = 'Y' $inner_where
        ";

        $sql = "
            select 
                p.prd_cd
                , pc.goods_no as goods_no
                , pc.brand as brand
                , pc.prd_cd_p as prd_cd_p
                , d.code_val as brand
                , c.code_val as opt_kind_nm
                , pc.color as color
                , pc.size as size
                , pc.goods_opt
                , p.prd_nm as goods_nm
                , p.prd_nm_eng as goods_nm_eng
                , p.tag_price as goods_sh
                , p.price
                , p.wonga
                , com.com_nm as com_nm
                , p.rt as reg_dm
                , i.img_url as img
                , ifnull(pss.wqty, 0) as sg_qty
				, sum(ifnull(pss2.wqty, 0)) as s_qty
                , p.com_id as com_id
            from product p
                inner join product_code pc on p.prd_cd = pc.prd_cd
                left outer join company com on com.com_id = p.com_id
                left outer join product_stock_storage pss on p.prd_cd = pss.prd_cd
				left outer join product_stock_store pss2 on p.prd_cd = pss2.prd_cd
                left outer join product_image i on p.prd_cd = i.prd_cd
                left outer join code c on c.code_id = pc.opt and c.code_kind_cd = 'PRD_MATERIAL_OPT'
                left outer join code d on d.code_id = pc.brand and d.code_kind_cd = 'PRD_MATERIAL_TYPE'
            where 1=1 and p.use_yn = 'Y' $inner_where
            group by pc.prd_cd
            $limit
        ";

        return (object)['total_sql' => $total_sql, 'sql' => $sql];
    }

    /*********************************************************************************/
    /*************************** 상품코드(바코드) 검색 관련 **************************/
    /*********************************************************************************/

    const Conds = [
        'brand' => 'BRAND',
        'year' => 'YEAR',
        'season' => 'SEASON',
        'gender' => 'GENDER',
        'item' => 'ITEM',
        'opt' => 'OPT'
    ];

    public function search_product_conditions()
    {
        $result = [];

        foreach(self::Conds as $key => $cond_cd)
        {
            $sql = "
                select code_id, code_val
                from code
                where code_kind_cd = 'PRD_CD_$cond_cd'
                order by code_seq
            ";

            if($key == 'brand') {
                $sql = "
                    select br_cd as code_id, brand_nm as code_val
                    from brand
                    where use_yn = 'Y'
                        and br_cd != ''
                    order by field(br_cd, 'F') desc, brand_nm asc
                ";
            }

            if($key == 'item') {
                $sql = "
                select 
                    code_id, code_val
                from code 
                where code_kind_cd = 'prd_cd_item'
                order by code_val asc
                ";
            }
            $result[$key] = DB::select($sql);
        }

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => 1,
            ],
            "body" => $result
        ]);
    }

    public function search_prdcd(Request $request)
    {
        $prd_cd = $request->input('prd_cd', '');
        $goods_nm = $request->input('goods_nm', '');

        $brand = $request->input('brand', []);
        $brand_contain = $request->input('brand_contain', '');
        $year = $request->input('year', []);
        $year_contain = $request->input('year_contain', '');
        $season = $request->input('season', []);
        $season_contain = $request->input('season_contain', '');
        $gender = $request->input('gender', []);
        $gender_contain = $request->input('gender_contain', '');
        $items = $request->input('item', []);
        $items_contain = $request->input('item_contain', '');
        $opt = $request->input('opt', []);
        $opt_contain = $request->input('opt_contain', '');
        $match = $request->input('match');

        $match_yn = $request->input('match_yn');


        $page = $request->input('page', 1);
        $where = "";

        if($prd_cd != '') $where .= " and pc.prd_cd like '%$prd_cd%'";

        if ($match == 'true') {
            if($goods_nm != '') $where .= " and p.prd_nm like '%$goods_nm%'";
        } else {
            if($goods_nm != '') $where .= " and g.goods_nm like '%$goods_nm%'";
        }

        //상품 매칭
        if($match != 'false') $where .= "and pc.type = 'N'";

        if($match != 'false') {
            if($match_yn == 'Y') 	$where .= " and p.match_yn = 'Y'";
		    if($match_yn == 'N') 	$where .= " and p.match_yn = 'N'";
        }

        if($match_yn == 'Y') 	$where .= " and p.match_yn = 'Y'";
		if($match_yn == 'N') 	$where .= " and p.match_yn = 'N'";


        foreach(self::Conds as $key => $value)
        {
            if($key === 'item') $key = 'items';
            if(count(${ $key }) > 0)
            {
                $where .= ${ $key . '_contain' } == 'true' ? " and (1!=1" : " and (1=1";

                $col = $key === 'items' ? 'item' : $key;
                foreach(${ $key } as $item) {
                    if(${ $key . '_contain' } == 'true')
                        $where .= " or pc.$col = '$item'";
                    else
                        $where .= " and pc.$col != '$item'";
                }
                $where .= ")";
            }
        }

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;


        $sql = "
            select 
                *
            from product_code
            where prd_cd = '$prd_cd'
        ";

        if ($match == 'false') {
            $sql = "
                select 
                pc.prd_cd
                , pc.goods_no
                , if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
                , pc.goods_opt
                , pc.prd_cd_p as prd_cd1,
                pc.color, p.match_yn,
				pc.size
                , pc.size_kind
                from product_code pc
                    left outer join goods g on g.goods_no = pc.goods_no
                    inner join product p on pc.prd_cd = p.prd_cd
                where 1=1
                $where
            ";
        } else {
            // 상품매칭
            $sql = "
                select pc.prd_cd, p.prd_nm, pc.goods_no, pc.goods_opt, concat(pc.brand,pc.year, pc.season, pc.gender, pc.item, pc.opt, pc.seq) as prd_cd1,
                pc.color, p.match_yn, pc.rt
                , pc.size
                , pc.size_kind
                from product_code pc
                    inner join product p on pc.prd_cd = p.prd_cd
                where 1=1 $where
                order by pc.rt desc
            ";
        }


        $result = DB::select($sql);

        // if ($page == 1) {
        //     $sql = "
        //         select count(*) as total
        //         from product_code p
        //             inner join goods g on g.goods_no = p.goods_no
        //         where 1=1 $where
        //     ";
        //     $row = DB::select($sql);
        //     $total = $row[0]->total;
        //     $page_cnt = (int)(($total - 1) / $page_size) + 1;
        // }

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
                // "page" => $page,
                // "page_cnt" => $page_cnt,
                // "page_total" => count($result)
            ],
            "body" => $result
        ]);
    }

    /** 코드일련으로 해당 상품의 컬러옵션 리스트 조회 */
    public function search_color(Request $request)
    {
        $prd_cd_p = $request->input('prd_cd_p', '');
        $colors = [];

        if ($prd_cd_p != '') {
            $sql = "
                select p.color, c.code_val as color_nm
                from product_code p
                    inner join code c on c.code_kind_cd = 'PRD_CD_COLOR' and c.code_id = p.color
                where p.prd_cd like '$prd_cd_p%'
                group by p.color
            ";
            $colors = DB::select($sql);
        }

        return response()->json(['colors' => $colors]);
    }

    /** 코드일련 검색 */
    public function search_prdcd_p(Request $request)
    {
        $prd_cd_p		= $request->input('prd_cd', '');
		$goods_nm		= $request->input('goods_nm', '');
		$goods_nm_eng	= $request->input('goods_nm_eng', '');
		$style_no		= $request->input('style_no', '');

        $brand = $request->input('brand', []);
        $brand_contain = $request->input('brand_contain', '');
        $year = $request->input('year', []);
        $year_contain = $request->input('year_contain', '');
        $season = $request->input('season', []);
        $season_contain = $request->input('season_contain', '');
        $gender = $request->input('gender', []);
        $gender_contain = $request->input('gender_contain', '');
        $items = $request->input('item', []);
        $items_contain = $request->input('item_contain', '');
        $opt = $request->input('opt', []);
        $opt_contain = $request->input('opt_contain', '');

        $where = "";
        $having = "";

        if ($prd_cd_p != '')		$having .= " and prd_cd_p like '$prd_cd_p%' ";
        if ($goods_nm != '')		$having .= " and goods_nm like '%$goods_nm%' ";
		if ($goods_nm_eng != '')	$having .= " and goods_nm_eng like '%$goods_nm_eng%' ";
		if ($style_no != '')		$having .= " and style_no like '%$style_no%' ";
        
        foreach(self::Conds as $key => $value)
        {
            if($key === 'item') $key = 'items';
            if(count(${ $key }) > 0)
            {
                $where .= ${ $key . '_contain' } == 'true' ? " and (1!=1" : " and (1=1";

                $col = $key === 'items' ? 'item' : $key;
                foreach(${ $key } as $item) {
                    if(${ $key . '_contain' } == 'true')
                        $where .= " or pc.$col = '$item'";
                    else
                        $where .= " and pc.$col != '$item'";
                }
                $where .= ")";
            }
        }

        $sql = "
            select
                concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
                , pc.prd_cd
                , pc.goods_no
                , if(ifnull(p.prd_nm, '') != '',p.prd_nm, g.goods_nm) as goods_nm
                , if(ifnull(p.prd_nm_eng, '') != '', p.prd_nm_eng, g.goods_nm_eng ) as goods_nm_eng
                , if(ifnull(p.style_no, '') != '', p.style_no, g.style_no) as style_no
            from product_code pc
            inner join product p on p.prd_cd = pc.prd_cd
            left outer join goods g on g.goods_no = pc.goods_no
            where 1=1 $where
            group by prd_cd_p
            having 1=1 $having
            order by 
                field(pc.brand, 'F') desc,
                pc.year desc,
                prd_cd_p
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
            ],
            "body" => $result
        ]);
    }

    /*********************************************************************************/
    /****************************** 원부자재코드 검색 관련 ****************************/
    /********************************************************************************/

    const Conds_sub = [
        'brand' => 'BRAND',
        'year' => 'YEAR',
        'season' => 'SEASON',
        'gender' => 'GENDER',
        'item' => 'ITEM',
        'opt' => 'OPT'
    ];

    public function search_product_sub_conditions()
    {
        $result = [];

        foreach (self::Conds_sub as $key => $cond_cd) {
            $sql = "
                select code_id, code_val
                from code
                where code_kind_cd = 'PRD_CD_$cond_cd'
                order by code_seq
            ";

            if($key == 'brand') {
                $sql = "
                    select code_id, code_val
                    from code
                    where code_kind_cd = 'PRD_MATERIAL_TYPE'
                ";
            }
			
			if ($key == 'opt') {
				$sql = "
					select code_id, code_val
					from code
					where code_kind_cd = 'PRD_MATERIAL_OPT'
				";
			}
			
            $result[$key] = DB::select($sql);
        }

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => 1,
            ],
            "body" => $result
        ]);
    }

    public function search_prdcd_sub(Request $request)
    {
        $prd_cd = $request->input('prd_cd_sub', '');
        $goods_nm = $request->input('goods_nm_sub', '');

        $brand = $request->input('brand', []);
        $brand_contain = $request->input('brand_contain', '');
        $year = $request->input('year', []);
        $year_contain = $request->input('year_contain', '');
        $season = $request->input('season', []);
        $season_contain = $request->input('season_contain', '');
        $gender = $request->input('gender', []);
        $gender_contain = $request->input('gender_contain', '');
        $items = $request->input('item', []);
        $items_contain = $request->input('item_contain', '');
        $opt = $request->input('opt', []);
        $opt_contain = $request->input('opt_contain', '');

        $page = $request->input('page', 1);
        $where = "";

        if($prd_cd != '') $where .= " and pc.prd_cd like '$prd_cd%'";
        if($goods_nm != '') $where .= " and p.prd_nm like '%$goods_nm%'";
		
        foreach (self::Conds_sub as $key => $value) {
            if ($key === 'item') $key = 'items';
            if (count(${ $key }) > 0) {
                $where .= ${ $key . '_contain' } == 'true' ? " and (1!=1" : " and (1=1";

                $col = $key === 'items' ? 'item' : $key;
                foreach (${ $key } as $item) {
                    if(${ $key . '_contain' } == 'true')
                        $where .= " or pc.$col = '$item'";
                    else
                        $where .= " and pc.$col != '$item'";
                }
                $where .= ")";
            }
        }

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;

        $sql = "
			select 
				  pc.prd_cd
				, p.prd_nm
				, pc.goods_no
				, pc.color
				, ifnull((
					select s.size_cd from size s
					where s.size_kind_cd = pc.size_kind
					   and s.size_cd = pc.size
					   and use_yn = 'Y'
				),'') as size
				, pc.color
				, pc.item
				, pc.opt
			from product_code pc 
				inner join product p on p.prd_cd = pc.prd_cd
			where 1=1 and pc.brand in('PR','SM') and p.use_yn = 'Y'
				$where   
        ";

        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
              
            ],
            "body" => $result
        ]);
    }

    //판매유형 검색
    public function search_sell_type(Request $request)
    {
        $sell_nm= $request->input('sell_nm', '');

        $page = $request->input('page', 1);
        $where = "";

        if($sell_nm != '') $where .= " and code_val like '$sell_nm%'";

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'sale_kind'
        ";
      
        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
              
            ],
            "body" => $result
        ]);
    }


    //행사코드 검색
    public function search_prcode(Request $request)
    {
        $pr_code_nm= $request->input('pr_code_nm', '');

        $page = $request->input('page', 1);
        $where = "";

        if($pr_code_nm != '') $where .= " and code_val like '$pr_code_nm%'";

        $page_size = 100;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";
        $total = 0;
        $page_cnt = 0;

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'pr_code'
        ";
      
        $result = DB::select($sql);

        return response()->json([
            "code" => '200',
            "head" => [
                "total" => count($result),
                "page" => 1,
                "page_cnt" => 1,
                "page_total" => 1,
              
            ],
            "body" => $result
        ]);
    }
	
	/** 사이즈코드표 팝업 오픈 */
	public function show_size_code()
	{
		$values = $this->_get_size_code();
		return view(Config::get('shop.store.view') . '/common/sizecode', $values);
	}
	
	private function _get_size_code()
	{
		$size_kinds = DB::table('size_kind')->where('use_yn', 'Y')->orderBy('seq')->get();
		$sql = "
			select size_kind_cd, size_cd, size_seq
				, SUBSTRING_INDEX(size_nm, '(', 1) as size_nm
			from size
			where use_yn = 'Y'
			order by size_kind_cd, size_seq
		";
		$sizes = DB::select($sql);

		for ($i = 0; $i < count($size_kinds); $i++) {
			$kind_cd = $size_kinds[$i]->size_kind_cd;
			$ss = array_filter($sizes, function ($s) use ($kind_cd) { return $s->size_kind_cd === $kind_cd; });
			$size_kinds[$i]->sizes = $ss;
		}

		$max_size_cnt = array_reduce($size_kinds->toArray(), function ($a, $c) { return $a < count($c->sizes) ? count($c->sizes) : $a; }, 0);
		return [ 'size_kinds' => $size_kinds, 'max_size_cnt' => $max_size_cnt ];
	}

	/** 사이즈코드표 다운로드 */
	public function download_size_code()
	{
		$data = array_merge([ 'one_sheet_count' => -1 ], $this->_get_size_code());
		$view_url = Config::get('shop.store.view') . '/common/sizecode_excel';
		$keys = [ 'list_key' => 'size_kinds', 'one_sheet_count' => $data['one_sheet_count'] ];

		return \Maatwebsite\Excel\Facades\Excel::download(new ExcelViewExport($view_url, $data, [], [], $keys), '사이즈코드표.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}
	
	public function search_size(Request $request) {
		$size_kind = $request->input('size_kind', '');
		
		foreach ($size_kind as $sk) {
			$sql = "
				select
				    size_cd
				    , size_nm
				from size
				where size_kind_cd = '$sk'
					and use_yn = 'Y'
				order by size_seq
			";
			$sizes = DB::select($sql);
		}

		return response()->json([
			"body" => $sizes
		]);
		
	}
}
