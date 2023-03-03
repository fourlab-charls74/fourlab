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
    <!--<input type="hidden" name="c_id" value="{{$admin_id}}">
    <input type="hidden" name="c_name" value="{{$admin_nm}}"> -->
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
        <input type="hidden" name="user_question" value="">
        <input type="hidden" name="qna_datas" value="">
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
                                    <a href="#" class="btn-sm btn btn-primary mr-1 open-btn" onclick="ChangeShow(document.f1)">일괄출력</a>
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
                            <h5 class="m-0 font-weight-bold">품목 정보</h5>
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
											<th>문의이미지</td>
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
                                                    <input type="hidden" name="ans_id"  value="{{ $admin_id }}">
                                                    <div class="form-inline">
                                                        <div><input type="text" name="ans_nm" id="ans_nm" value="{{ $admin_nm }}" class="form-control form-control-sm search-all mr-1"></div>
                                                        <div><input type="button" name="btn_checkin" id="btn_checkin" class="btn btn-primary mr-1" value=" " onclick="CheckIO();" style="width:auto;"></div>
                                                    </div>
                                                    <input type="button" name="btn_save" id="btn_save" value="답변완료" onclick="Cmder(document.f1.cmd.value);return false;" class="btn btn-outline-secondary mr-1" style="width:auto;">
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
    var ed;

    $(document).ready(function() {
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
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/head/mem20',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions);
    });
</script>

<script language="javascript">

const CELL_COLOR = {
    YELLOW: { 'background' : '#ffff99' }
};

var columns = [
        // this row shows the row index, doesn't use any data from the row

        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28,
			cellStyle: {"background":"#F5F7F7"},
            cellRenderer: function(params) {
                if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
                    return "<input type='checkbox' checked/>";
                }
            }
        },
        {field:"type",headerName:"문의유형",width:80,cellStyle:StyleGoodsTypeNM,editable: true, },
        {field:"subject",headerName:"제목",width:200,editable: true,
            cellRenderer: function(params) {
                var qna_data = params.data.idx;
                return '<a href="javascript:;" onClick="GetContents('+ qna_data +')">'+ params.value+'</a>'
            }
        },
        {field:"user_nm",headerName:"작성자", width:75, editable: true, },
        {field:"regi_date",headerName:"작성일", width:120, editable: true,},
        {field:"open_state",headerName:"출력", editable: true,},
        {field:"ans_state",headerName:"상태",
			cellStyle: function(params) {
				if (params.value == '대기') {
					return {'color':'#FF0000'};
				}else{
					return {'color':'#0000FF'};
				}
			}
        },
        {field:"idx",headerName:"idx", hide: true,},
        {field:"user_id",headerName:"user_id", hide: true,},
        {field:"admin_open_yn",headerName:"admin_open_yn", hide: true,},
        {field:"ans_yn",headerName:"ans_yn", hide: true,},
        { width: "auto" }
];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(); //270
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                // console.log(params);
                if (params.data.ans_yn == "Y") return CELL_COLOR.YELLOW;
            }
        }
        gx = new HDGrid(gridDiv, columns, options);
        //Search(1);
        Search();
    });

    function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/member/mem20/search', formData, 1);
    }

