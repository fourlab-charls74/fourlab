@extends('store_with.layouts.layout')
@section('title','기타재반자료')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">기타재반자료</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 정산/마감관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="return openExtraPopup();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="fas fa-plus fa-sm"></i> 추가</a>
					<a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
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
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="return openExtraPopup();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="fas fa-plus fa-sm"></i> 추가</a>
			<a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
			<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
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
	const YELLOW = {'background-color': "#ffff99"};
	const CENTER = { 'text-align': 'center' };
    const columns = [
		{ field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28 },
        { field: "ymonth", headerName: "판매연월", pinned: 'left', width: 70, cellStyle: {...CENTER, "text-decoration": "underline"},
			cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return openExtraPopup('${params.value}');">${params.value.slice(0,4) + '-' + params.value.slice(4,6)}</a>`,
		},
		{ field: "total_amt", headerName: "총합계", type: 'currencyType', width: 100, cellStyle: {"background-color": "#ededed", "font-weight": "bold"} },
		@foreach ($extra_cols as $group_nm => $children)
		{ headerName: "{{ $group_nm }}",
			children: [
				@foreach ($children as $child)
					{ headerName: "{{ $child->code_val }}", field: "{{ $child->code_id }}_amt", type: 'currencyType', width: 100 },
					@if (in_array($child->code_id, ['P1', 'M3']))
					{ headerName: "{{ $child->code_val }}(-VAT)", field: "{{ $child->code_id }}_novat", type: 'currencyType', width: 105,
						cellRenderer: (params) => params.data["{{ $child->code_id }}_amt"] ? Comma(Math.round((params.data["{{ $child->code_id }}_amt"] || 0) / 1.1)) : 0,
					},
					@endif
				@endforeach
				@if (!in_array($group_nm, ['마일리지', '기타운영경비']))
				{ headerName: "소계", field: "{{ str_split($children[0]->code_id ?? '')[0] }}_sum", type: 'currencyType', width: 100 },
				@endif
			]
        },
		@if ($group_nm === '관리')
		{ field: "G_sum", headerName: "사은품", type: 'currencyType', width: 100 },
		{ field: "S_sum", headerName: "소모품", type: 'currencyType', width: 100 },
		@endif
		@endforeach
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
