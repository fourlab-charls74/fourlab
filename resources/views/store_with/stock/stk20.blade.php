@extends('store_with.layouts.layout')
@section('title','매장RT')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장RT관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 매장관리</span>
		<span>/ 매장RT관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="/store/stock/stk21" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>본사요청RT</a>
                    <a href="/store/stock/stk22" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>매장요청RT</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- {{-- <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a> --}} -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
                <input type='hidden' name='goods_nos' value='' />
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>RT구분</label>
                            <div class="flex_box">
                                <select name='rt_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value='G'>매장요청RT</option>
                                    <option value='R'>본사요청RT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>보내는 매장</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="send_store_nm" name="send_store_nm">
                                <select id="send_store_no" name="send_store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-send-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>받는 매장</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
                            <div class="form-group">
                                <label>일자검색</label>
                                <div class="d-flex">
                                    <div class="flex_box w-25 mr-2">
                                        <select name='rt_date_stat' class="form-control form-control-sm">
                                            @foreach ($rt_states as $key => $value)
                                                @if($key == '-10') @continue @endif
                                                <option value='{{ $key }}'>{{ $value }}일자</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline date-select-inbox w-75">
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
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>RT상태</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <select name='rt_stat' class="form-control form-control-sm mr-2">
                                    <option value=''>전체</option>
                                    @foreach ($rt_states as $key => $value)
                                        <option value='{{ $key }}'>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div class="custom-control custom-checkbox form-check-box" style="min-width: 100px;">
                                    <input type="checkbox" class="custom-control-input" name="ext_done_state" id="ext_done_state" value="Y">
                                    <label class="custom-control-label font-weight-normal" for="ext_done_state">완료상태 제외</label>
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
                            <label for="prd_cd">상품검색조건</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
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
                                        <option value="psr.req_rt">RT요청일</option>
                                        <option value="g.goods_no">온라인코드</option>
                                        <option value="psr.prd_cd">바코드</option>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">공급업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_cd" name="com_cd" />
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter ac-company sch-sup-company" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row search-area-ext d-none">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목 (온라인)</label>
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
                </div>
            </div>
		</div>

        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/store/stock/stk21" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>본사요청RT</a>
            <a href="/store/stock/stk22" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>매장요청RT</a>
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
                        {{--<div class="d-flex mr-2 mb-1 mb-lg-0">
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
                        </div>--}}
{{--						<div class="d-flex mr-2 mb-1 mb-lg-0">--}}
{{--							<select id="update_state_select" name="update_state_select" class="form-control form-control-sm" style="width: 150px;">--}}
{{--								<option value="10">RT요청(원복)</option>--}}
{{--							</select>--}}
{{--						</div>--}}
{{--                        <a href="javascript:void(0);" onclick="updateState()" class="btn btn-sm btn-primary shadow-sm">RT상태변경</a>--}}
{{--						<span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>--}}
                        <a href="javascript:void(0);" onclick="receipt()" class="btn btn-sm btn-primary shadow-sm">접수</a>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
                        <a href="javascript:void(0);" onclick="release()" class="btn btn-sm btn-primary shadow-sm mr-1">RT처리중</a>
                        <a href="javascript:void(0);" onclick="receive()" class="btn btn-sm btn-primary shadow-sm mr-1">RT완료</a>
                        <a href="javascript:void(0);" onclick="reject()" class="btn btn-sm btn-primary shadow-sm">거부</a>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
                        <a href="javascript:void(0);" onclick="remove()" class="btn btn-sm btn-outline-primary shadow-sm">삭제</a>
                    </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<!-- script -->
