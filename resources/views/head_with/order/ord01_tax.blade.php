@extends('head_with.layouts.layout-nav')
@section('title','세금계산서 관리')
@section('content')

<style>
    .flex-center {
        display: flex;
        justify-content: center;
        align-content: center;
    }
    .custom-table tbody tr td {
        padding: 5px 10px !important;
    }
    .custm_tb1 th {
        padding: 7px 12px 7px 12px;
        text-align: center;
    }
    .custm_tb1 td {
        text-align: center;
    }
    input.sm {
        max-width: 300px;
        display: inline;
    }
    input.right {
        text-align: right
    }
    .hidden {
        display: none;
    }
</style>

<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">세금계산서 {{ @$type === "create" ? "등록" : "변경(취소, 조회)" }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 주문</span>
                <span>/ 주문상세내역</span>
                <span>/ 세금계산서</span>
            </div>
        </div>
        <div>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="javascript:void(0)" class="m-0 font-weight-bold" disabled="disabled">세금계산서 발행 내역</a>
            </div>
            <div class="card-body pt-2">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box px-0 mx-0">
                            <h6 class="m-0 font-weight-bold">총 : <span id="tax_total" class="text-primary"></span> 건</h6>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="tax_grid" style="width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
        @if (@$type === "create")
            <form id="cr-form">
                <input type="hidden" name='user_id' value="{{ @$ord['ord']->user_id }}" />
                <input type="hidden" name='user_nm' value="{{ @$ord['ord']->user_nm }}" />
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">주문 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table table-bordered custom-table" id="dataTable" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="9%">
                                                <col width="25%">
                                                <col width="9%">
                                                <col width="25%">
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th>주문번호</th>
                                                    <td><div class="txt_box">{{ @$ord['ord_no'] }}</div></td>
                                                    <th>주문자 이름</th>
                                                    <td><div class="txt_box">{{ @$ord['ord']->user_nm }}</div></td>
                                                </tr>
                                                <tr>
                                                    <th>주문자 E-Mail</th>
                                                    <td><div class="txt_box">{{ @$ord['ord']->email }}</div></td>
                                                    <th>주문자 전화번호</th>
                                                    <td><div class="txt_box">{{ @$ord['ord']->phone }}</div></td>
                                                </tr>
                                                <tr>
                                                    <th>상품정보</th>
                                                    <td colspan="3"><div class="txt_box">{{ @$ord['ord_lists'][0]->goods_nm }}</div></td>
                                                </tr>
                                                <tr>
                                                    <th>비고</th>
                                                    <td colspan="3">
                                                        <div>
                                                            <input type='text' class="form-control form-control-sm" id="comment" name='comment'>
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
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">결제 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered th_border_none custm_tb1">
                                            <thead>
                                                <tr>
                                                    <th>입금액</th>
                                                    <th>적립금</th>
                                                    <th>쿠폰</th>
                                                    <th>할인</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ number_format(@$ord['pay']->pay_amt) }}</td>
                                                    <td>{{ number_format(@$ord['pay']->pay_point) }}</td>
                                                    <td>{{ number_format(@$ord['pay']->coupon_amt) }}</td>
                                                    <td>{{ number_format(@$ord['pay']->dc_amt) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">세금계산서 발급 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table table-bordered custom-table" id="dataTable" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="9%">
                                                <col width="40%">
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th>거래금액 총합</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm right" id="amt_tot" name='amt_tot' value="{{ number_format(@$ord['pay']->pay_amt) }}" onchange="setPriceTable()" />
                                                        <span>원 (공급가액 + 봉사료 + 부가가치세)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>공급가액</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm right" id="amt_sup" name='amt_sup' value="{{ number_format(round(@$ord['pay']->pay_amt / 1.1)) }}" readonly />
                                                        <span>원 (거래금액 총 합 - 봉사료 - 부가가치세)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>봉사료</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm right" id="amt_svc" name='amt_svc' value="0" onchange="setPriceTable()" />
                                                        <span>원</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>부가가치세</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm right" id="amt_tax" name='amt_tax' value="{{ number_format(@$ord['pay']->pay_amt - round(@$ord['pay']->pay_amt / 1.1)) }}" readonly />
                                                        <span>원 공급가액의 10%</span>
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
                <div class="flex-center mt-2">
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="reqCreateReceipt(event)">등록 요청</a>
                </div>
            </form>
        @else
            <form id='ud-form'>
                <input type="hidden" id='tax_stat' />
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">변경 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table table-bordered custom-table" id="dataTable" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="9%">
                                                <col width="40%">
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th class="mod_gubn_th" data-mod-gubn="tax_no">세금계산서 번호</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm" id="tax_no" name='tax_no' readonly />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>취소 금액</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm right" id="amt_tot" name='amt_tot' readonly />
                                                        <span>원</span>
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
            </form>
            <div class="flex-center mt-2">
                <a href="#" class="btn btn-sm btn-primary shadow-sm mr-2" onclick="updateReceipt(event)">취소 요청</a>
                <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="createReceipt(event)">세금계산서 발행</a>
            </div>
        @endif
    </div>
</div>

<script>
    const ord_no = '{{ @$ord["ord_no"] }}';
    const ord_opt_no = '{{ @$ord["ord_opt_no"] }}';
    const type = '{{ @$type }}';

    const pApp = new App('', { gridId: "#tax_grid" });
    const gridDiv = document.querySelector(pApp.options.gridId);
	let gx;

    const columns = [
        {headerName: '#', width: 40, valueGetter: 'node.id', cellRenderer: 'loadingRenderer'},
        {field: "tax_stat", headerName: "상태", width: 70},
        {field: "tax_no", headerName: "세금계산서 번호", width: 120, cellRenderer: (params) => `<u><a href="#" onclick="selectReceipt(event, ${params.data.tax_no}, '${params.data.amt_tot}', '${params.data.tax_stat}')">${params.value}</a></u>`},
        {field: "ord_no", headerName: "주문번호", width: 140},
        {field: "user_id", headerName: "회원아이디", width: 100},
        {field: "user_nm", headerName: "회원이름", width: 90},
        {field: "admin_id", headerName: "발행자아이디", width: 120},
        {field: "admin_nm", headerName: "발행자이름", width: 100},
        {field: "amt_tot", headerName: "발행금액", width: 100},
        {field: "rt", headerName: "발행일자", width: 100},
    ]

    function Search() {
        gx.Request(`/head/order/ord01/${ord_no}/tax/list`, null, 1, (res) => $("#tax_total").text(res.head.total));
    }

    // 해당 번호의 세금계산서 조회
    function selectReceipt(e, tax_no, amt_tot, tax_stat) {
        e.preventDefault();
        if(type === 'create') {
            const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/tax?tax_no=${tax_no}`;
            window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
            window.close();
        } else if(type === "update") {
            // 해당 세금계산서 번호 조회
            $("#tax_no").val(tax_no);
            $("#amt_tot").val(amt_tot);
            $("#tax_stat").val(tax_stat);
        }
    }

    /*
    ***
    세금계산서 등록 관련
    ***
    */

    let amt_tot_ori = parseInt('{{ @$ord["pay"]->pay_amt }}');
    let amt_tot = amt_tot_ori;
    let amt_sup = Math.round(amt_tot/1.1);
    let amt_svc = 0;
    let amt_tax = amt_tot - amt_sup;

    // 금액테이블값 변경
    function setPriceTable() {
        amt_tot = parseInt($("#amt_tot").val().replaceAll(",", ""));
        amt_svc = parseInt($("#amt_svc").val().replaceAll(",", ""));

        if(isNaN(amt_tot) || isNaN(amt_svc)) {
            amt_tot = amt_tot_ori;
            amt_svc = 0;
        } else if(amt_tot > amt_tot_ori) {
            alert("주문에 대한 발행금액이 초과되었습니다.");
            amt_tot = amt_tot_ori;
        } else if(amt_svc > amt_sup) {
            alert("봉사료는 공급가액보다 클 수 없습니다.");
            amt_svc = 0;
        }

        amt_sup = Math.round(amt_tot/1.1) - amt_svc;
        amt_tax = amt_tot - amt_sup - amt_svc;

        $("#amt_tot").val(amt_tot.toLocaleString('ko-KR'));
        $("#amt_sup").val(amt_sup.toLocaleString('ko-KR'));
        $("#amt_svc").val(amt_svc.toLocaleString('ko-KR'));
        $("#amt_tax").val(amt_tax.toLocaleString('ko-KR'));
    }

    // 세금계산서 발행
    function reqCreateReceipt(e) {
        e.preventDefault();
        const data = $("#cr-form").serialize();
        $.ajax({
            async: true,
            type: 'put',
            url: `/head/order/ord01/${ord_no}/${ord_opt_no}/tax-receipt?type=create`,
            data: data,
            success: function(res) {
                if(res.code === 200) {
                    alert("세금계산서가 정상적으로 발행되었습니다.");
                    const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/tax?tax_no=1`;
                    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
                    window.close();
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    /*
    ***
    세금계산서 변경(취소 ,조회) 관련
    ***
    */

    // 취소 요청
    function updateReceipt() {
        if($("#tax_no").val() === '') return alert("취소할 세금계산서를 선택해주세요.");
        if($("#tax_stat").val() === "취소") return alert("이미 취소된 계산서입니다.");

        const data = $("#ud-form").serialize();

        $.ajax({
            async: true,
            type: 'put',
            url: `/head/order/ord01/${ord_no}/${ord_opt_no}/tax-receipt?type=update`,
            data: data,
            success: function(res) {
                if(res.code === 200) {
                    alert("취소 요청이 정상적으로 처리되었습니다.");
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    // 세금계산서 발행
    function createReceipt(e) {
        e.preventDefault();
        const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/tax`;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
        window.close();
    }
        
	$(document).ready(function() {
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid(550);
        pApp.BindSearchEnter();
        Search();
	});  
</script>

@stop