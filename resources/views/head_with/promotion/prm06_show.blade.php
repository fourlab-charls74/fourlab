@extends('head_with.layouts.layout-nav')
@section('title','사은품')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<!-- Page Heading -->
<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">사은품</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 사은품 관리</span>
            </div>
        </div>
        <div>
        	<a href="#" id="search_sbtn" onclick="self.close();" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    @csrf
	<div class="card_wrap mb-3">
		<form name="f1" id="f1">
		<input type="hidden" name="cmd" id="cmd" value="{{ $cmd }}">
		<input type="hidden" name="in_group_nos" id="in_group_nos">
		<input type="hidden" name="gift_no" value="{{ $gift_no }}">
		<input type="hidden" name="apply_group" value= "{{ $gift_info->apply_group }}">
		<input type="hidden" name="goods"/>
        <input type="hidden" name="ex_goods"/>
		<div class="card shadow">
			<div class="card-header mb-0">
				<a href="#" class="m-0 font-weight-bold">사은품 정보</a>
			</div>
			<div class="card-body">
				<div class="row_wrap">
					<div class="row">
						<div class="col-lg-8">
							<div class="table-box mobile">
								<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
									<colgroup>
										<col width="120px">
									</colgroup>
									<tbody>
										<tr>
											<th>사은품명</th>
											<td>
												<div class="input_box">
													<input type="text" name="name" class="form-control form-control-sm search-all" value="{{ $gift_info->name }}" >
												</div>
											</td>
										</tr>
										<tr>
											<th>증정구분</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" name="kind" id="kind_w" class="custom-control-input" value="W" @if($gift_info->kind != 'P' ) checked @endif>
														<label class="custom-control-label" for="kind_w">구매금액</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="kind" id="kind_p" class="custom-control-input" value="P" @if($gift_info->kind == 'P' ) checked @endif>
														<label class="custom-control-label" for="kind_p">상품별</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>증정대상</th>
											<td>
												<div class="flax_box">
													<div class="flax_box" style="width:100%;">
														<select class="form-control form-control-sm search-all" name="chc_in_group_no" id="chc_in_group_no">
															<option value="0">선택하세요.</option>
															@foreach($gift_group_nos as $gift_group)
																<option value="{{ $gift_group->id }}" >
																{{ $gift_group->val }}
																</option>
															@endforeach
														</select>
														<div class="no-gutters row my-2" style="width:100%;">
															<div class="col-6">
																<a href="javascript:;" id="addBnt" class="btn btn-sm btn-outline-primary shadow-sm add-target-btn fs-12" style="width:calc(100% - 3px);margin-right:3px;" onclick="AddGroup();">추가</a>
															</div>
															<div class="col-6">
																<a href="javascript:;" id="delBtn" class="btn btn-sm btn-outline-primary shadow-sm del-target-btn fs-12" style="width:calc(100% - 3px);margin-left:3px;" onclick="DelGroup();">삭제</a>
															</div>
														</div>
													</div>
													<div class="flax_box" style="width:100%;">
														<select name="in_group_no" id="in_group_no" class="form-control form-control-sm search-all" style="display:block;" size="5" ></select>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>증정기간</th>
											<td>
												<div class="form-inline">
													<div class="docs-datepicker form-inline-inner input_box">
														<div class="input-group">
															<input type="text" class="form-control form-control-sm docs-date" name="fr_date" value="{{ $gift_info->fr_date }}" autocomplete="off"  disable>
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
															<input type="text" class="form-control form-control-sm docs-date" name="to_date" value="{{ $gift_info->to_date }}"  autocomplete="off">
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
											<th>적용구매금액</th>
											<td>
												<div class="flax_box">
													<div><input type="text" class="form-control form-control-sm search-all" name="apply_amt" id="apply_amt" value="{{ $gift_info->apply_amt }}" ></div>
													<span class="txt_box ml-1"> 원 이상 적용</span>
												</div>
											</td>
										</tr>
										<tr>
											<th>사은품 가격</th>
											<td>
												<div class="flax_box">
													<input type="text" class="form-control form-control-sm search-all" name="gift_price" id="gift_price" value="{{ $gift_info->gift_price }}">
												</div>
											</td>
										</tr>
										<tr>
											<th class="brbn">사은품 갯수</th>
											<td class="brbn">
												<div class="flax_box">
													<div>
													<input type="text" name="qty" id="qty" style="width:50px;" class="form-control form-control-sm search-all" 
														value="{{ $gift_info->qty ? $gift_info->qty : 0 }}" {{ $gift_info->unlimited_yn == 'Y' ? 'disabled' : null }}/></div>
													<span class="ml-1 mr-3">개</span>
													<div class="form-inline form-check-box">
														<div class="custom-control custom-checkbox">
															<input type="checkbox" name="unlimited_yn" id="unlimited_yn" class="custom-control-input" 
																{{ $gift_info->unlimited_yn == 'Y' ? 'checked' : null }}/>
															<label class="custom-control-label" for="unlimited_yn">무한재고</label>
														</div>
														<div class="custom-control custom-checkbox">
															<input type="checkbox" name="dp_soldout_yn" id="dp_soldout_yn" class="custom-control-input" 
																{{ $gift_info->dp_soldout_yn == 'Y' ? 'checked' : null }}>
															<label class="custom-control-label" for="dp_soldout_yn">품절시 출력여부</label>
														</div>
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
                        </div>
						<div class="col-lg-4">
							<div class="p-3">
								<span id="preview_logo_img" style="width:100%;border:1px solid #b3b3b3; display:block;">
									@if($gift_info->img != '' && $gift_info->img != null)
										<img style="width: 100%;" src="{{ asset($gift_info->img) }}" alt="{{$gift_info->name}}">
									@endif
								</span>
								<div class="img_file_cum_wrap mt-1 pr-0">
									<div class="custom-file">
										<input type="file" class="custom-file-input" name="file" id="file" aria-describedby="inputGroupFileAddon03">
										<label class="custom-file-label" for="file"><i class="bx bx-images font-size-16 align-middle mr-1"></i>이미지 찾아보기</label>
									</div>
								</div>
							</div>
                        </div>
                    </div>
					<div class="row">
						<div class="col-lg-12">
							<div class="table-box mobile">
								<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
									<colgroup>
										<col width="120px">
									</colgroup>
									<tbody>
										<tr>
											<th>사은품 상세설명</th>
											<td>
												<div class="input_box">
													<textarea name="contents" id="contents" class="form-control editor1" >{{ $gift_info->contents }}</textarea>
												</div>
											</td>
										</tr>
										<tr>
											<th>메모</th>
											<td>
												<div class="input_box">
													<input type="text" name="memo" id="memo" class="form-control form-control-sm search-all" value="{{ $gift_info->memo }}" >
												</div>
											</td>
										</tr>
										<tr>
											<th>지급업체</th>
											<td>
												<div class="flax_box">
													<select name="apply_com" id="apply_com" class="form-control form-control-sm">
														@foreach($apply_coms as $apply_com)
															<option value="{{ $apply_com->com_id }}"
															@if( $gift_info->apply_com == $apply_com->com_id)
															selected
															@endif
															>{{ $apply_com->com_nm }}</option>
														@endforeach
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<th>환불여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" name="refund_yn" id="refund_yn_y" class="custom-control-input" value="Y" @if($gift_info->refund_yn != "N") checked @endif>
														<label class="custom-control-label" for="refund_yn_y">환불함</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="refund_yn" id="refund_yn_n" class="custom-control-input" value="N" @if($gift_info->refund_yn == "N") checked @endif>
														<label class="custom-control-label" for="refund_yn_n">환불안함</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>사용여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" @if($gift_info->use_yn != "N") checked @endif>
														<label class="custom-control-label" for="use_yn_y">사용</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N" @if($gift_info->use_yn == "N") checked @endif>
														<label class="custom-control-label" for="use_yn_n">미사용</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>적용대상</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" name="apply_product" id="apply_product_ag" class="custom-control-input" value="AG" onclick="Search(this.value)" @if($gift_info->apply_product == "AG") checked @endif>
														<label class="custom-control-label" for="apply_product_ag">전체상품</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="apply_product" id="apply_product_sg" class="custom-control-input" value="SG" onclick="Search(this.value)" @if($gift_info->apply_product == "SG") checked @endif>
														<label class="custom-control-label" for="apply_product_sg">일부상품</label>
													</div>
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
		</form>
	</div>

	<div class="row justify-content-center mt-3">
        <div class="col text-center">
            <a href="#" class="btn btn-sm btn-primary shadow-sm submit-btn">저장</a>
            <a href="#" class="btn btn-sm btn-secondary" onclick="document.f1.reset();">취소</a>
        </div>
    </div>
