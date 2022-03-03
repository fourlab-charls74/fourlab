@extends('head_with.layouts.layout')
@section('title','입금내역(뱅크다)')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">입금내역(뱅크다)</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 주문&amp;배송</span>
        <span>/ 입금</span>
        <span>/ 입금내역(뱅크다)</span>
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
				<!-- 입금일자/입금은행/입금자  -->
                <div class="row">
					<!-- 입금일자 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">입금일자</label>
							<div class="form-inline date-select-inbox">
								<select name="date_type" class="form-control form-control-sm" style="width:23%;margin-right:2%;">
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

					<!-- 입금은행 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="type">입금은행</label>
							<div class="form-inline inline_btn_box" style="padding-right:60px;">
								<select name='account' class="form-control form-control-sm" style="width:100%;">
                                    <option value=''>전체</option>
									@foreach($accounts as $account)
										<option value="{{ $account->id }}">{{ $account->val }}</option>
									@endforeach
                                </select>
                                <a href="#" class="btn btn-sm btn-secondary account-add-btn" style="width:50px;">관리</a>
							</div>
                        </div>
                    </div>

					<!-- 입금자  -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">입금자</label>
                            <div class="flax_box">
                               <input type="text" class="form-control form-control-sm search-all search-enter" name="bank_inpnm" value="">
                            </div>
                        </div>
                    </div>

                </div>

				<!-- 보류여부/입금확인여부/입금액 -->
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">보류여부</label>
                            <div class="flax_box">
                                <select name='is_hold' class="form-control form-control-sm">
                                    <option value=''>전체</option>

                                    @foreach($is_yn_item as $is_yn)
										<option value="{{ $is_yn->code_id }}">{{ $is_yn->code_val }}</option>
									@endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">입금확인여부</label>
                            <div class="flax_box">
                                <select name='is_matched' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach($is_yn_item as $is_yn)
										<option value="{{ $is_yn->code_id }}">{{ $is_yn->code_val }}</option>
									@endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">입금액</label>
                            <div class="flax_box">
								<input id="bank_input" class="form-control form-control-sm" name="bank_input">
                            </div>
                        </div>
                    </div>
                </div>
				<!-- 주문번호 / 주문자 / 수령자  -->
                <div class="search-area-ext row d-none align-items-center">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">주문번호</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">주문자</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='user_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">수령자</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</form>
</div>

