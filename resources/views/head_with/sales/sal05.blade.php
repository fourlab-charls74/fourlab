@extends('head_with.layouts.layout')
@section('title','업체별 매출 통계')
@section('content')

    <div class="page_tit">
        <h3 class="d-inline-flex">업체별 매출 통계</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 업체별 매출 통계</span>
        </div>
    </div>
    <form method="get" name="search" id="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">매출일자</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-email-input">매출시점</label>
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="ord_state" value="10" id="ord_state10" class="custom-control-input" checked="">
                                        <label class="custom-control-label" for="ord_state10">출고요청</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="ord_state" value="30" id="ord_state30" class="custom-control-input">
                                        <label class="custom-control-label" for="ord_state30">출고완료</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
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
                                            <input type='hidden' name='com_cd' id='com_cd' value=''>
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%;">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputCity">품목</label>
                                <div class="flax_box">
                                    <select name='item' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($items as $item)
                                            <option value='{{ $item->cd }}'>{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputState">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputZip">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="row">
						<div class="col-lg-8 inner-td">
							<div class="form-group">
								<label for="formrow-inputState">주문구분</label>
								<div class="form-inline form-check-box">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[0]" id="ord_type_5" value="5" checked>
										<label class="custom-control-label" for="ord_type_5">교환</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[1]" id="ord_type_4" value="4" checked>
										<label class="custom-control-label" for="ord_type_4">예약</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[2]" id="ord_type_3" value="3" checked>
										<label class="custom-control-label" for="ord_type_3">특별주문</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[3]" id="ord_type_13" value="13" checked>
										<label class="custom-control-label" for="ord_type_13">도매주문</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[4]" id="ord_type_12" value="12" checked>
										<label class="custom-control-label" for="ord_type_12">서비스</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[5]" id="ord_type_17" value="17" checked>
										<label class="custom-control-label" for="ord_type_17">기관납품</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[6]" id="ord_type_14" value="14" checked>
										<label class="custom-control-label" for="ord_type_14">수기</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[7]" id="ord_type_15" value="15" checked>
										<label class="custom-control-label" for="ord_type_15">정상</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ord_type[8]" id="ord_type_16" value="16" checked>
										<label class="custom-control-label" for="ord_type_16">오픈마켓</label>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="width:100%;height:calc(100vh - 370px);" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-0">
        <div class="card-body">
            <div class="card-title">
                <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
            </div>
            <ul class="mb-0">
                <li>매출액 = 과세 + 비과세</li>
                <li>매출원가 = 실제판매원가</li>
                <li>부가세 = 과세 - ( 과세 / 1.1 )</li>
                <li>세전 매출이익 = 매출액 - 매출원가</li>
            </ul>
        </div>
    </div>
    <script language="javascript">
        var columns = [
            {headerName: "업체", field: "com_nm",pinned:'left',aggSum:"합계",aggAvg:"평균"},
            {headerName: '매출액구분',
                children: [
                    {headerName: "수량", field: "sum_qty",type:'numberType',aggregation:true},
                    {headerName: "적립금", field: "sum_point_amt",type:'currencyType',aggregation:true},
                    {headerName: "할인", field: "sum_dc_amt",type:'currencyType',aggregation:true},
                    {headerName: "쿠폰", field: "sum_coupon_amt",type:'currencyType',aggregation:true},
                    {headerName: "수수료", field: "sum_fee_amt",type:'currencyType',aggregation:true},
                    {headerName: "결제금액", field: "sum_recv_amt",type:'currencyType',aggregation:true},
                    {headerName: "과세", field: "sum_taxation_amt",type:'currencyType',aggregation:true},
                    {headerName: "비과세", field: "sum_taxfree",type:'currencyType',aggregation:true},
                ]
            },
            {headerName: "부가세", field: "vat",type:'currencyType',aggregation:true},
            {headerName: "매출액", field: "sum_amt",type:'currencyType',aggregation:true},
            {headerName: "매출원가", field: "wonga_60",type:'currencyType',aggregation:true},
            {headerName: "마진율", field: "margin",type:'percentType',
                valueGetter:function(params){
                    if(params.data.goods_nm === "합계" || params.data.goods_nm === "평균"){
                        const data = params.data;
                        return (1- parseInt(data.sum_wonga) / ( parseInt(data.sum_recv_amt) + parseInt(data.sum_point_amt) - parseInt(data.sum_fee_amt) )) * 100;
                    }
                    return params.data.margin;
                }},
            {headerName: '매출이익',
                children: [
                    {headerName: "세전", field: "margin1",type:'currencyType',aggregation:true},
                    {headerName: "세후", field: "margin2",type:'currencyType',aggregation:true},
                ]
            },
            {headerName: '판매',
                children: [
                    {headerName: "수량", field: "sum_qty",type:'numberType',aggregation:true},
                    {headerName: "적립금", field: "point_amt_10",type:'currencyType',aggregation:true},
                    {headerName: "할인", field: "dc_amt_10",type:'currencyType',aggregation:true},
                    {headerName: "쿠폰", field: "coupon_amt_10",type:'currencyType',aggregation:true},
                    {headerName: "수수료", field: "fee_amt_10",type:'currencyType',aggregation:true},
                    {headerName: "결제금액", field: "sum_wonga",type:'currencyType',aggregation:true},
                ]
            },
            {headerName: '교환',
                children: [
                    {headerName: "수량", field: "qty_60",type:'numberType',aggregation:true},
                    {headerName: "적립금", field: "point_amt_60",type:'currencyType',aggregation:true},
                    {headerName: "할인", field: "dc_amt_60",type:'currencyType',aggregation:true},
                    {headerName: "쿠폰", field: "coupon_amt_60",type:'currencyType',aggregation:true},
                ]
            },
        ];

    </script>
    <script type="text/javascript" charset="utf-8">
        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(430);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            Search();
        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Aggregation({
                "sum":"top",
                "avg":"top"
            });
            gx.Request('/head/sales/sal05/search', data);
        }
    </script>



@stop
