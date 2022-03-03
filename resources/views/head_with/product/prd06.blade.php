@extends('head_with.layouts.layout')
@section('title','원사이즈 및 클리어런스')
@section('content')
<div class="page_tit">
<<<<<<< HEAD
    <h3 class="d-inline-flex">원사이즈 및 클리어런스</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 원사이즈 및 클리어런스</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품구분</label>
                            <div class="flax_box">
                                <select name='goods_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($goods_types as $goods_type)
                                    <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품상태</label>
                            <div class="flax_box">
                                <select name='goods_stat' class="form-control form-control-sm">
                                <option value=''>전체</option>
                                <?php
                                    collect($goods_stats)->map(function($goods_stat) {
                                        $selected = "";
                                        if ($goods_stat->code_id == 40) $selected = 'selected';
                                        echo "<option value='" . $goods_stat->code_id . "' ${selected}>" . $goods_stat->code_val .  "</option>";
                                    });
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
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
				</div>
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="head_desc">상단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
=======
	<h3 class="d-inline-flex">원사이즈 및 클리어런스</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 원사이즈 및 클리어런스</span>
	</div>
</div>
<form method="get" name="search" id="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">상품구분</label>
							<div class="flax_box">
								<select name='goods_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_types as $goods_type)
									<option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">상품상태</label>
							<div class="flax_box">
								<select name='goods_stat' class="form-control form-control-sm">
								<option value=''>전체</option>
								<?php
									collect($goods_stats)->map(function($goods_stat) {
										$selected = "";
										if ($goods_stat->code_id == 40) $selected = 'selected';
										echo "<option value='" . $goods_stat->code_id . "' ${selected}>" . $goods_stat->code_val .  "</option>";
									});
								?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">스타일넘버/상품코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
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
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="brand_cd">브랜드</label>
							<div class="form-inline inline_btn_box">
								<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
								<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="head_desc">상단홍보글</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
>>>>>>> main
						<div class="form-group">
							<label for="">상품명</label>
							<div class="flax_box">
								<input type="text" name="goods_nm" class="form-control form-control-sm search-enter">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
<<<<<<< HEAD
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">세일여부/세일구분/타임세일여부</label>
                            <div class="flex_box">
                                <select id="sale_yn" name="sale_yn" class="form-control form-control-sm ml-3 mr-3" style="width:70px;">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N" selected>N</option>
                                </select>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="coupon_yn" id="coupon_yn" class="custom-control-input" value="Y">
                                    <label class="custom-control-label" for="coupon_yn" style="font-weight: 400;">쿠폰여부</label>
                                    <span>　/　</span>
                                </div>
                                <select id="sale_type" name="sale_type" class="form-control form-control-sm" style="width:100px;">
                                    <option value="">선택</option>
                                    <option value="event">event</option>
                                    <option value="onesize">onesize</option>
                                    <option value="clearance">clearance</option>
                                    <option value="refurbished">refurbished</option>
                                    <option value="newmember">newmember</option>
                                </select>
                                <span>　/　</span>
                                <select id="sale_dt_yn" name="sale_dt_yn" class="form-control form-control-sm" style="width:70px;">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N" selected>N</option>
                                </select>
                            </div>
                        </div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">원사이즈/클리어런스</label>
                            <div class="flex_box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="onesize_yn" id="onesize_yn" class="custom-control-input" value="Y" checked>
                                    <label class="custom-control-label" for="onesize_yn" style="font-weight: 400;">원사이즈상품</label>
                                    <span>　/　</span>
                                </div>
                                <select id="onesize_qty" name="onesize_qty" class="form-control form-control-sm" style="width:100px;">
                                    <option value="wqty" selected>창고재고</option>
                                    <option value="good_qty">온라인재고</option>
                                </select>
                                <span>　/　</span>
                                <select id="not_order" name="not_order" class="form-control form-control-sm" style="width:100px;">
                                    <option value=''>선택</option>
                                    <option value="1">1개월</option>
                                    <option value="3">3개월</option>
                                    <option value="12">1년</option>
                                </select>
                                <span class="ml-2">동안 미주문</span>
                            </div>
                        </div>