<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <a href="#" class="btn-sm btn btn-primary confirm-order-btn" onclick="Pay('order');">입금확인</a>
					<a href="#" class="btn-sm btn btn-primary confirm-order-btn" onclick="Pay('hold');">입금보류</a>
					<a href="#" class="btn-sm btn btn-primary confirm-memo-btn">메모저장</a>
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
	var memo_Change_id = new Array();
	var order_Change_id = new Array();
	var _row_pos = new Array();
	var row = new Array();
	var _proc_pay_cnt = 0;

	const pageNo = 1;
	var columns = [
			// this row shows the row index, doesn't use any data from the row
			{
				headerName: '#',
				width:40,
				pinned: 'left',
				maxWidth: 100,
				// it is important to have node.id here, so that when the id changes (which happens
				// when the row is loaded) then the cell is refreshed.
				valueGetter: 'node.id',
				cellRenderer: 'loadingRenderer',
			},
			{
				headerName: '#',
				field : 'chk',
				pinned: 'left',
				width:40,
				checkboxSelection: function(event){},
				cellRenderer: function(params) {
					if(memo_Change_id.indexOf(params.data.no)>=0 || order_Change_id.indexOf(params.data.no)>=0){
						return "<input type='checkbox' id='checkBkdaNoId"+ params.data.no +"' name='check_"+ params.data.no +"' checked  onclick=\"ordStatsChange('"+ params.node.id +"')\" />";
					}else if(params.data.is_matched =="N"){
						return "<input type='checkbox' id='checkBkdaNoId"+ params.data.no +"' name='check_"+ params.data.no +"' onclick=\"ordStatsChange('"+ params.node.id +"')\"/>";
					}
				},
			},
			{field:"bkdate" , headerName:"입금일자", width:80, cellStyle:StyleOrdNo , pinned: 'left',
				cellRenderer: function(params) {
					return '<a href="#" data-code="'+params.value+'" onClick="goAccountLog(this)">'+ params.value+'</a>';
				}
			},
			{field:"bkname",headerName:"은행", width:60, pinned: 'left',},
			{field:"number",headerName:"계좌번호", width:90, pinned: 'left',},


			{field:"bkjukyo",headerName:"입금자명",width:85,editable: true, pinned: 'left',},
			{field:"bkinput" , headerName:"입금액", width:80, type: 'currencyType', pinned: 'left', },
			{field:"bkinfo" , headerName:"이제청보",sortable:"ture", width:120},
			{field:"memo" , headerName:"메모", width:200, editable: true, onCellValueChanged:setMemo},

			{field:"is_matched",headerName:"입금확인여부", width:100},
			{field:"is_hold",headerName:"입금보류여부", width:100,},
			{field:"rt",headerName:"입금내역수집일시", width:120, cellStyle:StyleFontOrdState, },


			{field:"matched_dt",headerName:"입금확인일시", width:160,},
			{field:"ord_no",headerName:"주문번호", width:130,
				cellRenderer: function(params) {
					if(params.value !== undefined && params.value !== null && params.value === "선택"){
						return '<a href="#" onClick="goOrderList('+ params.data.no +')">'+ params.value+'</a>'

					}else{
						return params.value;
					}
				},
				cellStyle:StyleOrdNoSet,
				onCellValueChanged:setOrdNoBg
			},
			{field:"ord_nos",headerName:"복수주문번호", width:120,},

			{field:"expect_ord_no",headerName:"예상주문번호", width:80, editable: true},
			{field:"ord_state",headerName:"주문상태", width:80, cellStyle:StyleBgOrdState, editable: true},
			{field:"pay_type",headerName:"결제방법", width:200, editable: true},
			{field:"pay_stat",headerName:"입금상태", width:80, cellStyle:StylePayStats, },

			{field:"ord_amt",headerName:"주문금액", width:120,},
			{headerName:"할인금액", width:120,
				children : [
					{
						headerName : "적립금",
						field : "point_amt"
					},
					{
						headerName : "적립금",
						field : "coupon_amt"
					},
					{
						headerName : "할인",
						field : "dc_amt"
					},
				]
			},

			{headerName:"주문자정보", width:120,
				children : [
					{
						headerName : "연락처",
						field : "phone"
					},
					{
						headerName : "핸드폰번호",
						field : "mobile"
					},
					{
						headerName : "주문자(아이디)",
						field : "user_nm"
					},
				]
			},
			{field:"r_nm",headerName:"수령자", width:120,},
			{field:"sale_price",headerName:"판매처", width:120,},
			{field:"admin_name",headerName:"입금확인처리자", width:120,},
			{field:"h_bkdate", headerName:"h_bkdate", hide: true},
			{field:"no", headerName:"no", hide: true},

	];

	const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);
	gx.gridOptions.suppressRowClickSelection = true;
	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		Search();
	});

	function Search(page) {
        let formData = $('form[name="search"]').serialize();
		memo_Change_id = new Array();
		order_Change_id = new Array();
		_row_pos = new Array();
		row = new Array();
        gx.Request('/head/order/ord06/search', formData, page, ordCallback);
    }

	function ordCallback(data){
	}

	$(function(){
		$(".company-add-btn").click((e) => {
			e.preventDefault();

			searchCompany.Open((code, name) => {
				if (confirm("선택한 업체를 추가하시겠습니까?") === false) return;

				$("#com_nm").val(name);
				$("#com_id").val(code);

			});
		});

		Search(pageNo);

		$(".account-add-btn").click(function(){
			PopAccount();
		});

		$(".confirm-memo-btn").click(function(){
			SaveMemo();
		});

		$("[name=is_hold]").val('N');
		$("[name=is_matched]").val('N');
	});


	function StyleBgOrdState(params){
		var font_color = "";
		var font_weight = "400";
		if(params.value !== undefined){

			switch(params.data.ord_state){
				case "출고완료":
					font_color = "blue";
					font_weight = "bold";
					break;
				case "출고요청":
					font_color = "blue";
					font_weight = "400";
					break;
				case "입금완료":
					font_color = "red";
					font_weight = "bold";
					break;
			}

			return {
				'color': font_color,
				'font-weight': font_weight
			}
		}
	}

	function StylePayStats(params){
		if(params.value !== undefined){
			var font_color = "#0000";
			switch(params.data.pay_stat){
				case "입금":
					font_color = "black";
					break;
				case "입금보류":
					font_color = "red";
					break;
			}

			return {
				'color': font_color,
				'font-weight' : '400'
			}

		}
	}

	function StyleFontOrdState(params){
		if(params.value !== undefined){
			if(params.data.pay_stat == "예정"){
				return {
					'color': '#FF0000'
				}
			}
		}
	}

	function setMemo(params){
		if (params.oldValue !== params.newValue) {
			var rowNode = params.node;

			rowNode.setSelected(true);

			memo_Change_id.push(rowNode.data.no);

			gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
			gx.gridOptions.api.refreshCells({rowNodes:[rowNode]});

			$("[name=memo_Change_id]").eq(rowNode.id).prop("checked", true);
			//gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);

		}
	}

	function SaveMemo(){
		var data = new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();

		selectedRowData.forEach( function(selectedRowData, index) {
			if(memo_Change_id.indexOf(selectedRowData.no)>=0){
				data.push({"no": selectedRowData.no, "memo" : selectedRowData.memo});
			}
		});

		if(data.length > 0){
			if(!confirm('메모를 저장 하시겠습니까?')) {return false;}
			$.ajax({
				async: true,
				type: 'put',
				url: `/head/order/ord06/save_memo`,
				data: {
					'data': data

				},
				success: function (data) {
					if( data.return_code == 1 ){
						alert("메모가 저장되었습니다.");
						Search();

					}
				},
				complete:function(){
						_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});
		}else{
			alert("메모를 작성해 주십시오.");
			return false;
		}
	}

	function ordStatsChange(val){
		var rowNode = gx.gridOptions.api.getRowNode(val);

		if(order_Change_id.indexOf(rowNode.data.no)>=0){
			const idx = order_Change_id.indexOf(rowNode.data.no);
			order_Change_id.splice(idx, 1);
			rowNode.setSelected(false);
		}else{
			order_Change_id.push(rowNode.data.no);
			rowNode.setSelected(true);
		}

	}


	function Pay(cmd){
		var is_data = 0;
		var checkOrdNo = new Array();
		var checkOrdInfo_arr = new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();

		is_data = order_Change_id.length;

		if(is_data == 0){
			alert("입금내역을 선택해 주십시오.");
			return false;
		}


		selectedRowData.forEach( function(selectedRowData, index) {

			if(order_Change_id.indexOf(selectedRowData.no) >= 0){

				checkOrdInfo_arr.push(selectedRowData);
			}

		});

		if(cmd == "hold"){
			//_row_pos = gx.getFixedRows();
			_row_pos = checkOrdInfo_arr;
			_proc_pay_cnt = 0;
			PayHold();

			/*

			*/
		} else if(cmd == "order"){

			if(confirm('입금확인 하시겠습니까?')){

				_row_pos = checkOrdInfo_arr;
				_proc_pay_cnt = 0;

				PayOrder();

			}
		}
	}

	function PayOrder(){
		var ord_no = "";
		var bankda_no = "";
		var memo = "";
		var card_msg = "";
		var ord_opt_no = "";
		var is_data = false;
		var num = 0;
		var row_num = _row_pos.length;
		row = new Array();

		for(i = _proc_pay_cnt; i < row_num; i++){

			 if(_row_pos[i].pay_stat != ""){
				ord_no = _row_pos[i].ord_no;
				bankda_no = _row_pos[i].no;
				memo = _row_pos[i].memo;
				is_data = true;
				row = _row_pos[i];
				_row_pos = _row_pos[i];


				break;

			 }
		}

		if(ord_no == "선택"){
			alert("주문번호가 설정되어 있지 않습니다.");
			_row_pos = "";
			_proc_pay_cnt = 0;
			return false;
		}


		if(is_data == true){
			gx.gridOptions.api.forEachNode(function (node) {
				console.log(node)
                if(node.data.ord_no == ord_no){
                    node.setDataValue('pay_stat', "입금확인중...");
                }
            });

			$.ajax({
				async: true,
				type: 'put',
				url: `/head/order/ord06/pay`,
				data: {
					'ord_no': ord_no,
					'bankda_no': bankda_no,
					'memo': memo

				},
				success: function (data) {
					cbPay(data);
				},
				complete:function(){
						_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");


					// console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});


		}else{
			if(_proc_pay_cnt > 0){
				//alert('선택한 주문을 ' + _proc_pay_cnt + '건 입금확인 하였습니다.');
				alert('선택한 주문을 입금확인 하였습니다.');
				Search();
			}
		}


	}

	function cbPay(res){
		var ret = res.return_code;

		if(ret == "1")
		{
			gx.gridOptions.api.forEachNode(function (node) {
				if(node.data.no == row.no){
                    node.setDataValue('ord_state', "출고요청...");
					node.setDataValue('pay_stat', "입금");
                }
			});
			//_row_pos++;
			_proc_pay_cnt++;
			PayOrder();
		}
		else if(ret == "2")
		{
			gx.gridOptions.api.forEachNode(function (node) {
				if(node.data.no == row.no){
                    node.setDataValue('ord_state', "입금완료");
					node.setDataValue('pay_stat', "입금(품절)");
                }
			});

			//_row_pos++;
			_proc_pay_cnt++;
			PayOrder();
		}
		else if(ret == "0")
		{
			alert('입금된 주문입니다.');
		}
		else
		{
			alert('입금처리 시 오류가 발생하였습니다. 다시 입금확인해 주십시오.');
		}
	}

	function PayHold(){

		var bankda_no = "";
		var is_hold = "N";
		var is_data = false;
		var checkOrdInfo_arr= new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();

		var data = new Array();


		selectedRowData.forEach( function(selectedRowData, index) {
			if(order_Change_id.indexOf(selectedRowData.no) >= 0){
				if(selectedRowData.is_hold == 'N'){
					data.push(selectedRowData);
					checkOrdInfo_arr.push(selectedRowData);

				}else{
					is_hold = 'Y';
				}
			}

		});


		if(is_hold == "Y"){
			alert("이미 입금보류처리되었습니다.");
			return false;
		}

		$.ajax({
			async: true,
			type: 'put',
			url: `/head/order/ord06/pay_hold`,
			data: {
				'data': data
			},
			success: function (data) {
				//cbPayHold(data);
				if(data.return_code!=1){
					alert('입금처리 시 오류가 발생하였습니다. 다시 입금확인해 주십시오.');
				}else{
					alert('선택한 주문을 입금보류 하였습니다.');
					Search();
				}

			},
			complete:function(){
					_grid_loading = false;
			},
			error: function(request, status, error) {
				alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

			}
		});
	}



	// 입금은행 계좌 관리 팝업
	function PopAccount() {
		var url = "/head/order/ord06/account";
		const Com=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=800,height=500");
	}



	function goAccountLog(a){
		var bkdate = $(a).attr('data-code');

		var url = "/head/order/ord06/account_log/"+bkdate;
		const Pop=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1150,height=500");

	}

	var row_idx = 0;
	// 주문내역
	function goOrderList(idx){
		order_Change_id.push(idx);
		var url = "/head/order/ord01?o=pop&ismt=Y";
		row_idx = idx;
		var width = 1920;
		var height = 700;
		const Pop=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width="+width+",height="+height);
	}

	function UserFromToDate(val, form_nm, rt_name1, rt_name2){

		var now = new Date();
		var return_sdate = new Date();
		var return_edate = new Date();


		if(val != "0"){
			switch(val){
				case "1D":
					return_sdate = now;
					break;
				case "2D":
					return_sdate.setDate(now.getDate() - 1);
					break;
				case "7D":
					return_sdate.setDate(now.getDate() - 7);
					break;
				case "14D":
					return_sdate.setDate(now.getDate() - 14);
					break;
				case "1M":
					return_sdate.setMonth(now.getMonth() - 1);
					break;
				case "0R":
					var sdate = new Date(now.getFullYear() , now.getMonth(), 1);
					return_sdate = new Date(now.getFullYear() , now.getMonth(), 1);
					break;
				case "1R":
					var prev_month = new Date();
					prev_month.setMonth(now.getMonth() - 1);
					return_sdate = new Date(prev_month.getFullYear(), prev_month.getMonth(), 1);
					return_edate = new Date(now.getFullYear(), now.getMonth(), 0);
					break;
			}
		}

		var syear = return_sdate.getFullYear();
		var smonth = (return_sdate.getMonth()+1);
		var sdate = return_sdate.getDate();

		var eyear = return_edate.getFullYear();
		var emonth = (return_edate.getMonth()+1);
		var edate = return_edate.getDate();

		if(smonth<10){
			smonth = "0"+smonth;
		}

		if(sdate<10){
			sdate = "0"+sdate;
		}

		if(emonth<10){
			emonth = "0"+emonth;
		}

		if(edate<10){
			edate = "0"+edate;
		}

		$("[name="+ rt_name1 +"]").val(syear +"-"+ smonth +"-"+ sdate);
		$("[name="+ rt_name2 +"]").val(eyear +"-"+ emonth +"-"+ edate);

	}

	function SetOrd(ord_no, ord_opt_no){

		gx.gridOptions.api.forEachNode(function (node) {
			if(node.data.no == row_idx){
				node.setDataValue('ord_no', ord_no.join(','));
			}
		});


	}


	function StyleOrdNoSet(params){
		if(params.data.is_matched == 'N' && row_idx == params.data.no && params.data.ord_no != '선택'){
			return {
					'color': "#FFFFFF",
					'background-color' : '#F90214',
					'font-weight' : 'bold'
				}
		}

	}

	function setOrdNoBg(params){
		if (params.oldValue !== params.newValue && params.newValue != "") {
			var rowNode = params.node;

			rowNode.setSelected(true);
			//$("#check_"+rowNode.id).prop("checked", true);
			$("checkbox[name=check_"+rowNode.id+"]").attr("checked", true);
			//memo_Change_id.push(rowNode.id);
			gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
			gx.gridOptions.api.refreshCells({rowNodes:[rowNode]});


		}
	}

</script>
@stop
