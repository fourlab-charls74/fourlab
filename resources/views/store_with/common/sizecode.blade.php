@extends('store_with.layouts.layout-nav')
@section('title','사이즈코드표')
@section('content')

<style>
	.size_code_table { font-size: 13px; table-layout: fixed; border: 2px solid gray; }
	.size_code_table th { padding: 0 10px; min-width: 100px; height: 35px; border: 1px solid lightgrey; background-color: #f2f2f2; }
	.size_code_table td { padding: 0 3px; min-width: 65px; height: 35px; border: 1px solid lightgrey; text-align: center; }
</style>

<div class="pt-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between pb-2">
		<h3 class="d-inline-flex">사이즈코드표</h3>
		<div>
			<a href="javascript:excelDownload();" class="btn btn-sm btn-primary"><i class="fas fa-download fa-sm text-white-50 mr-2"></i>엑셀다운로드</a>
			<a href="javascript:window.close();" class="btn btn-sm btn-outline-primary"><i class="fas fa-times fa-sm mr-2"></i>닫기</a>
		</div>
	</div>
	<div class="show_layout py-0 px-sm-0" id="div_grid">
		<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
			<div class="table-responsive d-flex justify-content-center">
				<table class="size_code_table">
					<tbody>
					@foreach($size_kinds as $kind)
						<tr>
							<th>{{ $kind->size_kind_nm }}</th>
						@foreach($kind->sizes as $size)
							<td>{{ $size->size_nm }}</td>
						@endforeach
						@if ($max_size_cnt - count($kind->sizes) > 0)	
						@foreach(range(1, $max_size_cnt - count($kind->sizes)) as $i)
							<td></td>
						@endforeach
						@endif
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	function excelDownload() {
		location.href = "/store/api/sizecode/download";
	}
</script>
@stop
