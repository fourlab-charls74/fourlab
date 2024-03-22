@extends('head_with.layouts.layout')
@section('title','상품전시')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">상품전시</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품</span>
        <span>/ 전시</span>
    </div>
</div>
<form method="get" name="search">
    <input type="hidden" name="d_cat_cd" value="" />
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return validate();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_stat">상품상태</label>
                            <div class="flax_box">
                                <select id="goods_stat" name='goods_stat' class="form-control form-control-sm">
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
                            <label for="style_no">스타일넘버/온라인코드</label>
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
                            <label for="formrow-email-input">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-25 pr-1">
                                    <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        @foreach ($com_types as $com_type)
                                        <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box w-75">
                                    <div class="form-inline inline_btn_box">
                                        <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company search-enter" style="width:100%;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand search-enter" data-all-brand="true"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">상단홍보글</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='head_desc' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_stat">혜택</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="sale_yn" id="sale_yn" class="custom-control-input" value="2">
                                    <label class="custom-control-label" for="sale_yn">할인</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="point_yn" id="point_yn" class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="point_yn">적립</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="div_yn" id="div_yn" class="custom-control-input" value="16">
                                    <label class="custom-control-label" for="div_yn">무료배송</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">출력여부</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="disp_yn" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">자료/정렬순서</label>
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
                                        <option value="seq" selected>인기도</option>
                                        <option value="goods_no">상품번호</option>
                                        <option value="sale">누적판매순</option>
                                        <option value="sale_3m">최근판매순</option>
                                        <option value="grade_3m">상품평점</option>
                                        <option value="review_3m">상품평건수</option>
                                        <option value="price">가격순</option>
                                        <option value="new_product_day">신상품순</option>
                                        <option value="reg_dm">최근등록순</option>
                                        <option value="soldout_day">최근품절일시</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc">
                                    <input type="radio" name="ord" id="sort_asc" value="asc" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
    <div class="show_layout">
        <div class="row">
            <div class="col-sm-4">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-title mb-3">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h5 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h5>
                                </div>
                                <div class="fr_box">
                                    <select id='cat_type' name='cat_type' class="form-control form-control-sm pr-4">
                                        <option value='DISPLAY'>전시카테고리</option>
                                        <option value='ITEM'>용도카테고리</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-title mb-3">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-goods-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                    <span class="d-none d-sm-inline">선택한 상품을</span>
                                    <select id='chg_disp_yn' name='chg_disp_yn' class="form-control form-control-sm" style='width:130px;display:inline'>
                                        <option value='Y'>활성</option>
                                        <option value='N'>비활성</option>
                                    </select>
                                    <span class="d-none d-sm-inline">로</span>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return ChangeDisp();"><span class="fs-12">상태변경</span></a>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return openChangeSeqPopup();"><span class="fs-12">순서변경</span></a>
									<button type="button" id="add_goods_btn" class="btn btn-sm btn-primary shadow-sm" onclick="return AddGoods();"><span class="fs-12">상품추가</span></button>
                                    <a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DelGoods();"><span class="fs-12">상품삭제</span></a>

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd-goods" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- <a href="https://bizest.netpx.co.kr/head/webapps/standard/std03.php">품목관리 링크</a> -->
<style>
    /* 전시카테고리 상품 이미지 사이즈 픽스 */
    .img{
        height:40px;
    }
</style>
<script language="javascript">
    var columns = [{
            field: "d_cat_nm",
            headerName: "카테고리",
            width: 220,
            cellRenderer: function(params) {
                const d_cat_cd = params.data.d_cat_cd;
                let lvl = '';
                if (d_cat_cd.length === 6) {
                    lvl = 'ml-2';
                } else if (d_cat_cd.length === 9) {
                    lvl = 'ml-3';
				} else if (d_cat_cd.length === 12) {
					lvl = 'ml-4';
				} else if (d_cat_cd.length > 12) {
					lvl = 'ml-5';
                }
				return '<a href="#" class="' + lvl + '" data-code="' + params.data.d_cat_cd + '" data-child-cnt="' + params.data.child_cnt + '" onClick="ClickCategory(this)">' + params.value + '</a>'
            }
        },
        {
            field: "cnt",
            headerName: "전체",
            width: 65,
            type: 'numberType'
        },
        {
            field: "30_cnt",
            headerName: "품절",
            width: 65,
            type: 'numberType'
        },
		{
			field: "40_cnt",
			headerName: "판매중",
			width: 65,
			type: 'numberType'
		},
		{
			field: "display_cnt",
			headerName: "전시중",
			width: 65,
			type: 'numberType'
		},
		{
			field: "product_match_cnt",
			headerName: "store",
			width: 65,
			type: 'numberType'
		},
        {
            field: "sort_opt",
            headerName: "정렬",
			width: 55,
			cellClass: 'hd-grid-code'
        },
        { width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            defaultColDef: {
                suppressMenu: true,
                flex: 1,
                resizable: true,
                autoHeight: true,
                sortable: false,
            },
        }
        gx = new HDGrid(gridDiv, columns, options);

        Search();

        $("#cat_type").change(function() {
            Search();
        });

    });

    function Search() {
        var cat_type = $('#cat_type').val();
        gx.Request('/head/product/prd10/search', "cat_type=" + cat_type);
    }
