<table class="document_table">
	<tbody>
		<tr>
			<td colspan="26" rowspan="2" style="font-size: 30px;font-weight: bold;text-align: center;vertical-align:center;">배분현황</td>
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
	@if(is_array($hd[0]))
		<td rowspan="6" colspan="{{ $hd[0][count($hd[0]) - 1] === 'size_kind_nm' ? 2 : 1 }}" style="text-align:center;vertical-align:center;font-size:12px;font-weight:bold;background-color:#e2e2e2;border:1px solid #000000;">
			@foreach($hd[0] as $i => $ss)
				@if($ss !== 0 && is_object($ss) && isset($ss->size_cd)) {{ $ss->size_cd }} @endif @if($i < count($hd[0]) - 2) <br/> @endif
			@endforeach
		</td>
	@else
		<td rowspan="6" colspan="{{ $hd[2] ?? 1 }}" style="text-align:center;vertical-align:center;font-size:12px;font-weight:bold;background-color:#ffeeee;border:1px solid #000000;">{{ $hd[0] ?? '' }}</td>
	@endif
	@endforeach
		</tr>
	@foreach(range(1, 5) as $i)
		<tr></tr>
	@endforeach
	@foreach($list as $row)
		<tr>
		@foreach($headers as $hd)
		@if(is_array($hd[0]))
			<td 
				colspan="{{ $hd[0][count($hd[0]) - 1] === 'size_kind_nm' ? 2 : 1 }}" 
			    style="height:30px;vertical-align:center;font-size:12px;border:1px solid #000000;@if(isset($row->sum)) background-color:#ffffdd;font-weight:bold; @endif @if(isset($row->total)) background-color:#eeeeee;font-weight:bold; @endif"
			>
				{{ $hd[0][count($hd[0]) - 1] === 'size_kind_nm' ? '' : ($row->{$hd[0][count($hd[0]) - 1] ?? ''} ?? 0) }}
			</td>
		@else
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
		@endif
		@endforeach
		</tr>
	@endforeach
	</tbody>
</table>
