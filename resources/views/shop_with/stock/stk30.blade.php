@extends('shop_with.layouts.layout')
@section('title','매장반품관리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장반품관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 매장관리</span>
		<span>/ 매장반품관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="initSearch(['#store_no'])">검색조건 초기화</a> -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">반품요청일</label>
                            <div class="form-inline date-select-inbox">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">반품상태</label>
                            <div class="d-flex">
                                <select name='sr_state' class="form-control form-control-sm">
									<option value=''>전체</option>
                                    @foreach ($sr_states as $sr_state)
                                    <option value='{{ $sr_state->code_id }}'>{{ $sr_state->code_val }}</option>
                                    @endforeach
								</select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">반품사유</label>
                            <div class="d-flex">
                                <select name='sr_reason' class="form-control form-control-sm">
									<option value=''>전체</option>
                                    @foreach ($sr_reasons as $sr_reason)
                                    <option value='{{ $sr_reason->code_id }}'>{{ $sr_reason->code_val }}</option>
                                    @endforeach
								</select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">반품창고</label>
                            <div class="d-flex">
                                <select name='storage_cd' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach (@$storages as $storage)
                                        <option value='{{ $storage->storage_cd }}'>{{ $storage->storage_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="sr_cd">반품코드</option>
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
            </div>
		</div>
        
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
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
                        <!-- <div class="d-flex">
                            <select id='chg_return_state' name='chg_return_state' class="form-control form-control-sm mr-1" style='width:70px;display:inline'>
                                <option value="30">이동</option>
                                <option value="40">완료</option>
                            </select>
                        </div>
                        <a href="javascript:void(0);" onclick="ChangeState()" class="btn btn-sm btn-primary">상태변경</a>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span> -->
                    </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">

    function StyleReturnState(params) {
        let state = {
            "10":"#222222", // 요청
            "30":"#0000ff", // 이동
            "40":"#2aa876", // 완료
        }
        if (params.value !== undefined) {
            if (state[params.data.sr_state]) {
                return {
                    'color': state[params.data.sr_state],
                    'text-align': 'center'
                }
            }
        }
    }

	let columns = [
		{headerName: "No", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellClass: 'hd-grid-code', pinned: 'left'},
		{field: "sr_cd", headerName: "반품코드", width: 140, cellClass: 'hd-grid-code', pinned: 'left',
			cellRenderer: function(params) {
				let sr_date = params.data.sr_date;
				let return_date = sr_date.replaceAll('-','');
				let sr_cd = params.data.sr_cd.toString();
				let return_code = sr_cd.padStart(3, '0');

				return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.data.store_cd}_${return_date}_${return_code}</a>`;
			}
		},
        {field: "sr_date", headerName: "반품요청일", width: 90, cellClass: 'hd-grid-code'},
        {field: "sr_pro_date", headerName: "반품처리일", width: 90, cellClass: 'hd-grid-code'},
        {field: "sr_fin_date", headerName: "반품완료일", width: 90, cellClass: 'hd-grid-code'},
        {field: "sr_state", hide: true},
        {field: "sr_state_nm", headerName: "반품상태", width: 65, cellStyle: StyleReturnState},
        {field: "sr_kind", hide: true},
        {field: "storage_cd", hide: true},
        {field: "storage_nm", headerName: "반품창고", width: 120},
        {field: "store_cd", headerName: "매장코드", width: 70, cellClass: 'hd-grid-code'},
        {field: "store_nm", headerName: "매장명", width: 150},
		{field: "store_qty", headerName: "매장보유재고", type: "currencyType", width: 90},
		{field: "sr_price", headerName: "요청금액", type: "currencyType", width: 80},
		{field: "sr_qty", headerName: "요청수량", type: "currencyType", width: 60},
		{field: "return_p_price", headerName: "처리금액", type: "currencyType", width: 80},
		{field: "return_p_qty", headerName: "처리수량", type: "currencyType", width: 60},
		{field: "fixed_return_price", headerName: "확정금액", type: "currencyType", width: 80},
		{field: "fixed_return_qty", headerName: "확정수량", type: "currencyType", width: 60},
        {field: "sr_reason", hide: true},
        {field: "sr_reason_nm", headerName: "반품사유", width: 80, cellClass: 'hd-grid-code'},
        {field: "comment", headerName: "메모", width: 200},
		{field: "print", headerName: "명세서 출력", width: 100, cellStyle: {"text-align": "center", "color": "#4444ff", "font-size": '13px'},
			cellRenderer: function(params) {
				if(params.data.sr_state >= 10) {
					return `<a href="javascript:void(0);" style="color: inherit;" onclick="printDocument(${params.data.sr_cd})">출력</a>`;
				} else{
					return '-';
				}
			}
		},
		{width: 0},
	];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd", height: 265 });

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/shop/stock/stk30/search', data, 1);
	}

    // 매장반품내역 팝업 오픈
    function openDetailPopup(sr_cd) {
        const url = '/shop/stock/stk30/show/' + sr_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }
    
	// 창고반품 거래명세서 출력
	function printDocument(sr_cd) {
		location.href = '/shop/stock/stk30/download?sr_cd=' + sr_cd;
	}
</script>
@stop
