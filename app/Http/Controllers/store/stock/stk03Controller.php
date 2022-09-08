<?php

namespace App\Http\Controllers\store\stock;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Conf;

class stk03Controller extends Controller
{
    public function index()
	{
		$values = [
            'sdate'         => now()->sub(3, 'month')->format('Y-m-d'),
            'edate'         => date("Y-m-d"),
            'style_no'      => '',
            'ord_states' => SLib::getordStates(), // 주문상태
            'clm_states' => SLib::getCodes('G_CLM_STATE'), // 클레임상태
            'stat_pay_types' => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
            'ord_types' => SLib::getCodes('G_ord_TYPE'), // 주문구분
            'ord_kinds' => SLib::getCodes('G_ord_KIND'), // 출고구분
            'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'), // 상품상태
			'items'			=> SLib::getItems(), // 품목
		];

        return view(Config::get('shop.store.view') . '/stock/stk03', $values);
	}

    public function search(Request $request)
    {
        $sdate          = $request->input('sdate', now()->sub(3, 'month')->format('Ymd'));
        $edate          = $request->input('edate', date('Ymd'));
        $nud            = $request->input('nud', ''); // 주문일자 검색여부
        $ord_no         = $request->input('ord_no', '');
        $store_no       = $request->input('store_no', '');
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
        $goods_stat     = $request->input('goods_stat', []);
        $item           = $request->input('item', '');
        $brand_cd       = $request->input('brand_cd', '');
        $goods_nm_eng   = $request->input('goods_nm_eng', '');
        $com_cd         = $request->input('com_cd', '');
        $com_nm         = $request->input('com_nm', '');
        $limit          = $request->input('limit', 100);
        $ord            = $request->input('ord', 'desc');
        $ord_field      = $request->input('ord_field', 'o.ord_date');
        $page           = $request->input('page', 1);
        if ($page < 1 or $page == '') $page = 1;

        // $mobile_yn      = $request->input('mobile_yn', '');  // 모바일 주문 여부
        // $app_yn         = $request->input('app_yn', '');    // 앱 주문 여부
        // $receipt        = $request->input('receipt', 'N');  // 현금영수증 : N(미신청), R(신청), Y(발행)
        // $dlv_type       = $request->input('dlv_type', '');  // 배송방식: D(택배), T(택배(당일배송)), G(직접수령)
        // $pay_fee        = $request->input('pay_fee', '');  // 결제수수료 주문
        // $fintech        = $request->input('fintech', '');  // 핀테크

        $where = "";
        $where .= " and o.ord_kind != '10' "; // 정상판매건이 아닌 경우에만 출력
        
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
                    $where .= " and date_format($ord_info_keyl, '%Y%m%d') = $ord_info_value ";
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
                $in_prd_cds = join(',', $prd_cds);
                $where .= " and o.prd_cd in ($in_prd_cds) ";
            } else {
                $where .= " and o.prd_cd = '$prd_cd' ";
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
        if (count($goods_stat) > 0) {
            if (count($goods_stat) == 1 && $goods_stat[0] != '') {
                $where .= " and g.sale_stat_cl = '" . $goods_stat[0] . "' ";
            } else {
                $in_goods_stats = join(',', $goods_stat);
                $where .= " and g.sale_stat_cl in ($in_goods_stats) ";
            }
        }
        if ($item != '') $where .= " and g.opt_kind_cd = '$item' ";
        if ($brand_cd != '') $where .= " and g.brand = '$brand_cd' ";
        if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%$goods_nm_eng%' ";
        if ($com_cd != '') $where .= " and g.com_id = '$com_cd' ";
        else if ($com_nm != '') $where .= " and g.com_nm = '$com_nm' ";

        // ordreby
        $orderby = sprintf("order by %s %s", $ord_field, $ord);

        // pagination
        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $sql = "
            select
                a.ord_no,
                a.ord_opt_no,
                ord_state.code_val as ord_state,
                clm_state.code_val as clm_state,
                pay_stat.code_val as pay_stat,
                a.prd_cd,
                a.goods_no,
                a.style_no,
                a.goods_type,
                ifnull(gt.code_val, 'N/A') as goods_type_nm,
                a.goods_nm,
                replace(a.goods_opt, '^', ' : ') as opt_val,
                a.qty,
                concat(a.user_nm, ' (', a.user_id, ')') as user_nm,
                a.r_nm,
                a.goods_price,
                a.price,
                a.dlv_amt,
                a.sales_com_fee,
                pay_type.code_val as pay_type,
                ord_type.code_val as ord_type,
                ord_kind.code_val as ord_kind,
                a.store_cd,
                a.store_nm,
                baesong_kind.code_val as baesong_kind,
                a.dlv_no,
                dlv_cd.code_val as dlv_cm,
                a.state,
                a.memo,
                a.ord_date,
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
                    p.prd_cd,
                    g.goods_no,
                    g.style_no,
                    g.goods_type,
                    g.goods_nm,
                    p.goods_opt,
                    o.qty,
                    om.user_id,
                    om.user_nm,
                    om.r_nm,
                    g.price as goods_price,
                    o.price,
                    o.dlv_amt,
                    o.sales_com_fee,
                    pay.pay_type,
                    o.ord_type,
                    o.ord_kind,
                    o.store_cd,
                    s.store_nm,
                    g.baesong_kind as dlv_baesong_kind,
                    o.dlv_no,
                    o.dlv_cd,
                    m.state,
                    m.memo,
                    o.ord_date,
                    pay.pay_date,
                    o.dlv_end_date,
                    c.last_up_date,
                    (select count(*) from order_opt where ord_no = o.ord_no and ord_opt_no != o.ord_opt_no and (ord_state > 10 or clm_state > 0)) as ord_opt_cnt
                from order_opt o
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join product_code p on o.prd_cd = p.prd_cd
                    inner join goods g on p.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join store s on o.store_cd = s.store_cd
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
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
                left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
                left outer join code dlv_cd on (a.dlv_cd = dlv_cd.code_id and dlv_cd.code_kind_cd = 'DELIVERY')
                left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
        ";
        $result = DB::select($sql);

        // pagination
        $total = 0;
        $page_cnt = 0;
        if($page == 1) {
            $sql = "
                select count(*) as total
                from order_opt o
                    inner join order_mst om on o.ord_no = om.ord_no
                    inner join product_code p on o.prd_cd = p.prd_cd
                    inner join goods g on p.goods_no = g.goods_no
                    left outer join payment pay on om.ord_no = pay.ord_no
                    left outer join store s on o.store_cd = s.store_cd
                    left outer join claim c on c.ord_opt_no = o.ord_opt_no
                    left outer join order_opt_memo m on o.ord_opt_no = m.ord_opt_no
                where 1=1 $where
            ";

            $row = DB::selectOne($sql);
            $total = $row->total;
            $page_cnt = (int)(($total - 1) / $page_size) + 1;
        }

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ],
            "body" => $result,
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
        return view(Config::get('shop.store.view') . '/stock/stk03_show', $values);
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
}
