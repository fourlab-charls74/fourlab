@extends('shop_with.layouts.layout-nav')
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

    {{-- 검색 --}}
    <div id="search-area" class="search_cum_form mb-2">
        <form name="search" method="get">
            <div class="card">
                <div class="d-flex card-header justify-content-between">
                    <h4 onClick="displaySearch();" style="cursor:pointer;">검색</h4>
                    <div class="flax_box">
                        <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    </div>
                </div>
                <div id="search_sec" class="card-body" style="display:none;">
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
                                <label for="store_cd">바코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input-box w-100">
                                        <div class="form-inline inline_btn_box">
                                            <input type='text' id="prd_cd_p" name='prd_cd_p' value="{{ @$prd_cd_p }}" class="form-control form-control-sm w-100 ac-style-no search-enter">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-p"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        {{-- <div class="col-lg-6 inner-td">
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
                        </div> --}}
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
    {{-- 상품정보 --}}
    <div class="show_layout mb-2">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a onClick="displayProductInfo();" style="cursor:pointer;" href="#">상품정보</a>
            </div>
            <div id="product_info_sec" class="card-body" style="display:none;">
                <div class="table-responsive">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="120px"/>
                                <col width="15%"/>
                                <col width="35%"/>
                                <col width="15%"/>
                                <col width="35%"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td rowspan="4" class="img_box brln" id="prd_image">
                                    @if (@$prd->img !== null)
                                    <img class="goods_img" src="{{config('shop.image_svr')}}/{{@$prd->img}}" alt="이미지" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;" />
                                    @else
                                    <p class="d-flex align-items-center justify-content-center" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;">이미지 없음</p>
                                    @endif
                                </td>
                                <th>품번</th>
                                <td id="prd_prd_cd_p"></td>
                                <th>온라인코드</th>
                                <td id="prd_goods_no"></td>
                            </tr>
                            <tr>
                                <th>스타일넘버</th>
                                <td id="prd_style_no"></td>
                                <th>공급업체명</th>
                                <td id="prd_com_nm"></td>
                            </tr>
                            <tr>
                                <th>품목</th>
                                <td id="prd_opt_kind_nm"></td>
                                <th>브랜드</th>
                                <td id="prd_brand_nm"></td>
                            </tr>
                            <tr>
                                <th>상품명</th>
                                <td id="prd_goods_nm"></td>
                                <th>상품명(영문)</th>
                                <td id="prd_goods_nm_eng"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 창고/매장 재고현황 --}}
    <div class="show_layout">
        <div class="card shadow">
            <div class="card-header mb-0">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <a href="#" class="m-0 font-weight-bold">창고/매장 재고 현황</a>
                    </div>
					<div style="text-align:right">
						<p style="color:red;font-weight: bold"> * 가용재고 = 재고 - 이동중재고</p>
					</div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive mb-1">
                    <div id="div-gd-storage-stock" class="ag-theme-balham"></div>
                </div>
				<h6 class="fs-16 mt-3"></h6>
                <div class="table-responsive">
                    <div id="div-gd-store-stock" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ag-row-level-1 {
		background-color: #f2f2f2 !important;
	}
</style>

<script language="javascript">
    const setting_color = '{{ @$color }}';
    const setting_size = '{{ @$size }}';

    let AlignCenter = {"text-align": "center"};

    let storage_columns = [
        {field: "storage_cd", hide: true},
        {field: "storage_nm", headerName: "창고명", width: 180, cellStyle: AlignCenter, pinned: "left", rowGroup: true, hide: true},
        {field: "color", headerName: "컬러", width: 180, pinned: "left",
            cellRenderer: function(params){
                if (params.node.group === true) return;
                else return params.data.color_nm + ' [' + params.data.color + ']';
            }
        },
    ];

    let store_columns = [
        {field: "color", headerName: "컬러", width: 60, cellStyle: AlignCenter, pinned: "left", rowGroup: true, hide: true,
            valueGetter:function(params){
                return params.data.color_nm + ' [' + params.data.color + ']';
            }
        },
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
                autoGroupColumnDef: basic_autoGroupColumnDef('컬러', 180),
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
        let { data, status } = await axios({ url: "/shop/api/product/color?prd_cd_p=" + prd_cd_p, method: "get" });
        
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
        let { data, status } = await axios({ url: "/shop/product/prd04/stock/search?" + params, method: "get" });
        if (status === 200) {
            const sizes = data.data.sizes;
            setGoodsInfo(data.data.prd);
            await setColumns(sizes);
            gx.gridOptions.api.applyTransaction({ add: data.data.storages });
            gx2.gridOptions.api.applyTransaction({ add: data.data.stores });
        }
    }

    function setGoodsInfo(prd) {
        let img = "";
        if (prd.img) {
            img = `<img class="goods_img" src="{{config('shop.image_svr')}}/${prd.img}" alt="${prd.goods_nm}" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;" />`;
        } else {
            img = `<p class="d-flex align-items-center justify-content-center" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;">이미지 없음</p>`;
        }
        $("#prd_image").html(img);
        $("#prd_prd_cd_p").text(prd.prd_cd_p ??= '');
        $("#prd_goods_no").text(prd.goods_no ??= '');
        $("#prd_style_no").text(prd.style_no ??= '');
        $("#prd_com_nm").text(prd.com_nm ??= '');
        $("#prd_opt_kind_nm").text(prd.opt_kind_nm ??= '');
        $("#prd_brand_nm").text(prd.brand_nm ??= '');
        $("#prd_goods_nm").text(prd.goods_nm ??= '');
        $("#prd_goods_nm_eng").text(prd.goods_nm_eng ??= '');
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
                    },
                    {field: ss + "_wqty", headerName: "보유재고", maxWidth: 65, minWidth: 65,
                        cellStyle: (params) => ({"text-align": "right", "background-color": size === setting_size ? "#AAFF99" : "none"}),
                        aggFunc: (params) => params.values.reduce((a,c) => a + (c * 1), 0),
                    },        
                ],
            });
        }

        list.push({
            headerName: "합계",
            children: [
                {field: "qty", headerName: "실재고", type: "currencyType", maxWidth: 68, minWidth: 68, cellStyle: {"text-align": "right"}, pinned: "right", sort: "desc",
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
	
	// 검색 섹션 표시 유무
	function displaySearch(){
		if($('#search_sec').css('display') != "none"){
			$('#search_sec').hide();
		}else{
			$('#search_sec').show();
		}
	}

	// 상품정보 표시 유무
	function displayProductInfo(){
		if($('#product_info_sec').css('display') != "none"){
			$('#product_info_sec').hide();
		}else{
			$('#product_info_sec').show();
		}
	}
</script>
@stop
