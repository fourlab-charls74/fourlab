@extends('head_with.layouts.layout-nav')
@section('title','색상선택')
@section('content')

<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">색상 선택</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 색상 선택</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="window.close();" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    
    <div id="filter-area" class="card shadow-none mb-0 ty2">
        <div class="card-body">
            <div class="card-title mb-3">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                        <a href="#" id="search_sbtn" onclick="selectMultiWords()" class="btn btn-sm btn-primary shadow-sm">선택 확인</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', checkboxSelection: true, width: 80, sort: null},
        {field: "color_nm", headerName: "색상명", width: 140, cellRenderer: (params) => `${params.value}`},
        {field: "color", headerName: "색상코드", width: 100},
        {field: "use_yn", headerName: "사용여부", width: 90},
        {field: "code_seq", headerName: "정렬순서", width: 90},
        {field: "img1_url", headerName: "기본이미지 경로", width: 350},
        {field: "img2_url", headerName: "추가이미지 경로", width: 350}
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
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/api/colors/search', data, 1, searchCallback);
    }

    const searchCallback = () => {};

    /**
     * 부모창에서 사용할 팝업창 콜백 설정
     */
    const selectMultiWords = () => {
        if (opener.multiWordsCallback) opener.multiWordsCallback(gx.getSelectedRows());
        window.close();
    };

</script>
@stop