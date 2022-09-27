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

        $sql_add_script = "select * from conf where type = 'shop' and name = 'add_script'";
        $add_script = DB::selectOne($sql_add_script);

        $sql_sale_place = "select * from conf where type = 'shop' and name = 'sale_place'";
        $sale_place = DB::selectOne($sql_sale_place);

        //주문탭
        $sql_cash_use_yn = "select * from conf where type = 'shop' and name = 'cash_use_yn'";
        $cash_use_yn = DB::selectOne($sql_cash_use_yn);

        $sql_bank = "select * from conf where type = 'bank' and name = 'info'";
        $bank = DB::selectOne($sql_bank);

        $account = $bank->value;
        $bank_arr = explode("|", $account);
        $bank_nm = $bank_arr[0];
        $account_no = $bank_arr[1];
        $account_holder = $bank_arr[2];

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

        ];


        return view(Config::get('shop.head.view') . '/system/sys05', $values);
    }

    public function update(Request $request)
    {
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
        
        
        
        
        try {
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