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
                <span>/ 상품관리</span>
                <span>/ 상품가격 관리</span>
                <span>/ 상품가격 변경</span>
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
        .table th {min-width: 130px;}
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
                                            <th class="required">상품가격변경 구분</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="product_price_type" value="reservation" id="reservation" class="custom-control-input" 
                                                        @if ($cmd == 'update')
                                                            @if ($res->change_type == 'R')
                                                                checked
                                                            @endif
                                                        @else
                                                            checked
                                                        @endif
                                                        >
                                                        <label class="custom-control-label" for="reservation">예약</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="product_price_type" value="now" id="now" class="custom-control-input"  
                                                        @if ($cmd == 'update')
                                                            @if ($res->change_type != 'R')
                                                                checked
                                                            @endif
                                                        @endif>
                                                        <label class="custom-control-label" for="now">즉시</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">변경일자</th>
                                            <td>
                                                <div class="form-inline" id="sel_date">
                                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="change_date_res" id="change_date_res" value="@if($cmd == 'update') {{$res->change_date}} @else {{$rdate}} @endif" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                </div>
                                                <div class="form-inline" id="cur_date">
                                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                                        <div>
                                                            <span id="change_date_now">{{$edate}}</span>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
										<tr>
											<th class="required">상품운영 구분</th>
											<td>
												<div class="flax_box">
													<select name='plan_category' id="plan_category" class="form-control form-control-sm">
														<option value='00'>00 : 변경없음</option>
														<option value='01'>01 : 정상매장</option>
														<option value='02'>02 : 전매장</option>
														<option value='03'>03 : 이월취급점</option>
														<option value='04'>04 : 아울렛전용</option>
													</select>
												</div>
											</td>
											<th>&nbsp;</th>
											<td>&nbsp;</td>
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
                        <select id='price_kind' name='price_kind' class="form-control form-control-sm mr-1"  style='width:80px;display:inline;'>
                            <option value="">선택</option>
                            <option value="tag_price" @if($res->price_kind == 'T') selected @endif>정상가</option>
                            <option value="price" @if($res->price_kind == 'P') selected @endif>현재가</option>
                        </select>
                        <select id='change_kind' name='change_kind' class="form-control form-control-sm mr-1"  style='width:70px;display:inline;'>
                                <option value=''>선택</option>
                                <option value='P' @if($res->change_kind == 'P') selected @endif>%</option>
                                <option value='W' @if($res->change_kind == 'W') selected @endif>금액</option>
                        </select>
                        <input type='text' id="change_price" name='change_price' class="form-control form-control-sm" style="width:90px;" value="{{@$res->change_val}}">
                        <button type="button" onclick="change_apply(false);" class="btn btn-sm btn-primary shadow-sm ml-1" id="change_btn"> 적용</button>
                        @elseif ($cmd == 'add')
                        <select id='price_kind' name='price_kind' class="form-control form-control-sm mr-1"  style='width:80px;display:inline;'>
                            <option value="">선택</option>
                            <option value="tag_price">정상가</option>
                            <option value="price">현재가</option>
                        </select>
                        <select id='change_kind' name='change_kind' class="form-control form-control-sm mr-1"  style='width:70px;display:inline;'>
                                <option value=''>선택</option>
                                <option value='P'>%</option>
                                <option value='W'>금액</option>
                        </select>
                        <input type='text' id="change_price" name='change_price' class="form-control form-control-sm" style="width:90px;">
                        <button type="button" onclick="change_apply(false);" class="btn btn-sm btn-primary shadow-sm ml-1" id="change_btn"> 적용</button>
                        <button type="button" onclick="change_return(false);" class="btn btn-sm btn-primary shadow-sm ml-1" id="change_return"> 정상가로 환원하기</button>
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
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 65},
        {field: "price", headerName: "현재가", type: "currencyType", width: 65},
        {field: "change_val", headerName: "가격", type: "currencyType", width: 80 @if ($cmd == 'update' && $res->apply_yn == 'N') ,editable:true, cellStyle: {'background' : '#ffff99'} @elseif($cmd == 'add') ,editable:true, cellStyle: {'background' : '#ffff99'} @endif},
		{width : 'auto'}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let add_product = [];
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(380);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        if('{{ @$cmd }}' === 'update') GetProducts();
        $('#cur_date').hide();
    });
    

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "product_price_cd=" + '{{ @$res->product_price_cd }}';
        gx.Request('/store/product/prd05/show-search', data, 1);
    }

    // 상품 삭제
    let del_product = [];

    function del_rows() {

        const rows = gx.getSelectedRows();

        for (let i = 0; i < rows.length; i++) {
            gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
            del_product.push(rows[i]);
        }

    };


    const delGoods = () => {
        const rows = gx.getSelectedRows();

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
    
    function validate() {
        let rows = gx.getSelectedRows();

        if(rows.length < 1) {
            return alert('가격을 변경할 상품을 선택해주세요.');
        }

        if ($('#price_kind').val() === '') {
            alert('정상가 또는 현재가 기준으로 변경할 것인지 선택해주세요.');
            return false;
        }

        if ($('#change_kind').val() === '') {
            alert('변경종류를 선택해주세요.');
            return false;
        }

        if ($('#change_price').val() === '') {
            alert('변경금액(율)을 입력해주세요.');
            return false;
        }

        return true;
    }

    function change_apply(is_zero = false) {

        let change_kind = $('#change_kind').val();
        let change_price = parseInt($('#change_price').val()) ?? '';
        let price_kind = $('#price_kind').val();
        let rows = gx.getSelectedRows();
        let row = gx.getRows();

        if(row.length < 1) return alert('상품가격을 변경할 상품을 추가해주세요.');

        if (!validate()) return;
       
        if (change_kind == 'W') {
            let change_rows = [];
            if(price_kind == 'tag_price') {
                change_rows = gx.getSelectedRows().map(row => ({
                    ...row, 
                    change_val : row.goods_sh + change_price 
                }));
            } else {
                change_rows = gx.getSelectedRows().map(row => ({
                    ...row, 
                    change_val : row.price + change_price 
                }));
            }
            
            for (let i = 0; i < rows.length; i++) {
                gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
            }
            gx.gridOptions.api.applyTransaction({ add : change_rows });
            gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero)); 

        } else if (change_kind == 'P') {
            /**
             * 할인율  = 판매가 - (판매가 * 할인율)
             */
            let change_rate = [];
            if (change_price >= 100) {
                let sale = change_price/100;
            
                if(price_kind == 'tag_price') {
                    change_rate = gx.getSelectedRows().map(row => ({
                        ...row, 
                        change_val : 0 
                    }));
                } else {
                    change_rate = gx.getSelectedRows().map(row => ({
                        ...row, 
                        change_val : 0
                    }));
                }

                for (let i = 0; i < rows.length; i++) {
                    gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
                }
                gx.gridOptions.api.applyTransaction({ add : change_rate });
                gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero));
                
            } else if (change_price < 100) {
                let sale = change_price/100;

                if(price_kind == 'tag_price') {
                    change_rate = gx.getSelectedRows().map(row => ({
                        ...row, 
                        change_val : row.goods_sh - (row.goods_sh * sale)
                    }));
                } else {
                    change_rate = gx.getSelectedRows().map(row => ({
                        ...row, 
                        change_val : row.price - (row.price * sale)
                    }));
                }

                for (let i = 0; i < rows.length; i++) {
                    gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
                }
                gx.gridOptions.api.applyTransaction({ add : change_rate });
                gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero)); 
            }
                
        }
       
    }

    function Save() {
		let change_date_res	= $('#change_date_res').val();
		let change_date_now	= document.getElementById('change_date_now').innerText;
		let change_price	= parseInt($('#change_price').val());
		let change_kind		= $('#change_kind').val();
		let type			= $("input[name='product_price_type']:checked").val();
		let rows			= gx.getSelectedRows();
		let change_cnt		= rows.length;
		let price_kind		= $('#price_kind').val();
		let plan_category	= $('#plan_category').val();

		console.log(plan_category);

        if(rows.length < 1)		return alert('저장할 상품을 선택해주세요.');

		if(price_kind == '')	return alert('정상가/현재가는 반드시 선택해야 합니다.');
		if(change_kind == '')	return alert('변경구분은 반드시 선택해야 합니다.');
		if($('#change_price').val() == '' )	return alert('변경 액/률은 반드시 선택해야 합니다.');

        if(!confirm("선택한 상품의 가격을 저장하시겠습니까?")) return;

        axios({
            url: '/store/product/prd05/change-price',
            method: 'put',
            data: {
                data: rows,
                change_date_res : change_date_res,
                change_date_now : change_date_now,
                change_kind : change_kind,
                change_price : change_price,
                change_cnt : change_cnt,
                type : type,
                price_kind : price_kind,
				plan_category : plan_category
                
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

	// 수정 사용하지 않음
    function Update() {
		let change_date			= $('#change_date').val();
		let change_price		= parseInt($('#change_price').val());
		let change_kind			= $('#change_kind').val();
		let product_price_cd	= '{{@$code}}';
		let rows				= gx.getRows();
		let change_cnt			= rows.length;

        for(let i = 0; i < rows.length;i++) {
            if(rows[i].change_val == 0){
                return alert('변경금액(율)을 입력해주세요.');
            }
        }

        if(!confirm("상품을 수정하시겠습니까?")) return;

        axios({
            url: '/store/product/prd05/update-price',
            method: 'put',
            data: {
                data: rows,
                change_date : change_date,
                change_kind : change_kind,
                change_price : change_price,
                change_cnt : change_cnt,
                product_price_cd : product_price_cd,
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

    @if ($cmd == 'add')
    $("input[name='product_price_type']").change(function(){
        let type = $("input[name='product_price_type']:checked").val();

        if (type == 'reservation') {
            $('#sel_date').show();
            $('#cur_date').hide();

        } else {
            $('#sel_date').hide();
            $('#cur_date').show();
        }
    });
    @endif

    // 정상가로 환원하기
    function change_return(is_zero = false) {
        let rows = gx.getSelectedRows();
        change_return_price = gx.getSelectedRows().map(row => ({
            ...row, 
            change_val : row.goods_sh 
        }));
        // gx.gridOptions.api.applyTransaction({ update : change_return });
        for (let i = 0; i < rows.length; i++) {
            gx.gridOptions.api.applyTransaction({ remove : [rows[i]] });
        }
        gx.gridOptions.api.applyTransaction({ add : change_return_price });
        gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero));

		$('#price_kind').val('tag_price').prop("selected", true);
		$('#change_kind').val('P').prop("selected", true);
		$('#change_price').val('0');
    }

</script>
@stop
