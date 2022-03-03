@extends('head_with.layouts.layout')
@section('title','키워드별 상품 주문')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">키워드별 상품 주문</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매출통계</span>
            <span>/ 주문</span>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
    </div>
    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
        <div class="card-body">
            <div class="card-title mb-3">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
@stop
