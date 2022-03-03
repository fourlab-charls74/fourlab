<?php

namespace App\Http\Controllers\partner;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    //
    public function index() {
        echo  Auth::guard('partner')->user();
        return view( Config::get('shop.partner.view') . '/auth/login', [
            "className" => "loginPage"
        ]);
    }

    public function login(Request $request) {

        $request->validate([
            'email' => 'required|min:3',
            'password' => 'required',
        ]);

        $user = Partner::where('com_id','=',$request->email)
            ->where('use_yn','=','Y')
            ->first();
        if($user){
            $password =  $request->password;
            if($password == $user->pwd){
                Auth::guard('partner')->login($user,true);
                return redirect('/partner');
            } else {
                return redirect('/partner/login');
            }
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
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        return redirect('/partner');
    }
}
