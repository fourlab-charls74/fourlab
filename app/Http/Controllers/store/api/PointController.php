<?php

namespace App\Http\Controllers\head\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use App\Models\Point;

class PointController extends Controller
{
    public function index(Request $req) {

		$point_kinds = $req->input('point_kinds', 1);
		$kind = $point_kinds;

		//상품후기 관련 적립금 지급
		if ($kind === "ESTIMATE") $kind = 11;

		//커뮤니티 관련 적립금 지급
		if ($kind === "BBS") $kind = 12;

		$values = [
			'states' => SLib::getCodes('G_POINT_ST'),
			'types' => SLib::getCodes('G_POINT_TYPE'),
			'kind' => $kind,
			'point_kinds' => $point_kinds,
			'data' => $req->input('data', '')
		];

        return view( Config::get('shop.head.view') . "/common/point", $values);
    }

    public function search(Request $req) {
		$kind = Request("kind", 1);
		$data = Request("data", "");
		$datas = explode(',', $data);

		// 적립금 지급설정 금액
		$point = "0";
		$comment = "";
		
        // 설정 값 얻기
		$conf = new Conf();

		// 상품 후기 관련
		if( $kind == "ESTIMATE" ){
			$point_yn = $conf->getConfigValue("point","estimate_point_yn");
			if( $point_yn == "Y"){
				$point = $conf->getConfigValue("point","estimate_point");
			}

			$comment = "상품후기 작성으로 인한 적립금 지급";
		}

		// 커뮤니티 관련
		if( $kind == "BBS" ){
			$comment = "커뮤니티 게시물 작성으로 인한 적립금 지급";
		}

		$sql = "";
		foreach($datas as $rownum => $data) {
			$_data = explode('|', $data);
			$id = empty($_data[0]) ? '' : $_data[0];
			$no = empty($_data[1]) ? '' : $_data[1];
			$ord_no = empty($_data[2]) ? '' : $_data[2];

			if($sql != "") $sql .= " union ";
			
			$sql .= "
				select '' as chkbox, user_id, name, '$point' as point, '$comment' as comment, '$ord_no' as ord_no, ''  as expire_day, '$no' as no, '$rownum' rownum  from member where user_id = '$id'
			";
		}

		if ($sql == "") return response()->json(null, 400);

		$result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [ 'total' => count($result) ],
			"body" => $result,
			'sql' => $sql
        ]);
	}

	public function apply(Request $req) {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
		];
		
		$state			= Request("state");
		$type			= Request("type");
		$point_kinds	= Request("point_kinds");
		$datas	= Request("datas", "");

		//포인트 클래스 생성
		$point = new Point( $user );

		try {
			DB::beginTransaction();

			foreach($datas as $data) {
				echo 1;
				list($user_id, $user_nm, $point_amt, $point_nm, $ord_no, $expire_day, $no) = explode('|', $data);
				echo 2;
				
				$point->SetUserId($user_id);
				if($ord_no != "") $point->SetOrdNo($ord_no);

				//포인트 추가, 차감 함수 호출
				$point->Admin( $point_amt, $point_nm, $type, $state, $expire_day);

				// 상품평 적립금 지급 처리 시
				if( $point_kinds == "ESTIMATE" ){
					//상품평 적립금 여부 변경
					$sql = "
						update goods_estimate set
							point = point + $point_amt , point_yn = 'Y'
						where no = $no
					";

					DB::update($sql);
				}

				// 커뮤니티 적립금 지급 처리 시
				if( $point_kinds == "BBS" ){	
					// 커뮤니티 적립금 여부 변경
					$sql = "
						update board set
							point = point + $point_amt, point_yn = 'Y'
						where b_no = $no
					";
					
					DB::update($sql);
				}
			}

			DB::commit();
			return response()->json(null, 204);
		} catch(Exception $e) {
			DB::rollBack();
			return response()->json(null, 500);
		}
	}
}
