@extends('head_with.layouts.layout')
@section('title','트래킹 결제내역 관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">트레킹 결제내역 관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 트레킹</span>
        <span>/ 트레킹 결제내역 관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">

            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일 :</label>
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
                            <label for="type">이벤트명/번호 :</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:65%;">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='s_title' value=''>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:29%;">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='s_evt_idx' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">신청번호 :</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='s_order_no' value=''>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">접수상태/결제상태 :</label>
                            <div class="form-inline inline_input_box">
                                <select name="s_evt_state" class="form-control form-control-sm" style="width:60%;">
                                    <option value=''>전체</option>
                                    <option value="1">입금예정</option>
                                    <option value="5">접수후보</option>
                                    <option value="9">후보결제대기</option>
                                    <option value="10">접수완료</option>
                                    <option value="20">확정대기</option>
                                    <option value="30">확정완료</option>
                                    <option value="-10">결제오류</option>
                                    <option value="-20">신청취소</option>
                                </select>
                                <div class="px-2">/</div>
                                <select name='s_pay_state' class="form-control form-control-sm" style="width:20%;">
                                    <option value=''>전체</option>
                                    <option value="1">입금</option>
                                    <option value="0">미입금</option>
                                </select>

                                <div style="height:30px;margin-left:2px;">
                                    <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="오류 및 취소 제외">
                                        <input type="checkbox" class="custom-control-input" id="s_pay_ok" name="s_pay_ok" switch="primary" value="Y">
                                        <label for="s_pay_ok" data-on-label="Yes" data-off-label="No" style="margin-top:2px;"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">접수자명(대표) :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_user_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">휴대폰(대표) :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_mobile' value=''>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="resul_btn_wrap d-sm-none">
                    <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
  <div class="card-body">
    <div class="card-title">
        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
    </div>
    <div class="table-responsive">
        <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
    </div>
  </div>
</div>
<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",type:'NumType'},
        {headerName: "이벤트번호", field: "evt_idx",width:90},
        {headerName: "이벤트명", field: "title",width:300},
        {headerName: "신청번호", field: "order_no", width:180,
            cellRenderer: function(params) {
				return '<a href="#" onClick="PopPrm13(\''+ params.value +'\')">'+ params.value+'</a>'
            }
        },
        {headerName: "접수상태", field: "evt_state_nm", cellStyle:chgStateStyle, width:90},
        {headerName: "결제상태", field: "pay_stat", width:80},
        {headerName: "접수방법", field: "kind", width:80},
        {headerName: "결제금액", field: "amount", type: 'currencyType', width:100},
        {headerName: "결제자명", field: "buyr_name", width:100},
        {headerName: "휴대폰번호", field: "mobile", width:120},
        {headerName: "등록일", field: "regdate", width:150},
    ];

    function PopPrm13(item)
    {
        const url='/head/promotion/prm13/show/' + item;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

	function chgStateStyle(params)
    {
		var font_color = "";

        if(params.value !== undefined){
			var font_color = "#0000";
			switch(params.data.evt_state_nm){
				case "접수후보":
					font_color = "#3E9900"; break;
                case "접수완료":
					font_color = "#1F4C00"; break;
                case "확정대기":
					font_color = "#4C4CFF"; break;
                case "확정완료":
					font_color = "#0000FF"; break;
                case "결제오류":
					font_color = "#FF0000"; break;
                case "신청취소":
					font_color = "#FF0000"; break;
			}

			return {
				'color': font_color,
				'font-weight' : '400'
			}
			
		}
    }
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(250);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm12/search', data);
    }

</script>
@stop
