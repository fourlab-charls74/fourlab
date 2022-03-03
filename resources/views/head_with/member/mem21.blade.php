@extends('head_with.layouts.layout')
@section('title','상품Q&A')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<div class="page_tit">
    <h3 class="d-inline-flex">상품Q&A</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원&amp;CRM</span>
        <span>/ 상품Q&A</span>
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
                    <!-- 작성일 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">작성일</label>
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
                    <!-- 스타일넘버 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">스타일넘버</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm" name="style_no" id="style_no">
                            </div>
                        </div>
                    </div>
                    <!-- 출력여부 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">출력여부</label>
                            <div class="flax_box">
                                <select name='show_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($show_yn_items as $show_yn)
                                    <option value="{{ $show_yn->code_id }}">{{ $show_yn->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- 유형 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">유형</label>
                            <div class="flax_box">
                                <select name="type" id="type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($qa_types as $qa_type)
                                    <option value="{{ $qa_type->code_id }}">{{ $qa_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 상품코드 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">상품코드</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm" name="goods_no">
                                <input type="hidden" name="goods_sub" value="0">
                            </div>
                        </div>
                    </div>

                    <!-- 상품명 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">상품명</label>
                            <div class="flax_box">
                                <input type="text" name="goods_nm" class="form-control form-control-sm" style="width:100%;">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <!-- 진행상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">진행상태</label>
                            <div class="flax_box">
                                <select name='answer_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="N">답변대기</option>
                                    <option value="Y">답변완료</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 품목 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">품목</label>
                            <div class="flax_box">
                                <select name="opt_kind_cd" id="opt_kind_cd" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($opt_kind_cd_items as $opt_kind_cd)
                                    <option value="{{ $opt_kind_cd->id }}">{{$opt_kind_cd->val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 검색 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">검색</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                    <select name="kind" class="form-control form-control-sm">
                                        <option value="a.user_nm">작성자 이름</option>
                                        <option value="a.user_id" @if ($user_id !='' ) selected @endif>작성자 ID</option>
                                        <option value="a.admin_nm">답변자 이름</option>
                                        <option value="a.admin_id">답변자 ID</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box">
                                    <input type="text" name="qry" class="form-control form-control-sm" value="{{$user_id}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
        <input type="hidden" name="user_question" value="">
        <input type="hidden" name="qna_datas" value="">
        <input type="hidden" name="show_yn_s" value="">
        <input type="hidden" name="no" value="">
        <div class="row">
            <div class="col-sm-6">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mt-3 mt-sm-0">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-header mb-0">
                            <h5 class="m-0 font-weight-bold">상품 Q&A 상세 내용</h5>
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
                                            <th>상품정보</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <div id="goods_info_area">
                                                        <span id="view_img" style="display:inline-block; width:100px;"></span>
                                                        <ul style="margin:0; padding:0; list-style-type:none; list-style:none; display:inline-block; ">
                                                            <li id="goods_nm"></li>
                                                            <li id="goods_info"></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>출력여부</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="show_yn"></span>
                                                </div>
                                            </td>
                                            <th>IP</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="ipaddress"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>작성자</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="view_name"></span>
                                                </div>
                                            </td>
                                            <th>작성일</th>
                                            <td>
                                                <div class="txt_box">
                                                    <span id="q_date"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>질문제목</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <span id="view_goods_title"></span>
                                                    <span id="biew_comment"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>질문내용</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <span id="question"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>답변자</th>
                                            <td colspan="3">
                                                <div class="flax_box">
                                                    <input type="hidden" name="ans_id" value="{{ $admin_id }}">
                                                    <div class="form-inline">
                                                        <div><input type="text" name="admin_nm" class="form-control form-control-sm mr-1"></div>
                                                        <div><input type="hidden" name="admin_id"></div>
                                                        <div><input type="button" class="button btn btn-primary mr-1" value="" id="btn_checkin" onclick="CheckIO('checkin')" style="display:none; width:auto;"></div>
                                                        <div><input type="button" class="button btn btn-primary mr-1" value="" id="btn_checkout" onclick="CheckIO('checkout')" style="display:none; width:auto;"></div>
                                                        <div><input type="button" class="button btn btn-primary mr-1" value="" id="btn_checkedit" onclick="CheckEdit()" style="display:none; width:auto;"></div>
                                                    </div>
                                                    <input type='submit' class="btn btn-outline-secondary mr-1" name="btn_save" style="width:auto;" value='답변완료' onclick="Cmder(document.f1.cmd.value);return false;">
                                                    @if ($sms_yn == 'Y')
                                                    <div class="form-inline form-check-box mr-1">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="sms_yn" id="sms_check" class="custom-control-input" value="">
                                                            <label class="custom-control-label" for="sms_check">SMS</label>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if ($email_yn == 'Y')
                                                    <!--
                                                    <div class="form-inline form-check-box mr-1">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="email_yn" id="email_check" class="custom-control-input" value="">
                                                            <label class="custom-control-label" for="email_check">Email</label>
                                                        </div>
                                                    </div>
                                                    //-->
                                                    @endif
                                                    <!--
                                                    <div class="form-inline form-check-box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="none_check" id="none_check" class="custom-control-input" value="">
                                                            <label class="custom-control-label" for="none_check">NONE</label>
                                                        </div>
                                                    </div>
                                                    //-->
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>템플릿 검색</th>
                                            <td colspan="3">
                                                <div class="input_box">
                                                    <!-- <input type="text" name="q" value="" class="inputah" style="width:420px;" id="template_q" onkeydown='o_ac.ack();' 
                                                    onkeyup='o_ac.ac("hidden", this.id,"QNO");' onblur='o_ac.acb("hidden",this.id,"QNO");' autocomplete='off' > -->
                                                    <input type="text" name="q" value="" class="form-control form-control-sm ac-template-q" id="template_q" autocomplete='off'>
                                                    <input type="hidden" name="qno" id="qno" value="" style="width:100px;">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>답변내용</th>
                                            <td colspan="3">
                                                <div class="input_box">
                                                    <textarea name="answer" id="answer" class="form-control editor1"></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>답변현황</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    <div id="qa_state"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- 확인 -->
                            <!-- <div style="text-align:center;">
                                <input type="button" class="button" value="확인" onclick="Cmder('savecmd')">
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
<script type="text/javascript" charset="utf-8">
    var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload: {
                dir: '/data/head/mem21',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        ed = new HDEditor('.editor1', editorOptions);
    });
</script>

<script language="javascript">
    var columns = [
        // this row shows the row index, doesn't use any data from the row
        {
            headerName: '#',
            width: 40,
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
        },
        {
            field: "show_yn",
            headerName: "출력",
            width: 50,
            cellStyle: StyleGoodsTypeNM,
            editable: true,
            cellRenderer: function(params) {
                var qna_data = params.data.idx;
                return '<a href="#" onClick="Click(\'show_type\', \'' + params.data.no + '\')">' + params.value + '</a>'
            }
        },
        {
            field: "type",
            headerName: "유형",
            width: 70,
            cellStyle: StyleGoodsTypeNM,
            editable: true,
        },
        {
            field: "subject",
            headerName: "제목",
            width: 200,
            editable: true,
            cellRenderer: function(params) {
                var qna_data = params.data.idx;
                return '<a href="#" onClick="Click(\'qa_detail\', \'' + params.data.no + '\');return false;">' + params.value + '</a>'
            }
        },
        {
            field: "user_info",
            headerName: "작성자",
            width: 110,
            type: "HeadUserType"
        },
        {
            field: "q_date",
            headerName: "작성일",
            width: 110,
            editable: true,
        },
        {
            field: "code_val",
            headerName: "상태",
            width: 80,
            editable: true,
        },
        {
            field: "no",
            headerName: "no",
            hide: true,
        },
        {
            field: "goods_no",
            headerName: "goods_no",
            hide: true,
        },
        {
            field: "goods_sub",
            headerName: "goods_sub",
            hide: true,
        },
        {
            field: "goods_nm",
            headerName: "goods_nm",
            hide: true,
        },
        {
            field: "answer_yn",
            headerName: "answer_yn",
            hide: true,
        },
        {
            field: "style_no",
            headerName: "style_no",
            hide: true,
        },
        {
            field: "user_id",
            headerName: "user_id",
            hide: true,
        }
    ];

    function GoodsPopup(url) {
        domain = "{{ $domain }}";
        const product = window.open(domain + url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=600");
    }
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd"
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);
    pApp.ResizeGrid(); //270
    const _user_id = "{{ $admin_id }}";
    const _user_nm = "{{ $admin_nm }}";

    function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/member/mem21/search', formData, 1);
    }


    function gridCallback(data) {
        //console.log("data : "+data);
    }

    function Click(type, val) {
        if (type == "show_type") {
            ChangeShow(val);
        } else if (type == "qa_detail") {
            DetailQA(val);
        } else if (type == "user_info") {
            //alert("개발중입니다.");
            PopUser(val);
        }
        //console.log(gx.gridOptions.getValues());
    }

    function Validate(cmd) {
        if ($("[name=admin_nm]").val() == '') {
            alert('작성자를 입력해 주십시오.');
            $("[name=admin_nm]").focus();
            return false;
        }

        //내용 얻기(xqEditor)
        var answer = $("#answer").val();

        if (answer == '' || answer == '<p>&nbsp;</p>') {
            alert('답변 내용을 입력해 주십시오.');
            return false;
        }
        return true;
    }

    function Cmder(cmd) {
        var f1 = document.f1;

        if (cmd == "search") {
            search(1);
        } else if (cmd == "editcmd") {
            var no = f1.no.value;
            if (no != "") {
                if (Validate(cmd)) {
                    SaveCmd(cmd);
                }
            } else {
                alert("답변하실 질문을 선택해 주십시오.");
                return false;
            }
        }
    }

    function SaveCmd(cmd) {
        //SMS, EMAIL 전송

        if ($("#sms_check")) {
            if ($("#sms_check").is(":checked")) {
                $("#sms_check").val("Y");
            } else {
                $("#sms_check").val("N");
            }
        }
        if ($("#email_check")) {
            if ($("#email_check").is(":checked")) {
                $("#email_check").val("Y");
            } else {
                $("#email_check").val("N");
            }
        }

        var qa_no = $("[name=no]").val();
        //console.log("qa_no "+ qa_no);

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem21/comm/' + qa_no,
            data: $("[name=f1]").serialize(),
            success: function(data) {
                cbSaveCmd(data, qa_no);
            },
            complete: function() {
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }

    function cbSaveCmd(res, qa_no) {

        var ret = parseInt(res.qa_code);

        if (ret == "1") {
            gx.gridOptions.api.forEachNode(function(node) {
                if (node.data.no == qa_no) {
                    node.setDataValue('code_val', "답변완료");
                }
            });

            $("#btn_checkin").hide();
            $("#btn_checkout").hide();
            $("[name=btn_save]").val("답변완료 되었습니다.").attr("disabled", true);

            $("#qa_state").html($("[name=admin_nm]").val() + "님이 답변완료 하였습니다.");

        } else if (ret == "-1") {
            alert("Email 발송 시 문제가 있습니다. '디자인 > 스킨' 메뉴에서 상품QA 답변메일 스킨을 점검해 주십시오.");
        } else {
            alert("답변 시 문제가 발생하였습니다. 다시 시도하여 주십시오.");
        }
    }

    function ChangeShow(qa_no) {
        ;
        var show_yn = "";
        var change_show = "";
        var node_id = 0;
        change_show = (show_yn == "Y") ? "N" : "Y";
        gx.gridOptions.api.forEachNode(function(node) {
            if (node.data.no == qa_no) {
                show_yn = node.data.show_yn;
                node_id = node.id;
                change_show = (show_yn == "Y") ? "N" : "Y";
                node.setDataValue('show_yn', change_show);
            }
        });
        $("[name=cmd]").val("change");
        //$("[name=show_yn]").val(show_yn);
        $("[name=show_yn_s]").val(show_yn);

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem21/comm/' + qa_no,
            data: $("[name=f1]").serialize(),
            success: function(data) {
                console.log(data);
            },
            complete: function() {
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }

    function DetailQA(qa_no) {
        $.ajax({
            async: true,
            type: 'get',
            url: '/head/member/mem21/show/' + qa_no,
            success: function(data) {
                //console.log(data.body);
                cbDetailQA(data.body);
            },
            complete: function() {
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error")
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }

    function cbDetailQA(res) {
        var qa_data = res[0];
        if (qa_data) {
            var blank = "&nbsp;";

            f1.cmd.value = qa_data.cmd;
            $("[name=email_yn]").prop("checked", false);
            $("[name=sms_yn]").prop("checked", false);
            $("[name=none_check]").prop("checked", false);
            $("[name=no]").val(qa_data.no);
            $("form[name=f1]>input[name=goods_no]").val(qa_data.goods_no);
            $("form[name=f1]>input[name=goods_sub]").val(qa_data.goods_sub);
            $("#goods_nm").html("<a href=\"#\" onclick=\"return openHeadProduct('" + qa_data.goods_no + "'); \" style=\"font-size: 11pt; font-weight: bold;\">" + qa_data.goods_nm + "</a> <b style=\"color: red;\">[" + qa_data.sale_stat_cl + "]</b>");
            //$("#goods_info").html("재고(<a href=\"#\" onclick=\"Cmder('pop_jaego')\">" + Comma(qa_data.good_qty) + " / " + Comma(qa_data.wqty) + "</a>)");
            $("#goods_info").html("재고(<span onclick=\"return openHeadStock('"+qa_data.goods_no+"','');\" style=\"cursor: pointer;\"><b style=\"font-size: 11pt; color: red;\">" + Comma(qa_data.good_qty) + "</b> / <b style=\"font-size: 11pt; color: red;\">" + Comma(qa_data.wqty) + "</b></span>)");
            $("#view_img").html("<a href=\"#\" onclick=\"GoodsPopup('/app/product/detail/" + qa_data.goods_no + '/' + qa_data.goods_sub + "'); return false;\"><img src=" + qa_data.goods_img + " border=0  style=\"max-width:100px; width:auto; height:auto;\"/></a>");
            $("#show_yn").html(qa_data.show_yn);

            var url_user = "/head/webapps/user/usr01.php?CMD=edit&USER_ID=" + qa_data.user_id;
            $("#view_name").html(qa_data.user_nm + " / <a href=# onclick=PopUser('" + qa_data.user_id + "');>" + qa_data.user_id + "</a>");
            $("#q_date").html(qa_data.q_date);
            $("#ipaddress").html(qa_data.ip);
            $("#view_goods_title").html(qa_data.subject);
            $("#question").html(qa_data.question);

            if (qa_data.comment_cnt > 0) {
                $("#view_comment").html("&nbsp;(<a href=\"#\" onclick=\"PopComment('QA','" + qa_data.no + "'); return false;\" style=\"font-weight: bold;\">" + qa_data.comment_cnt + "</a>)");
            } else {
                $("#view_comment").html("");
            }


            if (qa_data.type == "사이즈") {
                ///$("VIEW_GOODS_SIZE").innerHTML = o.goods_size;
                $("VIEW_SEX").html((qa_data.user_sex == "F") ? "여성" : "남성");
                $("VIEW_HEIGHT").html(qa_data.user_height + " cm");
                $("VIEW_WEIGHT").html(qa_data.user_weight + " kg");
                $("VIEW_TOP").hytml(qa_data.user_top + blank);
                $("VIEW_BOTTOM").html(qa_data.user_bottom + " inch");
                $("VIEW_SHOES").html(qa_data.user_shoes + " mm");
                $("VIEW_BODY").html(qa_data.user_body + blank);
                $("VIEW_DESC").html(qa_data.user_etc_ment + blank);
                $("#goods_qa_size_info").show();
            } else {
                $("#goods_qa_size_info").hide();
            }

            $("[name=admin_nm]").val(qa_data.admin_nm);
            $("[name=admin_id]").val(qa_data.admin_id);
            $("#qa_state").html("&nbsp;");

            // 접수 관련 버튼 감추기
            $("#btn_checkin").hide();
            $("#btn_checkout").hide();
            //$("[name=answer]").val("");
            ed.editor.summernote("code", "");

            // 수정버튼
            $("#btn_checkedit").val("수정하시겠습니까?").css({
                'width': '170px'
            }).hide();

            // 답변완료 버튼
            $("[name=btn_save]").val("답변완료").attr("disabled", true);

            //SMS, EMAIL 전송
            $("[name=user_name]").val(qa_data.user_nm);
            $("[name=user_mobile]").val(qa_data.mobile);
            $("[name=user_email]").val(qa_data.email);
            $("[name=qa_subject]").val(qa_data.subject);
            $("[name=qa_regi_date]").val(qa_data.q_date);
            $("[name=user_question]").val(qa_data.question.replace(/\n/g, '<br />'));
            $("[name=goods_name]").val(qa_data.goods_nm);

            if (qa_data.answer_yn == "Y") { // 답변이 되어있는 상태

                // 수정버튼 활성화
                //$("#btn_checkedit").style.display = "";
                $("#btn_checkedit").show();
                ed.editor.summernote("code", qa_data.answer);

                $("#qa_state").html(qa_data.admin_nm + "(" + qa_data.admin_id + ") 님께서 " + qa_data.a_date + "에 답변하였습니다");

            } else {

                if (qa_data.check_id != "" && qa_data.check_id != null) {

                    $("#qa_state").html(qa_data.check_nm + "님이 접수하였습니다. ");
                    $("#btn_checkout").val(qa_data.check_nm + "님이 접수하였습니다. ").css({
                        'width': '170px'
                    }).show();

                    if (qa_data.check_id == _user_id) {
                        $("[name=btn_save]").attr("disabled", false);
                    }

                } else {

                    $("#btn_checkin").val("접수 하시겠습니까?").show();
                }
            }

            if (qa_data.answer_type != "") {
                if (qa_data.answer_type.indexOf("email") > -1) {
                    $("[name=email_yn]").attr("checked", true);
                }

                if (qa_data.answer_type.indexOf("phone") > -1) {
                    $("[name=sms_yn]").prop("checked", true);
                }

                if (qa_data.answer_type.indexOf("none") > -1) {
                    $("[name=none_check]").prop("checked", true);
                }
            }

        }
    }

    function Comma(num) {
        var len, point, str;

        num = num + "";
        point = num.length % 3;
        len = num.length;

        str = num.substring(0, point);
        while (point < len) {
            if (str != "") str += ",";
            str += num.substring(point, point + 3);
            point += 3;
        }

        return str;
    }

    function PopUser(memId) {
        //const url='/head/member/mem01?cmd=edit&user_id='+memId;
        const url = '/head/member/mem01/show/edit/' + memId;
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
    }

    function CheckIO(cmd) {
        var qa_no = $("[name=no]").val();
        $("[name=cmd]").val(cmd);
        //console.log($("[name=cmd]").val());

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem21/check/' + qa_no,
            data: $("[name=f1]").serialize(),
            success: function(data) {
                //console.log(data);
                if (data.qa_code == 1) {
                    CheckAfterView(cmd);
                } else if (data.qa_code == 0) {
                    alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
                } else {
                    alert(data.check_msg);
                }
            },
            complete: function() {
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error")
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

    }

    function CheckAfterView(cmd) {
        if (cmd == "checkin") {
            $("#btn_checkout").val(_user_nm + "님이 접수하였습니다.").css({
                "width": "170px"
            }).show();
            $("#btn_checkin").hide();

            $("[name=btn_save]").val("답변완료").attr("disabled", false);

            $("#qa_state").html(_user_nm + "님이 접수하였습니다.");

        } else if (cmd == "checkout") {

            $("#btn_checkin").val("접수 하시겠습니까?").show();
            $("#btn_checkout").hide();

            $("[name=btn_save]").val("답변완료").attr("disabled", true);
            $("#qa_state").html("&nbsp;");
        }
        $("input[name=cmd]").val("editcmd");
    }

    function CheckEdit() {
        $("[name=admin_id]").val(_user_id);
        $("[name=admin_nm]").val(_user_nm);
        $("#btn_checkedit").hide();
        $("[name=btn_save]").val("답변완료").attr("disabled", false);
    }

    function setTemplate() {
        const no = $("#qno").val();
        $.ajax({
            method: 'get',
            url: '/head/api/template/detail/' + no,
            data: {
                keyword: this.term
            },
            success: function(data) {
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

    var template_no = 0;
    $(function() {
        Search();
        $(".ac-template-q")
            .autocomplete({
                //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
                source: function(request, response) {
                    $.ajax({
                        method: 'get',
                        url: '/head/auto-complete/template-q',
                        data: {
                            keyword: this.term
                        },
                        success: function(data) {
                            response(data);
                        },
                        error: function(request, status, error) {
                            console.log("error");
                            //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                        }
                    });
                },
                minLength: 1,
                autoFocus: true,
                delay: 100,
                focus: function(event, ui) {},
                select: function(event, ui) {
                    //var text = ui.item.value;
                    var item_no = ui.item.no;
                    template_no = item_no;
                    $("#qno").val(item_no);
                    setTemplate();

                    //return false; 
                }

            });
    });
</script>

@stop