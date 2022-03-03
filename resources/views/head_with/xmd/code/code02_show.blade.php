@extends('head_with.layouts.layout-nav')
@section('title','XMD - 매장 등록')
@section('content')

<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3">
	<form method="post" name="search">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">XMD - 매장 등록</a>
				</div>
				<div class="card-body mt-1">
					<div class="row_wrap">

						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="30%">
										</colgroup>
										<tbody>
											<tr>
												<th>파일</th>
												<td>
													<input id="excelfile" type="file" name="excelfile" />
													<a href="#" onclick="Upload();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">자료 불러오기</a>
													<span class="pl30">※ XMD >> 코드관리 >> 매장리스트(본사)에서 엑셀 다운로드</span>
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

		</div>

		<!-- DataTales Example -->
		<div class="card shadow mb-4 last-card pt-2 pt-sm-0">
			<div class="card-body">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
						</div>
						<div class="fr_box flax_box" style="font-size:12px;font-weight:700;color:#FF0000;">
							※ 저장 - 자료 없슴:신규등록, 자료 있슴:업데이트
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="height:calc(100vh - 280px);width:100%;" class="ag-theme-balham"></div>
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
		{headerName: "#",			field: "num",			filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
		{headerName:"No",			field:"no",				width:75},
		{headerName:"매장구분",		field:"com_type_nm",	width:90},
		{headerName:"매장코드",		field:"com_id",			width:90},
		{headerName:"매장명",		field:"com_nm",			width:90},
		{headerName:"매장종류",		field:"store_kind_nm",	width:100},
		{headerName:"전화",			field:"phone",			width:100},
		{headerName:"모바일",		field:"mobile",			width:100},
		{headerName:"FAX",			field:"fax",			width:100},
		{headerName:"우편번호",		field:"zipcode",		width:100},
		{headerName:"주소",			field:"addr",			width:100},
		{headerName:"개장일",		field:"sdate",			width:100},
		{headerName:"폐점일",		field:"edate",			width:100},
		{headerName:"매니저",		children:[
			{headerName:"매니저명",	field:"manager_nm",		width:100},
			{headerName:"시작일",	field:"manager_sdate",	width:100},
			{headerName:"종료일",	field:"manager_edate",	width:100},
		]},
		{headerName:"매니저보증금",	field:"manager_deposit",width:100},
		{headerName:"매니저수수료",	children:[
			{headerName:"정상",		field:"manager_fee",	width:100},
			{headerName:"행사",		field:"manager_sfee",	width:100},
		]},
		{headerName:"보증금",		children:[
			{headerName:"현금",		field:"deposit_cash",	width:100},
			{headerName:"담보",		field:"deposit_coll",	width:100},
		]},
		{headerName:"인테리어",		children:[
			{headerName:"비용",		field:"interior_cost",	width:100},
			{headerName:"부담",		field:"interior_burden",width:100},
		]},
		{headerName:"기본수수료",	field:"fee",			width:100},
		{headerName:"판매수수료율",	field:"sale_fee",		width:100},
		{headerName:"사용여부",		field:"use_yn",			width:90}
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

/*		
	gridOptions.api.sizeColumnsToFit();
	const remInPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
	gridOptions.columnApi.getAllColumns().forEach(function (column) {
		//gridOptions.columnApi.autoSizeColumn(column.colId, false);
		//console.log(column.colDef.width);
		if(column.colDef.width == undefined){
			const hn = column.colDef.headerName;
			const hnWidth = hn.length*3*remInPixel;
			//console.log(hn + ' - ' + hnWidth);
			gridOptions.columnApi.setColumnWidth(column.colId,hnWidth);
		} else {
		}
		//console.log(column.colId);
		//allColumnIds.push(column.colId);
	});
*/

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
			alert('엑셀파일을 입력하여 주십시오.');
			return false;
		}

		$.ajax({
			async: true,
			type: 'put',
			url: '/head/xmd/code/code02/show',
			data: {
				data : JSON.stringify(GridData),
			},
			success: function (data) {
				if( data.code == "200" )
				{
					alert("매장 정보가 등록(수정)되었습니다.");
					window.opener.Search();
					self.close();
				} 
				else 
				{
					alert("데이터 등록(수정)이 실패하였습니다.");
				}
			},
			error: function(request, status, error) {
				alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
				console.log(status);
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
			'A':"no",
			'B':"com_type_nm",
			'C':"com_id",
			'D':"com_nm",
			'E':"store_kind_nm",
			'F':"phone",
			'G':"mobile",
			'H':"fax",
			'I':"zipcode",
			'J':"addr",
			'K':"sdate",
			'L':"edate",
			'M':"manager_nm",
			'N':"manager_sdate",
			'O':"manager_edate",
			'P':"manager_deposit",
			'Q':"manager_fee",
			'R':"manager_sfee",
			'S':"deposit_cash",
			'T':"deposit_coll",
			'U':"interior_cost",
			'V':"interior_burden",
			'W':"fee",
			'X':"sale_fee",
			'Y':"use_yn"
		};


		// start at the 2nd row - the first row are the headers
		var rowIndex = 3;

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
			url: '/head/xmd/code/code02/upload', // point to server-side PHP script
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