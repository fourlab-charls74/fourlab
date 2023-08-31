@extends('store_with.layouts.layout-nav')
@section('title', '창고간상품이동 일괄등록')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">창고간상품이동 일괄등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품관리</span>
                <span>/ 창고간상품이동</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
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
                                                    <a href="/sample/sample_cs04.xlsx" download="창고간상품이동_일괄등록양식" class="ml-2" style="text-decoration: underline !important;">창고간상품이동 일괄등록양식 다운로드</a>
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
        <div class="card shadow mt-3 d-none" id="basic_info_form">
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
                                            <th>이동일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14" id="sgr_date"></p>
                                                </div>
                                            </td>
	                                        <th>출고창고</th>
	                                        <td>
		                                        <div class="form-inline">
			                                        <p class="fs-14" id="storage_nm"></p>
		                                        </div>
	                                        </td>
	                                        <th>이동코드</th>
	                                        <td>
		                                        <div class="form-inline">
			                                        <p class="fs-14" id="sgr_idx"></p>
		                                        </div>
	                                        </td>
                                        </tr>
                                        <tr>
	                                        <th>이동창고</th>
	                                        <td>
		                                        <div class="form-inline">
			                                        <p class="fs-14" id="target_nm"></p>
		                                        </div>
	                                        </td>
                                            <th>메모</th>
                                            <td colspan="3">
                                                <div class="form-inline">
                                                    <p class="fs-14" id="comment"></p>
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
	            <p class="text-danger">* 일괄등록 시, 즉시 <span class="font-weight-bold">이동완료</span> 처리됩니다.</p>
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
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_return_price: 0 }];

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
        {field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 150},
        {field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 150},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 70},
        {field: "price", headerName: "판매가", type: "currencyType", width: 70},
        {field: "return_price", headerName: "이동단가", width: 70, type: 'currencyType',
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "storage_wqty", headerName: "창고재고", width: 60, type: 'currencyType'},
        {field: "qty", headerName: "이동수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "total_return_price", headerName: "이동금액", width: 80, type: 'currencyType'},
		{width: 0}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });
    let basic_info = {};

    $(document).ready(function() {
        pApp.ResizeGrid(330);
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
                        if(e.column.colId === "qty" && e.data.storage_wqty < parseInt(e.data.qty)) {
                            alert("해당 창고의 보유재고보다 많은 수량을 이동할 수 없습니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        } else {
                            e.data.total_return_price = parseInt(e.data.qty) * parseInt(e.data.return_price);
                            gx.gridOptions.api.updateRowData({update: [e.data]});
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
    });

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
			'A': 'sgr_date',
			'B': 'storage_cd',
			'C': 'target_cd',
            'D': 'comment',
            'E': 'prd_cd',
            'F': 'return_price',
            'G': 'return_qty',
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
            
            row.return_price = row.return_price || 0; // 반품단가
            row = { ...row, 
                count: ++count, isEditable: true,
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
        if(basic_info.sgr_date !== undefined && !confirm("새로 적용하시는 경우 기존정보는 저장되지 않습니다.\n적용하시겠습니까?")) return;
        
		const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

		const form_data = new FormData();
        form_data.append('cmd', 'import');
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        axios({
            method: 'post',
            url: '/store/cs/cs04/batch-import',
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

    const getGood = async (rows, firstIndex) => {

        axios({
            url: '/store/cs/cs04/batch-getgoods',
            method: 'post',
            data: { data: rows },
        }).then(async (res) => {
            setBasicInfo({...res.data.head, ...rows[0]});
            let data = res.data.body.map(r => ({...r, qty: r.storage_wqty < r.qty ? r.storage_wqty : r.qty}));
            await gx.gridOptions.api.applyTransaction({add : data});
            updatePinnedRow();
        }).catch((error) => {
            console.log(error);
        });
    };

    function setBasicInfo(obj) {
        basic_info = {...obj};
        
        $("#sgr_date").text(basic_info.sgr_date);
        $("#sgr_idx").text(basic_info.sgr_idx);
        $("#target_nm").text(basic_info.target_nm);
        $("#storage_nm").text(basic_info.storage_nm);
        $("#comment").text(basic_info.comment);

        $("#basic_info_form").removeClass("d-none");
        pApp.ResizeGrid(275, 370);
    }

    // 상품반품 일괄등록
    function Save() {
        let rows = gx.getRows();
        if(basic_info.sgr_date === undefined) return alert("일괄등록할 엑셀 파일을 적용해주세요.");
        if(rows.length < 1) return alert("일괄등록할 상품이 존재하지 않습니다.");

        let sgr_date = basic_info.sgr_date;
        let storage_cd = basic_info.storage_cd;
        let target_type = basic_info.target_type;
        let target_cd = basic_info.target_cd;
        let comment = basic_info.comment;

        let zero_qtys = rows.filter(r => r.qty < 1);
        if(zero_qtys.length > 0) return alert("이동수량이 0개인 항목이 존재합니다.");

        if(!confirm("일괄등록하시겠습니까?")) return;

        axios({
            url: '/store/cs/cs04/add-storage-return',
            method: 'put',
            data: {
                sgr_type: 'B',
                sgr_date,
                storage_cd,
                target_type,
                target_cd,
                comment,
                products: rows.map(r => ({ prd_cd: r.prd_cd, price: r.price, return_price: r.return_price, return_qty: r.qty })),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
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

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ qty, total_return_price ] = [ 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                qty += parseFloat(row.qty);
                total_return_price += parseFloat(row.total_return_price);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, qty: qty, total_return_price: total_return_price }
        ]);
    };
</script>
@stop
