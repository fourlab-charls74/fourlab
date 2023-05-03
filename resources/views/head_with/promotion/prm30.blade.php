@extends('head_with.layouts.layout')
@section('title','검색어 관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">검색어 관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 검색어 관리</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 검색어 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">검색어</label>
                            <div class="flax_box">
                                <input type="text" name="kwd" id="kwd" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>

                    <!-- 검색횟수 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">검색횟수</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-enter" name="pv_1m_fr" value="">
                                    </div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-enter" name="pv_1m_to" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 검색 상품수 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">검색 상품수</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-enter" name="sch_cnt_fr" value="">
                                    </div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-enter" name="sch_cnt_to" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 출력여부 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">출력여부</label>
                            <div class="flax_box">
                                <select name="disp_yn" class="form-control form-control-sm">
                                    <option value="">선택</option>
                                    <option value="Y">예</option>
                                    <option value="N">아니오</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 인기제외여부 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">인기제외여부</label>
                            <div class="flax_box">
                                <select name="ex_pop_yn" class="form-control form-control-sm">
                                    <option value="">선택</option>
                                    <option value="Y">예</option>
                                    <option value="N">아니오</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 검색어 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">자료/정렬순서</label>
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
                                        <option value="s.rank" selected>순위</option>
                                        <option value="s.point">점수</option>
                                        <option value="s.pv_1m">검색횟수(1달)</option>
                                        <option value="s.tags">태그수</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc">
                                    <input type="radio" name="ord" id="sort_asc" value="asc" checked="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
                    <select id="disp_yn" class="form-control form-control-sm mr-1" style="width:70px;">
                        <option value="">선택</option>
                        <option value="Y">예</option>
                        <option value="N">아니오</option>
                    </select>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 disp-btn">출력여부 변경</a>

                    <select id="ex_pop_yn" class="form-control form-control-sm mr-1" style="width:70px;">
                        <option value="">선택</option>
                        <option value="Y">예</option>
                        <option value="N">아니오</option>
                    </select>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 ex-pop-btn">인기제외여부 변경</a>

                    <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 mpv-save-btn">관리자점수 저장</a>
                    <input type="text" name="pv" id="pv" value="{{$sch->pv}}" class="form-control form-control-sm mr-1 text-center" maxlength="2" style="width:35px">
                    <div class="txt_box">* 검색횟수(1달) +</div>
                    <input type="text" name="tags" id="tags" value="{{$sch->tags}}" class="form-control form-control-sm mr-1 text-center" maxlength="2" style="width:35px">
                    <div class="txt_box">* 태그수 +</div>
                    <input type="text" name="mpv" id="mpv" value="{{$sch->mpv}}" class="form-control form-control-sm mr-1 text-center" maxlength="2" style="width:35px">
                    <div class="txt_box">* 관리자점수 = </div>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm mx-1 point-btn">점수 저장</a>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm rank-btn">순위 변경</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script>
    const editCellStyleTypeNumber = {
        ...StyleEditCell,
        'text-align': 'right'
    };

    const columns = [{
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width: 28,
            pinned: 'left'
        },
        {
            field: "kwd",
            headerName: "검색어",
            type: "SearchType"
        },
        {
            field: "rank",
            headerName: "순위"
        },
        {
            field: "rank_inc",
            headerName: "순위증감"
        },
        {
            headerName: "검색횟수",
            children: [{
                    field: "pv_1d",
                    headerName: "1일",
                    type: 'currencyType'
                },
                {
                    field: "pv_1w",
                    headerName: "7일",
                    type: 'currencyType'
                },
                {
                    field: "pv_1m",
                    headerName: "1달",
                    type: 'currencyType'
                },
                {
                    field: "pv",
                    headerName: "누적",
                    type: 'currencyType'
                }
            ]
        },
        {
            headerName: "검색 상품수(평균)",
            children: [{
                    field: "sch_cnt_1d",
                    headerName: "1일",
                    type: 'currencyType'
                },
                {
                    field: "sch_cnt_1w",
                    headerName: "7일",
                    type: 'currencyType'
                },
                {
                    field: "sch_cnt_1m",
                    headerName: "1달",
                    type: 'currencyType'
                }
            ]
        },
        {
            field: "tags",
            headerName: "태그수",
            type: 'currencyType'
        },

        {
            field: "mpv",
            headerName: "관리자점수",
            cellStyle: editCellStyleTypeNumber,
            editable: true,
            onCellValueChanged: function(p) {
                p.node.setSelected(true);
            },
            cellRenderer: function(p) {
                return numberFormat(p.value);
            }
        },
        {
            field: "point",
            headerName: "점수",
            type: 'currencyType'
        },
        {
            field: "disp_yn",
            headerName: "출력여부",
            width: 70,
            cellStyle:{"text-align" : "center"},
			cellRenderer: function(params) {
				if(params.value == 'Y') return "예"
				else if(params.value == 'N') return "아니오"
				else return params.value
			}
        },
        {
            field: "ex_pop_yn",
            headerName: "인기제외여부",
            width: 90,
            cellStyle:{"text-align" : "center"},
			cellRenderer: function(params) {
				if(params.value == 'Y') return "예"
				else if(params.value == 'N') return "아니오"
				else return params.value
			}
        },
        // {
        //     field: "synonym",
        //     headerName: "동의어",
        //     cellStyle: StyleEditCell,
        //     editable: true,
        //     onCellValueChanged: function(p) {
        //         console.log(p);
        //         if (confirm("해당 내용으로 변경하시겠습니까?") === false) return;

        //         $.ajax({
        //             async: true,
        //             type: 'put',
        //             url: '/head/promotion/prm30/synonym',
        //             data: {
        //                 kwd: p.data.kwd,
        //                 synonym: p.newValue
        //             },
        //             success: function(data) {
        //                 alert("변경되었습니다.");
        //             },
        //             error: function(request, status, error) {
        //                 console.log("error")
        //             }
        //         });
        //     }
        // },
        {
            field: "kwd_rel",
            headerName: "연관검색어"
        },
        {
            field: "rt",
            headerName: "등록일시",
            width: 120
        },
        {
            field: "ut",
            headerName: "변경일시",
            width: 120
        },
        { width: "auto" }
    ];

    const pApp = new App('', {
        gridId: "#div-gd"
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        gx.gridOptions.rowSelection = 'multiple';
        gx.gridOptions.rowMultiSelectWithClick = true;

        Search();
    });

    const Search = () => {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm30/search', data, 1, searchCallback);
    }

    function searchCallback(data) {}

    $('.disp-btn').click((e) => {
        e.preventDefault();
        const yn = $('#disp_yn').val();
        const rows = gx.getSelectedRows();

        if (!yn) {
            alert("출력여부를 선택 해 주세요.");
            return;
        }

        if (rows.length === 0) {
            alert("변경할 데이터를 선택해주세요.");
            return;
        }

        if (confirm('출력여부를 변경하시겠습니까?') === false) {
            return;
        }

        const datas = rows.map((row) => {
            return row.kwd;
        });

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm30/disp',
            data: {
                datas,
                yn
            },
            success: function(data) {
                alert("변경되었습니다.");
                Search();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.ex-pop-btn').click((e) => {
        e.preventDefault();

        const yn = $('#ex_pop_yn').val();
        const rows = gx.getSelectedRows();

        if (!yn) {
            alert("인기제외여부를 선택 해 주세요.");
            return;
        }

        if (rows.length === 0) {
            alert("변경할 데이터를 선택해주세요.");
            return;
        }

        if (confirm('인기제외여부를 변경하시겠습니까?') === false) {
            return;
        }

        const datas = rows.map((row) => {
            return row.kwd;
        });

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm30/ex-pop',
            data: {
                datas,
                yn
            },
            success: function(data) {
                alert("변경되었습니다.");
                Search();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.mpv-save-btn').click((e) => {
        e.preventDefault();

        const rows = gx.getSelectedRows();
        if (rows.length === 0) {
            alert("변경할 데이터를 선택해주세요.");
            return;
        }

        const datas = rows.map((row) => {
            return {
                kwd: row.kwd,
                mpv: row.mpv
            };
        });

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm30/mpv',
            data: {
                datas
            },
            success: function(data) {
                alert("변경되었습니다.");
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.point-btn').click((e) => {
        e.preventDefault();
        const rows = gx.getSelectedRows();

        let kwd = rows.map(r => ({kwd : r.kwd}));

        if (rows.length == 0) {
            return alert('검색어를 선택해주세요');
        }

        if (confirm('점수를 변경하시겠습니까?') === false) return;


        $.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm30/point',
            data: {
                pv: $('#pv').val(),
                tags: $('#tags').val(),
                mpv: $('#mpv').val(),
                kwd : kwd
            },
            success: function(data) {
                alert("변경되었습니다.");
                location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.rank-btn').click((e) => {
        e.preventDefault();

        if (confirm('순위를 변경하시겠습니까?') === false) return;

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm30/rank',
            data: {
                pv: $('#pv').val(),
                tags: $('#tags').val(),
                mpv: $('#mpv').val()
            },
            success: function(data) {
                alert("변경되었습니다.");
                location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });
</script>
@stop