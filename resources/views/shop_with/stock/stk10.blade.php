@extends('shop_with.layouts.layout')
@section('title','상품입고관리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">상품입고관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>상품관리</span>
		<span>/ 상품입고관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    {{--<!-- <a href="/shop/stock/stk12" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>초도출고</a> -->--}}
					{{--<!-- <a href="/shop/stock/stk13" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>판매분출고</a> -->--}}
					<a href="/shop/stock/stk14" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
					{{--<!-- <a href="/shop/stock/stk15" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>일반출고</a> -->--}}
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
							<label for="brand_cd">전표번호</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='dc_num' id="dc_num" value=''>
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
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			{{--<!-- <a href="/shop/stock/stk12" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>초도출고</a> -->--}}
			{{--<!-- <a href="/shop/stock/stk13" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>판매분출고</a> -->--}}
			<a href="/shop/stock/stk14" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
			{{--<!-- <a href="/shop/stock/stk15" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>일반출고</a> -->--}}
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
					{{--	
						<!-- <div class="d-flex mr-1 mb-1 mb-lg-0">
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
									<option value='{{ $rel_order->code_val }}'>{{ $rel_order->code_val }}</option>
								@endforeach
							</select>
						</div>
						<a href="javascript:void(0);" onclick="receipt()" class="btn btn-sm btn-primary shadow-sm">접수</a>
						<span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
						<a href="javascript:void(0);" onclick="release()" class="btn btn-sm btn-primary shadow-sm mr-1">출고</a>
						<span class="d-none d-lg-block ml-1 mr-2 tex-secondary">|</span> -->
					--}}
						<a href="javascript:void(0);" onclick="receive()" class="btn btn-sm btn-primary shadow-sm">매장입고</a>
					{{--<!-- <a href="javascript:void(0);" onclick="reject()" class="btn btn-sm btn-primary shadow-sm">거부</a> -->--}}
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
	{{--let rel_orders = <?= json_encode(@$rel_orders) ?> ;--}}

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

	const pinnedRowData = [{ prd_cd: '합계', qty: 0 }];
	let columns = [
        {field: "idx", hide: true},
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"},
			cellRenderer: params => params.node.rowPinned == 'top' ? '' : parseInt(params.value) + 1
		},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 28, headerCheckboxSelection: true,
            checkboxSelection: function(params) {
                if (params.data.state == 30 && params.data.fin_ok == 'Y') {
                    return true;
                } else {
                    return false;
                }
            },
        },
        // 출고일자 값 : 출고상태가 요청/접수 일때 -> 출고예정일자(exp_dlv_day) | 출고상태가 출고/입고 일때 -> 출고처리일자(prc_rt)
        {field: "dlv_day", headerName: "출고일자", pinned: 'left', width: 110, cellStyle: {"text-align": "center"}, 
        cellRenderer: function(params) {
            return params.data.state > 0 ? (params.value || '') : '';
        }
        },
        {field: "rel_type",	headerName: "출고구분", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "rel_order", headerName: "출고차수", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
		{field: "document_number",	headerName: "전표번호", pinned: 'left', width: 60, cellStyle: {"text-align": "center"}},
        {field: "state", headerName: "출고상태", pinned: 'left', cellStyle: StyleReleaseState, width: 70,
            cellRenderer: function(params) {
                return rel_states[params.value];
            }
        },
        {field: "exp_dlv_day", headerName: "출고예정일자", pinned:'left', cellStyle: {"text-align": "center"}, width: 90,
           cellRenderer: function(params) {
                return params.data.exp_dlv_day_data;
           }
        },
        {field: "storage_cd",	headerName: "창고코드", pinned: 'left', width: 60, cellStyle: {"text-align": "center"}},
        {field: "storage_nm", headerName: "창고", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
		{field: "goods_no",	headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
		{field: "opt_kind_nm",	headerName: "품목", width: 80, cellStyle: {"text-align": "center"}},
		{field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
		{field: "style_no",	headerName: "스타일넘버", cellStyle: {"text-align": "center"}},
		{field: "goods_nm",	headerName: "상품명", width: 200,
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
                    return '<a href="#" onclick="return openShopProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
		{field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200,
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
                    return '<a href="#" onclick="return openShopProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "color_nm", headerName: "컬러명", width: 100, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
		{field: "store_wqty", headerName: "매장재고", width: 60, type: "currencyType"},
		{field: "qty", headerName: "수량", type: "currencyType", width: 50,
            cellRenderer: function(params) {
                if (params.data.state != 10) {
                    if (params.value !== undefined) {
						if (params.node.rowPinned != 'top') {
                        	return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + Comma(params.value) + '</a>';
						} else {
							return params.data.qty;
						}
                    }
                } else {
                    return params.data.qty;
                }
            }
        },
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
        {field: "storage_comment", headerName: "창고메모", width: 300},
        {field: "comment", headerName: "본사메모", width: 300},
        {field: "req_nm", headerName: "요청자", cellStyle: {"text-align": "center"}},
        {field: "req_rt", headerName: "요청일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "rec_nm", headerName: "접수자", cellStyle: {"text-align": "center"}},
        {field: "rec_rt", headerName: "접수일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "prc_nm", headerName: "처리자", cellStyle: {"text-align": "center"}},
        {field: "prc_rt", headerName: "처리일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "fin_nm", headerName: "완료(입고)자", cellStyle: {"text-align": "center"}},
        {field: "fin_rt", headerName: "완료(입고)일시", width: 120, cellStyle: {"text-align": "center"}},
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
			getRowStyle: function(params) {
				if (params.node.rowPinned) return { 'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
			},
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
                if (e.column.colId == "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
						updatePinnedRow();
					}
                }
            }
        }
        
        );
        @if($user_store != '')
            $("#store_no").select2({data:['{{ @$user_store }}']??'', tags: true});
        @endif
        Search();
    });
	
	const updatePinnedRow = () => {
		let [ qty ] = [ 0 ];
		const rows = gx.getRows();
		if (rows && Array.isArray(rows) && rows.length > 0) {
			rows.forEach((row, idx) => {
				qty += parseInt(row.qty);
			});
		}
		
		let pinnedRow =  gx.gridOptions.api.getPinnedTopRow(0);
		gx.gridOptions.api.setPinnedTopRowData([
			{... pinnedRow.data, qty}
		])
	}

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/shop/stock/stk10/search', data, 1, function(e) {
			let total_data = e.head.total_data;
			
			const pinnedRowData = {
				prd_cd : '합계',
				qty : total_data
			};
			
			gx.gridOptions.api.setPinnedTopRowData([pinnedRowData]);
		});
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
            url: '/shop/stock/stk10/receipt',
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
            url: '/shop/stock/stk10/release',
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
            url: '/shop/stock/stk10/receive',
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
            url: '/shop/stock/stk10/reject',
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

    function openShopProduct(prd_no){
        var url = '/shop/product/prd01/' + prd_no;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

	// 출고 거래명세서 출력
	function printDocument(document_number, idx) {
		location.href = '/shop/stock/stk10/download?document_number=' + document_number + '&idx=' + idx;
	}

	// 출고 거래명세서 일괄출력
	function printSelectedDocuments() {
		let rows = gx.getSelectedRows();
		if (rows.length < 1) return alert("일괄출력할 명세서를 선택해주세요.");

		alert("명세서를 일괄출력하고 있습니다. 잠시만 기다려주세요.");

		axios({
			url: '/shop/stock/stk10/download-multi',
			method: 'post',
			data: { data: rows.map(row => ({ document_number: row.document_number, idx: row.idx })) },
		}).then(function (res) {
			window.location = '/' + res.data.file_path;
		}).catch(function (err) {
			console.log(err);
		});
	}
</script>
@stop
