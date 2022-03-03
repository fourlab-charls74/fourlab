<?php

namespace App\Http\Controllers\head\member;

use App\Components\SLib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Conf;
use App\Models\SMS;
use Illuminate\Support\Facades\Mail;

class mem21Controller extends Controller
{
    public function index($type='', Request $req) {
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $conf = new Conf();
        $show_yn_items  = SLib::getCodes("G_SHOW_YN");
        $qa_types       = SLib::getCodes("G_GOODS_QA_TYPE");
        $cfg_sms        = $conf->getConfig("sms");
        $cfg_sms_yn     = $conf->getValue($cfg_sms,"sms_yn");
        $cfg_email        = $conf->getConfig("email");
        $cfg_email_yn   = $conf->getValue($cfg_email,"goods_qa_yn");
        $cfg_domain			= "http://" . $conf->getConfig("shop","domain");

        $query = "
            select
                opt_kind_cd id,
                case when 'kor' = 'kor' then
                    concat('(',ifnull(opt_kind_cd, ''),') ',ifnull(opt_kind_nm, ''))
                else
                    opt_kind_cd
                end as val
            from opt where opt_id = 'K' and use_yn = 'Y'
            order by  use_yn desc,opt_kind_nm 
        ";

        $opt_kind_cd_items = DB::select($query);
        
        $edate = $req->input('date', date("Y-m-d"));

        $sdate = date('Y-m-d', strtotime(-7 .'days'));
        $sdate = $req->input('date', $sdate);
        $user_id = $req->input('user_id', '');

        $values = [
            "admin_id" =>$id,
            "admin_nm" => $name,
            "edate" => $edate,
            "sdate" => $sdate,
            "show_yn_items" => $show_yn_items,
            "qa_types" => $qa_types,
            "opt_kind_cd_items" => $opt_kind_cd_items,
            "sms_yn" => $cfg_sms_yn,
            "email_yn" => $cfg_email_yn,
            'layout' => $type ? 'head_skote.layouts.master-without-nav' : 'head_skote.layouts.app',
            'user_id' => $user_id,
            'domain'    => $cfg_domain
        ];
        return view( Config::get('shop.head.view') . '/member/mem21',$values);
    }

    public function search(Request $request){
        $type			= $request->input("type");
		$edate		    = $request->input("edate");
		$sdate		    = $request->input("sdate");
		$style_no		= $request->input("style_no");
		$show_yn	    = $request->input("show_yn");
		$goods_no		= $request->input("goods_no");
		$goods_sub	    = $request->input("goods_sub");
		$goods_nm		= $request->input("goods_nm");
		$kind			= $request->input("kind");
		$qry			= $request->input("qry");
		$answer_yn	    = $request->input("answer_yn");
        $opt_kind_cd	= $request->input("opt_kind_cd");
        $page           = $request->input("page",1);
        
        $edate = $edate." 23:59:59";
        $sdate = $sdate." 00:00:00";

		$where = "";
		if ($type != "")			$where .= " and a.type = '$type' ";
		if ($sdate != "")			$where .= " and a.q_date >= '$sdate' ";
		if ($edate != "")			$where .= " and a.q_date < '$edate' ";
		if ($style_no != "")		$where .= " and b.style_no like '$style_no%' ";
		if ($show_yn != "")		$where .= " and a.show_yn = '$show_yn' ";
		if ($goods_no != "")		$where .= " and a.goods_no = '$goods_no' ";
		if ($goods_sub != "")		$where .= " and a.goods_sub = '$goods_sub' ";
		if ($goods_nm != "")		$where .= " and b.goods_nm like '%$goods_nm%' ";
		if ($answer_yn != "")		$where .= " and a.answer_yn = '$answer_yn' ";
		if($opt_kind_cd != "")	$where .= " and b.opt_kind_cd = '$opt_kind_cd' ";
		if($qry !="") 			$where .= " and $kind like '$qry%' ";

        
		if ($page < 1 or $page == "") $page = 1;
		$page_size = 100;
		$query = "
            select count(*) as cnt from goods_qa_new a
            left outer join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
            where 1=1 $where
        ";
        //echo $query;
        $row = DB::select($query);
        $total = $row[0]->cnt;

        // 페이지 얻기
        $page_cnt=(int)(($total-1)/$page_size) + 1;

        if($page == 1){
            $startno = ($page-1) * $page_size;
        } else {
            $startno = ($page-1) * $page_size;
        }

        $arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page);

        $query = "
        select
            a.show_yn, a.goods_no, a.goods_sub, b.goods_nm, b.style_no, d.code_val as type,
            concat(a.subject, if(a.comment_cnt > 0, concat(' (',a.comment_cnt,')'), ''))  as subject,
            concat(a.user_nm, '(', a.user_id ,')' ) as user_info, a.user_id, date_format(a.q_date,'%y.%m.%d %H:%i:%s') as q_date,
            c.code_val, a.no, a.answer_yn
        from goods_qa_new a
            inner join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
            left outer join code c on c.code_kind_cd = 'G_ANS_YN' and c.code_id = a.answer_yn
            left outer join code d on d.code_kind_cd = 'G_GOODS_QA_TYPE' and d.code_id = a.type
        where 1=1 $where
        order by
            no desc
        limit $startno,$page_size
        ";

