@extends('store_with.layouts.layout')
@section('title','요청RT')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">요청RT</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 매장RT</span>
		<span>/ 요청RT</span>
	</div>
</div>

<style>
    .hide-element-title {position: relative; top: -80px;}
</style>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a>
                    <a href="/store/stock/stk20" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 매장RT 리스트</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

            <input type='hidden' name='goods_nos' value='' />
			<div class="card-body">
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm w-100 ac-style-no search-enter">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품번호</label>
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
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_stat">상품상태</label>
                            <div class="flax_box">
                                <select name="goods_stat[]" id="goods_stat" class="form-control form-control-sm multi_select w-100" multiple>
                                    <option value=''>전체</option>
                                    @foreach ($goods_stats as $goods_stat)
                                        <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
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
                </div>
                <div class="row">
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
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                                        <option value="goods_no">상품번호</option>
                                        <option value="prd_cd">상품코드</option>
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
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a>
            <a href="/store/stock/stk20" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 매장RT 리스트</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

	</div>
</form>

<div class="row show_layout mb-3">
    <div class="col-lg-4 pr-1">
        <div class="card shadow mb-0 pt-2 pt-sm-0">
            <div class="card-title">
                <div class="filter_wrap mt-2 pt-2">
                    <div class="d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-product-total" class="text-primary">0</span>건</h6>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd-product" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow mb-0 pt-2 pt-sm-0">
            <div class="card-title">
                <div class="filter_wrap mt-2 pt-2">
                    <div class="d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-stock-total" class="text-primary">0</span>건 <strong id="selected_prd_nm" class="ml-2 fs-14" style="font-weight: 500; color: blue;"></strong></h6>
                        <div class="d-flex">
                            <select id="store_type" name="store_type" class="form-control form-control-sm mr-2" style="width:140px;">
                                <option value="">전체</option>
                                    @foreach ($store_types as $store_type)
                                        <option value='{{ $store_type->code_id }}' @if($store_type->code_id == '08') selected @endif>{{ $store_type->code_val }}</option>
                                    @endforeach
                            </select>
                            <a href="javascript:void(0);" onclick="AddRTToFinalTable()" class="btn btn-sm btn-outline-primary shadow-sm">RT리스트에 등록</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd-stock" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<div id="final_table_area" class="hide-element-title"></div>

<!-- RT 최종 등록 테이블 -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
    <div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-rt-total" class="text-primary">0</span>건</h6>
                    <div class="d-flex">
                        <a href="javascript:void(0);" onclick="RequestRT()" class="btn btn-sm btn-primary shadow-sm mr-2">RT등록</a>
                        <a href="javascript:void(0);" onclick="DeleteRows()" class="btn btn-sm btn-outline-primary shadow-sm">삭제</a>
                    </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd-rt" class="ag-theme-balham" style="min-height: 300px;"></div>
		</div>
	</div>
</div>

