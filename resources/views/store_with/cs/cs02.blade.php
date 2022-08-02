@extends('store_with.layouts.layout')
@section('title','상품반품')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">상품반품</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 생산입고관리</span>
		<span>/ 상품반품</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="javascript:void(0);" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                    {{-- <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a> --}}
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

            <div class="card-body">
                <input type="hidden" name="goods_no" value="">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">반품일자</label>
                            <div class="form-inline date-select-inbox">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
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
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">반품창고</label>
                            <div class="d-flex">
                                <select name='storage_cd' class="form-control form-control-sm">
                                    @foreach (@$storages as $storage)
                                        <option value='{{ $storage->storage_cd }}'>{{ $storage->storage_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='prd_cd' id="prd_cd" value='' />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">상품구분</label>
                            <div class="flex_box">
                                <select name='type' id="type" class="form-control form-control-sm" style="width: 47%">
                                    <option value=''>전체</option>
                                    <option value='N'>일반</option>
                                    <option value='D'>납품</option>
                                    <option value='E'>기획</option>
                                </select>
                                <span class="text_line" style="width: 6%; text-align: center;">/</span>
                                <select name='goods_type' id="goods_type" class="form-control form-control-sm" style="width: 47%">
                                    <option value=''>전체</option>
                                    <option value='S'>매입</option>
                                    <option value='I'>위탁매입</option>
                                    <option value='P'>위탁판매</option>
                                    <option value='O'>구매대행</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_stat">상품상태</label>
                            <div class="flax_box">
                                <select name="goods_stat[]" id="goods_stat" class="form-control form-control-sm multi_select w-100" multiple>
                                    <option value=''>전체</option>
                                    @foreach ($goods_stats as $goods_stat)
                                        <option value='{{ $goods_stat->code_id }}' @if($goods_stat->code_id == 40) selected @endif>{{ $goods_stat->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버/상품번호</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-25 pr-1">
                                    <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        @foreach ($com_types as $com_type)
                                            <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box w-75">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_cd" name="com_cd" />
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company search-all search-enter" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm_eng">상품명(영문)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="return_date">반품일자</option>
                                        <option value="goods_no">상품번호</option>
                                        <option value="prd_cd">상품코드</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="javascript:void(0);" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	let columns = [
        {field: "idx", hide: true},
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 28},
        {field: "req_rt", headerName: "반품일자", width: 120, cellStyle: {"text-align": "center"}},
        // {field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 200},
        // {field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 200},
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no",	headerName: "상품번호", cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 350},
        {field: "goods_opt", headerName: "옵션", width: 350},
        {field: "qty", headerName: "반품수량", type: "numberType",
            cellStyle: {"font-weight": "700"},
        },
        {field: "price", headerName: "반품금액", type: "currencyType"},
        {field: "price", headerName: "반품단가", type: "currencyType"},
        {field: "comment", headerName: "메모", width: 300},
	];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

	function Search() {
		// let data = $('form[name="search"]').serialize();
		// gx.Request('/store/stock/stk20/search', data, 1);
	}

    // 상품반품등록 팝업 오픈
    const add = () => {
        const url = '/store/cs/cs02/show';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1735,height=800");
    };
</script>
@stop
