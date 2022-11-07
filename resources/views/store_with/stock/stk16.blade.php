@extends('store_with.layouts.layout')
@section('title','원부자재 출고')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">원부자재 출고</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 원부자재 출고</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="/store/stock/stk17" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
                    <a href="/store/stock/stk18" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>일반출고</a>
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
							<label for="rel_order">출고구분/차수</label>
                            <div class="flex_box">
                                <select name='rel_type' class="form-control form-control-sm" style="width: 47%;">
                                    <option value=''>전체</option>
                                    @foreach ($rel_types as $rel_type)
                                        @if ($rel_type->code_id == "R" || $rel_type->code_id == "G")
                                        <option value='{{ $rel_type->code_id }}'>{{ $rel_type->code_val }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <span class="text_line" style="width: 6%; text-align: center;">/</span>
                                <select name='rel_order' class="form-control form-control-sm" style="width: 47%">
                                    <option value=''>전체</option>
                                    @foreach ($rel_orders as $rel_order)
                                        <option value='{{ $rel_order->code_id }}'>{{ $rel_order->code_val }}</option>
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
                                    <input type="checkbox" class="custom-control-input" name="ext_done_state" id="ext_done_state" value="Y">
                                    <label class="custom-control-label font-weight-normal" for="ext_done_state">매장입고완료 제외</label>
                                </div>
                            </div>
						</div>
					</div>
				</div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="store_type">매장구분</label>
                            <div class="flex_box">
                                <select name='store_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($store_types as $store_type)
                                        <option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="store_no">매장</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
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
                </div>
                <div class="row search-area-ext d-none">
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
                </div>
                <div class="row">
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
                                        <option value="p.wonga">원가</option>
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
            <a href="/store/stock/stk17" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>요청분출고</a>
            <a href="/store/stock/stk18" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>일반출고</a>
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
                                @foreach ($rel_orders as $rel_order)
                                    <option value='{{ $rel_order->code_id }}'>{{ $rel_order->code_val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="javascript:void(0);" onclick="receipt()" class="btn btn-sm btn-primary shadow-sm">접수</a>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span>
                        <a href="javascript:void(0);" onclick="release()" class="btn btn-sm btn-primary shadow-sm mr-1">출고</a>
                        <a href="javascript:void(0);" onclick="receive()" class="btn btn-sm btn-primary shadow-sm mr-1">매장입고</a>
                        <a href="javascript:void(0);" onclick="reject()" class="btn btn-sm btn-primary shadow-sm">거부</a>
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

    const DEFAULT = { lineHeight : "30px" };

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
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 28,
            checkboxSelection: function(params) {
                return params.data.state < 40 && params.data.state > 0;
            },
        },
        // 출고일자 값 : 출고상태가 요청/접수 일때 -> 출고예정일자(exp_dlv_day) | 출고상태가 출고/입고 일때 -> 출고처리일자(prc_rt)
        {field: "dlv_day", headerName: "출고일자", pinned: 'left', width: 110, cellStyle: {"text-align": "center"}, 
            cellRenderer: function(params) {
                return params.data.state > 0 ? (params.value || '') + (params.data.state < 30 ? ' (예정)' : '') : '';
            }
        },
        {field: "state", headerName: "출고상태", pinned: 'left', cellStyle: StyleReleaseState,
            cellRenderer: function(params) {
                return rel_states[params.value];
            }
        },
        {field: "rel_type",	headerName: "출고구분", pinned: 'left', width: 80, cellStyle: {"text-align": "center"}},
        {field: "store_nm",	headerName: "매장", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
        {field: "storage_nm", headerName: "창고", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
        {
            field: "type_nm",
            headerName: "구분",
            width: 70,
            cellStyle: DEFAULT
        },
        {
            field: "opt",
            headerName: "품목",
            width: 70,
            cellStyle: DEFAULT
        },
        {field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: DEFAULT, surl:"{{config('shop.front_url')}}"},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "prd_cd", headerName: "상품코드", width:120, cellStyle: DEFAULT,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return EditProduct(\'' + params.value + '\');">' + params.value + '</a>';
                }
            }
        },
        {field: "prd_nm", headerName: "원부자재명", width: 120},
        {
            field: "color",
            headerName: "칼라",
            cellStyle: DEFAULT,
            width: 100
        },
        {
            field: "size",
            headerName: "사이즈",
            cellStyle: DEFAULT,
            width: 80
        },
        {
            field: "unit",
            headerName: "단위",
            cellStyle: DEFAULT,
            width: 120
        },
        {
            field: "price",
            headerName: "판매가",
            type: 'currencyType',
            cellStyle: DEFAULT,
            width: 80
        },
        {
            field: "wonga",
            headerName: "원가",
            type: 'currencyType',
            cellStyle: DEFAULT,
            width: 80
        },
		{field: "qty", headerName: "수량", type: "numberType",
            editable: function(params) {return params.data.state === 10;}, 
            cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99"} : {};},
            cellRenderer: function(params) {
                if (params.data.state != 10) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                    }
                } else {
                    return params.data.qty
                }
            }
        },
        {field: "origin_qty", headerName: "수량", type: "numberType", hide: true},
        {field: "amount", headerName: "합계", type: 'currencyType', width: 80, valueGetter: (params) => calAmount(params)},
		{field: "exp_dlv_day", headerName: "출고예정일자", cellStyle: {"text-align": "center"},
            // cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99", "text-align": "center"} : {"text-align": "center"};},
            // cellRenderer: (params) => {
            //         return params.data.state === 10 ? `<input type="date" class="grid-date" value="${params.value ?? ''}" onchange="selectCurRow('exp_dlv_day', this, '${params.rowIndex}')" />` : params.value;
            // }
        },
		{field: "rel_order", headerName: "출고차수", width: 100, 
            // editable: function(params) {return params.data.state === 10;}, 
            // cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99", "text-align": "center"} : {"text-align": "center"};},
            // cellEditorSelector: function(params) {
            //     return {
            //         component: 'agRichSelectCellEditor',
            //         params: { values: rel_orders.map(o => o.code_val) },
            //     };
            // },
            cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return params.data.state === 10 ? params.value : params.data.dlv_day?.replaceAll("-", "") + (params.value) || '' + (params.value || '');
            }
        },
        {field: "req_id", headerName: "요청자", cellStyle: {"text-align": "center"}},
        {field: "req_rt", headerName: "요청일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "rec_id", headerName: "접수자", cellStyle: {"text-align": "center"}},
        {field: "rec_rt", headerName: "접수일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "prc_id", headerName: "처리자", cellStyle: {"text-align": "center"}},
        {field: "prc_rt", headerName: "처리일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "fin_id", headerName: "완료(입고)자", cellStyle: {"text-align": "center"}},
        {field: "fin_rt", headerName: "완료(입고)일시", width: 120, cellStyle: {"text-align": "center"}},
        {field: "comment", headerName: "메모", width: 300, 
            editable: function(params) {return params.data.state === 10;}, 
            cellStyle: function(params) {return params.data.state === 10 ? {"background-color": "#ffFF99"} : {};}
        },
        {width: 'auto'}
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
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk16/search', data, 1);
	}

    function selectCurRow(fieldName, e, rowIndex) {
        const node = gx.getRowNode(rowIndex);
        node.data[fieldName] = e.value;
        node.setDataValue(fieldName, e.value);
    }

    function EditProduct(product_code) {
        var url = '/store/product/prd03/edit/' + product_code;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=555");
    }

    const calAmount = (params) => {
        const row = params.data;
        const result = parseInt(row.price) * parseInt(row.qty);
        return isNaN(result) ? 0 : result;
    };

    // 접수 (10 -> 20)
    function receipt() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("접수처리할 항목을 선택해주세요.");
        if (rows.filter(r => r.state !== 10).length > 0) return alert("'요청'상태의 항목만 접수처리 가능합니다.");
        if (!confirm("선택한 항목을 접수처리하시겠습니까?")) return;
        axios({
            url: '/store/stock/stk16/receipt',
            method: 'post',
            data: {
                data: rows, 
                exp_dlv_day: $("[name=exp_dlv_day]").val(), 
                rel_order: $("[name=exp_rel_order]").val(),
            },
        }).then(function (res) {
            if (res.data.code === 200) {
                alert("접수처리가 정상적으로 완료되었습니다.");
                Search();
            } else if (res.data.code == -1) {
                const prd_cd = res.data.prd_cd;
                alert(`상품 코드 - ${prd_cd}\n창고재고가 입력하신 수량보다 적은 경우 접수처리가 불가능합니다.\n창고재고를 다시 확인해주세요.`);
            } else {
                // console.log(res.data);
                alert("접수처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            // console.log(err);
        });
    }

    // 출고 (20 -> 30)
    function release() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("출고처리할 항목을 선택해주세요.");
        if (rows.filter(r => r.state !== 20).length > 0) return alert("'접수'상태의 항목만 출고처리 가능합니다.");
        if (!confirm("선택한 항목을 출고처리하시겠습니까?")) return;
        axios({
            url: '/store/stock/stk16/release',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if (res.data.code === 200) {
                alert("출고처리가 정상적으로 완료되었습니다.");
                Search();
            } else {
                // console.log(res.data);
                alert("출고처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            // console.log(err);
        });
    }

    // 매장입고 (30 -> 40)
    function receive() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("매장입고처리할 항목을 선택해주세요.");
        if (rows.filter(r => r.state !== 30).length > 0) return alert("'출고'상태의 항목만 매장입고처리 가능합니다.");
        if (!confirm("선택한 항목을 매장입고처리하시겠습니까?")) return;
        axios({
            url: '/store/stock/stk16/receive',
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

    // 거부 (10 -> -10)
    function reject() {
        let rows = gx.getSelectedRows();
        if (rows.length < 1) return alert("거부처리할 항목을 선택해주세요.");
        if (rows.filter(r => r.state !== 10).length > 0) return alert("'요청'상태의 항목만 거부처리 가능합니다.");
        if (rows.filter(r => !r.comment).length > 0) return alert("'메모'에 거부사유를 반드시 입력해주세요.");
        if (!confirm("선택한 항목을 거부처리하시겠습니까?")) return;
        axios({
            url: '/store/stock/stk16/reject',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if (res.data.code === 200) {
                alert("거부처리가 정상적으로 완료되었습니다.");
                Search();
            } else {
                // console.log(res.data);
                alert("거부처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            // console.log(err);
        });
    }

</script>
@stop
