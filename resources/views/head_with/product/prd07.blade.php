@extends('head_with.layouts.layout-nav')
@section('title','상품관리 - 일괄등록')
@section('content')
<style>
    .wrap { overflow-y: 'hidden' }
    .wrap legend {
        position: relative;
        width: auto;
        height: auto;
        line-height: 1;
        left: 0;
        overflow: visible;
        z-index: 1;
        font-size: 18px;
        color: blue;
        font-weight: 500;
        margin-bottom: 0px;
        cursor: pointer;
    }

    .wrap .helpContent {
        margin-top: 12px;
    }
    .wrap .FSHelp ul {
        padding-top: 8px;
    }
    .wrap .FSHelp ul, .wrap .FSHelp li {
        list-style: square;
        margin-left: 12px;
        margin-bottom: 8px;
        font-size: 13px;
    }
    .wrap strong {
        font-weight: bold;
    }

    .disabled {
        background: #ccc;
    }

    .required::after {
        position: relative;
        top: 3px;
    }

</style>
<div class="py-3 px-sm-3 wrap">
    <div class="page_tit">
        <h3 class="d-inline-flex">상품관리 - 일괄등록</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 일괄등록 {{$goods_nos}} </span>
        </div>
    </div>
    <form method="get" name="f1">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>일괄 등록 품목</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <div class="form-group">
                                <label for="prd_cnt" class="required" style="color: blue; font-weight: 600;">상품수</label>
                                <div class="flex_box">
                                    <input type="text" name="prd_cnt" id="prd_cnt" class="form-control form-control-sm mr-2" style="width:40px;" onkeydown="onlyNum(this);"/>
                                    <span>개</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item" class="required">업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm w-100" readonly disabled>
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <input type='hidden' name='com_cd' id='com_cd' value=''>
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%;">
                                            <a href="#" class="btn btn-sm btn-outline-primary" onclick="customSearchCompany()">
                                                <i class="bx bx-dots-horizontal-rounded fs-16"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item" class="required">품목</label>
                                <div class="flax_box">
                                    <select id="item" name="item" class="form-control form-control-sm">
                                        <option value="">==품목==</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="brand_cd" class="required">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="rep_cat_cd">대표카테고리</label>
                                <div class="form-inline inline_btn_box">
                                    <input type="hidden" id="rep_cat_cd" name="rep_cat_cd" value=""/>
                                    <input class="w-100 form-control form-control-sm" id="rep_cat_nm" name="rep_cat_nm" value="" placeholder="변경할 카테고리 선택" readonly/>
                                    <a href="#" id="cate_choice" onclick="popCategory('display');" class="btn btn-sm btn-outline-primary ml-2"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="u_cat_cd">용도카테고리</label>
                                <div class="form-inline inline_btn_box">
                                    <input type="hidden" id="u_cat_cd" name="u_cat_cd" value=""/>
                                    <input class="w-100 form-control form-control-sm" id="u_cat_nm" name="u_cat_nm" value="" placeholder="변경할 카테고리 선택" readonly/>
                                    <a href="#" id="cate_choice" onclick="popCategory('item');" class="btn btn-sm btn-outline-primary ml-2"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="dlv_pay_type" class="required">배송비 지불</label>
                                <div class="flex_box form-radio-box">
                                    <div class="custom-control custom-radio mr-2">
                                        <input type="radio" name="dlv_pay_type" value="P" id="dlv_pay_type_1" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="dlv_pay_type_1">선불</label>
                                    </div>
                                    <div class="custom-control custom-radio mr-2">
                                        <input type="radio" name="dlv_pay_type" value="F" id="dlv_pay_type_2" class="custom-control-input">
                                        <label class="custom-control-label" for="dlv_pay_type_2">착불</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="" class="required">배송비</label>
                                <div class="flex_box">
                                    <div class="form-radio-box">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="dlv_fee_cfg_s" name="dlv_fee_cfg" value="S" onchange="changeDeliveryConfig(this)" checked/>
                                            <label class="custom-control-label" for="dlv_fee_cfg_s" style="justify-content:left">쇼핑몰 설정(S)</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="dlv_fee_cfg_g" name="dlv_fee_cfg" value="G" onchange="changeDeliveryConfig(this)"/>
                                            <label class="custom-control-label" for="dlv_fee_cfg_g">상품 개별 설정(G)</label>
                                        </div>
                                    </div>
                                    <span class="mt-2 mb-2">배송비 {{$dlv_fee}}원 ( {{$free_dlv_fee_limit}}원 이상 무료)</span>
                                    <span class="mx-2 mt-1">
                                        <select name="dlv_fee_yn" id="dlv_fee_yn" class="form-control form-control-sm" style="width: 120px" disabled>
                                            @foreach ($dlv_fee_yn as $key => $val)
                                                <option value='{{ $key }}' <?=$key == 'Y' ? 'selected' : null?>>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </span>
                                    <input type='text' class="form-control form-control-sm mr-2 mt-1" style="width:100px" id="baesong_price" name='baesong_price' value='' readonly disabled/>원
                                    <span id="point_calculated" style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="point" class="required">적립금</label>
                                <div class="flex_box">
                                    <div class="form-radio-box">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="point_cfg_s" name="point_cfg" value="S" onchange="changePointConfig(this)" checked/>
                                            <label class="custom-control-label" for="point_cfg_s" style="justify-content:left">쇼핑몰 설정(S)</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="point_cfg_g" name="point_cfg" value="G" onchange="changePointConfig(this)" />
                                            <label class="custom-control-label" for="point_cfg_g">상품 개별 설정(G)</label>
                                        </div>
                                    </div>
                                    <span class="mt-2 mb-2">지급함. 상품 가격의 {{$order_point_ratio}}% 적립금 지급</span>
                                    <input type="hidden" id="point_shop_ratio" name="point_shop_ratio" value="<?=$order_point_ratio?>"/>
                                    <span class="ml-1 mt-1">
                                        <select name="point_yn" id="point_yn" class="form-control form-control-sm" style="width: 110px" disabled>
                                            @foreach ($point_yn as $key => $val)
                                                <option value='{{ $key }}' <?=$key == 'Y' ? 'selected' : null?>>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </span>
                                    <span class="mx-2 mt-1">
                                        <select name="point_unit" id="point_unit" class="form-control form-control-sm" style="width: 110px" disabled onchange="changePointUnit(this)">
                                            @foreach ($point_unit as $key => $val)
                                                <option value='{{ $key }}' <?=$key == 'W' ? 'selected' : null?>>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </span>
                                    <input type='text' class="form-control form-control-sm mr-2 mt-1" style="width:100px" id="point" name="point" value='' disabled/>
                                    <span id="point_unit_str">원</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="org_nm">원산지</label>
                                <div class="flex_box">
                                    <input type='text' class="form-control form-control-sm" id="org_nm" name='org_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="make">제조사</label>
                                <div class="flex_box">
                                    <input type='text' class="form-control form-control-sm" id="make" name='make' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="dlv_pay_type" class="required">옵션사용</label>
                                <div class="form-inline form-radio-box flex_box txt_box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="is_option_use" value="Y" id="is_option_use1" class="custom-control-input" onchange="changeOptionUse(this)" checked>
                                        <label class="custom-control-label" for="is_option_use1">사용함</label>
                                    </div>
                                    <div class="custom-control custom-radio mr-2">
                                        <input type="radio" name="is_option_use" value="N" id="is_option_use2" class="custom-control-input" onchange="changeOptionUse(this)">
                                        <label class="custom-control-label" for="is_option_use2">사용안함</label>
                                    </div>
                                    <div style="margin-top:2px;">
                                        <x-tool-tip>
                                            <x-slot name="arrow">top</x-slot>
                                            <x-slot name="align">left</x-slot>
                                            <x-slot name="html">
                                                옵션 사용 항목을 변경하면, 등록된 모든 재고 수량 정보가 삭제됩니다.
                                            </x-slot>
                                        </x-tool-tip>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="check_option_kind" class="required">옵션구분</label>
                                <div class="form-inline inline_input_box flex_box">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" id="option_kind" name="option_kind" value="" />
                                        <input type="checkbox" name="chk_option_kind1" id="chk_option_kind1" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="chk_option_kind1">&nbsp;</label>
                                    </div>
                                    <input type="text" class="form-control form-control-sm mr-2" id="option_kind1" name="option_kind1" value="사이즈" onkeyup="checkOptionValue(this);" style="width: 100px"/>
                                    <span class="txt_box mr-2">~</span>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="chk_option_kind2" id="chk_option_kind2" class="custom-control-input" checked/>
                                        <label class="custom-control-label" for="chk_option_kind2">&nbsp;</label>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" id="option_kind2" name="option_kind2" value="컬러" onkeyup="checkOptionValue(this);" style="width: 100px"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="tax_yn" class="required">과세구분</label>
                                <div class="flex_box">
                                    <select id="tax_yn" name="tax_yn" class="form-control form-control-sm">
                                            <option value=''>==과세 구분==</option>
                                            <option value='Y' selected>과세</option>
                                            <option value='N'>면세</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="is_unlimited" class="required">재고 수량 관리</label>
                                <div class="flex_box form-inline form-radio-box">
                                    <div class="custom-control custom-radio mr-2">
                                        <input type="radio" name="is_unlimited" value="N" id="is_unlimited1" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="is_unlimited1">수량 관리함</label>
                                    </div>
                                    <div class="custom-control custom-radio mr-2">
                                        <input type="radio" name="is_unlimited" value="Y" id="is_unlimited2" class="custom-control-input">
                                        <label class="custom-control-label" for="is_unlimited2">수량 관리 안함 (무한재고)</label>
                                    </div>
                                    <div style="margin-top:1px;">
                                        <x-tool-tip>
                                            <x-slot name="arrow">top</x-slot>
                                            <x-slot name="align">left</x-slot>
                                            <x-slot name="html">매입상품은 무한재고 기능을 사용할 수 없습니다.</x-slot>
                                        </x-tool-tip>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="restock_yn">재입고알림</label>
                                <div class="flex_box form-inline txt_box">
                                    <div class="custom-control custom-checkbox form-check-box mr-2">
                                        <input type="checkbox" class="custom-control-input" value="Y" id="restock_yn" checked>
                                        <label class="custom-control-label" for="restock_yn">재 입고함</label>
                                    </div>
                                    <x-tool-tip>
                                        <x-slot name="arrow">top</x-slot>
                                        <x-slot name="align">left</x-slot>
                                        <x-slot name="html">
                                            품절 시 "<strong>재입고알림</strong>" 버튼이 노출됩니다.
                                        </x-slot>
                                    </x-tool-tip>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="resul_btn_wrap mt-3 d-block">
                        <a href="#" class="btn btn-sm btn-primary submit-btn" onclick="commander('apply');">적용</a>
                        <a href="#" class="btn btn-sm btn-secondary" onclick="commander('clear');">취소</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <fieldset class="FSHelp">
                    <legend class="Tip" onclick="return document.querySelector('.helpContent').classList.toggle('d-none')">Help(클릭해 주세요)</legend>
                    <ul class="helpContent d-none">
                        <li>
                            <span style="color:#ff0000;">취소</span> 클릭시 상품 및 일괄 등록 폼이 
                            <span style="color:#ff0000;">초기화</span> 됩니다.
                        </li>
                        <li>
                            수정할수 있는 항목: <span style="color:#ff0000;">스타일넘버, 상품명, 시중가, 판매가, 홍보글/단축명, 상품상세, 제품사양, 예약/배송, MD상품평, 상품위치, 상품태그</span>
                            (등록하려는 엑셀 파일 필드 <span style="color:#ff0000;">Ctrl+C, Ctrl+V</span>)
                        </li>
                        <li>
                            <span style="color:#ff0000;">저장</span> 클릭시 상품 
                            <span style="color:#ff0000;">선택</span>(체크박스 체크)과 무관하게 일괄 등록 됩니다.
                        </li>
                        <li style="margin:7 0 0 0">
                            <span style="color:blue;font-weight:bold;">옵션 등록 도움말 </span>
                            <ul style="margin:5 0 0 0;">
                                <li>단일 옵션 입력 시 <span style="color:#ff0000;font-weight:bold;">"옵션구분"</span> 입력란에 <span style="color:blue;">사이즈</span> 또는 <span style="color:blue;">size</span> 과 같이 옵션구분명을 입력합니다. <span style="color:#ff0000;font-weight:bold;">"옵션1"</span> 모두 필수 입력.</li>
                                <li>멀티 옵션 입력 시 <span style="color:#ff0000;font-weight:bold;">"옵션구분"</span> 입력란에 <span style="color:blue;">사이즈^컬러</span> 또는 <span style="color:blue;">size^color</span>과  같이 <span style="color:#ff0000;font-weight:bold;">공백</span> 없이 <span style="color:#ff0000;font-weight:bold;">"^"</span>로 연결하여 옵션구분명을 입력합니다. <span style="color:#ff0000;font-weight:bold;">"옵션1", "옵션2"</span> 모두 필수 입력.</li>
                                <li style="margin:5 0 0 0;"><span style="color:#ff0000;font-weight:bold;">단일 옵션 입력 시</span> : <span style="color:blue;">S,M,L</span> 또는 <span style="color:blue;">검정,파랑,노랑,초록</span> 과 같이 <span style="color:#ff0000;font-weight:bold;">공백</span> 없이 쉼표(,)로 연결하여 <strong>"옵션1"</strong> 항목에 입력합니다. </li>
                                <li style="margin:5 0 0 0;"><span style="color:#ff0000;font-weight:bold;">멀티 옵션 입력 시</span> : <strong>"옵션1"</strong> 항목에 <span style="color:blue;">검정,파랑,노랑,초록</span>,  <strong>"옵션2"</strong> 항목에 <span style="color:blue;">S,M,L</span> 와 같이 <span style="color:#ff0000;font-weight:bold;">공백</span> 없이 쉼표(,)로 연결하여 입력합니다. </li>
                                <li style="margin:5 0 0 0;"><strong>"옵션1", "옵션2" 항목</strong>에 입력된 옵션은 "검정^S","검정^M","검정^L","파랑^S", .. 와 같은 형태로 옵션이 등록되며, 쇼핑몰에서는 멀티옵션으로 표시됩니다.</li>
                                <li style="margin:5 0 0 0;"><span style="color:#ff0000;font-weight:bold;">수량 입력 시</span> : <span style="color:blue;">100,200,300</span> 또는 <span style="color:blue;">0,0,300</span>과 같이 <span style="color:#ff0000;font-weight:bold;">공백</span> 없이 쉼표(,)로 연결하여 <strong>"수량"</strong> 항목에 입력합니다. <strong>"옵션1"</strong> 항목을 기준으로 적용되므로 <strong>"옵션1"</strong> 항목의 갯수와 <strong>"수량"</strong> 항목의 갯수는 같아야 합니다.</li>
                                <li style="margin:5 0 0 0;"><span style="color:#ff0000;font-weight:bold;">옵션 가격 입력 시</span> : <span style="color:blue;">100,200,300</span> 또는 <span style="color:blue;">0,0,300</span>과 같이 <span style="color:#ff0000;font-weight:bold;">공백</span> 없이 쉼표(,)로 연결하여 <strong>"옵션가격"</strong> 항목에 입력합니다. <strong>"옵션1"</strong> 항목을 기준으로 적용되므로 <strong>"옵션1"</strong> 항목의 갯수와 <strong>"옵션가격"</strong> 항목의 갯수는 같아야 합니다.</li>
                            </ul>
                        </li>
                        <li style="margin:7 0 0 0">
                            <span style="color:blue;font-weight:bold;">옵션등록 샘플</span>
                            <ul style="margin:5 0 0 0;">
                                <li>사이즈 또는 컬러 선택시 <strong><a href="/sample/single_option.jpg" target="_blank">single_option.jpg</a></strong></li>
                                <li>컬러/사이즈 선택시 <strong><a href="/sample/multi_option.jpg" target="_blank">multi_option.jpg</a></strong></li>
                            </ul>
                        </li>
                        <li style="margin:7 0 0 0">
                            <span style="color:blue;font-weight:bold;">태그 등록</span>
                            <ul style="margin:5 0 0 0;">
                                <li style="margin:5 0 0 0;"><span style="color:#ff0000;font-weight:bold;">태그 등록 시</span> : <span style="color:blue;">나이키,신발,운동화</span> 와 같이 <span style="color:#ff0000;font-weight:bold;">공백</span> 없이 쉼표(,)로 연결하여 <strong>"상품태그"</strong> 항목에 입력합니다. </li>
                            </ul>
                        </li>
                    </ul>
                </fieldset>
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mt-3 mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6> 
                        </div>
                        <div class="fr_box">
                            <div class="flex_box">
                                <!-- <a href="#" onclick="commander('remove');" class="btn btn-sm btn-primary shadow-sm mr-1">삭제</a> -->
                                <a href="#" onclick="commander('save');" class="btn btn-sm btn-primary shadow-sm mr-1">저장</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="min-height: 300px; height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>
    <script type="text/javascript" charset="utf-8">

        /**
         * ag-grid defines
         */
        const DEFAULT_STYLE = { 'background' : 'none', 'line-height': '30px'};

        const CELL_STYLE = {
            EDIT: { 'background': '#ffff99', 'line-height': '30px'},
            OK: { 'background': 'rgb(200,200,255)' },
            FAIL: { 'background': 'rgb(255,200,200)' }
        };

        var columns= [
            // {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, sort: null, pinned:'left', hide:true},
            {field: "msg", headerName:"처리", pinned:'left', width:100, cellStyle: (params) => resultStyle(params)},
            {field: "com_id", headerName: "업체", width:100, pinned: 'left'},
            {field: "opt_kind_cd", headerName: "품목", width: 100, pinned: 'left'},
            {field: "brand", headerName: "브랜드", pinned: 'left'},
            {field: "rep_cat_cd", headerName: "대표카테고리", width: 100, pinned: 'left'},
            {field: "u_cat_cd", headerName: "용도카테고리", width: 100, pinned: 'left'},
            {field: "style_no", headerName: "스타일넘버", width: 120, pinned: 'left', editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "goods_nm", headerName: "상품명", width: 230, pinned: 'left', editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "goods_nm_eng", headerName: "상품영문명", width: 230, editable: true, cellStyle: CELL_STYLE.EDIT},
            {headerName:"가격", editable: true, cellStyle: CELL_STYLE.EDIT,
                children: [
                    {field: "goods_sh", headerName: "시중가", type: 'currencyType', editable: true, cellStyle: CELL_STYLE.EDIT},
                    {field: "price", headerName: "판매가", type: 'currencyType', editable: true, cellStyle: CELL_STYLE.EDIT},
                    {field: "wonga", headerName: "원가", width: 60, type: 'currencyType', editable: true, cellStyle: CELL_STYLE.EDIT},
                    {field: "margin_rate", headerName: "마진율(%)", width:84, type: 'percentType'},
                ]
            },
            {headerName:"상품옵션", editable: true, cellStyle: CELL_STYLE.EDIT,
                children: [
                    {field: "option_kind", headerName: "옵션구분", width: 200},
                    {field: "opt1", headerName: "옵션1", width: 200,
                        editable: params => params.data.is_chk_opt_kind1 == true,
                        cellStyle: params => {
                            if (params.data.is_chk_opt_kind1 == true) {
                                return CELL_STYLE.EDIT;
                            }
                        }
                    },
                    {field: "opt2", headerName: "옵션2", width: 200, 
                        editable: params => params.data.is_chk_opt_kind2 == true,
                        cellStyle: params => {
                            if (params.data.is_chk_opt_kind2 == true) {
                                return CELL_STYLE.EDIT;
                            }
                        }
                    },
                    {field: "opt_qty", headerName: "수량", editable: true, cellStyle: CELL_STYLE.EDIT},
                    {field: "opt_price", headerName: "옵션가격", width: 200, editable: true, cellStyle: CELL_STYLE.EDIT},
                ]
            },
            {field: "head_desc", headerName: "상단홍보글", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "ad_desc", headerName: "하단홍보글", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "dlv_pay_type", headerName: "배송비지불"},
            {field: "dlv_fee_cfg", headerName: "배송비설정"},
            {field: "bae_yn", headerName: "배송비여부"},
            {field: "baesong_price", headerName: "배송비"},
            {headerName: "적립금",
                children: [
                    {headerName: "설정", field: "point_cfg", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "지급", field: "point_yn", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "적립", field: "point", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'},
                    {headerName: "단위", field: "point_unit", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "금액", field: "point_amt", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'}
                ]
            },
            {field: "org_nm", headerName: "원산지"},
            {field: "md_nm", headerName: "MD"},
            {field: "make", headerName: "제조사"},
            {field: "goods_cont", headerName: "상품상세", width: 240, editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "spec_desc", headerName: "제품사양", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "baesong_desc", headerName: "예약/배송", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "opinion", headerName: "MD상품평", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "is_unlimited", headerName: "무한재고여부"},
            {field: "restock_yn", headerName: "재입고알림"},
            {field: "tax_yn", headerName: "과세구분"},
            {field: "goods_location", headerName: "상품위치", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "tags", headerName: "상품태그", editable: true, cellStyle: CELL_STYLE.EDIT},
            {field: "com_type", hide: true},
            {field: "", headerName: "", width: "auto"}
        ];

        /**
         * ag-grid render
         */
        const pApp = new App('', {
            gridId: "#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(240);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            let options = {
                onCellValueChanged: params => onCellValueChanged(params),
                getRowNodeId: (data) => data.idx // 업데이터 및 제거를 위한 식별 ID 할당
            };
            gx = new HDGrid(gridDiv, columns, options);
        });

        // 판매가 원가 작성할 때 마진율값 자동입력
        async function onCellValueChanged(params) {
            if (params.oldValue == params.newValue) return;
            const row = params.data;

            if (row.price != null && row.wonga != null ) row.margin_rate = ((row.price - row.wonga)/row.price)*100;

            await gx.gridOptions.api.applyTransaction({ 
            update: [{...row}] 
        });
        }


        /**
         * ag-grid utils
         */
        const getRowNode = (row) => {
            return gx.gridOptions.api.getRowNode(row.idx);
        };

        const startEditingCell = (row_index, col_key) => {
            gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
        };

        const stopEditing = () => {
            gx.gridOptions.api.stopEditing();
        };

        const addRow = (row) => {
            gx.gridOptions.api.applyTransaction({add : [{...row}]});
        };

        const updateRow = (row) => {
            // gx.gridOptions.api.applyTransaction({update : [{...row}]});

            gx.gridOptions.api.applyTransaction({remove : [{...row}]});
            gx.gridOptions.api.applyTransaction({add : [{...row}]});
        };

        const deleteRow = async (row) => { await gx.gridOptions.api.applyTransaction({remove : [{...row}]}); };

        /**
         * utils
         */
        const _ = (selector) => {
            const result = document.querySelectorAll(selector);
            if (result == undefined) return result;
            return result.length > 1 ? result : result[0];
        };

        const onlyNum = (obj) => {
            val = obj.value;
            new_val = '';
            for (i=0; i<val.length; i++) {
                char = val.substring(i, i+1);
                if (char < '0' || char > '9') {
                    alert('숫자만 입력가능 합니다.');
                    obj.value = new_val;
                    return;
                } else {
                    new_val = new_val + char;
                }
            }
        };

        const isEng = (str) => {
            for (var i=0; i<str.length; i++) {
                achar = str.charCodeAt(i);
                if ( achar > 255 ) {
                    return false;
                }
            }
            return true;
        };

        const compareObj = (obj, obj2) => {
            return Object.entries(obj).sort().toString() === Object.entries(obj2).sort().toString();
        };

        /**
         * actions
         */
        const commander = (cmd) => {
            switch (cmd) {
                case "apply":
                    apply();
                break;
                case "clear":
                    if (!confirm("취소 시 설정한 데이터와 등록목륵의 모든 상품이 지워집니다..\n취소 하시겠습니까?")) return false;
                    clear();
                break;
                // case "remove":
                //     if (!confirm("선택한 상품을 등록 목록에서 삭제 하시겠습니까?")) return false;
                //     deleteRows();
                // break;
                case "save":
                    save();
                break;
            };
        };
        
        const SHOP_POINT_RATIO = "{{$order_point_ratio}}";
        const apply = () => { // 적용

            if (!applyValidation()) return false;

            let row = {};
            prd_cnt			    = _("#prd_cnt").value; // 상품수

            row.com_type		= _("#com_type").value;	// 업체타입
            row.com_id			= _("#com_cd").value; // 업체아이디
            row.com_nm			= _("#com_nm").value; // 업체명
            row.opt_kind_cd		= _("#item").value;	// 품목
            row.brand			= _("#brand_cd").value; // 브랜드
            row.rep_cat_cd		= _("#rep_cat_cd").value; // 대표카테고리
            row.u_cat_cd		= _("#u_cat_cd").value; // 용도카테고리

            // 배송비설정
            if (document.f1.dlv_fee_cfg[1].checked) {
                row.dlv_fee_cfg = "G";
            } else {
                row.dlv_fee_cfg = "S";
            }
            row.dlv_fee_yn		= _("#dlv_fee_yn").value; // 배송시 유료 유무
            row.baesong_price	= _("#baesong_price").value; // 배송비

            // 적립금 설정
            if (document.f1.point_cfg[1].checked) {
                row.point_cfg = "G";
            } else {
                row.point_cfg = "S";
            }
            row.point_yn		= _("#point_yn").value; // 적립금 유무
            row.point_unit		= _("#point_unit").value // 적립단위
            row.point			= _("#point").value; // 적립율
            if (row.point_cfg == "S") {
                row.point = SHOP_POINT_RATIO;
                row.point_unit = "P";
            } else {
                if (row.point_unit == "W") {
                    //point_amt = point;
                }
            }

            // 배송비지불
            if (document.f1.dlv_pay_type[1].checked) {
                row.dlv_pay_type = "F";
            } else {
                row.dlv_pay_type = "P";
            }

            row.org_nm			= _("#org_nm").value;		// 원산지
            row.make			= _("#make").value;			// 제조사

            // 무한재고 상품
            if (document.f1.is_unlimited[1].checked) {
                row.is_unlimited = "Y";
            } else {
                row.is_unlimited = "N";
            }

            // 옵션사용 여부 및 옵션 구분
            if (document.f1.is_option_use[1].checked) {
                row.is_option_use = "N";
                _("#option_kind").value = "NONE";
            } else {
                row.is_option_use = "Y";
                if (_("#chk_option_kind1").checked && _("#option_kind1").value != "") {
                    var option_kind1 = _("#option_kind1").value;
                    _("#option_kind").value = option_kind1;
                }
                if (_("#chk_option_kind2").checked && _("#option_kind2").value != "") {
                    var option_kind2 = _("#option_kind2").value;
                    _("#option_kind").value = option_kind2;
                }
                if (option_kind1 && option_kind2) {
                    _("#option_kind").value = option_kind1 + "^" + option_kind2;
                }
            }

            $is_chk_opt_kind2 = "";
            if($("#chk_option_kind2").is(":checked")){
                $is_chk_opt_kind2 = true;
            }else{
                $is_chk_opt_kind2 = false;
            }

            row.is_chk_opt_kind2 = $is_chk_opt_kind2;
           
            $is_chk_opt_kind1 = "";
            if($("#chk_option_kind1").is(":checked")){
                $is_chk_opt_kind1 = true;
            }else{
                $is_chk_opt_kind1 = false;
            }

            row.is_chk_opt_kind1 = $is_chk_opt_kind1;

            row.option_kind	= _("#option_kind").value;
            row.tax_yn		= _("#tax_yn").value;		// 과세구분
            row.restock_yn	= (_("#restock_yn").checked == true) ? "Y" : "N";	// 재입고 설정

            const data = gx.getRows();
            const len = data.length;
            if (len > 0 && !confirm("적용 시 입력한 데이터가 초기화되고 덮어쓰여 지워집니다..\n적용 하시겠습니까?")) return false;

            /**
             * 입력한 상품수가 데이터와 같거나 커질 경우 처리
             */
            for (let i = 1; i <= prd_cnt; i++) {
                row.idx = i - 1; // update를 위한 ag-grid node key 설정
                
                if (len < prd_cnt) {
                    if (i <= len) {
                        updateRow(row);
                    } else {
                        addRow(row);
                    }
                } else if (len == prd_cnt) {
                    updateRow(row);
                }
            }

            /**
             * 입력한 상품수가 데이터보다 줄어들 경우 삭제처리
             */
            if (len > prd_cnt) {
                data.map((item, index) => { // ex) 상품수가 4, 데이터가 5
                    const count = index + 1;
                    row.idx = index;
                    if (count <= prd_cnt) {
                        updateRow(row);
                    } else {
                        deleteRow(row);
                    }
                });
            };

            $("#gd-total").text(prd_cnt);

        };

        const clear = () => { // 취소 - 항목 전체 삭제 및 조건 초기화
            const rows = gx.getRows();
            rows.map((row) => {
                deleteRow(row);
            });
            
            // 브랜드 비우기
            $("#brand_cd").empty();

            // 배송비, 적립금, 옵션구분 제거
            _('#dlv_fee_cfg_s').click();
            _('#point_cfg_s').click();
            _('#is_option_use1').click();

            // 나머지 input 초기화
            document.f1.reset();
            
            $("#gd-total").text("0");
        };

        const deleteRows = () => {}; // 미구현 - 없어도 기능은 동작

        const save = () => { // 일괄 등록
            if (validation()) {
                const data = gx.getRows();
                insertDB(data);
            };
        };

        const insertDB = async (data) => {
            for (let i = 0; i < data.length; i++) {
                let row = data[i];
                const response = await axios({
                    url: '/head/product/prd07/enroll',
                    method: 'post',
                    data: { row: row }
                });
                const { result, msg } = response.data;
                row = { ...getRowNode(row).data, msg: msg, result: result };
                updateRow(row);
            }
        };

        const resultStyle = (params) => {
            let STYLE = {...DEFAULT_STYLE, 'text-align': 'center'};
            if (params.data.result == undefined) return STYLE;
            if (params.data.result == '100' || params.data.result == '0') return STYLE = {...STYLE, ...CELL_STYLE.FAIL} // 중복된 스타일 넘버거나 시스템 에러
            if (params.data.result) return STYLE = {...STYLE, ...CELL_STYLE.OK} // 성공
        };

        const popCategory = (type) => {
            if (type == 'display') {
                searchCategory.Open('DISPLAY', (code, name, full_name) => {
                    if (searchCategory.type === "ITEM") {
                        alert("대표 카테고리는 전시 카테고리만 설정가능합니다.");
                        return false;
                    }
                    document.querySelector("#rep_cat_cd").value = code;
                    document.querySelector("#rep_cat_nm").value = full_name;
                });
            } else if (type == 'item') {
                searchCategory.Open('ITEM', (code, name, full_name) => {
                    if (searchCategory.type === "DISPLAY") {
                        alert("용도 카테고리만 설정가능합니다.");
                        return false;
                    }
                    document.querySelector("#u_cat_cd").value = code;
                    document.querySelector("#u_cat_nm").value = full_name;
                });
            }
        };



        const changeOptionUse = (obj) => {
            const used = obj.value;
            // if (used == "N") {
            //     $("input[name='option_kind1']").attr("disabled", true);
            //     $("input[name='option_kind1']").attr("readonly", true);
            //     $("input[name='option_kind1']").val("NONE");
            //     $("input[name='chk_option_kind1']").attr("disabled", true);
            //     $("input[name='chk_option_kind1']").attr("checked", false);
            //     $("input[name='option_kind2']").attr("disabled", true);
            //     $("input[name='option_kind2']").attr("readonly", true);
            //     $("input[name='option_kind2']").val("NONE");
            //     $("input[name='chk_option_kind2']").attr("disabled", true);
            //     $("input[name='chk_option_kind2']").attr("checked", false);
            // } else if (used == "Y") {
            //     $("input[name='option_kind1']").attr("disabled", false);
            //     $("input[name='option_kind1']").attr("readonly", false);
            //     $("input[name='option_kind2']").attr("disabled", false);
            //     $("input[name='option_kind2']").attr("readonly", false);
            //     $("input[name='option_kind1']").val("사이즈");
            //     $("input[name='option_kind1']").className = "input";
            //     $("input[name='chk_option_kind1']").attr("disabled", false);
            //     $("input[name='chk_option_kind1']").attr("checked", true);
            //     $("input[name='option_kind2']").val("컬러");
            //     $("input[name='chk_option_kind2']").attr("disabled", false);
            //     $("input[name='chk_option_kind2']").attr("checked", true);
            // }

            if (used == 'Y') {
                $("input[name='option_kind1']").attr("disabled", false);
                $("input[name='option_kind1']").val("사이즈");
                $("input[name='chk_option_kind1']").attr("disabled", false);
                $("input[name='chk_option_kind1']").attr("checked", true);
                $("input[name='option_kind2']").attr("disabled", false);
                $("input[name='option_kind2']").val("컬러");
                $("input[name='chk_option_kind2']").attr("disabled", false);
                $("input[name='chk_option_kind2']").attr("checked", true);

            } else {
                $("input[name='option_kind1']").attr("disabled", true);
                $("input[name='option_kind1']").val("NONE");
                $("input[name='chk_option_kind1']").attr("disabled", true);
                $("input[name='chk_option_kind1']").attr("checked", false);
                $("input[name='option_kind2']").attr("disabled", true);
                $("input[name='option_kind2']").val("NONE");
                $("input[name='chk_option_kind2']").attr("disabled", true);
                $("input[name='chk_option_kind2']").attr("checked", false);
            }
        };

        // $("#chk_option_kind1").bind("change", () => {
        //     var checkbox1 = $("#chk_option_kind1")[0];
        //     if (!checkbox1.checked) {
        //         if (document.f1.is_option_use[0].checked) {
        //             alert("옵션사용을 \"사용함\"으로 선택하셨기 때문에 적어도 한개의 옵션은 입력하셔야 합니다.");
        //             checkbox1.checked = true;
        //         }
        //     }
        // });
        $("#chk_option_kind1").bind("change", () => {
            if ($("#chk_option_kind1")[0].checked) {
                $("input[name='option_kind1']").attr("disabled", false);
                $("input[name='option_kind1']").attr("readonly", false);
                $("input[name='option_kind1']").val("사이즈");
                $("input[name='option_kind1']").focus();
            } else {
                $("input[name='option_kind1']").attr("disabled", true);
                $("input[name='option_kind1']").attr("readonly", true);
                $("input[name='option_kind1']").val("사이즈");
            }
        });
        $("#chk_option_kind2").bind("change", () => {
            if ($("#chk_option_kind2")[0].checked) {
                $("input[name='option_kind2']").attr("disabled", false);
                $("input[name='option_kind2']").attr("readonly", false);
                $("input[name='option_kind2']").val("컬러");
                $("input[name='option_kind2']").focus();
            } else {
                $("input[name='option_kind2']").attr("disabled", true);
                $("input[name='option_kind2']").attr("readonly", true);
                $("input[name='option_kind2']").val("컬러");
            }
        });

        const changeDeliveryConfig = (radio) => {
            if (radio.value == "S") {
                $("#dlv_fee_yn").attr("disabled", true);
                $("#dlv_fee_yn").val("Y");
                $("#baesong_price").attr("disabled", true);
                $("#baesong_price").val("");
            }else if (radio.value == "G") {
                $("#dlv_fee_yn").attr("disabled", false);
                $("#baesong_price").attr("readonly", false);
                $("#baesong_price").attr("disabled", false);
                $("#baesong_price").val("");
            }
        };

        const changePointConfig = (radio) => {
            if (radio.value == "S") {
                $("#point_yn").attr("disabled", true);
                $("#point_yn").val("Y");
                $("#point_unit").attr("disabled", true);
                $("#point_unit").val("W");
                $("#point").attr("readonly", true);
                $("#point").attr("disabled", true);
                $("#point").val("");
            } else if (radio.value == "G") {
                $("#point_yn").attr("disabled", false);
                $("#point_unit").attr("disabled", false);
                $("#point").attr("readonly", false);
                $("#point").attr("disabled", false);
            }
        };

        const changePointUnit = (obj) => {
            document.querySelector("#point").value = 0;
            if (obj.value == "P") {
                document.querySelector("#point_unit_str").innerHTML = "%";
            } else {
                document.querySelector("#point_unit_str").innerHTML = "원";
            }
        };
        
        const changeLimitedQtyYN = (obj) => {
            const min_qty = document.querySelector("#limited_min_qty");
            const max_qty = document.querySelector("#limited_max_qty");
            if (obj.value == "Y") {
                min_qty.disabled = false;
                min_qty.readOnly = false;
                max_qty.disabled = false;
                max_qty.readOnly = false;
            } else {
                min_qty.disabled = true;
                min_qty.readOnly = true;
                max_qty.disabled = true;
                max_qty.readOnly = true;
            }
            min_qty.value = "";
            max_qty.value = "";
        };

        const checkOptionValue = (obj) => {
            if (obj.value.indexOf("^") > -1) {
                alert("옵션 구분 항목에는 \"^\"문자를 사용할 수 없습니다.");
                obj.value = obj.value.replace("^","");
                obj.focus();
            }
        };

        const customSearchCompany = () => {
            searchCompany.Open((code, name, com_type) => {
                if (com_type == '1' || com_type == '2') { // com_type 값: 공급업체 = '1', 입점업체 = '2'
                    if ( $('#com_cd').length > 0 ) $('#com_cd').val(code);
                    if ( $('#com_id').length > 0 ) $('#com_id').val(code);
                    if ( $('#com_nm').length > 0 ) $('#com_nm').val(name);
                    $('#com_type').val(com_type);
                } else {
                    alert('공급업체 또는 입점업체의 상품만 등록하실 수 있습니다.');
                }
            });
        };

        /*
            Function: isNumVal
                Number Format Check

            Parameters:
                Num - number

            Returns:
                true or false
        */

        function isNumVal(NUM) {
            for(var i=0;i<NUM.length;i++){
                achar = NUM.substring(i,i+1);
                if( achar < "0" || achar > "9" ){
                    return false;
                }
            }
            return true;
        }

        const applyValidation = () => {

            const prd_cnt = document.f1.prd_cnt.value;
            if (!prd_cnt || prd_cnt < "0" || prd_cnt == "0") {
                alert("상품수를 입력해 주세요.");
                document.f1.prd_cnt.focus();
                return false;
            }

            if (document.f1.com_cd.value == "") {
                alert("업체를 입력해 주세요.");
                customSearchCompany();
                return false;
            }

            if (document.f1.item.value == "") {
                alert("품목을 입력해 주세요.");
                document.f1.item.focus();
                return false;
            }

            if (document.f1.brand_cd.value == "") {
                alert("브랜드를 입력해 주세요.");
                document.querySelector('.sch-brand').click();
                return false;
            }

            // if (document.f1.rep_cat_cd.value == "") {
            //     alert("대표카테고리를 입력해 주세요.");
            //     popCategory('display');
            //     return false;
            // }

            // if (document.f1.org_nm.value == "") {
            //     alert("원산지를 입력해 주세요.");
            //     document.f1.org_nm.focus();
            //     return false;
            // }

            if (document.f1.is_unlimited.value == "") {
                alert("재고 수량 관리를 선택해 주세요.");
                return false;
            }

            if (document.f1.is_option_use.value == "") {
                alert("옵션사용을 선택해 주세요.");
                return false;
            }

            if (document.f1.dlv_pay_type.value == "") {
                alert("배송비지불을 입력해 주세요.");
                return false;
            }
            if (document.f1.dlv_fee_cfg.value == "") {
                alert("배송비설정을 선택해 주세요.");
                return false;
            }
            if (document.f1.dlv_fee_yn.value == "") {
                alert("배송비여부를 입력해 주세요.");
                return false;
            }
            if (document.f1.point_cfg.value == "") {
                alert("적립금설정을 선택해 주세요.");
                return false;
            }
            if (document.f1.point_yn.value == "") {
                alert("적립금여부를 입력해 주세요.");
                return false;
            }
            
            if (document.f1.tax_yn.value == "") {
                alert("과세구분을 입력해 주세요.");
                return false;
            }

            return true;

        };

        const validation = () => {
            
            // 모든 행의 판매가, 마진율을 검사
            const rows = gx.getRows();

            let row;
            for (let i=0; i < rows.length; i++) {

                row = rows[i];

                // console.log(row);

                row.goods_cont = row.goods_cont?.replace(/^\"+|\"+$/g,"");
                row.goods_cont = row.goods_cont?.replace(/\"\"/g,"'");

                if (row.style_no == undefined || row.style_no == "") { // 저기
                    stopEditing();
                    alert("스타일넘버을 입력해 주세요.");
                    startEditingCell(row.idx, 'style_no');
                    return false;
                }

                if (row.style_no && !isEng(row.style_no)) {
                    stopEditing();
                    alert("스타일넘버는 한글을 입력하실수 없습니다.");
                    startEditingCell(row.idx, 'style_no');
                    return false;
                }
                if (row.goods_nm == undefined || row.goods_nm == "") {
                    stopEditing();
                    alert("상품명을 입력해 주세요.");
                    startEditingCell(row.idx, 'goods_nm');
                    return false;
                }

                if (row.price == undefined || row.price == "" || row.price <= 0 || !isNumVal(parseInt(row.price))) {
                    stopEditing();
                    alert("판매가를 입력해 주세요.");
                    startEditingCell(row.idx, 'price');
                    return false;
                }
                if (row.margin_rate == undefined || row.margin_rate == "" || row.margin_rate <= 0) {
                    stopEditing();
                    alert("마진율을 입력해 주세요.");
                    startEditingCell(row.idx, 'margin_rate');
                    return false;
                }
                if (row.wonga == undefined || row.wonga == "" || row.wonga <= 0) {
                    stopEditing();
                    alert("원가를 입력해 주세요.");
                    startEditingCell(row.idx, 'wonga');
                    return false;
                }

                if (row.option_kind == undefined || row.option_kind == "") {
                    stopEditing();
                    alert("옵션구분을 입력해 주세요.");
                    startEditingCell(row.idx, 'option_kind');
                    return false;
                }
                var a_opt_kind = row.option_kind.split("^");
                if (a_opt_kind.length > 2) {
                    stopEditing();
                    alert("옵션구분은 최대 2개까지만 입력 가능합니다.\nex) 사이즈^컬러");
                    startEditingCell(row.idx, 'option_kind');
                    return false;
                }
                if ( row.option_kind.indexOf("^") > -1 ) {
                    if (row.opt1 == "" ) {
                        stopEditing();
                        alert("옵션1 항목에 옵션값을 입력 하십시오.");
                        startEditingCell(row.idx, 'opt1');
                        return false;
                    }
                    if (row.opt2 == "" ) {
                        alert("옵션2 항목에 옵션값을 입력 하십시오.");
                        return false;
                    }
                } else {
                    if (row.opt1 == undefined || row.opt1 == "" ) {
                        stopEditing();
                        alert("옵션을 입력해 주세요.");
                        startEditingCell(row.idx, 'opt1');
                        return false;
                    }
                }

                if (row.opt_qty == undefined || row.opt_qty == "") {
                    stopEditing();
                    alert("수량을 입력해 주세요.");
                    startEditingCell(row.idx, 'opt_qty');
                    return false;
                }
                // 첫번째 옵션 갯수와 수량 갯수 비교
                if (row.opt_qty != "") {
                    var a_opt1 = row.opt1?.split(",");
                    var a_opt_qty = row.opt_qty?.split(",");
                    if (a_opt1?.length > 1 && a_opt1?.length != a_opt_qty?.length) {
                        stopEditing();
                        alert("수량의 갯수가 옵션의 갯수와 다릅니다.");
                        startEditingCell(row.idx, 'opt_qty');
                        return false;
                    }
                }
                // 첫번째 옵션 갯수와 옵션 가격 비교
                if (row.opt_price != "") {
                    var a_opt1 = row.opt1?.split(",");
                    var a_opt_price = row.opt_price?.split(",");
                    if(a_opt1?.length > 1 && a_opt1?.length != a_opt_price?.length){
                        stopEditing();
                        alert("옵션 가격의 갯수가 옵션1의 갯수와 다릅니다.");
                        startEditingCell(row.idx, 'opt_price');
                        return false;
                    }
                }
                
            }

            if ( gx.getRows().length == 0 ) {
                alert("상품을 등록해 주세요.");
                return false;
            } else {
                return true;
            }

        };

        // const evtAfterEdit = (params) => {
        //     if (params.oldValue !== params.newValue) {
        //         row = params.data;
        //         const column_name = params.column.colId;
        //         const value = params.newValue;
        //         switch (column_name) {
        //             case "goods_sh": // 시중가
        //                 if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
        //                     alert("숫자만 입력가능합니다.");
        //                     startEditingCell(row.idx, column_name);
        //                 } 
        //                 // else if (value == "") { - 시중가는 필수 항목 x
        //                 //     alert("시중가를 입력해주세요.");
        //                 //     startEditingCell(row.idx, column_name);
        //                 // }
        //                 break;
        //             case "wonga": // 원가
        //                 if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
        //                     alert("숫자만 입력가능합니다.");
        //                     startEditingCell(row.idx, column_name);
        //                 } else if (value == "") {
        //                     alert("원가를 입력해주세요.");
        //                     startEditingCell(row.idx, column_name);
        //                 }
        //                 break;
        //             case "price": // 판매가
        //                 if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
        //                     alert("숫자만 입력가능합니다.");
        //                     startEditingCell(row.idx, column_name);
        //                 } else if (value == "") {
        //                     alert("판매가를 입력해주세요.");
        //                     startEditingCell(row.idx, column_name);
        //                 } else {
        //                     const ed_price = value;
        //                     row = cmdPrice(row, ed_price, row.margin_rate); 
        //                     if (row.point_yn == "Y") {
        //                         if ( row.point_unit == "P" ) {
        //                             row.point_amt = value * row.point / 100;
        //                         }
        //                     }
        //                 }
        //                 break;
        //             case "margin_rate": // 마진율
        //                 if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
        //                     alert("숫자만 입력가능합니다.");
        //                     startEditingCell(row.idx, column_name);
        //                 } else if (value == "") {
        //                     alert("마진율을 입력해 주세요.");
        //                     startEditingCell(row.idx, column_name);
        //                 } else {
        //                     const ed_margin_rate = parseInt(value);
        //                     if (ed_margin_rate > 100) {
        //                         alert("마진율은 100을 넘을 수 없습니다.");
        //                         startEditingCell(row.idx, column_name);
        //                     } else {
        //                         row = cmdPrice(row, row.price, ed_margin_rate);
        //                     }
        //                 }
        //                 break;
        //             default:
        //                 break;
        //         }
        //         gx.gridOptions.api.applyTransaction({ update : [row] });
        //     }
        // };
       
        // const cmdPrice = (row, price, margin_rate) => {
        //     const com_type = row.com_type;
        //     return calPrice(row, com_type, parseInt(price), margin_rate);
        // }

        // const calPrice = (row, com_type, price, margin_rate) => { // 행, 판매가, 마진율

        //     let wonga = row.wonga;

        //     if (com_type == 1) {	// 공급 업체인 경우
        //         if ( row.price != price ) {
        //             margin_rate = ((price-wonga)/price)*100;
        //         } else if ( row.margin_rate != margin_rate ) {
        //             price = parseInt(Math.round(wonga / (1-margin_rate/100)), 10);
        //         }
        //     } else { // 그 외의 경우
        //         if ( price && wonga ) {
        //             margin_rate = parseFloat(((price - wonga) / price) * 100).toFixed(2);
        //         }
        //     }

        //     row = {...row, price: price, margin_rate: margin_rate, wonga: wonga }

        //     return row;
        // };

</script>

@stop