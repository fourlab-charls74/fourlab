<?php

namespace App\Http\Controllers\head\api\sabangnet;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;

class delivery_xmlController extends Controller
{
    public function index()
    {

        $values = [
        ];
        return view(Config::get('shop.head.view') . '/api/sabangnet/delivery_xml', $values);
    }

    public function dlv_view(Request $request)
    {
        set_time_limit(0);

		// 설정 값 얻기
        $conf	= new Conf();

		$cfg_api_sabangnet_id	= $conf->getConfigValue("api","sabangnet_id");
		$cfg_api_sabangnet_key	= $conf->getConfigValue("api","sabangnet_key");

		$sabangnet_order_id = $request->input("order_id");

		$delivery_company = array(
			"대한통운"      	=> "001",
			"롯데택배"      	=> "002",
			"CJGLS택배"     	=> "003",
			"한진택배"      	=> "004",
			"KGB택배"       	=> "005",
			"동부택배"      	=> "006",
			"로젠 택배"      	=> "007",
			"로젠택배"      	=> "007",
			"옐로우캡택배"  	=> "008",
			"우체국택배"    	=> "009",
			"하나로택배"    	=> "010",
			"동원로엑스택배"	=> "011",
			"편의점택배"    	=> "012",
			"경동택배"      	=> "013",
			"일양로지스택배"   	=> "014",
			"업체직송"      	=> "015",
			"천일택배"      	=> "016",
			"동부익스프레스"	=> "017",
			"SC로지스"      	=> "018",
			"네덱스"        	=> "019",
			"사가와택배"    	=> "020",
			"아주택배"      	=> "021",
			"트라넷"        	=> "022",
			"이노지스택배"  	=> "023",
			"양양택배"      	=> "024",
			"에버런택배"    	=> "025",
			"한국택배"      	=> "026",
			"국제특송"      	=> "027",
			"선경물류"      	=> "028",
			"로지스월드"    	=> "034",
			"영진물류"      	=> "029",
			"부경물류"      	=> "030",
			"백산물류"      	=> "031",
			"옐로우캡"      	=> "032",
			"호남택배"      	=> "033",
			"KGB특급택배"   	=> "035",
			"오렌지택배"    	=> "036",
			"대신화물택배"  	=> "037",
			"EMS"				=> "038",
			"에버런택배"    	=> "039",
			"이클라인택배"  	=> "040",
			"편의점택배"    	=> "041",
			"KT로지스"      	=> "042",
			"건영택배"      	=> "043",
			"고려택배"      	=> "044",
			"우편발송"      	=> "045",
			"삼성택배(HTH)" 	=> "046",
			"일양택배"      	=> "047",
			"훼미리택배"    	=> "048",
			"CJ대한통운"		=> "055",
			"CJ대한통운택배"	=> "055",
			"대한통운택배"		=> "055",
			"기타"          	=> "999"
		);

		//header("Content-Type: text/plain; charset=utf-8");
		
		$data		= "";

        $data	.= "<?xml version=\"1.0\" encoding=\"EUC-KR\"?>\n";
		$data	.= "<SABANG_INV_REGI>\n";
		$data	.= "<HEADER>\n";
		$data	.= "<SEND_COMPAYNY_ID>$cfg_api_sabangnet_id</SEND_COMPAYNY_ID>\n";
		$data	.= "<SEND_AUTH_KEY><![CDATA[$cfg_api_sabangnet_key]]></SEND_AUTH_KEY>\n";
		$data	.= "<SEND_DATE><![CDATA[" . date("Ymd") . "]]></SEND_DATE>\n";
		$data	.= "</HEADER>\n";

		$sql	= "
			select
				s.sabangnet_order_id as 'SABANGNET_IDX',
				dlv_cd.code_val  as 'TAK_CODE',
				o.dlv_no as 'TAK_INVOICE',
				'' as 'DELV_HOPE_DATE'
			from shop_sabangnet_order s left outer join order_opt o on s.ord_opt_no = o.ord_opt_no
				left outer join code dlv_cd on  dlv_cd.code_kind_cd = 'DELIVERY' and o.dlv_cd = dlv_cd.code_id
			where s.sabangnet_order_id = :sabangnet_order_id
		";
		$result = DB::select($sql, ['sabangnet_order_id' => $sabangnet_order_id]);
        
		foreach($result as $row) 
		{
			// 택배사 코드
			$row->TAK_CODE	= isset($delivery_company[$row->TAK_CODE]) ? $delivery_company[$row->TAK_CODE]:"999";

			// APPLY CDATA
			$row->SABANGNET_IDX		=  sprintf("<![CDATA[%s]]>",$row->SABANGNET_IDX);
			$row->TAK_CODE			=  sprintf("<![CDATA[%s]]>",$row->TAK_CODE);
			$row->TAK_INVOICE		=  sprintf("<![CDATA[%s]]>",$row->TAK_INVOICE);
			$row->DELV_HOPE_DATE	=  sprintf("<![CDATA[%s]]>",$row->DELV_HOPE_DATE);
		}

		$data	.= $this->println("DATA",$row);

		$data	.= "</SABANG_INV_REGI>";


		return Response($data)->header('Content-type','text/plan;charset=euc-kr');
	}

	/**
	 * Function : println
	 *	XML출력
	 */
	function println($type,$data)
	{
		$buffer = "";
		foreach($data as $key => $value)
		{
			if( is_array($value) )
			{
				$buffer	.= sprintf("<%s>\n",$key);

				foreach( $value as $k => $v)
				{
					foreach( $v as $kk => $vv)
					{
						$vv	= iconv("UTF-8","CP949",$vv);
						//$buffer .= $v->SKU_VALUE;
						$buffer .= sprintf("\t<%s>%s</%s>\n",$kk,$vv,$kk);
					}
				}

				$buffer	.= sprintf("</%s>\n",$key);
			}
			else
			{
                $value	= iconv("UTF-8","CP949",$value);
				$buffer	.= sprintf("<%s>%s</%s>\n",$key,$value,$key);
			}
		}
		$buffer	= sprintf("<%s>\n%s</%s>\n",$type,$buffer,$type);

		return $buffer;
	}

}
