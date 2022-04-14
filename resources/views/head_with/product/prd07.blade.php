@extends('head_with.layouts.layout-nav')
@section('title','상품관리 - 일괄수정')
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
                                <label for="prd_cnt" style="color: blue; font-weight: 600;">상품수</label>
                                <div class="flex_box">
                                    <input type="text" name="prd_cnt" id="prd_cnt" class="form-control form-control-sm" style="width:40px;" onkeydown="onlyNum(this);"/>
                                    <span>&nbsp;개</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item">업체</label>
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
                                <label for="item">품목</label>
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
                                <label for="brand_cd">브랜드</label>
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
                                <label for="dlv_pay_type">배송비 지불</label>
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
                                <label for="">배송비</label>
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
                                <label for="point">적립금</label>
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
                                <label for="dlv_pay_type">옵션사용</label>
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
                                <label for="check_option_kind">옵션구분</label>
                                <div class="form-inline inline_input_box flex_box">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="chk_option_kind1" id="chk_option_kind1" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="chk_option_kind1">&nbsp;</label>
                                    </div>
                                    <input type="text" class="form-control form-control-sm mr-2" name="option_kind1" value="사이즈" onkeyup="checkOptionValue(this);" style="width: 100px"/>
                                    <span class="txt_box mr-2">~</span>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="chk_option_kind2" id="chk_option_kind2" class="custom-control-input" onkeyup="checkOptionValue(this);"/>
                                        <label class="custom-control-label" for="chk_option_kind2">&nbsp;</label>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="option_kind2" value="컬러" style="width: 100px" disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="tax_yn">과세구분</label>
                                <div class="flex_box">
                                    <select id="tax_yn" name="tax_yn" class="form-control form-control-sm">
                                        @foreach ($tax_yn as $key => $val)
                                        <option value='{{ $key }}'>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="is_unlimited">재고 수량 관리</label>
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
                                        <input type="checkbox" class="custom-control-input" value="Y" id="restock" checked>
                                        <label class="custom-control-label" for="restock">재 입고함</label>
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
                        <a href="#" class="btn btn-sm btn-primary submit-btn" onclick="apply();">적용</a>
                        <a href="#" class="btn btn-sm btn-secondary" onclick="cancel();">취소</a>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-2">
                <a href="#" onclick="addGoods()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>상품추가</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
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
                                <a href="#" onclick="deleteRows();" class="btn btn-sm btn-primary shadow-sm mr-1">삭제</a>
                                <a href="#" onclick="saveRows();" class="btn btn-sm btn-primary shadow-sm mr-1">저장</a>
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
        const DEFAULT_STYLE = { 'background' : 'none', 'line-height': '40px', 'height': '40px' };

        const CELL_STYLE = {
            EDIT: { 'background': '#ffff99' },
            OK: { 'background': 'rgb(200,200,255)' },
            FAIL: { 'background': 'rgb(255,200,200)' }
        };

        var columns= [
            {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, sort: null, pinned:'left'},
            {field:"msg", headerName:"결과", pinned:'left', width:65,
                cellStyle: (params) => {
                    let STYLE = {...DEFAULT_STYLE, 'text-align': 'center'};
                    if (params.data.msg == 'OK') {
                        STYLE = {...STYLE, ...CELL_STYLE.OK}
                    } else if (params.data.msg == 'FAIL') {
                        STYLE = {...STYLE, ...CELL_STYLE.FAIL}
                    }
                    return STYLE;
                },
                cellRenderer: (params) => {
                    if (params.data.msg == 'OK') {
                        return 'OK';
                    } else if (params.data.msg == 'FAIL') {
                        return 'FAIL';
                    }
                    return "";
                }
            },
            {headerName:"상품번호", 
                children: [
                    {headerName: "번호", field: "goods_no", width: 70, pinned:'left', cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}},
                    {headerName: "하위", field: "goods_sub", width: 65, pinned:'left', cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}}
                ],
                pinned:'left'
            },
            {field:"goods_type_nm" ,headerName:"상품구분",width:90, pinned:'left', cellStyle: DEFAULT_STYLE},
            {field:"style_no" ,headerName:"스타일넘버",pinned:'left',width:100, cellStyle: DEFAULT_STYLE},
            {field:"opt_kind_nm" ,headerName:"품목",pinned:'left',width:70, cellStyle: DEFAULT_STYLE},
            {field:"brand_nm" ,headerName:"브랜드",pinned:'left',width:80, cellStyle: DEFAULT_STYLE},
            {field:"rep_cat_nm", headerName: "대표카테고리", pinned:'left', cellStyle: DEFAULT_STYLE},
            {field:"sale_stat_nm" ,headerName:"상품상태",width:100, pinned:'left', cellStyle: DEFAULT_STYLE},
            {field:"img", headerName: "이미지", type: 'GoodsImageType', cellStyle: DEFAULT_STYLE},
            {field:"head_desc", headerName: "상단홍보글", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true, width: 100},
            {field:"goods_nm", headerName: "상품명", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true, width: 200},
            {field:"goods_nm_eng", headerName: "상품영문명", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true, width: 150},
            {field:"ad_desc", headerName: "하단홍보글", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true, width: 100},
            {headerName:"현재 가격",
                children: [
                    {headerName: "정상가", field: "normal_price", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "판매가", field: "price", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "세일(%)", field: "sale_rate", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'percentType'},
                    {headerName: "마진율(%)", field: "margin_rate", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'percentType'},
                    {headerName: "정상원가", field: "normal_wonga", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "원가", field: "wonga", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "세일원가", field: "sale_wonga", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'}
                ]
            },
            {headerName:"수정 가격",
                children: [
                    {headerName: "정상가", field: "ed_normal_price", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "판매가", field: "ed_price", width: 100, type:'currencyType', cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
                    {headerName: "세일(%)", field: "ed_sale_rate", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'percentType'},
                    {headerName: "세일가", field: "ed_sale_price", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "마진율(%)", field: "ed_margin_rate", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'percentType',
                        editable: (params) => {
                            return ( params.data.com_type == "2" && params.data.margin_type == "FEE") ? true : false;
                        },
                        cellStyle: (params) => {
                            let STYLE = {...DEFAULT_STYLE, 'text-align': 'right'};
                            return ( params.data.com_type == "2" && params.data.margin_type == "FEE") ? {...STYLE, ...CELL_STYLE.EDIT} : STYLE;
                        }
                    },
                    {headerName: "정상원가", field: "ed_normal_wonga", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "원가", field: "ed_wonga", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'},
                    {headerName: "세일원가", field: "ed_sale_wonga", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'right'}, type:'currencyType'}
                ]
            },
            {field:"sale_yn", headerName: "세일여부", cellStyle: DEFAULT_STYLE},
            {field:"sale_dt_yn", headerName: "세일기간사용여부", cellStyle: DEFAULT_STYLE},
            {headerName:"세일기간",
                children: [
                    {headerName: "시작", field: "sale_s_dt", width: 120, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "종료", field: "sale_e_dt", width: 120, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}}
                ]
            },
            {field:"baesong_info_nm", headerName: "배송방식", cellStyle: DEFAULT_STYLE},
            {field:"baesong_info", headerName: "배송업체", cellStyle: DEFAULT_STYLE},
            {field:"dlv_pay_type_nm", headerName: "배송비지불", cellStyle: DEFAULT_STYLE},
            {headerName:"배송비",
                children: [
                    {headerName: "설정", field: "dlv_fee_cfg", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "지급", field: "dlv_fee_yn", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "금액", field: "baesong_price", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'}
                ]
            },
            {field:"dlv_due_type", headerName: "배송예정구분", cellStyle: DEFAULT_STYLE},
            {field:"dlv_due_period", headerName: "배송기간", cellStyle: DEFAULT_STYLE},
            {field:"dlv_due_day", headerName: "배송예정일", cellStyle: DEFAULT_STYLE},
            {field:"dlv_due_memo", headerName: "배송예정일 사유", cellStyle: DEFAULT_STYLE},
            {headerName:"적립금",
                children: [
                    {headerName: "설정", field: "point_cfg", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "지급", field: "point_yn", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "적립", field: "point", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'},
                    {headerName: "단위", field: "point_unit", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "금액", field: "point_amt", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}, type:'currencyType'}
                ]
            },
            {field:"org_nm", headerName: "원산지", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"md_nm", headerName: "MD", cellStyle: DEFAULT_STYLE},
            {field:"goods_cont", headerName: "상품상세", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"make", headerName: "제조사", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"spec_desc", headerName: "제품사양", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"baesong_desc", headerName: "예약/배송", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"opinion", headerName: "MD상품평", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"restock_yn", headerName: "재입고알림", cellStyle: DEFAULT_STYLE},
            {field:"tax_yn", headerName: "과세구분", cellStyle: DEFAULT_STYLE},
            {field:"goods_location", headerName: "상품위치", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {field:"tags", headerName: "상품태그", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {headerName:"신상품적응일",
                children: [
                    {headerName: "구분", field: "new_product_type", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "신상품일자", field: "new_product_day", width: 100, cellStyle:{...DEFAULT_STYLE, ...CELL_STYLE.EDIT, 'text-align': 'center'}, editable: true}
                ]
            },
            {field:"color", headerName: "상품색상", cellStyle: {...DEFAULT_STYLE, ...CELL_STYLE.EDIT}, editable: true},
            {headerName:"수량제한",
                children: [
                    {headerName: "수량제한여부", field: "limited_qty_yn", width: 140, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "최소", field: "limited_min_qty", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "최대", field: "limited_max_qty", width: 100, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "총구매수량제한 여부", field: "limited_total_qty_yn", width: 160, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}},
                    {headerName: "회원전용상품 여부", field: "member_buy_yn", width: 140, cellStyle:{...DEFAULT_STYLE, 'text-align': 'center'}}
                ]
            }
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
                onCellValueChanged: params => evtAfterEdit(params),
                getRowNodeId: (data) => data.index // 업데이터 및 제거를 위한 식별 ID를 index로 할당
            };
            gx = new HDGrid(gridDiv, columns, options);
        });

        /**
         * ag-grid utils
         */
        const getRowNode = (row) => {
            gx.gridOptions.api.getRowNode(row.count);
        };

        const startEditingCell = (row_index, col_key) => {
            gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
        };

        const stopEditing = () => {
            gx.gridOptions.api.stopEditing();
        };

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

        /**
         * actions
         */
        const commander = (cmd) => {
            switch (cmd) {
                case "apply":
                    apply(cmd);
                break;
                case "clear":
                    if (!confirm("취소 시 설정한 데이터와 등록목륵의 모든 상품이 지워집니다..\n취소 하시겠습니까?")) return false;
                    clear(cmd);
                break;
                case "remove":
                    if (!confirm("선택한 상품을 등록 목록에서 삭제 하시겠습니까?")) return false;
                    remove(cmd);
                break;
                case "save":
                    save(cmd);
                break;
            };
        };
        
        const apply = (cmd) => { // 적용

        };

        const clear = (cmd) => { // 취소

        };

        const remove = (cmd) => { // 삭제

        };

        const save = (cmd) => { //  저장

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
            if (used == "N") {
                $("input[name='option_kind1']").attr("disabled", true);
                $("input[name='option_kind1']").attr("readonly", true);
                $("input[name='option_kind1']").val("NONE");
                $("input[name='chk_option_kind1']").attr("disabled", true);
                $("input[name='chk_option_kind1']").attr("checked", false);
                $("input[name='option_kind2']").attr("disabled", true);
                $("input[name='option_kind2']").attr("readonly", true);
                $("input[name='option_kind2']").val("NONE");
                $("input[name='chk_option_kind2']").attr("disabled", true);
                $("input[name='chk_option_kind2']").checked = false;
            } else if (used == "Y") {
                $("input[name='option_kind1']").attr("disabled", false);
                $("input[name='option_kind1']").attr("readonly", false);
                $("input[name='option_kind1']").val("사이즈");
                $("input[name='option_kind1']").className = "input";
                $("input[name='chk_option_kind1']").attr("disabled", false);
                $("input[name='chk_option_kind1']").attr("checked", true);
                $("input[name='option_kind2']").val("컬러");
                $("input[name='chk_option_kind2']").attr("disabled", false);
            }
        };

        $("#chk_option_kind1").bind("change", () => {
            var checkbox1 = $("#chk_option_kind1")[0];
            if (!checkbox1.checked) {
                if (document.f1.is_option_use[0].checked) {
                    alert("옵션사용을 \"사용함\"으로 선택하셨기 때문에 적어도 한개의 옵션은 입력하셔야 합니다.");
                    checkbox1.checked = true;
                }
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

        const evtAfterEdit = (params) => {
            if (params.oldValue !== params.newValue) {
                row = params.data;
                const column_name = params.column.colId;
                const value = params.newValue;
                switch (column_name) {
                    // case "ed_goods_sh": // 시중가
                    //     if (isNaN(value) == true) {
                    //         alert("숫자만 입력가능합니다.");
                    //         startEditingCell(row.index, column_name);
                    //     } else if (value == "") {
                    //         alert("시중가를 입력해주세요.");
                    //         startEditingCell(row.index, column_name);
                    //     }
                    //     break;
                    case "ed_price": // 판매가
                        if (isNaN(value) == true) {
                            alert("숫자만 입력가능합니다.");
                            startEditingCell(row.index, column_name);
                        } else if (value == "") {
                            alert("판매가를 입력해주세요.");
                            startEditingCell(row.index, column_name);
                        } else if (row.fix_wonga && row.margin_type == "FEE") {
                            alert("수수료 적용 방식이 '수수료 지정'으로 설정된 업체의 상품은,\n'원가고정'이 체크된 상태에서 판매가의 변경이 불가능합니다.");
                            startEditingCell(row.index, column_name);
                        } else {
                            const ed_price = value;
                            row = cmdPrice(row, ed_price, row.ed_margin_rate);
                            if (row.point_yn == "Y") {
                                if ( row.point_unit == "P" ) {
                                    row.point_amt = value * point / 100;
                                }
                            }
                            if (row.sale_yn == "Y") {
                                row.sale_rate = Math.round((1-val / ed_normal_price) * 100);
                                row.sale_price = parseInt(value);
                            }
                        }
                        break;
                    case "ed_margin_rate": // 마진율
                        if (isNaN(value) == true) {
                            alert("숫자만 입력가능합니다.");
                            startEditingCell(row.index, column_name);
                        } else if (value == "") {
                            alert("마진율을 입력해 주세요.");
                            startEditingCell(row.index, column_name);
                        } else {
                            ed_margin_rate = parseInt(value);
                            if (ed_margin_rate > 100) {
                                alert("마진율은 100을 넘을 수 없습니다.");
                                startEditingCell(row.index, column_name);
                            } else {
                                row = cmdPrice(row, row.ed_price, ed_margin_rate);
                            }
                        }
                        break;
                    default:
                        break;
                }
                gx.gridOptions.api.applyTransaction({ update : [row] });
            }
        };

        const cmdPrice = (row, price, margin) => {
            const com_type = row.com_type;

            let fix_wonga = '';
            if (_("#fix_wonga") && _("#fix_wonga").checked){
                fix_wonga = 'Y';
            }

            let sale_yn = "";
            if(document.f1.sale_yn[0].checked){
                sale_yn = "Y";
            } else if(document.f1.sale_yn[1].checked){
                sale_yn = "N";
            }
            
            return calPrice(row, com_type, parseInt(price), margin, fix_wonga, sale_yn);
        }

        const calPrice = (row, com_type, price, margin, fix_wonga, sale_yn) => { // 행, 판매가, 마진율

            let wonga = row.wonga;
            let sale_price, sale_wonga;

            if (com_type == 1) {	// 공급
                if ( row.price != price ) {
                    margin = ((price-wonga)/price)*100;
                } else if ( row.margin_rate != margin ) {
                    price = parseInt(Math.round(wonga / (1-margin/100)), 10);
                }
            } else {
                const margin_type = row.margin_type;
                if (fix_wonga == 'Y') {
                    if ( margin && wonga ) {
                        price = parseInt(Math.round(wonga / (1 - (margin / 100))),10);
                    }
                } else {
                    if ( margin_type == "FEE" ) {
                        if ( price && margin ) {
                            wonga = parseInt(Math.round(price * (1 - margin / 100)), 10);
                        }
                    } else {
                        if ( price && wonga ) {
                            margin = parseFloat(((price - wonga) / price) * 100).toFixed(2);
                        }
                    }
                    sale_price = row.ed_sale_price;
                    sale_wonga = Math.round(sale_price * (1 - (margin/100)));
                }
            }

            if (sale_yn == "") {
                row = {...row, ed_normal_price: price, ed_normal_wonga: wonga};
            }

            row = {...row, ed_price: price, ed_margin_rate: margin, ed_wonga: wonga, ed_sale_wonga: sale_wonga }

            return row;
        };

        const validation = () => {
            
            // 모든 행의 판매가, 마진율을 검사
            const rows = gx.getRows();
            rows.map(row => {
                const { ed_price, ed_margin_rate } = row;
                if (ed_price == "" || ed_price <= 0) {
                    stopEditing();
                    alert("판매가를 입력해 주세요.");
                    startEditingCell(row.index, 'ed_price');
                    return false;
                }
                if (ed_margin_rate == "") {
                    stopEditing();
                    alert("마진율을 입력해 주세요.");
                    startEditingCell(row.index, 'ed_margin_rate');;
                    return false;
                }
            });

            if ( gx.getRows().length == 0 ) {
                alert("상품을 추가해 주세요.");
                return false;
            } else {
                return true;
            }

        };

        const cancel = () => { 

            if (!confirm("취소 시 설정하신 모든 데이터가 지워집니다. 취소 하시겠습니까?")) return false;

            if (validation() == true) {
                const rows = gx.getRows();
                const goods_nos = rows.reduce((acc, row, index) => {
                    return acc += (index == 0) ? numberSet(row) : ',' + numberSet(row);
                }, "");
                getEditingRows(goods_nos);
            }

            _("#search-area input").value ="";
            _("#search-area select").value ="";
            if (_("#search-area input:checked")) _("#search-area input:checked").checked = false;

            _("#dlv_fee_yn").value = "Y";
            _("#point_yn").value = "Y";

        };

</script>

@stop