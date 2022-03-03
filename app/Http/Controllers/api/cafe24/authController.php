<?php

namespace App\Http\Controllers\api\cafe24;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller\partner\product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use PDO;

class authController extends Controller
{

    function getToken(Request $request){

        $id	= $request->input("id");
        $key = $request->input("key","");

        $sql = /** @lang text */
            "
            select count(*) as cnt from company
            where com_id = :com_id and api_yn = 'Y' and api_key = :api_key
        ";
        $row = DB::selectone($sql,[
            "com_id" => $id,
            "api_key" => $key
        ]);

        if($row->cnt === 1){

            $sql = /** @lang text */
                    "
                select * from token
                where id = :com_id and api_key = :api_key and expire_dt >= now()
            ";
            $row = (array)DB::selectone($sql,[
                "com_id" => $id,
                "api_key" => $key
            ]);
            $expire_dt = date("Y-m-d H:i:s",strtotime("+1 hours"));

            if($row){
                $accessToken = $row["key"];
                DB::table('token')
                    ->where("id","=",$id)
                    ->where("api_key","=",$key)
                    ->update([
                            'expire_dt' => $expire_dt,
                            'ut' => DB::raw('now()')
                        ]);

            } else {
                $accessToken = base64_encode(md5(uniqid()));
                DB::table('token')
                    ->where("id","=",$id)
                    ->where("api_key","=",$key)
                    ->delete();

                DB::table('token')->insert([
                    'key' => $accessToken,
                    'id' => $id,
                    'api_key' => $key,
                    'type' => 'partner',
                    'expire_dt' => $expire_dt,
                    'rt' => DB::raw('now()'),
                    'ut' => DB::raw('now()'),
                ]);
            }

            return response()->json([ "data" => [
                "accessToken" => $accessToken,
                "expire_dt" => $expire_dt
            ]
            ]);
        } else {
            return response()->json([ "error" => [
                "code" => 401,
                "message" => "Invalid user or password",
                "more_info" => ""
                ]
            ]);
        }
    }

}





