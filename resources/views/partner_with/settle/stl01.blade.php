@extends('partner_with.layouts.layout')
@section('title','정산내역')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">정산내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 정산내역</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">정산일자</label>
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
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<div class="card mb-1">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 inner-td">
                Tip
                <ul>
                    <li>매출금액 = 판매금액(소계) + 배송비 + 기타정산액 + 부담금액(할인쿠폰부담금액)</li>
                    <li>수수료 = 수수료지정 : 판매금액(소계) * 수수료율, 공급가지정 : 판매금액(소계) - 공급가액</li>
                    <li>정산금액 = 매출금액 - 수수료</li>
                </ul>
            </div>
        </div>
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
            pinned: 'left',
            cellRenderer: (params) => {
                if (params.node.rowPinned != 'top') return parseInt(params.value) + 1;
            }
        },
        {headerName: "정산일자", field: "date",width:120,cellClass:'hd-grid-code',pinned:'left',aggSum:"합계",
            cellRenderer: function(params) {
                if (params.node.rowPinned == 'top') return params.value;
                if (params.value !== undefined) {
                    return '<a target="_blank" href="/partner/settle/stl01/' + params.data.idx + '" rel="noopener">' + params.value + '</a>';
                }
            }
        },
        {headerName: "업체명", field: "com_nm",width:120,cellClass:'hd-grid-code',pinned:'left'},
        {headerName: "수수료지정", field: "margin_type",width:120,cellClass:'hd-grid-code',pinned:'left'},
        {headerName: '판매금액',
            children: [
                {headerName: "판매", field: "sale_amt",type:'currencyType',aggregation:true},
                {headerName: "클레임", field: "clm_amt",type:'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "coupon_amt",type:'currencyType',aggregation:true},
                {headerName: "소계", field: "sale_clm_cpn_amt",type:'currencyType',aggregation:true},
            ]
        },
        {headerName: "배송비", field: "dlv_amt",type:'currencyType',aggregation:true},
        {headerName: "부담금액", field: "allot_amt",type:'currencyType',aggregation:true},
        {headerName: "기타정산금액", field: "etc_amt",type:'currencyType',aggregation:true},
        {headerName: "매출금액", field: "sale_net_amt",type:'currencyType',aggregation:true},
        {headerName: "수수료", field: "fee",type:'currencyType',aggregation:true},
        {headerName: "정산금액", field: "acc_amt",type:'currencyType',aggregation:true},
        {headerName: "지급일", field: "pay_date",cellClass:'hd-grid-code'},
        {headerName: "마감여부", field: "closed_yn",cellClass:'hd-grid-code'},
        {headerName: "세금계산서", field: "tax_state",cellClass:'hd-grid-code'},
        {headerName: "", field: "", width: "auto"}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(300);
        let gridDiv = document.querySelector(pApp.options.gridId);
        const options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            }
        };
        gx = new HDGrid(gridDiv, columns, options);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
        });
        gx.Request('/partner/settle/stl01/search', data);
    }
</script>
@stop
