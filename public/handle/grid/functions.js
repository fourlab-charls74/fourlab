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

function openStoreProduct(prd_no){
    var url = '/store/product/prd01/' + prd_no;
    var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openOrder(ord_no,ord_opt_no = ''){
    if(ord_opt_no !== ''){
        var url = '/headorder/ord01/' + ord_no + '/' + ord_opt_no;
    } else {
        var url = '/head/order/ord01/' + ord_no;
    }
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}
function openOrder2(ord_no,ord_opt_no = ''){
    if(ord_opt_no !== ''){
        var url = '/shop/order/ord01/order/' + ord_no + '/' + ord_opt_no;
    } else {
        var url = '/shop/order/ord01/order/' + ord_no;
    }
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openHeadOrder(ord_no,ord_opt_no){
    var url = '/head/order/ord01/' + ord_no + '/' + ord_opt_no;
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openShopOrder(ord_no,ord_opt_no){
    var url = '/shop/order/ord01/' + ord_no + '/' + ord_opt_no;
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

function openShopSmsSend(phone='', name='') {
    var url = '/shop/api/sms/send?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openStoreSmsSend(phone='', name='') {
    var url = '/store/api/sms/send?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openMultiSmsSend(ids = '') {
    var url = '/head/api/sms/send?ids=' + ids;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openStoreMultiSmsSend(ids = '') {
    var url = '/store/api/sms/send?ids=' + ids;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openSmsList(phone='', name='') {
    var url = '/head/api/sms/list?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openShopSmsList(phone='', name='') {
    var url = '/shop/api/sms/list?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

function openStoreSmsList(phone='', name='') {
    var url = '/store/api/sms/list?phone=' + phone + '&name=' + name;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}


function openSchDetail(idx='') {
    const url = `/head/promotion/prm32/show/${idx}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800")

}
function openSchPop(kwd) {
    const url = `https://bizest.fjallraven.co.kr/app/product/search?q=${kwd}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800")
}

function openStoreStock(prd_cd, date = ''){
    var url = '/store/stock/stk01/' + prd_cd + '?date=' + date;
    var stock = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=768");
}

function openShopStock(prd_cd, date = ''){
    var url = '/shop/stock/stk01/' + prd_cd + '?date=' + date;
    var stock = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=768");
}

function openStoreOrder(ord_no,ord_opt_no){
    var url = '/store/order/ord01/order/' + ord_no + '/' + ord_opt_no;
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

function openShopStock(prd_cd, date = '') {
    var url = '/shop/stock/stk01/' + prd_cd + '?date=' + date;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
}
function openShopOrder(ord_no,ord_opt_no){
    var url = '/shop/order/ord01/order/' + ord_no + '/' + ord_opt_no;
    var order = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
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

function openUserEditShop(id='') {
    var url = '/shop/member/mem01/show/edit/'+id;
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

function openStoreShop(store_cd){
    const url='/shop/standard/std02/show/' + store_cd;
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
                color: color,
                "text-align": "center"
            }
        }
    }
}

function StyleGoodsTypeNM(params){
    var state = {
        "위탁판매":"#F90000",
        "위탁":"#F90000",
        "매입":"#009999",
        "해외":"#0000FF",
    }
    if (params.value !== undefined) {
        if (state[params.value]) {
            return {
                color :state[params.value],
                "text-align": "center",
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
        "임시저장":"#000000",
        "판매대기중":"#000000"
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
        "판매대기중": "#000000",
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
            color: color,
            "text-align": "center",
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
        "구매확정":"#0000ff",
		"환불완료":"#ff0000",
		"교환완료":"#ff0000",
    }

    var color = state[params.value];
    return {
        color: color,
        "text-align": "center",
    };
}

function StylePayState(params) {
	const state = {
		"예정": "#ff2222",
		"입금": "#222222",
	}

	return {
		"color": state[params.value] || "inherit",
		"text-align": "center",
	};
}

function StyleClmState(params){
    if(params.value != ""){
        return {
            'color':'#FF0000',
            'font-weight':'bold',
            "text-align": "center",
        }
    }
}

function StyleStockOrdState(params) {
    var state = {
        "입고대기":"#222222",
        "입고취소":"#999999",
        "입고처리중":"#0000ff",
        "입고완료":"#2aa876",
        "원가확정":"#e8554e",
    }

    var color = state[params.value];
    return {
        color: color,
        "text-align": "center",
        "font-weight": '700',
        "text-decoration": params.value == '입고취소' ? "line-through" : "none",
    };
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

var _styleGoodsNoCnt = 0;
var _styleColorGIndex = -1;
function StyleGoodsNo(params){
    if(params.value !== undefined){
        var colors = {
            0:"#FFFFE6",
            1:"#F0FFE6",
        }
        var rowIndex = params.node.rowIndex;
        if(rowIndex > 0 && params.data.goods_no_bg_color === undefined){
            var rowNode = params.api.getDisplayedRowAtIndex(rowIndex-1);
            if( params.value == rowNode.data.goods_no){
                _styleColorGIndex = _styleGoodsNoCnt % 2;
                params.data['goods_no_bg_color'] = colors[_styleColorGIndex];
                rowNode.data['goods_no_bg_color'] = colors[_styleColorGIndex];
                //gridOptions.api.redrawRows({rowNodes:[rowNode]});
            } else {
                if(_styleColorGIndex >= 0){
                    _styleGoodsNoCnt++;
                    _styleColorGIndex = -1;
                }
            }
        }
        if(params.data.goods_no_bg_color !== undefined || params.data.goods_no_bg_color != '') {
            return {
                'background-color': params.data.goods_no_bg_color,
                'text-align': 'center',
            }
        }
    }
}

const StyleEditCell = {
    'background' : '#ffff99',
    'border-right' : '1px solid #e0e7e7'
};

const StyleLineHeight = {
    'line-height': '30px',
    'text-align': 'center',
};

const setRowGroupExpanded = (e) => {
    e.api.selectionController.lastSelectedNode?.setExpanded(e.api.selectionController.lastSelectedNode.selected);
};

const setAllRowGroupExpanded = (expand = true) => {
    if (!gx) return;
    if (expand) gx.gridOptions.api.expandAll();
    else gx.gridOptions.api.collapseAll();
};

function unComma(txt) {
    if (txt && txt.replace) return txt.replace(/,/gi, '') * 1

    return 0;
}

function com(obj) {
    obj.value = numberFormat(unComma(obj.value));
}

//타입이 숫자고 ,로 자리수 표현을 하지 않을 경우
function onlynum(obj) {
    let val = obj.value;

    if (isNaN( val * 1 )) {
        obj.value = 0;
        return;
    }
}

//타입이 숫자고 ,로 자리수 표현을 할경우
function currency(obj) {
    let val = unComma(obj.value);

    if (isNaN( val )) {
        obj.value = 0;
        return;
    }

    com(obj);
}

/* grid selected cell delete & backspace key 클릭 시 내용 삭제 기능 관련 + 방향키 셀 이동기능 */
function getDeleteCellColumnObject() {
	return {
		suppressKeyboardEvent: params => {
			if (!params.editing) {
				let isBackspaceKey = params.event.keyCode === 8;
				let isDeleteKey = params.event.keyCode === 46;

				if(isDeleteKey || isBackspaceKey){
					params.api.getCellRanges().forEach(r => {
						let colIds = r.columns.map(col => col.colId);
						let startRowIndex = Math.min(r.startRow.rowIndex, r.endRow.rowIndex);
						let endRowIndex = Math.max(r.startRow.rowIndex, r.endRow.rowIndex);

						for(let i = startRowIndex; i <= endRowIndex; i++) {
							let rowNode = params.api.getRowNode(i);
							colIds.forEach(column => {
								rowNode.setDataValue(column, '');
							});
						}
					});

					return true;
				}
			} else {
				let key = params.event.key;
				if (params.editing) {
					if (key == 'ArrowDown') {
						if (params.api.getDisplayedRowCount() > params.node.rowIndex + 1) {
							params.api.setFocusedCell(params.node.rowIndex + 1, params.column);
						} else {
							params.api.stopEditing();
							params.api.setFocusedCell(params.node.rowIndex, params.column);
						}
					} else if (key == 'ArrowUp') {
						if (params.api.getDisplayedRowCount() > params.node.rowIndex + 1) {
							params.api.setFocusedCell(params.node.rowIndex + 1, params.column);
						} else {
							params.api.stopEditing();
							params.api.setFocusedCell(params.node.rowIndex, params.column);
						}
					} else if (key == 'ArrowLeft') {
						if (params.event.target.selectionStart < 1) {
							params.api.stopEditing();
						}
					} else if (key == 'ArrowRight') {
						if (params.event.target.selectionStart >= params.event.target.value.length) {
							params.api.stopEditing();
						}
					}
				}
			}
			return false;
		},
	}
}

/** 포커스된 셀 중 column이 1개 이하일 때는 header 제외, 2개 이상일 때는 header 포함하여 클립보드에 복사하기 */
function getCopyFocusedCellToClipboardObject(hdGridName = 'gx') {
	return {
		sendToClipboard: function (params) {
			let headers = "";
			const focused_cells = eval(hdGridName).gridOptions.api.getCellRanges();
			if (focused_cells.length < 1) return;
			if (focused_cells[0].columns.length > 1) {
				focused_cells[0].columns.forEach((cell, i) => {
					headers += `${cell.colDef.headerName}`;
					if (i < focused_cells[0].columns.length - 1) headers += "\t";
				});
				headers += "\r\n";
			}
			window.navigator.clipboard.writeText(headers + params.data);
		},
		suppressCopyRowsToClipboard: true,
	}
}

//멀티 셀렉트 박스2
$(document).ready(function() {
    $('.multi_select').select2({
        placeholder : "전체",
        width : "100%",
        closeOnSelect: false
    });
});

function filter_pid (str) {
	return str.replace('/^[A-Za-z0-9+]*$/', '');
}

function get_indiv_columns(pid, columns, callback) {
	$.ajax({
		method: 'get',
		url: `/head/com01/get?pid=${pid}`,
		success: function (data) {
			let parseData = null;
			let resData = [];

			if(data.body.indiv_columns.length > 0) {
				parseData = JSON.parse(data.body.indiv_columns);
				parseData.forEach((value) => {
					columns.forEach((col) => {
						if(value['field'] === col['field']) {
							if(value['children'].length > 0) {
								let value_children = value['children'];
								let col_children = col['children'];
								let new_children = [];

								if(value['hide'] === true) {
									resData.push(Object.assign(clone(col), {'hide': true }));
								} else {
									Object.keys(value_children).forEach((key) => {
										if(value_children[key]['hide'] === true) {
											new_children.push(Object.assign(col_children[key], {'hide': true }));
										} else {
											new_children.push(col_children[key]);
										}
									});

									col['children'] = new_children;
									resData.push(col);
								}
							} else {
								if(value['hide'] === true) {
									resData.push(Object.assign(clone(col), {'hide': true }));
								} else {
									resData.push(clone(col));
								}
							}
						}

					})
				});
			}

			if(resData.length === 0) {
				callback.call(this, columns);
			} else {
				callback.call(this, resData);
			}
		},
		error: function(request, status, error) {
			console.log("error")
		}
	});
}


function indiv_grid_save (pid, gx) {
	let column_datalist = gx.gridOptions.api.getColumnDefs();
	let new_column_datalist = [];

	column_datalist.forEach((value) => {
		let value_children = value['children'];
		let newchildren = [];

		if(value['children'] !== undefined) {
			value_children.forEach((val) => {
				newchildren.push({'field': val['field'], 'hide': val['hide']});
			});
		}

		new_column_datalist.push({'field': value['field'], 'hide': value['hide'], 'children': newchildren});
	});

	let data = {
		'pid'	 : pid,
		'indiv_columns' : JSON.stringify(new_column_datalist)
	}

	$.ajax({
		method: 'post',
		url: '/head/com01/save',
		data: data,
		success: function (data) {
			console.log(data);
			window.location.reload();
		},
		error: function(request, status, error) {
			console.log("error")
		}
	});
}

function indiv_grid_init (pid) {
	let data = {
		'pid' : pid
	}

	$.ajax({
		method: 'delete',
		url: '/head/com01/init',
		data: data,
		success: function (data) {
			console.log(data);
			window.location.reload();
		},
		error: function(request, status, error) {
			console.log("error")
		}
	});
}
