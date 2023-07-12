@extends('head_with.layouts.layout')
@section('title','메뉴관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">메뉴관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 시스템</span>
        <span>/ 메뉴관리</span>
    </div>
</div>

<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" data-code="" onclick="Add(this)" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상위메뉴</label>
                            <div class="flax_box">
                                <select id='menu_no' name='menu_no' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach($pmenus as $pmenu)
                                        <option value="{{$pmenu["menu_no"]}}">{{$pmenu["kor_nm"]}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상태</label>
                            <div class="flax_box">
                                <select id='state' name='state' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value='+0' selected>미사용제외</option>
                                    <option value="0">사용중</option>
                                    <option value="2">개발중</option>
                                    <option value="4">테스트중</option>
                                    <option value="-1">미사용</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">삭제여부</label>
                            <div class="flax_box">
                                <select id='is_del' name='is_del' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value="0" selected>아니요</option>
                                    <option value="1">예</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
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
        {field: "pid", headerName: "프로그램 ID", width: 100, cellClass: 'hd-grid-code',
            cellRenderer:function(params) {
                let pid = params.data.pid;

                if (pid == null) {
                    return pid
                } else {
                    return pid.toLowerCase();
                }
            },
        
        },
        {field: "state", headerName: "상태", cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value == '-1') return "미사용"
                else if(params.value == '0') return "사용중"
                else if(params.value == '2') return "개발중"
                else if(params.value == '4') return "테스트중"
                else if(params.value == '6') return ""
                else return params.value
            }
        },
        {
            field: "kor_nm",
            headerName: "메뉴명",
            width: 200,
            cellRenderer: function(params) {
                let margin = 0;
                if(params.data.lev === 2) {
                    margin = 20;
                } else if(params.data.lev === 3){
                    margin = 40;
                }
                return '<a href="#" data-code="' + params.data.menu_no + '" onClick="Edit(this)" style="margin-left:' + margin + 'px">' + params.value + '</a>'
            }
        },
        {field: "eng_nm", headerName: "영문명", width: 150},
        {field: "kind", headerName: "종류", width: 100, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value === "M") {
                    return '<a href="#" data-code="' + params.data.menu_no + '" onClick="Add(this)">' + params.value + '</a>'
                } else {
                    return params.value;
                }
            }
        },
        {field: "action", headerName: "동작", width: 200},
        {field: "sys_menu", headerName: "유저", width: 100, cellClass: 'hd-grid-code'},
        {field: "regi_date", headerName: "등록일시", type:'DateTimeType'},
        {field: "ut", headerName: "수정일시", type:'DateTimeType'},
        {field: "is_del", headerName: "삭제여부", width: 100, cellClass: 'hd-grid-code',
            cellRenderer: (params) => params.value < 1 ? 'N' : 'Y',
        },
        {width: 0}
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
        gx.Request('/head/system/sys02/search', data);
    }

    function Add(a) {
        const cd = $(a).attr('data-code');
        let url = '/head/system/sys02/create';
        if (cd !== '') {
            url = '/head/system/sys02/create?entry=' + cd;
        }
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=1000");
    }

    function Edit(a) {
        let url = '/head/system/sys02/' + $(a).attr('data-code');
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=1000");
    }
</script>
@stop
