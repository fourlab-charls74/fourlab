@extends('shop_with.layouts.layout')
@section('title','동종업계월별매출관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">동종업계월별매출관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 매장관리</span>
        <span>/ 동종업계월별매출관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
                    <!-- 동종업계정보입력이 'Y'면 추가버튼이 생김 -->
                    @if ($competitor_yn == 'Y')
                        <a href="javascript:void(0);" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 등록</a>
                    @endif
                    <a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">매출기간</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
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
                    <div class="col-lg-4 inner-td" style="display:none">
                        <div class="form-group">
                            <label for="store_no">매장명</label>
                            <div class="form-inline inline_btn_box search-enter" >
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="cs.sale_date">매출월</option>
                                        <option value="cs.sale_amt">매출액</option>
                                        <option value="s.store_cd">매장코드</option>
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
                <div class="row">
                    {{-- <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">동종업계</label>
                            <div class="flax_box">
                                <select name='competitor_type' class="form-control form-control-sm search-enter">
                                    <option value=''>전체</option>
                                @foreach ($competitors as $competitor)
                                    <option value='{{ $competitor->code_id }}'>{{ $competitor->code_val }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
            <!-- 동종업계정보입력이 'Y'면 추가버튼이 생김 -->
            @if ($competitor_yn == 'Y')
                <a href="javascript:void(0);" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 등록</a>
            @endif
            <a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
            <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="d-flex justify-content-end">
					<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
						<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
						<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
					</div>
				</div>
                <div class="fr_box">

                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<style>
	.ag-row-level-1 {background-color: #ffffcc !important;}
	.ag-row-level-2 {background-color: #f2f2f2 !important;}
</style>
<script language="javascript">

const pinnedRowData = [{ store_cd : 'total' , "store_amt" : 0, "sale_amt_off" : 0, "sale_amt_on" : 0,  "sale_amt" : 0 }];

const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0);

    
    let columns = [
        {headerName: "매출월", field: "sale_date", rowGroup: true, hide:true,
            cellRenderer:function(params) {
                if(params.value === undefined) return "";
                return '<a href="/shop/stock/stk33?date='+ params.value + '">'+ params.value +'</a>';
            }
        },
        {headerName: "매장명", field: "store_nm", rowGroup: true, hide:true},
        {headerName: '매출월', showRowGroup: 'sale_date', cellRenderer: 'agGroupCellRenderer', width: 130, pinned:'left', sortable: false},
        { field: "store_cd", headerName: "매장코드", pinned:'left', width:60, cellStyle: { 'text-align': "center" }, groupDepth: 1,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 1 ? params.value : '',
        },
        {headerName: '매장명', showRowGroup: 'store_nm', cellRenderer: 'agGroupCellRenderer', width: 180, pinned:'left'},
        {headerName: "동종업계코드", field: "competitor_cd",  pinned:'left', width: 80, cellClass: 'hd-grid-code', hide:true},
        {headerName: "동종업계명", field: "competitor",  pinned:'left', width: 120, cellClass: 'hd-grid-code',
			cellStyle : (params) => {
				if(params.value == '피엘라벤') {
					return {'background' : '#FA8072'};
				} else {
					return {};
				}
			}
		},
		{headerName: "오프라인", field: "sale_amt_off",  pinned:'left', width: 110, cellClass: 'hd-grid-code', type:'currencyType', aggFunc: sumValuesFunc,
			cellStyle : (params) => {
				if(params.node.level == 2 && params.data.competitor == '피엘라벤') {
					return {'text-align' : 'right', 'background': '#FA8072'};
				} else {
					return {'text-align' : 'right'};
				}
			}
		},
		{headerName: "온라인", field: "sale_amt_on",  pinned:'left', width: 110, cellClass: 'hd-grid-code', type:'currencyType', aggFunc: sumValuesFunc,
			cellStyle : (params) => {
				if(params.node.level == 2 && params.data.competitor == '피엘라벤') {
					return {'text-align' : 'right', 'background': '#FA8072'};
				} else {
					return {'text-align' : 'right'};
				}
			}
		},
        {headerName: "매출액", field: "sale_amt",  pinned:'left', width: 110, cellClass: 'hd-grid-code', type:'currencyType', aggFunc: sumValuesFunc,
			cellStyle : (params) => {
				if (params.node.level == 2 && params.data.competitor == '피엘라벤') {
					return {'text-align': 'right', 'background' : '#FA8072'};
				} else {
					return {'text-align': 'right'};
				}
			}
		},
        {headerName: "매장코드", field: "store_cd",  pinned:'left', width: 70, cellClass: 'hd-grid-code' , hide:true},
        {headerName: "매장구분", field: "store_type",  pinned:'left', width: 70, cellClass: 'hd-grid-code', hide:true},
        {headerName: "동종업계 메모", field: "sale_memo",  pinned:'left', width: 120, cellClass: 'hd-grid-code'},
        {width: 'auto'}
    ];

    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
                if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
            },
            rollup: true,
			groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
        });

        @if($user_store != '')
            $("#store_no").select2({data:['{{ @$user_store }}']??'', tags: true});
        @endif
        Search();

        // 엑셀다운로드 레이어 오픈
        $(".export-excel").on("click", function (e) {
            depthExportChecker.Open({
                depths: ['매출월별', '매장별'],
                download: (level) => {
                    gx.Download('월별동종업계매출_{{ date('YmdH') }}.xlsx', { type: 'excel', level: level });
                }
            });
        });
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/stock/stk34/search', data, 1, function(e){
			let store_amt = e.head.store_amt;
			
			let rowData = [];
			store_amt.forEach(store => {
				let sale_amt		= store[0].store_amt;
				let store_cd		= store[0].store_cd;
				let store_nm		= store[0].store_nm;
				let sale_date		= store[0].sale_date;
				let competitor		= store[0].competitor;
				let sale_amt_off	= store[0].store_amt_off;
				let sale_amt_on		= store[0].store_amt_on;
				
				rowData.push({
					sale_date,
					store_cd,
					store_nm,
					sale_amt_off,
					sale_amt_on,
					sale_amt,
					competitor
				});
			});
			
			gx.gridOptions.api.applyTransaction({add: rowData});
            updatePinnedRow();
            setAllRowGroupExpanded($("#grid_expand").is(":checked"))
			
			let ord_field = $('#ord_field').val();
			let ord = $('input[name="ord"]:checked').val();

			gx.gridOptions.api.setSortModel([{ colId: 'sale_date', sort: 'desc' }]);
			gx.gridOptions.api.setSortModel([{ colId: 'sale_amt', sort: 'desc' }]);
        });

    }

    const initSearchInputs = () => {
        document.search.reset(); // 모든 일반 input 초기화
        $('#store_no').val(null).trigger('change'); // 브랜드 select2 박스 초기화
        location.reload();
    };


    function add() {
        const url = '/shop/stock/stk33/create';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=900");
    }

    const updatePinnedRow = () => {
        let [ sale_amt ] = [ 0 ];
        const rows = gx.getRows();

        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                sale_amt += parseInt(row?.sale_amt || 0);
            });
        }
        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, sale_amt : sale_amt }
        ]);
    };

</script>


@stop
