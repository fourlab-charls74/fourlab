@extends('store_with.layouts.layout')
@section('title','기타재반자료')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">기타재반자료</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 매장관리</span>
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
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
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
					<div class="col-lg-4">
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
					<div class="col-lg-4">
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
					<div class="col-lg-4">
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
			<input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="initSearch()">
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
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
					<a href="#" class="btn btn-sm btn-primary shadow-sm" onclick="return DataEdit();"><span class="fs-12">선택 매장 수정</span></a>
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
					{ headerName: "{{ $child->code_val }}", field: "{{ $child->code_id }}_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW },
					@if (in_array($child->code_id, ['P1', 'M3']))
					{ headerName: "{{ $child->code_val }}(-VAT)", field: "{{ $child->code_id }}_novat", type: 'currencyType', width: 100,
						cellRenderer: (params) => Math.round((params.data["{{ $child->code_id }}"] || 0) / 1.1),
					},
					@endif
				@endforeach
				@if (!in_array($group_nm, ['마일리지', '기타운영경비']))
				{ headerName: "소계", field: "{{ $group_nm }}_sum", type: 'currencyType', width: 100 },
				@endif
			]
        },
		@if ($group_nm === '관리')
		{ headerName: "부자재",
			children: [
				@foreach ($expandables as $exp)
					{ headerName: "{{ $exp->prd_nm }}", field: "{{ $exp->prd_cd }}_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW },
				@endforeach
				{ headerName: "소계", field: "expandables_sum", type: 'currencyType', width: 100 },
			]
        },
		{ headerName: "사은품",
			children: [
				@foreach ($gifts as $gift)
					{ headerName: "{{ $gift->prd_nm }}", field: "{{ $gift->prd_cd }}_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW },
				@endforeach
				{ headerName: "소계", field: "gifts_sum", type: 'currencyType', width: 100 },
			]
        },
		@endif
		@endforeach
		{ field: "total", headerName: "총합계", type: 'currencyType', width: 100 },
        { width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId: "#div-gd" });
	let gx;
	let current_Ym = "";

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		gx.gridOptions.defaultColDef = {
			suppressMenu: true,
			resizable: false,
			sortable: true,
		};
		// gx.gridOptions.onCellValueChanged = params => evtAfterEdit(params);
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc05/search', data, -1, (d) => {
			current_Ym = (document.search.sdate.value).replace("-", "");
			setColumns(d.head.gifts, d.head.expandables);
		});
	}

	function setColumns(gifts, expandables) {
		const cols = columns.reduce((a, c) => {
			let col = {...c};
			if(col.headerName === '부자재') {
				col.children = expandables.map(exp => ({ headerName: exp.prd_nm, field: exp.prd_cd + "_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW}))
					.concat({ headerName: "소계", field: "expandables_sum", type: 'currencyType', width: 100 });
			}
			if(col.headerName === '사은품') {
				col.children = gifts.map(gf => ({ headerName: gf.prd_nm, field: gf.prd_cd + "_amt", type: 'currencyType', editable: true, width: 100, cellStyle: YELLOW}))
					.concat({ headerName: "소계", field: "gifts_sum", type: 'currencyType', width: 100 });
			}
			a.push(col);
			return a;
		}, []);

		gx.gridOptions.api.setColumnDefs([]);
		gx.gridOptions.api.setColumnDefs(cols);
    }

	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////

	const sumChildren = (params, children) => {
		const row = params.data;
		let total_str = 0;
		total_str = children.reduce((prev, curr) => {
			let id = curr.code_id;
			let value = row[`${id}_code`];
			if (value === null) value = 0;
			return prev += parseInt(value);
		}, total_str)
		return isNaN(total_str) ? 0 : total_str;
	};

	async function DataEdit() {
		let arr = [];
        let rows = gx.getSelectedRows();
        for (let i=0; i < rows.length; i++) {
            let row = rows[i];
			let regExp = /.+(?=_code)/i;
			
			const code_ids = Object.keys(row)
				.filter(key => key.match(regExp))
				.map(key => key.split('_code')[0]);
			const code_amts = code_ids.map(id => row[`${id}_code`] ? row[`${id}_code`] : 0);

			arr.push({
				codes: code_ids,
				amts: code_amts,
				store_cd: row.store_cd,
				ymonth: row.ymonth ? row.ymonth : current_Ym
			});
        }
		
        try {
            const response = await axios({ 
                url: '/store/account/acc05/save',
                method: 'post', 
                data: { selected_data: arr } 
            });
            const { data } = response;
            if (data?.code == 200) {
				alert('저장되었습니다.');
                Search();
            } else {
                alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
            }
        } catch (error) {
            // console.log(error);
        }
    }

	const evtAfterEdit = (params) => {
		if (params.oldValue !== params.newValue) {
			row = params.data;
			const rowNode = params.node;
			const column_name = params.column.colId;
			const value = params.newValue;
			if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
				alert("숫자만 입력가능합니다.");
				startEditingCell(params.rowIndex, column_name);
			}
			rowNode.setSelected(true);
		}
	};

    const startEditingCell = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

</script>
@stop
