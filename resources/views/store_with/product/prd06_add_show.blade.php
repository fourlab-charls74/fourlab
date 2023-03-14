@extends('store_with.layouts.layout-nav')
@section('title','온라인상품 재고예외 등록')
@section('content')
<div class="show_layout py-3">
    <form name="f1">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">온라인상품 재고 예외 등록</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="15%">
											<col width="35%">
											<col width="15%">
											<col width="35%">
										</colgroup>
										<tbody>
											<tr>
												<th>바코드</th>
                                                <td>
													<div class="flax_box">
														<input type="text" name="prd_cd" class="form-control form-control-sm">
													</div>
                                                </td>
                                                <th>정보</th>
                                                <td>
													<div class="flax_box">
														<input type="text" name="comment" class="form-control form-control-sm">
													</div>
                                                </td>
                                            </tr>
											<tr>
												<th>창고 제한 수</th>
                                                <td>
													<div class="flax_box">
														<input type="text" name="storage_limit_qty" class="form-control form-control-sm">
													</div>
                                                </td>
                                                <th>매장 제한 수</th>
                                                <td>
													<div class="flax_box">
														<input type="text" name="store_limit_qty" class="form-control form-control-sm">
													</div>
                                                </td>
                                            </tr>
										</tbody>
									</table>
									<div class="form-group" style="padding-top:5px;text-align:right;color:#FF0000;font-weight:bold;">
									※ 창고, 매장의 제한 수량을 입력하지 않으면 예외처리 안함
									</div>
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
		var frm = $('form');

		if( $('input[name="prd_cd"]').val() === "" )
		{
			alert('바코드는 반드시 입력해야 합니다.');
			$('input[name="prd_cd"]').focus();

			return false;
		}

		if( $('input[name="prd_cd"]').val().length < 10 || $('input[name="prd_cd"]').val().length > 16 )
		{
			alert("바코드 형식이 잘못되었습니다.");
			$('input[name="prd_cd"]').focus();

			return false;
		}

		if( isNaN($('input[name="storage_limit_qty"]').val()) )
		{
			alert("창고 제한 수는 반드시 숫자로 입력해야 합니다.");
			$('input[name="storage_limit_qty"]').val("");
			$('input[name="storage_limit_qty"]').focus();

			return false;
		}

		if( isNaN($('input[name="store_limit_qty"]').val()) )
		{
			alert("매장 제한 수는 반드시 숫자로 입력해야 합니다.");
			$('input[name="store_limit_qty"]').val("");
			$('input[name="store_limit_qty"]').focus();

			return false;
		}

		if( $('input[name="storage_limit_qty"]').val() === "" && $('input[name="store_limit_qty"]').val() === "" )
		{
			alert("창고 제한 수와 매장 제한 수 중에 하나 이상의 예외값을 등록하셔야 합니다.");
			$('input[name="storage_limit_qty"]').focus();

			return false;
		}

		$.ajax({
			async: true,
			type: 'post',
			url: '/store/product/prd06/add_show',
			data: $("[name=f1]").serialize(),
			success: function (data) {
				if( data.code == "200" )
				{
					alert("온라인상품 재고 예외 데이터가 등록되었습니다.");
					window.opener.Search();
					self.close();
				} 
				else 
				{
					alert(data.code);
					alert("데이터 등록이 실패하였습니다.");
				}
			},
			error: function(request, status, error) {
				alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
				console.log("error");
			}
		});

	}
</script>
@stop