@extends('store_with.layouts.layout')
@section('title','배분현황')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">배분현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 배분현황</span>
	</div>
</div>

<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
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
							<label>배분일자</label>
                            <div class="form-inline">
                                <input type="text" class="form-control form-control-sm docs-date mr-2" name="baebun_date" id="baebun_date" value="" autocomplete="off" onclick="openApi();">
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-baebun"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                <input type="text" class="form-control form-control-sm ml-2" name='rel' id="rel" style="width:70px;" onclick="openApi();">차
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>출고일자</label>
                            <div class="docs-datepicker flex_box">
								<div class="input-group">
								<input type="text" class="form-control form-control-sm docs-date" name="release_date" id="release_date" value="" autocomplete="off">
									<div class="input-group-append">
										<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
											<i class="fa fa-calendar" aria-hidden="true"></i>
										</button>
									</div>
								</div>
								<div class="docs-datepicker-container"></div>
							</div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>배분구분</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[0]" id="baebun_type_0" value="" onclick="changeBaebunType()" checked>
                                    <label class="custom-control-label" for="baebun_type_0">전체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[1]" id="baebun_type_1" value="F" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_1">초도</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[2]" id="baebun_type_2" value="S" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_2">판매</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[3]" id="baebun_type_3" value="R" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_3">요청</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[4]" id="baebun_type_4" value="G" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_4">일반</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[5]" id="baebun_type_5" value="O" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_5">온라인</label>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</form>
