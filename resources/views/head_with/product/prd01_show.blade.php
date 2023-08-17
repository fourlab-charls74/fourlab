@extends('head_with.layouts.layout-nav')
@section('title','상품')
@section('content')

<style>
    select.select_cat
    {
        width:100%;
        height:100px;
        border:0px;
        overflow:auto;
        background-image:none;
    }
	select.select_cat option
	{
		color: #556ee6;
		height:23px;
		padding:5px 10px;
		margin:5px 0px;
		background-color: transparent;
	}
    .red {
        color: red;
    }
    .sub_title {
        display: flex;
        align-items: center;
        font-size: 0.8rem;
        font-weight: 500;
        min-width: 60px;
    }
    .sub_title::before {
        content: "ㆍ ";
    }
    .sub_title.sub::before {
        content: '';
    }

    .img {
        height: 40px;
    }

    /* 기본옵션 ag grid 3단 가운데 정렬 css 적용 */
    .basic-option .ag-header-row.ag-header-row-column-group + .ag-header-row.ag-header-row-column > .bizest.ag-header-cell {
        transform: translateY(-65%);
        height: 320%;
    }

    /* 옵션 컬럼 셀 잠금 */
    .locked-cell.ag-cell:focus{  border:none !important;  outline: none; border-right: 1px solid #bdc3c7 !important }
    .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-left {
        border-left-color: transparent;
    }
    .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-top {
        border-top-color: transparent;
    }
    .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-right {
        border-right-color: transparent;
    }
    .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-bottom {
        border-bottom-color: transparent;
    }

    .ag-theme-balham .ag-ltr .locked-cell.ag-cell-range-single-cell,
    .ag-theme-balham .ag-ltr .locked-cell.ag-cell-range-single-cell.ag-cell-range-handle,
    .ag-theme-balham .ag-ltr .ag-has-focus .locked-cell.ag-cell-focus:not(.ag-cell-range-selected),
    .ag-theme-balham .ag-rtl .ag-cell-range-single-cell, .ag-theme-balham .ag-rtl .locked-cell.ag-cell-range-single-cell.ag-cell-range-handle,
    .ag-theme-balham .ag-rtl .ag-has-focus .locked-cell.ag-cell-focus:not(.ag-cell-range-selected) {
        border: 1px solid transparent;
    }

    .table-box th {
        background : #f5f5f5;
        border: 1px solid #ddd;
    }

    .table-box td {
        width: 300px;
        border: 1px solid #ddd;
    }


</style>

    <script type="text/javascript" src="/handle/editor/editor.js"></script>
    <script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
    <script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                @if( $type == '')
                <h3 class="d-inline-flex">상품수정</h3>
                @elseif ( $type == "create")
                <h3 class="d-inline-flex">상품등록</h3>
                @endif
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 상품 - {{ $goods_no }}</span>
                </div>
            </div>
            <div>
                @if( $type == '' )
                <a href="#" onclick="pop_prd_page()" class="btn btn-sm btn-outline-primary shadow-sm">
                <i class="bx bx-link-external mr-1"></i>상품조회
                </a>
                <a href="#" class="btn btn-sm btn-outline-primary shadow-sm copy-btn">
                    <i class="bx bx-copy-alt mr-1"></i>복사
                </a>
                @endif
                <a href="#" class="btn btn-sm btn-primary shadow-sm save-btn"><i class="bx bx-save mr-1"></i>저장</a>
            </div>
        </div>
        <form name="f1" id="f1" onsubmit="return false;">
            @csrf
			<input type="hidden" name="goods_no" value="{{@$goods_no}}">
            <input type="hidden" name="goods_sub" value="0">
			<input type="hidden" name="is_def_d_category" value="0">				<!-- 일반카테고리를 선택했는지 -->
			<input type="hidden" name="d_category_s" id="d_category_s" value="">	<!-- 전시카테고리 string 으로 넘기기 위해서 -->
			<input type="hidden" name="u_category_s" id="u_category_s" value="">	<!-- 용도카테고리 string 으로 넘기기 위해서 -->

			<div class="card_wrap aco_card_wrap">
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">카테고리 연결</a>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mt-2" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="main-tab" data-toggle="tab" href="#main" role="tab" aria-controls="main" aria-selected="true">전시 카테고리</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="usage-tab" data-toggle="tab" href="#usage" role="tab" aria-controls="usage" aria-selected="false">용도 카테고리</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade" id="main" role="tabpanel" aria-labelledby="main-tab">
                                <div class="card shadow mb-1">
                                    <ul class="row category_list">
                                        <li class="col-lg-6">
                                            <dl>
                                                <dt class="d-flex align-items-center justify-content-between">
                                                    <div class="required">
                                                        대표 카테고리
                                                    </div>
                                                    <div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
                                                        <button type="button" class="btn btn-sm btn-primary btn-rep-add"  data-toggle="tooltip" data-placement="top" title="" data-original-title="선택" data-toggle="modal" data-target="#category_list_modal">
                                                            <i class="bx bx-search-alt-2"></i>
                                                        </button>
                                                    </div>
                                                </dt>
                                                <dd style="white-space:normal;">
                                                    <div class="cate_scroll">
                                                        <ul>
                                                            <li style="word-break:keep-all">
                                                                <input type="hidden" name="rep_cat_cd" id="rep_cat_cd" value="{{ @$goods_info->rep_cat_cd }}">
                                                                <a href="#" id="txt_rep_cat_nm" style="cursor:default;">{{ @$goods_info->rep_cat_nm }}</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </dd>
                                            </dl>
                                        </li>
                                        <li class="col-lg-6 mt-2 mt-lg-0">
                                            <dl>
                                                <dt class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        전시 카테고리
                                                    </div>
                                                    <div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
                                                        <button type="button" class="btn btn-sm btn-primary btn-display-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가"  data-toggle="modal" data-target="#category_list_modal">
                                                            <i class="bx bx-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-display-display" data-toggle="tooltip" data-placement="top" title="" data-original-title="활성">
                                                            <i class="far fa-check-circle"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-display-hidden" data-toggle="tooltip" data-placement="top" title="" data-original-title="비활성">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-display-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="삭제">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </dt>
                                                <select id="category_select_display" name="d_category" class="select_cat" size="4"></select>
                                            </dl>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="usage" role="tabpanel" aria-labelledby="usage-tab">
                                <div class="card shadow mb-1">
                                    <ul class="row category_list">
                                        <li class="col-lg-12 mt-2 mt-lg-0">
                                            <dl>
                                                <dt class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        용도 카테고리
                                                    </div>
                                                    <div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
                                                        <button type="button" class="btn btn-sm btn-primary btn-item-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가"  data-toggle="modal" data-target="#category_list_modal">
                                                            <i class="bx bx-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-item-display" data-toggle="tooltip" data-placement="top" title="" data-original-title="활성">
                                                            <i class="far fa-check-circle"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-item-hidden" data-toggle="tooltip" data-placement="top" title="" data-original-title="비활성">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-item-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="삭제">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </dt>
                                                <select id="category_select_item" name="u_category" class="select_cat" size="4"></select>
                                            </dl>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card_wrap aco_card_wrap">
                    <div class="card shadow">
                        <div class="card-header mb-0">
                            <a href="#">상품 세부 정보</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-box mobile">
                                        @if( $type == '' )
                                        <div class="img_box cum_slider_cont p-4">
                                            <img src="{{@$goods_info->img}}" alt="{{@$goods_info->goods_nm}}">
                                        </div>
                                        <div class="cum_slider_thum_wrap">
                                            <div class="sd_prev pop_sd_btn bg-secondary"><i class="bx bx-left-arrow"></i></div>
                                            <div class="inbox">
                                                <ul class="cum_slider_thum">
                                                    <li>
                                                        <a href="#" class="active"><img src="{{@$goods_images[0]}}"></a>
                                                    </li>
                                                    @for ($i = 1; $i < count($goods_images); $i++)
                                                        <li>
                                                            <a href="#"><img src="{{@$goods_images[$i]}}" alt="22"></a>
                                                        </li>
                                                    @endfor
                                                </ul>
                                            </div>
                                            <div class="sd_next pop_sd_btn bg-secondary"><i class="bx bx-right-arrow"></i></div>
                                        </div>
                                        @else
                                        <!-- 상품 복사 및 상품 등록 페이지일경우 표기됨. -->
                                        <h3 class="pt50 text-center">상품을 먼저 등록 하신 후<br>이미지를 등록할 수 있습니다.</h3>
                                        @endif
                                        <div class="text-center mt-2">
                                            <button type="button" id="img-setting" class="btn btn-primary waves-effect waves-light" data-no="{{$goods_no}}">
                                                <i class="bx bx-cog font-size-14 align-middle"></i> 이미지 관리
                                            </button>
                                            <button type="button" id="img-show" class="btn btn-success waves-effect waves-light" data-no="{{$goods_no}}"  onclick="openFrontUrl()">
                                                <i class="bx bx-images font-size-14 align-middle"></i> 상품 보기
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 기본정보</div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box mobile">  
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <th>품번</th>
                                                    <td>
                                                        <div class="form-inline">
                                                            <input type="text" class="form-control form-control-sm search-all w-100" name="prd_cd_p" id="prd_cd_p" maxlength="100" value="" />
                                                        </div>
                                                    </td>
                                                    <th class="required" >상품명</th>
                                                    <td>
                                                        <div class="form-inline">
                                                            <input type="text" class="form-control form-control-sm search-all w-100" name="goods_nm" id="goods_nm" maxlength="100" value="{{ @$goods_info->goods_nm  }}" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th  >상품명(영문)</th>
                                                    <td>
                                                        <div class="input_box">
                                                            <input type="text" class="form-control form-control-sm search-all w-100" name="goods_nm_eng" id="goods_nm_eng" maxlength="100" value="{{ @$goods_info->goods_nm_eng  }}" />
                                                        </div>
                                                    </td>
                                                    <th class="required">업체</th>
                                                    <td>
                                                        <div class="input_box wd300">
                                                            <div class="form-inline inline_btn_box">
                                                                <input type="hidden" name="com_type" id="com_type" value="{{ @$goods_info->com_type }}" >
                                                                <input type="hidden" name="margin_type" id="margin_type" value="{{ @$goods_info->margin_type }}">
                                                                <input type="text" id="com_nm" name="com_nm" value="{{ @$goods_info->com_nm }}" class="form-control form-control-sm  btn-select-company" style="width:70%;">
                                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary btn-select-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                                <input type="text" name="com_id" id="com_id" value="{{ @$goods_info->com_id }}" class="form-control form-control-sm ml-1" style="width:28%;" readonly>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>제조사/수입자</th>
                                                    <td>
                                                        <div class="input_box wd300">
                                                            <input type='text' class="form-control form-control-sm search-all" name='make' id='make' value='{{ @$goods_info->make  }}' />
                                                        </div>
                                                    </td>
                                                    <th class="required">상품구분</th>
                                                    <td>
                                                        <div class="flex">
                                                            <select name="goods_type" id="goods_type" class="form-control form-control-sm d-inline-block"
                                                                style="width: 85%; margin-right: 15px;"
                                                            >
                                                                <option value="">==상품구분==</option>
                                                                @foreach($goods_types as $goods_type )
                                                                <option
                                                                value="{{$goods_type->code_id}}"
                                                                @if ($goods_type->code_id === @$goods_info->goods_type) selected @endif
                                                                >
                                                                    {{ $goods_type->code_val }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            <x-tool-tip>
                                                                <x-slot name="arrow">top</x-slot>
                                                                <x-slot name="align">left</x-slot>
                                                                <x-slot name="html">
                                                                    위탁판매 : 입점사가 상품에 대한 판매를 위탁하고 일정한 수수료를 지급하는 상품<br/>
                                                                    위탁매입 : 입점사가 상품에 대한 <u>재고를 선 제공 후, 판매된 매출에 대하여 정산</u>하는 상품<br/>
                                                                    ※ <b>매입, 위탁매입 상품은 보유재고 관리가 가능</b>합니다.
                                                                </x-slot>
                                                            </x-tool-tip>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>입고창고</th>
                                                    <td>
                                                        <div class="input_box wd300">
                                                            <input type='text' class="form-control form-control-sm search-all" name='goods_location' id='goods_location' value='{{ @$goods_info->goods_location }}'>
                                                        </div>
                                                    </td>
                                                    <th class="required">브랜드</th>
                                                    <td>
                                                        <div class="wd300">
                                                            <div class="form-inline inline_btn_box">
                                                                <input type="text" name="brand_nm" id="brand_nm" value="{{@$goods_info->brand_nm}}" class="form-control form-control-sm ac-brand" style="width:70%;">
                                                                <input type="text" name="brand_cd" id="brand_cd" value="{{@$goods_info->brand}}" class="form-control form-control-sm ml-1" style="width:28%;" readonly>
                                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="required">품목</th>
                                                    <td>
                                                        <div class="wd300">
                                                            <select class="form-control form-control-sm search-all" name="opt_kind_cd" id="opt_kind_cd">
                                                                <option value="">선택하세요.</option>
                                                                @foreach($opt_cd_list as $opt_cd)
                                                                <option value="{{$opt_cd->cd}}" {{ (@$goods_info->opt_kind_cd == $opt_cd->cd) ? "selected" : "" }}>{{$opt_cd->val}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <th class="required">스타일넘버</th>
                                                    <td>
                                                        <div class="input_box wd300">
                                                            <input type='text' class="form-control form-control-sm ac-style-no search-all" name='style_no' id='style_no' value='{{ @$goods_info->style_no  }}'>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="required">원산지</th>
                                                    <td>
                                                        <div class="input_box wd300">
                                                            <input type='text' class="form-control form-control-sm search-all" name='org_nm' id='org_nm' value='{{ @$goods_info->org_nm  }}' />
                                                        </div>
                                                    </td>
                                                    <th>정상가</th>
                                                    <td>
                                                        <div class="txt_box flax_box">
                                                            <input
                                                            type='text'
                                                            class="form-control form-control-sm search-all text-right"
                                                            name='goods_sh'
                                                            id='goods_sh'
                                                            value='{{@number_format(@$goods_info->goods_sh)}}'
                                                            style="width:93%"
                                                            >
                                                            <div class="txt_box ml-1">원</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="required">판매가</th>
                                                    <td>
                                                        <div class="txt_box flax_box">
                                                            <input
                                                                type='text'
                                                                style="width:70%"
                                                                class="form-control form-control-sm search-all text-right"
                                                                name='price'
                                                                id='price'
                                                                value='{{ @number_format(@$goods_info->price) }}'
                                                                @if (@$goods_info->sale_yn == "Y") readonly @endif
                                                            >
                                                            <div class="txt_box ml-1 mr-2">원</div>
                                                            @if ($type !== 'create')
                                                            <div class="custom-control custom-checkbox form-check-box">
                                                                <input type='hidden' name="sale_yn" value='{{ @$goods_info->sale_yn }}' />
                                                                <input type="checkbox" class="custom-control-input" id="sale_yn" {{ (@$goods_info->sale_yn == "Y") ? "checked" : "" }}>
                                                                <label class="custom-control-label" for="sale_yn">세일</label>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <th class="required">원가</th>
                                                    <td class="pb-1">
                                                        <div class="txt_box flax_box">
                                                            <input
                                                            type='text'
                                                            class="form-control form-control-sm search-all text-right"
                                                            name='wonga'
                                                            id='wonga'
                                                            value='{{@number_format(@$goods_info->wonga)}}'
                                                            @if( $type == "" )
                                                            readonly
                                                            @endif
                                                            style="width:93%"
                                                            >
                                                            <div class="txt_box ml-1">원</div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="font-size-12 mt-1 mb-0">
                                                                *원가는 자동계산됩니다.
                                                            </p>
                                                            @if (@$goods_info->goods_type === 'S')
                                                                <a href="javascript:void(0);" class="txt_box ml-1" onclick="return openWongaPopup();">(평균 {{ number_format(@$avg_wonga) }}원)</a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @if ($type !== 'create')
                                                <tr class="sale_control" style="@if (@$goods_info->sale_yn != 'Y') display: none; @endif">
                                                    <th >세일</th>
                                                    <td>
                                                        <div class=" d-flex mb-2">
                                                            <span class="sub_title mr-2">구분</span>
                                                            <select name="sale_type" id="sale_type" class="form-control form-control-sm">
                                                                <option value="">==세일구분==</option>
                                                                @foreach (@$goods_info->sale_types as $sale_type)
                                                                    <option value="{{ $sale_type['key'] }}" @if ($sale_type['key'] === @$goods_info->sale_type) selected @endif>{{ $sale_type['value'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class=" d-flex mb-2">
                                                            <span class="sub_title mr-2">정상가</span>
                                                            <input type='text' value='{{ number_format(@$goods_info->normal_price) }}' class="form-control form-control-sm search-all text-right" disabled/>
                                                            <div class="txt_box ml-1 mr-2">원</div>
                                                            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm cancel-sale-btn" style="width: 180px;">세일취소</a>
                                                        </div>
                                                        <div class=" d-flex mb-2">
                                                            <span class="sub_title mr-2">세일</span>
                                                            <input type='text' name='sale_rate' value='{{ @$goods_info->sale_rate }}' class="form-control form-control-sm search-all text-right" numberOnly/>
                                                            <div class="txt_box ml-1">%</div>
                                                        </div>
                                                        <div class=" d-flex mb-2 pl-5">
                                                            <span class="sub_title sub mr-2">(세일가</span>
                                                            <input type='text' name='sale_price' value='{{ number_format(@$goods_info->sale_price) }}' class="form-control form-control-sm search-all text-right" numberOnly/>
                                                            <div class="txt_box ml-1">원)</div>
                                                        </div>
                                                        <!-- 상품구분이 위탁판매인 경우에만 보여지도록 설정 -->
                                                        @if (@$goods_info->goods_type === 'P')
                                                        <div class=" d-flex mb-2">
                                                            <span class="sub_title mr-2">마진율</span>
                                                            <input type='text' name='sale_margin' value='{{ @$goods_info->sale_margin }}' class="form-control form-control-sm search-all text-right" readonly />
                                                            <div class="txt_box ml-1">%</div>
                                                        </div>
                                                        <div class=" d-flex mb-2 pl-5">
                                                            <span class="sub_title sub mr-2">(정상원가</span>
                                                            <input type='text' name='normal_wonga' value='{{ number_format(@$goods_info->normal_wonga) }}' class="form-control form-control-sm search-all text-right" readonly />
                                                            <div class="txt_box ml-1">원)</div>
                                                        </div>
                                                        <div class=" d-flex mb-2 pl-5">
                                                            <span class="sub_title sub mr-2">(세일원가</span>
                                                            <input type='text' name='sale_wonga' value='{{ number_format(@$goods_info->sale_wonga) }}' class="form-control form-control-sm search-all text-right" readonly />
                                                            <div class="txt_box ml-1">원)</div>
                                                        </div>
                                                        @endif
                                                        
                                                    </td>
                                                    <th >세일 기간</th>
                                                    <td>
                                                        <div class=" d-flex mb-2">
                                                            <span class="sub_title mr-2">기간</span>
                                                            <div class="custom-control custom-checkbox form-check-box">
                                                                <input type="hidden" name="sale_dt_yn" value='{{ $goods_info->sale_dt_yn }}}' />
                                                                <input type="checkbox" id="sale_dt_yn" class="custom-control-input" @if (@$goods_info->sale_dt_yn === 'Y') checked @endif />
                                                                <label class="custom-control-label" for="sale_dt_yn">기간 사용</label>
                                                            </div>
                                                        </div>
                                                        <div class=" d-flex pl-5">
                                                            <div class="docs-datepicker form-inline-inner" id="sale_s_dt">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control form-control-sm docs-date" name="sale_s_dt" autocomplete="off" @if (@$goods_info->sale_dt_yn !== 'Y') disabled @endif />
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger sale_s_dt_btn p-0 pl-2 pr-2" @if (@$goods_info->sale_dt_yn !== 'Y') disabled @endif>
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="docs-datepicker-container"></div>
                                                            </div>
                                                            <div class="form-inline-inner d-flex align-items-center ml-2" style="width: 40%;">
                                                                <select name="sale_s_dt_tm" class="form-control form-control-sm select-time" @if (@$goods_info->sale_dt_yn !== 'Y') disabled @endif></select>
                                                                <span class="pl-1">시</span>
                                                            </div>
                                                        </div>
                                                        <div class="wd300 d-flex pl-5 justify-content-center">
                                                            <span class="text_line">~</span>
                                                        </div>
                                                        <div class="wd300 d-flex pl-5">
                                                            <div class="docs-datepicker form-inline-inner" id="sale_e_dt">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control form-control-sm docs-date" name="sale_e_dt" autocomplete="off" @if (@$goods_info->sale_dt_yn !== 'Y') disabled @endif />
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger sale_e_dt_btn p-0 pl-2 pr-2" @if (@$goods_info->sale_dt_yn !== 'Y') disabled @endif>
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="docs-datepicker-container"></div>
                                                            </div>
                                                            <div class="form-inline-inner d-flex align-items-center ml-2" style="width: 40%;">
                                                                <select name="sale_e_dt_tm" class="form-control form-control-sm select-time" @if (@$goods_info->sale_dt_yn !== 'Y') disabled @endif></select>
                                                                <span class="pl-1">시</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th class="required">마진율</th>
                                                    <td>
                                                        <div class="txt_box flax_box">
                                                            <input
                                                            type='text'
                                                            id="margin"
                                                            name="margin"
                                                            class="form-control form-control-sm search-all text-right"
                                                            value='{{ round(@$goods_info->prf, 2) }}'
                                                            @if ($type === '') readonly @endif
                                                            style="width:93%";
                                                            >
                                                            <div class="txt_box ml-1">%</div>
                                                        </div>
                                                    </td>
                                                    <th class="required">과세구분</th>
                                                    <td>
                                                        <div class="wd300">
                                                            <select name="tax_yn" id="tax_yn" class="form-control form-control-sm search-all">
                                                                <option value="">==과세구분==</option>
                                                                <option value="Y" {{ (@$goods_info->tax_yn== 'Y') ? "selected" : "" }}>과세</option>
                                                                <option value="N" {{ (@$goods_info->tax_yn== 'N') ? "selected" : "" }}>비과세</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 전시정보</div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <th class="required">전시상태</th>
                                                    <td>
                                                        <div class="txt_box flax_box">
                                                            <select name="sale_stat_cl" id="sale_stat_cl" class="form-control form-control-sm" style="width:61%; margin-right:7px">
                                                                <option value="">전시상태</option>
                                                            @foreach ($goods_stats as $goods_stat)
                                                                <option
                                                                    value="{{$goods_stat->code_id}}"
                                                                    @if ($goods_stat->code_id === @$goods_info->sale_stat_cl ) selected @endif
                                                                >
                                                                    {{ $goods_stat->code_val }}
                                                                </option>
                                                            @endforeach
                                                            </select>
                                                            <div class="custom-control custom-checkbox form-check-box mr-1">
                                                                <input type="checkbox" class="custom-control-input" value="Y" id="restock" {{ (@$goods_info->restock_yn=="Y") ? "checked" : "" }}>
                                                                <label class="custom-control-label" for="restock">재 입고함</label>
                                                            </div>
                                                            <x-tool-tip>
                                                                <x-slot name="arrow">top</x-slot>
                                                                <x-slot name="align">left</x-slot>
                                                                <x-slot name="html">
                                                                    품절 시 "<b>재입고알림</b>" 버튼이 노출됩니다.
                                                                </x-slot>
                                                            </x-tool-tip>
                                                        </div>
                                                    </td>
                                                    <th >상단 홍보글</th>
                                                    <td>
                                                        <div class="input_box">
                                                            <input type="text" name="head_desc" class="form-control form-control-sm search-all" value="{{ @$goods_info->head_desc }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th >하단 홍보글</th>
                                                    <td >
                                                        <div class="input_box">
                                                            <input type="text" class="form-control form-control-sm search-all" name="ad_desc" id="ad_desc" value="{{ @$goods_info->ad_desc }}"/>
                                                        </div>
                                                    </td>
                                                    <th class="required">MD선택</th>
                                                    <td>
                                                        <div class="wd300">
                                                            <input type="hidden" name="md_nm" id="md_nm" value="{{@$goods_info->md_nm}}">
                                                            <select name="md_id" id="md_id" class="form-control form-control-sm search-all">
                                                                <option value="">==MD선택==</option>
                                                                @foreach($md_list as $md)
                                                                    <option value="{{$md->id}}" {{ (@$goods_info->md_id == $md->id) ? "selected" : "" }}>{{$md->name}} ({{$md->id}})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>신상품 적용일</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="new_product_type" value="M" id="new_product_type1" onclick="display_new_prd_day('n')" class="custom-control-input" {{ (@$goods_info->new_product_type=="M")? "checked" : "" }} />
                                                                <label class="custom-control-label" for="new_product_type1">등록일 기준</label>
                                                            </div>
                                                            <div class="custom-control custom-radio mr-2">
                                                                <input type="radio" name="new_product_type" value="R" onclick="display_new_prd_day('y')" id="new_product_type2" class="custom-control-input" {{ (@$goods_info->new_product_type=="R")? "checked" : "" }}/>
                                                                <label class="custom-control-label" for="new_product_type2">직접입력</label>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker form-inline-inner" id="new_product_day" style="display:none;">
                                                            <div class="input-group">
                                                                <input type="text" style="width:90%" class="form-control form-control-sm docs-date" name="new_product_day" value="{{ @$goods_info->new_product_day }}" autocomplete="off" disable>
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                    </td>
                                                    <th>등록일시</th>
                                                    <td>
                                                        <div class="txt_box">
                                                            {{ @$goods_info->reg_dm }}
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>수정일시</th>
                                                    <td>
                                                        <div class="txt_box">
                                                            {{ @$goods_info->upd_dm }}
                                                        </div>
                                                    </td>
                                                    <th>메모</th>
                                                    <td>
                                                        <div class="input_box wd300">
                                                            <input type='text' class="form-control form-control-sm search-all" name='goods_memo' id='goods_memo' value='{{ @$goods_info->goods_memo  }}' />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 주문배송 정보</div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <th class="required">배송정보</th>
                                                    <td>
                                                        <div class="form-inline wd400">
                                                            <div class="form-inline-inner input_box">
                                                                <select name="baesong_info" id="baesong_info"  class="form-control form-control-sm search-all">
                                                                    <option value="">==배송지역==</option>
                                                                    <option value="1" @if(@$goods_info->baesong_info == "1") selected @endif>국내배송</option>
                                                                    <option value="2" @if(@$goods_info->baesong_info == "2") selected @endif>해외배송</option>
                                                                </select>
                                                            </div>
                                                            <span class="text_line">/</span>
                                                            <div class="form-inline-inner input_box">
                                                                <select name="baesong_kind" id="baesong_kind" class="form-control form-control-sm search-all">
                                                                    <option value="">==배송업체==</option>
                                                                    <option value="1" @if(@$goods_info->baesong_kind == "1") selected @endif >본사배송</option>
                                                                    <option value="2" @if(@$goods_info->baesong_kind == "2") selected @endif >입점업체배송</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <th class="required">배송비 지불</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="dlv_pay_type" value="P" id="dlv_pay_type1" class="custom-control-input" {{ (@$goods_info->dlv_pay_type!="F") ? "checked" : "" }} />
                                                                <label class="custom-control-label" for="dlv_pay_type1">선불</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="dlv_pay_type" value="F" id="dlv_pay_type2" class="custom-control-input" {{ (@$goods_info->dlv_pay_type=="F") ? "checked" : "" }} />
                                                                <label class="custom-control-label" for="dlv_pay_type2"> 착불 </label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="required">배송비</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="dlv_fee_cfg" value="S" onclick="change_dlv_cfg_form('s')" id="dlv_fee_cfg1" class="custom-control-input" @if(@$goods_info->dlv_fee_cfg == "S" || $type == "create" ) checked @endif />
                                                                <label class="custom-control-label" for="dlv_fee_cfg1">
                                                                @if( @$goods_info->dlv_policy == "C" )
                                                                    업체별 설정
                                                                @else
                                                                    쇼핑몰 설정
                                                                @endif
                                                                </label>
                                                            </div>
                                                            <div class="custom-control custom-radio mr-2">
                                                                <input type="radio" name="dlv_fee_cfg" value="G" onclick="change_dlv_cfg_form('g')" id="dlv_fee_cfg2" class="custom-control-input" @if(@$goods_info->dlv_fee_cfg == "G") checked @endif />
                                                                <label class="custom-control-label" for="dlv_fee_cfg2">상품 개별 설정</label>
                                                            </div>
                                                            <div
                                                            class="dlv_config_detail_div txt_box"
                                                            id="dlv_config_detail_s_div"
                                                            @if(@$goods_info->dlv_fee_cfg == "G") style="display:none;" @endif
                                                            >
                                                            @if( @$goods_info->dlv_policy == "C" )
                                                                업체별 설정
                                                            @else
                                                                유료, 배송비 {{@$g_dlv_fee}}원 ({{@$g_free_dlv_fee_limit}}원 이상 구매 시 무료)
                                                            @endif
                                                            </div>
                                                            <div
                                                            class="dlv_config_detail_div"
                                                            id="dlv_config_detail_g_div"
                                                            @if(@$goods_info->dlv_fee_cfg == "S" || $type == "create" ) style="display:none;" @endif
                                                            >
                                                                <div class="flax_box">
                                                                    <div class="select">
                                                                        <select name="bae_yn" class="form-control form-control-sm search-all">
                                                                            <option value="Y" @if (@$goods_info->bae_yn !== 'N') selected @endif>유료</option>
                                                                            <option value="N" @if (@$goods_info->bae_yn === 'N') selected @endif>무료</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input_box">
                                                                        <input
                                                                        type="text"
                                                                        name="baesong_price"
                                                                        id="baesong_price"
                                                                        class="form-control form-control-sm search-all"
                                                                        style="width:100px;text-align:right;"
                                                                        value="{{@number_format(@$goods_info->baesong_price)}}"
                                                                        @if (@$goods_info->bae_yn === 'N') readonly @endif
                                                                        />
                                                                    </div>
                                                                    <div class="txt_box">원</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <th class="required">적립금</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="point_cfg" value="S" onclick="change_point_cfg_form('s')" id="point_cfg1" class="custom-control-input" @if(@$goods_info->point_cfg == "S" || $type == "create" ) checked @endif />
                                                                <label class="custom-control-label" for="point_cfg1">쇼핑몰 설정</label>
                                                            </div>
                                                            <div class="custom-control custom-radio mr-2">
                                                                <input type="radio" name="point_cfg" value="G" onclick="change_point_cfg_form('g')" id="point_cfg2" class="custom-control-input" @if(@$goods_info->point_cfg == "G") checked @endif />
                                                                <label class="custom-control-label" for="point_cfg2">상품 개별 설정</label>
                                                            </div>
                                                            <div
                                                            class="point_config_detail_div txt_box"
                                                            id="point_config_detail_s_div"
                                                            @if(@$goods_info->point_cfg == "G") style="display:none;" @endif
                                                            >
                                                            지급함, 상품 가격의 {{ $g_order_point_ratio }}% 적립금 지급
                                                            </div>
                                                            <div
                                                            class="point_config_detail_div"
                                                            id="point_config_detail_g_div"
                                                            @if(@$goods_info->point_cfg == "S" || $type == "create" ) style="display:none;" @endif
                                                            >
                                                                <select class="form-control form-control-sm search-all" name='point_yn' id="point_yn">
                                                                    <option value=''>==지급 여부==</option>
                                                                    <option value="Y" @if( @$goods_info->point_yn != 'N' ) selected @endif>지급함</option>
                                                                    <option value="N" @if( @$goods_info->point_yn == 'N' ) selected @endif>지급안함</option>
                                                                </select>

                                                                <select class="form-control form-control-sm search-all" name='point_unit' id="point_unit">
                                                                    <option value=''>단위</option>
                                                                    <option value="W" @if( @$goods_info->point_uint != 'P' ) selected @endif >원</option>
                                                                    <option value="P" @if( @$goods_info->point_unit == 'P' ) selected @endif>%</option>
                                                                </select>													<!--
                                                                <div class="flax_box">
                                                                    <div class="select">
                                                                        <select name="bae_yn" class="form-control form-control-sm search-all">
                                                                            <option value="Y" @if (@$goods_info->bae_yn !== 'N') selected @endif>유료</option>
                                                                            <option value="N" @if (@$goods_info->bae_yn === 'N') selected @endif>무료</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input_box">
                                                                        <input
                                                                        type="text"
                                                                        name="baesong_price"
                                                                        id="baesong_price"
                                                                        class="form-control form-control-sm search-all"
                                                                        style="width:100px;text-align:right;"
                                                                        value="{{@number_format(@$goods_info->baesong_price)}}"
                                                                        @if (@$goods_info->bae_yn === 'N') readonly @endif
                                                                        />
                                                                    </div>
                                                                    <div class="txt_box">원</div>
                                                                </div>
                                                            //-->
                                                            </div>
                                                        </div>
                                                        <div class="input_box">
                                                            <input
                                                            type="text"
                                                            name="point"
                                                            id="point"
                                                            class="form-control form-control-sm search-all"
                                                            style="width:100px;text-align:right;"
                                                            value="{{@number_format(@$goods_info->point)}}"
                                                            @if (@$goods_info->poing_cfg != 'G') readonly @endif
                                                            />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품 옵션 관리</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th width="15%">재고 수량 관리</th>
                                            <td width="85%" colspan="3">
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_unlimited" value="N" id="is_unlimited1" class="custom-control-input" {{ (@$goods_info->is_unlimited=="N")? "checked" : "" }}>
                                                        <label class="custom-control-label" for="is_unlimited1">수량 관리함</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_unlimited" value="Y" id="is_unlimited2" class="custom-control-input" {{ (@$goods_info->is_unlimited=="Y")? "checked" : "" }}>
                                                        <label class="custom-control-label" for="is_unlimited2">수량 관리 안함 (무한재고)</label>
                                                    </div>
                                                    <x-tool-tip>
                                                        <x-slot name="arrow">top</x-slot>
                                                        <x-slot name="align">left</x-slot>
                                                        <x-slot name="html">
                                                            매입상품은 무한재고 기능을 사용할 수 없습니다.
                                                        </x-slot>
                                                    </x-tool-tip>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>옵션 사용</th>
                                            <td colspan="3">
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_option_use" value="Y" id="is_option_use_y" class="custom-control-input" {{ (@$goods_info->is_option_use=="Y")? "checked" : "" }}>
                                                        <label class="custom-control-label" for="is_option_use_y">사용</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="is_option_use" value="N" id="is_option_use_n" class="custom-control-input" @if( $type != 'create' ) {{ (@$goods_info->is_option_use=="N")? "checked" : "" }} @else checked @endif>
                                                        <label class="custom-control-label" for="is_option_use_n">사용 안함</label>
                                                    </div>
                                                    <x-tool-tip>
                                                        <x-slot name="arrow">top</x-slot>
                                                        <x-slot name="align">left</x-slot>
                                                        <x-slot name="html">
                                                            옵션 사용 항목을 변경하면, 등록된 모든 재고 수량 정보가 삭제됩니다.
                                                        </x-slot>
                                                    </x-tool-tip>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="use_option_n" style="@if( @$goods_info->is_option_use == 'Y' )display:none;@endif">
                                            <th width="15%">온라인 재고</th>
                                            <td width="35%">
                                                <div class="input_box flax_box">
                                                    <div class="input_box wd200">
                                                        <input type='text' name="goods_qty" id="goods_qty" class="form-control form-control-sm search-all" value="{{ $qty ?? 0 }}" onfocus="this.select()">
                                                    </div>
													@if( $type != 'create' )
                                                    <button type="button" class="btn-sm btn btn-secondary btn-change-qty" style="max-width:80px;width:19%;margin-left:1%;padding:0.22rem 0;">변경</button>
													@endif
                                                </div>
                                            </td>
                                            <th width="15%">보유 재고</th>
                                            <td width="35%">
                                                <div class="input_box flax_box">
                                                    <div class="wd200">{{ $wqty ?? 0 }}개</div>
													@if( $type != 'create')
                                                    <input type="button" class="btn-sm btn btn-secondary btn-qty-in" value="입고" style="max-width:80px;width:19%;margin-left:1%;padding:0.22rem 0;">
													@endif
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

						<div class="row use_option_y" style="@if( $type == 'create' || @$goods_info->is_option_use == 'N' )display:none;@endif">
							<div class="col-lg-4">
								<div class="card-body pt-2">
									<div class="card-title">
										<div class="filter_wrap">
											<div class="fl_box px-0 mx-0">
												<h6 class="m-0 font-weight-bold">총 : <span id="gd-optkind-total" class="text-primary">0</span> 건</h6>
											</div>
											<div class="fr_box">
											<a href="#" class="btn btn-sm btn-primary shadow-sm option-kind-add-btn"><span class="fs-12">추가</span></a>
											<a href="#" class="btn btn-sm btn-primary shadow-sm option-kind-del-btn"><span class="fs-12">삭제</span></a>
											</div>
										</div>
									</div>
									<div class="table-responsive">
										<div id="div-gd-optkind" style="height:255px;" class="ag-theme-balham"></div>
									</div>
								</div>
							</div>
							<div class="col-lg-8">
								<div class="card-body pt-2">
									<div class="card-title">
										<div class="filter_wrap">
											<div class="fl_box px-0 mx-0">
												<h6 class="m-0 font-weight-bold"><span id="opt-type" class="text-primary"></span></h6>
											</div>
											<div class="fr_box">
												<a href="#" class="btn btn-sm btn-primary shadow-sm option-add-btn"><span class="fs-12">관리</span></a>
												<a href="#" class="btn btn-sm btn-primary shadow-sm option-del-btn"><span class="fs-12">삭제</span></a>
												<a href="#" class="btn btn-sm btn-primary shadow-sm option-sav-btn"><span class="fs-12">저장</span></a>
												<a href="javascript:void(0);" onclick="openOptsStock();" class="btn btn-sm btn-primary shadow-sm option-inv-btn"><span class="fs-12">입고</span></a>
											</div>
										</div>
									</div>
									<div class="table-responsive basic-option">
										<div id="div-gd-opt" style="height:255px;" class="ag-theme-balham"></div>
									</div>
								</div>
							</div>
						</div>

                        <div class="row">
							<div class="col-4">
                                {{-- 옵션관리 좌측 옵션추가영역 s --}}
                                <div id="option_add" class="table-box-ty2 mobile" style="display: none;">
                                    <table class="table incont table-bordered mt-2 mb-2" width="100%" cellspacing="0"style="border: 1px solid #eff2f7;">
                                        <colgroup>
                                            <col width="30%">
                                            <col width="70%">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th class="required" style="padding: 5px;">유형</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <select name="opt_type" id="opt_type" class="form-control form-control-sm" style="height: 25px;">
                                                            <option value="basic">기본</option>
                                                            <option value="extra">추가</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required" style="padding: 5px;">옵션구분</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="flax_box">
                                                        <input type="text" class="form-control form-control-sm" name="opt_type_nm" id="opt_type_nm" style="height: 25px;">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required" style="padding: 5px;">필수여부</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box flax_box txt_box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="opt_required_yn" value="Y" id="opt_required_yn1" class="custom-control-input" checked />
                                                            <label class="custom-control-label" for="opt_required_yn1">Y</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="opt_required_yn" value="N" id="opt_required_yn2" class="custom-control-input" />
                                                            <label class="custom-control-label" for="opt_required_yn2">N</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required" style="padding: 5px;">사용여부</th>
                                                <td style="padding:0px 10px 0px 10px;">
                                                    <div class="form-inline form-radio-box flax_box txt_box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="opt_use_yn" value="Y" id="opt_use_yn1" class="custom-control-input" checked />
                                                            <label class="custom-control-label" for="opt_use_yn1">Y</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="opt_use_yn" value="N" id="opt_use_yn2" class="custom-control-input" />
                                                            <label class="custom-control-label" for="opt_use_yn2">N</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="flax_box" style="justify-content: center;">
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm mr-1" onclick="addOptionKind()"><span class="fs-12">확인</span></button>
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm option-kind-add-cancel-btn"><span class="fs-12">취소</span></button>
                                    </div>
                                </div>
                                {{-- // 옵션관리 좌측 옵션추가영역 e --}}
							</div>
						</div>
                    </div>
                </div>
                @if(count($coupon_list) > 0)
				<div class="card">
					<div class="card-header mb-0">
						<a href="#">할인 쿠폰</a>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-12">
								<div class="table-box">
									<table class="table table- th_border_none" id="dataTable" width="100%" cellspacing="0">
										<thead>
											<tr>
												<th>쿠폰명</th>
												<th>기간</th>
												<th>대상</th>
												<th>판매가</th>
												<th>쿠폰가</th>
												<th>삭제</th>
											</tr>
										</thead>
										<tbody>
										@forelse($coupon_list as $row)
											<tr>
												<td style="padding:5px;">
													<div class="txt_box" style="font-size:12px;"><a href="#" onclick="return openCouponDetail('edit','{{ $row->coupon_no }}');">{{ $row->coupon_nm }}</a></div>
												</td>
												<td style="padding:5px;">
													<div class="txt_box" style="font-size:12px;">{{ $row->use_fr_date }} ~ {{ $row->use_to_date  }}</div>
												</td>
												<td style="padding:5px;">
													<div class="txt_box" style="font-size:12px;">{{ $row->coupon_apply }}</div>
												</td>
												<td style="padding:5px;">
													<div class="txt_box" style="font-size:12px;">{{ number_format($row->price) }}</div>
												</td>
												<td style="padding:5px;">
													<div class="txt_box" style="font-size:12px;">{{ number_format($row->coupon_applied_price) }}</div>
												</td>
                                                <td style="padding:5px;text-align: center;">
                                                    <input type="button" class="btn btn-sm btn-outline-secondary discount-del-btn" onclick="deleteCoupon('{{ $row->coupon_no }}')" value="삭제" />
                                                </td>
											</tr>
										@empty
											<tr><td colspan=99 style="font-size:12px;text-align: center">할인 쿠폰 내역이 없습니다.</td></tr>
										@endforelse
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
                @endif
                @if($type != 'create')
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#" class="d-inline-block">유사 상품</a>
                    </div>
                    <div class="card-body pt-2">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box px-0 mx-0">
                                    <h6 class="m-0 font-weight-bold">총 : <span id="goods-similar-grid-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box d-flex">
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm ml-2 similar-add-btn"><span class="fs-12">추가</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm ml-2 similar-del-btn"><span class="fs-12">삭제</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="goods-similar-grid" style="height:250px;width:100%;" class="ag-theme-balham"></div>
                        </div>
                        <p class="red mt-2">* 브랜드, 업체, 품목, 대표카테고리가 같은 상품만 유사 상품으로 등록 가능합니다.</p>
                    </div>
                </div>
                @endif
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품 전시 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <ul class="row category_list_ty2">
                                    <li class="col-lg-12">
                                        <dl>
                                            <dt class="d-flex align-items-center justify-content-between">
                                                <div>상품설명</div>
                                                @if ($type !== 'create')
                                                <button type="button" id="sabang_cont" class="btn btn-sm btn-outline-primary shadow-sm">판매처별</button>
                                                @endif
                                            </dt>
                                            <dd>
                                                <div class="area_box edit_box">
                                                    <textarea name="goods_cont" id="goods_cont" class="form-control editor1">{{ @$goods_info->goods_cont }}</textarea>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
                                    <li class="col-lg-12 mt-2">
                                        <dl>
                                            <dt class="d-flex align-items-center justify-content-between">
                                                <div>제품사양</div>
                                            </dt>
                                            <dd>
                                                <div class="area_box">
                                                    <textarea name="spec_desc" class="form-control">{{ @$goods_info->spec_desc }}</textarea>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
                                    <li class="col-lg-12 mt-2">
                                        <dl>
                                            <dt class="d-flex align-items-center justify-content-between">
                                                <div>예약 / 배송</div>
                                            </dt>
                                            <dd>
                                                <div class="area_box">
                                                    <textarea name="baesong_desc" class="form-control">{{ @$goods_info->baesong_desc }}</textarea>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
                                    <li class="col-lg-12 mt-2">
                                        <dl>
                                            <dt class="d-flex align-items-center justify-content-between">
                                                <div>MD 상품평</div>
                                            </dt>
                                            <dd>
                                                <div class="area_box">
                                                    <textarea name="opinion" class="form-control">{{ @$goods_info->opinion }}</textarea>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
									@if ($type === '')
                                    <li class="col-lg-12 mt-2">
                                        <dl>
                                            <dt class="d-flex align-items-center justify-content-between">
                                                <div>기획전</div>
                                            </dt>
                                            <dd>
                                                <div class="table-responsive">
                                                    <table class="table table- th_border_none" id="dataTable" width="100%" cellspacing="0">
                                                        <thead>
                                                        <tr>
                                                            <th>제목</th>
                                                            <th>상태</th>
                                                            <th>기간</th>
                                                            <th>삭제</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @forelse($planing as $row)
                                                            <tr>
                                                                <td style="padding:5px;"><div class="txt_box" style="font-size:12px;">{{ $row->title }}</div></td>
                                                                <td style="padding:5px;"><div class="txt_box" style="font-size:12px;">{{ $row->plan_date_yn  }}</div></td>
                                                                <td style="padding:5px;"><div class="txt_box" style="font-size:12px;">{{ $row->start_date }} ~ {{ $row->end_date }}</div></td>
                                                                <td style="padding:5px;text-align: center;">
                                                                    <input type="button" class="btn btn-sm btn-outline-secondary discount-del-btn" onclick="deletePlanning('{{ $row->d_cat_cd }}')" value="삭제" />
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr><td colspan=99 style="text-align: center">기타 정산 내역이 없습니다.</td></tr>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
									@endif
                                    <li class="col-lg-12 mt-2">
                                        <dl>
                                            <dt class="d-flex align-items-center justify-content-between">
                                                <div>관련 상품</div>
                                            </dt>
                                            <dd>
                                                @if ( $type == "create")
                                                <div class="txt_box">
                                                    상품 등록을 완료 하신 후 관련상품을 설정할 수 있습니다.
                                                </div>
                                                @else
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-radio pr-0">
                                                        <input type="radio" name="related_cfg" id="related_cfg_1" value="A" class="custom-control-input" onclick="relatedGoods(this);"
                                                        {{ (@$goods_info->related_cfg=="A") ? "checked" : "" }} />
                                                        <label class="custom-control-label" for="related_cfg_1">자동 설정</label>
                                                    </div>
                                                    <span style="margin-left: -2px" class="mr-3">
                                                        <x-tool-tip>
                                                            <x-slot name="arrow">top</x-slot>
                                                            <x-slot name="align">left</x-slot>
                                                            <x-slot name="html">
                                                                동일 카테고리 내의 일부 상품이 관련 상품으로 출력됩니다.
                                                            </x-slot>
                                                        </x-tool-tip>
                                                    </span>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="related_cfg" id="related_cfg_2" value="G" class="custom-control-input" onclick="relatedGoods(this);"
                                                        {{ (@$goods_info->related_cfg=="G") ? "checked" : "" }} />
                                                        <label class="custom-control-label" for="related_cfg_2">상품별 설정</label>
                                                    </div>
                                                </div>
                                                <style>
                                                    .img {
                                                        height: 30px;
                                                    }
                                                </style>
                                                <div class="related_goods_area">
                                                    <div class="filter_wrap mb-2">
                                                        <div class="fl_box"></div>
                                                        <div class="fr_box">
                                                            <div class="custom-control custom-checkbox form-check-box mr-1" style="display: inline-block;">
                                                                <input type="checkbox" name="cross_yn" class="custom-control-input" value="Y" id="cross_yn" {{ (@$goods_info->cross_yn=="Y") ? "checked" : "" }}>
                                                                <label class="custom-control-label" for="cross_yn">크로스 등록</label>
                                                            </div>
                                                            <span class="mr-2">
                                                                <x-tool-tip>
                                                                    <x-slot name="arrow">top</x-slot>
                                                                    <x-slot name="align">left</x-slot>
                                                                    <x-slot name="html">
                                                                        <p>
                                                                            크로스 등록이란?<br/><br/>
                                                                            <b> A 상품을 기준으로 B, C 상품을 선택했다면, A 상품의 관련상품이 B,C로 등록되며, <br/>
                                                                            B 상품의 관련상품으로 A,C가 등록되며, C 상품의 관련상품으로 A,B 상품이 등록됩니다.</b><br/><br/>
                                                                            <b>※ 즉, 한번에 모든 상품을 서로의 관련상품으로 등록할 수 있습니다.</b>
                                                                        </p>
                                                                    </x-slot>
                                                                </x-tool-tip>
                                                            </span>
                                                            <a href="javascript:void(0);" onclick="openAddRelatedGoods()" class="btn btn-sm btn-primary shadow-sm">추가</a>
                                                        </div>
                                                    </div>
                                                    <div id="div-gd-related-goods" style="min-height: 48px; height:500px;" class="ag-theme-balham"></div>
                                                        <script>
                                                            const related_goods_style_obj = {"height": "48px", "line-height": "48px", "text-align": "center"};
                                                            const related_goods_columns = [
                                                                {field: "r_goods_no", headerName: "관련상품번호", hide: true, height: 48},
                                                                {field: "img" , headerName:"이미지", type: 'GoodsImageType', cellStyle: related_goods_style_obj},
                                                                {field: "img", headerName: "이미지_url", cellStyle: related_goods_style_obj, hide: true},
                                                                {field: "opt_kind_nm", headerName: "품목", width: 130, cellStyle: related_goods_style_obj},
                                                                {field: "brand_nm", headerName: "브랜드", width: 80, cellStyle: related_goods_style_obj},
                                                                {field: "goods_nm", headerName: "상품명", width: 'auto', cellStyle: related_goods_style_obj},
                                                                {field: "sale_stat_cl", headerName: "상품상태", type: 'GoodsStateTypeLH50', width: 80, cellStyle: related_goods_style_obj},
                                                                {field: "price", headerName: "판매가", type: 'currencyType', width: 80, cellStyle: related_goods_style_obj},
                                                                {field: "", headerName: "삭제", cellRenderer: (params) => {
                                                                    const row = params?.data;
                                                                    return "<a href='javascript:void(0);' onclick='delRelatedGood(" + JSON.stringify(row) + ")'>삭제</a>";
                                                                }, cellStyle: {...related_goods_style_obj, 'text-decoration': "underline"} }
                                                            ];
                                                            const related_goods_options = {
                                                                getRowNodeId: (data) => data?.r_goods_no // 업데이터 및 제거를 위한 식별 ID 할당
                                                            }
                                                            let gx_related_goods = new HDGrid(document.querySelector("#div-gd-related-goods"), related_goods_columns, related_goods_options);
                                                        </script>
                                                    </div>
                                                </div>
                                            @endif
                                            </dd>
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @if(count($modify_history) > 0 && $type === '')
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품 변경 내역</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive" style="max-height:250px;border-bottom:1px solid #F1F1F1;">
                                    <table class="table table-bordered th_border_none">
                                        <thead>
                                            <tr>
                                                <th>변경일</th>
                                                <th>이름(ID)</th>
                                                <th>상단홍보글</th>
                                                <th>변경사유</th>
                                                <th>판매가(원)</th>
                                                <th>원가(원)</th>
                                                <th>마진(%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($modify_history as $row)
                                            <tr>
                                                <td>{{ $row->upd_date }}</td>
                                                <td>{{ $row->name }} ({{ $row->id }})</td>
                                                <td>{{ $row->head_desc }}</td>
                                                <td>{{ $row->memo }}</td>
                                                <td>{{ number_format($row->price) }}</td>
                                                <td>{{ number_format($row->wonga) }}</td>
                                                <td>{{ $row->margin }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <form method="post" name="save">

                @if(count($class_items) > 0)
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품정보고시 내역</a>
                    </div>
                    <div class="card-body pt-2">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box px-0 mx-0">
                                    <h6 class="m-0 font-weight-bold">총 : <span id="goods-class-grid-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                @if (@$type === '')
                                    <span class="d-none d-sm-inline">선택한 상품을</span>
                                    <select id="to_class" name="to_class" class="form-control form-control-sm goods_class" style="width:130px;display:inline">
                                        <option value="">선택</option>
                                        @foreach ($class_items as $class_item)
                                            <option value='{{ $class_item->class }}'>
                                                {{ $class_item->class_nm }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="d-none d-sm-inline">로</span>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm goods-info-change-btn"><span class="fs-12">분류변경</span></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm goods-info-save-btn px-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="저장"><i class="bx bx-save fs-14"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm goods-info-delete-btn px-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="삭제"><i class="far fa-trash-alt fs-12"></i></a>
                                @elseif (@$type === 'create')
                                    <span class="d-none d-sm-inline">분류 : </span>
                                    <select id="create_to_class" name="create_to_class" class="form-control form-control-sm" style="width:130px;display:inline">
                                        <option value="">선택</option>
                                        @foreach ($class_items as $class_item)
                                            <option value='{{ $class_item->class }}'>
                                                {{ $class_item->class_nm }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="goods-class-grid" style="height:95px;width:100%;" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
                @endif
                </form>
            </div>
        </form>
    </div>

    <script type="text/javascript" charset="utf-8">
        const goods_no = '{{$goods_no}}';
        const goods_sub = '{{@$goods_info->goods_sub}}';
        const type = '{{@$type}}';

		var ed;
        let gxc; // 상품정보고시 테이블

        $(document).ready(function() {
            $('#main-tab').trigger("click");
            
            var editorToolbar = [
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['paragraph']],
                ['insert', ['picture', 'video']],
                ['emoji', ['emoji']],
                ['view', ['undo', 'redo', 'codeview','help']]
            ];
            var editorOptions = {
                lang: 'ko-KR', // default: 'en-US',
                minHeight: 150,
                height: 200,
                dialogsInBody: true,
                disableDragAndDrop: false,
                toolbar: editorToolbar,
                imageupload:{
                    dir:'/data/head/goods_cont',
                    maxWidth:1280,
                    maxSize:10
                }
            }
            ed = new HDEditor('.editor1',editorOptions, true);
        });

		@if( $type != 'create')
			get_category_by_goods_no("display", goods_no, {{@$goods_info->goods_sub}});
			get_category_by_goods_no("item", goods_no, {{@$goods_info->goods_sub}});
		@endif

        $("#search_brand_nm").keyup(function(e){
            if(e.keyCode == 13){
                search_brand();
            }
        });

        function get_category_by_goods_no(cat_type, goods_no, goods_sub)
		{
            $.ajax({
                async: true,
                type: 'get',
                url: '/head/api/category/get_category_by_goods_no/'+cat_type+"/"+goods_no+"/"+goods_sub,
                success: function (data)
				{
                    var res	= jQuery.parseJSON(data);

                    if( Object.keys(res.body).length == 0 )
					{
                        var opt = "<option value='0'>카테고리를 추가해 주십시오.</option>";
                        $("#category_select_"+cat_type).append(opt);
                    }

                    $.each(res.body,function(index,row){
						var	value	= row.d_cat_cd + "|" + row.seq + "|" + row.disp_yn;
                        var opt = "<option value='" + value + "' class='cat_"+cat_type+"_opt'>"+row.display_str+"</option>";
                        $("#category_select_"+cat_type).append(opt);
                    });

                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        }

        function PopPrdDetail(goods_no, goods_sub){
            window.open("/head/product/prd01/"+goods_no,"Product Detail");
        }

		function addRepCategory(cat_type)
		{
			searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
			{
                if(searchCategory.type === "ITEM") return alert("대표 카테고리는 전시 카테고리만 설정가능합니다.");

				if(code.length < mx_len)
				{
					alert("최하단에서만 등록 가능합니다");
					return false;
				}

				$("[name=rep_cat_cd]").val(code);
				$('#txt_rep_cat_nm').html(full_name);

                var is_rep = true;
				addCategory(searchCategory.type, code, name, full_name, mx_len, is_rep);
			});
		}

		function addDCategory(cat_type)
		{
			searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
			{
                addCategory(searchCategory.type, code, name, full_name, mx_len);
			});
		}

		function addUCategory(cat_type)
		{
			searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
			{
				addCategory(searchCategory.type, code, name, full_name, mx_len);
			});
		}

        function addCategory(cat_type, code, name, full_name, mx_len, is_rep = false)
		{
            cat_type = cat_type.toLowerCase();

			if(code.length < mx_len)
			{
				alert("최하단에서만 등록 가능합니다");
				return false;
			}

			const options	= $('#category_select_'+cat_type+' option');
			var seq			= options.length;
			let isChecking	= false;

			$.each(options, function(idx, option){
				if (isChecking) return;

				const txt = option.value.split("|");

				if (txt[0] === code) {
					isChecking = true;
					if(!is_rep) alert("중복된 카테고리가 있습니다.");
					return false;
				}
			});

			if (!isChecking)
			{
				// 전시 순서
				if( seq > 1 )
				{
					arr	= options[seq - 1].value.split('|');
					seq	= parseInt(arr[1]) + 1;
				}
				else
				{
					seq	= 1;
				}

				$('#category_select_'+cat_type+' option[value=0]').remove();

				// 전시 뎁스
				arr_txt	= full_name.split('>');
				add_len	= arr_txt.length;
				cat_txt	= "";

				for( i = 0; i < add_len; i++ )
				{
					options.length++;
					if( i != 0 )	cat_txt = cat_txt + ">";

					cat_txt	= cat_txt + arr_txt[i];
					icode	= code.substring(0,(i*3)+3);

					$('#category_select_'+cat_type).append(`
						<option value="${icode}|${seq}|Y">
							${cat_txt} (${seq}) [Y] ${icode}
						</option>
					`);

				}
			}

        }

        function displayCategory(cat_type)
		{
			if( $("#category_select_"+cat_type+" option:selected").val() == undefined )
			{
				alert("활성화 할 카테고리를 선택해 주십시오.");
				return false;
			}

			ar	= $("#category_select_"+cat_type+" option:selected").val().split('|');
			$("#category_select_"+cat_type+" option:selected").val(ar[0]+"|"+ar[1]+"|Y");

            var str = $("#category_select_"+cat_type+" option:selected").text();
            str = str.replace("[N]", "[Y]");
            $("#category_select_"+cat_type+" option:selected").text(str);
        }

        function hiddenCategory(cat_type)
		{
			if( $("#category_select_"+cat_type+" option:selected").val() == undefined )
			{
				alert("비활성화 할 카테고리를 선택해 주십시오.");
				return false;
			}

			ar	= $("#category_select_"+cat_type+" option:selected").val().split('|');
			$("#category_select_"+cat_type+" option:selected").val(ar[0]+"|"+ar[1]+"|N");

            var str = $("#category_select_"+cat_type+" option:selected").text();
            str = str.replace("[Y]", "[N]");
            $("#category_select_"+cat_type+" option:selected").text(str);
        }

        function deleteCategory(cat_type)
		{
			if( $("#category_select_"+cat_type+" option:selected").val() == undefined )
			{
				alert("삭제할 카테고리를 선택해 주십시오.");
				return false;
			}

			ar		= $("#category_select_"+cat_type+" option:selected").val().split('|');
			idx1	= ar[0];

			const options	= $('#category_select_'+cat_type+' option');

			$.each(options, function(idx, option){

				ar2		= option.value.split("|");
				idx2	= ar2[0].substring(0, idx1.length);

				if( idx1 == idx2 )
				{
					option.remove();
				}

			});

            if($('#category_select_'+cat_type+' option').length === 0) {
                $('#category_select_'+cat_type).append('<option value="0">카테고리를 추가해 주십시오.</option>');
            }

			//$("#category_select_"+cat_type+" option:selected").remove();
        }

		function SelectCompany()
		{
			searchCompany.Open(function(com_cd, com_nm, com_type, baesong_kind, baesong_info, margin_type, dlv_amt){
				/*
				console.log(com_cd);
				console.log(com_nm);
				console.log(com_type);
				console.log(baesong_kind);
				console.log(baesong_info);
				console.log(margin_type);
				console.log(dlv_amt);
				*/

				if( com_type == "3" || com_type == "4" || com_type == "5" || com_type == "9" || com_type == "999" )
				{
					alert('공급업체 또는 입점업체의 상품만 등록하실 수 있습니다.');
					return false;
				}
				else if( com_type == "2" )
				{	// 입점업체
					$('#com_id').val(com_cd);
					$('#com_nm').val(com_nm);
					$('#com_type').val(com_type);
					$('#margin_type').val(margin_type);
					$('#goods_type').val("P");
					$('#bae_yn').val("N");

					if( dlv_amt > 0 )	$('#bae_yn').val("Y");

					$('#baesong_price').val(Comma(dlv_amt));
					$('#is_unlimited1').attr('checked', true);
					$('#is_unlimited2').attr('disabled', false);

					$('#baesong_kind').val(baesong_kind);
					$('#baesong_info').val(baesong_info);

                    $('#com_id').trigger("change");
                    $('#com_nm').trigger("change");
                    $('#goods_type').trigger("change");
				}
				else if( com_type == "1" )
				{	// 공급업체
					$('#com_id').val(com_cd);
					$('#com_nm').val(com_nm);
					$('#com_type').val(com_type);
					$('#margin_type').val(margin_type);
					$('#goods_type').val("S");
					$('#bae_yn').val("N");

					if( dlv_amt > 0 )	$('#bae_yn').val("Y");

					$('#baesong_price').val(Comma(dlv_amt));
					$('#is_unlimited1').attr('checked', true);
					$('#is_unlimited2').attr('disabled', false);

					$('#baesong_kind').val(baesong_kind);
					$('#baesong_info').val(baesong_info);

                    $('#com_id').trigger("change");
                    $('#com_nm').trigger("change");
                    $('#goods_type').trigger("change");
				}

			});
		}

		$(".btn-rep-add").click(function(){ addRepCategory('display'); });

        $(".btn-item-add").click(function(){ addUCategory('item'); });
        $(".btn-display-add").click(function(){ addDCategory('display'); });

        $(".btn-item-display").click(function(){ displayCategory('item'); });
        $(".btn-display-display").click(function(){ displayCategory('display'); });

        $(".btn-item-hidden").click(function(){ hiddenCategory('item'); });
        $(".btn-display-hidden").click(function(){ hiddenCategory('display'); });

        $(".btn-item-delete").click(function(){ deleteCategory('item') });
        $(".btn-display-delete").click(function(){ deleteCategory('display') });

		$(".btn-select-company").click(function(){ SelectCompany() });

		function change_dlv_cfg_form(value){
			$(".dlv_config_detail_div").css("display","none");
			$("#dlv_config_detail_"+value+"_div").css("display","inline");
		}

		function change_point_cfg_form(value){
			$(".point_config_detail_div").css("display","none");
			$("#point_config_detail_"+value+"_div").css("display","inline");

			if( value == "g" )
				$('#point').prop("readonly",false);
			else
				$('#point').prop("readonly",true);
		}

        if($("#new_product_type2").is(":checked")){
            $("#new_product_day").css('display','block');
        }
        function display_new_prd_day(value){
            if(value == "y"){
                $("#new_product_day").css('display','block');
            }else{
                $("#new_product_day").css('display','none');
            }
        }

        function pop_prd_page(){
            url = "{{config('shop.front_url')}}/app/product/detail/"+goods_no+"/"+goods_sub;
            window.open(url);
        }

        function validate(){
	        let f = document.f1;
            let is_sale = $("#sale_yn").is(":checked");

			if( $('#rep_cat_cd').val() == "" ){
				alert("대표카테고리를 선택해 주십시오.");
				return false;
			}

            if( $("#goods_nm").val() == "" ){
                alert("상품명을 입력해 주십시오.");
                $("#goods_nm").focus();
                return false;
            }
            if( $("#goods_nm").val().match(/[',|]/) ){
                alert("상품명에 특수문자(\',|)를 입력할 수 없습니다.");
                $("#goods_nm").focus();
                return false;
            }

            /*
            if( $("#goods_nm_eng").val() == "" ){
                alert("상품명(영문)을 입력해 주십시오.");
                $("#goods_nm_eng").focus();
                return false;
            }
            */
            if( $('#com_id').val() == "" ){
				alert('업체를 선택해 주십시오.');
				$('.btn-select-company').click();
				return false;
			}
            if( $('#goods_type').val() == "" ){
				alert('상품구분을 선택해 주십시오.');
				$('#goods_type').focus();
				return false;
			}
            if( $("#brand_cd").val() == "" ){
                alert("브랜드를 선택해 주십시오.");
                $(".sch-brand").click();
                return false;
            }
            if( $("#opt_kind_cd").val() == "" ){
                alert("품목을 선택해 주십시오.");
                $("#opt_kind_cd").focus();
                return false;
            }
            if( $("#style_no").val() == "" ){
                alert("스타일넘버를 입력해 주십시오.");
                $("#style_no").focus();
                return false;
            }
            if( $("#org_nm").val() == "" ) {
                alert("원산지를 입력해 주십시오.");
                $("#org_nm").focus();
                return false;
            }
            
            if( $('#tax_yn').val() == "" ){
				alert("과세구분을 선택해 주십시오.");
				$('#tax_yn').focus();
				return false;
			}
			if( $('#sale_stat_cl').val() == "" ){
				alert("상품상태를 선택해 주십시오.");
				$('#sale_stat_cl').focus();
				return false;
			}
            if( $("#md_id").val() == "" ){
				alert("MD를 선택해 주십시오.");
				$("#md_id").focus();
				return false;
			}
            if( $("#baesong_info").val() == "" ){
                alert("배송지역를 선택해 주십시오.");
                $("#baesong_info").focus();
                return false;
            }
            if( $("#baesong_kind").val() == "" ){
                alert("배송업체를 선택해 주십시오.");
                $("#baesong_kind").focus();
                return false;
            }
			if( $('#dlv_fee_cfg').val() == "G" ){
				if( $('#bae_yn').val() == "Y" && $('#baesong_price').val() == "" ){
					alert("배송비를 입력해 주십시오.");
					$('#baesong_price').focus();
					return false;
				}
			}
			if( $('#point_cfg').val() == "G" )
			{
				if( $('#point_yn').val() == "Y" && $('#point').val() == "" ){
					alert("지급 적립금을 입력해 주십시오.");
					$('#point').focus();
					return false;
				}

				if( $('#point_unit').val() == "P" && $('#point').val() >= 100 ){
					alert("적립금을 100% 이상 지급할 수 없습니다.");
					$('#point').val(0);
					$('#point').focus();
					return false;
				}
			}
            if( $("#price").val() == "0" ) {
				if( !confirm("입력하신 정상가는 0원 입니다. 저장 하시겠습니까?") ){
					$("#price").focus();
					return false;
				}
            }
            if( is_sale && $("#sale_type").val() == "" ) {
                alert("세일구분을 선택해 주십시오.");
                $("#sale_type").focus();
                return false;
            }
            if( is_sale && ($("[name='sale_rate']").val() == "" || $("[name='sale_rate']").val() == 0) ) {
                alert("세일율을 입력해 주십시오.");
                $("[name='sale_rate']").focus();
                return false;
            }
            

            return true;
        }

        // store
		$('.save-btn').click(function(){
			if (!validate()) return;

			$("#restock:checked").attr("name", "restock_yn");

			let frm	= $("#f1");
			let d_cat_str	= "";
			let u_cat_str	= "";
			let md_nm	= $('#md_id > option:selected').html();

			// console.log(md_nm);
				md_nm = md_nm.replace(/(\s)|(\t)|(\n)/g, "");

			$('#md_nm').val(md_nm);
            $("#goods_cont").val(ed.html());

			//전시 카테고리 전송값
			$("#category_select_display option").each(function(){
				if( $(this).val() != 0 ){
					//d_cat_str	+= ","+$(this).text();
					d_cat_str	+= ","+$(this).val();
				} else {
                    d_cat_str = "null";
                }
			});

			$("#d_category_s").val(d_cat_str);

			//용도 카테고리 전송값
			$("#category_select_item option").each(function(){
				if($(this).val() != 0) {
					//u_cat_str += ","+$(this).text();
					u_cat_str += ","+$(this).val();
				} else {
                    u_cat_str = "null";
                }
			});

			$("#u_category_s").val(u_cat_str);

            const save_method = type === '' ? 'put' : 'post';
            
            let save_data = frm.serialize();
            if ($("#create_to_class").val() !== '') {
                save_data += "&goods_class=" + JSON.stringify(gxc.getRows()?.[0]);
            }

			$.ajax({
				async: true,
				type: save_method,
				url: '/head/product/prd01',
				data: save_data,
				success: function (data) {
					if (!isNaN(data * 1)) {
                        if (type == "create") {
                            alert("상품이 등록되었습니다.");
                            opener.Search();
                            window.close();
                        } else {
						    alert("변경된 내용이 정상적으로 저장 되었습니다.");
						    location.href="/head/product/prd01/" + data;
                        }
					}
				},
				error: function(e) {
					console.log(e.responseText)
				}
			});
		});

        $('[name=new_product_day]').change(function(){
            this.value = this.value.replace(/[-]/g, '');
        });

        $(".sch-goods-category").click(function(e){
          e.preventDefault();
          searchCategory.Open('DISPLAY', function(code, name, full_name){
            $("#goods_cat_cd").val(code);
            $(".goods_cat_nm").html(full_name);
          });
        });
        $("#img-setting").click(function(){
			//console.log('image');
			@if( $type == 'create' )
				alert('상품을 먼저 등록 하신 후 이미지를 등록할 수 있습니다.');
			@else
				window.open("/head/product/prd02/"+goods_no+"/image","_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=960");
			@endif
        });

        //수정 페이지에서 복사버튼 클릭했을 경우.
        $(".copy-btn").click(function(e){
          e.preventDefault();

          location.href="/head/product/prd01/"+goods_no+"?type=copy";
        });

        //배송비 지불 방식을 선택했을 경우.
        $('[name=dlv_pay_type]').change(function(e){
          if (this.value !== 'F') return;

          alert('현재 선불방식만 사용하실 수 있습니다.');

          $("#dlv_pay_type1").click();
        });

        //배송비 개별설정에서 유료인지 무료인지 선택했을 경우.
        $('[name=bae_yn]').change(function(){
          $("#baesong_price").attr('readonly', this.value === 'N');
        });

        $('#price, #goods_price, #margin, #goods_sh, #wonga').keyup(function(){

			if( $('#com_id').val() == '' ){
				alert("업체를 선택해 주십시오.");
				$('#price').val('0');

				return false;
			}

			if( $('#goods_type').val() == '' ){
				alert("상품구분을 선택해주십시오.");
				$('#price').val('0');

				return false;
			}

			if( $('#margin').val() > 100 ){
				alert("마진율은 100%를 넘을 수 없습니다.");
				$('#margin').val('0');

				return false;
			}

			if( $('#goods_type').val() == "P" ){
                var price	= unComma($('#price').val());
				var margin	= unComma($('#margin').val());
				var wonga	= unComma($('#wonga').val());
                var goods_sh = unComma($('#goods_sh').val());

				@if( $type == 'create' )
					if( price > 0 ){
						if( margin == '' )	margin = 0;
						var wonga = parseInt(Math.round(price * (1-margin/100)),10);
						$("#wonga").val(Comma(wonga));
					}
				@else
					if( wonga > 0 ){
						if( this.id == "margin" ){
							price	= parseInt(Math.round(wonga / (1 - (margin / 100))),10);
							$("#price").val(Comma(price));
						}else if( this.id == "price" ){
							margin	= parseFloat(((price - wonga) / price) * 100).toFixed(2);
							$("#margin").val(margin);
						}
					}
				@endif

				$('#price').val(numberFormat(price));
				$('#goods_sh').val(numberFormat(goods_sh));
				$('#wonga').val(numberFormat(wonga));
			}
			else if( $('#goods_type').val() == "S" ){
				//공급업체
				var price	= unComma($('#price').val());
				var margin	= unComma($('#margin').val());
				var wonga	= unComma($('#wonga').val());
                var goods_sh = unComma($('#goods_sh').val());

				@if( $type == 'create' )
					if( price > 0 ){
						if( margin == '' )	margin = 0;
						var wonga = parseInt(Math.round(price * (1-margin/100)),10);
						$("#wonga").val(Comma(wonga));
					}
				@else
					if( wonga > 0 ){
						if( this.id == "margin" ){
							price	= parseInt(Math.round(wonga / (1 - (margin / 100))),10);
							$("#price").val(Comma(price));
						}else if( this.id == "price" ){
							margin	= parseFloat(((price - wonga) / price) * 100).toFixed(2);
							$("#margin").val(margin);
						}
					}
				@endif

				$('#price').val(numberFormat(price));
				$('#goods_sh').val(numberFormat(goods_sh));
				$('#wonga').val(numberFormat(wonga));

			}

        });

        // 초기 옵션사용여부
        let prevOptionUsed = $("[name=is_option_use]:checked").val();

		//재고 사용 유무
		$('[name=is_option_use]').change(function(e){
			@if( $type != 'create' )
                if(confirm("옵션 사용여부 변경 시 등록되어 있는 옵션 정보와 재고 수량이 모두 삭제됩니다.\n변경하시겠습니까?")){
                    setOptionArea();
                    prevOptionUsed = $("[name=is_option_use]:checked").val();
                    delOptionAll();
                } else {
                    $(`[name=is_option_use][value=${prevOptionUsed}]`).prop("checked", true);
                }
            @else
                setOptionArea();
			@endif
		});

        function setOptionArea() {
            if( $('#is_option_use_n').is(":checked") == true ){
                console.log('사용안함');
                $('.use_option_n').css('display','table-row');
                $('.use_option_y').css('display','none');
                resetAddOptionKindBox();
            }else{
                console.log('사용함')
                $('.use_option_n').css('display','none');
                $('.use_option_y').css('display','flex');
            }
        }


        $(".btn-change-qty").click(function(e) {
            e.preventDefault();
            const qty = $('[name=goods_qty]').val();

            if (isNaN(qty * 1)) {
                alert("온라인 재고를 입력해주세요");
                return;
            }

          $.ajax({
              async: true,
              type: 'put',
              url: '/head/product/prd01/update/qty',
              data: { qty, goods_no, goods_sub },
              success: function (data) {
                  alert("수량이 변경되었습니다.");
                //   location.reload();
              },
              error: function(request, status, error) {
                  console.log("error")
              }
          });
        });

        $('.btn-qty-in').click(function(){
            var url = "/head/product/prd01/" + goods_no + "/in-qty";
            window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
        });
        
        /** 상품정보고시 관련 */
        
        if ($("#goods-class-grid").length > 0) {
            const CENTER = {'text-align': 'center'};
            let pApp;
            const goods_class = "{{ @$goods_info->class ?? '' }}";

            const goods_class_columns = [
                {
                    field: "chk",
                    headerName: '',
                    cellClass: 'hd-grid-code',
                    headerCheckboxSelection: true,
                    checkboxSelection: true,
                    width: 28,
                    pinned: 'left',
                    sort: null
                },
                {field: "goods_type", headerName: "상품구분", width: 70, cellStyle: StyleGoodsTypeNM, pinned: 'left'},
                {field: "com_nm", headerName: "업체", width: 80, cellStyle: CENTER, pinned: 'left'},
                {field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: CENTER, pinned: 'left'},
                {field: "brand_nm", headerName: "브랜드", minWidth: 80, maxWidth: 80, cellStyle: CENTER},
                {field: "style_no", headerName: "스타일넘버", minWidth: 80, maxWidth: 80, cellStyle: CENTER},
                {field: "img2", headerName: "img2", hide: true},
                {field: "img", headerName: "이미지", type: 'GoodsImageType',  minWidth: 60, maxWidth: 60, cellStyle: CENTER, surl:"{{config('shop.front_url')}}"},
                {field: "sale_stat_cl", headerName: "상품상태", minWidth: 80, maxWidth: 80, cellStyle: StyleGoodsState},
                {
                    field: "goods_no", headerName: "온라인코드", minWidth: 80, maxWidth: 80, cellStyle: CENTER,
                    cellRenderer: function (params) {
                        if (params.value !== undefined) {
                            return params.data.goods_no + ' [' + params.data.goods_sub + ']';
                        }
                    }
                },
                {field: "goods_sub", headerName: "goods_sub", hide: true},
                {field: "class", headerName: "분류", minWidth: 80, cellStyle: CENTER},
                {field: "class_cd", headerName: "class_cd", hide: true},
                {width: "auto"}
                // {field:"item_001",headerName:"제품소재",editable: checkEdit,cellStyle:editerStyle },
                // {field:"item_002",headerName:"색상",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_003",headerName:"치수",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_004",headerName:"제조사(수입자/병행수입)",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_005",headerName:"제조국",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_006",headerName:"세탁방법 및 취급시 주의사항",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_007",headerName:"제조연월",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_008",headerName:"품질보증기준",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_009",headerName:"A/S 책임자와 전화번호",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_010",headerName:"KC안전인증 대상 유무",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_011",headerName:"수입여부",editable: checkEdit,cellStyle : editerStyle},
                // {field:"item_012",headerName:"종류",editable: checkEdit,cellStyle : editerStyle},
            ];

            if (type === 'create') {
                // 상품등록 시 상품정보고시 설정

                pApp = new App('', {gridId: "#goods-class-grid"});
                const gridDiv = document.querySelector(pApp.options.gridId);
                gxc = new HDGrid(gridDiv, goods_class_columns);
                gxc.gridOptions.getRowNodeId = function (data) {
                    return data.rownum;
                }
                gxc.gridOptions.api.setRowData([{}]);
                $("#goods-class-grid-total").text('1');
                
                $("#create_to_class").on("change", async function (e) {
                    const value = e.target.options[e.target.selectedIndex].value ? e.target.options[e.target.selectedIndex].text : '';
                    setGoodsClassValue('class', value);
                    setGoodsClassValue('class_cd', e.target.options[e.target.selectedIndex].value);
                    
                    const res = await axios({ 'method': 'get', url: "/head/product/prd05/column_search?class=" + e.target.value });
                    if (res.status === 200) {
                        const cols = goods_class_columns.filter(c => c.field).concat(res.data.columns.reduce((a, c) => a.concat({
                            field: c[0],
                            headerName: c[1],
                            editable: true,
                            minWidth: 100,
                            maxWidth: 400,
                            cellStyle: { 'background': '#ffff99', 'border-right': '1px solid #e0e7e7' },
                        }), []));
                        cols.push({ 'width': 'auto' });

                        gxc.gridOptions.api.setColumnDefs([]);
                        gxc.gridOptions.api.setColumnDefs(cols);
                    } else {
                        alert('상품정보고시 분류항목 조회 중 에러가 발생했습니다. 다시 시도해주세요.');
                        console.error(res);
                    }
                });
                
                $("#goods_type").on("change", function (e) {
                    const value = e.target.options[e.target.selectedIndex].value ? e.target.options[e.target.selectedIndex].text : '';
                    setGoodsClassValue('goods_type', value);
                });
                $("#opt_kind_cd").on("change", function (e) {
                    const value = e.target.options[e.target.selectedIndex].value ? e.target.options[e.target.selectedIndex].text : '';
                    setGoodsClassValue('opt_kind_nm', value);
                });                
                $("#sale_stat_cl").on("change", function (e) {
                    const value = e.target.options[e.target.selectedIndex].value ? e.target.options[e.target.selectedIndex].text : '';
                    setGoodsClassValue('sale_stat_cl', value);
                });
                $("#com_nm").on("change", function (e) {
                    setGoodsClassValue('com_nm', e.target.value);
                });
                $("#brand_nm").on("change", function (e) {
                    setGoodsClassValue('brand_nm', e.target.value);
                });
                $("#style_no").on("change", function (e) {
                    setGoodsClassValue('style_no', e.target.value);
                });
                
                function setGoodsClassValue(colId, value) {
                    const row = gxc.getRows()?.[0];
                    if (row) {
                        row[colId] = value;
                        gxc.gridOptions.api.applyTransaction({ update: [row] });
                    }
                }
            } else {
                // 상품수정 시 상품정보고시 설정
                
                //선택한 항목 분류변경
                $('.goods-info-change-btn').click(function(e){
                    e.preventDefault();

                    const selectedRowData	= gxc.gridOptions.api.getSelectedRows();
                    const selectRowCount	= selectedRowData.length;

                    if( selectRowCount == 0 ) {
                        alert('분류변경할 정보고시내용을 선택해주세요.');
                        return;
                    }

                    const s_goods_class_cd	= $('.goods_class').val();
                    const s_goods_class_nm	= $('.goods_class > option:selected').html();
                    const good_no           = '{{$goods_no}}';

                    if( s_goods_class_cd === '' ) {
                        alert('변경할 품목을 선택해주세요.');
                        return;
                    }

                    if( confirm("선택하신 상품정보고시 품목으로 변경하시겠습니까?") ){
                        $.ajax({
                            method: 'put',
                            url: '/head/product/prd01/goods-class-opt-update',
                            data: {
                                'goods_class': s_goods_class_cd,
                                'goods_no': goods_no
                            },
                            dataType: 'json',
                            success: function(res) {
                                if (res.code == '200') {
                                    location.reload();
                                    // goodsClassSearch();
                                } else {
                                    console.log(res);
                                    alert(res.msg);
                                }
                            },
                            error: function(e) {
                                console.log(e.responseText)
                            }
                        });
                    }
                });

                //선택된 상품정보고시 저장
                $('.goods-info-save-btn').click(function(e){
                    e.preventDefault();

                    const selectedRowData	= gxc.gridOptions.api.getSelectedRows();
                    const selectRowCount	= selectedRowData.length;

                    if( selectRowCount == 0 ) {
                        alert('저장하실 정보고시 내용을 선택해주세요.');
                        return;
                    }

                    selectedRowData.forEach(function(data, idx) {

                        if(data.class_cd == null) {
                            alert('선택한 상품의 분류를 지정한 후에 저장해주세요');
                            return;
                        }

                        $.ajax({
                            async: true,
                            type: 'put',
                            url: `/head/product/prd01/goods-class-update`,
                            data: data,
                            success: function (data) {
                                if (selectRowCount -1 === idx) {
                                    alert("변경된 내용이 정상적으로 저장 되었습니다.");
                                    goodsClassSearch();
                                    // window.close();
                                    // location.reload();
									opener?.Search();
                                }
                            },
                            error: function(request, status, error) {
                                console.log("error")
                            }
                        });
                    });
                });

                //선택된 상품정보고시 삭제
                $('.goods-info-delete-btn').click(function(e){
                    e.preventDefault();

                    const selectedRowData	= gxc.gridOptions.api.getSelectedRows();
                    const selectRowCount	= selectedRowData.length;

                    if( selectRowCount == 0 ) {
                        alert('삭제하실 정보고시 내용을 선택해주세요.');
                        return;
                    }

                    if( confirm("삭제하시겠습니까?") ){

                        selectedRowData.forEach(function(data, idx) {
                            $.ajax({
                                async: true,
                                type: 'put',
                                url: `/head/product/prd01/goods-class-delete`,
                                data: data,
                                dataType: "json",
                                success: function (data) {
                                    if (selectRowCount -1 === idx) {
                                        alert("정상적으로 삭제 되었습니다.");
                                        // goodsClassSearch();
                                        location.reload();
                                    }
                                },
                                error: function(request, status, error) {
                                    console.log("error")
                                }
                            });
                        });

                    }
                });

                $.ajax({
                    async: true,
                    type: 'get',
                    url: '/head/product/prd05/column_search',
                    data: "class=" + goods_class,
                    success: function(data) {
                        let col_arr = data['columns'];
                        col_arr.forEach((col, i) => {
                            let col_val = {
                                field: col[0],
                                headerName: col[1],
                                editable: true,
                                minWidth: 100,
                                cellStyle: {'background' : '#ffff99', 'border-right' : '1px solid #e0e7e7'},
                            }
                            goods_class_columns.push(col_val);
                        });

                        pApp = new App('', { gridId: "#goods-class-grid" });
                        const gridDiv = document.querySelector(pApp.options.gridId);
                        gxc = new HDGrid(gridDiv, goods_class_columns.filter(c => c.field).concat({width: "auto"}));
                        gxc.gridOptions.getRowNodeId = function(data) {
                            return data.rownum;
                        }

                        goodsClassSearch();
                    },
                    error: function(request, status, error) {
                        alert("error");
                        console.log(request);
                    }
                });

                function goodsClassSearch() {
                    const data = `goods_no=${goods_no}&goods_sub=${goods_sub}`;
                    gxc.Request(`/head/product/prd01/${goods_no}/goods-class`, data, -1);
                }

                function checkEdit(params) {
                    return params.data.class;
                }

                // function editerStyle(params) {
                //     if (params.data.class != null)
                //         return { 'background' : '#ffff99', 'border-right' : '1px solid #e0e7e7' }
                // }
            }
        } // 상품정보고시 관련 end

        $(document).ready(function(){
            var popSlideWidth = 0;
            var popSlideWrap = $(".cum_slider_thum");
            var ouTnum = 0;
            var end = 0;
            var $dots2 = 0;
            var pgwidth = 0;
            var selectImg = $(".cum_slider_cont img");
            var selectIdx = 0;
            $(".cum_slider_thum_wrap ul li").each(function(e){
                popSlideWidth += $(this).outerWidth() + parseInt($(this).css("margin-left"));
                if($(".cum_slider_thum_wrap ul li").last().index() == e){
                    popSlideWrap.css("width", popSlideWidth+"px");
                }
            });
            end = $(".cum_slider_thum_wrap").outerWidth() - popSlideWidth;
            $dots2 = $(".cum_slider_thum_wrap ul li");
            pgwidth = ($dots2.width()+10) * $(".cum_slider_thum_wrap").width() / $dots2.width();
            popSlideWrap.css({left:0});
            $(".cum_slider_thum li a").on("click", function(){
                var elMargin = parseInt($(this).parent().css("margin-left"));
                var elWrapWidth = ($(".cum_slider_thum_wrap").outerWidth() / 2) + elMargin;
                var elWidth = $(this).parent().outerWidth() + elMargin;
                var num = ((($(this).parent().index() * elWidth)+(elWidth/2)))-elWrapWidth+5;
                ouTnum = num * -1;
                if(!$(this).hasClass("active")){
                    if(num > 0){
                        if($(".cum_slider_thum_wrap").outerWidth() < popSlideWidth){
                            if(num*-1 < end){
                                popSlideWrap.animate({left:end},200);
                            }else{
                                popSlideWrap.animate({left:num * -1},200);
                            }
                        }
                    }else{
                        popSlideWrap.animate({left:0},200);
                    }
                    $(".cum_slider_thum_wrap ul li a").removeClass("active");
                    if(selectIdx != $(this).parent().index()){
                        $(this).addClass("active");
                        selectIdx = $(this).parent().index();
                        selectImg.clearQueue();
                        selectImg.css("opacity", "0").attr({
                            "src" : $(this).find("img").attr("src"),
                            "alt" : $(this).find("img").attr("alt")
                        });
                        selectImg.animate({"opacity" : "1"},500);
                    }
                }
                return false;
            });

            $(".pop_sd_btn").on("click", function(){
                // console.log(ouTnum,pgwidth,ouTnum-pgwidth);
                if($(this).hasClass("sd_next")){
                    if(ouTnum-pgwidth < end){
                        popSlideWrap.animate({left:end},200);
                        ouTnum = end;
                    }else{
                        popSlideWrap.animate({left: ouTnum -pgwidth},200);
                        ouTnum = ouTnum - pgwidth;
                    }
                }else{
                    if(ouTnum+pgwidth >= 0){
                        popSlideWrap.animate({left: 0},200);
                        ouTnum = 0;
                    }else{
                        popSlideWrap.animate({left: ouTnum + pgwidth},200);
                        ouTnum = ouTnum + pgwidth;
                    }
                }
            });

            // 판매처별 상품관리 팝업 띄우기
            $("#sabang_cont").on("click", function() {
                const url = `/head/product/prd01/${goods_no}/goods-cont`;
                window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=700");
            })

            // 세일관리 관련
            setSaleDate('{{ @$goods_info->sale_s_dt }}', '{{ @$goods_info->sale_e_dt }}');
            setSaleOnclickEvent();
        });

    </script>

@if($type == 'create')
    <style>
        .use_option_y {
            display: none !important;
        }
    </style>
@endif


    <style>
    /* -------------------------------------------------------- 상품 옵션 시작 */

        /* 상품 옵션 grid - gx2 셀 변경시 색깔 css */
        .opt-cell-changed {
            background: #DC3545 !important;
            color: white;
            font-weight: 700;
        }
        .opt-cell-common {
            background: #ffff99 !important;
        }

        .ag-input-field-input.ag-text-field-input {
            color: #222 !important;
        }

    </style>

	<script language="javascript">

    let gx1;
    let gx2;

    let opt1 = [];
	let opt2 = [];

    let basic_is_single = false; // basic 옵션 기준 싱글, 멀티 구분
    let last_option_row = {};

	@if (count(@$opt['opt2']) > 0)
		@foreach (@$opt['opt2'] as $i => $op)

		opt2[{{$i}}]	= "{{ $op->opt_nm }}";

		@php $i++; @endphp

		@endforeach
	@endif

    const CELL_COLOR = {
        LOCKED: {'background' : '#f5f7f7'},
        YELLOW : {'background' : '#ffff99'}
    };

    let opt1_kind_nm = "{{ @$opt_kind_list[0]->name }}";
    let opt2_kind_nm = "{{ @$opt_kind_list[1]->name }}";

    /**
     * 옵션 좌측 그리드 컬럼 정의
     */
    const option_kind_columns = [
        { field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 30, pinned: 'left', sort: null },
        { field: "type",headerName: "유형", width:50, cellStyle: {"text-align":"center"} },
        { field: "name",headerName: "옵션구분", width:"auto", cellStyle: {"text-align":"center"},
            cellRenderer: (params) => {
                return "<a href='javascript:void(0)' onclick='getOptionStock("+JSON.stringify(params.data)+")'>" + params.value +"</a>";
            }
        },
        { field: "required_yn", headerName: "필수", width:70, cellStyle:{"text-align":"center"} },
        { field: "use_yn", headerName: "사용", width:70, cellStyle:{"text-align":"center"} },
        { field: "no", headerName: "no", hide:true }
    ];

    const basicSingleOptStockColumns = (opt1_kind_nm) => {
        return [
            { field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, cellClass: "locked-cell", checkboxSelection: true, width: 30, pinned: 'left', sort: null },
            { field: "opt1_kind_name", headerName: opt1_kind_nm, width:100, pinned: 'left', suppressMovable: true },
            { field: "opt_price", headerName: "옵션가격", width:100, type: 'numberType', editable: true, cellStyle: CELL_COLOR.YELLOW, suppressMovable: true},
            { field: "good_qty", headerName: "온라인재고", width:80, type: 'numberType', editable: true,
                cellStyle: (params) => ({...CELL_COLOR.YELLOW, 'color': params.data.good_qty_chg_yn === 'Y' ? '#ff4444' : 'none', 'font-weight': params.data.good_qty_chg_yn === 'Y' ? 'bold' : 'normal'})
            },
            { field: "wqty", headerName: "보유재고", width:80, type: 'numberType', editable: true,
                cellStyle: (params) => ({...CELL_COLOR.YELLOW, 'color': params.data.wqty_chg_yn === 'Y' ? '#ff4444' : 'none', 'font-weight': params.data.wqty_chg_yn === 'Y' ? 'bold' : 'normal'})
            },
            { field: "opt_memo", headerName: "옵션메모", width:90, cellStyle: {"text-align":"center"}, editable: true, cellStyle: CELL_COLOR.YELLOW, suppressMovable: true}
        ];
    };

    const basicMultiOptStockColumns = (opt1_kind_nm, opt2_kind_nm) => {
        return [
            {field: "opt1_kind_name", headerName: opt1_kind_nm, width:100, cellStyle: CELL_COLOR.LOCKED, cellClass: "locked-cell", checkboxSelection: true, pinned: 'left', suppressMovable: true},
            {field: "opt_price", headerName: "옵션가격", width:100, type: 'numberType', editable: true, cellStyle: CELL_COLOR.YELLOW, suppressMovable: true,
                cellClassRules: optCellClassRules
            },
            {
                field: "opt2_kind_name",
                headerName: opt2_kind_nm,
                width: 120
            },
            {field: "opt_memo", headerName: "옵션메모", width: 140, cellStyle: {"text-align":"center"}, editable: true, cellStyle: CELL_COLOR.YELLOW, suppressMovable: true,
                cellClassRules: optCellClassRules
            }
        ];
    };

    const optCellClassRules = { // 색 변경 규칙 정의
        "opt-cell-changed": params => {
            const column_name = params.colDef.field;
            if (params.data.hasOwnProperty('is_changed')) {
                return params.data?.is_changed[column_name] ? true : false;
            } else {
                return false;
            }
        }
    };

    const initRightOptStockColumns = (name) => {
        return {
            headerName: name,
            field: name,
            children: [
                {headerName: "온라인재고", field: `${name}_good_qty`, type: 'numberType', width: 70, editable: true, suppressMovable: true,
                    cellStyle: (params) => ({...CELL_COLOR.YELLOW, 'color': params.data[`${name}_good_qty_chg_yn`] === 'Y' ? '#ff4444' : 'none', 'font-weight': params.data[`${name}_good_qty_chg_yn`] === 'Y' ? 'bold' : 'normal'})
                },
                // {headerName: "보유재고", field: `${name}_wqty`, type: 'numberType', width: 58, suppressMovable: true},
                {headerName: "보유재고", field: `${name}_wqty`, type: 'numberType', width: 58, editable: true, suppressMovable: true,
                    cellStyle: (params) => ({...CELL_COLOR.YELLOW, 'color': params.data[`${name}_wqty_chg_yn`] === 'Y' ? '#ff4444' : 'none', 'font-weight': params.data[`${name}_wqty_chg_yn`] === 'Y' ? 'bold' : 'normal'})
                },
            ],
            headerGroupComponent: columnOptDelete
        };
    };

    /**
     * 옵션 우측 그리드 컬럼 정의
     */
    const extra_option_stock_columns = [
        { field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 30, pinned: 'left', sort: null },
        { field: "name", headerName: "옵션명", width:90, type: 'numberType', cellStyle: { "text-align": "left" } },
        { field: "option", headerName: "옵션", width:"auto", type: 'numberType', editable: true, cellStyle: { ...CELL_COLOR.YELLOW, "text-align": "left" }, cellClassRules: optCellClassRules },
        { field: "qty", headerName: "온라인재고", width:80, type: 'numberType', editable: true, cellStyle: CELL_COLOR.YELLOW, cellClassRules: optCellClassRules },
        { field: "wqty", headerName: "보유재고", width:80, type: 'numberType', editable: true, cellStyle: CELL_COLOR.YELLOW, cellClassRules: optCellClassRules },
        { field: "price", headerName: "추가금액", width:80, type: 'numberType', editable: true, cellStyle: CELL_COLOR.YELLOW, cellClassRules: optCellClassRules },
        { field: "soldout_yn", headerName: "품절여부", width:80, editable: true, cellStyle: { ...CELL_COLOR.YELLOW, "text-align": "center" }, cellClassRules: optCellClassRules }
    ];

    let column_opt_del_list = [];
    /**
     * 커스텀 그룹 체크박스 헤더 (컬럼 별 옵션 삭제 - opt2_kind_name 관련)
     */
    class columnOptDelete {
        init(agParams) {
            this.agParams = agParams;
            this.eGui = document.createElement('div');
            this.eGui.innerHTML = `
                <div class="customHeaderLabel">
                    <input ref="eInput" class="customHeaderCheckBox" type="checkbox" tabindex="-1">
                    ${this.agParams.displayName}
                </div>
            `;
            this.eCheckBox = this.eGui.querySelector(".customHeaderCheckBox");
            this.onChangeListener = this.onChange.bind(this);
            this.eCheckBox.addEventListener('change', this.onChangeListener);
        }

        onChange(e) {
            const { target } = e;
            const goods_opt = this.agParams.displayName;
            if (target.checked == true) {
                // 컬럼이 체크된 경우 해당 컬럼 이름을 저장
                column_opt_del_list.push(goods_opt);
            } else if (target.checked == false) {
                // 컬럼을 체크 해제할 경우 해당 컬럼 이름을 배열에서 제거
                const idx = column_opt_del_list.findIndex((item) => {
                    return item == goods_opt;
                });
                column_opt_del_list.splice(idx, 1);
            }
        }

        getGui() {
            return this.eGui;
        }

        destroy() {
            if (this.onChangeListener) {
                this.eCheckBox.removeEventListener('change', this.onChangeListener)
            }
        }
    };

    /**
     * DOM 로딩 이후 옵션 관련 그리드 생성 및 초기화
     */

    document.addEventListener('DOMContentLoaded', (event) => {
        const pApp1 = new App('', { gridId:"#div-gd-optkind" });
        let gridDiv = document.querySelector(pApp1.options.gridId);
        let options = {};
        gx1 = new HDGrid(gridDiv, option_kind_columns);

        const pApp2 = new App('', { gridId:"#div-gd-opt" });
        gridDiv = document.querySelector(pApp2.options.gridId);
        options = {
            onCellValueChanged: (params) => optEvtAfterEdit(params),
            suppressFieldDotNotation: true // 컬럼명에 . 문자가 들어간 경우 깊은 참조로 처리 되는 것을 방지
        }
        gx2 = new HDGrid(gridDiv, extra_option_stock_columns, options);

        searchOptKind();
    });

    const autoSizeColumns = (grid, except = [], skipHeader = false) => {
        const allColumnIds = [];
        grid.gridOptions.columnApi.getAllColumns().forEach((column) => {
            if (except.includes(column.getId())) return;
            allColumnIds.push(column.getId());
        });
        grid.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
    };

    /* custom grid css - 이전 bizest 스타일 형식으로 UI/UX 로 변경 및 적용 */
    const applyBizestColumns = (columns) => {
        const applied_columns = columns.map((column) => {
            if (!column.hasOwnProperty("children")) {
                column.headerClass = column.hasOwnProperty("headerClass")
                    ? `${column.headerClass} bizest`
                    : 'bizest'
            }
            return column;
        })
        return applied_columns;
    };

    const gx2StartEditingCell = (row_index, col_key) => {
        gx2.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

    let changedOptCells = [];
    const optEvtAfterEdit = (params) => {

        if (params.oldValue !== params.newValue) {
            row = params.data;

            const row_index = params.rowIndex;
            const column_name = params.column.colId;
            const value = params.newValue;
            if (column_name == "opt_price") {
                if (isNaN(value) == true || value == "") {
                    alert("숫자만 입력가능합니다.");
                    gx2StartEditingCell(row_index, column_name);
                }
            } else {
                // 온라인 재고인 경우 유효성 검사
                let regExp = /.+(?=_good_qty)/i;
                let arr = column_name.match(regExp);
                regExp = /.+(?=_wqty)/i;
                let arr2 = column_name.match(regExp);
                if (arr || arr2) {
                    if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
                        alert("숫자만 입력가능합니다.");
                        gx2StartEditingCell(row_index, column_name);
                        return false;
                    } else {
                        if (arr) {
                            params.data[column_name + '_chg_yn'] = 'Y';
                            params.api.redrawRows({ rowNodes: [params.node] });
                            gx2.setFocusedWorkingCell();
                        } else if (arr2) {
                            let opt_nm = column_name.split('_wqty')[0];
                            params.data[opt_nm + '_good_qty'] = (isNaN(params.data[opt_nm + '_good_qty'] * 1) ? 0 : params.data[opt_nm + '_good_qty'] * 1) + (params.newValue - params.oldValue);
                            params.data[[opt_nm + '_good_qty_chg_yn']] = 'Y';
                            params.data[column_name + '_chg_yn'] = 'Y';
                            params.api.redrawRows({ rowNodes: [params.node] });
                            gx2.setFocusedWorkingCell();
                        }
                    }
                }
            }

            // 단일 옵션 - 온라인 재고인 경우
            if (column_name == "good_qty") {
                if (isNaN(value) == true || value == "") {
                    alert("숫자만 입력가능합니다.");
                    gx2StartEditingCell(row_index, column_name);
                } else {
                    params.data.good_qty_chg_yn = 'Y';
                    params.api.redrawRows({ rowNodes: [params.node] });
                    gx2.setFocusedWorkingCell();
                }
            }

            // 단일 옵션 - 보유 재고
            if (column_name == "wqty") {
                if (isNaN(value) == true || value == "") {
                    alert("숫자만 입력가능합니다.");
                    gx2StartEditingCell(row_index, column_name);
                } else {
                    params.data.good_qty = params.data.good_qty * 1 + (params.newValue - params.oldValue);
                    params.data.good_qty_chg_yn = 'Y';
                    params.data.wqty_chg_yn = 'Y';
                    params.api.redrawRows({ rowNodes: [params.node] });
                    gx2.setFocusedWorkingCell();
                }
            }

            // 단일 옵션 - 추가 금액
            if (column_name == "price") {
                if (isNaN(value) == true || value == "") {
                    alert("숫자만 입력가능합니다.");
                    gx2StartEditingCell(row_index, column_name);
                }
            }

            // 단일 옵션 - 품절여부
            if (column_name == "soldout_yn") {
                if (value == "Y" || value == "N") {
                } else {
                    alert("품절여부는 Y 또는 N으로 입력해주세요.");
                    gx2StartEditingCell(row_index, column_name);
                }
            }

            // 셀 값 수정시 빨간색으로 변경
            if (row.hasOwnProperty('is_changed')) {
                row.is_changed[column_name] = true;
            } else {
                row.is_changed = {};
                row.is_changed[`${column_name}`] = true;
            }

            gx2.gridOptions.api.applyTransaction({ update : [row] });
        }
    };

    /**
     * 상품 옵션 로직
     */
    const searchOptKind = () => { // 좌측 옵션 그리드 초기화
        if (goods_no) {
            const data = `goods_no=${goods_no}&goods_sub=${goods_sub}`;
            gx1.Request(`/head/product/prd01/${goods_no}/get-option-name`, data, -1, (response) => {
                const { code, body } = response;
                if (code == 200 && body.length > 0) {
                    const first_row = body[0];
                    getOptionStock(first_row);
                }
            });
        }
    };

    const initRightOptSection = async (type = "기본", columns = []) => {
        gx2.setRows([]);
        gx2.gridOptions.api.setColumnDefs([]);
        if (type == "기본") {
            const response = await axios({ url: `/head/product/prd01/${goods_no}/get-basic-opts-matrix`, method: 'get' });
            const { data, status } = response;
            if (status == 200) {
                let { opt_kind_names, is_single } = data;
                if (opt_kind_names && Array.isArray(opt_kind_names)) { // 변경된 옵션구분이 있는 경우 우측 컬럼 초기화
                    opt1_kind_nm = opt_kind_names[0] ? opt_kind_names[0] : "";
                    opt2_kind_nm = opt_kind_names[1] ? opt_kind_names[1] : "";
                    basic_is_single = is_single;
                }
            }
            if (basic_is_single) { // 기본 싱글 옵션인지 기본 멀티 옵션인지 구분하여 컬럼 초기화
                columns = Object.keys(columns).length > 0 ? columns : basicSingleOptStockColumns(opt1_kind_nm);
            } else {
                columns = Object.keys(columns).length > 0 ? columns : basicMultiOptStockColumns(opt1_kind_nm, opt2_kind_nm);
            }
            $("#opt-type").html("기본옵션");
            $(".option-add-btn").html("관리");
            $(".option-inv-btn").show();
        } else if (type == "추가") {
            columns = Object.keys(columns).length > 0 ? columns : extra_option_stock_columns;
            $("#opt-type").html("추가옵션");
            $(".option-add-btn").html("추가");
            $(".option-inv-btn").hide();
        }
        gx2.gridOptions.api.setColumnDefs(columns);
    };

    const getOptionStock = async (row) => {
        const type = row?.type;

        await initRightOptSection(type);

        const GOODS_NO = '{{$goods_no}}';
        row.goods_no = GOODS_NO;

        try {
            const response = await axios({ url: `/head/product/prd01/get-option-stock`, method: 'post', data: { data: row, type: type } });
            const { data, status } = response;
            if (status == 200) {
                last_option_row = row;
                await setRightOptColumns(type);
                setRightOptRows(type, data);
            }
        } catch (error) {
            // console.log(error);
        }
    };

    const setRightOptRows = async (type, { result }) => {

        if (type == "기본") {

            let list = [];

            if (basic_is_single) { // 기본 상품 단일 옵션

                list = result.map((item) => {
                    return { ...item, opt1_kind_name: item?.goods_opt };
                });

            } else { // 기본 상품 멀티 옵션

                result.reduce((prev, item) => {

                    let { idx, opt1_kind_name } = prev;
                    const { goods_no, goods_opt, opt_name, opt_memo, opt_price, good_qty, wqty } = item;
                    const basic_opts = goods_opt.split('^');
                    const opt_names = opt_name.split('^');

                    if (opt1_kind_name != basic_opts[0]) idx++;

                    opt1_kind_name = basic_opts[0];
                    const opt2_kind_name = basic_opts[1];

                    list[idx] = { opt1_kind_name: opt1_kind_name, opt_memo: opt_memo, opt_price: opt_price, ...list[idx] };
                    list[idx][`${opt2_kind_name}_good_qty`] = good_qty;
                    list[idx][`${opt2_kind_name}_wqty`] = wqty;

                    return {idx: idx, opt1_kind_name: opt1_kind_name};

                }, {idx: 0, opt1_kind_name: ""});

                list.shift();

            }

            gx2.setRows(list);

        } else if (type == "추가") {

            try {
                const { no } = last_option_row;
                const response = await axios({
                    url: `/head/product/prd01/get-extra-options`, method: 'post',
                    data: { 'option_no': no }
                });

                const { data, status } = response;
                if (status == 200) {
                    gx2.setRows(data);
                }
            } catch (error) {
                // console.log(error);
            }

        }
    };

    const setRightOptColumns = async (type) => {

        if (type == "기본") {

            try {
                const response = await axios({ url: `/head/product/prd01/${goods_no}/get-basic-opts-matrix`, method: 'get' });
                const { data, status } = response;
                if (status == 200) {

                    let { opt_matrix } = data;
                    let { opt1, opt2 } = opt_matrix;

                    let rightOptColumns = gx2.gridOptions.api.getColumnDefs();

                    let stock_cols = [];
                    opt2.map((item, idx) => {
                        const goods_opt_nm = item.opt_nm;
                        stock_cols[idx] = initRightOptStockColumns(goods_opt_nm);
                    });

                    let opt2_count;
                    let opt2_child_count;
                    rightOptColumns = rightOptColumns.map((column) => { // opt2 종류별로 재고 컬럼 정의
                        if (column.field == "opt2_kind_name") {
                            column.children = stock_cols;
                            opt2_count = column.children.length;
                            opt2_child_count = column.children[0].children.length;
                        }
                        return column;
                    });

                    await gx2.gridOptions.api.setColumnDefs(applyBizestColumns(rightOptColumns));
                    gx2.gridOptions.columnApi.moveColumn("opt_memo", 2 + opt2_count * opt2_child_count); // 옵션 메모 열을 맨 뒤로 보냄

                    // 세로 옵션 두개 이하인 경우 옵션메모 크기 늘림
                    if (opt2.length <= 2) {
                        autoSizeColumns(gx2, ["opt1_kind_name", "opt_memo"]);
                    } else {
                        autoSizeColumns(gx2, ["opt1_kind_name"]);
                    }

                }
            } catch (error) {
                // console.log(error);
            }

        } else if (type == "추가") {

        }

    };

    /*
    ***
    상품 옵션명 관리 관련
    ***
    */

    $(".option-kind-add-btn").on("click", function(e) {
        e.preventDefault();
        $("#option_add").css("display", "block");
    })
    $(".option-kind-del-btn").on("click", function(e) {
        e.preventDefault();
        delOptionKind();
    })
    $(".option-kind-add-cancel-btn").on("click", function() {resetAddOptionKindBox()});

    function resetAddOptionKindBox() {
        $("#option_add").css("display", "none");
        $("#opt_type_nm").val("");
    }

    function addOptionKind() {

        const basic_count = gx1.getRows().filter(item => item.type === '기본').length;

        if ($("[name='opt_type']").val() === 'basic' && basic_count >= 2) return alert("기본옵션은 최대 2개까지 설정 가능합니다.");
        if ($("[name='opt_type_nm']").val() === '') return alert("옵션구분값을 입력해주세요.");

        if (basic_count == 1 && ($("[name='opt_type']").val() === 'basic')) {
            if(!confirm("2단옵션으로 변경됩니다. 진행하시겠습니까? \n(단일옵션을 다시 사용하려면 삭제후 재등록하셔야 합니다.)")) return;
        }

        $.ajax({
            async: true,
            type: 'post',
            url: `/head/product/prd01/${goods_no}/option-kind-add`,
            data: {
                'opt_type': document.f1.opt_type.value,
                'opt_type_nm': document.f1.opt_type_nm.value,
                'opt_required_yn': document.f1.opt_required_yn.value,
                'opt_use_yn': document.f1.opt_use_yn.value,
                'basic_count': basic_count
            },
            success: async function (res) {
                if (res.code === 200) {

                    if ($("[name='opt_type']").val() === 'basic' && basic_count == 0) location.reload();

                    // resetAddOptionKindBox();
                    // searchOptKind();

                    // 사용안함인 경우 api에 none 뜨는 버그 방지 (goods_no가 null 인 경우)
                    // const GOODS_NO = document.f1.goods_no.value;
                    // controlOption.SetGoodsNo(GOODS_NO);
                    // initOptGridAndApi();

                    window.location.reload(); // 추가시 관리팝업에서 옵션구분이 다른 경우가 있어 새로고침 처리

                } else alert(res.msg);
            },
            error: function(request, status, error) {
                console.log(request, status, error)
            }
        });

    }

    // 옵션구분 삭제
    function delOptionKind() {
        let selected_list = gx1.getSelectedRows();

        if(selected_list.length < 1) return alert("삭제하실 옵션구분을 선택하십시오.");
        if(!confirm("옵션구분을 삭제하게 되면, 해당 옵션구분으로 등록된 모든 옵션이 삭제됩니다. 정말 삭제하시곘습니까?")) return;

        $.ajax({
            async: true,
            type: 'post',
            url: `/head/product/prd01/${goods_no}/option-kind-del`,
            data: {
                'del_id_list': selected_list.map(s => s.no).join(","),
                'goods_sub': goods_sub,
                'goods_type': $('#goods_type').val(),
            },
            success: function (res) {
                if(res.code === 200) {
                    // searchOptKind();
                    // initOptGridAndApi();
                    window.location.reload(); // 삭제시 관리팝업에서 옵션구분이 다른 경우가 있어 새로고침 처리
                }
                else alert(res.msg);
            },
            error: function(request, status, error) {
                console.log(request, status, error)
            }
        });
    }

    // 옵션 사용여부 변경 시 기존 옵션정보 및 재고정보 모두 삭제
    function delOptionAll() {
        let rows = gx1.getRows();
        let is_option_use = $("[name=is_option_use]:checked").val();
        $.ajax({
            async: true,
            type: 'post',
            url: `/head/product/prd01/${goods_no}/option-kind-del`,
            data: {
                'del_id_list': rows.map(s => s.no).join(","),
                'goods_sub': goods_sub,
                'goods_type': $('#goods_type').val(),
                'is_option_use': is_option_use
            },
            success: function (res) {
                if(res.code === 200) {
                    searchOptKind();
                    initOptGridAndApi();
                    $("#goods_qty").val(0);
                }
                else alert(res.msg);
            },
            error: function(request, status, error) {
                console.log(request, status, error)
            }
        });
    }

    const initOptGridAndApi = async () => {
        getOptionStock(last_option_row);
        $('#ControlOptionModal .close').trigger('click');
        $("#div-gd-option").html("");
        document.control_option.reset();
        controlOption.SetGrid("#div-gd-option");
    };

    /*
    ***
    상품 기본 옵션 품목 관리 관련
    ***
    */
    $(".option-add-btn").on("click", async function(e) {

        e.preventDefault();

        const type = last_option_row?.type;
        if (type == "추가") {
            const option_no = last_option_row?.no;
            const row = { ...last_option_row,
                option_no: option_no, soldout_yn: "N",
                qty: 0, wqty: 0, price: 0
            };

            gx2.gridOptions.api.applyTransaction({add : [row]});

            return false;
        }

        const GOODS_NO = document.f1.goods_no.value;
        controlOption.Open(GOODS_NO,
            /**
             * afterSaveOrDel 콜백 정의
             */
            async (data) => {
                initOptGridAndApi();
            }
        );

    });

    // 옵션 삭제 (공통)
    $(".option-del-btn").on("click", function(e) {
        e.preventDefault();
        const type = last_option_row?.type;
        if (type == "기본") {
            optDeleteInGrid();
        } else if (type =="추가") {
            const rows = gx2.getSelectedRows();
            gx2.gridOptions.api.applyTransaction({remove : rows});
        }
    });

    const optDeleteInGrid = async () => {
        let del_opt_list = [];
        const rows = gx2.getSelectedRows();

        rows.map((item, idx) => {
            const { opt1_kind_name } = item;
            del_opt_list.push({goods_opt: opt1_kind_name, opt_name: opt1_kind_nm, goods_no: parseInt(goods_no)});
        });

        column_opt_del_list.map((opt2_kind_name, idx) => {
            del_opt_list.push({goods_opt: opt2_kind_name, opt_name: opt2_kind_nm, goods_no: parseInt(goods_no)});
        });

        if (Array.isArray(del_opt_list) && !(del_opt_list.length > 0)) {
            alert('삭제할 옵션을 선택해주세요.');
            return false;
        } else {
            if (!confirm("체크된 옵션을 삭제하시겠습니까? \n(하나의 옵션구분만 남게되면 등록된 모든 옵션이 삭제됩니다.)")) return false;

            try {
                const response = await axios({ url: `/head/product/prd01/${goods_no}/delete-basic-options`,
                    method: 'post', data: { del_opt_list: del_opt_list }
                });
                const { code, msg } = response?.data;
                if (code == 200) {

                    alert(msg);
                    column_opt_del_list = [];

                    // 삭제후 옵션구분값 표시안되는 버그 방지 (goods_no가 null 인 경우)
                    const GOODS_NO = document.f1.goods_no.value;
                    controlOption.SetGoodsNo(GOODS_NO);

                    initOptGridAndApi();

                } else alert(msg);
            } catch (error) {
                console.log(error);
            }
        };
    };

    /**
     * 상품 옵션 관련 재고 및 메모, 가격 일괄 수정
     */
	$('.option-sav-btn').click(function(e){
		e.preventDefault();
        const type = last_option_row?.type;
        const rows = gx2.getRows();
        if (type == "기본") {
            updateBasicOptsData(rows);
        } else if (type =="추가") {
            updateExtraOptsData(rows);
        }
	});

    const updateBasicOptsData = async (rows) => {

        let data = [];

        if (basic_is_single) {

            data = rows.map((item) => {
                return { ...item, opt1: item?.opt1_kind_name }
            });

        } else {

            const opt_name = `${opt1_kind_nm}^${opt2_kind_nm}`;

            rows.map((row, idx) => {

                const keys = Object.keys(row);
                const { opt1_kind_name, opt_price, opt_memo } = row;
                keys.reduce((prev, key) => {

                    let obj = {};
                    let { opt1, opt2, goods_opt, good_qty, wqty, opt_price, opt_memo } = prev;
                    obj.opt1 = opt1;
                    obj.opt_price = opt_price;
                    obj.opt_memo = opt_memo;

                    if (!opt2) { // prefix 추출하여 opt2 값 알아냄
                        let regExp = /.+(?=_good_qty)/i; // 전방탐색
                        let arr = key.match(regExp);
                        if (arr) obj.opt2 = arr[0];
                    }

                    if ((opt2)) {
                        obj.goods_opt = opt1 + "^" + opt2;
                    }

                    // 온라인 재고 할당
                    obj.good_qty = good_qty;
                    regExp = /.+(?=_good_qty)/i;
                    let arr_2 = key.match(regExp);
                    if (arr_2) {
                        obj.good_qty = parseInt(row[`${arr_2[0]}_good_qty`]);
                    }

                    obj.wqty = wqty;
                    regExp = /.+(?=_wqty)/i;
                    arr_2 = key.match(regExp);
                    if (arr_2) {
                        obj.wqty = parseInt(row[`${arr_2[0]}_wqty`]);
                    }

                    // 루프돌면서 백앤드에서 사용하는 형식에 맞는 goods_opt 만들었으면 data에 push함
                    if (obj?.goods_opt) {
                        data.push(obj);
                        obj.opt_name = opt_name;
                    }

                    return obj;

                }, { goods_opt : "", opt1 : opt1_kind_name, opt2: "", good_qty: 0, wqty: 0, opt_price: opt_price, opt_memo: opt_memo });

            });
        }

        const response = await axios({ url: `/head/product/prd01/${goods_no}/update-basic-opts-data`,
            method: 'post', data: { data: data }
        });
        data = response?.data;
        if (data?.code == 500) {
            alert(data.msg)
        } else if (data?.code == 200) {
            alert(data.msg)
            initOptGridAndApi();
        };

    };

    const updateExtraOptsData = async (rows) => {
        for (let i = 0; i < rows.length; i++ ) {
            const option = rows[i]?.option;
            if (option == "") {
                alert("빈 옵션 값이 있습니다.");
                return false;
            }
        }
        const response = await axios({ url: `/head/product/prd01/update-extra-opts-data`,
            method: 'post', data: { data: rows, opt_no: last_option_row?.no }
        });
        data = response?.data;
        if (data?.code == 200) {
            alert("저장되었습니다.");
            initOptGridAndApi();
        } else if (data?.code == 500) {
            alert("저장중 에러가 발생했습니다. 잠시 후 다시 시도해주세요.");
        };
    };

    const openOptsStock = () => { // 상품관리 - 입고 오픈
        const url = `/head/product/prd01/${goods_no}/stock`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=960");
    };

    /**
     * -------------------------------------------------------- 상품 옵션 끝
     */

    // 해당 상품의 기획전 포함 정보 삭제
    function deletePlanning(d_cat_cd){
        if(!confirm("삭제하시겠습니까?")) return;
        var data = {
            'goods_sub': goods_sub,
            'd_cat_cd': d_cat_cd,
        }
        $.ajax({
            type: 'post',
            url: `/head/product/prd01/${goods_no}/planing-delete`,
            data: data,
            dataType: 'json',
            success: function (data) {
                if(data.code === 200) {
                    location.reload();
                } else {
                    alert (data.msg);
                }
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    // 해당 상품의 할인쿠폰 포함 정보 삭제
    function deleteCoupon(coupon_no){
        if(!confirm("삭제하시겠습니까?")) return;
        var data = {
            'goods_sub': goods_sub,
            'coupon_no': coupon_no,
        }
        $.ajax({
            type: 'post',
            url: `/head/product/prd01/${goods_no}/coupon-delete`,
            data: data,
            dataType: 'json',
            success: function (data) {
                if(data.code === 200) {
                    location.reload();
                } else {
                    alert (data.msg);
                }
            },
            error: function(request, status, error) {
                console.log(request.responseJSON);
            }
        });
    }

    /*
    ***
    유사 상품 관리 관련
    ***
    */

    let gx3;

    $(document).ready(function() {
        if (type !== 'create') SetSimilarTable();

        $(".similar-add-btn").on("click", function(e) {
            e.preventDefault();
            const url = '/head/product/prd01/choice';
            window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        })
        $(".similar-del-btn").on("click", function(e) {
            e.preventDefault();
            DeleteSimilarGoods();
        })
    });

    function SetSimilarTable() {
        const goods_similar_columns = [
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left'},
            {field: "img" , headerName:"이미지", type: 'GoodsImageType'},
            {field: 'goods_nm', headerName: "상품명", width: 200},
            {field: 'brand', headerName: "브랜드"},
            {field: 'com_nm', headerName: "업체"},
            {field: 'opt_kind_cd', headerName: "품목", width: 120},
            {field: 'cat_nm', headerName: "대표카테고리", width: 200},
            {field: 'price', headerName: "판매가", cellRenderer: (params) => params.value.toLocaleString('ko-KR')+"원"},
            {field: 'admin_nm', headerName: "관리자(아이디)", cellRenderer: (params) => {return `${params.value} (${params.data.admin_id})`}},
            {field: 'rt', headerName: "등록일시", cellRenderer: (params) => params.value.substring(0, 10)},
        ];

        const pApp3 = new App('', { gridId: "#goods-similar-grid" });
        const gridDiv = document.querySelector(pApp3.options.gridId);
        gx3 = new HDGrid(gridDiv, goods_similar_columns);

        SearchSimilarGoods();
    }

    function SearchSimilarGoods() {
        const data = `goods_sub=${goods_sub}`;
        gx3.Request(`/head/product/prd01/${goods_no}/get-similar-goods`, data, -1);
    }

    function ChoiceGoodsNo(nos, add_goods) {
        $.ajax({
            async: true,
            type: 'post',
            url: `/head/product/prd01/${goods_no}/similar-goods-add`,
            data: {
                'add_goods': add_goods,
                'goods_sub': goods_sub,
            },
            success: function (res) {
                if(res.code === 200) {
                    SearchSimilarGoods();
                }
                else alert(res.message);
            },
            error: function(error) {
				alert(error.responseJSON.message);
            }
        });
    }

    function DeleteSimilarGoods() {
        const del_goods = gx3.getSelectedRows().map(row => `${row.goods_no}||${row.goods_sub}||${row.similar_no}`);
        if(del_goods.length < 1) return alert('삭제하실 상품을 선택해주세요.');
        if(!confirm("선택하신 상품을 유사 상품에서 삭제하시겠습니까?")) return;

        $.ajax({
            async: true,
            type: 'delete',
            url: `/head/product/prd01/${goods_no}/similar-goods-del`,
            data: {
                'del_goods': del_goods,
                'goods_sub': goods_sub,
            },
            success: function (res) {
                if(res.code === 200) {
                    SearchSimilarGoods();
                }
                else alert(res.message);
            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    /*
    ***
    세일설정 관련
    ***
    */

    const ori_price = '{{  @$goods_info->price }}';
	const ori_sale_rate = '{{  @$goods_info->sale_rate }}';
    const ori_sale_price = '{{  @$goods_info->sale_price }}';
    const ori_sale_margin = '{{  @$goods_info->sale_margin }}';
    const ori_normal_wonga = '{{  @$goods_info->normal_wonga }}';
    const ori_sale_wonga = '{{  @$goods_info->sale_wonga }}';

    function setSaleDate(start, end) {
        $("input[name=sale_s_dt]").val(getDate(start));
        $("input[name=sale_e_dt]").val(getDate(end));

        // 시간옵션세팅
        const options = [...Array(24).keys()].map((i) => (i < 10 ? "0" : "") + i);
        $(".select-time").each(function(i, node) {
            let opt_html = '';
            options.forEach(opt => opt_html += `<option value=${opt}>${opt}</option>`);
            node.innerHTML = opt_html;
        })

        $("select[name='sale_s_dt_tm']").val(getTime(start));
        $("select[name='sale_e_dt_tm']").val(getTime(end));

        // date -> 날짜 추출
        function getDate(str) {
            return str.substr(0, 10);
        }

        // date -> 시간 추출
        function getTime(str) {
            return str.substr(11,2);
        }
    }

    // 세일기간 사용 미선택 시 해당입력 row -> disabled 처리
    function setUseDisplayDate(is_use) {
        $("[name='sale_dt_yn']").val(is_use ? "Y" : "N");
        $("input[name='sale_s_dt']").attr("disabled", !is_use);
        $(".sale_s_dt_btn").attr("disabled", !is_use);
        $("input[name='sale_e_dt']").attr("disabled", !is_use);
        $(".sale_e_dt_btn").attr("disabled", !is_use);
        $("select[name='sale_s_dt_tm']").attr("disabled", !is_use);
        $("select[name='sale_e_dt_tm']").attr("disabled", !is_use);
		if(is_use === true) {
			$("[name='price']").val(Comma(ori_price));
		} else {
			$("[name='price']").val(Comma($("[name='sale_price']").val()));
		}
    }

    function setSaleAmount(is_reset, is_sale_rate) {
        if(is_reset) {

            $("[name='price']").val(Comma(ori_price));
            $("[name='sale_rate']").val(0);
            $("[name='sale_price']").val(0);
            $("[name='sale_margin']").val(0);
            $("[name='sale_wonga']").val(Comma(ori_sale_wonga));
        } else {
            let sale_rate = 0;
            let sale_price = 0;

            if(is_sale_rate) {
                sale_rate = unComma($("[name='sale_rate']").val());
                sale_price = Math.round(ori_price * (100 - sale_rate) / 100);
            }
            else {
                sale_price = unComma($("[name='sale_price']").val());
                sale_rate = Math.round(100 - (100 / (ori_price / sale_price)));
            }

            let sale_margin = 0; // 작업필요
            let sale_wonga = Math.round(ori_normal_wonga * (100 - sale_rate) / 100);

            $("[name='price']").val(Comma(sale_price));
            $("[name='sale_rate']").val(Comma(sale_rate));
            $("[name='sale_price']").val(Comma(sale_price));
            $("[name='sale_margin']").val(Comma(sale_margin));
            $("[name='sale_wonga']").val(Comma(sale_wonga));
        }
    }

    $(document).on("keyup", "input[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );})

    function setSaleOnclickEvent() {
        $("#sale_yn").on("change", function() {
            $("[name='sale_yn']").val(this.checked ? "Y" : "N");
            $('.sale_control').css("display", this.checked ? "table-row" : "none");
            $('[name="price"]').prop("readonly", this.checked);
            if(!this.checked && window.confirm("세일을 취소하였습니다.(저장 후에 반영됩니다.)\n판매가 및 원가를 정상으로 변경하시겠습니까?")) {
                setSaleAmount(true);
            }
        });
        $(".cancel-sale-btn").on("click", function(e) {
            e.preventDefault();
            $('.sale_control').css("display", "none");
            $("#sale_yn").prop("checked", false);
            if(!this.checked && window.confirm("세일을 취소하였습니다.(저장 후에 반영됩니다.)\n판매가 및 원가를 정상으로 변경하시겠습니까?")) {
                setSaleAmount(true);
            }
        });
        $("#sale_dt_yn").on("click", function(e) {
            setUseDisplayDate(e.target.checked);
        });
        $("[name='sale_rate']").on("keyup", function() {
            if(this.value > 100) {
                alert("세일율은 100%를 넘을 수 없습니다.\n다시 입력해주세요.");
                return this.value = '';
            }
            setSaleAmount(false, true);
        });
        $("[name='sale_price']").on("keyup", function() {
            if(unComma(this.value) > ori_price) {
                alert("세일가를 정상가보다 작게 입력해주세요.");
                return this.value = '';
            }
            setSaleAmount(false, false);
        });
    }

    </script>

    <script language="javascript">

    // 아래부터 관련상품 관련 로직

    $(document).ready(function() {
        if(goods_no > 0){
            get_add_info();
        }
        const hide_related_products = document.f1.related_cfg?.value == "A" ? true : false;
        hide_related_products
            ? document.querySelector(".related_goods_area").style.display = "none"
            : null
    });

    function get_add_info() {
        $.ajax({
            type: "get",
            url: '/head/product/prd01/' + goods_no + '/get-addinfo',
            dataType: 'json',
            // data: {},
            success: function (res) {
                gx_related_goods.setRows(res.goods_related);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }

        });
    }

    /**
     * 관련상품 - goods api 관련 - api에서 직접 invoke할 함수들은 var로 선언함
     */
    const addRow = (row) => {
        const GOODS_NO = document.f1.goods_no.value;
        row.r_goods_no = row.goods_no; // 삭제 가능하도록 매칭 상품번호를 관련상품번호에 매칭
        if (row.goods_no == GOODS_NO) {
            alert('추가하려는 관련 상품 중 현재 수정중인 상품은 제외되었습니다.')
            return false;
        }
        gx_related_goods.gridOptions.api.applyTransaction({add : [row]});
    };

    const deleteRow = (row) => { gx_related_goods.gridOptions.api.applyTransaction({remove : [row]}); };

    var goodsCallback = (row) => {
        addRelatedGoods(row);
    }

    var multiGoodsCallback = (rows) => {
        addRelatedGoods(rows);
    };

    /**
     * 관련상품 - 미구현이였던 기존 관련상품 로직 연동 및 추가
     */
    function relatedGoods(obj) {
	    if (obj.value == "A") {
            document.querySelector(".related_goods_area").style.display = "none";
	    } else {
            document.querySelector(".related_goods_area").style.display = "block";
            gx_related_goods.gridOptions.api.resetRowHeights(); // 첫 행 높이 깨짐 버그 수정
	    }
    }

    const openAddRelatedGoods = () => { // goods api 팝업 오픈
        const url=`/head/api/goods/show`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    };

    const checkDuplicated = (data) => { // 관련상품 추가시 중복 row 검사
        const rows = gx_related_goods.getRows();
        const r_goods_nos = rows.map((row, index) => {
            return row?.r_goods_no;
        });
        data = data.filter((row, index) => {
            if (!r_goods_nos.includes(row?.goods_no)) return row;
        });
        return data;
    };

    const addRelatedGoods = (data) => { // 관련상품 추가

        data = Array.isArray(data) ? data : [data]; // 단일 상품인 경우 배열로 감쌈
        data = checkDuplicated(data);

        const related_goods = data.reduce((acc, row, idx) => {
            const r_goods_no = row?.goods_no;
            const r_goods_sub = row?.goods_sub;
            const set = `${r_goods_no}|${r_goods_sub}`;
            return (idx == 0) ? set : acc + ',' + set;
        }, "");

        if (related_goods == "") {
            alert("이미 반영된 상품입니다.")
            return false;
        }

        const CMD = "add_related_goods";
        var related_cfg = document.f1.related_cfg.value;
        var cross_yn = document.f1.cross_yn ? "Y" : "";

        $.ajax({
            type: "post",
            url: '/head/product/prd01/add-related-goods',
            dataType: 'json',
            data: {
                cmd: CMD,
                goods_no: document.f1.goods_no.value,
                goods_sub: document.f1.goods_sub.value,
                cross_yn: cross_yn,
                related_cfg: related_cfg,
                related_goods: related_goods
            },
            success: function (response) {
                if (response == "1") {
                    data.map((row, index) => {
                        addRow(row);
                    });
                } else if (response == "0") {
                    alert("[ 관련상품 작업 실패 ] 관리자에게 문의해 주십시오.");
                }
            },
            error: function(xhr, status, error) {
                alert("[ 관련상품 작업 실패 ] 관리자에게 문의해 주십시오.");
            }
        });

    }

    function delRelatedGood(row) { // 관련상품 삭제

        const { r_goods_no, r_goods_sub } = row;
        const CMD = "del_related_goods";

        $.ajax({
            type: "post",
            url: '/head/product/prd01/del-related-good',
            dataType: 'json',
            data: {
                cmd: CMD,
                goods_no: document.f1.goods_no.value,
                goods_sub: document.f1.goods_sub.value,
                r_goods_no: r_goods_no,
                r_goods_sub: r_goods_sub
            },
            success: function (response) {
                if (response == "1") {
                    deleteRow(row);
                } else if(response == "0") {
                    alert("[ 관련상품 작업 실패 ] 관리자에게 문의해 주십시오.");
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("[ 관련상품 작업 실패 ] 관리자에게 문의해 주십시오.");
            }
        });

    }

    // 평균원가 히스토리 조회팝업 오픈
    function openWongaPopup() {
        const url = "/head/product/prd03/wonga?goods_no=" + document.f1.goods_no.value + "&goods_sub=" + document.f1.goods_sub.value;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=200,width=1024,height=900");
    }

	function isMobile()
	{
		return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
	}

	function openFrontUrl()
	{
		let url = '<?php echo e(@$front_url->value); ?>';

		if(!isMobile){
			url = '<?php echo e(@$front_url->mvalue); ?>';
		}

		window.open(`https://${url}/app/product/detail/<?php echo e(@$goods_no); ?>`, '_blank');
	}
	
	</script>

	<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
    <link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >
@stop
