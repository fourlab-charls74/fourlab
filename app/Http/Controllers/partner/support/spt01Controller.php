<?php

namespace App\Http\Controllers\partner\support;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class spt01Controller extends Controller
{
    //
    public function index() {

        $mutable = Carbon::now();
        $sdate	= $mutable->sub(1, 'year')->format('Y-m-d');

        $values = [
            'sdate' => $sdate,
            'edate' => date("Y-m-d")
        ];
        return view( Config::get('shop.partner.view') . '/support/spt01',$values);
    }

    public function search(Request $request){

        $user = Auth::guard('partner')->user();
        $com_id = $user->com_id;

        $sdate = $request->input('sdate', Carbon::now()->sub(1, 'year')->format('Y-m-d'));
        $edate = $request->input('edate', date("Y-m-d"));
        $sdate = $sdate . " 00:00:00";
        $edate = $edate . " 23:59:59";

        $subject = $request->input('subject');
        $content = $request->input('content');

        $where = "";
        $where .= 
            "AND ( com_choice = '1'
                OR a.nc_no IN ( SELECT nc_no FROM notice_company_coms WHERE nc_no = a.nc_no AND com_id = '${com_id}' ))
            ";
        if ($subject != "") $where .= " and a.subject like '%" . Lib::quote($subject) . "%' ";
        if ($content != "") $where .= " and a.content like '%" . Lib::quote($content) . "%' ";

        $query = /** @lang text */
            "SELECT
				cd.code_val AS com_choice,a.subject, a.cnt, a.admin_nm AS 'name', a.regi_date, a.nc_no AS idx
			FROM notice_company a
				INNER JOIN code cd ON (a.com_choice = cd.code_id AND cd.code_kind_cd = 'G_COM_CHOICE')			
			WHERE a.regi_date >= :sdate AND a.regi_date <= :edate $where
			ORDER BY a.nc_no DESC
		";

        $result = DB::select($query, ['sdate' => $sdate,'edate' => $edate]);

        return response()->json([
            "code" => 200,
            "head" => array(
                "total" => count($result),
            ),
            "body" => $result
        ]);
    }

    public function show($no) {
        DB::table('notice_company')->increment('cnt');
        $user = DB::table('notice_company')->where('nc_no',"=",$no)->first();
        return view( Config::get('shop.partner.view') . '/support/spt01_show',
            ['no' => $no, 'user' => $user]
        );
    }
}
