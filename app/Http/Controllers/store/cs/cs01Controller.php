<?php

namespace App\Http\Controllers\store\cs;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class cs01Controller extends Controller {

    public function index(Request $request)
	{
        $immutable = CarbonImmutable::now();
        $sdate = $immutable->sub(6, 'month')->format('Y-m-d');
        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems(),
            'order_stock_states' => Slib::getCodes('G_ORDER_STOCK_STATE')
        ];
        return view( Config::get('shop.store.view') . '/cs/cs01', $values);
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

		$where = "where b.stock_date >= '$sdate' and b.stock_date <= '$edate'";
		$where2 = "where 1=1";

		if ($com_id != "") $where .= " and b.com_id = '" . Lib::quote($com_id) . "'";
		if ($state != "") $where .= " and b.state = '" . Lib::quote($state) . "'";
		if ($invoice_no != "") $where .= " and b.invoice_no = '" . Lib::quote($invoice_no) . "'";
		
		if ($com_nm != "") $where2 .= " and c.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if ($user_name != "") $where2 .= " and u.name like '%" . Lib::quote($user_name) . "%' ";
		if ($item != "") $where2 .= " and item_cds like '%" . Lib::quote($item) . "%' ";

		/**
		 * 추후 입고 유저 롤에 대한 처리 필요
		 */
		// if ($role >= 1 && $role < 2) { 
		// 		$where .= " and b.id = '$id' ";
		// }

		$sql = "
			select
				b.stock_no, b.invoice_no, ar.code_val as area_type,
				b.stock_date, cd.code_val as state_nm, c.com_nm, s.item,
				b.currency_unit,b.exchange_rate,b.custom_amt, b.custom_tax,b.custom_tax_rate,
				s.qty, s.total_cost,
				ifnull((select sum(qty) from stock_product_buy_order where stock_no = b.stock_no),0) as buy_order_qty,
				u.name, b.rt, s.item_cds as item_cds
			from stock_order b
				inner join (
					select p.stock_no,group_concat(distinct p.item) as item,
					group_concat(distinct o.opt_kind_cd) as item_cds,
					sum(p.qty) as qty, sum(cost * qty) as total_cost
					from stock_order b inner join stock_product p on b.stock_no = p.stock_no
						left outer join opt o on p.item = o.opt_kind_nm
					$where
					group by p.stock_no
				) s on b.stock_no = s.stock_no
				inner join company c on c.com_id = b.com_id
				left outer join code ar on ar.code_kind_cd = 'g_buy_order_ar_type' and ar.code_id = b.area_type
				left outer join code cd on cd.code_kind_cd = 'g_order_stock_state' and cd.code_id = b.state
				left outer join mgr_user u on b.id = u.id
			$where2
			order by b.stock_date desc, b.rt desc
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

		$cmd = $request->input('cmd');
		$stock_no = $request->input('stock_no');
		$stock_date = date("Ymd");
		$invoice_no = "";
		$cmd = "addcmd";
		$com_id = "";
		$com_nm = "";
		$state = "10";
		$loc = "loc";
		$area_type = "D";
		$custom_tax_rate =  "0.00";
		$custom_amt = 0;
		$custom_tax = 0;
		$exchange_rate = "";
		$currency_unit = "KRW";
		$opts = "";

