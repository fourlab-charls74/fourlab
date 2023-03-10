@extends('store_with.layouts.layout-nav')
@section('title','온라인재고매핑 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">온라인재고매핑</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>온라인재고매핑</span>
            </div>
        </div>
    </div>
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
			<div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">기본 정보</a>
                </div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>가격 반영</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="price_apply_yn" id="price_apply_y" class="custom-control-input" value="Y" checked/>
                                                            <label class="custom-control-label" for="price_apply_y">예</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="price_apply_yn" id="price_apply_n" class="custom-control-input" value="N"/>
                                                            <label class="custom-control-label" for="price_apply_n">아니오</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">창고 버퍼링 설정</a>
                </div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>대표창고</th>
                                                <td>
													<div class="flax_box">
                                                        <input type="hidden" name="default_storage_cd" id="default_storage_cd" value="{{ $default->storage_cd }}">
														<input type='text' class="form-control form-control-sm" name='default_storage_nm' id="default_storage_nm" value="{{ $default->storage_nm }}" autocomplete="off" readonly/>
													</div>
                                                </td>
												<th>버퍼링</th>
                                                <td>
													<div class="flax_box">
														<input type='text' class="form-control form-control-sm" name='default_storage_buffer' id="default_storage_buffer" value="" autocomplete="off" />
													</div>
                                                </td>
                                            </tr>
											<tr>
                                                <th>온라인창고</th>
												<td>
													<div class="flax_box">
                                                        <input type="hidden" name="online_storage_cd" id="online_storage_cd" value="{{ $online->storage_cd }}">
                                                        <input type='text' class="form-control form-control-sm" name='online_storage_nm' id="online_storage_nm" value="{{ $online->storage_nm }}" readonly/>
                                                    </div>
                                                </td>
                                                <th>버퍼링</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm" name='online_storage_buffer' id="online_storage_buffer" value="" autocomplete="off" />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">매장 버퍼링 설정</a>
                </div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>유형</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="store_buffer_kind" id="store_buffer_a" class="custom-control-input" value="A"checked/>
                                                            <label class="custom-control-label" for="store_buffer_a">통합 버퍼링</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="store_buffer_kind" id="store_buffer_s" class="custom-control-input" value="S"/>
                                                            <label class="custom-control-label" for="store_buffer_s">개별 버퍼링</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>                                            
                                                <th>통합버퍼링</th>
                                                <td>
                                                    <div class="form-inline">
                                                        <div class="d-flex w-100">
                                                            <input type='text' class="form-control form-control-sm" name='store_buffer' id="store_buffer" />
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="card shadow" id="store_buffer_s_chk">
				<div class="card-header mb-0">
					<a href="#">매장별 개별버퍼링</a>
				</div>
				<div class="card-body mt-1">
					<div class="card-title">
						<div class="filter_wrap">
							<div class="fl_box px-0 mx-0">
								<h6 class="m-0 font-weight-bold">총 : <span id="gd_store-total" class="text-primary">0</span> 건</h6>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div id="div-gd_store" style="height:200px" class="ag-theme-balham"></div>
					</div>
				</div>
			</div>
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">재고예외</a>
                </div>
                <div class="card-body mt-1">
                    <div class="card-title">
						<div class="filter_wrap">
							<div class="fl_box px-0 mx-0">
								<h6 class="m-0 font-weight-bold">총 : <span id="gd_prd-total" class="text-primary">0</span> 건</h6>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div id="div-gd_prd" style="height:200px" class="ag-theme-balham"></div>
					</div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="resul_btn_wrap mt-3 d-block">
    <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
    <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
</div>
<script>
    const store_columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null, cellStyle: {"background":"#F5F7F7"}},
        {field: "store_nm", headerName: "매장명", width:200},
        {field: "store_buffer", headerName: "버퍼링 수", width:100},
        {field: "", width:"auto"}
    ];

    const product_columns = [
        {headerName: '#', width:35, pinned: 'left', type:'NumType', cellStyle: {"background":"#F5F7F7"}},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null, cellStyle: {"background":"#F5F7F7"}},
        {field: "prd_nm", headerName: "매장명", width:200},
        {field: "prd_limit_storage", headerName: "창고 제한 수", width:100},
        {field: "prd_limit_store", headerName: "매장 제한 수", width:100},
        {field: "", width:"auto"}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd_prd",
    });
    let gxStore;
    let gxProduct;

    $(document).ready(function() {
        // pApp.ResizeGrid(550);
        let gridPrdDiv = document.querySelector(pApp.options.gridId);
        if(gridPrdDiv !== null){
            gxProduct = new HDGrid(gridPrdDiv, product_columns);
            SearchProduct();
        }

        let gridStoreDiv = document.querySelector("#div-gd_store");
        if(gridStoreDiv !== null) {
            gxStore = new HDGrid(gridStoreDiv, store_columns);
            
            if ($('#store_buffer_s').is(":checked") === true) {
                SearchStore();
            }

            $("#store_buffer_s").click(function () {
                $("#store_buffer_s_chk").toggle();
            });
        }

        // gxStore = new HDGrid(gridDiv, store_columns);
        // storeSearch();
        // gxProduct = new HDGrid(gridDiv, product_columns);
        // prdSearch();
    });


    $(document).ready(function() {
  
        if($("input[name='store_buffer_kind']:checked").val() == "S"){
            $("input:text[name='store_buffer']").attr("readonly",true);
            $('#store_buffer_s_chk').show();
            // radio 버튼의 value 값이 S라면 활성화
        } else {
           
        }  
    });

    function SearchStore() {
        gxStore.Request('/store/product/prd06/search-store', data, 1);
    }

    function SearchProduct() {
        gxProduct.Request('/store/product/prd06/search-prd', 1);
    }


</script>
@stop