@extends('head_with.layouts.layout')
@section('title','1:1문의')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<div class="page_tit">
    <h3 class="d-inline-flex">1:1문의</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원&amp;CRM</span>
        <span>/ 1:1문의</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 질문작성일 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">질문작성일</label>
							<div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 진행상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">진행상태</label>
                            <div class="flax_box">
                                <select name='ans_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($admin_ans_items as $ans_item)
                                        <option value="{{ $ans_item->code_id}}">{{ $ans_item->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 문의유형 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">문의유형</label>
                            <div class="flax_box">
                                <select name='qna_type' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($qna_types as $qna_type)
                                        <option value="{{ $qna_type->code_id }}">{{ $qna_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 제목 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">제목</label>
                            <div class="flax_box">
                                <input type="text" name="subject" class="form-control form-control-sm search-enter" style="width:100%;">
                            </div>
                        </div>
                    </div>

                    <!-- 작성자 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">작성자</label>
                            <div class="flax_box">
                                <input type="text" name="name" class="form-control form-control-sm search-enter" style="width:100%;">
                            </div>
                        </div>
                    </div>

                    <!-- 출력상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">출력상태</label>
                            <div class="flax_box">
                                <select name='admin_open_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($admin_open_yn_items as $admin_open_yn)
                                        <option value="{{ $admin_open_yn->code_id }}">{{ $admin_open_yn->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 비밀글여부 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">비밀글여부</label>
                            <div class="flax_box">
                                <select name='open_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($open_yn_items as $open_yn_item)
                                        <option value="{{ $open_yn_item->code_id }}">{{ $open_yn_item->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">아이디</label>
                            <div class="flax_box">
                                <input type="text" name="user_id" class="form-control form-control-sm search-enter" style="width:100%;" value="{{$user_id}}">
                            </div>
                        </div>
                    </div>
                    <!-- 담당 CS -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">담당 CS</label>
                            <div class="flax_box">
                                <input type="text" name="ans_nm" class="form-control form-control-sm search-enter" style="width:100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<div class="show_layout">
    <form name="f1" id="f1">
        <input type="hidden" name="cmd" id="cmd" value="">
        <input type="hidden" name="idx" value="">
        <input type="hidden" name="check_id" value="">
        <input type="hidden" name="id" value="{{ $admin_id }}">
        <input type="hidden" name="qa_goods_no" value="">
        <input type="hidden" name="qa_goods_sub" value="">
        <input type="hidden" name="user_name" value="">
        <input type="hidden" name="user_mobile" value="">
        <input type="hidden" name="user_email" value="">
        <input type="hidden" name="qa_subject" value="">
        <input type="hidden" name="qa_regi_date" value="">
        <input type="hidden" name="qa_repl_date" value="">
        <input type="hidden" name="user_question" value="">
        <input type="hidden" name="qna_datas" value="">
        <input type="hidden" name="prev_ans_yn" value="">
        <input type="hidden" name="prev_ans_id" value="">
        <input type="hidden" name="prev_ans_nm" value="">
        <div class="row">
            <div class="col-sm-6">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                    <a href="#" class="btn-sm btn btn-primary open-btn" onclick="ChangeShow(document.f1)">일괄출력</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" style="height:750px;width:100%;" class="ag-theme-balham"></div>
                        </div>
                    </div>
                 </div>
            </div>
            <div class="col-sm-6 mt-3 mt-sm-0">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-header mb-0">
                            <h5 class="m-0 font-weight-bold">문의 상세정보</h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="200px">
                                        <col width="30%">
                                        <col width="200px">
                                        <col width="30%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>제목</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <span id="qa_subject"></span>&nbsp;/&nbsp;
                                                    <span id="qa_goods_nm"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>작성자</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="qa_nm"></span>
                                                </div>
                                            </td>
                                            <th>비밀글여부</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="qa_secret"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>문의유형</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="qa_type"></span>
                                                </div>
                                            </td>
                                            <th>연락처</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="qa_tel"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>문의일자</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="qa_date"></span>
                                                </div>
                                            </td>
                                            <th>출력상태</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="a_open_yn" id="a_open_y" class="custom-control-input" value="Y">
                                                        <label class="custom-control-label" for="a_open_y">예</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="a_open_yn" id="a_open_n" class="custom-control-input" value="N" checked>
                                                        <label class="custom-control-label" for="a_open_n">아니요</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주문번호</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <span id="ord_no"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>문의내용</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <span id="qa_content"></span>
                                                </div>
                                            </td>
                                        </tr>
										<tr >
											<th>문의이미지</th>
											<td colspan="3">
												<span id="qna_image0">&nbsp;</span><br>
												<span id="qna_image1">&nbsp;</span><br>
												<span id="qna_image2">&nbsp;</span>
											</td>
										</tr>
                                        <tr>
                                            <th>답변자</th>
                                            <td colspan="3">
                                                <div class="flax_box">
                                                    <input type="hidden" name="ans_id" value="{{ $admin_id }}">
                                                    <div class="form-inline">
                                                        <div><input type="text" name="ans_nm" id="ans_nm" value="{{ $admin_nm }}" class="form-control form-control-sm search-all mr-1"></div>
                                                        <div><input type="button" name="btn_checkin" id="btn_checkin" class="btn btn-primary mr-1" value="" onclick="return setQnaCheck();" style="width:auto;display: none;"></div>
                                                    </div>
                                                    <input type="button" name="btn_save" id="btn_save" value="" onclick="Cmder(document.f1.cmd.value);return false;" class="btn btn-outline-secondary mr-1" style="width:auto;display: none;">
                                                    @if ($sms_yn == 'Y')
                                                    <div class="form-inline form-check-box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="sms_yn" id="sms_check" class="custom-control-input" value="Y">
                                                            <label class="custom-control-label" for="sms_check">SMS</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if ($email_yn == 'Y')
                                                    <div class="form-inline form-check-box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="email_check" id="email_check" class="custom-control-input" value="">
                                                            <label class="custom-control-label" for="email_check">Email</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>답변제목</th>
                                            <td colspan="3">
                                                <div class="flax_box">
                                                    <input type="text" name="ans_subject" id="ans_subject" class="form-control form-control-sm search-all" style="width:75%;" >
                                                    <select name="c_ans_yn" id="c_ans_yn" class="form-control form-control-sm" style="width:calc(25% - 5px);margin-left:5px;">
                                                        @foreach ($admin_ans_items as $ans_item)
                                                            <option value="{{ $ans_item->code_id}}">{{ $ans_item->code_val }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>답변내용</th>
                                            <td colspan="3">
                                                <div class="area_box">
                                                    <textarea name="answer" id="answer" class="form-control editor1"></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>템플릿 검색</th>
                                            <td colspan="3">
                                                <div class="input_box">
                                                    <input type="text" name="opt_kind_nm" id="opt_kind_nm" class="form-control form-control-sm search-all ac-template-q">
                                                    <input type="hidden" name="qno" id="qno" value="">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>답변현황</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <span id="qa_state"></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- 확인
                            <div class="mt-3" style="text-align:center;">
                                <input type="button" class="btn btn-primary" value="확인" onclick="Cmder('savecmd')">
                            </div-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', { gridId: "#div-gd", height: 260 });
    let gx;
    let ed;
    const CELL_COLOR = { YELLOW: { 'background' : '#ffff99' } };

    const columns = [
        { headerName: '', headerCheckboxSelection: true, checkboxSelection: true, width: 28, cellStyle: { "background":"#F5F7F7" },
            cellRenderer: function(params) {
                if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
                    return "<input type='checkbox' checked/>";
                }
            }
        },
        { field: "type", headerName: "문의유형", width: 80, cellStyle: (params) => ({ ...StyleGoodsTypeNM(params), 'text-align': 'center' }) },
        { field: "subject", headerName: "제목", width: 200,
            cellRenderer: function(params) {
                return '<a href="javascript:void(0);" onclick="return setQnaDetail('+ params.data.idx +');">'+ params.value + '</a>';
            }
        },
        { field: "user_nm", headerName: "작성자", width: 75, cellStyle: { 'text-align': 'center' } },
        { field: "regi_date", headerName: "작성일", width: 120, cellStyle: { 'text-align': 'center' } },
        { field: "admin_open_state", headerName: "출력", width: 60, cellStyle: { 'text-align': 'center' } },
        { field: "open_state", headerName: "비밀글여부", width: 80, cellStyle: { 'text-align': 'center' } },
        { field: "ans_state", headerName: "상태",
            cellStyle: (params) => ({ 'text-align': 'center', 'color': (params.value === '대기' ? '#FF0000' : '#0000FF') }),
        },
        { field: "idx", headerName: "idx", hide: true },
        { field: "user_id", headerName: "user_id", hide: true },
        { field: "admin_open_yn", headerName: "admin_open_yn", hide: true },
        { field: "ans_yn", headerName: "ans_yn", hide: true },
        { width: "auto" }
    ];

    $(document).ready(function() {
        pApp.ResizeGrid(260);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            getRowStyle: (params) => {
                if (params.data.ans_yn == "Y") return CELL_COLOR.YELLOW;
            }
        });

        Search();
        setTemplateAutoComplete();

        let editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        let editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir: '/data/head/mem20',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        ed = new HDEditor('.editor1', editorOptions);
    });

    function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/member/mem20/search', formData, 1);
    }

    // 좌측테이블에서 제목 클릭 시, 해당문의의 상세정보를 우측테이블에 출력
    async function setQnaDetail(idx) {
        const res = await axios({ method: 'get', url: '/head/member/mem20/show/' + idx });
        if (res.status !== 200 || res.data.code !== 200) {
            alert('조회 중 에러가 발생했습니다. 질문을 다시 한 번 선택해주세요.');
            return;
        }

        const qna = res.data.body[0];
        let ans_subject = '[re]' + qna.subject;

        $("[name='a_open_yn']")[qna.admin_open_yn === 'Y' ? 0 : 1].checked = true;
        $("#qa_subject").html(qna.subject);
        $("#qa_nm").html(`${qna.user_nm} / <a href="javascript:void(0);" onclick="return openUserPopup('${qna.user_id}');">${qna.user_id}</a>`);
        $("#qa_secret").html(qna.open_yn);
        $("#qa_type").html(qna.type_cd);
        $("#qa_tel").html(qna.mobile);
        $("#qa_date").html(qna.regi_date);
        $("#qa_content").html(qna.question);
        $("#ans_nm").val(qna.ans_nm);
        $("#ans_subject").val(ans_subject);
        $("#c_ans_yn").val(qna.ans_yn);
        $("#qa_state").html('');
        $("#ord_no").html('');
        $("#qa_goods_nm").html('');

        $("[name='check_id']").val(qna.check_id);
        $("[name='user_name']").val(qna.user_nm);
        $("[name='user_mobile']").val(qna.mobile);
        $("[name='user_email']").val(qna.email);
        $("[name='qa_subject']").val(qna.subject);
        $("[name='qa_regi_date']").val(qna.regi_date);
        $("[name='qa_repl_date']").val(qna.repl_date);
        $("[name='user_question']").val(qna.question.replace(/\n/g,'<br />'));
        $("[name='idx']").val(qna.idx);
        $("[name='qa_goods_no']").val(qna.goods_no);
        $("[name='qa_goods_sub']").val(qna.goods_sub);
        $("[name='prev_ans_yn']").val(qna.ans_yn);
        $("[name='prev_ans_id']").val(qna.ans_id);
        $("[name='prev_ans_nm']").val(qna.ans_nm);

        if (qna.ord_no !== null) {
            $("#ord_no").html(`<a href="javascript:void(0);" onclick="return openOrder('${qna.ord_no}')">${qna.ord_no}</a>`);
        }
        if (qna.goods_nm !== null) {
            $("#qa_goods_nm").html(`<a href="javascript:void(0);" onclick="return openProduct('${qna.goods_no}')">${qna.goods_nm}</a>`);
        }
        if (qna.ans_id) {
            $("[name='ans_id']").val(qna.ans_id);
        }

        // 답변현황 설정
        if (qna.check_id !== '' && qna.check_id !== null) {
            if (qna.check_id !== $("[name='id']").val()) {
                $("#btn_checkin").val(qna.check_nm + " 접수중").show();
                $("#btn_checkin").attr("disabled", true);
                $("#btn_save").attr("disabled", true);
                $("#btn_save").hide();
            } else {
                $("#btn_checkin").val(qna.check_nm + " 접수취소").show();
                $("#btn_checkin").attr("disabled", false);
                $("#btn_save").attr("disabled", false);
                $("#btn_save").val(qna.ans_yn === 'Y' ? "수정완료" : "답변완료").show();
            }
            $("#qa_state").html(`<strong>${qna.check_nm}</strong> 접수중`);
        } else {
            $("#btn_checkin").val("접수").show();
            $("#btn_checkin").attr("disabled", false);
            $("#btn_save").attr("disabled", true);
            $("#btn_save").hide();
            if (qna.ans_yn === 'Y') $("#qa_state").html(`<strong class="mr-2">${qna.ans_nm}(${qna.ans_id}) 답변완료</strong>${qna.repl_date}`);
            else if (qna.ans_yn === 'C') $("#qa_state").html(`<strong class="mr-2">${qna.ans_nm}(${qna.ans_id}) 등록불가처리</strong>${qna.repl_date}`);
        }

        if (qna.ans_yn === 'N') {
            $("[name='cmd']").val("addcmd");
        } else {
            $("[name='cmd']").val("editcmd");
        }

        // 답변내용 글 & 이미지 출력
        ed.editor.summernote("code", qna.answer);
        const answer_res = await axios({ method: 'put', url: '/head/member/mem20/get-data-image/' + idx });
        if (answer_res.status === 200) {
            cbQnaImage(answer_res.data);
        } else {
            console.error(answer_res);
        }
    }

    // 문의 작성자 회원 상세정보 팝업 오픈
    function openUserPopup(user_id) {
        const url = '/head/member/mem01/show/edit/' + user_id;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
    }

    // 접수 & 접수취소
    function setQnaCheck() {
        const check_id = $("[name='check_id']").val();
        const cmd_ori = $("#cmd").val();

        $("#cmd").val(check_id === '' ? 'checkin' : 'checkout');

        const data = $("[name='f1']").serialize();
        const cmd = $("#cmd").val();

        axios({
            method: 'put',
            url: '/head/member/mem20/check',
            data: data
        }).then((res) => {
            $("#cmd").val(cmd_ori);
            if (cmd === 'checkin') cbCheckIn(res.data);
            else if (cmd === 'checkout') cbCheckOut(res.data);
        }).catch((error) => {
            console.log(error);
        });
    }

    // 접수처리 완료
    function cbCheckIn(res) {
        if (res.qa_code == "1") {
            const ff = document.f1;
            $("#btn_checkin").val(ff.ans_nm.value + " 접수취소");
            $("#btn_save").attr("disabled", false);
            $("#btn_save").val(ff.prev_ans_yn.value === 'Y' ? "수정완료" : "답변완료").show();
            $("#qa_state").html(`<strong>${ff.ans_nm.value}</strong> 접수중`);
            $("[name='check_id']").val($("[name='id']").val());
        } else if (res.qa_code == "-1") {
            alert("다른 운영자가 접수중 입니다.");
            setQnaDetail($("[name=idx]").val());
        } else {
            alert("처리 중 오류가 발생하였습니다. 다시 시도하여 주십시오.");
        }
    }

    // 접수취소처리 완료
    function cbCheckOut(res) {
        if (res.qa_code == 1) {
            $("#btn_checkin").val("접수");
            $("#btn_save").attr("disabled", true);
            $("#btn_save").hide();
            $("#qa_state").html(
                $("[name='prev_ans_yn']").val() === 'Y'
                    ? `<strong class="mr-2">${$("[name='prev_ans_nm']").val()}(${$("[name='prev_ans_id']").val()}) 답변완료</strong>${$("[name='qa_repl_date']").val()}`
                    : $("[name='prev_ans_yn']").val() === 'C'
                        ? `<strong class="mr-2">${$("[name='prev_ans_nm']").val()}(${$("[name='prev_ans_id']").val()}) 등록불가처리</strong>${$("[name='qa_repl_date']").val()}`
                        : ''
            );
            $("[name=check_id]").val("");
        } else {
            alert("처리 중 오류가 발생하였습니다. 다시 시도하여 주십시오.");
        }
    }

    function Cmder(cmd) {
        if (cmd == "search") {
            Search(1);
        } else if (cmd == "addcmd" || cmd == "editcmd") {
            if (Validate(cmd)) Save(cmd);
        }
    }

    // 답변완료 & 수정완료 시 내용체크
    function Validate(cmd) {
        if ($("[name='c_ans_yn']").val() === 'Y') {
            if ($("[name='ans_subject']").val() === '') {
                alert('답변제목을 입력해 주십시오.');
                $("[name=ans_subject]").focus();
                return false;
            }

            if ($("[name='answer']").val() === '' || $("[name='answer']").val() == '<p>&nbsp;</p>') {
                alert('답변 내용을 입력해 주십시오.');
                return false;
            }
        }
        return true;
    }

    // 답변완료 & 수정완료
    function Save(cmd) {
        const check_id = $("[name='check_id']").val();
        const user_id = $("[name='id']").val();
        const idx = $("[name='idx']").val();

        if (check_id !== '' && check_id !== user_id) {
            if (!confirm('다른 운영자가 접수중 입니다. 답변완료를 하시겠습니까?')) {
                return;
            }
        }

        if ($("[name='sms_yn']")) {
            if ($("[name='sms_yn']").checked) {
                $("[name='sms_yn']").value = "Y";
            } else {
                $("[name='sms_yn']").value = "N";
            }
        }
        if ($("[name='email_yn']")) {
            if ($("[name='email_yn']").checked) {
                $("[name='email_yn']").value = "Y";
            } else {
                $("[name='email_yn']").value = "N";
            }
        }

        const form_data = $("form[name='f1']").serialize();

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem20/show/' + idx,
            data : form_data,
            success: function (data) {
                cbSave(data);
            },
            complete:function(){
                _grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error")
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }

    // 템플릿 검색 자동완성 지원
    function setTemplateAutoComplete() {
        $(".ac-template-q").autocomplete({
            //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
            source : function(request, response) {
                $.ajax({
                    method: 'get',
                    url: '/head/auto-complete/template-q',
                    data: { keyword : this.term },
                    success: function (data) {
                        response(data);
                    },
                    error: function(request, status, error) {
                        console.log("error");
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            delay: 100,
            focus: function(event, ui) {
            },
            select:function(event,ui){
                //var text = ui.item.value;
                var item_no = ui.item.no;
                template_no = item_no;
                $("#qno").val(item_no);
                setTemplate();

                //return false;
            }

        });
    }
</script>

<script type="text/javascript" charset="utf-8">
	function cbQnaImage(res) {
		//이미지 초기화
		$("#qna_image0").html("");
		$("#qna_image1").html("");
		$("#qna_image2").html("");

		if( res.code == "200" )
		{

			var	data	= res.body

			if( data )
			{
				for( i = 0; i < data.length; i++ )
				{
					$("#qna_image" + i).html("<a href='{{config('shop.image_svr')}}" + data[i]['img_url'] + "' target='_new'>관련이미지" + (i+1) + "</a>");
				}
			}

		}
	}

    function cbSave(res) {
        if (res.qa_code == "1") {
            $("#btn_checkin").hide();
            $("#btn_save").attr("disabled", true);

            setQnaDetail($("[name='idx']").val());
            Search();
        } else {
            alert("저장 중 오류가 발생하였습니다. 다시 처리하여 주십시요.");
        }
    }

    // 일괄출력 실행
    function ChangeShow(ff) {
        var selectedRowData = gx.gridOptions.api.getSelectedRows();
        var qna_data = "";
        selectedRowData.forEach( function(selectedRowData, index) {
            if(selectedRowData.idx != ""){
                if(qna_data == ""){
                    qna_data = selectedRowData.idx;
                }else{
                    qna_data += ',' + selectedRowData.idx;
                }
            }
        });

        if (qna_data == ""){
            alert('출력하실 항목을 선택해 주십시오.');
            return;
        }

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem20/change',
            data : {
                "data": qna_data
            },
            success: function (data) {
                if(data.qa_code == 1 ){
                    Search(1);
                }else{
                    alert("처리 중 오류가 발생하였습니다. 다시 시도하여 주십시요.");
                }
            },
            complete:function(){
                _grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error")
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

    }

	function setTemplate(){
        const no = $("#qno").val();
        $.ajax({
            method: 'get',
            url: '/head/api/template/detail/'+no,
            data: { keyword : this.term },
            success: function (data) {
                var answer = data.body[0].ans;
                answer = answer.replaceAll("\n", "<br>");
                ed.editor.summernote("code", answer);
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }
</script>
@stop
