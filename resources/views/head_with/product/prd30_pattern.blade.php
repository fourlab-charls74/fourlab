@extends('head_with.layouts.layout-nav')
@section('title','사방넷 상세정보 연동 설정')
@section('content')

<div class="show_layout py-3">
	<form method="post" name="f1">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">사방넷 상세정보 연동 설정</a>
				</div>
				<div class="card-body mt-1">
					<div class="row_wrap">

						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="20%">
										</colgroup>
										<tbody>
											<tr>
												<th>a 태그 제거</th>
												<td>
												<div class="custom-control custom-checkbox">
														<input type="checkbox" name="pattern_a" id="pattern_a" class="custom-control-input" value="Y" @if( $shop_config['pattern']['a']['use_yn'] == "Y" ) checked @endif>
														<label class="custom-control-label" for="pattern_a">&lt;a&gt; ~ &lt;/a&gt; 태그 제거후 사방넷에 상품정보를 전송합니다.</label>
													</div>
												</td>
											</tr>
											<tr>
												<th>iframe 태그 제거</th>
												<td>
												<div class="custom-control custom-checkbox">
														<input type="checkbox" name="pattern_iframe" id="pattern_iframe" class="custom-control-input" value="Y" @if( $shop_config['pattern']['iframe']['use_yn'] == "Y" ) checked @endif>
														<label class="custom-control-label" for="pattern_iframe">&lt;iframe&gt; ~ &lt;/iframe&gt; 태그 제거후 사방넷에 상품정보를 전송합니다.</label>
													</div>
												</td>
											</tr>
											<tr>
												<th>script 태그 제거</th>
												<td>
												<div class="custom-control custom-checkbox">
														<input type="checkbox" name="pattern_script" id="pattern_script" class="custom-control-input" value="Y" @if( $shop_config['pattern']['script']['use_yn'] == "Y" ) checked @endif>
														<label class="custom-control-label" for="pattern_script">&lt;script&gt; ~ &lt;/script&gt; 태그 제거후 사방넷에 상품정보를 전송합니다.</label>
													</div>
												</td>
											</tr>
											<tr>
												<th>style 태그 제거</th>
												<td>
												<div class="custom-control custom-checkbox">
														<input type="checkbox" name="pattern_style" id="pattern_style" class="custom-control-input" value="Y" @if( $shop_config['pattern']['style']['use_yn'] == "Y" ) checked @endif>
														<label class="custom-control-label" for="pattern_style">&lt;style&gt; ~ &lt;/style&gt; 태그 제거후 사방넷에 상품정보를 전송합니다.</label>
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

    <div class="resul_btn_wrap mt-3 d-block">
        <a href="#" onclick="Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
        <a href="#" onclick="window.close()" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
    </div>

</div>


<script language="javascript">
function Save()
{
	ret	= confirm("변경된 내용을 수정 하시겠습니까?");

	if( ret )
	{

		const data = $('form[name="f1"]').serialize();

		$.ajax({
			async: true,
			type: 'put',
			url: '/head/product/prd30/pattern',
			data: data,
			success: function (data) {
				if( data.result_code == "200" )
				{
					alert("수정되었습니다.");
					location.reload();
				}
				else
				{
					alert("시스템 오류입니다. 관리자에게 문의하십시요." + data.result_code);
				}
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});

	}
}
</script>

@stop