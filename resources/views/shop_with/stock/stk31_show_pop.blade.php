@extends('shop_with.layouts.layout-nav')
@section('title','매장 공지사항')
@section('content')

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">매장 공지사항</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 매장 공지사항</span>
            </div>
        </div>
        <div class="flax_box">
            <button type="button" onclick="noticeRead()" class="btn btn-sm btn-primary shadow-sm mr-1">읽음</button>
            <button type="button" onclick="window.close()" class="btn btn-sm btn-outline-primary shadow-sm mr-1">닫기</button>
        </div>
    </div>
    <form>
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>작성자</th>
                                        <td>
                                            <div class="txt_box">{{$user->name}}
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td>
                                            <div class="txt_box">{{$user->subject}}
                                                @error(' subject') <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td>
                                            <div>
                                                <input type="hidden" id="div_content1" name="content" value='{{$user->content}}' />
                                                <div id="div_content2" class="txt_box" style="min-height: 200px"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="resul_btn_wrap mt-3 d-block">
                                    <a href="javascript:locate();" class="btn btn-sm btn-primary">공지사항 바로 가기</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">

    $(document).ready(function() {
        document.getElementById('div_content2').innerHTML = $('#div_content1').val();
    });

    function noticeRead() {
        let ns_cd = "{{$storeCode->ns_cd}}";
        let store_cd = "{{$storeCode->store_cd}}";

        $.ajax({
            method: 'put',
            url: '/shop/stock/stk31/notice_read',
            data: {
                ns_cd : ns_cd,
                store_cd : store_cd,
            },
            success: function(data) {
                if (data.code == '200') {
                    alert('읽음 처리 되었습니다.');
                    window.close();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                    // console.log(e.responseText)
            }
        });
    }

    function locate() {
        if(confirm('공지사항 메뉴로 이동시 자동으로 읽음 처리 됩니다.\r\n이동하시겠습니까?')){
            noticeRead();
            window.opener.location.href = "/shop/stock/stk31";
            window.close();
        }
    }
</script>
@stop
