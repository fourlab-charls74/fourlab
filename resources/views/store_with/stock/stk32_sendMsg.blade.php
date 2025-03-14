@extends('store_with.layouts.layout-nav')
@section('title','전송')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">알림</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 알림전송</span>
                </div>
            </div>
            <div>
                <button type="button" onclick="window.close()" id="close_btn" class="btn btn-sm btn-outline-primary shadow-sm mr-1" style="float:right;">닫기</button>
                <button type="button" onclick="Send()" id="sendMsg_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">전송</button>
            </div>
        </div>

        <form name="store">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0" style="display:inline-block">
                        <a>받는 사람</a>
                    </div>
                    <div style="display:inline-block;"></div>
                    <div class="card-body mt-1">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="94px">
                                            </colgroup>
                                            <tbody>
                                                <tr>
                                                    <th>받는 사람</th>
                                                    <td>
                                                        <div class="flax_box" name="sd" id="sd">
                                                            @if($store_cds != "")
                                                                @foreach($stores as $store)
                                                                    <span>{{$store->store_nm}}@if (!$loop->last),&nbsp; @endif</span>
                                                                @endforeach
                                                            @elseif ($group_cds != "")
                                                                @foreach($groupName as $gp)
                                                                    <span>{{$gp->group_nm}}@if (!$loop->last),&nbsp; @endif</span>
                                                                @endforeach
															@else
																@foreach($ids as $id)
																	<span>{{$id->name}}@if (!$loop->last),&nbsp; @endif</span>
																@endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        
            <div class="show_layout py-3 px-sm-0">
                <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
                    <div class="card-title">
                        <div class="filter_wrap">
                            <div class="fl_box">
                                <h6 class="m-0 font-weight-bold">내용</h6>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="content" name="content" rows="10" style="margin:auto;resize: none;"></textarea>
                        <div id="test_cnt" style="text-align:right;">(0 / 20000)</div>
                    </div>
                    <br>
                    <div>
                        <div style="font-size: 14px;display:inline-block">
                            <input type="checkbox" id="reservation_msg" name="reservation_msg" value="">&nbsp;
                            <label for="reservation_msg">예약발송</label>
                        
                        </div>
                        
                        <div id="res_date" style="float:right;display:none">
                            <div style="width:120px;display:inline-block;">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="rm_date" value="{{$edate}}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                            
                            <div style="width:100px;display:inline-block">
                                <select name="rm_hour" id="rm_hour" class="form-control form-control-sm">
                                @for( $i=0; $i <= 23;$i++)
                                    @if($i < 10)
                                        <option value="0{{$i}}">0{{$i}}시</option>
                                    @else
                                        <option value="{{$i}}">{{$i}}시</option>
                                    @endif
                                @endfor
                                </select>
                            </div>
                            
                            <div style="width:100px;display:inline-block;">
                                <select name="rm_min" id="rm_min" class="form-control form-control-sm">
                                @for($i=00; $i <= 59; $i++)
                                    @if($i < 10)
                                    <option value="0{{$i}}">0{{$i}}분</option>
                                    @else
                                    <option value="{{$i}}">{{$i}}분</option>
                                    @endif
                                @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

<script>
    $(document).ready(function(){
        $("#reservation_msg").change(function() {
            if($("#reservation_msg").is(":checked")) {
                $('#res_date').show();
            } else {
                $('#res_date').hide();
            }
        });
    });

</script>

<script>
    function Send() {

        let frm = $('form[name=store]').serialize();
        let ds = "";
        let check = document.querySelector('input[name="reservation_msg"]').checked;
    
        if ($('#content').val() === '') {
            $('#content').focus();
            alert('내용을 입력해 주세요.');
            return false;
        }
        
        frm += "&store_cds=" + "{{ @$store_cds }}";
        frm += "&group_cds=" + "{{ @$group_cds }}";
		frm += "&user_ids=" + "{{ @$user_ids }}";
        frm += "&check=" + "{{ @$check }}";
        frm += "&reservation_msg=" + $('[name=reservation_msg]').is(":checked");

        $.ajax({
            method: 'post',
            url: '/store/stock/stk32/store',
            data: frm,
            dataType: 'json',
            success: function(data) {
                if (data.code == 200) {
                    alert(data.msg);
                    window.close();
                    opener.close();
                    opener.opener.Search();
                } else if (data.code == 100) {
                    alert(data.msg);
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                    console.log(e.responseText)
            }
        });
    }
    
</script>

<script>
    $(document).ready(function() {
        $('#content').on('keyup', function() {
            $('#test_cnt').html("("+$(this).val().length+" / 20000)");
 
            if($(this).val().length > 20000) {
                $(this).val($(this).val().substring(0, 20000));
                $('#test_cnt').html("(20000 / 20000)");
            }
        });
    });
</script>
@stop
