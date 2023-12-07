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
														<option value='00'>00 : 변경없음</option>
														<option value='01'>01 : 정상매장</option>
														<option value='02'>02 : 전매장</option>
														<option value='03'>03 : 이월취급점</option>
														<option value='04'>04 : 아울렛전용</option>
													</select>
												</div>
											</td>
											<th>샘플파일</th>
											<td><a href="/sample/sample_prd05.xlsx"> sample_prd05.xlsx</a></td>
										</tr>
										<tr>
											<th class="required">파일</th>
											<td colspan="3">
												<div class="d-flex flex-column">
													<div class="d-flex" style="width:100%;">
														<input id="excel_file" type="file" name="excel_file" class="mr-2" />
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
				<div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
					<a href="#">상품정보</a>
				</div>
				<div class="card-body">
					<div class="table-responsive mt-2">
						<div id="div-gd" class="ag-theme-balham"></div>
					</div>
				</div>
			</div>
		</div>

		<script language="javascript">
			let columns = [
				{headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
				{field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
				{field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
				{field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
				{field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
				{field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
				{field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
				{field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
				{field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
				{field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
				{field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
				{field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
				{field: "goods_opt", headerName: "옵션", width: 153},
				{field: "goods_sh", headerName: "정상가", type: "currencyType", width: 65},
				{field: "price", headerName: "현재가", type: "currencyType", width: 65},
				{field: "price_kind", headerName: "가격기준", width: 65, cellStyle: {"text-align": "center"}},
				{field: "change_kind", headerName: "변경기준", width: 65, cellStyle: {"text-align": "center"}},
				{field: "change_val_rate", headerName: "변경금액(율)", width: 80, type: "currencyType", cellStyle: {"text-align": "center"},
					cellRenderer : function(params) {
						if (params.data.change_kind == '%') {
							return params.data.change_val_rate + '%';
						} else {
							return Comma(params.data.change_val_rate);
						}
					}
				},
				{field: "change_val", headerName: "가격", type: "currencyType", width: 80, cellStyle: {'background' : '#FFDFDF'}},
				{width : 'auto'}
			];
		</script>

		<script type="text/javascript" charset="utf-8">
			let add_product = [];
			let gx;
			const pApp = new App('', { gridId: "#div-gd" });

			$(document).ready(function() {
				pApp.ResizeGrid(430);
				pApp.BindSearchEnter();
				let gridDiv = document.querySelector(pApp.options.gridId);
				gx = new HDGrid(gridDiv, columns);
				$('#cur_date').hide();
			});
		</script>

		<script type="text/javascript" charset="utf-8">

			$(document).ready(function() {
				gx.gridOptions.api.setRowData([]);
				$('#cur_date').hide();
			});

			function Save() {

				let change_date_res	= $('#change_date_res').val();
				let change_date_now	= document.getElementById('change_date_now').innerText;
				let type			= $("input[name='product_price_type']:checked").val();
				let plan_category	= $('#plan_category').val();
				let rows = gx.getSelectedRows();

				if(rows.length < 1)	return alert('가격을 변경할 상품을 선택해주세요.');

				if(!confirm("선택한 상품의 가격을 변경하시겠습니까?")) return;

				axios({
					url: '/store/product/prd05/batch-update',
					method: 'put',
					data: {
						data: rows,
						change_date_res : change_date_res,
						change_date_now : change_date_now,
						change_cnt : rows.length,
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
			const convertDataToWorkbook = (data) => {
				/* convert data to binary string */
				data = new Uint8Array(data);
				const arr = new Array();

				for (let i = 0; i !== data.length; ++i) {
					arr[i] = String.fromCharCode(data[i]);
				}

				const bstr = arr.join("");

				return XLSX.read(bstr, {type: "binary"});
			};

			const makeRequest = (method, url, success, error) => {
				let httpRequest = new XMLHttpRequest();
				httpRequest.open("GET", url, true);
				httpRequest.responseType = "arraybuffer";

				httpRequest.open(method, url);
				httpRequest.onload = () => {
					success(httpRequest.response);
				};
				httpRequest.onerror = () => {
					error(httpRequest.response);
				};
				httpRequest.send();
			};

			const populateGrid = async (workbook) => {
				let firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
				let worksheet = workbook.Sheets[firstSheetName];

				let columns	= {
					'A': 'prd_cd',
					'B': 'price_kind',
					'C': 'change_kind',
					'D': 'change_val'
				};

				let firstRowIndex = 6; // 엑셀 2행부터 시작 (샘플데이터 참고)
				let rowIndex = firstRowIndex;

				let count = gx.gridOptions.api.getDisplayedRowCount();
				let rows = [];
				while (worksheet['A' + rowIndex]) {
					let row = {};
					Object.keys(columns).forEach(function(column) {
						let item = worksheet[column + rowIndex];
						if(item !== undefined && item.w) {
							row[columns[column]] = item.w;
						}
					});

					rows.push(row);
					row = { ...row,
						count: ++count, isEditable: true,
					};
					rowIndex++;
				}
				if(rows.length < 1) return alert("한 개 이상의 상품정보를 입력해주세요.");
				rows = rows.filter(r => r.prd_cd);
				let values = { data: rows };
				await getGood(values, firstRowIndex);
			}

			const Upload = () => {
				const file_data = $('#excel_file').prop('files')[0];
				if(!file_data) return alert("적용할 파일을 선택해주세요.");

				const form_data = new FormData();
				form_data.append('file', file_data);
				form_data.append('_token', "{{ csrf_token() }}");

				alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");

				axios({
					method: 'post',
					url: '/store/product/prd05/upload',
					data: form_data,
					headers: {
						"Content-Type": "multipart/form-data",
					}
				}).then(async (res) => {
					gx.gridOptions.api.setRowData([]);
					if (res.data.code == 1) {
						const file = res.data.file;
						await importExcel("/" + file);
					} else {
						console.log(res.data.message);
						console.log('실패');
					}
				}).catch((error) => {
					console.log(error);
				});

				return false;
			}

			const getGood = async (values, firstIndex) => {
				axios({
					url: '/store/product/prd05/batch-getgoods',
					method: 'post',
					data: values,
				}).then(async (res) => {
					if (res.data.code == 200) {
						await gx.gridOptions.api.applyTransaction({add : res.data.body});
					} else {
						alert("상품정보조회 중 오류가 발생했습니다.\n다시 시도해주세요.");
					}
				}).catch((error) => {
					console.log(error);
				});
			};

			const importExcel = async (url) => {
				await makeRequest('GET',
					url,
					// success
					async (data) => {
						const workbook = convertDataToWorkbook(data);
						await populateGrid(workbook);
					},
					// error
					(error) => {
						console.log(error);
					}
				);
			};


		</script>
@stop
