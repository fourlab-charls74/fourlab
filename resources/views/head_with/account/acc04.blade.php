@extends('head_with.layouts.layout')
@section('title','마감')
@section('content')

<style> .pop {text-decoration:underline !important; } </style>

<div class="page_tit">
	<h3 class="d-inline-flex">정산지급 및 계산서</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 입점&정산</span>
		<span>/ 정산지급 및 계산서</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
	<input type='hidden' name='data' value=''>
	@csrf
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>자료받기</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="sdate">등록일</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
											<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
								<span class="text_line">~</span>
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
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
					<div class="col-lg-4">
						<div class="form-group">
							<label for="name">업체</label>
							<div class="flex_box flex-nowrap">
								<input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-company" style="width: 60%;">
								<a href="#" style="margin-left: 1rem; margin-right: 1rem;" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								<input type="text" id="com_cd" name="com_cd" class="form-control form-control-sm" readonly style="background: white; width: 30%;">
							</div>
						</div>
					</div>
					<div class="col-lg-4">
                        <div class="form-group">
                            <label for="tax_state">상태</label>
                            <div class="flex_box flex-nowrap">
                                <select name="tax_state" id="tax_state" class="form-control form-control-sm mr-2" style="width:70%">
									<option value=''>전체</option>
                                    <?php
                                        foreach ($tax_state as $item) {
											$id = $item->code_id;
											$val = $item->code_val;
                                            echo "<option value='${id}'>${val}</option>";
                                        }
                                    ?>
                                </select>
								<div class="custom-control custom-checkbox form-check-box" style="margin-left: 1rem; width:30%; min-width: 170px;">
                                    <input type="checkbox" name="tax_state_ex" id="tax_state_ex" class="custom-control-input" value="-1" checked/>
                                    <label class="custom-control-label" for="tax_state_ex">삭제 건 제외</label>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>

		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 자료받기</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>

	</div>


	<!-- DataTales Example -->
	<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
		<div class="card-body">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box">
						<div class="flex_box">
							<div class="docs-datepicker form-inline-inner input_box mr-2" style="width: 120px">
								<div class="input-group">
									<input type="text" class="form-control form-control-sm docs-date" name="day" value="{{ $edate }}" autocomplete="off">
									<div class="input-group-append">
										<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
										<i class="fa fa-calendar" aria-hidden="true"></i>
										</button>
									</div>
								</div>
								<div class="docs-datepicker-container"></div>
							</div>
							<a href="#" onclick="pubTax();" class="btn btn-sm btn-primary shadow-sm mr-1">세금계산서 발행</a>
							<a href="#" onclick="payTaxSheet();" class="btn btn-sm btn-primary shadow-sm mr-1">지급</a>
							<a href="#" onclick="openDownloadPopup();" class="btn btn-sm btn-primary shadow-sm mr-1">세금계산서 다운로드</a>
						</div>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</form>

<div class="card shadow">
	<div class="card-body">
		<div class="card-title">
			<h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
		</div>
		<ul class="mb-0">
			<li>매출금액 = 판매금액 - 클레임금액 - 할인금액 - 쿠폰금액(업체부담) + 배송비 + 기타정산액</li>
			<li>판매수수료 = 수수료지정 : 판매가격 * 수수료율, 공급가지정 : 판매가격 - 공급가액</li>
			<li>수수료 = 판매수수료 - 할인금액</li>
			<li>정산금액 = 매출금액 - 수수료</li>
			<li>쿠폰금액(본사부담) = 판매촉진비 수수료 매출 신고</li>
			<li>카드수수료 등 수수료 부담의 주체가 귀사에 있으므로 입점업체의 경우 매출 신고 시에 해당 매출금액에 대하여 현금성으로 신고</li>
		</ul>
	</div>
</div>

