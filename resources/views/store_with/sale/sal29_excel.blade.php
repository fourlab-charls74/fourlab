<table class="document_table">
	<tbody>
		<tr>
			<td colspan="{{ 15 + count($size_columns) }}" rowspan="2" style="font-size: 30px;font-weight: bold;text-align: center;vertical-align:center;">배분현황</td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="3" style="font-size: 14px;vertical-align:center;">배분일자 - {{ $exp_dlv_date }}</td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: 14px;vertical-align:center;">출고일자 - {{ $exp_dlv_date }}</td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: 14px;vertical-align:center;">배분차수 - {{ $rel_order }}</td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: 14px;vertical-align:center;">정렬 - @if($groupby === 'store') 매장-상품별 @elseif($groupby === 'product') 상품-매장별 @endif</td>
		</tr>
		<tr>
		@foreach($headers as $hd) 
			<td rowspan="6" colspan="{{ $hd[2] ?? 1 }}" style="text-align:center;vertical-align:center;font-size:12px;font-weight:bold;background-color:#ffeeee;border:1px solid #000000;">{{ $hd[0] ?? '' }}</td>
		@endforeach
		@foreach($size_columns as $size)
			<td rowspan="6" style="text-align:center;vertical-align:center;font-size:12px;font-weight:bold;background-color:#e2e2e2;border:1px solid #000000;">
			@foreach($size as $ss)
				@if($ss !== 0 && isset($ss->size_cd)) {{ $ss->size_cd }} @endif <br/>
			@endforeach
			</td>
		@endforeach
		</tr>
	@foreach(range(1, 5) as $i)
		<tr></tr>
	@endforeach
	@foreach($list as $row)
		<tr>
		@foreach($headers as $hd)
			<td 
				colspan="{{ $hd[2] ?? 1 }}" 
			    style="height:30px;vertical-align:center;font-size:12px;border:1px solid #000000;@if(isset($row->sum)) background-color:#ffffdd;font-weight:bold; @endif @if(isset($row->total)) background-color:#eeeeee;font-weight:bold; @endif"
			>
				@if(isset($row->sum) || isset($row->total))
					@if(($hd[1] ?? '') === 'baebun_type')
						<b>합계</b>
					@elseif(($hd[1] ?? '') === 'qty' || ($hd[1] ?? '') === $group_key)
						{{ $row->{$hd[1] ?? ''} ?? '' }}
					@endif
				@else 
					{{ $row->{$hd[1] ?? ''} ?? '' }} 
				@endif
			</td>
		@endforeach
		@foreach($size_columns as $idx => $size)
			<td style="height:30px;vertical-align:center;font-size:12px;border:1px solid #000000;@if(isset($row->sum)) background-color:#ffffdd;font-weight:bold; @endif @if(isset($row->total)) background-color:#eeeeee;font-weight:bold; @endif">
				{{ $row->{'SIZE_' . $idx} ?? 0 }}
			</td>
		@endforeach
		</tr>
	@endforeach
	</tbody>
</table>
