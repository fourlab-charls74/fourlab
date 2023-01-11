<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title> @yield('title') | 매장관리</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="format-detection" content="telephone=no">
        <meta content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" name="viewport"  />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        @include('shop_with.layouts.head')
    </head>

    @section('body')
    @show
    <body class="sidebar-enable">
        <!-- header -->
        @include('shop_with.layouts.top')
        @include('shop_with.layouts.lnb')
        <!-- header -->
        <!-- content -->
        <div id="content">
            <div class="innter_wrap">
            @yield('content')
            </div>
        </div>
        <!-- content -->
        @include('shop_with.layouts.modal')
        @include('shop_with.layouts.footer')
    </body>
    <script src="{{ URL::asset('/with/libs/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('/skin/libs/select2/select2.min.js')}}"></script>
    <script src="{{ URL::asset('/skin/libs/datepicker/datepicker.min.js')}}"></script>
    <script src="{{ URL::asset('/js/head_search.js?20220707')}}"></script>
    <script src="{{ URL::asset('/js/store_search.js?20220707')}}"></script>
    <script src="/handle/http/axios.js"></script>
    <script src="/with/js/app.js"></script>
    <script language="javascript">
        $(document).ready(function() {
            /*
            2022-06-22 ceduce 메뉴 정의해야함!!

            $.ajax({
                type: "get",
                url: '/user/menu',
                dataType: 'json',
                // data: {},
                success: function (res) {
                    //console.log(res);
                    if(res.profile_img !== ""){
                        $("#top_profile_img").attr("src", res.profile_img);
                        $("#top_profile_img").css("border", '1px solid #e2e2e2');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }

            });
            */
        });
    </script>
</html>
