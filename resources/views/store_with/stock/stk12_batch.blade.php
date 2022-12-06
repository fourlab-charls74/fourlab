@extends('store_with.layouts.layout-nav')
@section('title', '초도 출고 엑셀 업로드')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">초도출고 엑셀 업로드</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 출고</span>
                <span>/ 초도출고</span>
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
                                                    <a href="/sample/sample_stk12.xlsx" class="ml-2" style="text-decoration: underline !important;">초도출고 등록양식 다운로드</a>
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
                    <div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
                        <div class="d-flex mr-1 mb-1 mb-lg-0">
                            <span class="mr-1">출고예정일</span>
                            <div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $today }}" autocomplete="off" readonly />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <select id='rel_order' name='rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
                                @foreach ($rel_order_res as $rel_order)
                                    <option value='{{ $rel_order->code_val }}'>{{ $rel_order->code_val }}</option>
                                @endforeach
                            </select>
                            <a href="#" onclick="requestRelease();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>출고요청</a> &nbsp;&nbsp;
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
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_return_price: 0 }];

    let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        // {field: "store_cd", headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "출고매장", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "상품번호", width: 78, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 80, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 220},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", type: 'HeadGoodsNameType', width: 220},
        {field: "prd_cd_p", headerName: "코드일련", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 60, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 200},
        {field: "storage_qty", headerName: "창고재고", width: 60, type: 'currencyType'},
        {field: "store_qty", headerName: "매장재고", width: 60, type: 'currencyType'},
        {field: "qty", headerName: "배분수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
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
            selectedData = selectedData[0];
        } else {
            selectedData = {};
        }

        if (confirm('해당 상품을 삭제하시겠습니까?')){
            gx.gridOptions.api.applyTransaction({ remove: [selectedData] });
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
			'A': 'store_cd',
			'B': 'prd_cd',
			'C': 'qty',
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
		const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

		const form_data = new FormData();
        form_data.append('cmd', 'import');
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        axios({
            method: 'post',
            url: '/store/stock/stk12/batch-import',
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

            if(rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");

            let storage_qty = "";
            let over_qty = "";
            for(let i = 0; i < rows.length; i++) {
                storage_qty = rows[i].storage_qty;
                over_qty = rows[i].qty;

            }
        
            if(storage_qty < over_qty) return alert(`창고의 재고보다 많은 수량을 요청하실 수 없습니다`);
            if(over_qty == 0) return alert(`배분수량을 0개를 요청할 수 없습니다. 1개 이상을 요청해주세요.`);

            if(!confirm("해당 상품을 출고요청하시겠습니까?")) return;

            const data = {
                products: rows,
                exp_dlv_day: $('[name=exp_dlv_day]').val(),
                rel_order: $('[name=rel_order]').val(),
            };

            axios({
                url: '/store/stock/stk12/request-release-excel',
                method: 'post',
                data: data,
            }).then(function (res) {
                if(res.data.code === 200) {
                    if(!confirm(res.data.msg + "\n출고요청을 계속하시겠습니까?")) {
                        window.close();
                        opener.location.href = "/store/stock/stk10";
                    } else {
                        Search();
                    }
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
            url: '/store/stock/stk12/batch-getgoods',
            method: 'post',
            data: { data: rows },
        }).then(async (res) => {
            let data = res.data.body.map(r => ({...r, qty: r.storage_wqty < r.qty ? r.storage_wqty : r.qty}));
            await gx.gridOptions.api.applyTransaction({add : data});
            updatePinnedRow();
        }).catch((error) => {
            console.log(error);
        });
    };

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
