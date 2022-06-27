@extends('store_with.layouts.layout')
@section('title','매장재고')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장재고</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 매장관리</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="Add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>데이터업로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장구분</label>
							<div class="flax_box">
								<select name='store_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($com_types as $com_type)
										<option value='{{ $com_type->code_id }}'>{{ $com_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_nm">매장명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='store_nm' value=''>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">상품코드</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='prd_cd' value=''>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>

		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 데이터업로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>

	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
    const columns = [
        {headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: {"line-height": "40px"}},
        {
            field: "goods_no",
            headerName: "상품번호",
            width: 58,
            pinned: 'left',
            cellStyle: {"line-height": "40px"},
            cellRenderer: function (params) {
                if (params.value) {
                    return `<a href="{{config('shop.front_url')}}/app/product/detail/${params.value}" target="_blank">${params.value}</a>`
                }
            }
        },
        {field: "prd_cd", headerName: "상품코드", cellStyle: {"line-height": "40px"}},
        {field: "goods_type", headerName: "상품구분", width: 58, pinned: 'left', type: 'StyleGoodsTypeNM'},
        {field: "opt_kind_cd", headerName: "품목", width:96, cellStyle: {"line-height": "40px"}},
        {field: "brand_nm", headerName: "브랜드", cellStyle: {"line-height": "40px"}},
        {field: "style_no", headerName: "스타일넘버", width: 120, cellStyle: {"line-height": "40px"}},
        {field: "sale_stat_cl", headerName: "상품상태", width:70, type: 'GoodsStateTypeLH50'},
        {field: "img", headerName: "이미지", type: 'GoodsImageType', width:60, cellStyle: {"line-height": "40px"}, surl:"{{config('shop.front_url')}}"},
        {field: "img", headerName: "이미지_url", hide: true},
        {field: "goods_nm", headerName: "상품명", type: 'HeadGoodsNameType', width: 230, cellStyle: {"line-height": "40px"}},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 230, cellStyle: {"line-height": "40px"}},
        {field: "goods_opt", headerName: "옵션", cellStyle: {"line-height": "40px"}},
        {field: "store_nm", headerName: "매장", width: 170, cellStyle: {"line-height": "40px"}},
        {field: "barcode", headerName: "바코드", cellStyle: {"line-height": "40px"}},
        {
            field: "wqty", headerName: "보유재고수", width:70, type: 'numberType', cellStyle: {"line-height": "40px"},
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openHeadStock(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                }
            }
        },
        {field: "rt", headerName: "등록일자", width:110, cellStyle: {"line-height": "40px"}},
        {field: "ut", headerName: "수정일자", width:110, cellStyle: {"line-height": "40px"}},
		{width: "auto"}
    ];

	function Add()
	{
		const url='/head/xmd/store/store01/show';
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();
	});
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk01/search', data,1);
	}

	/**
     * 고객명 팝업 api
     */
    var goodsCallback = (row) => {
        const { name } = row;
        $('#customer').val(name);
    };

    const getStores = () => {
        const url=`/store/api/stores`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=800,height=800");
    };

</script>
@stop
