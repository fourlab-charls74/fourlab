@extends('head_skote.layouts.app')
@section('title','Q&A')
@section('content')
<div class="show_layout">
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">상품 Q&A - {{ $list->goods_nm }}</h1>
  <div>
    <a href="#" onclick="return Update('{{ $idx }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sm text-white-50"></i> 저장</a>
    <a href="#" onclick="return Destroy('{{ $idx }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sm text-white-50"></i> 삭제</a>
  </div>
</div>

<form method="post" name="store" action="/head/cs/cs02">
    @csrf
    <div class="card_wrap">
      <div class="card shadow brdn">
        <div class="card-body py-4">
          <div class="row align-items-center">
              <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="img_box mb-2 text-center"><img src="{{config('shop.image_svr')}}/{{$list->img}}" style="width:100%;max-width:450px;" /></div>
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
                <div class="row_wrap border_in pt-sm-0 pt-3">
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box half">
                            <label for="">출력여부</label>
                            <div>{{$list->show_yn}}</div>
                        </div>
                        <div class="inline-inner-box half">
                            <label for="">IP</label>
                            <div>{{$list->ip}}</div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box half">
                            <label for="">작성자</label>
                            <div>{{$list->user_nm}} / {{$list->user_id}}</div>
                        </div>
                        <div class="inline-inner-box half">
                            <label for="">작성일</label>
                            <div>{{$list->q_date}}</div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box triple">
                            <label for="">질문제목</label>
                            <div>{{$list->subject}}</div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box triple">
                            <label for="">질문내용</label>
                            <div>{{$list->question}}</div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box triple">
                            <label for="">답변자</label>
                            <div>
                              <input
                                type="text"
                                name="admin_nm"
                                id="admin_nm"
                                value="{{ empty($list->admin_nm) ? $user_nm : $list->admin_nm }}"
                                class="form-control form-control-sm wd100"
                              >
                            </div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box triple">
                            <label for="">템플릿검색</label>
                            <div>
                              <input type="text" id="auto-template" class="form-control form-control-sm wd100">
                            </div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box triple">
                            <label for="">답변내용</label>
                            <div>
                                <textarea class="form-control form-control-sm wd100" name='answer' style='height:200px;' id="answer">{{$list->answer}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row form-inline no-gutters">
                        <div class="inline-inner-box triple">
                            <label for="">답변현황</label>
                            <div>
                              <?php if ($list->admin_nm != "") { ?>
                              {{$list->admin_nm}} ({{$list->admin_id}}) 님께서 {{$list->a_date}}에 답변하였습니다.
                              <?php } ?>
                            </div>
                        </div>
                    </div>
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
    function Create() {
        $.ajax({
            async: true,
            method: 'post',
            url: '/head/cs/cs02',
            data: frm.serialize(),
            success: function (data) {
                document.location.href = '/head/cs/cs02'
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    function Update(idx) {
        $.ajax({
            async: true,
            method: 'put',
            url: '/head/cs/cs02/' + idx,
            data: frm.serialize(),
            success: function (data) {
                document.location.href = '/head/cs/cs02'
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    function Destroy(idx) {
        $.ajax({
            async: true,
            method: 'delete',
            url: '/head/cs/cs02/' + idx,
            data: frm.serialize(),
            success: function (data) {
                document.location.href = '/head/cs/cs02'
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

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
      delay: 500
    });

    //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
    function getTemplateData(request, response) {
      $.ajax({
          method: 'get',
          url: '/head/cs/cs02/show/template',
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
          url: '/head/cs/cs02/show/template/' + target.no,
          success: function (data) {
            $("#answer").val(data[0].ans_msg);
          },
          error: function(request, status, error) {
              console.log("error")
          }
      });
    }
</script>
@stop
