<?php
  function get_style($id) {
    $text = "mso-number-format:'\@';";
    $number = "mso-number-format:'\#\,\#'; text-align:right";
    if ($id === 'user_id' || $id === 'jumin' ) {
      return $text;
    }

    if ($id === 'ord_amt' || $id === 'point' || $id == 'visit_cnt' ) {
      return $number;
    }
  }
?>
<meta charset="utf-8" />
<table border="1">
  <thead>
    <tr>
      @foreach ($fields as $field)
        <th style="background:yellow">{{$field['name']}}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    <?php foreach($rows as $row) { ?>
      <tr>
        @foreach ($fields as $field)
          <td style="{{get_style($field['value'])}}">
                <strong>{{ $row->{$field['value']} }}</strong>
          </td>
        @endforeach
      </tr>
    <?php } ?>
  </tbody>
</table>
<!-- #ffa980 -->
<style>
  table .id {} 
</style>