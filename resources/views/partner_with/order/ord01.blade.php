@extends('partner_with.layouts.layout')
@section('title','주문내역')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">주문내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
	    <span>/ 주문&amp;배송</span>
        <span>/ 주문내역</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
	                <a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <a href="javascript:void(0);" onclick="gx.Download('주문내역.csv');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">주문일자</label>
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
		                            <input type="checkbox" class="custom-control-input" name="s_nud" id="s_nud" checked="" value="N" onClick="ManualNotUseData();">
		                            <label class="" for="s_nud" data-on-label="ON" data-off-label="OFF"></label>
	                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_no">주문번호</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter search-all" id="ord_no" name='ord_no' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">주문자/아이디</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm search-enter search-all" name='user_nm' value=''>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
	                                <input type="text" class="form-control form-control-sm search-all search-enter" name="user_id" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''   >
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
	                <div class="col-lg-4 inner-td">
		                <div class="form-group">
			                <label for="name">수령자/입금자</label>
			                <div class="form-inline">
				                <div class="form-inline-inner input_box">
					                <div class="form-group">
						                <input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' value=''>
					                </div>
				                </div>
				                <span class="text_line">/</span>
				                <div class="form-inline-inner input_box">
					                <div class="form-group">
						                <input type='text' class="form-control form-control-sm search-all search-enter" name='bank_inpnm' value=''>
					                </div>
				                </div>
			                </div>
		                </div>
	                </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="key">검색항목</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
	                                <div class="form-group">
	                                    <select name='cols' id="cols" class="form-control form-control-sm">
		                                    <option value="">선택하세요.</option>
		                                    <option value="b.mobile" selected>주문자핸드폰번호</option>
		                                    <option value="b.phone">주문자전화번호</option>
		                                    <option value="b.r_mobile">수령자핸드폰번호</option>
		                                    <option value="b.r_phone">수령자전화번호</option>
		                                    <option value="b.email">주문자이메일</option>
		                                    <option value="b.r_addr1">주소(동명)</option>
		                                    <option value="b.ord_amt">주문총금액</option>
		                                    <option value="a.recv_amt">단일주문금액</option>
		                                    <option value="a.dlv_end_date">배송일자</option>
		                                    <option value="b.dlv_msg">배송메세지</option>
		                                    <option value="a.dlv_cd">택배사</option>
	                                    </select>
	                                </div>
                                </div>
	                            <span class="text_line">/</span>
	                            <div class="form-inline-inner input_box">
		                            <div class="form-group">
			                            <input type='text' class="form-control form-control-sm search-all search-enter" name='key' id="key" value=''>
		                            </div>
	                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
	                <div class="col-lg-4 inner-td">
		                <div class="form-group">
			                <label for="ord_state">주문/입금상태</label>
			                <div class="form-inline">
				                <div class="form-inline-inner input_box">
					                <div class="form-group">
						                <select name='ord_state' class="form-control form-control-sm">
							                <option value=''>전체</option>
							                @foreach ($ord_states as $ord_state)
								                <option value="{{ $ord_state->code_id }}">{{ $ord_state->code_val }}</option>
							                @endforeach
						                </select>
					                </div>
				                </div>
				                <span class="text_line">/</span>
				                <div class="form-inline-inner input_box">
					                <div class="form-group">
						                <select name='pay_stat' class="form-control form-control-sm">
							                <option value=''>전체</option>
							                <option value="0">예정</option>
							                <option value="1">입금</option>
						                </select>
					                </div>
				                </div>
			                </div>
		                </div>
	                </div>
	                <div class="col-lg-4 inner-td">
		                <div class="form-group">
			                <label for="name">클레임 상태</label>
			                <div class="flax_box">
				                <select name='clm_state' class="form-control form-control-sm">
					                <option value=''>전체</option>
					                @foreach ($clm_states as $clm_state)
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
			                <label for="name">결제방법/현금영수증</label>
			                <div class="form-inline">
				                <div class="form-inline-inner" style="width:74%;">
					                <div class="form-group flax_box">
						                <div style="width:calc(100% - 177px);">
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
						                <div style="height:30px;margin-left:2px;">
							                <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="결제수수료 주문(개발예정)">
								                <input type="checkbox" class="custom-control-input" id="pay_fee" value="Y" disabled>
								                <label for="pay_fee" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
							                </div>
						                </div>
						                <div style="height:30px;margin-left:2px;">
							                <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="간편결제(개발예정)">
								                <input type="checkbox" class="custom-control-input" id="fintech" value="Y" disabled>
								                <label for="fintech" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
							                </div>
						                </div>
					                </div>
				                </div>
				                <span class="text_line">/</span>
				                <div class="form-inline-inner input_box" style="width:20%;">
					                <div class="form-group">
						                <select name="receipt" class="form-control form-control-sm">
							                <option value="">전체</option>
							                <option value="R">신청</option>
							                <option value="Y">발행</option>
						                </select>
					                </div>
				                </div>
			                </div>
		                </div>
	                </div>
                </div>
	            <div class="row d-none search-area-ext">
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="name">주문/출고구분</label>
				            <div class="form-inline">
					            <div class="form-inline-inner input_box">
						            <div class="form-group">
							            <select name='ord_type' class="form-control form-control-sm">
								            <option value=''>전체</option>
								            @foreach ($ord_types as $ord_type)
									            <option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
								            @endforeach
							            </select>
						            </div>
					            </div>
					            <span class="text_line">/</span>
					            <div class="form-inline-inner input_box">
						            <div class="form-group">
							            <select name='ord_kind' class="form-control form-control-sm">
								            <option value=''>전체</option>
								            @foreach ($ord_kinds as $ord_kind)
									            <option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
								            @endforeach
							            </select>
						            </div>
					            </div>
				            </div>
			            </div>
		            </div>
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="name">배송구분/배송방식</label>
				            <div class="form-inline">
					            <div class="form-inline-inner input_box">
						            <div class="form-group">
							            <select name="dlv_kind" class="form-control form-control-sm">
								            <option value="">전체</option>
								            @foreach ($dlv_kinds as $dlv_kind)
									            <option value='{{ $dlv_kind->code_id }}'>{{ $dlv_kind->code_val }}</option>
								            @endforeach
							            </select>
						            </div>
					            </div>
					            <span class="text_line">/</span>
					            <div class="form-inline-inner input_box">
						            <div class="form-group">
							            <select name='dlv_type' class="form-control form-control-sm">
								            <option value=''>전체</option>
								            @foreach ($dlv_types as $dlv_type)
									            <option value='{{ $dlv_type->code_id }}'>{{ $dlv_type->code_val }}</option>
								            @endforeach
							            </select>
						            </div>
					            </div>
				            </div>
			            </div>
		            </div>
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="name">송장번호</label>
				            <div class="flax_box">
					            <input type="text" name="dlv_no" id="dlv_no" class="form-control form-control-sm search-all search-enter">
				            </div>
			            </div>
		            </div>
                </div>
	            <div class="row d-none search-area-ext">
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="name">판매처</label>
				            <div class="flax_box">
					            <select name='sale_place' class="form-control form-control-sm">
						            <option value=''>전체</option>
						            @foreach ($sale_places as $sale_place)
							            <option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
						            @endforeach
					            </select>
				            </div>
			            </div>
		            </div>
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="name">판매처 주문번호</label>
				            <div class="flax_box">
					            <input type="text" name="out_ord_no" id="out_ord_no" class="form-control form-control-sm search-all search-enter">
				            </div>
			            </div>
		            </div>
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="formrow-email-input">상품명</label>
				            <div class="flax_box">
					            <input type='text' class="form-control form-control-sm ac-goods-nm search-all search-enter" name='goods_nm' value=''>
				            </div>
			            </div>
		            </div>
	            </div>
	            <div class="row d-none search-area-ext">
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
                            <label for="brand_nm">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
		            <div class="col-lg-4 inner-td">
			            <div class="form-group">
				            <label for="goods_nm_eng">상품명(영문)</label>
				            <div class="flax_box">
					            <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
				            </div>
			            </div>
		            </div>
                </div>
                <div class="row search-area-ext d-none">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
	                        <label for="goods_type">상품구분</label>
	                        <div class="flax_box">
		                        <select name='goods_type' id="goods_type" class="form-control form-control-sm">
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
                            <label for="dlv_kind">상단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='head_desc' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="limit">자료수/정렬</label>
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
                                        <option value="a.ord_date" selected>주문일자</option>
                                        <option value="b.user_nm" >주문자</option>
                                        <option value="b.r_nm" >수령자</option>
                                        <option value="c.goods_nm" >상품명</option>
                                        <option value="c.style_no" >스타일넘버</option>
                                        <option value="a.head_desc" > 상단홍보글</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
	            <a href="javascript:void(0);" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
	            <a href="javascript:void(0);" onclick="initSearch()" class="search-area-ext d-inline-block btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
	            <a href="javascript:void(0);" onclick="gx.Download('주문내역.csv');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </div>
