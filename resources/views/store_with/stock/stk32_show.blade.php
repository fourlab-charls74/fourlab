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
        <form name="search">
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
                                                            <input type="radio" name="div_store" id="store_o" class="custom-control-input" value="onceStore" checked/>
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
                                                <th>매장명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='store_nm' id="store_nm" >
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <div style="text-align:center;margin-top:7px;margin-bottom:-14px;">
                                            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 매장조회</a>
                                            <a href="#" id="search_sbtn2" onclick="Search2();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 그룹조회</a>
                                        </div>                                    
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
                    <a href="#" onclick="openGroupPopup()" id="add_group_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 그룹관리</a>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                <div id="div-gd2" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    

<script language="javascript">
    $(document).ready(function(){
        $('#div-gd2').hide();
        $('#search_sbtn2').hide();

        $("input[name='div_store']").change(function() {
            if($("input[name='div_store']:checked").val() == 'onceStore') {
                $('#div-gd2').hide();
                $('#div-gd').show();
                $('#search_sbtn2').hide();
                $('#search_sbtn').show();
                Search();
            } else if ($("input[name='div_store']:checked").val() == 'groupStore') {
                $('#div-gd2').show();
                $('#div-gd').hide();
                $('#search_sbtn2').show();
                $('#search_sbtn').hide();
                Search2();
            }
        });
		
    });

    let columns = [
        {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
        {headerName: "매장코드", field: "store_cd",width:100, cellStyle: {'text-align':'center' }},
        {headerName: "매장명", field: "store_nm",  width:200, cellClass: 'hd-grid-code'},
        {headerName: "연락처", field: "mobile",  width:100, cellClass: 'hd-grid-code'},
        // {headerName: "그룹명", field: "store_group", width: 150, cellClass: 'hd-grid-code'},
        {width: 'auto'}
    ];


    let g_columns = [
        {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
        {headerName: "그룹명", field: "group_nm",width:100, cellStyle: {'text-align':'center' }},
        {headerName: "그룹매장명", field: "group_store_nm",width:300, cellStyle: {'text-align':'center' }},
        {headerName: "그룹코드", field: "group_cd", hide:true},
        {width: 'auto'}
    ];     
        
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    let gx2;
    const pApp = new App('',{ gridId:"#div-gd" });
    const pApp2 = new App('',{ gridId:"#div-gd2" });

    $(document).ready(function() {
        pApp.ResizeGrid(420);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        gx.gridOptions.defaultColDef = {
            suppressMenu: true,
            resizable: false,
            sortable: true,
        };
        Search();
    });

    $(document).ready(function() {
        pApp2.ResizeGrid(420);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, g_columns);
        gx2.gridOptions.defaultColDef = {
            suppressMenu: true,
            resizable: false,
            sortable: true,
        };
        Search2();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/stock/stk32/search-receiver', data);
    }
    
    function Search2() {
        let data2 = $('form[name="search"]').serialize();
        gx2.Request('/store/stock/stk32/search-receiver', data2);
    }

</script>

<script>
    function openSendMsgPopup() {
        const rows = gx.getSelectedRows();
        let store_cd = "";

        const rows2 = gx2.getSelectedRows();
        let group_nm = "";
        
        const rows3 = gx2.getSelectedRows();
        let group_cd = "";

        let check_radio = $('input[name=div_store]:checked').val();

        for (let i=0; i<rows.length; i++) {
            store_cd += rows[i].store_cd + ',';
        }
        const sc = store_cd.replace(/,\s*$/, "");
        
        for (let i=0; i<rows2.length; i++) {
            group_nm += rows2[i].group_nm + ',';
        }
        const sc2 = group_nm.replace(/,\s*$/, "");

        for (let i=0; i<rows3.length; i++) {
            group_cd += rows2[i].group_cd + ',';
        }
        const sc3 = group_cd.replace(/,\s*$/, "");

        if(rows.length < 1 && rows2.length < 1) {
            alert('적어도 한 개 이상의 매장을 선택해주세요');
        } else {
            const url = '/store/stock/stk32/sendMsg?store_cd=' + sc + '&group_nm=' + sc2 + '&group_cd=' + sc3 + '&check=' + check_radio;
            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
        }
    }

    function openGroupPopup() {
        const url = '/store/stock/stk32/group';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=500");

    }

</script>
@stop
