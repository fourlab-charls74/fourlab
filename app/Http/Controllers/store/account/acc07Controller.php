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
		$sdate = Lib::quote(str_replace('-', '', $sdate));

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
				, c.coupon_amt, c.allot_amt, (c.coupon_amt - c.allot_amt) as coupon_com_amt, c.dlv_amt, c.etc_amt
				, c.sale_net_taxation_amt, c.sale_net_taxfree_amt, c.sale_net_amt, c.tax_amt
				, (c.sale_net_amt - c.tax_amt) as sales_amt_except_vat
				, c.fee_JS1, c.fee_JS2, c.fee_JS3, c.fee_TG, c.fee_YP, c.fee_OL
				, ae.*
				, (ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as extra_amt
				, (c.fee + ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as fee_net
				, c.fee as fee_amt
				, c.closed_yn, date_format(c.closed_date, '%Y-%m-%d') as closed_date, date_format(c.pay_day, '%Y-%m-%d') as pay_day, c.tax_no, c.admin_nm, c.rt
				, s.store_nm, s.manager_nm
			from store_account_closed c
				inner join store s on s.store_cd = c.store_cd
				left outer join (
					select e.store_cd as ae_store_cd
						, sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_amt
						, (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
							+ sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
							+ sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
						) * -1 as extra_S_amt
						, sum(if(et.entry_cd = 'O' and el.type <> 'O1' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_C_amt
					from store_account_extra e
						inner join store_account_extra_list el on el.ext_idx = e.idx
						inner join store_account_extra_type et on et.type_cd = el.type
					where e.ymonth = :ymonth
					group by e.store_cd
				) ae on ae.ae_store_cd = c.store_cd
			where c.sday = :sday and c.eday = :eday
				$where
			order by c.idx desc
		";

        $rows = DB::select($sql, ['ymonth' => $sdate, 'sday' => $f_sdate, 'eday' => $f_edate]);

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
				, c.fee_JS1, c.fee_JS2, c.fee_JS3, c.fee_TG, c.fee_YP, c.fee_OL, c.fee as fee_amt
				, ae.*
				, (ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as extra_amt
				, (c.fee + ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as account_amt
			from store_account_closed c
				inner join store s on s.store_cd = c.store_cd
				left outer join (
					select e.store_cd as ae_store_cd, e.ymonth
						, sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_amt
						, (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
							+ sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
							+ sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
						) * -1 as extra_S_amt
						, sum(if(et.entry_cd = 'O' and el.type <> 'O1' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_C_amt
					from store_account_extra e
						inner join store_account_extra_list el on el.ext_idx = e.idx
						inner join store_account_extra_type et on et.type_cd = el.type
					group by e.store_cd, e.ymonth
				) ae on ae.ae_store_cd = c.store_cd and ae.ymonth = left(c.sday, 6)
			where c.idx = :idx
		";
        $row = DB::selectOne($sql, ['idx' => $idx]);
        return view( Config::get('shop.store.view') . '/account/acc07_show', [ "closed" => $row ]);
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
					, (c.coupon_amt - c.allot_amt) as coupon_com_amt
					, c.dlv_amt, c.dlv_amt as old_dlv_amt, c.etc_amt, c.etc_amt as old_etc_amt
					, c.sale_net_taxation_amt, c.sale_net_taxfree_amt, c.sale_type
					, if(c.sale_type = 'JS', '정상', if(c.sale_type = 'TG', '특가', if(c.sale_type = 'YP', '용품', ''))) as sale_type_nm
					, c.sale_net_amt, c.sale_net_amt as old_sale_net_amt
					, if(c.sale_type = 'JS', c.sale_net_amt, 0) as sale_JS
					, if(c.sale_type = 'TG', c.sale_net_amt, 0) as sale_TG
					, if(c.sale_type = 'YP', c.sale_net_amt, 0) as sale_YP
					, if(c.sale_type = 'JS', c.sale_net_amt, 0) as old_sale_JS
					, if(c.sale_type = 'TG', c.sale_net_amt, 0) as old_sale_TG
					, if(c.sale_type = 'YP', c.sale_net_amt, 0) as old_sale_YP
					, (c.sale_net_amt / 1.1) as sale_amt_except_vat
					, m.user_nm, p.pay_type, w.ord_state, w.ord_state as clm_state
					, if(c.type = 30, date_format(o.ord_date, '%Y-%m-%d'), '') as ord_date
					, if(c.type in (60,61), (
						select date_format(max(end_date),'%Y-%m-%d') as clm_end_date 
						from claim
						where ord_opt_no = c.ord_opt_no
					), '') as clm_end_date
					, c.memo, date_format(ac.sday, '%Y%m') as ymonth
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
					, (c.coupon_amt - c.allot_amt) as coupon_com_amt
					, c.dlv_amt, c.dlv_amt as old_dlv_amt, c.etc_amt, c.etc_amt as old_etc_amt
					, c.sale_net_taxation_amt, c.sale_net_taxfree_amt, c.sale_type
					, c.sale_net_amt, c.sale_net_amt as old_sale_net_amt
					, (c.sale_net_amt / 1.1) as sale_amt_except_vat
					, c.fee_ratio as fee_rate_OL, c.fee as fee_OL
					, m.user_nm, p.pay_type, w.ord_state, o.clm_state
					, if(c.type = 30, date_format(o.ord_date, '%Y-%m-%d'), '') as ord_date
					, if(c.type in (60,61), (
						select date_format(max(end_date),'%Y-%m-%d') as clm_end_date 
						from claim
						where ord_opt_no = c.ord_opt_no
					), '') as clm_end_date
					, c.memo, date_format(ac.sday, '%Y%m') as ymonth
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

			$acc_list = DB::table('store_account_closed_list')->where('acc_idx', $idx);

			// 기타정산액 데이터 제거
			$acc_list_idxs = array_map(function($acc) { return $acc->idx; }, $acc_list->select('idx')->get()->toArray());
			DB::table('store_account_etc')->whereIn('acc_list_idx', $acc_list_idxs)->delete();

			// 마감상세 데이터 제거
			$acc_list->delete();
			
			// 마감 데이터 제거
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
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		$code = "200";
		$msg = "";

		$closed = [];

		try {
			DB::beginTransaction();

			$acc_idx = $data[0]['acc_idx'];
			$nowdate = now()->format("Ymd");

			// Set store_account_closed_list
			foreach ($data as $val) {
				$dlv = ($val['dlv_amt'] ?? 0) * 1;
				$etc = ($val['etc_amt'] ?? 0) * 1;

				DB::table('store_account_closed_list')
					->where('idx', $val['account_idx'])
					->update([
						'sale_net_amt' => DB::raw("(sale_net_amt - dlv_amt - etc_amt + $dlv + $etc)"),
						'tax_amt' => DB::raw("(sale_net_amt / 11)"),
						'fee' => DB::raw("(sale_net_amt / 1.1 * fee_ratio / 100)"),
						'dlv_amt' => $dlv,
						'etc_amt' => $etc,
						'memo' => $val['memo'] ?? '',
					]);

				// Set store_account_etc
				$where = ['acc_list_idx' => $val['account_idx']];
				$values = [
					'ymonth' => $val['ymonth'] ?? '',
					'etc_day' => DB::raw("date_format(now(),'%Y%m%d')"),
					'store_cd' => $val['store_cd'] ?? '',
					'ord_opt_no' => $val['ord_opt_no'] ?? '',
					'etc_amt' => $etc,
					'etc_memo' => $val['memo'] ?? '',
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
					'rt' => now(),
					'ut' => now(),
				];
				DB::table('store_account_etc')->updateOrInsert($where, $values);
			}

			// Set store_account_closed
			$sql = "
				update store_account_closed as c
					, (
						select b.*
							, (ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as extra_amt
							, (b.fee_JS1 + b.fee_JS2 + b.fee_JS3 + b.fee_TG + b.fee_YP + b.fee_OL) as fee
							, (b.fee_JS1 + b.fee_JS2 + b.fee_JS3 + b.fee_TG + b.fee_YP + b.fee_OL + (ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt)) as fee_net
						from (
							select ac.idx as acc_idx, ac.store_cd, ac.sday
								, sum(c.dlv_amt) as dlv_amt
								, sum(c.etc_amt) as etc_amt
								, sum(if(c.sale_type = 'OL', 0, c.sale_net_amt)) as sale_net_amt
								, round(sum(if(c.sale_type = 'OL', 0, c.sale_net_amt)) / 11) as tax_amt
								, round(
									if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > sg.amt1
										, sg.amt1
										, sum(if(c.sale_type = 'JS', c.sale_net_amt, 0))
									) * sg.fee1 / 100 / 1.1
								) as fee_JS1
								, round(
									if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > sg.amt2
										, sg.amt2 - sg.amt1
										, if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > sg.amt1
											, sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) - sg.amt1
											, 0
										)
									) * sg.fee2 / 100 / 1.1
								) as fee_JS2
								, round(
									if(sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) > sg.amt2
										, sum(if(c.sale_type = 'JS', c.sale_net_amt, 0)) - sg.amt2
										, 0
									) * sg.fee3 / 100 / 1.1
								) as fee_JS3
								, round(sum(if(c.sale_type = 'TG', c.sale_net_amt, 0)) * sg.fee_10 / 100 / 1.1) as fee_TG
								, round(sum(if(c.sale_type = 'YP', c.sale_net_amt, 0)) * sg.fee_11 / 100 / 1.1) as fee_YP
								, round(sum(if(c.sale_type = 'OL', c.sale_net_amt, 0)) * sg.fee_12 / 100 / 1.1) as fee_OL
							from store_account_closed_list c
								inner join store_account_closed ac on ac.idx = c.acc_idx
								inner join store s on s.store_cd = ac.store_cd
								inner join (
									select grade_cd, round(amt1 * 1.1) as amt1, fee1, round(amt2 * 1.1) as amt2, fee2, fee3, fee_10, fee_11, fee_12, fee_10_info, fee_10_info_over_yn 
									from store_grade
									where concat(replace(sdate, '-', ''), '01') <= :nowdate1
										and concat(replace(edate, '-', ''), '31') >= :nowdate2
								) sg on sg.grade_cd = s.grade_cd
							where c.acc_idx = :acc_idx
						) b
							left outer join (
								select e.store_cd as ae_store_cd, e.ymonth
									, sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_amt
									, (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
										+ sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
										+ sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
									) * -1 as extra_S_amt
									, sum(if(et.entry_cd = 'O' and el.type <> 'O1' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_C_amt
								from store_account_extra e
									inner join store_account_extra_list el on el.ext_idx = e.idx
									inner join store_account_extra_type et on et.type_cd = el.type
								group by e.store_cd, e.ymonth
							) ae on ae.ae_store_cd = b.store_cd and ae.ymonth = left(b.sday, 6)
					) as a
					set c.dlv_amt = a.dlv_amt
					, c.etc_amt = a.etc_amt
					, c.sale_net_amt = a.sale_net_amt
					, c.tax_amt = a.tax_amt
					, c.fee_JS1 = a.fee_JS1
					, c.fee_JS2 = a.fee_JS2
					, c.fee_JS3 = a.fee_JS3
					, c.fee_TG = a.fee_TG
					, c.fee_YP = a.fee_YP
					, c.fee_OL = a.fee_OL
					, c.fee = a.fee
					, c.fee_net = a.fee_net
					, c.ut = now()
				where c.idx = a.acc_idx
			";
			DB::update($sql, ['acc_idx' => $acc_idx, 'nowdate1' => $nowdate, 'nowdate2' => $nowdate]);

			// 업데이트된 마감상세 수수료 정보 조회
			$sql = "
				select c.idx, c.store_cd, s.store_nm, s.manager_nm, c.sday, c.eday, c.closed_yn, c.closed_date, c.rt, c.admin_nm
					, c.fee_JS1, c.fee_JS2, c.fee_JS3, c.fee_TG, c.fee_YP, c.fee_OL, c.fee as fee_amt
					, round(ae.extra_P_amt) as extra_P_amt
					, round(ae.extra_S_amt) as extra_S_amt
					, round(ae.extra_C_amt) as extra_C_amt
					, round(ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as extra_amt
					, round(c.fee + ae.extra_P_amt + ae.extra_C_amt + ae.extra_S_amt) as account_amt
				from store_account_closed c
					inner join store s on s.store_cd = c.store_cd
					left outer join (
						select e.store_cd as ae_store_cd, e.ymonth
							, sum(if(et.entry_cd = 'P' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_P_amt
							, (sum(if(et.entry_cd = 'S' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
								+ sum(if(el.type = 'G' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
								+ sum(if(el.type = 'E' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0))
							) * -1 as extra_S_amt
							, sum(if(et.entry_cd = 'O' and el.type <> 'O1' and et.total_include_yn = 'Y', if(et.except_vat_yn = 'Y', el.extra_amt / 1.1, el.extra_amt), 0)) as extra_C_amt
						from store_account_extra e
							inner join store_account_extra_list el on el.ext_idx = e.idx
							inner join store_account_extra_type et on et.type_cd = el.type
						group by e.store_cd, e.ymonth
					) ae on ae.ae_store_cd = c.store_cd and ae.ymonth = left(c.sday, 6)
				where c.idx = :idx
			";
			$closed = DB::selectOne($sql, ['idx' => $acc_idx]);

			DB::commit();
			$msg = "마감정보가 정상적으로 수정되었습니다.";
		} catch(Exception $e) {
			DB::rollback();
			$code = "500";
			$msg = $e->getmessage();
		}

		return response()->json(["code" => $code, "msg" => $msg, "closed" => $closed]);
	}
}
