@php
    $title = isset($store) ? '매장별 상품검색' : (isset($storage) ? '창고별 상품검색' : '상품검색');
    $subTitle = isset($store) ? '- ' . $store->store_nm : (isset($storage) ? '- ' . $storage->storage_nm : '');
    $subTitle2 = isset($store) ?  $store->store_nm : (isset($storage) ? $storage->storage_nm : '');
@endphp

@extends('store_with.layouts.layout-nav')
@section('title', $title)
@section('content')

<style>
    .select2.select2-container .select2-selection {
        border: 1px solid rgb(210, 210, 210);
    }
    ::placeholder {
        font-size: 13px;
        font-family: "Montserrat","Noto Sans KR",'mg', Dotum,"돋움",Helvetica,AppleSDGothicNeo,sans-serif;
        font-weight: 300;
        padding: 0px 2px 1px;
        color: black;
    }
    /* 상품 이미지 사이즈 픽스 */
    .img {
        height:30px;
    }
    .ag-row-level-1 {
		background-color: #edf4fd !important;
	}
</style>

{{-- <script>
    //멀티 셀렉트 박스2
    $(document).ready(function() {
        $('.multi_select').select2({
            placeholder :'전체',
            multiple: true,
            width : "100%",
            closeOnSelect: false,
        });
    });
</script> --}}

