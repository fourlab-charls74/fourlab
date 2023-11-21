@extends('shop_with.layouts.layout')
@section('title','수선관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">수선관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ A/S 관리</span>
        <span>/ 수선관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <!-- {{-- <a href="/store/standard/std11/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a> --}} -->
                    <a href="#" onclick="openReceipt()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 등록</a>
		            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="#" onclick="formReset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="date_type">일자</label>
                            <div class="form-inline date-select-inbox">
                                <select id="date_type" name="date_type" class="form-control form-control-sm" style="width:30%; margin-right: auto;">
                                    <option value="receipt_date">접수일자</option>
                                    <option value="h_receipt_date">본사접수일</option>
                                    <option value="end_date">수선완료일</option>
                                    <option value="err_date">불량등록일</option>
                                </select>
                                <div class="docs-datepicker form-inline-inner input_box" style="width:30%">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date search-enter" id="sdate" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box" style="width:30%">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date search-enter" id="edate" name="edate" value="{{ $edate }}" autocomplete="off">
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
                            <label for="where1">조회구분</label>
                            <div class="flex_box">
                                <select class="form-control form-control-sm" name="where1" id="where1" onchange="changeWhere1(this)">
                                    <option value="">조회내역없음</option>
                                    <option value="a.customer">고객명</option>
                                    <option value="a.mobile">전화번호</option>
                                    <option value="a.prd_cd">바코드</option>
                                    <option value="a.goods_nm">상품명</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="where2">조회내역</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm search-enter" id='where2' name='where2' value='' disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="as_type">접수구분</label>
                            <div class="flex_box">
                                <select class="form-control form-control-sm" name="as_type" id="as_type">
                                    <option value="">전체</option>
                                    <option value="1">매장접수(A/S)</option>
                                    <option value="2">매장접수(불량)</option>
                                    <option value="3">매장접수(심의)</option>
                                    <option value="4">접수완료/진행</option>
                                    <option value="5">완료(정상)</option>
                                    <option value="6">완료(불량)</option>
                                </select>
                            </div>
                        </div>
                    </div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="as_state">수선상태</label>
							<div class="d-flex justify-content-between align-items-center">
								<select class="form-control form-control-sm" name="as_state" id="as_state">
									<option value="">전체</option>
									@foreach($as_states as $as)
										<option value="{{ $as->code_id }}">{{ $as->code_val }}</option>
									@endforeach
								</select>
								<div class="custom-control custom-checkbox form-check-box ml-1" style="min-width: 110px;">
									<input type="checkbox" class="custom-control-input" name="ext_done_state" id="ext_done_state" value="Y" checked>
									<label class="custom-control-label font-weight-normal" for="ext_done_state">완료 조회 제외</label>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="/store/standard/std11/create" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <a href="#" onclick="return remove();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>수선삭제</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<script language="javascript">

        /*
		 * 접수 구분
		 *  1 : 매장접수(A/S)
		 *  2 : 매장접수(불량)
		 *  3 : 매장접수(심의)
		 *  4 : 접수완료/진행
		 *  5 : 완료(정상)
		 *  6 : 완료(불량)
		 */

		/**
		 * 수선진행상태
		 *  10 : 매장접수
		 *  11 : 매장접수    10, 11, 12 상태값이 같음
		 *  12 : 매장접수
		 *  20 : 본사접수
		 *  30 : 진행중
		 *  40 : 완료(정상)
		 *  50 : 완료(불량)
		 */

    const DEFAULT_STYLE = {'text-align': 'center'};

    const CELL_COLOR = {
        HQ_RECEIPT: {'background' : '#ffff99'},
        DONE: {'background' : '#E5E7E9'}
    };

    const columns = [
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, width: 40, pinned: 'left', sort: null,
            checkboxSelection: params => {
                //본사에서만 본사AS진행중일때도 삭제 가능
					if(params.data.as_state == 10 || params.data.as_state == 11 || params.data.as_state == 12) {
						return true;
					} else {
						return false;
					}
				}
        },
        // this row shows the row index, doesn't use any data from the row
        {
            headerName: '#',
            width: 40,
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
            cellStyle: { ...DEFAULT_STYLE, "background": CELL_COLOR.DONE },
            pinned: 'left'
        },
        { field: "idx", headerName: '접수번호', width:100, pinned:'left', maxWidth: 100, cellRenderer: 'loadingRenderer', 
            cellStyle: { ...DEFAULT_STYLE, 'font-size': '13px', 'font-weight': 500 },
            cellRenderer: (params) => {
                return `<a href="#" onclick="openReceiptDetail(${params.value})" style="text-decoration: underline !important">${params.value}</a>`
            },
            pinned: 'left'
        },
        { field: "receipt_date", headerName: "접수일자", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "as_state", headerName: "수선상태", width: 100, pinned: 'left' ,
            cellRenderer: (params) => {
                switch (params.value) {
                    case 10:
                    case 11:
                    case 12:
                        return "매장접수";
                    case 20:
                        return "본사접수";
                    case 30:
                        return "진행중";
                    case 40:
                        return "완료(정상)";
                    case 50:
                        return "완료(불량)";
                }
            },
            cellStyle: (params) => {
                switch (params.value) {
                    case 10:
                    case 11:
                    case 12:
                        return {'text-align' : 'center'};
                    case 20:
                        return {'color' : 'blue' , 'text-align' : 'center'};
                    case 30:
                        return {'color' : 'green' , 'text-align' : 'center'};
                    case 40:
                        return {'color' : 'red' , 'text-align' : 'center'};
                    case 50:
                        return {'color' : 'purple' , 'text-align' : 'center'};
                   
                }

            }
        },
        { field: "store_cd", headerName: "접수매장", width: 80, cellStyle: DEFAULT_STYLE, pinned: 'left', hide:true },
        { field: "store_nm", headerName: "접수매장", width: 80, cellStyle: DEFAULT_STYLE, pinned: 'left' },
        { field: "as_type", headerName: "접수구분", width: 100, cellStyle: DEFAULT_STYLE, pinned: 'left',
            cellRenderer: (params) => {
                switch (params.value) {
                    case "1": 
                        return "매장접수(A/S)";
                    case "2": 
                        return "매장접수(불량)";
                    case "3": 
                        return "매장접수(심의)";
                    case "4": 
                        return "접수완료/진행";
                    case "5": 
                        return "완료(정상)";
                    case "6": 
                        return "완료(불량)";
                    default:
                        return params.value;
                };
            }
        },
        { field: "customer_no", headerName: "고객 아이디", width: 100, cellStyle: DEFAULT_STYLE, },
        { field: "customer", headerName: "고객명", width: 80, cellStyle: DEFAULT_STYLE, },
        { field: "mobile", headerName: "핸드폰번호", width: 100, cellStyle: DEFAULT_STYLE,  },
        { field: "zipcode", headerName: "우편번호", width: 80, cellStyle: DEFAULT_STYLE,  },
        { field: "addr1", headerName: "주소", width: 200, cellStyle: DEFAULT_STYLE,  },
        { field: "addr2", headerName: "상세주소", width: 100, cellStyle: DEFAULT_STYLE, },
        { field: "prd_cd", headerName: "바코드", width: 120, cellStyle: DEFAULT_STYLE, },
        { field: "goods_nm", headerName: "상품명", width: 300, cellStyle: DEFAULT_STYLE,  },
        { field: "color", headerName: "컬러", width: 60, cellStyle: DEFAULT_STYLE,  },
		{ field: "size_kind_cd", headerName: "사이즈구분", width: 80, cellStyle: DEFAULT_STYLE,  },
        { field: "size", headerName: "사이즈", width: 60, cellStyle: DEFAULT_STYLE, },
        // { field: "qty", headerName: "수량", width: 60, cellStyle: DEFAULT_STYLE, },
        { field: "is_free", headerName: "수선 유료구분", width: 100, cellStyle: DEFAULT_STYLE, },
        { field: "as_amt", headerName: "수선 금액", width: 80, cellStyle: {'text-align' : 'right'}, type: 'currencyType'},
        { field: "content", headerName: "수선내용", width: 300, cellStyle: DEFAULT_STYLE, },
        { field: "h_receipt_date", headerName: "본사접수일", width: 100, cellStyle: DEFAULT_STYLE, },
        { field: "end_date", headerName: "수선완료일", width: 100, cellStyle: DEFAULT_STYLE, },
        { field: "err_date", headerName: "불량등록일", width: 100, cellStyle: DEFAULT_STYLE, },
        { field: "h_content", headerName: "본사설명", width: 300, cellStyle: DEFAULT_STYLE, },
        { field: "rt", headerName: "등록일", width: 120, cellStyle: DEFAULT_STYLE, },
        { field: "ut", headerName: "수정일", width: 120, cellStyle: DEFAULT_STYLE, },
        
    ];

</script>
<script type="text/javascript" charset="utf-8">

    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    const options = {
        rowStyle: CELL_COLOR.COMMON,
        getRowStyle: params => {
            let as_type = params.data.as_type;
            if (as_type == '4') return CELL_COLOR.HQ_RECEIPT;
            if (as_type == '5' || as_type == '6') return CELL_COLOR.DONE
        },
    };

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, options);
        Search();
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/standard/std11/search', data, -1, (data) => {});
    }

    const formReset = () => {
        document.search.reset();
    };

    const changeWhere1 = (obj) => {
        if (obj.value == "") {
            document.search.where2.disabled = true;
        } else {
            document.search.where2.disabled = false;
        }
    };

    const batchEdit = async () => {
        const rows = gx.getSelectedRows();
        const date_type = document.batch.edit_date_type.value;
        const date_type_nm = document.querySelector(`select[name='edit_date_type'] option[value='${date_type}']`).innerText;
        const date = document.batch.edit_date.value;

        if (Number.isNaN(Date.parse(date))) {
            alert("유효한 날짜 형식을 입력해주세요");
            return false;
        }

        if (rows.length == 0) {
            alert("수정할 항목을 선택해주세요");
            return false;
        }
        
        const msg = `체크된 항목들의 ${date_type_nm}을 일괄수정하시겠습니까?`;
        const confirmed = window.confirm(msg);

        if (confirmed == true) {
            try {
                const response = await axios({ 
                    url: '/store/standard/std11/batch-edit',
                    method: 'post', 
                    data: { data: rows, type: date_type, date: date }
                });
                const { data } = response;
                if (data?.code == 200) {
                    alert("수정되었습니다.");
                    Search();
                } else {
                    alert("처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.");
                }
            } catch (error) {
                // console.log(error)
            }
        }
    };

    function openReceipt() {
        const url = '/shop/standard/std11/view/';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }


    function openReceiptDetail(idx) {
        const url = '/shop/standard/std11/detail/' + idx;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
    }


    function remove() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("삭제할 항목을 선택해주세요.");
        if(!confirm("수선정보를 삭제하시겠습니까?")) return;
        
        axios({
            url: '/shop/standard/std11/delete',
            method: 'post',
            data: {
                idx : rows.map(r => r.idx),
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


</script>

@stop
