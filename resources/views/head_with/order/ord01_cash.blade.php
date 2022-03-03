@extends('head_with.layouts.layout-nav')
@section('title','현금영수증 관리')
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
            <h3 class="d-inline-flex">현금영수증 {{ @$type === "create" ? "등록" : (@$type === "update" ? "변경(취소, 조회)" : "결과") }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 주문</span>
                <span>/ 주문상세내역</span>
                <span>/ 현금영수증</span>
            </div>
        </div>
        <div>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
        @if (in_array(@$type, ['create', 'update']))
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="javascript:void(0)" class="m-0 font-weight-bold" disabled="disabled">현금영수증 발행 내역</a>
                </div>
                <div class="card-body pt-2">
                    <div class="card-title">
                        <div class="filter_wrap">
                            <div class="fl_box px-0 mx-0">
                                <h6 class="m-0 font-weight-bold">총 : <span id="cash_total" class="text-primary"></span> 건</h6>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="cash_grid" style="width:100%;" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        @endif
        @if (@$type === "create")
            <form id="cr-form">
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
                                                            <input type='text' class="form-control form-control-sm" id="etc" name='etc'>
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
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">가맹점 정보</a>
                    </div>
                    <div class="card-body">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table table-bordered custom-table" id="dataTable" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="15%">
                                                <col width="25%">
                                                <col width="15%">
                                                <col width="25%">
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th>사업장 구분</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="corp_type_0" name="corp_type" value="0" checked />
                                                                <label class="custom-control-label" for="corp_type_0">직접 판매</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="corp_type_1" name="corp_type" value="1" />
                                                                <label class="custom-control-label" for="corp_type_1">입점몰 판매</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <th>과세/면세 구분</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="corp_tax_type_TG01" name="corp_tax_type" value="TG01" checked />
                                                                <label class="custom-control-label" for="corp_tax_type_TG01">과세</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="corp_tax_type_TG02" name="corp_tax_type" value="TG02" />
                                                                <label class="custom-control-label" for="corp_tax_type_TG02">면세</label>
                                                            </div>
                                                        </div>
                                                    </td>                                        
                                                </tr>
                                                <tr>
                                                    <th>발행 사업자번호</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm" id="corp_tax_no" name='corp_tax_no' />
                                                    </td>                                            
                                                    <th>상호</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm" id="corp_nm" name='corp_nm' />
                                                    </td>                                        
                                                </tr>
                                                <tr>
                                                    <th>대표자명</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm" id="corp_owner_nm" name='corp_owner_nm' />
                                                    </td>
                                                    <th>사업장 대표자 연락처</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm" id="corp_telno" name='corp_telno' />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>사업장 주소</th>
                                                    <td colspan="3">
                                                        <div>
                                                            <input type='text' class="form-control form-control-sm" id="corp_addr" name='corp_addr' />
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
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">현금영수증 발급 정보</a>
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
                                                    <th>발행 용도</th>
                                                    <td>
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="tr_code_0" name="tr_code" value="0" checked />
                                                                <label class="custom-control-label" for="tr_code_0">소득공제용</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" id="tr_code_1" name="tr_code" value="1" />
                                                                <label class="custom-control-label" for="tr_code_1">지출증빙용</label>
                                                            </div>
                                                        </div>
                                                    </td>                                     
                                                </tr>
                                                <tr>
                                                    <th class="id_info_th" data-tr-code="0">주민(휴대폰)번호</th>
                                                    <th class="id_info_th hidden" data-tr-code="1">사업자번호</th>
                                                    <td>
                                                        <input type='text' class="form-control form-control-sm sm" id="id_info" name='id_info' maxlength="13" value="{{ str_replace("-", "", @$ord['ord']->phone) }}"/>
                                                        <span>("-" 생략)</span>
                                                    </td>
                                                </tr>
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
            @if (@$type === "update")
                <form id='ud-form'>
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
                                                        <th>변경 타입</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="mod_type_STSC" name="mod_type" value="STSC" checked />
                                                                    <label class="custom-control-label" for="mod_type_STSC">취소요청</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="mod_type_STPC" name="mod_type" value="STPC" />
                                                                    <label class="custom-control-label" for="mod_type_STPC">부분취소요청</label>
                                                                </div>
                                                            </div>
                                                        </td>                                     
                                                    </tr>
                                                    <tr>
                                                        <th>변경 요청 거래번호 구분</th>
                                                        <td>
                                                            <div class="form-inline form-radio-box">
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="mod_gubn_MG01" name="mod_gubn" value="cash_no" checked />
                                                                    <label class="custom-control-label" for="mod_gubn_MG01">현금 영수증 거래번호</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="mod_gubn_MG02" name="mod_gubn" value="receipt_no" />
                                                                    <label class="custom-control-label" for="mod_gubn_MG02">현금 영수증 승인번호</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="mod_gubn_MG03" name="mod_gubn" value="id_info" />
                                                                    <label class="custom-control-label" for="mod_gubn_MG03">신분확인 ID (휴대폰번호/주민번호/사업자번호)</label>
                                                                </div>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input" id="mod_gubn_MG04" name="mod_gubn" value="tno" />
                                                                    <label class="custom-control-label" for="mod_gubn_MG04">PG 결제 거래번호</label>
                                                                </div>
                                                            </div>
                                                        </td>                                     
                                                    </tr>
                                                    <tr>
                                                        <th class="mod_gubn_th" data-mod-gubn="cash_no">현금영수증 거래번호</th>
                                                        <th class="mod_gubn_th hidden" data-mod-gubn="receipt_no">현금 영수증 승인번호</th>
                                                        <th class="mod_gubn_th hidden" data-mod-gubn="id_info">신분확인 ID</th>
                                                        <th class="mod_gubn_th hidden" data-mod-gubn="tno">PG 결제 거래번호</th>
                                                        <td>
                                                            <input type='text' class="form-control form-control-sm mod_gubn_td" id="cash_no" name='cash_no' />
                                                            <input type='text' class="form-control form-control-sm mod_gubn_td hidden" id="receipt_no" name='receipt_no' />
                                                            <input type='text' class="form-control form-control-sm mod_gubn_td hidden" id="id_info" name='id_info' />
                                                            <input type='text' class="form-control form-control-sm mod_gubn_td hidden" id="tno" name='tno' />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>원거래 시각</th>
                                                        <td>
                                                            <input type='text' class="form-control form-control-sm" id="trad_time" name='trad_time' maxlength="14" />
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
                    <a href="#" class="btn btn-sm btn-primary shadow-sm mr-2" onclick="updateReceipt(event)">변경 요청</a>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="createReceipt(event)">현금영수증 발행</a>
                </div>
            @else
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="javascript:void(0)" class="m-0 font-weight-bold">결과 페이지(현금영수증 {{ @$result['type'] === "update" ? "변경/조회" : '등록' }})</a>
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
                                                    <th>결과 메세지</th>
                                                    <td><div class="txt_box">{{ @$result['msg'] }}</div></td>                                
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    const ord_no = '{{ @$ord["ord_no"] }}';
    const ord_opt_no = '{{ @$ord["ord_opt_no"] }}';
    const type = '{{ @$type }}';

    const pApp = new App('', { gridId: "#cash_grid" });
    const gridDiv = document.querySelector(pApp.options.gridId);
	let gx;

    const columns = [
        {headerName: '#', width: 40, valueGetter: 'node.id', cellRenderer: 'loadingRenderer'},
        {field: "cash_stat", headerName: "상태", width: 70},
        {field: "cash_no", headerName: "거래번호", width: 120, cellRenderer: (params) => `<u><a href="#" onclick="selectReceipt(event, ${params.data.receipt_no}, ${params.data.cash_no}, ${params.data.app_time})">${params.value}</a></u>`},
        {field: "ord_no", headerName: "주문번호", width: 140},
        {field: "user_id", headerName: "회원아이디", width: 100},
        {field: "user_nm", headerName: "회원이름", width: 90},
        {field: "admin_id", headerName: "발행자아이디", width: 120},
        {field: "admin_nm", headerName: "발행자이름", width: 100},
        {headerName: "영수증확인", width: 100, cellRenderer: (params) => `<u><a href="#" onclick="printReceipt(event)">출력</a></u>`},
    ]

    function Search() {
        gx.Request(`/head/order/ord01/${ord_no}/cash/list`, null, 1, (res) => $("#cash_total").text(res.head.total));
    }

    // 해당 거래번호의 현금영수증 조회
    function selectReceipt(e, receipt_no, cash_no, app_time) {
        e.preventDefault();
        if(type === 'create') {
            const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/cash?cash_no=${cash_no}`;
            window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
            window.close();
        } else if(type === "update") {
            // 해당 거래번호 조회
            $("#receipt_no").val(receipt_no);
            $("#cash_no").val(cash_no);
            $("#tno").val(cash_no);
            $("#trad_time").val(app_time);
        }
    }

    // 해당 현금영수증 출력
    function printReceipt(e) {
        e.preventDefault();
        const tno = '{{ @$ord["pay"]->tno }}';
        const pay_amt = '{{ @$ord["pay"]->pay_amt }}';
        const url = `https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno=${tno}&order_no=${ord_no}&trade_mony=${pay_amt}`;
        window.open(url,"_blank","top=100,left=100,width=360,height=647");
    }  

    /*
    ***
    현금영수증 등록 관련
    ***
    */

    let amt_tot_ori = parseInt('{{ @$ord["pay"]->pay_amt }}');
    let amt_tot = amt_tot_ori;
    let amt_sup = Math.round(amt_tot/1.1);
    let amt_svc = 0;
    let amt_tax = amt_tot - amt_sup;

    // 발행둉도값 선택 시 하단컨텐츠 변경
    function setTrCode() {
        $('input[name="tr_code"]').on("click", function(e) {
            $('.id_info_th').addClass("hidden");
            $(`.id_info_th[data-tr-code="${e.target.value}"]`).removeClass("hidden");
        });
    }

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

    // 현금영수증 발행
    function reqCreateReceipt(e) {
        e.preventDefault();
        const data = $("#cr-form").serialize();
        $.ajax({
            async: true,
            type: 'put',
            url: `/head/order/ord01/${ord_no}/${ord_opt_no}/cash-receipt?type=create`,
            data: data,
            success: function(res) {
                console.log(res);
                const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/cash?done=1`;
                window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
                window.close();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    /*
    ***
    현금영수증 변경(취소 ,조회) 관련
    ***
    */

    // 변경 요청 거래번호 구분값 선택 시 하단컨텐츠 변경
    function setModGubnBox() {
        $('input[name="mod_gubn"]').on("click", function(e) {
            $('.mod_gubn_th').addClass("hidden");
            $(`.mod_gubn_th[data-mod-gubn="${e.target.value}"]`).removeClass("hidden");
            $('.mod_gubn_td').addClass("hidden");
            $(`.mod_gubn_td[name="${e.target.value}"]`).removeClass("hidden");
        });
    }

    // 변경 요청
    function updateReceipt() {
        const selected_gubn = $("input[name='mod_gubn']:checked").val();
        if(selected_gubn === "cash_no" && $("#cash_no").val() === '') return alert("현금 영수증 거래번호를 입력해주세요.");
        if(selected_gubn === "receipt_no" && $("#receipt_no").val() === '') return alert("현금 영수증 승인번호를 입력해주세요.");
        if(selected_gubn === "id_info" && $("#id_info").val() === '') return alert("신분확인 ID를 입력해주세요.");
        if(selected_gubn === "tno" && $("#tno").val() === '') return alert("PG 결제 거래번호를 입력해주세요.");
        if($("#trad_time").val() === '') return alert("원거래 시각을 입력해주세요.");

        const data = $("#ud-form").serialize();

        $.ajax({
            async: true,
            type: 'put',
            url: `/head/order/ord01/${ord_no}/${ord_opt_no}/cash-receipt?type=update`,
            data: data,
            success: function(res) {
                console.log(res);
                const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/cash?done=1&cash_no=1`;
                window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
                window.close();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    // 현금영수증 발행
    function createReceipt(e) {
        e.preventDefault();
        const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/cash`;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
        window.close();
    }
        
	$(document).ready(function() {
        if(type === "create" || type === "update") {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(550);
            pApp.BindSearchEnter();
            Search();
        }

        if(type === "create") {
            setTrCode();
        } else if(type === "update") {
            setModGubnBox();
        }
	});  
</script>

@stop