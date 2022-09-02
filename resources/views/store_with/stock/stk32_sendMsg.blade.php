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
        </div>
        <form name="sendMsg">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0" style="display:inline-block">
                        <a>수신처</a>
                        <button id="sendMsg_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 전송</button>
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
                                                    <th>수신처</th>
                                                    <td>
                                                        <div class="flax_box">
                                                            <input type='text' class="form-control form-control-sm search-enter" name='store_nm' id="store_nm" >
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
        </form>
    </div>

    <div class="show_layout py-3 px-sm-3">
        <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">내용</h6>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="10"></textarea>
            </div>
            <br>
            <div>
                <div style="font-size: 14px;display:inline-block">
                    <input type="checkbox" id="reservation_msg" value="rm">&nbsp;
                    <label for="reservation_msg">예약발송</label>
                </div>

                <div id="res_date" style="float:right;display:none">
                    <div style="width:120px;display:inline-block;">
                        <div class="docs-datepicker form-inline-inner input_box">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{$edate}}" autocomplete="off" disable>
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
                        <select name="hour" id="hour" class="form-control form-control-sm">
                            @for( $i=1; $i <= 12;$i++)
                                <option value="{{$i}}">{{$i}}시</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div style="width:100px;display:inline-block;">
                        <select name="minite" id="minute" class="form-control form-control-sm">
                            @for($i=00; $i <= 59; $i++)
                                @if($i < 10)
                                    <option value="{{$i}}">0{{$i}}분</option>
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
    
<script language="javascript">
    let columns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28,
            pinned:'left'
        },
        {headerName: "매장코드", field: "sender_type",width:150},
        {headerName: "매장명", field: "sender_cd",  width:150, cellClass: 'hd-grid-code'},
        {headerName: "연락처", field: "content",  width:150, cellClass: 'hd-grid-code'},
        {headerName: "그룹명", field: "rt", width: 150, cellClass: 'hd-grid-code'},
        {width: 'auto'}
    ];                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      

</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.BindSearchEnter();
        // Search();
    });

    // function Search() {
    //     let data = $('form[name="search"]').serialize();
    //     gx.Request('/store/stock/stk32/search', data);
    // }

</script>

<script>
    $(document).ready(function(){
        $("#reservation_msg").change(function(){
            if($("#reservation_msg").is(":checked")){
                $('#res_date').show();
            }else{
                $('#res_date').hide();
            }
        });
    });
</script>

@stop
