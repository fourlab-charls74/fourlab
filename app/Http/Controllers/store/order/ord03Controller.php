<?php

namespace App\Http\Controllers\store\order;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use App\Models\Conf;
use App\Models\Order;
use App\Models\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

const HEAD = 'HEAD';
const STORAGE = 'STORAGE';

/** 온라인 배송처리 */
class ord03Controller extends Controller
{
	public function index(Request $request) {

		$user_group = 'HEAD'; // 추후 로직적용 필요

		$sdate = Carbon::now()->sub(15, 'day')->format("Y-m-d");
		$receipt_sdate = Carbon::now()->sub(4, 'day')->format("Y-m-d");
		$edate = Carbon::now()->format("Y-m-d");

		$sale_places_sql = "select com_id as id, com_nm as val from company where com_type = '4' and use_yn = 'Y' order by com_nm";
		$sale_places = DB::select($sale_places_sql);

		$dlv_storages_sql = "select storage_cd, storage_nm, if(online_yn = 'Y', 0, 1) as seq from storage where online_yn = 'Y' or default_yn = 'Y' order by seq";
		$dlv_storages = DB::select($dlv_storages_sql);

		$dlv_locations = $this->_get_dlv_locations($user_group);

		$conf   = new Conf();
        $cfg_dlv_cd = $conf->getConfigValue("delivery","dlv_cd");

		$values = [
            'sdate'         	=> $sdate,
            'receipt_sdate'     => $receipt_sdate,
			'edate'         	=> $edate,
			'dlv_storages'		=> $dlv_storages, // 창고목록
			'dlv_locations'		=> $dlv_locations, // 배송처
			'dlv_types'			=> SLib::getCodes('G_DLV_TYPE'), // 배송방식
			'sale_places'		=> $sale_places, // 판매처
            'stat_pay_types'    => SLib::getCodes('G_STAT_PAY_TYPE'), // 결제방법
			'items'			    => SLib::getItems(), // 품목
            'sale_kinds'        => SLib::getUsedSaleKinds(), // 판매유형
			'ord_kinds'			=> SLib::getCodes('G_ORD_KIND'), // 출고구분
			'dlv_cd'			=> $cfg_dlv_cd,
			'dlvs'				=> SLib::getCodes('DELIVERY'), // 택배사목록
			'user_group'		=> $user_group,
			'user_groups'		=> [
				'HEAD' 		=> HEAD,
				'STORAGE' 	=>  STORAGE,
			]
		];
        return view( Config::get('shop.store.view') . '/order/ord03', $values );
	}

	public function search(Request $request)
	{
		$user_group = 'HEAD'; // 추후 로직적용 필요

		$receipt_sdate = $request->input('receipt_sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$receipt_edate = $request->input('receipt_edate', Carbon::now()->format("Y-m-d"));
		$rel_order = $request->input('rel_order', '');
		$dlv_place_type = strtoupper($request->input('dlv_place_type', 'storage'));
		$storage_cd = $request->input('storage_cd', '');
		$store_cd = $request->input('store_no', '');
		$sdate = $request->input('sdate', Carbon::now()->sub(3, 'day')->format("Y-m-d"));
		$edate = $request->input('edate', Carbon::now()->format("Y-m-d"));
		$ord_no = $request->input('ord_no', '');
		$ord_state = $request->input('ord_state', '20'); // 접수상태
		$dlv_type = $request->input('dlv_type', ''); // 배송방식
		$sale_place = $request->input('sale_place', ''); // 판매처
		$ord_info_key = $request->input('ord_info_key', 'om.user_nm');
		$ord_info_value = $request->input('ord_info_value', '');
		$sale_kind = $request->input('sale_kind', ''); // 판매유형
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

		if ($user_group === 'STORAGE') $where .= " and rcp.dlv_location_type = 'STORAGE' ";

		$where .= " and rc.req_rt >= '$receipt_sdate 00:00:00' and rc.req_rt <= '$receipt_edate 23:59:59' ";
		if ($rel_order != '') $where .= " and rc.rel_order like '" . $rel_order . "%' ";
		if ($dlv_place_type === 'STORAGE' && $storage_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $storage_cd . "' ";
		} else if ($dlv_place_type === 'STORE' && $store_cd != '') {
			$where .= " and rcp.dlv_location_type = '" . $dlv_place_type . "' and rcp.dlv_location_cd = '" . $store_cd . "' ";
		}
		$where .= " and o.ord_date >= '$sdate 00:00:00' and o.ord_date <= '$edate 23:59:59' ";
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
		
		if ($sale_kind != '') $where .= " and o.sale_kind = '" . $sale_kind . "' ";
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
				$in_query = $prd_cd_range[$opt . '_contain'] == 'true' ? 'in' : 'not in';
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$where .= " and pc.$opt $in_query ($opt_join) ";
			}
		}