@include('store_with.stock.stk20_js')
<!-- script -->
<script language="javascript">
    let rt_states = <?= json_encode(@$rt_states) ?> ;

    function StyleRtType(params) {
        let state = {
            "G":"#0000ff", // 일반RT
            "R":"#e8554e", // 요청RT
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                return {
                    'color': state[params.value],
                    'text-align': 'center'
                }
            }
        }
    }

    function StyleReleaseState(params) {
        let state = {
            "10":"#222222", // 요청
            "20":"#e8554e", // 접수
            "30":"#0000ff", // 처리
            "40":"#2aa876", // 완료
            "-10":"#666666", // 거부
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                return {
                    'color': state[params.value],
                    'text-align': 'center'
                }
            }
        }
    }

	const pinnedRowData = [{ dep_store_cd : "합계", qty : 0 }];
	
	let columns = [
        {field: "idx", hide: true},
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"},
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
		},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 28,
            checkboxSelection: function(params) {
                // return params.data.state < 40 && params.data.state > 0;
                return params.data.state < 40;
            },
        },
        {field: "type", headerName: "RT구분", pinned: 'left', cellStyle: StyleRtType,
            cellRenderer: function(params) {
                return params.value === 'R' ? '본사요청RT' : params.value === 'G' ? '매장요청RT' : '';
            }
        },
        {field: "state", headerName: "RT상태", pinned: 'left', cellStyle: StyleReleaseState,
            cellRenderer: function(params) {
                return rt_states[params.value];
            }
        },
        {field: "dep_store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 150},
        {field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 150},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 130, cellStyle: {"text-align": "center"}},
        {field: "goods_no",	headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", width: 150,
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 비어있는 상품입니다.`);">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 150},
        {field: "prd_cd_p",	headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color",	headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size",	headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 150},
        {field: "goods_sh", headerName: "정상가", width: 60, type: "currencyType"},
        {field: "price", headerName: "현재가", width: 60, type: "currencyType"},
        {field: "qty", headerName: "수량", type: "currencyType", width: 60, cellStyle: {"font-weight": "700"},
            cellRenderer: function(params) {
					if (params.node.rowPinned === 'top') {
						return params.value ?? 0;
					} else {
                        return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                    }
            }
        },
        {field: "req_rt", headerName: "요청일시", type: "DateTimeType"},
        {field: "rec_rt", headerName: "접수일시", type: "DateTimeType"},
        {field: "prc_rt", headerName: "처리일시", type: "DateTimeType"},
        {field: "fin_rt", headerName: "완료일시", type: "DateTimeType"},
        {field: "req_comment", headerName: "요청메모", width: 300},
        {field: "rec_comment", headerName: "접수메모", width: 300,
            editable: function(params) {return params.data.state === 10;},
            cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99"} : {};}
        },
        {field: "del_rt", headerName: "RT 삭제", cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
				if (params.node.rowPinned === 'top') {
					return '';
				} else {
					if(params.data.state != 40) {
						return `<a href="javascript:void(0);" onclick="remove(${params.data.idx}, ${params.data.state})" style="color:#ff4444;">삭제</a>`;
					} else{
						return '-';
					}
				}
               
            }
        },
		{field: "document_number",	headerName: "전표번호", width: 60, cellStyle: {"text-align": "center"}},
		{field: "print", headerName: "전표 출력", cellStyle: {"text-align": "center", "color": "#4444ff", "font-size": '13px'},
            cellRenderer: function(params) {
                if(params.data.state > 10) {
                    return `<a href="javascript:void(0);" style="color: inherit;" onclick="printRT(${params.data.document_number}, ${params.data.idx})">출력</a>`;
                } else{
                    return '-';
                }
            }
        },
	];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd", height: 265 });

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
			getRowStyle : (params) => {
				if (params.node.rowPinned) return { "font-weight": "bold", 'background': '#eee', "border": 'none' };
			},
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
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk20/search', data, -1, function(d) {
			let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
			let total_data = d.head.total_data;
			if (pinnedRow && total_data != '') {
				gx.gridOptions.api.setPinnedTopRowData([
					{...pinnedRow.data, ...total_data}
				])
			}
		});
	}

    // 접수 (10 -> 20)
    function receipt() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("접수처리할 항목을 선택해주세요.");
        if(rows.filter(r => r.state !== 10).length > 0) return alert("'요청'상태의 항목만 접수처리 가능합니다.");
        if(!confirm("선택한 항목을 접수처리하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk20/receipt',
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
            url: '/store/stock/stk20/release',
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
            url: '/store/stock/stk20/receive',
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
        if(rows.filter(r => !r.rec_comment).length > 0) return alert("'접수메모'에 거부사유를 반드시 입력해주세요.");
        if(!confirm("선택한 항목을 거부처리하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk20/reject',
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

    // 삭제 (RT 삭제)
    function remove(idx, state) {
        let rows;
        if(!idx) {
            rows = gx.getSelectedRows();
            if(rows.length < 1) {
                return alert("삭제할 RT를 선택해주세요.");
            }
            if(rows.filter(r => (r.state == 40)).length > 0) {
                return alert("'RT완료' 상태에서는 삭제를 할 수 없습니다.");
            }
        } else{
            rows = [{idx, state}];
        }
        if(!confirm("선택한 항목을 삭제하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk20',
            method: 'delete',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("RT삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
	
	// RT요청상태로 원복 (20 -> 10)
	function updateState() {
		let update_state = $('#update_state_select').val();
		let rows = gx.getSelectedRows();
		if(rows.length < 1) return alert("RT요청상태로 원복하려는 RT를 선택해주세요.");
		if(rows.filter(r => r.state !== 20).length > 0) return alert("'RT접수'상태의 항목만 RT요청상태로 원복 가능합니다.");

		if(!confirm("선택한 RT를 RT요청상태로 원복하시겠습니까?")) return;

		axios({
			url: '/store/stock/stk20/update-state',
			method: 'post',
			data: {data: rows},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				Search();
			} else {
				console.log(res.data);
				alert("원복 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}
	
	// RT전표 출력
    function printRT(document_number, idx) {
		location.href = '/store/stock/stk20/download?document_number=' + document_number + '&idx=' + idx;
    }

    function openApi() {
        document.getElementsByClassName('sch-prdcd-range')[0].click();
    }
</script>
@stop
