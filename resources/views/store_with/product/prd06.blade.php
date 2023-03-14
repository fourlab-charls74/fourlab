@extends('store_with.layouts.layout')
@section('title','온라인 재고 매핑')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">온라인 재고 매핑</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 재고</span>
        <span>/ 온라인 재고 매핑</span>
    </div>
</div>

<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" data-code="" onclick="openCodePopup(this)" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 설정</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">일자</label>
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
							<label for="price_apply">가격 반영</label>
							<div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="price_apply_yn" id="price_apply_yn_" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="price_apply_yn_">전체</label>
                                </div>
								<div class="custom-control custom-radio">
									<input type="radio" name="price_apply_yn" id="price_apply_y" class="custom-control-input" value="Y">
									<label class="custom-control-label" for="price_apply_y">예</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="price_apply_yn" id="price_apply_n" class="custom-control-input" value="N">
									<label class="custom-control-label" for="price_apply_n">아니오</label>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_buffer">매장버퍼링 유형</label>
							<div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="store_buffer" id="store_buffer_" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="store_buffer_">전체</label>
                                </div>
								<div class="custom-control custom-radio">
									<input type="radio" name="store_buffer" id="store_buffer_a" class="custom-control-input" value="A">
									<label class="custom-control-label" for="store_buffer_a">통합</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="store_buffer" id="store_buffer_s" class="custom-control-input" value="S">
									<label class="custom-control-label" for="store_buffer_s">개별</label>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 설정</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

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
<script>
    const columns = [
        {
            headerName: '#',
            width:35,
            type:'NumType',
            cellStyle: {"background":"#F5F7F7", "text-align":"center"}
        },
        {
            field: "rt",
            headerName: "등록일",
            width: 200,
            cellClass: 'hd-grid-code'
        },
        {
            field: "store_cnt",
            headerName: "매장 수",
            width: 100,
            cellClass: 'hd-grid-code'
        },
        {
            field: "match_y_cnt",
            headerName: "매칭상품 수",
            width: 100,
            cellClass: 'hd-grid-code'
        },
        {
            field: "match_n_cnt",
            headerName: "비매칭 상품 수",
            width: 100,
            cellClass: 'hd-grid-code'
        },
        {
            field: "price_apply_yn",
            headerName: "가격반영",
            width: 80,
            cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value == 'Y') return "예"
                else if(params.value == 'N') return "아니오"
                else return params.value
            }
        },
        {
            field: "store_buffer_kind",
            headerName: "매장 버퍼링 유형",
            width: 100,
            cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                if(params.value == 'A') return "통합"
                else if(params.value == 'S') return "개별"
                else return params.value
            }
        },
        {
            field: "id",
            headerName: "담당자",
            width: 80,
            cellClass: 'hd-grid-code'
        },
        {
            field: "",
            headerName: "",
            width: "auto"
        }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/product/prd06/search', data, 1);
    }

    function openCodePopup(a) {
        let url = '/store/product/prd06/create';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
    }
    
</script>
@stop