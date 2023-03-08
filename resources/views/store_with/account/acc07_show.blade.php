@extends('store_with.layouts.layout-nav')
@section('title','마감상세내역')
@section('content')

<style>
    .text { box-sizing: border-box; margin-top: 1px;}
    .form-control-sm { padding: 0.25rem 0.5rem; }
    #gd {text-overflow: initial;}
    .custom-box { position: relative; background: #f6f6f6; border-radius: 7px; font-weight: 400; }
    .custom-box:after {
        bottom: 100%;
        left: 3%;
        border: solid transparent;
        content: "";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-bottom-color: #f6f6f6;
        border-width: 7px;
        margin-left: -7px;
    }
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
        <input type="hidden" name="sdate" value="{{ @$closed->sday }}"/>
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>마감정보</h4>
                    <div>
                        @if(@$closed->closed_yn !== 'Y')
                        <a href="#" onclick="return completeAccount();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-check mr-1"></i>마감완료</a>
                        <a href="#" onclick="return removeAccount();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-trash mr-1"></i>마감삭제</a>
                        @else
                        <a href="#" onclick="" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 정산서 다운로드</a>
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
                    <div class="row p-0 m-0 w-100">
                        <div class="col-lg-4 pl-lg-0 mt-3">
                            <p class="fs-14 font-weight-bold"><i class="bx bx-won fs-16 text-primary"></i> 최종정산금액 - <span id="account_amt" class="text-danger">{{ number_format(@$closed->account_amt) }}</span>원</p>
                            <div class="p-3 mt-2 custom-box">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <p>중간관리자 수수료 + 기타재반자료</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-3">
                            <p class="fs-14 font-weight-bold"><i class="bx bx-won fs-16 text-primary"></i> 중간관리자 수수료 - <span id="fee_amt">{{ number_format(@$closed->fee_amt) }}</span>원</p>
                            <div class="p-3 mt-2 custom-box">
                                <div class="row">
                                    <div class="col-6 d-flex justify-content-between">
                                        <p>정상1 수수료</p>
                                        <p id="fee_amt_JS1"><span id="fee_amt">{{ number_format(@$closed->fee_JS1) }}</span>원</p>
                                    </div>
                                    <div class="col-6 d-flex justify-content-between">
                                        <p>특가 수수료</p>
                                        <p id="fee_amt_TG"><span id="fee_amt">{{ number_format(@$closed->fee_TG) }}</span>원</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 d-flex justify-content-between">
                                        <p>정상2 수수료</p>
                                        <p id="fee_amt_JS2"><span id="fee_amt">{{ number_format(@$closed->fee_JS2) }}</span>원</p>
                                    </div>
                                    <div class="col-6 d-flex justify-content-between">
                                        <p>용품 수수료</p>
                                        <p id="fee_amt_YP"><span id="fee_amt">{{ number_format(@$closed->fee_YP) }}</span>원</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 d-flex justify-content-between">
                                        <p>정상3 수수료</p>
                                        <p id="fee_amt_JS3"><span id="fee_amt">{{ number_format(@$closed->fee_JS3) }}</span>원</p>
                                    </div>
                                    <div class="col-6 d-flex justify-content-between">
                                        <p>특가(온라인) 수수료</p>
                                        <p id="fee_amt_OL"><span id="fee_amt">{{ number_format(@$closed->fee_OL) }}</span>원</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-3">
                            <p class="fs-14 font-weight-bold"><i class="bx bx-won fs-16 text-primary"></i> 기타재반자료 - <a href="javascript:void(0);" class="text-decoration-underline" onclick="return openExtraAmtPopup('{{ @$closed->store_cd }}');"><u id="extra_amt">{{ number_format(@$closed->extra_amt) }}</u></a>원</p>
                            <div class="p-3 mt-2 custom-box">
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <p>인건비</p>
                                        <p id="extra_P_amt"><span id="fee_amt">{{ number_format(@$closed->extra_P_amt) }}</span>원</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <p>매장부담금</p>
                                        <p id="extra_S_amt"><span id="fee_amt">{{ number_format(@$closed->extra_S_amt) }}</span>원</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-between">
                                        <p>본사부담금</p>
                                        <p id="extra_C_amt"><span id="fee_amt">{{ number_format(@$closed->extra_C_amt) }}</span>원</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                @if(@$closed->closed_yn !== 'Y')
                <a href="#" onclick="return completeAccount();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-check mr-1"></i>마감완료</a>
                <a href="#" onclick="return removeAccount();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-trash mr-1"></i>마감삭제</a>
                @else
                <a href="#" onclick="" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 정산서 다운로드</a>
                @endif
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body pb-0">
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
                            <h6 class="m-0 font-weight-bold">특가(온라인) (총 <span id="gd-online-total" class="text-primary">0</span>건)</h6>
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
</div>

<script type="text/javascript" charset="utf-8">
	const CENTER = { 'text-align': 'center' };
    const SET_YELLOW = (params) => ({ "background-color": params.node.rowPinned === 'top' ? 'none' : ("{{ @$closed->closed_yn }}" === 'Y' ? 'none' : '#ffff99') });
    const SET_EDITABLE = (params, cond = true) => cond && "{{ @$closed->closed_yn }}" !== 'Y' && params.node.rowPinned !== 'top';
    
    const columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28},
		{field: "account_idx", headerName: "마감일련", pinned: 'left', cellStyle: CENTER, width: 70,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : params.value,
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
        {field: "prd_cd", headerName: "바코드", width: 125, cellStyle: CENTER},
		{field: "goods_no", headerName: "온라인코드",	width: 70, cellStyle: CENTER},
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
        {field: "sale_net_taxation_amt", headerName: "과세", width: 90, type: "currencyType", aggregation: true},
		{field: "sale_net_taxfree_amt", headerName: "비과세", width: 50, type: "currencyType", aggregation: true},
		{field: "dlv_amt", headerName: "배송비", width: 90, type: "currencyType", aggregation: true, cellStyle: SET_YELLOW, editable: SET_EDITABLE},
		{field: "etc_amt", headerName: "기타정산액", width: 90, type: "currencyType", aggregation: true, cellStyle: SET_YELLOW, editable: SET_EDITABLE},
        {headerName: "매출", field: "sales",
			children: [
				{field: "sale_type_nm", headerName: "구분", width: 50, type: "currencyType", cellStyle: CENTER},
				{field: "sale_JS", headerName: "정상", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_TG", headerName: "특가", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_YP", headerName: "용품", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_net_amt", headerName: "매출합계", width: 90, type: "currencyType", aggregation: true},
				{field: "sale_amt_except_vat", headerName: "매출합계(-VAT)", width: 90, type: "currencyType", aggregation: true},
			]
        },
        {field: "user_nm", headerName: "주문자", width: 60, cellStyle: CENTER},
		{field: "pay_type_nm",	headerName: "결제방법",	width: 70, cellStyle: CENTER},
		{field: "ord_state_nm", headerName: "주문상태", width: 70, cellStyle: StyleOrdState},
		{field: "ord_date",	headerName: "출고완료일", width: 80, cellStyle: CENTER},
		{field: "clm_state_nm",headerName: "클레임상태", width: 70, cellStyle: StyleClmState},
		{field: "clm_end_date", headerName: "클레임완료일",	width: 80},
		{field: "memo", headerName: "메모",	width: 200, editable: (params) => params.node.rowPinned === 'top' ? false : true, cellStyle: SET_YELLOW},
    ];

    const online_columns = columns.map((col, i) => {
        if (col.field === 'store') return {...col, headerName: "배송매장정보"};
        if (col.field === 'sale_place_nm') return {...col, hide: false};
        if (col.field === 'clm_state_nm' || col.field === 'clm_end_date') return {...col, hide: true};
        if (col.field === 'sales') return {...col, headerName: "특가(온라인) 수수료", children: [
            {field: "sale_net_amt", headerName: "판매처매출", width: 100, type: "currencyType", aggregation: true},
            {field: "sale_amt_except_vat", headerName: "판매처매출(-VAT)", width: 100, type: "currencyType", aggregation: true},
            {field: "fee_rate_OL", headerName: "수수료율(%)", width: 80, type: "percentType"},
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

        pApp.ResizeGrid(480, 380);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee', 'font-weight': 'bold' }
                }
            },
            onCellValueChanged: (e) => {
				if (e.oldValue !== e.newValue) {
                    if (e.column.colId !== 'memo') {
                        const val = e.newValue;
                        if (isNaN(val) || val == '' || parseFloat(val) < 0) {
                            alert("숫자만 입력가능합니다.");
                            e.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        } else {
                            const sale_type_colId = 'sale_' + e.data.sale_type;
                            e.data[sale_type_colId] = e.data['old_sale_net_amt'] + (e.data.dlv_amt * 1) + (e.data.etc_amt * 1);
                            e.data.sale_net_amt = e.data[sale_type_colId];
                            e.data.sale_amt_except_vat = (e.data.sale_net_amt || 0) / 1.1;

                            e.api.redrawRows({ rowNodes: [e.node] });
                            updatePinnedRow(false, ['dlv_amt', 'etc_amt', 'sale_JS', 'sale_TG', 'sale_TG', 'sale_net_amt','sale_amt_except_vat']);
                            gx.setFocusedWorkingCell();
                        }
                    }
                    e.node.setSelected(true);
				}
			},
        });

        pApp2.ResizeGrid(550, 250);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, online_columns, {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee', 'font-weight': 'bold' }
                }
            },
            onCellValueChanged: (e) => {
				if (e.oldValue !== e.newValue) {
                    if (e.column.colId !== 'memo') {
                        const val = e.newValue;
                        if (isNaN(val) || val == '' || parseFloat(val) < 0) {
                            alert("숫자만 입력가능합니다.");
                            e.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        } else {                            
                            e.data.sale_net_amt = e.data.old_sale_net_amt + (e.data.dlv_amt * 1) + (e.data.etc_amt * 1);
                            e.data.sale_amt_except_vat = (e.data.sale_net_amt || 0) / 1.1;
                            e.data.fee_OL = Math.round((e.data.sale_net_amt || 0) / 1.1 * e.data.fee_rate_OL / 100);

                            e.api.redrawRows({ rowNodes: [e.node] });
                            updatePinnedRow(true, ['dlv_amt', 'etc_amt', 'sale_net_amt','sale_amt_except_vat', 'fee_OL']);
                            gx2.setFocusedWorkingCell();
                        }
                    }
                    e.node.setSelected(true);
				}
			},
        });
        
        Search();
        SearchOnline();
    });

    function Search(callback) {
        if (callback) return location.reload();
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top" });
        gx.Request('/store/account/acc07/show-search/except-online', data, -1);
    };

    function SearchOnline() {
        let data = $('form[name="search"]').serialize();
        gx2.Aggregation({ "sum": "top" });
        gx2.Request('/store/account/acc07/show-search/online', data, -1);
    };

    // 합계 row 업데이트
    const updatePinnedRow = (is_online = false, colArray = []) => {
        const defs = is_online 
            ? gx2.gridOptions.columnApi.columnController.columnDefs 
            : gx.gridOptions.columnApi.columnController.columnDefs;
        
        let cols = defs
            .reduce((a,c) => {
                let result = [];
                if(c.children && c.children.length > 0) result = result.concat(c.children);
                return a.concat(result).concat(c);
            }, [])
            .map(c => ({ field: c.field, value: 0 }))
            .filter(c => colArray.includes(c.field));

        const rows = is_online ? gx2.getRows() : gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                cols.forEach((col) => {
                    col.value += parseFloat(row[col.field] || 0);
                });
            });
        }

        cols = cols.reduce((a,c) => {
            a[c.field] = c.value;
            return a;
        }, {});

        if (is_online) {
            let pinnedRow = gx2.gridOptions.api.getPinnedTopRow(0);
            gx2.gridOptions.api.setPinnedTopRowData([
                { ...pinnedRow.data, ...cols }
            ]);
        } else {   
            let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            gx.gridOptions.api.setPinnedTopRowData([
                { ...pinnedRow.data, ...cols }
            ]);
        }
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

                // 업데이트 정보 반영
                const closed = res.data.closed;
                $("#fee_amt_JS1").text(Comma(closed.fee_JS1));
                $("#fee_amt_JS2").text(Comma(closed.fee_JS2));
                $("#fee_amt_JS3").text(Comma(closed.fee_JS3));
                $("#fee_amt_TG").text(Comma(closed.fee_TG));
                $("#fee_amt_YP").text(Comma(closed.fee_YP));
                $("#fee_amt_OL").text(Comma(closed.fee_OL));
                $("#fee_amt").text(Comma(closed.fee_amt));
                $("#extra_P_amt").text(Comma(closed.extra_P_amt));
                $("#extra_S_amt").text(Comma(closed.extra_S_amt));
                $("#extra_C_amt").text(Comma(closed.extra_C_amt));
                $("#extra_amt").text(Comma(closed.extra_amt));
                $("#account_amt").text(Comma(closed.account_amt));
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

    // 기타재반자료 상세
	function openExtraAmtPopup(store_cd) {
		const sdate = $('input[name="sdate"]').val();
		const url = '/store/account/acc05/show?date=' + sdate.substring(0,6) + '&store_cd=' + store_cd;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
	}

</script>

@stop