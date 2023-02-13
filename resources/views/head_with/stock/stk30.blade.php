@extends('head_with.layouts.layout')
@section('title','XMD 상품매칭')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">XMD 상품매칭</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 재고</span>
        <span>/ XMD</span>
        <span>/ 상품매칭</span>
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
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="good_types">상품구분 :</label>
                            <div class="flax_box">
								<select name='s_goods_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_types as $goods_types)
										<option value='{{ $goods_types->code_id }}'>{{ $goods_types->code_val }}</option>
									@endforeach
								</select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품상태 :</label>
                            <div class="form-inline inline_input_box">
								<div class="form-inline-inner input-box w-75 pr-1">
									<select name='s_goods_stat' class="form-control form-control-sm" style="width:100%;">
										<option value=''>전체</option>
										@foreach ($goods_stats as $goods_stats)
											<option value='{{ $goods_stats->code_id }}'>{{ $goods_stats->code_val }}</option>
										@endforeach
									</select>
								</div>
                                <div style="height:30px;margin-left:5px;">
                                    <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="휴지통 제외">
                                        <input type="checkbox" class="custom-control-input" id="s_ex_trash" name="s_ex_trash" value="Y" checked>
                                        <label for="s_ex_trash" data-on-label="Yes" data-off-label="No" style="margin-top:2px;"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">스타일넘버/상품코드 :</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='s_style_no' id="s_style_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100" name='s_goods_no' id='s_goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos" data-name="s_goods_no"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">업체 :</label>

                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-25 pr-1">
                                    <select id="s_com_type" name="s_com_type" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        @foreach ($com_types as $com_type)
                                            <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box w-75">
                                    <div class="form-inline inline_btn_box">
										<select id="s_com_id" name="s_com_id" class="form-control form-control-sm select2-company" style="width:100%;"></select>
										<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">브랜드 :</label>
                            <div class="form-inline inline_btn_box">
                                <select id="s_brand_cd" name="s_brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-secondary sch-brand">...</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">판매처 :</label>
                            <div class="flax_box">
                                <select name="s_site" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($sites as $item)
                                        <option value="{{ $item->com_id }}">{{ $item->com_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">매칭여부 :</label>
                            <div class="flax_box">
                                <select name="s_match" class="form-control form-control-sm">
                                    <option value="">전체</option>
									<option value="Y">예</option>
									<option value="N">아니오</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="item">자료수/정렬 :</label>
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
                                        <option value="g.goods_no" selected>상품번호</option>
                                        <option value="g.goods_nm" >상품명</option>
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
            <a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 추가</a>
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
				<div class="fr_box flax_box">
					<a href="#" onclick="ChangeData()" class="btn-sm btn btn-primary">옵션 및 코드 변경</a>
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
			width:30,
			pinned:'left',
		},
		{headerName: "#", field: "num",type:'NumType', width:35, cellStyle: {"background":"#F5F7F7"}},
		{field: "opt_kind_nm", headerName: "품목", width:84},
		{field: "brand_nm", headerName: "브랜드", width:84},
		{field: "goods_no", headerName: "상품번호", width: 58},
		{field: "style_no", headerName: "스타일넘버", width:96,
			cellRenderer: function(params) {
				return '<a href="/head/product/prd01/?style_no='+ params.value +'" target="new">'+ params.value+'</a>'
			}
		},
		{field: "sale_stat_cl_val", headerName: "상품상태", width:58, type:'GoodsStateType'},
		{field: "goods_nm", headerName: "상품명", width: 400, type:'HeadGoodsNameType'},
		{field: "goods_opt", headerName: "옵션", width: 170, editable: true, cellStyle:{"background-color":"#FFFF99"}, onCellValueChanged:checkData},
		{field: "qty", headerName: "재고수", width:46, type:'numberType',
			cellRenderer: function(params) {
			if (params.value !== undefined) {
				return `<a href="javascript:openHeadStock('` + params.data.goods_no + `','` + params.data.goods_opt + `')">` + params.value + `</a>`;
			}
		}
		},
		{field: "cd", headerName: "XMD 코드", width:120, editable: true, cellStyle:{"background-color":"#FFFF99"}, onCellValueChanged:checkData},
		{field: "org_opt", headerName: "기존 옵션", hide: true},
		{field: "org_cd", headerName: "기존 XMD 코드", hide: true},
        {field: "", headerName: "", width: "auto"}
	];

	function checkData(params){
		if (params.oldValue !== params.newValue) {
			var rowNode = params.node;
			rowNode.setSelected(true);
		}
	}

	function Add()
	{
        const url='/head/stock/stk30/show';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

    function PopGoods(item)
    {
		alert('개발중입니다.');
		//const url='/head/promotion/prm12/' + item;
		//window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

	function ChangeData()
	{
		var checkRows = gx.gridOptions.api.getSelectedRows();
        var not_matched_cnt	= 0;

		if( checkRows.length === 0 )
		{
            alert("수정할 데이터를 선택해주세요.");
            return;
		}
        
        checkRows.forEach((selectedRow, index) => {
			if( selectedRow.cd == "" || selectedRow.cd == null )
			{
                not_matched_cnt++;
			}
		});

        if( not_matched_cnt != 0)
        {
            alert("선택하신 데이터 중 XMD코드와 매칭되어 있지 않은 데이터가 있습니다. XMD코드와 매칭 후에 변경할 수 있습니다.");
			return;
        }
        else
        {
            if(confirm("옵션을 수정하면 기존에 연결된 매칭 데이터가 초기화 됩니다.\r\n선택하신 데이터를 수정하시겠습니까?")) 
            {

                console.log(JSON.stringify(checkRows));

                $.ajax({
                	async: true,
                	type: 'put',
                	url: '/head/stock/stk30',
                	data: {
                		data : JSON.stringify(checkRows),
                	},
                	success: function (data) {
                		if( data.code == "200" ){
                			alert("선택한 데이터가 수정 되었습니다.");
                			Search();
                		}else if( data.code == "401" ){
                			alert("이미 존재하는 코드 혹은 옵션 데이터 입니다.");
                		}else{
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
        gx.Request('/head/stock/stk30/search', data,1);
    }

</script>
@stop