<script type="text/javascript" charset="utf-8">

	/**
	 * constants
	 */
	const URL = {
		SHOW: '/head/account/acc04/show',
		TAX: {
			PUB: '/head/account/acc04/tax/pub',
			PAY: '/head/account/acc04/tax/pay'
		}
	};

    /**
     * ag grid columns
     */
    var columns = [
		{field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, pinned: 'left', sort: null},
		{field: "day",			headerName: "마감일자",		width:140, pinned: 'left', cellStyle: { 'text-align': 'center' }},
		{field: "com_nm",		headerName: "업체명",		width:140 },
		{field: "sale_amt",		headerName: "판매금액",		width:100, type: 'currencyType', aggregation: true},
		{field: "clm_amt",		headerName: "클레임금액",	width:100, type: 'currencyType', aggregation: true},
		{field: "dc_amt",		headerName: "할인금액",		width:90, type: 'currencyType', aggregation: true},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "coupon_com_amt",
					headerName: "(업체부담)",
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "dlv_amt",	headerName: "배송비",	width:90, type: 'currencyType', aggregation: true},
		{field: "etc_amt",	headerName: "기타정산액",	width:110, type: 'currencyType', aggregation: true},
		{
			headerName: '매출금액',
			children: [
				{
					field: "sale_net_taxation_amt",
					headerName: "과세",
					width:100,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_taxfree_amt",
					headerName: "비과세",
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_amt",
					headerName: "소계",
					width:100,
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "tax_amt",	headerName: "부가세", width:90, type: 'currencyType', aggregation: true, hide: false},
		{
			headerName: '수수료',
			children: [
				{
					field: "fee",
					headerName: "판매수수료",
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_dc_amt",
					headerName: "할인금액",
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_net",
					headerName: "소계",
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "acc_amt",	headerName: "정산금액",	width:100,	type: 'currencyType', aggregation: true},
		{
			headerName: '쿠폰금액',
			children: [
				{
					field: "allot_amt",
					headerName: "(본사부담)",
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "tax_day", headerName: "세금계산서",  cellStyle: { 'text-align': 'center' } },
		{field: "pay_day", headerName: "지급일",  cellStyle: { 'text-align': 'center' } },
		{
            width: 'auto'
        }
	];


    /**
     * ag grid init
     */

	const pApp = new App('',{
		gridId: "#div-gd",
	});
	let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(225);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            },
			onPinnedRowDataChanged: (params) => {
				if (gx.getRows().length > 0) {
					let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
					gx.gridOptions.api.setPinnedTopRowData([
						{ ...pinnedRow.data, day: '합계' }
					]);
				}
			}
        };
        gx = new HDGrid(gridDiv, columns, options);
    });

	function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top" });
        gx.Request('/head/account/acc04/search', data, -1);
    }

	const pubTax = () => {

		if (confirm('세금계산서 발행일자를 수정 하시겠습니까?')) {

			const rows = gx.getSelectedRows();
			let data = [];
			rows.map((item) => {
				const { idx } = item;
				const str = idx;
				data.push(str);
			});
			data = data.join('::');
			if ( data == "" ) {
				alert('자료를 선택해 주십시오.');
				return;
			}

			const { day } = document.search;
			axios({
                url: URL.TAX.PUB,
                method: 'post',
                data: { day: day.value, data: urlEncode(data) }
            }).then((response) => {
                if (response.data.result == 1) {
                    window.Search();
                } else {
					alert("처리 시 장애가 발생했습니다. 다시 시도하여 주십시오.");		
				}
            }).catch((error) => { console.log(error) });
			
		}
	};

	const payTaxSheet = () => {

		if (confirm('지급일자를 수정 하시겠습니까?')) {

			const rows = gx.getSelectedRows();
			let data = [];
			rows.map((item) => {
				const { idx } = item;
				const str = idx;
				data.push(str);
			});
			data = data.join('::');
			if ( data == "" ) {
				alert('자료를 선택해 주십시오.');
				return;
			}
			
			const { day } = document.search;
			axios({
                url: URL.TAX.PAY,
                method: 'post',
                data: { day: day.value, data: urlEncode(data) }
            }).then((response) => {
                if (response.data.result == 1) {
                    window.Search();
                } else {
					alert("처리 시 장애가 발생했습니다. 다시 시도하여 주십시오.");		
				}
            }).catch((error) => { console.log(error) });

		}

	};

	const openDownloadPopup = () => {
		
		const rows = gx.getSelectedRows();
		let data = [];
		
		rows.map((item) => {
			const { idx } = item;
			data.push(idx);
		});
		data.sort();

		const tax_sheet_data = data.join('::');
		if ( tax_sheet_data == "" ) {
			alert('자료를 선택해 주십시오.');
			return;
		}

		const POP_URL = URL.SHOW;
        const target = "popForm";
        
        const [ top, left, width, height ] = [ 100, 100, 800, 500 ];
        const child_window = window.open(POP_URL, target, `toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=${top},left=${left},width=${width},height=${height}`);

        const form = document.search;
        form.action = POP_URL;
        form.method = 'post';
        form.target = target;
        form.data.value = tax_sheet_data;
        form.submit();
		
	};

	/*
		Function: urlEncode
			문자열 인코딩 ( encodeURIComponent 사용 )

		Parameters:
			str - 문자열

		Returns:
			encoded str
	*/

	function urlEncode(str){

		var ch;
		var estr = encodeURIComponent(str);

		// 특수문자 처리
		re = /%C2%A0/gi;
		estr =estr.replace(re,"%20");
		return estr;
	}


</script>

@stop