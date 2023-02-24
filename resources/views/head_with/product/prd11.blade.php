@extends('head_with.layouts.layout')
@section('title','섹션')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">섹션</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품</span>
        <span>/ 세션</span>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">구분</label>
                            <div class="flax_box">
                                <select name="sec_code" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($section_types as $section_type)
                                        <option value='{{ $section_type->code_id }}'>{{ @$section_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">섹션명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='name' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상품명</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm ac-goods-nm search-enter" name="goods_nm" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">사용여부</label>
                            <div class="flex_box">
                                <select name="use_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">사용</option>
                                    <option value="N">미사용</option>
                                </select>
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
    const columns = [
        {field: "type", headerName: "구분"},
        {field: "sec_no", headerName: "코드", width: 50, cellClass: 'hd-grid-code',},
        {field: "name", headerName: "섹션명", width: 220,
            cellRenderer: function(params) {
                return '<a href="#" data-code="'+params.data.sec_no +'" onClick="openCodePopup(this)">'+ params.value+'</a>'
            }
        },
        {field: "max_limit", headerName: "최대출력수", width:84, type:'numberType'},
        {field: "soldout_ex_yn", headerName: "품절상품제외", width:96, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value == 'Y') return "제외"
                else if(params.value == 'N') return "해당없음"
                else return params.value
            }
        },
        {field: "sort", headerName: "정렬", cellStyle:{'text-align':'center'}},
        {headerName:"상품수",
            children : [
                {headerName : "판매중", field : "40_cnt",type:'numberType'},
                {
                    headerName : "품절", 
                    field : "30_cnt",
                    type:'numberType', 
                    cellStyle: function(params) {
                        return {"background": parseInt(params.value) > 0 ? "#C5FF9D" : 'none'};
                    }
                },
                {headerName : "전체", field : "cnt",type:'numberType'}
            ]
        },
        {field: "use_yn", headerName: "사용여부", width: 58,cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value == 'Y') return "사용"
                else if(params.value == 'N') return "미사용"
                else return params.value
            }
        },
        {field: "rt", headerName: "등록일시", width: 110,cellClass: 'hd-grid-code'},
        {field:"", headerName:"", width:"auto"}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
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
        gx.Request('/head/product/prd11/search', data);
    }

    function openCodePopup(a) {
        const url='/head/product/prd11/' + $(a).attr('data-code');
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function openAddPopup(){
        const url='/head/product/prd11/create';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=500");
    }
</script>
@stop
