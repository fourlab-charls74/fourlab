@extends('head_with.layouts.layout-nav')
@section('title','기획전 상세')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">기획전</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 코드 - {{ $code }}</span>
                </div>
            </div>
        </div>
        <!-- FAQ 세부 정보 -->
        <div class="card_wrap aco_card_wrap">
            <form name="detail">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <a href="#">상세</a>
                        </div>
                        <div class="fr_box">
                            <button type="button" class="btn-sm btn btn-primary sms-send-btn fs-12" onclick="return Save();">저장</button>
                            @if ($code !== '')
                            <button type="button" class="btn-sm btn btn-primary sms-list-btn fs-12" onclick="return Delete();">삭제</button>
                            <button type="button" class="btn-sm btn btn-primary sms-send-btn fs-12" onclick="return View();">미리보기</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body mt-1">
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
                                            <th>유형</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    @foreach ($plan_types as $plan_type)
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="plan_type" id="plan_type_{{ @$plan_type->code_id }}" value="{{ @$plan_type->code_id }}" class="custom-control-input" {{ (@$plan->plan_type == @$plan_type->code_id) ? "checked" : "" }} />
                                                            <label class="custom-control-label" for="plan_type_{{ @$plan_type->code_id }}">{{ @$plan_type->code_val }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>제목</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type='text' class="form-control form-control-sm search-enter" name='subject' id="subject" value='{{@$plan->title}}'>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>구분</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    @foreach ($plan_kinds as $plan_kind)
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="plan_kind" id="plan_kind_{{ @$plan_kind->code_id }}" value="{{ @$plan_kind->code_id }}" class="custom-control-input" {{ (@$plan->plan_kind == $plan_kind->code_id) ? "checked" : "" }} />
                                                            <label class="custom-control-label" for="plan_kind_{{ @$plan_kind->code_id }}">{{ @$plan_kind->code_val }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>사용여부</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    @foreach ($is_shows as $is_show)
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_show" value="{{ @$is_show->code_id }}" id="is_show_{{ @$is_show->code_id }}" class="custom-control-input" {{ (@$plan->is_show == $is_show->code_id or ( $code == '' and $is_show->code_id == '1') ) ? "checked" : "" }} />
                                                            <label class="custom-control-label" for="is_show_{{ @$is_show->code_id }}">{{ @$is_show->code_val }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>노출</th>
                                            <td>
                                                <div class="form-inline form-radio-box flax_box txt_box">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" value="1" name="plan_show" id="plan_show" {{ (@$plan->plan_show=="1") ? "checked" : "" }}>
                                                        <label class="custom-control-label" for="plan_show">쇼핑몰 메인</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" value="1" name="promotion1" id="promotion1" {{ (@$plan->promotion1=="1") ? "checked" : "" }}>
                                                        <label class="custom-control-label" for="promotion1">쇼핑기획전</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" value="1" name="promotion2" id="promotion2" {{ (@$plan->promotion2=="1") ? "checked" : "" }}>
                                                        <label class="custom-control-label" for="promotion2">기획전</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>노출 기간</th>
                                            <td>
                                                <div class="form-inline form-box">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" value="Y" name="plan_date_yn" id="plan_date_yn" {{ (@$plan->plan_date_yn == "Y") ? "checked" : "" }}>
                                                        <label class="custom-control-label" for="plan_date_yn"></label>
                                                    </div>
                                                    <div class="docs-datepicker form-inline-inner" id="start_date">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="start_date" autocomplete="off" disable>
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger start_date_btn p-0 pl-2 pr-2" disable>
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                    <div class="form-inline-inner ml-2">
                                                        <select name="start_time" class="form-control form-control-sm pr-4 select-time"></select>
                                                        시
                                                    </div>
                                                    <span class="text_line">~</span>
                                                    <div class="docs-datepicker form-inline-inner" id="end_date">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="end_date" autocomplete="off" disable>
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger end_date_btn p-0 pl-2 pr-2" disable>
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                    <div class="form-inline-inner ml-2">
                                                        <select name="end_time" class="form-control form-control-sm pr-4 select-time"></select>
                                                        시
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>이미지</th>
                                            <td>
                                                <div class="form-inline form-box">
                                                    <div class="flax_box w-25">
                                                        <input type="hidden" id="plan_img_url" name="plan_img_url">
                                                        <ul style="padding:0; list-style:none; margin:0; list-style-type:none;">
                                                            <li>
                                                                <div style="width:157px; height:100px; border:1px solid #b3b3b3;padding:5px">
                                                                    <label id="plan_img_file-label" for="plan_img_file" class="h-100">
                                                                        @if (@$plan->plan_img != '')
                                                                            <img src="{{@$plan->plan_img}}" id="plan_img" alt="" style="height:100%">
                                                                        @else
                                                                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQYV2NgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII=" id="plan_img" alt="" style="height:100%" title="이미지를 추가해 주세요." >

                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                <input type="file" id="plan_img_file" name="plan_img_file">
                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                배너 ( 157*102px )
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="flax_box w-25">
                                                        <input type="hidden" id="plan_preview_img_url" name="plan_preview_img_url">
                                                        <ul style="padding:0; list-style:none; margin:0; list-style-type:none;">
                                                            <li>
                                                                <div style="width:157px; height:100px; border:1px solid #b3b3b3; padding:5px">
                                                                    <label id="plan_preview_img_file-label" for="plan_preview_img_file" class="h-100">
                                                                        @if (@$plan->plan_preview_img != '')
                                                                            <img src="{{@$plan->plan_preview_img}}" id="plan_preview_img" alt="" style="height:100%">
                                                                        @else
                                                                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQYV2NgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII=" id="plan_preview_img" alt="" style="height:100%" title="이미지를 추가해 주세요." >
                                                                        @endif
                                                                    </label>
                                                                </div>

                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                <input type="file" id="plan_preview_img_file" name="plan_preview_img_file">
                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                미리보기 ( 157*102px )
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="flax_box w-50">
                                                        <input type="hidden" id="plan_top_img_url" name="plan_top_img_url">
                                                        <ul style="padding:0; list-style:none; margin:0; list-style-type:none;">
                                                            <li>
                                                                <div style="width:300px; height:100px; border:1px solid #b3b3b3; padding:5px">
                                                                    <label id="plan_top_img_file-label" for="plan_top_img_file" class="h-100">
                                                                        @if (@$plan->plan_top_img != '')
                                                                            <img src="{{@$plan->plan_top_img}}" id="plan_top_img" alt="" style="height:100%">
                                                                        @else
                                                                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQYV2NgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII=" id="plan_top_img" alt="" style="height:100%" title="이미지를 추가해 주세요." >
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                <input type="file" id="plan_top_img_file" name="plan_top_img_file">
                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                상단 ( 940*∞px )
                                                            </li>
                                                        </ul>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>상단이미지맵</th>
                                            <td>
                                                <div class="form-inline form-box">
                                                    <textarea name="map" class="form-control w-100">{{ @$plan->map }}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>상품상세 출력</th>
                                            <td>
                                                <?php $checked = @$plan->disp_prd_yn == "Y" ? "checked" : "" ?>
                                                <input type="checkbox" name="disp_prd_yn" value="Y" <?=$checked?>/> 상품상세 출력
                                                <div>
                                                    <div class="mt-1">
                                                        - PC
                                                        <div class="mt-1 mb-2">
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertLink(document.detail.disp_prd_pc,'{{ @$plan->p_no }}','{{ @$plan->no }}');">링크 삽입</button>
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertImage(document.detail.disp_prd_pc,'{{ @$plan->plan_top_img }}');">상단(PC)이미지 삽입</button>
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertImage(document.detail.disp_prd_pc,'{{ @$plan->plan_img }}');">배너 이미지 삽입</button>
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertImage(document.detail.disp_prd_pc,'{{ @$plan->plan_preview_img }}');">미리보기 이미지 삽입</button>
                                                        </div>
                                                        <textarea class="form-control" id="disp_prd_pc" name="disp_prd_pc" style="width: 100%; height: 80px;">{{ @$plan->disp_prd_pc }}</textarea>
                                                    </div>
                                                    <div>
                                                        - 모바일
                                                        <div class="mt-1 mb-2">
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertLink(document.detail.disp_prd_mobile,'{{ @$plan->p_no }}','{{ @$plan->no }}');">링크 삽입</button>
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertImage(document.detail.disp_prd_mobile,'{{ @$plan->plan_top_img }}');">상단(모바일)이미지 삽입</button>
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertImage(document.detail.disp_prd_mobile,'{{ @$plan->plan_img }}');">배너 이미지 삽입</button>
                                                            <button class="btn btn-sm btn-primary shadow-sm" onclick="return insertImage(document.detail.disp_prd_mobile,'{{ @$plan->plan_preview_img }}');">미리보기 이미지 삽입</button>
                                                        </div>
                                                        <textarea class="form-control" id="disp_prd_mobile" name="disp_prd_mobile" style="width: 100%; height: 80px;">{{ @$plan->disp_prd_mobile }}</textarea>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @if ($code !== '')
                                        <tr>
                                            <th>하위 폴더 사용여부</th>
                                            <td>
                                                <div class="form-inline form-box">
                                                    <input type='hidden' name='p_no' id='p_no' value='{{ @$plan->p_no }}'>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" value="1" name="folder_cnt" id="folder_cnt" {{ (@$plan->folder_cnt >= 1) ? "checked" : "" }}>
                                                        <label class="custom-control-label" for="folder_cnt">하위카테고리</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            @if ($code !== '')
                <div class="card mt-2 mb-0" id="folder_category" style="display:{{ (@$plan->folder_cnt >= 1) ? "" : "none" }}">
                    <div class="card-header mb-0">
                        <a href="#">하위카테고리</a>
                    </div>
                    <div class="card-body pt-2">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box px-0 mx-0">
                                    <h6 class="m-0 font-weight-bold">총 : <span id="gd_folder-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return FolderAdd();"><span class="fs-12">추가</span></a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return FolderSave();"><span class="fs-12">저장</span></a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return FolderDel();"><span class="fs-12">삭제</span></a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return FolderChangeSeq();"><span class="fs-12">순서변경</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd_folder" style="height:200px" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header mb-0">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-goods" data-toggle="tab" href="#tab-goods-body" role="tab" aria-controls="goods" aria-selected="true">상품</button>
                            </li>
                            <li class="nav-item" role="presentation" id="tab_category" style="display:none">
                                <button class="nav-link" id="tab-category" data-toggle="tab" href="#tab-category-body" role="tab" aria-controls="category" aria-selected="true">카테고리</button>
                            </li>
                        </ul>
                    </div>
                    <div id="tab-goods-body"  class="card-body brtn mt-0 pt-2 tab-pane active" role="tabpanel" aria-labelledby="goods-tab">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>

                                <div class="fr_box">
                                    <div class="custom-control custom-checkbox form-check-box mr-2" style="display:inline-block;">
                                        <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input" checked>
                                        <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                                    </div>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return AddGoods();"><span class="fs-12">상품추가</span></a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return DelGoods();"><span class="fs-12">상품삭제</span></a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="return ChangeGoodsSeq();"><span class="fs-12">순서변경</span></a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" style="height:500px; width:100%;" class="ag-theme-balham"></div>
                        </div>
                    </div>
                    <div id="tab-category-body" class="card-body brtn mt-0 pt-2 tab-pane" role="tabpanel" aria-labelledby="category-tab" >
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <form name="category">
                                            <input type='hidden' name='d_cat_cd' id='d_cat_cd' value='{{ @$category->d_cat_cd }}' />
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="150px">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>템플릿</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="tpl_kind" id="tpl_kind" class="form-control form-control-sm">
                                                            <option value='A' @if(@$category->tpl_kind == 'A') selected @endif>A 타임 (이미지 사이즈 140X140,한줄 5개)</option>
                                                            <option value='B' @if(@$category->tpl_kind == 'B') selected @endif>B 타임 (이미지 사이즈 180X180,한줄 4개)</option>
                                                            <option value='C' @if(@$category->tpl_kind == 'C') selected @endif>C 타임 (이미지 사이즈 250X250,한줄 3개)</option>
                                                            <option value='D' @if(@$category->tpl_kind == 'D') selected @endif>D 타임 (이미지 사이즈 400X400,한줄 2개)</option>
                                                            <option value='E' @if(@$category->tpl_kind == 'E') selected @endif>E 타임 (이미지 사이즈 400X400,한줄 2개,최근상품평)</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>HTML</th>
                                                <td>
                                                    <div class="form-inline form-box">
                                                        <textarea name="header_html" id="header_html" class="form-control w-100">{{ @$category->header_html }}</textarea>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>세일</th>
                                                <td>
                                                    <div class="form-inline form-box">
                                                        <div class="input_box mr-1">
                                                            <input type='text' class="form-control form-control-sm" name='sale_amt' id='sale_amt' value='{{@$category->sale_amt}}'>
                                                        </div>
                                                        <div class="input_box">
                                                            <select name="sale_kind" id="sale_kind" class="form-control form-control-sm wd100">
                                                                <option value="P" @if(@$category->sale_kind == "P") selected @endif>%</option>
                                                                <option value="W" @if(@$category->sale_kind == "W") selected @endif>원</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 p-3 text-center">
                                    <button class="btn-sm btn btn-primary sms-send-btn fs-12" onclick="return CategorySave();">저장</button>
                                    <button class="btn-sm btn btn-primary sms-send-btn fs-12" onclick="return CategoryReset();">취소</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="card mt-2">
                <div class="row_wrap card-body" style="border: none;">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="150px">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>관련전시카테고리</th>
                                            <td>
                                                <div class="flex_box">
                                                    @foreach ($d_category as $category)
                                                        @if ($loop->first)
                                                            <div class="w-100">
                                                                {{$category}}
                                                            </div>
                                                        @else
                                                            <div class="w-100 mt-2">
                                                                {{$category}}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="flex_box w-100">
                        <button type="button" style="margin: 0 auto;" class="mt-2 btn-sm btn btn-primary sms-send-btn fs-12" onclick="return Save();">저장</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* 기획전 상품 이미지 사이즈 픽스 */
        .img{
            height:30px;
        }
    </style>
    <script>
        const folder_columns = [
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null, cellStyle: {"background":"#F5F7F7"}},
            {field: "d_cat_cd", headerName: "코드",width:100},
            {field: "d_cat_nm", headerName: "폴더명",width:200,rowDrag: true, editable: true, cellStyle: {'background' : '#ffff99'},
                // editable: function(params){ return (params.data !== undefined && params.data.editable === 'Y')? true:false; },
                cellClass:function(params){ return (params.data !== undefined && params.data.editable == 'Y')? ['hd-grid-edit']: [];},
                cellRenderer: function (params) {
                    // if (params.data !== undefined && params.data.editable !== 'Y') {
                    //     return `<a href="javascript:void(0);" onclick="return SearchCategoryGoods('${params.data.d_cat_cd}');">${params.value}</a>`;
                    // } else {
                    //     return params.value;
                    // }
                    addEventListener('click', function(event) {
                        params.node.data.editable = 'Y';
                    });
                    return `<a href="javascript:void(0);" onclick="return SearchCategoryGoods('${params.data.d_cat_cd}');">${params.value}</a>`;
                }
            },
            {field: "use_yn", headerName: "사용여부",width:72, editable: true, cellStyle: {'background' : '#ffff99'},
                cellEditorSelector: function(params) {
                    addEventListener('click', function(event) {
                        params.node.data.editable = 'Y';
                    });
                    return {
                        component: 'agRichSelectCellEditor',
                        params: { 
                            values: ['Y', 'N']
                        },
                    };
                },
            },
            {field: "tpl_kind", headerName: "템플릿",width:60},
            {field: "sale_yn", headerName: "세일",width:60},
            {field: "reg_dm", headerName: "헤더",width:60},
            {field: "goods_cnt", headerName: "상품수", width:60,type:'numberType'},
            {field: "editable",hide:true}
        ];

        const columns = [
            {headerName: '#', width:35, pinned: 'left', type:'NumType', cellStyle: {"background":"#F5F7F7"}},
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left', sort: null},
            {field: "goods_no", headerName: "상품번호", width: 82, pinned: 'left',rowDrag: true},
            {field: "head_desc", headerName: "상단홍보글"},
            {field: "img", headerName: "이미지", width:46, cellStyle: {"text-align":"center"},
                cellRenderer: function(params) {
                    return '<a href="https://{{ @$domain }}/app/product/detail/'+ params.data.goods_no +'/0" target="_blank"><img src="' + params.data.img + '" class="img" alt="" onerror="this.src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'"/></a>'
                }
            },
            {field: "img", headerName: "이미지_url", hide: true},
            {field: "goods_nm", headerName: "상품명",type:'HeadGoodsNameType'},
            {field: "com_nm", headerName: "공급업체", width: 80},
            {field: "com_id", headerName: "com_id", hide:true},
            {field: "ad_desc", headerName: "하단홍보글"},
            {field: "sale_stat_cl", headerName: "상품상태", width:70, type:'GoodsStateType'},
            {field: "before_sale_price", headerName: "정상가", type:'currencyType', hide: true},
            {field: "price", headerName: "판매가", width:60, type:'currencyType'},
            {field: "coupon_price", headerName: "쿠폰가", width:60, type:'currencyType'},
            {field: "sale_rate", headerName: "세일율(,%)", type:'percentType', hide: true},
            {field: "sale_s_dt", headerName: "세일기간", hide: true},
            {field: "sale_e_dt", headerName: "세일기간", hide: true},
            {field: "qty", headerName: "재고수", width:46, type:'numberType'},
            {field: "wqty", headerName: "보유재고수", width:70, type:'numberType'},
            {field: "reg_dm", headerName: "등록일시", width:110},
            {field: "sale_price", headerName: "sale_price", hide: true},
            {field: "goods_type_cd", headerName: "goods_type", hide: true},
        ];
    </script>
    <style>
        div.tab-pane {
            display: none;
        }
        div.active {
            display: block;
        }
    </style>
    <script type="text/javascript" charset="utf-8">

        let code = '{{ $code  }}';

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        
        let gx;
        let gxFolder;

        $(document).ready(function() {
            //pApp.ResizeGrid(650);
            //pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            if(gridDiv !== null){
                gx = new HDGrid(gridDiv, columns);
                gx.gridOptions.rowDragManaged = true;
                gx.gridOptions.animateRows = true;

				gx.gridOptions.enableMultiRowDragging	= true;
				gx.gridOptions.rowSelection	= 'multiple';

                SearchCategoryGoods();

                 // 이미지 출력 설정
                $("#chk_to_class").click(function() {
                    gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
                });
            }

            const gridFolderDiv = document.querySelector("#div-gd_folder");

            if(gridFolderDiv !== null) {
                gxFolder = new HDGrid(gridFolderDiv, folder_columns);
                gxFolder.gridOptions.rowDragManaged = true;
                gxFolder.gridOptions.animateRows = true;

                if ($('#folder_cnt').is(":checked") === true) {
                    FolderSearch();
                }

                $("#folder_cnt").click(function () {
                    $("#folder_category").toggle();
                    $("#tab_category").toggle();

                });
            }

            setUseDisplayDate('{{ @$plan->plan_date_yn }}' == 'Y');
            setDisplayDate('{{ @$plan->start_date }}', '{{ @$plan->end_date }}'); // 노출기간설정
        });

        const insertImage = (obj, url) => {
            event.preventDefault();
            obj.value = obj.value + "<img src='" + url + "'/>";
        };

        const insertLink = (obj, p_no, no) => {
            event.preventDefault();
            var url = "/app/planning/views/" + p_no +  "/" + no;
            obj.value = obj.value + "<a href='" + url + "'></a>";
        };

        function is_checked() {
            const checkbox = document.getElementById('folder_cnt');
        }

        

    </script>

    <!-- script -->
    @include('head_with.product.prd12_js')
    <!-- script -->
@stop
