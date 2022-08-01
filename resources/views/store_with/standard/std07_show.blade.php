@extends('store_with.layouts.layout-nav')
@php
    $title = "매장마진관리 - " . @$store->store_nm;
@endphp
@section('title', $title)

@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 코드관리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">매장마진정보 변경내역</a>
                <div>
                    <button type="button" onclick="addData();" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-plus"></i> 추가</button>
                    <button type="button" onclick="removeData();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-trash"></i> 삭제</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="table-responsive">
                            <div id="div-gd" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
        {field: "use_yn", headerName: "사용", pinned: "left", 
            cellStyle: function(params) {
                return {"text-align": "center", "color": params.value === "A" ? "red" : ""};
            },
            cellRenderer: function(params) {
                return params.value === "Y" ? "Y" : params.value === "A" ? "추가" : "";
            }
        },
        {field: "pr_code_cd", headerName: "행사코드", width: 60, cellStyle: {"text-align": "center"}},
        {field: "pr_code_nm", headerName: "행사명", width: 60, cellStyle: {"text-align": "center"}},
        {field: "sdate", headerName: "시작일", width: 90, 
            cellStyle: function(params) {
                return {"text-align": "center", "background-color": params.data.use_yn === "A" ? "#ffff99" : ""};
            },
            cellRenderer: (params) => {
                return `<input type="date" class="grid-date" value="${params.value ?? ''}" onchange="changeNodeData('sdate', this, '${params.rowIndex}')" ${params.data.use_yn === "A" ? '' : 'readonly'} />`;
            }
        },
        {field: "edate", headerName: "종료일", width: 90, cellStyle: {"text-align": "center"},
            cellRenderer: (params) => {
                return `<input type="date" class="grid-date" value="${params.value ?? ''}" readonly />`;
            }
        },
        {field: "store_fee", headerName: "매장수수료(%)", width: 120, type: "percentType", 
            cellStyle: function(params) {
                return {"background-color": params.data.use_yn === "A" ? "#ffff99" : ""};
            }, 
            editable: function(params) {
                return params.data.use_yn === "A";
            }
        },
        {field: "manager_fee", headerName: "중간관리수수료(%)", width: 120, type: "percentType", 
            cellStyle: function(params) {
                return {"background-color": params.data.use_yn === "A" ? "#ffff99" : ""};
            }, 
            editable: function(params) {
                return params.data.use_yn === "A";
            }
        },
        {field: "comment", headerName: "메모", width: 235, 
            cellStyle: function(params) {
                return {"background-color": params.data.use_yn === "A" ? "#ffff99" : ""};
            }, 
            editable: function(params) {
                return params.data.use_yn === "A";
            }
        },
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });
    let has_new = false;

    let store_cd = "{{ @$store->store_cd }}";
    let store_nm = "{{ @$store->store_nm }}";
    let pr_code_cd = "{{ @$pr_code->pr_code_cd }}";
    let pr_code_nm = "{{ @$pr_code->pr_code_nm }}";

    $(document).ready(function() {
        pApp.ResizeGrid(275, 350);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            onCellValueChanged: (e) => {
                if (e.column.colId.includes("fee")) {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    }
                }
            }
        });
        Search();
    });

    // 매장마진정보 변경내역 조회
    function Search() {
        let params = "store_cd=" + store_cd + "&pr_code_cd=" + pr_code_cd;
        gx.Request("/store/standard/std07/search-store-fee-history", params, -1, function(e) {
            resetAddBtn();
        });
    }

    // 마진정보 추가
    function addData() {
        if(has_new) return;
        
        let rows = gx.getRows();
        let new_row = {
            use_yn: "A",
            store_cd: store_cd,
            pr_code_cd: pr_code_cd,
            pr_code_nm: pr_code_nm,
            sdate: getDateObjToStr(new Date()), // 오늘 날짜
            edate: '9999-12-31',
            store_fee: 0,
            manager_fee: 0,
            comment: '',
        };

        // if(rows.length > 0) {
        //     rows = rows.map(row => ({...row, edate: row.use_yn === "Y" ? getDateObjToStr(new Date()) : row.edate}));
        // }
        gx.gridOptions.api.setRowData([new_row, ...rows]);

        has_new = true;
        $("#add_row_btn").attr("disabled", true);
    }

    // 시작일 변경 시 데이터 변경
    function changeNodeData(fieldName, e, rowIndex) {
        const node = gx.getRowNode(rowIndex);
        node.data[fieldName] = e.value;
        node.setDataValue(fieldName, e.value);
    }

    // 마진정보 변경내역 저장
    function Save() {
        let rows = gx.getRows();

        // 시작일 검사
        let wrong_sdates = rows.reduce((a,b) => {
            if(!a) return a;
            if(!a.sdate || !b.sdate) return false;
            if(a.use_yn !== "A") return b;
            if(new Date(a.sdate).getTime() - new Date(b.sdate).getTime() < (1000 * 60 * 60 * 24)) return false;
            return b;
        });
        if(wrong_sdates === false) return alert("새로 추가되는 항목의 시작일을 기존 항목의 시작일보다 이후의 일자로 입력해주세요.");

        if(!confirm("해당 매장의 마진정보 변경내역을 저장하시겠습니까?")) return;

        axios({
            url: '/store/standard/std07/update-store-fee',
            method: 'put',
            data: rows,
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.SearchDetail(store_cd, store_nm);
                Search();
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 최근 마진정보 변경내역 삭제
    function removeData() {
        let rows = gx.getRows().filter(r => r.use_yn !== 'A');
        if(rows.length < 1) return alert("삭제할 정보가 없습니다.");
        if(!confirm("가장 최근 정보가 삭제됩니다. 삭제하시겠습니까?")) return;

        let remove_idx = rows.map(r => r.idx).sort((a,b) => b - a)[0];

        axios({
            url: '/store/standard/std07/remove-store-fee/' + remove_idx,
            method: 'delete',
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.SearchDetail(store_cd, store_nm);
                Search();
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 날짜형식 변경
    function getDateObjToStr(date) {
		var str = new Array();

		var _year = date.getFullYear();
		str[str.length] = _year;

		var _month = date.getMonth() + 1;
		if (_month < 10) _month = "0" + _month;
		str[str.length] = _month;

		var _day = date.getDate();
		if (_day < 10) _day = "0" + _day;
		str[str.length] = _day
		var getDateObjToStr = str.join("-");

		return getDateObjToStr;
	}

    // 추가버튼 리셋
    function resetAddBtn() {
        has_new = false;
        $("#add_row_btn").attr("disabled", false);
    }
</script>
@stop
