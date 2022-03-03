@extends('head_with.layouts.layout-nav')
@section('title','XMD 재고파일 등록')
@section('content')

<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3">
    <!-- FAQ 세부 정보 -->
    <form method="post" name="search">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">XMD 재고파일 등록</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="94px">
										</colgroup>
										<tbody>
											<tr>
                                                <th>파일</th>
                                                <td>
                                                    <div class="flax_box">
														<input id="excelfile" type="file" name="excelfile" />
														<a href="#" onclick="Upload();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">자료 불러오기</a>
                                                    </div>
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
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="height:calc(100vh - 290px);width:100%;" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>

	</form>

    <div class="resul_btn_wrap mt-3 d-block">
        <a href="#" onclick="Save();" id="save_btn" class="btn btn-sm btn-primary submit-btn">저장</a>
        <a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
    </div>
</div>

<div id="img_loading" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);display:none;">
	<img src="/skin/images/loading.gif">
</div>

<script language="javascript">
	var columnDefs = [
		{headerName: "#",		field: "num",		width:50,	filter:true,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
		{headerName:"코드",		field:"cd",	width:100},
		{headerName:"상품명",	field:"goods_nm",	width:200},
		{headerName:"컬러",		field:"color",	width:120},
		{headerName:"판매가",	field:"price",	width:80, type:'numberType'},
		{headerName:"원가",		field:"cost",	width:80, type:'numberType'},
		{headerName:"수량",		field:"qty",	width:70, type:'numberType'},
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
			alert('입력할 자료를 선택해 주십시오.');
			return false;
		}

		//$('input[name="data"]').val(JSON.stringify(GridData));
		//frm.submit();

		// console.log(frm.serialize());

		ret	= confirm("입력하신 자료를 등록하시겠습니까?");

		if( ret )
		{
			$('#img_loading').show();

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/stock/stk33/show',
				data: {
					data : JSON.stringify(GridData),
				},
				success: function (data) {
					if( data.code == "200" )
					{
						alert(data.result_code);
						//window.opener.Search();
						window.opener.location.reload();
						self.close();
					} 
					else 
					{
						alert(data.code);
						alert("데이터 등록이 실패하였습니다.");
					}

					$('#img_loading').hide();
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					$('#img_loading').hide();
					console.log("error");
				}
			});
		}

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
			'A': 'cd',
			'B': 'goods_nm',
			'C': 'color',
			'D': 'price',
			'E': 'cost',
			'F': 'qty',
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
			url: '/head/stock/stk33/upload', // point to server-side PHP script
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