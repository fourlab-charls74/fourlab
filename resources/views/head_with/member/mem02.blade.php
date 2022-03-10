@extends('head_with.layouts.layout')
@section('title','휴면회원')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">휴면회원</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 휴면회원</span>
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
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 자료받기</a>
                        <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">아이디</label>
                                <div class="flax_box">
                                    <input type="text" name="user_ids" id="user_ids" class="form-control form-control-sm mr-1 search-enter" placeholder="여러명 검색 시 콤마(,)로 구분">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="dlv_kind">회원명</label>
                                <div class="flax_box">
                                    <input type="text" name="name" id="name" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">그룹</label>
                                <div class="flax_box">
                                    <select name='user_group' class="form-control form-control-sm">
                                        <option value=''>회원그룹</option>
                                        @foreach($groups as $group)
                                            <option value="{{$group->id}}">{{$group->val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">최근로그인</label>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="item">자료수/정렬</label>
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
                                            <option value="a.user_id" selected>아이디</option>
                                            <option value="a.name" >이름</option>
                                            <option value="a.regdate" >가입일</option>
                                            <option value="a.lastdate" >최근로그인</option>
                                            <option value="e.ord_date" >최근주문일</option>
                                            <option value="e.ord_amt" >구매금액</option>
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
                <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
                <div class="btn-group dropleftbtm mr-1">
                    <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                    </button>
                </div>
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
                    <div class="fr_box form-inline">
                        <a href="#" class="btn-sm btn btn-primary active-btn">휴면해제</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>

    <script language="javascript">
        var columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
            {field:"user_id" , headerName:"아이디", pinned:'left', type:"HeadUserType", width:100  },
            {field:"user_nm" , headerName:"이름"},
            {field:"regdate" , headerName:"가입일", type:"DateTimeType"},
            {field:"lastdate" , headerName:"최근로그인",type:"DateTimeType"},
            {field:"visit_cnt" , headerName:"로그인횟수",  type: 'numberType'},
            {field:"auth_type", headerName:"auth_type", hide:true,width:100},
            {field:"auth_type_nm" , headerName:"인증방식", width:100},
            {field:"auth_yn" , headerName:"인증여부",cellClass: 'hd-grid-code'},
            {field:"mobile_chk" , headerName:"SMS수신",cellClass: 'hd-grid-code'},
            {field:"yn" , headerName:"승인",cellClass: 'hd-grid-code'},
            {field:"site" , headerName:"판매처"},
            { width: "auto" }
        ];

        const pApp = new App('', {gridId: "#div-gd"});
        const gridDiv = document.querySelector(pApp.options.gridId);
        const gx = new HDGrid(gridDiv, columns);

        pApp.ResizeGrid(275);
        // pApp.BindSearchEnter();

        pApp.BindSearchEnter();

        $(document).ready(function () {
            Search();
        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/head/member/mem02/search', data, 1);
        }

        $('.active-btn').click(function(e){

            const rows = gx.getSelectedRows();
            if (rows.length === 0) {
                alert("휴면해제 할 회원을 선택해주세요.");
                return;
            }

            if(confirm('휴면해제를 하시겠습니까?')){
                const user_ids = [];
                rows.forEach(function(data){
                    user_ids.push(data.user_id);
                });
                $.ajax({
                    async: true,
                    type: 'put',
                    url: "/head/member/mem02/active",
                    data: {'user_ids':user_ids},
                    success: function (res) {
                        if(res.code == "200"){
                            alert("휴면해제를 하였습니다.");
                            Search();
                        } else {
                            console.log(res.responseText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText)
                    }
                });
            }
        });


    </script>
@stop
