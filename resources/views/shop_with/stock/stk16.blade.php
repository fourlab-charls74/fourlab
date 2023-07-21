@extends('shop_with.layouts.layout')
@section('title','원부자재출고')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">원부자재출고</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>원부자재관리</span>
		<span>/ 원부자재출고</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="/shop/stock/stk17" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
                            <div class="form-group">
                                <label>일자검색</label>
                                <div class="d-flex">
                                    <div class="flex_box w-25 mr-2">
                                        <select name='date_type' class="form-control form-control-sm">
                                            <option value='req_rt' selected>요청일자</option>
                                            <option value='dlv_day'>출고일자</option>
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
							<label for="rel_order">출고구분</label>
                            <div class="flex_box">
                                <select name='rel_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($rel_types as $rel_type)
                                        @if ($rel_type->code_id == "R" || $rel_type->code_id == "G")
                                        <option value='{{ $rel_type->code_id }}'>{{ $rel_type->code_val }}</option>
                                        @endif
                                    @endforeach
                                </select>
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
			                <label for="prd_cd">원부자재코드</label>
			                <div class="form-inline">
				                <div class="form-inline-inner input-box w-100">
					                <div class="form-inline inline_btn_box">
						                <input type='text' id="prd_cd_sub" name='prd_cd_sub' class="form-control form-control-sm w-100 ac-style-no search-enter">
						                <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd_sub"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
					                </div>
				                </div>
			                </div>
		                </div>
	                </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_nm">원부자재명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='prd_nm' id="prd_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">구분</label>
                            <div class="flax_box">
                                <select name='type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($types as $type)
                                    <option value='{{ $type->code_id }}'>{{ $type->code_id }} : {{ $type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
	                <div class="col-lg-4 inner-td">
		                <div class="form-group">
			                <label for="opt">품목</label>
			                <div class="flax_box">
				                <select name='opt' class="form-control form-control-sm">
					                <option value=''>전체</option>
					                @foreach ($opts as $opt)
						                <option value='{{ $opt->code_id }}'>{{ $opt->code_id }} : {{ $opt->code_val }}</option>
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
                                        <option value="req_rt">출고요청일</option>
                                        <option value="p.prd_cd">상품코드</option>
                                        <option value="p.price">판매가</option>
                                        <!-- <option value="p.wonga">원가</option> -->
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
            <a href="/shop/stock/stk17" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
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
                        <a href="javascript:void(0);" onclick="receive()" class="btn btn-sm btn-primary shadow-sm">매장입고</a>
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
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellClass: 'hd-grid-code'},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29,
            checkboxSelection: function(params) {
                return params.data.state === 30;
            },
        },
        // 출고일자 값 : 출고상태가 요청/접수 일때 -> 출고예정일자(exp_dlv_day) | 출고상태가 출고/입고 일때 -> 출고처리일자(prc_rt)
        {field: "dlv_day", headerName: "출고일자", pinned: 'left', width: 110, cellClass: 'hd-grid-code', 
            cellRenderer: function(params) {
                return params.data.state > 0 ? (params.value || '') + (params.data.state < 30 ? ' (예정)' : '') : '';
            }
        },
        {field: "state", headerName: "출고상태", pinned: 'left', cellStyle: StyleReleaseState, width: 60,
            cellRenderer: function(params) {
                return rel_states[params.value];
            }
        },
        {field: "rel_type",	headerName: "출고구분", pinned: 'left', width: 60, cellClass: 'hd-grid-code'},
        {field: "store_nm",	headerName: "매장", pinned: 'left', width: 100},
        {field: "storage_nm", headerName: "창고", pinned: 'left', width: 100},
		{field: "type_nm", headerName: "구분", width: 60, cellClass: 'hd-grid-code'},
		{field: "opt", headerName: "품목", width: 80, cellClass: 'hd-grid-code'},
		{field: "img", headerName: "이미지", type: 'GoodsImageType', width: 50, surl: "{{config('shop.front_url')}}"},
		{field: "img", headerName: "이미지_url", hide: true},
        {field: "prd_cd", headerName: "바코드", width: 130, 
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return EditProduct(\'' + params.value + '\');">' + params.value + '</a>';
                }
            }
        },
		{field: "prd_nm", headerName: "원부자재명", width: 150},
		{field: "color", headerName: "컬러명", width: 80},
		{field: "size", headerName: "사이즈명", width: 80},
		{field: "unit", headerName: "단위", width: 100},
		{field: "price", headerName: "판매가", type: 'currencyType', width: 70},
		{field: "qty", headerName: "요청수량", type: "numberType", width: 60,
			cellRenderer: (params) => {
				if (params.value !== undefined) {
					return '<a href="#" onclick="return openShopStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
				}
				return params.value;
			}
		},
		{field: "rec_qty", headerName: "접수수량", type: "numberType", width: 60,
			cellRenderer: (params) => params.data.state < 0 ? '-' : params.data.state > 10 ? params.value : `<span class="text-secondary">(${params.value})</span>`,
		},
		{field: "prc_qty", headerName: "출고수량", type: "numberType", width: 60, 
			cellRenderer: (params) => params.data.state < 0 ? '-' : params.data.state > 20 ? params.value : `<span class="text-secondary">(${params.value})</span>`,
		},
        {field: "amount", headerName: "합계", type: 'currencyType', width: 80, valueGetter: (params) => calAmount(params)},
		{field: "exp_dlv_day", headerName: "출고예정일자", cellClass: 'hd-grid-code',
            // cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99", "text-align": "center"} : {"text-align": "center"};},
            // cellRenderer: (params) => {
            //         return params.data.state === 10 ? `<input type="date" class="grid-date" value="${params.value ?? ''}" onchange="selectCurRow('exp_dlv_day', this, '${params.rowIndex}')" />` : params.value;
            // }
        },
		{field: "rel_order", headerName: "출고차수", width: 150, cellClass: 'hd-grid-code',
            // editable: function(params) {return params.data.state === 10;}, 
            // cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99", "text-align": "center"} : {"text-align": "center"};},
            // cellEditorSelector: function(params) {
            //     return {
            //         component: 'agRichSelectCellEditor',
            //         params: { values: rel_orders.map(o => o.code_val) },
            //     };
            // },
            cellRenderer: function(params) {
                return params.data.state < 20 ? params.value : params.data.dlv_day?.replaceAll("-", "") + '-' + (params.value) || '' + (params.value || '');
            }
        },
        {field: "req_comment", headerName: "매장메모", width: 200},
        {field: "comment", headerName: "본사메모", width: 200},
		{field: "req_nm", headerName: "요청자", width: 80, cellClass: 'hd-grid-code'},
		{field: "req_rt", headerName: "요청일시", type: "DateTimeType"},
		{field: "rec_nm", headerName: "접수자", width: 80, cellClass: 'hd-grid-code'},
		{field: "rec_rt", headerName: "접수일시", type: "DateTimeType"},
		{field: "prc_nm", headerName: "처리자", width: 80, cellClass: 'hd-grid-code'},
		{field: "prc_rt", headerName: "처리일시", type: "DateTimeType"},
		{field: "fin_nm", headerName: "완료(입고)자", width: 80, cellClass: 'hd-grid-code'},
		{field: "fin_rt", headerName: "완료(입고)일시", type: "DateTimeType"},
		{width: 0}
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
			isRowSelectable: (params) => params.data.state === 30,
        });
        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/shop/stock/stk16/search', data, 1);
	}

    function selectCurRow(fieldName, e, rowIndex) {
        const node = gx.getRowNode(rowIndex);
        node.data[fieldName] = e.value;
        node.setDataValue(fieldName, e.value);
    }

    function EditProduct(product_code) {
        var url = '/shop/product/prd03/edit/' + product_code;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=555");
    }

    const calAmount = (params) => {
        const qty = params.data.state > 20 ? params.data.prc_qty : params.data.state > 10 ? params.data.rec_qty : params.data.qty;
        const result = parseInt(params.data.price) * parseInt(qty);
        return isNaN(result) ? 0 : result;
    };

    // 매장입고 (30 -> 40)
    function receive() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("매장입고처리할 항목을 선택해주세요.");
        if (rows.filter(r => r.state !== 30).length > 0) return alert("'출고'상태의 항목만 매장입고처리 가능합니다.");
        if (!confirm("선택한 항목을 매장입고처리하시겠습니까?")) return;
        axios({
            url: '/shop/stock/stk16/receive',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if (res.data.code === 200) {
                alert("매장입고처리가 정상적으로 완료되었습니다.");
                Search();
            } else {
                // console.log(res.data);
                alert("매장입고처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            // console.log(err);
        });
    }

</script>
@stop
