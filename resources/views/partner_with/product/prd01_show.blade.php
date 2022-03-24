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
</style>

    <script type="text/javascript" src="/handle/editor/editor.js"></script>
    <script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
    <script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                @if( $type == '')
                <h3 class="d-inline-flex">상품수정</h3>
                @elseif ( $type == 'create')
                <h3 class="d-inline-flex">상품생성</h3>
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
                        <ul class="row category_list">
                            <li class="col-lg-4">
                                <dl>
                                    <dt class="d-flex align-items-center justify-content-between">
                                        <div>
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
													<input type="hidden" name="rep_cat_cd" value="{{ @$goods_info->rep_cat_cd }}">
													<a href="#" id="txt_rep_cat_nm">{{ @$goods_info->rep_cat_nm }}</a>
												</li>
                                            </ul>
                                        </div>
                                    </dd>
                                </dl>
                            </li>
                            <li class="col-lg-4 mt-2 mt-lg-0">
                                <dl class="choice">
                                    <dt class="d-flex align-items-center justify-content-between">
                                        <div>
                                            전시 카테고리
                                        </div>
                                        <div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
                                            <button type="button" class="btn btn-sm btn-primary btn-display-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가"  data-toggle="modal" data-target="#category_list_modal">
                                                <i class="bx bx-plus"></i>
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
                            <li class="col-lg-4 mt-2 mt-lg-0">
                                <dl>
                                    <dt class="d-flex align-items-center justify-content-between">
                                        <div>
                                            용도 카테고리
                                        </div>
                                        <div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
                                            <button type="button" class="btn btn-sm btn-primary btn-item-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가"  data-toggle="modal" data-target="#category_list_modal">
                                                <i class="bx bx-plus"></i>
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
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품 세부 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
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
                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="table-box">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th class="required">상품명</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" class="form-control form-control-sm search-all" name="goods_nm" id="goods_nm" maxlength="100" value="{{ @$goods_info->goods_nm  }}" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">상품명(영문)</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" class="form-control form-control-sm search-all" name="goods_nm_eng" id="goods_nm_eng" maxlength="100" value="{{ @$goods_info->goods_nm_eng  }}" />
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
                                        </tr>
                                        <tr>
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
                                        </tr>
                                        <tr>
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
													{{--
                                                    @if( $type != '' )
                                                        @foreach ($goods_stats as $goods_stat)
                                                            @if ($goods_stat->code_id === @$goods_info->sale_stat_cl )
                                                                {{ $goods_stat->code_val }}
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @else
													--}}
                                                        <select name="sale_stat_cl" id="sale_stat_cl" class="form-control form-control-sm">
															<option value="">상품상태</option>
                                                        @foreach ($goods_stats as $goods_stat)
                                                            <option value="{{$goods_stat->code_id}}" @if ($goods_stat->code_id == @$goods_info->sale_stat_cl ) selected @endif>
                                                                {{ $goods_stat->code_val }}
                                                            </option>
                                                        @endforeach
                                                        </select>
													{{--
                                                    @endif
													--}}
                                                    </div>
                                                    <div class="custom-control custom-checkbox form-check-box">
                                                        <input type="checkbox" class="custom-control-input" value="Y" id="restock" {{ (@$goods_info->restock_yn=="Y") ? "checked" : "" }}>
                                                        <label class="custom-control-label" for="restock">재 입고함</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
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
                                                    <select name="goods_type" id="goods_type" class="form-control form-control-sm">
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
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">업체</th>
                                            <td>
                                                <div class="input_box wd300">
													<div class="form-inline inline_btn_box">
														<input type="hidden" name="com_type" id="com_type" value="{{ @$goods_info->com_type }}" >
														<input type="hidden" name="margin_type" id="margin_type" value="{{ @$goods_info->margin_type }}">
                                                        {{ @$goods_info->com_nm }}
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
                                        </tr>
                                        <tr>
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
                                        </tr>
                                        <tr>
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
                                        </tr>
                                        <tr>
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
                                        </tr>
                                        <tr>
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
                                        </tr>
                                        <tr>
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
                                        </tr>
                                        <tr>
                                            <th class="required">적립금</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{ @$goods_info->point  }} 원
                                                </div>
											</td>
                                        </tr>
                                        <tr>
                                            <th><p>상품위치</p></th>
                                            <td>
                                                <div class="input_box wd200">
                                                    <input type='text' class="form-control form-control-sm search-all" name='goods_location' id='goods_location' value='{{ @$goods_info->goods_location }}'>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><p>등록일시</p></th>
                                            <td>
                                                <div class="txt_box">
                                                    {{ @$goods_info->reg_dm  }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
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
                                                {field:"option",headerName:"옵션", width:13,editable:true,cellClass:['hd-grid-edit'] },
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
                                                <div id="div-gd-related-goods" style="height:300px;" class="ag-theme-balham"></div>
                                                <script>
                                                    const columns_related_goods = [
                                                        {field: "goods_no", headerName: "상품번호", width: 100 },
                                                        {field: "opt_kind_nm", headerName: "품목" },
                                                        {field: "brand_nm", headerName: "브랜드" },
                                                        {field: "style_no", headerName: "스타일넘버" },
                                                        {field: "img", headerName: "이미지", type: 'GoodsImageType' },
                                                        {field: "img", headerName: "이미지_url", hide: true},
                                                        {field: "goods_nm", headerName: "상품명", type: 'GoodsNameType' },
                                                        {field: "sale_stat_cl", headerName: "상품상태", type: 'GoodsStateTypeLH50'},
                                                        {field: "price", headerName: "판매가", type: 'currencyType' },
                                                    ];
                                                    let gx_related_goods = new HDGrid(document.querySelector("#div-gd-related-goods"), columns_related_goods);
                                                </script>
                                            </dd>
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">상품 변경 내역</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div id="div-gd-history" style="height:200px;" class="ag-theme-balham"></div>
                                <script>
                                    const columns_history = [
                                        {field: "upd_date", headerName: "변경일", type: 'DateTimeType' },
                                        {field: "name", headerName: "이름(ID)",
                                            cellRenderer: function(params) {
                                                if (params.value !== undefined && params.data.id != "") {
                                                    return params.value + '(' + params.data.id + ')';
                                                }
                                            }
                                        },
                                        {field: "memo", headerName: "변경사유",width:250 },
                                        {field: "head_desc", headerName: "상단홍보글" },
                                        {field: "price", headerName: "판매가", type: 'currencyType'},
                                        {field: "wonga", headerName: "원가", type: 'currencyType'},
                                        {field: "margin", headerName: "마진", type: 'currencyType'},
                                    ];
                                    let gx_history = new HDGrid(document.querySelector("#div-gd-history"), columns_history);
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm goods-info-change-btn"><span class="fs-12">품목변경</span></a>
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
@include('partner_with.product.prd01_js')
<!-- script -->

@stop
