@extends('head_with.layouts.layout')
@section('title','상품도매')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">상품도매</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품도매</span>
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
                            <label for="">상품상태</label>
                            <div class="flax_box">
                                <select name='goods_stat' class="form-control form-control-sm">
                                <option value=''>전체</option>
                                <?php
                                    collect($goods_stats)->map(function($goods_stat) {
                                        $selected = "";
                                        // if ($goods_stat->code_id == 40) $selected = 'selected';
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
                            <label for="">도매가</label>
                            <div class="form-inline-inner form-check-box ml-2">
                                <div class="form-inline">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="not_price" id="not_price" class="custom-control-input" value="Y">
                                        <label class="custom-control-label" for="not_price" style="font-weight: 400;">미설정 상품</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="ne_margin" id="ne_margin" class="custom-control-input" value="Y">
                                        <label class="custom-control-label" for="ne_margin" style="font-weight: 400;">기본마진과 다른 상품</label>
                                    </div>
                                </div>
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
				<div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">품목</label>
                            <div class="flax_box">
                                <select name='opt_kind_cd' class="form-control form-control-sm">
                                <option value=''>전체</option>
                                <?php
                                    collect($opt_kind_cds)->map(function($opt_kind_cd) {
                                        $selected = "";
                                        // if ($goods_stat->code_id == 40) $selected = 'selected';
                                        echo "<option value='" . $opt_kind_cd->cd . "' ${selected}>" . "(" . $opt_kind_cd->cd . ")" . $opt_kind_cd->val .  "</option>";
                                    });
                                ?>
                                </select>
                            </div>
                        </div>
					</div>
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
							<label for="com_nm">업체</label>
							<div class="form-inline inline_select_box">
								{{-- <div class="form-inline-inner input-box w-25 pr-1">
									<select id="com_type" name="com_type" class="form-control form-control-sm w-100">
										<option value="">전체</option>
										@foreach ($com_types as $com_type)
										<option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
										@endforeach
									</select>
								</div> --}}
								<div class="form-inline-inner input-box w-75">
									<div class="form-inline inline_btn_box">
										<input type="hidden" id="com_cd" name="com_cd">
										<input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-company" style="width:100%;">
										<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
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
<form name="wholesale">
	@csrf
	<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
		<div class="card-body">
			<div class="card-title mb-3">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box flex_box">
                        <div class="custom-control custom-checkbox form-check-box mr-2" style="display:inline-block;">
                            <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
                            <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                        </div>
                        <span>|</span>
                        <div class="flex_box ml-2">
                            최대 할인율 <input class="form-control form-control-sm ml-1" type='text' name='max_dc_ratio' value='50' style="text-align: center;width:70px;">&nbsp;%&nbsp;
                        </div>
                        <select name='cut_type' class="form-control form-control-sm mx-1" style="width: 70px;">
                            <option value='U'>절상</option>
                            <option value='D'>절사</option>
                        </select>
                        <select name='cut_price' class="form-control form-control-sm mr-1" style="width: 70px;">
                            <option value="10">1</option>
                            <option value="100" selected="selected">10</option>
                            <option value="1000">100</option>
                        </select>
                        <div class="flex_box ml-1">
                            <a href="#" onclick="SetUserBasicMargin();" class="btn btn-sm btn-primary shadow-sm mr-1">회원 기본마진율 게산</a>
                            <a href="#" onclick="Save();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="bx bx-save mr-1"></i>저장</a>
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
    .img{
        height:30px;
    }

    .price-red {
        font-weight: bold;
        background: #d05050;
    }
    .price-pink {
        background: #FFACAC;
    }
    .price-yellow {
        background: #FFFF99;
    }
</style>
<script language="javascript">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });
    const group_columns = <?= json_encode($group_columns) ?>;

    let columns = [
        { headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left' },
        { field: "goods_no", headerName: "온라인코드", width: 70, type:"HeadGoodsNameType", pinned: 'left' },
        { field: "style_no", headerName: "스타일넘버", width:70, cellStyle: {'text-align':'center'}, pinned: 'left' },
        { field: "opt_kind_nm", headerName: "품목", width: 100, pinned: 'left' },
        { field: "goods_type", headerName: "상품구분", width: 58, cellStyle: (params) => cellStyleGoodsType(params), pinned: 'left' },
        { field: "brand_nm", headerName: "브랜드", width:96, pinned: 'left' },
        { field: "img", headerName: "이미지", width: 46, type:'GoodsImageType', pinned: 'left', hide: true },
        { field: "img", headerName: "이미지_url", width:75, hide:true },
        { field: "goods_nm", headerName: "상품명", width: 260, pinned: 'left' },
        { 
            headerName: "재고수", 
            children: [
                { field: "qty", headerName: "온라인", type: 'numberType', width: 58 },
                { field: "wqty", headerName: "보유", type: 'numberType', width: 58 },
            ] 
        },
        { field: "price", headerName: "판매가", type: 'numberType', width: 58 },
        { field: "wonga", headerName: "원가", type: 'numberType', width: 58, cellRenderer: (params) => `<a href="#" onclick="openWongaPopup(event, ${params.data.goods_no}, ${params.data.goods_sub})">${Comma(params.value)}</a>` },
        { field: "margin", headerName: "원가대비 마진율", type: 'numberType', width: 100 },
		{ field: "is_changed", hide: true }
    ];

    function styleOfMarginRatio(params) {
        return {'background': parseInt(params.value) <= 0 || params.data.is_changed ? '#DD4080' : "none"};
    }

    function styleOfDCRatio(params) {
        const max_dc_ratio = $("[name=max_dc_ratio]").val();
        const group_price = parseInt(params.data[`group_${params.column.originalParent.colGroupDef.group_no}_price`]);
        return {'background': max_dc_ratio > 0 && parseInt(params.value) > max_dc_ratio && group_price !== 0 && params.data.price !== group_price ? '#FF4080' : "none"};
    }

    // 판매가 변경 시 해당 값에 대한 마진율 & 할인율 계산
    function calculatePriceMargin(e) {
		if(isNaN(e.newValue * 1)) {
            alert("숫자만 입력해주세요");
            e.data[e.colDef.field] = e.oldValue;
            e.api.refreshCells({columns : [e.colDef.field], rosNodes: [e.node]});
            return;
        }

        cut_type = $("[name=cut_type]").val();
        cut_price = $("[name=cut_price]").val();

        let wonga = e.data.wonga; // 원가
        let price = e.data.price; // 판매가
        let group_name = e.colDef.field.split("_").slice(0,2).join("_");
        let wholesale_price = e.newValue; // 그룹별 판매가
        let margin_ratio = 0; // 그룹별 마진율
        let dc_ratio = 0; // 그룹별 할인율

        if(wonga > 0) margin_ratio = (100 - Math.round((wonga / wholesale_price) * 100));
        else margin_ratio = 0;
        e.node.setDataValue(group_name + "_ratio", margin_ratio);

        if(price > 0) {
            dc_ratio = Math.round(((price - wholesale_price) / price) * 100);
            e.node.setDataValue(group_name + "_dc_ratio", dc_ratio);
        }

        e.node.setDataValue('is_changed', true);
        e.node.setSelected(true);        
    }

    group_columns.forEach(col => {
        columns.push({ 
            headerName: col.group_nm,
            group_no: col.group_no,
            children: [
                { field: `group_${col.group_no}`, headerName: "그룹코드", hide: true },
                { field: `group_${col.group_no}_margin`, headerName: "그룹마진", type: 'numberType', width: 90 },
                { field: `group_${col.group_no}_price`, headerName: "판매가", type: 'numberType', 
                    cellClassRules: {
                        'price-red': (p) => p.data.is_changed,
                        'price-pink': (p) => !p.data.is_changed && (parseInt(p.value) === 0 || p.data.price === parseInt(p.value)),
                        'price-yellow': (p) => !p.data.is_changed && !(parseInt(p.value) === 0 || p.data.price === parseInt(p.value)),
                    },
                    width: 80, editable: true, onCellValueChanged: calculatePriceMargin
                },
                { field: `group_${col.group_no}_ratio`, headerName: "마진율", type: 'numberType', cellStyle: styleOfMarginRatio, width: 80 },
                { field: `group_${col.group_no}_dc_ratio`, headerName: "할인율", type: 'numberType', cellStyle: styleOfDCRatio, width: 80 },
            ],
        })
    })
    columns.push({
        width: "auto"
    });

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

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        Search();

        // 이미지 출력 설정
        $("#chk_to_class").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
        });
    });