</form>

<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <div class="box">
                        <div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
                            <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input" checked>
                            <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<style> /* 상품 이미지 사이즈 강제 픽스 */ .img { height:20px; } </style>
<script type="text/javascript" charset="utf-8">
    const columns = [
        {headerName: '#', width: 40, maxWidth: 100, valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellClass: 'hd-grid-code', pinned: 'left'},
        {field: "ord_no", headerName: "주문번호", width: 140, cellStyle: StyleOrdNo, type: 'OrderNoType', cellClass: 'hd-grid-code', pinned: 'left'},
        {field: "ord_opt_no", headerName: "일련번호", sortable: "true", cellClass: 'hd-grid-code', pinned: 'left',
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openOrder(\'' + params.data.ord_no + '\',\'' + params.value +'\');">' + params.value + '</a>';
                }
            }
        },
        {field: "ord_state", headerName: "주문상태", cellStyle: StyleOrdState, pinned: 'left'},
        {field: "clm_state", headerName: "클레임상태", cellStyle: StyleClmState, pinned: 'left'},
        {field: "pay_stat", headerName: "입금상태", width: 60, cellStyle: StylePayState},
        {field: "goods_type_nm", headerName: "상품구분", width: 60, cellStyle: StyleGoodsType},
        {field: "style_no", headerName: "스타일넘버", width: 70, cellClass: 'hd-grid-code'},
        {field: "img", headerName: "이미지", type: 'GoodsImageType', width: 65},
        {field: "goods_nm", headerName: "상품명", width: 200, type: "GoodsNameType"},
        {field: "opt_val", headerName: "옵션", width: 100},
        {field: "goods_addopt", headerName: "추가옵션"},
        {field: "qty", headerName: "수량", width: 50, type: "numberType"},
        {field: "user_nm", headerName: "주문자(아이디)", width: 100},
        {field: "r_nm", headerName: "수령자", width: 60, cellClass: 'hd-grid-code'},
        {field: "price", headerName: "판매가", type: 'currencyType', width: 60},
        {field: "sale_amt", headerName: "쿠폰/할인", width: 70, type: "numberType"},
        {field: "gift", headerName: "사은품", width: 60},
        {field: "dlv_amt", headerName: "배송비", width: 60, type: "numberType"},
        {field: "pay_fee", headerName: "결제수수료", width: 65, type: "numberType"},
        {field: "pay_type", headerName: "결제방법", width: 80, cellClass: 'hd-grid-code'},
        {field: "fintech", headerName: "간편결제", width: 70},
        {field: "cash_apply_yn", headerName: "현금영수증신청", width: 85, cellClass: 'hd-grid-code'},
        {field: "cash_yn", headerName: "현금영수증발행", width: 85, cellClass: 'hd-grid-code'},
        {field: "ord_type", headerName: "주문구분", width: 60, cellClass: 'hd-grid-code'},
        {field: "ord_kind", headerName: "출고구분", width: 60, cellStyle: StyleOrdKind},
		{field: "sale_place", headerName: "판매처", width: 80, cellClass: 'hd-grid-code'},
		{field: "out_ord_no", headerName: "판매처주문번호", width: 100},
        {field: "baesong_kind", headerName: "배송구분", width: 70, cellClass: 'hd-grid-code'},
        {field: "dlv_type", headerName: "배송방식", width: 80, cellClass: 'hd-grid-code'},
        {field: "dlv_nm", headerName: "택배업체", width: 100},
        {field: "dlv_no", headerName: "송장번호", width: 100},
        {field: "ord_date", headerName: "주문일시", type: "DateTimeType"},
        {field: "pay_date", headerName: "입금일시", type: "DateTimeType"},
        {field: "dlv_end_date", headerName: "배송일시", type: "DateTimeType"},
        {field: "last_up_date", headerName: "클레임일시", type: "DateTimeType"},
        {field: "goods_no", headerName: "goods_no", hide: true},
        {field: "goods_sub", headerName: "goods_sub", hide: true},
        {field: "img", headerName: "goods_img", hide: true},
        {field: "goods_type", headerName: "goods_type", hide: true},
        {field: "level", headerName: "level", hide: true},
        {field: "sms_name", headerName: "order_name", hide: true},
        {field: "sms_mobile", headerName: "order_mobile", hide: true},
		{width: "auto"}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;
	
    $(document).ready(function () {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
		const gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
		
        Search();
	    
        $("#chk_to_class").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
        });
    });
	
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/order/ord01/search', data, 1);
    }

    $(document).ready(function() {
        document.search.user_id.onkeyup	= checkNotUseDate;
        document.search.user_nm.onkeyup	= checkNotUseDate;
        document.search.ord_no.onkeyup	= checkNotUseDate;
        document.search.r_nm.onkeyup	= checkNotUseDate;
        document.search.cols.onchange	= checkNotUseDate;
        document.search.key.onkeyup		= checkNotUseDate;
    });

    function IsNotUseDate()
    {
        var ff = document.search;
        var is_not_use_date = false;

        // 주문번호, 회원아이디, 주문자, 수령자, 주문자핸드폰/전화, 수령자 핸드폰 일때 날짜 검색 무시

        if( ff.user_id.value != "" )
            is_not_use_date = true;
        else if( ff.user_nm.value != "" )
            is_not_use_date = true;
        else if( ff.ord_no.value != "" )
            is_not_use_date = true;
        else if( ff.r_nm.value.length >= 2 )
            is_not_use_date = true;
        else if(ff.cols.value == "b.mobile" && ff.key.value.length >= 8)
            is_not_use_date = true;
        else if(ff.cols.value == "b.phone" && ff.key.value.length >= 8)
            is_not_use_date = true;
        else if(ff.cols.value == "b.r_mobile" && ff.key.value.length >= 8)
            is_not_use_date = true;

        return is_not_use_date;
    }


    function checkNotUseDate()
    {
        if( IsNotUseDate() )
        {
            $('#s_nud').prop("checked", false);
        }
        else
        {
            $('#s_nud').prop("checked", true);
        }
        ManualNotUseData();
    }

    function ManualNotUseData()
    {
        if( $("[name=s_nud]").is(":checked") == true )
        {
            $("[name=sdate]").prop("disabled", false);
            $("[name=edate]").prop("disabled", false);
        }
        else
        {
            $("[name=sdate]").prop("disabled", true);
            $("[name=edate]").prop("disabled", true);
        }
    }


</script>
@stop

