@extends('shop_with.layouts.layout')
@section('title','매장 공지사항')
@section('content')

<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">매장 공지사항</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매장관리</span>
            <span>/ 매장 공지사항</span>
        </div>
    </div>
    <form>
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        <button type="button" onclick="document.location.href='/shop/stock/stk31';" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">목록</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>작성자</th>
                                        <td>
                                            <div class="txt_box">{{$user->name}}
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td>
                                            <div class="txt_box">{{$user->subject}}
                                                @error(' subject') <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td>
                                            <div>
                                                <input type="hidden" id="div_content1" name="content" value='{{$user->content}}' />
                                                <div id="div_content2" class="txt_box" style="min-height: 200px"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">

    $(document).ready(function() {
        document.getElementById('div_content2').innerHTML = $('#div_content1').val();
    });
        
</script>
@stop
