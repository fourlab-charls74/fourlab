@extends('head_with.layouts.layout')
@section('title','트레킹 공지사항')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">트레킹 공지사항</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 트레킹</span>
        <span>/ 트레킹 공지사항</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="/head/promotion/prm11/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일 :</label>
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
                            <label for="">트레킹 이벤트 :</label>
                            <div class="flax_box">
                                <select name='evt_idx' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($evt as $evt)
                                    <option value='{{ $evt->idx }}'>{{ $evt->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">제목 :</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all" name='subject' value=''>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="">내용 :</label>
                        <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all" name='content' value=''>
                        </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">공개여부 :</label>
                            <div class="flax_box">
                                <select name='use_yn' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    <option value='Y'>예</option>
                                    <option value='N'>아니요</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/head/promotion/prm11/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

    </div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
  <div class="card-body">
    <div class="card-title">
        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
    </div>
    <div class="table-responsive">
        <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
    </div>
  </div>
</div>
<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",type:'NumType'},
        {headerName: "이벤트", field: "title", width:200},
		{headerName: "이미지", field:"img", height:50,
			cellRenderer: function (params) {
				if (params.value !== undefined && params.value !== "" && params.value !== null) {
					return '<img src="' + params.value + '" class="img" style="width:50px; height:auto;"/>';
				}
			}
		},
        {headerName: "제 목", field: "subject", width:500,
            cellRenderer: function(params) {
                return '<a href="/head/promotion/prm11/show/' + params.data.idx +'" rel="noopener">'+ params.value+'</a>'
            }
        },
        {headerName: "작성자", field: "admin_nm", width:100},
        {headerName: "등록일", field: "regi_date", width:150},
        {headerName: "조회수", field: "cnt"}
    ];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(250);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm11/search', data);
    }

</script>
@stop
