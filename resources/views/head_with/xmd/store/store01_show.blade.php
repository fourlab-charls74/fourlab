@extends('head_with.layouts.layout-nav')
@section('title','XMD - 매장판매일보 등록')
@section('content')

<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3">
	<form method="post" name="search">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">XMD - 매장판매일보 등록</a>
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
													<span>※ XMD >> 매장관리 >> 매장판매일보(본사) 조회에서 엑셀 다운로드</span>
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
							※ 저장 - 자료 없음:신규등록, 자료 있음:업데이트
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
		{headerName:"판매일자",		field:"ord_date",		width:75},
		{headerName:"매장구분",		field:"com_type_nm",	width:90},
		{headerName:"매장코드",		field:"com_id",			width:90},
		{headerName:"매장명",		field:"com_nm",			width:100},
		{headerName:"영수번호",		field:"receipt_no",		width:100},
		{headerName:"주문일련번호",	field:"seq",			width:90},
		{headerName:"아이템코드",	field:"style_no",		width:100},
		{headerName:"품목",			field:"opt_kind_nm",	width:100},
		{headerName:"브랜드",		field:"brand_nm",		width:100},
		{headerName:"상품코드",		field:"goods_code",		width:100},
		{headerName:"상품명",		field:"goods_nm",		width:100},
		{headerName:"칼라",			field:"color",			width:100},
		{headerName:"칼라명",		field:"color_nm",		width:100},
		{headerName:"사이즈",		field:"size",			width:100},
		{headerName:"사이즈명",		field:"size_nm",		width:100},
		{headerName:"결제방법",		field:"stat_pay_type_nm",	width:100},
		{headerName:"택가",			field:"goods_sh",		width:100},
		{headerName:"판매가",		field:"price",			width:100},
		{headerName:"원가",			field:"wonga",			width:100},
		{headerName:"할인율",		field:"sale_rate",		width:100},
		{headerName:"판매유형",		field:"sell_type_nm",	width:100},
		{headerName:"주문가",		field:"ord_amt",		width:100},
		{headerName:"주문할인율",	field:"ord_sale_rate",	width:100},
		{headerName:"할인율차이",	field:"sale_gap",		width:100},
		{headerName:"차이",			field:"gap",			width:100},
		{headerName:"주문수량",		field:"qty",			width:100},
		{headerName:"주문합계",		field:"recv_amt",		width:100},
		{headerName:"순판매금액",	field:"act_amt",		width:100},
		{headerName:"택가합계",		field:"tag_sum",		width:100},
		{headerName:"행사구분",		field:"event_kind_nm",	width:100},
		{headerName:"마진",			field:"pay_fee",		width:100},
		{headerName:"매장마진",		field:"store_pay_fee",	width:100},
		{headerName:"주문자ID",		field:"user_id",		width:100},
		{headerName:"주문자명",		field:"ord_nm",			width:100},
		{headerName:"주문자명2",	field:"ord_nm2",		width:100},
		{headerName:"비고",			field:"comment",		width:100},
		{headerName:"바코드",		field:"barcode",		width:100},
		{headerName:"등록자정보",	field:"admin_nm",		width:100},
		{headerName:"등록일",		field:"reg_date",		width:100},
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

		//console.log(JSON.stringify(GridData));
		//return false;

		$.ajax({
			async: false,
			type: 'post',
			url: '/head/xmd/store/store01/show',
			data: {
				data : JSON.stringify(GridData),
			},
			success: function (data) {
				if( data.code == "200" )
				{
					alert("매장 판매일보가 등록(수정)되었습니다.");
					window.opener.Search();
					self.close();
				} 
				else 
				{
					alert("데이터 등록(수정)이 실패하였습니다.");
				}
			},
			error: function(request, status, error) {
				alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.11" + request + status + error);
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
			'A':"ord_date",		
			'B':"com_type_nm",	
			'C':"com_id",			
			'D':"com_nm",			
			'E':"receipt_no",		
			'F':"seq",		
			'G':"style_no",		
			'H':"opt_kind_nm",	
			'I':"brand_nm",		
			'J':"goods_code",		
			'K':"goods_nm",		
			'L':"color",			
			'M':"color_nm",		
			'N':"size",			
			'O':"size_nm",		
			'P':"stat_pay_type_nm",	
			'Q':"goods_sh",		
			'R':"price",			
			'S':"wonga",			
			'T':"sale_rate",		
			'U':"sell_type_nm",	
			'V':"ord_amt",		
			'W':"ord_sale_rate",
			'X':"sale_gap",		
			'Y':"gap",			
			'Z':"qty",			
			'AA':"recv_amt",		
			'AB':"act_amt",
			'AC':"tag_sum",		
			'AD':"event_kind_nm",
			'AE':"pay_fee",		
			'AF':"store_pay_fee",
			'AG':"user_id",		
			'AH':"ord_nm",			
			'AI':"ord_nm2",		
			'AJ':"comment",		
			'AK':"barcode",		
			'AL':"admin_nm",		
			'AM':"reg_date",		
		};


		// start at the 2nd row - the first row are the headers
		var rowIndex = 7;

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
			url: '/head/xmd/store/store01/upload', // point to server-side PHP script
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