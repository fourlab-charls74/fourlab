@extends('store_with.layouts.layout')
@section('title','상품반품관리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">상품반품관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 상품관리</span>
		<span>/ 상품반품관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="initSearch()" hidden>검색조건 초기화</a> -->
                    @if(Auth('head')->user()->logistics_group_yn == 'N')
                    <a href="javascript:void(0);" onclick="openDetailPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 반품 등록</a>
                    <a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 반품 일괄등록</a>
                    @endif
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">출고창고</label>
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
                            <label for="">반품사유</label>
                            <div class="flex_box">
                                <select name='return_reason' class="form-control form-control-sm" style="width: 100%;">
                                    <option value="">전체</option>
                                    @foreach (@$return_reason as $rr)
                                        <option value='{{ $rr->code_id }}'>{{ $rr->code_val }}</option>
                                    @endforeach
                                </select>
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="sgr_type">반품구분</label>
                            <div class="d-flex">
                                <select name="sgr_type" id="sgr_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="G">일반</option>
                                    <option value="B">일괄</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sgr_state">반품상태</label>
                            <div class="d-flex">
                                <select name="sgr_state" id="sgr_state" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="10">접수</option>
                                    <option value="30">완료</option>
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
                                        <option value="sgr_cd">반품코드</option>
                                        <option value="sgr_date">반품일자</option>
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
                            {{-- 반품이동처 --}}
                            <label for="">반품업체</label>
                            <div class="flex_box">
                                <select name='target_com_cd' class="form-control form-control-sm" style="width: 100%;">
                                    <option value="">전체</option>
                                    @foreach (@$sup_coms as $sup_com)
                                        <option value='{{ $sup_com->com_id }}'>{{ $sup_com->com_nm }}</option>
                                    @endforeach
                                </select>
                                <!-- <span class="text_line" style="width: 6%; text-align: center;">/</span>
                                <select name='target_storage_cd' class="form-control form-control-sm" style="width: 47%;">
                                    <option value="">창고 전체</option>
                                    @foreach (@$storages as $storage)
                                        <option value='{{ $storage->storage_cd }}'>{{ $storage->storage_nm }}</option>
                                    @endforeach
                                </select> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a> -->
            @if(Auth('head')->user()->logistics_group_yn == 'N')
            <a href="javascript:void(0);" onclick="openDetailPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 반품 등록</a>
            <a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 반품 일괄등록</a>
            @endif
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
                        <a href="javascript:void(0);" onclick="ChangeState()" class="btn btn-sm btn-primary mr-1"><i class="fas fa-check fa-sm text-white-50 mr-1"></i> 반품완료처리</a>
                        <a href="javascript:void(0);" onclick="DelReturn()" class="btn btn-sm btn-outline-primary"><i class="fas fa-trash-alt fa-sm"></i> 삭제</a>
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
	let columns = [
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: false, sort: null, width: 28,
            checkboxSelection: function(params) {
                return params.data.sgr_state < 30;
            },
        },
        {field: "sgr_cd", headerName: "반품코드", width: 100, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.value}</a>`;
            }
        },        
        {field: "sgr_date", headerName: "반품일자", width: 80, cellStyle: {"text-align": "center"}},
        {field: "storage_nm", headerName: "창고명", width: 100, cellStyle: {"text-align": "center"}},
        {field: "storage_cd", headerName: "창고코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "target_nm", headerName: "반품업체", width: 120,
            cellRenderer: (params) => (params.data.target_type == "C" ? " " : params.data.target_type == "S" ? " " : "") + params.value,
        }, // 반품이동처
        {field: "sgr_type", hide: true},
        {field: "sgr_type_nm", headerName: "반품구분", width: 60, cellStyle: (params) => ({"text-align": "center", "color": params.data.sgr_type == "B" ? "#2aa876" : "none"})},
        {field: "sgr_state", hide: true},
        {field: "sgr_state_nm", headerName: "반품상태", width: 60, cellStyle: (params) => ({"text-align": "center", "color": params.data.sgr_state == "30" ? "#2aa876" : "#0000ff"})},
        {field: "target_type", hide: true},
        {field: "target_cd", hide: true},
        {field: "sgr_qty", headerName: "반품수량", type: "currencyType", width: 60},
        {field: "sgr_price", headerName: "반품금액", type: "currencyType", width: 60},
        {field: "comment", headerName: "메모", width: 300},
        {field: "return_reason_nm", headerName: "반품사유", width: 200},
        {field: "print", headerName: "명세서 출력", cellStyle: {"text-align": "center", "color": "#4444ff", "font-size": '13px'},
			cellRenderer: function(params) {
				if(params.data.sgr_state >= 10) {
					return `<a href="javascript:void(0);" style="color: inherit;" onclick="printDocument(${params.data.sgr_cd})">출력</a>`;
				} else{
					return '-';
				}
			}
		},
        {width: "auto"}
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
		gx.Request('/store/cs/cs02/search', data, 1);
	}

    // 거래처반품 상품관리 팝업 오픈
    const openDetailPopup = (sgr_cd = '') => {
        const url = '/store/cs/cs02/show/' + sgr_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    };
    
    // 거래처반품 일괄등록 팝업 오픈
    const openBatchPopup = () => {
        const url = '/store/cs/cs02/batch';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }

    // 반품상태변경
    function ChangeState() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("반품완료처리할 항목을 선택해주세요.");

        let wrong_list = rows.filter(r => r.sgr_state != 10);
        if(wrong_list.length > 0) return alert("'접수'상태의 항목만 '완료'처리할 수 있습니다.");

        if(!confirm("선택한 항목을 반품완료처리 하시겠습니까?")) return;

        axios({
            url: '/store/cs/cs02/update-return-state',
            method: 'put',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("반품완료처리 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 반품정보 삭제
    function DelReturn() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("삭제할 항목을 선택해주세요.");

        let wrong_list = rows.filter(r => r.sgr_state != 10);
        if(wrong_list.length > 0) return alert("'접수'상태의 항목만 삭제할 수 있습니다.");

        if(!confirm("삭제한 거래처반품정보는 다시 되돌릴 수 없습니다.\n선택한 항목을 삭제하시겠습니까?")) return;

        axios({
            url: '/store/cs/cs02/del-return',
            method: 'delete',
            data: {
                sgr_cds: rows.map(r => r.sgr_cd),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data);
                alert("삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 상품반품 명세서 출력
	function printDocument(sgr_cd) {
		location.href = '/store/cs/cs02/download?sgr_cd=' + sgr_cd;
	}
</script>
@stop
