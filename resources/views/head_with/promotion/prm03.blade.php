@extends('head_with.layouts.layout')
@section('title','카드무이자')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">카드무이자</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 카드무이자</span>
    </div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="AddCdInfo();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
			<div class="card-body">
				<!-- 카드사 -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">카드사</label>
							<div class="flax_box">
								<select name='credit_card_cd' class="form-control form-control-sm">
                                    <option value="">전체</option>
									@foreach($credit_card_items as $card_item)
										<option value="{{ $card_item->code_id }}">{{ $card_item->code_val }}</option>
									@endforeach
                                </select>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="AddCdInfo();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
			<a href="#" onclick="Cmder('delcmd');" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
	</div>
</form>

<div class="show_layout">
    <form name="f1" id="f1">
        <input type="hidden" name="c">
        <div class="row">
            <div class="col-sm-6">
                <div class="card_wrap">
                    <div class="card shadow">
						<div class="card-title">
							<div class="filter_wrap">
								<div class="fl_box">
									<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
								</div>
							</div>
						</div>
						<div class="table-responsive">
							<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
						</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="row_wrap">
                            <div class="card-header mb-0">
                                <h5 class="m-0 font-weight-bold">상세 정보</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
										<tbody>
											<tr id="credit_card_cd_d" style="display:none;">
												<th>카드사</th>
												<td>
													<div id="goods_info_area" class="flax_box">
														<select name="credit_card_cd" id="credit_card_cd" class="form-control form-control-sm">
															<option value="">카드사</option>
															@foreach($credit_card_items as $card_item)
																<option value="{{ $card_item->code_id }}">{{ $card_item->code_val }}</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
											<tr id="credit_card_cd_v" style="display:none">
												<th>카드사</th>
												<td>
													<div class="txt_box">
														<span id="credit_card_cd_view"></span>
													</div>
												</td>
											</tr>
											<tr>
												<th>할부개월수</th>
												<td>
													<div id="goods_info_area" class="form-inline">
														<div class="form-inline-inner input_box">
															<select name="month_fr" id="month_fr" class="form-control form-control-sm">
																<option value="">할부기간</option>
																@for($i=2; $i < 37; $i++)
																	<option value="{{ $i }}">@if($i < 10) 0{{ $i }} @else {{ $i }} @endif </option>
																@endfor
															</select>
														</div>
														<span class="text_line">~</span>
														<div class="form-inline-inner input_box">
															<select name="month_to" id="month_to" class="form-control form-control-sm">
																<option value="">할부기간</option>

																@for($i=2; $i < 37; $i++)
																	<option value="{{ $i }}">@if($i < 10) 0{{ $i }} @else {{ $i }} @endif </option>
																@endfor
															</select>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>적용금액</th>
												<td>
													<div id="goods_info_area" class="flax_box">
														<input type="text" name="m_price" value="" class="form-control form-control-sm mr-1" id="m_price" placeholder="(예:50000)" autocomplete='off' >
													</div>
												</td>
											</tr>
											<tr>
												<th>표기금액</th>
												<td>
													<div id="goods_info_area" class="flax_box">
														<input type="text" name="m_price_print" value="" class="form-control form-control-sm mr-1" id="m_price_print" placeholder="(예:5만원)" autocomplete='off' >
													</div>
												</td>
											</tr>
											<tr>
												<th>행사기간</th>
												<td>
													<div class="form-inline">
														<div class="docs-datepicker form-inline-inner input_box">
															<div class="input-group">
																<input type="text" class="form-control form-control-sm docs-date" name="date_fr" value="" autocomplete="off" disable>
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
																<input type="text" class="form-control form-control-sm docs-date" name="date_to" value="" autocomplete="off">
																<div class="input-group-append">
																	<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																	</button>
																</div>
															</div>
															<div class="docs-datepicker-container"></div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>표기금액</th>
												<td>
													<div class="flax_box">
														<select name="is_use" id="use_yn" class="form-control form-control-sm">
															<option value="">사용여부</option>
															<option value="Y">사용</option>
															<option value="N">미사용</option>
														</select>
													</div>
												</td>
											</tr>
										</tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- 품목코드 -->
                        </div>
                        <!-- 확인 -->
						<div style="text-align:center;" class="mt-3">
							<input type="button" class="btn btn-sm btn-primary shadow-sm" id="btn_add" value="저장" onclick="Cmder('addcmd');" style="display:none;">
							<input type="button" class="btn btn-sm btn-primary shadow-sm" id="btn_edit" value="수정" onclick="Cmder('editcmd')" style="display:none;">
							<input type="button" class="btn btn-sm btn-primary shadow-sm" value="취소" onclick="document.f1.reset();">
						</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script language="javascript">
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
			{field:"code_val",headerName:"카드사",width:50, cellStyle:StyleGoodsTypeNM, width:80, },
			{field:"ymd_fr_to",headerName:"행사기간",width:70,cellStyle:StyleGoodsTypeNM, width:180},
			{field:"month_fr_to",headerName:"할부기간",width:80, 
			},
			{field:"m_price",headerName:"적용금액",  },
			{field:"m_price_print",headerName:"표기금액", },
			{field:"is_use",headerName:"사용여부", },
			{field:"credit_card_cd", headerName:"credit_card_cd", hide:true}
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);
	gx.gridOptions.onRowClicked = showCardInfo;
	pApp.ResizeGrid();

	function showCardInfo(event){
		var code = event.node.data.credit_card_cd;
//		console.log(code);

		$.ajax({
            async: true,
            type: 'get',
            url: '/head/promotion/prm03/'+code,
            success: function (data) {
				cbGetInfo(data.result);
            },
            complete:function(){
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
	}
	
	function cbGetInfo(res){

		if(res){
			var date_fr = res.date_from.substring(0,4) +"-"+ res.date_from.substring(4,6) +"-"+ res.date_from.substring(6,8) ;
			var date_to = res.date_end.substring(0,4) +"-"+ res.date_end.substring(4,6) +"-"+ res.date_end.substring(6,8) ;
			$("[name=credit_card_cd]").val(res.credit_card_cd);
			$("#credit_card_cd_view").html(res.credit_card_cd);
			$("[name=month_fr]").val(res.month1);
			$("[name=month_to]").val(res.month2);
			$("#m_price").val(res.m_price);
			$("#m_price_print").val(res.m_price_print);
			$("[name=date_fr]").val(date_fr);
			$("[name=date_to]").val(date_to);
			$("#use_yn").val(res.is_use);

			$("#credit_card_cd_d").hide();
			$("#credit_card_cd_v").show();

			$("#btn_add").hide();
			$("#btn_edit").show();

		}


	}

	function AddCdInfo(){
		FormReset(document.f1);
		
		$("#credit_card_cd_d").show();
		$("#credit_card_cd_v").hide()
	}

	function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/promotion/prm03/search', formData, 1);
    }

	function FormReset(form){

		form.reset();
		$("#credit_card_cd_view").innerHTML = "";
		$("#btn_add").show();
		$("#btn_edit").hide();
	}

	function Cmder(cmd) {
		if(cmd == "addcmd" || cmd == "editcmd") {

			if(Validate(cmd)){
				CmdSave(cmd);
			}
			
		} else if(cmd == "delcmd") {

			if(Validate(cmd)){
				CmdDel(cmd);
			}
			
		}
	}

	function CmdSave(cmd){
		var f1 = $("#f1");
		$.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm03/store/'+cmd,
			data: f1.serialize(),
            success: function (data) {
				if(data.return_code == 1){
					Search();
				}else{
					alert("장애가 발생했습니다. \n 관리자에게 문의하시기 바랍니다.");
				}
            },
            complete:function(){
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
	}

	function CmdDel(cmd){
		var f1 = $("#f1");
		$.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm03/store/'+cmd,
			data: f1.serialize(),
            success: function (data) {
				if(data.return_code == 1){
					Search();
				}else{
					alert("장애가 발생했습니다. \n 관리자에게 문의하시기 바랍니다.");
				}
            },
            complete:function(){
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
	}

	function Validate(cmd){
		if(cmd == "addcmd" || cmd == "editcmd"){
			if($("[name=credit_card_cd]").val() == ""){
				alert("카드사를 선택해 주십시오.");
				$("[name=credit_card_cd]").focus();
				return false;
			}
			if($("[name=month_fr]").val() == ""){
				alert("할부개월수를 선택해 주십시오.");
				$("[name=month_fr]").focus();
				return false;
			}
			if($("[naem=month_to]").val() == ""){
				alert("할부개월수를 선택해 주십시오.");
				$("[name=month_to]").focus();
				return false;
			}
			if($("[name=m_price]").val() == ""){
				alert("적용금액을 입력해 주십시오.");
				$("[name=m_price]").focus();
				return false;
			}
			if($("[name=m_price_print]").val() == ""){
				alert("표기금액을 입력해 주십시오.");
				$("[name=m_price_print]").focus();
				return false;
			}
			if($("[name=date_fr]").val() == ""){
				alert("행사기간을 입력해 주십시오.");
				$("[name=date_fr]").focus();
				return false;
			}
			if($("[naem=date_to]").val() == ""){
				alert("행사기간을 입력해 주십시오.");
				$("[name=date_to]").focus();
				return false;
			}
			if($("[name=is_use]").val() == ""){
				alert("사용여부를 선택해 주십시오.");
				$("[name=is_use]").focus();
				return false;
			}
			return true;
		}

		if(cmd == "delcmd"){
			//var row = gxList.getRow();
			var selectedRowData = gx.gridOptions.api.getSelectedRows();
			
			if(!selectedRowData){
				alert("삭제하실 카드사를 그리드에서 선택해 주십시오.");
				return false;
			}
			//var credit_card_nm = gxList.Cell(0,row,1);
			var credit_card_nm = "";
			selectedRowData.forEach( function(selectedRowData, index) {
				credit_card_nm = selectedRowData.code_val;
			});

			if(! confirm("선택하신 '"+credit_card_nm+"' 카드사 정보를 삭제 하시겠습니까?")){
				return false;
			}
			return true;
		}
	}


    function gridCallback (data){
        //console.log("data : "+data);
    }

	$(function(){
		Search();
	});


</script>
@stop
