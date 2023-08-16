<?php

namespace App\Http\Controllers\head\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conf;
use App\Models\Point;
use Exception;

class mem01Controller extends Controller
{
    public function index($type='')
    {
        //taxpayer_yn
        $values = [
            'mail' => SLib::getCodes('S_MAIL'),
            'mobile' => SLib::getCodes('G_MOBILE'),
            'sex' => SLib::getCodes('G_SEX_TYPE'),
            'age' => SLib::getCodes('G_AGE'),
            'yn' => SLib::getCodes('G_YN'),
            'auth_type' => SLib::getCodes('G_AUTH_TYPE'),
            //'texpayer_yn' => SLib::getCodes('taxpayer_yn'),
            'type' => $type,
            'today' => date("md"),
            'layout' => $type ? 'head_skote.layouts.master-without-nav' : 'head_skote.layouts.app',
        ];

        $sql = " select group_no as id, group_nm as val from user_group order by group_no ";
        $values['groups'] = DB::select($sql);

        $sql = "select com_id as code_id,com_nm as code_val from company where com_type = '4' and site_yn = 'Y'";
        $values['sites'] = DB::select($sql);

        return view( Config::get('shop.head.view') . '/member/mem01', $values);
    }

    public function show(Request $req, $type = '', $user_id = '')
    {
        if ($type === 'add') {
            $values = $this->__getShowAddData();
        } else if ($type === 'edit') {
            $values = $this->__getShowEditData($user_id);
        }

        // 회원그룹 콤보
        $sql = " select group_no as id, group_nm as val from user_group order by group_no ";
        $values['groups'] = DB::select($sql);


        $values['type'] = $type;
        $values['admin_id'] = Auth('head')->user()->id;

        // dd($values);
        return view( Config::get('shop.head.view') . '/member/mem01_show', $values);
    }

    public function get($user_id)
    {
        $values = $this->__getShowEditData($user_id);
        return response()->json($values);
    }

    private function __getShowAddData()
    {
        $values = [
            'user' => (object) [],
            'out_yn' => '',
            'out_nm' => '',
            'user_groups' => (object) [],
            'use_point' => 0,
            'interest' => '없음'
        ];

        return $values;
    }

    private function __getShowEditData($user_id)
    {
        $values = [];
        $member_table = "";

        $out_yn = DB::table('member')
            ->where("user_id",$user_id)
            ->value('out_yn');
        if (isset($out_yn)) {
            $values["out_yn"] = $out_yn;

            if($values['out_yn'] == "I") {
                $member_table = "member_inactive";
            } else {
                $member_table = "member";
            }

            $sql = "
                select
                    b.group_nm
                     , a.group_no
                from user_group_member a
                    inner join user_group b on a.group_no = b.group_no
                where a.user_id = :user_id
            ";
            $values['user_groups'] = DB::select($sql, [ 'user_id' => $user_id ]);

            $sql = "
                select
                    a.user_id, a.name, a.user_pw, substring(a.jumin,1,8) as jumin, a.phone, a.mobile, a.email, a.email_chk, a.out_yn, a.point,
                    a.zip, a.addr, a.addr2, a.mobile_chk, a.regdate, a.lastdate, a.name_chk, a.wsale_status, '' as taxpayer_yn,
                    a.married_yn, date_format(a.married_date,'%Y%m%d') as married_date, date_format(a.anniv_date,'%Y%m%d') as anniv_date,
                    a.job, a.interest, a.yn, a.memo, a.visit_cnt, a.opt, a.recommend_id,
                    b.ord_date, b.ord_cnt, b.ord_amt,
                    c.code_val as auth_type_nm, a.auth_type, a.auth_yn, a.auth_key, a.ipin, a.foreigner, a.mobile_cert_yn,
                    a.yyyy_chk, a.yyyy, a.mm, a.dd, a.sex, a.store_nm,
                    ifnull(mbl.black_yn, 'N') as black_yn,
                    (case when mbl.black_reason is not null then mbl.black_reason else '' end) as black_reason
                from $member_table a
                    left outer join member_stat b on a.user_id = b.user_id
                    left outer join member_black_list mbl on a.user_id = mbl.user_id
                    left outer join code c on c.code_kind_cd = 'G_AUTH_TYPE' and c.code_id = a.auth_type
                where a.user_id = :user_id
            ";

            $user = DB::selectOne($sql, [ 'user_id' => $user_id ]);
            $user->point = number_format($user->point);
            $user->ord_amt = number_format($user->ord_amt);

            $phone = explode("-", $user->phone);
            $mobile = explode("-", $user->mobile);

            $user->phone1 = $phone[0] ? $phone[0] : '';
            if($user->phone !== '') {
                $user->phone2 = $phone[1] ? $phone[1] : '';
                $user->phone3 = $phone[2] ? $phone[2] : '';
            }

            $user->mobile1 = $mobile[0] ? $mobile[0] : '';
            if($user->mobile !== '') {
                $user->mobile2 = $mobile[1] ? $mobile[1] : '';
                $user->mobile3 = $mobile[2] ? $mobile[2] : '';
            }

            if($values['out_yn'] == "I") {
                $values['out_nm'] = "휴면회원";
            } else if($values['out_yn'] == "Y") {
                $values['out_nm'] = "탈퇴회원";
            } else if ($values['out_yn'] == "N") {
                $values['out_nm'] = "회원";
            }

            // 사용한 적립금
            $sql = " select sum(point) as total from point_list where user_id = '$user_id' and point_st = '사용' ";
            $total = DB::selectOne($sql)->total;
            $values['use_point'] = number_format($total);

            //관심분야
            $sql = " select count(*) as cnt from code where code_kind_cd = 'G_INTEREST' ";
            $cnt = DB::selectOne($sql)->cnt;

            $interest = [];
            for($j = $cnt; $j > 0; $j--){
                $code = pow(2,$j);
                if($user->interest > $code){
                    $user->interest = $user->interest - $code;

                    $sql = " select code_val from code where code_kind_cd = 'G_INTEREST' and code_id = '$code' ";
                    $code_val = DB::selectOne($sql)->code_val;
                    $interest[] = sprintf("%s", $code_val);
                }
            }

            $values['user'] = $user;
            $values['interest'] = count($interest) > 0 ? implode(',', $interest) : '없음';

        } else {
            $values = [];
        }
        return $values;
    }

