@extends('shop_with.layouts.layout-nav')
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
               
                <h3 class="d-inline-flex">상품</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 상품 - {{ $goods_no }}</span>
                </div>
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
                                                    <div>
                                                        대표 카테고리
                                                    </div>
                                                    <div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
                                                        <!-- <button type="button" class="btn btn-sm btn-primary btn-rep-add"  data-toggle="tooltip" data-placement="top" title="" data-original-title="선택" data-toggle="modal" data-target="#category_list_modal">
                                                            <i class="bx bx-search-alt-2"></i>
                                                        </button> -->
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
                                                        <!-- <button type="button" class="btn btn-sm btn-primary btn-display-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가"  data-toggle="modal" data-target="#category_list_modal">
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
                                                        </button> -->
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
                <div class="card">
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
                                        <!-- <button type="button" id="img-setting" class="btn btn-primary waves-effect waves-light" data-no="{{$goods_no}}">
                                            <i class="bx bx-cog font-size-14 align-middle"></i> 이미지 관리
                                        </button> -->
                                        <button type="button" id="img-show" class="btn btn-success waves-effect waves-light" data-no="{{$goods_no}}" onClick="openSitePop('{{ $goods_no }}');return false;">
                                            <i class="bx bx-images font-size-14 align-middle"></i> 상품 보기
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-4 mt-lg-4">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tr>
                                            <th  id="th">상품명</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->goods_nm  }}
                                                </div>
                                            </td>
                                            <th id="th" >상품명(영문)</th>
                                            <td>
                                                <div class="input_box" style="width:302px;">
                                                   {{ @$goods_info->goods_nm_eng  }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th id="th">상단 홍보글</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->head_desc }}
                                                </div>
                                            </td>
                                            <th >하단 홍보글</th>
                                            <td >
                                                <div class="input_box">
                                                   {{ @$goods_info->ad_desc }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th >품목</th>
                                            <td>
                                                <div class="input_box">
                                                        @foreach($opt_cd_list as $opt_cd)
                                                            {{ (@$goods_info->opt_kind_cd == $opt_cd->cd) ? $opt_cd->val : "" }}
                                                        @endforeach
                                                </div>
                                            </td>
                                            <th >브랜드</th>
                                            <td>
                                                <div class="input_box">
                                                    <div class="form-inline inline_btn_box">
                                                        {{@$goods_info->brand_nm}}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th >스타일넘버</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->style_no  }}
                                                </div>
                                            </td>
                                            <th >업체</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->com_nm }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>제조사</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->make  }}
                                                </div>
                                            </td>
                                            <th >원산지</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->org_nm  }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>메모</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->goods_memo  }}
                                                </div>
                                            </td>
                                            <th >판매가</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @number_format(@$goods_info->price) }}원
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tag가</th>
                                            <td>
                                                <div class="input_box">
                                                    {{@number_format(@$goods_info->goods_sh)}}원
                                                </div>
                                            </td>
                                            <th >과세구분</th>
                                            <td>
                                                <div class="input_box">
                                                        {{ (@$goods_info->tax_yn== 'Y') ? "과세" : "비과세" }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>등록일시</th>
                                            <td>
                                                <div class="input_box">
                                                    {{ @$goods_info->reg_dm }}
                                                </div>
                                            </td>
                                            <th>수정일시</th>
                                            <td>
                                                <div class="input_box">
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
                                                    <textarea name="goods_cont" id="goods_cont" class="form-control editor1" readonly>{{ @$goods_info->goods_cont }}</textarea>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript" charset="utf-8">
        const goods_no = '{{$goods_no}}';
        const goods_sub = '{{@$goods_info->goods_sub}}';

		var ed;

        $(document).ready(function(){
            $('#main-tab').trigger("click");
            $('#goods_cont').summernote('disable');
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


		//ESC 클릭시 창 닫기
		$(document).keydown(function(e){
			// ESCAPE key pressed
			if (e.keyCode == 27) {
				window.close();
			}
		});

    </script>

	<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
    <link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >
@stop
