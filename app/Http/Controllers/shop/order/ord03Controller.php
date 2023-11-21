<?php

namespace App\Http\Controllers\shop\order;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Conf;
use App\Models\Order;
use App\Exports\ExcelExport;
use App\Models\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

const PRODUCT_STOCK_TYPE_STORE_IN = 1; // (매장)입고
const PRODUCT_STOCK_TYPE_STORE_SALE = 2; // (매장)주문
const PRODUCT_STOCK_TYPE_STORE_RT = 15; // (매장)RT
const PRODUCT_STOCK_TYPE_STORAGE_OUT = 17; // (창고)출고

/** 온라인 배송처리 */
class ord03Controller extends Controller
{
	private $dlv_place_type = 'store';

	public function index() 
	{
		$sdate = Carbon::now()->sub(3, 'day')->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");
		$store_cd = Auth('head')->user()->store_cd;

		$sale_places_sql = "select com_id as id, com_nm as val from company where com_type = '4' and use_yn = 'Y' order by com_nm";
		$sale_places = DB::select($sale_places_sql);

		$dlv_locations = $this->_get_dlv_locations($store_cd);
		$dlv_companies = DB::table('code')->where('code_kind_cd', 'DELIVERY')->select('code_id as id', 'code_val as label')->get();

		$conf   = new Conf();
        $cfg_dlv_cd = $conf->getConfigValue("delivery","dlv_cd");

		$values = [
            'sdate'         	=> $sdate,
			'edate'         	=> $edate,
			'dlv_locations'		=> $dlv_locations, // 배송처
			'dlv_types'			=> SLib::getCodes('G_DLV_TYPE'), // 배송방식
			'sale_places'		=> $sale_places, // 판매처
            'stat_pay_types'    => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
            'sale_kinds'        => SLib::getUsedSaleKinds(), // 판매유형
			'ord_kinds'			=> SLib::getCodes('G_ORD_KIND'), // 출고구분
			'rel_reject_reasons'=> SLib::getCodes('REL_REJECT_REASON'), // 출고거부사유
			'dlv_cd'			=> $cfg_dlv_cd,
			'dlvs'				=> SLib::getCodes('DELIVERY'), // 택배사목록
			'dlv_companies'		=> $dlv_companies,
		];
        return view(Config::get('shop.shop.view') . '/order/ord03', $values);
	}