</script>
<script language="javascript">
    var columns_list = [
        {
            field: "chk",
            headerName: '',
            cellClass: 'hd-grid-code',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width: 28,
            pinned: 'left',
            sort: null,
            cellStyle: {"background":"#F5F7F7"}
        },
        {
            field: "disp_yn",
            headerName: '출력',
            width: 58,
            pinned: 'left',
            // rowDrag: true,
            cellRenderer: (params) => params.value === 'Y' ? '활성' : '비활성',
            cellStyle: (params) => ({'line-height':'40px', 'text-align':'center', 'color':params.value === 'Y' ? '#4444ff' : '#666666'}),
        },
        {
            field: "img",
            headerName: "이미지",
            type: 'GoodsImageType',
            width:46,
            pinned: 'left'
        },
        {
            field: "img",
            headerName: "이미지_url",
            hide: true
        },
        {
            field: "goods_no",
            headerName: "온라인코드",
            width: 70,
            cellStyle:{'line-height':'40px', 'text-align':'center'}
        },
        {
            field: "style_no",
            headerName: "스타일넘버",
            width: 70,
            cellStyle:{'line-height':'40px', 'text-align':'center'}
        },
        {
            field: "goods_nm",
            headerName: "상품명",
            width: 240,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    let content = params.data.head_desc;
                    content += '<br/>' + '<a href="#" onclick="return openHeadProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                    content += '<br/>' + params.data.ad_desc;
                    return content;
                }
            },
            cellStyle:{
                "line-height":"12px"
            }
        },
        {
            field: "sale_stat_cl",
            headerName: "상품상태",
            width:70,
            cellStyle: function(params) {
                var state = {
                    판매중지: "#808080",
                    등록대기중: "#669900",
                    판매대기중: "#000000",
                    임시저장: "#000000",
                    판매중: "#0000ff",
                    "품절[수동]": "#ff0000",
                    품절: "#AAAAAA",
                    휴지통: "#AAAAAA"
                };
                if (params.value !== undefined) {
                    if (state[params.value]) {
                        var color = state[params.value];
                        return {
                            color: color,
                            "text-align": "center",
                            "line-height": "40px"
                        };
                    }
                }
            }
        },
        {
            field: "before_sale_price",
            headerName: "정상가",
            type: 'currencyType',
            hide: true
        },
        {
            field: "price",
            headerName: "판매가",
            width:72,
            type: 'currencyType',
            cellStyle:{'line-height':'40px'}
        },
        {
            field: "coupon_price",
            headerName: "쿠폰가",
            width:60,
            type: 'currencyType',
            cellStyle:{'line-height':'40px'}
        },
        {
            field: "baesong_price",
            headerName: "배송비",
            type: 'currencyType',
            cellStyle: { "text-align": "center", "line-height":"20px" },
            cellRenderer: (params) => {
                const value = params.valueFormatted;
                const { bae_yn, dlv_pay_type } = params.data;
                let render;
                if (bae_yn == "N") {
                    render = '무료';
                } else {
                    render = `${value}원`;
                }
                if (dlv_pay_type == "P") {
                    render += '<br/>(선불)';
                } else {
                    render += '<br/>(착불)';
                }
                return render;
            },
        },
        {
            field: "qty",
            headerName: "재고수",
            type: 'numberType',
            width:46
        },
        {
            headerName: "판매수(S) 및 조회수(P)",
            cellStyle: { "text-align": "left", "line-height":"20px" },
            type: 'currencyType',
            cellRenderer: (params) => {
                const { sale, sale_3m, pv, pv_3m } = params.data;
                let render =
                    `S : ${sale} / ${sale_3m}<br/>
                    P : ${pv} / ${pv_3m}`;
                return render;
            },
        },
        {
            headerName: "상품평(E) 및 상품문의(O)",
            cellStyle: { "text-align": "left","line-height":"20px" },
            type: 'currencyType',
            cellRenderer: (params) => {
                const { review, grade, grade_3m, qa, qa_3m } = params.data;
                let render =
                    `E : ${review}(${grade}) / {${review}(${grade_3m})<br/>
					Q : {${qa}} / {${qa_3m}}`;
                return render;
            },
        },
        {
            field: "com_nm",
            headerName: "업체",
            width: 100,
            cellStyle:{'line-height':'40px'}
        },
        {
            field: "sale_rate",
            headerName: "세일율(,%)",
            type: 'percentType',
            hide: true
        },
        {
            field: "sale_s_dt",
            headerName: "세일기간",
            hide: true
        },
        {
            field: "sale_e_dt",
            headerName: "세일기간",
            hide: true
        },
        {
            field: "reg_dm",
            headerName: "등록일시",
            cellStyle: { "text-align": "center","line-height":"20px" },
            cellRenderer: (params) => {
                const { reg_dm, new_product_type, new_product_day } = params.data;
                let render = `${reg_dm}<br/>(${new_product_type}${new_product_day})`;
                return render;
            },
            width: 110
        },
        {
            field: "new_product_day",
            headerName: "신상품일시",
            width:70,
            cellStyle:{'line-height':'40px', 'text-align':'right'}
        },
        {
            field: "soldout_day",
            headerName: "최근품절일시",
            cellStyle:{'line-height':'40px', 'text-align':'right'}
        },
        {
            field: "sale_price",
            headerName: "sale_price",
            hide: true
        },
        {
            field: "goods_type_cd",
            headerName: "goods_type",
            hide: true
        },
        { width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp2 = new App('', {
        gridId: "#div-gd-goods",
    });
    let gx2;
    $(document).ready(function() {
        pApp2.ResizeGrid(275);
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, columns_list, {
            // rowDragManaged: true,
            // enableMultiRowDragging: true, // 버젼이슈 문제 - rowDragMultiRow true하니깐 작동 x 이거로 사용하면 작동함
            rowSelection: 'multiple',
            // animateRows: true,
            // defaultColDef: {
            //     suppressMenu: true,
            //     flex: 1,
            //     resizable: true,
            //     autoHeight: true,
            //     sortable: false,
            // }
        });
    });

    function ClickCategory(a) {

        $('input[name="d_cat_cd"]').val($(a).attr('data-code'));
		$('#add_goods_btn').prop('disabled', $(a).attr('data-child-cnt') * 1 > 0);
        SearchGoods2();
    }

    function validate() {
        if(gx2.getRows().length > 0){
            return SearchGoods2();
        } else {
            return alert('카테고리를 선택해주세요');
        }
    }



    function SearchGoods2() {
        const d_cat_cd = $('input[name="d_cat_cd"]').val();
        const url = '/head/product/prd10/' + d_cat_cd + '/search';
        let data = $('form[name="search"]').serialize();
        gx2.Request(url, data, 1, searchGoodsCallback);
    }

    function searchGoodsCallback(data) {

    }

    /**
     * @return {boolean}
     */
    function ChoiceGoodsNo(goods_nos) {

        var cat_type = $('#cat_type').val();
        const d_cat_cd = $('input[name="d_cat_cd"]').val();

        $.ajax({
            method: 'post',
            url: '/head/product/prd10/' + d_cat_cd + '/save',
            data: {
                'cat_type': cat_type,
                'goods_no': goods_nos
            },
            dataType: 'json',
            success: function(res) {
                if (res.code == '200') {
                    SearchGoods2();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });
        return true;
    }

    /**
     * @return {boolean}
     */
    function AddGoods(goods_nos) {
        var url = '/head/product/prd01/choice';
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    /**
     * @return {boolean}
     */
    function DelGoods() {

        const cat_type = $('#cat_type').val();
        const d_cat_cd = $('input[name="d_cat_cd"]').val();

        let goods_nos = [];
        gx2.getSelectedRows().forEach((selectedRow, index) => {
            goods_nos.push(selectedRow.goods_no);
        });

        if (goods_nos.length === 0) {
            alert('삭제할 상품을 선택 해 주십시오.');
        } else if (goods_nos.length > 0 && confirm('삭제 하시겠습니까?')) {

            $.ajax({
                method: 'post',
                url: '/head/product/prd10/' + d_cat_cd + '/del',
                data: {
                    'cat_type': cat_type,
                    'goods_no': goods_nos
                },
                dataType: 'json',
                success: function(res) {
                    if (res.code == '200') {
                        SearchGoods2();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
        }
        return true;

    }

	/**
	 * @return {boolean}
	 */
	function ChangeDisp(){

		const cat_type		= $('#cat_type').val();
		const chg_disp_yn	= $('#chg_disp_yn').val();
		const d_cat_cd		= $('input[name="d_cat_cd"]').val();

		let goods_nos	= [];
		gx2.getSelectedRows().forEach((selectedRow, index) => {
			goods_nos.push(selectedRow.goods_no);
		});

		if( goods_nos.length == 0 ){
			alert('상태 변경할 상품을 선택해 주십시오.');
			return false;
		}

		$.ajax({
			method: 'post',
			url: '/head/product/prd10/' + d_cat_cd + '/disp',
			data: {
				'cat_type': cat_type,
				'goods_no': goods_nos,
				'disp_yn': chg_disp_yn
			},
			dataType: 'json',
			success: function(res) {
				if (res.code == '200') {
					SearchGoods2();
				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
		return true;
	}

    // 상품 순서변경 팝업 오픈
    function  openChangeSeqPopup() {
        const category_cd = $("[name='d_cat_cd']").val();
        const category_type = $("[name='cat_type']").val();
        if (!category_cd) return alert("카테고리를 선택해주세요.");
        const url = `/head/product/prd10/show/${category_type}/${category_cd}`;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1300,height=800");
    }
</script>


@stop
