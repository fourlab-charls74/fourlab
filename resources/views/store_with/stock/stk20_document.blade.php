<table class="document_table">
	<tbody>
		<tr>
		@foreach(range(1, 20) as $i)
			<td></td>
		@endforeach
			<td colspan="3" rowspan="2">보내는 매장<br />직원 성명</td>
			<td colspan="3" rowspan="2">받는 매장<br />직원 성명</td>
		</tr>
		<tr></tr>
		<tr>
		@foreach(range(1, 6) as $i)
			<td></td>
		@endforeach
			<td colspan="14" rowspan="2">RT 전표</td>
			<td colspan="3" rowspan="2"></td>
			<td colspan="3" rowspan="2"></td>
		</tr>
		<tr></tr>
		<tr></tr>
		<tr>
			<td colspan="3">구분</td>
			<td colspan="4" style="@if (isset($type)) background-color:yellow; @endif">{{ @$type }}</td>
			<td colspan="3">보내는 매장</td>
			<td colspan="6" style="@if (isset($dep_store_nm)) background-color:yellow; @endif">{{ @$dep_store_nm }}</td>
			<td colspan="3">이동일자</td>
			<td colspan="2" style="@if (isset($prc_rt_yyyy)) background-color:yellow; @endif">{{ @$prc_rt_yyyy }}</td>
			<td style="@if (isset($prc_rt_yyyy)) background-color:yellow; @endif">년</td>
			<td style="@if (isset($prc_rt_mm)) background-color:yellow; @endif">{{ @$prc_rt_mm }}</td>
			<td style="@if (isset($prc_rt_mm)) background-color:yellow; @endif">월</td>
			<td style="@if (isset($prc_rt_dd)) background-color:yellow; @endif">{{ @$prc_rt_dd }}</td>
			<td style="@if (isset($prc_rt_dd)) background-color:yellow; @endif">일</td>
		</tr>		
		<tr>
			<td colspan="3">전표번호</td>
			<td colspan="4" style="@if (isset($document_number)) background-color:yellow; @endif">{{ @$document_number }}</td>
			<td colspan="3">받는 매장</td>
			<td colspan="6" style="@if (isset($store_nm)) background-color:yellow; @endif">{{ @$store_nm }}</td>
			<td colspan="3">확정일자</td>
			<td colspan="2" style="@if (isset($fin_rt_yyyy)) background-color:yellow; @endif">{{ @$fin_rt_yyyy }}</td>
			<td style="@if (isset($fin_rt_yyyy)) background-color:yellow; @endif">년</td>
			<td style="@if (isset($fin_rt_mm)) background-color:yellow; @endif">{{ @$fin_rt_mm }}</td>
			<td style="@if (isset($fin_rt_mm)) background-color:yellow; @endif">월</td>
			<td style="@if (isset($fin_rt_dd)) background-color:yellow; @endif">{{ @$fin_rt_dd }}</td>
			<td style="@if (isset($fin_rt_dd)) background-color:yellow; @endif">일</td>
		</tr>		
		<tr>
			<td colspan="1">No.</td>
			<td colspan="4">품번</td>
			<td colspan="9">품명</td>
			<td colspan="2">색상</td>
			<td colspan="2">사이즈</td>
			<td colspan="2">수량</td>
			<td colspan="6">접수메모</td>
		</tr>
	@foreach(range(1, 25) as $i)
		@php
			$isset_product = isset($products[($i - 1) + ($sheet_num * 25)]);
		@endphp
		<tr>
			<td colspan="1" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $i + ($sheet_num * 25) }} @endif</td>
			<td colspan="4" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $products[($i - 1) + ($sheet_num * 25)]->prd_cd ?? '' }} @endif</td>
			<td colspan="9" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $products[($i - 1) + ($sheet_num * 25)]->goods_nm ?? '' }} @endif</td>
			<td colspan="2" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $products[($i - 1) + ($sheet_num * 25)]->color ?? '' }} @endif</td>
			<td colspan="2" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $products[($i - 1) + ($sheet_num * 25)]->size ?? '' }} @endif</td>
			<td colspan="2" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $products[($i - 1) + ($sheet_num * 25)]->qty ?? '' }} @endif</td>
			<td colspan="6" style="@if ($isset_product) background-color:yellow; @endif">@if ($isset_product) {{ $products[($i - 1) + ($sheet_num * 25)]->rec_comment ?? ''}} @endif</td>
		</tr>
	@endforeach
		<tr>
			<td colspan="18" rowspan="2">합계</td>
			<td colspan="2" rowspan="2">{{ array_reduce($products, function ($a, $c) { return $a + ($c->qty * 1); }, 0) }}</td>
			<td colspan="6" rowspan="2"></td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="26">(주) 알펜인터내셔널</td>
		</tr>		
		<tr>
			<td colspan="26">Fax. 031) 766-3106</td>
		</tr>
	</tbody>
</table>
