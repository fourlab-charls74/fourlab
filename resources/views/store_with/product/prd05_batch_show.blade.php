@extends('store_with.layouts.layout-nav')
@section('title','상품가격 변경[일괄]')
@section('content')

	<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>
	
	<div class="show_layout py-3 px-sm-3">
		<div class="page_tit d-flex justify-content-between">
			<div class="d-flex">
				<h3 class="d-inline-flex">상품가격 변경[일괄]</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 상품관리</span>
					<span>/ 상품가격 관리</span>
					<span>/ 상품가격 변경[일괄]</span>
				</div>
			</div>
			<div class="d-flex">
				<a href="javascript:void(0)" onclick="Save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
				<a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
			</div>
		</div>

		<style>
			.table th {min-width: 130px;}
			.table td {width: 50%;}

			@media (max-width: 740px) {
				.table td {float: unset !important;width: 100% !important;}
			}
		</style>

		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
					<a href="#">기본정보</a>
				</div>
				<div class="card-body">
					<form name="f1">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<tbody>
										<tr>
											<th class="required">상품가격변경 구분</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" name="product_price_type" value="reservation" id="reservation" class="custom-control-input" checked>
														<label class="custom-control-label" for="reservation">예약</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="product_price_type" value="now" id="now" class="custom-control-input">
														<label class="custom-control-label" for="now">즉시</label>
													</div>
												</div>
											</td>
											<th class="required">변경일자</th>
											<td>
												<div class="form-inline" id="sel_date">
													<div class="docs-datepicker form-inline-inner input_box w-100">
														<div class="input-group">
															<input type="text" class="form-control form-control-sm docs-date" name="change_date_res" id="change_date_res" value="{{$rdate}}" autocomplete="off">
															<div class="input-group-append">
																<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																</button>
															</div>
														</div>
														<div class="docs-datepicker-container"></div>
													</div>
												</div>
												<div class="form-inline" id="cur_date">
													<div class="docs-datepicker form-inline-inner input_box w-100">
														<div>
															<span id="change_date_now">{{$edate}}</span>
														</div>
														<div class="docs-datepicker-container"></div>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">상품운영 구분</th>
											<td>
												<div class="flax_box">
													<select name='plan_category' id="plan_category" class="form-control form-control-sm">
														<option value=''>00 : 변경없음</option>
														<option value='01'>01 : 정상매장</option>
														<option value='02'>02 : 전매장</option>
														<option value='03'>03 : 이월취급점</option>
														<option value='04'>04 : 아울렛전용</option>
													</select>
												</div>
											</td>
											<th>샘플파일</th>
											<td><a href="/data/store/sample/product_price_sample.xlsx"> product_price_sample.xlsx</a></td>
										</tr>
										<tr>
											<th class="required">파일</th>
											<td colspan="3">
												<div class="d-flex flex-column">
													<div class="d-flex" style="width:100%;">
														<input id="excelfile" type="file" name="excelfile" class="mr-2" />
														<button type="button" class="btn btn-outline-primary" onclick="Upload();"><i class="fas fa-sm"></i>자료 불러오기</button>
													</div>
												</div>
											</td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="card shadow mt-3">

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
						<div id="div-gd" style="height:calc(100vh - 460px);width:100%;" class="ag-theme-balham"></div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<script language="javascript">
		let columnDefs = [
			{field: "num", headerName: "#", filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
			{field: "prd_cd_p", headerName: "품번", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
			{field: "goods_sh", headerName: "정상가", type: "currencyType", width: 75},
			{field: "price", headerName: "현재가", type: "currencyType", width: 75},
			{field: "price_kind", headerName: "기준", width: 80},
			{field: "change_val", headerName: "변경금액(율)", type: "currencyType", width: 120 ,editable:true, cellStyle: {'background' : '#ffff99'}},
			{field: "change_kind", headerName: "율/액", width: 80 ,editable:true, cellStyle: {'background' : '#ffff99'}},
			{field: "change_price", headerName: "변경가", type: "currencyType", width: 75},
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

			$('#cur_date').hide();
		});

		var GridData = [];

		function Save() {

			let change_date_res	= $('#change_date_res').val();
			let change_date_now	= document.getElementById('change_date_now').innerText;
			let type			= $("input[name='product_price_type']:checked").val();
			let plan_category	= $('#plan_category').val();
			let change_cnt		= GridData.length;
			
			if(GridData.length === 0){
				alert('입력할 자료를 선택해 주십시오.');
				return false;
			}

			if(!confirm("등록한 상품의 변경금액(율)을 저장하시겠습니까?")) return;

			axios({
				url: '/store/product/prd05/batch-update',
				method: 'put',
				data: {
					data: JSON.stringify(GridData),
					change_date_res : change_date_res,
					change_date_now : change_date_now,
					change_cnt : change_cnt,
					type : type,
					plan_category : plan_category

				},
			}).then(function (res) {
				if(res.data.code === 200) {
					alert('상품가격 일괄변경 내용이 등록되었습니다.');
					window.close();
					opener.Search();
				} else {
					console.log(res.data);
					alert("상품가격 변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
				}
			}).catch(function (err) {
				alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
				console.log(err);
			});
		}
		
		$("input[name='product_price_type']").change(function(){
			let type = $("input[name='product_price_type']:checked").val();

			if (type == 'reservation') {
				$('#sel_date').show();
				$('#cur_date').hide();

			} else {
				$('#sel_date').hide();
				$('#cur_date').show();
			}
		});

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
				'A': 'prd_cd_p',
				'B': 'goods_sh',
				'C': 'price',
				'D': 'price_kind',
				'E': 'change_val',
				'F': 'change_kind',
				'G': 'change_price',
			};


			// start at the 2nd row - the first row are the headers
			var rowIndex = 2;

			var rowData = [];

			// iterate over the worksheet pulling out the columns we're expecting
			while (worksheet['A' + rowIndex]) {
				var row = {};
				Object.keys(columns).forEach(function(column) {
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
				url: '/store/product/prd05/upload', // point to server-side PHP script
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
					}else{
						alert('엑셀 파일 업로드 오류 입니다[1].');
						console.log(res.errmsg);
					}
				},
				error: function(request, status, error) {
					alert('엑셀 파일 업로드 오류 입니다[2].');
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
					var workbook = convertDataToWorkbook(data);
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
