<?php

namespace App\Http\Controllers\head\classic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class cls03Controller extends Controller
{
    private $evt_state;
    private $country_info;

    // 임시 공통정보
    public function __construct()
    {
        $this->evt_state = [
            '1'   => '입금예정',
            '5'   => '접수후보',
            '9'   => '후보결제대기',
            '10'  => '접수완료',
            '20'  => '확정대기',
            '30'  => '확정완료',
            '-10' => '결제오류',
            '-20' => '신청취소'
        ];

        $this->country_info = [
            '39178' => 'South Korea',
            '39004' => 'Sweden',
            '39078' => 'HongKong',
            '39005' => 'Denmark',
            '39006' => 'Norway',
            '39007' => 'Andorra',
            '39008' => 'Angola',
            '39009' => 'Anguilla',
            '39010' => 'Antigua &amp; Barbuda',
            '39011' => 'Argentina',
            '39012' => 'Armenia',
            '39013' => 'Aruba',
            '39014' => 'Australia',
            '39015' => 'Azerbaijan',
            '39016' => 'Bahamas',
            '39017' => 'Bahrain',
            '39018' => 'Bangladesh',
            '39019' => 'Barbados',
            '39020' => 'Belgium',
            '39021' => 'Belize',
            '39022' => 'Benin',
            '39023' => 'Bermuda',
            '39024' => 'Bhutan',
            '39025' => 'Bolivia',
            '39026' => 'Bosnia and Herzegovina',
            '39027' => 'Botswana',
            '39028' => 'Brazil',
            '39029' => 'British Virgin Islands',
            '39030' => 'Brunei',
            '39031' => 'Bulgaria',
            '39032' => 'Burkina Faso',
            '39033' => 'Burma',
            '39034' => 'Burundi',
            '39035' => 'Cayman Islands',
            '39036' => 'Central African Republic',
            '39037' => 'Chile',
            '39038' => 'Colombia',
            '39039' => 'Comorerna',
            '39040' => 'Cook Islands',
            '39041' => 'Costa Rica',
            '39042' => 'Cypros',
            '39043' => 'Dominica',
            '39044' => 'Dominican Republic',
            '39045' => 'Ecuador',
            '39046' => 'Egypt',
            '39047' => 'Equatorial Guinea',
            '39048' => 'El Salvador',
            '39049' => 'Ivory Coast (Cote d\'Ivoire)',
            '39050' => 'England',
            '39051' => 'Eritrea',
            '39052' => 'Estonia',
            '39053' => 'Ethiopia',
            '39054' => 'Falkland Is. (Malvinas)',
            '39055' => 'Fiji',
            '39056' => 'Philippines',
            '39057' => 'Finland',
            '39058' => 'France',
            '39059' => 'French Guiana',
            '39060' => 'French Polynesia',
            '39061' => 'Faroe Islands',
            '39062' => 'Gabon',
            '39063' => 'Gambia',
            '39064' => 'Georgia',
            '39065' => 'Ghana',
            '39066' => 'Gibraltar',
            '39067' => 'Greece',
            '39068' => 'Grenada',
            '39069' => 'Greenland',
            '39070' => 'Guadeloupe',
            '39071' => 'Guam',
            '39072' => 'Guatemala',
            '39073' => 'Guinea',
            '39074' => 'Guinea-Bissau',
            '39075' => 'Guyana',
            '39076' => 'Haiti',
            '39077' => 'Honduras',
            '39078' => 'Hongkong',
            '39079' => 'India',
            '39080' => 'Indonesia',
            '39081' => 'Irac',
            '39082' => 'Iran',
            '39083' => 'Ireland',
            '39084' => 'Island',
            '39085' => 'Isle of Man',
            '39086' => 'Israel',
            '39087' => 'Italy',
            '39088' => 'Jamaica',
            '39089' => 'Japan',
            '39090' => 'Jemen',
            '39091' => 'Jordania',
            '39092' => 'Cambodia',
            '39093' => 'Cameroon',
            '39094' => 'Canada',
            '39095' => 'Kenya',
            '39096' => 'China',
            '39097' => 'Kiribati',
            '39098' => 'Congo',
            '39099' => 'Croatia',
            '39100' => 'Cuba',
            '39101' => 'Kuwait',
            '39102' => 'Laos',
            '39103' => 'Lesotho',
            '39104' => 'Latvia',
            '39105' => 'Lebanon',
            '39106' => 'Liberia',
            '39107' => 'Libya',
            '39108' => 'Liechtenstein',
            '39109' => 'Lithuania',
            '39110' => 'Luxemburg',
            '39111' => 'Madagascar',
            '39112' => 'Maced',
            '39113' => 'Malawi',
            '39114' => 'Malaysia',
            '39115' => 'Maldives',
            '39116' => 'Mali',
            '39117' => 'Malta',
            '39118' => 'Marocko',
            '39119' => 'Marshall Islands',
            '39120' => 'Martinique',
            '39121' => 'Mauritius',
            '39122' => 'Mayotte',
            '39123' => 'Mexico',
            '39124' => 'Micronesia ',
            '39125' => 'Mozambique',
            '39126' => 'Moldova',
            '39127' => 'Monaco',
            '39128' => 'Mongolia',
            '39129' => 'Namibia',
            '39130' => 'Nauru',
            '39131' => 'Netherlands',
            '39132' => 'Netherlands Antilles',
            '39133' => 'Nepal',
            '39134' => 'Nicaragua',
            '39135' => 'Niger',
            '39136' => 'Nigeria',
            '39137' => 'North Korea',
            '39138' => 'Norway',
            '39139' => 'New Zealand',
            '39140' => 'Oman',
            '39141' => 'Pakistan',
            '39142' => 'Panama',
            '39143' => 'Papua New Guinea',
            '39144' => 'Paraguay',
            '39145' => 'Peru',
            '39146' => 'Pitcairn Island',
            '39147' => 'Poland',
            '39148' => 'Portugal',
            '39149' => 'Puerto Rico',
            '39150' => 'Reunion',
            '39151' => 'Romania',
            '39152' => 'Rwanda',
            '39153' => 'Russia',
            '39154' => 'Saint Christopher och Nevis',
            '39155' => 'Saint Helena',
            '39156' => 'Saint Lucia',
            '39157' => 'Saint Vincent och Grenadinerna',
            '39158' => 'Saint-Pierre-et-Miquelon',
            '39159' => 'Salomonoarna',
            '39160' => 'Samoa',
            '39161' => 'Soo Tomo och Principe',
            '39162' => 'Saudi Arabia',
            '39163' => 'Schweiz',
            '39164' => 'Senegal',
            '39165' => 'Serbia',
            '39166' => 'Sierra Leone',
            '39167' => 'Singapore',
            '39168' => 'Scottland',
            '39169' => 'Slovakia',
            '39170' => 'Slovenia',
            '39171' => 'Spain',
            '39172' => 'Sri Lanka',
            '39173' => 'Great Britain',
            '39174' => 'Sudan',
            '39175' => 'Surinam',
            '39176' => 'Swaziland',
            '39177' => 'South Africa',
            '39178' => 'South Korea',
            '39179' => 'Syria',
            '39180' => 'Taiwan',
            '39181' => 'Tanzania',
            '39182' => 'Tchad',
            '39183' => 'Thailand',
            '39184' => 'Czech Republic',
            '39185' => 'Togo',
            '39186' => 'Tonga',
            '39187' => 'Trinidad &amp; Tobago',
            '39188' => 'Tunisia',
            '39189' => 'Turkey',
            '39190' => 'Turkmenistan',
            '39191' => 'Turks and Caicos Is',
            '39192' => 'Tuvalu',
            '39193' => 'Germany',
            '39194' => 'Uganda',
            '39195' => 'Ukraine',
            '39196' => 'Hungaria',
            '39197' => 'Uruguay',
            '39198' => 'USA',
            '39199' => 'Uzbekistan',
            '39200' => 'Wales',
            '39201' => 'Wallis and Futuna',
            '39202' => 'Vanuatu',
            '39203' => 'Venezuela',
            '39204' => 'Vietnam',
            '39205' => 'Belarus',
            '39206' => 'Zambia',
            '39207' => 'Zimbabwe',
            '39208' => 'Austria',
            '39209' => 'East Timor',
        ];
    }

