@extends('head_with.layouts.layout-nav')
@section('title','광고 상세')
@section('content')
<div class="show_layout py-3 px-sm-3">
	<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
		<div>
			<h3 class="d-inline-flex">광고 상세</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 광고할인관리 - 상세</span>
			</div>
		</div>
	</div>
    <!-- 상품 세부 정보 -->
    <form name="detail">
        <div class="card_wrap mb-3">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">기본 정보</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
										</colgroup>
										<tbody>
											<tr>
												<th>할인명</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm" name='name' id="name" value='{{$name}}' >
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>사용</th>
												<td colspan="5">
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if ($use_yn === 'Y') checked @endif>
															<label class="custom-control-label" for="use_y">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if ($use_yn === 'N') checked @endif>
															<label class="custom-control-label" for="use_n">아니오</label>
														</div>
                                                        <div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_d" class="custom-control-input" value="D">
															<label class="custom-control-label" for="use_d">영구삭제</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>범위</th>
												<td colspan="5">
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="dc_range" id="dc_range_a" class="custom-control-input" value="A" @if ($dc_range === 'A') checked @endif>
															<label class="custom-control-label" for="dc_range_a">전체</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="dc_range" id="dc_range_g" class="custom-control-input" value="G" @if ($dc_range === 'G') checked @endif>
															<label class="custom-control-label" for="dc_range_g">상품</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>할인</th>
												<td colspan="5">
                                                    <div class="d-flex">
                                                        <div class="flax_box">
                                                            할인율 : <div class="flax_box"><input type='text' class="form-control form-control-sm text-right" name='dc_rate' id="dc_rate" value='{{$dc_rate}}' style="width:calc(100% - 30px);margin-right:2px;" maxlength="3" onKeyup="currency(this)">%</div>
                                                        </div>
                                                        <div class="flax_box">
                                                            + 할인금액 :<div class="flax_box"><input type='text' class="form-control form-control-sm text-right" name='dc_amt' id="dc_amt" value='{{number_format($dc_amt)}}' style="width:calc(100% - 30px);margin-right:2px;" onKeyup="currency(this)">원</div>
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>기간</th>
												<td colspan="5">
                                                <div class="form-inline">
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" id="date_from" name="date_from" value="{{ $date_from }}" autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                        <span class="text_line">~</span>
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" id="date_to" name="date_to" value="{{$date_to}}" autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>마진율제한</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        마진율 : <div class="flax_box mx-2"><input type='text' class="form-control form-control-sm text-right" name='limit_margin_rate' id='limit_margin_rate' value='{{$limit_margin_rate}}'></div> % 이하 제한
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>쿠폰제한</th>
												<td colspan="5">
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="limit_coupon_yn" id="limit_coupon_y" class="custom-control-input" value="Y" @if ($limit_coupon_yn === 'Y') checked @endif>
															<label class="custom-control-label" for="limit_coupon_y">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="limit_coupon_yn" id="limit_coupon_n" class="custom-control-input" value="N" @if ($limit_coupon_yn === 'N') checked @endif>
															<label class="custom-control-label" for="limit_coupon_n">아니오</label>
														</div>
													</div>
                                                    <div class="txt_box">( ※입점업체 100% 부담 쿠폰 제외 )</div>
												</td>
											</tr>
											<tr>
												<th>적립금제한</th>
												<td colspan="5">
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="limit_point_yn" id="limit_point_y" class="custom-control-input" value="Y" @if ($limit_point_yn === 'Y') checked @endif>
															<label class="custom-control-label" for="limit_point_y">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="limit_point_yn" id="limit_point_n" class="custom-control-input" value="N" @if ($limit_point_yn === 'N') checked @endif>
															<label class="custom-control-label" for="limit_point_n">아니오</label>
														</div>
													</div>
                                                    <div class="txt_box">( ※선할인 적립금 할인 및 적립금 사용 제한 )</div>
												</td>
											</tr>
											<tr>
												<th>적립금지급</th>
												<td colspan="5">
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="add_point_yn" id="add_point_y" class="custom-control-input" value="Y" @if ($add_point_yn === 'Y') checked @endif>
															<label class="custom-control-label" for="add_point_y">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="add_point_yn" id="add_point_n" class="custom-control-input" value="N" @if ($add_point_yn === 'N') checked @endif>
															<label class="custom-control-label" for="add_point_n">아니오</label>
														</div>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($no))
        <div class="card_wrap mb-3">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <h5 class="m-0 font-weight-bold">세부 정보</h5>
                </div>
                <div class="card-body">
                    <!-- 브랜드별 할인 -->
                    <div class="row_wrap mb-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                            <col width="*">
                                        </colgroup>
                                        <tbody>
                                            <tr id="brand_sale">
                                                <th class="ty2">브랜드별 할인</th>
                                                <td class="ty2" colspan="5">
                                                    <div class="filter_wrap_inner">
                                                        <div class="fl_box">
                                                            <h6 class="m-0 font-weight-bold">총 <span id="brand-total" class="text-primary">0</span> 건</h6>
                                                        </div>
                                                        <div class="fr_box">
                                                            <a href="#" class="btn btn-sm btn-secondary brand-manage-btn">관리</a>
                                                            <a href="#" class="btn btn-sm btn-secondary brand-add-btn">추가</a>
                                                            <a href="#" class="btn btn-sm btn-secondary brand-delete-btn">삭제</a>
                                                            <a href="#" class="btn btn-sm btn-secondary brand-submit-btn">저장</a>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive mt-1" >
                                                        <div id="brand-gd" style="height:120px; width:100%;" class="ag-theme-balham"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="goods_sale">
                                                <th class="ty2">상품별 할인</th>
                                                <td class="ty2" colspan="5">
                                                    <div class="filter_wrap_inner">
                                                        <div class="fl_box">
                                                            <h6 class="m-0 font-weight-bold">총 <span id="goods-total" class="text-primary">0</span> 건</h6>
                                                        </div>
                                                        <div class="fr_box">
                                                            <a href="#" class="btn btn-sm btn-secondary goods-manage-btn">관리</a>
                                                            <a href="#" class="btn btn-sm btn-secondary goods-add-btn">추가</a>
                                                            <a href="#" class="btn btn-sm btn-secondary goods-delete-btn">삭제</a>
                                                            <a href="#" class="btn btn-sm btn-secondary goods-submit-btn">저장</a>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive mt-1">
                                                        <div id="goods-gd" style="height:120px; width:100%;" class="ag-theme-balham"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="no_sale">
                                                <th class="ty2">할인 제외 상품</th>
                                                <td class="ty2" colspan="5">
                                                    <div class="filter_wrap_inner">
                                                        <div class="fl_box">
                                                            <h6 class="m-0 font-weight-bold">총 <span id="ex-goods-total" class="text-primary">0</span> 건</h6>
                                                        </div>
                                                        <div class="fr_box">
                                                            <a href="#" class="btn btn-sm btn-secondary ex-goods-add-btn">추가</a>
                                                            <a href="#" class="btn btn-sm btn-secondary ex-goods-delete-btn">삭제</a>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive mt-1">
                                                        <div id="ex-goods-gd" style="height:120px; width:100%;" class="ag-theme-balham"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </form>
    <div class="resul_btn_wrap mt-3 d-block">
        <a href="#" class="btn btn-sm btn-secondary submit-btn">저장</a>
        <a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
    </div>
