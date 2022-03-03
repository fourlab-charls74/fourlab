@extends('head_with.layouts.layout-nav')
@section('title','XMD 재고등록')
@section('content')

<div class="show_layout py-3">
    <!-- FAQ 세부 정보 -->
    <form method="get" name="search">

		<input type="HIDDEN" name="idx" value="{{$idx}}">
		<input type="HIDDEN" name="kind" value="{{$kind}}">

		<div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">XMD 재고등록</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									&nbsp;
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<!-- DataTales Example -->
		<div class="card shadow mb-4 last-card pt-2 pt-sm-0">
			<div class="card-body">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="height:calc(100vh - 290px);width:100%;" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>

	</form>

    <div class="resul_btn_wrap mt-3 d-block">
        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary submit-btn">검색</a>
        <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
    </div>

</div>

<script language="javascript">
    var columns = [
        {headerName: "#", field: "num", width:35, type:'NumType', cellStyle: {"background":"#F5F7F7"}},
		{field: "imp_idx",	headerName: "일련번호", width:80, cellStyle:{"text-align":"center"}},
		{field: "cd",		headerName: "코드", width:120},
		{field: "goods_nm",	headerName: "상품명", width:250},
		{field: "color",	headerName: "컬러", width:130},
		{field: "price",	headerName: "판매가", width:80, type: 'currencyType'},
		{field: "cost",		headerName: "원가", width:80, type: 'currencyType'},
		{field: "qty",		headerName: "수량", width:80, type: 'currencyType'},
		{field: "match_yn",	headerName: "매칭여부", width:80, cellStyle:{"text-align":"center"}},
    ];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
	const gridDiv = document.querySelector(pApp.options.gridId);
    let gx;

    $(document).ready(function() {
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid();
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/stock/stk33/detail_search', data);
    }

</script>
@stop
