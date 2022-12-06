@extends('store_with.layouts.layout-nav')
@section('title','그룹관리')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">그룹관리</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 알림</span>
                </div>
            </div>
            <div style="float:right;">
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="openGroupAdd();"> 그룹추가</button>
                <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="window.close()"> 닫기</button>
            </div>
        </div>
        <form method="post" name="add_group">
            <div class="row show_layout">
                <div class="col-sm-5 pr-1">
                    <div class="card shadow-none mb-0">
                        <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                            <h5 class="m-0">그룹</h5>
                        </div>
                        
                        <div class="card-body shadow pt-2">
                        <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 pr-1">
                    <div class="card shadow-none mb-0">
                        <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                            <h5 class="m-0">매장</h5>
                        </div>
                        
                        <div class="card-body shadow pt-2">
                        <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-store-total" class="text-primary">0</span> 건</h6>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-store" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script language="javascript">
        let columns = [
            {headerName: "그룹명", field: "group_nm",width:120,
                cellRenderer: function(params) {
                    return `<a href='javascript:void(0)' onclick='SearchDetail("${params.value}", "${params.data.group_nm}", "${params.data.group_cd}")'>${params.value}</a>`;
                }
            },
            {headerName: "삭제", width: 60, cellStyle: {'text-align':'center'},
                cellRenderer: function(params) {
                    return `<a href='#' onclick="del_group_data('${params.data.group_cd}')">삭제</a>`;
                },
            },
            {headerName: "인덱스", field: "group_cd", hide: true},
            {width: "auto"}
        ];

        let group_columns = [
            {headerName: "매장코드", field: "store_cd", width: 70, cellStyle: {'text-align':'center'}},
            {headerName: "매장명", field: "store_nm",width:"auto"},
            {headerName: "삭제", width:60, cellStyle: {'text-align':'center'},
                cellRenderer: function(params) {
                    return `<a href='#' onclick="del_store_data('${params.data.store_cd}')">삭제</a>`;
                },
            },
            {headerName: "인덱스", field: "group_cd",hide:true},
            {width: "auto"},
        ];


        let gx, gx2;
        let del_GroupData = [];
        let del_StoreData = [];
        let cur_group_cd = "";
        let newData = [];

        const pApp = new App('', { gridId: "#div-gd" });
        const pApp2 = new App('', { gridId: "#div-gd-store" });

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            gx.Request('/store/stock/stk32/search_group');

            pApp2.ResizeGrid(275);
            pApp2.BindSearchEnter();
            let gridDiv2 = document.querySelector(pApp2.options.gridId);
            gx2 = new HDGrid(gridDiv2, group_columns, {
                onCellValueChanged: (e) => {
                e.node.setSelected(true);
                
            }
            });
            gx2.Request('/store/stock/stk32/search_group2');

        });
        $(document).ready(function() {
            $('#saveData').show();
            $('#modData').hide();
        })
        function SearchDetail(group_nm, store_cd, group_cd) {
            $('#saveData').hide();
            $('#modData').show();
            // delData = "";
            if(store_cd === '') return;
            console.log(group_nm);
        
            $('#group_nm').val(group_nm);
            cur_group_nm = group_nm;
            cur_store_cd = store_cd;
            cur_group_cd = group_cd;

            gx2.Request('/store/stock/stk32/search_group2','group_cd='+ group_cd);
        }

       $(document).ready(function() {
            $('#add_group_data').click(function() {
                $('#saveData').show();
                $('#modData').hide();
                document.getElementById('group_nm').value ='';
                gx2.gridOptions.api.setRowData([]);
            });
            
        });

        //그룹 추가 팝업
        function openGroupAdd() {
            const url = '/store/stock/stk32/addGroup';
            const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=420");
        }
    </script>
@stop
