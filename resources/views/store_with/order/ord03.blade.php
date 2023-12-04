@extends('store_with.layouts.layout')
@section('title','온라인출고등록')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">온라인출고등록</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>판매관리</span>
		<span>/ 온라인출고등록</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
                    <a href="javascript:void(0);" onclick="return openBatchPopup();" class="btn btn-sm btn-primary shadow-sm mr-1">택배송장 일괄입력</a>
                    <div class="btn-group dropleftbtm mr-1">
                        <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" onclick="return exportDlvList();"><i class="bx bx-download fs-16 mr-1"></i> 배송목록 받기</a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" onclick="return openDlvInvoicePopup();"><i class="bx bx-download fs-16 mr-1"></i> 택배송장목록 받기</a>
                            {{-- <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"><i class="bx bx-download fs-16 mr-1"></i> 판매처 택배송장목록 받기</a> --}}
                        </div>
                    </div>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_no">일자검색</label>
                            <div class="d-flex">
                                <div class="flex_box w-25 mr-2">
                                    <select name='search_date_stat' class="form-control form-control-sm">
                                        <option value="receipt">접수일자</option>
                                        <option value="order">주문일자</option>
                                    </select>
                                </div>
                                <div class="form-inline date-select-inbox w-75">
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
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_no">출고차수</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='rel_order' id="rel_order" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="store_no">배송처</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box w-25">
                                    <div class="form-group">
                                        <select name="dlv_place_type" id="dlv_place_type" class="form-control form-control-sm">
                                            <option value="">전체</option>
                                            <option value="storage" selected>창고</option>
                                            @if (@$user_group === @$user_groups['HEAD'])
                                            <option value="store">매장</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div id="storage_search" class="form-inline w-75 pl-2">
                                    {{-- <input type='text' id="storage_nm" name="storage_nm" class="form-control form-control-sm w-100" placeholder="창고명을 입력해주세요"> --}}
                                    <select name='storage_cd' id="storage_cd" class="form-control form-control-sm w-100">
                                        <option value=''>전체</option>
                                        @foreach (@$dlv_storages as $dlv_storage)
                                        <option value='{{ $dlv_storage->storage_cd }}'>{{ $dlv_storage->storage_nm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="store_search" class="form-inline inline_btn_box w-75 pl-2 d-none">
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_no">주문번호</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='ord_no' id="ord_no" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>접수상태/배송방식</label>
                            <div class="form-inline">
                                <select name='ord_state' class="form-control form-control-sm" style="width: 47%;">
                                    <option value=''>전체</option>
                                    <option value='20' selected>주문접수</option>
                                    <option value='30'>배송처리</option>
                                </select>
                                <span class="text_line">/</span>
                                <select id="dlv_type" name='dlv_type' class="form-control form-control-sm" style="width: 47%;">
                                    <option value=''>전체</option>
                                    @foreach (@$dlv_types as $dlv_type)
                                        <option value='{{ $dlv_type->code_id }}'>
                                            {{ $dlv_type->code_val }}
                                        </option>
                                    @endforeach
                                </select>
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
                                                @foreach (@$stat_pay_types as $stat_pay_type)
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
                                        <option value="o.ord_opt_no">주문번호</option>
                                        <option value="om.user_nm">주문자명</option>
                                        <option value="om.r_nm">수령자</option>
                                        <option value="pc.prd_cd">바코드</option>
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
							<label>바코드</label>
							<div class="flex_box">
                                <input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
                                <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                            <label for="pr_code">판매유형</label>
                            <div class="flax_box">
                                <select id="sale_kind" name="sale_kind[]" class="form-control form-control-sm multi_select w-100" multiple>
                                    <option value=''>전체</option>
                                    @foreach (@$sale_kinds as $sale_kind)
                                    <option value='{{ $sale_kind->code_id }}'>{{ $sale_kind->code_val }}</option>
                                    @endforeach
                                </select>
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
            <a href="javascript:void(0);" onclick="return openBatchPopup();" class="btn btn-sm btn-primary shadow-sm mr-1">택배송장 일괄입력</a>
            <div class="btn-group dropleftbtm mr-1">
                <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                </button>
                <div class="dropdown-menu" style="">
                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" onclick="return exportDlvList();"><i class="bx bx-download fs-16 mr-1"></i> 배송목록 받기</a>
                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);" onclick="return openDlvInvoicePopup();"><i class="bx bx-download fs-16 mr-1"></i> 택배송장목록 받기</a>
                    <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"><i class="bx bx-download fs-16 mr-1"></i> 판매처 택배송장목록 받기</a>
                </div>
            </div>
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
                    <div class="d-flex">
                        <div class="custom-control custom-checkbox form-check-box mr-2">
							<input type="checkbox" name="send_sms_yn" id="send_sms_yn" class="custom-control-input" checked="" value="Y">
							<label class="custom-control-label text-left" for="send_sms_yn" style="line-height:27px;justify-content:left">배송 문자 발송</label>
						</div>
{{--                        <select id='u_dlvs' name='u_dlvs' class="form-control form-control-sm mr-2" style='width:120px;'>--}}
{{--                            <option value="">전체</option>--}}
{{--                            @foreach (@$dlvs as $dlv)--}}
{{--                                <option value='{{ $dlv->code_id }}'{{ $dlv->code_id === $dlv_cd ? 'selected' : '' }}>{{ $dlv->code_val }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
                    </div>
                    <a href="javascript:void(0);" onclick="return completeOrder();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-check fa-sm text-white-50 mr-1"></i> 온라인출고완료</a>
                    <span class="ml-2 mr-2 text-secondary">|</span>
                    <div class="d-flex">
                        <span class="mr-2">출고구분/거부사유 :</span>
                        <select id='ord_kind' name='ord_kind' class="form-control form-control-sm mr-2" style='width:120px;'>
                            @foreach (@$ord_kinds as $ord_kind)
                                <option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
                            @endforeach
                        </select>
	                    <select id='rel_reject_reason' name='rel_reject_reason' class="form-control form-control-sm mr-2" style='width:120px;'>
		                    @foreach (@$rel_reject_reasons as $rel_reject_reason)
			                    <option value='{{ $rel_reject_reason->code_id }}'>{{ $rel_reject_reason->code_val }}</option>
		                    @endforeach
	                    </select>
                    </div>
                    <a href="javascript:void(0);" onclick="return updateOrdKind();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-redo fa-sm text-white-50 mr-1"></i> 출고거부</a>
                </div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	const dlv_companies = <?= json_encode(@$dlv_companies) ?> ;
	const dlv_locations = <?= json_encode(@$dlv_locations) ?> ;
	const pinnedRowData = [{ dlv_location_nm : "합계", qty : 0
		@foreach($dlv_locations as $dlv_location)
		, '{{ $dlv_location->seq . "_" . $dlv_location->location_type . "_" . $dlv_location->location_cd . "_qty" }}' : 0
		@endforeach
	
	}];

    let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: (params) => params.data.state < 30, headerCheckboxSelection: true, sort: null, width: 28},
        {field: "rel_order", headerName: "출고차수", pinned: 'left', width: 100, cellStyle: {'text-align': 'center'}},
        {field: "dlv_no", headerName: "송장번호", pinned: 'left', width: 120, editable: (params) => params.data.state < 30, cellStyle: (params) => ({'text-align': 'center', 'background-color': params.data.state < 30 ? '#ffff99' : 'none'})},
        {field: "dlv_nm", headerName: "택배사", pinned: 'left', width: 100,
			editable: (params) => params.data.state < 30,
			cellStyle: (params) => ({'text-align': 'center', 'background-color': params.data.state < 30 ? '#ffff99' : 'none'}),
			cellEditor: GridAutoCompleteEditor,
			cellEditorPopup: true,
			cellEditorParams: {
				cellEditor: GridAutoCompleteEditor,
				rowData: dlv_companies,
				dataKey: "label",
				width: "138px",
			}
		},
        {field: "dlv_location_cd", headerName: "배송처코드", pinned: 'left', width: 70, 
            cellStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return {};
				} else {
					return {'text-align': 'center', 'background-color': params.data.dlv_location_type === 'STORAGE' ? '#0000ff' : '#ff0000', 'background-color': params.data.dlv_location_type === 'STORAGE' ? '#D9E3FF' : '#FFE9E9'};
				}
			},
        },
        {field: "dlv_location_nm", headerName: "배송처명", pinned: 'left', width: 100, 
            cellStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return {'text-align' : 'center'};
				} else {
					return {'text-align': 'center', 'color': params.data.dlv_location_type === 'STORAGE' ? '#0000ff' : '#ff0000', 'background-color': params.data.dlv_location_type === 'STORAGE' ? '#D9E3FF' : '#FFE9E9'};
				}
			},
        },
        {field: "ord_no", headerName: "주문번호", pinned: 'left', width: 135,
            cellRenderer: (params) => {
				if (params.node.rowPinned === 'top') {
					return '';
				}else {
                	return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + params.data?.ord_no + '\',\'' + params.data?.ord_opt_no +'\');">'+ params.value +'</a>';
				}
            }
        },
        {field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 60, cellStyle: {'text-align': 'center'},
            cellRenderer: (params) => {
				if (params.node.rowPinned === 'top') {
					return '';
				} else {
                	return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + params.data?.ord_no + '\',\'' + params.data?.ord_opt_no +'\');">'+ params.value +'</a>';
				}
            }
        },
        {field: "ord_state_nm", headerName: "주문상태", width: 70, cellStyle: StyleOrdState},
        {field: "pay_stat_nm", headerName: "입금상태", width: 55, cellStyle: {'text-align': 'center'}},
        {field: "ord_type_nm", headerName: "주문구분", width: 60, cellStyle: {'text-align': 'center'}},
        {field: "ord_kind_nm", headerName: "출고구분", width: 60, cellStyle: StyleOrdKind},
        {field: "sale_place_nm", headerName: "판매처", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "prd_cd", headerName: "바코드", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
        {field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: {'text-align': 'center'}},
        {field: "goods_nm", headerName: "상품명", width: 150,
            cellRenderer: function (params) {
				if (params.node.rowPinned === 'top') {
					return '';
				} else {
                	return '<a href="#" onclick="return openHeadProduct(\'' + params.data?.goods_no + '\');">' + params.value + '</a>';
				}
			}
        },
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 130},
        {field: "qty", headerName: "수량", width: 50, type: "currencyType", aggFunc: "first",
			cellStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return {};
				} else {
					return {"font-weight": "bold", 'background-color': '#D5FFDA'};
				}
			}
		},
        @foreach (@$dlv_locations as $loc)
            {field: "{{ $loc->seq }}_{{ $loc->location_type }}_{{ $loc->location_cd }}_qty", headerName: "{{ $loc->location_nm }}", width: 80, type: "currencyType",
                cellStyle: (params) => (
                    {
                        'color': params.data.dlv_location_cd === '{{ $loc->location_cd }}' ? (params.data.dlv_location_type === 'STORAGE' ? '#0000ff' : '#ff0000') : 'none', 
                        'background-color': params.data.dlv_location_cd === '{{ $loc->location_cd }}' ? (params.data.dlv_location_type === 'STORAGE' ? '#D9E3FF' : '#FFE9E9') : 'none'
                    }
                ),
                onCellDoubleClicked: (e) => {
                    if (e.data && e.value >= e.data.qty) e.node.setDataValue('dlv_place', "{{ $loc->location_nm }}");
                }
            },
        @endforeach
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
        {field: "dlv_store_fee", headerName: "판매수수료율(%)", width: 100, type: "percentType"},
        {field: "pay_type_nm", headerName: "결제방법", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "baesong_kind", headerName: "배송구분", width: 60, cellStyle: {'text-align': 'center'}},
        {field: "ord_date", headerName: "주문일시", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "pay_date", headerName: "입금일시", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "req_nm", headerName: "접수자", width: 80, cellStyle: {'text-align': 'center'}},
        {field: "receipt_date", headerName: "접수일시", width: 125, cellStyle: {'text-align': 'center'}},
        {field: "receipt_comment", headerName: "접수메모", width: 150},
    ];
