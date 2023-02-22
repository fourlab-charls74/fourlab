@extends('shop_with.layouts.layout-nav')
@section('title','내용')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">알림</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 알림 보기</span>
                </div>
            </div>
            @if ($msg_type == 'pop')
            <div class="flax_box">
                <button type="button" onclick="msgRead()" class="btn btn-sm btn-primary shadow-sm mr-1">읽음</button>
                <button type="button" onclick="window.close()" class="btn btn-sm btn-outline-primary shadow-sm mr-1">닫기</button>
            </div>
            @endif
        </div>
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0" style="display:inline-block"> 
                        <a>{{ @$msg_type == 'send' ? '수신처' : '발신처' }}</a>
                </div>
                <div style="display:inline-block;"></div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="94px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>{{ @$msg_type == 'send' ? '수신처' : '발신처' }}</th>
                                                <td>
                                                    <div class="flax_box" name="sd" id="sd">
                                                        @if($msg_type == 'send')
                                                            <span>{{@$receiver_nm}}</span>
                                                        @else
                                                            <span>{{@$sender_nm}}</span>
                                                        @endif
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
        <div class="show_layout py-3 px-sm-0">
            <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
                <div class="card-title">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">내용</h6>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="content" name="content" rows="10" style="margin:auto;resize: none;background-color: transparent !important;" readonly >{{@$content}}</textarea>
                </div><br>
            </div>
        </div>
    </div>

    <script type="text/javascript" charset="utf-8">
        function msgRead() {
            let msg_cd = "{{$msg_cd}}";
            let store_cd = "{{$store_cd}}";

            $.ajax({
                method: 'put',
                url: '/shop/stock/stk32/msg_read',
                data: { 
                    msg_cd : msg_cd, 
                    store_cd : store_cd
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
    </script>

@stop
