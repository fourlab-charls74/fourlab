@extends('store_with.layouts.layout-nav')
@section('title', '수기 일괄판매')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">수기 일괄판매</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 매장주문</span>
                <span>/ 수기 일괄판매</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0);" onclick="return Validate(document.f1)" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 판매등록</a>
            <a href="javascript:void(0);" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 120px;}
        .table td {width: 25%;}
        
        @media (max-width: 740px) {
            .table td {float: unset !important;width: 100% !important;}
        }
    </style>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">기본정보</a>
            </div>
            <div class="card-body">
                <form name="f1">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">판매매장</th>
                                            <td>
                                                <div class="flax_box mr-2">
                                                    <div class="form-inline inline_btn_box w-100">
                                                        <input type='hidden' id="store_nm" name="store_nm">
                                                        <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">파일</th>
                                            <td colspan="3">
                                                <div class="flex_box">
                                                    <div class="custom-file w-50">
                                                        <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
                                                        <label class="custom-file-label" for="file"></label>
                                                    </div>
                                                    <div class="btn-group ml-2">
                                                        <button class="btn btn-outline-primary apply-btn" type="button" onclick="upload();">적용</button>
                                                    </div>
                                                    <a href="/sample/sample_store_sugi_batch.xlsx" class="ml-2" style="text-decoration: underline !important;">수기 일괄판매 양식 다운로드</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>출고형태</th>
                                            <td>
                                                <div class="flax_box">
                                                    <select name="ord_type" id="ord_type" class="form-control form-control-sm">
                                                        @foreach(@$ord_types as $val)
                                                            <option value="{{$val->code_id}}" @if($val->code_id == '14') selected @endif>{{$val->code_val}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <th>입금은행</th>
                                            <td>
                                                <div class="flax_box">
                                                    <select name="bank_code" id="bank_code" class="form-control form-control-sm">
                                                        <option value="">선택하세요.</option>
                                                        @foreach($banks as $bank)
                                                            <option value="{{$bank->name}}">{{$bank->value}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <th>판매수수료</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="checkbox" name="apy_fee" value="Y" class="m-1" checked>
                                                    판매수수료(%) 미입력 시 <input type='text' class="form-control form-control-sm m-1 w-25" name='fee' id='fee' value='0'> % 자동적용
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">파일 데이터</a>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- script -->
@include('store_with.stock.stk03_batch_js')
<script language="javascript">
    let columns = [
        // {headerCheckboxSelection: true, checkboxSelection: true,  width: 28},
        {field: "result", headerName: "결과", width: 100, cellStyle: StyleLineHeight},
        {field: "ord_no", headerName: "주문번호", width: 100, cellStyle: StyleLineHeight},
        {field: "out_ord_no", headerName: "판매처 주문번호", width: 100, cellStyle: StyleLineHeight},
        {field: "ord_date", headerName: "주문일", width: 100, cellStyle: StyleLineHeight},
        {field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: StyleLineHeight},
        {field: "goods_no", headerName: "상품번호", width: 60, cellStyle: StyleLineHeight},
        {field: "goods_nm", headerName: "상품명", type: "HeadGoodsNameType", width: 227, wrapText: true, autoHeight: true},
        {field: "goods_opt", headerName: "옵션", width: 130},
        {field: "qty", headerName: "수량", width: 60, type: 'currencyType'},
        {field: "price", headerName: "판매가", width: 60, type: 'currencyType'},
        {field: "dlv_amt", headerName: "배송비", width: 60, type: 'currencyType'},
        {field: "dlv_amt", headerName: "추가배송비", width: 60, type: 'currencyType'},
        {field: "dlv_amt", headerName: "주문자 ID", width: 100},
        {field: "dlv_amt", headerName: "주문자", width: 100},
        {field: "dlv_amt", headerName: "주문자 전화", width: 100},
        {field: "dlv_amt", headerName: "주문자 휴대전화", width: 100},
        {field: "dlv_amt", headerName: "수령자", width: 100},
        {field: "dlv_amt", headerName: "수령자 전화", width: 100},
        {field: "dlv_amt", headerName: "수령자 휴대전화", width: 100},
        {field: "dlv_amt", headerName: "수령 우편번호", width: 100},
        {field: "dlv_amt", headerName: "수령 주소", width: 100},
        {field: "dlv_amt", headerName: "배송메세지", width: 100},
        {field: "dlv_amt", headerName: "택배업체", width: 100},
        {field: "dlv_amt", headerName: "송장번호", width: 100},
        {field: "dlv_amt", headerName: "출고메세지", width: 100},
        {field: "dlv_amt", headerName: "판매수수료율", width: 100},
    ];
</script>

<script type="text/javascript" charset="utf-8">

    const pApp = new App('', { gridId:"#div-gd" });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275, 410);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        // 파일선택 시 화면에 표기
        $('#excel_file').on('change', function(e){
            if (validateFile() === false) {
                $('.custom-file-label').html("");
                return;
            }
            $('.custom-file-label').html(this.files[0].name);
        });
    });

    // 선택파일형식 검사
    const validateFile = () => {
        const target = $('#excel_file')[0].files;

        if (target.length > 1) return alert("파일은 1개만 올려주세요.");

        if (target === null || target.length === 0) return alert("업로드할 파일을 선택해주세요.");

        if (!/(.*?)\.(xlsx|XLSX)$/i.test(target[0].name)) return alert("Excel파일만 업로드해주세요.(xlsx)");

        return true;
    };

</script>
@stop
