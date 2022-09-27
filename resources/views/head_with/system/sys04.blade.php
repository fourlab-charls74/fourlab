@extends('head_with.layouts.layout')
@section('title','환경관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">환경관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 시스템</span>
        <span>/ 환경관리</span>
    </div>
</div>


<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="/head/system/sys05" id="group_view" class="btn btn-sm btn-primary shadow-sm pl-2">그룹별 보기</a>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" data-type="" onclick="openCodePopup(this)" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">구분</label>
                            <div class="form-inline-inner input_box">
                                <select id='type' name='type' class="form-control form-control-sm w-25">
                                    <option value=''>전체</option>
                                    @foreach ($types as $key => $value)
                                        <option value='{{ $key }}'>{{ @$value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="name">이름</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter w-50" name='name' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="name">이름(일련번호)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter w-50" name='idx' value=''>
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

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
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
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script>
    const columns = [{
            field: "type_nm",
            headerName: "구분",
            width: 100
        },
        {
            field: "name",
            headerName: "이름",
            width: 150,
            cellRenderer: function(params) {
                return '<a href="#" data-type="' + params.data.type + '" data-name="' + params.data.name + '" data-idx="' + params.data.idx + '" onClick="openCodePopup(this)">' + params.value + '</a>'
            }
        },
        {
            field: "idx",
            headerName: "이름(일련번호)",
            width: 150
        },
        {
            field: "value",
            headerName: "값",
            width: 150
        },
        {
            field: "mvalue",
            headerName: "모바일값",
            width: 150,
        },
        {
            field: "content",
            headerName: "내용",
            width: 150,
        },
        {
            field: "desc",
            headerName: "세부설명",
            width: 150,
        },
        {
            field: "rt",
            headerName: "최초등록일",
            width: 150,
        },
        {
            field: "ut",
            headerName: "수정일",
            width: 150,
        },
        {
            width: 'auto'
        }

    ];
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
        gx.Request('/head/system/sys04/search', data);
    }

    function openCodePopup(a) {

        const type = $(a).attr('data-type');
        const name = $(a).attr('data-name');
        const idx = $(a).attr('data-idx');

        let url = '/head/system/sys04/create';
        if (type !== '') {
            url = '/head/system/sys04/' + type + '/' + name + '/' + (idx || '-');
        }
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=1024");
    }
</script>
@stop
