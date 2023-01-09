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
                                    <option value="1" selected>입금</option>
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
                <div class="fr_box d-flex">
                    <div class="d-flex">
                        <span class="mr-2">출고차수 :</span>
                        <select id='exp_rel_order' name='exp_rel_order' class="form-control form-control-sm mr-2"  style='width:90px;'>
                            @foreach ($rel_orders as $rel_order)
                                <option value='{{ $rel_order->code_val }}'>{{ $rel_order->code_val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="javascript:void(0);" onclick="return receiptOrder();" class="btn btn-sm btn-primary shadow-sm">온라인주문접수</a>
                </div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
    let dlv_locations = [];

    let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: (params) => params.node.level === 0, headerCheckboxSelection: true, sort: null, width: 28},
        {field: "ord_no", headerName: "주문번호", pinned: 'left', width: 135,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
            cellRenderer: (params) => {
                if (params.node.level != 0) return '';
                let ord_no = params.data ? params.data.ord_no : params.node.aggData ? params.node.aggData.ord_no : '';
                let ord_opt_no = params.data ? params.data.ord_opt_no : params.node.aggData ? params.node.aggData.ord_opt_no : '';
                return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + ord_no + '\',\'' + ord_opt_no +'\');">'+ params.value +'</a>';
            }
        },
        {field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 60, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
            cellRenderer: (params) => {
                if (params.node.level != 0) return '';
                let ord_no = params.data ? params.data.ord_no : params.node.aggData ? params.node.aggData.ord_no : '';
                let ord_opt_no = params.data ? params.data.ord_opt_no : params.node.aggData ? params.node.aggData.ord_opt_no : '';
                return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + ord_no + '\',\'' + ord_opt_no +'\');">'+ params.value +'</a>';
            }
        },
        {field: "ord_state", headerName: "주문상태코드", hide: true,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
        },
        {field: "ord_state_nm", headerName: "주문상태", pinned: 'left', width: 70, cellStyle: StyleOrdState,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "pay_stat_nm", headerName: "입금상태", pinned: 'left', width: 55, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "goods_no", headerName: "상품번호", width: 70, cellStyle: {'text-align': 'center'}, pinned: "left",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: {'text-align': 'center'}, pinned: "left"},
        {field: "prd_cd_p", headerName: "코드일련", width: 90, cellStyle: {"text-align": "center"}},
        {field: "goods_no_group", rowGroup: true, hide: true},
        // {headerName: "상품번호", width: 100, pinned: 'left', cellStyle: {'text-align': 'center'},
        //     showRowGroup: 'goods_no_group', 
        //     cellRenderer: 'agGroupCellRenderer', 
        // },
        {field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: {'text-align': 'center'}, 
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "goods_nm", headerName: "상품명", width: 150,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
            cellRenderer: function (params) {
                if (params.data?.prd_cd === '합계' || params.node.level != 0) return '';
				if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
					return params.value;
				} else {
					let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
					return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + params.value + '</a>';
				}
			}
        },
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}, 
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}, 
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "goods_opt", headerName: "옵션", width: 130, 
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "qty", headerName: "수량", width: 50, type: "currencyType", aggFunc: "first"},
        @foreach (@$dlv_locations as $loc)
            {field: "{{ $loc->seq }}_{{ $loc->location_type }}_{{ $loc->location_cd }}_qty", headerName: "{{ $loc->location_nm }}", width: 100, type: "currencyType",
                cellStyle: (params) => {
                //     // const qtys = Object.keys(params.data)
                //     //     .filter(k => k.indexOf('_qty') >= 0)
                //     //     .map(k => ({key: k, value: params.data[k], seq: k.split("_")[0]}));
                //     // 로직작업 필요
                    if (!params.data) return '';
                    return params.value == Math.max(...Object.keys(params.data).filter(k => k.indexOf('_qty') >= 0).map(k => params.data[k])) 
                        ? ({ "background-color": "#FFDFDF" }) : '';
                }
            },
        @endforeach
        {field: "dlv_place_type", headerName: "배송처타입", hide: true},
        {field: "dlv_place_cd", headerName: "배송처코드", hide: true},
        {field: "dlv_place", headerName: "배송처", width: 130, editable: true, cellStyle: (params) => ({'background-color': params.node.level == 0 ? '#ffff99' : params.node.level == 1 ? '#eeeeee' : 'none'})},
        {field: "comment", headerName: "접수메모", width: 120, editable: true, cellStyle: (params) => ({'background-color': params.node.level == 0 ? '#ffff99' : params.node.level == 1 ? '#eeeeee' : 'none'})},
        {field: "user_nm", headerName: "주문자(아이디)", width: 120, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "r_nm", headerName: "수령자", width: 70, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "wonga", headerName: "원가", width: 60, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "goods_sh", headerName: "TAG가", width: 60, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "goods_price", headerName: "자사몰판매가", width: 85, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "price", headerName: "판매가", width: 60, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "sale_kind_nm", headerName: "판매유형", width: 100, cellStyle: {"text-align": "center"},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "pr_code_nm", headerName: "행사구분", width: 60, cellStyle: {"text-align": "center"},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "dlv_amt", headerName: "배송비", width: 60, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "sales_com_fee", headerName: "판매수수료", width: 80, type: "currencyType",
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "pay_type_nm", headerName: "결제방법", width: 80, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "ord_type_nm", headerName: "주문구분", width: 60, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "ord_kind", headerName: "출고구분코드", hide: true,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
        },
        {field: "ord_kind_nm", headerName: "출고구분", width: 60, cellStyle: StyleOrdKind,
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "baesong_kind", headerName: "배송구분", width: 60, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "sale_place_nm", headerName: "판매처", width: 100, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "ord_date", headerName: "주문일시", width: 125, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "pay_date", headerName: "입금일시", width: 125, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "dlv_end_date", headerName: "배송일시", width: 125, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
        {field: "last_up_date", headerName: "클레임일시", width: 125, cellStyle: {'text-align': 'center'},
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
        },
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
            defaultColDef: {
                suppressMenu: true,
                resizable: false,
                autoHeight: true,
                suppressSizeToFit: false,
                sortable:true,
            },
            rollup: true,
            rollupCountLevel: 1,
			groupSuppressAutoColumn: true,
            groupDefaultExpanded: 1, // 0: close, 1: open
			suppressAggFuncInHeader: true,
			animateRows: true,
            onCellValueChanged: (e) => {
                if (e.column.colId === "dlv_place") {
                    let arr = dlv_locations.filter(s => s.location_nm === e.newValue);
                    if(arr.length > 0) {
                        e.data.dlv_place_cd = arr[0].location_cd;
                        e.data.dlv_place_type = arr[0].location_type;
                        if (e.node.parent.level >= 0) {
                            e.node.parent.allLeafChildren
                                .filter(c => c.data?.prd_cd !== e.data.prd_cd)
                                .forEach(c => {
                                    c.setDataValue('dlv_place', null);
                                    c.setDataValue('dlv_place_cd', null);
                                    c.setDataValue('dlv_place_type', null);
                                });

                            e.node.parent.aggData.dlv_place = arr[0].location_nm;
                            e.node.parent.aggData.dlv_place_cd = arr[0].location_cd;
                            e.node.parent.aggData.dlv_place_type = arr[0].location_type;
                            e.node.parent.aggData.prd_cd = e.node.data.prd_cd;
                            e.node.parent.aggData.prd_cd_p = e.node.data.prd_cd_p;
                            e.node.parent.aggData.comment = e.node.data.comment;
                            e.api.redrawRows({ rowNodes:[e.node, e.node.parent] });
                        } else {
                            e.api.redrawRows({ rowNodes:[e.node] });
                        }
                    }
                    e.node.data.goods_no_group === null ? e.node.setSelected(true) : e.node.parent.setSelected(true);
                }
                if (e.column.colId === "comment") {
                    if (e.node.parent.level >= 0) {
                        // let children = e.node.parent.allLeafChildren.filter(c => c.data?.prd_cd !== e.data.prd_cd);
                        // gx.gridOptions.api.applyTransaction({ update: children.map(c => ({...c.data, comment: null})) });

                        e.node.parent.aggData.comment = e.newValue;
                        e.node.parent.aggData.dlv_place = e.node.data.dlv_place;
                        e.node.parent.aggData.dlv_place_cd = e.node.data.dlv_place_cd;
                        e.node.parent.aggData.dlv_place_type = e.node.data.dlv_place_type;
                        e.node.parent.aggData.prd_cd = e.node.data.prd_cd;
                        e.node.parent.aggData.prd_cd_p = e.node.data.prd_cd_p;
                        e.api.redrawRows({ rowNodes:[e.node.parent] });
                    }
                    e.node.data.goods_no_group === null ? e.node.setSelected(true) : e.node.parent.setSelected(true);
                }
            },
            isRowSelectable: (params) => {
                return params.aggData || params.data?.goods_no_group === null;
            },
        });

		Search();
	});
	
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/order/ord02/search', data, 1, function(d) {
            dlv_locations = d.head.dlv_locations;
            setDlvPlaceListOptions(d.head.dlv_locations);
        });
	}

    // 배송처 초기화
    function setDlvPlaceListOptions(places) {
        let dlv_places = columns.map(c => c.field === 'dlv_place' ? ({
            ...c, 
            cellEditorSelector: function(params) {
                return {
                    component: 'agRichSelectCellEditor',
                    params: { 
                        values: places.map(s => s.location_nm)
                    },
                };
            },
        }) : c);
        gx.gridOptions.api.setColumnDefs(dlv_places);
	}

    // 주문접수
    function receiptOrder() {
        let rows = [];
        gx.gridOptions.api.forEachNode(function(node) {
            if ((node.aggData || node.data?.goods_no_group === null) && node.selected) {
                rows.push(node.aggData || node.data);
            }
        });

        if(rows.filter(r => r.ord_state != 10).length > 0) return alert("출고요청 상태의 주문건만 접수가 가능합니다.");
        if(rows.filter(r => r.ord_kind > 20).length > 0) return alert("출고보류중인 주문건은 접수할 수 없습니다.");
        // if(rows.filter(r => r.qty > ???).length > 0) return alert("재고가 부족한 상품이 있습니다.\n확인 후 다시 접수해주세요.");
        // console.log(rows);

        if(rows.length < 1) return alert("접수할 주문건을 선택해주세요.");
        if(!confirm("선택한 주문건을 접수하시겠습니까?")) return;

        axios({
            url: '/store/order/ord02/receipt',
            method: 'post',
            data: { 
                rel_order: $("#exp_rel_order").val(),
                data: rows
            },
        }).then(function (res) {
            console.log(res);
            if(res.data.code === 200) {
                // alert(res.data.msg);
                // location.href = "/store/stock/stk20";
            } else {
                console.log(res.data);
                alert("온라인주문접수 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop
