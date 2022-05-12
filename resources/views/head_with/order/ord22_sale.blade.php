@extends('head_with.layouts.layout-nav')
@section('title','판매처 택배송장 목록 받기')
@section('content')

<div class="container-fluid show_layout py-3">
	<div class="card_wrap aco_card_wrap">
		<div class="card shadow mb-4">
			<div class="d-sm-flex card-header mb-0 justify-content-between">
				<a href="#">판매처 택배송장 목록 받기</a>
				<button id="download-list" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
				<i class="bx bx-download fs-16"></i> 엑셀다운로드
				</button>
			</div>
				
			<div class="search_cum_form">
				<form action="/head/order/ord22/download/baesong_list/sale" method="get" name="beasong_list">
					<input type="hidden" name="fields">
					@foreach ($requests as $key => $val)
					<input type="hidden" name="{{ $key }}" value="{{$val}}">
					@endforeach
					<div class="row">
						<!-- sale_places -->
						<div class="col-lg inner-td">
							<div class="form-group">
								<label for="sale_place">판매처</label>
								<div class="flax_box">
									<select name='sale_place' class="form-control form-control-sm" onchange="chgSalePlace();">
										<option value=''>전체</option>
										@foreach ($sale_places as $val)
											<option value='{{ $val->com_id }}' @if($val->com_id == $sale_place) selected @endif>{{ $val->com_nm }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="card-body search_cum_form pt-3">
			<div class="row">
				<div class="col-lg-5">
				<div class="table-responsive">
					<table class="table table-bordered th_border_none">
						<thead>
							<tr>
								<th>전체컬럼</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<select multiple class="form-control" id="columns" style="height:500px">
									@foreach ($columns as $column)
										<option value="{{$column->name}}">{{$column->value}}</option>
									@endforeach
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				</div>
				<div class="col-lg-1 colmove_btn_wrap">
					<div class="colmove_btn">
					<a href="#" class="col btn btn-sm btn-primary shadow-sm" id="addColumn" style="max-width:110px;">
						<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
						</svg>
					</a>
					<a href="#" class="col btn btn-sm btn-primary shadow-sm align-self-start" id="deleteField" style="max-width:110px;">
						<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
						</svg>
					</a>
					</div>
				</div>
				<div class="col-lg-5">
				<div class="table-responsive">
					<table class="table table-bordered th_border_none">
						<thead>
							<tr>
								<th>선택컬럼</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<select multiple class="form-control" id="fields" style="height:500px">
										@foreach ($fields as $field)
										<option value="{{$field->name}}">{{$field->value}}</option>
										@endforeach
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				</div>
				<div class="col-lg-1 colmove_btn_wrap">
					<div class="colmove_btn">
					<a href="#" class="col btn btn-sm btn-primary shadow-sm" id="upField" style="max-width:110px;">
						<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
						</svg>
					</a>
					<a href="#" class="col btn btn-sm btn-primary shadow-sm" id="downField" style="max-width:110px;">
						<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-down-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/>
						</svg>
					</a>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
<script language="javascript">
  //컬럼에 선택된 컬럼 추가.
  $("#addColumn").click(function(e){
	e.preventDefault();

	// 백앤드에서 받아온 컬럼명과 필드명이 다른 경우 문제가 발생하여 중복 검사를 value 값이 아닌 text 값으로 변경하였음.
	const fields = document.querySelectorAll("#fields > option");
	let texts = [];
	for (let i = 0; i < fields.length; i++) {
		texts[i] = fields[i].innerText;
	}
	$("#columns > option:selected").each(function(idx) {
		const text = this.innerText;
		if (texts.includes(text)) {
			return;
		} else {
			$("#fields").append("<option value='"+this.value+"'>"+this.innerHTML+"</option>");
		}
	});

  });

  //컬럼에서 선택된 컬럼 제거
  $("#deleteField").click(function(e){
	e.preventDefault();

	$("#fields > option:selected").each(function(idx){
	  $(this).remove();
	});
  });

  $("#upField").click(function(e){
	e.preventDefault();

	var options = $("#fields > option:selected");

	if (options.length === 0) return;

	options.each(function(){
	  var idx = this.index;
	  var addHtml = "<option value='"+this.value+"' selected>"+this.innerHTML+"</option>";

	  $("#fields > option:nth-child(" + (idx) + ")").before(addHtml);

	  $(this).remove();
	});
  });

  $("#downField").click(function(e){
	e.preventDefault();

	var options = $("#fields > option:selected");

	if (options.length === 0) return;

	for (var i = options.length -1; i >= 0; i--) {
	  var target = $("#fields > option:selected")[i];
	  var idx = target.index + 2;
	  var addHtml = "<option value='"+target.value+"' selected>"+target.innerHTML+"</option>";

	  $("#fields > option:nth-child(" + (idx) + ")").after(addHtml);

	  target.remove();
	}
  });

  $("#download-list").click(function(e){
	e.preventDefault();
	var fields = [];

	$('#fields > option').each(function(){
	  fields.push(this.value);
	});

	$("[name=fields]").val(fields.join(','));

	$("[name=ord_state]").val('30');

	$('[name=beasong_list]').submit();
  });


function chgSalePlace()
{
	$('[name=beasong_list]').attr("action", "/head/order/ord22/show/sale");
	$('[name=beasong_list]').submit();
}
</script>
@stop