		// order by
		$orderby = sprintf("order by %s %s", $ord_field, $ord);

		// pagination
		$page_size = $limit;
		$startno = ($page - 1) * $page_size;
		$limit = " limit $startno, $page_size ";

		$dlv_locations = $this->_get_dlv_locations($user_group);
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
			from (
				select 
					rcp.or_cd, rcp.or_prd_cd, rc.rel_order, rc.req_id, rcp.comment as receipt_comment
					, rcp.state, rcp.dlv_location_type, rcp.dlv_location_cd, rcp.rt as receipt_date
					, if(rcp.dlv_location_type = 'STORAGE', (select storage_nm from storage where storage_cd = rcp.dlv_location_cd), (select store_nm from store where store_cd = rcp.dlv_location_cd)) as dlv_location_nm
					, o.ord_no, o.ord_opt_no, o.goods_no, g.goods_nm, g.goods_nm_eng, g.style_no, o.goods_opt
					, pc.prd_cd, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p, pc.color, pc.size
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
				where (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
					and o.clm_state in (-30,1,90,0)
					$where
				$orderby
				$limit
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
				where (o.store_cd is null or o.store_cd = 'HEAD_OFFICE') 
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
	private function _get_dlv_locations($user_group = '')
	{
		$dlv_locations = [];

		if ($user_group === HEAD) {
			$dlv_locations_sql = "
				(select 'storage' as location_type, storage_cd as location_cd, storage_nm as location_nm, if(online_yn = 'Y', 0, 1) as seq from storage where online_yn = 'Y' or default_yn = 'Y')
				union all
				(select 'store' as location_type, store_cd as location_cd, store_nm as location_nm, 2 as seq from store where store_cd in (select code_id from code where code_kind_cd = 'ONLINE_ORDER_STORE'))
				order by seq, location_cd
			";
			$dlv_locations = DB::select($dlv_locations_sql);
		} else if ($user_group === STORAGE) {
			$dlv_locations_sql = "
				select 
					'storage' as location_type, 
					storage_cd as location_cd, 
					storage_nm as location_nm, 
					if(online_yn = 'Y', 0, 1) as seq 
				from storage 
				where online_yn = 'Y' or default_yn = 'Y'
				order by if(online_yn = 'Y', 0, 1), storage_cd
			";
			$dlv_locations = DB::select($dlv_locations_sql);
		}

		return $dlv_locations;
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
		$dlv_cd = $request->input('u_dlvs', $cfg_dlv_cd);
		$rows = $request->input('data', []);
		$failed_rows = [];

		try {
            DB::beginTransaction();

			$order = new Order($user);
			
			foreach ($rows as $row) {
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
					$order->DlvEnd($dlv_cd, $dlv_no, $ord_state);
					$order->DlvLog($ord_state);

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
					$this->update_stock($row);

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

	// 배송처리 시 실재고 차감처리
	private function update_stock($row)
	{
		$prd_cd = $row['prd_cd'] ?? '';
		$ord_qty = $row['qty'] ?? 0;
		$location_type = $row['dlv_location_type'] ?? '';
		$location_cd = $row['dlv_location_cd'] ?? '';
		
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
	}

	/** 출고요청처리 (상태변경 및 출고구분변경 및 재고처리) */
	public function update_ord_kind()
	{
		// 출고요청으로 상태 돌려놓기
		// 출고구분값 변경
		// 보유재고 원래대로 돌려놓기
	}
}