</div>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title mb-3">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					<div class="d-flex">
						<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
							<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
							<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>


	<!-- 배분현황 배분일자 -->
	<!-- sample modal content -->
	<div id="SearchBaebunModal" class="modal fade" role="dialog" aria-labelledby="SearchBaebunModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 790px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">배분일자차수조회</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_baebun" method="get" onsubmit="return false">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label for="">조회기준</label>
                                                    <div class="form-inline">
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{$sdate}}" autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                        <span class="text_line">~</span>
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{$edate}}" autocomplete="off">
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
                                        </div>
                                    </div>
                                    <div class="resul_btn_wrap" style="padding-top:7px;text-align:right;display:block;">
                                        <a href="javascript:void(0);" id="search_store_sbtn" onclick="return searchBaebun.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1 pt-0">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-baebun-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-baebun" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<style>
	.ag-row-level-1 {background-color: #f2f2f2 !important;}
	.ag-row-level-2 {background-color: #e2e2e2 !important;}
</style>

<script>
	class CustomHeader {
		init(params) {
			this.eGui = document.createElement('div');
			this.eGui.classList.add('ag-cell-label-container');
			this.eGui.classList.add('ag-header-cell-sorted-none');
			this.eGui.innerHTML = `<span class="w-100 flex-center">${params.displayName.split(' ').join('<br/>')}</span>`;
		}

		getGui() {
			return this.eGui;
		}

		refresh(params) {
			return false;
		}
	}
	const pinnedRowData = [{ store_cd : '합계', qty : 0 }];
	const columns = [
		{field: "baebun_type",	headerName: "배분구분", pinned:'left', width: 80, cellStyle: {'text-align' : 'center'},
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
		},
		{field: "store_cd",	headerName: "매장코드",	width: 80, pinned:'left', cellStyle: {'text-align' : 'center'},
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: function(params) {
				if (params.node.level == 0) {
					return params.value;
				} else if (params.node.rowPinned === 'top') {
					return '합계';
				} else {
					return '';
				}
			}
		},
		{headerName: '매장명', showRowGroup: 'store_nm', cellRenderer: 'agGroupCellRenderer', minWidth: 150, pinned: 'left'},
		{field: "store_nm" , headerName: "매장명", rowGroup: true, hide: true},
		{field: "prd_cd",	headerName: "바코드",	width: 150, cellStyle: {'text-align' : 'center'}},
		{field: "goods_nm",	headerName: "상품명", width: 200,
			cellRenderer: function (params) {
				if (params.value !== undefined) {
					if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
					return '<a href="#" onclick="return openStoreProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
				}
			}
		},
		{field: "color",	headerName: "컬러",	width: 80, cellStyle: {'text-align' : 'center'}},
		{field: "color_nm",	headerName: "컬러명",	width: 100, cellStyle: {'text-align' : 'center'}},
		{field: "size",	headerName: "사이즈",	width: 100, cellStyle: {'text-align' : 'center'}},
		{field: "qty",	headerName: "수량",	width: 80, type: "currencyType", aggFunc : (params) => params.values.reduce((a,c) => a + (c * 1), 0)},
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
                if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
            },
			rollup: true,
            groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
			headerHeight:300,
		});
	});

	function Search() {
		if(!validation()) return;
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal29/search', data, 1, function(e) {
			setColumn(e.head.sizes);
			setAllRowGroupExpanded($("#grid_expand").is(":checked"));
			const t = e.head.total_row;
			gx.gridOptions.api.setPinnedTopRowData([{ 
				store_cd : '합계',
				qty: Comma(t.total_qty),
			}]);
		});
	}

    function changeBaebunType() {
		let baebun_type_0 = $("input[name='baebun_type[0]']");
		let baebun_type_1 = $("input[name='baebun_type[1]']");
		let baebun_type_2 = $("input[name='baebun_type[2]']");
		let baebun_type_3 = $("input[name='baebun_type[3]']");
		let baebun_type_4 = $("input[name='baebun_type[4]']");
		let baebun_type_5 = $("input[name='baebun_type[5]']");
		let otherCheckboxes = $("input[name^='baebun_type']:not([name='baebun_type[0]'])");

		baebun_type_0.click(function() {
			if ($(this).is(":checked")) {
				otherCheckboxes.prop('checked', false);
			}
		});

		otherCheckboxes.change(function() {
			if ($(this).is(":checked")) {
				baebun_type_0.prop('checked', false);
			} else if (baebun_type_1.is(":checked") || baebun_type_2.is(":checked") || baebun_type_3.is(":checked") || baebun_type_4.is(":checked") || baebun_type_5.is(":checked")) {
				baebun_type_0.prop('checked', false);
			} else {
				baebun_type_0.prop('checked', true);
			}
		});
	}

	const validation = () => {

		if($('#baebun_date').val() == "") {
			openApi();
			return alert("배분일자를 선택해주세요.");
		}
		return true;
	}

	function openApi() {
        document.getElementsByClassName('sch-baebun')[0].click();
    }





	// 배분일자차수조회API
	function SearchBaebun(){
		this.grid = null;
	}

	SearchBaebun.prototype.Open = async function(callback = null){
		if(this.grid === null){
			this.SetGrid("#div-gd-baebun");
			$("#SearchBaebunModal").draggable();
			this.callback = callback;
		}
		$('#SearchBaebunModal').modal({
			keyboard: false
		});
	};

	SearchBaebun.prototype.SetGrid = function(divId){
		let columns = [];

		columns.push(
			{headerName: '#', width:40, pinned: 'left', valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellStyle: { "text-align": "center" }},
			{ field:"select_rows", headerName:"선택", pinned:'left', width:50, cellStyle: { "text-align": "center" },
				cellRenderer:function(params) {
					return `<a href="#" onclick="searchBaebun.Choice('${params.data.baebun_date}', '${params.data.rel_baebun}');">선택</a>`;
				}
			},
			{ field:"storage_cd", headerName:"창고코드", width:100, cellStyle: { "text-align": "center" }, hide:true},
			{ field:"storage_nm", headerName:"창고명", pinned: 'left', width:100, cellStyle: { "text-align": "center" }},
			{ field:"baebun_date", headerName:"배분일자", pinned: 'left',width:100, cellStyle: { "text-align": "center" }},
			{ field:"rel_baebun", headerName:"배분차수", width:70, cellStyle: { "text-align": "center" }},
			{ field:"rel_order", headerName:"배분차수", width:70, cellStyle: { "text-align": "center" }, hide:true},
			{ field:"store_cnt", headerName:"매장수", width:50, cellStyle: { "text-align": "right" }},
			{ field:"store_cd", headerName:"매장코드", width:100, cellStyle: { "text-align": "center" }, hide:true},
			{ field:"store_nm", headerName:"매장명", width:130, cellStyle: { "text-align": "center" },
				cellRenderer:function(params) {
					if(params.data.store_cnt == '1') {
						return params.data.store_nm;
					} else {
						return "";
					}
				}
			},
			{ field:"baebun_qty", headerName:"배분수량", width:60, cellStyle: { "text-align": "right" }},
			{ field:"state", headerName:"출고상태", width:100, cellStyle: {"text-align" : "center"},
				cellRenderer:function(params){
					if (params.data.state == '10') {
						return '출고요청'
					} else if (params.data.state == '20') {
						return '출고처리중'
					} else if (params.data.state == '30') {
						return '출고완료'
					} else if (params.data.state == '40') {
						return '매장입고'
					}
				}
			}
		);

		this.grid = new HDGrid(document.querySelector( divId ), columns, {
			getRowStyle: (params) => { // 고정된 row styling
				if (params.data.state == '30' || params.data.state == '40')  return {'background': '#eee', 'border': 'none'};
			},
		});
	};

	SearchBaebun.prototype.Search = function(e) {
		const event_type = e?.type;
		if (event_type == 'keypress') {
			if (e.key && e.key == 'Enter') {
				let data = $('form[name="search_baebun"]').serialize();
				this.grid.Request('/store/sale/sal29/searchBaebun', data);
			} else {
				return false;
			}
		} else {
			let data = $('form[name="search_baebun"]').serialize();
			this.grid.Request('/store/sale/sal29/searchBaebun', data);
		}
	};

	SearchBaebun.prototype.Choice = function(baebun_date, rel_baebun){
		if(this.callback !== null){
			this.callback(baebun_date, rel_baebun);
		} else {
			$('#baebun_date').val(baebun_date);
			$('#rel').val(rel_baebun);
			$('#release_date').val(baebun_date);
		}
		this.InitValue();
		$('#SearchBaebunModal').modal('toggle');
	};



	SearchBaebun.prototype.InitValue = () => {
		document.search_baebun.reset();
		searchBaebun.grid.setRows([]);
		$('#gd-baebun-total').html(0);
	};

	let searchBaebun = new SearchBaebun();


	function setColumn(sizes) {
		if(!sizes) return;
		columns.splice(9);
		
		let size_column = '';

		for (let i = 0; i < sizes.length; i++) {
			let size_cd = sizes[i].size_cd;
			let size_kind_cd = sizes[i].size_kind_cd;
			let size_seq = sizes[i].size_seq;
			
			size_column += size_cd + ' ';
		}

		if (size_column.length > 0) {
			size_column = size_column.trim();
		}
		
		columns.push({field:"" , headerName: size_column , width:100,  headerComponent: CustomHeader},);
		columns.push({field:"" , headerName: size_column , width:100,  headerComponent: CustomHeader},);
		columns.push({width: "auto"});
		gx.gridOptions.api.setColumnDefs(columns);
	}
		

</script>
	
@stop
