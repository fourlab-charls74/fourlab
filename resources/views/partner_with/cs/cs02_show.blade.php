@extends('partner_with.layouts.layout')
@section('title','Q&A')
@section('content')

<style>
    textarea:disabled, textarea[disabled] {
        background: #eee !important;
    }

    input:disabled, input[disabled] {
        background: #eee !important;
    }

</style>


<div class="show_layout">
<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
    <div>
        <h3 class="d-inline-flex">상품 Q&A</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품 Q&A - {{ $list->goods_nm }}</span>
        </div>
    </div>
    <div>
        @if( $list->answer_yn == "Y" )
            <button type="button" class="btn btn-sm btn-primary shadow-sm" id="prepare-edit" onclick="return setEditable();"><i class="bx"></i>수정하시겠습니까?</button>
            <button type="button" class="btn btn-sm btn-primary shadow-sm d-none" id="edit" onclick="return update('{{ $idx }}');"><i class="bx bx-save mr-1"></i>수정</button>
        @elseif( $list->answer_yn == "N")
            @if( $list->check_id && $list->check_nm)
                @if( $list->check_id == $user_id )
                <button type="button" id="btn_checkout" class="btn btn-sm btn-primary shadow-sm" onclick="checkIO('checkout');"><i class="bx"></i>
                    접수 취소
                </button>
                <button type="button" class="btn btn-sm btn-primary shadow-sm" id="prepare-edit" onclick="return setEditable();"><i class="bx"></i>답변하시겠습니까?</button>
                <button type="button" class="btn btn-sm btn-primary shadow-sm d-none" id="edit" onclick="return update('{{ $idx }}');"><i class="bx bx-save mr-1"></i>답변완료</button>
                @else
                <button type="button" id="btn_checkout" class="btn btn-sm btn-primary shadow-sm" onclick="checkIO('checkout');"><i class="bx"></i>
                {{ $list->check_nm }} 님이 접수하였습니다.
                </button>
                @endif
            @else
            <button type="button" id="btn_checkin" class="btn btn-sm btn-primary shadow-sm" onclick="return checkIO('checkin');"><i class="bx"></i>접수 하시겠습니까?</button>
            @endif
        @endif
        <button type="button" onclick="moveToList();" class="btn btn-sm btn-primary shadow-sm"><i class="bx"></i>목록</button>
    </div>