<script language="javascript">
    let product_columns = [
        {field: "idx", hide: true},
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 110, cellStyle: {"text-align": "center"},
            cellRenderer: (params) => `<a href="javascript:void(0);" onclick="SearchStock('${params.rowIndex}')">${params.value}</a>`,
        },
        {field: "goods_nm",	headerName: "상품명", pinned: 'left', type: 'HeadGoodsNameType', width: 150},
        {field: "goods_opt", headerName: "옵션", pinned: 'left', width: 120},
        {field: "goods_no",	headerName: "상품번호", width: 60, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 60, cellStyle: {"text-align": "center"}},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 250, cellStyle: {"line-height": "30px"}},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 60},
        {field: "price", headerName: "판매가", type: "currencyType", width: 60},
	];

    const stores = <?= json_encode(@$stores) ?> ;

    let stock_columns = [
        {field: "prd_cd", hide: true},
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 30, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 28},
        {field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 140},
        {field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 140, editable: true, 
            cellStyle: {"background-color": "#ffFF99"},
            cellEditorSelector: function(params) {
                return {
                    component: 'agRichSelectCellEditor',
                    params: { 
                        values: stores.map(s => s.store_nm)
                    },
                };
            },
        },
        {field: "store_cd", hide: true},
        {headerName: "창고재고",
            children: [
                {field: "storage_qty", headerName: "재고", type: "currencyType", width: 60, 
                    cellRenderer: function(params) {
                        if (params.value !== undefined) {
                            return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                        }
                    }
                },
                {field: "storage_wqty", headerName: "보유재고", type: "currencyType", width: 60,
                    cellRenderer: function(params) {
                        if (params.value !== undefined) {
                            return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                        }
                    }
                },
            ]
        },
        {headerName: "매장재고",
            children: [
                {field: "qty", headerName: "재고", type: "currencyType", width: 60,
                    cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                    }
            }
                },
                {field: "wqty", headerName: "보유재고", type: "currencyType", width: 60,
                    cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                    }
            }
                },
            ]
        },
        {field: "rt_qty", headerName: "RT수량", type: "numberType", editable: true, cellStyle: {"background-color": "#ffFF99"}},
        {field: "comment", headerName: "메모", width: 200, editable: true, cellStyle: {"background-color": "#ffFF99"}},
        {width: 'auto'}
    ];

    let rt_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 28},
        {field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 140},
        {field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 140},
        {field: "rt_qty", headerName: "RT수량", type: "numberType", pinned: 'left', cellStyle: {"font-weight": "700"}},
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no",	headerName: "상품번호", pinned: 'left', width: 60, cellStyle: {"text-align": "center"}},
        {field: "goods_type_nm", headerName: "상품구분", width: 60, cellStyle: StyleGoodsType},
        {field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: {"text-align": "center"}},
        {field: "brand_nm", headerName: "브랜드", width: 60, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 60, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 250},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 250, cellStyle: {"line-height": "30px"}},
        {field: "sale_stat_cl", headerName: "상품상태", cellStyle: StyleGoodsState},
        {field: "goods_opt", headerName: "옵션", width: 200},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 60},
        {field: "price", headerName: "판매가", type: "currencyType", width: 60},
        {field: "wonga", headerName: "원가", type: "currencyType", width: 60},
        {field: "comment", headerName: "메모", width: 200},
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx, gx2, gx3;
    const pApp = new App('', { gridId: "#div-gd-product" });
    const pApp2 = new App('', { gridId: "#div-gd-stock" });
    const pApp3 = new App('', { gridId: "#div-gd-rt" });
    let selected_prd = {};

    $(document).ready(function() {
        pApp.ResizeGrid(275, 350);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, product_columns);

        pApp2.ResizeGrid(275, 350);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, stock_columns, {
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
                if (e.column.colId == "rt_qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx2.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    }
                }
                if (e.column.colId == "store_nm") {
                    let arr = stores.filter(s => s.store_nm === e.newValue);
                    if(arr.length > 0) {
                        e.data.store_cd = arr[0].store_cd;
                    }
                }
            }
        });

        pApp3.ResizeGrid(705);
        pApp3.BindSearchEnter();
        let gridDiv3 = document.querySelector(pApp3.options.gridId);
        gx3 = new HDGrid(gridDiv3, rt_columns);

        $("[name=store_type]").on("change", function(e) {
            SearchStock();
        })
    });

    // 상품검색
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk21/search-goods', data, 1);
	}

    // 매장/창고별 재고검색
    function SearchStock(rowIndex = '') {
        if(rowIndex !== '') {
            selected_prd = gx.gridOptions.api.getDisplayedRowAtIndex(rowIndex).data;
        }
        if(!selected_prd.prd_cd) return alert("좌측에서 상품을 선택해주세요.");

        let store_type = $("[name=store_type]").val();
        let data = 'prd_cd=' + selected_prd.prd_cd + "&store_type=" + store_type;
		gx2.Request('/store/stock/stk21/search-stock', data, -1, function(d) {
            $("#selected_prd_nm").html(`[${selected_prd.prd_cd}] ${selected_prd.goods_nm}`);
        });
    }

    // 최종RT리스트에 등록
    function AddRTToFinalTable() {
        let rows = gx2.getSelectedRows();
        if(rows.length < 1) return alert("RT리스트에 등록할 항목을 선택해주세요.");
        if(rows.filter(r => !r.rt_qty || !r.rt_qty.trim() || r.rt_qty == 0 || isNaN(parseInt(r.rt_qty))).length > 0) 
            return alert("선택한 항목의 RT수량을 입력해주세요.");
        if(rows.filter(r => !r.store_cd).length > 0) 
            return alert("선택한 항목의 받는 매장을 선택해주세요.");

        let over_qty_rows = rows.filter(row => {
            if(row.wqty !== null) {
                if(row.wqty < parseInt(row.rt_qty)) return true;
                else return false;
            }
            return true; // 상품재고가 없는경우
        });
        if(over_qty_rows.length > 0) return alert(`보내는 매장의 보유재고보다 많은 수량을 요청하실 수 없습니다.\n보내는 매장명 : ${over_qty_rows.map(o => o.dep_store_nm).join(", ")}`);

        let same_item_rows = gx3.getRows().filter(final => {
            if(rows.filter(row => row.prd_cd === final.prd_cd && row.store_cd === final.store_cd && row.dep_store_cd === final.dep_store_cd).length > 0) return true;
            return false;
        });
        if(same_item_rows.length > 0) return alert('이미 등록된 항목입니다.');


        rows = rows.map(r => ({...selected_prd, ...r, comment: r.comment ?? ''}));
        gx3.gridOptions.api.updateRowData({ add: rows });
        document.getElementById("final_table_area").scrollIntoView({ behavior: "smooth" });
    }

    // 최종RT 리스트 중 선택항목 삭제
    function DeleteRows() {
        let rows = gx3.getSelectedRows();
        if(rows.length < 1) return alert("삭제할 항목을 선택해주세요.");
        if(!confirm("선택한 항목을 삭제하시겠습니까?")) return;

        gx3.gridOptions.api.updateRowData({ remove: rows });
    }

    // RT 요청
    function RequestRT() {
        let rows = gx3.getSelectedRows();
        if(rows.length < 1) return alert("RT등록할 항목을 선택해주세요.");
        if(!confirm("선택한 항목을 RT등록하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk21/request-rt',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                location.href = "/store/stock/stk20";
            } else {
                console.log(res.data);
                alert("RT등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop
