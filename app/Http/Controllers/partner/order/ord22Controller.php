<?php

namespace App\Http\Controllers\partner\order;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;
use App\Models\Order;
use App\Models\Conf;
use App\Models\Product;
use App\Models\Gift;
use App\Models\SMS;
use Exception;

class ord22Controller extends Controller
{
    public function index()
    {
        $com_id = Auth('partner')->user()->com_id;

        $mutable    = Carbon::now();
        $sdate      = $mutable->sub(1, 'month')->format('Y-m-d');

        $conf   = new Conf();
        $cfg_dlv_cd = $conf->getConfigValue("delivery","dlv_cd");

        $values = [
            'sdate'			=> $sdate,
            'edate'			=> date("Y-m-d"),
            'items'			=> SLib::getItems(),
            'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
            'sale_places'	=> SLib::getSalePlaces(),
            'ord_states'	=> SLib::getOrdStates(),
            'clm_states'	=> SLib::getClmStates(),
            'ord_kinds'		=> SLib::getCodes('G_ORD_KIND'),
            'ord_types'		=> SLib::getCodes('G_ORD_TYPE'),
            'dlv_kinds'		=> SLib::getCodes('G_BAESONG_KIND'),
            'dlv_types'		=> SLib::getCodes('G_DLV_TYPE'),
            'dlv_cd'		=> $cfg_dlv_cd,
            'dlvs'			=> SLib::getCodes('DELIVERY'),
            'com_types'		=> SLib::getCodes('G_COM_TYPE'),
            'dlv_series_nos'    => SLib::getDlvSeries($com_id)
        ];

        return view( Config::get('shop.partner.view') . '/order/ord22',$values);
    }

