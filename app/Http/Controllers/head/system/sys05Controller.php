<?php

namespace App\Http\Controllers\head\system;

use App\Http\Controllers\Controller;
use App\Components\SLib;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class sys05Controller extends Controller
{

    public function index()
    {
        $arr = ["name"];
        //상점 탭
        $sql_name = "select * from conf where type = 'shop' and name = 'name'";
        $name = DB::selectOne($sql_name);
        
        $sql_code = "select * from conf where type = 'shop' and name = 'code'";
        $code = DB::selectOne($sql_code);

        $sql_phone = "select * from conf where type = 'shop' and name = 'company_phone_number'";
        $phone = DB::selectOne($sql_phone);
       
        $sql_store_domain = "select * from conf where type = 'shop' and name = 'domain'";
        $s_domain = DB::selectOne($sql_store_domain);
       
        $sql_admin_domain = "select * from conf where type = 'shop' and name = 'domain_bizest'";
        $a_domain = DB::selectOne($sql_admin_domain);

        $sql_email = "select * from conf where type = 'shop' and name = 'email'";
        $email = DB::selectOne($sql_email);

        $sql_title = "select * from conf where type = 'shop' and name = 'title'";
        $title = DB::selectOne($sql_title);

        $sql_title_main = "select * from conf where type = 'shop' and name = 'title_main'";
        $title_main = DB::selectOne($sql_title_main);

        $sql_meta_tag = "select * from conf where type = 'shop' and name = 'meta_tag'";
        $meta_tag = DB::selectOne($sql_meta_tag);

        $sql_add_script = "select * from conf where type = 'shop' and name = 'add_script'";
        $add_script = DB::selectOne($sql_add_script);

        $sql_sale_place = "select * from conf where type = 'shop' and name = 'sale_place'";
        $sale_place = DB::selectOne($sql_sale_place);

        //주문 탭
        $sql_cash_use_yn = "select * from conf where type = 'shop' and name = 'cash_use_yn'";
        $cash_use_yn = DB::selectOne($sql_cash_use_yn);

        $sql_bank = "select * from conf where type = 'bank' and name = 'info'";
        $bank = DB::selectOne($sql_bank);

        $account = $bank->value;
        $bank_arr = explode("|", $account);
        $bank_nm = $bank_arr[0];
        $account_no = $bank_arr[1];
        $account_holder = $bank_arr[2];

        //배송 탭
        $sql_base_delivery_fee = "select * from conf where type = 'delivery' and name = 'base_delivery_fee'";
        $base_delivery_fee = DB::selectOne($sql_base_delivery_fee);

        $sql_add_delivery_fee = "select * from conf where type = 'delivery' and name = 'add_delivery_fee'";
        $add_delivery_fee = DB::selectOne($sql_add_delivery_fee);

        $sql_free_delivery_amt = "select * from conf where type = 'delivery' and name = 'free_delivery_amt'";
        $free_delivery_amt = DB::selectOne($sql_free_delivery_amt);

        $sql_wholesale_free_delivery_amt = "select * from conf where type = 'delivery' and name = 'wholesale_free_delivery_amt'";
        $wholesale_free_delivery_amt = DB::selectOne($sql_wholesale_free_delivery_amt);

        $sql_dlv_cd = "select * from conf where type = 'delivery' and name = 'dlv_cd'";
        $dlv_cd = DB::selectOne($sql_dlv_cd);

        //적립금 탭
        $sql_estimate_point_yn = "select * from conf where type = 'point' and name = 'estimate_point_yn'";
        $estimate_point = DB::selectOne($sql_estimate_point_yn);

        $sql_join_point = "select * from conf where type = 'point' and name = 'join_point'";
        $join_point = DB::selectOne($sql_join_point);

        $sql_policy = "select * from conf where type = 'point' and name = 'policy'";
        $policy = DB::selectOne($sql_policy);

        $sql_ratio = "select * from conf where type = 'point' and name = 'ratio'";
        $ratio = DB::selectOne($sql_ratio);
    
        $sql_return_yn = "select * from conf where type = 'point' and name = 'return_yn'";
        $return_yn = DB::selectOne($sql_return_yn);

        //카카오 탭
        $sql_kakao_yn = "select * from conf where type = 'kakao' and name = 'kakao_yn'";
        $kakao_yn = DB::selectOne($sql_kakao_yn);

        //SMS 탭
        $sql_sms_yn = "select * from conf where type = 'sms' and name = 'sms_yn'";
        $sms_yn = DB::selectOne($sql_sms_yn);
       
        $sql_join_yn = "select * from conf where type = 'sms' and name = 'join_yn'";
        $join_yn = DB::selectOne($sql_join_yn);
       
        $sql_join_msg = "select * from conf where type = 'sms' and name = 'join_msg'";
        $join_msg = DB::selectOne($sql_join_msg);

        $sql_passwd_yn = "select * from conf where type = 'sms' and name = 'passwd_yn'";
        $passwd_yn = DB::selectOne($sql_passwd_yn);

        $sql_passwd_msg = "select * from conf where type = 'sms' and name = 'passwd_msg'";
        $passwd_msg = DB::selectOne($sql_passwd_msg);

        $sql_order_yn = "select * from conf where type = 'sms' and name = 'order_yn'";
        $order_yn = DB::selectOne($sql_order_yn);

        $sql_order_msg_pay = "select * from conf where type = 'sms' and name = 'order_msg_pay'";
        $order_msg_pay = DB::selectOne($sql_order_msg_pay);

        $sql_payment_yn = "select * from conf where type = 'sms' and name = 'payment_yn'";
        $payment_yn = DB::selectOne($sql_payment_yn);

        $sql_payment_msg = "select * from conf where type = 'sms' and name = 'payment_msg'";
        $payment_msg = DB::selectOne($sql_payment_msg);

        $sql_delivery_yn = "select * from conf where type = 'sms' and name = 'delivery_yn'";
        $delivery_yn = DB::selectOne($sql_delivery_yn);
        
        $sql_delivery_msg = "select * from conf where type = 'sms' and name = 'delivery_msg'";
        $delivery_msg = DB::selectOne($sql_delivery_msg);

        $sql_refund_yn = "select * from conf where type = 'sms' and name = 'refund_yn'";
        $refund_yn = DB::selectOne($sql_refund_yn);

        $sql_refund_msg_complete = "select * from conf where type = 'sms' and name = 'refund_msg_complete'";
        $refund_msg_complete = DB::selectOne($sql_refund_msg_complete);

        $sql_refund_msg_cancel = "select * from conf where type = 'sms' and name = 'refund_msg_cancel'";
        $refund_msg_cancel = DB::selectOne($sql_refund_msg_cancel);

        $sql_out_of_stock_yn = "select * from conf where type = 'sms' and name = 'out_of_stock_yn'";
        $out_of_stock_yn = DB::selectOne($sql_out_of_stock_yn);

        $sql_out_of_stock_msg = "select * from conf where type = 'sms' and name = 'out_of_stock_msg'";
        $out_of_stock_msg = DB::selectOne($sql_out_of_stock_msg);

        //부가 기능 탭
        $sql_ssl_yn = "select * from conf where type = 'shop' and name = 'ssl_yn'";
        $ssl_yn = DB::selectOne($sql_ssl_yn);

        $sql_wholesale_yn = "select * from conf where type = 'shop' and name = 'wholesale_yn'";
        $wholesale_yn = DB::selectOne($sql_wholesale_yn);

        $sql_est_confirm_yn = "select * from conf where type = 'shop' and name = 'est_confirm_yn'";
        $est_confirm_yn = DB::selectOne($sql_est_confirm_yn);

        $sql_new_good_day = "select * from conf where type = 'shop' and name = 'new_good_day'";
        $new_good_day = DB::selectOne($sql_new_good_day);

        $sql_new_data_day = "select * from conf where type = 'shop' and name = 'new_data_day'";
        $new_data_day = DB::selectOne($sql_new_data_day);

        $sql_category_goods_cnt = "select * from conf where type = 'shop' and name = 'category_goods_cnt'";
        $category_goods_cnt = DB::selectOne($sql_category_goods_cnt);

        $sql_newarrival_goods_cnt = "select * from conf where type = 'shop' and name = 'newarrival_goods_cnt'";
        $newarrival_goods_cnt = DB::selectOne($sql_newarrival_goods_cnt);

        $sql_onsale_goods_cnt = "select * from conf where type = 'shop' and name = 'onsale_goods_cnt'";
        $onsale_goods_cnt = DB::selectOne($sql_onsale_goods_cnt);

        $sql_brandshop_goods_cnt = "select * from conf where type = 'shop' and name = 'brandshop_goods_cnt'";
        $brandshop_goods_cnt = DB::selectOne($sql_brandshop_goods_cnt);

        $sql_best_rank_goods_cnt = "select * from conf where type = 'shop' and name = 'best_rank_goods_cnt'";
        $best_rank_goods_cnt = DB::selectOne($sql_best_rank_goods_cnt);

        $sql_relative_goods_cnt = "select * from conf where type = 'shop' and name = 'relative_goods_cnt'";
        $relative_goods_cnt = DB::selectOne($sql_relative_goods_cnt);

        $sql_search_goods_cnt = "select * from conf where type = 'shop' and name = 'search_goods_cnt'";
        $search_goods_cnt = DB::selectOne($sql_search_goods_cnt);

        $sql_search_goods_sort = "select * from conf where type = 'shop' and name = 'search_goods_sort'";
        $search_goods_sort = DB::selectOne($sql_search_goods_sort);

        $sql_counsel_yn = "select * from conf where type = 'email' and name = 'counsel_yn'";
        $counsel_yn = DB::selectOne($sql_counsel_yn);

        $sql_goods_qa_yn = "select * from conf where type = 'email' and name = 'goods_qa_yn'";
        $goods_qa_yn = DB::selectOne($sql_goods_qa_yn);

        //게시물 탭
        $sql_community_goods_qa = "select * from conf where type = 'list_count' and name = 'community_goods_qa'";
        $community_goods_qa = DB::selectOne($sql_community_goods_qa);

        $sql_community_goods_review = "select * from conf where type = 'list_count' and name = 'community_goods_review'";
        $community_goods_review = DB::selectOne($sql_community_goods_review);

        $sql_community_main_notice = "select * from conf where type = 'list_count' and name = 'community_main_notice'";
        $community_main_notice = DB::selectOne($sql_community_main_notice);

        $sql_community_main_qa = "select * from conf where type = 'list_count' and name = 'community_main_qa'";
        $community_main_qa = DB::selectOne($sql_community_main_qa);

        $sql_community_main_review = "select * from conf where type = 'list_count' and name = 'community_main_review'";
        $community_main_review = DB::selectOne($sql_community_main_review);

        $sql_main_notice = "select * from conf where type = 'list_count' and name = 'main_notice'";
        $main_notice = DB::selectOne($sql_main_notice);

        $sql_notice = "select * from conf where type = 'list_count' and name = 'notice'";
        $notice = DB::selectOne($sql_notice);

        //서비스 탭

        $sql_shoplinker_id = "select * from conf where type = 'api' and name = 'shoplinker_id'";
        $shoplinker_id = DB::selectOne($sql_shoplinker_id);
       
        $sql_shoplinker_user_id = "select * from conf where type = 'api' and name = 'shoplinker_user_id'";
        $shoplinker_user_id = DB::selectOne($sql_shoplinker_user_id);

        $sql_sabangnet_id = "select * from conf where type = 'api' and name = 'sabangnet_id'";
        $sabangnet_id = DB::selectOne($sql_sabangnet_id);

        $sql_sabangnet_key = "select * from conf where type = 'api' and name = 'sabangnet_key'";
        $sabangnet_key = DB::selectOne($sql_sabangnet_key);





    

        $values = [
            //상점 탭
            'name' => $name->value,
            'code'  => $code->value,
            'phone' => $phone->value,
            's_domain' => $s_domain->value,
            'a_domain' => $a_domain->value,
            'email' => $email->value,
            'title' => $title->value,
            'title_main' => $title_main->value,
            'meta_tag' => $meta_tag->value,
            'add_script' => $add_script->value,
            'sale_place' => $sale_place->value,
            //주문 탭
            'cash_use_yn' => $cash_use_yn->value,
            'bank_nm' => $bank_nm,
            'account_no' => $account_no,
            'account_holder' => $account_holder,
            //배송 탭
            'base_delivery_fee' => $base_delivery_fee->value,
            'add_delivery_fee' => $add_delivery_fee->value,
            'free_delivery_amt' => $free_delivery_amt->value,
            'wholesale_free_delivery_amt' => $wholesale_free_delivery_amt->value,
            'dlv_cd' => $dlv_cd->value,
            //적립금 탭
            'estimate_point' => $estimate_point->value,
            'join_point' => $join_point->value,
            'policy' => $policy->value,
            'ratio' => $ratio->value,
            'return_yn' => $return_yn->value,
            //카카오 탭
            'kakao_yn' => $kakao_yn->value,
            //SMS 탭
            'sms_yn' => $sms_yn->value,
            'join_yn' => $join_yn->value,
            'join_msg' => $join_msg->content,
            'passwd_yn' => $passwd_yn->value,
            'passwd_msg' => $passwd_msg->content,
            'order_yn' => $order_yn->value,
            'payment_yn' => $payment_yn->value,
            'payment_msg' => $payment_msg->content,
            'delivery_yn' => $delivery_yn->value,
            'delivery_msg' => $delivery_msg->content,
            'refund_yn' => $refund_yn->value,
            'refund_msg_complete' => $refund_msg_complete->content,
            'refund_msg_cancel' => $refund_msg_cancel->content,
            'out_of_stock_yn' => $out_of_stock_yn->value,
            'out_of_stock_msg' => $out_of_stock_msg->content,
            //부가기능 탭
            'ssl_yn' => $ssl_yn->value,
            'wholesale_yn' => $wholesale_yn->value,
            'est_confirm_yn' => $est_confirm_yn->value,
            'new_good_day' => $new_good_day->value,
            'new_data_day' => $new_data_day->value,
            'category_goods_cnt' => $category_goods_cnt->value,
            'newarrival_goods_cnt' => $newarrival_goods_cnt->value,
            'onsale_goods_cnt' => $onsale_goods_cnt->value,
            'brandshop_goods_cnt' => $brandshop_goods_cnt->value,
            'best_rank_goods_cnt' => $best_rank_goods_cnt->value,
            'relative_goods_cnt' => $relative_goods_cnt->value,
            'search_goods_cnt' => $search_goods_cnt->value,
            'search_goods_sort' => $search_goods_sort->value,
            'counsel_yn' => $counsel_yn->value,
            'goods_qa_yn' => $goods_qa_yn->value,
            //게시물 탭
            'community_goods_qa' => $community_goods_qa->value,
            'community_goods_review' => $community_goods_review->value,
            'community_main_notice' => $community_main_notice->value,
            'community_main_qa' =>$community_main_qa->value,
            'community_main_review' => $community_main_review->value,
            'main_notice' => $main_notice->value,
            'notice' => $notice->value,

            //서비스 탭
            'sabangnet_id' => $sabangnet_id->value,
            'sabangnet_key' => $sabangnet_key->value,
            'shoplinker_id' => $shoplinker_id->value,
            'shoplinker_user_id' => $shoplinker_user_id->value,
        ];


        return view(Config::get('shop.head.view') . '/system/sys05', $values);
    }

    public function update(Request $request)
    {

        $type = $request->input('type');

        //상점 탭
        $name = $request->input('name');
        $s_code = $request->input('code');
        $phone = $request->input('phone');
        $domain = $request->input('domain');
        $domain_bizest = $request->input('domain_bizest');
        $email = $request->input('email');
        $title = $request->input('title');
        $title_main = $request->input('title_main');
        $meta_tag = $request->input('meta_tag');
        $add_script = $request->input('add_script');
        $sale_place = $request->input('sale_place');
        $ut = now();

        //주문 탭
        $cash_use_yn = $request->input('cash_use_yn');
        $bank_nm = $request->input('bank_nm');
        $account_no = $request->input('account_no');
        $account_holder = $request->input('account_holder');

        //배송 탭
        $base_delivery_fee = $request->input('base_delivery_fee');
        $add_delivery_fee = $request->input('add_delivery_fee');
        $free_delivery_amt = $request->input('free_delivery_amt');
        $wholesale_free_delivery_amt = $request->input('wholesale_free_delivery_amt');

        //적립금 탭
        $join_point = $request->input('join_point');
        $policy = $request->input('policy');
        $ratio = $request->input('ratio');
        $return_yn = $request->input('return_yn');

        //카카오 탭
        $kakao_yn = $request->input('kakao_yn');

        //SMS 탭
        $sms_yn = $request->input('sms_yn');
        $join_yn = $request->input('join_yn');
        $join_msg = $request->input('join_msg');
        $passwd_yn = $request->input('passwd_yn');
        $passwd_msg = $request->input('passwd_msg');
        $order_yn = $request->input('order_yn');
        $payment_yn = $request->input('payment_yn');
        $payment_msg = $request->input('payment_msg');
        $delivery_yn = $request->input('delivery_yn');
        $delivery_msg = $request->input('delivery_msg');
        $refund_yn = $request->input('refund_yn');
        $refund_msg_complete = $request->input('refund_msg_complete');
        $refund_msg_cancel = $request->input('refund_msg_cancel');
        $out_of_stock_yn = $request->input('out_of_stock_yn');
        $out_of_stock_msg = $request->input('out_of_stock_msg');

        //부가기능 탭
        $ssl_yn = $request->input('ssl_yn');
        $wholesale_yn = $request->input('wholesale_yn');
        $est_confirm_yn = $request->input('est_confirm_yn');
        $new_goods_day = $request->input('new_good_day');
        $new_data_day = $request->input('new_data_day');
        $category_goods_cnt = $request->input('category_goods_cnt');
        $newarrival_goods_cnt = $request->input('newarrival_goods_cnt');
        $onsale_goods_cnt = $request->input('onsale_goods_cnt');
        $brandshop_goods_cnt = $request->input('brandshop_goods_cnt');
        $best_rank_goods_cnt = $request->input('best_rank_goods_cnt');
        $relative_goods_cnt = $request->input('relative_goods_cnt');
        $search_goods_cnt = $request->input('search_goods_cnt');
        $search_goods_sort = $request->input('search_goods_sort');
        $counsel_yn = $request->input('counsel_yn');
        $goods_qa_yn = $request->input('goods_qa_yn');

        //게시물 탭
        $community_goods_qa = $request->input('community_goods_qa');
        $community_goods_review = $request->input('community_goods_review');
        $community_main_notice = $request->input('community_main_notice');
        $community_main_qa = $request->input('community_main_qa');
        $community_main_review = $request->input('community_main_review');
        $main_notice = $request->input('main_notice');
        $notice = $request->input('notice');

        $sabangnet_id = $request->input('sabangnet_id');
        $sabangnet_key = $request->input('sabangnet_key');
        $shoplinker_id = $request->input('shoplinker_id');
        $shoplinker_user_id = $request->input('shoplinker_user_id');

        try {
            if ($type == 'shop') {
                // 상점 탭
                $sql_name = "update conf set value='$name', ut = '$ut' where type='shop' and name='name'";
                DB::update($sql_name);

                $sql_code = "update conf set value='$s_code', ut = '$ut' where type='shop' and name='code'";
                DB::update($sql_code);

                $sql_phone = "update conf set value='$phone', ut = '$ut' where type='shop' and name='phone'";
                DB::update($sql_phone);

                $sql_domain = "update conf set value='$domain', ut = '$ut' where type='shop' and name='domain'";
                DB::update($sql_domain);

                $sql_domain_bizest = "update conf set value='$domain_bizest', ut = '$ut' where type='shop' and name='domain_bizest'";
                DB::update($sql_domain_bizest);

                $sql_email = "update conf set value='$email', ut = '$ut' where type='shop' and name='email'";
                DB::update($sql_email);

                $sql_title = "update conf set value='$title', ut = '$ut' where type='shop' and name='title'";
                DB::update($sql_title);

                $sql_title_main = "update conf set value='$title_main', ut = '$ut' where type='shop' and name='title_main'";
                DB::update($sql_title_main);

                $sql_meta_tag = "update conf set value='$meta_tag', ut = '$ut' where type='shop' and name='meta_tag'";
                DB::update($sql_meta_tag);

                $sql_add_script = "update conf set value='$add_script', ut = '$ut' where type='shop' and name='add_script'";
                DB::update($sql_add_script);

                $sql_sale_place = "update conf set value='$sale_place', ut = '$ut' where type='shop' and name='sale_place'";
                DB::update($sql_sale_place);
            } elseif ($type == 'order') {
                //주문 탭
                $sql_cash_use_yn = "update conf set value='$cash_use_yn', ut = '$ut' where type='shop' and name='cash_use_yn'";
                DB::update($sql_cash_use_yn);

                $sql_bank = "update conf set value='$bank_nm|$account_no|$account_holder',account_no|$account_holder', ut = '$ut' where type='bank' and name='info'";
                DB::update($sql_bank);

            } elseif ($type == 'delivery') {
                //배송 탭
                $sql_base_delivery_fee = "update conf set value='$base_delivery_fee', ut = '$ut' where type='delivery' and name='base_delivery_fee'";
                DB::update($sql_base_delivery_fee);

                $sql_add_delivery_fee = "update conf set value='$add_delivery_fee', ut = '$ut' where type='delivery' and name='add_delivery_fee'";
                DB::update($sql_add_delivery_fee);

                $sql_free_delivery_amt = "update conf set value='$free_delivery_amt', ut = '$ut' where type='delivery' and name='free_delivery_amt'";
                DB::update($sql_free_delivery_amt);

                $sql_wholesale_free_delivery_amt = "update conf set value='$wholesale_free_delivery_amt', ut = '$ut' where type='delivery' and name='wholesale_free_delivery_amt'";
                DB::update($sql_wholesale_free_delivery_amt);

            } elseif ($type == 'point') {
                //적립금 탭
                $sql_join_point = "update conf set value='$join_point', ut = '$ut' where type='point' and name='join_point'";
                DB::update($sql_join_point);

                $sql_policy = "update conf set value='$policy', ut = '$ut' where type='point' and name='policy'";
                DB::update($sql_policy);

                $sql_ratio = "update conf set value='$ratio', ut = '$ut' where type='point' and name='ratio'";
                DB::update($sql_ratio);

                $sql_join_point = "update conf set value='$return_yn', ut = '$ut' where type='point' and name='return_yn'";
                DB::update($sql_join_point);
            } elseif ($type == 'kakao') {
                //카카오 탭
                $sql_kakao_yn = "update conf set value='$kakao_yn', ut = '$ut' where type='kakao' and name='kakao_yn'";
                DB::update($sql_kakao_yn);

            } elseif ($type == 'sms') {
                //sms 탭
                $sql_sms_yn = "update conf set value='$sms_yn', ut='$ut' where type='sms' and name='sms_yn'";
                DB::update($sql_sms_yn);
                
                $sql_join_yn = "update conf set value='$join_yn', ut='$ut' where type='sms' and name='join_yn'";
                DB::update($sql_join_yn);
                
                $sql_join_msg = "update conf set value='$join_yn', content='$join_msg', ut='$ut' where type='sms' and name='join_msg'";
                DB::update($sql_join_msg);

                $sql_delivery_msg = "update conf set value='$delivery_yn', content='$delivery_msg', ut='$ut' where type='sms' and name='delivery_msg'";
                DB::update($sql_delivery_msg);
                
                $sql_delivery_yn = "update conf set value='$delivery_yn', ut = '$ut' where type='sms' and name='delivery_yn'";
                DB::update($sql_delivery_yn);

                $sql_out_of_stock_yn = "update conf set value='$out_of_stock_yn', ut = '$ut' where type='sms' and name='out_of_stock_yn'";
                DB::update($sql_out_of_stock_yn);
               
                $sql_out_of_stock_msg = "update conf set value='$out_of_stock_yn', content='$out_of_stock_msg', ut = '$ut' where type='sms' and name='out_of_stock_msg'";
                DB::update($sql_out_of_stock_msg);

                $sql_passwd_yn = "update conf set value='$passwd_yn', ut = '$ut' where type='sms' and name='passwd_yn'";
                DB::update($sql_passwd_yn);
                
                $sql_passwd_msg = "update conf set value='$passwd_yn', content='$passwd_msg', ut = '$ut' where type='sms' and name='passwd_msg'";
                DB::update($sql_passwd_msg);

                $sql_payment_yn = "update conf set value='$payment_yn', ut = '$ut' where type='sms' and name='payment_yn'";
                DB::update($sql_payment_yn);
                
                $sql_payment_msg = "update conf set value='$payment_yn', content='$payment_msg', ut = '$ut' where type='sms' and name='payment_msg'";
                DB::update($sql_payment_msg);

                $sql_refund_yn = "update conf set value='$refund_yn', ut = '$ut' where type='sms' and name='refund_yn'";
                DB::update($sql_refund_yn);
                
                $sql_refund_msg_complete = "update conf set value='$refund_yn', content='$refund_msg_complete', ut = '$ut' where type='sms' and name='refund_msg_complete'";
                DB::update($sql_refund_msg_complete);
               
                $sql_refund_msg_cancel = "update conf set value='$refund_yn', content='$refund_msg_cancel', ut = '$ut' where type='sms' and name='refund_msg_cancel'";
                DB::update($sql_refund_msg_cancel);

            } else if ($type == 'stock_reduction') {
                //부가 기능 탭
                $sql_ssl_yn = "update conf set value='$ssl_yn', ut = '$ut' where type='shop' and name='ssl_yn'";
                DB::update($sql_ssl_yn);

                $sql_est_confirm_yn = "update conf set value='$est_confirm_yn', ut = '$ut' where type='shop' and name='est_confirm_yn'";
                DB::update($sql_est_confirm_yn);

                $sql_wholesale_yn = "update conf set value='$wholesale_yn', ut = '$ut' where type='shop' and name='wholesale_yn'";
                DB::update($sql_wholesale_yn);

                $sql_new_goods_day = "update conf set value='$new_goods_day', ut = '$ut' where type='shop' and name='new_goods_day'";
                DB::update($sql_new_goods_day);

                $sql_new_data_day = "update conf set value='$new_data_day', ut = '$ut' where type='shop' and name='new_data_day'";
                DB::update($sql_new_data_day);

                $sql_category_goods_cnt = "update conf set value='$category_goods_cnt', ut = '$ut' where type='shop' and name='category_goods_cnt'";
                DB::update($sql_category_goods_cnt);

                $sql_newarrival_goods_cnt = "update conf set value='$newarrival_goods_cnt', ut = '$ut' where type='shop' and name='newarrival_goods_cnt'";
                DB::update($sql_newarrival_goods_cnt);

                $sql_onesale_goods_cnt = "update conf set value='$onsale_goods_cnt', ut = '$ut' where type='shop' and name='onsale_goods_cnt'";
                DB::update($sql_onesale_goods_cnt);

                $sql_brandshop_goods_cnt = "update conf set value='$brandshop_goods_cnt', ut = '$ut' where type='shop' and name='brandshop_goods_cnt'";
                DB::update($sql_brandshop_goods_cnt);

                $sql_best_rank_goods_cnt = "update conf set value='$best_rank_goods_cnt', ut = '$ut' where type='shop' and name='best_rank_goods_cnt'";
                DB::update($sql_best_rank_goods_cnt);

                $sql_relative_goods_cnt = "update conf set value='$relative_goods_cnt', ut = '$ut' where type='shop' and name='relative_goods_cnt'";
                DB::update($sql_relative_goods_cnt);

                $sql_search_goods_cnt = "update conf set value='$search_goods_cnt', ut = '$ut' where type='shop' and name='search_goods_cnt'";
                DB::update($sql_search_goods_cnt);

                $sql_search_goods_sort = "update conf set value='$search_goods_sort', ut = '$ut' where type='shop' and name='search_goods_sort'";
                DB::update($sql_search_goods_sort);

                $sql_counsel_yn = "update conf set value='$counsel_yn', ut = '$ut' where type='email' and name='counsel_yn'";
                DB::update($sql_counsel_yn);
                
                $sql_goods_qa_yn = "update conf set value='$goods_qa_yn', ut = '$ut' where type='email' and name='goods_qa_yn'";
                DB::update($sql_goods_qa_yn);

            } else if ($type == 'list_count') {
                //게시물 탭
                $sql_community_goods_qa = "update conf set value='$community_goods_qa', ut = '$ut' where type='list_count' and name='community_goods_qa'";
                DB::update($sql_community_goods_qa);

                $sql_community_goods_review = "update conf set value='$community_goods_review', ut = '$ut' where type='list_count' and name='community_goods_review'";
                DB::update($sql_community_goods_review);

                $sql_community_main_notice = "update conf set value='$community_main_notice', ut = '$ut' where type='list_count' and name='community_main_notice'";
                DB::update($sql_community_main_notice);

                $sql_community_main_qa = "update conf set value='$community_main_qa', ut = '$ut' where type='list_count' and name='community_main_qa'";
                DB::update($sql_community_main_qa);

                $sql_community_main_review = "update conf set value='$community_main_review', ut = '$ut' where type='list_count' and name='community_main_review'";
                DB::update($sql_community_main_review);

                $sql_main_notice = "update conf set value='$main_notice', ut = '$ut' where type='list_count' and name='main_notice'";
                DB::update($sql_main_notice);

                $sql_notice = "update conf set value='$notice', ut = '$ut' where type='list_count' and name='notice'";
                DB::update($sql_notice);

            } else if ($type == 'admin') {
                $sql_sabangnet_id = "update conf set value='$sabangnet_id', ut = '$ut' where type='api' and name='sabangnet_id'";
                DB::update($sql_sabangnet_id);

                $sql_sabangnet_key = "update conf set value='$sabangnet_key', ut = '$ut' where type='api' and name='sabangnet_key'";
                DB::update($sql_sabangnet_key);

                $sql_shoplinker_id = "update conf set value='$shoplinker_id', ut = '$ut' where type='api' and name='shoplinker_id'";
                DB::update($sql_shoplinker_id);
               
                $sql_shoplinker_user_id = "update conf set value='$shoplinker_user_id', ut = '$ut' where type='api' and name='shoplinker_user_id'";
                DB::update($sql_shoplinker_user_id);
            } else if ($type == 'mobile') {

            } else if ($type == 'image') {
                
            }


                DB::commit();
                $code = '200';
                $msg = '';
            } catch (Exception $e) {
                $code = 500;
                DB::rollBack();
                $msg = $e->getMessage();
            }
            
            return response()->json(['code' => $code, 'msg' => $msg]);
        }
   
}