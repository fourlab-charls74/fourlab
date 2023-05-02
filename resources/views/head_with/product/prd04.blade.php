@extends('head_with.layouts.layout')
@section('title','세일관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">세일관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 세일관리</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
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
                                <?php
                                    collect($goods_stats)->map(function($goods_stat) {
                                        $selected = "";
                                        if ($goods_stat->code_id == 40) $selected = 'selected';
                                        echo "<option value='" . $goods_stat->code_id . "' ${selected}>" . $goods_stat->code_val .  "</option>";
                                    });
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">스타일넘버/온라인코드</label>
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
				</div>
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
					</div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="head_desc">상단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
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
				</div>
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">세일여부/세일구분</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-25 pr-1" style="min-width:70px">
                                    <select id="sale_yn" name="sale_yn" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner form-check-box ml-2">
                                    <div class="form-inline">
                                        <div class="custom-control custom-checkbox" style="display: inline-flex; min-width: 80px;">
                                            <input type="checkbox" name="coupon_yn" id="coupon_yn" class="custom-control-input" value="Y">
                                            <label class="custom-control-label" for="coupon_yn" style="font-weight: 400;">쿠폰여부</label>
                                        </div>
                                    </div>
                                </div>
                                <span>　/　</span>
                                <div class="form-inline-inner form-check-box" style="flex-grow: 1;">
                                    <select id="sale_type" name="sale_type" class="form-control form-control-sm w-100">
                                        <option value="">선택</option>
                                        <option value="event">event</option>
                                        <option value="onesize">onesize</option>
                                        <option value="clearance">clearance</option>
                                        <option value="refurbished">refurbished</option>
                                        <option value="newmember">newmember</option>
                                    </select>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">타임세일여부</label>
							<div class="flax_box">
                                <select id="sale_dt_yn" name="sale_dt_yn" class="form-control form-control-sm w-100">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
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
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
	</div>
</form>
<!-- DataTales Example -->
<form method="post" name="save" action="/head/stock/stk01">
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
                                    <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
                                    <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                                </div>
                            </div>
						</div>
						<div class="fl_inner_box">
							<a href="#" onclick="timeSaleOff();" class="btn btn-sm btn-primary shadow-sm">타임세일종료</a>
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
<style> /* 상품 이미지 사이즈 강제 픽스 */ .img { height:30px; } </style>
<script language="javascript">

    const YELLOW_CELL = { 'background' : '#ffff99' };

    const cellStyleGoodsType = (params) => {
        var state = {
            "위탁판매":"#F90000",
            "매입":"#009999",
            "해외":"#0000FF",
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                return {
                    color: state[params.value],
                    height: '30px',
                    textAlign: 'center'
                }
            }
        }
    };

    function cellStyleGoodsState(params){
        var state = {
            "판매중지":"#808080",
            "등록대기중":"#669900",
            "판매중":"#0000ff",
            "품절[수동]":"#ff0000",
            "품절":"#AAAAAA",
            "휴지통":"#AAAAAA",
            "판매대기중": "black",
            "임시저장": "black"
        }
        if (params.value !== undefined) {
            if (state[params.value]) {
                var color = state[params.value];
                return {
                    color: color,
                    textAlign: 'center'
                }
            }
        }
    };

    const columns = [   
        { headerName: '', pinned: 'left', headerCheckboxSelection: true, checkboxSelection: true, width: 28 },
        { field: "goods_no", headerName: "온라인코드", width: 70, pinned: 'left' },
        { field: "goods_type", headerName: "상품구분", width: 58, cellStyle: (params) => cellStyleGoodsType(params), pinned: 'left' },
        { field: "opt_kind_nm", headerName: "품목", width: 100, pinned: 'left' },
        { field: "brand_nm", headerName: "브랜드", pinned: 'left' },
        { field: "style_no", headerName: "스타일넘버", width:70, cellStyle: {'text-align':'center'}, pinned: 'left' },
        { field: "head_desc", headerName: "상단홍보글", width: 120 },
        { field: "img", headerName: "이미지", width:46, type:'GoodsImageType', hide: true },
        { field: "img_url", headerName: "이미지_url", width:75, hide: true },
        { field: "goods_nm", headerName: "상품명", width: 230, type:"HeadGoodsNameType" },
        { field: "sale_stat_cl_val", headerName: "상품상태", width:70, cellStyle: (params) => cellStyleGoodsState(params) },
        { field: "goods_sh", headerName: "시중가", width:60, type: 'currencyType' },
        { field: "normal_price", headerName: "정상가", width:60, type: 'currencyType' },
        { field: "price", headerName: "판매가", width:60, type: 'currencyType' },
        { field: "sale_type", headerName: "세일구분", width:72 },
        { field: "sale_yn", headerName: "세일여부", width:72, cellStyle: YELLOW_CELL,
            cellRenderer: function(params) {
                if(params.value == 'Y') return "해당"
                else if(params.value == 'N') return "해당없음"
                else return params.value
            }
        },
        { field: "before_sale_price", headerName: "이전세일가", width:84, type: 'currencyType' },
        { field: "sale_price", headerName: "세일가", width:60, cellStyle: YELLOW_CELL, type: 'currencyType' },
        { field: "sale_rate", headerName: "세일율(%)", width:84, cellStyle: YELLOW_CELL, cellRenderer: (params) => Math.round(params.data.sale_rate) },
        { field: "sale_dt_yn", headerName: "타임세일여부", cellStyle: YELLOW_CELL, width:85,
            cellRenderer: function(params) {
                if(params.value == 'Y') return "해당"
                else if(params.value == 'N') return "해당없음"
                else return params.value
            }
        },
        { field: "sale_s_dt", headerName: "세일기간(시작)" },
        { field: "sale_e_dt", headerName: "세일기간(종료)" },
        { field: "coupon_price", headerName: "쿠폰가", width:60, type: 'currencyType' },
        { field: "wonga", headerName: "원가", width:60, type: 'currencyType' },         
        { field: "margin_amt", headerName: "마진액", width:60, type: 'currencyType' },
        { field: "margin_rate", headerName: "마진율(%)", cellRenderer: (params) => Math.round(params.data.margin_rate) },
        { field: "qty", headerName: "재고수", width:58, cellStyle: {'text-align':'right'}},
        { field: "md_nm", headerName: "MD" },
        { field: "reg_dm", headerName: "등록일자", width: 120 },
        { field: "upd_dm", headerName: "수정일자", width: 120 },
        { field: "goods_type_cd", headerName: "goods_type", hide:true }
    ];

    const CELL_DIMENSION_SIZE = 30;
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);

        let options = {
            rowHeight: CELL_DIMENSION_SIZE
        };
        
        gx = new HDGrid(gridDiv, columns, options);
        Search(1);

        $("#chk_to_class").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
        });
    });
    
    const Search = () => {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/product/prd04/search', data, 1, (data) => {
            // console.log(data);
        });
    };

    const popSearchBrand = (type, ismt) => {
        if (type == null || typeof(type) == "undefined") type = '';
        if (ismt == null || typeof(ismt) == "undefined") ismt = '';

        var url = "/head/webapps/standard/std24.php?CMD=popup&TYPE=" + type + "&ISMT=" + ismt;
        openWindow(url, "", "resizable=yes,scrollbars=yes", 700, 600);
    }

    const applyToClass = () => {
        if ($("#chk_to_class").is(":checked")) {
            gx.gridOptions.api.selectAll();
        } else {
            gx.gridOptions.api.deselectAll();
        }
    };
    

    $(".sort_toggle_btn label").on("click", function() {
        $(".sort_toggle_btn label").attr("class", "btn btn-secondary");
        $(this).attr("class", "btn btn-primary");
    });

    const timeSaleOff = () => {
        if (confirm("타임세일을 종료하시겠습니까?")) {
            const checked_rows = gx.gridOptions.api.getSelectedRows();
            const target_rows = checked_rows.filter(row => row.sale_dt_yn == "Y" ? true : false );
            const TIME_SALE_OFF_URL = '/head/product/prd04/time-sale-off';
            if (target_rows.length > 0) {
                axios({
                    url: TIME_SALE_OFF_URL,
                    method: 'post',
                    data: { data : target_rows }
                }).then((response) => {
                    if (response.data.code == 1) Search();
                }).catch((error) => {
                    // console.log(error);
                });
            }
        }
    };

</script>


@stop
