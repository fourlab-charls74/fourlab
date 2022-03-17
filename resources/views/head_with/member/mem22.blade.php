@extends('head_with.layouts.layout')
@section('title','상품평')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">상품평</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원&amp;CRM</span>
        <span>/ 상품평</span>
    </div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
			<div class="card-body">
				<!-- 스타일넘버/상품코드/상품명/v -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no2 search-enter" name='style_no' id="style_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm search-enter w-100" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">상품명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all ac-goods-nm search-enter" name='goods_nm' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">평점</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter ac-style-no2" name='goods_est_from' value=''>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type="text" class="form-control form-control-sm search-all search-enter" name="goods_est_to" value="">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- 제목/이름/아이디/베스트/구분/적립금 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">제목</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='goods_title' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">이름/아이디</label>
							<div class="form-inline">
								 <div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all ac-style-no2 search-enter" name='name' value=''>
									</div>
								</div>
								<span class="text_line">~</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type="text" class="form-control form-control-sm search-all search-enter" name="id" value="">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">베스트/적립금</label>
							<div class="form-inline">
								 <div class="form-inline-inner input_box" style="width:47%;">
									<select  name="best_yn" class="form-control form-control-sm">
										<option value="">전체</option>
										<option value="Y">Y</option>
										<option value="N">N</option>
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:47%;">
									<select name="point_yn" id="point_yn" class="form-control form-control-sm">
										<option value="">전체</option>
										<option value="Y">Y</option>
										<option value="N">N</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- 구매/출력여부/출력갯수 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">구매</label>
							<div class="flax_box">
								<select  name="buy_yn" class="form-control form-control-sm">
									<option value="">전체</option>
									<option value="Y">Y</option>
									<option value="N">N</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">출력여부</label>
							<div class="flax_box">
								<select  name="use_yn" class="form-control form-control-sm">
									<option value="">전체</option>
									<option value="Y">Y</option>
									<option value="N">N</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">출력갯수</label>
							<div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:30%;">
                                    <div class="form-group">
										<select name="limit" class="form-control form-control-sm">
											<option selected value=30>30</option>
											<option value=50>50</option>
											<option value=100>100</option>
											<option value=150>150</option>
										</select>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:64%;">
                                    <div class="form-inline form-check-box">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="prt_text" id="prt_text" class="custom-control-input" value="Y">
                                            <label class="custom-control-label" for="prt_text">내용출력</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
	</div>
</form>

<form name="f1">
<input type="hidden" name="use_yn" value="y"/>
<input type="hidden" name="data" />
<input type="hidden" name="ac_id" />
<input type="hidden" name="user_id" />
<input type="hidden" name="cmd" />
<input type="hidden" name="page_save" />

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
					<div class="flax_box">
						<div class="form-inline form-check-box mr-1">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" name="checkAll" id="checkAll" class="custom-control-input" value="Y">
								<label class="custom-control-label" for="checkAll">전체선택,</label>
							</div>
						</div>
						출력:
						<div style="width:50px;" class="mx-1">
							<select name="use_yn_value" class="form-control form-control-sm" onchange="document.f1.use_yn.value = this.value;">
								<option value="Y">Y</option>
								<option value="N">N</option>
							</select>
						</div>
						<a href="#" onclick="ChangeUseYN();" class="btn-sm btn btn-primary confirm-clm-no-btn">변경</a>&nbsp;
						<a href="#" onclick="Cmder('delcmd');" class="btn-sm btn btn-primary confirm-clm-no-btn">삭제</a>&nbsp;

						<div class="form-inline form-check-box">
							<div class="custom-control custom-checkbox mr-1">
								<input type="checkbox" name="ord_opt_no_ex" id="ord_opt_no_ex" class="custom-control-input" checked>
								<label class="custom-control-label" for="ord_opt_no_ex">중복 상품평 제외</label>
							</div>
							<div class="custom-control custom-checkbox mr-1">
								<input type="checkbox" name="buy_yn" id="buy_yn" class="custom-control-input" checked>
								<label class="custom-control-label" for="buy_yn">구매 상품평만</label>
							</div>
							<div class="custom-control custom-checkbox mr-1">
								<input type="checkbox" name="point_yn_ex" id="point_yn_ex" class="custom-control-input" checked>
								<label class="custom-control-label" for="point_yn_ex">지급한 상품평 제외</label>
							</div>
						</div>
						<a href="#" class="btn-sm btn btn-primary confirm-clm-no-btn point-btn">적립금 지급</a>
					</div>
                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
</form>

