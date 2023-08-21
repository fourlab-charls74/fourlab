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
						<button type="button" class="btn btn-sm btn-primary shadow-sm search-btn"><i class="fas fa-search fa-sm mr-1 fs-12"></i>조회</button>
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
		<div class="card shadow-none ty2 last-card">
			<div class="card-body m-0 brtn">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box px-0 mx-0">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
						</div>
						<div class="fr_box">
							<a href="javascript:void(0);" class="btn btn-sm btn-primary shadow-sm out-complate-btn">출고완료</a>
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
					width: 28,
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
					width:40,
					maxWidth: 100,
					valueGetter: 'node.id',
					cellRenderer: 'loadingRenderer',
				},
				{field:"msg" , headerName:"내용", 
					cellRenderer: (p) => p.value === "S" ? "성공" : p.value === "F" ? "실패" : "", 
					cellStyle: (p) => ({"text-align": "center", "color": p.value === "S" ? "blue" : p.value === "F" ? "red" : "black"}),
				},
				{field:"ord_opt_no" , headerName:"주문일련번호", width: 80, cellClass: 'hd-grid-code'},
				{field:"dlv_cd_nm" , headerName:"택배사"  },
				{field:"dlv_no" , headerName:"송장번호"  },
				{field:"ord_no" , headerName:"주문번호", width: 120},
				{field:"ord_state_nm" , headerName:"주문상태", cellStyle: StyleOrdState, width: 60},
				{field:"clm_state_nm" , headerName:"클레임상태", cellStyle: StyleClmState, width: 65},
				{field:"ord_kind_nm" , headerName:"출고구분", cellStyle: StyleOrdKind, cellClass: 'hd-grid-code', width: 60},
				{field:"dlv_series_nm" , headerName:"출고차수"  },
				{field:"goods_nm" , headerName:"상품명", width: 90},
				{field:"goods_opt" , headerName:"옵션"  },
				{field:"qty" , headerName:"수량", type: "currencyType"},
				{width: "auto"}
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

	pApp.ResizeGrid(365);
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
			uploadLines.push(line.replaceAll(/\t/ig, ","));
		});

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

	fr.readAsText(target, "EUC-KR");
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

		let call_cnt = 0;
		(function (call_cnt) {
			for (let i = 0; i < orderNos.length; i++) {
				let order = orderNos[i];

				$.ajax({
					type: 'put',
					url: '/head/order/ord22/out-complete',
					data: {
						"order_nos[]" : [order],
						ord_state : 30,
						dlv_cd : $("#dlv_cd").val(),
						send_sms_yn : $("[name=snd_sms_yn]:checked").val()
					},
					success: function (data) {
						gx.gridOptions.api.forEachNode(node => {
							if (node.data.ord_opt_no === order[1]) {
								node.data.msg = data.code == "200" ? '성공' : '실패';
								node.data.chk = data.code == "200" ? 2 : node.data.chk;
								node.setSelected(false);
								node.setRowSelectable(!data.code == "200");
								gx.gridOptions.api.redrawRows({ rowNodes: [node] });
								gx.gridOptions.api.ensureIndexVisible(node.rowIndex, 'bottom');
								call_cnt++;
							}
						});

						if (call_cnt === orderNos.length) {
							alert('택배송장 일괄입력이 완료되었습니다. "내용" 항목에서 성공여부를 확인해주세요.');
							call_cnt = 0;
							return;
						}
					},
					error: function(request, status, error) {
						const msg = request.responseJSON.msg;
						const code = request.status;
						alert(`${msg} (Code : ${code})`);
					}
				})
			}
		}(call_cnt));
	}
});
</script>
@stop
