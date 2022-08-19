@extends('store_with.layouts.layout-nav')
@section('title', '상품반품이동 일괄등록')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">상품반품이동 일괄등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 생산입고관리</span>
                <span>/ 상품반품이동</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
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
                                                    <a href="/sample/sample_cs02.xlsx" class="ml-2" style="text-decoration: underline !important;">상품반품이동 등록양식 다운로드</a>
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
                                                    <p class="fs-14">??</p>
                                                </div>
                                            </td>
                                            <th class="required">이동처</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14">??</p>
                                                </div>
                                            </td>
                                            <th>반품번호</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14">??</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">반품창고</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p class="fs-14">??</p>
                                                </div>
                                            </td>
                                            <th>메모</th>
                                            <td colspan="3">
                                                <div class="form-inline">
                                                    <p class="fs-14">??</p>
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
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "상품번호", cellStyle: {"text-align": "center"}},
        {field: "goods_type", headerName: "상품구분", cellStyle: StyleGoodsTypeNM},
        {field: "opt_kind_nm", headerName: "품목", width: 80, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", cellStyle: {"text-align": "center"}},
        {field: "sale_stat_cl", headerName: "상품상태", cellStyle: StyleGoodsState},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 250},
        {field: "goods_opt", headerName: "옵션", width: 240},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 70},
        {field: "price", headerName: "판매가", type: "currencyType", width: 70},
        {field: "return_price", headerName: "반품단가", width: 80, type: 'currencyType',
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "storage_wqty", headerName: "창고보유재고", width: 100, type: 'currencyType'},
        {field: "qty", headerName: "반품수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "total_return_price", headerName: "반품금액", width: 80, type: 'currencyType'},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275, 470);
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
        console.log(workbook);
		var firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
		var worksheet = workbook.Sheets[firstSheetName];

		var excel_columns = {
			'A': 'sgr_date',
			'B': 'target_type',
			'C': 'target_cd',
            'D': 'storage_cd',
            'E': 'comment',
            'F': 'prd_cd',
            'G': 'return_price',
            'H': 'return_qty',
		};

		var rowIndex = 6; // 엑셀 6행부터 시작 (샘플데이터 참고)

        alert('상품을 순차적으로 불러오고 스타일 넘버를 검사합니다. \n다소 시간이 소요될 수 있습니다.'); // progress
        let count = gx.gridOptions.api.getDisplayedRowCount();
		while (worksheet['F' + rowIndex]) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if(item !== undefined && item.w) {
					row[excel_columns[column]] = item.w;
				}
			});
            
            row.return_price = row.return_price || -1; // 반품단가
            row = { ...row, 
                count: ++count, isEditable: true,
            };

            gx.gridOptions.api.applyTransaction({add : [row]}); // 한 줄씩 import
            
            await getGood(row, rowIndex === 6);
            rowIndex++;
		}
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
		const form_data = new FormData();
        form_data.append('cmd', 'import');
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        axios({
            method: 'post',
            url: '/store/cs/cs02/batch-import',
            data: form_data,
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }).then(async (res) => {
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

    const getGood = async (row, isFirst) => {
        console.log(row);
        // const CMD = 'getgood';
        // axios({
        //     cmd: CMD,
        //     url: COMMAND_URL,
        //     method: 'post',
        //     data: { cmd: CMD, style_no: row.style_no }
        // }).then(async (response) => {
            
        //     const code = response.data.code; // 0: 상품없음, -1: 상품중복 또는 입점상품, 1: 존재하는 상품
        //     let good, message, checked_row;
            
        //     if (response.data.code == 1) {
        //         good = response.data.good;                
        //         checked_row = {...row, ...good};
        //     } else {
        //         message = response.data.message;
        //         checked_row = {...row, goods_no: message};
        //     }

        //     await gx.gridOptions.api.applyTransaction({update : [checked_row]});

        // }).catch((error) => {
        //     console.log(error);
        // });
    };

    // 상품반품 등록
    // function Save(cmd) {
    //     if(!cmd) return;

    //     let comment = document.f1.comment.value;
    //     let rows = gx.getRows();

    //     if(cmd === 'add') {
    //         let sgr_date = document.f1.sdate.value;
    //         let storage_cd = document.f1.storage_cd.value;
    //         let target_type = document.f1.target_type.value;
    //         let target_cd = document.f1.target_cd.value;

    //         if(rows.length < 1) return alert("반품등록할 상품을 선택해주세요.");

    //         let zero_qtys = rows.filter(r => r.qty < 1);
    //         if(zero_qtys.length > 0) return alert("반품수량이 0개인 항목이 존재합니다.");

    //         if(!confirm("등록하시겠습니까?")) return;

    //         axios({
    //             url: '/store/cs/cs02/add-storage-return',
    //             method: 'put',
    //             data: {
    //                 sgr_date,
    //                 storage_cd,
    //                 target_type,
    //                 target_cd,
    //                 comment,
    //                 products: rows.map(r => ({ prd_cd: r.prd_cd, price: r.price, return_price: r.return_price, return_qty: r.qty })),
    //             },
    //         }).then(function (res) {
    //             if(res.data.code === 200) {
    //                 alert(res.data.msg);
    //                 opener.Search();
    //                 window.close();
    //             } else {
    //                 console.log(res.data);
    //                 alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
    //             }
    //         }).catch(function (err) {
    //             console.log(err);
    //         });
    //     } else if(cmd === 'update') {
    //         let sgr_state = '{{ @$sgr->sgr_state }}';
    //         let sgr_cd = '{{ @$sgr->sgr_cd }}';

    //         if('{{ @$sgr->sgr_state }}' != 10) return alert("상품반품이동이 '접수'상태일떄만 수정가능합니다.");
    //         if(!confirm("수정하시겠습니까?")) return;

    //         axios({
    //             url: '/store/cs/cs02/update-storage-return',
    //             method: 'put',
    //             data: {
    //                 sgr_cd,
    //                 comment,
    //                 products: rows.map(r => ({ sgr_prd_cd: r.sgr_prd_cd, return_price: r.return_price, return_qty: r.qty })),
    //             },
    //         }).then(function (res) {
    //             if(res.data.code === 200) {
    //                 alert(res.data.msg);
    //                 opener.Search();
    //                 window.close();
    //             } else {
    //                 console.log(res.data);
    //                 alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
    //             }
    //         }).catch(function (err) {
    //             console.log(err);
    //         });
    //     }
    // }

    const checkIsEditable = (params) => {
        return params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    };
</script>
@stop
