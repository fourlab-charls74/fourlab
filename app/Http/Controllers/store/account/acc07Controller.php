<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class acc07Controller extends Controller
{
    public function index()
    {
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m");

        $values = [ 
			'sdate' => $sdate, 
			'store_types' => SLib::getStoreTypes(),
			'store_kinds' => SLib::getCodes("STORE_KIND")
		];

        return view( Config::get('shop.store.view') . '/account/acc07', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', now()->format("Y-m"));

        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Ymd");

        $store_type = $request->input('store_type', '');
        $store_kind = $request->input('store_kind', '');
        $store_cd = $request->input('store_cd', '');
		$closed_yn = $request->input('closed_yn', '');

		// 검색조건 필터링
		$where = "";
		if ($store_type != '') $where .= " and s.store_type = '" . Lib::quote($store_type) . "'";
        if ($store_kind != '') $where .= " and s.store_kind = '". Lib::quote($store_kind) . "'";
        if ($store_cd != '') $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";
        if ($closed_yn != '') $where .= " and c.closed_yn = '" . Lib::quote($closed_yn) . "'";

		$sql = "
			select c.idx, c.store_cd, c.sday, c.eday, c.sale_amt, c.clm_amt, c.dc_amt
				, c.coupon_amt, c.allot_amt, (c.coupon_amt - c.allot_amt) as coupon_com_amt, c.dlv_amt
				, c.sale_net_taxation_amt, c.sale_net_taxfree_amt, c.sale_net_amt, c.tax_amt
				, (c.sale_net_amt - c.tax_amt) as sales_amt_except_vat
				, c.fee_JS1, c.fee_JS2, c.fee_JS3, c.fee_TG, c.fee_YP, c.fee_OL, c.fee, c.extra_amt
				, (c.fee_JS1 + c.fee_JS2 + c.fee_JS3 + c.fee_TG + c.fee_YP + c.fee_OL) as fee_amt
				, c.closed_yn, date_format(c.closed_date, '%Y-%m-%d') as closed_date, date_format(c.pay_day, '%Y-%m-%d') as pay_day, c.tax_no, c.admin_nm, c.rt
				, s.store_nm, s.manager_nm
			from store_account_closed c
				inner join store s on s.store_cd = c.store_cd
			where c.sday = '$f_sdate' and c.eday = '$f_edate'
				$where
			order by c.idx desc
		";

        $rows = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    public function show(Request $request, $idx)
    {
		$sql = "
			select c.idx, c.store_cd, s.store_nm, s.manager_nm, c.sday, c.eday, c.closed_yn, c.closed_date, c.rt, c.admin_nm
				, c.fee_JS1, c.fee_JS2, c.fee_JS3, c.fee_TG, c.fee_YP, c.fee_OL, c.extra_amt, c.fee as account_amt, (c.fee - c.extra_amt) as fee_amt
			from store_account_closed c
				inner join store s on s.store_cd = c.store_cd
			where c.idx = :idx
		";
        $row = DB::selectOne($sql, ['idx' => $idx]);

        $values = [
			"closed" => $row,
        ];

        return view( Config::get('shop.store.view') . '/account/acc07_show', $values);
    }

    public function search_command(Request $request, $cmd)
    {
        switch ($cmd) {
			case 'except-online':
				$response = $this->search_except_online($request);
				break;
			case 'online':
				$response = $this->search_online($request);
				break;
            default:
                $message = 'Command not found';
                $response = response()->json(['code' => 0, 'msg' => $message], 404);
		};
		return $response;
    }

	// 마감상세내역 조회 (특약(온라인) 제외)
	public function search_except_online(Request $request)
	{
		$idx = $request->input('idx', '');
		if ($idx == '') return response()->json(['code' => 400, 'msg' => '부정확한 요청입니다.'], 404);

		$sql = "
			select a.*
				, act.code_val as a_ord_state_nm
				, pyt.code_val as pay_type_nm
				, ods.code_val as ord_state_nm
				, cls.code_val as clm_state_nm
			from (
				select c.acc_idx, c.idx as account_idx, c.state_date
					, o.ord_no, c.ord_opt_no, ac.store_cd, s.store_nm
					, o.prd_cd, o.goods_no, o.goods_nm, o.goods_opt
					, c.qty, c.sale_amt, c.clm_amt, c.dc_amt, c.coupon_amt, c.allot_amt
					, (c.coupon_amt - c.allot_amt) as coupon_com_amt, c.dlv_amt
					, c.sale_net_taxation_amt, c.sale_net_taxfree_amt, c.sale_type
					, if(c.sale_type = 'JS', '정상', if(c.sale_type = 'TG', '특가', if(c.sale_type = 'YP', '용품', ''))) as sale_type_nm
					, if(c.sale_type = 'JS', c.sale_net_amt, 0)as sale_JS
					, if(c.sale_type = 'TG', c.sale_net_amt, 0)as sale_TG
					, if(c.sale_type = 'YP', c.sale_net_amt, 0)as sale_YP
					, (c.sale_net_amt - c.tax_amt) as sale_amt_except_vat
					, m.user_nm, p.pay_type, w.ord_state, w.ord_state as clm_state
					, if(c.type = 30, date_format(o.ord_date, '%Y-%m-%d'), '') as ord_date
					, if(c.type in (60,61), (
						select date_format(max(end_date),'%Y-%m-%d') as clm_end_date 
						from claim
						where ord_opt_no = c.ord_opt_no
					), '') as clm_end_date
					, c.memo
				from store_account_closed_list c
					inner join store_account_closed ac on ac.idx = c.acc_idx
					inner join order_opt_wonga w on w.ord_opt_no = c.ord_opt_no and w.ord_state = c.type
					inner join order_opt o on o.ord_opt_no = c.ord_opt_no
					inner join order_mst m on m.ord_no = o.ord_no
					inner join payment p on p.ord_no = o.ord_no
					inner join store s on s.store_cd = ac.store_cd
				where c.sale_type <> 'OL' and c.acc_idx = :acc_idx
				order by c.idx
			) a
				left outer join code act on act.code_kind_cd = 'G_ACC_TYPE' and act.code_id = a.ord_state
				left outer join code pyt on pyt.code_kind_cd = 'G_PAY_TYPE' and pyt.code_id = a.pay_type
				left outer join code ods on ods.code_kind_cd = 'G_ORD_STATE' and ods.code_id = a.ord_state
				left outer join code cls on cls.code_kind_cd = 'G_CLM_STATE' and cls.code_id = a.clm_state
		";
		$rows = DB::select($sql, ['acc_idx' => $idx]);

		return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($rows)
			],
            "body" => $rows
        ]);
	}

	// 특가(온라인) 마감상세내역 조회
	public function search_online(Request $request)
	{
		$idx = $request->input('idx', '');
		if ($idx == '') return response()->json(['code' => 400, 'msg' => '부정확한 요청입니다.'], 404);

		$sql = "
			select a.*
				, act.code_val as a_ord_state_nm
				, pyt.code_val as pay_type_nm
				, ods.code_val as ord_state_nm
				, cls.code_val as clm_state_nm
				, com.com_nm as sale_place_nm
			from (
				select c.acc_idx, c.idx as account_idx, c.state_date
					, o.ord_no, c.ord_opt_no, ac.store_cd, s.store_nm, o.sale_place
					, o.prd_cd, o.goods_no, o.goods_nm, o.goods_opt
					, c.qty, c.sale_amt, c.clm_amt, c.dc_amt, c.coupon_amt, c.allot_amt
					, (c.coupon_amt - c.allot_amt) as coupon_com_amt, c.dlv_amt
					, c.sale_net_taxation_amt, c.sale_net_taxfree_amt, c.sale_type
					, (w.qty * w.price / 1.1) as sale_amt_except_vat
					, c.fee_ratio as fee_rate_OL, c.fee as fee_OL
					, m.user_nm, p.pay_type, w.ord_state, o.clm_state
					, if(c.type = 30, date_format(o.ord_date, '%Y-%m-%d'), '') as ord_date
					, if(c.type in (60,61), (
						select date_format(max(end_date),'%Y-%m-%d') as clm_end_date 
						from claim
						where ord_opt_no = c.ord_opt_no
					), '') as clm_end_date
					, c.memo
				from store_account_closed_list c
					inner join store_account_closed ac on ac.idx = c.acc_idx
					inner join order_opt_wonga w on w.ord_opt_no = c.ord_opt_no and w.ord_state = c.type
					inner join order_opt o on o.ord_opt_no = c.ord_opt_no
					inner join order_mst m on m.ord_no = o.ord_no
					inner join payment p on p.ord_no = o.ord_no
					inner join store s on s.store_cd = ac.store_cd
				where c.sale_type = 'OL' and c.acc_idx = :acc_idx
				order by c.idx
			) a
				left outer join code act on act.code_kind_cd = 'G_ACC_TYPE' and act.code_id = a.ord_state
				left outer join code pyt on pyt.code_kind_cd = 'G_PAY_TYPE' and pyt.code_id = a.pay_type
				left outer join code ods on ods.code_kind_cd = 'G_ORD_STATE' and ods.code_id = a.ord_state
				left outer join code cls on cls.code_kind_cd = 'G_CLM_STATE' and cls.code_id = a.clm_state
				left outer join company com on com.com_id = a.sale_place
		";
		$rows = DB::select($sql, ['acc_idx' => $idx]);

		return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($rows)
			],
            "body" => $rows
        ]);
	}

	// 마감삭제
	public function remove(Request $request, $idx)
	{
		$code = "200";
		$msg = "";

		try {
			DB::beginTransaction();

			DB::table('store_account_closed_list')->where('acc_idx', $idx)->delete();
			
			DB::table('store_account_closed')->where('idx', $idx)->delete();

			DB::commit();
			$msg = "마감삭제가 정상적으로 완료되었습니다.";
		} catch(Exception $e) {
			DB::rollback();
			$code = "500";
			$msg = $e->getmessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	// 마감완료
	public function complete_closed(Request $request)
	{
		$acc_idx = $request->input('idx', '');
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		$code = "200";
		$msg = "";

		try {
			DB::beginTransaction();
			
			DB::table('store_account_closed')->where('idx', $acc_idx)
				->update([
					'closed_yn' => 'Y',
					'closed_date' => now(),
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
					'ut' => now(),
				]);

			DB::commit();
			$msg = "마감완료처리가 정상적으로 완료되었습니다.";
		} catch(Exception $e) {
			DB::rollback();
			$code = "500";
			$msg = $e->getmessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
	}

	// 마감정보 수정
	public function update_closed(Request $request)
	{
		$data = $request->input('data', []);
		$code = "200";
		$msg = "";

		try {
			DB::beginTransaction();
			
			foreach ($data as $val) {
				DB::table('store_account_closed_list')
					->where('idx', $val['account_idx'])
					->update([
						'memo' => $val['memo'] ?? '',
					]);
			}

			DB::commit();
			$msg = "마감정보가 정상적으로 수정되었습니다.";
		} catch(Exception $e) {
			DB::rollback();
			$code = "500";
			$msg = $e->getmessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);


		// $acc_idx = $request->input("idx");
		// $data = $request->input("data");

		// $admin_id = Auth('head')->user()->id;
		// $admin_nm = Auth('head')->user()->name;

		// $lines = explode("<>", $data);

		// try {
		// 	DB::beginTransaction();
		// 	// 정산 마감 상세 내역 업데이트 ( 배송비/기타정산액/비고 )
		// 	for ( $i = 0; $i < count($lines); $i++ )
		// 	{
		// 		$fields = explode("::", $lines[$i]);

		// 		$tax_yn			= str_replace(",", "", $fields[0]);
		// 		$dlv_amt 		= str_replace(",", "", $fields[1]);
		// 		$etc_amt 		= str_replace(",", "", $fields[2]);

		// 		$sale_tax_amt 	= str_replace(",", "", $fields[3]);
		// 		$sale_ntax_amt 	= str_replace(",", "", $fields[4]);
		// 		$sale_amt		= str_replace(",", "", $fields[5]);
		// 		$tax_amt		= str_replace(",", "", $fields[6]);
		// 		$fee_ratio 	= str_replace(",", "", $fields[7]);
		// 		$fee 				= str_replace(",", "", $fields[8]);
		// 		$fee_net 		= str_replace(",", "", $fields[9]);

		// 		$acc_amt 		= str_replace(",", "", $fields[10]);
		// 		$memo	 		= str_replace(",", "", $fields[11]);
		// 		$idx	 		= str_replace(",", "", $fields[12]);

		// 		$sql = "
		// 			update store_account_closed_list set
		// 				dlv_amt					= :dlv_amt,
		// 				etc_amt					= :etc_amt,
		// 				sale_net_taxation_amt	= :sale_net_taxation_amt,
		// 				sale_net_taxfree_amt	= :sale_net_taxfree_amt,
		// 				sale_net_amt			= :sale_net_amt,
		// 				tax_amt					= :tax_amt,
		// 				fee_ratio				= :fee_ratio,
		// 				fee						= :fee,
		// 				fee_net	 				= :fee_net,
		// 				memo					= :memo,
		// 				acc_amt					= :acc_amt
		// 			where
		// 				idx = :idx
		// 		";

		// 		DB::update($sql,
		// 			[
		// 				'dlv_amt' => $dlv_amt,
		// 				'etc_amt' => $etc_amt,
		// 				'sale_net_taxation_amt' => $sale_tax_amt,
		// 				'sale_net_taxfree_amt' => $sale_ntax_amt,
		// 				'sale_net_amt' => $sale_amt,
		// 				'tax_amt' => $tax_amt,
		// 				'fee_ratio' => $fee_ratio,
		// 				'fee' => $fee,
		// 				'fee_net' => $fee_net,
		// 				'memo' => $memo,
		// 				'acc_amt' => $acc_amt,
		// 				'idx' => $idx
		// 			]
		// 		);

		// 	}

		// 	// 정산 마감 마스터 업데이트 ( 배송비/기타정산액/비고 )
		// 	$sql = "
		// 		select
		// 			sum(dlv_amt) as total_dlv_amt,
		// 			sum(etc_amt) as total_etc_amt,
		// 			sum(sale_net_taxation_amt) as total_sale_net_taxation_amt,
		// 			sum(sale_net_taxfree_amt) as total_sale_net_taxfree_amt,
		// 			sum(sale_net_amt) as total_sale_net_amt,
		// 			sum(tax_amt) as total_tax_amt,
		// 			sum(fee) as total_fee,
		// 			sum(fee_net) as total_fee_net,
		// 			sum(acc_amt) as total_acc_amt
		// 		from
		// 			store_account_closed_list
		// 		where
		// 			acc_idx= '$acc_idx'
		// 		group by
		// 			acc_idx
		// 	";
		// 	$row = DB::selectOne($sql);

		// 	$total_dlv_amt = $row->total_dlv_amt;
		// 	$total_etc_amt = $row->total_etc_amt;
		// 	$total_sale_net_taxation_amt = $row->total_sale_net_taxation_amt;
		// 	$total_sale_net_taxfree_amt = $row->total_sale_net_taxfree_amt;
		// 	$total_sale_net_amt = $row->total_sale_net_amt;
		// 	$total_tax_amt = $row->total_tax_amt;
		// 	$total_fee	 = $row->total_fee;
		// 	$total_fee_net = $row->total_fee_net;
		// 	$total_acc_amt = $row->total_acc_amt;

		// 	$sql = "
		// 		update store_account_closed set
		// 			dlv_amt = :total_dlv_amt,
		// 			etc_amt = :total_etc_amt,
		// 			sale_net_taxation_amt = :total_sale_net_taxation_amt,
		// 			sale_net_taxfree_amt = :total_sale_net_taxfree_amt,
		// 			sale_net_amt = :total_sale_net_amt,
		// 			tax_amt = :total_tax_amt,
		// 			fee = :total_fee,
		// 			fee_net = :total_fee_net,
		// 			acc_amt = :total_acc_amt,
		// 			admin_id = :admin_id,
		// 			admin_nm = :admin_nm,
		// 			upd_date = now()
		// 		where
		// 			idx = :idx
		// 	";

		// 	DB::update($sql, 
		// 		[
		// 			"total_dlv_amt" => $total_dlv_amt,
		// 			"total_etc_amt" => $total_etc_amt,
		// 			"total_sale_net_taxation_amt" => $total_sale_net_taxation_amt,
		// 			"total_sale_net_taxfree_amt" => $total_sale_net_taxfree_amt,
		// 			"total_sale_net_amt" => $total_sale_net_amt,
		// 			"total_tax_amt" => $total_tax_amt,
		// 			"total_fee"	=> $total_fee,
		// 			"total_fee_net" => $total_fee_net,
		// 			"total_acc_amt" => $total_acc_amt,
		// 			'admin_id' => $admin_id,
		// 			'admin_nm' => $admin_nm,
		// 			'idx' => $acc_idx
		// 		]
		// 		);
		// 	DB::commit();
		// 	return response()->json(["result" => 1]);
		// } catch (Exception $e) {
		// 	DB::rollBack();
		// 	return response()->json(["result" => 0]);
		// }
	}
}
