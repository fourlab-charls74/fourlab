@extends('head_with.layouts.layout-nav')
@section('title','출첵이벤트')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<!-- Page Heading -->
<div class="container-fluid show_layout py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">출첵이벤트</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 출첵이벤트</span>
            </div>
        </div>
        <div>
			@if($evnet_info->idx != "")
				<a href="#" id="search_sbtn" onclick="Del();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">삭제</a>
			@endif
        </div>
    </div>
    @csrf
	<form name="f1" id="f1">
	<input type="hidden" name="cmd" value="{{ $cmd }}">
	<input type="hidden" name="idx" value="{{ $evnet_info->idx }}">
		<div class="card_wrap aco_card_wrap">
			<div class="card">
				<div class="card-header mb-0">
					<a href="#" class="m-0 font-weight-bold">이벤트 정보</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box mobile">
									<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
										<colgroup>
											<col width="120px">
										</colgroup>
										<tbody>
											<tr>
												<th>제목</th>
												<td>
													<div class="flax_box">
														<input type="text" name="subject" class="form-control form-control-sm" value="{{ $evnet_info->title }}" >
													</div>
												</td>
											</tr>
											<tr>
												<th>내용</th>
												<td style="max-width: 700px;">
													<div class="flax_box" style="width: 100%;">
														<textarea name="content" id="content" class="form-control editor1" >{{ $evnet_info->content }}</textarea>
													</div>
												</td>
											</tr>
											<tr>
												<th>기간</th>
												<td>
													<div class="form-inline" style="max-width:500px;">
														<div class="docs-datepicker form-inline-inner input_box">
															<div class="input-group">
																<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $evnet_info->start_dt }}" autocomplete="off"  disable style="width:60%;">
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
																<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $evnet_info->end_dt }}"  autocomplete="off"style="width:60%;">
																<div class="input-group-append">
																	<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																	</button>
																</div>
															</div>
															<div class="docs-datepicker-container"></div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty2">적립금 지급 시점</th>
												<td class="ty2">
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="attend_point_type" id="attend_point_type_n" class="custom-control-input" value="N" @if($evnet_info->attend_point_type != "R") checked @endif>
															<label class="custom-control-label" for="attend_point_type_n">출석체크시</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="attend_point_type" id="attend_point_type_r" class="custom-control-input" value="R" @if($evnet_info->attend_point_type == "R") checked @endif>
															<label class="custom-control-label" for="attend_point_type_r">매월1일</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty2">적립금 지급 금액</th>
												<td class="ty2">
													<div class="flax_box">
														<input type="text" name="attend_point" value="{{ @number_format($evnet_info->attend_point) }}" class="form-control form-control-sm search-all" style="text-align:right;width:100px;" onkeyup="com(this);" /> 원
													</div>
												</td>
											</tr>
											<tr>
												<th>첫출근</th>
												<td>
													<div class="flax_box">
														<div class="form-inline form-check-box">
															<div class="custom-control custom-checkbox">
																<input type="checkbox" name="first_attend_yn" id="first_attend_yn" class="custom-control-input" value="Y" @if($evnet_info->first_attend_yn == "Y") checked @endif>
																<label class="custom-control-label" for="first_attend_yn">첫출근 시 적립금 사용,</label>
															</div>
														</div>
														<div class="txt_box mx-1" style="font-size:13px;font-weight:500;">적립금 :</div><input type="text" name="first_attend_point" value="{{ @number_format($evnet_info->first_attend_point) }}" class="form-control form-control-sm search-all" style="text-align:right;width:100px;" onkeyup="currency(this);"  onfocus="this.select()"/> <div class="txt_box" style="font-size:13px;font-weight:500;">원</div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty2">개근기간 및 적립금</th>
												<td class="ty2">
													<div class="flax_box">
														<div class="txt_box" style="font-size:13px;font-weight:500;">개근기간 :&nbsp;</div><input type="text" name="regular_attend_day" class="form-control form-control-sm search-all" value="{{ $evnet_info->regular_attend_day }}" style="text-align:right; width:100px;" onkeyup="currency(this);"  onfocus="this.select()"/> <div class="txt_box" style="font-size:13px;font-weight:500;">일,</div>
														<div class="txt_box" style="font-size:13px;font-weight:500;">적립금 :&nbsp;</div><input type="text" name="regular_attend_point" value="{{ @number_format($evnet_info->regular_attend_point) }}" class="form-control form-control-sm search-all" style="text-align:right; width:100px;" onkeyup="currency(this);"  onfocus="this.select()"/> <div class="txt_box" style="font-size:13px;font-weight:500;">원</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>배팅</th>
												<td>
													<div class="flax_box">
														<div style="width:100%;max-width:60px;">
															<select name="bet" class="form-control form-control-sm search-all">
																<option value="1" @if($evnet_info->bet == "1") selected @endif> 1 </option>
																<option value="2"  @if($evnet_info->bet == "2") selected @endif> 2 </option>
															</select>
														</div>
														<div class="txt_box ml-1" style="font-size:13px;font-weight:500;">배</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>쇼핑지원금</th>
												<td>
													<div class="flax_box">
														<div class="txt_box" style="margin-right:4px;">사용여부 :</div>
														<div class="form-inline form-radio-box">
															<div class="custom-control custom-radio">
																<input type="radio" name="support_point_yn" id="support_point_y" class="custom-control-input" value="Y" @if($evnet_info->support_point_yn == "Y") checked @endif>
																<label class="custom-control-label" for="support_point_y">예</label>
															</div>
															<div class="custom-control custom-radio">
																<input type="radio" name="support_point_yn" id="support_point_n" class="custom-control-input" value="N" @if($evnet_info->support_point_yn != "Y") checked @endif>
																<label class="custom-control-label" for="support_point_n">아니오</label>
															</div>
														</div>
													</div>
													<div class="form-inline date-select-inbox mt-1" style="max-width:500px;">
														<div class="txt_box" style="width:58px;margin-right:4px;">
															사용일자 :
														</div>
														<div class="docs-datepicker form-inline-inner" style="width:calc(47% - 31px);">
															<div class="input-group">
																<input type="text" class="form-control form-control-sm docs-date" name="support_point_sday" value="{{ $evnet_info->support_point_sday }}" autocomplete="off"  disable style="width:60%;">
																<div class="input-group-append">
																	<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																	</button>
																</div>
															</div>
															<div class="docs-datepicker-container"></div>
														</div>
														<span class="text_line">~</span>
														<div class="docs-datepicker form-inline-inner" style="width:calc(47% - 31px);">
															<div class="input-group">
																<input type="text" class="form-control form-control-sm docs-date" name="support_point_eday" value="{{ $evnet_info->support_point_eday }}"  autocomplete="off"style="width:60%;">
																<div class="input-group-append">
																	<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																	</button>
																</div>
															</div>
															<div class="docs-datepicker-container"></div>
														</div>
													</div>
													<div class="flax_box mt-2">
														<div class="txt_box" style="margin-right:4px;">총지원금액 :</div>
														<input type="text" name="support_point_amt" value="{{ @number_format($evnet_info->support_point_amt) }}" class="form-control form-control-sm search-all" style="text-align:right;width:100px;"
															onkeyup="currency(this);"  onfocus="this.select()"/> 원 ( 0원은 제한없음 )
													</div>
													<div class="flax_box mt-2">
														<div class="txt_box" style="margin-right:4px;">쇼핑지원금액 :</div>
														<div><input type="text" name="support_point" value="{{ @number_format($evnet_info->support_point) }}" class="form-control form-control-sm search-all" style="text-align:right;width:100px;"
															onkeyup="currency(this);"  onfocus="this.select()"/></div>
															<div> 원</div>
													</div>
													<div class="flax_box mt-2">
														<div class="txt_box" style="margin-right:4px;">유효기간 :</div>
														<select name="support_point_expireday" class="form-control form-control-sm search-all" style="width:100px;">
															<option value="0" @if($evnet_info->support_point_expireday == "0") selected @endif>당일</option>
															<option value="1" @if($evnet_info->support_point_expireday == "1") selected @endif>내일</option>
															<option value="2" @if($evnet_info->support_point_expireday == "2") selected @endif>3일</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>사용여부</th>
												<td>
													<div class="flax_box">
														<div class="form-inline form-radio-box">
															<div class="custom-control custom-radio">
																<input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N" @if($evnet_info->is_use != "Y") checked @endif />
																<label class="custom-control-label" for="use_yn_n">미사용</label>
															</div>
															<div class="custom-control custom-radio">
																<input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" @if($evnet_info->is_use == "Y") checked @endif />
																<label class="custom-control-label" for="use_yn_y">사용</label>
															</div>
														</div>
													</div>
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
	</form>
	<div class="row justify-content-center mt-3">
        <div class="col text-center">
            <a href="#" class="btn btn-sm btn-primary shadow-sm submit-btn" onclick="Save();">저장</a>
            <a href="#" class="btn btn-sm btn-secondary" onclick="document.f1.reset();">취소</a>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<script type="text/javascript" charset="utf-8">
	var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
			width:700,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/head/prm20',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions);
    });
	
	function Validate(){
		var ff = document.f1;
			
		if ($("[name=subject]").val() == "") {
			alert("이벤트 제목을 입력해 주십시오.");
			$("[name=subject]").focus();
			return false;
		}
		if ($("[name=sdate]").val() == "") {
			alert("시작일을 입력해 주십시오.");
			$("[name=sdate]").focus();
			return false;
		}
		if ($("[name=edate]").val() == "") {
			alert("마감일을 입력해 주십시오.");
			$("[name=edate]").focus();
			return false;
		}
		if (!ff.use_yn[0].checked && !ff.use_yn[1].checked) {
			alert("사용여부를 채크해 주십시오.");		
			return false;
		}
		return true;
	}

	function CheckAttendType(attend_type){
		if(attend_type.value == "N"){
			$("[name=first_attend_yn]").attr("disabled", false);
		} else if(attend_type.value == "R"){
			$("[name=first_attend_yn]").prop("check", false);
			$("[name=first_attend_yn]").attr("disabled", true);
		}
	}

	function Del(){
		if(confirm('정말로 삭제하시겠습니까?')){
			var ff = $("[name=f1]");
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/promotion/prm20/del/',
				data: ff.serialize(),
				success: function (data) {
					console.log(data);
					if(data.return_code == 1){
						alert('삭제하였습니다.');
						window.opener.Search();
						self.close();
					}else{
						alert(data.responseText );

					}

				},
				complete:function(){
					//_grid_loading = false;
				},
				error: function(request, status, error) {
					console.log("error");
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});

			/*
			var http = new xmlHttp();
			var param = "CMD=del&IDX=" + ff.IDX.value;
			http.onexec('prm04.php','POST',param,true,AfterDel);	
			*/
		}
	}



	function Save(){
		if(Validate()){
			if(confirm('저장하시겠습니까?')){
				var ff = $("[name=f1]");
				$.ajax({
					async: true,
					type: 'put',
					url: '/head/promotion/prm20/store/',
					data: ff.serialize(),
					success: function (data) {
						if(data.return_code == 1){
							alert("저장하였습니다.");
                            opener?.Search?.();
                            window.close();
						}else{
							alert(data.responseText );
						}
					},
					complete:function(){
						//_grid_loading = false;
					},
					error: function(request, status, error) {
						console.log(request);
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});

			}
		}
	}

    $("a").click(function(e){
        e.preventDefault();
    });
</script>
@stop
