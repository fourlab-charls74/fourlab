@extends('store_with.layouts.layout')
@section('title','상품별이익현황')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">상품별이익현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 경영관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="good_types">판매기간</label>
                            <div class="form-inline date-select-inbox">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="">매장구분</label>
							<div class="flex_box">
								<select name='store_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_types as $store_type)
										<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
				</div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='prd_cd' value=''>
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
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="goods_stat">상품상태</label>
                            <div class="flax_box">
                                <select id="goods_stat" name="goods_stat[]" class="form-control form-control-sm multi_select w-100" multiple>
                                    <option value=''>전체</option>
                                    @foreach ($goods_stats as $goods_stat)
                                        <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="goods_nm_eng">상품명(영문)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-25 pr-1">
                                    <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        @foreach ($com_types as $com_type)
                                            <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box w-75">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_cd" name="com_cd" />
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company search-all search-enter" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="item">상품구분</label>
                            <div class="flex_box">
                                <select name='type' id="type" class="form-control form-control-sm" style="width: 47%">
                                    <option value=''>전체</option>
                                    <option value='N'>일반</option>
                                    <option value='D'>납품</option>
                                    <option value='E'>기획</option>
                                </select>
                                <span class="text_line" style="width: 6%; text-align: center;">/</span>
                                <select name='goods_type' id="goods_type" class="form-control form-control-sm" style="width: 47%">
                                    <option value=''>전체</option>
                                    <option value='S'>매입</option>
                                    <option value='I'>위탁매입</option>
                                    <option value='P'>위탁판매</option>
                                    <option value='O'>구매대행</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="goods_no">상품번호</option>
                                        <option value="prd_cd">상품코드</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="initSearch()">
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	var columns = [
		{headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: {"line-height": "40px"}},
		{
            field: "goods_no",
            headerName: "상품번호",
            width: 58,
            pinned: 'left',
            cellStyle: {"line-height": "40px"},
            cellRenderer: function (params) {
                if (params.value) {
                    return `<a href="{{config('shop.front_url')}}/app/product/detail/${params.value}" target="_blank">${params.value}</a>`
                }
            }
        },
		{field: "goods_type_nm", headerName: "상품구분", width: 58, type: 'StyleGoodsTypeNM'},
		{field: "prd_cd", headerName: "상품코드", cellStyle: {"line-height": "40px", 'text-align': 'center'}},
		{field: "brand_nm", headerName: "브랜드", cellStyle: {"line-height": "40px"}},
        {field: "style_no", headerName: "스타일넘버", width: 120, cellStyle: {"line-height": "40px", 'text-align': 'center'}},
		{field: "sale_stat_cl_val", headerName: "상품상태", width:70, type: 'GoodsStateTypeLH50'},
		{field: "img", headerName: "이미지", type: 'GoodsImageType', width:60, cellStyle: {"line-height": "40px"}, surl:"{{config('shop.front_url')}}"},
        {field: "img", headerName: "이미지_url", hide: true},
		{field: "goods_nm", headerName: "상품명", type: 'HeadGoodsNameType', cellStyle: {"line-height": "40px"}},
		{field: "goods_nm_eng", headerName: "상품명(영문)", width: 230, cellStyle: {"line-height": "40px"}},
		{field: "goods_opt", headerName: "옵션", cellStyle: {"line-height": "40px"}, width: 120},
        // {field: "",	headerName: "TAG가"},
        {field: "s",	headerName: "판매현황",
            children: [
                {headerName: "수량", field: "qty", type: 'currencyMinusColorType'},
                {headerName: "단가", field: "wonga", type: 'numberType'},
                {headerName: "매출액", field: "amt", type: 'currencyMinusColorType'},
                {headerName: "할인", field: "discount", type: 'currencyMinusColorType'},
                {headerName: "결제금액", field: "recv_amt", type: 'currencyMinusColorType'}, // 판매금액 + 포인트 합친게 결제(주문) 금액.
                {headerName: "매장수수료", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType'}, // 0 처리
                {headerName: "중간관리수수료", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType'}, // 0 처리
            ]
        },
        {field: "sum_wonga", headerName: "원가", type: 'currencyMinusColorType'},
        {field: "sales_profit",	headerName: "매출이익", type: 'currencyMinusColorType'}, // 매출이익 = 결제금액 - 원가 합계금액
        {field: "profit_rate",	headerName: "이익율(%)", type:'percentType'}, // 매출이익 분의 매출액 = 이익율
        {headerName: "", field: "nvl", width: "auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();
	});
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal07/search', data, 1);
	}
</script>
@stop
