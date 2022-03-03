<?php

namespace App\Http\Controllers\head\account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\SLib;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class acc04Controller extends Controller
{
    public function index()
    {
		$immutable = CarbonImmutable::now();
        $sdate = $immutable->sub(1, 'month')->format('Y-m-d');
        $values = [
			'sdate'         => $sdate,
			'edate'         => date("Y-m-d"),
			'tax_state' 	=> SLib::getCodes('G_TAX_STATE')
		];
        return view( Config::get('shop.head.view') . '/account/acc04', $values);
    }

    public function search(Request $request)
    {
        $sdate = str_replace("-", "", $request->input("sdate", date("Ymd")));
        $edate = new \DateTime($request->input("edate", date("Ymd", strtotime("-7 Day"))));
		$edate = $edate->format('Ymd');

		$tax_state = $request->input("tax_state");
		$tax_state_ex = $request->input("tax_state_ex", '');
        $com_id = $request->input("com_cd", "");

		$where = "";
		$tax_where = "";

		if ($com_id != "") $where .= " and a.com_id = '$com_id' ";
		if ($tax_state != "") {
			// 미발행인 경우 자료 없는 경우도 포함되므로
			if ( $tax_state == "0" ) {
				$where .= " and ( t.state = '${tax_state}' or t.state is null ) ";
			} else {
				$where .= " and t.state = '${tax_state}' ";
			}
		}
		if ($tax_state_ex != "") {
			$tax_where .= " and ifnull(t.state,0) <> '${tax_state_ex}' ";
		}

		$sql = "
			select
				'','', a.idx,
				concat_ws('~',date_format(a.sday,'%Y%m%d'),date_format(a.eday,'%Y%m%d')) as day,
				d.com_nm,
				a.sale_amt, a.clm_amt, a.dc_amt, ( a.coupon_amt - a.allot_amt ) as coupon_com_amt,
				a.dlv_amt, a.etc_amt,
				a.sale_net_taxation_amt, a.sale_net_taxfree_amt, a.sale_net_amt, a.tax_amt,
				a.fee, a.fee_dc_amt, a.fee_net, a.acc_amt, a.allot_amt, a.tax_day,a.pay_day
			from
				account_closed a inner join company d on ( a.com_id = d.com_id )
				left outer join tax t on a.tax_no = t.idx ${tax_where}
			where
				a.sday >= '20110101' and a.sday <= '${edate}' and a.eday >= '${sdate}' and a.closed_yn = 'Y'
				${where}
			order by
				a.com_id
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

	/**
	 * 세금계산서 발행일 수정
	 */
	public function pubTax(Request $request)
	{
		$data = urldecode($request->input('data', ""));
		$tax_day = new \DateTime($request->input('day'));
		$tax_day = $tax_day->format('Ymd');
		$datas = explode("::", $data);
		try {
			DB::beginTransaction();
			collect($datas)->map(function($item) use($tax_day) {
				$account_closed_idx = $item;
				$sql = "
					update account_closed set tax_day = :tax_day
					where idx = :idx
				";
				DB::update($sql, ['tax_day' => $tax_day, 'idx' => $account_closed_idx]);
			});
			DB::commit();
			return response()->json(["result" => 1]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(["result" => 0]);
		}
	}

	/**
	 * 지급일자 수정
	 */
	public function payTaxSheet(Request $request)
	{
		$data = urldecode($request->input('data', ""));
		$pay_day = new \DateTime($request->input('day'));
		$pay_day = $pay_day->format('Ymd');
		$datas = explode("::", $data);
		try {
			DB::beginTransaction();
			collect($datas)->map(function($item) use($pay_day) {
				$account_closed_idx = $item;
				$sql = "
					update account_closed set pay_day = :pay_day
					where idx = :idx
				";
				DB::update($sql, ['pay_day' => $pay_day, 'idx' => $account_closed_idx]);
			});
			DB::commit();
			return response()->json(["result" => 1]);
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json(["result" => 0]);
		}
	}
		
	/**
	 * 세금계산서 다운로드 팝업
	 */
	public function show(Request $request)
	{
		$data = $request->input('data');
		$values = [
			'date' => date("Y-m-d"),
			'data' => $data
		];
        return view( Config::get('shop.head.view') . '/account/acc04_show', $values);
	}

	/**
	 * 세금계산서 다운로드
	 */
	public function show_search(Request $request)
	{
		$pub_day		= $request->input("date",date("Ymd"));
		$type			= $request->input("type","01");
		$t2				= $request->input("t2","01");
		$biz_rp			= $request->input("biz_rp","Y");

		$data			= urldecode($request->input("data",""));

		$datas 			= explode("::",$data);
		$idxs			= join(",", $datas);

		$sale_place = "HEAD_OFFICE";

		// 세금계산서 발행을 위한 공급자 정보
		$sql = "
			select
				com_nm,ceo,biz_num,uptae,upjong,concat(ifnull(addr1, ''),' ',ifnull(addr2, '')) as addr,
				staff_email1,staff_email2
			from company where com_id = '$sale_place'
		";
		$row = DB::selectOne($sql);

		$pub_cname		= $row->com_nm;
		$pub_ceo		= $row->ceo;
		$pub_biznum		= $row->biz_num;
		$pub_uptae		= $row->uptae;
		$pub_upjong		= $row->upjong;
		$pub_addr		= $row->addr;

		if ($row->staff_email2 == "") {
			$pub_email1 = $row->staff_email1;
			
		} else {
			$pub_email1 = $row->staff_email2;
		}

		$item_txt ="tax";
		$sql = "
			select
				a.com_id,a.sday,a.eday,b.name,
				b.ceo,b.biz_num,b.uptae,b.upjong,concat(ifnull(b.addr1, ''),' ',ifnull(b.addr2, '')) as addr,staff_email1,staff_email2,
				a.fee_net as fee,
				date_format(a.closed_date,'%Y%m%d') as closed_date
			from
				account_closed a inner join company b on ( a.com_id = b.com_id )
			where
				a.idx in ( $idxs )
		";

		$rows = DB::select($sql);

		$header_names = array(
			"구분",
			"작성일자",
			"공급자 등록번호",
			"공급자 상호",
			"공급자 성명",
			"공급자 사업장주소",
			"공급자 업태",
			"공급자 종목",
			"공급자 이메일",
			"공급받는자 등록번호",
			"공급자받는자 상호",
			"공급자받는자 성명",
			"공급자받는자 사업장주소",
			"공급자받는자 업태",
			"공급자받는자 종목",
			"공급자받는자 이메일1",
			"공급자받는자 이메일2",
			"공급가액",
			"세액",
			"일자1",
			"품목1",
			"규격1",
			"수량1",
			"단가1",
			"공급가액1",
			"세액1",
			"영수/청구",
		);
		
		$list = [];
		foreach ($rows as $row) {

			$fee	= $row->fee;
			$amt	= round($fee / 1.1);
			$tax	= $fee - $amt;
			$item	= sprintf("%s (%s~%s)",$item_txt,$row->sday,$row->eday);

			if($row->staff_email2 == ""){
				$email1 = $row->staff_email1;
				$email2 = "";
			} else {
				$email1 = $row->staff_email2;
				$email2 = $row->staff_email1;
			}

			if($biz_rp == "Y"){
				$pub_biznum = str_replace("-","",$pub_biznum);
				$row->biz_num = str_replace("-","",$row->biz_num);
			}

			$array = array(
				$type,$pub_day,$pub_biznum,$pub_cname,$pub_ceo,$pub_addr,$pub_uptae,$pub_upjong,$pub_email1,
				$row->biz_num,$row->name,$row->ceo,$row->addr,$row->uptae,$row->upjong,$email1,$email2,
				$amt,$tax,
				sprintf("%02d",substr($pub_day,6,2)),$item,"","","",$amt,$tax,$t2
			);

			array_push($list, $array);
			
		}

		return response()->json([ "headers" => $header_names, "list" => $list ]);

	}

}
