<table class="size_code_table">
	<thead>
		<tr>
			<td colspan="{{ $max_size_cnt + 1 }}" rowspan="2" style="text-align: center; vertical-align: middle; font-size: 30px; font-weight: bold;">사이즈코드표</td>
		</tr>
		<tr></tr>
		<tr></tr>
	</thead>
	<tbody style="table-layout: fixed; border: 2px solid gray;">
	@foreach($size_kinds as $kind)
		<tr>
			<th style="vertical-align: middle; font-size: 15px; font-weight: bold; width: 120px; height: 35px; border: 1px solid lightgrey; background-color: #f2f2f2;">{{ $kind->size_kind_nm }}</th>
			@foreach($kind->sizes as $size)
				<td style="text-align: center; vertical-align: middle; font-size: {{ strlen($size->size_nm) > 4 ? '14px' : '18px' }}; width: 70px; height: 35px; border: 1px solid lightgrey; text-align: center;">{{ $size->size_nm }}</td>
			@endforeach
			@if ($max_size_cnt - count($kind->sizes) > 0)
				@foreach(range(1, $max_size_cnt - count($kind->sizes)) as $i)
					<td style="text-align: center; vertical-align: middle; min-width: 70px; height: 35px; border: 1px solid lightgrey; text-align: center;"></td>
				@endforeach
			@endif
		</tr>
	@endforeach
	</tbody>
</table>
