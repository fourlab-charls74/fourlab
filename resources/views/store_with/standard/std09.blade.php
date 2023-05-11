@extends('store_with.layouts.layout')
@section('title','판매채널관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">판매채널관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 코드관리</span>
        <span>/ 판매채널관리</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                   <!-- <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">구분</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='code_kind_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">코드명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='code_kind_nm' value=''>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_yn" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="use_yn">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="y">
                                    <label class="custom-control-label" for="use_y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="n">
                                    <label class="custom-control-label" for="use_n">N</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

<div class="row show_layout">
    <div class="col-lg-4 pr-1">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 pt-1 pb-1">
                <h5 class="m-0">판매채널</h5>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-0">매장구분</h5>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-type" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
        {field: "store_type", headerName: "구분"},
        {field: "store_channel_cd", headerName: "판매채널코드", width: 110},
        {field: "store_channel", headerName: "판매채널", width: 100},
        {field: "use_yn", headerName: "사용여부"},
        {width: 'auto'}
        
    ]

    const store_type_columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
        {field: "store_type", headerName: "구분"},
        {field: "store_channel_cd", headerName: "판매채널코드", width: 110},
        {field: "store_channel", headerName: "판매채널", width: 100},
        {field: "store_kind_cd", headerName: "매장구분코드", width: 100},
        {field: "store_kind", headerName: "매장구분", width: 100},
        {field: "seq", headerName: "순서"},
        {field: "use_yn", headerName: "사용여부"},
        {width: 'auto'}
    ]

</script>
<script type="text/javascript" charset="utf-8">
   const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/standard/std09/search', data);
    }


    // function openCodePopup(a) {
    //     const url = '/store/standard/std09/' + $(a).attr('data-code');
    //     const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    // }

    function openAddPopup() {
        const url = '/store/standard/std09/show';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=420");
    }
</script>
@stop