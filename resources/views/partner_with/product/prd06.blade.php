@extends('partner_with.layouts.layout-nav')
@section('title','상품일괄등록')
@section('content')

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품일괄등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품관리</span>
                <span>/ 상품일괄등록</span>
            </div>
        </div>
    </div>
    <form name="f1">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header">
                    <a href="#">일괄등록품목</a>
                </div>
                <div class="fr_box flax_box" style="position: absolute; right: 2%;">
                    <a href="#" class="btn-sm btn btn-primary mr-1 apply-btn" onclick="commander('apply');">적용</a>
                    <a href="#" onclick="document.detail.reset()"  class="btn btn-sm btn-primary shadow-sm">취소</a>
                </div>
                <style>
                    .required:after {
                      content:" *"; color: red;
                    }
                </style>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable">
                                        <colgroup>
                                            <col width="15%">
                                            <col width="35%">
                                            <col width="15%">
                                            <col width="35%">
                                        </colgroup>
                                        <tr>
                                            <th>상품수</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="prd_cnt" id="prd_cnt" class="form-control form-control-sm search-all" style="width: 86%">&nbsp;개&nbsp;
                                                </div>
                                            </td>
                                            <th class="required">업체</th>
                                            <td>
                                                <div class="flax_box txt_box">{{$com_info->com_nm}}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">품목</th>
                                            <td>
                                                <div class="select_box">
                                                    <select name="op_cd" id="op_cd" class="form-control form-control-sm search-all">
                                                        <option value="">선택하세요.</option>
                                                        @foreach($opt_cd_list as $opt_cd)
                                                            <option value="{{$opt_cd->name}}">{{$opt_cd->value}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <th class="required">브랜드</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type="text" class="form-control form-control-sm search-all" name="brand_nm" id="brand_nm" value="" style="width: 75%">
													<input type="text" class="form-control form-control-sm search-all" name="brand_cd" id="brand_cd" value="" style="width: 25%" disabled>
                                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">대표카테고리</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type="text" value="" name="rep_cat_nm" id="rep_cat_nm" class="form-control form-control-sm search-all" style="width: 100%"/>
                                                    <input type="hidden" value="" name="rep_cat_cd" id="rep_cat_cd"/>
                                                    <a href="#" class="btn btn-sm btn-outline-primary"
                                                        onclick="searchCategory.Open('DISPLAY',function(code,name){
                                                            $('#rep_cat_cd').val(code);
                                                            $('#rep_cat_nm').val(name);
                                                        });"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                            <th>용도카테고리</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type="text" value="" name="u_cat_nm" id="u_cat_nm" class="form-control form-control-sm search-all" style="width: 100%"/>
                                                    <input type="hidden" id="u_cat_cd" name="u_cat_cd" value=""/>
                                                    <a href="#" class="btn btn-sm btn-outline-primary"
                                                        onclick="searchCategory.Open('ITEM',function(code,name){
                                                            $('#u_cat_cd').val(code);
                                                            $('#u_cat_nm').val(name);
                                                        });"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">배송비</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="dlv_fee_cfg" value="S" onclick="change_dlv_cfg_form('s')" id="dlv_fee_cfg1" class="custom-control-input" checked>
                                                        <label class="custom-control-label" for="dlv_fee_cfg1">쇼핑몰 설정</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="dlv_fee_cfg" value="G" onclick="change_dlv_cfg_form('g')" id="dlv_fee_cfg2" class="custom-control-input" >
                                                        <label class="custom-control-label" for="dlv_fee_cfg2">상품 개별 설정</label>
                                                    </div>
                                                    <div class="dlv_config_detail_div txt_box" id="dlv_config_detail_s_div">
                                                        유료, 배송비 2,500원(50,000원 이상 구매 시 무료)
                                                    </div>
                                                    <div class="dlv_config_detail_div" id="dlv_config_detail_g_div" style="display:none;">
                                                        <div class="flax_box">
                                                            <div class="select_box mr-1">
                                                                <select name="bae_yn" id="bae_yn" class="form-control form-control-sm search-all">
                                                                    <option value="Y" selected>유료</option>
                                                                    <option value="N">무료</option>
                                                                </select>
                                                            </div>
                                                            <div class="input_box"><input type="text" name="baesong_price" id="baesong_price" class="form-control form-control-sm search-all" style="width:100px;text-align:right;"></div>
                                                            <div class="txt_box">원</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">배송비 지불</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="dlv_pay_type" value="P" id="dlv_pay_type1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="dlv_pay_type1">선불</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="dlv_pay_type" value="F" id="dlv_pay_type2" class="custom-control-input">
                                                        <label class="custom-control-label" for="dlv_pay_type2">착불</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">적립금</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="point_cfg" value="S" id="point_cfg1" class="custom-control-input" onchange="changePointConfig(this)" checked>
                                                        <label class="custom-control-label" for="point_cfg1">쇼핑몰 설정</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="point_cfg" value="G" id="point_cfg2" class="custom-control-input" onchange="changePointConfig(this)">
                                                        <label class="custom-control-label" for="point_cfg2">상품 개별 설정</label>
                                                    </div>
                                                    <div class="point_config_detail_div txt_box" id="point_config_detail_s_div">
                                                        (지급함, 상품 가격의 {{$point_info->value}}% 적립금 지급)
                                                    </div>
                                                    <div class="point_config_detail_div" id="point_config_detail_g_div" style="display:none;">
                                                        <div class="flax_box">
                                                            <div class="select_box mr-1">
                                                                <select name="point_yn" id="point_yn" class="form-control form-control-sm search-all" disabled>
                                                                    <option value="Y" selected>지급함</option>
                                                                    <option value="N">지급안함</option>
                                                                </select>
                                                            </div>
															<div class="select_box mr-1">
																<select name="point_unit" id="point_unit" class="form-control form-control-sm search-all" disabled onchange="changePointUnit(this)">
																	<option value="W" selected>원</option>
																	<option value="P">%</option>
																</select>
															</div>
                                                            <div class="input_box"><input type="text" name="point" id="point" class="form-control form-control-sm search-all" style="width:100px;text-align:right;" disabled></div>
															<span id="point_unit_str">원</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">과세구분</th>
                                            <td>
                                                <div class="flax_box select_box">
                                                    <select name="tax_yn" id="tax_yn" class=" form-control form-control-sm search-all">
                                                        <option value="Y" selected>과세</option>
                                                        <option value="N">면세</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">원산지</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" name="org_nm" id="org_nm" class="form-control form-control-sm search-all" />
                                                </div>
                                            </td>
                                            <th>제조사</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" name="make" id="make" class="form-control form-control-sm search-all" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">옵션사용</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
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
                                            </td>
                                            <th class="required">옵션구분</th>
                                            <td>
                                                <div class="form-inline inline_input_box flax_box">
                                                    <div class="form-inline-inner text-box" style="margin-bottom:5px">
                                                        <div class="form-group flax_box">
                                                            <div class="custom-control custom-checkbox">
																<input type="hidden" id="option_kind" name="option_kind" value="" />
                                                                <input type="checkbox" name="chk_option_kind1" id="chk_option_kind1" class="custom-control-input" checked>
                                                                <label class="custom-control-label" for="chk_option_kind1">&nbsp;</label>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm" name="option_kind1" id="option_kind1" style="width:85%;" placeholder="사이즈" />
                                                        </div>
                                                    </div>
                                                    <div class="form-inline-inner text-box">
                                                        <div class="form-group flax_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="chk_option_kind2" id="chk_option_kind2" class="custom-control-input" >
                                                                <label class="custom-control-label" for="chk_option_kind2">&nbsp;</label>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm" name="option_kind2" id="option_kind2" style="width:85%;" placeholder="컬러" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">재고수량관리</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_unlimited" value="P" id="is_unlimited1" class="custom-control-input" checked="">
                                                        <label class="custom-control-label" for="is_unlimited1">수량 관리함</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-2">
                                                        <input type="radio" name="is_unlimited" value="F" id="is_unlimited2" class="custom-control-input">
                                                        <label class="custom-control-label" for="is_unlimited2">수량 관리 안함(무한재고)</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>재입고 알림</th>
                                            <td>
												<div class="flex_box form-inline txt_box">
													<div class="flax_box select_box" style = "margin-right: 10px;">
														<select name="restock_yn" id="restock_yn" class=" form-control form-control-sm search-all">
															<option value="Y" selected>재입고함</option>
															<option value="N">안함</option>
														</select>
													</div>
													<x-tool-tip>
														<x-slot name="arrow">top</x-slot>
														<x-slot name="align">left</x-slot>
														<x-slot name="html">
															품절 시 "<strong>재입고알림</strong>" 버튼이 노출됩니다.
														</x-slot>
													</x-tool-tip>
												</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="show_layout px-sm-3">
    <div class="card shadow">
        <form method="post" name="save" id ="insert_form" action="/partner/stock/stk01">
            @csrf
            <textarea style="display:none" name="form_str" id="csvResult"></textarea>
            <div class="card-body shadow">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">
                            <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="commander('save')">저장</a>
                            <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="commander('del');">삭제</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="width:100%;min-height:400px;" class="ag-theme-balham"></div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- <style>
    #toc-content {
    display: none;
    }
    #toc-toggle {
    cursor: pointer;
    }
    #toc-toggle:hover {
    text-decoration: underline;
    }