<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex align-items-end">{{ $title }} <p class="fs-18 pl-2" style="color: #444;">{{ $subTitle }}</p></h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ {{ $title }}</span>
            </div>
        </div>
        <div>
            <a href="javascript:void(0);" onclick="selectMultiGoods()" class="btn btn-sm btn-primary shadow-sm">확인</a>
            <a href="javascript:void(0);" onclick="window.close();" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <!-- <a href="#" id="search_sbtn" onclick="openFileSearch()" class="btn btn-sm btn-primary mr-1 shadow-sm">파일로 검색</a> -->
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
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
                                <label>바코드</label>
								<div class="flex_box">
									<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
									<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                                <label for="goods_nm_eng">상품명(영문)</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
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
                                            <option value="pc.prd_cd">바코드</option>
                                            <option value="pc.rt">등록일</option>
                                            <option value="pc.goods_no">온라인코드</option>
                                            <option value="g.goods_nm">상품명</option>
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
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <!-- <a href="#" id="search_sbtn" onclick="openFileSearch()" class="btn btn-sm btn-primary mr-1 shadow-sm">파일로 검색</a> -->
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </form>
    </div>
    
    <!-- DataTales Example -->
    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
        <div class="card-body">
            <div class="card-title mb-3">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                        <div class="box">
                            @if ((isset($store) && @$store->store_cd !='ALL') || isset($storage))
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="ext_zero_qty" id="ext_zero_qty" value="Y" checked>
                                <label class="custom-control-label font-weight-normal" for="ext_zero_qty">@if(isset($store) && @$store->store_cd !='ALL') 매장재고 @else 창고재고 @endif 0 제외</label>
                            </div>
                            @endif
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="goods_img" id="goods_img" value="Y" checked>
                                <label class="custom-control-label font-weight-light" for="goods_img">이미지출력</label>
                            </div>
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);">
                                <label class="custom-control-label font-weight-light" for="grid_expand">항목펼쳐보기</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * 파일 : 상품 검색 팝업
 * 
 * [사용법]
 * window open 한 php 파일에
 * goodsCallback나 multiGoodsCallback 메서드를 만들어서 선택한 데이터를 받습니다.
 * 
 * goodsCallback : 그리드에서 단일 항목을 선택할 경우 발생. 단일 항목만 callback에 전달됨.
 * multiGoodsCallback : 그리드에서 상품을 선택 후 확인 버튼을 눌렀을 경우 발생. 선택된 항목 모두 전달
 * 
 */
    const sum_values = (params) => params.values.reduce((a,c) => a + (c * 1), 0);
    const stock_render = (params) => {
        if (params.value === undefined) return "";
        if (params.data) {
            return '<a href="#" onclick="return openStoreStock(\'' + (params.data.prd_cd || '') + '\');">' + Comma(params.value) + '</a>';
        } else if (params.node.aggData) {
            return `<a href="#" onclick="return OpenStockPopup('${params.node.key}');">${Comma(params.value)}</a>`;
        } else {
            return '';
        }
    };

    let title = '{{ $subTitle2 }}';

    const columns = [
        // {headerName: '#', pinned: 'left', type: 'NumType', width: 40, cellStyle: StyleLineHeight},
        // {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left', sort: null},
        {field: "prd_cd", headerName: "바코드", width: 150, pinned: 'left', cellStyle: StyleLineHeight, checkboxSelection: true, headerCheckboxSelection: true,
            cellRenderer: (params) => params.value ??= '',
        },
        {field: "goods_no", headerName: "온라인코드", width: 80, pinned: 'left', cellStyle: StyleLineHeight, aggFunc: "first"},
        {field: "opt_kind_cd", hide: true},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: StyleLineHeight, aggFunc: "first"},
        {field: "brand_cd", hide: true},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: StyleLineHeight, aggFunc: "first"},
        {field: "style_no", headerName: "스타일넘버", width: 80, cellStyle: StyleLineHeight, aggFunc: "first"},
        {field: "img", headerName: "이미지", type: "GoodsImageType", width: 50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
        },
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "goods_nm", headerName: "상품명", width: 230, cellStyle: {"line-height": "30px"}, aggFunc: "first", 
            cellRenderer: function (params) {
				if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
					return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 비어있는 상품입니다.`);">' + params.value + '</a>';
				} else {
					let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
					return '<a href="javascript:void(0);" onclick="return openHeadProduct(\'' + goods_no + '\');">' + params.value + '</a>';
				}
			}
        },
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 230, cellStyle: {"line-height": "30px"}, aggFunc: "first"},
        {field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: StyleLineHeight, rowGroup: true, hide: true, checkboxSelection: true},
        {field: "color", headerName: "컬러", width: 55, cellStyle: StyleLineHeight},
        {field: "color_nm", hide: true},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: StyleLineHeight},
        {field: "goods_opt", headerName: "옵션", width: 150, cellStyle: {"line-height": "30px"}},
        {field: "total_qty", hide: true},
        @if (isset($store) && @$store->store_cd !='ALL')
            {
                field: "sg_qty", headerName: "창고재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                aggFunc: sum_values, cellRenderer: stock_render,
            },  
            {
                headerName: title??'매장재고',
                children: [
                    {
                        field: "store_qty", headerName: "실재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                        aggFunc: sum_values, cellRenderer: stock_render,
                    },
                    {
                        field: "store_wqty", headerName: "보유재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                        aggFunc: sum_values, cellRenderer: stock_render,
                    }
                ],
            },
        @elseif (isset($storage))
            {
                headerName: "창고재고",
                children: [
                    {
                        field: "storage_qty", headerName: "실재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                        aggFunc: sum_values, cellRenderer: stock_render,
                    },
                    {
                        field: "storage_wqty", headerName: "보유재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                        aggFunc: sum_values, cellRenderer: stock_render,
                    }
                ],
            },
        @else
            {
                field: "sg_qty", headerName: "창고재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                aggFunc: sum_values, cellRenderer: stock_render,
            },
            {
                field: "s_qty", headerName: "매장재고", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}, 
                aggFunc: sum_values, cellRenderer: stock_render,
            },  
        @endif
        {field: "goods_sh", headerName: "정상가", type: 'currencyType', width: 60, cellStyle: {"line-height": "30px"}, aggFunc: "first"},
        {field: "price", headerName: "현재가", type: 'currencyType', width: 60, cellStyle: {"line-height": "30px"}, aggFunc: "first"},
        {field: "wonga", headerName: "원가", type: 'currencyType', width: 60, cellStyle: {"line-height": "30px"}, aggFunc: "first"},
        {field: "margin_rate", headerName: "마진율", type: 'percentType', width: 60, cellStyle: {"line-height": "30px"}, aggFunc: "first"},
        {field: "margin_amt", headerName: "마진액", type: 'numberType', width: 60, cellStyle: {"line-height": "30px"}, aggFunc: "first"},
        {field: "org_nm", headerName: "원산지", width: 80, cellStyle: StyleLineHeight, aggFunc: "first"},
        {field: "com_id", hide: true},
        {field: "com_nm", headerName: "공급업체", width: 100, cellStyle: StyleLineHeight, aggFunc: "first"},
        {field: "make", headerName: "제조업체", width: 100, cellStyle: {"text-align": "center", "line-height": "30px"}, aggFunc: "first"},
        {field: "reg_dm", headerName: "등록일자", width: 120, cellStyle: {"text-align": "center", "line-height": "30px"}},
        // {field: "goods_type", headerName: "상품구분", width: 58, pinned: 'left', type: 'StyleGoodsTypeNM'},
        // {field: "sale_stat_cl", headerName: "전시상태", width:70, type: 'GoodsStateTypeLH50'},
        // {field: "full_nm", headerName: "대표카테고리", cellStyle: {"line-height": "30px"}},
        // {field: "head_desc", headerName: "상단홍보글", cellStyle: {"line-height": "30px"}},
        // {field: "upd_dm", headerName: "수정일자", width:110, cellStyle: {"line-height": "30px"}}
    ];

    const store_cd = '{{ @$store->store_cd }}';
    const storage_cd = '{{ @$storage->storage_cd }}';
    const include_not_match = '{{ @$include_not_match }}';

    const pApp = new App('', { gridId: "#div-gd", height: 202 });
    let gx;

    const basic_autoGroupColumnDef = (headerName, width = 150) => ({
		headerName: headerName,
		headerClass: 'bizest',
		minWidth: width,
		maxWidth: width,
		cellRenderer: 'agGroupCellRenderer',
		pinned: 'left'
	});

    $(document).ready(() => {
        pApp.ResizeGrid(202);
        pApp.BindSearchEnter();
        const gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: onCellValueChanged,
            rollup: true,
            autoGroupColumnDef: basic_autoGroupColumnDef('품번'),
			groupDefaultExpanded: 0, // 0: close, 1: open
			suppressAggFuncInHeader: true,
			animateRows: true,
            groupSelectsChildren: true,
			suppressDragLeaveHidesColumns: true,
			suppressMakeColumnVisibleAfterUnGroup: true,
            onSelectionChanged: setRowGroupExpanded,
        });

        Search();

        $("#goods_img").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#goods_img").is(":checked"));
        });
    });

    function onCellValueChanged(e) {
        e.node.setSelected(true);
    }

    const isOpenerCallback = (name) => {
        return window.hasOwnProperty('opener') && opener.hasOwnProperty(name) && typeof opener[name] === 'function';
    };

    function selectGoods(row) {
        if (confirm("상품을 추가하시겠습니까?") === false) return;
        if (opener.goodsCallback) opener.goodsCallback(row);
        window.close();
    };

    const selectMultiGoods = () => {
        let arr = gx.getSelectedRows();
        if(arr.length < 1) return alert("추가할 상품을 한 개 이상 선택해주세요.");
        if (confirm("상품을 추가하시겠습니까?") === false) return;
        if (opener.multiGoodsCallback) opener.multiGoodsCallback(arr);
        window.close();   
    };

    const Search = () => {
        if (isOpenerCallback('beforeSearchCallback')) opener.beforeSearchCallback(document);
        let data = $('form[name="search"]').serialize();
        data += `&store_cd=${store_cd}&storage_cd=${storage_cd}&include_not_match=${include_not_match}`;
        if($("[name=ext_zero_qty]").length > 0) {
            data += '&ext_zero_qty=' + $("[name=ext_zero_qty]").is(":checked");
        }
        gx.Request('/store/api/goods', data, 1, function(d) {
            setAllRowGroupExpanded($("#grid_expand").is(":checked"));
        });
    };
    
    // const openFileSearch = () => {
    //     const url='/store/api/goods/show/file/search';
    //     const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=600");
    // }

    // function fileSearch(rows) {
    //     const goods_nos = [];
    //         console.log(rows);

    //     rows.forEach((row) => {
    //         console.log(row);
    //         goods_nos.push(row.goods_no);
    //     });

    //     const data = "goods_nos="+goods_nos.join(',');

    //     gx.Request('/store/api/goods', data, 1);
    // }

    function openApi() {
        document.getElementsByClassName('sch-prdcd-range')[0].click();
    }

    function OpenStockPopup(prd_cd_p, date = '', color = '', size = '') {
		var url = `/store/product/prd04/stock?prd_cd_p=${prd_cd_p}&date=${date}&color=${color}&size=${size}`;
		var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
	}
</script>
@stop
