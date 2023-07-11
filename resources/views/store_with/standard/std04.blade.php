@extends('store_with.layouts.layout')
@section('title','동종업계관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">동종업계관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보관리</span>
        <span>/ 동종업계관리</span>
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
							<label for="store_nm">매장명</label>
                            <div class="form-inline">
                                <input type="text" id="store_nm" name="store_nm" class="form-control form-control-sm w-100 search-enter" />
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_A" name="use_yn" value="" checked />
                                    <label class="custom-control-label" for="use_yn_A">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_Y" name="use_yn" value="Y" />
                                    <label class="custom-control-label" for="use_yn_Y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="use_yn_N" name="use_yn" value="N" />
                                    <label class="custom-control-label" for="use_yn_N">N</label>
                                </div>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td" hidden>
                        <div class="form-group">
							<label for="store_cd">매장코드</label>
                            <div class="form-inline">
                                <input type="text" id="store_cd" name="store_cd" class="form-control form-control-sm w-100 search-enter" />
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
    <div class="col-lg-4 pr-1">
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
    <div class="col-lg-8">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-0"><span id="select_store_nm"></span>동종업계 세부정보</h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateCompetitors()"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="downlaodExcel()"><i class="fas fa-download fa-sm text-white-50 mr-1"></i> 엑셀다운로드</button>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="resetCompetitors()">전체 초기화</button>
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-competitor" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "store_cd", headerName: "매장코드", width: 60, cellStyle: {"text-align": "center"},
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='SearchDetail("${params.value}", "${params.data.store_nm}")'>${params.value}</a>`;
            }
        },
        {field: "store_channel", headerName: "판매채널", width: 80, cellStyle: {"text-align": "center"}},
        {field: "store_channel_kind", headerName: "매장구분", width: 80, cellStyle: {"text-align": "center"}},
        {field: "store_nm", headerName: "매장명", width: 140, 
            cellRenderer: function(params) {
                return `<a href='javascript:void(0)' onclick='SearchDetail("${params.data.store_cd}", "${params.value}")'>${params.value}</a>`;
            }
        },
        {field: "competitor_cnt", headerName: "동종업계수", cellStyle: {"text-align": "center"}, width: 80},
        {width: "auto"},
    ];

    let competitor_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"}},
        {field: "use_yn", headerName: "사용", cellStyle: {"text-align": "center"}, pinned: "left",
            cellRenderer: function(params) {
                return `<input type="checkbox" onclick="changeUseYnVal(event, '${params.rowIndex}')" style="width:15px;height:15px;" ${params.value === 'Y' ? "checked" : ""} />`;
        }},
        {field: "competitor_cd", headerName: "동종업계코드", width: 100, cellStyle: {"text-align": "center"}},
        {field: "competitor_nm", headerName: "동종업계명", width: 200},

        //본사에서 삭제요청 일단 히든처리
        {field: "item", headerName: "아이템", width: 200, cellStyle: {"background-color": "#ffff99"}, editable: true, hide:true},
        {field: "manager", headerName: "매니저", cellStyle: {"text-align": "center", "background-color": "#ffff99"}, editable: true, hide:true},
        {field: "sdate", headerName: "등록일", width: 80, cellStyle: {"text-align": "center", "background-color": "#ffff99"}, editable: true, hide:true},
        {field: "edate", headerName: "폐점일", width: 80, cellStyle: {"text-align": "center", "background-color": "#ffff99"}, editable: true, hide:true},
        {width: "auto"},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx, gx2;

    const pApp = new App('', { gridId: "#div-gd" });
    const pApp2 = new App('', { gridId: "#div-gd-competitor" });
    let cur_store_cd = "";
    let cur_store_nm = "";

    $(document).ready(function() {
        // 매장목록
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        // 동종업계 세부정보
        pApp2.ResizeGrid(275);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, competitor_columns, {
            onCellValueChanged: (e) => {
                e.node.data.use_yn = 'Y';
                gx2.gridOptions.api.updateRowData({update: [e.node.data]});
            }
        });

        // 최초검색
        Search();

        // 검색조건 숨김 시 우측 grid 높이 설정
        $(".search_mode_wrap .dropdown-menu a").on("click", function(e) {
            if(pApp2.options.grid_resize == true){
                pApp2.ResizeGrid(275);
            }
        });
        // 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
        load_store_channel();
    });

    // 매장목록 조회
    // * competitor_yn가 "Y"가 아닌 값은 조회되지않습니다.
    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/standard/std04/search", data, -1, function(d) {
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
    function SearchDetail(store_cd, store_nm) {
        if(store_cd === '') return;
        
        cur_store_cd = store_cd;
        cur_store_nm = store_nm;
		
		gx2.gridOptions.columnApi.applyColumnState({ defaultState: { sort: null } });	// 컬럼 정렬 리셋
		
        gx2.Request("/store/standard/std04/search-competitor/" + store_cd, "", -1, function(d) {
            $("#select_store_nm").text(`${store_nm} - `);
        })
    }

    // 동종업계별 사용여부 변경
    function changeUseYnVal(e, rowIndex) {
        const node = gx2.getRowNode(rowIndex);
        node.data.use_yn = e.target.checked ? 'Y' : 'N';
    }

    // 동종업계 세부정보 입력정보 저장
    function updateCompetitors() {
        if(!confirm(cur_store_nm + "의 동종업계 세부정보를 저장하시겠습니까?")) return;

        axios({
            url: `/store/standard/std04/update-competitor`,
            method: 'put',
            data: {
                store_cd: cur_store_cd,
                data: gx2.getRows()
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                Search();
            } else {
                console.log(res.data.msg);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 동종업계 세부정보 엑셀다운로드
    function downlaodExcel() {
        gx2.gridOptions.api.exportDataAsExcel({
            skipHeader: false,
            skipPinnedTop: false,
        });
    }

    // 동종업계 세부정보 전체 초기화
    function resetCompetitors() {
        if(!confirm(cur_store_nm + "의 동종업계 세부정보를 초기화하시겠습니까?")) return;

        const rows = gx2.getRows();
        gx2.gridOptions.api.setRowData(
            rows.map(
                row => ({
                    competitor_cd: row.competitor_cd, 
                    competitor_nm: row.competitor_nm
                })
            )
        );
    }
</script>
@stop
