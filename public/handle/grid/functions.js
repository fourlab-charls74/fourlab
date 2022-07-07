/*
*
* 그리드 출력 형식 정의 - 추후 별도의 js 로 만들기
*
* */
function openProduct(prd_no){
    var url = '/partner/product/prd01/' + prd_no;
    var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}
function openHeadProduct(prd_no){
    var url = '/head/product/prd01/' + prd_no;
    var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openOrder(ord_no,ord_opt_no = ''){
    if(ord_opt_no !== ''){
        var url = '/partner/order/ord01/' + ord_no + '/' + ord_opt_no;
    } else {
        var url = '/partner/order/ord01/' + ord_no;
    }
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openHeadOrder(ord_no,ord_opt_no){
    var url = '/head/order/ord01/' + ord_no + '/' + ord_opt_no;
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openStock(goods_no,goods_opt){
    var url = '/partner/stock/stk01/' + goods_no + '/' + goods_opt;
    var stock = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openHeadStock(goods_no,goods_opt){
    var url = '/head/stock/stk01/' + goods_no + '/' + goods_opt;
    var stock = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openSmsSend(phone='', name='') {
    var url = '/head/api/sms/send?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openSmsList(phone='', name='') {
    var url = '/head/api/sms/list?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}
function openSchDetail(idx='') {
    const url = `/head/promotion/prm32/show/${idx}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800")

}
function openSchPop(kwd) {
    const url = `https://devel.netpx.co.kr/app/product/search?q=${kwd}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800")
}

/**
 *
 * @param {*} data
 *  row별 구분은 ,로 합니다.
 *  column별 구분은 |로 합니다.
 *      ex)user_id|no|ord_no,user_id|no|ord_no
 * @param {*} point_kinds
 */
function openAddPoint(data = '', point_kinds = 1) {
    var url = '/head/api/point?data=' + data + '&point_kinds=' + point_kinds;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

/**
 * 해당 메서드는 mem01화면을 팝업 형식으로 띄워줍니다.
 * 회원을 선택 후 회원선택 버튼을 눌렀을 경우
 * opener에 usersCallback 메서드가 있으면
 * 해당 메서드에 선택된 회원의 정보를 전달합니다.
 */
function openUserSelect() {
    var url = '/head/member/mem01/pop';
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openUserEdit(id='') {
    var url = '/head/member/mem01/show/edit/'+id;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

/**
 * [쿠폰지급 버튼]
 * user_ids와 coupon_nos의 구분은 ,로 해주세요
 *
 * @param {*} user_ids
 *   ex)test1,test2,test3
 *
 * @param {*} coupon_nos
 *   ex)1635,1634,1633,1632
 */
function openCoupon(user_ids='', coupon_nos='') {
    const url=`/head/promotion/prm10/gift?user_ids=${user_ids}&coupon_nos=${coupon_nos}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}

/**
 * 해당 메서드는 prm10화면을 팝업 형식으로 띄워줍니다.
 * 쿠폰을 선택 후 쿠폰선택 버튼을 눌렀을 경우
 * opener에 couponSelectedCallback 메서드가 있으면
 * 해당 메서드에 선택된 쿠폰의 정보를 전달합니다.
 */
function openCouponSelect() {
    const url=`/head/promotion/prm10/pop`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}

function openCouponDetail(type='add',no='') {
    var url = `/head/promotion/prm10/show/${type}/${no}`;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openStore(store_cd){
    const url='/store/standard/std02/show/' + store_cd;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}


function sortnumber(n1,n2){
    if (n1 === null && n2 === null) {
        return 0;
    }
    if (n1 === null) {
        return -1;
    }
    if (n2 === null) {
        return 1;
    }
    return n1 - n2;
}

function StyleGoodsType(params){
    var state = {
        "P":"#F90000",
        "S":"#009999",
        "O":"#0000FF",
    }
    if (params.value !== undefined) {
        if (state[params.data.goods_type]) {
            var color = state[params.data.goods_type];
            return {
                color: color
            }
        }
    }
}

function StyleGoodsTypeNM(params){
    var state = {
        "위탁판매":"#F90000",
        "매입":"#009999",
        "해외":"#0000FF",
    }
    if (params.value !== undefined) {
        if (state[params.value]) {
            return {
                color :state[params.value],
                "text-align": "center",
                "line-height": "40px"
            }
        }
    }
}

function StyleGoodsState(params){
	var state = {
		"판매중지":"#808080",
		"등록대기중":"#669900",
		"판매중":"#0000ff",
		"품절[수동]":"#ff0000",
		"품절":"#AAAAAA",
		"휴지통":"#AAAAAA",
        "임시저장":"#000000"
	}
	if (params.value !== undefined) {
		if (state[params.value]) {
			var color = state[params.value];
			return {
				color: color,
				'text-align': 'center'
			}
		}
	}
}

function StyleGoodsStateLH50(params){
	var state = {
		"판매중지":"#808080",
		"등록대기중":"#669900",
        판매대기중: "#000000",
		"판매중":"#0000ff",
		"품절[수동]":"#ff0000",
		"품절":"#AAAAAA",
		"휴지통":"#AAAAAA",
        "임시저장":"#000000"
	}
	if (params.value !== undefined) {
		if (state[params.value]) {
			var color = state[params.value];
			return {
				color: color,
				'text-align': 'center',
				'line-height': '40px'
			}
		}
	}
}

function StyleOrdKind(params){
    var state = {
        "정상":"#0000ff",
        "출고가능":"#0000ff",
        "출고보류":"#669900",
        "출고보류(예약)":"#669900"
    }
    if(state[params.value]){
        var color = state[params.value];
        return {
            color:color
        }
    }
}

function StyleOrdState(params) {
    var state = {
        "입금예정":"#669900",
        "입금완료":"#ff0000",
        "출고요청":"#0000ff",
        "출고처리중":"#0000ff",
        "출고완료":"#0000ff",
        "주문취소":"#0000ff",
        "결제오류":"#ff0000",
        "구매확정":"#0000ff"
    }

    var color = state[params.value];
    return {
        color: color,
    };
}

function StyleClmState(params){
    if(params.value != ""){
        return {
            'color':'#FF0000',
            'font-weight':'bold'
        }
    }
}

var _styleOrdNoCnt = 0;
var _styleColorIndex = -1;
function StyleOrdNo(params){
    if(params.value !== undefined){
        var colors = {
            0:"#ffff00",
            1:"#C5FF9D",
        }
        var rowIndex = params.node.rowIndex;
        if(rowIndex > 0 && params.data.ord_no_bg_color === undefined){
            var rowNode = params.api.getDisplayedRowAtIndex(rowIndex-1);
            if( params.value == rowNode.data.ord_no){
                _styleColorIndex = _styleOrdNoCnt % 2;
                params.data['ord_no_bg_color'] = colors[_styleColorIndex];
                rowNode.data['ord_no_bg_color'] = colors[_styleColorIndex];
                //gridOptions.api.redrawRows({rowNodes:[rowNode]});
            } else {
                if(_styleColorIndex >= 0){
                    _styleOrdNoCnt++;
                    _styleColorIndex = -1;
                }
            }
        }
        if(params.data.ord_no_bg_color !== undefined || params.data.ord_no_bg_color != '') {
            return {
                'background-color': params.data.ord_no_bg_color
            }
        }
    }
}

const StyleEditCell = {
    'background' : '#ffff99',
    'border-right' : '1px solid #e0e7e7'
};

function unComma(txt) {
    if (txt && txt.replace) return txt.replace(/,/gi, '') * 1

    return 0;
};

function com(obj) {
    obj.value = numberFormat(unComma(obj.value));
};

//타입이 숫자고 ,로 자리수 표현을 하지 않을 경우
function onlynum(obj) {
    let val = obj.value;

    if (isNaN( val * 1 )) {
        obj.value = 0;
        return;
    };
};

//타입이 숫자고 ,로 자리수 표현을 할경우
function currency(obj) {
    let val = unComma(obj.value);

    if (isNaN( val )) {
        obj.value = 0;
        return;
    };

    com(obj);
};

//멀티 셀렉트 박스2
$(document).ready(function() {
    $('.multi_select').select2({
        placeholder : "전체",
        width : "100%",
        closeOnSelect: false
    });
});
