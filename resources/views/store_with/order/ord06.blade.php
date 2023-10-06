@extends('store_with.layouts.layout')
@section('title','판매내역조회')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">판매내역조회</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>판매관리</span>
		<span>/ 판매내역조회</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="javascript:;" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch(['#store_no'])">검색조건 초기화</a> -->
					@if(Auth('head')->user()->logistics_group_yn == 'N')
						<a href="javascript:;" onclick="Add()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>수기 등록</a>
						<a href="javascript:;" onclick="AddBatch()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>수기 일괄등록</a>
					@endif
					<a href="javascript:gx.Download('판매내역조회_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_yn">판매상태일자</label>
							<div class="date-switch-wrap form-inline">
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
								<div class="custom-control custom-switch date-switch-pos"  data-toggle="tooltip" data-placement="top" data-original-title="주문일자 사용">
									<input type="checkbox" class="custom-control-input" id="switch4" name="nud" checked="">
									<label class="" for="switch4" data-on-label="ON" data-off-label="OFF"></label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
										@foreach ($store_channel as $sc)
											<option value='{{ $sc->store_channel_cd }}' @if(@$p_store_channel === $sc->store_channel_cd) selected @endif>{{ $sc->store_channel }}</option>
										@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" @if(@$p_store_kind == '') disabled @endif>
										<option value=''>전체</option>
										@foreach ($store_kind as $sk)
											<option value='{{ $sk->store_kind_cd }}' @if(@$p_store_kind === $sk->store_kind_cd) selected @endif>{{ $sk->store_kind }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_no">주문매장</label>
							<div class="form-inline inline_btn_box">
								<input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
								<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>판매/클레임상태</label>
							<div class="form-inline">
								<select name='ord_state' class="form-control form-control-sm" style="width:47%">
									<option value=''>전체</option>
									@foreach (@$ord_states as $ord_state)
										<option value='{{ $ord_state->code_id }}'>
											{{ $ord_state->code_val }}
										</option>
									@endforeach
								</select>
								<span class="text_line">/</span>
								<select name='clm_state' class="form-control form-control-sm" style="width:47%">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">주문번호</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='ord_no' id="ord_no" value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>판매/출고구분</label>
							<div class="form-inline">
								<select name='ord_type' class="form-control form-control-sm" style="width: 47%;">
									<option value=''>전체</option>
									@foreach (@$ord_types as $ord_type)
										<option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
									@endforeach
								</select>
								<span class="text_line">/</span>
								<select name='ord_kind' class="form-control form-control-sm" style="width: 47%;">
									<option value=''>전체</option>
									@foreach (@$ord_kinds as $ord_kind)
										<option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_info_key">판매정보</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width: 35%;margin-right:1%;">
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
											<option value="om.bank_inpnm">입금자</option>
											<option value="om.r_addr1">주소(동명)</option>
											<option value="om.ord_amt">주문총금액</option>
											<option value="o.recv_amt">단일주문금액</option>
											<option value="o.dlv_end_date">배송일자</option>
											<option value="om.dlv_msg">배송메세지</option>
											<option value="o.dlv_no">송장번호</option>
											<option value="memo">처리상태/메모</option>
										</select>
									</div>
								</div>
								<div class="form-inline-inner input_box" style="width: 64%;">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>온/오프라인 주문</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="sale_form_A" name="sale_form" value="" @if($on_off_yn == '') checked @endif />
									<label class="custom-control-label" for="sale_form_A">전체</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="sale_form_On" name="sale_form" value="ON" @if($on_off_yn == 'ON') checked @endif />
									<label class="custom-control-label" for="sale_form_On">온라인</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="sale_form_Off" name="sale_form" value="OFF" @if($on_off_yn == 'OFF') checked @endif />
									<label class="custom-control-label" for="sale_form_Off">오프라인</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
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
							<label for="prd_cd">상품검색조건</label>
							<div class="form-inline">
								<div class="form-inline-inner input-box w-100">
									<div class="form-inline inline_btn_box">
										<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
										<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 sch-prdcd-range" readonly style="background-color: #fff;">
										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
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
										<option value="w.ord_state_date">판매상태일자</option>
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
							<label for="style_no">스타일넘버/온라인코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no">
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
{{--					<div class="col-lg-4 inner-td">--}}
{{--						<div class="form-group">--}}
{{--							<label for="item">품목</label>--}}
{{--							<div class="flex_box">--}}
{{--								<select id="item" name="item" class="form-control form-control-sm">--}}
{{--									<option value="">전체</option>--}}
{{--									@foreach (@$items as $t)--}}
{{--										<option value="{{ $t->cd }}" @if ($t->cd == @$item) selected @endif>{{ $t->val }}</option>--}}
{{--									@endforeach--}}
{{--								</select>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--					<div class="col-lg-4 inner-td">--}}
{{--						<div class="form-group">--}}
{{--							<label for="brand_cd">브랜드</label>--}}
{{--							<div class="form-inline inline_btn_box">--}}
{{--								<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>--}}
{{--								<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_nm">상품명</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value='{{ @$goods_nm }}'>
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
				</div>
				<div class="row search-area-ext d-none">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="pr_code">판매유형</label>
							<div class="flax_box">
								<select id="sell_type" name="sell_type[]" class="form-control form-control-sm multi_select w-100" multiple>
									<option value=''>전체</option>
									@foreach ($sale_kinds as $sale_kind)
										<option value='{{ $sale_kind->code_id }}' @if(in_array($sale_kind->code_id, $sell_type_ids)) selected @endif>{{ $sale_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="pr_code">판매처수수료</label>
							<div class="flax_box">
								<select id="pr_code" name="pr_code[]" class="form-control form-control-sm multi_select w-100" multiple>
									<option value=''>전체</option>
									@foreach ($pr_codes as $pr_code)
										<option value='{{ $pr_code->code_id }}' @if(in_array($pr_code->code_id, $pr_code_ids)) selected @endif>{{ $pr_code->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:;" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch(['#store_no'])">검색조건 초기화</a> -->
			@if(Auth('head')->user()->logistics_group_yn == 'N')
				<a href="javascript:;" onclick="Add()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>수기 등록</a>
				<a href="javascript:;" onclick="AddBatch()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>수기 일괄등록</a>
			@endif
			<a href="javascript:gx.Download('판매내역조회_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
					<div class="d-flex">
						<p class="text-danger fs-12 mr-2">* 상품코드가 맵핑되지 않은 상품의 주문건이 포함될 경우, '정상가', '자사몰판매가', '할인율'의 합계가 정확하지 않을 수 있습니다.</p>
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script type="text/javascript" charset="utf-8">
	const pinnedRowData = [];
	const columns = [
		{headerName: "No", pinned: "left", valueGetter: "node.id", width: 40, cellClass: 'hd-grid-code',
			cellRenderer: (params) => params.value === undefined || params.node.rowPinned === 'top' ? '' : params.node.rowIndex + 1,
		},
		// {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28},
		{field: "ord_no", headerName: "주문번호", pinned: 'left', width: 145, cellStyle: StyleOrdNo, type: 'StoreOrderNoType',
			cellStyle: params => {
				if (params.node.rowPinned === 'top') {
					return {'text-align': 'center'};
				} else {
					return {};
				}
			},
			cellRenderer: function(params) {
				if (params.node.rowPinned === 'top') return "합계";
				else return params.value;
			}
		},
		{field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 60, type: 'StoreOrderNoType', cellStyle: {'text-align': 'center'}},
		{field: "ord_state_date", headerName: "판매상태일자", pinned: 'left', width: 90, cellClass: 'hd-grid-code', 
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : params.value.slice(0,4) + '-' + params.value.slice(4,6) + '-' + params.value.slice(6,8)
		},
		{field: "ord_state", headerName: "판매상태", pinned: 'left', width: 70, cellClass: 'hd-grid-code', 
			cellStyle: (params) => ({ 'color': params.data.ord_state_cd > 30 ? '#ff0000' : '#0000ff', 'font-weight': 'bold' })
		},
		{field: "clm_state", headerName: "클레임상태", pinned: 'left', width: 70, cellStyle: StyleClmState},
		{field: "pay_stat", headerName: "입금상태", pinned: 'left', width: 60, cellStyle: {'text-align': 'center'}},
		{field: "prd_cd", headerName: "바코드", width: 120, cellStyle: {'text-align': 'center'}},
		{field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {'text-align': 'center'}},
		{field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: {'text-align': 'center'}},
		{field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, surl:"{{config('shop.front_url')}}"},
		{field: "img", headerName: "이미지_url", hide:true},
		{field: "goods_nm", headerName: "상품명", width: 150, type: "StoreGoodsNameType"},
		{field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
		{field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
		{field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
		{field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
		{field: "opt_val", headerName: "옵션", width: 130},
		{field: "user_nm", headerName: "주문자(아이디)", width: 100},
		{field: "r_nm", headerName: "수령자", width: 70},
		{field: "wonga", headerName: "원가", width: 85, type: "currencyType"},
		{field: "goods_sh", headerName: "정상가", width: 85, type: "currencyType"},
		{field: "goods_price", headerName: "자사몰판매가", width: 85, type: "currencyType"},
		{field: "price", headerName: "현재가", width: 85, type: "currencyType"},
		{field: "sale_dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType"},
		{field: "sale_kind_nm", headerName: "판매유형", width: 100},
		{field: "sale_kind", headerName: "판매유형", hide:true},
		{field: "sale_price", headerName: "판매단가", width: 80, type: "currencyType"},
		{field: "dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType"},
		{field: "qty", headerName: "판매수량", width: 60, type: "currencyType" ,
			cellRenderer: function(params) {
				if (params.node.rowPinned === 'top') return params.value;
				else if (params.value !== undefined) {
					return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
				}
			}
		},
		{field: "ord_amt", headerName: "판매금액", width: 80, type: "currencyType"},
		{field: "recv_amt", headerName: "실결제금액", width: 80, type: "currencyType"},
		{field: "pr_code_nm", headerName: "판매처수수료", width: 80},
		{field: "dlv_amt", headerName: "배송비", width: 60, type: "currencyType"},
		{field: "sales_com_fee", headerName: "판매수수료", width: 80, type: "currencyType"},
		{field: "pay_type", headerName: "결제방법", width: 80, cellStyle: {'text-align': 'center'}},
		{field: "ord_type", headerName: "판매구분", width: 60, cellStyle: {'text-align': 'center'}},
		{field: "ord_kind", headerName: "출고구분", width: 60, cellStyle: StyleOrdKind},
		{field: "store_nm", headerName: "주문매장", width: 100},
		{field: "baesong_kind", headerName: "배송구분", width: 60},
		{field: "state", headerName: "처리현황", width: 80},
		{field: "memo", headerName: "메모", width: 120},
		{field: "ord_date", headerName: "주문일시", type: "DateTimeType"},
		{field: "pay_date", headerName: "입금일시", type: "DateTimeType"},
		{field: "dlv_end_date", headerName: "배송일시", type: "DateTimeType"},
		{field: "last_up_date", headerName: "클레임일시", type: "DateTimeType"},
	];

	let gx;
	const pApp = new App('', { gridId: "#div-gd", height: 265 });

	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
				if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
			},
		});

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();

		initSearchTab();
		Search();
	});

	function initSearchTab() {
		let store_cd = "{{ @$store->store_cd }}";
		let store_nm = "{{ @$store->store_nm }}";

		{{--let brand_cd = "{{ @$brand->brand }}";--}}
		{{--let brand_nm = "{{ @$brand->brand_nm }}";--}}

		if (store_cd != '') {
			const option = new Option(store_nm, store_cd, true, true);
			$('#store_no').append(option).trigger('change');
		}

		// if (brand_cd != '') {
		// 	const option = new Option(brand_nm, brand_cd, true, true);
		// 	$('#brand_cd').append(option).trigger('change');
		// }

		let prd_cd_range = <?= json_encode(@$prd_cd_range) ?>;
		let prd_cd_range_nm = "{{ @$prd_cd_range_nm }}";
		prd_cd_range = Object.keys(prd_cd_range).reduce((a, c) => {
			if (c.includes('_contain') || c === 'match') return a;
			return a + prd_cd_range[c].map(rg => '&' + c +'[]=' + rg).join('');
		}, '');

		$('#prd_cd_range').val(prd_cd_range);
		$('#prd_cd_range_nm').val(prd_cd_range_nm);
	}

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/order/ord06/search', data, 1, function(e) {
			const t = e.head.total_row;
			gx.gridOptions.api.setPinnedTopRowData([{
				qty: t.total_qty,
				// goods_sh: t.total_goods_sh,
				// goods_price: t.total_goods_price,
				// price: t.total_price,
				// wonga: t.total_wonga,
				// sale_dc_rate: t.avg_sale_dc_rate,
				// sale_price: t.total_sale_price,
				// dc_rate: t.avg_dc_rate,
				ord_amt: t.total_ord_amt,
				recv_amt: t.total_recv_amt,
				dlv_amt: t.total_dlv_amt
			}]);
		});
	}

	// 수기등록 팝업오픈
	function Add() {
		let url = '/store/order/ord01/create';
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	// 수기일괄등록 팝업오픈
	function AddBatch() {
		let url = '/store/order/ord01/batch-create';
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1400,height=800");
	}
</script>
@stop
