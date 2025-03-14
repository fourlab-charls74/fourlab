<header>
    <div class="top_inner_box">
        <div class="d-flex">
            <div class="logo pc white"><a href="/store" style="background:url('/theme/{{config('shop.theme')}}/images/fourlab_logo_top.png') no-repeat center center;"></a></div>
            <ul class="top_setting_btn">
                <li><a href="#" class="view" data-toggle="fullscreen"></a></li>
                <li><a href="#" class="side"></a></li>
                <li><a href="javascrip:;" class="mode"></a></li>
            </ul>
        </div>
        <div class="d-flex">
            <ul class="top_link_btn">
                {{--<!-- <li><a href="javascript:void(0);" class="pos" onclick="return window.open('/store/pos', '_blank', 'toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=0,left=0,width=1920,height=980');"></a></li> -->--}}
                <li><a href="" class="history"></a></li>
                {{--<!-- <li><a href="" class="cart"></a></li> -->--}}
	            <li><a href="javascript:void(0);" class="sizecode"><i class="bx bx-font-size"></i></a></li>
                <li><a href="javascript:void(0);" onclick="return openSmsList();" class="mail"></a></li>
				{{--<li><a href="stk31" class="notice act"></a></li>--}}
				<li><a href="https://support.gorocket3.ai/login?back_url=https%3A%2F%2Fsupport.gorocket3.ai%2Fprojects%2Ffjallraven" target="_new" class="gorocket act" title="고로켓 고객지원"></a></li>
                <li>
                    <div class="dropdown">
                        <button type="button" class="profile_btn btn header-item waves-effect notice" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if (auth('head')->user()->profile_img !== '')
                                <span class="img"><img src="{{ auth('head')->user()->profile_img }}" class="rounded-circle" alt=""></span>
                            @endif
                            <span class="txt">{{ auth('head')->user()->name }} ({{ auth('head')->user()->id }})</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/head/user"><i class="bx bx-user font-size-16 align-middle mr-1"></i> Profile</a>
                            <a class="dropdown-item" href="/store/user/log"><i class="bx bx-wallet font-size-16 align-middle mr-1"></i> My Log</a>
                            <a class="dropdown-item d-block" href="#"><span class="badge badge-success float-right">11</span><i class="bx bx-wrench font-size-16 align-middle mr-1"></i> Settings</a>
                            <a class="dropdown-item" href="#"><i class="bx bx-lock-open font-size-16 align-middle mr-1"></i> Lock screen</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/store/logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<div class="mobile_sub_list shodow">
    <a href="" class="now_page"><span></span> <i class="bx bx-chevron-down fs-18"></i></a>
    <ul class="page_list"></ul>
</div>
