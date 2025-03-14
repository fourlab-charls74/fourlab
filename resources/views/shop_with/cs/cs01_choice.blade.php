@extends('shop_with.layouts.layout-nav')
@section('title','입고')
@section('content')
<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">입고</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 생산입고관리</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="Search()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="formrow-firstname-input">입고일자</label>
                                <div class="form-inline">
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
                                            <input type="text" class="form-control form-control-sm docs-date search-enter" name="edate" value="{{ $edate }}" autocomplete="off">
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="invoice_no">송장번호</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm search-all search-enter" id="invoice_no" name="invoice_no" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="order_stock_state">입고상태</label> 
                                <div class="flex_box">
                                    <select name="order_stock_state" class="form-control form-control-sm w-100">
                                        <option value="">모두</option>
                                        @foreach ($order_stock_states as $order_stock_state)
                                            <option value="{{ $order_stock_state->code_id }}">{{ $order_stock_state->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">공급업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-100">
                                        <div class="form-inline inline_btn_box">
                                            <input type="hidden" id="com_cd" name="com_cd">
                                            <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item">품목</label>
                                <div class="flex_box">
                                    <select id="item" name="item" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="user_name">입고자</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm search-all search-enter" name="user_name" id="user_name" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            </div>
        </div>
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
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">

    // ag-grid set

    var columns= [
        { headerName: "선택", pinned: "left", cellStyle: {"text-align": "center"},
            cellRenderer: (params) => `<a href='javascript:void(0);' onclick="selectInvoice('${params.data.invoice_no}')">선택</a>`,
        },
        { field: "stock_no", headerName: "입고번호", width: 100, cellStyle:{ 'text-align': 'center' } },
        { field: "invoice_no", headerName: "송장번호", width: 120,
            cellRenderer: (params) => `<a href="#" onClick="clickInvoiceNo(${params.data.stock_no})">${params.data.invoice_no}</a>`,
        },
        { field: "area_type", headerName: "입고지역", width: 100, cellStyle:{ 'text-align': 'center' } },
        { field: "stock_date", headerName: "입고일자", width: 100, cellStyle:{ 'text-align': 'center' } },
        { field: "state_nm", headerName: "입고상태", width: 100, cellStyle:{ 'text-align': 'center' } },
        { field: "com_nm", headerName: "공급업체", width: 110 },
        { field: "item", headerName: "품목", width: 100 },
        { field: "currency_unit", headerName: "화폐단위", width: 110, cellStyle:{ 'text-align': 'center' } },
        { field: "exchange_rate", headerName: "환율", width: 80, cellStyle:{ 'text-align': 'right' }, type:'percentType' },
        { field: "custom_amt", headerName: "신고금액", width: 90, cellStyle:{ 'text-align': 'right' }, type:'percentType' },
        { field: "custom_tax", headerName: "통관비", width: 80, cellStyle:{ 'text-align': 'right' }, type:'currencyType' },
        { field: "custom_tax_rate", headerName: "통관세율(%)", width: 130, cellStyle:{ 'text-align': 'right' }, type:'percentType' },
        { field: "qty", headerName: "수량", type:'currencyType' },
        { field: "total_cost", headerName: "총원가(원)", type:'currencyType' },
        { field: "buy_order_qty", headerName: "발주 후 입고수", width: 120, type:'numberType', },
        { field: "name", headerName: "입고자", width: 80, cellStyle:{ 'text-align': 'center' } },
        { field: "rt", headerName: "최종수정일", width: 150, cellStyle:{ 'text-align': 'center' } },
        { field: "", headerName:"", width: "auto" },
    ];

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275, 430);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {};
        gx = new HDGrid(gridDiv, columns,options);
        Search();

        $("#img").click(function() {
            gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));
        });

    });

    // logics
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/cs/cs01/search', data);
    };

    const clickInvoiceNo = (stock_no) => {
        const cmd = 'edit';
        const url = `/shop/cs/cs01/show?cmd=${cmd}&stock_no=${stock_no}`;
        const [ width, height ] = [ 1867, 1200 ];
        const pop = window.open(url,"_blank","toolbar=no,scrollbars=no,resizable=yes,status=yes,top=100,left=100,width="+width+",height="+height);
    };

    const selectInvoice = (invoice_no) => {
        opener.setInvoiceNo(invoice_no);
        window.close();
    }

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>        
@stop
