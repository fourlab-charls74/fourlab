@extends('head_with.layouts.layout-nav')
@section('title','쿠폰자동지급')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">쿠폰</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 쿠폰자동지급</span>
            </div>
        </div>
        <div>
            <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm save-btn">저장</a>
        </div>
    </div>
    
    <form name="detail">
        <div class="card_wrap mb-0">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>신규회원가입</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="new_member_coupon" id="new_member_coupon" class="form-control form-control-sm">
                                                            @foreach($coupons as $val)
                                                                <option value="{{$val->coupon_no}}" @if($val->coupon_no == $new) selected @endif>{{$val->coupon_nm}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>앱 신규회원가입</th>
                                                <td>
                                                    <div class="txt_box">
                                                        추후 작업 * 신규회원가입 + 앱신규회원가입 
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>생일쿠폰</th>
                                                <td>
                                                    <div class="txt_box">
                                                        추후 자동화
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $('.save-btn').click(function(){
        const no = $('#new_member_coupon').val();
        $.ajax({    
            type: "put",
            url: `/head/promotion/prm10/gift/auto/${no}`,
            success: function(data) {
                alert("수정되었습니다.");
                window.close();
            },
            error : function(res, a, b) {
                alert(res.responseJSON.message);
            }
        });
    });
</script>
@stop