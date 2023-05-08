@extends('partner_with.layouts.layout')
@section('title','상품')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">상품관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품관리</span>
    </div>
</div>
<!--div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">상품</h1>
        <div>
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="#" onclick="AddProduct();" class="btn btn-sm btn-primary shadow-sm">상품추가</a>
            <a href="#" onclick="gx.Download();" class="btn btn-sm btn-primary shadow-sm">다운로드</a>
            <div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div-->
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
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
                                    <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                    @endforeach
                                </select>
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
							<label>상단홍보글/하단홍보글</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width: 47%">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='head_desc' value=''>
								</div>
								<span class="text_line" style="width: 6%">/</span>
								<div class="form-inline-inner input-box" style="width: 47%">
                                    <input type='text' class="form-control form-control-sm w-100 search-enter" name='ad_desc' value=''>
								</div>
							</div>
						</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">카테고리</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                    <select name='cat_type' id="cat_type" class="form-control form-control-sm">
                                        <option value='DISPLAY'>전시</option>
                                        <option value='ITEM'>용도</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box">
                                    <div class="form-inline inline_btn_box">
                                        <select name='cat_cd' id='cat_cd' class="form-control form-control-sm select2-category"></select>
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
                                    <input type="radio" name="is_unlimited" id="is_unlimited0" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="is_unlimited0" value="">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_unlimited" id="is_unlimited1" class="custom-control-input" value="N">
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
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="org_nm">원산지</label>
                            <div class="flex_box">
                                <input type="text" name="org_nm" id="org_nm" class="form-control form-control-sm search-all search-enter"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="make">제조사</label>
                            <div class="flex_box">
                                <input type="text" name="make" id="make" class="form-control form-control-sm search-all search-enter"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">옵션사용/위치</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:38%;">
                                    <select name="is_option_use" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">사용</option>
                                        <option value="N">미사용</option>
                                    </select>
                                </div>
                                <span class="text_line" style="width: 6%">/</span>
                                <div class="form-inline-inner input-box" style="width: 56%">
                                    <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_location' value=''>
                                </div>
                            </div>                     
                        </div>
                    </div>
                </div>

                <!-- <tr name="ext" class="close">
                    <td class="label">원산지</td><td>{$F_S_ORG_NM}</td>
                    <td class="label">제조사</td><td>{$F_S_MAKE}</td>
                    <td class="label">등록일자</td><td>{$F_S_DATE}</td>
                </tr>
                <tr name="ext" class="close">
                    <td class="label">옵션사용/위치</td>
                    <td colspan="5">{$F_S_IS_OPTION_USE} / {$F_S_GOODS_LOCATION}
                    </td>
                </tr> -->

            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
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
                        <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" id="show_img" name="show_img" onclick="GridImageShow()" value="Y" checked>
							<label class="custom-control-label" for="show_img">상품이미지보기</label>
						</div>
                        <span class="d-none d-sm-inline">선택한 상품을</span>
                        <select id='chg_sale_stat' name='chg_sale_stat' class="form-control form-control-sm mr-1 ml-1" style='width:130px;display:inline'>
                            <option value=''>선택</option>
                        @foreach ($goods_stats as $goods_stat)
                            <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                        @endforeach
                        </select>
                        <span class="d-none d-sm-inline">로</span>
                        <a href="javascript:void(0);" onclick="return UpdateStates();" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1 ml-1"><i class="bx bx-sync fs-16 mr-1"></i>상태변경</a>
                        <div class="btn-group dropleftbtm">
                            <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="AddProducts();">일괄등록</a>
                                <a class="dropdown-item" href="#" onclick="EditProducts();">일괄수정</a>
                                <a class="dropdown-item" href="#" onclick="ShowProductImages();">이미지보기</a>
                                <a class="dropdown-item" href="#" onclick="AddProductImages();">이미지일괄등록</a>
                            </div>
                            <input type="hidden" name="data" id="data" value="" />
                        </div>
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
        height:30px;
    }