        //echo $query;
        $result = DB::select($query);
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

    public function show($qa_no = ''){
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;
        $ipadress		= $this->createAddress();
        
        $conf = new Conf();
        $cfg_domain_img	= $conf->getConfigValue("shop","domain");
		$goods_img_url = (strpos($cfg_domain_img, 'http://') !== false) ? $cfg_domain_img : sprintf("http://%s", $cfg_domain_img);
        $cfg_img_size_detail = SLib::getCodesValue("G_IMG_SIZE","detail");
        $cfg_img_size_real = SLib::getCodesValue("G_IMG_SIZE", "real");

        $query = "
        select
            a.no as no,b.goods_nm, '$ipadress' as input_ipaddres, 'editcmd' as cmd,
            a.goods_no,a.goods_sub, a.user_nm, a.user_id, m.email, m.mobile, a.subject ,a.question, c.code_val as show_yn,
            a.q_date, a.ip,
            if(a.answer_yn ='Y',a.admin_id,'$id') as admin_id,
            if(a.answer_yn = 'Y',a.admin_nm,'$name') as admin_nm,
            concat('$goods_img_url',replace(ifnull(b.img, ''),'$cfg_img_size_real', '$cfg_img_size_detail')) as goods_img,
            a.check_nm, a.check_id, a.answer_yn, a.answer, a.a_date,
            d.code_val as sale_stat_cl,
            ifnull((
                select sum(good_qty) from goods_summary
                where goods_no = b.goods_no and goods_sub = b.goods_sub and good_qty > 0
            ),0) as good_qty,
            ifnull((
                select sum(wqty) from goods_summary
                where goods_no = b.goods_no and goods_sub = b.goods_sub and wqty > 0
            ),0) as wqty,
            e.code_val as type, a.goods_opt, a.user_sex, a.user_height, a.user_weight,
            a.user_top, a.user_bottom, a.user_shoes, f.code_val as user_body, a.user_etc_ment,
            a.comment_cnt, '' as answer_type
        from goods_qa_new a
            inner join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
            inner join member m on a.user_id = m.user_id
            inner join code c on c.code_kind_cd = 'G_SHOW_YN' and c.code_id = a.show_yn
            left join code d on d.code_kind_cd = 'G_GOODS_STAT' and d.code_id = b.sale_stat_cl
            left join code e on e.code_kind_cd = 'G_GOODS_QA_TYPE' and e.code_id = a.type
            left join code f on f.code_kind_cd = 'CS_SIZE_TYPE' and f.code_id = a.user_body
        where
            a.no = '$qa_no'
        ";
        
        //echo $query;
        $result = DB::select($query);
        return response()->json([
            "code" => 200,
            "body" => $result
        ]);
    }

