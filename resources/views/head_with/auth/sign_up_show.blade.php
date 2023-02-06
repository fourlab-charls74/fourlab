@extends('head_with.layouts.layout-nav')
@section('title','회원가입 상세')
@section('content')
<div class="show_layout py-3 px-sm-3 container-fluid">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">회원가입</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>회원가입</span>
            </div>
        </div>
    </div>
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">기본정보</a>
                </div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="110px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>아이디</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='id' id="id" autocomplete="off" />
                                                        <input type="hidden" name="id_chk">
														<button name="id_check" class="btn btn-sm btn-primary fs-12 px-1 ml-1" style="width:75px;" onclick="checkdup();return false;">중복확인</button>
                                                    </div>
                                                    <div><span id="checkdupmessage"></span></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>비밀번호</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='password' class="form-control form-control-sm w-25" name='passwd' id="passwd" autocomplete="new-password" />
                                                    </div>
                                                    <p class="fs-12" style="color:red;" id="passwdchkmessage">* 비밀번호는 공백 없이 6~12자이며 영문과 숫자가 조합되어야 합니다.</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>비밀번호 확인</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='password' class="form-control form-control-sm w-25" name='pwchk' id="pwchk" autocomplete="new-password" />
                                                    </div>
                                                    <p class="fs-12" id="pwchkmessage"></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='name' id="name">
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
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">부가정보</a>
                </div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="110px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>부서</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-all" name='part' id='part'>
                                                    </div>
                                                </td>
                                                <th>직책</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='posi' id="posi">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>연락처/내선</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter w-25" name='tel' id="tel">
                                                        <span class="text_line p-1">/</span>
                                                        <input type='text' class="form-control form-control-sm w-25" name='exttel' id="exttel">
                                                    </div>
                                                </td>
                                                <th>메신저</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm" name='messenger' id="messenger">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이메일</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='email' id="email">
                                                    </div>
                                                </td>
                                                <th></th>
                                                <td>
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
<div class="resul_btn_wrap mt-3 d-block">
    <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn" id="signup-btn">가입</a>
    <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
</div>

<script>
    let code = '{{ $code }}';

    function Save() {
        const reg = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,12}$/;
        const regSpace = /\s/g;

        if (regSpace.test($('#id').val())) {
            $('#id').val('');
            $('#id').focus();
            alert("아이디는 공백 없이 입력해야 합니다.");
            return false;
        }

        if ($('#id').val() === '') {
            $('#id').focus();
            alert('아이디를 입력해주세요.');
            return false;
        }

        if ($("[name=id_chk]").val() === '') {
            alert('아이디 중복확인을 해주세요.');
            return false;
        }

        if ($("#passwd").val() === '') {
            $('#passwd').focus();
            alert('비밀번호를 입력해주세요.');
            return false;
        }

        if (!reg.test($("#passwd").val())) {
            $("#passwd").val('');
            $('#passwd').focus();
            alert('비밀번호는 공백 없이 6~12자 이며 영문과 숫자가 조합되어야 합니다.');
            return false;
        }

        if ($("#pwchk").val() === '' || $("#passwd").val() != $("#pwchk").val()) {
            $('#pwchk').val('');
            $('#pwchk').focus();
            alert('비밀번호를 확인해주세요');
            return false;
        }

        if ( $.trim($('#name').val()) === '' ) {
            $('#name').val('');
            $('#name').focus();
            alert('이름을 입력해주세요.');
            return false;
        }
        
        if (!confirm('가입하시겠습니까?')) {
            return false;
        }

        let frm = $('form[name="detail"]');

        if (code == "") {
            $.ajax({
                method: 'post',
                url: '/head/sign_up/store',
                data: frm.serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.code == '200') {
                        alert("가입이 완료되었습니다. 관리자 승인 후 로그인 하실 수 있습니다.");
                        self.close();
                        // opener.Search(1);
                    } else if (res.code == '501') {
                        alert('이미 등록 된 아이디입니다.');
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
        return true;
    }

    function checkdup() {
        const regSpace = /\s/g;

        if(regSpace.test($('#id').val())){
            $('#id').val('');
            $('#id').focus();
            alert("아이디는 공백 없이 입력해야 합니다.");
            return false;
        }

        if ($('#id').val() === '') {
            $('#id').focus();
            alert('아이디를 입력해주세요.');
            return false;
        }
        
        const Id = $("[name=id]").val();

		$.ajax({
            async: true,
            type: 'put',
            url: '/head/sign_up/checkid/' + Id,
            success: function(data) {
                cbcheckdup(data);
            },
            complete: function() {
                _grid_loading = false;
            },
            error: function(request, status, error) {
                alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

                console.log("error")
            }
		});
	}

	function cbcheckdup(res) {
		if (res.id_code == "1") {
			$("[name=id_chk]").val("Y");
			$("#checkdupmessage").html("<font color='blue' style='font-size:10px; letter-spacing:0px;'><b>입력하신 아이디는 등록 가능합니다.</b></font>");
		} else {
			$("[name=id_chk]").val("");
			$("#checkdupmessage").html("<font color='red' style='font-size:10px; letter-spacing:0px;'><b>입력하신 아이디는 이미 사용 중 입니다.</b></font>");
		}
	}

    $(function(){
        const reg = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,12}$/;
            
        $("#passwd").keyup(function(){
            $('#pwchkmessage').html('');
            
            if(reg.test($("#passwd").val())){
                $('#passwdchkmessage').hide();
            }
            
            if($("#passwd").val().length==0){
                $('#passwdchkmessage').show();
            }
        });

        $("#pwchk").keyup(function(){
            if($("#passwd").val() != "" || $("#pwchk").val() != ""){
                if($("#passwd").val() == $("#pwchk").val()){
                    $('#pwchkmessage').html('* 비밀번호가 일치합니다.');
                    $('#pwchkmessage').css('color', 'blue');
                }else{
                    $('#pwchkmessage').html('* 비밀번호를 똑같이 입력해주세요.');
                    $('#pwchkmessage').css('color', 'red');
                }
            }
        });
    });


    $(function(){  
        $("#id").keyup(function(){
            $("[name=id_chk]").val("");
			$("#checkdupmessage").html("");
        });
    });
</script>
@stop