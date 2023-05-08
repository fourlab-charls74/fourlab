@extends('partner_skote.layouts.master-without-nav')
@section('title','상품일괄등록')
@section('content')
    <style>
    .ag-checkbox-input{
        width:80px;
    }
    .row{
        margin-bottom:20px;
    }
    .input_style{
        font-weight: 400;
        color: #6e707e;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #d1d3e2;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }

    </style>
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">상품 일괄 등록</h1>

        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form name="f1" id="f1">
                    @CSRF
                    <input type="hidden" id="com_type" name="com_type" value="{{$com_info->com_type}}"/>
                    <input type="hidden" id="com_nm" name="com_nm" value="{{$com_info->com_nm}}" />
                    <input type="hidden" id="com_id" name="com_id" value="{{$com_info->com_id}}" />
                    <input type="hidden" id="pay_fee" name="pay_fee" value="{{$com_info->pay_fee}}" />
                    <input type="hidden" id="margin_type" name="margin_type" value="{{$com_info->margin_type}}" />
                    <input type="hidden" id="md_nm" name="md_nm" value="{{$com_info->md_nm}}" />
                    <input type="hidden" id="md_id" name="md_id" value="{{$com_info->md_id}}" />
                    <input type="hidden" id="bansong_info" name="bansong_info" value="{{$com_info->baesong_info}}" />
                    <input type="hidden" id="baesong_kind" name="baesong_kind" value="{{$com_info->baesong_kind}}" />
                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">업체</div>
                        <div class="text-gray-800" >{{$com_info->com_nm}}</div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">상품수</div>
                        <div class="text-gray-800"  style=""><input type="text" name="prd_cnt" id="prd_cnt" class=" input_style form-control-sm search-all" style="width:100px;ime-mode:disabled;vertical-align:middle;"  />&nbsp;개&nbsp;</div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">품목</div>
                        <div class="text-gray-800" >
                            <select name="op_cd" id="op_cd" class=" input_style form-control-sm search-all">
                                <option value="">선택하세요.</option>
                                @foreach($opt_cd_list as $opt_cd)
                                    <option value="{{$opt_cd->name}}">{{$opt_cd->value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">브랜드</div>
                        <div class="mb-0 text-gray-800">
                            <input type='text' class=" input_style form-control-sm search-all" name='brand' id='brand_nm' value='' style='width:70%;'>
                            <a href="javascript:void(0);" class="d-none d-sm-inline-block btn btn-sm btn-secondary " onclick="search_brand();"id="btn_pop_brnad" data-toggle="modal" data-target="#brand_list_modal" >...</a>
                        </div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">대표카테고리</div>
                        <div class="text-gray-800" >
                            <input type="text" value="" name="rep_cat_nm" id="rep_cat_nm" class=" input_style form-control-sm search-all" style="width:80%"/>
                            <input type="hidden" value="" name="rep_cat_cd" id="rep_cat_cd"/>
                            <a href="javascript:void(0);" class="d-none d-sm-inline-block btn btn-sm btn-secondary " onclick="select_category('display');" id="btn_pop_category" data-toggle="modal" data-target="#category_list_modal" >...</a>
                        </div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">용도카테고리</div>
                        <div class="text-gray-800" >
                            <input type="text" value="" name="u_cat_nm" id="u_cat_nm" class=" input_style form-control-sm search-all" style="width:80%"/>
                            <input type="hidden" id="u_cat_cd" name="u_cat_cd" value=""/>
                            <a href="javascript:void(0);" class="d-none d-sm-inline-block btn btn-sm btn-secondary " onclick="select_category('item');" id="btn_pop_category" data-toggle="modal" data-target="#category_list_modal" >...</a>
                        </div>
                    </div>
                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">배송비</div>
                        <div class="text-gray-800"  style="">
                            <label><input type="radio" name="dlv_fee_cfg" value="S" onclick="change_dlv_cfg_form('s')" checked="checked" /> 쇼핑몰 설정 </label>
                            &nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="dlv_fee_cfg" value="G" onclick="change_dlv_cfg_form('g')" /> 상품 개별 설정</label>
                        </div>
                    </div>

                    <div class="col-4" style="display: flex">

                        <div class="text-gray-800"  style="">
                            <span class="dlv_config_detail_div" id="dlv_config_detail_s_div" style="display:inline;" >
                                유료, 배송비 {{$com_info->dlv_amt}}원 ({{$com_info->free_dlv_amt_limit}}원 이상 구매 시 무료)
                            </span>
                            <span class="dlv_config_detail_div" id="dlv_config_detail_g_div" style="display:none;">
                                <select name="dlv_fee_yn">
                                    <option value="Y" selected>유료</option>
                                    <option value="N">무료</option>
                                </select>
                                <input type="text" name="baesong_price" id="baesong_price" class="input-disable" style="width:50px;text-align:right;" /> 원
                            </span>
                        </div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">배송비 지불</div>
                        <div class="text-gray-800" >
                            <label><input type="radio" name="dlv_pay_type" value="P" checked="checked" /> 선불 </label>
                            &nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="dlv_pay_type" value="F" /> 착불</label>

                        </div>
                    </div>
                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">원산지</div>
                        <div class="text-gray-800"  style=""><input type="text" name="org_nm" id="org_nm" class=" input_style form-control-sm search-all" style="width:100px;ime-mode:disabled;vertical-align:middle;"  /></div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">제조사</div>
                        <div class="text-gray-800" ><input type="text" name="make" id="make" class=" input_style form-control-sm search-all" style="width:100px;ime-mode:disabled;vertical-align:middle;"  /></div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">재고 수량 관리</div>
                        <div class="text-gray-800" >
                            <label><input type="radio" name="is_unlimited" value="N" checked="checked" /> 수량 관리함 </label>
                            &nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="is_unlimited" value="Y" /> 수량 관리 안함(무한재고) </label>
                        </div>
                    </div>
                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">옵션사용</div>
                        <div class="text-gray-800"  style="">
                            <label><input type="radio" name="is_option_use" value="Y" checked="checked" /> 사용함 </label>
                            &nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="is_option_use" value="N" /> 사용안함 </label>
                        </div>
                    </div>
                    <div class="col-8" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">옵션구분</div>
                        <div class="text-gray-800" >
                            <input type="checkbox" name="chk_option_kind1" id="chk_option_kind1" checked />
                            <input type="text" class=" input_style form-control-sm" name="option_kind1" value="사이즈" class="input" />
                            <input type="checkbox"  name="chk_option_kind2" id="chk_option_kind2" />
                            <input type="text" class=" input_style form-control-sm search-all" name="option_kind2" value="컬러" class="input" />

                        </div>


                    </div>

                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">과세구분</div>
                        <div class="text-gray-800" >
                            <select name="tax_yn" class=" input_style form-control-sm search-all">
                                <option value="Y" selected>과세</option>
                                <option value="N">면세</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">재입고 알림</div>
                        <div class="text-gray-800"  style="">
                            <select name="restock_yn" class=" input_style form-control-sm search-all">
                                <option value="Y" selected>재입고함</option>
                                <option value="N">안함</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-4" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">적립금</div>
                        <div class="text-gray-800"  style="">
                            <label><input type="radio" name="point_cfg" value="S" onclick="change_point_cfg_form('s')" checked="checked" /> 쇼핑몰 설정 </label>
                            &nbsp;&nbsp;&nbsp;
                            <label><input type="radio" name="point_cfg" value="G" onclick="change_point_cfg_form('g')" /> 상품 개별 설정</label>
                        </div>
                    </div>
                    <div class="col-8" style="display: flex">
                        <div class="font-weight-bold text-right mr-3" style="width:100px;">적립금</div>
                        <div class="text-gray-800"  style="">
                            <span class="point_config_detail_div" id="point_config_detail_s_div" style="display:inline;" >
                                지급함, 상품 가격의 {{$point_info->value}}% 적립금 지급
                                <input type="hidden" value="{{$point_info->value}}" name="point_rate"/>
                            </span>
                            <span class="point_config_detail_div" id="point_config_detail_g_div" style="display:none;">
                                <select name="point_yn" class=" input_style form-control-sm search-all">
                                    <option value="Y" selected>지급함</option>
                                    <option value="N">지급안함</option>
                                </select>
                                <input type="text" name="point" id="point" class=" input_style form-control-sm search-all" style="width:50px;text-align:right;" /> 원
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-12" style="display: flex;">
                        <div class="font-weight-bold mr-3" style="width:100%;text-align:center">
                            <input type="button" class=" input_style form-control-sm search-all" value="적용" onclick="apply()"/>
                            <input type="button" class=" input_style form-control-sm search-all" value="취소" onclick="cancel()"/>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>

        <div class="card shadow mb-4">

{{--            <div class="card-body m-0 p-2">--}}
{{--                <div class="table-responsive">--}}
{{--                    <div id="div-gd" style="width:100%;" class="ag-theme-balham"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="card-body">
                <form name="f2" id="f2">
                    @CSRF
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">총 : <span id="goods-total">0</span> 건</h6>
                        <div>
                            <input type="button" value="저장"
                                   class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                                   onclick="insert()"/>
                        </div>
                    </div>
                    <div class="card-body m-0 p-2">
                        <div class="table-responsive">
                            <div id="goods_list_div" style="width:100%;height:1000px;" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal fade bd-example-modal-lg" id="brand_list_modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">브랜드 검색</h5>
                <button type="button" class="close" id="btn_brand_win_close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="brand_search_form" onsubmit="return false">
                    <div id="search-area" class="row mb-2">
                        <div class="col">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" >브랜드 이름</div>
                                            <div class="mb-0 text-gray-800">
                                            <input type='text' class=" input_style form-control-sm search-all" name='brand_nm' id="search_brand_nm" value='' style='width:200px;'>
                                            </div>
                                        </div>
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">사용여부</div>
                                            <div class="mb-0 text-gray-800">

                                                <input type="radio" name="use_yn" value="Y"/> Y
                                                <input type="radio" name="use_yn" value="N"/> N
                                            </div>
                                        </div>

                                        <div class="col mr-2">
                                            <a href="#" onclick="search_brand();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="brand_list_div" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham">


            </div>
            </div>
        </div>
    </div>

    <div>
        <form name="insert_form" id="insert_form" >
            @CSRF
            <textarea id="csvResult" name="form_str" style="display:none"></textarea>
        </form>
    </div>

    <!-- 카테고리 선택 모달 영역 -start -->
    <div class="modal fade bd-example-modal-lg" id="category_list_modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">카테고리 선택</h5>
                <button type="button" class="close" id="btn_category_win_close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" id="cat_type" value="";/>
            <div id="category_list_div" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham">

            </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" charset="utf-8">
    <!-- 카테고리 선택 모달 영역 -end -->

    /* 상품리스트 그리드 - start*/
        var columns = [
            {
                headerName: '#',pinned:'left',
                width:50,
                maxWidth: 100,
                // it is important to have node.id here, so that when the id changes (which happens
                // when the row is loaded) then the cell is refreshed.
                valueGetter: 'node.id',
                cellRenderer: 'loadingRenderer',
            },
            {field:"com_nm" , headerName:"업체", width:200, pinned:'left', editable: false,},
            {field:"op_cd" , headerName:"품목", width:200,   pinned:'left', editable: false,},
            {field:"brand" , headerName:"브랜드", width:200,   pinned:'left', editable: false,},
            {field:"rep_cat_cd" , headerName:"대표카테고리코드", width:200,   pinned:'left', editable: false,},
            {field:"u_cat_cd" , headerName:"용도카테고리코드", width:200,   pinned:'left', editable: false,},
            {field:"rep_cat_nm" , headerName:"대표카테고리", width:200,   pinned:'left', editable: false,},
            {field:"u_cat_nm" , headerName:"용도카테고리", width:200,   pinned:'left', editable: false,},
            {field:"style_no" , headerName:"스타일넘버", width:200,   pinned:'left', },
            {field:"goods_nm" , headerName:"상품명", type:"GoodsNameType", width:200,   pinned:'left', },
            {field:"goods_nm_eng" , headerName:"상품 영문명", width:200,   },
            {field:"price" , headerName:"판매가", width:200, type: 'currencyType' },
            {field:"margin_rate" , headerName:"마진율", width:200,   editable: false, },
            {field:"wonga" , headerName:"원가", width:200, type: 'currencyType',   },
            {field:"chk_option_kind1" , headerName:"옵션구분", width:200,   },
            {field:"option_kind1" , headerName:"옵션1", width:200,   },
            {field:"chk_option_kind2" , headerName:"옵션구분", width:200,   },
            {field:"option_kind2" , headerName:"옵션2", width:200,   editable: false, },
            {field:"qty" , headerName:"수량", width:200,   },
            {field:"opt_price" , headerName:"옵션가격", width:200, type: 'currencyType'   },
            {field:"head_desc" , headerName:"상단홍보글", width:200,   editable: false, },
            {field:"ad_desc" , headerName:"하단홍보글", width:200,   editable: false, },
            {field:"baesong_info" , headerName:"배송방법", width:200,   editable: false, },
            {field:"baesong_kind" , headerName:"배송처리", width:200,   editable: false, },
            {field:"dlv_pay_type" , headerName:"배송비지불", width:200,   editable: false, },
            {field:"dlv_fee_cfg" , headerName:"배송비설정", width:200,   editable: false, },
            {field:"bae_yn" , headerName:"배송비여부", width:200,   editable: false, },
            {field:"baesong_price" , headerName:"배송비", width:200,   editable: false, },
            {field:"point_cfg" , headerName:"적립금설정", width:200,   editable: false, },
            {field:"point_yn" , headerName:"적립금여부", width:200,   editable: false, },
            {field:"point_rate" , headerName:"적립율", width:200,   editable: false, },
            {field:"point" , headerName:"적립금", width:200,   editable: false, },
            {field:"org_nm" , headerName:"원산지", width:200,   editable: false, },
            {field:"md_nm" , headerName:"md_nm", width:200,   editable: false, },
            {field:"md_id" , headerName:"md_id", width:200,   editable: false, },
            {field:"make" , headerName:"제조사", width:200,   editable: false, },
            {field:"goods_cont" , headerName:"상품상세", width:200,   },
            {field:"spec_desc" , headerName:"제품사양", width:200,   },
            {field:"baesong_desc" , headerName:"예약/배송", width:200,   },
            {field:"opinion" , headerName:"MD상품평", width:200,   },
            {field:"restock_yn" , headerName:"재입고알림", width:200,   editable: false, },
            {field:"tax_yn" , headerName:"과세구분", width:200,   editable: false, },
        ];
    </script>


    <script type="text/javascript" charset="utf-8">

        const pApp = new App('', {
            gridId: "#goods_list_div",
        });

        const gridDiv = document.querySelector(pApp.options.gridId);
        let gx;

        $(document).ready(function () {
            gx = new HDGrid(gridDiv, columns);
            pApp.ResizeGrid(250);
            //Search();

            $('.search-all').keyup(function(){
                date_use_check();
            });

            $("#search_brand_nm").keyup(function(e){
                if(e.keyCode == 13){
                    search_brand();
                }
            });

        });

        function Search() {
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/order/ord01/search', data, 1);
        }
    </script>


    <script type="text/javascript">


        function PopPrdDetail(goods_no, goods_sub){
            window.open("/partner/product/prd01/"+goods_no,"Product Detail");
        }

        function search_brand(){
            var frm =$("#brand_search_form");

            $("#brand_list_div").html("");

            var gridDiv  = document.querySelector("#brand_list_div");
            new agGrid.Grid(gridDiv , brand_options);

            brand_options.api.sizeColumnsToFit();
            const remInPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
            brand_options.columnApi.getAllColumns().forEach(function (column) {

                if(column.colDef.width == undefined){
                    const hn = column.colDef.headerName;
                    const hnWidth = hn.length*2*remInPixel;
                    brand_options.columnApi.setColumnWidth(column.colId,hnWidth);
                } else {
                }

            });



            var brand_list = {
                rowCount:null,
                getRows: function(params){
                    $.ajax({
                        async: true,
                        type: 'get',
                        url: '/partner/api/brand/get_brand_nm/',
                        data: frm.serialize(),
                        success: function (data) {

                            //console.log(data);

                            var res = jQuery.parseJSON(data);

                            //console.log(data);

                            setTimeout(function () {

                                var lastRow = res.head.total;


                                params.successCallback(res.body, lastRow);


                            }, 300);
                        },
                        error: function(request, status, error) {
                            console.log("error")
                        }
                    });
                },
            };

            console.log(brand_list);
            brand_options.api.setDatasource(brand_list);



        }

        function select_category(cat_type){

            $("#category_list_div").html("");

            var gridDiv  = document.querySelector("#category_list_div");
            new agGrid.Grid(gridDiv , category_options);

            category_options.api.sizeColumnsToFit();
            const remInPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
            category_options.columnApi.getAllColumns().forEach(function (column) {

                if(column.colDef.width == undefined){
                    const hn = column.colDef.headerName;
                    const hnWidth = hn.length*2*remInPixel;
                    category_options.columnApi.setColumnWidth(column.colId,hnWidth);
                } else {
                }

            });



            var category_list = {
                rowCount:null,
                getRows: function(params){
                    $.ajax({
                        async: true,
                        type: 'get',
                        url: '/partner/api/category/get_category_list/'+cat_type,
                        success: function (data) {

                            var res = jQuery.parseJSON(data);
                            //console.log(res.body);
                            setTimeout(function () {

                                var lastRow = res.head.total;

                                //console.log(res.body);

                                params.successCallback(res.body, lastRow);


                            }, 300);
                        },
                        error: function(request, status, error) {
                            console.log("error")
                        }
                    });
                },
            };
            category_options.api.setDatasource(category_list);
            $("#cat_type").val(cat_type);


        }


        function select_brand_nm(brand_nm){

            $("#brand_nm").val(brand_nm);
            $('#btn_pop_brnad').trigger('click');

        }

        function select_category_nm(cat_type, idx, text, mx_len){
            set_rep_category(cat_type, idx, text, mx_len);
            $('#btn_pop_category').trigger('click');
        }

        function set_rep_category(cat_type, idx, text, mx_len) { //카테고리 추가
            if(cat_type == "display"){
                $("#rep_cat_nm").val(text);
                $("#rep_cat_cd").val(idx);

            }else if(cat_type == "item"){
                $("#u_cat_nm").val(text);
                $("#u_cat_cd").val(idx);
            }

        }

        /* 브랜드 검색 팝업 그리드 - start*/
        var brand_column = [
            {field:"brand_nm" , headerName:"브랜드 이름", width:500, cellStyle:{'text-align':'center'}, },
            {field:"use_yn", headerName:"사용여부", width:200, cellStyle:{'text-align':'center'},  },
            {field:"brand_nm" , headerName:"선택",width:100, cellStyle:{'text-align':'center'},
                cellRenderer: function(params) {

                    return '<input type="button" class="btn-primary" onclick="select_brand_nm(\''+params.value+'\')" value="선택" />';

                }
            }
        ];


        // let the grid know which columns to use
        var brand_options = {
            defaultColDef: {
                // set every column width
                //flex: 1,
                // make every column editable
                editable: true,
                resizable: true,
                autoHeight: true,
                suppressSizeToFit: true,
                //minWidth:90,
                // make every column use 'text' filter by default
                filter: 'agTextColumnFilter'
            },
            components: {
                loadingRenderer: function (params) {
                    if (params.value !== undefined) {
                        return params.node.rowIndex+1 ;
                    } else {
                        return '<img src="https://raw.githubusercontent.com/ag-grid/ag-grid/master/grid-packages/ag-grid-docs/src/images/loading.gif">';
                    }
                },
            },
            columnDefs: brand_column,
            rowSelection:'multiple',
            rowHeight: 50,
            rowBuffer: 0,
            rowModelType: 'infinite',
            // how big each page in our page cache will be, default is 100
            paginationPageSize: 100,
            // how many extra blank rows to display to the user at the end of the dataset,
            // which sets the vertical scroll and then allows the grid to request viewing more rows of data.
            // default is 1, ie show 1 row.
            cacheOverflowSize: 2,
            // how many server side requests to send at a time. if user is scrolling lots, then the requests
            // are throttled down
            maxConcurrentDatasourceRequests: 1,
            // how many rows to initially show in the grid. having 1 shows a blank row, so it looks like
            // the grid is loading from the users perspective (as we have a spinner in the first col)
            infiniteInitialRowCount: 100,
            // how many pages to store in cache. default is undefined, which allows an infinite sized cache,
            // pages are never purged. this should be set for large data to stop your browser from getting
            // full of data
            //maxBlocksInCache: 10,
        };

        /* 브랜드 검색 팝업 그리드 - end*/

        /* 카테고리 검색 팝업 그리드 - start*/
        var category_column = [
            {field:"d_cat_cd" , headerName:"분류번호", width:200, cellStyle:{'text-align':'center'}, },
            {field:"full_nm", headerName:"카테고리명", width:500, cellStyle:{'text-align':'center'},  },
            {field:"str" , headerName:"선택",width:100, cellStyle:{'text-align':'center'},
                cellRenderer: function(params) {

                    return '<input type="button" class="btn-primary" onclick="select_category_nm('+params.value+')" value="선택" />';

                }

            }
        ];


        // let the grid know which columns to use
        var category_options = {
            defaultColDef: {
                // set every column width
                //flex: 1,
                // make every column editable
                //minWidth:90,
                // make every column use 'text' filter by default
                //filter: 'agTextColumnFilter'
                editable: true,
                resizable: true,
                autoHeight: true,
                suppressSizeToFit: true,
            },
            components: {
                loadingRenderer: function (params) {
                    if (params.value !== undefined) {
                        return params.node.rowIndex+1 ;
                    } else {
                        return '<img src="https://raw.githubusercontent.com/ag-grid/ag-grid/master/grid-packages/ag-grid-docs/src/images/loading.gif">';
                    }
                },
            },
            getRowNodeId: function(data) {
                return data.d_cat_cd;
            },
            columnDefs: category_column,
            rowSelection:'multiple',
            rowHeight: 50,
            rowBuffer: 0,
            rowModelType: 'infinite',
            // how big each page in our page cache will be, default is 100
            paginationPageSize: 200,
            // how many extra blank rows to display to the user at the end of the dataset,
            // which sets the vertical scroll and then allows the grid to request viewing more rows of data.
            // default is 1, ie show 1 row.
            cacheOverflowSize: 2,
            // how many server side requests to send at a time. if user is scrolling lots, then the requests
            // are throttled down
            maxConcurrentDatasourceRequests: 1,
            // how many rows to initially show in the grid. having 1 shows a blank row, so it looks like
            // the grid is loading from the users perspective (as we have a spinner in the first col)
            infiniteInitialRowCount: 200,
            // how many pages to store in cache. default is undefined, which allows an infinite sized cache,
            // pages are never purged. this should be set for large data to stop your browser from getting
            // full of data
            //maxBlocksInCache: 10,
        };

        /* 카테고리 검색 팝업 그리드 - end*/




        // let the grid know which columns to use
        var goods_options = {
            defaultColDef: {
                // set every column width
                //flex: 1,
                // make every column editable
                minWidth:90,
                editable: true,
                // make every column use 'text' filter by default
                filter: 'agTextColumnFilter',
                cellStyle:{'text-align':'center','border-right':'1px solid #858796'}
            },
            components: {
                loadingRenderer: function (params) {
                    if (params.value !== undefined) {
                        return params.node.rowIndex+1 ;
                    }
                },
            },
            columnDefs: columns,
            rowSelection:'multiple',
            rowHeight: 50,
            rowBuffer: 0,
            rowModelType: 'infinite',
            // how big each page in our page cache will be, default is 100
            paginationPageSize: 200,
            // how many extra blank rows to display to the user at the end of the dataset,
            // which sets the vertical scroll and then allows the grid to request viewing more rows of data.
            // default is 1, ie show 1 row.
            cacheOverflowSize: 2,
            // how many server side requests to send at a time. if user is scrolling lots, then the requests
            // are throttled down
            maxConcurrentDatasourceRequests: 1,
            // how many rows to initially show in the grid. having 1 shows a blank row, so it looks like
            // the grid is loading from the users perspective (as we have a spinner in the first col)
            infiniteInitialRowCount: 200,
            // how many pages to store in cache. default is undefined, which allows an infinite sized cache,
            // pages are never purged. this should be set for large data to stop your browser from getting
            // full of data
            //maxBlocksInCache: 10,
        };

        /* 상품리스트 그리드 - end*/

        function apply(){


            var check = true;
            if($("#op_cd option:selected").val() == ""){
                check = false;
                alert("품목을 선택하세요.");
                $("#op_cd").focus();
            }else if($("#prd_cnt").val() == "" || $("#prd_cnt").val() < 0 ){
                check = false;
                alert("상품 수를 입력해주세요.");
                $("#prd_cnt").focus();
            }else if($("#brand_nm").val() == ""){
                check = false;
                alert("브랜드를 선택해주세요.");
                $("#brand_nm").focus();
            }else if($("#rep_cat_cd").val() == ""){
                check = false;
                alert("카테고리를 선택해주세요.");
                $("#rep_cat_cd").focus();
            }


            if(check){
                $("#goods-total").text($("#prd_cnt").val());
                $("#goods_list_div").html("");
                var gridDiv  = document.querySelector("#goods_list_div");
                new agGrid.Grid(gridDiv , goods_options);

                goods_options.api.sizeColumnsToFit();
                const remInPixel = parseFloat(getComputedStyle(document.documentElement).fontSize);
                goods_options.columnApi.getAllColumns().forEach(function (column) {

                    if(column.colDef.width == undefined){
                        const hn = column.colDef.headerName;
                        const hnWidth = hn.length*2*remInPixel;
                        goods_options.columnApi.setColumnWidth(column.colId,hnWidth);
                    } else {
                    }

                });



                var goods_list = {
                    rowCount:null,
                    getRows: function(params){

                        //console.log(data);


                        var form_arr = $("#f1").serializeArray();

                        var goods_cnt = $("#prd_cnt").val();

                        var data ='{"head" : {"total" : "'+goods_cnt+'"}, "body" : [';

                        var cnt = 0;

                        for(cnt; cnt <goods_cnt ; cnt++){
                            var i =0;
                            data += "{"
                            for(i; i < form_arr.length; i++){
                                data += '"'+form_arr[i].name+'":"'+form_arr[i].value+'"'
                                if(i < (form_arr.length-1) ){
                                    data +=","
                                }
                            }
                            data += "}"
                            if(cnt < (goods_cnt-1) ){
                                data +=","
                            }


                        }

                        data += ']}';
                        console.log(data);

                        var res = jQuery.parseJSON(data);

                        console.log(res);

                        setTimeout(function () {

                            var lastRow = res.head.total;
                            params.successCallback(res.body, lastRow);

                        }, 300);

                    },
                };

                console.log(goods_list);

                goods_options.api.setDatasource(goods_list);
            }


        }




        function change_dlv_cfg_form(value){
            $(".dlv_config_detail_div").css("display","none");
            $("#dlv_config_detail_"+value+"_div").css("display","inline");
        }

        function change_point_cfg_form(value){
            $(".point_config_detail_div").css("display","none");
            $("#point_config_detail_"+value+"_div").css("display","inline");
        }

        function insert(){
            $('#csvResult').val(function(){
                return goods_options.api.getDataAsCsv(
                    {
                        suppressQuotes: "none",
                        columnSeparator: ",",
                        customHeader: "",
                        customFooter: "",
                    }
                );
            });

            $.ajax({
                async: true,
                type: 'post',
                url: '/partner/product/prd06/bundle/',
                data: $("#insert_form").serialize(),
                success: function (data) {

                    console.log(data);

                    alert("상품이 등록되었습니다.");
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        }
    </script>
@stop
