@extends('head_with.layouts.layout-nav')
@section('title','회원계급')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">회원계급</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 회원&amp;CRM</span>
                <span>/ 회원계급</span>
            </div>
        </div>
        <div>
            @if ($type == 'add')
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm save-btn">저장</a>
            @elseif($type == 'edit')
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm update-btn">수정</a>
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm delete-btn">삭제</a>
            @endif
        </div>
    </div>

    <form name="detail">
        <input type="hidden" name="src" value="">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">회원 그룹 정보</a>
                </div>
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
                                                <th>그룹명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm" name='group_nm' id="group_nm" value="{{@$group->group_nm}}">
                                                        @if($type == 'edit')
                                                            <span class="txt_box">회원수: <mark>{{number_format($group->user_cnt)}}</mark>명</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>구분</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="type" class="form-control form-control-sm">
                                                            <option value="">선택</option>
                                                            @foreach($types as $val)
                                                            <option value="{{$val->code_id}}" @if(@$group->type == $val->code_id) selected @endif>{{$val->code_val}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>조건</th>
                                                <td>
                                                    <div class="form-inline no-gutters">
                                                        <div class="col-sm-6 form-inline">
                                                            <span class="txt_box" style="width:60px;">구매금액 :</span>
                                                            <div class="form-inline" style="width:calc(100% - 60px);">
                                                                <div class="form-inline-inner input_box">
                                                                    <input 
                                                                        type="text"
                                                                        name="cond_amt_from"
                                                                        id="cond_amt_from"
                                                                        class="form-control form-control-sm text-right"
                                                                        value="{{number_format(@$group->cond_amt_from)}}"
                                                                        onkeyup="currency(this)"
                                                                    > 
                                                                </div>
                                                                <span class="text_line">~</span>
                                                                <div class="form-inline-inner input_box">
                                                                    <input 
                                                                        type="text"
                                                                        name="cond_amt_to"
                                                                        id="cond_amt_to"
                                                                        class="form-control form-control-sm text-right"
                                                                        value="{{number_format(@$group->cond_amt_to)}}"
                                                                        onkeyup="currency(this)"
                                                                    >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-1 pl-2 d-none d-sm-block" style="max-width:20px;">,</div>
                                                        <div class="col-sm-5 form-inline mt-1 mt-sm-0">
                                                            <span class="txt_box" style="width:60px;">구매횟수 :</span>
                                                            <div class="form-inline" style="width:calc(100% - 60px);">
                                                                <div class="form-inline-inner input_box">
                                                                    <input `
                                                                        type="text" 
                                                                        name="cond_cnt_from" 
                                                                        id="cond_cnt_from" 
                                                                        class="form-control form-control-sm text-right" 
                                                                        value="{{number_format(@$group->cond_cnt_from)}}"
                                                                        onkeyup="currency(this)"
                                                                    > 
                                                                </div>
                                                                <span class="text_line">~</span>
                                                                <div class="form-inline-inner input_box">
                                                                    <input 
                                                                        type="text" 
                                                                        name="cond_cnt_to" 
                                                                        id="cond_cnt_to" 
                                                                        class="form-control form-control-sm text-right" 
                                                                        value="{{number_format(@$group->cond_cnt_to)}}"
                                                                        onkeyup="currency(this)"
                                                                    >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>갱신주기</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="renew_period" id="renew_period" class="form-control form-control-sm">
                                                            <option value="0" @if(@$group->renew_period == '0') selected @endif>무제한</option>
                                                            <option value="1" @if(@$group->renew_period == '1') selected @endif>1개월</option>
                                                            <option value="3" @if(@$group->renew_period == '3') selected @endif>3개월</option>
                                                            <option value="6" @if(@$group->renew_period == '6') selected @endif>6개월</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>할인율</th>
                                                <td>
                                                    <div class="form-inline">
                                                        <div class="form-inline">
                                                            <div class="form-inline-inner input_box" style="width:calc(65% - 80px);">
                                                                <input 
                                                                    type="text" 
                                                                    name="dc_limit_amt" 
                                                                    id="dc_limit_amt" 
                                                                    class="form-control form-control-sm text-right" 
                                                                    value="{{number_format(@$group->dc_limit_amt)}}"
                                                                    onkeyup="currency(this)"
                                                                > 
                                                            </div>
                                                            <span class="txt_line" style="width:80px;padding-left:2px;">원 이상 구매시,</span>
                                                            <div class="form-inline-inner input_box" style="width:calc(35% - 52px);">
                                                                <input 
                                                                    type="text" 
                                                                    name="dc_ratio" 
                                                                    id="dc_ratio" 
                                                                    class="form-control form-control-sm text-right" 
                                                                    value="{{number_format(@$group->dc_ratio)}}"
                                                                    onkeyup="currency(this)"
                                                                >
                                                            </div>
                                                            <span class="txt_line" style="width:52px;padding-left:6px;">% 할인</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2" style="line-height:20px;">
                                                        (도매그룹일 경우 원가에 할인율만큼 추가된 금액으로 판매됩니다.) 
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>할인율 제외상품</th>
                                                <td>
                                                    <div class="form-inline">
                                                        @if ($type == 'edit')
                                                            <span class="dc_ext_goods txt_box">{{$group->dc_ext_goods}}</span>개
                                                            <a href="#" class="btn btn-sm btn-secondary ext-goods-btn ml-1">상품보기</a>
                                                        @else
                                                            <div class="txt_box" style="line-height:20px;">할인율 제외상품 추가는 회원그룹 등록 후 할 수 있습니다.</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>추가 적립율</th>
                                                <td>
                                                    <div class="form-inline">
                                                        <div class="form-inline">
                                                            <div class="form-inline-inner input_box" style="width:calc(65% - 80px);">
                                                                <input 
                                                                    type="text" 
                                                                    name="point_limit_amt" 
                                                                    class="form-control form-control-sm text-right" 
                                                                    maxlength="10" 
                                                                    value="{{number_format(@$group->point_limit_amt)}}"
                                                                    onkeyup="currency(this)" 
                                                                >
                                                            </div>
                                                            <span class="txt_line" style="width:80px;padding-left:2px;">원 이상 구매시,</span>
                                                            <div class="form-inline-inner input_box" style="width:calc(35% - 52px);">
                                                                <input 
                                                                    type="text" 
                                                                    name="point_ratio" 
                                                                    class="form-control form-control-sm text-right" 
                                                                    maxlength="10" 
                                                                    value="{{number_format(@$group->point_ratio)}}"
                                                                    onkeyup="currency(this);" 
                                                                >
                                                            </div>
                                                            <span class="txt_line" style="width:52px;padding-left:6px;">% 추가</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>도매 여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_wholesale_yn" id="is_wholesale_y" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="is_wholesale_y">Y</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_wholesale_yn" id="is_wholesale_n" class="custom-control-input" value="N" @if(@$group->is_wholesale == 'N') checked @endif>
                                                            <label class="custom-control-label" for="is_wholesale_n">N</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>적립금 사용</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_point_use_yn" id="is_point_use_y" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="is_point_use_y">적립금 사용가능</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_point_use_yn" id="is_point_use_n" class="custom-control-input" value="N" @if(@$group->is_point_use == 'N') checked @endif>
                                                            <label class="custom-control-label" for="is_point_use_n">적립금 사용불가능</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>적립금 지급</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_point_save_yn" id="is_point_save_y" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="is_point_save_y">적립금 사용가능</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_point_save_yn" id="is_point_save_n" class="custom-control-input" value="N" @if(@$group->is_point_save == 'N') checked @endif>
                                                            <label class="custom-control-label" for="is_point_save_n">적립금 사용불가능</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>쿠폰 사용</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_coupon_use_yn" id="is_coupon_use_y" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="is_coupon_use_y">쿠폰 사용가능</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="is_coupon_use_yn" id="is_coupon_use_n" class="custom-control-input" value="N" @if(@$group->is_coupon_use == 'N') checked @endif>
                                                            <label class="custom-control-label" for="is_coupon_use_n">쿠폰 사용불가능</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>아이콘</th>
                                                <td>
                                                    <div class="form-inline inline_input_box">
                                                        <figure class="img-upload-box mb-0">
                                                            <div>
                                                                @if(@$group->icon != '') 
                                                                    <img src="{{$group->icon}}" alt="아이콘사진" style="max-width:120px">
                                                                @endif
                                                            </div>
                                                            <input type="file" name="file" id="file" class="d-none">
                                                            <label for="file" class="btn btn-sm btn-primary shadow-sm upload-btn mb-0">이미지 업로드</label>
                                                        </figure>
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
<style>
    .img-upload-box{
        width:120px;
    }
    .img-upload-box div{
        width:120px;
        height:120px;
        border:1px solid #ccc;
        overflow:hidden;
    }
    .img-upload-box label{
        width:100%;
    }
</style>

<script>
const group_no = '{{@$group->group_no}}';

let target_file = null;

function validatePhoto(target) {
  if (target?.length === 0) {
    alert("업로드할 이미지를 선택해주세요.");
    return false;
  }

  if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target[0].name)) {
    alert("이미지 형식이 아닙니다.");
    return false;
  }

  return true;
}

function drawImage(e) {
    var image = new Image();

    image.src = e.target.result;
    image.className = 'img-preview';
    image.width = $('.img-upload-box div').width();

    $('.img-upload-box div').append(image);
}

function validate() {
    const f1 = document.detail;

	if($('#group_nm').val() == ""){
		alert("그룹명을 입력해 주십시오.");
		f1.group_nm.focus();
		return false;	
	}

	if($('#type').val() == ""){
		alert("구분을 선택하여 주십시오.");
		f1.type.focus();
		return false;
	}
	
	if($('#cond_amt_from').val() == ""){
		alert("구매금액을 입력해 주십시오.");
		f1.cond_amt_from.focus();
		return false;	
	}
	
	if($('#cond_amt_to').val() == ""){
		alert("구매금액을 입력해 주십시오.");
		f1.cond_amt_to.focus();
		return false;	
	}
	
	if($('#cond_cnt_from').val() == ""){
		alert("구매횟수을 입력해 주십시오.");
		f1.cond_cnt_from.focus();
		return false;	
	}
	
	if($('#cond_cnt_to').val() == ""){
		alert("구매횟수을 입력해 주십시오.");
		f1.cond_cnt_to.focus();
		return false;	
	}
	
	if($('#cond_email').val() == ""){
		alert("이메일을 입력해 주십시오.");
		f1.cond_email.focus();
		return false;	
	}
		
	if($('#dc_limit_amt').val() == ""){
		alert("할인 제한 금액을 입력해 주십시오.");
		f1.dc_limit_amt.focus();
		return false;	
	}
	
	if($('#dc_ratio').val() == ""){
		alert("할인율을 입력해 주십시오.");
		f1.dc_ratio.focus();
		return false;	
	}
	
	if($('#point_limit_amt').val() == ""){
		alert("적립 제한 금액을 입력해 주십시오.");
		f1.point_limit_amt.focus();
		return false;	
	}
	
	if($('#point_ratio').val() == ""){
		alert("적립율을 입력해 주십시오.");
		f1.point_ratio.focus();
		return false;	
    }

	return true;
}

$('#file').change(function(){
    $('.img-upload-box div').html('');
    if (validatePhoto(this.files) === false) return; 
    
    var fr = new FileReader();

    fr.onload = drawImage;
    fr.readAsDataURL(this.files[0]);
});

$('.save-btn').click(function(){
    if (validate() === false) return;
    if (confirm('해당내용을 저장하시겠습니까?') === false) return;

    document.detail.src.value = $('.img-preview').attr('src');

    const data = $('form[name="detail"]').serialize();
    
    $.ajax({    
        type: "post",
        url: '/head/member/mem03',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: data,
        success: function(data) {
            alert("그룹이 등록되었습니다.");
            opener?.Search();
            window.close();
            location.href = `/head/member/mem03/show/edit/${data.id}`;
        }
    });
});

$('.update-btn').click(function(){
    if (validate() === false) return;
    if (confirm('해당내용을 수정하시겠습니까?') === false) return;
    
    if ($('.img-preview').attr('src')) {
        document.detail.src.value = $('.img-preview').attr('src');
    }

    const data = $('form[name="detail"]').serialize();
    
    $.ajax({    
        type: "put",
        url: `/head/member/mem03/${group_no}`,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: data,
        success: function(data) {
            alert("그룹이 수정되었습니다.");
            location.reload();
        }
    });
});

$('.delete-btn').click(function() {
    if (confirm('해당그룹을 삭제하시겠습니까?') === false) return;
    
    $.ajax({    
        type: "delete",
        url: `/head/member/mem03/${group_no}`,
        success: function(data) {
            alert("삭제되었습니다.");
            opener?.Search();
            window.close();
        }
    });
});

$('.ext-goods-btn').click(function(){
    const url=`/head/member/mem03/ext-goods/${group_no}`;
    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    
});
</script>
@stop
