@extends('head_with.layouts.layout')
@section('title','광고할인')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">광고할인관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 광고할인관리</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openAddDCPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sale_name">할인명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='name' id="sale_name" value='' />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">사용</label>
                            <div class="flax_box">
                                <select name='use_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">사용</option>
                                    <option value="N">미사용</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">범위</label>
                            <div class="flax_box">
                                <select name="dc_range" id="dc_range" class="form-control form-control-sm">
                                    <option value="A">전체</option>
                                    <option value="G">상품</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">쿠폰제한</label>
                            <div class="flax_box">
                                <select name="limit_coupon_yn" id="limit_coupon_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">제한</option>
                                    <option value="N">제한 없음</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">적립금제한</label>
                            <div class="flax_box">
                                <select name="limit_point_yn" id="limit_point_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">제한</option>
                                    <option value="N">제한 없음</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="state">적립금지급</label>
                            <div class="flax_box">
                                <select name="add_point_yn" id="add_point_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">지급</option>
                                    <option value="N">지급 없음</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddDCPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
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
    const columnDefs = [
        {headerName: '#', width: 35, valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellClass: 'hd-grid-code', pinned: 'left'},
        {field: "name", headerName: "할인명", width: 100,
            cellRenderer: function(params) {
                return `<a href="#" onClick="openDetailPopup('${params.data.no}')">${params.value}</a>`;
            }
        },
        {field: "use_yn", headerName: "사용", width: 45, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
				if(params.value === 'Y') return "사용"
				else if(params.value === 'N') return "미사용"
                else return params.value
			}
        },
        {field: "dc_range", headerName: "범위", width: 45, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
				if(params.value === 'A') return "전체"
				else if(params.value === 'G') return "상품"
                else return params.value
			}
        },
        {field: "dc_rate", headerName: "할인율(%)", type:'percentType', width: 70},
        {field: "dc_amt", headerName: "할인금액(원)", type:'currencyType', width: 80},
        {field: "date_from", headerName: "할인기간 시작", width: 90, cellClass: 'hd-grid-code'},
        {field: "date_to", headerName: "할인기간 종료", width: 90, cellClass: 'hd-grid-code'},
        {field: "limit_margin_rate", headerName: "마진율제한(%)", type:'percentType', width: 90},
        {field: "limit_coupon_yn", headerName: "쿠폰제한", width:58,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value === 'Y') return "제한"
				else if(params.value === 'N') return "제한 없음"
                else return params.value
			}
        },
        {field: "limit_point_yn", headerName: "적립금제한", width:70,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value === 'Y') return "제한"
				else if(params.value === 'N') return "제한 없음"
                else return params.value
			}
        },
        {field: "add_point_yn", headerName: "적립금지급", width:70,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value === 'Y') return "지급"
				else if(params.value === 'N') return "지급 없음"
                else return params.value
			}
        },
        {field: "add_point_rate", headerName: "추가적립율(%)", type:'percentType', width: 90},
        {field: "add_point_amt", headerName: "추가적립금액(원)", type:'currencyType', width: 105},
        {field: "admin_nm", headerName: "관리자명", cellClass: 'hd-grid-code'},
        {field: "rt", headerName: "등록일시", type: 'DateTimeType'},
        {field: "ut", headerName: "수정일시", type: 'DateTimeType'},
        {field: "", headerName: "", width: 0}
    ];

    const pApp = new App('', { gridId: "#div-gd" });
	let gx;

	$(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		const gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columnDefs);

		Search();
	});

    function Search(){
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/standard/std11/search', data);
    }

    function openDetailPopup(no) {
        const url='/head/standard/std11/show/dc/' + no;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
    }
    
    function openAddDCPopup() {
        const url='/head/standard/std11/show/dc/';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=710");
    }
</script>
@stop
