@extends('head_with.layouts.layout-nav')
@section('title','할인율제외상품')
@section('content')

<div class="container-fluid py-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">할인율제외상품</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 할인율제외상품 - {{$group_nm}}</span>
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
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- 상품구분 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputState">상품구분</label>
                                <div class="flax_box">
                                    <select name='goods_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_types as $goods_type)
                                            <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- 상품상태 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputState">상품상태</label>
                                <div class="flax_box">
                                    <select name='goods_stat' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_states as $val)
                                            <option value='{{ $val->code_id }}'>{{ $val->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 상품상태 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all ac-style-no search-enter" name="style_no" value="">
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all search-enter" name="goods_no" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <!-- 업체 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">업체</label>
                                
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <input type="hidden" id="com_cd" name="com_cd">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 품목 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_type">품목</label>
                                <div class="flax_box">
                                    <select name="opt_kind_cd" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="dlv_kind">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">대표카테고리</label>
                                <div class="flax_box inline_btn_box">
                                    <input type="hidden" name="cat_cd" id="cat_cd">
                                    <input type="text" name="cat_nm" id="cat_nm" class="form-control form-control-sm">
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-category"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="dlv_kind">상품명</label>
                                <div class="flax_box">
                                    <input type="text" name="goods_nm" id="goods_nm" class="form-control form-control-sm ac-goods-nm">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">자료/정렬순서</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100" >100</option>
                                            <option value="500" >500</option>
                                            <option value="1000" >1000</option>
                                            <option value="2000" >2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="goods_no" selected>상품번호</option>
                                            <option value="goods_nm">상품명</option>
                                            <option value="price">판매가</option>
                                            <option value="com_nm">공급업체</option>
                                            <option value="md_nm">담당MD별</option>
                                            <option value="upd_dm">수정일</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                        <input type="radio" name="ord" id="sort_asc" value="asc">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
                    <div class="fr_box flax_box">
                        <a href="#" class="btn-sm btn btn-primary mr-1 add-goods-btn">상품추가</a>
                        <a href="#" class="btn-sm btn btn-primary mr-1 select-del-btn">선택상품삭제</a>
                        <a href="#" class="btn-sm btn btn-primary mr-1 all-del-btn">전체상품삭제</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
				<div id="div-gd" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>
<script>
const group_no = '{{$group_no}}';
let goods = null;

var columns = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28,
        pinned:'left'
    },
    {field:"goods_no", headerName:"상품번호", width:80, type:"HeadGoodsNameType", pinned:'left'},
    {field:"goods_nm" , headerName:"상품명", type:"HeadGoodsNameType", pinned:'left'},
    {field:"goods_type_val" , headerName:"상품구분"},
    {field:"com_nm" , headerName:"업체"},
    {field:"opt_kind_nm" , headerName:"품목"},
    {field:"brand_nm", headerName:"브랜드", width:100},
    {field:"full_nm" , headerName:"대표카테고리", width:100},
    {field:"style_no" , headerName:"스타일넘버"},
    {field:"sale_stat_cl_val" , headerName:"상품상태"},
    {field:"price" , headerName:"판매가", type: 'currencyType'},
    {field:"wqty" , headerName:"보유재고수", type: 'currencyType'},
    {field:"admin_id" , headerName:"관리자아이디"},
    {field:"brand_nm" , headerName:"관리자명"},
    {field:"rt" , headerName:"등록일시"},
    { width: "auto" }
];

const pApp = new App('', {gridId: "#div-gd", height: 200});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columns);

pApp.ResizeGrid(200);

function Search() {
    let data = $('form[name="search"]').serialize();
    gx.Request(`/head/member/mem03/search/ext-goods/${group_no}`, data, 1, function(res){
        goods = res.body
    });
}

Search();

//추가
function add(goods) {
    $.ajax({    
        type: "post",
        url: `/head/member/mem03/ext-goods/${group_no}`,
        data: {goods},
        success: function(data) {
            alert("추가되었습니다.");
            opener?.location.reload();
            Search();
        }
    });
}

//삭제
function del(goods) {
    $.ajax({    
        type: "delete",
        url: `/head/member/mem03/ext-goods/${group_no}`,
        data: {goods},
        success: function(data) {
            alert("삭제되었습니다.");
            Search();
        }
    });
}

function createValue(data) {
    return `${data.goods_no}|${data.goods_sub}`;
}

function goodsCallback(data) {
    add(createValue(data));
}


function multiGoodsCallback(datas) {
    const addValues = [];

    datas.forEach(function(data){
        addValues.push(createValue(data));
    });

    add(addValues.join(','));
}

function getDelValues(datas) {
    const delValues = [];

    datas.forEach(function(data){
        delValues.push(createValue(data));
    });

    return delValues.join(',');
}

$('.select-del-btn').click(function() {
    const datas = gx.getSelectedRows();

    if (datas.length === 0) {
        alert("삭제할 상품을 선택해주세요.");
        return;
    }

    if(confirm("선택된 상품을 할인율제외상품에서 제거하시겠습니까?") === false) return;

    del(getDelValues(datas));
});

$('.all-del-btn').click(function(){
    if (goods.length === 0) {
        alert("삭제할 상품이 없습니다.");
        return;
    }

    if(confirm("모든상품을 할인율제외상품에서 제거하시겠습니까?") === false) return;

    del(getDelValues(goods));
});

$('.add-goods-btn').click(function(){
    var url = `/head/api/goods/show/`;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=768");
});

</script>
@stop
