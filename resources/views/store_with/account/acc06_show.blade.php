@extends('store_with.layouts.layout-nav')
@section('title','매장중간관리자정산 - ' . @$store_nm)
@section('content')

<form method="post" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>
					매장중간관리자정산 - {{ @$store_nm }}
					@if (@$closed_yn == 'Y')
					<span class="ml-2 p-1 pl-2 pr-2 fs-14 text-white bg-success rounded">정산완료</span>
					@elseif (@$closed_yn == 'N')
					<span class="ml-2 p-1 pl-2 pr-2 fs-14 text-white bg-danger rounded">정산처리중</span>
					@endif
				</h4>
				<div class="flex_box">
					<a href="javascript:void(0);" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					@if($acc_idx == '')
					<a href="javascript:void(0);" onclick="return Closed();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 closed_btn"><i class="fas fa-plus fa-sm"></i> 마감추가</a>
					@endif
					<a href="javascript:void(0);" onclick="gridDownload();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="fas fa-download fa-sm"></i> 자료받기</a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="sdate">판매일자</label>
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_no">매장명</label>
							<div class="form-inline inline_btn_box">
								<input type='hidden' id="store_nm" name="store_nm">
								<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_state">주문상태</label>
							<div class="flex_box">
								<select name='ord_state' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($ord_states as $ord_state)
										<option value='{{ $ord_state->code_id }}'>
											{{ $ord_state->code_val }}
										</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="clm_state">클레임상태</label>
							<div class="flex_box">
								<select name='clm_state' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($clm_states as $clm_state)
										<option value='{{ $clm_state->code_id }}'>
											{{ $clm_state->code_val }}
										</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="stat_pay_type">결제방법</label>
							<div class="form-inline">
								<div class="form-inline-inner" style="width:100%;">
									<div class="form-group flex_box">
										<div style="width:calc(100% - 65px);">
											<select name="stat_pay_type" class="form-control form-control-sm mr-2" style="width:100%;">
												<option value="">전체</option>
												@foreach ($stat_pay_types as $stat_pay_type)
													<option value='{{ $stat_pay_type->code_id }}'>
														{{ $stat_pay_type->code_val }}
													</option>
												@endforeach
											</select>
										</div>
										<div style="height:30px;margin-left:5px;">
											<div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="복합결제 제외">
												<input type="checkbox" class="custom-control-input" id="not_complex" name="not_complex" value="Y">
												<label for="not_complex" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
											</div>
										</div>
									</div>
								</div>
							</div>
                        </div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
						<label for="ord_type">주문구분</label>
							<div class="flex_box">
								<select name='ord_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($ord_types as $ord_type)
										<option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
									@endforeach
								</select>
							</div>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			@if($acc_idx == '')
			<a href="javascript:void(0);" onclick="return Closed();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 closed_btn"><i class="fas fa-plus fa-sm"></i> 마감추가</a>
			@endif
			<a href="javascript:void(0);" onclick="gridDownload();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="fas fa-download fa-sm"></i> 자료받기</a>
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
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	const CENTER = { 'text-align': 'center' };
	const columns = [
		{field: "num", headerName: "#", type: 'NumType', pinned: 'left', aggSum: "합계", cellStyle: CENTER, width: 40,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : parseInt(params.value) + 1,
		},
		{field: "sale_type", headerName: "매출구분", width: 55, pinned: 'left', cellStyle: (params) => ({...CENTER, "color": params.data.ord_state != 30 ? "#dd0000" : "none"})},
		{field: "state_date", headerName: "일자", width: 80, pinned: 'left', cellStyle: CENTER},
		{field: "ord_no", headerName: "주문번호", width: 140, pinned: 'left'},
		{field: "ord_opt_no", headerName: "일련번호", width: 60, cellStyle: CENTER, type: 'StoreOrderNoType', pinned: 'left'},
		{field: "multi_order", headerName: "복수주문", width: 60, pinned: 'left',
			cellStyle: (params) => ({ ...CENTER, "background-color": params.value === 'Y' ? "#ffff99" : "none" }),
			cellRenderer: (params) => params.node.rowPinned === 'top' 
				? '' : params.value === 'Y' 
					? `<a href="javascript:void(0);" onclick="return openStoreOrder('${params.data.ord_opt_no}');">${params.value}</a>` : "-",
		},
		{field: "prd_cd", headerName: "바코드", width: 125, cellStyle: CENTER},
		{field: "goods_no", headerName: "온라인코드",	width: 70, cellStyle: CENTER},
		{field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: CENTER},
		{field: "goods_nm", headerName: "상품명", width: 180, type: 'HeadGoodsNameType'},
		{field: "prd_cd_p", headerName: "품번",	width: 100, cellStyle: CENTER},
		{field: "color", headerName: "컬러", width: 55, cellStyle: CENTER},
		{field: "size", headerName: "사이즈", width: 55, cellStyle: CENTER},
		{field: "goods_opt", headerName: "옵션", width: 150},
		{field: "qty", headerName: "수량", width: 50, type: 'currencyType', aggregation: true},
		{field: "goods_sh", headerName: "정상가", width: 70, type: 'currencyType'},
		{field: "price", headerName: "판매가", width: 70, type: 'currencyType'},
		{field: "sale_amt",	headerName: "판매금액",	width: 90, type: 'currencyType', aggregation: true}, // 판매금액 = 수량 * 판매가
		{field: "clm_amt", headerName: "클레임금액", width: 90, type: 'currencyType', aggregation: true}, // 클레임금액 = 수량 * 판매가
		{field: "dc_amt", headerName: "할인금액",	width: 90, type: 'currencyType', aggregation: true}, // 할인금액 = dc_apply_amt
		{field: "coupon_amt", headerName: "쿠폰금액",	width: 90, type: 'currencyType', aggregation: true}, // 쿠폰금액 = coupon_apply_amt
		{field: "recv_amt", headerName: "매출금액",	width: 90, type: 'currencyType', aggregation: true, 
			cellStyle: (params) => ({"background-color": params.node.rowPinned === 'top' ? "none" : "#E9EFFF"})
		},
		{field: "ord_type_nm", headerName: "주문구분", width: 60, cellStyle: CENTER},
		{field: "pr_code_nm", headerName: "판매처수수료", width: 85, cellStyle: CENTER},
		{field: "store_cd",	headerName: "매장코드", width: 70, cellStyle: CENTER},
		{field: "store_nm",	headerName: "매장명", width: 100},
		{field: "user_nm", headerName: "주문자", width: 60, cellStyle: CENTER},
		{field: "pay_type_nm",	headerName: "결제방법",	width: 70, cellStyle: CENTER},
		{field: "tax_yn", headerName: "과세", width: 40, cellStyle: CENTER},
		{field: "ord_state_nm", headerName: "주문상태", width: 70, cellStyle: StyleOrdState},
		{field: "ord_date",	headerName: "출고완료일", width: 80, cellStyle: CENTER},
		{field: "clm_state_nm",headerName: "클레임상태", width: 70, cellStyle: StyleClmState},
		{field: "clm_end_date", headerName: "클레임완료일",	width: 80},
		{width: "auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {

		// 매장명 초기화 - select2, autocomplete input에 값 할당
        $("#store_no").select2({data:[{
            id: "{{ @$store_cd ? @$store_cd : '' }}",
            text: "{{ @$store_nm ? @$store_nm : '' }}"
        }], tags: true});
        document.search.store_nm.value = "{{ @$store_nm ? @$store_nm : '' }}";

		// ag-grid 설정
		pApp.ResizeGrid(100);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
			getRowStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return { 'background': '#eee', 'font-weight': 'bold' }
				}
			}
        };
		gx = new HDGrid(gridDiv, columns, options);
		gx.Aggregation({
			"sum": "top"
		});
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc06/show-search', data, -1);
	}

	function gridDownload() {
		gx.Download("정산상세내역.csv");
	}

	function Closed() {
		const store_cd = document.search.store_no.value;
		const store_nm = document.search.store_nm.value;
		const sdate = document.search.sdate.value;

		if(!confirm(`${sdate} [ ${store_nm} ] 정산내용을 마감내역에 추가하시겠습니까?`)) return;
		// alert("다소 시간이 소요될 수 있습니다. 잠시만 기다려주세요.");

		$.ajax({
			async: false,
			type: 'put',
			url: '/store/account/acc06/closed',
			data: {
				store_cd : store_cd,
				sdate : sdate
			},
			success: function(data) {
				cbClosed(data);
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});
	}

	function cbClosed(data){
		/*
			<에러코드 구분>
			000 : 성공
			100 : 부정확한 요청
			110 : 마감처리된 내역
			999 : 자료등록 시 오류
		*/

		const results = {
			"000": "마감내역을 정상적으로 추가완료했습니다.",
			"100": "부정확한 요청입니다.",
			"110": "이미 마감처리된 내역입니다.",
			"999": "마감정보추가 시 오류가 발생했습니다."
		}

		const ret = data.code;
		const msg = data.msg;

		if (ret === "000") {
			alert(results[ret]);
			location.reload();
			opener.Search();
		} else {
			alert(results[ret] + (ret === "100" ? `\n${msg}` : ""));
			console.log(data);
		}
	}
</script>

@stop