    public function check_id($id)
    {
        $sql = "
			select count(*) as cnt from member
			where user_id = '$id'
        ";

        $row = DB::selectOne($sql);

        return $row->cnt;
    }

    public function download_show()
    {
        return view( Config::get('shop.head.view') . '/member/mem01_download_pop');
    }

    public function download(Request $req)
    {
        $this->set_excel_download("usr01_%s.xls");

        /** method : get_condition
         *
         * index 0 : where
         * 		 1 : order by
         * 		 2 : join
         */
        $cond = $this->get_condition($req);

        $sql = $this->get_user_sql($cond[0], $cond[1], $cond[2], 0, 200000, true);

        $fields = [];
        $_fields = explode(',',Request('fields', ''));

        foreach($_fields as $val) {
            list($name, $field) = explode('|', $val);

            $fields[] = [
                'name' => $name,
                'value' => $field
            ];
        }

        return view( Config::get('shop.head.view') . '/member/mem01_download', [
            'fields' => $fields,
            'rows' => DB::select($sql)
        ]);
    }

    public function search(Request $req)
    {
        $cond = $this->get_condition($req);

        $where = $cond[0];
        $order_by = $cond[1];
        $join = $cond[2];

        $page = $req->input("page", 1);
        $page_size	= $req->input("limit", 100);

        if ($page < 1 or $page == "") $page = 1;

        $total = 0;
        $page_cnt = 0;

        if ($page == 1) {
            $sql = "
				select
					count(*) as cnt
				from
					member a
					$join
					LEFT OUTER JOIN code d ON d.code_kind_cd = 'G_SEX_TYPE' AND a.sex = d.code_id
					LEFT OUTER JOIN member_stat e ON a.user_id = e.user_id
				where 1=1 and out_yn <> 'I' $where
			";
            $row = DB::selectOne($sql);
            $total = $row->cnt;

            // 페이지 얻기
            $page_cnt=(int)(($total - 1)/$page_size) + 1;
            $startno = ($page - 1) * $page_size;
            //$arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page, "page_total"=>count);
        } else {
            $startno = ($page - 1) * $page_size;
            //$arr_header = null;
        }

        $sql = $this->get_user_sql($where, $order_by, $join, $startno, $page_size);

        $arr_header = array("total"=>$total, "page_cnt"=>$page_cnt, "page"=>$page);


        $result = DB::select($sql);

        return response()->json([
            "code" => 200,
            "head" => $arr_header,
            "body" => $result
        ]);
    }

