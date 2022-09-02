@extends('store_with.layouts.layout-nav')
@section('title','알림 전송')
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
        <!-- FAQ 세부 정보 -->
        <form name="detail">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">수신처 선택</a>
                    </div>
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
                                                <th>구분</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="div_store" id="store_o" class="custom-control-input" value="onceStore"/>
                                                            <label class="custom-control-label" for="store_o">개별매장</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="div_store" id="store_g" class="custom-control-input" value="groupStore"/>
                                                            <label class="custom-control-label" for="store_g">그룹</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름</th>
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
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <a href="#" onclick="openSendMsgPopup()" id="send_msg_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 알림 보내기</a>
                        <a href="#" id="add_group_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 그룹추가</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
        </div>
    </div>
    <script language="javascript">
    var columns = [
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
        { width: 'auto' }
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
 function openSendMsgPopup() {
        const url = '/store/stock/stk32/sendMsg';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=600");
    }
</script>
@stop
