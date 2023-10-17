<?php

namespace App\Http\Controllers\store\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;

class ord06Controller extends Controller
{
	private function _getOrdStates() {
		return [
			(object) ['code_id' => 30, 'code_val' => '출고완료'],
			(object) ['code_id' => 60, 'code_val' => '교환완료'],
			(object) ['code_id' => 61, 'code_val' => '환불완료'],
		];
	} 

	public function index(Request $request)
	{
		$sdate = now()->sub(1, 'week')->format('Y-m-d');
		$edate = date('Y-m-d');

		$date = $request->input('date', '');
		if ($date != '') {
			$sdate = date('Y-m-d', strtotime($date));
			$edate = date('Y-m-d', strtotime($date));
		}

		$pr_code 			= $request->input('pr_code', []);
		$sell_type 			= $request->input('sell_type', []);
		$stat_pay_type 		= $request->input('stat_pay_type', []);
		$ord_type 			= $request->input('ord_type', []);
		$ord_state 			= $request->input('ord_state', '');
		// $item 				= $request->input('item', '');
		// $brand_cd 			= $request->input('brand_cd', '');
		$goods_nm 			= $request->input('goods_nm', '');
		$store_cd 			= $request->input('store_no', '');
		$on_off_yn 			= $request->input('on_off_yn', '');
		$store_channel 		= $request->input('store_channel', '');
		$store_channel_kind = $request->input('store_channel_kind', '');
		$prd_cd_range_text 	= $request->query("prd_cd_range", '');
		$prd_cd_range_nm 	= $request->query("prd_cd_range_nm", '');
		parse_str($prd_cd_range_text, $prd_cd_range);

		//매장별판매집계표(일별)에서 쿼리스트링으로 가져온 값들
		$sdate = $request->query('sdate', $sdate);
		$edate = $request->query('sdate', $edate);
		$store_cd = $request->query('store_cd',$store_cd);
		
		$pr_code_ids = DB::table('code')->select('code_id')->where('code_kind_cd', 'PR_CODE')->whereIn('code_id', $pr_code)->get();
		$pr_code_ids = array_map(function ($p) { return $p->code_id; }, $pr_code_ids->toArray());
		$sell_type_ids = DB::table('code')->select('code_id')->where('code_kind_cd', 'SALE_KIND')->whereIn('code_id', $sell_type)->get();
		$sell_type_ids = array_map(function ($p) { return $p->code_id; }, $sell_type_ids->toArray());
		$store = DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', $store_cd)->first();
		// $brand = DB::table('brand')->select('brand', 'brand_nm')->where('brand', $brand_cd)->first();
		$ord_states = $this->_getOrdStates();

		$conf = new Conf();
		$domain = $conf->getConfigValue("shop", "domain");

		
		

		$values = [
			'sdate' 		=> $sdate,
			'edate' 		=> $edate,
			'domain'		=> $domain,
			'ord_states'    => $ord_states, // 주문상태
			// 'ord_states'    => SLib::getordStates(), // 주문상태
			'clm_states'    => SLib::getCodes('G_CLM_STATE'), // 클레임상태
			'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
			'ord_types'     => SLib::getCodes('G_ORD_TYPE'), // 주문구분
			'ord_kinds'     => SLib::getCodes('G_ORD_KIND'), // 출고구분
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
			// 'items' 		=> SLib::getItems(),
			'sale_kinds'	=> SLib::getCodes('SALE_KIND'),
			'pr_codes'		=> SLib::getCodes('PR_CODE'),
			'store_channel'	=> SLib::getStoreChannel(),
			'store_kind'	=> SLib::getStoreKind(),
			'stat_pay_type'	=> $stat_pay_type,
			'ord_type'		=> $ord_type,
			'ord_state'		=> $ord_state,
			// 'item'			=> $item,
			// 'brand'			=> $brand,
			'goods_nm'		=> $goods_nm,
			'on_off_yn'		=> $on_off_yn,
			'pr_code_ids'	=> $pr_code_ids,
			'sell_type_ids'	=> $sell_type_ids,
			'p_store_channel' => $store_channel,
			'p_store_kind' 	=> $store_channel_kind,
			'store'			=> $store,
			'prd_cd_range'	=> $prd_cd_range,
			'prd_cd_range_nm' => $prd_cd_range_nm,
			'store_cd'		=> $store_cd
		];
		return view(Config::get('shop.store.view') . '/order/ord06', $values);
	}

