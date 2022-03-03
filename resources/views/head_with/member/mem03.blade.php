@extends('head_with.layouts.layout')
@section('title','회원그룹')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">회원그룹</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원&amp;CRM</span>
        <span>/ 회원그룹</span>
    </div>
</div>
<form method="get" name="search">
    <input type="hidden" name="fields">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" class="btn btn-sm btn-outline-primary mr-1 add-btn">등급추가</a>
                    <a href="#" class="btn btn-sm btn-outline-primary grade-btn mr-1">회원등급변경</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="">그룹명</label>
                            <div class="flax_box">
                                <input type="text" name="group_nm" id="group_nm" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">구분</label>
                            <div class="flax_box">
                                <select name="group_type" id="group_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($types as $val)
                                    <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" class="btn btn-sm btn-outline-primary mr-1 add-btn">등급추가</a>
            <a href="#" class="btn btn-sm btn-outline-primary grade-btn mr-1">회원등급변경</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body">
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
<script>

var columns = [
    {
        field:"group_nm" , 
        headerName:"그룹명", 
        width:200, 
        cellRenderer: function (params) {
            if (params.value) {
                return `<a href='#' onclick="openDetail('edit', '${params.data.group_no}')">${params.value}</a>`
            }
        } 
    },
    {
        field:"user_cnt", 
        headerName:"회원수",
        type: 'currencyType',
        cellRenderer: function(params) {
                return `<a href='#' onclick="openGroupUser('${params.data.group_no}')">${params.value}</a>`
        }
    },
    {field:"user_group_type" , headerName:"구분"},
    {field:"dc_ratio" , headerName:"할인율(%)", width:80},
    {
        field:"dc_ext_goods" , 
        headerName:"할인율제외상품",
        cellRenderer: function(params) {
                return `<a href='#' onclick="openExtGoods('${params.data.group_no}')">${params.value}</a>`
        }, 
        type: 'currencyType'
    },
    {field:"point_ratio", headerName:"추가적립율(%)", width:100},
    {field:"is_wholesale" , headerName:"도매여부", width:100},
    {field:"is_point_use" , headerName:"적립금사용"},
    {field:"is_point_save" , headerName:"적립금지급"},
    {field:"is_coupon_use" , headerName:"쿠폰사용"},
    {field:"rt" , headerName:"등록일시", width:135},
    {field:"ut" , headerName:"수정일시", width:135}
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columns);

pApp.ResizeGrid(275);

function Search() {
    let data = $('form[name="search"]').serialize();
    gx.Request('/head/member/mem03/search', data, 1);
}

const openDetail = (type, no='') => {
    var url = `/head/member/mem03/show/${type}/${no}`;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
}

const openExtGoods = (group_no) => {
    const url=`/head/member/mem03/ext-goods/${group_no}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}

const openGroupUser = (group_no) => {
    const url=`/head/member/mem03/group-user/${group_no}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}

const openGrade = () => {
    const url=`/head/member/mem03/grade`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}

Search();

$('.add-btn').click(function(){
    openDetail('add');
});

$('.grade-btn').click(function(){
    openGrade();
});
</script>
@stop