</style>
<div class="card-title">
    <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle" id="toc-toggle" onclick="openCloseToc()"> Help</h6>
    <h3>클릭해 주세요</h3>
</div>
<ul id="toc-content">
<li>취소 클릭시 상품 및 일괄 등록 폼이 초기화 됩니다.</li> 
<li>수정할수 있는 항목: 스타일넘버, 상품명, 판매가, 홍보글/단축명, 상품상세, 제품사양, 예약/배송 (등록하려는 엑셀 파일 필드 Ctrl+C, Ctrl+V)</li> 
<li>저장 클릭시 상품 선택(체크박스 체크)과 무관하게 일괄 등록 됩니다.</li> 
<li>옵션 등록 도움말</li> 
<li>단일 옵션 입력 시 "옵션구분" 입력란에 사이즈 또는 size 과 같이 옵션구분명을 입력합니다. "옵션1" 모두 필수 입력.</li> 
<li>멀티 옵션 입력 시 "옵션구분" 입력란에 사이즈^컬러 또는 size^color과 같이 공백 없이 "^"로 연결하여 옵션구분명을 입력합니다. "옵션1", "옵션2" 모두 필수 입력.</li> 
<li>단일 옵션 입력 시 : S,M,L 또는 검정,파랑,노랑,초록 과 같이 공백 없이 쉼표(,)로 연결하여 "옵션1" 항목에 입력합니다.</li> 
<li>멀티 옵션 입력 시 : "옵션1" 항목에 검정,파랑,노랑,초록, "옵션2" 항목에 S,M,L 와 같이 공백 없이 쉼표(,)로 연결하여 입력합니다.</li>
<li>"옵션1", "옵션2" 항목에 입력된 옵션은 "검정^S","검정^M","검정^L","파랑^S", .. 와 같은 형태로 옵션이 등록되며, 쇼핑몰에서는 멀티옵션으로 표시됩니다.</li> 
<li>수량 입력 시 : 100,200,300 또는 0,0,300과 같이 공백 없이 쉼표(,)로 연결하여 "수량" 항목에 입력합니다. "옵션1" 항목을 기준으로 적용되므로 "옵션1" 항목의 갯수와 "수량" 항목의 갯수는 같아야 합니다. </li>
<li>옵션 가격 입력 시 : 100,200,300 또는 0,0,300과 같이 공백 없이 쉼표(,)로 연결하여 "옵션가격" 항목에 입력합니다. "옵션1" 항목을 기준으로 적용되므로 "옵션1" 항목의 갯수와 "옵션가격" 항목의 갯수는 같아야 합니다.</li> 
<li>옵션등록 샘플</li> 
<li>사이즈 또는 컬러 선택시 single_option.jpg</li> 
<li>컬러/사이즈 선택시 multi_option.jpg</li> 
</ul> --}}

    <script language="javascript">

		const DEFAULT_STYLE = { 'background' : 'none', 'line-height': '30px'};

		const CELL_STYLE = {
			EDIT: { 'background': '#ffff99', 'line-height': '30px'},
			OK: { 'background': 'rgb(200,200,255)' },
			FAIL: { 'background': 'rgb(255,200,200)' }
		};
		
        var columns = [
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
					{field: "goods_sh", headerName: "시중가", type: 'numberType', editable: true, cellStyle: CELL_STYLE.EDIT},
					{field: "price", headerName: "판매가", type: 'numberType', editable: true, cellStyle: CELL_STYLE.EDIT},
					{field: "wonga", headerName: "원가", width: 60, type: 'numberType', editable: true, cellStyle: CELL_STYLE.EDIT},
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

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;

        $(document).ready(function() {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(250);
            //Search();
        });

		// 판매가 원가 작성할 때 마진율값 자동입력
		async function onCellValueChanged(params) {
			if (params.oldValue == params.newValue) return;
			let row = params.data;

			if (row.price != null && row.wonga != null ) row.margin_rate = ((row.price - row.wonga)/row.price)*100;

			await gx.gridOptions.api.applyTransaction({
				update: [{...row}]
			});
		}

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
			const arr = [];
			for (let i = 0; i < data.length; i++) {
				let row = data[i];
				const response = await axios({
					url: '/partner/product/prd06/enroll2',
					method: 'post',
					data: { row: row }
				});
				const { result, msg } = response.data;
				row = { ...row, msg: msg, result: result };
				arr.push(row);
			}
			gx.gridOptions.api.setRowData([]);
			await gx.gridOptions.api.applyTransaction({ add : arr });
			setTimeout(function() {
				alert("상품일괄등록이 완료되었습니다.");
			}, 1000);

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
				$("input[name='option_kind1']").val("컬러");
				$("input[name='option_kind1']").focus();
			} else {
				$("input[name='option_kind1']").attr("disabled", true);
				$("input[name='option_kind1']").attr("readonly", true);
				$("input[name='option_kind1']").val("컬러");
			}
		});
		$("#chk_option_kind2").bind("change", () => {
			if ($("#chk_option_kind2")[0].checked) {
				$("input[name='option_kind2']").attr("disabled", false);
				$("input[name='option_kind2']").attr("readonly", false);
				$("input[name='option_kind2']").val("사이즈");
				$("input[name='option_kind2']").focus();
			} else {
				$("input[name='option_kind2']").attr("disabled", true);
				$("input[name='option_kind2']").attr("readonly", true);
				$("input[name='option_kind2']").val("사이즈");
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
				$(".point_config_detail_div").css("display","none");
				$("#point_config_detail_"+radio.value+"_div").css("display","inline");
				
				$("#point_yn").attr("disabled", true);
				$("#point_yn").val("Y");
				$("#point_unit").attr("disabled", true);
				$("#point_unit").val("W");
				$("#point").attr("readonly", true);
				$("#point").attr("disabled", true);
				$("#point").val("");
			} else if (radio.value == "G") {
				$(".point_config_detail_div").css("display","block");
				$("#point_config_detail_"+radio.value+"_div").css("display","inline");

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

				if (row.goods_nm_eng == undefined || row.goods_nm_eng == "") {
					stopEditing();
					alert("상품영문명을 입력해 주세요.");
					startEditingCell(row.idx, 'goods_nm_eng');
					return false;
				}

				if (row.goods_sh == undefined || row.goods_sh == "" || row.goods_sh <= 0 || !isNumVal(parseInt(row.goods_sh))) {
					stopEditing();
					alert("시중가를 입력해 주세요.");
					startEditingCell(row.idx, 'goods_sh');
					return false;
				}

				if (row.price == undefined || row.price == "" || row.price <= 0 || !isNumVal(parseInt(row.price))) {
					stopEditing();
					alert("판매가를 입력해 주세요.");
					startEditingCell(row.idx, 'price');
					return false;
				}

				if (row.wonga == undefined || row.wonga == "" || row.wonga <= 0) {
					stopEditing();
					alert("원가를 입력해 주세요.");
					startEditingCell(row.idx, 'wonga');
					return false;
				}

				// if (row.option_kind == undefined || row.option_kind == "") {
				//     stopEditing();
				//     alert("옵션구분을 입력해 주세요.");
				//     startEditingCell(row.idx, 'option_kind');
				//     return false;
				// }
				var a_opt_kind = row.option_kind.split("^");

				if (a_opt_kind.length > 2) {
					stopEditing();
					alert("옵션구분은 최대 2개까지만 입력 가능합니다.\nex) 사이즈^컬러");
					startEditingCell(row.idx, 'option_kind');
					return false;
				}

				// if (a_opt_kind.length == 2) {
				//     if (row.opt1 == undefined || row.opt1 == '') {
				//         stopEditing();
				//         alert("옵션1 항목에 옵션값을 입력 하십시오.");
				//         startEditingCell(row.idx, 'opt1');
				//         return false;
				//     }

				//     if (row.opt2 == undefined || row.opt2 == '') {
				//         stopEditing();
				//         alert("옵션2 항목에 옵션값을 입력 하십시오.");
				//         startEditingCell(row.idx, 'opt2');
				//         return false;
				//     }
				// } else if (a_opt_kind.length == 1 && row.option_kind != "NONE") {
				//     if (row.opt1 == undefined || row.opt1 == '') {
				//         stopEditing();
				//         alert("옵션1 항목에 옵션값을 입력 하십시오.");
				//         startEditingCell(row.idx, 'opt1');
				//         return false;
				//     }
				// }

				// if (row.opt_qty == undefined || row.opt_qty == "") {
				//     stopEditing();
				//     alert("수량을 입력해 주세요.");
				//     startEditingCell(row.idx, 'opt_qty');
				//     return false;
				// }
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
		
		const apply = () => { // 적용

			if (!applyValidation()) return false;

			let row = {};
			prd_cnt			    = _("#prd_cnt").value; // 상품수

			row.com_type		= "{{$com_info->com_type}}";	// 업체타입
			row.com_id			= "{{$com_info->com_id}}"; // 업체아이디
			row.com_nm			= "{{$com_info->com_nm}}"; // 업체명
			row.opt_kind_cd		= _("#op_cd").value;	// 품목
			row.brand			= _("#brand_cd").value; // 브랜드
			row.rep_cat_cd		= _("#rep_cat_cd").value; // 대표카테고리
			row.u_cat_cd		= _("#u_cat_cd").value; // 용도카테고리

			// 배송비설정
			if (document.f1.dlv_fee_cfg[1].checked) {
				row.dlv_fee_cfg = "G";
			} else {
				row.dlv_fee_cfg = "S";
			}

			let bae_yn  = document.getElementById("bae_yn");

			row.dlv_fee_yn		= (bae_yn.options[bae_yn.selectedIndex].value); // 배송시 유료 유무
			row.baesong_price	= _("#baesong_price").value; // 배송비

			// 적립금 설정
			if (document.f1.point_cfg[1].checked) {
				row.point_cfg = "G";
			} else {
				row.point_cfg = "S";
			}
			let point_yn  = document.getElementById("point_yn");
			let point_unit  = document.getElementById("point_unit");
			
			row.point_yn		= (point_yn.options[point_yn.selectedIndex].value); // 적립금 유무
			row.point_unit		= (point_unit.options[point_unit.selectedIndex].value);  // 적립단위
			row.point			= _("#point").value; // 적립율
			
			if (row.point_cfg == "S") {
				row.point = "{{$order_point_ratio}}";
				row.point_unit = "P";
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

			let tax_yn  = document.getElementById("tax_yn");
			let restock_yn  = document.getElementById("restock_yn");
			
			row.option_kind	= _("#option_kind").value;
			row.tax_yn		= (tax_yn.options[tax_yn.selectedIndex].value);		// 과세구분
			row.restock_yn	= (restock_yn.options[restock_yn.selectedIndex].value);	// 재입고 설정

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

		const changeOptionUse = (obj) => {
			const used = obj.value;

			if (used == 'Y') {
				$("input[name='option_kind1']").attr("disabled", false);
				$("input[name='option_kind1']").val("컬러");
				$("input[name='chk_option_kind1']").attr("disabled", false);
				$("input[name='chk_option_kind1']").attr("checked", true);
				$("input[name='option_kind2']").attr("disabled", false);
				$("input[name='option_kind2']").val("사이즈");
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
		
		function insert(){
            $('#csvResult').val(function(){
                return gx.gridOptions.api.getDataAsCsv(
                    {
                        suppressQuotes: "none",
                        columnSeparator: ",",
                        customHeader: "",
                        customFooter: "",
                    }
                );
            });

            $.ajax({
                async: true,
                type: 'post',
                url: '/partner/product/prd06/bundle/',
                data: $("#insert_form").serialize(),
                success: function (data) {

                    //console.log(data);

                    alert("상품이 등록되었습니다.");
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        }

        function change_dlv_cfg_form(value){
            $(".dlv_config_detail_div").css("display","none");
            $("#dlv_config_detail_"+value+"_div").css("display","inline");
        }
		
		const applyValidation = () => {

			const prd_cnt = document.f1.prd_cnt.value;
			if (!prd_cnt || prd_cnt < "0" || prd_cnt == "0") {
				alert("상품수를 입력해 주세요.");
				document.f1.prd_cnt.focus();
				return false;
			}

			if (document.f1.op_cd.value == "") {
				alert("품목을 입력해 주세요.");
				document.f1.item.focus();
				return false;
			}

			if (document.f1.brand_cd.value == "") {
				alert("브랜드를 입력해 주세요.");
				document.querySelector('.sch-brand').click();
				return false;
			}

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
			if (document.f1.dlv_fee_cfg[1].checked) {
				if (document.f1.bae_yn.value == "") {
					alert("배송비여부를 입력해 주세요.");
					return false;
				}
			}
			if (document.f1.point_cfg.value == "") {
				alert("적립금설정을 선택해 주세요.");
				return false;
			}
			if (document.f1.point_cfg[1].value == "") {
				if (document.f1.point_yn.value == "") {
					alert("적립금여부를 입력해 주세요.");
					return false;
				}
			}

			if (document.f1.tax_yn.value == "") {
				alert("과세구분을 입력해 주세요.");
				return false;
			}

			return true;

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

    </script>
@stop
