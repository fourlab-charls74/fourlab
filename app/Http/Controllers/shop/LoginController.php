<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Head;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    //
    public function index() {
        return view( Config::get('shop.shop.view') . '/auth/login', [
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
                //->where('passwd','=',DB::raw("password('$password')"))
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

                return redirect('/shop');
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
        return redirect('/shop');
    }
}
