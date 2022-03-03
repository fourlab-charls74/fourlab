@extends('partner.layouts.app')
@section('title','공지사항')
@section('content')
<div class="container-fluid">
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">공지사항</h1>
  <div>
      @if($no > 0)
          <a href="#" onclick="return Update('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sm text-white-50"></i> 저장</a>
          <a href="#" onclick="return Destroy('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sm text-white-50"></i> 삭제</a>
      @else
          <a href="#" onclick="return Create();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-sm text-white-50"></i> 저장</a>
      @endif
  </div>
</div>

<form method="post" name="store" action="/partner/promotion/prm01">
    @csrf
<div class="row">
<!-- Earnings (Monthly) Card Example -->
<div class="col mb-4">
  <div class="card border-left-primary shadow h-100 py-2">
    <div class="card-body">
    <div class="row no-gutters align-items-center mb-4">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" >제목</div>
            <div class="h6 mb-0 text-gray-800">
                <input type='text' class="form-control form-control-sm" name='question' value='{{$user->question}}' style='width:70%;'>
            </div>
        </div>
    </div>
    <div class="row no-gutters align-items-center mb-4">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" >내용</div>
            <div class="h6 mb-0 font-weight-bold text-gray-800">
                <input type='text' class="form-control form-control-sm" name='answer' value='{{$user->answer}}' style='width:70%;'>
            </div>
        </div>
    </div>
    </div>
    </div>
</div>
</div>
</form>
</div>
<script type="text/javascript" charset="utf-8">
    function Create() {
        var frm = $('form');
        //console.log(frm.serialize());

        $.ajax({
            async: true,
            method: 'post',
            url: '/partner/promotion/prm02',
            data: frm.serialize(),
            success: function (data) {
                console.log(data);
                document.location.href = '/partner/promotion/prm02'
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    function Update(no) {
        var frm = $('form');
        //console.log(frm.serialize());

        $.ajax({
            async: true,
            method: 'put',
            url: '/partner/promotion/prm02/' + no,
            data: frm.serialize(),
            success: function (data) {
                console.log(data);
                document.location.href = '/partner/promotion/prm02'
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    function Destroy(no) {
        var frm = $('form');
        //console.log(frm.serialize());

        $.ajax({
            async: true,
            method: 'delete',
            url: '/partner/promotion/prm02/' + no,
            data: frm.serialize(),
            success: function (data) {
                console.log(data);
                document.location.href = '/partner/promotion/prm02'
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }
</script>
@stop