</script>
<script type="text/javascript" charset="utf-8">

    function GetContents(idx){
        const qnaIdx = idx;

        if(res){

            $.ajax({
                async: true,
                type: 'get',
                url: '/head/member/mem20/show/'+qnaIdx,
                success: function (data) {
                    //console.log(data.body);
                    setQaInfo(data.body);
                },
                complete:function(){
                    _grid_loading = false;
                },
                error: function(request, status, error) {
                    console.log("error")
                    //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });

        }else{
            alert("장애가 발생하였습니다. 질문을 다시 한번 선택해 주십시오.");
        }
    }

    function setQaInfo(res){
		// console.log(res);
        var qa_data = res[0];
        var ans_subject = "[re] " + qa_data.subject;

        // 출력상태
		if(qa_data.admin_open_yn=='Y') f1.a_open_yn[0].checked = true;
		else if(qa_data.admin_open_yn=='N') f1.a_open_yn[1].checked = true;

		// 답변상태
        //console.log("ans_yn : "+qa_data.ans_yn);
        $("#c_ans_yn").val(qa_data.ans_yn);

        $("#qa_subject").html(qa_data.subject);
        $("#qa_nm").html(qa_data.user_nm +"&nbsp;/&nbsp;<a href='#' onclick='PopUser(\""+ qa_data.user_id +"\");'>"+ qa_data.user_id +"</a>" );
        $("#qa_secret").html(qa_data.open_yn);
        $("#qa_type").html(qa_data.type_cd);
        $("#qa_tel").html(qa_data.mobile);
        $("#qa_date").html(qa_data.regi_date);
        //$("#a_open_yn").html(res.open_yn);
        $("#qa_content").html(qa_data.question);
        $("#qa_state").html('');
        $("#ans_subject").val(ans_subject);
        $("#ans_nm").html(qa_data.ans_nm);

        $("[name=check_id]").val(qa_data.check_id);
        $("[name=user_name]").val(qa_data.user_nm);
		$("[name=user_mobile]").val(qa_data.mobile);
		$("[name=user_email]").val(qa_data.email);
		$("[name=qa_subject]").val(qa_data.subject);
		$("[name=qa_regi_date]").val(qa_data.regi_date);
		$("[name=user_question]").val(qa_data.question.replace(/\n/g,'<br />'));
        $("[name=idx]").val(qa_data.idx);

        $("[name=qa_goods_no]").val(qa_data.goods_no);
		$("[namae=qa_goods_sub]").value = qa_data.goods_sub;

        if( qa_data.ord_no != null )
            $("#ord_no").html("<a href='#' onClick='openOrder(\"" + qa_data.ord_no + "\")'>" + qa_data.ord_no + "</a>");
        else
            $("#ord_no").html("");

        if( qa_data.goods_nm != null )
            $("#qa_goods_nm").html("<a href='#' onClick='openProduct(\"" + qa_data.goods_no + "\")'>" + qa_data.goods_nm + "</a>");
        else
            $("#qa_goods_nm").html("");

        if(qa_data.check_id != "" && qa_data.check_id != undefined){
            
            $("#btn_checkin").val(qa_data.check_nm + " 접수취소 ");
			$("#btn_save").attr("disabled",false);
			$("#qa_state").html(qa_data.check_nm + " 접수중 ");

        }else{
            $("#btn_checkin").show();
            $("#btn_checkin").val("접수");
            $("#btn_save").attr("disabled",true);
        }

        if(qa_data.ans_yn == "N"){
            $("[name=cmd]").val("addcmd");
            if (qa_data.check_id != "" && qa_data.check_id != undefined) {
                $("#btn_save").val("답변완료").attr("disabled", false);
            }
			$("[name=ans_subject]").val("[re] " + qa_data.subject);
			//$("[name=c_ans_yn]").val("Y");
        }else{
            $("[name=cmd]").val("editcmd");
            $("#btn_save").val("수정완료").attr("disabled",true);
            $("#qa_state").html(qa_data.check_nm + "(" + qa_data.check_id + ")&nbsp;&nbsp;" + qa_data.repl_date);
        }

        if(qa_data.ans_id != "" && qa_data.ans_id != undefined){
            //console.log("ans_nm : "+ qa_data.ans_nm);
            $("[name=ans_id]").val(qa_data.ans_id);
            $("#ans_nm").val(qa_data.ans_nm);
        }


        ed.editor.summernote("code", qa_data.answer);

		//관결 글 첨부 이미지 정보 불러오기
        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem20/get-data-image/' + qa_data.idx,
            success: function (data) {
                cbQnaImage(data);
            },
            complete:function(){
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });

    }

	function cbQnaImage(res)
	{
		//이미지 초기화
		$("#qna_image0").html("");
		$("#qna_image1").html("");
		$("#qna_image2").html("");

		if( res.code == "200" )
		{

			var	data	= res.body

			//console.log(data);

			if( data )
			{
				for( i = 0; i < data.length; i++ )
				{
					$("#qna_image" + i).html("<a href='{{config('shop.image_svr')}}" + data[i]['img_url'] + "' target='_new'>관련이미지" + (i+1) + "</a>");
				}
			}

		}
	}

    function Validate(cmd){

        if($("[name=c_ans_yn]").val() == 'Y'){
            if($("[name=ans_subject]").val() == ''){
                alert('답변제목을 입력해 주십시오.');
                $("[name=ans_subject]").focus();
                return false;
            }

            //내용 얻기(xqEditor)
            //var answer = xed.getCurrentContent();
            //var answer = $();
            //$("[name=answer]").val(answer);
            //console.log("answer : " + $("[name=answer]").val());

            if($("[name=answer]").val() == '' || $("[name=answer]").val() == '<p>&nbsp;</p>'){
                alert('답변 내용을 입력해 주십시오.');
                return false;
            }
        }
        return true;
    }

    function Save(cmd){
        var check_id = $("[name=check_id]").val();
        var user_id = $("[name=id]").val();
        var f1 = $("form[name=f1]");
        const idx = $("[name=idx]").val();

        if(check_id != "" && check_id != user_id){
            if(!confirm('다른 운영자가 접수중 입니다. 답변완료를 하시겠습니까?')){
                return;
            }
        }
        if($("[name=sms_yn]")){
            if($("[name=sms_yn]").checked){
                $("[name=sms_yn]").value = "Y";
            } else {
                $("[name=sms_yn]").value = "N";
            }
        }
        if($("[name=email_yn]")){
            if($("[name=email_yn]").checked){
                $("[name=email_yn]").value = "Y";
            } else {
                $("[name=email_yn]").value = "N";
            }
        }

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem20/show/'+ idx,
            data : f1.serialize(),
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

    function cbSave(res){
        if(res.qa_code == "1" ){
            //$("btn_checkin").style.display = "none";
            $("#btn_checkin").hide();

            if(document.f1.cmd.value == "addcmd"){
                $("#btn_save").val("답변완료 되었습니다.");
                $("[name=c_ans_yn]").val("Y");
            } else {
                $("#btn_save").val("수정완료 되었습니다.");
            }
            $("#btn_save").attr("disabled",true);
			Search();
        }else{
            alert("저장 중 오류가 발생하였습니다. 다시 처리하여 주십시요.");
        }
    }

    function Cmder(cmd){
        console.log("cmd : "+ cmd);
        if(cmd == "search"){
            //GridListDraw();
            search(1);
        } else if( cmd == "addcmd" || cmd == "editcmd" ){
            if( Validate(cmd) ){
                Save(cmd);
            }
        }
    }

    function CheckIO(){ // 접수, 접수해제
        var formData;
        var check_id = $("[name=check_id]").val();
        var cmd = "";
        var cmd_ori = $("#cmd").val();

        if(check_id == ""){
            $("#cmd").val("checkin");
        }else if(check_id != ""){
            $("#cmd").val("checkout");
        }
        formData = $("[name=f1]").serialize();
        cmd = $("[name=cmd]").val();

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/member/mem20/check',
            data : formData,
            success: function (data) {
                $("[name=cmd]").val(cmd_ori);
                if(cmd == "checkin"){
                    cbCheckIn(data);
                }else if(cmd == "checkout"){
                    cbCheckOut(data);
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


	function PopUser(memId){
        //const url='/head/member/mem01?cmd=edit&user_id='+memId;
        const url='/head/member/mem01/show/edit/'+memId;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
    }

    function cbCheckIn(res){
        var ff = document.f1;
        if(res.qa_code == "1"){
            $("#btn_checkin").val(ff.ans_nm.value + " 접수취소 ").show();
            $("#btn_save").attr("disabled",false);
            $("#qa_state").html(ff.ans_nm.value + " 접수중 ");
            $("[name=check_id]").val($("[name=id]").val());
        } else if(res.qa_code == "-1"){
            alert("다른 운영자가 접수중 입니다.");
            GetContents($("[name=idx]").val());
        } else {
            alert("처리 중 오류가 발생하였습니다. 다시 시도하여 주십시오.");
        }
    }

    function cbCheckOut(res){
        if(res.qa_code == 1){
            $("#btn_checkin").val("접수").show();
            $("#btn_save").attr("disabled",true);
            $("#qa_state").html("");
            $("[name=check_id]").val("");
        } else {
            alert("처리 중 오류가 발생하였습니다. 다시 시도하여 주십시오.");
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

	$(function(){
		 $(".ac-template-q")
        .autocomplete({
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
                        //console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
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
	});

</script>
@stop
