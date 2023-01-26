@extends('head_with.layouts.layout')
@section('title','클래식 숙소예약관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">(개)클래식 숙소예약관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 클래식</span>
        <span>/ 숙소예약관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0 search_mode_wrap">
                    <button type="button" class="btn btn-sm btn-outline-primary pr-1 waves-light waves-effect dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i id="" class="search-btn-label fa fa-square fs-12"></i>
                        <i class="bx bx-chevron-down fs-12"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width:0">
                        <a id="search-btn-minus" class="dropdown-item" data-search-type="minus" href="#"><i class="fa fa-minus-square"></i></a>
                        <a id="search-btn" class="dropdown-item" href="#" data-search-type="default"><i class="fa fa-square"></i></a>
                        <a id="search-btn-plus" class="dropdown-item" href="#" data-search-type="plus"><i class="fa fa-plus-square"></i></a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">등록번호 :</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-all search-enter" name="regist_number" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">모바일 :</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-all search-enter" name="mobile" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">이메일 :</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-all search-enter" name="email" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">이름 :</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-all search-enter" name="name1" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">접수상태 :</label>
                            <div class="flax_box">
                                <select name="state" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="10">접수대기</option>
                                    <option value="20">접수중</option>
                                    <option value="30">접수완료</option>
                                    <option value="40">확정완료</option>
                                    <option value="41">현장결제</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">전/후 예약일 :</label>
                            <div class="flax_box">
                                <select name="s_dm_date" class="form-control form-control-sm" style="width:49%;">
                                    <option value="">전체</option>
                                    <option value="1017">10월 17일(10월 18일 출발그룹)</option>
                                    <option value="1018">10월 18일(10월 19일 출발그룹)</option>
                                    <option value="1019">10월 19일(10월 20일 출발그룹)</option>
                                </select>
                                <select name="e_dm_date" class="form-control form-control-sm" style="margin-left:2%;width:49%;">
                                    <option value="">전체</option>
                                        <option value="1020">10월 20일(10월 18일 출발그룹)</option>
                                        <option value="1021">10월 21일(10월 19일 출발그룹)</option>
                                        <option value="1022">10월 22일(10월 20일 출발그룹)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0">
                <button type="button" class="btn btn-sm btn-outline-primary pr-1 waves-light waves-effect dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i id="" class="search-btn-label fa fa-square fs-12"></i> <i class="bx bx-chevron-down fs-12"></i>
                </button>
                <div class="dropdown-menu" style="min-width:0">
                    <a id="search-btn-minus" class="dropdown-item" data-search-type="minus" href="#"><i class="fa fa-minus-square"></i></a>
                    <a id="search-btn" class="dropdown-item" href="#" data-search-type="default"><i class="fa fa-square"></i></a>
                    <a id="search-btn-plus" class="dropdown-item" href="#" data-search-type="plus"><i class="fa fa-plus-square"></i></a>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">134</span>건</h6>
				</div>
                <div class="fr_box flax_box" style="color:#FF0000;font-weight:bold;">
					<div class="mr-1">
						<select id="s_state" name="s_state" class="form-control form-control-sm">
							<option value="">전체</option>
                            <option value="10">접수대기</option>
                            <option value="20">접수중</option>
                            <option value="30">접수완료</option>
                            <option value="40">확정완료</option>
                            <option value="41">현장결제</option>
                        </select>
					</div>
					<a href="#" onclick="ChangeData()" class="btn-sm btn btn-primary">선택 상태 변경</a>
				</div>
			</div>
		</div>
        <div class="table-responsive">
			<div id="div-gd" style="height: calc(100vh - 434.031px); width: 100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",type:'NumType', cellClass: 'hd-grid-code'},
        {headerName: "이벤트", field: "title", width:200},
        {headerName: "이미지", field: "thumb_img", cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                        return '<img style=" width:50%; height:auto;" class="img" src="' + params.data.thumb_img +'"/>';
                    }},
        {headerName: "제목", field: "subject",width:500,
            cellRenderer: function (params) {
                        if (params.value !== undefined) {
                            return '<a href="/head/classic/cls01/show/'+params.data.idx+'">' + params.value + '</a>';
                        }
        }},
        {headerName: "작성자", field: "admin_nm", width:100},
        {headerName: "공개여부", field: "use_yn", cellClass: 'hd-grid-code'},
        {headerName: "등록일시", field: "regi_date", width:130},
        {headerName: "조회수", field: "cnt", type:'numberType', cellClass: 'hd-grid-code'},
        { width: "auto" }
    ];
    
    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    pApp.ResizeGrid(275);
    pApp.BindSearchEnter();

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/classic/cls01/search', data, 1);
    }
</script>
<script type="text/javascript" charset="utf-8">
    
    $(function(){
        Search();
    });
</script>
@stop