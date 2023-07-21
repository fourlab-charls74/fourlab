@extends('head_with.layouts.layout')
@section('title','업체')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">업체</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 업체</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
		            <a href="#" onclick="formReset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="name">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_id" name="com_id">
                                        <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company search-enter" style="width:100%;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">업체구분</label>
                            <div class="flax_box">
                                <select id="com_type" name="com_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($com_types as $com_type)
                                        <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">본사판매처여부</label>
                            <div class="flax_box">
                                <select class="form-control form-control-sm" name="site_yn">
                                    <option selected value="">전체</option>
                                    <option value="Y">사용</option>
                                    <option value="N">미사용</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">사용여부</label>
                            <div class="flax_box">
                                <select class="form-control form-control-sm" name="use_yn">
                                    <option value="">전체</option>
                                    <option selected value="Y">사용</option>
                                    <option value="N">미사용</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">수수료적용</label>
                            <div class="flax_box">
                                <select class="form-control form-control-sm" name="margin_type">
                                    <option selected value="">전체</option>
                                    <option value="FEE">수수료 지정</option>
                                    <option value="WONGA">공급가 지정</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">API연동</label>
                            <div class="flax_box">
                                <select class="form-control form-control-sm" name="api_yn">
                                    <option selected value="">전체</option>
                                    <option value="Y">사용</option>
                                    <option value="N">미사용</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">배송비정책</label>
                            <div class="flax_box">
                                <select class="form-control form-control-sm" name="dlv_policy">
                                    <option selected value="">전체</option>
                                    <option value="S">쇼핑몰 설정</option>
                                    <option value="C">업체 정책</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">담당MD</label>
                            <div class="flax_box">
                                <input id="md_nm" class="form-control form-control-sm search-enter" name="md_nm" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">정산담당자</label>
                            <div class="flax_box">
                                <input id="settle_nm" class="form-control form-control-sm search-enter" name="settle_nm" />
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <a href="#" onclick="formReset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
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
	let columns = [
        {
			field:"seq",
            headerName: '#',
            width:35,
            pinned:'left',
            maxWidth: 100,
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
	        cellClass: 'hd-grid-code'
        },
        {field:"no",headerName:"no",hide:true},
        {field: "com_type", headerName: "업체구분", width: 70, cellClass: 'hd-grid-code', pinned: 'left', cellStyle: StyleGoodsTypeNM, editable: true},
        {field: "com_id", headerName: "업체아이디", pinned: 'left', width: 120,
            cellRenderer: function(params) {
                if (params.value !== undefined && params.data.no != "") {
                    return '<a href="#" onclick="ComDetail(\''+ params.value +'\');" >'+ params.value+'</a>';
                }
            }
        },
        {field:"com_nm",headerName:"업체명", pinned:'left', width: 150},
        {field: "baesong", headerName: "배송방식", width: 140, cellClass: 'hd-grid-code'},
        {field: "dlv_policy", headerName: "배송비정책", width: 70, cellClass: 'hd-grid-code'},
        {field: "baesong_price", headerName: "배송비", width: 210},
        {field: "md_nm", headerName: "담당MD", width: 65, cellClass: 'hd-grid-code'},
        {field: "settle_nm", headerName: "정산담당자", width: 65, cellClass: 'hd-grid-code'},
        {field: "pay_fee", headerName: "판매수수료율", width: 75, type: 'percentType'},
        {field: "margin_type", headerName: "수수료적용방식", width: 100, cellClass: 'hd-grid-code'},
        {field: "site_yn", headerName: "본사판매처", width:  70,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value == 'Y') return "사용"
				else if(params.value == 'N') return "미사용"
                else return params.value
			}
        },
        {field: "api_yn", headerName: "API연동", width: 70,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value == 'Y') return "사용"
				else if(params.value == 'N') return "미사용"
                else return params.value
			}
        },
        {field: "use_yn", headerName: "사용여부", width: 70,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value == 'Y') return "사용"
				else if(params.value == 'N') return "미사용"
                else return params.value
			}
        },
        {field: "biz_type", headerName: "CS사업자구분", cellClass: 'hd-grid-code'},
        {field: "cs_nm", headerName: "CS담당자", cellClass: 'hd-grid-code'},
        {field: "cs_email", headerName: "CS담당자 이메일"},
        {field: "cs_phone", headerName: "CS담당자 연락처", cellClass: 'hd-grid-code'},
        {field: "cs_hp", headerName: "CS담당자 휴대전화", cellClass: 'hd-grid-code'},
        {field: "staff_nm1", headerName: "업체담당자", cellClass: 'hd-grid-code'},
        {field: "staff_email1", headerName: "업체담당자 이메일"},
        {field: "staff_phone1", headerName: "업체담당자 연락처", cellClass: 'hd-grid-code'},
        {field: "staff_hp1", headerName: "업체담당자 휴대전화", cellClass: 'hd-grid-code'},
        {field: "staff_nm2", headerName: "정산담당자", cellClass: 'hd-grid-code'},
        {field: "staff_email2", headerName: "정산담당자 이메일"},
        {field: "staff_phone2", headerName: "정산담당자 연락처", cellClass: 'hd-grid-code'},
        {field: "staff_hp2", headerName: "정산담당자 휴대전화", cellClass: 'hd-grid-code'},
        {width: 0}
	];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        let gridDiv = document.querySelector(pApp.options.gridId);
        const gridOptions = {
            rowData: null,
            onColumnResized: (params) => {
                // console.log(params);
            }
        };
		
		let url_path_array = String(window.location.href).split('/');
		const pid = filter_pid(String(url_path_array[url_path_array.length - 1]).toLocaleUpperCase());

		get_indiv_columns(pid, columns, function(data) {
			gx = new HDGrid(gridDiv, data);

			setMyGridHeader.Init(gx,
				indiv_grid_save.bind(this, pid, gx),
				indiv_grid_init.bind(this, pid)
			);

			Search(1);
		});
    });

    pApp.BindSearchEnter();

    var _isloading = false;
    function onscroll(params){

        if(_isloading === false && params.top > gridDiv.scrollHeight){

            var rowtotal = gridOptions.api.getDisplayedRowCount();
            // console.log('getLastDisplayedRow : ' + gridOptions.api.getLastDisplayedRow());
            // console.log('rowTotalHeight : ' + rowtotal * 25);
            // console.log('params.top : ' + params.top);

            if(gridOptions.api.getLastDisplayedRow() > 0 && gridOptions.api.getLastDisplayedRow() ==  rowtotal -1) {
                // console.log(params);
                Search(0);
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

    var _page = 1;
    var _total = 0;
    var _grid_loading = false;
    var _code_items = "";
    var columns_arr = {};
    var option_key = {};

    function Search(page) {
        let data = $('form[name="search"]').serialize();
        // console.log(data);
        gx.Request('/head/standard/std02/search', data, -1);
    }

	function Cmder(cmd){
		if(cmd == "add"){
			var url = "/head/standard/std02/show/";
			//openWindow(url,'','resizable=yes,scrollbars=yes', '900', '600');
			const Com=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=1100,height=760");

		}
	}

    function ComDetail(com_id){
        var url = "/head/standard/std02/show/" + com_id;
        //openWindow(url,'','resizable=yes,scrollbars=yes', '900', '600');
        const Com=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=1100,height=760");
    }

	function formReset() {
        document.search.reset();
        location.reload();
    }
</script>

@stop
