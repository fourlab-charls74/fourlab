@extends('partner_with.layouts.layout')
@section('title','상품검색')
@section('content')

<div class="show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
        <h3 class="d-inline-flex">상품검색</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품관리</span>
            <span>/ 상품일괄수정</span>
            <span>/ 상품검색</span>
        </div>
        </div>
    </div>
    <form action="/partner/product/prd07/123441" method="post" name="search">
        <input type="hidden" name="data" value="">
        <input type="hidden" name="ismt" value="{{ $_GET['ismt'] }}">
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header">
                    <a href="#">상품검색</a>
                </div>
                <div class="fr_box flax_box" style="position: absolute; right: 2%;">
                    <a href="#" onclick="Search(1);" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <style>
                                        table{
                                                border: 1px solid #111111;
                                                text-align: center;
                                                border-collapse: collapse ;
                                            }
                                            thead{
                                                background-color: rgb(245, 245, 245);
                                            }
                                            th{
                                                padding: 5px !important;
                                            }
                                    </style>
                                    <table class="table incont table-bordered" id="dataTable">
                                        <colgroup>
                                            <col width="33%">
                                            <col width="33%">
                                            <col width="33%">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>상품상태</th>                                                
                                                <th>스타일넘버/상품코드</th>
                                                <th>상품명</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="select_box">
                                                        <select name='goods_stat' id="goods_stat" class="form-control form-control-sm">
                                                            <option value=''>전체</option>
                                                            @foreach ($goods_stats as $goods_stat)
                                                            <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
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
                                                </td>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm ac-goods-nm" name='goods_nm' value=''>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th>브랜드</th>
                                                <th>품목</th>
                                                <th>홍보글/단축명</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-inline inline_btn_box">
                                                        <input type="text" class="form-control form-control-sm search-all" name="brand" id="brand_nm" value="" style="width: 100%">
                                                        <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="select_box">
                                                        <select name="op_cd" id="op_cd" class="form-control form-control-sm search-all">
                                                            <option value="">선택하세요.</option>
                                                            @foreach($opt_cd_list as $opt_cd)
                                                                <option value="{{$opt_cd->name}}">{{$opt_cd->value}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-inline inline_input_box">
                                                        <div class="form-inline-inner text-box">
                                                            <div class="form-group">
                                                                <input type='text' class="form-control form-control-sm" name='head_desc' value='' style="width: 261px;">
                                                            </div>
                                                        </div>
                                                        <span class="text_line">/</span>
                                                        <div class="form-inline-inner text-box">
                                                            <div class="form-group">
                                                                <select name="head_desc_yn"  class="form-control form-control-sm" style="padding-right: 25px;">
                                                                    <option value="">전체</option>
                                                                    <option value="Y">Y</option>
                                                                    <option value="N">N</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th>이벤트 문구</th>
                                                <th>재고수량</th>
                                                <th>상품 이미지 출력</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm" name='ad_desc' value=''>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-inline inline_input_box">
                                                        <div class="form-inline-inner text-box">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control form-control-sm" name="f_qty" id="f_qty" value="" style="width: 60px;">
                                                            </div>
                                                        </div>
                                                        <span class="text_line">~</span>
                                                        <div class="form-inline-inner text-box">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control form-control-sm" name="t_qty" id="t_qty" value="" style="width: 60px;">
                                                            </div>
                                                        </div>
                                                        <span class="text_line">&nbsp;</span>
                                                        <div class="form-inline-inner text-box flax_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="change_category" name="onesize" value="Y">
                                                                <label class="custom-control-label" style="" for="change_category">원사이즈</label>
                                                            </div>
                                                            (옵션수&nbsp;<input type="text" class="form-control form-control-sm" style="width:40px; display:inline-block;" name="optcnt" value=""> 이상)
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flax_box txt_box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="show_img" name="show_img" onclick="GridImageShow()" value="Y" checked>
                                                            <label class="custom-control-label" style="" for="show_img">상품이미지보기</label>
                                                        </div>
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
                <input type="hidden" name="sdate" value=""> <!-- 등록일자 -->
                <input type="hidden" name="edate" value="">
                <input type="hidden" name="m_cat_nm" value=""> <!-- 대표카테고리 -->
                <input type="hidden" name="m_cat_cd" value="">
                <input type="hidden" name="f_price" value=""> <!-- 상품가격 -->
                <input type="hidden" name="t_price" value="">
                <input type="hidden" name="f_sqty" value=""> <!-- 최근판매수량 -->
                <input type="hidden" name="t_sqty" value="">
                <input type="hidden" name="limit" value="100"> <!-- 출력자료수 -->
                <input type="hidden" name="ord_field" value="goods_no"> <!-- 정렬순서 -->
                <input type="hidden" name="ord" value="desc"> <!-- 오름차순/내림차순 -->
            </div>
        </div>
    </form>
</div>
        
<div class="card shadow mb-3">
    <form method="post" name="save" id ="insert_form" action="/partner/stock/stk01">
        @csrf
        <textarea style="display:none" name="form_str" id="csvResult"></textarea>
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                        <input type="button" onclick="returnOpenRow();" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1 submit-btn" value="확인"/>
                        <input type="button" onclick="self.close();" class="btn btn-sm w-xs btn-secondary shadow-sm" value="닫기"/>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="width:100%;min-height:400px;" class="ag-theme-balham"></div>
            </div>
        </div>
    </form>
</div>

    <script>
        var pApp = new App('',{
            gridId:"#div-gd",
        });
        var gridDiv  = document.querySelector(pApp.options.gridId);

        $(document).ready(function() {
            new agGrid.Grid(gridDiv , gridOptions);
            gridOptions.api.setRowData([]);
            pApp.ResizeGrid(250);

            gridOptions.api.sizeColumnsToFit();
            const remInPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
            gridOptions.columnApi.getAllColumns().forEach(function (column) {
                if(column.colDef.width == undefined){
                    const hn = column.colDef.headerName;
                    const hnWidth = hn.length*2*remInPixel;

                    gridOptions.columnApi.setColumnWidth(column.colId,hnWidth);
                }
            });

            Search(1);
        });

        var gridOptions = {
            columnDefs: [
                // this row shows the row index, doesn't use any data from the row
                {
                    headerName: '',pinned:'left',
                    headerCheckboxSelection: true,
                    checkboxSelection: true,
                    width:30
                },
                {
                    headerName: '#',pinned:'left',
                    width:50,
                    maxWidth: 100,
                    // it is important to have node.id here, so that when the id changes (which happens
                    // when the row is loaded) then the cell is refreshed.
                    valueGetter: 'node.id',
                    cellRenderer: 'loadingRenderer',
                },
                {field:"goods_no",headerName:"상품코드", pinned:'left', width:80,
                    cellRenderer: function(params) {
                        if (params.value !== undefined) {
                            return params.data.goods_no +' ['+ params.data.goods_sub +']';
                        }
                    }
                },
                {field:"goods_sub", headerName: "goods_sub", hide:true},
                {field:"goods_type", headerName:"goods_type", hide:true},
                {field:"opt_kind_nm",headerName:"품목",pinned:'left', width:80 },
                {field:"opt_kind_cd", headerName :"opt_kind_cd",hide:true },
                {field:"brand_nm",headerName:"브랜드",pinned:'left', width:80,},
                {field:"brand", headerName :"brand",hide:true },
                {field:"special_yn", headerName:"상품구분", width:90,pinned:'left' },
                {field:"style_no",headerName:"스타일넘버", pinned:'left',width:100 },
                {field:"goods_img",headerName:"img2",hide:true},
                {field:"img_62" , headerName:"이미지", pinned:'left',
                    cellRenderer: function(params) {
                        if (params.value !== undefined && params.data.img_62 != "") {
                            return '<img src="{{config('shop.image_svr')}}' + params.data.img_62 + '"/>';
                        }
                    }
                },

                {field:"head_desc",headerName:"상단홍보글", pinned:'left', width:100},


                {field:"goods_nm" , headerName:"상품명",width:150,
                    cellRenderer: function(params) {
                        if (params.value !== undefined) {
                            return '<a href="#" onclick="return openProduct(\'' + params.data.goods_no +'\');">' + params.value + '</a>';

                        }
                    }
                },
                {field:"goods_nm_eng",headerName:"goods_nm_eng",hide:true},

                {field:"com_nm", headerName:"공급업체", width:90 },
                {field:"com_id", headerName:"com_id", hide:true},
                {field:"price", headerName:"판매가", width:80 },
                {field:"qty", headerName:"재고수", width:80 },
                {field:"sale_stat_cl", headerName:"상품상태", width:80 },



                {field:"regi_date", headerName:"등록일자", width:80 },
                {headerName:"선택", width:80,
                    cellRenderer: function(params) {
                        return '<a href="#" onclick="return addProductRows()">선택</a>';
                    }

                },

            ],
            defaultColDef: {
                // set every column width
                //flex: 1,
                // make every column editable
                editable: true,
                resizable: true,
                autoHeight: true,
                suppressSizeToFit: true,
                sortable:true,
                //minWidth:90,
                // make every column use 'text' filter by default
                //filter: 'agTextColumnFilter',
            },
            components: {
                loadingRenderer: function (params) {
                    if (params.value !== undefined) {
                        return params.node.rowIndex+1 ;
                    }
                },
            },
            rowData: [],
            rowSelection:'multiple',
            //rowDeselection: true,
            rowBuffer:100,
            //onBodyScroll:onscroll,
            debug:false,
            onRowEditingStarted: function (event) {
                console.log('never called - not doing row editing');
            },
            onRowEditingStopped: function (event) {
                console.log('never called - not doing row editing');
            },
            onCellEditingStarted: function (event) {
                console.log('cellEditingStarted');
            },
            onCellEditingStopped: function (event) {
                console.log('cellEditingStopped');
            },
            // onRowClicked: function(event){},

            // rowBuffer: 0,
            // suppressRowClickSelection: true,
            // // tell grid we want virtual row model type
            // //rowModelType: 'infinite',
            // // how big each page in our page cache will be, default is 100
            // paginationPageSize: 100,
            // // how many extra blank rows to display to the user at the end of the dataset,
            // // which sets the vertical scroll and then allows the grid to request viewing more rows of data.
            // // default is 1, ie show 1 row.
            // cacheOverflowSize: 2,
            // // how many server side requests to send at a time. if user is scrolling lots, then the requests
            // // are throttled down
            // maxConcurrentDatasourceRequests: 1,
            // // how many rows to initially show in the grid. having 1 shows a blank row, so it looks like
            // // the grid is loading from the users perspective (as we have a spinner in the first col)
            // infiniteInitialRowCount: 100,
            // // how many pages to store in cache. default is undefined, which allows an infinite sized cache,
            // // pages are never purged. this should be set for large data to stop your browser from getting
            // // full of data
            // maxBlocksInCache: 10,

            // debug: true,
            //Search(1);
        };

        var _isloading = false;

        function onscroll(params){
            if(_isloading === false && params.top > gridDiv.scrollHeight){

                var rowtotal = gridOptions.api.getDisplayedRowCount();

                if(gridOptions.api.getLastDisplayedRow() > 0 && gridOptions.api.getLastDisplayedRow() ==  rowtotal -1) {
                    Search(0);
                }
            }
        }


        var _page = 1;
        var _total = 0;
        var _grid_loading = false;
        var _code_items = "";
        var columns_arr = {};
        var setRowsData = [];

        function Search(page) {

            if(_grid_loading == false){

                _grid_loading = true;

                var frm = $('form');
                //var page_size = gridOptions.paginationPageSize;
                var rows = 0;

                if(page == 1){
                    _page = 1;
                } else {
                    _page = _page + 1;
                }
                console.log('page : ' + _page);
                console.log("serialize : "+frm.serialize());
                $.ajax({
                    async: true,
                    type: 'get',
                    url: '/partner/product/prd10/search',
                    data: frm.serialize() + '&page=' + _page,
                    success: function (data) {
                        //console.log(data);
                        var res = jQuery.parseJSON(data);
                        $("#gd-total").text(res.head.total);
                        if (_page == 1){
                            _total = res.head.total;
                            gridOptions.api.setRowData(res.body);
                            GridImageShow();
                            //console.log(_total);
                        } else {
                            var ret = gridOptions.api.applyTransaction({ add: res.body });
                            //console.log(ret);
                        }
                    },
                    complete:function(){
                        _grid_loading = false;
                    },
                    error: function(request, status, error) {
                        console.log("error")
                    }
                });
            }
        };

        function GridImageShow(){
            if($("#show_img").is(":checked")){
                gridOptions.columnApi.setColumnVisible('img_62', true);
            }else{
                gridOptions.columnApi.setColumnVisible('img_62', false);

            }
        }

        function addProductRows(){
            var focusedCellData = gridOptions.api.getFocusedCell();
            var getRowIndex = focusedCellData.rowIndex;
            var rowNode = gridOptions.api.getRowNode(getRowIndex);
            var data = [];
            data.push(rowNode.data.goods_no +"_"+ rowNode.data.goods_sub);
            getProductInfo(data);
        }

        function returnOpenRow(){
            var data = [];
            var selectedRowData = gridOptions.api.getSelectedRows();

            selectedRowData.forEach( function(selectedRowData, index) {
                if(selectedRowData.goods_no != ""){
                    data.push(selectedRowData.goods_no +"_"+ selectedRowData.goods_sub);
                }
            });

            if(data.length<1){
                alert("추가할 상품을 선택하세요.");
                return false;
            }
            getProductInfo(data);
        }

        function getProductInfo(obj){
            if(_grid_loading == false){

                _grid_loading = true;

                var frm = $('form');
                //var page_size = gridOptions.paginationPageSize;
                var rows = 0;

                $.ajax({
                    async: true,
                    type: 'get',
                    url: '/partner/product/prd07/search',
                    data: {
                      goods_nos : obj.join(',')
                    },
                    success: function (data) {
                        opener.parent.addProductRow(data.body);
                        self.close();
                    },
                    complete:function(){
                        _grid_loading = false;
                    },
                    error: function(request, status, error) {
                        console.log("error")
                    }
                });
            }
        }

        function PopSearchBrand(val){
            alert("개발예정입니다.");
            return false;
        }

    </script>
@stop
