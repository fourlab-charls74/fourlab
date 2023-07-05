@extends('store_with.layouts.layout-nav')
@section('title', '원부자재상품등록')
@section('content')

<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">원부자재상품등록</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 원부자재관리</span>
				<span>/ 원부자재상품등록</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0)" onclick="save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
			<a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
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
											<th class="required">구분</th>
											<td style="width:35%;">
												<div class="flax_box">
													<select name='type' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($types as $type)
														<option value='{{ $type->code_id }}'>{{ $type->code_id }} : {{ $type->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">년도</th>
											<td style="width:35%;">
												<div class="flax_box">
													<select name='year' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($years as $year)
														<option value='{{ $year->code_id }}'>{{ $year->code_id }} : {{ $year->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">시즌</th>
											<td>
												<div class="flax_box">
													<select name='season' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($seasons as $season)
														<option value='{{ $season->code_id }}'>{{ $season->code_id }} : {{ $season->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">성별</th>
											<td>
												<div class="flax_box">
													<select name='gender' id="gender" class="form-control form-control-sm" onchange="change_gender()">
														<option value=''>선택</option>
														@foreach ($genders as $gender)
														<option value='{{ $gender->code_id }}'>{{ $gender->code_id }} : {{ $gender->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">아이템</th>
											<td>
												<div class="flax_box">
													<select name='item' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($items as $item)
														<option value='{{ $item->code_id }}'>{{ $item->code_id }} : {{ $item->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">품목</th>
											<td>
												<div class="flax_box">
													<select name='opt' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($opts as $opt)
														<option value='{{ $opt->code_id }}'>{{ $opt->code_id }} : {{ $opt->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">칼라</th>
											<td>
												<div class="flax_box">
													<select name='color' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($colors as $color)
														<option value='{{ $color->code_id }}'>{{ $color->code_id }} : {{ $color->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">사이즈</th>
											<td>
												<div class="flax_box">
													<select name='size' id="size" class="form-control form-control-sm">
														<option value=''>선택</option>
														<!-- @foreach ($sizes as $size)
														<option value='{{ $size->code_id }}'>{{ $size->code_val }} : {{ $size->code_val2 }}</option>
														@endforeach -->
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">원부자재명</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='prd_nm' id="prd_nm" value=''>
												</div>
											</td>
											<th class="required">원부자재 업체</th>
											<td>
												<div class="flax_box">
													<select name='sup_com' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($sup_coms as $com)
														<option value='{{ $com->com_id }}'>{{ $com->com_id }} : {{ $com->com_nm }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<!-- <th class="required">원부자재명(영문)</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='prd_nm_eng' id="prd_nm_eng" value=''>
												</div>
											</td> -->
										</tr>
										<tr>
											<th class="required">Tag가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='tag_price' id="tag_price" value='' onkeyup="onlynum(this)">
												</div>
											</td>
											<th class="required">판매가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='price' id="price" value='' onkeyup="onlynum(this)">
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">원가</th>
											<td>
											<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='wonga' id="wonga" value='' onkeyup="onlynum(this)">
												</div>
											</td>
											<th class="required">단위</th>
											<td>
												<div class="flax_box">
													<select name='unit' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($units as $unit)
														<option value='{{ $unit->code_id }}'>{{ $unit->code_id }} : {{ $unit->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th>이미지</th>
											<td colspan="3">
												<div style="text-align:center;" id="multi_img">
													<input type='file' id='btnAdd' name="file" multiple='multiple' accept=".jpg" />
												</div>
												<div id='img_div'></div>
												@if(count($images) > 0)
													@foreach(@$images as $image)
													<div id='img_show_div' data-img="{{$image->seq}}" style="display:inline-block;position:relative;width:150px;height:150px;margin:5px;z-index:1">
														<img src="{{$image->img_url}}" alt="" id="img_show" style="width:100%;height:100%;z-index:none">
														<input type="button" value="x" onclick="delete_img('{{$image->prd_cd}}','{{$image->seq}}')" style="width:20px;height:20px;position:absolute;right:0px;top:0px;border:none;font-size:large;font-weight:bolder;background:none;color:black;padding-bottom:20px;">
													</div>
													@endforeach
												@endif
											</td>
										</tr>
								</table>

								<div style="width:100%;padding-top:20px;text-align:center;">
									<button type="button" class="btn btn-primary ml-2" onclick="add()">추가</button>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header mb-0">
				<a href="#">일괄저장목록</a>
			</div>
			<div class="card-body pt-2">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box px-0 mx-0">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
						</div>
						<div class="fr_box">
							<a href="#" onclick="return deleteRows();" class="btn btn-sm btn-primary shadow-sm option-del-btn"><span class="fs-12">제거</span></a>
						</div>
					</div>
				</div>
				<div class="table-responsive">
						<div id="div-gd" class="ag-theme-balham"></div>
					</div>
			</div>
		</div>

	</form>

</div>

<script>
	const columns = [{
			field: "chk",
			headerName: '',
			cellClass: 'hd-grid-code',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width: 30,
			sort: null,
		},
		{
			field: "type",
			headerName: "구분",
			width: 70
		},
		{
			field: "image_url",
			headerName: "이미지 경로",
			hide: true
		},
		{
			field: "opt",
			headerName: "품목",
			width: 80
		},
		{
			field: "image",
			headerName: "이미지",
			cellRenderer: (params) => `<img style="display:block; width: 100%; max-width: 30px; margin: 0 auto;" src="${params.data.image}">`
		},
		{
			field: "prd_cd",
			headerName: "원부자재코드일련",
			width: 140,
		},
		{
			field: "color",
			headerName: "칼라",
			cellRenderer: (params) => params.data.color.split(':')[1],
			width: 80
		},
		{
			field: "size",
			headerName: "사이즈",
			cellRenderer: (params) => params.data.size.split(':')[1],
			width: 80
		},
		{
			field: "prd_nm",
			headerName: "원부자재명",
			width: 100
		},
		// {
		// 	field: "prd_nm_eng",
		// 	headerName: "원부자재명(영문)",
		// 	width: 110
		// },
		{
			field: "seq",
			headerName: "순서",
			width: 50
		},
		{
			field: "tag_price",
			headerName: "Tag가",
			type: 'currencyType',
			width: 80
		},
		{
			field: "price",
			headerName: "판매가",
			type: 'currencyType',
			width: 80
		},
		{
			field: "wonga",
			headerName: "원가",
			type: 'currencyType',
			width: 80
		},
		{
			field: "year",
			headerName: "년도",
			width: 80
		},
		{
			field: "season",
			headerName: "시즌",
			width: 80
		},
		{
			field: "gender",
			headerName: "성별",
			width: 80
		},
		{
			field: "item",
			headerName: "아이템",
			width: 80
		},
		{
			field: "sup_com",
			headerName: "원부자재업체",
			width: 120
		},
		{
			field: "unit",
			headerName: "단위",
			width: 120
		},
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(647);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
	});

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

	//성별에 따라 사이즈 값 다르게 출력
	function change_gender() {
		let gender = $('#gender option:selected').val();
		
		$.ajax({
			method: 'post',
			url: '/store/product/prd03/change-gender',
			data: {
				gender : gender
			},
			success: function (res) {
				if(res.code == 200) {
					$('#size').empty();

					let option = '';
					let sel =''
					for(let i = 0; i < res.result.length;i++) {
						sel = "<option value=''>선택</option>"
						option += '<option value='+ res.result[i].code_id +'>' + res.result[i].code_id + ' : ' + res.result[i].code_val+ '</option>';
					}
					$('#size').append(sel);
					$('#size').append(option);
					
				}
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});
	}

	const addRow = (row) => {
    	gx.gridOptions.api.applyTransaction({add : [{...row}]});
		const count = gx.gridOptions.api.getDisplayedRowCount();
		$('#gd-total').html(count);
    };

	const deleteRows = () => {
		const rows = gx.getSelectedRows();
		if (Array.isArray(rows) && !(rows.length > 0)) {
			alert('선택된 항목이 없습니다.')
			return false;
		} else {
			if (!confirm("선택한 상품을 저장 목록에서 삭제 하시겠습니까?")) return false;
			rows.map(row => { 
				added_rows.splice(row.idx, 1)
				gx.gridOptions.api.applyTransaction({remove : [row]}); 
			});
			const count = gx.gridOptions.api.getDisplayedRowCount();
			$('#gd-total').html(count);
		};
	};

	let added_base64_image = "";
	
	let added_rows = [];
	const add = async() => {
		
		if (!validation()) return;
		const response = await axios({ url: `/store/product/prd03/get-seq`, method: 'post',
			data: {
				type: document.f1.type.value,
				year: document.f1.year.value,
				season: document.f1.season.value,
				item: document.f1.item.value,
				opt: document.f1.opt.value
			}
		});

		const { seq, code } = response.data;
		if (code == 200) {

			const idx = added_rows.length;

			const brand = document.f1.type.value;
			let type = "";
			if (brand == "SM") { 
				type = "S";
			} else if (brand == "PR") {
				type = "G";
			}
			
			const no_color_size_prd_cd = 
				brand
				+ document.f1.year.value
				+ document.f1.season.value
				+ document.f1.gender.value
				+ document.f1.item.value
				+ seq
				+ document.f1.opt.value
			;
			const prd_cd = no_color_size_prd_cd + document.f1.color.value + document.f1.size.value;

			added_rows.push({
				idx: idx,
				brand: brand,
				type: type,
				year: document.f1.year.value,
				season: document.f1.season.value,
				gender: document.f1.gender.value,
				item: document.f1.item.value,
				image: added_base64_image,
				opt: document.f1.opt.value,
				color: document.f1.color.value,
				size: document.f1.size.value,
				prd_nm: document.f1.prd_nm.value,
				// prd_nm_eng: document.f1.prd_nm_eng.value,
				sup_com: document.f1.sup_com.value,
				unit: document.f1.unit.value,
				year: document.f1.year.value,
				prd_cd: prd_cd,
				seq: seq,
				tag_price : document.f1.tag_price.value,
				price: document.f1.price.value,
				wonga: document.f1.wonga.value
			});

			let rows = {
				idx: idx,
				type: document.f1.type[document.f1.type.selectedIndex].text,
				year: document.f1.year[document.f1.year.selectedIndex].text,
				season: document.f1.season[document.f1.season.selectedIndex].text,
				gender: document.f1.gender[document.f1.gender.selectedIndex].text,
				item: document.f1.item[document.f1.item.selectedIndex].text,
				image: added_base64_image,
				opt: document.f1.opt[document.f1.opt.selectedIndex].text,
				color: document.f1.color[document.f1.color.selectedIndex].text,
				size: document.f1.size[document.f1.size.selectedIndex].text,
				prd_nm: document.f1.prd_nm.value,
				// prd_nm_eng: document.f1.prd_nm_eng.value,
				sup_com: document.f1.sup_com[document.f1.sup_com.selectedIndex].text,
				unit: document.f1.unit[document.f1.unit.selectedIndex].text,
				year: document.f1.year[document.f1.year.selectedIndex].text,
				prd_cd: no_color_size_prd_cd,
				seq: seq,
				tag_price : document.f1.tag_price.value,
				price: document.f1.price.value,
				wonga: document.f1.wonga.value
			};

			addRow(rows);
			
		}

	};

	const validation = (cmd) => {
		// 구분 선택 여부
		if (f1.type.selectedIndex == 0) {
			f1.type.focus();
			return alert("구분을 선택해주세요.");
		}

		// 년도 선택여부
		if (f1.year.selectedIndex == 0) {
			f1.year.focus();
			return alert("년도를 선택해주세요.");
		}

		// 시즌 선택여부
		if (f1.season.selectedIndex == 0) {
			f1.season.focus();
			return alert("시즌을 선택해주세요.");
		}

		// 성별 선택여부
		if (f1.gender.selectedIndex == 0) {
			f1.gender.focus();
			return alert("성별을 선택해주세요.");
		}

		// 아이템 선택여부
		if (f1.item.selectedIndex == 0) {
			f1.item.focus();
			return alert("아이템을 선택해주세요.");
		}

		// 품목 선택여부
		if (f1.opt.selectedIndex == 0) {
			f1.opt.focus();
			return alert("품목을 선택해주세요.");
		}

		// 칼라 선택여부
		if (f1.color.selectedIndex == 0) {
			f1.color.focus();
			return alert("칼라를 선택해주세요.");
		}

		// 사이즈 선택여부
		if (f1.size.selectedIndex == 0) {
			f1.size.focus();
			return alert("사이즈를 선택해주세요.");
		}

		// 원부자재명 입력여부
		if (f1.prd_nm.value.trim() === '') {
			f1.prd_nm.focus();
			return alert("원부자재명을 입력해주세요.");
		}

		// 원부자재명(영문) 입력여부
		// if (f1.prd_nm_eng.value.trim() === '') {
		// 	f1.prd_nm_eng.focus();
		// 	return alert("원부자재명(영문)을 입력해주세요.");
		// }

		// 원부자재 업체 입력여부
		if (f1.sup_com.value.trim() === '') {
			f1.sup_com.focus();
			return alert("원부자재 업체를 선택해주세요.");
		}

		// Tag가 입력여부
		if (f1.tag_price.value.trim() === '') {
			f1.tag_price.focus();
			return alert("Tag가를 입력해주세요.");
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

	function save() {
		let rows = added_rows;
		if (rows.length < 1) return alert("저장 목록에 정보를 입력해주세요.");

		axios({
			url: '/store/product/prd03/create',
			method: 'post',
			data: {
				data: rows,
			},
		}).then(function(res) {
			if (res.data.code === 200) {
				alert("저장이 완료되었습니다.");
				opener.location.reload();
    			window.close();
			} else if (res.data.code === -1) {
				const prd_cd = res.data.prd_cd;
				alert(`${prd_cd}는 중복되었거나 이미 존재하는 상품 코드입니다.\n중복을 제거하거나 상품 코드를 재확인 후 다시 시도해주세요.`);
			} else {
				console.log(res.data);
				alert("일괄 저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function(err) {
			console.log(err);
		});
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

		if (confirm("선택한 사진을 삭제하시겠습니까?")) {
			$.ajax({
				method: 'post',
				url: '/store/product/prd03/del_img',
				data: {
					data_img: prd_cd,
					seq: seq
				},
				success: function(data) {
					if (data.code == '200') {
						for (let i = 0; i < img_show.length; i++) {
							if (img_show[i].dataset.img == seq) {
								img_show[i].remove();
								break;
							}
						}
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