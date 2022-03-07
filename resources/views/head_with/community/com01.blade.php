@extends('head_with.layouts.layout')
@section('title','커뮤니티')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">커뮤니티</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 커뮤니티</span>
        <span>/ 커뮤니티</span>
    </div>
</div>
<form method="get" name="search">
    <input type="hidden" name="fields">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="openCommunity('add')" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="community_name">이름</label>
                            <div class="flax_box">
                                <input type="text" name="name" id="community_name" class="search-all search-enter form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="use_yn">사용여부</label>
                            <div class="flax_box">
                                <select name="use_yn" id="use_yn" class="search-all search-enter form-control form-control-sm">
                                    <option value="">모두</option>
                                    <option value="1" selected>사용</option>
                                    <option value="0">미사용</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="javascript:;" onclick="openCommunity('add')" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
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

<script>
const display_writers = ["", "이름", "아이디"];
const display_comment_writers = ["", "상점정보", "작성자"];
const rights = ["", "제한없음", "회원"];
const writes = ["", "관리자", "회원"];

var columns = [
    {
        field:"board_id",
        headerName:"아이디",
        width:120,
        cellRenderer : function(p) {
            return `<a href="#" onclick="openCommunity('edit','${p.value}')">${p.value}</a>`;
        }
    },
    {
        field:"board_nm",
        headerName:"이름",
        width:150,
        cellRenderer : function(p) {
            return `<a href="https://devel.netpx.co.kr/app/boards/lists/${p.data.board_id}"' target="_blank">${p.value}</a>`;
        }
    },
    { field:"board_type" , headerName:"타입" },
    {
        field:"display_writer" ,
        headerName:"작성자노출", 
        cellRenderer: function(params) {
            return display_writers[params.value];
        }
    },
    {
        field:"display_comment_writer", 
        headerName:"덧글작성자 노출", 
        cellRenderer: function(params) {
            console.log(params);
            return display_comment_writers[params.value];
        }
    },
    {
        headerName:"권한",
        children: [
            // {headerName: "리스트", field: "rights", cellRenderer: (p) => console.log(p) },
            {headerName: "리스트", field: "rights", cellRenderer: (p) => rights[p.value] },
            {headerName: "조회", field: "rights_view", cellRenderer: (p) => rights[p.value]},
            {headerName: "작성", field: "rights_write", cellRenderer: (p) => writes[p.value]},
            {headerName: "덧글", field: "rights_comment", cellRenderer: (p) => writes[p.value]}
        ]
    },
    {
        field:"content_cnt" ,
        headerName:"게시글",
        type: 'currencyType',
        cellRenderer: function(p) {
            return `<a href="#" onclick="openBoard('${p.data.board_id}')">${numberFormat(p.value)}</a>`;
        }
    },
    {field:"content_date" , headerName:"최근 등록일시"},
    {
        field:"comment_cnt",
        headerName:"덧글",
        type:'currencyType',
        cellRenderer: function(p) {
            return `<a href="#" onclick="openComment('${p.data.board_id}')">${numberFormat(p.value)}</a>`;
        }
    },
    {field:"comment_date" , headerName:"댓글 최근 등록일시"},
    {field:"is_use" , headerName:"사용여부", width: 120},
    {field:"regi_date" , headerName:"등록일시", width: 150},
    {field:"upd_date" , headerName:"수정일시", width: 150},
    { width: "auto" }
];

var pApp = new App('', {gridId: "#div-gd"});
var gx;

var Search = function () {
    var data = $('form[name="search"]').serialize();
    gx.Request('/head/community/com01/search', data);   
};

document.addEventListener("DOMContentLoaded", function () {
    pApp.ResizeGrid(275);
    pApp.BindSearchEnter();
    var gridDiv = document.querySelector(pApp.options.gridId);
    gx = new HDGrid(gridDiv, columns);
    Search();
});

var openCommunity = function (type, id='') {
    var url = '/head/community/com01/show/' + type + '/' + id;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1050,height=1000");
};

var openBoard = function (id='') {
    var url = '/head/community/com02/pop/' + id;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1050,height=1000");
};

var openComment = function (id='') {
    var url = '/head/community/com03/pop/' + id;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1050,height=1000");
};

</script>
@stop
