@extends('store_with.layouts.layout-nav')
@section('title','마감 - 상세')
@section('content')

<style>
    .text { box-sizing: border-box; margin-top: 1px;}
    .form-control-sm { padding: 0.25rem 0.5rem; }
    #gd {
        text-overflow: initial;
    }
</style>

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">마감</h3>
        <div class="d-inline-flex location">
            <span class="home">/</span>
            <span>/ 입점&정산 / 마감 / 마감상세내역</span>
        </div>
    </div>
    <form method="get" name="search">
        <input type="hidden" name="idx" value="{{ $idx }}"/>
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 다운로드</a>
                        @if($closed_yn !== 'Y')
                            <a href="#" onclick="setAccountClose()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>마감완료</a>
                            <a href="#" onclick="removeAll()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>삭제</a>
                        @endif
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">마감대상일자</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ $sday }}
                                        <span class="text_line">~</span>
                                        {{ $eday }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="store_nm">매장</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ $store_nm }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="closed_yn">마감여부</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ $closed_yn }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="reg_date">등록일</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ $reg_date }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="closed_date">마감일</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ $closed_date }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="admin_nm">처리자</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ $admin_nm }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 다운로드</a>
                <a href="#" onclick="setAccountClose()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>마감완료</a>
                <a href="#" onclick="removeAll()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>삭제</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                @if($closed_yn !== 'Y')
                    <div class="card-title mb-3">
                        <div class="filter_wrap">
                            <div class="fl_box">
                            </div>
                            <div class="fr_box">
                                <div class="flax_box">
                                    <a href="#" onclick="updateData();" class="btn-sm btn btn-primary">저장</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body">
            <div class="card-title">
                <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
            </div>
            <ul class="mb-0">
                <li>매출금액 = 판매금액 - 클레임금액 - 할인금액 - 쿠폰금액(업체부담) + 배송비 + 기타정산액</li>
                <li>판매수수료 = 수수료지정 : 판매가격 * 수수료율, 공급가지정 : 판매가격 - 공급가액</li>
                <li>수수료 = 판매수수료 - 할인금액</li>
                <li>정산금액 = 매출금액 - 수수료</li>
                <li>쿠폰금액(본사부담) = 판매촉진비 수수료 매출 신고</li>
                <li><font color="red">배송비 , 기타 정산액 , 수수료율, 비고</font> 외에는 수정하실 수 없습니다.</li>
                <li><strong><font color="red">배송비, 기타 정산액, 비고 수정 후 저장 버튼을 클릭하셔야 합니다.</font></strong></li>
            </ul>
        </div>
    </div>

</div>



