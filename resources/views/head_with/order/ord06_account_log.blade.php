@extends('head_with.layouts.layout-nav')
@section('title','입금수집내역 조회')
@section('content')


<div class="container-fluid show_layout pt-3">
	<div class="page_tit d-flex align-items-center justify-content-between mb-0">
		<div>
			<h3 class="d-inline-flex">계좌관리</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 주문</span>
				<span>/ 입금내역(뱅크다)</span>
				<span>/ 계좌관리</span>
			</div>
		</div>
		<div>
			<a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
		</div>
	</div>
</div>

<div class="p-3">
	<div id="search-area" class="search_cum_form">
		<form method="get" name="search">
			<input type="hidden" name="page" id="1page" value="">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					</div>
				</div>
				<div class="card-body">
					<!-- 수집일자/성공여부  -->
					<div class="row">
						<!-- 수집일자 -->
						<div class="col-lg-6 inner-td">
							<div class="form-group">
								<label for="user_yn">수집일자</label>
								<div class="form-inline date-select-inbox">
									<select name="date_type" class="form-control form-control-sm" onchange="UserFromToDate(this.value, this.form, 'sdate', 'edate')" style="width:23%;margin-right:2%;">
										<option value="0">사용자</option>
										<option value="1D" selected>금일</option>
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
						<!-- 성공여부 -->
						<div class="col-lg-6 inner-td">
							<div class="form-group">
								<label for="type">성공여부</label>
								<div class="flax_box">
									<select name='success_yn' class="form-control form-control-sm">
										<option value=''>전체</option>
										@foreach($is_yn_item as $is_yn)
											<option value="{{ $is_yn -> code_id }}">{{ $is_yn -> code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card shadow mb-3">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</div>

<script language="javascript">

	const pageNo = -1;
	var checkVal = null;
	var newAc = new Array();
	var oriAc = new Array();
	var oriRowNum = 0;
	var columns = [
			// this row shows the row index, doesn't use any data from the row
			{
				headerName: '#',
				width:40,
				maxWidth: 100,
				// it is important to have node.id here, so that when the id changes (which happens
				// when the row is loaded) then the cell is refreshed.
				valueGetter: 'node.id',
				cellRenderer: 'loadingRenderer',
			},
			
			{field:"number" , headerName:"계좌번호", width:120, editable: true, onCellValueChanged:addRowCheck},
			{field:"bkname",headerName:"은행명", width:80, editable: true,},
			{field:"bkdate",headerName:"입금일자", width:130, editable: true,},


			{field:"record",headerName:"수집데이터 개수",width:110,editable: true, },
			{field:"description" , headerName:"수집결과", width:100, editable: true,
				cellRenderer: function(params) {
					return '<a href="#" onClick="viewLog('+ params.data.no +')">'+ params.value+'</a>'
				}
			},
			{field:"rt" , headerName:"수집시간", width:130, },
			{field:"admin_id" , headerName:"수집자아이디", width:100, },
			{field:"admin_name" , headerName:"수집자이름", width:100, },
			{field:"no", headName: "no", hide:true}

	];

	const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);
	gx.gridOptions.api.suppressRowClickSelection = true;
    pApp.ResizeGrid();

	function Search(page) { // here 
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/order/ord06/account_log_search', formData, page, ordCallback);
		console.log(gx.Request('/head/order/ord06/account_log_search', formData, page, ordCallback));
    }

	function ordCallback(data){
		gx.gridOptions.onSelectionChanged = function (event) {
//			console.log('onSelectionChanged');
		};

		gx.gridOptions.api.suppressRowClickSelection = true;

		oriRowNum = gx.gridOptions.api.getDisplayedRowCount();
		gx.gridOptions.api.forEachNode(function (node) {
			oriAc.push(node.data);
		});
	}
    
	$(function(){
		Search(pageNo);
	});

	function addRowCheck (params){
		var number_check = true;
		if (params.oldValue !== params.newValue) {
			checkVal = params.oldValue;
			var rowNode = params.node;
			var account = params.newValue;

			if(isNaN(account)== true){
				alert("숫자만 입력가능합니다.");
				if(account == ""){
					account = "0";
				}else{
					account = "";
				}
				number_check = false;
			}

			if(number_check){
				gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
				gx.gridOptions.api.refreshCells({rowNodes:[rowNode]});
			}else{
				rowNode.setDataValue('number', params.oldValue);
			}
			
			//gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);

		}
	}

	function Cmder(cmd){

		if(cmd =="add"){
			AddRow();
		} else if(cmd =="del"){
			Delete();
		} else if(cmd =="save"){
			if(validate()){
				Save();
			}
		}
	} 

	function selectionCheck(event){
		//console.log("onSelectionChanged");
	}

	function AddRow(){
		var Ac_arr = new Array();
		var new_row = [{
			"number" : '',
			"bkname" : '',
			"bankda_id" : '',
			"bankda_pwd" : '',
			"use_yn" : 'Y',
			"ut" : '',
			"rt" : '',
			"no" : '',

		}];

		gx.gridOptions.api.updateRowData({add: new_row});    
			
		selectionRow();

	}

	function Save(){
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var oriAc_len = oriAc.length;
		var data = new Array();
		var num = 0;
		
		var use_yn = new Array();
		//var data;
		
		selectedRowData.forEach( function(selectedRowData, index) {
			
            if(selectedRowData.number != ""){
				data[num] = selectedRowData;
				num++;
            }
        });
		
		//var sendData = data.serialize();
		$.ajax({
			async: true,
			type: 'put',
			url: `/head/order/ord06/save_account`,
			data: {
				'data': data

			},
			success: function (data) {
				//cbPay(data);
				cbCallBack(data);
			},
			complete:function(){
					_grid_loading = false;
			},
			error: function(request, status, error) {
				alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
				
				console.log("error");
				
			}
		});

	}

	function cbCallBack(res){
		if( res.result_code == 1 ){
			Search(pageNo);
		}
	}

	function Delete(){
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var data = new Array();
		var num = 0;

		selectedRowData.forEach( function(selectedRowData, index) {
			
            if(selectedRowData.number != ""){
				data[num] = selectedRowData;
				num++;

            }
        });

		if( data.length  == 0 ){
			alert("삭제할 계좌를 선택하십시오.");
			return false;
		}

		if(confirm("선택하신 계좌를 삭제 하시겠습니까?")){

			$.ajax({
				async: true,
				type: 'put',
				url: `/head/order/ord06/del_account`,
				data: {
					'data': data

				},
				success: function (data) {
					//cbPay(data);
					cbCallBack(data);
				},
				complete:function(){
						_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
					
					console.log("error")
					
				}
			});
		}
	}


	function validate(){
		var is_flag = true;
		gx.gridOptions.api.forEachNode(function (node) {
			if(is_flag == true){
				if(node.data.number == '' || node.data.bkname == '' || node.data.bankda_id == '' || node.data.bankda_pwd == ''){
					is_flag = false;
					alert("계좌정보를 입력해 주세요.");

				}
			}
		});
		return is_flag;
	}

	function selectionRow(){
		var rowCnt = gx.gridOptions.api.getDisplayedRowCount();
		
		for(i=oriRowNum; i<rowCnt; i++ ){
			var rowNode = gx.gridOptions.api.getRowNode(i);
			rowNode.setSelected(true);
		}
	}


	function viewLog(no){
		var url = "/head/order/ord06/pop_log/" + no;
		//openWindow(url,"view_log","resizable=yes,scrollbars=no",100,100);
		const Pop=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=500,height=500");
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
</script>
@stop
