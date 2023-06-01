@extends('store_with.layouts.layout')
@section('title','실사')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">실사</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 실사</span>
	</div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch(['#store_no'])">검색조건 초기화</a> -->
                    <a href="javascript:void(0);" onclick="openDetailPopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 실사개별등록</a>
                    <a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 실사일괄등록</a>
                    <a href="javascript:void(0);" onclick="openBarCodePopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 실사바코드등록</a>
                    <a href="javascript:void(0);" onclick="moveToSal20()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fa fa-arrow-right fa-sm mr-1"></i> LOSS등록</a>
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
                                <input type='text' class="form-control form-control-sm search-enter" name='sc_cd'>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>매장</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
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
                                    <input type="radio" class="custom-control-input" id="sc_state_A" name="sc_state" value="" checked />
                                    <label class="custom-control-label" for="sc_state_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sc_state_Y" name="sc_state" value="Y" />
                                    <label class="custom-control-label" for="sc_state_Y">등록</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="sc_state_N" name="sc_state" value="N" />
                                    <label class="custom-control-label" for="sc_state_N">미등록</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch()">검색조건 초기화</a> -->
            <a href="javascript:void(0);" onclick="openDetailPopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 실사등록</a>
            <a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 실사일괄등록</a>
            <a href="javascript:void(0);" onclick="openBarCodePopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 실사바코드등록</a>
            <a href="javascript:void(0);" onclick="moveToSal20()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fa fa-arrow-right fa-sm mr-1"></i> LOSS등록</a>
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
                    <a href="javascript:void(0);" onclick="return DeleteStockCheck();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fa fa-trash fa-sm mr-1"></i> 삭제</a>
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
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28},
        {field: "sc_date", headerName: "실사일자", width: 80, cellStyle: {"text-align": "center"}},
        {field: "sc_cd", headerName: "실사코드", width: 100, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.value}</a>`;
            }
        },
        {field: "sc_type", headerName: "실사구분", width: 70, cellStyle: (params) => ({"text-align": "center", "color": params.value === 'B' ? '#2aa876' : 'none'}),
            cellRenderer: (params) => params.value === 'G' ? '일반' : params.value === 'B' ? '일괄' : params.value === 'C' ? '바코드' : '-',
        },
        {field: "store_cd", headerName: "매장코드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "매장명", width: 170},
        {field: "loss_qty", headerName: "LOSS 총수량", width: 80, type: "currencyType"},
        {field: "loss_price", headerName: "LOSS 총금액", width: 80, type: "currencyType"},
        {field: "sc_state", headerName: "LOSS처리여부", width: 90, 
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

        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk26/search', data, -1);
	}

    function openDetailPopup(sc_cd = '') {
        const url = '/store/stock/stk26/show/' + sc_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }
    
    function openBatchPopup() {
        const url = '/store/stock/stk26/batch';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");    
    }

    function openBarCodePopup() {
        const url = '/store/stock/stk26/barcode-batch';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");    
    }

    function moveToSal20() {
        const url = "/store/sale/sal20?from=stk26";
        let params = "";
        params += "&sdate=" + $("[name=sdate]").val();
        params += "&edate=" + $("[name=edate]").val();
        params += "&sc_cd=" + $("[name=sc_cd]").val();
        params += "&store_cd=" + $("[name=store_no]").val();
        params += "&sc_state=" + $("[name=sc_state]:checked").val();
        window.open(url + params, "_blank");
    }

    function DeleteStockCheck() {
        let rows = gx.getSelectedRows();

        let loss_rows = rows.filter(r => r.sc_state == 'Y');
        if (loss_rows.length > 0) {
            gx.gridOptions.api.forEachNode((node) => {
                if (node.selected && node.data?.sc_state == 'Y') {
                    node.setSelected(false);
                }
            });
            return alert("LOSS등록된 실사정보는 삭제할 수 없습니다.");
        }

        if (!confirm("실사정보를 삭제하시겠습니까?\n삭제된 실사정보는 되돌릴 수 없습니다.")) return;

        axios({
            url: '/store/stock/stk26',
            method: 'delete',
            data: { sc_cds: rows.map(r => r.sc_cd) }
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("실사정보가 삭제되었습니다.");
                Search();
            } else {
                console.log(res.data);
                alert(res.data.msg);
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop
