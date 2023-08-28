@extends('shop_with.layouts.layout-nav')
@section('title', '재고현황')
@section('content')
<div class="py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">재고현황</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 바코드 - {{ @$prd->prd_cd }}</span>
            </div>
        </div>
    </div>

    @if(empty($prd))
    <p class="w-100 fs-16 text-center">해당 상품의 정보가 존재하지 않습니다.</p>
    @else
    {{-- 상품정보 --}}
    <div class="show_layout mb-4">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#">상품정보</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="120px"/>
                                <col width="15%"/>
                                <col width="35%"/>
                                <col width="15%"/>
                                <col width="35%"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td rowspan="5" class="img_box brln">
                                    @if (@$prd->img !== null)
                                    <img class="goods_img" src="{{config('shop.image_svr')}}/{{@$prd->img}}" alt="이미지" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;" />
                                    @else
                                    <p class="d-flex align-items-center justify-content-center" style="min-width: 120px;max-width:120px; min-height: 120px;max-height:120px;">이미지 없음</p>
                                    @endif
                                </td>
                                <th>바코드</th>
                                <td>{{ @$prd->prd_cd }}</td>
                                <th>온라인코드</th>
                                <td>{{ @$prd->goods_no }}</td>
                            </tr>
                            <tr>
                                <th>스타일넘버</th>
                                <td>{{ @$prd->style_no }}</td>
                                <th>공급업체명</th>
                                <td>{{ @$prd->com_nm }}</td>
                            </tr>
                            <tr>
                                <th>품목</th>
                                <td>{{ @$prd->opt_kind_nm }}</td>
                                <th>브랜드</th>
                                <td>{{ @$prd->brand_nm }}</td>
                            </tr>
                            <tr>
                                <th>상품명</th>
                                <td>{{ @$prd->goods_nm }}</td>
                                <th>컬러/사이즈</th>
                                <td>{{ @$prd->color_cd == '00' ? '단일색상' : @$prd->color }} / {{ @$prd->size }}</td>
                            </tr>
                            <tr>
                                <th>정상가/현재가</th>
                                <td>{{ number_format(@$prd->goods_sh) }}원 / {{ number_format(@$prd->price) }}원</td>
                                <th>원가</th>
                                <td>{{ number_format(@$prd->wonga) }}원</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 창고/매장 재고현황 --}}
    <div class="show_layout">
        <div class="card shadow">
            <div class="card-header mb-0">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <a href="#" class="m-0 font-weight-bold">창고/매장별 재고현황</a>
                    </div>
                    <div class="fr_box">
                        <div class="font-weight-light">
                            <span class="mr-2">* 실재고 / 보유재고</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <h6 class="fs-16">[ 창고 현재 재고 ]</h6>
                <div class="table-responsive mb-1">
                    <div id="div-gd-storage-stock" class="ag-theme-balham"></div>
                </div>
                @if($ostore_stock_yn == 'Y') 
                <h6 class="fs-16 mt-3">[ 매장 현재 재고 ]</h6>
                <div class="table-responsive mb-1">
                    <div id="div-gd-store-stock" class="ag-theme-balham"></div>
                </div>
                @endif
                <p class="mt-1" style="color:red;">* 매장에 입고된 적이 없는 상품의 경우 재고가 표시되지 않습니다.</p>
            </div>
        </div>
    </div>
		<div id="search-area" class="search_cum_form mb-4">
			<form name="search" method="get">
				<input type="hidden" name="prd_cd" value="{{ @$prd->prd_cd }}">
				<div class="card">
					<div class="d-flex card-header justify-content-between">
						<h4>매장기간재고 검색</h4>
						<div class="flax_box">
							<a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-12 inner-td">
								<div class="form-group">
									<div class="form-group">
										<label>조회일자</label>
										<div class="form-inline date-select-inbox">
											<div class="docs-datepicker form-inline-inner input_box">
												<div class="input-group">
													<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off" disable>
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
													<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ @$edate }}" autocomplete="off">
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
						<h6 class="fs-16 mt-3">[ 매장 기간 재고 ] <span style="font-size: 12px;"> - 조회일자 반영, 보유재고 기준</span></h6>
						<div class="table-responsive">
							<div id="div-gd-store-stock-detail" class="ag-theme-balham"></div>
						</div>
						<p class="mt-1" style="color:red;">* 매장에 입고된 적이 없는 상품의 경우 재고가 표시되지 않습니다.</p>
					</div>
				</div>
			</form>
		</div>
    @endif
</div>

