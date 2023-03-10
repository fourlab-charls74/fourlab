<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Components\Lib;
use App\Components\SLib;
use App\Components\ULib;
use App\Models\Coupon;
use App\Models\Conf;

use Exception;

class prm10Controller extends Controller
{
    private $user = null;

    private $types = [
        'O'		=> '온라인',
        'E'		=> '이벤트',
        'F'		=> '오프라인',
        'C'		=> 'CRM'
    ];

    private $apply = [
        'AG'	=> '전체상품',
        'SC'	=> '대표카테고리',
        'SG'	=> '상품'
    ];

    public function index($type='') {
        $values = [
            'types' => $this->types,
            'use_yn' => SLib::getCodes('USE_YN'),
            'apply' => $this->apply,
            'type' => $type,
            'layout' => $type ? 'head_skote.layouts.master-without-nav' : 'head_skote.layouts.app',
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm10',$values);
    }

    public function show($type, $no = '') {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];
        $coupon = new Coupon($user);

        $values = [
            'type' => $type,
            'coupon_no' => $no,
            'types' => $this->types,
            'apply' => $this->apply,
            'pub_kinds' => SLib::getCodes('COUPON_PUB_TIME'), // netpx old 버젼에 맞춰 작업 (수정 전 코드값 : G_COUPON_PUB_KIND)
            'pub_dup_yn' => SLib::getCodes('G_COUPON_PUB_DUP_YN'),
            'coupon' => []
        ];

        if (!empty($no)) {
        $values['coupon'] = $coupon->getCouponInfo($no);
        }
        return view( Config::get('shop.head.view') . '/promotion/prm10_show',$values);
    }

    public function used_show($coupon_no) {
		$sql = "
            select coupon_nm from coupon where coupon_no = '$coupon_no'
        ";
        $values = [
            'use_yn' => SLib::getCodes('USE_YN'),
            'coupon_no' => $coupon_no,
            'coupon_nm' => DB::selectOne($sql)->coupon_nm
        ];
        return view( Config::get('shop.head.view') . '/promotion/prm10_used',$values);
    }

    public function gift_show() {
        $values = [
            'user_ids' => Request('user_ids', ''),
            'coupon_nos' => Request('coupon_nos', '')
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm10_gift',$values);
    }

    public function auto_show() {
        $sql = "
            SELECT 
                coupon_no,coupon_nm,use_date_type,use_to_date FROM coupon 
            WHERE use_yn = 'Y' AND coupon_type = 'C' 
                AND IF( use_date_type = 'P' ,DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 DAY),'%Y%m%d'),use_to_date) >= DATE_FORMAT(NOW(), '%Y%m%d')
            ORDER BY coupon_no DESC        
        ";
        $conf = new Conf();
        $values = [
            'coupons' => DB::select($sql),
            'new' =>$conf->getConfigValue("coupon","new_member_coupon","")
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm10_auto',$values);
    }

    public function serial_show($coupon_no) {

		$sql = "
            SELECT
                coupon_nm,
                (
                    CASE
                        WHEN coupon_type = 'A' THEN '전체'
                        WHEN coupon_type = 'O' THEN '온라인'
                        WHEN coupon_type = 'E' THEN '이벤트'
                        WHEN coupon_type = 'F' THEN '오프라인'
                        WHEN coupon_type = 'C' THEN 'CRM'
                    END
                ) AS coupon_type
            FROM
                coupon
            WHERE
                coupon_no = '$coupon_no'
        ";

        $values = [
            'use_yn' => SLib::getCodes('USE_YN'),
            'coupon_no' => $coupon_no,
            'coupon' => DB::selectOne($sql)
        ];

        return view( Config::get('shop.head.view') . '/promotion/prm10_serial',$values);
    }

    public function gift_user_search() {
        $user_ids = explode(',', Request("user_ids"));

        $result = DB::table('member')
                    ->select('user_id', 'name')
                    ->whereIn('user_id', $user_ids)
                    ->get();

        return response()->json([
            "code" => 200,
            "head" => [
            'total' => count($result)
            ],
            "body" => $result
        ]);
    }

