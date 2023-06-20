@extends('store_with.layouts.layout')
@section('title','회원관리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">회원관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 고객관리</span>
		<span>/ 회원관리</span>
	</div>
</div>

<form method="get" name="search">
	<input type="hidden" name="fields">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="#" onclick="Batch()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>데이터업로드</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
					<div class="btn-group dropleftbtm mr-1">
						<button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
							<i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
						</button>
						<div class="dropdown-menu" style="">
							<a class="dropdown-item add-btn" href="#">회원추가</a>
							<a class="dropdown-item coupon-btn" href="#" >쿠폰지급</a>
							<a class="dropdown-item point-btn" href="#" >적립금지급</a>
							<a class="dropdown-item sms-btn" href="#" >SMS 발송</a>
							<a class="dropdown-item download-btn" href="#" >다운로드</a>
						</div>
						<input type="hidden" name="data" id="data" value=""/>
					</div>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">아이디</label>
							<div class="flax_box">
								<input type="text" name="user_ids" id="user_ids" class="form-control form-control-sm mr-1 search-enter" placeholder="여러명 검색 시 콤마(,)로 구분">
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="dlv_kind">회원명</label>
							<div class="flax_box">
								<input type="text" name="name" id="name" class="form-control form-control-sm search-enter">
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">그룹</label>
							<div class="flax_box">
								<select name='user_group' class="form-control form-control-sm select2-user_group" multiple="multiple">
									<option value='' disabled>회원그룹</option>
									@foreach($groups as $group)
										<option value="{{$group->id}}">{{$group->val}}</option>
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
								<input type='hidden' id="store_nm" name="store_nm">
								<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">휴대전화</label>
							<div class="flax_box">
								<input type="text" name="mobile" id="mobile" class="form-control form-control-sm search-enter">
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">회원가입 종류</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="type" id="type_nb" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="type_nb">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="type" id="type_n" class="custom-control-input" value="N">
                                    <label class="custom-control-label" for="type_n">온라인</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="type" id="type_b" class="custom-control-input" value="B">
                                    <label class="custom-control-label" for="type_b">오프라인</label>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="search-area-ext row d-none align-items-center">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">가입일</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="edate" autocomplete="off">
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
							<label for="">최근로그인</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="last_sdate" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="last_edate" autocomplete="off">
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
							<label for="">기념일 기간(년)/월일</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:17%">
									<div class="form-group">
										<input type="text" name="birth_sdate" id="birth_sdate" class="form-control form-control-sm text-center" maxlength="4" placeholder="ex)1980">
									</div>
								</div>
								<span class="text_line">~</span>
								<div class="form-inline-inner input_box" style="width:17%">
									<div class="form-group">
										<input type="text" name="birth_edate" id="birth_edate" class="form-control form-control-sm text-center" maxlength="4" placeholder="ex)1982">
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:54%">
									<div class="flax_box inline_btn_box" style="padding-right:60px;">
										<input type="text" name="mmdd" id="mmdd"  class="form-control form-control-sm text-center" maxlength="8" placeholder="예)1224">
										<a href="#" onClick="getToday();" class="btn btn-sm btn-secondary now-btn" style="width:50px;">금일</a> 
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="search-area-ext row d-none align-items-center">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">최근주문일</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="order_sdate" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="order_edate" autocomplete="off">
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
							<label for="">구입금액</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_amt_from' value='' onkeyup="currency(this)">
									</div>
								</div>
								<span class="text_line">~</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_amt_to' value='' onkeyup="currency(this)">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">구매수량</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_cnt_from' value='' onkeyup="currency(this)">
									</div>
								</div>
								<span class="text_line">~</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<input type='text' class="form-control form-control-sm search-all search-enter text-right" name='cond_cnt_to' value='' onkeyup="currency(this)">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="search-area-ext row d-none align-items-center">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">메일수신</label>
							<div class="flax_box">
								<select name="mail" id="mail" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($mail as $val)
										<option value="{{$val->code_id}}">{{$val->code_val}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">SMS수신/성별/연령</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:22%;">
									<div class="form-group">
										<select name="mobile_chk" id="mobile_chk" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($mobile as $val)
												<option value="{{$val->code_id}}">{{$val->code_val}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:22%;">
									<div class="form-group">
										<select name="sex" id="sex" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($sex as $val)
												<option value="{{$val->code_id}}">{{$val->code_val}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:44%;">
									<div class="form-group">
										<select name="age" id="age" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($age as $val)
												<option value="{{$val->code_id}}">{{$val->code_val}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">승인/가입판매처</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select name="yn" id="yn" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($yn as $val)
												<option value="{{$val->code_id}}">{{$val->code_val}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box">
									<div class="form-group">
										<select name="site" id="site" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($sites as $val)
												<option value="{{$val->code_id}}">{{$val->code_val}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">인증방식</label>
							<div class="flax_box">
								<select name="auth_type" id="auth_type" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($auth_type as $val)
										<option value="{{$val->code_id}}">{{$val->code_val}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">자료수/정렬</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<select name="limit" class="form-control form-control-sm">
										<option value="100" >100</option>
										<option value="500" >500</option>
										<option value="1000" >1000</option>
										<option value="2000" >2000</option>
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:45%;">
									<select name="ord_field" class="form-control form-control-sm">
										<option value="a.user_id" selected>아이디</option>
										<option value="a.name" >이름</option>
										<option value="a.regdate" selected >가입일</option>
										<option value="a.lastdate" >최근로그인</option>
										<option value="e.ord_date" >최근주문일</option>
										<option value="e.ord_amt" >구매금액</option>
									</select>
								</div>
								<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
									<div class="btn-group" role="group">
										<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
										<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
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
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="Batch()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>데이터업로드</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
			<div class="btn-group dropleftbtm mr-1">
				<button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
					<i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
				</button>
				<div class="dropdown-menu" style="">
					<a class="dropdown-item add-btn" href="#">회원추가</a>
					<a class="dropdown-item coupon-btn" href="#" >쿠폰지급</a>
					<a class="dropdown-item point-btn" href="#" >적립금지급</a>
					<a class="dropdown-item sms-btn" href="#" >SMS 발송</a>
					<a class="dropdown-item download-btn" href="#" >다운로드</a>
				</div>
			</div>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>

<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
	<div class="card-body">
		<div class="card-title mb-3">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	var columns = [
		{headerName:'', headerCheckboxSelection:true, checkboxSelection:true, width:28, pinned:'left'},
		{field:"user_id", headerName:"아이디", pinned:'left', type:"HeadUserType", width:100},
		{field:"name", headerName:"이름", width:70, cellStyle:{"text-align": "center"}},
		{field:"sex", headerName:"성별", width:50},
		{field:"birth_day", headerName:"생년월일", width:80},
		{field:"mobile", headerName:"휴대전화", width:100, cellStyle:{"text-align":"center"}},
		{field:"email", headerName:"이메일", width:150, cellStyle:{"text-align": "center"}},
		{field:"point", headerName:"적립금", type:'currencyType', width:60},
		{field:"regdate", headerName:"가입일", width:70, cellStyle:{"text-align": "center"}},
		{field:"store_nm", headerName:"가입 매장명", width:170},
		{field:"lastdate", headerName:"최근로그인", width:130},
		{field:"visit_cnt", headerName:"로그인횟수", type:'currencyType', width:80},
		{field:"auth_yn", headerName:"인증여부", width:70, cellStyle:{"text-align": "center"}},
		{field:"ord_date", headerName:"최근주문일", width:125, cellStyle:{"text-align": "center"}},
		{field:"ord_cnt", headerName:"구매수량", type:'currencyType', width:60},
		{field:"ord_amt", headerName:"구입금액", type:'currencyType', width:70},
		{field:"email_chk", headerName:"메일수신", width:70, cellStyle:{"text-align": "center"}},
		{field:"mobile_chk", headerName:"SMS수신", width:70, cellStyle:{"text-align": "center"}},
		{field:"yn", headerName:"승인", width:50, cellStyle:{"text-align": "center"}},
		{field:"site", headerName:"판매처", width:70, cellStyle:{"text-align": "center"}},
		{width:"auto"}
		
	];

	const pApp = new App('', {gridId: "#div-gd"});
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(200);
	pApp.BindSearchEnter();

	Search();

	function Search() {
		let data = $('form[name="search"]').serialize();
		let user_group_data = "&user_group=" + $("[name='user_group']").val().join(",");
		gx.Request('/store/member/mem01/search', data+user_group_data, 1);
	};

	function Download(fields) {
		$('[name=fields]').val(fields);
		let data = $('form[name="search"]').serialize();
		location.href = '/head/member/mem01/download?' + data;
	}

	$('.download-btn').click(function(){
		const data = $('form[name="search"]').serialize();
		const url='/head/member/mem01/download/show?' + data;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	});

	$('.sms-btn').click(function(){
		const rows = gx.getSelectedRows();

		if (rows.length === 0) {
			alert("메시지 보낼 유저를 선택해주세요.");
			return;
		}

		// openSmsSend(rows[0].mobile, rows[0].name);
		openStoreMultiSmsSend(rows.map(row => row.user_id).join(","));
	});

	$('.point-btn').click(function(e){
		const rows = gx.getSelectedRows();

		if (rows.length === 0) {
			alert("메시지 보낼 유저를 선택해주세요.");
			return;
		}
		const user_ids = [];

		rows.forEach(function(data){
			user_ids.push(data.user_id);
		});

		openAddPoint(user_ids.join(','));
	});

	$('.add-btn').click(function(e){
		var url = '/head/member/mem01/show/add';
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=768");
	});

	$('.coupon-btn').click(function(){
		const rows = gx.getSelectedRows();
		const user_ids = rows.map((row) => row.user_id);
		openCoupon(user_ids.join(','));
	});

	if ($('.confirm-btn').length > 0){
		$('.confirm-btn').click(function(){
			const rows = gx.getSelectedRows();

			if (rows.length === 0) {
				alert("회원을 선택해주세요.");
				return;
			}

			opener?.usersCallback?.(rows);

			window.close();
		});
	}

	function getToday(){
		$('[name="mmdd"]').val('{{ $today }}');
	}

	function Batch(){
		const url='/store/member/mem01/batch';
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	$( ".sch-store" ).on("click", function() {
		searchStore.Open(null, "multiple");
	});

//openSmsSend
</script>
@stop
