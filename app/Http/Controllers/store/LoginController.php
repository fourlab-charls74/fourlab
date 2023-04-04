<?php

namespace App\Http\Controllers\store;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\Head;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    //
    public function index() {
        return view( Config::get('shop.store.view') . '/auth/login', [
            "className" => "loginPage"
        ]);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|min:3',
            'password' => 'required',
        ]);

        $user = Head::where('id','=',$request->email)
            ->where('use_yn','=','Y')
            ->first();

        if($user){

            $password =  $request->password;
            $user = Head::where('id','=',$request->email)
                ->where('use_yn','=','Y')
                ->where('grade','!=','P')
                ->where('passwd','=',DB::raw("CONCAT('*', UPPER(SHA1(UNHEX(SHA1('$password')))))"))
                ->first();
            if($user){
                Auth::guard('head')->login($user,true);

                $ip = $request->ip;

                DB::table('mgr_user')
                    ->where('id','=',$request->email)
                    ->update([
                        'visit_cnt' => DB::raw("visit_cnt + 1"),
                        'visit_ip' => $ip,
                        'visit_date' => DB::raw('now()')
                    ]);


                $mgr_group_sql = "
                    select group_no from mgr_user_group mug where id = '$request->email';
                ";

                $group_ids = DB::select($mgr_group_sql);

                // LNB 메뉴생성
                $kind = [
                    'store' => []
                ];

                $logistics_group = null;

                //물류그룹만 특정 메뉴 조회
                foreach($group_ids as $group_id) {
                    if($group_id->group_no == '19') {
                        $logistics_group = true;
                        break;
                    }
                }
                
                if($logistics_group) {
                    $kind['store']  = SLib::getSpecialGroupLnbs('store', $group_id->group_no);
                } else {
                    $kind['store']  = SLib::getLnbs('store');
                }

                $menu = [];
                foreach($kind as $key => $kind_val) {
                    foreach($kind_val as $menu_val) {
                        $arr_menu = (array)$menu_val;
                        if(isset($menu[$key][$arr_menu['entry']])) {                   
                            $menu[$key][$arr_menu['entry']]['sub'][$arr_menu['menu_no']] = $arr_menu;              
                        } else {
                            if($arr_menu['main_no']) {
                                $menu[$key][$arr_menu['main_no']]['sub'][$arr_menu['entry']]['sub'][$arr_menu['menu_no']] = $arr_menu;
                            } else {
                                $menu[$key][$arr_menu['menu_no']] = $arr_menu;
                            }
                        }
                    }
                    $html[$key] = view(Config::get('shop.'.$key.'.view') . '/layouts/lnb', ['menu_list' => $menu[$key]])->render();
                    Cache::forever($key.'_lnb', $html[$key]);
                }
                   

                return redirect('/store');
            } else {
                return $this->sendFailedLoginResponse($request);
                //return redirect('/head/login');
            }

//            $password =  $request->password;
//            if($password == $user->passwd){
//                Auth::guard('head')->login($user,true);
//                return redirect('/head');
//            } else {
//                return redirect('/head/login');
//            }
        } else {
        }
        return $this->sendFailedLoginResponse($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => ['회원정보를 정확하게 입력해 주세요.'],
        ]);
    }

    public function logout(Request $request){
        Auth::guard('head')->logout();
        $request->session()->invalidate();
        return redirect('/store');
    }
}
