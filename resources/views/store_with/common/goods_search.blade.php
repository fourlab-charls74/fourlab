@extends('store_with.layouts.layout-nav')
@section('title','상품 검색')
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
    
</style>
<script>
    //멀티 셀렉트 박스2
    $(document).ready(function() {
        $('.multi_select').select2({
            placeholder :'전체',
            multiple: true,
            width : "100%",
            closeOnSelect: false,
        });
    });
</script>
<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품 검색</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품 검색</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="selectMultiGoods()" class="btn btn-sm btn-primary shadow-sm">확인</a>
            <a href="#" id="search_sbtn" onclick="window.close();" class="btn btn-sm btn-primary shadow-sm">닫기</a>
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
                                <label>상품코드</label>
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
                                    <select name="goods_stat[]" class="form-control form-control-sm multi_select w-100" multiple>
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
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="goods_no">상품번호</option>
                                            <option value="goods_nm">상품명</option>
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
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="goods_img" id="goods_img" value="Y" checked>
                                <label class="custom-control-label font-weight-light" for="goods_img">이미지출력</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
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
    const columns = [
        {headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: {"line-height": "30px"}},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left', sort: null},
        {field: "prd_cd", headerName: "상품코드", width:120, pinned: 'left', cellStyle: {"line-height": "30px"}},
        {
            field: "goods_no",
            headerName: "상품번호",
            width: 58,
            pinned: 'left',
            cellStyle:StyleGoodsNo,
        },
        {field: "goods_type", headerName: "상품구분", width: 58, pinned: 'left', type: 'StyleGoodsTypeNM'},
        {field: "opt_kind_cd", headerName: "품목", width:70, cellStyle: {"line-height": "30px"}},
        {field: "opt_kind_nm", headerName: "품목", width:70, cellStyle: {"line-height": "30px"}},
        {field: "brand", headerName: "브랜드", cellStyle: {"line-height": "30px"}},
        {field: "style_no", headerName: "스타일넘버", cellStyle: {"line-height": "30px"}},
        {field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}"},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "goods_nm", headerName: "상품명", type: 'HeadGoodsNameType', width: 230, cellStyle: {"line-height": "30px"}},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 230, cellStyle: {"line-height": "30px"}},
        {field: "sale_stat_cl", headerName: "상품상태", width:70, type: 'GoodsStateTypeLH50'},
        {field: "goods_opt", headerName: "옵션", width:150, cellStyle: {"line-height": "30px"}},
        {
            field: "wqty", headerName: "창고재고", width:70, type: 'numberType', cellStyle: {"line-height": "30px"},
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                }
            }
        },
        {
            field: "wqty", headerName: "매장재고", width:70, type: 'numberType', cellStyle: {"line-height": "30px"},
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                }
            }
        },
        {field: "normal_price", headerName: "정상가", type: 'currencyType', cellStyle: {"line-height": "30px"}},
        {field: "price", headerName: "판매가", type: 'currencyType', width:60, cellStyle: {"line-height": "30px"}},
        {field: "goods_sh", headerName: "TAG가", hide: true},
        {field: "wonga", headerName: "원가", type: 'currencyType', width:60, cellStyle: {"line-height": "30px"}},
        {field: "margin_rate", headerName: "마진율", type: 'percentType', width:60, cellStyle: {"line-height": "30px"}},
        {field: "margin_amt", headerName: "마진액", type: 'numberType', width:60, cellStyle: {"line-height": "30px"}},
        {field: "org_nm", headerName: "원산지", cellStyle: {"line-height": "30px"}},
        {field: "com_nm", headerName: "업체", width:84, cellStyle: {"line-height": "30px"}},
        {field: "full_nm", headerName: "대표카테고리", cellStyle: {"line-height": "30px"}},
        {field: "head_desc", headerName: "상단홍보글", cellStyle: {"line-height": "30px"}},
        {field: "make", headerName: "제조업체", cellStyle: {"line-height": "30px"}},
        {field: "reg_dm", headerName: "등록일자", width:110, cellStyle: {"line-height": "30px"}},
        {field: "upd_dm", headerName: "수정일자", width:110, cellStyle: {"line-height": "30px"}}
    ];

    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns, {onCellValueChanged: onCellValueChanged});

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
        gx.Request('/store/api/goods', data, 1);
    };
    
    const openFileSearch = () => {
        const url='/store/api/goods/show/file/search';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=600");
    }

    function fileSearch(rows) {
        const goods_nos = [];
            console.log(rows);

        rows.forEach((row) => {
            console.log(row);
            goods_nos.push(row.goods_no);
        });

        const data = "goods_nos="+goods_nos.join(',');

        gx.Request('/store/api/goods', data, 1);
    }

    Search();

    $("#goods_img").click(function() {
        gx.gridOptions.columnApi.setColumnVisible("img", $("#goods_img").is(":checked"));
    });
</script>
@stop