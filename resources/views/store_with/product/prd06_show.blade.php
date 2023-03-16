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
        <input type="hidden" name="idx" id="idx" value="{{ @$idx }}"/>
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
                                                            <input type="radio" name="price_apply_yn" id="price_apply_y" class="custom-control-input" value="Y" @if($price_apply_yn == 'Y') checked @endif/>
                                                            <label class="custom-control-label" for="price_apply_y">예</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="price_apply_yn" id="price_apply_n" class="custom-control-input" value="N" @if($price_apply_yn == 'N' || $price_apply_yn == null) checked @endif/>
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
                <div class="card-header mb-0 d-flex align-items-center justify-content-between">
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
														<input type='text' class="form-control form-control-sm" name='default_storage_nm' id="default_storage_nm" value="{{ $default->storage_nm }}" readonly/>
													</div>
                                                </td>
												<th>버퍼링</th>
                                                <td>
													<div class="flax_box">
														<input type='text' class="form-control form-control-sm" name='default_storage_buffer' id="default_storage_buffer" value="{{ @$default_storage_buffer }}" autocomplete="off" />
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
                                                        <input type='text' class="form-control form-control-sm" name='online_storage_buffer' id="online_storage_buffer" value="{{ @$online_storage_buffer }}" autocomplete="off" />
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
                <div class="card-header mb-0 d-flex align-items-center justify-content-between">
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
                                                            <input type="radio" name="store_buffer_kind" id="store_buffer_a" class="custom-control-input" value="A" @if($store_buffer_kind == 'A' || $store_buffer_kind == null) checked @endif/>
                                                            <label class="custom-control-label" for="store_buffer_a">통합 버퍼링</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="store_buffer_kind" id="store_buffer_s" class="custom-control-input" value="S" @if($store_buffer_kind == 'S') checked @endif/>
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
                                                            <input type='text' class="form-control form-control-sm" name='store_tot_buffer' id="store_tot_buffer" value="{{ @$store_tot_buffer }}" />
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
				<div class="card-header mb-0 d-flex align-items-center justify-content-between">
					<a href="#">매장</a>
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
                <div class="card-header mb-0 d-flex align-items-center justify-content-between">
                    <a href="#">재고예외</a>
                </div>
                <div class="card-body mt-1">
                    <div class="card-title">
						<div class="filter_wrap">
                            <div class="fl_box mt-1">
                                <h6 class="font-weight-bold">총 : <span id="gd_prd-total" class="text-primary">0</span> 건</h6>
                            </div>
                            <div class="fr_box mt-1">
                                <div class="flax_box">
                                    <a href="#" onclick="Add(); return false;" class="btn-sm btn btn-primary shadow-sm pl-2 mr-1">추가</a>
                                    <a href="#" onclick="ChangeData(); return false;" class="btn-sm btn btn-primary shadow-sm pl-2 mr-1">선택 정보 변경</a>
                                <div>
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
<style>
    .cellCenter .ag-cell-wrapper {
        justify-content: center;
    }
