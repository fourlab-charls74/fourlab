@extends('store_with.layouts.layout')
@section('title','기타재반자료')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">기타재반자료</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 정산/마감관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<a href="#" onclick="return openBatchPopup();" class="btn btn-sm btn-primary mr-1 shadow-sm"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 자료일괄등록</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="sdate">판매기간(판매연월)</label>
							<div class="docs-datepicker flex_box">
								<div class="input-group">
								<input type="text" id="sdate" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
									<div class="input-group-append">
										<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
											<i class="fa fa-calendar" aria-hidden="true"></i>
										</button>
									</div>
								</div>
								<div class="docs-datepicker-container"></div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_type">매장구분</label>
							<div class="flex_box">
								<select name='store_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_types as $store_type)
										<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_kind">매장종류</label>
							<div class="flex_box">
								<select name='store_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_kinds as $store_kind)
										<option value='{{ $store_kind->code_id }}'>{{ $store_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
                        </div>
					</div>
				</div>
				<div class="row">
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
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
			<a href="#" onclick="return openBatchPopup();" class="btn btn-sm btn-primary mr-1 shadow-sm"><i class="fas fa-plus fa-sm text-white-50 mr-1"></i> 자료일괄등록</a>
			<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
        <div class="card-title mb-2">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
				<div class="fr_box">
					<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return updateExtraData();"><i class="fas fa-save fa-sm mr-1"></i> 선택매장 자료저장</a>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	const YELLOW = {'background-color': "#ffff99"};
	const CENTER = { 'text-align': 'center' };
    const columns = [
		{ field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28 },
		{ headerName: "#", field: "num", type: 'NumType', pinned: 'left', width: 30, cellStyle: CENTER,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : parseInt(params.value) + 1,
        },
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 57, cellStyle: CENTER },
        { field: "store_type_nm", headerName: "매장구분", pinned: 'left', width: 70, cellStyle: CENTER },
        { field: "store_nm", headerName: "매장명", pinned: 'left', type: 'StoreNameType', width: 150 },
		@foreach ($extra_cols as $group_nm => $children)
		{ headerName: "{{ $group_nm }}",
			children: [
				@foreach ($children as $child)
					{ headerName: "{{ $child->code_val }}", field: "{{ $child->code_id }}_amt", type: 'currencyType', width: 100, 
						editable: "{{ $child->code_id }}" !== 'E3', cellStyle: "{{ $child->code_id }}" !== 'E3' ? YELLOW : {}
					},
					@if (in_array($child->code_id, ['P1', 'M3']))
					{ headerName: "{{ $child->code_val }}(-VAT)", field: "{{ $child->code_id }}_novat", type: 'currencyType', width: 105,
						cellRenderer: (params) => Math.round((params.data["{{ $child->code_id }}_amt"] || 0) / 1.1),
					},
					@endif
				@endforeach
				@if (!in_array($group_nm, ['마일리지', '기타운영경비']))
				{ headerName: "소계", field: "{{ str_split($children[0]->code_id ?? '')[0] }}_sum", type: 'currencyType', width: 100 },
				@endif
			]
        },
		@if ($group_nm === '관리')
		{ headerName: "사은품" },
		{ headerName: "부자재" },
		@endif
		@endforeach
		{ field: "total", headerName: "총합계", type: 'currencyType', width: 100 },
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
		gx = new HDGrid(gridDiv, columns, {
			onCellValueChanged: (e) => {
				if (e.oldValue !== e.newValue) {
					const val = e.newValue;
					if (isNaN(val) || val == '' || parseFloat(val) < 0) {
						alert("숫자만 입력가능합니다.");
						e.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
					} else {
						const group_cd = e.column.colId.split("")[0];

						// E1(온라인RT), E2(온라인반송)은 소계에 포함시키지 않습니다. (because, E3(온라인) = E1 - E2)
						if (['E1', 'E2'].includes(e.column.colId.split("_")[0])) {
							e.data['E3_amt'] = e.data['E1_amt'] - e.data['E2_amt'];
						}

						// 각 소계 계산
						e.data[group_cd + "_sum"] 
							= Object.keys(e.data).reduce((a,c) => (
								(c.split("")[0] === group_cd && c.split("_").slice(-1)[0] === "amt" && !['E1', 'E2'].includes(c.split("_")[0])) 
									? (e.data[c] * 1) : 0
							) + a, 0);

						// 총합계 계산
						e.data.total = Object.keys(e.data).reduce((a,c) => (c.split("_").slice(-1)[0] === "sum" ? (e.data[c] * 1) : 0) + a, 0);

						e.api.redrawRows({ rowNodes: [e.node] });
						e.node.setSelected(true);
						gx.setFocusedWorkingCell();
					}
				}
			}
		});
		gx.gridOptions.defaultColDef = {
			suppressMenu: true,
			resizable: false,
			sortable: true,
		};

		Search();
	});

	function Search(sdate = '') {
		let data = $('form[name="search"]').serialize();
		if (sdate !== '') data += "&sdate=" + sdate;
		gx.Request('/store/account/acc05/search', data, -1, (d) => {
			setColumns(d.head.gifts, d.head.expandables);
			if (sdate !== '') $("#sdate").val(sdate);
		});
	}

	function setColumns(gifts, expandables) {
		const cols = columns.reduce((a, c) => {
			let col = {...c};
			if(col.headerName === '부자재') {
				col.children = expandables.map(exp => ({ headerName: exp.prd_nm, field: exp.type + "_" + exp.prd_cd + "_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW}))
					.concat({ headerName: "소계", field: "S_sum", type: 'currencyType', width: 100 });
			}
			if(col.headerName === '사은품') {
				col.children = gifts.map(gf => ({ headerName: gf.prd_nm, field: gf.type + "_" + gf.prd_cd + "_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW}))
					.concat({ headerName: "소계", field: "G_sum", type: 'currencyType', width: 100 });
			}
			a.push(col);
			return a;
		}, []);

		gx.gridOptions.api.setColumnDefs([]);
		gx.gridOptions.api.setColumnDefs(cols);
    }

	// 선택매장 자료저장
	function updateExtraData() {
		let rows = gx.getSelectedRows();
		if (rows.length < 1) return alert("자료저장할 매장을 선택해주세요.");

		if (!confirm("선택매장의 자료를 저장하시겠습니까?")) return;
		alert("다소 시간이 소요될 수 있습니다. 잠시만 기다려주세요.");

		axios({
            url: '/store/account/acc05/save',
            method: 'post',
            data: { data: rows }
        }).then((res) => {
            if (res.data.code === "200") {
                alert("자료가 정상적으로 저장되었습니다.");
                Search();
            } else {
                alert("자료저장 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.");
                console.log(res);
            }
        }).catch((err) => {
            alert("에러가 발생했습니다. 관리자에게 문의해주세요.");
            console.log(err);
        });
	}

	// 자료일괄등록 팝업오픈
	function openBatchPopup() {
		const url = '/store/account/acc05/show-batch';
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=2100,height=1200");
	}
</script>
@stop
