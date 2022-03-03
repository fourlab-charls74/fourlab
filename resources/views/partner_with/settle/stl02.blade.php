@extends('partner_with.layouts.layout')
@section('title','마감내역')
@section('content')

    <div class="page_tit">
        <h3 class="d-inline-flex">마감내역</h3>
    </div>
    <form method="get" name="search">
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
                                <label for="formrow-firstname-input">마감일자</label>
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
                                <label for="formrow-inputZip">판매업체</label>
                                <div class="flax_box">
                                    <select name='com_nm' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($com_nms as $com_nm)
                                            <option value='{{ $com_nm->com_id }}'>{{ $com_nm->com_nm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-email-input">상태</label>
                                <div class="flax_box">
                                    <select name="closed_state" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
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
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="card-title">
                <h6 class="m-0 font-weight-bold text-primary fas fa-info-circle"> Tip</h6>
            </div>
            <ul class="mb-0">
                <li>매출금액 = 판매금액 - 클레임금액 - 할인금액 - 쿠폰금액(업체부담) + 배송비 + 기타정산액</li>
                <li>판매수수료 = 수수료지정 : 판매가격 * 수수료율, 공급가지정 : 판매가격 - 공급가액</li>
                <li>수수료 = 판매수수료 - 할인금액</li>
                <li>정산금액 = 매출금액 - 수수료</li>
                <li>쿠폰금액(본사부담) = 판매촉진비 수수료 매출 신고</li>
                <li>카드수수료 등 수수료 부담의 주체가 귀사에 있으므로 입점업체의 경우 매출 신고 시에 해당 매출금액에 대하여 현금성으로 신고</li>
            </ul>
        </div>
    </div>

    <script language="javascript">
        var columns = [
            {
                headerName: '#',
                width:50,
                maxWidth: 100,
                // it is important to have node.id here, so that when the id changes (which happens
                // when the row is loaded) then the cell is refreshed.
                valueGetter: 'node.id',
                cellRenderer: 'loadingRenderer',
            },
            {field:"", headerName:"마감"},
            {field:"", headerName:"마감일자"},
            {field:"com_nm", headerName:"업체명"},
            {field:"fee", headerName:"수수료지정"},
            {field:"sale_amt", headerName:"판매금액"},
            {field:"clm_amt", headerName:"클레임금액"},
            {field:"dc_amt", headerName:"할인금액"},
            {field:"coupon_amt", headerName:"쿠폰금액(업체부담)"},
            {field:"dlv_amt", headerName:"배송비"},
            {field:"etc_amt", headerName:"기타정산액"},
            {headerName: "매출금액",
            children:  [{field: "sale_net_taxation_amt", headerName: "과세"},
                        {field: "sale_net_taxfree_amt", headerName: "비과세"},
                        {field: "", headerName: "소계"}
                    ]},
            {headerName: "수수료",
            children:  [{field: "sale_fee", headerName: "판매수수료"},
                        {field: "dc_amt", headerName: "할인금액"},
                        {field: "", headerName: "소계"}
                    ]},
            {field:"acc_amt", headerName:"정산금액"},
            {field:"allot_amt", headerName:"쿠폰금액(본사부담)"},
            {field:"pay_day", headerName:"지급일"},
            {headerName: "", field: "nvl"}
        ];
    </script>

    <script type="text/javascript" charset="utf-8">

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;
        $(document).ready(function () {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(300);
            Search();

            $('.search-all').keyup(function(){
                date_use_check();
            });

        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/settle/stl02/search', data, 1, searchCallback);
    }

    function searchCallback(data) {}

    </script>

@stop
