@extends('store_with.layouts.layout')
@section('title','정산내역')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">정산내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 정산내역</span>
    </div>
</div>

<form method="get" name="search">
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
                            <label for="date_type">매출일자</label>
                            <div class="d-flex">
                                <span class="mr-2">
                                    <div class="form-group">
                                        <select id="date_type" name='date_type' class="form-control form-control-sm" onchange="onChangeDateType(this);" style="width: 80px">
                                            <?php
                                                foreach ($date_types as $type => $value) {
                                                    if ($type == '전월') $value = 'prev';
                                                    echo "<option value='${value}'>${type}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </span>
                                <div class="w-100 d-flex align-items-center">
                                    <div class="docs-datepicker form-inline-inner input_box mr-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" onchange="onChangeDate(this)">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="docs-datepicker-container"></div>
                                    </div>
                                    <span class="text_line mr-2">~</span>
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off" onchange="onChangeDate(this)">
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
                            <label for="state">상태</label>
                            <div class="d-flex align-items-center">
                                <select name="state" id="state" class="form-control form-control-sm w-100 mr-2">
                                    <?php
                                        foreach ($states as $id => $val) {
                                            $selected = ($id == '30') ? 'selected' : '';
                                            echo "<option value='${id}' ${selected}>${val}</option>";
                                        }
                                    ?>
                                </select>
                                <div class="custom-control custom-checkbox form-check-box" style="min-width:110px;">
                                    <input type="checkbox" name="clm_state_ex" id="clm_state_ex" class="custom-control-input" value="Y" />
                                    <label class="custom-control-label" for="clm_state_ex">클레임 매출 제외</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="ord_no">주문번호 / 주문자</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' id="ord_no" class="form-control form-control-sm search-all" name='ord_no' value=''>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm search-all" name='user_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="com_type">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                    <select name="com_type" id="com_type" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach($com_types as $com_type)
                                        <?php $selected = ($com_type->code_id == 2) ? "selected" : ""?>
                                        <option value="{{ $com_type->code_id }}" {{$selected}}>{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" name="com_cd" id="com_cd" value="">
                                        <input type="text" class="form-control form-control-sm sch-company" name='com_nm' id='com_nm' value='' autocomplete='off' readonly style="background-color: white;" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sale_place">판매처</label>
                            <div class="flax_box">
                                <select id="sale_place" name="sale_place" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <?php
                                        collect($sale_places)->map(function($item) {
                                            $com_id = $item->com_id;
                                            $com_nm = $item->com_nm;
                                            echo "<option value='${com_id}'>${com_nm}</option>";
                                        })
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품코드</label>
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
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">주문일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="ord_sdate" value="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="ord_edate" value="" autocomplete="off">
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
                            <label for="formrow-firstname-input">입금일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="pay_sdate" value="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="pay_edate" value="" autocomplete="off">
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
                            <label for="formrow-firstname-input">출고일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="dlv_sdate" value="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="dlv_edate" value="" autocomplete="off">
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
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">환불일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="clm_sdate" value="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="clm_edate" value="" autocomplete="off">
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
                            <label for="ord_type">주문 / 출고구분</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select id="ord_type" name='ord_type' class="form-control form-control-sm">
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
                            <label for="stat_pay_type">결제방법</label>
                            <div class="d-flex align-items-center">
                                <select id="stat_pay_type" name='stat_pay_type' class="form-control form-control-sm w-100 mr-2" style="width:calc(20% - 10px);margin-right:10px;">
                                    <option value=''>전체</option>
                                    @foreach($stat_pay_types as $stat_pay_type)
                                    <option value="{{ $stat_pay_type->code_id }}">{{ $stat_pay_type->code_val }}</option>
                                    @endforeach
                                </select>
                                <div class="form-inline" style="min-width:100px;">
                                    <div class="custom-control custom-checkbox form-check-box">
                                        <input type="checkbox" name="not_complex" id="not_complex_y" class="custom-control-input" value="Y" />
                                        <label class="custom-control-label" for="not_complex_y">복합결제 제외</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select id="item" name="item" class="form-control form-control-sm">
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id="goods_nm" name='goods_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->
<form method="post" name="save" action="/store/stock/stk01">
    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
        <div class="card-body">
            <div class="card-title mb-3">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript" charset="utf-8">

    // style 적용시 aggSum을 항상 케이스 분류하여 적용해야함
    const numberStyle = (params) => {
        const value = parseInt(params.value);
        if (params.node.rowPinned === 'top') return { 'color' : 'black' };
        return (value >= 0)
            ? { 'color' : 'black' }
            : { 'color' : 'red' }
    };

    const CELL_COLOR = {
        YELLOW: { 'background' : '#ffff99' },
        GREEN: { 'background' : '#C5FF9D' }
    };

    const colorGrouping = (params) => {
        if (params.node.rowPinned === 'top') {
            return { 'background': '#eee' }
        }
        if (params.data.is_group == false) return {};
        const is_green = params.data.is_green;
        return is_green ? CELL_COLOR.GREEN : CELL_COLOR.YELLOW;
    };

    /**
     * ag grid columns
     */
    
    var columns= [
        {field:"ord_state_date" ,headerName:"매출일자",pinned:'left', width:120, aggSum: "합계"},
        {field:"ord_date" ,headerName:"주문일시",pinned:'left', width:150},
        {field:"pay_date" ,headerName:"입금일시",pinned:'left', width:150},
        {field:"dlv_end_date" ,headerName:"출고일시",pinned:'left', width:150},
        {field:"clm_date", headerName:"클레임일시", pinned:'left', width:150},
        {field:"ord_no", headerName:"주문번호", width:150, cellStyle: (params) => colorGrouping(params)},
        {field:"ord_opt_no", headerName:"일련번호", type: 'HeadOrderNoType', width: 100},
        {field:"user_nm", headerName:"주문자"},
        {headerName: '상품번호', width: 120,
            children: [{
                    headerName: "",
                    field: "goods_no",
                    width: 80
                },
                {
                    headerName: "",
                    field: "goods_sub",
                    width: 50
                },
            ]
        },
		{field:"goods_nm", headerName: "상품명", width:170, type:"HeadGoodsNameType"},
		{field:"ord_state", headerName: "주문상태", width:90, cellStyle:StyleOrdState},
		{field:"ord_type", headerName: "주문구분"},
		{field:"ord_kind", headerName: "출고구분", width: 90, cellStyle:StyleOrdKind},
		{field:"pay_nm", headerName: "결제", width: 110},
		{field:"qty", headerName: "수량"},
		{field:"price", headerName: "판매가", type: 'currencyType', aggregation: true},
		{field:"amt", headerName: "판매금액", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"point_apply_amt", headerName: "적립금", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"coupon_apply_amt", headerName: "쿠폰금액", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"coupon_com_amt", headerName: "쿠폰(업체부담)", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"coupon_allot_amt", headerName: "쿠폰(당사부담)", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"dlv_amt", headerName: "배송비", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"dlv_ret_amt", headerName: "환불배송비", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"ret_amt", headerName: "환불금액", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"sale_place", headerName: "판매처"},
		{field:"com_type", headerName: "업체구분"},
        {field:"com_nm", headerName: "업체", width: 130},
		{field:"fee", headerName: "수수료율"},
		{field:"sale_amt", headerName: "매출액", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
		{field:"cal_acc_amt", headerName: "정산액", type: 'currencyType', cellStyle: numberStyle, aggregation: true},
        { width:"auto" }
    ];

    /**
     * ag grid init
     */

    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            }
        };
        gx = new HDGrid(gridDiv, columns, options);
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum": "top"
        });
        gx.Request('/store/account/acc01/search', data, -1);
    }

    /**
     * logics
     */

    const onChangeDateType = (obj) => {
        const date_types = <?=json_encode($date_types, JSON_UNESCAPED_SLASHES)?>;
        if (obj.value == 'prev') {
            document.search.sdate.value = date_types['전월'].start;
            document.search.edate.value = date_types['전월'].end;
        } else if (obj.value == 0) {
            return false;
        } else {
            const today = getDateObjToStr(new Date()); // yyyymmdd
            document.search.sdate.value = obj.value;
            document.search.edate.value = formatStringToDate(today);
        }
    };

    const onChangeDate = (input) => {
        const name = input.name;
        const today = getDateObjToStr(new Date()); // yyyymmdd

        // 오늘 이전의 데이터만 조회 가능
        let value = (input.value).replace(/-/gi, ""); // value is yyyymmdd

        if (value > today) {
		    alert("미래의 날짜는 선택할 수 없습니다.");
            document.search.sdate.value = formatStringToDate(calcDate(today, -1, "M"));
            document.search.edate.value = formatStringToDate(today);
            return false;
	    }

        // 조회 기간을 한달로 고정
        if (name == 'sdate' && value.length == 8) {
            const edate = (document.search.edate.value).replace(/-/gi, ""); // y-m-d -> yyyymmdd
            const nn = calcDate(value, 1, "M");
            if (value > edate || edate > nn) {
                document.search.edate.value = formatStringToDate(nn);
            }
        } else if (name == 'edate' && value.length == 8) {
            const sdate = (document.search.sdate.value).replace(/-/gi, "");
            const nn = calcDate(value, -1, "M");
            if (value < sdate || sdate < nn) {
                document.search.sdate.value = formatStringToDate(nn);
            }
        }
    };

    const formatDateToString = (date) => {
        return date.replace("-", "");
    }

    const formatStringToDate = (string) => {
        const y = string.substr(0,4);
        const m = string.substr(4,2);
        const d = string.substr(6,2);
        return `${y}-${m}-${d}`;
    };

    /*
        Function: getDateObjToStr
            날짜를 YYYYMMDD 형식으로 변경

        Parameters:
            date - date object

        Returns:
            date string "YYYYMMDD"
    */

    function getDateObjToStr(date){
        var str = new Array();

        var _year = date.getFullYear();
        str[str.length] = _year;

        var _month = date.getMonth()+1;
        if(_month < 10) _month = "0"+_month;
        str[str.length] = _month;

        var _day = date.getDate();
        if(_day < 10) _day = "0"+_day;
        str[str.length] = _day
        var getDateObjToStr = str.join("");

        return getDateObjToStr;
    }

    /*
        Function: calcDate
        데이트 계산 함수

        Parameters:
            date - string "yyyymmdd"
            period - int
            period_kind - string "Y","M","D"
            gt_today - boolean

        Returns:
            calcDate("20080205",30,"D");
    */

    function calcDate(date,period, period_kind,gt_today){

        var today = getDateObjToStr(new Date());

        var in_year = date.substr(0,4);
        var in_month = date.substr(4,2);
        var in_day = date.substr(6,2);

        var nd = new Date(in_year, in_month-1, in_day);
        if(period_kind == "D"){
            nd.setDate(nd.getDate()+period);
        }
        if(period_kind == "M"){
            nd.setMonth(nd.getMonth()+period);
        }
        if(period_kind == "Y"){
            nd.setFullYear(nd.getFullYear()+period);
        }
        var new_date = new Date(nd);
        var calcDate = getDateObjToStr(new_date);
        if(! gt_today){ // 금일보다 큰 날짜 반환한다면
            if(calcDate > today){
                calcDate = today;
            }
        }
        return calcDate;
    }

</script>


@stop
