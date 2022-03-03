<?php

namespace App\Http\Controllers\partner;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function index()
    {
        $user = Auth::guard('partner')->user();
        return view(Config::get('shop.partner.view') . '/auth/user', [
            'user' => Auth::guard('partner')->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('partner')->user();

        Partner::where('com_id', $user->com_id)->update($request->all());

        return 'success';
    }

    public function category($type)
    {
        $user = Auth::guard('partner')->user();
        $com_id = $user->com_id;

        $sql = /** @lang text */
            "
        select a.d_cat_cd,a.full_nm
        from p_partner_category	p inner join category a on p.cat_cd = a.d_cat_cd and p.cat_type = a.cat_type
        where p.com_id = :com_id and p.cat_type = UPPER('$type') and use_yn = 'Y'
        order by a.d_cat_cd
      ";
        $result = DB::select($sql,['com_id'=>$com_id]);

        return response()->json([
            "code" => 200,
            "body" => $result,
        ]);
    }

    public function menu(){

        $user = Auth::guard('partner')->user();
        $com_id = $user->com_id;

        $sql = /** @lang text */
            "
            select b.brand_logo as profile_img from brand b inner join (
            select brand,count(*) as cnt from goods where com_id = :com_id and sale_stat_cl = 40 order by cnt desc limit 0,1
            ) a on b.brand = a.brand
          ";
        $row = (array)DB::selectone($sql,['com_id'=>$com_id]);
        $profile_img = "";
        if($row && $row["profile_img"] && $row["profile_img"] != ""){
            $profile_img = sprintf("%s%s",config("shop.image_svr"),$row["profile_img"]);
        }
        return response()->json([
            "code" => 200,
            "profile_img" => $profile_img
        ]);
    }
}
