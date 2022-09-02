@extends('store_with.layouts.layout')
@section('title','매장LOSS등록')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장LOSS등록</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
        <span>/ 영업관리</span>
		<span>/ 매장LOSS등록</span>
	</div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>실사일자</label>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>실사코드</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='sc_cd' value='{{ @$sc_cd }}'>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>매장</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
						</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>LOSS처리여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sc_state_A" name="sc_state" value="" @if(@$sc_state == "") checked @endif />
                                    <label class="custom-control-label" for="sc_state_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sc_state_Y" name="sc_state" value="Y" @if(@$sc_state == "Y") checked @endif />
                                    <label class="custom-control-label" for="sc_state_Y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sc_state_N" name="sc_state" value="N" @if(@$sc_state == "N") checked @endif />
                                    <label class="custom-control-label" for="sc_state_N">N</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
        </div>
    </form>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
                    <div class="fr_box">
                        <a href="javascript:void(0);" onclick="addLossData()" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> LOSS 등록</a>
                    </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, sort: null, width: 30,
            checkboxSelection: function(params) {
                return params.data.sc_state === "N";
            }
        },
        {field: "sc_date", headerName: "실사일자", width: 100, cellStyle: {"text-align": "center"}},
        {field: "sc_cd", headerName: "실사코드", width: 100, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.value}</a>`;
            }
        },
        {field: "store_cd", headerName: "매장코드", width: 100, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "매장명", width: 150},
        {field: "loss_qty", headerName: "LOSS 총수량", width: 100, type: "currencyType"},
        {field: "loss_price", headerName: "LOSS 총금액", width: 100, type: "currencyType"},
        {field: "sc_state", headerName: "LOSS처리여부", width: 100, 
            cellStyle: (params) => ({"text-align": "center", "color": params.value == "N" ? "red" : params.value == "Y" ? "green" : "none"}),
            cellRenderer: (params) => params.value === "Y" ? "등록" : "미등록",
        },
        {field: "md_id", hide: true},
        {field: "md_nm", headerName: "담당자", width: 80, cellStyle: {"text-align": "center"}},
        {field: "comment", headerName: "메모", width: 300},
        {width: 'auto'}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        initStore();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal20/search', data, -1);
	}

    
    // 특정매장 파라미터로 전달 시 매장정보 초기화
    function initStore() {
        const store_cd = '{{ @$store->store_cd }}';
        const store_nm = '{{ @$store->store_nm }}';

        if(store_cd != '') {
            const option = new Option(store_nm, store_cd, true, true);
            $('#store_no').append(option).trigger('change');
        }
        Search();
    }

    function openDetailPopup(sc_cd = '') {
        const url = '/store/stock/stk26/show/' + sc_cd + "?editable=N";
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }

    function addLossData() {
        // loss 등록처리
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("LOSS 등록할 항목을 선택해주세요.");
        if(!confirm("LOSS 등록하시겠습니까?")) return;

        axios({
            url: '/store/sale/sal20/loss',
            method: 'post',
            data: {
                data: rows.map(r => ({ sc_cd: r.sc_cd, store_cd: r.store_cd })),
            },
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("LOSS 등록이 성공적으로 완료되었습니다.");
                Search();
            } else {
                console.log(res.data);
                alert("등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop
