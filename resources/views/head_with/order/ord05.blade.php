@extends('head_with.layouts.layout')
@section('title','입금내역')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">입금내역</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 주문&amp;배송</span>
		<span>/ 입금</span>
		<span>/ 입금내역</span>
	</div>
</div>

<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
		<input type="hidden" name="page" id="1page" value="">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<!-- 주문일자/주문번호/주문자/아이디  -->
				<div class="row">
					<!-- 주문일자 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_yn">주문일자</label>
							<div class="form-inline date-select-inbox">
								<select name="date_type" onchange="return UserFromToDate(this.value,document.search,'sdate','edate');" class="form-control form-control-sm" style="width:23%;margin-right:2%;">
									<option value="0" selected>사용자</option>
									<option value="1D">금일</option>
									<option value="2D">어제</option>
									<option value="7D">최근1주</option>
									<option value="14D">최근2주</option>
									<option value="1M">최근1달</option>
									<option value="0R">금월</option>
									<option value="1R">전월</option>
								</select>
								<div class="docs-datepicker form-inline-inner" style="width:35%;">
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
								<span class="text_line" style="width:5%;">~</span>
								<div class="docs-datepicker form-inline-inner" style="width:35%;">
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
					<!-- 주문번호 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="type">주문번호</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
							</div>
						</div>
					</div>

					<!-- 주문자/아이디  -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="type">주문자/아이디</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type="text" class="form-control form-control-sm search-all search-enter" name="user_nm" value="">
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type="text" class="form-control form-control-sm search-all search-enter" name="user_id" value="">
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

				<!-- 수령자/입금자/판매처/검색항목 -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">수령자/입금자</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' value=''>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='bank_inpnm' value=''>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">판매처</label>
							<div class="flax_box">
								<select name='sale_place' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($sale_places as $sale_place)
									<option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">검색항목</label>
							<div class="form-inline inline_select_box">
								<div class="form-inline-inner select-box">
									<select class="form-control form-control-sm" name="cols">
										<option selected value="">선택하세요.</option>
										<option value="m.mobile">주문자핸드폰번호</option>
										<option value="m.phone">주문자전화번호</option>
										<option value="m.r_mobile">수령자핸드폰번호</option>
										<option value="m.r_phone">수령자전화번호</option>
										<option value="m.email">주문자이메일</option>
									</select>
								</div>
								<div class="form-inline-inner input-box">
									<input id="key" class="form-control form-control-sm search-enter" name="key">
								</div>
							</div>
						</div>
					</div>

				</div>


				<!-- 주문상태 / 클레임상태/결제방법/결제상태 / 현금영수증  -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">주문상태 / 클레임상태</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select name="ord_state" id="ord_state" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($ord_states as $ord_state)
											<option value="{{ $ord_state->code_id }}">{{ $ord_state->code_val }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select name="clm_state" id="clm_state" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($clm_states as $clm_state)
											<option value="{{ $clm_state->code_id }}">{{ $clm_state->code_val }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">결제방법</label>
							<div class="flax_box">
								<select name='stat_pay_type' class="form-control form-control-sm" style="width:calc(20% - 10px);margin-right:10px;">
									<option value=''>전체</option>
									@foreach($stat_pay_types as $stat_pay_type)
									<option value="{{ $stat_pay_type->code_id }}">{{ $stat_pay_type->code_val }}</option>
									@endforeach
								</select>
								<div class="form-inline" style="width:80%;">
									<div class="custom-control custom-checkbox form-check-box">
										<input type="checkbox" name="not_complex" id="not_complex_y" class="custom-control-input" value="Y" />
										<label class="custom-control-label" for="not_complex_y">복합결제 제외</label>
									</div>
									<!--
									<div class="custom-control custom-checkbox form-check-box ml-1">
										<input type="checkbox" name="pay_fee" id="pay_fee" class="custom-control-input" value="Y" disabled />
										<label class="custom-control-label" for="pay_fee">결제수수료 주문</label>
									</div>
									//-->
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">결제상태 / 현금영수증</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select class="form-control form-control-sm" name="pay_stat">
											<option value="">전체</option>
											@foreach($pay_stats as $pay_stat)
											<option value="{{ $pay_stat->code_id }}">{{ $pay_stat->code_val }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select class="form-control form-control-sm" name="receipt">
											<option value="">전체</option>
											<option value="R">신청</option>
											<option value="Y">발행</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- 입금액 불일치/외상주문/수동입금확인  -->
				<div class="row">
					<!-- 입금액 불일치 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="diff_amt">입금액 불일치</label>
							<div class="form-inline">
								<div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="입금액 불일치 사용">
									<input type="checkbox" class="custom-control-input" name="diff_amt" id="diff_amt">
									<label class="" for="diff_amt" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
								</div>
							</div>
						</div>
					</div>

					<!-- 외상주문 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="type">외상주문</label>
							<div class="form-inline">
								<div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="외상주문">
									<input type="checkbox" class="custom-control-input" name="debt_order" id="debt_order">
									<label class="" for="debt_order" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
								</div>
							</div>
						</div>
					</div>

					<!-- 수동입금확인  -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="type">수동입금확인</label>
							<div class="form-inline">
								<div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="수동입금확인">
									<input type="checkbox" class="custom-control-input" name="confirm" id="confirm">
									<label class="" for="confirm" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
								</div>
							</div>
						</div>
					</div>

				</div>


			</div>
		</div>
	</form>
	<div class="resul_btn_wrap mb-3">
		<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
		<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
	</div>
</div>
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
	<div class="card-body">
		<div class="card-title mb-3">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
				</div>
				<div class="fr_box flax_box" style="width:280px;">
					<select name="check_bank" id="check_bank" class="form-control form-control-sm" style="max-width:200px;">
						<option selected value="">:: 입금확인계좌 ::</option>
						<option value="{{ $banks['key'] }}">{{ $banks['value'] }}</option>
					</select>&nbsp;
					<a href="#" class="btn-sm btn btn-primary confirm-order-btn">입금확인</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	//const pageNo = -1;
	const pageNo = 1;
	var changeAmt = null;
	var columns = [
		// this row shows the row index, doesn't use any data from the row
		{
			headerName: '#',
			width: 35,
			pinned: 'left',
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
		},
		/*
			{
				headerName: '',pinned:'left',
				headerCheckboxSelection: true,
				checkboxSelection: true,
				width:50,
				cellRenderer: function(params) {
					if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
						return "<input type='checkbox' checked/>";
					}
				}
			},
			*/

		{
			field: "ord_no",
			headerName: "주문번호",
			width: 130,
			cellStyle: StyleOrdNo,
			type: 'HeadOrderNoType',
			pinned: 'left',
		},
		{
			field: "ord_opt_no",
			headerName: "ord_opt_no",
			hide: true,
		},
		{
			field: "ord_state_nm",
			headerName: "주문상태",
			width: 72,
			cellStyle: StyleOrdState
		},
		{
			field: "clm_state_nm",
			headerName: "클레임상태",
			width: 72,
			cellStyle: StyleClmState
		},


		{
			field: "ord_amt",
			headerName: "주문금액",
			width: 72,
			type: 'currencyType',
			editable: true,
		},
		{
			field: "sale_amt",
			headerName: "할인금액",
			width: 72,
			type: 'currencyType'
		},
		{
			field: "point_amt",
			headerName: "적립금",
			sortable: "ture",
			width: 60,
			type: 'currencyType'
		},
		{
			field: "pay_fee",
			headerName: "결제수수료",
			width:72,
			type: 'currencyType'
		},

		{
			field: "recv_amt",
			headerName: "결제금액",
			width: 72,
			type: 'currencyType'
		},
		{
			field: "pay_type",
			headerName: "결제방법",
			width: 72,
		},
		{
			field: "pay_stat",
			headerName: "결제상태",
			width: 72,
			cellStyle: StyleFontOrdState,

			cellRenderer: function(params) {
				if (params.value == "예정") {
					if (params.data.confirm_amt != null && params.data.confirm_amt != "" && params.data.confirm_amt > 0 && changeAmt != params.data.confirm_amt) {
						return "<input type='checkbox' id='check_ordNo_"+params.data.ord_no+"' name='check_ordNo' value='" + params.data.ord_no + "' checked/>" + params.value;
					} else {
						return "<input type='checkbox'  id='check_ordNo_"+params.data.ord_no+"' name='check_ordNo' value='" + params.data.ord_no + "' onClick='chkState(" + params.data.ord_no + ");' />" + params.value;
					}
				} else {
					return params.value;
				}
			}

		},


		{
			field: "user_nm",
			headerName: "주문자(아이디)",
			width: 120,
			type: "HeadUserType"
		},
		{
			field: "bank_code",
			headerName: "입금은행",
			width: 72,
		},
		{
			field: "bank_number",
			headerName: "입금계좌",
			width: 120,
		},

		{
			field: "bank_inpnm",
			headerName: "입금자",
			width: 60,
			cellStyle: StyleBgOrdState,
			editable: true,
		},
		{
			field: "confirm_amt",
			headerName: "입금액",
			width: 60,
			cellStyle: StyleBgOrdState,
			editable: true,
			type: 'currencyType',
			onCellValueChanged: EditAmt
		},
		{
			field: "card_msg",
			headerName: "메시지",
			width: 200,
			cellStyle: StyleBgOrdState,
			editable: true
		},
		{
			field: "r_nm",
			headerName: "수령자",
			width: 60,
		},

		{
			field: "cash_apply_yn",
			headerName: "현금영수증신청",
			width: 100,
		},
		{
			field: "cash_yn",
			headerName: "현금영수증발행",
			width: 100,
		},
		{
			field: "ord_type",
			headerName: "주문구분",
			width: 72,
		},

		{
			field: "ord_kind",
			headerName: "출고구분",
			width: 72,
		},
		{
			field: "com_nm",
			headerName: "판매처",
			width: 84,
		},
		{
			field: "ord_date",
			headerName: "주문일시",
			width: 120,
		},
		{
			field: "pay_date",
			headerName: "입금일시",
			width: 120,
		},
		{
			field: "dlv_end_date",
			headerName: "배송일시",
			width: 120,
		},

		{
			field: "user_id",
			headerName: "user_id",
			hide: true,
		},
	];

	const pApp = new App('', {
		gridId: "#div-gd"
	});
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(275);
	pApp.BindSearchEnter();

	function Search(page) {
		let formData = $('form[name="search"]').serialize();
		gx.Request('/head/order/ord05/search', formData, page, ordCallback);
	}

	function ordCallback(data) {
		//console.log(data);
	}
	$(function() {
		$("#ord_state").val('1');
		$("#clm_state").val('90');
		Search(pageNo);


		$(".confirm-order-btn").click(function() {
			Pay();
		});
	});


	function EditAmt(params) {
		/*
		신규 기능으로 주석처리

		if (params.oldValue !== params.newValue) {
			changeAmt = params.oldValue;
			var rowNode = params.node;
			rowNode.setSelected(true);

			gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
			gx.gridOptions.api.refreshCells({rowNodes:[rowNode]});
			gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);
		}
		*/
	}

	function isNumber(s) {
		s += ''; // 문자열로 변환
		s = s.replace(/^\s*|\s*$/g, ''); // 좌우 공백 제거

		if (s == '' || isNaN(s)) return false;
		return true;
	}



	// 신규 입금확인 프로세스 시작
	//
	//
	function Pay() {
		grid_data = gx.getRows();
		grid_data_cnt = grid_data.length;
		chk_data = 0;
		nodeid = "";
		is_data = false;

		for (let i = 0; i < grid_data_cnt; i++) {
			if ($(`#check_ordNo_${grid_data[i].ord_no}`).is(":checked") == true) {
				var RowNode = gx.getRowNode(i);

				if (RowNode.data.pay_stat == "예정") {
					nodeid = i;
					ord_no = RowNode.data.ord_no;
					bank_inpnm = RowNode.data.bank_inpnm;
					confirm_amt = RowNode.data.confirm_amt;
					card_msg = RowNode.data.card_msg;
					ord_opt_no = RowNode.data.ord_opt_no;
					is_data = true;

					chk_data += 1;
				}
			}
		}

		/*
		if( chk_data == 0 )
		{
			alert("입금 처리할 주문은 반드시 선택해야 합니다.");
			return false;
		}
		*/

		if (is_data == true) {
			if ($("[name=check_bank]").val() == "") {
				alert("입금확인계좌를 선택하십시오.");
				$("[name=check_bank]").focus();
				return false;
			}

			/*
			if( chk_data > 1 )
			{
				alert("여러 주문 선택시 마지막 주문이 입금 처리 됩니다.");
			}
			*/

			if (bank_inpnm == "") {
				alert("입금자를 입력해주십시오.");
				return false;
			}

			if (confirm_amt == "" || confirm_amt == "0") {
				alert("입금액을 입력해주십시오.");
				return false;
			}

			if (!isNumber(confirm_amt)) {
				alert("입금액을 정확하게 입력해주십시오..");
				return false;
			}

			gx.gridOptions.api.forEachNode(function(nodeid) {
				if (nodeid.data.ord_no == ord_no) {
					nodeid.setDataValue('pay_stat', "입금확인중...");
				}
			});

			$.ajax({
				async: true,
				type: 'put',
				url: `/head/order/ord05/pay`,
				data: {
					"ord_no": ord_no,
					"bank": $("[name=check_bank]").val(),
					"bank_inpnm": bank_inpnm,
					"confirm_amt": confirm_amt,
					"ord_opt_no": ord_opt_no,
					"card_msg": card_msg,
					"nodeid": nodeid

				},
				success: function(data) {
					console.log(data);
					cbPay(data);
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error")
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});
		} else {
			if (_proc_pay_cnt > 0) {
				alert('선택한 주문(들)을 입금확인 하였습니다.');
				search();
			}
		}
	}

	function cbPay(res) {
		var ret = res.pay_result;
		var nodeid = res.nodeid;

		if (ret == "1") {
			gx.gridOptions.api.forEachNode(function(id) {
				if (id.id == nodeid) {
					id.setDataValue('ord_state_nm', "출고요청...");
					id.setDataValue('pay_stat', "입금");
				}
			});

			Pay();
		} else if (ret == "2") {
			gx.gridOptions.api.forEachNode(function(id) {
				if (id.id == nodeid) {
					nodeid.setDataValue('ord_state_nm', "입금완료");
					nodeid.setDataValue('pay_stat', "입금(품절)");
				}
			});

			Pay();
		} else if (ret == "0") {
			alert('입금된 주문입니다.');
		} else {
			alert('입금처리 시 오류가 발생하였습니다. 다시 입금확인해 주십시오.');
		}
	}
	//
	//
	// 신규 입금확인 프로세스 종료



	var _row_pos = 0;
	var _proc_pay_cnt = 0;
	var OrdNo = 0;
	var row = new Array();

	function Pay_bak() {
		var checkOrdNo = new Array();
		var checkOrdInfo_arr = new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var is_data = false;

		$("[name=check_ordNo]:checked").each(function() {
			checkOrdNo.push($(this).val());
		});
		//console.log(checkOrdNo);
		gx.gridOptions.api.forEachNode(function(node) {
			if (is_data === false && checkOrdNo.indexOf(node.data.ord_no) >= 0) {
				is_data = true;
				//node.setDataValue('code_val', "답변완료");
			}
		});

		if (is_data) {

			if (document.f1.check_bank.value == "") {
				alert("입금확인계좌를 선택하십시오.");
				document.f1.check_bank.focus();
				return false;
			}
			if (confirm('입금확인 하시겠습니까?')) {
				//_row_pos = selectedRowData;
				gx.gridOptions.api.forEachNode(function(node) {
					if (checkOrdNo.indexOf(node.data.ord_no) >= 0) {
						checkOrdInfo_arr.push(node.data);
					}
				});

				console.log("checkOrdInfo_arr : ");
				console.log(checkOrdInfo_arr);
				_row_pos = checkOrdInfo_arr;
				_proc_pay_cnt = 0;
				PayOrder();
			}
		} else {
			alert("'입금확인' 처리하실 주문건을 선택하십시오.\n입금자, 입금액을 입력하시면 자동 선택됩니다.");
			return false;
		}
	}

	function PayOrder() {

		var ord_no = "";
		var bank_inpnm = "";
		var confirm_amt = "";
		var card_msg = "";
		var ord_opt_no = "";
		var bank = document.f1.check_bank.value;
		var is_data = false;
		var num = 0;
		var row_num = _row_pos.length;

		//console.log(row_num);
		for (i = _proc_pay_cnt; i < row_num; i++) {

			if (_row_pos[i].pay_stat != "") {
				ord_no = _row_pos[i].ord_no;
				bank_inpnm = _row_pos[i].bank_inpnm;
				confirm_amt = _row_pos[i].confirm_amt;
				card_msg = _row_pos[i].card_msg;
				ord_opt_no = _row_pos[i].ord_opt_no;
				is_data = true;
				row = _row_pos[i];

				break;

			}
		}


		if (is_data == true) {
			if (bank_inpnm == "") {
				alert("입금자를 입력해주십시오.");
				return false;
			}
			if (confirm_amt == "" || confirm_amt == "0") {
				alert("입금액을 입력해주십시오.");
				return false;
			}
			if (!isNumber(confirm_amt)) {
				alert("입금액을 정확하게 입력해주십시오..");
				return false;
			}
			gx.gridOptions.api.forEachNode(function(node) {
				if (node.data.ord_no == ord_no) {
					node.setDataValue('pay_stat', "입금확인중...");
				}
			});

			$.ajax({
				async: true,
				type: 'put',
				url: `/head/order/ord05/pay`,
				data: {
					"ord_no": ord_no,
					"bank": bank,
					"bank_inpnm": bank_inpnm,
					"confirm_amt": confirm_amt,
					"ord_opt_no": ord_opt_no,
					"card_msg": card_msg

				},
				success: function(data) {
					console.log(data);
					cbPay(data);
				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error")
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});

		} else {
			if (_proc_pay_cnt > 0) {
				alert('선택한 주문을 ' + _proc_pay_cnt + '건 입금확인 하였습니다.');
			}
		}

	}


	function cbPay_bak(res) {

		var ret = res.pay_result;
		if (ret == "1") {

			gx.gridOptions.api.forEachNode(function(node) {
				if (node.data.ord_no == row.ord_no) {
					node.setDataValue('ord_state_nm', "출고요청...");
					node.setDataValue('pay_stat', "입금");
				}
			});
			//_row_pos++;
			_proc_pay_cnt++;
			PayOrder();
		} else if (ret == "2") {
			gx.gridOptions.api.forEachNode(function(node) {
				if (node.data.ord_no == row.ord_no) {
					node.setDataValue('ord_state_nm', "입금완료");
					node.setDataValue('pay_stat', "입금(품절)");
				}
			});

			//_row_pos++;
			_proc_pay_cnt++;
			PayOrder();
		} else if (ret == "0") {
			alert('입금된 주문입니다.');
		} else {
			alert('입금처리 시 오류가 발생하였습니다. 다시 입금확인해 주십시오.');
		}
	}




	function PopUser(memId) {
		//const url='/head/member/mem01?cmd=edit&user_id='+memId;
		const url = '/head/member/mem01/' + memId;
		const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}

	function StyleBgOrdState(params) {
		if (params.value !== undefined) {
			if (params.data.pay_stat == "예정") {
				return {
					'background-color': '#ffff99'
				}
			}
		}

	}

	function StyleFontOrdState(params) {
		if (params.value !== undefined) {
			if (params.data.pay_stat == "예정") {
				return {
					'color': '#FF0000'
				}
			}
		}

	}

	function chkState(ord_no) {
		const rows = gx.getRows();
		let rowNode = null;

		rows.forEach((row) => {
			if(row.ord_no == ord_no) {
				rowNode = row;
			}
		});

		//if( bank_number == "" || bank_inpnm == "" || ( confirm_amt == 0 || confirm_amt == "" || confirm_amt == null ) )
		if (rowNode.bank_inpnm == "" || (rowNode.confirm_amt == 0 || rowNode.confirm_amt == "" || rowNode.confirm_amt == null)) {
			$(`#check_ordNo_${ord_no}`).prop("checked", false);
		}
	}

	/*
	Function: UserFromToDate
		사용자 날짜 선택

	Returns:
		없음
	*/
	function UserFromToDate(type, ff, from, to) {
		if (type.length < 2) return;

		var today = getDateObjToStr(new Date());
		var date = "";

		var peroid = type.substring(0, type.length - 1);
		var peroid_type = type.substring(type.length - 1, type.length);

		try {
			peroid = 0 - parseInt(peroid);

			if (type == "0D") {
				ff[from].value = chgHyphenDate(today);
				ff[to].value = chgHyphenDate(today);

			} else if (peroid_type == "R") {
				if (peroid == 0) {
					var date = today.substr(0, 4) + today.substr(4, 2) + "01";
					ff[from].value = chgHyphenDate(date);
					ff[to].value = chgHyphenDate(today);
				} else {
					var lastdays = new Array("", 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
					var date = calcDate(today, -1, "M");
					var in_year = date.substr(0, 4);
					var in_month = date.substr(4, 2);
					var date = in_year + in_month + "01";
					var idx = parseInt(in_month);
					var today = in_year + in_month + lastdays[idx];

					ff[from].value = chgHyphenDate(date);
					ff[to].value = chgHyphenDate(today);
				}
			} else {
				var date = calcDate(today, peroid, peroid_type);
				ff[from].value = chgHyphenDate(date);
				ff[to].value = chgHyphenDate(today);
			}

		} catch (e) {}
	}

	/*
		Function: getDateObjToStr
			날짜를 YYYYMMDD 형식으로 변경

		Parameters:
			date - date object

		Returns:
			date string "YYYYMMDD"
	*/
	function getDateObjToStr(date) {
		var str = new Array();

		var _year = date.getFullYear();
		str[str.length] = _year;

		var _month = date.getMonth() + 1;
		if (_month < 10) _month = "0" + _month;
		str[str.length] = _month;

		var _day = date.getDate();
		if (_day < 10) _day = "0" + _day;
		str[str.length] = _day
		var getDateObjToStr = str.join("");

		return getDateObjToStr;
	}

	/*
		Function: calcDate
		데이트 계산 함수

		Parameters:
			date - string "yyyymmdd"
			period - int
			period_kind - string "Y","M","D"
			gt_today - boolean

		Returns:
			calcDate("20080205",30,"D");
	*/
	function calcDate(date, period, period_kind, gt_today) {
		var today = getDateObjToStr(new Date());

		var in_year = date.substr(0, 4);
		var in_month = date.substr(4, 2);
		var in_day = date.substr(6, 2);

		var nd = new Date(in_year, in_month - 1, in_day);

		if (period_kind == "D")
			nd.setDate(nd.getDate() + period);

		if (period_kind == "M")
			nd.setMonth(nd.getMonth() + period);

		if (period_kind == "Y")
			nd.setFullYear(nd.getFullYear() + period);

		var new_date = new Date(nd);
		var calcDate = getDateObjToStr(new_date);

		if (!gt_today) { // 금일보다 큰 날짜 반환한다면
			if (calcDate > today) calcDate = today;
		}

		return calcDate;
	}

	function chgHyphenDate(item) {
		var date = "";

		tyear = item.substr(0, 4);
		tmonth = item.substr(4, 2);
		tday = item.substr(6, 2);

		date = tyear + "-" + tmonth + "-" + tday;

		return date;
	}
</script>

@stop
