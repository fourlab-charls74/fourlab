@extends('shop_with.layouts.layout-nav')
@section('title', '수기 일괄판매')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">수기 일괄판매</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 판매/배송처리</span>
                <span>/ 판매내역조회</span>
                <span>/ 수기 일괄판매</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0);" onclick="return save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 판매등록</a>
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
                                            <th>판매매장</th>
                                            <td>
                                                <div class="flax_box mr-2">
                                                    <input type='hidden' id="store_no" name="store_no" value="{{$store_cd}}">
                                                    <input type='text' id="store_nm" name="store_nm" class="form-control form-control-sm mt-1 mt-sm-0" value="{{$store_nm}}" readonly>
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
                                                    <a href="/sample/sample_sugi_batch.xlsx" class="ml-2" style="text-decoration: underline !important;">수기 일괄판매 양식 다운로드</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
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
                                            <th>출고형태</th>
                                            <td>
                                                <div class="flax_box">
                                                    <select name="ord_type" id="ord_type" class="form-control form-control-sm" disabled>
                                                        @foreach(@$ord_types as $val)
                                                            <option value="{{$val->code_id}}" @if($val->code_id == '14') selected @endif>{{$val->code_val}}</option>
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
                    <p class="mt-1" style="color: red;">* 주문매장 미선택 시 상품이 창고에서 출고됩니다.</p>
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
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>
@include('shop_with.order.ord01_batch_js')
<script language="javascript">
    let columns = [
        {field: "result", headerName: "결과", width: 100, cellStyle: (params) => ({...StyleLineHeight, "color": params.value == '200' ? 'green' : 'red'}),
            cellRenderer: (params) => params.value == null ? '' : params.value  == '200' ? '성공' : ('실패(' + (out_order_errors[params.value] || '') + ')')
        },
        // {field: "ord_no", headerName: "주문번호", width: 100, cellStyle: StyleLineHeight},
        // {field: "out_ord_no", headerName: "매장 주문번호", width: 100, cellStyle: StyleLineHeight},
        {field: "ord_date", headerName: "판매일", width: 80, cellStyle: StyleLineHeight},
        {field: "prd_cd", headerName: "바코드", width: 120,
			editable: (params) => params.data.result == "-102" || params.data.result == "-101" ? true : false,
			cellStyle: (params) => params.data.result == "-102" || params.data.result == "-101" ? {"background-color": "#ffff99"} : {}
		},
        {field: "goods_no", headerName: "상품번호", width: 60, cellStyle: StyleLineHeight},
        {field: "goods_nm", headerName: "상품명", width: 230, cellStyle: {"line-height": "30px"},
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
                    return '<a href="#" onclick="return openShopProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
        {field: "goods_opt", headerName: "옵션", width: 180, cellStyle: {"line-height": "30px"}},
        {field: "qty", headerName: "수량", width: 50, type: 'currencyType',
			editable: (params) => params.data.result == "-103" || params.data.result == "-105" ? true : false,
			cellStyle: (params) => params.data.result == "-103" || params.data.result == "-105" ? {"background-color": "#ffff99"} : {}
		},
        {field: "price", headerName: "판매가", width: 60, type: 'currencyType',
			editable: (params) => params.data.result == "-104" ? true : false,
			cellStyle: (params) => params.data.result == "-104" ? {"background-color": "#ffff99"} : {}
		},
        {field: "dlv_amt", headerName: "배송비", width: 60, type: 'currencyType', cellStyle: {"line-height": "30px"}},
        {field: "add_dlv_amt", headerName: "추가배송비", width: 70, type: 'currencyType', cellStyle: {"line-height": "30px"}},
        {field: "pay_type", headerName: "결제방법", width: 60, cellStyle: StyleLineHeight,
            cellRenderer: (params) => params.value == 1 ? '현금' : '카드',
        },
        {field: "pay_date", headerName: "입금일자", width: 80, cellStyle: StyleLineHeight},
        {field: "bank_inpnm", headerName: "입금자명", width: 60, cellStyle: StyleLineHeight},
        {field: "user_id", headerName: "주문자 ID", width: 80, cellStyle: StyleLineHeight},
        {field: "user_nm", headerName: "주문자", width: 60, cellStyle: StyleLineHeight},
        {field: "phone", headerName: "주문자 전화", width: 80, cellStyle: StyleLineHeight},
        {field: "mobile", headerName: "주문자 휴대전화", width: 100, cellStyle: StyleLineHeight},
        {field: "r_nm", headerName: "수령자", width: 60, cellStyle: StyleLineHeight},
        {field: "r_phone", headerName: "수령자 전화", width: 80, cellStyle: StyleLineHeight},
        {field: "r_mobile", headerName: "수령자 휴대전화", width: 100, cellStyle: StyleLineHeight},
        {field: "r_zipcode", headerName: "수령 우편번호", width: 100, cellStyle: StyleLineHeight},
        {field: "r_addr1", headerName: "수령 주소", width: 170, cellStyle: {"line-height": "30px"},
            cellRenderer: (params) => `${params.data.r_addr1 || ''} ${params.data.r_addr2 || ''}`
        },
        {field: "dlv_msg", headerName: "배송메세지", width: 130, cellStyle: {"line-height": "30px"}},
        {field: "dlv_cd", headerName: "택배업체", width: 80, cellStyle: StyleLineHeight},
        {field: "dlv_no", headerName: "송장번호", width: 80, cellStyle: StyleLineHeight},
        {field: "dlv_comment", headerName: "출고메세지", width: 130, cellStyle: {"line-height": "30px"}},
        {field: "fee_rate", headerName: "판매수수료율", width: 90, type: 'currencyType', cellStyle: {"line-height": "30px"}},
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
    });

    function openShopProduct(prd_no){
        var url = '/shop/product/prd01/' + prd_no;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

</script>
@stop
