@extends('head_with.layouts.layout')
@section('title','배너')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">배너</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 배너</span>
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
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">페이지</label>
                            <div class="flax_box">
                                <select name="page_type" id="page_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($pages as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 회원명 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">영역코드</label>
                            <div class="flax_box">
                                <input type="text" name="arcd" id="arcd" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- 상품상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">영역</label>
                            <div class="flax_box">
                                <input type="text" name="area" id="area" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">코드</label>
                            <div class="flax_box">
                                <input type="text" name="code" id="code" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- 회원명 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">내용</label>
                            <div class="flax_box">
                                <input type="text" name="subject" id="subject" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- 상품상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">구분</label>
                            <div class="flax_box">
                                <select name="type" id="type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($types as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">사용여부</label>
                            <div class="flax_box">
                                <select name="use_yn" id="use_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($use_yn as $val)
                                        <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 회원명 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">출력자료수/정렬순서</label>
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
                                        <option value="a.arcd" selected>영역코드</option>
                                        <option value="a.code">배너코드</option>
                                        <option value="a.subject" >배너명</option>
                                        <option value="a.rt" >등록일</option>
                                        <option value="a.ut" >수정일</option>
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
                <!-- end row -->
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<div id="filter-area" class="card shadow-none mb-4 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <a href="#" class="btn-sm btn btn-primary mr-1">캐시초기화</a>
                    <a href="#" class="btn-sm btn btn-primary mr-1 reset-btn">클릭 및 주문 초기화</a>
                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script>
const pageType = {
    main : "메인",
    submain : "서브메인",
    category : "카테고리",
    etc : "기타"
};

var columns = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28,
        pinned:'left'
    },
    {field:"code" , headerName:"코드", width:170},
    {
        field:"page", 
        headerName:"페이지", 
        cellRenderer : function(p) {
            if (p.value)
                return pageType[p.value];
        }
    },
    {field:"arcd" , headerName:"영역코드", width:150},
    {field:"area" , headerName:"영역", width:180},
    {
        field:"subject",
        headerName:"내용",
        width:220,
        cellRenderer : function(p) {
            if (p.value) {
                return `<a href="#" onclick="openDetail('edit', '${p.data.code}')" >${p.value}</a>`
            }
        }
    },
    {field:"type", headerName:"구분"},
    {field:"seq" , headerName:"순서"},
    {field:"click" , headerName:"클릭", type: 'currencyType'},
    {field:"order" , headerName:"주문", type: 'currencyType'},
    {field:"use_yn" , headerName:"사용"},
    {field:"rt" , headerName:"등록일시", width:140},
    {field:"ut" , headerName:"수정일시", width:140}
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columns);

pApp.ResizeGrid();

const Search = () => {
    let data = $('form[name="search"]').serialize();
    gx.Request(`/head/promotion/prm05/search`, data, 1);
}

const openDetail = (type, code='') => {
    const url=`/head/promotion/prm05/show/${type}/${code}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
}

$('.add-btn').click(() => {
    openDetail('add')
});

$('.reset-btn').click(() => {
    const rows = gx.getSelectedRows();

    if (rows.length === 0) {
        alert("초기화할 배너를 선택해주세요.");
        return;
    }

    if(confirm("선택하신 배너를 초기화 하시겠습니까?") === false) return;

    const codes = rows.map((value) => value.code);

    $.ajax({    
        type: "put",
        url: `/head/promotion/prm05/banner-reset`,
        data: {codes},
        success: function(data) {
            alert("초기화 되었습니다.");
            Search();
        },
        error: function(res){
            alert(res.responseJSON.message);
        }
    });
});
Search();
</script>
@stop