		if ($stock_no != "") {
			
			$cmd = "editcmd";
			$sql = "
				select
					b.invoice_no, b.stock_date, b.stock_type, b.area_type,
					b.com_id, c.com_nm, b.item, b.currency_unit, b.exchange_rate,
					b.custom_amt,b.custom_tax,b.custom_tax_rate, b.state, b.loc,b.opts, b.id
				from stock_order b
					inner join company c on c.com_id = b.com_id
				where stock_no = '$stock_no'
			";
			$row = DB::selectOne($sql);

			if ($row) {

				$invoice_no = $row->invoice_no;
				$stock_date = $row->stock_date;
				$area_type = $row->area_type;
				$com_id = $row->com_id;
				$com_nm = $row->com_nm;
				$currency_unit = $row->currency_unit;
				$exchange_rate = $row->exchange_rate;
				$custom_amt = $row->custom_amt;
				$custom_tax = $row->custom_tax;
				$custom_tax_rate = $row->custom_tax_rate;
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

		$states = Slib::getCodes('G_ORDER_STOCK_state');
		$collection = $states->map(function ($item) {
			return collect($item)->only(['code_id','code_val'])->all();
		});

		// 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
		$states = $collection->reject(function ($item) {
			$code_id = $item['code_id'];
			return ($code_id == -10);
		})->values();

		if ($state > 0 and $state < 30){
			// 입고 대기나 입고 처리중인 경우 입고 완료를 제외한 모든 입고 상태 표시
			$states = $collection->reject(function ($item) {
				$code_id = $item['code_id'];
				return ($code_id == -10); // 특정 조건을 제외한 모든 아이템을 리턴
			})->values();

		} else if ($cmd == "addcmd" && $state == 10) {
			$states = $collection->filter(function ($item) {
				$code_id = $item['code_id'];
				return ($code_id == 10); // 특정 조건에 걸리는 아이템만 리턴
			})->values();

		} else if ($state < 0) {
			$states = $collection->filter(function ($item) {
				$code_id = $item['code_id'];
				return ($code_id == -10);
			})->values();

		} else if ($state == 30) {
			$states = $collection->filter(function ($item) {
				$code_id = $item['code_id'];
				return ($code_id == 30);
			})->values();
		};

		if ($opts != ""){
			$col_opts = explode("\t", $opts);
		} else {
			$col_opts = array();
		};

        $values = [
			'stock_no' => $stock_no,
			'invoice_no' => $invoice_no,
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
			'cmd' => $cmd,
			'opts' => $opts,
			'opt_cnt' => count($col_opts),
			"col_opts" => $col_opts,
			"locs" => Slib::getCodes('G_STOCK_loc'),
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
				$style_no = $request->input("style_no");
				$response = $this->getGood($style_no);
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
	public function addCmd(Request $request) {

		$id = Auth::guard('head')->user()->id;
		str_replace('-','',$request->input("sdate"));
		// Form
		$invoice_no				= $request->input("invoice_no");			//송장번호
		$stock_date				= str_replace('-','',$request->input("stock_date"));	//입고일자
		$stock_type				= "A";										//입고구분
		$area_type				= $request->input("area_type");				//입고지역
		$com_id					= $request->input("com_id");				//공급처
		$item					= "";										//품목
		$currency_unit			= $request->input("currency_unit");			//화폐단위
		$exchange_rate			= $request->input("exchange_rate");			//환율
		$exchange_rate			= str_replace(",","",$exchange_rate);

		$custom_amt				= $request->input("custom_amt");			//신고금액
		$custom_amt				= str_replace(",","",$custom_amt);
		$custom_tax				= $request->input("custom_tax");			//통관비
		$custom_tax				= str_replace(",","",$custom_tax);
		$custom_tax_rate		= $request->input("custom_tax_rate");		//통관세율

		$state					= $request->input("state");					//입고상태
		$loc					= $request->input("loc");					//위치

		$prd_cd = $request->input("prd_cd"); // 상품코드

		$data					= $request->input("data");

		if ($currency_unit == "KRW") {
			$exchange_rate = 0;
			$custom_tax_rate = 0;
		}

		if ($area_type == "") {
			$area_type = ($currency_unit == "KRW") ? "D" : "O";
		}

		$opts		= "";
		$opt_cnt 	= 0;

		$stock_no = 0;
		try {
            DB::beginTransaction();
			if ($invoice_no == "") {
				$invoice_no = $this->getInvoiceNo($com_id);
			}
			// 등록

			$stock_no = DB::table('stock_order')->insertGetId([
				'invoice_no'	=> $invoice_no, 
				'stock_date'	=> $stock_date,
				'stock_type'	=> $stock_type,
				'area_type'		=> $area_type,
				'com_id'		=> $com_id,
				'item'			=> $item,
				'currency_unit'	=> $currency_unit,
				'exchange_rate'	=> $exchange_rate,
				'custom_amt'	=> $custom_amt,
				'custom_tax'	=> $custom_tax,
				'custom_tax_rate'	=> $custom_tax_rate,
				'state'			=> $state,
				'loc'			=> $loc,
				'opts'			=> $opts,
				'id'			=> $id,
				'rt'			=> now(),
				'ut'			=> now()
			]);

			$this->saveStockProduct(
				"E", $stock_no, $invoice_no, $state, $loc, $stock_date, $com_id,
				$currency_unit, $exchange_rate, $custom_tax_rate, $opt_cnt, $data
			);
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$message = "입고 추가중 에러가 발생하였습니다.";
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
	 * 발주 수정
	 */
	public function editCmd(Request $request) {

		$id = Auth::guard('head')->user()->id;

		// Form
		$stock_no				= $request->input("stock_no");				//입고번호
		$invoice_no				= $request->input("invoice_no");			//송장번호
		$stock_date				= str_replace('-','',$request->input("stock_date")); //입고일자

		$area_type				= $request->input("area_type");				//입고지역
		$com_id					= $request->input("com_id");				//공급처

		$item					= $request->input("item");					//품목
		$currency_unit			= $request->input("currency_unit");			//화폐단위
		$exchange_rate			= $request->input("exchange_rate");			//환율
		$exchange_rate			= str_replace(",", "", $exchange_rate);

		$custom_amt				= $request->input("custom_amt");			//신고금액
		$custom_amt				= str_replace(",","", $custom_amt);
		$custom_tax				= $request->input("custom_tax");			//통관비
		$custom_tax				= str_replace(",","", $custom_tax);
		$custom_tax_rate		= $request->input("custom_tax_rate");		//통관세율

		$state					= $request->input("state");					//입고상태
		$loc					= $request->input("loc");					//위치
		$data					= $request->input("data");

		$prd_cd = $request->input("prd_cd"); // 상품코드

		if ($currency_unit == "KRW") {
			$exchange_rate = 0;
			$custom_tax_rate = 0;
		}

		if ($area_type == "") {
			$area_type = ($currency_unit == "KRW") ? "D" : "O";
		}

		$opts		= "";
		$opt_cnt 	= 0;

		$sql = "
			select state from stock_order
			where stock_no = '$stock_no'
		";

		$row = DB::selectOne($sql);

		try {
			if ($row->state < 30) { // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
				DB::beginTransaction();
				$sql = "
					update stock_order set
						invoice_no = '${invoice_no}',
						stock_date = '${stock_date}',
						area_type = '${area_type}',
						com_id = '${com_id}',
						item = '${item}',
						currency_unit = '${currency_unit}',
						exchange_rate = '${exchange_rate}',
						custom_amt = '${custom_amt}',
						custom_tax = '${custom_tax}',
						custom_tax_rate = '${custom_tax_rate}',
						state = '${state}',
						loc = '${loc}',
						opts = '${opts}',
						id = '${id}',
						ut = now()
					where stock_no = '$stock_no'
				";
				DB::update($sql);
				$this->saveStockProduct(
					"E", $stock_no, $invoice_no, $state, $loc, $stock_date, $com_id,
					$currency_unit, $exchange_rate, $custom_tax_rate, $opt_cnt, $data
				);
				DB::commit();
			}
		} catch (Exception $e) {
			DB::rollback();
			$message = "발주 수정시 에러가 발생하였습니다.";
			$code = 0;
			if ($e = $e->getPrevious()) {
				$message = $e->getMessage();
				$code = $e->getCode();
			}
            return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => 1, 'message' => "발주 수정이 완료되었습니다."], 200);
	}

	/**
	 * 입고 삭제
	 */
	public function delCmd($stock_no) { // 입고번호
		$sql = "
			select state from stock_order
			where stock_no = '$stock_no'
		";
		$row = DB::selectOne($sql);

		if($row->state < 30){
			try {
				DB::beginTransaction();
				$sql = "
					delete from stock_order
					where stock_no = :stock_no
				";
				DB::delete($sql, ['stock_no' => $stock_no]);
				$sql = "
					delete from stock_product
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

		$sql = "
			select * from stock_order where stock_no = '$stock_no' and state = 30
		";

		$row = DB::selectOne($sql);
		
		if ($row) {
			$loc = $row->loc;
		} else {
			$loc = '';
		}

		$id = Auth::guard('head')->user()->id;
		$name = Auth::guard('head')->user()->name;

		$user = [
			'id' => $id,
			'name' => $name
		];

		try {
			DB::beginTransaction();
			/**
			 * 재고 등록(+)
			 */
			$s = new Stock($user);
			$s->SetLoc($loc);

			$sql = "
				select stock_no,invoice_no,goods_no,prd_cd,opt_kor as opt,unit_cost,cost_notax,qty
				from stock_product
				where stock_no = '$stock_no' and state = 30
			";

			$rows = DB::select($sql);

			foreach ($rows as $row) {
				$invoice_no = $row->invoice_no;
				$goods_no = $row->goods_no;
				$prd_cd = $row->prd_cd;
				$opt = $row->opt;
				$cost_notax = $row->cost_notax;
				$qty = $row->qty;

				$stock = array(
					"type" => 9, // 여기
					"etc" => "입고 취소",
					"qty" => $qty,
					"goods_no" => $goods_no,
					"prd_cd" => $prd_cd,
					"goods_opt" => $opt,
					"wonga" => $cost_notax,
					"invoice_no" => $invoice_no,
				);
				
				$s->Minus( $stock );
				$sql = "
					update stock_product set state = '-10'
					where stock_no = '$stock_no' and goods_no = '$goods_no' and prd_cd = '$prd_cd'
							and opt_kor = '$opt' and cost_notax = '$cost_notax'
				";
				DB::update($sql);
			}
			
			DB::commit();
		} catch (Exception $e) {
			dd($e);
			DB::rollBack();
			return response()->json(['code' => -1, 'message' => "입고 취소를 실패하였습니다. 다시 한번 시도하여 주십시오."], 200);
		}

		try {
			DB::beginTransaction();
			$sql = "
				update stock_order set state = '-10'
				where stock_no = '$stock_no'
			";
			DB::update($sql);
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			return response()->json(['code' => -2, 'message' => "입고 취소를 실패하였습니다. 다시 한번 시도하여 주십시오."], 200);
		}

		return response()->json(['code' => 1, 'message' => "입고가 취소되었습니다."], 200);
	}

	/**
	 * 입고 추가
	 */
	public function addStockCmd(Request $request) { // 입고번호, 데이터

		$stock_no = $request->input('stock_no');
		$data = $request->input('data');

		$opts		= "";
		$opt_cnt 	= 0;

		$sql = "
			select
				invoice_no,state,loc,stock_date,com_id,currency_unit,exchange_rate,
				custom_amt,custom_tax,custom_tax_rate
			from stock_order
			where stock_no = '$stock_no'
		";
		$row = DB::selectOne($sql);

		if ($row->state == 30) { // 입고완료면 그대로 유지

			$invoice_no = $row->invoice_no;
			$state = $row->state;
			$stock_date = $row->stock_date;
			$com_id = $row->com_id;
			$currency_unit = $row->currency_unit;
			$exchange_rate = $row->exchange_rate;
			$custom_tax_rate = $row->custom_tax_rate;
			$loc = $row->loc;

			if ($currency_unit == "KRW") {
				$exchange_rate = 0;
				$custom_tax_rate = 0;
			}

			try {
				DB::beginTransaction();
				$sql = "
					update stock_order set
						opts = '${opts}',
						ut = now()
					where stock_no = '${stock_no}'
				";
				DB::update($sql);

				$this->saveStockProduct(
					"A", $stock_no, $invoice_no, $state, $loc, $stock_date, $com_id, 
					$currency_unit, $exchange_rate, $custom_tax_rate, $opt_cnt, $data
				);

				DB::commit();
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
	}

	public function listProduct($stock_no) {
		$sql = "
			select
				if(state = 30 or state = -10,'0','2') as chk,
				stock_prd_no as stock_prd_no,
				s.item,s.brand,s.style_no,s.goods_no,s.prd_cd,g.goods_nm, g.brand_nm,
				ifnull(( select goods_opt from product_stock
						where goods_no = s.goods_no and prd_cd = s.prd_cd and goods_opt = s.opt_kor),concat('ERR:',ifnull(s.opt_kor, ''))) as opt_kor,
				s.qty as qty,
				s.unit_cost as unit_cost,
				(s.unit_cost * qty) as unit_total_cost,
				s.cost as cost,
				(s.cost * qty) as total_cost,
				(s.cost_notax * qty) as total_cost_novat,
				s.stock_date as stock_date
			from stock_product s inner join goods g
					on s.goods_no = g.goods_no
			where stock_no = '$stock_no'
			order by stock_prd_no asc
		";
		$rows = DB::select($sql);
		return response()->json(['rows' => $rows], 200);
	}

	/**
	 * 상품 입고
	 */
	public function saveStockProduct($type, $stock_no, $invoice_no, $state, $loc, $stock_date, $com_id, $currency_unit, $exchange_rate, $custom_tax_rate, $opt_cnt, $data) {
		try {
			DB::beginTransaction();
			if ($type != "A") {
				$sql = "
					delete from stock_product
					where stock_no = :stock_no
				";
				DB::delete($sql, ['stock_no' => $stock_no]);
			}
			$products = $data;
			if (count($products) > 0) {
				$id = Auth::guard('head')->user()->id;
				for ($i=0; $i<count($products); $i++) {

					$row = $products[$i];
					$stock_prd_no = $row['count'];

					if ($type == "A" && $stock_prd_no > 0) {
					} else {
						$item = $row['item'];
						$brand = $row['brand'];
						$style_no = $row['style_no'];
						$goods_no = $row['goods_no'];
						$prd_cd = $row['prd_cd'];

						if ($goods_no > 0) {
							$unit_cost = str_replace(",","",str_replace("\\","",$row['unit_cost']));
							$cost = str_replace(",","",$row['cost']);
							if ($currency_unit == "KRW") {
								$cost = $unit_cost;
								$cost_notax = round($cost / 1.1);
							} else {
								$cost = round($unit_cost * $exchange_rate * ( 1 + $custom_tax_rate / 100));
								$cost_notax = round($cost / 1.1);
							}
							
							$opt = array_key_exists('opt_kor', $row) ? trim($row['opt_kor']) : "NONE";
							if ($opt == "") $opt = "NONE";

							$qty = $row['qty'];

							if ($opt != "" && $qty > 0) {

								$sql = "
									insert into stock_product
									( stock_no,invoice_no,com_id,item,brand,style_no, prd_cd, goods_no,goods_sub,opt_kor,
										qty,unit_cost,cost_notax,cost,state,stock_date,id,rt,ut ) values
									( '${stock_no}', '${invoice_no}',
										'${com_id}', '${item}', '${brand}','${style_no}', '${prd_cd}', '${goods_no}','0','${opt}',
										'${qty}','${unit_cost}','${cost_notax}','${cost}','${state}','${stock_date}','${id}',now(),now())
								";

								DB::insert($sql);
							}

							if ($state == 30) { // 입고 완료인 경우
								$this->stockIn($goods_no, $prd_cd, $opt, $qty, $stock_no, $invoice_no, $cost, $loc);
							}
						}
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
	 * 상품 입고
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
				
				$s = new Stock($user);
				$s->SetPrdCd($prd_cd);
				$s->SetLoc($loc);
				$s->Plus( array(
					"type" => 1,
					"etc" => "",
					"qty" => $qty,
					"goods_no" => $goods_no,
					"prd_cd" => $prd_cd,
					"goods_opt" => $opt,
					"wonga" => $cost,
					"invoice_no" => $invoice_no,
				));
				/**
				 * 재고확정 시 상품의 원가 변경
				 */
				$sql = "
					update goods set wonga = '${cost}'
					where goods_no = '${goods_no}'
				";
				DB::update($sql);
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
	 * 상품 정보 얻기
	 */
	public function getGood($style_no) {

		$cnt = 0;
		$where = "";

		$sql = "
			select opt_kind_cd,brand,goods_no,goods_sub,goods_nm,com_type from goods
			where style_no = '${style_no}' and sale_stat_cl > 0 ${where} limit 0,2
		";
		$rows = DB::select($sql);

		$goods = (object)[];
		foreach ($rows as $row) {
			$goods->item = $row->opt_kind_cd;
			$goods->brand = $row->brand;
			$goods->goods_no = $row->goods_no;
			$goods->goods_sub = $row->goods_sub;
			$goods->goods_nm = $row->goods_nm;
			$goods->com_type = $row->com_type;
			$cnt++;
		}
	
		if ($cnt == 0) {
			$error_msg = "상품없음";
			return response()->json(['code' => 0, 'message' => $error_msg], 200);
		} else if ($cnt > 1) {
			$error_msg = "상품중복";
			return response()->json(['code' => -1, 'message' => $error_msg], 200);
		} else if ($cnt == 1) {
			if ($goods->com_type == 1){
				return response()->json(['code' => 1, 'good' => $goods], 200);
			} else {
				$error_msg = "입점상품";
				return response()->json(['code' => -1, 'message' => $error_msg], 200);
			}
		}
		
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
			select ifnull(max(invoice_no),0) as invoice_no from stock_order
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