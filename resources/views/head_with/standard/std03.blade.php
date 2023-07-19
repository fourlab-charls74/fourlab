@extends('head_with.layouts.layout')
@section('title','브랜드')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>

<style>
	center img {
		margin: 9px;
		width: 80px;
		height: 80px;
	}
</style>

<div class="page_tit">
	<h3 class="d-inline-flex">브랜드</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 기준정보</span>
		<span>/ 브랜드</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<input type="hidden" name="c_id" value="{{$admin_id}}">
		<input type="hidden" name="c_name" value="{{$admin_nm}}">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
					<a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="far fa-trash-alt fs-12"></i> 삭제</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-firstname-input">브랜드</label>
							<div class="flax_box">
								<input type="text" name="brand" class="form-control form-control-sm search-enter">
							</div>
						</div>
					</div>
					<!-- <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="dlv_kind">베스트여부</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="best_yn" id="sch_best_yn_" class="custom-control-input" checked="" value="">
									<label class="custom-control-label" for="sch_best_yn_">전체</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="best_yn" id="sch_best_yn_y" class="custom-control-input" value="y">
									<label class="custom-control-label" for="sch_best_yn_y">Y</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="best_yn" id="sch_best_yn_n" class="custom-control-input" value="n">
									<label class="custom-control-label" for="sch_best_yn_n">N</label>
								</div>
							</div>
						</div>
					</div> -->
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="dlv_kind">사용여부</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="use_yn" id="sch_use_yn_" class="custom-control-input" checked="" value="">
									<label class="custom-control-label" for="sch_use_yn_">전체</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="use_yn" id="sch_use_yn_y" class="custom-control-input" value="Y">
									<label class="custom-control-label" for="sch_use_yn_y">사용</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="use_yn" id="sch_use_yn_n" class="custom-control-input" value="N">
									<label class="custom-control-label" for="sch_use_yn_n">미사용</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">구분</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="brand_type" id="sch_brand_type_" class="custom-control-input" checked="" value="">
									<label class="custom-control-label" for="sch_brand_type_">전체</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="brand_type" id="sch_brand_type_y" class="custom-control-input" value="S">
									<label class="custom-control-label" for="sch_brand_type_y">시스템</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="brand_type" id="sch_brand_type_n" class="custom-control-input" value="U">
									<label class="custom-control-label" for="sch_brand_type_n">유저</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-firstname-input">메모</label>
							<div class="flax_box">
								<input type="text" name="memo" class="form-control form-control-sm" style="width:100%;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
			<a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="far fa-trash-alt fs-12"></i> 삭제</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<div class="show_layout">
	<form name="f1" id="f1" method="POST" enctype="multipart/form-data">

		<input type="hidden" name="c">
		<input type="hidden" name="cmd">

		<div class="row">
			<div class="col-lg-6">
				<div class="card_wrap">
					<div class="card">
						<div class="card-title mb-3">
							<div class="filter_wrap">
								<div class="fl_box">
									<h5 class="m-0 font-weight-bold">상품 세부 정보</h5>
								</div>
								<div class="fr_box">
									<button type="button" class="setting-grid-col ml-2"><i class="fas fa-cog text-primary"></i></button>
								</div>
							</div>
						</div>
						<div class="card-body pt-3">
							<div class="row">
								<div class="col-12">
									<div class="table-responsive">
										<div id="div-gd" class="ag-theme-balham"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 pl-1">
				<div class="card_wrap">
					<div class="card shadow">
						<div class="row_wrap">
							<div class="card-header mb-0">
								<h5 class="m-0 font-weight-bold">브랜드 정보</h5>
							</div>
							<div class="card-body pt-3">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
										<colgroup>
											<col width="120px;">
										</colgroup>
										<tbody>
											<tr>
												<th class="required">브랜드</th>
												<td>
													<input type="text" name="brand" id="brand" class="form-control form-control-sm search-all" style="width:50%;float:left;"  onkeyup="return CheckBrandCode();">

													<span id="chg_brand" style="display: none;float:left;">
														&nbsp;->&nbsp;
														<select name="chg_brand" id="chg_brand" style="width: 100px;padding-left:5px;height:30px;line-height:30px;"></select>
														<input type="button" class="btn btn-sm btn-primary shadow-sm" value="변경" onclick="ChangeBrandCode();" />
													</span>
													<span id="CheckBrandMessage" style="color:red;font-weight:bold;padding-left:10px;line-height:30px;"></span>
													<input type="hidden" name="BRAND_CHECK" value="" />
												</td>
											</tr>
											<tr>
												<th class="ty2 required">브랜드명(국문)</th>
												<td class="ty2">
													<div class="input_box">
														<input type="text" name="brand_nm" id="brand_nm" class="form-control form-control-sm search-all">
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty2 required">브랜드명(영문)</th>
												<td class="ty2">
													<div class="input_box">
														<input type="text" name="brand_nm_eng" id="brand_nm_eng" class="form-control form-control-sm search-all">
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty2 required">단축코드</th>
												<td class="ty2">
												<div class="d-flex flex-column">
													<div class="d-flex">
														<input type="text" name="br_cd" id="br_cd" class="form-control form-control-sm w-50 mr-2" />
														<div class="w-50" style="line-height:30px;font-size:14px;">
															* 3자리 이하 영문숫자
														</div>
													</div>
												</div>
												</td>
											</tr>
											<tr>
												<th>개요</th>
												<td>
													<div class="area_box">
														<textarea class="form-control" name="overview" style="height: 50px;"></textarea>
													</div>
												</td>
											</tr>
											<tr>
												<th>메모</th>
												<td>
													<div class="area_box">
														<textarea class="form-control" name="memo" style="height: 50px;"></textarea>
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty2">키워드</th>
												<td class="ty2">
													<div class="input_box">
														<input type="text" name="keyword" id="keyword" class="form-control form-control-sm search-all">
													</div>
												</td>
											</tr>
											<tr>
												<th class="required">구분</th>
												<td>
													<div class="input_box">
														<div class="form-inline form-radio-box">
															<div class="custom-control custom-radio">
																<input type="radio" name="brand_type" id="brand_type_s" class="custom-control-input" value="S" checked>
																<label class="custom-control-label" for="brand_type_s">시스템(S)</label>
															</div>
															<div class="custom-control custom-radio">
																<input type="radio" name="brand_type" id="brand_type_u" class="custom-control-input" value="U">
																<label class="custom-control-label" for="brand_type_u">유저(U)</label>
															</div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="required">베스트 여부</th>
												<td>
													<div class="input_box">
														<div class="form-inline form-radio-box">
															<div class="custom-control custom-radio">
																<input type="radio" name="best_yn" id="best_yn_y" class="custom-control-input" value="Y" checked>
																<label class="custom-control-label" for="best_yn_y">Y</label>
															</div>
															<div class="custom-control custom-radio">
																<input type="radio" name="best_yn" id="best_yn_n" class="custom-control-input" value="N">
																<label class="custom-control-label" for="best_yn_n">N</label>
															</div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="required">사용여부</th>
												<td>
													<div class="input_box">
														<div class="form-inline form-radio-box">
															<div class="custom-control custom-radio">
																<input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" checked>
																<label class="custom-control-label" for="use_yn_y">Y</label>
															</div>
															<div class="custom-control custom-radio">
																<input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N">
																<label class="custom-control-label" for="use_yn_n">N</label>
															</div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>등록자</th>
												<td>
													<span id="admin">{{$admin_nm}}({{$admin_id}})</span>
												</td>
											</tr>
											<tr>
												<th>등록일시</th>
												<td>
													<span id="regi_date"></span>
												</td>
											</tr>
											<tr>
												<th>수정일시</th>
												<td>
													<span id="upd_date"></span>
												</td>
											</tr>
											<tr>
												<th>브랜드 태그</th>
												<td>
													<div class="area_box">
														<textarea name="brand_contents" id="brand_contents" class="form-control editor1"></textarea>
													</div>
												</td>
											</tr>
											<tr>
												<th>브랜드 로고</th>
												<td>
													<input type="hidden" name="brand_logo" id="brand_logo" />
													<ul style="padding:0; list-style:none; margin:0; list-style-type:none;">
														<li>
															<span id="preview_logo_img" style="width:100px; height:100px; border:1px solid #b3b3b3; display:block;">
																<center></center>
															</span>
														</li>
														<li style="padding-top:5px;">
															<input type="file" id="brand_file" name="brand_file" />
														</li>
													</ul>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<!-- 확인 -->
							<div style="text-align:center;" class="mt-2">
								<input type="button" class="btn btn-sm btn-primary shadow-sm" value="확인" onclick="Cmder('savecmd')">
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-header mb-0">
							<h5 class="m-0 font-weight-bold">브랜드 현황</h5>
						</div>
						<div class="card-body pt-3">
							<div class="row_wrap">
								<!-- 브랜드 -->
								<div class="row">
									<div class="col-lg-12 inner-td">
										<div class="table-responsive">
											<table class="table table-bordered th_border_none" id="dataTable" width="100%" cellspacing="0">
												<thead>
													<tr>
														<th>상품 상태</th>
														<th>상품수</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">미지정</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_none_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">휴지통</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_-90_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">판매중지</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_-10_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">등록대기중</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_5_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">판매대기중</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_10_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">품절수동</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_20_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">품절</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_30_cnt">0</span>
															&nbsp;
														</td>
													</tr>
													<tr>
														<td class="hdx" style="text-align:left;" width="10%">판매중</td>
														<td class="hdx" style="text-align:right;" width="10%">
															<span id="stat_40_cnt">0</span>
															&nbsp;
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	</form>
</div>

<script type="text/javascript" charset="utf-8">
	var ed;

	$(document).ready(function() {
		var editorToolbar = [
			['font', ['bold', 'underline', 'clear']],
			['color', ['color']],
			['para', ['paragraph']],
			['insert', ['picture', 'video']],
			['emoji', ['emoji']],
			['view', ['undo', 'redo', 'codeview', 'help']]
		];
		var editorOptions = {
			lang: 'ko-KR', // default: 'en-US',
			minHeight: 100,
			height: 200,
			width: 500,
			dialogsInBody: true,
			disableDragAndDrop: false,
			toolbar: editorToolbar,
			imageupload: {
				dir: '/data/head/brand_img',
				maxWidth: 1280,
				maxSize: 10
			}
		}
		ed = new HDEditor('.editor1', editorOptions);
	});
</script>
<script language="javascript">
	var columns = [
		// this row shows the row index, doesn't use any data from the row

		{
			field: 'seq',
			headerName: '#',
			width: 35,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			cellStyle: {"background":"#F5F7F7"},
			cellClass: 'hd-grid-code',
		},
		{
			field: "chk", 
			headerName: '', 
			cellClass: 'hd-grid-code', 
			headerCheckboxSelection: true, 
			checkboxSelection: true, 
			width: 28,
		},
		{
			field: "brand_type",
			headerName: "구분",
			width: 50,
			cellStyle: StyleGoodsTypeNM,
			cellClass: 'hd-grid-code',
			cellRenderer: function(params) {
				if(params.value === 'S') return "시스템"
				else if(params.value === 'U') return "유저"
			}
		},
		{
			field: "brand",
			headerName: "브랜드",
			width: 100,
			cellRenderer: function(params) {
				return '<a href="javascript:;" data-code="' + params.value + '" onClick="GetBrand(this)">' + params.value + '</a>'
			}
		},
		{
			field: "brand_nm",
			headerName: "브랜드명",
			width: 100,
		},
		{
			field: "goods_cnt",
			headerName: "상품수",
			width: 50,
			type: 'numberType'
		},
		{
			field: "use_yn",
			headerName: "사용여부",
			width: 60,
			cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
				if(params.value == 'Y') return "사용"
				else if(params.value == 'N') return "미사용"
                else return params.value
			}
		},
		{
			field: "regi_date",
			headerName: "등록일시",
			type: 'DateTimeType'
		},
		{
			field: "ut",
			headerName: "수정일시",
			type: 'DateTimeType'
		},
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId: "#div-gd", height: 265 });
	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		let url_path_array = String(window.location.href).split('/');
		const pid = filter_pid(String(url_path_array[url_path_array.length - 1]).toLocaleUpperCase());

		get_indiv_columns(pid, columns, function(data) {
			if(data !== null) {
				gx = new HDGrid(gridDiv, data);
			} else {
				gx = new HDGrid(gridDiv, columns);
			}

			setMyGridHeader.Init(gx,
				indiv_grid_save.bind(this, pid, gx),
				indiv_grid_init.bind(this, pid)
			);

			Search(1);
		});
	});


	/*
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Aggregation({
			"sum":"top",
		});
		gx.Request('/partner/cs/cs72/search', data);
	}
	*/
</script>
<script type="text/javascript" charset="utf-8">
	var _isloading = false;

	function onscroll(params) {


		if (_isloading === false && params.top > gridDiv.scrollHeight) {

			//var rowtotal = gridOptions.api.getDisplayedRowCount();
			//console.log(rowtotal);
		}
	}

	var _page = 1;
	var _total = 0;
	var _grid_loading = false;
	var _code_items = "";
	var columns_arr = {};
	var option_key = {};

	function Search(page) {
		let data = $('form[name=search]').serialize();
		gx.Request('/head/standard/std03/search', data, -1);
	}
</script>

<script>
	/*
	 * 명령어 함수
	 */
	function Cmder(cmd)
	{
		if( cmd == "add" ){ // 등록버튼 실행
			// 브랜드 폼 초기화
			ResetForm();
			EnableAdd(true);
			$("#brand").focus();

			// 브랜드 코드 중복 메세지 초기화
			$("CheckBrandMessage").innerHTML = "";

			// 브랜드 현황 초기화
			ResetSummay();

		}else if( cmd == "savecmd" ){
			if( $("[name=c]").val() == "edit" ){ // 수정모드
				if( Validate("editcmd") ){
					SaveCmd("editcmd");
				}
			}else{ // 등록모드
				if( Validate("addcmd") ){
					SaveCmd("addcmd");
				}
			}
		}else if( cmd == "delcmd" ) { // 삭제모드
			DeleteCmd(cmd);
		}else if( cmd == "grid_load" ){ // 그리드 출력
			GridListDraw(true);
		}
	}

	function Validate(cmd) {
		if (cmd == "addcmd") {
			if ($("#brand").val() == "") {
				alert("브랜드를 입력해 주십시오.");
				$("#brand").focus();
				return false;
			}
			if ($("[name=BRAND_CHECK]").val() == "") {
				alert("등록된 브랜드 입니다. 사용하실 수 없습니다.");
				$("BRAND").focus();
				return false;
			}
		}
		if ($("[name=brand_nm]").val() == "") {
			alert("브랜드명(국문)을 입력하십시오.");
			$("[name=brand_nm]").focus();
			return false;
		}
		if ($("[name=brand_nm_eng]").val() == "") {
			alert("브랜드명(영문)을 입력하십시오.");
			$("[name=brand_nm_eng]").focus();
			return false;
		}

		return true;
	}

	/*
	*	브랜드 등록, 수정
	*/
	function SaveCmd(cmd) {
		var brand_contents	= $("textarea[name=brand_contents]").val();
		var img_url	= "";

		$("[name='cmd']").val(cmd);

		brand_contents	= brand_contents.replace(/(<([^>]+)>)/ig, "");

		const form = new FormData(document.querySelector("#f1"));
		form.append("brand_file", $("#brand_file")[0].files[0] || '');

		$.ajax({
			method: 'post',
			url: '/head/standard/std03/Command',
			data: form,
			contentType: false,
			processData: false,
			success: function(data) {
				var save_msg = "";
				if (data.brand_result == "200") {
					if (cmd == "editcmd") {
						save_msg = "수정되었습니다.";
					} else {
						save_msg = "등록되었습니다.";
					}
				} else {
					save_msg = "처리 중 오류가 발생하였습니다. 관리자에게 문의하세요.";
				}
				alert(save_msg);
				Search(1);

				if (cmd == "editcmd") {
					//openCodePopup('');
				} else {
					ResetForm();
					EnableAdd(true);
				}
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});
	}


	/*
	 *	브랜드 코드 중복 및 한글 포함 여부 판단
	 */
	function CheckBrandCode() {
		var brand = $("#brand").val();

		if (brand) {

			// 한글 포함 여부 판단
			var brand = brand ? brand : el.brand;
			var pattern = /^[a-zA-Z0-9]+$/;

			if (!pattern.test(brand)) {
				alert("영문 또는 숫자만 입력 가능합니다.");
				$("#brand").val("");
				// 브랜드 코드 중복 메세지 초기화
				$("#CheckBrandMessage").html("");
				return false;
			}

			// 중복 판단
			$.ajax({
				async: true,
				type: 'post',
				url: '/head/standard/std03/CheckBrand',
				data: "brand=" + brand,
				success: function(data) {
					//console.log(data);
					cbCheckBrandCode(data);
				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					console.log("error")
					console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
				}
			});

			/*
			var param = "CMD=check";
			param += "&BRAND=" + brand;
			var http = new xmlHttp();
			http.onexec('std24.php','POST',param,true,cbCheckBrandCode);
			*/
		}
	}

	function cbCheckBrandCode(res) {

		if (res.responseText == "1") {
			$("[name=BRAND_CHECK]").val("Y");
			$("#CheckBrandMessage").html("");
		} else {
			$("[name=BRAND_CHECK]").val("");
			$("#CheckBrandMessage").css({
				"color": "red"
			});
			$("#CheckBrandMessage").html("등록된 브랜드 입니다. 사용하실 수 없습니다.");
		}
	}

	function ResetForm() {
		var f1 = document.f1;
		f1.reset();
		$("#CheckBrandMessage").html("");

		f1.use_yn[0].checked = true;

		document.getElementById("admin").innerHTML = "";
		document.getElementById("regi_date").innerHTML = "";
		document.getElementById("upd_date").innerHTML = "";

		$("textarea[name=brand_contents]").value = "";
		ed.editor.summernote("code", '');
		//xed.rdom.getRoot().innerHTML = "";

		// 브랜드 로고 이미지
		jQuery("#preview_logo_img").html("<center></center>");
		f1.brand_logo.value = "";
	}

	function ResetSummay() {
		document.getElementById("stat_none_cnt").innerHTML = "0";
		document.getElementById("stat_-10_cnt").innerHTML = "0";
		document.getElementById("stat_-90_cnt").innerHTML = "0";
		document.getElementById("stat_5_cnt").innerHTML = "0";
		document.getElementById("stat_10_cnt").innerHTML = "0";
		document.getElementById("stat_20_cnt").innerHTML = "0";
		document.getElementById("stat_30_cnt").innerHTML = "0";
		document.getElementById("stat_40_cnt").innerHTML = "0";
	}

	/*
	 *	브랜드 정보 얻기
	 */
	function GetBrand(a) {
		var brand = $(a).attr('data-code');
		EnableAdd(false);

		// 중복 판단
		$.ajax({
			async: true,
			type: 'get',
			url: '/head/standard/std03/GetBrand/' + brand,
			success: function(data) {
				//console.log(data);
				cbGetBrand(data.body);
				//cbGetBrandList(data.brand_list);
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error")
				console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
			}
		});
		GetBrandSummary(brand);
		GetBrandList();

	}

	function cbGetBrand(res) {
		var f1	= document.f1;
		//console.log(res[0]);
		//var object = Dom2Obj(res);
		if( res[0] ) {
			var o	= res[0];
			$("#brand").val(o.brand);
			$("input[name=brand_nm]").val(o.brand_nm);
			$("input[name=brand_nm_eng]").val(o.brand_nm_eng);
			$("input[name=br_cd]").val(o.br_cd);
			$("textarea[name=memo]").val(o.memo);
			$("textarea[name=overview]").val(o.overview);
			$("input[name=keyword]").val(o.keyword);
			$("[name=c]").val('edit');
			$("[name=cmd]").val('editcmd');

			//console.log(o.brand_contents);
			//$("#brand_contents").val(o.brand_contents);
			ed.editor.summernote("code", o.brand_contents);

			// 브랜드 로고
			var logo_img = "";
			if( o.brand_logo != "" ) {
				// logo_img = '<center><img src="{{config('shop.image_svr')}}' + o.brand_logo + '"/></center>';
				logo_img = '<center><img src="' + o.brand_logo + '"/></center>'; // 저장위치 관련 추가작업 필요
			} else {
				logo_img = '<center></center>';
			}

			$("#preview_logo_img").html(logo_img);
			f1.brand_logo.value = "";

			if (o.brand_type == "S") f1.brand_type[0].checked = true;
			if (o.brand_type == "U") f1.brand_type[1].checked = true;
			if (o.use_yn == "Y") f1.use_yn[0].checked = true;
			if (o.use_yn == "N") f1.use_yn[1].checked = true;

			if (o.best_yn == "Y") f1.best_yn[0].checked = true;
			if (o.best_yn == "N") f1.best_yn[1].checked = true;

			document.getElementById("admin").innerHTML = o.admin_nm + "(" + o.admin_id + ")";
			document.getElementById("regi_date").innerHTML = o.regi_date;
			document.getElementById("upd_date").innerHTML = o.ut;

			// 기존 선택된 브랜드 로고 파일 초기화
			f1.brand_file.value = '';
		}
		//LoadingPopupShowHide();
	}

	function GetBrandSummary(brand) {
		/*
		var param = "CMD=get_brand_summary&BRAND=" + brand;
		param += "&UID=" + Math.random();

		var http = new xmlHttp();
		http.onexec('std24.php','POST',param,true,cbGetBrandSummary);
		*/
		// 중복 판단
		$.ajax({
			async: true,
			type: 'post',
			url: '/head/standard/std03/GetBrandSummary',
			data: "brand=" + brand,
			success: function(data) {
				//console.log(data);
				cbGetBrandSummary(data.body);
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error")
				console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
			}
		});
	}

	function cbGetBrandSummary(res) {
		var brand_summary = res;
		// 브랜드 현황 초기화
		ResetSummay();

		if (res.length > 0) { // 해당 브랜드의 상품이 존재할때만
			//브랜드 리스트를 select에 option 넣기
			for (i = 0; i < brand_summary.length; i++) {
				if (brand_summary[i].sale_stat_cl == '') { // 상품 상태가 없는 상품
					document.getElementById("stat_none_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '-10') { // 상품 상태가 판매중지인 상품
					document.getElementById("stat_-10_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '-90') { // 상품 상태가 휴지통 상품
					document.getElementById("stat_-90_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '5') { // 상품 상태가 등록대기중인 상품
					document.getElementById("stat_5_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '10') { // 상품 상태가 등록대기중인 상품
					document.getElementById("stat_10_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '20') { // 상품 상태가 품절수동인 상품
					document.getElementById("stat_20_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '30') { // 상품 상태가 품절인 상품
					document.getElementById("stat_30_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				} else if (brand_summary[i].sale_stat_cl == '40') { // 상품 상태가 판매중인 상품
					document.getElementById("stat_40_cnt").innerHTML = Comma(brand_summary[i].goods_cnt);
				}
			}
		}
	}

	function EnableAdd(flag) {
		if (flag) {
			//document.getElementById("chg_brand").style.display = "none";
			$("span#chg_brand").hide();
			$("#CheckBrandMessage").show();
			$("#brand").attr("readonly", false);
			$("[name=c]").val('');
			//$("#admin").text($("[name=c_name]").val() + "(" + $("[name=c_id]").val() + ")");		//로그인 정보로 등록자 세팅.
			document.getElementById("admin").innerHTML = $("[name=c_name]").val() + "(" + $("[name=c_id]").val() + ")";
		} else {
			//document.getElementById("chg_brand").style.display = "";
			$("span#chg_brand").show();
			$("#CheckBrandMessage").hide();
			$("#brand").attr("readonly", true);
			$("[name=c]").val("edit");
		}
	}

	/*
	 *	브랜드 삭제
	 */
	function DeleteCmd(cmd) {
		var f1 = $("form[name=f1]");
		var selectedRowData = gx.gridOptions.api.getSelectedRows();

		if (selectedRowData.length < 1) {
			return alert("삭제할 브랜드를 선택하세요.");
		}

		if (selectedRowData.length > 1) {
			return alert("삭제할 브랜드를 한 개만 선택해주세요.");
		}

		if ($("#brand").value == "none") { // 필수 브랜드인 none은 삭제 못함.
			return alert("'none' 브랜드는 삭제 할 수 없습니다.");
		}
		if (!confirm("삭제하시겠습니까?")) {
			return false;
		}

		const delBrand = selectedRowData[0];

		$.ajax({
			async: true,
			type: 'post',
			url: '/head/standard/std03/Command',
			data: "brand=" + delBrand.brand + "&cmd=" + cmd,
			success: function(data) {
				ResetForm();
				// 브랜드 현황 초기화
				ResetSummay();
				Search(1);
				EnableAdd(true);
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error")
				console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
			}
		});
	}

	function Comma(num) {
		var len, point, str;

		num = num + "";
		point = num.length % 3;
		len = num.length;

		str = num.substring(0, point);
		while (point < len) {
			if (str != "") str += ",";
			str += num.substring(point, point + 3);
			point += 3;
		}

		return str;
	}

	/*
	 *	브랜드 리스트 얻기
	 */
	function GetBrandList() {

		$.ajax({
			async: true,
			type: 'post',
			url: '/head/standard/std03/GetBrandList',
			success: function(data) {
				//console.log(data);
				cbGetBrandList(data.body);
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error")
				console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
			}
		});
	}

	function cbGetBrandList(res) {
		var f1 = document.f1;
		var brand_list = res;
		//console.log("res : "+ res);

		//브랜드 리스트를 select에 option 넣기
		$("select#chg_brand").append("<option value=''>선택하십시오.</option>");
		for (i = 0; i < brand_list.length; i++) {
			$("select#chg_brand").append("<option value='" + brand_list[i].brand + "'>" + brand_list[i].brand_nm + "</option>");
		}
	}

	/*
	*	브랜드 변경 처리
	*/
	function ChangeBrandCode() {
		var f1		= $("form[name=f1]");
		var brand	= $("#brand").val();
		var chg_brand	= $("select[name=chg_brand]").val();
		var cmd		= "chg_brand";

		//console.log(f1.serialize());
		$("[name='cmd']").val(cmd);

		if( brand == "none" ) { // 필수 브랜드인 none은 삭제 못함.
			alert("'none' 브랜드는 변경할 수 없습니다.");
			return false;
		}
		if( chg_brand == "" ) {
			alert("브랜드를 선택해 주십시오.");
			return false;
		}
		if( brand == chg_brand ) {
			alert("같은 브랜드로는 변경할 수 없습니다.\n\n다른 브랜드를 선택해 주십시오.");
			return false;
		}
		if( !confirm("브랜드를 변경하시겠습니까?\n\n해당 브랜드로 등록된 상품의 브랜드도 일괄변경 됩니다.") ) {
			return false;
		}

		$.ajax({
			async: true,
			type: 'post',
			url: '/head/standard/std03/Command',
			data: f1.serialize() + "&cmd=" + cmd,
			success: function(data) {
				//console.log(data);
				//cbDeleteCmd(res);
				cbChangeBrandCode(data);
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error")
				console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
			}
		});

	}

	function cbChangeBrandCode(res) {
		// 브랜드 폼 초기화
		ResetForm();

		// 브랜드 현황 초기화
		ResetSummay();

		//브랜드 리스트 얻기
		GetBrandList();

		EnableAdd(true);
		Search(1);
	}

	const IMG_TYPE_FRONT = 'a';
	const IMG_TYPE_SIZE = 'f';
	let target_file = null;

	function validatePhoto() {
		if (target_file === null || target_file.length === 0) {
			alert("업로드할 이미지를 선택해주세요.");
			return false;
		}

		if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target_file[0].name)) {
			alert("이미지 형식이 아닙니다.");
			return false;
		}

		return true;

	}

	function appendCanvas(size, id, type) {
		var canvas = $("<canvas></canvas>").attr({
			id: id,
			name: id,
			width: size,
			height: size,
			style: "margin:10px",
			"data-type": type
		});

		$("#preview_logo_img").append(canvas);
	}

	function drawImage(e) {
		$('#preview_logo_img canvas').each(function(idx) {
			var size = this.width;
			var canvas = this;
			var ctx = canvas.getContext('2d');
			var image = new Image();

			image.src = e.target.result;

			image.onload = function() {
				ctx.drawImage(this, 0, 0, size, size);
			}
		});
	}


	$(function() {
		$("[name=brand_file]").change(function(e) {
			target_file = this.files;
			if (target_file.length < 1) {
				document.querySelector("#preview_logo_img center").innerHTML = '';
				$("#preview_logo_img canvas").remove();
				return;
			}
			if (validatePhoto() === false) return;

			document.querySelector("#preview_logo_img center").innerHTML = '';
			$("#preview_logo_img canvas").remove();
			
			var fr = new FileReader();
			appendCanvas(80, 'c_80', 'a');

			fr.onload = drawImage;
			fr.readAsDataURL(target_file[0]);

			var f1 = document.f1;
			f1.brand_logo.value = this.files;
		});
	});
</script>


<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
@stop