    public function search() {
        // 검색 Request var
        $coupon_nm		    = Request("coupon_nm");
        $coupon_type		= Request("coupon_type");
        $use_yn			    = Request("use_yn");
        $coupon_apply		= Request("coupon_apply");
        $style_no			= Request("style_no");
        $goods_no			= Request("goods_no");
        $goods_nm			= Request("goods_nm");

        $join = array();
        $where = array();
        $having = "";

        if ($coupon_nm != "") $where[] = "a.coupon_nm LIKE '%" . $coupon_nm . "%'";
        if ($coupon_type != "") $where[] = "a.coupon_type = '" . $coupon_type . "'";
        if ($use_yn != "") $where[] = "a.use_yn = '" . $use_yn . "'";
        if ($coupon_apply != "") $where[] = "coupon_apply = '" . $coupon_apply . "'";
        if ($style_no != "" || $goods_no != "" || $goods_nm != ""){

        $having_where = "";
        if ($style_no != "") $having_where = " and g.style_no = '$style_no' ";

        //if ($goods_no != "") $having_where = " and g.goods_no =  '$goods_no' ";
        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $having_where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                if ($goods_no != "") $having_where .= " and g.goods_no = '" . Lib::quote($goods_no) . "' ";
            }
        }

        if ($goods_nm != "") $having_where = " and g.goods_nm like '$goods_nm%' ";

