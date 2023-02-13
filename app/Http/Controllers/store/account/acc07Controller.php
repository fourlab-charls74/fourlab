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
				, c.closed_yn, c.closed_date, date_format(c.pay_day, '%Y-%m-%d') as pay_day, c.tax_no, c.admin_nm, c.rt
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

	public function search_except_online(Request $request)
	{
		$idx = $request->input('idx', '');
		if ($idx == '') return response()->json(['code' => 400, 'msg' => '부정확한 요청입니다.'], 404);

		// 아래 쿼리문 작업중입니다. - 최유현
		$sql = "
			select *, c.idx as account_idx
			from store_account_closed_list c
				inner join store_account_closed ac on ac.idx = c.acc_idx
				inner join store s on s.store_cd = ac.store_cd
				inner join order_opt o on o.ord_opt_no = c.ord_opt_no
				inner join goods g on g.goods_no = o.goods_no
			where c.acc_idx = :acc_idx
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

    public function show_search(Request $request)
    {
        $idx = $request->input('idx');

        $sql = "
			select
				acc_type.code_val as type, w.state_date, o.ord_no, o.ord_opt_no,
				if((select count(*) from order_opt where ord_no = o.ord_no) > 1, 'Y','') as multi_order,
				if(o.coupon_no <>0, (select coupon_nm from coupon where coupon_no = o.coupon_no),'') as coupon_nm,
				o.goods_nm, o.prd_cd, replace(o.goods_opt,'^',':') as opt_nm, g.style_no,
				opt_type.code_val as opt_type, m.user_nm, pay_type.code_val as pay_type,
				'Y' as tax_yn,
				w.qty as qty,
				w.sale_amt, w.clm_amt, w.dc_amt, ( w.coupon_amt - w.allot_amt ) as coupon_com_amt,
				w.dlv_amt, w.etc_amt as fee_etc_amt,
				w.sale_net_taxation_amt, w.sale_net_taxfree_amt, w.sale_net_amt, w.tax_amt,
				w.fee_ratio , w.fee, w.fee_dc_amt, w.fee_net,
				w.acc_amt, w.allot_amt as fee_allot_amt,

				cd.code_val as ord_state ,cd2.code_val as clm_state,
				date_format(o.ord_date,'%y%m%d') as ord_date, date_format(o.dlv_end_date,'%y%m%d') as dlv_end_date,
				if(o.clm_state in (60,61),
				(
					select
						date_format(max(end_date),'%y%m%d') clm_end_date
					from
						claim
					where
						ord_opt_no = o.ord_opt_no),''
				) as clm_end_date,
				w.memo, g.goods_no, g.goods_sub, w.idx, w.acc_idx
			from
				store_account_closed closed
				inner join store_account_closed_list w on w.acc_idx = closed.idx
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst m on o.ord_no = m.ord_no
				inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				inner join payment p on m.ord_no = p.ord_no
				left outer join code cd on cd.code_kind_cd = 'g_ord_state' and cd.code_id = o.ord_state
				left outer join code cd2 on cd2.code_kind_cd = 'g_clm_state' and cd2.code_id = o.clm_state
				left outer join code opt_type on opt_type.code_kind_cd = 'g_ord_type' and o.ord_type = opt_type.code_id
				left outer join code acc_type on acc_type.code_kind_cd = 'g_acc_type' and w.type = acc_type.code_id
				left outer join code pay_type on pay_type.code_kind_cd = 'g_pay_type' and p.pay_type = pay_type.code_id
			where
				closed.idx = '${idx}'
			order by
				w.state_date
		";

        $rows = DB::select($sql);

		/**
         * 조회한 데이터에서 필요한 정보들을 추가 또는 가공
         */
        $rows = collect($rows)->map(function($row, $index) {
            $row->index = $index;
			$row->edited = false;
            return $row;
        })->all();

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);

    }

	public function show_update(Request $request)
	{
		$acc_idx = $request->input("idx");
		$data = $request->input("data");

		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		$lines = explode("<>", $data);

		try {
			DB::beginTransaction();
			// 정산 마감 상세 내역 업데이트 ( 배송비/기타정산액/비고 )
			for ( $i = 0; $i < count($lines); $i++ )
			{
				$fields = explode("::", $lines[$i]);

				$tax_yn			= str_replace(",", "", $fields[0]);
				$dlv_amt 		= str_replace(",", "", $fields[1]);
				$etc_amt 		= str_replace(",", "", $fields[2]);

				$sale_tax_amt 	= str_replace(",", "", $fields[3]);
				$sale_ntax_amt 	= str_replace(",", "", $fields[4]);
				$sale_amt		= str_replace(",", "", $fields[5]);
				$tax_amt		= str_replace(",", "", $fields[6]);
				$fee_ratio 	= str_replace(",", "", $fields[7]);
				$fee 				= str_replace(",", "", $fields[8]);
				$fee_net 		= str_replace(",", "", $fields[9]);

				$acc_amt 		= str_replace(",", "", $fields[10]);
				$memo	 		= str_replace(",", "", $fields[11]);
				$idx	 		= str_replace(",", "", $fields[12]);

				$sql = "
					update store_account_closed_list set
						dlv_amt					= :dlv_amt,
						etc_amt					= :etc_amt,
						sale_net_taxation_amt	= :sale_net_taxation_amt,
						sale_net_taxfree_amt	= :sale_net_taxfree_amt,
						sale_net_amt			= :sale_net_amt,
						tax_amt					= :tax_amt,
						fee_ratio				= :fee_ratio,
						fee						= :fee,
						fee_net	 				= :fee_net,
						memo					= :memo,
						acc_amt					= :acc_amt
					where
						idx = :idx
				";

				DB::update($sql,
					[
						'dlv_amt' => $dlv_amt,
						'etc_amt' => $etc_amt,
						'sale_net_taxation_amt' => $sale_tax_amt,
						'sale_net_taxfree_amt' => $sale_ntax_amt,
						'sale_net_amt' => $sale_amt,
						'tax_amt' => $tax_amt,
						'fee_ratio' => $fee_ratio,
						'fee' => $fee,
						'fee_net' => $fee_net,
						'memo' => $memo,
						'acc_amt' => $acc_amt,
						'idx' => $idx
					]
				);

			}

			// 정산 마감 마스터 업데이트 ( 배송비/기타정산액/비고 )
			$sql = "
				select
					sum(dlv_amt) as total_dlv_amt,
					sum(etc_amt) as total_etc_amt,
					sum(sale_net_taxation_amt) as total_sale_net_taxation_amt,
					sum(sale_net_taxfree_amt) as total_sale_net_taxfree_amt,
					sum(sale_net_amt) as total_sale_net_amt,
					sum(tax_amt) as total_tax_amt,
					sum(fee) as total_fee,
					sum(fee_net) as total_fee_net,
					sum(acc_amt) as total_acc_amt
				from
					store_account_closed_list
				where
					acc_idx= '$acc_idx'
				group by
					acc_idx
			";
			$row = DB::selectOne($sql);

			$total_dlv_amt = $row->total_dlv_amt;
			$total_etc_amt = $row->total_etc_amt;
			$total_sale_net_taxation_amt = $row->total_sale_net_taxation_amt;
			$total_sale_net_taxfree_amt = $row->total_sale_net_taxfree_amt;
			$total_sale_net_amt = $row->total_sale_net_amt;
			$total_tax_amt = $row->total_tax_amt;
			$total_fee	 = $row->total_fee;
			$total_fee_net = $row->total_fee_net;
			$total_acc_amt = $row->total_acc_amt;

			$sql = "
				update store_account_closed set
					dlv_amt = :total_dlv_amt,
					etc_amt = :total_etc_amt,
					sale_net_taxation_amt = :total_sale_net_taxation_amt,
					sale_net_taxfree_amt = :total_sale_net_taxfree_amt,
					sale_net_amt = :total_sale_net_amt,
					tax_amt = :total_tax_amt,
					fee = :total_fee,
					fee_net = :total_fee_net,
					acc_amt = :total_acc_amt,
					admin_id = :admin_id,
					admin_nm = :admin_nm,
					upd_date = now()
				where
					idx = :idx
			";

			DB::update($sql, 
				[
					"total_dlv_amt" => $total_dlv_amt,
					"total_etc_amt" => $total_etc_amt,
					"total_sale_net_taxation_amt" => $total_sale_net_taxation_amt,
					"total_sale_net_taxfree_amt" => $total_sale_net_taxfree_amt,
					"total_sale_net_amt" => $total_sale_net_amt,
					"total_tax_amt" => $total_tax_amt,
					"total_fee"	=> $total_fee,
					"total_fee_net" => $total_fee_net,
					"total_acc_amt" => $total_acc_amt,
					'admin_id' => $admin_id,
					'admin_nm' => $admin_nm,
					'idx' => $acc_idx
				]
				);
			DB::commit();
			return response()->json(["result" => 1]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(["result" => 0]);
		}
	}

	public function show_delete(Request $request)
	{
		$acc_idx = $request->input("idx");
		try {
			DB::beginTransaction();
			// 마감 상세 내역 삭제
			$sql = "delete from store_account_closed_list where acc_idx = :idx";
			DB::delete($sql, ["idx" => $acc_idx]);
			// 마감 마스터 삭제
			$sql = "delete from store_account_closed where idx = :idx";
			DB::delete($sql, ["idx" => $acc_idx]);
			DB::commit();
			return response()->json(["result" => 1], 200);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(["result" => 0], 200);
		}
	}

	public function show_close(Request $request)
	{
		$acc_idx = $request->input("idx");
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;
		try {
			DB::beginTransaction();
			// 마감완료
			$sql = "
				update store_account_closed set
					closed_yn = 'Y',
					closed_date = now(),
					admin_id = :admin_id,
					admin_nm = :admin_nm
				where idx = :idx";
			DB::update($sql, ["admin_id" => $admin_id, "admin_nm" => $admin_nm, "idx" => $acc_idx]);
			DB::commit();
			return response()->json(["result" => 1]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(["result" => 0]);
		}
	}
}
