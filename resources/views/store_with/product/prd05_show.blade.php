@extends('store_with.layouts.layout-nav')

@php
    $title = "상품가격 변경";
    if($cmd == "update") $title = "상품가격 변경 상세";
@endphp

@section('title', $title)
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품가격 관리</span>
                <span>/ 상품관리</span>
            </div>
        </div>
        <div class="d-flex">
            @if ($cmd == 'add')
                <a href="javascript:void(0)" onclick="Save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            @elseif ($cmd == 'update')
                @if ($res->apply_yn == 'N')
                    <a href="javascript:void(0)" onclick="Update();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
                @endif
            @endif
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 120px;}
        .table td {width: 50%;}
        
        @media (max-width: 740px) {
            .table td {float: unset !important;width: 100% !important;}
        }
    </style>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">기본정보</a>
            </div>
            <div class="card-body">
                <form name="f1">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">변경일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="change_date" id="change_date" value="@if($cmd == 'update') {{$res->change_date}} @else {{$edate}} @endif" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>@if ($cmd == 'update') 가격변경 코드 @endif</th>
                                            <td>
                                                <div class="form-inline inline_select_box">
                                                    <div class="d-flex w-100">
                                                        <span id="product_price_cd">@if ($cmd == 'update') {{@$code}} @endif </span>
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
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">상품정보</a>
                <div class="d-flex">
                    <div class="d-flex mr-1 mb-1 mb-lg-0">
                        @if ($cmd == 'update' && $res->apply_yn == 'N')
                        <input type='text' id="change_price" name='change_price' class="form-control form-control-sm" style="width:90px;" value="{{@$res->change_val}}">
                        <select id='change_kind' name='change_kind' class="form-control form-control-sm ml-1"  style='width:70px;display:inline;'>
                                <option value=''>선택</option>
                                <option value='P' @if($res->change_kind == 'P') selected @endif>%</option>
                                <option value='W' @if($res->change_kind == 'W') selected @endif>원</option>
                        </select>
                        <button type="button" onclick="change_apply(false);" class="btn btn-sm btn-primary shadow-sm ml-1" id="change_btn"> 적용</button>
                        @elseif ($cmd == 'add')
                        <input type='text' id="change_price" name='change_price' class="form-control form-control-sm" style="width:90px;">
                        <select id='change_kind' name='change_kind' class="form-control form-control-sm ml-1"  style='width:70px;display:inline;'>
                                <option value=''>선택</option>
                                <option value='P'>%</option>
                                <option value='W'>원</option>
                        </select>
                        <button type="button" onclick="change_apply(false);" class="btn btn-sm btn-primary shadow-sm ml-1" id="change_btn"> 적용</button>
                        @endif
                    </div>
                    @if ($cmd == 'update' && $res->apply_yn == 'N')
                    <span class="d-none d-lg-block ml-1 mr-2 tex-secondary" style="font-size:large">|</span>
                    <button type="button" onclick="addGoods();" class="btn btn-sm btn-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-plus"></i> 상품추가</button>
                    <button type="button" onclick="del_rows();" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
                    @elseif ($cmd == 'add')
                    <span class="d-none d-lg-block ml-1 mr-2 tex-secondary" style="font-size:large">|</span>
                    <button type="button" onclick="addGoods();" class="btn btn-sm btn-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-plus"></i> 상품추가</button>
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
                    @endif
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

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        @if ($cmd == 'update' && $res->apply_yn == 'N')
            {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        @elseif ($cmd == 'add')
            {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        @endif
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 153},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 65},
        {field: "price", headerName: "판매가", type: "currencyType", width: 65},
        {field: "change_val", headerName: "변경금액(율)", type: "currencyType", width: 80 @if ($cmd == 'update' && $res->apply_yn == 'N') ,editable:true, cellStyle: {'background' : '#ffff99'} @elseif($cmd == 'add') ,editable:true, cellStyle: {'background' : '#ffff99'} @endif},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let add_product = [];
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(340);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        if('{{ @$cmd }}' === 'update') GetProducts();
    });
    

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "product_price_cd=" + '{{ @$res->product_price_cd }}';
        gx.Request('/store/product/prd05/show-search', data, 1);
    }

    // 상품 삭제
    // const deleteRow = (row) => { gx.gridOptions.api.applyTransaction({remove : [row]}); };

    function del_rows() {
        let rows = gx.getSelectedRows();
        let idx = document.getElementById('product_price_cd').innerHTML
        let row = gx.getRows();
        let row_cnt = row.length;

        if(rows.length < 1) {
            return alert('삭제할 상품을 선택해주세요.');
        }

        if(!confirm("선택한 상품을 삭제하시겠습니까? \n삭제한 상품은 되돌릴 수 없습니다.")) return;

        axios({
            url: '/store/product/prd05/del-product',
            method: 'put',
            data: {
                data: rows,
                cnt: row_cnt,
                idx: idx
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                location.reload();
                opener.Search();
            } else {
                console.log(res.data);
                alert("상품 삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
        
        
    };


    const delGoods = () => {
        const rows = gx.getSelectedRows();

        console.log(rows);

        for (let i = 0; i < rows.length; i++) {
            gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
        }

    };

    /***************************************************************************/
    /******************************** 상품 추가 관련 ****************************/
    /***************************************************************************/

    // 상품 추가
    function addGoods() {
        const url = `/store/api/goods/show/`;
        window.open(url, "_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /**
     * goods api logics - 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다.
     */

    let callbaackRows = [];

    var goodsCallback = (row) => {
        addRow(row);
        setGoodsRows();
    };
    
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.map(row => addRow(row));
        setGoodsRows();
    };

    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount() + callbaackRows.length;
        row = { 
            ...row,
            change_val : 0
        };
        callbaackRows.push(row);

    };
    
    var setGoodsRows = () => {
        gx.gridOptions.api.applyTransaction({ add : callbaackRows });
        callbaackRows = [];
    }
    
    function change_apply(is_zero = false) {

        let change_kind = $('#change_kind').val();
        let change_price = parseInt($('#change_price').val()) ?? '';
        let rows = gx.getSelectedRows();

        function validate() {
            if(rows.length < 1) {
                return alert('가격을 변경할 상품을 선택해주세요.');
            } 

            if ($('#change_price').val() === '') {
                alert('변경금액(율)을 입력해주세요.');
                return false;
            }

            if ($('#change_kind').val() === '') {
                alert('변경종류를 선택해주세요.');
                return false;
            }

            return true;
        }
    

        if (!validate()) return;
       
        if (change_kind == 'W') {
            const change_rows = gx.getSelectedRows().map(row => ({
                ...row, 
                change_val : row.goods_sh - change_price 
            }));
            
            for (let i = 0; i < rows.length; i++) {
                gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
            }
            gx.gridOptions.api.applyTransaction({ add : change_rows });

            // await gx.gridOptions.api.applyTransaction({ update: [change_rows] });
            gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero)); 

        } else if (change_kind == 'P') {
            /**
             * 할인율  = 판매가 - (판매가 * 할인율)
             */

            if (change_price == 100) {
                let sale = change_price/100;
            
                const change_rate = gx.getSelectedRows().map(row => ({
                    ...row, 
                    change_val : row.goods_sh 
                }));

                for (let i = 0; i < rows.length; i++) {
                    gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
                }
                gx.gridOptions.api.applyTransaction({ add : change_rate });
                gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero));
                
            } else if (change_price > 100){
                let sale = change_price/100;

                const change_rate = gx.getSelectedRows().map(row => ({
                    ...row, 
                    change_val : row.goods_sh * sale 
                }));

                for (let i = 0; i < rows.length; i++) {
                    gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
                }
                gx.gridOptions.api.applyTransaction({ add : change_rate });
                gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero)); 
            } else if (change_price < 100) {
                let sale = change_price/100;

                const change_rate = gx.getSelectedRows().map(row => ({
                    ...row, 
                    change_val : row.goods_sh - (row.goods_sh * sale) 
                }));

                for (let i = 0; i < rows.length; i++) {
                    gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
                }
                gx.gridOptions.api.applyTransaction({ add : change_rate });
                gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero)); 
            }
                
        }
       
    }

    function Save() {
        let change_date = $('#change_date').val();
        let change_price = parseInt($('#change_price').val());
        let change_kind = $('#change_kind').val();
        let rows = gx.getSelectedRows();
        let change_cnt = rows.length;

        if(rows.length < 1) return alert('저장할 상품을 선택해주세요.');

        if(!confirm("선택한 상품의 변경금액(율)을 저장하시겠습니까?")) return;

        axios({
            url: '/store/product/prd05/change-price',
            method: 'put',
            data: {
                data: rows,
                change_date : change_date,
                change_kind : change_kind,
                change_price : change_price,
                change_cnt : change_cnt
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                window.close();
                opener.Search();
            } else {
                console.log(res.data);
                alert("상품가격 변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    function Update() {
        let change_date = $('#change_date').val();
        let change_price = parseInt($('#change_price').val());
        let change_kind = $('#change_kind').val();
        let product_price_cd = '{{@$code}}';
        let rows = gx.getRows();
        let change_cnt = rows.length;

        // if(rows.length < 1) return alert('저장할 상품을 선택해주세요.');

        if(!confirm("선택한 상품의 변경금액(율)을 수정하시겠습니까?")) return;

        axios({
            url: '/store/product/prd05/update-price',
            method: 'put',
            data: {
                data: rows,
                change_date : change_date,
                change_kind : change_kind,
                change_price : change_price,
                change_cnt : change_cnt,
                product_price_cd : product_price_cd
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                window.close();
                opener.Search();
            } else {
                console.log(res.data);
                alert("상품가격 변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }


</script>
@stop
