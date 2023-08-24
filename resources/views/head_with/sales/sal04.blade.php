@extends('head_with.layouts.layout')
@section('title','상품별 매출 통계')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">상품별 매출 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 상품별 매출 통계</span>
    </div>
</div>
<form method="get" name="search" id="search">
	<input type="hidden" id="goods_nos" name="goods_nos" />
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">매출일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" id="sdate" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
										<input type="text" class="form-control form-control-sm docs-date" id="edate" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">매출시점</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="ord_state" id="ord_state10" value="10" class="custom-control-input" checked="">
                                    <label class="custom-control-label" for="ord_state10">출고요청</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="ord_state" id="ord_state30" value="30" class="custom-control-input">
                                    <label class="custom-control-label" for="ord_state30">출고완료</label>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-inputZip">상품명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
							</div>
						</div>
					</div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매유형</label>
							<div class="form-inline inline_select_box">
<!--								<div class="form-inline-inner input-box w-75">
									<div class="flax_box">
										<select name='sale_place' class="form-control form-control-sm" style="width: 95%">
											<option value=''>전체</option>
											@foreach ($sale_places as $sale_place)
												<option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
											@endforeach
										</select>
									</div>
								</div>-->
								<div class="form-inline-inner input-box w-25">
									<div class="form-inline form-check-box">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="mobile_yn" id="mobile_yn" value = "">
											<label class="custom-control-label" for="mobile_yn">모바일</label>
										</div>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="app_yn" id="app_yn" value = "">
											<label class="custom-control-label" for="app_yn">앱</label>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                    <div class="col-lg-8 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">결제방법</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[0]" id="stat_pay_type_16" value="16">
                                    <label class="custom-control-label" for="stat_pay_type_16">계좌이체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[1]" id="stat_pay_type_32" value="32">
                                    <label class="custom-control-label" for="stat_pay_type_32">핸드폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[2]" id="stat_pay_type_1" value="1">
                                    <label class="custom-control-label" for="stat_pay_type_1">현금</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[3]" id="stat_pay_type_2" value="2">
                                    <label class="custom-control-label" for="stat_pay_type_2">카드</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[4]" id="stat_pay_type_4" value="4">
                                    <label class="custom-control-label" for="stat_pay_type_4">포인트</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[5]" id="stat_pay_type_8" value="8">
                                    <label class="custom-control-label" for="stat_pay_type_8">쿠폰</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="stat_pay_type[6]" id="stat_pay_type_64" value="64">
                                    <label class="custom-control-label" for="stat_pay_type_64">가상계좌</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputCity">품목</label>
                            <div class="flax_box">
                                <select name='item' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($items as $item)
                                    <option value='{{ $item->cd }}'>{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">주문구분</label>
                            <div class="form-inline form-check-box">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[0]" id="ord_type_5" value="5" checked>
									<label class="custom-control-label" for="ord_type_5">교환</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[1]" id="ord_type_4" value="4" checked>
									<label class="custom-control-label" for="ord_type_4">예약</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[2]" id="ord_type_3" value="3" checked>
									<label class="custom-control-label" for="ord_type_3">특별주문</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[3]" id="ord_type_13" value="13" checked>
									<label class="custom-control-label" for="ord_type_13">도매주문</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[4]" id="ord_type_12" value="12" checked>
									<label class="custom-control-label" for="ord_type_12">서비스</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[5]" id="ord_type_17" value="17" checked>
									<label class="custom-control-label" for="ord_type_17">기관납품</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[6]" id="ord_type_14" value="14" checked>
									<label class="custom-control-label" for="ord_type_14">수기</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[7]" id="ord_type_15" value="15" checked>
									<label class="custom-control-label" for="ord_type_15">정상</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" name="ord_type[8]" id="ord_type_16" value="16" checked>
									<label class="custom-control-label" for="ord_type_16">오픈마켓</label>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">상품상태 :</label>
							<div class="flax_box">
								<select name="goods_stat" id="goods_stat" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($goods_stats as $goods_stat)
										<option value="{{ $goods_stat->code_id }}">{{ $goods_stat->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<div class="card shadow mb-1" id="chart_area">
	<div class="card-body">
		<input type="hidden" id="chart-type" value="date">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="date-tab" data-toggle="tab" href="#home" role="tab" aria-controls="date" aria-selected="true">매출액(결제금액 + 적립금 - 수수료)</a>
			</li>
		</ul>
		<div id="opt_chart" style="height: 100%; min-height:300px;"></div>
	</div>
</div>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
				<div class="fr_box flax_box">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" name="view_chart" id="view_chart" checked>
						<label class="custom-control-label" for="view_chart">차트보기</label>
					</div>
				</div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="width:100%;height:calc(100vh - 370px);" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<div class="card shadow mb-0">
    <div class="card-body">
        <div class="card-title">
            <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
        </div>
        <ul class="mb-0">
            <li>매출액 = 과세 + 비과세</li>
            <li>매출원가 = 실제판매원가</li>
            <li>부가세 = 과세 - ( 과세 / 1.1 )</li>
            <li>세전 매출이익 = 매출액 - 매출원가</li>
        </ul>
    </div>
</div>
<script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
<script language="javascript">
	let columns = [
		{field: "chk", pinned: 'left', headerName: '체크', cellClass: 'hd-grid-code', checkboxSelection: true, width: 40},
		{
            headerName: "상품상태",
            field: "goods_state",
            type: 'GoodsStateType',
            pinned: 'left'
        },
        {
            headerName: "품목",
            field: "opt_kind_nm",
            width: 80,
            pinned: 'left'
        },
        {
            headerName: "브랜드",
            field: "brand_nm",
            width: 120,
            pinned: 'left'
        },
        {
            headerName: "스타일넘버",
            field: "style_no",
            width: 80,
            pinned: 'left',
            cellStyle:{"text-align" : "center"}
        },
        {
            headerName: "상품명",
            field: "goods_nm",
            type: 'GoodsNameType',
            pinned: 'left',
            aggSum: "합계",
            aggAvg: "평균"
        },
        {
            field: "jaego_qty",
            headerName: "재고",
            type: 'numberType',
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openHeadStock(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                }
            },
            pinned: 'left'
        },
        {
            headerName: '매출액구분',
            children: [{
                    headerName: "수량",
                    field: "sum_qty",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "sum_point_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "sum_dc_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "sum_coupon_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "sum_fee_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "sum_recv_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "과세",
                    field: "sum_taxation_amt",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "비과세",
                    field: "sum_taxfree",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "비중",
                    field: "ratio",
                    type: 'percentType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: "부가세",
            field: "vat",
            type: 'currencyType',
            aggregation: true
        },
        {
            headerName: "매출액",
            field: "sum_amt",
            type: 'currencyType',
            aggregation: true
        },
        {
            headerName: "매출원가",
            field: "wonga_60",
            type: 'currencyType',
            aggregation: true
        },
        {
            headerName: "마진율",
            field: "margin",
            type: 'percentType',
            valueGetter: function(params) {
                if (params.data.goods_nm === "합계" || params.data.goods_nm === "평균") {
                    const data = params.data;
                    return (1 - parseInt(data.sum_wonga) / (parseInt(data.sum_recv_amt) + parseInt(data.sum_point_amt) - parseInt(data.sum_fee_amt))) * 100;
                }
                return params.data.margin;
            }
        },
        {
            headerName: '매출이익',
            children: [{
                    headerName: "세전",
                    field: "margin1",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "세후",
                    field: "margin2",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '판매',
            children: [{
                    headerName: "수량",
                    field: "sum_qty",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "point_amt_10",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "dc_amt_10",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "coupon_amt_10",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "수수료",
                    field: "fee_amt_10",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "결제금액",
                    field: "sum_wonga",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
        {
            headerName: '교환',
            children: [{
                    headerName: "수량",
                    field: "qty_60",
                    type: 'numberType',
                    aggregation: true
                },
                {
                    headerName: "적립금",
                    field: "point_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "할인",
                    field: "dc_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
                {
                    headerName: "쿠폰",
                    field: "coupon_amt_60",
                    type: 'currencyType',
                    aggregation: true
                },
            ]
        },
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;
	let chart_data = null;
	let chart;
	
    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
		let options = {
			getRowStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return { 'background': '#eee', 'font-weight': 'bold' }
				}
			},
			onSelectionChanged: e => evtAfterEdit(e)
		};
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, options);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();

		if($('#mobile_yn').is(':checked')) {
			$('#mobile_yn').val('Y');
		}

		if($('#app_yn').is(':checked')) {
			$('#app_yn').val('Y');
		}
		
        gx.Aggregation({
            "sum": "top",
            "avg": "top"
        });
		
		gx.Request('/head/sales/sal04/search', data, -1, function(data) {
		});
    }

	function drawCanvasByDate(list) {

		if(list.length === 0){
			return;
		}

		$('#goods_nos').val(list.toString());

		let formData = $('form[name="search"]').serialize();
		let chart = null;

		$.ajax({
			method: 'get',
			url: '/head/sales/sal04/chart-data/search',
			data: formData,
			success: function (res) {
				chart_data = res.chart_data;

				$('#opt_chart').html('');
				let series_data = [];
				let data = [];

				const sdate = $('#sdate').val();
				const edate = $('#edate').val();

				let diff = new Date(edate) - new Date(sdate);
				let currDay = 24 * 60 * 60 * 1000;// 시 * 분 * 초 * 밀리세컨

				let diffDay = (diff / currDay) + 1;

				for(let i = 0; i < diffDay; i++) {
					let date = new Date(sdate);
					let dateConvertor = date.setDate(date.getDate() + i);
					let dateYear = new Date(dateConvertor).getFullYear();
					let dateMonth = Number(new Date(dateConvertor).getMonth()) > 9 ? String(Number(new Date(dateConvertor).getMonth())) : ("0" + String(Number(new Date(dateConvertor).getMonth()) + 1));
					let dateDay =  Number(new Date(dateConvertor).getDate()) > 9 ? String(Number(new Date(dateConvertor).getDate())) : ("0" + String(Number(new Date(dateConvertor).getDate())));

					data.push(
						{
							'date' : dateYear + dateMonth + dateDay
						}
					);
				}

				//상품명 그룹화
				const result = chart_data.reduce((acc, curr) => {
					const { goods_nm } = curr;
					if (acc[goods_nm]) acc[goods_nm].push(curr);
					else acc[goods_nm] = [curr];
					return acc;
				}, {});

				Object.keys(result).forEach(function(key, index) {
					const element = result[key];
					element.forEach(function(ele) {
						data.map(function(row) {
							if (String(ele.sale_date) === String(row.date)) {
								row[ele.goods_nm] = Number(ele.sum_amt);
							} else if(row[ele.goods_nm] === undefined){
								row[ele.goods_nm] = 0;
							}
						});
					});
				});

				Object.keys(result).forEach(function(element, index){
					series_data.push(
						{
							type: 'line',
							title: element,
							xKey: 'date',
							yKey: element,
							yName: '매출액'
						}
					);
				});

				const chartOption = {
					container: document.getElementById('opt_chart'),
					autoSize: true,
					data: data,
					theme: {
						palette: {
							fills: ["#c16068","#a2bf8a","#ebcc87","#80a0c3","#b58dae"],
							strokes: ["#c16068","#a2bf8a","#ebcc87","#80a0c3","#b58dae"],
						},
						overrides: {
							column: { series: { strokeWidth: 5 } },
							line: { series: { strokeWidth: 5, marker: { enabled: false } } },
						},
					},
					series: series_data,
					axes: [
						{
							type: 'category',
							position: 'bottom',
							label: {
								rotation: 90,
							},
						},
						{
							type: 'number',
							position: 'right',
							label: {
								formatter: (params) => {
									return params.value / 1000 + 'k';
								},
							},
						},
					],
					legend: {
						item: {
							marker: {
								shape: 'square',
								strokeWidth: 0,
							},
						},
					},
				};

				if(chart !== null) {
					chart.destroy();
				}

				chart = agCharts.AgChart.create(chartOption);
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});
	}

	const evtAfterEdit = (e) => {
		if(gx.getSelectedRows().length > 5) {
			gx.gridOptions.api.deselectIndex(gx.getSelectedNodes().splice(gx.getSelectedRows().length -1, 1)[0].childIndex);
			alert('5개 이상 초과할 수 없습니다.');
			return;
		}

		let goodsList = [];

		gx.getSelectedRows().forEach(function(element, index) {
			goodsList.push(element.goods_no);
		});

		drawCanvasByDate(goodsList);
	};

	$('#view_chart').change(() => {
		$('#chart_area').toggle();
		drawCanvasByDate([]);
	});
	
</script>



@stop
