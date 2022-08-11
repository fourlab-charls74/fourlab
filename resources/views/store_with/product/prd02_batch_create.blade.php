@extends('head_with.layouts.layout-nav')
@section('title','상품코드 일괄 등록')
@section('content')

<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">상품코드 일괄 등록</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품관리</span>
				<span>/ 상품관리(재고)</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0)" onclick="addPrdCd();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
			<a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
		</div>
	</div>

	<style> 
		.required:after {content:" *"; color: red;}
		.table th {min-width:120px;}

		@media (max-width: 740px) {
			.table td {float: unset !important;width:100% !important;}
		}
	</style>

	<form method="post" name="search">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<div class="table-box-ty2 mobile">
								<table class="table incont table-bordered" width="100%" cellspacing="0">
									<tbody>
										<tr>
											<th class="required">파일</th>
											<td style="width:35%;">
												<div class="d-flex flex-column">
													<div class="d-flex" style="width:100%;">
														<input id="excelfile" type="file" name="excelfile" class="w-50 mr-2" />
														<button type="button" class="btn btn-outline-primary" onclick="Upload();"><i class="fas fa-sm"></i>자료 불러오기</button>
													</div>
												</div>
											</td>
											<th>샘플파일</th>
											<td style="width:35%;"><a href="/data/head/sample/상품매칭_샘플.xlsx"> 상품매칭_샘플.xlsx</a></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="card-title">
						<h6 class="mt10 m-0 font-weight-bold text-primary fas fa-question-circle"> 주의사항</h6>
					</div>
					<ul class="mb-0">
						<li>- 해당 상품번호에 이미 매칭된 상품코드가 있다면 삭제됩니다!</li>
					</ul>
				</div>

				<!-- DataTales Example -->
				<div class="card-body pt-2">
					<div class="card-title">
						<div class="filter_wrap">
							<div class="fl_box">
								<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
					</div>
				</div>

			</div>

		</div>
	</form>

	<div class="resul_btn_wrap mt-3 d-block">
		<a href="#" onclick="Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
		<a href="#" onclick="window.close()" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
	</div>

</div>



<script language="javascript">
	var columnDefs = [
		{headerName: "#", field: "num",filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
		{headerName:"코드",field:"xmd_code", width:150},
		{headerName:"상품번호",field:"goods_no", width:100},
		{headerName:"옵션",field:"goods_opt", width:250},
	];

	// let the grid know which columns to use
	var gridOptions = {
		columnDefs: columnDefs,
		defaultColDef: {
			// set every column width
			//flex: 1,
			//width: 100,
			// make every column editable
			editable: true,
			resizable: true,
			autoHeight: true,
			// make every column use 'text' filter by default
			filter: 'agTextColumnFilter'
		},
		rowSelection:'multiple',
		rowHeight: 275,
	};

	// lookup the container we want the Grid to use
	var eGridDiv = document.querySelector('#div-gd');

	new agGrid.Grid(eGridDiv, gridOptions);

</script>
<script type="text/javascript" charset="utf-8">

	$(document).ready(function() {
		gridOptions.api.setRowData([]);
	});


	var GridData = [];

	/**
	 * @return {boolean}
	 */
	function Save() 
	{
		//console.log(GridData);
		//return;

		var frm = $('form');

		if(GridData.length === 0){
			alert('입력할 자료를 선택해 주십시오.');
			return false;
		}

		//$('input[name="data"]').val(JSON.stringify(GridData));
		//frm.submit();

		// console.log(frm.serialize());

		$.ajax({
			async: true,
			type: 'put',
			url: '/head/stock/stk30/show',
			data: {
				data : JSON.stringify(GridData),
			},
			success: function (data) {
				if( data.code == "200" )
				{
					alert("상품 매칭 데이터가 등록되었습니다.");
					window.opener.Search();
					self.close();
				} 
				else 
				{
					alert("데이터 등록이 실패하였습니다.");
				}
			},
			error: function(request, status, error) {
				alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
				console.log("error");
			}
		});

	}

	// read the raw data and convert it to a XLSX workbook
	function convertDataToWorkbook(data) {
		/* convert data to binary string */
		var data = new Uint8Array(data);
		var arr = new Array();

		for (var i = 0; i !== data.length; ++i) {
			arr[i] = String.fromCharCode(data[i]);
		}

		var bstr = arr.join("");

		return XLSX.read(bstr, {type: "binary"});
	}

	function makeRequest(method, url, success, error) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.open("GET", url, true);
		httpRequest.responseType = "arraybuffer";

		httpRequest.open(method, url);
		httpRequest.onload = function () {
			success(httpRequest.response);
		};
		httpRequest.onerror = function () {
			error(httpRequest.response);
		};
		httpRequest.send();
	}

	function populateGrid(workbook) {
		// our data is in the first sheet
		var firstSheetName = workbook.SheetNames[0];
		var worksheet = workbook.Sheets[firstSheetName];

		var columns	= {
			'A': 'xmd_code',
			'B': 'goods_no',
			'C': 'goods_opt',
		};


		// start at the 2nd row - the first row are the headers
		var rowIndex = 2;

		var rowData = [];

		// iterate over the worksheet pulling out the columns we're expecting
		while (worksheet['A' + rowIndex]) {
			var row = {};
			Object.keys(columns).forEach(function(column) {
				//console.log(worksheet[column + rowIndex]);
				if(worksheet[column + rowIndex] !== undefined){
					row[columns[column]] = worksheet[column + rowIndex].w;
				}
			});

			rowData.push(row);


			rowIndex++;
		}

		GridData = rowData;

		// finally, set the imported rowData into the grid
		gridOptions.api.setRowData(rowData);

		//토탈 갯수 보여주기
		$("#gd-total").text(rowData.length);
	}

	function Upload(){
		var file_data = $('#excelfile').prop('files')[0];
		var form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");
		$.ajax({
			url: '/head/stock/stk30/upload', // point to server-side PHP script
			dataType: 'json',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function(res){
				if(res.code == "200"){
					file = res.file;
					//alert(file);
					importExcel("/" + file);
				} else {
					console.log(res.errmsg);
				}
			},
			error: function(request, status, error) {
				console.log(error)
			}
		});
		return false;
	}

	function importExcel(url) {

		makeRequest('GET',
			//'https://www.ag-grid.com/example-excel-import/OlymicData.xlsx',
			url,
			// success
			function (data) {
			//console.log(data);
				var workbook = convertDataToWorkbook(data);
				//console.log(workbook);
				populateGrid(workbook);
			},
			// error
			function (error) {
				throw error;
			}
		);
	}


</script>


@stop