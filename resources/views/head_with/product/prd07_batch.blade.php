@extends('head_with.layouts.layout-nav')
@section('title', '상품관리 일괄등록')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">상품관리 엑셀업로드</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품/전시</span>
                <span>/ 상품관리</span>
                <span>/ 상품관리 일괄등록</span>
                <span>/ 상품관리 엑셀업로드</span>
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
                                                    <a href="/sample/sample_prd07.xlsx" class="ml-2" style="text-decoration: underline !important;">상품일괄등록양식 다운로드</a>
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
    const DEFAULT_STYLE = { 'background' : 'none', 'line-height': '30px'};

    const CELL_STYLE = {
        EDIT: { 'background': '#ffff99', 'line-height': '30px'},
        OK: { 'background': 'rgb(200,200,255)' },
        FAIL: { 'background': 'rgb(255,200,200)' }
    };
  
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_return_price: 0 }];

    let columns= [
            {field: "msg", headerName:"처리", pinned:'left', width:100, cellStyle: (params) => resultStyle(params)},
            {field: "com_id", headerName: "업체", width:100, pinned: 'left'},
            {field: "opt_kind_cd", headerName: "품목", width: 100, pinned: 'left'},
            {field: "brand", headerName: "브랜드", pinned: 'left'},
            {field: "rep_cat_cd", headerName: "대표카테고리", width: 100, pinned: 'left'},
            {field: "u_cat_cd", headerName: "용도카테고리", width: 100, pinned: 'left'},
            {field: "style_no", headerName: "스타일넘버", width: 120, pinned: 'left'},
            {field: "goods_nm", headerName: "상품명", width: 230, pinned: 'left'},
            {field: "goods_nm_eng", headerName: "상품영문명", width: 230},
            {headerName:"가격",
                children: [
                    {field: "goods_sh", headerName: "시중가", type: 'currencyType'},
                    {field: "price", headerName: "판매가", type: 'currencyType'},
                    {field: "wonga", headerName: "원가", width: 60, type: 'currencyType'},
                    {field: "margin_rate", headerName: "마진율(%)", width:84, type: 'percentType'},
                ]
            },
            {headerName:"상품옵션",
                children: [
                    {field: "option_kind", headerName: "옵션구분", width: 200},
                    {field: "opt1", headerName: "옵션1", width: 200,
                        editable: params => params.data.is_chk_opt_kind1 == true,
                        cellStyle: params => {
                            if (params.data.is_chk_opt_kind1 == true) {
                                return CELL_STYLE.EDIT;
                            }
                        }
                    },
                    {field: "opt2", headerName: "옵션2", width: 200, 
                        editable: params => params.data.is_chk_opt_kind2 == true,
                        cellStyle: params => {
                            if (params.data.is_chk_opt_kind2 == true) {
                                return CELL_STYLE.EDIT;
                            }
                        }
                    },
                    {field: "opt_qty", headerName: "수량"},
                    {field: "opt_price", headerName: "옵션가격", width: 200},
                ]
            },
            {field: "head_desc", headerName: "상단홍보글"},
            {field: "ad_desc", headerName: "하단홍보글"},
            {field: "dlv_pay_type", headerName: "배송비지불"},
            {field: "dlv_fee_cfg", headerName: "배송비설정"},
            {field: "bae_yn", headerName: "배송비여부"},
            {field: "baesong_price", headerName: "배송비"},
            {headerName: "적립금",
                children: [
                    {headerName: "설정", field: "point_cfg", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "지급", field: "point_yn", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "적립", field: "point", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'},
                    {headerName: "단위", field: "point_unit", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "금액", field: "point_amt", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'}
                ]
            },
            {field: "org_nm", headerName: "원산지"},
            {field: "md_nm", headerName: "MD"},
            {field: "make", headerName: "제조사"},
            {field: "goods_cont", headerName: "상품상세", width: 240},
            {field: "spec_desc", headerName: "제품사양"},
            {field: "baesong_desc", headerName: "예약/배송"},
            {field: "opinion", headerName: "MD상품평"},
            {field: "is_unlimited", headerName: "무한재고여부"},
            {field: "restock_yn", headerName: "재입고알림"},
            {field: "tax_yn", headerName: "과세구분"},
            {field: "goods_location", headerName: "상품위치"},
            {field: "tags", headerName: "상품태그"},
            {field: "com_type", hide: true},
            {field: "", headerName: "", width: "auto"}
        ];

</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275, 450);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
                onCellValueChanged: (e) => {
                    onCellValueChanged(e);

                    if (e.column.colId == "goods_sh" || e.column.colId == 'price' || e.column.colId == 'wonga') {
                        if (isNaN(e.newValue) == true || e.newValue == "") {
                            alert("숫자만 입력가능합니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        }
                    }
                },
                getRowNodeId: (data) => data.idx // 업데이터 및 제거를 위한 식별 ID 할당
            };
        gx = new HDGrid(gridDiv, columns, options);

        $('#excel_file').on('change', function(e){
            if (validateFile() === false) {
                $('.custom-file-label').html("");
                return;
            }
            $('.custom-file-label').html(this.files[0].name);
        });
    });

    // 판매가 원가 작성할 때 마진율값 자동입력
    async function onCellValueChanged(params) {
        if (params.oldValue == params.newValue) return;
        let row = params.data;
            
        if (row.price != null && row.wonga != null ) row.margin_rate = ((row.price - row.wonga)/row.price)*100;

        await gx.gridOptions.api.applyTransaction({ 
            update: [{...row}] 
        });
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
			'A': 'com_id',
			'B': 'opt_kind_cd',
			'C': 'brand',
			'D': 'rep_cat_cd',
			'E': 'u_cat_cd',
			'F': 'style_no',
			'G': 'goods_nm',
			'H': 'goods_nm_eng',
			'I': 'goods_sh',
			'J': 'price',
			'K': 'wonga',
			'L': 'margin_rate',
			'M': 'option_kind',
			'N': 'opt1',
			'O': 'opt2',
			'P': 'opt_qty',
			'Q': 'opt_price',
            'R': 'head_desc',
            'S': 'ad_desc',
            'T': 'dlv_pay_type',
            'U': 'dlv_fee_cfg',
            'V': 'bae_yn',
            'W': 'baesong_price',
            'X': 'point_cfg',
            'Y': 'point_yn',
            'Z': 'point',
            'AA': 'point_unit',
            'AB': 'point_amt',
            'AC': 'org_nm',
            'AD': 'md_nm',
            'AE': 'make',
            'AF': 'goods_cont',
            'AG': 'spec_desc',
            'AH': 'baesong_desc',
            'AI': 'opinion',
            'AJ': 'is_unlimited',
            'AK': 'restock_yn',
            'AL': 'tax_yn',
            'AM': 'goods_location',
            'AN': 'tags',
		};

        var firstRowIndex = 33; // 엑셀 33행부터 시작 (샘플데이터 참고)
		var rowIndex = firstRowIndex;

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

        axios({
            method: 'post',
            url: '/head/product/prd07/batch-import',
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
            url: '/head/product/prd07/batch-getproducts',
            method: 'post',
            data: { data: rows },
        }).then(async (res) => {
            let data = res.data.body;
            await gx.gridOptions.api.applyTransaction({add : data});
        }).catch((error) => {
            console.log(error);
        });
    };

    const save = () => { // 일괄 등록
        // if (validation()) {
            const data = gx.getRows();
            insertDB(data);
        // };
    };

    const insertDB = async (data) => {
        for (let i = 0; i < data.length; i++) {
            let row = data[i];
            const response = await axios({
                url: '/head/product/prd07/enroll2',
                method: 'post',
                data: { row: row }
            });
            const { result, msg } = response.data;
            row = { ...getRowNode(row).data, msg: msg, result: result };
            updateRow(row);
        }
    };

    const getRowNode = (row) => {
        return gx.gridOptions.api.getRowNode(row.idx);
    };

    const updateRow = (row) => {
        gx.gridOptions.api.applyTransaction({update : [{...row}]});
        // gx.gridOptions.api.applyTransaction({add : [{...row}]});
    };

    const resultStyle = (params) => {
        let STYLE = {...DEFAULT_STYLE, 'text-align': 'center'};
        if (params.data.result == undefined) return STYLE;
        if (params.data.result == '100' || params.data.result == '0') return STYLE = {...STYLE, ...CELL_STYLE.FAIL} // 중복된 스타일 넘버거나 시스템 에러
        if (params.data.result) return STYLE = {...STYLE, ...CELL_STYLE.OK} // 성공
    };


</script>
@stop
