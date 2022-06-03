@extends('partner_with.layouts.layout')
@section('title','Q&A')
@section('content')
    <div class="show_layout">
        <div class="page_tit">
            <h3 class="d-inline-flex">Q&A</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ Q&A</span>
            </div>
        </div>

        <form method="post" name="store">
            @csrf
            <div class="card_wrap aco_card_wrap">
                <div class="card">
                    <div class="card-header mb-0 justify-content-between d-flex">
                        <div></div>
                        <div>
                            @if(@$list->state != "1")
                                <button type="button" onclick="return removeQna('{{ @$idx }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</button>
                            @endif
                            <button type="button" onclick="document.location.href = '/partner/support/spt02';" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx fs-14 mr-1"></i>목록</button>
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
                                            <th>문의상태</th>
                                            <td>
                                                {{$list->state_nm}}
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
                                            <th>답변 내용</th>
                                            <td colspan="3">
                                                @nl2br($list->answer)
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <colgroup>
                                <col width="60%">
                                <col width="20%">
                                <col width="20%">
                            </colgroup>
                            <thead style="background: #f5f5f5">
                                <tr>
                                <?php
                                    $column = ["내용", "작성자", "작성일"];
                                    foreach ($column as $name) {
                                        echo "<th>${name}</th>";
                                    }
                                ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $memos->map(function ($memo) {
                                        $content = nl2br($memo->memo);
                                        $writer = $memo->admin_nm;
                                        $date = $memo->regi_date;
                                        echo (
                                            "<tr>
                                                <td>${content}</td>
                                                <td>${writer}</td>
                                                <td>${date}</td>
                                            </tr>"
                                        );
                                    });
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>
                                            <label for="reply">댓글</label>
                                        </th>
                                        <td> 
                                            <textarea name="reply" id="reply" rows="10" style="width:100%" class="form-control form-control-sm"></textarea>
                                        </td>
                                        <th>
                                            <a href="#" onclick="saveReply('{{ @$idx }}');"
                                                style="margin: 0 auto;"
                                                class="btn btn-sm btn-primary shadow-sm d-block">
                                            저장
                                            </a>
                                        </th>
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

        const removeQna = (idx) => {
            if (confirm('삭제하시겠습니까?')) {
                const no = idx;
                axios({
                    url: '/partner/support/spt02/show/remove_qna',
                    method: 'delete',
                    data: { no: no }
                }).then((response) => {
                    if (response.data.result == 1) {
                        location.href = '/partner/support/spt02';
                    } else {
                        alert("Q&A 삭제에 실패하였습니다. 다시 한번 시도하여 주십시오.");
                    }
                }).catch((error) => {});
            }
        }

        const saveReply = (idx) => {
            const reply = document.store.reply.value;
            const no = idx;
            axios({
                url: '/partner/support/spt02/show/save_reply',
                method: 'post',
                data: { reply: reply, no: no }
            }).then((response) => {
                if (response.data.result == 1) {
                    window.location.reload();
                } else {
                    alert("댓글 저장에 실패했습니다. 다시 한번 시도하여 주십시오.");
                }
            }).catch((error) => {});
        };

    </script>
@stop
