@extends('store_with.layouts.layout-nav')
@section('title', '그룹 추가')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">그룹추가</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 알림</span>
                </div>
            </div>
        </div>
        <form name="detail">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">그룹상세</a>
                    </div>
                    <div class="card-body mt-1">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="94px">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>그룹명</th>
                                                <td>
                                                    <div class="input_box">
                                                        <input type='text' class="form-control form-control-sm search-all" name='group_nm' id='group_nm' value=''>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>매장명</th>
                                                <td>
                                                    <div class="form-inline inline_btn_box" style="display:inline-block; width:100%">
                                                        <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple ></select>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body shadow pt-2">
                    <div class="filter_wrap" style="margin:10px;">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="cntStore" class="text-primary">0</span> 건</h6>
                        </div>
                        <div style="float:right;">
                            <button type="button" onclick="addRow()" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" id="add_store"> 추가</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd-group" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>

        //매장 다중 선택
        $( ".sch-store" ).on("click", function() {
                    searchStore.Open(null, "multiple");
        });

        let group_columns = [
            {headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width:28, pinned:'left'},
            {headerName: "매장코드", field: "store_cd", width: 70, cellStyle: {'text-align':'center'}},
            {headerName: "매장명", field: "store_nm",width:"auto"},
            {headerName: "삭제", width:60, cellStyle: {'text-align':'center'},
                cellRenderer: function(params) {
                    return `<a href='#' onclick="del_store_data('${params.data.store_cd}')">삭제</a>`;
                },
            },
            {headerName: "그룹코드", field: "group_cd",hide:true},
            {width: "auto"},
        ];

        let gx;
        const pApp = new App('', { gridId: "#div-gd-group" });

        $(document).ready(function() {
            pApp.ResizeGrid(500);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, group_columns, {
                onCellValueChanged: (e) => {
                e.node.setSelected(true);
                
            }
            });
            gx.Request('/store/stock/stk32/search_group2');
        });

        //매장 선택 후 추가 클릭 시 그리드에 반영
        
        let newData = [];
        function addRow(){
            let rows = gx.getRowCount();

            console.log(rows);
            let store_cds = [];
            let store_nms = [];
            for (let sel_option of document.getElementById('store_no').options) {
                if (sel_option.selected) {
                    store_cds.push(sel_option.value);
                    store_nms.push(sel_option.innerText);
                }
            }
            
            for (let i = 0;i<store_cds.length;i++) {
                newData.push({
                    store_cd:store_cds[i],
                    store_nm: store_nms[i]
                })
            }

            gx.gridOptions.api.applyTransaction({add:newData});
            document.getElementById('cntStore').innerText = store_cds.length;
            $('#store_no').val(null).trigger('change');
       }


    </script>
@stop