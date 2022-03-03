<?php

namespace App\Http\Controllers\partner\api;

use App\Components\SLib;
use App\Components\Lib;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDO;

class claim extends Controller
{


    function store(request $request){

        $com_id = Auth('partner')->user()->com_id;

        $ord_no = $request->input("ord_no");
        $ord_opt_no = $request->input("ord_opt_no");
        $req_claim_gubun = $request->input("ord_no");
        $msg = $request->input("msg");
        $ord_state = $request->input("ord_state");
        $clm_no;

		$C_ADMIN_ID = $this->user["id"];
		$C_ADMIN_NAME = $this->user["name"];

		if(empty($clm_status)){
			$clm_status = $ord_sate;
		}

		if(empty($clm_no)){
			$query = "
				select ifnull(max(clm_no),'') as clm_no
				from claim
				where ord_opt_no = '$ord_opt_no'
            ";

            $result = DB::select($query);

			$clm_no = $result[0]->clm_no;
		}

		$param = array(
			"ord_state"=>$ord_state
			,"clm_state"=>$clm_state
			,"cs_form"=>$cs_form
			,"memo"=>$msg
		);

		$claim = new Claim( $conn, $this->user );
		$claim->SetOrdOptNo( $ORD_OPT_NO );
		$claim->SetClmNo( $CLM_NO );
		$memo_no = $claim->InsertMessage( $param );

		/*****************************************************************************************************/
		// 입점 업체 및 기타 방법으로 클레임 작성된 경우, 향후 고객의 직접 요청하는 클레임 케이스 포함
		/*****************************************************************************************************/
		$sql = "
			insert into claim_memo_client (
				ord_no, ord_opt_no, memo_no, req_claim_gubun, req_client_gubun, id, name, regi_date
			) values (
				'$ORD_NO', '$ORD_OPT_NO', '$memo_no', '$REQ_CLAIM_GUBUN', 'partner', '$C_ADMIN_ID', '$C_ADMIN_NAME', now()
			)
		";
		//DebugSQL($sql);exit;
		$conn->Execute($sql);

		$writer = new GridWriter("xml");
		$writer->printType();
		$writer->printStart();

		// 마지막 저장된 메모 얻기
		$sql = "
			select
				cd.code_val as cs_form, cd2.code_val as ord_state
				, if(cd3.code_id is not null,cd3.code_val,cd2.code_val) as clm_state
				, a.memo, a.admin_nm, date_format(a.regi_date, '%y.%m.%d %H:%i:%s') as regi_date
				,'' as alt
			from claim_memo a
				left outer join code cd on cd.code_kind_cd = 'CS_FORM2' and cd.code_id = a.cs_form
				left outer join code cd2 on cd2.code_kind_cd = 'G_ORD_STATE' and cd2.code_id = a.ord_state
				left outer join code cd3 on cd3.code_kind_cd = 'G_CLM_STATE' and cd3.code_id = a.clm_state
			where ord_opt_no = '$ORD_OPT_NO' and memo_no = '$memo_no'
		";
		$rs = &$conn->Execute($sql);
		$cnt = 0;
		while(!$rs->EOF){
			$row = $rs->fields;
			$alt = ($cnt % 2 == 1 ) ? "alt":"";
			$row["alt"] = $alt;
			$writer->printDataXML($row);
			$rs->MoveNext();
			$cnt++;
		}
        $writer->printEnd();








        $query = "
                    select a.d_cat_cd, a.full_nm,
                        case
                            when length(a.d_cat_cd) = 3 then 1
                            when length(a.d_cat_cd) = 6 then 2
                            when length(a.d_cat_cd) = 9 then 3
                            when length(a.d_cat_cd) = 12 then 4
                        end as level,
                        ( select if(count(*) >0,length(a.d_cat_cd)+1,length(a.d_cat_cd))
                            from category where cat_type = '$cat_type' and p_d_cat_cd = a.d_cat_cd limit 0,1 ) as mx_len
                    from p_partner_category	p inner join category a on p.cat_cd = a.d_cat_cd and a.cat_type = '$cat_type'
                    where p.com_id = '$com_id'
                        and p.cat_type = '$cat_type'
                    order by a.d_cat_cd
                ";

        $result = DB::select($query);
        //echo "<pre>$sql</pre>";exit;


        echo json_encode(array(
            "code" => 200,
            "head" => array(
                "total" => count($result)
            ),
            "body" => $result
        ));








        }

}





