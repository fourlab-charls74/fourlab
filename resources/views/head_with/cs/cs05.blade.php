@extends('head_with.layouts.layout')
@section('title','환불완료(무통장)')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">환불완료(무통장)</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 클레임/CS</span>
		<span>/ 환불완료(무통장)</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<!-- 환불대상일/환불완료일/환불차수 -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">환불대상일</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
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
							<label for="enddate">환불완료일</label>

							<div class="form-inline">
								<div class="docs-datepicker form-inline-inne input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="enddate" value="{{ $today }}" autocomplete="off">
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
							<label for="clm_series_no">환불차수</label>
							<div class="flax_box">
								<select name="clm_series_no" id="clm_series_no" class="form-control form-control-sm ">
									<option value="">전체</option>
									@foreach($clm_series_no_list as $clm_series_no_item)
									<option value="{{ $clm_series_no_item->id }}">{{ $clm_series_no_item->val }}</option>
									@endforeach
								</select>

							</div>
						</div>
					</div>
				</div>

				<!-- 결제수단/주문번호/주문자-->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">결제수단</label>
							<div class="form-inline form-check-box">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="pay_type[0]" id="pay_type1" class="custom-control-input" checked="" value="1">
									<label class="custom-control-label" for="pay_type1">무통장</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="pay_type[1]" id="pay_type2" class="custom-control-input" value="2">
									<label class="custom-control-label" for="pay_type2">카드</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="pay_type[2]" id="pay_type16" class="custom-control-input" value="16">
									<label class="custom-control-label" for="pay_type16">계좌이체</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="pay_type[3]" id="pay_type32" class="custom-control-input" value="32">
									<label class="custom-control-label" for="pay_type32">핸드폰</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="pay_type[4]" id="pay_type64" class="custom-control-input" checked value="64">
									<label class="custom-control-label" for="pay_type64">가상계좌</label>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">주문번호</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all" name='ord_no' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">주문자</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all" name='user_nm' value=''>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
				<div class="fr_box flax_box">
					<input type="checkbox" name="checkAll" id="checkAll">전체선택&nbsp;&nbsp;
					환불차수:&nbsp;
					<input type="text" name="clm_series_no" id="clm_series_no" value="{{ $clm_series_no }}" class="form-control form-control-sm search-all" style="width:110px;">&nbsp;
					<a href="#" class="btn-sm btn btn-primary confirm-clm-no-btn">환불차수 등록</a>&nbsp;
					<a href="#" class="btn-sm btn btn-primary confirm-refund-btn">환불완료 처리</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 500px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	var columns = [{
			headerName: '#',
			width: 50,
			maxWidth: 90,
			type: 'NumType',
			pinned: 'left'
		},
		{
			field: "clm_no",
			headerName: "clm_no",
			hide: true
		},
		{
			headerName: '상태',
			pinned: 'left',
			checkboxSelection: true,
			width: 50,
			cellRenderer: function(params) {
				if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
					return "<input type='checkbox' checked/>";
				}
			}
		},
		{
			field: "ord_no",
			headerName: "주문번호",
			pinned: 'left',
			type: 'HeadOrderNoType'
		},
		{
			field: "ord_opt_no",
			headerName: "ord_opt_no",
			hide: true,
		},
		{
			field: "user_nm",
			headerName: "주문자",
			type: "HeadUserType"
		},
		{
			field: "user_id",
			headerName: "user_id",
			hide: true
		},
		{
			field: "refund_bank",
			headerName: "환불은행"
		},
		{
			field: "refund_account",
			headerName: "환불계좌",
			width: 150
		},
		{
			field: "refund_nm",
			headerName: "환불예금주"
		},
		{
			field: "pay_type",
			headerName: "결제방법",
			width: 150
		},
		{
			field: "escw_use",
			headerName: "에스크로"
		},
		{
			field: "st_cd",
			headerName: "구매확인/취소"
		},
		{
			field: "pay_amt",
			headerName: "입금액",
			type: 'currencyType',
			cellStyle: StylePayAmt
		},
		{
			field: "refund_amt",
			headerName: "환불금액",
			type: 'currencyType'
		},
		{
			field: "refund_yn",
			headerName: "환불여부",
		},
		{
			field: "ord_state_nm",
			headerName: "주문상태",
			cellStyle: StyleOrdState
		},
		{
			field: "clm_state_nm",
			headerName: "클레임상태",
			cellStyle: StyleClmState
		},
		{
			field: "clm_reason",
			headerName: "클레임사유"
		},
		{
			field: "memo",
			headerName: "클레임내용",
			width: 200
		},
		{
			field: "clm_state",
			headerName: "clm_state",
			hide: true
		},
		{
			field: "clm_series_nm",
			headerName: "환불차수"
		},
		{
			field: "req_nm",
			headerName: "환불요청자"
		},
		{
			field: "req_date",
			headerName: "환불요청일시"
		},
		{
			field: "end_nm",
			headerName: "환불완료자"
		},
		{
			field: "end_date",
			headerName: "환불완료일시"
		},
		{
			field: "not_ref_cnt",
			headerName: "not_ref_cnt",
			hide: true
		},
		{
			field: "confirm_id",
			headerName: "confirm_id",
			hide: true
		},
		{
			headerName: "",
			field: "nvl"
		}
	];
	const pApp = new App('', {
		gridId: "#div-gd"
	});
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(265);

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/cs/cs05/search', data, 1);
	}
	
	$(function() {
		Search();
		$("#checkAll").click(function() {
			checkAll();
		});

		$(".confirm-clm-no-btn").click(function() {
			SetSeries();
		});

		$(".confirm-refund-btn").click(function() {
			MultiRefunds();
		});
	});

	function StyleOrdState(params) {
		var font_color = "";
		var font_style = "";
		if (params.value !== undefined) {
			var ord_state = params.data.ord_state_nm;
			switch (ord_state) {
				case "입금예정":
					font_color = "#669900";
					font_style = "";
					break;
				case "입금완료":
					font_color = "#ff0000";
					font_style = "bold";
					break;
				case "출고요청":
					font_color = "#0000ff";
					font_style = "";
					break;
				case "출고처리중":
					font_color = "#0000ff";
					font_style = "bold";
					break;
				case "출고완료":
					font_color = "#0000ff";
					font_style = "bold";
					break;
				case "주문취소":
					font_color = "#0000ff";
					font_style = "bold";
					break;
				case "결제오류":
					font_color = "#ff0000";
					font_style = "bold";
					break;
				case "구매확정":
					font_color = "#0000ff";
					font_style = "bold";
					break;
			}

			return {
				'color': font_color,
				'font-weight': font_style
			}
		}

		var state = {

		}
		var value = gx.TextMatrix(row, col);

		return {
			'color': '#ffff99'
		}
	}

	function StyleClmState(params) {
		if (params.value !== undefined) {
			if (params.value != "") {
				return {
					'color': '#FF0000',
					'font-weight': 'bold'
				}
			}
		}
	}

	function StylePayAmt(params) {
		var payamt_style = new Array('#f59394', '#ff999a');
		if (params.data.confirm_id !== undefined && params.data.confirm_id !== null) {
			if (params.data.confirm_id != "") {
				return {
					'background': payamt_style[0]
				};
			}

		}
	}


	function PopRefund(ord_no, ord_opt_no) {
		//const url='/head/member/mem01?cmd=edit&user_id='+memId;
		const url = '/head/cs/cs06/refund?ord_no=' + ord_no + "&ord_opt_no=" + ord_opt_no;
		const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1115,height=810");
	}

	function checkAll() {
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var displayRowCnt = gx.gridOptions.api.getDisplayedRowCount();

		if (selectedRowData.length == displayRowCnt) {
			gx.gridOptions.api.deselectAll();
		} else {
			gx.gridOptions.api.selectAll();
		}
	}

	function SetSeries() {
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var selectedRowNum = selectedRowData.length;
		var data = new Array();
		var clm_series_nm = $("input[name=clm_series_no]").val();

		selectedRowData.forEach(function(selectedRowData, index) {
			data.push(selectedRowData.clm_no);
		});

		if (selectedRowNum == 0) {
			alert("환불차수 등록할 건을 선택해 주십시오.");
			return false;
		} else {

			$.ajax({
				async: true,
				type: 'put',
				url: `/head/cs/cs05/series_comm`,
				data: {
					'data': data.join(','),
					'clm_series_nm': clm_series_nm

				},
				success: function(data) {
					if (data.return_code == 1) {
						cbSetSeries(clm_series_nm);

					} else if (data.return_code == 0) {
						alert("차수등록 오류입니다.");
						return false;
					} else if (data.return_code == -1) {
						alert("클레임 차수등록 오류입니다.");
						return false;
					}
				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					//alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});

		}
	}

	function cbSetSeries(series_no) {
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var data = new Array();
		selectedRowData.forEach(function(selectedRowData, index) {
			data.push(selectedRowData.clm_no);
		});

		$("select[name=clm_series_no]").append("<option value='" + series_no + "'>" + series_no + "</option>");
		gx.gridOptions.api.forEachNode(function(node) {
			if (data.indexOf(node.data.clm_no) >= 0) {
				node.setDataValue('clm_series_nm', series_no);
			}
		});
	}

	function MultiRefunds() {
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var selectedRowNum = selectedRowData.length;
		var data = new Array();

		selectedRowData.forEach(function(selectedRowData, index) {
			data.push(selectedRowData.ord_opt_no);
		});

		console.log("MultiRefunds");
		console.log(data);
		if (selectedRowNum == 0) {
			alert("일괄 환불완료 처리하실 건을 선택해 주십시오.");
			return false;
		} else {

			$.ajax({
				async: true,
				type: 'put',
				url: `/head/cs/cs05/multi_refunds`,
				data: {
					'data': data.join(',')

				},
				success: function(data) {
					console.log(data);
					if (data.return_code == 1) {
						//search();
					} else if (data.return_code == "101") {
						alert("자료 중 클레임 상태가 '환불처리중' 이 아닌 주문상품이 있습니다. 클레임 상태를 '환불처리중' 으로 변경 후 처리하여 주십시오.");
					} else {
						alert("처리 중에 장애가 발생하였습니다. 다시 시도하여 주십시오.");
						alert(data.return_code);
					}
				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					//alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error");
					console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);

				}
			});

		}
	}
</script>

@stop