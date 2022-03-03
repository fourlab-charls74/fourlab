@extends('partner_with.layouts.layout')
@section('title','정산상세내역')
@section('content')

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">정산상세내역</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 정산내역</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="gx.Download();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">자료받기</a>
        </div>
    </div>
    <form>
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>정산일자</th>
                                                <td>
                                                    <div class="form-inline inline_input_box">
                                                        <div class="docs-datepicker form-inline-inner">
                                                            <div class="input-group">
                                                                {{$acc_info->sday}} ~ {{$acc_info->eday}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <th>업체</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            {{$acc_info->com_id}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <th>마감여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            {{$acc_info->closed_yn}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>등록일</th>
                                                <td>
                                                    <div class="form-inline inline_input_box">
                                                        <div class="docs-datepicker form-inline-inner">
                                                            <div class="input-group">
                                                                {{$acc_info->reg_date}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <th>마감일</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            {{$acc_info->closed_date}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <th>처리자</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            {{$acc_info->admin_nm}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="filter-area" class="card shadow">              
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
            <div class="card shadow">
                <div class="card-title">
                    <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle">Tip</h6>
                </div>
                <ul>
                    <li>매출금액 = 판매금액(소계) + 배송비 + 기타정산액 + 부담금액(할인쿠폰부담금액)</li>
                    <li>수수료 = 수수료지정 : 판매금액(소계) * 수수료율, 공급가지정 : 판매금액(소계) - 공급가액</li>
                    <li>정산금액 = 매출금액 - 수수료</li>
                </ul>
            </div>
        </div>
    </form>
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
        {headerName: "구분", field: "type",width:120,cellClass:'hd-grid-code',pinned:'left',aggSum:"합계",},
        {headerName: "일자", field: "state_date",width:120,cellClass:'hd-grid-code',pinned:'left',},
        {headerName: "주문번호", field: "ord_no",width:140,cellClass:'hd-grid-code',pinned:'left',
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a target="_blank"href="/partner/order/ord01/' + params.value + '" rel="noopener">' + params.value + '</a>';
                }
            }
        },
        {headerName: "복수주문", field: "multi_order", width:80, cellClass:'hd-grid-code',
            cellRenderer: function(params){
				if ( params.value == "Y" ) {
                    return `<a href="#" onclick="openMultiOrder('${params.data.ord_no}', '${params.data.ord_opt_no}')">${params.value}</a>`;
				}
			},
            cellStyle: (params) => { if (params.node.rowPinned != 'top' && params.value == "Y" ) 
                return { background: "#ffff96", textDecoration: "underline" };
            },
			pinned: 'left'
        },
        {headerName: "쿠폰", field: "coupon_nm",width:100,cellClass:'hd-grid-code',},
        {headerName: "상품", field: "goods_nm",width:170,cellClass:'hd-grid-code',type:'GoodsNameType'},
        {headerName: "옵션", field: "opt_nm",width:120,cellClass:'hd-grid-code',},
        {headerName: "스타일넘버", field: "style_no",width:120,cellClass:'hd-grid-code'},
        {headerName: "출고형태", field: "opt_type",width:120,cellClass:'hd-grid-code'},
        {headerName: "주문자", field: "user_nm",width:120,cellClass:'hd-grid-code'},
        {headerName: "결제방법", field: "pay_type",width:120,cellClass:'hd-grid-code'},
        {headerName: "수량", field: "qty",type:'currencyType',aggregation:true},
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
        {headerName: '수수료',
            children: [
                {headerName: "수수료율", field: "fee",type:'currencyType'},
                {headerName: "금액", field: "fee_ratio",type:'currencyType',aggregation:true},
            ]
        },
        {headerName: "정산금액", field: "acc_amt",type:'currencyType',aggregation:true},
        {headerName: "주문상태", field: "ord_state",cellClass:'hd-grid-code'},
        {headerName: "클레임상태", field: "clm_state",cellClass:'hd-grid-code'},
        {headerName: "주문일", field: "ord_date",cellClass:'hd-grid-code'},
        {headerName: "배송완료일", field: "dlv_end_date",cellClass:'hd-grid-code'},
        {headerName: "클레임완료일", field: "clm_end_date",cellClass:'hd-grid-code'},
        {headerName: "", field: "", width: "auto"},
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
        Search("{{$idx}}");
    });

    function Search(idx) {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
        });
        gx.Request('/partner/settle/stl01/detail_search/'+idx, data, -1, searchCallback);
    }

    function searchCallback(data) {}

    const openMultiOrder = (ord_no, ord_opt_no) => {
        const URL = `/partner/order/ord01/${ord_no}/${ord_opt_no}`;
        const OPENER = { TOP: 100, LEFT: 100, WIDTH: 1050, HEIGHT: 1000 };
        window.open(URL, "_blank", 
            `toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=${OPENER.TOP},left=${OPENER.LEFT},width=${OPENER.WIDTH},height=${OPENER.HEIGHT}`
        );
    };
    
</script>
@stop
