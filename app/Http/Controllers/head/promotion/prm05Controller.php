<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class prm05Controller extends Controller
{
    private $pages = ["main"=>"메인", "submain"=>"서브메인", "category"=>"카테고리", "etc"=>"기타"];
    private $types = ['H' => 'HTML'];
    //
    public function index() {
        $values = [
            'pages' => $this->pages,
            'types' => $this->types,
            'use_yn' => SLib::getCodes('USE_YN')
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm05',$values);
    }

    public function show($type, $code = '') {
        $sql = "
            select a.*
            from banner a
            where a.code = '$code'
        ";
        
        $values = [
            'pages' => $this->pages,
            'type' => $type,
            'code' => $code,
            'banner' => DB::selectOne($sql),
            'main_code_htmls' => [
                '$banner'.".ar.$code.contents",
                '@foreach($banner.ARS.$code as $val)',
                '$val->contents',
                '@endforeach'
            ]
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm05_show',$values);
    }

    public function search() {
        // 검색 Request var
        $code			= Request("code");
        $subject		= Request("subject");
        $arcd			= Request("arcd");
        $area			= Request("area");
        $use_yn		    = Request("use_yn");
        $limit		    = Request("limit");
        $page_type      = Request("page_type");

        $where = "";

        if($code != "")		    $where .= " and a.code LIKE '%".$code."%'";
        if($arcd != "")		    $where .= " and a.arcd LIKE '%".$arcd."%'";
        if($subject != "")	    $where .= " and a.subject LIKE '%".$subject."%'";
        if($page_type != "")	$where .= " and a.page = '".$page_type."'";
        if($area != "")		    $where .= " and a.area like '%".$area."%'";
        if($use_yn != "")		$where .= " and a.use_yn = '".$use_yn."'";

        $page = Request("page", 1);
        if ($page < 1 or $page == "") $page = 1;

        $limit		    = Request("limit", 100);
        $ord_field	    = Request("ord_field","a.code");
        $ord			= Request("ord","asc");

        $page_size = $limit;

        if($ord_field == "a.arcd"){
            $str_order_by = sprintf(" order by a.arcd %s,a.seq, a.code %s ",$ord,$ord);
        } else {
            $str_order_by = sprintf(" order by %s %s ",$ord_field,$ord);
        }

        $arr_header = ["page" => $page, "str_order_by" => $str_order_by];

        if ($page == 1)
        {
            $sql = "
				select
					count(*) as cnt
				from banner a
				where 1=1 $where
			";
            $row = DB::selectOne($sql);
            $data_cnt = $row->cnt;

            // 페이지 얻기
            $page_cnt=(int)(($data_cnt-1)/$page_size) + 1;

            $startno = ($page-1) * $page_size;
            $arr_header['total'] = $data_cnt;
            $arr_header['page_cnt'] = $page_cnt;
        }
        else
        {
            $startno = ($page-1) * $page_size;
        }

        $sql = "
			select a.code, a.page, a.arcd, a.area, a.subject, a.type, a.seq, a.click, a.order, a.use_yn, a.rt, a.ut
			from banner a
			where 1=1 $where
			$str_order_by
			limit $startno, $page_size
		";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $result
        ]);
    }

    public function edit_banner($code='') {
        try {
            $chk = $this->__contents_check();
    
            if ($chk[0] === false) {
                throw new Exception($chk[1]);
            }

            DB::beginTransaction();

            DB::table('banner')->where('code', $code)->update([
                'arcd' => Request("arcd", ""),
                'type' => Request("type", ""),
                'subject' => Request("subject" , ""),
                'contents' => Request("contents"),
                'url1' => Request("url1"),
                'url2' => Request("url2"),
                'url3' => Request("url3"),
                'url4' => Request("url4"),
                'url5' => Request("url5"),
                'url6' => Request("url6"),
                'url7' => Request("url7"),
                'url8' => Request("url8"),
                'url9' => Request("url9"),
                'url10' => Request("url10"),
                'target1' => Request("target1"),
                'target2' => Request("target2"),
                'target3' => Request("target3"),
                'target4' => Request("target4"),
                'target5' => Request("target5"),
                'target6' => Request("target6"),
                'target7' => Request("target7"),
                'target8' => Request("target8"),
                'target9' => Request("target9"),
                'target10' => Request("target10"),
                'page' => Request("page"),
                'area' => Request("area"),
                'use_yn' => Request("use_yn"),
                'seq' => Request("seq"),
                'ut' => now()
            ]);

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function code($code='') {
        $sql = "select count(*) as cnt from banner where code = '$code'";

        return response()->json([
            'cnt' => DB::selectOne($sql)->cnt
        ], 201);
    }

    public function delete_banner($code='') {
        DB::table('banner')
            ->where('code', $code)
            ->delete();

        return response()->json(null, 204);
    }
    
    public function add_banner() {
        $code = Request("code");
        try {
            $chk = $this->__contents_check();

            if ($chk[0] === false) throw new Exception($chk[1]);

            $sql = "select count(*) as cnt from banner where code = '$code'";

            $row = DB::selectOne($sql);

            if ($row->cnt > 0) throw new Exception("사용중인 코드입니다. 코드를 변경해주십시오."); 

            DB::beginTransaction();

            $id = DB::table('banner')->insertGetId([
                'code' => $code,
                'arcd' => Request("arcd", ""),
                'type' => Request("type", ""),
                'subject' => Request("subject" , ""),
                'contents' => Request("contents"),
                'url1' => Request("url1"),
                'url2' => Request("url2"),
                'url3' => Request("url3"),
                'url4' => Request("url4"),
                'url5' => Request("url5"),
                'url6' => Request("url6"),
                'url7' => Request("url7"),
                'url8' => Request("url8"),
                'url9' => Request("url9"),
                'url10' => Request("url10"),
                'target1' => Request("target1"),
                'target2' => Request("target2"),
                'target3' => Request("target3"),
                'target4' => Request("target4"),
                'target5' => Request("target5"),
                'target6' => Request("target6"),
                'target7' => Request("target7"),
                'target8' => Request("target8"),
                'target9' => Request("target9"),
                'target10' => Request("target10"),
                'page' => Request("page"),
                'area' => Request("area"),
                'use_yn' => Request("use_yn"),
                'seq' => Request("seq", 0),
                'click' => 0,
                'order' => 0,
                'admin_id' => Auth('head')->user()->id,
                'rt' => now(),
                'ut' => now()
            ]);

            DB::commit();

            return response()->json(Request("code"), 201);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function banner_reset() {
        $codes = Request('codes', []);

        try {
            DB::beginTransaction();
            
            foreach($codes as $code) {
                $sql = "
                    update banner set
                        `click` = 0,
                        `order` = 0,
                        ut = now()
                    where `code` = '$code'
                ";
                DB::update($sql);
            }

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    private function __contents_check() {
        $result = true;
        $msg = "";
        // contents 검증
        for($i=1;$i<=10;$i++) {
            if(!$result || Request('url'.$i) == "") continue;
            
            $pattern = "<link$i>.+<\/link$i>";

            if(preg_match("/$pattern/i",str_replace("\n","",Request('contents')))){
                continue;
            } else {
                $result = false;
                $msg = "입력하신 배너 태그에서 오류가 확인되었습니다.'<link$i> ~ </link$i>' 와 같은 형식으로 입력해주시기 바랍니다.";
            }
        }

        return [$result, $msg];
    }

    /**
     *  memcache 등록 / 삭제
     * @param string $cmd 명령구분
     * @param string $code 배너코드
     * @param int $ttl TTL
     */
    public function cache($cmd, $code, $ttl = 0)
    {
        // if($this->cfg_cache != null && isset($this->cfg_cache["use"]) == "Y")
        // {
            // $cache = new Cache($this->cfg_cache);
            // $cache_key_banner = sprintf("bz.%s.front.common.banner.%s",$cache->site,$code);
            // if($cache != null)
            // {
            //     if($cmd == "set")
            //     {
            //         $sql = "
			// 			select * from banner where code = ?
			// 		";
            //         $input_arr = array("code" => $code);
            //         $rs = $conn->Execute($sql, $input_arr);
            //         if(!$rs->EOF)
            //         {
            //             $row = $rs->fields;
            //             $contents = "";
            //             if($row["use_yn"] == "Y")
            //             {
            //                 $contents = $row["contents"];
            //                 $stag_to = "";
            //                 $etag_to = "";

            //                 // 멀티링크 처리
            //                 for($i=1;$i<=10;$i++)
            //                 {
            //                     if(isset($row["url".$i]))
            //                     {
            //                         $target = $row["target".$i];

            //                         $stag = sprintf("<link%s>",$i);
            //                         $etag = sprintf("</link%s>",$i);

            //                         $stag_to .= sprintf("<a href=\"/app/banner/check/%s/%s\"",$code,$i);
            //                         if($target != "") $stag_to .= sprintf(" target=\"%s\"",$target);
            //                         $stag_to .= ">";
            //                         $etag_to = "</a>";
            //                         $contents = str_replace($stag,$stag_to,$contents);
            //                         $contents = str_replace($etag,$etag_to,$contents);
            //                     } else {
            //                     }
            //                 }
            //             }
            //             $banner["contents"] = $contents;
            //             $cache->set($cache_key_banner, $banner,$ttl);
            //         }
            //     }
            //     else if($cmd == "delete")
            //     {
            //         $cache->delete($cache_key_banner);
            //     }
            // }
        // }
    }

    /**
     * 클릭 및 주문 초기화
     * @param object $conn DB커넥션
     */
    public function ResetCache($ttl = 0)
    {
        if($this->cfg_cache != null && isset($this->cfg_cache["use"]) == "Y")
        {
            $cache = new Cache($this->cfg_cache);
            $cache_key_banner = sprintf("bz.%s.front.common.banners",$cache->site);
            if($cache != null)
            {
                $cache->set($cache_key_banner, null,$ttl);
                echo "1";
            } else {
                echo "-1";
            }
        } else {
            echo "-1";
        }
    }
}
