@extends('head_with.layouts.layout')
@section('title','클래식 공지사항')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">(개)클래식 공지사항</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 클래식</span>
        <span>/ 공지사항</span>
    </div>
</div>
	<form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <a href="/head/classic/classic01/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">등록일 : </label>
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
                                <label for="">트레킹 이벤트 : </label>
                                <div class="flax_box">
                                    <select name='evtTitle' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach($evt_mst as $mst)
                                        <option value='{{$mst->idx}}'>{{$mst->title}} (<?php $sd = substr( $mst->start_date, 0, 8); echo $sd;?> ~ <?php  $ed = substr( $mst->end_date, 0, 8); echo $ed;?>)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">제목 : </label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all search-enter" name='subject' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                            <label for="">내용 : </label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='content' value=''>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">공개여부 : </label>
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
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>

    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
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
                <div id="div-gd" style="height:calc(100vh - 500px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>

	<script language="javascript">
        var columns = [
            {headerName: "#", field: "num",type:'NumType', cellClass: 'hd-grid-code'},
            {headerName: "이벤트", field: "title", width:200},
            {headerName: "이미지", field: "thumb_img", cellClass: 'hd-grid-code',
                cellRenderer: function(params) {
                            return '<img style="width:50%; height:auto;" class="img" src="' + params.value +'"/>';
                        }},
            {headerName: "제목", field: "subject",width:500,
                cellRenderer: function (params) {
                            if (params.value !== undefined) {
                                return '<a href="#" onclick="return AddProducts(\'' + params.data.idx + '\');">' + params.value + '</a>';

                            }
                        }},
            {headerName: "작성자", field: "admin_nm", width:100},
            {headerName: "공개여부", field: "use_yn", cellClass: 'hd-grid-code'},
            {headerName: "등록일시", field: "regi_date", width:130},
            {headerName: "조회수", field: "cnt", type:'numberType', cellClass: 'hd-grid-code'},
            { width: "auto" }
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
		const pApp = new App('', { gridId: "#div-gd" });
		const gridDiv = document.querySelector(pApp.options.gridId);
		const gx = new HDGrid(gridDiv, columns);

		pApp.ResizeGrid(265);
        pApp.BindSearchEnter();

		function Search() {
			let data = $('form[name="search"]').serialize();
            gx.Request('/head/classic/classic01/search', data,1);
		}
		
		$(function(){
			Search();
		});
    </script>
@stop