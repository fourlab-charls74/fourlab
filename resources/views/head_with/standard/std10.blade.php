@extends('head_with.layouts.layout')
@section('title','광고')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">광고</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 광고</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
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
                            <label for="user_yn">유형</label>
                            <div class="flax_box">
                                <select name='user_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($user_yn as $user)
                                    <option value="{{$user->code_id}}">{{$user->code_val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">광고구분</label>
                            <div class="flax_box">
                                <select name='type' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($types as $type)
                                    <option value="{{$type->code_id}}">{{$type->code_val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">광고명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter search-enter" name='name' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="state">상태</label>
                            <div class="flax_box">
                                <select name="state" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($states as $state)
                                    <option value="{{$state->code_id}}" @if($state->code_id == '1') selected @endif
                                        >
                                        {{$state->code_val}}
                                    </option>
                                    @endforeach
                                </select>
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
    </form>
</div>

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

<script>

    const USER = {
        Y: "사용자",
        N: "시스템"
    };

    const TYPE = {
        KEYWORD : '키워드',
        ETC : '기타',
        EMAIL : '이메일',
        BANNER : '배너',
        AFFILIATE : '제휴'
    }

    const columnDefs = [{ field: "user_yn", headerName: "유형",
            cellRenderer: (params) => {
                if (params.value == "Y") {
                    return USER.Y;
                } else if (params.value == "N") return USER.N;
                return params.value;
            }
        },
        {
            field: "type",
            headerName: "광고구분",
            cellRenderer: (params) => TYPE[params.value] || params.value,
        },
        {
            field: "ad",
            headerName: "코드",
            width: 120,
            cellRenderer: function(params) {
                return '<a href="#" data-code="' + params.value + '" onClick="openCodePopup(this)">' + params.value + '</a>'
            }
        },
        {
            field: "name",
            headerName: "광고명",
            width: 150
        },
        {
            field: "state",
            headerName: "상태",
            width:46,
            cellStyle: {'text-align':'center'},
            cellRenderer: function(params) {
				if(params.value === 'Y') return "사용"
				else if(params.value === 'N') return "미사용"
                else return params.value
			}
        },
        {
            field: "rt",
            headerName: "등록일시",
	        type: "DateTimeType"
        },
        {
            field: "ut",
            headerName: "수정일시",
	        type: "DateTimeType"
        },
        { field: "", headerName: "", width: 0 }
    ];

    const pApp = new App('', {
        gridId: "#div-gd"
    });
    const gridDiv = document.querySelector(pApp.options.gridId);

	let url_path_array = String(window.location.href).split('/');
	const pid = filter_pid(String(url_path_array[url_path_array.length - 1]).toLocaleUpperCase());

	get_indiv_columns(pid, columns, function(data) {
		gx = new HDGrid(gridDiv, data);

		setMyGridHeader.Init(gx,
			indiv_grid_save.bind(this, pid, gx),
			indiv_grid_init.bind(this, pid)
		);

		Search();
	});

    pApp.ResizeGrid(275);
    pApp.BindSearchEnter();

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/standard/std10/search', data);
    }

    function openCodePopup(a) {
        const url = '/head/standard/std10/show/' + $(a).attr('data-code');
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=600,height=440");
    }

    function openAddPopup() {
        const url = '/head/standard/std10/show/';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=600,height=440");
    }

    Search();
</script>
@stop
