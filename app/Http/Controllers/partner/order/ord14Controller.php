<?php

namespace App\Http\Controllers\partner\order;

use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ord14Controller extends Controller
{
    //
    public function index()
    {
        $mutable        = Carbon::now();
        $sdate          = $mutable->sub(14, 'day')->format('Y-m-d');

        $com_id = Auth('partner')->user()->com_id;

        $values = [
            'com_id'            => $com_id,
            'sdate'             => $sdate,
            'edate'             => date("Y-m-d")
        ];

        return view(Config::get('shop.partner.view') . '/order/ord14', $values);
    }

    public function show(Request $request)
    {
        $number     = $request->input('number');
        $bkname     = $request->input('bkname');
        $bankda_id  = $request->input('bankda_id');
        $bankda_pwd = $request->input('bankda_pwd');
        $use_yn     = $request->input('use_yn');
        $rt         = $request->input('rt');
        $ut         = $request->input('ut');

        $where = "";

        if ($number != "")          $where .= " and a.number        = '"             . Lib::quote($number) . "' ";
        if ($bkname != "")          $where .= " and a.bkname        = '"             . Lib::quote($bkname) . "' ";
        if ($bankda_id != "")       $where .= " and a.bankda_id     = '"             . Lib::quote($bankda_id) . "' ";
        if ($bankda_pwd != "")      $where .= " and a.bankda_pwd    = '"             . Lib::quote($bankda_pwd) . "' ";
        if ($use_yn != "")          $where .= " and a.use_yn        = '"             . Lib::quote($use_yn) . "' ";
        if ($rt != "")              $where .= " and a.rt            = '"             . Lib::quote($rt) . "' ";
        if ($ut != "")              $where .= " and a.ut            = '"             . Lib::quote($ut) . "' ";

        $query=
        "
        select * from bankda_account
        ";

        $rows = DB::select($query);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);

        return view(Config::get('shop.partner.view') . '/order/ord14_show');
    }

    public function search(Request $request)
    {
        //컴퍼니 아이디
        $com_id = Auth('partner')->user()->com_id;

        //주문일자 검색일자, 검색 타입
        $date_type = $request->input('date_type');
        $sdate = $request->input('sdate', Carbon::now()->sub(7, 'day')->format('Ymd'));
        $edate = $request->input('edate', date("Ymd"));

        //입금은행
        $bkname = $request->input('bkname');

        //계좌번호
        $number = $request->input('number');

        //입금자
        $bkjukyo = $request->input('bkjukyo');

        //보류여부
        $is_hold = $request->input('is_hold');

        //입금내역수집일시
        $rt = $request->input('rt');

        //입금확인여부
        $is_matched = $request->input('is_matched');

        //입금액
        $bkinput = $request->input('bkinput');

        //주문번호
        $ord_no = $request->input('ord_no');

        //복수주문번호
        $ord_nos = $request->input('ord_nos');

        //예상주문번호
        $expect_ord_no = $request->input('expect_ord_no');

        //주문자
        $user_nm = $request->input('user_nm');

        //수령자
        $r_nm = $request->input('r_nm');

        //판매처
        $sale_price = $request->input('sale_price');

        //페이징 처리
        $page = $request->input('page', 1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit', 100);

        $where = "";

        if ($bkname != "")          $where .= " and d.bkname        = '"             . Lib::quote($bkname)        . "' ";
        if ($number != "")          $where .= " and d.number        = '"             . Lib::quote($number)        . "' ";
        if ($bkjukyo != "")         $where .= " and a.bkjukyo       = '"             . Lib::quote($bkjukyo)       . "' ";
        if ($is_hold != "")         $where .= " and a.is_hold       = '"             . Lib::quote($is_hold)       . "' ";
        if ($rt != "")              $where .= " and d.rt            = '"             . Lib::quote($rt)            . "' ";
        if ($is_matched != "")      $where .= " and a.is_matched    = '"             . Lib::quote($is_matched)    . "' ";
        if ($bkinput != "")         $where .= " and a.bkinput       = '"             . Lib::quote($bkinput)       . "' ";
        if ($ord_no != "")          $where .= " and a.ord_no        = '"             . Lib::quote($ord_no)        . "' ";
        if ($ord_nos != "")         $where .= " and a.ord_nos       = '"             . Lib::quote($ord_nos)       . "' ";
        if ($expect_ord_no != "")   $where .= " and a.expect_ord_no = '"             . Lib::quote($expect_ord_no) . "' ";
        if ($user_nm != "")         $where .= " and b.user_nm       = '"             . Lib::quote($user_nm)       . "' ";
        if ($r_nm != "")            $where .= " and b.r_nm          = '"             . Lib::quote($r_nm)          . "' ";
        if ($sale_price != "")      $where .= " and e.sale_price    = '"             . Lib::quote($sale_price)    . "' ";

        $is_not_use_date = $this->get_is_not_use_date($request);

        if ($is_not_use_date == false) {
            $str_date = ' AND ord_date '; 
            if ($date_type != '') {
                //당월
                $now_month = " LAST_DAY(CURDATE() - INTERVAL 1 month) + INTERVAL 1 DAY ";
                //전월
                $prev_month = " LAST_DAY(CURDATE() - INTERVAL 2 month) + INTERVAL 1 DAY ";

                switch ($date_type) {
                    // 당일
                    case 1 :
                        $where .= " $str_date = CURDATE() ";
                        break;
                    // 어제
                    case 2 :
                        $where .= " $str_date = CURDATE()-INTERVAL 1 DAY ";
                        break;
                    // 최근 1주(당일기준)
                    case 3 :
                        $where .= " $str_date  BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE() ";
                        break;
                    // 최근 2주(당일기준)
                    case 4 :
                        $where .= " $str_date  BETWEEN CURDATE()-INTERVAL 2 WEEK AND CURDATE() ";
                        break;
                    // 최근 1달(당일기준)
                    case 5 :
                        $where .= " $str_date  BETWEEN CURDATE()-INTERVAL 1 MONTH AND CURDATE() ";
                        break;
                    // 금월
                    case 6 :
                        $where .= " $str_date  >= $now_month";
                        break;
                    // 전월
                    case 7 :
                        $where .= " $str_date BETWEEN $prev_month AND $now_month ";
                        break;
                    //그외
                    default :
                        break;
                }
            } else {
                $where .= " $str_date BETWEEN '$sdate' AND DATE_ADD('$edate', INTERVAL 1 DAY) ";
            }
        }

        $page_size = $limit;
        $startno = ($page - 1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        if($page == 1){
            $query = /** @lang text */
            "
            select
                count(*) as total
            from bankda_record a
                left outer join order_mst b on a.ord_no = b.ord_no
                left outer join payment c on a.ord_no = c.ord_no
                left outer join bankda_log d on a.log_no = d.no
                left outer join company e on b.sale_place = e.com_id and e.com_type = '4'
            where 1=1 $where
            order by a.no
			";
            //echo "<pre>$query $com_id</pre>";
            $row = DB::select($query,["com_id" => $com_id]);
            //$row = DB::select($query);
            $total = $row[0]->total;
            if($total > 0){
                $page_cnt=(int)(($total-1)/$page_size) + 1;
            }
        }

        $query =
        "
        select
            '' as chk, date_format(a.bkdate, '%Y.%m.%d') as bkdate, d.bkname, d.number, a.bkjukyo, a.bkinput, concat(a.bkcontent,'/',a.bketc) as bkinfo, a.memo
            , a.is_matched, a.is_hold, d.rt, a.matched_dt
            , ifnull(a.ord_no, '선택') as ord_no
            , ifnull(a.ord_nos, '') as ord_nos
            , if(a.ord_no = '' || a.is_matched = 'Y' || a.is_hold = 'Y', '', ifnull(a.expect_ord_no, '')) as expect_ord_no
            , ord_state.code_val as ord_state, pay_type.code_val as pay_type, if(a.is_hold = 'Y', '입금보류', pay_stat.code_val) as pay_stat
            , b.ord_amt, b.point_amt, b.coupon_amt, b.dc_amt
            , b.phone, b.mobile, CONCAT(b.user_nm,'(',b.user_id,')') as user_nm, b.r_nm
            , e.com_nm as sale_price, a.admin_name
            , a.bkdate as h_bkdate, a.no
        from bankda_record a
            left outer join order_mst b on a.ord_no = b.ord_no
            left outer join payment c on a.ord_no = c.ord_no
            left outer join bankda_log d on a.log_no = d.no
            left outer join company e on b.sale_place = e.com_id and e.com_type = '4'
            left outer join code ord_state on (b.ord_state = ord_state.code_id and ord_state.code_kind_cd = 'G_ORD_STATE')
            left outer join code pay_type on (c.pay_type = pay_type.code_id and pay_type.code_kind_cd = 'G_PAY_TYPE')
            left outer join code pay_stat on (c.pay_stat = pay_stat.code_id and pay_stat.code_kind_cd = 'G_PAY_STAT')
        where 1=1 $where
        order by a.no
        $limit
        ";
        //echo "<pre>$query</pre>";
        // dd($query);

        $rows = DB::select($query,["com_id" => $com_id]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    private function get_is_not_use_date(Request $request)
    {
        if ($request->ord_no != "") {
            return true;
        }

        if ($request->user_id != "") {
            return true;
        }

        if ($request->user_nm != "") {
            return true;
        }

        if (strlen($request->r_nm) >= 4) {
            return true;
        }

        return false;
    }

}
