@extends('store_with.layouts.layout-nav')
@section('title', '바코드 일괄등록')
@section('content')

	<!-- import excel lib -->
	<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

	<div class="show_layout py-3 px-sm-3">
		<div class="page_tit d-flex justify-content-between">
			<div class="d-flex">
				<h3 class="d-inline-flex">바코드 일괄등록</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 상품관리</span>
					<span>/ 상품코드관리</span>
					<span>/ 바코드 등록</span>
					<span>/ 바코드 일괄등록</span>
				</div>
			</div>
			<div class="d-flex">
				<a href="javascript:void(0)" onclick="save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
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
													<a href="/sample/sample_prd02_batch.xlsx" class="ml-2" style="text-decoration: underline !important;">상품일괄등록양식 다운로드</a>
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
						<div class="filter_wrap">
							<div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
								<a href="#">상품정보</a>
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
		let columns = [
			{field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
			{field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
			{field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
			{field: "year", headerName: "연도", width: 80, cellStyle: {"text-align": "center"}},
			{field: "season", headerName: "시즌",width: 80, cellStyle: {"text-align": "center"}},
			{field: "gender", headerName: "성별", width: 80, cellStyle: {"text-align": "center"}},
			{field: "item", headerName: "아이템", width: 80, cellStyle: {"text-align": "center"}},
			{field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
			{field: "seq", headerName: "순서", width: 50, cellStyle: {"text-align": "center"}},
			{field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"}},
			{field: "size_kind", headerName: "사이즈구분", width: 90, cellStyle: {"text-align": "center"}},
			{field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"}},
			{field: "goods_nm",	headerName: "상품명", width: 220},
			{field: "goods_nm_eng",	headerName: "상품명(영문)", width: 220},
			{field: "style_no",	headerName: "스타일넘버", width: 80, cellStyle: {"text-align": "center"}},
			{field: "tag_price", headerName: "정상가", type: 'currencyType', width: 80},
			{field: "price", headerName: "현재가", type: 'currencyType', width: 80},
			{field: "wonga", headerName: "원가", type: 'currencyType', width: 80},
			{field: "sup_com", headerName: "공급업체", width: 120, cellStyle: {"text-align": "center"}},
			{field: "origin", headerName: "원산지", width: 90, cellStyle: {"text-align": "center"}},
		];
	</script>

	<script type="text/javascript" charset="utf-8">
		let gx;
		const pApp = new App('', { gridId: "#div-gd" });

		$(document).ready(function() {
			pApp.ResizeGrid(275, 450);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns);

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
				if (confirm('해당 상품을 삭제하시겠습니까?')){
					for(let i = 0; i < selectedData.length; i++){
						gx.gridOptions.api.applyTransaction({ remove: [selectedData[i]] });
					}
				}
			} else {
				selectedData = {};
				alert('삭제할 상품을 선택해주세요.');
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
				'A': 'prd_cd_p',
				'B': 'brand',
				'C': 'year',
				'D': 'season',
				'E': 'gender',
				'F': 'item',
				'G': 'seq',
				'H': 'opt_kind_nm',
				'I': 'color',
				'J': 'size_kind',
				'K': 'size',
				'L': 'goods_nm',
				'M': 'goods_nm_eng',
				'N': 'style_no',
				'O': 'tag_price',
				'P': 'price',
				'Q': 'wonga',
				'R': 'sup_com',
				'S': 'origin'
			};

			var firstRowIndex = 6; // 엑셀 6행부터 시작 (샘플데이터 참고)
			var rowIndex = firstRowIndex;

			let count = gx.gridOptions.api.getDisplayedRowCount();
			let rows = [];
			while (worksheet['Q' + rowIndex]) {
				let row = {};
				Object.keys(excel_columns).forEach((column) => {
					let item = worksheet[column + rowIndex];
					if(item !== undefined && item.w) {
						row[excel_columns[column]] = item.w;
					}
				});
				rows.push(row);
				rowIndex++;
			}
			if(rows.length < 1) return alert("한 개 이상의 상품정보를 입력해주세요.");
			await getProducts(rows, firstRowIndex);
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
				url: '/store/product/prd02/batch-import2',
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

		const getProducts = async (rows, firstIndex) => {

			axios({
				url: '/store/product/prd02/batch-getproducts2',
				method: 'post',
				data: { data: rows },
			}).then(async (res) => {
				let data = res.data.body;
				await gx.gridOptions.api.applyTransaction({add : data});
			}).catch((error) => {
				console.log(error);
			});
		};

		function save() {
			let rows = gx.getSelectedRows();

			if(rows.length < 1) return alert("일괄등록할 상품을 선택해주세요.");


			if(!confirm("선택한 상품을 등록하시겠습니까?")) return;

			const data = {
				products: rows
			};

			axios({
				url: '/store/product/prd02/batch-products2',
				method: 'post',
				data: data,
			}).then(function (res) {
				if(res.data.code === 200) {
					alert('바코드 일괄등록에 성공하였습니다.');
					window.close();
				} else if (res.data.code === -1) {
					const prd_cd = res.data.prd_cd;
					alert(`${prd_cd}는 중복되었거나 이미 존재하는 상품 코드입니다.\n중복을 제거하거나 상품 코드를 재확인 후 다시 시도해주세요.`);
				} else {
					console.log(res.data);
					alert("바코드 일괄등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
				}
			}).catch(function (err) {
				console.log(err);
			});
		}

	</script>
@stop
