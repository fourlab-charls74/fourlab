@extends('head_with.layouts.layout-nav')
@section('title','상품이미지 일괄등록')
@section('content')

<style>
    .help-list li::before {
        content: "-";
        margin-right: 10px;
    }
    .result-count strong {
        font-weight: bold;
    }
    .grid-input-box {
        display: flex;
        align-items: center;
        height: 100%;
    }
    .grid-input-box input {
        padding: 0 5px;
        height: 30px;
        outline: none;
    }
    .grid-input-box a {
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
    }
</style>

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품이미지 일괄등록</h3>
        </div>
    </div>
    <div class="card_wrap">
        <div class="card shadow">
            <div class="card-header">
                <a href="javascript:void(0)">등록 정보</a>
            </div>
            <div class="card-body pt-3">
                {{-- 등록 정보 --}}
                <form id="f1">
                    <div class="table-box-ty2 mobile">
                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                            <tbody>
                                <tr>
                                    <th>유형</th>
                                    <td>
                                        <div class="form-inline form-radio-box">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="img_type" id="img_type_list" value="a" class="custom-control-input" checked />
                                                <label class="custom-control-label" for="img_type_list">목록이미지</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" name="img_type" id="img_type_detail" value="d" class="custom-control-input" />
                                                <label class="custom-control-label" for="img_type_detail">상세이미지</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="detail_desc_tr" class="d-none">
                                    <th>상세설명</th>
                                    <td>
                                        <div class="area_box edit_box">
                                            <textarea name="goods_cont" id="goods_cont" class="form-control editor1"><p><img id="prd_detail_img" src='/handle/editor/sample_img.jpg' alt='sample' /></p></textarea>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>업체</th>
                                    <td>
                                        <div class="input_box wd300">
                                            <div class="form-inline inline_btn_box">
                                                <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm btn-select-company" style="width:70%;background-color:white;" readonly />
                                                <input type="text" name="com_id" id="com_id" class="form-control form-control-sm btn-select-company ml-1" style="width:28%;" readonly />
                                                <a href="#" class="btn btn-sm btn-outline-primary btn-select-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>정렬</th>
                                    <td>
                                        <div class="form-inline-inner input_box wd300">
                                            <select name="sort" class="form-control form-control-sm">
                                                <option value="" selected>선택순</option>
                                                <option value="style_no">스타일넘버</option>
                                                <option value="goods_no">상품번호</option>
                                                <option value="goods_nm">상품명</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
                {{-- 등록 정보 end --}}

                {{-- Help --}}
                <div class="help-area mt-4 mb-4">
                        <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
                    <ul class="help-list">
                        <li>'파일추가' 버튼을 이용하여 상품이미지를 선택하여 주십시오. 파일선택 박스에서 <strong style="font-weight: bold;">동시에 여러개의 이미지를 선택</strong>하실 수 있습니다.</li>
                        <li>상품이미지를 추가한 후 상품번호 또는 스타일넘버를 입력해 주십시오. <strong style="color:red;">업체를 선택한 후 상품 이미지의 이름을 스타일넘버로 작성하시면 자동으로 상품을 매칭</strong>하실 수 있습니다.</li>
                        <li>예&#41; BA2332462.jpg, BA2332462_a_500.jpg</li>
                        <li>매칭된 상품이미지를 '이미지업로드' 버튼을 클릭하여 업로드하실 수 있습니다.</li>
                        <li>상세이미지의 경우 'Sample Image' 이미지가 등록하신 각 이미지로 대체됩니다.</li>
                    </ul>
                </div>
                {{-- Help end --}}

                {{-- 이미지 목록 --}}
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <p class="fs-14 result-count">총 <strong id="total-count">0</strong>개 &#40;일치 <strong id="right-count">0</strong> / 불일치 <strong id="wrong-count">0</strong>&#41;</p>
                    <div class="d-flex">
                        <input type="file" class="d-none" id="img-file" multiple />
                        <label for="img-file" class="btn btn-sm btn-outline-primary shadow-sm mr-1 mb-0" onclick="">파일추가</label>
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm mr-1" onclick="delImageFile()">삭제</button>
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm mr-1" onclick="">슬라이더</button>
                        <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="">이미지업로드</button>
                    </div>
                </div>
                <div id="div-gd" class="ag-theme-balham"></div>
                {{-- 이미지 목록 end --}}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    let ed, gx;
    let list = [];

    $(function() {
        setElementEvent();
        setEditor();
        setImageGrid();

        // test
        // list = [
        //     {idx: 1, file: undefined, width: 0, height: 0, volume: 0},
        //     {idx: 2, file: undefined, width: 0, height: 0, volume: 0},
        //     {idx: 3, file: undefined, width: 0, height: 0, volume: 0},
        //     {idx: 4, file: undefined, width: 0, height: 0, volume: 0},
        //     {idx: 5, file: undefined, width: 0, height: 0, volume: 0},
        // ];
        // gx.addRows(list.map(item => ({
        //     idx: item.idx,
        //     img: item.file,
        //     size: item.width + " X " + item.height,
        //     volume: item.volume + " KB",
        // })));
    });

    // element 이벤트 관리
    function setElementEvent() {
        // 업체 선택 팝업 오픈
        $(".btn-select-company").click(function() {
            SelectCompany();
        });
        // 이미지 유형 변경 시 상세설명 입력박스 세팅
        $("[name=img_type]").on("change", function() {
            if (this.value === 'd') $("#detail_desc_tr").removeClass("d-none");
            else $("#detail_desc_tr").addClass("d-none");
        })
        // 이미지파일 추가 시 목록에 세팅
        $("#img-file").on("change", function(e) {
            pushImageFile(e.target.files)
        })
    }

    // 업체 검색 팝업 설정
    function SelectCompany() {
        searchCompany.Open(function(com_cd, com_nm, com_type, baesong_kind, baesong_info, margin_type, dlv_amt) {
            $('#com_id').val(com_cd);
            $('#com_nm').val(com_nm);
        });
    }

    // editor 설정
    function setEditor() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']]
            , ['color', ['color']]
            , ['para', ['paragraph']]
            , ['insert', ['picture', 'video']]
            , ['emoji', ['emoji']]
            , ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 150
            , height: 150
            , dialogsInBody: true
            , disableDragAndDrop: false
            , toolbar: editorToolbar
            , imageupload: {
                dir: '/data/head/bulk_reg_img'
                , maxWidth: 1280
                , maxSize: 10
            }
        }
        ed = new HDEditor('.editor1', editorOptions, true);
    }

    // 이미지목록 설정
    function setImageGrid() {
        let columns = [
            {field: "idx", headerName: '#', type: 'numberType', width: 40, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 32, sort: null},
            {field: "img", headerName: "이미지", width:60, cellStyle: {"line-height": "40px"},             
                cellRenderer: function(p) {
                    if (p.value !== undefined) {
                        return `<span class="d-flex justify-content-center align-items-center" style='height:100%;'><img src="${URL.createObjectURL(p.value)}" alt="" style="width:100%;" /></span>`;
                    }
            }},
            {field: "size", headerName: '사이즈', width: 80, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "volume", headerName: '크기', width: 80, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "goods_no", headerName: "상품번호", width: 200,
                cellRenderer: function(p) {
                    return `<div class="grid-input-box"><input type="text" /><a href="#" class="btn btn-sm btn-secondary btn-select-goodsno ml-1"><i class="bx bx-dots-horizontal-rounded fs-12"></i></a></div>`;
                }
            },
            {field: "style_no", headerName: "스타일넘버", width: 200,
                cellRenderer: function(p) {
                    return `<div class="grid-input-box"><input type="text" /><a href="#" class="btn btn-sm btn-secondary btn-select-styleno ml-1"><i class="bx bx-dots-horizontal-rounded fs-12"></i></a></div>`;
                }
            },
            {field: "goods_nm", headerName: "상품명", type: 'HeadGoodsNameType', width: 260},
        ];

        const pApp = new App('', {
            gridId: "#div-gd"
        });

        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid(0, 370);
        pApp.BindSearchEnter();
    }

    // 이미지파일 추가 시 목록에 추가
    function pushImageFile(files) {
        console.log(list);
        let uploadFiles = [];
        [...files].forEach((f, i) => {
            const reader = new FileReader();
            reader.readAsDataURL(f);
            reader.onload = function(e) {
                let img1 = new Image();
                img1.src = e.target.result;
                img1.onload = function() {
                    let count = list.length ? list[list.length - 1].idx + 1 : 0;
                    uploadFiles.push({idx: count + i,file: f, width: this.width, height: this.height, volume: (f.size / 1024).toFixed(1)});
                    if(i >= files.length - 1) {
                        gx.addRows(uploadFiles.map((item, idx) => ({
                            idx: item.idx,
                            img: item.file,
                            size: item.width + " X " + item.height,
                            volume: item.volume + " KB",
                            // style_no: item.file.name.split("_")[0],
                        })));
                        list = list.concat(uploadFiles);
                        setImageRightCount();
                    }
                    return true;
                }
            }
        });
    }

    // 이미지파일 삭제
    function delImageFile(){
        let delList = gx.getSelectedRows();
        list = list.filter(item => !delList.map(d => d.idx).includes(item.idx));
        console.log(delList);
        console.log(list);
        gx.delSelectedRows();
    }

    // 일치/불일치 카운트 세팅
    function setImageRightCount() {
        $("#total-count").text(list.length);
    }

    // 상품번호 선택
    function selectGoodsNo(){
        let url = '/head/product/prd01/choice';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

</script>

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
@stop
