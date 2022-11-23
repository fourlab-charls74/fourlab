@extends('store_with.layouts.layout-nav')
@section('title', '옵션별 재고현황')
@section('content')
<div class="py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">옵션별 재고현황</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품재고관리</span>
                <span>/ 옵션별 재고현황</span>
            </div>
        </div>
    </div>

    {{-- 상품정보 --}}
    {{-- <div class="show_layout mb-4">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#">상품정보</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="120px"/>
                                <col width="20%"/>
                                <col width="30%"/>
                                <col width="20%"/>
                                <col width="30%"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td rowspan="3" class="img_box brln">
                                    @if (@$prd->img !== null)
                                    <img class="goods_img" src="{{config('shop.image_svr')}}/{{@$prd->img}}" alt="이미지" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;" />
                                    @else
                                    <p class="d-flex align-items-center justify-content-center" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;">이미지 없음</p>
                                    @endif
                                </td>
                                <th>코드일련</th>
                                <td>{{ @$prd->prd_cd_p }}</td>
                                <th>상품번호</th>
                                <td>{{ @$prd->goods_no }}</td>
                            </tr>
                            <tr>
                                <th>스타일넘버</th>
                                <td>{{ @$prd->style_no }}</td>
                                <th>공급처</th>
                                <td>{{ @$prd->com_nm }}</td>
                            </tr>
                            <tr>
                                <th>품목</th>
                                <td>{{ @$prd->opt_kind_nm }}</td>
                                <th>브랜드</th>
                                <td>{{ @$prd->brand_nm }}</td>
                            </tr>
                            <tr>
                                <th>상품명</th>
                                <td colspan="2">{{ @$prd->goods_nm }}</td>
                                <th>상품명(영문)</th>
                                <td>{{ @$prd->goods_nm_eng }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- 검색 --}}
    <div id="search-area" class="search_cum_form mb-2">
        <form name="search" method="get">
            <div class="card">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label>조회일자</label>
                                <div class="form-inline date-select-inbox">
                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off" disable>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="store_cd">코드일련</label>
                                <div class="form-inline">
                                    <input type="text" class="form-control form-control-sm w-100" name="prd_cd_p" id="prd_cd_p" value="{{ @$prd_cd_p }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">매장구분</label>
                                <div class="flax_box">
                                    <select name='store_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach (@$store_types as $store_type)
                                            <option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">컬러</label>
                                <div class="flax_box">
                                    <select name='color' class="form-control form-control-sm">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- 창고/매장 재고현황 --}}
    <div class="show_layout">
        <div class="card shadow">
            <div class="card-header mb-0">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <a href="#" class="m-0 font-weight-bold">재고현황</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <h6 class="fs-16">[ 창고 옵션별 기간재고 ]</h6>
                <div class="table-responsive mb-1">
                    <div id="div-gd-storage-stock" class="ag-theme-balham"></div>
                </div>
                <h6 class="fs-16 mt-3">[ 매장 옵션별 기간재고 ]</h6>
                <div class="table-responsive">
                    <div id="div-gd-store-stock" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ag-row-level-1 {
		background-color: #edf4fd !important;
	}
</style>

<script language="javascript">
    const setting_color = '{{ @$color }}';
    const setting_size = '{{ @$size }}';

    let AlignCenter = {"text-align": "center"};

    let storage_columns = [
        {field: "storage_cd", hide: true},
        {field: "storage_nm", headerName: "창고명", width: 180, cellStyle: AlignCenter, pinned: "left", rowGroup: true, hide: true},
        {field: "color", headerName: "컬러", width: 60, cellStyle: AlignCenter, pinned: "left"},
    ];

    let store_columns = [
        {field: "color", headerName: "컬러", width: 60, cellStyle: AlignCenter, pinned: "left", rowGroup: true, hide: true},
        {field: "store_cd", hide: true},
        {field: "store_nm", headerName: "매장명", width: 180, pinned: "left"},
    ];
