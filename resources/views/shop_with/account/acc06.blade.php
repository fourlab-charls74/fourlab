@extends('shop_with.layouts.layout')
@section('title','매장중간관리자정산')
@section('content')

<style>
	/* 기본옵션 ag grid 3단 가운데 정렬 css 적용 */
	.basic-option .ag-header-row.ag-header-row-column-group + .ag-header-row.ag-header-row-column > .bizest.ag-header-cell {
        transform: translateY(-65%);
        height: 320%;
    }

	/**
	 * 3단이 포함되지 않은 2단 셀 깨지는 부분 css 처리
	 */
	.merged-cell {
		height: 200%;
		top: -107%;
		padding-top: 4px;
	}
</style>
<div class="page_tit">
	<h3 class="d-inline-flex">매장중간관리자정산</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 경영관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="sdate">판매기간(판매연월)</label>
							<div class="docs-datepicker flex_box">
								<div class="input-group">
									<input type="text" id="sdate" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
									<div class="input-group-append">
										<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
											<i class="fa fa-calendar" aria-hidden="true"></i>
										</button>
									</div>
								</div>
								<div class="docs-datepicker-container"></div>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_type">매장구분</label>
							<div class="flex_box">
								<select name='store_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_types as $store_type)
										<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_kind">매장종류</label>
							<div class="flex_box">
								<select name='store_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_kinds as $store_kind)
										<option value='{{ $store_kind->code_id }}'>{{ $store_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
                        </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
					</div>					
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="initSearch()"> -->
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive basic-option">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
    var columns = [
        { headerName: "#", field: "num", type:'NumType', pinned:'left', cellStyle: { 'text-align': "center" },
            cellRenderer: function (params) {
                if (params.node.rowPinned === 'top') {
                    return "합계";
                } else {
                    return parseInt(params.value) + 1;
                }
            }
        },
        { field: "store_type_nm", headerName: "매장구분", pinned:'left', width:90, cellStyle: { 'text-align': "center" } },
        { field: "store_cd", headerName: "매장코드", pinned:'left', hide: true },
        { field: "store_nm", headerName: "매장명", pinned:'left', type: 'StoreNameType', width: 250 },
        { field: "", headerName: "매니저", pinned:'left',width: 70 },
        { field: "name", headerName: "등급", pinned:'left', width: 100},
        { field: "sale_status", headerName: "매출",
			children: [
				{ headerName: "소계", field: "ord_amt", type: 'currencyMinusColorType', width: 100,
					headerClass: "merged-cell",
					cellRenderer: (params) => {
						if ( params.value != undefined ) {
							return '<a href="#" onClick="popDetail(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>';
						} else {
							return 0;
						}
					}
				},
				@foreach (@$pr_codes as $pr_code)
				{ 
					headerName: "{{ $pr_code->code_val }}", field: "amt_{{ $pr_code->code_id }}", 
					type: 'currencyMinusColorType', width: 100,
					headerClass: "merged-cell",
				 },
				@endforeach
			]
        },
        { field: "sale_fee", headerName: "수수료",
            children: [
                { headerName: "소계", field: "s1", width:100,
					type: 'currencyMinusColorType', 
					valueFormatter: (params) => formatNumber(params),
					valueGetter: (params) => sumSaleFees(params),
					headerClass: "merged-cell"
				},
                { headerName: "정상", field: "s2", type: 'numberType', width:100,
                    children: [
                        { headerName: "수수료율", field: "fee1", type: 'percentType', width:100 },
                        { headerName: "수수료", field: "fee_amt_js1", type: 'currencyMinusColorType', width:100 },
                    ]
                },
                { headerName: "정상2", field: "s3", type: 'numberType', width:100,
                    children: [
                        { headerName: "수수료율", field: "fee2", type: 'percentType', width:100 },
                        { headerName: "수수료", field: "fee_amt_js2", type: 'currencyMinusColorType', width:100 },
                    ]
                },
                { headerName: "정상3", field: "s4", type: 'numberType', width:100,
                    children: [
                        { headerName: "수수료율", field: "fee3", type: 'percentType', width:100 },
                        { headerName: "수수료", field: "fee_amt_js3", type: 'currencyMinusColorType', width:100 },
                    ]
                },
                { headerName: "특가", field: "s5", type: 'numberType', width:100,
					// 행사로 해놓았음 추후 특가 반영 필요 (미구현)
                    children: [
                        { headerName: "수수료율", field: "fee_10", type: 'percentType', width:100 },
                        { headerName: "수수료", field: "fee_amt_gl", type: 'currencyMinusColorType', width:100 },
                    ]
                },
                { headerName: "용품", field: "s6", type: 'numberType', width:100,
                    children: [
                        { headerName: "수수료율", field: "fee_11", type: 'percentType', width:100 },
                        { headerName: "수수료", field: "fee_amt_j2", type: 'currencyMinusColorType', width:100 },
                    ]
                },
                { headerName: "특가(온라인)", field: "s7", type: 'numberType', width:100,
					// 균일로 해놓았음 추후 특가 반영 필요 (미구현)
                    children: [
                        { headerName: "수수료율", field: "fee_12", type: 'percentType', width:100 },
                        { headerName: "수수료", field: "fee_amt_j1", type: 'currencyMinusColorType', width:100 },
                    ]
                },
            ]
        },
        { field: "extra_total", headerName: "기타재반", type: 'currencyMinusColorType' },
        { field: "", headerName: "수수료+기타재반", type: 'currencyMinusColorType',
			valueFormatter: (params) => formatNumber(params),
			valueGetter: (params) => sumFeeExtra(params)
		},
        { headerName: "", field: "nvl", width: "auto" }
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
		gx.Request('/shop/account/acc06/search', data, -1);
	}

	const sumSaleFees = (params) => {
		const row = params.data;
		const sum = parseInt(row.fee_amt_js1) + parseInt(row.fee_amt_js2) + parseInt(row.fee_amt_js3)
			+ parseInt(row.fee_amt_gl) + parseInt(row.fee_amt_j1) + parseInt(row.fee_amt_j2);
		return isNaN(sum) ? 0 : sum;
	};

	const sumFeeExtra = (params) => {
		const extra = parseInt(params.data.extra_total);
		const sum = sumSaleFees(params) + extra;
		return isNaN(sum) ? 0 : sum;
	};

	function popDetail(store_cd) {
		let sdate	= $('input[name="sdate"]').val();
		const url	='/shop/account/acc06/show/' + store_cd + '/' + sdate;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
	}

</script>
@stop
