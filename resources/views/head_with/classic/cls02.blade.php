@extends('head_with.layouts.layout')
@section('title','클래식 숙소예약관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">(개)클래식 숙소예약관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 클래식</span>
        <span>/ 숙소예약관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0 search_mode_wrap">
                        <button type="button" class="btn btn-sm btn-outline-primary pr-1 waves-light waves-effect dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i id="" class="search-btn-label fa fa-square fs-12"></i> <i class="bx bx-chevron-down fs-12"></i>
                        </button>
                        <div class="dropdown-menu" style="min-width:0">
                            <a id="search-btn-minus" class="dropdown-item" data-search-type="minus" href="#"><i class="fa fa-minus-square"></i></a>
                            <a id="search-btn" class="dropdown-item" href="#" data-search-type="default"><i class="fa fa-square"></i></a>
                            <a id="search-btn-plus" class="dropdown-item" href="#" data-search-type="plus"><i class="fa fa-plus-square"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
				<div class="row">
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
                            <label for="regist_number">등록번호</label>
                            <div class="flax_box">
								<input type="text" class="form-control form-control-sm search-all search-enter" name="regist_number" value="">
                            </div>
                        </div>
					</div>
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
                            <label for="mobile">모바일</label>
                            <div class="flax_box">
								<input type="text" class="form-control form-control-sm search-all search-enter" name="mobile" value="">
                            </div>
                        </div>
					</div>
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="email">이메일</label>
                            <div class="flax_box">
								<input type="text" class="form-control form-control-sm search-all search-enter" name="email" value="">
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name1">이름</label>
                            <div class="flax_box">
								<input type="text" class="form-control form-control-sm search-all search-enter" name="name1" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="state">접수상태</label>
                            <div class="flax_box">
                                <select name="state" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->code }}">{{ $state->value1 }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>전/후 예약일</label>
                            <div class="flax_box">
                                <select name="s_dm_date" class="form-control form-control-sm" style="width:49%;">
                                    <option value="">전체</option>
                                    @foreach($dates as $date)
                                        @if($date->code < 1020)
                                        <option value="{{ $date->code }}">{{ $date->value1 }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <select name="e_dm_date" class="form-control form-control-sm" style="margin-left:2%;width:49%;">
                                    <option value="">전체</option>
                                    @foreach($dates as $date)
                                        @if($date->code >= 1020)
                                        <option value="{{ $date->code }}">{{ $date->value1 }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0">
                <button type="button" class="btn btn-sm btn-outline-primary pr-1 waves-light waves-effect dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i id="" class="search-btn-label fa fa-square fs-12"></i> <i class="bx bx-chevron-down fs-12"></i>
                </button>
                <div class="dropdown-menu" style="min-width:0">
                    <a id="search-btn-minus" class="dropdown-item" data-search-type="minus" href="#"><i class="fa fa-minus-square"></i></a>
                    <a id="search-btn" class="dropdown-item" href="#" data-search-type="default"><i class="fa fa-square"></i></a>
                    <a id="search-btn-plus" class="dropdown-item" href="#" data-search-type="plus"><i class="fa fa-plus-square"></i></a>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
                <div class="fr_box flax_box" style="color:#FF0000;font-weight:bold;">
					<div class="mr-1">
						<select id="s_state" name="s_state" class="form-control form-control-sm" style="width: 100px;">
							<option value="">전체</option>
                            @foreach($states as $state)
                            <option value="{{ $state->code }}">{{ $state->value1 }}</option>
                            @endforeach
                        </select>
					</div>
					<a href="#" onclick="ChangeData()" class="btn-sm btn btn-primary">선택 상태 변경</a>
				</div>
			</div>
		</div>
        <div class="table-responsive">
			<div id="div-gd" style="height: calc(100vh - 20vh); width: 100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<script language="javascript">
    const defaultCellStyle = {
        'text-align' : 'center',
    };

	function StyleState(params){
		var state = {
			"확정완료":"#0000ff",
			"접수완료":"#669900",
			"접수대기":"#000000",
			"접수중":"#000000",
			"현장결제":"#0000e0"
		}
		if(state[params.value]){
			var color = state[params.value];
			return {
				color:color,
				'text-align': 'center',
			}
		}
	}

    let columns = [
		{headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left',
			cellStyle: {
                "background":"#F5F7F7"
            }
		},
		{headerName: '#', width:35, maxWidth: 100, cellStyle: defaultCellStyle, cellRenderer: 'loadingRenderer', pinned:'left', 
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id'
		},
        {headerName: "등록번호", field: "regist_number", width:120, cellStyle: defaultCellStyle, pinned:'left',
            cellRenderer: (params) => `<a href="#" onClick="updateData('${params.value}')">${params.value}</a>`
		},
        {headerName: "이름", field: "name1", width:108},
        {headerName: "성", field: "name2", width:84},
        {headerName: "휴대폰", field: "mobile", width:96},
        {headerName: "이메일", field: "email", width:150},
        {headerName: "상태", field: "state_nm", width:72, cellStyle: StyleState},
        {headerName: "전예약일", field: "s_dm_date_nm", width:84, cellStyle: defaultCellStyle},
        {headerName: "전예약룸", field: "s_dm_type_nm", width:140},
        {headerName: "후예약일", field: "e_dm_date_nm", width:84, cellStyle: defaultCellStyle},
        {headerName: "후예약룸", field: "e_dm_type_nm", width:140},
        {headerName: "확정", field: "confirm_yn", cellStyle: defaultCellStyle, 
            cellRenderer: function(params) {
                if(params.value == 'Y') return "예"
                else if(params.value == 'N') return "아니오"
                else return params.value
            }
        },
        {headerName: "확정일", field: "confirm_dt", width:110, cellStyle: defaultCellStyle},
        {headerName: "등록일", field: "reg_dt", width:110, cellStyle: defaultCellStyle},
        {headerName: "수정일", field: "updt_dt", width:110, cellStyle: defaultCellStyle},
        {headerName: "_상태", field: "state", hide:true},
        {headerName: "", field: "", width:"auto"},
    ];

	function ChangeData() {
		var checkRows = gx.gridOptions.api.getSelectedRows();

		if( checkRows.length === 0 ){
            alert("수정하실 데이터를 선택해주세요.");
            return;
		}

		if( $("#s_state").val() == "" ){
            alert("변경하실 상태를 선택해주세요.");
            return;
		}

		if( $("#s_state").val() == "40" || $("#s_state").val() == "41" ) {
			if(!confirm("확정완료/현장결제 상태로 변경하면 고객의 예약이 확정됩니다.")){
				return;
			}
		}

		if( confirm("선택하신 데이터의 상태를 변경하시겠습니까?") ) {
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/classic/cls02/chg-state',
				data: {
					data : JSON.stringify(checkRows),
                    s_state : $("#s_state").val()
				},
				success: function (data) {
					if(data.code == "200") {
						alert("선택한 데이터의 상태가 수정 되었습니다.");
						Search();
					}else{
						alert("선택한 데이터의 상태 수정이 실패하였습니다.");
					}
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					console.log("error")
				}
			});
        }
	}

	function updateData(regist_number){
		const url='/head/classic/cls02/show/' + regist_number;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
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
        gx.Request('/head/classic/cls02/search', data, 1);
    }

</script>
@stop