@extends('head_with.layouts.layout')
@section('title','배송출고요청')
@section('content')

<style>
    input[type="text"]::placeholder {
        color: #aaa;
        text-align: right;
    }
</style>

<div class="page_tit">
	<h3 class="d-inline-flex">배송출고요청</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 주문&amp;배송</span>
		<span>/ 배송출고요청</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-firstname-input">주문일자</label>
							<div class="form-inline date-select-inbox">
								<select name="date_type" class="form-control form-control-sm" onchange="return UserFromToDate(this.value,document.search,'sdate','edate');" style="width:23%;margin-right:2%;">
									<option value="0" selected>사용자</option>
									<option value="1D">금일</option>
									<option value="2D">어제</option>
									<option value="7D">최근1주</option>
									<option value="14D">최근2주</option>
									<option value="1M">최근1달</option>
									<option value="0R">금월</option>
									<option value="1R">전월</option>
								</select>
								<div class="docs-datepicker form-inline-inner" style="width:35%;">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" onchange="onChangeDate(this)" disable>
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
												<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
								<span class="text_line" style="width:5%;">~</span>
								<div class="docs-datepicker form-inline-inner" style="width:35%;">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off" onchange="onChangeDate(this)">
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
							<label for="dlv_kind">주문상태/배송방식</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select name='ord_state' class="form-control form-control-sm">
											<option value=''>전체</option>
											@foreach ($ord_states as $ord_state)
											<option value='{{ $ord_state->code_id }}' {{ ($ord_state->code_id == '10') ? 'selected' : '' }}>
												{{ $ord_state->code_val }}
											</option>
											@endforeach
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select name='dlv_type' class="form-control form-control-sm">
											<option value=''>전체</option>
											@foreach ($dlv_types as $dlv_type)
											<option value='{{ $dlv_type->code_id }}'>{{ $dlv_type->code_val }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">배송구분</label>
							<div class="flax_box">
								<select name='dlv_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($dlv_kinds as $dlv_kind)
									<option value='{{ $dlv_kind->code_id }}'>{{ $dlv_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- 판매처 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-inputZip">판매처</label>
							<div class="flax_box">
								<select name='sale_place' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($sale_placies as $sale_place)
									<option value='{{ $sale_place->id }}'>{{ $sale_place->val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<!-- 업체 -->
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
                                        <input type="hidden" id="com_id" name="com_id">
										<input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%">
										<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>

							</div>
						</div>
					</div>
					<!-- 주문자/수령자 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">주문자/수령자</label>
							<div class="form-inline inline_input_box">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='user_nm' value=''>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' value=''>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- 상품구분 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-inputState">상품구분</label>
							<div class="flax_box">
								<select name='goods_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_types as $goods_type)
									<option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<!-- 스타일넘버 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-inputState">스타일넘버</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all ac-style-no search-enter" name='style_no' value=''>
							</div>
						</div>
					</div>
					<!-- 재고수량 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="style_no">재고수량</label>
							<div class="form-inline inline_input_box">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type="text" class="form-control form-control-sm search-enter" name="wqty_low" id="wqty_low" value="" placeholder="이상">
									</div>
								</div>
								<span class="text_line">~</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type="text" class="form-control form-control-sm search-enter" name="wqty_high" id="wqty_high" value="" placeholder="이하">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row align-items-center d-none search-area-ext">
					<!-- 주문번호 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">주문번호</label>
							<div class="flax_box">
								<input type="text" class="form-control form-control-sm search-all search-enter" name="ord_no" id="ord_no" value="">
							</div>
						</div>
					</div>
					<!-- 주문구분 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_type">주문구분</label>
							<div class="flax_box">
								<select name='ord_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($ord_types as $ord_type)
									<option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<!-- 출고구분 -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_kind">출고구분</label>
							<div class="flax_box">
								<select name='ord_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($ord_kinds as $ord_kind)
									<option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row align-items-center d-none search-area-ext">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_type">품목</label>
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="dlv_kind">상품명</label>
							<div class="flax_box">
								<input type="text" class="form-control form-control-sm ac-goods-nm search-enter" name="goods_nm" value="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
				</div>
				<div class="fr_box flax_box">
					<div class="custom-control custom-checkbox form-check-box mr-2" style="display:inline-block;">
						<input type="checkbox" name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
						<label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
					</div>
					<div class="custom-control custom-checkbox form-check-box">
						<input type="checkbox" name="chk_ord_no" id="chk_ord_no" class="custom-control-input" checked="">
						<label class="custom-control-label text-left" for="chk_ord_no" style="line-height:30px;justify-content:left">주문단위로 품절검사, </label>
					</div>
					<span style="font-weight:500;line-height:30px;margin-left:5px;vertical-align:middle;" class="mr-1">출고차수 :</span>
					<div class="mr-1">
						<input type="text" id="dlv_series_no" class="form-control form-control-sm" name="dlv_series_no" value="{{date('YmdH')}}">
					</div>
					<a href="#" onclick="updateState()" class="btn btn-sm  btn-primary shadow mr-1">출고처리중 변경</a>
					<div class="mr-1">
						<select id="u_ord_kind" class="form-control form-control-sm" style="width:120px;">
							@foreach ($ord_kinds as $ord_kind)
							<option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
							@endforeach
						</select>
					</div>
					<a href="#" onclick="updateKind()" class="btn-sm btn btn-primary">출고상태 변경</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	var columns = [
		{
			headerName: '#',
			width: 35,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			pinned: 'left',
			cellClass: 'hd-grid-code'
		},
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection:function(params){ return (params.data.ord_kind_nm == '출고가능' || params.data.ord_kind_nm == '정상')? true:false; },
			width: 28,
			pinned: 'left',
		},
		{
			field: "ord_type_nm",
			headerName: "주문구분",
			width: 58,
			pinned: 'left',
			cellClass: 'hd-grid-code'
		},
		{
			field: "ord_kind_nm",
			headerName: "출고구분",
			width: 58,
			cellStyle: StyleOrdKind,
			pinned: 'left'
		},
		{
			field: "ord_no",
			headerName: "주문번호",
			width: 130,
			cellStyle: StyleOrdNo,
			type: 'HeadOrderNoType',
			pinned: 'left'
		},
		{
			field: "ord_opt_no",
			headerName: "일련번호",
			width: 58,
			sortable: "ture",
			type: 'HeadOrderNoType',
			pinned: 'left'
		},
		{
			field: "ord_state_nm",
			headerName: "주문상태",
			width: 70,
			cellStyle: StyleOrdState
		},
		{
			field: "pay_stat_nm",
			headerName: "입금상태",
			width: 58,
			cellClass: 'hd-grid-code'
		},
		{
			field: "dlv_type",
			headerName: "배송방식",
			width: 58,
			cellClass: 'hd-grid-code'
		},
		{
			field: "clm_state_nm",
			headerName: "클레임상태",
			width: 70,
			cellClass: 'hd-grid-code',
			cellStyle: StyleClmState
		},
		{
			field: "goods_type_nm",
			headerName: "상품구분",
			width: 58,
			cellStyle: StyleGoodsTypeNM,
		},
		{
			field: "style_no",
			headerName: "스타일넘버",
			cellClass: 'hd-grid-code'
		},
		{
			field: "img",
			headerName: "이미지",
			width: 65,
			hide: true,
			type: "GoodsImageType"
		},
		{
			field: "goods_nm",
			headerName: "상품명",
			type: "GoodsNameType"
		},
		{
			field: "opt_val",
			headerName: "옵션",
			width: 100
		},
		{
			field: "sale_qty",
			headerName: "수량",
			width: 46,
			type: 'currencyType'
		},
		{
			field: "qty",
			headerName: "온라인재고",
			width: 70,
			type: 'currencyType',
			cellRenderer: function(params) {
				return '<a href="#" onclick="return openHeadStock(' + params.data.goods_no + ',\'' + params.data.opt_val +'\');">' + params.value + '</a>';
			}
		},
		{
			field: "wqty",
			headerName: "보유재고",
			width: 58,
			type: 'currencyType',
			cellStyle: function(params) {
				return {"background": parseInt(params.value) === 0 && params.data.goods_type === "S" ? "#FF8040" : 'none'};
			},
			cellRenderer: function(params) {
				return '<a href="#" onclick="return openHeadStock(' + params.data.goods_no + ',\'' + params.data.opt_val +'\');">' + params.value + '</a>';
			}
		},
		{
			field: "price",
			headerName: "판매가",
			width:60,
			type: 'currencyType'
		},
		{
			field: "sale_amt",
			headerName: "쿠폰/할인",
			width: 72,
			type: 'currencyType'
		},
		{
			field: "gift",
			headerName: "사은품",
			width:60
		},
		{
			field: "dlv_amt",
			headerName: "배송비",
			width:60,
			type: 'currencyType'
		},
		{
			field: "pay_type",
			headerName: "결제방법",
			width: 80,
			cellClass: 'hd-grid-code'
		},
		{
			field: "user_nm",
			headerName: "주문자(아이디)"
		},
		{
			field: "r_nm",
			headerName: "수령자",
			width:60,
			cellClass: 'hd-grid-code'
		},
		{
			field: "dlv_msg",
			headerName: "특이사항"
		},
		{
			field: "dlv_comment",
			headerName: "출고메시지"
		},
		{
			field: "proc_state",
			headerName: "처리현황",
			width:72
		},
		{
			field: "proc_memo",
			headerName: "메모",
			width:60
		},
		{
			field: "sale_place",
			headerName: "판매처",
			width: 80,
			cellClass: 'hd-grid-code'
		},
		{
			field: "out_ord_no",
			headerName: "판매처주문번호"
		},
		{
			field: "com_nm",
			headerName: "업체",
			width: 80,
			cellClass: 'hd-grid-code'
		},
		{
			field: "baesong_kind",
			headerName: "배송구분",
			width:72
		},
		{
			field: "ord_date",
			headerName: "주문일시",
			type: 'DateTimeType'
		},
		{
			field: "pay_date",
			headerName: "입금일시",
			type: 'DateTimeType'
		},
		{
			field: "last_up_date",
			headerName: "클레임일시",
			type: 'DateTimeType'
		},
		{width: "auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId: "#div-gd", height: 265 });
	const gridDiv = document.querySelector(pApp.options.gridId);
	let gx;
	$(document).ready(function() {
		gx = new HDGrid(gridDiv, columns, {
            isRowSelectable : function(node){
                return node.data.ord_state < '30';
            }
        });
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		Search();

		$("#chk_to_class").click(function() {
			gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
		});
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/order/ord21/search', data);
	}
</script>
<script type="text/javascript" charset="utf-8">
	function updateState() {
		var checkRows = gx.gridOptions.api.getSelectedRows();
		var dlvSeriesNo = $("#dlv_series_no").val();
		var isOutSuccess = true;

		for (var i = 0; i < checkRows.length && isOutSuccess; i++) {
			isOutSuccess = checkRows[i].ord_kind < 30;
		}

		if (isOutSuccess === false) {
			alert("출고보류 주문은 출고처리중으로 변경이 불가능합니다.");
			return;
		}

		if (dlvSeriesNo == "") {
			alert("출고차수를 입력해주세요.");
			return;
		}

		if (checkRows.length === 0) {
			alert("출고요청하실 주문건을 선택해주세요.");
			return;
		}

		if (confirm("선택하신 주문을 출고처리중으로 변경하시겠습니까?")) {
			var orderNos = checkRows.map(function(row) {
				return row.ord_no + "||" + row.ord_opt_no;
			});

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/order/ord21/update/state',
				data: {
					"ord_opt_nos[]": orderNos,
					dlv_series_no: dlvSeriesNo,
					chk_ord_no: $("#chk_ord_no").prop("checked") ? "Y" : "N",
					ord_state: 20
				},
				success: function(data) {
					if (data == 1) {
						alert("변경되었습니다.");
					} else {
						alert("품절된 제품이 있습니다.");
						console.log(data);
					}

					Search();
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		}
	}

	function updateKind() {
		var checkRows = gx.gridOptions.api.getSelectedRows();

		if (checkRows.length === 0) {
			alert("출고상태를 변경할 주문건을 선택해주세요.");
			return;
		}

		if (confirm("선택하신 주문의 출고상태를 변경하시겠습니까?")) {
			var ordOptNos = checkRows.map(function(row) {
				return row.ord_opt_no;
			});

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/order/ord21/update/kind',
				data: {
					ord_opt_nos: ordOptNos,
					ord_kind: $("#u_ord_kind").val()
				},
				success: function(data) {
					alert("변경되었습니다.");
					Search();
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
		}
	}


	$(document).ready(function() {
		var $eventSelect = $(".select2-events");
		$eventSelect.select2();
		$eventSelect.on("select2:select", function(e) {
			if (e.params.data.id == "img") {
				$("#div-gd").addClass("gd-lh50");
				gx.gridOptions.api.resetRowHeights();
				gx.gridOptions.columnApi.setColumnVisible("img", true);
			}
		});
		$eventSelect.on("select2:unselect", function(e) {
			if (e.params.data.id == "img") {
				$("#div-gd").removeClass("gd-lh50");
				gx.gridOptions.api.resetRowHeights();
				gx.gridOptions.columnApi.setColumnVisible("img", false);
			}
		});
	});

	const onChangeDate = (input) => {
		const name = input.name;
		const today = getDateObjToStr(new Date()); // yyyymmdd

		// 오늘 이전의 데이터만 조회 가능
		let value = (input.value).replace(/-/gi, ""); // value is yyyymmdd

		if (value > today) {
			alert("미래의 날짜는 선택할 수 없습니다.");
			document.search.sdate.value = formatStringToDate(calcDate(today, -3, "M"));
			document.search.edate.value = formatStringToDate(today);
			return false;
		}

		// 조회 기간을 6개월로 고정
		if (name == 'sdate' && value.length == 8) {
			const edate = (document.search.edate.value).replace(/-/gi, ""); // y-m-d -> yyyymmdd
			const nn = calcDate(value, 6, "M");
			if (value > edate || edate > nn) {
				document.search.edate.value = formatStringToDate(nn);
			}
		} else if (name == 'edate' && value.length == 8) {
			const sdate = (document.search.sdate.value).replace(/-/gi, "");
			const nn = calcDate(value, -6, "M");
			if (value < sdate || sdate < nn) {
				document.search.sdate.value = formatStringToDate(nn);
			}
		}
	};

	const formatDateToString = (date) => {
		return date.replace("-", "");
	}

	const formatStringToDate = (string) => {
		const y = string.substr(0,4);
		const m = string.substr(4,2);
		const d = string.substr(6,2);
		return `${y}-${m}-${d}`;
	};

		/*
		Function: getDateObjToStr
			날짜를 YYYYMMDD 형식으로 변경

		Parameters:
			date - date object

		Returns:
			date string "YYYYMMDD"
	*/

	function getDateObjToStr(date){
		var str = new Array();

		var _year = date.getFullYear();
		str[str.length] = _year;

		var _month = date.getMonth()+1;
		if(_month < 10) _month = "0"+_month;
		str[str.length] = _month;

		var _day = date.getDate();
		if(_day < 10) _day = "0"+_day;
		str[str.length] = _day
		var getDateObjToStr = str.join("");

		return getDateObjToStr;
	}

	/*
		Function: calcDate
		데이트 계산 함수

		Parameters:
			date - string "yyyymmdd"
			period - int
			period_kind - string "Y","M","D"
			gt_today - boolean

		Returns:
			calcDate("20080205",30,"D");
	*/

	function calcDate(date,period, period_kind,gt_today){

		var today = getDateObjToStr(new Date());

		var in_year = date.substr(0,4);
		var in_month = date.substr(4,2);
		var in_day = date.substr(6,2);

		var nd = new Date(in_year, in_month-1, in_day);
		if(period_kind == "D"){
			nd.setDate(nd.getDate()+period);
		}
		if(period_kind == "M"){
			nd.setMonth(nd.getMonth()+period);
		}
		if(period_kind == "Y"){
			nd.setFullYear(nd.getFullYear()+period);
		}
		var new_date = new Date(nd);
		var calcDate = getDateObjToStr(new_date);
		if(! gt_today){ // 금일보다 큰 날짜 반환한다면
			if(calcDate > today){
				calcDate = today;
			}
		}
		return calcDate;
	}

	/*
	Function: UserFromToDate
		사용자 날짜 선택

	Returns:
		없음
	*/
	function UserFromToDate(type, ff, from, to) {

		if (type.length < 2) return;

		var today = getDateObjToStr(new Date());
		var date = "";

		var peroid = type.substring(0, type.length - 1);
		var peroid_type = type.substring(type.length - 1, type.length);

		try {
			peroid = 0 - parseInt(peroid);

			if (type == "0D") {
				ff[from].value = chgHyphenDate(today);
				ff[to].value = chgHyphenDate(today);

			} else if (peroid_type == "R") {
				if (peroid == 0) {
					var date = today.substr(0, 4) + today.substr(4, 2) + "01";
					ff[from].value = chgHyphenDate(date);
					ff[to].value = chgHyphenDate(today);
				} else {
					var lastdays = new Array("", 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
					var date = calcDate(today, -1, "M");
					var in_year = date.substr(0, 4);
					var in_month = date.substr(4, 2);
					var date = in_year + in_month + "01";
					var idx = parseInt(in_month);
					var today = in_year + in_month + lastdays[idx];

					ff[from].value = chgHyphenDate(date);
					ff[to].value = chgHyphenDate(today);
				}
			} else {
				var date = calcDate(today, peroid, peroid_type);
				ff[from].value = chgHyphenDate(date);
				ff[to].value = chgHyphenDate(today);
			}

		} catch (e) { }
	}

	function chgHyphenDate(item) {
		var date = "";

		tyear = item.substr(0, 4);
		tmonth = item.substr(4, 2);
		tday = item.substr(6, 2);

		date = tyear + "-" + tmonth + "-" + tday;

		return date;
	}

</script>
@stop