</div>

<script>
const no = '{{$no}}';
const validate = () => {
    if ($('#name').val() === '') {
        alert('할인명을 입력해주세요.');
        return false;
    }

    if ( isNaN($('#dc_rate').val()*1) ) {
        alert('할인율은 숫자로 입력해주세요.');
        return false;
    }
    
    if ( isNaN( unComma($('#dc_amt').val()) * 1) ) {
        alert('할인금액은 숫자로 입력해주세요.');
        return false;
    }

    return true;
};

$('.submit-btn').click((e) => {
    e.preventDefault();

    if (validate() === false) return;
    let dc_amt = unComma($('#dc_amt').val());

    let date_form = $('#date_from').val();
        date_form = date_form ? date_form.replace(/[-]/g, '') : "";

    let date_to = $('#date_to').val();
        date_to = date_to ? date_to.replace(/[-]/g, '') : "";

    $('#date_from').val(date_form);
    $('#date_to').val(date_to);
    $('#dc_amt').val(dc_amt);

    const data = $('form[name=detail]').serialize();

    $.ajax({
        async: true,
        type: 'put',
        url: `/head/standard/std11/show/dc/${no}`,
        data: data,
        success: function (res) {
            opener?.Search?.();
            if (res) {
                alert("광고가 생성 되었습니다.");
                location.href="/head/standard/std11/show/dc/" + res;
            } else {
                if (document.detail.use_yn.value == "D") {
                    alert("정상적으로 삭제 되었습니다.");
                    window.close();
                    return false;
                }
                alert("변경된 내용이 정상적으로 저장 되었습니다.");
                location.reload();
            }
        },
        error: function(request, status, error) {
            alert(request.responseJSON.message);
            console.log("error");
        }
    });
});
</script>

