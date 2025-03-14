<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Shop
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
        $path = $request->getPathInfo();
        if($path != "/shop/login" && Auth::guard('head')->check() == false){
            return redirect('/shop/login');
        } else {
            if ($path != "/shop/login" && strpos($path, "shop/api/sabangnet") === false) {

                //매장용 아이디가 아닐때 접근 못하게 세팅
                if(Auth::guard('head')->user()->store_cd == ""){
                    return redirect('/shop/login');
                    exit;
                }

                $id = Auth::guard('head')->user()->id;
                $name = Auth::guard('head')->user()->name;

                $action = $request->route()->action;
                $uri = $request->route()->uri;

                 $white_controllers = [
                    "LOGIN" => 1,
                    "USER" => 1,
                    "INDEX" => 1,
                    "SYS01" => 1,
                    "SYS02" => 1,
                    "SYS03" => 1,
                ];

                $controller = $action["controller"];

                if(preg_match("/(\w+)Controller\@(.+)$/i",$controller,$m)){
                    $pid = strtoupper($m[1]);

                    if(!isset($white_controllers[$pid])){
                        $sql = "
                            select
                                a.menu_no, a.pid, a.kor_nm, a.eng_nm, a.kind
                            from shop_controller a
                            where a.pid = :pid
                            having (
                                    select count(*) from mgr_group_menu_role
                                    where menu_no = a.menu_no
                                        and group_no in ( select group_no from mgr_user_group where id = :id)
                                    ) >= 0
                        ";
                        $menu = (array)DB::selectOne($sql,["pid" => $pid, "id" => $id]);

                        if($menu) {
                            $log = [
                                'menu_no' => $menu['menu_no'],
                                'pid' => $pid,
                                'cmd' => $uri,
                                'menu_nm' => $menu["kor_nm"],
                                'exec_time' => 0,
                                'id' => $id,
                                'name' => $name,
                                'ip' => $request->ip(),
                                'log_time' => DB::raw('now()')
                            ];

                            try {
                                DB::transaction(function () use (&$result,$id,$log) {
                                    DB::table('shop_log')->insert($log);
                                });
                            } catch(Exception $e){
                            }
                        } else {
                        }
                    }
                } 
            } else {
                $id = "";
                $name = "";
            }
            return $next($request);
        }
    }
}