<script language="javascript">
    let AlignCenter = {"text-align": "center"};

    let storage_cnt = '{{ count(@$storages) }}' * 1;
    const storageLastWidth = 919 - (storage_cnt * 100);

    let storage_columns = [
        @foreach (@$storages as $storage)
            {field: '{{ $storage->storage_cd }}', headerName: '{{ $storage->storage_nm }}', cellStyle: AlignCenter, width: 100},
        @endforeach
        {width: storageLastWidth > 0 ? storageLastWidth : 0},
    ];

    const pApp = new App('', { gridId: "#div-gd-storage-stock" });
    let gx;

    let store_columns = [
        // {field: "total_qty", headerName: "매장 총재고", width: 100, pinned: "left", cellStyle: AlignCenter},
    ];

    const pApp2 = new App('', { gridId: "#div-gd-store-stock" });
    let gx2;

    const pinnedRowData = [{ store_cd: '합계' }];
    const setStockBgColor = (params) => params.node.rowPinned === 'top' ? '' : ({ 'background-color': params.value != 0 ? '#FFF5E5' : 'none' });
    let store_detail_columns = [
        {field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 60, cellStyle: AlignCenter},
        {field: "store_nm",	headerName: "매장명", pinned: 'left', width: 140},
        {field: "prev_qty", headerName: "이전재고", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "store_in_qty", headerName: "매장입고", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "store_return_qty", headerName: "매장반품", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "rt_in_qty", headerName: "이동입고", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "rt_out_qty", headerName: "이동출고", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "sale_qty", headerName: "매장판매", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "loss_qty", headerName: "LOSS", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {field: "term_qty", headerName: "기간재고", width: 74, type: "currencyType", cellStyle: setStockBgColor},
        {width: "auto"}
    ];

    const pApp3 = new App('', { gridId: "#div-gd-store-stock-detail" });
    let gx3;

    $(document).ready(function() {
        pApp.ResizeGrid(275, 80);
        let gridDiv = document.querySelector(pApp.options.gridId);
        if (gridDiv !== null) {
            gx = new HDGrid(gridDiv, storage_columns, {
                defaultColDef: {
                    suppressMenu: true,
                    resizable: true,
                    autoHeight: true,
                }
            });
        }

        pApp2.ResizeGrid(275, 80);
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        if (gridDiv2 !== null) {
            gx2 = new HDGrid(gridDiv2, store_columns, {
                alwaysShowHorizontalScroll: true,
                defaultColDef: {
                    suppressMenu: true,
                    resizable: true,
                    autoHeight: true,
                }
            });
        }

        pApp3.ResizeGrid(275, 220);
        let gridDiv3 = document.querySelector(pApp3.options.gridId);
        if (gridDiv3 !== null) {
            gx3 = new HDGrid(gridDiv3, store_detail_columns, {
                pinnedTopRowData: pinnedRowData,
                getRowStyle: (params) => { // 고정된 row styling
                    if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
                },
            });
        }

        let data = $("form[name=search]").serialize();

        @if($user_store != '')
            $("#store_no").select2({data:['{{ @$user_store }}']??'', tags: true});
        @endif

        search_storage_stock(data);
        Search();

        // 매장검색
        $( ".sch-store" ).on("click", function() {
            searchStore.Open(null, "multiple");
        });
    });

    function setStoreGridColumn(stores) {
        if(!stores) return;
        store_columns.splice(1);

        for(let i = 0; i < stores.length; i++) {
            store_columns.push({
                field: stores[i].store_cd,
                headerName: stores[i].store_nm,
                cellStyle: AlignCenter,
                width: 100,
            });
        }
        const lastWidth = 921 - (stores.length * 100);
        store_columns.push({ width: lastWidth > 0 ? lastWidth : 0 });
        gx2.gridOptions.api.setColumnDefs(store_columns);
    }
</script>
<script>
    function Search() {
        let data = $("form[name=search]").serialize();
        search_store_stock(data);
        search_store_stock_detail(data);
    }

    async function search_storage_stock(data) {
        let res = await axios({ method: "get", url: "/shop/stock/stk01/search-stock/storage?" + data });
        let total = res.data.total;
        let list = res.data.data;
        
        gx.gridOptions.api.setRowData([]);
        let col = { total_qty: total.qty + " / " + total.wqty };
        for(let s of list) {
            col[s.storage_cd] = s.qty + " / " + s.wqty;
        }
        await gx.gridOptions.api.applyTransaction({ add: [col] });
    }

    async function search_store_stock(data) {
        let res = await axios({ method: "get", url: "/shop/stock/stk01/search-stock/store?" + data });
        let total = res.data.total;
        let list = res.data.data;

        gx2.gridOptions.api.setRowData([]);
        setStoreGridColumn(list);
        let col = { total_qty: total.qty + " / " + total.wqty };
        for(let s of list) {
            col[s.store_cd] = s.qty + " / " + s.wqty;
        }
        await gx2.gridOptions.api.applyTransaction({ add: [col] });
    }

    async function search_store_stock_detail(data) {
        gx3.Request("/shop/stock/stk01/search-stock/store-detail", data, -1, async function(d) {
            let pinnedRow = gx3.gridOptions.api.getPinnedTopRow(0);
            let total_data = d.head.total_data;
            if(pinnedRow && total_data != '') {
				gx3.gridOptions.api.setPinnedTopRowData([
					{ ...pinnedRow.data, ...total_data }
				]);
			}
        });
    }
</script>
@stop
