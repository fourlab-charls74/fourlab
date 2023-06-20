@extends('store_with.layouts.layout')
@section('title','기타재반자료')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">기타재반자료</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 정산관리</span>
		<span>/ 기타재반자료</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="#" onclick="return openExtraPopup();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="fas fa-plus fa-sm"></i> 등록</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="sdate">판매기간(판매연월)</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $sdate }}" autocomplete="off">
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
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="return openExtraPopup();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="fas fa-plus fa-sm"></i> 등록</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
        <div class="card-title mb-2">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	const PAYER = { 'C': '(본사부담)', 'S': '(매장부담)' };

	const YELLOW = { 'background-color': '#ffff99' };
	const CENTER = { 'text-align': 'center' };

    const columns = [
		{ field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28 },
        { field: "ymonth", headerName: "판매연월", pinned: 'left', width: 70, cellStyle: { ...CENTER, "text-decoration": "underline", "text-decoration-color": "blue" },
			cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return openExtraPopup('${params.value}');">${params.value.slice(0,4) + '-' + params.value.slice(4,6)}</a>`,
		},
		@foreach ($extra_cols as $entry_cd => $children)
			@if ($entry_cd !== '')
				{ headerName: `{{ $children[0]->entry_nm }} ${ PAYER["{{ $children[0]->payer }}"] || '' }`,
					children: [
						@foreach ($children as $child)
							{ headerName: "{{ $child->type_nm }}", field: "{{ $child->type_cd }}_amt", type: 'currencyType', width: 100 },
							@if ($child->except_vat_yn === 'Y')
							{ headerName: "{{ $child->type_nm }}(-VAT)", field: "{{ $child->type_cd }}_novat", type: 'currencyType', width: 105,
								cellRenderer: (params) => params.data["{{ $child->type_cd }}_amt"] ? Comma(Math.round((params.data["{{ $child->type_cd }}_amt"] || 0) / 1.1)) : 0,
							},
							@endif
						@endforeach
						@if (!in_array($entry_cd, ['M', 'O']))
						{ headerName: "소계", field: "{{ $entry_cd }}_sum", type: 'currencyType', width: 100 },
						@endif
					]
				},
			@else
				@foreach ($children as $child)
					{ headerName: `{{ $child->type_nm }} ${ PAYER["{{ $child->payer }}"] || '' }`, field: "{{ $child->type_cd }}_sum", type: 'currencyType', width: 120 },
				@endforeach
			@endif
		@endforeach
		{ field: "S_total", headerName: "매장부담금 합계", type: 'currencyType', width: 100, cellStyle: {"font-weight": "700"} }, // 공제금
		{ field: "C_total", headerName: "본사부담금 합계", type: 'currencyType', width: 100, cellStyle: {"font-weight": "700"} }, // 추가지급금
        { width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId: "#div-gd", height: 270 });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(270);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);

		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc05/search', data, -1);
	}

	// 기타재반상세팝업 오픈
	function openExtraPopup(date = '') {
		const url = '/store/account/acc05/show' + (date !== '' ? '?date=' + date : '');
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=2100,height=1200");
	}
</script>
@stop
