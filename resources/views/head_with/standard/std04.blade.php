@extends('head_with.layouts.layout')
@section('title','카테고리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">카테고리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 카테고리</span>
    </div>
</div>

<form method="get" name="search">
    <input type=hidden name="data" />
    <input type="hidden" name="ac_id" />
    <input type=hidden name="cmd" value="" />
    <input type="hidden" name="cat_type" value="DISPLAY" />
    <input type="hidden" name="p_d_cat_cd" value="" />
    <input type="hidden" name="isld" value="isld" />
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">구분</label>
                            <div class="input_box">
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="s_cat_type" id="cat_type_display" class="custom-control-input" value="DISPLAY" checked>
                                        <label class="custom-control-label" for="cat_type_display">전시카테고리</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="s_cat_type" id="cat_type_item" class="custom-control-input" value="ITEM">
                                        <label class="custom-control-label" for="cat_type_item">용도카테고리</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">판매처</label>
                            <div class="flax_box">
                                <select name="site" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($sites as $site_arr)
                                    <option value='{{ $site_arr->com_id }}'>{{ $site_arr->com_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">이름</label>
                            <div class="flax_box">
                                <input type="text" name="cat_name" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">정렬</label>
                            <div class="input_box">
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sort_opt" id="sch_sort_opt_" class="custom-control-input" value="" checked>
                                        <label class="custom-control-label" for="sch_sort_opt_">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sort_opt" id="sch_sort_opt_m" class="custom-control-input" value="M">
                                        <label class="custom-control-label" for="sch_sort_opt_m">수동(M)</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sort_opt" id="sch_sort_opt_a" class="custom-control-input" value="A">
                                        <label class="custom-control-label" for="sch_sort_opt_a">자동(A)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 권한 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">권한</label>
                            <div class="input_box">
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="cat_auth" id="sch_cat_auth_" class="custom-control-input" value="" checked>
                                        <label class="custom-control-label" for="sch_cat_auth_">모두</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="cat_auth" id="sch_cat_auth_a" class="custom-control-input" value="A">
                                        <label class="custom-control-label" for="sch_cat_auth_a">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="cat_auth" id="sch_cat_auth_g" class="custom-control-input" value="G">
                                        <label class="custom-control-label" for="sch_cat_auth_g">그룹</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 사용여부 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">사용여부</label>
                            <div class="input_box">
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="use_yn" id="sch_use_yn_" class="custom-control-input" value="">
                                        <label class="custom-control-label" for="sch_use_yn_">전체</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="use_yn" id="sch_use_yn_y" class="custom-control-input" value="Y" checked>
                                        <label class="custom-control-label" for="sch_use_yn_y">Y</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="use_yn" id="sch_use_yn_n" class="custom-control-input" value="N">
                                        <label class="custom-control-label" for="sch_use_yn_n">N</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
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
            field: "d_cat_cd",
            headerName: "코드",
            cellStyle: StyleGoodsTypeNM,
			width:200,
            cellRenderer: function(params) {
				blnk = "";
				for( i = 0; i < params.data.p_d_cat_cd.length; i++ )
				{
					blnk += "&nbsp;&nbsp;";
				}

				return '<a href="#" onClick="insertCategoryCode(\'' + params.value + '\');">' + blnk + params.value + '</a>';
            }
            //editable: true,
            //rowGroup: true,
            //hide: true
        },
        {
            field: "no",
            headerName: "no",
            hide: true
        },
        {
            field: "d_cat_nm",
            headerName: "이름",
            minWidth: 190,
            cellRenderer: function(params) {
                if (params.value !== undefined && params.data.no != "") {
                    return '<a href="javascript:;" data-code="' + params.data.d_cat_cd + '" onclick="cateDetail(this);" >' + params.value + '</a>';
                }
            }
        },
        {
            headerName: "상품수",
            children: [{
                    headerName: "판매중",
                    field: "40_cnt"
                },
                {
                    headerName: "품절",
                    field: "30_cnt"
                },
                {
                    headerName: "전체",
                    field: "cnt"
                }
            ]
        },
        {
            field: "sort_opt",
            headerName: "정렬",
            width: 75,
        },
        {
            field: "auth",
            headerName: "권한",
        },
        {
            headerName: "조회수",
            children: [{
                    headerName: "전일",
                    field: "dpv"
                },
                {
                    headerName: "최근1주",
                    field: "wpv"
                },
                {
                    headerName: "최근1개월",
                    field: "mpv"
                }
            ]
        },
        {
            field: "use_yn",
            headerName: "사용여부",
        },
        {
            field: "com_nm",
            headerName: "판매처",
        },
        {
            field: "regi_date",
            headerName: "등록일시",
            width: 140
        },
        {
            field: "upd_date",
            headerName: "수정일시",
            width: 140
        },
        {
            field: "cat_type",
            headerName: "cat_type",
            hide: true
        },
        {
            field: "full_nm",
            headerName: "full_nm",
            hide: true
        },
		{ field:"p_d_cat_cd", headerName:"p_d_cat_cd", hide: true },
        { field: "", headerName: "", width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {
		gridId: "#div-gd"
	});
	const gridDiv = document.querySelector(pApp.options.gridId);
	/*
	const gx = new HDGrid(gridDiv, columns, {
		autoGroupColumnDef: {
			headerName: '코드',
			field: 'p_d_cat_cd',
			maxWidth: 120,
			cellRenderer: function(p) {
				if (p.value != "") {
					return `<div class="ml-4">${p.data.d_cat_cd}</div>`;
				}
				return p.data.d_cat_cd;
			}
		}
	});
	*/
	const gx = new HDGrid(gridDiv, columns);
	console.log(gx.gridOptions);
	pApp.ResizeGrid(275);

    var _isloading = false;

    function onscroll(params) {


        if (_isloading === false && params.top > gridDiv.scrollHeight) {}
    }
    var _page = 1;
    var _total = 0;
    var _grid_loading = false;
    var _code_items = "";
    var columns_arr = {};
    var option_key = {};

    function Search(page) {
        let data = $('form[name="search"]').serialize();
        //console.log(data);
        gx.Request('/head/standard/std04/search', data);

    }

	function insertCategoryCode(cat_code){
		if( cat_code != "" ){
			$("[name=p_d_cat_cd]").val(cat_code);
		}
	}

	function openAddPopup() {
		var f1	= document.search;
		var selectedRowData	= gx.gridOptions.api.getSelectedRows();
		var cat_type	= $("[name=cat_type]").val();		//카테고리 타입(DISPLAY:전시카테고리, ITEM:용도카테고리)
		var p_d_cat_cd	= $("[name=p_d_cat_cd]").val();		//부모카테고리 코드

		if( cat_type == "" )	cat_type = "DISPLAY";

		if( selectedRowData.length > 0 ) {
			selectedRowData.forEach(function(selectedRowData, index) {
				if( selectedRowData.d_cat_cd != "" ){
					//data.push(selectedRowData.group_no +"_"+ selectedRowData.goods_sub);
					console.log("selectedRowData.d_cat_cd : " + selectedRowData.d_cat_cd);
					$("[name=p_d_cat_cd]").val(selectedRowData.d_cat_cd);
					p_d_cat_cd = selectedRowData.d_cat_cd;
				}
			});
		}

		if( $("[name=s_cat_type]").eq(0).is(":checked") == true ) {
			$("[name=cat_type]").val("DISPLAY");
		}else{
			$("[name=cat_type]").val("ITEM");
		}

		cat_type	= $("[name=cat_type]").val();

		var site	= '';
		var url		= "/head/standard/std04/detail/?cat_type=" + cat_type + "&p_d_cat_cd=" + p_d_cat_cd + "&site=" + site;
		const CATE	= window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=900,height=600");
	}

    function cateDetail(a) {
        var cat_cd = $(a).attr('data-code');
        var length = cat_cd.length - 3;
        var p_d_cat_cd = cat_cd.substring(0, length);

        if (cat_cd == 000) {
            alert("미설정 카테고리는 변경할 수 없습니다.");
            return false;
        }

        if( $("[name=s_cat_type]").eq(0).is(":checked") == true ) {
			$("[name=cat_type]").val("DISPLAY");
		}else{
			$("[name=cat_type]").val("ITEM");
		}

        cat_type = $("[name=cat_type]").val();

        var url = "/head/standard/std04/detail/?cat_type=" + cat_type + "&p_d_cat_cd=" + p_d_cat_cd + "&d_cat_cd=" + cat_cd;
        const CATE = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=900,height=600");
    }

    function click(type) {
        var first_row_data = gx.gridOptions.api.getRowNode(0);
        var d_cat_cd = first_row_data.data.d_cat_cd;
        var cat_type = first_row_data.data.cat_type;

        if (type == 1) {
            $("[name=cat_type]").val(cat_type);
            $("[name=p_d_cat_cd]").val(d_cat_cd);
        } else {
            if (d_cat_cd == 000) {
                alert("미설정 카테고리는 변경할 수 없습니다.");
                return false;
            }
            var length = d_cat_cd.length - 3;
            var p_d_cat_cd = d_cat_cd.substring(0, length);
            $("[name=cat_type]]").val(cat_type);
            $("[name=p_d_cat_cd]").val(d_cat_cd);
            var url = "/head/standard/std04/detail/?CAT_TYPE=" + cat_type + "&p_d_cat_cd=" + p_d_cat_cd + "&D_CAT_CD=" + d_cat_cd;
            //const CATE=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=900,height=600");
        }

    }
    Search(1);
</script>
@stop