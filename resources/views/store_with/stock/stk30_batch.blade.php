@extends('store_with.layouts.layout-nav')
@section('title', '매장일괄반품')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">매장일괄반품</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 매장반품관리</span>
                <span>/ 매장일괄반품</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 반품요청</a>
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
                                            <th class="required">반품일자</th>
                                            <td>
                                                <div class="form-inline">
	                                                <div class="docs-datepicker form-inline-inner input_box w-100">
		                                                <div class="input-group">
			                                                <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off">
			                                                <div class="input-group-append">
				                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
					                                                <i class="fa fa-calendar" aria-hidden="true"></i>
				                                                </button>
			                                                </div>
		                                                </div>
		                                                <div class="docs-datepicker-container"></div>
	                                                </div>
                                                </div>
                                            </td>
                                            <th class="required">보내는 매장</th>
                                            <td>
                                                <div class="form-inline inline_select_box">
	                                                <div class="form-inline-inner input-box w-100">
		                                                <div class="form-inline inline_btn_box">
			                                                <input type='hidden' id="store_nm" name="store_nm">
			                                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
			                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
		                                                </div>
	                                                </div>
                                                </div>
                                            </td>
                                            <th class="required">반품창고</th>
                                            <td>
                                                <div class="form-inline">
	                                                <select name='storage_cd' class="form-control form-control-sm w-100" @if(@$cmd == 'update') disabled @endif>
		                                                @foreach (@$storages as $storage)
			                                                <option value='{{ $storage->storage_cd }}' @if(@$cmd == 'update' && $sr->storage_cd == $storage->storage_cd) selected @endif>{{ $storage->storage_nm }}</option>
		                                                @endforeach
		                                                <input type="hidden" id="storage" value="{{ @$sr->storage_cd }}" class="form-control form-control-sm w-100" readonly />
	                                                </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
	                                        <th class="required">반품사유</th>
	                                        <td>
		                                        <div class="form-inline">
			                                        <select name='sr_reason' id="sr_reason" class="form-control form-control-sm w-100">
				                                        @foreach ($sr_reasons as $sr_reason)
					                                        <option value='{{ $sr_reason->code_id }}'>{{ $sr_reason->code_val }}</option>
				                                        @endforeach
			                                        </select>
		                                        </div>
	                                        </td>
                                            <th>메모</th>
                                            <td>
                                                <div class="form-inline">
	                                                <textarea name="comment" id="comment" class="form-control w-100" rows="1"></textarea>
                                                </div>
                                            </td>
	                                        <th>반품코드</th>
	                                        <td>
		                                        <div class="form-inline">
			                                        <p id="sr_cd" class="fs-14"></p>
		                                        </div>
	                                        </td>
                                        </tr>
                                        <tr>
	                                        <th>파일</th>
	                                        <td colspan="5">
		                                        <div class="flex_box">
			                                        <div class="custom-file w-50">
				                                        <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
				                                        <label class="custom-file-label" for="file"></label>
			                                        </div>
			                                        <div class="btn-group ml-2">
				                                        <button class="btn btn-outline-primary apply-btn" type="button" onclick="upload();">적용</button>
			                                        </div>
			                                        <a href="/sample/sample_stk30.xlsx" download="매장일괄반품_샘플" class="ml-2" style="text-decoration: underline !important;">매장일괄반품 양식다운로드</a>
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
                <div class="d-flex">
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, return_amt: 0 }];

    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"},
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => {
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 130, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p",	headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size",	headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 100},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 70},
        {field: "price", headerName: "현재가", type: "currencyType", width: 70},
        {field: "return_price", headerName: "반품단가", width: 70, type: 'currencyType',
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
		{headerName: "매장재고", children: [
			{field: "store_qty", headerName: "재고", width: 60, type: 'currencyType'},
			{field: "store_wqty", headerName: "보유재고", width: 60, type: 'currencyType'},
		]},
        {field: "return_amt", headerName: "요청금액", width: 80, type: 'currencyType'},
        {field: "qty", headerName: "요청수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
		{width: 0}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(435);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
                if (e.column.colId === "return_price" || e.column.colId === "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        if(e.column.colId === "qty" && e.data.store_wqty < parseInt(e.data.qty)) {
                            alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        } else {
							e.node.setDataValue('return_amt', parseInt(e.data.qty) * parseInt(e.data.return_price));
                            updatePinnedRow();
                        }
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

		$("#store_no").on("change", function(e) {
			gx.gridOptions.api.setRowData([]);
			updatePinnedRow();
		});
    });

    /**
     * 아래부터 엑셀 관련 함수들
     * - read the raw data and convert it to a XLSX workbook
    */

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

		let excel_columns = {
			'A': 'prd_cd',
			'B': 'qty',
        };

        let firstRowIndex = 2; // 엑셀 2행부터 시작 (샘플데이터 참고)
		let rowIndex = firstRowIndex; 

        let count = gx.gridOptions.api.getDisplayedRowCount();
        let rows = [];
		while (worksheet['A' + rowIndex]) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if(item !== undefined && item.w) {
					row[excel_columns[column]] = item.w;
				}
			});
            
            row.qty = row.qty || 0; // 실사재고 미입력 시 0 처리
            row = { ...row, 
                count: ++count, isEditable: true,
            };
            rows.push(row);
            rowIndex++;
		}
        if(rows.length < 1) return alert("한 개 이상의 상품정보를 입력해주세요.");
        rows = rows.filter(r => r.prd_cd);
        let values = { data: rows, storage_cd: document.f1.storage_cd.value, store_cd: document.f1.store_no.value };
        await getGood(values, firstRowIndex);
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
		const store_cd = document.f1.store_no.value;
		if (store_cd === '') {
			$(".sch-store").click();
			return alert("매장을 선택해주세요.");
		}

		if (gx.getRows().length > 0 && !confirm("새로 적용하시는 경우 기존정보는 저장되지 않습니다.\n적용하시겠습니까?")) return;

		const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

		const form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        axios({
            method: 'post',
            url: '/store/stock/stk30/batch-import',
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

    const getGood = async (values, firstIndex) => {
        axios({
            url: '/store/stock/stk30/batch-getgoods',
            method: 'post',
            data: values,
        }).then(async (res) => {
            if (res.data.code == 200) {
	            await gx.gridOptions.api.applyTransaction({add : res.data.body});
	            updatePinnedRow();
            } else {
				alert("상품정보조회 중 오류가 발생했습니다.\n다시 시도해주세요.");
            }
        }).catch((error) => {
            console.log(error);
        });
    };

    // 매장일괄반품 저장
    function Save() {
        let sr_date = document.f1.sdate.value;
        let store_cd = document.f1.store_no.value;
        let storage_cd = document.f1.storage_cd.value;
        let sr_reason = document.f1.sr_reason.value;
        let comment = document.f1.comment.value;

		if(store_cd === '') {
			$(".sch-store").click();
			return alert("매장을 선택해주세요.");
		}

		let rows = gx.getRows();
		if(rows.length < 1) return alert("일괄등록할 상품을 추가해주세요.");

        if(!confirm("등록하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk30/save',
            method: 'put',
            data: {
                sr_kind: "B", // 일괄등록
                sr_date,
                store_cd,
                storage_cd,
                sr_reason,
                comment,
                products: rows.map(r => ({
	                prd_cd : r.prd_cd, 
	                price : r.price, 
	                return_price : r.return_price, 
	                return_qty: r.qty, 
	                store_wqty : r.store_wqty 
				})),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert("매장반품요청이 성공적으로 완료되었습니다.");
                opener.Search();
                window.close();
            } else if (res.data.code === 501) {
                alert("반품수량이 매장보유재고보다 많습니다. 반품수량을 수정해주세요.")
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    const checkIsEditable = (params) => {
        return params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    };

    // 상품 삭제
    const deleteRow = (row) => { gx.gridOptions.api.applyTransaction({remove : [row]}); };

    const delGoods = () => {
        const rows = gx.getSelectedRows();
        if (Array.isArray(rows) && !(rows.length > 0)) return alert("삭제할 상품을 선택해주세요.");

        rows.filter((row, idx) => row.isEditable).map((row) => { deleteRow(row); });
        updatePinnedRow();
    };

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ qty, return_amt ] = [ 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                qty += parseFloat(row.qty);
                return_amt += parseFloat(row.return_amt);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, qty: qty, return_amt: return_amt }
        ]);
    };
</script>
@stop
