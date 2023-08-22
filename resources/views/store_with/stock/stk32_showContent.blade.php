@extends('store_with.layouts.layout-nav')
@section('title','내용')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">알림</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 게시판/알리미</span>
                    <span>/ 매장알림</span>
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
                        <a>{{ @$msg_type == 'send' ? '받는 사람' : '보내는 사람' }}</a>
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
                                                <th>{{ @$msg_type == 'send' ? '받는 사람' : '보내는 사람' }}</th>
                                                <td>
                                                    <div class="d-inline-block" name="sd" id="sd">
                                                        @if($msg_type == 'send')
                                                            @if ($receiver_cnt-1 == 0)
                                                                <span style="vertical-align:middle">{{@$first_receiver}}</span>
                                                            @else
                                                                <span style="vertical-align:middle">{{@$first_receiver}} 외 {{@$receiver_cnt-1}}개</span>
                                                            @endif
                                                        @else
                                                            <span>{{@$sender_nm}}</span>
                                                        @endif
                                                    </div>
                                                    @if($msg_type == 'send')
                                                        <a href="#" style="float:right" onclick="showStore();" class="btn btn-lg btn-outline-primary mr-1">더보기</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($msg_type == 'send')
                                                <tr id="showStore">
                                                    <th></th>
                                                    <td>
                                                        @foreach ($store as $s)
                                                            @if ($s->check_yn == 'Y') 
                                                                <span style="color:green;">{{ $s->stores }}( {{ $s->check_yn }} )     </span>
                                                            @else
                                                                <span style="color:red;">{{ $s->stores }}( {{ $s->check_yn }} )     </span>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @elseif ($msg_type == 'pop')
                                                <tr>
                                                    <th>발신일</th>
                                                    <td>
                                                        <div class="flax_box" name="date" id="date">
                                                            @if($reservation_yn == 'Y')
                                                                <span>{{@$reservation_date}}</span>
                                                            @else
                                                                <span>{{@$rt}}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
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
                    <textarea class="form-control" id="content" name="content" rows="10" style="margin:auto;resize: none;background-color: transparent !important;" readonly>{{@$content}}</textarea>
                </div><br>
                @if ($msg_type == 'pop')
                <div class="resul_btn_wrap mt-1 d-block">
                    <a href="javascript:locate('{{ @$msg_kind }}');" class="btn btn-sm btn-primary">{{ (@$msg_kind == 'AS' ? '수선관리' : (@$msg_kind == 'RT' ? '매장RT' : '매장알림')) }} 바로 가기</a>
					<a href="#" onclick="OpenReply();" class="btn btn-sm btn-primary shadow-sm pl-2"> 답장</a>
					<a href="#" onclick=window.close(); class="btn btn-sm btn-primary shadow-sm pl-2"> 닫기</a>
				</div>
				@else
					<div class="resul_btn_wrap mt-3 d-block">
						@if($msg_type == 'receive')
							<a href="#" onclick="OpenReply();" class="btn btn-sm btn-primary shadow-sm pl-2"> 답장</a>
						@endif
						<a href="#" onclick=window.close(); class="btn btn-sm btn-primary shadow-sm pl-2"> 닫기</a>
					</div>
                @endif
            </div>
        </div>
		
    </div>

<script>

    /*$(document).ready(function(){
        $('#showStore').hide();

		const textarea = document.getElementById('content');
		const sender_cd = '{{ @$admin_id  }}'
		const sender_nm = '{{ @$admin_nm  }}'
		const separator = '\n-------------------------------------------------------------------';
		const content = textarea.value;
		if (content.length > 0) {
			textarea.value = sender_nm + '(' + sender_cd + ')' + '\n' + separator + '\n' + content;
		} else {
			textarea.value = separator;
		}
		
    }); */

    let cnt = 0;
    function showStore() {
        cnt++;
        $('#showStore').show();

        if (cnt%2 == 0) {
            $('#showStore').hide();
        }
    }

    function msgRead() {
        let msg_cd = "{{$msg_cd}}";

        $.ajax({
            method: 'put',
            url: '/store/stock/stk32/msg_read',
            data: { 
                msg_cd : msg_cd
            },
            success: function(data) {
                if (data.code == '200') {
                    alert('읽음 처리 되었습니다.');
                    window.close();
					opener.Search();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                    // console.log(e.responseText)
            }
        });
    }

    function locate(kind) {
        let url = '';
        let msg = '';
        let msg_cd = "{{$msg_cd}}";

        if(kind == 'S' || kind == '') {
            url = "/store/stock/stk32";
            msg = '매장알림 메뉴로 이동시 자동으로 읽음 처리 됩니다.\r\n이동하시겠습니까?';
        } else if(kind == 'RT') {
            url = "/store/stock/stk20";
            msg = '매장RT 메뉴로 이동시 자동으로 읽음 처리 됩니다.\r\n이동하시겠습니까?';
        } else if(kind == 'AS') {
            url = "/store/standard/std11";
            msg = '수선관리 메뉴로 이동시 자동으로 읽음 처리 됩니다.\r\n이동하시겠습니까?';
        }

        if(!confirm(msg)){
            return false;
        }

        axios({
            method: 'put',
            url: '/store/stock/stk32/msg_read',
            data: { 
                msg_cd : msg_cd
            }
        }).then(function (res) {
            if(res.data.code == '200') {
                alert('읽음 처리 되었습니다.');
                window.opener.location.href = url;
                window.close();
            } else {
                alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

	function OpenReply() {
		let msg_cd = "{{ $msg_cd }}"
		const url = `/store/stock/stk32/reply-msg?msg_cd={{$msg_cd}}`;
		const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
		self.close();
		
	}
	
</script>
@stop
