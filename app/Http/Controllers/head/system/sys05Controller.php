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

        $values = [
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
            'cash_use_yn' => $cash_use_yn->value,
            'bank_nm' => $bank_nm,
            'account_no' => $account_no,
            'account_holder' => $account_holder,
            'base_delivery_fee' => $base_delivery_fee->value,
            'add_delivery_fee' => $add_delivery_fee->value,
            'free_delivery_amt' => $free_delivery_amt->value,
            'wholesale_free_delivery_amt' => $wholesale_free_delivery_amt->value,
            'dlv_cd' => $dlv_cd->value,
            'estimate_point' => $estimate_point->value,
            'join_point' => $join_point->value,
            'policy' => $policy->value,
            'ratio' => $ratio->value,
            'return_yn' => $return_yn->value,
            'kakao_yn' => $kakao_yn->value,

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


        
        try {
            if ($type == 'shop') {
                // 상점 탭
                $sql_name = "update conf set value='$name', mvalue='$name', ut = '$ut' where type='shop' and name='name'";
                DB::update($sql_name);

                $sql_code = "update conf set value='$s_code', mvalue='$s_code', ut = '$ut' where type='shop' and name='code'";
                DB::update($sql_code);

                $sql_phone = "update conf set value='$phone', mvalue='$phone', ut = '$ut' where type='shop' and name='phone'";
                DB::update($sql_phone);

                $sql_domain = "update conf set value='$domain', mvalue='$domain', ut = '$ut' where type='shop' and name='domain'";
                DB::update($sql_domain);

                $sql_domain_bizest = "update conf set value='$domain_bizest', mvalue='$domain_bizest', ut = '$ut' where type='shop' and name='domain_bizest'";
                DB::update($sql_domain_bizest);

                $sql_email = "update conf set value='$email', mvalue='$email', ut = '$ut' where type='shop' and name='email'";
                DB::update($sql_email);

                $sql_title = "update conf set value='$title', mvalue='$title', ut = '$ut' where type='shop' and name='title'";
                DB::update($sql_title);

                $sql_title_main = "update conf set value='$title_main', mvalue='$title_main', ut = '$ut' where type='shop' and name='title_main'";
                DB::update($sql_title_main);

                $sql_meta_tag = "update conf set value='$meta_tag', mvalue='$meta_tag', ut = '$ut' where type='shop' and name='meta_tag'";
                DB::update($sql_meta_tag);

                $sql_add_script = "update conf set value='$add_script', mvalue='$add_script', ut = '$ut' where type='shop' and name='add_script'";
                DB::update($sql_add_script);

                $sql_sale_place = "update conf set value='$sale_place', mvalue='$sale_place', ut = '$ut' where type='shop' and name='sale_place'";
                DB::update($sql_sale_place);
            } elseif ($type == 'order') {
                //주문 탭
                $sql_cash_use_yn = "update conf set value='$cash_use_yn', mvalue='$cash_use_yn', ut = '$ut' where type='shop' and name='cash_use_yn'";
                DB::update($sql_cash_use_yn);

                $sql_bank = "update conf set value='$bank_nm|$account_no|$account_holder', mvalue='$bank_nm|$account_no|$account_holder', ut = '$ut' where type='bank' and name='info'";
                DB::update($sql_bank);

            } elseif ($type == 'delivery') {
                //배송 탭
                $sql_base_delivery_fee = "update conf set value='$base_delivery_fee', mvalue='$base_delivery_fee', ut = '$ut' where type='delivery' and name='base_delivery_fee'";
                DB::update($sql_base_delivery_fee);

                $sql_add_delivery_fee = "update conf set value='$add_delivery_fee', mvalue='$add_delivery_fee', ut = '$ut' where type='delivery' and name='add_delivery_fee'";
                DB::update($sql_add_delivery_fee);

                $sql_free_delivery_amt = "update conf set value='$free_delivery_amt', mvalue='$free_delivery_amt', ut = '$ut' where type='delivery' and name='free_delivery_amt'";
                DB::update($sql_free_delivery_amt);

                $sql_wholesale_free_delivery_amt = "update conf set value='$wholesale_free_delivery_amt', mvalue='$wholesale_free_delivery_amt', ut = '$ut' where type='delivery' and name='wholesale_free_delivery_amt'";
                DB::update($sql_wholesale_free_delivery_amt);

            } elseif ($type == 'point') {
                //적립금 탭
                $sql_join_point = "update conf set value='$join_point', mvalue='$join_point', ut = '$ut' where type='point' and name='join_point'";
                DB::update($sql_join_point);

                $sql_policy = "update conf set value='$policy', mvalue='$policy', ut = '$ut' where type='point' and name='policy'";
                DB::update($sql_policy);

                $sql_ratio = "update conf set value='$ratio', mvalue='$ratio', ut = '$ut' where type='point' and name='ratio'";
                DB::update($sql_ratio);

                $sql_join_point = "update conf set value='$return_yn', mvalue='$return_yn', ut = '$ut' where type='point' and name='return_yn'";
                DB::update($sql_join_point);
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