</script>
<script>
    const pApp = new App('', { gridId: "#div-gd-storage-stock" });
	let gx;
    const pApp2 = new App('', { gridId: "#div-gd-store-stock" });
	let gx2;

    const basic_autoGroupColumnDef = (headerName, width = 80) => ({
		headerName: headerName,
		headerClass: 'bizest',
		minWidth: width,
		maxWidth: width,
		cellRenderer: 'agGroupCellRenderer',
		pinned: 'left'
	});

	$(document).ready(async function() {
        pApp.ResizeGrid(275, 152);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        if (gridDiv !== null) {
            gx = new HDGrid(gridDiv, storage_columns, {
                autoGroupColumnDef: basic_autoGroupColumnDef('창고명', 180),
                groupDefaultExpanded: 0, // 0: close, 1: open
                suppressAggFuncInHeader: true,
                animateRows: true,
                suppressMakeColumnVisibleAfterUnGroup: true,
            });
        }

        pApp2.ResizeGrid(275, 315);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        if (gridDiv2 !== null) {
            gx2 = new HDGrid(gridDiv2, store_columns, {
                autoGroupColumnDef: basic_autoGroupColumnDef('컬러'),
                groupDefaultExpanded: 1, // 0: close, 1: open
                suppressAggFuncInHeader: true,
                animateRows: true,
                suppressMakeColumnVisibleAfterUnGroup: true,
            });
        }

        $("[name=prd_cd_p]").on("change", function(e) {
            getColors();
        });

        await getColors(setting_color);
        Search();
	});

    async function getColors(color = '') {
        let prd_cd_p = $("[name=prd_cd_p]").val();
        let { data, status } = await axios({ url: "/store/api/product/color?prd_cd_p=" + prd_cd_p, method: "get" });
        
        if (status === 200) {
            $("[name=color]").find("option").remove();
            $("[name=color]").append(`<option value="">전체</option>`);
            for (let opt of data.colors) {
                $("[name=color]").append(`<option value="${opt.color}" ${color === opt.color ? 'selected' : ''}>[ ${opt.color} ] ${opt.color_nm}</option>`);
            }
        }
    }

    async function Search() {
        resetGrid();
        let params = $("form[name=search]").serialize();
        let { data, status } = await axios({ url: "/store/product/prd04/stock/search?" + params, method: "get" });
        if (status === 200) {
            const sizes = data.data.sizes;
            await setColumns(sizes);
            gx.gridOptions.api.applyTransaction({ add: data.data.storages });
            gx2.gridOptions.api.applyTransaction({ add: data.data.stores });
        }
    }

    function setColumns(sizes) {
        storage_columns.splice(3);
        store_columns.splice(3);

        let list = [];
        for(let size of sizes) {
            let ss = size.replaceAll(".", "");
            list.push({
                headerName: size,
                children: [
                    {field: ss + "_qty", headerName: "실재고", maxWidth: 60, minWidth: 60, 
                        cellStyle: (params) => ({"text-align": "right", "background-color": size === setting_size ? "#AAFF99" : "none"}),
                        aggFunc: (params) => params.values.reduce((a,c) => a + (c * 1), 0),
                        cellRenderer: (params) => {
                            if (params.data !== undefined) {
                                return params.data[ss + "_qty"] < 1 ? '' : params.data[ss + "_qty"];
                            }
                            else return params.value || 0;
                        },
                    },
                    {field: ss + "_wqty", headerName: "보유재고", maxWidth: 65, minWidth: 65,
                        cellStyle: (params) => ({"text-align": "right", "background-color": size === setting_size ? "#AAFF99" : "none"}),
                        aggFunc: (params) => params.values.reduce((a,c) => a + (c * 1), 0),
                        cellRenderer: (params) => {
                            if (params.data !== undefined) {
                                return params.data[ss + "_wqty"] < 1 ? '' : params.data[ss + "_wqty"];
                            } else {
                                return params.value || 0;
                            }
                        }
                    },        
                ],
            });
        }

        list.push({
            headerName: "합계",
            children: [
                {field: "qty", headerName: "실재고", type: "currencyType", maxWidth: 60, minWidth: 60, cellStyle: {"text-align": "right"}, pinned: "right",
                    aggFunc: (params) => params.values.reduce((a,c) => a + (c * 1), 0),
                },
                {field: "wqty", headerName: "보유재고", type: "currencyType", maxWidth: 65, minWidth: 65, cellStyle: {"text-align": "right"}, pinned: "right",
                    aggFunc: (params) => params.values.reduce((a,c) => a + (c * 1), 0),
                },
            ],
        });
        list.push({ width: "auto" });

        storage_columns.push(...list.map(a => a));
        store_columns.push(...list.map(a => a));
        
        gx.gridOptions.api.setColumnDefs(storage_columns);
        gx2.gridOptions.api.setColumnDefs(store_columns);
    }

    function resetGrid() {
        gx.gridOptions.api.setRowData([]);
        gx2.gridOptions.api.setRowData([]);
    }
</script>
@stop
