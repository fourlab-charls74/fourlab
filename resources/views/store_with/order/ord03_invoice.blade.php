@extends('store_with.layouts.layout-nav')
@section('title', '택배송장목록 받기')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">택배송장목록 받기</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 주문/배송관리</span>
                <span>/ 온라인 배송처리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0);" onclick="return exportDlvInvoiceList();" class="btn btn-primary mr-1"><i class="bx bx-download fs-16 mr-1"></i> 엑셀다운로드</a>
            <a href="javascript:void(0);" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-body" style="border:none;">
                <div class="row">
                    <div class="col-lg-5">
                      <div class="table-responsive">
                          <table class="table table-bordered th_border_none">
                              <thead>
                                <tr>
                                    <th>전체컬럼</th>
                                </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td>
                                        <select multiple class="form-control" id="columns" style="height:500px">
                                          @foreach ($columns as $column)
                                            <option value="{{$column->name}}">{{$column->value}}</option>
                                          @endforeach
                                        </select>
                                      </td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                    </div>
                    <div class="col-lg-1 colmove_btn_wrap">
                        <div class="colmove_btn">
                          <a href="#" class="col btn btn-sm btn-outline-primary shadow-sm" id="addField" style="max-width:110px;">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z"/>
                            </svg>
                          </a>
                          <a href="#" class="col btn btn-sm btn-outline-primary shadow-sm align-self-start" id="deleteField" style="max-width:110px;">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                            </svg>
                          </a>
                        </div>
                    </div>
                    <div class="col-lg-5">
                      <div class="table-responsive">
                          <table class="table table-bordered th_border_none">
                              <thead>
                                <tr>
                                    <th>선택컬럼</th>
                                </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td>
                                        <select multiple class="form-control" id="fields" style="height:500px">
                                          @foreach ($fields as $field)
                                            <option value="{{$field->name}}">{{$field->value}}</option>
                                          @endforeach
                                        </select>
                                      </td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                    </div>
                    <div class="col-lg-1 colmove_btn_wrap">
                        <div class="colmove_btn">
                          <a href="#" class="col btn btn-sm btn-outline-primary shadow-sm" id="upField" style="max-width:110px;">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
                            </svg>
                          </a>
                          <a href="#" class="col btn btn-sm btn-outline-primary shadow-sm" id="downField" style="max-width:110px;">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-down-short" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/>
                            </svg>
                          </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">

    // 컬럼에 선택된 컬럼 추가 ->
    $("#addField").click(function(e) {
        e.preventDefault();

        $("#columns > option:selected").each(function(idx) {
            const searchThis = $("#fields option[value=" + this.value + "]");
            if (searchThis.length > 0) return;
            $("#fields").append("<option value='" + this.value + "'>" + this.innerHTML + "</option>");
        });
    });

    // 컬럼에서 선택된 컬럼 제거 <-
    $("#deleteField").click(function(e) {
        e.preventDefault();

        $("#fields > option:selected").each(function(idx) {
            $(this).remove();
        });
    });

    $("#upField").click(function(e) {
        e.preventDefault();

        const options = $("#fields > option:selected");
        if (options.length === 0) return;

        options.each(function() {
            const idx = this.index;
            const addHtml = "<option value='" + this.value + "' selected>" + this.innerHTML + "</option>";
            $("#fields > option:nth-child(" + (idx) + ")").before(addHtml);
            $(this).remove();
        });
    });

    $("#downField").click(function(e){
        e.preventDefault();

        const options = $("#fields > option:selected");
        if (options.length === 0) return;

        for (let i = options.length -1; i >= 0; i--) {
            const target = $("#fields > option:selected")[i];
            const idx = target.index + 2;
            const addHtml = "<option value='" + target.value + "' selected>" + target.innerHTML + "</option>";

            $("#fields > option:nth-child(" + (idx) + ")").after(addHtml);
            target.remove();
        }
    });

    function exportDlvInvoiceList() {
        const search_date = opener.getFormSerializedData();
        const fields = [];
        $('#fields > option').each(function() {
            fields.push(this.value);
        });
        location.href = "/store/order/ord03/download/invoice-list?" + search_date + "&fields=" + fields.join(",");
    }
</script>
@stop
