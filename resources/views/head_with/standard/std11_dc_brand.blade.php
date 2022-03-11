@extends('head_with.layouts.layout-nav') 
@section('title','브랜드 검색')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">광고할인관리</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 기준정보</span>
            <span>/ 광고할인관리</span>
            <span>/ 브랜드검색</span>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <input type="hidden" name="no" value="{{$no}}">
            <div class="card mb-1">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <button onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</button>
                        <button class="btn btn-sm btn-outline-primary shadow-sm brand-add-btn">브랜드 추가</button>
                        <button class="btn btn-sm btn-outline-primary shadow-sm brand-delete-btn">브랜드 삭제</button>
                        <button class="btn btn-sm btn-primary shadow-sm brand-submit-btn">저장</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_cd">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">업체</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm" style="width:100%">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="type">구분</label>
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="brand_type" id="type-a" class="custom-control-input" value="" checked>
                                        <label class="custom-control-label" for="type-a">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="brand_type" id="type-s" class="custom-control-input" value="S">
                                        <label class="custom-control-label" for="type-s">S</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="brand_type" id="type-u" class="custom-control-input" value="U">
                                        <label class="custom-control-label" for="type-u">U</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">성별구분</label>
                                <!--div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sex" id="sex-a" class="custom-control-input" value="" checked>
                                        <label class="custom-control-label" for="type-a">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sex" id="sex-m" class="custom-control-input" value="m">
                                        <label class="custom-control-label" for="type-s">남자</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sex" id="sex-w" class="custom-control-input" value="w">
                                        <label class="custom-control-label" for="type-u">여자</label>
                                    </div>
                                </div-->
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="com_type">베스트여부</label>
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="best" id="best-a" class="custom-control-input" value="" checked>
                                        <label class="custom-control-label" for="best-a">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="best" id="best-y" class="custom-control-input" value="Y">
                                        <label class="custom-control-label" for="best-y">Y</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="best" id="best-n" class="custom-control-input" value="N">
                                        <label class="custom-control-label" for="best-n">N</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="type">사용여부</label>
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="use_yn" id="use-a" class="custom-control-input" value="">
                                        <label class="custom-control-label" for="use-a">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="use_yn" id="use-y" class="custom-control-input" value="Y" checked>
                                        <label class="custom-control-label" for="use-y">Y</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="use_yn" id="use-n" class="custom-control-input" value="N">
                                        <label class="custom-control-label" for="use-n">N</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                        <div class="flax_box">할인율 : <div class="mx-1"><input type="text" id="dc_rate" class="form-control form-control-sm" value="0" style="text-align:right;width:50px;"></div>/ </div>
                        <div class="flax_box ml-1">할인금액 : <div class="mx-1"><input type="text" id="dc_amt" class="form-control form-control-sm" value="0" style="text-align:right;width:50px;"></div> 원 /</div>
                        <div class="flax_box ml-1">마진율 제한 : <div class="mx-1"><input type="text" id="limit_margin_rate" class="form-control form-control-sm" value="0" style="text-align:right;width:50px;"></div>%</div>
                        <a href="#" onclick="" class="btn btn-sm btn-primary shadow-sm apply-btn ml-1">제휴할인 적용</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="brand-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<!-- 스크립트 선언부 -->
<script>
    const editCellStyle = { 
        'background' : '#ffff99', 
        'border-right' : '1px solid #e0e7e7' 
    };

    //브랜드별 할인 컬럼
    const brandColumns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {
            field: "brand_nm", 
            headerName: "브랜드",
            width : 120
        },
        {
            field: "brand_nm_eng", 
            headerName: "브랜드(영문)",
            width : 120
        },
        {
            field: "qty", 
            headerName: "상품수",
            width : 60,
            type:'currencyType'
        },
        {
            field: "com_nm", 
            headerName: "업체",
            width : 80
        },
        {
            field: "dc_rate", 
            headerName: "할인율(%)", 
            editable: true, 
            cellStyle: editCellStyle, 
            type:'currencyType'
        },
        {
            field: "dc_amt", 
            headerName: "할인금액(원)", 
            editable: true, 
            cellStyle: editCellStyle, 
            type:'currencyType'
        },
        {
            field: "limit_margin_rate", 
            headerName: "마진율제한(%)", 
            editable: true, 
            cellStyle: editCellStyle, 
            type:'currencyType'
        },
        {field: "admin_nm", headerName: "관리자명"},
        {field: "ut", headerName: "최근수정일시", width: 130}
    ];


