@extends('store_with.layouts.layout')
@section('title','출고 > 판매분출고')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">판매분출고</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>매장관리</span>
            <span>/ 출고</span>
            <span>/ 판매분출고</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">

                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                        <a href="/store/stock/stk10" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 출고 리스트</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="good_types">판매일자</label>
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
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="store_type">매장구분</label>
                                <div class="flex_box">
                                    <select name='store_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($store_types as $store_type)
                                            <option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="store_cd">매장명</label>
                                <div class="form-inline inline_btn_box">
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store multi_select" multiple></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="item">상품구분</label>
                                <div class="flex_box">
                                    <select name='type' id="type" class="form-control form-control-sm" style="width: 47%">
                                        <option value=''>전체</option>
                                        <option value='N'>일반</option>
                                        <option value='D'>납품</option>
                                        <option value='E'>기획</option>
                                    </select>
                                    <span class="text_line" style="width: 6%; text-align: center;">/</span>
                                    <select name='goods_type' id="goods_type" class="form-control form-control-sm" style="width: 47%">
                                        <option value=''>전체</option>
                                        <option value='S'>매입</option>
                                        <option value='I'>위탁매입</option>
                                        <option value='P'>위탁판매</option>
                                        <option value='O'>구매대행</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">상품상태</label>
                                <div class="flax_box">
                                    <select name="goods_stat[]" class="form-control form-control-sm multi_select w-100" multiple>
                                        <option value=''>전체</option>
                                        @foreach ($goods_stats as $goods_stat)
                                            <option value='{{ $goods_stat->code_id }}' @if($goods_stat->code_id == 40) selected @endif>{{ $goods_stat->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no">스타일넘버/상품번호</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input-box" style="width:47%">
                                        <div class="form-inline-inner inline_btn_box">
                                            <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <input type="hidden" id="com_cd" name="com_cd" />
                                            <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company search-all search-enter" style="width:100%;" autocomplete="off" />
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="item">품목</label>
                                <div class="flax_box">
                                    <select name="item" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_cd">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="prd_cd">상품코드</label>
                                <div class="flex_box">
                                    <input type='text' class="form-control form-control-sm search-enter" name='prd_cd' value='' />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="goods_no">상품번호</option>
                                            <option value="barcode">상품코드</option>
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

                </div>
            </div>

            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
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
                    <div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
                        {{-- <div class="d-flex mb-1 mb-lg-0">
                            <span class="mr-1">창고</span>
                            <select id='storage' name='storage' class="form-control form-control-sm"  style='width:160px;display:inline'>
                                <option value=''>선택</option>
                                @foreach ($storages as $storage)
                                    <option value='{{ $storage->storage_cd }}' @if($storage->default_yn == "Y") selected @endif>{{ $storage->storage_nm }} @if($storage->default_yn == "Y") (대표) @endif </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span> --}}
                        <div class="d-flex mr-1 mb-1 mb-lg-0">
                            <span class="mr-1">출고예정일</span>
                            <div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $edate }}" autocomplete="off" readonly />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <select id='rel_order' name='rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
                                @foreach ($rel_orders as $rel_order)
                                    <option value='{{ $rel_order->code_id }}'>{{ $rel_order->code_val }}</option>
                                @endforeach
                            </select>
                            <a href="#" onclick="requestRelease();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>출고요청</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <script language="javascript">
        let columns = [
            {field: "store_nm" , headerName: "매장", rowGroup: true, hide: true},
            // {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
            {field: "prd_cd", headerName: "상품코드", width: 120, cellStyle: {"text-align": "center"}, checkboxSelection: true},
            {field: "goods_no", headerName: "상품번호", cellStyle: {"text-align": "center"}},
            {field: "goods_type_nm", headerName: "상품구분", cellStyle: StyleGoodsType},
            {field: "opt_kind_nm", headerName: "품목", width: 100, cellStyle: {"text-align": "center"}},
            {field: "brand_nm", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
            {field: "style_no",	headerName: "스타일넘버", cellStyle: {"text-align": "center"}},
            {field: "sale_stat_cl", headerName: "상품상태", cellStyle: StyleGoodsState},
            {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 350},
            {field: "goods_opt", headerName: "옵션", width: 300},
            {
                headerName: '(대표)창고재고', // 대표창고의 재고를 조회
                children: [
                    {field: "storage_qty", headerName: "재고", type: 'currencyType'},
                    {field: "storage_wqty", headerName: "보유재고", type: 'currencyType'},
                ]
            },
            {
                headerName: '판매',
                children: [
                    {field: "total_sale_cnt", headerName: "총판매수량", type: 'currencyType'},
                    {field: "sale_cnt", headerName: "판매수량", type: 'currencyType'},
                ]
            },
            {
                headerName: '매장재고',
                children: [
                    {field: "store_qty", headerName: "재고", type: 'currencyType'},
                    {field: "store_wqty", headerName: "보유재고", type: 'currencyType'},
                    {field: "exp_soldout_day", headerName: "소진예상일", cellStyle: {"text-align": "center"}},
                    {
                        field: "rel_qty",
                        headerName: "배분수량",
                        type: "currencyType",
                        width: 80,
                        editable: true,
                        cellStyle: function(params) {return params.value ? {"background-color": "#ffff99"} : {}},
                        valueFormatter: formatNumber,
                    }
                ]
            },
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        let gx;
        const pApp = new App('', { gridId: "#div-gd" });

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns, {
                autoGroupColumnDef: {
                    headerName: '매장',
                    headerClass: 'bizest',
                    minWidth: 230,
                    cellRenderer: 'agGroupCellRenderer',
                },
                groupDefaultExpanded: 1,
                rowSelection: 'multiple',
                groupSelectsChildren: true,
                suppressRowClickSelection: true,
                suppressAggFuncInHeader: true,
                onCellValueChanged: (e) => {
                    e.node.setSelected(true);
                    if (e.column.colId === "rel_qty") {
                        if (isNaN(e.newValue) == true || e.newValue == "") {
                            alert("숫자만 입력가능합니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        }
                    }
                }
            });
            gx.gridOptions.defaultColDef = {
                suppressMenu: true,
                resizable: false,
                sortable: true,
            };

            // 매장검색
            $( ".sch-store" ).on("click", function() {
                searchStore.Open(null, true);
            });
        });

        // 검색버튼 클릭 시
        function Search() {
            let store_type = $("[name=store_type]").val();
            let store_nos = $("[name=store_no]").val();

            let url = '/store/api/stores/search-storenm-from-type';
            let data = {store_type: store_type};
            if(store_nos.length > 0) {
                url = '/store/api/stores/search-storenm';
                data = {store_cds: store_nos};
            }
            
            if(store_nos.length < 1 && store_type === '') {
                SearchConnect();
            } else {
                axios({
                    url: url,
                    method: 'post',
                    data: data,
                }).then(function (res) {
                    if(res.data.code === 200) {
                        if(store_nos.length > 0 && store_type !== '') {
                            // 매장구분, 매장명 둘 다 선택한 경우
                            axios({
                                url: '/store/api/stores/search-storenm-from-type',
                                method: 'post',
                                data: {store_type: store_type},
                            }).then(function (response) {
                                if(response.data.code === 200) {
                                    let arr = res.data.body.filter(r => response.data.body.find(f => f.store_cd === r.store_cd));
                                    SearchConnect(arr.map(r => r.store_cd));
                                }
                            }).catch(function (error) {
                                console.log(error);
                            });
                        } else {
                            SearchConnect(res.data.body.map(r => r.store_cd));
                        }
                    }
                }).catch(function (err) {
                    console.log(err);
                });
            }
        }

        // 검색연결
        function SearchConnect(store_cds = []) {
            let d = $('form[name="search"]').serialize();
            d += "&store_nos=" + store_cds;
            gx.Request('/store/stock/stk13/search', d, -1);
        }

        // 출고요청
        function requestRelease() {
            let rows = gx.getSelectedRows();
            if(rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");

            let empty_rows = rows.filter(r => {
                if(!r.rel_qty || !r.rel_qty.trim() || r.rel_qty == 0 || isNaN(parseInt(r.rel_qty))) return true;
                return false;
            });
            if(empty_rows.length > 0) return alert("선택한 상품의 배분수량을 모두 입력해주세요.");

            let over_qty_rows = rows.filter(r => {
                if(r.rel_qty > (r.storage_wqty || 0)) return true;
                return false;
            });
            if(over_qty_rows.length > 0) return alert(`대표창고의 재고보다 많은 수량을 요청하실 수 없습니다.\n상품코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);

            if(!confirm("해당 상품을 출고요청하시겠습니까?")) return;

            const data = {
                products: rows,
                exp_dlv_day: $('[name=exp_dlv_day]').val(),
                rel_order: $('[name=rel_order]').val(),
            };

            axios({
                url: '/store/stock/stk13/request-release',
                method: 'post',
                data: data,
            }).then(function (res) {
                if(res.data.code === 200) {
                    if(!confirm(res.data.msg + "\n출고요청을 계속하시겠습니까?")) {
                        location.href = "/store/stock/stk10";
                    } else {
                        Search();
                    }
                } else {
                    console.log(res.data);
                    alert("출고요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                }
            }).catch(function (err) {
                console.log(err);
            });
        }

    </script>
@stop
