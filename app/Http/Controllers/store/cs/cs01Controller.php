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
use Carbon\Carbon;

const KRW = 'KRW';
const SUPER_ADMIN_ID = 'pklpkl'; // 슈퍼권한 (본사_정연수)

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
		// $item = $request->input("item");
		$com_id = $request->input("com_cd");
		$com_nm = $request->input("com_nm");
		$state = $request->input("order_stock_state");
		$user_name = $request->input("user_name");
		$user_name_type = $request->input("user_name_type", "req_nm");
		$goods_nm = $request->input("goods_nm", "");
		$prd_cd = $request->input("prd_cd", "");
		$prd_cd_range_text = $request->input("prd_cd_range", "");

		$where = "where b.stock_date >= '$sdate' and b.stock_date <= '$edate'";
		$where2 = "";
		$goods_where = "";
		$having = "";

		if ($com_id != "") $where .= " and b.com_id = '" . Lib::quote($com_id) . "'";
		if ($state != "") $where .= " and b.state = '" . Lib::quote($state) . "'";
		if ($invoice_no != "") $where .= " and b.invoice_no like '%" . Lib::quote($invoice_no) . "%'";
		if ($com_nm != "") $where2 .= " and c.com_nm like '%" . Lib::quote($com_nm) . "%' ";
		if ($user_name != "") $having .= " and $user_name_type like '%" . Lib::quote($user_name) . "%' ";
		if ($goods_nm != "") $goods_where .= " and g.goods_nm like '%" . Lib::quote($goods_nm) . "%' ";
		if ($prd_cd != "") {
			$prd_cd = explode(',', $prd_cd);
			$goods_where .= " and (1!=1";
			foreach($prd_cd as $cd) {
				$goods_where .= " or p.prd_cd like '" . Lib::quote($cd) . "%' ";
			}
			$goods_where .= ")";
		}

		// 상품옵션 범위검색
		$range_opts = ['brand', 'year', 'season', 'gender', 'item', 'opt'];
		parse_str($prd_cd_range_text, $prd_cd_range);
		foreach ($range_opts as $opt) {
			$rows = $prd_cd_range[$opt] ?? [];
			if (count($rows) > 0) {
				$opt_join = join(',', array_map(function($r) {return "'$r'";}, $rows));
				$goods_where .= " and pc.$opt in ($opt_join) ";
			}
		}

		// ordreby
        $ord_field  = $request->input("ord_field", "b.req_rt");
        $ord        = $request->input("ord", "desc");
        $orderby    = sprintf("order by %s %s", $ord_field, $ord);
        
        // pagination
        $page       = $request->input("page", 1);
        $page_size  = $request->input("limit", 100);
        if ($page < 1 or $page == "") $page = 1;
        $startno    = ($page - 1) * $page_size;
        $limit      = " limit $startno, $page_size ";

		$sql = "
			select
				b.stock_no, b.invoice_no, ar.code_val as area_type,
				b.bl_no,
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
					from product_stock_order b 
					    inner join (
							select p.stock_no, p.item, p.qty, p.exp_qty, p.total_cost
							from product_stock_order_product p
								inner join product_code pc on pc.prd_cd = p.prd_cd
								left outer join goods g on g.goods_no = p.goods_no
							where 1=1 $goods_where
					    ) p on b.stock_no = p.stock_no
						left outer join opt o on p.item = o.opt_kind_nm
					$where
					group by p.stock_no
				) s on b.stock_no = s.stock_no
				inner join company c on c.com_id = b.com_id
				left outer join code ar on ar.code_kind_cd = 'g_buy_order_ar_type' and ar.code_id = b.area_type
				left outer join code cd on cd.code_kind_cd = 'STOCK_ORDER_STATE' and cd.code_id = b.state
			where 1=1 $where2
			having 1=1 $having
			$orderby
			$limit
		";
		
		$rows = DB::select($sql);

		// pagination
		$total = 0;
		$page_cnt = 0;

		if ($page == 1) {
			$sql = "
				select count(*) as total,
					   req_nm,
					   prc_nm,
					   fin_nm,
					   cfm_nm,
					   rej_nm
				from (
					select b.stock_no,
						   (select name from mgr_user where id = b.req_id) as req_nm,
						   (select name from mgr_user where id = b.prc_id) as prc_nm,
						   (select name from mgr_user where id = b.fin_id) as fin_nm,
						   (select name from mgr_user where id = b.cfm_id) as cfm_nm,
						   (select name from mgr_user where id = b.rej_id) as rej_nm
					from product_stock_order b
					inner join (
						select p.stock_no,
							   group_concat(distinct p.item) as item,
							   group_concat(distinct o.opt_kind_cd) as item_cds,
							   sum(p.qty) as qty,
							   sum(p.exp_qty) as exp_qty,
							   sum(total_cost) as total_cost
						from product_stock_order b 
						inner join (
							select p.stock_no, p.item, p.qty, p.exp_qty, p.total_cost
							from product_stock_order_product p
							inner join product_code pc on pc.prd_cd = p.prd_cd
							left outer join goods g on g.goods_no = p.goods_no
							where 1=1 $goods_where
						) p on b.stock_no = p.stock_no
						left outer join opt o on p.item = o.opt_kind_nm
						$where
						group by p.stock_no
					) s on b.stock_no = s.stock_no
					inner join company c on c.com_id = b.com_id
					left outer join code ar on ar.code_kind_cd = 'g_buy_order_ar_type' and ar.code_id = b.area_type
					left outer join code cd on cd.code_kind_cd = 'STOCK_ORDER_STATE' and cd.code_id = b.state
					where 1=1 $where2
				) as a
				where 1=1 $having

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
				"page_total" => count($rows)
			],
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

		$admin_id = Auth::guard('head')->user()->id;

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

		$sql = "
			select
				code_id
				, code_val
			from code
			where code_kind_cd = 'G_STOCK_LOC'
		";

		$locs = DB::select($sql);

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
			"locs" => $locs,
			"loc" => $loc,
			"super_admin" => ($admin_id === SUPER_ADMIN_ID ? 'true' : 'false')
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
			case 'search-stock-log':
				$prd_cds = $request->input('prd_cds', '');
				$response = $this->searchProductStockLog($prd_cds);
				break;
			case 'getgood':
				$prd_cd = $request->input("prd_cd");
				$response = $this->getGood($prd_cd);
				break;
			case 'getinvoiceno':
				$com_id = $request->input('com_id');
				$stock_date = $request->input('stock_date');
				$stock_date = Carbon::parse($stock_date);
				$stock_date = $stock_date->format('ymd');
				$invoice_no = $this->getInvoiceNo($com_id, $stock_date);
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
		$code = 0;
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

		$tariff_rate 			= 0;
		$freight_rate 			= 0;
		$custom_tax_rate 		= 0;
		
		$area_type 				= ($currency_unit == KRW) ? "D" : "O"; 		//입고지역
		$data					= $request->input("data");

		if ($currency_unit == KRW) {
			$exchange_rate = 0;
			$custom_amt = 0;
			$tariff_amt = 0;
			$freight_amt = 0;
		}

		$opts = "";
		$opt_cnt = 0;

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
			$response = $this->saveStockOrderProduct("E", $params, $data, $id);
			if ($response['code'] < 1) {
				throw new Exception($response['msg']);
			}

			$code = 1;
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$message = $e->getMessage();
            return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => $code, 'message' => "입고 추가가 완료되었습니다."], 201);
	}

	/**
	 * 입고 수정
	 */
	public function editCmd(Request $request) 
	{
		$code = 0;
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

		$tariff_rate 			= 0;
		$freight_rate 			= 0;
		$custom_tax_rate 		= 0;
		
		$area_type				= ($currency_unit == KRW) ? "D" : "O"; 		//입고지역
		$data					= $request->input("data");

		if ($currency_unit == KRW) {
			$exchange_rate = 0;
			$custom_amt = 0;
			$tariff_amt = 0;
			$freight_amt = 0;
		}

		$opts = "";
		$opt_cnt = 0;

		$cur_state = DB::table('product_stock_order')->where('stock_no', $stock_no)->value('state');

		try {
			if ($cur_state < 40 || $id == SUPER_ADMIN_ID) { // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30, 원가확정: 40
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
				$response = $this->saveStockOrderProduct("E", $params, $data, $id, $cur_state);
				if ($response['code'] < 1) {
					throw new Exception($response['msg']);
				}

				$code = 1;
				DB::commit();
			}
		} catch (Exception $e) {
			DB::rollback();
			$message = $e->getMessage();
			return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => $code, 'message' => "입고 수정이 완료되었습니다."], 200);
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
					where stock_no = '$stock_no' and prd_cd = '$prd_cd' and cost = '$cost'
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
			if ($msg == '') $msg = $e->getMessage();
		}
		return response()->json(['code' => $code, 'message' => $msg], 200);
	}

	/**
	 * 추가입고
	 */
	public function addStockCmd(Request $request) // 입고번호, 데이터
	{
		$code = 0;
		$id = Auth::guard('head')->user()->id;

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

		$tariff_rate 			= 0;
		$freight_rate 			= 0;
		$custom_tax_rate 		= 0;

		$area_type				= ($currency_unit == KRW) ? "D" : "O"; 		//입고지역
		$data 					= $request->input('data');

		if ($currency_unit == KRW) {
			$exchange_rate = 0;
			$custom_amt = 0;
			$tariff_amt = 0;
			$freight_amt = 0;
		}

		$opts = "";
		$opt_cnt = 0;

		$cur_state = DB::table('product_stock_order')->where('stock_no', $stock_no)->value('state');

		try {
			if ($cur_state == 30 || $id === SUPER_ADMIN_ID) { // 입고완료 시 or 슈퍼권한일 때만 추가입고 가능
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
				$response = $this->saveStockOrderProduct("A", $values, $data, $id);
				if ($response['code'] < 1) {
					throw new Exception($response['msg']);
				}

				$code = 1;
				DB::commit();
			}
		} catch(Exception $e) {
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['code' => $code, 'message' => $message], 200);
		}
		return response()->json(['code' => $code, 'message' => "입고 추가되었습니다."], 200);
	}

	/** 입고상세 기존상품조회 */
	public function listProduct($stock_no) {
		/* $row->is_last: 가장 최근에 입고된 상품 여부 확인 (order by stock_no desc) */

		$sql = "
			select
				if(state = 30 or state = -10,'0','2') as chk
				, stock_prd_no as stock_prd_no
				, s.item
				, s.brand
				, s.style_no
				, s.goods_no
				, s.prd_cd
				, if(s.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
				, if(s.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng
				, if(s.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
				, if(s.goods_no = 0, p.price, g.price) as price
				, b.brand_nm
				, pc.prd_cd_p
				, pc.color
				, c.code_val as color_nm
			    , c.code_val2 as color_cd
				, pc.size
				, ifnull(s.exp_qty, 0) as exp_qty
				, ifnull(s.qty, 0) as qty
				, ifnull(s.prd_tariff_rate, 0) as prd_tariff_rate
				, ps.in_qty
				, ps.qty as total_qty
				, ps.wqty as sg_qty
				, s.unit_cost as unit_cost
				, (s.unit_cost * s.exp_qty) as unit_total_cost
				, s.cost as cost
				, s.total_cost as total_cost
				, s.total_cost * 1.1 as total_cost_novat
				, date_format(s.stock_date, '%Y-%m-%d') as stock_date
			    , date_format((select stock_date from product_stock_order_product where prd_cd = s.prd_cd order by stock_date desc limit 1), '%Y-%m-%d') as recent_stock_date
			    , '' as stock_cnt
				-- , (
			    --  	select (count(ss.stock_prd_no) + 1) as stock_cnt
			    --  	from product_stock_order_product ss
			    --  		inner join product_code pp on pp.prd_cd = ss.prd_cd
			    --  	where pp.prd_cd_p = pc.prd_cd_p and ss.rt < s.rt
			  	-- ) as stock_cnt
			    -- , (
				-- 	select
				-- 		(count(gr_ss.stock_no) + 1) as stock_cnt
				-- 	from (
				-- 		select 
				--  			ss.stock_no
				-- 		from product_stock_order_product ss
				-- 		inner join product_code pp on pp.prd_cd = ss.prd_cd
				-- 		where 
				-- 		pp.prd_cd_p = pc.prd_cd_p and ss.rt < s.rt
				-- 		group by ss.stock_no
				-- 	) gr_ss
			    -- ) as stock_cnt
				, ifnull((
					select stock_prd_no 
					from product_stock_order_product 
					where state > 30 and prd_cd = s.prd_cd 
					order by stock_no desc limit 1
				  ), 0) = s.stock_prd_no as is_last
				, s.comment
				, s.rt
			from product_stock_order_product s
				inner join product_code pc on pc.prd_cd = s.prd_cd
				inner join product p on p.prd_cd = s.prd_cd
				inner join product_stock ps on s.prd_cd = ps.prd_cd
				left outer join goods g on s.goods_no = g.goods_no
				left outer join brand b on b.br_cd = s.brand
				left outer join code c on c.code_id = pc.color and c.code_kind_cd = 'PRD_CD_COLOR'
			where stock_no = '$stock_no'
			order by stock_prd_no asc
		";
		$rows = DB::select($sql);

		foreach ($rows as $row) {
			$stock_prd_cd_p	= $row->prd_cd_p;
			$stock_rt		= $row->rt;
			
			$sql_stock_cnt	= "
				select
					(count(gr_ss.stock_no)) as stock_cnt
				from (
					select 
						ss.stock_no
					from product_stock_order_product ss
					inner join product_code pp on pp.prd_cd = ss.prd_cd
					where 
						pp.prd_cd_p = :prd_cd_p
						and ss.rt <= :rt
						group by ss.stock_no
				) gr_ss
			";
			$stock_cnt	= DB::selectOne($sql_stock_cnt,['prd_cd_p' => $stock_prd_cd_p, 'rt' => $stock_rt])->stock_cnt;
			
			$row->stock_cnt	= $stock_cnt;
		}

		return response()->json(['rows' => $rows], 200);
	}
	
	/** 입고추가 시, 선택상품의 최근입고일자/입고순번 등 정보조회 */
	public function searchProductStockLog($prd_cds)
	{
		$prd_cd_sql = join(',', array_map(function($s) { return "'$s'"; }, $prd_cds));
		$sql = "
			select
			    s.prd_cd
			    , date_format((select stock_date from product_stock_order_product where prd_cd = s.prd_cd order by stock_date desc limit 1), '%Y-%m-%d') as recent_stock_date
				, (
					select (count(ss.stock_prd_no) + 1) as stock_cnt
					from product_stock_order_product ss
					   inner join product_code pp on pp.prd_cd = ss.prd_cd
					where pp.prd_cd_p = pc.prd_cd_p and ss.rt <= max(s.rt)
			  	) as stock_cnt
			from product_stock_order_product s
				inner join product_code pc on pc.prd_cd = s.prd_cd
			where s.prd_cd in ($prd_cd_sql)
			group by s.prd_cd
			order by s.stock_prd_no desc
		";
		$rows = DB::select($sql);
		return response()->json([ 'data' => $rows ], 200);
	}

	/**
	 * 상품 입고
	 * $type: 추가입고(A) / 기본(E)
	 * $values: 입고정보
	 * $products: 상품정보 []
	 * $id: 관리자 아이디
	 * $cur_state: 현재상태
	 * 
	 * description
	 * - 추가입고는 현재상태가 '입고완료(30)' 일 때만 가능합니다.
	 * - 추가입고 여부는 입고번호 단위로 판단합니다. (상품단위 X)
	 * - 입고상품 삭제는 현재상태가 '입고처리중(20)' 이하일 때만 가능합니다.
	 * - 슈퍼권한 관리자는 입고완료 처리 이후에 수량(재고) 변경 가능합니다.
	 */
	public function saveStockOrderProduct($type, $values, $products, $id, $cur_state = 0)
	{
		try {
			$stock_no = $values['stock_no'];
			$invoice_no = $values['invoice_no'] ?? '';
			$state = $values['state'] ?? 10; // 변경할 입고상태
			$loc = $values['loc'] ?? '';

			$stk_ord_products = [];
			if ($state == 40 && $cur_state == $state && $id == SUPER_ADMIN_ID) {
				// 슈퍼권한 관리자가 재원가확정할 때, 기존 상품데이터 백업
				// * 재원가확정 기능을 제거할 때 아래 코드를 주석처리해 주세요.
				$stk_ord_products = DB::table('product_stock_order_product')->select('prd_cd', 'exp_qty', 'qty', 'cost', 'prd_tariff_rate')->where('stock_no', $stock_no)->get()->toArray();
			}

			$ori_products = [];
			// 추가입고가 아닐 때 ($type === 'E')
			if ($type != "A") {
				// 기존 상품데이터 백업
				$ori_products = DB::table('product_stock_order_product')->where('stock_no', $stock_no)->get()->toArray();
				// 기존 상품데이터 삭제
				DB::table('product_stock_order_product')->where('stock_no', $stock_no)->delete();
			}
			
			if (count($products) > 0) {
				$id = Auth::guard('head')->user()->id;

				for ($i = 0; $i < count($products); $i++) {
					$row = $products[$i];
					$stock_prd_no = $row['stock_prd_no'] ?? 0;

					if ($type != "A" || $stock_prd_no <= 0) {
						// 추가입고가 아닌 경우 (저장버튼으로 입고등록/입고수정 시)
						// 또는 추가입고의 상품데이터 중 새로 추가된 상품인 경우 (추가입고 시, 입고상품번호가 없는 새로 추가된 상품)

						$item = $row['item'] ?? '';
						$brand = $row['brand'] ?? '';
						$prd_cd = $row['prd_cd'] ?? '';
						$style_no = $row['style_no'] ?? '';
						$goods_no = $row['goods_no'] ?? 0;

						$exp_qty = $row['exp_qty'] ?? 0;
						$qty = $row['qty'] ?? 0;
						$unit_cost = str_replace(",","",str_replace("\\","",$row['unit_cost'])) ?? 0; // 단가
						$prd_tariff_rate = round($row['prd_tariff_rate'] ?? 0, 2); // 상품별 관세율
						$cost = str_replace(",","",$row['cost']);
						$total_cost = str_replace(",","",$row['total_cost']);
						$cost_notax = round($cost / 1.1);

						$opt = array_key_exists('opt_kor', $row) ? trim($row['opt_kor']) : "NONE";
						if ($opt == "") $opt = "NONE";

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
							'comment' => $row['comment'] ?? '',
							'id' => $id,
							'rt' => now(),
							'ut' => now(),
						];
						// 상품데이터 새로 등록
						DB::table('product_stock_order_product')->insert($params);

						if (($state == 30 && ($cur_state < $state || $type == 'A')) || ($state == 40 && $type === 'A' && $id === SUPER_ADMIN_ID)) {
							// 입고완료 이전상태 > 입고완료 상태로 변경 하는 경우
							// 또는 입고완료 상태에서 추가입고하는 경우 (새로 추가된 상품만 해당)
							// 또는 슈퍼권한 관리자가 원가확정 상태에서 추가입고하는 경우 (새로 추가된 상품만 해당)

							// * 슈퍼권한 관리자의 원가확정 상태에서의 추가입고 처리 기능을 제거할 경우, 아래 if문 코드를 주석해제해 주세요.
							if (!($state == 40 && $type === 'A' && $id === SUPER_ADMIN_ID)) {
								$this->stockIn($goods_no, $prd_cd, $opt, $qty, $stock_no, $invoice_no, $cost, $loc);
							}

							// * 슈퍼권한 관리자의 원가확정 상태에서의 추가입고 처리 기능을 제거할 경우, 아래 if문 코드를 모두 주석처리해 주세요.
							if ($state == 40 && $type === 'A' && $id === SUPER_ADMIN_ID) {
								// 슈퍼권한 관리자가 원가확정 상태에서 추가입고 시 (새로 추가된 상품만 해당)
								//$this->confirmWonga($stock_no, $prd_cd, $goods_no, $exp_qty, $cost, $invoice_no);
							}
						} else if ($state >= 30 && $state <= 40) {
							// 입고완료 상태에서 입고정보 및 상품정보만 변경하는 경우
							// 또는 입고완료 상태에서 원가확정 상태로 변경하는 경우
							// 또는 원가확정 상태에서 재원가확정 처리하는 경우

							$ori_product_stock = [];
							if ($state == 40 && $cur_state == $state && $id == SUPER_ADMIN_ID) {
								// 슈퍼권한 관리자가 재원가확정 처리할 때, 기존 상품데이터 백업
								// * 재원가확정 기능을 제거할 때 아래 코드를 주석처리해 주세요.
								$ori_product_stock = DB::table('product_stock')->select('wonga', 'in_qty')->where('prd_cd', $prd_cd)->first();
							}

							// 단가 수정 처리 (입고완료 처리 시 모두 가능, 원가확정 처리 시 슈퍼권한 관리자만 가능)
							if ($state == 30 || $id == SUPER_ADMIN_ID) {
								DB::table('product_stock_hst')
									->where('prd_cd', $prd_cd)->where('type', '1')->where('invoice_no', $invoice_no)
									->update([ 'wonga' => $cost ]);
							}

							// 수량(재고) 수정 (입고완료 이후 슈퍼권한 관리자만 가능)
							if ($id == SUPER_ADMIN_ID) {
								$plus_qty = array_reduce($ori_products, function($a, $c) use ($prd_cd, $qty) { 
									if ($c->prd_cd == $prd_cd && $c->qty != $qty) return array_merge($a, [$c]); 
									else return $a;
								}, []);
								if ($plus_qty != null && count($plus_qty) > 0) {
									$plus_qty = $plus_qty[0]->qty;
									$plus_qty = $qty - $plus_qty;
									$this->stockIn($goods_no, $prd_cd, $opt, $plus_qty, $stock_no, $invoice_no, $cost, $loc);
								}
							}

							if ($state == 40 && $cur_state < $state) {
								// 입고완료 상태에서 원가확정 상태로 변경하는 경우
								//$this->confirmWonga($stock_no, $prd_cd, $goods_no, $exp_qty, $cost, $invoice_no);
							} else if ($state == 40 && $id == SUPER_ADMIN_ID) {
								// 원가확정 상태에서 재원가확정 처리하는 경우
								// * 재원가확정 기능을 제거할 때 아래 코드를 주석처리해 주세요. (updateConfirmedWonga 실행부분까지 주석처리)
								//$stk_ord_product = array_reduce($stk_ord_products, function($a, $c) use ($prd_cd) { 
								//	if ($c->prd_cd === $prd_cd) return array_merge($a, [$c]); 
								//	else return $a;
								//}, []);
								//if (count($stk_ord_product) > 0) $stk_ord_product = $stk_ord_product[0];
								//$this->updateConfirmedWonga($stock_no, $prd_cd, $goods_no, $exp_qty, $cost, $prd_tariff_rate, $invoice_no, $ori_product_stock, $stk_ord_product);
							}
						}
					} else {
						// 입고완료 상태에서 추가입고 진행 시, 상품데이터 중 기존상품번호가 있는 경우
						// (추가된 상품이 아닌, 이전에 등록된 기존상품만 해당)
						// * 해당 상품은 delete 처리되지 않은 상태입니다.

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
							'comment' => $row['comment'] ?? '',
							'ut' => now(),
						];
						DB::table('product_stock_order_product')->where('stock_prd_no', $stock_prd_no)->update($params);

						// 단가 수정
						$prd_cd = $row['prd_cd'] ?? '';
						DB::table('product_stock_hst')
							->where('prd_cd', $prd_cd)->where('invoice_no', $invoice_no)
							->update([ 'wonga' => $cost ]);
					}
				}

				//원가확정시 평균원가 세팅
				if ($state >= 30 && $state <= 40) {
					if ($state == 40 && $cur_state < $state) {
						$this->confirmWongaMst($invoice_no);
					}
				}
				
			}
			return ['code' => 1, 'msg' => '입고상품이 정상적으로 추가되었습니다.'];
		} catch (Exception $e) {
			return ['code' => 0, 'msg' => $e->getMessage()];
		}
	}

	/**
	 * 상품 입고 (재고)
	 */
	public function stockIn($goods_no, $prd_cd, $opt, $qty, $stock_no, $invoice_no, $cost, $loc) {
		/**
		 * '입고완료' 라면 입고처리
		 * 관련테이블 - product_stock, product_stock_storage, product_stock_hst
		 */

		/** 재고 등록(+) */
		$id = Auth::guard('head')->user()->id;
		$name = Auth::guard('head')->user()->name;
		$user = ['id' => $id, 'name' => $name];
		
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
	}

	// 품번별 원가확정
	private function confirmWongaMst($invoice_no)
	{
		try {
	
			$sql = "
				select 
					distinct pc.prd_cd_p
                    , date_format(date_sub(psop.stock_date, interval 1 month),'%Y-%m') as chk_date
					-- , date_format(date_sub(psop.stock_date, interval 1 month),'%Y%m') as chk_date2
				from product_stock_order_product psop 
				inner join product_code pc on psop.prd_cd = pc.prd_cd
				where 
					psop.invoice_no = :invoice_no 
					and psop.state = '40'
			";
			$rows	= DB::select($sql, ['invoice_no' => $invoice_no]);
	
			foreach ($rows as $row){
	
				$prd_cd_p	= $row->prd_cd_p;
				$chk_sdate	= $row->chk_date . "-01 00:00:00";
				$chk_edate	= $row->chk_date . "-31 23:59:59";
				//$chk_sdate2	= $row->chk_date2 . "01";
				//$chk_edate2	= $row->chk_date2 . "31";
				$tot_qty	= 0;
				$tot_cur_qty	= 0;
				$total_old_wonga	= 0;
				$total_cur_wonga	= 0;
	
				$sql_stock	= "
					select
						ps.prd_cd, ps.wonga, ps.in_qty
					from product_stock ps
					inner join product_code pc on ps.prd_cd = pc.prd_cd
					where
						pc.prd_cd_p	= :prd_cd_p
				";
				$rows_stock	= DB::select($sql_stock, ['prd_cd_p' => $prd_cd_p]);
	
				foreach ($rows_stock as $row_stock){
	
					$in_qty	= $row_stock->in_qty;
					$wonga	= $row_stock->wonga;
					$cur_wonga	= 0;
					$cur_qty	= 0;
					$tot_qty	+= $in_qty;
	
					$sql_order	= " select qty, cost from product_stock_order_product where state = '40' and invoice_no = :invoice_no and prd_cd = :prd_cd limit 1 ";
					$row_order	= DB::selectOne($sql_order, ['invoice_no' => $invoice_no, 'prd_cd' => $row_stock->prd_cd]);
	
					if ($row_order != null){
						$cur_qty	= $row_order->qty;
						$cur_wonga	= $row_order->cost;
						$tot_cur_qty	+= $cur_qty;
					}
	
					$total_old_wonga	+= ($in_qty - $cur_qty) * $wonga;
					$total_cur_wonga	+= $cur_qty * $cur_wonga;
				}
	
				$total_wonga	= $total_old_wonga + $total_cur_wonga;
				$avg_wonga		= round($total_wonga / ($tot_qty));
	
				//로그 데이터 등록
				DB::table('product_stock_order_wonga')->insert([
					'invoice_no'	=> $invoice_no,
					'prd_cd_p'		=> $prd_cd_p,
					'qty'			=> $tot_qty,
					'wonga'			=> $total_wonga,
					'in_qty'		=> $tot_cur_qty,
					'in_wonga'		=> $total_cur_wonga,
					'avg_wonga'		=> $avg_wonga,
					'rt'     		=> now(),
					'ut'     		=> now()
				]);
	
				// 1. product_stock update
				$sql_ps	= " update product_stock set wonga = '$avg_wonga', qty_wonga = qty * $avg_wonga, ut = now() where prd_cd like '$prd_cd_p%' ";
				DB::update($sql_ps);
	
				// 2. goods update
				$sql_goods = "
					update goods g
					inner join product_code pc on pc.goods_no = g.goods_no and pc.goods_no <> '0'
						set g.wonga	= :avg_wonga
					where
						pc.prd_cd_p = :prd_cd_p
				";
				DB::update($sql_goods,['avg_wonga' => $avg_wonga, 'prd_cd_p' => $prd_cd_p]);
	
				// 3. product update
				$sql_product	= " update product set wonga = '$avg_wonga' where prd_cd like '$prd_cd_p%' ";
				DB::update($sql_product);
	
				// 4. 모든 판매된 주문건(및 hst)의 원가 값 업데이트 ( 정산기간전 자료 )
				$sql_o = "
						update order_opt set
							wonga = '$avg_wonga'
						where
							ord_date >= '$chk_sdate' and ord_date <= '$chk_edate'
							and prd_cd like '$prd_cd_p%'
					";
				DB::update($sql_o);
	
				$orders = DB::select("select ord_opt_no from order_opt where ord_date >= '$chk_sdate' and ord_date <= '$chk_edate' and prd_cd like '$prd_cd_p%'");
				foreach ($orders as $ord) {
					$sql_ow = "
							update order_opt_wonga set
								wonga = '$avg_wonga'
							where ord_opt_no = '$ord->ord_opt_no'
						";
					DB::update($sql_ow);
	
					// product_stock_hst 에서 단가 수정
					$sql_hst	= " update product_stock_hst set wonga = '$avg_wonga' where ord_opt_no = '$ord->ord_opt_no' and prd_cd like '$prd_cd_p%' ";
					DB::update($sql_hst);
				}
	
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * 원가 확정
	 */
	private function confirmWonga($stock_no, $prd_cd, $goods_no, $qty, $cost, $invoice_no)
	{
		$stock = DB::table('product_stock')->select('wonga', 'in_qty')->where('prd_cd', '=', $prd_cd)->first();
		
		try {
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

				// 2. 상품테이블 원가값 업데이트
				// 2-1. goods 업데이트
				DB::table('goods')->where('goods_no', $goods_no)->update([ 'wonga' => $avg_wonga ]);
				// 2-2. product 업데이트
				DB::table('product')->where('prd_cd', $prd_cd)->update([ 'wonga' => $avg_wonga ]);

				// 3. 모든 판매된 주문건(및 hst)의 원가 값 업데이트
				$sql = "
					update order_opt set
						wonga = '$avg_wonga'
					where prd_cd = '$prd_cd'
				";
				DB::update($sql);

				$orders = DB::select("select ord_opt_no from order_opt where prd_cd = '$prd_cd'");
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

				// // ** hst 로그 업데이트 여부에 대한 논의필요
				// // 4. 입고완료 ~ 원가확정 기간동안 매장으로 출고된 hst 로그 기록의 원가 값 업데이트
				// $fin_date = date('Ymd', strtotime($fin_rt));
				// $sql = "
				// 	update product_stock_hst set
				// 		wonga = '$avg_wonga'
				// 	where prd_cd = '$prd_cd' 
				// 		and ((type = '1' and location_type = 'STORE') or (type = '17' and location_type = 'STORAGE')) 
				// 		and stock_state_date >= '$fin_date' and stock_state_date <= '$fin_date'
				// ";
				// DB::update($sql);
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * 확정된 원가 업데이트 (원개재확정 - 슈퍼권한)
	 */
	private function updateConfirmedWonga($stock_no, $prd_cd, $goods_no, $qty, $cost, $prd_tariff_rate, $invoice_no, $ori_product_stock, $stk_ord_product)
	{
		$stock = DB::table('product_stock')->select('wonga', 'in_qty')->where('prd_cd', '=', $prd_cd)->first();

		try {
			if (
				$stock != null
				&& ($stk_ord_product->exp_qty != $qty
					|| $stk_ord_product->cost != $cost
					|| $stk_ord_product->prd_tariff_rate != $prd_tariff_rate)
			) {
				// 1. 재고테이블 평균원가 및 재고총원가 값 업데이트
				$total_old_wonga = ($ori_product_stock->wonga * $ori_product_stock->in_qty) - ($stk_ord_product->exp_qty * $stk_ord_product->cost);
				$total_cur_wonga = $qty * $cost;
				$total_wonga = $total_old_wonga + $total_cur_wonga;
				$avg_wonga = round($total_wonga / ($stock->in_qty ?? 1));
				$values = [
					'wonga' => $avg_wonga,
					'qty_wonga' => DB::raw('qty * ' . $avg_wonga),
					'ut' => now(),
				];
				DB::table('product_stock')->where('prd_cd', '=', $prd_cd)->update($values);

				// 2. 상품테이블 원가값 업데이트
				// 2-1. goods 업데이트
				DB::table('goods')->where('goods_no', $goods_no)->update([ 'wonga' => $avg_wonga ]);
				// 2-2. product 업데이트
				DB::table('product')->where('prd_cd', $prd_cd)->update([ 'wonga' => $avg_wonga ]);

				// 3. 모든 판매된 주문건(및 hst)의 원가 값 업데이트
				$sql = "
					update order_opt set
						wonga = '$avg_wonga'
					where prd_cd = '$prd_cd'
				";
				DB::update($sql);

				$orders = DB::select("select ord_opt_no from order_opt where prd_cd = '$prd_cd'");
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

				// // ** hst 로그 업데이트 여부에 대한 논의필요
				// // 4. 입고완료 ~ 원가확정 기간동안 매장으로 출고된 hst 로그 기록의 원가 값 업데이트
				// $fin_date = date('Ymd', strtotime($fin_rt));
				// $sql = "
				// 	update product_stock_hst set
				// 		wonga = '$avg_wonga'
				// 	where prd_cd = '$prd_cd' 
				// 		and ((type = '1' and location_type = 'STORE') or (type = '17' and location_type = 'STORAGE')) 
				// 		and stock_state_date >= '$fin_date' and stock_state_date <= '$fin_date'
				// ";
				// DB::update($sql);
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/** 엑셀 개별상품데이터 상세조회 */
	public function getGood($prd_cd) {

		$sql = "
			select a.*
				, if (a.goods_no = 0, a.opt, a.opt_kind_cd) as opt_kind_cd
				, if (a.goods_no = 0, c.code_val, op.opt_kind_nm) as item
			from (
				select 
					pc.prd_cd
					, pc.goods_no
					, pc.prd_cd_p
					, pc.color
					, pc.size
					, if(pc.goods_no = 0, p.prd_nm, g.goods_nm) as goods_nm
					, if(pc.goods_no = 0, p.prd_nm_eng, g.goods_nm_eng) as goods_nm_eng
					, if(pc.goods_no = 0, p.style_no, g.style_no) as style_no
					, if(pc.goods_no = 0, p.tag_price, g.goods_sh) as goods_sh
					, if(pc.goods_no = 0, p.price, g.price) as price
					, if(pc.goods_no = 0, p.wonga, g.wonga) as wonga
					, if(pc.goods_no = 0, p.com_id, g.com_id) as com_id
					, pc.goods_opt as opt_kor
					, pc.opt
					, g.opt_kind_cd
					, pc.brand as brand_cd
					, b.brand_nm as brand
					, ps.qty as total_qty
					, ps.wqty as sg_qty
				from product_code pc
					inner join product p on p.prd_cd = pc.prd_cd
					left outer join product_stock ps on ps.prd_cd = pc.prd_cd
					left outer join goods g on g.goods_no = pc.goods_no
					left outer join brand b on b.br_cd = pc.brand
				where pc.prd_cd = :prd_cd
			) a
				left outer join opt op on op.opt_kind_cd = a.opt_kind_cd and op.opt_id = 'K'
				left outer join code c on c.code_kind_cd = 'PRD_CD_OPT' and c.code_id = a.opt
		";
		$row = DB::selectOne($sql, ['prd_cd' => $prd_cd]);

		if ($row == null) {
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

	public function getInvoiceNo($com_id, $stock_date) {
		$sql = "
			select
				invoice_no
			from product_stock_order
			order by stock_no desc
		";
		
		$invoice_no = DB::selectOne($sql);
		$invoice_seq = explode("_", $invoice_no->invoice_no);
		$invoice_seq = $invoice_seq[2];
		$seq = substr($invoice_seq, 1);
		$invoice_str = substr($invoice_seq, 0, 1);
		$result = 0;
		$format_result = "";
		if ($seq == "999") {
			$invoice_str = "B";
			$result = 1;
			$format_result = $invoice_str . str_pad($result, 3, '0', STR_PAD_LEFT);
		} else {
			$result = (int)$seq + 1;
			$format_result = $invoice_str . str_pad($result, 3, '0', STR_PAD_LEFT);
		}
		$invoice_no = sprintf("%s_%s_%s",$com_id,$stock_date, $format_result);

		return $invoice_no;
		
//		2024-01-04 양대성 주석처리 (기존 입고번호 가져오는 부분)
//		$prefix_invoice_no = sprintf("%s_%s_A",$com_id,$stock_date);
//		$sql = "
//			select ifnull(max(invoice_no),0) as invoice_no from product_stock_order
//			where invoice_no like '$prefix_invoice_no%'
//		";
//		$row = DB::selectOne($sql);
//		$max_invoice_no = $row->invoice_no;
//		if ($max_invoice_no == "0"){
//			$seq = 1;
//		} else {
//			$seq = str_replace($prefix_invoice_no,"",$max_invoice_no);
//			$seq = $seq + 1;
//		}
//		$invoice_no = sprintf("%s%03d",$prefix_invoice_no,$seq);
//		return $invoice_no;
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