    public function createAddress(){
        $ip[] = array("start"=>"210.181.1.xxx","end"=>"210.181.31.xxx");
        $ip[] = array("start"=>"211.39.224.xxx","end"=>"211.39.255.xxx");
        $ip[] = array("start"=>"211.45.64.xxx","end"=>"211.45.95.xxx");
        $ip[] = array("start"=>"211.56.128.xxx","end"=>"211.56.191.xxx");
        $ip[] = array("start"=>"211.61.128.xxx","end"=>"211.61.255.xxx");
        $ip[] = array("start"=>"211.111.0.xxx","end"=>"211.111.127.xxx");
        $ip[] = array("start"=>"211.175.0.xxx","end"=>"211.175.255.xxx");
        $ip[] = array("start"=>"211.183.0.xxx","end"=>"211.183.255.xxx");
        $ip[] = array("start"=>"211.242.0.xxx","end"=>"211.242.63.xxx");
        $ip[] = array("start"=>"211.242.64.xxx","end"=>"211.242.127.xxx");
        $ip[] = array("start"=>"211.242.128.xxx","end"=>"211.242.191.xxx");
        $ip[] = array("start"=>"61.96.0.xxx","end"=>"61.96.127.xxx");
        $ip[] = array("start"=>"211.249.0.xxx","end"=>"211.249.255.xxx");
        $ip[] = array("start"=>"61.96.128.xxx","end"=>"61.96.255.xxx");
        $ip[] = array("start"=>"61.103.0.xxx","end"=>"61.103.255.xxx");
        $ip[] = array("start"=>"61.107.0.xxx","end"=>"61.107.255.xxx");
        $ip[] = array("start"=>"220.64.0.xxx","end"=>"220.64.255.xxx");
        $ip[] = array("start"=>"211.242.192.xxx","end"=>"211.242.255.xxx");
        $ip[] = array("start"=>"211.247.128.xxx","end"=>"211.247.255.xxx");
        $ip[] = array("start"=>"59.150.0.xxx","end"=>"59.150.255.xxx");
        $ip[] = array("start"=>"210.181.0.xxx","end"=>"210.181.3.xxx");
        $ip[] = array("start"=>"210.181.4.xxx","end"=>"210.181.11.xxx");
        $ip[] = array("start"=>"210.181.12.xxx","end"=>"210.181.31.xxx");
        $ip[] = array("start"=>"211.39.224.xxx","end"=>"211.39.255.xxx");
        $ip[] = array("start"=>"211.45.64.xxx","end"=>"211.45.95.xxx");
        $ip[] = array("start"=>"211.56.128.xxx","end"=>"211.56.191.xxx");
        $ip[] = array("start"=>"211.61.128.xxx","end"=>"211.61.255.xxx");
        $ip[] = array("start"=>"211.111.0.xxx","end"=>"211.111.127.xxx");
        $ip[] = array("start"=>"211.175.0.xxx","end"=>"211.175.255.xxx");
        $ip[] = array("start"=>"211.183.0.xxx","end"=>"211.183.255.xxx");
        $ip[] = array("start"=>"211.242.0.xxx","end"=>"211.242.63.xxx");
        $ip[] = array("start"=>"211.242.64.xxx","end"=>"211.242.127.xxx");
        $ip[] = array("start"=>"211.242.128.xxx","end"=>"211.242.191.xxx");
        $ip[] = array("start"=>"61.96.0.xxx","end"=>"61.96.127.xxx");
        $ip[] = array("start"=>"211.249.0.xxx","end"=>"211.249.255.xxx");
        $ip[] = array("start"=>"61.96.128.xxx","end"=>"61.96.255.xxx");
        $ip[] = array("start"=>"61.103.0.xxx","end"=>"61.103.255.xxx");
        $ip[] = array("start"=>"61.107.0.xxx","end"=>"61.107.255.xxx");
        $ip[] = array("start"=>"220.64.0.xxx","end"=>"220.64.255.xxx");
        $ip[] = array("start"=>"211.242.192.xxx","end"=>"211.242.255.xxx");
        $ip[] = array("start"=>"211.247.128.xxx","end"=>"211.247.255.xxx");
        $ip[] = array("start"=>"59.150.0.xxx","end"=>"59.150.255.xxx");
        $ip[] = array("start"=>"211.115.192.xxx","end"=>"211.115.207.xxx");
        $ip[] = array("start"=>"211.115.208.xxx","end"=>"211.115.211.xxx");
        $ip[] = array("start"=>"211.115.212.xxx","end"=>"211.115.223.xxx");
        $ip[] = array("start"=>"211.189.160.xxx","end"=>"211.189.191.xxx");
        $ip[] = array("start"=>"211.239.0.xxx","end"=>"211.239.127.xxx");
        $ip[] = array("start"=>"211.239.128.xxx","end"=>"211.239.191.xxx");
        $ip[] = array("start"=>"61.100.0.xxx","end"=>"61.100.191.xxx");
        $ip[] = array("start"=>"211.239.192.xxx","end"=>"211.239.255.xxx");
        $ip[] = array("start"=>"61.250.64.xxx","end"=>"61.250.95.xxx");
        $ip[] = array("start"=>"61.250.96.xxx","end"=>"61.250.127.xxx");
        $ip[] = array("start"=>"211.236.224.xxx","end"=>"211.236.255.xxx");
        $ip[] = array("start"=>"61.97.64.xxx","end"=>"61.97.79.xxx");
        $ip[] = array("start"=>"61.252.160.xxx","end"=>"61.252.191.xxx");
        $ip[] = array("start"=>"210.98.64.xxx","end"=>"210.98.127.xxx");
        $ip[] = array("start"=>"210.112.0.xxx","end"=>"210.112.127.xxx");
        $ip[] = array("start"=>"210.116.0.xxx","end"=>"210.116.63.xxx");
        $ip[] = array("start"=>"210.116.128.xxx","end"=>"210.116.255.xxx");
        $ip[] = array("start"=>"210.121.0.xxx","end"=>"210.121.127.xxx");
        $ip[] = array("start"=>"210.127.128.xxx","end"=>"210.127.191.xxx");
        $ip[] = array("start"=>"210.127.64.xxx","end"=>"210.127.127.xxx");
        $ip[] = array("start"=>"210.126.128.xxx","end"=>"210.126.255.xxx");
        $ip[] = array("start"=>"210.122.0.xxx","end"=>"210.122.255.xxx");
        $ip[] = array("start"=>"210.109.0.xxx","end"=>"210.109.255.xxx");
        $ip[] = array("start"=>"210.103.128.xxx","end"=>"210.103.255.xxx");
        $ip[] = array("start"=>"203.255.128.xxx","end"=>"203.255.159.xxx");
        $ip[] = array("start"=>"203.255.112.xxx","end"=>"203.255.119.xxx");
        $ip[] = array("start"=>"203.248.0.xxx","end"=>"203.248.127.xxx");
        $ip[] = array("start"=>"203.243.128.xxx","end"=>"203.243.255.xxx");
        $ip[] = array("start"=>"211.116.192.xxx","end"=>"211.116.223.xxx");
        $ip[] = array("start"=>"211.43.160.xxx","end"=>"211.43.191.xxx");
        $ip[] = array("start"=>"203.239.0.xxx","end"=>"203.239.127.xxx");
        $ip[] = array("start"=>"203.238.0.xxx","end"=>"203.238.127.xxx");
        $ip[] = array("start"=>"203.236.128.xxx","end"=>"203.236.191.xxx");
        $ip[] = array("start"=>"203.235.0.xxx","end"=>"203.235.127.xxx");
        $ip[] = array("start"=>"203.231.0.xxx","end"=>"203.231.255.xxx");
        $ip[] = array("start"=>"203.227.0.xxx","end"=>"203.227.255.xxx");
        $ip[] = array("start"=>"61.97.96.xxx","end"=>"61.97.111.xxx");
        $ip[] = array("start"=>"211.36.192.xxx","end"=>"211.36.223.xxx");
        $ip[] = array("start"=>"211.39.160.xxx","end"=>"211.39.191.xxx");
        $ip[] = array("start"=>"211.56.192.xxx","end"=>"211.56.223.xxx");
        $ip[] = array("start"=>"211.111.128.xxx","end"=>"211.111.159.xxx");
        $ip[] = array("start"=>"211.116.176.xxx","end"=>"211.116.191.xxx");
        $ip[] = array("start"=>"211.238.0.xxx","end"=>"211.238.15.xxx");
        $ip[] = array("start"=>"211.255.224.xxx","end"=>"211.255.255.xxx");
        $ip[] = array("start"=>"203.235.128.xxx","end"=>"203.235.191.xxx");
        $ip[] = array("start"=>"61.109.128.xxx","end"=>"61.109.255.xxx");
        $ip[] = array("start"=>"210.94.0.xxx","end"=>"210.94.3.xxx");
        $ip[] = array("start"=>"210.94.4.xxx","end"=>"210.94.11.xxx");
        $ip[] = array("start"=>"210.180.96.xxx","end"=>"210.180.107.xxx");
        $ip[] = array("start"=>"210.94.12.xxx","end"=>"210.94.31.xxx");
        $ip[] = array("start"=>"210.180.108.xxx","end"=>"210.180.127.xxx");
        $ip[] = array("start"=>"210.217.160.xxx","end"=>"210.217.191.xxx");
        $ip[] = array("start"=>"210.220.64.xxx","end"=>"210.220.95.xxx");
        $ip[] = array("start"=>"210.220.160.xxx","end"=>"210.220.191.xxx");
        $ip[] = array("start"=>"210.205.0.xxx","end"=>"210.205.63.xxx");
        $ip[] = array("start"=>"211.37.0.xxx","end"=>"211.37.127.xxx");
        $ip[] = array("start"=>"211.41.96.xxx","end"=>"211.41.127.xxx");
        $ip[] = array("start"=>"211.44.0.xxx","end"=>"211.44.127.xxx");
        $ip[] = array("start"=>"211.44.128.xxx","end"=>"211.44.255.xxx");
        $ip[] = array("start"=>"211.58.0.xxx","end"=>"211.58.255.xxx");
        $ip[] = array("start"=>"211.108.0.xxx","end"=>"211.108.255.xxx");
        $ip[] = array("start"=>"211.117.0.xxx","end"=>"211.117.255.xxx");
        $ip[] = array("start"=>"211.176.0.xxx","end"=>"211.177.255.xxx");
        $ip[] = array("start"=>"211.178.0.xxx","end"=>"211.179.255.xxx");
        $ip[] = array("start"=>"211.200.0.xxx","end"=>"211.205.255.xxx");
        $ip[] = array("start"=>"211.206.0.xxx","end"=>"211.211.255.xxx");
        $ip[] = array("start"=>"211.212.0.xxx","end"=>"211.215.255.xxx");
        $ip[] = array("start"=>"218.48.0.xxx","end"=>"218.49.255.xxx");
        $ip[] = array("start"=>"218.50.0.xxx","end"=>"218.55.255.xxx");
        $ip[] = array("start"=>"218.232.0.xxx","end"=>"218.233.255.xxx");
        $ip[] = array("start"=>"219.240.0.xxx","end"=>"219.241.255.xxx");
        $ip[] = array("start"=>"218.234.0.xxx","end"=>"218.235.255.xxx");
        $ip[] = array("start"=>"218.236.0.xxx","end"=>"218.239.255.xxx");
        $ip[] = array("start"=>"218.38.0.xxx","end"=>"218.39.255.xxx");
        $ip[] = array("start"=>"219.248.0.xxx","end"=>"219.251.255.xxx");
        $ip[] = array("start"=>"221.138.0.xxx","end"=>"221.143.255.xxx");
        $ip[] = array("start"=>"219.254.0.xxx","end"=>"219.255.255.xxx");
        $ip[] = array("start"=>"222.232.0.xxx","end"=>"222.239.255.xxx");
        $ip[] = array("start"=>"203.251.192.xxx","end"=>"203.251.255.xxx");
        $ip[] = array("start"=>"203.240.128.xxx","end"=>"203.240.255.xxx");
        $ip[] = array("start"=>"203.228.128.xxx","end"=>"203.228.255.xxx");
        $ip[] = array("start"=>"210.127.192.xxx","end"=>"210.127.255.xxx");
        $ip[] = array("start"=>"210.118.128.xxx","end"=>"210.118.255.xxx");
        $ip[] = array("start"=>"210.114.128.xxx","end"=>"210.114.255.xxx");
        $ip[] = array("start"=>"210.111.0.xxx","end"=>"210.111.127.xxx");
        $ip[] = array("start"=>"210.101.0.xxx","end"=>"210.101.63.xxx");
        $ip[] = array("start"=>"202.30.128.xxx","end"=>"202.30.255.xxx");
        $ip[] = array("start"=>"211.61.64.xxx","end"=>"211.61.127.xxx");
        $ip[] = array("start"=>"211.113.128.xxx","end"=>"211.113.255.xxx");
        $ip[] = array("start"=>"211.190.0.xxx","end"=>"211.191.255.xxx");
        $ip[] = array("start"=>"61.248.0.xxx","end"=>"61.248.255.xxx");
        $ip[] = array("start"=>"61.110.128.xxx","end"=>"61.111.255.xxx");
        $ip[] = array("start"=>"61.249.0.xxx","end"=>"61.249.255.xxx");
        $ip[] = array("start"=>"61.110.0.xxx","end"=>"61.110.127.xxx");
        $ip[] = array("start"=>"210.117.0.xxx","end"=>"210.117.63.xxx");
        $ip[] = array("start"=>"210.117.64.xxx","end"=>"210.117.127.xxx");
        $ip[] = array("start"=>"210.94.64.xxx","end"=>"210.94.95.xxx");
        $ip[] = array("start"=>"210.94.96.xxx","end"=>"210.94.127.xxx");
        $ip[] = array("start"=>"210.181.96.xxx","end"=>"210.181.127.xxx");
        $ip[] = array("start"=>"210.181.64.xxx","end"=>"210.181.95.xxx");
        $ip[] = array("start"=>"210.219.128.xxx","end"=>"210.219.191.xxx");
        $ip[] = array("start"=>"210.218.128.xxx","end"=>"210.218.191.xxx");
        $ip[] = array("start"=>"210.221.0.xxx","end"=>"210.221.127.xxx");
        $ip[] = array("start"=>"210.205.128.xxx","end"=>"210.205.255.xxx");
        $ip[] = array("start"=>"211.33.0.xxx","end"=>"211.33.127.xxx");
        $ip[] = array("start"=>"211.49.0.xxx","end"=>"211.49.127.xxx");
        $ip[] = array("start"=>"211.49.128.xxx","end"=>"211.49.255.xxx");
        $ip[] = array("start"=>"211.52.128.xxx","end"=>"211.52.255.xxx");
        $ip[] = array("start"=>"211.59.0.xxx","end"=>"211.59.255.xxx");
        $ip[] = array("start"=>"211.110.0.xxx","end"=>"211.110.255.xxx");
        $ip[] = array("start"=>"211.109.0.xxx","end"=>"211.109.255.xxx");
        $ip[] = array("start"=>"211.187.0.xxx","end"=>"211.187.255.xxx");
        $ip[] = array("start"=>"211.186.0.xxx","end"=>"211.186.255.xxx");
        $ip[] = array("start"=>"211.244.0.xxx","end"=>"211.244.255.xxx");
        $ip[] = array("start"=>"211.245.0.xxx","end"=>"211.245.127.xxx");
        $ip[] = array("start"=>"211.243.0.xxx","end"=>"211.243.255.xxx");
        $ip[] = array("start"=>"211.245.128.xxx","end"=>"211.245.255.xxx");
        $ip[] = array("start"=>"61.254.0.xxx","end"=>"61.255.255.xxx");
        $ip[] = array("start"=>"61.98.0.xxx","end"=>"61.98.255.xxx");
        $ip[] = array("start"=>"61.99.0.xxx","end"=>"61.99.255.xxx");
        $ip[] = array("start"=>"61.101.0.xxx","end"=>"61.101.127.xxx");
        $ip[] = array("start"=>"61.101.128.xxx","end"=>"61.101.223.xxx");
        $ip[] = array("start"=>"203.245.0.xxx","end"=>"203.245.15.xxx");
        $ip[] = array("start"=>"203.245.16.xxx","end"=>"203.245.31.xxx");
        $ip[] = array("start"=>"203.245.32.xxx","end"=>"203.245.63.xxx");
        $ip[] = array("start"=>"210.114.0.xxx","end"=>"210.114.63.xxx");
        $ip[] = array("start"=>"210.180.64.xxx","end"=>"210.180.95.xxx");
        $ip[] = array("start"=>"210.220.128.xxx","end"=>"210.220.159.xxx");
        $ip[] = array("start"=>"211.39.192.xxx","end"=>"211.39.223.xxx");
        $ip[] = array("start"=>"211.37.128.xxx","end"=>"211.37.191.xxx");
        $ip[] = array("start"=>"211.41.64.xxx","end"=>"211.41.95.xxx");
        $ip[] = array("start"=>"211.45.128.xxx","end"=>"211.45.191.xxx");
        $ip[] = array("start"=>"211.47.0.xxx","end"=>"211.47.63.xxx");
        $ip[] = array("start"=>"211.42.128.xxx","end"=>"211.42.159.xxx");
        $ip[] = array("start"=>"211.56.64.xxx","end"=>"211.56.127.xxx");
        $ip[] = array("start"=>"211.56.0.xxx","end"=>"211.56.63.xxx");
        $ip[] = array("start"=>"211.62.0.xxx","end"=>"211.62.63.xxx");
        $ip[] = array("start"=>"211.113.0.xxx","end"=>"211.113.127.xxx");
        $ip[] = array("start"=>"211.188.0.xxx","end"=>"211.188.127.xxx");
        $ip[] = array("start"=>"211.41.128.xxx","end"=>"211.41.131.xxx");
        $ip[] = array("start"=>"211.41.132.xxx","end"=>"211.41.135.xxx");
        $ip[] = array("start"=>"211.41.136.xxx","end"=>"211.41.143.xxx");
        $ip[] = array("start"=>"211.41.144.xxx","end"=>"211.41.159.xxx");
        $ip[] = array("start"=>"211.189.224.xxx","end"=>"211.189.255.xxx");
        $ip[] = array("start"=>"211.237.96.xxx","end"=>"211.237.111.xxx");
        $ip[] = array("start"=>"61.251.224.xxx","end"=>"61.251.255.xxx");
        $ip[] = array("start"=>"61.102.128.xxx","end"=>"61.102.223.xxx");
        $ip[] = array("start"=>"61.102.224.xxx","end"=>"61.102.255.xxx");
        $ip[] = array("start"=>"61.251.192.xxx","end"=>"61.251.223.xxx");
        $ip[] = array("start"=>"203.229.64.xxx","end"=>"203.229.127.xxx");
        $ip[] = array("start"=>"203.229.0.xxx","end"=>"203.229.63.xxx");
        $ip[] = array("start"=>"61.97.32.xxx","end"=>"61.97.47.xxx");
        $ip[] = array("start"=>"61.97.48.xxx","end"=>"61.97.63.xxx");
        $ip[] = array("start"=>"211.172.144.xxx","end"=>"211.172.159.xxx");
        $ip[] = array("start"=>"211.36.128.xxx","end"=>"211.36.159.xxx");
        $ip[] = array("start"=>"211.232.192.xxx","end"=>"211.232.239.xxx");
        $ip[] = array("start"=>"211.116.64.xxx","end"=>"211.116.127.xxx");
        $ip[] = array("start"=>"211.232.240.xxx","end"=>"211.232.255.xxx");
        $ip[] = array("start"=>"211.255.208.xxx","end"=>"211.255.223.xxx");
        $ip[] = array("start"=>"211.237.160.xxx","end"=>"211.237.191.xxx");
        $ip[] = array("start"=>"211.36.160.xxx","end"=>"211.36.163.xxx");
        $ip[] = array("start"=>"211.36.164.xxx","end"=>"211.36.171.xxx");
        $ip[] = array("start"=>"211.36.172.xxx","end"=>"211.36.175.xxx");
        $ip[] = array("start"=>"211.36.176.xxx","end"=>"211.36.183.xxx");
        $ip[] = array("start"=>"211.36.184.xxx","end"=>"211.36.191.xxx");
        $ip[] = array("start"=>"211.237.224.xxx","end"=>"211.237.239.xxx");
        $ip[] = array("start"=>"211.237.112.xxx","end"=>"211.237.127.xxx");
        $ip[] = array("start"=>"61.252.96.xxx","end"=>"61.252.111.xxx");
        $ip[] = array("start"=>"61.252.112.xxx","end"=>"61.252.127.xxx");
        $ip[] = array("start"=>"211.172.0.xxx","end"=>"211.172.31.xxx");
        $ip[] = array("start"=>"211.255.160.xxx","end"=>"211.255.191.xxx");
        $ip[] = array("start"=>"210.106.192.xxx","end"=>"210.106.223.xxx");
        $ip[] = array("start"=>"61.97.224.xxx","end"=>"61.97.239.xxx");
        $ip[] = array("start"=>"211.111.224.xxx","end"=>"211.111.255.xxx");
        $ip[] = array("start"=>"211.112.96.xxx","end"=>"211.112.127.xxx");
        $ip[] = array("start"=>"210.97.160.xxx","end"=>"210.97.191.xxx");
        $ip[] = array("start"=>"203.81.128.xxx","end"=>"203.81.159.xxx");
        $ip[] = array("start"=>"61.97.192.xxx","end"=>"61.97.207.xxx");
        $ip[] = array("start"=>"61.102.0.xxx","end"=>"61.102.95.xxx");
        $ip[] = array("start"=>"210.106.32.xxx","end"=>"210.106.63.xxx");
        $ip[] = array("start"=>"61.97.208.xxx","end"=>"61.97.223.xxx");
        $ip[] = array("start"=>"61.102.96.xxx","end"=>"61.102.127.xxx");
        $ip[] = array("start"=>"61.106.80.xxx","end"=>"61.106.127.xxx");
        $ip[] = array("start"=>"210.106.0.xxx","end"=>"210.106.31.xxx");
        $ip[] = array("start"=>"218.37.128.xxx","end"=>"218.37.191.xxx");
        $ip[] = array("start"=>"211.235.32.xxx","end"=>"211.235.63.xxx");
        $ip[] = array("start"=>"211.112.64.xxx","end"=>"211.112.95.xxx");
        $ip[] = array("start"=>"211.238.64.xxx","end"=>"211.238.95.xxx");
        $ip[] = array("start"=>"210.111.160.xxx","end"=>"210.111.191.xxx");
        $ip[] = array("start"=>"211.47.96.xxx","end"=>"211.47.99.xxx");
        $ip[] = array("start"=>"211.47.100.xxx","end"=>"211.47.107.xxx");
        $ip[] = array("start"=>"211.47.108.xxx","end"=>"211.47.127.xxx");
        $ip[] = array("start"=>"211.47.80.xxx","end"=>"211.47.95.xxx");
        $ip[] = array("start"=>"211.237.240.xxx","end"=>"211.237.255.xxx");
        $ip[] = array("start"=>"211.41.192.xxx","end"=>"211.41.207.xxx");
        $ip[] = array("start"=>"211.172.208.xxx","end"=>"211.172.223.xxx");
        $ip[] = array("start"=>"211.237.208.xxx","end"=>"211.237.223.xxx");
        $ip[] = array("start"=>"211.41.208.xxx","end"=>"211.41.223.xxx");
        $ip[] = array("start"=>"61.106.64.xxx","end"=>"61.106.79.xxx");
        $ip[] = array("start"=>"211.41.224.xxx","end"=>"211.41.255.xxx");
        $ip[] = array("start"=>"211.174.96.xxx","end"=>"211.174.127.xxx");
        $ip[] = array("start"=>"211.255.128.xxx","end"=>"211.255.159.xxx");
        $ip[] = array("start"=>"211.233.128.xxx","end"=>"211.233.255.xxx");
        $ip[] = array("start"=>"211.115.32.xxx","end"=>"211.115.63.xxx");
        $ip[] = array("start"=>"211.172.128.xxx","end"=>"211.172.143.xxx");
        $ip[] = array("start"=>"211.236.192.xxx","end"=>"211.236.223.xxx");
        $ip[] = array("start"=>"211.189.192.xxx","end"=>"211.189.223.xxx");
        $ip[] = array("start"=>"211.173.160.xxx","end"=>"211.173.191.xxx");
        $ip[] = array("start"=>"211.236.128.xxx","end"=>"211.236.159.xxx");
        $ip[] = array("start"=>"211.174.0.xxx","end"=>"211.174.15.xxx");
        $ip[] = array("start"=>"211.172.64.xxx","end"=>"211.172.79.xxx");
        $ip[] = array("start"=>"211.172.32.xxx","end"=>"211.172.63.xxx");
        $ip[] = array("start"=>"211.173.128.xxx","end"=>"211.173.159.xxx");
        $ip[] = array("start"=>"211.115.224.xxx","end"=>"211.115.255.xxx");
        $ip[] = array("start"=>"61.252.192.xxx","end"=>"61.252.255.xxx");
    
        $cnt = count($ip);
    
        $range = $ip[rand(0,$cnt-1)];
        $start_range = $range["start"];
        $end_range = $range["end"];
    
        $start = explode(".",$start_range);
        $end = explode(".",$end_range);
    
        // 랜덤으로 IP 대역을 생성
        $rand_ip = rand($start[2],$end[2]);
    
        return $start[0].".".$start[1].".".$rand_ip.".000";
    }

