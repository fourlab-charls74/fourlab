@extends('store_with.layouts.layout')
@section('title','온라인교환환불 재고처리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">온라인교환환불 재고처리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>판매관리</span>
		<span>/ 온라인교환환불 재고처리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<a href="javascript:gx.Download('온라인교환환불재고처리_{{ date('YmdH') }}');" class="export-excel btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">주문일자</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ @$edate }}" autocomplete="off">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">주문번호</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='ord_no' id="ord_no" value=''>
							</div>
						</div>
					</div>
{{--						<div class="col-lg-4 inner-td">--}}
{{--							<div class="form-group">--}}
{{--								<label>주문/입금상태</label>--}}
{{--								<div class="form-inline">--}}
{{--									<select name='ord_state' class="form-control form-control-sm" style="width: 47%;">--}}
{{--										<option value=''>전체</option>--}}
{{--										@foreach (@$ord_states as $ord_state)--}}
{{--											<option value='{{ $ord_state->code_id }}' @if($ord_state->code_id === '50') selected @endif>--}}
{{--												{{ $ord_state->code_val }}--}}
{{--											</option>--}}
{{--										@endforeach--}}
{{--									</select>--}}
{{--									<span class="text_line">/</span>--}}
{{--									<select id="pay_stat" name='pay_stat' class="form-control form-control-sm" style="width: 47%;">--}}
{{--										<option value=''>전체</option>--}}
{{--										<option value="0">예정</option>--}}
{{--										<option value="1" selected>입금</option>--}}
{{--									</select>--}}
{{--								</div>--}}
{{--							</div>--}}
{{--						</div>--}}
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>클레임상태</label>
							<div class="form-inline">
								<select name='clm_state' id="clm_state" class="form-control form-control-sm w-100">
									<option value=''>전체</option>
									@foreach (@$clm_states as $clm_state)
										<option value='{{ $clm_state->code_id }}'>
											{{ $clm_state->code_val }}
										</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-inputZip">판매처</label>
							<div class="flax_box">
								<select name='sale_place' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach (@$sale_places as $sale_place)
										<option value='{{ $sale_place->id }}'>{{ $sale_place->val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_info_key">주문정보</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width: 35%;margin-right:2%;">
									<div class="form-group">
										<select name="ord_info_key" id="ord_info_key" class="form-control form-control-sm">
											<option value="om.user_nm">주문자명</option>
											<option value="om.user_id">주문자아이디</option>
											<option value="om.mobile">주문자핸드폰번호</option>
											<option value="om.phone">주문자전화번호</option>
											<option value="om.email">주문자이메일</option>
											<option value="om.r_nm">수령자</option>
											<option value="om.r_mobile">수령자핸드폰번호</option>
											<option value="om.r_phone">수령자전화번호</option>
										</select>
									</div>
								</div>
								<div class="form-inline-inner input_box" style="width: 63%;">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='ord_info_value' value=''>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>결제방법</label>
							<div class="form-inline">
								<div class="form-inline-inner w-100">
									<div class="form-group flax_box">
										<div style="width:calc(100% - 62px);">
											<select name="stat_pay_type" class="form-control form-control-sm mr-2" style="width:100%;">
												<option value="">전체</option>
												@foreach ($stat_pay_types as $stat_pay_type)
													<option value='{{ $stat_pay_type->code_id }}'>
														{{ $stat_pay_type->code_val }}
													</option>
												@endforeach
											</select>
										</div>
										<div style="height:30px;margin-left:5px;">
											<div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="복합결제 제외">
												<input type="checkbox" class="custom-control-input" id="not_complex" name="not_complex" value="Y">
												<label for="not_complex" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
{{--						<div class="col-lg-4 inner-td">--}}
{{--							<div class="form-group">--}}
{{--								<label for="pr_code">판매유형</label>--}}
{{--								<div class="flax_box">--}}
{{--									<select id="sale_kind" name="sale_kind[]" class="form-control form-control-sm multi_select w-100" multiple>--}}
{{--										<option value=''>전체</option>--}}
{{--										@foreach (@$sale_kinds as $sale_kind)--}}
{{--											<option value='{{ $sale_kind->code_id }}'>{{ $sale_kind->code_val }}</option>--}}
{{--										@endforeach--}}
{{--									</select>--}}
{{--								</div>--}}
{{--							</div>--}}
{{--						</div>--}}
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>바코드</label>
							<div class="flex_box">
								<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
								<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>검수여부</label>
							<div class="form-inline">
								<select name='stock_check_yn' id="stock_check_yn" class="form-control form-control-sm w-100">
									<option value=''>전체</option>
									<option value='N' selected>검수전</option>
									<option value='Y'>검수완료</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">자료수/정렬</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<select name="limit" class="form-control form-control-sm">
										<option value="500">500</option>
										<option value="1000">1000</option>
										<option value="2000">2000</option>
										<option value="5000">5000</option>
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:45%;">
									<select name="ord_field" class="form-control form-control-sm">
										<option value="o.ord_date">주문일자</option>
										<option value="o.ord_no">주문번호</option>
										<option value="om.user_nm">주문자명</option>
										<option value="om.r_nm">수령자</option>
										<option value="p.prd_cd">바코드</option>
										<option value="g.goods_nm">상품명</option>
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
				<div class="row search-area-ext d-none">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="prd_cd">상품검색조건</label>
							<div class="form-inline">
								<div class="form-inline-inner input-box w-100">
									<div class="form-inline inline_btn_box">
										<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
										<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 ac-style-no sch-prdcd-range" readonly style="background-color: #fff;">
										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="style_no">스타일넘버/온라인코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ @$style_no }}">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">품목/브랜드</label>
							<div class="form-inline">
								<div class="form-inline-inner select-box" style="width: 32%">
									<select name="item" class="form-control form-control-sm w-100">
										<option value="">전체</option>
										@foreach ($items as $item)
											<option value="{{ $item->cd }}">{{ $item->val }}</option>
										@endforeach
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input-box" style="width:62%">
									<div class="form-inline inline_btn_box">
										<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
										<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row search-area-ext d-none">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">상품명</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_nm_eng">상품명(영문)</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">공급업체</label>
							<div class="form-inline inline_select_box">
								<div class="form-inline-inner input-box w-100">
									<div class="form-inline inline_btn_box">
										<input type="hidden" id="com_cd" name="com_cd" />
										<input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter sch-sup-company" style="width:100%;" autocomplete="off" />
										<a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
			<a href="javascript:gx.Download('온라인교환환불재고처리_{{ date('YmdH') }}');" class="export-excel btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
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
				<div class="fr_box d-flex">
					<p class="text-secondary fs-12 mr-2">* 검수완료 시, <span class="text-danger font-weight-bold">'온라인창고'</span>로 설정된 창고로 재고처리됩니다.</p>
					<a href="javascript:completeCheckStock();" class="btn btn-sm btn-primary shadow-sm">검수완료</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	let columns = [
		{field: "chk", headerName: '', pinned: 'left', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28},
		{field: "ord_no", headerName: "주문번호", pinned: 'left', type: 'StoreOrderNoType'},
		{field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 70, type: 'StoreOrderNoType'},
		{field: "ord_state", hide: true},
		{field: "ord_state_nm", headerName: "주문상태", pinned: 'left', width: 70, cellStyle: StyleOrdState},
		{field: "clm_state", hide: true},
		{field: "clm_state_nm", headerName: "클레임상태", pinned: 'left', width: 70, cellStyle: StyleClmState},
		{field: "pay_stat", hide: true}, // 입금상태
		{field: "ord_type", hide: true}, // 주문구분
		{field: "ord_kind", hide: true}, // 출고구분
		{field: "ord_kind_nm", headerName: "출고구분", pinned: 'left', width: 70, cellStyle: StyleOrdKind},
		{field: "stock_check_yn", headerName: "검수여부", pinned: 'left', width: 70, cellClass: 'hd-grid-code',
			cellStyle: (params) => ({ color: params.value === 'Y' ? '#ff0000' : '#999' }),
			cellRenderer: (params) => params.value === 'Y' ? '검수완료' : '검수전',
		},
		{field: "sale_place", hide: true}, // 판매처
		{field: "sale_place_nm", headerName: "판매처", pinned: 'left', width: 80},
	@if (@$user_group === 'N')
		{field: "store_cd", hide: true}, // 판매매장
		{field: "store_nm", headerName: "판매매장", pinned: 'left', width: 80, type: 'StoreNameType'},
	@endif
		{field: "goods_no", headerName: "온라인코드", pinned: "left", width: 70, cellClass: 'hd-grid-code'},
		{field: "prd_cd", headerName: "바코드", pinned: 'left', width: 125, cellClass: 'hd-grid-code'},
		{field: "prd_cd_p", headerName: "품번", width: 100, cellClass: 'hd-grid-code'},
		{field: "style_no", headerName: "스타일넘버", width: 70, cellClass: 'hd-grid-code'},
		{field: "goods_nm", headerName: "상품명", width: 150, type: 'StoreGoodsNameType'},
		{field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
		{field: "color", headerName: "컬러", width: 55, cellClass: 'hd-grid-code'},
		{field: "size", headerName: "사이즈", width: 55, cellClass: 'hd-grid-code'},
		{field: "goods_opt", headerName: "옵션", width: 100},
		{field: "qty", headerName: "수량", width: 50, type: "currencyType", cellStyle: {"font-weight": "bold"}},
		{field: "comment", headerName: "검수메모", width: 150, 
			cellClass: (params) => params.data.stock_check_yn !== 'Y' ? 'hd-grid-edit' : '', 
			editable: (params) => params.data.stock_check_yn !== 'Y',
			cellRenderer: function(params) {
				return params.data.comment;
			}
		},
		{field: "user_nm", headerName: "주문자(아이디)", width: 120, cellClass: 'hd-grid-code'},
		{field: "r_nm", headerName: "수령자", width: 70, cellClass: 'hd-grid-code'},
		{field: "wonga", headerName: "원가", width: 60, type: "currencyType"},
		{field: "goods_sh", headerName: "정상가", width: 60, type: "currencyType"},
		{field: "goods_price", headerName: "자사몰판매가", width: 85, type: "currencyType"},
		{field: "price", headerName: "판매가", width: 60, type: "currencyType"},
		{field: "pay_type_nm", headerName: "결제방법", width: 80, cellClass: 'hd-grid-code'},
		{field: "dlv_amt", headerName: "배송비", width: 60, type: "currencyType"},
		{field: "baesong_kind", headerName: "배송구분", width: 60, cellClass: 'hd-grid-code'},
		{field: "ord_date", headerName: "주문일시", type: 'DateTimeType'},
		{field: "pay_date", headerName: "입금일시", type: 'DateTimeType'},
		{field: "dlv_end_date", headerName: "배송일시", type: 'DateTimeType'},
		{field: "last_up_date", headerName: "클레임일시", type: 'DateTimeType'},
	];

	const pApp = new App('', { gridId:"#div-gd", height: 265 });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			isRowSelectable: (params) => params.data.stock_check_yn !== 'Y'
		});
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/order/ord04/search', data, 1);
	}
	
	// 검수완료 처리
	const completeCheckStock = () => {
		const rows = gx.getSelectedRows();
		if (rows.length < 1) return alert('검수완료할 클레임건을 선택해주세요.');
		
		if (!confirm("선택한 클레임건을 검수완료하시겠습니까?")) return;

		axios({
			url: '/store/order/ord04/complete-check',
			method: 'post',
			data: { data: rows },
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				Search();
			} else {
				console.log(res.data);
				alert("검수완료처리 중 오류가 발생했습니다.\n다시 시도해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}
</script>
@stop
