@extends('store_with.layouts.layout')
@section('title','원부자재 요청분출고')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">원부자재 요청분출고</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>원부자재관리</span>
            <span>/ 원부자재출고</span>
            <span>/ 원부자재 요청분출고</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                        <a href="/store/stock/stk16" class="btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="fas fa-step-backward fa-sm"></i> 출고리스트</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="store_cd">요청매장</label>
                                <div class="form-inline inline_btn_box">
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="prd_cd">원부자재코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input-box w-100">
                                        <div class="form-inline inline_btn_box">
                                            <input type='text' id="prd_cd_sub" name='prd_cd_sub' class="form-control form-control-sm w-100 ac-style-no search-enter">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd_sub"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="prd_nm">원부자재명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-enter" name='prd_nm' id="prd_nm" value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="type">구분</label>
                                <div class="flax_box">
                                    <select name='type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($types as $type)
                                        <option value='{{ $type->code_id }}'>{{ $type->code_id }} : {{ $type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="opt">품목</label>
                                <div class="flax_box">
                                    <select name='opt' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($opts as $opt)
                                        <option value='{{ $opt->code_id }}'>{{ $opt->code_id }} : {{ $opt->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                            <option value="5000">5000</option>
                                            <option value="10000">10000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="p.prd_cd">바코드</option>
                                            <option value="p.price">현재가</option>
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
                <div class="filter_wrap d-flex justify-content-between">
                    <div class="d-flex">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
                    </div>
                    <div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
                        <div class="d-flex">
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="ext_storage_qty" id="ext_storage_qty" value="Y" checked>
                                <label class="custom-control-label font-weight-normal" for="ext_storage_qty">창고재고 0 제외</label>
                            </div>
                        </div>
                        <span class="d-none d-lg-block mr-2 tex-secondary">|</span>
                        <div class="d-flex">
                            <a href="javascript:requestRelease();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-check fa-sm text-white-50 mr-2"></i>출고요청</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript" charset="utf-8">
        let columns = [
            {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 29, sort: null},
            {field: "type_nm", headerName: "구분", width: 60, cellClass: 'hd-grid-code'},
            {field: "opt", headerName: "품목", width: 80, cellClass: 'hd-grid-code'},
            {field: "img", headerName: "이미지", type: 'GoodsImageType', width: 50, surl: "{{config('shop.front_url')}}"},
            {field: "img", headerName: "이미지_url", hide: true},
            {field: "prd_nm", headerName: "원부자재명", width: 200},
            {field: "prd_cd", headerName: "바코드", width: 130,
                cellRenderer: function(params) {
                    if (params.value !== undefined) {
                        return '<a href="#" onclick="return EditProduct(\'' + params.value + '\');">' + params.value + '</a>';
                    }
                }
            },
            {field: "color", headerName: "컬러명", width: 100},
            {field: "size", headerName: "사이즈명", width: 100},
            {field: "unit", headerName: "단위", width: 120},
            {field: "price", headerName: "현재가", type: 'currencyType', width: 80},
            {field: "wonga", headerName: "원가", type: 'currencyType', width: 80, hide: true},
            {field: 'storage_wqty', headerName: '창고재고',
                children: [
                    {field: 'storage_qty', headerName: '재고', type: "currencyType", width: 60,
                        cellRenderer: function(params) {
                            if (params.value !== undefined) {
                                return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                            }
                    }},
                    {field: 'storage_wqty', headerName: '보유재고', type: "currencyType", width: 80,
                        cellRenderer: function(params) {
                            if (params.value !== undefined) {
                                return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                            }
                    }},
                ],
            },
            {field: 'store_info', headerName: '', hide: true,
                children: [
                    {field: 'rel_qty', headerName: '요청수량', type: "currencyType", hide: true, width: 100, editable: true, 
	                    cellClass: ['hd-grid-edit', 'hd-grid-number'], 
	                    valueFormatter: formatNumber
                    },
                ],
            },
            {field: "amount", headerName: "합계", type: 'currencyType', width: 100, valueGetter: (params) => calAmount(params)},
            {field: "req_comment", headerName: "매장메모", width: 305, editable: true, cellClass: 'hd-grid-edit'},
            {width: "auto"}
        ];

        let gx;
        const pApp = new App('', { gridId: "#div-gd", height: 265 });

        $(document).ready(function() {
            pApp.ResizeGrid(265);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns, {
				defaultColDef: {
					suppressMenu: true,
					resizable: true,
					autoHeight: true,
					sortable: true,
				},
                onCellValueChanged: (e) => {
                    e.node.setSelected(true);
                    if (e.column.colId == "rel_qty") {
                        if (isNaN(e.newValue) == true || e.newValue == "") {
                            alert("숫자만 입력가능합니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        }
                    }
                }
            });
        });

		const calAmount = (params) => {
			const row = params.data;
			const result = parseInt(row.price) * parseInt(row.rel_qty);
			return isNaN(result) ? 0 : result;
		};

		function setColumn(store) {
			if (!store) return;

			columns[13].headerName = store.store_nm;
			gx.gridOptions.api.setColumnDefs(columns);

			gx.gridOptions.columnApi.applyColumnState({
				state: [
					{colId: 'store_info', hide: false},
					{colId: 'store_qty', hide: false},
					{colId: 'store_wqty', hide: false},
					{colId: 'rel_qty', hide: false},
				],
			});
		}

        function EditProduct(product_code) {
			var url = '/store/product/prd03/edit/' + product_code;
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=555");
		}

        function Search() {
            if (!$("[name=store_no]").val()) {
                alert("요청매장을 선택 후 검색해주세요.");
                $('.sch-store').click();
                return false;
            }
            let data = $('form[name="search"]').serialize();
            data += "&ext_storage_qty=" + $("[name=ext_storage_qty]").is(":checked");

            axios({
                url: '/store/api/stores/search-storenm',
                method: 'post',
                data: {store_cds: [$("[name=store_no]").val()]},
            }).then(function (res) {
                if (res.data.code === 200) {
                    setColumn(res.data.body[0]);
                    gx.Request('/store/stock/stk17/search', data, 1);
                } else {
                    // console.log(res.data);
                }
            }).catch(function (err) {
                console.log(err);
            });
        }

        // 출고요청
        function requestRelease() {
            let rows = gx.getSelectedRows();
            if (rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");
            if (rows.filter(r => !r.rel_qty || !r.rel_qty.trim() || r.rel_qty == 0 || isNaN(parseInt(r.rel_qty))).length > 0) return alert("선택한 상품의 요청수량을 입력해주세요.");

            let over_qty_rows = rows.filter(row => {
                if (row.storage_wqty !== null) {
                    if (row.storage_wqty < parseInt(row.rel_qty)) return true;
                    else return false;
                }
                return true; // 상품재고가 없는경우
            });
            if (over_qty_rows.length > 0) return alert(`창고의 보유재고보다 많은 수량을 요청하실 수 없습니다.\n바코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);

            if (!confirm("해당 상품을 출고요청하시겠습니까?")) return;

            let store_cd = $("[name=store_no]").val();

            const data = {
                products: rows, 
                store_cd,
                // exp_dlv_day: $('[name=exp_dlv_day]').val(),
                // rel_order: $('[name=rel_order]').val(),
            };

            axios({
                url: '/store/stock/stk17/request-release',
                method: 'post',
                data: data,
            }).then(function (res) {
                if (res.data.code === 200) {
                    if (!confirm("요청분출고 요청이 정상적으로 등록되었습니다." + "\n출고요청을 계속하시겠습니까?")) {
                        location.href = "/store/stock/stk16";
                    } else {
                        Search();
                    }
                } else {
                    // console.log(res.data);
                    alert("출고요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                }
            }).catch(function (err) {
                // console.log(err);
            });
        }
    </script>
@stop
