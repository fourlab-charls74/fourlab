<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm13Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(2, 'year')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view( Config::get('shop.head.view') . '/promotion/prm13',$values);
    }

    public function search(Request $request)
    {
        $sdate      = $request->input('sdate',Carbon::now()->sub(2, 'year')->format('Ymd'));
        $edate      = $request->input('edate',date("Ymd"));
        $title      = $request->input('s_title');
        $evt_idx    = $request->input('s_evt_idx');
		$order_no	= $request->input('s_order_no');
		$user_code	= $request->input('s_user_code');
        $evt_state	= $request->input('s_evt_state');
		$user_nm	= $request->input('s_user_nm');
		$mobile		= $request->input('s_mobile');
		$sex		= $request->input('s_sex');
		$country	= $request->input('s_country');


        $where = "";
        if( $title != "" )		$where .= " and b.title like '%" . Lib::quote($title) . "%' ";
		if( $evt_idx != "" )	$where .= " and a.evt_idx = '$evt_idx' ";
		if( $order_no != "" )	$where .= " and a.order_no = '$order_no' ";
		if( $user_code != "" )	$where .= " and a.user_code = '$user_code' ";
		if( $evt_state != "" )	$where .= " and a.evt_state = '$evt_state' ";
		if( $user_nm != "" )	$where .= " and a.user_nm like '%" . Lib::quote($user_nm) . "%' ";
		if( $mobile != "" )		$where .= " and a.mobile = '$mobile' ";
		if( $sex != "" )		$where .= " and a.sex = '$sex' ";
		if( $country != "" )	$where .= " and a.country = '$country' ";

		$query	= "
			select
				'' as chk, a.evt_idx, b.title, a.order_no,
				case
					when a.evt_state = '1' then '입금예정'
					when a.evt_state = '5' then '접수후보'
					when a.evt_state = '9' then '후보결제대기'
					when a.evt_state = '10' then '접수완료'
					when a.evt_state = '20' then '확정대기'
					when a.evt_state = '30' then '확정완료'
					when a.evt_state = '-10' then '결제오류'
					when a.evt_state = '-20' then '신청취소'
					else '-'
				end as evt_state_nm,
				c.kind, a.user_id, a.user_code,
				a.user_nm, concat(a.en_nm1, ' ', a.en_nm2) as en_nm,
				case
					when a.kind = '0' then '성인'
					when a.kind = '1' then '소인'
					when a.kind = '2' then '소인'
					else '-'
				end as ckind,
				a.mobile, a.email,
				case
					when a.sex = 'M' then '남성'
					when a.sex = 'F' then '여성'
					else '-'
				end as sex,
				a.country,
				a.birthdate, a.em_phone, concat(a.group_nm, '그룹') as group_nm,
				a.team_nm, concat(a.addr1, ' ', a.addr2) as addr, a.regdate,
				a.evt_state, a.idx as evt_mem_idx, a.seq,
				a.dietary_yn
			from evt_member a
			inner join evt_mst b on a.evt_idx = b.idx
			inner join evt_order c on a.order_no = c.order_no
			where
				1 = 1 
				$where
				and ( a.regdate >= :sdate and a.regdate < date_add(:edate,interval 1 day))
			order by a.idx desc
		";
        $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);
        foreach($result as $row) {
            $row->country = $this->getCountry($row->country);
        }

		return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

	//국가 코드 vs 명
	function getCountry($country)
	{
		$data	= "-";

		if($country == "39178") $data = "South Korea";
		if($country == "39004") $data = "Sweden";
		if($country == "39078") $data = "HongKong";
		if($country == "39005") $data = "Denmark";
		if($country == "39006") $data = "Norway";
		if($country == "39007") $data = "Andorra";
		if($country == "39008") $data = "Angola";
		if($country == "39009") $data = "Anguilla";
		if($country == "39010") $data = "Antigua &amp; Barbuda";
		if($country == "39011") $data = "Argentina";
		if($country == "39012") $data = "Armenia";
		if($country == "39013") $data = "Aruba";
		if($country == "39014") $data = "Australia";
		if($country == "39015") $data = "Azerbaijan";
		if($country == "39016") $data = "Bahamas";
		if($country == "39017") $data = "Bahrain";
		if($country == "39018") $data = "Bangladesh";
		if($country == "39019") $data = "Barbados";
		if($country == "39020") $data = "Belgium";
		if($country == "39021") $data = "Belize";
		if($country == "39022") $data = "Benin";
		if($country == "39023") $data = "Bermuda";
		if($country == "39024") $data = "Bhutan";
		if($country == "39025") $data = "Bolivia";
		if($country == "39026") $data = "Bosnia and Herzegovina";
		if($country == "39027") $data = "Botswana";
		if($country == "39028") $data = "Brazil";
		if($country == "39029") $data = "British Virgin Islands";
		if($country == "39030") $data = "Brunei";
		if($country == "39031") $data = "Bulgaria";
		if($country == "39032") $data = "Burkina Faso";
		if($country == "39033") $data = "Burma";
		if($country == "39034") $data = "Burundi";
		if($country == "39035") $data = "Cayman Islands";
		if($country == "39036") $data = "Central African Republic";
		if($country == "39037") $data = "Chile";
		if($country == "39038") $data = "Colombia";
		if($country == "39039") $data = "Comorerna";
		if($country == "39040") $data = "Cook Islands";
		if($country == "39041") $data = "Costa Rica";
		if($country == "39042") $data = "Cypros";
		if($country == "39043") $data = "Dominica";
		if($country == "39044") $data = "Dominican Republic";
		if($country == "39045") $data = "Ecuador";
		if($country == "39046") $data = "Egypt";
		if($country == "39047") $data = "Equatorial Guinea";
		if($country == "39048") $data = "El Salvador";
		if($country == "39049") $data = "Ivory Coast (Cote d'Ivoire)";
		if($country == "39050") $data = "England";
		if($country == "39051") $data = "Eritrea";
		if($country == "39052") $data = "Estonia";
		if($country == "39053") $data = "Ethiopia";
		if($country == "39054") $data = "Falkland Is. (Malvinas)";
		if($country == "39055") $data = "Fiji";
		if($country == "39056") $data = "Philippines";
		if($country == "39057") $data = "Finland";
		if($country == "39058") $data = "France";
		if($country == "39059") $data = "French Guiana";
		if($country == "39060") $data = "French Polynesia";
		if($country == "39061") $data = "Faroe Islands";
		if($country == "39062") $data = "Gabon";
		if($country == "39063") $data = "Gambia";
		if($country == "39064") $data = "Georgia";
		if($country == "39065") $data = "Ghana";
		if($country == "39066") $data = "Gibraltar";
		if($country == "39067") $data = "Greece";
		if($country == "39068") $data = "Grenada";
		if($country == "39069") $data = "Greenland";
		if($country == "39070") $data = "Guadeloupe";
		if($country == "39071") $data = "Guam";
		if($country == "39072") $data = "Guatemala";
		if($country == "39073") $data = "Guinea";
		if($country == "39074") $data = "Guinea-Bissau";
		if($country == "39075") $data = "Guyana";
		if($country == "39076") $data = "Haiti";
		if($country == "39077") $data = "Honduras";
		if($country == "39078") $data = "Hongkong";
		if($country == "39079") $data = "India";
		if($country == "39080") $data = "Indonesia";
		if($country == "39081") $data = "Irac";
		if($country == "39082") $data = "Iran";
		if($country == "39083") $data = "Ireland";
		if($country == "39084") $data = "Island";
		if($country == "39085") $data = "Isle of Man";
		if($country == "39086") $data = "Israel";
		if($country == "39087") $data = "Italy";
		if($country == "39088") $data = "Jamaica";
		if($country == "39089") $data = "Japan";
		if($country == "39090") $data = "Jemen";
		if($country == "39091") $data = "Jordania";
		if($country == "39092") $data = "Cambodia";
		if($country == "39093") $data = "Cameroon";
		if($country == "39094") $data = "Canada";
		if($country == "39095") $data = "Kenya";
		if($country == "39096") $data = "China";
		if($country == "39097") $data = "Kiribati";
		if($country == "39098") $data = "Congo";
		if($country == "39099") $data = "Croatia";
		if($country == "39100") $data = "Cuba";
		if($country == "39101") $data = "Kuwait";
		if($country == "39102") $data = "Laos";
		if($country == "39103") $data = "Lesotho";
		if($country == "39104") $data = "Latvia";
		if($country == "39105") $data = "Lebanon";
		if($country == "39106") $data = "Liberia";
		if($country == "39107") $data = "Libya";
		if($country == "39108") $data = "Liechtenstein";
		if($country == "39109") $data = "Lithuania";
		if($country == "39110") $data = "Luxemburg";
		if($country == "39111") $data = "Madagascar";
		if($country == "39112") $data = "Maced";
		if($country == "39113") $data = "Malawi";
		if($country == "39114") $data = "Malaysia";
		if($country == "39115") $data = "Maldives";
		if($country == "39116") $data = "Mali";
		if($country == "39117") $data = "Malta";
		if($country == "39118") $data = "Marocko";
		if($country == "39119") $data = "Marshall Islands";
		if($country == "39120") $data = "Martinique";
		if($country == "39121") $data = "Mauritius";
		if($country == "39122") $data = "Mayotte";
		if($country == "39123") $data = "Mexico";
		if($country == "39124") $data = "Micronesia ";
		if($country == "39125") $data = "Mozambique";
		if($country == "39126") $data = "Moldova";
		if($country == "39127") $data = "Monaco";
		if($country == "39128") $data = "Mongolia";
		if($country == "39129") $data = "Namibia";
		if($country == "39130") $data = "Nauru";
		if($country == "39131") $data = "Netherlands";
		if($country == "39132") $data = "Netherlands Antilles";
		if($country == "39133") $data = "Nepal";
		if($country == "39134") $data = "Nicaragua";
		if($country == "39135") $data = "Niger";
		if($country == "39136") $data = "Nigeria";
		if($country == "39137") $data = "North Korea";
		if($country == "39138") $data = "Norway";
		if($country == "39139") $data = "New Zealand";
		if($country == "39140") $data = "Oman";
		if($country == "39141") $data = "Pakistan";
		if($country == "39142") $data = "Panama";
		if($country == "39143") $data = "Papua New Guinea";
		if($country == "39144") $data = "Paraguay";
		if($country == "39145") $data = "Peru";
		if($country == "39146") $data = "Pitcairn Island";
		if($country == "39147") $data = "Poland";
		if($country == "39148") $data = "Portugal";
		if($country == "39149") $data = "Puerto Rico";
		if($country == "39150") $data = "Reunion";
		if($country == "39151") $data = "Romania";
		if($country == "39152") $data = "Rwanda";
		if($country == "39153") $data = "Russia";
		if($country == "39154") $data = "Saint Christopher och Nevis";
		if($country == "39155") $data = "Saint Helena";
		if($country == "39156") $data = "Saint Lucia";
		if($country == "39157") $data = "Saint Vincent och Grenadinerna";
		if($country == "39158") $data = "Saint-Pierre-et-Miquelon";
		if($country == "39159") $data = "Salomonoarna";
		if($country == "39160") $data = "Samoa";
		if($country == "39161") $data = "Soo Tomo och Principe";
		if($country == "39162") $data = "Saudi Arabia";
		if($country == "39163") $data = "Schweiz";
		if($country == "39164") $data = "Senegal";
		if($country == "39165") $data = "Serbia";
		if($country == "39166") $data = "Sierra Leone";
		if($country == "39167") $data = "Singapore";
		if($country == "39168") $data = "Scottland";
		if($country == "39169") $data = "Slovakia";
		if($country == "39170") $data = "Slovenia";
		if($country == "39171") $data = "Spain";
		if($country == "39172") $data = "Sri Lanka";
		if($country == "39173") $data = "Great Britain";
		if($country == "39174") $data = "Sudan";
		if($country == "39175") $data = "Surinam";
		if($country == "39176") $data = "Swaziland";
		if($country == "39177") $data = "South Africa";
		if($country == "39178") $data = "South Korea";
		if($country == "39179") $data = "Syria";
		if($country == "39180") $data = "Taiwan";
		if($country == "39181") $data = "Tanzania";
		if($country == "39182") $data = "Tchad";
		if($country == "39183") $data = "Thailand";
		if($country == "39184") $data = "Czech Republic";
		if($country == "39185") $data = "Togo";
		if($country == "39186") $data = "Tonga";
		if($country == "39187") $data = "Trinidad &amp; Tobago";
		if($country == "39188") $data = "Tunisia";
		if($country == "39189") $data = "Turkey";
		if($country == "39190") $data = "Turkmenistan";
		if($country == "39191") $data = "Turks and Caicos Is";
		if($country == "39192") $data = "Tuvalu";
		if($country == "39193") $data = "Germany";
		if($country == "39194") $data = "Uganda";
		if($country == "39195") $data = "Ukraine";
		if($country == "39196") $data = "Hungaria";
		if($country == "39197") $data = "Uruguay";
		if($country == "39198") $data = "USA";
		if($country == "39199") $data = "Uzbekistan";
		if($country == "39200") $data = "Wales";
		if($country == "39201") $data = "Wallis and Futuna";
		if($country == "39202") $data = "Vanuatu";
		if($country == "39203") $data = "Venezuela";
		if($country == "39204") $data = "Vietnam";
		if($country == "39205") $data = "Belarus";
		if($country == "39206") $data = "Zambia";
		if($country == "39207") $data = "Zimbabwe";
		if($country == "39208") $data = "Austria";
		if($country == "39209") $data = "East Timor";

		return $data;
	}

    public function show($order_no)
	{
        $query	= "
			select
				a.group_nm, a.evt_idx, a.order_no, a.ord_state, a.good_name, a.qty, a.price,
				b.tno, b.card_cd, b.card_name, b.app_time, b.app_no, b.noinf, b.quota
			from evt_order a
			inner join evt_payment b on a.order_no = b.order_no
			where
				a.order_no = :order_no
		";
		$row = DB::selectOne($query, ['order_no' => $order_no]);

		$query	= "
				select
					a.idx, a.kind, a.evt_state, a.user_code, a.passwd, a.en_nm1, a.en_nm2, a.addr1, a.zipcode, a.area, a.country, a.mobile, a.email, a.birthdate, a.sex, a.group_nm,
					a.em_phone, a.dietary_yn, a.part_cnt_sweden, a.part_cnt_denmark, a.part_cnt_usa, a.part_cnt_hongkong
				from evt_member a
				where
					a.order_no = :order_no
		";
        $evt_mem = DB::select($query, ['order_no' => $order_no]);
        foreach($evt_mem as $row_mem) {

			switch($row_mem->sex)
			{
				case "M":	$row_mem->sex = "남성";	break;
				case "F":	$row_mem->sex = "여성";	break;
				default:	$row_mem->sex = "-";	break;
			}

			switch($row_mem->evt_state)
			{
				case "1":	$ord_state_nm = "입금예정";	break;
				case "9":	$ord_state_nm = "접수후보";	break;
				case "10":	$ord_state_nm = "접수완료"; break;
				case "20":	$ord_state_nm = "확정대기"; break;
				case "30":	$ord_state_nm = "확정완료";	break;
				case "-10":	$ord_state_nm = "결제오류";	break;
				case "-20":	$ord_state_nm = "신청취소"; break;
				default:	$ord_state_nm = "";			break;
			}
		}

		$group_nm1	= "";
		$group_nm2	= "";

        $query	= " select group_nm1, group_nm2 from evt_mst where idx = :evt_idx ";
		$row_group	= DB::selectOne($query, ['evt_idx' => $row->evt_idx]);
        if( !empty($row_group->group_nm1) )
		{
			$group_nm1	= $row_group->group_nm1;
			$group_nm2	= $row_group->group_nm2;
        }

		$values = [
            'order_no'	=> $order_no,
			'evt_idx'	=> $row->evt_idx,
			'good_name'	=> $row->good_name,
			'tno'		=> $row->tno,
			'qty'		=> $row->qty,
			'price'		=> $row->price,
			'card_cd'	=> $row->card_cd,
			'card_name'	=> $row->card_name,
			'app_time'	=> $row->app_time,
			'app_no'	=> $row->app_no,
			'noinf'		=> $row->noinf,
			'quota'		=> $row->quota,
			'group_nm1'	=> $group_nm1,
			'group_nm2'	=> $group_nm2,
			'evt_mem'	=> $evt_mem
        ];

		return view( Config::get('shop.head.view') . '/promotion/prm13_show',$values);
    }

    public function show2($order_no, $user_code)
	{
        $query	= "
			select
				a.group_nm, a.evt_idx, a.order_no, a.ord_state, a.good_name, a.qty, a.price,
				b.tno, b.card_cd, b.card_name, b.app_time, b.app_no, b.noinf, b.quota
			from evt_order a
			inner join evt_payment b on a.order_no = b.order_no
			where
				a.order_no = :order_no
		";
		$row = DB::selectOne($query, ['order_no' => $order_no]);

		$group_nm1	= "";
		$group_nm2	= "";

        $query	= " select group_nm1, group_nm2 from evt_mst where idx = :evt_idx ";
		$row_group	= DB::selectOne($query, ['evt_idx' => $row->evt_idx]);
        if( !empty($row_group->group_nm1) )
		{
			$group_nm1	= $row_group->group_nm1;
			$group_nm2	= $row_group->group_nm2;
        }

		$evt_mem = [];

		$query	= "
				select
					a.idx, a.kind, a.evt_state, a.user_code, a.passwd, a.en_nm1, a.en_nm2, a.addr1, a.zipcode, a.area, a.country, a.mobile, a.email, a.birthdate, a.sex, a.group_nm,
					a.em_phone, a.dietary_yn, a.part_cnt_sweden, a.part_cnt_denmark, a.part_cnt_usa, a.part_cnt_hongkong
				from evt_member a
				where
					a.user_code = :user_code
		";
        $row_mem = DB::selectOne($query, ['user_code' => $user_code]);

		switch($row_mem->sex)
		{
			case "M":	$row_mem->sex = "남성";	break;
			case "F":	$row_mem->sex = "여성";	break;
			default:	$row_mem->sex = "-";	break;
		}

		switch($row_mem->evt_state)
		{
			case "1":	$ord_state_nm = "입금예정";	break;
			case "9":	$ord_state_nm = "접수후보";	break;
			case "10":	$ord_state_nm = "접수완료"; break;
			case "20":	$ord_state_nm = "확정대기"; break;
			case "30":	$ord_state_nm = "확정완료";	break;
			case "-10":	$ord_state_nm = "결제오류";	break;
			case "-20":	$ord_state_nm = "신청취소"; break;
			default:	$ord_state_nm = "";			break;
		}

		$evt_mem	= array(
			"idx"			=> $row_mem->idx,
			"ord_state"		=> $row_mem->evt_state,
			"user_code"		=> $row_mem->user_code,
			"passwd"		=> $row_mem->passwd,
			"en_nm1"		=> $row_mem->en_nm1,
			"en_nm2"		=> $row_mem->en_nm2,
			"addr1"			=> $row_mem->addr1,
			"zipcode"		=> $row_mem->zipcode,
			"area"			=> $row_mem->area,
			"country"		=> $row_mem->country,
			"mobile"		=> $row_mem->mobile,
			"email"			=> $row_mem->email,
			"birthdate"		=> $row_mem->birthdate,
			"sex"			=> $row_mem->sex,
			"group_nm"		=> $row_mem->group_nm,
			"em_phone"		=> $row_mem->em_phone,
			"dietary_yn"	=> $row_mem->dietary_yn,
			"kind"			=> $row_mem->kind,
			"part_cnt_sweden"	=> $row_mem->part_cnt_sweden,
			"part_cnt_denmark"	=> $row_mem->part_cnt_denmark,
			"part_cnt_usa"		=> $row_mem->part_cnt_usa,
			"part_cnt_hongkong"	=> $row_mem->part_cnt_hongkong
		);


		$values	= [
			'order_no'	=> $order_no,
			'user_code'	=> $user_code,
			'evt_idx'	=> $row->evt_idx,
			'good_name'	=> $row->good_name,
			'tno'		=> $row->tno,
			'qty'		=> $row->qty,
			'price'		=> $row->price,
			'card_cd'	=> $row->card_cd,
			'card_name'	=> $row->card_name,
			'app_time'	=> $row->app_time,
			'app_no'	=> $row->app_no,
			'noinf'		=> $row->noinf,
			'quota'		=> $row->quota,
			'group_nm1'	=> $group_nm1,
			'group_nm2'	=> $group_nm2,
			'evt_mem'	=> $evt_mem
		];

		return view( Config::get('shop.head.view') . '/promotion/prm13_part_show',$values);
	}

    public function chgstate(Request $request)
	{
		$order_no		= $request->input('order_no');
		$user_code		= $request->input('user_code');
		$evt_state		= $request->input('evt_state');

		$error_code		= "200";
		$result_code	= "";

		if( $order_no == "" || $user_code == "" )
		{
			$error_code	= "400";
		}

		DB::beginTransaction();

		$query	= " update evt_member set evt_state = :evt_state where user_code = :user_code ";
		DB::update($query, ['user_code' => $user_code, 'evt_state' => $evt_state]);
		
		DB::commit();


		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);
	}

	public function update($order_no, $user_code, Request $request)
	{
		$error_code		= "200";
		$result_code	= "";

		$evt_state		= $request->input("evt_state");

		if( $order_no == "" || $user_code == "" || $evt_state == "" )
		{
			$error_code	= "400";
		}

		$kind			= $request->input("kind");
		$passwd			= $request->input("passwd");
		$en_nm1			= $request->input("en_nm1");
		$en_nm2			= $request->input("en_nm2");
		$addr1			= $request->input("addr1");
		$zipcode		= $request->input("zipcode");
		$area			= $request->input("area");
		$country		= $request->input("country");
		$mobile			= $request->input("mobile");
		$email			= $request->input("email");
		$birthdate		= $request->input("birthdate");
		$sex			= $request->input("sex");
		$group_nm		= $request->input("group_nm");
		$em_phone		= $request->input("em_phone");
		$dietary_yn		= $request->input("dietary_yn");
		$part_cnt_sweden	= $request->input("part_cnt_sweden");
		$part_cnt_denmark	= $request->input("part_cnt_denmark");
		$part_cnt_usa		= $request->input("part_cnt_usa");
		$part_cnt_hongkong	= $request->input("part_cnt_hongkong");

		DB::beginTransaction();

		$query	= " 
			update evt_member set
				evt_state	= :evt_state,
				kind		= :kind,
				passwd		= :passwd,
				en_nm1		= :en_nm1,
				en_nm2		= :en_nm2,
				addr1		= :addr1,
				zipcode		= :zipcode,
				area		= :area,
				country		= :country,
				mobile		= :mobile,
				email		= :email,
				birthdate	= :birthdate,
				sex			= :sex,
				group_nm	= :group_nm,
				em_phone	= :em_phone,
				dietary_yn	= :dietary_yn,
				part_cnt_sweden		= :part_cnt_sweden,
				part_cnt_denmark	= :part_cnt_denmark,
				part_cnt_usa		= :part_cnt_usa,
				part_cnt_hongkong	= :part_cnt_hongkong
			where
				user_code = :user_code
		";
		DB::update($query, 
			[
				'evt_state'		 => $evt_state,
				'kind'			=> $kind,
				'passwd'		=> $passwd,
				'en_nm1'		=> $en_nm1,
				'en_nm2'		=> $en_nm2,
				'addr1'			=> $addr1,
				'zipcode'		=> $zipcode,
				'area'			=> $area,
				'country'		=> $country,
				'mobile'		=> $mobile,
				'email'			=> $email,
				'birthdate'		=> $birthdate,
				'sex'			=> $sex,
				'group_nm'		=> $group_nm,
				'em_phone'		=> $em_phone,
				'dietary_yn'	=> $dietary_yn,
				'part_cnt_sweden'	=> $part_cnt_sweden,
				'part_cnt_denmark'	=> $part_cnt_denmark,
				'part_cnt_usa'		=> $part_cnt_usa,
				'part_cnt_hongkong'	=> $part_cnt_hongkong,
				'user_code'		=> $user_code
			]
		);
		
		DB::commit();
		
		return response()->json([
			"code" => $error_code,
			"result_code" => $result_code
		]);
	}

	public function chgstate_arr(Request $request)
	{
		$evt_state		= $request->input("s1_evt_state");
		$evt_mem_idx	= implode(',', $request->input("evt_mem_idxs"));

		$query	= " 
			update evt_member set 
				evt_state = :evt_state 
			where idx in ($evt_mem_idx)
		";

		DB::update($query, ['evt_state' => $evt_state]);
		
		return response()->json([
			"code" => "200",
			"result_code" => true
		]);
	}

}
