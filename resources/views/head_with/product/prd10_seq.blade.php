@extends('head_with.layouts.layout-nav')
@section('title', '상품전시 순서변경')
@section('content')

<style>
    .table th {min-width: 120px;}
    .table td {width: 40%;}

    @media (max-width: 740px) {
        .table td {float: unset !important;width: 100% !important;}
    }
</style>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">상품전시 순서변경</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품전시 - {{ @$d_cat_nm }}</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0);" onclick="return Save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            <a href="javascript:void(0);" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow mt-3" id="basic_info_form">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="javascript:void(0);">검색</a>
                <div class="fr_box">
                    <button type="button" onclick="return Search();" class="btn btn-primary"><i class="fas fa-search fa-sm mr-1"></i> 조회</button>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-cog fa-sm mr-1"></i> 고급</button>
                </div>
            </div>
            <div class="card-body">
                <form name="search">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th>페이지</th>
                                            <td>
                                                <div class="form-inline flex_box">
                                                    <select name='page_h' class="form-control form-control-sm" style="width:60px;">
                                                        <option value='5'>5</option>
                                                        <option value='6'>6</option>
                                                        <option value='7'>7</option>
                                                        <option value='8'>8</option>
                                                        <option value='9' selected>9</option>
                                                        <option value='10'>10</option>
                                                        <option value='12'>12</option>
                                                        <option value='15'>15</option>
                                                        <option value='20'>20</option>
                                                        <option value='25'>25</option>
                                                        <option value='30'>30</option>
                                                        <option value='35'>35</option>
                                                        <option value='40'>40</option>
                                                        <option value='45'>45</option>
                                                        <option value='50'>50</option>
                                                    </select>
                                                    <span class="ml-2 mr-2">x</span>
                                                    <select name='page_v' class="form-control form-control-sm" style="width:60px;">
                                                        <option value='4'>4</option>
                                                        <option value='5' selected>5</option>
                                                        <option value='6'>6</option>
                                                        <option value='7'>7</option>
                                                        <option value='8'>8</option>
                                                        <option value='9'>9</option>
                                                        <option value='10'>10</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <th>이미지출력</th>
                                            <td>
                                                <div class="form-inline flex_box">
                                                    <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                                        <input type="checkbox" class="custom-control-input" name="goods_img" id="goods_img" value="Y" checked>
                                                        <label class="custom-control-label font-weight-light" for="goods_img">이미지출력</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow mt-3 pb-3">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <div class="fl_box">
                    <h6 class="m-0 fs-16 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-arrows-alt-v fa-sm mr-1"></i> 이동</button>
                    <span class="ml-2 mr-2">|</span>
                    <button type="button" onclick="return moveRows('up');" class="btn btn-outline-primary"><i class="fas fa-long-arrow-alt-up fa-sm mr-1"></i> 위</button>
                    <button type="button" onclick="return moveRows('down');" class="btn btn-outline-primary"><i class="fas fa-long-arrow-alt-down fa-sm mr-1"></i> 아래</button>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-long-arrow-alt-up fa-sm mr-1"></i> 처음</button>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-long-arrow-alt-down fa-sm mr-1"></i> 끝</button>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-long-arrow-alt-up fa-sm mr-1"></i> 페이지처음</button>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-long-arrow-alt-down fa-sm mr-1"></i> 페이지끝</button>
                    <span class="ml-2 mr-2">|</span>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-undo fa-sm mr-1"></i> 초기화</button>
                    <button type="button" onclick="" class="btn btn-outline-primary"><i class="fas fa-magic fa-sm mr-1"></i> 자동정렬</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ag-cell-focus, .ag-cell {
        border: none !important;
        background-color: transparent !important;
    }
</style>