        $having = "
            having (
            select count(*) from coupon_goods c inner join goods g
            on c.goods_no = g.goods_no and c.goods_sub = g.goods_sub
            where c.coupon_no = a.coupon_no $having_where ) > 0
            ";
        }

        $where_str = ($where) ? "WHERE " . implode("AND \n", $where) : "";

        $sql = /** @lang text */
            "
            SELECT
                '' as chk,
                a.coupon_no, a.coupon_nm,
            (
            CASE
                WHEN coupon_type = 'A' THEN '전체'
                WHEN a.coupon_type = 'O' THEN '온라인'
                WHEN a.coupon_type = 'E' THEN '이벤트'
                WHEN a.coupon_type = 'F' THEN '오프라인'
                WHEN a.coupon_type = 'C' THEN 'CRM'
            END
            ) AS coupon_type_nm,
            a.coupon_type,
            a.pub_fr_date, a.pub_to_date, 
            -- IF (a.use_date_type = 'S', a.use_fr_date, '발급일') as use_fr_date,
            -- IF (a.use_date_type = 'S', a.use_to_date, CONCAT(a.use_date, '일까지')) as use_to_date,
            a.use_fr_date,a.use_to_date,
            a.pub_dup_yn,
            IF (a.coupon_amt_kind = 'W', CONCAT(FORMAT(a.coupon_amt, 0), '원'), CONCAT(a.coupon_per, '%')) AS coupon_amt, '' as pub_time,
            (
            CASE
                WHEN a.coupon_apply = 'AG' THEN '전체상품'
                WHEN a.coupon_apply = 'SC' THEN '대표카테고리'
                WHEN a.coupon_apply = 'SG' THEN '상품'
            END
                ) AS coupon_apply,
                IF (a.pub_cnt = '-1', '무제한', a.pub_cnt) as pub_cnt,
                IFNULL(a.coupon_pub_cnt, 0) as coupon_pub_cnt,
                IFNULL(a.coupon_order_cnt, 0) as coupon_order_cnt,
                a.use_yn,ifnull(m.name,a.admin_id) as admin_nm,a.regi_date
            from coupon a 
                left outer join mgr_user m on a.admin_id = m.id
            $where_str
            $having
            order by a.coupon_no desc
        ";
            //echo "<pre>$sql</pre>";

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [
                "total" => count($result)
            ],
            "body" => $result
        ]);
    }

    public function search_serial($coupon_no) {
		$use_yn     = Request("use_yn");
		$off_serial = Request("off_serial");
		$user_id    = Request("user_id");
		$user_nm    = Request("user_nm");

        $where = array();
        $where_str = "";
        $where[] = "a.coupon_no = '$coupon_no'";

		if($use_yn != "") $where[] = "a.use_yn = '" . $use_yn . "'";
		if($off_serial != "") $where[] = "a.serial = '" . $off_serial . "'";
		if($user_id != "") $where[] = "b.user_id = '" . $user_id . "'";
		if($user_nm != "") $where[] = "c.name = '" . $user_nm . "'";

		if (is_array($where)) $where_str = "WHERE " . implode(" AND ", $where);

		$sql = "
			SELECT
				a.serial, IF(a.use_yn = '', 'N', a.use_yn) AS use_yn,
				b.down_date, admin_id, ifnull(b.user_id,'') as user_id, c.name, IFNULL(a.use_cnt, 0) AS use_cnt, b.use_date
			FROM
				coupon_serial AS a
				LEFT OUTER JOIN coupon_member AS b ON a.coupon_no = b.coupon_no AND a.serial = b.serial
                LEFT OUTER JOIN member AS c ON b.user_id = c.user_id
			$where_str
        ";

        $result = DB::select($sql);

        return response()->json([
        "code" => 200,
        "head" => [
            'total' => count($result)
        ],
        "body" => $result
        ]);
    }

    public function search_used($coupon_no, Request $req) {
		// 검색 Request var
		$user_id			= Request("user_id");
		$user_nm			= Request("user_nm");
		$use_yn			    = Request("use_yn");
		$style_no			= Request("style_no");
		$goods_no			= Request("goods_no");
        $limit              = $req->input("limit", 1000);

        $page = $req->input("page", 1);
        if ($page < 1 or $page == "") $page = 1;

		$where = "";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            // 갯수 얻기
            $sql = /** @lang text */
                " 
                select count(*) as total
                from coupon_member c inner join member m on c.user_id = m.user_id
                where c.coupon_no = '$coupon_no' 
                $where
                ";
            $row = DB::selectOne($sql);
            $total = $row->total;
            if($total > 0){
                $page_cnt = (int)(($total-1)/$page_size) + 1;
            }
        }

		if ($user_id != ""){
			$where .= " and c.user_id =  '$user_id' ";
		}

		if ($user_nm != ""){
			$where .= " and m.name like '$user_nm%' ";
        }

		if ($use_yn != ""){
			$where .= " and c.user_yn = '$use_yn' ";
        }

		if ($style_no != ""){
			$where .= " and g.style_no like '$style_no%'";
		}

		if ($goods_no != ""){
			$where .= " and o.goods_no = '$goods_no'";
		}

		$sql = "
			select '' as chk, c.idx,c.user_id, m.name,c.down_date,c.use_date,o.ord_no,c.serial, o.ord_opt_no
			from coupon_member c inner join member m on c.user_id = m.user_id
					left outer join order_opt o on c.ord_opt_no = o.ord_opt_no
					left outer join goods g on o.goods_no = g.goods_no and o.goods_sub = g.goods_sub
            where c.coupon_no = '$coupon_no' 
            $where
			order by idx desc
            limit $startno, $page_size
        ";

        $row = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($row)
            ),
            "body" => $row
        ]);
    }

    public function grid_data($type, $no)
    {
        switch($type) {
            case "product" :
                $sql = "
                    select
                        '' as c,
                        a.goods_no, a.goods_sub, b.style_no, e.com_nm, d.brand_nm, b.goods_nm, c.code_val sale_stat_cl, b.com_id
                    from
                        coupon_goods as a
                        inner join goods as b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
                        inner join code as c on code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = c.code_id
                        inner join brand as d on d.brand = b.brand
                        left outer join company e on b.com_id = e.com_id
                    where
                        a.coupon_no = '$no'
                ";
                break;
            case "exProduct" :
                $sql = "
                    select
                        '' as c,
                        a.goods_no, a.goods_sub, b.style_no, e.com_nm, d.brand_nm, b.goods_nm, c.code_val sale_stat_cl, b.com_id
                    from
                        coupon_goods_ex as a
                        inner join goods as b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
                        inner join code as c on code_kind_cd = 'G_GOODS_STAT' and b.sale_stat_cl = c.code_id
                        inner join brand as d on d.brand = b.brand
                        left outer join company e on b.com_id = e.com_id
                    where
                        a.coupon_no = '$no'
                ";
                break;
            case "company" :
                $sql = "
                    SELECT '' AS c, b.com_nm, a.com_rat, a.ut, a.com_id
                    FROM coupon_company AS a
                    INNER JOIN company AS b ON a.com_id = b.com_id
                    WHERE a.coupon_no = '$no'
                    ORDER BY b.com_type, b.com_id
                ";
                break;
            default : break;
        }

        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => [
            'total' => count($result)
            ],
            "body" => $result
        ]);
    }

    public function edit_auto_coupon($coupon_no) {
        $sql = "
			delete from conf where type = 'coupon' and name = 'new_member_coupon' 
        ";
        DB::delete($sql);

        if ($coupon_no > 0) {
            $sql = "
                insert into conf ( type, name,idx,value, mvalue,content,rt,ut) values (
                    'coupon','new_member_coupon','1', '$coupon_no', '$coupon_no','',now(),now()
                )
            ";

            DB::insert($sql);
        }

        return response()->json(null, 204);
    }

    public function gift_coupon() {
		$coupon_nos	= Request("coupon_nos");
        $user_ids	= Request("user_ids");
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        $coupon = new Coupon($user);

        try {
            DB::beginTransaction();

            foreach($coupon_nos as $coupon_no) {
                $result = $coupon->couponAdd($user_ids, $coupon_no);

                if($result == -2) {
                    throw new Exception("지급하려는 쿠폰수가 쿠폰의 발행수보다 많습니다.발행수를 늘리거나 지급하려는 회원수를 조정 후 다시 처리하여 주십시오.");
                }

                if($result == -1) {
                    throw new Exception("쿠폰 지급 시 오류가 발생하였습니다.다시 시도해 주십시오.");
                }
            }

            DB::commit();

            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function gift_coupon_search() {
        $coupon_nos = explode(',', Request("coupon_nos"));

        $result = DB::table('coupon')
                    ->select('coupon_no', 'coupon_type', 'coupon_nm')
                    ->whereIn('coupon_no', $coupon_nos)
                    ->get();

        return response()->json([
            "code" => 200,
            "head" => [
              'total' => count($result)
            ],
            "body" => $result
        ]);
    }

    public function del_used_coupon($coupon_no) {
        $idxs = implode(',', Request('idxs', []));
		$sql = "
			delete from coupon_member
            where coupon_no = '$coupon_no' 
              and idx in ( $idxs ) and ifnull(ord_opt_no,'') = ''
        ";
        DB::delete($sql);

		$sql = "
            update coupon a set coupon_pub_cnt = ( select count(*) from coupon_member where coupon_no = a.coupon_no )
            where coupon_no = '$coupon_no'
        ";
        DB::update($sql);
        return response()->json(null, 204);
    }

    public function add_coupon(Request $req) {
        $values = $this->__get_coupon_data($req);
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];

        try {
            DB::beginTransaction();

            $coupon = new Coupon($user);
            $coupon_no = $coupon->setCouponInfo($values);
            // return $coupon_no;
            $coupon->delSerial($coupon_no);

            $oldGoods = $this->__get_old_goods($coupon_no);

            list($goods, $ex_goods) = $this->__set_link_data($coupon_no, $coupon);

            if($values['serial_dup_yn'] == "Y"){
                $values['pub_cnt'] = 1;
            }

            if ($values['serial_yn'] == "Y") {
                $coupon->MakeCouponSerialNumber($coupon_no, abs($values['pub_cnt']));
            }

            DB::commit();

            $this->__set_goods($oldGoods, $goods, $coupon);

            return response()->json([ 'coupon_no' => $coupon_no ], 201);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

	public function edit_coupon($coupon_no, Request $req) {

		$values	= $this->__get_coupon_data($req);
		$values['coupon_no']	= $coupon_no;
		$user	= [
			'id'	=> Auth('head')->user()->id,
			'name'	=> Auth('head')->user()->name
		];

		try {
			DB::beginTransaction();

			$coupon		= new Coupon($user);
			$coupon_no	= $coupon->modCouponInfo($values);

			$coupon->delLinkData($coupon_no);

			$oldGoods	= $this->__get_old_goods($coupon_no);

			list($goods, $ex_goods)	= $this->__set_link_data($coupon_no, $coupon);
			// return response()->json($goods, 201);

			DB::commit();

			$this->__set_goods($oldGoods, $goods, $coupon);

			// DB::rollback();
			return response()->json(null, 204);
		}catch(Exception $e) {
			DB::rollback();
			return response()->json(['message' => $e->getMessage()], 500);
		}
	}

    public function del_coupon($coupon_no) {
        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name
        ];
		$coupon = new Coupon($user);

		$oldGoods = array();

		$sql = "
			SELECT goods_no, goods_sub
			  FROM coupon_goods
			 WHERE coupon_no = '$coupon_no'
		";

        $rows = DB::select($sql);

        foreach($rows as $row) {
            $oldGoods[] = $row->goods_no.'|'.$row->goods_sub;
        }

        try {
            DB::beginTransaction();

            // 쿠폰 기본정보 삭제
            $coupon -> delCouponInfo($coupon_no);

            // 쿠폰 관련 정보 삭제
            $coupon -> delGoods($coupon_no);
            $coupon -> delGoodsEx($coupon_no);
            $coupon -> delCompany($coupon_no);
            $coupon -> delSerial($coupon_no);

            DB::commit();

            $coupon -> uptGoods($oldGoods);

            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function __set_goods($oldGoods, $goods, Coupon $coupon) {
        $goods = array_merge($oldGoods, $goods);
        $goods = array_unique($goods);
        $coupon->uptGoods($goods);
    }

    private function __set_link_data($coupon_no, Coupon $coupon) {
		// 상품이 배열이 아닌 ^구분자로 변형
		$goods = @explode("^", Request('goods'));
        $ex_goods = @explode("^", Request('ex_goods'));

        $coupon_apply = Request('coupon_apply');
        $com_id = Request("com_id");
        $com_rat = Request("com_rat");

		if ($coupon_apply == "AG"){
			$coupon->setGoods($coupon_no, array('99999999|0'));
			$coupon->setGoodsEx($coupon_no, $ex_goods);
		} else if ($coupon_apply == "SG"){
			$coupon->setGoods($coupon_no, $goods);
        }

        $coupon->setCompany($coupon_no, $com_id, $com_rat);

        return [$goods, $ex_goods];
    }

    private function __get_old_goods($coupon_no) {
		$sql = "
            SELECT goods_no, goods_sub
            FROM coupon_goods
            WHERE coupon_no = '$coupon_no'
        ";

        $rows = DB::select($sql);
        $oldGoods = [];

        foreach($rows as $row) {
			$oldGoods[] = $row->goods_no. "|" . $row->goods_sub;
        }

        return $oldGoods;
    }

	private function __get_coupon_data(Request $req) {
		$all = $req->all();

		$all['admin_id']			= Auth('head')->user()->id;
		$all['pub_time']			= Request('pub_time');
		$all['pub_type']			= Request('pub_type');
		$all['pub_dup_yn']			= Request('pub_dup_yn', "N");
		$all['coupon_type']			= Request('coupon_type', '');
		$all['coupon_pub_kind']		= Request('coupon_pub_kind', '');
        $all['use_date_alarm_yn']   = Request('use_date_alarm_yn', "");
        $all['use_date_alarm']      = Request('use_date_alarm_day', "");
        $all['use_date']			= $all['use_date'] ? Lib::uncm($all['use_date']) : 0;

        // 발급일 기준 유효기간 추가
        $use_date_type = $all['use_date_type'];
        if ($use_date_type == "P") {
            $use_date = $all['use_date'];
            $use_fr_date = $all['pub_fr_date'];
            $use_to_date = strtotime($all['pub_fr_date']."+${use_date} days");
            $all['use_fr_date'] = $use_fr_date;
            $all['use_to_date'] = date("Ymd", $use_to_date);
        }

		$all['coupon_amt']			= $all['coupon_amt_kind'] == "W" ? $all['coupon_amt_values'] : 0;
		$all['coupon_per']			= $all['coupon_amt_kind'] == "P" ? $all['coupon_amt_values'] : 0;

		$all['pub_cnt']				= Lib::uncm($all['pub_cnt']);
		
		$all['low_price']			= Lib::uncm($all['low_price']);
		$all['high_price']			= Lib::uncm($all['high_price']);
		$all['coupon_amt']			= Lib::uncm($all['coupon_amt']);
		$all['coupon_per']			= Lib::uncm($all['coupon_per']);

		//if( $all['pub_type'] == "W" )	$all['pub_day'] = $all['pub_dayofweek'];

		$all['old_src']				= Request('ord_src','');
		$all['coupon_url']			= Request('coupon_url','');

		if( $req->del_image_yn === "Y" && !empty($req->old_src) ) {
			ULib::deleteFile(str_replace('/storage', '', $req->old_src));

			if( empty($all['src']) ) {
				$all['coupon_img'] = "";
			}
		}

		if( !empty($req->src) ) {
			//$path	= '/head/promotion/coupon/';
            // $all['coupon_img']	= ULib::uploadBase64img($path, $req->src);

			$path	= '/images/coupon_img';
            $src = $req->src;

            $image = preg_replace('/data:image\/(.*?);base64,/', '', $src);
            $file_name = sprintf("%s.jpg", date('YmdHis'));
            $save_file = sprintf("%s/%s", $path, $file_name);

            if(!Storage::disk('public')->exists($path)){
                Storage::disk('public')->makeDirectory($path);
            }

            Storage::disk('public')->put($save_file, base64_decode($image));

			$all['coupon_img']	= $save_file;
		}else{
			$all['coupon_img']	= $all['old_src'];
		}

		$all['pub_cnt']	= $all['ck_pub_cnt_kind'] == 1 ? $all['pub_cnt'] : '-1';

		return $all;

        
	}
}