=======
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">세일여부/구분/타임세일여부</label>
							<div class="flax_box">
								<select id="sale_yn" name="sale_yn" class="form-control form-control-sm ml-3 mr-3" style="width:70px;">
									<option value="">전체</option>
									<option value="Y">Y</option>
									<option value="N" selected>N</option>
								</select>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="coupon_yn" id="coupon_yn" class="custom-control-input" value="Y">
									<label class="custom-control-label" for="coupon_yn" style="font-weight: 400;">쿠폰여부</label>
									<span>　/　</span>
								</div>
								<select id="sale_type" name="sale_type" class="form-control form-control-sm" style="width:100px;">
									<option value="">선택</option>
									<option value="event">event</option>
									<option value="onesize">onesize</option>
									<option value="clearance">clearance</option>
									<option value="refurbished">refurbished</option>
									<option value="newmember">newmember</option>
								</select>
								<span>　/　</span>
								<select id="sale_dt_yn" name="sale_dt_yn" class="form-control form-control-sm" style="width:70px;">
									<option value="">전체</option>
									<option value="Y">Y</option>
									<option value="N" selected>N</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">원사이즈/클리어런스</label>
							<div class="flax_box">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="onesize_yn" id="onesize_yn" class="custom-control-input" value="Y" checked>
									<label class="custom-control-label" for="onesize_yn" style="font-weight: 400;">원사이즈상품</label>
									<span>　/　</span>
								</div>
								<select id="onesize_qty" name="onesize_qty" class="form-control form-control-sm" style="width:100px;">
									<option value="wqty" selected>창고재고</option>
									<option value="good_qty">온라인재고</option>
								</select>
								<span>　/　</span>
								<select id="not_order" name="not_order" class="form-control form-control-sm" style="width:100px;">
									<option value=''>선택</option>
									<option value="1">1개월</option>
									<option value="3">3개월</option>
									<option value="12">1년</option>
								</select>
								<span class="ml-2">동안 미주문</span>
							</div>
						</div>
>>>>>>> main
					</div>
					<div class="col-lg-4 inner-td">
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
										<option value="goods_nm">상품명</option>
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
<<<<<<< HEAD
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
=======
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
>>>>>>> main
	</div>
</form>
<form name="sale">
	@csrf
	<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
		<div class="card-body">
			<div class="card-title mb-3">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
<<<<<<< HEAD
                        <h6 class="m-0 font-weight-bold">, 총 상품수 : <span id="prd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box flex_box">
                        <div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
                            <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
                            <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                        </div>
                        <select name='sale_type' class="form-control form-control-sm mx-2" style="width: 100px;">
                            <option value=''>세일구분</option>
                            <option value="event">event</option>
                            <option value="onesize">onesize</option>
                            <option value="clearance">clearance</option>
                            <option value="refurbished">refurbished</option>
                        </select>
                        <div class="flex_box">
                            세일율&nbsp;:&nbsp;<input class="form-control form-control-sm" type='text' name='sale_rate' value='' onkeydown="onlyNum(this);" style="text-align: center;width:50px;">&nbsp;%&nbsp;
                            <a href="#" onclick="saleApply();" class="btn btn-sm btn-primary shadow-sm mr-1">세일가계산</a>
                            <a href="#" onclick="saleOn();" class="btn btn-sm btn-primary shadow-sm mr-1">세일</a>
                            <a href="#" onclick="saleOff();" class="btn btn-sm btn-primary shadow-sm">세일취소</a>
                        </div>
                    </div>
=======
						<h6 class="m-0 font-weight-bold">, 총 상품수 : <span id="prd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box flax_box">
						<div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
							<input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
							<label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
						</div>
						<select name='sale_type' class="form-control form-control-sm mx-2" style="width: 100px;">
							<option value=''>세일구분</option>
							<option value="event">event</option>
							<option value="onesize">onesize</option>
							<option value="clearance">clearance</option>
							<option value="refurbished">refurbished</option>
						</select>
						<div class="flax_box">
							세일율&nbsp;:&nbsp;<input class="form-control form-control-sm" type='text' name='sale_rate' value='' onkeydown="onlyNum(this);" style="text-align: center;width:50px;">&nbsp;%&nbsp;
							<a href="#" onclick="saleApply();" class="btn btn-sm btn-primary shadow-sm mr-1">세일가계산</a>
							<a href="#" onclick="saleOn();" class="btn btn-sm btn-primary shadow-sm mr-1">세일</a>
							<a href="#" onclick="saleOff();" class="btn btn-sm btn-primary shadow-sm">세일취소</a>
						</div>
					</div>
