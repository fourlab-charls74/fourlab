@extends('store_with.layouts.layout')
@section('title','출고')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">출고</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 출고</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    @if(Auth('head')->user()->logistics_group_yn == 'N')
                    <a href="/store/stock/stk12" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>초도출고</a>
                    <a href="/store/stock/stk13" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>판매분출고</a>
                    <a href="/store/stock/stk14" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
                    <a href="/store/stock/stk15" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>일반출고</a>
                    @endif
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
                <input type='hidden' name='goods_nos' value='' />
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
                            <div class="form-group">
                                <label>출고일자</label>
                                <div class="form-inline date-select-inbox">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="rel_order">출고구분/차수</label>
                            <div class="flex_box">
                                <select name='rel_type' class="form-control form-control-sm" style="width:37%;">
                                    <option value=''>전체</option>
                                    @foreach ($rel_types as $rel_type)
                                        <option value='{{ $rel_type->code_id }}'>{{ $rel_type->code_val }}</option>
                                    @endforeach
                                </select>
                                <span class="text_line" style="text-align: center; width: 5%">/</span>
                                <div class="form-inline-inner input_box" style="width: 58%">
                                    <input type='text' class="form-control form-control-sm search-enter" name='rel_order' id="rel_order" value="">
                                </div>
                            </div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>출고상태</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <select name='state' class="form-control form-control-sm mr-2">
                                    <option value=''>전체</option>
                                    @foreach ($rel_states as $key => $value)
                                        <option value='{{ $key }}'>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div class="custom-control custom-checkbox form-check-box" style="min-width: 130px;">
                                    <input type="checkbox" class="custom-control-input" name="ext_done_state" id="ext_done_state" value="Y" checked>
                                    <label class="custom-control-label font-weight-normal" for="ext_done_state">매장입고완료 제외</label>
                                </div>
                            </div>
						</div>
					</div>
				</div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">판매채널/매장구분</label>
                            <div class="d-flex align-items-center">
                                <div class="flex_box w-100">
                                    <select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
                                        <option value=''>전체</option>
                                    @foreach ($store_channel as $sc)
                                        <option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <span class="mr-2 ml-2">/</span>
                                <div class="flex_box w-100">
                                    <select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
                                        <option value=''>전체</option>
                                    @foreach ($store_kind as $sk)
                                        <option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="store_no">매장명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store multi_select" multiple></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품검색조건</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">바코드</label>
                            <div class="flex_box">
                                <input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
                                <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm_eng">상품명(영문)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                        <option value="5000">5000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="req_rt">출고요청일</option>
                                        <option value="goods_no">온라인코드</option>
                                        <option value="prd_cd">바코드</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row search-area-ext d-none">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">공급업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_cd" name="com_cd" />
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/온라인코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            @if(Auth('head')->user()->logistics_group_yn == 'N')
            <a href="/store/stock/stk12" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>초도출고</a>
            <a href="/store/stock/stk13" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>판매분출고</a>
            <a href="/store/stock/stk14" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
            <a href="/store/stock/stk15" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>일반출고</a>
            @endif
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
                    <div class="d-flex">
                        @if(Auth('head')->user()->logistics_group_yn == 'N')
                        <div class="d-flex mr-1 mb-1 mb-lg-0">
                            <span class="mr-1">출고예정일</span>
                            <div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $edate }}" autocomplete="off" readonly />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <select id='exp_rel_order' name='exp_rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
                                @foreach ($rel_order_res as $rel_order)
                                    <option value='{{ $rel_order->code_val3 }}'>{{ $rel_order->code_val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="javascript:void(0);" onclick="receipt()" class="btn btn-sm btn-primary shadow-sm">접수</a>
                        <a href="javascript:void(0);" onclick="reject()" class="btn btn-sm btn-primary shadow-sm ml-1">거부</a>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
                        @endif
                        <a href="javascript:void(0);" onclick="release()" class="btn btn-sm btn-primary shadow-sm mr-1">출고</a>
                        @if(Auth('head')->user()->logistics_group_yn == 'N')
                        <a href="javascript:void(0);" onclick="receive()" class="btn btn-sm btn-primary shadow-sm mr-1">매장입고</a>
                        @endif
                    </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
    let rel_states = <?= json_encode(@$rel_states) ?> ;
    let rel_orders = <?= json_encode(@$rel_orders) ?> ;

    function StyleReleaseState(params) {
        let state = {
            "10":"#ff0000",
            "20":"#669900",
            "30":"#190DDB",
            "40":"#C628E8",
            "-10":"#666666",
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                let color = state[params.value];
                return {
                    'color': color,
                    'text-align': 'center'
                }
            }
        }
    }

	let columns = [
        {field: "idx", hide: true},
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 28, headerCheckboxSelection: true,
            checkboxSelection: function(params) {
                return params.data.state < 40 && params.data.state > 0;
            },
        },
        // 출고일자 값 : 출고상태가 요청/접수 일때 -> 출고예정일자(exp_dlv_day) | 출고상태가 출고/입고 일때 -> 출고처리일자(prc_rt)
        {field: "dlv_day", headerName: "출고일자", pinned: 'left', width: 80, cellStyle: {"text-align": "center"}, 
            cellRenderer: function(params) {
               return params.data.state > 0 ? (params.value || '') + (params.data.state < 30 ? ' (예정)' : '') : '';
            }
        },
        {field: "state", headerName: "출고상태", pinned: 'left', cellStyle: StyleReleaseState, width: 60,
            cellRenderer: function(params) {
                return rel_states[params.value];
            }
        },
        {field: "rel_type",	headerName: "출고구분", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 60, cellStyle: {"text-align": "center"}},
        {field: "store_nm",	headerName: "매장", pinned: 'left', width: 140, cellStyle: {"text-align": "center"}},
        {field: "storage_cd",	headerName: "창고코드", pinned: 'left', width: 60, cellStyle: {"text-align": "center"}},
        {field: "storage_nm", headerName: "창고", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
		{field: "goods_no",	headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
		{field: "opt_kind_nm",	headerName: "품목", width: 80, cellStyle: {"text-align": "center"}},
		{field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
		{field: "style_no",	headerName: "스타일넘버", cellStyle: {"text-align": "center"}},
		{field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
		{field: "goods_nm_eng",	headerName: "상품명(영문)", type: 'HeadGoodsNameType', width: 200},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
		{field: "goods_opt", headerName: "옵션", width: 130},
		{field: "storage_wqty", headerName: "창고재고", width: 60, type: "currencyType"},
		{field: "store_wqty", headerName: "매장재고", width: 60, type: "currencyType"},
		{field: "qty", headerName: "수량", type: "currencyType", width: 50,
            editable: function(params) {return params.data.state === 10;}, 
            cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99"} : {};},
            cellRenderer: function(params) {
                if (params.data.state != 10) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + Comma(params.value) + '</a>';
                    }
                } else {
                    return params.data.qty;
                }
            }
        },
		{field: "exp_dlv_day", headerName: "출고예정일자", cellStyle: {"text-align": "center"}, width: 90,
           cellRenderer: function(params) {
                return params.data.dlv_day;
           }
        },
		{field: "rel_order", headerName: "출고차수", width: 100, cellStyle: {"text-align": "center"}},
        {field: "last_release_date", headerName: "최근출고일", width: 90,
            cellRenderer: function(params){
                let last_release_date = params.data.last_release_date;
                let date = new Date(last_release_date);
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();

                if (year > 1970) {
                    return `${year}-${month >= 10 ? month : '0' + month}-${day >= 10 ? day : '0' + day}`;
                }
            },
            cellStyle : function(params) {
                if(params.data.prc_rt == params.data.last_release_date || params.data.req_rt == params.data.last_release_date) {
                    return {"color" : "red", "text-align" : "center"};
                } 
            }
        },
        {field: "req_comment", headerName: "매장메모", width: 300},
        {field: "comment", headerName: "본사메모", width: 300, 
            editable: function(params) {return params.data.state === 10;}, 
            cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99"} : {};}
        },
        {field: "req_nm", headerName: "요청자", cellStyle: {"text-align": "center"}},
        {field: "req_rt", headerName: "요청일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "rec_nm", headerName: "접수자", cellStyle: {"text-align": "center"}},
        {field: "rec_rt", headerName: "접수일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "prc_nm", headerName: "처리자", cellStyle: {"text-align": "center"}},
        {field: "prc_rt", headerName: "처리일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "fin_nm", headerName: "완료(입고)자", cellStyle: {"text-align": "center"}},
        {field: "fin_rt", headerName: "완료(입고)일시", width: 120, cellStyle: {"text-align": "center"}},
		{field: "document_number",	headerName: "전표번호", width: 60, cellStyle: {"text-align": "center"}},
		{field: "del_rt", headerName: "명세서 출력", cellStyle: {"text-align": "center", "color": "#4444ff", "font-size": '13px'},
			cellRenderer: function(params) {
				if(params.data.state >= 10) {
					return `<a href="javascript:void(0);" style="color: inherit;" onclick="printDocument(${params.data.document_number}, ${params.data.idx})">출력</a>`;
				} else{
					return '-';
				}
			}
		},
	];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
                if (e.column.colId == "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    }
                }
            }
        });
        Search();

        // 매장검색
        $( ".sch-store" ).on("click", function() {
            searchStore.Open(null, "multiple");
        });
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk10/search', data, 1);
	}

    function selectCurRow(fieldName, e, rowIndex) {
        const node = gx.getRowNode(rowIndex);
        node.data[fieldName] = e.value;
        node.setDataValue(fieldName, e.value);
    }

    // 접수 (10 -> 20)
    function receipt() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("접수처리할 항목을 선택해주세요.");
        if(rows.filter(r => r.state !== 10).length > 0) return alert("'요청'상태의 항목만 접수처리 가능합니다.");
        if(!confirm("선택한 항목을 접수처리하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk10/receipt',
            method: 'post',
            data: {
                data: rows, 
                exp_dlv_day: $("[name=exp_dlv_day]").val(), 
                rel_order: $("[name=exp_rel_order]").val(),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("접수처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 출고 (20 -> 30)
    function release() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("출고처리할 항목을 선택해주세요.");
        if(rows.filter(r => r.state !== 20).length > 0) return alert("'접수'상태의 항목만 출고처리 가능합니다.");
        if(!confirm("선택한 항목을 출고처리하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk10/release',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("출고처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 매장입고 (30 -> 40)
    function receive() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("매장입고처리할 항목을 선택해주세요.");
        if(rows.filter(r => r.state !== 30).length > 0) return alert("'출고'상태의 항목만 매장입고처리 가능합니다.");
        if(!confirm("선택한 항목을 매장입고처리하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk10/receive',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("매장입고처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 거부 (10 -> -10)
    function reject() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("거부처리할 항목을 선택해주세요.");
        if(rows.filter(r => r.state !== 10).length > 0) return alert("'요청'상태의 항목만 거부처리 가능합니다.");
        if(rows.filter(r => !r.comment).length > 0) return alert("'메모'에 거부사유를 반드시 입력해주세요.");
        if(!confirm("선택한 항목을 거부처리하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk10/reject',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("거부처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
	$(document).ready(function() {
		const store_channel = document.getElementById("store_channel");
		const store_channel_kind = document.getElementById("store_channel_kind");

		store_channel.addEventListener("change", () => {
			if (store_channel.value) {
				store_channel_kind.disabled = false;
			} else {
				store_channel_kind.disabled = true;
			}
		});
	});

	// 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
	function chg_store_channel() {

		const sel_channel = document.getElementById("store_channel").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel',
			data: {
				'store_channel' : sel_channel
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#store_channel_kind').append(option);
					}

				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}

	// 출고 거래명세서 출력
	function printDocument(document_number, idx) {
		location.href = '/store/stock/stk10/download?document_number=' + document_number + '&idx=' + idx;
	}
</script>
@stop
