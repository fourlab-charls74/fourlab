@extends('head_with.layouts.layout')
@section('title','XMD 재고등록 오류관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">XMD 재고등록 오류관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 재고</span>
        <span>/ XMD</span>
        <span>/ 오류관리</span>
    </div>
</div>

<form method="get" name="search">
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
                            <label for="good_types">XMD 상품명 :</label>
                            <div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='s_goods_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">Bizest 상품명 :</label>
                            <div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='s_goods_nm_b' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상품상태 :</label>
							<div class="flax_box">
								<select id="s_sale_stat_cl" name='s_sale_stat_cl' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_stats as $goods_stat)
										<option value='{{ $goods_stat->code_val }}'>{{ $goods_stat->code_val }}</option>
									@endforeach
								</select>
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
			<div id="div-gd" class="ag-theme-balham" style="min-height:300px;width:100%;"></div>
		</div>
	</div>
</div>

<div class="card shadow mb-0">
	<div class="card-body">
		<div class="card-title">
			<h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
		</div>
		<ul class="mb-0">
			<li><span style="color:#FF0000;font-weight:bold;">[비매칭]</span> : XMD 상품과 BIZEST 상품이 연결되어 있지 않습니다. XMD 재고가 0 이상인 경우에는 반드시 BIZEST 상품매칭 데이터를 먼저 등록해야 합니다.</li>
			<li><span style="color:#FF0000;font-weight:bold;">[상품명 불일치]</span> : XMD와 BIZEST의 상품명이 일치하지 않습니다. 단순 비교에 대한 항목으로 완전히 상이 상품명의 데이터는 검토가 필요합니다.</li>
			<li><span style="color:#FF0000;font-weight:bold;">[재고요약 오류]</span> : 정상적으로 재고가 등록 되었으나 BIZEST 시스템의 문제로 재고가 표현되지 않았습니다. 시스템 관리자에게 문의해 주시길 바랍니다.</li>
			<li><span style="color:#FF0000;font-weight:bold;">[수량 불일치]</span> : XMD와 BIZEST의 재고가 일치하지 않습니다. 재고 업로드 시점에서는 반드시 일치해야 합니다.</li>
			<li><span style="color:#FF0000;">"판매중"이 아니면서 재고가 존재하는 상품은 반드시 상품 상태를 변경해야 합니다.</span></li>
			<li><span style="color:#FF0000;">키즈 관련 상품은 노출하지 않습니다.</span></li>
		</ul>
	</div>
</div>

<script language="javascript">
    var columns = [
        {headerName: "#", field: "num", width:35, type:'NumType', cellStyle: {"background":"#F5F7F7"}},
		{field: "goods_no",		headerName: "상품번호",		width:100},
		{field: "xmd_goods_nm", headerName: "XMD 상품명",	width:250},
		{field: "goods_nm",		headerName: "Bizest 상품명",width:250},
		{field: "goods_opt",	headerName: "상품옵션",		width:180},
		{field: "qty",			headerName: "XMD 수량",		width:100, type: 'currencyType'},
		{field: "good_qty",		headerName: "Bizest 수량",	width:100, type: 'currencyType'},
		{field: "sale_stat_cl",	headerName: "상품상태",		width:80, type:'GoodsStateType'},
		{field: "match_yn",		headerName: "매칭유무",		width:80, cellStyle:{"text-align":"center"}},
		{field: "chk_cmt",		headerName: "비고",			width:300, cellStyle:{"color":"#FF0000"}},
    ];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(445);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/stock/stk34/search', data);
    }

</script>
@stop
