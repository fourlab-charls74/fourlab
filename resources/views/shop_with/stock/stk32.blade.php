@extends('shop_with.layouts.layout')
@section('title','알림')
@section('content')


<div class="page_tit">
    <h3 class="d-inline-flex">{{ @$cmd == 'send' ? '보낸' : '받은' }} 알림 보관함</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 매장관리</span>
        <span>/ 알림</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    
                    <a href="#" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
                    <a href="#" onclick="openPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"> 알림전송</a>
                    @if(@$cmd == 'receive')
                        <a href="/shop/stock/stk32?is_send_msg=true" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"> 보낸알림 보관함</a>
                    @elseif(@$cmd == 'send')
                        <a href="/shop/stock/stk32" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"> 받은알림 보관함</a>
                    @endif
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">{{ @$cmd == 'send' ? '보낸' : '받은' }} 날짜</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">{{ @$cmd == 'send' ? '수신' : '발신' }}처</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='sender' value=''>
                          </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                        <label for="">내용</label>
                        <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='content' value=''>
                        </div>
                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="rt">등록일</option>
                                            <option value="content">내용</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                        <input type="radio" name="ord" id="sort_asc" value="asc">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/shop/stock/stk31/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                @if(@$cmd == 'send')
                    <a href="#" id="msg_del_btn" onclick="msgDel()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 삭제</a>
                @elseif(@$cmd == 'receive')
                    <a href="#" id="msg_read_btn" onclick="msgRead()" class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;"> 읽음</a>
                @endif
                <div class="fr_box">

                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">

    let columns = [];
    if('{{ @$cmd }}' == 'send') {

        columns = [
            {
                headerName: '',
                checkboxSelection: (params) => {
                    const check_yn = params.data.check_yn;
                    return check_yn != 'Y'? true : false;
                },
                width:28,
                pinned:'left'
            },
            {field: "receiver_cd", hide: true},
            {headerName: "수신처", field: "receiver_nm", width:200,
                cellRenderer: (params) => 
                        params.data.receiver_nm
            },
            {headerName: "내용", field: "content", width:300, cellStyle: {'text-overflow': 'ellipsis'},
                cellRenderer: params => {
                    return "<a href='#' id='contentArea' onclick='showContent(" + params.data.msg_cd +")'>"+params.value+"</a>";
                },
            },
            {headerName: "보낸 날짜", field: "rt", width:120},
            {headerName: "확인여부", field: "check_yn", width: 70, cellClass: 'hd-grid-code',
                cellStyle: (params) => ({color: params.data.check_yn == 'Y' ? 'blue' : 'red'})
            },
            {headerName: "확인날짜", field: "check_date", width:130, cellClass: 'hd-grid-code'},    
            {headerName: "알림 번호", field: "msg_cd", hide: true},    
            {width: 'auto'}
        ];                              
    } else {
    
        columns = [
            {
                headerName: '',
                checkboxSelection: (params) => {
                    const check_yn = params.data.check_yn;
                    return check_yn != 'Y'? true : false;
                },
                width:28,
                pinned:'left'
            },
            {field: "sender_cd", hide: true},
            {headerName: "발신처", field: "sender_nm", width:150},
            {headerName: "연락처", field: "mobile", width: 80, cellClass: 'hd-grid-code'},
            {headerName: "내용", field: "content", width: 300, cellStyle: {'text-overflow': 'ellipsis'},
                cellRenderer: params => {
                    return "<a href='#' onclick='showContent(" + params.data.msg_cd +")'>"+params.value+"</a>";
                },
            },
            {headerName: "받은 날짜", field: "rt", width: 110, cellClass: 'hd-grid-code'},
            {headerName: "확인여부", field: "check_yn", width: 110, cellClass: 'hd-grid-code',
                cellStyle: (params) => ({color: params.data.check_yn == 'Y' ? 'blue' : 'red'})
            },
            {headerName: "알림 번호", field: "msg_cd", hide: true},        
            {width: 'auto'}
        ];                              
    }

</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        data += "&msg_type=" + "{{ @$cmd }}";
        gx.Request('/shop/stock/stk32/search', data);
    }

    const initSearchInputs = () => {
        document.search.reset(); // 모든 일반 input 초기화
        $('#store_no').val(null).trigger('change'); // 브랜드 select2 박스 초기화
    };

    function openPopup() {
        const url = '/shop/stock/stk32/create';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1100,height=700");
    }

    function showContent(msg_cd) {
        const url = '/shop/stock/stk32/showContent?msg_type=' + '{{@$cmd}}&msg_cd=' + msg_cd;
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
    }

    function msgRead() {
        const rows = gx.getSelectedRows();

        let msg_cd = "";
        
        for (let i=0; i<rows.length; i++) {
            msg_cd += rows[i].msg_cd + ',';
        }

        msg_cd = msg_cd.replace(/,\s*$/, "");
        let msg_cds = msg_cd.split(',');

        if (rows.length == 0) return alert('적어도 하나 이상 선택해주세요.');
 
        $.ajax({
            method: 'put',
            url: '/shop/stock/stk32/msg_read',
            data: {msg_cd : msg_cds},
            dataType : 'json',
            success: function(data) {
                if (data.code == '200') {
                    alert('선택한 알림이 읽음 처리 되었습니다.');
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
                Search();
            },
            error: function(e) {
                    // console.log(e.responseText)
            }
        });

    }

    function msgDel() {
        const rows = gx.getSelectedRows();

        let msg_cd = "";
        
        for (let i=0; i<rows.length; i++) {
            msg_cd += rows[i].msg_cd + ',';
        }

        msg_cd = msg_cd.replace(/,\s*$/, "");
        let msg_cds = msg_cd.split(',');

        if (rows.length == 0) return alert('적어도 하나 이상 선택해주세요.');

        if(confirm("삭제하시겠습니까?")) {
            $.ajax({
                method: 'post',
                url: '/shop/stock/stk32/msg_del',
                data: {msg_cd : msg_cds},
                dataType : 'json',
                success: function(data) {
                    if (data.code == 200) {
                        alert('선택한 알림이 삭제 처리 되었습니다.');
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(e) {
                        // console.log(e.responseText)
                }
            });
        }
    }

</script>



@stop
