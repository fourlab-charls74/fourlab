@extends('store_with.layouts.layout-nav')
@section('title', '상품코드 등록')
@section('content')

<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">상품코드 등록</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품관리</span>
				<span>/ 상품관리(코드)</span>
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
					<a href="#">상품추가 정보 입력</a>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<div class="table-box-ty2 mobile">
								<table class="table incont table-bordered" width="100%" cellspacing="0">
									<tbody>
										<tr>
											<th class="required">브랜드</th>
											<td style="width:35%;">
												<div class="flax_box">
													<select name='brand' id='brand' class="form-control form-control-sm prd_code">
														<option value=''>선택</option>
														@foreach ($brands as $brand)
														<option value='{{ $brand->br_cd }}'>{{ $brand->br_cd }} : {{ $brand->brand_nm }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">년도</th>
											<td style="width:35%;">
												<div class="flax_box">
													<select name='year' id='year' class="form-control form-control-sm prd_code">
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
													<select name='season' id='season' class="form-control form-control-sm prd_code">
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
													<select name='gender' id='gender' class="form-control form-control-sm prd_code">
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
													<select name='item' id='item' class="form-control form-control-sm prd_code">
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
													<select name='opt' id='opt' class="form-control form-control-sm prd_code">
														<option value=''>선택</option>
														@foreach ($opts as $opt)
															<option value='{{ $opt->code_id }}'>{{ $opt->code_id }} : {{ $opt->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">순서</th>
											<td>
												<div class="flax_box">
													<select name='seq' id='seq' class="form-control form-control-sm" onclick="sel_seq();" onchange="changeSelect();">
													<!-- <option value=''>선택</option> -->
													</select>
												</div>
											</td>
											<th class="required">컬러</th>
											<td>
												<div class="flax_box">
													<select name='color' id='color' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($colors as $color)
														<option value='{{ $color->code_id }}'>{{ $color->code_id }} : {{ $color->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">사이즈</th>
											<td>
												<div class="flax_box">
													<select name='size' id='size' class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($sizes as $size)
														<option value='{{ $size->code_id }}'>{{ $size->code_val }} : {{ $size->code_val2 }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">스타일넘버</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='style_no' id="style_no" value=''>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">상품명</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='prd_nm' id="prd_nm" value=''>
												</div>
											</td>
											<th class="required">상품명(영문)</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='prd_nm_eng' id="prd_nm_eng" value=''>
												</div>
											</td>
										</tr>
                                        <tr>
											<th class="required">공급업체</th>
											<td>
												<div class="flax_box">
													<select name='sup_com' id="sup_com" class="form-control form-control-sm">
														<option value=''>선택</option>
														@foreach ($sup_coms as $com)
														<option value='{{ $com->com_id }}'>{{ $com->com_id }} : {{ $com->com_nm }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th>TAG가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='tag_price' id="tag_price" value='' onkeyup="onlynum(this)">
												</div>
											</td>
										</tr>
										<tr>
											<th>판매가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='price' id="price" value='' onkeyup="onlynum(this)">
												</div>
											</td>
											<th>원가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='wonga' id="wonga" value='' onkeyup="onlynum(this)">
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
													<div id='img_show_div' data-img="{{$image->seq}}" style="display:inline-block;position:relative;width:150px;height:120px;margin:5px;z-index:1">
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
			pinned: 'left',
			sort: null,
		},
		{
			field: "brand",
			headerName: "브랜드",
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
			headerName: "코드일련",
			width: 140,
		},
		{
			field: "color",
			headerName: "컬러",
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
			headerName: "상품명",
			width: 100
		},
		{
			field: "prd_nm_eng",
			headerName: "상품명(영문)",
			width: 100
		},
		{
			field: "style_no",
			headerName: "스타일넘버",
			width: 100
		},
		{
			field: "seq",
			headerName: "순서",
			width: 50
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
			field: "tag_price",
			headerName: "tag가",
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
			headerName: "공급업체",
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
			if (!confirm("선택한 상품을 수정 목록에서 삭제 하시겠습니까?")) return false;
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
		const response = await axios({ url: `/store/product/prd02/get-seq`, method: 'post',
			data: {
				brand: document.f1.brand.value,
				year: document.f1.year.value,
				season: document.f1.season.value,
				item: document.f1.item.value,
				opt: document.f1.opt.value
			}

		});

		const { code } = response.data;
		if (code == 200) {

			const idx = added_rows.length;
			const seq = document.f1.seq.value;
			const brand = document.f1.brand.value;
			
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
				year: document.f1.year.value,
				season: document.f1.season.value,
				gender: document.f1.gender.value,
				item: document.f1.item.value,
				image: added_base64_image,
				opt: document.f1.opt.value,
				color: document.f1.color.value,
				size: document.f1.size.value,
				prd_nm: document.f1.prd_nm.value,
				prd_nm_eng: document.f1.prd_nm_eng.value,
				style_no: document.f1.style_no.value,
				sup_com: document.f1.sup_com.value,
				year: document.f1.year.value,
				prd_cd: prd_cd,
				seq: seq,
				price: document.f1.price.value,
				wonga: document.f1.wonga.value,
                tag_price: document.f1.tag_price.value
			});

			let rows = {
				idx: idx,
				brand: document.f1.brand[document.f1.brand.selectedIndex].text,
				year: document.f1.year[document.f1.year.selectedIndex].text,
				season: document.f1.season[document.f1.season.selectedIndex].text,
				gender: document.f1.gender[document.f1.gender.selectedIndex].text,
				item: document.f1.item[document.f1.item.selectedIndex].text,
				image: added_base64_image,
				opt: document.f1.opt[document.f1.opt.selectedIndex].text,
				color: document.f1.color[document.f1.color.selectedIndex].text,
				size: document.f1.size[document.f1.size.selectedIndex].text,
				prd_nm: document.f1.prd_nm.value,
				prd_nm_eng: document.f1.prd_nm_eng.value,
				style_no: document.f1.style_no.value,
				sup_com: document.f1.sup_com[document.f1.sup_com.selectedIndex].text,
				year: document.f1.year[document.f1.year.selectedIndex].text,
				prd_cd: no_color_size_prd_cd,
				seq: seq,
				price: document.f1.price.value,
				wonga: document.f1.wonga.value,
                tag_price: document.f1.tag_price.value
			};

			addRow(rows);
			
		}

	};

	const validation = (cmd) => {
		// 브랜드 선택 여부
		if (f1.brand.selectedIndex == 0) {
			f1.brand.focus();
			return alert("브랜드를 선택해주세요.");
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

		// 컬러 선택여부
		if (f1.color.selectedIndex == 0) {
			f1.color.focus();
			return alert("컬러를 선택해주세요.");
		}

		// 사이즈 선택여부
		if (f1.size.selectedIndex == 0) {
			f1.size.focus();
			return alert("사이즈를 선택해주세요.");
		}

		// 상품명 입력여부
		if (f1.prd_nm.value.trim() === '') {
			f1.prd_nm.focus();
			return alert("상품명을 입력해주세요.");
		}

		// 상품명(영문) 입력여부
		if (f1.prd_nm_eng.value.trim() === '') {
			f1.prd_nm_eng.focus();
			return alert("상품명(영문)을 입력해주세요.");
		}

		// 스타일넘버 입력여부
		if (f1.style_no.value.trim() === '') {
			f1.style_no.focus();
			return alert("스타일넘버를 입력해주세요.");
		}

		return true;
	}

	function save() {
		let rows = added_rows;
		if (rows.length < 1) return alert("저장 목록에 정보를 입력해주세요.");
		let sel_rows = gx.getSelectedRows();
		console.log(sel_rows);

		axios({
			url: '/store/product/prd02/save_product',
			method: 'post',
			data: {
				data: rows,
				sel_data: sel_rows,
			},
		}).then(function(res) {
			if (res.data.code === 200) {
				alert("저장이 완료되었습니다.");
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
			'width:150px;height:120px;margin:5px;z-index:1';
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
				url: '/store/product/prd02/del_img',
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

	//순서 선택
	let count = 0;
	function sel_seq() {
		count++;
		let brand = document.getElementById('brand').value;
		let year = document.getElementById('year').value;
		let season = document.getElementById('season').value;
		let gender = document.getElementById('gender').value;
		let item = document.getElementById('item').value;
		let opt = document.getElementById('opt').value;

		let prd_cd = brand+year+season+gender+item;

		if (brand == "") {
			$('#brand').focus();
			alert('브랜드를 선택해주세요.');
		} else if (year == "") {
			$('#year').focus();
			alert('년도를 선택해주세요.');
		} else if (season == "") {
			$('#season').focus();
			alert('시즌을 선택해주세요.');
		} else if (gender == "") {
			$('#gender').focus();
			alert('성별을 선택해주세요.');
		} else if (item == "") {
			$('#item').focus();
			alert('아이템을 선택해주세요.');
		} else if (opt == "") {
			$('#opt').focus();
			alert('품목을 선택해주세요.');
		}
			$.ajax({
					method: 'post',
					url: '/store/product/prd02/sel_seq',
					data: {
						prd_cd : prd_cd
					},
					success: function(data) {
						if (data.code == '200') {
							if (count == 1) {
								if (data.result == '') {
									let sel = "<option value=''>선택</option>"
									let option = "<option value='01'>01 : 신규 생성</option>"
									$('#seq').append(sel);
									$('#seq').append(option);
								} else {
									let sel = "<option value=''>선택</option>"
									$('#seq').append(sel);
									let seq;
									for(let i = 0; i < data.result.length; i++){
										let pc = data.result[i].prd_cd;
										seq = data.result[i].seq;
										let option = "";
										if (seq >= 10) {
											option = '<option value='+ seq +'>'+ seq +' : '+ data.result[i].prd_nm +'</option>'
										} else {
											option = '<option value=0'+ seq +'>0'+ seq +' : '+ data.result[i].prd_nm +'</option>'
										}
										$('#seq').append(option);
									}
									let new_save = "";
									if(seq >= 10){
										new_save = '<option value='+ (seq+1) +'>'+ (seq+1) +' : 신규 생성</option>';
									}else{
										new_save = '<option value=0'+(seq+1) +'>0'+(seq+1) +' : 신규 생성</option>';
									}
									$('#seq').append(new_save);
								}
							}
						

							$(document).on('change', '.prd_code', function(){
								$('#seq').empty();
								count = 0;
							});

							// count = 0;
						} else {
							alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
						}
					},
					error: function(res, status, error) {
						console.log(error);
					}
				});
		}

	

	
	//순서에 신규생성이 아닌 값을 클릭시 하위 목록 자동 입력 및 비활성화하는 부분
	function changeSelect() {
		let selectList = document.getElementById('seq');
		let option_text = selectList.options[selectList.selectedIndex].text;
		let prd_nm = option_text.split(' : ');

		$.ajax({
			method: 'post',
			url: '/store/product/prd02/change_seq',
			data: {
				prd_nm : prd_nm[1],
				prd_seq : prd_nm[0]
			},
			success: function(data) {
				if (data.code == '200') {
						if (prd_nm[1] != '신규 생성') {
								document.getElementById('prd_nm').value = data.result[0].prd_nm;
								document.getElementById('style_no').value = data.result[0].style_no;
								document.getElementById('tag_price').value = data.result[0].tag_price;
								document.getElementById('price').value = data.result[0].price;
								document.getElementById('wonga').value = data.result[0].wonga;
								document.getElementById('sup_com').value = data.result[0].com_id;
								
								document.getElementById('prd_nm').readOnly = true;
								document.getElementById('style_no').readOnly = true;
								document.getElementById('tag_price').readOnly = true;
								document.getElementById('price').readOnly = true;
								document.getElementById('wonga').readOnly = true;
								document.getElementById('sup_com').disabled = true;

							} else {
								document.getElementById('prd_nm').value = '';
								document.getElementById('style_no').value = '';
								document.getElementById('tag_price').value = '';
								document.getElementById('price').value = '';
								document.getElementById('wonga').value = '';
								document.getElementById('sup_com').value = '';


								document.getElementById('prd_nm').readOnly = false;
								document.getElementById('style_no').readOnly = false;
								document.getElementById('tag_price').readOnly = false;
								document.getElementById('price').readOnly = false;
								document.getElementById('wonga').readOnly = false;
								document.getElementById('sup_com').disabled = false;

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

</script>
@stop