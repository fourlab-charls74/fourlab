@extends('head_with.layouts.layout-nav')
@section('title','택배송장 일괄입력')
@section('content') 
<div class="container-fluid show_layout py-3">
	<form action="">
		@csrf
		<div class="card_wrap aco_card_wrap">
			<div class="card">
				<div class="d-sm-flex card-header mb-0 justify-content-between">
					<a href="#">택배송장 일괄입력</a>
					<div class="d-none d-sm-inline-block">
							<button class="btn btn-sm btn-primary shadow-sm search-btn"><i class="fas fa-search fa-sm mr-1 fs-12"></i>조회</button>
					</div>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
										<tr>
											<th>택배사</th>
											<td>
												<div class="input_box">
													<select id="dlv_cd" class="form-control form-control-sm">
															@foreach ($dlvs as $dlv)
																	<option value='{{ $dlv->code_id }}'{{ $dlv->code_id === $dlv_cd ? 'selected' : '' }}>{{ $dlv->code_val }}</option>
															@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th>파일선택</th>
											<td>
												<div class="inline-inner-box ty2 triple">
													<div class="img_file_cum_wrap">
														<div class="custom-file">
															<input type="file" class="custom-file-input" id="file" aria-describedby="inputGroupFileAddon03">
															<label class="custom-file-label" for="file">파일 찾아보기</label>
														</div>
														<div class="btn-group">
															<button class="btn btn-outline-primary apply-btn" type="button">적용</button>
														</div>
													</div>
													<p class="mb-0 cum_stxt mt-2">* 파일형식: TSV(탭으로 분리) - 샘플파일.txt (주문일련번호, 송장번호)</p>
												</div>
											</td>
										</tr>
										<tr>
											<th>SMS 발송</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio mr-1">
														<input type="radio" name="snd_sms_yn" id="snd_sms_yn_y" value="Y" class="custom-control-input" checked="">
														<label class="custom-control-label" for="snd_sms_yn_y">즉시 발송</label>
													</div>
													<div class="custom-control custom-radio mr-1">
														<input type="radio" name="snd_sms_yn" id="snd_sms_yn_n" value="N" class="custom-control-input">
														<label class="custom-control-label" for="snd_sms_yn_n">발송 안함</label>
													</div>
													<div class="custom-control custom-radio mr-0">
														<input type="radio" name="snd_sms_yn" id="snd_sms_yn_r" value="R" class="custom-control-input">
														<label class="custom-control-label" for="snd_sms_yn_r">예약 발송</label>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
		<!-- DataTales Example -->
		<div class="card shadow-none mb-4 ty2 last-card">
			<div class="card-body m-0 brtn">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box px-0 mx-0">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
						</div>
						<div class="fr_box">
							<a href="#" class="btn btn-sm btn-primary shadow-sm out-complate-btn">출고완료</a>
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
		let columns = [
				{
					field: "chk",
					headerName: '',
					headerCheckboxSelection: true,
					headerCheckboxSelectionFilteredOnly: true,
					width: 50,
					checkboxSelection: function(params) {
						return params.data.chk != 2;
					},
					cellStyle: function(params){
						if (params.value === 2) 
							return { "display" : "none" };
					}
				},
				{
					headerName: '#',
					width:50,
					maxWidth: 100,
					valueGetter: 'node.id',
					cellRenderer: 'loadingRenderer',
				},
				{field:"msg" , headerName:"내용"},
				{field:"ord_opt_no" , headerName:"주문일련번호"},
				{field:"dlv_cd_nm" , headerName:"택배사"  },
				{field:"dlv_no" , headerName:"송장번호"  },
				{field:"ord_no" , headerName:"주문번호"},
				{field:"ord_state_nm" , headerName:"주문상태"  },
				{field:"clm_state_nm" , headerName:"클레임상태"},
				{field:"ord_kind_nm" , headerName:"출고구분"  },
				{field:"dlv_series_nm" , headerName:"출고차수"  },
				{field:"goods_nm" , headerName:"상품명"  },
				{field:"goods_opt" , headerName:"옵션"  },
				{field:"qty" , headerName:"수량"  }
		];

