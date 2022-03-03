@extends('head_with.layouts.layout')
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
                                                        <input type='text' class="form-control form-control-sm w-25" name='id' id="id" value='{{@$user->id}}'>
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
                                            <tr>
                                                <th>IP</th>
                                                <td>
                                                    <div class="flax_box">
                                                        @if (@$user->iptype == "A")
                                                            모두
                                                        @else
                                                            {{@$user->ipfrom}} ~ {{@$user->ipto}}
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사진</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="hidden" id="file_url" name="file_url">
                                                        <ul style="padding:0; list-style:none; margin:0; list-style-type:none;">
                                                            <li>
                                                            <div id="profile_img" style="width:100px; height:100px; border:1px solid #b3b3b3; display:block;">
                                                                <img src="{{@$user->profile_img}}" alt="" style="width:80px;height:80px;margin:10px">
                                                            </div>
                                                            </li>
                                                            <li style="padding-top:5px;">
                                                                <input type="file" id="file" name="file">
                                                            </li>
                                                        </ul>
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

            if ($('#name').val() === '') {
                $('#name').focus();
                alert('이름을 입력해 주세요.');
                return false;
            }

            if ($('#part').val() === '') {
                $('#part').focus();
                alert('부서를 입력해 주세요.');
                return false;
            }

            if ($('#posi').val() === '') {
                $('#posi').focus();
                alert('직책을 입력해 주세요.');
                return false;
            }

            if ($('#email').val() === '') {
                $('#email').focus();
                alert('이메일을 입력해 주세요.');
                return false;
            }

            if(!confirm('저장하시겠습니까?')){
                return false;
            }


            var frm = $("form[name=detail]")[0];
            var formData = new FormData(frm);

            console.log(formData);



/*            $.ajax({
                type: 'post',
                url: '/head/standard/std03/Command',
                processData: false,
                contentType: false,
                //contentType: "application/x-www-form-urlencoded; charset=utf-8",
                data: formData,
                success: function (data) {
                    console.log(data);
                    var save_msg = "";
                    if(data.brand_result == "200"){
                        if(cmd == "editcmd"){
                            save_msg = "수정되었습니다.";
                        }else{
                            save_msg = "등록되었습니다.";
                        }
                    }else{
                        save_msg = "처리 중 오류가 발생하였습니다. 관리자에게 문의하세요.";
                    }
                    alert(save_msg);
                    Search(1);

                },
                complete:function(){
                    _grid_loading = false;
                },
                error: function(request, status, error) {
                    console.log("error")
                    //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });*/

            $.ajax({
                method: 'post',
                url: '/head/user/store',
                //data: frm.serialize(),
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    console.log(res);
                    if(res.code == '200'){
                        alert("정상적으로 저장 되었습니다.");
                        //location.reload();
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


        function validatePhoto(files) {
            console.log(files);
            if (files === null || files.length === 0) {
                alert("업로드할 이미지를 선택해주세요.");
                return false;
            }
            if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(files[0].name)) {
                alert("이미지 형식이 아닙니다.");
                return false;
            }
            return true;
        }

        function appendCanvas(size, id) {
            var canvas = $("<canvas></canvas>").attr({
                id : id,
                name : id,
                width : size,
                height : size,
                style : "margin:10px"
            });
            $("#profile_img").html(canvas);
        }

        function drawImage(e) {
            $('#profile_img canvas').each(function(idx){
                var size = this.width;
                var canvas = this;
                var ctx = canvas.getContext('2d');
                var image = new Image();

                image.src = e.target.result;

                image.onload = function() {
                    ctx.drawImage(this, 0, 0, size, size);
                    var imgURL = canvas.toDataURL('image/jpeg');
                    $('#file_url').val(imgURL);
                    //$('#file').val('');
                }
            });
        }

        function uploadImage(files){
            if (validatePhoto(files) === false) return;
            var fr = new FileReader();
            appendCanvas(80, 'c_80');
            fr.onload = drawImage;
            fr.readAsDataURL(files[0]);
        }

        $(document).ready(function() {
            $("[name=file]").change(function(){
                uploadImage(this.files);
            });
        });

    </script>
@endsection
