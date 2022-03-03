@extends('head_with.layouts.layout-nav')
@section('title','제휴문의')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">제휴문의</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 제휴문의</span>
            </div>
        </div>
        <div>
			<a href="#" id="search_sbtn" onclick="self.close();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    @csrf
    <div class="card_wrap aco_card_wrap">
		<form name="f1" id="f1">
		<input type="hidden" name="cmd" value="edit">
		<input type="hidden" name="idx" value="{{ $idx }}">

		<!-- 업체 기본 정보 -->
		<div class="card shadow">
			<div class="card-body">
				<div class="row_wrap">
					<div class="row">
						<div class="col-12">
							<div class="table-box mobile">
								<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
									<colgroup>
										<col width="120px">
										<col width="40%">
										<col width="120px">
										<col width="40%">
									</colgroup>
									<tbody>
										<tr>
											<th>회사</th>
											<td>
												<div class="txt_box">{{ $company_nm }}</div>
											</td>
											<th>담당자</th>
											<td>
												<div class="txt_box">{{ $name }}</div>
											</td>
										</tr>
										<tr>
											<th>연락처</th>
											<td>
												<div class="txt_box">{{ $phone }}</div>
											</td>
											<th>핸드폰</th>
											<td>
												<div class="txt_box">{{ $mobile }}</div>
											</td>
										</tr>
										<tr>
											<th>주소</th>
											<td colspan="3">
												<div class="txt_box">{{ $address }}</div>
											</td>
										</tr>
										<tr>
											<th>홈페이지 URL</th>
											<td colspan="3">
												<div class="txt_box">{{ $url }}</div>
											</td>
										</tr>
										<tr>
											<th>카테고리</th>
											<td colspan="3">
												<div class="txt_box">{{ $category }}</div>
											</td>
										</tr>
										<tr>
											<th>문의내용</th>
											<td colspan="3">
												<div class="txt_box">{!! $content !!}</div>
											</td>
										</tr>
										<tr>
											<th>유형</th>
											<td>
												<div class="input_box flax_box">
													<select name="type" id="type" class="form-control form-control-sm">
														@foreach($pattypes as $item)
															<option value="{{ $item->code_id }}" @if($type == $item->code_id) selected @endif>{{ $item->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th>상태</th>
											<td>
												<div class="input_box flax_box">
													<select name="state" id="state" class="form-control form-control-sm">
														@foreach($patstates as $item)
															<option value="{{ $item->code_id }}" @if($state == $item->code_id) selected @endif>{{ $item->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th>처리내용</th>
											<td colspan="3">
												<div class="flax_box">
													<textarea name="ans" rows="7" style="width:100%;">{{ $ans }}</textarea>
												</div>
											</td>
										</tr>
										<tr>
											<th>첨부파일</th>
											<td colspan="3">
												<div class="txt_box">
													<a href="{{ $file }}" target="_blank">{{ $application }}</a>
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
		</form>
	</div>
	<div class="row justify-content-center mb-3" style="margin-top:20px;">
        <div class="col text-center">
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm submit-btn">저장</a>
			<a href="#" id="search_sbtn" onclick="self.close();" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">취소</a>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8">

    $(function(){
		$("a").click(function(e){
			e.preventDefault();
		});
		$(".submit-btn").click(function(e){
			e.preventDefault();
			Cmder('edit');

		});
    });

    function Cmder(cmd){
		var ff = document.f1;
		if(cmd == 'edit'){
			if(Validate(ff)){
				/*
				var http = new xmlHttp();
				var param = formData2QueryString(ff);
				http.onexec('pat01.php','POST',param,true,AfterCmd);	
				*/
				$.ajax({
					async: true,
					method: 'put',
					url: '/head/promotion/prm02/store',
					data: $("[name=f1]").serialize(),
					success: function (data) {
                        alert("저장되었습니다.");
						window.opener.Search();
						self.close();
						document.location.href = '/head/promotion/prm02'
					},
					error: function(request, status, error) {
						console.log("error");
						console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});

			}
		}
	}

	function Validate(ff){
		if(ff.ans.value == ''){
			alert('처리내용을 입력해 주십시오.');
			ff.ans.focus();
			return false;
		} 
		return true;
	}

</script>
@stop
