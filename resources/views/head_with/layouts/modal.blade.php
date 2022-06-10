
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
                                                    <input type='text' class="form-control form-control-sm search-all" name='brand' value='' 
                                                        onkeypress="searchBrand.Search(event)"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:60px;">브랜드명</label>
                                                <div class="flax_box">
                                                    <input type='text' class="form-control form-control-sm search-all" name='brand_nm' value='' 
                                                        onkeypress="searchBrand.Search(event)"/>
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
                                    <a href="javascript:void(0);" id="search_brand_sbtn" onclick="return searchBrand.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
                                    {{-- <div class="fr_box form-check-box">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="search_category_close" id="search_category_close" class="custom-control-input" value="Y" checked>
                                            <label class="custom-control-label" for="search_category_close" value="30">선택 후 닫기</label>
                                        </div>
                                    </div> --}}
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


<div id="SearchCategoryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SearchCategoryModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="max-width:850px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="myModalLabel">카테고리 검색</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="return searchCategory.InitValue();">
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
                                    <a href="javascript:void(0);" id="search_sbtn" onclick="return searchCategory.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
        <div class="modal-content" style="width:700px">
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
                                        <div class="col-lg-6 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:70px;">스타일넘버</label>
                                                <div class="flax_box">
                                                    <textarea name="sch_style_nos" id="sch_style_nos" rows=4 class="form-control form-control-sm" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 inner-td">
                                            <div class="form-group">
                                                <label style="min-width:70px;">상품코드</label>
                                                <div class="flax_box">
                                                    <textarea name="sch_goods_nos" id="sch_goods_nos" rows=4 class="form-control form-control-sm" ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 inner-td">
                                            <div class="form-group">
                                                {{-- <label style="min-width:60px;">&nbsp;</label> --}}
                                                <div class="flax_box">
                                                    <div class="resul_btn_wrap mt-2" style="display:block;text-align:right;margin-left:auto;">
                                                        <a href="javascript:void(0);" id="search_sbtn" onclick="return searchGoodsNos.Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                                        <a href="javascript:void(0);" onclick="return searchGoodsNos.Choice();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>선택</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="resul_btn_wrap">
                                    <a href="javascript:void(0);" id="search_sbtn" onclick="return searchGoodsNos.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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

<!-- 
    ***
    옵션관리
    ***
-->

<style>
    .center {
        padding: 5px 10px !important;
        text-align: center;
    }
    .sb {
        display: flex;
        justify-content: space-between;
    }
    .sb::after {
        content: none;
        display: inline;
    }
</style>
<div id="ControlOptionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ControlOptionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="myModalLabel">옵션관리</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="control_option" id="control_option" method="get" onsubmit="return false">
                            <div class="card-body">
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-lg-12 inner-td">
                                            <div class="table-responsive">
                                                <table class="table table-bordered th_border_none custm_tb1">
                                                    <thead>
                                                        <tr>
                                                            <th class="center">옵션구분</th>
                                                            <th class="center">옵션</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="center">
                                                                <select id='opt_kind' name='opt_kind' class="form-control form-control-sm">
                                                                </select>
                                                            </td>
                                                            <td class="center">
                                                                <input type='text' id="opt_nm" onkeypress="return controlOption.Add(event);" class="form-control form-control-sm search-all" name='opt_nm' />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-2" style="text-align:right;">
                                                <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="return controlOption.Add(event);"><i class="bx bx-plus fs-16"></i> <span class="fs-12">추가</span></button>
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
                                <div class="filter_wrap sb">
                                    <div class="fl_box">
                                        <h6 class="m-0 font-weight-bold">총 <span id="gd-option-total"></span> 건</h6>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="return controlOption.Delete();"><span class="fs-12">삭제</span></button>
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="return controlOption.Save();"><span class="fs-12">저장</span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-option" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->