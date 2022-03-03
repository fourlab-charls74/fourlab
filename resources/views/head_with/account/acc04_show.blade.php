@extends('head_with.layouts.layout-nav')
@section('title','정산지급 및 계산서 - 다운로드')
@section('content')

<form action="get" name="search">
    <input type="hidden" name="data" value="{{$data}}">
    <div class="container-fluid show_layout py-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">정산지급 및 계산서</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 입점&정산</span>
                    <span>/ 정산지급 및 계산서</span>
                    <span>/ 세금계산서 다운로드</span>
                </div>
            </div>
        </div>
        <div class="card_wrap mb-3">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <div class="card-header mb-0">
                        <a href="#" class="m-0 font-weight-bold">정산지급 및 계산서</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th>발행일자</th>
                                            <td>
                                                <div class="flex_box" style="margin-top: -3px; margin-left: -8px;">
                                                    <div class="form-control-sm">
                                                        <div class="docs-datepicker form-inline-inner input_box mr-2" style="width: 120px">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="date" value="{{ $date }}" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>종류</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="type" id="type_1" class="custom-control-input" value="01" checked/>
                                                        <label class="custom-control-label" for="type_1">일반</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="type" id="type_2" class="custom-control-input" value="02"/>
                                                        <label class="custom-control-label" for="type_2">영세율</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>영수 / 청구</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="t2" id="t2_1" class="custom-control-input" value="01" checked/>
                                                        <label class="custom-control-label" for="t2_1">영수</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="t2" id="t2_2" class="custom-control-input" value="02"/>
                                                        <label class="custom-control-label" for="t2_2">청구</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>사업자번호</th>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="biz_rp" id="biz_rp" class="custom-control-input" value="N"/>
                                                    <label class="custom-control-label" for="biz_rp">구분자('-') 여부</label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="flex_box mt-3">
                            <a href="#" onclick="taxSheetDownload();" class="btn-sm btn-primary shadow-sm" style="margin: 0 auto; font-size: 0.9rem">세금계산서 다운로드</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive d-none">
        <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
    </div>

</form>

<script type="text/javascript" charset="utf-8">

    const URL = {
        SEARCH: '/head/account/acc04/show/search'
    };
    
    // ag-grid set field
    var columns = [];

    // logics

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        // pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {};
        gx = new HDGrid(gridDiv, columns, options);
    });
    
    const initTopRowData = () => {
        if (gx.getRows().length > 0) {
            let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            gx.gridOptions.api.setPinnedTopRowData([
                { ...pinnedRow.data, type: '합계' }
            ]);
        }
    };

    const taxSheetDownload = async () => {
        let data = $('form[name="search"]').serialize();
        try {
            const response = await axios({
                url: `${URL.SEARCH}?${data}`,
                method: 'get'
            });
            const { headers, list } = response.data;
            await initAgGrid(headers, list);
            gx.Download();
        } catch (error) {
            console.log(error);
        }
        
    };

    const initAgGrid = async (headers = [], list = []) => {

        const colDefs = [];
        gx.gridOptions.api.setRowData([]);
        headers.map((header, index) => {
            colDefs.push({field : String(index), headerName: header});
        });
        
        await gx.gridOptions.api.setColumnDefs(colDefs);
        await gx.gridOptions.api.setRowData(list); // add the data to the grid

    };

</script>

@stop