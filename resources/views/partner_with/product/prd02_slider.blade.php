
@extends('head_with.layouts.layout-nav')
@section('title','상품슬라이더')
@section('content')

<style>
    .left_card {
        flex:1;
        min-width: 330px;
    }
    .right_card {
        flex: 2;
        min-width: 630px;
    }
    .font-sm {
        font-size: 12px;
    }
    .select {
        padding: 0 10px;
        width: 80px;
        font-size: 14px;
        border-radius: 5px;
        border-color: #667b99;
        color: #667b99;
        outline: none;
    }
</style>

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">상품슬라이더</h3>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm get-prd-btn">상품선택</a>
        </div>
    </div>

    <div class="d-flex">
        <!-- 좌측 -->
        <div class="card_wrap left_card mr-3">
            <div class="card shadow">
                <div class="card-header">
                    <a href="javascript:void(0)">슬라이더 설정</a>
                </div>
                <div class="card-body pt-2">
                    <div class="card-title">
                        <div class="filter_wrap">
                            <div class="fl_box d-flex justify-content-between" style="width: 100%; align-items: center;">
                                <h6 class="mb-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm to-prev"><i class="fas fa-caret-square-left"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm to-stop"><i class="fas fa-stop-circle"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm to-next"><i class="fas fa-caret-square-right"></i></button>
                                    <select id="auto_second" class="select">
                                        <option value="0">수동</option>
                                        <option value="2">2초</option>
                                        <option value="3">3초</option>
                                        <option value="4">4초</option>
                                        <option value="5">5초</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="div-gd" style="min-height:300px;height:calc(100vh - 370px);width:100%;" class="ag-theme-balham gd-lh50 ty2"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 좌측 end -->

        <!-- 우측 -->
        <div class="card_wrap right_card">
            <div class="card shadow">
                <div class="card-header">
                    <a href="javascript:void(0)">이미지 관리 - <strong id="selected_goods_nm"></strong></a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <th>유형</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="img_type" id="base" value="a" class="custom-control-input" checked>
                                                            <label class="custom-control-label" for="base">기본 이미지</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="img_type" id="side" value="f" class="custom-control-input">
                                                            <label class="custom-control-label" for="side">측면 이미지</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>파일</th>
                                                <td>
                                                    <div class="img_file_cum_wrap">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="file" aria-describedby="inputGroupFileAddon03">
                                                            <label id="file-label" class="custom-file-label font-sm" for="file">
                                                                <i class="bx bx-images font-size-16 align-middle mr-1"></i>
                                                                이미지 찾아보기
                                                            </label>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button class="btn btn-outline-secondary" type="button" id="apply">적용</button>
                                                        </div>
                                                            <p class="mb-0 cum_stxt mt-1 font-sm">(기본이미지 : 700*700 사이즈의 이미지를 업로드 해 주십시오.)</p>
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
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#img-setting-tab" role="tab" aria-controls="img-setting-tab" aria-selected="true">크기</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#effect" role="tab" aria-controls="effect" aria-selected="false">유형</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="img-setting-tab" role="tabpanel">
                        <div class="card-body">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-box-ty2 mobile">
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                <tbody>
                                                <tr>
                                                    <th>시스템</th>
                                                    <td colspan="12">
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-50" value="50" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-50" class="custom-control-label">50 * 50</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-62" value="62" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-62" class="custom-control-label">62 * 62</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-70" value="70" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-70" class="custom-control-label">70 * 70</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-100" value="100" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-100" class="custom-control-label">100 * 100</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-129" value="129" class="custom-control-input" disabled="disabled"/>
                                                                <label for="size-129" class="custom-control-label">129 * 129</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>기본</th>
                                                    <td colspan="12">
                                                        <div class="form-inline form-radio-box flax_box txt_box">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-55" value="55" class="custom-control-input"/>
                                                                <label for="size-55" class="custom-control-label">55 * 55</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-120" value="120" class="custom-control-input"/>
                                                                <label for="size-120" class="custom-control-label">120 * 120</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-160" value="160" class="custom-control-input"/>
                                                                <label for="size-160" class="custom-control-label">160 * 160</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-180" value="180" class="custom-control-input"/>
                                                                <label for="size-180" class="custom-control-label">180 * 180</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-270" value="270" class="custom-control-input"/>
                                                                <label for="size-270" class="custom-control-label">270 * 270</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-280" value="280" class="custom-control-input"/>
                                                                <label for="size-280" class="custom-control-label">280 * 280</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="size" id="size-320" value="320" class="custom-control-input"/>
                                                                <label for="size-320" class="custom-control-label">320 * 320</label>
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
                    <div class="tab-pane fade" id="effect" role="tabpanel" aria-labelledby="effect-tab">
                        <div class="card-body">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-box-ty2 mobile">
                                            <table class="table incont table-bordered" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <th>Amount</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="amount" id="amount" value="50" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 500</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Radius</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="radius" id="radius" value="0.5" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 50</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Threshold</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="threshold" id="threshold" value="0" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 255</div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Quality</th>
                                                        <td>
                                                            <div class="flax_box">
                                                            <div class="input_box">
                                                                <input type="text" name="quality" id="quality" value="95" class="form-control form-control-sm">
                                                            </div>
                                                                <div class="txt_box ml-1">1 ~ 255</div>
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
            </div>
            <div class="card shadow">
                <div>
                    <ul class="nav nav-tabs" id="uploadTab">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#prd-list" role="tab"
                            aria-controls="prd-list" aria-selected="true" id="prd-list-tab">상품목록</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#detail" role="tab"
                            aria-controls="detail" aria-selected="false">상품상세</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#upload-tab" role="tab"
                            aria-controls="upload" aria-selected="false">업로드 미리보기</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="nav-tabContent">
                        <div class="tab-pane show active" id="prd-list" role="tabpanel" aria-labelledby="prd-list-tab">
                            <div class="prd-image-list row" id="select_prd_files">
                            </div>
                        </div>
                        <div class="tab-pane" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                            {{-- {{$goods->goods_cont}} --}}
                        </div>
                        <div class="tab-pane" id="upload-tab" role="tabpanel" aria-labelledby="upload-tab">
                            <ul>
                                <li>신규 등록 및 수정 시 업로드 하신 상품 목록 이미지를 미리 보여 줍니다.</li>
                                <li>자세한 사항은 <span class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</span>를 참조해 주십시오.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-title">
                    <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
                </div>
                <ul>
                    <li><span>"<i class="bx bx-images font-size-14 align-middle mr-0"></i> 이미지 찾아보기"</span>
                    를 클릭하여 업로드 하실 <span>"이미지 파일"</span>을 선택합니다.</li>
                    <li>'이미지 업로드' 버튼을 클릭한 후, '이미지 미리보기' 탭의 이미지가 정확한지 확인합니다.</li>
                    <li>이미지가 정확히 등록되었다면 '이미지 적용' 버튼을 클릭하여 미리보기의 상품 이미지를 해당 상품의 이미지로 적용합니다.</li>
                    <li>'등록된 상품 이미지'가 보이지 않는다면, 아직 해당 상품의 이미지가 등록되지 않은 것입니다.</li>
                </ul>
            </div>
        </div>
        <!-- 우측 end -->
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    let gx;
    let target_file = null;
    const columns = [
        {field: "goods_no", hide: true},
        {
            field: "img", 
            headerName: "이미지", 
            width: 80,
            cellRenderer: function(p) {
                return `<img src="${p.value || ''}" alt="${p.data.goods_nm}" style="height: 50px;min-height: 50px;" />`;
            }
        },
        {
            field: "goods_nm", 
            headerName: "상품명", 
            width: 400, 
            cellRenderer: function(p) {
                return `<a href="javascript:void(0)">${p.value}</a>`;
            }
        },
    ];
    const pApp = new App('', {gridId: '#div-gd'});
    const gridDiv = document.querySelector(pApp.options.gridId);

    $(document).ready(function() {
        gx = new HDGrid(gridDiv, columns, {rowSelection: 'single', suppressRowClickSelection: false, onSelectionChanged: onSelectionChanged});
        gx.gridOptions.enableRangeSelection = false;
        pApp.ResizeGrid(200);
        pApp.BindSearchEnter();

        SetSlideList();
        SetInfoBase();
    });
