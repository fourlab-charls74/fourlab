@extends('head_with.layouts.layout')
@section('title','재고입고알림')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">재고입고알림</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 재고입고알림</span>
        </div>
    </div>

    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품구분</label>
                                <div class="flex_box">
                                    <select name='goods_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_types as $goods_type)
                                            <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품상태</label>
                                <div class="flex_box">
                                    <select name='goods_stat' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_stats as $goods_stat)
                                            <option value='{{ $goods_stat->code_id }}' @if( $goods_stat->code_id == $default_goods_stat) selected @endif>{{ $goods_stat->code_val }}</option>
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
                    </div>
                    <div class="row">
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
                                <label>상단홍보글</label>
                                <div class="flex_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='head_desc' value=''>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">상품명</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm ac-goods-nm search-enter" name="goods_nm" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="restock_cnt">재입고요청</label>
                                <div class="form-inline">
                                    <span>미알림요청</span>　
                                    <div class="form-inline-inner input_box">
                                        <input id="restock_ncnt" type="number" class="form-control form-control-sm search-enter" name="restock_ncnt" min="0" value="1"/>
                                    </div>
                                    <span>　이상</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">출력자료수/정렬순서</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100" >100</option>
                                            <option value="500" >500</option>
                                            <option value="1000" >1000</option>
                                            <option value="2000" >2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="restock_cnt" selected>재고입고요청</option>
                                            <option value="restock_ncnt">미알림요청수</option>
                                            <option value="restock_ut" >최근요청일시</option>
                                            <option value="goods_no" >상품번호</option>
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
                <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <form method="post" name="save" action="/head/stock/stk06">
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">
                            <div class="fl_inner_box">
                                <div class="box">
                                    <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                        <input type="checkbox" class="custom-control-input" name="goods_img" id="goods_img" value="Y">
                                        <label class="custom-control-label font-weight-light" for="goods_img">이미지출력</label>
                                    </div>
                                </div>
                                <div class="box">
                                    <a href="#" onclick="Del()" class="btn-sm btn btn-primary">재입고요청삭제</a>
                                </div>
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
    <script type="text/javascript" charset="utf-8">

        // libs
        
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

        // ag-grid

        var columns= [
                {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left', sort: null},
                {field:"goods_no", headerName:"상품번호", width:58, cellStyle: {textAlign:"center"}, pinned:'left'},
                {field:"goods_type_nm", headerName:"상품구분", width:58, 
                    cellStyle: function (params) {
                        var state = {
                            "위탁판매":"#F90000",
                            "매입":"#009999",
                            "해외":"#0000FF",
                        }
                        if (params.value !== undefined) {
                            if (state[params.value]) {
                                return {
                                    color: state[params.value],
                                    textAlign: 'center'
                                }
                            }
                        }
                    }, 
                pinned:'left'},
                {field:"opt_kind_nm" , headerName:"품목", width:118, pinned:'left'},
                {field:"brand_nm" , headerName:"브랜드", pinned:'left', width: 90},
                {field:"style_no" , headerName:"스타일넘버", width:80, cellStyle:{'text-align':'center'}, pinned:'left'},
                {field:"head_desc", headerName:"상단홍보글", width:180},
                {field:"goods_img" , headerName:"이미지", width:46,
                    cellRenderer: function(params) {
                        if (params.value !== undefined && params.data.goods_img != "") {
                            return '<img src="{{config('shop.image_svr')}}' + params.data.goods_img + '" style="height:30px;"/>';
                        }
                    },
                    hide: true
                },
                {field:"goods_nm", headerName:"상품명", width:320, type:'HeadGoodsNameType'},
                {field:"sale_stat_cl_val", headerName: "상품상태", width: 58, type:'GoodsStateType'},
                {field:"goods_opt", headerName: "옵션", width: 150},
                {field:"good_qty",headerName:"온라인재고", width:70, type:'numberType'},
                {field:"wqty", headerName: "보유재고수", width:70, type:'numberType'},
                {field:"restock_cnt" ,headerName:"재입고요청", width:70, type:'numberType',
                    cellRenderer: function(params) {
                        if (params.value !== undefined && params.data.goods_no !== undefined) {
                            var a = document.createElement('a');
                            a.setAttribute('href', '#');
                            a.setAttribute('style', 'text-decoration: underline !important;')
                            a.setAttribute('data-state', 'Y'); // 상태 - 재입고 요청
                            a.setAttribute('data-no', params.data.goods_no);
                            a.innerText = params.value;
                            a.addEventListener('click', function(e) {
                                Click(e);
                            });
                            return a;
                        }
                    }
                },
                {field:"restock_ncnt" ,headerName:"미알림요청수", width:82, type:'numberType',
                    cellRenderer: function(params) {
                        if (params.value !== undefined && params.data.goods_no !== undefined) {
                            var a = document.createElement('a');
                            a.setAttribute('href', '#');
                            a.setAttribute('style', 'text-decoration: underline !important;')
                            a.setAttribute('data-state', 'N'); // 상태 - 미알림 요청
                            a.setAttribute('data-no', params.data.goods_no);
                            a.innerText = params.value;
                            a.addEventListener('click', function(e) {
                                Click(e);
                            });
                            return a;
                        }
                    }
                },
                {field:"goods_sh",headerName:"시중가격", width:60, type:'currencyType'},
                {field:"price" , headerName:"판매가", width:60, type: 'currencyType'},
                {field:"restock_ut" ,headerName:"최근요청일시", width:110},
                {field:"", headerName:"", width:"auto"}
            ];
    
        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;
        
        document.addEventListener('DOMContentLoaded', function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns);
            Search();

            $("#goods_img").click(function() {
                gx.gridOptions.columnApi.setColumnVisible("goods_img", $("#goods_img").is(":checked"));
            });
        });
        
        // logics

        var Search = function () {
            var data = $('form[name="search"]').serialize();
            gx.Aggregation({ "sum" : "top" });
            gx.Request('/head/stock/stk06/search', data, 1);
        };

        var Del = function () {            
            var arr;
            var goods_numbers = [];
            if (confirm("재입고요청을 삭제하시겠습니까?")) {
                var arr = gx.getSelectedRows();
                if (Array.isArray(arr) && !(arr.length > 0)) {
                    alert('상품을 선택 해 주십시오.')
                    return false;
                } else {
                    arr.map(function(obj, idx) {
                        obj.hasOwnProperty('goods_no')
                            ? goods_numbers[idx] = obj.goods_no
                            : null;
                    });
                    axios({
                        url: '/head/stock/stk06/delete',
                        method: 'delete',
                        data: { goods_numbers : goods_numbers }
                    }).then(function (response) {
                        console.log(response)
                        if (response.status == 200) Search();
                    }).catch(function (error) {
                        console.log(error.response.data);
                    });
                }
            };
        };

        var Click = function (e) {
            e.preventDefault();
            var data = e.target.dataset;
            if (data != undefined) {
                var state = data.state;
                var goods_no = data.no;
                var url = '/head/stock/stk06/restock?goods_no=' + goods_no + '&state=';
                switch (state) {
                    case "Y":
                        url += state;
                        break;
                    case "N":
                        url += state;
                        break;
                    default: 
                        return false;
                };
                var width = 1000;
		        var height = 800;
		        var pop = window.open(url,"_blank","toolbar=no,scrollbars=no,resizable=yes,status=yes,top=100,left=100,width="+width+",height="+height);
            }
        };

    </script>

@stop
