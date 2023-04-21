@extends('head_with.layouts.layout')
@section('title','기획전')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">기획전</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품</span>
        <span>/ 기획전</span>
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
                            <label for="user_yn">유형</label>
                            <div class="flax_box">
                                <select name="plan_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($plan_types as $plan_type)
                                        <option value='{{ $plan_type->code_id }}'>{{ @$plan_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">구분</label>
                            <div class="flax_box">
                                <select name="plan_kind" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($plan_kinds as $plan_kind)
                                        <option value='{{ $plan_kind->code_id }}'>{{ @$plan_kind->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">제목</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='title' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="state">사용여부</label>
                            <div class="flax_box">
                                <select name="is_show" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($is_shows as $is_show)
                                        <option value="{{$is_show->code_id}}" @if($is_show->code_id == '1') selected @endif>
                                            {{$is_show->code_val}}
                                        </option>
                                    @endforeach
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
        {field: "no", headerName: "코드", width: 50, cellClass: 'hd-grid-code',},
        {field: "plan_type", headerName: "유형", width:96},
        {field: "plan_kind", headerName: "구분"},
        {field: "title", headerName: "제목", width: 260,
            cellRenderer: function(params) {
                return '<a href="#" data-code="'+params.data.no +'" onClick="openCodePopup(this)">'+ params.value+'</a>'
            }
        },
        {field: "plan_date_yn", headerName: "기간", width:48},
        {field: "start_date", headerName: "시작일자", width:72},
        {field: "end_date", headerName: "종료일자", width:72},
        {field: "folder_yn", headerName: "하위폴더",cellClass: 'hd-grid-code',width:72,
            cellRenderer: function(params) {
                if(params.value == 'Y') return "존재"
                else if(params.value == 'N') return "해당없음"
                else return params.value
            }
        },
        {field: "cnt", headerName: "상품수", width:60, type: 'numberType' },
        {field: "p_cnt", headerName: "조회수", width:60, type: 'numberType' },
        {field: "preview", headerName: "미리보기", width:72,cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                return '<a href="https://bizest.' + '{{ $domain }}' + '/app/planning/views/' + params.data.p_no + '/' + params.data.no + '?is_preview=y" target="_blank">보기</a>'
            }
        },
        {field: "is_show", headerName: "사용여부", width:58, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value == 'Y') return "사용"
                else if(params.value == 'N') return "미사용"
                else return params.value
            }},
        {field: "admin_name", headerName: "등록자", },
        {field: "regi_date", headerName: "등록일시", width: 110,cellClass: 'hd-grid-code'},
        {field: "upd_date", headerName: "수정일시", width: 110,cellClass: 'hd-grid-code'},
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
        gx.Request('/head/product/prd12/search', data,1);
    }

    function openCodePopup(a) {
        const url='/head/product/prd12/' + $(a).attr('data-code');
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function openAddPopup(){
        const url='/head/product/prd12/create';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=600");
    }
</script>
@stop
