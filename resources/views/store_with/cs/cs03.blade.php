@extends('store_with.layouts.layout')
@section('title','원부자재입고/반품')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">원부자재입고/반품</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 생산입고관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <a href="#" onclick="add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="fa fa-exchange-alt fs-16"></i> 입고/반품</a>
                    <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="formrow-firstname-input">일자</label>
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
                                        <input type="text" class="form-control form-control-sm docs-date search-enter" name="edate" value="{{ $edate }}" autocomplete="off">
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
                            <label for="prd_ord_no">입고번호(송장번호)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id="prd_ord_no" name='prd_ord_no' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="state">입고/반품상태</label>
                            <div class="flex_box">
                                <select name="state" class="form-control form-control-sm w-100">
                                    <option value="">모두</option>
                                    <option value='10'>입고대기</option>
                                    <option value='20'>입고처리중</option>
                                    <option value='30'>입고완료</option>
                                    <option value='-10'>반품대기</option>
                                    <option value='-20'>반품처리중</option>
                                    <option value='-30'>반품완료</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="com_nm">원부자재 업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_cd" name="com_cd" />
                                        <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>원부자재코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='text' id="prd_cd_sub" name='prd_cd_sub' class="form-control form-control-sm w-100 ac-style-no search-enter">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd_sub"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="prd_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" id="prd_nm" name='prd_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="user_nm">입고자</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" id="user_nm" name='user_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="#" onclick="add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> </a>
            <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
        <div class="card-body">
            <div class="card-title mb-3">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box">
                        <select id='change_state' name='change_state' class="form-control form-control-sm" style='width:130px; display:inline'>
                            <option value=''>선택</option>
                            <option value='20'>입고처리중</option>
                            <option value='30'>입고완료</option>
                            <option value='-20'>반품처리중</option>
                            <option value='-30'>반품완료</option>
                        </select>
                        <a href="javascript:void(0);" onclick="changeState();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-sync fs-16 mr-1"></i>입고/반품 상태변경</a>
                        <a href="#" onclick="del()" class="btn-sm btn btn-primary">입고/반품 삭제</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" charset="utf-8">
    // ag-grid set

    var columns = [{
            field: "chk",
            headerName: '',
            cellClass: 'hd-grid-code',
            headerCheckboxSelection: false,
            checkboxSelection: (params) => {
                const state = params.data.state;
                return state == 10 || state == 20 || state == -10 || state == -20 ? true : false;
            },
            width: 28,
            sort: null,
            pinned: 'left',
        },
        {
            field: "prd_ord_date",
            headerName: "일자",
            width: 100
        },
        {
            field: "prd_ord_no",
            headerName: "입고번호",
            width: 120
        },
        {
            field: "state",
            headerName: "상태",
            width: 72,
            cellRenderer: (params) => { // 상태:입고대기(10), 입고처리중(20), 입고완료(30), 반품대기(-10), 반품처리중(-20), 반품완료(-30)
                const state = params.data.state;
                switch (state) {
                    case "10":
                        return "입고대기";
                    case "20":
                        return "입고처리중";
                    case "30":
                        return "입고완료";
                    case "-10":
                        return "반품대기";
                    case "-20":
                        return "반품처리중";
                    case "-30":
                        return "반품완료";
                }
            }
        },
        {
            field: "sup_com_nm",
            headerName: "공급업체",
            width: 100
        },
        {
            field: "prd_cd",
            headerName: "원부자재코드",
            width: 130
        },
        {
            field: "prd_nm",
            headerName: "원부자재명",
            width: 84
        },
        {
            field: "color",
            headerName: "칼라",
            width: 96
        },
        {
            field: "size",
            headerName: "사이즈",
            width: 84
        },
        {
            field: "unit",
            headerName: "단위",
            width: 84
        },
        {
            field: "qty",
            headerName: "수량",
            width: 60,
            type: 'numberType'
        },
        {
            field: "price",
            headerName: "단가",
            width: 60,
            type: 'currencyType'
        },
        {
            field: "amount",
            headerName: "금액",
            width: 60,
            type: 'currencyType'
        },
        {
            field: "rt",
            headerName: "등록일자",
            width: 110,
            cellStyle: {
                "line-height": "30px"
            }
        },
        {
            field: "ut",
            headerName: "수정일자",
            width: 110,
            cellStyle: {
                "line-height": "30px"
            }
        },
        {
            field: "user_nm",
            headerName: "입고자",
            width: 96
        },
        {
            width: "auto"
        }
    ];

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {};
        gx = new HDGrid(gridDiv, columns, options);
        Search();

        $("#img").click(function() {
            gx.gridOptions.columnApi.setColumnVisible('img', $("#img").is(":checked"));
        });
    });

    // logics

    const strNumToPrice = (price) => {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };

    function Search() {
        let data = $("form[name=search]").serialize();
        gx.Request('/store/cs/cs03/search', data, 1, () => {});
    };

    const add = () => {
        const url = '/store/cs/cs03/buy';
        const [width, height] = [1700, 1000];
        const pop = window.open(url, "_blank", "toolbar=no,scrollbars=no,resizable=yes,status=yes,top=100,left=100,width=" + width + ",height=" + height);
    };

    const changeState = () => {
        let state;
        let prd_ord_nos = [];
        let prd_cds = [];
        let qties = [];
        const select = document.querySelector('#change_state');
        if (select.value) {
            state = select.value;
        } else {
            alert('입고/반품 상태를 선택 해 주십시오.');
            document.search.change_state.focus();
            return false;
        };
        const rows = gx.getSelectedRows();
        if (rows.length < 1) return alert('상태를 변경할 항목을 선택해주세요.');
        if (confirm('입고/반품 상태를 변경하시겠습니까?')) {
            if (Array.isArray(rows) && !(rows.length > 0)) {
                alert('항목을 선택 해 주십시오.')
                select.focus();
                return false;
            } else {
                for (let i=0; i < rows.length; i++ ) {
                    const row = rows[i];
                    if (row.state == 10 || row.state == 20) {
                        if (state < 0) {
                            alert('입고중인 상품은 입고 관련 상태만 변경하실 수 있습니다.\n선택 항목을 다시 확인해주세요.');
                            return false;
                        }
                    } else if (row.state == -10 || row.state == -20) {
                        if (state > 0) {
                            alert('반품중인 상품은 반품 관련 상태만 변경하실 수 있습니다.\n선택 항목을 다시 확인해주세요.');
                            return false;
                        }
                    }
                    prd_ord_nos[i] = row.prd_ord_no;
                    prd_cds[i] = row.prd_cd;
                    qties[i] = row.qty;
                }
                axios({
                    url: '/store/cs/cs03/update',
                    method: 'put',
                    data: {
                        state: state,
                        prd_ord_nos: prd_ord_nos,
                        prd_cds: prd_cds,
                        qties: qties
                    }
                }).then((response) => {
                    if (response.data.code == 200) {
                        alert("입고/반품 상태가 변경되었습니다.");
                        Search();
                    } else {
                        alert("상태 변경 중 에러가 발생했습니다. 잠시 후 다시 시도 해주세요.");
                    }
                }).catch((error) => {
                    // console.log(error.response.data);
                });
            };
        };
    };

    const del = () => {
        let prd_ord_nos = [];
        let prd_cds = [];
        if (confirm('삭제하시겠습니까?')) {
            const rows = gx.getSelectedRows();
            if (Array.isArray(rows) && !(rows.length > 0)) {
                alert('항목을 선택 해 주십시오.')
                return false;
            } else {
                let state = document.search.change_state;
                for (let i=0; i < rows.length; i++ ) {
                    const row = rows[i];
                    if (row.state == 20 || row.state == -20) {
                        alert('상태가 입고대기/반품대기인 경우에만 삭제 가능합니다.');
                        return false;
                    }
                    prd_ord_nos[i] = row.prd_ord_no;
                    prd_cds[i] = row.prd_cd;
                }
                axios({
                    url: '/store/cs/cs03/delete',
                    method: 'delete',
                    data: {
                        prd_ord_nos: prd_ord_nos,
                        prd_cds : prd_cds
                    }
                }).then((response) => {
                    if (response.data.code == 200) {
                        alert("삭제되었습니다");
                        Search();
                    } else {
                        alert("삭제중 에러가 발생했습니다. 잠시 후 다시시도 해주세요.");
                    }
                }).catch((error) => {
                    // console.log(error.response.data);
                });
            }
        };
    };

    //원부자재 업체 검색
    $( ".sch-sup-company" ).on("click", () => {
        searchCompany.Open(null, '6', 'wonboo');
    });

    
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
@stop