@extends('partner_with.layouts.layout-nav')
@section('title','재고')
@section('content')
    <div class="py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">재고현황</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 상품코드 - {{ $goods_no }}</span>
                </div>
            </div>
            <div>
            </div>
        </div>
        <div id="search-area" class="search_cum_form">
            <form method="get" name="search">
                <input type="hidden" name="goods_no" id="goods_no" value="{{ $goods_no }}">
                <input type="hidden" name="qty_yn" id="goods_yn" value="">
                <div class="card mb-3">
                    <div class="d-flex card-header justify-content-between">
                        <h4>검색</h4>
                        <div class="flax_box">
                            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                            <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0 ml-1"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- end row -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="name">옵션</label>
                                    <div class="flax_box">
                                        <select name="goods_opt" id="goods_opt" class="form-control form-control-sm">
                                            <option value="">전체</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="name">일자</label>
                                    <div class="form-inline">
                                        <div class="docs-datepicker form-inline-inner input_box">
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control form-control-sm docs-date"
                                                       name="sdate" value="{{ $sdate }}" autocomplete="off"
                                                       disable>
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2"
                                                            disable>
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="docs-datepicker-container"></div>
                                        </div>
                                        <span class="text_line">~</span>
                                        <div class="docs-datepicker form-inline-inner input_box">
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control form-control-sm docs-date"
                                                       name="edate" value="{{ $edate }}" autocomplete="off">
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
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
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name">입출고구분</label>
                                    <div class="flax_box">
                                        <select name="io" id="io" class="form-control form-control-sm">
                                            <option value="">전체</option>
                                            <option value="I">입고</option>
                                            <option value="O">출고</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name">입출고형태</label>
                                    <div class="flax_box">
                                        <select name='jaego_type' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($jaego_types as $jaego_type)
                                                <option value='{{ $jaego_type->code_id }}'>
                                                    {{ $jaego_type->code_val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-area-ext d-none row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="name">송장번호</label>
                                    <div class="flax_box">
                                        <input type="text" name="invoice_no" id="invoice_no" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="resul_btn_wrap mb-3">
                    <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
                </div>
            </form>
        </div>
        <div class="show_layout mb-4">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">상품정보</a>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                <colgroup>
                                    <col width="120px"/>
                                    <col width="20%"/>
                                    <col width="30%"/>
                                    <col width="20%"/>
                                    <col width="30%"/>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td rowspan="3" class="img_box brln">
                                        <img id="goods_img" class="goods_img" src="{{@$goods_info->img}}" alt="이미지" style="width:120px;"
                                             onerror="this.src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='"/>
                                    </td>
                                    <th>스타일 넘버</th>
                                    <td><span id="style_no"></span></td>
                                    <th>공급처</th>
                                    <td><span id="com_nm"></span></td>
                                </tr>
                                <tr>
                                    <th>상품코드</th>
                                    <td>{{ @$goods_no }}</td>
                                    <th>대표카테고리</th>
                                    <td><span id="rep_cat_cd"></span></td>
                                <tr>
                                    <th>품목</th>
                                    <td><span id="opt_kind_nm"></span></td>
                                    <th>브랜드</th>
                                    <td><span id="brand_nm"></span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="show_layout mb-4">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <a href="#" class="m-0 font-weight-bold">옵션 및 판매</a>
                        </div>
                        <div class="fr_box">
                            <div class="font-weight-light">
                                <span class="mr-2">* 온라인/창고/판매수</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="card-title">
                        <div class="filter_wrap">
                            <div class="fl_box">
                            </div>
                            <div class="fr_box">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd-option" style="height:50PX; width:100%;" class="ag-theme-balham"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-body pt-3">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:300PX; width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
    <script language="javascript">

        var columns_opt = [
            {field: "option", headerName: "옵션", width: 200, sortable: "true"},
        ];

        var columns = [
            {field: "regi_date", headerName: "처리일", cellClass: 'hd-grid-code'},
            {field: "opt_val", headerName: "옵션", width: 200, sortable: "true"},
            {field: "io_gubun", headerName: "입출고"},
            {field: "type", headerName: "입출고형태"},
            {field: "qty", headerName: "수량", type: 'numberType'},
            {field: "ord_no", headerName: "주문번호", width: 130, type: 'HeadOrderNoType'},
            {field: "invoice_no", headerName: "송장번호"},
            {field: "ord_kind", headerName: "출고구분", cellStyle: StyleOrdKind},
            {field: "ord_state", headerName: "주문상태", cellStyle: StyleOrdState},
            {field: "clm_state", headerName: "클레임상태"},
            {field: "etc", headerName: "기타"},
            {field: "admin_nm", headerName: "처리자"}
        ];
    </script>
    <script>
        const pApp = new App('', {
            gridId: "#div-gd",
        });
        let gx;
        let gx_opt = null;
        let gridOptDiv = document.querySelector("#div-gd-option");
        let goods_opt = '{{$goods_opt}}';
        console.log(goods_opt);

        $(document).ready(function () {
            let gridDiv = document.querySelector(pApp.options.gridId);
            if (gridDiv !== null) {
                gx = new HDGrid(gridDiv, columns);
            }
            Search();
        });

        function ViewOptions(options,qty,wqty,sales){
            //console.log(options);
            //console.log(qty);
            if (gridOptDiv !== null) {

                if(gx_opt == null){
                    for(i=0;i<options[0].length;i++){
                        columns_opt.push(
                            {field: 'opt_' + options[0][i], headerName:options[0][i]},
                        );
                    }
                    gx_opt = new HDGrid(gridOptDiv, columns_opt);
                }

                rows = [];
                //console.log(options[1].length);
                if(options[1].length == 0){
                    var row = {"option":'재고'};
                    for(i=0;i<options[0].length;i++){
                        var opt2 = options[0][i];
                        var field = "opt_" + opt2;
                        sale = 0;
                        if (sales.hasOwnProperty(opt2)) {
                            // your code here
                            sale  = sales[opt2];
                        }
                        row[field] = qty[opt2] + ' / ' + wqty[opt2] + ' / ' + sale;
                    }
                    rows.push(row);
                } else {
                    for(j=0;j<options[1].length;j++){
                        var opt1= options[1][j];
                        var row = {"option":opt1};
                        for(i=0;i<options[0].length;i++){
                            var opt2 = options[0][i];
                            var field = "opt_" + opt2;
                            sale = 0;
                            if (sales.hasOwnProperty(opt2+'^'+opt1)) {
                                // your code here
                                sale  = sales[opt2+'^'+opt1];
                            }
                            row[field] = qty[opt2+'^'+opt1] + ' / ' + wqty[opt2+'^'+opt1] + ' / ' + sale;
                        }
                        rows.push(row);
                    }
                }
                gx_opt.setRows(rows);
                gx_opt.gridOptions.api.setDomLayout('autoHeight');
                // auto height will get the grid to fill the height of the contents,
                // so the grid div should have no height set, the height is dynamic.
                document.querySelector('#div-gd-option').style.height = '';
                if(columns_opt.length <= 5){
                    gx_opt.gridOptions.api.sizeColumnsToFit();
                }

                length = $('#goods_opt').children('option').length;
                if(length === 1){
                    for(opt in qty){
                        $("#goods_opt").append(new Option(opt, opt));
                    }
                    $("#goods_opt").val(goods_opt).attr("selected","selected");
                }
            }
        }

        function ViewGoods(goods){
            $('#style_no').html(goods.style_no);
            $('#opt_kind_nm').html(goods.opt_kind_nm);
            $('#com_nm').html(goods.com_nm);
            $('#brand_nm').html(goods.brand_nm);
            $('#rep_cat_cd').html(goods.rep_cat_cd);
            $('#goods_img').attr("src",goods.img);
            // $('#img').val(info.img);
            // document.getElementByid("img").src=info[0].img;
            //console.log(ord_lists);
            // goods_img.attr('src',info[0].img);
            // gx_n.gridOptions.api.setRowData(info);
        }

        function Search() {
            var goods_no = $("#goods_no").val();
            let data = $('form[name="search"]').serialize();
            gx.Request('/partner/stock/stk01/option/search/' + goods_no, data, 1);
            // gx_n.Request('/partner/stock/stk01/option/search/' + goods_no, data, 1);
            var goods_img = $('.goods_img');
            $.ajax({
                type: "get",
                url: '/partner/stock/stk01/option/search/' + goods_no,
                contentType: "application/x-www-form-urlencoded; charset=utf-8",
                dataType: 'json',
                data: $('form').serialize(),
                // data: {},
                success: function (data) {
                    ViewGoods(data.info);
                    ViewOptions(data.options,data.qty,data.wqty,data.sale);
                },
                error: function (e) {
                    console.log(e.responseText);
                }
            });
        }

    </script>
@stop
