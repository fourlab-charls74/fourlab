@extends('store_with.layouts.layout')
@section('title','상품가격 관리')
@section('content')

	<div class="page_tit">
		<h3 class="d-inline-flex">상품가격 관리</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 상품관리</span>
			<span>/ 상품가격 관리</span>
		</div>
	</div>
	<form method="get" name="search" id="search">
		@csrf
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="#" onclick="Add('add');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 가격변경 등록</a>
						<a href="#" onclick="Add('batch_add');" class="btn btn-sm btn-outline-primary shadow-sm pl-2" hidden><i class="bx bx-plus fs-16"></i> 가격변경 일괄등록</a>
                        <!-- <a href="#" onclick="Instant('add');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 가격변경 즉시 추가</a> -->
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">변경일자</label>
								<div class="date-switch-wrap form-inline">
									<div class="form-inline date-select-inbox">
										<div class="docs-datepicker form-inline-inner input_box">
											<div class="input-group">
												<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
												<div class="input-group-append">
													<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
														<i class="fa fa-calendar" aria-hidden="true"></i>
													</button>
												</div>
											</div>
											<div class="docs-datepicker-container"></div>
										</div>
										<span class="text_line">~</span>
										<div class="docs-datepicker form-inline-inner input_box">
											<div class="input-group">
												<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
												<div class="input-group-append">
													<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
													<i class="fa fa-calendar" aria-hidden="true"></i>
													</button>
												</div>
											</div>
											<div class="docs-datepicker-container"></div>
										</div>
										<div class="custom-control custom-switch date-switch-pos"  data-toggle="tooltip" data-placement="top" data-original-title="변경일자 사용">
											<input type="checkbox" class="custom-control-input" name="s_nud" id="s_nud" checked="" value="Y" onClick="ManualNotUseData();">
											<label class="" for="s_nud" data-on-label="ON" data-off-label="OFF"></label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">바코드</label>
								<div class="flex_box">
									<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
									<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">자료수/정렬</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="100" selected>100</option>
											<option value="500">500</option>
											<option value="1000">1000</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="prd_cd">바코드</option>
											<option value="idx">가격정보 코드</option>
											<option value="change_date">변경일자</option>
											<option value="rt">등록일</option>
											<option value="ut" selected>수정일</option>
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
				<a href="#" onclick="Add('add');" class="btn btn-sm btn-primary"><i class="bx bx-plus fs-16"></i> 가격변경 등록</a>
				<a href="#" onclick="Add('batch_add');" class="btn btn-sm btn-secondary" hidden><i class="bx bx-plus fs-16"></i> 가격변경 일괄등록</a>
			</div>
		</div>
	</form>
	
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
					<button type="button" onclick="del_product_price();" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<script>
    let columns = [
			{field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code',  headerCheckboxSelection: true, sort: null, width: 29,
				checkboxSelection: params => {
					if(params.data.apply_yn == 'Y') {
						return false
					} else {
						return true
					}
				}
			},
            {field: "idx", headerName: "가격변경 코드", width: 100, cellClass: 'hd-grid-code', hide:true,
				cellRenderer: function(params) {
					if (params.value !== undefined && params.data.idx != "") {
						return '<a href="#" onclick="cmd(\''+ params.value +'\');" >'+ params.value+'</a>';
					}
				}
			},
            {field: "change_date", headerName: "변경일자", width: 100, cellClass: 'hd-grid-code'},
            {field: "prd_cd", headerName: "바코드", width: 120, cellClass: 'hd-grid-code'},
            {field: "style_no", headerName: "스타일넘버", width: 80, cellClass: 'hd-grid-code'},
            {field: "goods_no", headerName: "온라인코드", width: 80, cellClass: 'hd-grid-code'},
            {field: "brand", headerName: "브랜드", width: 80, cellClass: 'hd-grid-code'},
            {field: "opt_kind_nm", headerName: "품목", width: 70, cellClass: 'hd-grid-code'},
            {field: "goods_nm", headerName: "상품명", width: 180, cellClass: 'hd-grid-code', type:"HeadGoodsNameType", cellStyle: {"text-align": "left"}},
            {field: "goods_nm_eng", headerName: "상품명(영문)", width: 180, cellClass: 'hd-grid-code', type:"HeadGoodsNameType", cellStyle: {"text-align": "left"}},
            {field: "color", headerName: "컬러", width: 100, cellClass: 'hd-grid-code'},
            {field: "size", headerName: "사이즈", width: 60, cellClass: 'hd-grid-code'},
            {field: "goods_sh", headerName: "정상가", width: 90, cellClass: 'hd-grid-code', type: "currencyType",
				cellRenderer:function(params) {
					return Comma(params.data.goods_sh) + '원';
				}
			},
            {field: "org_price", headerName: "변경전가", width: 90, cellClass: 'hd-grid-code', type: "currencyType",
				cellRenderer:function(params) {
					return Comma(params.data.org_price) + '원';
				}
			},
            {field: "change_val", headerName: "변경금액(율)", type: "currencyType", width: 100, cellClass: 'hd-grid-code',
				cellRenderer:function(params) {
					if (params.data.change_kind == 'P'){
						return '(' + params.data.price_kind + ')' +  params.data.change_val + '%'
					} else {
						return '(' + params.data.price_kind + ')' +  Comma(params.data.change_val) + '원'
					}
				}
			},
			{field: "change_price", headerName: "변경후가", width: 90, cellClass: 'hd-grid-code', type: "currencyType",
				cellRenderer:function(params) {
					return Comma(params.data.change_price) + '원';
				}
			},
			{field: "plan_category", headerName: "운영구분", width: 80, cellClass: 'hd-grid-code'},
            {field: "change_kind", headerName: "변경종류", width: 80, cellClass: 'hd-grid-code' , hide:true},
            {field: "change_cnt", headerName: "변경상품수", width: 100, cellClass: 'hd-grid-code', hide:true},
            {field: "change_type", headerName: "적용구분", width: 80, cellClass: 'hd-grid-code',
				cellStyle: params => {
					if (params.data.change_type == 'A') {
						return { "background-color": "#FFDFDF" }
					} else {
						return {'color' : '#8a2be2'}
					}
				},
				cellRenderer: params => {
					if(params.data.change_type == 'A') {
						return '즉시';
					} else {
						return '예약';
					}
				}
			},
            {field: "apply_yn", headerName: "적용여부", width: 80, cellClass: 'hd-grid-code',
				cellStyle: params => {
                        if(params.data.apply_yn == 'Y'){
							return {'color' : 'blue'}
						} else {
							return {'color' : 'red'}
						}
                    },
				cellRenderer: params => {
					if(params.data.apply_yn == 'Y') {
						return '적용';
					} else {
						return '미적용';
					}
				}
			},
            {field: "rt", headerName: "등록일자", width: 120, cellClass: 'hd-grid-code'},
            {field: "ut", headerName: "수정일자", width: 120, cellClass: 'hd-grid-code'},
            {width : 'auto'}
            
        ];
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/product/prd05/search', data,1);
    }

    function Add(cmd) {
		if (cmd == 'add') {
			const url = '/store/product/prd05/show/';
			window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1000,height=880");
		}else if (cmd == 'batch_add') {
			const url = '/store/product/prd05/batch-import/';
			window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1000,height=880");
		}
    };

	function cmd(code) {
		const url = '/store/product/prd05/show/' + code;
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1000,height=880");
    };

	// function cmd2 (code) {
	// 	const url = '/store/product/prd05/view/' + code;
	// 	window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1000,height=880");
    // };

	function del_product_price() {
		let rows = gx.getSelectedRows();

		if(rows.length < 1) return alert('삭제할 상품가격변경 정보를 선택해주세요.');

		if(!confirm("선택한 상품가격변경 정보를 삭제하시겠습니까?")) return;

		axios({
			url: '/store/product/prd05/del-product-price',
			method: 'put',
			data: {
				data: rows
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				Search();
			} else {
				console.log(res.data);
				alert("상품가격변경 삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}

	// 가격변경 즉시 
	function Instant(cmd) {
		if (cmd == 'add') {
			const url = '/store/product/prd05/view/';
			window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1000,height=880");
		}
	}

	//변경일자 사용 on/off
	function ManualNotUseData()
	{
		if( $("[name=s_nud]").is(":checked") == true )
		{
			$("[name=sdate]").prop("disabled", false);
			$("[name=edate]").prop("disabled", false);
		}
		else
		{
			$("[name=sdate]").prop("disabled", true);
			$("[name=edate]").prop("disabled", true);
		}
	}
</script>
	
@stop
