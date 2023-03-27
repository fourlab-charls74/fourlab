@extends('store_with.layouts.layout')
@section('title','매장 공지사항')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script>

<div class="show_layout">
    <div class="page_tit">
        @if($store_notice_type === "notice")
            <h3 class="d-inline-flex">매장 공지사항</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 매장 공지사항</span>
            </div>
        @else 
            <h3 class="d-inline-flex">VMD 게시판</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ VMD 게시판</span>
            </div>
        @endif
    </div>
    <form method="post" name="store">
        @csrf
        <input type="hidden" id= "store_notice_type" name="store_notice_type" value="{{ $store_notice_type }}">
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        @if($no > 0)
                        <button type="button" onclick="return Update('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                        <button type="button" href="#" onclick="history.back();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> 목록</button>
                        <!-- <button type="button" href="#" onclick="return Destroy('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</button> -->
                        @else
                        <button type="button" href="#" onclick="Create();return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                        <button type="button" href="#" onclick="location.href = document.referrer; " class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> 취소</button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>작성자</th>
                                        <td>
                                            <div class="txt_box">
                                                <input type='text' class="form-control form-control-sm wd100" name='name' value='{{$user->name}}' required readonly>
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td>
                                            <div class="txt_box">
                                                <input type='text' class="form-control form-control-sm wd100" name='subject' value='{{$user->subject}}' required>
                                                @error(' subject') <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td>
                                            <div>
                                                <input type="hidden" name="content" value='{{$user->content}}' />
                                                <textarea id="editor1">{{$user->content}}</textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        @if($store_notice_type === 'notice')
                                            <th>매장 선택</th>
                                            <td>
                                                @if($no > 0)
                                                <div class="form-inline inline_btn_box">
                                                    <input type='hidden' id="store_nm" name="store_nm">
                                                            @foreach($storeCode as $sc)
                                                                @if($sc->store_nm == null)
                                                                    <b style="color:red;">※전체 매장 공지입니다.</b>
                                                                @elseif($sc->check_yn == 'Y')
                                                                    <div class="store_sel" data-store='{{$sc->store_cd}}' style="border:0.5px solid gray; border-radius: 5px; margin-right:15px;margin-bottom:10px;padding-left:10px;">
                                                                        <span>{{$sc->store_nm}}
                                                                            <b style="color:blue;">[확인]</b>&nbsp;&nbsp;
                                                                        </span>
                                                                    </div>
                                                                @elseif($sc->check_yn == 'N')
                                                                    <div class="store_sel" data-store='{{$sc->store_cd}}' style="border:0.5px solid gray; border-radius: 5px; margin-right:15px;margin-bottom:10px;padding-left:10px;">
                                                                        <span>{{$sc->store_nm}}
                                                                            <b style="color:red;">[미확인]</b>&nbsp;
                                                                            <button type= "button" class= "select_store_delete" onclick="select_store_delete('{{$sc->store_cd}}','{{$sc->ns_cd}}')" style="border:none;background:none;color:#153066;padding-bottom:3px;">x</button>&nbsp;&nbsp;
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                            
                                                </div>
                                                @endif
                                                <div class="form-inline inline_btn_box">
                                                    <input type='hidden' id="store_nm" name="store_nm">
                                                    <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple ></select>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                                @if($no == "")
                                                <span style="color:red">※매장 미선택시 전체 매장 공지로 등록됩니다.</span>
                                                @endif
                                            </td>
                                        @else
                                            @if($user->attach_file_url != '')
                                                <th>파일 다운로드</th>
                                                <td>
                                                    @foreach(explode(',', $user->attach_file_url) as $file_url) 
                                                            <a href="javascript:downloadFile('{{$file_url}}')">{{explode('/', $file_url)[3]}}</a>
                                                            &nbsp;&nbsp;
                                                            <a href="javascript:deleteFile('{{$no}}', '{{$file_url}}')">X</a>
                                                            <br/>
                                                    @endforeach
                                                </td>
                                            @else
                                                <th>파일 업로드</th>
                                                <td>
                                                    <div class="form-inline inline_btn_box">
                                                        <input type = "file" name= "notice_add_file" id="notice_add_file" multiple>
                                                    </div>
                                                    <span style="color:red">※이미지(jpg, png), 엑셀(excel), ppt(pptx)만 가능합니다.</span>
                                                </td>
                                            @endif
                                        @endif
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

    let editor1;
    let formData = new FormData();

    $(document).ready(function() {
        let editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        let editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 200,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload: {
                dir: '/data/store',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        editor1 = new HDEditor('#editor1', editorOptions, true);

        //파일 업로드 로직
        $('#notice_add_file').change(() => {
            let files = $("input[name=notice_add_file]")[0].files;

            if(files.length > 5) {
                alert('첨부파일은 5개까지만 가능합니다.');
                return;
            }

            for(let i = 0; i < files.length; i++) {
                console.log(files[i].name);
                if(availableCheckExtension(files[i].name)) {
                    formData.append(`files[]`, files[i]);
                } else {
                    alert(`지원하지 않는 확장자가 존재합니다. ${String(files[i].name).split('.')[1]}`);
                }
            }
        });

    });

    function availableCheckExtension(filename) {

        const excelExtension = [
            'xlsx' ,
            'xlsm' ,
            'xltx' ,
            'xltm',
            'xls',
            'xlt' ,
            'ods' ,
            'ots' ,
            'slk' ,
            'xml', 
            'gnumeric',
            'htm' ,  
            'html',
            'csv' , 
            'tsv',        
        ];

        const pptExtension = [
            'ppt',
            'pptx'
        ];

        const imageExtension = [
            'jpg',
            'jpeg',
            'png'
        ];

        const extension = String(filename).split('.')[1];
        let extensionStr = String(extension).toLowerCase();

        if(excelExtension.indexOf(extensionStr) >= 0 || pptExtension.indexOf(extensionStr) >= 0 || imageExtension.indexOf(extensionStr) >= 0) {
            return true;
        }

        return false;
    }

    function getFileName (contentDisposition) {
        let fileName = contentDisposition
            .split(';')
            .filter(function(ele) {
                return ele.indexOf('filename') > -1
            })
            .map(function(ele) {
                return ele
                    .replace(/"/g, '')
                    .split('=')[1]
            });
        return fileName[0] ? fileName[0] : null
    }

    function downloadFile(path) {
        $.ajax({
            url: `/store/common/comm01/file/download/${path.split('/').reverse()[0]}`,
            type: 'GET',
            cache: false,
            xhrFields: {
                responseType: 'blob'
            },
        })
        .done(function (data, status, jqXhr) {
            if (!data) {
                return;
            }

            try {
                let blob = new Blob([data], { type: jqXhr.getResponseHeader('content-type') });
                //let blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                
                let fileName = getFileName(jqXhr.getResponseHeader('content-disposition'));
                fileName = decodeURI(fileName);
    
                //익스플로어
                if (window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(blob, fileName);
                } else { 
                    //그 외
                    const fileReader = new FileReader();

                    let file = fileReader.readAsBinaryString(blob);
                    let link = document.createElement('a');
                    let url = window.URL.createObjectURL(file);
                    link.href = url;
                    link.target = '_self';
                    link.download = fileName;
                    document.body.append(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);
                }
            } catch (e) {
                console.error(e);
            }
        });
    }

    function clearFormData() {
        for (const key of formData.keys()) {
            formData.delete(key);
        };
    }

    function Create() {
        const type = $('#store_notice_type').val();

        if ($('input[name="subject"]').val() === '') {
            $('input[name="subject"]').focus();
            alert('제목을 입력해 주세요.');
            return false;
        }

        if ($('#editor1').val() === '') {
            $('#editor1').focus();
            alert('내용을 입력해 주세요.');
            return false;
        }

        $('input[name="content"]').val(editor1.html());

        //form data 재설정
        formData.append('store_notice_type', $('#store_notice_type').val());
        formData.append('subject', $('input[name="subject"]').val());
        formData.append('name', $('input[name="name"]').val());
        formData.append('content', $('input[name="content"]').val());
        formData.append('store_no', $('#store_no').val() == undefined ? '' : $('#store_no').val());
        formData.append('store_nm', $('input[name="store_nm"]').val());
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            method: 'POST',
            url: '/store/common/comm01/store',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.code == '200') {
                    clearFormData();
                    alert('게시물 등록에 성공하였습니다.');
                    document.location.href = `/store/common/comm01/${type}`;
                } else {
                    clearFormData();
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                console.log(e.responseText);
                clearFormData();
            }
        });
    }

    function deleteFile(no, path) {

        if(confirm("삭제하시겠습니까?")){
            $.ajax({
                method: 'delete',
                url: `/store/common/comm01/file/delete/${no}/${path.split('/').reverse()[0]}`,
                success: function(data) {
                    if (data.code == '200') {
                        alert('파일 삭제에 성공하였습니다.');
                        location.reload();
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }

                },
                error: function(res, status, error) {
                    console.log(res.responseText);
                }
            });
        }
    }

    function Update(no) {
        let frm = $('form[name=store]');
        $('input[name="content"]').val(editor1.html());

        $.ajax({
            method: 'PUT',
            url: '/store/common/comm01/edit/' + no,
            data: frm.serialize(),
            success: function(data) {
                if (data.code == '200') {
                    clearFormData();
                    alert('게시물 수정에 성공하였습니다.');
                    location.reload();
                } else {
                    clearFormData();
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(res, status, error) {
                clearFormData();
                console.log(res.responseText);
            }
        });
    }
    
    $( ".sch-store" ).on("click", function() {
        searchStore.Open(null, "multiple");
    });
        
</script>

<script>
    
    function select_store_delete(store_cd, ns_cd){
        let ss = document.querySelectorAll(".store_sel");
       
        if(confirm("삭제하시겠습니까?")){
            $.ajax({
                method: 'post',
                url: '/store/common/comm01/del_store',
                data: {data_store : store_cd, ns_cd : ns_cd},
                success: function(data) {
                    if (data.code == '200') {
                        for(let i = 0;i<ss.length;i++){
                            if(ss[i].dataset.store == store_cd){
                                data_store = ss[i].dataset.store;
                                ss[i].remove();
                                break;
                            }
                        }
                        alert('공지매장 삭제에 성공하였습니다.');
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

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
@stop
