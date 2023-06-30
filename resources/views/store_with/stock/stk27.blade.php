@extends('store_with.layouts.layout')
@section('title','창고재고조정')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">창고재고조정</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>상품관리</span>
		<span>/ 창고재고조정</span>
	</div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50 mr-1"></i> 조회</a>
                    <a href="javascript:void(0);" onclick="openDetailPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm mr-1"></i> 재고조정개별등록</a>
                    <a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm mr-1"></i> 재고조정일괄등록</a>
                    <a href="javascript:void(0);" onclick="openBarCodePopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm mr-1"></i> 재고조정바코드등록</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>재고조정일자</label>
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
							<label>창고</label>
	                        <div class="form-inline inline_btn_box">
		                        <input type='hidden' id="storage_nm" name="storage_nm">
		                        <select id="storage_no" name="storage_no" class="form-control form-control-sm select2-storage"></select>
		                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-storage-one"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
	                        </div>
						</div>
                    </div>
	                <div class="col-lg-4 inner-td">
		                <div class="form-group">
			                <label>재고조정사유</label>
			                <div class="flex_box">
				                <select name='loss_reason' id="loss_reason" class="form-control form-control-sm">
					                <option value=''>전체</option>
				                @foreach ($loss_reasons as $reason)
					                <option value='{{ $reason->code_id }}'>{{ $reason->code_val }}</option>
				                @endforeach
				                </select>
			                </div>
		                </div>
	                </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="javascript:void(0);" onclick="openDetailPopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 재고조정개별등록</a>
            <a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 재고조정일괄등록</a>
            <a href="javascript:void(0);" onclick="openBarCodePopup()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 재고조정바코드등록</a>
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
        { headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellClass: 'hd-grid-code' },
        { field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28 },
        { field: "ssc_date", headerName: "재고조정일자", width: 100, cellClass: 'hd-grid-code' },
        { field: "ssc_cd", headerName: "재고조정코드", width: 140, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                let ssc_date = params.data.ssc_date.replaceAll('-','');
                let ssc_cd = params.data.ssc_cd.toString().padStart(3, '0');
                return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.data.storage_cd}_${ssc_date}_${ssc_cd}</a>`;
            }
        },
        { field: "ssc_type", headerName: "재고조정구분", width: 90, cellClass: 'hd-grid-code', 
	        cellStyle: (params) => ({ "color": params.value === 'B' ? '#2aa876' : params.value === 'C' ? '#ff7f00' : 'none' }),
            cellRenderer: (params) => params.value === 'G' ? '일반' : params.value === 'B' ? '일괄' : params.value === 'C' ? '바코드' : '-',
        },
        { field: "storage_cd", headerName: "창고코드", width: 80, cellClass: 'hd-grid-code' },
        { field: "storage_nm", headerName: "창고명", width: 150 },
        { field: "storage_qty", headerName: "창고보유재고", width: 90, type: "currencyType" },
        { field: "qty", headerName: "조정재고", width: 80, type: "currencyType" },
        { field: "loss_qty", headerName: "LOSS 총수량", width: 90, type: "currencyType" },
        { field: "loss_price", headerName: "LOSS 금액", width: 90, type: "currencyType" },
        { field: "md_id", hide: true },
        { field: "md_nm", headerName: "담당자", width: 80, cellClass: 'hd-grid-code' },
        { field: "comment", headerName: "메모", width: 0 },
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd", height: 265 });

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk27/search', data, -1);
	}

    function openDetailPopup(ssc_cd = '') {
        const url = '/store/stock/stk27/show/' + ssc_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }
    
    function openBatchPopup() {
        const url = '/store/stock/stk27/batch';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");    
    }

    function openBarCodePopup() {
        const url = '/store/stock/stk27/barcode-batch';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");    
    }
</script>
@stop
