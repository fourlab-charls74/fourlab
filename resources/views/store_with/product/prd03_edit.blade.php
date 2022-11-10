@extends('store_with.layouts.layout-nav')
@section('title', '원부자재상품관리')
@section('content')

<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">원부자재상품수정</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 원부자재상품관리</span>
				<span>/ 수정</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0);" onclick="save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>수정</a>
			<a href="javascript:void(0);" onclick="del();" class="btn btn-outline-primary mr-1"><i class="far fa-trash-alt fs-12"></i> 삭제</a>
			<a href="javascript:void(0);" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
		</div>
	</div>

	<style>
		.required:after {
			content: " *";
			color: red;
		}

		.table th {
			min-width: 120px;
		}

		@media (max-width: 740px) {
			.table td {
				float: unset !important;
				width: 100% !important;
			}
		}

		/* 상품코드 api로 검색시 code-filter 부분 제거 */
		#SearchPrdcdModal .code-filter {
			display: none;
			padding-top: 5px;
		}

		#SearchPrdcdModal #search_prdcd_sbtn {
			margin-top: 27px;
		}
	</style>

	<form name="f1" id="f1">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">원부자재상품 정보 입력</a>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<div class="table-box-ty2 mobile">
								<table class="table incont table-bordered" width="100%" cellspacing="0">
									<tbody>
										<tr>
											<th class="">원부자재코드</th>
											<td colspan="3">
												<div class="flax_box">{{$prd_cd}}</div>
											</td>
										</tr>
										<tr>
											<th class="required">원부자재명</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='prd_nm' id="prd_nm" value='{{$prd_nm}}'>
												</div>
											</td>
											<th class="">원부자재업체</th>
											<td>
												<div class="flax_box">{{$sup_com}}</div>
											</td>
										</tr>
										<tr>
											<th class="required">판매가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='price' id="price" value='{{$price}}' onkeyup="onlynum(this)">
												</div>
											</td>
											<th class="required">원가</th>
											<td>
											<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='wonga' id="wonga" value='{{$wonga}}' onkeyup="onlynum(this)">
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">단위</th>
											<td>
												<div class="flax_box">
													<select name='unit' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($units as $unit)
														<option value='{{ $unit->code_id }}' {{$unit->code_id == $unit_id ? "selected" : ""}}>
															{{ $unit->code_id }} : {{ $unit->code_val }}
														</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="">{{$ut != "" ? "수정일" : "등록일"}}</th>
											<td>
												<div class="flax_box">{{$ut != "" ? $ut : $rt}}</div>
											</td>
										</tr>
										<tr>
											<th>이미지</th>
											<td colspan="3">
												<div style="text-align:center;" id="multi_img">
													<input type='file' id='btnAdd' name="btnAdd" multiple='multiple' accept=".jpg" />
												</div>
												<div id='img_div'></div>
												<div id='img_show_div' data-img="" style="display:inline-block;position:relative;width:150px;height:150px;margin:5px;z-index:1">
													<img src="{{$img_url}}" alt="" id="img_show" style="width:100%;height:100%;z-index:none">
													<input type="button" value="x" onclick="delete_img('{{$prd_cd}}','01')" style="width:20px;height:20px;position:absolute;right:0px;top:0px;border:none;font-size:large;font-weight:bolder;background:none;color:black;padding-bottom:20px;">
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
	</form>

