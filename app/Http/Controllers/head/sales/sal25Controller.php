<?php

namespace App\Http\Controllers\head\sales;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;
use Carbon\Carbon;

class sal25Controller extends Controller
{
    // 일별 매출 통계
    public function index() {

        //return '일별매출통계';
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'week')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
            'items' => SLib::getItems()
        ];

        $sql = " select group_no as id, group_nm as val from user_group order by group_no ";
        $values['groups'] = DB::select($sql);

        return view( Config::get('shop.head.view') . '/sales/sal25',$values);
    }

    public function search(Request $request){

        $sdate = str_replace("-","",$request->input('sdate',Carbon::now()->sub(1, 'month')->format('Ymd')));
        $edate = str_replace("-","",$request->input('edate',date("Ymd")));

        $brand_cd = $request->input("brand_cd");
        $goods_nm = $request->input("goods_nm");
        $item	= $request->input("item");
        $user_group	= $request->input("user_group");
        $user_id	= $request->input("user_id");
        $user_nm	= $request->input("user_nm");

        $where = "";
        $where2 = "";

        if($user_id !=""){
            $ids = explode(",",$user_id);
            if(count($ids) > 1){
                if(count($ids) > 50) array_splice($ids,50);
                for($i=0;$i<count($ids);$i++){
                    $ids[$i] = sprintf("'%s'",$ids[$i]);
                }
                $in_ids = join(",",$ids);
                $where .= " and m.user_id in ( $in_ids ) ";
            } else {
                $where .= " and m.user_id = '$user_id' ";
            }
        }

        if ($goods_nm != "") $where .= " and g.goods_nm like '%$goods_nm%' ";
        if ($brand_cd != "") $where .= " and g.brand ='$brand_cd'";
        if ($item != "") $where .= " and g.opt_kind_cd = '$item' ";
        if ($user_nm !="")	$where2 .= " and m.name = '$user_nm' ";



        $join = "";

        if($user_group != ""){
            $join = " inner join user_group_member b on m.user_id = b.user_id ";
            $join .= " inner join user_group c on b.group_no = c.group_no and c.group_no = '$user_group' ";
        }

        $sql = /** @lang text */
            "
			select '' as chk, m.user_id,m.name,m.mobile,m.point,
					date_format(m.regdate,'%Y%m%d') as regdate,
					m.lastdate as lastdate, m.visit_cnt,
					a.ord_date, 
					( a.pay_opt_cnt - a.ref_opt_cnt - a.ret_opt_cnt ) as net_opt_cnt,
					( a.pay_amt - a.ref_amt - a.ret_amt ) as net_amt,
					a.pay_cnt, a.pay_opt_cnt, a.pay_amt,
					a.dlv_opt_cnt, a.dlv_amt,
					a.ref_opt_cnt, a.ref_amt, 
					a.ret_opt_cnt,a.ret_amt
			from member m $join inner join (
				select 
					a.user_id,
					max(a.ord_date) as ord_date,
					sum(if(a.ord_state = 10,cnt,0)) as pay_cnt,
					sum(if(a.ord_state = 10,opt_cnt,0)) as pay_opt_cnt,
					sum(if(a.ord_state = 10,amt,0)) as pay_amt,
					sum(if(a.ord_state = 30,opt_cnt,0)) as dlv_opt_cnt,
					sum(if(a.ord_state = 30,amt,0)) as dlv_amt,
					sum(if(a.ord_state = 61,opt_cnt,0)) as ref_opt_cnt,
					sum(if(a.ord_state = 61,amt,0)) as ref_amt,
					sum(if(a.ord_state = 60,opt_cnt,0)) as ret_opt_cnt,
					sum(if(a.ord_state = 60,amt,0)) as ret_amt
				from (
					select m.user_id,w.ord_state,
							max(m.ord_date) as ord_date,
							count(distinct m.ord_no) as cnt, 
							count(*) as opt_cnt, 
							sum(w.recv_amt+w.point_apply_amt+w.dlv_amt) as amt
					from order_mst m 
							inner join order_opt o on m.ord_no = o.ord_no
                            inner join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub							
							inner join order_opt_wonga w on o.ord_opt_no = w.ord_opt_no
					where m.ord_date >= '$sdate' and m.ord_date <= DATE_ADD($edate, INTERVAL 1 DAY) $where
					group by m.user_id,w.ord_state
				) a 
				group by a.user_id
			) a on m.user_id = a.user_id
			where 1=1 and m.user_id <> '' $where2
			order by net_amt desc            
        ";
            //echo "<pre>$sql</pre>";

        $rows = DB::select($sql);
        $result = $rows;

//        foreach($rows as $row) {
//            $row->qty_sale = $row->qty_10 + $row->qty_61 - $row->qty_60;
//            $row->price_sale = $row->price_10 + $row->price_61 - $row->price_60;
//        }
//       $result[] = $row;

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

}
