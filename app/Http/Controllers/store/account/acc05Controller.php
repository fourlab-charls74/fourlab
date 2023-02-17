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

            $extra_sql .= ", sum(if(el.type in (" . join(',', $types_str) . "), el.extra_amt, null)) as " . $group_cd . "_sum";
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
                where e.ymonth >= '$sdate' and e.ymonth <= '$sdate'
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

        ////////////////////////////////////////////////////////////////////

        // $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        // $f_sdate = Carbon::parse($sdate)->firstOfMonth()->format("Y-m-d H:i:s");
        // $f_edate = Carbon::parse($sdate)->lastOfMonth()->format("Y-m-d H:i:s");
        // $sdate = Lib::quote(str_replace('-', '', $sdate));

        // $sql = "
        //     select r.prd_cd, p.prd_nm, p.type
        //     from sproduct_stock_release r
        //         inner join store s on s.store_cd = r.store_cd and s.account_yn = 'Y'
        //         inner join product p on p.prd_cd = r.prd_cd and p.type = :type
        //     where r.fin_rt >= '$f_sdate' and r.fin_rt <= '$f_edate'
        //     group by r.prd_cd
        // ";
        // $gifts = DB::select($sql, ['type' => 'G']); // 사은품
        // $expandables = DB::select($sql, ['type' => 'S']); // 부자재(소모품)
        // $extra_types = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2')->toArray(); // 사은품/부자재 외 기타재반

        // // 검색조건 필터링
        // $where = "";
        // if ($store_type != '') $where .= " and s.store_type = " . Lib::quote($store_type);
        // if ($store_kind != '') $where .= " and s.store_kind = '". Lib::quote($store_kind) . "'";
        // if ($store_cd != '') $where .= " and s.store_cd = '" . Lib::quote($store_cd) . "'";

        // // 기타재반타입별 쿼리문 생성
        // $extra_sql = join('', array_map(function($key, $value) {
        //     $gruop_cd = str_split($value[0]->code_id ?? '')[0];
        //     $query = join('', array_map(function($v) {
        //         return ", sum(if(e.type = '" . $v->code_id . "', e.extra_amt, null)) as " . $v->code_id . "_amt";
        //     }, $value));
        //     $types = array_map(function($val) { return "'" . $val->code_id . "'"; }, $value);
        //     // E1(온라인RT), E2(온라인반송)은 소계에 포함시키지 않습니다. (because, E3(온라인) = E1 - E2)
        //     if ($gruop_cd === 'E') $types = array_map(function($val) { return "'" . $val->code_id . "'"; }, array_filter($value, function($t) { return !in_array($t->code_id, ['E1', 'E2']); }));
        //     $query .= ", sum(if(e.type in (" . join(',', $types) . "), e.extra_amt, null)) as " . $gruop_cd . "_sum";
        //     return $query;
        // }, array_keys($extra_types), $extra_types));

        // // 사은품 쿼리문 생성
        // $extra_sql .= join('', array_map(function($value) {
        //     return ", sum(if(e.type = '" . $value->type . "' and e.prd_cd = '" . $value->prd_cd . "', e.extra_amt, null)) as " . $value->type . "_" . $value->prd_cd . "_amt";
        // }, $gifts));
        // $extra_sql .= ", sum(if(e.type = 'G', e.extra_amt, null)) as G_sum";
        
        // // 부자재(소모품) 쿼리문 생성
        // $extra_sql .= join('', array_map(function($value) {
        //     return ", sum(if(e.type = '" . $value->type . "' and e.prd_cd = '" . $value->prd_cd . "', e.extra_amt, null)) as " . $value->type . "_" . $value->prd_cd . "_amt";
        // }, $expandables));
        // $extra_sql .= ", sum(if(e.type = 'S', e.extra_amt, null)) as S_sum";

        // $sql = "
        //     select s.store_cd, s.store_nm, s.store_type, c.code_val as store_type_nm, '$sdate' as ymonth, e.*
        //     from store s
        //         left outer join (
        //             select e.store_cd as store
        //                 $extra_sql
        //                 , sum(if(e.type not in ('E1', 'E2'), e.extra_amt, 0)) as total
        //             from store_account_extra e
        //             where e.ymonth = '$sdate'
        //             group by e.store_cd
        //         ) e on e.store = s.store_cd
        //         left outer join code c on c.code_kind_cd = 'STORE_TYPE' and c.code_id = s.store_type
        //     where s.account_yn = 'Y' $where
        // ";
        // $result = DB::select($sql);
    }

    public function show(Request $request)
    {
        $cmd = $request->input('date') === null ? 'add' : 'update';
        $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        $extra_cols = SLib::getCodes('STORE_ACC_EXTRA_TYPE')->groupBy('code_val2'); // code_val2를 상위 카테고리로 사용

        $values = [
            'cmd' => $cmd,
            'sdate' => $sdate,
            'extra_cols' => $extra_cols,
        ];

        return view( Config::get('shop.store.view') . '/account/acc05_show', $values );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// 아래 작업중 ////////////////////////////////////////////

	public function save(Request $request)
	{
        $save_type = $request->input('type', 'G'); // G: 일반, B: 일괄
        $sdate = $request->input('sdate', '');
		$data = $request->input('data');

        $code = "200";
		$msg = "";

		try {
			DB::beginTransaction();
			
            foreach ($data as $extra) {
                $amts = array_filter($extra, function($key) { 
                    $key_arr = (explode('_', $key));
                    return end($key_arr) === 'amt';
                }, ARRAY_FILTER_USE_KEY);

                foreach ($amts as $key => $value) {
                    $ymonth = $extra['ymonth'];
                    $store_cd = $extra['store_cd'];
                    $type = explode('_', $key)[0];
                    $prd_cd = '';

                    $rows = DB::table('store_account_extra')
                        ->where('store_cd', $store_cd)->where('ymonth', $ymonth)->where('type', $type);
                    if (in_array($type, ['S', 'G'])) {
                        $prd_cd = explode('_', $key)[1];
                        $rows = $rows->where('prd_cd', $prd_cd);
                    }
                    $rows = $rows->get();

                    if ($rows->count() < 1) {
                        // save
                        DB::table('store_account_extra')->insert([
                            'store_cd' => $store_cd,
                            'ymonth' => $ymonth,
                            'type' => $type,
                            'prd_cd' => ($prd_cd == '' ? null : $prd_cd),
                            'extra_amt' => $value,
                            'rt' => now(),
                        ]);
                    } else {
                        // update
                        $idx = $rows->first()->idx;
                        DB::table('store_account_extra')->where('idx', $idx)->update([
                            'extra_amt' => $value,
                            'ut' => now(),
                        ]);
                    }
                }
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
