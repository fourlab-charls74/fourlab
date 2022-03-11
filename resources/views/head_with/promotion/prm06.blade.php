@extends('head_with.layouts.layout')
@section('title','사은품')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">사은품</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 사은품</span>
    </div>
</div>

<form method="get" name="search">
	<input type="hidden" name="data"/>
	<input type="hidden" name="page">
	<input type="hidden" name="ac_id"/>

	<div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="AddGiftInfo();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
			<div class="card-body">
				
				<!-- 사은품명/증정구분/사용여부 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">사은품명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter search-all" name='gift_nm' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">증정구분</label>
							<div class="flax_box">
								 <select name="gift_kind" id="gift_kind" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($gift_kinds as $item)
										<option value="{{ $item->code_id }}">{{ $item->code_val }}</option>
									@endforeach
								 </select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">사용여부</label>
							<div class="flax_box">
								<select name="use_yn" id="use_yn" class="form-control form-control-sm">
									<option value="">전체</option>
									<option value="Y">사용</option>
									<option value="N">미사용</option>
								</select>
							</div>
						</div>
					</div>

				</div>

				<!-- 스타일 넘버 / 상품 코드/상품명/구매금액 -->
				<div class="search-area-ext  row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all ac-style-no search-enter" name="style_no" value="">
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="goods_no" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">상품명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter ac-goods_nm" name='goods_nm' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">구매금액</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='fr_apply_amt' value='' style="">
									</div>
								</div>
								<span class="text_line">~</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='to_apply_amt' value=''>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- 증정기간/출력자료수/정렬순서 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">증정기간</label>
							<div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="fr_apply_date" value="" autocomplete="off"  disable>
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
										<input type="text" class="form-control form-control-sm docs-date search-all search-enter" name="to_apply_date" value=""  autocomplete="off">
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

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">출력자료수/정렬순서</label>
							<div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
										<option selected value=100>100</option>
										<option value=500>500</option>
										<option value="1000">1000</option>
										<option value="2000">2000</option>
										<option value="">모두</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
										<option value="a.no" selected>사은품번호</option>
										<option value="a.name" >사은품명</option>
										<option value="a.apply_amt" >구매금액</option>
										<option value="a.qty" >재고수</option>
										<option value="a.rt" >등록일</option>
										<option value="a.ut" >수정일</option>
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
            <a href="#" onclick="AddGiftInfo();"class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
	</div>
</form>

<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
					<span>선택한 사은품을</span>
					<a href="#" class="btn-sm btn btn-primary confirm-del-btn">삭제</a>
                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>


<script type="text/javascript" charset="utf-8">
	var columns = [
		{field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
		{field:"no",  headerName: "번호", width: 70},
		{field:"name" , headerName:"사은품명", width:220,
			cellRenderer: function(params) {
				return '<a href="#" onClick="PopGiftInfo(\''+ params.data.no +'\')">'+ params.value+'</a>'
			}
		},
		{field:"kind" , headerName:"증정구분",},
		{headerName:"증정기간", width:120, 
			children : [
				{
					headerName : "시작",
					field : "fr_date",
					width:80
				},
				{
					headerName : "종료",
					field : "to_date",
					width:80
				}
			]
		},
		{field:"user_id", headerName:"user_id", hide:true},
		{field:"apply_product" , headerName:"적용대상" },
		{field:"ext_godos", headerName:"제외상품", width:100},
		{field:"gift_price" , headerName:"사은품가격", width:100, type:'currencyType'  },
		{field:"apply_amt" , headerName:"구매금액", width:100,type:'currencyType' },
		{headerName:"수량", width:120, 
			children : [
				{
					headerName : "재고수량",
					field : "qty",
					width:100
				},
				{
					headerName : "주문수량",
					field : "ord_qty",
					width:100
				}
			]
		},
		{field:"dp_soldout_yn" , headerName:"품절시 출력" },
		{field:"refund_yn" , headerName:"환불여부", width:100},
		{field:"use_yn" , headerName:"사용여부", width:110},
		{field:"admin_nm" , headerName:"등록자" },
		{field:"rt" , headerName:"등록일시", width:150},
		{field:"ut" , headerName:"수정일시", width:150},
		{field:"memo" , headerName:"메모", width:200},
		{ width: "auto" }
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(275);
	pApp.BindSearchEnter();


	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/promotion/prm06/search', data,1);
	}

	function callBack(data){
		console.log(data);
		console.log("callback");
	}

	function AddGiftInfo(){
		const url='/head/promotion/prm06/create';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=810");
	}

	function PopGiftInfo(val){
		const url='/head/promotion/prm06/'+ val;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=810");
	}

	function DelGift(cmd){
		var gift_nos = [];
		const rows = gx.getSelectedRows();

		rows.forEach(function(data){
			gift_nos.push(data.no);
		});

		if(gift_nos == ""){
			alert("삭제할 사은품을 선택해 주십시오.");
			return false;
		}

		if(confirm("사은품을 삭제하시겠습니까?")){
			/*
			var param = "";
			param += "&CMD=" + cmd + "&DATA=" + data;
			param += "&UID=" + Math.random();

			var http = new xmlHttp();
			http.onexec('prm05.php','POST',param,true,cbDelGift);
			*/
			$.ajax({
				async: true,
                type: 'put',
				url: '/head/promotion/prm06/del',
				//contentType: "application/x-www-form-urlencoded; charset=utf-8",
				data: {
					'data' : gift_nos

				},
				success: function (data) {
					console.log(data);
					var save_msg = "";
					if(data.code==1){
						Search(1);
					}
					
				},
				complete:function(){
					_grid_loading = false;
				},
				error: function(request, status, error) {
					console.log("error")
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});

		}
	}

	function checkAll(){
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var displayRowCnt = gx.gridOptions.api.getDisplayedRowCount();

		if(selectedRowData.length == displayRowCnt){
			gx.gridOptions.api.deselectAll();
		}else{
			gx.gridOptions.api.selectAll();
		}
	}


	$(function(){
		$(".ac-style-no2").autocomplete({
			//keydown 됬을때 해당 값을 가지고 서버에서 검색함.
			source : function(request, response) {
				$.ajax({
					method: 'get',
					url: '/head/auto-complete/style-no2',
					data: { keyword : this.term },
					success: function (data) {
						response(data);
					},
					error: function(request, status, error) {
						//console.log("error");
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});
			},
			minLength: 1,
			autoFocus: true,
			delay: 100,
		});


		$('.ac-goods_nm').autocomplete({
			//keydown 됬을때 해당 값을 가지고 서버에서 검색함.
			source : function(request, response) {
				$.ajax({
					method: 'get',
					url: '/head/auto-complete/goods-nm',
					data: { keyword : this.term },
					success: function (data) {
						response(data);
					},
					error: function(request, status, error) {
						//console.log("error")
					}
				});
			},
			minLength: 1,
			autoFocus: true,
			delay: 100
		});

		Search();
		
		$(".confirm-del-btn").click(function(){
			DelGift();
		});;

	});
	
</script>
@stop
