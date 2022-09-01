
    <!-- sample modal content -->
    <div id="SearchBrandModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">브랜드 검색</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_brand" method="get">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-6 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">브랜드</label>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-all" name='brand' value=''>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">브랜드명</label>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-all" name='brand_nm' value=''>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">구분</label>
                                                    <div class="flax_box">
                                                        <select name='brand_type' class="form-control form-control-sm">
                                                            <option value=''>전체</option>
                                                            <option value='S'>S</option>
                                                            <option value='U'>U</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">사용여부</label>
                                                    <div class="flax_box">
                                                        <select name='use_yn' class="form-control form-control-sm">
                                                            <option value=''>전체</option>
                                                            <option value='Y' selected>예</option>
                                                            <option value='N'>아니요</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="resul_btn_wrap" style="padding-top:10px;text-align:right;display:block;">
                                        <a href="#" id="search_sbtn" onclick="return searchBrand.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-brand-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        <div class="fr_box form-check-box">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="search_brand_close" id="search_brand_close" class="custom-control-input" value="Y" checked>
                                                <label class="custom-control-label" for="search_brand_close">선택 후 닫기</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-brand" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- sample modal content -->
    <div id="SearchStoreModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">매장 검색</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_store" method="get" onsubmit="return false">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:80px;">매장구분</label>
                                                    <div class="flax_box">
														<select name='store_type' class="form-control form-control-sm" id="search_store_type">
															<option value=''>전체</option>
														</select>
													</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:80px;">매장명</label>
                                                    <div class="flex_box">
                                                        <input type='text' class="form-control form-control-sm search-all" onkeypress="searchStore.Search(event);" name='store_nm' value=''>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="resul_btn_wrap" style="padding-top:7px;text-align:right;display:block;">
                                        <a href="javascript:void(0);" id="search_store_sbtn" onclick="return searchStore.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1 pt-0">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-store-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        <div class="fr_box">
                                            <a href="javascript:void(0);" id="search_store_cbtn" onclick="return searchStore.ChoiceMultiple();" class="btn btn-sm btn-primary shadow-sm" style="display: none"><i class="fas fa-check fa-sm text-white-50"></i> 선택</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-store" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div id="SearchCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchCategoryModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="max-width:850px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="myModalLabel">카테고리 검색</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="search_category" id="search_category" method="get" onsubmit="return false">
                            <div class="card-body">
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-lg-12 inner-td">
                                            <div class="form-group">
                                                <label for="" style="min-width:60px;">카테고리</label>
                                                <div class="flax_box">
                                                    <div class="form-inline-inner select-box w-25">
                                                        <select name='cat_type' class="form-control form-control-sm">
                                                            <option value='DISPLAY'>전시</option>
                                                            <option value='ITEM'>용도</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-inline-inner input-box w-75 pl-1">
                                                        <input type='text' onkeypress="return searchCategory.Search();" class="form-control form-control-sm search-all" name='cat_nm' value=''>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="resul_btn_wrap" style="padding-top:10px;text-align:right;display:block;">
                                    <a href="#" id="search_sbtn" onclick="return searchCategory.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card shadow mb-1">
                        <div class="card-body m-0">
                            <div class="card-title">
                                <div class="filter_wrap">
                                    <div class="fl_box">
                                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-category-total" class="text-primary">0</span> 건</h6>
                                    </div>
                                    {{-- <div class="fr_box form-check-box">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="search_brand_close" id="search_brand_close" class="custom-control-input" value="Y" checked>
                                            <label class="custom-control-label" for="search_brand_close" value="30">선택 후 닫기</label>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-category" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <div id="SearchCompanyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">업체 검색</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_company" method="get">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">업체명</label>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm sch-company" name='com_nm' value=''>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="resul_btn_wrap mt-2" style="display:block;">
                                        <a href="javascript:void(0);" id="search_sbtn" onclick="return searchCompany.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-company-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        {{-- <div class="fr_box form-check-box">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="search_category_close" id="search_category_close" class="custom-control-input" value="Y" checked>
                                                <label class="custom-control-label" for="search_category_close" value="30">선택 후 닫기</label>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-company" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="SearchGoodsNosModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchGoodsNosModalLabel" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">상품 검색</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_goods_nos" method="get">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">상품</label>
                                                    <div class="flax_box">
                                                        <textarea name="sch_goods_nos" id="sch_goods_nos" rows=4 class="form-control form-control-sm" ></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">&nbsp;</label>
                                                    <div class="flax_box">
                                                        <div class="resul_btn_wrap mt-2" style="display:block;">
                                                            <a href="#" id="search_sbtn" onclick="return searchGoodsNos.Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                                            <a href="#" onclick="return searchGoodsNos.Choice();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>선택</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-goods_nos-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        <div class="fr_box form-check-box">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-goods_nos" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <style>
        /* 전시카테고리 상품 이미지 사이즈 픽스 */
        .img {
            height:30px;
        }
    </style>
    <div id="SearchGoodsNoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchGoodsNoModalLabel" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">상품 검색</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_goods_no" method="get">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">상품번호</label>
                                                    <div class="flax_box">
                                                        <input type="text" name="sch_goods_nos" id="sch_goods_nos" class="form-control form-control-sm w-80" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">상품명</label>
                                                    <div class="flax_box">
                                                        <input type="text" name="goods_nm" id="goods_nm" class="form-control form-control-sm w-80" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label style="min-width:60px;">&nbsp;</label>
                                                    <div class="flax_box">
                                                        <div class="resul_btn_wrap mt-2" style="display:block;">
                                                            <a href="#" id="search_sbtn" onclick="return searchGoodsNo.Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                                            <a href="#" onclick="return searchGoodsNo.Choice();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>선택</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-goods_no-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        <div class="fr_box form-check-box">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-goods_no" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<!-- 담당자(MD) 검색 -->
<div id="SearchMdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchMdModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="myModalLabel">담당자 검색</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="search_md" method="get" onsubmit="return false">
                            <div class="card-body">
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-lg-12 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:80px;">이름</label>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm search-all" onkeypress="searchMd.Search(event);" name='md_nm' value=''>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="resul_btn_wrap" style="padding-top:7px;text-align:right;display:block;">
                                    <a href="javascript:void(0);" id="search_md_sbtn" onclick="return searchMd.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card shadow mb-1 pt-0">
                        <div class="card-body m-0">
                            <div class="card-title">
                                <div class="filter_wrap">
                                    <div class="fl_box">
                                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-md-total" class="text-primary">0</span> 건</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-md" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- 상품코드 검색 -->
<div id="SearchPrdcdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchPrdcdModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="myModalLabel">상품코드 검색</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="search_prdcd" method="get" onsubmit="return false">
                            <div class="card-body">
                                <div class="row_wrap mb-2">
                                    <div class="row">
                                        <div class="col-lg-6 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:80px;">상품코드</label>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm search-all" onkeypress="searchPrdcd.Search(event);" name='prd_cd' value=''>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:80px;">상품명</label>
                                                <div class="flex_box">
                                                    <input type='text' class="form-control form-control-sm search-all" onkeypress="searchPrdcd.Search(event);" name='goods_nm' value=''>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-4 col-lg-2 pr-0">
                                            <div class="table-responsive">
                                                <div id="div-gd-prdcd-brand" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-2 pr-0 pl-0">
                                            <div class="table-responsive">
                                                <div id="div-gd-prdcd-year" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-2 pr-0 pl-0">
                                            <div class="table-responsive">
                                                <div id="div-gd-prdcd-season" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-2 pr-0 pl-0">
                                            <div class="table-responsive">
                                                <div id="div-gd-prdcd-gender" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-2 pr-0 pl-0">
                                            <div class="table-responsive">
                                                <div id="div-gd-prdcd-item" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-2 pl-0">
                                            <div class="table-responsive">
                                                <div id="div-gd-prdcd-opt" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 text-center mt-2">
                                    <a href="javascript:void(0);" id="search_prdcd_sbtn" onclick="return searchPrdcd.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card shadow mb-1 pt-0">
                        <div class="card-body m-0">
                            <div class="card-title">
                                <div class="d-flex justify-content-between">
                                    <div class="filter_wrap">
                                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-prdcd-total" class="text-primary">0</span> 건</h6>
                                    </div>
                                    <a href="#" onclick="return searchPrdcd.Choice();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-check fa-sm text-white-50 pr-1"></i>선택</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-prdcd" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->