    public function check($qa_no = '', Request $request){
        $id     = Auth('head')->user()->id;
        $name   = Auth('head')->user()->name;

        $cmd    = $request->input("cmd");
        $code   = 0;
        $check_msg = "";
        $query = "
			select ifnull(check_id,'') as check_id, ifnull(check_nm,'') as check_nm
			from goods_qa_new
			where no = '$qa_no'
		";
        $qna_rs = DB::select($query);
        $_check_id = $qna_rs[0]->check_id;
        $_check_nm = $qna_rs[0]->check_nm;

        if($cmd == "checkin"){ // 접수
            if($_check_id != ""){
				if($id != $_check_id){
					$check_msg = sprintf("%s(%s) 님께서 접수하셨습니다.",$_check_id,$_check_nm);
				}else{
					$check_msg = "";
				}
			}else{
                $update_items = [
                    "check_id" => $id,
                    "check_nm" => $name
                ];
                
                try {
                    DB::table('goods_qa_new')
                    ->where('no','=', $qa_no)
                    ->update($update_items);
                    $code = 1;
                } catch(Exception $e){
                    $code = 0;
                }
			}
        }else if($cmd == "checkout"){ // 접수 해제  
            if($id == $_check_id){
                $update_items = [
                    "check_id" => null,
                    "check_nm" => null
                ];
                
                try {
                    DB::table('goods_qa_new')
                    ->where('no','=', $qa_no)
                    ->update($update_items);
                    $code = 1;
                } catch(Exception $e){
                    $code = 0;
                }
            }else{
                $check_msg = sprintf("%s (%s) 님이 접수한 건입니다. 접수 해제 하실 수 없습니다.",$_check_nm,$_check_id);
            }
        }
        return response()->json([
            "code" => 200,
            "qa_code" => $code,
            "check_msg" => $check_msg
        ]);
    }