</script>
<script language="javascript">
const pApp = new App('', {
	gridId: "#div-gd",
});
const gridDiv = document.querySelector(pApp.options.gridId);
let gx;

$(document).ready(function () {
	gx = new HDGrid(gridDiv, columns);

	gx.gridOptions.isRowSelectable = function(params) {
		return params.data.chk != 2;
	}

	pApp.ResizeGrid();
	Search();
});

function Search() {
	let data = $('form[name="search"]').serialize();
	gx.Request('/head/order/ord22/dlv-import/search', data);
}

function validateTSV() {
	const target = $('#file')[0].files;

	if (target === null || target.length === 0) {
		alert("업로드할 TSV파일을 선택해주세요.");
		return false;
	}

	if (!/(.*?)\.(tsv|TSV|txt|TXT)$/i.test(target[0].name)) {
		var msg  = "";
		msg += "선택하신 문서를 읽을 수 없습니다.\n\n";
		msg += "- 선택하신 문서가 TSV(Tab으로 구분된) 텍스트 문서인지 확인해 주십시오.\n";
		msg += "- IE8.0의 경우 'c:\\fakepath' 에서 파일을 선택하거나 '도구 > 인터넷 옵션' 에서 '업로드할 로컬디렉토리' 설정을 변경 해 주십시오. ";

		alert(msg);

		return false;
	}

	return true;
}

$('.apply-btn').click(function(e){
	if (!validateTSV()) return;

	const target = $('#file')[0].files[0];
	const fr = new FileReader();

	fr.onload = (e) => {
		const lines = e.target.result.split('\r\n');
		const uploadLines = [];
		lines.forEach((line) => {
			if (line === '') return;
			uploadLines.push(line.replace(/\t/, ","));
		});
		console.log()
		$.ajax({
			async: true,
			type: 'put',
			url: '/head/order/ord22/dlv-import/upload',
			data: {
				"datas[]" : uploadLines,
				dlv_cd : $('#dlv_cd').val()
			},
			success: function (data) {
					Search();
			},
			error: function(request, status, error) {
					console.log("error")
			}
		});
	}

	fr.readAsText(target);
});

$('#file').change(function(e){
	if (this.files.length > 1) {
		alert("파일은 1개만 올려주세요.");
		return;
	}

	if (validateTSV() === false) {
		$('.custom-file-label').html('TSV파일을 선택해주세요.');
		return;
	}

	$('.custom-file-label').html(this.files[0].name);
});

$('.search-btn').click(Search);

$(".out-complate-btn").click(function()
{
	let checkRows = gx.gridOptions.api.getSelectedRows();

	if( $('#dlv_cd').val() == "" ){
		alert("택배사를 선택해주세요.");
		return;
	}

	if (checkRows.length === 0) {
		alert("출고완료하실 주문건을 선택해주세요.");
		return;
	}

	if(confirm("선택하신 주문을 출고완료로 변경하시겠습니까?")) {
		let orderNos = checkRows.map(function(row) {
			return [row.ord_no, row.ord_opt_no, row.dlv_no];
		});

		$.ajax({
			async: false,
			type: 'put',
			url: '/head/order/ord22/out-complete',
			data: {
				"order_nos[]" : orderNos,
				ord_state : 30,
				dlv_cd : $("#dlv_cd").val(),
				send_sms_yn : $("[name=snd_sms_yn]:checked").val()
			},
			success: function (data) {
				if(data.code == "200" ){
					alert("출고완료 상태로 변경되었습니다.");
					Search();
				}else{
					alert(data.msg);
				}
			},
			error: function(request, status, error) {
				const msg = request.responseJSON.msg;
				const code = request.status;
				alert(`${msg} (Code : ${code})`);
				//console.log(error);
			}
		});
	}
});
</script>
@stop
