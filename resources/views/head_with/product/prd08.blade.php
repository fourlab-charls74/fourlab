@extends('head_with.layouts.layout')
@section('title','베스트 랭킹')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">베스트 랭킹</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 베스트 랭킹</span>
    </div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
        			<div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
                </div>
			</div>
			<div class="card-body">
				<!-- 주문일자/성별/상품상태 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">주문일자 :</label>
							<div class="form-inline inline_input_box">
								<select name="date_type" id="date_type" class="form-control form-control-sm" style="width:auto;">
									<option value="1d">전일</option>
									<option value="1w">최근1주</option>
									<option value="2w">최근2주</option>
									<option value="1m">최근1달</option>
									<option value="3m">최근3달</option>
									<option value="1y">최근1년</option>
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">성별 :</label>
							<div class="flax_box">
								<select name="s_sex" id="s_sex" class="form-control form-control-sm" style="width: 70px">
									<option value="">전체</option>
									@foreach($sex_types as $sex_type)
										<option value="{{ $sex_type->code_id }}">{{ $sex_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">상품상태 :</label>
							<div class="flax_box">
								<select name="goods_stat" id="goods_stat" class="form-control form-control-sm" style="width:auto;">
									<option value="">전체</option>
									@foreach($goods_stats as $goods_stat)
										<option value="{{ $goods_stat->code_id }}">{{ $goods_stat->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

				</div>

				<!-- 품목/브랜드/업체 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">품목 :</label>
							<div class="form-inline inline_input_box">
								<select name="opt_kind_cd" id="opt_kind_cd" class="form-control form-control-sm" style="width:auto;">
                                    <option value="">전체</option>
                                    @foreach($opt_kind_cd_items as $opt_kind_cd)
                                        <option value="{{ $opt_kind_cd->id }}">{{$opt_kind_cd->val}}</option>
                                    @endforeach
                                </select>

							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">브랜드 :</label>
							<div class="flax_box">
								<input type=text class='form-control form-control-sm search-all search-enter ac-brand2' name='brand_nm' id='brand_nm' value='' style='width:130px;' autocomplete='off' >
								<a href="#" class="btn btn-sm btn-secondary brand-add-btn">...</a>

								<!--<input type="button" name="brand_btn" class="brand-add-btn" value="..." onclick="PopSearchBrand('search');">-->
								<input type=text class='form-control form-control-sm search-all search-enter' name='brand_cd' id='brand_cd' value='' style='width:60px;' readonly>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">업체 :</label>
							<div class="flax_box">
								<input type=text  class="form-control form-control-sm search-all search-enter ac-company2" name='com_nm' id='com_nm' value='' style='width:100px;' autocomplete='off' >
								<a href="#" class="btn btn-sm btn-secondary company-add-btn">...</a>
								<!--<input type="button" name="btnSchCom" value="..." onclick="PopSearchCompany('PT=S&ISCLOSE=Y');">-->
								<input type=text  class="form-control form-control-sm search-all search-enter" name='com_id' id='com_id' value='' style='width:60px;' readonly>
							</div>
						</div>
					</div>

				</div>


				<!-- 대표카테고리/상품명/출력 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">대표카테고리</label>
                            <div class="flax_box inline_btn_box">
                                <input type="hidden" name="cat_cd" id="cat_cd">
                                <input type="text" name="cat_nm" id="cat_nm" class="form-control form-control-sm">
                                <a href="#" class="btn btn-sm btn-outline-primary sch-category"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">상품명 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter ac-goods-nm" name='goods_nm' value=''>


							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">출력 :</label>
							<div class="flax_box">
								<select  name="limit" class="form-control form-control-sm" style="width:auto;">
									<option value=100>100</option>
									<option value=500>500</option>
									<option value=1000>1000</option>
									<option value=2000>2000</option>
									<option value=>모두</option>
								</select>&nbsp;
							</div>
						</div>
					</div>

				</div>


				<div class="resul_btn_wrap d-sm-none">
					<a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
				</div>
			</div>
		</div>
	</div>
</form>

<form name="f1">

<div id="filter-area" class="card shadow-none search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
					<input type="checkbox" name="checkAll" id="checkAll">전체선택 &nbsp;

					<a href="#" class="btn-sm btn btn-primary confirm-clm-no-btn point-btn">저장</a>
                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
</form>


<script>

	const CELL_COLOR = {
        YELLOW: { 'background' : '#ffff99' }
	};

	var columns = [
		{headerName: '#', width:50,type:'NumType', pinned:'left',},
		{
		  field: "blank",
		  headerName: '',
		  checkboxSelection: true, pinned:'left',
		  headerCheckboxSelectionFilteredOnly: true,
		  width: 28,
		},

		{field:"img" , headerName:"이미지", pinned:'left', height:50,
			cellRenderer: function (params) {
				if (params.value !== undefined && params.value !== "" && params.value !== null) {
					return '<img src="{{config('shop.image_svr')}}/' + params.value + '" class="img" style="width:50px; height:auto;"/>';
				}
			}
		},
		{field:"goods_nm" , headerName:"상품명", pinned:'left', width:230, type: "HeadGoodsNameType"},
		{field:"code_val" , headerName:"상품상태", pinned:'left',},
		{field:"rank" , headerName:"순위", pinned:'left',},
		{field:"variation" , headerName:"순위증감", pinned:'left', width:80},
		{field:"sale_point" , headerName:"판매점수", pinned:'left', width:80},
		{
			field:"admin_point" , headerName:"관리자 점수", pinned:'left', width:100, editable: true, type: 'currencyType',
			cellStyle: CELL_COLOR.YELLOW
		},
		{field:"pre_point" , headerName:"예상총점", pinned:'left', width:80},
		{field:"point" , headerName:"총점"},

		{headerName:"조회수", width:120,
			children : [
				{
					headerName : "누적",
					field : "clm_sum",
				},
				{
					headerName : "기간",
					field : "clm_avg",
				}
			]
		},
		{headerName:"상품평",
			children : [
				{
					headerName : "누적",
					field : "review",
				},
				{
					headerName : "기간",
					field : "review_avg",
				}
			]
		},
		{headerName:"상품QA",
			children : [
				{
					headerName : "누적",
					field : "qa",
				},
				{
					headerName : "기간",
					field : "qa_avg",
				}
			]
		},
		{field:"goods_no", headerName:"goods_no", hide:true},
		{field:"goods_sub", headerName:"goods_sub", hide:true},
		{ width: "auto" }
	];
	const pApp = new App('', { gridId: "#div-gd" });


	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);
	//gx.gridOptions.suppressRowClickSelection = true;
	//gx.gridOptions.suppressExcelExport = true;

	pApp.ResizeGrid(265);
	pApp.BindSearchEnter();

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/product/prd08/search', data,1, callBack);
	}

	function callBack(data){
		// console.log(data);
	}

	function checkAll(){
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var displayRowCnt = gx.gridOptions.api.getDisplayedRowCount();

		if(selectedRowData.length == displayRowCnt){
			gx.gridOptions.api.deselectAll();
		}else{
			gx.gridOptions.api.selectAll();
		}
	}

	function PopCategoryMulti(param){
        var url = "";
        var cat_type = "";
        cat_type = "DISPLAY";

        //url = "/head/standard/std04/cate_pop/?cat_type="+cat_type +"&"+ param;
        if(param!= ""){
            url = "/head/api/category/"+cat_type +"?"+param;
        }else{
            url = "/head/api/category/"+cat_type;
        }
        const CATE_POP=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=580,height=600");
    }

	/*
    * 변경 또는 복사할 전시 카테고리 세팅
    */
    //function SetRepCategory(idx, text, mx_len) {
	function SetDCategory (idx, text, mx_len){

        //미설정 카테고리 변경 못함.
        if(idx == "000"){
            alert("미설정 카테고리로는 선택할 수 없습니다.\n다른 카테고리를 선택해 주십시오.");
            return false;
        }

		$("#rep_cat_nm").html(text);
		$("#rep_cat_cd").val(idx);
		$("#delete_link").show();
		/*
        $("#chg_d_cat_cd").val(idx);
        $("[name=chg_d_cat_nm]").val(text);
		*/
    }

	function DeleteValue(){
		document.getElementById("rep_cat_nm").innerText = '';
		document.getElementById("rep_cat_cd").value = '';
		document.getElementById("delete_link").style.display = 'none';
	}


	function SaveCmd(){
		var selectedRows = gx.gridOptions.api.getSelectedRows();
		
		let data = [];
		selectedRows.map((item, index) => {
			data.push([item.goods_no, item.goods_sub, item.admin_point]);
		});

		if (data == "") {
			alert("변경할 데이터가 없습니다.");
			return false;
		}
		var cmd = "save_point";

		$.ajax({
            async: true,
            type: 'put',
            url: '/head/product/prd08/save_point/',
            data: {
				'cmd': cmd,
				'data' : data
            },
            success: function (data) {
                if(data.return_code == 1){
					Search();
                }else{
					alert("저장 시 장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
                }
            },
            complete:function(){
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
	}



	$(function(){

		$(".ac-brand2")
        .autocomplete({
            //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
            source : function(request, response) {
                $.ajax({
                    method: 'get',
                    url: '/head/auto-complete/brand',
                    data: {
						"keyword" : this.term
					},
                    success: function (data) {
						//console.log(data);
                        response(data);
                    },
                    error: function(request, status, error) {
                        console.log("error");
                        console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            delay: 100,
            focus: function(event, ui) {
            },
            select:function(event,ui){
				//console.log(ui.item);
				$("#brand_cd").val(ui.item.id);
            }

        });

		$('.ac-company2').autocomplete({
			//keydown 됬을때 해당 값을 가지고 서버에서 검색함.
			source : function(request, response) {
				$.ajax({
					method: 'get',
					url: '/head/auto-complete/company',
					data: { keyword : this.term },
					success: function (data) {
						response(data);
					},
					error: function(request, status, error) {
						console.log("error")
					}
				});
			},
			minLength: 1,
			autoFocus: true,
			delay: 100,
			select:function(event,ui){
				//console.log(ui.item);
				$("#com_id").val(ui.item.id);
            }
		});


		$('.brand-add-btn').click((e) => {
            e.preventDefault();

            searchBrand.Open((code, name) => {
                if (confirm("선택한 브랜드를 추가하시겠습니까?") === false) return;

				$("#brand_cd").val(code);
				$("#brand_nm").val(name);

            });
        });

		$(".company-add-btn").click((e) => {
			e.preventDefault();

            searchCompany.Open((code, name) => {
                if (confirm("선택한 업체를 추가하시겠습니까?") === false) return;

				$("#com_nm").val(name);
				$("#com_id").val(code);

            });
		});
		/*
		$(".cate-add-btn").click((e) => {
			e.preventDefault();

            searchCategory.Open((code, name) => {
                if (confirm("선택한 카테고리를 추가하시겠습니까?") === false) return;

				$("#rep_cat_nm").html(name);
				$("#rep_cat_cd").val(code);

            });
		});
		*/
		$(".cate-add-btn").click(function(){

			PopCategoryMulti('');
		});

		$("#checkAll").click(function(){
			checkAll();
		});

		$(".point-btn").click(function(){
			SaveCmd();
		});

	});
</script>

@stop
