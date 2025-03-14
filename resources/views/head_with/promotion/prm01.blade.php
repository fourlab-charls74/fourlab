@extends('head_with.layouts.layout')
@section('title','공지사항')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">공지사항</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 공지사항</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
                            <label for="">공개여부</label>
                            <div class="flax_box">
                                <select name='use_yn' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value='Y'>예</option>
                                    <option value='N'>아니요</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">제목</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='subject' value=''>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="">내용</label>
                        <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='content' value=''>
                        </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">공지</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="main_yn" class="custom-control-input" name="main_yn" value="Y">
                                    <label class="custom-control-label" for="main_yn">메인 공지</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="notice_yn" class="custom-control-input" name="notice_yn" value="Y">
                                    <label class="custom-control-label" for="notice_yn">게시판 공지</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="popup_yn" class="custom-control-input" name="popup_yn" value="Y">
                                    <label class="custom-control-label" for="popup_yn">팝업 공지</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/head/promotion/prm01/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
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
<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",type:'NumType', cellClass: 'hd-grid-code'},
        {headerName: "메인공지", field: "main_yn", cellClass: 'hd-grid-code', width: 70, 
            cellRenderer: function(params) {
                if(params.value == 'Y') return "예"
                else if(params.value == 'N') return "아니오"
                else return params.value
            }
        },
        {headerName: "게시판공지", field: "notice_yn", cellClass: 'hd-grid-code', width: 70, 
            cellRenderer: function(params) {
                if(params.value == 'Y') return "예"
                else if(params.value == 'N') return "아니오"
                else return params.value
            }
        },
        {headerName: "제목", field: "subject",width:400,
            cellRenderer: function (params) {
                        if (params.value !== undefined) {
                            return '<a href="#" onclick="return AddProducts(\'' + params.data.idx + '\');">' + params.value + '</a>';

                        }
                    }},
        {headerName: "조회수", field: "cnt", type:'numberType', cellClass: 'hd-grid-code', cellStyle:{"text-align" : "right"}},
        {headerName: "작성자", field: "name", width:100},
        {headerName: "공개여부", field: "use_yn", cellClass: 'hd-grid-code', width: 70, 
            cellRenderer: function(params) {
                if(params.value == 'Y') return "예"
                else if(params.value == 'N') return "아니오"
                else return params.value
            }
        },
        {headerName: "등록일시", field: "regi_date", width:130},
        {headerName: "수정일시", field: "ut", width:130},
        {headerName: "글번호", field: "idx", hide:true },
        { width: "auto" }
    ];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    pApp.BindSearchEnter();

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm01/search', data);
    }

    function AddProduct() {
        var url = '/head/promotion/prm01/create';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    function AddProducts(idx){
    var url = '/head/promotion/prm01/' + idx;
    var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
}

</script>
@stop
