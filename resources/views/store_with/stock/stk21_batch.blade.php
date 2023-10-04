@extends('store_with.layouts.layout-nav')
@section('title', '본사요청RT 엑셀 업로드')
@section('content')

	<!-- import excel lib -->
	<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

	<div class="show_layout py-3 px-sm-3">
		<div class="page_tit d-flex justify-content-between">
			<div class="d-flex">
				<h3 class="d-inline-flex">본사요청RT 엑셀 업로드</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 매장관리</span>
					<span>/ 매장RT관리</span>
					<span>/ 본사요청RT</span>
					<span>/ 엑셀 업로드</span>
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
													<a href="/sample/sample_stk21.xlsx" class="ml-2" style="text-decoration: underline !important;">본사요청RT 등록양식 다운로드</a>
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
								<a href="#">상품정보</a>
							</div>
							<div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-right">
								<div class="d-flex">
									<a href="#" onclick="RequestRT();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>RT등록</a> &nbsp;&nbsp;
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
			{headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"},
				cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
			},
			{field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 28},
			{field: "dep_store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
			{field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 140},
			{field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
			{field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 140},
			{field: "qty", headerName: "RT수량", type: "numberType", pinned: 'left',
				editable: (params) => params.node.rowPinned === "top" ? false : true,
				cellStyle: (params) => params.node.rowPinned === "top" ? '' : {"background-color": "#ffFF99"}
			},
			{field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
			{field: "goods_no",	headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}, pinned: 'left'},
			{field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: {"text-align": "center"}},
			{field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
			{field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
			{field: "goods_nm",	headerName: "상품명", width: 150,
				cellRenderer: function (params) {
					if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
						return '<a href="javascript:void(0);" onclick="return alert(`상품번호가 비어있는 상품입니다.`);">' + (params.value || '') + '</a>';
					} else {
						let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
						return '<a href="#" onclick="return openStoreProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
					}
				}
			},
			{field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
			{field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
			{field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
			{field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
			{field: "goods_opt", headerName: "옵션", width: 150},
			{field: "tag_price", headerName: "정상가", type: "currencyType", width: 60},
			{field: "price", headerName: "현재가", type: "currencyType", width: 60},
			{field: "wonga", headerName: "원가", type: "currencyType", width: 60},
			{field: "comment", headerName: "요청메모", width: 200,
				editable: (params) => params.node.rowPinned === "top" ? false : true,
				cellStyle: (params) => params.node.rowPinned === "top" ? '' : {"background-color": "#ffFF99"}
			},
		];
	</script>

	<script type="text/javascript" charset="utf-8">
		let gx;
		const pApp = new App('', { gridId: "#div-gd" });

		$(document).ready(function() {
			pApp.ResizeGrid(275, 550);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns,{
				pinnedTopRowData: pinnedRowData,
				getRowStyle: (params) => {
					if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
				},
				onCellValueChanged: (e) => {
					e.node.setSelected(true);
					if (e.column.colId == "qty") {
						if (isNaN(parseFloat(e.newValue)) == true || e.newValue == "") {
							alert("숫자만 입력가능합니다.");
							gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
						} else {
							updatePinnedRow();

						}
					}
				}
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
				'A': 'dep_store_cd',
				'B': 'store_cd',
				'C': 'prd_cd',
				'D': 'qty',
				'E': 'comment'
			};

			var firstRowIndex = 6; // 엑셀 6행부터 시작 (샘플데이터 참고)
			var rowIndex = firstRowIndex;

			let count = gx.gridOptions.api.getDisplayedRowCount();
			let rows = [];
			while (worksheet['E' + rowIndex]) {
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
			await getGood(rows, firstRowIndex);
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
				url: '/store/stock/stk21/batch-import',
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

		// 출고요청
		function requestRelease() {
			let rows = gx.getSelectedRows();

			let rel_order = $('#rel_order').val();
			if(rel_order === '') return alert("출고차수를 선택해주세요.");

			let rel_type = $('#rel_type').val();
			if(rel_type === '') return alert("상태를 선택해주세요.")

			if(rows.length < 1) return alert("창고출고할 상품을 선택해주세요.");


			let over_qty_rows = rows.filter(row => {
				let storage_cd = row.storage_cd;
				let cur_storage = row.storage_qty.filter(s => s.storage_cd === storage_cd);
				if(cur_storage.length > 0) {
					if(cur_storage[0].wqty2 < parseInt(row.qty)) {
						return true;
					} else {
						return false;
					}
				}
				return true; // 상품재고가 없는경우
			});

			if(over_qty_rows.length > 0) return alert(`선택하신 창고의 재고보다 많은 수량을 요청하실 수 없습니다.\n바코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);


			if(!confirm("해당 상품을 출고하시겠습니까?")) return;

			const data = {
				products: rows,
				exp_dlv_day: $('[name=exp_dlv_day]').val(),
				rel_order: $('[name=rel_order]').val(),
				rel_order,
				rel_type
			};

			axios({
				url: '/store/stock/stk19/request-release-excel',
				method: 'post',
				data: data,
			}).then(function (res) {
				if(res.data.code === 200) {
					alert('해당상품이 출고요청 되었습니다.');
					window.close();
					opener.location.href = "/store/stock/stk10";
				} else {
					console.log(res.data);
					alert("출고요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
				}
			}).catch(function (err) {
				console.log(err);
			});
		}

		const getGood = async (rows, firstIndex) => {

			axios({
				url: '/store/stock/stk21/batch-getgoods',
				method: 'post',
				data: { data: rows },
			}).then(async (res) => {
				if (res.data.code == 200) {
					await gx.gridOptions.api.applyTransaction({add : res.data.body});
					updatePinnedRow();
				} else if (res.data.code === 400) {
					alert(res.data.msg);
				} else {
					alert("엑셀일괄업로드 중 오류가 발생했습니다.\n다시 시도해주세요.");
				}
			}).catch((error) => {
				console.log(error);
			});
		};

		const updatePinnedRow = () => {
			let qty = 0;
			const rows = gx.getRows();
			if (rows && Array.isArray(rows) && rows.length > 0) {
				rows.forEach((row, idx) => {
					qty += parseFloat(row.qty);
				});
			}

			let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
			gx.gridOptions.api.setPinnedTopRowData([
				{ ...pinnedRow.data, qty: qty }
			]);
		};
		
		
		//RT등록
		function RequestRT() {
			let rows = gx.getSelectedRows();
			if(rows.length < 1) return alert("일괄RT 요청할 항목을 선택해주세요.");
			if(!confirm("선택한 항목을 일괄RT 요청하시겠습니까?")) return;

			axios({
				url: '/store/stock/stk21/batch-request-rt',
				method: 'post',
				data: {data: rows},
			}).then(function (res) {
				if(res.data.code === 200) {
					alert(res.data.msg);
					window.close();
					location.href = "/store/stock/stk20";
				} else if (res.data.code === 400) {
					alert(res.data.msg);
				} else {
					console.log(res.data);
					alert("본사요청RT 일괄등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
				}
			}).catch(function (err) {
				console.log(err);
			});
		}

	</script>
@stop
