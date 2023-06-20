@extends('store_with.layouts.layout')
@section('title','매장별할인율적용조회')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장별할인율적용조회</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 매장관리</span>
		<span>/ 매장별할인율적용조회</span>
	</div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch(['#store_no'])">검색조건 초기화</a> -->
                    <a href="javascript:void(0);" onclick="downlaodExcel()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-download fa-sm text-white-50 mr-1"></i> 엑셀다운로드</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>할인적용기간</label>
                            <div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ @$sdate }}" autocomplete="off">
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
							<label>매장</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
                                <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2" onclick="initSearch()">검색조건 초기화</a> -->
            <a href="javascript:void(0);" onclick="downlaodExcel()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-download fa-sm text-white-50 mr-1"></i> 엑셀다운로드</a>
        </div>
    </form>

    <style>
        .sale_table {border:1px solid #ddd;}
        .sale_table>div {width:100%;}
        .sale_table>div:not(:last-child) {border-right:1px solid #ddd;}
        .sale_table>div>p {padding: 7px 0;text-align:center;font-size:14px;font-weight:500;border-bottom:1px solid #ddd;background-color: #f2f2f2;}
        
        @media (max-width: 992px) {
            .sale_table>div:not(:last-child) {border-right: unset;}
            .sale_table>div>p {padding:0;min-width:160px;display:flex;align-items:center;justify-content:center;border-right:1px solid #ddd;}
            .sale_table>div:last-child>p {border-bottom: 0;}
            .sale_table>div>p+div {width:100%;}
            .sale_table>div:not(:last-child)>p+div {border-bottom:1px solid #ddd;}
        }

        input:read-only {background-color: #f2f2f2 !important;}
    </style>

    <div class="card mb-3">
        <div class="d-flex card-header justify-content-between">
            <h4>할인율 적용내역</h4>
        </div>
        <div class="card-body pt-2">
            <div class="sale_table d-flex flex-column flex-lg-row justify-content-between">
                <div class="d-flex flex-row flex-lg-column">
                    <p>할인적용구분</p>
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <div id="sale_types" class="form-control-sm d-flex align-items-center"></div>
                    </div>
                </div>
                <div class="d-flex flex-row flex-lg-column">
                    <p>전체판매금액</p>
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <input type='text' class="form-control form-control-sm text-right" name='total_sale_amt' readonly>
                        <span class="ml-1">원</span>
                    </div>
                </div>
                <div class="d-flex flex-row flex-lg-column">
                    <p>전체할인가능금액 (<span id="apply_rate" class="text-primary">0</span>%)</p>
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <input type='text' class="form-control form-control-sm text-right" name='total_dc_amt' readonly>
                        <span class="ml-1">원</span>
                    </div>
                </div>
                <div class="d-flex flex-row flex-lg-column">
                    <p>할인판매된금액</p>
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <input type='text' class="form-control form-control-sm text-right" name='dc_price' readonly>
                        <span class="ml-1">원</span>
                    </div>
                </div>
                <div class="d-flex flex-row flex-lg-column">
                    <p>남은할인가능금액</p>
                    <div class="d-flex justify-content-center align-items-center p-2">
                        <input type='text' class="form-control form-control-sm text-right" name='left_dc_price' readonly>
                        <span class="ml-1">원</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "ord_no", headerName: "주문번호", pinned: "left", width: 130, cellStyle: {"text-align": "center"}, cellClass: "stringType"},
        {field: "ord_opt_no", headerName: "일련번호", pinned: "left", width: 60, cellStyle: {"text-align": "center"}},
        {field: "ord_date", headerName: "주문일자", pinned: "left", width: 80, cellStyle: {"text-align": "center"}},
        {field: "ord_state_cd", hide: true},
        {field: "ord_state", headerName: "주문상태", pinned: "left", cellStyle: StyleOrdState},
        {field: "prd_cd", headerName: "바코드", pinned: "left", width: 110, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", pinned: "left", width: 60, cellStyle: {"text-align": "center"}},
        // {field: "goods_type_nm", headerName: "상품구분", width: 60, cellStyle: StyleGoodsType},
        {field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: {"text-align": "center"}},
        {field: "brand_nm", headerName: "브랜드", width: 60, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 80, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 230},
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 230},
        {field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: {"text-align": "center"}	},
		{field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
		{field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 150},
        {field: "qty", headerName: "판매수량", width: 60, type: "currencyType"},
        {field: "price", headerName: "판매가", width: 60, type: "currencyType"},
        {field: "sale_per", headerName: "할인율(%)", width: 70, type: "currencyType"},
        {field: "dc_price", headerName: "할인가", width: 60, type: "currencyType"},
        {field: "sale_kind_cd", hide: true},
        {field: "sale_kind_nm", headerName: "판매유형", width: 100, cellStyle: {"text-align": "center"}},
        {width: 'auto'}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        initStore();
    });

	function Search() {
        if(!$("[name=store_no]").val()) return alert("조회할 매장을 선택해주세요.");

		let data = $('form[name="search"]').serialize();
		gx.Request('/store/stock/stk25/search', data, -1, function(e) {
            // 할인적용구분
            let html = "";
            for(let i = 0; i < e.head.sale_types.length; i++) {
                if(i > 0) html += ', ';
                html += e.head.sale_types[i].sale_type_nm;
            }
            if(!html) html = "<span class='fs-18 fw-bold'>-</span>";
            $("#sale_types").html(html);

            // 계산금액 표시
            let amt = e.head.amts;
            $("[name=total_sale_amt]").val(Comma(amt.total_sale_amt || 0));
            $("[name=total_dc_amt]").val(Comma(amt.total_dc_amt || 0));
            $("[name=dc_price]").val(Comma(amt.dc_price || 0));
            $("[name=left_dc_price]").val(Comma(amt.left_dc_price || 0));
            $("#apply_rate").text(amt.apply_rate || 0);

            if(amt.left_dc_price < 0) {
                $("[name=left_dc_price]").addClass("text-danger font-weight-bold fs-18");
            } else {
                $("[name=left_dc_price]").removeClass("text-danger font-weight-bold fs-18");
            }
        });
	}

    // 엑셀다운로드
    function downlaodExcel() {
        gx.gridOptions.api.exportDataAsExcel({
            skipHeader: false,
            skipPinnedTop: false,
        });
    }

    // 특정매장 파라미터로 전달 시 매장정보 초기화
    function initStore() {
        const store_cd = '{{ @$store->store_cd }}';
        const store_nm = '{{ @$store->store_nm }}';

        if(store_cd != '') {
            const option = new Option(store_nm, store_cd, true, true);
            $('#store_no').append(option).trigger('change');
            Search();
        }
    }
</script>
@stop