</style>
<script>
    const store_columns = [
        {
            headerName: '사용여부',
            checkboxSelection: true,
            width: 58,
            pinned:'left',
            cellClass: 'cellCenter'
        },
        {
			headerName: '#',
			width:35,
			maxWidth: 100,
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
            cellStyle: {'text-align':'center'},
			pinned:'left'
		},
        {field: "store_nm", headerName: "매장명", width:200},
        {field: "buffer_cnt", headerName: "버퍼링 수", width:100, editable: true, cellStyle: {'background' : '#ffff99', "text-align":"right"}},
        {field: "code_id", headerName: "매장아이디", hide: true},
        {field: "", width:"auto"}
    ];

    const product_columns = [
        {
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width:28,
			pinned:'left'
		},
		{
			headerName: '#',
			width:35,
			maxWidth: 100,
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
            cellStyle: {'text-align':'center'},
			pinned:'left'
		},
        {field: "prd_cd", headerName: "바코드", width:200},
        {field: "storage_limit_qty", headerName: "창고 제한 수", width:100, editable: true, cellStyle:{"background-color":"#FFFF99", "text-align":"right"}},
        {field: "store_limit_qty", headerName: "매장 제한 수", width:100, editable: true, cellStyle:{"background-color":"#FFFF99", "text-align":"right"}},
        {field: "comment", headerName: "정보", width:220, editable: true, cellStyle:{"background-color":"#FFFF99"}},
        {headerName: "삭제", field: "del", width:58, cellStyle:{"text-align":"center"},
            cellRenderer: function(params) {
				return '<a href="#" onClick="Del(\''+ params.data.prd_cd +'\')">'+ params.value+'</a>'
            }
		},
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
        let kind = "{{ @$store_buffer_kind }}";
        if( kind == 'S' ) {
            $("#store_tot_buffer").val('');
            $("#store_tot_buffer").attr("readonly", true);
        }

        pApp.ResizeGrid(550);
        let gridPrdDiv = document.querySelector(pApp.options.gridId);
        if(gridPrdDiv !== null){
            gxProduct = new HDGrid(gridPrdDiv, product_columns);
            SearchProduct();
        }

        const gridStoreDiv = document.querySelector("#div-gd_store");
        if(gridStoreDiv !== null) {
            gxStore = new HDGrid(gridStoreDiv, store_columns, {
                onFirstDataRendered: (params) => {
                    params.api.forEachNode((node) =>
                        node.setSelected(!!node.data && node.data.store_use_yn == 'Y')
                    );

                }
            });
            Search_Store();
        }

        $("#store_buffer_s").click(function () {
            $("#store_tot_buffer").val('');
            $("#store_tot_buffer").attr("readonly", true);
        });

        $("#store_buffer_a").click(function () {
            $("#store_tot_buffer").val("{{ @$store_tot_buffer }}");
            $("#store_tot_buffer").attr("readonly", false);
        });
    });

    function Validate() {
        const ff = document.detail;
        let storeRows = gxStore.getSelectedRows();
        let result = true;

        if( isNaN(ff.default_storage_buffer.value) ) {
			alert("대표창고 버퍼링 수는 반드시 숫자로 입력해야 합니다.");
			$('input[name="default_storage_buffer"]').val("");
			$('input[name="default_storage_buffer"]').focus();

			return false;
		}

        if( isNaN(ff.online_storage_buffer.value) ) {
			alert("온라인창고 버퍼링 수는 반드시 숫자로 입력해야 합니다.");
			$('input[name="online_storage_buffer"]').val("");
			$('input[name="online_storage_buffer"]').focus();

			return false;
		}

        if( isNaN(ff.store_tot_buffer.value) ) {
			alert("통합 버퍼링 수는 반드시 숫자로 입력해야 합니다.");
			$('input[name="store_tot_buffer"]').val("");
			$('input[name="store_tot_buffer"]').focus();

			return false;
		}

        if( ff.default_storage_buffer.value != "" && ff.default_storage_buffer.value != 0 ) {
            ret = confirm("대표창고 버퍼링을 설정하였습니다.\r\n이대로 진행하시겠습니까?");
            if(!ret) {
                ff.default_storage_buffer.focus();
                result = false;
                return false;
            }
        }

        if( ff.online_storage_buffer.value != "" && ff.online_storage_buffer.value != 0 ) {
            ret = confirm("온라인창고 버퍼링을 설정하였습니다.\r\n이대로 진행하시겠습니까?");
            if(!ret) {
                ff.online_storage_buffer.focus();
                result = false;
                return false;
            }
        }

        if( $("input[name='store_buffer_kind']:checked").val() == 'A' && ff.store_tot_buffer.value == "" ) {
            ret = confirm("통합버퍼링이 설정되지 않았습니다.\r\n이대로 진행하시겠습니까?");
            if(!ret) {
                ff.store_tot_buffer.focus();
                result = false;
                return false;
            }
        }

        if( $("input[name='store_buffer_kind']:checked").val() == 'S' ) {
            if( storeRows.length == 0) {
                alert("버퍼링을 설정할 매장을 선택하십시오.");
                result = false;       
                return false;
            } else {
                storeRows.forEach((row, idx) => {
                    if ( typeof(row.buffer_cnt) == "undefined" ) {
                        alert("매장별 개별버퍼링을 설정하세요.");
                        result = false;
                        return false;
                    }
                });
            }
        }

        storeRows.forEach((row, idx) => {
            if ( isNaN(row.buffer_cnt) && typeof(row.buffer_cnt) != "undefined" ) {
                console.log(row.buffer_cnt);
                alert("개별 버퍼링 수는 반드시 숫자로 입력해야 합니다.");
                gxStore.gridOptions.api.stopEditing();
                gxStore.gridOptions.api.startEditingCell({ rowIndex: idx, colKey: 'buffer_cnt' });
                result = false;
                return false;
            }
        });

        return result;
    }

    function Search_Store() {
        gxStore.Request('/store/product/prd06/search_store', '');
    }

    function SearchProduct() {
        gxProduct.Request('/store/product/prd06/search_prd', '');
    }

    function Save() {
        if (Validate() === false) return;

        let data = $('form[name="detail"]').serialize();
        
        let idArr = [];
        let storeRows = gxStore.getSelectedRows();
        let allRows = gxStore.getRows();
        allRows.forEach((row, idx) => {
            idArr.push(row.code_id);
        });

        if( storeRows.length > 0 ) {
            storeRows.forEach((row, idx) => {
                idArr = idArr.filter((element) => element != row.code_id);
            });
        }

        axios({
			url: '/store/product/prd06/save',
			method: 'post',
			data: data + '&store_data=' + JSON.stringify(storeRows) + '&idArr=' + JSON.stringify(idArr),
		}).then(function(res) {
			if (res.data.code === 200) {
				alert("저장이 완료되었습니다.");
				window.close();
				opener.Search();
			} else {
				console.log(res.data);
				alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function(err) {
			console.log(err);
		});
    }

    function Add() {
        const url='/store/product/prd06/prd_add';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=330");
	}

    function ChangeData() {
		let checkRows = gxProduct.gridOptions.api.getSelectedRows();

		if (checkRows.length === 0) {
            alert("수정할 데이터를 선택해주세요.");
            return;
		}

		if (confirm("선택하신 데이터를 수정하시겠습니까?")) {
			$.ajax({
				async: true,
				type: 'put',
				url: '/store/product/prd06/prd_update',
				data: {
					data : JSON.stringify(checkRows),
				},
				success: function (data) {
					if (data.code == "200") {
						alert("선택한 데이터가 수정 되었습니다.");
						SearchProduct();
					} 
					else {
						alert("데이터 수정이 실패하였습니다.");
					}
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					console.log("error")
				}
			});
		}
	}

    function Del(item) {
		ret	= confirm("삭제 하시겠습니까?");

		if (ret) {
			$.ajax({
				async: true,
				type: 'put',
				url: '/store/product/prd06/prd_delete',
				data: {
					prd_cd : item,
				},
				success: function (data) {
					if (data.code == "200") {
						alert("삭제 되었습니다.");
						SearchProduct();
					} else {
					    alert("삭제를 실패하였습니다.");
					}
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					console.log("error")
				}
			});
		}
	}
</script>
@stop