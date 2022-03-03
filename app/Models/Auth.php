<?php

namespace App\Models;

use App\Components\Lib;
use Illuminate\Support\Facades\DB;

class Auth
{
    private $token = null;
    private $user = null;

    function __construct($token) {
        $this->token = $token;
        $sql = /** @lang text */
            "
            select c.com_id as id,c.com_nm as name
            from company c inner join token t on c.com_id = t.id
            where t.key = :key and expire_dt >= now()
        ";
        $this->user = (array)DB::selectone($sql,["key" => $token]);
    }

    public function isAuth(){
        if(isset($this->user["id"])){
            return true;
        } else {
            return false;
        }
    }

    public function getUser(){
        DB::table('token')
            ->where("key","=",$this->token)
            ->update([
                "expire_dt" => DB::raw("date_add(now(),interval 1 HOUR)")
            ]);
        return $this->user;
    }

    /**
     * 배열 값 얻기
     * @param array $config 설정값
     * @param string $name 항목
     * @param string $default 기본값
     * @return string 배열 값
     */
    public function getValue($config, $name,$default = ""){
      return isset($config[$name]) ? $config[$name]: $default;
    }


}