<!-- 범위 라디오버튼 클릭시 해당 그리드만 출력 -->
<script>
    $(document).ready(function(){
        if($("input[name=dc_range]:checked").val() == "G"){
                $('#brand_sale').hide();
                $('#goods_sale').show();
                $('#no_sale').hide();
        }
        $("input[name='dc_range']").click(function(){
            if ($("input[name=dc_range]:checked").val() == "G") {
                $('#brand_sale').hide();
                $('#goods_sale').show();
                $('#no_sale').hide();
            } else {
                $('#brand_sale').show();
                $('#goods_sale').show();
                $('#no_sale').show();
            }
        });

		$(".docs-date").on("change", function (e) {
			if (/[^0123456789-]/g.test(e.target.value)) {
				alert("날짜형식으로 입력해주세요.");
				e.target.value = '';
			}
		});
		
		$("#limit_margin_rate").on("change", function (e) {
			if (/[^0123456789]/g.test(e.target.value)) {
				alert("숫자형식으로 입력해주세요.(소수점 단위 불가능)");
				e.target.value = 0;
			}
		});
    });
</script>

<!-- 수정일 경우만 로직 실행 -->
@if (!empty($no))
<script>
    const pageNo = -1;
    const GRID_MARGIN = 140;
    const GRID_HEIGHT = 200;
    const editCellStyle = { 
        'background' : '#ffff99', 
        'border-right' : '1px solid #e0e7e7' 
    };
    //브랜드별 할인 컬럼
    const brandColumns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {
            field: "brand_nm", 
            headerName: "브랜드",
            width : 120
        },
        {
            field: "dc_rate", 
            headerName: "할인율(%)", 
            editable: true, 
            cellStyle: editCellStyle, 
            type:'currencyType'
        },
        {
            field: "dc_amt", 
            headerName: "할인금액(원)", 
            editable: true, 
            cellStyle: editCellStyle, 
            type:'currencyType'
        },
        {
            field: "limit_margin_rate", 
            headerName: "마진율제한(%)", 
            editable: true, 
            cellStyle: editCellStyle, 
            type:'currencyType'
        },
        {field: "admin_nm", headerName: "관리자명"},
        {field: "ut", headerName: "최근수정일시", width: 130}
    ];

    //상품별 할인 컬럼
    const goodsColumns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {field: "goods_no", headerName: "상품코드"},
        {field: "brand_nm", headerName: "브랜드"},
        {field: "goods_nm", headerName: "상품명"},
        {field: "dc_rate", headerName: "할인율(%)", editable: true, cellStyle: editCellStyle, type:'currencyType' },
        {field: "dc_amt", headerName: "할인금액(원)", editable: true, cellStyle: editCellStyle, type:'currencyType' },
        {field: "admin_nm", headerName: "관리자명"},
        {field: "ut", headerName: "최근수정일시", width: 130}
    ];

    //할인 제외 상품 그리드 컬럼
    const exGoodsColumns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {field: "goods_no", headerName: "상품코드"},
        {field: "brand_nm", headerName: "브랜드"},
        {field: "goods_nm", headerName: "상품명"},
        {field: "admin_nm", headerName: "관리자명"},
        {field: "ut", headerName: "최근수정일시", width: 130}
    ];

    //브랜드별 할인 설정
    const brandApp = new App('', { gridId: "#brand-gd" });
    const brandGrid = document.querySelector(brandApp.options.gridId);
    const brandGx = new HDGrid(brandGrid, brandColumns);

    //상품별 할인 설정
    const goodsApp = new App('', { gridId: "#goods-gd" });
    const goodsGrid = document.querySelector(goodsApp.options.gridId);
    const goodsGx = new HDGrid(goodsGrid, goodsColumns);

    //할인 제외 상품 그리드 설정
    const exGoodsApp = new App('', { gridId: "#ex-goods-gd" });
    const exGoodsGrid = document.querySelector(exGoodsApp.options.gridId);
    const exGoodsGx = new HDGrid(exGoodsGrid, exGoodsColumns);

    const brandSearch = () => {
        const data = `no=${no}`;
        brandGx.Request('/head/standard/std11/search/dc-brand', data, pageNo, function(res){
            $('#brand-total').html(res.head.total);
        });
    };

    const goodsSearch = () => {
        const data = `no=${no}`;
        goodsGx.Request('/head/standard/std11/search/dc-goods', data, pageNo, function(res){
            $('#goods-total').html(res.head.total);
        });
    };
    
    const exGoodsSearch = () => {
        const data = `no=${no}`;
        exGoodsGx.Request('/head/standard/std11/search/dc-ex-goods', data,pageNo, function(res){
            $('#ex-goods-total').html(res.head.total);
        });
    };

    const addDCGoods = (data) => {
        $.ajax({
            async: true,
            type: 'post',
            url: `/head/standard/std11/dc/goods/${no}`,
            data: 'goods_nos='+data,
            success: function (res) {
                alert("상품별 할인이 추가되었습니다.");
                goodsSearch();
            },
            error: function(request, status, error) {
                alert(request.responseJSON.message);
            }
        });
    }

    const addDCExGoods = (data) => {
        $.ajax({
            async: true,
            type: 'post',
            url: `/head/standard/std11/dc/ex-goods/${no}`,
            data: 'goods_nos='+data,
            success: function (res) {
                alert("할인 제외 상품이 추가되었습니다.");
                exGoodsSearch();
            },
            error: function(request, status, error) {
                alert(request.responseJSON.message);
            }
        });
    }

    const createGoodsData = (row) => {
        if (!row) return "";
        return row.goods_no+"|"+row.goods_sub;
    }

    //window open 에서는 arrow function이 인식이 안됨.
    function goodsCallback(row) {
        if (goodsPopType === 'goods') {
            addDCGoods(createGoodsData(row));
        } else if (goodsPopType === 'ex-goods') {
            addDCExGoods(createGoodsData(row));
        }
    }

    function multiGoodsCallback(rows) {
        const dataRow = [];

        rows.forEach((row) => {
            dataRow.push(createGoodsData(row));
        });

        if (goodsPopType === 'goods') {
            addDCGoods(dataRow.join(","));
        } else if (goodsPopType === 'ex-goods') {
            addDCExGoods(dataRow.join(","));
        }
    }

    let goodsPopType = "";

    brandApp.ResizeGrid(GRID_MARGIN, GRID_HEIGHT);
    goodsApp.ResizeGrid(GRID_MARGIN, GRID_HEIGHT);
    exGoodsApp.ResizeGrid(GRID_MARGIN, GRID_HEIGHT);

    brandSearch();
    goodsSearch();
    exGoodsSearch();

    //브랜드별 할인 이벤트 정의
    {
        $('.brand-add-btn').click((e) => {
            e.preventDefault();

            searchBrand.Open((code) => {
                if (confirm("선택한 브랜드를 추가하시겠습니까?") === false) return;

                $.post(`/head/standard/std11/dc/brand/${no}`, { 'brand' : code }, () =>{
                    alert('추가되었습니다.');
                    brandSearch();
                })
                .fail((res) => alert(res.responseJSON.msg));
            });
        });
        
        $('.brand-delete-btn').click((e) => {
            e.preventDefault();

            const s_brand_cnt = brandGx.getSelectedRows().length;

            if (s_brand_cnt === 0) {
                alert('삭제할 브랜드를 선택해주세요.');
                return;
            }

            if (confirm("선택한 브랜드를 삭제하시겠습니까?") === false) return;

            brandGx.getSelectedRows().forEach((row, idx) => {        
                $.ajax({
                    async: true,
                    type: 'delete',
                    url: `/head/standard/std11/dc/brand/${no}`,
                    data: { 'brand' : row.brand },
                    success: function (res) {
                        if (idx === s_brand_cnt -1) {
                            alert('삭제되었습니다.');
                            brandSearch();
                        }
                    },
                    error: function(request, status, error) {
                        console.log(request);
                        alert(request.responseJSON.message);
                    }
                });
            });
        });

        $('.brand-submit-btn').click((e) => {
            e.preventDefault();
            const s_brand_cnt = brandGx.getSelectedRows().length;

            if (s_brand_cnt === 0) {
                alert('수정할 브랜드를 선택해주세요.');
                return;
            }

            if (confirm("선택한 브랜드를 수정하시겠습니까?") === false) return;

            brandGx.getSelectedRows().forEach((row, idx) => {        
                $.ajax({
                    async: true,
                    type: 'put',
                    url: `/head/standard/std11/dc/brand/${no}`,
                    data: row,
                    success: function (res) {
                        if (idx === s_brand_cnt -1) {
                            alert('수정되었습니다.');
                            brandSearch();
                        }
                    },
                    error: function(request, status, error) {
                        console.log(request);
                        alert(request.responseJSON.message);
                    }
                });
            });
        });

        $('.brand-manage-btn').click(e => {
            e.preventDefault();
            const url=`/head/standard/std11/dc/brand/${no}`;
            const product=window.open(url,"manager","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=800");
        });
    }

    //상품별 할인 이벤트 정의
    {
        $('.goods-add-btn').click((e) => {
            e.preventDefault();
            goodsPopType = "goods";
            const url='/head/api/goods/show';
            const product=window.open(url,"goods","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=800");
        });
        
        $('.goods-delete-btn').click((e) => {
            e.preventDefault();

            const s_goods_cnt = goodsGx.getSelectedRows().length;

            if (s_goods_cnt === 0) {
                alert('삭제할 상품를 선택해주세요.');
                return;
            }

            if (confirm("선택한 상품를 삭제하시겠습니까?") === false) return;

            const delRows = [];

            goodsGx.getSelectedRows().forEach((row, idx) => {        
                delRows.push(createGoodsData(row));
            });
            
            $.ajax({
                async: true,
                type: 'delete',
                url: `/head/standard/std11/dc/goods/${no}`,
                data: { 'goods_nos' : delRows.join(',') },
                success: function (res) {
                    alert('삭제되었습니다.');
                    goodsSearch();
                },
                error: function(request, status, error) {
                    console.log(request);
                    alert(request.responseJSON.message);
                }
            });
        });

        $('.goods-submit-btn').click((e) => {
            e.preventDefault();
            const s_goods_cnt = goodsGx.getSelectedRows().length;

            if (s_goods_cnt === 0) {
                alert('수정할 상품를 선택해주세요.');
                return;
            }

            if (confirm("선택한 상품를 수정하시겠습니까?") === false) return;

            goodsGx.getSelectedRows().forEach((row, idx) => {        
                $.ajax({
                    async: true,
                    type: 'put',
                    url: `/head/standard/std11/dc/goods/${no}`,
                    data: row,
                    success: function (res) {
                        if (idx === s_goods_cnt -1) {
                            alert('수정되었습니다.');
                            goodsSearch();
                        }
                    },
                    error: function(request, status, error) {
                        console.log(request);
                        alert(request.responseJSON.message);
                    }
                });
            });
        });

        $('.goods-manage-btn').click(e => {
            e.preventDefault();
            const url=`/head/standard/std11/dc/goods/${no}`;
            const product=window.open(url,"manager","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=800");
        });
    }
    
    //할인 제외 상품 이벤트 정의
    {
        $('.ex-goods-add-btn').click((e) => {
            e.preventDefault();
            goodsPopType = "ex-goods";
            const url='/head/api/goods/show';
            const product=window.open(url,"goods","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=800");
        });
        
        $('.ex-goods-delete-btn').click((e) => {
            e.preventDefault();

            const s_goods_cnt = exGoodsGx.getSelectedRows().length;

            if (s_goods_cnt === 0) {
                alert('삭제할 상품를 선택해주세요.');
                return;
            }

            if (confirm("선택한 상품를 삭제하시겠습니까?") === false) return;

            const delRows = [];

            exGoodsGx.getSelectedRows().forEach((row, idx) => {        
                delRows.push(createGoodsData(row));
            });
            
            $.ajax({
                async: true,
                type: 'delete',
                url: `/head/standard/std11/dc/ex-goods/${no}`,
                data: { 'goods_nos' : delRows.join(',') },
                success: function (res) {
                    alert('삭제되었습니다.');
                    exGoodsSearch();
                },
                error: function(request, status, error) {
                    console.log(request);
                    alert(request.responseJSON.message);
                }
            });
        });
    }
</script>
@endif
@stop
