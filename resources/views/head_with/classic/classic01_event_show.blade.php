@extends('head_with.layouts.layout-nav')
@section('title','클래식 이벤트 검색')
@section('content')

<div class="show_layout py-3">
	<form method="post" name="search">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#">트레킹 이벤트</a>
            </div>
            <div class="card-body mt-1">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="30%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>트레킹 제목</th>
                                            <td>
                                                <div class="input_box">
                                                    <input type="text" name="s_title" class="form-control form-control-sm search-all">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4 last-card pt-2 pt-sm-0">
			<div class="card-body">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">7</span>건</h6>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="height: calc(100vh - 140px); width: 100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="#" onclick="Search();" class="btn btn-sm btn-primary submit-btn">검색</a>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-secondary">닫기</a>
        </div>
	</form>
</div>
<script language="javascript">
	var columns = [
		{headerName: "#", field: "num",	filter:true,width:50,
            valueGetter: function(params) {
                return params.node.rowIndex+1;
                }, 
            pinned:'left'},
		{headerName:"트레킹", field:"title", width:250},
		{headerName:"참여",	field:"join_cnt", width:60,
            type:'numberType'},
		{headerName:"시작일", field:"start_date", width:80},
		{headerName:"마감일", field:"end_date", width:80},
        {headerName: "보기", field: "view",	width:60,
            cellRenderer: function(params) {
				return `<a href="javascript:ViewEvent('` + params.data.idx + `')">` + "보기" + `</a>`;
			}
		},
        {headerName: "선택", field: "slct",	width:60,
            cellRenderer: function(params) {
				return `<a href="javascript:SelectEvent('` + params.data.idx + `','` + params.data.title + `')">` + "선택" + `</a>`;
			}
		},
        {headerName: "이벤트 코드", field: "idx", hide:true },
	];

    //보기
	function ViewEvent(idx) {
        // const url='주소' + idx;
        // window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

    //선택
	function SelectEvent(idx,nm) {
		window.opener.document.f1.evt_idx.value = idx;
		window.opener.document.getElementById('evt_nm').innerHTML = nm;

		self.close();
	}
</script>
<script type="text/javascript" charset="utf-8">
    // var App = function(id,options){
    //     this.options = options;
    // };
    const pApp = new App('', { gridId: "#div-gd" });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/classic/classic01/event-search', data);
    }
</script>
@stop