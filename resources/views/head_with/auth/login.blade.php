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
            <div class="right_cont" style="background-image:url('/theme/{{config('shop.theme')}}/images/login_fourlab2_bg.jpg');background-size:contain;">
                <dl>
                    <dt>
                        <img src="/theme/{{config('shop.theme')}}/images/fourlab_logo_w.png" style="width:50%;position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);"
                             onError="this.src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='">
                    </dt>
                </dl>
            </div>
            <div class="left_cont">
                <form method="POST" action="{{ route('head.login') }}" class="login_input_box">
                    @csrf
                    <div class="login_mobile_tit">
                        <img src="/theme/{{config('shop.theme')}}/images/fourlab_logo_w.png" style="width:80%">
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
                    <div class="btn_wra pt-4
                        @if( $errors->has('code') && in_array(@$errors->first('code'), [2]) )
                        text-danger font-weight-bold">
                        @else
                        text-warning">
                        @endif
                            @foreach( $errors->get('email') as $err)
                                {{ $err }}
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
                            <a href="#" onClick="openPopup();">회원가입</a>
                        </li>
                        <li>
                            <a href="#" onClick="openMessage();">아이디찾기</a>
                        </li>
                        <li>
                            <a href="#" onClick="openMessage();">비밀번호찾기</a>
                        </li>
                    </ul>

                    <!-- QA 기간동안의 임시 URL 정보 노출 시작 //-->
					{{--
					@if($_SERVER['SERVER_NAME'] != 'devel.fjallraven.co.kr')
					<div class="pt50" style="font-size:15px;color:#FF0000;font-weight:bold;">
						@if($_SERVER['SERVER_NAME'] == 'handle.fjallraven.co.kr')
							※ 현재페이지는 운 입니다.<br>
						@endif
						※ 테스트 페이지는 아래 링크를 이용해 주십시요.
					</div>
					<div class="txtc pt10" style="font-size:15px;">
						테스트 페이지 URL : <a href="https://devel.fjallraven.co.kr/" style="font-weight:bold;text-decoration:underline !important;">https://devel.fjallraven.co.kr/</a>
					</div>
					@endif
					--}}
                    <!-- QA 기간동안의 임시 URL 정보 노출 종료 //-->

                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" charset="utf-8">
    function openPopup() {
        let url = '/head/sign-up';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=800,height=660");
    }

    function openMessage() {
        alert('시스템 관리자에게 문의하시길 바랍니다.');
    }
    </script>
    @endsection