    public function command($qa_no = '', Request $request){
        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $check_msg = "";
        $code = 0;
        $cmd = $request->input("cmd");
        
        //echo "qa_no : ". $qa_no;
        if($cmd == "editcmd"){
            $conf = new Conf();
            $cfg_shop_name = "";
            $cfg_shop_name = $conf->getConfigValue("shop","name");
            $cfg_kakao_yn	= $conf->getConfigValue("kakao","kakao_yn");
            $shop_tel = $conf->getConfigValue("shop","phone");
            $cfg_sms = $conf->getConfigValue("sms","qa_msg");
            
            $answer = $request->input("answer");
            $sms_yn = $request->input("sms_yn");
            $email_yn = $request->input("email_yn");
            
            $user_name = $request->input("user_name");
            $user_mobile = $request->input("user_mobile");
            $user_email = $request->input("user_email");
            $qa_subject = $request->input("qa_subject");
            $qa_regi_date = $request->input("qa_regi_date");
            $user_question = $request->input("user_question");
            $goods_name = $request->input("goods_name");

            $sms = new SMS([
                'admin_id' => $id,
                'admin_nm' => $name,
            ]);
            
            $update_items = [
                "answer" => $answer,
                "admin_id" => $id,
                "admin_nm" => $name,
                "answer_yn" => 'Y',
                "a_date" => now(),
                "check_id" => null,
                "check_nm" => null
            ];
            
            try {
                DB::table('goods_qa_new')
                ->where('no','=', $qa_no)
                ->update($update_items);
                $code = 1;
            } catch(Exception $e){
                $code = 0;
            }

            if($code==1){
                /******************************************************
                * 테스트 위해 아래로 휴대폰 번호 임시 지정 시작
                ******************************************************/
                //$user_mobile = "010-9877-2675";
                //$user_name = "테스트";
                /******************************************************
                * 테스트 위해 아래로 휴대폰 번호 임시 지정 시작
                ******************************************************/

                if($sms_yn == "Y"){
                    $msgarr = array(
                        "SHOP_NAME" => $cfg_shop_name,
                        "USER_NAME" => $user_name,
                    );

                    $sms_msg = $sms->MsgReplace($cfg_sms, $msgarr);
                    
                    //echo "sms_msg : ". $sms_msg;
                    if($user_mobile != ""){
                        //$sms->Send($sms_msg, $user_mobile, $user_name, $shop_tel);
						$sms->SendAligoSMS( $user_mobile, $sms_msg, $user_name );
                    }
                }
            }
            
        }else if($cmd == "change"){
            $show_yn = $request->input("show_yn_s");
            $show_change = ($show_yn == "Y") ? "N" : "Y";
            
            $update_items = [
                "show_yn" => $show_change
            ];

            try {
                DB::table('goods_qa_new')
                ->where('no','=', $qa_no)
                ->update($update_items);
                $code = 1;
            } catch(Exception $e){
                $code = 0;
            }

        }
        return response()->json([
            "code" => 200,
            "qa_code" => $code
        ]);
    }
    
}
