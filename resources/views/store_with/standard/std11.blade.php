@extends('store_with.layouts.layout')
@section('title','수선관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">수선관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 고객/수선관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="/store/standard/std11/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
		            <a href="#" onclick="formReset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">접수일자</label>
                            <div class="form-inline date-select-inbox">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
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
                            <label for="where1">조회구분</label>
                            <div class="flex_box">
                                <select class="form-control form-control-sm" name="where1" id="where1" onchange="changeWhere1(this)">
                                    <option value="">조회내역없음</option>
                                    <option value="customer">고객명</option>
                                    <option value="mobile">전화번호</option>
                                    <option value="product_cd">상품코드</option>
                                    <option value="product">상품명</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="where2">조회내역</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='where2' value='' disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <!-- 추후 api 작업 예상됨 -->
                            <label for="store_no">매장번호/매장명</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='store_no' id="store_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type="text" class="form-control form-control-sm search-enter" name="store_nm" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목구분</label>
                            <div class="flex_box">
                                <select name="item" id="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="CT">CLOTH TOP</option>
                                    <option value="CB">CLOTH BOTTOM</option>
                                    <option value="BA">BAG</option>
                                    <option value="SO">SHOES</option>
                                    <option value="AC">ACC</option>
                                    <option value="SM">SAMPLE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="as_type">수선구분</label>
                            <div class="flex_box">
                                <select id="as_type" name="as_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="C">고객수선</option>
                                    <option value="S">매장수선</option>
                                    <option value="H">본사수선</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/store/standard/std11/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
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
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<script language="javascript">

    const DEFAULT_STYLE = {'text-align': 'center'};

    const columns = [
        // this row shows the row index, doesn't use any data from the row
        { field: "idx", headerName: 'No', width:35, pinned:'left', maxWidth: 100, cellRenderer: 'loadingRenderer', 
            cellStyle: { ...DEFAULT_STYLE, 'font-size': '13px', 'font-weight': 500 },
            cellRenderer: (params) => {
                return `<a href="/store/standard/std11/detail/${params.value}" style="text-decoration: underline !important">${params.value}</a>`
            },
            pinned: 'left'
        },
        { field: "receipt_date", headerName: "접수일자", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "customer_no", headerName: "고객번호", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "customer", headerName: "고객명", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "as_type", headerName: "수선구분", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left',
            cellRenderer: (params) => {
                switch (params.value) {
                    case "C": 
                        return "고객수선";
                    case "S": 
                        return "매장수선";
                    case "H": 
                        return "본사수선";
                    default:
                        return params.value;
                };
            }
        },
        { field: "sale_date", headerName: "판매일자", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "h_receipt_date", headerName: "본사접수일", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "start_date", headerName: "수선인도일", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "due_date", headerName: "수선예정일", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "end_date", headerName: "수선완료일", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "receipt_no", headerName: "접수번호", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "store_no", headerName: "매장번호", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "store_nm", headerName: "매장명", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "item", headerName: "수선품목", width: 100, cellStyle: DEFAULT_STYLE,
            cellRenderer: (params) => {
                switch (params.value) {
                    case "CT": 
                        return "CLOTH TOP";
                    case "CB": 
                        return "CLOTH BOTTOM";
                    case "BA": 
                        return "BAG";
                    case "SO": 
                        return "SHOES";
                    case "AC": 
                        return "ACC";
                    case "SM": 
                        return "SAMPLE";
                    default:
                        return params.value;
                };
            }
        },
        { field: "product_cd", headerName: "제품코드", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "product", headerName: "제품명", width: 150, cellStyle: DEFAULT_STYLE },
        { field: "color", headerName: "칼라", width: 80, cellStyle: DEFAULT_STYLE },
        { field: "size", headerName: "사이즈", width: 80, cellStyle: DEFAULT_STYLE },
        { field: "quantity", headerName: "수량", width: 80, cellStyle: DEFAULT_STYLE },
        { field: "is_free", headerName: "수선유료구분", width: 80, cellStyle: DEFAULT_STYLE,
            cellRenderer: (params) => {
                switch (params.value) {
                    case "Y": 
                        return "유료";
                    case "N": 
                        return "무료";
                    default:
                        return params.value;
                };
            }
        },
        { field: "charged_price", headerName: "유료수선금액", width: 100, type: 'currencyType' },
        { field: "free_price", headerName: "무료수선금액", width: 100, type: 'currencyType' },
        { field: "mobile", headerName: "핸드폰", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "zipcode", headerName: "우편번호", width: 80, cellStyle: DEFAULT_STYLE },
        { field: "addr1", headerName: "주소1", width: 150, cellStyle: DEFAULT_STYLE },
        { field: "addr2", headerName: "주소2", width: 150, cellStyle: DEFAULT_STYLE },
        { field: "content", headerName: "수선내용", width: 180, cellStyle: DEFAULT_STYLE },
        { field: "h_explain", headerName: "본사설명", width: 180, cellStyle: DEFAULT_STYLE },
        { field: "storing_cd", headerName: "입고처코드", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "storing_nm", headerName: "입고처명", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "as_cd", headerName: "수선처코드", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "as_place", headerName: "수선처명", width: 100, cellStyle: DEFAULT_STYLE },
        { field: "", width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/standard/std11/search', data, -1, (data) => {});
    }

    const formReset = () => {
        document.search.reset();
    };

    const changeWhere1 = (obj) => {
        if (obj.value == "") {
            document.search.where2.disabled = true;
        } else {
            document.search.where2.disabled = false;
        }
    };

</script>

@stop
