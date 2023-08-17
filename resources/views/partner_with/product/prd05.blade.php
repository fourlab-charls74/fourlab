@extends('partner_with.layouts.layout')
@section('title','상품정보고시')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">상품정보고시</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품정보고시</span>
        </div>
    </div>
    <script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

    <form method="get" name="search" id="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <a href="#" onclick="onBtnExportDataAsExcel();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                        <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="SearchFormReset()">
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">분류</label>
                                <div class="flax_box">
                                    <select name="class" id="class" class="form-control form-control-sm">
                                    @foreach ($class_items as $class_item)
                                        <option value='{{ $class_item->class }}'>{{ $class_item->class_nm }}[{{ $class_item->cnt }}]</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <div class="flax_box">
                                    <div>
                                        <span style="font-size: 0.7rem; display:block;margin:3px 0;">* "공정거래위원회 제품분류"를 변경하면, 변경된 분류에 따라 그리드(리스트)의 컬럼(항목)이 자동으로 갱신됩니다.</span>
                                        <span style="font-size: 0.7rem; display:block;">* 선택된 <label style="color:red;" class="mb-0">공정거래위원회 제품분류</label> 와 <label style="color:red;" class="mb-0">상품의 분류</label>가 다를 경우, <label style="color:red;" class="mb-0">상품정보고시 항목 값의 순서가 달라질 수 있으므로 주의</label>하시기 바랍니다.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품구분</label>
                                <div class="flax_box">
                                    <select name='goods_type' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_types as $goods_type)
                                            <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품상태</label>
                                <div class="flax_box">
                                    <select name='goods_stat' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($goods_stats as $goods_stat)
                                            <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                       <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
                                    </div>
                                    <span class="text_line">/</span>
	                                <div class="form-inline-inner input-box" style="width:47%">
		                                <div class="form-inline-inner inline_btn_box">
			                                <input type='text' class="form-control form-control-sm search-enter w-100 search-enter" name='goods_no' id='goods_no' value=''>
			                                <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
		                                </div>
	                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">업체</label>
	                            <div class="flax_box">
		                            <input type="text" name="com_nm" class="form-control form-control-sm bg-light" value="[{{ $com_id }}] {{ $com_nm }}" readonly>
	                            </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">품목</label>
                                <div class="flax_box">
                                    <select name="item" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">({{ $item->cd }}){{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">브랜드</label>
                                <div class="form-inline">
                                    <div class="inbox" style="width:70%;">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" class="form-control form-control-sm search-all sch-brand" name="brand" id="brand_nm" value="" style="width:100%;">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-brand" onclick="PopSearchBrand('search');"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                    <input type="text" id="brand_cd" name="brand_cd" class="form-control form-control-sm" readonly style="width:calc(30% - 10px);margin-left:10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상, 하단홍보글</label>
                                <div class="flax_box">
                                    <input type="text" name="head_desc" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품명</label>
                                <div class="flax_box">
                                    <input type="text" name="goods_nm" class="form-control form-control-sm search-enter">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
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
                                            <option value="goods_no">상품번호</option>
                                            <option value="goods_nm">상품명</option>
                                            <option value="price">판매가</option>
                                            <option value="com_nm">공급업체</option>
                                            <option value="md_nm">담당MD별</option>
                                            <option value="upd_dm">수정일</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                        <input type="radio" name="ord" id="sort_asc" value="asc">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
	                <div class="row code_color_row" style="display: none;">
		                <div class="col-lg-12 inner-td">
			                <div class="form-group">
				                <label for="">입력 가능한 색상</label>
				                <div class="flax_box">
					                <div class="h5 mb-0 text-gray-800 text-xs">
						                <p class="color_title">
							                @for ($i=0; $i < count($product_colors); $i++ ) @if( $i> 0 )
								                , {{ $product_colors[$i]->val }}
							                @else
								                {{ $product_colors[$i]->val }}
							                @endif
							                @endfor
						                </p>
						                <b class="guide">* 색상을 여러개 입력할 경우 위의 예처럼 콤마(,)를 사용하여 색상을 등록합니다. </b>
					                </div>
				                </div>
			                </div>
		                </div>
	                </div>
	                <div class="row code_item_row" style="display: none;">
		                <div class="col-lg-12 inner-td">
			                <div class="form-group">
				                <label for="">미등록 항목</label>
				                <div class="d-flex">
					                <div id="code_item" class="form-inline form-radio-box flax_box txt_box"></div>
				                </div>
			                </div>
		                </div>
	                </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <!-- DataTales Example -->
    <form method="post" name="save" action="/partner/stock/stk01">
        <input type="hidden" name="goods_info" id="goods_info" value="">
        @csrf
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                        </div>
                        <div class="fr_box">
                            <div class="fl_inner_box">
                                <div class="box">
                                    <div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
                                        <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" onclick="ApplyToClass();" class="custom-control-input">
                                        <label class="custom-control-label text-left" for="chk_to_class">전체선택</label>
                                    </div>
                                </div>
                                <div class="box">
                                    <a href="#" id="del_btn" onclick="Cmder('delete');">선택삭제</a>
                                </div>
                            </div>
                            <div class="fl_inner_box">
                                <select id='to_class' name='to_class' class="form-control form-control-sm"  style='width:100px;display:inline'>
                                    <option value=''>선택</option>
                                    @foreach ($class_items as $class_item)
                                        <option value='{{ $class_item->class }}'>{{ $class_item->class_nm }}</option>
                                    @endforeach
                                </select>
                                <a href="#" id="save_btn"  onclick="Save();"  class="btn btn-sm btn-primary shadow-sm">저장</a>
                                <input type="hidden" name="data" id="data" value=""/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
    <style>
		/* 상품 이미지 사이즈 픽스 */
		img {
			height:30px;
		}
		.color_title {
			font-size: 0.8rem;
			font-weight: 400;
			line-height: 1.5;
		}
		.guide {
			font-size: 0.8rem;
			font-weight: 500;
		}
    </style>
    <script language="javascript">
        function formatNumber(params) {
            // this puts commas into the number eg 1000 goes to 1,000,
            // i pulled this from stack overflow, i have no idea how it works
            return Math.floor(params.value)
                .toString()
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }

        function StyleGoodsType(params){
            var state = {
                "P":"#F90000",
                "S":"#009999",
                "O":"#0000FF",
            }
            if (params.value !== undefined) {
                if (state[params.data.goods_type]) {
                    var color = state[params.data.goods_type];
                    return {
                        color: color
                    }
                }
            }
        }

        function StyleGoodsState(params){
            var state = {
                "판매중지":"#808080",
                "등록대기중":"#669900",
                "판매중":"#0000ff",
                "품절[수동]":"#ff0000",
                "품절":"#AAAAAA",
                "휴지통":"#AAAAAA"

            }
            if (params.value !== undefined) {
                if (state[params.value]) {
                    var color = state[params.value];
                    return {
                        color: color
                    }
                }
            }
        }
        function StyleChangeYN(params){
            if(params.value !== undefined){
                var chg_yn = params.data[params.colDef.field + '_chg_yn'];
                if(chg_yn !== undefined && chg_yn == 'Y'){
                    return {
                        color: 'red'
                    }
                }
            }
        }

        function formatNumber(params) {
            // this puts commas into the number eg 1000 goes to 1,000,
            // i pulled this from stack overflow, i have no idea how it works
            if (params.value !== undefined) {
                return Math.floor(params.value)
                    .toString()
                    .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>
    <script language="javascript">
		var columns = [
			// this row shows the row index, doesn't use any data from the row
			{
				headerName: '',
				pinned: 'left',
				headerCheckboxSelection: true,
				checkboxSelection: true,
				width: 28,
			},
			{
				headerName: '#',
				pinned: 'left',
				width: 35,
				maxWidth: 100,
				// it is important to have node.id here, so that when the id changes (which happens
				// when the row is loaded) then the cell is refreshed.
				valueGetter: 'node.id',
				cellRenderer: 'loadingRenderer',
			},
			{
				field: "goods_type",
				headerName: "상품구분",
				width: 72,
				cellStyle: StyleGoodsTypeNM,
			},
			{
				field: "com_nm",
				headerName: "업체",
				minWidth: 80,
				width: 80,
			},
			{
				field: "opt_kind_nm",
				headerName: "품목",
				minWidth: 80,
				width: 80,
			},
			{
				field: "brand_nm",
				headerName: "브랜드",
				minWidth: 80,
				width: 80,
			},
			{
				field: "style_no",
				headerName: "스타일넘버",
				minWidth: 80,
				width: 80,
			},
			{
				field: "img2",
				headerName: "img2",
				hide: true,
			},
			{
				field: "img",
				headerName: "이미지",
				width:46,
				cellRenderer: function(params) {
					if (params.value !== undefined && params.data.img != "") {
						return '<img src="{{config('shop.image_svr')}}/' + params.data.img + '" style="height:30px;"/>';
					}
				},
			},
			{
				field: "goods_nm",
				headerName: "상품명",
				type: "GoodsNameType",
				minWidth: 150,
				width: 150,
			},
			{
				field: "sale_stat_cl",
				headerName: "상품상태",
				minWidth: 80,
				width: 80,
				cellStyle: StyleGoodsState,
			},
			{
				field: "goods_no",
				headerName: "상품번호",
				minWidth: 80,
				width: 80,
				cellRenderer: function(params) {
					if (params.value !== undefined) {
						return params.data.goods_no + ' [' + params.data.goods_sub + ']';

					}
				},
			},
			{
				field: "goods_sub",
				headerName: "goods_sub",
				hide: true,
			},
			{
				field: "class",
				headerName: "분류",
				minWidth: 80,
				width: 80,
			},

			{
				field: "item_001",
				headerName: "제품소재",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_002",
				headerName: "색상",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_003",
				headerName: "치수",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_004",
				headerName: "제조사(수입자/병행수입)",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_005",
				headerName: "제조국",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
				width: 300,
			},
			{
				field: "item_006",
				headerName: "세탁방법 및 취급시 주의사항",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
				width: 300,
			},
			{
				field: "item_007",
				headerName: "제조연월",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
				width: 300,
			},
			{
				field: "item_008",
				headerName: "품질보증기준",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_009",
				headerName: "A/S 책임자와 전화번호",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_010",
				headerName: "KC안전인증 대상 유무",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_011",
				headerName: "수입여부",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{
				field: "item_012",
				headerName: "종류",
				cellStyle: {'background' : '#ffff99'},
				editable: true,
			},
			{ width: "auto" }
		];
    </script>
    <script type="text/javascript" charset="utf-8">
		const pApp = new App('', { gridId: "#div-gd" });
		let gx;
		
		$(document).ready(function() {
			pApp.ResizeGrid(275);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			let style = { ...getDeleteCellColumnObject(), ...getCopyFocusedCellToClipboardObject() };
			// style['suppressColumnVirtualisation'] = true;
			// style['skipHeaderOnAutoSize'] = true;
			gx = new HDGrid(gridDiv, columns, style);
			Search(1);

			$("select[name='class']").on("change", function() {
				Search(1, true);
			})
		});

		var _isloading = false;

        function onscroll(params){


            if(_isloading === false && params.top > gridDiv.scrollHeight){

                var rowtotal = gridOptions.api.getDisplayedRowCount();
                // console.log('getLastDisplayedRow : ' + gridOptions.api.getLastDisplayedRow());
                // console.log('rowTotalHeight : ' + rowtotal * 25);
                // console.log('params.top : ' + params.top);

                if(gridOptions.api.getLastDisplayedRow() > 0 && gridOptions.api.getLastDisplayedRow() ==  rowtotal -1) {
                    // console.log(params);
                    Search(0);
                }
                // var rowtotal = gridOptions.api.getDisplayedRowCount();
                // var rowHeight = 25;
                // var rowTotalHeight = rowtotal * gridOptions.rowHeight;
                // if(rowtotal > 0 && params.top > rowTotalHeight && (rowtotal - 1) == gridOptions.api.getLastDisplayedRow()){
                //     console.log('params.top :' + params.top);
                //     console.log('rowTotalHeight :' + rowTotalHeight);
                //     console.log('top : ' + params.top);
                //     console.log('eGridDiv : ' + eGridDiv.scrollHeight);
                //     console.log(gridOptions.api.getDisplayedRowCount());
                //     console.log(gridOptions.api.getFirstDisplayedRow());
                //     console.log(gridOptions.api.getLastDisplayedRow());
                //     _isloading = true;
                //     Search(0);
                // }
            }
        }


        var _page = 1;
        var _total = 0;
        var _grid_loading = false;
        var _code_items = "";
        var columns_arr = {};
        var option_key = {};

		function Search(page, reset) {
			setColumn(page, reset);
		}

        function getParams() {
            return {
                columnKeys: option_key,
                skipHeader: false,
                skipPinnedTop: false,
            };
        }

        function onBtnExportDataAsExcel() {
            var params = getParams();
            gx.gridOptions.api.exportDataAsExcel(params);
        }


		function setColumn(_page, _reset) {
			var frm = $('form[name="search"]');
			var min_w = 100;
			var setCol_style = [{
				colId: "goods_type",
				width: 80
			},
				{
					colId: "com_nm",
					width: 80
				},
				{
					colId: "opt_kind_nm",
					width: 80
				},
				{
					colId: "brand_nm",
					width: 100
				},
				{
					colId: "style_no",
					width: 80
				},
				{
					colId: "goods_nm",
					width: 200
				},
				{
					colId: "sale_stat_cl",
					width: 100
				},
				{
					colId: "goods_no",
					width: 80
				},
				{
					colId: "class",
					width: 100
				},
			];
			var setCol = [
				// this row shows the row index, doesn't use any data from the row
				{
					headerName: '',
					pinned: 'left',
					headerCheckboxSelection: true,
					checkboxSelection: true,
					lockPosition: true,
					width: 28,
				},
				{
					headerName: '#',
					pinned: 'left',
					width: 35,
					lockPosition: true,
					maxWidth: 100,
					// it is important to have node.id here, so that when the id changes (which happens
					// when the row is loaded) then the cell is refreshed.
					valueGetter: 'node.id',
					cellRenderer: 'loadingRenderer',
				},
				{
					field: "goods_type",
					headerName: "상품구분",
					minWidth: 72,
					cellStyle: StyleGoodsTypeNM,
					suppressSizeToFit: true,
					lockPosition: true,
				},
				{
					field: "com_nm",
					headerName: "업체",
					lockPosition: true,
					cellClass: 'hd-grid-code'
				},
				{
					field: "opt_kind_nm",
					headerName: "품목",
					lockPosition: true,
				},
				{
					field: "brand_nm",
					headerName: "브랜드",
					minWidth: 100,
					lockPosition: true,
					cellClass: 'hd-grid-code'
				},
				{
					field: "style_no",
					headerName: "스타일넘버",
					minWidth: 80,
					lockPosition: true,
				},
				{
					field: "img2",
					headerName: "img2",
					hide: true
				},
				{
					field: "img",
					headerName: "이미지",
					lockPosition: true,
					minWidth:46,
					cellRenderer: function(params) {
						if (params.value !== undefined && params.data.img != "") {
							return '<img src="{{config('shop.image_svr')}}/' + params.data.img + '"/>';
						}
					}
				},
				{
					field: "goods_nm",
					headerName: "상품명",
					type: "GoodsNameType",
					minWidth: 200,
					lockPosition: true,
				},
				{
					field: "sale_stat_cl",
					headerName: "상품상태",
					minWidth: 100,
					lockPosition: true,
					cellStyle: StyleGoodsState
				},
				{
					field: "goods_no",
					headerName: "상품번호",
					minWidth: 80,
					lockPosition: true,
					cellRenderer: function(params) {
						if (params.value !== undefined) {
							return params.data.goods_no + ' [' + params.data.goods_sub + ']';
						}
					}
				},
				{ width: "auto" },
				{
					field: "goods_sub",
					headerName: "goods_sub",
					hide: true
				},
				{
					field: "class",
					headerName: "분류",
					minWidth: 100,
					lockPosition: true,
				}
			];
			option_key = ['goods_type', 'com_nm', 'opt_kind_nm', 'brand_nm', 'style_no', 'img', 'goods_nm', 'sale_stat_cl', 'goods_no', 'goods_sub', 'class'];
			const basic_keys = option_key;


			// if ($("#class").val() == "") {
			//     return false;
			// }

            $.ajax({
                async: true,
                type: 'get',
                url: '/partner/product/prd05/column_search',
                data: frm.serialize() + '&page=' + _page,
                success: function (data) {
                    var col_arr = data['columns'];
                    var col_cnt = data['columns'].length;

					for (i = 0; i < col_cnt; i++) {
						var col_val, col_style;
						/*
						if(col_cnt[i][0]){}
	
	
						*/
						let cellWidth = columns.find(c => c.field === col_arr[i][0])?.width;
						col_val = {
							field: col_arr[i][0],
							headerName: col_arr[i][1],
							editable: true,
							minWidth: cellWidth || 100,
							maxWidth: 400,
							lockPosition: true,
							cellStyle: {'background' : '#ffff99', 'white-space': 'normal'},
						};
						col_style = {
							colId: col_arr[i][0],
							// width: 100,
							// minWidth: 100
						};
						// console.log("field : " + col_arr[i][0]);
						setCol.push(col_val);
						setCol_style.push(col_style);
						option_key.push(col_arr[i][0]);
					}

					gx.gridOptions.api.setColumnDefs(setCol);
					gx.gridOptions.columnApi.applyColumnState({
						state: [setCol_style]
					});
					gx.gridOptions.columnApi.autoSizeColumns(basic_keys, false);

					onPrintColumns();

					if(_reset) {
						if(data.code_items.length > 0) {
							$(".code_color_row").css("display", "flex");
							$(".code_item_row").css("display", "flex");
							$(".code_excel_row").css("display", "flex");
							let html = '';
							for(let i = 0; i < data.code_items.length; i++) {
								let item = data.code_items[i];
								html += `
	                                <div class="custom-control custom-checkbox">
	                                    <input type="checkbox" name="omission_column" id="code_item_${item.item}" value="${item.item}" class="custom-control-input" />
	                                    <label for="code_item_${item.item}" class="custom-control-label">${item.item_nm}</label>
	                                </div>
	                            `;
							}
							$("#code_item").html(html);
						} else {
							$(".code_color_row").css("display", "none");
							$(".code_item_row").css("display", "none");
							$(".code_excel_row").css("display", "none");
						}
					}
					let omission_column = $.map( $("[name='omission_column']"), function( n ) { return n.checked ? n.defaultValue : ''; }).join(",");
					let d = $('form[name="search"]').serialize() + '&omission_column=' + omission_column;
					gx.Request('/partner/product/prd05/search', d, _page);
				},
				complete: function() {},
				error: function(request, status, error) {
					alert("error");
					console.log(request);
				}
			});

		}

        function onPrintColumns() {
        var cols = gx.gridOptions.columnApi.getAllGridColumns();
        var colToNameFunc = function (col, index) {
            return index + ' = ' + col.getId();
        };
        var colNames = cols.map(colToNameFunc).join(', ');
        console.log('columns are: ' + colNames);
        }

        function AddProducts(){
            var url = '/partner/product/prd06';
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        }

        function EditProducts(){
            var url = '/partner/product/prd07';
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        }

        function AddProductImages(){
            var url = '/partner/product/prd08';
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        }

        function ShowProductImages(){
            var url = '/partner/product/prd09';
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        }

        function PopSearchBrand(type, ismt){
	        if(type == null || typeof(type) == "undefined") type = '';
            if(ismt == null || typeof(ismt) == "undefined") ismt = '';

            var url = "/partner/webapps/standard/std24.php?CMD=popup&TYPE=" + type + "&ISMT="+ismt;
            openWindow(url,"","resizable=yes,scrollbars=yes",700,600);
        }

        var removedRows = [];
        var checkedRows = [];

        function Cmder(value){
            if(value == "delete"){
                Delete();
            }else if(value == "reset"){
                SearchFormReset();
                gridOptions.api.selectAll();
                var selectedRowData = gridOptions.api.getSelectedRows();
                //gridOptions.api.applyTransaction({ remove: selectedRowData });

                //var selectedRows = mainGrid.gridOpts.api.getSelectedRows();
                selectedRowData.forEach( function(selectedRowData, index) {
                    removedRows.push(selectedRowData);
                    gridOptions.api.updateRowData({remove: [selectedRowData]});
                });

            }
        }

        function Save(){
            var frm = $('form[name=save]');
            var class_code = $("#class").val();
            var next_data = "";

			var selectedRowData = gx.gridOptions.api.getSelectedRows();
			var goods = JSON.stringify(selectedRowData);

			var to_class_code = $("#to_class").val();

			if (class_code == '') { // 미분류인 경우

				// class 값이 미분류인 경우 품목 선택하지 않았을 때 알림 처리
				if ((to_class_code == 'not_selected' || to_class_code == '')) {
					alert('변경할 품목을 선택해주세요.');
					return false;
				}

			} else {
				// 분류항목이 있을 때 select 값이 '선택' 인 경우 to_class를 동일하게 처리
				if (to_class_code == 'not_selected') {
					$("#to_class").val(class_code);
				}

			}

			$("#goods_info").val(goods);
			$("#data").val(goods);

			frm.attr('method', 'post');

			$.ajax({
				async: true,
				type: 'post',
				url: '/partner/product/prd05/update',
				data: frm.serialize() + '&class=' + class_code,
				success: function(data) {
					alert('변경 내용이 저장되었습니다.');
					Search(1);
				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
        }

        function Delete(){
            var frm = $('form[name="search"]');
            var next_data = "";

            var selectedRowData = gx.gridOptions.api.getSelectedRows();
            //gridOptions.api.applyTransaction({ remove: selectedRowData });

            //var selectedRows = mainGrid.gridOpts.api.getSelectedRows();
            selectedRowData.forEach( function(selectedRowData, index) {
                console.log("field : " +removedRows.field);
                removedRows.push(selectedRowData);

                gx.gridOptions.api.updateRowData({remove: [selectedRowData]});
            });


            for(i=0; i < removedRows.length; i++){
                var rowsKey = Object.keys(removedRows[i]);
                var keyData = [];
                keyData.push(removedRows[i].goods_no);
                keyData.push(removedRows[i].goods_sub);

                if(removedRows[i].goods_no!= ""){
                    next_data += keyData +"\t^EOL";
                }
            }

			$.ajax({
				async: true,
				type: 'get',
				url: '/partner/product/prd05/delete',
				data: frm.serialize() + '&data=' + next_data,
				success: function(data) {
					alert('선택하신 상품의 정보고시 데이터가 삭제 되었습니다.');
					Search();
				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
		}

        function ImportFile(){
            file_data = $("#file").prop('files')[0];
            var frm = $('form[name="search"]');
            var form_data = new FormData();
            form_data.append('file', file_data);

            frm.attr('method', 'post');

            $.ajax({
                url: '/partner/product/prd05/load_excel', // point to server-side PHP script
                dataType: 'json',  // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(res){
                    if(res.code == "200"){
                        file = res.file;
                        alert(file);
                        //importExcel("/" + file);
                        importExcel('/'+file);
                    } else {
                        console.log(res.errmsg);
                    }
                },
                complete:function(){
                        _grid_loading = false;
                },
                error: function(request, status, error) {
                        console.log("error")
                }

            });

        }

        function importExcel(url) {
            console.log("url : "+ url);
            makeRequest('GET',
                //'https://www.ag-grid.com/example-excel-import/OlymicData.xlsx',
                url,
                // success
                function (data) {
                //console.log(data);
                    var workbook = convertDataToWorkbook(data);
                    //console.log(workbook);
                    populateGrid(workbook);
                },
                // error
                function (error) {
                    throw error;
                }
            );
        }

        // read the raw data and convert it to a XLSX workbook
        function convertDataToWorkbook(data) {
            /* convert data to binary string */
            var data = new Uint8Array(data);
            var arr = new Array();

            for (var i = 0; i !== data.length; ++i) {
                arr[i] = String.fromCharCode(data[i]);
            }

            var bstr = arr.join("");

            return XLSX.read(bstr, {type: "binary"});
        }

        function makeRequest(method, url, success, error) {
            var httpRequest = new XMLHttpRequest();
            httpRequest.open("GET", url, true);
            httpRequest.responseType = "arraybuffer";

            httpRequest.open(method, url);
            httpRequest.onload = function () {
                success(httpRequest.response);
            };
            httpRequest.onerror = function () {
                error(httpRequest.response);
            };
            httpRequest.send();
        }

        function populateGrid(workbook) {
            // our data is in the first sheet
            var firstSheetName = workbook.SheetNames[0];
            var worksheet = workbook.Sheets[firstSheetName];
            var columns = columns_arr;

            // start at the 2nd row - the first row are the headers
            var rowIndex = 2;

            var rowData = [];
            var goodsNo_row = [];
            var next_data = "";
            /*
            console.log(workbook.Sheets.length);
            console.log(worksheet['A' + 1].w);
            */
            while (worksheet['A' + rowIndex]) {
                var row = {};
                Object.keys(columns).forEach(function(column) {
                    row[columns[column]] = worksheet[column + rowIndex].w;
                });

                rowData.push(row);

                rowIndex++;
            }

            gx.gridOptions.api.setRowData(rowData);
        }


        function SearchFormReset(){
            var class_val = $("#class").val();
			initSearch();
			$("#class").val(class_val).trigger('change');
        }

        function GridImageShow(){
            if($("#show_img").is(":checked")){
                gridOptions.columnApi.setColumnVisible('img', true);
            }else{
                gridOptions.columnApi.setColumnVisible('img', false);

            }
        }

        function rowGroupCallback(params) {
            return params.node.key;
        }

        function getIndentClass(params) {
            var indent = 0;
            var node = params.node;
            while (node && node.parent) {
                indent++;
                node = node.parent;
            }
            return ['indent-' + indent];
        }


        function ApplyToClass(){
            if($("#chk_to_class").is(":checked")){
                gx.gridOptions.api.selectAll();
                //$("#to_class").attr("disabled", false);
            }else{
                gx.gridOptions.api.deselectAll();
                //$("#to_class").attr("disabled", true);
            }
        }
    </script>


    <script type="text/javascript" charset="utf-8">

    $(".sort_toggle_btn label").on("click", function(){
        $(".sort_toggle_btn label").attr("class","btn btn-secondary");
        $(this).attr("class","btn btn-primary");
    });

    </script>


@stop