</div>
<script type="text/javascript" charset="utf-8">

	const onlyNum = (obj) => {
		val = obj.value;
		new_val = '';
		for (i=0; i<val.length; i++) {
			char = val.substring(i, i+1);
			if (char < '0' || char > '9') {
				alert('숫자만 입력가능 합니다.');
				obj.value = new_val;
				return;
			} else {
				new_val = new_val + char;
			}
		}
	};

	const validation = (cmd) => {

		// 원부자재명 입력여부
		if (f1.prd_nm.value.trim() === '') {
			f1.prd_nm.focus();
			return alert("원부자재명을 입력해주세요.");
		}

		// 판매가 입력여부
		if (f1.price.value.trim() === '') {
			f1.price.focus();
			return alert("판매가를 입력해주세요.");
		}

		// 원가 입력여부
		if (f1.wonga.value.trim() === '') {
			f1.wonga.focus();
			return alert("원가를 입력해주세요.");
		}

		// 단위 선택여부
		if (f1.unit.selectedIndex == 0) {
			f1.unit.focus();
			return alert("단위를 선택해주세요.");
		}

		return true;
	}

	const PRD_CD = "{{$prd_cd}}";
	let added_base64_image = "";

	function save() {
		if (!validation()) return;
		axios({
			url: '/store/product/prd03/edit',
			method: 'post',
			data: {
				prd_cd: PRD_CD,
				prd_nm: document.f1.prd_nm.value,
				price: document.f1.price.value,
				wonga: document.f1.wonga.value,
				image: added_base64_image,
				unit: document.f1.unit.value,
				seq: 01, // 단일 이미지로 일단 처리 - 01로 고정
				img: document.getElementById('img_show').src
				
			},
		}).then(function(res) {
			if (res.data.code === 200) {
				alert("수정이 완료되었습니다.");
				window.opener.Search();
				window.close();
			} else {
				console.log(res.data);
				alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function(err) {
			console.log(err);
		});
	}

	async function del() {
		if (!confirm("삭제 하시겠습니까?")) {
			return false;
		}
		const response = await axios({ url: `/store/product/prd03/delete/${PRD_CD}`, method: 'get' });
		const { code } = response.data;
		if (code == 200) {
			alert("삭제가 완료되었습니다.");
			window.opener.Search();
			window.close();
		} else if (code == 500) {
			alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
		}
	}

	/**
	 * 이미지 - 매장관리 구현된 이미지 참고하여 작업
	 */
	$(document).ready(function() {
		$("input:file[name='file']").change(function() {
			var str = $(this).val();
			var fileName = str.split('\\').pop().toLowerCase();

			checkFileName(fileName);
		});
	});

	function checkFileName(str) {

		//1. 확장자 체크
		var ext = str.split('.').pop().toLowerCase();
		if ($.inArray(ext, ['jpg']) == -1) {
			alert(ext + '파일은 업로드 하실 수 없습니다.');
			$("input:file[name='file']").val("");
			$('#img_div').remove();
		} else {

		}
	}
	
	(imageView = function imageView(img_div, btn) {

		var img_div = document.getElementById(img_div);
		var btnAdd = document.getElementById(btn)
		var sel_files = [];

		// 이미지와 체크 박스를 감싸고 있는 div 속성
		var div_style = 'display:inline-block;position:relative;' +
			'width:120px;height:120px;margin:5px;z-index:1';
		// 미리보기 이미지 속성
		var img_style = 'width:100%;height:100%;z-index:none';
		// 이미지안에 표시되는 체크박스의 속성
		var chk_style = 'width:20px;height:20px;position:absolute;right:0px;top:0px;border:none;font-size:large;' +
			'font-weight:bolder;background:none;color:black;padding-bottom:20px;';

		btnAdd.onchange = function(e) {
			var files = e.target.files;
			var fileArr = Array.prototype.slice.call(files)
			for (f of fileArr) {
				imageLoader(f);
			}
		}

		/*첨부된 이미지들을 배열에 넣고 미리보기 */
		imageLoader = function(file) {

			// 이미지 한개만 미리 보기 되도록 고정
			if (img_div.getElementsByTagName('div').length > 0) {
				img_div.removeChild(img_div.getElementsByTagName('div')[0]);

			}
			
			sel_files.push(file);
			var reader = new FileReader();
			reader.onload = function(ee) {
				let img = document.createElement('img')
				img.setAttribute('style', img_style)
				img.src = ee.target.result;
				added_base64_image = ee.target.result;
				let div = document.querySelector("#img_show_div");
				div.style.display = 'none';

				img_div.appendChild(makeDiv(img, file));
			}

			reader.readAsDataURL(file);
		}

		makeDiv = function(img, file) {
			var div = document.createElement('div');
			div.setAttribute('style', div_style);

			var btn = document.createElement('input');
			btn.setAttribute('type', 'button');
			btn.setAttribute('value', 'x');
			btn.setAttribute('delFile', file.name);
			btn.setAttribute('style', chk_style);
			btn.onclick = function(ev) {
				var ele = ev.srcElement;
				var delFile = ele.getAttribute('delFile');
				for (var i = 0; i < sel_files.length; i++) {
					if (delFile == sel_files[i].name) {
						sel_files.splice(i, 1);
					}
				}

				dt = new DataTransfer();

				for (f in sel_files) {
					var file = sel_files[f];
					dt.items.add(file);
				}
				btnAdd.files = dt.files;
				var p = ele.parentNode;
				img_div.removeChild(p);
			}
			div.appendChild(img);
			div.appendChild(btn);

			return div;
		}
	})('img_div', 'btnAdd')

	function delete_img(prd_cd, seq) {
		let img_show = document.querySelectorAll("#img_show_div");
		console.log(img_show);
		
		if (confirm("선택한 사진을 삭제하시겠습니까?")) {
			$.ajax({
				method: 'post',
				url: '/store/product/prd03/del-img',
				data: {
					prd_cd: prd_cd,
					seq: seq
				},
				success: function(data) {
					if (data.code == 200) {
						for (let i = 0; i < img_show.length; i++) {
								img_show[i].remove();
								break;
							}

						alert('이미지가 삭제되었습니다.');
					} else {
						alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
					}
				},
				error: function(res, status, error) {
					console.log(error);
				}
			});
		}
	}
</script>
@stop