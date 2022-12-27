@extends('store_with.layouts.layout')
@section('title','온라인 주문접수')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">온라인 주문접수</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 주문/배송관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
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
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='ord_no' id="ord_no" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>주문/입금상태</label>
                            <div class="form-inline">
                                <select name='ord_state' class="form-control form-control-sm" style="width: 47%;">
                                    <option value=''>전체</option>
                                    @foreach (@$ord_states as $ord_state)
                                        <option value='{{ $ord_state->code_id }}' @if($ord_state->code_id === '10') selected @endif>
                                            {{ $ord_state->code_val }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text_line">/</span>
                                <select id="pay_stat" name='pay_stat' class="form-control form-control-sm" style="width: 47%;">
                                    <option value=''>전체</option>
                                    <option value="0">예정</option>
                                    <option value="1">입금</option>
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
                            <label for="store_no">배송처</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box w-25">
                                    <div class="form-group">
                                        <select name="dlv_place" id="dlv_place" class="form-control form-control-sm">
                                            <option value="storage">창고</option>
                                            <option value="store">매장</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="storage_search" class="form-inline w-75 pl-2">
                                    <input type='text' id="storage_nm" name="storage_nm" class="form-control form-control-sm w-100" placeholder="창고명을 입력해주세요">
                                </div>
                                <div id="store_search" class="form-inline inline_btn_box w-75 pl-2 d-none">
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
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
                                <div class="form-inline-inner input_box" style="width: 63%;">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_info_value' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                            <label for="sale_kind">판매유형</label>
                            <div class="flex_box">
                                <select name="sale_kind" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach (@$sale_kinds as $sale_kind)
                                        <option value="{{ $sale_kind->code_id }}">{{ $sale_kind->code_val }}</option>
                                    @endforeach
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
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="o.ord_no">주문번호</option>
                                        <option value="om.user_nm">주문자명</option>
                                        <option value="om.r_nm">수령자</option>
                                        <option value="p.prd_cd">상품코드</option>
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
							<label>상품코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input-box w-100">
									<div class="form-inline inline_btn_box">
										<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm w-100 ac-style-no search-enter">
										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품번호</label>
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
                            <label for="name">공급업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_cd" name="com_cd" />
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="row search-area-ext d-none">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="prd_cd">상품옵션 범위검색</label>
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
                </div>
                <div class="row search-area-ext d-none">
					<div class="col-lg-4 inner-td">
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
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="brand_cd">브랜드</label>
							<div class="form-inline inline_btn_box">
								<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
								<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
            <a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
            <a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
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
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
    let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28},
        {field: "ord_no", headerName: "주문번호", pinned: 'left', width: 130, cellStyle: StyleOrdNo, type: 'StoreOrderNoType'},
        {field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 60, type: 'StoreOrderNoType', cellStyle: {'text-align': 'center'}},
        {field: "ord_state", headerName: "주문상태", pinned: 'left', width: 70, cellStyle: StyleOrdState},
        {field: "pay_stat", headerName: "입금상태", pinned: 'left', width: 55, cellStyle: {'text-align': 'center'}},
        {field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "goods_no", headerName: "상품번호", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "goods_nm", headerName: "상품명", width: 150, type: "HeadGoodsNameType"},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
        {field: "prd_cd_p", headerName: "코드일련", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "opt_val", headerName: "옵션", width: 130},
        {field: "qty", headerName: "수량", width: 50, type: "currencyType" ,
            cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                    }
            }
        },
        {field: "user_nm", headerName: "주문자(아이디)", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "r_nm", headerName: "수령자", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "wonga", headerName: "원가", width: 60, type: "currencyType"},
        {field: "goods_sh", headerName: "TAG가", width: 60, type: "currencyType"},
        {field: "goods_price", headerName: "자사몰판매가", width: 85, type: "currencyType"},
        {field: "price", headerName: "판매가", width: 60, type: "currencyType"},
        {field: "dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType"},
        {field: "sale_kind_nm", headerName: "판매유형", width: 100, cellStyle: {"text-align": "center"}},
        {field: "pr_code_nm", headerName: "행사구분", width: 60, cellStyle: {"text-align": "center"}},
        {field: "dlv_amt", headerName: "배송비", width: 60, type: "currencyType"},
        {field: "sales_com_fee", headerName: "판매수수료", width: 80, type: "currencyType"},
        {field: "pay_type", headerName: "결제방법", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "ord_type", headerName: "주문구분", width: 60, cellStyle: {'text-align': 'center'}},
        {field: "ord_kind", headerName: "출고구분", width: 60, cellStyle: StyleOrdKind},
        {field: "sale_place", headerName: "판매처", width: 100},
        {field: "state", headerName: "처리현황", width: 120},
        {field: "memo", headerName: "메모", width: 120},
        {field: "ord_date", headerName: "주문일시", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "pay_date", headerName: "입금일시", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "dlv_end_date", headerName: "배송일시", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "last_up_date", headerName: "클레임일시", width: 120, cellStyle: {'text-align': 'center'}},
        {field: "_qty", headerName: "창고1", pinned: 'right', width: 80},
        {field: "_qty", headerName: "매장1", pinned: 'right', width: 80},
        {field: "dlv_place", headerName: "배송처", pinned: 'right', width: 100},
    ];
</script>

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);

		// Search();

        $("[name=dlv_place]").on("change", function(e) {
            $("#store_search").toggleClass("d-none", e.target.value === 'storage');
            $("#storage_search").toggleClass("d-none", e.target.value === 'store');
        });
	});
	
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/order/ord02/search', data, -1);
	}
</script>
@stop