    public function search(Request $req) {

        $com_id = Auth('partner')->user()->com_id;

        // 설정 값 얻기
        $conf = new Conf();
        $cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
        $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

        $condition = $this->get_condition($req);

        $page = $req->input('page',1);
        if ($page < 1 or $page == "") $page = 1;

        $limit = $req->input('limit',100);

        $where = $condition[0];
        $orderby = $condition[1];

        $page_size = $limit;
        $startno = ($page-1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = /** @lang text */
                "
				select count(*) as total
				from order_opt a
			        inner join order_mst b on a.ord_no = b.ord_no
			        inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
			        inner join payment d on b.ord_no = d.ord_no
			        inner join company f on a.sale_place = f.com_id and f.com_type='4'
			        inner join company e on a.com_id = e.com_id
			        left outer join order_opt_memo g on a.ord_opt_no = g.ord_opt_no
			        left outer join claim i on a.ord_opt_no = i.ord_opt_no
			        left outer join order_dlv_series n on n.dlv_series_no = a.dlv_series_no
		            where 1 = 1
			        and a.com_id = :com_id and ( a.clm_state = '0' or a.clm_state = '-30')
			    $where
			";
            //echo "<pre>$query $com_id</pre>";
            $row = DB::select($query,["com_id" => $com_id]);
            //$row = DB::select($query);
            $total = $row[0]->total;
            if($total > 0){
                $page_cnt=(int)(($total-1)/$page_size) + 1;
            }
        }

        $sql = /** @lang text */
            "
		select
		  '' as chk, a.dlv_series_nm, a.dlv_series_no, a.dlv_cd, a.dlv_no, 
		  ord_type.code_val as ord_type_nm, ord_kind.code_val as ord_kind_nm,
		  a.user_nm, a.r_nm,
		  a.ord_no, a.ord_opt_no, ord_state.code_val as ord_state_nm, pay_stat.code_val as pay_stat,
		  dlv_type.code_val as dlv_type,
		  clm_state.code_val as clm_state_nm, ifnull(gt.code_val,'N/A') as goods_type_nm,
		  a.style_no, '' as img_col, a.goods_nm, if( a.goods_addopt = '', a.goods_opt, concat(a.goods_opt,' : ', a.goods_addopt)) as opt_val,
		  a.sale_qty, a.qty, a.wqty, a.price, a.sale_amt, a.gift, a.dlv_amt,
		  pay_type.code_val pay_type, a.r_zipcode, a.r_addr, a.r_phone, a.r_mobile, a.r_jumin,
		  a.dlv_msg, a.dlv_comment, a.proc_state, a.proc_memo, a.gift, a.sale_place, a.out_ord_no,
		  a.com_nm, baesong_kind.code_val baesong_kind, a.ord_date, a.pay_date,
		  a.dlv_proc_date, a.dlv_end_date, a.last_up_date,
		  a.goods_no, a.goods_sub, '1' as depth, a.ord_state, a.clm_state, a.img, a.gift_info
		from
		(
		  select
			  a.ord_kind, a.ord_type, a.ord_no, a.ord_opt_no,a.ord_state, d.pay_stat,
			b.dlv_type, a.clm_state,
			c.goods_type, c.style_no, a.goods_nm,
			replace(a.goods_opt,'^',' : ') as goods_opt,
			if( ifnull(a.goods_addopt,'') = '',( select ifnull(group_concat(concat(addopt,'(+',addopt_amt,')')),'')  from order_opt_addopt where ord_opt_no = a.ord_opt_no ),a.goods_addopt) as goods_addopt,
			a.qty as sale_qty, n.dlv_series_nm, n.dlv_series_no,
			ifnull( (
			  select sum(if(c.goods_type = 'P', good_qty, wqty)) from goods_summary
			  where goods_no = a.goods_no and goods_sub = a.goods_sub and goods_opt = a.goods_opt
			), 0) as qty,
			ifnull( (
				select sum(wqty) from goods_summary
				where goods_no = a.goods_no and goods_sub = a.goods_sub and goods_opt = a.goods_opt
			), 0) as wqty,
			a.price, (a.coupon_amt+a.dc_amt) as sale_amt,
			(
			  select group_concat(gf.name)
			  from order_gift og
				inner join gift gf on og.gift_no = gf.no
			  where og.ord_no = a.ord_no and og.ord_opt_no = a.ord_opt_no
			) as gift,
			a.dlv_amt,
			case d.pay_type
			  when '0' then d.card_name
			  when '1' then d.bank_code
			  when '4' then '-'
			else d.bank_code end bank_code,
			b.r_zipcode, concat(ifnull(b.r_addr1, ''),' ',ifnull(b.r_addr2, '')) as r_addr, b.r_phone, b.r_mobile, b.r_jumin,
			b.dlv_msg, a.dlv_comment,
			case when user_id <> '' then concat(ifnull(user_nm, ''),'(',ifnull(user_id, ''),')')
			  else user_nm end as user_nm,
			b.r_nm, f.com_nm as sale_place,
			b.out_ord_no,e.com_nm,
			c.baesong_kind, b.ord_date,d.pay_date,
			a.dlv_proc_date, a.dlv_end_date, i.last_up_date, c.goods_no, c.goods_sub, b.user_id, d.pay_type,
			a.dlv_no, a.dlv_cd,
			g.state as proc_state, g.memo as proc_memo,
			replace(c.img,'$cfg_img_size_real','$cfg_img_size_list') as img,
			(
			  select group_concat(gf.no)
			  from order_gift og
				inner join gift gf on og.gift_no = gf.no
			  where og.ord_no = a.ord_no and og.ord_opt_no = a.ord_opt_no
			) as gift_info
		  from order_opt a
			inner join order_mst b on a.ord_no = b.ord_no
			inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
			inner join payment d on b.ord_no = d.ord_no
			inner join company f on a.sale_place = f.com_id and f.com_type='4'
			inner join company e on a.com_id = e.com_id
			left outer join order_opt_memo g on a.ord_opt_no = g.ord_opt_no
			left outer join claim i on a.ord_opt_no = i.ord_opt_no
			left outer join order_dlv_series n on n.dlv_series_no = a.dlv_series_no
		  where 1 = 1
			and a.com_id = :com_id and ( a.clm_state = '0' or a.clm_state = '-30')
			$where
			$orderby
            limit $startno, $page_size
		) a
		left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
		left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
		left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
		left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
		left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
		left outer join code baesong_kind on (a.baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
		left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
		left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
		left outer join code dlv_type on (a.dlv_type = dlv_type.code_id and dlv_type.code_kind_cd = 'G_DLV_TYPE')
		";

        $result = DB::select($sql,["com_id" => $com_id]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function update_state(Request $req) {

        $user = Auth('partner')->user();

        //$ord_kind = Request("ord_kind", 30);
        //$ord_state = Request("ord_state", 10);
        //$order_nos = Request("order_nos", array());

        $ord_kind	= $req->input("ord_kind", 30);
        $ord_state	= $req->input("ord_state", 10);
        $order_nos	= $req->input("order_nos", array());

        try{
            // Start transaction
            DB::beginTransaction();

            $order = new Order([
                'id' => $user->com_id,
                'name' => $user->com_nm
            ]);

            for($i = 0; $i < count($order_nos); $i++){
                if(trim($order_nos[$i]) != ""){
                    list($ord_no, $ord_opt_no) = explode(",", $order_nos[$i]);
                    $order->SetOrdOptNo($ord_opt_no, $ord_no);

                    //주문상태 로그
                    $order->AddStateLog(['ord_state' => $ord_state], "출고요청(보류)");

                    //주문 상품의 주문상태를 출고처리중에서 출고요청 상태로 변경
                    $order->DlvReqWait($ord_kind, $ord_state);
                }
            }
            // Finish transaction
            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();

            return response()->json([
                "msg" => $e->getMessage()
            ], 500);
        }
    }

    public function dlv_import(Request $req)
    {
        $conf   = new Conf();
        $cfg_dlv_cd			= $conf->getConfigValue("delivery","dlv_cd");
        $cfg_delivery_yn	= $conf->getConfigValue("sms","delivery_yn");

        $values = [
            'dlvs'		=> SLib::getCodes('DELIVERY'),
            'dlv_cd'	=> $cfg_dlv_cd
        ];

        return view( Config::get('shop.partner.view') . '/order/ord22_import',$values);
    }

    public function dlv_import_search() {

        $com_id = Auth('partner')->user()->com_id;
        $id = $com_id;

        $msgs = array(
            "100" => "주문번호 없음",
            "110" => "주문상태 오류",
            "111" => "출고완료",
            "120" => "클레임 주문",
            "130" => "출고보유 주문",
        );

        $sql = /** @lang text */
            "
			select
				'0' as chk,ifnull(t.msg,'') as msg, t.ord_opt_no, t.dlv_cd, dlv_cd.code_val as dlv_cd_nm, t.dlv_no,
				o.ord_no, ord_state.code_val as ord_state_nm, clm_state.code_val as clm_state_nm,
				ord_kind.code_val as ord_kind_nm,n.dlv_series_nm,
				o.goods_nm, o.goods_opt, o.qty as qty,o.goods_no,o.goods_sub,o.ord_state,o.clm_state,o.ord_kind
			from delivery_import t
			left outer join order_opt o on t.ord_opt_no = o.ord_opt_no
			left outer join code dlv_cd on (t.dlv_cd = dlv_cd.code_id and dlv_cd.code_kind_cd = 'DELIVERY')
			left outer join code ord_kind on (o.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
			left outer join code ord_state on (o.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
			left outer join code clm_state on (o.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
			left outer join order_dlv_series n on n.dlv_series_no = o.dlv_series_no
			where t.com_id = '$com_id' and t.admin_id = '$id' and t.dlv_yn = 'N'
		";

        $rows = DB::select($sql);

        foreach ($rows as $row) {
            if($row->ord_no == ""){
                $row->chk = 2;
                $row->msg = $msgs["100"];
            } else if ($row->ord_state == "30") {
                $row->chk = 2;
                $row->msg = $msgs["111"];
            } else if($row->ord_state != "20"){
                $row->chk = 2;
                $row->msg = $msgs["110"];
            } else if($row->clm_state > 0){
                $row->chk = 2;
                $row->msg = $msgs["120"];
            } else if($row->ord_kind != 10 && $row->ord_kind != 20){
                $row->chk = 2;
                $row->msg = $msgs["130"];
            }
        }

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    //택배송장 일괄 입력 페이지에서 적용 버튼 눌렀을 경우 실행됨
    public function dlv_import_upload(Request $req) {

        $com_id = Auth('partner')->user()->com_id;
        $id = $com_id;

        try {
            DB::beginTransaction();

            //기존 데이터 삭제
            DB::table('delivery_import')
                ->where('com_id',$com_id )
                ->where('admin_id', $com_id)
                ->delete();

            $datas = $req->input('datas', []);
            $dlv_cd = $req->input('dlv_cd', '');

            foreach($datas as $data) {
                list($ord_opt_no, $dlv_no) = explode(",", $data);

                // 주문번호
                $sql = "select ord_no from order_opt where ord_opt_no = '$ord_opt_no'";
                $ord_no = DB::selectOne($sql);

                if ($ord_no) {
                    DB::table('delivery_import')->insert([
                        'ord_opt_no' => $ord_opt_no ,
                        'ord_no' => $ord_no->ord_no,
                        'dlv_cd' => $dlv_cd,
                        'dlv_no' => $dlv_no,
                        'msg' => '',
                        'dlv_yn' => 'N',
                        'msg_yn' => 'N',
                        'com_id' => $com_id,
                        'admin_id' => $id,
                        'rt' => now(),
                        'ut' => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json(["msg" => "업로드 되었습니다."], 201);
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(["msg" => $e->getMessage()], 500);
        }
    }

    public function show(Request $req) {

        $com_id = Auth('partner')->user()->com_id;
        $type = 'dlv_inv_dn_'.$com_id;

        $sql = /** @lang text */
            "select count(*) as cnt from columns where type = '$type'";

        $count = DB::selectOne($sql);

        if($count->cnt == 0){
            $sql = /** @lang text */
                "
			insert into columns ( type, cn, name, seq, use_yn, use_seq, rt, ut )
			select '$type' as type, cn, name, seq, use_yn, use_seq, now() as rt, now() as ut
			from columns where type = 'dlv_inv_dn' order by use_seq
			";

            DB::insert($sql);
        }

		$sql = "select cn as name, name as value, seq, use_seq from columns where type = '$type' order by seq";
        $columns = DB::select($sql);

		/** 사은품 항목 없을 시, DB에 저장 -> 실DB 적용 완료 시 코드삭제 */
		$gift_column = array_reduce($columns, function ($a, $c) {
			if ($c->name === 'gift') return $c;
			return $a;
		});

		if ($gift_column === null) {
			DB::table('columns')->insert([
				'type' => $type,
				'cn' => 'gift',
				'name' => '사은품',
				'seq' => $columns[count($columns) - 1]->seq + 1,
				'use_yn' => 'N',
				'use_seq' => 0,
				'rt' => now(),
				'ut' => now(),
			]);

			$sql = "select cn as name, name as value from columns where type = '$type' order by seq";
			$columns = DB::select($sql);
		}
		/** //end 사은품 항목 없을 시, DB에 저장 -> 실DB 적용 완료 시 코드삭제 */

        $sql = /** @lang text */
            "select cn as name, name as value from columns where type = '$type' and use_yn = 'Y' order by use_seq";
        $fields = DB::select($sql);

        return view( Config::get('shop.partner.view') . '/order/ord22_show', [
            'columns' => $columns,
            'fields' => $fields,
            'requests' => $req->all(),
            'sale_places' => SLib::getSalePlaces()
        ]);
    }

    public function download_delivery_list(Request $request)
    {
        $com_id = Auth('partner')->user()->com_id;

        $condition	= $this->get_condition($request);
        $where		= $condition[0];
        $orderby	= $condition[1];
        $where_dt	= "";

        $sql = /** @lang text */
            "
			select
				 -- replace(g.img,'_a_500','_s_62') as img,
				 g.style_no,g.brand,c.com_nm,g.goods_location,
				 a.goods_nm,a.goods_opt,a.ord_cnt, a.sale_qty,a.qty,a.wqty,' ' as rqty
			from (
				select
					a.goods_no,a.goods_sub,
					goods_opt(a.goods_opt,a.goods_addopt,a.ord_opt_no) as goods_opt,
					a.goods_nm,
					count(distinct(a.ord_opt_no)) as ord_cnt,
					sum(a.qty) as sale_qty,
					min(
						ifnull((select sum(good_qty) from goods_summary
						where goods_no = a.goods_no and goods_sub = a.goods_sub and goods_opt = a.goods_opt),0)) as qty,
					min(
						ifnull((select sum(wqty) from goods_summary
						where goods_no = a.goods_no and goods_sub = a.goods_sub and goods_opt = a.goods_opt),0)) as wqty
				from order_opt a
					 inner join order_mst b on a.ord_no = b.ord_no
					 inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
				where 1 = 1
				    and a.com_id = :com_id
					$where_dt
					and ( a.clm_state = '0' or a.clm_state = '-30')
					$where
				group by a.goods_no, a.goods_sub,goods_opt
			) a inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
				inner join company c on g.com_id = c.com_id
			order by g.brand,c.com_nm,g.goods_nm
		";

        $this->set_excel_download("delivery_list_%s.xls");

        return view( Config::get('shop.partner.view') . '/order/ord22_delivery_excel',[
            'rows' => DB::select($sql,["com_id" => $com_id]),
        ]);
    }

    public function download_baesong_list(Request $request) {
        $condition = $this->get_condition($request);
        $where = $condition[0];
        $orderby = $condition[1];

        $com_id = Auth('partner')->user()->com_id;
        $type = sprintf("dlv_inv_dn_%s", $com_id);

        DB::transaction(function () use($request, $type) {
            $fields	 = explode(",",$request->fields);

            $sql = "update columns set use_yn = 'N' , ut = now() where type = '$type'";
            DB::update($sql);

            for ($i=0; $i < count($fields); $i++){
                $cn = $fields[$i];
                $sql = "
					update columns 
					set use_yn = 'Y', 
						use_seq = '$i', 
						ut = now()
					where type = '$type' and cn = '$cn'
				";

                DB::update($sql);
            }
        });

        $sql = " select cn as name, name as value from columns where type = '$type' and use_yn = 'Y' order by use_seq ";
        $fields = DB::select($sql);

        $sql = /** @lang text */
            "
			select
			ifnull(a.dlv_series_nm,' ') as dlv_series_nm,
			ifnull(ord_type.code_val,'') as ord_type,
			ifnull(ord_kind.code_val,'') as ord_kind,
			a.ord_no, a.ord_opt_no, a.ord_cnt, a.goods_no,
			ord_state.code_val as ord_state_nm, pay_stat.code_val as pay_stat,
			clm_state.code_val as clm_state, ifnull(gt.code_val,'N/A') as goods_type_nm,
			a.style_no, a.goods_nm, if(a.goods_addopt = '', a.goods_opt, concat(a.goods_opt,' : ', a.goods_addopt)) as goods_opt,
			a.goods_location,
			a.sale_qty, a.qty, a.price, a.recv_amt, a.sale_amt, a.dlv_amt,
			pay_type.code_val as pay_type, a.user_nm, a.r_nm, a.r_jumin, a.r_zipcode, a.r_addr, a.r_phone, a.r_mobile,
			a.dlv_msg, a.dlv_comment, a.gift, a.sale_place, a.out_ord_no,
			a.com_nm, baesong_kind.code_val baesong_kind,
			date_format(a.ord_date,'%Y-%m-%d %H:%i:%s') as ord_date,
			ifnull(date_format(a.pay_date,'%Y-%m-%d %H:%i:%s'),' ') as pay_date,
			ifnull(date_format(a.dlv_proc_date,'%Y-%m-%d %H:%i:%s'),' ') as dlv_proc_date,
			a.state, a.memo, a.member_memo, a.head_desc, a.opt_memo,
			a.zip, a.addr, a.phone, a.mobile,
			a.goods_nm_abr,
			a.blank1, a.blank2, a.blank3, a.blank4, a.blank5
			from
			(
			select
				a.ord_kind, a.ord_type, a.ord_no, a.ord_opt_no,
				( select count(*) from order_opt where ord_no = a.ord_no ) as ord_cnt,
				a.ord_state, d.pay_stat, a.clm_state,
				c.goods_type, c.style_no, a.goods_nm,
				replace(a.goods_opt,'^',' : ') as goods_opt,
				if(ifnull(a.goods_addopt,'') = '', (select ifnull(group_concat(concat(addopt,'(+',addopt_amt,')')),'') from order_opt_addopt where ord_opt_no = a.ord_opt_no ),a.goods_addopt) as goods_addopt,
				c.goods_location,
				a.qty as sale_qty, n.dlv_series_nm,
				ifnull( (
				select sum(good_qty) from goods_summary
				where goods_no = a.goods_no and goods_sub = a.goods_sub
				), 0) as qty,
				a.price, (a.price - a.coupon_amt) as recv_amt, (a.coupon_amt+a.dc_amt) as sale_amt, 
				(
					select group_concat(gf.name)
					from order_gift og
						inner join gift gf on og.gift_no = gf.no
					where og.ord_no = a.ord_no and og.ord_opt_no = a.ord_opt_no
				) as gift,
				a.dlv_amt,
				case d.pay_type
				when '0' then d.card_name
				when '1' then d.bank_code
				when '4' then '-'
				else d.bank_code end bank_code,
				b.r_zipcode, concat(ifnull(b.r_addr1, ''),' ',ifnull(b.r_addr2, '')) as r_addr, b.r_phone, b.r_mobile,
				b.dlv_msg, a.dlv_comment,
				concat(ifnull(b.user_nm, ''),'(',ifnull(b.user_id, ''),')') as user_nm, b.r_nm, b.r_jumin, f.com_nm as sale_place,
				b.out_ord_no,e.com_nm,
				c.baesong_kind, b.ord_date,d.pay_date,
				a.dlv_proc_date, a.dlv_end_date, i.last_up_date, c.goods_no, c.goods_sub, b.user_id, d.pay_type,
				a.dlv_no, a.dlv_cd,
				g.state, g.memo, m.memo as member_memo, c.head_desc,
				m.zip, concat(m.addr,m.addr2) as addr, m.phone, m.mobile,
				( select opt_memo from goods_summary where goods_no = a.goods_no and goods_sub = a.goods_sub and goods_opt = a.goods_opt ) as opt_memo,
				concat(ifnull(substr(a.goods_nm,1,20), ''),'...') as goods_nm_abr,
				'' as blank1, '' as blank2, '' as blank3, '' as blank4, '' as blank5
			from order_opt a
				inner join order_mst b on a.ord_no = b.ord_no
				inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
				inner join payment d on b.ord_no = d.ord_no
				inner join company e on a.com_id = e.com_id
				inner join company f on a.sale_place = f.com_id and f.com_type='4'
				left outer join order_opt_memo g on a.ord_opt_no = g.ord_opt_no
				left outer join claim i on a.ord_opt_no = i.ord_opt_no
				left outer join order_dlv_series n on n.dlv_series_no = a.dlv_series_no
				left outer join member m on b.user_id = m.user_id
			where 1 = 1
			    and a.com_id = :com_id
				and ( a.clm_state = '0' or a.clm_state = '-30')
				$where
			$orderby
			) a
			left outer join code ord_type on (a.ord_type = ord_type.code_id and ord_type.code_kind_cd = 'G_ORD_TYPE')
			left outer join code ord_kind on (a.ord_kind = ord_kind.code_id and ord_kind.code_kind_cd = 'G_ORD_KIND')
			left outer join code ord_state on (a.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
			left outer join code pay_type on (a.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
			left outer join code clm_state on (a.clm_state = clm_state.code_id and clm_state.code_kind_cd = 'G_CLM_STATE')
			left outer join code baesong_kind on (a.baesong_kind = baesong_kind.code_id and baesong_kind.code_kind_cd = 'G_BAESONG_KIND')
			left outer join code pay_stat on (a.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
			left outer join code gt on (a.goods_type = gt.code_id and gt.code_kind_cd = 'G_GOODS_TYPE')
		";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["com_id" => $com_id]);
        $rows = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $rows[] = $row;
        }

        $this->set_excel_download("택배송장목록_%s.xls");

        return view( Config::get('shop.partner.view') . '/order/ord22_pop_excel',[
            'rows' => $rows,
            'fields' => $fields
        ]);
    }

    private function set_excel_download($filename_format) {
        $filename = sprintf($filename_format,date("YmdH"));

        header("Content-type: application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");

    }

    private function get_condition(Request $request) {
        //주문일자 검색일자, 검색 타입
        $sdate = $request->input('sdate',Carbon::now()->sub(7, 'day')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));

        //주문상태, 배송방식
        $ord_state = $request->input('ord_state');
        $dlv_type = $request->input('dlv_type');

        //주문번호
        $ord_no = $request->input('ord_no');

        //판매처
        $sale_place = $request->input('sale_place');

        //주문자, 수령자 이름
        $user_nm = $request->input('user_nm');
        $r_nm = $request->input('r_nm');

        //정렬순서
        $ord_field = $request->input('ord_field');
        $ord       = $request->input("ord","desc");

        //스타일넘버
        $style_no = $request->input('style_no');

        //상품명
        $goods_nm = $request->input('goods_nm');

        //배송구분
        $dlv_kind = $request->input('dlv_kind');

        //주문구분
        $ord_type = $request->input('ord_type');

        //출고구분
        $ord_kind = $request->input('ord_kind');

        $dlv_series_no = $request->input('dlv_series_no');

        //업체
        $com_type       = $request->input("com_type", "");
        $com_id         = $request->input("com_id", "");

        $where = "";
        $orderby = "";

        if($com_type != "")	  $where .= " and c.com_type   = '$com_type' ";
        if($com_id != "")	  $where .= " and c.com_id     = '$com_id' ";
        if($ord_state != "")  $where .= " and a.ord_state    = '" . Lib::quote($ord_state) . "' ";
        if($dlv_type != "")   $where .= " and b.dlv_type     = '" . Lib::quote($dlv_type) . "'";
        if($dlv_kind != "")   $where .= " and c.baesong_kind = '" . Lib::quote($dlv_kind) . "' ";
        if($user_nm != "")	  $where .= " and b.user_nm      = '" . Lib::quote($user_nm) . "' ";
        if($r_nm != "")		  $where .= " and b.r_nm         = '" . Lib::quote($r_nm) . "' ";
        if($style_no != "")   $where .= " and c.style_no like  '" . Lib::quote($style_no)."%'";
        if($ord_no != "")	  $where .= " and a.ord_no       = '" . Lib::quote($ord_no) . "' ";
        if($ord_type != "")   $where .= " and a.ord_type     = '" . Lib::quote($ord_type) . "'";
        if($ord_kind != "")   $where .= " and a.ord_kind     = '" . Lib::quote($ord_kind) . "' ";
        if($goods_nm != "")   $where .= " and a.goods_nm like  '%" . Lib::quote($goods_nm) . "%' ";
        if($sale_place != "") $where .= " and a.sale_place   = '" . Lib::quote($sale_place) . "' ";
        if($dlv_series_no != "") $where .= " and a.dlv_series_no   = '" . Lib::quote($dlv_series_no) . "' ";


        if($ord_field == "r_nm"){
            $orderby = " order by b.r_nm,r_addr,a.ord_no,a.ord_opt_no $ord ";
        } else if($ord_field == "ord_no"){
            $orderby = " order by a.ord_no,a.ord_opt_no $ord ";
        } else {
            $orderby = " order by goods_nm asc,a.ord_opt_no $ord ";
        }
        $where .= " and a.ord_date BETWEEN '$sdate' AND DATE_ADD('$edate', INTERVAL 1 DAY) ";

        return [$where, $orderby];
    }

    public function out_complete(Request $request) {

		$code 	= 200;
		$msg 	= "모든 항목이 정상적으로 출고완료 처리되었습니다.";

        // 설정 값 얻기
        $conf = new Conf();
        $cfg_shop_name		= $conf->getConfigValue("shop","name");
        $cfg_kakao_yn		= $conf->getConfigValue("kakao","kakao_yn");
        $cfg_sms			= $conf->getConfig("sms");
        $cfg_sms_yn			= $conf->getValue($cfg_sms,"sms_yn");
        $cfg_delivery_yn	= $conf->getValue($cfg_sms,"delivery_yn");
        $cfg_delivery_msg	= $conf->getValue($cfg_sms,"delivery_msg");
        $shop_phone 		= $conf->getConfigValue("shop","phone");
		
        $user = [
            'id'	=> Auth('partner')->user()->com_id,
            'name'	=> Auth('partner')->user()->com_nm
        ];
        $order_nos		= $request->input("order_nos", array());
        $dlv_cd			= $request->input("dlv_cd", '');
        $send_sms_yn	= $request->input("send_sms_yn", 'N');

		$failed = [];

		foreach ($order_nos as $order_data) {
			list ($ord_no, $ord_opt_no, $dlv_no) = explode(",", $order_data);
			
			try {
				DB::beginTransaction();

				$dlv_nm = SLib::getCodesValue('DELIVERY', $dlv_cd);
				if ($dlv_nm === "") $dlv_nm = $dlv_cd;

				$order = new Order($user);
				$order->SetOrdOptNo($ord_opt_no, $ord_no);

				// 중복 방지 상태 점검
				$check_state = $order->CheckState("30");

				if (!$check_state) {
					throw new Exception("선택하신 주문 중 이미 출고된 주문건이 있습니다. 검색 후 다시 처리하여 주십시오.");
				}

                /*******************************************************
                 * 주문상태 로그
                 *******************************************************/
                $state_log	= [
                    "ord_no"		=> $ord_no,
                    "ord_opt_no"	=> $ord_opt_no,
                    "ord_state"		=> "30",
                    "comment"		=> "배송 출고처리",
                    "admin_id"		=> $user['id'],
                    "admin_nm"		=> $user['name']
                ];

                $order->AddStateLog($state_log);

                $order->DlvEnd($dlv_cd, $dlv_no);
                $order->DlvLog($ord_state = 30);

                ################################################################
                // 보유재고 차감 로직 추가

				$sql = "
					select qty
					     , goods_no
					     , goods_sub
					     , goods_opt
					from order_opt
					where ord_opt_no = :ord_opt_no
				";
				$opt = DB::selectOne($sql, [ 'ord_opt_no' => $ord_opt_no ]);
				
				$_qty 		= $opt->qty;
				$_goods_no	= $opt->goods_no;
				$_goods_sub	= $opt->goods_sub;
				$_goods_opt	= $opt->goods_opt;

                $prd = new Product($user);

				// 재고 차감 처리
				$stocks = $ret = $prd->Minus([
					"type"			=> $type = 2,
					"etc" 			=> "배송출고처리",
					"qty" 			=> $_qty,
					"goods_no"		=> $_goods_no,
					"goods_sub"		=> $_goods_sub,
					"goods_opt"		=> $_goods_opt,
					"ord_no"		=> $ord_no,
					"ord_opt_no"	=> $ord_opt_no,
				]);

				if (count($stocks) > 0) {
					// 추가옵션에 대한 재고 차감
					$sql = "
						select addopt_idx, addopt_qty
						from order_opt_addopt
						where ord_opt_no = :ord_opt_no
					";
					$rows = DB::select($sql, [ 'ord_opt_no' => $ord_opt_no ]);

					foreach ($rows as $row) {
						$_addopt_idx = $row->addopt_idx;
						$_addopt_qty = $row->addopt_qty;

						$sql2 = "
							update` options set
								wqty = wqty - $_addopt_qty
							where `no = '$_addopt_idx'
						";
                        DB::update($sql2);
                    }
                    ################################################################

                    // 에스크로 결제 여부 검사
                    $is_escrow = $order->IsEscrowOrder();

					if ($is_escrow) {
						// 거래번호 얻기
						$sql = "select tno from payment where ord_no = :ord_no ";
						$row = DB::selectOne($sql, [ 'ord_no' => $ord_no ]);
						$tno = $row->tno;

						// Parameters
						$ip			= $_SERVER["REMOTE_ADDR"];
						$memo		= "배송 시작 요청";
						$a_param	= [ "deli_numb" => $dlv_no, "deli_corp" => $dlv_nm ];

						// 배송요청 시작
						$pg	= new pay();
						list ($res_cd, $res_msg) = $pg->mod_escrow("STE1", $tno, $ord_no, $ip, $memo, $a_param);

						// 클레임 메모 등록
						$param = [
							"ord_state"	=> 30,
							"clm_state"	=> 30,
							"cs_form"	=> 10,
							"memo"		=> $msg = "[에스크로] 배송시작[ $dlv_nm ($dlv_no) ] - $res_msg [$res_cd]",
						];
						$claim = new Claim($user);
						$claim->SetOrdOptNo($ord_opt_no);
						$claim->SetClmNo("");
						$memo_no = $claim->InsertMessage($param);
					}

					/*******************************************************
					 * 사은품 지급 :
					 *******************************************************/

					$gift = new Gift();

					$sql = "
						select no
						from order_gift
						where ord_no = :ord_no and ord_opt_no = :ord_opt_no
					";
					$gifts = DB::select($sql, [ 'ord_no' => $ord_no, 'ord_opt_no' => $ord_opt_no ]);

					foreach ($gifts as $g_row) {
						$order_gift_no = $g_row->no;
						if ($order_gift_no != "") {
							$gift->GiveGift($order_gift_no);
						}
					}

					$msg_yn = "N";

					if ($send_sms_yn != "N") {
						if ($cfg_sms_yn == "Y" && $cfg_delivery_yn == "Y") {
							$sql = "
								select 
								    b.user_nm
								    , b.mobile
								    , a.goods_nm
									, (select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y') as msg_cnt
								from order_opt a
									 inner join order_mst b on a.ord_no = b.ord_no
								where ord_opt_no = :ord_opt_no
								      and ( select count(*) from delivery_import where dlv_cd = a.dlv_cd and dlv_no = a.dlv_no and msg_yn = 'Y' ) = 0
							";
							$opt = DB::selectone($sql, [ 'ord_opt_no' => $ord_opt_no ]);
							
							if (!empty($opt->user_nm)) {
								$user_nm	= $opt->user_nm;
								$mobile		= $opt->mobile;
								$goods_nm	= mb_substr($opt->goods_nm, 0, 10);

								$sms = new SMS($user);
								$sms_msg = sprintf("[%s]%s..발송완료 %s(%s)", $cfg_shop_name, $goods_nm, $dlv_nm, $dlv_no);

								if ($cfg_kakao_yn == "Y") {
									// $template_code = "OrderCode6";
									// $msgarr = array(
									// 	"SHOP_NAME" => $cfg_shop_name,
									// 	"GOODS_NAME" => $goods_nm,
									// 	"DELIVERY_NAME" => $dlv_nm,
									// 	"DELIVERY_NO" => $dlv_no,
									// 	"USER_NAME"	=> $user_nm,
									// 	"ORDER_NO"	=> $ord_no,
									// 	"SHOP_URL"	=> 'http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo='.$dlv_no
									// );
									// $btnarr = array(
									// 	"BUTTON_TYPE" => '1',
									// 	"BUTTON_INFO" => '배송 조회하기^DS^http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo='.$dlv_no
									// );
									// $sms->SendKakao( $template_code, $mobile, $user_nm, $sms_msg, $msgarr, '', $btnarr);
								} else {
									if ($mobile != "") {
										$sms->Send($sms_msg, $mobile, $user_nm, $shop_phone);
										$msg_yn = "Y";
									}
								}
							}
						}
					}

					DB::table("delivery_import")
						->where("com_id", $user['id'])
						->where("admin_id", $user['id'])
						->where("ord_opt_no", '$ord_opt_no')
						->update([
							'dlv_yn' => 'Y',
							'msg_yn' => $msg_yn,
							'rt' 	 => DB::raw("now()")
						]);
				} else {
					throw new Exception("재고없음");
					// throw new Exception("선택하신 주문 중 재고가 없는 주문건이 있습니다. 검색 후 다시 처리하여 주십시오.[ord_no:" . $ord_no . ", ord_opt_no:" . $ord_opt_no . "]");
				}
				
				DB::commit();
			} catch (Exception $e) {
				DB::rollBack();
				$code = 206;
				$msg = "선택하신 주문 중 재고가 없는 주문건이 있습니다. 처리 실패한 항목은 다시 처리해주세요.";
				array_push($failed, $ord_opt_no);
			}
		}

		return response()->json([ 'code' => $code, 'msg' => $msg, 'body' => [ 'failed' => $failed ] ]);
    }

    private function get_is_not_use_date(Request $request) {
        if($request->ord_no != "") {
            return true;
        }

        if($request->user_id != "") {
            return true;
        }

        if($request->user_nm != "") {
            return true;
        }

        if(strlen($request->r_nm) >= 4) {
            return true;
        }

        return false;
    }

    private function getFieldName($fields) {
        $field  = "";

        foreach($fields as $val){
            if($val->name == "coupon_amt") {
                $field .= ", a.coupon_amt";
            }else if($val->name == "dlv_series"){
                $field .= ", a.dlv_series_nm as dlv_series";
            } else if($val->name == "dlv_cd"){
                $field .= ", a.dlv_cd";
            } else if($val->name == "dlv_no"){
                $field .= ", a.dlv_no";
            } else if($val->name == "ord_type"){
                $field .= ", ord_type.code_val as ord_type";
            } else if($val->name == "ord_kind"){
                $field .= ", ord_kind.code_val as ord_kind";
            } else if($val->name == "ord_no"){
                $field .= ", a.ord_no";
            } else if($val->name == "ord_opt_no"){
                $field .= ", a.ord_opt_no";
            } else if($val->name == "ord_state"){
                $field .= ", ord_state.code_val ord_state";
            } else if($val->name == "pay_state"){
                $field .= ", pay_stat.code_val as pay_state";
            } else if($val->name == "clm_state"){
                $field .= ", clm_state.code_val clm_state";
            } else if($val->name == "goods_type"){
                $field .= ", ifnull(gt.code_val,'N/A') as goods_type";
            } else if($val->name == "style_no"){
                $field .= ", a.style_no";
            } else if($val->name == "goods_nm"){
                $field .= ", a.goods_nm";
            } else if($val->name == "goods_opt"){
                $field .= ", replace(a.goods_opt, '^', ' : ') as goods_opt";
            } else if($val->name == "sale_qty"){
                $field .= ", a.sale_qty";
            } else if($val->name == "qty"){
                $field .= ", a.qty";
            } else if($val->name == "price"){
                $field .= ", a.price";
            } else if($val->name == "sale_amt"){
                $field .= ", a.sale_amt";
            } else if($val->name == "dlv_amt"){
                $field .= ", a.dlv_amt";
            } else if($val->name == "pay_type"){
                $field .= ", pay_type.code_val pay_type";
            } else if($val->name == "r_nm"){
                $field .= ", a.r_nm";
            } else if($val->name == "r_zipcode"){
                $field .= ", a.r_zipcode";
            } else if($val->name == "r_addr"){
                $field .= ", a.r_addr";
            } else if($val->name == "r_phone"){
                $field .= ", a.r_phone";
            } else if($val->name == "r_mobile"){
                $field .= ", a.r_mobile";
            }  else if($val->name == "r_jumin"){
                $field .= ", a.r_jumin";
            } else if($val->name == "dlv_msg"){
                $field .= ", a.dlv_msg";
            } else if($val->name == "sale_place"){
                $field .= ", a.sale_place";
            } else if($val->name == "sale_place_addr"){
                $field .= ", a.sale_place_addr";
            } else if($val->name == "sale_place_phone"){
                $field .= ", a.sale_place_phone";
            } else if($val->name == "out_ord_no"){
                $field .= ", a.out_ord_no";
            } else if($val->name == "com_nm"){
                $field .= ", a.com_nm";
            } else if($val->name == "baesong_kind"){
                $field .= ", baesong_kind.code_val baesong_kind";
            } else if($val->name == "ord_date"){
                $field .= ", a.ord_date";
            } else if($val->name == "upd_dm"){
                $field .= ", date_format(a.upd_dm,'%Y-%m-%d %H:%i:%s')";
            } else if($val->name == "dlv_proc_date"){
                $field .= ", a.dlv_proc_date";
            } else if($val->name == "dlv_end_date"){
                $field .= ", a.dlv_end_date";
            } else if($val->name == "last_up_date"){
                $field .= ", a.last_up_date";
            } else if($val->name == "pay_date"){
                $field .= ", a.pay_date";
            } else if($val->name == "dlv_comment"){
                $field .= ", a.dlv_comment";
            } else if($val->name == "user_nm"){
                $field .= ", a.user_nm";
            } else if($val->name == "state"){
                $field .= ", a.state";
            } else if($val->name == "memo"){
                $field .= ", a.memo";
            } else if($val->name == "head_desc"){
                $field .= ", a.head_desc";
            }
        }

        $field .= ", a.ord_cnt";

        return $field;
    }
}
