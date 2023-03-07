@extends('store_with.layouts.layout-nav')
@php
    $title = "기타재반자료 추가";
    if($cmd == "update") $title = "기타재반자료 상세내역";
@endphp
@section('title', $title)
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

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
                                            <td class="@if (!isset($store)) w-100 @endif">
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
                                            @if (isset($store))
                                            <th>매장정보</th>
                                            <td>
                                                <p class="fs-14 font-weight-bold">[{{ @$store->store_cd }}] {{ @$store->store_nm }}</p>
                                            </td>
                                            @endif
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
                @if (!isset($store))
                <div class="fr_box">
                    <button type="button" onclick="return openUploadModal();" class="btn btn-outline-primary mr-1"><i class="fas fa-plus fa-sm mr-1"></i> 엑셀일괄업로드</button>
                    <button type="button" onclick="return openUploadModal(true);" class="btn btn-outline-primary mr-1"><i class="fas fa-plus fa-sm mr-1"></i> 엑셀일괄업로드(원부자재포함)</button>
                </div>
                @endif
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

<!-- 엑셀파일 선택 및 적용 모달 -->
<div id="SelectFileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="SelectFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="SelectFileModalLabel">엑셀일괄업로드<span id="modal-sub-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="search_prdcd_range" method="get" onsubmit="return false">
                            <div class="card-body">
                                <div class="row_wrap code-filter">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="flex_box justify-content-end">
                                                <div class="custom-file">
                                                    <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
                                                    <label class="custom-file-label" for="file"></label>
                                                </div>
                                                <a href="/sample/sample_extra_acc.xlsx" class="mt-2" id="sample_file_link" style="text-decoration: underline !important;">일괄등록양식 다운로드</a>
                                                <a href="/sample/sample_old_extra_acc.xlsx" class="mt-2" id="sample_file_link2" style="text-decoration: underline !important;" hidden>(원부자재포함) 일괄등록양식 다운로드</a>
                                                {{-- file_type: G(기본) / S(원부자재포함) --}}
                                                <input type="hidden" id="file_type" value="G">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 text-center mt-3">
                                    <a href="#" onclick="return upload();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50 pr-1"></i> 적용</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    const CMD = "{{ @$cmd }}";
    const EXCLUED_TOTAL = "{{ @$extra_etc->exclude_total }}"; // 합계 계산 시 제외타입
    const EXCEPT_VAT = "{{ @$extra_etc->except_vat }}"; // 세금제외타입
    const S_PAYERS = "{{ @$extra_etc->pay_for_s }}"; // 매장부담타입
    const C_PAYERS = "{{ @$extra_etc->pay_for_c }}"; // 본사부담타입
    
    const CLOSED_STATUS = { 'Y': '마감완료', 'N': '마감추가' };
    const PAYER = { 'C': '(본사부담)', 'S': '(매장부담)' };

	const YELLOW = { 'background-color': '#ffff99' };
	const CENTER = { 'text-align': 'center' };
    const setEditable = (params, cond = true) => cond && params.node.rowPinned !== 'top' && params.data.closed_yn !== 'Y';

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
        @foreach ($extra_cols as $entry_cd => $children)
			@if ($entry_cd !== '')
				{ headerName: `{{ $children[0]->entry_nm }} ${ PAYER["{{ $children[0]->payer }}"] || '' }`,
					children: [
						@foreach ($children as $child)
							{ headerName: "{{ $child->type_nm }}", field: "{{ $child->type_cd }}_amt", type: 'currencyType', width: 100,
                                editable: (params) => setEditable(params, "{{ $child->type_cd }}" !== 'P3'), 
                                cellStyle: (params) => setEditable(params, "{{ $child->type_cd }}" !== 'P3') ? YELLOW : {},
                                cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0),
                            },
							@if ($child->except_vat_yn === 'Y')
							{ headerName: "{{ $child->type_nm }}(-VAT)", field: "{{ $child->type_cd }}_novat", type: 'currencyType', width: 105,
								cellRenderer: (params) => params.data["{{ $child->type_cd }}_amt"] ? Comma(Math.round((params.data["{{ $child->type_cd }}_amt"] || 0) / 1.1)) : (CMD === 'add' ? '' : 0),
							},
							@endif
						@endforeach
						@if (!in_array($entry_cd, ['M', 'O']))
						{ headerName: "소계", field: "{{ $entry_cd }}_sum", type: 'currencyType', width: 100,
                            cellRenderer: (params) => params.value !== null ? Comma(params.value) : (CMD === 'add' ? '' : 0),
                        },
						@endif
					]
				},
			@else
				@foreach ($children as $child)
					{ headerName: `{{ $child->type_nm }} ${ PAYER["{{ $child->payer }}"] || '' }`, field: "{{ $child->type_cd }}", type: 'currencyType', width: 120 },
				@endforeach
			@endif
		@endforeach
		{ field: "C_total", headerName: "본사부담금 합계", type: 'currencyType', width: 100, cellStyle: { "font-weight": "700" } }, // 추가지급금
		{ field: "S_total", headerName: "매장부담금 합계", type: 'currencyType', width: 100, cellStyle: { "font-weight": "700" } }, // 공제금
        { width: "auto" }
    ];
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;

    let is_file_applied = false;
    let applied_date = "{{ @$sdate }}";
    let applied_store_cd = "{{ isset($store) ? @$store->store_cd : '' }}";

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

						if (['P1', 'P2'].includes(e.column.colId.split("_")[0])) {
							e.data['P3_amt'] = e.data['P1_amt'] - e.data['P2_amt'];
						}

						// 각 소계 계산
						e.data[group_cd + "_sum"] 
							= Object.keys(e.data).reduce((a,c) => (
								(c.split("")[0] === group_cd && c.split("_").slice(-1)[0] === "amt" && !EXCLUED_TOTAL.split(",").includes(c.split("_")[0])) 
									? EXCEPT_VAT.split(",").includes(c.split("_")[0])
                                        ? Math.round(e.data[c] * 1 / 1.1) 
                                        : (e.data[c] * 1) 
                                    : 0
							) + a, 0);

						// 총합계 계산
						e.data.C_total = Object.keys(e.data).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" && C_PAYERS.includes(c.split("_")[0]) ? (e.data[c] * 1) : 0) + a, 0);
						e.data.S_total = Object.keys(e.data).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" && S_PAYERS.includes(c.split("_")[0]) ? (e.data[c] * 1) : 0) + a, 0);

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
        
        if (CMD === 'update') Search();
        updatePinnedRow();

        /** 엑셀 관련 */
        $('#excel_file').on('change', function(e) {
            if (validateFile() === false) {
                $('.custom-file-label').html("");
                return;
            }
            $('.custom-file-label').html(this.files[0].name);
        });
        $('#SelectFileModal').on('hide.bs.modal', function (e) {
            $('#excel_file').val('');
            $('.custom-file-label').html('');
        });
    });

    function Search(applied = false, callback = null) {
        if (applied && gx.getRows().length > 0) {
            if (!confirm("적용 시, 기존에 작성되었던 정보는 저장되지 않습니다.\n해당 판매기간을 적용하시겠습니까?")) return;
        }

        let data = $('form[name="search"]').serialize();
        data += "&cmd=" + CMD;

        if (applied_store_cd !== '') data += "&store_cd=" + applied_store_cd;

		gx.Request('/store/account/acc05/show-search', data, -1, function(e) {
            if (e.code === 200) {
                setColumns(e.head);
                is_file_applied = true;
                if (CMD === 'add') applied_date = $("#sdate").val();
                updatePinnedRow();
                if (callback !== null) callback();
            } else {
                is_file_applied = false;
                alert(e.head.msg);
                console.log(e);
            }
        });
    }

    function setColumns({ gifts = [], expandables = [] }) {
		const cols = columns.reduce((a, c) => {
			let col = {...c};
			if(col.field === 'G') {
				col.children = gifts.map(gf => ({ 
                        headerName: gf.prd_nm, 
                        field: "G_" + gf.prd_cd + "_amt", 
                        type: 'currencyType', width: 100, 
                        editable: (params) => setEditable(params),
                        cellStyle: (params) => setEditable(params) ? YELLOW : {}, 
                        cellRenderer: (params) => params.value !== null ? Comma(params.value || 0) : (CMD === 'add' ? '' : 0) 
                    })).concat({ 
                        headerName: "소계", 
                        field: "G_sum", 
                        type: 'currencyType', width: 100, 
                        cellRenderer: (params) => params.value !== null ? Comma(params.value || 0) : (CMD === 'add' ? '' : 0) 
                    });
			}
            if(col.field === 'E') {
				col.children = expandables.map(exp => ({ 
                    headerName: exp.prd_nm, 
                    field: "E_" + exp.prd_cd  + "_amt", 
                    type: 'currencyType', width: 100, 
                    editable: (params) => setEditable(params),
                    cellStyle: (params) => setEditable(params) ? YELLOW : {}, 
                    cellRenderer: (params) => params.value !== null ? Comma(params.value || 0) : (CMD === 'add' ? '' : 0) 
                })).concat({ 
                    headerName: "소계", 
                    field: "E_sum", 
                    type: 'currencyType', width: 100, 
                    cellRenderer: (params) => params.value !== null ? Comma(params.value || 0) : (CMD === 'add' ? '' : 0) 
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

        const file_type = $("#file_type").val(); // G(기본) / S(원부자재포함)
        let colDef = [];

        colDef = gx.gridOptions.columnApi.columnController.columnDefs
            .reduce((a,c) => {
                let result = {};
                if(['G', 'E'].includes(c.field) && c.children && c.children.length > 0) {
                    result = c.children
                        .filter(child => !['G_sum', 'E_sum'].includes(child.field))
                        .reduce((aa, cc) => {
                            let vv = {};
                            vv[cc.field] = cc.headerName;
                            return {...aa, ...vv};
                        }, {});
                }
                return {...a, ...result};
            }, {});

		axios({
            url: '/store/account/acc05/save',
            method: 'post',
            data: { 
                cmd: CMD, 
                type: file_type,
                data: rows, 
                cols: colDef, 
                sdate: applied_date 
            }
        }).then((res) => {
            if (res.data.code === "200") {
                alert("자료가 정상적으로 저장되었습니다.");
                if (opener) opener.Search();
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
            .filter(c => !['num', 'store_cd', 'store_nm', 'G', 'E', undefined].includes(c.field));

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
        $("#modal-sub-title").text(with_sproduct ? '(원부자재포함)' : '');
        $("#sample_file_link").attr("hidden", with_sproduct);
        $("#sample_file_link2").attr("hidden", !with_sproduct);

        $("#file_type").val(with_sproduct ? 'S' : 'G');

        $("#SelectFileModal").draggable();
        $('#SelectFileModal').modal({ keyboard: false });
    }
    
    /** 
     * 엑셀 관련 함수
     * - read the raw data and convert it to a XLSX workbook
    */

    // 일괄등록 엑셀파일 적용
    const upload = () => {
        const file_data = $('#excel_file').prop('files')[0];
        if(!file_data) return alert("적용할 파일을 선택해주세요.");

        if((gx.getRows().length > 0 || is_file_applied) && !confirm("적용 시, 기존에 작성되었던 정보는 저장되지 않습니다.\n파일을 적용하시겠습니까?\n판매기간 : " + applied_date)) return;
        is_file_applied = true;

        Search(false, () => {
            
            const form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('_token', "{{ csrf_token() }}");

            $("#SelectFileModal").modal('hide');
            alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
            
            axios({
                url: '/store/account/acc05/batch-import',
                method: 'post',
                headers: { "Content-Type": "multipart/form-data" },
                data: form_data,
            }).then(async (res) => {
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
        });
    }

    const populateGrid = async (workbook) => {
        let firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
		let worksheet = workbook.Sheets[firstSheetName];

        const file_type = $("#file_type").val(); // G(기본) / S(원부자재포함)

		let excel_columns = {
			'B': 'store_cd', 'C': 'store_nm',
			'D': 'M1_amt',
			'G': 'P1_amt', 'H': 'P2_amt', 'J': 'P4_amt', 'K': 'P5_amt', 'L': 'P6_amt',
			'N': 'S1_amt', 'O': 'S2_amt', 'P': 'S3_amt', 'R': 'S4_amt',
			'S': 'O1_amt', 'T': 'O2_amt', 'U': 'O3_amt'
        };

        let g_types = [];
        let e_types = [];

        if (file_type === 'S') {
            excel_columns = {
                ...excel_columns,
                'W': 'G', 'X': 'G', 'Y': 'G', 'Z': 'G', 'AA': 'G', 'AB': 'G', 'AC': 'G', 'AD': 'G', 'AE': 'G', 'AF': 'G',
			    'AH': 'E', 'AI': 'E', 'AJ': 'E', 'AK': 'E', 'AL': 'E', 'AM': 'E', 'AN': 'E', 'AO': 'E', 'AP': 'E', 'AQ': 'E'
            };

            g_types = ['W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF']
                .map(v => ({ prd_nm: worksheet[v + '4']?.v, prd_cd: v, type: 'G', colId: v }))
                .filter(v => v.prd_nm !== '-' && v.prd_nm !== '');
            e_types = ['AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ']
                .map(v => ({ prd_nm: worksheet[v + '4']?.v, prd_cd: v, type: 'E', colId: v }))
                .filter(v => v.prd_nm !== '-' && v.prd_nm !== '');
        }


        let firstRowIndex = 5; // 엑셀 5행부터 시작 (샘플데이터 참고)
		let rowIndex = firstRowIndex; 

        let rows = [];
        let group_cd = '';
		while (worksheet['B' + rowIndex]) {
			let row = {};
			Object.keys(excel_columns).forEach((column) => {
                let item = worksheet[column + rowIndex];
				if (item !== undefined) {
                    let val = item.v;
                    if (
                        !['store_cd', 'store_nm'].includes(excel_columns[column]) 
                        && (isNaN(item.v) || item.v == '' || parseFloat(item.v) < 0)
                    ) val = 0;

                    if (file_type === 'S') {
                        if (excel_columns[column] === 'G') {
                            let p = g_types.filter(g => g.colId === column)?.[0];
                            if (p) row[`G_${p.prd_cd}_amt`] = val;
                        } else if (excel_columns[column] === 'E') {
                            let p = e_types.filter(s => s.colId === column)?.[0];
                            if (p) row[`E_${p.prd_cd}_amt`] = val;
                        } else {
                            row[excel_columns[column]] = val;
                        }
                    } else {
                        row[excel_columns[column]] = val;
                    }
				}
			});

            // 온라인항목 처리
            row = {...row, 'P3_amt': (row['P1_amt'] || 0) - (row['P2_amt'] || 0)};

            // 각 소계 계산
            @foreach ($extra_cols as $entry_cd => $children)
                group_cd = "{{ $entry_cd }}";
                row[group_cd + "_sum"]
                    = Object.keys(row).reduce((a,c) => (
                        (c.split("")[0] === group_cd && c.split("_").slice(-1)[0] === "amt" && !EXCLUED_TOTAL.split(",").includes(c.split("_")[0])) 
                            ? EXCEPT_VAT.split(",").includes(c.split("_")[0])
                                ? Math.round(row[c] * 1 / 1.1) 
                                : (row[c] * 1) 
                            : 0
                    ) + a, 0);                
            @endforeach

            if (file_type === 'S') {
                row["G_sum"]
                    = Object.keys(row).reduce((a,c) => (
                        (c.split("")[0] === 'G' && c.split("_").slice(-1)[0] === "amt") 
                            ? (row[c] * 1) : 0
                    ) + a, 0);    
    
                row["E_sum"]
                    = Object.keys(row).reduce((a,c) => (
                        (c.split("")[0] === 'E' && c.split("_").slice(-1)[0] === "amt")
                            ? (row[c] * 1) : 0
                    ) + a, 0);    
            }

            // 총합계 계산
            row.C_total = Object.keys(row).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" && C_PAYERS.includes(c.split("_")[0]) ? (row[c] * 1) : 0) + a, 0);
            row.S_total = Object.keys(row).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" && S_PAYERS.includes(c.split("_")[0]) ? (row[c] * 1) : 0) + a, 0);

            rows.push(row);
            rowIndex++;
		}

        if(rows.length < 1) return alert("한 개 이상의 매장자료를 입력해주세요.");
        rows = rows.filter(r => r.store_cd);

        if (file_type === 'S') {
            await setColumns({ gifts: g_types, expandables: e_types });
        }

        const rowsToUpdate = [];
        gx.gridOptions.api.forEachNode(function(node) {
            let data = [];
            let old_data = node.data;
            const item = rows.filter(row => row.store_cd === old_data.store_cd);

            if (item.length > 0) {
                if (file_type === 'S') {
                    Object.keys(old_data)
                        .filter(key => ['G', 'E'].includes(key.split("")[0]) && key.split("_").slice(-1)[0] === "amt")
                        .forEach(key => {
                            delete old_data[key];
                        });
                }

                if (old_data.closed_yn === 'Y') {
                    data = { ...old_data };
                } else {
                    data = { ...old_data, ...item[0], store_nm: old_data.store_nm };
                }
                data.C_total = Object.keys(data).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" && C_PAYERS.includes(c.split("_")[0]) ? (data[c] * 1) : 0) + a, 0);
                data.S_total = Object.keys(data).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" && S_PAYERS.includes(c.split("_")[0]) ? (data[c] * 1) : 0) + a, 0);
            }

            rowsToUpdate.push(data);
        });

        await gx.gridOptions.api.setRowData([]);
        await gx.gridOptions.api.applyTransaction({ add : rowsToUpdate });
        await updatePinnedRow();
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

</script>
@stop
