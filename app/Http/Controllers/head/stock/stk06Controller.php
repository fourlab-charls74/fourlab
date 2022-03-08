<?php

namespace App\Http\Controllers\head\stock;
use App\Components\Lib;
use App\Components\SLib;
use App\Http\Controllers\Controller;
use App\Models\Conf;
use App\Models\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class stk06Controller extends Controller
{
    public function index() {
		$default_goods_stat	= "40";	//판매중 상태 디폴트
        $values = [
			'goods_types'	=> SLib::getCodes('G_GOODS_TYPE'),
			'default_goods_stat'	=> $default_goods_stat,
			'goods_stats'	=> SLib::getCodes('G_GOODS_STAT'),
			'items'			=> SLib::getItems(),
			'is_unlimiteds'	=> SLib::getCodes('G_IS_UNLIMITED'),
			'alter_reasons'	=> SLib::getCodes('G_JAEGO_REASON'),
        ];
        return view( Config::get('shop.head.view') . '/stock/stk06', $values);
    }

    public function search(Request $request){
        $page = $request->input('page',1);
        if ($page < 1 or $page == "") $page = 1;
        $limit = $request->input('limit',100);

        $goods_no = $request->input("goods_no"); // product number
        $goods_type = $request->input("goods_type"); // product type
        $goods_stat = $request->input("goods_stat"); // product status
        $goods_nm = $request->input("goods_nm"); // producct name
        $goods_nos = $request->input("goods_nos"); // product number textarea

        $style_no = $request->input("style_no"); // style number
        $style_nos = $request->input("style_nos"); // style number textarea

        $brand_nm = $request->input("brand_nm"); // brand
        $brand_cd = $request->input("brand_cd"); // brand

        $limit = $request->input("limit", 100); // list
        $ord_field	= Request("ord_field", ""); // order field
        $ord		= Request("ord", "asc"); // order

        $head_desc = $request->input("head_desc");
        $restock_ncnt = $request->input("restock_ncnt");

        $where = "";
        $having = "";

        if( $goods_type != "" )		$where .= " and g.goods_type = '$goods_type' ";
        if( $goods_stat != "" )		$where .= " and g.sale_stat_cl in ($goods_stat) ";

        if( $style_nos != "" ) {
            $style_no = $style_nos;
        }
        $style_no = preg_replace("/\s/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);
        $style_no = preg_replace("/\t/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);
        $style_no = preg_replace("/\n/",",",$style_no);
        $style_no = preg_replace("/,,/",",",$style_no);

        if( $style_no != "" ) {
            $style_nos = explode(",",$style_no);
            if(count($style_nos) > 1){
                if(count($style_nos) > 500) array_splice($style_nos,500);
                $in_style_nos = "";
                for($i=0; $i<count($style_nos); $i++){
                    if(isset($style_nos[$i]) && $style_nos[$i] != ""){
                        $in_style_nos .= ($in_style_nos == "") ? "'$style_nos[$i]'" : ",'$style_nos[$i]'";
                    }
                }
                if($in_style_nos != "") {
                    $where .= " and g.style_no in ( $in_style_nos ) ";
                }
            } else {
                $where .= " and g.style_no like '$style_no%' ";
            }
        }

        if($goods_no != ""){
            $goods_no = $goods_no;
        }

        $goods_no = preg_replace("/\s/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);
        $goods_no = preg_replace("/\t/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);
        $goods_no = preg_replace("/\n/",",",$goods_no);
        $goods_no = preg_replace("/,,/",",",$goods_no);

        if( $goods_no != "" ){
            $goods_nos = explode(",",$goods_no);
            if(count($goods_nos) > 1){
                if(count($goods_nos) > 500) array_splice($goods_nos,500);
                $in_goods_nos = join(",",$goods_nos);
                $where .= " and g.goods_no in ( $in_goods_nos ) ";
            } else {
                $where .= " and g.goods_no = '$goods_no' ";
            }
        }

        if( $brand_cd != "" ) $where .= " and g.brand = '$brand_cd' ";
        if( $brand_nm != "" ) $where .= " and g.brand = '$brand_nm' ";
        if( $goods_nm != "" ) $where .= " and g.goods_nm like '%$goods_nm%' ";
        if( $head_desc != "" ) $where .= " and g.head_desc like '%$head_desc%' ";
        if( $restock_ncnt != "" ) $having .= " having restock_ncnt >= $restock_ncnt ";

        $page_size = $limit;
        $startno = ($page-1) * $page_size;
        $limit = " limit $startno, $page_size ";

        $total = 0;
        $page_cnt = 0;

        ##############################################################
        # 리스트 페이징 처리
        ##############################################################
        if ($page == 1) {
            $sql = "
                select count(*) as cnt from (                
                    select
                        r.goods_no,r.goods_sub,r.goods_opt,sum(if(state = 'N',1,0)) as restock_ncnt
                    from goods_restock r inner join goods g on r.goods_no = g.goods_no and r.goods_sub = g.goods_sub
                    where 1=1 $where
                    group by goods_no,goods_sub,goods_opt 
                    $having            
                ) a
			";
            $row = DB::select($sql);
            $total = $row[0]->cnt;
            $page_cnt=(int)(($total-1)/$page_size) + 1;
        }

        if ($limit == -1) {
            $limit = "";
        } else $limit = " limit $startno,$page_size ";

        $orderby = "";
        if ($ord_field != ""){
            $orderby = " order by $ord_field $ord ";
        }

        $sql = " 
			select
				'' as chk, g.goods_no,
				ifnull( type.code_val, 'N/A') as goods_type_nm,
				opt.opt_kind_nm, brand.brand_nm, g.style_no, g.head_desc, '' as img_view,
                replace(g.img,'a_500', 's_62') as goods_img,
				g.goods_nm, stat.code_val as sale_stat_cl_val, 
				a.goods_opt,s.good_qty,s.wqty,a.restock_cnt,a.restock_ncnt,
				g.goods_sh,g.price,a.restock_ut,g.goods_type
			from (
                    select
                        r.goods_no,r.goods_sub,r.goods_opt,
                        count(*) as restock_cnt, sum(if(state = 'N',1,0)) as restock_ncnt,max(rt) as restock_ut
                    from goods_restock r inner join goods g on r.goods_no = g.goods_no and r.goods_sub = g.goods_sub
                    where 1=1 $where
                    group by goods_no,goods_sub,goods_opt        		
                    $having            
                    $orderby
                    $limit
			    ) a  inner join goods g on a.goods_no = g.goods_no and a.goods_sub = g.goods_sub
			    left outer join goods_summary s on a.goods_no = s.goods_no and a.goods_sub = s.goods_sub and a.goods_opt = s.goods_opt
				left outer join code type on type.code_kind_cd = 'G_GOODS_TYPE' and g.goods_type = type.code_id
				left outer join code stat on stat.code_kind_cd = 'G_GOODS_STAT' and g.sale_stat_cl = stat.code_id
				left outer join opt opt on opt.opt_kind_cd = g.opt_kind_cd and opt.opt_id = 'K'
				left outer join brand brand on brand.brand = g.brand
		";
        $collection = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => $total,
                "page" => $page,
                "page_cnt" => $page_cnt,
                "page_total" => count($collection)
            ),
            "body" => $collection
        ]);
    }

    public function showRestockingRequest(Request $request) {
        $state = $request->input('state');
        $goods_no = $request->input('goods_no');
        $sql = " 
            select goods_nm from goods where goods_no = ?
        ";
        $result = DB::select($sql, [$goods_no])[0];
        $goods_nm = $result->goods_nm;
        $values = [
            'goods_no' => $goods_no,
            'state' => $state,
            'goods_nm' => $goods_nm
        ];
        return view( Config::get('shop.head.view') . '/stock/stk06_restock', $values);
    }

    public function searchRestockingRequest(Request $request) {
        $state = $request->input('state');
        $goods_no = $request->input('goods_no');
        $user_id = $request->input('user_id');
        $where = "";
        if( $state != "" ){
            $where .= " and a.state = '$state' ";
        }
        if( $user_id != "" ){
            $where .=  " and a.user_id = '$user_id' ";
        }
        $sql = "
			select
				'', a.rt, a.user_id, b.name, b.mobile, b.email, a.state, a.no
			from goods_restock a
				inner join member b on a.user_id = b.user_id
			where a.goods_no = '$goods_no' $where
		";
        $collection = DB::select($sql);
        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($collection)
            ),
            "body" => $collection
        ]);
    }

    public function deleteRestockingRequest(Request $request) {
        $data = $request->input('goods_numbers');
        if (is_array($data) && count($data) > 0) {
            try {
                DB::beginTransaction();
                foreach ($data as $goods_no) {
                    if (!empty($goods_no)){
                        DB::table('goods_restock')
                            ->where('goods_no', $goods_no)
                            ->where('goods_sub', '0')
                            ->delete();
                    }
                }
                DB::commit();
                return response()->json(["msg" => "재입고요청삭제 성공", "deleted_no" => $data], 200);
            } catch(Exception $e) {
                DB::rollback();
                return response()->json(["msg" => "재입고요청삭제시 에러가 발생했습니다.", "failed_no" => $data], 500);
            }
        } else {
            return response()->json(["msg" => "null"], 200);
        }
    }

    public function sendSMS(Request $request) {
        $conf = new Conf();
        $cfg_sms_yn = $conf->getConfigValue("sms", "sms_yn");
        $cfg_shop_name = $conf->getConfig("shop","name");
        $cfg_kakao_yn = $conf->getConfigValue("kakao","kakao_yn");

        $data = urldecode($request->input('data'));
        $msg = urldecode($request->input('msg'));
        $goods_no = $request->input('goods_no');
        $goods_nm = $request->input('goods_nm');

        $user = [
            'id'	=> Auth('head')->user()->id,
            'name'	=> Auth('head')->user()->name
        ];
        try {
            DB::beginTransaction();
            if ($data != "" && $cfg_sms_yn == "Y") {        
                $sms = new SMS($user);
                $template_code = "Ordercode11";
                $datas = explode("\t", $data);
                for ($i=0;$i<count($datas);$i++) {
                    $datas[$i] = trim($datas[$i]);
                    if (!empty($datas[$i])) {
                        $info = explode("|", $datas[$i]);
                        $no = isset($info[0]) ? $info[0] : "";
                        $name = isset($info[1]) ? $info[1] : "";
                        $mobile = isset($info[2]) ? $info[2] : "";
                        
                        $msgarr = array(
                            "SHOP_NAME"			=> $cfg_shop_name,
                            "USER_NAME"			=> $name,
                            "GOODS_NAME"		=> $goods_nm,
                            "GOODS_LINK"		=> sprintf('http://www.netpx.co.kr/app/product/detail/%s/0',$goods_no),
                            "SHOP_URL"			=> 'http://www.netpx.co.kr'
                        );
                        $btnarr = array(
                            "BUTTON_TYPE" => '1',
                            "BUTTON_INFO" => '넷피엑스 바로가기^WL^http://www.netpx.co.kr'
                        );

                        // 테스트 코드
                        // $mobile = '010-내번호';
                        // $sms->Send($msg, $mobile, $name);

                        if ($cfg_kakao_yn == "Y" && $template_code != ""){
                            $sms->SendKakao($template_code, $mobile, $name, $msg, $msgarr, '', $btnarr);
                        } else {
                            $sms->Send($msg, $mobile, $name);
                        }

                        // 테스트로 Send메소드만 호출하면 sms 수신이 되지만 DB에 반영이 안되고 업데이트가 안되는 상태
                        // 추후 작업 필요
                        $sql = "
                            update goods_restock set
                                state = 'Y'
                            where no = :no
                        ";
                        DB::update($sql, ['no' => $no]);
                        return response()->json(['msg' => "SMS 알림 발송 성공"], 200);
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
