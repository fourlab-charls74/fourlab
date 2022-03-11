@extends('head_with.layouts.layout-nav')
@section('title','재입고알림')
@section('content')
<style> body { overflow: hidden; } .card-title { margin: 0 0 1.25rem 0 } </style>
<div class="container-fluid show_layout pt-3">
	<div class="page_tit d-flex align-items-center justify-content-between mb-0">
		<div>
			<h3 class="d-inline-flex">
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 재고입고알림</span>
					<span>/ 재입고알림</span>
				</div>
			</h3>
		</div>
		<div>
			<a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
		</div>
	</div>
</div>
<div class="p-3">
	<div id="search-area" class="search_cum_form">
		<form method="get" name="search">
			<input type="hidden" name="goods_no" id="goods_no" value="{{ $goods_no }}">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>재입고알림 - {{ $goods_nm }} </h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
                        <div class="col-lg-6 inner-td">
							<div class="form-group">
								<label for="state_check">알림여부</label>
								<div class="flex_box">
                                    <select id="state_check" name="state" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        <option value="Y">Y</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
							</div>   
						</div>
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="user_id">아이디</label>
                                <div class="flex_box">
									<input id="user_id" type="text" class="form-control form-control-sm search-all search-enter" name="user_id" value="">
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card shadow">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box">
						<div class="fr_inner_box">
							<div class="box">
								<a href="#" onclick="SendSMS();" class="btn-sm btn btn-primary">SMS 발송</a>
							</div>
						</div>
                    </div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="width:100%; height:calc(100vh - 1.5rem);" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</div>

<script language="javascript">

	const pageNo = -1;
	var checkVal = null;
	var newAc = new Array();
	var oriAc = new Array();
	var oriRowNum = 0;
	var columns = [
			// this row shows the row index, doesn't use any data from the row
			{
				headerName: '#',
				width:40,
				maxWidth: 100,
				// it is important to have node.id here, so that when the id changes (which happens
				// when the row is loaded) then the cell is refreshed.
				valueGetter: 'node.id',
				cellRenderer: 'loadingRenderer',
			},
            {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
            {field:"rt" , headerName:"요청일시", width:130},
            {field:"user_id" , headerName:"아이디",type:"HeadUserType", width:100},
            {field:"name" , headerName:"이름", width:100},
            {field:"mobile" , headerName:"휴대전화", width:100},
            {field:"email" , headerName:"이메일", width:150},
            {field:"state" , headerName:"알림여부",cellClass: 'hd-grid-code'},
			{field:"no", headName: "no", hide:true}
	];

	var pApp = new App('', { gridId: "#div-gd" });
	var gridDiv = document.querySelector(pApp.options.gridId);
    gx = new HDGrid(gridDiv, columns);
    
	gx.gridOptions.api.suppressRowClickSelection = true;
	
	document.addEventListener('DOMContentLoaded', function () {
		var select_box = document.querySelector('#state_check');
		var state_check = "<?=$state?>";
		select_box.value = (state_check == "N") ? "N" : "";
		pApp.ResizeGrid(200);
        pApp.BindSearchEnter();
		Search();
	});

	var Search = function (page) { // here 
        var formData = $('form[name="search"]').serialize();
        gx.Request('/head/stock/stk06/search_restock', formData, page, stkCallback);
		// console.log(gx.Request('/head/stock/stk06/search_restock', formData, page, stkCallback));
    };

	var stkCallback = function () {
		
	};

	function urlEncode(str){
		var ch;
		var estr = encodeURIComponent(str);
		re = /%C2%A0/gi; // trim으로 걸러지지 않는 특수문자 처리
		estr =estr.replace(re,"%20");
		return estr;
	}

	var SendSMS = function () {
		if (confirm("SMS을 발송하시겠습니까?")){
            var arr = gx.getSelectedRows();
                if (Array.isArray(arr) && !(arr.length > 0)) {
                    alert('항목을 선택 해 주십시오.')
                    return false;
                } else {
					var data = "";
					arr.map(function(obj, idx) {
						data += [obj.no, obj.name, obj.mobile].join('|') + '\t';
					});
					var goods_no = '<?=$goods_no?>';
					var goods_nm = '<?=$goods_nm?>';
					var message = "[재입고] " + goods_nm + " 상품이 재입고 되었습니다.";
					console.log(arr);
                    axios({
                        url: '/head/stock/stk06/update_restock',
                        method: 'put',
                        data: {
							data: urlEncode(data),
							msg: urlEncode(message),
							goods_no: goods_no,
							goods_nm: goods_nm
						}
                    }).then(function (response) {
                        console.log(response) // sms송신이 성공하였고 미알림 요청인 경우 refresh 해줘야 하는 코드 추후 추가해야 함
                    }).catch(function (error) {
                        console.log(error.response.data);
                    });
                }
            };
        };
	

</script>
@stop
