@extends('store_with.layouts.layout-nav')
@section('title','회원 일괄 업로드')
@section('content')

<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3">
	<form method="post" name="search">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">회원 관리 - 일괄업로드</a>
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
													<span class="pl30">※ XMD >> 고객/수선관리 >> 고객리스트에서 엑셀 다운로드</span>
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
		{headerName: "#",			field: "num",		filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
		{headerName:"고객코드",		field:"user_code",	width:75},
		{headerName:"고객명",		field:"user_nm",	width:75},
		{headerName:"등급코드",		field:"grade_code",	width:75},
		{headerName:"고객등급",		field:"grade_nm",	width:75},
		{headerName:"성별",			field:"sex",		width:65},
		{headerName:"모바일",		field:"mobile",		width:85},
		{headerName:"이메일",		field:"email",		width:75},
		{headerName:"적립금",		field:"point",		width:65},
		{headerName:"총구매금액",	field:"ord_amt",	width:90},
		{headerName:"총구매횟수",	field:"ord_cnt",	width:90},
		{headerName:"최근구매일",	field:"last_ord_date",	width:90},
		{headerName:"가입매장",		field:"store_nm",	width:120},
		{headerName:"가입일",		field:"rt",			width:70},
		{headerName:"생일",			field:"birth_date",	width:60},
		{headerName:"우편번호",		field:"zip",		width:75},
		{headerName:"주소1",		field:"addr",		width:150},
		{headerName:"주소2",		field:"addr2",		width:150},
		{headerName:"메모",			field:"memo",		width:150},
	];

	// let the grid know which columns to use
	var gridOptions = {
		columnDefs: columnDefs,
		defaultColDef: {
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
			alert('엑셀파일을 입력하여 주십시오.');
			return false;
		}

		$.ajax({
			async: true,
			type: 'put',
			url: '/store/member/mem01/batch',
			data: {
				data : JSON.stringify(GridData),
			},
			success: function (data) {
				if( data.code == "200" )
				{
					alert("회원 정보가 등록(수정)되었습니다.");
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
			'B':"check",
			'C':"user_code",
			'D':"user_nm",
			'E':"grade_code",
			'F':"grade_nm",
			'G':"sex",
			'H':"age_p",
			'I':"age",
			'J':"card_no",
			'K':"mobile",
			'L':"email",
			'M':"point",
			'N':"use_point",
			'O':"p_ord_amt",
			'P':"ord_amt",
			'Q':"p_use_point",
			'R':"coupon_amt",
			'S':"p_ord_cnt",
			'T':"ord_cnt",
			'U':"avg_ord_amt",
			'V':"last_ord_date",
			'W':"store_nm",
			'X':"rt",
			'Y':"sl_birth_chk",
			'Z':"birth_date",
			'AA':"married_date",
			'AB':"tel_kind",
			'AC':"tel_home",
			'AD':"tel_office",
			'AE':"zip",
			'AF':"addr",
			'AG':"addr2",
			'AH':"o_zip",
			'AI':"o_addr",
			'AJ':"o_addr2",
			'AK':"memo"
		};


		// start at the 2nd row - the first row are the headers
		var rowIndex = 4;

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
			url: '/store/member/mem01/upload', // point to server-side PHP script
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