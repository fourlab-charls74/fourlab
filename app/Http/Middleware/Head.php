<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Head
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*
         * 임시 토큰 구현
         */
        $ssm_key = $request->header("ssm-key",'');
        $ssm_pwd = $request->header("ssm-pwd",'');
        $is_loin = false;

        if($ssm_key != '' && $ssm_pwd != '') {
            $user = \App\Models\Head::where('id', '=', $ssm_key)
                ->where('use_yn', '=', 'Y')
                ->first();

            if ($user) {
                $is_loin = true;
                Auth::guard('head')->login($user, true);
            }
        }


        $path = $request->getPathInfo();
        if($is_loin === false && ($path != "/head/login" && strpos($path, "head/api/sabangnet")  === false) && Auth::guard('head')->check() == false){
            return redirect('/head/login');
        } else {

			if( $path != "/head/login" && strpos($path, "head/api/sabangnet")  === false )
			{
				$id		=  Auth::guard('head')->user()->id;
				$name	=  Auth::guard('head')->user()->name;

                $action = $request->route()->action;
                $uri = $request->route()->uri;

                $white_controllers = [
                    "LOGIN" => 1,
                    "USER" => 1,
                    "DSH01" => 1,
                    "SYS01" => 1,
                    "SYS02" => 1,
                    "SYS03" => 1,
                    "SYS04" => 1,
                    "SYS05" => 1,
                ];

                $controller = $action["controller"];
                if(preg_match("/(\w+)Controller\@(.+)$/i",$controller,$m)){
                    $pid = strtoupper($m[1]);
                    $function = strtoupper($m[2]);
                    if(!isset($white_controllers[$pid])){
                        $sql = /** @lang text */
                            "
                            select
                                a.menu_no,a.pid,a.kor_nm,a.eng_nm,a.kind
                            from mgr_controller a
                            where a.pid = :pid
                            having (
                                    select count(*) from mgr_group_menu_role
                                        where menu_no = a.menu_no
                                            and group_no in ( select group_no from mgr_user_group where id = :id )
                                ) > 0
                        ";
                        $menu = (array)DB::selectone($sql,[
                            "pid" => $pid, "id" => $id
                        ]);

                        if($menu){
                            $log = [
                                'pid' => $pid,
                                'cmd' => $controller,
                                'menu_nm' => $menu["kor_nm"],
                                'exec_time' => 0,
                                'id' => $id,
                                'name' => $name,
                                'ip' => $request->ip(),
                                'log_time' => DB::raw('now()')
                            ];
                            try {
                                DB::transaction(function () use (&$result,$id,$log) {
                                    DB::table('mgr_log')->insert($log);
                                });
                            } catch(Exception $e){
                            }

                        } else {
                            //return abort(401);
                        }
                    }

                } else {
                    if(preg_match("/RedirectController$/i",$controller,$m)){
                    } else {
                        //return abort(401);
                    }

                }
			}
			else
			{
				$id		= "";
				$name	= "";
			}

            return $next($request);
        }
    }
}
