@extends('head_with.layouts.layout')
@section('title','회원별 주문 통계')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">회원별 주문 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원별 주문 통계</span>
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
                            <label for="formrow-firstname-input">일자</label>
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
                            <label for="type">주문자/아이디</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="user_nm" value="">
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="user_id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">그룹</label>
                            <div class="flax_box">
                                <select name='user_group' class="form-control form-control-sm">
                                    <option value=''>회원그룹</option>
                                    @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->val}}</option>
                                    @endforeach
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
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
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
<script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
<script language="javascript">
    var columns = [{
            headerName: "아이디",
            field: "user_id",
            width: 100,
            pinned: 'left',
            aggSum: "합계",
            aggAvg: "평균",
            cellRenderer: function(params){
                if(params.value !== undefined){
                    if( params.value === '합계' || params.value === '평균' )
                        return params.value;
                    else if( params.data.user_id != "" )
                        return '<a href="#" onclick="return openUserEdit(\'' + params.data.user_id + '\');">'+ params.value +'</a>';
                    else
                        return params.value;
                }
            }
        },
        {
            headerName: "이름",
            field: "name",
            width: 100,
            pinned: 'left'
        },
        {
            headerName: "휴대전화",
            field: "mobile",
            width: 100,
            cellClass: 'hd-grid-code',
            pinned: 'left'
        },
        {
            headerName: "적립금",
            field: "point",
            type: 'numberType',
            pinned: 'left',
            aggregation: true
        },
        {
            headerName: "가입일",
            field: "regdate",
            cellClass: 'hd-grid-code',
            pinned: 'left'
        },
        {
            headerName: "최근로그인",
            field: "lastdate",
            cellClass: 'hd-grid-code',
            pinned: 'left'
        },
        {
            headerName: "로그인횟수",
            field: "visit_cnt",
            type: 'numberType',
            pinned: 'left',
            aggregation: true
        },
        {
            headerName: '매출(순판매)',
            children: [{
                    headerName: "수량",
                    field: "net_opt_cnt",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "금액",
                    field: "net_amt",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '결제',
            children: [{
                    headerName: "건수",
                    field: "pay_cnt",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "수량",
                    field: "pay_opt_cnt",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "금액",
                    field: "pay_amt",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '배송',
            children: [{
                    headerName: "수량",
                    field: "dlv_opt_cnt",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "금액",
                    field: "dlv_amt",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '교환',
            children: [{
                    headerName: "수량",
                    field: "ret_opt_cnt",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "금액",
                    field: "ret_amt",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '환불',
            children: [{
                    headerName: "수량",
                    field: "ref_opt_cnt",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "금액",
                    field: "ref_amt",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let chart_data = null;

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(265);
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
        Search();
    });

    function Search() {
        $('[name=ord_state]').val(10);

        let data = $('form[name="search"]').serialize();

        gx.Aggregation({
            "sum": "top",
            "avg": "top"
        });

        gx.Request('/head/sales/sal25/search', data, -1, function(data) {
            chart_data = data.body;
        });
    }
</script>

@stop
