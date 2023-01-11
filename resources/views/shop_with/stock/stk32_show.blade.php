@extends('shop_with.layouts.layout-nav')
@section('title','알림 전송')
@section('content')
    
<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">알림전송</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 알림</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                        <a href="#" id="search_sbtn2" onclick="Search2();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                            <label for="formrow-email-input">구분</label>
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="store" value="O" id="oneStore" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="oneStore">개별매장</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="store" value="G" id="groupStore" class="custom-control-input">
                                        <label class="custom-control-label" for="groupStore">그룹매장</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="store_type">매장구분</label>
								<div class="flex_box">
									<select name='store_type' class="form-control form-control-sm">
										<option value=''>전체</option>
										@foreach ($store_types as $store_type)
											<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group" id="div_store_nm">
                                <label for="store_nm">매장명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-enter" id="store_nm" name='store_nm' value=''>
                                </div>
                            </div>
                            <div class="form-group" id="div_group_nm">
                                <label for="group_nm">그룹명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm" id="group_nm" name='group_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                <a href="#" id="search_sbtn2" onclick="Search2();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            </div>
        </div>
        <div class="show_layout py-0 px-sm-0" id="div_grid">
            <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <a href="#" onclick="openSendMsgPopup()" id="send_msg_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 알림 보내기</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
        <div class="show_layout py-0 px-sm-0" id="div_grid2">
            <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-group-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <a href="#" onclick="openSendMsgPopup()" id="send_msg_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 알림 보내기</a>
                        <a href="#" onclick="openGroupPopup()" id="add_group_btn" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 그룹관리</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd-group" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<script language="javascript">
    
       let columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
            {headerName: "매장코드", field: "store_cd",width:100, cellStyle: {'text-align':'center' }},
            {headerName: "매장명", field: "store_nm",  width:200, cellClass: 'hd-grid-code'},
            {headerName: "연락처", field: "mobile",  width:100, cellClass: 'hd-grid-code'},
            {headerName: "구분", field: "store", hide:true},
            {width: 'auto'}
        ];

        let g_columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
            {headerName: "그룹명", field: "group_nm",width:100, cellStyle: {'text-align':'center' },},
            {headerName: "그룹매장명", field: "group_store_nm",width:700, cellStyle: {'text-align':'left' }},
            {headerName: "그룹코드", field: "group_cd", hide:true},
            {headerName: "구분", field: "store", hide:true},
            {width: 'auto'}
        ];

</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    let gx2;
    const pApp = new App('',{ gridId:"#div-gd" });
    const pApp2 = new App('',{ gridId:"#div-gd-group" });

    $(document).ready(function(){
        $('#div_grid2').hide();
        $('#search_sbtn2').hide();
        $('#div_group_nm').hide();

        $("input[name='store']").change(function() {
            if($("input[name='store']:checked").val() == 'O') {
                $('#div_grid2').hide();
                $('#div_grid').show();
                $('#search_sbtn2').hide();
                $('#search_sbtn').show();
                $('#div_store_nm').show();
                $('#div_group_nm').hide();
            } else if ($("input[name='store']:checked").val() == 'G') {
                $('#div_grid2').show();
                $('#div_grid').hide(); 
                $('#search_sbtn2').show();
                $('#search_sbtn').hide();
                $('#div_store_nm').hide();
                $('#div_group_nm').show();
            }
        });
		
    });

    $(document).ready(function() {
        pApp.ResizeGrid(185);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        gx.gridOptions.defaultColDef = {
            suppressMenu: true,
            resizable: false,
            sortable: true,
        };
        // Search();
    });

    $(document).ready(function() {
        pApp2.ResizeGrid(185);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, g_columns);
        gx2.gridOptions.defaultColDef = {
            suppressMenu: true,
            resizable: false,
            sortable: true,
        };
        // Search2();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/stock/stk32/search-store', data);
    }
    
    function Search2() {
        let data2 = $('form[name="search"]').serialize();
        gx2.Request('/shop/stock/stk32/search-groupstore', data2);
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

        let check_radio = $('input[name=store]:checked').val();

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
        } else if (rows2.length > 1) {  
            alert('그룹알림전송은 1개의 그룹만 선택해주세요')
        } else {
            const url = '/shop/stock/stk32/sendMsg?store_cd=' + sc + '&group_nm=' + sc2 + '&group_cd=' + sc3 + '&check=' + check_radio;
            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
        }
    }

    function openGroupPopup() {
        const url = '/shop/stock/stk32/group_show';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width= 1200,height=900");

    }

    // 그룹매장으로 변경 시 search-enter가 먹히지 않아서 JS로 대체
    var input = document.getElementById("group_nm");

    input.addEventListener("keyup", function (event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        document.getElementById("search_sbtn2").click();
      }
    });

</script>
@stop
