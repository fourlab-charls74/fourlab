@extends('shop_with.layouts.layout-nav')
@section('title','원부자재입고/반품')
@section('content')



<style>
    /* 테이블 반응형 처리 */
		.table th {
			min-width: 120px;
		}

		@media (max-width: 740px) {
			.table td {
				float: unset !important;
				width: 100% !important;
			}
		}

        .img {
            width: 30px;
        }

</style>

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">원부자재입고/반품</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 생산입고관리</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>상품 선택</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
								<label for="com_nm">원부자재 업체</label>
								<div class="form-inline inline_select_box">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type="hidden" id="com_cd" name="com_cd" />
											<input onchange="changeInput();" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
											<a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="prd_nm">원부자재명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id="prd_nm" name='prd_nm' value=''>
                                </div>
                            </div>
                        </div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>원부자재 코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='text' id="prd_cd_sub" name='prd_cd_sub' class="form-control form-control-sm w-100 ac-style-no search-enter">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd_sub"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            </div>
        </div>

        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6> 
                        </div>
                        <div class="fr_box">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
                
    <div class="show_layout py-3">
        <form name="f1" id="f1">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">정보 입력</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th class="required"><label for="invoice_no">원부자재 업체</label></th>
                                                <td style="width:35%;">
                                                    <div class="form-inline inline_select_box">
                                                        <div class="form-inline-inner input-box w-100">
                                                            <div class="flax_box">
                                                                <input type="hidden" name="com_cd2" id="com_cd2" value="">
                                                                <input type="text" class="form-control form-control-sm" name="com_code" id="com_code" value="" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <th class="required"><label for="state">구분</label></th>
                                                <td style="width:35%;">
                                                    <div class="flax_box">
                                                        <select name="state" class="form-control form-control-sm w-100">
                                                            <option value="">선택</option>
                                                            <option value='10'>입고</option>
                                                            <option value='-10'>반품</option>
                                                        </select>
                                                    </div>
                                                </td>
                                               
                                            </tr>
                                            <tr>
                                                <th class="required"><label for="sdate">일자</label></th>
                                                <td style="width:35%;">
                                                    <div class="form-inline">
                                                        <div class="docs-datepicker form-inline-inner input_box w-100">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <th class="required"><label for="invoice_no">입고/반품번호</label></th>
                                                <td style="width:35%;">
                                                    <div class="flax_box">
                                                        <input type="text" onfocus="return getInvoiceNo();" class="form-control form-control-sm" name="invoice_no" value="">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required"><label for="prd_ord_type">유형</label></th>
                                                <td style="width:35%;">
                                                    <div class="flax_box">
                                                        <select name="prd_ord_type" class="form-control form-control-sm w-100">
                                                            <option value="">선택</option>
                                                            <option value='S'>부자재</option>
                                                            <option value='G'>사은품</option>
                                                        </select>
                                                    </div>
                                                </td>

                                                <th></th>
                                                <td style="width:35%;">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div style="width:100%;padding-top:20px;text-align:center;">
                                        <a href="#" onclick="add()" class="btn btn-sm btn-primary shadow-sm"><i class="bx bx-save mr-1"></i>저장</a>
                                        <a href="javascript:;" onclick="window.close()" class="btn btn-sm btn-secondary shadow-sm">닫기</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" charset="utf-8">

    const DEFAULT = { lineHeight : "30px" };
    const YELLOW = { backgroundColor: '#ffff99' };

    var columns= [
        {
            headerName: '#',
            width:50,
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
            hide: true
        },
        {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null, pinned:'left'},
        {
            field: "sup_com_nm",
            headerName: "공급업체(거래선)",
            width: 120,
            cellStyle: DEFAULT
        },
        {field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}"},
        {
            field: "prd_nm",
            headerName: "원부자재명",
            width: 100,
            cellStyle: DEFAULT
        },
        {field: "prd_cd", headerName: "원부자재코드", width:120, cellStyle: DEFAULT,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return EditProduct(\'' + params.value + '\');">' + params.value + '</a>';
                }
            }
        },
        {
            field: "color",
            headerName: "칼라",
            width: 80,
            cellStyle: DEFAULT
        },
        {
            field: "size",
            headerName: "사이즈",
            width: 80,
            cellStyle: DEFAULT
        },
        {
            field: "unit",
            headerName: "단위",
            width: 120,
            cellStyle: DEFAULT
        },
        {field:"stock_qty", headerName:"창고재고", width:60, type:'numberType', cellStyle: DEFAULT,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
                }
            }
        },
        {field:"in_qty", headerName:"수량", width:60, type:'numberType', cellStyle: { ...DEFAULT, ...YELLOW } , editable: true},
        {field:"price", headerName: "단가", width:84, type:'currencyType', cellStyle: { ...DEFAULT, ...{'text-align': 'right'}} },
        {field:"wonga", headerName: "원가", hide: true},
        {field:"amount", headerName: "금액", width:84, type:'currencyType', cellStyle: DEFAULT},
        {field:"sup_com_id", headerName: "공급업체", hide: true},
        {field:"type", headerName: "원부자재구분", hide: true},
        {headerName:"", field:"", width:"auto", cellStyle: DEFAULT}
    ];

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(457);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowId: params => params.data.id,
            onCellValueChanged: params => evtAfterEdit(params),
        };
        gx = new HDGrid(gridDiv, columns,options,{
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
            }
        })
        Search();
    });

    const gxStartEditingCell = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

    const evtAfterEdit = (params) => {
        if (params.oldValue !== params.newValue) {

            const row_index = params.rowIndex;
            const column_name = params.column.colId;
            const value = params.newValue;

            if (column_name == "in_qty") {
                params.node.setSelected(true);
                if (isNaN(value) == true || value == "" || parseInt(value) < 1) {
                    alert("1 이상의 숫자만 입력가능합니다.");
                    gxStartEditingCell(row_index, column_name);
                    return false;
                }
            }

            if (params.colDef.field == 'in_qty' || params.colDef.field == 'price') {
                const row = params.data;
                row.amount = parseFloat(row.in_qty) * row.price;
                gx.gridOptions.api.applyTransaction({ update: [row] });
            };
        }
    };
    
    const strNumToPrice = (price) => {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };


    function Search() {
        
        $('[name=search]').val(10);
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/cs/cs03/buy/search', data, 1, (data) => {
            // console.log(data);
        });
        let com_nm = document.getElementById('com_nm').value;
        let com_cd = document.getElementById('com_cd').value;

        if(com_nm != ''){
            document.getElementById('com_code').value = com_nm;
            document.getElementById('com_cd2').value = com_cd;
        }
    };


    const getInvoiceNo = () => {
	    const com_id = document.f1.com_cd2.value;
	    let invoice_no = document.f1.invoice_no.value;
        if (invoice_no == '' && com_id != "") {
            axios({
                url: `/shop/cs/cs03/buy/get-invoice-no/${com_id}`,
                method: 'get'
            }).then((response) => {
                invoice_no = response.data;
                document.f1.invoice_no.value = invoice_no;
            }).catch((error) => { 
                // console.log(error);
            });
        }
    };

    const validation = () => {

        // 업체 검색여부
        if (search.com_cd.value.trim() === '') {
            search.com_cd.focus();
            alert('공급업체를 선택하여 주십시오.');
            $('.sch-sup-company').click();
            return false;
        }

        // 입고/반품 상태 선택여부
        if (f1.state.selectedIndex == 0) {
            f1.state.focus();
            alert("구분를 선택해주세요.");
            return false;
        }

        // 일자 입력여부
        if (f1.sdate.value.trim() === '') {
            f1.sdate.focus();
            alert("일자를 입력해주세요");
            return false;
        }

        // 입고송장번호/반품번호 선택여부
        if (f1.invoice_no.value.trim() === '') {
            f1.invoice_no.focus();
            alert("입고송장번호/반품번호 입력해주세요");
            return false;
        }

        // 유형 선택여부
        if (f1.prd_ord_type.selectedIndex == 0) {
            f1.prd_ord_type.focus();
            alert("유형을 선택해주세요.");
            return false;
        }

        // 입고/반품 상품 선택여부
        const arr = gx.getSelectedRows();
        if (Array.isArray(arr) && !(arr.length > 0)) {
            alert('상품을 선택 해 주십시오.')
            return false;
        } else {
            // 저장시 수량은 1이상만 입력 가능
            const rowNodes = gx.gridOptions.api.getModel().rowsToDisplay;
            for (let i = 0; i < rowNodes.length; i++) {
                const rowNode = rowNodes[i];

                if (rowNode.selected != true) continue;

                const idx = rowNode.rowIndex;
                const { in_qty } = rowNode.data;

                if (parseInt(in_qty) < 1) {
                    alert("1 이상의 숫자만 입력가능합니다.");
                    gxStartEditingCell(idx, "in_qty");
                    return false;
                }
            }
        }

        return true;
    };

    const add = () => {
        if (!validation()) return;
        axios({
            url: '/shop/cs/cs03/buy/add',
            method: 'post',
            data: { 
                rows: gx.getSelectedRows(),
                state: f1.state.value,
                sdate: f1.sdate.value,
                invoice_no: f1.invoice_no.value,
                prd_ord_type: f1.prd_ord_type.value
            }
        }).then((response) => {
            console.log(response);
            if (response.data.code == 201) {
                alert("저장되었습니다.");
                window.opener.Search();
                window.close();
            } else if (response.data.code == 202) {
                alert('창고재고보다 수량이 많습니다. 수량을 수정해주세요.')    
            } else {
                alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
            }
        }).catch((error) => {
            // console.log(error);
        });
    };

    function EditProduct(product_code) {
        var url = '/shop/product/prd03/edit/' + product_code;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=555");
    }

    //원부자재 업체 검색
    $( ".sch-sup-company" ).on("click", () => {
        searchCompany.Open(null, '6', 'wonboo');
    });


    // function changeInput() {
	// 	let com_nm = document.getElementById('com_nm');

	// 	$.ajax({
	// 		method: 'post',
	// 		url: '/shop/cs/cs03/buy/changeInput',
	// 		data: {
	// 			com_nm : com_nm
	// 		},
	// 		success: function(data) {
	// 			if (data.code == '200') {
    //                 document.getElementById('com_code').value = data.result;
								
    //             } else {
    //                 alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
    //             }
    //         },
    //         error: function(res, status, error) {
    //             console.log(error);
    //         }
    //     });
    // }

 
   

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>        
@stop