</div>
<div class="container-fluid px-3">
	<div id="filter-area" class="card shadow-none mb-4 search_cum_form ty2 last-card">
		<div class="card-body shadow brtn mt-0">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold"><span id="list_name"></span> 총 : <span id="gx-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box">
						<span style="font-size: 12px; color: red;">※ 상품을 추가, 삭제한 후 반드시 저장 버튼을 클릭해야만 사은품 정보에 반영됩니다.</span>
						<a href="#" class="btn-sm btn btn-primary confirm-add-btn">상품추가</a>
						<a href="#" class="btn-sm btn btn-primary confirm-del-btn">상품삭제</a>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<script type="text/javascript" charset="utf-8">
	var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/head/prm06',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions);

		$("a").click(function(e){
			e.preventDefault();
		});

		
		$(".submit-btn").click(function(e){
			e.stopPropagation();
			const goods_array = [];
    		const ex_goods_array = [];
			const getRadioValue = (name) => $(`[name=${name}]:checked`).val();
			const getGoodsNo = (goods) => `${goods.goods_no}|${goods.goods_sub}`;
			const target = getRadioValue('apply_product') == 'SG' ? goods_array : ex_goods_array;

			gx.gridOptions.api.forEachNode(node => target.push(getGoodsNo(node.data)));

			console.log(getRadioValue('apply_product'));
			console.log(goods_array, ex_goods_array, target);

			$('[name="goods"]').val(goods_array.join('^'));
    		$('[name="ex_goods"]').val(ex_goods_array.join('^'));

			Cmder('save');

		});


		$("[name=file]").change(function(){
            target_file = this.files;
            if (validatePhoto() === false) return;
            console.log("dddd");
            var fr = new FileReader();
            appendCanvas(80, 'c_80', 'a');

            fr.onload = drawImage;
            fr.readAsDataURL(target_file[0]);
        });

		if(document.f1.apply_group.value != "") {
			var ff = document.f1;
			var obj = ff.in_group_no;
			var a_group_nos = ff.apply_group.value.split("^");
			for(i=0;i<a_group_nos.length;i++){
				obj.length++;
				for(j=0;j<ff.chc_in_group_no.length;j++){
					if(ff.chc_in_group_no.options[j].value == a_group_nos[i]){
						obj.options[obj.length-1].text = ff.chc_in_group_no.options[j].text;
					}
				}
				obj.options[obj.length-1].value = a_group_nos[i]
			}
		}

		Search("{{ $gift_info->apply_product }}");

		$("#unlimited_yn").change(() => {
			var chk = $('#unlimited_yn').is(":checked");
			chk ? $("#qty").attr('disabled', true) : $("#qty").attr('disabled', false);
		});

		$(".confirm-add-btn").click(function(){
			openChoiceGoods();
		});

		$(".confirm-del-btn").click(function(){
			var selectedRowData = gx.gridOptions.api.getSelectedRows();
			//gridOptions.api.applyTransaction({ remove: selectedRowData });

			//var selectedRows = mainGrid.gridOpts.api.getSelectedRows();
			selectedRowData.forEach( function(selectedRowData, index) {
				
				gx.gridOptions.api.applyTransaction({remove: [selectedRowData]});
			});
		});

		$('#gx-total').html(gx.gridOptions.api.getDisplayedRowCount());
    });

	var columns = [
		{headerName: '#', width:50, maxWidth: 90,type:'NumType', pinned : 'left', width: 40},
		{field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 40, sort: null},
		{headerName:"상품번호", width:120, 
			children : [
				{
					headerName : "",
					field : "goods_no",
					width:80,
					cellRenderer: (params) => {
						if (params.value) {
                    		return `<a href="https://www.netpx.co.kr/app/product/detail/${params.value}" target="_blank">${params.value}</a>`;
                		}
					}
				},
				{
					headerName : "",
					field : "goods_sub",
					width:80
				}
			]
		},
		{field:"style_no" , headerName:"스타일넘버"},
		{field:"com_nm" , headerName:"업체"},
		{field:"com_id", headerName:"com_id", hide:true},
		{field:"brand_nm" , headerName:"브랜드"},
		{field:"goods_nm", headerName:"상품명", width: 250,
			cellRenderer: (params) => {
				const goods_no = params.data.goods_no;
				return `<a href="#" onclick="openGoodsDetail(${goods_no})">${params.data.goods_nm}</a>`;
			}
		},
		{field:"sale_stat_cl" , headerName:"상태", cellStyle: {textAlign:"center"}},
		{field:"reg_dm" , headerName:"등록일시", width: 195},
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid(200);

	function Search(type) {
		var gift_no = $("[name=gift_no]").val();
		var sch_url = "";
		let data = $('form[name="f1"]').serialize();
		if(type == "SG"){
			//gift_no = "";
			$("#list_name").text("적용상품");
			sch_url = "/head/promotion/prm06/search_goods/";
		}else{
			$("#list_name").text("제외상품");
			sch_url = "/head/promotion/prm06/search_exgoods/";
		}
		gx.Request(sch_url+gift_no, data,1, (response) => {
			$('#gx-total').html(response.head.total);
		});
	}

	const openGoodsDetail = (goods_no) => {
		const url=`/head/product/prd01/${goods_no}`;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=1200");
	};

	function AddGroup(){
		var ff = document.f1;
		var choice_group = ff.chc_in_group_no.value;
		var in_group_nos = "";
		if(choice_group != "0"){
			var obj = ff.in_group_no;
			for(var i=0;i<obj.length;i++){
				if(obj.options[i].value == choice_group) return;
			}
			obj.length++;
			obj.options[obj.length-1].text = ff.chc_in_group_no.options[ff.chc_in_group_no.selectedIndex].text;
			obj.options[obj.length-1].value = choice_group;
			for(var i=0;i<obj.length;i++){
				if(in_group_nos == ""){
					in_group_nos = obj.options[i].value; 
				} else {
					in_group_nos += "^" + obj.options[i].value;
				}
			}
			document.f1.in_group_nos.value = in_group_nos;
		} else {
			alert('대상 등급을 선택해 주십시오.');
			ff.chc_in_group_no.focus();
		}
	}

	function DelGroup(){
		var ff = document.f1;
		var obj = ff.in_group_no;
		var index = obj.selectedIndex;
		var in_group_nos = "";
		if(index >= 0){
			obj.options[index] = null;
			for(var i=0;i<obj.length;i++){
				if(in_group_nos == ""){
					in_group_nos = obj.options[i].value; 
				} else {
					in_group_nos += "^" + obj.options[i].value; 
				}
			}
			document.f1.in_group_nos.value = in_group_nos;
		} else {
			alert('삭제할 등급을 선택 해 주십시오.');
			obj.focus();
		}
	}

	let target_file = null;

    function validatePhoto() {
        // console.log(target_file);
        if (target_file === null || target_file.length === 0) {
          alert("업로드할 이미지를 선택해주세요.");
          return false;
        }

        if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target_file[0].name)) {
          alert("이미지 형식이 아닙니다.");
          return false;
        }

        return true;
    
    }

    function appendCanvas(size, id, type) {
		$("#preview_logo_img>canvas").remove();
        var canvas = $("<canvas></canvas>").attr({
            id : id,
            name : id,
            width : size,
            height : size,
            style : "margin:10px",
            "data-type" : type
        });

        $("#preview_logo_img").append(canvas);
     }

	function drawImage(e) {
		$('#preview_logo_img canvas').each(function(idx){
			var size = this.width;
			var canvas = this;
			var ctx = canvas.getContext('2d');
			var image = new Image();

			image.src = e.target.result;

			image.onload = function() {
				ctx.drawImage(this, 0, 0, size, size);
			}
		});
	}


	function Cmder(cmd)
	{
		var ff = document.f1;
		if (cmd == "save") {
			if (Validate(ff)) {
				Save(cmd);
			}
		} else if (cmd == 'delcmd') {
			DelGift(cmd);
		}
	}

	function Save(cmd){
        console.log($("[name='cmd']").val());
        var f1 = $("form[name=f1]")[0];
        var content = $("textarea[name=contents]").val();
        var img_url = "";
		var unlimited_yn = f1.querySelector('#unlimited_yn');
		var dp_soldout_yn = f1.querySelector('#dp_soldout_yn');
		unlimited_yn.value = unlimited_yn.checked ?  'Y' : 'N';
		dp_soldout_yn.value = dp_soldout_yn.checked ?  'Y' : 'N';

        var formData = new FormData(f1);

        content = content.replace(/(<([^>]+)>)/ig,"");

        $.ajax({
            type: 'post',
            url: '/head/promotion/prm06/comm',
            processData: false,
            contentType: false,
            //contentType: "application/x-www-form-urlencoded; charset=utf-8",
            data: formData,
            success: function (data) {
                var save_msg = "";
                if(data.code == "1"){
                    if(cmd == "editcmd"){
                        save_msg = "수정되었습니다.";
                    }else{
                        save_msg = "등록되었습니다.";
                    }
					alert(save_msg);
					window.opener.Search();
					location.replace('/head/promotion/prm06/'+data.gift_no);
                }else{
                    save_msg = "처리 중 오류가 발생하였습니다. 관리자에게 문의하세요.";
                }
            },
            complete:function(){
                _grid_loading = false;
            },
            error: function(request, status, error) {
                // console.log("error")
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
		
    }


	function Validate(ff)
	{
		if ($("[name=name]").val() == "")
		{
			alert("사은품명을 입력해 주세요");
			$("[name=name]").focus();
			return false;
		}

		if ($("[naem=fr_date]").val() == "")
		{
			alert("사은품 증정 기간을 입력해 주세요");
			$("[name=fr_date]").focus();
			return false;
		}

		if ($("[name=to_date]").val() == "")
		{
			alert("사은품 증정 기간을 입력해 주세요");
			$("[name=to_date]").focus();
			return false;
		}

		if ($("[name=apply_amt]").val() == "")
		{
			alert("적용구매금액 입력해 주세요");
			$("[name=apply_amt]").focus();
			return false;
		}
		
		if (!$("[name=unlimited_yn]").checked){
			if($("[name=qty]").val() == ""){
				alert("사은품 갯수를 입력해 주세요");
				$("[name=qty]").focus();
				return false;
			}
		}

		if ($("[name=apply_com]").val() == "")
		{
			alert("지급업체를 선택해 주세요");
			$("[naem=apply_com]").focus();
			return false;
		}

		return true;
	}

	var add = (row) => {
    	gx.gridOptions.api.applyTransaction({add : [row]});
    	$('#gx-total').html(gx.gridOptions.api.getDisplayedRowCount());
	};

	var goodsCallback = (row) => {
		// console.log(row);
		add(row);
	};

	var multiGoodsCallback = (rows) => {
		// console.log(rows);
		if (rows && Array.isArray(rows)) rows.map(row => add(row));
	};

	const openChoiceGoods = () => {
    	const url=`/head/api/goods/show`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1500,height=1000");
	};

</script>
@stop
