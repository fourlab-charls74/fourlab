@extends('partner_with.layouts.layout-nav')
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
                                            <textarea name="goods_cont" id="goods_cont" class="form-control editor1">***이미지영역***</textarea>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>업체</th>
                                    <td>
                                        <div class="input_box wd300">
                                            <div class="form-inline inline_btn_box">
                                                <input type="text" id="com_nm" name="com_nm" value = "{{$com_nm}}" class="form-control form-control-sm btn-select-company" style="width:70%;" readonly disabled/>
                                                <input type="text" name="com_id" id="com_id" value = "{{$com_id}}" class="form-control form-control-sm btn-select-company ml-1" style="width:28%;" readonly disabled/>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>상품파일명</th>
                                    <td>
                                        <div class="form-inline-inner input_box wd300">
                                            <select name="file_type" class="form-control form-control-sm">
                                                <option value="goods_no" selected>상품번호</option>
                                                <option value="style_no">스타일넘버</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                {{-- <tr>
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
                                </tr> --}}
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
                        <li>상품이미지를 추가한 후 상품번호 또는 스타일넘버를 입력해 주십시오.</li>
                        <li>상단 '상품파일명'에서 타입을 선택하신 후, <strong style="color:red;">선택한 타입에 해당하는 값으로 상품 이미지의 이름을 작성하여 추가하시면 자동으로 상품을 매칭</strong>하실 수 있습니다.</li>
                        <li>예&#41; &#40;상품번호 - 123456.jpg / 123456_a_700.jpg&#41;, &#40;스타일넘버 - BA2332462.jpg / BA2332462_a_500.jpg&#41;</li>
                        <li>매칭된 상품이미지를 '이미지업로드' 버튼을 클릭하여 업로드하실 수 있습니다.</li>
                        <li>목록이미지의 경우 700*700 이상의 이미지를 업로드해주세요.</li>
                        <li><strong style="color:red;">목록이미지의 경우 기본 이미지만 적용이 됩니다.</strong></li>
                        <li>상세이미지의 경우 '***이미지영역***' 텍스트가 등록하신 각 이미지로 대체됩니다.</li>
                    </ul>
                </div>
                {{-- Help end --}}

                {{-- 이미지 목록 --}}
                <div id="upload_grid">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="fs-14 result-count">총 <strong id="total-count">0</strong>개 &#40;일치 <strong id="right-count">0</strong> / 불일치 <strong id="wrong-count">0</strong>&#41;</p>
                        <div class="d-flex">
                            <input type="file" class="d-none" id="img-file" accept="image/*" multiple />
                            <label for="img-file" class="btn btn-sm btn-outline-primary shadow-sm mr-1 mb-0" onclick="clickEventForAddBtn(event)">파일추가</label>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm mr-1" onclick="delImageFile()">삭제</button>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm mr-1" onclick="openProductImageSlider()">슬라이더</button>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="uploadImages()">이미지업로드</button>
                        </div>
                    </div>
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
                {{-- 이미지 목록 end --}}

                {{-- 업로드 결과 목록 --}}
                <div id="upload_result_grid" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <p class="fs-14 result-count">총 <strong id="result-total-count">0</strong>개 &#40;성공 <strong id="result-right-count">0</strong> / 실패 <strong id="result-wrong-count">0</strong>&#41;</p>
                        <div class="d-flex">
                            {{-- <button type="button" class="btn btn-sm btn-primary shadow-sm" onclick="">재업로드</button> --}}
                        </div>
                    </div>
                    <div id="div-gd-result" class="ag-theme-balham"></div>
                </div>
                {{-- 업로드 결과 목록 end --}}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    const IMG_SIZE = 700;

    let ed, gx, gx1;
    let list = []; // 추가된 파일 리스트
    let curIdx = -1; // 현재 선택된 파일 (상품번호/스타일넘버 반환 시 사용)

    $(function() {
        setElementEvent();
        setEditor();
        setImageGrid();
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
        });
        // 이미지파일 추가 시 목록에 세팅
        $("#img-file").on("change", async function(e) {
            await pushImageFile(e.target.files);
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
                dir: '/data/head/goods_cont'
                , maxWidth: 1280
                , maxSize: 10
            }
        }
        ed = new HDEditor('.editor1', editorOptions, true);
    }

    // 이미지목록 설정
    function setImageGrid() {
        let columns = [
            {field: "id", headerName: '#', type: 'numberType', width: 40, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 32, sort: null},
            {field: "file", headerName: "이미지", width:60, cellStyle: {"line-height": "40px"},             
                cellRenderer: function(p) {
                    if (p.value !== undefined) {
                        return `<span class="d-flex justify-content-center align-items-center" style='height:100%;'><img src="${URL.createObjectURL(p.value)}" alt="" style="width:100%;" /></span>`;
                    }
            }},
            {field: "size", headerName: '사이즈', width: 80, cellStyle: {'text-align':'center', "line-height": "40px"},
                cellRenderer: function(p) {
                    return p.data.width + " X " + p.data.height;
            }},
            {field: "volume", headerName: '크기', width: 80, cellStyle: {'text-align':'center', "line-height": "40px"},
                cellRenderer: function(p) {
                    return p.data.volume + " KB";
            }},
            {field: "goods_no", headerName: "상품번호", width: 200,
                cellRenderer: function(p) {
                    return `<div class="grid-input-box"><input type="text" value="${p.data.goods_no ||''}" onkeydown="setGoodsInfoOnpressEnter(event,'${p.data.id}','goods-no')" /><a href="#" class="btn btn-sm btn-secondary btn-select-goodsno ml-1" onclick="clickSearchBtn('${p.data.id}')"><i class="bx bx-dots-horizontal-rounded fs-12"></i></a></div>`;
            }},
            {field: "style_no", headerName: "스타일넘버", width: 200,
                cellRenderer: function(p) {
                    return `<div class="grid-input-box"><input type="text" value="${p.data.style_no || ''}" onkeydown="setGoodsInfoOnpressEnter(event,'${p.data.id}','style-no')" /><a href="#" class="btn btn-sm btn-secondary btn-select-styleno ml-1" onclick="clickSearchBtn('${p.data.id}')"><i class="bx bx-dots-horizontal-rounded fs-12"></i></a></div>`;
            }},
            {field: "goods_nm", headerName: "상품명", type: 'HeadGoodsNameType', width: 260, cellStyle: {"line-height": "40px"}},
        ];

        const pApp = new App('', {
            gridId: "#div-gd"
        });

        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.ResizeGrid(0, 350);
        pApp.BindSearchEnter();
    }

    // 파일추가 버튼 클릭 이벤트
    function clickEventForAddBtn(e) {
        if($("[name=file_type]").val() === 'style_no' && $("#com_id").val() === '') {
            if(!confirm("업체를 선택하지 않았을 경우, 상품 이미지의 이름을 스타일넘버로 작성해도 정확한 상품으로 매칭되지 않을 수 있습니다.")) {   
                e.preventDefault();
            }
        }
    }

    // 이미지파일 추가 시 목록에 추가
    function pushImageFile(files) {
        let uploadFiles = [];
        let file_type = $("[name=file_type]").val();
        [...files].forEach((f, i) => {
            const reader = new FileReader();
            reader.readAsDataURL(f);
            new Promise((response) => {
                reader.onload = function(e) {
                    let img1 = new Image();
                    img1.src = e.target.result;
                    new Promise((res, rej) => {
                        img1.onload = function() {
                            let count = (list.length ? list[list.length - 1].id : 0) + 1;
                            uploadFiles = uploadFiles.concat({id: count + i,file: f, src: this.src, width: this.width, height: this.height, volume: (f.size / 1024).toFixed(1)});
                            if(i >= files.length - 1) {
                                setTimeout(() => {
                                    uploadFiles = uploadFiles.sort((a,b) => a.id - b.id);
                                    gx.addRows(uploadFiles);
                                    list = list.concat(uploadFiles);
                                    if(file_type === 'goods_no'){
                                        getGoodsInfoByGoodsNo(uploadFiles.map(item => ({goods_no: item.file ? item.file.name.split(".")[0].split("_")[0] : '', id: item.id})));
                                    } else if(file_type === 'style_no') {
                                        getGoodsinfoByStyleNo(uploadFiles.map(item => ({style_no: item.file ? item.file.name.split(".")[0].split("_")[0] : '', id: item.id})));
                                    }

                                    $("#img-file").val('');
                                }, 100);
                            }
                            return res(img1);
                        };
                        img1.onerror = e => rej(e);
                    });
                }
            })
        });
    }

    // 이미지파일 삭제
    function delImageFile(){
        let delList = gx.getSelectedRows();
        if(delList.length < 1) return alert("삭제할 파일을 선택해주세요.");
        
        list = list.filter(item => !delList.map(d => d.id).includes(item.id));
        gx.delSelectedRows();
        setImageRightCount();
    }

    // 일치/불일치 카운트 세팅
    function setImageRightCount() {
        $("#total-count").text(list.length);
        let rightCount = list.filter(item => item.goods_nm !== undefined).length;
        $("#right-count").text(rightCount);
        $("#wrong-count").text(list.length - rightCount);
    }

    // 상품번호/스타일넘버 선택 버튼 클릭
    function clickSearchBtn(id){
        curIdx = parseInt(id);
        let url = '/partner/product/prd01/choice';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }

    // 상품번호/스타일넘버 입력후 엔터클릭 시 정보 적용
    function setGoodsInfoOnpressEnter(e, id, type) {
        e.stopImmediatePropagation();
        if(e.keyCode !== 13) return;
        if(type === 'goods-no') getGoodsInfoByGoodsNo([{goods_no: e.target.value, id}]);
        else if(type === 'style-no') getGoodsinfoByStyleNo([{style_no: e.target.value, id}]);
    }

    /***************************** 상품번호로 정보 조회 관련 *****************************/

    // 선택한 상품의 상품번호로 정보 적용
    function ChoiceGoodsNo(goods_nos) {
        if(goods_nos.length > 1) return alert('상품을 한 개만 선택해주세요.');
        getGoodsInfoByGoodsNo([{goods_no: goods_nos[0], id: curIdx}]);
    }

    // 상품번호로 상품정보 조회
    function getGoodsInfoByGoodsNo(goods_arr) {
        $.ajax({
            async: true,
            type: 'get',
            url: `/partner/product/prd23/goods-info/goods-no`,
            data: { data: goods_arr },
            success: function (res) {
                if(res.code === 200){
                    if(goods_arr.length === 1 && res.body.data.failed.length == 1) {
                        clickSearchBtn(goods_arr[0].id);
                    } else {
                        let arr = res.body.data.all.filter(item => !res.body.data.failed.includes(fid => fid === item.id)).map(item => item || {});
                        list = list.map(item => {
                            if(arr.map(a => parseInt(a.id)).includes(item.id)) {
                                const c = arr.find(a => parseInt(a.id) === item.id);
                                const { goods_no, goods_sub, goods_nm, style_no } = c;
                                return ({...item, goods_no, goods_sub, style_no, goods_nm });
                            } else{
                                return item;
                            }
                        });
                        gx.gridOptions.api.forEachNode((rowNode, i) => {
                            if(arr.map(a => parseInt(a.id)).includes(rowNode.data.id)) {
                                rowNode.setData(list.find((item) => item.id === rowNode.data.id));
                            }
                        });
                        setImageRightCount();
                    }
                } else {
                    console.log(res);
                }
            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    /***************************** 스타일넘버로 정보 조회 관련 *****************************/

    // 스타일넘버로 상품정보 조회
    function getGoodsinfoByStyleNo(style_arr) {
        const com_id = $("#com_id").val();
        $.ajax({
            async: true,
            type: 'get',
            url: `/partner/product/prd23/goods-info/style-no`,
            data: { data: style_arr, com_id },
            success: function (res) {
                if(res.code === 200){
                    if(style_arr.length === 1 && res.body.data.failed.length == 1) {
                        clickSearchBtn(style_arr[0].id);
                    } else {
                        let arr = res.body.data.all.filter(item => !res.body.data.failed.includes(fid => fid === item.id)).map(item => item || {});
                        list = list.map(item => {
                            if(arr.map(a => parseInt(a.id)).includes(item.id)) {
                                const c = arr.find(a => parseInt(a.id) === item.id);
                                const { goods_no, goods_sub, goods_nm, style_no } = c;
                                return ({...item, goods_no, goods_sub, style_no, goods_nm });
                            } else{
                                return item;
                            }
                        });
                        gx.gridOptions.api.forEachNode((rowNode, i) => {
                            if(arr.map(a => parseInt(a.id)).includes(rowNode.data.id)) {
                                rowNode.setData(list.find((item) => item.id === rowNode.data.id));
                            }
                        });
                        setImageRightCount();
                    }
                } else {
                    console.log(res);
                }
            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    /***************************** 이미지 업로드 관련 *****************************/

    // 이미지 업로드
    function uploadImages() {
        let images = gx.gridOptions.api.getSelectedRows();
        if(images.length < 1) return alert("업로드할 이미지를 선택해주세요.");

        let right_cnt = images.filter(item => item.goods_nm !== undefined).length;
        if(right_cnt !== images.length) return alert("상품과 매칭되지 않은 이미지가 존재합니다.\n선택하신 모든 이미지를 상품과 매칭한 후 업로드해주세요.");

        const type = $("[name=img_type]:checked").val();
        const sizes = [50, 62, 70, 100, 129, 55, 120, 160, 180, 270, 280, 320, 500];
        const effect = {
            "amount" : 50,
            "radius" : 0.5,
            "threshold" : 0,
            "quality" : 95,
        }; // default로 설정

        let failedIds = [];
        
        if(type === 'a') {
            failedIds = images.filter(img => img.width < IMG_SIZE || img.height < IMG_SIZE).map(img => img.id); // 사이즈로 미리 걸러진 실패 목록 리스트
            images = images.filter(img => img.width >= IMG_SIZE && img.height >= IMG_SIZE);
        }
        
        alert("이미지를 업로드하고 있습니다. 잠시만 기다려주세요.");
        
        $.ajax({
            async: true,
            type: 'put',
            url: `/partner/product/prd23/upload`,
            data: {data: images.map(item => ({...item, file: ''})), type, size: IMG_SIZE, sizes, effect, goods_cont: $("[name=goods_cont]").val()},
            success: function (res) {
                if(res.code === 200){
                    alert("이미지 업로드가 완료되었습니다. 업로드 목록을 확인해주세요.");
                    setUploadResultGrid(failedIds, res.body.success.map(s => parseInt(s.id)));
                } else {
                    console.log(res);
                }
            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    // 업로드 성공여부 grid 세팅
    function setUploadResultGrid(failedIds, successIds) {
        $("#upload_grid").css("display", "none");
        $("#upload_result_grid").css("display", "block");

        let columns1 = [
            {field: "id", headerName: '#', type: 'numberType', width: 40, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "file", headerName: "이미지", width:60, cellStyle: {"line-height": "40px"},             
                cellRenderer: function(p) {
                    if (p.value !== undefined) {
                        return `<span class="d-flex justify-content-center align-items-center" style='height:100%;'><img src="${URL.createObjectURL(p.value)}" alt="" style="width:100%;" /></span>`;
                    }
            }},
            {field: "result", headerName: "성공여부", width: 100, cellStyle: {'text-align':'center', "line-height": "40px"},
                cellRenderer: function(p) {
                    return p.value === 's' ? '<p style="color:blue;font-weight:bold;">성공</p>' : '<p style="color:red;font-weight:bold;">실패</p>'
            }},
            {field: "size", headerName: '사이즈', width: 80, cellStyle: {'text-align':'center', "line-height": "40px"},
                cellRenderer: function(p) {
                    return p.data.width + " X " + p.data.height;
            }},
            {field: "goods_no", headerName: "상품번호", width: 200, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "style_no", headerName: "스타일넘버", width: 200, cellStyle: {'text-align':'center', "line-height": "40px"}},
            {field: "goods_nm", headerName: "상품명", type: 'HeadGoodsNameType', width: 280, cellStyle: {"line-height": "40px"}},
        ];

        const pApp1 = new App('', {
            gridId: "#div-gd-result"
        });

        let gridDiv1 = document.querySelector(pApp1.options.gridId);
        gx1 = new HDGrid(gridDiv1, columns1);
        pApp1.ResizeGrid(0, 350);
        pApp1.BindSearchEnter();

        // gx.gridOptions.api.setRowData([]);
        let images = gx.gridOptions.api.getSelectedRows();
        images = images.map(item => ({...item, result: (failedIds.includes(item.id) || !successIds.includes(item.id)) ? "f" : "s"}));
        gx1.addRows(images);
        setResultRightCount(images);
    }

    // 업로드 성공/실패 여부 세팅
    function setResultRightCount(images) {
        $("#result-total-count").text(images.length);
        let rightCount = images.filter(item => item.result === 's').length;
        $("#result-right-count").text(rightCount);
        $("#result-wrong-count").text(images.length - rightCount);

    }

    /***************************** 이미지 슬라이더 관련 *****************************/

    function openProductImageSlider() {
        let goods_nos = gx.gridOptions.api.getSelectedRows().filter(img => img.goods_no);
        if(goods_nos.length < 1) return alert("상품과 일치하는 파일을 한 개 이상 선택해주세요.");

        var url = '/partner/product/prd02/slider?goods_nos=' + goods_nos.map(g => g.goods_no).join(",");
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
    }

</script>

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
@stop