    public function add_user(Request $req)
    {

        // 설정 값 얻기
        $conf = new Conf();

        $encrypt_mode = $conf->getConfigValue("shop","encrypt_mode");

        // 암호화 키
        $encrypt_key = "";
        if( $encrypt_mode == "mhash" ){
            $encrypt_key = $conf->getConfigValue("shop","encrypt_key");
        }

        $resno_enc_yn = $conf->getConfigValue("shop","resno_enc_yn");

        $id = Auth('head')->user()->id;
        $name = Auth('head')->user()->name;

        $user_id			= Request("user_id");
        $pw     			= Request("pw");
        $name				= Request("name");

        $jumin1				= Request("jumin1");
        $jumin2				= Request("jumin2");
        $chk_jumin			= Request("chk_jumin");
        $phone1				= Request("phone1");
        $phone2				= Request("phone2");
        $phone3				= Request("phone3");
        $phone				= $phone1."-".$phone2."-".$phone3;
        $mobile1			= Request("mobile1");
        $mobile2			= Request("mobile2");
        $mobile3			= Request("mobile3");
        $mobile				= $mobile1."-".$mobile2."-".$mobile3;
        $rmobile			= strrev($mobile);
        $email				= Request("email");
        $zip				= Request("zipcode");
        $addr				= Request("addr1");
        $addr2				= Request("addr2");
        $email_chk			= Request("send_mail_yn");
        $mobile_chk			= Request("send_mobile_yn");
        $married_yn			= Request("married_yn");
        $married_date		= Request("married_date");
        $anniv_date			= Request("anniv_date");
        $job				= Request("job");
        $interest			= Request("interest", "");
        $yn					= Request("yn");
        $opt				= Request("opt");
        $memo				= Request("memo");
        $taxpayer_yn		= Request("taxpayer_yn");
        $type               = Request("type");
        $store_nm           = Request("store_nm");
        $store_cd           = Request("store_no");

        $auth_type			= Request("auth_type");
        $auth_yn			= Request("auth_yn");
        $auth_key			= Request("auth_key");
        $yyyy				= Request("yyyy");
        $mm					= sprintf("%02d", Request("mm", 01));
        $dd					= sprintf("%02d", Request("dd", 01));
        $yyyy_chk			= Request("yyyy_chk");
        $sex				= Request("sex");

        // 비밀번호 암호화
        $enc_pwd = Lib::get_enc_hash($pw, $encrypt_mode, $encrypt_key);
        // $enc_pwd = $pw;

        // 주민등록번호 관련
        $jumin = "";
        $enc_jumin2 = "";

        // 주민등록번호 암호화
        // $enc_jumin2 = $jumin2;
        // $first_jumin2 = substr($jumin2, 0, 1);
        // $second_jumin2 = substr($jumin2, 1, 6);
        // if( $chk_jumin == "Y" ){
        //     if( $resno_enc_yn == "Y" ){
        //         $enc_jumin2 = $first_jumin2."[".Lib::get_enc_hash($second_jumin2, $encrypt_mode, $encrypt_key)."]";
        //     }
        //     $jumin = $jumin1."-".$first_jumin2."******";
        // } else {
        //     $jumin = "";
        // }
        // // 주민등록 번호 중복사용 체크
        // $sql = "
        //     select count(*) as cnt
        //     from member
        //     where jumin1 = '$jumin1'
        //         and jumin2 = '$enc_jumin2'
        // ";
        // $row = DB::selectOne($sql);
        // $resno_cnt = $row->cnt;
        // if( $resno_cnt > 0 ){
        //     return response()->json("사용이 불가능한 주민등록번호 입니다.\\n정확하게 입력하시기 바랍니다.", 500);
        // }

        // // 성별 및 생년월일
        // $sex = "M";
        // $yyyy = "";
        // $mm = "";
        // $dd = "";
        // if( $chk_jumin == "Y" ){
        //     $sex =  ( $first_jumin2 % 2 == 0 ) ? "F" : "M";
        //     $yyyy_prefix =  ( $first_jumin2 > 2 ) ? "20":"19";
        //     $yyyy = $yyyy_prefix . substr($jumin1,0,2);
        //     $mm = substr($jumin1,2,2);
        //     $dd = substr($jumin1,4,2);
        // }

        try {
            DB::beginTransaction();

            $sql= "
                insert into member (
                    user_id, user_pw, name, name_eng, jumin, jumin1, jumin2, email, email_chk
                    , zip, addr, addr2, phone, mobile, rmobile, regdate
                    , point, ypoint, yn, mobile_chk, yyyy_chk
                    , yyyy, mm, dd, opt, out_yn, name_chk, wsale_status, taxpayer_yn, enjumin, anniv_date, anniv_type
                    , job, interest, memo, pwd_reset_yn, sex, recommend_id
                    , auth_type, auth_yn, auth_key, store_nm, store_cd, type
                ) values (
                    '$user_id', '$enc_pwd', '$name', '', '$jumin', '$jumin1', '$enc_jumin2', '$email', '$email_chk'
                    , '$zip', '$addr', '$addr2', '$phone', '$mobile', '$rmobile', now()
                    , '0', '0', 'Y', '$mobile_chk', ''
                    , '$yyyy', '$mm', '$dd', '$opt', 'N', 'N', 'N', '$taxpayer_yn', '', '$anniv_date', ''
                    , '$job', '$interest', '$memo', 'N', '$sex', ''
                    , '$auth_type', '$auth_yn', '$auth_key', '$store_nm', '$store_cd', '$type'
                )
            ";

            DB::insert($sql);
            DB::commit();

        } catch(Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }


        return response()->json($user_id, 201);
    }

