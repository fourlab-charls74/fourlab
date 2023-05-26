<table class="document_table">
	<tbody>
		<tr>
		@foreach(range(1, 12) as $i)
			<td></td>
		@endforeach
			<td colspan="8" rowspan="2">(출고) 거래명세서</td>
		</tr>
		<tr></tr>
		<tr>
			<td></td>
			<td>거래일자 : 2023-05-26</td>
		@foreach(range(1, 22) as $i)
			<td></td>
		@endforeach
			<td colspan="5">구분 : 출고</td>
			<td>전표번호 : {{ @$document_number }}</td>
		</tr>
		<tr>
			<td rowspan="5">공 급 자</td>
			<td colspan="3">등록번호</td>
			<td colspan="13"></td>			
			<td rowspan="5">공급받는자</td>
			<td colspan="3">등록번호</td>
			<td colspan="13">-- --</td>
		</tr>
		<tr>
			<td colspan="3">상호</td>
			<td colspan="5"></td>			
			<td colspan="3">성명</td>
			<td colspan="4"></td>
			<td colspan="1">(인)</td>
			<td colspan="3">상호</td>
			<td colspan="5">{{ @$store_nm }}</td>			
			<td colspan="3">성명</td>
			<td colspan="4"></td>
			<td colspan="1">(인)</td>
		</tr>
		<tr>
			<td colspan="3">주소</td>
			<td colspan="13"></td>
			<td colspan="3">주소</td>
			<td colspan="13">{{ @$store_addr }}</td>
		</tr>
		<tr>
			<td colspan="3">업태</td>
			<td colspan="5"></td>
			<td colspan="3">종목</td>
			<td colspan="5"></td>
			<td colspan="3">업태</td>
			<td colspan="5"></td>
			<td colspan="3">종목</td>
			<td colspan="5"></td>
		</tr>
		<tr>
			<td colspan="3">전화</td>
			<td colspan="5"></td>
			<td colspan="3">팩스</td>
			<td colspan="5"></td>
			<td colspan="3">전화</td>
			<td colspan="5">{{ @$store_phone }}</td>
			<td colspan="3">팩스</td>
			<td colspan="5">{{ @$store_fax }}</td>
		</tr>
		<tr>
			<td>No.</td>
			<td colspan="5">품번</td>
			<td colspan="11">품명</td>
			<td colspan="2">색상</td>
			<td colspan="3">사이즈</td>
			<td colspan="2">수량</td>
			<td colspan="2">판매가</td>
			<td colspan="3">판매가합</td>
			<td colspan="2">출고가</td>
			<td colspan="3">출고가합</td>
		</tr>
	@foreach(range(1, $one_sheet_count) as $i)
		<tr>
			<td>{{ $i }}</td>
			<td colspan="5">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->prd_cd ?? '' }}</td>
			<td colspan="11">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->goods_nm ?? '' }}</td>
			<td colspan="2">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->color ?? '' }}</td>
			<td colspan="3">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->size ?? '' }}</td>
			<td colspan="2">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->qty ?? '' }}</td>
			<td colspan="2">@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ number_format($products[($i - 1) + ($sheet_num * $one_sheet_count)]->price ?? 0) }} @endif</td>
			<td colspan="3">@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ number_format($products[($i - 1) + ($sheet_num * $one_sheet_count)]->total_price ?? 0) }} @endif</td>
			<td colspan="2">@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ number_format($products[($i - 1) + ($sheet_num * $one_sheet_count)]->release_price ?? 0) }} @endif</td>
			<td colspan="3">@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ number_format($products[($i - 1) + ($sheet_num * $one_sheet_count)]->total_release_price ?? 0) }} @endif</td>
		</tr>
	@endforeach
		<tr>
			<td colspan="22">합계</td>
			<td colspan="2">{{ number_format(array_reduce($products, function ($a, $c) { return $a + ($c->qty * 1); }, 0)) }}</td>
			<td colspan="2"></td>
			<td colspan="3">{{ number_format(array_reduce($products, function ($a, $c) { return $a + ($c->total_price * 1); }, 0)) }}</td>
			<td colspan="2"></td>
			<td colspan="3">{{ number_format(array_reduce($products, function ($a, $c) { return $a + ($c->total_release_price * 1); }, 0)) }}</td>
		</tr>
		<tr>
			<td colspan="5" rowspan="2">입금계좌</td>
			<td colspan="19" rowspan="2"></td>
			<td colspan="4" rowspan="4">인수자</td>
			<td colspan="4" rowspan="4"></td>
			<td colspan="2" rowspan="4">(인)</td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="5" rowspan="2">물류주소</td>
			<td colspan="11" rowspan="2"></td>
			<td colspan="2" rowspan="2">연락처</td>
			<td colspan="6" rowspan="2"></td>
		</tr>
		<tr></tr>
	</tbody>
</table>
