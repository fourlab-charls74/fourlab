@extends('store_with.layouts.layout-nav')
@php
    $title = "기타재반자료 추가";
    if($cmd == "update") $title = "기타재반자료 상세내역";
@endphp
@section('title', $title)
@section('content')

<!-- import excel lib -->
{{-- <script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script> --}}

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>매장관리</span>
                <span>/ 정산/마감관리</span>
                <span>/ 기타재반자료</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="return Save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
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
        <div class="card shadow mt-3" id="basic_info_form">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">기본정보</a>
            </div>
            <div class="card-body">
                <form name="search">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th>판매기간(판매연월)</th>
                                            <td class="w-100">
                                                <div class="docs-datepicker w-100 flex_box">
                                                    @if (@$cmd === 'add')
                                                    <div class="input-group" style="max-width:300px;">
                                                        <input type="text" id="sdate" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="docs-datepicker-container"></div>
                                                    <a href="javascript:void(0)" onclick="return Search(true);" class="btn btn-outline-primary ml-2">적용</a>
                                                    @else
                                                    <input type="hidden" name="sdate" value="{{ @$sdate }}">
                                                    <p class="fs-14 font-weight-bold">{{ @$sdate_str }}</p>
                                                    @endif
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
        <div class="card shadow mt-3 pb-2">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">기타재반자료</a>
                <div class="fl_box">
                    <button type="button" onclick="return openUploadModal();" class="btn btn-outline-primary mr-1"><i class="fas fa-plus fa-sm mr-1"></i> 엑셀일괄업로드</button>
                    <button type="button" onclick="return openUploadModal({});" class="btn btn-outline-primary mr-1"><i class="fas fa-plus fa-sm mr-1"></i> 엑셀일괄업로드(원부자재포함)</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
                <p class="mt-2 text-success">* 해당연월에 <u class="font-weight-bold">마감완료처리</u>된 매장의 기타재반자료는 수정할 수 없습니다.</p>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    const CMD = "{{ @$cmd }}";
    
    const CLOSED_STATUS = { 'Y': '마감완료', 'N': '마감추가' };
	const YELLOW = { 'background-color': "#ffff99" };
	const CENTER = { 'text-align': 'center' };
    const setEditable = (params, cond = true) => cond && params.data.closed_yn !== 'Y';

    const columns = [
		{ headerName: "#", field: "num", type: 'NumType', pinned: 'left', width: 30, cellStyle: CENTER,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
        },
        { field: "closed_yn", headerName: "마감상태", pinned: 'left', width: 57,
            cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : (CLOSED_STATUS[params.value] || '-'),
            cellStyle: (params) => ({
                ...CENTER, 
                "background-color": params.value === 'Y' ? '#E2FFE0' : params.value === 'N' ? '#FFE9E9' : 'none',
                "color": params.value === 'Y' ? '#0BAC00' : params.value === 'N' ? '#ff0000' : 'none'
            }),
        },
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 57, cellStyle: CENTER },
        { field: "store_nm", headerName: "매장명", pinned: 'left', type: 'StoreNameType', width: 170 },
		@foreach ($extra_cols as $group_nm => $children)
		{ headerName: "{{ $group_nm }}",
			children: [
				@foreach ($children as $child)
					{ headerName: "{{ $child->code_val }}", field: "{{ $child->code_id }}_amt", type: 'currencyType', width: 100, 
						editable: (params) => setEditable(params, "{{ $child->code_id }}" !== 'E3' && params.node.rowPinned !== 'top'), 
                        cellStyle: (params) => setEditable(params, "{{ $child->code_id }}" !== 'E3' && params.node.rowPinned !== 'top') ? YELLOW : {},
                        cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0),
					},
					@if (in_array($child->code_id, ['P1', 'M3']))
					{ headerName: "{{ $child->code_val }}(-VAT)", field: "{{ $child->code_id }}_novat", type: 'currencyType', width: 105,
						cellRenderer: (params) => params.data["{{ $child->code_id }}_amt"] ? Comma(Math.round((params.data["{{ $child->code_id }}_amt"] || 0) / 1.1)) : (CMD === 'add' ? '' : 0),
					},
					@endif
				@endforeach
				@if (!in_array($group_nm, ['마일리지', '기타운영경비']))
				{ headerName: "소계", field: "{{ str_split($children[0]->code_id ?? '')[0] }}_sum", type: 'currencyType', width: 100,
                    cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0),
                },
				@endif
			]
        },
		@if ($group_nm === '관리')
		{ headerName: "사은품", field: "gifts" },
		{ headerName: "소모품", field: "expandables" },
		@endif
		@endforeach
		{ field: "total", headerName: "총합계", type: 'currencyType', width: 100 },
        { width: "auto" }
    ];
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;

    let is_file_applied = false;
    let applied_date = "{{ @$sdate }}";

    $(document).ready(function() {
        pApp.ResizeGrid(340);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: [{ store_cd: "합계" }],
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee'};
            },
			onCellValueChanged: (e) => {
				if (e.oldValue !== e.newValue) {
					const val = e.newValue;
					if (isNaN(val) || val == '' || parseFloat(val) < 0) {
						alert("숫자만 입력가능합니다.");
						e.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
					} else {
						const group_cd = e.column.colId.split("")[0];

						// E1(온라인RT), E2(온라인반송)은 소계에 포함시키지 않습니다. (because, E3(온라인) = E1 - E2)
						if (['E1', 'E2'].includes(e.column.colId.split("_")[0])) {
							e.data['E3_amt'] = e.data['E1_amt'] - e.data['E2_amt'];
						}

						// 각 소계 계산
						e.data[group_cd + "_sum"] 
							= Object.keys(e.data).reduce((a,c) => (
								(c.split("")[0] === group_cd && c.split("_").slice(-1)[0] === "amt" && !['E1', 'E2'].includes(c.split("_")[0])) 
									? ['P1', 'M3'].includes(c.split("_")[0])
                                        ? Math.round(e.data[c] * 1 / 1.1) 
                                        : (e.data[c] * 1) 
                                    : 0
							) + a, 0);

						// 총합계 계산
						e.data.total = Object.keys(e.data).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" ? (e.data[c] * 1) : 0) + a, 0);

						e.api.redrawRows({ rowNodes: [e.node] });
                        updatePinnedRow();
						gx.setFocusedWorkingCell();
					}
				}
			}
		});
		gx.gridOptions.defaultColDef = {
			suppressMenu: true,
			resizable: false,
			sortable: true,
		};

        // $('#excel_file').on('change', function(e){
        //     if (validateFile() === false) {
        //         $('.custom-file-label').html("");
        //         return;
        //     }
        //     $('.custom-file-label').html(this.files[0].name);
        // });

        if (CMD === 'update') Search();
        updatePinnedRow();
    });

    function Search(applied = false) {
        if (applied && gx.getRows().length > 0) {
            if (!confirm("적용 시, 기존에 작성되었던 정보는 저장되지 않습니다.\n해당 판매기간을 적용하시겠습니까?")) return;
        }

        let data = $('form[name="search"]').serialize();
        data += "&cmd=" + CMD;

		gx.Request('/store/account/acc05/show-search', data, -1, function(e) {
            if (e.code === 200) {
                setColumns(e.head);
                is_file_applied = true;
                if (CMD === 'add') applied_date = $("#sdate").val();
                updatePinnedRow();
            } else {
                alert(e.head.msg);
                console.log(e);
            }
        });
    }

    function setColumns({gifts, expandables}) {
		const cols = columns.reduce((a, c) => {
			let col = {...c};
			if(col.field === 'gifts') {
				col.children = gifts.map(gf => ({ 
                        headerName: gf.prd_nm, 
                        field: "G_" + gf.prd_cd + "_amt", 
                        type: 'currencyType', width: 100, 
                        editable: (params) => setEditable(params, params.node.rowPinned !== 'top'),
                        cellStyle: (params) => setEditable(params, params.node.rowPinned !== 'top') ? YELLOW : {}, 
                        cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0) 
                    })).concat({ 
                        headerName: "소계", 
                        field: "G_sum", 
                        type: 'currencyType', width: 100, 
                        cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0) 
                    });
			}
            if(col.field === 'expandables') {
				col.children = expandables.map(exp => ({ 
                    headerName: exp.prd_nm, 
                    field: "S_" + exp.prd_cd  + "_amt", 
                    type: 'currencyType', width: 100, 
                    editable: (params) => setEditable(params, params.node.rowPinned !== 'top'),
                    cellStyle: (params) => setEditable(params, params.node.rowPinned !== 'top') ? YELLOW : {}, 
                    cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0) 
                })).concat({ 
                    headerName: "소계", 
                    field: "S_sum", type: 'currencyType', width: 100, 
                    cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0) 
                });
			}
			a.push(col);
			return a;
		}, []);

		gx.gridOptions.api.setColumnDefs([]);
		gx.gridOptions.api.setColumnDefs(cols);
    }

    // 저장
    function Save() {
        let rows = gx.getRows();
        if (rows.length < 1 || !is_file_applied) return alert("저장할 자료를 적용해주세요.");

		if (!confirm(`[${applied_date}]의 기타재반자료를 저장하시겠습니까?`)) return;
		alert("다소 시간이 소요될 수 있습니다. 잠시만 기다려주세요.");

		axios({
            url: '/store/account/acc05/save',
            method: 'post',
            data: { cmd: CMD, data: rows, sdate: applied_date }
        }).then((res) => {
            if (res.data.code === "200") {
                alert("자료가 정상적으로 저장되었습니다.");
                opener.Search();
                self.close();
            } else {
                alert("자료저장 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
                console.log(res);
            }
        }).catch((err) => {
            alert("에러가 발생했습니다. 관리자에게 문의해주세요.");
            console.log(err);
        });
    }

    // 합계 row 업데이트
    const updatePinnedRow = () => {
        let cols = gx.gridOptions.columnApi.columnController.columnDefs
            .reduce((a,c) => {
                let result = [];
                if(c.children && c.children.length > 0) result = result.concat(c.children);
                return a.concat(result).concat(c);
            }, [])
            .map(c => ({ field: c.field, value: 0 }))
            .filter(c => !['num', 'store_cd', 'store_nm', 'gifts', 'expandables', undefined].includes(c.field));

        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                cols.forEach((col) => {
                    col.value += parseFloat(row[col.field] || 0);
                });
            });
        }

        cols = cols.reduce((a,c) => {
            a[c.field] = c.value;
            return a;
        }, {});

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, ...cols }
        ]);
    };

    // 일괄업로드모달 오픈 (with_sproduct: 원부자재포함여부)
    function openUploadModal(with_sproduct = false) {
        alert("개발중입니다.");
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////// 아래 작업중 ////////////////////////////////////////////

    /** 
     * 엑셀 관련 함수
     * - read the raw data and convert it to a XLSX workbook
    */
    
    // 파일 적용
    const upload = () => {
        const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

        if(is_file_applied && !confirm("새로 적용하시는 경우 기존에 수정하신 정보는 저장되지 않습니다.\n적용하시겠습니까?")) return;
        is_file_applied = true;
        
		const form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        axios({
            url: '/store/account/acc05/batch-import',
            method: 'post',
            headers: { "Content-Type": "multipart/form-data" },
            data: form_data,
        }).then(async (res) => {
            gx.gridOptions.api.setRowData([]);
            if (res.data.code == 1) {
                const file = res.data.file;
                await importExcel("/" + file);
            } else {
                alert("엑셀파일 적용 중 에러가 발생했습니다. 다시 시도해주세요.");
                console.log(res);
            }
        }).catch((error) => {
            alert("에러가 발생했습니다. 관리자에게 문의해주세요.");
            console.log(error);
        });
    }

    const populateGrid = async (workbook) => {
		let firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
		let worksheet = workbook.Sheets[firstSheetName];

		let excel_columns = {
			'B': 'store_cd',
			'C': 'store_nm',
			'E': 'P1_amt',
			'J': 'E1_amt', 'K': 'E2_amt', 'M': 'E4_amt', 'N': 'E5_amt', 'O': 'E6_amt',
			'Q': 'M1_amt', 'R': 'M2_amt', 'S': 'M3_amt', 'U': 'M4_amt',
			'V': 'G', 'W': 'G', 'X': 'G', 'Y': 'G', 'Z': 'G', 'AA': 'G', 'AB': 'G', 'AC': 'G', 'AD': 'G', 'AE': 'G',
			'AG': 'S', 'AH': 'S', 'AI': 'S', 'AJ': 'S', 'AK': 'S', 'AL': 'S', 'AM': 'S', 'AN': 'S', 'AO': 'S', 'AP': 'S',
			'AS': 'O1_amt', 'AT': 'O2_amt', 'AU': 'O3_amt'
        };

        const g_types = ['V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE']
            .map(v => ({prd_nm: worksheet[v + '7']?.v, prd_cd: worksheet[v + '8']?.v, type: 'G', colId: v}))
            .filter(v => v.prd_nm !== '-' && v.prd_nm !== '');
        const s_types = ['AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP']
            .map(v => ({prd_nm: worksheet[v + '7']?.v, prd_cd: worksheet[v + '8']?.v, type: 'S', colId: v}))
            .filter(v => v.prd_nm !== '-' && v.prd_nm !== '');

        let firstRowIndex = 9; // 엑셀 9행부터 시작 (샘플데이터 참고)
		let rowIndex = firstRowIndex; 

        let count = gx.gridOptions.api.getDisplayedRowCount();
        let rows = [];
        let group_cd = '';
        let p = '';
		while (worksheet['B' + rowIndex]) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if (item !== undefined) {
                    if (excel_columns[column] === 'G') {
                        p = g_types.filter(g => g.colId === column)?.[0];
                        if (p) row[`G_${p.prd_cd || p.colId}_amt`] = item.v;
                    } else if (excel_columns[column] === 'S') {
                        p = s_types.filter(s => s.colId === column)?.[0];
                        if (p) row[`S_${p.prd_cd || p.colId}_amt`] = item.v;
                    } else {
                        row[excel_columns[column]] = item.v;
                    }
				}
			});

            // 온라인항목 처리
            row = {...row, 'E3_amt': (row['E1_amt'] || 0) - (row['E2_amt'] || 0)};

            // 각 소계 계산
            @foreach ($extra_cols as $group_nm => $children)
            group_cd = "{{ str_split($children[0]->code_id ?? '')[0] }}";
            row[group_cd + "_sum"]
                = Object.keys(row).reduce((a,c) => (
                    (c.split("")[0] === group_cd && c.split("_").slice(-1)[0] === "amt" && !['E1', 'E2'].includes(c.split("_")[0])) 
                        ? (row[c] * 1) : 0
                ) + a, 0);                
            @endforeach

            row["G_sum"]
                = Object.keys(row).reduce((a,c) => (
                    (c.split("")[0] === 'G' && c.split("_").slice(-1)[0] === "amt") 
                        ? (row[c] * 1) : 0
                ) + a, 0);    

            row["S_sum"]
                = Object.keys(row).reduce((a,c) => (
                    (c.split("")[0] === 'S' && c.split("_").slice(-1)[0] === "amt")
                        ? (row[c] * 1) : 0
                ) + a, 0);    

            // 총합계 계산
            row.total = Object.keys(row).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" ? (row[c] * 1) : 0) + a, 0);

            rows.push(row);
            rowIndex++;
		}

        if(rows.length < 1) return alert("한 개 이상의 매장자료를 입력해주세요.");
        rows = rows.filter(r => r.store_cd);
        await setColumns(g_types, s_types);
        await gx.gridOptions.api.applyTransaction({ add : rows });
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

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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
</script>
@stop
