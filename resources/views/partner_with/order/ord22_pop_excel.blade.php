<meta charset="utf-8" />
<table border="1" x:str>
	<thead>
		<tr>
			@foreach ($fields as $field)
			<th style="background:yellow">{{$field->value}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
	@php
		$ord_index1		= 0;
		$ord_index2		= 0;
		$ord_pvalue1	= "";
		$ord_pvalue2	= "";
		$colors			= array("#5BFF5B","#FFA980");
    @endphp
    @foreach ($rows as $row)
		<tr>
		@foreach ($fields as $field)
        @php
            $style1	= "";
            $style2	= "";

			//주문 묶음 단위 표시
			if( $row["ord_cnt"] > 1 && $field->name == "ord_no" )
			{
				if( $row->ord_no != $ord_pvalue1 )
				{
					$ord_index1		= 1 - $ord_index1;
					$ord_pvalue1	= $row["ord_no"];
				}
				$color	= $colors[$ord_index1];
				$style1	= "mso-pattern:auto none;background:$color;";
			}

			//주문 묶음 단위 표시
			if( $row["sale_qty"] > 1 && $field->name == "sale_qty" )
			{
				if( $row["ord_no"] != $ord_pvalue2 )
				{
					$ord_index2		= 1 - $ord_index2;
					$ord_pvalue2	= $row["ord_no"];
				}
				$color	= $colors[$ord_index2];
				$style2	= "mso-pattern:auto none;background:$color;";
			}
        @endphp
			<td style="mso-number-format:'\@';white-space:nowrap;{{ @$style1 }}{{ @$style2 }}">
                @if($field->name === 'goods_no_name')
                    [{{$row["goods_no"]}}] {{$row["goods_nm"]}}
                @else
                    {{$row[$field->name]}}
                @endif
            </td>
		@endforeach
		</tr>
@endforeach
	</tbody>
</table>