    public function edit_user($user_id='', Request $req)
    {
        $name				= Request("name");
        $pw     			= Request("pw");
        $jumin1				= Request("jumin1");
        $jumin2				= Request("jumin2");
        $chk_jumin			= Request("chk_jumin");
        $phone1				= Request("phone1");
        $phone2				= Request("phone2");
        $phone3				= Request("phone3");
        $phone				= $phone1."-".$phone2."-".$phone3;
        $mobile1			= Request("mobile1");
        $mobile2			= Request("mobile2");
        $mobile3			= Request("mobile3");
        $mobile				= $mobile1."-".$mobile2."-".$mobile3;
        $rmobile			= strrev($mobile);
        $email				= Request("email");
        $zip				= Request("zipcode");
        $addr				= Request("addr1");
        $addr2				= Request("addr2");
        $email_chk			= Request("send_mail_yn");
        $mobile_chk			= Request("send_mobile_yn");
        $married_yn			= Request("married_yn");
        $married_date		= Request("married_date");
        $anniv_date			= Request("anniv_date");
        $job				= Request("job");
        $interest			= Request("interest", "");
        //$yn					= Request("yn");
        $opt				= Request("opt");
        $memo				= Request("memo");
        $taxpayer_yn		= Request("taxpayer_yn","");            // 피엘라벤 사용안함
        $wsale_status		= Request("wsale_status", "");
        $type               = Request("type");
        $store_nm           = "";
        $store_cd           = Request("store_no", "");
        $store_chg          = Request("store_chg", "false"); // 가입매장변경여부
        $black_yn           = Request("black_yn");
        $black_reason       = Request("black_reason");
        $id = Auth('head')->user()->id;
        $store_sql = "";

        try {
            DB::beginTransaction();

            if ($store_chg == 'true') {
                $store_nm = DB::table('store')->where('store_cd', $store_cd)->value('store_nm');
                $store_sql = "
                    , store_nm = '$store_nm'
                    , store_cd = '$store_cd'
                ";
            }

            $sql = "
                update member set
                    name ='$name'
                    , phone = '$phone'
                    , mobile = '$mobile'
                    , email = '$email'
                    , zip = '$zip'
                    , addr = '$addr'
                    , addr2 = '$addr2'
                    , email_chk = '$email_chk'
                    , mobile_chk = '$mobile_chk'
                    , wsale_status = '$wsale_status'
                    -- , taxpayer_yn = '$taxpayer_yn'
                    , married_yn	= '$married_yn'
                    , married_date = '$married_date'
                    , anniv_date	= '$anniv_date'
                    , job = '$job'
                    , interest = '$interest'
                    , opt = '$opt'
                    , memo = '$memo'
                    , type = '$type'
                    $store_sql
                where user_id = '$user_id'
            ";

            DB::update($sql);

            $sql = "
                select
                    user_id,
                    black_yn
                from
                    member_black_list
                where
                    user_id = '$user_id'
            ";

            $row = DB::select($sql);

            if(count($row) > 0) {
                $sql = "
                    update member_black_list
                    set ut_date = now(), black_yn ='$black_yn', black_reason ='$black_reason', ut_user_id = '$id'
                    where
                        user_id = '$user_id'
                ";
                DB::update($sql);
            } else if(count($row) === 0 && $black_yn === 'Y'){
                $sql= "
                    insert into member_black_list (
                        user_id ,
                        black_yn ,
                        black_reason,
                        rt_user_id ,
                        rt_date
                    ) values (
                        '$user_id',
                        '$black_yn',
                        '$black_reason',
                        '$id',
                        now()
                    )
                ";

                DB::insert($sql);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(null, 204);
    }

    public function delete_user($user_id='', Request $req) {
        $code = 200;
        $msg = '';

        $user = [
            'id' => Auth('head')->user()->id,
            'name' => Auth('head')->user()->name,
        ];

        try {

            DB::beginTransaction();

            $sql = "
                update member set
                    user_pw='',name='',jumin='',email='',email_chk='',zip='',addr='',addr2=''
                    ,phone='',mobile='',yn='',mobile_chk='', interest = '', anniv_date = ''
                    ,yyyy_chk='',yyyy='',mm='',dd='',opt='',out_yn='Y',out_date=now()
                    ,name_chk = '', name_eng = '', job = '', married_yn = '', married_date = ''
                    ,rmobile = '', opt = '', wsale_status = '', taxpayer_yn = '', memo = ''
                    ,visit_cnt = '', jumin1 = '', jumin2 = ''
                    ,sex='', auth_type='', auth_yn='', auth_key='', ipin='', foreigner='', mobile_cert_yn='' , type='',store_nm='',store_cd=''
                where user_id = '$user_id'
            ";
            DB::update($sql);

            $point = new Point($user, $user_id);
            $point->DeleteUser();

            DB::commit();
            $msg = '해당 회원이 탈퇴처리 되었습니다.';
        }catch(Exception $e) {
            DB::rollback();
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json(['code' => $code, 'message' => $msg], $code);
    }

    public function add_group($user_id='', Request $req) {
        $member_group = Request("member_group", "");

        try{
            DB::beginTransaction();

            $sql = "
				select
					count(*) as cnt
				from user_group_member
				where user_id = '$user_id' and group_no = '$member_group'
            ";
            $row = DB::selectOne($sql);

            if ($row->cnt == 0) {
                $sql = "
					insert into user_group_member (
						user_id, group_no
					) values (
						'$user_id','$member_group'
					)
                ";
                DB::insert($sql);
            } else {
                throw new Exception("이미 등록된 그룹입니다.");
            }

            DB::commit();
            return response()->json($member_group, 201);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function del_group($user_id='', Request $req) {
        $member_group = Request("member_group", "");

        try{
            DB::beginTransaction();

            $sql = "
				delete from user_group_member
				where user_id = '$user_id'
					and group_no = '$member_group'
            ";

            DB::delete($sql);

            DB::commit();
            return response()->json(null, 204);
        }catch(Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function change_pw($user_id='', Request $req) {
        // 설정 값 얻기
        $conf = new Conf();

        $encrypt_mode = $conf->getConfigValue("shop","encrypt_mode");

        // 암호화 키
        $encrypt_key = "";
        if( $encrypt_mode == "mhash" ){
            $encrypt_key = $conf->getConfigValue("shop","encrypt_key");
        }

        $resno_enc_yn = $conf->getConfigValue("shop","resno_enc_yn");

        $pw = Request("pw");

        // 암호화 모드
        $enc_pwd = Lib::get_enc_hash($pw, $encrypt_mode, $encrypt_key);

        $sql = "
            update member set
                user_pw = '$enc_pwd'
                , pwd_reset_yn = 'N'
            where user_id = '$user_id'
        ";
        DB::update($sql);

        return response()->json(null, 204);
    }

    public function show_search($type='', $user_id='') {
        $sql = $this->__get_show_sql($type, $user_id);

        $rows = DB::select($sql);

        $header = ['total' => count($rows)];

        if ($type == 'buylist') {
            $data = $this->__get_buylist_total($user_id);

            $header['qty'] = $data->qty;
            $header['ord_amt'] = $data->ord_amt;
            $header['clm_qty'] = $data->clm_qty;
            $header['clm_amt'] = $data->clm_amt;
        }

        if ($type == 'coupon_list') {
            $data = $this->__get_coupon_use_count($user_id);

            $header['use_cnt'] = $data->use_cnt;
        }

        return response()->json([
            "code" => 200,
            "head" => $header,
            "body" => $rows
        ]);
    }

    private function __get_show_sql($type, $user_id){
        $sql = "";

        switch($type) {
            //상담 목록 검색 쿼리
            case "claim_msg" :
                $sql ="
                    select
                        date_format(regi_date,'%Y.%m.%d %H:%i:%s') regi_date, ord_no, contents, admin_nm
                    from member_cousel
                    where user_id = '$user_id'
                    order by idx desc
                ";
                break;
            //주문 목록 검색 쿼리
            case "buylist" :
                $sql = "
                    select
                        date_format(a.ord_date,'%Y.%m.%d') ord_date, a.ord_no, a.ord_seq, a.goods_nm, ifnull(e.opt_val, a.goods_opt) as opt, a.qty, a.price,
                        cp.code_val as pay_type, a.bank_code, co.code_val as ord_state, cc.code_val as clm_state, a.sale_place, a.coupon_nm, a.ord_opt_no, a.goods_no
                    from
                    (
                        select
                            a.ord_opt_no,b.ord_date, b.ord_no, a.ord_seq, c.goods_nm, a.qty, a.goods_opt, b.user_nm, b.r_nm, a.price,
                            d.pay_type, c.opt_kind_cd,
                            case d.pay_type
                                when '2' then d.card_name
                                when '1' then d.bank_code
                                when '6' then d.card_name
                                else d.bank_code
                            end bank_code,
                            a.ord_state, a.clm_state,
                            -- e.com_nm as sale_place,
                            ifnull(e.com_nm, a.sale_place) as sale_place,
                            i.com_nm, c.price-c.wonga as prf,
                            case a.ord_state
                                when '-20' then null
                                else d.upd_dm
                            end upd_dm,
                            a.dlv_end_date, b.upd_date, c.goods_no, c.goods_sub, a.ord_kind, c.baesong_kind, f.coupon_nm
                        from order_opt a
                            inner join order_mst b on a.ord_no = b.ord_no
                            inner join goods c on a.goods_no = c.goods_no and a.goods_sub = c.goods_sub
                            inner join payment d on b.ord_no = d.ord_no
                            left outer join company e on a.sale_place = e.com_id and e.com_type= '4'
                            left outer join company i on c.com_id = i.com_id
                            left outer join coupon f on a.coupon_no = f.coupon_no
                        where b.user_id = '$user_id'
                    ) a left outer join opt e on e.opt_kind_cd = a.opt_kind_cd and e. a.goods_opt = e.opt_id
                    left outer join code cc on cc.code_kind_cd = 'G_CLM_STATE' and cc.code_id = a.clm_state
                    left outer join code co on co.code_kind_cd = 'G_ORD_STATE' and co.code_id = a.ord_state
                    left outer join code cp on cp.code_kind_cd = 'G_PAY_TYPE' and cp.code_id = a.pay_type
                    order by a.ord_opt_no desc
                ";
                break;
            //적립금 목록 검색 쿼리
            case "point" :
                $sql = "
                    select
                        ord_no, point_st,
                        if(point_status = 'N',concat(point_nm,'\(대기\)'),point_nm) as point_nm, point, regi_date, '' as expire_day,admin_nm, admin_id, '' as ord_opt_no
                    from point_list
                    where user_id = '$user_id'
                    order by no desc
                ";
                break;
            //고객문의 목록 검색 쿼리
            case "claim" :
                $ans_y = "답변완료";
                $ans_s = "답변대기";
                $ans_c = "등록불가";

                $sql = "
                    select
                        date_format(q.regi_date,'%Y.%m.%d %H:%i:%s') regi_date, cd.code_val as typenm, q.subject,
                        (case ans_yn when 'Y' then '$ans_y' when 'C' then '$ans_c' else '$ans_s' end) as ans_state
                        , q.ans_nm, date_format(q.regi_date,'%Y%m%d'), cd.code_id as type
                    from qna q
                    left outer join code cd on cd.code_kind_cd = 'G_TYPE_CD' and cd.code_id = substring(q.qna_type,1,3)
                    where user_id = '$user_id'
                ";
                break;
            //Q&A 목록 검색 쿼리
            case "qa" :
                $sql = "
                    select
                        date_format(q.q_date,'%Y.%m.%d %H:%i:%s') q_date, g.goods_nm,
                        q.subject, q.answer_yn, q.admin_nm,
                        q.goods_no, q.goods_sub,
                        date_format(q.q_date,'%Y%m%d')
                    from goods_qa_new q
                        inner join goods g on g.goods_no = q.goods_no and g.goods_sub = q.goods_sub
                    where q.user_id = '$user_id'
                    order by
                        q.no desc
                    limit 300
                ";
                break;
            //삼품평 목록 검색 쿼리
            case "estimate" :
                // 설정 값 얻기
                $cfg_img_size_list		= SLib::getCodesValue("G_IMG_SIZE","list");
                $cfg_img_size_real		= SLib::getCodesValue("G_IMG_SIZE","real");

                $sql = "
                    select
                        '' as img
                        , b.goods_nm
                        , (case a.goods_est
                            when '1' then '★☆☆☆☆'
                            when '2' then '★★☆☆☆'
                            when '3' then '★★★☆☆'
                            when '4' then '★★★★☆'
                            when '5' then '★★★★★'
                        end) as estimate
                        , ifnull(a.best_yn, 'N') as best_yn
                        , ifnull(a.buy_yn, 'N') as buy_yn
                        , a.goods_title, ifnull(a.point, 0) as point, a.use_yn, a.cnt, a.regi_date
                        , a.no, a.goods_no, a.goods_sub
                        , if(b.special_yn <> 'Y', replace(b.img, '$cfg_img_size_real', '$cfg_img_size_list'), (
                            select replace(c.img, '$cfg_img_size_real', '$cfg_img_size_list') as img
                            from goods c where c.goods_no = b.goods_no and c.goods_sub = 0
                          )) as img_s_50
                    from goods_estimate a
                        inner join goods b on a.goods_no = b.goods_no and a.goods_sub = b.goods_sub
                    where a.user_id = '$user_id'
                    order by a.regi_date desc
                ";
                break;
            //클레임 목록 검색 쿼리
            case "claim_list" :
                $today = date("Ymd");

                $sql ="
                    select
                        date_format(c.regi_date, '%y.%m.%d %H:%i:%s') as regi_date, a.ord_no, '' as ord_opt_no,
                        cd.code_val as cs_form,
                        if(cd3.code_id is not null,cd3.code_val,cd2.code_val) as clm_state,c.memo,c.admin_nm
                    from order_mst a
                        inner join order_opt b on a.ord_no = b.ord_no
                        inner join claim_memo c on c.ord_opt_no = b.ord_opt_no
                        left outer join code cd on cd.code_kind_cd = 'CS_FORM2' and cd.code_id = c.cs_form
                        left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_STATE' and cd2.code_id = c.ord_state
                        left outer join code cd3 on cd3.code_kind_cd = 'G_CLM_STATE' and cd3.code_id = c.clm_state
                    where a.user_id = '$user_id' and c.regi_date >= date_sub($today, INTERVAL 6 MONTH)
                    order by c.regi_date desc
                ";
                break;
            //쿠폰 목록 검색 쿼리
            case "coupon_list" :
                $today = date("Ymd");

                $sql ="
                    select
                        b.coupon_no, b.coupon_nm, b.coupon_type, c.ord_no, a.ord_opt_no, a.down_date, a.use_date, a.serial
                        , ifnull(a.use_yn, 'N') as coupon_member_use_yn
                        , ifnull(b.use_yn, 'N') as coupon_use_yn
                        , if(b.use_fr_date = '99999999', '$today', b.use_fr_date) as use_fr_date
                        -- , if(b.use_date_type = 'P' ,a.use_to_date,b.use_to_date) as use_to_date
						, b.use_to_date
                        , g.goods_nm, c.goods_no, c.goods_sub
                    from coupon_member a
                        inner join coupon b on a.coupon_no = b.coupon_no
                        left outer join order_opt c on a.ord_opt_no = c.ord_opt_no
                        left outer join goods g on c.goods_no = g.goods_no
                        left outer join code d on d.code_kind_cd = 'G_COUPON_USE_YN' and a.use_yn = d.code_id
                    where a.user_id = '$user_id'
						-- and if( b.use_date_type = 'P' ,a.use_to_date,b.use_to_date) >= date_format(now(), '%Y%m%d')
						-- and b.use_to_date >= date_format(now(), '%Y%m%d')
                    order by a.idx desc
                ";
                break;
        }

        return $sql;
    }

    private function __get_coupon_use_count($user_id) {
        $sql = "
			select
				count(a.idx) use_cnt
			from coupon_member a
				inner join coupon c on a.coupon_no = c.coupon_no
			where a.user_id = '$user_id' and ifnull(a.use_yn, 'N') <> 'Y' and ifnull(c.use_yn, 'N') = 'Y'
				-- and if( c.use_date_type = 'P' ,a.use_to_date,c.use_to_date) >= date_format(now(), '%Y%m%d')
				-- and c.use_to_date >= date_format(now(), '%Y%m%d')
			";

        return DB::selectOne($sql);
    }

    private function __get_buylist_total($user_id) {
        $sql = "
            select
                sum(a.qty) as qty ,
                ifnull(sum(a.qty * a.price),0) as ord_amt,
                ifnull(sum(if(a.clm_state = 60 or a.clm_state = 61,a.qty,0)),0) as clm_qty,
                ifnull(sum(if(a.clm_state = 60 or a.clm_state = 61,a.qty * a.price,0)),0) as clm_amt
            from order_opt a
                inner join order_mst b on a.ord_no = b.ord_no
            where
                b.user_id = '$user_id'
                and a.ord_state >= 10
        ";

        return DB::selectOne($sql);
    }

    private function get_user_sql($where, $order_by, $join, $startno = 0, $page_size = 0, $use_group = false) {

        $group_sql = "";

        if ($use_group) {
            $group_sql = "
                ,(
                    select group_concat(ug.group_nm order by ug.point_ratio desc separator ',')
                    from user_group_member ugm
                    inner join user_group ug on ug.group_no = ugm.group_no
                    where ugm.user_id = a.user_id
                ) as `group`
            ";
        }

        $sql = "
			select
				'' as chkbox, a.user_id, a.name,
				d.code_val as sex, concat(ifnull(a.yyyy, ''),ifnull(a.mm, ''),ifnull(a.dd, '')) as birth_day,
				ifnull(a.jumin1, '') as jumin,
				a.phone, a.mobile, a.email, a.point,
				date_format(a.regdate,'%y%m%d') as regdate,
				a.lastdate as lastdate, a.visit_cnt,
				a.auth_type, f.code_val as auth_type_str, a.auth_yn,
				e.ord_date, e.ord_cnt, e.ord_amt,
				a.email_chk, a.mobile_chk, a.yn, ifnull(cm.com_nm,a.site) as site,
                a.name_chk, a.addr, a.zip
                ${group_sql}
			from
				member a
				$join
				left outer join code d on d.code_kind_cd = 'G_SEX_TYPE' and a.sex = d.code_id
				left outer join member_stat e on a.user_id = e.user_id
				left outer join code f on f.code_kind_cd = 'G_AUTH_TYPE' and a.auth_type = f.code_id
				left outer join company cm on cm.com_type = 4 and a.site = cm.com_id
			where 1=1 and out_yn <> 'I'
				$where
				$order_by
			limit $startno, $page_size
		";

        return $sql;
    }

    private function get_condition(Request $req) {

        $user_id	= Request("user_ids");
        $name		= Request("name");
        $yn			= Request("yn");
        $jumin		= Request("jumin");
        $phone		= Request("phone");
        $mobile		= Request("mobile");
        $sex		= Request("sex");
        $age		= Request("age");
        $user_group	= Request("user_group");

        $sdate		= Request("sdate");
        $edate		= Request("edate");
        $last_sdate	= Request("last_sdate");
        $last_edate	= Request("last_edate");
        $ord_sdate	= Request("order_sdate");
        $ord_edate	= Request("order_edate");
        $mmdd		= Request("mmdd");
        $mail		= Request("mail");
        $mobile_chk	= Request("mobile_chk");

        $fr_ord_amt	= Lib::uncm(Request("cond_amt_from"));
        $to_ord_amt	= Lib::uncm(Request("cond_amt_to"));
        $fr_ord_cnt	= Lib::uncm(Request("cond_cnt_from"));
        $to_ord_cnt	= Lib::uncm(Request("cond_cnt_to"));

        $birth_sdate = Request("birth_sdate");
        $birth_edate = Request("birth_edate");

        // 인증
        $auth_type	= Request("auth_type");
        $auth_yn	= Request("auth_yn");
        $site 	    = Request("site");

        $mobile_yn    = Request("mobile_yn"); // 모바일 주문 여부
        $app_yn       = Request("app_yn"); // 앱 주문 여부

        $where = "";

        // ag grid copy & paste 추가
        $user_id = preg_replace("/\s/",",",$user_id);
		$user_id = preg_replace("/,,/",",",$user_id);
		$user_id = preg_replace("/\t/",",",$user_id);
		$user_id = preg_replace("/\n/",",",$user_id);

        if($user_id !=""){
            $ids = explode(",",$user_id);
            if(count($ids) > 1){
                if(count($ids) > 300) array_splice($ids,300);
                for($i=0;$i<count($ids);$i++){
                    $ids[$i] = sprintf("'%s'",$ids[$i]);
                }
                $in_ids = join(",",$ids);
                $where .= " and a.user_id in ( $in_ids ) ";
            } else {
                $where .= " and a.user_id = '$user_id' ";
            }
        }

        if($name != "")				$where .= " and a.name = '$name' ";

        if( $yn != "" )
        {
            $yn = ( $yn == "Y" )?"Y":"";
            $where .= " and a.yn = '$yn' ";
        }

        if($jumin != "")			$where .= " and a.jumin1 = '$jumin' ";
        if($phone != "")			$where .= " and a.phone = '$phone' ";
        if($mobile != "")			$where .= " and a.mobile = '$mobile' ";
        if($sex != "")				$where .= " and a.sex = '$sex'";
        if($sdate != "")			$where .= " and a.regdate >= '$sdate' ";
        if($edate != "")			$where .= " and a.regdate < DATE_ADD('$edate', INTERVAL 1 DAY) ";
        if($last_sdate != "")		$where .= " and a.lastdate >= '$last_sdate' ";
        if($last_edate != "")		$where .= " and a.lastdate < DATE_ADD('$last_edate', INTERVAL 1 DAY) ";
        if($ord_sdate != "")		$where .= " and e.ord_date >= '$ord_sdate 00:00:00' ";
        if($ord_edate != "")		$where .= " and e.ord_date < DATE_ADD('$ord_edate 23:59:59', INTERVAL 1 DAY) ";

        if($mail != "")				$where .= " and a.email_chk = '$mail' ";
        if($mobile_chk != "")		$where .= " and a.mobile_chk = '$mobile_chk' ";

        if($fr_ord_amt != "")		$where .= " and e.ord_amt >= '$fr_ord_amt' ";
        if($to_ord_amt != "")		$where .= " and e.ord_amt < '$to_ord_amt' ";
        if($fr_ord_cnt != "")		$where .= " and e.ord_cnt >= '$fr_ord_cnt' ";
        if($to_ord_cnt != "")		$where .= " and e.ord_cnt < '$to_ord_cnt' ";
        if($birth_sdate != "")		$where .= " and a.yyyy >= '$birth_sdate' ";
        if($birth_edate != "")		$where .= " and a.yyyy <= $birth_edate";

        if($age != "") {
            if($age == '10')		$where .= " and  a.yyyy > YEAR(CURDATE())-7 and a.yyyy <= YEAR(CURDATE()) ";	// 초등학생미만
            if($age == '11')		$where .= " and  a.yyyy >= YEAR(CURDATE())-12 and a.yyyy <= YEAR(CURDATE())-7 ";	// 초등학생
            if($age == '12')		$where .= " and  a.yyyy >= YEAR(CURDATE())-15 and a.yyyy <= YEAR(CURDATE())-13 ";	// 중학생
            if($age == '13')		$where .= " and  a.yyyy >= YEAR(CURDATE())-18 and a.yyyy <= YEAR(CURDATE())-16 ";	// 고등학생
            if($age == '20')		$where .= " and  a.yyyy >= YEAR(CURDATE())-28 and a.yyyy <= YEAR(CURDATE())-19 ";	// 20대
            if($age == '30')		$where .= " and  a.yyyy >= YEAR(CURDATE())-38 and a.yyyy <= YEAR(CURDATE())-29 ";	// 30대
            if($age == '40')		$where .= " and  a.yyyy >= YEAR(CURDATE())-48 and a.yyyy <= YEAR(CURDATE())-39 ";	// 40대
            if($age == '50')		$where .= " and  a.yyyy >= YEAR(CURDATE())-58 and a.yyyy <= YEAR(CURDATE())-49 ";	// 50대
            if($age == '60')		$where .= " and  a.yyyy <= YEAR(CURDATE())-59 ";									// 60대이상
        }

        if($mmdd != ""){
            $mm = substr($mmdd,0,2);
            if($mm != ""){
                $where .= " and a.mm = '$mm' ";
            }
            $dd = substr($mmdd,2,2);
            if($dd != ""){
                $where .= " and a.dd = '$dd' ";
            }
        }

        if($auth_type != ""){
            $where .= " and a.auth_type = '$auth_type' ";
        }

        if($auth_yn != ""){
            $where .= " and a.auth_yn = '$auth_yn' ";
        }

        if($site != ""){
            $where .= " and a.site = '$site' ";
        }

        $join = "";
        if($user_group != ""){
            $join = " inner join user_group_member b on a.user_id = b.user_id ";
            $join .= " inner join user_group c on b.group_no = c.group_no and c.group_no in ($user_group) ";
        }

        $page = Request("page",1);
        if ($page < 1 or $page == "") $page = 1;

        $page_size	= Request("limit", 100);
        $ord_field	= Request("ord_field","a.user_id");
        $ord		= Request("ord","asc");

        $str_order_by = sprintf(" order by %s %s ",$ord_field,$ord);
        return [$where, $str_order_by, $join];
    }

    private function set_excel_download($filename_format) {
        $filename = sprintf($filename_format, date("YmdH"));

        header("Content-type: application/vnd.ms-excel;charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");
        Header("Content-Transfer-Encoding: binary");
        Header("Pragma: no-cache");
        Header("Expires: 0");
    }
	
	/** 회원 상담내용 등록 */
	public function add_counsel($user_id = '', Request $request)
	{
		$code = 200;
		$msg = "";
		$ord_no = $request->input('ord_no', '');
		$content = $request->input('content', '');
		$admin_id = Auth('head')->user()->id;
		$admin_nm = Auth('head')->user()->name;

		try{
			DB::beginTransaction();

			$user_nm = DB::table('member')->where('user_id', $user_id)->value('name');

			DB::table('member_cousel')->insert([
				'user_id' => $user_id,
				'name' => $user_nm,
				'contents' => $content,
				'ord_no' => $ord_no ?? '',
				'admin_id' => $admin_id,
				'admin_nm' => $admin_nm,
				'regi_date' => now(),
			]);

			DB::commit();
			$msg = "상담내용이 정상적으로 등록되었습니다.";
		}catch(Exception $e) {
			DB::rollback();
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([ 'code' => $code, 'msg' => $msg ], $code);
	}
}
