<?php

namespace App\Http\Controllers\head\api\sabangnet;

use App\Http\Controllers\Controller;
use App\Components\Lib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Conf;

class order_xmlController extends Controller
{
    public function index()
    {

        $values = [
        ];
        return view(Config::get('shop.head.view') . '/api/sabangnet/order_xml', $values);
    }

    public function get_order(Request $request)
    {
        set_time_limit(0);

		// 설정 값 얻기
        $conf	= new Conf();

		$cfg_api_sabangnet_id	= $conf->getConfigValue("api","sabangnet_id");
		$cfg_api_sabangnet_key	= $conf->getConfigValue("api","sabangnet_key");

		$ord_st_date = $request->input("ORD_ST_DATE");
		$ord_ed_date = $request->input("ORD_ED_DATE",0);

		$order_fields = "IDX|ORDER_ID|MALL_ID|ORDER_STATUS|USER_NAME|USER_TEL|USER_CEL|USER_EMAIL|RECEIVE_TEL|RECEIVE_CEL|DELV_MSG|RECEIVE_NAME|RECEIVE_ZIPCODE|RECEIVE_ADDR|TOTAL_COST|ORDER_DATE|MALL_PRODUCT_ID|PRODUCT_ID|P_PRODUCT_NAME|SALE_COST|WON_COST|P_SKU_VALUE|SALE_CNT|DELIVERY_METHOD_STR|DELV_COST|COMPAYNY_GOODS_CD";
		$order_fields .= "|MALL_USER_ID|USER_ID|RECEIVE_EMAIL|PAY_COST|PARTNER_ID|DPARTNER_ID|SKU_ID|PRODUCT_NAME|SKU_VALUE|MALL_WON_COST|SKU_ALIAS|BOX_EA|JUNG_CHK_YN";

		//header("Content-Type: text/plain; charset=utf-8");

		$data		= "";

		$data	.= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$data	.= "<SABANG_ORDER_LIST>\n";
		$data	.= "<HEADER>\n";
		$data	.= "<SEND_COMPAYNY_ID>$cfg_api_sabangnet_id</SEND_COMPAYNY_ID>\n";
		$data	.= "<SEND_AUTH_KEY><![CDATA[$cfg_api_sabangnet_key]]></SEND_AUTH_KEY>\n";
		$data	.= "<SEND_DATE><![CDATA[" . date("Ymd") . "]]></SEND_DATE>\n";
		$data	.= "</HEADER>\n";
		$data	.= "<DATA>\n";
		$data	.= "<ORD_ST_DATE>$ord_st_date</ORD_ST_DATE>\n";
		$data	.= "<ORD_ED_DATE>$ord_ed_date</ORD_ED_DATE>\n";
		$data	.= "<ORD_FIELD><![CDATA[$order_fields]]></ORD_FIELD>\n";
		$data	.= "</DATA>\n";
		$data	.= "</SABANG_ORDER_LIST>\n";


		return Response($data)->header('Content-type','text/plan;charset=euc-kr');
	}

}
