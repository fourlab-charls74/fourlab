<?php

namespace App\Http\Controllers\head\promotion;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm03Controller extends Controller
{
  //
	public function index() {
		$credit_card_items  = SLib::getCodes("CREDIT_CARD_CD");

		$values = [
			'credit_card_items' => $credit_card_items
		];

		return view( Config::get('shop.head.view') . '/promotion/prm03',$values);
	}
	public function search(Request $request){
		$credit_card_cd = $request->input("credit_card_cd");

		$where = "";
		if($credit_card_cd != "") $where = " and a.credit_card_cd  = '$credit_card_cd' ";

		$sql = "
			select
				b.code_val,
				concat(concat(DATE_FORMAT(date_from,'%Y.%m.%d'),' ~ '), DATE_FORMAT(date_end,'%Y.%m.%d')) as ymd_fr_to,
				concat(concat(month1,' ~ '),month2) as month_fr_to,
				m_price, m_price_print,
				is_use, a.credit_card_cd
			from
				interest_free_card a inner join code b on (  a.credit_card_cd = b.code_id  )
			where
				b.code_kind_cd = 'CREDIT_CARD_CD' and b.code_id <> 'k'
				$where
		";


		$result = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
                "page" => 1,
                "page_cnt" => count($result),
                "page_total" => 1
            ),
            "body" => $result
        ]);

	}

	/*
		Function: GetInfomation
		무이자 카드 상세정보
	*/

	public function GetInfomation($credit_card_cd) {

		$credit_card_cd = request("credit_card_cd");
		$sql = "
			select
				a.credit_card_cd,b.code_val,
				month1,month2,m_price,m_price_print,
				date_from,date_end,is_use
			from
				interest_free_card a inner join code b on (  a.credit_card_cd = b.code_id  )
			where
				b.code_kind_cd = 'CREDIT_CARD_CD' and b.code_id <> 'k'
				and a.credit_card_cd = '$credit_card_cd'
		";
		//$x2gate->select($conn,$sql,"xml");
		$result = DB::selectOne($sql);
		return response()->json([
            "code" => 200,
            "result" => $result
        ]);

	}

	public function Command($cmd = '', Request $request){
		$credit_card_cd	= $request->input("credit_card_cd");
		$month_fr		= $request->input("month_fr");
		$month_to		= $request->input("month_to");
		$m_price		= $request->input("m_price");
		$m_price_print	= $request->input("m_price_print");
		$date_fr		= $request->input("date_fr");
		$date_to		= $request->input("date_to");
		$is_use			= $request->input("is_use");

		$return_code = 0;
		
		/*
		echo $cmd;
		echo "<br>";
		*/
		$date_fr = str_replace("-", "", $date_fr);
		$date_to = str_replace("-", "", $date_to);
		if($cmd == "addcmd"){
			
			try {
				DB::table('interest_free_card')
				->where('credit_card_cd', $credit_card_cd)
				->delete();
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
			}

			if($return_code == 1){
				$sql = "
					insert into interest_free_card(
						credit_card_cd, month1, month2, m_price, m_price_print, date_from, date_end, is_use
					)values(
						'$credit_card_cd', '$month_fr', '$month_to', '$m_price', '$m_price_print', '$date_fr', '$date_to', '$is_use'
					)
				";
				try {
					DB::insert($sql);
					$return_code = 1;
				} catch(Exception $e){
					$return_code = -1;
				};

			}
		}else if($cmd == "editcmd"){
			$update_item = [
				"credit_card_cd"	=> $credit_card_cd,
				"month1"			=> $month_fr,
				"month2"			=> $month_to,
				"m_price"			=> $m_price,
				"m_price_print"		=> $m_price_print,
				"date_from"			=> $date_fr,
				"date_end"			=> $date_to,
				"is_use"			=> $is_use	
			];
			try {
				DB::table('interest_free_card')
				->where('credit_card_cd','=', $credit_card_cd)
				->update($update_item);
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
			}
		}else if($cmd == "delcmd"){
			
			try {
				DB::table('interest_free_card')
				->where('credit_card_cd', $credit_card_cd)
				->delete();
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
			}
		}

		return response()->json([
            "code" => 200,
            "return_code" => $return_code
        ]);
	}

}
