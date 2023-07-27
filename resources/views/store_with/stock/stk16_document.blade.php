<table class="document_table">
	<tbody>
		<tr>
		@foreach(range(1, 10) as $i)
			<td></td>
		@endforeach
			<td colspan="14" rowspan="2">상품반품 명세서</td>
		</tr>
		<tr></tr>
		<tr>
			<td></td>
			<td>거래일자 : {{ @$receipt_date }}</td>
		@foreach(range(1, 26) as $i)
			<td></td>
		@endforeach
			<td>반품번호 : {{ @$sgr_cd }}</td>
		</tr>
		<tr>
			<td rowspan="5">반품업체</td>
			<td colspan="3">등록번호</td>
			<td colspan="13">{{ @$business_registration_number }}</td>			
			<td rowspan="5">반품창고</td>
			<td colspan="3">창고코드</td>
			<td colspan="13">{{ @$storage_cd }}</td>
		</tr>
		<tr>
			<td colspan="3">상호</td>
			<td colspan="5">{{ @$company_name }}</td>			
			<td colspan="3">성명</td>
			<td colspan="4">{{ @$company_ceo_name }}</td>
			<td colspan="1">(인)</td>
			<td colspan="3">상호</td>
			<td colspan="5">{{ @$storage_nm }}</td>			
			<td colspan="3">성명</td>
			<td colspan="4">{{ @$storage_ceo }}</td>
			<td colspan="1">(인)</td>
		</tr>
		<tr>
			<td colspan="3">주소</td>
			<td colspan="13">{{ @$company_address }}</td>
			<td colspan="3">주소</td>
			<td colspan="13">{{ @$storage_addr }}</td>
		</tr>
		<tr>
			<td colspan="3">업태</td>
			<td colspan="5">{{ @$company_uptae }}</td>
			<td colspan="3">종목</td>
			<td colspan="5">{{ @$company_upjong }}</td>
			<td colspan="3">업태</td>
			<td colspan="5">창고</td>
			<td colspan="3">종목</td>
			<td colspan="5">창고</td>
		</tr>
		<tr>
			<td colspan="3">전화</td>
			<td colspan="5">{{ @$company_office_phone }}</td>
			<td colspan="3">팩스</td>
			<td colspan="5">{{ @$company_fax }}</td>
			<td colspan="3">전화</td>
			<td colspan="5">{{ @$storage_phone }}</td>
			<td colspan="3">팩스</td>
			<td colspan="5">{{ @$storage_fax }}</td>
		</tr>
		<tr>
			<td>No.</td>
			<td colspan="4">품 번</td>
			<td colspan="12">품 명</td>
			<td colspan="2">색 상</td>
			<td colspan="3">사이즈</td>
			<td colspan="3">반품수량</td>
			<td colspan="4">반품금액</td>
			<td colspan="5">반품금액합</td>
		</tr>
	@foreach(range(1, $one_sheet_count) as $i)
		<tr>
			<td>@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ $i }} @endif</td>
			<td colspan="4">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->prd_cd ?? '' }}</td>
			<td colspan="12">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->goods_nm_eng ?? '' }}</td>
			<td colspan="2">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->color ?? '' }}</td>
			<td colspan="3">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->size ?? '' }}</td>
			<td colspan="3">{{ $products[($i - 1) + ($sheet_num * $one_sheet_count)]->return_qty ?? '' }}</td>
			<td colspan="4">@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ number_format($products[($i - 1) + ($sheet_num * $one_sheet_count)]->return_price ?? 0) }} @endif</td>
			<td colspan="5">@if (isset($products[($i - 1) + ($sheet_num * $one_sheet_count)])) {{ number_format($products[($i - 1) + ($sheet_num * $one_sheet_count)]->total_price ?? 0) }} @endif</td>
		</tr>
	@endforeach
		<tr>
			<td colspan="22">합 계</td>
			<td colspan="3">{{ number_format(array_reduce($products, function ($a, $c) { return $a + ($c->return_qty * 1); }, 0)) }}</td>
			<td colspan="4">{{ number_format(array_reduce($products, function ($a, $c) { return $a + ($c->return_price * 1); }, 0)) }}</td>
			<td colspan="5">{{ number_format(array_reduce($products, function ($a, $c) { return $a + ($c->total_price * 1); }, 0)) }}</td>
		</tr>
		<tr>
			<td colspan="5" rowspan="2">입금계좌</td>
			<td colspan="19" rowspan="2">{{ @$company_bank_number }}</td>
			<td colspan="4" rowspan="4">인수자</td>
			<td colspan="4" rowspan="4"></td>
			<td colspan="2" rowspan="4">(인)</td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="5" rowspan="2">물류주소</td>
			<td colspan="11" rowspan="2">{{ @$storage_addr }}</td>
			<td colspan="2" rowspan="2">연락처</td>
			<td colspan="6" rowspan="2">{{ @$storage_phone }}</td>
		</tr>
		<tr></tr>
	</tbody>
</table>
