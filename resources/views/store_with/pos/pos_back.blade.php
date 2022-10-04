@extends('store_with.layouts.layout-nav')
@section('title','POS')
@section('content')

<link href="{{ URL::asset('css/pos.css')}}" rel="stylesheet" type="text/css" />

<div id="pos" class="row w-100 m-0" style="height: 100vh;">
    <div class="col-lg-8 d-flex flex-column p-0">
        <div class="row flex-grow-1 m-0">

            <div class="col-lg-6 border-right border-dark">
                <div class="p-2">
                    <div class="store-title d-flex flex-column mt-3 mb-4 pl-2 pt-1">
                        <h1 class="fs-20 fw-b">본사매장</h1>
                        <p class="fc-gray fs-12 fw-b pl-1">M0001</p>
                    </div>
                    <div class="d-flex flex-column mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>영수증번호</h4>
                            <button type="button" class="btn fc-price fs-14 fw-b" data-toggle="modal" data-target="#receiptNoModal">M0001202207010001</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>판매구분</h4>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary p-1 pl-4 pr-4 fs-12 active">
                                    <input type="radio" name="sale_type" autocomplete="off" disabled checked> 판매
                                </label>
                                <label class="btn btn-outline-primary p-1 pl-4 pr-4 fs-12">
                                    <input type="radio" name="sale_type" autocomplete="off" disabled> 환불
                                </label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>판매유형</h4>
                            {{-- <button type="button" class="btn">일반판매</button> --}}
                            <div class="input-group w-75">
                                <select class="custom-select fs-12">
                                  <option selected>일반판매</option>
                                  <option value="1">5%할인</option>
                                  <option value="2">10%할인</option>
                                  <option value="3">15%할인</option>
                                </select>
                              </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>특이사항</h4>
                            <textarea class="form-control w-75 fs-10 noresize" rows="2"></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline w-100 pt-3 pb-3 mr-3">영수증조회</button>
                            <button type="button" class="btn btn-outline w-100 pt-3 pb-3">당일판매내역</button>
                        </div>
                    </div>
                    <div style="height: 100px;"></div>
                    <div class="d-flex flex-column">
                        <div class="d-flex justify-content-between mb-4">
                            <h3 class="fc-gray">고객정보</h3>
                            <div class="d-flex">
                                <button type="button" class="btn p-1 pl-3 pr-3 mr-2 text-light fs-12 bg-primary">조회</button>
                                <button type="button" class="btn p-1 pl-3 pr-3 text-light fs-12 bg-primary">등록</button>
                            </div>
                        </div>
                        @if(false)
                        <p class="w-100 pt-5 text-center fc-gray fs-12">선택된 고객이 없습니다.</p>
                        @else
                        <p class="mb-2 fs-14"><strong class="fw-b">최유현</strong> (010-2261-2183)</p>
                        <p class="mb-4 fs-12">경기도 성남시 분당구 판교역로 230 삼환하이팩스 B동 208-2호</p>
                        <div class="d-flex justify-content-center w-100">
                            <div class="d-flex flex-column align-items-center pr-5 mr-5 border-right">
                                <span class="mb-2 fc-gray fs-12">본사 마일리지</span>
                                <strong class="fc-price fs-18 fw-b">0</strong>
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <span class="mb-2 fc-gray fs-12">매장 마일리지</span>
                                <strong class="fc-price fs-18 fw-b">0</strong>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 border-right border-dark"></div>

        </div>
        <div class="d-flex justify-content-between align-items-center pl-3 pr-3 border-top border-right border-dark" style="height: 70px;">
            <p class="fc-gray fs-14 fw-sb">Today<strong class="fc-price fs-18 ml-4 mr-2">2,000,000원</strong><span class="fs-12">(100개)</span></p>
            <p class="fs-16 fw-sb">2022년 7월 4일 오전 10:00:00</p>
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column p-0">
        <div class="d-flex justify-content-end bg-dark p-2 pr-3">
            <button type="button" class="btn fc-gray fs-20" onclick="closePos()">&times;</button>
        </div>
        <div class="d-flex flex-column justify-content-between flex-grow-1">
            <div class="pt-2">
                <h3 class="p-3 pb-0 fc-gray">선택한 상품목록</h3>
                <ul>
                    <li class="p-2 pl-3 pr-3">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-grow-1">
                                <span class="w-10 fc-gray fw-sb" style="min-width: 30px;">01</span>
                                <div class="d-flex flex-column w-100">
                                    <strong class="fs-14 fw-b">KANKEN CLASSIC</strong>
                                    <p class="fc-gray fs-10 fw-sb">F232UBP011 - BM / 99</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end" style="min-width: 110px;">
                                <strong class="fc-price fs-14 fw-b">1,054,500</strong>
                                <p class="fc-gray fs-10 fw-sb">10개</p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-3 p-2 pl-3 pr-3" style="background-color: #f2f2f2;">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-grow-1">
                                <span class="w-10 fc-gray fw-sb" style="min-width: 30px;">02</span>
                                <div class="d-flex flex-column w-100">
                                    <strong class="fs-14 fw-b">KANKEN CLASSIC 2022 NEW VERSION BAG BIG SALE</strong>
                                    <p class="fc-gray fs-10 fw-sb">F232UBP011 - DD / FREE</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end" style="min-width: 110px;">
                                <strong class="fc-price fs-14 fw-b">95,000</strong>
                                <p class="fc-gray fs-10 fw-sb">1개</p>
                            </div>
                        </div>
                    </li>
                    <li class="mt-3 p-2 pl-3 pr-3">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-grow-1">
                                <span class="w-10 fc-gray fw-sb" style="min-width: 30px;">03</span>
                                <div class="d-flex flex-column w-100">
                                    <strong class="fs-14 fw-b">KANKEN CLASSIC</strong>
                                    <p class="fc-gray fs-10 fw-sb">F232UBP011 - BM / 99</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end" style="min-width: 110px;">
                                <strong class="fc-price fs-14 fw-b">1,054,500</strong>
                                <p class="fc-gray fs-10 fw-sb">10개</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div>
                <div class="d-flex justify-content-between align-items-center p-3">
                    <p class="fs-12 fw-sb pt-1">TOTAL</p>
                    <p class="fc-price fs-16 fw-b">2,359,000</p>
                </div>
                <button type="button" class="btn w-100 text-light fs-20 fw-sb bg-primary rounded-0" style="height: 70px;">판매</button>
            </div>
        </div>
    </div>

</div>

{{-- MODAL --}}
<div id="pos-modal">
    <div class="modal fade" id="receiptNoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="mt-1 fs-12 fw-b">영수증번호</h5>
                    <div class="d-flex flex-column align-items-center mt-5">
                        <input type="text" class="inp mb-4 p-2 w-75 text-center fs-18 fw-sb" value="M0001202207010001" />
                        <div class="d-flex justify-content-end w-100 fs-12">
                            <button type="button" class="btn p-2 pl-5 pr-5 mr-2 fc-gray fw-sb" data-dismiss="modal">취소</button>
                            <button type="button" class="btn p-2 pl-5 pr-5 text-light fw-sb bg-primary rounded-0">확인</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('store_with.pos.pos_js')

@stop
