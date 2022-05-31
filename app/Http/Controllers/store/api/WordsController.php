<?php

namespace App\Http\Controllers\head\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
/**
 * api 팝업을 이용하여 input에 쉼표 등으로 구분된 단어들을 불러올 때 사용
 */
class WordsController extends Controller {

    /**
     * 머리말, 꼬리말 단어 선택 화면 랜더링
     */
    public function showAddHeadTail() {
        return view(Config::get('shop.head.view') . "/common/words_head_tail");
    }

    /**
     * 머리말, 꼬리말 단어 선택 화면 쿼리조회
     */
    public function searchAddHeadTail() {
        $sql = "
            SELECT '' as blank,code_val, code_id FROM code WHERE code_kind_cd = 'G_SHOP_DESC' ORDER BY code_val
        ";
        $rows = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

    /**
     * 색상 선택 뷰
     */
    public function showColors() {
        return view(Config::get('shop.head.view') . "/common/words_colors");
    }

    /**
     * 색상 선택 조회
     */
    public function searchColors() {
		$sql = "
            select
                '' as chk, a.code_id, a.code_val as color_nm, a.code_val_eng as color,
                a.use_yn, a.code_seq, a.code_val2 as img1_url, a.code_val3 as img2_url
            from code a
            where
                a.code_kind_cd = 'G_PRODUCTS_COLOR'
            order by a.code_seq
        ";
        $rows = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($rows)
            ),
            "body" => $rows
        ]);
    }

}

