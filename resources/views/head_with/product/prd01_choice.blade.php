@extends('head_with.layouts.layout-nav')
@section('title','상품')
@section('content')

    <div class="p-4">
        <div class="page_tit">
            <h3 class="d-inline-flex">상품관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품관리</span>
            </div>
        </div>

        <form method="get" name="search" id="search">
            <div id="search-area" class="search_cum_form">
                <div class="card mb-3">
                    <div class="d-flex card-header justify-content-between">
                        <h4>검색</h4>
                        <div>
                            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                            <a href="#" class="btn-sm btn btn-primary cancel-order-btn" onclick="return Choice();">선택</a>
                            <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="goods_stat">상품상태</label>
                                    <div class="flax_box">
                                        <select id="goods_stat" name='goods_stat' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($goods_stats as $goods_stat)
                                                <option value='{{ $goods_stat->code_id }}' @if($goods_stat->code_id == "40") selected @endif>{{ $goods_stat->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="style_no">스타일넘버/온라인코드</label>
                                    <div class="form-inline">
                                        <div class="form-inline-inner input_box">
                                            <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
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
                                        <input type='text' class="form-control form-control-sm ac-goods-nm" name='goods_nm' value=''>
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
                                    <label for="brand_cd">브랜드</label>
                                    <div class="form-inline inline_btn_box">
                                        <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="formrow-inputZip">상단홍보글</label>
                                    <div class="flax_box">
                                        <input type='text' class="form-control form-control-sm" name='head_desc' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-area-ext d-none row">
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="item">카테고리</label>
                                    <div class="form-inline inline_select_box">
                                        <div class="form-inline-inner select-box">
                                            <select name='cat_type' class="form-control form-control-sm">
                                                <option value='DISPLAY'>전시</option>
                                                <option value='ITEM'>용도</option>
                                            </select>
                                        </div>
                                        <div class="form-inline-inner input-box">
                                            <div class="form-inline inline_btn_box">
                                                <input type='hidden' name='cat_cd' id='cat_cd' value=''>
                                                <input type='text' class="form-control form-control-sm" name='cat_nm' id='cat_nm' value=''>
                                                <a href="#" class="btn btn-sm btn-outline-primary sch-category"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="item">재고구분</label>
                                    <div class="form-inline form-radio-box">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_unlimited" id="is_unlimited1" class="custom-control-input" checked="" value="N">
                                            <label class="custom-control-label" for="is_unlimited1" value="20">수량관리함</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="is_unlimited" id="is_unlimited2" class="custom-control-input" value="Y">
                                            <label class="custom-control-label" for="is_unlimited2" value="30">수량 관리 안함(무한재고)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="item">자료수/정렬</label>
                                    <div class="flax_box">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="resul_btn_wrap mb-3">
                    <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
                </div>
            </div>
        </form>
        <!-- DataTales Example -->
        <form method="post" name="save" action="/head/stock/stk01">
            @csrf
            <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
                <div class="card-body">
                    <div class="card-title mb-3">
                        <div class="filter_wrap">
                            <div class="fl_box">
                                <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                            </div>
                            <div class="fr_box">
                                <div class="fl_inner_box">
                                    <div class="box">
                                        <div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
                                            <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
                                            <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                                        </div>
                                    </div>
                                    <div class="box">
                                        <div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
                                            <input type="checkbox"  name="chk_close" id="chk_close" value="Y" class="custom-control-input" checked>
                                            <label class="custom-control-label text-left" for="chk_close">선택 후 닫기</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd" style="min-height:300px;height:calc(100vh - 370px); width:100%;" class="ag-theme-balham gd-lh50 ty2"></div>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <style>
        /* 상품 이미지 사이즈 강제 픽스 */
        .img {
            height:30px;
        }

    </style>
    <script language="javascript">
        const columns = [
            {headerName: '#', pinned: 'left', type:'NumType', cellStyle: {"line-height": "30px"}},
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null, cellStyle: {"line-height": "30px"}},
            {field: "goods_no", headerName: "온라인코드", width: 70, pinned: 'left', cellStyle: {"line-height": "30px"}},
            {field: "goods_type", headerName: "상품구분", width: 60, cellStyle: StyleGoodsTypeNM, pinned: 'left', cellStyle: {"line-height": "30px", 'text-align' : 'center'}},
            {field: "com_nm", headerName: "업체", width: 100, cellStyle: {"line-height": "30px"}},
            {field: "opt_kind_nm", headerName: "품목", cellStyle: {"line-height": "30px", 'text-align' : 'center'}},
            {field: "brand_nm", headerName: "브랜드", cellStyle: {"line-height": "30px"}},
            {field: "full_nm", headerName: "대표카테고리", cellStyle: {"line-height": "30px"}},
            {field: "style_no", headerName: "스타일넘버", width: 80, cellStyle: {"line-height": "30px"}},
            {field: "head_desc", headerName: "상단홍보글", cellStyle: {"line-height": "30px"}},
            {field: "img", headerName: "이미지", width: 75, type:'GoodsImageType', hide: true, cellStyle: {"line-height": "30px"}},
            {field: "img", headerName: "이미지_url", hide: true, cellStyle: {"line-height": "30px"}},
            {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType', cellStyle: {"line-height": "30px"}},
            {field: "ad_desc", headerName: "하단홍보글"},
            {field: "sale_stat_cl", headerName: "상품상태", type:'GoodsStateType', cellStyle: {"line-height": "30px"}},
            {field: "before_sale_price", headerName: "정상가", type:'currencyType', hide: true, cellStyle: {"line-height": "30px"}},
            {field: "price", headerName: "판매가", type:'currencyType', cellStyle: {"line-height": "30px"}},
            {field: "coupon_price", headerName: "쿠폰가", width: 50, type:'currencyType', cellStyle: {"line-height": "30px"}},
            {field: "sale_rate", headerName: "세일율(,%)", type:'percentType', hide: true, cellStyle: {"line-height": "30px"}},
            {field: "sale_s_dt", headerName: "세일기간", hide: true, cellStyle: {"line-height": "30px"}},
            {field: "sale_e_dt", headerName: "세일기간", hide: true, cellStyle: {"line-height": "30px"}},
            {field: "qty", headerName: "재고수", width: 50, type:'numberType', cellStyle: {"line-height": "30px"}},
            {field: "wqty", headerName: "보유재고수", width: 70, type:'numberType', cellStyle: {"line-height": "30px"}},
            {field: "wonga", headerName: "원가", type:'currencyType', cellStyle: {"line-height": "30px"}},
            {field: "margin_rate", headerName: "마진율", type:'percentType', cellStyle: {"line-height": "30px"}},
            {field: "margin_amt", headerName: "마진액", type:'numberType', cellStyle: {"line-height": "30px"}},
            {field: "md_nm", headerName: "MD", cellStyle: {"line-height": "30px"}},
            {field: "baesong_info", headerName: "배송지역", cellStyle: {"line-height": "30px"}},
            {field: "baesong_kind", headerName: "배송업체", cellStyle: {"line-height": "30px"}},
            {field: "dlv_pay_type", headerName: "배송비지불", cellStyle: {"line-height": "30px"}},
            {field: "baesong_price", headerName: "배송비", type:'numberType', cellStyle: {"line-height": "30px"}},
            {field: "point", headerName: "적립금", type:'numberType', cellStyle: {"line-height": "30px"}},
            {field: "org_nm", headerName: "원산지", cellStyle: {"line-height": "30px"}},
            {field: "make", headerName: "제조업체", cellStyle: {"line-height": "30px"}},
            {field: "reg_dm", headerName: "등록일자", cellStyle: {"line-height": "30px"}},
            {field: "upd_dm", headerName: "수정일자", cellStyle: {"line-height": "30px"}},
            {field: "goods_location", headerName: "위치", cellStyle: {"line-height": "30px"}},
            {field: "sale_price", headerName: "sale_price", hide: true, cellStyle: {"line-height": "30px"}},
            {field: "goods_type_cd", headerName: "goods_type", hide: true, cellStyle: {"line-height": "30px"}},
            {field: "com_type_d", headerName: "com_type", hide: true, cellStyle: {"line-height": "30px"}},
        ];

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;
        $(document).ready(function() {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(220);
            pApp.BindSearchEnter();
            //Search();

            $("#chk_to_class").click(function() {
                gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
            });
        });

        function Search(){
            let data = $('form[name="search"]').serialize();
            gx.Request('/head/product/prd01/search', data,1);
        }

        function Choice(){
            let goods_nos = [];
            let goods = [];
            gx.getSelectedRows().forEach((selectedRow, index) => {
                goods_nos.push(selectedRow.goods_no);
                goods.push(`${selectedRow.goods_no}||${selectedRow.goods_sub}`);
            });

            try{
                if(parent.window.opener != null && !parent.window.opener.closed)
                {
                    parent.window.opener.focus();
                    parent.window.opener.ChoiceGoodsNo(goods_nos, goods);
                    if($("input:checkbox[name='chk_close']").is(":checked") == true){
                        self.close();
                    }
                }

            }catch(e){ alert(e.description);}

        }

    </script>
@stop
