@extends('head_with.layouts.layout-nav')

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
                        <img src="/theme/{{config('shop.theme')}}/images/login_logo_w.png" style="width:50%;position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);"
                             onError="this.src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='">
                    </dt>
                </dl>
            </div>
            <div class="left_cont">
                <form method="POST" action="{{ route('head.login') }}" class="login_input_box">
                    @csrf
                    <div class="login_mobile_tit">
                        <img src="/theme/{{config('shop.theme')}}/images/pc_logo_white.png">
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
                    @if( $errors->has('email') )
                        <div class="btn_wra text-warning pt-4">
                            @foreach( $errors->get('email') as $err )
                                {{ $err  }}
                            @endforeach
                    </div>
                    @endif
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
