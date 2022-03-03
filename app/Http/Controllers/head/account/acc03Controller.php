<?php

namespace App\Http\Controllers\head\account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\SLib;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class acc03Controller extends Controller
{
    public function index()
    {
        $immutable = CarbonImmutable::now();

        $sdate = $immutable->sub(1, 'month')->startOfMonth()->format('Y-m-d');
        $edate = $immutable->sub(1, 'month')->endOfMonth()->format('Y-m-d');

        // 20110101
		if ($sdate <= '20110101') $sdate = '20110101';
		if ($edate <= '20110101') $edate = '20110131';

        $values = [
            'sdate' => $sdate,
            'edate' => $edate,
            'closed_yn' => ["전체", "Y", "N"]
        ];

        return view( Config::get('shop.head.view') . '/account/acc03', $values);
    }

    public function search(Request $request)
    {
        $sdate = str_replace("-", "", $request->input("sdate"));
        $edate = str_replace("-", "", $request->input("edate"));
        $closed_state = $request->input("closed_state");
        $com_cd = $request->input("com_cd");

        $where = "";
        if ($closed_state != "") $where .= " and a.closed_yn = '${closed_state}'";
        if ($com_cd != "") $where .= " and a.com_id = '${com_cd}'";

        $sql = "
			select
				a.closed_yn,concat_ws('~',a.sday,a.eday) as closed_day,
				b.com_nm,cd.code_val as margin_type,
				a.sale_amt,a.clm_amt,a.dc_amt,
				( a.coupon_amt - a.allot_amt ) as coupon_com_amt,
				a.dlv_amt, a.etc_amt,
				a.sale_net_taxation_amt, a.sale_net_taxfree_amt,a.sale_net_amt,a.tax_amt,
				a.fee, a.fee_dc_amt, a.fee_net, a.acc_amt, a.allot_amt,

				date_format(a.tax_day,'%Y%m%d') as tax_day,
				date_format(a.pay_day,'%Y%m%d') as pay_day,
				a.idx,a.com_id,a.sday,a.eday
			from
				account_closed a inner join company b on ( a.com_id = b.com_id )
				inner join code cd on cd.code_kind_cd = 'G_MARGIN_TYPE' and b.margin_type = cd.code_id
				left outer join tax t on a.tax_no = t.idx
			where
				a.sday >= '20110101' and a.sday <= '$edate' and a.eday >= '$sdate' $where
			order by
				a.acc_amt desc
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

    public function show(Request $request)
    {
        $idx = $request->input('idx');
        $sql = "
            select
                a.reg_date,a.closed_date,a.closed_yn,
                a.com_id, c.com_nm, c.com_type,
                a.sday,a.eday,a.admin_id,a.admin_nm
            from account_closed a inner join company c on a.com_id = c.com_id
            where a.idx = '$idx'
        ";
        $row = DB::selectOne($sql);

        if($row) {
			$sday = $row->sday;
			$eday = $row->eday;
			$com_nm = $row->com_nm;
			$reg_date = $row->reg_date;
			$closed_date = $row->closed_date;
			$closed_yn = $row->closed_yn;
			$admin_nm = $row->admin_nm;
		} else {
			$sday = "";
			$eday = "";
			$com_nm = "";
			$reg_date = "";
			$closed_date = "";
			$closed_yn = "";
			$admin_nm = "";
		}

        $values = [
            "idx"			=> $idx,
			"sday"			=> $sday,
			"eday"			=> $eday,
			"com_nm"		=> $com_nm,
			"reg_date"		=> $reg_date,
			"closed_date"	=> $closed_date,
			"closed_yn"		=> $closed_yn,
			"admin_nm"		=> $admin_nm
        ];

        return view( Config::get('shop.head.view') . '/account/acc03_show', $values);
    }

    public function show_search(Request $request)
    {
        $idx = $request->input('idx');

        $sql = "
			SELECT
				acc_type.code_val AS type, w.state_date, o.ord_no, o.ord_opt_no,
				IF((SELECT count(*) FROM order_opt WHERE ord_no = o.ord_no) > 1, 'Y','') AS multi_order,
				if(o.coupon_no <>0,(select coupon_nm from coupon where coupon_no = o.coupon_no),'') as coupon_nm,
				o.goods_nm , replace(o.goods_opt,'^',':') as opt_nm, g.style_no,
				opt_type.code_val as opt_type, cp.com_nm, m.user_nm, pay_type.code_val as pay_type,
				'Y' as tax_yn,
				w.qty as qty,
				w.sale_amt, w.clm_amt, w.dc_amt, ( w.coupon_amt - w.allot_amt ) as coupon_com_amt,
				w.dlv_amt, w.etc_amt as fee_etc_amt,
				w.sale_net_taxation_amt, w.sale_net_taxfree_amt, w.sale_net_amt, w.tax_amt,
				w.fee_ratio , w.fee, w.fee_dc_amt, w.fee_net,
				w.acc_amt, w.allot_amt AS fee_allot_amt,

				cd.code_val as ord_state ,cd2.code_val as clm_state,
				date_format(o.ord_date,'%Y%m%d') AS ord_date, date_format(o.dlv_end_date,'%Y%m%d') AS dlv_end_date,
				if(o.clm_state in (60,61),
				(
					SELECT
						date_format(max(end_date),'%Y%m%d') clm_end_date
					FROM
						claim
					WHERE
						ord_opt_no = o.ord_opt_no),''
				) AS clm_end_date,
				w.bigo, g.goods_no, g.goods_sub, w.idx, w.acc_idx
			FROM
				account_closed closed
				inner join account_closed_list w on w.acc_idx = closed.idx
				inner join order_opt o on o.ord_opt_no = w.ord_opt_no
				inner join order_mst m on o.ord_no = m.ord_no
				inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
				inner join payment p on m.ord_no = p.ord_no
				left outer join company cp on o.sale_place = cp.com_id and cp.com_type = '4'
				left outer join code cd on cd.code_kind_cd = 'G_ORD_STATE' and cd.code_id = o.ord_state
				left outer join code cd2 on cd2.code_kind_cd = 'G_CLM_STATE' and cd2.code_id = o.clm_state
				left outer join code opt_type on opt_type.code_kind_cd = 'G_ORD_TYPE' and o.ord_type = opt_type.code_id
				left outer join code acc_type on acc_type.code_kind_cd = 'G_ACC_TYPE' and w.type = acc_type.code_id
				left outer join code pay_type on pay_type.code_kind_cd = 'G_PAY_TYPE' and p.pay_type = pay_type.code_id
			WHERE
				closed.idx = '${idx}'
			ORDER BY
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
				$bigo	 		= str_replace(",", "", $fields[11]);
				$idx	 		= str_replace(",", "", $fields[12]);

				$sql = "
					update account_closed_list set
						dlv_amt					= :dlv_amt,
						etc_amt					= :etc_amt,
						sale_net_taxation_amt	= :sale_net_taxation_amt,
						sale_net_taxfree_amt	= :sale_net_taxfree_amt,
						sale_net_amt			= :sale_net_amt,
						tax_amt					= :tax_amt,
						fee_ratio				= :fee_ratio,
						fee						= :fee,
						fee_net	 				= :fee_net,
						bigo					= :bigo,
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
						'bigo' => $bigo,
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
					account_closed_list
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
				update account_closed set
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
			$sql = "delete from account_closed_list where acc_idx = :idx";
			DB::delete($sql, ["idx" => $acc_idx]);
			// 마감 마스터 삭제
			$sql = "delete from account_closed where idx = :idx";
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
				update account_closed set
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