</script>
<script language="javascript">
    let cut_type = "U"; // 초기값 '절상'
    let cut_price = "100"; // 초기값 10

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/product/prd03/search', data, 1);
    }

    function SetUserBasicMargin() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("상품을 선택해주세요.");

        cut_type = $("[name=cut_type]").val();
        cut_price = $("[name=cut_price]").val();

        let wonga = 0; // 원가
        let group_margin = 0; // 그룹마진
        let wholesale_price = 0; // 그룹별 판매가
        let margin_ratio = 0; // 그룹별 마진율
        let dc_ratio = 0; // 그룹별 할인율

        rows = rows.map(row => {
            wonga = row.wonga;
            price = row.price;
            
            group_columns.forEach(col => {
                margin = row[`group_${col.group_no}_margin`];

                // 판매가
                wholesale_price = Math.round(wonga / (1 - (margin / 100)));

                if(cut_type === "D") { // 절사
                    wholesale_price = parseInt(wholesale_price / cut_price) * cut_price;
                } else if(cut_type === "U") { // 절상
                    wholesale_price = Math.ceil(wholesale_price / cut_price) * cut_price;
                }
                row[`group_${col.group_no}_price`] = wholesale_price;

                // 마진율
                if(wonga > 0) margin_ratio = (100 - Math.round((wonga / wholesale_price) * 100));
                else margin_ratio = 0;
                row[`group_${col.group_no}_ratio`] = margin_ratio;

                // 할인율
                if(price > 0) {
                    dc_ratio = Math.round(((price - wholesale_price) / price) * 100);
                    row[`group_${col.group_no}_dc_ratio`] = dc_ratio;
                }
            });

            row['is_changed'] = true;
            gx.gridOptions.api.updateRowData({ update: [row] });
        });
    }

    function Save() {
        let rows = [];
        gx.gridOptions.api.forEachNode(node => {
            if(node.data.is_changed) rows.push(node.data);
        })

        if(rows.length < 1) return alert("변경된 상품이 없습니다.");
        if(!window.confirm("변경된 도매 가격을 저장하시겠습니까?")) return;

        axios({
            url: "/head/product/prd03/price-save",
            method: 'post',
            data: { data : rows }
        }).then((response) => {
            alert(response.data.message);
            if(response.data.code === 200) {
                Search();
            }
        }).catch((error) => {
            console.log(error);
        });
    }

    function openWongaPopup(e, goods_no, goods_sub) {
        e.preventDefault();
        const url = "/head/product/prd03/wonga?goods_no=" + goods_no + "&goods_sub=" + goods_sub;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=200,width=1024,height=900");
    }
</script>
@stop
