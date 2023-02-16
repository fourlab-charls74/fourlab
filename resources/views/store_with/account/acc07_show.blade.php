@extends('store_with.layouts.layout-nav')
@section('title','마감상세내역')
@section('content')

<style>
    .text { box-sizing: border-box; margin-top: 1px;}
    .form-control-sm { padding: 0.25rem 0.5rem; }
    #gd {text-overflow: initial;}
    .fee_table {border: 1px solid #ccc; border-width: 1px 1px 0;}
    .fee_table p {padding: 7px 12px; display: flex; align-items: center; border-bottom: 1px solid #ccc;}
    .fee_table p:nth-child(odd) {font-weight: 500;background-color: #ededed;}
    .fee_table p:nth-child(even) {padding-left: 0;justify-content: flex-end;}
</style>

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">마감상세내역</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>매장관리</span>
            <span>/ 정산/마감관리</span>
            <span>/ 매장중간관리자정산마감</span>
        </div>
    </div>
    <form method="get" name="search">
        <input type="hidden" name="idx" value="{{ @$closed->idx }}"/>
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>마감정보</h4>
                    <div>
                        @if(@$closed->closed_yn !== 'Y')
                        <a href="#" onclick="return completeAccount();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-check mr-1"></i>마감완료</a>
                        <a href="#" onclick="return removeAccount();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-trash mr-1"></i>마감삭제</a>
                        @else
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 정산서 다운로드</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">마감대상일자</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ @$closed->sday }}
                                        <span class="text_line">~</span>
                                        {{ @$closed->eday }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="store_nm">매장/매니저</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ @$closed->store_nm }} / {{ @$closed->manager_nm ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="closed_yn">마감상태</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ @$closed->closed_yn == 'Y' ? '마감완료' : '마감추가' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="rt">등록일</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ @$closed->rt }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="closed_date">마감일</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ @$closed->closed_date ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="admin_nm">처리자</label>
                                <div class="flex_box">
                                    <div class="form-control-sm text">
                                        {{ @$closed->admin_nm }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row fee_table p-0 m-0 mt-3 w-100">
                        <p class="col-3 col-lg-1">정상1</p>
                        <p class="col-3 col-lg-1" id="fee_amt_JS1">{{ number_format(@$closed->fee_JS1) }}</p>
                        <p class="col-3 col-lg-1">정상2</p>
                        <p class="col-3 col-lg-1" id="fee_amt_JS2">{{ number_format(@$closed->fee_JS2) }}</p>
                        <p class="col-3 col-lg-1">정상3</p>
                        <p class="col-3 col-lg-1" id="fee_amt_JS3">{{ number_format(@$closed->fee_JS3) }}</p>
                        <p class="col-3 col-lg-1">특가</p>
                        <p class="col-3 col-lg-1" id="fee_amt_TG">{{ number_format(@$closed->fee_TG) }}</p>
                        <p class="col-3 col-lg-1">용품</p>
                        <p class="col-3 col-lg-1" id="fee_amt_YP">{{ number_format(@$closed->fee_YP) }}</p>
                        <p class="col-3 col-lg-1">특가(온라인)</p>
                        <p class="col-3 col-lg-1" id="fee_amt_OL">{{ number_format(@$closed->fee_OL) }}</p>
                        <p class="col-3 col-lg-1">수수료합계</p>
                        <p class="col-3 col-lg-1" id="fee_amt">{{ number_format(@$closed->fee_amt) }}</p>
                        <p class="col-3 col-lg-1">기타재반</p>
                        <p class="col-3 col-lg-1" id="extra_amt">{{ number_format(@$closed->extra_amt) }}</p>
                        <p class="col-3 col-lg-1">정산금액</p>
                        <p class="col-9 col-lg-7 fs-14 text-danger" id="account_amt" style="font-weight: 500;">{{ number_format(@$closed->account_amt) }}</p>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                @if(@$closed->closed_yn !== 'Y')
                <a href="#" onclick="return completeAccount();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-check mr-1"></i>마감완료</a>
                <a href="#" onclick="return removeAccount();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-trash mr-1"></i>마감삭제</a>
                @else
                <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 정산서 다운로드</a>
                @endif
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-2">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span>건</h6>
                        </div>
                        @if(@$closed->closed_yn !== 'Y')
                            <div class="fr_box">
                                <div class="flax_box">
                                    <a href="#" onclick="return updateAccount();" class="btn-sm btn btn-primary"><i class="bx bx-save mr-1"></i> 저장</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-title mb-2">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">특가(온라인) (총 <span id="gd-total-online" class="text-primary">0</span>건)</h6>
                        </div>
                        @if(@$closed->closed_yn !== 'Y')
                            <div class="fr_box">
                                <div class="flax_box">
                                    <a href="#" onclick="return updateOnlineAccount();" class="btn-sm btn btn-primary"><i class="bx bx-save mr-1"></i> 저장</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd-online" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>

    {{-- <div class="card shadow">
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
    </div> --}}
</div>

<script type="text/javascript" charset="utf-8">
	const CENTER = { 'text-align': 'center' };
    const EDITABLE = (params) => ({ "background-color": params.node.rowPinned === 'top' ? 'none' : '#ffff99' });
    
    const columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28},
		{field: "account_idx", headerName: "마감일련", pinned: 'left', cellStyle: CENTER, width: 70,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : parseInt(params.value) + 1,
		},
        {field: "a_ord_state_nm", headerName: "매출구분", width: 55, pinned: 'left', cellStyle: (params) => ({...CENTER, "color": params.data.ord_state != 30 ? "#dd0000" : "none"})},
		{field: "state_date", headerName: "일자", width: 80, pinned: 'left', cellStyle: CENTER},
		{field: "ord_no", headerName: "주문번호", width: 140, pinned: 'left'},
		{field: "ord_opt_no", headerName: "일련번호", width: 60, cellStyle: CENTER, type: 'StoreOrderNoType', pinned: 'left'},
        {field: "sale_place_nm", headerName: "판매처", width: 80, cellStyle: CENTER, hide: true},
        {headerName: "매장정보", field: "store",
            children: [
                {field: "store_cd",	headerName: "매장코드", width: 60, cellStyle: CENTER},
                {field: "store_nm",	headerName: "매장명", width: 120},
            ]
        },
        {field: "prd_cd", headerName: "상품코드", width: 125, cellStyle: CENTER},
		{field: "goods_no", headerName: "상품번호",	width: 70, cellStyle: CENTER},
		{field: "goods_nm", headerName: "상품명", width: 180, type: 'HeadGoodsNameType'},
		{field: "goods_opt", headerName: "옵션", width: 150},
		{field: "qty", headerName: "수량", width: 50, type: 'currencyType', aggregation: true},
		{field: "sale_amt", headerName: "판매금액", width: 90, type: "currencyType", aggregation: true },
		{field: "clm_amt", headerName: "클레임금액", width: 90, type: "currencyType", aggregation: true },
		{field: "dc_amt", headerName: "할인금액", width: 90, type: "currencyType", aggregation: true },
		{headerName: "쿠폰금액",
			children: [
				{field: "coupon_com_amt", headerName: "업체부담", width: 90, type: "currencyType", aggregation: true },
				{field: "allot_amt", headerName: "본사부담", width: 90, type: "currencyType", aggregation: true },
			]
        },
		{field: "dlv_amt", headerName: "배송비", width: 90, type: "currencyType", aggregation: true},
		// {field: "etc_amt", headerName: "기타정산액", width: 90, type: "currencyType", aggregation: true},
        {field: "sale_net_taxation_amt", headerName: "과세", width: 90, type: "currencyType", aggregation: true},
		{field: "sale_net_taxfree_amt", headerName: "비과세", width: 50, type: "currencyType", aggregation: true},
        {headerName: "매출", field: "sales",
			children: [
				{field: "sale_type_nm", headerName: "구분", width: 50, type: "currencyType", cellStyle: CENTER},
				{field: "sale_JS", headerName: "정상", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_TG", headerName: "특가", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_YP", headerName: "용품", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_amt_except_vat", headerName: "매출합계(-VAT)", width: 90, type: "currencyType", aggregation: true},
			]
        },
        {field: "user_nm", headerName: "주문자", width: 60, cellStyle: CENTER},
		{field: "pay_type_nm",	headerName: "결제방법",	width: 70, cellStyle: CENTER},
		{field: "ord_state_nm", headerName: "주문상태", width: 70, cellStyle: StyleOrdState},
		{field: "ord_date",	headerName: "출고완료일", width: 80, cellStyle: CENTER},
		{field: "clm_state_nm",headerName: "클레임상태", width: 70, cellStyle: StyleClmState},
		{field: "clm_end_date", headerName: "클레임완료일",	width: 80},
		{field: "memo", headerName: "메모",	width: 200, editable: (params) => params.node.rowPinned === 'top' ? false : true, cellStyle: EDITABLE},
    ];

    const online_columns = columns.map((col, i) => {
        if (col.field === 'store') return {...col, headerName: "배송매장정보"};
        if (col.field === 'sale_place_nm') return {...col, hide: false};
        if (col.field === 'sales') return {...col, headerName: "특가(온라인) 수수료", children: [
            {field: "sale_amt_except_vat", headerName: "판매처매출(-VAT)", width: 100, type: "currencyType", aggregation: true},
            {field: "fee_rate_OL", headerName: "수수료율(%)", width: 80, type: "currencyType", aggregation: true},
            {field: "fee_OL", headerName: "수수료", width: 90, type: "currencyType", aggregation: true},
        ]};
        return col;
    });

    const pApp = new App('', { gridId: "#div-gd" });
    const pApp2 = new App('', { gridId: "#div-gd-online" });
    let gx, gx2;

    const acc_idx = "{{ @$closed->idx }}";

    $(document).ready(function() {
        if ("{{ @$closed->idx }}" === '') {
            alert("존재하지않는 마감정보입니다.");
            if(opener === null) history.back();
            else window.close();
        }

        pApp.ResizeGrid(465, 350);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee', 'font-weight': 'bold' }
                }
            },
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
            }
            // getRowNodeId: (data) => data.hasOwnProperty('index') ? data.index : "0",
            // onCellValueChanged: (params) => evtAfterEdit(params),
            // onPinnedRowDataChanged: (params) => {
            //     initTopRowData(params);
			// }
        });

        pApp2.ResizeGrid(710, 205);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, online_columns, {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee', 'font-weight': 'bold' }
                }
            },
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
            }
        });
        
        Search();
        SearchOnline();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top" });
        gx.Request('/store/account/acc07/show-search/except-online', data, -1);
    };

    function SearchOnline() {
        let data = $('form[name="search"]').serialize();
        gx2.Aggregation({ "sum": "top" });
        gx2.Request('/store/account/acc07/show-search/online', data, -1);
    };

    // 마감정보 수정
    function updateAccount() {
        const data = gx.getSelectedRows();
        if (data.length < 1) return alert("저장할 마감상세정보를 선택해주세요.");
        if (!confirm("변경된 마감정보를 저장하시겠습니까?")) return;

        update(data);
    }

    // 특가(온라인)마감정보 수정
    function updateOnlineAccount() {
        const data = gx2.getSelectedRows();
        if (data.length < 1) return alert("저장할 마감상세정보를 선택해주세요.");
        if (!confirm("변경된 특가(온라인) 마감정보를 저장하시겠습니까?")) return;

        update(data, 'online');
    }

    function update(data, type = '') {
        axios({
            url: '/store/account/acc07/update',
            method: 'put',
            data: { data }
        }).then((res) => {
            if (res.data.code === "200") {
                if (type === 'online') SearchOnline();
                else Search();
            } else {
                alert("저장 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
                console.log(res);
            }
        }).catch((err) => {
            alert("에러가 발생했습니다. 관리자에게 문의해주세요.");
            console.log(err);
        });
    }

    // 마감완료
    function completeAccount() {
        if (!confirm("완료처리된 마감정보는 수정할 수 없습니다.\n마감완료처리하시겠습니까?")) return;

        axios({
            url: '/store/account/acc07/complete',
            method: 'post',
            data: { idx: acc_idx }
        }).then((res) => {
            if (res.data.code === "200") {
                alert("마감완료처리가 정상적으로 완료되었습니다.");
                opener.Search();
                location.reload();
            } else {
                alert("마감완료 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
                console.log(res);
            }
        }).catch((err) => {
            alert("에러가 발생했습니다. 관리자에게 문의해주세요.");
            console.log(err);
        });
    };

    // 마감삭제
    function removeAccount() {
        if (!confirm("삭제된 마감정보는 다시 되돌릴 수 없습니다.\n정말 삭제하시겠습니까?")) return;

        axios({
            url: '/store/account/acc07/' + acc_idx,
            method: 'delete',
        }).then((res) => {
            console.log(res);
            if (res.data.code === "200") {
                alert("마감정보가 정상적으로 삭제되었습니다.");
                opener.Search();
                self.close();
            } else {
                alert("마감삭제 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
                console.log(res);
            }
        }).catch((err) => {
            alert("에러가 발생했습니다. 관리자에게 문의해주세요.");
            console.log(err);
        });
    };

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // const CELL_STYLE = {
    //     EDIT: { 'background': '#DEEDB6', 'color': '#FF0000', 'font-weight': 'bold' }
    // };

    // const URL = {
    //     UPDATE: '/store/account/acc07/show_update',
    //     REMOVE: '/store/account/acc07/show_delete',
    //     CLOSE: '/store/account/acc07/show_close'
    // }
    
    // ag-grid set field
    // var columns_d = [
	// 	{field: "num",			headerName: "#", type:'NumType', pinned: 'left'},
	// 	{field: "type",			headerName: "구분",			width:80, pinned: 'left', cellStyle: { 'text-align': 'center' }},
	// 	{field: "state_date",	headerName: "일자",			width:80, pinned: 'left'},
	// 	{field: "ord_no",		headerName: "주문번호",	    width:130, pinned: 'left'},
	// 	{field: "ord_opt_no",	headerName: "일련번호",		width:90, type:'HeadOrdOptNoType', pinned: 'left'},
	// 	{field: "multi_order",	headerName: "복수",			width:70,
	// 		cellRenderer: function(params){
	// 			if( params.value == "Y" ){
	// 				return '<a href="#" onclick="return openHeadOrderOpt(\'' + params.data.ord_opt_no +'\');">'+ params.value +'</a>';
	// 			}
	// 		},
    //         cellStyle: function(params){
	// 			return params.value === 'Y' ? {"background-color": "yellow"} : {};
	// 		},
	// 		pinned: 'left'
	// 	},
	// 	{field: "coupon_nm",	headerName: "쿠폰",			width:70, pinned: 'left'},
	// 	{field: "goods_nm",		headerName: "상품",		width:150, type:'HeadGoodsNameType'},
	// 	{field: "opt_nm",		headerName: "옵션",			width:70},
	// 	{field: "style_no",		headerName: "스타일넘버",	width:110},
	// 	{field: "opt_type",		headerName: "출고형태",		width:90},
	// 	{field: "com_nm",		headerName: "판매처",		width:80},
	// 	{field: "user_nm",		headerName: "주문자",		width:80},
	// 	{field: "pay_type",		headerName: "결제방법",		width:90},
	// 	{field: "tax_yn",		headerName: "과세",			width:70},
	// 	{field: "qty",			headerName: "수량",			width:70, type: 'currencyType', aggregation: true},
	// 	{field: "sale_amt",		headerName: "판매금액",		width:90, type: 'currencyType', aggregation: true},
	// 	{field: "clm_amt",		headerName: "클레임금액",	width:110, type: 'currencyType', aggregation: true},
	// 	{field: "dc_amt",	headerName: "할인금액",		width:90, type: 'currencyType', aggregation: true},
	// 	{
	// 		headerName: '쿠폰금액',
	// 		children: [{
	// 				field: "coupon_com_amt",
	// 				headerName: "(업체부담)",
	// 				width:95,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			}
	// 		]
	// 	},
	// 	{field: "dlv_amt",		headerName: "배송비",		width:80, type: 'currencyType', aggregation: true,
    //         editable: true,
    //         cellStyle: (params) => { return params.node.rowPinned === 'top' ? {} : CELL_STYLE.EDIT; }
    //     },
	// 	{field: "fee_etc_amt",	headerName: "기타정산액",	width:110, type: 'currencyType', aggregation: true,
    //         editable: true,
    //         cellStyle: (params) => { return params.node.rowPinned === 'top' ? {} : CELL_STYLE.EDIT; }
    //     },
	// 	{
	// 		headerName: '매출금액',
	// 		children: [{
	// 				field: "sale_net_taxation_amt",
	// 				headerName: "과세",
	// 				width:90,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "sale_net_taxfree_amt",
	// 				headerName: "비과세",
	// 				width:90,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "sale_net_amt",
	// 				headerName: "소계",
	// 				width:90,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 		]
	// 	},
	// 	{field: "tax_amt",	headerName: "부가세",	type: 'currencyType',	hide:true},
	// 	{
	// 		headerName: '수수료',
	// 		children: [{
	// 				field: "fee_ratio",
	// 				headerName: "수수료율(%)",
	// 				width:135,
    //                 editable: true,
    //                 cellStyle: (params) => { 
    //                     return (
    //                         params.node.rowPinned === 'top' 
    //                             ? {"text-align":"right"} 
    //                             : {"text-align":"right", ...CELL_STYLE.EDIT }
    //                     );
    //                 }
	// 			},
	// 			{
	// 				field: "fee",
	// 				headerName: "판매수수료",
	// 				width:110,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "fee_dc_amt",
	// 				headerName: "할인금액",
	// 				width:90,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "fee_net",
	// 				headerName: "소계",
	// 				width:90,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 		]
	// 	},
	// 	{field: "acc_amt",		headerName: "정산금액",		width:90, type: 'currencyType', aggregation: true},
	// 	{
	// 		headerName: '쿠폰금액',
	// 		children: [{
	// 				field: "fee_allot_amt",
	// 				headerName: "(본사부담)",
	// 				width:95,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			}
	// 		]
	// 	},
	// 	{field: "ord_state",	headerName: "주문상태",		width:90},
	// 	{field: "clm_state",	headerName: "클레임상태",	width:110},
	// 	{field: "ord_date",		headerName: "주문일",		width:80},
	// 	{field: "dlv_end_date",	headerName: "배송완료일",	width:110},
	// 	{field: "clm_end_date",	headerName: "클레임완료일",	width:130},
	// 	{field: "bigo",			headerName: "비고",			width:120, editable: true, 
    //         cellStyle: (params) => { return params.node.rowPinned === 'top' ? {} : CELL_STYLE.EDIT; }
    //     },
    //     {field: "prd_cd",		headerName: "상품코드", width: 120},
	// 	// {field: "goods_no",		headerName: "상품코드1"},
	// 	// {field: "goods_sub",	headerName: "상품코드2"},
	// 	{field: "idx",		headerName: "마감일련번호"}
	// ];
    
    // const initTopRowData = () => {
    //     let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
    //     gx.gridOptions.api.setPinnedTopRowData([
    //         { ...pinnedRow.data, type: '합계' }
    //     ]);
    // };


    // const evtAfterEdit = async (params) => {
    //     const bool = await validation(params);
    //     if (bool) calculate(params);
    // };

    // const stopEditing = () => {
    //     gx.gridOptions.api.stopEditing();
    // };

    // const startEditing = (row_index, col_key) => {
    //     gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    // };

    // const validation = (params) => {

    //     let row = params.data;
    //     const row_index = params.data.index;
    //     const col_key = params.column.colId;

    //     let n = params.newValue;
    //     let p = params.oldValue;

    //     if (col_key == 'dlv_amt') {
    //         n = Math.abs(n);
    //         if (!isNumber(n)) {	// 숫자만 입력
    //             stopEditing();
    //             alert("배송비는 숫자만 입력하실 수 있습니다.");
    //             startEditing(row_index, col_key)
    //             row.dlv_amt = p;
    //             return false;
    //         } else if (n == "" && n != 0) {
    //             stopEditing();
    //             alert("배송비를 입력해 주십시오.");
    //             startEditing(row_index, col_key)
    //             return false;
    //         }
    //     }
    //     if (col_key == 'fee_etc_amt') {
    //         n = Math.abs(n);
    //         if (!isNumber(n)) { // 숫자만 입력
    //             stopEditing();
    //             alert("기타정산액은 숫자만 입력하실 수 있습니다.");
    //             startEditing(row_index, col_key)
    //             row.fee_etc_amt = p;
    //             return false;
    //         } else if (n == "" && n != 0) {
    //             stopEditing();
    //             alert("기타정산액을 입력해 주십시오.");
    //             startEditing(row_index, col_key)
    //             return false;
    //         }
    //     } else if (col_key == 'tax_amt') {
            
    //         if (!isNumber(n)) {	// 숫자만 입력
    //             stopEditing();
    //             alert("수수료율은 숫자만 입력하실 수 있습니다.");
    //             startEditing(row_index, col_key);
    //             row.tax_amt = p;
    //             return false;
    //         }
    //     }

    //     gx.gridOptions.api.applyTransaction({ update: [row] });
        
    //     return true;

    // };

    // const calculate = (params) => {
    //     const row = params.data;

    //     var tax_yn		= row.tax_yn;
    //     var sale_amt	= parseInt(row.sale_amt);
    //     var clm_amt		= parseInt(row.clm_amt);
    //     var dc_amt		= parseInt(row.dc_amt);
    //     var coupon_amt	= parseInt(row.coupon_com_amt);
    //     var dlv_amt		= parseInt(row.dlv_amt);
    //     var etc_amt		= parseInt(row.fee_etc_amt);

    //     // 매출금액
    //     var sale_net_amt	= sale_amt - Math.abs(clm_amt) - dc_amt - coupon_amt + dlv_amt  + etc_amt;

    //     // 매출금액
    //     if (tax_yn == 'Y') {
    //         // 비과세
    //         var sale_net_taxation_amt	= sale_net_amt;
	// 	    var sale_net_taxfree_amt	= 0;
    //     } else { 
    //         // 과세
    //         var sale_net_taxation_amt	= 0;
	// 	    var sale_net_taxfree_amt	= sale_net_amt;
    //     }

    //     var sale_net_amt	= sale_net_taxation_amt + sale_net_taxfree_amt;
    //     let tax_amt			= Math.floor(sale_net_taxation_amt / 11);

    //     let fee_ratio			= parseInt(row.fee_ratio);
    //     let fee					= ( sale_amt + clm_amt ) * fee_ratio / 100;
    //     let fee_dc_amt		= parseInt(row.fee_dc_amt);

    //     // 수수료
    //     let fee_net		= fee - fee_dc_amt;
    //     // 정산금액
    //     let acc_amt		= sale_net_amt - fee_net;

    //     gx.gridOptions.api.applyTransaction({ update: [{...row,
    //         sale_net_taxation_amt: sale_net_taxation_amt,
    //         sale_net_taxfree_amt: sale_net_taxfree_amt,
    //         sale_net_amt: sale_net_amt,
    //         tax_amt: tax_amt,
    //         fee: fee,
    //         fee_net: fee_net,
    //         acc_amt: acc_amt
    //     }] });

    //     gx.CalAggregation();

    // };

    // const updateData = () => {
    //     if (confirm("마감 내역을 저장 하시겠습니까?")) {
    //         const row = gx.getRows();
    //         let data_arr = [];
    //         for ( i = 0; i < row.length; i++ ) {

    //             const { tax_yn, dlv_amt, fee_etc_amt, sale_tax_amt, sale_ntax_amt, sale_amt, tax_amt, fee_ratio, fee, fee_net, acc_amt, bigo, idx } = row[i];

    //             if ( bigo.indexOf("::") != -1) { alert('비고란에 :: 문자는 허용되지 않습니다.'); return; }
    //             if ( bigo.indexOf("<>") != -1) { alert('비고란에 <> 문자는 허용되지 않습니다.'); return; }

    //             let line_arr = [];
    //             line_arr.push(tax_yn);
    //             line_arr.push(dlv_amt);
    //             line_arr.push(fee_etc_amt);

    //             line_arr.push(sale_tax_amt);
    //             line_arr.push(sale_ntax_amt);
    //             line_arr.push(sale_amt);
    //             line_arr.push(tax_amt);

    //             line_arr.push(fee_ratio);
    //             line_arr.push(fee);
    //             line_arr.push(fee_net);
    //             line_arr.push(acc_amt);
    //             line_arr.push(bigo);
    //             line_arr.push(idx);

    //             line_data = line_arr.join("::");
    //             data_arr.push(line_data);
                
    //         }
    //         if ( data_arr.length == 0 ) { alert('처리할 데이터가 없습니다.'); return; }
    //         let data = data_arr.join("<>");
            
    //         axios({
    //             url: URL.UPDATE,
    //             method: 'put',
    //             data: { idx: document.search.idx.value, data: data }
    //         }).then((response) => {
    //             console.log(response);
    //             if (response.data.result == 1) {
    //                 window.Search();
    //             }
    //         }).catch((error) => { console.log(error) });

    //     }
    // };

    // /*
    //     Function: isNumber
    //         Check Number

    //     Parameters:
    //         Num - number

    //     Returns:
    //         true or false
    //     */
    // function isNumber(value) {
    //     var num = parseFloat(value); // 정수 변환
    //     if (isNaN(num)) { // 값이 NaN 이면 숫자 아님.
    //         return false;
    //     }
    //     return true;
    // }

</script>

@stop