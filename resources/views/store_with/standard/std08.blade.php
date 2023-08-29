@extends('store_with.layouts.layout')
@section('title','중간관리자수수료관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">중간관리자수수료관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보관리</span>
        <span>/ 중간관리자수수료관리</span>
    </div>
</div>

<style>
    @media (max-width: 740px) {
        #div-gd {height: 130px !important;}
    }
</style>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
		            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="grade_cd">수수료코드</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='grade_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name">수수료명</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='name' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm" onclick="return DataAdd();"><i class="bx bx-plus"></i> 추가</a>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm" onclick="return DataDel();"><i class="bx bx-trash"></i> 선택삭제</a>
	                <span class="mx-2">|</span>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DataSave();"><i class="bx bx-save text-white-50 mr-1"></i> 저장</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
        <div class="help-area mt-4">
            <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
            <ul class="help-list">
                <li>- 기본수수료 : 설정 금액까지의 판매금액</li>
                <li>- 초과1수수료 : 기본수수료 이후부터 설정 금액까지의 판매금액</li>
                <li>- 초과2수수료 : 초과1수수료 설정 금액 이후의 판매금액</li>
            </ul>
        </div>
    </div>
</div>

<script language="javascript">
    const CENTER = {'text-align': "center"};
    const YELLOW = {'background-color': "#ffff99"};
    let columns = [
        { field: "chk", headerName: '', cellClass: 'hd-grid-code', checkboxSelection: true, width: 28, pinned: 'left', sort: null },
        { field: "idx", hide: true },
        { field: "seq", hide: true },
        { field: "grade_cd", headerName: "수수료코드", width: 120, rowDrag: true, editable: (params) => isAdded(params), 
            cellStyle: (params) => isAdded(params) ? YELLOW : {}
        },
        { field: "name", headerName: "수수료명", width: 100, cellStyle: CENTER, editable: true, cellStyle: YELLOW },
        { field: "sdate", headerName: "시작월", width: 80, cellStyle: CENTER, editable: true, cellStyle: {...YELLOW, ...CENTER} },
        { field: "edate", headerName: "종료월", width: 80, cellStyle: CENTER,
            cellRenderer: (params) => params.data?.edate != "9999-99" && params.data?.edate ? params.data.edate : "-"
        },
        { field: "g1", headerName: "기본수수료",
            children: [
                { headerName: "판매금액", field: "amt1", type: 'currencyType', width:100, editable: true, cellStyle: YELLOW },
                { headerName: "수수료(%)", field: "fee1", type: 'percentType', width:100, editable: true, cellStyle: YELLOW },
            ]
        },
        { field: "g2", headerName: "초과1수수료",
            children: [
                { headerName: "판매금액", field: "amt2", type: 'currencyType', width:100, editable: true, cellStyle: YELLOW },
                { headerName: "수수료(%)", field: "fee2", type: 'percentType', width:100, editable: true, cellStyle: YELLOW },
            ]
        },
        { field: "g3", headerName: "초과2수수료",
            children: [
                // 정상3은 정상2 금액을 넘어가는 경우에 적용되며 금액 기준선은 동일
                { headerName: "판매금액", field: "amt2", type: 'currencyType', width:100,
                    valueFormatter: (params) => formatNumber(params) + ' ~'
                },
                { headerName: "수수료(%)", field: "fee3", type: 'percentType', width:100, editable: true, cellStyle: YELLOW },
            ]
        },
        { field: "fee_10", headerName: "행사", width: 60, type: 'percentType', editable: true, cellStyle: YELLOW },
        { field: "fee_11", headerName: "용품", width: 60, type: 'percentType', editable: true, cellStyle: YELLOW },
        { field: "fee_12", headerName: "특약온라인", width: 90, type: 'percentType', editable: true, cellStyle: YELLOW },
        { headerName: "행사기준",
            children: [
                { field: "fee_10_info", headerName: "할인율(%)", width: 80, type: 'percentType', editable: true, cellStyle: YELLOW },
                { field: "fee_10_info_over_yn", hide: true },
                { field: "fee_10_info_over_yn_nm", headerName: "이상/초과", width: 64, editable: true, cellStyle: {...CENTER, ...YELLOW},
                    cellEditorSelector: function(params) {
                        return {
                            component: 'agRichSelectCellEditor',
                            params: { 
                                values: ['이상', '초과']
                            },
                        };
                    },
                },
            ]
        },
        { field: "bigo", headerName: "비고", width: 200, editable: true, cellStyle: YELLOW },
        { width: 0 },
    ];
</script>
<script type="text/javascript" charset="utf-8">

    const isAdded = (params) => params?.data.added ? true : false;

    let gx;
    const pApp = new App('', { gridId: "#div-gd", height: 365 });
    
    $(document).ready(function() {
        pApp.ResizeGrid(365);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: (e) => {
                if (e.column.colId == "fee_10_info_over_yn_nm") {
                    e.data.fee_10_info_over_yn = e.newValue === '초과' ? 'Y' : 'N';
                }
            }
        });
        gx.gridOptions.rowDragManaged = true;
        gx.gridOptions.animateRows = true;
        Search();
    });

    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/standard/std08/search", data, -1, () => {});
    }

    // 검색조건 초기화
    function formReset(id) {
        document[id].reset();
    }

    function DataAdd() {
        let rows = gx.getRows();
        var newData = {
            idx: "",
            seq: rows.length,
            added: true
        };
        gx.gridOptions.api.applyTransaction({
            add: [newData],
            addIndex: rows.length,
        });
        gx.gridOptions.api.redrawRows();
    }

    async function DataSave() {
        if (confirm("중간관리자수수료 정보를 저장하시겠습니까?") === false) return;
        let arr = [];
        let rows = gx.getRows();
        let seq = 0;
        for (let i=0; i < rows.length; i++) {
            let row = rows[i];
            row.seq = seq;
            const is_valid = await validation(row, i);
            if (is_valid) {
                arr.push(row);
                seq++;
            } else {
                return false;
            }
        }
        try {
            const response = await axios({ 
                url: '/store/standard/std08/save',
                method: 'post', 
                data: { data: arr }
            });
            const { data } = response;
            if (data?.code == 200) {
                Search();
            } else {
                alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
            }
        } catch (error) {
            // console.log(error);
        }
    }

    async function DataDel() {
        let arr = [];
        const rows = gx.getSelectedRows();
        rows.map(row => {
            if (row.added) {
                gx.gridOptions.api.applyTransaction({remove : [row]});
                return false;
            }
            arr.push(row)
        });
        if (arr.length > 0 && confirm('삭제 하시겠습니까?')) {
            try {
                const response = await axios({ 
                    url: '/store/standard/std08/remove',
                    method: 'post', 
                    data: { data: arr } 
                });
                const { data } = response;
                if (data?.code == 200) {
                    Search();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            } catch (error) {
                // console.log(error);
            }
        }
    }

    const validation = (row, i)  => {
        const validSdate = new Date(row?.sdate);
        const validEdate = new Date(row?.edate);
        if (row?.grade_cd == "" || row?.grade_cd == null) {
            alert("등급 코드를 입력해 주세요.");
            startEditingCell(i, "grade_cd");
            return false;
        }
        if (row?.name == "" || row?.name == null) {
            alert("등급명을 입력해 주세요.");
            startEditingCell(i, "name");
            return false;
        }
        const regex = /\d{4}-(0[1-9]|1[012])/;
        let arr = row?.sdate?.match(regex);
        if (Array.isArray(arr) && arr.length > 0) {
        } else {
            alert("시작월은 연-월 형식으로 입력해 주세요.");
            startEditingCell(i, "sdate");
            return false;
        }
        return true;
    };

    const startEditingCell = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

</script>
@stop
