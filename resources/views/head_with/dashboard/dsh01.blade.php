@extends('head_with.layouts.layout')
@section('title','대시보드')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">대시보드</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 대시보드</span>
        </div>
    </div>
    <form method="get" name="search" id="search">
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
                                <label for="user_yn">주문일자</label>
                                <div class="date-switch-wrap form-inline">
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
                                <label for="">구분</label>
                                <div class="form-inline form-check-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="type" id="type_item" value="item"  checked>
                                        <label class="custom-control-label" for="type_item">품목</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="type" id="type_brand" value="brand">
                                        <label class="custom-control-label" for="type_brand">브랜드</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">크기</label>
                                <div class="form-inline form-check-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="size" id="type_cnt" value="cnt"  checked>
                                        <label class="custom-control-label" for="type_cnt">주문수량</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="size" id="type_amt" value="amt">
                                        <label class="custom-control-label" for="type_amt">주문금액</label>
                                    </div>
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
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="card-title">
                <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
            </div>
            <ul class="mb-0">
                <li>클레임율(0~100%) : Green ~ Red</li>
            </ul>
        </div>
    </div>
    <div id="myChart" style="height: calc(100vh - 430px)">
    </div>
    <script>var __basePath = './';</script>
    <script src="https://unpkg.com/ag-charts-community@4.0.0/dist/ag-charts-community.min.js">
    </script>
<!-- script -->
@include('head_with.dashboard.dsh01_js')
<!-- script -->
@endsection
