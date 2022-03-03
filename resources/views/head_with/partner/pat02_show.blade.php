@extends('head_with.layouts.layout')
@section('title','Q&A')
@section('content')
<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">파트너 Q&A</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 파트너 Q&A</span>
        </div>
    </div>

<form method="post" name="store">
    @csrf
    <div class="card_wrap aco_card_wrap">
        <div class="card">
            <div class="card-header mb-0 justify-content-between d-flex">
                <div></div>
                <div>
                    <button type="button" onclick="document.location.href = '/head/partner/pat02';" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx fs-14 mr-1"></i>목록</button>
                    <button type="button" onclick="return Update('{{ $idx }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-box-ty2 mobile">
                            <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <tr>
                                    <th>유형</th>
                                    <td>
                                        {{$list->type_nm}}
                                    </td>
                                    <th>입점업체</th>
                                    <td>
                                        {{$list->com_nm}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>제목</th>
                                    <td colspan="3">
                                        {{$list->subject}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>내용</th>
                                    <td colspan="3">
                                        @nl2br($list->question)
                                    </td>
                                </tr>
                                <tr>
                                    <th>문의일시</th>
                                    <td>
                                        {{$list->question_date}}
                                    </td>
                                    <th>답변일시</th>
                                    <td>
                                        {{$list->answer_date}}
                                    </td>
                                </tr>
                                <tr>
                                    <th>문의상태</th>
                                    <td colspan="3">
                                        <select id='state' name='state' class="form-control form-control-sm">
                                            @foreach ($qna_states as $qna_state)
                                                <option value='{{ $qna_state->code_id }}' @if($qna_state->code_id == $list->state)selected @endif>
                                                    {{ $qna_state->code_val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>답변 내용</th>
                                    <td colspan="3">
                                        <textarea class="form-control form-control-sm" id='answer' name='answer' style='height:200px;'>{{$list->answer}}</textarea>
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
<script type="text/javascript" charset="utf-8">
    function Update(idx) {

        if($('#answer').val() === "" && $('#state').val() == "1"){
            alert('답변내용을 작성해 주십시오.');
            $('#answer').focus();
            return false;
        }

        if(confirm('저장하시겠습니까?')){
            var frm = $('form[name=store]');
            var url = '/head/partner/pat02/' + idx;
            $.ajax({
                method: 'put',
                url: url,
                data: frm.serialize(),
                success: function (data) {
                    if(data.code == "200"){
                        alert('저장하였습니다.');
                        document.location.href = url
                    } else {
                        console.log(data.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }
</script>
@stop
