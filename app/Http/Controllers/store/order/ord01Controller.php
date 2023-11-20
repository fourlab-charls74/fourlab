<?php

namespace App\Http\Controllers\store\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\Order;
use App\Models\Point;
use App\Models\Claim;
use App\Models\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use PDO;

class ord01Controller extends Controller
{
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

		$pr_code_ids = DB::table('code')->select('code_id')->where('code_kind_cd', 'PR_CODE')->whereIn('code_id', $pr_code)->get();
		$pr_code_ids = array_map(function ($p) { return $p->code_id; }, $pr_code_ids->toArray());
		$sell_type_ids = DB::table('code')->select('code_id')->where('code_kind_cd', 'SALE_KIND')->whereIn('code_id', $sell_type)->get();
		$sell_type_ids = array_map(function ($p) { return $p->code_id; }, $sell_type_ids->toArray());
		$store = DB::table('store')->select('store_cd', 'store_nm')->where('store_cd', $store_cd)->first();
		// $brand = DB::table('brand')->select('brand', 'brand_nm')->where('brand', $brand_cd)->first();

		$conf = new Conf();
		$domain		= $conf->getConfigValue("shop", "domain");

		$values = [
			'sdate' 		=> $sdate,
			'edate' 		=> $edate,
			'domain'		=> $domain,
			'ord_states'    => SLib::getordStates(), // 주문상태
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
		];
		return view(Config::get('shop.store.view') . '/order/ord01', $values);
	}


    public function view(Request $req)
    {
        $p_ord_opt_no = $req->input("p_ord_opt_no","");

        $sql = /** @lang text */
            "
            select
                a.ord_no,a.ord_opt_no,a.goods_no,a.head_desc, a.goods_nm, replace(a.goods_opt, '^', ' : ') as opt_val
                , a.qty, a.price, a.point_amt, a.coupon_amt, a.recv_amt, a.dlv_amt
                , a.dc_amt, a.opt_amt, 0 as pay_fee
                , substr(IFNULL(a.head_desc, ''), 0, 12 ) as old_head_desc
                , substr(a.goods_nm, 0, 30) as old_goods_nm
                , a.dlv_amt + a.recv_amt + 0 as total_amt
                , a.qty * a.price as ord_amt
            from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
                inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
            where a.ord_opt_no = '$p_ord_opt_no'
        ";
        $p_ord_opt = DB::selectOne($sql);
        $ord_no = $p_ord_opt->ord_no;
        $ord_opt_no = $p_ord_opt->ord_opt_no;

        $p_ord_opt->addopts = [];
        if (!empty($p_ord_opt->ord_opt_no)) {
            // 추가 옵션 값 얻기
            $sql = /** @lang text */
                "
                select *
                from order_opt_addopt
                where ord_opt_no = '$p_ord_opt_no'
                order by no
            ";
            $p_ord_opt->addopts  = DB::select($sql);
        }

        $sql = /** @lang text */
            "
            select
                concat(code_val,'_',ifnull(code_val2, '')) as 'name',
                concat(code_val,' [',ifnull(code_val2, ''),']') as 'value'
            from code
            where code_kind_cd ='BANK'
                and code_id != 'K'
                and use_yn = 'Y'
            order by code_seq
        ";
        $banks = DB::select($sql);

        $values = [
            'ord_no' => $ord_no,
            'ord_opt_no' => $ord_opt_no,
            'p_ord_opt_no' => $p_ord_opt_no,
            'p_ord_opt' => $p_ord_opt,
            'banks' => $banks,
            'pay_types' => SLib::getCodes("G_STAT_PAY_TYPE"),
            'ord_types' => SLib::getCodes('G_ORD_TYPE'),
            'sale_places' => SLib::getSalePlaces(),
            'dlv_cds' => SLib::getCodes('DELIVERY'),
        ];
        return view(Config::get('shop.store.view') . '/order/ord01_view', $values);
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
        $pay_state      = $request->input('pay_stat', '');
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
        // $goods_stat     = $request->input('goods_stat', []);
        $item           = $request->input('item', '');
        $brand_cd       = $request->input('brand_cd', '');
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
        //2023-09-18 해당 주석처리
		//$where .= " and o.ord_kind != '10' "; // 정상판매건이 아닌 경우에만 출력

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
            $where .= " and o.ord_date >= '$sdate 00:00:00' ";
            $where .= " and o.ord_date <= '$edate 23:59:59' ";
        }
        if ($ord_no != '') $where .= " and o.ord_no = '$ord_no' ";
        if ($store_no != '') $where .= " and o.store_cd = '$store_no' ";
        if ($ord_state != '') $where .= " and o.ord_state = '$ord_state' ";
        if ($pay_state != '') $where .= " and pay.pay_stat = '$pay_state' ";
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
                    $where .= " and $ord_info_key = '$ord_info_value' ";
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
                $in_prd_cds = join(',', array_map(function($s) { return "'$s'"; }, $prd_cds));
                $where .= " and o.prd_cd in ($in_prd_cds) ";
            } else {
                $where .= " and o.prd_cd like '$prd_cd%' ";
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

        if ($sale_form == 'Off') $where .= " and (o.store_cd is not null and o.store_cd <> '$offline_store') ";
        else if ($sale_form == 'On') $where .= " and (o.store_cd is null or o.store_cd = '$offline_store') ";

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
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

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
                ord_state.code_val as ord_state,
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
                a.qty,
                concat(a.user_nm, ' (', a.user_id, ')') as user_nm,
                a.r_nm,
                a.sale_place,
                a.goods_price,
                a.price,
                a.goods_sh,
                a.wonga,
				a.recv_amt,
                a.dlv_amt,
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
                a.dlv_comment,
                a.ord_date,
                a.sale_kind,
                (a.price - a.sale_kind_amt) as sale_price,
                (a.qty * (a.price - a.sale_kind_amt)) as ord_amt,
                sale_kind.code_val as sale_kind_nm,
                a.sale_dc_rate,
				round((1 - ((a.price - a.sale_kind_amt) / a.goods_sh)) * 100) as dc_rate,
                a.pr_code,
                pr_code.code_val as pr_code_nm,
                a.pay_date,
                a.dlv_end_date,
                a.last_up_date,
                if(a.ord_state <= 10 and a.clm_state = 0 and ord_opt_cnt = 0, 'Y', 'N') as ord_del_yn,
                '2' as depth
            from (
                select
                    om.ord_no,
                    o.ord_opt_no,
                    o.ord_state,
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
                    o.qty,
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
                    o.dlv_comment,
                    o.sale_kind,
                    o.pr_code,
                    o.ord_date,
                    pay.pay_date,
                    o.dlv_end_date,
                    c.last_up_date,
                    (select count(*) from order_opt where ord_no = o.ord_no and ord_opt_no != o.ord_opt_no and (ord_state > 10 or clm_state > 0)) as ord_opt_cnt,
                    st.amt_kind,
                    ifnull(if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt), 0) as sale_kind_amt,
                    round((1 - (o.price / g.goods_sh)) * 100) as sale_dc_rate
                from order_opt o
                    left outer join product_code pc on pc.prd_cd = o.prd_cd
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join goods g on o.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                    left outer join sale_type st on st.sale_kind = o.sale_kind and st.use_yn = 'Y'
					left outer join store store on store.store_cd = o.store_cd
                where 1=1 $where
                $orderby
                $limit
            ) a
                left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
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
		while($row2 = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($row2["img"] != ""){
				$row2["img"] = sprintf("%s%s",config("shop.image_svr"), $row2["img"]);
			}

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
                    sum(o.qty) as total_qty,
                    sum(o.qty * g.price) as total_goods_price,
                    sum(o.qty * o.price) as total_price,
                    sum(o.qty * g.goods_sh) as total_goods_sh,
                    sum(o.qty * o.wonga) as total_wonga,
                    round((1 - (sum(o.price) / sum(g.goods_sh))) * 100) as avg_sale_dc_rate,
                    sum(o.price - if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt)) as total_sale_price,
                    round((1 - (sum(o.price - if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt)) / sum(g.goods_sh))) * 100) as avg_dc_rate,
                    sum(o.qty * (o.price - if(st.amt_kind = 'per', round(o.price * st.sale_per / 100), st.sale_amt))) as total_ord_amt,
                    sum(o.recv_amt) as total_recv_amt,
                    sum(o.dlv_amt) as total_dlv_amt
                from order_opt o
                    left outer join product_code pc on pc.prd_cd = o.prd_cd
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join goods g on o.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                    left outer join sale_type st on st.sale_kind = o.sale_kind and st.use_yn = 'Y'
                	inner join store store on store.store_cd = o.store_cd
                where 1=1 $where
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

    /** 출고 전 주문삭제 */
    public function del_order(Request $request)
    {
        $ord_nos = $request->input('ord_nos', []);
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        $code = '200';
        $msg = '';
        $success_cnt = 0;

        try {
            $order = new Order($user);
            foreach ($ord_nos as $ord_no) {
                $success = $order->DeleteStoreOrder($ord_no);
                $success_cnt += $success;
            }
        } catch(Exception $e) {
            $code = '500';
            $msg = $e->getMessage();
        }

        return response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => [
                'total_count' => count($ord_nos),
                'success_count' => $success_cnt
            ],
        ]);
    }

    public function create()
    {
        $sql = "
            select
                concat(code_val,'_',ifnull(code_val2, '')) as 'name',
                concat(code_val,' [',ifnull(code_val2, ''),']') as 'value'
            from code
            where code_kind_cd ='BANK'
                and code_id != 'K'
                and use_yn = 'Y'
            order by code_seq
        ";
        $banks = DB::select($sql);

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'G_PAY_TYPE'
                and code_id <> 'K'
                and code_id in ('1','2','5','9','13','16','32','64')
            order by code_seq
        ";
        $pay_types = DB::select($sql);

        $conf = new Conf();
		
		// 본사 Default 매장 
		$head_store = DB::table('store')->where('store_cd', 'A0003')->first();

        $values = [
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            'pay_types'     => $pay_types,
            'banks'         => $banks,
            'dlv_cds'       => SLib::getCodes('DELIVERY'),
            'dlv_fee'       => [
                'base_dlv_fee'  => $conf->getConfigValue('delivery', 'base_delivery_fee'),
                'add_dlv_fee'   => $conf->getConfigValue('delivery', 'add_delivery_fee'),
                'free_dlv_amt'  => $conf->getConfigValue('delivery', 'free_delivery_amt'),
            ],
			'head_store' => $head_store,
			'sale_kinds' => SLib::getUsedSaleKinds(),
        ];
        return view(Config::get('shop.store.view') . '/order/ord01_show', $values);
    }
	
	// 수기판매 주문매장정보 변경 시, 해당매장에서 사용중인 판매처수수료목록 조회
	public function search_store_info($store_cd = '')
	{
		$date = date('Y-m-d');
		$is_online = 0;

	 	if ($store_cd !== '') {
			$sql = "
				select f.store_cd, f.pr_code, p.code_val as pr_code_nm, f.store_fee, f.sdate, f.edate, f.use_yn
				from store_fee f
					inner join code p on p.code_kind_cd = 'PR_CODE' and p.code_id = f.pr_code
				where f.store_cd = :store_cd and f.use_yn = 'Y' and f.sdate <= :date1 and f.edate >= :date2
				group by f.pr_code
			";
			$pr_codes = DB::select($sql, [ 'store_cd' => $store_cd, 'date1' => $date, 'date2' => $date ]);
			
			// 판매채널이 '자사온라인' or '위탁온라인' 인 매장에서는 수기판매 출고완료처리가 불가능합니다. (2023-09-19)
			$sql = "
				select count(*) as cnt
				from store
				where store_cd = :store_cd and store_cd in (
					select store_cd
					from store
					where store_channel = 'EC' or store_channel = 'CE'
				)
			";
			$is_online = DB::selectOne($sql, [ 'store_cd' => $store_cd ])->cnt;
		} else {
			$sql = "
				select f.store_cd, f.pr_code, p.code_val as pr_code_nm, f.store_fee, f.sdate, f.edate, f.use_yn
				from store_fee f
					inner join code p on p.code_kind_cd = 'PR_CODE' and p.code_id = f.pr_code
				where f.use_yn = 'Y' and f.sdate <= :date1 and f.edate >= :date2
				group by f.pr_code
			";
			$pr_codes = DB::select($sql, [ 'date1' => $date, 'date2' => $date ]);
		}

		return response()->json([ 
			'code' => 200, 
			'msg' => '판매처수수료목록이 정상적으로 조회되었습니다.', 
			'pr_codes' => $pr_codes,
			'is_online' => $is_online,
		]);
	}

    // 수기판매 등록
    public function save(Request $req)
    {
        $code = '200';
        $msg = '';
        $ord_no = '';

        $conf = new Conf();
        $cfg_ratio = $conf->getConfigValue("point", "ratio");

        // $ord_no = $req->input("ord_no", "");
        $p_ord_opt_no = $req->input("p_ord_opt_no", "");
        $ord_type = $req->input("ord_type", ""); // 출고형태
        $ord_kind = $req->input("ord_kind", ""); // 출고구분
        $ord_state = $req->input("ord_state", ""); // 주문상태
        $store_cd = $req->input("store_no", ""); // 주문매장

        $cart = $req->input("cart"); // 상품정보

        $base_dlv_amt = $conf->getConfigValue('delivery', 'base_delivery_fee'); // 기본배송비
        $free_dlv_amt = $conf->getConfigValue('delivery', 'free_delivery_amt'); // 배송비무료 금액
        $dlv_apply = $req->input("dlv_apply", ""); // 배송비적용 여부
        $add_dlv_fee = $req->input("add_dlv_fee", 0); // 추가배송비

        $coupon_no = $req->input("coupon_no", "");
        $pay_type = $req->input("pay_type", ""); // 결제방법
        $bank_inpnm = $req->input("bank_inpnm", ""); // 입금자
        $bank_code = $req->input("bank_code", ""); // 입금은행

        $user_id = $req->input("user_id", ""); // 주문자 ID
        $user_nm = $req->input("user_nm", ""); // 주문자 이름
        $phone = $req->input("phone", ""); // 주문자 전화
        $mobile = $req->input("mobile", ""); // 주문자 휴대전화

        $r_nm = $req->input("r_user_nm", ""); // 수령자 이름
        $r_phone = $req->input("r_phone", ""); // 수령자 전화
        $r_mobile = $req->input("r_mobile", ""); // 수령자 휴대전화

        $r_zip_code = $req->input("r_zip_code", ""); // 수령 우편번호
        $r_addr1 = $req->input("r_addr1", ""); // 수령 주소1
        $r_addr2 = $req->input("r_addr2", ""); // 수령 주소2
        $dlv_msg = $req->input("dlv_msg", ""); // 출고메시지

        $give_point = $req->input("give_point", ""); // 적립금지급 여부
        $group_apply = $req->input("group_apply", "");
        $dlv_cd = $req->input("dlv_cd", ""); // 출고완료시 택배업체
        $dlv_no = $req->input("dlv_no", ""); // 출고완료시 송장번호

        $reservation_yn = $req->input("reservation_yn", 'N'); // 예약판매여부
        if ($ord_type == 4) $reservation_yn = 'Y';
        if ($reservation_yn === 'Y') $ord_type = 4; // 예약(4)

        try {
            DB::beginTransaction();

            $order_result = $this->_save_order([
                'cfg_ratio' => $cfg_ratio,
                'p_ord_opt_no' => $p_ord_opt_no,
                'ord_type' => $ord_type,
                'ord_kind' => $ord_kind,
                'ord_state' => $ord_state,
                'store_cd' => $store_cd,
                'cart' => $cart,
                'base_dlv_amt' => $base_dlv_amt,
                'free_dlv_amt' => $free_dlv_amt,
                'dlv_apply' => $dlv_apply,
                'add_dlv_fee' => $add_dlv_fee,
                'coupon_no' => $coupon_no,
                'pay_type' => $pay_type,
                'bank_inpnm' => $bank_inpnm,
                'bank_code' => $bank_code,
                'user_id' => $user_id,
                'user_nm' => $user_nm,
                'phone' => $phone,
                'mobile' => $mobile,
                'r_nm' => $r_nm,
                'r_phone' => $r_phone,
                'r_mobile' => $r_mobile,
                'r_zip_code' => $r_zip_code,
                'r_addr1' => $r_addr1,
                'r_addr2' => $r_addr2,
                'dlv_msg' => $dlv_msg,
                'give_point' => $give_point,
                'group_apply' => $group_apply,
                'dlv_cd' => $dlv_cd,
                'dlv_no' => $dlv_no,
                'user' => [
                    'id' => Auth('head')->user()->id,
                    'name' => Auth('head')->user()->name,
                ],
                'reservation_yn' => $reservation_yn,
            ]);

            if ($order_result['code'] != '200') {
                $code = $order_result['code'];
                if ($code == '-105') throw new Exception('재고 부족');
            }

            $ord_no = $order_result['ord_no'];

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $code = $code == '200' ? '500' : $code;
            $msg = $e->getTraceAsString();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg,
            "ord_no" => $ord_no,
        ]);
    }

    public function _save_order($data)
    {
        $code = '200';
        $cfg_ratio = $data['cfg_ratio'];
        $p_ord_opt_no = $data['p_ord_opt_no'];
        $ord_type = $data['ord_type']; // 출고형태
        $ord_kind = $data['ord_kind']; // 출고구분
        $ord_state = $data['ord_state']; // 주문상태
        $store_cd = $data['store_cd']; // 주문매장
        $store_nm = "본사"; // 주문매장명
        $give_point = "N"; // 적립금지급 여부
        if ($store_cd != '') {
            $row = DB::table('store')->select('store_nm', 'point_in_yn')->where('store_cd', '=', $store_cd)->first();
            if ($row != null) {
                $store_nm = $row->store_nm;
                $give_point = $row->point_in_yn;
            }
        }

        $cart = $data['cart']; // 상품정보

        $ord_amt = 0;
        $ord_qty = 0;
        $recv_amt = 0;
        $point_amt = 0;
        $coupon_amt = 0;
        $dc_amt = 0;
        $pay_fee = 0;
        $dlv_amt = 0;

        $base_dlv_amt = $data['base_dlv_amt']; // 기본배송비
        $free_dlv_amt = $data['free_dlv_amt']; // 배송비무료 금액
        $dlv_apply = $data['dlv_apply']; // 배송비적용 여부
        $add_dlv_fee = $data['add_dlv_fee']; // 추가배송비
        if($add_dlv_fee == '') $add_dlv_fee = 0;

        $coupon_no = $data['coupon_no'];
        $pay_type = $data['pay_type']; // 결제방법
        $bank_inpnm = $data['bank_inpnm']; // 입금자
        $bank_code = $data['bank_code']; // 입금은행
        $bank_number = ""; // 계좌번호
        if ($bank_code != "") {
            list($bank_code, $bank_number) = explode("_", $bank_code);
        }

        $user_id = $data['user_id']; // 주문자 ID
        $user_nm = $data['user_nm']; // 주문자 이름
        $phone = $data['phone']; // 주문자 전화
        $mobile = $data['mobile']; // 주문자 휴대전화
        $email = DB::table("member")->select("email")->where("user_id", "=", $user_id)->first(); // 주문자 이메일
        if($email != null) $email = $email->email;
        else $email = "";

        $r_nm = $data['r_nm']; // 수령자 이름
        $r_phone = $data['r_phone']; // 수령자 전화
        $r_mobile = $data['r_mobile']; // 수령자 휴대전화

        $r_zip_code = $data['r_zip_code']; // 수령 우편번호
        $r_addr1 = $data['r_addr1']; // 수령 주소1
        $r_addr2 = $data['r_addr2']; // 수령 주소2
        $dlv_msg = $data['dlv_comment']; // 출고메시지

        $group_apply = $data['group_apply'];
        $dlv_cd = $data['dlv_cd']; // 출고완료시 택배업체
        $dlv_no = $data['dlv_no']; // 출고완료시 송장번호

        $sale_kind = "00"; // 판매유형 (00: 일반판매_실)
        $pr_code = "01"; // 행사구분 (01: 정상_실)
        $user = $data['user'];

        $out_ord_no = $data['out_ord_no'] ?? '';
        $ord_date = $data['ord_date'] ?? date('Y-m-d H:i:s');
        $ord_state_date = date_format(date_create($ord_date), 'Ymd');
        $ord_date = date_format(date_create($ord_date), 'Y-m-d H:i:s');
        $fee_rate = $data['fee_rate'] ?? 0;

        $reservation_yn = $data['reservation_yn'] ?? 'N';
		
		$dlv_comment = $data['dlv_comment'] ?? '';

        ################################
        #	수기 주문번호 생성
        ################################
        $c_admin_id = $user['id'];
        $c_admin_name = $user['name'];

        ################################
        # 포인트 지급
        ################################
        $point_flag = false;
        $add_point_ratio = 0;
        $add_point = 0;

        if ($give_point == "Y") {
            // 회원 여부 확인
            $sql = " select count(*) as cnt from member where user_id = :user_id ";
            $row = DB::selectOne($sql, ["user_id" => $user_id]);

            if ($row->cnt == 1) {
                // 적립금 지급
                $point_flag = true;
                if ($group_apply == "Y") {
                    // 회원 그룹 추가 포인트
                    $sql = "
                        select a.group_no, b.point_ratio
                        from user_group_member a
                            inner join user_group b on a.group_no = b.group_no
                        where a.user_id = :user_id
                            order by b.point_ratio desc
                        limit 0,1
                    ";
                    $group = DB::selectOne($sql, ["user_id" => $user_id]);
                    if (!empty($group->point_ratio)) {
                        $add_point_ratio = $group->point_ratio;
                    }
                }
            }
        }

        ################################
        #	재고 수량 확인
        ################################

        $order_opt = [];

        for ($i = 0; $i < count($cart); $i++) {
            $goods_no = $cart[$i]['goods_no'] ?? '';
            $goods_sub = $cart[$i]['goods_sub'] ?? '';
            if(empty($goods_sub) || !is_numeric($goods_sub)) $goods_sub = 0;
            $goods_type = $cart[$i]['goods_type_cd'] ?? '';
            $goods_price = ($cart[$i]['price'] ?? 0) - ($cart[$i]['dc_amt'] ?? 0) - ($cart[$i]['coupon_amt'] ?? 0);
			$sugi_price = ($cart[$i]['price'] ?? 0) * 1;
            $point = $cart[$i]['point'] ?? '';
            $com_type = $cart[$i]['com_type'] ?? '';
            $prd_cd = $cart[$i]['prd_cd'] ?? '';
            $goods_opt = $cart[$i]['goods_opt'] ?? '';
            $qty = ($cart[$i]['qty'] ?? 0) * 1; // 판매수량
            $addopt_amt = $cart[$i]['addopt_amt'] ?? 0;
            $order_addopt_amt = $addopt_amt * $qty;

            $opt_ord_type = 14; // order_opt의 ord_type (수기판매:14 / 예약:4)

            // 옵션가격
            $a_goods_opt = explode("|", $goods_opt);
            $opt_amt = $cart[$i]["opt_amt"] ?? 0;
            $order_opt_amt = $opt_amt * $qty;

            $sql = "
                select
                    a.goods_nm, a.head_desc, a.md_id, a.md_nm, b.com_nm, a.com_id, a.baesong_kind,
                    a.baesong_price, b.pay_fee/100 as com_rate, a.com_type, a.goods_type, a.is_unlimited,
                    a.point_cfg, a.point_yn, a.point_unit, a.price, a.point, a.wonga, '' as margin_rate
                from goods a
                    left outer join company b on a.com_id  = b.com_id
                where a.goods_no = :goods_no
            ";
            $goods = DB::selectOne($sql, ["goods_no" => $goods_no]);

			$product = DB::table('product')->where('prd_cd', $prd_cd)->first();
			$product_price = $product->price ?? 0;
			$product_wonga = $product->wonga ?? 0;
			
			// 판매가 지정되지 않은 경우, 현재판매가로 설정
			if ($sugi_price == 0 || $sugi_price == '') {
				$sugi_price = $product_price;
			}

            // 위탁상품인 경우, 옵션가격이 있다면 수수료율에 맞춰 원가 재계산 > 정산 시 수수료율 보정
            if ($goods_type == "P" && ($opt_amt + $addopt_amt) > 0) {
                // $goods->wonga = ($goods_price + $opt_amt + $addopt_amt) * (1 - $goods->margin_rate / 100);
				$product_wonga = ($goods_price + $opt_amt + $addopt_amt) * (1 - $goods->margin_rate / 100);
            }

            $product_stock = 0;
            if ($store_cd != '') {
                $row = DB::table('product_stock_store')
                    ->select('wqty')->where('prd_cd', '=', $prd_cd)->where('store_cd', '=', $store_cd)
                    ->first();
            } else {
                $sql = "
                    select wqty
                    from product_stock_storage
                    where prd_cd = '$prd_cd' and storage_cd = (select storage_cd from storage where default_yn = 'Y')
                ";
                $row = DB::selectOne($sql);
            }
            if ($row != null) $product_stock = $row->wqty;

			// 예약판매가 아닐 경우에만 재고부족 에러처리
			
			// 본사에서 수기 일괄등록 매장재고 부족상품도 판매등록 가능하게 수정요청
			
//            if ($goods->is_unlimited == "Y") {
//                if ($product_stock < 1) {
//                    if ($reservation_yn === 'Y') {
//                        $opt_ord_type = 4; // order_opt의 ord_type (수기판매:14 / 예약:4)
//                    } else {
//                        $code = '-105';
//                        // throw new Exception("재고가 부족하여 수기판매 처리를 할 수 없습니다.");
//                    }
//                }
//            } else {
//                if ($qty >= 0 && $qty > $product_stock) {
//                    if ($reservation_yn === 'Y') {
//                        $opt_ord_type = 4; // order_opt의 ord_type (수기판매:14 / 예약:4)
//                    } else {
//                        $code = '-105';
//                        // throw new Exception("[상품코드 : $prd_cd] 재고가 부족하여 수기판매 처리를 할 수 없습니다.");
//                    }
//                }
//            }

			if ($qty === 0) $code = '-103'; // 판매수량 0일 경우 에러처리
            if ($code != '200') break;

            $com_rat = 0;

            if (isset($cart[$i]["coupon_no"])) {
                $coupon_no = $cart[$i]["coupon_no"];
                // 쿠폰정보 얻기
                $sql = "
                    select com_rat
                    from coupon_company
                    where coupon_no = :coupon_no and com_id = :com_id
                ";
                $coupon = DB::selectOne($sql, ["coupon_no" => $coupon_no, "com_id" => $goods->com_id]);
                if (!empty($coupon->com_rat)) {
                    $com_rat = $coupon->com_rat;
                }
            }

            $add_group_point = 0;
            if ($add_point_ratio > 0) {
                $add_group_point = ($goods_price * ($add_point_ratio / 100)) * $qty;
            }

            $ord_opt_add_point = 0;
            if ($point_flag) {
                if ($goods->point_yn == "Y") {
                    if ($goods->point_cfg == "G") {
                        if ($goods->point_unit == "P") {
                            $ord_opt_add_point = round(($goods_price * $goods->point / 100) * $qty, 0) + $add_group_point;
                        } else {
                            //echo "($goods->point * $qty) + $add_group_point;";
                            $ord_opt_add_point = ($goods->point * $qty) + $add_group_point;
                        }
                    } else {
                        // 쇼핑몰 설정
                        //echo "round(($cfg_ratio / 100) * $qty, 0) + $add_group_point";
                        $ord_opt_add_point = round(($goods_price * $cfg_ratio / 100) * $qty, 0) + $add_group_point;
                    }
                }
            }
            $add_point += $ord_opt_add_point;
            $ord_opt_point_amt = Lib::getValue($cart[$i], "point_amt", 0);
            $ord_opt_coupon_amt = Lib::getValue($cart[$i], "coupon_amt", 0);
            // $ord_opt_dc_amt = Lib::getValue($cart[$i], "dc_amt", 0);
			$ord_opt_dc_amt = ($product_price - $sugi_price) * $qty;
            $ord_opt_dlv_amt = Lib::getValue($cart[$i], "dlv_amt", 0);

            $a_ord_amt = $sugi_price * $qty;
            $a_recv_amt = $a_ord_amt;
            // $a_recv_amt = ($a_ord_amt - $ord_opt_point_amt - $ord_opt_coupon_amt - $ord_opt_dc_amt);
            if ($dlv_apply == 'N' || $a_ord_amt >= $free_dlv_amt) {
                $ord_opt_dlv_amt = 0;
            }

            array_push($order_opt, [
                    'goods_no' => $goods_no,
                    'goods_sub' => $goods_sub,
                    'ord_no' => '',
                    'ord_seq' => '0',
                    'head_desc' => $goods->head_desc,
                    'goods_nm' => $goods->goods_nm,
                    'goods_opt' => $goods_opt,
                    'qty' => $qty,
                    'wonga' => $product_wonga,
                    'price' => $sugi_price,
                    'dlv_amt' => $ord_opt_dlv_amt,
                    'pay_type' => $pay_type,
                    'point_amt' => $ord_opt_point_amt,
                    'coupon_amt' => $ord_opt_coupon_amt,
                    'dc_amt' => $ord_opt_dc_amt,
                    'opt_amt' => $order_opt_amt,
                    'addopt_amt' => $order_addopt_amt,
                    'recv_amt' => $a_recv_amt,
                    'p_ord_opt_no' => $p_ord_opt_no,
                    'dlv_no' => $dlv_no,
                    'dlv_cd' => $dlv_cd,
                    'md_id' => $goods->md_id,
                    'md_nm' => $goods->md_nm,
                    'sale_place' => $store_nm,
                    'ord_state' => $ord_state,
                    'clm_state' => 0,
                    'com_id' => $goods->com_id,
                    'add_point' => $ord_opt_add_point,
                    'ord_kind' => $ord_kind,
                    'ord_type' => $opt_ord_type,
                    'baesong_kind' => $goods->baesong_kind,
                    'dlv_start_date' => null,
                    'dlv_proc_date' => null,
                    'dlv_end_date' => null,
                    'dlv_cancel_date' => null,
                    'dlv_series_no' => null,
                    'ord_date' => $ord_date,
                    'dlv_comment' => $dlv_comment,
                    'admin_id' => $c_admin_id,
                    'coupon_no' => $coupon_no,
                    'com_coupon_ratio' => $com_rat,
                    'sales_com_fee' => round($a_ord_amt * $fee_rate / 100, 2),
                    'out_ord_opt_no' => $out_ord_no,
                    'prd_cd' => $prd_cd,
                    'store_cd' => $store_cd,
                    'sale_kind' => $cart[$i]["sale_kind_cd"] ?? $sale_kind,
                    'pr_code' => $cart[$i]["pr_code_cd"] ?? $pr_code,
            ]);
            $ord_amt += $order_opt[$i]["price"] * $order_opt[$i]["qty"];
			$ord_qty += $order_opt[$i]["qty"] * 1;
            $point_amt += $ord_opt_point_amt;
            $coupon_amt += $ord_opt_coupon_amt;
            $dc_amt += $ord_opt_dc_amt;
            // $dlv_amt += $ord_opt_dlv_amt;
            $recv_amt += $order_opt[$i]["recv_amt"];
        }

        if ($code != '200') {
            return ['code' => $code, 'ord_no' => ''];
        }

        $order = new Order($user, true);
        $ord_no = $order->ord_no;

        if ($dlv_apply == 'Y' && $ord_amt < $free_dlv_amt) {
            $dlv_amt = $base_dlv_amt;
        }

        DB::table('order_mst')->insert([
            'ord_no' => $ord_no,
            'ord_date' =>$ord_date,
            'user_id' => $user_id,
            'user_nm' => $user_nm,
            'phone' => $phone,
            'mobile' => $mobile,
            'email' => $email,
            'ord_amt' => $ord_amt,
            'point_amt' => $point_amt,
            'coupon_amt' => $coupon_amt,
            'dc_amt' => $dc_amt,
            'dlv_amt' => $dlv_amt,
            'add_dlv_fee' => $add_dlv_fee,
            'recv_amt' => $recv_amt + $dlv_amt + $add_dlv_fee - $point_amt - $coupon_amt - $dc_amt,
            'r_nm' => $r_nm,
            'r_zipcode' => $r_zip_code,
            'r_addr1' => $r_addr1,
            'r_addr2' => $r_addr2,
            'r_phone' => $r_phone,
            'r_mobile' => $r_mobile,
            'dlv_msg' => $dlv_msg,
            'ord_state' => $ord_state,
            'upd_date' => DB::raw('now()'),
            'dlv_end_date' => DB::raw('NULL'),
            'ord_type' => $ord_type,
            'ord_kind' => $ord_kind,
            'out_ord_no' => $out_ord_no,
            'store_cd' => $store_cd,
            'sale_place' => $store_nm,
            'chk_dlv_fee' => DB::raw('NULL'),
            'admin_id' => $c_admin_id
        ]);

        $pay_stat = 0;
        $tno = '';
        $pay_amt = $recv_amt + $dlv_amt + $add_dlv_fee - $point_amt - $coupon_amt - $dc_amt;

        ##################################################
        #	부모 결제 정보 복사
        ##################################################
        if ($p_ord_opt_no > 0) {
            $sql = /** @lang text */
                "
                select p.tno, p.pay_type, p.pay_stat, p.pay_amt
                from order_opt o inner join payment p on o.ord_no = p.ord_no
                where ord_opt_no = :ord_opt_no
            ";
            $row = DB::selectOne($sql, ["ord_opt_no" => $p_ord_opt_no]);
            if (!empty($row->pay_type)) {
                $ppay_type = $row->pay_type;
                if ($row->tno != "" && (($pay_type & $ppay_type) == $pay_type || ($pay_type & $ppay_type) == $ppay_type)) {
                    $tno = $row->tno;
                    $pay_amt = $row->pay_amt;
                    $pay_stat = $row->pay_stat;
                }
            }
        }

        $card_msg = "상품수기판매";
        DB::table('payment')->insert([
            "ord_no"		=> $ord_no,
            "pay_type" 		=> $pay_type,
            "pay_nm" 		=> $user_nm,
            "pay_amt" 		=> $pay_amt,
            "pay_stat" 		=> $pay_stat,
            "tno"           => $tno,
            "bank_inpnm" 	=> $bank_inpnm,
            "bank_code" 	=> $bank_code,
            "bank_number" 	=> $bank_number,
            "card_msg"      => $card_msg,
            "pay_ypoint"    => 0,
            "pay_point"     => $point_amt,
            "pay_baesong"   => $dlv_amt,
            "coupon_amt"    => $coupon_amt,
            "dc_amt"        => $dc_amt,
            //"pay_fee"       => $pay_fee,
            "ord_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
            "upd_dm"        => DB::raw('date_format(now(),\'%Y%m%d%H%i%s\')'),
        ]);

        for ($i = 0; $i < count($order_opt); $i++) {
            $order_opt[$i]["ord_no"] = $ord_no;
            DB::table('order_opt')->insert($order_opt[$i]);
            $ord_opt_no = DB::getPdo()->lastInsertId();
			
			// 수기판매 시, 판매상품별 메모등록
			$order_memo = $cart[$i]['memo'] ?? '';
			if ($order_memo !== '') {
				DB::table('order_opt_memo')->insert([
					'ord_opt_no' => $ord_opt_no,
					'ord_no' => $ord_no,
					'memo' => $order_memo,
					'admin_id' => $c_admin_id,
					'admin_nm' => $c_admin_name,
					'ut' => now(),
				]);
			}

            $goods_addopt = Lib::getValue($cart[$i], "goods_addopt", "");
            $a_goods_addopts = explode("^", $goods_addopt);

            foreach ($a_goods_addopts as $a_goods_addopt) {
                if (!empty($a_goods_addopt)) {
                    list($addopt_value, $addopt_goods_no, $addopt_goods_sub, $a_addopt_amt, $addopt_idx) = explode("|", $a_goods_addopt);
                    $a_addopt_amt = $a_addopt_amt * $order_opt[$i]["qty"];
                    DB::table('order_opt_addopt')->insert([
                        "ord_opt_no" => $ord_opt_no,
                        "goods_no" => $order_opt[$i]["goods_no"],
                        "goods_sub" => $order_opt[$i]["goods_sub"],
                        "addopt_idx" => $addopt_idx,
                        "addopt" => $addopt_value,
                        "addopt_amt" => $a_addopt_amt,
                        "addopt_qty" => $order_opt[$i]["qty"],
                    ]);
                }
            }

            #####################################################
            #	재고 처리
            #####################################################
            $is_store_order = true;
			$is_sugi = true;
            $order->SetOrdOptNo($ord_opt_no);
            $order->CompleteOrderSugi($ord_opt_no, $ord_state, $is_store_order, $is_sugi, $ord_state_date);

            if ($ord_state == "10" || $ord_state == "30") {

                // 상품배송완료인경우 상태 변경
                if ($ord_state == "30") {
                    // 송장 정보 등록
                    $order->DlvEnd($dlv_cd, $dlv_no, "30");

                    // 주문상태 로그
                    $state_log = array(
                        "ord_no" => $ord_no,
                        "ord_state" => "30",
                        "comment" => "수기판매",
                        "admin_id" => $user["id"],
                        "admin_nm" => $user["name"]
                    );
                    $order->AddStateLog($state_log);

                    //	order_opt_wonga 정산건 반영
                    $order->DlvLog("30");

                    // 추가 옵션 온라인 및 보유 재고 처리
                    $sql_addopt = "
                        select addopt_idx, addopt_qty
                        from order_opt_addopt
                        where ord_opt_no = :ord_opt_no
                            and goods_no = :goods_no
                            and goods_sub = :goods_sub
                    ";
                    $rows = DB::select($sql_addopt, [
                        "ord_opt_no" => $ord_opt_no,
                        "goods_no" => $order_opt[$i]["goods_no"],
                        "goods_sub" => $order_opt[$i]["goods_sub"],
                    ]);

                    foreach ($rows as $row) {
                        $addopt_qty = $row->addopt_qty;
                        DB::table('options')
                            ->where("no","=", $row->addopt_idx)
                            ->update([
                            "qty" => DB::raw("ifnull(qty, 0) - $addopt_qty"),
                            "wqty" => DB::raw("ifnull(wqty, 0) - $addopt_qty"),
                        ]);
                    }
                } else if ($ord_state == "10") { // 출고요청

                    if ($ord_kind != "30") { // 출고구분이 "보류"가 아닌경우 출고요청일 Update
                        //	주문상태 로그
                        $state_log = [
                            "ord_no" => $ord_no,
                            "ord_state" => "10",
                            "comment" => "수기판매",
                            "admin_id" => $user["id"],
                            "admin_nm" => $user["name"]
                        ];
                        $order->AddStateLog($state_log);
                    }

                    // 추가 옵션 온라인 및 보유 재고 처리
                    $sql_addopt = "
                        select addopt_idx, addopt_qty
                        from order_opt_addopt
                        where ord_opt_no = :ord_opt_no
                            and goods_no = :goods_no
                            and goods_sub = :goods_sub
                    ";
                    $rows = DB::select($sql_addopt, [
                        "ord_opt_no" => $ord_opt_no,
                        "goods_no" => $order_opt[$i]["goods_no"],
                        "goods_sub" => $order_opt[$i]["goods_sub"],
                    ]);

                    foreach ($rows as $row) {
                        $addopt_qty = $row->addopt_qty;
                        DB::table('options')
                            ->where("no","=", $row->addopt_idx)
                            ->update([
                                "qty" => DB::raw("ifnull(qty, 0) - $addopt_qty"),
                                "wqty" => DB::raw("ifnull(wqty, 0) - $addopt_qty"),
                            ]);
                    }
                }
            }
        }

        #####################################################
        #	포인트 지급
        #####################################################
        if ($ord_state != "1") {
            if ($point_flag === true && $user_id != null) {
                $point = new Point($user, $user_id);
                $point->SetOrdNo($ord_no);
                $point->StoreOrder();
            }
        }

        return ['code' => $code, 'ord_no' => $ord_no];
    }

    /**
     *
     * 수기 일괄등록
     *
     */

    /** 수기 일괄등록 화면 */
    public function batch_create()
    {
        $sql = "
            select
                concat(code_val,'_',ifnull(code_val2, '')) as 'name',
                concat(code_val,' [',ifnull(code_val2, ''),']') as 'value'
            from code
            where code_kind_cd ='BANK'
                and code_id != 'K'
                and use_yn = 'Y'
            order by code_seq
        ";
        $banks = DB::select($sql);

        $sql = "
            select
                code_id, code_val
            from code
            where code_kind_cd = 'G_PAY_TYPE'
                and code_id <> 'K'
                and code_id in ('1','2','5','9','13','16','32','64')
            order by code_seq
        ";
        $pay_types = DB::select($sql);

        $conf = new Conf();

        $values = [
            'ord_types'     => SLib::getCodes('G_ORD_TYPE'),
            'pay_types'     => $pay_types,
            'banks'         => $banks,
            'dlv_cds'       => SLib::getCodes('DELIVERY'),
            'dlv_fee'       => [
                'base_dlv_fee'  => $conf->getConfigValue('delivery', 'base_delivery_fee'),
                'add_dlv_fee'   => $conf->getConfigValue('delivery', 'add_delivery_fee'),
                'free_dlv_amt'  => $conf->getConfigValue('delivery', 'free_delivery_amt'),
            ],
        ];
        return view(Config::get('shop.store.view') . '/order/ord01_batch', $values);
    }

    /** Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환 */
    public function batch_import(Request $request)
    {
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => '0', 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();

				$save_path = "data/store/ord01/";
				$file_name = "${now}_${user_id}.${extension}";

                if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}

				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);

				return response()->json(['code' => '1', 'file' => $file], 200);
			}
		}
    }

    /** 수기 일괄등록 판매등록 */
	public function batch_add(Request $request)
	{
		$store_cd = $request->input('store_cd', '');
		$bank_code = $request->input('bank_code', '');
		$apy_fee = $request->input('apy_fee', 'false');
		$fee = $request->input('fee', 0);
		$orders = $request->input('orders', []);

		$conf = new Conf();
		$cfg_ratio = $conf->getConfigValue("point", "ratio");
		$ord_type = 14; // 출고형태: 수기판매
		$ord_kind = 20; // 출고구분: 출고가능
		$ord_state = 30; // 주문상태 : 출고완료
		$free_dlv_amt = $conf->getConfigValue('delivery', 'free_delivery_amt'); // 배송비무료 금액
		$dlv_apply = 'Y'; // 배송비적용
		$give_point = 'N'; // 적립금지급
		$group_apply = 'N';

		$success_list = [];
		$failed_list = [];
		$fail_check = true;

		DB::beginTransaction();
		
		foreach ($orders as $order) {
			$code = '200';
			$cart = $order['cart'] ?? [];

			try {
					// 상품정보 조회
					foreach ($cart as $key => $item) {
						if (!isset($item['prd_cd'])) {
							$fail_check = false;
							$code = '-101';
							throw new Exception('바코드 없음');
						} else if (!isset($item['qty'])) {
							$fail_check = false;
							$code = '-103';
							throw new Exception('수량정보 없음');
							// } else if (!isset($item['price'])) {
							//     $code = '-104';
							//     throw new Exception('판매가 부정확');
						} else {
							$sql = "
								select
									g.goods_no
									, g.goods_sub
									, g.goods_type as goods_type_cd
									, g.point
									, g.com_type
									, p.prd_cd
									, p.goods_opt
								from product_code p
									inner join goods g on g.goods_no = p.goods_no
								where p.prd_cd = :prd_cd
							";
							$product = DB::selectOne($sql, ['prd_cd' => $item['prd_cd']]);
	
							if ($product == null) {
								$fail_check = false;
								$code = '-102';
								throw new Exception('바코드 부정확');
							} else {
								$item['goods_no'] = $product->goods_no;
								$item['goods_sub'] = $product->goods_sub;
								$item['goods_type_cd'] = $product->goods_type_cd;
								$item['point'] = $product->point;
								$item['com_type'] = $product->com_type;
								$item['goods_opt'] = $product->goods_opt;
								$cart[$key] = $item;
							}
						}
					}

				$order_result = $this->_save_order([
					'cfg_ratio' => $cfg_ratio,
					'p_ord_opt_no' => '',
					'ord_type' => $ord_type,
					'ord_kind' => $ord_kind,
					'ord_state' => $ord_state,
					'store_cd' => $store_cd,
					'cart' => $cart,
					'base_dlv_amt' => $order['dlv_amt'] ?? 0,
					'free_dlv_amt' => $free_dlv_amt,
					'dlv_apply' => $dlv_apply,
					'add_dlv_fee' => $order['add_dlv_amt'] ?? 0,
					'coupon_no' => '',
					'pay_type' => $order['pay_type'] ?? 1, // 결제타입: (default)현금
					'bank_inpnm' => $order['bank_inpnm'] ?? '',
					'bank_code' => $bank_code,
					'user_id' => $order['user_id'] ?? '',
					'user_nm' => $order['user_nm'] ?? '',
					'phone' => $order['phone'] ?? '',
					'mobile' => $order['mobile'] ?? '',
					'r_nm' => $order['r_nm'] ?? '',
					'r_phone' => $order['r_phone'] ?? '',
					'r_mobile' => $order['r_mobile'] ?? '',
					'r_zip_code' => $order['r_zipcode'] ?? '',
					'r_addr1' => $order['r_addr1'] ?? '',
					'r_addr2' => $order['r_addr2'] ?? '',
					'dlv_msg' => $order['dlv_msg'] ?? '',
					'give_point' => $give_point,
					'group_apply' => $group_apply,
					'dlv_cd' => $order['dlv_cd'] ?? '',
					'dlv_no' => $order['dlv_no'] ?? '',
					'user' => [
						'id' => Auth('head')->user()->id,
						'name' => Auth('head')->user()->name,
					],
					'out_ord_no' => $order['out_ord_no'] ?? '',
					'ord_date' => $order['ord_date'] ?? date('Y-m-d'),
					'fee_rate' => $order['fee_rate'] ?? ($apy_fee == 'true' ? $fee : 0),
					'dlv_comment' => $order['dlv_comment'] ?? '',
				]);

				if ($order_result['code'] != '200') {
					$fail_check = false;
					$code = $order_result['code'];
					if ($code == '-103') throw new Exception('수량 부정확');
					if ($code == '-105') throw new Exception('재고 부족');
				}
				
					$order['order_no'] = $order_result['ord_no'];
					$order['code'] = $code;
					array_push($success_list, $order);
				
			} catch (Exception $e) {
				$fail_check = false;
				$order['code'] = $code;
				array_push($failed_list, $order);
			}
		}

		if ($fail_check) {
			DB::commit(); // 모든 주문이 성공한 경우에만 커밋
		} else {
			DB::rollback(); // 실패한 경우 롤백
		}

		return response()->json([
			"code" => $fail_check ? '200' : '500',
			"head" => [
				"success_total" => count($success_list),
				"failed_total" => count($failed_list),
			],
			"body" => [
				"success_list" => $success_list,
				"failed_list" => $failed_list
			]
		]);
	}

    /**
     * 전화번호 숫자에 '-' 넣어서 반환
     * - Parameters: $tel(전화번호)
     * - Returns: String
    */
    private function __replaceTel($tel)
    {
        $tel = trim($tel);
        if (strpos($tel, '-') === false) {
            $len = strlen($tel);
            if ($len == 9) {
                $patterns = array("/(\d{2})(\d{3})(\d{4})/");
                $replace = array("\\1-\\2-\\3");
                $tel = preg_replace($patterns, $replace, $tel);
            } else if ($len == 10) {
                if (substr($tel, 0, 2) == "02") {
                    $patterns = array("/(\d{2})(\d{4})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                } else {
                    $patterns = array("/(\d{3})(\d{3})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                }
            } else if ($len == 11) {
                if (substr($tel, 0, 4) == "0505") {
                    $patterns = array("/(\d{4})(\d{3})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                } else {
                    $patterns = array("/(\d{3})(\d{4})(\d{4})/");
                    $replace = array("\\1-\\2-\\3");
                    $tel = preg_replace($patterns, $replace, $tel);
                }
            } else if ($len == 12) {
                $patterns = array("/(\d{4})(\d{4})(\d{4})/");
                $replace = array("\\1-\\2-\\3");
                $tel = preg_replace($patterns, $replace, $tel);
            }
            return $tel;
        } else {
            return $tel;
        }
    }

    /**
     *
     * 주문상세내역
     *
     */
    public function show($ord_no, $ord_opt_no = '')
    {
        if ($ord_opt_no == '') {
            $sql = "
				select max(ord_opt_no) as ord_opt_no
				from order_opt
				where ord_no = '$ord_no'
			";
            $row = DB::selectOne($sql);
            if ($row != null) $ord_opt_no = $row->ord_opt_no;
        }

        $values = $this->_get($ord_no, $ord_opt_no);

        $sql = "
            select code_val id, code_val val
            from code
            where code_kind_cd = 'G_JAEGO_REASON' and use_yn = 'Y'
            order by code_seq asc
        ";
        $values['jaego_reasons'] = DB::select($sql);

        $values = array_merge($values, [
            'today'         => date("Y-m-d"),
            'cs_forms'		=> SLib::getCodes("CS_FORM2"),
            'clm_reasons'	=> SLib::getCodes("G_CLM_REASON"),
            'clm_states'	=> SLib::getCodes("G_CLM_STATE"),
            'dlv_cds'		=> SLib::getCodes("DELIVERY"),
			'refund_yn'		=> ''
        ]);

        return view(Config::get('shop.store.view') . '/order/ord01_detail', $values);
    }

    private function _get($ord_no, $ord_opt_no = '') {

        // 설정 값 얻기
        $conf = new Conf();

        $cfg_shop_name			= $conf->getConfigValue("shop","name");
        $cfg_sms_yn				= $conf->getConfigValue("sms","sms_yn");
        $cfg_delivery_yn		= $conf->getConfigValue("sms","delivery_yn");
        $cfg_domain_bizest		= $conf->getConfigValue("shop","domain_bizest");
        $cfg_img_size_detail	= SLib::getCodesValue("G_IMG_SIZE","detail");
        $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");
        $cfg_bank_code			= SLib::getCodes("G_BANK_CODE");

        // 현금영수증 사용여부 설정값 얻기
        $cfg_cash_use_yn		= $conf->getConfigValue("shop","cash_use_yn", "N");
        $isfind = false;

        $values = [
            'ord_no' => $ord_no,
            'ord_opt_no' => $ord_opt_no,
        ];

        if($ord_opt_no == ''){
            $sql = /** @lang text */
                "
				select max(ord_opt_no) as ord_opt_no
				from order_opt
				where ord_no = '$ord_no'
			";
            $row = DB::selectOne($sql);
            $ord_opt_no = $row->ord_opt_no;
        }

        if ($ord_opt_no != "") {
            ###################################################################
            #	기본 주문정보
            ###################################################################
            $sql = " /* admin : order/ord01.php (1) */
                select
                    a.ord_no,date_format(b.ord_date,'%Y.%m.%d %H:%i:%s') ord_date, a.ord_kind
                    , b.user_id, b.user_nm, b.phone, b.mobile, b.email
                    , b.r_nm, b.r_phone, b.r_mobile, b.r_zipcode, b.r_addr1, b.r_addr2
                    , b.dlv_msg, a.com_id, b.url, a.ord_state, ord_state.code_val ord_state_nm, a.ord_type, a.dlv_no /*송장번호*/
                    , c.code_val dlv_cd /*택배사*/,c.code_val2 dlv_homepage
                    , date_format(b.dlv_end_date,'%Y.%m.%d %H:%i:%s') mst_dlv_end_date, d.com_nm sale_place, b.sale_place sale_place_nm
                    , 0 as tax, b.dlv_amt, b.add_dlv_fee, a.add_point
                    , date_format(a.dlv_start_date,'%Y.%m.%d %H:%i:%s') dlv_start_date
                    , date_format(a.dlv_proc_date,'%Y.%m.%d %H:%i:%s') dlv_proc_date
                    , date_format(a.dlv_end_date,'%Y.%m.%d %H:%i:%s') dlv_end_date
                    , date_format(b.upd_date,'%Y.%m.%d %H:%i:%s') upd_date, a.dlv_comment, a.p_ord_opt_no
                    , company.com_type, com_type.code_val com_type_nm, company.com_nm, company.staff_nm1, company.last_login_date
                    , company.staff_phone1, company.staff_hp1
                    , company.zip_code, company.addr1, company.addr2
                    , company.r_zip_code as com_r_zip_code, company.r_addr1 as com_r_addr1, company.r_addr2  as com_r_addr2
                    , company.md_nm, company.memo as com_memo, a.price, a.dlv_pay_type
                    , m.memo as member_memo, 'Y' as taxpayer_yn,mu.name as seller
                    , a.store_cd, s.store_nm, a.pr_code, a.prd_cd
                from order_opt a
                    inner join order_mst b on a.ord_no = b.ord_no
                    left outer join code c on c.code_kind_cd = 'DELIVERY' and a.dlv_cd = c.code_id
                    left outer join company d on a.sale_place = d.com_id and d.com_type= '4'
                    left outer join company company on a.com_id = company.com_id
                    left outer join code com_type on com_type.code_kind_cd = 'G_COM_TYPE' and company.com_type = com_type.code_id
                    left outer join code ord_state on ord_state.code_kind_cd = 'G_ORD_STATE' and a.ord_state = ord_state.code_id
                    left outer join member m on b.user_id = m.user_id
                    left outer join mgr_user mu on b.admin_id = mu.id
                    left outer join store s on s.store_cd = a.store_cd
                where a.ord_opt_no = :ord_opt_no
            ";
            $isfind = true;
            $row = DB::selectOne($sql,array("ord_opt_no" => $ord_opt_no));

            $row->dlv_pay_type = $row->dlv_pay_type == "P" ? "선불" : "착불";
            $values['ord'] = $row;

            if($ord_no === "ord_no"){
                $ord_no = $row->ord_no;
                $values["ord_no"] = $ord_no;
            }
        }

        // 주문매장정보 조회
        $store_cd = $row->store_cd;
        $sql = "
            select
                a.store_cd, a.store_nm_s as store_nm
                , a.store_type, a.store_kind, a.zipcode, a.addr1, a.addr2
                , c.code_val as store_type_nm, d.code_val as store_kind_nm
            from store a
                left outer join code c on c.code_kind_cd = 'store_type' and c.code_id = a.store_type
                left outer join code d on d.code_kind_cd = 'store_kind' and d.code_id = a.store_kind
            where a.store_cd = '$store_cd'
        ";
        $store = DB::selectOne($sql);
        $values['store'] = $store;

        // 현금영수증 발행 내역
        if($cfg_cash_use_yn == "Y"){
            $sql = "
				select *
				from cash_history
				where ord_no = '$ord_no'
				order by rt desc
            ";
            $rows = DB::select($sql);
            $values['cash_history_cnt'] = count($rows);
            $values['cash_histories'] = $rows;
        }

        if ($isfind === false) return false;

        ###################################################################
        #	회원그룹 정보
        ###################################################################
        if (isset($values['ord']) && !empty($values['ord']->user_id) ) {
            $user_id = $values['ord']->user_id;

            $sql = /** @lang text */
                "
                select
                    a.group_no, b.group_nm
                from user_group_member a
                    inner join user_group b on a.group_no = b.group_no
                where a.user_id = '$user_id'
                order by b.dc_ratio desc, b.point_ratio desc
                limit 0,1
            ";
            $values['group'] = DB::selectOne($sql);
        }

        ###################################################################
        #	부모 주문건
        ###################################################################
        if(isset($values['ord']) && !empty($values['ord']->p_ord_opt_no)) {
            $p_ord_opt_no = $values['ord']->p_ord_opt_no;

            $sql = " /* admin : order/ord01_detail.php (2) */
                select ord_no from order_opt where ord_opt_no = '$p_ord_opt_no' order by ord_date desc
            ";
            $row = DB::selectOne($sql);

            if (!empty($row->ord_no)) $values['p_ord_no'] = $row->ord_no;
        }
        ###################################################################
        #	자식 주문건
        ###################################################################
        $sql = "select ord_no, ord_opt_no from order_opt where p_ord_opt_no = '$ord_opt_no'";

        $row = DB::selectOne($sql);

        if (!empty($row->ord_no)) {
            $values['c_ord_no'] = $row->ord_no;
            $values['c_ord_opt_no'] = $row->ord_opt_no;
        }

        ###################################################################
        #	자식 주문건
        ###################################################################
        $sql = /** @lang text */
            "
            select
                o.ord_opt_no, ord_state, o.clm_state
                , if(ifnull(o.clm_state, 0) = 0
                    , (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = o.ord_state)
                    , (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = o.clm_state)
                 ) as order_state
                , o.ord_kind
                , ord_kind.code_val as ord_kind_nm
                , o.ord_type
                , ord_type.code_val as ord_type_nm
                , if(g.com_type = 1, g.com_type, o.com_id) as com_id
                , if(g.com_type = 1, '$cfg_shop_name', cm.com_nm) as com_nm
                , o.head_desc, o.goods_nm, g.goods_no, g.goods_sub, g.style_no, replace(g.img,'$cfg_img_size_real','$cfg_img_size_detail') as img
                , o.goods_opt
                , replace(o.goods_opt,'^',' : ') as opt_val
                , o.qty,o.price
                , ifnull(
                    if( o.ord_state < 10, o.qty, (
                            select sum(qty) from order_opt_wonga where ord_opt_no = o.ord_opt_no and ord_state = 10
                        )
                    ), 0
                 ) as wqty
                , ifnull(
                    ( select sum(qty) from product_stock
                        where goods_no = g.goods_no and goods_opt = o.goods_opt
                    ), 0
                 ) as jaego_qty
                , ifnull(
                    ( select sum(wqty) from product_stock
                        where goods_no = g.goods_no and goods_opt = o.goods_opt
                    ), 0
                 ) as stock_qty
                , o.point_amt, o.coupon_amt,o.dc_amt * -1 as dc_amt, o.dlv_amt, o.recv_amt
			 	, ifnull(if(st.amt_kind = 'per', round(o.price * o.qty * st.sale_per / 100), st.sale_amt), 0) * -1 as sale_kind_amt
                , c.refund_amt, o.add_point
                , g.is_unlimited, g.goods_type
                , o.opt_amt, o.addopt_amt, o.dlv_comment
                ,( select point_status from point_list where ord_no = o.ord_no and ord_opt_no = o.ord_opt_no and point > 0 order by no desc limit 0,1 ) as point_status
                , om.state, om.memo
                , o.dlv_cd, o.dlv_no, dlv.code_val as dlv_nm, dlv.code_val2 as dlv_homepage
                , o.prd_cd, o.store_cd
                , '' as choice_class
            from order_opt o
                inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
                inner join company cm on o.com_id = cm.com_id
                left outer join claim c on o.ord_opt_no = c.ord_opt_no
                left outer join code ord_type on ord_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = ord_type.code_id
                left outer join code ord_kind on ord_kind.code_kind_cd = 'G_ORD_KIND' and o.ord_kind = ord_kind.code_id
                left outer join order_opt_memo om on o.ord_opt_no = om.ord_opt_no
                left outer join code dlv on dlv.code_kind_cd = 'DELIVERY' and o.dlv_cd = dlv.code_id
                left outer join sale_type st on st.sale_kind = o.sale_kind and st.use_yn = 'Y'
            where o.ord_no = '$ord_no' and g.goods_type <> 'O'
            order by com_id, o.ord_opt_no desc
        ";

        $rows = DB::select($sql);

        $sum_amt = 0;
        $sum_qty = 0;
        $sum_dlv_amt = 0;
        $sum_coupon_amt = 0;
        $sum_refund_amt = 0;
        $sum_add_point = 0;

        $sum_claim_amt = 0;			// 취소금액
        $sum_normal_amt = 0;		// 유효금액

        $pcom_id = "";
        $pcom_idx = 0;
        $com_cnt = 1;
        $com_dlv_amt = 0;
        $dlv_comment_cnt = 0;

        $goods_no = "";
        $goods_sub = "";
        $choice_goods_type = "";

        foreach($rows as $row) {
            if($ord_opt_no == $row->ord_opt_no){
                $values['goods_no'] = $row->goods_no;
                $values['goods_sub'] = $row->goods_sub;
                $choice_goods_type = $row->goods_type;
                $row->choice_class = "choice";
            }

            $sum_amt		+= $row->qty * $row->price;
            $sum_qty		+= $row->qty;
            $sum_dlv_amt	+= $row->dlv_amt;
            $sum_coupon_amt	+= $row->coupon_amt + $row->dc_amt;
            $sum_refund_amt	+= $row->refund_amt;
            $sum_add_point	+= $row->add_point;

            if( $row->clm_state == 0 ){	// 클레임이 없는 경우에만 금액 가산
                $sum_normal_amt += $row->qty * $row->price;
            } else {
                $sum_claim_amt += $row->qty * $row->price;
                //$sum_claim_amt += $row->recv_amt;		// 클레임 금액은 상품가격에서 적립금, 쿠폰, 할인을 제외한
            }

            $sql2 = /** @lang text */
                "
                select addopt, addopt_amt, addopt_qty
                from order_opt_addopt
                where ord_opt_no = '$row->ord_opt_no'
            ";

            $row->a_addopts = DB::select($sql2);
            if($row->dlv_comment != ""){
                $dlv_comment_cnt++;
            }

            // 업체별 배송비 처리
            if($pcom_id != $row->com_id){
                $row->dlv_grp_amt = $com_dlv_amt;
                $com_dlv_amt = $row->dlv_amt;
            } else {
                $com_dlv_amt += $row->dlv_amt;
            }

            $pcom_id = $row->com_id;
        }

        $values['ord_lists'] = $rows;

        ###################################################################
        #	결제정보
        ###################################################################
        $sql = " /* admin : order/ord01_detail.php (7) */
            select
                a.pay_type, pay_type.code_val pay_type_nm, '' as fintech, a.pay_amt, a.pay_point, a.pay_nm,
                a.pay_stat, pay_stat.code_val pay_stat_nm, a.bank_inpnm, a.bank_code, a.bank_number,
                a.card_code, a.card_isscode,
                a.card_quota, a.card_appr_no,
                date_format(a.card_appr_dm,'%Y.%m.%d %H:%i:%s') card_appr_dm, a.card_tid, a.tno, a.card_msg,
                date_format(a.ord_dm,'%Y.%m.%d %H:%i:%s') ord_dm, date_format(a.upd_dm,'%Y.%m.%d %H:%i:%s') upd_dm,
                a.pay_ypoint, a.pay_baesong, a.card_name, a.nointf, a.ghost_use, a.escw_use, a.tno,
                a.st_cd, ifnull(a.coupon_amt,0) coupon_amt,
                cr.bank_code as cr_bank_code,
                ifnull(a.dc_amt,0) as dc_amt,
                ifnull(a.cash_yn, 'N') as cash_yn, a.cash_date,
                ifnull(a.tax_yn, 'N') as tax_yn, a.tax_date,
                a.confirm_id, ma.name as confirm_nm, a.confirm_amt,
                0 as pay_fee
            from payment a
                left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
                left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STATE')
                left outer join common_return cr on cr.order_no = a.ord_no
                left outer join mgr_user ma on a.confirm_id = ma.id
            where a.ord_no = '$ord_no'
        ";

        $row = DB::selectOne($sql);

        if(!empty($row->fintech)){
            $row->pay_type_nm = sprintf("%s(%s)", $row->pay_type_nm, strtoupper($row->fintech));
        }

        if(isset($row->cr_bank_code) && isset($cfg_bank_code[$row->cr_bank_code])){
            $row->bank_code = $cfg_bank_code[$row->cr_bank_code];
        }

        $values['pay'] = $row;
        ###################################################################
        #	해외 배송 정보
        ###################################################################
        if($choice_goods_type == "O") {
            $sql = /** @lang text */
                "
                select c.code_val as local_ord_state_nm, a.local_state_date, a.comment, a.admin_nm, a.admin_id
                from order_oversea_state a
                    inner join code c on a.local_ord_state = c.code_id  and c.code_kind_cd = 'G_ORD_STATE'
                where ord_opt_no = '$ord_opt_no'
                order by ord_state_no
            ";

            $values['oversea_states'] = DB::select($sql);
        }

        ###################################################################
        #	주문상태 정보
        ###################################################################
        if (isset($values['ord']) && $values['ord']->ord_state > 1) {
            $sql = "
                select
                    a.p_ord_state
                    , b.code_val as p_ord_state_nm
                    , a.ord_state
                    , c.code_val as ord_state_nm
                    , a.admin_id
                    , a.admin_nm
                    , date_format(a.state_date,'%Y.%m.%d %H:%i:%s') as state_date
                    , a.comment
                from order_state a
                    inner join code b on a.p_ord_state = b.code_id and b.code_kind_cd = 'G_ORD_STATE'
                    inner join code c on a.ord_state = c.code_id and c.code_kind_cd = 'G_ORD_STATE'
                where a.ord_opt_no = '$ord_opt_no'
                order by state_date asc
            ";

            $values['state_logs'] = DB::select($sql);
        }

        ###################################################################
        #	클레임 정보
        ###################################################################
        $claimInfoSql = " /* admin : order/ord01_detail.php (9) */
            select
                clm_no, clm_state
                , clm_reason, refund_yn, refund_amt, refund_bank, refund_account, refund_nm, memo
                , date_format(req_date,'%Y.%m.%d %H:%i:%s') as req_date
                , date_format(proc_date,'%Y.%m.%d %H:%i:%s') as proc_date
                , date_format(end_date,'%Y.%m.%d %H:%i:%s') as end_date
                , req_nm,proc_nm,end_nm,date_format(last_up_date,'%Y.%m.%d %H:%i:%s') as last_up_date
                , dlv_deduct
            from claim
            where
                ord_opt_no = '$ord_opt_no'
        ";

        $values['claim_info'] = DB::selectOne($claimInfoSql);
        $values['clm_state'] = empty($values['claim_info']->clm_state) ? 0 : $values['claim_info']->clm_state;
        $values['ord_state'] = empty($values['claim_info']->ord_state) ? 0 : $values['claim_info']->ord_state;

        ###################################################################
        #	클레임 대상 리스트
        ###################################################################
        $array_claim = array();


        $sql = "
            select
                o.goods_no, o.goods_sub, o.goods_sub, g.goods_type,
                o.qty, o.price, o.goods_nm, o.clm_state,
                d.clm_det_no, d.clm_qty, d.jaego_yn, d.jaego_reason, d.stock_state,
                'Y' as stocked_yn,
                if(ifnull(o.clm_state, 0) = 0
                    , (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = o.ord_state)
                    , (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = o.clm_state)
                 ) as order_state
                 , o.ord_state, o.clm_state, o.ord_type
                 , o.recv_amt, o.dc_amt, o.point_amt, o.coupon_amt
            from order_opt o
                inner join goods g on g.goods_no = o.goods_no and g.goods_sub = o.goods_sub
                left outer join claim c on o.ord_opt_no = c.ord_opt_no
                left outer join claim_detail d on c.clm_no = d.clm_no
            where
                o.ord_opt_no = '$ord_opt_no'
        ";

        $values['order_opt'] = DB::selectOne($sql);

        ###################################################################
        #	클레임 내역 리스트 변경 : CS유형, 주문상태, 클레임상태 추가
        ###################################################################
        $sql = "
            select
                a.memo_no, a.admin_id, a.admin_nm
                , date_format(a.regi_date, '%y.%m.%d %H:%i:%s') as regi_date, a.memo
                , cd.code_val as cs_form
                , cd2.code_val as ord_state
                , if(cd3.code_id is not null,cd3.code_val,cd2.code_val) as clm_state
                , a.ord_opt_no
                , '' as alt
            from claim_memo a
                inner join order_opt b on a.ord_opt_no = b.ord_opt_no
                left outer join code cd on cd.code_kind_cd = 'CS_FORM2' and cd.code_id = a.cs_form
                left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_STATE' and cd2.code_id = a.ord_state
                left outer join code cd3 on cd3.code_kind_cd = 'G_CLM_STATE' and cd3.code_id = a.clm_state
            where b.ord_no = '$ord_no'
            order by a.regi_date asc
        ";
        $rows = DB::select($sql);

        foreach($rows as $idx => $row) {
            $row->memo = str_replace("\n","<br>",$row->memo);

            if( $ord_opt_no == $row->ord_opt_no ) {
                $choice_class	= "choice";
            }

            $row->alt = ($idx % 2 == 1) ? "alt" : "";
        }

        $values['claim_memos'] = $rows;

        if (isset($values['ord']) && $values['ord']->com_type == 2) {
            $sql = " /* admin : order/ord01_detail.php (12) */
                select
                     etc_day,etc_amt, etc_memo, regi_date, admin_nm, '' as alt
                from account_etc where ord_opt_no = '$ord_opt_no'
            ";

            $rows = DB::select($sql);

            foreach($rows as $idx => $row) {
                $row->alt = ($idx % 2 == 1) ? "alt" : "";
            }

            $values['account_etcs'] = $rows;
        }

        ###################################################################
        #	유입정보
        ###################################################################
        $sql = "
            select
                ifnull(t.vt,0) as vt, t.vc,datediff(t.rt,t.lvd) as vp, t.pageview, t.referer,
                e.code_val as type,	a.name, t.kw, t.point  , t.ad, o.referrer as track,
                t.browser, t.domain, t.agent, m.mobile_yn
            from order_track t
                left join ad a on a.ad= t.ad
                left outer join order_mst m on m.ord_no = t.ord_no
                left outer join order_opt o on o.ord_no = t.ord_no
                left outer join code e on e.code_id = a.type and e.code_kind_cd = 'G_AD_TYPE'
            where o.ord_opt_no = '$ord_opt_no'
        ";

        $row = DB::selectOne($sql);

        if (!empty($row->vt)){
            $row->visit_time = sprintf("%02d:%02d",floor($row->vt/60), $row->vt % 60);
        }

        $values['track'] = $row;

        ###################################################################
        #	사은품 정보
        ###################################################################
        $sql = "
            select a.no, a.ord_no, a.ord_opt_no,
                ifnull(a.give_yn, 'N') as give_yn,
                ifnull(a.give_date, '') as give_date,
                ifnull(a.refund_no, '0') as refund_no,
                ifnull(a.refund_yn, 'N') as refund_yn,
                ifnull(a.refund_date, '') as refund_date,
                a.admin_id, a.admin_nm, a.rt, a.ut,
                b.no as gift_no, b.name,
                ifnull(cd.code_val, '') as type_val,
                ifnull(cd2.code_val, '') as kind_val,
                b.type, b.kind, b.img, b.apply_amt,
                g.goods_no, g.goods_sub, g.goods_nm,
                '' as choice_class
            from order_gift a
                inner join gift b on a.gift_no = b.no
                inner join order_opt c on c.ord_opt_no = a.ord_opt_no
                inner join goods g on g.goods_no = c.goods_no and g.goods_sub = c.goods_sub
                left outer join code cd on cd.code_kind_cd = 'G_GIFT_TYPE' and cd.code_id = b.type
                left outer join code cd2 on cd2.code_kind_cd = 'G_GIFT_KIND' and cd2.code_id = b.kind
            where a.ord_no = '$ord_no'
            order by a.ord_opt_no desc
        ";
        $rows = DB::select($sql);

        foreach($rows as $row){
            if( $row->kind == "P" && $ord_opt_no == $row->ord_opt_no ) {
                $row->choice_class	= "choice";
            }

            $row->goods_snm = mb_substr($row->goods_nm, 0, 28);
        }

        $values['gifts'] = $rows;

        return $values;
    }

    /** 매장환불처리 */
    public function store_refund_save(Request $request)
    {
        $ord_opt_no = $request->input('ord_opt_no', '');
        $clm_reason = $request->input('store_clm_reason', '');
        $refund_bank = $request->input('store_refund_bank', '');
        $refund_nm = $request->input('store_refund_nm', '');
        $refund_account = $request->input('store_refund_account', '');
        $refund_memo = $request->input('store_refund_memo', '');

        $code = 200;
        $msg = '';
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        try {
            DB::beginTransaction();

            $sql = "
			    select
                    o.ord_no, o.ord_opt_no, o.prd_cd, o.goods_no, o.goods_sub, o.goods_opt
                    , o.qty, o.wonga, o.price, o.dc_amt, o.point_amt, o.recv_amt, o.ord_state
                    , o.coupon_amt, o.com_id, c.pay_fee as com_rate, o.ord_kind, o.ord_type
                    , o.com_coupon_ratio, o.coupon_no, o.sales_com_fee, o.dlv_amt, o.store_cd
                    , o.recv_amt as ref_amt, o.point_amt as refund_point_amt, o.add_point
                    , (o.price * o.qty) as refund_price, o.recv_amt as refund_amt
                    , m.user_id
                from order_opt o
                    inner join order_mst m on m.ord_no = o.ord_no
                    left outer join company c on o.com_id = c.com_id
                where ord_opt_no = :ord_opt_no
            ";
            $ord = DB::selectOne($sql, ['ord_opt_no' => $ord_opt_no]);

            if ($ord == null) throw new Exception("존재하지 않는 주문건입니다.");

            $success_code = 1;
            $claim = new Claim($user);
            $claim->SetOrdOptNo($ord_opt_no);

            $clm = [
                'clm_reason' 		=> $clm_reason,
                'refund_bank' 		=> $refund_bank,
                'refund_account' 	=> $refund_account,
                'refund_nm' 		=> $refund_nm,
                'memo' 				=> $refund_memo,
            ];

            // 클레임상태 등록
            $ord_wonga_no = $claim->UpdateStoreOrder($ord);
            $clm_no = $claim->InsertStoreClaim($clm, $ord);

            // 재고업데이트
            $update_stock = $claim->UpdateStoreStockToRefund($ord);

            if ($ord_wonga_no == 0 || $clm_no == 0 || $update_stock == 0) $success_code = 0;
            if ($success_code < 1) {
                if ($update_stock == 0) throw new Exception("매장 재고처리 중 오류가 발생했습니다. 해당 매장의 재고를 확인해주세요."); 
				else throw new Exception("환불처리 중 오류가 발생했습니다.");
            }

            // 포인트 환원 및 반납처리
            if($ord->user_id != null && $ord->refund_point_amt > 0) {
                $point = new Point($user, $ord->user_id);
                $point->SetOrdNo($ord->ord_no);
                $point->SetOrdOptNo($ord->ord_opt_no);

                // 포인트 환원
                $point->Cancel($ord->refund_point_amt);

                // 포인트 반납(차감)
                $point->Refund($ord->ord_opt_no, $ord->add_point, '61');
            }

            $msg = '매장환불처리가 완료되었습니다.';
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg], 200);
    }

    public function refund($ord_no, $ord_opt_no, Request $req) {
		// 설정 값 얻기
        $conf = new Conf();
		$cfg_shop_name				= $conf->getConfigValue("shop","name");
		$cfg_dlv_fee				= $conf->getConfigValue("delivery","base_delivery_fee");
		$cfg_free_dlv_fee_limit		= $conf->getConfigValue("delivery","free_delivery_amt");
        $cfg_add_dlv_fee			= $conf->getConfigValue("delivery","add_delivery_fee");

        $IsGroupDlv = true;

		// 환불계좌 입력 여부
        $isrefund_bank = "Y";

		$refund_bank = "";
		$refund_account = "";
		$refund_depositor = "";
        $p_ord_opt_no = "";
        $refunded_amt = 0;
        $pgcancelstate = "";
		// 주문 건수
        $sql = "select count(*) as cnt from order_opt where ord_no = '$ord_no'";

        $row = DB::selectOne($sql);

		$ord_cnt = $row->cnt;

		// 주문 & 입금정보
		$ordSql = "
            select
                a.ord_state,a.ord_amt, a.add_dlv_fee,
                b.pay_type, b.pay_nm, b.pay_amt, b.pay_point, b.pay_baesong, b.coupon_amt, b.dc_amt, 0 as pay_fee,
                b.bank_inpnm, bank_code, bank_number, tno, card_name, c.code_val as pay_name,
                b.escw_use,
                b.refund_bank, b.refund_account, b.refund_depositor
            from order_mst a
                inner join payment b on a.ord_no = b.ord_no
                left outer join code c on c.code_kind_cd = 'G_PAY_TYPE' and b.pay_type = c.code_id
            where a.ord_no = '$ord_no'
        ";

        $ord = DB::selectOne($ordSql);

        if (!empty($ord->ord_state)) {
            $ord_state = $ord->ord_state;
            $ord_amt = $ord->ord_amt;
            $add_dlv_fee = $ord->add_dlv_fee;

            $pay_amt = $ord->pay_amt;
            $pay_point = $ord->pay_point;
            $pay_baesong = $ord->pay_baesong;
            $coupon_amt = $ord->coupon_amt;
            $dc_amt = $ord->dc_amt;
            $pay_fee = $ord->pay_fee;

            $pay_type = $ord->pay_type;
            $pay_name = $ord->pay_name;
            $pay_nm = $ord->pay_nm;
            $card_name = $ord->card_name;
            $tno = $ord->tno;

            $pg = new Pay();
            $pgcancelstate = $pg->cancelstate($ord->pay_type, $ord->tno, $ord->card_name);

            if(($pay_type & 2) == 2){	// 카드
                $isrefund_bank = "N";
                $refund_bank = "";
                $refund_account = "";
            } else if(($pay_type & 16) == 16){	// 계좌이체 : PG 거래번호를 기본값으로 출력
                if($pgcancelstate > 0){
                    $isrefund_bank = "N";
                }

                $refund_bank = $ord->bank_code;
            } else if(($pay_type & 1) == 1 || ($pay_type & 64) == 64){	// 무통장 OR 가상계좌(에스크로)
                $refund_bank = $ord->refund_bank;
                $refund_account = $ord->refund_account;
                $pay_nm = $ord->refund_depositor;
            }
        } else {
			// 주문 정보 또는 입금 정보가 없는 경우는 에러처리!!!
        }

		//
		//	환불정보는 공유
		//

		$refund_no = "";

		$sql = "
			select refund_no
			from claim
			where ord_opt_no = '$ord_opt_no'
        ";

        $row = DB::selectOne($sql);

		if(!empty($row->refund_no)) {
			$refund_no = $row->refund_no;
		}

		// 환불정보
		$refundSql = "
			select
				clm_state,refund_price,refund_dlv_amt, refund_dlv_ret_amt, refund_dlv_enc_amt, refund_dlv_pay_amt,
				refund_point_amt, refund_coupon_amt, refund_etc_amt, 0 as refund_pay_fee, refund_gift_amt, refund_amt,
				refund_bank, refund_account, refund_nm, '' as refund_pay_fee_yn
			from claim a
			where a.ord_opt_no = '$refund_no'
        ";

		// 환불금액
		if($tno != ""){
			$sql = "
				select sum(ifnull(c.refund_amt,0)) as refunded_amt
				from (
					select ord_no
					from payment where tno = '$tno' and ord_no <> ''
				) a inner join order_opt o on a.ord_no = o.ord_no
					inner join claim c on o.ord_opt_no = c.ord_opt_no
				where c.clm_state = 61
			";
		} else {
			$sql = "
				select
					sum(ifnull(d.refund_amt,0)) as refunded_amt
				from order_opt a inner join claim d on a.ord_opt_no = d.ord_opt_no
				where a.ord_no = '$ord_no'
			";
        }

        $row = DB::selectOne($sql);

        if (!empty($row->refunded_amt)) {
            $refunded_amt = $row->refunded_amt;
        }

		// 그룹 주문
        $sum_dlv_amt = 0;

		if($IsGroupDlv){
			$sql = "
				select
					if(g.com_type = 1, g.com_type, a.com_id) as com_id,
					count(*) as cnt,
					sum(a.dlv_amt) as dlv_amt,
					sum(ifnull(c.dlv_add_amt,0)) as dlv_add_amt
				from order_opt a
					inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
						and g.goods_type <> 'O'
					left outer join claim c on a.ord_opt_no = c.ord_opt_no
				where a.ord_no = '$ord_no'
				group by if(g.com_type = 1, g.com_type, a.com_id)
			";
			$rows = DB::select($sql);

			$group_dlv = array();

			foreach ($rows as $row) {
				$group_dlv[$row->com_id]["cnt"] = $row->cnt;
				$group_dlv[$row->com_id]["dlv_amt"] = $row->dlv_amt;
				$group_dlv[$row->com_id]["dlv_add_amt"] = $row->dlv_add_amt;
				$sum_dlv_amt += $row->dlv_amt;
			}
		} else {

			$sql = "
				select
					count(*) as cnt,
					sum(a.dlv_amt) as dlv_amt,
					sum(ifnull(c.dlv_add_amt,0)) as dlv_add_amt
				from order_opt a
					inner join goods g
						on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub and g.goods_type <> 'O'
					left outer join claim c on a.ord_opt_no = c.ord_opt_no
				where a.ord_no = '$ord_no'
            ";
            $row = DB::selectOne($sql);

			$group_dlv = array();
			$group_dlv["1"]["cnt"] = $ord_cnt;
			$group_dlv["1"]["dlv_amt"] = $pay_baesong;
			$group_dlv["1"]["dlv_add_amt"] = $row->dlv_add_amt;
		}

		// 주문 상품
		$sql = "
            select
                a.ord_opt_no, a.p_ord_opt_no, a.ord_state, a.clm_state,
                if(ifnull(a.clm_state,0) = 0,
                    (select code_val from code where code_kind_cd = 'G_ORD_STATE' and code_id = a.ord_state),
                    (select code_val from code where code_kind_cd = 'G_CLM_STATE' and code_id = a.clm_state)
                ) as state,
                if(g.com_type = 1, g.com_type, a.com_id) as com_id,
                if(g.com_type = 1, '$cfg_shop_name',e.com_nm) as com_nm,
                a.goods_nm,
                replace(a.goods_opt, '^',':') as opt_nm,
                a.price, a.qty, a.price * a.qty as amt,
                a.coupon_amt , a.dc_amt, 0 as pay_fee,
                ifnull(a.dlv_amt, 0) as dlv_amt,
                ifnull(d.dlv_type, '') as clm_clm_dlv_type,
                ifnull(d.dlv_cm, '') as clm_dlv_cm,
                ifnull(d.dlv_amt, '') as clm_dlv_amt,
                ifnull(d.dlv_ret_amt, '') as clm_dlv_ret_amt,
                ifnull(d.dlv_add_amt, '') as clm_dlv_add_amt,
                ifnull(d.dlv_enc_amt, '') as clm_dlv_enc_amt,
                ifnull(d.dlv_pay_amt, '') as clm_dlv_pay_amt,
                ifnull(d.ref_amt, 0) as ref_amt,
                ifnull(d.refund_no, 0) as refund_no,
                ifnull(d.refund_amt, '') as refund_amt,
                g.goods_type,
                ifnull(e.dlv_policy,'S') as com_dlv_policy,
                ifnull(e.dlv_amt, 0) as com_dlv_amt,
                ifnull(e.free_dlv_amt_limit, 0) as com_free_dlv_amt_limit
            from order_opt a
                inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
                inner join company e on a.com_id = e.com_id
                left outer join coupon b on a.coupon_no = b.coupon_no
                left outer join claim d on a.ord_opt_no = d.ord_opt_no
            where a.ord_no = '$ord_no'
            order by com_id,ord_opt_no desc
        ";

        $rows = DB::select($sql);
		$prds = array();
        $pre_com_id = "";
        $s_prd = null;
        foreach($rows as $row) {
            $class = "";

			if($row->ord_opt_no == $ord_opt_no){
				$class ="choice";
                $p_ord_opt_no = $row->p_ord_opt_no;
                $s_prd = $row;
			}

			// 배송비 및 열수
			if($IsGroupDlv){
				$com_id = $row->com_id;

				if($sum_dlv_amt == 0 && $pay_baesong > 0){
					$dlv_amt = $pay_baesong;
				} else {
					//$dlv_amt = $row->dlv_amt;
					$dlv_amt = $group_dlv[$com_id]["dlv_amt"]; // 2008-07-18 : 그룹 배송 처
				}
				$dlv_grp_amt = $group_dlv[$com_id]["dlv_amt"];
				$dlv_grp_add_amt = $group_dlv[$com_id]["dlv_add_amt"];
				$dlv_grp_cnt = ($pre_com_id != $row->com_id)? $group_dlv[$com_id]["cnt"]:"";
			} else {
				$com_id = "1";
				$dlv_amt = $pay_baesong;
				$dlv_grp_amt = $group_dlv[$com_id]["dlv_amt"];
				$dlv_grp_add_amt = $group_dlv[$com_id]["dlv_add_amt"];
				$dlv_grp_cnt = $ord_cnt;
			}

			array_push($prds,
				array(
					"class"				=> $class,
					"refund_no"			=> $row->refund_no,
					"ord_opt_no"		=> $row->ord_opt_no,
					"ord_state"			=> $row->ord_state,
					"clm_state"	 		=> $row->clm_state,
					"state"				=> $row->state,
					"com_id"			=> $com_id,
					"com_nm"			=> $row->com_nm,
					"goods_nm"			=> $row->goods_nm,
					"goods_snm"			=> $row->goods_nm,
					"opt_nm"			=> $row->opt_nm,
					"price"				=> $row->price,
					"qty"				=> $row->qty,
					"amt"				=> $row->amt,
					"dc_amt"			=> $row->dc_amt,
					"refunded_amt"		=> $refunded_amt,
					"pay_fee"			=> $row->pay_fee,
					// "coupon_amt"		=> $row->coupon_amt+$row->dc_amt,
					"coupon_amt"		=> $row->coupon_amt,
					"dlv_amt"			=> $dlv_amt,
					"dlv_grp_cnt"		=> $dlv_grp_cnt,
					"dlv_grp_amt"		=> $dlv_grp_amt,
					"dlv_grp_add_amt"	=> $dlv_grp_add_amt,
					"clm_clm_dlv_type"		=> $row->clm_clm_dlv_type,
					"clm_dlv_cm"		=> $row->clm_dlv_cm,
					"clm_dlv_amt"		=> $row->clm_dlv_amt,
					"clm_dlv_ret_amt"	=> $row->clm_dlv_ret_amt,
					"clm_dlv_add_amt"	=> $row->clm_dlv_add_amt,
					"clm_dlv_enc_amt"	=> $row->clm_dlv_enc_amt,
					"clm_dlv_pay_amt"	=> $row->clm_dlv_pay_amt,
					"ref_amt"			=> $row->ref_amt,
					"goods_type"		=> $row->goods_type,
					"com_dlv_policy"	=> $row->com_dlv_policy,
					"com_dlv_amt"		=> $row->com_dlv_amt,
                    "com_dlv_amt_free_limit"	=> $row->com_free_dlv_amt_limit,
                    "refund_amt"        => $row->refund_amt
				)
			);

			$pre_com_id = $com_id;
        }

		###################################################################
		#	사은품 정보
        ###################################################################
		$array_gift = array();
		$sql = "
			select a.no, a.ord_no, a.ord_opt_no,
				ifnull(a.give_yn, 'N') as give_yn,
				ifnull(a.give_date, '') as give_date,
				ifnull(a.refund_no, '0') as refund_no,
				ifnull(a.refund_yn, 'N') as refund_yn,
				ifnull(a.refund_amt, '0') as refund_amt,
				ifnull(a.refund_date, '') as refund_date,
				a.admin_id, a.admin_nm, a.rt, a.ut,
				b.no as gift_no, b.name, b.type, b.kind, b.refund_yn as g_refund_yn,
				ifnull(cd.code_val, '') as type_val,
				ifnull(cd2.code_val, '') as kind_val,
				b.img, b.apply_amt, 0 as gift_price,
				g.goods_no, g.goods_sub, g.goods_nm
			from order_gift a
				inner join gift b on a.gift_no = b.no
				inner join order_opt c on c.ord_opt_no = a.ord_opt_no
				inner join goods g on g.goods_no = c.goods_no and g.goods_sub = c.goods_sub
				left outer join code cd on cd.code_kind_cd = 'G_GIFT_TYPE' and cd.code_id = b.type
				left outer join code cd2 on cd2.code_kind_cd = 'G_GIFT_KIND' and cd2.code_id = b.kind
			where a.ord_no = '$ord_no'
			order by b.kind desc, b.apply_amt desc, a.ord_opt_no desc
        ";

        $rows = DB::select($sql);

		foreach($rows as $row){
			$order_gift_no		= $row->no;
			$gift_no			= $row->gift_no;
			$gift_nm			= $row->name;
			$gift_type			= $row->type;
			$gift_type_val		= $row->type_val;
			$gift_kind			= $row->kind;
			$gift_kind_val		= $row->kind_val;
			$gift_img			= $row->img;
			$gift_apply_amt		= $row->apply_amt;
			$g_refund_yn		= $row->g_refund_yn;
			$gift_give_yn		= $row->give_yn;
			$gift_give_date		= $row->give_date;
			$gift_refund_no		= $row->refund_no;
			$gift_refund_yn		= $row->refund_yn;
			$gift_refund_amt	= $row->refund_amt;
			$gift_refund_date	= $row->refund_date;
			$gift_goods_no		= $row->goods_no;
			$gift_goods_sub		= $row->goods_sub;
			$gift_goods_nm		= $row->goods_nm;
			$gift_price			= $row->gift_price;

			$_ord_opt_no = $row->ord_opt_no;

			$choice_class = "";
			if( $gift_kind == "P" && $ord_opt_no == $_ord_opt_no ) {
				$choice_class	= "choice";
			}

			array_push($array_gift, array(
				"order_gift_no"	=> $order_gift_no,
				"gift_no"		=> $gift_no,
				"name"			=> $gift_nm,
				"type"			=> $gift_type,
				"type_val"		=> $gift_type_val,
				"kind"			=> $gift_kind,
				"kind_val"		=> $gift_kind_val,
				"img"			=> $gift_img,
				"apply_amt"		=> $gift_apply_amt,
				"g_refund_yn"	=> $g_refund_yn,
				"give_yn"		=> $gift_give_yn,
				"give_date"		=> $gift_give_date,
				"refund_no"		=> $gift_refund_no,
				"refund_yn"		=> $gift_refund_yn,
				"refund_amt"	=> $gift_refund_amt,
				"refund_date"	=> $gift_refund_date,
				"ord_opt_no"	=> $_ord_opt_no,
				"ord_no"		=> $ord_no,
				"goods_no"		=> $gift_goods_no,
				"goods_sub"		=> $gift_goods_sub,
				"goods_nm"		=> $gift_goods_nm,
				"goods_snm"		=> $gift_goods_nm,
				"choice_class"	=> $choice_class,
				"gift_price"	=> $gift_price
			));
        }

        $values = [
            "g_dlv_fee"             => Lib::CheckInt($cfg_dlv_fee),
            "g_dlv_add_fee"         => Lib::CheckInt($cfg_add_dlv_fee),
            "g_free_dlv_fee_limit"  => Lib::CheckInt($cfg_free_dlv_fee_limit),

            'ord_no'		=> $ord_no,
            'ord_opt_no'	=> $ord_opt_no,
            'refund_no'		=> $refund_no,
            'ord'			=> $ord,
            'refund'		=> DB::selectOne($refundSql),
            'group_dlv'		=> $group_dlv,
            'prds'			=> $prds,
            'gifts'			=> $array_gift,
            'p_ord_opt_no'	=> $p_ord_opt_no,
            'ord_cnt'		=> $ord_cnt,
            'refunded_amt'	=> $refunded_amt,
            's_prd'			=> $s_prd,
            'refund_bank'	=> $refund_bank,
            'refund_account'=> $refund_account,
            'pay_nm'		=> $pay_nm,
            'pgcancelstate'	=> $pgcancelstate,
            'isrefund_bank'	=> $isrefund_bank,
            'escw_show'		=> ( ($ord->escw_use == "O" || $ord->escw_use == "Y") && $ord->pay_amt >= 100000 ) ? "" : "none"
        ];

        // dd($values);
        return view( Config::get('shop.store.view') . '/order/ord01_refund',$values);
    }

    /** 예약판매상품 지급완료처리 (예약주문건 정상주문처리) */
    public function complete_reservation(Request $request)
    {
        $ord_no = $request->input('ord_no', '');
        $ord_opt_no = $request->input('ord_opt_no', '');
        $ord_type = 15; // 정상:15

        $code = 200;
        $msg = '';

        try {
            DB::beginTransaction();

            $order = DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->first();
            $stock_amt = DB::table('product_stock_store')->where('store_cd', $order->store_cd)->where('prd_cd', $order->prd_cd)->value('wqty');

            if (is_null($stock_amt) || $stock_amt < 0) {
                $code = 404;
                throw new Exception('매장 보유재고가 0개 이상일 때만 예약상품지급이 가능합니다. 해당상품의 현재 보유재고는 ' . ($stock_amt ?? '-') . '개 입니다.');
            }

            DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->update([ 'ord_type' => $ord_type ]);
            DB::table('order_opt_wonga')->where('ord_opt_no', $ord_opt_no)->update([ 'ord_type' => $ord_type ]);

            $reservation_ord_cnt = DB::table('order_opt')->where('ord_no', $ord_no)->where('ord_type', 4)->count();
            if ($reservation_ord_cnt < 1) {
                DB::table('order_mst')->where('ord_no', $ord_no)->update([ 'ord_type' => $ord_type ]);
            }

            DB::commit();
            $msg = '예약판매상품이 지급완료처리되었습니다.';
        } catch (Exception $e) {
            DB::rollback();
            if ($code === 200) $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'msg' => $msg], 200);
    }
}
