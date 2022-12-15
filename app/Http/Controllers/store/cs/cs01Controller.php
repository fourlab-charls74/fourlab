<?php

namespace App\Http\Controllers\store\cs;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\S_Stock;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

const KRW = 'KRW';

class cs01Controller extends Controller {

    public function index(Request $request)
	{
        $immutable = CarbonImmutable::now();
        $sdate = $immutable->sub(6, 'month')->format('Y-m-d');
        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'order_stock_states' => Slib::getCodes('STOCK_ORDER_STATE')
        ];
        return view( Config::get('shop.store.view') . '/cs/cs01', $values);
    }

	public function choice_index()
	{
		$immutable = CarbonImmutable::now();
        $sdate = $immutable->sub(6, 'month')->format('Y-m-d');
        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'order_stock_states' => Slib::getCodes('STOCK_ORDER_STATE')
        ];
        return view( Config::get('shop.store.view') . '/cs/cs01_choice', $values);
	}

    public function search(Request $request)
	{
		$sdate = str_replace('-','',$request->input("sdate"));
        $edate = str_replace('-','',$request->input("edate"));
		$invoice_no	= $request->input("invoice_no");
		$item = $request->input("item");
		$com_id = $request->input("com_cd");
		$com_nm = $request->input("com_nm");
		$state = $request->input("order_stock_state");
		$user_name = $request->input("user_name");
		$user_name_type = $request->input("user_name_type", "req_nm");

		$where = "where b.stock_date >= '$sdate' and b.stock_date <= '$edate'";
		$where2 = "where 1=1";
		$having = "having 1=1";

		if ($com_id != "") $where .= " and b.com_id = '" . Lib::quote($com_id) . "'";
		if ($state != "") $where .= " and b.state = '" . Lib::quote($state) . "'";
		if ($invoice_no != "") $where .= " and b.invoice_no like '%" . Lib::quote($invoice_no) . "%'";
		
		if ($com_nm != "") $where2 .= " and c.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if ($item != "") $where2 .= " and item_cds like '%" . Lib::quote($item) . "%' ";
		
		if ($user_name != "") $having .= " and $user_name_type like '%" . Lib::quote($user_name) . "%' ";

		/**
		 * 추후 입고 유저 롤에 대한 처리 필요
		 */
		// if ($role >= 1 && $role < 2) { 
		// 		$where .= " and b.id = '$id' ";
		// }

		$sql = "
			select
				b.stock_no, b.invoice_no, ar.code_val as area_type,
				date_format(b.stock_date, '%Y-%m-%d') as stock_date, 
				cd.code_val as state_nm, c.com_nm, s.item,
				b.currency_unit,b.exchange_rate,b.custom_amt, b.custom_tax,b.custom_tax_rate,
				b.tariff_amt, b.tariff_rate, b.freight_amt, b.freight_rate,
				s.qty, s.exp_qty, s.total_cost,
				-- ifnull((select sum(qty) from stock_product_buy_order where stock_no = b.stock_no),0) as buy_order_qty,
				s.item_cds as item_cds,
			    b.req_id,
				(select name from mgr_user where id = b.req_id) as req_nm, 
                b.req_rt,
                b.prc_id,
				(select name from mgr_user where id = b.prc_id) as prc_nm, 
                b.prc_rt, 
                b.fin_id, 
				(select name from mgr_user where id = b.fin_id) as fin_nm, 
                b.fin_rt,
                b.cfm_id, 
				(select name from mgr_user where id = b.cfm_id) as cfm_nm, 
                b.cfm_rt,
				b.rej_id, 
				(select name from mgr_user where id = b.rej_id) as rej_nm, 
                b.rej_rt
			from product_stock_order b
				inner join (
					select p.stock_no, group_concat(distinct p.item) as item,
					group_concat(distinct o.opt_kind_cd) as item_cds,
					sum(p.qty) as qty, sum(p.exp_qty) as exp_qty, sum(total_cost) as total_cost
					from product_stock_order b inner join product_stock_order_product p on b.stock_no = p.stock_no
						left outer join opt o on p.item = o.opt_kind_nm
					$where
					group by p.stock_no
				) s on b.stock_no = s.stock_no
				inner join company c on c.com_id = b.com_id
				left outer join code ar on ar.code_kind_cd = 'g_buy_order_ar_type' and ar.code_id = b.area_type
				left outer join code cd on cd.code_kind_cd = 'STOCK_ORDER_STATE' and cd.code_id = b.state
			$where2
			$having
			order by b.stock_date desc, b.req_rt desc
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
		$stock_no = $request->input('stock_no');
		$stock_date = date("Y-m-d");
		$invoice_no = "";
		$bl_no = "";
		$cmd = "addcmd";
		$com_id = "";
		$com_nm = "";
		$state = "10";
		$loc = "loc";
		$area_type = "D";
		$custom_tax_rate =  "0.00";
		$custom_amt = 0;
		$custom_tax = 0;
		$tariff_amt = 0;
		$tariff_rate = 0;
		$freight_amt = 0;
		$freight_rate = 0;
		$exchange_rate = "";
		$currency_unit = KRW;
		$opts = "";

		if ($stock_no != "") {
			
			$cmd = "editcmd";
			$sql = "
				select
					b.invoice_no, b.bl_no, date_format(b.stock_date, '%Y-%m-%d') as stock_date, b.stock_type, b.area_type,
					b.com_id, c.com_nm, b.item, b.currency_unit, b.exchange_rate,
					b.tariff_amt, b.tariff_rate, b.freight_amt, b.freight_rate,
					b.custom_amt,b.custom_tax,b.custom_tax_rate, b.state, b.loc,b.opts, b.req_id
				from product_stock_order b
					inner join company c on c.com_id = b.com_id
				where stock_no = '$stock_no'
			";
			$row = DB::selectOne($sql);

			if ($row) {

				$invoice_no = $row->invoice_no;
				$bl_no = $row->bl_no;
				$stock_date = $row->stock_date;
				$area_type = $row->area_type;
				$com_id = $row->com_id;
				$com_nm = $row->com_nm;
				$currency_unit = $row->currency_unit;
				$exchange_rate = $row->exchange_rate;
				$custom_amt = $row->custom_amt;
				$custom_tax = $row->custom_tax;
				$custom_tax_rate = $row->custom_tax_rate;
				$tariff_amt = $row->tariff_amt;
				$tariff_rate = $row->tariff_rate;
				$freight_amt = $row->freight_amt;
				$freight_rate = $row->freight_rate;
				$state = $row->state;
				$loc = $row->loc;
				$opts = $row->opts;

				/**
				 * 추후 입고 유저 롤에 대한 처리 필요
				 */
				// if(($role & 2) == 2 || $id == $rows["id"]){
				// } else {
				// 	RedirectUrl("/common/NotAuthority.php?pidx=$pid");
				// 	exit;
				// }

			} else {
				$msg = '존재하지 않는 입고입니다.';
				Lib::printMsg($msg, 'close');
				exit;
			}
		}

		$states = Slib::getCodes('STOCK_ORDER_STATE');
		$collection = $states->map(function ($item) {
			return collect($item)->only(['code_id','code_val'])->all();
		});

		// 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
		// 원가확정: 40 (20221122 추가)
		$states = [];
		foreach ($collection as $stt) {
			$cond = true;
			if ($state < 0) $cond = $stt['code_id'] == -10;
			if ($cond && $state > 0) $cond = $stt['code_id'] >= $state;
			if ($cond && $state < 30) $cond = $stt['code_id'] < 40;

			if($cond) $states[] = $stt;
		}

		if ($opts != ""){
			$col_opts = explode("\t", $opts);
		} else {
			$col_opts = array();
		};

        $values = [
			'stock_no' => $stock_no,
			'invoice_no' => $invoice_no,
			'bl_no' => $bl_no,
			'stock_date' => $stock_date,
			'area_type' => $area_type,
			'com_id' => $com_id,
			'com_nm' => $com_nm,
			'states' => $states,
			'state' => $state,
			"currency_unit"	=> $currency_unit,
			"exchange_rate"	=> Lib::cm($exchange_rate),
			"custom_amt" => Lib::cm($custom_amt),
			"custom_tax" => Lib::cm($custom_tax),
			"custom_tax_rate" => $custom_tax_rate,
			"tariff_amt" => $tariff_amt,
			"tariff_rate" => $tariff_rate,
			"freight_amt" => $freight_amt,
			"freight_rate" => $freight_rate,
			'cmd' => $cmd,
			'opts' => $opts,
			'opt_cnt' => count($col_opts),
			"col_opts" => $col_opts,
			"locs" => Slib::getCodes('G_STOCK_LOC'),
			"loc" => $loc
        ];

        return view( Config::get('shop.store.view') . '/cs/cs01_show', $values);
		
	}

	/**
	 * 입력된 커맨드 별로 response를 return 합니다.
	 */
	public function command(Request $request) {
		$cmd = $request->input('cmd');
		switch ($cmd) {
			case 'addcmd':
				$response = $this->addCmd($request);
				break;
			case 'editcmd':
				$response = $this->editCmd($request);
				break;
			case 'delcmd':
				$stock_no = $request->input('stock_no');
				$response = $this->delCmd($stock_no);
				break;
			case 'cancelcmd':
				$stock_no = $request->input('stock_no');
				$response = $this->cancelCmd($stock_no);
				break;
			case 'addstockcmd':
				$response = $this->addStockCmd($request);
				break;
			case 'product':
				$stock_no = $request->input('stock_no');
				$response = $this->listProduct($stock_no);
				break;
			case 'getgood':
				$prd_cd = $request->input("prd_cd");
				$response = $this->getGood($prd_cd);
				break;
			case 'getinvoiceno':
				$com_id = $request->input('com_id');
				$invoice_no = $this->getInvoiceNo($com_id);
				$response = response()->json(['code' => 1, 'invoice_no' => $invoice_no], 200);
				break;
			case 'checkopt':
				$response = $this->checkOption($request);
				break;
			case 'import':
				$response = $this->importExcel($request);
				break;
			default:
				$message = 'Command not found';
				$response = response()->json(['code' => 0, 'message' => $message], 200);
		};
		return $response;
	}

	/**
	 * 입고 추가
	 */
	public function addCmd(Request $request) 
	{
		$id = Auth::guard('head')->user()->id;

		// Form
		$invoice_no				= $request->input("invoice_no");			//송장번호
		$bl_no					= $request->input("bl_no", "");				//통관번호 (B/L No.)
		$stock_date				= str_replace('-','',$request->input("stock_date"));	//입고일자
		$stock_type				= "A";										//입고구분 (일괄/발주)
		$com_id					= $request->input("com_id");				//공급처
		$item					= "";										//품목
		$state					= $request->input("state");					//입고상태
		$loc					= $request->input("loc");					//위치

		$currency_unit			= $request->input("currency_unit");			//화폐단위
		$exchange_rate			= $request->input("exchange_rate");			//환율
		$exchange_rate			= str_replace(",","",$exchange_rate);
		$custom_amt				= $request->input("custom_amt");			//신고금액
		$custom_amt				= str_replace(",","",$custom_amt);
		$tariff_amt				= $request->input("tariff_amt");			//관세총액
		$tariff_amt				= str_replace(",","",$tariff_amt);
		$freight_amt			= $request->input("freight_amt");			//운임비
		$freight_amt			= str_replace(",","",$freight_amt);
		$custom_tax				= intval($tariff_amt) + intval($freight_amt); //통관비 = 관세총액 + 운임비

		$tariff_rate = 0;
		$freight_rate = 0;
		$custom_tax_rate = 0;
		
		$area_type 				= ($currency_unit == KRW) ? "D" : "O"; 		//입고지역
		$data					= $request->input("data");

		if ($currency_unit == KRW) {
			$exchange_rate = 0;
			$custom_amt = 0;
			$tariff_amt = 0;
			$freight_amt = 0;
		}

		$opts		= "";
		$opt_cnt 	= 0;

		$stock_no = 0;

		try {
            DB::beginTransaction();

			if ($invoice_no == "") {
				$invoice_no = $this->getInvoiceNo($com_id);
			}

			if ($currency_unit != KRW) {
				$tariff_rate = $custom_amt < 1 ? 0 : round(($tariff_amt / $custom_amt) * 100, 2); // 관세율 = 관세총액 / 신고금액
				$freight_rate = $custom_amt < 1 ? 0 : round(($freight_amt / $custom_amt) * 100, 2); // 운임율 = 운임비 / 신고금액
				$custom_tax_rate = $custom_amt < 1 ? 0 : round(($custom_tax / $custom_amt) * 100, 2); // 통관세율 = 통관비 / 신고금액
			}
			
			// 등록
			$params = [
				'invoice_no'	=> $invoice_no, 
				'bl_no'			=> $bl_no, 
				'stock_date'	=> $stock_date,
				'stock_type'	=> $stock_type,
				'area_type'		=> $area_type,
				'com_id'		=> $com_id,
				'item'			=> $item,
				'currency_unit'	=> $currency_unit,
				'exchange_rate'	=> $exchange_rate,
				'tariff_amt'	=> $tariff_amt,
				'tariff_rate'	=> $tariff_rate, 
				'freight_amt'	=> $freight_amt,
				'freight_rate'	=> $freight_rate, 
				'custom_amt'	=> $custom_amt,
				'custom_tax'	=> $custom_tax,
				'custom_tax_rate' => $custom_tax_rate, 
				'state'			=> $state,
				'loc'			=> $loc,
				'opts'			=> $opts,
				'req_id'		=> $id,
				'req_rt'		=> now(),
				'ut'			=> now()
			];

			if ($state >= 20) {
				$params = array_merge($params, [
					'prc_id'		=> $id,
					'prc_rt'		=> now(),
				]);
			}
			if ($state >= 30) {
				$params = array_merge($params, [
					'fin_id'		=> $id,
					'fin_rt'		=> now(),
				]);
			}

			$stock_no = DB::table('product_stock_order')->insertGetId($params);
			
			// 개별상품 입고처리
			$params = array_merge($params, [ 'stock_no' => $stock_no ]);
			$this->saveStockOrderProduct("E", $params, $data);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$message = "입고추가 중 에러가 발생하였습니다.";
			$code = 0;
			if ($e->getPrevious()) {
				$message = $e->getMessage();
				$code = $e->getCode();
			}
            return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => 1, 'message' => "입고 추가가 완료되었습니다."], 201);
	}

	/**
	 * 입고 수정
	 */
	public function editCmd(Request $request) 
	{
		$id = Auth::guard('head')->user()->id;

		// Form
		$stock_no				= $request->input("stock_no");				//입고번호
		$invoice_no				= $request->input("invoice_no");			//송장번호
		$bl_no					= $request->input("bl_no", "");				//통관번호 (B/L No.)
		$stock_date				= str_replace('-','',$request->input("stock_date")); //입고일자
		$com_id					= $request->input("com_id");				//공급처
		$item					= $request->input("item");					//품목
		$state					= $request->input("state");					//입고상태
		$loc					= $request->input("loc");					//위치
		
		$currency_unit			= $request->input("currency_unit");			//화폐단위
		$exchange_rate			= $request->input("exchange_rate");			//환율
		$exchange_rate			= str_replace(",", "", $exchange_rate);
		$custom_amt				= $request->input("custom_amt");			//신고금액
		$custom_amt				= str_replace(",","", $custom_amt);
		$tariff_amt				= $request->input("tariff_amt");			//관세총액
		$tariff_amt				= str_replace(",","",$tariff_amt);
		$freight_amt			= $request->input("freight_amt");			//운임비
		$freight_amt			= str_replace(",","",$freight_amt);
		$custom_tax				= intval($tariff_amt) + intval($freight_amt); //통관비 = 관세총액 + 운임비

		$tariff_rate = 0;
		$freight_rate = 0;
		$custom_tax_rate = 0;
		
		$area_type				= ($currency_unit == KRW) ? "D" : "O"; 		//입고지역
		$data					= $request->input("data");

		if ($currency_unit == KRW) {
			$exchange_rate = 0;
			$custom_amt = 0;
			$tariff_amt = 0;
			$freight_amt = 0;
		}

		$opts		= "";
		$opt_cnt 	= 0;

		$cur_state = DB::table('product_stock_order')->where('stock_no', $stock_no)->value('state');

		try {
			if ($cur_state < 40) { // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30, 원가확정: 40
				DB::beginTransaction();

				if ($currency_unit != KRW) {
					$tariff_rate = $custom_amt < 1 ? 0 : round(($tariff_amt / $custom_amt) * 100, 2); // 관세율 = 관세총액 / 신고금액
					$freight_rate = $custom_amt < 1 ? 0 : round(($freight_amt / $custom_amt) * 100, 2); // 운임율 = 운임비 / 신고금액
					$custom_tax_rate = $custom_amt < 1 ? 0 : round(($custom_tax / $custom_amt) * 100, 2); // 통관세율 = 통관비 / 신고금액
				}
				
				// 수정
				$params = [
					'invoice_no'	=> $invoice_no, 
					'bl_no'			=> $bl_no, 
					'stock_date'	=> $stock_date,
					'area_type'		=> $area_type,
					'com_id'		=> $com_id,
					// 'item'			=> $item,
					'currency_unit'	=> $currency_unit,
					'exchange_rate'	=> $exchange_rate,
					'tariff_amt'	=> $tariff_amt,
					'tariff_rate'	=> $tariff_rate, 
					'freight_amt'	=> $freight_amt,
					'freight_rate'	=> $freight_rate, 
					'custom_amt'	=> $custom_amt,
					'custom_tax'	=> $custom_tax, 
					'custom_tax_rate' => $custom_tax_rate, 
					'state'			=> $state,
					'loc'			=> $loc,
					'opts'			=> $opts,
					'ut'			=> now()
				];

				if ($cur_state == 10 && $state >= 20) {
					$params = array_merge($params, [
						'prc_id'		=> $id,
						'prc_rt'		=> now(),
					]);
				}
				if (($cur_state == 10 || $cur_state == 20) && $state >= 30) {
					$params = array_merge($params, [
						'fin_id'		=> $id,
						'fin_rt'		=> now(),
					]);
				}
				if ($cur_state == 30 && $state == 40) {
					$params = array_merge($params, [
						'cfm_id'		=> $id,
						'cfm_rt'		=> now(),
					]);
				}

				DB::table('product_stock_order')->where('stock_no', $stock_no)->update($params);

				// 개별상품 입고처리
				$params = array_merge($params, [ 'stock_no' => $stock_no ]);
				if ($cur_state == 30) {
					$data = array_filter($data, function($row) {
						return isset($row['stock_prd_no']);
					});
				}
				$this->saveStockOrderProduct("E", $params, $data, $cur_state);

				DB::commit();
			}
		} catch (Exception $e) {
			DB::rollback();
			$message = "입고 수정시 에러가 발생하였습니다.";
			$code = 0;
			if ($e = $e->getPrevious()) {
				$message = $e->getMessage();
				$code = $e->getCode();
			}
            return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => 1, 'message' => "입고 수정이 완료되었습니다."], 200);
	}

	/**
	 * 입고 삭제
	 */
	public function delCmd($stock_no) { // 입고번호
		$sql = "
			select state from product_stock_order
			where stock_no = '$stock_no'
		";
		$row = DB::selectOne($sql);

		if($row->state < 30){
			try {
				DB::beginTransaction();
				$sql = "
					delete from product_stock_order
					where stock_no = :stock_no
				";
				DB::delete($sql, ['stock_no' => $stock_no]);
				$sql = "
					delete from product_stock_order_product
					where stock_no = :stock_no
				";
				DB::delete($sql, ['stock_no' => $stock_no]);

				DB::commit();
			} catch (Exception $e) {
				DB::rollBack();
				$msg = "삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
				return response()->json(['code' => 0, 'message' => $msg], 200);
			}
		}
		return response()->json(['code' => 1, 'message' => "입고가 삭제되었습니다."], 200);
	}

	/**
	 * 입고 취소
	 */
	public function cancelCmd($stock_no) { // 입고번호
		$code = 1;
		$msg = '';

		$id = Auth::guard('head')->user()->id;
		$name = Auth::guard('head')->user()->name;
		$user = [
			'id' => $id,
			'name' => $name
		];

		$row = DB::table('product_stock_order')->where('stock_no', $stock_no)->select('state', 'loc')->first();
		$loc = '';
		if ($row != null && $row->state == 30) $loc = $row->loc;

		try {
			DB::beginTransaction();
			/**
			 * 재고 차감(-)
			 */
			$s = new S_Stock($user);
			$s->SetLoc($loc);

			$sql = "
				select stock_no,invoice_no,goods_no,prd_cd,opt_kor as opt,unit_cost,cost_notax,cost,qty
				from product_stock_order_product
				where stock_no = '$stock_no' and state = 30
			";

			$rows = DB::select($sql);

			foreach ($rows as $row) {
				$sg_stock = DB::table('product_stock')->select('wqty')->where('prd_cd', $row->prd_cd)->first();
				if ($sg_stock != null) {
					if ($sg_stock->wqty < $row->qty) {
						$msg = '창고재고가 부족한 상품이 존재하여 입고취소가 불가능합니다.';
						throw new Exception($msg);
					}
				}

				$invoice_no = $row->invoice_no;
				$goods_no = $row->goods_no;
				$prd_cd = $row->prd_cd;
				$opt = $row->opt;
				$cost = $row->cost;
				$qty = $row->qty;

				$stock = array(
					"type" => 9,
					"etc" => "입고취소",
					"qty" => $qty,
					"goods_no" => $goods_no,
					"prd_cd" => $prd_cd,
					"goods_opt" => $opt,
					"wonga" => $cost,
					"invoice_no" => $invoice_no,
				);
				$s->Minus( $stock );
				
				$sql = "
					update product_stock_order_product set state = '-10'
					where stock_no = '$stock_no' and goods_no = '$goods_no' and prd_cd = '$prd_cd'
							and opt_kor = '$opt' and cost = '$cost'
				";
				DB::update($sql);
			}

			$sql = "
				update product_stock_order set 
					state = '-10', rej_id = '$id', rej_rt = now()
				where stock_no = '$stock_no'
			";
			DB::update($sql);

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			$code = -1;
			if ($msg == '') $msg = '입고 취소를 실패하였습니다. 다시 한번 시도하여 주십시오.';
		}
		return response()->json(['code' => $code, 'message' => $msg], 200);
	}

	/**
	 * 추가입고
	 */
	public function addStockCmd(Request $request) { // 입고번호, 데이터

		$stock_no = $request->input('stock_no');
		$invoice_no				= $request->input("invoice_no");			//송장번호
		$bl_no					= $request->input("bl_no", "");				//통관번호 (B/L No.)
		$stock_date				= str_replace('-','',$request->input("stock_date")); //입고일자
		$com_id					= $request->input("com_id");				//공급처
		$item					= $request->input("item");					//품목
		$loc					= $request->input("loc");					//위치
		
		$currency_unit			= $request->input("currency_unit");			//화폐단위
		$exchange_rate			= $request->input("exchange_rate");			//환율
		$exchange_rate			= str_replace(",", "", $exchange_rate);
		$custom_amt				= $request->input("custom_amt");			//신고금액
		$custom_amt				= str_replace(",","", $custom_amt);
		$tariff_amt				= $request->input("tariff_amt");			//관세총액
		$tariff_amt				= str_replace(",","",$tariff_amt);
		$freight_amt			= $request->input("freight_amt");			//운임비
		$freight_amt			= str_replace(",","",$freight_amt);
		$custom_tax				= intval($tariff_amt) + intval($freight_amt); //통관비 = 관세총액 + 운임비

		$tariff_rate = 0;
		$freight_rate = 0;
		$custom_tax_rate = 0;

		$area_type				= ($currency_unit == KRW) ? "D" : "O"; 		//입고지역
		$data 					= $request->input('data');

		if ($currency_unit == KRW) {
			$exchange_rate = 0;
			$custom_amt = 0;
			$tariff_amt = 0;
			$freight_amt = 0;
		}

		$opts		= "";
		$opt_cnt 	= 0;

		$cur_state = DB::table('product_stock_order')->where('stock_no', $stock_no)->value('state');

		try {
			if ($cur_state == 30) { // 입고완료 시에만 추가입고 가능
				DB::beginTransaction();
	
				if ($currency_unit != KRW) {
					$tariff_rate = $custom_amt < 1 ? 0 : round(($tariff_amt / $custom_amt) * 100, 2); // 관세율 = 관세총액 / 신고금액
					$freight_rate = $custom_amt < 1 ? 0 : round(($freight_amt / $custom_amt) * 100, 2); // 운임율 = 운임비 / 신고금액
					$custom_tax_rate = $custom_amt < 1 ? 0 : round(($custom_tax / $custom_amt) * 100, 2); // 통관세율 = 통관비 / 신고금액
				}

				$params = [
					'invoice_no'	=> $invoice_no, 
					'bl_no'			=> $bl_no, 
					'stock_date'	=> $stock_date,
					'area_type'		=> $area_type,
					'com_id'		=> $com_id,
					'currency_unit'	=> $currency_unit,
					'exchange_rate'	=> $exchange_rate,
					'tariff_amt'	=> $tariff_amt,
					'tariff_rate'	=> $tariff_rate, 
					'freight_amt'	=> $freight_amt,
					'freight_rate'	=> $freight_rate, 
					'custom_amt'	=> $custom_amt,
					'custom_tax'	=> $custom_tax, 
					'custom_tax_rate' => $custom_tax_rate, 
					'state'			=> $cur_state,
					'loc'			=> $loc,
					'opts'			=> $opts,
					'ut'			=> now(),
				];
				DB::table('product_stock_order')->where('stock_no', $stock_no)->update($params);

				// 개별상품 입고처리
				$values = array_merge($params, [ 'stock_no' => $stock_no ]);
				$this->saveStockOrderProduct("A", $values, $data);

				DB::commit();
			}
		} catch(Exception $e) {
			DB::rollBack();
			$message = "입고 추가에 실패했습니다.";
			$code = 0;
			if ($e->getPrevious()) {
				$message = $e->getMessage();
				$code = $e->getCode();
			}
			return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => 1, 'message' => "입고 추가되었습니다."], 200);
	}

	public function listProduct($stock_no) {
		$sql = "
			select
				if(state = 30 or state = -10,'0','2') as chk
				, stock_prd_no as stock_prd_no
				, s.item
				, s.brand
				, s.style_no
				, s.goods_no
				, s.prd_cd
				, g.goods_nm
				, g.goods_nm_eng
				, g.brand_nm
				, g.goods_sh
				, g.price
				, ifnull(
					(select goods_opt from product_stock where goods_no = s.goods_no and prd_cd = s.prd_cd and goods_opt = s.opt_kor)
					, concat('err:',ifnull(s.opt_kor, ''))
				  ) as opt_kor
				, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
				, pc.color
				, pc.size
				, ifnull(s.exp_qty, 0) as exp_qty
				, ifnull(s.qty, 0) as qty
				, ifnull(s.prd_tariff_rate, 0) as prd_tariff_rate
				, ps.in_qty
				, ps.qty as total_qty
				, ps.wqty as sg_qty
				, s.unit_cost as unit_cost
				, (s.unit_cost * s.qty) as unit_total_cost
				, s.cost as cost
				, (s.cost * s.qty) as total_cost
				, (s.cost_notax * s.qty) as total_cost_novat
				, date_format(s.stock_date, '%Y-%m-%d') as stock_date
			from product_stock_order_product s
				inner join product_code pc on pc.prd_cd = s.prd_cd
				inner join goods g on s.goods_no = g.goods_no
				inner join product_stock ps on s.prd_cd = ps.prd_cd
			where stock_no = '$stock_no'
			order by stock_prd_no asc
		";
		$rows = DB::select($sql);
		return response()->json(['rows' => $rows], 200);
	}

	/**
	 * 상품 입고
	 * $type: 추가입고(A) / 기본(E) 
	 */
	public function saveStockOrderProduct($type, $values, $products, $cur_state = 0)
	{
		try {
			DB::beginTransaction();
			$stock_no = $values['stock_no'];
			$invoice_no = $values['invoice_no'] ?? '';
			$state = $values['state'] ?? 10;
			$loc = $values['loc'] ?? '';

			if ($type != "A") { // 추가입고가 아닐때
				$sql = "
					delete from product_stock_order_product
					where stock_no = :stock_no
				";
				DB::delete($sql, ['stock_no' => $stock_no]);
			}

			if (count($products) > 0) {
				$id = Auth::guard('head')->user()->id;

				for ($i=0; $i<count($products); $i++) {
					$row = $products[$i];
					$stock_prd_no = $row['stock_prd_no'] ?? 0;

					if ($type != "A" || $stock_prd_no <= 0) {
						$item = $row['item'] ?? '';
						$brand = $row['brand'] ?? '';
						$prd_cd = $row['prd_cd'] ?? '';
						$style_no = $row['style_no'] ?? '';
						$goods_no = $row['goods_no'] ?? 0;
						
						if ($goods_no > 0) {
							$exp_qty = $row['exp_qty'] ?? 0;
							$qty = $row['qty'] ?? 0;
							$unit_cost = str_replace(",","",str_replace("\\","",$row['unit_cost'])); // 단가
							$prd_tariff_rate = round($row['prd_tariff_rate'] ?? 0, 2); // 상품별 관세율
							$cost = str_replace(",","",$row['cost']);
							$total_cost = str_replace(",","",$row['total_cost']);
							$cost_notax = round($cost / 1.1);

							$opt = array_key_exists('opt_kor', $row) ? trim($row['opt_kor']) : "NONE";
							if ($opt == "") $opt = "NONE";

							if ($opt != "" && ($qty > 0 || $exp_qty > 0)) {
								$params = [
									'stock_no' => $stock_no,
									'invoice_no' => $invoice_no,
									'com_id' => $values['com_id'] ?? '',
									'item' => $item,
									'brand' => $brand,
									'prd_cd' => $prd_cd,
									'style_no' => $style_no,
									'goods_no' => $goods_no,
									'goods_sub' => 0,
									'opt_kor' => $opt,
									'exp_qty' => $exp_qty,
									'qty' => $qty,
									'unit_cost' => $unit_cost,
									'prd_tariff_rate' => $prd_tariff_rate,
									'cost_notax' => $cost_notax,
									'cost' => $cost,
									'total_cost' => $total_cost,
									'state' => $state,
									'stock_date' => $values['stock_date'] ?? '',
									'id' => $id,
									'rt' => now(),
									'ut' => now(),
								];
								DB::table('product_stock_order_product')->insert($params);
							}

							if ($state == 30) { // 입고 완료인 경우
								if ($cur_state < $state || $type == 'A') {
									$this->stockIn($goods_no, $prd_cd, $opt, $qty, $stock_no, $invoice_no, $cost, $loc);
								} else {
									// product_stock_hst 에서 단가 수정
									DB::table('product_stock_hst')
										->where('prd_cd', $prd_cd)->where('invoice_no', $invoice_no)
										->update([ 'wonga' => $cost ]);
								}
							}

							if ($state == 40) { // 원가확정인 경우
								$this->confirmWonga($stock_no, $prd_cd, $qty, $cost, $invoice_no);
							}
						}
					} else {
						// 추가입고 and 기존등록상품일때
						$unit_cost = str_replace(",","",str_replace("\\","",$row['unit_cost'])); // 단가
						$prd_tariff_rate = round($row['prd_tariff_rate'] ?? 0, 2); // 상품별 관세율
						$cost = str_replace(",","",$row['cost']);
						$total_cost = str_replace(",","",$row['total_cost']);
						$cost_notax = round($cost / 1.1);

						$params = [
							'unit_cost' => $unit_cost,	
							'prd_tariff_rate' => $prd_tariff_rate,	
							'cost_notax' => $cost_notax,	
							'total_cost' => $total_cost,
							'cost' => $cost,
							'ut' => now(),
						];
						DB::table('product_stock_order_product')->where('stock_prd_no', $stock_prd_no)->update($params);

						// product_stock_hst 에서 단가 수정
						$prd_cd = $row['prd_cd'] ?? '';
						DB::table('product_stock_hst')
							->where('prd_cd', $prd_cd)->where('invoice_no', $invoice_no)
							->update([ 'wonga' => $cost ]);
					}
				}
			}
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$message = "상품 입고중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.";
			$code = -1;
			if ($e->getPrevious()) {
				$message = $e->getMessage();
				$code = $e->getCode();
			}
			throw new Exception($message, $code, $e);
		}
	}

	/**
	 * 상품 입고 (재고)
	 */
	public function stockIn($goods_no, $prd_cd, $opt, $qty, $stock_no, $invoice_no, $cost, $loc) {
		/**
		 * '입고완료' 라면 입고처리
		 * 관련테이블 - product_stock, goods_good, goods_history, stock
		 */
		$sql = "
			select goods_no
			from goods
			where goods_no = '${goods_no}'
		";
		$row = DB::selectOne($sql);

		$fail_message = "상품 입고에 실패하였습니다. 상품번호: " . $goods_no;

		if ($row) {
			try {
				/**
				 * 재고 등록(+)
				 */
				DB::beginTransaction();
				$id = Auth::guard('head')->user()->id;
				$name = Auth::guard('head')->user()->name;
				
				$user = [
					'id' => $id,
					'name' => $name
				];
				
				$s = new S_Stock($user);
				$s->SetPrdCd($prd_cd);
				$s->SetLoc($loc);
				$s->Plus([
					"type" => 1,
					"etc" => "창고입고",
					"qty" => $qty,
					"goods_no" => $goods_no,
					"prd_cd" => $prd_cd,
					"goods_opt" => $opt,
					"wonga" => $cost,
					"invoice_no" => $invoice_no,
				]);
				/**
				 * 재고확정 시 상품의 원가 변경
				 * - 원가확정단계 추가 (20221123 최유현) => 아래코드 주석처리
				 */
				// $sql = "
				// 	update goods set wonga = '${cost}'
				// 	where goods_no = '${goods_no}'
				// ";
				// DB::update($sql);

				DB::commit();
			} catch (Exception $e) {
				DB::rollBack();	
				$code = -2;
				throw new Exception($fail_message, $code, $e);
			}
		} else {
			throw new Exception($fail_message);
		}
	}

	/**
	 * 원가 확정
	 */
	public function confirmWonga($stock_no, $prd_cd, $qty, $cost, $invoice_no)
	{
		$stock = DB::table('product_stock')->select('wonga', 'in_qty')->where('prd_cd', '=', $prd_cd)->first();
		
		try {
			DB::beginTransaction();

			if ($stock != null && ($stock->wonga != $cost)) {
				// 1. 재고테이블 평균원가 및 재고총원가 값 업데이트
				$total_old_wonga = ($stock->in_qty - $qty) * $stock->wonga;
				$total_cur_wonga = $qty * $cost;
				$total_wonga = $total_old_wonga + $total_cur_wonga;
				$avg_wonga = round($total_wonga / ($stock->in_qty));
				
				$values = [
					'wonga' => $avg_wonga,
					'qty_wonga' => DB::raw('qty * ' . $avg_wonga),
					'ut' => now(),
				];
				DB::table('product_stock')->where('prd_cd', '=', $prd_cd)->update($values);

				$fin_rt = DB::table('product_stock_order')->where('stock_no', '=', $stock_no)->value('fin_rt');
				if ($fin_rt != null) {
					// 2. 입고완료 ~ 원가확정 기간동안 판매된 주문건(및 hst)의 원가 값 업데이트
					$where = " where ord_date >= '$fin_rt' and ord_date <= now() and prd_cd = '$prd_cd' ";

					$sql = "
						update order_opt set
							wonga = '$avg_wonga'
						$where
					";
					DB::update($sql);

					$orders = DB::select("select ord_opt_no from order_opt $where");
					foreach ($orders as $ord) {
						$sql = "
							update order_opt_wonga set
								wonga = '$avg_wonga'
							where ord_opt_no = '$ord->ord_opt_no'
						";
						DB::update($sql);

						// product_stock_hst 에서 단가 수정
						DB::table('product_stock_hst')
							->where('prd_cd', $prd_cd)->where('ord_opt_no', $ord->ord_opt_no)
							->update([ 'wonga' => $avg_wonga ]);
					}

					// 3. 입고완료 ~ 원가확정 기간동안 매장으로 출고된 hst 로그 기록의 원가 값 업데이트
					$fin_date = date('Ymd', strtotime($fin_rt));
					$sql = "
						update product_stock_hst set
							wonga = '$avg_wonga'
						where prd_cd = '$prd_cd' 
							and ((type = '1' and location_type = 'STORE') or (type = '17' and location_type = 'STORAGE')) 
							and stock_state_date >= '$fin_date' and stock_state_date <= '$fin_date'
					";
					DB::update($sql);
				}

				// 4. product_stock_hst 에서 창고입고 시 단가 수정
				DB::table('product_stock_hst')
					->where('prd_cd', $prd_cd)->where('type', '1')->where('invoice_no', $invoice_no)
					->update([ 'wonga' => $cost ]);
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();	
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * 상품 정보 얻기
	 */
	public function getGood($prd_cd) {

		$sql = "
			select 
				pc.prd_cd
				, pc.goods_no
				, concat(pc.brand, pc.year, pc.season, pc.gender, pc.item, pc.seq, pc.opt) as prd_cd_p
				, pc.color
				, pc.size
				, g.goods_nm
				, g.goods_nm_eng
				, g.style_no
				, pc.goods_opt as opt_kor
				, g.opt_kind_cd
				, op.opt_kind_nm as item
				, g.goods_sh
				, g.wonga
				, g.price
				, g.com_id
				, g.brand as brand_cd
				, b.brand_nm as brand
				, ps.qty as total_qty
				, ps.wqty as sg_qty
			from product_code pc
				left outer join product_stock ps on ps.prd_cd = pc.prd_cd
				left outer join goods g on g.goods_no = pc.goods_no
				left outer join brand b on b.brand = g.brand
				left outer join opt op on op.opt_kind_cd = g.opt_kind_cd and op.opt_id = 'K'
			where pc.prd_cd = :prd_cd
		";
		$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		if ($row == null || $row->goods_no == '0') {
			// $sql = "
			// 	select 
			// 		p.prd_cd, p.prd_nm as goods_nm, p.style_no, p.tag_price as goods_sh
			// 		, p.price, p.wonga, p.type, p.com_id, c.com_nm, p.match_yn, p.use_yn
			// 		, pc.brand as brand_cd, b.brand_nm as brand, ps.qty as total_stock_qty
			// 		, pc.goods_opt as opt_kor, '0' as goods_no
			// 	from product p
			// 		inner join product_code pc on pc.prd_cd = p.prd_cd
			// 		left outer join product_stock ps on ps.prd_cd = p.prd_cd
			// 		left outer join company c on c.com_id = p.com_id
			// 		left outer join brand b on b.br_cd = pc.brand
			// 	where p.prd_cd = :prd_cd
			// ";
			// $row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

			$row->goods_nm = '상품정보 없음';
			$row->goods_no = null;
			$row->total_qty = 0;
			$row->sg_qty = 0;
		}

		return response()->json(['code' => 1, 'good' => $row], 200);
	}

	/**
	 * 옵션정보 얻기
	 */
	public function checkOption(Request $request) {

		$goods_no	= $request->input("goods_no", 0);				// 상품번호
		$prd_cd		= $request->input("prd_cd");					// 상품코드
		$opt		= $this->Rq(Trim($request->input("opt")));		// 옵션

		if ($opt == '') $opt = "NONE";

		$sql = "
			select count(*) as cnt
			from product_stock
			where goods_no = '$goods_no' and prd_cd = '$prd_cd' and goods_opt = '$opt'
		";

		$row = DB::selectOne($sql);

		if ( $row->cnt > 0 ){
			return response()->json(['code' => 1], 200);
		} else {
			return response()->json(['code' => 0], 200);
		}
		
	}

	public function getInvoiceNo($com_id) {
		$prefix_invoice_no = sprintf("%s_%s_A",$com_id,date("ymd"));
		$sql = "
			select ifnull(max(invoice_no),0) as invoice_no from product_stock_order
			where invoice_no like '$prefix_invoice_no%'
		";
		$row = DB::selectOne($sql);
		$max_invoice_no = $row->invoice_no;
		if ($max_invoice_no == "0"){
			$seq = 1;
		} else {
			$seq = str_replace($prefix_invoice_no,"",$max_invoice_no);
			$seq = $seq + 1;
		}
		$invoice_no = sprintf("%s%03d",$prefix_invoice_no,$seq);
		return $invoice_no;
	}

	/**
	 * Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환
	 */
	public function importExcel($request) {
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();
	
				$save_path = "data/store/cs01/";
				$file_name = "${now}_${user_id}.${extension}";
				
				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}
	
				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);
	
				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}

	/*
		Function: Rq
			DB 입력을 위한 quote 처리 ( 김대진 이름 만듬 )

		Parameters:
			str - 변경할 문자열
			flag - stripslashes 여부 ( 기본값 : 1 )

		Returns:
			String
	*/
	public function Rq($str, $flag = "1"){
		if($flag != "1"){
			return str_replace("'","''",$str);
		} else {
			return str_replace("'","''",stripslashes($str));
		}
	}
	
};