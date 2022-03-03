@extends('head_with.layouts.layout')
@section('title','XMD 재고파일 관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">XMD 재고파일 관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 재고</span>
        <span>/ XMD</span>
        <span>/ XMD 재고파일 관리</span>
    </div>
</div>


<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">

            <div class="d-flex card-header justify-content-between">
                <h4>재고상태</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" onclick="Delete()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 초기화</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 다운로드</a>
                    <a href="/head/stock/stk31" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> XMD상품 재고예외 관리</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

			<div class="card-body">
				<div class="row">
                    <div class="col-lg-12 inner-td">
                        <div style="text-align:center;">
							<label for="name">변환된 매장 : </label>
							<a href="#" id="bonsa" class="d-none search-area-ext d-sm-inline-block btn btn-sm 
								@if($bonsa_jeago_yn == 'Y')	
									btn-primary 
								@else 
									btn-secondary
								@endif
							shadow-sm" style="padding:5px 20px;font-size:16px;">물류 창고</a>
							@foreach ($store_info as $list)
							<a href="#" id="{{$list['store_cd']}}" class="d-none search-area-ext d-sm-inline-block btn btn-sm 
								@if($list['store_jeago_yn'] == 'Y')	
									btn-primary 
								@else 
									btn-secondary
								@endif
							shadow-sm" style="padding:5px 20px;font-size:16px;">{{$list['store_nm']}}</a>
							@endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 inner-td">
                        <div style="text-align:center;">
							※ 회색 박스는 변환작업이 완료되지 않은 창고(매장) 입니다. 
                        </div>
                    </div>
                </div>

			</div>
        </div>
    
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
			<a href="#" onclick="Delete()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 초기화</a>
			<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 다운로드</a><br><br>
			<a href="/head/stock/stk31" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> XMD상품 재고예외 관리</a>
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
    var columns = [
		{field: "xmd_goods_code_full", headerName: "코드", width:160},
		{field: "goods_nm", headerName: "상품명", width:250},
		{field: "color_nm", headerName: "컬러", width: 160},
		{field: "price", headerName: "판매가", width:100, type: 'currencyType'},
		{field: "wonga", headerName: "원가", width:100, type: 'currencyType'},
		{field: "amt", headerName: "수량", width:50, type: 'currencyType'},
    ];

	function Add()
	{
        const url='/head/stock/stk32/show';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	function Delete()
	{
		ret	= confirm("기존데이터를 삭제 하시겠습니까?");

		if( ret )
		{
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/stock/stk32/delete',
				data: "",
				success: function (data) {
					if( data.code == "200" )
					{
						alert("삭제 되었습니다.");
						location.reload();
						//Search();
					} 
					else 
					{
						alert("삭제를 실패하였습니다.");
					}
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					console.log("error")
				}
			});
		}
	}

	function chgBox(store_cd)
	{
		$('#bonsa').removeClass("btn-primary");
		$('#bonsa').addClass('btn-secondary');
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
        gx.Request('/head/stock/stk32/search', data);
    }

</script>
@stop
