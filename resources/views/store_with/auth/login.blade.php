@extends('store_with.layouts.layout-nav')

@section('title')
Login
@endsection

@section('body')

<body class="{{@$className}}">
    @endsection
    @section('content')
    <div class="login_inner">
        <div class="cont">
            <div class="right_cont" style="background-image:url('/theme/{{config('shop.theme')}}/images/login_bg.jpg');background-size:auto;">
                <dl>
                    <dt>
{{--                        <span>Log in to</span>--}}
{{--                        <strong>Handle</strong>--}}
                    </dt>
                    <dd>
                        등록한 매장 아이디로 로그인해 주세요.<br/>
                        처음 방문하신 분은 매장 판매자 가입 후, 이용해 주세요.
                    </dd>
                </dl>
            </div>
            <div class="left_cont">
                <form method="POST" action="/store/login" class="login_input_box">
                    @csrf
                    <div class="login_mobile_tit">
                        <strong>Handle</strong>
                    </div>
                    <ul class="list_input">
                        <li>
                            <div class="input_box">
                                <i class="icon login"></i>
                                <input id="id" class="form-control @error('email') is-invalid @enderror" name="email" value="" autocomplete="id" autofocus placeholder="ID">
                            </div>
                        </li>
                        <li>
                            <div class="input_box">
                                <i class="icon pw"></i>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                            </div>
                        </li>
                    </ul>
                    <div class="btn_wrap">
                        <button type="submit" class="btn btn-primary d-block wd100 font-weight-bold brnone">
                            {{ __('LOGIN') }}
                        </button>
                    </div>
                    <ul class="list_link">
                        <li>
                            <a href="">회원가입</a>
                        </li>
                        <li>
                            <a href="">아이디찾기</a>
                        </li>
                        <li>
                            <a href="">비밀번호찾기</a>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
    @endsection
