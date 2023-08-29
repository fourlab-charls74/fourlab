@extends('store_with.layouts.layout')
@section('title','사이즈관리')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">사이즈관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보관리</span>
        <span>/ 사이즈관리</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i>등록</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                   <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">사이즈구분</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='size_kind_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">사이즈구분명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='size_kind_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_yn" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="use_yn">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="y">
                                    <label class="custom-control-label" for="use_y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="n">
                                    <label class="custom-control-label" for="use_n">N</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i>등록</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
					<button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="changeSeq()">순서변경</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

</div>
<script>
    const columns = [{
            field: "size_kind_cd",
            headerName: "사이즈구분",
            width: 180,
			rowDrag: true
        },
        {
            field: "size_kind_nm",
            headerName: "사이즈구분명",
            width: 150,
            cellRenderer: function(params) {
                return '<a href="#" data-code="' + params.data.size_kind_cd + '" onClick="openCodePopup(this)">' + params.value + '</a>'
            }
        },
        {
            field: "use_yn",
            headerName: "사용여부",
            width: 100,
            cellStyle:{'text-align':'center'}
        },
        {
            field: "admin_nm",
            headerName: "작성자",
            width: 100,
            cellStyle:{'text-align':'center'}
        },
        {
            field: "rt",
            headerName: "작성일시",
            width: 130,
            cellStyle:{'text-align':'center'}
        },
		{
			field: "ut",
			headerName: "수정일시",
			width: 130,
			cellStyle:{'text-align':'center'}
		},
		{
			field: "seq",
			headerName: "순서",
			hide: "true"
		},
        {
            field: "",
            headerName: "",
            width: "auto"
        },
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', { gridId: "#div-gd", height: 265 });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/standard/std10/search', data);
    }

    function openCodePopup(a) {
        const url = '/store/standard/std10/' + $(a).attr('data-code');
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=800");
    }

    function openAddPopup() {
        const url = '/store/standard/std10/create';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=380");
    }

	function changeSeq(){
		let size_kind_cds = [];
		gx.gridOptions.api.forEachNode(function(node) {
			size_kind_cds.push(node.data.size_kind_cd);
		});

		if(confirm('사이즈 리스트 순서를 변경 하시겠습니까?')){
			$.ajax({
				method: 'post',
				url: '/store/standard/std10/change-seq',
				data: {'size_kind_cds': size_kind_cds},
				dataType: 'json',
				success: function (res) {
					if(res.code == '200'){
						alert(res.msg);
						Search();
					} else {
						alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
					}
				},
				error: function(e) {
					console.log(e.responseText)
				}
			});
		}
		return true;
	}

</script>
@stop
