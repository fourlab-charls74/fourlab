@extends('head_with.layouts.layout')
@section('title','입출고내역')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">입출고내역</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 입출고내역</span>
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
                                <label for="formrow-firstname-input">일자</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
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
                                    <span class="text_line">~</span>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">품목</label>
                                <div class="flex_box">
                                    <select name="item" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">({{ $item->cd }}){{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_nm">브랜드</label>
                                <div class="form-inline">
                                    <div class="inbox" style="width:50%;">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" class="form-control form-control-sm search-all sch-brand" name="brand_nm" id="brand_nm" value="" style="width:100%;">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-brand" onclick="PopSearchBrand('search');"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                    <input type="text" id="brand_cd" name="brand_cd" class="form-control form-control-sm" readonly style="width:calc(50% - 10px);margin-left:10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">업체</label>
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
                                            <input type="hidden" id="com_cd" name="com_cd">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%;">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm ac-goods-nm search-enter" id="goods_nm" name="goods_nm" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="kind">구분</label>
                                <div class="flex_box">
                                    <select id="kind" name='kind' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($kind as $key => $value)
                                            <option value='{{ $key }}'>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="jaego_type">사유</label>
                                <div class="flex_box">
                                    <select id="jaego_type" name='jaego_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($jaego_types as $jaego_type)
                                            <option value='{{ $jaego_type->code_id }}'>{{ $jaego_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">상품상태</label>
                                <div class="flex_box">
                                    <select id="goods_stat" name='goods_stat' class="form-control form-control-sm">
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="loc">위치</label>
                                <div class="flex_box">
                                    <select id="loc" name='loc' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($locs as $loc)
                                            <option value='{{ $loc->code_id }}'>{{ $loc->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="name">자료/정렬순서</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="100">100</option>
											<option value="500">500</option>
											<option value="1000">1000</option>
											<option value="2000">2000</option>
											<option value=-1>모두</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="a.goods_no" >상품번호</option>
											<option value="a.style_no" >스타일넘버</option>
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
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <form method="post" name="save" action="/head/stock/stk06">
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
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

        // ag-grid
        var columns= [        
            {field:"date", headerName:"일시", pinned:'left', width: 110},
            {field:"kind", headerName:"구분", pinned:'left', cellStyle:{'text-align': 'center'}},
            {field:"type", headerName:"사유", pinned:'left', width: 100},
            {field:"com_nm", headerName:"공급업체", pinned:'left', width: 130},
            {field:"opt_kind_nm", headerName:"품목", pinned:'left', width: 140},
            {field:"brand_nm", headerName:"브랜드", width: 100},
            {field:"style_no", headerName:"스타일넘버", width: 120},
            {field:"goods_type", headerName:"상품구분"},
            {headerName:"상품코드",
                children: [
                    {headerName: "번호", field: "goods_no", width: 46, cellStyle:{'text-align': 'right'}},
                    {headerName: "보조", field: "goods_sub", width: 34, cellStyle:{'text-align': 'right'}}
                ]
            },
            {field:"goods_nm", headerName:"상품명", width: 320, type:'HeadGoodsNameType'},
            {field:"goods_opt", headerName:"옵션", width: 100},
            {field: "qty", headerName: "수량", width:46, type:'numberType',
                cellRenderer: (params) => {
                    if (params.value !== undefined) {
                        return `<a href="javascript:openHeadStock('` + params.data.goods_no + `','` + params.data.goods_opt + `')">` + params.value + `</a>`;
                    }
                }
            },
            {field:"wonga", headerName: "원가", width:60, type: 'currencyType'},
            {field:"loc", headerName:"위치"},
            {field:"invoice_no", headerName:"송장번호", width: 120},
            {field:"ord_no" , headerName:"주문번호", type:'HeadOrderNoType', width:135, cellStyle:{'text-align': 'center'}},
            {field:"etc", headerName:"메모", width: 130},
            {field:"admin_nm", headerName:"처리자", width: 100},
            {field:"ord_opt_no", headerName:"주문일련번호", hide: true},
            {field:"", headerName:"", width:"auto"}
        ];

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;
        
        document.addEventListener('DOMContentLoaded', function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            Search();
        });
        
        // logics
        var Search = function () {
            var data = $('form[name="search"]').serialize();
            gx.Request('/head/stock/stk05/search', data, 1);
        };

        const onChangeDate = (input) => {
            const name = input.name;
            const today = getDateObjToStr(new Date()); // yyyymmdd

            // 오늘 이전의 데이터만 조회 가능
            let value = (input.value).replace(/-/gi, ""); // value is yyyymmdd

            if (value > today) {
                alert("미래의 날짜는 선택할 수 없습니다.");
                document.search.sdate.value = formatStringToDate(calcDate(today, -3, "M"));
                document.search.edate.value = formatStringToDate(today);
                return false;
            }

            // 조회 기간을 3개월로 고정
            if (name == 'sdate' && value.length == 8) {
                const edate = (document.search.edate.value).replace(/-/gi, ""); // y-m-d -> yyyymmdd
                const nn = calcDate(value, 3, "M");
                if (value > edate || edate > nn) {
                    document.search.edate.value = formatStringToDate(nn);
                }
            } else if (name == 'edate' && value.length == 8) {
                const sdate = (document.search.sdate.value).replace(/-/gi, "");
                const nn = calcDate(value, -3, "M");
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
