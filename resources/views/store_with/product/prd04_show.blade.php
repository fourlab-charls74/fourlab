@extends('store_with.layouts.layout-nav')
@section('title', '옵션별 재고현황')
@section('content')
<div class="py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">옵션별 재고현황</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 코드일련 - {{ @$prd->prd_cd_p }}</span>
            </div>
        </div>
    </div>

    @if(empty($prd))
    <p class="w-100 fs-16 text-center">해당 상품의 정보가 존재하지 않습니다.</p>
    @else
    {{-- 상품정보 --}}
    <div class="show_layout mb-4">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#">상품정보</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="120px"/>
                                <col width="20%"/>
                                <col width="30%"/>
                                <col width="20%"/>
                                <col width="30%"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td rowspan="3" class="img_box brln">
                                    @if (@$prd->img !== null)
                                    <img class="goods_img" src="{{config('shop.image_svr')}}/{{@$prd->img}}" alt="이미지" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;" />
                                    @else
                                    <p class="d-flex align-items-center justify-content-center" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;">이미지 없음</p>
                                    @endif
                                </td>
                                <th>코드일련</th>
                                <td>{{ @$prd->prd_cd_p }}</td>
                                <th>상품번호</th>
                                <td>{{ @$prd->goods_no }}</td>
                            </tr>
                            <tr>
                                <th>스타일넘버</th>
                                <td>{{ @$prd->style_no }}</td>
                                <th>공급처</th>
                                <td>{{ @$prd->com_nm }}</td>
                            </tr>
                            <tr>
                                <th>품목</th>
                                <td>{{ @$prd->opt_kind_nm }}</td>
                                <th>브랜드</th>
                                <td>{{ @$prd->brand_nm }}</td>
                            </tr>
                            <tr>
                                <th>상품명</th>
                                <td colspan="2">{{ @$prd->goods_nm }}</td>
                                <th>상품명(영문)</th>
                                <td>{{ @$prd->goods_nm_eng }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script language="javascript">
    let AlignCenter = {"text-align": "center"};
</script>
@stop
