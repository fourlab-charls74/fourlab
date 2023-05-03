@extends('head_with.layouts.layout-nav')
@section('title','상품원가')
@section('content')

<style>
    .strong {
        font-weight: bold;
        letter-spacing: 0.15em;
    }
</style>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품원가</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품원가</span>
            </div>
        </div>
        <div>
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm" onclick="window.close()"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <div class="card_wrap aco_card_wrap">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <tr>
                                    <th>상품명</th>
                                    <td>{{ @$goods_nm }}</td>
                                </tr>
                                <tr>
                                    <th>업체</th>
                                    <td>{{ @$com_nm }}</td>
                                </tr>
                                <tr>
                                    <th>판매가</th>
                                    <td>{{ number_format(@$price ?? 0) }}원</td>
                                </tr>
                                <tr>
                                    <th>평균원가</th>
                                    <td>현재고 총원가 ( <strong class="strong">{{ number_format(@$sum_wonga) }}</strong> ) / 현재고 ( <strong class="strong">{{ number_format(@$avail_qty) }}</strong> ) = <strong class="strong text-danger">{{ number_format(@$avg_wonga) }}</strong>원</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-none mb-0 pt-0 ty2 last-card">
            <div class="card-body border-0">
                <div class="card-title mt-2 mb-3 ml-2">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 500px);max-height:500px;width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let gx; 
    const pApp = new App('', { gridId: "#div-gd" });
    const goods_no = '{{ @$goods_no }}';
    const goods_sub = '{{ @$goods_sub }}';
    const price = '{{ @$price }}';

    let columns = [
        { headerName: '#', valueGetter: 'node.id', cellRenderer: 'loadingRenderer', width: 40, cellStyle: { 'text-align': 'center' } },
        { field: "invoice_no", headerName: "송장번호", width: 130, cellClass:'hd-grid-number', cellStyle: { 'text-align': 'center' } },
        { field: "regi_date", headerName: "입고일자", width: 80, cellStyle: { 'text-align': 'center' } },
        { field: "com_nm", headerName: "공급처", width: 100, cellStyle: { 'text-align': 'center' } },
        { field: "h_wonga", headerName: "원가", type: "numberType", width: 80 },
        { field: "qty", headerName: "수량", type: "numberType", width: 80 },
        { field: "goods_opt", headerName: "옵션", width: 200 },
        { field: "margin", headerName: "마진율(%)", width: 80, cellClass: 'hd-grid-number' },
        { width: "auto" }
    ];

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        let data = 'goods_no=' + goods_no + '&goods_sub=' + goods_sub + '&price=' + price;
        gx.Request('/head/product/prd03/wonga_search', data, 1);
    });
</script>

@stop
