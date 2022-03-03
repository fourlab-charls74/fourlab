
//document.write('<scrip' + 't type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>');
//document.write('<scrip' + 't type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>');
//document.write('<scrip' + 't type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020072014"></script>');
//document.write('<lin' + 'k rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >');
//document.write('<lin' + 'k rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css" >');


document.emojiSource = '/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/img';

function HDEditor(id,params,is_statusbar = false){
    this.ed = self;
    this.editor = null;
    this.editor = $(id.toString());
    this.imageupload_url = '/handle/editor/imageupload.php';

    params['callbacks'] = {
        onChange: function(contents, $editable) {
            //console.log('onChange:', contents, $editable);
        }
    };

    if(typeof(params["imageupload"]) !== "undefined" && params["imageupload"] !== null ){
        this.imageupload_dir = null;
        if(typeof(params["imageupload"]["url"]) !== "undefined" && params["imageupload"]["url"] !== null ){
            this.imageupload_url = params["imageupload"]["url"];
        }
        if(typeof(params["imageupload"]["dir"]) !== "undefined" && params["imageupload"]["dir"] !== null ){
            this.imageupload_dir = params["imageupload"]["dir"];
        } else {
            console.error('image.upload.dir not defined');
        }


        if(this.imageupload_url !== null && this.imageupload_dir !== null){

            imageupload_maxwdith = 0;
            if(typeof(params["imageupload"]["maxWidth"]) !== "undefined" && params["imageupload"]["maxWidth"] !== null ){
                imageupload_maxwdith = params["imageupload"]["maxWidth"];
            }

            imageupload_maxsize = 0;
            if(typeof(params["imageupload"]["maxSize"]) !== "undefined" && params["imageupload"]["maxSize"] !== null ){
                imageupload_maxsize = params["imageupload"]["maxSize"];
            }

            imageupload_callback = null;
            if (typeof(params["imageupload"]["callback"]) === 'function' ) {
                imageupload_callback = params["imageupload"]["callback"];
            }

            imageupload_url = this.imageupload_url;
            imageupload_dir = this.imageupload_dir;
            objEditor = this.editor;

            params['callbacks']['onImageUpload'] = function(files) {
                //console.log('image uploading');
                for(let i=0; i < files.length; i++) {
                    if(imageupload_maxsize > 0 && files[i].size > imageupload_maxsize * 1024 * 1024) {
                        alert('크기가 ' + imageupload_maxsize + 'MB를 넘는 이미지는 업로드가 되지 않습니다.');
                        return false;
                    } else {
                        /*
                        var reader = new FileReader();
                        reader.readAsDataURL(files[i]);
                        reader.onload = function (e) {

                            var image = new Image();

                            image.src = e.target.result;
                            image.onload = function () {

                                var height = this.height;
                                var width = this.width;
                                console.log('width : ' + width + ',' + 'height : ' + height);
                            };
                        }

                         */

                        __HDEditorUploadFile(objEditor,imageupload_url, imageupload_dir,imageupload_maxwdith,files[i],i,files.length,imageupload_callback);
                    }
                }
            };
            params['callbacks']['onMediaDelete'] = function(target) {
                //console.log('image deleting');
                __HDEditorDeleteFile(imageupload_url,imageupload_dir,target[0].src);
            };
        }
    }
    this.editor.summernote(params);
    if(is_statusbar === false){
        $('.note-statusbar').hide();
    }
}

HDEditor.prototype.html = function(){
    return this.editor.summernote('code');
}

HDEditor.prototype.focus = function(){
    return this.editor.summernote('focus');
}

HDEditor.prototype.onfocus = function(func){
    this.editor.on('summernote.focus', func);
}

HDEditor.prototype.onblur = function(func){
    this.editor.on('summernote.blur', func);
}

HDEditor.prototype.set = function(name,value){
    this.editor.summernote(name,value);
}

function __HDEditorUploadFile(editor,upload_url,upload_dir,upload_maxwidth,file,file_index,file_len,callback) {
    data = new FormData();
    data.append("c", 'add');
    data.append("img_dir", upload_dir);
    if(upload_maxwidth > 0){
        data.append("maxwidth", upload_maxwidth);
    }
    data.append("file", file);
    $.ajax({
        async:true,
        data: data,
        type: "POST",
        url: upload_url,
        dataType:"json",
        cache: false,
        contentType: false,
        processData: false,
        success: function(image, status, xhr) {
            if(image.errmsg !== null) {

                console.log(image);
                //console.log("url :" + url);
                //console.log("url :" + file_index);
                //console.log("url :" + file_len);

                var width = ' style="width: auto;" ';
                if((file_index + 1) == file_len){
                    if(image.width > upload_maxwidth){
                        width = ' style="width: 100%;" ';
                    }
                    var img = '<p><img src="' + image.url + '"' + width + '><p/><p><br/><p/>';
                    console.log(img);
                    editor.summernote('pasteHTML', img);
                } else {
                    var img = '<p><img src="' + image.url + '"' + width + '><p/>';
                    console.log(img);
                    editor.summernote('pasteHTML', img);
                }
                callback?.(image);
            } else {
                console.log(image.errmsg);
            }
        },
        error: function (request, status, error) {
            console.error(request.responseText);
        }
    });
}

function __HDEditorDeleteFile(upload_url,upload_dir,img_url) {
    $.ajax({
        data: {'c':'del','img_dir':upload_dir,'img_url':img_url},
        type: "POST",
        url: upload_url,
        dataType:"json",
        cache: false,
    }).done(function(image){
        if(image.errmsg !== null) {
            console.log(msg);
        } else {
            console.log(msg);
        }
    });
}

