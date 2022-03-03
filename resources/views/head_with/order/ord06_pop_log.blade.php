@extends('head_with.layouts.layout-nav')
@section('title','뱅크다 - 입금수집내역 조회')
@section('content')


<form name="f1">
	<input type="hidden" name="number[]">
	<input type="hidden" name="bkname[]">
	<input type="hidden" name="bankda_id[]">
	<input type="hidden" name="bankda_pwd[]">
	<input type="hidden" name="use_yn[]">
	<div class="container-fluid show_layout pt-3">
		<div class="page_tit d-flex align-items-center justify-content-between mb-0">
			<div>
				<h3 class="d-inline-flex">입금수집내역 조회</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 주문</span>
					<span>/ 입금내역(뱅크다)</span>
					<span>/ 입금수집내역 조회</span>
				</div>
			</div>
			<div>
				<a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
			</div>
		</div>

		<div class="card shadow mt-3">
			<div class="card-body shadow">
				<div class="table-responsive">
					<textarea style="width: 100%; height: 400px;" readonly>{{ $log }}</textarea>
				</div>
			</div>
		</div>
	</div>
</form>
@stop