</script>
<script type="text/javascript" charset="utf-8">
    const goods_list = <?= json_encode(@$goods_list) ?>;
    let current_row = 0;
    let timer = null;
    const IMG_MIN_WIDTH = 700;
    const IMG_MIN_HEIGHT = 700;

    function SetSlideList() {
        gx.gridOptions.api.setRowData(goods_list);
        const firsNode = gx.gridOptions.api.getRowNode(0);
        firsNode.setSelected(true);
        SelectPrd(firsNode.data.goods_no);
    }

    function onSelectionChanged(event) {
        SelectPrd(event.api.getSelectedNodes()[0].data.goods_no);
    }

    function SelectPrd(goods_no) {
        const goods = goods_list.filter(g => g.goods_no === goods_no)[0];
        let fileHtml = '';

        goods.files.forEach(file => {
            fileHtml += `
                <figure class="p-2">
                    <figcaption>${file.size} (${file.filesize}KB)</figcaption>
                    <img src="${file.src}" alt="${file.size}">
                </figure>
            `;
        })
        if(goods.files.length < 1) fileHtml = '<span class="pl-3">등록된 상품 이미지파일이 없습니다.</span>';
        
        target_file = null;
        drawImage(null);
        $('#file-label').html('이미지를 선택해주세요.');
        $('#upload-tab').html('<span class="pl-1">파일을 업로드해주세요.</span>');
        $("#selected_goods_nm").text(goods.goods_nm);
        $("#detail").html(goods.goods_cont);
        $("#select_prd_files").html(fileHtml);
    }

    function SetInfoBase() {
        // 모든 사이즈 체크
        $("#img-setting-tab [name=size]").prop('checked', true);

        // 총 목록갯수 표시
        $("#gd-total").text(goods_list.length);

        // 상품 선택
        $(".get-prd-btn").on("click", function() {
            var url = '/head/product/prd01/choice';
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
        })

        // 이미지 업로드
        $('#file').on("change", function(){
            if(this.files.length < 1) return;
            if(this.files.length > 1) return alert("파일은 한 개만 올려주세요.");
            target_file = this.files[0];

            if(validatePhoto() !== 200) {
                target_file = null;
                return $('#file-label').html('이미지를 선택해주세요.');
            } else {
                $('#file-label').html(target_file.name);
                previewImage();
            }
        });

        // 이미지 적용
        $("#apply").on("click", function(){
            let img_type = $('[name=img_type]:checked').val();
            let imgURL = $("#img-preview").attr("src");

            uploadImage(imgURL, img_type);
        });

        // 슬라이드 재생 to next
        $(".to-next").on("click", function() {
            const second = parseInt($("#auto_second").val());

            clearInterval(timer);
            timer = null;
            if(second === 0) slide("next");
            else {
                timer = setInterval(function() {
                    slide('next');
                }, second*1000);
            }
        })

        // 슬라이드 재생 to prev
        $(".to-prev").on("click", function() {
            const second = parseInt($("#auto_second").val());

            clearInterval(timer);
            timer = null;
            if(second === 0) slide("prev");
            else {
                timer = setInterval(function() {
                    slide('prev');
                }, second*1000);
            }
        })

        // 슬라이드 정지
        $(".to-stop").on("click", function() {
            clearInterval(timer);
            timer = null;
        })
    }

    // 슬라이드
    function slide(go_next) {
        const is_next = go_next === "next";
        const idx = gx.getSelectedNodes()[0].rowIndex;
        current_row = idx + (is_next ? 1 : -1);

        if(is_next) {
            if(current_row >= goods_list.length) current_row = 0;
        } else {
            if(current_row < 0) current_row = goods_list.length - 1;
        }
        const node = gx.getRowNode(current_row);
        node.setSelected(true);
    }

    // 선택한 이미지 파일 업로드 미리보기
    function previewImage() {
        const fr = new FileReader();
        fr.onload = drawImage;
        fr.readAsDataURL(target_file);
    }

    // 업로드 파일 적용
    function uploadImage(url, type) {
        if(validatePhoto() !== 200) return;
        
        let goods_no = $("#goods_no").val();
        let sizes = [];

        $("[name=size]:checked").each(function(){
            sizes.push(this.value);
        });

        let effect = {
            "amount" : $("#amount").val(),
            "radius" : $("#radius").val(),
            "threshold" : $("#threshold").val(),
            "quality" : $("#quality").val(),
        };

        $.ajax({
            type: "post",
            url: '/head/product/prd02/' + getGoodsNo() + '/upload',
            contentType: "application/x-www-form-urlencoded; charset=utf-8",
            dataType: 'json',
            data: {
                img : url,
                sizes : sizes,
                img_type : type,
                effect : effect,
                size : '500',
                _token : $("[name=_token]").val()
            },
            success: function(res) {
                if(res.code == "200"){
                    alert('적용되었습니다.');
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

    // 업로드파일 유효성 검사
    function validatePhoto() {
        if(target_file === null) return alert("업로드할 이미지를 선택해주세요.");
        if(!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target_file.name)) return alert("이미지 형식이 아닙니다.");
        if(target_file.size > 10*1024*1024) return alert("10M 이상 파일은 업로드 하실 수 없습니다.");

        if (target_file !== null) {
            const url = window.URL || window.webkitURL;
            const img = new Image();
	    
            img.src = url.createObjectURL(target_file);
            if(img.width < IMG_MIN_WIDTH || img.height < IMG_MIN_HEIGHT) {
                return alert('이미지 가로 세로 최소 사이즈는 700 X 700 입니다.');
            }
        }

        return 200;
    }

    // 업로드 미리보기 이미지 그리기
    function drawImage(e) {
        if(e === null) return $("#upload-tab").html('');

        let tmpImg = new Image();
        tmpImg.onload = function() {
            let ratio = this.height / this.width;
            let img_type = $('[name=img_type]:checked').val();

            $("#upload-tab").html('<div class="p-4"><img src="" id="img-preview" width="500px" alt=""></div>');
            $("#img-preview").attr("src", this.src);

            switch(img_type) {
                case 'a' :
                    getSortSizes().forEach(function(size){
                        appendCanvas(size,ratio, 'c_' + size, 'a');
                    });
                    break;
                case 'f' :
                    appendCanvas(500,ratio, 'c_500', 'f');
                    break;
            }

            const user_setting = $('#user-size:checked');
            const user_set_width = $('#user-size-w').val();
            if (user_setting.length > 0 && user_set_width > 0) {
                appendCanvas(user_set_width, 'u_' + user_set_width, 'u');
            }

            $('#uploadTab a[href="#upload-tab"]').tab('show');
            $('#upload-tab canvas').each(function(idx){
                const width = this.width;
                const height = this.width * ratio;

                const canvas = this;
                const ctx = canvas.getContext('2d');
                const image = new Image();

                image.src = e.target.result;
                image.onload = function() {
                    ctx.drawImage(this, 0, 0, width,height);
                }
            });

        };
        tmpImg.src = e.target.result;
    }

    function getSortSizes() {
        let sizes = [];

        $("[name=size]:checked").each(function() {
            sizes.push(this.value);
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
        let canvas = $("<canvas></canvas>").attr({
            id : id,
            name : id,
            width : size,
            height : size * ratio,
            style : "margin:10px",
            "data-type" : type
        });
        $("#upload-tab").append(canvas);
    }

    function getGoodsNo() {
        return gx.getSelectedRows()[0].goods_no;
    }

    // 상품선택 콜백함수
    function ChoiceGoodsNo(goods_nos){
        if(goods_nos.length < 1) return;
        var url = '/head/product/prd02/slider?goods_nos=' + goods_nos.join(",");
        window.location.href = url;
    }
</script>
@stop