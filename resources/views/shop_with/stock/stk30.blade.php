@extends('shop_with.layouts.layout')
@section('title','창고반품')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">창고반품</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 매장관리</span>
		<span>/ 창고반품</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="initSearch(['#store_no'])">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">반품일자</label>
                            <div class="form-inline date-select-inbox">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
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
                            <label for="">반품상태</label>
                            <div class="d-flex">
                                <select name='sr_state' class="form-control form-control-sm">
									<option value=''>전체</option>
                                    @foreach ($sr_states as $sr_state)
                                    <option value='{{ $sr_state->code_id }}'>{{ $sr_state->code_val }}</option>
                                    @endforeach
								</select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">반품사유</label>
                            <div class="d-flex">
                                <select name='sr_reason' class="form-control form-control-sm">
									<option value=''>전체</option>
                                    @foreach ($sr_reasons as $sr_reason)
                                    <option value='{{ $sr_reason->code_id }}'>{{ $sr_reason->code_val }}</option>
                                    @endforeach
								</select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">반품창고</label>
                            <div class="d-flex">
                                <select name='storage_cd' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach (@$storages as $storage)
                                        <option value='{{ $storage->storage_cd }}'>{{ $storage->storage_nm }}</option>
                                    @endforeach
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
                                        <option value="sr_cd">반품코드</option>
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
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
                    <div class="d-flex">
                        <!-- <div class="d-flex">
                            <select id='chg_return_state' name='chg_return_state' class="form-control form-control-sm mr-1" style='width:70px;display:inline'>
                                <option value="30">이동</option>
                                <option value="40">완료</option>
                            </select>
                        </div>
                        <a href="javascript:void(0);" onclick="ChangeState()" class="btn btn-sm btn-primary">상태변경</a>
                        <span class="d-none d-lg-block ml-2 mr-2 tex-secondary">|</span> -->
                        <a href="javascript:void(0);" onclick="ChangeState()" class="btn btn-sm btn-outline-primary">반품진행</a>
                    </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">

    function StyleReturnState(params) {
        let state = {
            "10":"#222222", // 요청
            "30":"#0000ff", // 이동
            "40":"#2aa876", // 완료
        }
        if (params.value !== undefined) {
            if (state[params.data.sr_state]) {
                return {
                    'color': state[params.data.sr_state],
                    'text-align': 'center'
                }
            }
        }
    }

	let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28,
            // checkboxSelection: function(params) {
            //     return params.data.sr_state < 40;
            // },
        },
        {field: "sr_cd", headerName: "반품코드", width: 100, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.value}</a>`;
            }
        },
        {field: "sr_date", headerName: "반품일자", width: 100, cellStyle: {"text-align": "center"}},
        {field: "sr_state", hide: true},
        {field: "sr_state_nm", headerName: "반품상태", width: 60, cellStyle: StyleReturnState},
        {field: "sr_kind", hide: true},
        {field: "storage_cd", hide: true},
        {field: "storage_nm", headerName: "반품창고", width: 100, cellStyle: {"text-align": "center"}},
        {field: "store_type", hide: true},
        {field: "store_cd", headerName: "매장코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "매장명", width: 200, cellStyle: {"text-align": "center"}},
        {field: "sr_qty", headerName: "반품수량", type: "currencyType", width: 80},
        {field: "sr_price", headerName: "반품금액", type: "currencyType", width: 80},
        {field: "sr_reason", hide: true},
        {field: "sr_reason_nm", headerName: "반품사유", width: 120, cellStyle: {"text-align": "center"}},
        {field: "comment", headerName: "메모", width: 300},
        {width: "auto"},
	];
</script>
<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/shop/stock/stk30/search', data, 1);
	}

    // 창고반품관리 팝업 오픈
    function openDetailPopup(sr_cd) {
        const url = '/shop/stock/stk30/show/' + sr_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }

    // 반품상태변경
    function ChangeState() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("상태변경할 항목을 선택해주세요.");

        let wrong_list = rows.filter(r => r.sr_state != 10);
        if(wrong_list.length > 0) return alert("'요청'상태의 항목만 반품 처리할 수 있습니다.");

        if(!confirm("선택한 항목의 반품상태를 '이동'으로 변경하시겠습니까?")) return;

        axios({
            url: '/shop/stock/stk30/update-return-state',
            method: 'put',
            data: {
                data: rows,
                new_state: chg_state
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("상태변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop
