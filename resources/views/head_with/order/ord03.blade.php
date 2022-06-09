@extends('head_with.layouts.layout')
@section('title','수기판매')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">수기일괄입력</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 주문&amp;배송</span>
        <span>/ 수기일괄입력</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Save();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1">저장</a>
                    <a href="#" onclick="reset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
                    <a href="#" onclick="openImportPopup()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">판매처 데이터 변환</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <!-- end row -->
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매업체</label>
                            <div class="flax_box">
                                <select name='sale_place' id='sale_place' class="form-control form-control-sm sale_place_select">
                                    <option value=''>전체</option>
                                    @foreach ($sale_places as $sale_place)
                                    <option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 inner-td">
                        <div class="form-group">
                            {{-- <label for="name">파일</label>
                            <div class="flax_box">
                                <select name='fmt' class="form-control form-control-sm w-25 mr-1">
                                    <option value='xls'>Excel</option>
                                    <option value='tsv'>TSV</option>
                                    <option value='csv'>CSV</option>
                                </select>
                                <input type='text' class="form-control form-control-sm w-50" name='r_nm' value=''>
                                <a href="/sample/sample_sugi.xls" target="_blank" class="ml-1">파일형식 다운로드</a>
                            </div>--}}

                            <label for="name">파일</label>
                            <div class="d-flex align-items-center flex-column flex-sm-row">
                                <div class="custom-file w-100 mr-2">
                                    <input type="file" class="custom-file-input" id="file" aria-describedby="inputGroupFileAddon03">
                                    <label id="file-label" class="custom-file-label" for="file"><i class="bx bx-images font-size-16 align-middle mr-1"></i>입력할 파일을 선택 해 주세요.</label>
                                </div>
                                <div class="" style="min-width: 180px;">
                                    <button class="btn btn-outline-secondary" type="button" id="apply">적용</button>
                                    <a href="/sample/sample_sugi_2.xls" target="_blank" class="ml-2" style="text-decoration: underline !important;">수기판매 양식 다운로드</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매수수료</label>
                            <div class="flax_box">
                                <input type="checkbox" name="apy_fee" value="Y" class="m-1" checked>
                                판매수수료(%) 미입력 시에 <input type='text' class="form-control form-control-sm m-1 w-25" name='fee' id='fee' value='0'> % 자동적용
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">입금은행</label>
                            <div class="flax_box">
                                <select name='bank_code' id="bank_code" class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($banks as $bank)
                                    <option value='{{ $bank->name }}'>{{ $bank->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">출력형태</label>
                            <div class="flax_box">
                                <select name='ord_type' id='ord_type' class="form-control form-control-sm">
                                    @foreach ($ord_types as $ord_type)
                                    <option value='{{ $ord_type->code_id }}' @if($ord_type->code_id == "14") selected @endif>{{ $ord_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">

            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body">
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
</div>

<script>

    var columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
        {field: "code", headerName: "결과", width: 150,pinned: 'left'},
        {field: "ord_no", headerName: "주문번호", width: 170, type: 'HeadOrderNoType', pinned: 'left'},
        {field: "out_ord_no", headerName: "판매처 주문번호", width: 170, pinned: 'left'},
        {field: "ord_date", headerName: "주문일"},
        {field: "style_no", headerName: "스타일넘버",editable:true,cellClass:['hd-grid-edit']},
        {field: "goods_no", headerName: "상품번호",editable:true,cellClass:['hd-grid-edit']},
        {field: "goods_opt", headerName: "옵션",editable:true,cellClass:['hd-grid-edit']},
        {field: "goods_nm", headerName: "상품명",type:"HeadGoodsNameType"},
        {field: "qty", headerName: "수량",type:"numberType"},
        {field: "ord_amt", headerName: "금액",type:"numberType"},
        {field: "dlv_pay_type", headerName: "배송비지불시점",editable:true,cellClass:['hd-grid-edit']},
        {field: "dlv_amt", headerName: "배송비",type:"numberType",editable:true,cellClass:['hd-grid-edit']},
        {field: "dlv_add_amt", headerName: "추가배송비",type:"numberType",editable:true,cellClass:['hd-grid-edit']},
        {field: "pay_type", headerName: "결제방법"},
        {field: "pay_date", headerName: "입금일"},
        {field: "user_id", headerName: "회원아이디"},
        {field: "user_nm", headerName: "주문자"},
        {field: "phone", headerName: "주문자 연락처"},
        {field: "mobile", headerName: "주문자 핸드폰번호"},
        {field: "r_nm", headerName: "수령자"},
        {field: "r_zipcode", headerName: "수령자 우편번호"},
        {field: "r_addr", headerName: "수령자 주소"},
        {field: "r_phone", headerName: "수령자 연락처"},
        {field: "r_mobile", headerName: "수령자 핸드폰번호"},
        {field: "dlv_msg", headerName: "배송정보"},
        {field: "dlv_nm", headerName: "택배사"},
        {field: "dlv_cd", headerName: "송장정보"},
        {field: "dlv_msg", headerName: "출고메세지"},
        {field: "fee_rate", headerName: "수수료율(%)"},
        {field: "nvl", headerName: " "},
    ];

</script>

<script>

    const pApp = new App('', {
        gridId: "#div-gd"
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns, {});

    pApp.ResizeGrid(275);

    $(document).ready(function() {

        $("#apply").click(function() {
            console.log('upload');
            var file_data = $('#file').prop('files');
            uploadFile(file_data);
        });

        $('#file').change(function(e){
            uploadFile(this.files);
        });

        $(document).on("dragover", function(e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $(document).on('drop', function(e) {
            console.log('file drop');
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            uploadFile(files);
        });

    });

</script>
<script src="/handle/excel/xlsx.full.min.js"></script>
<script src="/handle/excel/xlsx.js"></script>
<!-- script -->
@include('head_with.order.ord03_js')
<!-- script -->
@stop
