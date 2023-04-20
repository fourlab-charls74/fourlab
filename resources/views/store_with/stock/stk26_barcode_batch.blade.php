@extends('store_with.layouts.layout-nav')
@section('title', '매장실사바코드등록')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">매장실사 바코드 등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 실사</span>
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
                                                    <a href="/sample/sample_barcode.xlsx" class="ml-2" style="text-decoration: underline !important;">실사 바코드등록 양식 다운로드</a>
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
                                            <th class="required">실사일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14" id="sc_date"></p>
                                                </div>
                                            </td>
                                            <th class="required">매장</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14" id="store_nm"></p>
                                                </div>
                                            </td>
                                            <th>실사코드</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14" id="new_sc_cd"></p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">담당자</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14" id="md_nm"></p>
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
                <div class="d-flex">
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
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
    const pinnedRowData = [{ prd_cd: '합계', store_wqty: 0, qty: 0, loss_qty: 0, loss_price: 0 }];

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
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", width: 200,
            cellRenderer: (params) => {
                if (params.data.goods_no === undefined) return '';
                if (params.data.goods_no != '0') {
                    return '<a href="javascript:void(0);" onclick="return openHeadProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                } else {
                    return '<a href="javascript:void(0);" onclick="return alert(`상품번호가 없는 상품입니다.`);">' + params.value + '</a>';
                }
            }   
        },
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 50, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 50, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 130},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 70},
        {field: "price", headerName: "판매가", type: "currencyType", width: 70},
        {field: "store_wqty", headerName: "매장보유재고", width: 100, type: 'currencyType'},
        {field: "qty", headerName: "실사재고", width: 60, type: 'currencyType',
            editable: (params) => {
                if (params.node.level != 0) {
                    return false;
                }else {
                    return true;
                }
            },
            cellStyle: (params) => {
                if (params.node.level != 0) {
                    return {};
                }else {
                    return {'background-color':'#ffff99'};
                }
            }
        },
        {field: "loss_qty", headerName: "LOSS수량", width: 80, type: 'currencyType'},
        {field: "loss_price", headerName: "LOSS금액", width: 80, type: 'currencyType'}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });
    let basic_info = {};

    $(document).ready(function() {
        pApp.ResizeGrid(275,550);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
                if (e.column.colId === "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        e.data.loss_qty = parseInt(e.data.store_wqty) - parseInt(e.data.qty);
                        e.data.loss_price = parseInt(e.data.price) * parseInt(e.data.loss_qty);
                        gx.gridOptions.api.updateRowData({update: [e.data]});
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

        let sc_date = worksheet['C4']?.w;
        let store_cd = worksheet['C5']?.w;
        let md_id = worksheet['C6']?.w;
        let comment = worksheet['C7']?.w;

		let excel_columns = {
			'B': 'prd_cd',
        };

        let firstRowIndex = 10; // 엑셀 10행부터 시작 (샘플데이터 참고)
		let rowIndex = firstRowIndex; 

        let count = gx.gridOptions.api.getDisplayedRowCount();
        let rows = [];
		while (worksheet['B' + rowIndex]) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if(item !== undefined && item.w) {
					row[excel_columns[column]] = item.w;
				}
			});
            
            row.qty = row.qty || 1; // 실사재고 미입력 시 0 처리
            row = { ...row, 
                count: ++count, isEditable: true,
            };
            rows.push(row);
            rowIndex++;
		}
        if(rows.length < 1) return alert("한 개 이상의 상품정보를 입력해주세요.");
        rows = rows.filter(r => r.prd_cd);
        let values = { data: rows, store_cd, md_id, sc_date, comment };
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
        if(basic_info.sc_date !== undefined && !confirm("새로 적용하시는 경우 기존정보는 저장되지 않습니다.\n적용하시겠습니까?")) return;
        
		const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

		const form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        axios({
            method: 'post',
            url: '/store/stock/stk26/batch-import2',
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
            url: '/store/stock/stk26/batch-getgoods2',
            method: 'post',
            data: values,
        }).then(async (res) => {
            if (res.data.code != 200) return alert(res.data.msg);

            setBasicInfo(res.data.head);
            const result = res.data.body;
            let data = mergeDuplicateValues(result);

            for(let i = 0; i< data.length; i++) {
                data[i].loss_qty = data[i].store_wqty - data[i].qty;
                data[i].loss_price = data[i].price * data[i].loss_qty;
            }

            console.log(data);
            await gx.gridOptions.api.applyTransaction({add : data});
            updatePinnedRow();
        }).catch((error) => {
            console.log(error);
        });
    };

    // 바코드의 중복값을 찾고 qty를 더해주는 부분
    function mergeDuplicateValues(arr) {
        let result = {};
        let merged = [];

        for (let i = 0; i < arr.length; i++) {
            let key = arr[i].prd_cd;

            if (result.hasOwnProperty(key)) {
            result[key].qty += arr[i].qty;
            } else {
            result[key] = Object.assign({}, arr[i]); // 같은 속성을 가진 배열을 병합하는 부분
            merged.push(result[key]);
            }
        }

        return merged;
    }

    function setBasicInfo(obj) {
        basic_info = {...obj};

        $("#new_sc_cd").text(basic_info.new_sc_cd);
        $("#sc_date").text(basic_info.sc_date);
        $("#store_nm").text(basic_info.store?.store_nm);
        $("#md_nm").text(basic_info.md?.name);
        $("#comment").text(basic_info.comment);

        $("#basic_info_form").removeClass("d-none");
        pApp.ResizeGrid(275, 340);
    }

    // 실사 등록
    function Save() {
        let rows = gx.getRows();
       
        let sc_date = basic_info.sc_date;
        let store_cd = basic_info.store?.store_cd;
        let md_id = basic_info.md?.id;
        let comment = basic_info.comment;

        if(!store_cd) return alert("매장정보가 올바르지 않습니다.");
        if(rows.length < 1) return alert("실사등록할 상품을 선택해주세요.");
        if(!md_id) return alert("담당자정보가 올바르지 않습니다.");

        if(!confirm("등록하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk26/save',
            method: 'put',
            data: {
                sc_type: "C", //바코드 등록
                sc_date,
                store_cd,
                md_id,
                comment,
                products: rows.map(r => ({ prd_cd: r.prd_cd, price: r.price, qty: r.qty, store_qty: r.store_wqty })),
            },
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("실사 바코드 등록이 성공적으로 완료되었습니다.");
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

    // 상품 삭제
    const deleteRow = (row) => { gx.gridOptions.api.applyTransaction({remove : [row]}); };

    const delGoods = () => {
        const ff = document.f1;
        const rows = gx.getSelectedRows();
        if (Array.isArray(rows) && !(rows.length > 0)) return alert("삭제할 상품을 선택해주세요.");

        rows.filter((row, idx) => row.isEditable).map((row) => { deleteRow(row); });
        updatePinnedRow();
    };

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ store_wqty, qty, loss_qty, loss_price ] = [ 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                store_wqty += parseInt(row.store_wqty || 0);
                qty += parseInt(row.qty || 0);
                loss_qty += parseInt(row.loss_qty || 0);
                loss_price += parseInt(row.loss_price || 0);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, store_wqty: store_wqty, qty: qty, loss_qty: loss_qty, loss_price: loss_price }
        ]);
    };
</script>
@stop
