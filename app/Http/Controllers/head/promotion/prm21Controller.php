<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm21Controller extends Controller
{
  //
	public function index() {
		$values = [
		];

		return view( Config::get('shop.head.view') . '/promotion/prm21',$values);
	}

	public function search(Request $request){
		// 검색 파라미터
		$subject	= $request->input("evt_sbj");
		$page		= $request->input("page",1);

		// 조건절 설정
		$where = "";
		if ( $subject <> "" ) $where .= " and subject like '%$subject%'";

		
		if ($page < 1 or $page == "") $page = 1;
		$page_size = 100;

		$sql = "
			select
				count(idx) as cnt
			from event_mst
			where 1 = 1 $where
		";
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}

		$sql = "
			select
				a.subject, ifnull(b.total,0) as total, a.is_use, date_format(a.regi_date,'%Y.%m.%d') as regi_date, a.idx
			from event_mst a
				left join ( select count(idx) as total,event_idx from event_comment group by event_idx ) b on ( a.idx = b.event_idx )
			where
				1 = 1
				$where
			order by a.idx desc
			limit $startno,$page_size
		";

		$result = DB::select($sql);

		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($result),
				"page_total" => $page_cnt
			),
			"body" => $result
		]);
	}


	/*
		Function: GetInfomation
		상세 정보 얻기
	*/

	public function GetInfomation($idx = ''){


		$sql = "
			select
				subject,is_use,content,idx
			from
				event_mst
			where
				idx = '$idx'
		";
		$event_info = DB::selectOne($sql);
		return response()->json([
			"code" => 200,
			"event_info" => $event_info
		]);

	}

	/*
		Function: GetInformationList
		 이벤트 댓글 참가자
	*/

	public function GetInformationList($idx = '', Request $request){

		$userid			= $request->input("userid");
		$slct_award		= $request->input("slct_award");
		$page			= $request->input("page", 1);

		// 조건절 설정
		$where = "";
		if ( $slct_award != "" ) $where .= " and award_level = '$slct_award' ";
		if ( $userid != "" ) $where .= " and user_id = '$userid' ";
		
		if ($page < 1 or $page == "") $page = 1;
		$page_size = 100;

		$sql = "
			select
				count(idx) as cnt
			from
				event_comment
			where
				event_idx = '$idx'
				$where
		";
		$row = DB::selectOne($sql);
		$data_cnt = $row->cnt;

		// 페이지 얻기
		$page_cnt=(int)(($data_cnt-1)/$page_size) + 1;
		if($page == 1){
			$startno = ($page-1) * $page_size;
		} else {
			$startno = ($page-1) * $page_size;
		}

		$sql = "
			select
				'' as type, user_id,'' as gift, comment, regi_date, is_show, award_level, idx
			from
				event_comment
			where
				event_idx = '$idx'
				$where
			order by idx desc
			limit $startno,$page_size
		";
		$result = DB::select($sql);
		return response()->json([
			"code" => 200,
			"head" => array(
				"total" => $data_cnt,
				"page" => $page,
				"page_cnt" => count($result),
				"page_total" => $page_cnt
			),
			"body" => $result
		]);
	}

	public function store(Request $request){
		$cmd			= $request->input("cmd");
		$subject		= $request->input("subject");
		$content		= $request->input("content");
		$is_use		= $request->input("use_yn");
		$idx			= $request->input("event_idx");
		
		$return_code = 0;
		$responseText = "";
		
		/*
		echo "cmd : ". $cmd;
		echo "<br>";
		echo "idx : ". $idx;
		echo "<br>";
		echo "content : ". $content;
		echo "<br>";
		*/
		
		if($cmd == "addcmd"){
			/* admin sales/sal03_cmd.php (1) */
			$sql = "
				insert into event_mst(
					subject,is_use,regi_date,content
				)values(
					'$subject','$is_use',now(),'$content'
				)
			";
			
			try {
				DB::insert($sql);
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
				$responseText = "등록 처리 시 장애가 발생했습니다.\\n관리자에게 문의해주세요.";
			};
			

		}else if($cmd == "editcmd"){
			$update_items = [
				"subject"		=> $subject,
				"content"		=> $content,
				"is_use"		=> $is_use
			];

			try {
				DB::table('event_mst')
				->where('idx','=', $idx)
				->update($update_items);
				
				$return_code = 1;
			} catch(Exception $e){
				$return_code = 0;
				$responseText = "수정 처리 시 장애가 발생했습니다.\\n관리자에게 문의해주세요.";
			}

		}

		return response()->json([
			"code" => 200,
			"return_code" => $return_code,
			"responseText"	=> $responseText
		]);

	}

	public function change_list($event_idx = '', Request $request){
		$data = $request->input("data");
		$show_yn = $request->input("show_yn");

		$return_code = 0;
		$responseText = 0;

		//print_r($data);

		$is_show = ($show_yn == "Y") ? "Y":"N";

		for($i=0;$i<count($data);$i++){
			if(!empty($data[$i])){
				$idx = $data[$i];

				$sql = "
					update event_comment set is_show = '$is_show' where idx = '$idx'
				";
				$update_item = [
					"is_show" => $is_show
				];
				try {
					DB::table('event_comment')
					->where('idx','=', $idx)
					->update($update_item);
					
					$return_code = 1;
				} catch(Exception $e){
					$return_code = 0;
					$responseText = "출력여부 변경 시 장애가 발생했습니다.\n관리자에게 문의해주세요.";
				}
				if($return_code == 0) break;
			}
		}

		
		return response()->json([
			"code" => 200,
			"return_code" => $return_code,
			"responseText"	=> $responseText
		]);
	}
	
	/*
		Function: ArrayGjift
		선물 리스트 배열
	*/

	public function ArrayGjift($idx = '', Request $request) {
		$type = $request->input("type");
		$key = $request->input("key");
		$gift_list = array();

		if ( $idx == "7" ) {
			// 선물 리스트 정보
			$gift_list['1'] = "구기미 아빠 120_SB 후드 집업자켓 블루";
			$gift_list['2'] = "구기미 아빠 118_Y 후드 티셔츠";
			$gift_list['3'] = "구기미 아빠 119_R 집업 자켓";
			$gift_list['4'] = "3QR 111 자켓 챠콜";
			$gift_list['5'] = "나이키 스포츠컬처 맨투맨 그레이";
			$gift_list['6'] = "GREEN BANANA wing_black_round";
			$gift_list['7'] = "갱스알루팝_M";
			$gift_list['8'] = "갱스알루팝_L";
			$gift_list['9'] = "갱스알루팝_XL";
			$gift_list['10'] = "리바이스 블랙진";
			$gift_list['11'] = "구기미 아빠 120_G 후드 집업자켓";
			$gift_list['12'] = "3QR 캠퍼스 NP 후드 네이비";
			$gift_list['13'] = "구기미 아빠 118_G 후드 티셔츠";
			$gift_list['14'] = "구기미 아빠 120_I 후드 집업자켓";
			$gift_list['15'] = "나이키 스포츠컬처 맨투맨 지구본 그레이";
			$gift_list['16'] = "3QR 면바지 111C ";
			$gift_list['17'] = "GREEN BANANA cross_black_round";
			$gift_list['18'] = "구기미 아빠 118_I 후드 티셔츠";
			$gift_list['19'] = "3QR 야상 이중 자켓 카키";
			$gift_list['20'] = "구기미 아빠 119_P 집업 자켓";
			$gift_list['21'] = "구기미 아빠 111_I 후드 티셔츠";
			$gift_list['22'] = "3QR 103 쟈켓 네이비";
			$gift_list['23'] = "구기미 아빠 111_G 후드 티셔츠";
			$gift_list['24'] = "구기미 아빠 119_B 집업 자켓";
			$gift_list['25'] = "3QR 카고바지 PC101 카키";
			$gift_list['26'] = "구기미 아빠 111_N 후드 티셔츠";
			$gift_list['27'] = "구기미 아빠 307_BL Mini Bag & Pouch";
			$gift_list['28'] = "구기미 아빠 Mini Bag & Pouch";
			$gift_list['29'] = "구기미 아빠 Mini Bag & Pouch";
			$gift_list['30'] = "구기미 아빠 Mini Bag & Pouch";
			$gift_list['31'] = "투 컬러 니트 모자 _BROWN";
			$gift_list['32'] = "투 컬러 니트 모자_KHAKI";
			$gift_list['33'] = "짐줌 체크모직 캡 퍼플";
			$gift_list['34'] = "짐줌 체크 모직3선캡 블루";
			$gift_list['35'] = "짐줌 벨벳3선캡 블랙";
			$gift_list['36'] = "짐줌 코듀로이사선캡 브라운";
			$gift_list['37'] = "짐줌 모노모직캡 바이올렛 ";
			$gift_list['38'] = "GREEN BANANA crest_beanie";
			$gift_list['39'] = "짐줌 체크코튼캡 핑크";
			$gift_list['40'] = "짐줌 체크모직캡 그린";
			$gift_list['41'] = "짐줌 모노모직캡 라이트블루";
			$gift_list['42'] = "짐줌 모노모직캡 골드";
			$gift_list['43'] = "짐줌 체크코튼3쪽캡 오렌지";
			$gift_list['44'] = "짐줌 헤링본 모직캡 화이트 ";
			$gift_list['45'] = "짐줌 헤링본 모직캡 그레이";
			$gift_list['46'] = "투 컬러 니트 머플러_BROWN";
			$gift_list['47'] = "투 컬러 니트 머플러 _KHAKI";
			$gift_list['48'] = "2007 구김스 365 다이어리 ";
			$gift_list['49'] = "2007 구김스 위클리 다이어리";
			$gift_list['50'] = "구김스 만년 다이어리";
			$gift_list['51'] = "구기미 아빠 501_G 골드바 포켓노트";
			$gift_list['52'] = "구기미 아빠 501_S 실버바 포켓노트";
			$gift_list['53'] = "더 맨리스트 크로스 목걸이";
			$gift_list['54'] = "더 맨리스트 드래곤 공갈 피어싱";
		}else if ( $idx == "8" ) {
			$gift_list['1'] = "나이키SB 줌 FC 검흰 STAND UP SPEAK UP (AIR ZOOM FC BK/WH)";
			$gift_list['2'] = "나이키SB 줌 FC 흰검 STAND UP SPEAK UP (AIR ZOOM FC WH/BK)";
			$gift_list['3'] = "줌 터미네이터 흰빨파 (ZOOM TERMINATOR LOW WH/EN-ROYAL)";
			$gift_list['4'] = "덩크 로우 화이트 메탈릭 실버 (W DUNK LOW W/MSV-ORG)";
			$gift_list['5'] = "컴프타운 ST 블랙화이트풀 (COMPTOWN ST BLACK/RWHITE)";
			$gift_list['6'] = "슈퍼스타 1 화이트블루골드 (SUPERST...";
			$gift_list['7'] = "오가피 2 벨크로 핑크화이트 창고 대방출";
			$gift_list['8'] = "가젤 2 남흰 창고 대방출";
			$gift_list['9'] = "플럭션 블랙실버 (FLUXION BLACK-SILVER)";
			$gift_list['10'] = "벨리어 화이트 팬텀 (BELIER WH/PK/BK)";
			$gift_list['11'] = "쥬욕 아스토 L.E.S 흰검빨 창고 대방출";
			$gift_list['12'] = "쥬욕 리빙턴 브라운 창고 대방출";
			$gift_list['13'] = "째즈 3000 캘리포니아 검흰 (JAZZ 3000 BK/D.GY/WH)";
			$gift_list['14'] = "째즈 오리지널 핑크 GS (JAZZ ORIGINAL GS PNK/WH)";
			$gift_list['15'] = "컨버스 척테일러 슬립 OX 블랙핑크 (CT AS SLIP OX BK/HOT PINK)";
			$gift_list['16'] = "컨버스 올스타 하이 핑크 (CONVERSE ALL STAR HI PINK)";
			$gift_list['17'] = "뉴에라 DOLLAR 검금 (창고대방출)";
			$gift_list['18'] = "뉴에라 SKULL 검흰 (창고대방출)";
			$gift_list['19'] = "뉴에라 JAMAICA RESPERT 초노 (NEW ERA CAP JAMRTBKGR_8)";
			$gift_list['20'] = "뉴에라 LOGO 검빨 (창고대방출)";
			$gift_list['21'] = "뉴에라 MAVERICKS 네이비그린 (NEW ERA CAP NB18NAGRWH)";
			$gift_list['22'] = "나이키 TM PU 숄더백 브라운/핑크 (TM PU SHOULDER S/M BROWN/PNK)";
			$gift_list['23'] = "NIKE 미니백팩 블루 (B4.3-MINI BP UB/BK)";
			$gift_list['24'] = "나이키 B2.4 XS/S 더플/그립백 핫핑크 블랙 (B2.4 XS/S DUFFEL/GRIP HOT PNK/BLK)";
			$gift_list['25'] = "나이키 B2.4 XS/S 더플/그립백 블랙 (그립 B2.4 XS/S DUFL/GRIP GRY/BLK)";
			$gift_list['26'] = "아디다스 샌디에고 스몰 더플백 핑크 (ADIDAS BAG PNK)";
			$gift_list['27'] = "아디다스 디아블로 더플백 핑크 (DIABLO SML SFL-SHERB)";
			$gift_list['28'] = "아디다스 트레이프 잭케리 토트백 핑크/카키 (TREF JAC CARY ALL-CR PINK/KAHKI)";
			$gift_list['29'] = "아디다스 트레이프 잭케리 토트백 블루 (TREF JAC CARY ALL-WV BLUE)";
			$gift_list['30'] = "마계천사 성천사 지브릴 아리에스 1/8";
			$gift_list['31'] = "이웃집 토토로 입체액자 고양이버스";
			$gift_list['32'] = "킹콩 헤드낙커 스카이스크래퍼 (KINGKONG HEAD KNOCKERS)";
			$gift_list['35'] = "클래식 스틸2 그루브 검은 ";
			$gift_list['36'] = "리복 클래식 쥬니어 후레쉬 핑크 플라워";
			$gift_list['37'] = "리복 클레식 레더 흰핑";
		}else if ( $idx == "26" ) {
			$gift_list['11'] = "2008프로야구/SKT";
			$gift_list['12'] = "2008프로야구/KTF";
			$gift_list['13'] = "2008프로야구/LGT";
			$gift_list['21'] = "놈3/SKT";
			$gift_list['22'] = "놈3/KTF";
			$gift_list['23'] = "놈3/LGT";
			$gift_list['31'] = "랜덤/SKT";
			$gift_list['32'] = "랜덤/KTF";
			$gift_list['33'] = "랜덤/LGT";
		}
		
		/*
		if($type == "one"){
			return isset($gift_list[$key])? $gift_list[$key]:"";
		}else{
			for($i=0;$i<100;$i++){
				if(!isset($gift_list[$i])) continue;
				echo $i ."|". $gift_list[$i]."\n";
			}
		}
		*/
		return response()->json([
			"code" => 200,
			"gift_cnt"	=> count($gift_list),
			"gift_list" => $gift_list
		]);
	}

}
