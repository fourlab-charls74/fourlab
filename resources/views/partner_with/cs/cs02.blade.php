@extends('partner_with.layouts.layout')
@section('title','상품 Q&A')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
    <div class="page_tit">
        <h3 class="d-inline-flex">상품 Q&A</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품 Q&A</span>
        </div>
    </div>
    <form method="get" name="search" id="search">
        @csrf
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">작성일</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
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
                                <label for="style_no">스타일넘버</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all ac-style-no search-enter"  name='style_no' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-email-input">출력여부</label>
                                <div class="flax_box">
                                    <select name="show_yn" id="show_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputCity">진행상태</label>
                                <div class="flax_box">
                                    <select name="answer_yn" id="answer_yn" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="N" selected>답변대기</option>
                                        <option value="Y">답변완료</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all ac-goods-nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputZip">검색</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner select-box">
                                    <select name="column" id="column" class="form-control form-control-sm">
                                        <option value="a.user_nm">작성자 이름</option>
                                        <option value="a.user_id">작성자 ID</option>
                                        <option value="a.admin_nm">답변자 이름</option>
                                        <option value="a.admin_id">답변자 ID</option>
                                    </select>
                                    </div>
                                    <div class="form-inline-inner input-box">
                                        <input type='text' name='keyword' class="form-control form-control-sm search-all"  value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="search-area-ext d-none row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputCity">품목</label>
                                <div class="flax_box">
                                    <select name='item' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($items as $item)
                                            <option value='{{ $item->cd }}'>{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="width:100%;min-height:300px;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>

<script type="text/javascript" charset="utf-8">

    const BLUE = '#556ee6';

    const CELL_COLOR = {
        YELLOW: { 'background' : '#ffff99' }
    };

    var columns = [
        {headerName: "#", field: "num",width:50, valueGetter: function(params) {return params.node.rowIndex+1;}},
        {headerName: "출력", field: "show_yn",
            cellRenderer: (params) => {
                return `<a href='#' onclick='changeShowYN(` + JSON.stringify(params.data) + `)' style="color: ${BLUE}">${params.value}</a>`;
            }
        },
        {headerName: "상태", field: "answer_yn", cellRenderer: function(params) {return params.value === 'Y' ? '답변완료' : '답변대기'}},
        {headerName: "제목", field: "subject",width:250, cellRenderer: function(params) {return '<a href="/partner/cs/cs02/show/' + params.data.no +'" rel="noopener">'+ params.value+'</a>';}},
        {headerName: "이미지", field:"img", type:'GoodsImageType'},
        {headerName: "상품명", field: "goods_nm", type: "GoodsNameType"},
        {headerName: "작성자", field: "user_id",width:130, cellRenderer: function(params) {if (params.value !== undefined) {return `${params.data.user_nm}(${params.value})`;}}},
        {headerName: "답변자", field: "admin_id",width:170, cellRenderer: function(params) {if (params.value !== undefined && params.value !== null) {return `${params.data.admin_nm}(${params.value})`}}},
        {headerName: "질문일시", field: "q_date",type:'DateTimeType'},
        {headerName: "답변일시", field: "a_date",type:'DateTimeType'},
        {headerName: "", field: "", width: "auto"}
    ];

    const pApp = new App('',{
        gridId:"#div-gd",
    });

    let gx;
    $(document).ready(function() {
        
        pApp.ResizeGrid(300);
        pApp.BindSearchEnter();

        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.data.answer_yn == "Y") return CELL_COLOR.YELLOW;
            }
        }
        gx = new HDGrid(gridDiv, columns, options);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/cs/cs02/search', data,1);
    }
    
    const changeShowYN = async (row) => {

        const CMD = "change";
        const URL = "/partner/cs/cs02";

        const csrf_token = document.search._token.value;
        const { no, show_yn } = row;

        try {

            const response = await axios({ url: URL + `/${no}`, method: 'put', 
                data: { 
                    cmd: CMD, show_yn: show_yn, _token: csrf_token
                }
            });

            if (response.data == 1) {
                Search();
            }

        } catch (error) {

            // console.log(error);
            alert("장애가 발생했습니다. 관리자에게 문의해 주십시오.");

        }

    };


</script>


@stop