    // 트래킹 메인
	public function index(Request $request)
	{
        $mutable = Carbon::now();
        $sdate	 = $mutable->sub(2, 'year')->format('Y-m-d');

        $values  = [
            'event_state'   => $this->evt_state,
            'country_info'  => $this->country_info,
            'sdate'         => $sdate,
            'edate'         => date('Y-m-d')
        ];

		return view( Config::get('shop.head.view') . '/classic/cls03', $values);
	}

    // 일괄 접수상태 변경
    public function change_state(Request $request)
	{
		$evt_state   = $request->input("s1_evt_state");
		$evt_mem_idx = $request->input("evt_mem_idxs");

        try {
            $affected = DB::table('evt_member')
            ->whereIn('idx', $evt_mem_idx)
            ->update([
                'evt_state' => $evt_state
            ]);
        } catch (Exception $exception) {
            return response()->json(['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
        }
        return response()->json(['code' => 200, 'msg' => $affected]);
	}

    // 트레킹 리스트 검색
	public function search(Request $request)
    {
        $params['sdate']        = $request->input('sdate', Carbon::now()->sub(2, 'year')->format('Ymd'));
        $params['edate']        = $request->input('edate', date("Ymd"));
        $params['title']        = $request->input('s_title');
        $params['evt_idx']      = $request->input('s_evt_idx');
		$params['order_no']	    = $request->input('s_order_no');
		$params['user_code']	= $request->input('s_user_code');
        $params['evt_state']	= $request->input('s_evt_state');
		$params['user_nm']	    = $request->input('s_user_nm');
		$params['mobile']		= $request->input('s_mobile');
		$params['sex']		    = $request->input('s_sex');
		$params['country']	    = $request->input('s_country');

        $page		= $request->input("page", 1);
		$page_size 	= $request->input("limit", 100);
		$total      = 0;

        try {

            if ($page == 1) {
                $query = DB::table('evt_member as member');
                $query = $query->join('evt_mst as mst', 'member.evt_idx', '=', 'mst.idx');
                $query = $this->search_where($query, $params);
                $total = $query->count();
            }


            $query = DB::table('evt_member as member');
            $query = $query->selectRaw('
                "" as chk,
                member.evt_idx,
                mst.title,
                member.order_no,
                member.evt_state,
                order.kind,
                member.user_id,
                member.user_code,
                member.user_nm,
                concat(member.en_nm1, " ", member.en_nm2) as en_nm,
                member.kind as ckind,
                member.mobile,
                member.email,
                member.sex,
                member.country,
                member.birthdate,
                member.em_phone,
                concat(member.group_nm, "그룹") as group_nm,
                member.team_nm, concat(member.addr1, " ", member.addr2) as addr,
                member.regdate,
                member.evt_state,
                member.idx as evt_mem_idx,
                member.seq,
                member.dietary_yn
            ');
            $query = $query->join('evt_mst as mst', 'member.evt_idx', '=', 'mst.idx');
            $query = $query->join('evt_order as order', 'member.order_no', '=', 'order.order_no');
            $query = $this->search_where($query, $params);
            $query = $query->orderBy('member.idx', 'desc');
            $receipt_list = $query->paginate($page_size);

            $return_values = [];
            foreach($receipt_list as $key => $values) {
                $array_values = (array)$values;

                $array_values['user_code']      = empty($array_values['user_code']) ? '' : $array_values['user_code'];
                $array_values['ckind']          = $array_values['ckind'] == 0 ? '성인' : '소인';
                $array_values['sex']            = $array_values['sex'] == 'M' ? '남성' : '여성';
                $array_values['country']        = isset($this->country_info[$array_values['country']]) ? $this->country_info[$array_values['country']] : '';
                $array_values['evt_state_nm']   = isset($this->evt_state[$array_values['evt_state']]) ? $this->evt_state[$array_values['evt_state']] : '';

                $return_values[] = $array_values;
            }
        } catch (Exception $exception) {
            return response()->json(['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
        }
		return response()->json([
            'code' => 200,
            'head' => [
                'total'     => $total,
                'page'      => $page
            ],
            'body' => $return_values
        ]);
    }

    // 검색 조건
    public function search_where($query, $params)
	{
        $query = $query->where('member.regdate', '>=', $params['sdate']);
        $query = $query->where('member.regdate', '<=', $params['edate']);
        $query = $query->when($params['title'], function ($where) use ($params) {
            return $where->where('mst.title', 'like', '%'.$params['title'].'%');
        });
        $query = $query->when($params['evt_idx'], function ($where) use ($params) {
            return $where->where('member.evt_idx', $params['evt_idx']);
        });
        $query = $query->when($params['order_no'], function ($where) use ($params) {
            return $where->where('member.order_no', $params['order_no']);
        });
        $query = $query->when($params['user_code'], function ($where) use ($params) {
            return $where->where('member.user_code', $params['user_code']);
        });
        $query = $query->when($params['evt_state'], function ($where) use ($params) {
            return $where->where('member.evt_state', $params['evt_state']);
        });
        $query = $query->when($params['user_nm'], function ($where) use ($params) {
            return $where->where('member.user_nm', 'like', '%' . $params['user_nm'] . '%');
        });
        $query = $query->when($params['mobile'], function ($where) use ($params) {
            return $where->where('member.mobile', $params['mobile']);
        });
        $query = $query->when($params['sex'], function ($where) use ($params) {
            return $where->where('member.sex', $params['sex']);
        });
        $query = $query->when($params['country'], function ($where) use ($params) {
            return $where->where('member.country', $params['country']);
        });

        return $query;
    }

    // 트레킹 결제건별 보기
    public function show($order_no)
	{
        $query = DB::table('evt_order as order');
        $query = $query->selectRaw('
            order.group_nm,
            order.evt_idx,
            order.order_no,
            order.ord_state,
            order.good_name,
            order.qty,
            order.price,
            payment.tno,
            payment.card_cd,
            payment.card_name,
            payment.app_time,
            payment.app_no,
            payment.noinf,
            payment.quota
        ');
        $query = $query->join('evt_payment as payment', 'order.order_no', '=', 'payment.order_no');
        $query = $query->where('order.order_no', $order_no);
        $order_info = (array)$query->first();


        $query = DB::table('evt_mst');
        $query = $query->selectRaw('
            group_nm1,
            group_nm2
        ');
        $query = $query->where('idx', $order_info['evt_idx']);
        $group_info = (array)$query->first();


        $query = DB::table('evt_member');
        $query = $query->selectRaw('
            idx,
            kind,
            evt_state,
            user_code,
            passwd,
            en_nm1,
            en_nm2,
            addr1,
            zipcode,
            area,
            country,
            mobile,
            email,
            birthdate,
            sex,
            group_nm,
            em_phone,
            dietary_yn,
            part_cnt_sweden,
            part_cnt_denmark,
            part_cnt_usa,
            part_cnt_hongkong
        ');
        $query = $query->where('order_no', $order_no);
        $member_list = $query->get();

        $member_values = [];
        foreach($member_list as $key => $values) {
            $array_values = (array)$values;

            $array_values['kind']        = $array_values['kind'] == 0 ? '성인' : '소인';
            $array_values['sex']         = $array_values['sex'] == 'M' ? '남성' : '여성';
            $array_values['country']     = isset($this->country_info[$array_values['country']]) ? $this->country_info[$array_values['country']] : '';
            $array_values['evt_state']   = isset($this->evt_state[$array_values['evt_state']]) ? $this->evt_state[$array_values['evt_state']] : '';
            $array_values['group_nm']    = $array_values['group_nm'] == '1' ? $group_info['group_nm1'] : $group_info['group_nm2'];

            $member_values[] = $array_values;
        }

		$values = [
            'order_no'	=> $order_no,
			'evt_idx'	=> $order_info['evt_idx'],
			'good_name'	=> $order_info['good_name'],
			'tno'		=> $order_info['tno'],
			'qty'		=> $order_info['qty'],
			'price'		=> $order_info['price'],
			'card_cd'	=> $order_info['card_cd'],
			'card_name'	=> $order_info['card_name'],
			'app_time'	=> $order_info['app_time'],
			'app_no'	=> $order_info['app_no'],
			'noinf'		=> $order_info['noinf'],
			'quota'		=> $order_info['quota'],
			'evt_member'=> $member_values
        ];

		return view( Config::get('shop.head.view') . '/classic/cls03_show', $values);
    }

    // 트레킹 접수자별 보기
    public function show_user($order_no, $user_code)
	{
        $query = DB::table('evt_order as order');
        $query = $query->selectRaw('
            order.group_nm,
            order.evt_idx,
            order.order_no,
            order.ord_state,
            order.good_name,
            order.qty,
            order.price,
            payment.tno,
            payment.card_cd,
            payment.card_name,
            payment.app_time,
            payment.app_no,
            payment.noinf,
            payment.quota
        ');
        $query = $query->join('evt_payment as payment', 'order.order_no', '=', 'payment.order_no');
        $query = $query->where('order.order_no', $order_no);
        $order_info = (array)$query->first();


        $query = DB::table('evt_mst');
        $query = $query->selectRaw('
            group_nm1,
            group_nm2
        ');
        $query = $query->where('idx', $order_info['evt_idx']);
        $group_info = (array)$query->first();


		$query = DB::table('evt_member');
        $query = $query->selectRaw('
            idx,
            kind,
            evt_state,
            user_code,
            passwd,
            en_nm1,
            en_nm2,
            addr1,
            zipcode,
            area,
            country,
            mobile,
            email,
            birthdate,
            sex,
            group_nm,
            em_phone,
            dietary_yn,
            part_cnt_sweden,
            part_cnt_denmark,
            part_cnt_usa,
            part_cnt_hongkong
        ');
        $query = $query->where('order_no', $order_no);
        $member_info = (array)$query->first();

		$values = [
            'order_no'	    => $order_no,
			'user_code'	    => $user_code,
            'evt_idx'	    => $order_info['evt_idx'],
			'good_name'	    => $order_info['good_name'],
			'tno'		    => $order_info['tno'],
			'qty'		    => $order_info['qty'],
			'price'		    => $order_info['price'],
			'card_cd'	    => $order_info['card_cd'],
			'card_name'	    => $order_info['card_name'],
			'app_time'	    => $order_info['app_time'],
			'app_no'	    => $order_info['app_no'],
			'noinf'		    => $order_info['noinf'],
			'quota'		    => $order_info['quota'],
			'evt_member'    => $member_info,
            'group_info'    => $group_info,
            'event_state'   => $this->evt_state,
            'country_info'  => $this->country_info
        ];

		return view( Config::get('shop.head.view') . '/classic/cls03_part_show', $values);
	}

    // 트레킹 접수자별 수정
    public function show_update($order_no, $user_code, Request $request)
	{
		$evt_state		    = $request->input("evt_state");
		$kind			    = $request->input("kind");
		$passwd			    = $request->input("passwd");
		$en_nm1			    = $request->input("en_nm1");
		$en_nm2			    = $request->input("en_nm2");
		$addr1			    = $request->input("addr1");
		$zipcode		    = $request->input("zipcode");
		$area			    = $request->input("area");
		$country		    = $request->input("country");
		$mobile			    = $request->input("mobile");
		$email			    = $request->input("email");
		$birthdate		    = $request->input("birthdate");
		$sex			    = $request->input("sex");
		$group_nm		    = $request->input("group_nm");
		$em_phone		    = $request->input("em_phone");
		$dietary_yn		    = $request->input("dietary_yn");
		$part_cnt_sweden	= $request->input("part_cnt_sweden");
		$part_cnt_denmark	= $request->input("part_cnt_denmark");
		$part_cnt_usa		= $request->input("part_cnt_usa");
		$part_cnt_hongkong	= $request->input("part_cnt_hongkong");

        try {
            $affected = DB::table('evt_member')
            ->where('user_code', $user_code)
            ->update([
                'evt_state'         => $evt_state,
                'kind'              => $kind,
                'passwd'            => $passwd,
                'en_nm1'            => $en_nm1,
                'en_nm2'            => $en_nm2,
                'addr1'             => $addr1,
                'zipcode'           => $zipcode,
                'area'              => $area,
                'country'           => $country,
                'mobile'            => $mobile,
                'email'             => $email,
                'birthdate'         => $birthdate,
                'sex'               => $sex,
                'group_nm'          => $group_nm,
                'em_phone'          => $em_phone,
                'dietary_yn'        => $dietary_yn,
                'part_cnt_sweden'   => $part_cnt_sweden,
                'part_cnt_denmark'  => $part_cnt_denmark,
                'part_cnt_usa'      => $part_cnt_usa,
                'part_cnt_hongkong' => $part_cnt_hongkong
            ]);
        } catch (Exception $exception) {
            return response()->json(['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
        }
        return response()->json(['code' => 200, 'msg' => $affected]);
	}




}
