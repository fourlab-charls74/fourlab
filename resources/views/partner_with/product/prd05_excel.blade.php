<table border="1">
  <thead>
    <tr>
      @foreach ($headers as $header)
        @if ($header === "상품번호")
          <th style="background:yellow" colspan="2">{{$header}}</th>
        @else
          <th style="background:yellow">{{$header}}</th>
        @endif
      @endforeach
    </tr>
  </thead>
  <tbody>
      @foreach ($rows as $row)
      <tr>
        @foreach ($fields as $field)
        
          <td>
            {{ $row->{$field} }}
          </td>
        @endforeach
      </tr>
      @endforeach
  </tbody>
</table>