@extends('store_with.layouts.layout')
@section('title','매장별매출현황')
@section('content')
<style>
	/* 기본옵션 ag grid 3단 가운데 정렬 css 적용 */
	.ag-header-row.ag-header-row-column-group + .ag-header-row.ag-header-row-column > .bizest.ag-header-cell {
        transform: translateY(-65%);
        height: 320%;
		padding-top: 2px;
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
	<h3 class="d-inline-flex">매장별매출현황</h3>
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
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="initSearch(['#store_no'])" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="sdate">판매기간(판매연월)</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box w-100">
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
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
									@foreach ($store_channel as $sc)
										<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_kind">마감상태</label>
							<div class="flex_box">
								<select name='closed_yn' id="closed_yn" class="form-control form-control-sm">
									<option value=''>전체</option>
									<option value='Z'>상태없음</option>
									<option value='N'>마감추가</option>
									<option value='Y'>마감완료</option>
								</select>
							</div>
                        </div>
					</div>	
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sale_yn">매출여부</label>
                            <div class="flex_box">
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sale_yn" id="sale_y" value="Y" class="custom-control-input" checked/>
                                        <label class="custom-control-label" for="sale_y">Y</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sale_yn" id="sale_n" value="N" class="custom-control-input"/>
                                        <label class="custom-control-label" for="sale_n">N</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <a href="#" onclick="initSearch(['#store_no'])" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
			<a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box flex_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
					<p id="current_date" class="ml-3 pl-2 pr-2 fs-14 text-white bg-secondary rounded"></p>
				</div>
				<div class="fr_box">
					<a href="javascript:void(0);" onclick="return openHelpModal();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fas fa-question-circle fa-sm"></i> 도움말</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<!-- 도움말 모달 -->
<div id="HelpModal" class="modal fade" role="dialog" aria-labelledby="HelpModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="min-width: 30%;max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="HelpModalLabel">도움말</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body show_layout" style="background:#f5f5f5;">
                <div class="card_wrap search_cum_form write">
                    <div class="card shadow">
                        <form name="search_prdcd_range" method="get" onsubmit="return false">
                            <div class="card-body">
								<p class="fs-12 mb-2"><i class="bx bx-won fs-16 text-primary"></i> <span class="fs-14 font-weight-bold mr-2">매출이익</span> 매출합계(-VAT) - 마일리지(-VAT) - 원가</p>
								<p class="fs-12 mb-2"><i class="bx bx-won fs-16 text-primary"></i> <span class="fs-14 font-weight-bold mr-2">수수료</span> 매출액(-VAT) x 수수료율 % 100</p>
								<p class="fs-12 mb-2"><i class="bx bx-won fs-16 text-primary"></i> <span class="fs-14 font-weight-bold mr-2">영업이익</span> 매출이익 - 판매수수료 - 중간관리자수수료 - 기타재반자료(인건비 + 본사부담금)</p>
								<p class="fs-12"><i class="bx bx-won fs-16 text-primary"></i> <span class="fs-14 font-weight-bold mr-2">영업이익율</span> 영업이익 % 매출합계 x 100</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
	const CLOSED_STATUS = { 'Y': '마감완료', 'N': '마감추가' };
	const PAYER = { 'C': '(본사부담)', 'S': '(매장부담)' };
	const CENTER = { 'text-align': 'center' };

    const columns = [
        { headerName: "#", field: "num", type: 'NumType', pinned: 'left', aggSum: "합계", cellStyle: CENTER, width: 30,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : (parseInt(params.value) + 1),
        },
		{ field: "closed_yn", headerName: "마감상태", pinned: 'left', width: 57,
            cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : (CLOSED_STATUS[params.value] || '-'),
            cellStyle: (params) => ({
                ...CENTER, 
                "background-color": params.value === 'Y' ? '#E2FFE0' : params.value === 'N' ? '#FFE9E9' : 'none',
                "color": params.value === 'Y' ? '#0BAC00' : params.value === 'N' ? '#ff0000' : 'none'
            }),
        },
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 60, cellStyle: CENTER },
        { field: "store_type_nm", headerName: "매장구분", pinned: 'left', width: 70, cellStyle: CENTER },
        { field: "store_nm", headerName: "매장명", pinned: 'left', type: 'StoreNameType', width: 150 },
        { field: "manager_nm", headerName: "매니저", pinned: 'left', width: 55, cellStyle: CENTER },
		{ field: "grade_nm", headerName: "수수료등급", pinned: 'left', width: 65, cellStyle: CENTER },
        { headerName: "매출",
            children: [
				{ field: "sales_amt", headerName: "매출합계", width: 100, headerClass: "merged-cell", type: 'currencyType', aggregation: true,
					cellRenderer: (params) => {
						if (params.value == undefined) return 0;
						if (params.node.rowPinned === 'top') return params.valueFormatted;
						return '<a href="#" onClick="openDetailPopup(\''+ params.data.store_cd +'\', \''+ params.data.acc_idx +'\')">' + params.valueFormatted +'</a>';
					}
				},
				{ field: "sales_amt_except_vat", headerName: "매출합계(-VAT)", width: 100, headerClass: "merged-cell", type: 'currencyType', aggregation: true },
				{ field: "extra_M1_amt", headerName: "마일리지(-VAT)", width: 100, headerClass: "merged-cell", type: 'currencyType', aggregation: true },
				{ field: "wonga_amt", headerName: "원가", width: 100, headerClass: "merged-cell", type: 'currencyType', aggregation: true },
				{ field: "sales_profit", headerName: "매출이익", width: 100, headerClass: "merged-cell", type: 'currencyType', aggregation: true,
					cellStyle: (params) => params.node.rowPinned === 'top' ? '' : ({ 'color': '#0BAC00', 'background-color': '#fafffa' })
				},
            ]
        },
        { headerName: "판매 수수료",
            children: [
				@foreach (@$pr_codes as $pr_code)
                { headerName: "{{ @$pr_code->code_val }}",
                    children: [
						{ headerName: "매출액", field: "sales_{{ @$pr_code->code_id }}_amt", type: 'currencyType', width: 90, aggregation: true },
						{ headerName: "매출액(-VAT)", field: "sales_{{ @$pr_code->code_id }}_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "sales_{{ @$pr_code->code_id }}_fee_rate", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "sales_{{ @$pr_code->code_id }}_fee", type: 'currencyType', width: 100, aggregation: true },
                    ]
                },
				@endforeach
                { headerName: "수수료 합계", field: "sales_fee", type: 'currencyType', width: 100, headerClass: "merged-cell", aggregation: true,
					cellStyle: (params) => params.node.rowPinned === 'top' ? '' : ({ 'color': '#ff2222', 'background-color': '#fffafa', 'background-color': '#fffafa' }) 
				},
                // { headerName: "임대관리비", field: "management_fee", type: 'numberType', width: 100, headerClass: "merged-cell" },
            ]
        },
		{ headerName: "중간관리자 수수료",
            children: [
                { headerName: "정상1",
                    children: [
                        { headerName: "매출액", field: "ord_JS1_amt", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "매출액(-VAT)", field: "ord_JS1_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee1", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS1", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "정상2",
                    children: [
						{ headerName: "매출액", field: "ord_JS2_amt", type: 'currencyType', width: 90, aggregation: true },
						{ headerName: "매출액(-VAT)", field: "ord_JS2_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee2", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS2", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "정상3",
                    children: [
						{ headerName: "매출액", field: "ord_JS3_amt", type: 'currencyType', width: 90, aggregation: true },
						{ headerName: "매출액(-VAT)", field: "ord_JS3_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee3", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS3", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "특가",
                    children: [
						{ headerName: "매출액", field: "ord_TG_amt", type: 'currencyType', width: 90, aggregation: true },
						{ headerName: "매출액(-VAT)", field: "ord_TG_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee_10", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_TG", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "용품",
                    children: [
						{ headerName: "매출액", field: "ord_YP_amt", type: 'currencyType', width: 90, aggregation: true },
						{ headerName: "매출액(-VAT)", field: "ord_YP_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee_11", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_YP", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "특가(온라인)",
                    children: [
						{ headerName: "매출액", field: "ord_OL_amt", type: 'currencyType', width: 90, aggregation: true,
							cellRenderer: (params) => ['0', null].includes(params.value) ? 0 : (params.node.rowPinned === 'top' ? params.valueFormatted 
								: '<a href="javascript:void(0);" onClick="openOnlineFeePopup(\''+ params.data.store_cd +'\', \''+ params.data.acc_idx +'\')">' + params.valueFormatted +'</a>')
						},
						{ headerName: "매출액(-VAT)", field: "ord_OL_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee_12", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_OL", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
				{ headerName: "수수료 합계", field: "fee_amt", type: 'currencyMinusColorType', width: 90, headerClass: "merged-cell", aggregation: true,
					cellStyle: (params) => params.node.rowPinned === 'top' ? '' : ({ 'color': '#ff2222', 'background-color': '#fffafa' }) 
				},
            ]
        },
		{ headerName: "기타재반자료",
			children: [
				@foreach (@$extra_types as $entry_cd => $children)
					@if (true)
					{ headerName: `{{ $children[0]->entry_nm }} ${ PAYER["{{ $children[0]->payer }}"] || '' }`,
						children: [
							@foreach ($children as $child)
								{ headerName: "{{ $child->type_nm }}", field: "extra_{{ $child->type_cd }}_amt", type: 'currencyType', width: 90, aggregation: true },
							@endforeach
							{ headerName: "합계", field: "extra_{{ $entry_cd }}_sum", type: 'currencyType', width: 90, aggregation: true,
								cellStyle: (params) => params.node.rowPinned === 'top' ? '' : ({ 'color': '#ff2222', 'background-color': '#fffafa' }) 
							},
						]
					},
					@endif
				@endforeach
			]
		},
        { field: "real_profit", headerName: "영업이익", type: 'currencyType', width: 100, cellStyle: { 'font-weight': '700' }, aggregation: true, },
        { field: "real_profit_rate", headerName: "영업이익율(%)", type: 'percentType', width: 100, cellStyle: { 'font-weight': '700' } },
        { width: "auto" }
    ];

</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd", height: 265 });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
		});
		gx.Aggregation({ "sum": "top" });

		Search();

		//매장 다중 선택
		$(".sch-store").on("click", function() {
			searchStore.Open(null, "multiple");
        });
	});

	function Search() {
		const data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc04/search', data, -1, function(d) {
			$("#current_date").text(d.head.date);
		});
	}

	// 판매내역 상세
	function openDetailPopup(store_cd, acc_idx) {
		const sdate = $('input[name="sdate"]').val();
		if (acc_idx !== 'null') {
			let url = '/store/account/acc07/show/' + acc_idx;
			window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=2100,height=1200");
		} else {
			url = '/store/account/acc06/show/' + store_cd + '/' + sdate;
			window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
		}
	}

	// 특약(온라인) 판매내역 상세
	function openOnlineFeePopup(store_cd, acc_idx) {
		const sdate = $('input[name="sdate"]').val();
		if (acc_idx !== 'null') {
			let url = '/store/account/acc07/show/' + acc_idx;
			window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=2100,height=1200");
		} else {
			utl = '/store/account/acc06/show-online?store_cd=' + store_cd + '&sdate=' + sdate;
			window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
		}
	}

	// 도움말 모달 오픈
	function openHelpModal() {
        $("#HelpModal").draggable();
        $('#HelpModal').modal({ keyboard: false });
    }

	// 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
	$(document).ready(function() {
		const store_channel = document.getElementById("store_channel");
		const store_channel_kind = document.getElementById("store_channel_kind");

		store_channel.addEventListener("change", () => {
			if (store_channel.value) {
				store_channel_kind.disabled = false;
			} else {
				store_channel_kind.disabled = true;
			}
		});
	});

	// 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
	function chg_store_channel() {

		const sel_channel = document.getElementById("store_channel").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel',
			data: {
				'store_channel' : sel_channel
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#store_channel_kind').append(option);
					}

				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}	

</script>
@stop
