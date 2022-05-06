@extends('head_with.layouts.layout-nav')
@section('title','단어선택')
@section('content')

<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">단어 선택</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 단어 선택</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="selectMultiWords()" class="btn btn-sm btn-primary shadow-sm">확인</a>
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
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', checkboxSelection: true, width: 38, sort: null},
        {
            field: "code_id",
            headerName: "형식",
            width: 140,
            cellRenderer: (params) => `{${params.value}}`
        },
        {
            field: "code_val",
            headerName: "이름",
            width: "auto",
        },
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
        gx.Request('/head/api/head_tail/search', data, 1, searchCallback);
    }

    const searchCallback = () => {};

    const getParameterByName = (name, url = window.location.href) => {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    /**
     * 팝업창에 전달받은 type 파라미터의 case에 따라 원하는 콜백을 구현합니다.
     */
    const selectMultiWords = () => {
        const type = getParameterByName('type');
        if (opener.multiWordsCallback) opener.multiWordsCallback(gx.getSelectedRows(), type);
        window.close();
    };

</script>
@stop