<script type="text/javascript" charset="utf-8">

    const CELL_STYLE = {
        EDIT: { 'background': '#DEEDB6', 'color': '#FF0000', 'font-weight': 'bold' }
    };

    const URL = {
        UPDATE: '/store/account/acc07/show_update',
        REMOVE: '/store/account/acc07/show_delete',
        CLOSE: '/store/account/acc07/show_close'
    }
    
    // ag-grid set field
    var columns = [
		{field: "num",			headerName: "#", type:'NumType', pinned: 'left'},
		{field: "type",			headerName: "구분",			width:80, pinned: 'left', cellStyle: { 'text-align': 'center' }},
		{field: "state_date",	headerName: "일자",			width:80, pinned: 'left'},
		{field: "ord_no",		headerName: "주문번호",	    width:130, pinned: 'left'},
		{field: "ord_opt_no",	headerName: "일련번호",		width:90, type:'HeadOrdOptNoType', pinned: 'left'},
		{field: "multi_order",	headerName: "복수",			width:70,
			cellRenderer: function(params){
				if( params.value == "Y" ){
					return '<a href="#" onclick="return openHeadOrderOpt(\'' + params.data.ord_opt_no +'\');">'+ params.value +'</a>';
				}
			},
            cellStyle: function(params){
				return params.value === 'Y' ? {"background-color": "yellow"} : {};
			},
			pinned: 'left'
		},
		{field: "coupon_nm",	headerName: "쿠폰",			width:70, pinned: 'left'},
		{field: "goods_nm",		headerName: "상품",		width:150, type:'HeadGoodsNameType'},
		{field: "opt_nm",		headerName: "옵션",			width:70},
		{field: "style_no",		headerName: "스타일넘버",	width:110},
		{field: "opt_type",		headerName: "출고형태",		width:90},
		{field: "com_nm",		headerName: "판매처",		width:80},
		{field: "user_nm",		headerName: "주문자",		width:80},
		{field: "pay_type",		headerName: "결제방법",		width:90},
		{field: "tax_yn",		headerName: "과세",			width:70},
		{field: "qty",			headerName: "수량",			width:70, type: 'currencyType', aggregation: true},
		{field: "sale_amt",		headerName: "판매금액",		width:90, type: 'currencyType', aggregation: true},
		{field: "clm_amt",		headerName: "클레임금액",	width:110, type: 'currencyType', aggregation: true},
		{field: "dc_amt",	headerName: "할인금액",		width:90, type: 'currencyType', aggregation: true},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "coupon_com_amt",
					headerName: "(업체부담)",
					width:95,
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "dlv_amt",		headerName: "배송비",		width:80, type: 'currencyType', aggregation: true,
            editable: true,
            cellStyle: (params) => { return params.node.rowPinned === 'top' ? {} : CELL_STYLE.EDIT; }
        },
		{field: "fee_etc_amt",	headerName: "기타정산액",	width:110, type: 'currencyType', aggregation: true,
            editable: true,
            cellStyle: (params) => { return params.node.rowPinned === 'top' ? {} : CELL_STYLE.EDIT; }
        },
		{
			headerName: '매출금액',
			children: [{
					field: "sale_net_taxation_amt",
					headerName: "과세",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_taxfree_amt",
					headerName: "비과세",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_amt",
					headerName: "소계",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
			]
		},
		{field: "tax_amt",	headerName: "부가세",	type: 'currencyType',	hide:true},
		{
			headerName: '수수료',
			children: [{
					field: "fee_ratio",
					headerName: "수수료율(%)",
					width:135,
                    editable: true,
                    cellStyle: (params) => { 
                        return (
                            params.node.rowPinned === 'top' 
                                ? {"text-align":"right"} 
                                : {"text-align":"right", ...CELL_STYLE.EDIT }
                        );
                    }
				},
				{
					field: "fee",
					headerName: "판매수수료",
					width:110,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_dc_amt",
					headerName: "할인금액",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_net",
					headerName: "소계",
					width:90,
					type: 'currencyType',
					aggregation: true
				},
			]
		},
		{field: "acc_amt",		headerName: "정산금액",		width:90, type: 'currencyType', aggregation: true},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "fee_allot_amt",
					headerName: "(본사부담)",
					width:95,
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "ord_state",	headerName: "주문상태",		width:90},
		{field: "clm_state",	headerName: "클레임상태",	width:110},
		{field: "ord_date",		headerName: "주문일",		width:80},
		{field: "dlv_end_date",	headerName: "배송완료일",	width:110},
		{field: "clm_end_date",	headerName: "클레임완료일",	width:130},
		{field: "bigo",			headerName: "비고",			width:120, editable: true, 
            cellStyle: (params) => { return params.node.rowPinned === 'top' ? {} : CELL_STYLE.EDIT; }
        },
        {field: "prd_cd",		headerName: "상품코드", width: 120},
		// {field: "goods_no",		headerName: "상품코드1"},
		// {field: "goods_sub",	headerName: "상품코드2"},
		{field: "idx",		headerName: "마감일련번호"}
	];

    // logics

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            },
            getRowNodeId: (data) => data.hasOwnProperty('index') ? data.index : "0",
            onCellValueChanged: (params) => evtAfterEdit(params),
            onPinnedRowDataChanged: (params) => {
                initTopRowData(params);
			}
        };
        gx = new HDGrid(gridDiv, columns, options);
        Search();
    });
    
    const initTopRowData = () => {
        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, type: '합계' }
        ]);
    };

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top" });
        gx.Request(`/store/account/acc07/show_search`, data, -1, () => {
            // callback
        });
    };

    const evtAfterEdit = async (params) => {
        const bool = await validation(params);
        if (bool) calculate(params);
    };

    const stopEditing = () => {
        gx.gridOptions.api.stopEditing();
    };

    const startEditing = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

    const validation = (params) => {

        let row = params.data;
        const row_index = params.data.index;
        const col_key = params.column.colId;

        let n = params.newValue;
        let p = params.oldValue;

        if (col_key == 'dlv_amt') {
            n = Math.abs(n);
            if (!isNumber(n)) {	// 숫자만 입력
                stopEditing();
                alert("배송비는 숫자만 입력하실 수 있습니다.");
                startEditing(row_index, col_key)
                row.dlv_amt = p;
                return false;
            } else if (n == "" && n != 0) {
                stopEditing();
                alert("배송비를 입력해 주십시오.");
                startEditing(row_index, col_key)
                return false;
            }
        }
        if (col_key == 'fee_etc_amt') {
            n = Math.abs(n);
            if (!isNumber(n)) { // 숫자만 입력
                stopEditing();
                alert("기타정산액은 숫자만 입력하실 수 있습니다.");
                startEditing(row_index, col_key)
                row.fee_etc_amt = p;
                return false;
            } else if (n == "" && n != 0) {
                stopEditing();
                alert("기타정산액을 입력해 주십시오.");
                startEditing(row_index, col_key)
                return false;
            }
        } else if (col_key == 'tax_amt') {
            
            if (!isNumber(n)) {	// 숫자만 입력
                stopEditing();
                alert("수수료율은 숫자만 입력하실 수 있습니다.");
                startEditing(row_index, col_key);
                row.tax_amt = p;
                return false;
            }
        }

        gx.gridOptions.api.applyTransaction({ update: [row] });
        
        return true;

    };

    const calculate = (params) => {
        const row = params.data;

        var tax_yn		= row.tax_yn;
        var sale_amt	= parseInt(row.sale_amt);
        var clm_amt		= parseInt(row.clm_amt);
        var dc_amt		= parseInt(row.dc_amt);
        var coupon_amt	= parseInt(row.coupon_com_amt);
        var dlv_amt		= parseInt(row.dlv_amt);
        var etc_amt		= parseInt(row.fee_etc_amt);

        // 매출금액
        var sale_net_amt	= sale_amt - Math.abs(clm_amt) - dc_amt - coupon_amt + dlv_amt  + etc_amt;

        // 매출금액
        if (tax_yn == 'Y') {
            // 비과세
            var sale_net_taxation_amt	= sale_net_amt;
		    var sale_net_taxfree_amt	= 0;
        } else { 
            // 과세
            var sale_net_taxation_amt	= 0;
		    var sale_net_taxfree_amt	= sale_net_amt;
        }

        var sale_net_amt	= sale_net_taxation_amt + sale_net_taxfree_amt;
        let tax_amt			= Math.floor(sale_net_taxation_amt / 11);

        let fee_ratio			= parseInt(row.fee_ratio);
        let fee					= ( sale_amt + clm_amt ) * fee_ratio / 100;
        let fee_dc_amt		= parseInt(row.fee_dc_amt);

        // 수수료
        let fee_net		= fee - fee_dc_amt;
        // 정산금액
        let acc_amt		= sale_net_amt - fee_net;

        gx.gridOptions.api.applyTransaction({ update: [{...row,
            sale_net_taxation_amt: sale_net_taxation_amt,
            sale_net_taxfree_amt: sale_net_taxfree_amt,
            sale_net_amt: sale_net_amt,
            tax_amt: tax_amt,
            fee: fee,
            fee_net: fee_net,
            acc_amt: acc_amt
        }] });

        gx.CalAggregation();

    };

    const setAccountClose = () => {
        if (confirm("마감완료된 자료는 삭제 및 수정이 불가능합니다. 마감완료 처리하시겠습니까?")) {
            axios({
                url: URL.CLOSE,
                method: 'post',
                data: { idx: document.search.idx.value }
            }).then((response) => {
                if (response.data.result == 1) {
                    opener.Search();
                    location.reload();
                }
            }).catch((error) => { console.log(error) });
        }
    };

    const removeAll = () => {
        if (confirm("삭제된 자료는 다시 마감추가 하셔야 합니다. 정말로 삭제 하시겠습니까?")) {
            axios({
                url: URL.REMOVE,
                method: 'delete',
                data: { idx: document.search.idx.value }
            }).then((response) => {
                if (response.data.result == 1) {
                    opener.Search();
                    self.close();
                } else if(response.data.result == 0) {
                    alert("처리 시 장애가 발생했습니다. 관리자에게 문의해 주십시오.");
                }
            }).catch((error) => {});
        }
    };

    const updateData = () => {
        if (confirm("마감 내역을 저장 하시겠습니까?")) {
            const row = gx.getRows();
            let data_arr = [];
            for ( i = 0; i < row.length; i++ ) {

                const { tax_yn, dlv_amt, fee_etc_amt, sale_tax_amt, sale_ntax_amt, sale_amt, tax_amt, fee_ratio, fee, fee_net, acc_amt, bigo, idx } = row[i];

                if ( bigo.indexOf("::") != -1) { alert('비고란에 :: 문자는 허용되지 않습니다.'); return; }
                if ( bigo.indexOf("<>") != -1) { alert('비고란에 <> 문자는 허용되지 않습니다.'); return; }

                let line_arr = [];
                line_arr.push(tax_yn);
                line_arr.push(dlv_amt);
                line_arr.push(fee_etc_amt);

                line_arr.push(sale_tax_amt);
                line_arr.push(sale_ntax_amt);
                line_arr.push(sale_amt);
                line_arr.push(tax_amt);

                line_arr.push(fee_ratio);
                line_arr.push(fee);
                line_arr.push(fee_net);
                line_arr.push(acc_amt);
                line_arr.push(bigo);
                line_arr.push(idx);

                line_data = line_arr.join("::");
                data_arr.push(line_data);
                
            }
            if ( data_arr.length == 0 ) { alert('처리할 데이터가 없습니다.'); return; }
            let data = data_arr.join("<>");
            
            axios({
                url: URL.UPDATE,
                method: 'put',
                data: { idx: document.search.idx.value, data: data }
            }).then((response) => {
                console.log(response);
                if (response.data.result == 1) {
                    window.Search();
                }
            }).catch((error) => { console.log(error) });

        }
    };

    /*
        Function: isNumber
            Check Number

        Parameters:
            Num - number

        Returns:
            true or false
        */
    function isNumber(value) {
        var num = parseFloat(value); // 정수 변환
        if (isNaN(num)) { // 값이 NaN 이면 숫자 아님.
            return false;
        }
        return true;
    }

</script>

@stop