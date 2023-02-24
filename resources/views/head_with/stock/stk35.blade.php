@extends('head_with.layouts.layout')
@section('title','XMD 재고 모니터링')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">XMD 재고 모니터링</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 재고</span>
        <span>/ XMD</span>
        <span>/ 재고 모니터링</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">

            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="Delete()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 초기화</a>
                    <a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 매장재고등록</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품상태 :</label>
                            <div class="flax_box">
								<select name='s_goods_stat' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_stats as $goods_stats)
										<option value='{{ $goods_stats->code_id }}'>{{ $goods_stats->code_val }}</option>
									@endforeach
								</select>
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
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">품목 :</label>
                            <div class="flax_box">
                                <select name="s_opt_kind_cd" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">상품명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">온라인재고(~이하)</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='bizest_qty' value=''>
							</div>
						</div>
                    </div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">재고차이(~이상)</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='qty_buffer_cnt' value=''>
							</div>
						</div>
					</div>
                </div>

                <div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">상품별/옵션별</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="qty_type" id="qty_type1" class="custom-control-input" checked="" value="goods">
									<label class="custom-control-label" for="qty_type1" value="goods">상품별</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="qty_type" id="qty_type2" class="custom-control-input" value="opt">
									<label class="custom-control-label" for="qty_type2" value="opt">옵션별</label>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">매장재고 제외</label>
							<div class="form-inline form-check-box">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="exp0" id="exp0" class="custom-control-input" value="Y" checked>
									<label class="custom-control-label" for="exp0">매장재고 0 제외</label>
								</div>
							</div>
						</div>
                    </div>
                </div>

            </div>
        </div>

        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Delete()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 초기화</a>
            <a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 매장재고등록</a>
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
        {headerName: "#", field: "num", width:35, type:'NumType', cellStyle: {"background":"#F5F7F7"}},
		{field: "goods_no", headerName: "상품코드", width: 65, type:'HeadGoodsNameType', cellStyle: {"text-align":"center"}},
		{field: "head_desc", headerName: "XMD 코드", width:180},
		{field: "goods_nm", headerName: "상품명", width: 320, type:'HeadGoodsNameType'},
		{field: "goods_opt", headerName: "옵션", width: 220},
		{field: "sale_stat_cl_val", headerName: "상품상태", width: 65, type:'GoodsStateType'},
		{field: "xmd_qty", headerName: "매장재고", width: 70, type:'numberType'},
		{field: "bizest_qty", headerName: "온라인재고", width: 70, type:'numberType'},
		{field: "qty_term", headerName: "비교", width: 65, type:'numberType'},
		{field: "month_ord", headerName: "최근3개월", width: 65, type:'numberType'},
		{field: "tot_ord", headerName: "총주문", width:65, type:'numberType'},
		{field: "rt", headerName: "등록일", width:80},
		{field:"", headerName:"", width:"auto"}
    ];

	function Add()
	{
        const url='/head/stock/stk35/show';
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
				url: '/head/stock/stk35/delete',
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
        gx.Request('/head/stock/stk35/search', data);
    }
</script>
@stop