</style>
<script language="javascript">

    const style_obj = {"line-height": "30px"};
    
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    
    const gridDiv = document.querySelector(pApp.options.gridId);
    let gx;
    $(document).ready(function() {
        gx = new HDGrid(gridDiv, columnDefs);
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        Search();
    });

    const columnDefs = [
        {headerName: '#', pinned: 'left', type: 'NumType', cellStyle: style_obj},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
        {field: "goods_no", headerName: "상품번호", width: 80, pinned: 'left', cellStyle: style_obj},
        {field: "goods_type", headerName: "상품구분", width: 100, 
            // cellStyle: { ...StyleGoodsTypeNM, "line-height": "40px" }, 
            cellStyle: (params) => 
                { 
                    let obj = StyleGoodsTypeNM(params);
                    obj = { ...obj, ...style_obj, "text-align": "center"};
                    return obj;
                },
            pinned: 'left'},
        {field: "com_nm", headerName: "업체", cellStyle: style_obj},
        {field: "opt_kind_nm", headerName: "품목", cellStyle: style_obj, width: 110},
        {field: "brand_nm", headerName: "브랜드", cellStyle: style_obj},
        {field: "full_nm", headerName: "대표카테고리", cellStyle: style_obj, width: 200},
        {field: "style_no", headerName: "스타일넘버", cellStyle: style_obj},
        {field: "head_desc", headerName: "상단홍보글", cellStyle: style_obj, width: 150},
        {field: "img", headerName: "이미지", type: 'GoodsImageType', cellStyle: style_obj},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "goods_nm", headerName: "상품명", type: 'GoodsNameType', cellStyle: style_obj},
        {field: "ad_desc", headerName: "하단홍보글", cellStyle: style_obj, width: 150},
        {field: "sale_stat_cl", headerName: "상품상태", type: 'GoodsStateTypeLH50'},
        {field: "before_sale_price", headerName: "정상가", type: 'currencyType', hide: true},
        {field: "price", headerName: "판매가", type: 'currencyType', cellStyle: style_obj},
        {field: "coupon_price", headerName: "쿠폰가", type: 'currencyType', cellStyle: style_obj},
        {field: "sale_rate", headerName: "세일율(,%)", type: 'percentType', hide: true},
        {field: "sale_s_dt", headerName: "세일기간", hide: true}, {field: "sale_e_dt", headerName: "세일기간", hide: true},
        {
            field: "qty", headerName: "재고수", type: 'numberType', cellStyle: style_obj,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openStock(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                }
            }
        },
        {
            field: "wqty", headerName: "보유재고수", type: 'numberType', cellStyle: style_obj,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openHeadStock(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                }
            }
        },
        {field: "wonga", headerName: "원가", type: 'currencyType', cellStyle: style_obj},
        {field: "margin_rate", headerName: "마진율", type: 'percentType', cellStyle: style_obj},
        {field: "margin_amt", headerName: "마진액", type: 'numberType', cellStyle: style_obj},
        {field: "md_nm", headerName: "MD", cellStyle: style_obj},
        {field: "baesong_info", headerName: "배송지역", cellStyle: style_obj},
        {field: "baesong_kind", headerName: "배송업체", cellStyle: style_obj},
        {field: "dlv_pay_type", headerName: "배송비지불", cellStyle: style_obj},
        {field: "baesong_price", headerName: "배송비", type: 'numberType', cellStyle: style_obj},
        {field: "point", headerName: "적립금", type: 'numberType', cellStyle: style_obj},
        {field: "org_nm", headerName: "원산지", cellStyle: style_obj},
        {field: "make", headerName: "제조업체", cellStyle: style_obj},
        {field: "reg_dm", headerName: "등록일자", cellStyle: style_obj, width: 130},
        {field: "upd_dm", headerName: "수정일자", cellStyle: style_obj, width: 130},
        {field: "goods_location", headerName: "위치", cellStyle: style_obj},
        {field: "sale_price", headerName: "sale_price", hide: true},
        {field: "goods_type_cd", headerName: "goods_type", hide: true},
        {field: "com_type_d", headerName: "com_type", hide: true},
        {field: "", headerName: "", width: "auto"}
    ];
    
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/product/prd01/search', data, 1);
    }

    const formReset = () => {
        document.search.reset();
    };

	/**
	 * @return {boolean}
	 */
	function UpdateStates(){

		var checkRows		= gx.getSelectedRows();
		var chg_sale_stat	= $("#chg_sale_stat").val();
		var goods_nos		= checkRows.map(function(row) {
			return row.goods_no;
		});

		if( chg_sale_stat === "" ){
			alert('변경할 상품상태를 선택해 주십시오.');
			return false;
		}

		if( goods_nos.length === 0 ){
			alert("상품상태를 변경할 상품을 선택해 주십시오.");
			return false;
		}

		if( confirm("선택된 상품의 상품상태를 변경하시겠습니까?") ){
			$.ajax({
				async: true,
				type: 'put',
				url: '/partner/product/prd01/update/state',
				data: {
					"goods_no[]": goods_nos,
					"chg_sale_stat": chg_sale_stat,
				},
				success: function(res) {
					console.log(res);
					if (res.code === 200) {
						var fail = res.head.fail;
						if (fail === 0) {
							alert('상품상태를 변경하였습니다.');
							Search(1);
						} else {
							alert(fail + ' 개의 상품이 재고부족으로 판매중 상태로 변경되지 않았습니다.\n해당 상품은 재고를 먼저 확인하신 후 판매중으로 상태 변경하시기 바랍니다.');
						}
					} else {
						console.log(res);
					}
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
		}
	}

    function GridImageShow(){
        if($("#show_img").is(":checked")){
            gx.gridOptions.columnApi.setColumnVisible('img', true);
        }else{
            gx.gridOptions.columnApi.setColumnVisible('img', false);
        }
    }
    
    function AddProduct() {
        var url = '/partner/product/prd01/create';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function AddProducts() {
        var url = '/partner/product/prd06';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function EditProducts() {
        // getSelectedRows  gridOptions.api.setRowData
        var goods_nos = gx.gridOptions.api.getSelectedRows().map(function(row) {
            return row.goods_no + "_" + row.goods_sub;
        });

        var url = '/partner/product/prd07?goods_nos=' + goods_nos.join(',');
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function AddProductImages() {
        var url = '/partner/product/prd08';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function ShowProductImages() {
        const goods_nos = gx.gridOptions.api.getSelectedRows().map(row => row.goods_no);
        if (goods_nos.length < 1) return alert("상품을 선택해주세요.");
        var url = '/partner/product/prd02/slider?goods_nos=' + goods_nos.join(",");
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }
</script>
@stop