>>>>>>> main
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</form>
<style> /* 상품 이미지 사이즈 강제 픽스 */ .img { height:30px; } </style>
<script language="javascript">

<<<<<<< HEAD
    const CELL_COLOR = {
        YELLOW: { 'background' : '#ffff99' },
        GREEN: { 'background' : '#C5FF9D' }
    }

    const cellStyleGoodsType = (params) => {
        var state = {
            "위탁판매":"#F90000",
            "매입":"#009999",
            "해외":"#0000FF",
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                return {
                    color: state[params.value],
                    height: '30px',
                    textAlign: 'center'
                }
            }
        }
    };

    const cellStyleGoodsState = (params) => {
        var state = {
            "판매중지":"#808080",
            "등록대기중":"#669900",
            "판매중":"#0000ff",
            "품절[수동]":"#ff0000",
            "품절":"#AAAAAA",
            "휴지통":"#AAAAAA",
            "판매대기중": "black",
            "임시저장": "black"
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                var color = state[params.value];
                return {
                    color: color,
                    textAlign: 'center'
                }
            }
        }
    };

    const colorGrouping = (params) => {
        const is_green = params.data.is_green;
        return is_green ? CELL_COLOR.GREEN : CELL_COLOR.YELLOW;
    };

    const columns = [

        { headerName: '', pinned: 'left', headerCheckboxSelection: true, checkboxSelection: true, width: 50 },
        { field: "index", headerName: "인덱스", hide: true },
        { field: "goods_no", headerName: "상품번호", width: 90, pinned: 'left' },
        { field: "goods_type", headerName: "상품구분", width: 100, cellStyle: (params) => cellStyleGoodsType(params), pinned: 'left' },
        { field: "opt_kind_nm", headerName: "품목", width: 100, pinned: 'left' },
        { field: "brand_nm", headerName: "브랜드", pinned: 'left' },
        { field: "style_no", headerName: "스타일넘버", width: 120, pinned: 'left' },
        { field: "head_desc", headerName: "상단홍보글", width: 130 },
        { field: "img", headerName: "이미지", width:75, type:'GoodsImageType', hide: true },
        { field: "img_url", headerName: "이미지_url", width:75, hide: true },
        { field: "goods_nm", headerName: "상품명", width: 230, type:"HeadGoodsNameType" },
        { field: "sale_stat_cl_val", headerName: "상품상태", cellStyle: (params) => cellStyleGoodsState(params) },
        { field: "goods_opt", headerName: "옵션", cellStyle: (params) => colorGrouping(params) },
        { field: "good_qty", headerName: "온라인재고", cellStyle: {textAlign: 'right'}, cellStyle: (params) => colorGrouping(params) },
        { field: "wqty", headerName: "창고재고", cellStyle: {textAlign: 'right'}, cellStyle: (params) => colorGrouping(params) },
        { field: "restock", headerName: "재입고요청", cellStyle: {textAlign: 'right'} },
        { field: "goods_sh", headerName: "시중가", type: 'currencyType' },
        { field: "normal_price", headerName: "정상가", type: 'currencyType' },
        { field: "price", headerName: "판매가", type: 'currencyType' },
        { field: "sale_type", headerName: "세일구분", cellStyle: CELL_COLOR.YELLOW },
        { field: "sale_yn", headerName: "세일여부", cellStyle: CELL_COLOR.YELLOW },
        { field: "before_sale_price", headerName: "이전세일가", type: 'currencyType' },
        { field: "sale_price", headerName: "세일가", cellStyle: CELL_COLOR.YELLOW, type: 'currencyType' },
        { field: "sale_rate", headerName: "세일율(%)", cellStyle: CELL_COLOR.YELLOW, cellRenderer: (params) => Math.round(params.data.sale_rate) },
        { field: "sale_dt_yn", headerName: "타임세일여부", cellStyle: CELL_COLOR.YELLOW },
        { field: "sale_s_dt", headerName: "세일기간(시작)" },
        { field: "sale_e_dt", headerName: "세일기간(종료)" },
        { field: "coupon_price", headerName: "쿠폰가", type: 'currencyType' },
        { field: "wonga", headerName: "원가", type: 'currencyType' },         
        { field: "margin_amt", headerName: "마진액", type: 'currencyType' },
        { field: "margin_rate", headerName: "마진율(%)", cellRenderer: (params) => Math.round(params.data.margin_rate) },
        { field: "qty", headerName: "재고수" },
        { field: "md_nm", headerName: "MD" },
        { field: "reg_dm", headerName: "등록일자", width: 150 },
        { field: "upd_dm", headerName: "수정일자", width: 150 },
        { field: "goods_type_cd", headerName: "goods_type" }
    ];

    const CELL_DIMENSION_SIZE = 30;
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);

        let options = {
            rowHeight: CELL_DIMENSION_SIZE,
            getRowNodeId: data => data.index // 업데이트 및 제거를 위핸 식별 ID 설정
        };
        
        gx = new HDGrid(gridDiv, columns, options);
        Search();

        $("#chk_to_class").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
        });
    });
    
    const Search = () => {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/product/prd06/search', data, 1, (data) => {
            $('#prd-total').html(data.head.goods_cnt);
        });
    };

    const popSearchBrand = (type, ismt) => {
        if (type == null || typeof(type) == "undefined") type = '';
        if (ismt == null || typeof(ismt) == "undefined") ismt = '';

        var url = "/head/webapps/standard/std24.php?CMD=popup&TYPE=" + type + "&ISMT=" + ismt;
        openWindow(url, "", "resizable=yes,scrollbars=yes", 700, 600);
    }

    const applyToClass = () => {
        if ($("#chk_to_class").is(":checked")) {
            gx.gridOptions.api.selectAll();
        } else {
            gx.gridOptions.api.deselectAll();
        }
    };
    
    $(".sort_toggle_btn label").on("click", function() {
        $(".sort_toggle_btn label").attr("class", "btn btn-secondary");
        $(this).attr("class", "btn btn-primary");
    });

    const URL = {
        SALE_ON: "/head/product/prd06/sale-on",
        SALE_OFF: "/head/product/prd06/sale-off",
    }

    const onlyNum = ( obj, type ) => { // 해당 객체의 숫자 여부 확인, onkeydown 시 사용
        if ( ! type ) type = "ko";
        if (type == "ko") { // 천단위 콤마
            if (event.keyCode == 8) { 			// Back Space
            } else if (event.keyCode == 9) {		// Tab
            } else if (event.keyCode == 46) {		// Delete
            } else if (event.keyCode >= 48 && event.keyCode <= 57) {		// 1 ~ 0
            } else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
            } else {
                event.returnValue = false;
            }
        } else if (type == "kom") { // 천단위 콤마, 마이너스
            if (event.keyCode == 8) { 			// Back Space
            } else if (event.keyCode == 9) {		// Tab
            } else if (event.keyCode == 46) {		// Delete
            } else if (event.keyCode >= 48 && event.keyCode <= 57) {		// 1 ~ 0
            } else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
            } else if (event.keyCode == 45 && obj.value == ""){ 			// 첫 '-' 가능
            } else {
                event.returnValue = false;
            }
        } else if (type == "us") { // 천단위 콤마, 소수점
            if (event.keyCode == 8) { 			// Back Space
            } else if (event.keyCode == 9) {		// Tab
            } else if (event.keyCode == 46) {		// Delete
            } else if (event.keyCode >= 48 && event.keyCode <= 57) {		// 1 ~ 0
            } else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
            } else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
            } else if (event.keyCode == 110  ) {							// Number Pad .
            } else if (event.keyCode == 190) {								// .
            } else {
                event.returnValue = false;
            }		
        } else if (type == "usm") { // 천단위 콤마, 소수점, 마이너스
        }
    };

    const saleApply = () => {
        const [ sale_type, sale_rate ] = [ document.sale.sale_type.value, document.sale.sale_rate.value ];
        const cnt = 0;
        if (sale_rate > 0) {
            const checked_rows = gx.gridOptions.api.getSelectedRows();
            const target_rows = checked_rows.map((row) => {
                let price;
                if (row.sale_yn == "Y") {
                    price = row.normal_price;
                } else {
                    price = row.price;
                }
                const sale_price = price * ( 1 - sale_rate / 100 );
                // 루프를 돌면서 체크된 행들을 업데이트합니다. - 세일가: sale_price, 세일율: sale_rate, 세일구분: sale_type
                gx.gridOptions.api.applyTransaction({update : [ 
                    { ...row, price: price, sale_price: sale_price, sale_type: sale_type, sale_rate: sale_rate }
                ]});
            });
        }
    };

    const saleOn = () => {
        if (confirm("세일을 시작하시겠습니까?")) {
            const checked_rows = gx.gridOptions.api.getSelectedRows();

            let check_on_sale = false;
            checked_rows.map(row => { // 세일여부 검사
                if (row.sale_dt_yn == "N" && row.sale_yn == "N") {
                    check_on_sale = false;
                } else {
                    check_on_sale = true;
                    return false;
                }
            });

            if (check_on_sale) {
                alert("이미 세일중인 상품이 있습니다 \n세일중인 상품을 제외하고 선택해주세요");
                return false;
            }

            const target_rows = checked_rows.filter(row => { // 타임세일여부가 N인 경우에만 세일 적용
                return row.sale_dt_yn == "N" ? true : false
            });
            
            const [ sale_type, sale_rate ] = [ document.sale.sale_type.value, document.sale.sale_rate.value ];
            if (target_rows.length > 0) {
                axios({
                    url: URL.SALE_ON,
                    method: 'post',
                    data: { 
                        data : target_rows, 
                        sale_type: sale_type ? sale_type : "event", 
                        sale_rate: sale_rate ? sale_rate : 0
                    }
                }).then((response) => {
                    if (response.data.code == 1) Search();
                }).catch((error) => {
                    // console.log(error);
                });
            } else {
                alert('상품을 선택 해 주십시오.');
            }
        }
    };

    const saleOff = () => {
        if (confirm("세일을 종료하시겠습니까?")) {
            const checked_rows = gx.gridOptions.api.getSelectedRows();
            const target_rows = checked_rows.filter(row => row.sale_dt_yn == "N" ? true : false );
            if (target_rows.length > 0) {
                axios({
                    url: URL.SALE_OFF,
                    method: 'post',
                    data: { data : target_rows }
                }).then((response) => {
                    if (response.data.code == 1) Search();
                }).catch((error) => {
                    // console.log(error);
                });
            } else {
                alert('상품을 선택 해 주십시오.');
            }
        }
    };
