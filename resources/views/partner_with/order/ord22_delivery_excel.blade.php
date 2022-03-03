<meta charset="utf-8" />
<table border="1">
<thead>
	<tr>
		<th style="background:yellow">스타일넘버</th>
		<th style="background:yellow">브랜드</th>
		<th style="background:yellow">업체</th>
		<th style="background:yellow">위치</th>
		<th style="background:yellow">상품명</th>
		<th style="background:yellow">옵션</th>
		<th style="background:yellow">주문수</th>
		<th style="background:yellow">주문수량</th>
		<th style="background:yellow">온라인수량</th>
		<th style="background:yellow">재고수량</th>
		<th style="background:yellow">실제수량</th>
	</tr>
</thead>
<tbody>
<?php foreach($rows as $row) { ?>
	<tr>
		<td><?=$row->style_no;?></td>
		<td><?=$row->brand;?></td>
		<td><?=$row->com_nm;?></td>
		<td><?=$row->goods_location;?></td>
		<td><?=$row->goods_nm;?></td>
		<td><?=$row->goods_opt;?></td>
		<td><?=$row->ord_cnt;?></td>
		<td><?=$row->sale_qty;?></td>
		<td><?=$row->qty;?></td>
		<td><?=$row->wqty;?></td>
		<td><?=$row->rqty;?></td>
	</tr>
<?php } ?>
</tbody>
</table>
