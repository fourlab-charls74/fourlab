<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title> @yield('title') | Handle</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="format-detection" content="telephone=no">
        <meta content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" name="viewport"  />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        @include('store_with.layouts.head')
    </head>

    @section('body')
    @show
    @yield('content')
    @include('store_with.layouts.modal')
    </body>
    <script src="{{ URL::asset('/with/libs/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('/skin/libs/select2/select2.min.js')}}"></script>
    <script src="{{ URL::asset('/skin/libs/datepicker/datepicker.min.js')}}"></script>
    <script src="{{ URL::asset('/js/head_search.js?20220707')}}"></script>
    <script src="{{ URL::asset('/js/store_search.js?20220707')}}"></script>
    <script src="/with/js/app.js"></script>
    <script src="/handle/http/axios.js"></script>
</html>
