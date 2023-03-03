@extends('head_with.layouts.layout')
@section('title','환불완료(카드/계좌이체)')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">환불완료(카드/계좌이체)</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 클레임/CS</span>
        <span>/ 환불완료(카드/계좌이체)</span>
    </div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<!-- 환불대상일/환불완료일 -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">환불대상일</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box" >
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
											<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
								<span class="text_line">~</span>
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
											<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="enddate">환불완료일</label>
							
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inne input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="enddate" value="{{ $today }}" autocomplete="off">
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
											<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			
				<!-- 결제수단/주문번호-->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">결제수단</label>
							<div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="pay_type[1]" id="pay_type2" class="custom-control-input" value="2" checked>
                                    <label class="custom-control-label" for="pay_type2">카드</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="pay_type[2]" id="pay_type16" class="custom-control-input" value="16" checked>
                                    <label class="custom-control-label" for="pay_type16">계좌이체</label>
                                </div>
                            </div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_type">주문번호</label>
							<div class="flax_box">
								 <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 500px);min-height:300px;width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>


<script language="javascript">
	var columns = [
		{headerName: '#', width:50, maxWidth: 90,type:'NumType', pinned:'left'},
		{field: "clm_no", headerName: "clm_no", hide:true},
		{field:"ord_no" , headerName:"주문번호",type:'HeadOrderNoType', pinned:'left'},
		{field: "ord_opt_no", headerName: "ord_opt_no", hide:true},
		{field:"user_nm" , headerName:"주문자", type:"HeadUserType"},
		{field:"user_id", headerName:"user_id", hide:true},
		{field:"pay_type" , headerName:"결제방법" },
		{field:"escw_use" , headerName:"에스크로"  },
		{field:"st_cd", headerName:"구매확인/취소",},
		{field:"pay_amt" , headerName:"입금액"  },
		{field:"refund_bank" , headerName:"환불방법"  },
		{field:"refund_amt" , headerName:"환불금액",type:'currencyType' },
		{field:"tno" , headerName:"거래번호",cellStyle:StyleOrdState,
			cellRenderer: function(params) {
				return '<a href="#" onClick="PopRefund(\''+ params.data.ord_no +'\', \''+ params.data.ord_opt_no +'\')">'+ params.value+'</a>'
			}
		},
		{field:"refund_yn" , headerName:"환불여부"  },
		{field:"ord_state_nm" , headerName:"주문상태",cellStyle:StyleOrdState  },
		{field:"clm_state_nm" , headerName:"클레임상태", cellStyle:StyleClmState },
		{field:"clm_reason" , headerName:"클레임사유"  },
		{field:"memo" , headerName:"클레임내용"  },
		{field:"clm_state", headerName: "clm_state", hide:true},
		{field:"req_nm" , headerName:"환불요청자"  },
		{field:"req_date" , headerName:"환불요청일시"},
		{field:"end_nm" , headerName:"환불완료자"  },
		{field:"end_date" , headerName:"환불완료일시"  },
		{field:"confirm_id", headerName:"confirm_id", hide:true},
		{headerName: "", field: "nvl"}
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(265);


	function Search() {
		let data = $('form[name="search"]').serialize();
		
		gx.Request('/head/cs/cs06/search', data,1);
	}


	$(function(){
		Search();
	});


	function StyleOrdState(params){
		var font_color = "";
		var font_style = "";
		if(params.value !== undefined){
			var ord_state = params.data.ord_state_nm;
			switch(ord_state){
				case "입금예정":
					font_color = "#669900";
					font_style = "";
					break;
				case "입금완료":
					font_color = "#ff0000";
					font_style = "bold";
					break;
				case "출고요청":
					font_color = "#0000ff";
					font_style = "";
					break;
				case "출고처리중":
					font_color = "#0000ff";
					font_style = "bold";
					break;
				case "출고완료":
					font_color = "#0000ff";
					font_style = "bold";
					break;
				case "주문취소":
					font_color = "#0000ff";
					font_style = "bold";
					break;
				case "결제오류":
					font_color = "#ff0000";
					font_style = "bold";
					break;
				case "구매확정":
					font_color = "#0000ff";
					font_style = "bold";
					break;
			}

			return {
				'color': font_color,
				'font-weight': font_style
			}
		}

		var state = {
			
		}
		var value = gx.TextMatrix(row,col);

		return {
			'color': '#ffff99'
		}
	}

	function StyleClmState(params){
		if(params.value !== undefined){
			if(params.value != ""){
				return {
					'color': '#FF0000',
					'font-weight': 'bold'
				}
			}
		}

	}


	function PopRefund(ord_no, ord_opt_no){
        //const url='/head/member/mem01?cmd=edit&user_id='+memId;
        const url='/head/cs/cs06/refund?ord_no='+ ord_no +"&ord_opt_no="+ord_opt_no;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1115,height=810");
    }





</script>

@stop
