@extends('head_with.layouts.layout-nav')
@section('title','상품이미지 관리')
@section('content')

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품이미지 관리 - {{$goods->goods_nm}}</h3>
        </div>
    </div>
    <form action="">
        <input type="hidden" id="goods_no" value="{{$goods_no}}">
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">이미지 등록</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>유형</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="img_type" id="base" value="a" class="custom-control-input" @if($img_ta == "a") checked @endif>
                                                            <label class="custom-control-label" for="base">기본 이미지</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="img_type" id="side" value="f" class="custom-control-input" @if($img_ta != "a") checked @endif>
                                                            <label class="custom-control-label" for="side">추가 이미지</label>
                                                        </div>
														<div class="form-inline-inner input_box" style="width:100px;">
															<select name="img_type_alias" class="form-control form-control-sm">
																<option value="">선택</option>
															@foreach($img_type_alias as $img_add_type)
																<option value="{{$img_add_type}}" @if($img_ta == $img_add_type) selected @endif>{{$img_add_type}}</option>
															@endforeach
															</select>
														</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>파일</th>
                                                <td>
                                                    <div class="img_file_cum_wrap">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="file" aria-describedby="inputGroupFileAddon03" accept=".jpg">
                                                            <label id="file-label" class="custom-file-label" for="file">
                                                            <i class="bx bx-images font-size-16 align-middle mr-1"></i>이미지 찾아보기</label>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button class="btn btn-outline-secondary" type="button" id="apply">적용</button>
                                                        </div>
                                                            <p class="mb-0 cum_stxt mt-1">(기본이미지 : 최소 700*700 사이즈의 이미지를 업로드 해 주십시오.)</p>
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
            <div class="card shadow">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#img-setting-tab" role="tab" aria-controls="img-setting-tab" aria-selected="true">크기</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#effect" role="tab" aria-controls="effect" aria-selected="false">유형</a>
                        </li>
                    </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="img-setting-tab" role="tabpanel">
                        <div class="card-body">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-box-ty2 mobile">
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                <tbody>
                                                <tr>
                                                    <th>시스템</th>
                                                    <td colspan="12">
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-50" value="50" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-50" class="custom-control-label">50 * 50</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-62" value="62" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-62" class="custom-control-label">62 * 62</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-70" value="70" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-70" class="custom-control-label">70 * 70</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-90" value="90" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-90" class="custom-control-label">90 * 90</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-100" value="100" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-100" class="custom-control-label">100 * 100</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-129" value="129" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-129" class="custom-control-label">129 * 129</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>기본</th>
                                                    <td colspan="12">
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-55" value="55" class="custom-control-input"/>
                                                                <label for="size-55" class="custom-control-label">55 * 55</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-120" value="120" class="custom-control-input"/>
                                                                <label for="size-120" class="custom-control-label">120 * 120</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-160" value="160" class="custom-control-input"/>
                                                                <label for="size-160" class="custom-control-label">160 * 160</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-180" value="180" class="custom-control-input"/>
                                                                <label for="size-180" class="custom-control-label">180 * 180</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-270" value="270" class="custom-control-input"/>
                                                                <label for="size-270" class="custom-control-label">270 * 270</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-280" value="280" class="custom-control-input"/>
                                                                <label for="size-280" class="custom-control-label">280 * 280</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-320" value="320" class="custom-control-input"/>
                                                                <label for="size-320" class="custom-control-label">320 * 320</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-500" value="500" class="custom-control-input"/>
                                                                <label for="size-500" class="custom-control-label">500 * 500</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-600" value="600" class="custom-control-input"/>
                                                                <label for="size-600" class="custom-control-label">600 * 600</label>
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
                        </div>
                    </div>
                    <div class="tab-pane fade" id="effect" role="tabpanel" aria-labelledby="effect-tab">
                        <div class="card-body">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-box-ty2 mobile">
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <th>Amount</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="amount" id="amount" value="50" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 500</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Radius</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="radius" id="radius" value="0.5" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 50</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Threshold</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="threshold" id="threshold" value="0" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 255</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Quality</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="quality" id="quality" value="95" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 255</div>
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
                </div>
            </div>
            <div class="card shadow mb-4">
                <div>
                    <ul class="nav nav-tabs" id="uploadTab">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#prd-list" role="tab"
                            aria-controls="prd-list" aria-selected="true" id="prd-list-tab">상품목록</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#detail" role="tab"
                            aria-controls="detail" aria-selected="false">상품상세</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#upload-tab" role="tab"
                            aria-controls="upload" aria-selected="false">업로드 미리보기</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="nav-tabContent">
                        <div class="tab-pane show active" id="prd-list" role="tabpanel" aria-labelledby="prd-list-tab">
                            <div class="prd-image-list row" style="margin:0px 5px;border-bottom:1px solid #dddddd;text-align:center;">
                                @foreach($img_urls as $img_url)
                                    <figure class="p-2" style="width:12%;">
                                        <img src="{{$img_url['img']}}" onClick="location.href='/head/product/prd02/{{$goods_no}}/image?img_ta={{$img_url['kind']}}';" alt="" style="width:100%;border:1px solid #dfdfdf;cursor:pointer;">
                                        <figcaption style="@if($img_ta == $img_url['kind']) font-weight:bold; @endif">{{$img_url['title']}}</figcaption>
                                    </figure>
                                @endforeach
                            </div>
                            <div class="prd-image-list row" style="padding:30px 10px 0px 10px;">
                                @foreach($files as $file)
                                    <figure class="p-2">
                                        <figcaption style="text-align:center;">{{$file['size']}} ({{$file['filesize']}}KB)</figcaption>
                                        <img src="{{$file['src']}}" alt="">
                                    </figure>
                                @endforeach
                            </div>
                        </div>
                        <div class="tab-pane" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                            @php echo $goods->goods_cont @endphp
                            {{-- $goods->goods_cont --}}
                        </div>
                        <div class="tab-pane" id="upload-tab" role="tabpanel" aria-labelledby="upload-tab">
                            <ul>
                                <li>신규 등록 및 수정 시 업로드 하신 상품 목록 이미지를 미리 보여 줍니다.</li>
                                <li>자세한 사항은 Help를 참조해 주십시오.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-title">
                    <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
                </div>
                <ul>
                    <li><span>"<i class="bx bx-images font-size-14 align-middle mr-0"></i> 이미지 찾아보기"</span>
                    를 클릭하여 업로드 하실 <span>"이미지 파일"</span>을 선택합니다.</li>
                    <li>'이미지 업로드' 버튼을 클릭한 후, '이미지 미리보기' 탭의 이미지가 정확한지 확인합니다.</li>
                    <li>이미지가 정확히 등록되었다면 '이미지 적용' 버튼을 클릭하여 미리보기의 상품 이미지를 해당 상품의 이미지로 적용합니다.</li>
                    <li>'등록된 상품 이미지'가 보이지 않는다면, 아직 해당 상품의 이미지가 등록되지 않은 것입니다.</li>
                </ul>
            </div>
        </div>
    </form>
</div>

<style>
.custom-file-label::after {
            display: none;
            }
</style>
<!-- script -->
@include('head_with.product.prd02_js')
<!-- script -->
@stop
