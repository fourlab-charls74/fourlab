@extends('shop_with.layouts.layout-nav')
@section('title','주문내역 - 주문상품관리')
@section('content')
<div class="container-fluid show_layout py-3">
	<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
		<div>
			<h3 class="d-inline-flex">주문내역</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 주문상품 관리</span>
				<span>/ {{ $ord_no }}</span>
			</div>
		</div>
		<div>
			<a href="#" class="btn btn-sm btn-primary shadow-sm update-btn">저장</a>
			<a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
		</div>
	</div>

	<form method="get" name="search">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#" class="m-0 font-weight-bold">주문상품 정보</a>
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
										<tr>
											<th>상품명</th>
											<td>{{ $goods_list->goods_nm }}</td>
										</tr>
										<tr>
											<th>주문옵션</th>
											<td>{{ $goods_list->opt_val }}</td>
										</tr>
										<tr>
											<th>수량</th>
											<td>{{ $goods_list->qty }} 개</td>
										</tr>
										<tr>
											<th>판매가격</th>
											<td>{{ $goods_list->price }} 원</td>
										</tr>
										<tr>
											<th>출고형태</th>
											<td>{{ $goods_list->ord_type_nm }}</td>
										</tr>
										<tr>
											<th>판매구분</th>
											<td>
												<select class="form-control form-control-sm search-all" name="oro_kind" id="ord_kind">
													<option value="">출고구분</option>
													@foreach($ord_kinds as $ord_kind)
													<option value="{{$ord_kind->id}}" {{ (@$goods_list->ord_kind == $ord_kind->id) ? "selected" : "" }}>{{$ord_kind->val}}</option>
													@endforeach
												</select>
											</td>
										</tr>
										<tr>
											<th>배송업체</th>
											<td>{{ $goods_list->code_val }}</td>
										</tr>
										<tr>
											<th>택배송장번호</th>
											<td>{{ $goods_list->dlv_no }}</td>
										</tr>
										<tr>
											<th>최종처리일자</th>
											<td>{{ $goods_list->upd_date }}</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</form>
</div>



<script>
const ord_no		= '{{$ord_no}}';
const ord_opt_no	= '{{$ord_opt_no}}';

$(".update-btn").click(function(){

	if( $('#ord_kind').val() == "" ){
		alert("출고구분은 반드시 선택해야 합니다.");
		return false;
	}

	$.ajax({
		async: true,
		type: 'put',
		url: '/shop/order/ord01/order-goods/' + ord_no + '/' + ord_opt_no,
		data: {
			"ord_kind": $('#ord_kind').val()
		},
		success: function(data) {
			alert("출고구분이 수정되었습니다.");
			opener.location.reload();
			self.close();
		},
		error: function(request, status, error) {
			console.log("error")
		}
	});

});
</script>
@stop