<script>
	var columns = [
		{
			headerName: '선택',
			checkboxSelection: true,
			width:28,
			cellRenderer: function(params) {
				if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
					return "<input type='checkbox' checked/>";
				}
			}
		},

		{field:"no" , headerName:"번호",width: 60,
			cellRenderer: function(params) {
				return '<a href="#" onClick="popPhotoView(\''+ params.value +'\')">'+ params.value+'</a>'
			}
		},
		{field:"img_s_50" , headerName:"이미지", width:60,
			cellRenderer: function(params) {
				if(params.value !== undefined && params.value!== null){
					return '<a href="#" onClick="popGoodsView(\''+ params.data.goods_no +'\')"><img src="{{config('shop.image_svr')}}'+ params.data.img_s_50 +'" alt="'+ params.value  +'" style="width:30px;height:30px;"></a>';
				}
			}
		},
		{field:"goods_nm" , headerName:"상품명",type:"HeadGoodsNameType", width:250, },
		{field:'goods_no', headerName:'goods_no', hide:true},
		{field:'goods_sub', headerName:'goods_sub', hide:true},
		{field:'style_no', headerName:'style_no', hide:true},



		{field:"estimate" , headerName:"평점", width:80},
		{field:'goods_est', headerName: 'goods_est', hide:true},
		{field:'estimate_no', headerName:'estimate_no', hide:true},

		{field:"best_yn" , headerName:"베스트", width:60},
		{field:'best_type', headerName: 'best_type', hide:true},
		{field:"buy_yn" , headerName:"구매", width:50, },

		{field:"goods_title" , headerName:"제목", width:250,
			cellRenderer: function(params){
				return "<a href='#' onclick=\"popShow('"+ params.data.no +"');\">"+ params.value +"</a>";
			}
		},
		{field:'goods_text', headerName: 'best_type', hide:true},
		{field:"name" , headerName:"이름(아이디)", width:100, type:"HeadUserType"},
		{field:'user_id', headerName:'user_id', hide:true, },

		{field:"ord_no" , headerName:"주문번호", type:'HeadOrderNoType', width:135},
		{field:'ord_opt_no', headerName:'ord_opt_no', hide:true},
		{field:"point_yn" , headerName:"적립금", width:75,
			cellRenderer: function(params){
				if( params.value == "Y" )
					return params.data.point.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "원";
				else
					return params.value;
			}
		},
		{field:"g_sum" , headerName:"중복", width:60},
		{field:"use_yn" , headerName:"출력", },
		{field:"est_user_cnt" , headerName:"상품평수(회원/상품)", width:100,
			cellRenderer: function(params) {
				return params.value +"/"+params.data.est_goods_cnt;
			}
		},
		{field:'est_goods_cnt', headerName:'est_goods_cnt', hide:true},
		{field:'comment_cnt', headerName:'comment_cnt', hide:true},
		{field:"cnt" , headerName:"조회수", width:60},
		{field:"regi_date" , headerName:"등록일시", width:135},
		{ width: "auto" }
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(265);
	pApp.BindSearchEnter();

	function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/member/mem22/search', formData, 1);
    }


	$(function(){
		Search();

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
						console.log("error");
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});
			},
			minLength: 1,
			autoFocus: true,
			delay: 100,
		});

		$("#checkAll").click(function(){
			checkAll();
		});


		$('.point-btn').click(function(e){
			const rows = gx.getSelectedRows();

			if (rows.length === 0) {
				alert("메시지 보낼 유저를 선택해주세요.");
				return;
			}
			const user_ids = [];

			rows.forEach(function(data){
				user_ids.push(data.user_id);
			});
			//console.log(user_ids);
			openAddPoint(user_ids.join(','), 11);
		});
	});

	function popShow(val){
		var url = "/head/member/mem22/"+val;
		const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}

	function popPhotoView(val){
		var url = "https://devel.netpx.co.kr/app/contents/photo_view/"+val;
		const potho_view=window.open(url,"_blank");
	}

	function popGoodsView(val){
		var url = "https://devel.netpx.co.kr/app/product/detail/"+val;
		//console.log(val);
		const potho_view=window.open(url,"_blank");
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

	function ChangeUseYN(){
		var data = new Array();
		var ff = document.f1;
		var use_yn = $("[name=use_yn_value]").val();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();

		selectedRowData.forEach( function(selectedRowData, index) {
			data.push(selectedRowData.no);
		});

		console.log(data);
		console.log(use_yn);

		if(data.length>0){
			$.ajax({
				method: 'put',
				url: '/head/member/mem22/change',
				data:{
					'data' : data,
					'use_yn': use_yn
				},
				success: function (data) {
					if(data.return_code == 1){
						Search();
						self.close();
					}else{
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.\n"+data.return_code);
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}else{

			alert("변경할 상품평을 선택해 주십시오.");
			return false;
		}

	}

	function DelCmd(cmd){
		var data = new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();

		selectedRowData.forEach( function(selectedRowData, index) {
			data.push(selectedRowData.no);
		});

		if(!confirm("삭제 하시겠습니까?")){
			return false;
		}

		if(data.length>0){
			$.ajax({
				method: 'put',
				url: '/head/member/mem22/delcmd',
				data:{
					'data' : data
				},
				success: function (data) {
					if(data.return_code == 1){
						alert("선택하신 상품평이 삭제되었습니다.");
						Search();
					}else{
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.\n"+data.return_code);
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}else{
			alert("삭제할 상품평을 선택해 주십시오.");
			return false;
		}

	}


	function cbChangeUseYn(res){

		ProcessingPopupShowHide();

		if(res.responseText == 1){
			PageList(document.f1.PAGE_SAVE.value);
		}else{
			alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
		}
	}

	function Cmder(cmd){
		DelCmd(cmd);
	}

	function AddPoint(){
		alert("적립금 지급!! 개발중");
	}
</script>

@stop