	public function search(Request $request)
	{
		$store_no       = $request->input('store_no', '');
		$sale_kind      = $request->input('sale_kind', '');
		$pr_code        = $request->input('pr_code', '');
		$sdate          = $request->input('sdate', now()->sub(3, 'month')->format('Ymd'));
		$edate          = $request->input('edate', date('Ymd'));
		$nud            = $request->input('nud', ''); // 주문일자 검색여부
		$ord_no         = $request->input('ord_no', '');
		$ord_state      = $request->input('ord_state', '');
		// $pay_state      = $request->input('pay_stat', '');
		$clm_state      = $request->input('clm_state', '');
		$ord_type       = $request->input('ord_type', '');
		$ord_kind       = $request->input('ord_kind', '');
		$ord_info_key   = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$stat_pay_type  = $request->input('stat_pay_type', '');
		$not_complex    = $request->input('not_complex', 'N'); // 복합결제 제외
		$prd_cd         = $request->input('prd_cd', '');
		$style_no       = $request->input('style_no', '');
		$goods_no       = $request->input('goods_no', '');
		$goods_nm       = $request->input('goods_nm', '');
		// $item           = $request->input('item', '');
		// $brand_cd       = $request->input('brand_cd', '');
		$goods_nm_eng   = $request->input('goods_nm_eng', '');
		$com_cd         = $request->input('com_cd', '');
		$com_nm         = $request->input('com_nm', '');
		$limit          = $request->input('limit', 100);
		$ord            = $request->input('ord', 'desc');
		$ord_field      = $request->input('ord_field', 'o.ord_date');
		$page           = $request->input('page', 1);
		$prd_cd_range_text = $request->input("prd_cd_range", '');
		$sale_form      = $request->input('sale_form', '');
		$sell_type      = $request->input('sell_type');
		$store_channel		= $request->input("store_channel");
		$store_channel_kind	= $request->input("store_channel_kind");

		if ($page < 1 or $page == '') $page = 1;

		$offline_store = 'HEAD_OFFICE';

		// $mobile_yn      = $request->input('mobile_yn', '');  // 모바일 주문 여부
		// $app_yn         = $request->input('app_yn', '');    // 앱 주문 여부
		// $receipt        = $request->input('receipt', 'N');  // 현금영수증 : N(미신청), R(신청), Y(발행)
		// $dlv_type       = $request->input('dlv_type', '');  // 배송방식: D(택배), T(택배(당일배송)), G(직접수령)
		// $pay_fee        = $request->input('pay_fee', '');  // 결제수수료 주문
		// $fintech        = $request->input('fintech', '');  // 핀테크

		$where = "";
		//2023-10-17 해당 주석처리
//		$where .= " and o.ord_kind != '10' "; // 정상판매건이 아닌 경우에만 출력

		// 날짜검색 미사용 여부
		$is_not_use_date = false;
		// if (
		//     $ord_no != ''
		//     || ($ord_info_key == 'om.user_id' && $ord_info_value != '')
		//     || ($ord_info_key == 'om.user_nm' && $ord_info_value != '')
		//     || ($ord_info_key == 'om.r_nm' && strlen($ord_info_value) >= 4)
		//     || ($ord_info_key == 'om.mobile' && strlen($ord_info_value) >= 8)
		//     || ($ord_info_key == 'om.phone' && strlen($ord_info_value) >= 8)
		//     || ($ord_info_key == 'om.r_mobile' && strlen($ord_info_value) >= 8)
		// ) {
		//     $is_not_use_date = true;
		// }
		if ($is_not_use_date == false && $nud == 'on') {
			$sdate = str_replace("-", "", $sdate);
			$edate = str_replace("-", "", $edate);
			$where .= " and w.ord_state_date >= '$sdate' ";
			$where .= " and w.ord_state_date <= '$edate' ";
		}
		if ($ord_no != '') $where .= " and o.ord_no = '$ord_no' ";
		if ($store_no != '') $where .= " and o.store_cd = '$store_no' ";
		if ($ord_state != '') $where .= " and w.ord_state = '$ord_state' ";
		// if ($pay_state != '') $where .= " and pay.pay_stat = '$pay_state' ";
		// 클레임상태
		if ($clm_state == '90') {
			$where .= " and o.clm_state = '0' ";
		} else {
			if ($clm_state != '') {
				$where .= " and o.clm_state = '$clm_state' ";
			}
		}
		if ($ord_kind != '') $where .= " and o.ord_kind = '$ord_kind' ";
		if ($ord_type != '') $where .= " and o.ord_type = '$ord_type' ";
		// 주문정보검색
		if ($ord_info_value != '') {
			if (in_array($ord_info_key, ['om.mobile', 'om.phone', 'om.r_mobile', 'om.r_phone'])) {
				$val = $this->__replaceTel($ord_info_value);
				if (in_array($ord_info_key, ['om.mobile', 'om.phone', 'om.r_mobile'])) {
					$where .= " and $ord_info_key = '$val' ";
				} else {
					$where .= " and $ord_info_key like '$val%' ";
				}
			} else {
				if ($ord_info_key == 'memo') {
					$where .= " and (m.state like '%$ord_info_value%' or m.memo like '%$ord_info_value%') ";
				} else if ($ord_info_key == 'o.dlv_end_date') {
					$where .= " and date_format($ord_info_key, '%Y%m%d') = $ord_info_value ";
				} else if (in_array($ord_info_key, ['om.user_nm', 'om.user_id', 'om.r_nm', 'om.bank_inpnm'])) {
					$where .= " and $ord_info_key like '$ord_info_value%' ";
				} else {
					$where .= " and $ord_info_key like '$ord_info_value%' ";
				}
			}
		}
		// 결제방법
		if ($stat_pay_type != '') {
			if ($not_complex == 'Y') {
				$where .= " and o.pay_type = '$stat_pay_type' ";
			} else {
				$where .= " and ((o.pay_type & $stat_pay_type) = $stat_pay_type) ";
			}
		}
		// 상품코드
		if ($prd_cd != '') {
			$prd_cd = preg_replace("/\s/", ",", $prd_cd);
			$prd_cd = preg_replace("/\t/", ",", $prd_cd);
			$prd_cd = preg_replace("/\n/", ",", $prd_cd);
			$prd_cd = preg_replace("/,,/", ",", $prd_cd);
			$prd_cds = explode(',', $prd_cd);
			if (count($prd_cds) > 1) {
				if (count($prd_cds) > 500) array_splice($prd_cds, 500);
				$where .= " and (1!=1";
				foreach($prd_cds as $cd) {
					$where .= " or o.prd_cd like '" . Lib::quote($cd) . "%' ";
				}
				$where .= ")";
			} else {
				$where .= " and o.prd_cd like '" . Lib::quote($prd_cd) . "%' ";
			}
		}

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
		if ($style_no != '') $where .= " and g.style_no like '$style_no%' ";
		if ($goods_no != '') {
			$goods_no = preg_replace("/\s/", ",", $goods_no);
			$goods_no = preg_replace("/\t/", ",", $goods_no);
			$goods_no = preg_replace("/\n/", ",", $goods_no);
			$goods_no = preg_replace("/,,/", ",", $goods_no);
			$goods_nos = explode(',', $goods_no);
			if (count($goods_nos) > 1) {
				if (count($goods_nos) > 500) array_splice($goods_nos, 500);
				$in_goods_nos = join(',', $goods_nos);
				$where .= " and g.goods_no in ($in_goods_nos) ";
			} else {
				$where .= " and g.goods_no = '$goods_no' ";
			}
		}
		if ($goods_nm != '') $where .= " and g.goods_nm like '%$goods_nm%' ";
		// if (count($goods_stat) > 0) {
		//     if (count($goods_stat) == 1 && $goods_stat[0] != '') {
		//         $where .= " and g.sale_stat_cl = '" . $goods_stat[0] . "' ";
		//     } else {
		//         $in_goods_stats = join(',', $goods_stat);
		//         $where .= " and g.sale_stat_cl in ($in_goods_stats) ";
		//     }
		// }
		// if ($item != '') $where .= " and g.opt_kind_cd = '$item' ";
		// if ($brand_cd != '') $where .= " and g.brand = '$brand_cd' ";
		if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%$goods_nm_eng%' ";
		if ($com_cd != '') $where .= " and g.com_id = '$com_cd' ";
		else if ($com_nm != '') $where .= " and g.com_nm = '$com_nm' ";

		if ($sale_form == 'OFF') $where .= " and (o.store_cd is not null and o.store_cd <> '$offline_store') ";
		else if ($sale_form == 'ON') $where .= " and (o.store_cd is null or o.store_cd = '$offline_store') ";

		if ($sale_kind != '') $where .= "and o.sale_kind = '$sale_kind' ";

		//행사코드 검색
		if ( $pr_code != "" ) {
			$where	.= " and (1!=1";
			foreach($pr_code as $pr_codes) {
				$where .= " or o.pr_code = '$pr_codes' ";

			}
			$where	.= ")";
		}

		//판매유형 검색
		if ( $sell_type != "" ) {
			$where	.= " and (1!=1";
			foreach($sell_type as $sell_types) {
				$where .= " or o.sale_kind = '$sell_types' ";

			}
			$where	.= ")";
		}

		// 판매채널/매장구분 검색
		if ($store_channel != "") $where .= "and store.store_channel ='" . Lib::quote($store_channel). "'";
		if ($store_channel_kind != "") $where .= "and store.store_channel_kind ='" . Lib::quote($store_channel_kind). "'";

		// ordreby
		$orderby = sprintf("order by %s %s, w.ord_wonga_no desc", $ord_field, $ord);

		// pagination
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$cfg_img_size_real	= "a_500";
		$cfg_img_size_list	 = "s_50";

		$sql = "
            select
                a.ord_no,
                a.ord_opt_no,
                a.ord_state as ord_state_cd,
                a.ord_state_date,
                clm_state.code_val as clm_state,
                pay_stat.code_val as pay_stat,
                a.prd_cd,
                a.goods_no,
                a.style_no,
                a.goods_nm,
                a.goods_nm_eng,
                a.prd_cd_p,
                a.color,
                a.size,
                a.img,
                replace(a.goods_opt, '^', ' : ') as opt_val,
                concat(a.user_nm, ' (', a.user_id, ')') as user_nm,
                a.r_nm,
                a.sale_place,
                if(a.ord_state > 30, a.qty * -1, a.qty) as qty,
                a.goods_price,
                a.price,
                a.goods_sh,
                a.wonga,
                if(a.ord_state > 30, a.recv_amt * -1, a.recv_amt) as recv_amt,
                if(a.ord_state > 30, a.dlv_amt * -1, a.dlv_amt) as dlv_amt,
                a.sales_com_fee,
                pay_type.code_val as pay_type,
                ord_type.code_val as ord_type,
                ord_kind.code_val as ord_kind,
                a.store_cd,
                ifnull(s.store_nm, '본사') as store_nm,
                baesong_kind.code_val as baesong_kind,
                a.dlv_no,
                dlv_cd.code_val as dlv_cm,
                a.state,
                a.memo,
                a.ord_date,
                a.sale_kind,
                (a.price - a.sale_kind_amt) as sale_price,
                (a.qty * (a.price - a.sale_kind_amt)) * if(a.ord_state > 30, -1, 1) as ord_amt,
                sale_kind.code_val as sale_kind_nm,
                a.sale_dc_rate,
				round((1 - ((a.price - a.sale_kind_amt) / a.goods_sh)) * 100) as dc_rate,
                a.pr_code,
                pr_code.code_val as pr_code_nm,
                a.pay_date,
                a.dlv_end_date,
                a.last_up_date,
                if(a.opt_ord_state <= 10 and a.clm_state = 0 and ord_opt_cnt = 0, 'Y', 'N') as ord_del_yn,
                '2' as depth
            from (
                select
                    om.ord_no,
                    o.ord_opt_no,
                    o.ord_state as opt_ord_state,
                    w.ord_state,
                    w.ord_state_date,
                    o.clm_state,
                    pay.pay_stat,
                    o.prd_cd,
                    g.goods_no,
                    g.style_no,
                    g.goods_nm_eng,
                    o.goods_nm,
                    o.goods_opt,
                    concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p,
                    if(g.special_yn <> 'Y', replace(g.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                        select replace(a.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                        from goods a where a.goods_no = g.goods_no and a.goods_sub = 0
                    )) as img,
                    '' as img_view,
                    pc.color,
                    ifnull((
						select s.size_cd from size s
						where s.size_kind_cd = pc.size_kind
						   and s.size_cd = pc.size
						   and use_yn = 'Y'
					),'') as size,
                    w.qty,
                    om.user_id,
                    om.user_nm,
                    om.r_nm,
                    om.sale_place,
                    g.price as goods_price,
                    g.goods_sh,
                    o.wonga,
                    o.price,
                    o.recv_amt,
                    o.dlv_amt,
                    o.sales_com_fee,
                    pay.pay_type,
                    o.ord_type,
                    o.ord_kind,
                    o.store_cd,
                    g.baesong_kind as dlv_baesong_kind,
                    o.dlv_no,
                    o.dlv_cd,
                    m.state,
                    m.memo,
                    o.sale_kind,
                    o.pr_code,
                    o.ord_date,
                    pay.pay_date,
                    o.dlv_end_date,
                    c.last_up_date,
                    (select count(*) from order_opt where ord_no = o.ord_no and ord_opt_no != o.ord_opt_no and (ord_state > 10 or clm_state > 0)) as ord_opt_cnt,
                    st.amt_kind,
                    if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt) as sale_kind_amt,
                    round((1 - (o.price / g.goods_sh)) * 100) as sale_dc_rate
                from order_opt_wonga w
                    inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                    left outer join product_code pc on pc.prd_cd = o.prd_cd
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join goods g on o.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                    left outer join sale_type st on st.sale_kind = o.sale_kind and st.use_yn = 'Y'
					left outer join store store on store.store_cd = o.store_cd
                where w.ord_state in (30,60,61) $where
                $orderby
                $limit
            ) a
                left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
                left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
                left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
                left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
                left outer join code baesong_kind on (a.dlv_baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
                left outer join code dlv_cd on (a.dlv_cd = dlv_cd.code_id and dlv_cd.code_kind_cd = 'DELIVERY')
                left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
                left outer join store s on s.store_cd = a.store_cd
                left outer join code sale_kind on (sale_kind.code_id = a.sale_kind and sale_kind.code_kind_cd = 'SALE_KIND')
                left outer join code pr_code on (pr_code.code_id = a.pr_code and pr_code.code_kind_cd = 'PR_CODE')
        ";
		// $result = DB::select($sql);

		$pdo	= DB::connection()->getPdo();
		$stmt	= $pdo->prepare($sql);
		$stmt->execute();
		$result	= [];
		
		$ord_states = $this->_getOrdStates();
		
		while($row2 = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row2["img"] != ""){
				$row2["img"] = sprintf("%s%s",config("shop.image_svr"), $row2["img"]);
			}
			
			$idx = array_search($row2["ord_state_cd"], array_column($ord_states, 'code_id'));
			$row2["ord_state"] = $ord_states[$idx]->code_val;

			$result[] = $row2;
		}

		// pagination
		$total = 0;
		$page_cnt = 0;
		$total_row = [];
		if($page == 1) {
			$sql = "
                select
                    count(*) as total,
                    sum(w.qty * if(w.ord_state > 30, -1, 1)) as total_qty,
                    -- sum(w.qty * g.price * if(w.ord_state > 30, -1, 1)) as total_goods_price,
                    -- sum(w.qty * o.price * if(w.ord_state > 30, -1, 1)) as total_price,
                    -- sum(w.qty * g.goods_sh * if(w.ord_state > 30, -1, 1)) as total_goods_sh,
                    -- sum(w.qty * o.wonga * if(w.ord_state > 30, -1, 1)) as total_wonga,
                    -- round((1 - (sum(o.price * if(w.ord_state > 30, -1, 1)) / sum(g.goods_sh * if(w.ord_state > 30, -1, 1)))) * 100) as avg_sale_dc_rate,
                    -- sum((o.price - if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt)) * if(w.ord_state > 30, -1, 1)) as total_sale_price,
                    -- round((1 - (sum((o.price - if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt)) * if(w.ord_state > 30, -1, 1)) / sum(g.goods_sh * if(w.ord_state > 30, -1, 1)))) * 100) as avg_dc_rate,
                    sum(w.qty * (o.price - if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt)) * if(w.ord_state > 30, -1, 1)) as total_ord_amt,
                    sum(o.recv_amt * if(w.ord_state > 30, -1, 1)) as total_recv_amt,
                    sum(o.dlv_amt * if(w.ord_state > 30, -1, 1)) as total_dlv_amt
                from order_opt_wonga w
                    inner join order_opt o on o.ord_opt_no = w.ord_opt_no
                    left outer join product_code pc on pc.prd_cd = o.prd_cd
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join goods g on o.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                    left outer join sale_type st on st.sale_kind = o.sale_kind and st.use_yn = 'Y'
                	inner join store store on store.store_cd = o.store_cd
                where w.ord_state in (30,60,61) $where
            ";

			$row = DB::selectOne($sql);
			$total = $row->total;
			$total_row = $row;
			$page_cnt = (int)(($total - 1) / $page_size) + 1;
		}

		return response()->json([
			"code" => 200,
			"head" => [
				"total" => $total,
				"page" => $page,
				"page_cnt" => $page_cnt,
				"page_total" => count($result),
				'total_row'  => $total_row,
			],
			"body" => $result,
		]);
	}
}