	public function search(Request $request)
	{
		$search_date_stat = $request->input('search_date_stat', 'receipt');
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));
		$rel_order = $request->input('rel_order', '');
		$dlv_place_type = strtoupper($this->dlv_place_type);
		$store_cd = Auth('head')->user()->store_cd;
		$ord_no = $request->input('ord_no', '');
		$ord_state = $request->input('ord_state', '20'); // 접수상태
		$dlv_type = $request->input('dlv_type', ''); // 배송방식
		$sale_place = $request->input('sale_place', ''); // 판매처
		$ord_info_key = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$sale_kind = $request->input('sale_kind', []); // 판매유형
		$stat_pay_type = $request->input('stat_pay_type', ''); // 결제방법
		$not_complex = $request->input('not_complex', 'N'); // 복합결제 제외여부
		$com_id = $request->input('com_cd', '');
		$prd_cd = $request->input('prd_cd', '');
		$style_no = $request->input('style_no', '');
		$goods_no = $request->input('goods_no', '');
		$item = $request->input('item', '');
		$brand_cd = $request->input('brand_cd', '');
		$prd_cd_range_text = $request->input('prd_cd_range', '');
		$goods_nm = $request->input('goods_nm', '');
		$goods_nm_eng = $request->input('goods_nm_eng', '');

		$ord_field = $request->input('ord_field', 'o.ord_date');
		$ord = $request->input('ord', 'desc');
		$page = $request->input('page', 1);
		$limit = $request->input('limit', 100);

		/** 검색조건 필터링 */
		$where = "";

		if ($search_date_stat === 'receipt') {
			$where .= " and rc.req_rt >= '$sdate 00:00:00' and rc.req_rt <= '$edate 23:59:59' ";
		} else if ($search_date_stat === 'order') {
			$where .= " and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59' ";
		}

		if ($rel_order != '') $where .= " and rc.rel_order like '" . $rel_order . "%' ";
		if ($dlv_place_type === 'STORE' && $store_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $store_cd . "' ";
		}
		if ($ord_no != '') $where .= " and o.ord_no like '" . $ord_no . "%' ";
		if ($ord_state != '') $where .= " and rcp.state = '" . $ord_state . "' ";
		if ($dlv_type != '') $where .= " and om.dlv_type = '" . $dlv_type . "' ";
		if ($sale_place != '') $where .= " and o.sale_place = '" . $sale_place . "' ";

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
				if (in_array($ord_info_key, ['om.user_nm', 'om.user_id', 'om.r_nm'])) {
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
		
		if (count($sale_kind) > 0) {
			$sale_kind_join = join(',', array_map(function($s) { return "'$s'"; }, $sale_kind));
			$where .= " and o.sale_kind in ($sale_kind_join) ";
		}
		if ($com_id != '') $where .= " and g.com_id = '" . $com_id . "' ";

		// 상품코드 검색
		if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '$cd%' ";
			}
			$where .= ") ";
		}

		if ($style_no != '') $where .= " and g.style_no like '" . $style_no . "%' ";
		if ($item != '') $where .= " and g.opt_kind_cd = '" . $item . "' ";
		if ($brand_cd != '') $where .= " and g.brand = '" . $brand_cd . "' ";
		if ($goods_nm != '') $where .= " and g.goods_nm like '%" . $goods_nm . "%' ";
		if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%" . $goods_nm_eng . "%' ";

		// 상품번호 검색
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if($goods_no != ""){
            $goods_nos = explode(",", $goods_no);
            if(count($goods_nos) > 1) {
                if(count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(",", $goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// order by
		if ($ord_field === 'o.ord_date') $ord_field = "date_format(o.ord_date, '%Y-%m-%d')";
		$orderby = sprintf("order by %s %s, om.r_nm asc", $ord_field, $ord);

		// pagination
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$dlv_locations = $this->_get_dlv_locations($store_cd);
		$qty_sql = "";
		foreach ($dlv_locations as $loc) {
			$qty_sql .= ", (select qty from product_stock_$loc->location_type where " . $loc->location_type . "_cd = '$loc->location_cd' and prd_cd = pc.prd_cd) as "  . $loc->seq . "_" . $loc->location_type . "_" . $loc->location_cd . "_qty ";
		}

		$sql = "
			select a.*
				, os.code_val as ord_state_nm
			    , cm.code_val as clm_state_nm
				, round((1 - (a.price * (1 - if(st.amt_kind = 'per', st.sale_per, 0) / 100)) / a.goods_sh) * 100) as dc_rate
				, sk.code_val as sale_kind_nm, pr.code_val as pr_code_nm
				, ot.code_val as ord_type_nm, ok.code_val as ord_kind_nm
				, bk.code_val as baesong_kind, com.com_nm as sale_place_nm
				, pt.code_val as pay_type_nm, ps.code_val as pay_stat_nm
				, mu.name as req_nm
			from (
				select 
					rcp.or_cd, rcp.or_prd_cd, rc.rel_order, rc.req_id, rcp.comment as receipt_comment
					, rcp.state, rcp.dlv_location_type, rcp.dlv_location_cd, rcp.rt as receipt_date
					, if(rcp.dlv_location_type = 'STORAGE', (select storage_nm from storage where storage_cd = rcp.dlv_location_cd), (select store_nm from store where store_cd = rcp.dlv_location_cd)) as dlv_location_nm
					, o.ord_no, o.ord_opt_no, o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, o.goods_opt
					, pc.prd_cd, pc.prd_cd_p as prd_cd_p, pc.color
					, pc.size
					, o.wonga, o.price, g.price as goods_price, g.goods_sh, o.qty, o.dlv_no
					, o.pay_type, o.dlv_amt, o.point_amt, o.coupon_amt, o.dc_amt, o.recv_amt
					, o.sale_place, o.store_cd, o.ord_state, o.clm_state, o.com_id, o.baesong_kind as dlv_baesong_kind, o.ord_date
					, o.sale_kind, o.pr_code, o.sales_com_fee, o.ord_type, o.ord_kind, p.pay_stat, p.pay_date
					, concat(ifnull(om.user_nm, ''), '(', ifnull(om.user_id, ''), ')') as user_nm, om.r_nm
					, o.dlv_end_date as rel_date
					, om.r_zipcode, om.r_addr1, om.r_addr2, om.r_phone, om.r_mobile, om.dlv_msg
					, c.code_val as dlv_nm
					$qty_sql
				from order_receipt_product rcp
					inner join order_receipt rc on rc.or_cd = rcp.or_cd
					inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
					inner join order_mst om on om.ord_no = o.ord_no
					inner join product_code pc on pc.prd_cd = rcp.prd_cd
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
					left outer join code c on c.code_id = o.dlv_cd and c.code_kind_cd = 'DELIVERY'
				where rcp.reject_yn = 'N'
					-- and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					-- 클레임상태 (-30 : 클레임무효, 1 : 임시저장, 90: 클레임없음, 0: 클레임없음)
					$where
				$orderby
				$limit
			) a
				left outer join code sk on sk.code_kind_cd = 'SALE_KIND' and sk.code_id = a.sale_kind
				left outer join code pr on pr.code_kind_cd = 'PR_CODE' and pr.code_id = a.pr_code
				left outer join code os on os.code_kind_cd = 'G_ORD_STATE' and os.code_id = a.ord_state
			    left outer join code cm on cm.code_kind_cd = 'G_CLM_STATE' and cm.code_id = a.clm_state
				left outer join code ot on ot.code_kind_cd = 'G_ORD_TYPE' and ot.code_id = a.ord_type
				left outer join code ok on ok.code_kind_cd = 'G_ORD_KIND' and ok.code_id = a.ord_kind
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = a.dlv_baesong_kind
				left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = a.pay_type
				left outer join code ps on ps.code_kind_cd = 'G_PAY_STAT' and ps.code_id = a.pay_stat
				left outer join sale_type st on st.sale_kind = a.sale_kind and st.use_yn = 'Y'
				left outer join company com on com.com_type = '4' and com.use_yn = 'Y' and com.com_id = a.sale_place
				inner join mgr_user mu on mu.id = a.req_id
		";
		$result = DB::select($sql);

		// pagination
		$total = 0;
		$page_cnt = 0;
		if($page == 1) {
			$sql = "
				select count(*) as total
				from order_receipt_product rcp
					inner join order_receipt rc on rc.or_cd = rcp.or_cd
					inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
					inner join order_mst om on om.ord_no = o.ord_no
					inner join product_code pc on pc.prd_cd = rcp.prd_cd
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
				where rcp.reject_yn = 'N'
					-- and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					$where
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
                "page_total" => count($result),
				"dlv_locations" => $dlv_locations
            ],
            "body" => $result,
        ]);
	}

	/** 배송처목록 조회 */
	private function _get_dlv_locations($store_cd)
	{
		$dlv_locations_sql = "
			select 'store' as location_type
			     , store_cd as location_cd
			     , store_nm as location_nm
			     , 2 as seq 
			from store 
			where store_cd = :store_cd
			order by seq, location_cd
		";
		return DB::select($dlv_locations_sql, [ 'store_cd' => $store_cd ]);
	}

	/** 출고완료처리 */
	public function complete(Request $request)
	{
		$conf   			= new Conf();
        $cfg_dlv_cd 		= $conf->getConfigValue("delivery", "dlv_cd");
		$cfg_kakao_yn		= $conf->getConfigValue("kakao", "kakao_yn");
		$cfg_shop_name		= $conf->getConfigValue("shop", "name");
		$cfg_sms			= $conf->getConfig("sms");
		$cfg_sms_yn			= $conf->getValue($cfg_sms, "sms_yn");
        $cfg_delivery_yn	= $conf->getValue($cfg_sms, "delivery_yn");
		$cfg_delivery_msg	= $conf->getValue($cfg_sms, "delivery_msg");
		$shop_phone         = $conf->getConfigValue("shop", "phone");

		$user = [
			'id'	=> Auth('head')->user()->id,
			'name'	=> Auth('head')->user()->name
		];
		$ord_state = 30; // 출고완료
		$send_sms_yn = $request->input('send_sms_yn', 'N');
//		$dlv_cd = $request->input('u_dlvs', $cfg_dlv_cd);
		$rows = $request->input('data', []);
		$failed_rows = [];

		try {
            DB::beginTransaction();

			$order = new Order($user);
			
			foreach ($rows as $row) {
				$sql = "select code_id from code where code_kind_cd = 'DELIVERY' and code_val = :code_val";
				$dlv_cd = DB::selectOne($sql, ['code_val' => $row['dlv_nm']])->code_id ?? $cfg_dlv_cd;
				
				if (!isset($row['ord_no']) || !isset($row['ord_opt_no'])) {
					array_push($failed_rows, $row['ord_no']);
					continue;
				}

				$ord_opt_no = $row['ord_opt_no'];
				$dlv_no = $row['dlv_no'] ?? '';

				$order->SetOrdOptNo($ord_opt_no, $row['ord_no']);

				// 상태점검 (이미 완료된 출고건인지 점검)
				$check_state = $order->CheckState($ord_state);
				if (!$check_state) {
					array_push($failed_rows, $row['ord_no']);
					continue;
				}

				// 재고검사
				$stock_check = false;
				if ($row['dlv_location_type'] === 'STORAGE') {
					$sql = DB::table('product_stock_storage')->where('storage_cd', $row['dlv_location_cd'])->where('prd_cd', $row['prd_cd']);
					if ($sql->count() > 0) $stock_check = $sql->value('qty') >= $row['qty'];
				}
				if ($row['dlv_location_type'] === 'STORE') {
					$sql = DB::table('product_stock_store')->where('store_cd', $row['dlv_location_cd'])->where('prd_cd', $row['prd_cd']);
					if ($sql->count() > 0) $stock_check = $sql->value('qty') >= $row['qty'];
				}

				if ($stock_check) {
					// 출고완료처리
					$state_log = [
						'ord_no' => $row['ord_no'], 
						'ord_opt_no' => $ord_opt_no, 
						'ord_state' => $ord_state, 
						'comment' => "배송출고완료(온라인배송처리)", 
						'admin_id' => $user['id'], 
						'admin_nm' => $user['name']
					];
					$order->AddStateLog($state_log);
					$order->DlvEnd($dlv_cd, $dlv_no, $ord_state, $row['dlv_location_type'], $row['dlv_location_cd']);
					$order->DlvLog($ord_state);

					$this->update_sale_store($ord_opt_no);

					// 온라인주문접수 업데이트정보 변경
					DB::table('order_receipt')
						->where('or_cd', $row['or_cd'])
						->update([
							'fin_rt' => now(),
							'fin_id' => $user['id'],
							'ut' => now(),
						]);

					// 온라인주문접수 상품리스트 상태변경
					DB::table('order_receipt_product')
						->where('or_prd_cd', $row['or_prd_cd'])
						->update([
							'state' => $ord_state,
							'ut' => now(),
						]);

					// 재고처리
					$this->update_stock($row, $user);

					// 배송문자발송
					$msg_yn = "N";
					if ($send_sms_yn == 'Y') {
						if ($cfg_sms_yn == 'Y' && $cfg_delivery_yn == 'Y') {
							$sql = "
								select b.user_nm, b.mobile, a.goods_nm
									, ( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) as msg_cnt
								from order_opt a
									inner join order_mst b on a.ord_no = b.ord_no
								where ord_opt_no = '$ord_opt_no'
									and ( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) = 0
							";
							$opt = DB::selectone($sql);
							if (!empty($opt->user_nm)) {
								$user_nm	= $opt->user_nm;
                                $mobile		= $opt->mobile;
                                $goods_nm	= mb_substr($opt->goods_nm, 0, 10);

								$dlv_nm = SLib::getCodesValue('DELIVERY', $dlv_cd);
								if($dlv_nm === '') $dlv_nm = $dlv_cd;

                                $sms = new SMS($user);
                                $sms_msg = sprintf("[%s]%s..발송완료 %s(%s)",$cfg_shop_name, $goods_nm, $dlv_nm, $dlv_no);

								if($mobile != ""){
									$sms->SendAligoSMS( $mobile, $sms_msg, $user_nm );
									$msg_yn = "Y";
								}
							}
						}
					}
				} else {
					array_push($failed_rows, $row['ord_no']);
					continue;
				}
			}

			DB::commit();
			$code = 200;
			$msg = "온라인주문이 정상적으로 출고완료되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}
		return response()->json(['code' => $code, 'msg' => $msg, 'failed_rows' => $failed_rows], $code);
	}

	/** 배송처리 시 실재고 차감처리 */
	private function update_stock($row, $user)
	{
		$prd_cd = $row['prd_cd'] ?? '';
		$goods_no = $row['goods_no'] ?? '';
		$goods_opt = $row['goods_opt'] ?? '';
		$ord_price = $row['price'] ?? '';
		$ord_opt_no = $row['ord_opt_no'] ?? '';
		$ord_qty = $row['qty'] ?? 0;
		$wonga = $row['wonga'] ?? 0;
		$location_type = $row['dlv_location_type'] ?? '';
		$location_cd = $row['dlv_location_cd'] ?? '';
		$sale_place = $row['sale_place'] ?? '';
		
		// 실재고 차감
		if ($location_type === 'STORE') {
			DB::table('product_stock_store')
				->where('prd_cd', '=', $prd_cd)
				->where('store_cd', '=', $location_cd) 
				->update([
					'qty' => DB::raw('qty - ' . $ord_qty),
					'ut' => now(),
				]);
		} else if ($location_type === 'STORAGE') {
			DB::table('product_stock_storage')
				->where('prd_cd', '=', $prd_cd)
				->where('storage_cd', '=', $location_cd)
				->update([
					'qty' => DB::raw('qty - ' . $ord_qty),
					'ut' => now(),
				]);
		}

		if ($sale_place == '') return;

		// 온라인매장 입고 및 판매처리
		$o_store_cd = '';
		$store_info = DB::table('store')->select('store_cd', 'store_nm')->where('sale_place_match_yn', 'Y')->where('com_id', $sale_place)->first();
		if ($store_info != null) $o_store_cd = $store_info->store_cd;

		$stock_cnt = DB::table('product_stock_store')->where('prd_cd', '=', $prd_cd)->where('store_cd', '=', $o_store_cd)->count();
		if ($stock_cnt < 1) {
			DB::table('product_stock_store')
				->insert([
					'goods_no' => $goods_no,
					'prd_cd' => $prd_cd,
					'store_cd' => $o_store_cd,
					'qty' => 0,
					'wqty' => 0,
					'goods_opt' => $goods_opt,
					'use_yn' => 'Y',
					'rt' => now(),
				]);
		} else {
			DB::table('product_stock_store')
				->where('prd_cd', '=', $prd_cd)
				->where('store_cd', '=', $o_store_cd)
				->update([
					'ut' => now(),
				]);
		}

		DB::table('product_stock')
			->where('prd_cd', '=', $prd_cd)
			->update([
				'ut' => now(),
			]);

		DB::table('product_stock_hst')
			->insert([
				'goods_no' => $goods_no,
				'prd_cd' => $prd_cd,
				'goods_opt' => $goods_opt,
				'location_cd' => $o_store_cd,
				'location_type' => 'STORE',
				'type' => ($location_type === 'STORE' ? PRODUCT_STOCK_TYPE_STORE_RT : PRODUCT_STOCK_TYPE_STORE_IN), // 재고분류 : 매장RT입고(15) / 매장입고(1)
				'price' => $ord_price,
				'wonga' => $wonga,
				'qty' => $ord_qty,
				'stock_state_date' => date('Ymd'),
				'r_stock_state_date' => date('Ymd'),
				'ord_opt_no' => $ord_opt_no,
				'comment' => ($location_type === 'STORE' ? '매장RT' : '매장') . '입고(온라인배송)',
				'rt' => now(),
				'admin_id' => $user['id'],
				'admin_nm' => $user['name'],
			]);

		DB::table('product_stock_hst')
			->insert([
				'goods_no' => $goods_no,
				'prd_cd' => $prd_cd,
				'goods_opt' => $goods_opt,
				'location_cd' => $o_store_cd,
				'location_type' => 'STORE',
				'type' => PRODUCT_STOCK_TYPE_STORE_SALE, // 재고분류 : 매장주문(2)
				'price' => $ord_price,
				'wonga' => $wonga,
				'qty' => $ord_qty * -1,
				'stock_state_date' => date('Ymd'),
				'r_stock_state_date' => date('Ymd'),
				'ord_opt_no' => $ord_opt_no,
				'comment' => '매장주문(온라인배송)',
				'rt' => now(),
				'admin_id' => $user['id'],
				'admin_nm' => $user['name'],
			]);

		DB::table('product_stock_hst')
			->where('ord_opt_no', '=', $ord_opt_no)
			->where('location_cd', '=', $location_cd)
			->where('type', '=', '15')			// 재고분류 : 매장RT출고(15)
			->update([
				'r_stock_state_date' => date('Ymd'),
				'ut'	=> now()
			]);
	}

	/** 주문접수 시 판매매장정보 등록 */
	private function update_sale_store($ord_opt_no)
	{
		$ord = DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->select('sale_place', 'store_cd')->first();
		if ($ord->sale_place != '') {
			$store = DB::table('store')->where('sale_place_match_yn', 'Y')->where('com_id', $ord->sale_place)->select('store_cd')->first();
			if ($store !== null) {
				DB::table('order_opt')->where('ord_opt_no', $ord_opt_no)->update([ 'store_cd' => $store->store_cd ]);
			}
		}
	}

	/** 
	 * 출고요청처리 (상태변경 및 출고구분변경 및 재고처리)
	 * (출고처리중 -> 출고요청)
	 * */
	public function update_ord_kind(Request $request)
	{
		$user = [
			'id'	=> Auth('head')->user()->id,
			'name'	=> Auth('head')->user()->name
		];
		$ord_state = 10; //	출고요청
		$ord_kind = $request->input('ord_kind', '');
		$reject_reason = $request->input('reject_reason', '');
		$ord_opt_nos = $request->input('ord_opt_nos', []);
		$ord_opt_nos = implode(', ', $ord_opt_nos);
		$or_prd_cds = $request->input('or_prd_cds', []);

		try {
            DB::beginTransaction();

			// 주문상태 및 출고구분값 변경
			$sql = "
				update order_opt set 
					ord_kind = '$ord_kind'
					, ord_state = '$ord_state'
				where ord_opt_no in ($ord_opt_nos)
			";
			DB::update($sql);
			$sql = "
				update order_opt_wonga
					set ord_kind = '$ord_kind'
				where ord_opt_no in ($ord_opt_nos)
			";
			DB::update($sql);

			// 차감된 재고 리셋처리
			foreach ($or_prd_cds as $cd) {
				$result = $this->reset_stock($user, $cd, $ord_state);
				if ($result != 1) throw new Exception("재고처리 중 에러가 발생했습니다.");
			}

			// 온라인주문접수에서 제거
			$or_prd_cds_join = implode(', ', $or_prd_cds);

			$sql = "
				update order_receipt_product set
					reject_yn = 'Y',
					reject_reason = :reject_reason,
					ut = :now
				where or_prd_cd in ($or_prd_cds_join)
			";
			DB::update($sql, [ 'reject_reason' => $reject_reason, 'now' => now() ]);

			DB::commit();
			$code = 200;
			$msg = "출고거부처리가 정상적으로 완료되었습니다.";
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json(['code' => $code, 'msg' => $msg], $code);
	}

	/** 출고요청처리 시, 보유재고차감 리셋 */
	private function reset_stock($user, $or_prd_cd, $ord_state)
	{
		$sql = "
			select orp.or_prd_cd, orp.ord_opt_no, orp.prd_cd, orp.qty
				, orp.dlv_location_type, orp.dlv_location_cd
				, pc.goods_no, pc.goods_opt, p.price, p.wonga
			from order_receipt_product orp
				inner join product_code pc on pc.prd_cd = orp.prd_cd
				inner join product p on p.prd_cd = orp.prd_cd
			where or_prd_cd = '$or_prd_cd'
		";
		$row = DB::selectOne($sql);
		if ($row == null) return 0;

		$ord_opt_no = $row->ord_opt_no;
		$prd_cd = $row->prd_cd;
		$goods_no = $row->goods_no;
		$goods_opt = $row->goods_opt;
		$price = $row->price;
		$wonga = $row->wonga;
		$ord_qty = $row->qty;
		$location_type = $row->dlv_location_type;
		$location_cd = $row->dlv_location_cd;

		try {
			// 차감된 보유재고 리셋
			if ($location_type === 'STORE') {
				DB::table('product_stock_store')
					->where('prd_cd', '=', $prd_cd)
					->where('store_cd', '=', $location_cd) 
					->update([
						'wqty' => DB::raw('wqty + ' . $ord_qty),
						'ut' => now(),
					]);
				DB::table('product_stock')
					->where('prd_cd', '=', $prd_cd)
					->update([
						'qty_wonga'	=> DB::raw('qty_wonga + ' . ($ord_qty * $wonga)),
						'out_qty' => DB::raw('out_qty - ' . $ord_qty),
						'qty' => DB::raw('qty + ' . $ord_qty),
						'ut' => now(),
					]);
			} else if ($location_type === 'STORAGE') {
				DB::table('product_stock_storage')
					->where('prd_cd', '=', $prd_cd)
					->where('storage_cd', '=', $location_cd)
					->update([
						'wqty' => DB::raw('wqty + ' . $ord_qty),
						'ut' => now(),
					]);
				DB::table('product_stock')
					->where('prd_cd', '=', $prd_cd)
					->update([
						'qty_wonga'	=> DB::raw('qty_wonga + ' . ($ord_qty * $wonga)),
						'out_qty' => DB::raw('out_qty - ' . $ord_qty),
						'qty' => DB::raw('qty + ' . $ord_qty),
						'wqty' => DB::raw('wqty + ' . $ord_qty),
						'ut' => now(),
					]);
			}

			// 재고이력 등록
			DB::table('product_stock_hst')
				->insert([
					'goods_no' => $goods_no,
					'prd_cd' => $prd_cd,
					'goods_opt' => $goods_opt,
					'location_cd' => $location_cd,
					'location_type' => $location_type,
					'type' => ($location_type === 'STORE' ? PRODUCT_STOCK_TYPE_STORE_RT : PRODUCT_STOCK_TYPE_STORAGE_OUT), // 재고분류 : 매장RT출고취소(15) / 창고출고취소(17)
					'price' => $price,
					'wonga' => $wonga,
					'qty' => $ord_qty,
					'stock_state_date' => date('Ymd'),
					'ord_opt_no' => $ord_opt_no,
					'comment' => ($location_type === 'STORE' ? '매장RT' : '창고') . '출고취소(온라인배송)',
					'rt' => now(),
					'admin_id' => $user['id'],
					'admin_nm' => $user['name'],
				]);

			return 1;
		} catch (Exception $e) {
			return 0;
		}
	}

	/** 팝업오픈 모음 */
	public function show_popup(Request $request, $cmd)
	{
		switch ($cmd) {
			case 'invoice-list':
				$response = $this->show_invoice($request);
				break;
			case 'batch':
				$response = $this->show_batch($request);
				break;
            default:
                $message = 'Command not found';
                $response = response()->json(['code' => 0, 'msg' => $message], 404);
		};
		return $response;
	}

	/** 택배송장목록받기 팝업 show */
	public function show_invoice(Request $request)
	{
		$col_type = 'dlv_inv_dn_store';
		$cnt = DB::table('columns')->where('type', $col_type)->count();
		if ($cnt < 1) {
			$sql = "
				insert into columns 
					( type, cn, name, seq, use_yn, use_seq, rt, ut )
				select '$col_type' as type, cn, name, seq, use_yn, use_seq, now() as rt, now() as ut
				from columns where type = 'dlv_inv_dn' 
				order by use_seq
			";
			DB::insert($sql);
		}

		$sql = "select cn as name, name as value from columns where type = '$col_type' order by seq";
		$columns = DB::select($sql);

		$sql = "select cn as name, name as value from columns where type = '$col_type' and use_yn = 'Y' order by use_seq";
		$fields = DB::select($sql);

		$values = [
			'columns' => $columns,
			'fields' => $fields,
		];

		return view( Config::get('shop.shop.view') . '/order/ord03_invoice', $values );
	}

	/** 택배송장일괄입력 팝업 show */
	public function show_batch(Request $request)
	{
		$store_cd = Auth('head')->user()->store_cd;

		$conf   = new Conf();
        $cfg_dlv_cd = $conf->getConfigValue("delivery","dlv_cd");

		$dlv_locations = $this->_get_dlv_locations($store_cd);

		$values = [
			'dlv_cd' => $cfg_dlv_cd,
			'dlvs' => SLib::getCodes('DELIVERY'), // 택배사목록
			'dlv_locations'	=> $dlv_locations, // 배송처
		];
		return view( Config::get('shop.shop.view') . '/order/ord03_batch', $values );
	}

	/** 엑셀다운로드 모음 */
	public function download(Request $request, $cmd)
	{
		switch ($cmd) {
			case 'dlv-list':
				$response = $this->_get_dlv_list($request);
				break;
			case 'invoice-list':
				$response = $this->_get_dlv_invoice_list($request);
				break;
            default:
                $message = 'Command not found';
                $response = response()->json(['code' => 0, 'msg' => $message], 404);
		};
		return $response;
	}

	/** 배송목록받기 */
	private function _get_dlv_list(Request $request)
	{
		$search_date_stat = $request->input('search_date_stat', 'receipt');
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));
		$rel_order = $request->input('rel_order', '');
		$dlv_place_type = strtoupper($request->input('dlv_place_type', 'storage'));
		$storage_cd = $request->input('storage_cd', '');
		$store_cd = Auth('head')->user()->store_cd;
		$ord_no = $request->input('ord_no', '');
		$ord_state = $request->input('ord_state', '20'); // 접수상태
		$dlv_type = $request->input('dlv_type', ''); // 배송방식
		$sale_place = $request->input('sale_place', ''); // 판매처
		$ord_info_key = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$sale_kind = $request->input('sale_kind', []); // 판매유형
		$stat_pay_type = $request->input('stat_pay_type', ''); // 결제방법
		$not_complex = $request->input('not_complex', 'N'); // 복합결제 제외여부
		$com_id = $request->input('com_cd', '');
		$prd_cd = $request->input('prd_cd', '');
		$style_no = $request->input('style_no', '');
		$goods_no = $request->input('goods_no', '');
		$item = $request->input('item', '');
		$brand_cd = $request->input('brand_cd', '');
		$prd_cd_range_text = $request->input('prd_cd_range', '');
		$goods_nm = $request->input('goods_nm', '');
		$goods_nm_eng = $request->input('goods_nm_eng', '');

		$ord_field = $request->input('ord_field', 'o.ord_date');
		$ord = $request->input('ord', 'desc');
		$page = $request->input('page', 1);
		$limit = $request->input('limit', 100);

		/** 검색조건 필터링 */
		$where = "";

		if ($search_date_stat === 'receipt') {
			$where .= " and rc.req_rt >= '$sdate 00:00:00' and rc.req_rt <= '$edate 23:59:59' ";
		} else if ($search_date_stat === 'order') {
			$where .= " and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59' ";
		}

		if ($rel_order != '') $where .= " and rc.rel_order like '" . $rel_order . "%' ";
		if ($dlv_place_type === 'STORAGE' && $storage_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $storage_cd . "' ";
		} else if ($dlv_place_type === 'STORE' && $store_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $store_cd . "' ";
		}
		if ($ord_no != '') $where .= " and o.ord_no like '" . $ord_no . "%' ";
		if ($ord_state != '') $where .= " and rcp.state = '" . $ord_state . "' ";
		if ($dlv_type != '') $where .= " and om.dlv_type = '" . $dlv_type . "' ";
		if ($sale_place != '') $where .= " and o.sale_place = '" . $sale_place . "' ";

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
				if (in_array($ord_info_key, ['om.user_nm', 'om.user_id', 'om.r_nm'])) {
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
		
		if (count($sale_kind) > 0) {
			$sale_kind_join = join(',', array_map(function($s) { return "'$s'"; }, $sale_kind));
			$where .= " and o.sale_kind in ($sale_kind_join) ";
		}
		if ($com_id != '') $where .= " and g.com_id = '" . $com_id . "' ";

		// 상품코드 검색
		if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '$cd%' ";
			}
			$where .= ") ";
		}

		if ($style_no != '') $where .= " and g.style_no like '" . $style_no . "%' ";
		if ($item != '') $where .= " and g.opt_kind_cd = '" . $item . "' ";
		if ($brand_cd != '') $where .= " and g.brand = '" . $brand_cd . "' ";
		if ($goods_nm != '') $where .= " and g.goods_nm like '%" . $goods_nm . "%' ";
		if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%" . $goods_nm_eng . "%' ";

		// 상품번호 검색
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if($goods_no != ""){
            $goods_nos = explode(",", $goods_no);
            if(count($goods_nos) > 1) {
                if(count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(",", $goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// order by
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$dlv_locations = $this->_get_dlv_locations($store_cd);
		$qty_sql = "";
		foreach ($dlv_locations as $loc) {
			$qty_sql .= ", ifnull((select qty from product_stock_$loc->location_type where " . $loc->location_type . "_cd = '$loc->location_cd' and prd_cd = pc.prd_cd), 0) as "  . $loc->seq . "_" . $loc->location_type . "_" . $loc->location_cd . "_qty ";
		}

		$sql = "
			select 
				o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, o.goods_opt
				, pc.prd_cd, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color
				, (
                    select s.size_cd from size s
                    where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
                        and s.size_cd = pc.size
                        and use_yn = 'Y'
                ) as size
				, sum(o.qty) as order_qty, count(o.ord_opt_no) as order_cnt
				, g.com_nm, b.brand_nm as brand
				$qty_sql
			from order_receipt_product rcp
				inner join order_receipt rc on rc.or_cd = rcp.or_cd
				inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
				inner join order_mst om on om.ord_no = o.ord_no
				inner join product_code pc on pc.prd_cd = rcp.prd_cd
				inner join goods g on g.goods_no = o.goods_no
				left outer join payment p on p.ord_no = o.ord_no
				left outer join brand b on b.brand = g.brand
			where rcp.reject_yn = 'N' 
				and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
				and o.clm_state in (-30,1,90,0)
				$where
			group by rcp.prd_cd
			$orderby
		";

		$headers = [
			'prd_cd' => '바코드',
			'goods_no' => '온라인코드',
			'style_no' => '스타일넘버', 
			'brand' => '브랜드', 
			'com_nm' => '공급업체', 
			'goods_nm' => '상품명', 
			'goods_nm_eng' => '상품명(영문)',
			'prd_cd_p' => '품번',
			'color' => '컬러', 
			'size' => '사이즈', 
			'goods_opt' => '옵션명', 
			'order_cnt' => '주문수', 
			'order_qty' => '주문수량',
		];

		foreach ($dlv_locations as $loc) {
			$headers[$loc->seq . "_" . $loc->location_type . "_" . $loc->location_cd . "_qty"] = $loc->location_nm . "재고";
		}

		$sizes = [
			'prd_cd' => 18,
			'com_nm' => 15,
			'goods_nm' => 50, 
			'goods_nm_eng' => 50, 
			'prd_cd_p' => 15,  
			'goods_opt' => 30,
		];

		return Excel::download(new ExcelExport($sql, $headers, $sizes), date('YmdH').'_배송목록.xlsx', \Maatwebsite\Excel\Excel::XLSX);
	}

	/** 택배송장목록받기 */
	private function _get_dlv_invoice_list(Request $request)
	{
		$search_date_stat = $request->input('search_date_stat', 'receipt');
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));
		$rel_order = $request->input('rel_order', '');
		$dlv_place_type = strtoupper($request->input('dlv_place_type', 'storage'));
		$storage_cd = $request->input('storage_cd', '');
		$store_cd = $request->input('store_no', '');
		$ord_no = $request->input('ord_no', '');
		$ord_state = $request->input('ord_state', '20'); // 접수상태
		$dlv_type = $request->input('dlv_type', ''); // 배송방식
		$sale_place = $request->input('sale_place', ''); // 판매처
		$ord_info_key = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$sale_kind = $request->input('sale_kind', []); // 판매유형
		$stat_pay_type = $request->input('stat_pay_type', ''); // 결제방법
		$not_complex = $request->input('not_complex', 'N'); // 복합결제 제외여부
		$com_id = $request->input('com_cd', '');
		$prd_cd = $request->input('prd_cd', '');
		$style_no = $request->input('style_no', '');
		$goods_no = $request->input('goods_no', '');
		$item = $request->input('item', '');
		$brand_cd = $request->input('brand_cd', '');
		$prd_cd_range_text = $request->input('prd_cd_range', '');
		$goods_nm = $request->input('goods_nm', '');
		$goods_nm_eng = $request->input('goods_nm_eng', '');

		$ord_field = $request->input('ord_field', 'o.ord_date');
		$ord = $request->input('ord', 'desc');
		$page = $request->input('page', 1);
		$limit = $request->input('limit', 100);

		$fields = $request->input('fields', '');

		/** 검색조건 필터링 */
		$where = "";

		if ($search_date_stat === 'receipt') {
			$where .= " and rc.req_rt >= '$sdate 00:00:00' and rc.req_rt <= '$edate 23:59:59' ";
		} else if ($search_date_stat === 'order') {
			$where .= " and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59' ";
		}

		if ($rel_order != '') $where .= " and rc.rel_order like '" . $rel_order . "%' ";
		if ($dlv_place_type === 'STORAGE' && $storage_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $storage_cd . "' ";
		} else if ($dlv_place_type === 'STORE' && $store_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $store_cd . "' ";
		}
		if ($ord_no != '') $where .= " and o.ord_no like '" . $ord_no . "%' ";
		if ($ord_state != '') $where .= " and rcp.state = '" . $ord_state . "' ";
		if ($dlv_type != '') $where .= " and om.dlv_type = '" . $dlv_type . "' ";
		if ($sale_place != '') $where .= " and o.sale_place = '" . $sale_place . "' ";

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
				if (in_array($ord_info_key, ['om.user_nm', 'om.user_id', 'om.r_nm'])) {
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
		
		if (count($sale_kind) > 0) {
			$sale_kind_join = join(',', array_map(function($s) { return "'$s'"; }, $sale_kind));
			$where .= " and o.sale_kind in ($sale_kind_join) ";
		}
		if ($com_id != '') $where .= " and g.com_id = '" . $com_id . "' ";

		// 상품코드 검색
		if ($prd_cd != '') {
			$prd_cd = explode(',', $prd_cd);
			$where .= " and (1<>1 ";
			foreach ($prd_cd as $cd) {
				$where .= " or pc.prd_cd like '$cd%' ";
			}
			$where .= ") ";
		}

		if ($style_no != '') $where .= " and g.style_no like '" . $style_no . "%' ";
		if ($item != '') $where .= " and g.opt_kind_cd = '" . $item . "' ";
		if ($brand_cd != '') $where .= " and g.brand = '" . $brand_cd . "' ";
		if ($goods_nm != '') $where .= " and g.goods_nm like '%" . $goods_nm . "%' ";
		if ($goods_nm_eng != '') $where .= " and g.goods_nm_eng like '%" . $goods_nm_eng . "%' ";

		// 상품번호 검색
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if($goods_no != ""){
            $goods_nos = explode(",", $goods_no);
            if(count($goods_nos) > 1) {
                if(count($goods_nos) > 500) array_splice($goods_nos, 500);
                $in_goods_nos = join(",", $goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

		// 상품옵션 범위검색
		parse_str($prd_cd_range_text, $prd_cd_range);
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				// $in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// order by
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		$sql = "
			select a.*
				, ot.code_val as ord_type
				, ok.code_val as ord_kind
				, os.code_val as ord_state_nm
				, ps.code_val as pay_stat
				, cs.code_val as clm_state
				, pt.code_val as pay_type
				, bk.code_val as baesong_kind
				, com.com_nm as sale_place
			from (
				select
					rc.rel_order as dlv_series_nm
					, o.ord_type
					, o.ord_kind
					, o.ord_no
					, o.ord_opt_no
					, o.ord_state
					, p.pay_stat
					, o.clm_state
					, pc.prd_cd
					, g.goods_no
					, g.style_no
					, b.brand_nm as brand
					, g.com_nm
					, g.goods_nm
					, g.goods_nm_eng
					, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
					, pc.color
					, (
						select s.size_cd from size s
						where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
							and s.size_cd = pc.size
							and use_yn = 'Y'
					) as size
					, o.goods_opt
					, o.qty as sale_qty
					, 0 as dlv_qty -- 배송처 재고수량
					, o.price
					, (o.coupon_amt + o.dc_amt) as sale_amt
					, o.dlv_amt
					, o.recv_amt
					, o.pay_type
					, concat(ifnull(om.user_nm, ''), '(', ifnull(om.user_id, ''), ')') as user_nm
					, om.r_nm
					, om.r_zipcode
					, concat(om.r_addr1, ' ', ifnull(om.r_addr2, '')) as r_addr
					, om.r_phone
					, om.r_mobile
					, om.dlv_msg
					, o.sale_place
					, o.baesong_kind
					, om.phone
					, om.mobile
					, rcp.dlv_location_type
					, rcp.dlv_location_cd
					, if(rcp.dlv_location_type = 'STORAGE', (select storage_nm from storage where storage_cd = rcp.dlv_location_cd), (select store_nm from store where store_cd = rcp.dlv_location_cd)) as dlv_location_nm
					, if(rcp.dlv_location_type = 'STORAGE', (select qty from product_stock_storage where storage_cd = rcp.dlv_location_cd and prd_cd = pc.prd_cd), (select qty from product_stock_store where store_cd = rcp.dlv_location_cd and prd_cd = pc.prd_cd)) as qty
					, o.dlv_no
					, o.sale_kind
				from order_receipt_product rcp
					inner join order_receipt rc on rc.or_cd = rcp.or_cd
					inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
					inner join order_mst om on om.ord_no = o.ord_no
					inner join product_code pc on pc.prd_cd = rcp.prd_cd
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
					left outer join brand b on b.brand = g.brand
				where rcp.reject_yn = 'N'
					and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					$where
				$orderby
			) a
				left outer join code ot on ot.code_kind_cd = 'G_ORD_TYPE' and ot.code_id = a.ord_type
				left outer join code ok on ok.code_kind_cd = 'G_ORD_KIND' and ok.code_id = a.ord_kind
				left outer join code os on os.code_kind_cd = 'G_ORD_STATE' and os.code_id = a.ord_state
				left outer join code ps on ps.code_kind_cd = 'G_PAY_STAT' and ps.code_id = a.pay_stat
				left outer join code cs on cs.code_kind_cd = 'G_CLM_STAT' and cs.code_id = a.clm_state
				left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = a.pay_type
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = a.baesong_kind
				left outer join sale_type st on st.sale_kind = a.sale_kind and st.use_yn = 'Y'
				left outer join company com on com.com_type = '4' and com.use_yn = 'Y' and com.com_id = a.sale_place
		";

		try {
			DB::beginTransaction();

			// columns 테이블 업데이트 (필드 순서 및 사용여부)
			$fields = explode(',', $fields);
			$field_type = 'dlv_inv_dn_store';

			$field_sql = "
				update columns set
					use_yn = 'N'
					, ut = now()
				where type = '$field_type'
			";
			DB::update($field_sql);

			foreach ($fields as $i => $cn) {
				$field_sql = "
					update columns set
						use_yn = 'Y'
						, use_seq = '$i'
						, ut = now()
					where type = '$field_type' and cn = '$cn'
				";
				DB::update($field_sql);
			}

			$columns_sql = "
				select type, cn, name
				from columns
				where type = 'dlv_inv_dn_store' and use_yn = 'Y'
				order by use_seq
			";
			$columns = DB::select($columns_sql);
			$headers = array_column($columns, 'name', 'cn');

			$sizes = [
				'dlv_series_nm' => 20,
				'ord_no' => 20,
				'ord_state_nm' => 15,
				'prd_cd' => 18,
				'com_nm' => 18,
				'goods_nm' => 50, 
				'goods_nm_eng' => 50, 
				'prd_cd_p' => 15,  
				'goods_opt' => 30,
				'user_nm' => 15,
				'r_addr' => 40,
				'r_phone' => 15,
				'r_mobile' => 15,
			];
			
			DB::commit();
			return Excel::download(new ExcelExport($sql, $headers, $sizes), date('YmdH').'_택배송장목록.xlsx', \Maatwebsite\Excel\Excel::XLSX);
		} catch (Exception $e) {
			DB::rollBack();
			$code = 500;
			$msg = $e->getMessage();
			return response()->json(['code' => $code, 'msg' => $msg], $code);
		}
	}

	/** 택배송장일괄입력 엑셀파일 적용 */
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
	
				$save_path = "data/shop/ord03/";
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

	/** 주문일련번호로 온라인주문접수목록 조회 */
	public function batch_search_orders(Request $request)
	{
		$store_cd = Auth('head')->user()->store_cd;

        $data = $request->input('data', []);
		$opt_nos = join(',', array_map(function($row) { return $row['ord_opt_no'] ?? ''; }, $data));
		$cols = array_column($data, 'dlv_no', 'ord_opt_no');

		$dlv_locations = $this->_get_dlv_locations($store_cd);
		$qty_sql = "";
		foreach ($dlv_locations as $loc) {
			$qty_sql .= ", (select qty from product_stock_$loc->location_type where " . $loc->location_type . "_cd = '$loc->location_cd' and prd_cd = pc.prd_cd) as "  . $loc->seq . "_" . $loc->location_type . "_" . $loc->location_cd . "_qty ";
		}

		$sql = "
			select a.*
				, os.code_val as ord_state_nm
				, round((1 - (a.price * (1 - if(st.amt_kind = 'per', st.sale_per, 0) / 100)) / a.goods_sh) * 100) as dc_rate
				, sk.code_val as sale_kind_nm, pr.code_val as pr_code_nm
				, ot.code_val as ord_type_nm, ok.code_val as ord_kind_nm
				, bk.code_val as baesong_kind, com.com_nm as sale_place_nm
				, pt.code_val as pay_type_nm, ps.code_val as pay_stat_nm
				, mu.name as req_nm
				, 0 as result
			from (
				select 
					rcp.or_cd, rcp.or_prd_cd, rc.rel_order, rc.req_id, rcp.comment as receipt_comment
					, rcp.state, rcp.dlv_location_type, rcp.dlv_location_cd, rcp.rt as receipt_date
					, if(rcp.dlv_location_type = 'STORAGE', (select storage_nm from storage where storage_cd = rcp.dlv_location_cd), (select store_nm from store where store_cd = rcp.dlv_location_cd)) as dlv_location_nm
					, o.ord_no, o.ord_opt_no, o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, o.goods_opt
					, pc.prd_cd, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color
					, (
						select s.size_cd from size s
						where s.size_kind_cd = if(pc.size_kind != '', pc.size_kind, if(pc.gender = 'M', 'PRD_CD_SIZE_MEN', if(pc.gender = 'W', 'PRD_CD_SIZE_WOMEN', 'PRD_CD_SIZE_UNISEX')))
							and s.size_cd = pc.size
							and use_yn = 'Y'
					) as size
					, o.wonga, o.price, g.price as goods_price, g.goods_sh, o.qty, o.dlv_no
					, o.pay_type, o.dlv_amt, o.point_amt, o.coupon_amt, o.dc_amt, o.recv_amt
					, o.sale_place, o.store_cd, o.ord_state, o.clm_state, o.com_id, o.baesong_kind as dlv_baesong_kind, o.ord_date
					, o.sale_kind, o.pr_code, o.sales_com_fee, o.ord_type, o.ord_kind, p.pay_stat, p.pay_date
					, concat(ifnull(om.user_nm, ''), '(', ifnull(om.user_id, ''), ')') as user_nm, om.r_nm
					$qty_sql
				from order_receipt_product rcp
					inner join order_receipt rc on rc.or_cd = rcp.or_cd
					inner join order_opt o on o.ord_opt_no = rcp.ord_opt_no
					inner join order_mst om on om.ord_no = o.ord_no
					inner join product_code pc on pc.prd_cd = rcp.prd_cd
					inner join goods g on g.goods_no = o.goods_no
					left outer join payment p on p.ord_no = o.ord_no
				where rcp.reject_yn = 'N'
					and (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					and o.ord_opt_no in ($opt_nos)
				order by rc.or_cd desc
			) a
				left outer join code sk on sk.code_kind_cd = 'SALE_KIND' and sk.code_id = a.sale_kind
				left outer join code pr on pr.code_kind_cd = 'PR_CODE' and pr.code_id = a.pr_code
				left outer join code os on os.code_kind_cd = 'G_ORD_STATE' and os.code_id = a.ord_state
				left outer join code ot on ot.code_kind_cd = 'G_ORD_TYPE' and ot.code_id = a.ord_type
				left outer join code ok on ok.code_kind_cd = 'G_ORD_KIND' and ok.code_id = a.ord_kind
				left outer join code bk on bk.code_kind_cd = 'G_BAESONG_KIND' and bk.code_id = a.dlv_baesong_kind
				left outer join code pt on pt.code_kind_cd = 'G_PAY_TYPE' and pt.code_id = a.pay_type
				left outer join code ps on ps.code_kind_cd = 'G_PAY_STAT' and ps.code_id = a.pay_stat
				left outer join sale_type st on st.sale_kind = a.sale_kind and st.use_yn = 'Y'
				left outer join company com on com.com_type = '4' and com.use_yn = 'Y' and com.com_id = a.sale_place
				inner join mgr_user mu on mu.id = a.req_id
		";
		$result = DB::select($sql);

		foreach ($result as $re) {
			$re->dlv_no = $cols[$re->ord_opt_no] ?? '';
		}

		return response()->json([
            "code" => 200,
			"head" => [],
            "body" => $result,
        ]);
	}
}