//     //브랜드별 할인 설정
    const brandApp = new App('', { gridId: "#brand-gd" });
    const brandGrid = document.querySelector(brandApp.options.gridId);
    const brandGx = new HDGrid(brandGrid, brandColumns);

    brandGx.gridOptions.getRowNodeId = function(data) {
        return data.brand;
    }

    const no = '{{$no}}';
    const pageNo = -1;

    const Search = () => {
        const data = $('form[name="search"]').serialize();

        brandGx.Request('/head/standard/std11/search/dc-brand', data, pageNo, function(res){
            $('#brand-total').html(res.head.total);
        });
    };
</script>

 <!-- 스크립트 동작 -->
 <script>
    $('.brand-add-btn').click((e) => {
        e.preventDefault();

        searchBrand.Open((code) => {
            if (confirm("선택한 브랜드를 추가하시겠습니까?") === false) return;

            $.post(`/head/standard/std11/dc/brand/${no}`, { 'brand' : code }, () =>{
                alert('추가되었습니다.');
                Search();
            })
            .fail((res) => alert(res.responseJSON.msg));
        });
    });
    
    $('.brand-delete-btn').click((e) => {
        e.preventDefault();

        const s_brand_cnt = brandGx.getSelectedRows().length;

        if (s_brand_cnt === 0) {
            alert('삭제할 브랜드를 선택해주세요.');
            return;
        }

        if (confirm("선택한 브랜드를 삭제하시겠습니까?") === false) return;

        brandGx.getSelectedRows().forEach((row, idx) => {        
            $.ajax({
                async: true,
                type: 'delete',
                url: `/head/standard/std11/dc/brand/${no}`,
                data: { 'brand' : row.brand },
                success: function (res) {
                    if (idx === s_brand_cnt -1) {
                        alert('삭제되었습니다.');
                        Search();
                    }
                },
                error: function(request, status, error) {
                    console.log(request);
                    alert(request.responseJSON.message);
                }
            });
        });
    });

    $('.brand-submit-btn').click((e) => {
        e.preventDefault();
        const s_brand_cnt = brandGx.getSelectedRows().length;

        if (s_brand_cnt === 0) {
            alert('수정할 브랜드를 선택해주세요.');
            return;
        }

        if (confirm("선택한 브랜드를 수정하시겠습니까?") === false) return;

        brandGx.getSelectedRows().forEach((row, idx) => {        
            $.ajax({
                async: true,
                type: 'put',
                url: `/head/standard/std11/dc/brand/${no}`,
                data: row,
                success: function (res) {
                    if (idx === s_brand_cnt -1) {
                        alert('수정되었습니다.');
                        Search();
                    }
                },
                error: function(request, status, error) {
                    console.log(request);
                    alert(request.responseJSON.message);
                }
            });
        });
    });

    $('.apply-btn').click(e => {
        brandGx.getSelectedRows().forEach(function(data) {
            const nodeRow = brandGx.gridOptions.api.getRowNode(data.brand);
            data.dc_rate = $('#dc_rate').val();
            data.dc_amt = $('#dc_amt').val();
            data.limit_margin_rate = $('#limit_margin_rate').val();

            nodeRow.setData(data);
        });
    });

    brandApp.ResizeGrid();
    Search();
</script>
@stop
