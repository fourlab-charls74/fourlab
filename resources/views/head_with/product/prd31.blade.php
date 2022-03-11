@extends('head_with.layouts.layout')
@section('title','사방넷 주문연동')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">사방넷 주문연동</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품·전시</span>
        <span>/ 사방넷</span>
        <span>/ 주문연동</span>
    </div>
</div>

<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">

            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">주문수집일자 :</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
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
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
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
								<div class="form-inline-inner input_box">
									<input type="text" class="form-control form-control-sm search-enter" name="goods_no" value="">
								</div>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매처</label>
                            <div class="flax_box">
                                <select name='site' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($sale_places as $sale_place)
                                        <option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="brand_cd">주문번호</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
                            </div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">주문자/수령자 :</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="ord_nm" value="">
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="r_nm" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">상품명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm" name='goods_nm' value=''>
							</div>
						</div>
					</div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상태 :</label>
                            <div class="flax_box">
                                <select name="s_state" class="form-control form-control-sm">
									<option value="" >모두</option>
									<option value="9" >주문수집(주문자정보없음)</option>
									<option value="10" >주문수집(주문자정보있음)</option>
									<option value="20" >주문등록</option>
									<option value="30" >송장전송</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">주문/클레임 상태</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name='ord_state' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($ord_states as $ord_state)
                                                <option value='{{ $ord_state->code_id }}'>
                                                    {{ $ord_state->code_val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
										<select name='clm_state' class="form-control form-control-sm">
											<option value=''>전체</option>
											@foreach ($clm_states as $clm_state)
												<option value='{{ $clm_state->code_id }}'>
													{{ $clm_state->code_val }}
												</option>
											@endforeach
										</select>
                                    </div>
                                </div>
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
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="s.sabangnet_order_id" selected>사방넷주문번호</option>
                                        <option value="s.mall_order_id" >판매처주문번호</option>
                                        <option value="o.ord_no" >주문번호</option>
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
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

    </div>
</form>
<!-- DataTales Example -->
<form method="get" name="f2">
<div class="card shadow mb-3 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
				<div class="fr_box flax_box" style="">
					<button class="btn-sm btn btn-primary mr-1" onclick="Cmder('get');return false;">주문수집</button>
					<button class="btn-sm btn btn-primary mr-1" onclick="Cmder('add');return false;">주문등록</button>
					<button class="btn-sm btn btn-primary mr-1" onclick="Cmder('del');return false;">주문등록 전 삭제</button>
					<button class="btn-sm btn btn-primary mr-1" onclick="Cmder('delivery');return false;">송장전송</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
</form>

<div class="card shadow">
	<div class="card-body">
		<div class="card-title">
			<h6 class="m-0 font-weight-bold text-primary fas fa-exclamation-circle"> Tip</h6>
		</div>
		<ul class="mb-0">
			<li>각쇼핑몰의 주문들이 사방넷에 수집이 되어져 있어야 합니다.</li>
			<li>쇼핑몰 주문수집은 사방넷 메뉴 [주문관리] >> [주문서수집(자동)] 의 수집버튼을 눌러 진행하시면 됩니다.</li>
			<li>수집된 주문건에 대해서 [주문관리] >> [주문서 확정관리] 메뉴에서 확정처리를 하셔야 주문을 연동하여 수집할 수 있습니다.</li>
		</ul>
	</div>
</div>



<script language="javascript">
    const editCellStyle = {
        'background' : '#ffff99',
        'border-right' : '1px solid #e0e7e7',
		'color'	: '#ff0000'
    };

	var columns = [
		{
			headerName: '#',
			width:35,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			pinned:'left',
		},
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width:28,
			pinned:'left'
		},
        {headerName:"주문번호", field: "ord_no", width: 130, pinned:'left'},
        {headerName:"일련번호", field: "ord_opt_no", width: 58, pinned:'left', type:'HeadOrderNoType'},
        {headerName:"사방넷 주문번호", field: "sabangnet_order_id", width: 110, pinned:'left'},
        {headerName:"판매처 주문번호", field: "mall_order_id", width: 130, pinned:'left'},
        {headerName:"판매처", field: "mall_name", width: 90, pinned:'left'},
        {headerName:"상태", field: "baesong_status", width: 70, pinned:'left'},
        {headerName:"판매처 상품번호", field: "order_product_id", width: 115},
        {headerName:"사방넷 상품번호", field: "sabangnet_product_id", width: 100},
        {headerName:"고객사 상품번호", field: "partner_product_id", width: 100},
        {headerName:"상품번호", field: "goods_no", width: 58, editable: true, cellStyle: editCellStyle },
        {headerName:"스타일넘버", field: "style_no", width: 85, editable: true, cellStyle: editCellStyle },
        {headerName:"상품명", field: "product_name", width: 150, type:"HeadGoodsNameType"},
        {headerName:"SKU", field: "sku", width: 115,
            cellRenderer: function(params) {
				return '<a href="#" onClick="getOptionPop(\''+ params.node.id +'\');return false;">'+ params.value + '</a>'
            }
		},
        {headerName:"옵션", field: "opt", width: 110, editable: true, cellStyle: editCellStyle },
        {headerName:"자사몰판매가", field: "goods_price", width: 90, type: 'currencyType'},
        {headerName:"판매가", field: "sale_price", width: 80, type: 'currencyType'},
        {headerName:"수량", field: "quantity", width: 46, type: 'currencyType'},
        {headerName:"주문금액", field: "order_price", width: 80, type: 'currencyType'},
        {headerName:"판매수수료", field: "sale_fee", width: 85, type: 'currencyType'},
        {headerName:"판매수수료율(%)", field: "sale_fee_ratio", width: 90, type: 'percentType'},
        {headerName:"주문자", field: "order_name", width: 60},
        {headerName:"주문자 연락처", field: "order_tel", width: 100},
        {headerName:"주문자 핸드폰번호", field: "order_cel", width: 100},
        {headerName:"주문자 이메일", field: "order_email", width: 100},
        {headerName:"수령자", field: "receive", width: 60},
        {headerName:"수령자 연락처", field: "receive_tel", width: 100},
        {headerName:"수령자 핸드폰번호", field: "receive_cel", width: 100},
        {headerName:"수령자 우편번호", field: "receive_zipcode", width: 100},
        {headerName:"수령자 주소", field: "receive_addr", width: 100},
        {headerName:"배송지불시점", field: "baesong_type", width: 95},
        {headerName:"배송비", field: "baesong_bi", width: 60, type: 'currencyType'},
        {headerName:"배송메세지", field: "delivery_msg", width: 100},
        {headerName:"택배사", field: "dlv_cd", width: 80},
        {headerName:"송장번호", field: "dlv_no", width: 100},
        {headerName:"상태", field: "shop_state", width: 60},
        {headerName:"주문상태", field: "ord_state", width: 70, cellStyle:StyleOrdState},
        {headerName:"클레임상태", field: "clm_state", width: 70, cellStyle:StyleClmState},
        {headerName:"주문일시", field: "orderdate", width: 110},
        {headerName:"주문접수일시", field: "order_reg_date", width: 110},
        {headerName:"등록자", field: "admin_nm", width: 70},
        {headerName:"등록일시", field: "rt", width: 110},
        {headerName:"최종수정일시", field: "ut", width: 110},
    ];

	function getOptionPop(id)
	{
		var RowNode	= gx.getRowNode(id);

		goods_no	= RowNode.data.goods_no;
		goods_nm	= RowNode.data.product_name;
		sku			= RowNode.data.sku;

		//console.log(row.data.ord_no);

        const url='/head/product/prd31/option/' + goods_no + '/?goods_nm=' + goods_nm + '&sku=' + sku + '&id=' + id;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=750,height=600");
	}

	function cbOptionPop(goods_opt, id)
	{
		RowNode	= gx.getRowNode(id);

		RowNode.data.opt	= goods_opt;

		RowNode.setData(RowNode.data);
	}

	function Cmder(cmd)
	{
		if( cmd == "get" )				GetOrder();
		else if( cmd == "add" )			AddOrder();
		else if( cmd == "del" )			DelOrder();
		else if( cmd == "delivery" )	DlvOrder();
	}

	function GetOrder(){

		if(confirm('주문을 수집하시겠습니까?'))
		{

			sdate	= $('input[name="sdate"]').val();
			edate	= $('input[name="edate"]').val();

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/product/prd31/get-order',
				data: 
				{ 
					s_sdate : sdate,
					s_edate : edate
				},
				success: function (data) {
					if( data.result_code == "200" )
					{
						alert("주문을 수집하였습니다.");

						Search();
					}
					else
					{
						alert(data.result_code);
					}
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		}
	}

	var selectedData	= [];
	var _total_cnt		= 0;
	var _proc_cnt		= 0;

	function AddOrder()
	{
		var checkNodes	= gx.getSelectedNodes();

		if( checkNodes.length === 0 )
		{
            alert("등록 할 주문을 선택 해 주십시오.");
            return;
		}

		selectedData	= checkNodes;
		_total_cnt		= checkNodes.length;

		if (confirm("선택한 주문을 등록하시겠습니까?")) 
		{
			AddOrderOpt();
		}
	}

	function AddOrderOpt()
	{
		if( selectedData.length > 0 )
		{
			var row = selectedData.shift();
			var nodeid		= row.id;
			var sabangnet_order_id	= row.data.sabangnet_order_id;
			var goods_no	= row.data.goods_no;
			var option		= row.data.opt;
			var sale_fee	= row.data.sale_fee;

			checkData		= row.data;
			checkData.ord_no	= "등록중 ...";

			gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/product/prd31/add-order',
				data: 
				{ 
					order_id : sabangnet_order_id,
					goods_no : goods_no,
					option : option,
					sale_fee : sale_fee
				},
				success: function (data) {
					cbAddOrderOpt(data, row);
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		} 
		else 
		{
			alert('선택한 ' + _total_cnt + ' 건의 주문 중 ' + _proc_cnt + ' 건을 등록하였습니다.');
		}

	}

	function cbAddOrderOpt(res, row)
	{
		nodeid		= row.id;
		checkData	= row.data;

		if( res.result_code == "200" )
		{
			_proc_cnt++;
		
			checkData.ord_no		= res.result_msg;
			checkData.ord_opt_no	= res.result_no;
		} else {
			checkData.ord_no		= res.result_msg;
		}

		gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

		setTimeout("AddOrderOpt()",100);
	}

	function DelOrder()
	{
		var checkRows	= gx.getSelectedRows();
		var reg_cnt		= 0;

		if( checkRows.length === 0 )
		{
            alert("삭제 할 주문을 선택 해 주십시오.");
            return;
		}

		checkRows.forEach((selectedRow, index) => {
			if( selectedRow.ord_no != "" && selectedRow.ord_no != null )
			{
				reg_cnt++;
			}
		});


		if( reg_cnt != 0 )
		{
			alert("등록된 주문은 삭제할 수 없습니다.");
			return;
		}
		else
		{
			
			if(confirm("선택하신 주문을 삭제 하시겠습니까?"))
			{
				$.ajax({
					async: true,
					type: 'put',
					url: '/head/product/prd31/del-order',
					data: {
						data : JSON.stringify(checkRows),
					},
					success: function (data) {
						if( data.code == "200" )
						{
							alert("선택하신 주문을 삭제하였습니다.");
							Search();
						} 
						else 
						{
							//console.log(data.code);
							alert("데이터 삭제를 실패하였습니다.");
						}
					},
					error: function(request, status, error) {
						alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
						console.log("error")
					}
				});
			}

		}
		
	}

	function DlvOrder()
	{
		var checkNodes	= gx.getSelectedNodes();

		if( checkNodes.length === 0 )
		{
            alert("송장등록 할 주문을 선택 해 주십시오.");
            return;
		}

		checkNodes.forEach((selectedRow, index) => {

			if( selectedRow.data.ord_opt_no > 0 &&  ( selectedRow.data.dlv_no != "" && selectedRow.data.dlv_no != null ) )
			{
				selectedData.push(selectedRow);
			}
		});

		_total_cnt	= selectedData.length;
		_proc_cnt	= 0;

		if (_total_cnt == 0) 
		{
			alert("송장등록 가능한 주문을 선택 해 주십시오.");
			return;
		} 
		else 
		{
			if( confirm("선택한 주문을 송장등록 하시겠습니까?") )
			{
				DlvOrderOpt();
			}
		}
	}

	function DlvOrderOpt()
	{

		if( selectedData.length > 0 )
		{
			var row = selectedData.shift();
			var nodeid		= row.id;
			var sabangnet_order_id	= row.data.sabangnet_order_id;

			checkData		= row.data;
			checkData.shop_state	= "...";

			gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/product/prd31/dlv-order',
				data: 
				{ 
					order_id : sabangnet_order_id,
				},
				success: function (data) {
					cbDlvOrderOpt(data, row);
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		} 
		else 
		{
			alert('선택한 ' + _total_cnt + ' 건의 주문 중 ' + _proc_cnt + ' 건을 송장등록 하였습니다.');
		}

	}

	function cbDlvOrderOpt(res, row)
	{
		nodeid		= row.id;
		checkData	= row.data;

		if( res.result_code == "200" )
		{
			_proc_cnt++;
		
			checkData.shop_state	= "30";
		} else {
			checkData.shop_state	= res.result_msg;
		}

		gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

		setTimeout("DlvOrderOpt()",100);
	}

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(405);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/product/prd31/search', data, 1);
    }

</script>
@stop
