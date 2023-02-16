@extends('head_with.layouts.layout')
@section('title','트래킹 명단 관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">(개)트래킹 명단 관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 트레킹</span>
        <span>/ 트레킹 명단 관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>명단 추가</a>
					<a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>클래식 참여자 결제 URL 생성</a>
                    <a href="#" id="excel_sbtn" onclick="onBtnExportDataAsExcel();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
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
                          <label for="">결제번호 :</label>
                          <div class="flax_box">
                            <input type='text' class="form-control form-control-sm search-all search-enter" name='s_order_no' value=''>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">등록번호 :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_user_code' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">접수상태 :</label>
                            <div class="flax_box">
                                <select name="s_evt_state" class="form-control form-control-sm">
                                    <option value=''>:: 전체 ::</option>
                                    @foreach ($event_state as $event_state_code => $event_state_name)
									<option value='{{ $event_state_code }}'>{{ $event_state_name }}</option>
									@endforeach
                                </select>
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
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">휴대폰(대표) :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='s_mobile' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">성별 :</label>
                            <div class="flax_box">
                                <select name="s_sex" class="form-control form-control-sm">
                                    <option value=''>:: 전체 ::</option>
                                    <option value="M">남성</option>
                                    <option value="F">여성</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">국가 :</label>
                            <div class="flax_box">
                                <select id="s_country" name="s_country" class="form-control form-control-sm" oninput="onQuickFilterChanged()">
                                    <option value=''>:: 전체 ::</option>
									@foreach ($country_info as $country_code => $country_name)
									<option value='{{ $country_code }}'>{{ $country_name }}</option>
									@endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="resul_btn_wrap d-sm-none">
                    <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                </div>
            </div>

        </div>

        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>명단 추가</a>
			<a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>클래식 참여자 결제 URL 생성</a><br><br>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

    </div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
				<div class="fr_box flax_box">
					<div class="mr-1">
						<select id="s1_evt_state" class="form-control form-control-sm">
							<option value="">:: 전체 ::</option>
							@foreach ($event_state as $event_state_code => $event_state_name)
                            <option value='{{ $event_state_code }}'>{{ $event_state_name }}</option>
                            @endforeach
						</select>
					</div>
					<a href="#" onclick="ChangeState()" class="btn-sm btn btn-primary">상태 변경</a>
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
			width:50,
			pinned:'left'
		},
		{
			headerName: '#',
			width:50,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			pinned:'left'
		},
        {headerName: "번호", field: "evt_idx",width:50},
        {headerName: "이벤트명", field: "title",width:200},
        {headerName: "결제번호", field: "order_no", cellStyle:StyleOrderNo, width:180,
            cellRenderer: function(params) {
				return '<a href="#" onClick="Popcls13(\''+ params.value +'\')">'+ params.value+'</a>'
            }
        },
        {headerName: "접수상태", field: "evt_state_nm", cellStyle:chgStateStyle, width:90},
        {headerName: "접수방법", field: "kind", width:80},
        {headerName: "아이디", field: "user_id", cellStyle:chgUseridStyle, width:120},
        {headerName: "등록번호", field: "user_code", width:80, cellStyle:{"text-align":"center"},
            cellRenderer: function(params) {
				return '<a href="#" onClick="PopUpdcls13(\''+ params.data.order_no +'\',\''+ params.value +'\')">'+ params.value+'</a>'
            }
        },
        {headerName: "접수자명", field: "user_nm", width:100},
        {headerName: "접수자명(영문)", field: "en_nm", width:120},
        {headerName: "연령", field: "ckind", width:50},
        {headerName: "휴대폰번호", field: "mobile", width:120},
        {headerName: "이메일", field: "email", width:150},
        {headerName: "성별", field: "sex", width:50},
        {headerName: "국가", field: "country", width:100},
        {headerName: "생년월일", field: "birthdate", width:80},
        {headerName: "긴급연락처", field: "em_phone", width:120},
        {headerName: "출발그룹", field: "group_nm", width:60},
        {headerName: "주소", field: "addr", width:180},
        {headerName: "채식여부", field: "dietary_yn", width:60},
        {headerName: "등록일", field: "regdate", width:150},
        {headerName: "evt_mem_idx", field: "evt_mem_idx", hidden:true},
    ];

    function Popcls13(item)
    {
        const url='/head/classic/cls03/show/' + item;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

	function PopUpdcls13(item1, item2)
	{
        const url='/head/classic/cls03/show/' + item1 + '/' + item2;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=730");
	}

	function chgStateStyle(params)
    {
		var font_color = "";

        if(params.value !== undefined){
			var font_color = "#0000";
			switch(params.data.evt_state_nm){
				case "입금예정":
					font_color = "#939DAA"; break;
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

	function chgUseridStyle(params)
    {
		return {
			'background-color': '#FDE9E8',
		}
    }

	// 그룹 셀 컬러 지정 시작
	var _styleOrdNoCnt		= 0;
	var _styleColorIndex	= -1;
	function StyleOrderNo(params)
	{
		if( params.value !== undefined )
		{
			var colors	= {
				0:"#ffff00",
				1:"#C5FF9D",
			}
			var rowIndex	= params.node.rowIndex;
			if( rowIndex > 0 && params.data.ord_no_bg_color === undefined )
			{
				var rowNode	= params.api.getDisplayedRowAtIndex(rowIndex-1);
				if( params.value == rowNode.data.order_no )
				{
					_styleColorIndex	= _styleOrdNoCnt % 2;
					params.data['ord_no_bg_color']	= colors[_styleColorIndex];
					rowNode.data['ord_no_bg_color']	= colors[_styleColorIndex];
				}
				else
				{
					if( _styleColorIndex >= 0 )
					{
						_styleOrdNoCnt++;
						_styleColorIndex	= -1;
					}
				}
			}
			if( params.data.ord_no_bg_color !== undefined || params.data.ord_no_bg_color != '' )
			{
				return {
					'background-color': params.data.ord_no_bg_color
				}
			}
		}
	}

	// 그룹 쉘 컬러 지정 끝
	function ChangeState()
	{
		var checkRows = gx.gridOptions.api.getSelectedRows();
		if( checkRows.length === 0 )
		{
            alert("접수상태를 변경할 명단을 선택해주세요.");
            return;
		}

		var s1_evt_state = $("#s1_evt_state").val();
		if( s1_evt_state == "" )
		{
            alert("접수상태를 선택해주세요.");
            return;
		}

		if(confirm("선택하신 명단의 접수상태를 변경하시겠습니까?"))
		{
			var evt_mem_idxs = checkRows.map(function(row)
			{
				return row.evt_mem_idx;
			});

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/classic/cls03',
				data: {
					"evt_mem_idxs[]" : evt_mem_idxs,
					s1_evt_state : s1_evt_state
				},
				success: function (data) {
					if( data.code == "200" )
					{
						alert("접수상태를 변경하였습니다.");
					}
					else
					{
						alert("접수상태 변경을 실패하였습니다.");
					}
					Search();
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
		}
	}
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/classic/cls03/search', data, 1);
    }

    var option_key = {};
    function getParams() {
        return {
            columnKeys: option_key,
            skipHeader: false,
            skipPinnedTop: false,
        };
    }

    function onBtnExportDataAsExcel() {
        var params = getParams();
        gx.gridOptions.api.exportDataAsExcel(params);
    }

    function onQuickFilterChanged() {
        gx.gridOptions.api.setQuickFilter($('#s_country').val());
    }
</script>
@stop
