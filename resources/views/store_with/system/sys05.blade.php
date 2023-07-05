@extends('store_with.layouts.layout')
@section('title','환경관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">환경관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 시스템</span>
        <span>/ 환경관리</span>
    </div>
</div>

<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
{{--					<a href="/store/system/sys06" id="group_view" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-sync fs-16"></i> 그룹별 보기</a>--}}
{{--					<span class="mx-1">|</span>--}}
					<a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50 mr-1"></i> 조회</a>
					<a href="javascript:void(0);" onclick="return openCodePopup();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="type">구분</label>
							<div class="d-flex form-inline-inner input_box">
								<select id='type' name='type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($types as $key => $value)
										<option value='{{ $key }}'>{{ @$value }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">이름</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" id='name' name='name' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="idx">이름(일련번호)</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" id='idx' name='idx' value=''>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50 mr-1"></i> 조회</a>
			<a href="javascript:void(0);" onclick="return openCodePopup();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
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
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	const columns = [
		{ field: "type", hide: true },
		{ field: "type_nm", headerName: "구분", width: 80, cellClass: 'hd-grid-code' },
		{ field: "name", headerName: "이름", width: 200,
			cellRenderer: (params) => `<a href="javascript:void(0);" onClick="return openCodePopup('${params.data.type}', '${params.data.name}', '${params.data.idx}');">${params.value}</a>`,
		},
		{ field: "idx", headerName: "이름(일련번호)", width: 100 },
		{ field: "value", headerName: "값", width: 200 },
		{ field: "mvalue", headerName: "모바일값", width: 200 },
		{ field: "content", headerName: "내용", width: 200 },
		{ field: "desc", headerName: "세부설명", width: 200 },
		{ field: "rt", headerName: "등록일자", type: "DateTimeType" },
		{ field: "ut", headerName: "수정일자", type: "DateTimeType" },
		{ field: "admin_nm", headerName: "최종수정자", width: 80, cellClass: 'hd-grid-code' },
		{ width: 0 }
	];

	const pApp = new App('', { gridId: "#div-gd" });
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
		gx.Request('/store/system/sys05/search', data, -1);
	}

	function openCodePopup(type = '', name = '', idx = '') {
		let url = '/store/system/sys05/show';
		if (type !== '') {
			url += '?type=' + type + '&name=' + name + '&idx=' + idx;
		}
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=800,height=570");
	}
</script>
@stop
