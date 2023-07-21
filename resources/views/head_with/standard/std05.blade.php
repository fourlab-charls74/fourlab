@extends('head_with.layouts.layout')
@section('title','FAQ')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">FAQ</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ FAQ</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 등록</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">분류</label>
                            <div class="flax_box">
                                <select name="type" id="type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($faq_types as $faq_type)
                                    <option value='{{ $faq_type->id }}'>{{ $faq_type->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">공개여부</label>
                            <div class="flax_box">
                                <select name="show" id="show" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">예</option>
                                    <option value="N">아니요</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">베스트여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="best_yn" id="best_yn" class="custom-control-input" value="Y">
                                    <label class="custom-control-label" for="best_yn">예</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">제목</label>
                            <div class="flax_box">
                                <input type="text" name="que" id="que" class="form-control form-control-sm search-all search-enter">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">내용</label>
                            <div class="flax_box">
                                <input type="text" name="ans" id="ans" class="form-control form-control-sm search-all search-enter">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 등록</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
	                <button type="button" class="setting-grid-col ml-2"><i class="fas fa-cog text-primary"></i></button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script language="javascript">
    var columns = [
        // this row shows the row index, doesn't use any data from the row

        {
			field: "seq",
            headerName: '#',
            width: 35,
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
            cellStyle: {"background":"#F5F7F7"}
        },
        {
            field: "type",
            headerName: "분류",
            width: 140,
            cellStyle: StyleGoodsTypeNM,
            editable: true
        },
        {
            field: "no",
            headerName: "no",
            hide: true
        },
        {
            field: "question",
            headerName: "제목",
            width: 400,
            cellRenderer: function (params) {
                if (params.value !== undefined && params.data.no != "") {
                    return '<a href="javascript::" onclick="FaqDetail(' + params.data.no + ');" >' + params.value + '</a>';
                }
            }
        },
        {
            field: "admin_nm",
            headerName: "작성자",
            width: 75,
        },
        {
            field: "regi_date",
            headerName: "등록일",
            width: 75,
        },
        {
            field: "show_yn",
            headerName: "공개여부",
            width:58,
            cellStyle: {'text-align':'center'},
            cellRenderer: function (params) {
				if(params.value === 'Y') return "공개"
				else if(params.value === 'N') return "비공개"
                else return params.value
			}
        },
        {
            field: "best_yn",
            headerName: "베스트여부",
            width:70,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value === 'Y') return "베스트"
                else return params.value
			}
        },
        { field: "", headerName: "", width: 0 }
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
	
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
		
		let url_path_array = String(window.location.href).split('/');
		const pid = filter_pid(String(url_path_array[url_path_array.length - 1]).toLocaleUpperCase());

		//gx = new HDGrid(gridDiv, columns);

		get_indiv_columns(pid, columns, function(data) {
			gx = new HDGrid(gridDiv, data);

			setMyGridHeader.Init(gx,
				indiv_grid_save.bind(this, pid, gx),
				indiv_grid_init.bind(this, pid)
			);

			Search();
		});
		
    });
	
    function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Aggregation({
			"sum": "top",
		});
		gx.Request('/partner/cs/cs72/search', data);
	}

	function test() {
		
		console.log(newTest);
	}
	
</script>
<script type="text/javascript" charset="utf-8">
    var _isloading = false;

    function onscroll(params) {


        if (_isloading === false && params.top > gridDiv.scrollHeight) {

            var rowtotal = gridOptions.api.getDisplayedRowCount();
            // console.log('getLastDisplayedRow : ' + gridOptions.api.getLastDisplayedRow());
            // console.log('rowTotalHeight : ' + rowtotal * 25);
            // console.log('params.top : ' + params.top);

            if (gridOptions.api.getLastDisplayedRow() > 0 && gridOptions.api.getLastDisplayedRow() == rowtotal - 1) {
                // console.log(params);
                Search();
            }
            // var rowtotal = gridOptions.api.getDisplayedRowCount();
            // var rowHeight = 25;
            // var rowTotalHeight = rowtotal * gridOptions.rowHeight;
            // if(rowtotal > 0 && params.top > rowTotalHeight && (rowtotal - 1) == gridOptions.api.getLastDisplayedRow()){
            //     console.log('params.top :' + params.top);
            //     console.log('rowTotalHeight :' + rowTotalHeight);
            //     console.log('top : ' + params.top);
            //     console.log('eGridDiv : ' + eGridDiv.scrollHeight);
            //     console.log(gridOptions.api.getDisplayedRowCount());
            //     console.log(gridOptions.api.getFirstDisplayedRow());
            //     console.log(gridOptions.api.getLastDisplayedRow());
            //     _isloading = true;
            //     Search(0);
            // }
        }
    }

    function FaqDetail(idx) {
        //console.log("idx : "+idx);

        var url = "/head/standard/std05/show/" + idx;
        //openWindow(url,'','resizable=yes,scrollbars=yes', '900', '600');
        const FAQ = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=900,height=620");
    }

    function openAddPopup() {
        var url = "/head/standard/std05/show/";
        const FAQ = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=900,height=600");
    }

    var _page = 1;
    var _total = 0;
    var _grid_loading = false;
    var _code_items = "";
    var columns_arr = {};
    var option_key = {};

    function Search() {
        let data = $('form[name="search"]').serialize();
        //console.log(data);
        gx.Request('/head/standard/std05/search', data, -1);
    }
</script>
@stop
