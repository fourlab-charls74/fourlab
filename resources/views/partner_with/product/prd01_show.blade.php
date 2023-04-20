@extends('partner_with.layouts.layout-nav')
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

    table tr th {
        background : #f5f5f5;
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
                <a href="https://{{ $shop_domain }}/app/product/detail/{{ $goods_no }}" class="btn btn-sm btn-outline-primary shadow-sm" target="_blank">
                <i class="bx bx-link-external mr-1"></i>상품조회
                </a>
                <a href="#" class="btn btn-sm btn-outline-primary shadow-sm copy-btn">
                    <i class="bx bx-copy-alt mr-1"></i>복사
                </a>
                @endif
                <a href="#" class="btn btn-sm btn-primary shadow-sm save-btn"><i class="bx bx-save mr-1"></i>저장</a>
            </div>
        </div>
        <form name="f1" id="f1">
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
                                                <select id="category_select_display" name="d_category" class="select_cat" size="4">
                                                @forelse($displays as $display)
                                                    <option value="{{$display->d_cat_cd}}|{{$display->seq}}|{{$display->disp_yn}}">{{$display->full_nm}} - {{$display->d_cat_cd}}</option>
                                                @empty
                                                    <option value=''>카테고리를 추가해 주십시오.</option>
                                                @endforelse
                                                </select>
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
                                                <select id="category_select_item" name="u_category" class="select_cat" size="4">
                                                @forelse($items as $item)
                                                    <option value="{{$item->d_cat_cd}}|{{$item->seq}}|{{$item->disp_yn}}">{{$item->full_nm}} - {{$item->d_cat_cd}}</option>
                                                @empty
                                                    <option value=''>카테고리를 추가해 주십시오.</option>
                                                @endforelse
                                                </select>
                                            </dl>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품 세부 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                @if( $type == '' )
                                <div class="img_box cum_slider_cont p-4">
                                    <img src="{{config('shop.image_svr')}}{{@$goods_info->img}}?{{@$goods_info->img_upate}}" onerror="this.src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='" alt="{{@$goods_info->goods_nm}}">
                                </div>
                                <div class="cum_slider_thum_wrap">
                                    <div class="sd_prev pop_sd_btn bg-secondary"><i class="bx bx-left-arrow"></i></div>
                                    <div class="inbox">
                                        <ul class="cum_slider_thum">
                                            <li>
                                                <a href="#" class="active">
                                                    <img src="{{config('shop.image_svr')}}{{@$goods_images[0]}}?{{@$goods_info->img_upate}}" onerror="this.src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='">
                                                </a>
                                            </li>
                                            @for ($i = 1; $i < count($goods_images); $i++)
                                                <li>
                                                    <a href="#"><img src="{{config('shop.image_svr')}}{{@$goods_images[$i]}}?{{@$goods_info->img_upate}}" alt="22"></a>
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
                                    <button type="button" id="img-show" class="btn btn-success waves-effect waves-light" data-no="https://{{ $shop_domain }}/app/product/detail/{{ $goods_no }}">
                                        <i class="bx bx-images font-size-14 align-middle"></i> 상품보기
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 mt-4 mt-lg-4">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th class="required" id="th">상품명</th>
                                            <td style="width:35%">
                                                <div class="form-inline">
                                                    <input type="text" class="form-control form-control-sm search-all w-100" name="goods_nm" id="goods_nm" maxlength="100" value="{{ @$goods_info->goods_nm  }}" />
                                                </div>
                                            </td>
                                            <th class="required" id="th">상품명(영문)</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" class="form-control form-control-sm search-all w-100" name="goods_nm_eng" id="goods_nm_eng" maxlength="100" value="{{ @$goods_info->goods_nm_eng  }}" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><p>상단 홍보글</p></th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" name="head_desc" class="form-control form-control-sm search-all"z value="{{ @$goods_info->head_desc }}" readonly>
                                                </div>
                                            </td>
                                            <th><p>하단 홍보글</p></th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" class="form-control form-control-sm search-all" name="ad_desc" id="ad_desc" value="{{ @$goods_info->ad_desc }}" readonly/>
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
                                            <th class="required">브랜드</th>
                                            <td>
                                                <div class="wd300">
                                                    <div class="form-inline inline_btn_box">
                                                        <input type="text" name="brand_nm" id="brand_nm" value="{{@$goods_info->brand_nm}}" class="form-control form-control-sm ac-brand" style="width:70%;">
														<input type="text" name="brand_cd" id="brand_cd" value="{{@$goods_info->brand}}" class="form-control form-control-sm ml-1" style="width:28%;" readonly>
                                                        <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">상품상태</th>
                                            <td>
                                                <div class="txt_box flax_box">
                                                    <div class="mr-1">
                                                    @if( $type != '' )
                                                        @foreach ($goods_stats as $goods_stat)
                                                            @if ($goods_stat->code_id === @$goods_info->sale_stat_cl )
                                                                {{ $goods_stat->code_val }}
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <select name="sale_stat_cl" id="sale_stat_cl" class="form-control form-control-sm">
															<option value="">상품상태</option>
                                                        @foreach ($goods_stats as $goods_stat)
                                                            <option value="{{$goods_stat->code_id}}" @if ($goods_stat->code_id == @$goods_info->sale_stat_cl ) selected @endif>
                                                                {{ $goods_stat->code_val }}
                                                            </option>
                                                        @endforeach
                                                        </select>
                                                    @endif
                                                    </div>
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
                                            <th class="required">스타일넘버</th>
                                            <td>
                                                <div class="input_box wd300">
                                                    <input type='text' class="form-control form-control-sm ac-style-no search-all" name='style_no' id='style_no' value='{{ @$goods_info->style_no  }}'>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">상품구분</th>
                                            <td>
                                                <div class="wd300">
                                                    @if($type == 'create')
                                                    <input value="P" type="hidden"/>
                                                    <span class="ml-1" style="font-size: 13px; margin-right: 2px; font-weight:500;">위탁상품</span>
                                                    <x-tool-tip>
                                                        <x-slot name="arrow">top</x-slot>
                                                        <x-slot name="align">left</x-slot>
                                                        <x-slot name="html">
                                                            위탁판매 : 입점사가 상품에 대한 판매를 위탁하고 일정한 수수료를 지급하는 상품<br/>
                                                            위탁매입 : 입점사가 상품에 대한 재고를 선 제공 후, 판매된 매출에 대하여 정산하는 상품</br>
                                                            ※ 매입, 위탁매입 상품은 보유재고 관리가 가능합니다.
                                                        </x-slot>
                                                    </x-tool-tip>
                                                    @else
                                                    <span class="ml-1" style="font-size: 13px; margin-right: 2px; font-weight:500;">위탁판매</span>
                                                    <x-tool-tip>
                                                        <x-slot name="arrow">top</x-slot>
                                                        <x-slot name="align">left</x-slot>
                                                        <x-slot name="html">
                                                            위탁판매 : 입점사가 상품에 대한 판매를 위탁하고 일정한 수수료를 지급하는 상품<br/>
                                                        </x-slot>
                                                    </x-tool-tip>
                                                    @endif
                                                </div>
                                            </td>
                                            <th class="required">업체</th>
                                            <td>
                                                <div class="input_box wd300">
													<div class="form-inline inline_btn_box">
														<input type="hidden" name="com_type" id="com_type" value="{{ @$goods_info->com_type }}" >
														<input type="hidden" name="margin_type" id="margin_type" value="{{ @$goods_info->margin_type }}">
                                                        <span class="ml-1" style="font-size: 13px; margin-right: 2px; font-weight:500;">{{ @$com_nm }}</span>
													</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><p>제조사</p></th>
                                            <td>
                                                <div class="input_box wd300">
                                                    <input type='text' class="form-control form-control-sm search-all" name='make' id='make' value='{{ @$goods_info->make  }}' />
                                                </div>
                                            </td>
                                            <th class="required">원산지</th>
                                            <td>
                                                <div class="input_box wd300">
                                                    <input type='text' class="form-control form-control-sm search-all" name='org_nm' id='org_nm' value='{{ @$goods_info->org_nm  }}' />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><p>판매가</p></th>
                                            <td>
                                                <div class="flax_box">
                                                    <div class="input_box wd200">
                                                        <input type='text' class="form-control form-control-sm search-all text-right" name='price' id='price' value='{{ @number_format(@$goods_info->price) }}'
                                                            @if( $type == "" && @$goods_info->sale_stat_cl >= 30) readonly @endif>
                                                    </div>
                                                    <div class="txt_box ml-1">원</div>
                                                </div>
                                            </td>
                                            <th><p>시중가</p></th>
                                            <td>
                                                <div class="flax_box">
                                                    <div class="input_box wd200">
                                                        <input
                                                        type='text'
                                                        class="form-control form-control-sm search-all text-right"
                                                        name='goods_sh'
                                                        id='goods_sh'
                                                        value='{{@number_format(@$goods_info->goods_sh)}}'
                                                        >
                                                    </div>
                                                    <div class="txt_box ml-1">원</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><p>원가</p></th>
                                            <td>
                                                <div class="flax_box">
                                                    <div class="input_box wd200">
                                                        <input type='text' class="form-control form-control-sm search-all text-right" name='wonga' id='wonga' value='{{@number_format(@$goods_info->wonga)}}'
                                                            @if( $type == "" || @$goods_info->sale_stat_cl >= 30) readonly @endif>
                                                    </div>
                                                    <div class="txt_box ml-1">원</div>
                                                </div>
                                                <p class="font-size-12 mt-1 mb-0">
                                                    *원가는 자동계산됩니다.
                                                </p>
                                            </td>
                                            <th><p>수수료</p></th>
                                            <td>
                                                <div class="flax_box">
                                                    <div class="input_box wd200">
                                                        <input type='text' id="margin" name="margin" class="form-control form-control-sm search-all text-right"
                                                            value='{{@sprintf("%.2f",$goods_info->pay_fee)}}' readonly>
                                                    </div>
                                                    <div class="txt_box ml-1">%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
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
                                            <th class="required">배송정보</th>
                                            <td>
                                                <div class="form-inline wd400">
                                                    <div class="form-inline-inner input_box">
                                                        <select name="baesong_info" id="baesong_info"  class="form-control form-control-sm search-all">
															<option value="" class="required">==배송지역==</option>
                                                            <option value="1" @if(@$goods_info->baesong_info == "1") selected @endif>국내배송</option>
                                                            <option value="2" @if(@$goods_info->baesong_info == "2") selected @endif>해외배송</option>
                                                        </select>
                                                    </div>
                                                    <span class="text_line">/</span>
                                                    <div class="form-inline-inner input_box">
                                                        <select name="baesong_kind" id="baesong_kind" class="form-control form-control-sm search-all">
															<option value="" class="required">==배송업체==</option>
                                                            <option value="1" @if(@$goods_info->baesong_kind == "1") selected @endif >본사배송</option>
                                                            <option value="2" @if(@$goods_info->baesong_kind == "2") selected @endif >입점업체배송</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <th><p>배송비 지불</p></th>
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
                                                    @if($type == "create")
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="point_cfg" class="custom-control-input" value="S" checked/>
                                                            <label class="custom-control-label" for="point_cfg">쇼핑몰 정책 : 구매 금액의 {{$g_order_point_ratio}}% 적립</label>
                                                        </div>
                                                    @else
                                                        <input type="hidden" name="point_cfg" value="{{$goods_info->point_cfg}}"/>
                                                        <input type="hidden" name="point_yn" value="{{$goods_info->point_yn}}"/>
                                                        <input type="hidden" name="point" value="{{$goods_info->point}}"/>
                                                        @if($goods_info->point_yn == "Y")
                                                            {{ @$goods_info->point  }} 원 ( 구매 금액의 {{$g_order_point_ratio}}% 적립 )
                                                        @else
                                                        <div class="txt_box">
                                                            지급안함
                                                        </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><p>등록일시</p></th>
                                            <td>
                                                <div class="txt_box">
                                                    {{ @$goods_info->reg_dm }}
                                                </div>
                                            </td>
                                            <th><p>수정일시</p></th>
                                            <td>
                                                <div class="txt_box">
                                                    {{ @$goods_info->upd_dm }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
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
                                <div class="table-box">
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
                                                        <input type="radio" name="is_option_use" value="N" id="is_option_use_n" class="custom-control-input" {{ (@$goods_info->is_option_use=="N")? "checked" : "" }}>
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
                                    </table>
                                </div>
                            </div>
                        </div>
						@if( $type == '' && @$goods_info->is_option_use == 'Y' )
                        <div class="row" id="option_kind_area">
                            <div class="col-6">
                                <div class="table-responsive mt-1">
                                    <div class="pt-1 pb-1 text-right">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm optionkind-add-btn"><span class="fs-12">추가</span></a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm optionkind-sav-btn"><span class="fs-12">저장</span></a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm optionkind-del-btn"><span class="fs-12">삭제</span></a>
                                    </div>
                                    <div id="div-gd-optkind" style="height:240px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <ul class="nav nav-tabs mt-1" id="optionTab">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#option-basic-tab" role="tab" aria-controls="option-basic-tab" aria-selected="true">기본</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#option-extra-tab" role="tab" aria-controls="option-extra-tab" aria-selected="false">추가</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active mt-1" id="option-basic-tab" role="tabpanel">
                                        <div class="pt-1 pb-1 text-right">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm option-add-btn"><span class="fs-12">추가</span></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm option-sav-btn"><span class="fs-12">저장</span></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm option-del-btn"><span class="fs-12">삭제</span></a>
                                        </div>
                                        <div id="div-gd-opt" style="height:200px;" class="ag-theme-balham"></div>
                                        <script>
                                            var columns_opt = [
                                                {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
                                                {field:"name",headerName:"옵션구분", width:90,rowDrag: true },
                                                {field:"option",headerName:"옵션", width:130,editable:true,cellClass:['hd-grid-edit'] },
                                                {field:"price",headerName:"옵션가", width:90,type: 'numberType',editable:true,cellClass:['hd-grid-number','hd-grid-edit'] },
                                                {field:"memo",headerName:"메모", width:90, editable:true,cellClass:['hd-grid-edit'],cellStyle:{"text-align":"center"} },
                                                {field:"option_no",headerName:"option_no", hide:true},
                                                {field:"no",headerName:"no", hide:true}
                                            ];
                                            var gx2 = new HDGrid(document.querySelector("#div-gd-opt"), columns_opt);
                                            gx2.gridOptions.rowDragManaged = true;
                                            gx2.gridOptions.animateRows = true;
                                        </script>
                                    </div>
                                    <div class="tab-pane fade mt-1" id="option-extra-tab" role="tabpanel">
                                        <div class="pt-1 pb-1 text-right">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm optionextra-add-btn"><span class="fs-12">추가</span></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm optionextra-sav-btn"><span class="fs-12">저장</span></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm optionextra-del-btn"><span class="fs-12">삭제</span></a>
                                        </div>
                                        <div id="div-gd-opt-extra" style="height:200px;" class="ag-theme-balham"></div>
                                        <script>
                                                var columns_option_extra = [
                                                    {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
                                                    {field:"name",headerName:"옵션구분", width:90 },
                                                    {field:"option",headerName:"옵션", width:130,editable:true,cellClass:['hd-grid-edit'] },
                                                    {field:"price",headerName:"옵션가", width:90,type: 'numberType',editable:true,cellClass:['hd-grid-number','hd-grid-edit'] },
                                                    {field:"qty",headerName:"재고", width:90,type: 'numberType',editable:true,cellClass:['hd-grid-number','hd-grid-edit'] },
                                                    {field:"memo",headerName:"메모", width:90, editable:true,cellClass:['hd-grid-edit'],cellStyle:{"text-align":"center"} },
                                                    {field:"option_no",headerName:"option_no", hide:true},
                                                    {field:"no",headerName:"no", hide:true}
                                                ];
                                                gx3 = new HDGrid(document.querySelector("#div-gd-opt-extra"), columns_option_extra);
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if( $type == '' )
                        <div class="row">
                            <div class="col-12">
                                <div class="card-body pt-2">
                                    <div class="card-title">
                                        <div class="filter_wrap">
                                            <div class="fl_box px-0 mx-0">
                                            </div>
                                            <div class="fr_box">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm stock-sav-btn"><span class="fs-12">저장</span></a>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm stock-stk-btn"><span class="fs-12">입고</span></a>
                                                <!-- <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm option-inv-btn"><span class="fs-12">입고</span></a>//-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <div id="div-gd-option" style="height:50PX; width:100%;" class="ag-theme-balham"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

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
											</tr>
										</thead>
										<tbody>
										@forelse($coupon_list as $row)
											<tr>
												<td style="padding:5px;">
													<div class="txt_box" style="font-size:12px;">{{ $row->coupon_nm }}</div>
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
                                            </dt>
                                            <dd>
                                                <div class="area_box edit_box">
                                                    <textarea name="goods_cont" id=" " class="form-control editor1">{{ @$goods_info->goods_cont }}</textarea>
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
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @forelse($planing as $row)
                                                            <tr>
                                                                <td>{{ $row->title }}</td>
                                                                <td>{{ $row->plan_date_yn  }} </td>
                                                                <td>{{ $row->start_date }} ~ {{ $row->end_date }}</td>
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
                                                                {field: "img" , headerName:"이미지",
                                                                    cellRenderer: (params) => {
                                                                        if (params.value !== undefined && params.data.img != "") {
                                                                            return '<img class="img" src="' + params.data.img + '"/>';
                                                                        }
                                                                    },
                                                                    cellStyle: related_goods_style_obj
                                                                },
                                                                {field: "img", headerName: "이미지_url", hide: true, cellStyle: related_goods_style_obj},
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
                @if(count($class_items) > 0 && $type === '')
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
                                    <span class="d-none d-sm-inline">선택한 상품을</span>
                                    <select class="form-control form-control-sm goods_class" style="width:130px;display:inline">
                                        <option value="">선택</option>
                                        @foreach ($class_items as $class_item)
                                            <option value='{{ $class_item->class }}' @if(@$goods_info->class === $class_item->class) selected @endif>
                                                {{ $class_item->class_nm }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="d-none d-sm-inline">로</span>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm goods-info-change-btn"><span class="fs-12">분류변경</span></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm goods-info-save-btn px-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="저장"><i class="bx bx-save fs-14"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm goods-info-delete-btn px-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="삭제"><i class="far fa-trash-alt fs-12"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="goods-class-grid" style="height:250px;width:100%;" class="ag-theme-balham"></div>
                            <script>
                                const goods_class_columns = [
                                    {field:"class",headerName:"분류코드",hide:true},
                                    {field:"class_nm",headerName:"분류" },
                                    {field:"item",headerName:"항목코드"},
                                    {field:"item_nm",headerName:"항목",width:300},
                                    {field:"value",headerName:"내용",width:400,editable:true,cellClass:'hd-grid-edit'},
                                ];
                                let gx = new HDGrid(document.querySelector("#goods-class-grid"), goods_class_columns);
                            </script>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </form>
    </div>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<!-- script -->
<!-- @include('partner_with.product.prd01_js') -->
<!-- script -->

<script type="text/javascript" charset="utf-8">
    const goods_no = '{{$goods_no}}';
    const goods_sub = '{{@$goods_info->goods_sub}}';

    var ed;

    $(document).ready(function(){
        $('#main-tab').trigger("click");
    });


    $(document).ready(function() {
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
            minHeight: 100,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/images/goods_cont',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions, true);

        const TYPE = "{{$type}}";
        if (TYPE == "create") {
            document.f1.reset();
        }

    });


    $("#search_brand_nm").keyup(function(e){
        if(e.keyCode == 13){
            search_brand();
        }
    });

    function PopPrdDetail(goods_no, goods_sub){
        window.open("/partner/product/prd01/"+goods_no,"Product Detail");
    }

    $(".btn-rep-add").click(function(){ addRepCategory(); });

    $(".btn-item-add").click(function(){ addSearchCategory('item'); });
    $(".btn-display-add").click(function(){ addSearchCategory('display'); });

    $(".btn-item-delete").click(function(){ deleteCategory('item') });
    $(".btn-display-delete").click(function(){ deleteCategory('display') });


    function addRepCategory()
    {
        var cat_type	= "display";

        searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
        {
            if(code.length < mx_len)
            {
                alert("최하단에서만 등록 가능합니다");
                return false;
            }
            $("[name=rep_cat_cd]").val(code);
            $('#txt_rep_cat_nm').html(full_name);
            addCategory(cat_type, code, name, full_name, mx_len);
        });
    }

    function addSearchCategory(cat_type)
    {
        searchCategory.Open(cat_type.toUpperCase(), function(code, name, full_name, mx_len)
        {
            addCategory(searchCategory.type, code, name, full_name, mx_len);
        });
    }

    function addCategory(cat_type, code, name, full_name, mx_len)
    {
        if(code.length < mx_len)
        {
            alert("최하단에서만 등록 가능합니다");
            return false;
        }

        cat_type = cat_type.toLowerCase();
        const options	= $('#category_select_'+cat_type+' option');

        var codes = [];
        $.each(options, function(idx, option){
            if(option.value !== ""){
                const txt = option.value.split("|");
                if (txt[0] === code) {
                    alert("중복된 카테고리가 있습니다.");
                    return false;
                }
                codes.push(txt[0]);
            }
        });

        var seq = 1;
        $('#category_select_'+cat_type+' option[value=""]').remove();

        // 전시 뎁스
        arr_txt	= full_name.split('>');
        cat_txt	= "";

        for( i = 0; i < arr_txt.length; i++ )
        {
            options.length++;
            if( i !== 0 )	cat_txt = cat_txt + ">";
            cat_txt	+= arr_txt[i];
            icode	= code.substring(0,(i*3)+3);
            if(codes.includes(icode) === false){
                $('#category_select_'+cat_type).append(`<option value="${icode}|${seq}|Y">${cat_txt} - ${icode}</option>`);
            }
        }
    }

    function deleteCategory(cat_type)
    {
        if( $("#category_select_"+cat_type+" option:selected").val() == undefined )
        {
            alert("삭제할 카테고리를 선택해 주십시오.");
            return false;
        }
        var ar		    = $("#category_select_"+cat_type+" option:selected").val().split('|');
        var d_cat_cd	= ar[0];

        const options	= $('#category_select_'+cat_type+' option');
        $.each(options, function(idx, option){
            var ar2		= option.value.split("|");
            if( d_cat_cd === ar2[0].substring(0, d_cat_cd.length) )
            {
                option.remove();
            }
        });
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
            }

        });
    }

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

    function validate(){
        var f = document.f1;

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
        if( $("#goods_nm_eng").val() == "" ){
            alert("상품명(영문)을 입력해 주십시오.");
            $("#goods_nm_eng").focus();
            return false;
        }
        if( $("#opt_kind_cd").val() == "" ){
            alert("품목을 선택해 주십시오.");
            $("#opt_kind_cd").focus();
            return false;
        }
        if( $("#brand_nm").val() == "" ){
            alert("브랜드를 선택해 주십시오.");
            $("#brand_nm").focus();
            return false;
        }
        if( $('#sale_stat_cl').val() == "" ){
            alert("상품상태를 선택해 주십시오.");
            $('#sale_stat_cl').focus();
            return false;
        }
        if( $("#style_no").val() == "" ){
            alert("스타일넘버를 입력해 주십시오.");
            $("#style_no").focus();
            return false;
        }
        if( $('#goods_type').val() == "" ){
            alert('상품구분을 선택해 주십시오.');
            $('#goods_type').focus();
            return false;
        }
        if( $('#com_id').val() == "" ){
            alert('업체를 선택해 주십시오.');
            $('#com_nm').focus();
            return false;
        }
        if( $("#org_nm").val() == "" ) {
            alert("원산지를 입력해 주십시오.");
            $("#org_nm").focus();
            return false;
        }
        if( $("#price").val() == "0" ) {
            if( !confirm("입력하신 정상가는 0원 입니다. 저장 하시겠습니까?") ){
                $("#price").focus();
                return false;
            }
        }
        if( $('#tax_yn').val() == "" ){
            alert("과세구분을 선택해 주십시오.");
            $('#tax_yn').focus();
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

        return true;
    }

    //store
    $('.save-btn').click(function(){
        if (!validate()) return;

        $("#restock:checked").attr("name", "restock_yn");

        let frm	= $("#f1");
        let d_cat_str	= "";
        let u_cat_str	= "";
        let md_nm	= $('#md_id > option:selected').html();

        md_nm = md_nm.replace(/(\s)|(\t)|(\n)/g, "");

        $('#md_nm').val(md_nm);
        $("#goods_cont").val(ed.html());

        //전시 카테고리 전송값
        $("#category_select_display option").each(function(){
            if( $(this).val() != "" ){
                //d_cat_str	+= ","+$(this).text();
                d_cat_str	+= ","+$(this).val();
            }
        });

        $("#d_category_s").val(d_cat_str);

        //용도 카테고리 전송값
        $("#category_select_item option").each(function(){
            if($(this).val() !="") {
                //u_cat_str += ","+$(this).text();
                u_cat_str += ","+$(this).val();
            }
        });

        $("#u_category_s").val(u_cat_str);

        @if ($type === '')
        const type	= 'put';
            @else
        const type	= 'post';
        @endif

        $.ajax({
            async: true,
            type: type,
            url: '/partner/product/prd01',
            data: frm.serialize(),
            success: function (data) {
                if (!isNaN(data * 1)) {
                    const TYPE = "{{$type}}";
                    if (TYPE == "create") {
                        alert("상품이 등록되었습니다.");
                        opener.Search();
                        location.href="/partner/product/prd01/" + data;
                    } else {
                        alert("변경된 내용이 정상적으로 저장 되었습니다.");
                        opener.Search();
                        location.href="/partner/product/prd01/" + data;
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
        window.open("/partner/product/prd02/"+goods_no+"/image","_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=960");
        @endif
    });

    $("#img-show").click(function(){
        window.open($(this).attr("data-no"));
    });

    //수정 페이지에서 복사버튼 클릭했을 경우.
    $(".copy-btn").click(function(e){
        e.preventDefault();

        location.href="/partner/product/prd01/"+goods_no+"?type=copy";
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

    $('#price, #goods_price, #margin').keyup(function(){

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
            if(price > 0){
                var margin	= unComma($('#margin').val());
                if( margin == '' )	margin = 0;
                var wonga = parseInt(Math.round(price * (1-margin/100)),10);
                $("#wonga").val(Comma(wonga));
            }
        }
        else if( $('#goods_type').val() == "S" ){
            //공급업체
            var price	= unComma($('#price').val());
            var margin	= unComma($('#margin').val());
            var wonga	= unComma($('#wonga').val());

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

        }

    });

    $('.stock-stk-btn').click(function(){
        window.open("/partner/product/prd01/"+goods_no+"/in-qty","_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=800");
    });

    //상품정보고시 카드가 나올경우만 실행
    if( $('#goods-class-grid').length > 0 ){

        //선택한 항목 상태변경
        $('.goods-info-change-btn').click(function(e){
            e.preventDefault();

            const s_goods_class_cd	= $('.goods_class').val();
            // const s_goods_class_nm	= $('.goods_class > option:selected').html();

            if( s_goods_class_cd === '' ) {
                alert('분류변경할 정보고시내용을 선택해주세요.');
                return;
            }

            goodsClassSearch();
            // row	= gx.gridOptions.api.getRowNode(0);
            // row.data.class		= s_goods_class_nm.replace(/(\s)|(\t)|(\n)/g,"");
            // row.data.class_cd	= s_goods_class_cd;
            // gx.gridOptions.api.redrawRows({
            //     rowNodes : [row]
            // });
        });

        //선택된 상품정보고시 저장
        $('.goods-info-save-btn').click(function(e){
            e.preventDefault();

            classes	= gx.getRows();
            //return;
            $.ajax({
                async: true,
                type: 'put',
                url: `/partner/product/prd01/goods-class-update`,
                data: {'goods_no':goods_no,'goods_sub':goods_sub,'classes':classes},
                success: function (data) {
                    alert("저장하였습니다.");
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText)
                }
            });
        });

        //선택된 상품정보고시 삭제
        $('.goods-info-delete-btn').click(function(e){

            if( confirm("삭제하시겠습니까?") ){

                $.ajax({
                    async: true,
                    type: 'put',
                    url: `/partner/product/prd01/goods-class-delete`,
                    data: {'goods_no':goods_no,'goods_sub':goods_sub},
                    success: function (data) {
                        alert("삭제하였습니다.");
                    },
                    error: function(request, status, error) {
                        console.log("error")
                    }
                });
            }
        });

        gx.gridOptions.getRowNodeId = function(data) {
            return data.rownum;
        };

        function goodsClassSearch() {
            const class_value = $('.goods_class').val();
            const data = `goods_no=${goods_no}&goods_sub=${goods_sub}&goods_class=${class_value}`;
            gx.Request(`/partner/product/prd01/${goods_no}/goods-class`, data, -1);
        }
        goodsClassSearch();
    }

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
            console.log(ouTnum,pgwidth,ouTnum-pgwidth);
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
    });

    //ESC 클릭시 창 닫기
    $(document).keydown(function(e){
        // ESCAPE key pressed
        if (e.keyCode == 27) {
            window.close();
        }
    });

</script>



<script language="javascript">

    const pApp1 = new App('',{
        gridId:"#div-gd-optkind",
    });
    let gx1;

    const pApp2 = new App('',{
        gridId:"#div-gd-opt",
    });
    let gx_opt = null;
    let gridOptDiv = document.querySelector("#div-gd-option");

    var columns_stock = [
        {field: "option", headerName: "옵션", width: 200, sortable: "true"},
    ];

    //상품 옵션 종류
    if( $('#div-gd-optkind').length > 0 ){
        var columns_optkind = [
            {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
            {field:"no",headerName:"번호",
                cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        if(params.data.type === "extra"){
                            return '<a href="javascript:void(0);" onclick="return Search_optextra(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                        } else {
                            return params.value;
                        }
                    }
                }
            },
            {field:"type",headerName:"유형", width:70, cellStyle:{"text-align":"center"},
                editable: function(params){ return (params.data !== undefined && params.data.no > 0)? false:true; },
                cellClass:['hd-grid-edit'],
                //cellEditor: 'agRichSelectCellEditor',
                //cellEditorParams: cellOpionsParams,
                cellEditorSelector: function(params) {
                    return {
                        component: 'agRichSelectCellEditor',
                        params: {
                            values: ['basic','extra']
                        }
                    };
                }
            },
            {field:"name",headerName:"옵션구분", width:120,editable:true,cellClass:['hd-grid-edit'] },
            {field:"required_yn",headerName:"필수", width:70, cellStyle:{"text-align":"center"} },
            {field:"use_yn",headerName:"사용", width:70, cellStyle:{"text-align":"center"}},
    ];

        $(document).ready(function() {
            let gridDiv = document.querySelector(pApp1.options.gridId);
            gx1 = new HDGrid(gridDiv, columns_optkind);

            Search_optkind();

        });

        function Search_optkind() {
            if(goods_no) {
                gx1.Request(`/partner/product/prd01/${goods_no}/get-option-name`, '', -1);
            }
        }
    }

    //상품 옵션 재고
    if( $('#div-gd-opt').length > 0 ){
        $(document).ready(function() {
            Search_opt();
        });
        function Search_opt() {
            if(goods_no) {
                gx2.Request(`/partner/product/prd01/${goods_no}/get-option`, '', -1);
            }
        }
    }

    $(".optionkind-add-btn").on("click", function(){
        gx1.addRows([{
            "chk":0,
            "type":'basic',
            "name" : '',
            "required_yn" : 'Y',
            "use_yn" : 'Y',
            "no" : '',
        }]);
    });

    $(".optionkind-del-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 옵션구분을 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/del-option-name',
                    data:{'optionkinds':selectrows},
                    success: function (res) {
                        gx1.delSelectedRows();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".optionkind-sav-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('저장할 옵션구분을 선택 해 주십시오.');
        } else {
            if(confirm('저장하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/save-option-name',
                    data:{'optionkinds':selectrows},
                    success: function (res) {
                        alert('저장하였습니다.');
                        Search_optkind();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".option-add-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('추가할 옵션구분을 선택 해 주십시오.');
        } else {
            if(selectrows[0]["no"] > 0){
                gx2.addRows([{
                    "chk":0,
                    "name":selectrows[0]["name"],
                    "option" : '',
                    "price" : 0,
                    "memo" : '',
                    "option_no" : selectrows[0]["no"],
                    "no" : '',
                }]);
            } else {
                alert('추가할 옵션구분을 저장 후 선택 해 주십시오.')
            }
        }
    });

    $(".option-sav-btn").on("click", function(){
        var selectrows = gx2.getSelectedRows();
        if(selectrows.length === 0){
            alert('저장할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('저장하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/save-option',
                    data:{'options':selectrows},
                    success: function (res) {
                        alert('저장하였습니다.');
                        Search_opt();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".option-del-btn").on("click", function(){
        var selectrows = gx2.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까? 삭제하시면 재고수량도 삭제됩니다.')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/del-option',
                    data:{'options':selectrows},
                    success: function (res) {
                        gx2.delSelectedRows();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });


    $(document).ready(function() {
        if(goods_no > 0){
            GoodsOption();
        }
    });

    function ViewOptions(options,qty,wqty,sales){
        //console.log(options);
        //console.log(qty);
        if (gridOptDiv !== null) {

            if(gx_opt == null){
                for(i=0;i<options[0].length;i++){
                    columns_stock.push(
                        {field: 'opt_' + options[0][i], headerName:options[0][i],type:'numberType', editable: true, cellClass:['hd-grid-number','hd-grid-edit'],
                            cellStyle:function(params){
                                return (params.value === 0 || params.value === '')? {color:'#FF0000'}: {};
                            },
                            onCellValueChanged:EditQty
                        },
                    );
                }
                gx_opt = new HDGrid(gridOptDiv, columns_stock);
            }

            rows = [];
            //console.log(options[1].length);
            if(options[1].length == 0){
                var row = {"option":''};
                for(i=0;i<options[0].length;i++){
                    var opt2 = options[0][i];
                    var field = "opt_" + opt2;
                    sale = 0;
                    if (sales.hasOwnProperty(opt2)) {
                        // your code here
                        sale  = wqtys[opt2];
                    }
                    //row[field] = qty[opt2] + ' / ' + wqty[opt2] + ' / ' + sale;
                    row[field] = qty[opt2];
                }
                rows.push(row);
            } else {
                for(j=0;j<options[1].length;j++){
                    var opt1= options[1][j];
                    var row = {"option":opt1};
                    for(i=0;i<options[0].length;i++){
                        var opt2 = options[0][i];
                        var field = "opt_" + opt2;
                        sale = 0;
                        if (sales.hasOwnProperty(opt2+'^'+opt1)) {
                            // your code here
                            sale  = wqtys[opt2+'^'+opt1];
                        }
                        //row[field] = qty[opt2+'^'+opt1] + ' / ' + wqty[opt2+'^'+opt1] + ' / ' + sale;
                        row[field] = qty[opt2+'^'+opt1];
                    }
                    rows.push(row);
                }
            }
            gx_opt.setRows(rows);
            gx_opt.gridOptions.api.setDomLayout('autoHeight');
            // auto height will get the grid to fill the height of the contents,
            // so the grid div should have no height set, the height is dynamic.
            document.querySelector('#div-gd-option').style.height = '';
            if(columns_stock.length <= 5){
                gx_opt.gridOptions.api.sizeColumnsToFit();
            }
        }
    }

    $('[name=is_option_use]').change(function(e){
        if( $('#is_option_use_n').is(":checked") == true ){
            //작업 해야함
            $('#option_kind_area').css('display','none');
            gx_opt = null;
            columns_stock = [columns_stock.shift()];
            $('#div-gd-option').html('');
            GoodsOption();
        }else{
            $('#option_kind_area').css('display','');
            gx_opt = null;
            columns_stock = [columns_stock.shift()];
            $('#div-gd-option').html('');
            GoodsOption();
        }
        // if(confirm("변경 시 등록되어 있는 옵션 정보와 재고 수량이 모두 삭제됩니다.\n변경 하시겠습니까?")){
        //     //DeleteOptionAll();
        // }
    });

    function GoodsOption() {

        var is_option_use = $("input[name=is_option_use]:checked").val();

        $.ajax({
            type: "get",
            url: '/partner/product/prd01/' + goods_no + '/get-stock',
            dataType: 'json',
            data:{'is_option_use':is_option_use},
            // data: {},
            success: function (data) {
                // console.log(data);
                ViewOptions(data.options,data.qty,data.wqty,[]);
            },
            error: function (e) {
                console.log(e.responseText);
            }
        });
    }

    $(".stock-sav-btn").on("click", function(){
        if(confirm('재고정보를 수정하시겠습니까?')){
            console.log(_stock_qty);
            var is_option_use = $("input[name=is_option_use]:checked").val();

            $.ajax({
                type: 'post',
                url: '/partner/product/prd01/' + goods_no + '/save-stock',
                dataType:'json',
                data:{'stocks':_stock_qty,'is_option_use':is_option_use},
                success: function (res) {
                    alert('재고정보를 수정햐였습니다.');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    });

    var _stock_qty = new Object();

    function EditQty(params){
        if (params.oldValue !== params.newValue) {
            var opt = params.colDef.field;
            if(params.data.option !== ""){
                opt = opt + '^' + params.data.option;
            }
            _stock_qty[opt] = params.newValue;
            // params.data[params.colDef.field + '_chg_yn'] = 'Y';
            // var rowNode = params.node;
            // rowNode.setSelected(true);
            // gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
            // //gridOptions.api.refreshCells({rowNodes:[rowNode]});
            // gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);
        }
    }

    function Search_optextra() {
        $('#optionTab a[href="#option-extra-tab"]').tab('show');
        gx3.Request(`/partner/product/prd01/${goods_no}/get-option-extra`, '', -1);
    }

    $(".optionextra-add-btn").on("click", function(){
        var selectrows = gx1.getSelectedRows();
        if(selectrows.length === 0){
            alert('추가할 옵션구분을 선택 해 주십시오.');
        } else {
            gx3.addRows([{
                "chk":0,
                "name":selectrows[0]["name"],
                "option" : '',
                "price" : 0,
                "qty" : 0,
                "wqty" : 0,
                "memo" : '',
                "option_no" : selectrows[0]["no"],
                "no" : '',
            }]);
        }
    });

    $(".optionextra-sav-btn").on("click", function(){
        var selectrows = gx3.getSelectedRows();
        if(selectrows.length === 0){
            alert('저장할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('저장하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/save-option-extra',
                    data:{'optionextras':selectrows},
                    success: function (res) {
                        alert('저장하였습니다.');
                        Search_opt();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });

    $(".optionextra-del-btn").on("click", function(){
        var selectrows = gx3.getSelectedRows();
        if(selectrows.length === 0){
            alert('삭제할 옵션을 선택 해 주십시오.');
        } else {
            if(confirm('삭제하시겠습니까?')){
                $.ajax({
                    type: 'post',
                    url: '/partner/product/prd01/' + goods_no + '/del-option-extra',
                    data:{'optionextras':selectrows},
                    success: function (res) {
                        gx3.delSelectedRows();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            }
        }
    });


</script>

<script language="javascript">

    $(document).ready(function() {
        if(goods_no > 0){
            get_add_info();
        }

        const hide_related_products = document.f1.related_cfg.value == "A" ? true : false;
        hide_related_products
            ? document.querySelector(".related_goods_area").style.display = "none"
            : null
    });

    function get_add_info() {
        $.ajax({
            type: "get",
            url: '/partner/product/prd01/' + goods_no + '/get-addinfo',
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
        const url=`/partner/api/goods/show`;
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
            url: '/partner/product/prd01/add-related-goods',
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
            url: '/partner/product/prd01/del-related-good',
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

</script>

@stop
