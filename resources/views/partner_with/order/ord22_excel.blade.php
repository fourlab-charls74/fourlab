<meta charset="utf-8" />
<table border="1">
  <thead>
    <tr>
      @foreach ($headers as $header)
        <th style="background:yellow">
          {{$header}}
        </th>
      @endforeach
    </tr>
  </thead>
  <tbody>
      @foreach ($rows as $row)
      <tr>
        @foreach ($fields as $field)
          <td>
            {{ @$row->{$field} }}
          </td>
        @endforeach
      </tr>
      @endforeach
  </tbody>
</table>
