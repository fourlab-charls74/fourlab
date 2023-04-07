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

        $sql_add_script_content = "select * from conf where type = 'shop' and name = 'add_script_content'";
        $add_script_content = DB::selectOne($sql_add_script_content);

        $sql_sale_place = "select * from conf where type = 'shop' and name = 'sale_place'";
        $sale_place = DB::selectOne($sql_sale_place);

        //주문 탭
        $sql_cash_use_yn = "select * from conf where type = 'shop' and name = 'cash_use_yn'";
        $cash_use_yn = DB::selectOne($sql_cash_use_yn);

        $sql_bank = "select * from conf where type = 'bank' and name = 'info'";
        $bank = DB::selectOne($sql_bank);

        $sql_cancel_period = "select * from conf where type = 'order' and name = 'cancel_period'";
        $cancel_period = DB::selectOne($sql_cancel_period);

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

        $sql_wholesale_base_delivery_fee = "select * from conf where type = 'delivery' and name = 'wholesale_base_delivery_fee'";
        $wholesale_base_delivery_fee = DB::selectOne($sql_wholesale_base_delivery_fee);

        $sql_wholesale_add_delivery_fee = "select * from conf where type = 'delivery' and name = 'wholesale_add_delivery_fee'";
        $wholesale_add_delivery_fee = DB::selectOne($sql_wholesale_add_delivery_fee);

        $sql_day_delivery_yn = "select * from conf where type = 'delivery' and name = 'day_delivery_yn'";
        $day_delivery_yn = DB::selectOne($sql_day_delivery_yn);

        $sql_day_delivery_amt = "select * from conf where type = 'delivery' and name = 'day_delivery_amt'";
        $day_delivery_amt = DB::selectOne($sql_day_delivery_amt);

        $sql_day_delivery_type = "select * from conf where type = 'delivery' and name = 'day_delivery_type'";
        $day_delivery_type = DB::selectOne($sql_day_delivery_type);

        $sql_day_delivery_zone = "select * from conf where type = 'delivery' and name = 'day_delivery_zone'";
        $day_delivery_zone = DB::selectOne($sql_day_delivery_zone);

        $sql_dlv_cd = "select * from conf where type = 'delivery' and name = 'dlv_cd'";
        $dlv_cd = DB::selectOne($sql_dlv_cd);

        //적립금 탭
        $sql_estimate_point_yn = "select * from conf where type = 'point' and name = 'estimate_point_yn'";
        $estimate_point_yn = DB::selectOne($sql_estimate_point_yn);

        $sql_estimate_point = "select * from conf where type = 'point' and name = 'estimate_point'";
        $estimate_point = DB::selectOne($sql_estimate_point);

        $sql_join_point = "select * from conf where type = 'point' and name = 'join_point'";
        $join_point = DB::selectOne($sql_join_point);

        $sql_policy = "select * from conf where type = 'point' and name = 'policy'";
        $policy = DB::selectOne($sql_policy);

        $sql_ratio = "select * from conf where type = 'point' and name = 'ratio'";
        $ratio = DB::selectOne($sql_ratio);

        $sql_point_limit = "select * from conf where type = 'point' and name = 'point_limit'";
        $point_limit = DB::selectOne($sql_point_limit);

        $sql_p_give_type = "select * from conf where type = 'point' and name = 'p_give_type'";
        $p_give_type = DB::selectOne($sql_p_give_type);

        $sql_return_yn = "select * from conf where type = 'point' and name = 'return_yn'";
        $return_yn = DB::selectOne($sql_return_yn);

        //카카오 탭
        $sql_kakao_yn = "select * from conf where type = 'kakao' and name = 'kakao_yn'";
        $kakao_yn = DB::selectOne($sql_kakao_yn);

        $sql_sender_key = "select * from conf where type = 'kakao' and name = 'sender_key'";
        $sender_key = DB::selectOne($sql_sender_key);


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

        $sql_auth_yn = "select * from conf where type = 'sms' and name = 'auth_yn'";
        $auth_yn = DB::selectOne($sql_auth_yn);

        $sql_auth_msg = "select * from conf where type = 'sms' and name = 'auth_msg'";
        $auth_msg = DB::selectOne($sql_auth_msg);

        $sql_order_msg_pay = "select * from conf where type = 'sms' and name = 'order_msg_pay'";
        $order_msg_pay = DB::selectOne($sql_order_msg_pay);

        $sql_order_msg_not_pay = "select * from conf where type = 'sms' and name = 'order_msg_not_pay'";
        $order_msg_not_pay = DB::selectOne($sql_order_msg_not_pay);

        $sql_cancel_yn = "select * from conf where type = 'sms' and name = 'cancel_yn'";
        $cancel_yn = DB::selectOne($sql_cancel_yn);

        $sql_cancel_msg_bank = "select * from conf where type = 'sms' and name = 'cancel_msg_bank'";
        $cancel_msg_bank = DB::selectOne($sql_cancel_msg_bank);

        $sql_cancel_msg_card = "select * from conf where type = 'sms' and name = 'cancel_msg_card'";
        $cancel_msg_card = DB::selectOne($sql_cancel_msg_card);

        $sql_cancel_msg_transfer = "select * from conf where type = 'sms' and name = 'cancel_msg_transfer'";
        $cancel_msg_transfer = DB::selectOne($sql_cancel_msg_transfer);

        $sql_birth_yn = "select * from conf where type = 'sms' and name = 'birth_yn'";
        $birth_yn = DB::selectOne($sql_birth_yn);

        $sql_birth_msg = "select * from conf where type = 'sms' and name = 'birth_msg'";
        $birth_msg = DB::selectOne($sql_birth_msg);

        $sql_welcome_yn = "select * from conf where type = 'sms' and name = 'welcome_yn'";
        $welcome_yn = DB::selectOne($sql_welcome_yn);

        $sql_welcome_msg = "select * from conf where type = 'sms' and name = 'welcome_msg'";
        $welcome_msg = DB::selectOne($sql_welcome_msg);



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

        $sql_init_url = "select * from conf where type = 'stock_reduction' and name = 'init_url'";
        $init_url = DB::selectOne($sql_init_url);

        $sql_member_inactive_yn = "select * from conf where type = 'stock_reduction' and name = 'member_inactive_yn'";
        $member_inactive_yn = DB::selectOne($sql_member_inactive_yn);


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

        $sql_ipin_site_cd = "select * from conf where type = 'admin' and name = 'ipin_site_cd'";
        $ipin_site_cd = DB::selectOne($sql_ipin_site_cd);

        $sql_ipin_site_pw = "select * from conf where type = 'admin' and name = 'ipin_site_pw'";
        $ipin_site_pw = DB::selectOne($sql_ipin_site_pw);

        $sql_ipin_site_seq = "select * from conf where type = 'admin' and name = 'ipin_site_seq'";
        $ipin_site_seq = DB::selectOne($sql_ipin_site_seq);

        //모바일 탭
        $sql_m_domain = "select * from conf where type = 'mobile' and name = 'm_domain'";
        $m_domain = DB::selectOne($sql_m_domain);

        $sql_m_category_goods_cnt = "select * from conf where type = 'mobile' and name = 'm_category_goods_cnt'";
        $m_category_goods_cnt = DB::selectOne($sql_m_category_goods_cnt);

        $sql_m_newarrival_cnt = "select * from conf where type = 'mobile' and name = 'm_newarrival_cnt'";
        $m_newarrival_cnt = DB::selectOne($sql_m_newarrival_cnt);

        $sql_m_onsale_goods_cnt = "select * from conf where type = 'mobile' and name = 'm_onsale_goods_cnt'";
        $m_onsale_goods_cnt = DB::selectOne($sql_m_onsale_goods_cnt);

        $sql_m_brandshop_goods_cnt = "select * from conf where type = 'mobile' and name = 'm_brandshop_goods_cnt'";
        $m_brandshop_goods_cnt = DB::selectOne($sql_m_brandshop_goods_cnt);

        $sql_m_best_rank_goods_cnt = "select * from conf where type = 'mobile' and name = 'm_best_rank_goods_cnt'";
        $m_best_rank_goods_cnt = DB::selectOne($sql_m_best_rank_goods_cnt);

        $sql_m_search_goods_cnt = "select * from conf where type = 'mobile' and name = 'm_search_goods_cnt'";
        $m_search_goods_cnt = DB::selectOne($sql_m_search_goods_cnt);

        $sql_app_main_banner_1 = "select * from conf where type = 'mobile' and name = 'app_main_banner_1'";
        $app_main_banner_1 = DB::selectOne($sql_app_main_banner_1);

        $sql_app_main_banner_2 = "select * from conf where type = 'mobile' and name = 'app_main_banner_2'";
        $app_main_banner_2 = DB::selectOne($sql_app_main_banner_2);

        $sql_app_main_section_1 = "select * from conf where type = 'mobile' and name = 'app_main_section_1'";
        $app_main_section_1 = DB::selectOne($sql_app_main_section_1);

        $sql_app_main_section_2 = "select * from conf where type = 'mobile' and name = 'app_main_section_2'";
        $app_main_section_2 = DB::selectOne($sql_app_main_section_2);

        $sql_app_main_section_3 = "select * from conf where type = 'mobile' and name = 'app_main_section_3'";
        $app_main_section_3 = DB::selectOne($sql_app_main_section_3);

        //이미지 탭
        $sql_image_yn = "select * from conf where type = 'image' and name = 'image_yn'";
        $image_yn = DB::selectOne($sql_image_yn);

        $sql_i_domain = "select * from conf where type = 'image' and name = 'i_domain'";
        $i_domain = DB::selectOne($sql_i_domain);

        $sql_ftp_yn = "select * from conf where type = 'image' and name = 'ftp_yn'";
        $ftp_yn = DB::selectOne($sql_ftp_yn);

        $sql_hostname = "select * from conf where type = 'image' and name = 'hostname'";
        $hostname = DB::selectOne($sql_hostname);

        $sql_username = "select * from conf where type = 'image' and name = 'username'";
        $username = DB::selectOne($sql_username);

        $sql_password = "select * from conf where type = 'image' and name = 'password'";
        $password = DB::selectOne($sql_password);

        $sql_home_dir = "select * from conf where type = 'image' and name = 'home_dir'";
        $home_dir = DB::selectOne($sql_home_dir);

        $values = [
            //상점 탭
            'name' => $name->value ?? '',
            'code'  => $code->value ?? '',
            'phone' => $phone->value ?? '',
            's_domain' => $s_domain->value ?? '',
            'a_domain' => $a_domain->value ?? '',
            'email' => $email->value ?? '',
            'title' => $title->value ?? '',
            'title_main' => $title_main->value ?? '',
            'meta_tag' => $meta_tag->value ?? '',
            'add_script_content' => $add_script_content->value ?? '',
            'sale_place' => $sale_place->value ?? '',
            //주문 탭
            'cash_use_yn' => $cash_use_yn->value ?? '',
            'bank_nm' => $bank_nm,
            'account_no' => $account_no,
            'account_holder' => $account_holder,
            'cancel_period' => $cancel_period->value ?? '',
            //배송 탭
            'base_delivery_fee' => $base_delivery_fee->value ?? '',
            'add_delivery_fee' => $add_delivery_fee->value ?? '',
            'free_delivery_amt' => $free_delivery_amt->value ?? '',
            'wholesale_free_delivery_amt' => $wholesale_free_delivery_amt->value ?? '',
            'wholesale_base_delivery_fee' => $wholesale_base_delivery_fee->value ?? '',
            'wholesale_add_delivery_fee' => $wholesale_add_delivery_fee->value ?? '',
            'day_delivery_yn' => $day_delivery_yn->value ?? '',
            'day_delivery_amt' => $day_delivery_amt->value ?? '',
            'day_delivery_type' => $day_delivery_type->value ?? '',
            'day_delivery_zone' => $day_delivery_zone->value ?? '',
            'dlv_cd' => $dlv_cd->value ?? '',
            //적립금 탭
            'estimate_point_yn' => $estimate_point_yn->value ?? '',
            'estimate_point' => $estimate_point->value ?? '',
            'join_point' => $join_point->value ?? '',
            'policy' => $policy->value ?? '',
            'ratio' => $ratio->value ?? '',
            'return_yn' => $return_yn->value ?? '',
            'point_limit' => $point_limit->value ?? '',
            'p_give_type' => $p_give_type->value ?? '',
            //카카오 탭
            'kakao_yn' => $kakao_yn->value ?? '',
            'sender_key' => $sender_key->value ?? '',
            //SMS 탭
            'sms_yn' => $sms_yn->value ?? '',
            'join_yn' => $join_yn->value ?? '',
            'join_msg' => $join_msg->content ?? '',
            'passwd_yn' => $passwd_yn->value ?? '',
            'passwd_msg' => $passwd_msg->content ?? '',
            'order_yn' => $order_yn->value ?? '',
            'payment_yn' => $payment_yn->value ?? '',
            'payment_msg' => $payment_msg->content ?? '',
            'delivery_yn' => $delivery_yn->value ?? '',
            'delivery_msg' => $delivery_msg->content ?? '',
            'refund_yn' => $refund_yn->value ?? '',
            'refund_msg_complete' => $refund_msg_complete->content ?? '',
            'refund_msg_cancel' => $refund_msg_cancel->content ?? '',
            'out_of_stock_yn' => $out_of_stock_yn->value ?? '',
            'out_of_stock_msg' => $out_of_stock_msg->content ?? '',
            'auth_yn' => $auth_yn->value ?? '',
            'auth_msg' => $auth_msg->value ?? '',
            'order_msg_pay' => $order_msg_pay->value ?? '',
            'order_msg_not_pay' => $order_msg_not_pay->value ?? '',
            'cancel_yn' => $cancel_yn->value ?? '',
            'cancel_msg_bank' => $cancel_msg_bank->value ?? '',
            'cancel_msg_card' => $cancel_msg_card->value ?? '',
            'cancel_msg_transfer' => $cancel_msg_transfer->value ?? '',
            'birth_yn' => $birth_yn->value ?? '',
            'birth_msg' => $birth_msg->value ?? '',
            'welcome_yn' => $welcome_yn->value ?? '',
            'welcome_msg' => $welcome_msg->value ?? '',
            //부가기능 탭
            'ssl_yn' => $ssl_yn->value ?? '',
            'wholesale_yn' => $wholesale_yn->value ?? '',
            'est_confirm_yn' => $est_confirm_yn->value ?? '',
            'new_good_day' => $new_good_day->value ?? '',
            'new_data_day' => $new_data_day->value ?? '',
            'category_goods_cnt' => $category_goods_cnt->value ?? '',
            'newarrival_goods_cnt' => $newarrival_goods_cnt->value ?? '',
            'onsale_goods_cnt' => $onsale_goods_cnt->value ?? '',
            'brandshop_goods_cnt' => $brandshop_goods_cnt->value ?? '',
            'best_rank_goods_cnt' => $best_rank_goods_cnt->value ?? '',
            'relative_goods_cnt' => $relative_goods_cnt->value ?? '',
            'search_goods_cnt' => $search_goods_cnt->value ?? '',
            'search_goods_sort' => $search_goods_sort->value ?? '',
            'counsel_yn' => $counsel_yn->value ?? '',
            'goods_qa_yn' => $goods_qa_yn->value ?? '',
            'init_url' => $init_url->value ?? '',
            'member_inactive_yn' => $member_inactive_yn->value ?? '',
            //게시물 탭
            'community_goods_qa' => $community_goods_qa->value ?? '',
            'community_goods_review' => $community_goods_review->value ?? '',
            'community_main_notice' => $community_main_notice->value ?? '',
            'community_main_qa' =>$community_main_qa->value ?? '',
            'community_main_review' => $community_main_review->value ?? '',
            'main_notice' => $main_notice->value ?? '',
            'notice' => $notice->value ?? '',

            //서비스 탭
            'sabangnet_id' => $sabangnet_id->value ?? '',
            'sabangnet_key' => $sabangnet_key->value ?? '',
            'shoplinker_id' => $shoplinker_id->value ?? '',
            'shoplinker_user_id' => $shoplinker_user_id->value ?? '',
            'ipin_site_cd' => $ipin_site_cd->value ?? '',
            'ipin_site_pw' => $ipin_site_pw->value ?? '',
            'ipin_site_seq' => $ipin_site_seq->value ?? '',

            //모바일 탭
            'm_domain' => $m_domain->value ?? '',
            'm_category_goods_cnt' => $m_category_goods_cnt->value ?? '',
            'm_newarrival_cnt' => $m_newarrival_cnt->value ?? '',
            'm_onsale_goods_cnt' => $m_onsale_goods_cnt->value ?? '',
            'm_brandshop_goods_cnt' => $m_brandshop_goods_cnt->value ?? '',
            'm_best_rank_goods_cnt' => $m_best_rank_goods_cnt->value ?? '',
            'm_search_goods_cnt' => $m_search_goods_cnt->value ?? '',
            'app_main_banner_1' => $app_main_banner_1->value ?? '',
            'app_main_banner_2' => $app_main_banner_2->value ?? '',
            'app_main_section_1' => $app_main_section_1->value ?? '',
            'app_main_section_2' => $app_main_section_2->value ?? '',
            'app_main_section_3' => $app_main_section_3->value ?? '',

            //이미지 탭
            'image_yn' => $image_yn->value ?? '',
            'i_domain' => $i_domain->value ?? '',
            'ftp_yn' => $ftp_yn->value ?? '',
            'hostname' => $hostname->value ?? '',
            'username' => $username->value ?? '',
            'password' => $password->value ?? '',
            'home_dir' => $home_dir->value ?? '',
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
        $add_script_content = $request->input('add_script_content');
        $sale_place = $request->input('sale_place');
        $ut = now();

        //주문 탭
        $cash_use_yn = $request->input('cash_use_yn');
        $bank_nm = $request->input('bank_nm');
        $account_no = $request->input('account_no');
        $account_holder = $request->input('account_holder');
        $cancel_period = $request->input('cancel_period');


        //배송 탭
        $base_delivery_fee = $request->input('base_delivery_fee');
        $add_delivery_fee = $request->input('add_delivery_fee');
        $free_delivery_amt = $request->input('free_delivery_amt');
        $wholesale_free_delivery_amt = $request->input('wholesale_free_delivery_amt');
        $wholesale_base_delivery_fee = $request->input('wholesale_base_delivery_fee');
        $wholesale_add_delivery_fee = $request->input('wholesale_add_delivery_fee');
        $day_delivery_yn = $request->input('day_delivery_yn');
        $day_delivery_type = $request->input('day_delivery_type');
        $day_delivery_amt = $request->input('day_delivery_amt');
        $day_delivery_zone = $request->input('day_delivery_zone');


        //적립금 탭
        $join_point = $request->input('join_point');
        $policy = $request->input('policy');
        $ratio = $request->input('ratio');
        $return_yn = $request->input('return_yn');
        $point_limit = $request->input('point_limit');
        $p_give_type = $request->input('p_give_type');

        //카카오 탭
        $kakao_yn = $request->input('kakao_yn');
        $sender_key = $request->input('sender_key');

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
        $auth_yn = $request->input('auth_yn');
        $auth_msg = $request->input('auth_msg');
        $order_msg_pay = $request->input('order_msg_pay');
        $order_msg_not_pay = $request->input('order_msg_not_pay');
        $cancel_yn = $request->input('cancel_yn');
        $cancel_msg_bank = $request->input('cancel_msg_bank');
        $cancel_msg_card = $request->input('cancel_msg_card');
        $cancel_msg_transfer = $request->input('cancel_msg_transfer');
        $birth_yn = $request->input('birth_yn');
        $birth_msg = $request->input('birth_msg');
        $welcome_yn = $request->input('welcome_yn');
        $welcome_msg = $request->input('welcome_msg');

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
        $init_url = $request->input('init_url');
        $member_inactive_yn = $request->input('member_inactive_yn');

        //게시물 탭
        $community_goods_qa = $request->input('community_goods_qa');
        $community_goods_review = $request->input('community_goods_review');
        $community_main_notice = $request->input('community_main_notice');
        $community_main_qa = $request->input('community_main_qa');
        $community_main_review = $request->input('community_main_review');
        $main_notice = $request->input('main_notice');
        $notice = $request->input('notice');

        //서비스 탭
        $sabangnet_id = $request->input('sabangnet_id');
        $sabangnet_key = $request->input('sabangnet_key');
        $shoplinker_id = $request->input('shoplinker_id');
        $shoplinker_user_id = $request->input('shoplinker_user_id');
        $ipin_site_cd = $request->input('ipin_site_cd');
        $ipin_site_pw = $request->input('ipin_site_pw');
        $ipin_site_seq = $request->input('ipin_site_seq');


        //모바일 탭
        $m_domain = $request->input('m_domain');
        $m_category_goods_cnt = $request->input('m_category_goods_cnt');
        $m_newarrival_cnt = $request->input('m_newarrival_cnt');
        $m_onsale_goods_cnt = $request->input('m_onsale_goods_cnt');
        $m_brandshop_goods_cnt = $request->input('m_brandshop_goods_cnt');
        $m_best_rank_goods_cnt = $request->input('m_best_rank_goods_cnt');
        $m_search_goods_cnt = $request->input('m_search_goods_cnt');
        $app_main_banner_1 = $request->input('app_main_banner_1');
        $app_main_banner_2 = $request->input('app_main_banner_2');
        $app_main_section_1 = $request->input('app_main_section_1');
        $app_main_section_2 = $request->input('app_main_section_2');
        $app_main_section_3 = $request->input('app_main_section_3');

        //이미지 탭
        $image_yn = $request->input('image_yn');
        $i_domain = $request->input('i_domain');
        $ftp_yn = $request->input('ftp_yn');
        $hostname = $request->input('hostname');
        $username = $request->input('username');
        $password = $request->input('password');
        $home_dir = $request->input('home_dir');


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

                $sql_add_script_content = "update conf set value='$add_script_content', ut = '$ut' where type='shop' and name='add_script_content'";
                DB::update($sql_add_script_content);

                $sql_sale_place = "update conf set value='$sale_place', ut = '$ut' where type='shop' and name='sale_place'";
                DB::update($sql_sale_place);
            } elseif ($type == 'order') {
                //주문 탭
                $sql_cash_use_yn = "update conf set value='$cash_use_yn', ut = '$ut' where type='shop' and name='cash_use_yn'";
                DB::update($sql_cash_use_yn);

                $sql_bank = "update conf set value='$bank_nm|$account_no|$account_holder', ut = '$ut' where type='bank' and name='info'";
                DB::update($sql_bank);

                $sql_cancel_period = "update conf set value='$cancel_period', ut = '$ut' where type='order' and name='cancel_period'";
                DB::update($sql_cancel_period);

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

                $sql_wholesale_base_delivery_fee = "update conf set value='$wholesale_base_delivery_fee', ut = '$ut' where type='delivery' and name='wholesale_base_delivery_fee'";
                DB::update($sql_wholesale_base_delivery_fee);

                $sql_wholesale_add_delivery_fee = "update conf set value='$wholesale_add_delivery_fee', ut = '$ut' where type='delivery' and name='wholesale_add_delivery_fee'";
                DB::update($sql_wholesale_add_delivery_fee);

                $sql_day_delivery_yn = "update conf set value='$day_delivery_yn', ut = '$ut' where type='delivery' and name='day_delivery_yn'";
                DB::update($sql_day_delivery_yn);

                $sql_day_delivery_type = "update conf set value='$day_delivery_type', ut = '$ut' where type='delivery' and name='day_delivery_type'";
                DB::update($sql_day_delivery_type);

                $sql_day_delivery_amt = "update conf set value='$day_delivery_amt', ut = '$ut' where type='delivery' and name='day_delivery_amt'";
                DB::update($sql_day_delivery_amt);

                $sql_day_delivery_zone = "update conf set value='$day_delivery_zone', ut = '$ut' where type='delivery' and name='day_delivery_zone'";
                DB::update($sql_day_delivery_zone);

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

                $sql_point_limit = "update conf set value='$point_limit', ut = '$ut' where type='point' and name='point_limit'";
                DB::update($sql_point_limit);

                $sql_p_give_type = "update conf set value='$p_give_type', ut = '$ut' where type='point' and name='p_give_type'";
                DB::update($sql_p_give_type);

            } elseif ($type == 'kakao') {
                //카카오 탭
                $sql_kakao_yn = "update conf set value='$kakao_yn', ut = '$ut' where type='kakao' and name='kakao_yn'";
                DB::update($sql_kakao_yn);

                $sql_sender_key = "update conf set value='$sender_key', ut = '$ut' where type='kakao' and name='sender_key'";
                DB::update($sql_sender_key);

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

                $sql_auth_yn = "update conf set value='$auth_yn', ut = '$ut' where type='sms' and name='auth_yn'";
                DB::update($sql_auth_yn);

                $sql_auth_msg = "update conf set value='$auth_msg', ut = '$ut' where type='sms' and name='auth_msg'";
                DB::update($sql_auth_msg);

                $sql_order_msg_pay = "update conf set value='$order_msg_pay', ut = '$ut' where type='sms' and name='order_msg_pay'";
                DB::update($sql_order_msg_pay);

                $sql_order_msg_not_pay = "update conf set value='$order_msg_not_pay', ut = '$ut' where type='sms' and name='order_msg_not_pay'";
                DB::update($sql_order_msg_not_pay);

                $sql_cancel_yn = "update conf set value='$cancel_yn', ut = '$ut' where type='sms' and name='cancel_yn'";
                DB::update($sql_cancel_yn);

                $sql_cancel_msg_bank = "update conf set value='$cancel_msg_bank', ut = '$ut' where type='sms' and name='cancel_msg_bank'";
                DB::update($sql_cancel_msg_bank);

                $sql_cancel_msg_card = "update conf set value='$cancel_msg_card', ut = '$ut' where type='sms' and name='cancel_msg_card'";
                DB::update($sql_cancel_msg_card);

                $sql_cancel_msg_transfer = "update conf set value='$cancel_msg_transfer', ut = '$ut' where type='sms' and name='cancel_msg_transfer'";
                DB::update($sql_cancel_msg_transfer);

                $sql_birth_yn = "update conf set value='$birth_yn', ut = '$ut' where type='sms' and name='birth_yn'";
                DB::update($sql_birth_yn);

                $sql_birth_msg = "update conf set value='$birth_msg', ut = '$ut' where type='sms' and name='birth_msg'";
                DB::update($sql_birth_msg);

                $sql_welcome_yn = "update conf set value='$welcome_yn', ut = '$ut' where type='sms' and name='welcome_yn'";
                DB::update($sql_welcome_yn);

                $sql_welcome_msg = "update conf set value='$welcome_msg', ut = '$ut' where type='sms' and name='welcome_msg'";
                DB::update($sql_welcome_msg);

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

                $sql_init_url = "update conf set value='$init_url', ut = '$ut' where type='stock_reduction' and name='init_url'";
                DB::update($sql_init_url);

                $sql_member_inactive_yn = "update conf set value='$member_inactive_yn', ut = '$ut' where type='stock_reduction' and name='member_inactive_yn'";
                DB::update($sql_member_inactive_yn);

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
                //서비스 탭
                $sql_sabangnet_id = "update conf set value='$sabangnet_id', ut = '$ut' where type='api' and name='sabangnet_id'";
                DB::update($sql_sabangnet_id);

                $sql_sabangnet_key = "update conf set value='$sabangnet_key', ut = '$ut' where type='api' and name='sabangnet_key'";
                DB::update($sql_sabangnet_key);

                $sql_shoplinker_id = "update conf set value='$shoplinker_id', ut = '$ut' where type='api' and name='shoplinker_id'";
                DB::update($sql_shoplinker_id);

                $sql_shoplinker_user_id = "update conf set value='$shoplinker_user_id', ut = '$ut' where type='api' and name='shoplinker_user_id'";
                DB::update($sql_shoplinker_user_id);

                $sql_ipin_site_cd = "update conf set value='$ipin_site_cd', ut = '$ut' where type='admin' and name='ipin_site_cd'";
                DB::update($sql_ipin_site_cd);

                $sql_ipin_site_pw = "update conf set value='$ipin_site_pw', ut = '$ut' where type='admin' and name='ipin_site_pw'";
                DB::update($sql_ipin_site_pw);

                $sql_ipin_site_seq = "update conf set value='$ipin_site_seq', ut = '$ut' where type='admin' and name='ipin_site_seq'";
                DB::update($sql_ipin_site_seq);

            } else if ($type == 'mobile') {
                //모바일 탭
                $sql_m_domain = "update conf set value='$m_domain', ut = '$ut' where type='mobile' and name='m_domain'";
                DB::update($sql_m_domain);

                $sql_m_category_goods_cnt = "update conf set value='$m_category_goods_cnt', ut = '$ut' where type='mobile' and name='m_category_goods_cnt'";
                DB::update($sql_m_category_goods_cnt);

                $sql_m_newarrival_cnt = "update conf set value='$m_newarrival_cnt', ut = '$ut' where type='mobile' and name='m_newarrival_cnt'";
                DB::update($sql_m_newarrival_cnt);

                $sql_m_onsale_goods_cnt = "update conf set value='$m_onsale_goods_cnt', ut = '$ut' where type='mobile' and name='m_onsale_goods_cnt'";
                DB::update($sql_m_onsale_goods_cnt);

                $sql_m_brandshop_goods_cnt = "update conf set value='$m_brandshop_goods_cnt', ut = '$ut' where type='mobile' and name='m_brandshop_goods_cnt'";
                DB::update($sql_m_brandshop_goods_cnt);

                $sql_m_best_rank_goods_cnt = "update conf set value='$m_best_rank_goods_cnt', ut = '$ut' where type='mobile' and name='m_best_rank_goods_cnt'";
                DB::update($sql_m_best_rank_goods_cnt);

                $sql_m_search_goods_cnt = "update conf set value='$m_search_goods_cnt', ut = '$ut' where type='mobile' and name='m_search_goods_cnt'";
                DB::update($sql_m_search_goods_cnt);

                $sql_app_main_banner_1 = "update conf set value='$app_main_banner_1', ut = '$ut' where type='mobile' and name='app_main_banner_1'";
                DB::update($sql_app_main_banner_1);

                $sql_app_main_banner_2 = "update conf set value='$app_main_banner_2', ut = '$ut' where type='mobile' and name='app_main_banner_2'";
                DB::update($sql_app_main_banner_2);

                $sql_app_main_section_1 = "update conf set value='$app_main_section_1', ut = '$ut' where type='mobile' and name='app_main_section_1'";
                DB::update($sql_app_main_section_1);

                $sql_app_main_section_2 = "update conf set value='$app_main_section_2', ut = '$ut' where type='mobile' and name='app_main_section_2'";
                DB::update($sql_app_main_section_2);

                $sql_app_main_section_3 = "update conf set value='$app_main_section_3', ut = '$ut' where type='mobile' and name='app_main_section_3'";
                DB::update($sql_app_main_section_3);

            } else if ($type == 'image') {
                //이미지 탭
                $sql_image_yn = "update conf set value='$image_yn', ut = '$ut' where type='image' and name='image_yn'";
                DB::update($sql_image_yn);

                $sql_i_domain = "update conf set value='$i_domain', ut = '$ut' where type='image' and name='i_domain'";
                DB::update($sql_i_domain);

                $sql_ftp_yn = "update conf set value='$ftp_yn', ut = '$ut' where type='image' and name='ftp_yn'";
                DB::update($sql_ftp_yn);

                $sql_hostname = "update conf set value='$hostname', ut = '$ut' where type='image' and name='hostname'";
                DB::update($sql_hostname);

                $sql_username = "update conf set value='$username', ut = '$ut' where type='image' and name='username'";
                DB::update($sql_username);

                $sql_password = "update conf set value='$password', ut = '$ut' where type='image' and name='password'";
                DB::update($sql_password);

                $sql_home_dir = "update conf set value='$home_dir', ut = '$ut' where type='image' and name='home_dir'";
                DB::update($sql_home_dir);
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
