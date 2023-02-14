@extends('store_with.layouts.layout-nav')
@section('content')
<div class="show_layout py-4 px-sm-4">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $store_nm }} - 점포 수수료</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 코드관리</span>
                <span>/ 매장관리</span>
            </div>
              
        </div>
        <div style="float:right;">
            <a href="#" onclick="self.close()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>   
    </div>
    <div>
        <form name="search" id="search">
            <input type="hidden" value="{{$store_cd}}" id="store_cd" name="store_cd" >
        </form>
    </div>
    <div class="table-responsive">
        <div id="div-gd-store-fee" class="ag-theme-balham"></div>
    </div>
</div>

<script>
    let fee_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "use_yn", headerName: "사용", pinned: "left", cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return params.value || 'N';
            }
        },
        {field: "pr_code_cd", headerName: "코드", width: 60, cellStyle: {"text-align": "center"}},
        {field: "pr_code_nm", headerName: "수수료명", width: 60, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='showDetailPopup("${params.data.store_cd}", "${params.data.pr_code_cd}")'>${params.value}</a>`;
            }
        },
        {field: "sdate", headerName: "시작일", width: 90, cellStyle: {"text-align": "center"}},
        {field: "edate", headerName: "종료일", width: 90, cellStyle: {"text-align": "center"}},
        {field: "store_fee", headerName: "매장수수료(%)", width: 120, type: "percentType"},
        {field: "grade_cd", hide: true},
        {field: "grade_nm", headerName: "매장등급", width: 80, cellStyle: {"text-align": "center"},
            cellRenderer: (params) => {
                return `<a href='javascript:void(0)' onclick='showStoreGradePopup("${params.value || params.data.grade_cd}")'>${params.value || params.data.grade_cd || ''}</a>`;
            }
        },
        // {field: "manager_fee", headerName: "중간관리수수료(%)", width: 120, type: "percentType"},
        {field: "comment", headerName: "메모", width: 300},
        {width: "auto"},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd-store-fee" });

    $(document).ready(function() {
        pApp.ResizeGrid(180);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, fee_columns);

        Search();
    });

    function Search() {

        let data = $('form[name="search"]').serialize();

        gx.Request("/store/standard/std02/charge_search", data,1);
    }

     // 마진정보 세부변경내역 팝업창 열기
     function showDetailPopup(store_cd, pr_code_cd) {
        const url = "/store/standard/std07/show/" + store_cd + "/" + pr_code_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=900,height=535");
    }
    
    // 매장등급조회
    function showStoreGradePopup(grade_nm = '') {
        const url = "/store/standard/std08/choice?grade_nm=" + grade_nm;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1200,height=710");
    }


</script>
	
@stop