</div>
<form method="post" name="store" action="/partner/cs/cs02">
    @csrf
    <div class="card_wrap">
      <div class="card shadow">
        <div class="card-body mt-0">
          <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="img_box mb-2 text-center"><img src="{{config('shop.image_svr')}}{{$list->img}}" style="width:100%;max-width:450px;" /></div>
                <div class="txt_box mb-2" style="text-align:center">
                    {{ $list->goods_nm }}
                </div>
                <div class="text-center">
                <a href="#" onclick="return openStock('{{$list->goods_no}}');" class="btn btn-sm btn-primary shadow-sm">
                    재고보기
                </a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="table-box-ty2 mobile">
                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <colgroup>
                            <col width="94px" />
                            <col width="33%" />
                            <col width="94px" />
                            <col width="33%" />
                        </colgroup>
                        <tr>
                            <th>출력여부</th>
                            <td>
                                <div class="txt_box">{{$list->show_yn}}</div>
                            </td>
                            <th>IP</th>
                            <td>
                                <div class="txt_box">{{$list->ip}}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>작성자</th>
                            <td>
                                <div class="txt_box">{{$list->user_nm}} / {{$list->user_id}}</div>
                            </td>
                            <th>작성일</th>
                            <td>
                                <div class="txt_box">{{$list->q_date}}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>질문제목</th>
                            <td colspan="3">
                                <div class="txt_box">{{$list->subject}}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>질문내용</th>
                            <td colspan="3">
                                <div class="txt_box">{{$list->question}}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                @if( $list->answer_yn == "Y")
                                    답변자
                                @elseif( $list->answer_yn == "N")
                                    접수자
                                @endif
                            </th>
                            <td colspan="3">
                                <div class="input_box">
                                    <div class="txt_box">
                                    @if( $list->answer_yn == "Y")
                                        {{$list->admin_nm}}
                                    @elseif( $list->answer_yn == "N")
                                        @if( $list->check_id && $list->check_nm )
                                            {{ $list->check_nm }}
                                        @endif
                                    @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>템플릿검색</th>
                            <td colspan="3">
                                <div class="input_box">
                                    <input type="text" name="q" value="" class="form-control form-control-sm ac-template-q" id="auto-template" autocomplete='off' disabled>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>답변내용</th>
                            <td colspan="3">
                                <div class="input_box">
                                    <textarea class="editor1 form-control form-control-sm wd100" name='answer' style='height:200px;' id="answer" disabled>{{$list->answer}}</textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>답변현황</th>
                            <td colspan="3">
                                <div class="txt_box" id="qa_state">
                                    @if( $list->answer_yn == "Y")
                                        {{$list->admin_nm}} ({{$list->admin_id}}) 님께서 {{$list->a_date}}에 답변하였습니다.
                                    @elseif( $list->answer_yn == "N")
                                        @if( $list->check_id && $list->check_nm )
                                            {{ $list->check_nm }} 님이 접수하였습니다.
                                        @endif
                                    @endif
                                </div>
                            </td>
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript" charset="utf-8">
    var frm = $('form');

    function update(idx) {
        $.ajax({
            async: true,
            method: 'put',
            url: '/partner/cs/cs02/' + idx,
            data: frm.serialize(),
            success: function (data) {
                alert("등록되었습니다.");
                window.location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    var template_no = 0;
    
    document.addEventListener("DOMContentLoaded", () => {

        $("#auto-template").autocomplete({
            //keydown
            source : getTemplateData,
            //검색어 선택되었을 경우
            select : getSelectedMsg,
            //최소 단어 입력
            minLength: 1,
            //첫번째 검색어 자동선택
            autoFocus: true,
            //검색 후 화면에 나오는 딜레이
            delay: 100
            });

        //keydown 됐을때 해당 값을 가지고 서버에서 검색함.
        function getTemplateData(request, response) {
            $.ajax({
                method: 'get',
                url: '/partner/cs02/show/template',
                data: { keyword : $("#auto-template").val() },
                success: function (data) {
                    response(data);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        }

        //선택된 항목 msg 불러오기.
        function getSelectedMsg(event, ui) {
            var target = ui.item;
            $.ajax({
                method: 'get',
                url: '/partner/cs02/show/template/' + target.no,
                success: function (data) {
                    console.log(data);
                    // $("#answer").val(data[0].ans_msg);
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        }

    });


    const moveToList = () => {
        document.location.href = '/partner/cs/cs02';
    }

    const setEditable = () => {
        document.store.q.disabled = false;
        document.store.answer.disabled = false;
        document.querySelector('#edit').classList.toggle('d-none');
        document.querySelector('#prepare-edit').classList.toggle('d-none');
    }

    const errorAlert = (message) => {
        let msg = "장애가 발생했습니다.\n관리자에게 문의해 주십시오.";
        if (message) msg = message;
        alert(`${message}`);
    }
    
    const checkIO = async (cmd) => {

        const NO = '{{ $idx }}';

        if (cmd == "checkin") {

            try {

                const response = await axios({ 
                    url: '/partner/cs/cs02/show/check', method: 'post', data: { no: NO, cmd: cmd }
                });

                const { result, check_msg } = response.data;

                if (result == 1) {
                    alert('접수되었습니다.');
                    window.location.reload();
                } else if (result == 0) {
                    errorAlert(check_msg);
                }
                
            } catch (error) {
                errorAlert();
            }

        } else if (cmd == "checkout") {

            try {
                
                const response = await axios({ 
                    url: '/partner/cs/cs02/show/check', method: 'post', data: { no: NO, cmd: cmd }
                });

                const { result, check_msg } = response.data;

                if (result == 1) {
                    alert('접수가 취소되었습니다.');
                    window.location.reload();
                } else if (result == 0) {
                    errorAlert(check_msg);
                }

            } catch (error) {
                errorAlert();
            }

        }

    };


</script>
@stop
