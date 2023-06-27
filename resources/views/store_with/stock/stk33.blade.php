@extends('store_with.layouts.layout')
@section('title','동종업계일별매출관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">동종업계일별매출관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 매장관리</span>
        <span>/ 동종업계일별매출관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="#" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
                    <a href="#" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 등록</a>
                    <button id="download-list" onclick="gx.Download();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="bx bx-download fs-16"></i> 엑셀다운로드
                    </button>&nbsp;&nbsp;
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="good_types">매출기간</label>
                                <div class="form-inline date-select-inbox">
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
                        <!-- <div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="good_types">판매채널/매장구분</label>
								<div class="d-flex align-items-center">
									<div class="flex_box w-100">
										<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
											<option value=''>전체</option>
										@foreach ($store_channel as $sc)
											<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
										@endforeach
										</select>
									</div>
									<span class="mr-2 ml-2">/</span>
									<div class="flex_box w-100">
										<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
											<option value=''>전체</option>
										@foreach ($store_kind as $sk)
											<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
										@endforeach
										</select>
									</div>
								</div>
							</div>
						</div> -->
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="store_no">매장명</label>
                                <div class="form-inline inline_btn_box search-enter" >
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="cs.sale_date">매출일</option>
                                            <option value="total_amt">합계</option>
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
                    <div class="row">
                        {{-- <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="good_types">동종업계</label>
                                <div class="flax_box">
                                    <select name='competitor_type' class="form-control form-control-sm search-enter">
                                        <option value=''>전체</option>
                                    @foreach ($competitors as $competitor)
                                        <option value='{{ $competitor->code_id }}'>{{ $competitor->code_val }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> --}}
                        
                    </div>
                </div>
            </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/store/stock/stk33/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
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

const pinnedRowData = [{ store_nm : '합계' , "total_amt" : 0 , "store_amt" : 0,

    @foreach($competitors as $com)
         amt_{{$com->code_id}} : 0,
    @endforeach
}];

    
    let columns = [
        {headerName: "#", field: "num",type:'NumType', pinned:'left', cellClass: 'hd-grid-code',
            cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
        },
        {headerName: "매출일", field: "sale_date", pinned:'left',  width: 80, cellClass: 'hd-grid-code'},
        {headerName: "매장코드", field: "store_cd",  pinned:'left', width: 70, cellClass: 'hd-grid-code', aggFunc: "first"},
        {headerName: "매장명", field: "store_nm",  pinned:'left', width: 110, cellClass: 'hd-grid-code', aggFunc: "first"},
        {headerName: "매장코드", field: "store_cd",  pinned:'left', width: 70, cellClass: 'hd-grid-code' , hide:true},
        {headerName: "동종업계코드", field: "competitor_cd",  pinned:'left', width: 70, cellClass: 'hd-grid-code' , hide:true},
        {headerName: "매장구분", field: "store_type",  pinned:'left', width: 70, cellClass: 'hd-grid-code', hide:true},
        {headerName: "매장매출액", field: "store_amt",  pinned:'left', width: 100, cellClass: 'hd-grid-code', type:'currencyType', cellStyle: { 'font-weight': '700', background: '#eee', textAlign: 'right' },aggFunc: "first",},
        {headerName: "합계(원)", field: "total_amt",  pinned:'left', width: 100, cellClass: 'hd-grid-code', type:'currencyType', cellStyle: { 'font-weight': '700', background: '#eee', textAlign: 'right' },aggFunc: "first",},
        {field: "competitors",	headerName: "동종업계 매장",
            children: [
                @foreach($competitors as $com)
                    {headerName: "{{ $com->code_val }}", field: "amt_{{$com->code_id}}",  width: 90, cellClass: 'hd-grid-code', type:'currencyType', cellStyle:{textAlign:'right'},aggFunc: "first"},
                @endforeach
            ]
        },
        {width: 'auto'}
    ];

    const pApp = new App('',{
        gridId:"#div-gd"
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
                if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
            },
        });
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/stock/stk33/search', data, 1, function(e){
            let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            let total_data = e.head.total_data;

			if(pinnedRow && total_data != '') {
				gx.gridOptions.api.setPinnedTopRowData([
					{ ...pinnedRow.data, ...total_data }
				]);
			}
        });

    }

    //검색조건초기화
    const initSearchInputs = () => {
        document.search.reset(); // 모든 일반 input 초기화
        $('#store_no').val(null).trigger('change'); // 브랜드 select2 박스 초기화
        location.reload();
    };

    //추가버튼 팝업 오픈
    function add() {
        const url = '/store/stock/stk33/create';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=700");
    }

    // 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
	$(document).ready(function() {
		const store_channel = document.getElementById("store_channel");
		const store_channel_kind = document.getElementById("store_channel_kind");

		store_channel.addEventListener("change", () => {
			if (store_channel.value) {
				store_channel_kind.disabled = false;
			} else {
				store_channel_kind.disabled = true;
			}
		});
	});

	// 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
	function chg_store_channel() {

		const sel_channel = document.getElementById("store_channel").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel',
			data: {
				'store_channel' : sel_channel
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#store_channel_kind').append(option);
					}

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


@stop
