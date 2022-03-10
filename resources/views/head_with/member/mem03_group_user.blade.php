@extends('head_with.layouts.layout-nav')
@section('title','회원계급')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">회원계급</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 회원계급 - {{$group_nm}}</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="window.close();" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <form method="get" name="search">
        <input type="hidden" name="fields">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="#" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="SearchFormReset()">
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- 아이디 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">아이디</label>
                                <div class="flax_box">
                                    <input type="text" name="user_id" id="user_id" class="form-control form-control-sm mr-1">
                                </div>
                            </div>
                        </div>
                        <!-- 회원명 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="dlv_kind">회원명</label>
                                <div class="flax_box">
                                    <input type="text" name="name" id="name" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <!-- 상품상태 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">승인</label>
                                <div class="flax_box">
                                    <select name="yn" id="yn" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach($yn as $val)
                                            <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <!-- 가입일 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">가입일</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" autocomplete="off" disable>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="edate" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 성별 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_type">성별</label>
                                <div class="flax_box">
                                    <select name="sex" id="sex" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach($sex as $val)
                                            <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- 연령 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="dlv_kind">연령</label>
                                <div class="flax_box">
                                    <select name="age" id="age" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach($age as $val)
                                            <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="search-area-ext row d-none align-items-center">
                        <!-- 최근로그인 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for=""">최근로그인</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="last_sdate" autocomplete="off" disable>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="last_edate" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i> 
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 최근 주문일 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">최근주문일</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="order_sdate" autocomplete="off" disable>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="order_edate" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">출력자료수</label>
                                <div class="flax_box">
                                    <select name='limit' class="form-control form-control-sm">
                                        <option value="100" >100</option>
                                        <option value="500" selected>500</option>
                                        <option value="1000" >1000</option>
                                        <option value="2000" >2000</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="search-area-ext row d-none align-items-center">
                        <!-- 구매금액 -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">구입금액</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_amt_from' value='' onkeyup="currency(this)">
                                        </div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_amt_to' value='' onkeyup="currency(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">구매수</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_cnt_from' value='' onkeyup="currency(this)">
                                        </div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_cnt_to' value='' onkeyup="currency(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="SearchFormReset()">
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
                    <div class="fr_box">
                        <select name='user_group' id="user_group" class="form-control form-control-sm" style="width:130px;display:inline">
                            <option value=''>회원그룹</option>
                            @foreach($groups as $group)
                                <option value="{{$group->id}}">{{$group->val}}</option>
                            @endforeach
                        </select>
                        <div class="btn-group dropleftbtm mr-1">
                            <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                            </button>
                            <div class="dropdown-menu" style="">
                                <a href="#" class="dropdown-item move-btn">그룹이동</a>
                                <a href="#" class="dropdown-item add-btn">그룹등록</a>
                                <a href="#" class="dropdown-item del-btn">그룹삭제</a>
                                <a href="#" class="dropdown-item coupon-btn">쿠폰지급</a>
                                <a href="#" class="dropdown-item point-btn">적립금지급</a>
                                <a href="#" class="dropdown-item sms-btn">SMS발송</a>
                            </div>
                            <input type="hidden" name="data" id="data" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>
<script>
const group_no = '{{$group_no}}';

var columns = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28,
        pinned:'left'
    },
    {field:"user_id" , headerName:"아이디", pinned:'left', type:"HeadUserType"  },
    {field:"name" , headerName:"이름"},
    {field:"sex" , headerName:"성별"},
    {field:"jumin1" , headerName:"주민번호"},
    {field:"mobile" , headerName:"휴대전화", width:100},
    {field:"regdate", headerName:"가입일", width:100},
    {field:"lastdate" , headerName:"최근로그인"},
    {field:"visit_cnt" , headerName:"로그인횟수", type: 'currencyType'},
    {field:"ord_date" , headerName:"최근주문일"},
    {field:"ord_cnt" , headerName:"구매수", type: 'currencyType'},
    {field:"ord_amt" , headerName:"구입금액", type: 'currencyType'},
    {field:"est_cnt" , headerName:"상품평작성횟수", type: 'currencyType'},
    {field:"point" , headerName:"적립금", type: 'currencyType'},
    {field:"email_chk", headerName:"메일수신"},
    {field:"mobile_chk", headerName:"SMS수신"},
    { width: "auto" }
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columns);

pApp.ResizeGrid();

function Search() {
    let data = $('form[name="search"]').serialize();
    gx.Request(`/head/member/mem03/search/group-user/${group_no}`, data, 1);
}

Search();

//추가
function add(ids) {
    $.ajax({    
        type: "post",
        url: `/head/member/mem03/group-user/${group_no}`,
        data: { 'user_ids' : ids},
        success: function(data) {
            alert("추가되었습니다.");
            Search();
        }
    });
}

//추가
function move(ids) {
    $.ajax({    
        type: "put",
        url: `/head/member/mem03/group-user/${group_no}`,
        data: { 'user_ids' : ids, 'change_id' : $('#user_group').val() },
        success: function(data) {
            alert("이동되었습니다.");
            Search();
        }
    });
}

//삭제
function del(ids) {
    $.ajax({    
        type: "delete",
        url: `/head/member/mem03/group-user/${group_no}`,
        data: { 'user_ids' : ids },
        success: function(data) {
            alert("삭제되었습니다.");
            Search();
        }
    });
}

function createValue(data) {
    return data.user_id;
}

function usersCallback(datas) {
    add(getUserValues(datas));
}

function getUserValues(datas) {
    const values = [];

    datas.forEach(function(data){
        values.push(createValue(data));
    });

    return values.join(',');
}

$('.del-btn').click(function() {
    const datas = gx.getSelectedRows();

    if (datas.length === 0) {
        alert("삭제할 회원을 선택해주세요.");
        return;
    }

    if(confirm("선택된 회원을 해당그룹에서 제거하시겠습니까?") === false) return;

    del(getUserValues(datas));
});

$('.move-btn').click(function(){
    const datas = gx.getSelectedRows();

    if ($('#user_group').val() === group_no) {
        alert("같은 그룹으로는 이동할 수 없습니다.");
        return;
    }

    if (datas.length === 0) {
        alert("그룹이동할 회원을 선택해주세요.");
        return;
    }

    if(!$('#user_group').val()) {
        alert("변경할 그룹을 선택해주세요.");
        return;
    }

    move(getUserValues(datas));
});

$('.add-btn').click(function(){
    var url = `/head/member/mem01/pop`;
    window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=768");
});

$('.sms-btn').click(function(){
    const rows = gx.getSelectedRows();

    if (rows.length === 0) {
        alert("메시지 보낼 유저를 선택해주세요.");
        return;
    }

    openSmsSend(rows[0].mobile, rows[0].name);
});

$('.point-btn').click(function(e){
    const rows = gx.getSelectedRows();

    if (rows.length === 0) {
        alert("메시지 보낼 유저를 선택해주세요.");
        return;
    }
    const user_ids = [];

    rows.forEach(function(data){
        user_ids.push(data.user_id);
    });

    openAddPoint(user_ids.join(','));
});

$('.coupon-btn').click(function(e){
    const rows = gx.getSelectedRows();
<<<<<<< HEAD

=======
>>>>>>> main
    if (rows.length === 0) {
        alert("쿠폰을 지급할 유저를 선택해주세요.");
        return;
    }
    const user_ids = [];
<<<<<<< HEAD

    rows.forEach(function(data){
        user_ids.push(data.user_id);
    });

    openCoupon(user_ids.join(','));
});
=======
    rows.forEach(function(data){
        user_ids.push(data.user_id);
    });
    openCoupon(user_ids.join(','));
});

>>>>>>> main
</script>
@stop