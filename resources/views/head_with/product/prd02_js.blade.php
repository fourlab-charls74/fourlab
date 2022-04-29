<script type="text/javascript">

    let target_file = null;

    $(document).ready(function() {
        $("#img-setting-tab [name=size]").prop('checked', true);

        $("[name=img_type]").change(function(){
            if (this.value === 'a') {
                $(".only-base").css('display', 'flex')
            } else {
                $(".only-base").css('display', 'none');
            }
        });

        $("#apply").click(function(){

            var img_type        = $('[name=img_type]:checked').val();
            var img_type_alias  = $('[name=img_type_alias]').val();
            var imgURL          = $("#img-preview").attr("src");

            uploadImage(imgURL, img_type, img_type_alias);
            // const canvas_cnt = $('#upload-tab canvas').length;
            // $('#upload-tab canvas').each(function(idx){
            //     var size = this.width;
            //     var canvas = this;
            //     var ctx = canvas.getContext('2d');
            //     var imgURL = canvas.toDataURL('image/jpeg');
            //
            //     uploadImage(size, imgURL, $(this).attr('data-type'), idx === canvas_cnt - 1);
            // });
        });

        $(document).on("dragover", function(e) {
            e.stopPropagation();
            e.preventDefault();
        });

        $(document).on('drop', function(e){

            console.log('file drop');

            e.preventDefault();
            var files = e.originalEvent.dataTransfer.files;

            if (files.length === 0) return;

            if (files.length > 1) {
                alert("파일은 1개만 올려주세요.");
                return;
            }

            target_file = files;

            if (validatePhoto() === false) {
                target_file = null;
                $('#file-label').html('이미지를 선택해주세요.');
                return;
            }
            $('#file-label').html(files[0].name);

            previewImage();

        });

        $('#file').change(function(e){
            if($("[name=img_type]:checked").val() == "f" && $("[name=img_type_alias]").prop("selectedIndex") == 0){
                alert("추가이미지 유형을 선택해 주십시오.");
                target_file = null;
                $('#file-label').html('이미지를 선택해주세요.');
                return;
            }

            if (this.files.length > 1) {
                alert("파일은 1개만 올려주세요.");
                return;
            }
            target_file = this.files;

            if (validatePhoto() === false) {
                target_file = null;
                $('#file-label').html('이미지를 선택해주세요.');
                return;
            }
            $('#file-label').html(this.files[0].name);

            previewImage();
        });
    });

    function validatePhoto() {
        if (target_file === null || target_file.length === 0) {
            alert("업로드할 이미지를 선택해주세요.");
            return false;
        }

        if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target_file[0].name)) {
            alert("이미지 형식이 아닙니다.");
            return false;
        }

        if (target_file[0].size > 10*1024*1024) {
            alert("10M 이상 파일은 업로드 하실 수 없습니다.");
            return false;
        }

        return true;
    }

    function previewImage() {
        var fr = new FileReader();
        fr.onload = drawImage;
        fr.readAsDataURL(target_file[0]);
    }

    function drawImage(e) {

        var tmpImg = new Image();

        tmpImg.onload = function(){

            var ratio = this.height / this.width;

            var img_type = $('[name=img_type]:checked').val();

            $("#upload-tab").html('<div class="p-4"><img src="" id="img-preview" width="500px" alt=""></div>');
            $("#img-preview").attr("src", this.src);

            //switch(img_type) {
            //  case 'a' :
                    getSortSizes(img_type).forEach(function(size){
                        appendCanvas(size,ratio, 'c_' + size, 'a');
                    });
            //        break;

            //    case 'f' :
            //        appendCanvas(500,ratio, 'c_500', 'f');
            //        break;
            //}

            var user_setting = $('#user-size:checked');
            var user_set_width = $('#user-size-w').val();
            if (user_setting.length > 0 && user_set_width > 0) {
                appendCanvas(user_set_width, 'u_' + user_set_width, 'u');
            }

            $('#uploadTab a[href="#upload-tab"]').tab('show');

            $('#upload-tab canvas').each(function(idx){

                var width = this.width;
                var height = this.width * ratio;

                var canvas = this;
                var ctx = canvas.getContext('2d');
                var image = new Image();

                image.src = e.target.result;

                image.onload = function() {
                    ctx.drawImage(this, 0, 0, width,height);
                }
            });

        };
        tmpImg.src = e.target.result;
    }

    function getSortSizes(img_type) {
        var sizes = [];

        $("[name=size]:checked").each(function(){
            if( img_type == "f" && ( this.value == "50" || this.value == "62" || this.value == "70" || this.value == "100" || this.value == "129" )){
                //추가이미지 생성은 기본크기만 생성
            }else{
                sizes.push(this.value);
            }
        });

        //500사이즈는 기본 사이즈
        sizes.push(500);

        //오름차순 정렬
        sizes.sort(function(a, b) {
            return a - b;
        });
        return sizes;
    }


    function appendCanvas(size,ratio, id, type) {
        var canvas = $("<canvas></canvas>").attr({
            id : id,
            name : id,
            width : size,
            height : size * ratio,
            style : "margin:10px",
            "data-type" : type
        });
        $("#upload-tab").append(canvas);
    }

    function uploadImage(url, type, type_alias) {

        if (validatePhoto() === false) {
            return false;
        }

        var goods_no = $("#goods_no").val();
        var sizes = [];

        $("[name=size]:checked").each(function(){
            sizes.push(this.value);
        });

        var effect = {
            "amount" : $("#amount").val(),
            "radius" : $("#radius").val(),
            "threshold" : $("#threshold").val(),
            "quality" : $("#quality").val(),
        };

        $.ajax({
            type: "post",
            url: '/head/product/prd02/'+goods_no+'/upload',
            contentType: "application/x-www-form-urlencoded; charset=utf-8",
            dataType: 'json',
            data: {
                img : url,
                sizes : sizes,
                img_type : type,
                img_type_alias : type_alias,
                effect : effect,
                size : '500',
                _token : $("[name=_token]").val()
            },
            success: function(res) {
                console.log(res);
                if(res.code == "200"){
                    alert('선택영역을 서버의 이미지 파일에 저장했습니다.');

                    if( res.img_type_alias != "")
                        location.href   = "/head/product/prd02/"+goods_no+"/image?img_ta=" + res.img_type_alias;
                    else
                        document.location.reload();
                } else {
                    console.log(res.msg);
                }
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });
    }


</script>
