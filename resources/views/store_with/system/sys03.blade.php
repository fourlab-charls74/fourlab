@extends('store_with.layouts.layout')
@section('title','그룹관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">그룹관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 시스템</span>
        <span>/ 그룹관리</span>
    </div>
</div>


<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" data-code="" onclick="openCodePopup(this)" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">그룹명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter w-50" name='group_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">영문명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter w-50" name='group_nm_eng' value=''>
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
            field: "group_no",
            headerName: "그룹 번호",
            width: 100
        },
        {
            field: "group_nm",
            headerName: "그룹명",
            width: 150,
            cellRenderer: function(params) {
                return '<a href="#" data-code="' + params.data.group_no + '" onClick="openCodePopup(this)">' + params.value + '</a>'
            }
        },
        {
            field: "group_nm_eng",
            headerName: "영문명",
            width: 150
        },
        {
            field: "id",
            headerName: "아이디",
            width: 100
        },
        {
            field: "regi_date",
            headerName: "등록일",
            width: 150,
            cellClass: 'hd-grid-code'
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
        gx.Request('/store/system/sys03/search', data);
    }

    function openCodePopup(a) {
        const cd = $(a).attr('data-code');
        let url = '/store/system/sys03/create';
        if (cd !== '') {
            url = '/store/system/sys03/' + cd;
        }
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=1024");
    }
</script>
@stop