@extends('store_with.layouts.layout-nav')
@section('title', '상품코드 일괄매핑')
@section('content')

	<!-- import excel lib -->
	<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

	<div class="show_layout py-3 px-sm-3">
		<div class="page_tit d-flex justify-content-between">
			<div class="d-flex">
				<h3 class="d-inline-flex">상품코드 일괄매핑</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 상품관리</span>
					<span>/ 상품코드관리</span>
					<span>/ 일괄매핑</span>
				</div>
			</div>
			<div class="d-flex">
				<a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
			</div>
		</div>

		<style>
			.table th {min-width: 120px;}
			.table td {width: 25%;}

			@media (max-width: 740px) {
				.table td {float: unset !important;width: 100% !important;}
			}
		</style>

		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
					<a href="#">파일 업로드</a>
				</div>
				<div class="card-body">
					<form name="f1">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<tbody>
										<tr>
											<th>파일</th>
											<td class="w-100">
												<div class="flex_box">
													<div class="custom-file w-50">
														<input name="excel_file" type="file" class="custom-file-input" id="excel_file">
														<label class="custom-file-label" for="file"></label>
													</div>
													<div class="btn-group ml-2">
														<button class="btn btn-outline-primary apply-btn" type="button" onclick="upload();">적용</button>
													</div>
													<a href="/sample/sample_prd02_batch_mapping.xlsx" class="ml-2" style="text-decoration: underline !important;">상품코드 일괄매핑 양식 다운로드</a>
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
			<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
				<div class="card-body">
					<div class="card-title">
						<div class="filter_wrap d-flex justify-content-between">
							<div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0 mt-3">
								<a href="#">업로드 정보</a>
							</div>
							<div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-right">
								<div class="d-flex">
									<a href="#" onclick="RequestMapping();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>매핑등록</a> &nbsp;&nbsp;
									<a href="#" onclick="onRemoveSelected();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>삭제</a>
								</div>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script language="javascript">
		const pinnedRowData = [{ dep_store_cd: '합계', qty: 0 }];

		let columns = [
			{headerName: "No", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"},
				cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
			},
			{field: "chk",		headerName: '',			cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 28},
			{field: "prd_cd",	headerName: "바코드",	width: 150},
			{field: "goods_no",	headerName: "온라인코드",	width: 100, cellStyle: {"text-align": "center"}},
			{field: "goods_opt",headerName: "온라인옵션",	width: 250},
		];
	</script>

	<script type="text/javascript" charset="utf-8">
		let gx;
		const pApp = new App('', { gridId: "#div-gd" });

		$(document).ready(function() {
			pApp.ResizeGrid(310);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns,{
				//pinnedTopRowData: pinnedRowData,
			});

			$('#excel_file').on('change', function(e){
				if (validateFile() === false) {
					$('.custom-file-label').html("");
					return;
				}
				$('.custom-file-label').html(this.files[0].name);
			});
		});

		// 엑셀업로드 선택한 행 삭제 기능
		function onRemoveSelected() {
			let selectedData = gx.getSelectedRows();

			if (selectedData.length > 0) {
				if (confirm('해당 매핑정보를 삭제하시겠습니까?')){
					for(let i = 0; i < selectedData.length; i++){
						gx.gridOptions.api.applyTransaction({ remove: [selectedData[i]] });
					}
				}
			} else {
				selectedData = {};
				alert('삭제할 매핑정보를 선택해주세요.');
			}

		}

		const validateFile = () => {
			const target = $('#excel_file')[0].files;

			if (target.length > 1) {
				alert("파일은 1개만 올려주세요.");
				return false;
			}

			if (target === null || target.length === 0) {
				alert("업로드할 파일을 선택해주세요.");
				return false;
			}

			if (!/(.*?)\.(xlsx|XLSX)$/i.test(target[0].name)) {
				alert("Excel파일만 업로드해주세요.(xlsx)");
				return false;
			}

			return true;
		};

		/**
		 * 아래부터 엑셀 관련 함수들
		 * - read the raw data and convert it to a XLSX workbook
		 */
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
			var httpRequest = new XMLHttpRequest();
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
			var firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
			var worksheet = workbook.Sheets[firstSheetName];

			var excel_columns = {
				'A': 'prd_cd',
				'B': 'goods_no',
				'C': 'goods_opt'
			};

			var firstRowIndex = 6; // 엑셀 6행부터 시작 (샘플데이터 참고)
			var rowIndex = firstRowIndex;

			let count = gx.gridOptions.api.getDisplayedRowCount();
			let rows = [];
			while (worksheet['C' + rowIndex]) {
				let row = {};
				Object.keys(excel_columns).forEach((column) => {
					let item = worksheet[column + rowIndex];
					if(item !== undefined && item.w) {
						row[excel_columns[column]] = item.w;
					}
				});

				row = { ...row,
					count: ++count
				};
				rows.push(row);
				rowIndex++;
			}
			if(rows.length < 1) return alert("한 개 이상의 상품정보를 입력해주세요.");
			rows = rows.filter(r => r.prd_cd);
			await getData(rows, firstRowIndex);
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

		const upload = () => {
			const file_data = $('#excel_file').prop('files')[0];
			if(!file_data) return alert("적용할 파일을 선택해주세요.");

			const form_data = new FormData();
			form_data.append('cmd', 'import');
			form_data.append('file', file_data);
			form_data.append('_token', "{{ csrf_token() }}");

			alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");

			axios({
				method: 'post',
				url: '/store/product/prd02/batch-mapping-import',
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
				}
			}).catch((error) => {
				console.log(error);
			});

			return false;
		};

		const getData = async (rows, firstIndex) => {

			axios({
				url: '/store/product/prd02/batch-mapping-data',
				method: 'post',
				data: { data: rows },
			}).then(async (res) => {
				if (res.data.code == 200) {
					await gx.gridOptions.api.applyTransaction({add : res.data.body});
				} else if (res.data.code === 400) {
					alert(res.data.msg);
				} else {
					alert("엑셀일괄업로드 중 오류가 발생했습니다.\n다시 시도해주세요.");
				}
			}).catch((error) => {
				console.log(error);
			});
		};

		//매핑등록
		function RequestMapping() {
			let rows = gx.getSelectedRows();
			if(rows.length < 1) return alert("일괄매핑 할 항목을 선택해주세요.");
			if(!confirm("선택한 항목을 일괄 매핑 하시겠습니까?")) return;

			axios({
				url: '/store/product/prd02/add-product-product',
				method: 'post',
				data: {data: rows},
			}).then(function (res) {
				if(res.data.code === 200) {
					alert(res.data.msg);
					window.close();
					window.opener.location.href = "/store/product/prd02";
				} else if (res.data.code === 400) {
					alert(res.data.msg);
				} else {
					console.log(res.data);
					alert("일괄 매핑 등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
				}
			}).catch(function (err) {
				console.log(err);
			});
		}

	</script>
@stop