</script>

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
			getRowStyle : (params) => {
				if (params.node.rowPinned) return { "font-weight": "bold", "background": "#eee", "border": "none" };
			},
			defaultColDef: {
				suppressMenu: true,
				resizable: false,
				autoHeight: true,
				suppressSizeToFit: false,
				sortable:true,
			},
			suppressCopyRowsToClipboard: true,
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
				// if (e.column.colId == "dlv_nm") {
				// 	let arr = dlv_companies.filter(s => s.code_val === e.newValue);
				// 	e.data.code_id = arr.length > 0 ? arr[0].code_id : null;
				// 	if (!e.data.code_id) {
				// 		e.api.redrawRows({rowNodes:[e.node]});
				// 		gx.setFocusedWorkingCell();
				// 	}
				// }
            },
            isRowSelectable: (params) => {
                return params.data.state < 30;
            },
        });

		Search();

		$("[name=dlv_place_type]").on("change", function(e) {
			if (e.target.value == 'storage') {
				$("#store_search").hide();
				$("#storage_search").show();
			} else if (e.target.value == 'store') {
				$("#storage_search").hide();
				$("#store_search").show();
			} else {
				$("#store_search").hide();
				$("#storage_search").show();
			}
			
			$("#store_search").toggleClass("d-none", e.target.value === 'storage');
			$("#storage_search").toggleClass("d-none", e.target.value === 'store');
		});
	});
	
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/order/ord03/search', data, -1, function(d) {
			let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
			let total_data = d.head.total_data;
			if (pinnedRow && total_data != '') {
				gx.gridOptions.api.setPinnedTopRowData([{...pinnedRow.data, ...total_data}]);
			}
		});
	}

    // 출고완료처리
    function completeOrder() {
        let rows = gx.getSelectedRows();

        // validation
        // if(!$("#u_dlvs").val()) return alert("택배사를 선택해주세요.");
		if(rows.filter(r => !r.dlv_nm).length > 0) return alert("택배사를 선택해주세요.");
        if(rows.length < 1) return alert("출고완료처리할 주문건을 선택해주세요.");
        if(rows.filter(r => r.ord_state != 20).length > 0) return alert("출고처리중 상태의 주문건만 처리가 가능합니다.");
        if(rows.filter(r => r.ord_kind > 20).length > 0) return alert("출고보류중인 주문건은 처리할 수 없습니다.");
        if(rows.filter(r => !r.dlv_no).length > 0) return alert("송장번호가 입력되지 않은 주문건이 있습니다.\n확인 후 다시 처리해주세요.");

        if(!confirm("선택한 주문건을 출고완료처리하시겠습니까?")) return;

        axios({
            url: '/store/order/ord03/complete',
            method: 'post',
            data: { 
                send_sms_yn: $("#send_sms_yn:checked").val(),
                // u_dlvs: $("#u_dlvs").val(),
                data: rows
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                if (res.data.failed_rows.length > 0) alert("온라인주문이 출고완료되었으나 재고부족 등의 사유로 배송처리에 실패한 주문건이 존재합니다.\n주문번호 확인 후 다시 시도해주세요.\n해당주문건 : " + res.data.failed_rows.join(", "));
                else alert("출고완료처리가 정상적으로 완료되었습니다.");

                Search();
            } else {
                console.log(res.data);
                alert("출고완료처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 출고요청처리 (+출고구분변경)
    function updateOrdKind() {
        let rows = gx.getSelectedRows();

        // validation
        if(rows.length < 1) return alert("출고거부할 주문건을 선택해주세요.");
        const ord_kind_nm = $("#ord_kind option:checked").text();
        const reject_reason = $("#rel_reject_reason option:checked").text();

        if(!confirm(`아래 내용과 같이 출고거부처리하시겠습니까?\n출고구분: ${ord_kind_nm} / 거부사유: ${reject_reason}`)) return;

        axios({
            url: '/store/order/ord03/update/ord-kind',
            method: 'post',
            data: { 
                ord_kind: $("#ord_kind").val(),
				reject_reason: $("#rel_reject_reason").val(),
                ord_opt_nos: rows.map(r => r.ord_opt_no),
                or_prd_cds: rows.map(r => r.or_prd_cd),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert("출고거부처리가 정상적으로 완료되었습니다.");
                Search();
            } else {
                console.log(res.data);
                alert("출고거부처리 중 오류가 발생했습니다.\n다시 시도해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 배송목록 받기
    function exportDlvList() {
        let data = getFormSerializedData();
        location.href = "/store/order/ord03/download/dlv-list?" + data;
    }

    // 택배송장목록 받기 팝업창 오픈
    function openDlvInvoicePopup() {
        window.open('/store/order/ord03/show/invoice-list', '_blank', 'toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=720');
    }

    // 택배송장목록 받기 팝업창 오픈
    function openBatchPopup() {
        window.open('/store/order/ord03/show/batch', '_blank', 'toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=768');
    }

    // ord03_invoice.blade.php 에서 사용
    function getFormSerializedData() {
        return $('form[name="search"]').serialize();
    }
</script>
@stop
