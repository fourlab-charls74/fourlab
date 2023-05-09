<header>
    <div class="top_inner_box">
        <div class="d-flex">
            <div class="logo pc white"><a href="/shop" style="background:url('/theme/{{config('shop.theme')}}/images/pc_logo_white.png') no-repeat center center;"></a></div>
            <ul class="top_setting_btn">
                <li><a href="#" class="view" data-toggle="fullscreen"></a></li>
                <li><a href="#" class="side"></a></li>
                <li><a href="javascrip:;" class="mode"></a></li>
            </ul>
        </div>
        @if (auth('head')->user()->store_cd !== '' && auth('head')->user()->grade === 'P')
        <div class="d-inline-flex location">
            <span class="shop-title">{{auth('head')->user()->store_nm}}</span>
            <span class="shop-title2" style="color:gray; text-shadow:none;"> &nbsp; / &nbsp;</span>
            <span class="shop-title2">{{auth('head')->user()->store_cd}}</span>
        </div>
        @endif
        <div class="d-flex">
            <ul class="top_link_btn">
                <li><a href="javascript:void(0);" class="pos" onclick="return window.open('/shop/pos', '_blank', 'toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=0,left=0,width=1920,height=980');"></a></li>
                <li><a href="" class="history"></a></li>
                {{--<!-- <li><a href="" class="cart"></a></li>
                <li><a href="javascript:void(0);" onclick="return openSmsList();" class="mail act"></a></li> -->--}}
                <li>
                    <div class="dropdown">
                        <a href="javascript:;" class="notice act" id="em_cnt" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" id="em_subject">
                        </div>
                    </div>
                </li>
                <li>
                    <div class="dropdown">
                        <button type="button" class="profile_btn btn header-item waves-effect notice" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if (auth('head')->user()->profile_img !== '')
                                <span class="img"><img src="{{ auth('head')->user()->profile_img }}" class="rounded-circle" alt=""></span>
                            @endif
                            <span class="txt">{{ auth('head')->user()->name }} ({{ auth('head')->user()->id }})</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/shop/user"><i class="bx bx-user font-size-16 align-middle mr-1"></i> Profile</a>
                            <a class="dropdown-item" href="/shop/user/log"><i class="bx bx-wallet font-size-16 align-middle mr-1"></i> My Log</a>
                            @if (auth('head')->user()->store_cd !== '' && auth('head')->user()->grade === 'P')
                            <a class="dropdown-item" href="/shop/standard/std02/show/{{ auth('head')->user()->store_cd }}"><i class="bx bx-store font-size-16 align-middle mr-1"></i> My Store</a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/shop/logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    @if (auth('head')->user()->store_cd !== '' && auth('head')->user()->grade === 'P')
    <div class="shop-title-mobile pb-2">
        <span class="shop-title">{{auth('head')->user()->store_nm}}</span>
        <span class="shop-title2" style="color:gray; text-shadow:none;"> &nbsp; / &nbsp;</span>
        <span class="shop-title2">{{auth('head')->user()->store_cd}}</span>
    </div>
    @endif
</header>
<div class="mobile_sub_list shodow">
    <a href="" class="now_page"><span></span> <i class="bx bx-chevron-down fs-18"></i></a>
    <ul class="page_list"></ul>
</div>
<script>
    $(document).ready(function(){
        noticeAct();
    });
    
    // 미확인 공지사항 상단 아이콘 표시
    function noticeAct() {
        localStorage.removeItem("n_readMsg_cnt");
        localStorage.removeItem("n_readMsg");

        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/community/comm01/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
                    if(data.cnt > 0) {
                        localStorage.setItem("n_readMsg_cnt", data.cnt);
                        let cnt = window.localStorage.getItem('n_readMsg_cnt');
                        document.getElementById("em_cnt").innerHTML = '<em>'+cnt+'</em>';
                        
                        let msgObj = {};
                        $.each(data.nos, function(i, item){
                            item.content = item.content.replace(/<[^>]*>?/g, ''); 
                            msgObj[item.ns_cd] = "<span style='font-weight:bold;'>" + item.subject + "</span><font style='size:9px;'>" + item.content + "</font>";
                        });
                        localStorage.setItem("n_readMsg", JSON.stringify(msgObj));
                        let nReadMsg = localStorage.getItem("n_readMsg");
                        nReadMsg = JSON.parse(nReadMsg);

                        $.each(nReadMsg, function(i, item){
                            $('#em_subject').prepend(`<a class="dropdown-item" href="/shop/community/comm01/notice/${i}">${item}</a>`);
                        });

                        if (data.code === 200) {
                            $.each(data.nos, function(i, item){
                                const url = '/shop/community/comm01/popup_notice/' + item.ns_cd;
                                const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=600,height=500");
                            });
                        } else {
                            alert("공지사항 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
                        }
                        
                    } else if(data.cnt == 0) {
                        $('#em_subject').prepend(`<a class="dropdown-item" href="#">공지사항을 모두 읽었습니다.</a>`);
                    }
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});
        }
    }

</script>
