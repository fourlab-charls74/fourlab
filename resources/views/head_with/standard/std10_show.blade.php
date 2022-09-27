@extends('head_with.layouts.layout-nav')
@section('title','광고 상세')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<div class="show_layout pb-3 px-sm-3 pt-sm-3">
    <!-- 상품 세부 정보 -->
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">상품 세부 정보</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<tbody>
											<tr>
												<th>코드</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='code' value='{{$code}}' @if(!empty($code)) readonly @endif>
                                                    </div>
                                                    <div class="txt_box">
                                                        * 코드는 영문과 숫자로 10자 이내 
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>구분</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <select name='type' id="type" class="form-control form-control-sm">
                                                            <option value="">선택</option>
                                                            @foreach($types as $val) 
                                                                <option 
                                                                    value="{{$val->code_id}}"
                                                                    @if($val->code_id === $type) selected @endif
                                                                >
                                                                    {{$val->code_val}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>광고명</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='name' value='{{$name}}'>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>상태</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <select name='state' class="form-control form-control-sm">
                                                            @foreach($states as $val) 
                                                                <option 
                                                                    value="{{$val->code_id}}"
                                                                    @if($val->code_id == $state) selected @endif
                                                                >
                                                                    {{$val->code_val}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
												</td>
											</tr>
											<tr>
												<th>광고할인</th>
												<td colspan="5">
                                                    <div class="flax_box">
                                                        <select name='ad_sale' class="form-control form-control-sm">
                                                            <option value="">선택</option>
                                                            @foreach($ad_sale as $as) 
                                                                <option 
                                                                    value="{{$as->no}}"
                                                                    @if($as->no === $dc_no) selected @endif
                                                                >
                                                                    {{$as->name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
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
    <div class="resul_btn_wrap mt-3 d-block">
        <a href="#" class="btn btn-sm btn-primary submit-btn">저장</a>
        @if ($code !== '')
        <a href="#" class="btn btn-sm btn-secondary delete-btn">삭제</a>
        @endif
        <a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
    </div>
</div>

<script>
    const CODE = '{{$code}}';

    function validate() {
        if ($('#type').val() === '') {
            alert('구분을 선택해주세요.');
            return false;
        }

        if (CODE !== '') return true;

        if ($('#code').val() === '') {
            alert('코드를 입력해주세요.');
            return false;
        }

        return true;
    }
    
    $('.submit-btn').click(function(e){
        e.preventDefault();

        if (!validate()) return;

        const data = $('form[name=detail]').serialize();

        $.ajax({
            async: true,
            type: 'put',
            url: `/head/standard/std10/show/${CODE}`,
            data: data,
            success: function (res) {
                alert("정상적으로 저장 되었습니다.");
                self.close();
                opener.Search();
            },
            error: function(request, status, error) {
                alert(request.responseJSON.msg);
                console.log("error");
            }
        });
    });
    
    $('.delete-btn').click(function(e){
        e.preventDefault();

        $.ajax({
            async: true,
            type: 'delete',
            url: `/head/standard/std10/show/${CODE}`,
            success: function (res) {
                alert('삭제되었습니다.');
                opener.Search();
                window.close();
            },
            // PLAN_KKO_BET
            error: function(request, status, error) {
                console.log(request, status, error);
                console.log("error")
            }
        });
    });
    
</script>
@stop
