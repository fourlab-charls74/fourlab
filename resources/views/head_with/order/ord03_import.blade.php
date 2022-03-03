@extends('head_with.layouts.layout-nav')
@section('title','판매처 데이터 변환')
@section('content')

<div class="py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">수기판매 일괄입력</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>판매처 데이터 변환</span>
            </div>
        </div>
        <div>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="detail">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="javascript:void(0);" onclick="ApplyImportFile();return false;" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1">데이터 불러오기</a>
                        <a href="javascript:void(0);" id="search_sbtn" onclick="ApplyImportData();return false;" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1">데이터 적용</a>
                        <a href="#" onclick="openFormatPopup()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">판매업체 엑셀 양식 변경</a>
                        <a href="#" onclick="reset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- end row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group w-50">
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
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">파일</label>
                                <div class="flax_box img_file_cum_wrap">
                                    <div class="form-inline form-radio-box">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fmt" id="fmt_xls" class="custom-control-input" value="xls" checked/>
                                            <label class="custom-control-label" for="fmt_xls">xls</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fmt" id="fmt_tsv" class="custom-control-input" value="tsv"/>
                                            <label class="custom-control-label" for="fmt_tsv">tsv</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="fmt" id="fmt_csv" class="custom-control-input" value="csv"/>
                                            <label class="custom-control-label" for="fmt_csv">csv</label>
                                        </div>
                                        <div class="flax_box img_file_cum_wrap" style="width: 500px;">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="file">
                                                <label id="file-label" class="custom-file-label justify-content-start" for="file" aria-describedby="">입력할 파일을 선택 해 주세요.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">판매수수료</label>
                                <div class="flax_box">
                                    <input type="checkbox" name="apy_fee" value="Y" class="m-1" checked>
                                    판매수수료(%) 미입력 시에 <input type='text' class="form-control form-control-sm m-1 w-25" name='fee' id='fee' value='0'> % 자동적용
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">배송비</label>
                                <div class="flax_box">
                                    <input type="checkbox" name="dlv_amt_yn" id="dlv_amt_yn" value="Y" class="m-1">
                                    주문금액 <input type='text' class="form-control form-control-sm m-1 w-25" name='dlv_amt_limit' id="dlv_amt_limit" value='{{@$dlv_amt_limit}}'> 원 미만 시 배송비
                                    <input type='text' class="form-control form-control-sm m-1 w-25" name='dlv_amt' id="dlv_amt" value='{{@$dlv_amt}}'> 원 자동 적용,
                                    <input type="checkbox" name="dlv_amt_minus_yn" id="dlv_amt_minus_yn" value="Y" class="m-1">
                                    주문금액에서 배송비 빼기
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
    <div class="card shadow">
        <div class="card-body pt-3">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        파일을 선택 해 주십시오.
                    </div>
                    <div class="fr_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:30vh; width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body pt-3">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd2-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                        <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="gx2.delSelectedRows();return false;"><span class="fs-12">삭제</span></a>
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm" onclick="gx2.Download();return false;"><span class="fs-12">다운로드</span></a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd2" style="height:30vh; width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>
<script>
    const columns = [];
    for(i=1;i<=50;i++) {
        if(i>26) {
            cd = 'A' + String.fromCharCode(65+i-27)
        } else {
            cd = String.fromCharCode(65+i-1)
        }
        columns.push({
            "field": cd , "headerName": cd
        });
    }
</script>
<script>
    const pApp = new App('', {
        gridId: "#div-gd"
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    let gx = new HDGrid(gridDiv, columns, {});
    let gx2 = null;
    var columns2 = opener.columns.slice();
    columns2.splice(1,2);
    columns2.splice(6,0,{field: "choice_info", headerName: "선택정보"});

    $(document).ready(function() {

        gx2 = new HDGrid(document.querySelector("#div-gd2"), columns2, {});

        $('#file').change(function(e){
            uploadImportFile(this.files);
        });

        $(document).on("dragover", function(e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $(document).on('drop', function(e) {
            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;
            uploadImportFile(files);
        });

		@if( $sale_place != "" )
			$('#sale_place').val('{{$p_sale_place}}');
			get_fee();
		@endif
    });

</script>
<script src="/handle/excel/xlsx.full.min.js"></script>
<script src="/handle/excel/xlsx.js"></script>
<!-- script -->
@include('head_with.order.ord03_js')
<!-- script -->

@stop
