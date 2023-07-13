<?php

namespace App\Http\Controllers\head;

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
		$user = Auth::guard('head')->user();
		if ($user !== null) return redirect('/head');
		
        return view( Config::get('shop.head.view') . '/auth/login', [
            "className" => "loginPage"
        ]);
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|min:3',
            'password' => 'required',
        ]);

        $user = Head::where('id','=',$request->email)
            ->first();
    
        if($user!=null && $user->use_yn == 'Y'){
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

				$menu = [];
				$kind['head']  = SLib::getLnbs('head');
				foreach($kind as $key => $kind_val) {
					foreach($kind_val as $menu_val) {
						$arr_menu = (array)$menu_val;
						// 매출/통계(82)와 시스템(104) 메뉴는 시스템관리자만 사용 가능합니다.
						if (!in_array($arr_menu['menu_no'], [82, 104]) || $user->grade === 'S') {
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
					}
					// GNB 캐싱처리
					Cache::forever($key.'_gnb', $menu[$key]);
					// LNB 캐싱처리
					$html[$key] = view(Config::get('shop.'.$key.'.view') . '/layouts/lnb', ['menu_list' => $menu[$key]])->render();
					Cache::forever($key.'_lnb', $html[$key]);
				}

                //return redirect('/head/order/ord01');
                return redirect('/head/dashboard');
            } else {
                return $this->sendFailedLoginResponse($request, 1);
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
            //관리자 승인 안된 ID일 경우    
            if($user!=null && $user->confirm_yn == 'N'){
                $password =  $request->password;
                $user = Head::where('id','=',$request->email)
                        ->where('confirm_yn','=','N')
                        ->where('grade','!=','P')
                        ->where('passwd','=',DB::raw("CONCAT('*', UPPER(SHA1(UNHEX(SHA1('$password')))))"))
                        ->first();
                if($user){
                    return $this->sendFailedLoginResponse($request, 2);
                } 
            }
        }
        return $this->sendFailedLoginResponse($request, 1);
    }
 
    protected function sendFailedLoginResponse(Request $request, $code)
    {
        if($code == 1) { 
            $msg = '회원정보를 정확하게 입력해 주세요.';

            throw ValidationException::withMessages([
                'email' => [$msg],
            ]);
            
        } else if($code == 2){
            $msg = '관리자 승인 후 로그인 할 수 있습니다.';

            throw ValidationException::withMessages([
                'email' => [$msg],
                'code' => [$code],
            ]);
        }
    }

    public function logout(Request $request){
        Auth::guard('head')->logout();
        $request->session()->invalidate();
        return redirect('/head');
    }
}
