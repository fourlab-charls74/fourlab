@extends('head_with.layouts.layout-nav')
@section('title','접수 상세 내역')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">접수 상세 내역</h1>
        <div>
            <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 receipt-btn">수정</a>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>

    <!-- 주문번호 찾기 -->
    <div class="card_wrap mb-3">
        <div class="card shadow">
            <div class="card-header mb-0">
                <h5 class="m-0 font-weight-bold">주문번호</h5>
            </div>
            <div class="card-body">
                <div class="form-group" style="display:flex">
                    <label for=""><a href="#" class="btn btn-sm btn-secondary ord-no-btn">주문번호 찾기</a> :</label>
                    <div class="form-inline inline_input_box">
                        <a href="#" class="btn btn-sm btn-secondary search-btn">검색</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 주문번호 찾기 끝 -->

</div>

@stop
