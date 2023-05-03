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
            <a href="/head/system/sys04" id="code_view" class="btn btn-sm btn-primary shadow-sm">코드별 보기</a>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="shop-tab" data-toggle="tab" href="#shop" role="tab" aria-controls="shop" aria-selected="false">상점</a>
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
            <!-- <li class="nav-item">
                <a class="nav-link" id="naver_checkout-tab" data-toggle="tab" href="naver_checkout" role="tab" aria-controls="naver_checkout" aria-selected="false">네이버 체크아웃</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">이메일</a>
            </li> -->
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
                <a class="nav-link" id="mobile-tab" data-toggle="tab" href="#mobile" role="tab" aria-controls="mobile" aria-selected="false">모바일</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="image-tab" data-toggle="tab" href="#image" role="tab" aria-controls="image" aria-selected="false">이미지(FTP)</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="shop" role="tabpanel" aria-labelledby="shop-tab">
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
                                                                    <div style="color:gray;" >* 고객에게 주문, 클레임, 배송 처리 등 SMS가 발송되는 메시지 내용에 적용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 코드</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='code' value='{{@$code}}'>
                                                                    <div style="color:gray;">* 상점 코드를 입력해 주십시오. 상점 코드는 영문 또는 숫자로 입력해주십시오.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 전화번호</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='phone' value='{{@$phone}}'>
                                                                    <div style="color:gray;">* 상점 전화번호는 SMS 발송 시 회신 전화번호로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 도메인</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='domain' value='{{@$s_domain}}'>
                                                                    <div style="color:gray;">* 상점 도메인을 입력해 주십시오. "http://"는 생략합니다.  EX)www.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>관리자 도메인</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='domain_bizest' value='{{@$a_domain}}'>
                                                                    <div style="color:gray;">* 외부 서비스 연동 시 관리자 도메인을 사용하게 되며, 기본값은 "bizest" 입니다. "http://"는 생략합니다.    EX)bizest.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>관리자 도메인 NEW</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='domain_bizest_new' value='{{@$a_new_domain}}'>
                                                                    <div style="color:gray;">* 외부 서비스 연동 시 관리자 도메인 NEW을 사용하게 되며, 기본값은 "handle" 입니다. "http://"는 생략합니다.    EX)handle.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상점 이메일</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='email' value='{{@$email}}'>
                                                                    <div style="color:gray;">* 메일 발송 시 발송자 이메일로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>타이틀 문구</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='title' value='{{@$title}}'>
                                                                    <div style="color:gray;">* 상점의 모든 페이지의 기본 타이틀 태그로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>메인 페이지 타이틀 문구</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='title_main' value='{{@$title_main}}'>
                                                                    <div style="color:gray;">* 쇼핑몰 메인 페이지의 타이틀 태그로 사용됩니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>메타 태그</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='meta_tag' value='{{@$meta_tag}}'>
                                                                    <div style="color:gray;">* 쇼핑몰을 대표할 수 있는 키워드로 메타태그를 작성합니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>공통 스크립트</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <textarea rows="5" style="width: 100%;" name="add_script_content">{{@$add_script_content}}</textarea>
                                                                    <div style="color:gray;">* 공통 스크립트의 내용을 푸터에 삽입하여 전체 쇼핑몰에 적용되도록 설정합니다.</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>판매처</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <select name="sale_place" class="form-control form-control-sm" style="width: 200px;">
                                                                        <option value=''>선택</option>
                                                                        <option value='{{@$sale_place}}' selected>{{@$sale_place}}</option>
                                                                    </select>
                                                                </div>
                                                                <div style="color:gray;">* 판매처를 선택해주십시오.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('shop');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
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
                                                                    <input type='text' class="form-control form-control-sm" name='cancel_period' value="{{@$cancel_period}}" style="width:150px;text-align:right;"> 일
                                                                </div>
                                                                <div style="color:gray;">* 주문 완료 후 자동 취소 기간 동안 입금되지 않은 주문건은 자동으로 취소됩니다.</div>
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
                                                                <div style="color:gray;">* 현금영수증을 사용할 경우, "무통장 입금"으로 주문한 고객은 현금영수증을 신청할 수 있으며, 신청된 현금영수증은 입금 확인 시 자동으로 발행됩니다.</div>
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
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('order');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
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
                                                                    <input type='text' class="form-control form-control-sm" name='base_delivery_fee' value="{{@$base_delivery_fee}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 기본 배송비를 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>추가 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='add_delivery_fee' value="{{@$add_delivery_fee}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 도서, 산간 지역으로 배송 시 추가로 발생하는 금액을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>배송비 무료</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='free_delivery_amt' value="{{@$free_delivery_amt}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 고객이 배송비 무료 금액 이상 구매 시 무료로 배송을 합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매 기본 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='wholesale_base_delivery_fee' value="{{@$wholesale_base_delivery_fee}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 도매 기본 배송비를 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매 추가 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='wholesale_add_delivery_fee' value="{{@$wholesale_add_delivery_fee}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 도서,산간 지역으로 배송 시 추가로 발생하는 금액을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매 배송비 무료</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='wholesale_free_delivery_amt' value="{{@$wholesale_free_delivery_amt}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 도매 고객이 배송비 무료 금액 이상 구매 시 무료로 배송을 합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>주거래 택배업체</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <select name="" class="form-control form-control-sm" style="width: 200px;">
                                                                        <option value=''>선택</option>
                                                                        <option value='{{@$dlv_cd}}' selected>{{@$dlv_cd}}</option>
                                                                    </select>
                                                                </div>
                                                                <div style="color:gray;">* 주거래 택배업체를 선택하여 주십시오.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div><h4>* 업체 별 배송비 설정은 "<a href="/head/standard/std02" target="_blank">기준정보 > 업체 관리</a>"메뉴에서 업체별 배송비 정책을 설정할 수 있습니다.</h4></div>
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
                                                                        <input type="radio" name="day_delivery_yn" id="day_delivery_y" class="custom-control-input" value="Y"  @if ($day_delivery_yn === 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="day_delivery_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_yn" id="day_delivery_n" class="custom-control-input" value="N"/ @if ($day_delivery_yn === 'N') checked @endif>
                                                                        <label class="custom-control-label" for="day_delivery_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 당일배송 기능의 사용여부를 선택하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>당일배송 배송비</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='day_delivery_amt' value="{{@$day_delivery_amt}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 고객이 당일배송을 선택할 경우 추가로 지불해야할 배송비를 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>당일배송 가능 상품</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_type" id="day_delivery_type_s" class="custom-control-input" value="S" @if ($day_delivery_type === 'S') checked @endif/>
                                                                        <label class="custom-control-label" for="day_delivery_type_s">매입상품</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_type" id="day_delivery_type_p" class="custom-control-input" value="P" @if ($day_delivery_type === 'P') checked @endif/>
                                                                        <label class="custom-control-label" for="day_delivery_type_p">위탁상품</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="day_delivery_type" id="day_delivery_type_a" class="custom-control-input" value="A" @if ($day_delivery_type === 'A') checked @endif/>
                                                                        <label class="custom-control-label" for="day_delivery_type_a">전체상품</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 당일배송이 가능한 상품유형을 선택하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>당일배송 가능 지역</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                <input type="text" class="form-control form-control-sm" name="day_delivery_zone" value="{{@$day_delivery_zone}}" maxlength="50" style="width: 30%;" />
                                                                </div>
                                                                <div style="color:gray;">* 당일배송 가능 지역을 입력하십시오. 지역이 여러곳인 경우에는 콤마(,)를 사용하여 입력하실 수 있습니다.  EX)서울,경기,대전</div>
                                                            </td>
                                                        </tr>
                                                  
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('delivery');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
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
                                                                    <input type='text' class="form-control form-control-sm" name='point_limit' value="{{@$point_limit}}" style="width:150px;text-align:right;">원 이상
                                                                </div>
                                                                <div style="color:gray;">* 고객이 적립한 적립금이 "사용 가능 적립금" 이상인 경우 적립금을 사용할 수 있습니다.</div>
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
                                                                        <input type="radio" name="p_give_type" id="p_give_type_r" class="custom-control-input" value="R"/>
                                                                        <label class="custom-control-label" for="p_give_type_r">구매금액 기준</label>
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
                                                                    <input type='text' class="form-control form-control-sm" name='join_point' value="{{@$join_point}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 쇼핑몰 회원으로 가입한 고객에게 지금할 적립금을 입력합니다.</div>
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
                                                            <th rowspan="2" width="200px">적립금 지급 방식</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="policy" id="policy_s" class="custom-control-input" value="S" @if ($policy =='S') checked @endif/>
                                                                        <label class="custom-control-label" for="policy_s">쇼핑몰 기본 정책</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 상품 구매 시 설정한 비율에 맞춰 고객에게 적립금을 지급 하도록 설정합니다.</div><br>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" id='ratio' name='ratio' value="{{@$ratio}}" style="width:150px;text-align:right;">%
                                                                </div>
                                                                <div style="color:gray;">* 상품 구매시 지급할 적립금의 비율을 입력합니다.</div>
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
                                                                <div style="color:gray;">* 상품 구매시 상품에 설정된 적립금을 구매 고객에게 지급 하도록 설정합니다.</div>
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
                                                                <div style="color:gray;">* 당일배송이 가능한 상품유형을 선택하십시오.</div>
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
                                                                        <input type="radio" name="estimate_point_yn" id="estimate_point_y" class="custom-control-input" value="Y" @if ($estimate_point_yn == 'Y') checked @endif />
                                                                        <label class="custom-control-label" for="estimate_point_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="estimate_point_yn" id="estimate_point_n" class="custom-control-input" value="N" @if ($estimate_point_yn == 'N') checked @endif />
                                                                        <label class="custom-control-label" for="estimate_point_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 상품 구매 고객이 후기를 작성할 경우 자동으로 적립금을 지금할 것인지 여부를 설정합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>적립금 지급액</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='estimate_point' value="{{@$estimate_point}}" style="width:150px;text-align:right;">원
                                                                </div>
                                                                <div style="color:gray;">* 상품 구매 고객이 후기를 작성했을 때 자동으로 지급할 적립금을 입력합니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('point');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
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
                                                                <div style="color:gray;">* KAKAO 사용 여부를 "사용안함"으로 설정하면 알림톡이 발송되지 않습니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>KAKAO 발송키</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm" name='sender_key' value="{{@$sender_key}}" style="width:200px;">
                                                                </div>
                                                                <div style="color:gray;">* 카카오 알림톡 발송 키 입니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('kakao');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
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
                                                                        <input type="radio" name="sms_yn" id="sms_y" class="custom-control-input" value="Y" @if ($sms_yn == 'Y') checked @endif />
                                                                        <label class="custom-control-label" for="sms_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="sms_yn" id="sms_n" class="custom-control-input" value="N" @if ($sms_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="sms_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* SMS 사용 여부를 "사용안함"으로 설정하면 SMS가 발송되지 않으며, 아래의 "SMS 전송 설정" 항목의 설정된 조건들도 모두 무효화됩니다. </div>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">SMS 전송 설정</h5>
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
                                                        <div class="flax_box">
                                                            <tr>
                                                                <td width="15%" height="30" style="text-align:center;font-weight: bold;">항목</td>
                                                                <td width="15%" style="text-align:center;font-weight: bold;">설정</td>
                                                                <td width="10%" style="text-align:center;font-weight: bold;">구분</td>
                                                                <td style="text-align:center;font-weight: bold;">메시지</td>
                                                            </tr>
                                                            <tr>
                                                                <th>회원가입 인증 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="auth_yn" value="Y" @if($auth_yn == 'Y') checked @endif/> 발송함</label>
                                                                    <label><input type="radio" name="auth_yn" value="N" @if ($auth_yn == 'N') checked @endif/> 발송안함</label>
                                                                </td>
                                                                <td >회원 가입 인증 SMS 발송 메시지 &nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="auth_msg" class="form-control form-control-sm" value="{{@$auth_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('auth_msg','[[SHOP_NAME]]인증번호 [[AUTH_NO]]를 입력해주세요.');">[[SHOP_NAME]]인증번호 [[AUTH_NO]]를 입력해주세요.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>회원 가입 축하 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="join_yn" value="Y" @if ($join_yn == 'Y') checked @endif/> 발송함</label>
                                                                    <label><input type="radio" name="join_yn" value="N" @if ($join_yn == 'N') checked @endif/> 발송안함</label>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="join_msg" class="form-control form-control-sm" value="{{@$join_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('join_msg','[[SHOP_NAME]][USER_NAME] 회원님의 가입을 진심으로 축하드립니다.');">[[SHOP_NAME]][USER_NAME] 회원님의 가입을 진심으로 축하드립니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>비밀번호 찾기 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="passwd_yn" value="Y" @if ($passwd_yn == 'Y') checked @endif/> 발송함</label>
                                                                    <label><input type="radio" name="passwd_yn" value="N" @if ($passwd_yn == 'N') checked @endif/> 발송안함</label>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="passwd_msg" class="form-control form-control-sm" value="{{@$passwd_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('passwd_msg','[[SHOP_NAME]][USER_NAME] 회원님의 비밀번호는 [PASSWD] 입니다.');">[[SHOP_NAME]][USER_NAME] 회원님의 비밀번호는 [PASSWD] 입니다.</div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <th rowspan="2"> 주문 완료 시 SMS 발송</th>
                                                                <td rowspan="2" style="text-align:center;">
                                                                    <label><input type="radio" name="order_yn" value="Y" @if ($order_yn == 'Y') checked @endif/> 발송함</label>
                                                                    <label><input type="radio" name="order_yn" value="N" @if ($order_yn == 'N') checked @endif/> 발송안함</label>
                                                                </td>
                                                                <td>결제완료 상태</td>
                                                                <td>
                                                                    <input type="text" name="order_msg_pay" class="form-control form-control-sm" value="{{@$order_msg_pay}}" style="width: 100%;"/>
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('order_msg_pay','[[SHOP_NAME]][USER_NAME] 고객님의 주문이 접수되었습니다.([ORDER_NO])');">[[SHOP_NAME]][USER_NAME] 고객님의 주문이 접수되었습니다.([ORDER_NO])</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>미결제 상태</td>
                                                                <td>
                                                                    <input type="text" name="order_msg_not_pay" class="form-control form-control-sm" value="{{@$order_msg_not_pay}}" style="width: 100%;"/>
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('order_msg_not_pay','[[SHOP_NAME]]입금계좌:[BANK] [ACCOUNT] 예금주:[DEPOSITOR] [ORDER_AMT]원');">[[SHOP_NAME]]입금계좌: [BANK] [ACCOUNT] 예금주:[DEPOSITOR] [ORDER_AMT]원</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>결제 완료 시 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="payment_yn" value="Y" @if ($payment_yn == 'Y') checked @endif /> 발송함</label>
                                                                    <label><input type="radio" name="payment_yn" value="N"  @if ($payment_yn == 'N') checked @endif /> 발송안함</label>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="payment_msg"  class="form-control form-control-sm" value="{{@$payment_msg}}" style="width: 100%;"/>
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('payment_msg','[[SHOP_NAME]]입금이 확인되었습니다. 감사합니다.');">[[SHOP_NAME]]입금이 확인되었습니다. 감사합니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>출고 완료 시 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="delivery_yn" value="Y" @if ($delivery_yn == 'Y') checked @endif /> 발송함</label>
                                                                    <label><input type="radio" name="delivery_yn" value="N" @if ($delivery_yn == 'N') checked @endif /> 발송안함</label>
                                                                </td>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="delivery_msg" class="form-control form-control-sm" value="{{@$delivery_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('delivery_msg','[[SHOP_NAME]][GOODS_NAME]..발송완료 [DELIVERY_NAME]([DELIVERY_NO])');">[[SHOP_NAME]][GOODS_NAME]..발송완료 [DELIVERY_NAME]([DELIVERY_NO])</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th rowspan="2">환불 완료 시 SMS 발송</th>
                                                                <td rowspan="2" style="text-align:center;">
                                                                    <label><input type="radio" name="refund_yn" value="Y" @if ($refund_yn == 'Y') checked @endif /> 발송함</label>
                                                                    <label><input type="radio" name="refund_yn" value="N" @if ($refund_yn == 'N') checked @endif /> 발송안함</label>
                                                                </td>
                                                                <td>환불</td>
                                                                <td>
                                                                    <input type="text" name="refund_msg_complete" class="form-control form-control-sm" value="{{@$refund_msg_complete}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('refund_msg_complete','[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO] 주문건 환불처리되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO] 주문건 환불처리되었습니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>승인취소</td>
                                                                <td>
                                                                    <input type="text" name="refund_msg_cancel" class="form-control form-control-sm" value="{{@$refund_msg_cancel}}" style="width: 100%;"/>
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('refund_msg_cancel','[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO] 주문건 승인취소되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO] 주문건 승인취소되었습니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th rowspan="3" >주문 취소 시 SMS 발송</th>
                                                                <td rowspan="3" style="text-align:center;">
                                                                    <label><input type="radio" name="cancel_yn" value="Y" @if ($cancel_yn == 'Y') checked @endif /> 발송함</label>
                                                                    <label><input type="radio" name="cancel_yn" value="N" @if ($cancel_yn == 'N') checked @endif/> 발송안함</label>
                                                                </td>
                                                                <td>무통장&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="cancel_msg_bank"  class="form-control form-control-sm" value="{{@$cancel_msg_bank}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('cancel_msg_bank','[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO]주문건 취소 처리되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO]주문건 취소 처리되었습니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>카드&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="cancel_msg_card" class="form-control form-control-sm" value="{{@$cancel_msg_card}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('cancel_msg_card','[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO]주문건 승인취소되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO]주문건 승인취소되었습니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>계좌이체&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="cancel_msg_transfer" class="form-control form-control-sm" value="{{@$cancel_msg_transfer}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('cancel_msg_transfer','[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO]주문건 환불완료 처리되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님 [ORDER_NO]주문건 환불완료 처리되었습니다.</div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <th>품절 시 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="out_of_stock_yn" value="Y" @if ($out_of_stock_yn == 'Y') checked @endif /> 발송함</label>
                                                                    <label><input type="radio" name="out_of_stock_yn" value="N" @if ($out_of_stock_yn == 'N') checked @endif /> 발송안함</label>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="out_of_stock_msg" class="form-control form-control-sm" value="{{@$out_of_stock_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('out_of_stock_msg','[[SHOP_NAME]]주문하신상품이 품절되었습니다.고객센터로 문의 바랍니다.');">[[SHOP_NAME]]주문하신상품이 품절되었습니다.고객센터로 문의 바랍니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>생일쿠폰 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="birth_yn" value="Y" @if ($birth_yn == 'Y') checked @endif /> 발송함</label>
                                                                    <label><input type="radio" name="birth_yn" value="N" @if ($birth_yn == 'N') checked @endif/> 발송안함</label>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="birth_msg" class="form-control form-control-sm" value="{{@$birth_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('birth_msg','[[SHOP_NAME]][USER_NAME] 고객님의 생일쿠폰이 발급되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님의 생일쿠폰이 발급되었습니다.</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>웰컴백 쿠폰 SMS 발송</th>
                                                                <td style="text-align:center;">
                                                                    <label><input type="radio" name="welcome_yn" value="Y"  @if ($welcome_yn == 'Y') checked @endif/> 발송함</label>
                                                                    <label><input type="radio" name="welcome_yn" value="N"  @if ($welcome_yn == 'N') checked @endif/> 발송안함</label>
                                                                <td>&nbsp;</td>
                                                                <td>
                                                                    <input type="text" name="welcome_msg" class="form-control form-control-sm" value="{{@$welcome_msg}}" style="width: 100%;" />
                                                                    <div style="color:gray;" style="cursor: pointer;" onclick="applySMSMsg('welcome_msg','[[SHOP_NAME]][USER_NAME] 고객님의 웰컴백쿠폰이 발급되었습니다.');">[[SHOP_NAME]][USER_NAME] 고객님 웰컴백쿠폰이 발급되었습니다.</div>
                                                                </td>
                                                            </tr>
                                                        </div>
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
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('sms');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="tab-pane fade" id="stock_reduction" role="tabpanel" aria-labelledby="stock_reduction-tab">
                <form name="stock_reduction" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">기능 설정</h5>
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
                                                            <th width="200px">최초 출력 메뉴</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type="text" name="init_url" class="form-control form-control-sm" value="{{@$init_url}}" style="width: 100%;" />
                                                                </div>
                                                                <div style="color:gray;">* 관리자 로그인 후 최초로 출력할 기본 메뉴를 지정합니다. 최근에 사용한 메뉴 내역이 있는 경우에는 최근 사용 메뉴가 자동으로 출력됩니다. </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>인증서버(HTTPS) 사용 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="ssl_yn" id="ssl_y" class="custom-control-input" value="Y" @if ($ssl_yn == 'Y') checked @endif />
                                                                        <label class="custom-control-label" for="ssl_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="ssl_yn" id="ssl_n" class="custom-control-input" value="N" @if ($ssl_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="ssl_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 보안 인증서를 사용할 수 있는 경우 "사용함"으로 설정하시기 바랍니다. </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>도매기능 사용 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="wholesale_yn" id="wholesale_y" class="custom-control-input" value="Y" @if ($wholesale_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="wholesale_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="wholesale_yn" id="wholesale_n" class="custom-control-input" value="N" @if ($wholesale_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="wholesale_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* "<a href="/head/member/mem03">회원/CRM > 회원그룹관리</a>" 메뉴에서 도매 회원 그룹을 생성할 수 있습니다. </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상품평 승인 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="est_confirm_yn" id="est_confirm_y" class="custom-control-input" value="Y" @if ($est_confirm_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="est_confirm_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="est_confirm_yn" id="est_confirm_yn" class="custom-control-input" value="N" @if ($est_confirm_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="est_confirm_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 고객이 상품평 등록한 경우, 관리자의 승인 후 상품평 리스트에 노출하는 기능을 사용합니다. "사용안함"을 선택한 경우 상품평 등록 시 자동으로 노출됩니다. </div>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">출력 설정</h5>
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
                                                            <th width="200px">신상품 기간</th>
                                                            <td width="35%">
                                                                <input type="text" class="form-control form-control-sm" name="new_good_day" value="{{@$new_good_day}}" maxlength="3" style="width: 100px; text-align: right;display:inline">일
                                                            </td>
                                                            <th>새글 기간</th>
                                                            <td width="35%">
                                                                <input type="text" class="form-control form-control-sm" name="new_data_day" value="{{@$new_data_day}}" maxlength="3" style="width: 100px; text-align: right;display:inline" />일
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>카테고리 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="category_goods_cnt" value="{{@$category_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>신상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="newarrival_goods_cnt" value="{{@$newarrival_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>세일 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="onsale_goods_cnt" value="{{@$onsale_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>브랜드샵 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="brandshop_goods_cnt" value="{{@$brandshop_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>베스트랭킹 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="best_rank_goods_cnt" value="{{@$best_rank_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>관련 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="relative_goods_cnt" value="{{@$relative_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>검색 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="search_goods_cnt" value="{{@$search_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>검색 상품 정렬</th>
                                                            <td>
                                                                <select name="search_goods_sort" class="form-control form-control-sm"style="width:100px;">
                                                                    <option value="pop">판매량순</option>
                                                                    <option value="new">신상품순</option>/
                                                                    <option value="name_low">상품명순</option>
                                                                    <option value="price_low">낮은가격순</option>
                                                                    <option value="emt_high">상품평수순</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">메일 답변</h5>
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
                                                        <tr style="height:30px">
                                                            <td width="15%" style="text-align:center;font-weight: bold;"><label>항목</label></td>
                                                            <td width="15%" style="text-align:center;font-weight: bold;">설정</td>
                                                            <td style="text-align:center;font-weight: bold;">메일 HTML</td>
                                                        </tr>
                                                        <tr>
                                                            <th>1:1문의 답변 시 메일 발송</th>
                                                            <td width="15%">
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="counsel_yn" id="counsel_y" class="custom-control-input" value="Y"  @if ($counsel_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="counsel_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="counsel_yn" id="counsel_n" class="custom-control-input" value="N" @if ($counsel_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="counsel_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div style="color:gray;">* 1:1문의 답변 시 발송되는 메일의 스킨파일("front/{$theme}/skin/email/councel.html")은 변경/수정이 가능합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>상품 문의 답변시 메일 발송</th>
                                                            <td width="15%">
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="goods_qa_yn" id="goods_qa_y" class="custom-control-input" value="Y" @if ($goods_qa_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="goods_qa_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="goods_qa_yn" id="goods_qa_n" class="custom-control-input" value="N" @if ($goods_qa_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="goods_qa_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div style="color:gray;">* 상품 문의 답변 시 발송되는 메일의 스킨파일("front/{$theme}/skin/email/goods_qa.html")은 변경/수정이 가능합니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">휴먼 회원 설정</h5>
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
                                                            <th>휴면회원 설정 여부</th>
                                                            <td width="15%">
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="member_inactive_yn" id="member_inactive_y" class="custom-control-input" value="Y" @if ($member_inactive_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="member_inactive_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="member_inactive_yn" id="member_inactive_n" class="custom-control-input" value="N" @if ($member_inactive_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="member_inactive_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div style="color:gray;">* 1년간 접속하지 않은 회원을 휴면회원으로 전환시킵니다.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('stock_reduction');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>  

            </div>
            <div class="tab-pane fade" id="list_count" role="tabpanel" aria-labelledby="list_count-tab">
                <form name="list_count" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">쇼핑몰메인 출력개수</h5>
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
                                                            <th >쇼핑몰 메인 공지사항</td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="main_notice" value="{{@$main_notice}}" maxlength="2" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <td width="15%" style="text-align: left;"><label>&nbsp;</label></td>
                                                            <td width="35%">
                                                                &nbsp;
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>

                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">커뮤니티 출력개수</h5>
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
                                                            <th>커뮤니티 메인 공지사항</th>
                                                            <td>
                                                                <input type="text"class="form-control form-control-sm" name="community_main_notice" value="{{@$community_main_notice}}" maxlength="2" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>커뮤니티 메인 상품문의</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="community_main_qa" value="{{@$community_main_qa}}" maxlength="2" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>커뮤니티 메인 상품평</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="community_main_review" value="{{@$community_main_review}}" maxlength="2" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <td width="15%" style="text-align: left;"><label>&nbsp;</label></td>
                                                            <td>
                                                                &nbsp;
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>커뮤니티 상품문의 출력개수</th>
                                                            <td width="35%">
                                                                <input type="text" class="form-control form-control-sm" name="community_goods_qa" value="{{@$community_goods_qa}}" maxlength="2" style="width: 100px; text-align: right;display:inline" />개
                                                            </td>
                                                            <th>커뮤니티 상품평 출력개수</th>
                                                            <td width="35%">
                                                                <input type="text" class="form-control form-control-sm" name="community_goods_review" value="{{@$community_goods_review}}" maxlength="2" style="width: 100px; text-align: right;display:inline" />개
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>

                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">고객센터 출력개수</h5>
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
                                                            <th>공지사항 리스트</th>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="notice" value="{{@$notice}}" maxlength="2" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <td width="15%" style="text-align: left;"><label>&nbsp;</label></td>
                                                            <td width="35%">
                                                                &nbsp;
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('list_count');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                <form name="admin" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">샵링커</h5>
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
                                                            <th>로그인 아이디</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='shoplinker_id' value="{{@$shoplinker_id}}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>고객사 코드</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='shoplinker_user_id' value='{{@$shoplinker_user_id}}'>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">사방넷</h5>
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
                                                            <th>아이디</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='sabangnet_id' value="{{@$sabangnet_id}}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>인증키</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='sabangnet_key' value='{{@$sabangnet_key}}'>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">I_PIN</h5>
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
                                                            <th>아이핀 아이디</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='ipin_site_cd' value="{{@$ipin_site_cd}}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>아이핀 비밀번호</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='ipin_site_pw' value='{{@$ipin_site_pw}}'>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>아이핀 위변조 방지값</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='ipin_site_req' value='{{@$ipin_site_req}}'>
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
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('admin');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="mobile" role="tabpanel" aria-labelledby="mobile-tab">
                <form name="mobile" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                            <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">모바일</h5>
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
                                                            <th>도메인</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='m_domain' value="{{$m_domain}}">
                                                                    <div style="color:gray;">* 모바일 상점 도메인을 입력해 주십시오. "http://"는 생략합니다.  EX)m.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">출력 설정</h5>
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
                                                            <th width="200px">카테고리 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="m_category_goods_cnt" value="{{@$m_category_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>신상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="m_newarrival_goods_cnt" value="{{@$m_newarrival_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>세일 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="m_onsale_goods_cnt" value="{{@$m_onsale_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>브랜드샵 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="m_brandshop_goods_cnt" value="{{@$m_brandshop_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>


                                                        <tr>
                                                            <th>베스트랭킹 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="m_best_rank_goods_cnt" value="{{@$m_best_rank_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                            <th>검색 상품 출력 갯수</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="m_search_goods_cnt" value="{{@$m_search_goods_cnt}}" maxlength="3" style="width: 100px;text-align:right;display:inline" />개
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th>앱 메인 롤링 배너</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" style="width:100%" name='app_main_banner_1' value="{{@$app_main_banner_1}}"/>
                                                            </td>
                                                            <th>앱 메인 기획전 배너</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" style="width:100%" name='app_main_banner_2' value="{{@$app_main_banner_2}}"/>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <th>앱 메인 HOT PRODUCT</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name='app_main_section_1' value="{{@$app_main_section_1}}"/>
                                                            </td>
                                                            <th>앱 메인 MD PICK</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name='app_main_section_2' value="{{@$app_main_section_2}}"/>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            
                                                            <th>앱 메인 STEDY ITEM</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name='app_main_section_3' value="{{@$app_main_section_3}}"/>
                                                            </td>
                                                            <td width="15%" style="text-align: left;"><label>&nbsp;</label></td>
                                                            <td>&nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('mobile');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="image" role="tabpanel" aria-labelledby="image-tab">
                <form name="image" method="post">
                    <div class="card_wrap aco_card_wrap">
                        <div class="card shadow">
                            <div class="card-body mt-1">
                            <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">이미지 서버</h5>
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
                                                            <th>이미지서버 사용 여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="image_yn" id="image_y" class="custom-control-input" value="Y" @if ($image_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="image_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="image_yn" id="image_n" class="custom-control-input" value="N" @if ($image_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="image_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 별도의 이미지서버를 사용하는 경우에 "사용함"으로 설정하시기 바랍니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>이미지 도메인</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <input type='text' class="form-control form-control-sm w-100" name='i_domain' value="{{@$i_domain}}">
                                                                    <div style="color:gray;">* 별도의 이미지서버를 사용하는 경우, "http://"를 제외한 이미지 도메인을 입력해주십시오.  EX)image.domain.co.kr</div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                <div class="card-header mb-0">
                                    <h5 class="m-0 font-weight-bold">이미지 자동 전송(FTP)</h5>
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
                                                            <th>자동 전송 사용여부</th>
                                                            <td>
                                                                <div class="flax_box">
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="ftp_yn" id="ftp_y" class="custom-control-input" value="Y" @if ($ftp_yn == 'Y') checked @endif/>
                                                                        <label class="custom-control-label" for="ftp_y">사용함</label>
                                                                    </div>&nbsp;&nbsp;&nbsp;
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" name="ftp_yn" id="ftp_n" class="custom-control-input" value="N" @if ($ftp_yn == 'N') checked @endif/>
                                                                        <label class="custom-control-label" for="ftp_n">사용안함</label>
                                                                    </div>
                                                                </div>
                                                                <div style="color:gray;">* 이미지 파일이 업로드 될 때 별도의 이미지 서버에 FTP 전송을 자동으로 수행합니다.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>FTP 주소</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="hostname" value="{{@$hostname}}"/>
                                                                <div style="color:gray;">* 이미지(FTP) 서버의 도메인 또는 IP를 입력하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>사용자 ID</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name="username" value="{{@$username}}"/>
                                                                <div style="color:gray;">* 이미지(FTP) 서버에 접속할 수 있는 아이디를 입력하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>비밀번호</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" style="width:100%" name='password' value="{{@$password}}"/>
                                                                <div style="color:gray;">* 이미지(FTP) 서버에 접속할 수 있는 비밀번호를 입력하십시오.</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>디렉토리</th>
                                                            <td>
                                                                <input type="text"  class="form-control form-control-sm" name='home_dir' value="{{@$home_dir}}"/>
                                                                <div style="color:gray;">* 이미지(FTP) 서버의 초기 디렉토리를 입력하십시오.</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><br><br>
                                
                                <div style="text-align:center;">
                                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateData('image');" id="saveData"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>



            </div>
            <!-- <div class="tab-pane fade" id="naver_checkout" role="tabpanel" aria-labelledby="naver_checkout-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="coupon" role="tabpanel" aria-labelledby="coupon-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="pay" role="tabpanel" aria-labelledby="pay-tab">.ㅇㅇ</div> -->
            <!-- <div class="tab-pane fade" id="stock" role="tabpanel" aria-labelledby="stock-tab">.ㅇㅇ</div> -->
        </div>
    </div>
</div>

<script>

    $(document).ready(function(){
        $('#shop-tab').trigger("click");  
    }); 


    function updateData(type) {
        let frm = $('form[name='+type+']').serialize();
        
        frm += "&type="+ type;
        
        console.log(frm);
    
        $.ajax({
            method: 'post',
            url: '/head/system/sys05/update',
            data: frm,
            dataType: 'json',
            success: function(data) {
                if (data.code == '200') {
                    alert('정보 수정에 성공하였습니다.');
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

    $(document).ready(function(){
       if ($('#policy_g').is(':checked')) {
            $("input[name='ratio']").attr('readonly', true);
       }
        $("#policy_s").on('click',function(){
            $("input[name='ratio']").attr('readonly', false);
        });
    
        $("#policy_g").on('click',function(){
            $("input[name='ratio']").attr('readonly', true);
        });
    });


</script>
   
@stop
