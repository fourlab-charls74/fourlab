@extends('partner_with.layouts.layout')
@section('title','상품')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">상품일괄등록</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품일괄등록</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm save-btn"><i class="bx bx-plus fs-16"></i> 저장</a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm del-btn">삭제</a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm copy-btn">검색 후 복사</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_stat">상품구분</label>
                            <div class="flax_box">
                                <select id="goods_stat" name='goods_stat' class="form-control form-control-sm">
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
                            <label for="goods_stat">상품상태</label>
                            <div class="flax_box">
                                <select id="goods_stat" name='goods_stat' class="form-control form-control-sm">
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
                            <label for="item">카테고리</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                    <select name='cat_type' id='cat_type' class="form-control form-control-sm">
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

                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">상단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">하단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
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
                                    <select name='cat_type' id='cat_type' class="form-control form-control-sm">
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
                            <label for="item">자료수</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter text-center" id="cnt" name='cnt' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm del-btn">삭제</a>
            <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->
<form method="post" name="save" action="/partner/stock/stk01">
    @csrf
    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
        <div class="card-body">
            <div class="card-title mb-3">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="min-height:300px;height:calc(100vh - 370px);width:100%;" class="ag-theme-balham gd-lh50 ty2"></div>
            </div>
        </div>
    </div>
</form>
<style>
    .img {
        height:40px;
    }
</style>
<script language="javascript">
    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
        {field: "goods_no", headerName: "상품번호", width: 100, pinned: 'left'},
        {field: "goods_type_nm", headerName: "상품구분", width: 100, cellStyle: StyleGoodsTypeNM, pinned: 'left'},
        {field: "com_nm", headerName: "업체", cellStyle: {"line-height": "40px"}},
        {field: "opt_kind_nm", headerName: "품목", cellStyle: {"line-height": "40px"}},
        {field: "brand_nm", headerName: "브랜드", cellStyle: {"line-height": "40px"}},
        {field: "full_nm", headerName: "대표카테고리", cellStyle: {"line-height": "40px"}},
        {field: "style_no", headerName: "스타일넘버", cellStyle: {"line-height": "40px"}},
        {field: "sale_stat_cl", headerName: "상품상태", type: 'GoodsStateTypeLH50'},
        {field: "img", headerName: "이미지", type: 'GoodsImageType', cellStyle: {"line-height": "40px"}},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "head_desc", headerName: "상단홍보글", cellStyle: {"line-height": "40px"}},
        {field: "goods_nm", headerName: "상품명", type: 'GoodsNameType', cellStyle: {"line-height": "40px"}},
        {field: "ad_desc", headerName: "하단홍보글", cellStyle: {"line-height": "40px"}},
        {field: "before_sale_price", headerName: "정상가", type: 'currencyType', hide: true},
        {field: "price", headerName: "판매가", type: 'currencyType', cellStyle: {"line-height": "40px"}},
        {field: "wonga", headerName: "원가", type: 'currencyType', cellStyle: {"line-height": "40px"}},
        {field: "margin_rate", headerName: "마진율", type: 'percentType', cellStyle: {"line-height": "40px"}},
        {field: "margin_amt", headerName: "마진액", type: 'numberType', cellStyle: {"line-height": "40px"}},
        {field: "option_type", headerName: "옵션구분",editable:true},
        {field: "option", headerName: "옵션",editable:true},
        {field: "md_nm", headerName: "MD", cellStyle: {"line-height": "40px"}},
        {field: "baesong_info", headerName: "배송지역", cellStyle: {"line-height": "40px"}},
        {field: "baesong_kind", headerName: "배송업체", cellStyle: {"line-height": "40px"}},
        {field: "dlv_pay_type", headerName: "배송비지불", cellStyle: {"line-height": "40px"}},
        {field: "baesong_price", headerName: "배송비", type: 'numberType', cellStyle: {"line-height": "40px"}},
        {field: "point", headerName: "적립금", type: 'numberType', cellStyle: {"line-height": "40px"}},
        {field: "org_nm", headerName: "원산지",editable:true},
        {field: "make", headerName: "제조업체",editable:true},
        {field: "goods_cont", headerName: "상품설명",editable:true},
        {field: "goods_location", headerName: "위치", cellStyle: {"line-height": "40px"}},
        {field: "goods_type", headerName: "goods_type", hide: true},
        {field: "com_type", headerName: "com_type", hide: true}
    ];

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    let gx;
    $(document).ready(function() {
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/product/prd03/search', data, 1);
    }
</script>
<!-- script -->
@include('partner_with.product.prd03_js')
<!-- script -->
@stop
