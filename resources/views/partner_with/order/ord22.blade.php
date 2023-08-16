@extends('partner_with.layouts.layout')
@section('title','배송출고처리')
@section('content')
	<div class="page_tit">
		<h3 class="d-inline-flex">배송출고처리</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 주문&amp;배송</span>
			<span>/ 배송출고처리</span>
		</div>
	</div>
	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary">검색조건 초기화</a>
						<button type="button" onclick="exportBaesongList()" class="btn btn-sm btn-outline-primary shadow-sm" value="">배송목록 받기</button>
						<button type="button" onclick="deliveryInvDnView()" class="btn btn-sm btn-outline-primary shadow-sm" value="">택배송장 목록 받기</button>

						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
                                <label for="ord_date">주문일자</label>
								<div class="form-inline date-select-inbox">
									<select name='date_type' class="form-control form-control-sm date_type" style="width:23%;margin-right:2%;">
										<option value=''>사용자</option>
										<option value="0D">금일</option>
										<option value="1D">어제</option>
										<option value="7D">최근1주</option>
										<option value="14D">최근2주</option>
										<option value="30D">최근1달</option>
										<option value="0M">금월</option>
										<option value="1M">전월</option>
									</select>
									<div class="docs-datepicker form-inline-inner" style="width:35%;">
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
									<span class="text_line" style="width:5%;">~</span>
									<div class="docs-datepicker form-inline-inner" style="width:35%;">
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
								<label for="dlv_kind">주문상태/배송방식</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<select name='ord_state' class="form-control form-control-sm">
												<option value=''>전체</option>
												@foreach ($ord_states as $ord_state)
													<option
													value='{{ $ord_state->code_id }}'
													{{ ($ord_state->code_id == '20') ? 'selected' : '' }}
													>
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
								<label for="formrow-inputState">주문번호</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
					<!-- sale_places -->
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="sale_place">판매처</label>
								<div class="flax_box">
									<select name='sale_place' class="form-control form-control-sm">
										<option value=''>전체</option>
										@foreach ($sale_places as $sale_place)
											<option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="user_nm">주문자/수령자</label>
								<div class="form-inline inline_input_box">
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<input type="text" class="form-control form-control-sm search-all search-enter" name="user_nm" value="">
										</div>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<input type="text" class="form-control form-control-sm search-all search-enter" name="r_nm" value="">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="dlv_series_no">출고차수/정렬</label>
								<div class="form-inline inline_input_box">
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<select name='dlv_series_no' class="form-control form-control-sm">
												<option value=''>전체</option>
                                                @foreach ($dlv_series_nos as $dlv_series_no)
                                                    <option value='{{ $dlv_series_no->name }}'>{{ $dlv_series_no->value }}</option>
                                                @endforeach
											</select>
										</div>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<select name='ord_field' class="form-control form-control-sm">
												<option value='r_nm'>수령자</option>
												<option value='ord_no'>주문번호</option>
												<option value='goods_nm'>상품명</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="search-area-ext d-none row align-items-center">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="style_no">스타일넘버</label>
								<div class="flax_box">
									<input type="text" class="form-control form-control-sm search-all ac-style-no search-enter" name="style_no" value="">
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_nm">상품명</label>
								<div class="flax_box">
									<input type="text" class="form-control form-control-sm search-all ac-goods-nm search-enter" name="goods_nm" value="">
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="dlv_kind">배송구분</label>
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
					<div class="search-area-ext d-none row align-items-center">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="dlv_kind">주문구분</label>
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

					<div class="resul_btn_wrap d-sm-none mt-3">
						<div class="d-flex justify-content-center mb-2">
							<a href="javascript:void(0);" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
							<a href="javascript:void(0);" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary">검색조건 초기화</a>
						</div>
						<div class="d-flex justify-content-center">
							<button type="button" onclick="exportBaesongList()" class="btn btn-sm btn-outline-primary shadow-sm mr-1" value="">배송목록 받기</button>
							<button type="button" onclick="deliveryInvDnView()" class="btn btn-sm btn-outline-primary shadow-sm mr-1" value="">택배송장 목록 받기</button>
							<div class="search_mode_wrap btn-group mb-0 mb-sm-0"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- DataTales Example -->
	<div id="filter-area" class="card shadow-none search_cum_form ty2 last-card">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box flax_box">
						<div class="custom-control custom-checkbox form-check-box mr-2" style="display:inline-block;">
							<input type="checkbox" name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
							<label class="custom-control-label text-left" for="chk_to_class" style="line-height:27px;justify-content:left">이미지출력</label>
						</div>
						<div class="custom-control custom-checkbox form-check-box">
							<input type="checkbox" name="send_sms_yn" id="send_sms_yn" class="custom-control-input" checked="" value="Y">
							<label class="custom-control-label text-left" for="send_sms_yn" style="line-height:27px;justify-content:left">배송 문자 발송</label>
						</div>
						<select id="u_ord_kind" class="form-control form-control-sm mx-1 w-auto">
						@foreach ($ord_kinds as $ord_kind)
							<option value='{{ $ord_kind->code_id }}'{{ $ord_kind->code_id === '30' ? 'selected' : '' }}>{{ $ord_kind->code_val }}</option>
						@endforeach
						</select>
						<a href="#" onclick="updateState()" class="btn btn-sm btn-primary shadow-sm">출고요청</a>
						<select id="u_dlvs" class="form-control form-control-sm mx-1 w-auto">
						@foreach ($dlvs as $dlv)
							<option value='{{ $dlv->code_id }}'{{ $dlv->code_id === $dlv_cd ? 'selected' : '' }}>{{ $dlv->code_val }}</option>
						@endforeach
						</select>
						<a href="#" class="btn btn-sm btn-primary shadow-sm out-complete-btn">출고완료</a>
						<a href="#" class="btn btn-sm btn-primary shadow-sm ml-1 dlv-import-btn">택배송장 일괄입력</a>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
	<script language="javascript">
		let columns = [
			{
			  field: "blank",
			  headerName: '',
			  headerCheckboxSelection: true,
			  checkboxSelection: true,
			  headerCheckboxSelectionFilteredOnly: true,
			  width: 28,
			  pinned:'left'
			},
			{
				headerName: '#',
				width: 40,
				maxWidth: 100,
				// it is important to have node.id here, so that when the id changes (which happens
				// when the row is loaded) then the cell is refreshed.
				valueGetter: 'node.id',
				cellRenderer: 'loadingRenderer',
				cellClass: 'hd-grid-code',
				pinned:'left'
			},
			{field:"dlv_series_nm", headerName: "출고차수", width: 130, cellClass: 'hd-grid-code', pinned:'left'},
			{field:"dlv_no", headerName:"송장번호", width: 130, pinned:'left', editable: true, cellStyle: {'background' : '#ffff99'}},
			{field:"ord_type_nm", headerName:"주문구분", width: 60, cellClass: 'hd-grid-code', pinned:'left'},
			{field:"ord_kind_nm", headerName:"출고구분", width: 60, cellStyle: StyleOrdKind, pinned:'left'},
			{field:"user_nm", headerName:"주문자", width: 100, cellClass: 'hd-grid-code', pinned: 'left'},
			{field:"r_nm", headerName:"수령자", width: 60, cellClass: 'hd-grid-code', pinned: 'left'},
			{field:"ord_no", headerName:"주문번호", width: 140, cellStyle: StyleOrdNo, cellClass: 'hd-grid-code', type: 'OrderNoType', pinned: 'left'},
			{field:"ord_opt_no", headerName:"주문일련번호", sortable: "true", width: 80, cellClass: 'hd-grid-code',
                cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return openOrder(\'' + params.data.ord_no + '\',\'' + params.value +'\');">' + params.value + '</a>';
                    }
               },pinned:'left'},
			{field:"ord_state_nm", headerName:"주문상태",cellStyle:StyleOrdState},
			{field:"pay_stat_nm", headerName:"입금상태", width: 60, cellClass: 'hd-grid-code'},
			{field:"dlv_type", headerName:"배송방식", width: 60, cellClass: 'hd-grid-code'},
			{field:"clm_state_nm", headerName:"클레임상태", cellStyle: StyleClmState},
			{field:"goods_type_nm", headerName:"상품구분", cellStyle: StyleGoodsTypeNM},
			{field:"style_no", headerName:"스타일넘버", width: 80, cellClass: 'hd-grid-code'},
			{field:"img", headerName:"이미지", type:"GoodsImageType", width: 65, hide: true},
			{field:"goods_nm", headerName:"상품명",type:"GoodsNameType", width: 200},
			{field:"opt_val", headerName:"옵션", width: 150},
			{field:"sale_qty", headerName:"주문수량", type: 'currencyType', width: 70},
			{field:"qty", headerName:"온라인재고" , type: 'currencyType', width: 70},
			// 보유재고 확인요망
			{field:"wqty", headerName:"보유재고" , type: 'currencyType', width: 70},
			{field:"price", headerName:"판매가", type: 'currencyType', width: 70},
			{field:"sale_amt", headerName:"쿠폰할인", type: 'currencyType', width: 70},
			{field:"gift", headerName:"사은품"},
			{field:"dlv_amt", headerName:"배송비", type: 'currencyType', width: 70},
			{field:"pay_type", headerName:"결제방법", width: 80, cellClass: 'hd-grid-code'},
			{field:"r_zipcode", headerName:"우편번호", width: 60, cellClass: 'hd-grid-code'},
			{field:"r_addr", headerName:"주소", width: 200},
			{field:"r_phone", headerName:"전화번호", width: 100, cellClass: 'hd-grid-code'},
			{field:"r_mobile", headerName:"핸드폰", width: 100, cellClass: 'hd-grid-code'},
			{field:"r_jumin", headerName:"수령자 주민번호", hide: true},
			{field:"dlv_msg", headerName:"특이사항", width: 150},
			{field:"dlv_comment", headerName:"출고메시지", width: 150},
			{field:"proc_state", headerName:"처리현황", cellClass: 'hd-grid-code'},
			{field:"proc_memo", headerName:"메모", width: 150},
			{field:"sale_place", headerName:"판매처", width: 80, cellClass: 'hd-grid-code'},
			{field:"out_ord_no", headerName:"판매처주문번호", width: 100, cellClass: 'hd-grid-code'},
			{field:"com_nm", headerName:"업체", width: 80, cellClass: 'hd-grid-code'},
			{field:"baesong_kind", headerName:"배송구분", width: 80, cellClass: 'hd-grid-code'},
			{field:"ord_date", headerName:"주문일시", type: 'DateTimeType'},
			{field:"pay_date", headerName:"입금일시", type: 'DateTimeType'},
			{field:"dlv_proc_date", headerName:"출고요청일시", type: 'DateTimeType'},
			{field:"dlv_end_date", headerName:"배송일시", type: 'DateTimeType'},
			{field:"last_up_date", headerName:"클레임일시", type: 'DateTimeType'}
		];

		function numberWithCommas(x) {
			let parts = x.toString().split(".");
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			return parts.join(".");
		}

	</script>
	<script type="text/javascript" charset="utf-8">

		const pApp = new App('', { gridId: "#div-gd", height: 265 });
		const gridDiv = document.querySelector(pApp.options.gridId);
		let gx;
		$(document).ready(function () {
			gx = new HDGrid(gridDiv, columns, {
                isRowSelectable : function(node){
                    return node.data.ord_state < '30';
                }
            });
			pApp.ResizeGrid(265);
			pApp.BindSearchEnter();
			Search();

			$('.search-all').keyup(function(){
				date_use_check();
			});

			$("#chk_to_class").click(function() {
				gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
			});
		});

		function Search() {
			let data = $('form[name="search"]').serialize();
			gx.Request('/partner/order/ord22/search', data, 1, searchCallback);
    	}

    	function searchCallback(data) {}

		function updateState() {
		  let checkRows = gx.gridOptions.api.getSelectedRows();
		  let dlvSeriesNo  = $("#dlv_series_no").val();

		  if (dlvSeriesNo == "") {
			alert("출고차수를 입력해주세요.");
			return;
		  }

		  if (checkRows.length === 0) {
			alert("출고요청하실 주문건을 선택해주세요.");
			return;
		  }

		  if(confirm("선택하신 주문을 출고요청으로 변경하시겠습니까?")) {
			let orderNos = checkRows.map((row) => {
			  return [row.ord_no, row.ord_opt_no];
			});

			$.ajax({
				async: true,
				type: 'put',
				url: '/partner/order/ord22/state',
				data: {
				  "order_nos[]" : orderNos,
				  dlv_series_no : dlvSeriesNo,
				  ord_state : 10,
				  ord_kind : $("#u_ord_kind").val(),
				  _token : $('[name=_token]').val()
				},
				success: function (data) {
				  alert("변경되었습니다.");
				  Search();
				},
				error: function(xhr, status, error) {
					console.log(xhr.responseText);
				}
			});
		  }
		}

		function exportBaesongList()
		{
			$('[name=search]').attr("action", "/partner/order/ord22/download/delivery_list");
			$('[name=search]').submit();
/*
			const date = new Date();
			const yyyymmdd = $.datepicker.formatDate('yyyymmdd', date);
			const hour = date.getHours();

			gx.Download(`dlv_list_${yyyymmdd}${hour}.csv`);
*/
		}

		function s2ab(s) {
			//convert s to arrayBuffer
			let buf = new ArrayBuffer(s.length);

			//create uint8array as viewer
			let view = new Uint8Array(buf);

			//convert to octet
			for (let i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
			return buf;
		}

		function deliveryInvDnView() {
		  window.open(
			'/partner/order/ord22/show?'+$('form').serialize(),
			'_blank',
			'toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=768'
		  );
		}

		$("[name=date_kind]").change(function(){
		  if (this.value === "a.ord_date") {
			$("[name=ord_state]").val(20).prop("selected", true);
		  } else if (this. value === "a.dlv_end_date") {
			$("[name=ord_state]").val(30).prop("selected", true);
		  }
		});

		$("#hide_img").change(function(){
		  gx.gridOptions.rowHeight = this.checked ? 50 : 25 ;
		  gx.gridOptions.api.resetRowHeights();
		  gx.gridOptions.columnApi.setColumnVisible("img", this.checked);
		});


		$(".dlv-import-btn").click(function(){
		  window.open(
			'/partner/order/ord22/dlv-import',
			'_blank',
			'toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=768'
		  );
		});

		$(".out-complete-btn").click(function()
		{
			var	dlv_no_chk	= "Y";

			let checkRows = gx.gridOptions.api.getSelectedRows();

			if( checkRows.length === 0 )
			{
				alert("출고완료하실 주문건을 선택해주세요.");
				return;
			}

			if( confirm("선택하신 주문을 출고완료로 변경하시겠습니까?") )
			{
				let orderNos = checkRows.map(function(row) {

					if( row.dlv_no == null && dlv_no_chk == "Y" )
					{
						alert('먼저 송장번호를 입력하시길 바랍니다.');
						dlv_no_chk	= "N";
					}

					return [
						row.ord_no,
						row.ord_opt_no,
						row.dlv_no
					];
				});

				if( dlv_no_chk == "Y" )
				{
					$.ajax({
						type : 'put',
						url : '/partner/order/ord22/out-complete',
						data : {
							"order_nos[]" : orderNos,
							ord_state : 30,
							dlv_cd : $("#u_dlvs").val(),
							send_sms_yn : $('#send_sms_yn:checked').val()
						},
						success: function (data) {
							alert("출고완료 상태로 변경되었습니다.");
							Search();
						},
						error: function(xhr, status, error) {
						    console.log(xhr.responseText);
						}
					});
				}

			}
		});

        $('[name=date_type]').change(function(e){
            setDateType(this.value,$('[name=sdate]'),$('[name=edate]'));
        });
		//
	</script>
@stop
