@extends('head_with.layouts.layout')
@section('title','XMD 상품 재고예외 관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">XMD 상품 재고예외 관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 재고</span>
        <span>/ XMD</span>
        <span>/ 재고예외 관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">

            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 다운로드</a>
                    <a href="/head/stock/stk32" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> XMD 재고파일 관리</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

			<div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품코드 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='s_goods_code' value=''>
							</div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                          <label for="">분류(정보) :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='s_comment' value=''>
							</div>
                        </div>
                    </div>
                </div>

			</div>
        </div>

        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
			<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 다운로드</a><br><br>
			<a href="/head/stock/stk32" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> XMD 재고파일 관리</a>
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
				<div class="fr_box flax_box" style="color:#FF0000;font-weight:bold;">
					<div class="mr-1">※ 물류, 매장의 수량을 입력하지 않으면 예외처리 안함</div>
					<a href="#" onclick="ChangeData()" class="btn-sm btn btn-primary">선택 정보 변경</a>
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
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width:28,
			pinned:'left',
		},
		{
			headerName: '#',
			width:35,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			pinned:'left'
		},
        {headerName: "상품코드", field: "goods_code", width:150, editable: true, cellStyle:{"background-color":"#FFFF99"}},
        {headerName: "물류", field: "bonsa_cnt", width:58, editable: true, cellStyle:{"text-align":"right","background-color":"#FFFF99"}},
        {headerName: "매장", field: "store_cnt", width:58, editable: true, cellStyle:{"text-align":"right","background-color":"#FFFF99"}},
        {headerName: "정보", field: "comment", width:220, editable: true, cellStyle:{"background-color":"#FFFF99"}},
        {headerName: "삭제", field: "del", width:58, cellStyle:{"text-align":"center"},
            cellRenderer: function(params) {
				return '<a href="#" onClick="Del(\''+ params.data.idx +'\')">'+ params.value+'</a>'
            }
		},
        {headerName: "IDX", field: "idx", hide:true},
		{headerName:"", field:"", width:"auto"}
    ];

	function Add()
	{
        const url='/head/stock/stk31/show';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=330");
	}

	function Del(item)
	{
		ret	= confirm("삭제 하시겠습니까?");

		if( ret )
		{
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/stock/stk31/delete',
				data: {
					idx : item,
				},
				success: function (data) {
					if( data.code == "200" )
					{
						alert("삭제 되었습니다.");
						Search();
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

	function ChangeData()
	{
		var checkRows = gx.gridOptions.api.getSelectedRows();

		if( checkRows.length === 0 )
		{
            alert("수정할 데이터를 선택해주세요.");
            return;
		}

		if(confirm("선택하신 데이터를 수정하시겠습니까?")) 
		{
			console.log(checkRows);
			console.log(JSON.stringify(checkRows));
			//return;

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/stock/stk31',
				data: {
					data : JSON.stringify(checkRows),
				},
				success: function (data) {
					if( data.code == "200" )
					{
						alert("선택한 데이터가 수정 되었습니다.");
						Search();
					} 
					else 
					{
						alert("데이터 수정이 실패하였습니다.");
					}
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					console.log("error")
				}
			});
		}

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
        gx.Request('/head/stock/stk31/search', data);
    }

</script>
@stop
