<header>
    <div class="top_inner_box">
        <div class="d-flex">
            <div class="logo pc white"><a href="/partner" style="background:url('/theme/{{config('shop.theme')}}/images/pc_logo_white.png') no-repeat center center;"></a></div>
            <ul class="top_setting_btn">
                <li><a href="#" class="view" data-toggle="fullscreen"></a></li>
                <li><a href="#" class="side"></a></li>
                <li><a href="javascrip:;" class="mode"></a></li>
            </ul>
        </div>
        <div class="d-flex">
            <ul class="top_link_btn">
                <!--li><a href="" class="history"></a></li>
                <li><a href="" class="cart"></a></li>
                <li><a href="#" class="mail act"></a></li-->
                <!--
                <li>
                    <div class="dropdown d-inline-block">
                        <a href="javascript:;" class="notice act" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <em>12</em>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" >
                            11
                        </div>
                    </div>
                </li>
                //-->
                <li>
                    <div class="dropdown d-inline-block">
                        <button type="button" class="profile_btn btn header-item waves-effect notice" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="img"><img id="top_profile_img" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" class="rounded-circle" alt=""></span>
                            <span class="txt">{{ auth('partner')->user()->com_nm }} ({{ auth('partner')->user()->com_id }})</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/partner/user"><i class="bx bx-user font-size-16 align-middle mr-1"></i> Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/partner/logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="mobile_header">
        <ul>
            <li><a href="/partner">대시보드</a></li>
            <li><a href="/partner/product/prd01">상품/재고</a></li>
            <li><a href="/partner/order/ord01">주문/배송</a></li>
            <li><a href="/partner/cs/cs01">클레임/CS</a></li>
            <li><a href="/partner/sales/sal02">매출정산</a></li>
            <li><a href="/partner/support/spt01">게시판</a></li>
        </ul>
    </div>
</header>
<div class="mobile_sub_list shodow">
    <a href="" class="now_page"><span></span> <i class="bx bx-chevron-down fs-18"></i></a>
    <ul class="page_list"></ul>
</div>
