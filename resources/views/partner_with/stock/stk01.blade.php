@extends('partner_with.layouts.layout')
@section('title','재고')
@section('content')

<style>
    input[type="text"]::placeholder {
        color: #aaa;
        text-align: right;
    }
	.ag-row-level-0 {
		background-color: #ededed !important;
	}
</style>

    <div class="page_tit">
        <h3 class="d-inline-flex">재고</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 재고</span>
        </div>
    </div>

    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
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
                                <label for="">상품상태</label>
                                <div class="flax_box">
                                    <select name='goods_stat' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_stats as $goods_stat)
                                            <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id="goods_nm" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">재고구분</label>
                                <div class="flax_box">
                                    <select name='is_unlimited' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($is_unlimiteds as $is_unlimited)
                                            <option value='{{ $is_unlimited->code_id }}'>{{ $is_unlimited->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">보유재고수</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm" name='wqty_l' value='' placeholder="이상">
                                        </div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-enter" name='wqty_h' value='' placeholder="이하">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">온라인재고수</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm" name='qty_l' value='' placeholder="이상">
                                        </div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm search-enter" name='qty_h' value='' placeholder="이하">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">품목</label>
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
	                    <div class="col-lg-4 inner-td">
		                    <div class="form-group">
			                    <label for="name">자료/정렬순서</label>
			                    <div class="form-inline">
				                    <div class="form-inline-inner input_box" style="width:24%;">
					                    <select name="limit" class="form-control form-control-sm">
						                    <option value="100">100</option>
						                    <option value="500">500</option>
						                    <option value="1000">1000</option>
						                    <option value="2000">2000</option>
						                    <option value="-1">모두</option>
					                    </select>
				                    </div>
				                    <span class="text_line">/</span>
				                    <div class="form-inline-inner input_box" style="width:45%;">
					                    <select name="ord_field" class="form-control form-control-sm">
						                    <option value="a.goods_no">상품코드</option>
						                    <option value="a.style_no">스타일넘버</option>
					                    </select>
				                    </div>
				                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
					                    <div class="btn-group" role="group">
						                    <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
						                    <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
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
                <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <form method="post" name="save" action="/parnter/stock/stk01">
        <div id="filter-area" class="card shadow-none ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box d-flex flex-column flex-sm-row justify-content-end">
							<div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
								<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
								<label class="custom-control-label font-weight-light" for="grid_expand">항목펼쳐보기</label>
							</div>
	                        <span class="d-none d-sm-inline-block mx-2 text-secondary">|</span>
	                        <div>
	                            <span class="mr-1">재고조정</span>
	                            <select id='reason' name='reason' class="form-control form-control-sm"  style='width:160px;display:inline'>
	                                <option value=''>선택</option>
	                                @foreach ($alter_reasons as $alter_reason)
	                                    <option value='{{ $alter_reason->code_id }}'>{{ $alter_reason->code_val }}</option>
	                                @endforeach
	                            </select>
	                            <input type="hidden" name="data" id="data" value=""/>
	                            <a href="#" onclick="Save();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>저장</a>
	                        </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>

    <script language="javascript">
        function StyleChangeYN(params){
            if(params.value !== undefined){
                var chg_yn = params.data[params.colDef.field + '_chg_yn'];
                if(chg_yn !== undefined && chg_yn == 'Y'){
                    return {
                        color: 'red'
                    }
                }
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>

    <script language="javascript">

        const columns= [
            {field:"opt_kind_nm" ,headerName:"품목", pinned:'left', width: 80, cellClass: 'hd-grid-code', aggFunc: "first"},
            {field:"brand_nm" ,headerName:"브랜드", pinned:'left', width: 80, cellClass: 'hd-grid-code', aggFunc: "first"},
            {field:"goods_no" ,headerName:"상품코드", width:80, pinned:'left', rowGroup: true, hide: true},
            {field:"style_no" ,headerName:"스타일넘버" , width: 80, cellClass: 'hd-grid-code', pinned:'left', aggFunc: "first"},
            {field:"goods_type_nm",headerName:"상품구분", width: 70, cellStyle:StyleGoodsTypeNM, pinned:'left', aggFunc: "first"},
            {field:"is_unlimited_nm",headerName:"재고구분", width: 70, cellClass: 'hd-grid-code', pinned:'left', aggFunc: "first"},
            {field:"sale_stat_cl_nm" ,headerName:"상태", width: 80, cellStyle:StyleGoodsState, pinned:'left', aggFunc: "first"},
            {field:"wonga" ,headerName:"원가", width: 80, type: 'currencyType', aggFunc: "first"},
            {field:"goods_nm" ,headerName:"상품명", width: 400, 
                cellRenderer: function (params) {
                    if (params.data !== undefined) {
                        return '<a href="#" onclick="return openProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                    } else {
                        return '<a href="#" onclick="return openProduct(\'' + params.node.allLeafChildren[0].data.goods_no_hd + '\');">' + params.node.allLeafChildren[0].data.goods_nm + '</a>';
                    }
                }
            },
            {field:"goods_opt" ,headerName:"옵션", width: 200,
                checkboxSelection:function(params){ return (params.data !== undefined && params.data.is_unlimited != 'Y')? true:false; },
                cellRenderer: function(params) {
                    if (params.data !== undefined) {
                        return '<a href="#" onclick="return openStock(' + params.data.goods_no + ',\'' + params.value +'\');">' + params.value + '</a>';
                    }
                }
            },
            {field:"good_qty",headerName:"현재고수",hide:true},
            {field:"wqty",partererName:"현보유재고수",hide:true},
            {field:"edit_good_qty" ,headerName:"온라인재고",
                editable: function(params){ return (params.data !== undefined && params.data.is_unlimited != 'Y')? true:false; },
                cellClass:function(params){
                    return (params.data !== undefined && params.data.is_unlimited != 'Y')? ['hd-grid-number','hd-grid-edit']: ['hd-grid-number'];
                },
                cellStyle:StyleChangeYN, onCellValueChanged:EditQty, valueFormatter:formatNumber},
            {field:"edit_wqty" ,headerName:"보유재고",
                editable: function(params){ return (params.data !== undefined && params.data.is_unlimited != 'Y')? true:false; },
                cellClass:function(params){
                    return (params.data !== undefined && params.data.is_unlimited != 'Y')? ['hd-grid-number','hd-grid-edit']: ['hd-grid-number'];
                },
                cellStyle:StyleChangeYN, onCellValueChanged:WEditQty, valueFormatter:formatNumber},
            {field:"goods_no_hd",headerName:"goods_no",hide:true},
            {field:"goods_sub_hd",headerName:"goods_sub",hide:true},
            {field:"is_unlimited",headerName:"is_unlimited",hide:true},
            {width: "auto"}
        ];

        function EditQty(params){
            if (params.oldValue !== params.newValue) {
                params.data[params.colDef.field + '_chg_yn'] = 'Y';
                var rowNode = params.node;
                rowNode.setSelected(true);
                gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
                gx.setFocusedWorkingCell();
            }
        }

        function WEditQty(params){
            if (params.oldValue !== params.newValue) {
                params.data[params.colDef.field + '_chg_yn'] = 'Y';
                var rowNode = params.node;
                rowNode.setSelected(true);

                // 보유재고 수정시 온라인 재고도 자동 수정
                params.data.edit_good_qty = params.data.edit_good_qty  * 1 + (params.newValue - params.oldValue);
                gx.gridOptions.api.updateRowData({ update: [params.data]});

                gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
                gx.setFocusedWorkingCell();
            }
        }
    </script>
    <script type="text/javascript" charset="utf-8">
        const pApp = new App('', { gridId:"#div-gd" });
        let gx;

        const basic_autoGroupColumnDef = (headerName, width = 120) => ({
            headerName: headerName,
            headerClass: 'bizest',
            minWidth: width,
            maxWidth: width,
            cellRenderer: 'agGroupCellRenderer',
            pinned: 'left'
        });
        
        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns, 
                {
                    rollup: true,
                    autoGroupColumnDef: basic_autoGroupColumnDef('상품코드'),
                    groupDefaultExpanded: 0, // 0: close, 1: open
                    suppressAggFuncInHeader: true,
                    animateRows: true,
                    suppressDragLeaveHidesColumns: true,
                    suppressMakeColumnVisibleAfterUnGroup: true,
					isRowSelectable: (params) => {
						return !!params.data;
					},
                }
            );

            Search();
        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/stock/stk01/search', data, 1, function (e) {
				setAllRowGroupExpanded($("#grid_expand").is(":checked"));	
            });
        }
    </script>
    <script type="text/javascript" charset="utf-8">


        function Save(){
            if($("#reason option:selected").val() == ""){
                alert("재고조정 사유를 선택해 주십시오.");
                return false;
            } else {
                var data  = [];
                for(row = 0;row < gx.gridOptions.api.getDisplayedRowCount();row++){
                    var rowNode = gx.gridOptions.api.getDisplayedRowAtIndex(row);
                    if(rowNode.selected == true){
                        data.push(
                            {
                                'goods_no':rowNode.data.goods_no_hd,
                                'goods_opt':rowNode.data.goods_opt,
								'goods_sub':rowNode.data.goods_sub_hd,
                                'qty':rowNode.data.edit_good_qty,
                                'wqty':rowNode.data.edit_wqty
                            }
                        )
                        //data += rowNode.data.goods_no + '\t' + rowNode.data.goods_opt + '\t' + rowNode.data.edit_good_qty + '\t' + rowNode.data.edit_wqty + '\n';
                    }
                }
                if(data != ''){
                    var reason = $("#reason option:selected").val();
                    //var frm = $('form[name="save"]');
                    //frm.data.value = data;
                    //$('#data').val(JSON.stringify(data));
                    //console.log(frm.serialize());
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType:'json',
                        url: '/partner/stock/stk01',
                        data: {'reason':reason,'data':data},
                        success: function (res) {
                            //console.log(res);
                            //var res = jQuery.parseJSON(data);
                            if(res.code == '200'){
                                alert('재고수량을 저장하였습니다.');
								Search();
                            } else {
                                alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                                console.log(res);
                            }
                        },
                        error: function(e) {
                            console.log(e.responseText);
                        }
                    });
                }
            }
        }
    </script>


@stop
