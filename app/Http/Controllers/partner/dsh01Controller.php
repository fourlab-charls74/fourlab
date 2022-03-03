<?php

namespace App\Http\Controllers\partner;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use PDO;

class dsh01Controller extends Controller
{
    public function index() {
        $mutable = now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate'         => $sdate,
            'edate'         => date("Y-m-d"),
        ];
        return view(Config::get('shop.partner.view') . '/dashboard/dsh01', $values);
    }

    public function search(Request $req){

        $com_id = Auth('partner')->user()->com_id;

        $mutable = Carbon::now();
        $sdate = $req->input("sdate",$mutable->sub(3, 'month')->format('Y-m-d'));
        $edate = $req->input("edate",date("Y-m-d"));
        $type = $req->input("type","brand");
        $size = $req->input("size","cnt");
        if($size === "cnt"){
            $ord_cnt = "o.qty";
        } else {
            $ord_cnt = "o.recv_amt + o.point_amt";
        }

//        $query = /** @lang text */
//            "
//            select max(qty) as qty from (
//                select
//                    o.goods_no,sum(o.qty) as qty
//                from order_opt o
//                where o.ord_date >= '$sdate'  and o.ord_date < date_add('$edate',interval 1 day)
//                group by o.goods_no
//             ) a
//        ";
//         $row = (array)DB::selectone($query);
//         $max = $row["qty"];
//         $ratio = ($max > 0)? 60/$max:1;

        $query = /** @lang text */
            "
            SELECT
                opt.opt_kind_cd,opt.opt_kind_nm, b.brand,b.brand_nm,o.goods_no,g.goods_nm,
                IFNULL(SUM($ord_cnt), 0) AS ord_cnt,
                IFNULL(SUM(IF(o.clm_state >= 50,$ord_cnt,0)),0) AS clm_cnt
            FROM order_opt o
                INNER JOIN goods g ON o.goods_no = g.goods_no AND o.goods_sub = g.goods_sub
                INNER JOIN brand b ON g.brand = b.brand
                LEFT OUTER JOIN opt opt ON opt.opt_kind_cd = g.opt_kind_cd AND opt.opt_id = 'K'
            WHERE o.com_id = :com_id and o.ord_date >= '$sdate'  AND o.ord_date < DATE_ADD('$edate',INTERVAL 1 DAY)
            GROUP BY opt.opt_kind_cd,g.brand,o.goods_no
            order by ord_cnt desc
        ";
        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare($query);
        $stmt->execute(["com_id" => $com_id]);
        $data = [];
        $chart = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $goods_nm = str_replace("피엘라벤","",$row["goods_nm"]);
            $pattern = '/\((\d+)\)/i';
            $goods_nm =  preg_replace($pattern, '', $goods_nm);

            if($type === "brand"){
                $key1 = $row["brand"];
                $key1_nm = $row["brand_nm"];
                $key2 = $row["opt_kind_cd"];
                $key2_nm = $key2;
            } else {
                $key1 = $row["opt_kind_cd"];
                $key1_nm = $key1;
                $key2 = $row["brand"];
                $key2_nm = $row["brand_nm"];
            }

            if(!isset($data[$key1])){
                $data[$key1] = [
                    'name' => $key1_nm,
                    'color' => 0,
                    'children' => []
                ];
            }
            if(!isset($data[$key1]['children'][$key2])){
                $data[$key1]['children'][$key2] = [
                    'name' => $key2_nm,
                    'color' => 0,
                    'children' => []
                ];
            }

            //$row["color"] = $row["color"] * $ratio;

            //$data[$key1]['children'][$key2]['children'][] = $row;
            $data[$key1]['children'][$key2]['children'][] = [
                "name" => $row["goods_no"],
                "description" => $row["goods_nm"],
                "size" => $row["ord_cnt"],
                "color" => ($row["ord_cnt"] > 0)? round($row["clm_cnt"]/$row["ord_cnt"]*100,2):0,
            ];

            $data[$key1]['color'] += ($row["ord_cnt"] > 0)?  $row["clm_cnt"]/$row["ord_cnt"]*100:0;
            $data[$key1]['children'][$key2]['color'] += ($row["ord_cnt"] > 0)? $row["clm_cnt"]/$row["ord_cnt"]*100:0;
        }

        $key1_index = 0;
        foreach($data as $key1 => $value){
            $chart[] = [
                'name' => $value["name"],
                'color' => $value["color"],
                'children' => []
            ];
            $key2_index = 0;
            foreach($value["children"] as $key2 => $value2){
                $chart[$key1_index]['children'][] = [
                    'name' => $value2["name"],
                    'color' => $value2["color"],
                    'children' => []
                ];
                for($i=0;$i<count($value2['children']);$i++){
                    $chart[$key1_index]['children'][$key2_index]['children'][] = [
                        'name' => $value2['children'][$i]["name"],
                        'description' => $value2['children'][$i]["description"],
                        'color' => $value2['children'][$i]["color"],
                        'size' => $value2['children'][$i]["size"]
                    ];
                }
                $key2_index++;
            }
            $key1_index++;
        }
        //$rows = DB::select($query,);
        return response()->json([
            "data" => $chart
        ]);
    }
}
