@extends('partner_with.layouts.layout')
@section('title','뱅크다')
@section('content')

    <div class="page_tit">
        <h3 class="d-inline-flex">뱅크다</h3>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">주문일자</label>
                                <div class="form-inline date-select-inbox">
                                    <select name='date_type' class="form-control form-control-sm" style="width:23%;margin-right:2%;">
                                        <option value=''>사용자</option>
                                        <option value="1">금일</option>
                                        <option value="2">어제</option>
                                        <option value="3">최근1주</option>
                                        <option value="4">최근2주</option>
                                        <option value="5">최근1달</option>
                                        <option value="6">금월</option>
                                        <option value="7">전월</option>
                                    </select>
                                    <div class="docs-datepicker form-inline-inner" style="width:35%;">
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
                                    <span class="text_line" style="width:5%;">~</span>
                                    <div class="docs-datepicker form-inline-inner" style="width:35%;">
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
                                <label for="bkname">입금은행</label>
                                <div class="flax_box">
                                    <select name='bkname' class="form-control form-control-sm" style="width: 86%;">
                                        <option value=''>전체</option>
                                    </select>
                                    <button class="btn btn-sm btn-primary shadow-sm pl-2" onclick="PopAccount();" style="margin-left: 5px; padding-right: 8px;">관리</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="bkjukyo">입금자</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='bkjukyo' id="bkjukyo" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="is_hold">보류여부</label>
                                <div class="flax_box">
                                    <select name="is_hold" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="is_matched">입금확인여부</label>
                                <div class="flax_box">
                                    <select name="is_matched" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="bkinput">입금액</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='bkinput' id="bkinput" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="search-area-ext d-none row align-items-center">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="ord_no">주문번호</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-all" name="ord_no" id="ord_no" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="user_nm">주문자</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-all" name="user_nm" id="user_nm" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="r_nm">수령자</label>
                                <div class="flax_box">
                                    <input type="text" class="form-control form-control-sm search-all" name="r_nm" id="r_nm" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
						<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Pay('order')">입금확인</a>
						<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="Pay('hold');">입금보류</a>
                        <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="SaveMemo();">메모저장</a>
					</div>
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
                width:50
            },
            {
                headerName: '#',
                width:50,
                maxWidth: 100,
                // it is important to have node.id here, so that when the id changes (which happens
                // when the row is loaded) then the cell is refreshed.
                valueGetter: 'node.id',
                cellRenderer: 'loadingRenderer',
            },
            {field:"bkdate", headerName:"입금일자"},
            {field:"bkname", headerName:"은행"},
            {field:"number", headerName:"계좌번호"},
            {field:"bkjukyo", headerName:"입금자명"},
            {field:"bkinput", headerName:"입금액"},
            {field:"bkinfo", headerName:"이체정보"},
            {field:"memo", headerName:"메모"},
            {field:"is_matched", headerName:"입금확인여부"},
            {field:"is_hold", headerName:"입금보류여부"},
            {field:"rt", headerName:"입금내역수집일시"},
            {field:"matched_dt", headerName:"입금확인일시"},
            {field:"ord_no", headerName:"주문번호"},
            {field:"ord_nos", headerName:"복수주문번호"},
            {field:"expect_ord_no", headerName:"예상주문번호"},
            {field:"ord_state", headerName:"주문상태"},
            {field:"pay_type", headerName:"결제방법"},
            {field:"pay_stat", headerName:"입금상태"},
            {field:"ord_amt", headerName:"주문금액"},
            {headerName: "할인금액",
            children:  [{field: "point_amt", headerName: "적립금"},
                        {field: "coupon_amt", headerName: "쿠폰"},
                        {field: "dc_amt", headerName: "할인"}
                    ]},
            {headerName: "주문자정보",
            children:  [{field: "phone", headerName: "연락처"},
                        {field: "mobile", headerName: "핸드폰번호"},
                        {field: "user_nm", headerName: "주문자(아이디)"}
                    ]},
            {field:"r_nm", headerName:"수령자"},
            {field:"sale_price", headerName:"판매처"},
            {field:"admin_name", headerName:"입금확인처리자"},
            {headerName: "", field: "nvl"}
        ];
    </script>

    <script>

        function PopAccount(a) {
            const cd = $(a).attr('data-code');
            let url = '/partner/order/ord14/{code?}';
            if (cd !== '') {
                url = '/partner/order/ord14/' + cd;
            }
            window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=600");
        }

        function Pay(cmd){

            var is_data = 0;
            for(var row = _row_pos; row < gx.getRows(); row++){
                if(gx.Cell(5,row,1) == 1){
                    is_data = 1;
                    break;
                }
            }

            if(is_data == 0){
                alert("입금내역을 선택해 주십시오.");
                return false;
            }

            if(cmd == "hold"){
                ProcessingPopupShowHide("show");
                _row_pos = gx.getFixedRows();
                _proc_pay_cnt = 0;
                PayHold();
            } else if(cmd == "order"){
                if(confirm('입금확인 하시겠습니까?')){
                    ProcessingPopupShowHide("show");
                    _row_pos = gx.getFixedRows();
                    _proc_pay_cnt = 0;
                    PayOrder();
                }
            }
        }

        function SaveMemo(){
        	var data = '';
        	for(var row = gx.getFixedRows(); row < gx.getRows(); row++){
        		no = gx.Cell(0, row, 30);
        		//alert(gx.Cell(20, row, 8));
        		memo = ( gx.Cell(20, row, 8) == "1" ) ? gx.Cell(0,row,8) : "";

        		if( memo != "" ){
        			data += no + "\t" + memo + "\n";
        		}
        	}
        	//alert(data);
        	//return false;
        	if(data != "") {
        		if(confirm('메모를 저장 하시겠습니까?')) {
        			var http = new xmlHttp();
        			var param = "CMD=save_memo";
        			param += "&DATA=" + urlEncode(data);
        			http.onexec('ord14.php','POST',param,true,cbSaveMemo);
        			ProcessingPopupShowHide("show");
        		}
        	} else {
        		alert("메모를 작성해 주십시오.");
        		return false;
        	}
        }

        function cbSaveMemo(res){
        	if( res.responseText == 1 ){
        		alert("메모가 저장되었습니다.");
        	}
        	ProcessingPopupShowHide();
        }

    </script>

    <script type="text/javascript" charset="utf-8">

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;
        $(document).ready(function () {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(300);
            Search();

            $('.search-all').keyup(function(){
                date_use_check();
            });

        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/order/ord14/search', data, 1, searchCallback);
    }

    function searchCallback(data) {}

    </script>

@stop
