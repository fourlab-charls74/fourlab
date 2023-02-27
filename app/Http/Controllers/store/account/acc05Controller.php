<?php

namespace App\Http\Controllers\store\account;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use App\Components\SLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

// 기타재반자료
class acc05Controller extends Controller
{
    public function index(Request $request) 
    {
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준
        $extra_cols = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2'); // code_val2를 상위 카테고리로 사용

        $values = [
            'sdate' => $sdate,
            'extra_cols' => $extra_cols,
        ];

        return view( Config::get('shop.store.view') . '/account/acc05', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        $sdate = Lib::quote(str_replace('-', '', $sdate));
        $edate = $request->input('edate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        $edate = Lib::quote(str_replace('-', '', $edate));
        $extra_types = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2')->toArray(); // 사은품/소모품 외 기타재반

        $extra_sql = "";
        $sum_extra_sql = "";
        
        // 기타재반타입별 쿼리문 생성
        foreach ($extra_types as $key => $types) {
            foreach ($types as $type) {
                $extra_sql .= ", sum(if(el.type = '" . $type->code_id . "', el.extra_amt, null)) as " . $type->code_id . "_amt";
                $sum_extra_sql .= ", sum(" . $type->code_id . "_amt) as " . $type->code_id . "_amt";
            }

            $group_cd = str_split($types[0]->code_id ?? '')[0];
            $types_arr = $group_cd === 'E' 
                ? array_filter($types, function($t) { return !in_array($t->code_id, ['E1', 'E2']); }) // E1(온라인RT), E2(온라인반송)은 소계에 포함시키지 않습니다. (because, E3(온라인) = E1 - E2)
                : $types;
            $types_str = array_map(function($t) { return "'" . $t->code_id . "'"; }, $types_arr);

            $extra_sql .= ", sum(if(el.type in (" . join(',', $types_str) . "), if(el.type in ('P1', 'M3'), round(el.extra_amt / 1.1), el.extra_amt), null)) as " . $group_cd . "_sum";
            $sum_extra_sql .= ", sum(" . $group_cd . "_sum) as " . $group_cd . "_sum";
        }

        // 사은품/소모품 쿼리문 생성
        $extra_sql .= ", sum(if(el.type = 'G', el.extra_amt, null)) as G_sum";
        $extra_sql .= ", sum(if(el.type = 'S', el.extra_amt, null)) as S_sum";
        $sum_extra_sql .= ", sum(G_sum) as G_sum";
        $sum_extra_sql .= ", sum(S_sum) as S_sum";

        $sql = "
            select a.ymonth as ymonth
                , sum(total_amt) as total_amt
                $sum_extra_sql
            from (
                select e.ymonth, e.extra_amt as total_amt
                    $extra_sql
                from store_account_extra e
                    inner join store_account_extra_list el on el.ext_idx = e.idx
                where e.ymonth >= '$sdate' and e.ymonth <= '$edate'
                group by e.ymonth, e.store_cd
            ) a
            group by a.ymonth
        ";
        $result = DB::select($sql);

        return response()->json([
            'code'	=> 200,
            'head'	=> [
                'total'	=> count($result),
            ],
            'body' => $result
        ]);
    }

    public function show(Request $request)
    {
        $cmd = $request->input('date') === null ? 'add' : 'update';

        $sdate = $request->input('date', '');
        if ($sdate === '') $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m");
        else $sdate = substr_replace($sdate, '-', 4, 0);

        $store_cd = $request->input('store_cd', '');
        $store = DB::table('store')->where('store_cd', $store_cd)->select('store_cd', 'store_nm')->first();

        $extra_cols = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2'); // code_val2를 상위 카테고리로 사용

        $values = [
            'cmd' => $cmd,
            'sdate' => $sdate,
            'store' => $store,
            'sdate_str' => Carbon::parse($sdate)->format("Y년 m월"),
            'extra_cols' => $extra_cols,
        ];

        return view( Config::get('shop.store.view') . '/account/acc05_show', $values );
    }

    public function show_search(Request $request)
    {
        $cmd = $request->input('cmd', 'add');
        $store_cd = $request->input('store_cd', '');
        $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Y-m"));

        // 유효성 검사
        $sql = "
            select count(*) as total
            from store_account_extra
            where ymonth = :sdate
        ";
        $cnt = DB::selectOne($sql, ['sdate' => Lib::quote(str_replace('-', '', $sdate))])->total;

        if ($cmd === 'add' && $cnt > 0) {
            return response()->json(['code' => 400, 'head' => [
                'total' => 0, 
                'sdate' => $sdate, 
                'msg' => '해당연월의 기타재반자료정보가 이미 존재합니다.'
            ]]);
        } else if ($cmd === 'update' && $cnt < 1) {
            return response()->json(['code' => 400, 'head' => [
                'total' => 0, 
                'sdate' => $sdate, 
                'msg' => '해당연월의 기타재반자료정보가 존재하지 않습니다.'
            ]]);
        }

        // 해당연월의 원부자재정보 조회
        $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Y-m-d H:i:s");
        $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Y-m-d H:i:s");
        $sdate = Lib::quote(str_replace('-', '', $sdate));

        $sql = "
            select r.prd_cd, p.prd_nm, p.type
            from sproduct_stock_release r
                inner join store s on s.store_cd = r.store_cd and s.account_yn = 'Y'
                inner join product p on p.prd_cd = r.prd_cd and p.type = :type
            where r.fin_rt >= '$f_sdate' and r.fin_rt <= '$f_edate'
            group by r.prd_cd
        ";
        $gifts = DB::select($sql, ['type' => 'G']); // 사은품
        $expandables = DB::select($sql, ['type' => 'S']); // 소모품
        $extra_types = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2')->toArray(); // 사은품/소모품 외 기타재반

        // 기타재반 항목별 쿼리문 생성
        $extra_sql = "";
        foreach ($extra_types as $key => $types) {
            foreach ($types as $type) {
                $extra_sql .= ", sum(if(el.type = '" . $type->code_id . "', el.extra_amt, null)) as " . $type->code_id . "_amt";
            }

            $group_cd = str_split($types[0]->code_id ?? '')[0];
            $types_arr = $group_cd === 'E' 
                ? array_filter($types, function($t) { return !in_array($t->code_id, ['E1', 'E2']); }) // E1(온라인RT), E2(온라인반송)은 소계에 포함시키지 않습니다. (because, E3(온라인) = E1 - E2)
                : $types;
            $types_str = array_map(function($t) { return "'" . $t->code_id . "'"; }, $types_arr);

            // 마일리지(P1)와 본사수선비(M3)의 경우, 세금을 제한 값을 합계로 조회합니다.
            $extra_sql .= ", sum(if(el.type in (" . join(',', $types_str) . "), if(el.type in ('P1', 'M3'), round(el.extra_amt / 1.1), el.extra_amt), null)) as " . $group_cd . "_sum";
        }

        // 사은품 쿼리문 생성
        $extra_sql .= join('', array_map(function($value) {
            return ", sum(if(el.type = 'G' and (el.prd_cd = '" . $value->prd_cd . "' or el.prd_nm = '" . $value->prd_nm . "'), el.extra_amt, null)) as G_" . $value->prd_cd . "_amt";
        }, $gifts));
        $extra_sql .= ", sum(if(el.type = 'G', el.extra_amt, null)) as G_sum";
        
        // 소모품 쿼리문 생성
        $extra_sql .= join('', array_map(function($value) {
            return ", sum(if(el.type = 'S' and (el.prd_cd = '" . $value->prd_cd . "' or el.prd_nm = '" . $value->prd_nm . "'), el.extra_amt, null)) as S_" . $value->prd_cd . "_amt";
        }, $expandables));
        $extra_sql .= ", sum(if(el.type = 'S', el.extra_amt, null)) as S_sum";

        $f_sdate = Carbon::parse($f_sdate)->firstOfMonth()->format("Ymd");
        $f_edate = Carbon::parse($f_edate)->lastOfMonth()->format("Ymd");

        $where = "";
        if ($store_cd != '') $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";

        // search
        $sql = "
            select s.store_cd, s.store_nm, s.store_type, e.*, c.*
            from store s
                left outer join (
                    select e.idx as ext_idx, e.ymonth, e.store_cd as e_store_cd
                        , e.extra_amt as total
                        $extra_sql
                    from store_account_extra e
                        inner join store_account_extra_list el on el.ext_idx = e.idx
                    where e.ymonth = :sdate
                    group by e.store_cd
                ) e on e.e_store_cd = s.store_cd
                left outer join (
					select c.store_cd as c_store_cd, c.closed_yn
                    from store_account_closed c
                    where c.sday = :f_sdate and c.eday = :f_edate
                ) c on c.c_store_cd = s.store_cd
            where s.account_yn = 'Y' $where
            order by s.store_cd
        ";
        $result = DB::select($sql, ['sdate' => $sdate, 'f_sdate' => $f_sdate, 'f_edate' => $f_edate]);
        
        return response()->json([
            'code'	=> 200,
            'head'	=> [
                'total'	=> count($result),
                'gifts' => $gifts,
                'expandables' => $expandables,
            ],
            'body' => $result
        ]);
    }

    public function save(Request $request)
	{
        $cmd = $request->input('cmd', 'add');
        $save_type = $request->input('type', 'G'); // G: 일반, B: 일괄
		$data = $request->input('data');
        $sdate = $request->input('sdate', '');
        $sdate = Lib::quote(str_replace('-', '', $sdate));

        $code = "200";
		$msg = "";
        $admin_id = Auth('head')->user()->id;

		try {
			DB::beginTransaction();

            foreach ($data as $extra) {
                $amts = array_filter($extra, function($key) {
                    $key_arr = (explode('_', $key));
                    return end($key_arr) === 'amt';
                }, ARRAY_FILTER_USE_KEY);
                $ymonth = $extra['ymonth'] ?? $sdate;
                $store_cd = $extra['store_cd'];

                // 타입별정보 가공
                $extra_list = [];
                $total_amt = 0;

                foreach ($amts as $key => $value) {
                    $type = explode('_', $key)[0];
                    $prd_cd = null;
                    if (in_array($type, ['S', 'G'])) $prd_cd = explode('_', $key)[1];

                    array_push($extra_list, [
                        'type' => $type,
                        'prd_cd' => $prd_cd,
                        'prd_nm' => null,
                        'extra_amt' => $value ?? 0,
                    ]);

                    // 총합계 계산
                    if (!in_array($type, ['P1', 'E1', 'E2', 'M3'])) $total_amt += $value ?? 0;
                    if (in_array($type, ['P1', 'M3'])) $total_amt += round(($value ?? 0) / 1.1);
                }

                // 기존정보가 있을경우 삭제
                $originals = DB::table('store_account_extra')->where('ymonth', $ymonth)->where('store_cd', $store_cd);
                if ($originals->count() > 0) {
                    $del_idx = $originals->first()->idx;
                    $originals->delete();
                    DB::table('store_account_extra_list')->where('ext_idx', $del_idx)->delete();
                }

                // 등록
                $ext_idx = DB::table('store_account_extra')->insertGetId([
                    'ymonth' => $ymonth,
                    'store_cd' => $store_cd,
                    'extra_amt' => $total_amt,
                    'admin_id' => $admin_id,
                    'rt' => now(),
                ]);
                $extra_list = array_map(function($e) use ($ext_idx) { return array_merge($e, ['ext_idx' => $ext_idx]); }, $extra_list);
                DB::table('store_account_extra_list')->insert($extra_list);
            }

            DB::commit();
			$msg = "기타재반자료가 정상적으로 저장되었습니다.";
		} catch(Exception $e) {
			DB::rollback();
			$code = "500";
			$msg = $e->getmessage();
		}

		return response()->json(["code" => $code, "msg" => $msg]);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// 아래 작업중 ////////////////////////////////////////////

    /** 기타재반자료 일괄등록 팝업오픈 */
    public function show_batch()
    {
        $sdate = Carbon::now()->startOfMonth()->subMonth()->format("Y-m"); // 저번 달 기준
        $extra_cols = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2'); // code_val2를 상위 카테고리로 사용

        $values = [
            'sdate' => $sdate,
            'extra_cols' => $extra_cols,
        ];

        return view( Config::get('shop.store.view') . '/account/acc05_batch', $values );
    }

    /** 일괄등록 시 Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환 */
	public function import_excel(Request $request) {
		if (count($_FILES) > 0) {
			if ( 0 < $_FILES['file']['error'] ) {
				return response()->json(['code' => 0, 'message' => 'Error: ' . $_FILES['file']['error']], 200);
			}
			else {
				$file = $request->file('file');
				$now = date('YmdHis');
				$user_id = Auth::guard('head')->user()->id;
				$extension = $file->extension();
	
				$save_path = "data/store/acc05/";
				$file_name = "${now}_${user_id}.${extension}";
				
				if (!Storage::disk('public')->exists($save_path)) {
					Storage::disk('public')->makeDirectory($save_path);
				}
	
				$file = sprintf("${save_path}%s", $file_name);
				move_uploaded_file($_FILES['file']['tmp_name'], $file);
	
				return response()->json(['code' => 1, 'file' => $file], 200);
			}
		}
	}
}
