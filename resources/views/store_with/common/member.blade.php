@extends('head_with.layouts.layout-nav')
@section('title','고객명 조회')
@section('content')
<form name="search">
    <div class="container-fluid py-3">

        <div class="page_tit d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">고객 조회</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 고객 조회</span>
                </div>
            </div>
            <div>
                <a href="#" id="search_sbtn" onclick="Search()" class="btn btn-sm btn-primary mr-1 shadow-sm">검색</a>
                <a href="#" onclick="window.close();" class="btn btn-sm btn-primary shadow-sm">닫기</a>
            </div>
        </div>
        
        <div id="filter-area" class="card shadow mb-0 ty2">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="flex_box mt-3">
                                <input type='text' placeholder="고객명 검색" class="form-control form-control-sm search-enter" name='name' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>

    </div>
</form>

<script type="text/javascript" charset="utf-8">

    const DEFAULT_STYLE = {'text-align': 'center'};

    const columns = [
        {
            field: "", 
            headerName: "선택",
            cellRenderer: function(params) {
                return "<a href='#' onclick='selectMember("+JSON.stringify(params.data)+")'>선택</a>";
            },
            cellStyle: DEFAULT_STYLE
        },
        {field: "name", headerName: "고객명", width: 70, cellRenderer: (params) => `${params.value}`, cellStyle: DEFAULT_STYLE},
        {field: "phone", headerName: "핸드폰", width: 100, cellStyle: DEFAULT_STYLE},
        {field: "addr", headerName: "주소1", width: "auto"},
        {field: "addr2", headerName: "주소2", width: 110}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(225);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/api/members/search', data, 1, searchCallback);
    }

    const searchCallback = () => {};

    function selectMember(row) {
        if (confirm("고객명을 추가하시겠습니까?") === false) return;
        if (opener.goodsCallback) opener.goodsCallback(row);
        window.close();
    };

</script>
@stop