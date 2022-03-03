
@extends('head_with.layouts.layout-nav')
@section('title','판매처별 상품관리')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">판매처별 상품관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품 - {{ @$goods_no }}</span>
                <span>/ 판매처별 상품관리</span>
            </div>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-primary shadow-sm save-btn"><i class="bx bx-save mr-1"></i>저장</a>
        </div>
    </div>

    <div id="search-area" class="search_cum_form">
        <div class="card mb-3 px-1">
            <div class="d-flex card-header justify-content-between pt-2">
                    <h4>판매처별 상품설명 관리</h4>
                </div>
                <div class="card-body">
                <form method="get" name="search" id="search">
                    <div class="row">
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="sale_place">판매처</label>
                                <div class="d-flex justify-content-left">
                                    <select id="sale_place" name='sale_place' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach (@$sale_places as $sale_place)
                                        <option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="get_cont" class="btn btn-sm btn-primary shadow-sm ml-2" style="width: 80px">조회</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-12">
                        <ul class="row category_list_ty2">
                            <li class="col-lg-12">
                                <dl>
                                    <dt class="d-flex align-items-center justify-content-between">
                                        <div>판매처별 상품설명</div>
                                        <div>
                                            <button type="button" id="get_original" class="btn btn-sm btn-outline-primary shadow-sm">기본 상품설명 가져오기</button>
                                            <button type="button" id="reset_cont" class="btn btn-sm btn-outline-primary shadow-sm">내용 초기화</button>
                                        </div>
                                    </dt>
                                    <dd>
                                        <div class="area_box edit_box">
                                            <textarea name="goods_cont" id="goods_cont" class="form-control editor1"></textarea>
                                        </div>
                                    </dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let ed;
    let goods_no = 0;
    let goods_sub = 0;
    let ori_cont = '';

    $(document).ready(function() {
        goods_no = '{{ @$goods_no }}';
        goods_sub = '{{ @$goods_sub }}';
        ori_cont = `{{ @$goods_cont }}`;

        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 300,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:`/data/head/goods_cont/${goods_no}`,
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1', editorOptions, true);

        $("#get_cont").on("click", function() { searchContentOfSalePlace(); });
        $("#get_original").on("click", function() { getOriginalContent(); });
        $(".save-btn").on("click", function() { saveContent(); });
        $("#reset_cont").on("click", function() { ed.editor.summernote("code", ''); });
    });

    // 기본 상품설명 가져오기
    function getOriginalContent() {
        if(ori_cont === '') return;
        ed.editor.summernote("code", $.parseHTML(ori_cont)[0].data);
    }

    // 판매처별 상품설명 조회
    function searchContentOfSalePlace() {
        const sale_place = $("#sale_place").val();
        if(sale_place === '') return alert("판매처를 선택해주세요.");

        $.ajax({
            async: true,
            type: 'get',
            url: `/head/product/prd01/${goods_no}/search/sale-place-cont`,
            data: {
                'goods_sub': goods_sub,
                'sale_place': sale_place,
            },
            success: function (res) {
                if(res.code === 200) {
                    ed.editor.summernote("code", res.data === null ? '' : res.data.goods_cont);
                } else alert(res.message);
            },
            error: function(e) {
                alert(e.responseJSON.message);
            }
        });
    }

    // 상품설명 저장
    function saveContent() {
        const sale_place = $("#sale_place").val();
        if(sale_place === '') return alert("판매처를 선택해주세요.");
        if(!confirm(`"${$("#sale_place option:checked").text()}"의 상품설명을 저장하시겠습니까?`)) return;

        $.ajax({
            async: true,
            type: 'put',
            url: `/head/product/prd01/${goods_no}/save/sale-place-cont`,
            data: {
                'goods_sub': goods_sub,
                'sale_place': sale_place,
                'goods_cont': ed.html().replaceAll("'", "&lsquo;").replaceAll("\\", "&#8361;"),
            },
            success: function (res) {
                alert(res.message);
            },
            error: function(e) {
                alert(e.responseJSON.message);
                console.log(e);
            }
        });
    }
</script>

@stop