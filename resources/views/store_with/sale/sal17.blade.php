@extends('store_with.layouts.layout')
@section('title','매장별목표매출관리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장별목표매출관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 매장별목표매출관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<input type='hidden' id='is_searched' name='is_searched' value='{{$is_searched}}'>
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매기간</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" id="sdate" name="sdate" value="{{ $sdate }}" onchange="return isSearch('');" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date month" id="edate" name="edate" value="{{ $edate }}" onchange="return isSearch('');"  autocomplete="off">
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
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
									@foreach ($store_channel as $sc)
										<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
                    </div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
						<label for="">매장사용 유무</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="store_yn" value="" id="store_a" class="custom-control-input" checked>
									<label class="custom-control-label" for="store_a">전체</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="store_yn" value="Y" id="store_y" class="custom-control-input">
									<label class="custom-control-label" for="store_y">Y</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="store_yn" value="N" id="store_n" class="custom-control-input">
									<label class="custom-control-label" for="store_n">N</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="formReset()"> -->
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
				<div class="fr_box">
					<a href="#" id="save_btn" onclick="Save();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-save fa-sm text-white-50"></i> 저장</a>
                </div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<style>
    .hd-grid-red {
        color: red;
    }
</style>
<script type="text/javascript" charset="utf-8">

	//const IS_SEARCHED = document.search.is_searched.value;
	let columns = [
        { headerName: "#", field: "num", type:'NumType', pinned:'left', aggSum:"합계", cellStyle: { 'text-align': "center" },
            cellRenderer: function (params) {
                if (params.node.rowPinned === 'top') {
                    return "";
                } else {
                    return parseInt(params.value) + 1;
                }
            }
        },
        { field: "store_channel", headerName: "판매채널", pinned:'left', width:90, cellStyle: { 'text-align': "center" }},
        { field: "store_channel_kind", headerName: "매장구분", pinned:'left', width:90, cellStyle: { 'text-align': "center" },
			cellRenderer: function (params) {
					if (params.node.rowPinned === 'top') {
						return "합계";
					} else {
						return params.data.store_channel_kind
					}
				},
		},
        { field: "scd", headerName: "매장코드", pinned:'left', hide: true },
        { field: "store_nm", headerName: "매장명", pinned:'left', type: 'StoreNameType', width: 200},
        {
            field: "summary", headerName: "합계",
            children: [
                {field: "proj_amt", headerName: "목표", type: 'currencyType', width:100, aggregation: true },
                {field: "recv_amt", headerName: "금액", type: 'currencyMinusColorType', width:100, aggregation: true },
                {field: "progress_proj_amt", headerName: "달성율(%)", type: 'currencyMinusColorType', aggregation: true,
                    cellRenderer: params => goalProgress(params.data)
                },
                {field: "last_recv_amt", headerName: "전년", type: 'currencyMinusColorType', width:100, aggregation: true },
				{field: "growth_rate", headerName: "성장율(%)", aggregation: true,
					cellRenderer : function(params) {
						let recv_amt = toInt(params.data.recv_amt);
						let last_recv_amt = toInt(params.data.last_recv_amt);
						if(recv_amt != 0 && last_recv_amt != 0) {
							return ((recv_amt / last_recv_amt)*100).toFixed(0);
						} else {
							return "0";
						}

					}
                },
            ]
        },
        @foreach($months as $index => $month)
        {
            field: "{{$month["val"]}}", headerName: "{{$month["fmt"]}}",
            children: [
                { field: 'proj_amt_{{$month["val"]}}', headerName: "목표", type: 'currencyType', width:100, aggregation: true,
                    editable: params => params.node.rowPinned === 'top' ? false : true,
                    cellStyle: params => {
                        if (params.node.rowPinned === 'top') {
                            return {};
                        } else {
                            return { 'background': '#ffff99' };
                        }
                    }
                },
                { field: 'recv_amt_{{$month["val"]}}', headerName: "금액", type: 'currencyMinusColorType', width:100, aggregation: true},
                // { field: 'prev_recv_amt_{{$month["val"]}}', headerName: "전월", type: 'currencyMinusColorType', width:75, aggregation: true },
                { field: 'progress_proj_amt_{{$month["val"]}}', headerName: "달성율(%)", type: 'currencyMinusColorType', aggregation: true,
                    cellRenderer: function(params) {
						let progress = 0;
						let proj_amt = toInt(params.data.proj_amt_{{$month['val']}});
						let recv_amt = toInt(params.data.recv_amt_{{$month['val']}});

						if (proj_amt == 0) return progress = 0; //목표액이 0이면 달성율도 0으로 표시
						if (recv_amt == 0) return progress = 0;

						if(proj_amt != null && recv_amt != null) {
							progress = Comma(Math.round((recv_amt / proj_amt ) * 100));
						}
						if (progress == -Infinity) progress = 0;

						return progress;
					}
                },
                { field: 'last_recv_amt_{{$month["val"]}}', headerName: "전년", type: 'currencyMinusColorType', width:100, aggregation: true },
				{ field: 'growth_rate_{{$month["val"]}}', headerName: "성장율(%)", aggregation: true,
                    cellRenderer: function(params) {
						let progress = 0;
						let recv_amt = toInt(params.data.recv_amt_{{$month['val']}});
						let last_recv_amt = toInt(params.data.last_recv_amt_{{$month['val']}});

						if (recv_amt == 0) return progress = 0; //목표액이 0이면 달성율도 0으로 표시
						if (last_recv_amt == 0) return progress = 0;

						if(recv_amt != null && last_recv_amt != null) {
							progress = Comma(Math.round(( recv_amt / last_recv_amt) * 100));
						}
						if (progress == -Infinity) progress = 0;

						return progress;
					}
                },
            ]
        },
        @endforeach
        {field: "", headerName: "", width: "auto"}
    ];

	/**
	 * ( 목표 - 결제금액 ) / 목표 * 100 = 달성율(%)
	 * 
	 * 	달성율 = 금액 / 목표금액 * 100 
	 * 
	 * 
	 */

	const goalProgress = (row, Ym) => {
		let prefix = "";
		let progress = 0;

		if (Ym) prefix = `_${Ym}`;

		let proj_amt = toInt(row[`proj_amt${prefix}`]);
		let recv_amt = toInt(row[`recv_amt${prefix}`]);

		if (proj_amt == 0) return progress = 0; //목표액이 0이면 달성율도 0으로 표시
		if (recv_amt == 0) return progress = 0; //금액이 0이면 달성율도 0으로 표시
		if (proj_amt == 0 && recv_amt == 0) return progress = 0;

		progress = Comma(Math.round(( recv_amt / proj_amt ) * 100)); // 소수점 첫째짜리까지 반올림 처리

		if (progress == -Infinity) progress = 0;

		return progress;
	};
	
	const pApp = new App('',{
		gridId:"#div-gd",
	});

	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		let options = {
			getRowStyle: (params) => params.node.rowPinned ? ({'font-weight': 'bold', 'background-color': '#eee', 'border': 'none'}) : false,
			onCellValueChanged: params => evtAfterEdit(params)
		}
		gx = new HDGrid(gridDiv, columns, options);
		initStore()

        // if ($('#is_searched').val() === 'y') {
            Search();
        // }


		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();
	});

	function isSearch(val){
        $('#is_searched').val(val);
    }
	
	function Search() {
		if ($('#is_searched').val() === 'y') {
			let data = $('form[name="search"]').serialize();
			gx.Aggregation({ sum: "top" });
			gx.Request('/store/sale/sal17/search', data, -1);
		} else {
            isSearch('y');
			$('form[name="search"]').submit();
		}
	}

	function initStore() {
        const store_cd = '{{ @$store->store_cd }}';
        const store_nm = '{{ @$store->store_nm }}';

        if(store_cd != '') {
            const option = new Option(store_nm, store_cd, true, true);
            $('#store_cd').append(option).trigger('change');
        }
    }

	const formReset = () => {
		document.search.reset();
	};

	const startEditingCell = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

	

	const evtAfterEdit = async (params) => {

		let prev_value = parseInt(params.oldValue);
        let value = params.newValue;

		if (prev_value !== value) {
			let row = params.data;
            const row_index = params.rowIndex;
			const rowNode = gx.gridOptions.api.getRowNode(row_index);
            const column_name = params.column.colId;

			let regExp = /(?=proj_amt_).+/i;
			let arr = column_name.match(regExp);
			let parseValue = parseInt(value);

			if (arr) {
				if (isNaN(parseValue) == true || parseValue == "") {
					alert("숫자만 입력가능합니다.");
					startEditingCell(row_index, column_name);
					rowNode.setDataValue(column_name, parseInt(0));
					return false;
				} else {

					/* 목표 바꾸면 같이 바뀌는 부분 일괄저장을 하기 때문에 필요없어보이지만 혹시 몰라 주석처리 */

					// prev_value = toInt(params.oldValue);
					// value = toInt(params.newValue);

					// regExp = new RegExp(/[proj_amt_]/, "g");
					// const Ym = column_name.replace(regExp, "");

					// // 목표 금액부터 반영
					// row[column_name] = value;
					// gx.gridOptions.api.applyTransaction({ update: [row] });

					// // 목표 금액, 달성률 반영
					// regExp = /(?=proj_amt_).+/i;
					// const proj_keys = Object.keys(row).filter(key => {
					// 	return key.match(regExp) ? true : false;
					// });

					// let total_proj = 0;
					// proj_keys.map(key => {
					// 	total_proj = total_proj + toInt(row[key]);
					// });

					// row['proj_amt'] = total_proj;
					// row[`progress_proj_amt_${Ym}`] = row[`proj_amt_{{$month["val"]}}`] / row[`recv_amt_{{$month["val"]}}`] * 100;

				}
			}
		}
	};

	//매장별 목표 저장
	async function Save() {
		if (!confirm("목표를 저장하시겠습니까?")) return;

		let rows = gx.getRows();
		let sdate = $("#sdate").val();
		let edate = $("#edate").val();

        try {
            const response = await axios({ 
                url: '/store/sale/sal17/update',
                method: 'post', 
                data: { 
					data: rows,
					sdate : sdate,
					edate : edate
				}
            });
            const { data } = response;
            if (data?.code == 200) {
				alert(data.msg);
                Search();
            } else {
                alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
            }
        } catch (error) {
            // console.log(error);
        }

	}

	const toInt = (value) => {
		if (value == "" || value == NaN || value == null || value == undefined) return 0;
		return parseInt(value);
	};

</script>
@stop
