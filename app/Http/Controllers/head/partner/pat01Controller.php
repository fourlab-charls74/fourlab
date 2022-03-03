<?php

namespace App\Http\Controllers\head\partner;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class pat01Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(3, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view( Config::get('shop.head.view') . '/partner/pat01',$values);
    }

    public function search(Request $request){

        $sdate = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $subject = $request->input('subject');
		$content = $request->input('content');

        $where = "";
        if ($subject != "") $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
		if ($content != "") $where .= " and a.content like '%" . Lib::quote($content) . "%' ";

        $query = /** @lang text */
            "
			select
				subject,  a.cnt,
				a.admin_nm as name, a.regi_date, '' as ut, a.nc_no as idx
			from notice_company a
			where a.regi_date >= :sdate and a.regi_date < date_add(:edate,interval 1 day) $where
			order by a.nc_no desc
		";
        $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function create(){

        $name =  Auth('head')->user()->name;

        $user = new \stdClass();
        $user->name = $name;
        $user->subject = '';
        $user->content = '';

        return view( Config::get('shop.head.view') . '/partner/pat01_show',
            ['no' => '','user' => $user]
        );
    }

    public function show($no){
        $user = DB::table('notice_company')->where('nc_no',"=",$no)->first();
        $user->name = $user->admin_nm;
        return view( Config::get('shop.head.view') . '/partner/pat01_show',
            ['no' => $no, 'user' => $user]
        );
    }

    public function store(Request $request){

        $id =  Auth('head')->user()->id;

        $com_choice = $request->input('com_choice',1);
        $subject = $request->input('subject');
        $content = $request->input('content');
        $name = $request->input('name');

		$notice_company = [
			'subject' => $subject,
			'content' => $content,
			'com_choice' => $com_choice,
			'admin_id' => $id,
			'admin_nm' => $name,
			'cnt' => 0,
			'regi_date' => DB::raw('now()')
		];

		try {
			DB::transaction(function () use (&$result,$notice_company) {
				DB::table('notice_company')->insert($notice_company);
			});
			$code = 200;
			$msg = "";
		} catch(Exception $e){
			$code = 500;
			$msg = $e->getMessage();
		}

		return response()->json([
			"code" => $code,
            "msg" => $msg
		]);

    }

    public function update($no, Request $request){

        $id =  Auth('head')->user()->id;

        $com_choice = $request->input('com_choice',1);
        $subject = $request->input('subject');
        $content = $request->input('content');
        $name = $request->input('name');

        $notice_company = [
            'subject' => $subject,
            'content' => $content,
            'com_choice' => $com_choice,
            'admin_id' => $id,
            'admin_nm' => $name
        ];

        try {
            DB::transaction(function () use (&$result,$no,$notice_company) {
                DB::table('notice_company')
                    ->where('nc_no','=',$no)
                    ->update($notice_company);
            });
            $code = 200;
            $msg = "";
        } catch(Exception $e){
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

    public function destroy($no){

        try {
            DB::transaction(function () use (&$result,$no) {
                DB::table('notice_company')
                    ->where('nc_no','=',$no)
                    ->delete();
            });
            $code = 200;
            $msg = "";
        } catch(Exception $e){
            $code = 500;
            $msg = $e->getMessage();
        }

        return response()->json([
            "code" => $code,
            "msg" => $msg
        ]);
    }

}
