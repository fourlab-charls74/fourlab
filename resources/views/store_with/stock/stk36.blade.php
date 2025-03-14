@extends('store_with.layouts.layout')
@section('title','동종업계월별매출관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">동종업계월별매출관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 매장관리</span>
        <span>/ 동종업계월별매출관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
                    <a href="javascript:void(0);" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 등록</a>
                    <a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="">매출기간</label>
							<div class="docs-datepicker flex_box">
								<div class="input-group">
									<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
                    </div>
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
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
            <a href="javascript:void(0);" onclick="add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
            <a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
            <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="table-responsive">
			<div id="div-gd2" style="height:100%;min-height:120px " class="ag-theme-balham"></div>
		</div>
	</div>
	<div class="card-body shadow">
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	
	const pinnedRowData = [{ rank_idx : "랭크" }];
	
    let columns = [
		{field : "rank_idx", headerName: "순위", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"},
			cellRenderer: function(params) {
				if (params.node.rowPinned === 'top') {
					return "랭크";
				} else {
					return params.node.rowIndex + 1;
				}
			}
		},
        {width: 'auto'}
    ];

	let columns2 = [
		{headerName: "브랜드", field: "brand", width: 130 ,
			cellStyle: function(params) {
				if(params.value == '피엘라벤') {
					return {color: '#2E64FE'};
				} else if (params.value == '아크테릭스') {
					return {color: '#FA5858'};
				} else if (params.value == '파타고니아') {
					return {color: '#04B431'};
				}
			}
		},
		{headerName: "매장수", field: "store_cnt", width: 130, 
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE', 'text-align': 'center' };
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858',  'text-align': 'center' };
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431',  'text-align': 'center' };
				}
			}
		},
		{headerName: "전체매출", field: "total_amt", width: 130, type: "currencyType",
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE'};
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858'};
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431'};
				}
			}
		},
		{headerName: "매장평균매출", field: "store_avg_amt", width: 130, type: "currencyType",
			cellRenderer: function (params) {
				let store_cnt = params.data.store_cnt;
				let total_amt = params.data.total_amt;
				let avg = total_amt / store_cnt;
				return Comma(Math.round(avg));
			},
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE'};
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858'};
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431'};
				}
			}
		},
		{headerName: "최저매출매장", field: "worst_amt_store", width: 130,
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE'};
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858'};
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431'};
				}
			}
		},
		{headerName: "최저매출매장", field: "worst_amt", width: 130, type: "currencyType",
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE'};
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858'};
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431'};
				}
			}
		},
		{headerName: "최고매출매장", field: "best_amt_store", width: 130,
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE'};
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858'};
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431'};
				}
			}
		},
		{headerName: "최고매출매장", field: "best_amt", width: 130, type: "currencyType",
			cellStyle: function(params) {
				if (params.node.rowIndex === 0) {
					return { color: '#2E64FE'};
				} else if (params.node.rowIndex === 1) {
					return { color: '#FA5858'};
				} else if (params.node.rowIndex === 2) {
					return { color: '#04B431'};
				}
			}
		},
		{width: 'auto'}
	];

    const pApp = new App('',{
        gridId:"#div-gd",
    });
	const pApp2 = new App('',{
		gridId:"#div-gd2",
	});
    let gx;
	let gx2;

    $(document).ready(function() {
        pApp.ResizeGrid(390);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData : pinnedRowData,
			getRowStyle: (params) => {
				if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
			},
		});

		pApp2.ResizeGrid(1100);
		pApp2.BindSearchEnter();
		let gridDiv2 = document.querySelector(pApp2.options.gridId);
		gx2 = new HDGrid(gridDiv2, columns2);
        Search();
		totalSearch();

        // 엑셀다운로드 레이어 오픈
        $(".export-excel").on("click", function (e) {
			
			let gridOptions = gx.gridOptions;

			let processCellCallback = function(params) {
				if (params.node.rowPinned === 'top') {
					return processPinnedTopRowCell(params);
				} else {
					return processNormalRowCell(params);
				}
				
				return params.value;
			};

			let processPinnedTopRowCell = function(params) {
				if (params.node.rowIndex == 0 && params.column.colId == 'rank_idx') {
					return '랭크';
				}
				
				return (params.value != null) ? (params.value) : null;
			};

			let processNormalRowCell = function(params) {
				if (params.column.getColId() === 'rank_idx') {
					return parseInt(params.value) + 1;
				}
				return params.value;
			};

			let excelParams = {
				fileName: '월별동종업계매출_{{ date('YmdH') }}.xlsx',
				sheetName: 'Sheet1',
				processCellCallback: processCellCallback
			};

			gridOptions.api.exportDataAsExcel(excelParams);
        });

        // 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
        load_store_channel();
    });
	
	function rowFontColor(params) {
		if (params.node.rowIndex === 1) {
			return { color: '#2E64FE' };
		} else if (params.node.rowIndex === 2) {
			return { color: '#FA5858' };
		} else if (params.node.rowIndex === 3) {
			return { color: '#04B431' };
		}
	}

	function setColumn(stores, data) {
		if(!stores) return;

		columns.splice(1);

		for(let i = 0; i < stores[0].length; i++) {
			let cd = stores[0][i].store_cd;
			let nm = stores[0][i].store_nm;
			columns.push({
				field: cd,
				headerName: nm + '(피엘라벤)',
				minWidth : 600,
				children: [
					{field: cd + '_brand', headerName: '브랜드', minWidth : 100,
						cellStyle: function(params) {
							if(params.value == '피엘라벤') {
								return {color: '#2E64FE'};
							} else if (params.value == '아크테릭스') {
								return {color: '#FA5858'};
							} else if (params.value == '파타고니아') {
								return {color: '#04B431'};
							} else if (params.node.rowPinned === 'top') {
								return { 'text-align' : 'right' };
							}
						},
						cellRenderer: function (params) {
							let rightCellElement = params.eGridCell.nextElementSibling;
							if(params.value == '피엘라벤') {
								rightCellElement.style.color = '#2E64FE';
								return params.value;
							} else if (params.value == '아크테릭스') {
								rightCellElement.style.color = '#FA5858';
								return params.value;
							} else if (params.value == '파타고니아') {
								rightCellElement.style.color = '#04B431';
								return params.value;
							} else {
								return params.value;
							}
						},
					},
					{field: cd + '_amt', headerName: '매출', type: "currencyType", minWidth : 100,
						cellStyle: function(params) {
							if (params.node.rowPinned === 'top') {
								return { 'text-align' : 'left' };
							}
						},
					}
				],
			});
		}
		columns.push({ width: "auto" });
		gx.gridOptions.api.setColumnDefs(columns);
	}

    //검색
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/stock/stk36/search', data, 1, function(e) {
			setColumn(e.head.stores, e.body);
			
			let rank = e.head.rank_data;
			let stores = e.head.stores;

			const pinnedRowData = {
				rank_idx : "랭크"	
			};

			for (let i = 0; i < stores[0].length; i++) {
				let store_cd = stores[0][i].store_cd;
				let ranks = rank[`${store_cd}`].split('/');
				pinnedRowData[`${store_cd}_brand`] = ranks[0] || 0;
				pinnedRowData[`${store_cd}_amt`] = ranks[1] || 0;
			}

			gx.gridOptions.api.setPinnedTopRowData([pinnedRowData]);
		});
		totalSearch();
    }

	function totalSearch() {
		let data = $('form[name="search"]').serialize();
		gx2.Request('/store/stock/stk36/total-search', data);
	}


    // 매출액 등록 팝업
    function add() {
        const url = '/store/stock/stk36/create';
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=700");
    }
    
</script>


@stop
