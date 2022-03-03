@extends('head_with.layouts.layout-nav')
@section('title','상품 검색')
@section('content')
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
                        <a href="#" id="search_sbtn" onclick="openFileSearch()" class="btn btn-sm btn-primary mr-1 shadow-sm">파일로 검색</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="user_yn">상품상태</label>
                                <div class="flax_box">
                                    <select name='goods_stat' class="form-control form-control-sm">
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
                                <label for="style_no">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
                                        </div>
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
                                <label for="formrow-email-input">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">품목</label>
                                <div class="flax_box">
                                    <select name="opt_kind_cd" class="form-control form-control-sm">
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
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                            <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <input type="hidden" id="com_id" name="com_id">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-company">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row search-area-ext d-none">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">상품구분</label>
                                <div class="flax_box">
                                    <select name='goods_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_types as $goods_type)
                                            <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">홍보글/단축명</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm" name='head_desc'>
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <select name="" id="" class="form-control form-control-sm ">
                                            <option value="">전체</option>
                                            <option value="Y">Y</option>
                                            <option value="N">N</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">이벤트 문구</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm" name=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row search-area-ext d-none">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="site">판매처</label>
                                <div class="flax_box">
                                    <select name="site" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($sites as $val)
                                            <option 
                                                value='{{ $val->com_id }}'
                                                @if($val->com_id === $site) selected @endif
                                            >{{ $val->com_nm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">카테고리</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select name='cat_type' id='cat_type' class="form-control form-control-sm w-100">
                                            <option value='DISPLAY'>전시</option>
                                            <option value='ITEM'>용도</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <select id="cat_cd" name="cat_cd" class="form-control form-control-sm select2-category"></select>
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-category"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="state">출력수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100" >100</option>
                                            <option value="500" >500</option>
                                            <option value="1000" >1000</option>
                                            <option value="2000" >2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="a.goods_no" selected>상품번호</option>
                                            <option value="a.style_no" >스타일넘버</option>
                                            <option value="a.goods_nm" >상품명</option>
                                            <option value="a.price" >판매가</option>
                                            <option value="a.upd_dm" >수정일</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
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
                <a href="#" id="search_sbtn" onclick="openFileSearch()" class="btn btn-sm btn-primary mr-1 shadow-sm">파일로 검색</a>
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
    const columnDefs = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:50,
            pinned:'left'
        },
        {field: "goods_no", headerName: "상품번호", pinned:'left'},
        {field: "opt_kind_nm", headerName: "품목", pinned:'left', width:120},
        {
            field: "brand_nm", 
            headerName: "브랜드", 
            pinned:'left'
        },
        {field: "goods_type", headerName: "상품구분", pinned:'left', width: 80},
        {field: "style_no", headerName: "스타일넘버", pinned:'left'},
        {
            field: "img", 
            headerName: "이미지", 
            pinned:'left',
            cellRenderer: function(params) {
                if (params.value !== undefined && params.data.img != "") {
                    return '<img src="{{config('shop.image_svr')}}/' + params.data.img + '" style="height:30px;"/>';
                }
            },
        },
        {field: "head_desc", headerName: "상단홍보글", pinned:'left'},
        {
            field: "goods_nm", 
            headerName: "상품명", 
            type:"HeadGoodsNameType", 
            width:400
        },
        {field: "com_nm", headerName: "업체", width: 130},
        {field: "price", headerName: "판매가", type:'currencyType'},
        {
            field: "qty", 
            headerName: "재고수", 
            type:'currencyType',
            cellRenderer: function(params) {
                return '<a href="#" data-code="'+params.value+'" onClick="">'+ params.value+'</a>'
            }
        },
        {field: "sale_stat_cl", headerName: "상품상태", type:'GoodsStateType'},
        {field: "reg_dm", headerName: "등록일자", width: 130},
        {
            field: "", 
            headerName: "선택",
            cellRenderer: function(params) {
                return "<a href='#' onclick='selectGoods("+JSON.stringify(params.data)+")'>선택</a>";
            }
        },
        {field: "", headerName: "", width: "auto"}
    ];

    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columnDefs);

    const isOpenerCallback = (name) => {
        return window.hasOwnProperty('opener') && opener.hasOwnProperty(name) && typeof opener[name] === 'function';
    };

    function selectGoods(row) {
        if (confirm("상품을 추가하시겠습니까?") === false) return;
        if (opener.goodsCallback) opener.goodsCallback(row);
        window.close();
    };

    const selectMultiGoods = () => {
        if (confirm("상품을 추가하시겠습니까?") === false) return;

        if (opener.multiGoodsCallback) opener.multiGoodsCallback(gx.getSelectedRows());

        window.close();   
    };

    const Search = () => {
        if (isOpenerCallback('beforeSearchCallback')) opener.beforeSearchCallback(document);
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/api/goods', data, 1);
    };
    
    const openFileSearch = () => {
        const url='/head/api/goods/show/file/search';
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

        gx.Request('/head/api/goods', data, 1);
    }

    Search();

    $("#goods_img").click(function() {
        gx.gridOptions.columnApi.setColumnVisible("img", $("#goods_img").is(":checked"));
    });
</script>
@stop