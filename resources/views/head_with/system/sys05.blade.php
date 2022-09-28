@extends('head_with.layouts.layout')
@section('title','환경관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">환경관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 시스템</span>
        <span>/ 환경관리</span>
    </div>
</div>
<div class="card shadow mb-1">
    <div class="card-body">
        <div style="text-align:right;">
            <a href="/head/system/sys04" id="code_view" class="btn btn-sm btn-primary shadow-sm pl-2">코드별 보기</a>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="store-tab" data-toggle="tab" href="#store" role="tab" aria-controls="store" aria-selected="true">상점</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="order-tab" data-toggle="tab" href="#order" role="tab" aria-controls="order" aria-selected="false">주문</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" id="pay-tab" data-toggle="tab" href="#pay" role="tab" aria-controls="pay" aria-selected="false">결제</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="stock-tab" data-toggle="tab" href="#stock" role="tab" aria-controls="stock" aria-selected="false">재고</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" id="delivery-tab" data-toggle="tab" href="#delivery" role="tab" aria-controls="delivery" aria-selected="false">배송</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="point-tab" data-toggle="tab" href="#point" role="tab" aria-controls="point" aria-selected="false">적립금</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" id="coupon-tab" data-toggle="tab" href="#coupon" role="tab" aria-controls="coupon" aria-selected="false">쿠폰</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" id="kakao-tab" data-toggle="tab" href="#kakao" role="tab" aria-controls="kakao" aria-selected="false">KAKAO</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" id="api-tab" data-toggle="tab" href="#api" role="tab" aria-controls="api" aria-selected="false">API</a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" id="sms-tab" data-toggle="tab" href="#sms" role="tab" aria-controls="sms" aria-selected="false">SMS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="naver_checkout-tab" data-toggle="tab" href="naver_checkout" role="tab" aria-controls="naver_checkout" aria-selected="false">네이버 체크아웃</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">이메일</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="stock_reduction-tab" data-toggle="tab" href="#stock_reduction" role="tab" aria-controls="stock_reduction" aria-selected="false">부가기능</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="list_count-tab" data-toggle="tab" href="#list_count" role="tab" aria-controls="list_count" aria-selected="false">게시물</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab" aria-controls="admin" aria-selected="false">서비스</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="image-tab" data-toggle="tab" href="#image" role="tab" aria-controls="image" aria-selected="false">이미지</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="etc-tab" data-toggle="tab" href="#etc" role="tab" aria-controls="etc" aria-selected="false">기타</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="store" role="tabpanel" aria-labelledby="store-tab">
                <form name="shop" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">상점 정보</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>상점 명</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='name' value="{{@$name}}">
                                                                    <div class="gray">* 고객에게 주문, 클레임, 배송 처리 등 SMS가 발송되는 메시지 내용에 적용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 코드</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='code' value='{{@$code}}'>
                                                                    <div class="gray">* 상점 코드를 입력해 주십시오. 상점 코드는 영문 또는 숫자로 입력해주십시오.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 전화번호</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='phone' value='{{@$phone}}'>
                                                                    <div class="gray">* 상점 전화번호는 SMS 발송 시 회신 전화번호로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 도메인</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='domain' value='{{@$s_domain}}'>
                                                                    <div class="gray">* 상점 도메인을 입력해 주십시오. "http://"는 생략합니다.  EX)www.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>관리자 도메인</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='domain_bizest' value='{{@$a_domain}}'>
                                                                    <div class="gray">* 외부 서비스 연동 시 관리자 도메인을 사용하게 되며, 기본값은 "bizest" 입니다. "http://"는 생략합니다.    EX)bizest.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 이메일</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='email' value='{{@$email}}'>
                                                                    <div class="gray">* 메일 발송 시 발송자 이메일로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>타이틀 문구</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='title' value='{{@$title}}'>
                                                                    <div class="gray">* 상점의 모든 페이지의 기본 타이틀 태그로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>메인 페이지 타이틀 문구</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='title_main' value='{{@$title_main}}'>
                                                                    <div class="gray">* 쇼핑몰 메인 페이지의 타이틀 태그로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>메타 태그</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='meta_tag' value='{{@$meta_tag}}'>
                                                                    <div class="gray">* 쇼핑몰을 대표할 수 있는 키워드로 메타태그를 작성합니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>공통 스크립트</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <textarea rows="5" style="width: 100%;" name="add_script_content">{{@$add_script}}</textarea>
                                                                    <div class="gray">* 공통 스크립트의 내용을 푸터에 삽입하여 전체 쇼핑몰에 적용되도록 설정합니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>판매처</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <select name="sale_place" class="select" style="width: 200px;">
                                                                        <option value=''>선택</option>
                                                                        <option value='{{@$sale_place}}' selected>{{@$sale_place}}</option>
                                                                    </select>
                                                                </div>
                                                                <div class="gray">* 판매처를 선택해주십시오.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData_shop();" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="order" role="tabpanel" aria-labelledby="order-tab">
            <form name="order" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">주문취소 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>자동 취소 기간</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='' value="" style="width:150px;"> 일
                                                                </div>
                                                                <div class="gray">* 주문 완료 후 자동 취소 기간 동안 입금되지 않은 주문건은 자동으로 취소됩니다.</div>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">현금영수증 사용</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>현금연수증 사용 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="form-inline form-radio-box">
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" name="cash_use_yn" id="cash_use_y" class="custom-control-input" value="Y" @if ($cash_use_yn === 'Y') checked @endif/>
                                                                            <label class="custom-control-label" for="cash_use_y">사용함</label>
                                                                        </div>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" name="cash_use_yn" id="cash_use_n" class="custom-control-input" value="N" @if ($cash_use_yn === 'N') checked @endif/>
                                                                            <label class="custom-control-label" for="cash_use_n">사용안함</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 현금영수증을 사용할 경우, "무통장 입금"으로 주문한 고객은 현금영수증을 신청할 수 있으며, 신청된 현금영수증은 입금 확인 시 자동으로 발행됩니다.</div>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">입금은행</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table">
                                                    <thead class="thead">
                                                        <tr style="text-align:center">
                                                            <th scope="col">은행</th>
                                                            <th scope="col">계좌번호</th>
                                                            <th scope="col">예금주</th>
                                                            <th scope="col"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr style="text-align:center">
                                                            <th><input type="text" id="bank_nm" name="bank_nm" class="form-control form-control-sm w-100" value="{{@$bank_nm}}"></th>
                                                            <td><input type="text" id="account_no" name="account_no" class="form-control form-control-sm w-100" value="{{@$account_no}}"></td>
                                                            <td><input type="text" id="account_holder" name="account_holder" class="form-control form-control-sm w-100" value="{{@$account_holder}}"></td>
                                                            <td><button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="remove1()">지움</button></td>
                                                        </tr>
                                                        <tr style="text-align:center">
                                                            <th><input type="text" class="form-control form-control-sm w-100"></th>
                                                            <td><input type="text" class="form-control form-control-sm w-100"></td>
                                                            <td><input type="text" class="form-control form-control-sm w-100"></td>
                                                            <td><button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1">지움</button></td>
                                                        </tr>
                                                        <tr style="text-align:center">
                                                            <th><input type="text" class="form-control form-control-sm w-100"></th>
                                                            <td><input type="text" class="form-control form-control-sm w-100"></td>
                                                            <td><input type="text" class="form-control form-control-sm w-100"></td>
                                                            <td><button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1">지움</button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData_order();" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
            <form name="delivery" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">배송</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>기본 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='base_delivery_fee' value="{{@$base_delivery_fee}}" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 기본 배송비를 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>추가 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='add_delivery_fee' value="{{@$add_delivery_fee}}" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 도서, 산간 지역으로 배송 시 추가로 발생하는 금액을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>배송비 무료</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='free_delivery_amt' value="{{@$free_delivery_amt}}" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 고객이 배송비 무료 금액 이상 구매 시 무료로 배송을 합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매 기본 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='wholesale_base_delivery_fee' value="" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 도매 기본 배송비를 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매 추가 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='wholesale_add_delivery_fee' value="" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 도서,산간 지역으로 배송 시 추가로 발생하는 금액을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매 배송비 무료</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='wholesale_free_delivery_amt' value="{{@$wholesale_free_delivery_amt}}" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 도매 고객이 배송비 무료 금액 이상 구매 시 무료로 배송을 합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>주거래 택배업체</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <select name="" class="select" style="width: 200px;">
                                                                        <option value=''>선택</option>
                                                                        <option value='{{@$dlv_cd}}' selected>{{@$dlv_cd}}</option>
                                                                    </select>
                                                                </div>
                                                                <div class="gray">* 주거래 택배업체를 선택하여 주십시오.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div><h4>* 업체 별 배송비 설정은 "<a href="/head/standard/std02" target="_blank">기준정보 > 업체</a>"메뉴에서 업체별 배송비 정책을 설정할 수 있습니다.</h4></div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">당일 배송</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>당일배송 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_yn" id="day_delivery_y" class="custom-control-input" value="Y"/>
                                                                        <label class="custom-control-label" for="day_delivery_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_yn" id="day_delivery_n" class="custom-control-input" value="N"/>
                                                                        <label class="custom-control-label" for="day_delivery_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 당일배송 기능의 사용여부를 선택하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>당일배송 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='day_delivery_amt' value="" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 고객이 당일배송을 선택할 경우 추가로 지불해야할 배송비를 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>당일배송 가능 상품</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_type" id="day_delivery_type_s" class="custom-control-input" value="S"/>
                                                                        <label class="custom-control-label" for="day_delivery_type_s">매입상품</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_type" id="day_delivery_type_p" class="custom-control-input" value="P"/>
                                                                        <label class="custom-control-label" for="day_delivery_type_p">위탁상품</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_type" id="day_delivery_type_a" class="custom-control-input" value="A"/>
                                                                        <label class="custom-control-label" for="day_delivery_type_a">전체상품</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 당일배송이 가능한 상품유형을 선택하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>당일배송 가능 지역</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                <input type="text" class="input" name="day_delivery_zone" value="" maxlength="50" style="width: 30%;" />
                                                                </div>
                                                                <div class="gray">* 당일배송 가능 지역을 입력하십시오. 지역이 여러곳인 경우에는 콤마(,)를 사용하여 입력하실 수 있습니다.  EX)서울,경기,대전</div>
                                                            </td>
                                                        </tr>
                                                  
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData_delivery();" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="point" role="tabpanel" aria-labelledby="point-tab">
                <form name="point" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">적립금 사용 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>사용 가능 적립금</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='base_delivery_fee' value="" style="width:150px;">원 이상
                                                                </div>
                                                                <div class="gray">* 고객이 적립한 적립금이 "사용 가능 적립금" 이상인 경우 적립금을 사용할 수 있습니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">적립금 지급 방법 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>적립금 지급 방법 설정</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="p_give_type" id="p_give_type_g" class="custom-control-input" value="G"/>
                                                                        <label class="custom-control-label" for="p_give_type_g">상품가격 기준</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="p_give_type" id="p_give_type_R" class="custom-control-input" value="R"/>
                                                                        <label class="custom-control-label" for="day_delivery_n">구매금액 기준</label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">회원가입 적립금 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>회원가입 축하 적립금</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='join_point' value="{{@$join_point}}" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 쇼핑몰 회원으로 가입한 고객에게 지금할 적립금을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">주문 적립금 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th rowspan="2">적립금 지급 방식</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="policy" id="policy_s" class="custom-control-input" value="S" @if ($policy =='S') checked @endif/>
                                                                        <label class="custom-control-label" for="policy_s">쇼핑몰 기본 정책</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 상품 구매 시 설정한 비율에 맞춰 고객에게 적립금을 지급 하도록 설정합니다.</div><br>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='ratio' value="{{@$ratio}}" style="width:150px;">%
                                                                </div>
                                                                <div class="gray">* 상품 구매시 지급할 적립금의 비율을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="policy" id="policy_g" class="custom-control-input" value="G" @if ($policy =='G') checked @endif/>
                                                                        <label class="custom-control-label" for="policy_g">상품별 적립금 정책</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 상품 구매시 상품에 설정된 적립금을 구매 고객에게 지급 하도록 설정합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>주문취소 적립금 자동 환원</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="return_yn" id="return_y" class="custom-control-input" value="Y" @if ($return_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="return_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="return_yn" id="return_n" class="custom-control-input" value="N" @if ($return_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="return_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 당일배송이 가능한 상품유형을 선택하십시오.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">상품 후기 작성 시 적립금 자동 지급 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>자동지급 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="estimate_point_yn" id="estimate_point_y" class="custom-control-input" value="Y"/>
                                                                        <label class="custom-control-label" for="day_delivery_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="estimate_point_yn" id="estimate_point_n" class="custom-control-input" value="N"/>
                                                                        <label class="custom-control-label" for="day_delivery_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* 상품 구매 고객이 후기를 작성할 경우 자동으로 적립금을 지금할 것인지 여부를 설정합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>적립금 지급액</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='estimate_point' value="" style="width:150px;">원
                                                                </div>
                                                                <div class="gray">* 상품 구매 고객이 후기를 작성했을 때 자동으로 지급할 적립금을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData_point();" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="kakao" role="tabpanel" aria-labelledby="kakao-tab">
                <form name="kakao" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">KAKAO 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>KAKAO 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="kakao_yn" id="kakao_y" class="custom-control-input" value="Y" @if ($kakao_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="kakao_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="kakao_yn" id="kakao_n" class="custom-control-input" value="N" @if ($kakao_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="kakao_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* KAKAO 사용 여부를 "사용안함"으로 설정하면 알림톡이 발송되지 않습니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>KAKAO 발송키</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='kakao_key' value="" style="width:200px;">
                                                                </div>
                                                                <div class="gray">* 카카오 알림톡 발송 키 입니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData_delivery();" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">
                <form name="sms" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">SMS 설정</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>SMS 사용 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="kakao_yn" id="kakao_y" class="custom-control-input" value="Y" @if ($kakao_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="kakao_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="kakao_yn" id="kakao_n" class="custom-control-input" value="N" @if ($kakao_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="kakao_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div class="gray">* SMS 사용 여부를 "사용안함"으로 설정하면 SMS가 발송되지 않으며, 아래의 "SMS 전송 설정" 항목의 설정된 조건들도 모두 무효화됩니다. </div>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">치환 예약어</h5>
                                </div>
                                <div class="row_wrap">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-box-ty2 mobile">
                                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                    <colgroup>
                                                        <col width="150px">
                                                    </colgroup>
                                                    <tbody>
                                                        <tr>
                                                            <th>자동 치환 예약어</th>
                                                            <td>
                                                                <div>
                                                                    <table>
                                                                        <tr style="border: none;">
                                                                            <td style="width:20%;border: none;">[SHOP_NAME] : 쇼핑몰</td>
                                                                            <td style="width:20%;border: none;">[USER_NAME] : 회원명</td>
                                                                            <td style="width:20%;border: none;">[PASSWD] : 비밀번호</td>
                                                                            <td style="width:20%;border: none;">[ORDER_NO] : 주문번호</td>
                                                                        </tr>
                                                                        <tr style="border: none;">
                                                                            <td style="width:20%;border: none;">[BANK] : 은행</td>
                                                                            <td style="width:20%;border: none;">[ACCOUNT] : 계좌번호</td>
                                                                            <td style="width:20%;border: none;">[DEPOSITOR] : 예금주</td>
                                                                            <td style="width:20%;border: none;">[ORDER_AMT] : 주문금액</td>
                                                                        </tr>
                                                                        <tr style="border: none;">
                                                                            <td style="width:20%;border: none;">[AUTH_NO] : 인증번호</td>
                                                                            <td style="width:20%;border: none;">[GOODS_NAME] : 상품명</td>
                                                                            <td style="width:20%;border: none;">[DELIVERY_NAME] : 택배사명</td>
                                                                            <td style="width:20%;border: none;">[DELIVERY_NO] : 택배송장번호</td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>적용 예</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                        쇼핑몰 상호가 "Fjallraven" 이며, 회원의 이름이 "홍길동" 인 경우, "회원 가입 축하 SMS"는 아래와 같이 자동 치환되어 발송됩니다.
                                                                </div>
                                                                <div style="margin:5px;font-size:12px;color:blue;font-weight: bold;font-family: '돋움';">
                                                                    [Fjallraven]홍길동 회원님의 가입을 진심으로 축하드립니다.
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData_delivery();" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="tab-pane fade" id="naver_checkout" role="tabpanel" aria-labelledby="naver_checkout-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="stock_reduction" role="tabpanel" aria-labelledby="stock_reduction-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="list_count" role="tabpanel" aria-labelledby="list_count-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="image" role="tabpanel" aria-labelledby="image-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="etc" role="tabpanel" aria-labelledby="etc-tab">.ㅇㅇ</div>
            <!-- <div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="coupon" role="tabpanel" aria-labelledby="coupon-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="pay" role="tabpanel" aria-labelledby="pay-tab">.ㅇㅇ</div>
            <div class="tab-pane fade" id="stock" role="tabpanel" aria-labelledby="stock-tab">.ㅇㅇ</div> -->
        </div>
    </div>
</div>

<script>
    function updateData_shop() {
    
        let frm = $('form[name=shop]').serialize();

        frm += "&type=shop";

        if ($("input[name='name'").val() === '') {
                $("input[name='name'").focus();
                alert('상점 명을 입력해 주세요.');
                return false;
        }
        if ($("input[name='code'").val() === '') {
                $("input[name='code'").focus();
                alert('상점 코드를 입력해 주세요.');
                return false;
        }
        if ($("input[name='phone'").val() === '') {
                $("input[name='phone'").focus();
                alert('상점 전화번호를 입력해 주세요.');
                return false;
        }
        if ($("input[name='domain'").val() === '') {
                $("input[name='domain'").focus();
                alert('상점 도메인을 입력해 주세요.');
                return false;
        }
        if ($("input[name='domain_bizest'").val() === '') {
                $("input[name='domain_bizest'").focus();
                alert('관리자 도메인을 입력해 주세요.');
                return false;
        }
        if ($("input[name='email'").val() === '') {
                $("input[name='email'").focus();
                alert('상점 이메일을 입력해 주세요.');
                return false;
        }
        if ($("input[name='title'").val() === '') {
                $("input[name='title'").focus();
                alert('타이틀 문구를 입력해 주세요.');
                return false;
        }
        if ($("input[name='title_main'").val() === '') {
                $("input[name='title_main'").focus();
                alert('메인 페이지 타이틀 문구를  입력해 주세요.');
                return false;
        }
        if ($("input[name='meta_tag'").val() === '') {
                $("input[name='meta_tag'").focus();
                alert('메타 태그를 입력해 주세요.');
                return false;
        }
        if ($("input[name='sale_place'").val() === '') {
                $("input[name='sale_place'").focus();
                alert('판매처를 선택해 주세요.');
                return false;
        }
            
            $.ajax({
                method: 'post',
                url: '/head/system/sys05/update',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == '200') {
                        alert('상점 정보가 수정되었습니다.');
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(data);
                    }
                },
                error: function(e) {
                        console.log(e.responseText)
                }
            });

    }

    function updateData_order() {
    
        let frm = $('form[name=order]').serialize();
        
        frm += "&type=order";

            $.ajax({
                method: 'post',
                url: '/head/system/sys05/update',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == '200') {
                        alert('주문 정보가 수정되었습니다.');
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(data);
                    }
                },
                error: function(e) {
                        console.log(e.responseText)
                }
            });

    }


    function updateData_delivery() {
    
        let frm = $('form[name=delivery]').serialize();
        frm += "&type=delivery";
            $.ajax({
                method: 'post',
                url: '/head/system/sys05/update',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == '200') {
                        alert('배송 정보가 수정되었습니다.');
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(data);
                    }
                },
                error: function(e) {
                        console.log(e.responseText)
                }
            });

    }

    function updateData_point() {
    
        let frm = $('form[name=point]').serialize();
        frm += "&type=point";
            $.ajax({
                method: 'post',
                url: '/head/system/sys05/update',
                data: frm,
                dataType: 'json',
                success: function(data) {
                    if (data.code == '200') {
                        alert('적립금 정보가 수정되었습니다.');
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(data);
                    }
                },
                error: function(e) {
                        console.log(e.responseText)
                }
            });

    }
</script>

<script>
    function remove1(){
       let bank_nm = document.getElementById('bank_nm');
       let account_no = document.getElementById('account_no');
       let account_holder = document.getElementById('account_holder');

       bank_nm.value = "";
       account_holder.value = "";
       account_no.value = "";
       
    }

</script>

   
@stop
