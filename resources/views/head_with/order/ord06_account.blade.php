@extends('head_with.layouts.layout-nav')
@section('title','계좌관리')
@section('content')

<div class="container-fluid py-3">
	<form name="f1">
		<input type="hidden" name="number[]">
		<input type="hidden" name="bkname[]">
		<input type="hidden" name="bankda_id[]">
		<input type="hidden" name="bankda_pwd[]">
		<input type="hidden" name="use_yn[]">
		<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
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
	</form>
	<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
		<div class="card-body">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box">
						<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('add');">추가</a>
						<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('save')">저장</a>
						<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Cmder('del');">삭제</a>
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
			{
				headerName: '',
				headerCheckboxSelection: true,
				checkboxSelection: true,
				
				width:40,
				cellRenderer: function(params) {
					return "<input type='checkbox' checked />";
				},
				onSelectionChanged: selectionCheck,
				onSuppressRowClickSelection: function(){
				}
			},
			{field:"number" , headerName:"계좌번호", width:120, editable: true, onCellValueChanged:addRowCheck},
			{field:"bkname",headerName:"은행명", width:60, editable: true,},
			{field:"bankda_id",headerName:"뱅크다 아이디", width:90, editable: true,},


			{field:"bankda_pwd",headerName:"뱅크다 패스워드",width:85,editable: true, },
			{field:"use_yn" , headerName:"사용여부", width:80, },
			{field:"ut" , headerName:"등록일시", width:120, },
			{field:"rt" , headerName:"수정일시", width:200, },
			{field:"no", headName: "no", hide:true}

	];

	const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);
	gx.gridOptions.suppressRowClickSelection = true;
    pApp.ResizeGrid();

	function Search(page) {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/order/ord06/get_account_list', formData, page, ordCallback);
    }

	function ordCallback(data){
		gx.gridOptions.onSelectionChanged = function (event) {
//			console.log('onSelectionChanged');
		};

		//gx.gridOptions.api.suppressRowClickSelection = true;

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

</script>
@stop