<script type="text/javascript" charset="utf-8">
    const LINE_HEIGHT = { 'line-height': '30px' };
    const CENTER = { 'text-align': 'center', 'line-height': '30px' };
    const SALE_STATE = {
        판매중지: "#808080",
        등록대기중: "#669900",
        판매대기중: "#000000",
        임시저장: "#000000",
        판매중: "#0000ff",
        "품절[수동]": "#ff0000",
        품절: "#AAAAAA",
        휴지통: "#AAAAAA",
    };

    const columns = [
        { headerName: "No", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 38, cellStyle: { ...CENTER, 'background-color': '#f2f2f2' } },
        { headerName: '페이지',
            children: [
                // 상품이 전시되는 페이지
                { field: 'page1', headerName: '', width: 30, cellStyle: CENTER,
                    cellRenderer: (params) => {
                        return Math.ceil((params.rowIndex + 1) / (params.data.page_h * params.data.page_v));
                    }
                },
                // 해당페이지에서의 상품 순서
                { field: 'page2', headerName: '', width: 30, cellStyle: CENTER,
                    cellRenderer: (params) => {
                        return (params.rowIndex) % (params.data.page_h * params.data.page_v) + 1;
                    }
                },
                // 해당페이지 해당열에서의 상품 순서
                { field: 'page3', headerName: '', width: 30, cellStyle: CENTER,
                    cellRenderer: (params) => {
                        return (params.rowIndex) % (params.data.page_v) + 1;
                    }
                },
            ],
        },
        {{--{ field: "img", headerName: "이미지", type: 'GoodsImageType', width: 48, surl: "{{ config('shop.front_url') }}" },--}}
        { field: "img", headerName: "이미지", width: 48 },
        { field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: CENTER },
        { field: "style_no", headerName: "스타일넘버", width: 90, cellStyle: CENTER },
        { field: "goods_nm", headerName: "상품명", width: 235, cellStyle: LINE_HEIGHT },
        { field: "sale_stat_cl", headerName: "상품상태", width: 70,
            cellStyle: function(params) {
                if (params.value !== undefined && SALE_STATE[params.value]) {
                    return { ...CENTER, color: SALE_STATE[params.value] };
                }
            }
        },
        { field: "price", headerName: "판매가", width: 70, type: 'currencyType', cellStyle: LINE_HEIGHT },
        { field: "disp_yn", headerName: '전시상태', width: 60,
            cellRenderer: (params) => params.value === 'Y' ? '활성' : '비활성',
            cellStyle: (params) => ({ ...CENTER, 'color':params.value === 'Y' ? '#4444ff' : '#666666' }),
        },
        { field: "qty", headerName: "재고수", type: 'numberType', width: 50 },
        { field: "ord_qty", headerName: "주문수", type: 'numberType', width: 50 },
        { field: "seq", headerName: "순위", type: 'numberType', width: 50 },
        { field: "reg_dm", headerName: "등록일시", width: 80, cellStyle: CENTER, cellRenderer: (params) => params.value?.substring(0, 10) },
        { field: "upd_dm", headerName: "수정일시", width: 80, cellStyle: CENTER, cellRenderer: (params) => params.value?.substring(0, 10) },
        { field: "spoint", headerName: "정렬점수", width: 80, type: 'numberType' },
        { width: "auto" },
    ];

    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(325);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            suppressRowClickSelection: false,
            // onRangeSelectionChanged: (e) => {
                // console.log(e.api);
                // 셀 드래그해서 row 선택하는 기능 개발필요
            // }
            // onRowSelected: (params) => {
            //     if (params.node.selected) console.log("rowindex", params.rowIndex);
            // }
        });

        Search();

        $("#goods_img").on('click', function(e) {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#goods_img").is(":checked"));
        });
    });

    function Search() {
        const d_cat_cd = "{{ @$d_cat_cd }}";
        const data = $("form[name='search']").serialize();
        gx.Request('/head/product/prd10/' + d_cat_cd + '/search-seq', data, -1, function (e) {
            // console.log(e);
        });
    }

    function moveRows(direction = '') {
        const nodes = gx.gridOptions.api.getSelectedNodes();
        rows = nodes.sort((a,b) => a.rowIndex - b.rowIndex);

        if (rows.length > 0) {
            if (direction === 'up' && rows[0].rowIndex <= 0) return false;
            if (direction === 'down' && rows[rows.length - 1].rowIndex > rows[rows.length - 1].gridApi.getDisplayedRowCount() - 2) return false;

            gx.gridOptions.api.applyTransaction({ remove: rows.map(row => row.data) });
            let row_top = Math.min(...rows.map(row => row.rowIndex)) + (direction === 'up' ? -1 : 1);
            const result = gx.gridOptions.api.applyTransaction({
                add: rows.map(row => row.data),
                addIndex: row_top,
            });
            result.add.forEach(r => r.setSelected(true));

            if (direction === 'up') {
                gx.gridOptions.api.ensureIndexVisible(row_top - 2, 'top');
            } else if (direction === 'down') {
                gx.gridOptions.api.ensureIndexVisible(row_top + rows.length - 1, 'bottom');
            }

            rows = [];
            gx.gridOptions.api.forEachNode((node) => {
                if (node.alreadyRendered) rows.push(node);
            });
            gx.gridOptions.api.redrawRows({ rowNodes: rows });
        }
    }
</script>
@stop
