@extends('store_with.layouts.layout')
@section('title','판매처수수료관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">판매처수수료관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보관리</span>
        <span>/ 판매처수수료관리</span>
    </div>
</div>

<style>
    @media (max-width: 740px) {
        #div-gd {height: 130px !important;}
    }
</style>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
		            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
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
							<label for="store_cd">매장코드</label>
                            <div class="form-inline">
                                <input type="text" id="store_cd" name="store_cd" class="form-control form-control-sm w-100 search-enter" />
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="store_nm">매장명</label>
                            <div class="form-inline">
                                <input type="text" id="store_nm" name="store_nm" class="form-control form-control-sm w-100 search-enter" />
                            </div>
						</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_A" name="use_yn" value="" />
                                    <label class="custom-control-label" for="use_yn_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_Y" name="use_yn" value="Y" checked />
                                    <label class="custom-control-label" for="use_yn_Y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_N" name="use_yn" value="N" />
                                    <label class="custom-control-label" for="use_yn_N">N</label>
                                </div>
                            </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
        </div>
    </div>
</form>

<div class="row show_layout">
    <div class="col-lg-3 pr-1">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 pt-1 pb-1">
                <h5 class="m-0">매장목록</h5>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-2">매장별 판매처수수료정보<span id="select_store_nm"></span></h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
                    {{-- <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateStoreFee()"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="resetStoreFee()">전체 초기화</button> --}}
                </div>
                <div>
                    <div class="flex_box">
                        <span style="font-size:small"><strong>사용여부 : </strong></span>&nbsp&nbsp
                        <select name='charge_yn' id="charge_yn" style="width:150px" class="form-control form-control-sm" onchange="changeUseYn();">
                            <option value="A" selected>전체</option>
                            <option value="Y">Y</option>
                            <option value="N">N</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-store-fee" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "store_cd", headerName: "매장코드", width: 60, cellStyle: {"text-align": "center"}},
        {field: "store_channel", headerName: "판매채널", width: 65, cellStyle: {"text-align": "center"}},
        {field: "store_channel_kind", headerName: "매장구분", width: 65, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "매장명", width: 115, 
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='SearchDetail("${params.data.store_cd}", "${params.value}")'>${params.value}</a>`;
            }
        },
        {field: "use_yn", headerName: "사용여부", cellStyle: {"text-align": "center"}, width: 55},
        {width: 0},
    ];

    let fee_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "use_yn", headerName: "사용", pinned: "left", cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return params.value || 'N';
            }
        },
        {field: "pr_code_cd", headerName: "코드", width: 60, cellStyle: {"text-align": "center"}},
        {field: "pr_code_nm", headerName: "수수료명", width: 100, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='showDetailPopup("${params.data.store_cd}", "${params.data.pr_code_cd}")'>${params.value}</a>`;
            }
        },
        {field: "sdate", headerName: "시작일", width: 90, cellStyle: {"text-align": "center"}},
        {field: "edate", headerName: "종료일", width: 90, cellStyle: {"text-align": "center"}},
        {field: "store_fee", headerName: "판매처수수료(%)", width: 120, type: "percentType"},
        {field: "grade_cd", hide: true},
        // {field: "grade_nm", headerName: "매장등급", width: 80, cellStyle: {"text-align": "center"},
        //     cellRenderer: (params) => {
        //         return `<a href='javascript:void(0)' onclick='showStoreGradePopup("${params.value || params.data.grade_cd}")'>${params.value || params.data.grade_cd || ''}</a>`;
        //     }
        // },
        // {field: "manager_fee", headerName: "중간관리수수료(%)", width: 120, type: "percentType"},
        {field: "comment", headerName: "메모", width: 300},
        {width: 0},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx, gx2;

    const pApp = new App('', { gridId: "#div-gd" });
    const pApp2 = new App('', { gridId: "#div-gd-store-fee" });
    let cur_store_cd = "";
    let cur_store_nm = "";

    $(document).ready(function() {
        // 매장목록
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        // 마진정보
        pApp2.ResizeGrid(275);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, fee_columns, {
			isRowSelectable: (params) => false,
			getRowStyle: (params) => {
				if (params.data.use_yn === 'Y')  return { 'background': '#b7e4ff' };
			},
        });

        $('#charge_yn').val('Y');
        // 최초검색
        Search();

        // 검색조건 숨김 시 우측 grid 높이 설정
        $(".search_mode_wrap .dropdown-menu a").on("click", function(e) {
            if(pApp2.options.grid_resize == true){
                pApp2.ResizeGrid(275);
            }
        });

        //판매채널 선택전 매장구분 disable처리
        load_store_channel()
    });

    // 매장목록 조회
    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/standard/std07/search", data, -1, function(d) {
            if(cur_store_cd === "" && d.body.length > 0) {
                SearchDetail(d.body[0].store_cd, d.body[0].store_nm);
            }
        });
    }

    // 검색조건 초기화
    function formReset(id) {
        document[id].reset();
    }

    // 세부정보 grid 조회
    function SearchDetail(store_cd, store_nm, use_yn) {
        if(store_cd === '') return;
        if (use_yn == undefined) {
            use_yn = 'Y';
            $('#charge_yn').val('Y');
        }
        cur_store_cd = store_cd;
        cur_store_nm = store_nm;

        let data = "store_cd=" + cur_store_cd; 
        data += "&use_yn=" + use_yn;
        gx2.Request("/store/standard/std07/search-store-fee", data, -1, function(d) {
            $("#select_store_nm").text(` - ${store_nm}`);
        })
    }

    // 마진정보 세부변경내역 팝업창 열기
    function showDetailPopup(store_cd, pr_code_cd) {
        const url = "/store/standard/std07/show/" + store_cd + "/" + pr_code_cd;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=900,height=535");
    }
    
    // 매장등급조회
    function showStoreGradePopup(grade_nm = '') {
        const url = "/store/standard/std08/choice?grade_nm=" + grade_nm;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1200,height=710");
    }

    // 사용여부가 선택될때 바로 검색 후 리스트에 출력하는 부분
    function changeUseYn() {
        const charge_yn = document.getElementById("charge_yn").value;
        SearchDetail(cur_store_cd, cur_store_nm, charge_yn);
    }
</script>
@stop
