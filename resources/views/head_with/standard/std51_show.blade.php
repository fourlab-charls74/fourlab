@extends('head_with.layouts.layout-nav')
@section('title','코드관리')
@section('content')

<script>
    const code_no = '{{ @$code->no }}';
</script>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">코드등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 기준정보</span>
                <span>/ 코드관리</span>
            </div>
        </div>
        <div>
            @if ($type == 'edit')
            <a href="#" class="btn btn-sm btn-primary shadow-sm edit-btn">수정</a>
            <a href="#" class="btn btn-sm btn-primary shadow-sm delete-btn">삭제</a>
            @else
            <a href="#" class="btn btn-sm btn-primary shadow-sm add-btn">등록</a>
            @endif
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>

    <style> .required:after {content:" *"; color: red;}</style>

    <form method="get" name="search">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow @if ($type != 'edit') mb-0 @endif">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">코드정보</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tr>
                                            <th class="required">코드종류</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="code_kind_cd" id="code_kind_cd" class="form-control form-control-sm" value="{{ @$code->code_kind_cd }}" @if ($type == 'edit') readonly @endif>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">코드ID</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="code_id" id="code_id" class="form-control form-control-sm" value="{{ @$code->code_id }}" @if ($type == 'edit') readonly @endif>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">코드값1</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="code_val" id="code_val" class="form-control form-control-sm" value="{{ @$code->code_val }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <th class="required">코드값2</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="code_val2" id="code_val2" class="form-control form-control-sm" value="{{ @$code->code_val2 }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <th class="required">코드값3</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="code_val3" id="code_val3" class="form-control form-control-sm" value="{{ @$code->code_val3 }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <th class="required">코드값&lbbrk;영문&rbbrk;</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="code_val_eng" id="code_val_eng" class="form-control form-control-sm" value="{{ @$code->code_val_eng }}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>사용여부</th>
                                            <td>
                                                <div class="input_box">
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" @if(@$code->use_yn == 'Y' or @$code->use_yn == '') checked @endif>
                                                            <label class="custom-control-label" for="use_yn_y">사용</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N" @if(@$code->use_yn == 'N') checked @endif>
                                                            <label class="custom-control-label" for="use_yn_n">미사용</label>
                                                        </div>
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
<<<<<<< HEAD
    </form>
</div>

<script>
    function ValidateAdd() {
        var form = document.search;

        if(form.code_kind_cd.value == "") return alert("코드종류를 입력해주세요.");
        if(form.code_id.value == "") return alert("코드ID를 입력해주세요.");
        if(form.code_val.value == "") return alert("코드값1을 입력해주세요.");
        if(form.code_val2.value == "") return alert("코드값2를 입력해주세요.");
        if(form.code_val3.value == "") return alert("코드값3을 입력해주세요.");
        if(form.code_val_eng.value == "") return alert("코드값(영문)을 입력해주세요.");

        return true;
    }

    // 등록
    if ($('.add-btn').length > 0) {
        $('.add-btn').click(function(e){
            e.preventDefault();

            if (ValidateAdd() !== true) return;

            var data = $('form[name="search"]').serialize();

            $.ajax({
                async: true,
                type: 'post',
                url: `/head/standard/std51/`,
                data: data,
                success: function (response) {
                    alert(response.message);
                    if(response.code === 200) {
                        window.opener.location.reload();
                        window.close();
=======
    </div>
    <script>

        let code = '{{ $code  }}';

        /**
         * @return {boolean}
         */
        function Save() {

            if ($('#code_kind_cd').val() === '') {
                $('#code_kind_cd').focus();
                alert('구분을 선택해주세요.');
                return false;
            }

            if ($('#code_kind_nm').val() === '') {
                $('#code_kind_nm').focus();
                alert('코드명을 입력해 주세요.');
                return false;
            }

            if ($('#code_kind_nm_eng').val() === '') {
                $('#code_kind_nm_eng').focus();
                alert('영문명을 입력해 주세요.');
                return false;
            }

            if(!confirm('저장하시겠습니까?')){
                return false;
            }

            var frm = $('form');
            //console.log(frm.serialize());

            if(code == ""){
                $.ajax({
                    method: 'post',
                    url: '/head/standard/std51',
                    data: frm.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            alert("정상적으로 저장 되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                            console.log(res.msg);
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            } else {
                $.ajax({
                    method: 'put',
                    url: '/head/standard/std51/' + code,
                    data: frm.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        // console.log(res);
                        if(res.code == '200'){
                            alert("정상적으로 변경 되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
                    }
                });
            }


            return true;
        }

        function Delete() {
            if(confirm('삭제 하시겠습니까?')){
                $.ajax({
                    method: 'delete',
                    url: '/head/standard/std51/' + code,
                    dataType: 'json',
                    success: function (res) {
                        // console.log(response);
                        if(res.code == '200'){
                            alert("삭제되었습니다.");
                            self.close();
                            opener.Search(1);
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
>>>>>>> main
                    }
                },
                error: function(request, status, error) {
                    alert(request.responseJSON.message);
                }
            });
<<<<<<< HEAD
        });
    }

    // 수정
    if ($('.edit-btn').length > 0) {
        $('.edit-btn').click(function(e){
            e.preventDefault();

            if (ValidateAdd() !== true) return;

            var data = $('form[name="search"]').serialize();

            $.ajax({
                async: true,
                type: 'post',
                url: `/head/standard/std51/` + code_no,
                data: data,
                success: function (response) {
                    alert(response.message);
                    if(response.code === 200) {
                        window.opener.location.reload();
                        window.close();
=======
            //gx.gridOptions.defaultColDef.editable = true;
        }

		/**
		 * @return {boolean}
		 */
		function DataSave(){

			data_codes = [];
			let checkRows = gx.getSelectedRows();

			checkRows.map(function(row) {
				if(row.editable === 'Y'){
					data_codes.push(row);
				}
			});

			if(data_codes.length == 0 ){
				alert('저장할 코드정보를 선택해 주십시오.');
				return false;
			}

			$.ajax({
				method: 'post',
				url: '/head/standard/std51/' + code + '/save',
				data: {data:JSON.stringify(data_codes)},
				dataType: 'json',
				success: function (res) {
					if(res.code == '200'){
						Search();
					} else {
						alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
						console.log(res.msg);
					}
				},
				error: function(e) {
					console.log(e.responseText)
				}
			});
		}

        /**
         * @return {boolean}
         */
        function DataDel(){
            let code_ids = [];
            gx.getSelectedRows().forEach((selectedRow, index) => {
                code_ids.push(selectedRow.code_id);
            });

            if(code_ids.length === 0) {
                alert('삭제할 상품을 선택 해 주십시오.');
            } else if(code_ids.length > 0 && confirm('삭제 하시겠습니까?')){

                $.ajax({
                    method: 'post',
                    url: '/head/standard/std51/' + code + '/del',
                    data: {'code_ids':code_ids},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == '200'){
                            Search();
                        } else {
                            alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        }
                    },
                    error: function(e) {
                        console.log(e.responseText)
>>>>>>> main
                    }
                },
                error: function(request, status, error) {
                    alert(request.responseJSON.message);
                }
            });
        });
    }

    // 삭제
    if ($('.delete-btn').length > 0) {
        $('.delete-btn').click(function(e){
            e.preventDefault();
            
            if(!confirm("해당 코드를 삭제하시겠습니까?")) return;

            $.ajax({
                async: true,
                type: 'delete',
                url: `/head/standard/std51/` + code_no,
                success: function (response) {
                    alert(response.message);
                    if(response.code === 200) {
                        window.opener.location.reload();
                        window.close();
                    }
                },
                error: function(request, status, error) {
                    alert(request.responseJSON.message);
                }
            });
        });
    }
</script>
@stop

