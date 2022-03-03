<?php

namespace App\Http\Controllers\head\promotion;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class prm04Controller extends Controller
{
    public function index() {
        $mutable = Carbon::now();
        $sdate	= $mutable->sub(6, 'month')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d"),
        ];

        return view(Config::get('shop.head.view') . '/promotion/prm04', $values);
    }

    public function search(Request $request) {
        $sdate = $request->input('sdate',Carbon::now()->sub(3, 'month')->format('Ymd'));
        $edate = $request->input('edate',date("Ymd"));
        $subject = $request->input('subject');
        $content = $request->input('content');

        $where = "";
        if ($subject != "") $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
		if ($content != "") $where .= " and a.content like '%" . Lib::quote($content) . "%' ";

        $query = "
			select a.subject, a.name, date_format(a.regi_date,'%Y.%m.%d') as rt, cnt, a.idx
			from brd a
			where a.type = 1 and a.regi_date >= :sdate and a.regi_date < date_add(:edate, interval 1 day) $where
			order by a.idx desc
		";
        
        $result = DB::select($query, ['sdate' => $sdate, 'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ]);
    }

    public function show($idx) {
        $values = DB::table('brd')->where('idx',"=", $idx)->get();
        if(count($values) > 0) $values = $values[0];
        return view(Config::get('shop.head.view') . '/promotion/prm04_show', ['brd' => $values]);
    }
    
    public function create() {
        $curYear = (int)date('Y');  
        $curMonth = (int)date('m');  
        $sdate = date("Y년 m월 d일", mktime(0, 0, 0, $curMonth-1 , 1, $curYear));
        $edate = date("Y년 m월 d일", mktime(0, 0, 0, $curMonth , 0, $curYear));
        $values = (object)array(
            'type' => 'create',
            'name' => Auth('head')->user()->name,
            'email' => Auth('head')->user()->email,
            'subject' => $sdate . ' ~ ' . $edate . ' 입금 미확인 고객님들 성함입니다^^',
            'content' => '
                <p>입금자명단</p>
                <p>입금확인을 원하시면, <br />고객센타 &lt;1:1친절상담&gt;게시판이나 고객센터(☎)로 <br />은행명, 입금액, 입금하신날짜, 예금주성함, 주문자성함을 <br />등을 말씀해주시면 됩니다.</p>
                <p>입금자 성함과 주문자 성함이 달라 입금확인이 늦어질 경우 <br />제품이 품절되어 환불처리가 될 수 있으니 되도록 빨리 확인하여 <br />문의 주시기 바랍니다***</p>
            ',
        );
        return view(Config::get('shop.head.view') . '/promotion/prm04_show', ['brd' => $values]);

    }

    public function add(Request $req) {
        $code = 200;
        $msg = '';

        $type       = 1;
        $subject    = $req->input('subject', '');
        $content    = $req->input('content', '');
        $id         = Auth('head')->user()->id;
        $name       = $req->input('author_nm', Auth("head")->user()->name);
        $email      = $req->input('author_email', Auth("head")->user()->email);

        $values = [
            'type'      => $type,
            'subject'   => $subject,
            'content'   => $content,
            'id'        => $id,
            'name'      => $name,
            'email'     => $email,
            'regi_date' => now(),
        ];

        try {
			DB::transaction(function() use (&$result, $values) {
				DB::table('brd')->insert($values);
			});
            $msg = '저장되었습니다.';
        } catch(Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

		return response()->json(["code" => $code, "message" => $msg], $code);
    }

    public function update(Request $req, $idx) {
        $code = 200;
        $msg = '';

        $subject    = $req->input('subject', '');
        $content    = $req->input('content', '');
        $name       = $req->input('author_nm', Auth("head")->user()->name);
        $email      = $req->input('author_email', Auth("head")->user()->email);

        $values = [
            'subject'   => $subject,
            'content'   => $content,
            'name'      => $name,
            'email'     => $email,
        ];

        try {
            DB::transaction(function() use (&$result, $idx, $values) {
                DB::table('brd')
                    ->where('idx','=',$idx)
                    ->update($values);
            });
            $msg = '저장되었습니다.';
        } catch(Exception $e) {
            $code = 500;
            $msg = $e->getMessage();
        }

		return response()->json(["code" => $code, "message" => $msg], $code);
    }
    
    public function delete($idx) {
        $code = 200;
        $msg = '';

        try {
            DB::transaction(function () use (&$result, $idx) {
                DB::table('brd')
                    ->where('idx','=',$idx)
                    ->delete();
            });
            $msg = '삭제되었습니다.';
        } catch(Exception $e){
            $code = 500;
            $msg = $e->getMessage();
        }
        
        return response()->json(["code" => $code, "message" => $msg], $code);
    }
}
