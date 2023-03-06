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

        $values = [
            'sdate' => $sdate,
            'extra_cols' => $this->_get_account_extra_types(),
        ];

        return view( Config::get('shop.store.view') . '/account/acc05', $values );
    }

    public function search(Request $request)
    {
        $sdate = $request->input('sdate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        $sdate = Lib::quote(str_replace('-', '', $sdate));
        $edate = $request->input('edate', Carbon::now()->startOfMonth()->subMonth()->format("Ym"));
        $edate = Lib::quote(str_replace('-', '', $edate));
        $extra_types = $this->_get_account_extra_types()->toArray();

        $extra_sql = "";
        $sum_extra_sql = "";
        
        // 기타재반타입별 쿼리문 생성
        foreach ($extra_types as $key => $types) {
            if ($key !== '') {
                foreach ($types as $type) {
                    $extra_sql .= ", sum(if(el.type = '" . $type->type_cd . "', el.extra_amt, null)) as " . $type->type_cd . "_amt";
                    $sum_extra_sql .= ", sum(" . $type->type_cd . "_amt) as " . $type->type_cd . "_amt";
                }
    
                $types_arr = array_filter($types, function($t) { return $t->total_include_yn === 'Y'; });
                $types_str = array_map(function($t) { return "'" . $t->type_cd . "'"; }, $types_arr);
    
                $extra_sql .= ", sum(if(el.type in (" . join(',', $types_str) . "), if(el.type in (select type_cd from store_account_extra_type where except_vat_yn = 'Y'), round(el.extra_amt / 1.1), el.extra_amt), 0)) as " . $key . "_sum";
                $sum_extra_sql .= ", sum(" . $key . "_sum) as " . $key . "_sum";
            }
        }

        $sql = "
            select a.ymonth as ymonth
                , sum(G_sum) as G_sum
                , sum(E_sum) as E_sum
                , sum(S_total) as S_total
                , sum(C_total) as C_total
                $sum_extra_sql
            from (
                select e.ymonth
                    , sum(if(el.type = 'G', el.extra_amt, null)) as G_sum
                    , sum(if(el.type = 'E', el.extra_amt, null)) as E_sum
                    , sum(if(el.type in (select type_cd from store_account_extra_type where payer = 'S')
                        , if(el.type in (select type_cd from store_account_extra_type where except_vat_yn = 'Y'), round(el.extra_amt / 1.1), el.extra_amt)
                        , null
                    )) as S_total
                    , sum(if(el.type in (select type_cd from store_account_extra_type where payer = 'C')
                        , if(el.type in (select type_cd from store_account_extra_type where except_vat_yn = 'Y'), round(el.extra_amt / 1.1), el.extra_amt)
                        , null
                    )) as C_total
                    $extra_sql
                from store_account_extra e
                    inner join store_account_extra_list el on el.ext_idx = e.idx
                where e.ymonth >= '$sdate' and e.ymonth <= '$edate'
                group by e.ymonth, e.store_cd
            ) a
            group by a.ymonth
            order by a.ymonth desc
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

        $extra_cols = $this->_get_account_extra_types();
        $exclude_total_type = array_reduce($extra_cols->toArray(), function($a, $c) {
            $type = array_filter($c, function($tt) { return $tt->total_include_yn === 'N'; });
            if (count($type) > 0) return array_merge($a, array_map(function($tt) { return $tt->type_cd; }, $type)); 
            else return $a;
        }, []);
        $except_vat_type = array_reduce($extra_cols->toArray(), function($a, $c) {
            $type = array_filter($c, function($tt) { return $tt->except_vat_yn === 'Y'; });
            if (count($type) > 0) return array_merge($a, array_map(function($tt) { return $tt->type_cd; }, $type)); 
            else return $a;
        }, []);

        $payer_type = fn ($cd) => array_reduce($extra_cols->toArray(), function($a, $c) use ($cd) {
            $type = array_filter($c, function($tt) use ($cd) { return $tt->payer === $cd; });
            if (count($type) > 0) {
                if ($type[0]->entry_cd === null) {
                    return array_merge($a, array_map(function($tt) { return $tt->type_cd; }, $type)); 
                }
                return array_merge($a, [$type[0]->entry_cd]); 
            }
            return $a;
        }, []);

        $values = [
            'cmd' => $cmd,
            'sdate' => $sdate,
            'store' => $store,
            'sdate_str' => Carbon::parse($sdate)->format("Y년 m월"),
            'extra_cols' => $this->_get_account_extra_types(),
            'extra_etc' => (object) [
                'exclude_total' => join(',', $exclude_total_type),
                'except_vat' => join(',', $except_vat_type),
                'pay_for_s' => join(',', $payer_type('S')),
                'pay_for_c' => join(',', $payer_type('C')),
            ],
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

        $gifts = [];
        $expandables = [];

        $sql = "
            select el.type, el.prd_cd, el.prd_nm
            from store_account_extra e
                left outer join store_account_extra_list el on el.ext_idx = e.idx and el.type in ('G', 'E')
            where e.ymonth = :sdate
            group by el.type, el.prd_cd, el.prd_nm
        ";
        $rows = DB::select($sql, ['sdate' => $sdate]);
        
        if (count($rows) > 0) {
            // 이전에 등록한 자료에 원부자재정보가 포함되어 있을 경우
            $gifts = array_reduce($rows, function($a, $c) { 
                if ($c->type === 'G') return array_merge($a, [$c]); 
                else return $a;
            }, []); // 사은품
            $expandables = array_reduce($rows, function($a, $c) { 
                if ($c->type === 'E') return array_merge($a, [$c]); 
                else return $a;
            }, []); // 소모품
        } else {
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
        }

        $extra_types = $this->_get_account_extra_types()->toArray(); // 기타재반

        // 기타재반 항목별 쿼리문 생성
        $extra_sql = "";
        foreach ($extra_types as $key => $types) {
            if ($key !== '') {
                foreach ($types as $type) {
                    $extra_sql .= ", sum(if(el.type = '" . $type->type_cd . "', el.extra_amt, null)) as " . $type->type_cd . "_amt";
                }
    
                $types_arr = array_filter($types, function($t) { return $t->total_include_yn === 'Y'; });
                $types_str = array_map(function($t) { return "'" . $t->type_cd . "'"; }, $types_arr);
    
                $extra_sql .= ", sum(if(el.type in (" . join(',', $types_str) . "), if(el.type in (select type_cd from store_account_extra_type where except_vat_yn = 'Y'), round(el.extra_amt / 1.1), el.extra_amt), null)) as " . $key . "_sum";
            }
        }

        // 사은품 쿼리문 생성
        $extra_sql .= join('', array_map(function($value) {
            return ", sum(if(el.type = 'G' and el.prd_cd = '" . $value->prd_cd . "', el.extra_amt, null)) as G_" . $value->prd_cd . "_amt";
        }, $gifts));
        $extra_sql .= ", sum(if(el.type = 'G', el.extra_amt, null)) as G_sum";
        
        // 소모품 쿼리문 생성
        $extra_sql .= join('', array_map(function($value) {
            return ", sum(if(el.type = 'E' and el.prd_cd = '" . $value->prd_cd . "', el.extra_amt, null)) as E_" . $value->prd_cd . "_amt";
        }, $expandables));
        $extra_sql .= ", sum(if(el.type = 'E', el.extra_amt, null)) as E_sum";

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
                        , sum(if(el.type in (select type_cd from store_account_extra_type where payer = 'S')
                            , if(el.type in (select type_cd from store_account_extra_type where except_vat_yn = 'Y'), round(el.extra_amt / 1.1), el.extra_amt)
                            , null
                        )) as S_total
                        , sum(if(el.type in (select type_cd from store_account_extra_type where payer = 'C')
                            , if(el.type in (select type_cd from store_account_extra_type where except_vat_yn = 'Y'), round(el.extra_amt / 1.1), el.extra_amt)
                            , null
                        )) as C_total
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

    private function _get_account_extra_types()
    {
        $sql = "
            select t.type_cd, t.type_nm, t.entry_cd, tt.type_nm as entry_nm, t.payer
                , t.except_vat_yn, t.total_include_yn, t.has_child_yn, t.use_yn, t.seq, t.rt
            from store_account_extra_type t
                left outer join store_account_extra_type tt on tt.type_cd = t.entry_cd
            where t.use_yn = 'Y' and t.has_child_yn = 'N'
            order by t.payer is null desc, t.payer desc, t.entry_cd is null asc, t.seq
        ";
        return collect(DB::select($sql))->groupBy('entry_cd');
    }

    public function save(Request $request)
	{
        $cmd = $request->input('cmd', 'add');
        $file_type = $request->input('type', 'G'); // G: 일반, S: 원부자재포함
		$data = $request->input('data', []);
		$cols = $request->input('cols', []);
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
                    $prd_nm = null;
                    if (in_array($type, ['G', 'E'])) {
                        $prd_nm = $cols[$key] ?? '';
                        $prd_cd = explode('_', $key)[1];
                    }

                    array_push($extra_list, [
                        'type' => $type,
                        'prd_cd' => $prd_cd,
                        'prd_nm' => $prd_nm,
                        'extra_amt' => $value ?? 0,
                    ]);
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

    /** 일괄등록 시 Excel 파일 저장 후 ag-grid(front)에 사용할 응답을 JSON으로 반환 */
	public function import_excel(Request $request) 
    {
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