=======
	const CELL_COLOR = {
		YELLOW: { 'background' : '#ffff99' },
		GREEN: { 'background' : '#C5FF9D' }
	}

	const cellStyleGoodsType = (params) => {
		var state = {
			"위탁판매":"#F90000",
			"매입":"#009999",
			"해외":"#0000FF",
		}
		if (params.value !== undefined) {
			if (state[params.value]) {
				return {
					color: state[params.value],
					height: '30px',
					textAlign: 'center'
				}
			}
		}
	};

	const cellStyleGoodsState = (params) => {
		var state = {
			"판매중지":"#808080",
			"등록대기중":"#669900",
			"판매중":"#0000ff",
			"품절[수동]":"#ff0000",
			"품절":"#AAAAAA",
			"휴지통":"#AAAAAA",
			"판매대기중": "black",
			"임시저장": "black"
		}
		if (params.value !== undefined) {
			if (state[params.value]) {
				var color = state[params.value];
				return {
					color: color,
					textAlign: 'center'
				}
			}
		}
	};

	const colorGrouping = (params) => {
		const is_green = params.data.is_green;
		return is_green ? CELL_COLOR.GREEN : CELL_COLOR.YELLOW;
	};

	const columns = [

		{ headerName: '', pinned: 'left', headerCheckboxSelection: true, checkboxSelection: true, width:40, cellStyle: {"background":"#F5F7F7"} },
		{ field: "index", headerName: "인덱스", hide: true },
		{ field: "goods_no", headerName: "상품번호", width: 72, pinned: 'left' },
		{ field: "goods_type", headerName: "상품구분", width: 72, cellStyle: (params) => cellStyleGoodsType(params), pinned: 'left' },
		{ field: "opt_kind_nm", headerName: "품목", width: 72, pinned: 'left' },
		{ field: "brand_nm", headerName: "브랜드", pinned: 'left' },
		{ field: "style_no", headerName: "스타일넘버", width: 100, pinned: 'left' },
		{ field: "head_desc", headerName: "상단홍보글", width: 120 },
		{ field: "img", headerName: "이미지", width:60, type:'GoodsImageType', hide: true },
		{ field: "img_url", headerName: "이미지_url", width:75, hide: true },
		{ field: "goods_nm", headerName: "상품명", width: 230, type:"HeadGoodsNameType" },
		{ field: "sale_stat_cl_val", headerName: "상품상태", width:72, cellStyle: (params) => cellStyleGoodsState(params) },
		{ field: "goods_opt", headerName: "옵션", cellStyle: (params) => colorGrouping(params) },
		{ field: "good_qty", headerName: "온라인재고", width:84, cellStyle: {textAlign: 'right'}, cellStyle: (params) => colorGrouping(params) },
		{ field: "wqty", headerName: "창고재고", width:72, cellStyle: {textAlign: 'right'}, cellStyle: (params) => colorGrouping(params) },
		{ field: "restock", headerName: "재입고요청", width:84, cellStyle: {textAlign: 'right'} },
		{ field: "goods_sh", headerName: "시중가", width:60, type: 'currencyType' },
		{ field: "normal_price", headerName: "정상가", width:60, type: 'currencyType' },
		{ field: "price", headerName: "판매가", width:60, type: 'currencyType' },
		{ field: "sale_type", headerName: "세일구분", width:72, cellStyle: CELL_COLOR.YELLOW },
		{ field: "sale_yn", headerName: "세일여부", width:72, cellStyle: CELL_COLOR.YELLOW },
		{ field: "before_sale_price", headerName: "이전세일가", width:84, type: 'currencyType' },
		{ field: "sale_price", headerName: "세일가", width:60, cellStyle: CELL_COLOR.YELLOW, type: 'currencyType' },
		{ field: "sale_rate", headerName: "세일율(%)", width:84, cellStyle: CELL_COLOR.YELLOW, cellRenderer: (params) => Math.round(params.data.sale_rate) },
		{ field: "sale_dt_yn", headerName: "타임세일여부", width:84, cellStyle: CELL_COLOR.YELLOW },
		{ field: "sale_s_dt", headerName: "세일기간(시작)", width:108 },
		{ field: "sale_e_dt", headerName: "세일기간(종료)", width:108 },
		{ field: "coupon_price", headerName: "쿠폰가", width:60, type: 'currencyType' },
		{ field: "wonga", headerName: "원가", width:60, type: 'currencyType' },         
		{ field: "margin_amt", headerName: "마진액", width:60, type: 'currencyType' },
		{ field: "margin_rate", headerName: "마진율(%)", width:84, cellRenderer: (params) => Math.round(params.data.margin_rate) },
		{ field: "qty", headerName: "재고수", width:60 },
		{ field: "md_nm", headerName: "MD" },
		{ field: "reg_dm", headerName: "등록일자", width: 120 },
		{ field: "upd_dm", headerName: "수정일자", width: 120 },
		{ field: "goods_type_cd", headerName: "goods_type" }
	];

	const CELL_DIMENSION_SIZE = 30;
	const pApp = new App('', { gridId: "#div-gd" });
	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);

		let options = {
			rowHeight: CELL_DIMENSION_SIZE,
			getRowNodeId: data => data.index // 업데이트 및 제거를 위핸 식별 ID 설정
		};
		
		gx = new HDGrid(gridDiv, columns, options);
		Search();

		$("#chk_to_class").click(function() {
			gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
		});
	});
	
	const Search = () => {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/product/prd06/search', data, 1, (data) => {
			$('#prd-total').html(data.head.goods_cnt);
		});
	};

	const popSearchBrand = (type, ismt) => {
		if (type == null || typeof(type) == "undefined") type = '';
		if (ismt == null || typeof(ismt) == "undefined") ismt = '';

		var url = "/head/webapps/standard/std24.php?CMD=popup&TYPE=" + type + "&ISMT=" + ismt;
		openWindow(url, "", "resizable=yes,scrollbars=yes", 700, 600);
	}

	const applyToClass = () => {
		if ($("#chk_to_class").is(":checked")) {
			gx.gridOptions.api.selectAll();
		} else {
			gx.gridOptions.api.deselectAll();
		}
	};
	
	$(".sort_toggle_btn label").on("click", function() {
		$(".sort_toggle_btn label").attr("class", "btn btn-secondary");
		$(this).attr("class", "btn btn-primary");
	});

	const URL = {
		SALE_ON: "/head/product/prd06/sale-on",
		SALE_OFF: "/head/product/prd06/sale-off",
	}

	const onlyNum = ( obj, type ) => { // 해당 객체의 숫자 여부 확인, onkeydown 시 사용
		if ( ! type ) type = "ko";
		if (type == "ko") { // 천단위 콤마
			if (event.keyCode == 8) { 			// Back Space
			} else if (event.keyCode == 9) {		// Tab
			} else if (event.keyCode == 46) {		// Delete
			} else if (event.keyCode >= 48 && event.keyCode <= 57) {		// 1 ~ 0
			} else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
			} else {
				event.returnValue = false;
			}
		} else if (type == "kom") { // 천단위 콤마, 마이너스
			if (event.keyCode == 8) { 			// Back Space
			} else if (event.keyCode == 9) {		// Tab
			} else if (event.keyCode == 46) {		// Delete
			} else if (event.keyCode >= 48 && event.keyCode <= 57) {		// 1 ~ 0
			} else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
			} else if (event.keyCode == 45 && obj.value == ""){ 			// 첫 '-' 가능
			} else {
				event.returnValue = false;
			}
		} else if (type == "us") { // 천단위 콤마, 소수점
			if (event.keyCode == 8) { 			// Back Space
			} else if (event.keyCode == 9) {		// Tab
			} else if (event.keyCode == 46) {		// Delete
			} else if (event.keyCode >= 48 && event.keyCode <= 57) {		// 1 ~ 0
			} else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
			} else if (event.keyCode >= 96 && event.keyCode <= 105) {		// Number Pad 1 ~ 0
			} else if (event.keyCode == 110  ) {							// Number Pad .
			} else if (event.keyCode == 190) {								// .
			} else {
				event.returnValue = false;
			}		
		} else if (type == "usm") { // 천단위 콤마, 소수점, 마이너스
		}
	};

	const saleApply = () => {
		const [ sale_type, sale_rate ] = [ document.sale.sale_type.value, document.sale.sale_rate.value ];
		const cnt = 0;
		if (sale_rate > 0) {
			const checked_rows = gx.gridOptions.api.getSelectedRows();
			const target_rows = checked_rows.map((row) => {
				let price;
				if (row.sale_yn == "Y") {
					price = row.normal_price;
				} else {
					price = row.price;
				}
				const sale_price = price * ( 1 - sale_rate / 100 );
				// 루프를 돌면서 체크된 행들을 업데이트합니다. - 세일가: sale_price, 세일율: sale_rate, 세일구분: sale_type
				gx.gridOptions.api.applyTransaction({update : [ 
					{ ...row, price: price, sale_price: sale_price, sale_type: sale_type, sale_rate: sale_rate }
				]});
			});
		}
	};

	const saleOn = () => {
		if (confirm("세일을 시작하시겠습니까?")) {
			const checked_rows = gx.gridOptions.api.getSelectedRows();

			let check_on_sale = false;
			checked_rows.map(row => { // 세일여부 검사
				if (row.sale_dt_yn == "N" && row.sale_yn == "N") {
					check_on_sale = false;
				} else {
					check_on_sale = true;
					return false;
				}
			});

			if (check_on_sale) {
				alert("이미 세일중인 상품이 있습니다 \n세일중인 상품을 제외하고 선택해주세요");
				return false;
			}

			const target_rows = checked_rows.filter(row => { // 타임세일여부가 N인 경우에만 세일 적용
				return row.sale_dt_yn == "N" ? true : false
			});
			
			const [ sale_type, sale_rate ] = [ document.sale.sale_type.value, document.sale.sale_rate.value ];
			if (target_rows.length > 0) {
				axios({
					url: URL.SALE_ON,
					method: 'post',
					data: { 
						data : target_rows, 
						sale_type: sale_type ? sale_type : "event", 
						sale_rate: sale_rate ? sale_rate : 0
					}
				}).then((response) => {
					if (response.data.code == 1) Search();
				}).catch((error) => {
					// console.log(error);
				});
			} else {
				alert('상품을 선택 해 주십시오.');
			}
		}
	};

	const saleOff = () => {
		if (confirm("세일을 종료하시겠습니까?")) {
			const checked_rows = gx.gridOptions.api.getSelectedRows();
			const target_rows = checked_rows.filter(row => row.sale_dt_yn == "N" ? true : false );
			if (target_rows.length > 0) {
				axios({
					url: URL.SALE_OFF,
					method: 'post',
					data: { data : target_rows }
				}).then((response) => {
					if (response.data.code == 1) Search();
				}).catch((error) => {
					// console.log(error);
				});
			} else {
				alert('상품을 선택 해 주십시오.');
			}
		}
	};
>>>>>>> main

</script>


@stop

