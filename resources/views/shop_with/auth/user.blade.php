@extends('shop_with.layouts.layout')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">회원정보</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 회원정보  - {{ $id }}</span>
                </div>
            </div>
        </div>
        <!-- FAQ 세부 정보 -->
        <form name="detail">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-header mb-0">
                        <a href="#">회원정보 상세</a>
                    </div>
                    <div class="card-body mt-1">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="94px">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>아이디</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-25" name='id' id="id" value='{{@$user->id}}' readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>비밀번호</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='password' class="form-control form-control-sm w-25" name='passwd' id="passwd" value=''>
                                                        <div class="custom-control custom-checkbox form-check-box ml-2">
                                                            <input type="checkbox" class="custom-control-input" value="Y" name="passwd_chg" id="passwd_chg">
                                                            <label class="custom-control-label" for="passwd_chg">비밀번호변경 시 체크해 주십시오(* 비밀번호는 6~12자 영문과 숫자가 조합되어야 합니다.)</label>
                                                        </div>
                                                    </div>
                                                    <div class="txt_box mt-1">
                                                    * {{@$user->pwchgperiod}}일 주기로 변경, 최근변경일 : {{@$user->pwchgdate}}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이름</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-25" name='name' id="name" value='{{@$user->name}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>부서</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-25" name='part' id="part" value='{{@$user->part}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>직책</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-25" name='posi' id="posi" value='{{@$user->posi}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>연락처/내선</th>
                                                <td>
                                                     <div class="form-inline">
                                                        <input type='text' class="form-control form-control-sm w-25" name='tel' id="tel" value='{{@$user->tel}}'>
                                                        <span class="text_line">/</span>
                                                        <input type='text' class="form-control form-control-sm w-25" name='exttel' id="exttel" value='{{@$user->exttel}}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>이메일</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm w-25" name='email' id="email" value='{{@$user->email}}'>
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
            </div>
        </form>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="javascript:Save();" class="btn btn-sm btn-primary submit-btn">저장</a>
            <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
        </div>
    </div>
    <script>

        let id = '{{ $id  }}';

        /**
         * @return {boolean}
         */
        function Save() {
            const reg = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,12}$/;

            if ($('#name').val() === '') {
                $('#name').focus();
                alert('이름을 입력해 주세요.');
                return false;
            }

            if ($('#passwd_chg').is(":checked") === true) {
                if ($('#passwd').val() === '') {
                    $('#passwd').focus();
                    alert('비밀번호를 입력해 주세요.');
                    return false;
                }
            }

            if ($('#passwd').val() !== '') {
                if ($('#passwd_chg').is(":checked") === false) {
                    alert('비밀번호변경을 체크해 주세요.');
                    return false;
                }
            }

            if ($('#passwd_chg').is(":checked") === true && $('#passwd').val() !== '') {
                if (!reg.test($("#passwd").val())) {
                    $("#passwd").val('');
                    $('#passwd').focus();
                    alert('비밀번호는 6~12자 영문과 숫자가 조합되어야 합니다.');
                    return false;
                }
            }

            // if ($('#part').val() === '') {
            //     $('#part').focus();
            //     alert('부서를 입력해 주세요.');
            //     return false;
            // }

            // if ($('#posi').val() === '') {
            //     $('#posi').focus();
            //     alert('직책을 입력해 주세요.');
            //     return false;
            // }

            // if ($('#email').val() === '') {
            //     $('#email').focus();
            //     alert('이메일을 입력해 주세요.');
            //     return false;
            // }

            if(!confirm('저장하시겠습니까?')){
                return false;
            }

            var frm = $("form[name=detail]")[0];
            var formData = new FormData(frm);

            $.ajax({
                method: 'post',
                url: '/shop/user/store',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if(res.code == '200'){
                        alert("정상적으로 저장 되었습니다.");
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                        console.log(res.msg)
                    }
                },
                error: function(e) {
                    console.log(e.responseText)
                }
            });
            return true;
        }
    </script>
@endsection
